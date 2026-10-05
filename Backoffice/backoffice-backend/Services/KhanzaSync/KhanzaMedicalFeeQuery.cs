using MySql.Data.MySqlClient;

namespace RSSMBackend.Services.KhanzaSync;

/// <summary>
/// Query jasa medis per kunjungan. Sumber: run_medical_fee.py - daftar cabang UNION dan rumus
/// sarana + jasa medis disalin apa adanya; hanya filter tanggal diganti filter no_rawat.
///
/// procedure_date dikembalikan sebagai jam dinding Khanza (WIB, tanpa zona waktu). Service yang
/// mengonversinya ke UTC sebelum dicocokkan, sama seperti skrip yang menulis dengan sesi Asia/Jakarta.
/// </summary>
internal static class KhanzaMedicalFeeQuery
{
    // Nama kolom hasil UNION diambil dari cabang pertama, jadi cabang lain cukup menjaga urutan kolom.
    private static IEnumerable<string> Branches()
    {
        // 1-6. Rawat jalan & rawat inap: dokter, dokter+paramedis, paramedis.
        yield return @"
            SELECT rjd.no_rawat, 'Rawat Jalan' AS kategori, rjd.kd_dokter, jp.nm_perawatan, d.nm_dokter AS dokter_tindakan,
                   TIMESTAMP(rjd.tgl_perawatan, rjd.jam_rawat) AS tgl_tindakan,
                   (rjd.material+rjd.bhp+rjd.kso+rjd.menejemen) AS sarana, rjd.tarif_tindakandr AS jm
            FROM rawat_jl_dr rjd
            INNER JOIN jns_perawatan jp ON rjd.kd_jenis_prw=jp.kd_jenis_prw
            INNER JOIN dokter d ON rjd.kd_dokter=d.kd_dokter
            WHERE rjd.no_rawat IN ({V})
            GROUP BY rjd.no_rawat, rjd.kd_jenis_prw, rjd.tgl_perawatan";

        yield return @"
            SELECT rjdp.no_rawat, 'Rawat Jalan', rjdp.kd_dokter, jp.nm_perawatan, d.nm_dokter,
                   TIMESTAMP(rjdp.tgl_perawatan, rjdp.jam_rawat),
                   (rjdp.material+rjdp.bhp+rjdp.kso+rjdp.menejemen+rjdp.tarif_tindakanpr), rjdp.tarif_tindakandr
            FROM rawat_jl_drpr rjdp
            INNER JOIN jns_perawatan jp ON rjdp.kd_jenis_prw=jp.kd_jenis_prw
            INNER JOIN dokter d ON rjdp.kd_dokter=d.kd_dokter
            WHERE rjdp.no_rawat IN ({V})
            GROUP BY rjdp.no_rawat, rjdp.kd_jenis_prw, rjdp.tgl_perawatan";

        yield return @"
            SELECT rjp.no_rawat, 'Rawat Jalan', rjp.nip, jp.nm_perawatan, pt.nama,
                   TIMESTAMP(rjp.tgl_perawatan, rjp.jam_rawat),
                   (rjp.material+rjp.bhp+rjp.kso+rjp.menejemen), rjp.tarif_tindakanpr
            FROM rawat_jl_pr rjp
            INNER JOIN jns_perawatan jp ON rjp.kd_jenis_prw=jp.kd_jenis_prw
            INNER JOIN petugas pt ON rjp.nip=pt.nip
            WHERE rjp.no_rawat IN ({V})
            GROUP BY rjp.no_rawat, rjp.kd_jenis_prw, rjp.tgl_perawatan";

        yield return @"
            SELECT rid.no_rawat, 'Rawat Inap', rid.kd_dokter, jpi.nm_perawatan, d.nm_dokter,
                   TIMESTAMP(rid.tgl_perawatan, rid.jam_rawat),
                   (rid.material+rid.bhp+rid.kso+rid.menejemen), rid.tarif_tindakandr
            FROM rawat_inap_dr rid
            INNER JOIN jns_perawatan_inap jpi ON rid.kd_jenis_prw=jpi.kd_jenis_prw
            INNER JOIN dokter d ON rid.kd_dokter=d.kd_dokter
            WHERE rid.no_rawat IN ({V})
            GROUP BY rid.no_rawat, rid.kd_jenis_prw, rid.tgl_perawatan";

        yield return @"
            SELECT ridp.no_rawat, 'Rawat Inap', ridp.kd_dokter, jpi.nm_perawatan, d.nm_dokter,
                   TIMESTAMP(ridp.tgl_perawatan, ridp.jam_rawat),
                   (ridp.material+ridp.bhp+ridp.kso+ridp.menejemen+ridp.tarif_tindakanpr), ridp.tarif_tindakandr
            FROM rawat_inap_drpr ridp
            INNER JOIN jns_perawatan_inap jpi ON ridp.kd_jenis_prw=jpi.kd_jenis_prw
            INNER JOIN dokter d ON ridp.kd_dokter=d.kd_dokter
            WHERE ridp.no_rawat IN ({V})
            GROUP BY ridp.no_rawat, ridp.kd_jenis_prw, ridp.tgl_perawatan";

        yield return @"
            SELECT rip.no_rawat, 'Rawat Inap', rip.nip, jpi.nm_perawatan, pt.nama,
                   TIMESTAMP(rip.tgl_perawatan, rip.jam_rawat),
                   (rip.material+rip.bhp+rip.kso+rip.menejemen), rip.tarif_tindakanpr
            FROM rawat_inap_pr rip
            INNER JOIN jns_perawatan_inap jpi ON rip.kd_jenis_prw=jpi.kd_jenis_prw
            INNER JOIN petugas pt ON rip.nip=pt.nip
            WHERE rip.no_rawat IN ({V})
            GROUP BY rip.no_rawat, rip.kd_jenis_prw, rip.tgl_perawatan";

        // 7. Radiologi.
        yield return @"
            SELECT pr.no_rawat, 'Radiologi', pr.kd_dokter, jpr.nm_perawatan, d.nm_dokter,
                   TIMESTAMP(pr.tgl_periksa, pr.jam),
                   (pr.bagian_rs+pr.bhp+pr.kso+pr.menejemen+pr.tarif_perujuk+pr.tarif_tindakan_petugas), pr.tarif_tindakan_dokter
            FROM periksa_radiologi pr
            INNER JOIN jns_perawatan_radiologi jpr ON pr.kd_jenis_prw=jpr.kd_jenis_prw
            INNER JOIN dokter d ON pr.kd_dokter=d.kd_dokter
            WHERE pr.no_rawat IN ({V})
            GROUP BY pr.no_rawat, pr.kd_jenis_prw, pr.tgl_periksa";

        // 8a-8g. Operasi, satu cabang per peran dokter.
        var operasi = new (string Label, string Doctor, string Fee)[]
        {
            ("Operasi Op1", "op.operator1", "op.biayaoperator1"),
            ("Operasi Op2", "op.operator2", "op.biayaoperator2"),
            ("Operasi Op3", "op.operator3", "op.biayaoperator3"),
            ("Operasi Anestesi", "op.dokter_anestesi", "op.biayadokter_anestesi"),
            ("Operasi dr Anak", "op.dokter_anak", "op.biayadokter_anak"),
            ("Operasi dr PJ Anak", "op.dokter_pjanak", "IFNULL(op.biaya_dokter_pjanak,0)"),
            ("Operasi dr Umum", "op.dokter_umum", "IFNULL(op.biaya_dokter_umum,0)")
        };
        foreach (var o in operasi)
        {
            yield return $@"
            SELECT op.no_rawat, '{o.Label}', {o.Doctor}, po.nm_perawatan, d.nm_dokter,
                   op.tgl_operasi, 0, {o.Fee}
            FROM operasi op
            INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
            INNER JOIN dokter d ON {o.Doctor}=d.kd_dokter
            WHERE {o.Fee} > 0 AND op.no_rawat IN ({{V}})";
        }

        // 9. Laboratorium.
        yield return @"
            SELECT pl.no_rawat, 'Laboratorium', pl.kd_dokter, jpl.nm_perawatan, d.nm_dokter,
                   TIMESTAMP(pl.tgl_periksa, pl.jam),
                   (pl.bagian_rs+pl.bhp+pl.kso+pl.menejemen+pl.tarif_perujuk+pl.tarif_tindakan_petugas), pl.tarif_tindakan_dokter
            FROM periksa_lab pl
            INNER JOIN jns_perawatan_lab jpl ON pl.kd_jenis_prw=jpl.kd_jenis_prw
            INNER JOIN dokter d ON pl.kd_dokter=d.kd_dokter
            WHERE pl.no_rawat IN ({V}) AND pl.biaya > 0
            GROUP BY pl.no_rawat, pl.kd_jenis_prw, pl.tgl_periksa";

        // 10. Laboratorium detail.
        yield return @"
            SELECT dpl.no_rawat, 'Laboratorium', pl2.kd_dokter, tl.Pemeriksaan, d.nm_dokter,
                   TIMESTAMP(dpl.tgl_periksa, dpl.jam),
                   (dpl.bagian_rs+dpl.bhp+dpl.kso+dpl.menejemen+dpl.bagian_perujuk+dpl.bagian_laborat), dpl.bagian_dokter
            FROM detail_periksa_lab dpl
            INNER JOIN template_laboratorium tl ON dpl.id_template=tl.id_template
            INNER JOIN periksa_lab pl2 ON pl2.kd_jenis_prw=dpl.kd_jenis_prw
                AND pl2.tgl_periksa=dpl.tgl_periksa AND pl2.jam=dpl.jam AND pl2.no_rawat=dpl.no_rawat
            INNER JOIN dokter d ON pl2.kd_dokter=d.kd_dokter
            WHERE dpl.no_rawat IN ({V}) AND dpl.biaya_item > 0
            GROUP BY dpl.no_rawat, dpl.id_template, dpl.tgl_periksa";
    }

    public static async Task<List<Dictionary<string, object?>>> FetchAsync(MySqlConnection conn, IReadOnlyList<string> visitNumbers)
    {
        if (visitNumbers.Count == 0) return new();

        await using var cmd = new MySqlCommand { Connection = conn, CommandTimeout = 180 };
        var names = new List<string>(visitNumbers.Count);
        for (int i = 0; i < visitNumbers.Count; i++)
        {
            var n = $"@v{i}";
            cmd.Parameters.AddWithValue(n, visitNumbers[i]);
            names.Add(n);
        }
        var v = string.Join(",", names);
        var union = string.Join("\n            UNION ALL\n", Branches().Select(b => b.Replace("{V}", v)));

        cmd.CommandText = $@"
            SELECT reg.no_rawat AS visit_no,
                   t.kd_dokter AS doctor_code,
                   t.dokter_tindakan AS doctor_name,
                   MONTH(t.tgl_tindakan) AS period_month,
                   YEAR(t.tgl_tindakan) AS period_year,
                   (t.sarana + t.jm) AS total_fee,
                   t.kategori AS medical_procedure,
                   reg.no_rkm_medis AS medical_record_no,
                   ps.nm_pasien AS patient_name,
                   t.tgl_tindakan AS procedure_date,
                   t.nm_perawatan AS procedure_name,
                   pj.kd_pj AS payer_code,
                   pj.nama_perusahaan AS payer_name,
                   bs.no_sep AS no_sep
            FROM reg_periksa reg
            INNER JOIN pasien ps ON reg.no_rkm_medis = ps.no_rkm_medis
            INNER JOIN dokter dk ON reg.kd_dokter = dk.kd_dokter
            INNER JOIN penjab pj ON reg.kd_pj = pj.kd_pj
            INNER JOIN poliklinik pol ON reg.kd_poli = pol.kd_poli
            LEFT JOIN (
                SELECT no_rawat, MIN(no_sep) AS no_sep FROM bridging_sep
                WHERE no_rawat IN ({v}) GROUP BY no_rawat
            ) bs ON reg.no_rawat = bs.no_rawat
            INNER JOIN (
            {union}
            ) t ON reg.no_rawat = t.no_rawat
            WHERE reg.no_rawat IN ({v})";

        await using var reader = await cmd.ExecuteReaderAsync();
        return await SyncValue.ReadAllAsync(reader);
    }
}
