using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;

using Newtonsoft.Json;
namespace RSSMBackend.Services
{
    public class JournalJobService : IJournalJobService
    {
        private readonly ZeemarDbContext _context;
        private readonly AppSettings _appSettings;

        public JournalJobService(ZeemarDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<List<JobJournalResult>> Get(DateTime? journalDate)
        {
            // var dateTime = journalDate?.ToString("yyyy-MM-dd HH:mm:ss");    
            var dateTime = journalDate?.ToString("yyyy-MM-dd");

            var result = await _context.JobJournalResults
            .FromSqlRaw(@"
                WITH pemesanan AS (
                    select
                    pemesanan.no_faktur,
                    pemesanan.no_order,
                    datasuplier.nama_suplier,
                    petugas.nama,
                    pemesanan.tgl_tempo,
                    pemesanan.tgl_pesan,
                    pemesanan.tgl_faktur,
                    bangsal.nm_bangsal,
                    pemesanan.tagihan,
                    (
                        SELECT
                        ifnull(
                            SUM(besar_bayar),
                            0
                        )
                        FROM
                        bayar_pemesanan
                        where
                        bayar_pemesanan.no_faktur = pemesanan.no_faktur
                    ) as bayar,
                    datasuplier.nama_bank,
                    datasuplier.rekening
                    from pemesanan
                    inner join datasuplier
                    inner join bangsal
                    inner join petugas on
                        pemesanan.kode_suplier = datasuplier.kode_suplier
                        and pemesanan.nip = petugas.nip
                        and pemesanan.kd_bangsal = bangsal.kd_bangsal

                ),
                data_utama AS (
                    SELECT
                        jurnal.tgl_jurnal AS Tanggal,
                        detailjurnal.kd_rek AS KodeAkun,
                        jurnal.no_jurnal AS NoJurnal,
                        jurnal.no_bukti AS NoBukti,
                        CASE WHEN rekening.level = 1 THEN CONCAT('  ', rekening.nm_rek) ELSE rekening.nm_rek END AS NamaAkun,
                        CONCAT('No.Jur ', jurnal.no_jurnal, ', ', 'No.Buk ', jurnal.no_bukti, ', ', jurnal.keterangan) AS Keterangan,
                        detailjurnal.debet AS Debet,
                        detailjurnal.kredit AS Kredit,
                        penjab.png_jawab AS CaraBayar,
                        poliklinik.nm_poli AS NamaPoli,
                        jurnal.jam_jurnal AS JamJurnal,
                        pemesanan.no_order as NoPO,
                        pemesanan.tgl_tempo as TglPenerimaan,
                        pemesanan.tgl_pesan AS TglPO,
                        pemesanan.nama_suplier AS NamaSuplier,
                        pemesanan.bayar AS JmlTransaksi
                    FROM jurnal
                    INNER JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
                    INNER JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
                    LEFT JOIN reg_periksa ON reg_periksa.no_rawat = jurnal.no_bukti
                    LEFT JOIN penjab ON penjab.kd_pj = reg_periksa.kd_pj
                    LEFT JOIN poliklinik ON poliklinik.kd_poli = reg_periksa.kd_poli
                    LEFT JOIN pemesanan ON pemesanan.no_faktur = jurnal.no_bukti
                    WHERE jurnal.tgl_jurnal = {0}
                ),
                gabung AS (
                    SELECT
                        Tanggal, KodeAkun, NoJurnal, NoBukti, NamaAkun, Keterangan,
                        Debet, Kredit, CaraBayar, NamaPoli, JamJurnal,
                        NoPO, TglPenerimaan, TglPO, NamaSuplier, JmlTransaksi, 1 AS urutan
                    FROM data_utama
                )


                SELECT
                    Tanggal, KodeAkun, NoJurnal, NoBukti, NamaAkun, Keterangan,
                    Debet, Kredit, CaraBayar, NamaPoli, NoPO, TglPenerimaan,
                    TglPO, NamaSuplier, JmlTransaksi
                FROM gabung
                WHERE NoBukti like '%INV%'
                ORDER BY
                    urutan,
                    Tanggal ASC,
                    JamJurnal ASC,
                    NoJurnal ASC,
                    Debet DESC;
            ", dateTime)
            .ToListAsync();
            
            return result;
        }

        public async Task<List<JobTransactionResult>> GetTransaction(DateTime? journalDate)
        {
            // var dateTime = journalDate?.ToString("yyyy-MM-dd HH:mm:ss");            
            var dateTime = journalDate?.ToString("yyyy-MM-dd");

            var result = await _context.JobTransactionResults
                .FromSqlRaw(@"
                    select 
                        no_jurnal as NoJurnal,
                        tgl_jurnal as Tanggal,
                        keterangan as Keterangan
                    from jurnal
                    -- WHERE STR_TO_DATE(CONCAT(jurnal.tgl_jurnal, ' ', jurnal.jam_jurnal), '%Y-%m-%d %H:%i:%s') >= {0}
                    WHERE jurnal.tgl_jurnal = {0}
                    ", dateTime)
                .ToListAsync();
            
            return result;
        }
    }
}