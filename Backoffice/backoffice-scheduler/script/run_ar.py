"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Khanza Account Receivable Migration
  Created   : 2025
========================================================
"""

import sys
import os
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, project_root)

from libs import get_khanza_connection, get_production_connection, create_log, date_range, date_month, get_dev_connection

from datetime import datetime
import pandas as pd
import warnings
import argparse
import traceback
from psycopg2.extras import execute_values

warnings.filterwarnings("ignore")

def get_contact_source_by_payer(mysql_conn, payer_ids):

    if len(payer_ids) == 0:
        return pd.DataFrame()

    placeholders = ", ".join(["%s"] * len(payer_ids))


    sql = f"""
        select
            penjab.png_jawab as contact_name,
            penjab.no_telp as phone,
            penjab.alamat_asuransi as address,
            null as email,
            5 as contact_category_id,
            1 as branch_id,
            penjab.kd_pj as ref_code,
            cast(14 as signed) as due_date
        from penjab
        where penjab.kd_pj in ({placeholders})
    """

    return pd.read_sql_query(sql, mysql_conn, params=payer_ids)


def get_existing_contact(prod_conn, ref_codes):

    if len(ref_codes) == 0:
        return pd.DataFrame()

    sql = """
        SELECT ref_code
        FROM master.mst_contact
        WHERE ref_code IN %s
          AND contact_category_id = 5
    """

    return pd.read_sql_query(sql, prod_conn, params=(tuple(ref_codes),))


def sync_contact_from_ar(mysql_conn, prod_conn, ar_data):

    print(f"\n🔸 Sync Contact (from AR) {datetime.now()}")

    cur = prod_conn.cursor()
    payer_ids = (
        ar_data["payer_id"]
        .dropna()
        .astype(str)
        .unique()
        .tolist()
    )

    if len(payer_ids) == 0:
        print("⚠️ No payer_id found")
        return
    df_contact = get_contact_source_by_payer(mysql_conn, payer_ids)

    if df_contact.empty:
        print("⚠️ No contact data from penjab")
        return

    df_contact = df_contact.drop_duplicates(subset=["ref_code"])
    df_contact = df_contact.where(pd.notna(df_contact), None)

    existing = get_existing_contact(
        prod_conn,
        df_contact["ref_code"].astype(str).tolist()
    )

    existing_set = set(existing["ref_code"].astype(str)) if not existing.empty else set()

    df_contact["is_existing"] = df_contact["ref_code"].astype(str).isin(existing_set)

    df_insert = df_contact[~df_contact["is_existing"]].drop(columns=["is_existing"])
    df_update = df_contact[df_contact["is_existing"]].drop(columns=["is_existing"])

    try:
        if not df_insert.empty:
            execute_values(
                cur,
                """
                INSERT INTO master.mst_contact (
                    contact_name,
                    phone,
                    address,
                    email,
                    contact_category_id,
                    branch_id,
                    ref_code,
                    due_date
                ) VALUES %s
                """,
                df_insert.values.tolist()
            )

        if not df_update.empty:

            update_columns = [
                "contact_name",
                "phone",
                "address",
                "email",
                "due_date"
            ]

            value_columns = ["ref_code"] + update_columns

            set_clause = ",\n                ".join(
                [f"{col} = v.{col}" for col in update_columns]
            )

            value_alias = ",\n                ".join(value_columns)

            update_sql = f"""
            UPDATE master.mst_contact t
            SET
                {set_clause}
            FROM (VALUES %s) AS v(
                {value_alias}
            )
            WHERE t.ref_code = v.ref_code
              AND t.contact_category_id = 5
            """

            execute_values(
                cur,
                update_sql,
                df_update[value_columns].values.tolist()
            )

        prod_conn.commit()

        print(f"✅ Contact Insert : {len(df_insert)}")
        print(f"✅ Contact Update : {len(df_update)}")

    except Exception as e:
        prod_conn.rollback()
        print(f"❌ Error Sync Contact: {e}")
        traceback.print_exc()

    finally:
        cur.close()

def get_contact_due_date(prod_conn):

    sql = """
        SELECT
            contact_id,
            ref_code,
            due_date
        FROM master.mst_contact
        WHERE contact_category_id = 5
    """

    return pd.read_sql_query(sql, prod_conn)

def get_ar(mysql_conn, dt_value, delta=False):

    date_str = dt_value.strftime("'%Y-%m-%d'")
    tanggal_cond = f"(pp.tanggal >= {date_str} or pp.tanggal = CURDATE())"

    sql = f"""
        select
            reg_periksa.no_rawat as ar_number,
            penjab.kd_pj as payer_id,
            penjab.png_jawab as payer_name,
            if(penjab.kd_pj = 'BPJ','bpjs','non bpjs') as payer_type,
            reg_periksa.status_lanjut as service_type,
            timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg) as pipeline_date,
            pp.amount as total_amount,
            'Pending' as payment_status,
            1 as branch_id,
            pasien.no_rkm_medis as medical_record_no,
            pasien.nm_pasien as patient_name,
            poli.kd_poli as clinic_code,
            poli.nm_poli as clinic_name,
            no_sep,
            pp.status as status_penagihan,
            pp.tanggal as transaction_date
        from (
            select
                dpp.no_rawat,
                pp.status,
                pp.tgl_piutang as tanggal,
                pp.sisapiutang as amount,
                dpp.kd_pj,
                row_number() over (
                    partition by dpp.no_rawat
                    order by pp.tgl_piutang desc, pp.no_rawat desc
                ) as rn
            from detail_piutang_pasien dpp
            join piutang_pasien pp 
                on pp.no_rawat = dpp.no_rawat
        ) pp
        join reg_periksa on reg_periksa.no_rawat = pp.no_rawat
        left join penjab on pp.kd_pj = penjab.kd_pj
        left join pasien on pasien.no_rkm_medis = reg_periksa.no_rkm_medis
        left join poliklinik poli on poli.kd_poli = reg_periksa.kd_poli
        left join bridging_sep on reg_periksa.no_rawat = bridging_sep.no_rujukan
        where penjab.png_jawab not in ('***',' ','-','')
            and {tanggal_cond}
        order by ar_number desc;
    """

    return pd.read_sql_query(sql, mysql_conn)

def get_existing_ar(prod_conn, ar_pairs):

    if len(ar_pairs) == 0:
        return pd.DataFrame()

    sql = """
        SELECT ar_number, payer_id, payment_status
        FROM trx_account_receivable
        WHERE (ar_number, payer_id) IN %s
    """

    return pd.read_sql_query(sql, prod_conn, params=(tuple(ar_pairs),))

def main(ar_data, update_columns=None):

    print(f"\n🔸 Processing Account Receivable {datetime.now()}")

    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    if ar_data.empty:
        print("⚠️ No data to process")
        cur.close()
        prod_conn.close()
        return

    sync_contact_from_ar(mysql_conn, prod_conn, ar_data)

    df_contact = get_contact_due_date(prod_conn)

    ar_data = ar_data.merge(
        df_contact[['ref_code', 'due_date']],
        left_on='payer_id',
        right_on='ref_code',
        how='left'
    )

    # transaction_dates = pd.to_datetime(ar_data["transaction_date"], errors='coerce').dt.date

    df_ar = pd.DataFrame({
        "ar_number": ar_data["ar_number"],
        "payer_type": ar_data["payer_type"],
        "payer_id": ar_data["payer_id"],
        "payer_name": ar_data["payer_name"],
        "service_type": ar_data["service_type"],
        "transaction_date": ar_data["transaction_date"],
        "total_amount": ar_data["total_amount"].fillna(0),
        "payment_status": ar_data["payment_status"],
        "branch_id": ar_data["branch_id"],
        "created_by": 1,
        "updated_by": 1,
        "medical_record_no": ar_data["medical_record_no"],
        "patient_name": ar_data["patient_name"],
        "clinic_code": ar_data["clinic_code"],
        "clinic_name": ar_data["clinic_name"],
        "no_sep": ar_data["no_sep"],
        "pipeline_date": ar_data["pipeline_date"],
        "billing_status": ar_data["status_penagihan"]

    })

    df_ar = df_ar.where(pd.notna(df_ar), None)

    ar_pairs = list(
        set(
            zip(
                df_ar["ar_number"].astype(str),
                df_ar["payer_id"].astype(str)
            )
        )
    )
    existing = get_existing_ar(prod_conn, ar_pairs)
    
    if not existing.empty:
        non_pending = existing[existing["payment_status"].str.lower() != 'pending']
        non_pending_set = set(
            zip(
                non_pending["ar_number"].astype(str),
                non_pending["payer_id"].astype(str)
            )
        )
        
        row_keys_full = list(zip(df_ar["ar_number"].astype(str), df_ar["payer_id"].astype(str)))
        is_non_pending = pd.Series(row_keys_full, index=df_ar.index).isin(non_pending_set)
        
        df_ar = df_ar[~is_non_pending]
        
        pending = existing[existing["payment_status"].str.lower() == 'pending']
        existing_set = set(
            zip(
                pending["ar_number"].astype(str),
                pending["payer_id"].astype(str)
            )
        )
    else:
        existing_set = set()

    row_keys = list(zip(df_ar["ar_number"].astype(str), df_ar["payer_id"].astype(str)))
    is_existing = pd.Series(row_keys, index=df_ar.index).isin(existing_set)
    df_insert = df_ar[~is_existing]
    df_update = df_ar[is_existing]

    try:
        if not df_insert.empty:

            execute_values(
                cur,
                """
                INSERT INTO trx_account_receivable (
                    ar_number,
                    payer_type,
                    payer_id,
                    payer_name,
                    service_type,
                    transaction_date,
                    total_amount,
                    payment_status,
                    branch_id,
                    created_by,
                    updated_by,
                    medical_record_no,
                    patient_name,
                    clinic_code,
                    clinic_name,
                    no_sep,
                    pipeline_date,
                    billing_status
                ) VALUES %s
                """,
                df_insert.values.tolist()
            )

            prod_conn.commit()

        if not df_update.empty and update_columns:

            value_columns = ["ar_number", "payer_id"] + update_columns
            set_clause = ",\n                ".join(
                [f"{col} = v.{col}" for col in update_columns]
            )
            value_alias_columns = ",\n                ".join(value_columns)

            update_sql = f"""
            UPDATE trx_account_receivable t
            SET
                {set_clause}
            FROM (VALUES %s) AS v(
                {value_alias_columns}
            )
            WHERE t.ar_number = v.ar_number
              AND t.payer_id = v.payer_id
            """

            update_rows = df_update[value_columns].values.tolist()

            execute_values(
                cur,
                update_sql,
                update_rows
            )

            prod_conn.commit()

        print(f"✅ Insert : {len(df_insert)}")
        if update_columns:
            print(f"✅ Update : {len(df_update)}")
        else:
            print("⏭️ Update : skipped (tidak ada --update-columns)")
        print(f"✅ Done Account Receivable {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Account Receivable','error')
        print(f"❌ Error Account Receivable: {e}")
        traceback.print_exc()

    finally:
        cur.close()
        prod_conn.close()


# ================================
# GET LAST PIPELINE DATE
# ================================
def get_latest_trx_date():
    pg_conn = get_production_connection()
    cur = pg_conn.cursor()

    cur.execute("SELECT max(transaction_date) FROM trx_account_receivable")

    row = cur.fetchone()
    cur.close()

    if row:
        return row[0]

    return None


# ================================
# RUNNER
# ================================
if __name__ == "__main__":

    parser = argparse.ArgumentParser(description="Account Receivable Runner")

    group = parser.add_mutually_exclusive_group(required=False)

    group.add_argument("--date")
    group.add_argument("--range", nargs=2)
    group.add_argument("--month", nargs=2)
    group.add_argument("--delta")
    ALL_COLUMNS = [
        "payer_type",
        "payer_name",
        "service_type",
        "transaction_date",
        "total_amount",
        "payment_status",
        "branch_id",
        "created_by",
        "updated_by",
        "medical_record_no",
        "patient_name",
        "clinic_code",
        "clinic_name",
        "no_sep",
        "pipeline_date",
        "billing_status"
    ]

    parser.add_argument(
        "--update-columns",
        nargs="+",
        choices=["all"] + ALL_COLUMNS,
        help="List kolom yang akan di-update. Gunakan 'all' untuk semua kolom."
    )

    args = parser.parse_args()
    if args.update_columns and "all" in args.update_columns:
        update_columns = ALL_COLUMNS
    else:
        update_columns = args.update_columns or []
    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Account Receivable','start')

    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        ar_data = get_ar(mysql_conn, d, delta=False)
        main(ar_data, update_columns=update_columns)

    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            ar_data = get_ar(mysql_conn, dt, delta=False)
            main(ar_data, update_columns=update_columns)

    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            ar_data = get_ar(mysql_conn, dt, delta=False)
            main(ar_data, update_columns=update_columns)

    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d")
        ar_data = get_ar(mysql_conn, delta_dt, delta=True)
        main(ar_data, update_columns=update_columns)

    else:
        delta_dt = get_latest_trx_date()
        ar_data = get_ar(mysql_conn, delta_dt, delta=True)
        main(ar_data, update_columns=update_columns)

    create_log('Job Khanza Account Receivable','finish')
    mysql_conn.close()
