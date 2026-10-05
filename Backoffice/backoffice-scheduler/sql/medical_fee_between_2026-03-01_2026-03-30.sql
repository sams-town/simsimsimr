-- Khanza (MySQL/MariaDB) — raw query medical fee, mirror script/run_medical_fee.py
-- Filter tanggal tindakan per sumber data: BETWEEN 2026-03-01 dan 2026-03-30 (inklusif).
-- Sesuaikan literal tanggal di tiap WHERE jika perlu.

SELECT
    reg.no_rawat AS visit_no,
    t.kd_dokter AS docter_code,
    t.dokter_tindakan AS doctor_name,
    MONTH(t.tgl_tindakan) AS period_month,
    YEAR(t.tgl_tindakan) AS period_year,
    (t.sarana + t.jm) AS total_fee,
    t.kategori AS medical_procedure,
    'Pending' AS status,
    1 AS branch_id,
    reg.no_rkm_medis AS medical_record_no,
    ps.nm_pasien AS patient_name,
    t.tgl_tindakan AS procedure_date,
    t.nm_perawatan AS procedure_name,
    pj.kd_pj AS payer_code,
    pj.nama_perusahaan AS payer_name,
    pol.kd_poli AS clinic_code,
    pol.nm_poli AS clinic_name,
    bs.no_sep AS no_sep
FROM reg_periksa reg
INNER JOIN pasien ps ON reg.no_rkm_medis = ps.no_rkm_medis
INNER JOIN dokter dk ON reg.kd_dokter = dk.kd_dokter
INNER JOIN penjab pj ON reg.kd_pj = pj.kd_pj
INNER JOIN poliklinik pol ON reg.kd_poli = pol.kd_poli
LEFT JOIN (
    SELECT no_rawat, MIN(no_sep) AS no_sep
    FROM bridging_sep GROUP BY no_rawat
) bs ON reg.no_rawat = bs.no_rawat
INNER JOIN (

    -- 1. RALAN DOKTER
    SELECT rjd.no_rawat, 'Rawat Jalan' AS kategori,
        rjd.kd_dokter,
        jp.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(rjd.tgl_perawatan, rjd.jam_rawat) AS tgl_tindakan,
        COUNT(rjd.no_rawat) AS jumlah,
        (rjd.material+rjd.bhp+rjd.kso+rjd.menejemen) AS sarana,
        SUM(rjd.material+rjd.bhp+rjd.kso+rjd.menejemen) AS totalsarana,
        rjd.tarif_tindakandr AS jm,
        SUM(rjd.tarif_tindakandr) AS totaljm
    FROM rawat_jl_dr rjd
    INNER JOIN jns_perawatan jp ON rjd.kd_jenis_prw=jp.kd_jenis_prw
    INNER JOIN dokter d ON rjd.kd_dokter=d.kd_dokter
    WHERE rjd.tgl_perawatan BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY rjd.no_rawat, rjd.kd_jenis_prw, rjd.tgl_perawatan

    UNION ALL

    -- 2. RALAN DOKTER + PARAMEDIS
    SELECT rjdp.no_rawat, 'Rawat Jalan' AS kategori,
        rjdp.kd_dokter,
        jp.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(rjdp.tgl_perawatan, rjdp.jam_rawat) AS tgl_tindakan,
        COUNT(rjdp.no_rawat) AS jumlah,
        (rjdp.material+rjdp.bhp+rjdp.kso+rjdp.menejemen+rjdp.tarif_tindakanpr) AS sarana,
        SUM(rjdp.material+rjdp.bhp+rjdp.kso+rjdp.menejemen+rjdp.tarif_tindakanpr) AS totalsarana,
        rjdp.tarif_tindakandr AS jm,
        SUM(rjdp.tarif_tindakandr) AS totaljm
    FROM rawat_jl_drpr rjdp
    INNER JOIN jns_perawatan jp ON rjdp.kd_jenis_prw=jp.kd_jenis_prw
    INNER JOIN dokter d ON rjdp.kd_dokter=d.kd_dokter
    WHERE rjdp.tgl_perawatan BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY rjdp.no_rawat, rjdp.kd_jenis_prw, rjdp.tgl_perawatan

    UNION ALL

    -- 3. RALAN PARAMEDIS
    SELECT rjp.no_rawat, 'Rawat Jalan' AS kategori,
        rjp.nip AS kd_dokter,
        jp.nm_perawatan, pt.nama AS dokter_tindakan,
        TIMESTAMP(rjp.tgl_perawatan, rjp.jam_rawat) AS tgl_tindakan,
        COUNT(rjp.no_rawat) AS jumlah,
        (rjp.material+rjp.bhp+rjp.kso+rjp.menejemen) AS sarana,
        SUM(rjp.material+rjp.bhp+rjp.kso+rjp.menejemen) AS totalsarana,
        rjp.tarif_tindakanpr AS jm,
        SUM(rjp.tarif_tindakanpr) AS totaljm
    FROM rawat_jl_pr rjp
    INNER JOIN jns_perawatan jp ON rjp.kd_jenis_prw=jp.kd_jenis_prw
    INNER JOIN petugas pt ON rjp.nip=pt.nip
    WHERE rjp.tgl_perawatan BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY rjp.no_rawat, rjp.kd_jenis_prw, rjp.tgl_perawatan

    UNION ALL

    -- 4. RANAP DOKTER
    SELECT rid.no_rawat, 'Rawat Inap' AS kategori,
        rid.kd_dokter,
        jpi.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(rid.tgl_perawatan, rid.jam_rawat) AS tgl_tindakan,
        COUNT(rid.no_rawat) AS jumlah,
        (rid.material+rid.bhp+rid.kso+rid.menejemen) AS sarana,
        SUM(rid.material+rid.bhp+rid.kso+rid.menejemen) AS totalsarana,
        rid.tarif_tindakandr AS jm,
        SUM(rid.tarif_tindakandr) AS totaljm
    FROM rawat_inap_dr rid
    INNER JOIN jns_perawatan_inap jpi ON rid.kd_jenis_prw=jpi.kd_jenis_prw
    INNER JOIN dokter d ON rid.kd_dokter=d.kd_dokter
    WHERE rid.tgl_perawatan BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY rid.no_rawat, rid.kd_jenis_prw, rid.tgl_perawatan

    UNION ALL

    -- 5. RANAP DOKTER + PARAMEDIS
    SELECT ridp.no_rawat, 'Rawat Inap' AS kategori,
        ridp.kd_dokter,
        jpi.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(ridp.tgl_perawatan, ridp.jam_rawat) AS tgl_tindakan,
        COUNT(ridp.no_rawat) AS jumlah,
        (ridp.material+ridp.bhp+ridp.kso+ridp.menejemen+ridp.tarif_tindakanpr) AS sarana,
        SUM(ridp.material+ridp.bhp+ridp.kso+ridp.menejemen+ridp.tarif_tindakanpr) AS totalsarana,
        ridp.tarif_tindakandr AS jm,
        SUM(ridp.tarif_tindakandr) AS totaljm
    FROM rawat_inap_drpr ridp
    INNER JOIN jns_perawatan_inap jpi ON ridp.kd_jenis_prw=jpi.kd_jenis_prw
    INNER JOIN dokter d ON ridp.kd_dokter=d.kd_dokter
    WHERE ridp.tgl_perawatan BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY ridp.no_rawat, ridp.kd_jenis_prw, ridp.tgl_perawatan

    UNION ALL

    -- 6. RANAP PARAMEDIS
    SELECT rip.no_rawat, 'Rawat Inap' AS kategori,
        rip.nip AS kd_dokter,
        jpi.nm_perawatan, pt.nama AS dokter_tindakan,
        TIMESTAMP(rip.tgl_perawatan, rip.jam_rawat) AS tgl_tindakan,
        COUNT(rip.no_rawat) AS jumlah,
        (rip.material+rip.bhp+rip.kso+rip.menejemen) AS sarana,
        SUM(rip.material+rip.bhp+rip.kso+rip.menejemen) AS totalsarana,
        rip.tarif_tindakanpr AS jm,
        SUM(rip.tarif_tindakanpr) AS totaljm
    FROM rawat_inap_pr rip
    INNER JOIN jns_perawatan_inap jpi ON rip.kd_jenis_prw=jpi.kd_jenis_prw
    INNER JOIN petugas pt ON rip.nip=pt.nip
    WHERE rip.tgl_perawatan BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY rip.no_rawat, rip.kd_jenis_prw, rip.tgl_perawatan

    UNION ALL

    -- 7. RADIOLOGI
    SELECT pr.no_rawat, 'Radiologi' AS kategori,
        pr.kd_dokter,
        jpr.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(pr.tgl_periksa, pr.jam) AS tgl_tindakan,
        COUNT(pr.no_rawat) AS jumlah,
        (pr.bagian_rs+pr.bhp+pr.kso+pr.menejemen+pr.tarif_perujuk+pr.tarif_tindakan_petugas) AS sarana,
        SUM(pr.bagian_rs+pr.bhp+pr.kso+pr.menejemen+pr.tarif_perujuk+pr.tarif_tindakan_petugas) AS totalsarana,
        pr.tarif_tindakan_dokter AS jm,
        SUM(pr.tarif_tindakan_dokter) AS totaljm
    FROM periksa_radiologi pr
    INNER JOIN jns_perawatan_radiologi jpr ON pr.kd_jenis_prw=jpr.kd_jenis_prw
    INNER JOIN dokter d ON pr.kd_dokter=d.kd_dokter
    WHERE pr.tgl_periksa BETWEEN '2026-03-01' AND '2026-03-30'
    GROUP BY pr.no_rawat, pr.kd_jenis_prw, pr.tgl_periksa

    UNION ALL

    -- 8a. OPERASI - OPERATOR 1
    SELECT op.no_rawat, 'Operasi Op1' AS kategori,
        op.operator1 AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        op.biayaoperator1 AS jm, op.biayaoperator1 AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.operator1=d.kd_dokter
    WHERE op.biayaoperator1 > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 8b. OPERASI - OPERATOR 2
    SELECT op.no_rawat, 'Operasi Op2' AS kategori,
        op.operator2 AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        op.biayaoperator2 AS jm, op.biayaoperator2 AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.operator2=d.kd_dokter
    WHERE op.biayaoperator2 > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 8c. OPERASI - OPERATOR 3
    SELECT op.no_rawat, 'Operasi Op3' AS kategori,
        op.operator3 AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        op.biayaoperator3 AS jm, op.biayaoperator3 AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.operator3=d.kd_dokter
    WHERE op.biayaoperator3 > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 8d. OPERASI - DOKTER ANESTESI
    SELECT op.no_rawat, 'Operasi Anestesi' AS kategori,
        op.dokter_anestesi AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        op.biayadokter_anestesi AS jm, op.biayadokter_anestesi AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.dokter_anestesi=d.kd_dokter
    WHERE op.biayadokter_anestesi > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 8e. OPERASI - DOKTER ANAK
    SELECT op.no_rawat, 'Operasi dr Anak' AS kategori,
        op.dokter_anak AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        op.biayadokter_anak AS jm, op.biayadokter_anak AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.dokter_anak=d.kd_dokter
    WHERE op.biayadokter_anak > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 8f. OPERASI - DOKTER PJ ANAK
    SELECT op.no_rawat, 'Operasi dr PJ Anak' AS kategori,
        op.dokter_pjanak AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        IFNULL(op.biaya_dokter_pjanak,0) AS jm,
        IFNULL(op.biaya_dokter_pjanak,0) AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.dokter_pjanak=d.kd_dokter
    WHERE IFNULL(op.biaya_dokter_pjanak,0) > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 8g. OPERASI - DOKTER UMUM
    SELECT op.no_rawat, 'Operasi dr Umum' AS kategori,
        op.dokter_umum AS kd_dokter,
        po.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        op.tgl_operasi AS tgl_tindakan,
        1 AS jumlah, 0 AS sarana, 0 AS totalsarana,
        IFNULL(op.biaya_dokter_umum,0) AS jm,
        IFNULL(op.biaya_dokter_umum,0) AS totaljm
    FROM operasi op
    INNER JOIN paket_operasi po ON op.kode_paket=po.kode_paket
    INNER JOIN dokter d ON op.dokter_umum=d.kd_dokter
    WHERE IFNULL(op.biaya_dokter_umum,0) > 0
      AND DATE(op.tgl_operasi) BETWEEN '2026-03-01' AND '2026-03-30'

    UNION ALL

    -- 9. LABORATORIUM
    SELECT pl.no_rawat, 'Laboratorium' AS kategori,
        pl.kd_dokter,
        jpl.nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(pl.tgl_periksa, pl.jam) AS tgl_tindakan,
        COUNT(pl.no_rawat) AS jumlah,
        (pl.bagian_rs+pl.bhp+pl.kso+pl.menejemen+pl.tarif_perujuk+pl.tarif_tindakan_petugas) AS sarana,
        SUM(pl.bagian_rs+pl.bhp+pl.kso+pl.menejemen+pl.tarif_perujuk+pl.tarif_tindakan_petugas) AS totalsarana,
        pl.tarif_tindakan_dokter AS jm,
        SUM(pl.tarif_tindakan_dokter) AS totaljm
    FROM periksa_lab pl
    INNER JOIN jns_perawatan_lab jpl ON pl.kd_jenis_prw=jpl.kd_jenis_prw
    INNER JOIN dokter d ON pl.kd_dokter=d.kd_dokter
    WHERE pl.tgl_periksa BETWEEN '2026-03-01' AND '2026-03-30'
      AND pl.biaya > 0
    GROUP BY pl.no_rawat, pl.kd_jenis_prw, pl.tgl_periksa

    UNION ALL

    -- 10. LABORATORIUM DETAIL
    SELECT dpl.no_rawat, 'Laboratorium' AS kategori,
        pl2.kd_dokter,
        tl.Pemeriksaan AS nm_perawatan, d.nm_dokter AS dokter_tindakan,
        TIMESTAMP(dpl.tgl_periksa, dpl.jam) AS tgl_tindakan,
        COUNT(dpl.no_rawat) AS jumlah,
        (dpl.bagian_rs+dpl.bhp+dpl.kso+dpl.menejemen+dpl.bagian_perujuk+dpl.bagian_laborat) AS sarana,
        SUM(dpl.bagian_rs+dpl.bhp+dpl.kso+dpl.menejemen+dpl.bagian_perujuk+dpl.bagian_laborat) AS totalsarana,
        dpl.bagian_dokter AS jm,
        SUM(dpl.bagian_dokter) AS totaljm
    FROM detail_periksa_lab dpl
    INNER JOIN template_laboratorium tl ON dpl.id_template=tl.id_template
    INNER JOIN periksa_lab pl2 ON pl2.kd_jenis_prw=dpl.kd_jenis_prw
        AND pl2.tgl_periksa=dpl.tgl_periksa AND pl2.jam=dpl.jam AND pl2.no_rawat=dpl.no_rawat
    INNER JOIN dokter d ON pl2.kd_dokter=d.kd_dokter
    WHERE dpl.tgl_periksa BETWEEN '2026-03-01' AND '2026-03-30'
      AND dpl.biaya_item > 0
    GROUP BY dpl.no_rawat, dpl.id_template, dpl.tgl_periksa

) t ON reg.no_rawat = t.no_rawat

ORDER BY t.tgl_tindakan, t.kategori, reg.no_rawat;
