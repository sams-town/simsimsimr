using System.Data.Common;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.DbContext;
using RSSMBackend.DTOs.KhanzaSync;
using RSSMBackend.Models;
using TDPBackend.Helpers;

namespace RSSMBackend.Services.KhanzaSync;

/// <summary>
/// Sinkronisasi satuan data hasil tarikan scheduler dengan data terbaru di Khanza.
///
/// Khanza tidak punya kolom updated_at, jadi perubahan setelah data ditarik tidak pernah terdeteksi
/// scheduler. Modul ini membandingkan per record lalu menerapkan field yang dipilih user.
///
/// Aturan yang tidak boleh dilanggar:
///  * Khanza hanya dibaca (sesi READ ONLY).
///  * Record yang sudah diproses lebih lanjut (dibayar, diposting, bervoucher, sudah Tukar Faktur) tidak
///    bisa diubah - dicek ulang di dalam transaksi saat penerapan, bukan hanya di daftar.
///  * Kolom workflow (status, payment_status, updated_by, ...) tidak pernah disinkron: di skrip scheduler
///    nilainya di-hardcode, bukan data Khanza. Menyinkronkannya pernah mereset 7 invoice lunas menjadi
///    'not sent' lewat Migration Job.
/// </summary>
public partial class KhanzaSyncService : IKhanzaSyncService
{
    // Administrator(1) dan Manager(2). Allow-list supaya role baru default-nya tidak punya akses.
    private static readonly int[] AllowedRoles = { 1, 2 };
    private const int MaxKeysPerRequest = 50;
    private const int MedicalFeeDefaultWindowDays = 31;

    private readonly RSSMDbContext _context;
    private readonly IKhanzaConnectionFactory _khanza;
    private readonly AuditTrailUtils _audit;
    private readonly ILogger<KhanzaSyncService> _logger;

    public KhanzaSyncService(
        RSSMDbContext context,
        IKhanzaConnectionFactory khanza,
        AuditTrailUtils audit,
        ILogger<KhanzaSyncService> logger)
    {
        _context = context;
        _khanza = khanza;
        _audit = audit;
        _logger = logger;
    }

    private sealed record ModuleDef(
        string Code, string Label, string KeyLabel, string LockRule, string AuditModule, IReadOnlyList<SyncFieldDef> Fields);

    private static readonly ModuleDef ApDef = new(
        "ap", "AP / Tukar Faktur", "No. Faktur",
        "Terkunci bila sudah Tukar Faktur, sudah dibayar, atau lunas manual. Untuk invoice yang sudah Tukar Faktur, batalkan Tukar Faktur dulu.",
        AuditModule.InvoiceExchange,
        new[]
        {
            new SyncFieldDef("receipt_date", "Tgl Terima", SyncFieldType.Date, "{0}::date"),
            new SyncFieldDef("po_date", "Tgl PO", SyncFieldType.Date, "{0}::date"),
            new SyncFieldDef("due_date", "Jatuh Tempo", SyncFieldType.Date, "{0}::date"),
            new SyncFieldDef("supplier_code", "Kode Supplier", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("supplier_name", "Nama Supplier", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("total_transaction", "Total Transaksi", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("journal_date", "Tgl Jurnal Khanza", SyncFieldType.DateTime, "{0}::timestamp"),
        });

    private static readonly ModuleDef ArDef = new(
        "ar", "Account Receivable", "No. Rawat",
        "Terkunci bila status bukan Pending atau sudah masuk voucher.",
        AuditModule.AccountReceivable,
        new[]
        {
            new SyncFieldDef("payer_type", "Jenis Penjamin", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("payer_name", "Nama Penjamin", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("service_type", "Jenis Layanan", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("transaction_date", "Tgl Piutang", SyncFieldType.Date, "{0}::date"),
            new SyncFieldDef("total_amount", "Total Piutang", SyncFieldType.Number, "{0}::numeric"),
            new SyncFieldDef("medical_record_no", "No. RM", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("patient_name", "Nama Pasien", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("clinic_code", "Kode Poli", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("clinic_name", "Nama Poli", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("no_sep", "No. SEP", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("pipeline_date", "Tgl Registrasi", SyncFieldType.DateTime, "{0}::timestamp"),
            new SyncFieldDef("billing_status", "Status Penagihan", SyncFieldType.Text, "{0}::varchar"),
        });

    private static readonly ModuleDef PoDef = new(
        "po", "Purchase Order", "No. Pemesanan",
        "Terkunci bila lunas manual, atau ada invoice turunannya yang sudah Tukar Faktur/dibayar.",
        AuditModule.PurchaseOrder,
        new[]
        {
            new SyncFieldDef("supplier_code", "Kode Supplier", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("supplier_name", "Nama Supplier", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("nip", "NIP Pemesan", SyncFieldType.Text, "{0}::varchar"),
            // Kolom timestamptz yang diisi skrip sebagai tengah malam UTC; dibandingkan per tanggal.
            new SyncFieldDef("date", "Tanggal", SyncFieldType.Date, "(({0})::date)::timestamp AT TIME ZONE 'UTC'"),
            new SyncFieldDef("total", "Total", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("discount", "Potongan", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("grand_total", "Grand Total", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("ppn", "PPN", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("stamp_duty", "Meterai", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("invoice_amount", "Tagihan", SyncFieldType.Number, "{0}::double precision"),
            new SyncFieldDef("status", "Status Pemesanan", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("source", "Sumber", SyncFieldType.Text, "{0}::varchar"),
        });

    private static readonly ModuleDef MfDef = new(
        "mf", "Jasa Medis", "Tindakan",
        "Terkunci bila sudah diposting, bervoucher, atau dibayar. Baris kembar (duplikat) diperlakukan sebagai satu data.",
        AuditModule.MedicalFee,
        new[]
        {
            new SyncFieldDef("doctor_name", "Nama Dokter", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("total_fee", "Total Fee", SyncFieldType.Number, "{0}::numeric"),
            new SyncFieldDef("patient_name", "Nama Pasien", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("medical_record_no", "No. RM", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("payer_code", "Kode Penjamin", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("payer_name", "Nama Penjamin", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("no_sep", "No. SEP", SyncFieldType.Text, "{0}::varchar"),
            new SyncFieldDef("period_month", "Bulan Periode", SyncFieldType.Integer, "{0}::integer"),
            new SyncFieldDef("period_year", "Tahun Periode", SyncFieldType.Integer, "{0}::integer"),
        });

    private static readonly ModuleDef[] AllDefs = { ApDef, ArDef, PoDef, MfDef };

    // Ekspresi "boleh diubah" per modul. Dipakai bersama oleh daftar dan penerapan supaya aturannya tunggal.
    private const string ApEditableSql =
        "(COALESCE(t.po_number,'') <> '' AND COALESCE(t.status,'') <> 'Sent' " +
        "AND COALESCE(t.payment_status,'Unpaid') = 'Unpaid' AND COALESCE(t.paid_amount,0) = 0 " +
        "AND NOT COALESCE(t.manual_paid,false))";

    private const string ApLockReasonSql =
        "CASE WHEN COALESCE(t.payment_status,'Unpaid') <> 'Unpaid' OR COALESCE(t.paid_amount,0) > 0 THEN 'Sudah dibayar' " +
        "WHEN COALESCE(t.manual_paid,false) THEN 'Lunas manual' " +
        "WHEN COALESCE(t.status,'') = 'Sent' THEN 'Sudah Tukar Faktur' ELSE NULL END";

    private const string ArEditableSql =
        "(COALESCE(t.payment_status,'') = 'Pending' AND COALESCE(t.voucher_number,'') = '')";

    private const string ArLockReasonSql =
        "CASE WHEN COALESCE(t.voucher_number,'') <> '' THEN 'Sudah masuk voucher ' || t.voucher_number " +
        "WHEN COALESCE(t.payment_status,'') <> 'Pending' THEN 'Status ' || COALESCE(t.payment_status,'-') ELSE NULL END";

    private const string PoLinkedInvoiceLockedSql =
        "EXISTS (SELECT 1 FROM public.trx_invoice_exchange ie WHERE ie.po_number = t.order_number " +
        "AND (COALESCE(ie.status,'') = 'Sent' OR COALESCE(ie.payment_status,'Unpaid') <> 'Unpaid' " +
        "OR COALESCE(ie.paid_amount,0) > 0 OR COALESCE(ie.manual_paid,false)))";

    private static readonly string PoEditableSql =
        $"(NOT COALESCE(t.manual_paid,false) AND NOT {PoLinkedInvoiceLockedSql})";

    private static readonly string PoLockReasonSql =
        $"CASE WHEN COALESCE(t.manual_paid,false) THEN 'Lunas manual' " +
        $"WHEN {PoLinkedInvoiceLockedSql} THEN 'Invoice turunan sudah Tukar Faktur/dibayar' ELSE NULL END";

    private const string MfRowEditableSql =
        "(COALESCE(t.status,'') = 'Pending' AND COALESCE(t.batch_number,'') = '' AND COALESCE(t.payment_voucher,'') = '')";

    public List<SyncModuleDto> GetModules() => AllDefs.Select(d => new SyncModuleDto
    {
        Code = d.Code,
        Label = d.Label,
        KeyLabel = d.KeyLabel,
        LockRule = d.LockRule,
        Fields = d.Fields.Select(f => new SyncFieldMetaDto
        {
            Column = f.Column,
            Label = f.Label,
            Type = f.Type.ToString()
        }).ToList()
    }).ToList();

    private static ModuleDef Def(string module) => (module ?? "").Trim().ToLowerInvariant() switch
    {
        "ap" => ApDef,
        "ar" => ArDef,
        "po" => PoDef,
        "mf" => MfDef,
        _ => throw new ApplicationException($"Modul '{module}' tidak dikenal.")
    };

    private static void EnsureRole(int roleId)
    {
        if (!AllowedRoles.Contains(roleId))
            throw new ApplicationException("Hanya Administrator atau Manager yang boleh mengakses sinkronisasi data Khanza.");
    }

    // ------------------------------------------------------------------------------------------------
    // Helper ADO.NET di atas koneksi EF. Nama kolom yang disisipkan ke SQL selalu dari definisi statis.
    // ------------------------------------------------------------------------------------------------

    private async Task<DbConnection> PgConnectionAsync()
    {
        var conn = _context.Database.GetDbConnection();
        if (conn.State != System.Data.ConnectionState.Open)
            await conn.OpenAsync();
        return conn;
    }

    private static DbCommand NewCommand(DbConnection conn, string sql, DbTransaction? tx = null)
    {
        var cmd = conn.CreateCommand();
        cmd.CommandText = sql;
        cmd.Transaction = tx;
        cmd.CommandTimeout = 120;
        return cmd;
    }

    private static void AddParam(DbCommand cmd, string name, object? value)
    {
        var p = cmd.CreateParameter();
        p.ParameterName = name;
        p.Value = value ?? DBNull.Value;
        cmd.Parameters.Add(p);
    }

    private static async Task<List<Dictionary<string, object?>>> QueryAsync(DbCommand cmd)
    {
        await using var reader = await cmd.ExecuteReaderAsync();
        return await SyncValue.ReadAllAsync(reader);
    }

    private static double? ToDouble(object? v) => v == null ? null : Convert.ToDouble(v);

    private static DateTime? ToDate(object? v) => v is DateTime dt ? dt : null;

    private static string? Str(object? v) => v?.ToString();

    // ------------------------------------------------------------------------------------------------
    // Daftar record
    // ------------------------------------------------------------------------------------------------

    public async Task<PagedResult<SyncListItemDto>> GetRecords(
        string module, string? search, string? lockState, int page, int pageSize, int roleId)
    {
        EnsureRole(roleId);
        var def = Def(module);
        page = Math.Max(1, page);
        pageSize = Math.Clamp(pageSize, 1, 100);
        var state = (lockState ?? "editable").Trim().ToLowerInvariant();
        var like = string.IsNullOrWhiteSpace(search) ? null : "%" + search.Trim() + "%";

        return def.Code switch
        {
            "ap" => await ListSimpleAsync(
                table: "public.trx_invoice_exchange",
                select: "t.po_number, t.invoice_number, t.supplier_name, t.total_transaction, t.receipt_date",
                baseWhere: "COALESCE(t.po_number,'') <> ''",
                searchWhere: "(t.invoice_number ILIKE @q OR t.po_number ILIKE @q OR t.supplier_name ILIKE @q)",
                editableSql: ApEditableSql, lockReasonSql: ApLockReasonSql,
                orderBy: "t.receipt_date DESC NULLS LAST, t.invoice_exchange_id DESC",
                state, like, page, pageSize,
                r => new SyncListItemDto
                {
                    Key = SyncValue.KeyOf(Str(r.Get("po_number")), Str(r.Get("invoice_number"))),
                    Title = Str(r.Get("invoice_number")) ?? "-",
                    Subtitle = Str(r.Get("supplier_name")),
                    Info = "PO " + Str(r.Get("po_number")),
                    Amount = ToDouble(r.Get("total_transaction")),
                    Date = ToDate(r.Get("receipt_date"))
                }),

            "ar" => await ListSimpleAsync(
                table: "public.trx_account_receivable",
                select: "t.ar_number, t.payer_id, t.payer_name, t.patient_name, t.total_amount, t.transaction_date",
                baseWhere: "COALESCE(t.ar_number,'') <> ''",
                searchWhere: "(t.ar_number ILIKE @q OR t.patient_name ILIKE @q OR t.medical_record_no ILIKE @q OR t.payer_name ILIKE @q)",
                editableSql: ArEditableSql, lockReasonSql: ArLockReasonSql,
                orderBy: "t.transaction_date DESC NULLS LAST, t.ar_id DESC",
                state, like, page, pageSize,
                r => new SyncListItemDto
                {
                    Key = SyncValue.KeyOf(Str(r.Get("ar_number")), Str(r.Get("payer_id"))),
                    Title = Str(r.Get("ar_number")) ?? "-",
                    Subtitle = Str(r.Get("patient_name")),
                    Info = Str(r.Get("payer_name")),
                    Amount = ToDouble(r.Get("total_amount")),
                    Date = ToDate(r.Get("transaction_date"))
                }),

            "po" => await ListSimpleAsync(
                table: "public.thb_purchase_order",
                select: "t.order_number, t.supplier_name, t.status, t.grand_total, t.date",
                baseWhere: "COALESCE(t.order_number,'') <> ''",
                searchWhere: "(t.order_number ILIKE @q OR t.supplier_name ILIKE @q)",
                editableSql: PoEditableSql, lockReasonSql: PoLockReasonSql,
                orderBy: "t.date DESC NULLS LAST, t.order_number DESC",
                state, like, page, pageSize,
                r => new SyncListItemDto
                {
                    Key = SyncValue.KeyOf(Str(r.Get("order_number"))),
                    Title = Str(r.Get("order_number")) ?? "-",
                    Subtitle = Str(r.Get("supplier_name")),
                    Info = Str(r.Get("status")),
                    Amount = ToDouble(r.Get("grand_total")),
                    Date = ToDate(r.Get("date"))
                }),

            "mf" => await ListMedicalFeeAsync(state, like, page, pageSize),

            _ => throw new ApplicationException($"Modul '{module}' tidak dikenal.")
        };
    }

    private async Task<PagedResult<SyncListItemDto>> ListSimpleAsync(
        string table, string select, string baseWhere, string searchWhere,
        string editableSql, string lockReasonSql, string orderBy,
        string state, string? like, int page, int pageSize,
        Func<Dictionary<string, object?>, SyncListItemDto> map)
    {
        var where = new List<string> { baseWhere };
        if (like != null) where.Add(searchWhere);
        if (state == "editable") where.Add(editableSql);
        else if (state == "locked") where.Add($"NOT {editableSql}");
        var whereSql = string.Join(" AND ", where);

        var conn = await PgConnectionAsync();

        await using var countCmd = NewCommand(conn, $"SELECT COUNT(*) FROM {table} t WHERE {whereSql}");
        if (like != null) AddParam(countCmd, "q", like);
        var total = Convert.ToInt32(await countCmd.ExecuteScalarAsync());

        await using var dataCmd = NewCommand(conn, $@"
            SELECT {select}, {editableSql} AS editable, {lockReasonSql} AS lock_reason
            FROM {table} t
            WHERE {whereSql}
            ORDER BY {orderBy}
            LIMIT @limit OFFSET @offset");
        if (like != null) AddParam(dataCmd, "q", like);
        AddParam(dataCmd, "limit", pageSize);
        AddParam(dataCmd, "offset", (page - 1) * pageSize);

        var rows = await QueryAsync(dataCmd);
        var items = rows.Select(r =>
        {
            var item = map(r);
            item.Editable = r.Get("editable") is bool b && b;
            item.LockReason = Str(r.Get("lock_reason"));
            return item;
        }).ToList();

        return new PagedResult<SyncListItemDto>(items, total);
    }

    /// <summary>
    /// Jasa medis dikelompokkan per tindakan (kunjungan + tindakan + menit + dokter + kategori), karena
    /// tabel berisi banyak baris kembar hasil insert berulang scheduler. Satu kelompok = satu data.
    /// </summary>
    private async Task<PagedResult<SyncListItemDto>> ListMedicalFeeAsync(string state, string? like, int page, int pageSize)
    {
        // Tanpa pencarian, hanya tindakan 31 hari terakhir. Mengelompokkan seluruh tabel (±270 ribu baris)
        // butuh ±2,3 detik per muat halaman; dengan batas ini ±0,13 detik memakai index procedure_date.
        // Data lama tetap bisa dicari lewat no. rawat / nama pasien / dokter / tindakan.
        var where = like != null
            ? "WHERE (t.visit_no ILIKE @q OR t.patient_name ILIKE @q OR t.doctor_name ILIKE @q " +
              "OR t.procedure_name ILIKE @q OR t.medical_record_no ILIKE @q)"
            : $"WHERE t.procedure_date >= now() - interval '{MedicalFeeDefaultWindowDays} days'";
        var having = state switch
        {
            "editable" => $"HAVING bool_and({MfRowEditableSql})",
            "locked" => $"HAVING NOT bool_and({MfRowEditableSql})",
            _ => ""
        };

        var grouped = $@"
            SELECT t.visit_no, t.procedure_name, date_trunc('minute', t.procedure_date) AS pdate,
                   COALESCE(t.doctor_code,'') AS doctor_code, COALESCE(t.medical_procedure,'') AS medical_procedure,
                   MIN(t.doctor_name) AS doctor_name, MIN(t.patient_name) AS patient_name,
                   MIN(t.total_fee) AS total_fee,
                   COUNT(*) AS row_count, COUNT(DISTINCT t.total_fee) AS fee_variants,
                   bool_and({MfRowEditableSql}) AS editable,
                   string_agg(DISTINCT CASE WHEN NOT {MfRowEditableSql} THEN COALESCE(NULLIF(t.status,''),'-') END, '/') AS locked_status
            FROM public.trx_medical_fee t
            {where}
            GROUP BY t.visit_no, t.procedure_name, date_trunc('minute', t.procedure_date),
                     COALESCE(t.doctor_code,''), COALESCE(t.medical_procedure,'')
            {having}";

        var conn = await PgConnectionAsync();

        await using var countCmd = NewCommand(conn, $"SELECT COUNT(*) FROM ({grouped}) g");
        if (like != null) AddParam(countCmd, "q", like);
        var total = Convert.ToInt32(await countCmd.ExecuteScalarAsync());

        await using var dataCmd = NewCommand(conn, $"{grouped} ORDER BY pdate DESC NULLS LAST, t.visit_no DESC LIMIT @limit OFFSET @offset");
        if (like != null) AddParam(dataCmd, "q", like);
        AddParam(dataCmd, "limit", pageSize);
        AddParam(dataCmd, "offset", (page - 1) * pageSize);

        var rows = await QueryAsync(dataCmd);
        var items = rows.Select(r =>
        {
            var pdate = r.Get("pdate") as DateTime?;
            var rowCount = Convert.ToInt32(r.Get("row_count"));
            var editable = r.Get("editable") is bool b && b;
            var lockedStatus = Str(r.Get("locked_status"));
            return new SyncListItemDto
            {
                Key = MfKey(Str(r.Get("visit_no")), Str(r.Get("procedure_name")), pdate,
                            Str(r.Get("doctor_code")), Str(r.Get("medical_procedure"))),
                Title = Str(r.Get("procedure_name")) ?? "-",
                Subtitle = $"{Str(r.Get("patient_name"))} · {Str(r.Get("visit_no"))}",
                Info = $"{Str(r.Get("doctor_name"))} · {Str(r.Get("medical_procedure"))}",
                Amount = ToDouble(r.Get("total_fee")),
                Date = pdate,
                Editable = editable,
                LockReason = editable ? null : $"Status {lockedStatus}",
                RowCount = rowCount,
                ValuesDiffer = Convert.ToInt32(r.Get("fee_variants")) > 1
            };
        }).ToList();

        return new PagedResult<SyncListItemDto>(items, total);
    }

    /// <summary>Kunci jasa medis. Waktu dalam UTC dibulatkan ke menit, sama dengan pencocokan skrip.</summary>
    private static string MfKey(string? visitNo, string? procedureName, DateTime? procedureUtc, string? doctorCode, string? category)
        => SyncValue.KeyOf(
            visitNo,
            procedureName,
            procedureUtc?.ToString("yyyy-MM-ddTHH:mm", System.Globalization.CultureInfo.InvariantCulture),
            doctorCode ?? "",
            category ?? "");
}
