"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Khanza Medical Services Migration
  Created   : 2025
========================================================
"""
import sys
import os
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, project_root)

from libs import get_khanza_connection, get_production_connection, create_log,date_range,date_month
from datetime import datetime
import pandas as pd
import warnings
import argparse
import traceback
from psycopg2.extras import execute_values
warnings.filterwarnings("ignore")

# ================================
# CONFIG UPDATE
# ================================
ALL_UPDATE_COLUMNS = [
    "patient_id",
    "patient_name",
    "medical_record_no",
    "doctor_id",
    "doctor_name",
    "period_month",
    "period_year",
    "total_fee",
    "medical_procedure",
    "status",
    "branch_id",
    "payer_code",
    "payer_name",
    "doctor_code",
    "no_sep",
    "adjustment_amount",
    "adjustment_date"
]

KEY_COLUMNS = ["visit_no", "procedure_date", "total_fee", "procedure_name"]


def get_medical_fee(mysql_conn, dt_value, use_delta=False):
    if use_delta:
        if isinstance(dt_value, datetime):
            date_str = dt_value.strftime("'%Y-%m-%d %H:%M:%S'")
        else:
            date_str = f"'{dt_value}'"
        where_rjd = f"WHERE TIMESTAMP(rjd.tgl_perawatan, rjd.jam_rawat) >= {date_str}"
        where_rjdp = f"WHERE TIMESTAMP(rjdp.tgl_perawatan, rjdp.jam_rawat) >= {date_str}"
        where_rjp = f"WHERE TIMESTAMP(rjp.tgl_perawatan, rjp.jam_rawat) >= {date_str}"
        where_rid = f"WHERE TIMESTAMP(rid.tgl_perawatan, rid.jam_rawat) >= {date_str}"
        where_ridp = f"WHERE TIMESTAMP(ridp.tgl_perawatan, ridp.jam_rawat) >= {date_str}"
        where_rip = f"WHERE TIMESTAMP(rip.tgl_perawatan, rip.jam_rawat) >= {date_str}"
        where_pr = f"WHERE TIMESTAMP(pr.tgl_periksa, pr.jam) >= {date_str}"
        where_pl = f"WHERE TIMESTAMP(pl.tgl_periksa, pl.jam) >= {date_str}"
        where_dpl = f"WHERE TIMESTAMP(dpl.tgl_periksa, dpl.jam) >= {date_str}"
        where_op1 = f"WHERE op.biayaoperator1 > 0 AND op.tgl_operasi >= {date_str}"
        where_op2 = f"WHERE op.biayaoperator2 > 0 AND op.tgl_operasi >= {date_str}"
        where_op3 = f"WHERE op.biayaoperator3 > 0 AND op.tgl_operasi >= {date_str}"
        where_opa = f"WHERE op.biayadokter_anestesi > 0 AND op.tgl_operasi >= {date_str}"
        where_opa2 = f"WHERE op.biayadokter_anak > 0 AND op.tgl_operasi >= {date_str}"
        where_oppj = f"WHERE IFNULL(op.biaya_dokter_pjanak,0) > 0 AND op.tgl_operasi >= {date_str}"
        where_opu = f"WHERE IFNULL(op.biaya_dokter_umum,0) > 0 AND op.tgl_operasi >= {date_str}"
    else:
        date_str = dt_value.strftime("'%Y-%m-%d'")
        where_rjd = f"WHERE rjd.tgl_perawatan = {date_str}"
        where_rjdp = f"WHERE rjdp.tgl_perawatan = {date_str}"
        where_rjp = f"WHERE rjp.tgl_perawatan = {date_str}"
        where_rid = f"WHERE rid.tgl_perawatan = {date_str}"
        where_ridp = f"WHERE ridp.tgl_perawatan = {date_str}"
        where_rip = f"WHERE rip.tgl_perawatan = {date_str}"
        where_pr = f"WHERE pr.tgl_periksa = {date_str}"
        where_pl = f"WHERE pl.tgl_periksa = {date_str}"
        where_dpl = f"WHERE dpl.tgl_periksa = {date_str}"
        where_op1 = f"WHERE op.biayaoperator1 > 0 AND DATE(op.tgl_operasi) = {date_str}"
        where_op2 = f"WHERE op.biayaoperator2 > 0 AND DATE(op.tgl_operasi) = {date_str}"
        where_op3 = f"WHERE op.biayaoperator3 > 0 AND DATE(op.tgl_operasi) = {date_str}"
        where_opa = f"WHERE op.biayadokter_anestesi > 0 AND DATE(op.tgl_operasi) = {date_str}"
        where_opa2 = f"WHERE op.biayadokter_anak > 0 AND DATE(op.tgl_operasi) = {date_str}"
        where_oppj = f"WHERE IFNULL(op.biaya_dokter_pjanak,0) > 0 AND DATE(op.tgl_operasi) = {date_str}"
        where_opu = f"WHERE IFNULL(op.biaya_dokter_umum,0) > 0 AND DATE(op.tgl_operasi) = {date_str}"
    
    sql = f"""
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
            {where_rjd}
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
            {where_rjdp}
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
            {where_rjp}
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
            {where_rid}
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
            {where_ridp}
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
            {where_rip}
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
            {where_pr}
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
            {where_op1}

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
            {where_op2}

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
            {where_op3}

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
            {where_opa}

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
            {where_opa2}

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
            {where_oppj}

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
            {where_opu}

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
            {where_pl}
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
            {where_dpl}
            AND dpl.biaya_item > 0
            GROUP BY dpl.no_rawat, dpl.id_template, dpl.tgl_periksa

        ) t ON reg.no_rawat = t.no_rawat
        ORDER BY t.tgl_tindakan, t.kategori, reg.no_rawat;
    """
    df = pd.read_sql_query(sql, mysql_conn)
    return df


# ================================
# MAIN
# ================================
def main(medical_fee, update_columns):
    print(f"\n🔸 Processing Medical Fee {datetime.now()}")
    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    if len(medical_fee) == 0:
        print(f"⚪ No medical fee found. Skipping.")
        cur.close()
        prod_conn.close()
        return

    try:
        cur.execute("SET TIME ZONE 'Asia/Jakarta'")
    except:
        pass

    # =========================
    # MAPPING DOCTOR
    # =========================
    mapping_doctor = pd.read_sql_query(
        "SELECT contact_id as id, ref_code FROM master.mst_contact WHERE ref_code IS NOT NULL",
        prod_conn
    )
    mapping_doctor.columns = ["doctor_id", "docter_code"]
    
    medical_fee = medical_fee.merge(mapping_doctor, on="docter_code", how="left")
    medical_fee["doctor_id"].fillna(0, inplace=True)
    medical_fee["doctor_id"] = medical_fee["doctor_id"].astype(int)

    # =========================
    # MAPPING PATIENT
    # =========================
    mapping_patient = pd.read_sql_query(
        "SELECT contact_id, ref_code, contact_name FROM master.mst_contact WHERE ref_code IS NOT NULL",
        prod_conn
    )
    mapping_patient.columns = ["patient_id", "medical_record_no", "patient_name_mapped"]
    
    medical_fee = medical_fee.merge(mapping_patient, on="medical_record_no", how="left")
    medical_fee["patient_name"] = medical_fee["patient_name_mapped"].fillna(medical_fee["patient_name"])
    medical_fee.drop(columns=["patient_name_mapped"], inplace=True)
    
    medical_fee["patient_id"].fillna(0, inplace=True)
    medical_fee["patient_id"] = medical_fee["patient_id"].astype(int)

    medical_fee["doctor_code"] = medical_fee["docter_code"]
    medical_fee["no_sep"] = medical_fee["no_sep"].fillna("0")

    # =========================
    # MAPPING BPJS COST
    # =========================
    mapping_bpjs_cost = pd.read_sql_query(
        "SELECT procedure, cost FROM master.mst_bpjs_cost",
        prod_conn
    )
    if not mapping_bpjs_cost.empty:
        bpjs_cost_lookup = dict(zip(
            mapping_bpjs_cost["procedure"].str.strip().str.lower(),
            mapping_bpjs_cost["cost"]
        ))
        medical_fee["_procedure_key"] = medical_fee["procedure_name"].str.strip().str.lower()
        medical_fee["adjustment_amount"] = medical_fee["_procedure_key"].map(bpjs_cost_lookup)
        dates = []
        for val in medical_fee["adjustment_amount"]:
            if pd.notna(val):
                dates.append(datetime.now())
            else:
                dates.append(None)
        medical_fee["adjustment_date"] = dates
        medical_fee.drop(columns=["_procedure_key"], inplace=True)
    else:
        medical_fee["adjustment_amount"] = None
        medical_fee["adjustment_date"] = None

    df = medical_fee[[
        "visit_no", "patient_id", "patient_name", "medical_record_no",
        "doctor_id", "doctor_name", "period_month", "period_year", 
        "total_fee", "medical_procedure", "status", "branch_id",
        "procedure_date", "procedure_name", "payer_code", "payer_name",
        "doctor_code", "no_sep", "adjustment_amount", "adjustment_date"
    ]].copy()

    df = df.where(pd.notna(df), None)

    df = df.astype(object).where(pd.notna(df), None)

    # =========================
    # NORMALIZATION
    # =========================
    df["procedure_date"] = pd.to_datetime(df["procedure_date"], errors="coerce").dt.floor("min")
    df["total_fee"] = pd.to_numeric(df["total_fee"], errors="coerce").fillna(0).round(2)

    df = df.dropna(subset=["visit_no", "procedure_date"])
    df = df.drop_duplicates(subset=KEY_COLUMNS)

    # =========================
    # EXISTING
    # =========================
    keys = list(zip(
        df["visit_no"],
        df["procedure_date"],
        df["total_fee"],
        df["procedure_name"]
    ))

    existing_df = pd.read_sql_query(
        """
        SELECT visit_no, procedure_date, total_fee, procedure_name
        FROM trx_medical_fee
        WHERE (visit_no, procedure_date, total_fee, procedure_name) IN %s
        """,
        prod_conn,
        params=(tuple(keys),)
    )

    existing_set = set(zip(
        existing_df["visit_no"],
        pd.to_datetime(existing_df["procedure_date"]).dt.floor("min"),
        existing_df["total_fee"],
        existing_df["procedure_name"]
    ))

    df_insert = df[
        ~df.apply(lambda r: (r["visit_no"], r["procedure_date"], r["total_fee"], r["procedure_name"]) in existing_set, axis=1)
    ]

    df_update = df[
        df.apply(lambda r: (r["visit_no"], r["procedure_date"], r["total_fee"], r["procedure_name"]) in existing_set, axis=1)
    ]

    try:
        # =========================
        # INSERT
        # =========================
        if not df_insert.empty:
            insert_rows = [
                tuple(None if pd.isna(v) else v for v in row)
                for row in df_insert.itertuples(index=False)
            ]
            execute_values(
                cur,
                """
                INSERT INTO trx_medical_fee (
                    visit_no, patient_id, patient_name, medical_record_no,
                    doctor_id, doctor_name, period_month, period_year, 
                    total_fee, medical_procedure, status, branch_id,
                    procedure_date, procedure_name, payer_code, payer_name, 
                    doctor_code, no_sep, adjustment_amount, adjustment_date
                ) VALUES %s
                """,
                insert_rows
            )

        # =========================
        # UPDATE
        # =========================
        if not df_update.empty and update_columns:
            value_columns = KEY_COLUMNS + update_columns

            set_clause = ", ".join([f"{col} = v.{col}" for col in update_columns])

            update_sql = f"""
            UPDATE trx_medical_fee t
            SET {set_clause}
            FROM (VALUES %s) AS v({", ".join(value_columns)})
            WHERE t.visit_no = v.visit_no
              AND DATE_TRUNC('minute', t.procedure_date) = v.procedure_date
              AND t.total_fee = v.total_fee
              AND t.procedure_name = v.procedure_name
            """

            update_rows = [
                tuple(None if pd.isna(v) else v for v in row)
                for row in df_update[value_columns].itertuples(index=False)
            ]

            execute_values(
                cur,
                update_sql,
                update_rows
            )

        prod_conn.commit()
        print(f"✅ Done Medical Fee {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Medical Fee','error')
        print(f"❌ Error Medical Fee: {e}")
        traceback.print_exc()

    finally:
        cur.close()
        prod_conn.close()


def get_latest_trx_date():
    pg_conn = get_production_connection()
    cur = pg_conn.cursor()
    cur.execute("SELECT max(procedure_date) FROM public.trx_medical_fee")
    row = cur.fetchone()
    cur.close()
    pg_conn.close()
    if row and row[0] is not None:
        return row[0]
    return None


# ================================
# RUNNER
# ================================
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Medical Fee Runner")
    group = parser.add_mutually_exclusive_group(required=False)

    group.add_argument("--date")
    group.add_argument("--range", nargs=2)
    group.add_argument("--month", nargs=2)
    group.add_argument("--delta")

    parser.add_argument(
        "--update-columns",
        nargs="+",
        choices=["all"] + ALL_UPDATE_COLUMNS
    )

    args = parser.parse_args()

    if args.update_columns and "all" in args.update_columns:
        update_columns = ALL_UPDATE_COLUMNS
    else:
        update_columns = args.update_columns or []

    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Medical Fee','start')

    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        main(get_medical_fee(mysql_conn, d), update_columns)

    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            main(get_medical_fee(mysql_conn, dt), update_columns)

    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            main(get_medical_fee(mysql_conn, dt), update_columns)

    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        main(get_medical_fee(mysql_conn, delta_dt, True), update_columns)

    else:
        delta_dt = get_latest_trx_date()
        if delta_dt is None:
            print("⚠️ Belum ada baseline max(procedure_date). Gunakan --date, --range, --month, atau --delta.")
        else:
            main(get_medical_fee(mysql_conn, delta_dt, True), update_columns)

    create_log('Job Khanza Medical Fee','finish')
    mysql_conn.close()