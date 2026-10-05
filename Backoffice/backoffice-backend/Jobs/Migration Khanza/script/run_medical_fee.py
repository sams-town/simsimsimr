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

from libs import get_khanza_connection, get_production_connection, create_log, get_latest_trx_date,date_range,date_month
import calendar
from datetime import datetime, timedelta
import uuid
import pandas as pd
import warnings
import argparse
import traceback
from psycopg2.extras import execute_values
warnings.filterwarnings("ignore")



def get_medical_fee(mysql_conn, dt_value, use_delta=False):
    if use_delta:
        # For delta mode, use datetime format with time
        if isinstance(dt_value, datetime):
            date_str = dt_value.strftime("'%Y-%m-%d %H:%M:%S'")
        else:
            date_str = f"'{dt_value}'"
        date_filter_rj = f"AND rp.tgl_registrasi >= {date_str}"
        date_filter_ri = f"AND rid.tgl_perawatan >= {date_str}"
        date_filter_rad = f"AND pr.tgl_periksa >= {date_str}"
        date_filter_lab = f"AND pl.tgl_periksa >= {date_str}"
    else:
        # For exact date match, use date format only
        date_str = dt_value.strftime("'%Y-%m-%d'")
        date_filter_rj = f"AND rp.tgl_registrasi = {date_str}"
        date_filter_ri = f"AND rid.tgl_perawatan = {date_str}"
        date_filter_rad = f"AND pr.tgl_periksa = {date_str}"
        date_filter_lab = f"AND pl.tgl_periksa = {date_str}"
    
    sql = f"""
        SELECT
            rp.no_rawat AS visit_no,
            d.kd_dokter AS docter_code,
            d.nm_dokter AS doctor_name,
            MONTH(rp.tgl_registrasi) AS period_month,
            YEAR(rp.tgl_registrasi) AS period_year,
            rjd.biaya_rawat AS total_fee,
            'Rawat Jalan' AS medical_procedure,
            'Pending' AS status,
            1 AS branch_id,
            p.no_rkm_medis as medical_record_no,
            p.nm_pasien as patient_name,
            TIMESTAMP(rjd.tgl_perawatan, rjd.jam_rawat) as procedure_date,
            jp.nm_perawatan AS procedure_name,
            pj.kd_pj as payer_code,
            pj.nama_perusahaan as payer_name
        FROM rawat_jl_dr rjd
        JOIN reg_periksa rp ON rjd.no_rawat = rp.no_rawat
        JOIN dokter d ON rjd.kd_dokter = d.kd_dokter
        JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
        JOIN jns_perawatan jp ON rjd.kd_jenis_prw = jp.kd_jenis_prw
        JOIN penjab pj ON rp.kd_pj = pj.kd_pj
        WHERE rjd.tarif_tindakandr > 0
        {date_filter_rj}

        UNION ALL

        SELECT
            rp.no_rawat AS visit_no,
            d.kd_dokter AS docter_code,
            d.nm_dokter AS doctor_name,
            MONTH(rid.tgl_perawatan) AS period_month,
            YEAR(rid.tgl_perawatan) AS period_year,
            rid.biaya_rawat AS total_fee,
            'Rawat Inap' AS medical_procedure,
            'Pending' AS status,
            1 AS branch_id,
            p.no_rkm_medis as medical_record_no,
            p.nm_pasien as patient_name,
            TIMESTAMP(rid.tgl_perawatan, rid.jam_rawat) as procedure_date,
            jp.nm_perawatan AS procedure_name,
            pj.kd_pj as payer_code,
            pj.nama_perusahaan as payer_name
        FROM rawat_inap_dr rid
        JOIN reg_periksa rp ON rid.no_rawat = rp.no_rawat
        JOIN dokter d ON rid.kd_dokter = d.kd_dokter
        JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
        JOIN jns_perawatan jp ON rid.kd_jenis_prw = jp.kd_jenis_prw
        JOIN penjab pj ON rp.kd_pj = pj.kd_pj
        WHERE rid.tarif_tindakandr > 0
        {date_filter_ri}

        UNION ALL

        SELECT
            rp.no_rawat AS visit_no,
            d.kd_dokter AS docter_code,
            d.nm_dokter AS doctor_name,
            MONTH(pr.tgl_periksa) AS period_month,
            YEAR(pr.tgl_periksa) AS period_year,
            pr.biaya AS total_fee,
            'Radiologi' AS medical_procedure,
            'Pending' AS status,
            1 AS branch_id,
            p.no_rkm_medis as medical_record_no,
            p.nm_pasien as patient_name,
            TIMESTAMP(pr.tgl_periksa, pr.jam) as procedure_date,
            jp.nm_perawatan AS procedure_name,
            pj.kd_pj as payer_code,
            pj.nama_perusahaan as payer_name
        FROM periksa_radiologi pr
        JOIN reg_periksa rp ON pr.no_rawat = rp.no_rawat
        JOIN dokter d ON pr.kd_dokter = d.kd_dokter
        JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
        JOIN jns_perawatan jp ON pr.kd_jenis_prw = jp.kd_jenis_prw
        JOIN penjab pj ON rp.kd_pj = pj.kd_pj
        WHERE pr.tarif_tindakan_dokter > 0
        {date_filter_rad}

        UNION ALL

        SELECT
            rp.no_rawat AS visit_no,
            d.kd_dokter AS docter_code,
            d.nm_dokter AS doctor_name,
            MONTH(pl.tgl_periksa) AS period_month,
            YEAR(pl.tgl_periksa) AS period_year,
            pl.biaya AS total_fee,
            'Laboratorium' AS medical_procedure,
            'Pending' AS status,
            1 AS branch_id,
            p.no_rkm_medis as medical_record_no,
            p.nm_pasien as patient_name,
            TIMESTAMP(pl.tgl_periksa, pl.jam) as procedure_date,
            jp.nm_perawatan AS procedure_name,
            pj.kd_pj as payer_code,
            pj.nama_perusahaan as payer_name
        FROM periksa_lab pl
        JOIN reg_periksa rp ON pl.no_rawat = rp.no_rawat
        JOIN dokter d ON pl.kd_dokter = d.kd_dokter
        JOIN pasien p ON rp.no_rkm_medis = p.no_rkm_medis
        JOIN jns_perawatan jp ON pl.kd_jenis_prw = jp.kd_jenis_prw
        JOIN penjab pj ON rp.kd_pj = pj.kd_pj
        WHERE pl.tarif_tindakan_dokter > 0
        {date_filter_lab};

    """
    df = pd.read_sql_query(sql, mysql_conn)
    return df

def main(medical_fee):
    print(f"\n🔸 Processing Medical Fee {datetime.now()}")
    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    if len(medical_fee) == 0:
        print(f"⚪ No medical fee found. Skipping.")
        return

    # Get doctor_id mapping from master.mst_contact
    mapping_doctor = pd.read_sql_query(
        "SELECT contact_id as id, ref_code FROM master.mst_contact WHERE ref_code IS NOT NULL",
        prod_conn
    )
    mapping_doctor.columns = ["doctor_id", "docter_code"]
    
    # Merge to get doctor_id from docter_code
    medical_fee = medical_fee.merge(mapping_doctor, on="docter_code", how="left")
    
    # Fill null doctor_id with 0 or handle as needed
    medical_fee["doctor_id"].fillna(0, inplace=True)
    medical_fee["doctor_id"] = medical_fee["doctor_id"].astype(int)

    # Get patient_id mapping from master.mst_contact where ref_code = medical_record_no
    mapping_patient = pd.read_sql_query(
        "SELECT contact_id, ref_code, contact_name FROM master.mst_contact WHERE ref_code IS NOT NULL",
        prod_conn
    )
    mapping_patient.columns = ["patient_id", "medical_record_no", "patient_name_mapped"]
    
    # Merge to get patient_id and patient_name from medical_record_no
    medical_fee = medical_fee.merge(mapping_patient, on="medical_record_no", how="left")
    
    # Use mapped patient_name if available, otherwise use original patient_name
    medical_fee["patient_name"] = medical_fee["patient_name_mapped"].fillna(medical_fee["patient_name"])
    medical_fee.drop(columns=["patient_name_mapped"], inplace=True)
    
    # Fill null patient_id with 0 or handle as needed
    medical_fee["patient_id"].fillna(0, inplace=True)
    medical_fee["patient_id"] = medical_fee["patient_id"].astype(int)

    # Select only the columns that match the INSERT statement
    df_medical_fee = medical_fee[["visit_no", "patient_id", "patient_name", "medical_record_no",
                                   "doctor_id", "doctor_name", "period_month", "period_year", 
                                   "total_fee", "medical_procedure", "status", "branch_id"]]

    try:
        execute_values(cur,
            """
            INSERT INTO trx_medical_fee (
                visit_no, patient_id, patient_name, medical_record_no,
                doctor_id, doctor_name, period_month, period_year, 
                total_fee, medical_procedure, status, branch_id,
                procedure_date, procedure_name, payer_code, payer_name
            ) VALUES %s
            """,
            df_medical_fee.values.tolist()
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

if __name__ == "__main__":
    khanza_conn = get_khanza_connection()
    parser = argparse.ArgumentParser(description="Medical Fee Runner (MySQL -> Postgres)")
    group = parser.add_mutually_exclusive_group(required=False)
    group.add_argument("--date", help="Single date YYYY-MM-DD")
    group.add_argument("--range", nargs=2, help="Date range YYYY-MM-DD YYYY-MM-DD")
    group.add_argument("--month", nargs=2, help="Month mode: YYYY MM")
    group.add_argument("--delta", help="Datetime start YYYY-MM-DD HH:MM:SS")
    args = parser.parse_args()
    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Medical Fee','start')

    # --- SINGLE DATE ---
    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        medical_fee = get_medical_fee(mysql_conn, d, use_delta=False)
        main(medical_fee)
    
    # --- RANGE DATE (loop harian) ---
    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            medical_fee = get_medical_fee(mysql_conn, dt, use_delta=False)
            main(medical_fee)

    # --- MONTH MODE ---
    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            medical_fee = get_medical_fee(mysql_conn, dt, use_delta=False)
            main(medical_fee)

    # --- DELTA MODE (NO LOOP) ---
    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        medical_fee = get_medical_fee(mysql_conn, delta_dt, use_delta=True)
        main(medical_fee)
    
    else:
        delta_dt = get_latest_trx_date()
        medical_fee = get_medical_fee(mysql_conn, delta_dt, use_delta=True)
        main(medical_fee)
        
    create_log('Job Khanza Medical Fee','finish')
    mysql_conn.close()