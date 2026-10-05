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
import traceback
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
            kd_dokter as ref_code,
            cast(null as signed) as due_date
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
            no_rkm_medis as ref_code,
            cast(null as signed) as due_date
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
            penjab.kd_pj as ref_code,
            cast(14 as signed) as due_date
        from penjab
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
    
    # Ensure correct column order for INSERT
    columns_order = ['contact_name', 'phone', 'address', 'email', 'contact_category_id', 'branch_id', 'ref_code', 'due_date']
    df_contact = df_contact[columns_order].copy()
    
    # Convert integer columns - convert to numeric, then to object to avoid NAType - fully vectorized
    integer_columns = ['contact_category_id', 'branch_id', 'due_date']
    # Convert to numeric first (float64)
    numeric_values = df_contact[integer_columns].apply(pd.to_numeric, errors='coerce')
    # Create mask for non-None values
    mask = pd.notna(numeric_values)
    # Use a unique placeholder that won't conflict with real data (e.g., -999999)
    placeholder = -999999
    temp_filled = numeric_values.fillna(placeholder)
    int_values = temp_filled.astype(float).astype(int)
    # Convert to object, then replace placeholder values with None
    df_contact[integer_columns] = int_values.astype('object')
    df_contact[integer_columns] = df_contact[integer_columns].where(mask, None)
    
    # Convert string columns - keep None as None, convert others to string - fully vectorized
    string_columns = ['contact_name', 'phone', 'address', 'email', 'ref_code']
    # Convert to object to preserve None, then use mask to convert non-None to string
    df_contact[string_columns] = df_contact[string_columns].astype('object')
    mask = pd.notna(df_contact[string_columns])
    df_contact[string_columns] = df_contact[string_columns].where(
        ~mask, df_contact[string_columns].astype(str)
    )
    
    # Replace all remaining NaN/NaT with None for proper database insertion
    df_contact = df_contact.where(pd.notna(df_contact), None)
    df_contact = df_contact.replace({pd.NA: None, pd.NaT: None})
    
    df_cost_center = pd.DataFrame(cost_centers)

    try:
        # Check existing contacts
        cur.execute("SELECT ref_code, lower(contact_name), coalesce(phone, '') FROM master.mst_contact")
        existing_contacts = cur.fetchall()
        existing_refs = {row[0] for row in existing_contacts if row[0]}
        existing_name_phones = {(row[1], row[2]) for row in existing_contacts if row[1]}

        insert_contacts = []
        update_contacts_by_ref = []
        update_contacts_by_name = []

        for idx, row in df_contact.iterrows():
            ref = row['ref_code']
            name = str(row['contact_name']).lower() if pd.notna(row['contact_name']) else ''
            phone = str(row['phone']) if pd.notna(row['phone']) else ''
            
            row_tuple = (row['contact_name'], row['phone'], row['address'], row['email'], row['contact_category_id'], row['branch_id'], row['ref_code'], row['due_date'])

            if ref and ref in existing_refs:
                update_contacts_by_ref.append(row_tuple)
            elif name and (name, phone) in existing_name_phones:
                update_contacts_by_name.append(row_tuple)
            else:
                insert_contacts.append(row_tuple)

        # Check existing cost centers
        cur.execute("SELECT cost_center_code FROM master.mst_cost_center")
        existing_cc = {row[0] for row in cur.fetchall() if row[0]}
        
        insert_cc = []
        update_cc = []
        for idx, row in df_cost_center.iterrows():
            row_tuple = (row['cost_center_code'], row['cost_center_name'], row['department_id'], row['branch_id'])
            if row['cost_center_code'] in existing_cc:
                update_cc.append(row_tuple)
            else:
                insert_cc.append(row_tuple)

        # INSERT Contacts
        if insert_contacts:
            execute_values(cur,
                """
                INSERT INTO master.mst_contact (
                    contact_name, phone, address, email, contact_category_id, branch_id, ref_code, due_date
                ) VALUES %s
                """,
                insert_contacts
            )
        
        # UPDATE Contacts by ref_code
        if update_contacts_by_ref:
            execute_values(cur,
                """
                UPDATE master.mst_contact t
                SET contact_name = v.contact_name, phone = v.phone, address = v.address, email = v.email, contact_category_id = v.contact_category_id, branch_id = v.branch_id, due_date = v.due_date
                FROM (VALUES %s) AS v(contact_name, phone, address, email, contact_category_id, branch_id, ref_code, due_date)
                WHERE t.ref_code = v.ref_code
                """,
                update_contacts_by_ref
            )

        # UPDATE Contacts by name & phone
        if update_contacts_by_name:
            execute_values(cur,
                """
                UPDATE master.mst_contact t
                SET address = v.address, email = v.email, contact_category_id = v.contact_category_id, branch_id = v.branch_id, ref_code = v.ref_code, due_date = v.due_date
                FROM (VALUES %s) AS v(contact_name, phone, address, email, contact_category_id, branch_id, ref_code, due_date)
                WHERE lower(t.contact_name) = lower(v.contact_name) AND coalesce(t.phone, '') = coalesce(v.phone, '')
                """,
                update_contacts_by_name
            )

        # INSERT Cost Centers
        if insert_cc:
            execute_values(cur,
                """
                INSERT INTO master.mst_cost_center (
                    cost_center_code, cost_center_name, department_id, branch_id
                ) VALUES %s
                """,
                insert_cc
            )

        # UPDATE Cost Centers
        if update_cc:
            execute_values(cur,
                """
                UPDATE master.mst_cost_center t
                SET cost_center_name = v.cost_center_name, department_id = v.department_id, branch_id = v.branch_id
                FROM (VALUES %s) AS v(cost_center_code, cost_center_name, department_id, branch_id)
                WHERE t.cost_center_code = v.cost_center_code
                """,
                update_cc
            )

        prod_conn.commit()
        print(f"✅ Done Contact & Cost Center {datetime.now()} | Contacts: {len(insert_contacts)} ins, {len(update_contacts_by_ref)} upd_ref, {len(update_contacts_by_name)} upd_name | CC: {len(insert_cc)} ins, {len(update_cc)} upd")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Contact & Cost Center','error')
        print(f"❌ Error Contact & Cost Center: {e}")
        traceback.print_exc()

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
    khanza_conn.close()

    