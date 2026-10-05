"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Khanza Contact & Cost Center Migration
  Created   : 2025
========================================================
"""
import sys
import os
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, project_root)

from libs import get_khanza_connection, get_production_connection, create_log
import calendar
from datetime import datetime, timedelta
import uuid
import pandas as pd
import warnings
import argparse
from psycopg2.extras import execute_values
warnings.filterwarnings("ignore")

def get_contact(conn):
    sql = """
        -- dokter
        select
            nm_dokter as contact_name,
            no_telp as phone,
            almt_tgl as address,
            null as email,
            3 as contact_category_id,
            1 as branch_id,
            kd_dokter as ref_code
        from dokter

        union all

        -- pasien
        select
            nm_pasien as contact_name,
            no_tlp as phone,
            alamat as address,
            email as email,
            2 as contact_category_id,
            1 as branch_id,
            no_rkm_medis as ref_code
        from pasien

        union all

        -- perusahaan pj
        select
            penjab.nama_perusahaan as contact_name,
            penjab.no_telp as phone,
            penjab.alamat_asuransi as address,
            null as email,
            5 as contact_category_id,
            1 as branch_id,
            penjab.kd_pj as ref_code
        from penjab

        union all

        -- vendor
        select
            nama_suplier as contact_name,
            no_telp as phone,
            alamat as address,
            null as email,
            1 as contact_category_id,
            1 as branch_id,
            kode_suplier as ref_code
        from datasuplier
    """

    cur = conn.cursor(dictionary=True)
    cur.execute(sql)
    rows = cur.fetchall()
    cur.close()
    return rows

def get_cost_center(conn):
    sql = """ 
        select
            kd_bangsal as cost_center_code,
            nm_bangsal as cost_center_name,
            1 as department_id,
            1 as branch_id
        from bangsal

        union all

        select
            kd_poli as cost_center_code,
            nm_poli as cost_center_name,
            1 as department_id,
            1 as branch_id
        from poliklinik
    """
    cur = conn.cursor(dictionary=True)
    cur.execute(sql)
    rows = cur.fetchall()
    cur.close()
    return rows

def main(contacts, cost_centers):
    print(f"\n🔸 Processing Contact & Cost Center {datetime.now()}")
    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    df_contact = pd.DataFrame(contacts)
    df_cost_center = pd.DataFrame(cost_centers)

    try:
        cur.execute("truncate table master.mst_contact cascade")
        cur.execute("truncate table master.mst_cost_center cascade")

        execute_values(cur,
            """
            INSERT INTO master.mst_contact (
                contact_name, phone, address, email, contact_category_id, branch_id, ref_code
            ) VALUES %s
            """,
            df_contact.values.tolist()
        )
        execute_values(cur,
            """
            INSERT INTO master.mst_cost_center (
                cost_center_code, cost_center_name, department_id, branch_id
            ) VALUES %s
            """,
            df_cost_center.values.tolist()
        )
        prod_conn.commit()
        print(f"✅ Done Contact & Cost Center {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Contact & Cost Center','error')
        print(f"❌ Error Contact & Cost Center: {e}")

    finally:
        cur.close()
        prod_conn.close()

if __name__ == "__main__":
    khanza_conn = get_khanza_connection()

    create_log('Job Khanza Contact & Cost Center','start')

    contact = get_contact(khanza_conn)
    cost_center = get_cost_center(khanza_conn)
    main(contact, cost_center)
    
    create_log('Job Khanza Contact & Cost Center','finish')
    mysql_conn.close()

    