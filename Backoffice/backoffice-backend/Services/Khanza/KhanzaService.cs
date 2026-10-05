using ClosedXML.Excel;
using MySql.Data.MySqlClient;
using RSSMBackend.Models;
using Microsoft.Extensions.Logging;
using System.Data;
using RSSMBackend.DTOs.Journal; // Check if needed, but KhanzaJournalDto is in Models

namespace RSSMBackend.Services
{
    public class KhanzaService : IKhanzaService
    {
        private readonly IOptionService _optionService;
        private readonly ILogger<KhanzaService> _logger;
        private const string LOG = "KhanzaService {x}";

        public KhanzaService(IOptionService optionService, ILogger<KhanzaService> logger)
        {
            _optionService = optionService;
            _logger = logger;
        }

        public async Task<PagedResult<KhanzaJournalDto>> GetJournals(DateTime startDate, DateTime endDate, int page, int pageSize, int branchId)
        {
             // Get MySQL connection config from Options
            var dbHost = await _optionService.GetByName("KHANZA_DB_HOST", branchId);
            var dbPort = await _optionService.GetByName("KHANZA_DB_PORT", branchId);
            var dbName = await _optionService.GetByName("KHANZA_DB_NAME", branchId);
            var dbUser = await _optionService.GetByName("KHANZA_DB_USER", branchId);
            var dbPass = await _optionService.GetByName("KHANZA_DB_PASS", branchId);

            if (dbHost == null || dbName == null || dbUser == null)
            {
                throw new Exception("Konfigurasi database Khanza belum diatur. Silakan atur di menu Pengaturan.");
            }

            string connStr = $"Server={dbHost.OptionValue};Port={dbPort?.OptionValue ?? "3306"};Database={dbName.OptionValue};Uid={dbUser.OptionValue};Pwd={dbPass?.OptionValue ?? ""};default command timeout=600;";

            await using var conn = new MySqlConnection(connStr);
            await conn.OpenAsync();

            // First, get total count
            string countSql = @"
                SELECT COUNT(*) as total
                FROM jurnal
                INNER JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
                INNER JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
                WHERE jurnal.tgl_jurnal BETWEEN @start AND @end
            ";

            await using var countCmd = new MySqlCommand(countSql, conn);
            countCmd.Parameters.AddWithValue("@start", startDate.Date);
            countCmd.Parameters.AddWithValue("@end", endDate.Date);
            countCmd.CommandTimeout = 600;
            
            int totalCount = Convert.ToInt32(await countCmd.ExecuteScalarAsync());

            // Calculate offset for pagination
            int offset = (page - 1) * pageSize;

            // Data query with LIMIT/OFFSET
            string sql = @"
                WITH pemesanan AS (
                SELECT
                    pemesanan.no_faktur,
                    pemesanan.no_order,
                    datasuplier.nama_suplier,
                    pemesanan.tgl_tempo,
                    pemesanan.tgl_pesan,
                    pemesanan.tgl_faktur,
                    pemesanan.kd_bangsal,
                    bangsal.nm_bangsal,
                    pemesanan.tagihan,
                    COALESCE(SUM(bayar_pemesanan.besar_bayar), 0) AS bayar,
                    datasuplier.nama_bank,
                    datasuplier.rekening,
                    pemesanan.kode_suplier
                FROM pemesanan
                JOIN datasuplier
                    ON pemesanan.kode_suplier = datasuplier.kode_suplier
                JOIN bangsal
                    ON pemesanan.kd_bangsal = bangsal.kd_bangsal
                LEFT JOIN bayar_pemesanan
                    ON bayar_pemesanan.no_faktur = pemesanan.no_faktur
                GROUP BY
                    pemesanan.no_faktur,
                    pemesanan.no_order,
                    datasuplier.nama_suplier,
                    pemesanan.tgl_tempo,
                    pemesanan.tgl_pesan,
                    pemesanan.tgl_faktur,
                    pemesanan.kd_bangsal,
                    bangsal.nm_bangsal,
                    pemesanan.tagihan,
                    datasuplier.nama_bank,
                    datasuplier.rekening
            )

            SELECT
                TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) AS tanggal,
                jurnal.no_jurnal AS no_jurnal,
                jurnal.no_bukti AS no_bukti,
                jurnal.jam_jurnal AS jam_jurnal,

                CASE
                    WHEN rekening.level = 1 THEN CONCAT('  ', rekening.nm_rek)
                    ELSE rekening.nm_rek
                END AS nama_akun,
                CONCAT(
                    'No.Jur ', jurnal.no_jurnal,
                    ', No.Buk ', jurnal.no_bukti,
                    ', ', jurnal.keterangan
                ) AS keterangan,

                detailjurnal.debet AS debet,
                detailjurnal.kredit AS kredit,
                detailjurnal.kd_rek AS kode_akun,

                poliklinik.kd_poli AS kode_poli,
                poliklinik.nm_poli AS nama_poli,

                pasien.no_rkm_medis AS no_rekam_medis,
                pasien.nm_pasien AS nama_pasien,

                dokter.kd_dokter AS kode_dokter,
                dokter.nm_dokter AS nama_dokter,

                penjab.kd_pj AS kode_penjab,
                penjab.png_jawab AS nama_penjab,
                penjab.png_jawab AS cara_bayar,

                IF(reg_periksa.status_lanjut = 'Ranap', timestamp(kamar_inap.tgl_masuk, kamar_inap.jam_masuk), timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg)) as tgl_masuk,
                IF(reg_periksa.status_lanjut = 'Ranap', timestamp(kamar_inap.tgl_keluar, kamar_inap.jam_keluar), timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg)) as tgl_keluar,
                reg_periksa.status_lanjut AS status_lanjutan,
                bangsal.kd_bangsal as kode_bangsal,
                bangsal.nm_bangsal as nama_bangsal,
                pemesanan.no_order as no_po,
                pemesanan.tgl_pesan as tgl_po,
                pemesanan.kode_suplier,
                pemesanan.nama_suplier,
                pemesanan.no_faktur
            FROM jurnal
            JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
            JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
            LEFT JOIN reg_periksa ON reg_periksa.no_rawat = jurnal.no_bukti
            LEFT JOIN penjab ON penjab.kd_pj = reg_periksa.kd_pj
            LEFT JOIN poliklinik ON poliklinik.kd_poli = reg_periksa.kd_poli
            LEFT JOIN pemesanan ON pemesanan.no_faktur = jurnal.no_bukti
            LEFT JOIN bangsal ON pemesanan.kd_bangsal = bangsal.kd_bangsal
            LEFT JOIN dokter ON reg_periksa.kd_dokter = dokter.kd_dokter
            LEFT JOIN pasien ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis
            LEFT JOIN (
                SELECT
                    no_rawat,
                    tgl_masuk,
                    jam_masuk,
                    tgl_keluar,
                    jam_keluar
                FROM (
                    SELECT
                        no_rawat,
                        tgl_masuk,
                        jam_masuk,
                        tgl_keluar,
                        jam_keluar,
                        ROW_NUMBER() OVER (
                            PARTITION BY no_rawat
                            ORDER BY tgl_masuk DESC, jam_masuk DESC
                        ) AS rn
                    FROM kamar_inap
                ) t
                WHERE rn = 1
            ) kamar_inap on reg_periksa.no_rawat = kamar_inap.no_rawat
            where jurnal.tgl_jurnal BETWEEN @start AND @end
            ORDER BY
                tanggal ASC,
                jam_jurnal ASC,
                no_jurnal ASC,
                debet DESC
            LIMIT @pageSize OFFSET @offset;    
            ";

            await using var cmd = new MySqlCommand(sql, conn);
            cmd.Parameters.AddWithValue("@start", startDate.Date);
            cmd.Parameters.AddWithValue("@end", endDate.Date);
            cmd.Parameters.AddWithValue("@pageSize", pageSize);
            cmd.Parameters.AddWithValue("@offset", offset);
            cmd.CommandTimeout = 600;

            await using var reader = await cmd.ExecuteReaderAsync();
            
            var data = new List<KhanzaJournalDto>();

            while (await reader.ReadAsync())
            {
                var item = new KhanzaJournalDto
                {
                    // Core journal fields (style: direct reader["..."])
                    Tanggal = reader["tanggal"]?.ToString(),
                    NoJurnal = reader["no_jurnal"]?.ToString(),
                    NoBukti = reader["no_bukti"]?.ToString(),
                    JamJurnal = reader["jam_jurnal"]?.ToString(),
                    KodeAkun = reader["kode_akun"]?.ToString(),
                    NamaAkun = reader["nama_akun"]?.ToString(),
                    Keterangan = reader["keterangan"]?.ToString(),
                    Debet = reader["debet"] != DBNull.Value ? Convert.ToDecimal(reader["debet"]) : 0,
                    Kredit = reader["kredit"] != DBNull.Value ? Convert.ToDecimal(reader["kredit"]) : 0,

                    // // Poli / Bangsal / status rawat
                    // KodePoli = reader["kode_poli"] != DBNull.Value ? reader["kode_poli"]?.ToString() : null,
                    // NamaPoli = reader["nama_poli"] != DBNull.Value ? reader["nama_poli"]?.ToString() : null,
                    // KodeBangsal = reader["kode_bangsal"] != DBNull.Value ? reader["kode_bangsal"]?.ToString() : null,
                    // NamaBangsal = reader["nama_bangsal"] != DBNull.Value ? reader["nama_bangsal"]?.ToString() : null,
                    // StatusLanjutan = reader["status_lanjutan"] != DBNull.Value ? reader["status_lanjutan"]?.ToString() : null,

                    // // Purchasing (pemesanan)
                    // NoPo = reader["no_po"] != DBNull.Value ? reader["no_po"]?.ToString() : null,
                    // TglPenerimaan = reader["tgl_penerimaan"] != DBNull.Value ? reader["tgl_penerimaan"]?.ToString() : null,
                    // TglPo = reader["tgl_po"] != DBNull.Value ? reader["tgl_po"]?.ToString() : null,
                    // NamaSuplier = reader["nama_suplier"] != DBNull.Value ? reader["nama_suplier"]?.ToString() : null,
                    // JmlTransaksi = reader["jml_transaksi"] != DBNull.Value ? Convert.ToDecimal(reader["jml_transaksi"]) : 0,
                    // NoFaktur = reader["no_faktur"] != DBNull.Value ? reader["no_faktur"]?.ToString() : null,

                    // Patient / doctor / payer
                    NoRekamMedis = reader["no_rekam_medis"] != DBNull.Value ? reader["no_rekam_medis"]?.ToString() : null,
                    NamaPasien = reader["nama_pasien"] != DBNull.Value ? reader["nama_pasien"]?.ToString() : null,
                    KodeDokter = reader["kode_dokter"] != DBNull.Value ? reader["kode_dokter"]?.ToString() : null,
                    NamaDokter = reader["nama_dokter"] != DBNull.Value ? reader["nama_dokter"]?.ToString() : null,
                    KodePenjab = reader["kode_penjab"] != DBNull.Value ? reader["kode_penjab"]?.ToString() : null,
                    NamaPenjab = reader["nama_penjab"] != DBNull.Value ? reader["nama_penjab"]?.ToString() : null,

                    // Backward-compatible
                    CaraBayar = reader["cara_bayar"] != DBNull.Value ? reader["cara_bayar"]?.ToString() : null,

                    // Admission/discharge timestamps
                    TglMasuk = reader["tgl_masuk"] != DBNull.Value ? reader["tgl_masuk"]?.ToString() : null,
                    TglKeluar = reader["tgl_keluar"] != DBNull.Value ? reader["tgl_keluar"]?.ToString() : null,

                    StatusLanjutan = reader["status_lanjutan"] != DBNull.Value ? reader["status_lanjutan"]?.ToString() : null,
                    KodeBangsal = reader["kode_bangsal"] != DBNull.Value ? reader["kode_bangsal"]?.ToString() : null,
                    NamaBangsal = reader["nama_bangsal"] != DBNull.Value ? reader["nama_bangsal"]?.ToString() : null,
                    NoPo = reader["no_po"] != DBNull.Value ? reader["no_po"]?.ToString() : null,
                    TglPo = reader["tgl_po"] != DBNull.Value ? reader["tgl_po"]?.ToString() : null,
                    KodeSuplier = reader["kode_suplier"] != DBNull.Value ? reader["kode_suplier"]?.ToString() : null,
                    NamaSuplier = reader["nama_suplier"] != DBNull.Value ? reader["nama_suplier"]?.ToString() : null,
                    NoFaktur = reader["no_faktur"] != DBNull.Value ? reader["no_faktur"]?.ToString() : null,
                };
                
                data.Add(item);
            }

            return new PagedResult<KhanzaJournalDto>(data, totalCount);
        }

        public async Task<byte[]> ExportJournals(DateTime startDate, DateTime endDate, int branchId)
        {
             // Get MySQL connection config from Options
            var dbHost = await _optionService.GetByName("KHANZA_DB_HOST", branchId);
            var dbPort = await _optionService.GetByName("KHANZA_DB_PORT", branchId);
            var dbName = await _optionService.GetByName("KHANZA_DB_NAME", branchId);
            var dbUser = await _optionService.GetByName("KHANZA_DB_USER", branchId);
            var dbPass = await _optionService.GetByName("KHANZA_DB_PASS", branchId);

            if (dbHost == null || dbName == null || dbUser == null)
            {
                throw new Exception("Konfigurasi database Khanza belum diatur. Silakan atur di menu Pengaturan.");
            }

            string connStr = $"Server={dbHost.OptionValue};Port={dbPort?.OptionValue ?? "3306"};Database={dbName.OptionValue};Uid={dbUser.OptionValue};Pwd={dbPass?.OptionValue ?? ""};default command timeout=600;";

            await using var conn = new MySqlConnection(connStr);
            await conn.OpenAsync();

            string sql = @"
                WITH pemesanan AS (
                SELECT
                    pemesanan.no_faktur,
                    pemesanan.no_order,
                    datasuplier.nama_suplier,
                    pemesanan.tgl_tempo,
                    pemesanan.tgl_pesan,
                    pemesanan.tgl_faktur,
                    pemesanan.kd_bangsal,
                    bangsal.nm_bangsal,
                    pemesanan.tagihan,
                    COALESCE(SUM(bayar_pemesanan.besar_bayar), 0) AS bayar,
                    datasuplier.nama_bank,
                    datasuplier.rekening
                FROM pemesanan
                JOIN datasuplier
                    ON pemesanan.kode_suplier = datasuplier.kode_suplier
                JOIN bangsal
                    ON pemesanan.kd_bangsal = bangsal.kd_bangsal
                LEFT JOIN bayar_pemesanan
                    ON bayar_pemesanan.no_faktur = pemesanan.no_faktur
                GROUP BY
                    pemesanan.no_faktur,
                    pemesanan.no_order,
                    datasuplier.nama_suplier,
                    pemesanan.tgl_tempo,
                    pemesanan.tgl_pesan,
                    pemesanan.tgl_faktur,
                    pemesanan.kd_bangsal,
                    bangsal.nm_bangsal,
                    pemesanan.tagihan,
                    datasuplier.nama_bank,
                    datasuplier.rekening
            )

            SELECT
                TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) AS tanggal,
                jurnal.no_jurnal AS no_jurnal,
                jurnal.no_bukti AS no_bukti,
                jurnal.jam_jurnal AS jam_jurnal,

                CASE
                    WHEN rekening.level = 1 THEN CONCAT('  ', rekening.nm_rek)
                    ELSE rekening.nm_rek
                END AS nama_akun,
                CONCAT(
                    'No.Jur ', jurnal.no_jurnal,
                    ', No.Buk ', jurnal.no_bukti,
                    ', ', jurnal.keterangan
                ) AS keterangan,

                detailjurnal.debet AS debet,
                detailjurnal.kredit AS kredit,
                detailjurnal.kd_rek AS kode_akun,

                poliklinik.kd_poli AS kode_poli,
                poliklinik.nm_poli AS nama_poli,

                pasien.no_rkm_medis AS no_rekam_medis,
                pasien.nm_pasien AS nama_pasien,

                dokter.kd_dokter AS kode_dokter,
                dokter.nm_dokter AS nama_dokter,

                penjab.kd_pj AS kode_penjab,
                penjab.png_jawab AS nama_penjab,
                penjab.png_jawab AS cara_bayar,

                IF(reg_periksa.status_lanjut = 'Ranap', timestamp(kamar_inap.tgl_masuk, kamar_inap.jam_masuk), timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg)) as tgl_masuk,
                IF(reg_periksa.status_lanjut = 'Ranap', timestamp(kamar_inap.tgl_keluar, kamar_inap.jam_keluar), timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg)) as tgl_keluar
            FROM jurnal
            JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
            JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
            LEFT JOIN reg_periksa ON reg_periksa.no_rawat = jurnal.no_bukti
            LEFT JOIN penjab ON penjab.kd_pj = reg_periksa.kd_pj
            LEFT JOIN poliklinik ON poliklinik.kd_poli = reg_periksa.kd_poli
            LEFT JOIN pemesanan ON pemesanan.no_faktur = jurnal.no_bukti
            LEFT JOIN bangsal ON pemesanan.kd_bangsal = bangsal.kd_bangsal
            LEFT JOIN dokter ON reg_periksa.kd_dokter = dokter.kd_dokter
            LEFT JOIN pasien ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis
            LEFT JOIN (
                SELECT
                    no_rawat,
                    tgl_masuk,
                    jam_masuk,
                    tgl_keluar,
                    jam_keluar
                FROM (
                    SELECT
                        no_rawat,
                        tgl_masuk,
                        jam_masuk,
                        tgl_keluar,
                        jam_keluar,
                        ROW_NUMBER() OVER (
                            PARTITION BY no_rawat
                            ORDER BY tgl_masuk DESC, jam_masuk DESC
                        ) AS rn
                    FROM kamar_inap
                ) t
                WHERE rn = 1
            ) kamar_inap on reg_periksa.no_rawat = kamar_inap.no_rawat
            where jurnal.tgl_jurnal BETWEEN @start AND @end
            ORDER BY
                tanggal ASC,
                jam_jurnal ASC,
                no_jurnal ASC,
                debet DESC;
            ";

            await using var cmd = new MySqlCommand(sql, conn);
            cmd.Parameters.AddWithValue("@start", startDate.Date);
            cmd.Parameters.AddWithValue("@end", endDate.Date);
            cmd.CommandTimeout = 600;

            await using var reader = await cmd.ExecuteReaderAsync();
            var dt = new System.Data.DataTable();
            dt.Load(reader);

            if (dt.Rows.Count == 0)
            {
                return null;
            }

            // Generate Excel using ClosedXML
            using var wb = new XLWorkbook();
            var ws = wb.Worksheets.Add("Jurnal Khanza");
            ws.Cell(1, 1).InsertTable(dt);
            ws.Columns().AdjustToContents();

            using var stream = new MemoryStream();
            wb.SaveAs(stream);
            return stream.ToArray();
        }
    }
}
