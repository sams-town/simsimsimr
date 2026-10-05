using MySql.Data.MySqlClient;

namespace RSSMBackend.Services.KhanzaSync;

/// <summary>
/// Query per-record ke Khanza untuk AP, AR, dan PO.
///
/// Definisi kolom disalin dari skrip scheduler (backoffice-scheduler/script/run_*.py) supaya nilai yang
/// dibandingkan sama persis dengan nilai yang dulu ditarik. Bedanya hanya filter: skrip memfilter per
/// tanggal jurnal, di sini per kunci record. Kalau skrip scheduler diubah, sesuaikan juga di sini.
///
/// Semua kolom hasil sudah diberi nama kolom tabel aplikasi.
/// </summary>
internal static class KhanzaSourceQueries
{
    private static string InParams(MySqlCommand cmd, string prefix, IReadOnlyList<string> values)
    {
        var names = new List<string>(values.Count);
        for (int i = 0; i < values.Count; i++)
        {
            var n = $"@{prefix}{i}";
            cmd.Parameters.AddWithValue(n, values[i]);
            names.Add(n);
        }
        return string.Join(",", names);
    }

    private static async Task<List<Dictionary<string, object?>>> RunAsync(MySqlCommand cmd)
    {
        await using var reader = await cmd.ExecuteReaderAsync();
        return await SyncValue.ReadAllAsync(reader);
    }

    /// <summary>
    /// AP / faktur. Sumber: run_faktur.py. Baris diurutkan jurnal terbaru dulu: kalau penerimaan pernah
    /// dibatalkan lalu diposting ulang di Khanza, nilai yang berlaku adalah jurnal terakhir - justru
    /// perubahan seperti ini yang tidak tertangkap scheduler (skrip mengambil jurnal pertama).
    /// </summary>
    public static async Task<List<Dictionary<string, object?>>> FetchApAsync(MySqlConnection conn, IReadOnlyList<string> invoiceNumbers)
    {
        if (invoiceNumbers.Count == 0) return new();
        await using var cmd = new MySqlCommand { Connection = conn, CommandTimeout = 120 };
        var inv = InParams(cmd, "inv", invoiceNumbers);

        cmd.CommandText = $@"
            SELECT src.po_number, src.invoice_number, src.receipt_date, src.po_date, src.due_date,
                   src.supplier_code, src.supplier_name,
                   x.kredit  AS total_transaction,
                   x.tanggal AS journal_date
            FROM (
                SELECT p.no_order AS po_number, p.no_faktur AS invoice_number, p.tgl_faktur AS receipt_date,
                       p.tgl_pesan AS po_date, p.tgl_tempo AS due_date,
                       CONCAT('NM', SUBSTR(s.kode_suplier, 2)) AS supplier_code, s.nama_suplier AS supplier_name
                FROM ipsrspemesanan p
                LEFT JOIN ipsrssuplier s ON p.kode_suplier = s.kode_suplier
                WHERE p.no_faktur IN ({inv})

                UNION ALL

                SELECT p.no_order, p.no_faktur, p.tgl_faktur, p.tgl_pesan, p.tgl_tempo,
                       CONCAT('M', SUBSTR(s.kode_suplier, 2)), s.nama_suplier
                FROM pemesanan p
                LEFT JOIN datasuplier s ON p.kode_suplier = s.kode_suplier
                WHERE p.no_faktur IN ({inv})

                UNION ALL

                SELECT p.no_order, p.no_faktur, p.tgl_faktur, p.tgl_pesan, p.tgl_tempo,
                       CONCAT('DP', SUBSTR(s.kode_suplier, 2)), s.nama_suplier
                FROM dapurpemesanan p
                LEFT JOIN dapursuplier s ON p.kode_suplier = s.kode_suplier
                WHERE p.no_faktur IN ({inv})
            ) src
            JOIN (
                SELECT jurnal.no_bukti, jurnal.no_jurnal,
                       TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) AS tanggal,
                       CAST(detailjurnal.kredit AS DOUBLE) AS kredit
                FROM jurnal
                JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
                JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
                WHERE jurnal.no_bukti IN ({inv})
                  AND detailjurnal.kredit <> 0
            ) x ON x.no_bukti = src.invoice_number
            ORDER BY x.tanggal DESC, x.no_jurnal DESC, x.kredit DESC";

        return await RunAsync(cmd);
    }

    /// <summary>
    /// AR / piutang pasien. Sumber: run_ar.py. Diambil piutang terbaru per (no_rawat, penjamin).
    /// Kolom payment_status sengaja tidak diambil: di skrip nilainya di-hardcode 'Pending', bukan data Khanza.
    /// </summary>
    public static async Task<List<Dictionary<string, object?>>> FetchArAsync(MySqlConnection conn, IReadOnlyList<string> arNumbers)
    {
        if (arNumbers.Count == 0) return new();
        await using var cmd = new MySqlCommand { Connection = conn, CommandTimeout = 120 };
        var rawat = InParams(cmd, "ar", arNumbers);

        cmd.CommandText = $@"
            SELECT reg.no_rawat AS ar_number,
                   pj.kd_pj AS payer_id,
                   pj.png_jawab AS payer_name,
                   IF(pj.kd_pj = 'BPJ', 'bpjs', 'non bpjs') AS payer_type,
                   reg.status_lanjut AS service_type,
                   TIMESTAMP(reg.tgl_registrasi, reg.jam_reg) AS pipeline_date,
                   pp.amount AS total_amount,
                   ps.no_rkm_medis AS medical_record_no,
                   ps.nm_pasien AS patient_name,
                   poli.kd_poli AS clinic_code,
                   poli.nm_poli AS clinic_name,
                   bs.no_sep AS no_sep,
                   pp.status AS billing_status,
                   pp.tanggal AS transaction_date
            FROM (
                SELECT dpp.no_rawat, pp.status, pp.tgl_piutang AS tanggal, pp.sisapiutang AS amount, dpp.kd_pj,
                       ROW_NUMBER() OVER (PARTITION BY dpp.no_rawat, dpp.kd_pj ORDER BY pp.tgl_piutang DESC) AS rn
                FROM detail_piutang_pasien dpp
                JOIN piutang_pasien pp ON pp.no_rawat = dpp.no_rawat
                WHERE dpp.no_rawat IN ({rawat})
            ) pp
            JOIN reg_periksa reg ON reg.no_rawat = pp.no_rawat
            LEFT JOIN penjab pj ON pp.kd_pj = pj.kd_pj
            LEFT JOIN pasien ps ON ps.no_rkm_medis = reg.no_rkm_medis
            LEFT JOIN poliklinik poli ON poli.kd_poli = reg.kd_poli
            LEFT JOIN bridging_sep bs ON reg.no_rawat = bs.no_rujukan
            WHERE pp.rn = 1";

        return await RunAsync(cmd);
    }

    /// <summary>PO / surat pemesanan. Sumber: run_purchase_order.py.</summary>
    public static async Task<List<Dictionary<string, object?>>> FetchPoAsync(MySqlConnection conn, IReadOnlyList<string> orderNumbers)
    {
        if (orderNumbers.Count == 0) return new();
        await using var cmd = new MySqlCommand { Connection = conn, CommandTimeout = 120 };
        var po = InParams(cmd, "po", orderNumbers);

        cmd.CommandText = $@"
            SELECT spm.no_pemesanan AS order_number,
                   CONCAT('M', SUBSTR(spm.kode_suplier, 2)) AS supplier_code,
                   ds.nama_suplier AS supplier_name,
                   spm.nip AS nip, spm.tanggal AS date,
                   spm.total1 AS total, spm.potongan AS discount, spm.total2 AS grand_total,
                   spm.ppn AS ppn, spm.meterai AS stamp_duty, spm.tagihan AS invoice_amount,
                   spm.status AS status, 'pemesanan medis' AS source
            FROM surat_pemesanan_medis spm
            LEFT JOIN datasuplier ds ON ds.kode_suplier = spm.kode_suplier
            WHERE spm.no_pemesanan IN ({po})

            UNION ALL

            SELECT spm.no_pemesanan, CONCAT('DP', SUBSTR(spm.kode_suplier, 2)), ds.nama_suplier,
                   spm.nip, spm.tanggal, spm.subtotal, spm.potongan, spm.total,
                   spm.ppn, spm.meterai, spm.tagihan, spm.status, 'pemesanan dapur'
            FROM surat_pemesanan_dapur spm
            LEFT JOIN dapursuplier ds ON ds.kode_suplier = spm.kode_suplier
            WHERE spm.no_pemesanan IN ({po})

            UNION ALL

            SELECT spm.no_pemesanan, CONCAT('NM', SUBSTR(spm.kode_suplier, 2)), ds.nama_suplier,
                   spm.nip, spm.tanggal, spm.subtotal, spm.potongan, spm.total,
                   spm.ppn, spm.meterai, spm.tagihan, spm.status, 'pemesanan non medis'
            FROM surat_pemesanan_non_medis spm
            LEFT JOIN ipsrssuplier ds ON ds.kode_suplier = spm.kode_suplier
            WHERE spm.no_pemesanan IN ({po})";

        return await RunAsync(cmd);
    }
}
