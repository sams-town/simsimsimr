using System.Data.Common;
using System.Globalization;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.DTOs.KhanzaSync;

namespace RSSMBackend.Services.KhanzaSync;

public partial class KhanzaSyncService
{
    /// <summary>Record aplikasi yang sudah dinormalisasi. Untuk jasa medis bisa mewakili beberapa baris kembar.</summary>
    private sealed class AppRecord
    {
        public string Title = "";
        public string? Subtitle;
        public bool Editable = true;
        public string? LockReason;
        public int RowCount;
        public bool ValuesDiffer;
        public Dictionary<string, string?> Values = new();
        public List<string> RefIds = new();
    }

    private sealed record KhanzaLookup(Dictionary<string, Dictionary<string, string?>> Records, Dictionary<string, string> Notes);

    private sealed record SimpleSpec(
        string Table, string IdColumn, string[] KeyColumns, string TitleColumn, string SubtitleColumn,
        string EditableSql, string LockReasonSql);

    private static SimpleSpec SpecOf(ModuleDef def) => def.Code switch
    {
        "ap" => new("public.trx_invoice_exchange", "invoice_exchange_id", new[] { "po_number", "invoice_number" },
                    "invoice_number", "supplier_name", ApEditableSql, ApLockReasonSql),
        "ar" => new("public.trx_account_receivable", "ar_id", new[] { "ar_number", "payer_id" },
                    "ar_number", "patient_name", ArEditableSql, ArLockReasonSql),
        "po" => new("public.thb_purchase_order", "order_number", new[] { "order_number" },
                    "order_number", "supplier_name", PoEditableSql, PoLockReasonSql),
        _ => throw new InvalidOperationException($"Modul {def.Code} tidak memakai spesifikasi tabel sederhana.")
    };

    private static List<string> CleanKeys(IEnumerable<string>? keys)
    {
        var list = (keys ?? Enumerable.Empty<string>())
            .Where(k => !string.IsNullOrWhiteSpace(k))
            .Distinct(StringComparer.Ordinal)
            .ToList();
        if (list.Count == 0)
            throw new ApplicationException("Pilih minimal satu data.");
        if (list.Count > MaxKeysPerRequest)
            throw new ApplicationException($"Maksimal {MaxKeysPerRequest} data per proses.");
        return list;
    }

    private static string FieldColumns(ModuleDef def) => string.Join(", ", def.Fields.Select(f => $"t.\"{f.Column}\""));

    // ------------------------------------------------------------------------------------------------
    // Data aplikasi
    // ------------------------------------------------------------------------------------------------

    private async Task<Dictionary<string, AppRecord>> LoadAppAsync(
        ModuleDef def, IReadOnlyList<string> keys, DbConnection conn, DbTransaction? tx, bool forUpdate)
    {
        return def.Code == "mf"
            ? await LoadAppMedicalFeeAsync(def, keys, conn, tx, forUpdate)
            : await LoadAppSimpleAsync(def, keys, conn, tx, forUpdate);
    }

    private async Task<Dictionary<string, AppRecord>> LoadAppSimpleAsync(
        ModuleDef def, IReadOnlyList<string> keys, DbConnection conn, DbTransaction? tx, bool forUpdate)
    {
        var spec = SpecOf(def);
        await using var cmd = NewCommand(conn, "", tx);

        var tuples = new List<string>();
        for (int i = 0; i < keys.Count; i++)
        {
            var parts = SyncValue.SplitKey(keys[i]);
            var ps = new List<string>();
            for (int c = 0; c < spec.KeyColumns.Length; c++)
            {
                var name = $"k{i}_{c}";
                AddParam(cmd, name, c < parts.Length ? parts[c] : "");
                ps.Add("@" + name);
            }
            tuples.Add("(" + string.Join(",", ps) + ")");
        }

        var keyExpr = "(" + string.Join(", ", spec.KeyColumns.Select(k => $"COALESCE(t.{k},'')")) + ")";
        cmd.CommandText = $@"
            SELECT t.{spec.IdColumn} AS __id, {string.Join(", ", spec.KeyColumns.Select(k => $"t.{k} AS __k_{k}"))},
                   t.{spec.TitleColumn} AS __title, t.{spec.SubtitleColumn} AS __subtitle,
                   {FieldColumns(def)},
                   {spec.EditableSql} AS __editable, {spec.LockReasonSql} AS __lock_reason
            FROM {spec.Table} t
            WHERE {keyExpr} IN ({string.Join(",", tuples)})
            {(forUpdate ? "FOR UPDATE OF t" : "")}";

        var rows = await QueryAsync(cmd);
        var result = new Dictionary<string, AppRecord>(StringComparer.Ordinal);
        foreach (var r in rows)
        {
            var key = SyncValue.KeyOf(spec.KeyColumns.Select(k => Str(r.Get("__k_" + k))).ToArray());
            var values = def.Fields.ToDictionary(f => f.Column, f => SyncValue.Normalize(r.Get(f.Column), f.Type));
            Merge(result, key, values, Str(r.Get("__id")) ?? "",
                  Str(r.Get("__title")) ?? "-", Str(r.Get("__subtitle")),
                  r.Get("__editable") is bool b && b, Str(r.Get("__lock_reason")));
        }
        return result;
    }

    private async Task<Dictionary<string, AppRecord>> LoadAppMedicalFeeAsync(
        ModuleDef def, IReadOnlyList<string> keys, DbConnection conn, DbTransaction? tx, bool forUpdate)
    {
        var wanted = new HashSet<string>(keys, StringComparer.Ordinal);
        var visits = keys.Select(k => SyncValue.SplitKey(k)[0]).Distinct().ToList();

        await using var cmd = NewCommand(conn, "", tx);
        var names = new List<string>();
        for (int i = 0; i < visits.Count; i++)
        {
            AddParam(cmd, $"v{i}", visits[i]);
            names.Add($"@v{i}");
        }

        cmd.CommandText = $@"
            SELECT t.medical_fee_id AS __id, t.visit_no AS __visit, t.procedure_name AS __procedure,
                   date_trunc('minute', t.procedure_date) AS __pdate,
                   COALESCE(t.doctor_code,'') AS __doctor, COALESCE(t.medical_procedure,'') AS __category,
                   {FieldColumns(def)},
                   {MfRowEditableSql} AS __editable, t.status AS __status
            FROM public.trx_medical_fee t
            WHERE t.visit_no IN ({string.Join(",", names)})
            ORDER BY t.medical_fee_id
            {(forUpdate ? "FOR UPDATE OF t" : "")}";

        var rows = await QueryAsync(cmd);
        var result = new Dictionary<string, AppRecord>(StringComparer.Ordinal);
        foreach (var r in rows)
        {
            var key = MfKey(Str(r.Get("__visit")), Str(r.Get("__procedure")), r.Get("__pdate") as DateTime?,
                            Str(r.Get("__doctor")), Str(r.Get("__category")));
            if (!wanted.Contains(key)) continue;

            var editable = r.Get("__editable") is bool b && b;
            var values = def.Fields.ToDictionary(f => f.Column, f => SyncValue.Normalize(r.Get(f.Column), f.Type));
            Merge(result, key, values, Str(r.Get("__id")) ?? "",
                  Str(r.Get("__procedure")) ?? "-",
                  $"{Str(r.Get("patient_name"))} · {Str(r.Get("__visit"))}",
                  editable, editable ? null : $"Status {Str(r.Get("__status")) ?? "-"}");
        }
        return result;
    }

    private static void Merge(
        Dictionary<string, AppRecord> into, string key, Dictionary<string, string?> values, string refId,
        string title, string? subtitle, bool editable, string? lockReason)
    {
        if (!into.TryGetValue(key, out var rec))
        {
            into[key] = new AppRecord
            {
                Title = title,
                Subtitle = subtitle,
                Editable = editable,
                LockReason = lockReason,
                RowCount = 1,
                Values = values,
                RefIds = new List<string> { refId }
            };
            return;
        }

        // Baris kembar: nilai acuan tetap baris pertama, tapi tandai bila isinya tidak seragam dan
        // kunci seluruh kelompok bila salah satu barisnya sudah diproses.
        rec.RowCount++;
        rec.RefIds.Add(refId);
        if (!editable)
        {
            rec.Editable = false;
            rec.LockReason ??= lockReason;
        }
        if (values.Any(kv => !string.Equals(rec.Values.GetValueOrDefault(kv.Key), kv.Value, StringComparison.Ordinal)))
            rec.ValuesDiffer = true;
    }

    // ------------------------------------------------------------------------------------------------
    // Data Khanza
    // ------------------------------------------------------------------------------------------------

    private async Task<KhanzaLookup> FetchKhanzaAsync(ModuleDef def, IReadOnlyList<string> keys, int branchId)
    {
        var records = new Dictionary<string, Dictionary<string, string?>>(StringComparer.Ordinal);
        var notes = new Dictionary<string, string>(StringComparer.Ordinal);
        var parts = keys.ToDictionary(k => k, SyncValue.SplitKey, StringComparer.Ordinal);

        Dictionary<string, string?> Values(Dictionary<string, object?> row) =>
            def.Fields.ToDictionary(f => f.Column, f => SyncValue.Normalize(row.Get(f.Column), f.Type));

        await using var kconn = await _khanza.OpenReadOnlyAsync(branchId);

        switch (def.Code)
        {
            case "ap":
            {
                var rows = await KhanzaSourceQueries.FetchApAsync(kconn, parts.Values.Select(p => p.ElementAtOrDefault(1) ?? "").Distinct().ToList());
                // Baris sudah terurut jurnal terbaru dulu: yang pertama per kunci adalah nilai yang berlaku.
                foreach (var r in rows)
                    records.TryAdd(SyncValue.KeyOf(Str(r.Get("po_number")), Str(r.Get("invoice_number"))), Values(r));

                foreach (var (key, p) in parts)
                {
                    if (records.ContainsKey(key)) continue;
                    var sameInvoice = rows.FirstOrDefault(r => Str(r.Get("invoice_number")) == p.ElementAtOrDefault(1));
                    notes[key] = sameInvoice != null
                        ? $"No. PO faktur ini di Khanza sekarang {Str(sameInvoice.Get("po_number")) ?? "(kosong)"}, tidak bisa dicocokkan otomatis."
                        : "Faktur tidak ditemukan di Khanza, atau belum punya jurnal penerimaan.";
                }
                break;
            }
            case "ar":
            {
                var rows = await KhanzaSourceQueries.FetchArAsync(kconn, parts.Values.Select(p => p[0]).Distinct().ToList());
                foreach (var r in rows)
                    records.TryAdd(SyncValue.KeyOf(Str(r.Get("ar_number")), Str(r.Get("payer_id"))), Values(r));

                foreach (var (key, p) in parts)
                {
                    if (records.ContainsKey(key)) continue;
                    var other = rows.FirstOrDefault(r => Str(r.Get("ar_number")) == p[0]);
                    notes[key] = other != null
                        ? $"Penjamin di Khanza sekarang {Str(other.Get("payer_name"))} ({Str(other.Get("payer_id"))}), tidak bisa dicocokkan otomatis."
                        : "Piutang tidak ditemukan di Khanza (mungkin sudah dilunasi atau dihapus di Khanza).";
                }
                break;
            }
            case "po":
            {
                var rows = await KhanzaSourceQueries.FetchPoAsync(kconn, parts.Values.Select(p => p[0]).Distinct().ToList());
                foreach (var r in rows)
                    records.TryAdd(SyncValue.KeyOf(Str(r.Get("order_number"))), Values(r));

                foreach (var key in parts.Keys.Where(k => !records.ContainsKey(k)))
                    notes[key] = "Surat pemesanan tidak ditemukan di Khanza.";
                break;
            }
            case "mf":
            {
                var rows = await KhanzaMedicalFeeQuery.FetchAsync(kconn, parts.Values.Select(p => p[0]).Distinct().ToList());

                // Sama seperti skrip: nama pasien diganti nama kontak bila No. RM terdaftar sebagai kontak.
                var rmNos = rows.Select(r => Str(r.Get("medical_record_no"))).Where(s => !string.IsNullOrEmpty(s)).Distinct().ToArray();
                var contactNames = await ContactNamesByRefCodeAsync(rmNos!);

                foreach (var r in rows)
                {
                    var wib = SyncValue.ToDateTime(r.Get("procedure_date"));
                    DateTime? utc = null;
                    if (wib != null)
                    {
                        // Jam dinding Khanza = WIB (UTC+7).
                        var u = DateTime.SpecifyKind(wib.Value, DateTimeKind.Unspecified).AddHours(-7);
                        utc = new DateTime(u.Year, u.Month, u.Day, u.Hour, u.Minute, 0, DateTimeKind.Utc);
                    }

                    var key = MfKey(Str(r.Get("visit_no")), Str(r.Get("procedure_name")), utc,
                                    Str(r.Get("doctor_code")) ?? "", Str(r.Get("medical_procedure")) ?? "");

                    var rm = Str(r.Get("medical_record_no"));
                    if (rm != null && contactNames.TryGetValue(rm, out var contactName))
                        r["patient_name"] = contactName;
                    if (r.Get("no_sep") == null)
                        r["no_sep"] = "0";

                    records.TryAdd(key, Values(r));
                }

                foreach (var key in parts.Keys.Where(k => !records.ContainsKey(k)))
                    notes[key] = "Tindakan tidak ditemukan di Khanza - mungkin dihapus, atau dokter/waktu/kategorinya diubah.";
                break;
            }
        }

        return new KhanzaLookup(records, notes);
    }

    private async Task<Dictionary<string, string>> ContactNamesByRefCodeAsync(string[] refCodes)
    {
        var map = new Dictionary<string, string>(StringComparer.Ordinal);
        if (refCodes.Length == 0) return map;

        var conn = await PgConnectionAsync();
        await using var cmd = NewCommand(conn, @"
            SELECT DISTINCT ON (ref_code) ref_code, contact_name
            FROM master.mst_contact
            WHERE ref_code = ANY(@codes) AND contact_name IS NOT NULL
            ORDER BY ref_code, contact_id");
        AddParam(cmd, "codes", refCodes);

        foreach (var r in await QueryAsync(cmd))
        {
            var code = Str(r.Get("ref_code"));
            var name = Str(r.Get("contact_name"));
            if (code != null && name != null) map[code] = name;
        }
        return map;
    }

    // ------------------------------------------------------------------------------------------------
    // Bandingkan
    // ------------------------------------------------------------------------------------------------

    public async Task<List<SyncCompareRecordDto>> Compare(string module, List<string> keys, int roleId, int branchId)
    {
        EnsureRole(roleId);
        var def = Def(module);
        var wanted = CleanKeys(keys);

        var conn = await PgConnectionAsync();
        var app = await LoadAppAsync(def, wanted, conn, null, forUpdate: false);
        var khanza = await FetchKhanzaAsync(def, wanted, branchId);

        return wanted.Select(key =>
        {
            app.TryGetValue(key, out var a);
            khanza.Records.TryGetValue(key, out var kz);
            var parts = SyncValue.SplitKey(key);

            var dto = new SyncCompareRecordDto
            {
                Key = key,
                Title = a?.Title ?? (def.Code is "ap" or "mf" ? parts.ElementAtOrDefault(1) : parts[0]) ?? "-",
                Subtitle = a?.Subtitle,
                FoundInApp = a != null,
                FoundInKhanza = kz != null,
                Editable = a is { Editable: true } && kz != null,
                LockReason = a == null ? "Tidak ditemukan di aplikasi" : a.LockReason,
                Note = kz == null ? khanza.Notes.GetValueOrDefault(key) : null,
                RowCount = a?.RowCount ?? 0,
                AppValuesDiffer = a?.ValuesDiffer ?? false
            };

            foreach (var f in def.Fields)
            {
                var appValue = a?.Values.GetValueOrDefault(f.Column);
                var khanzaValue = kz?.GetValueOrDefault(f.Column);
                var equal = kz != null && string.Equals(appValue, khanzaValue, StringComparison.Ordinal);
                if (kz != null && a != null && !equal) dto.ChangedCount++;
                dto.Fields.Add(new SyncCompareFieldDto
                {
                    Column = f.Column,
                    Label = f.Label,
                    Type = f.Type.ToString(),
                    AppValue = appValue,
                    KhanzaValue = khanzaValue,
                    Equal = equal
                });
            }
            return dto;
        }).ToList();
    }

    // ------------------------------------------------------------------------------------------------
    // Terapkan
    // ------------------------------------------------------------------------------------------------

    public async Task<List<SyncApplyResultDto>> Apply(string module, SyncApplyRequestDto request, int roleId, int branchId, int userId)
    {
        EnsureRole(roleId);
        var def = Def(module);

        var items = (request?.Items ?? new())
            .Where(i => !string.IsNullOrWhiteSpace(i.Key))
            .GroupBy(i => i.Key, StringComparer.Ordinal)
            .Select(g => g.First())
            .ToList();
        CleanKeys(items.Select(i => i.Key));

        var fieldMap = def.Fields.ToDictionary(f => f.Column, StringComparer.OrdinalIgnoreCase);

        // Nilai tulis selalu diambil ulang dari Khanza di sini, bukan dari request.
        var khanza = await FetchKhanzaAsync(def, items.Select(i => i.Key).ToList(), branchId);

        var results = new List<SyncApplyResultDto>();
        foreach (var item in items)
            results.Add(await ApplyOneAsync(def, item, fieldMap, khanza, branchId, userId));
        return results;
    }

    private async Task<SyncApplyResultDto> ApplyOneAsync(
        ModuleDef def, SyncApplyItemDto item, Dictionary<string, SyncFieldDef> fieldMap,
        KhanzaLookup khanza, int branchId, int userId)
    {
        var result = new SyncApplyResultDto { Key = item.Key };

        var requested = (item.Fields ?? new()).Distinct(StringComparer.OrdinalIgnoreCase).ToList();
        if (requested.Count == 0)
        {
            result.Message = "Tidak ada field yang dipilih.";
            return result;
        }
        var unknown = requested.Where(c => !fieldMap.ContainsKey(c)).ToList();
        if (unknown.Count > 0)
        {
            result.Message = $"Field tidak dikenal atau tidak boleh disinkron: {string.Join(", ", unknown)}.";
            return result;
        }
        if (!khanza.Records.TryGetValue(item.Key, out var kz))
        {
            result.Message = khanza.Notes.GetValueOrDefault(item.Key) ?? "Tidak ditemukan di Khanza.";
            return result;
        }

        var fields = requested.Select(c => fieldMap[c]).ToList();

        await using var tx = await _context.Database.BeginTransactionAsync();
        var dbTx = tx.GetDbTransaction();
        var conn = dbTx.Connection!;

        try
        {
            // Kunci baris dulu, lalu periksa ulang status & nilai di dalam transaksi yang sama.
            var app = await LoadAppAsync(def, new List<string> { item.Key }, conn, dbTx, forUpdate: true);
            if (!app.TryGetValue(item.Key, out var a))
                return await FailAsync(tx, result, "Data tidak ditemukan di aplikasi.");
            if (!a.Editable)
                return await FailAsync(tx, result, $"Terkunci: {a.LockReason}.");

            foreach (var f in fields)
            {
                item.ExpectedAppValues.TryGetValue(f.Column, out var expected);
                if (!string.Equals(expected, a.Values.GetValueOrDefault(f.Column), StringComparison.Ordinal))
                    return await FailAsync(tx, result,
                        $"Nilai {f.Label} di aplikasi sudah berubah sejak dibandingkan. Bandingkan ulang sebelum menerapkan.");
            }

            var changes = fields
                .Where(f => !string.Equals(a.Values.GetValueOrDefault(f.Column), kz.GetValueOrDefault(f.Column), StringComparison.Ordinal))
                .ToList();
            if (changes.Count == 0)
            {
                await tx.RollbackAsync();
                result.Success = true;
                result.Message = "Sudah sama dengan Khanza, tidak ada yang diubah.";
                return result;
            }

            await using var cmd = NewCommand(conn, "", dbTx);
            var sets = new List<string>();
            for (int i = 0; i < changes.Count; i++)
            {
                var f = changes[i];
                sets.Add($"\"{f.Column}\" = {string.Format(CultureInfo.InvariantCulture, f.PgCast, "@s" + i)}");
                AddParam(cmd, "s" + i, SyncValue.ToDbValue(kz.GetValueOrDefault(f.Column), f.Type));
            }
            sets.Add("updated_by = @uid");
            sets.Add("updated_date = now()");
            AddParam(cmd, "uid", userId);

            var (table, where, editable) = UpdateTarget(def, item.Key, cmd);
            cmd.CommandText = $"UPDATE {table} t SET {string.Join(", ", sets)} WHERE {where} AND {editable}";

            var updated = await cmd.ExecuteNonQueryAsync();
            if (updated != a.RowCount)
                return await FailAsync(tx, result,
                    $"Hanya {updated} dari {a.RowCount} baris yang bisa diubah (status berubah saat diterapkan). Tidak ada yang disimpan.");

            await tx.CommitAsync();

            var before = changes.ToDictionary(f => f.Column, f => a.Values.GetValueOrDefault(f.Column));
            var after = changes.ToDictionary(f => f.Column, f => kz.GetValueOrDefault(f.Column));
            foreach (var refId in a.RefIds)
            {
                try
                {
                    await _audit.Add(def.AuditModule, "Sinkron Khanza", refId, before, after, userId, "Success", "", branchId);
                }
                catch (Exception e)
                {
                    // Perubahan sudah tersimpan; kegagalan audit dicatat tanpa membatalkan hasil.
                    _logger.LogWarning(e, "Audit sinkron Khanza gagal untuk {Module} {RefId}", def.Code, refId);
                }
            }

            result.Success = true;
            result.UpdatedRows = updated;
            result.AppliedFields = changes.Select(f => f.Column).ToList();
            result.Message = updated > 1
                ? $"{changes.Count} field diperbarui pada {updated} baris."
                : $"{changes.Count} field diperbarui.";
            return result;
        }
        catch (Exception e)
        {
            await tx.RollbackAsync();
            _logger.LogError(e, "Sinkron Khanza gagal untuk {Module} {Key}", def.Code, item.Key);
            result.Message = $"Gagal menerapkan: {e.Message}";
            return result;
        }
    }

    private static async Task<SyncApplyResultDto> FailAsync(IDbContextTransaction tx, SyncApplyResultDto result, string message)
    {
        await tx.RollbackAsync();
        result.Success = false;
        result.Message = message;
        return result;
    }

    /// <summary>Tabel, kondisi kunci, dan syarat "boleh diubah" untuk UPDATE. Parameter kunci ditambahkan ke cmd.</summary>
    private static (string Table, string Where, string Editable) UpdateTarget(ModuleDef def, string key, DbCommand cmd)
    {
        var p = SyncValue.SplitKey(key);
        string Part(int i) => i < p.Length ? p[i] : "";

        if (def.Code == "mf")
        {
            AddParam(cmd, "k0", Part(0));
            AddParam(cmd, "k1", Part(1));
            AddParam(cmd, "k3", Part(3));
            AddParam(cmd, "k4", Part(4));

            string dateCond;
            if (DateTime.TryParseExact(Part(2), "yyyy-MM-ddTHH:mm", CultureInfo.InvariantCulture,
                    DateTimeStyles.AdjustToUniversal | DateTimeStyles.AssumeUniversal, out var pdate))
            {
                AddParam(cmd, "k2", DateTime.SpecifyKind(pdate, DateTimeKind.Utc));
                dateCond = "date_trunc('minute', t.procedure_date) = @k2::timestamptz";
            }
            else
            {
                dateCond = "t.procedure_date IS NULL";
            }

            return ("public.trx_medical_fee",
                    $"t.visit_no = @k0 AND COALESCE(t.procedure_name,'') = @k1 AND {dateCond} " +
                    "AND COALESCE(t.doctor_code,'') = @k3 AND COALESCE(t.medical_procedure,'') = @k4",
                    MfRowEditableSql);
        }

        var spec = SpecOf(def);
        var conds = new List<string>();
        for (int i = 0; i < spec.KeyColumns.Length; i++)
        {
            AddParam(cmd, "k" + i, Part(i));
            conds.Add($"COALESCE(t.{spec.KeyColumns[i]},'') = @k{i}");
        }
        return (spec.Table, string.Join(" AND ", conds), spec.EditableSql);
    }
}
