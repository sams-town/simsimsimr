"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Dev to Prod Sync
========================================================
"""

import sys
import os

project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, project_root)

from libs import get_production_connection, get_dev_connection
from psycopg2.extras import execute_values

import pandas as pd
from datetime import datetime
import traceback
import warnings

warnings.filterwarnings("ignore")

def clean_dataframe(df):
    df = df.copy()

    for col in df.columns:

        # convert datetime NaT → None
        if str(df[col].dtype).startswith("datetime"):
            df[col] = df[col].astype(object)

        df[col] = df[col].where(pd.notnull(df[col]), None)

    return df


# =========================================================
# GET EXISTING DATA
# =========================================================

def get_existing(conn, table, key_col, values):

    if len(values) == 0:
        return pd.DataFrame()

    sql = f"""
        SELECT {key_col}
        FROM {table}
        WHERE {key_col} = ANY(%s)
    """

    return pd.read_sql_query(sql, conn, params=(list(values),))


# =========================================================
# MST CONTACT
# =========================================================

def sync_mst_contact(dev_conn, prod_conn):

    print("\n🔹 Sync mst_contact")

    cols = [
        "ref_code","contact_name","phone","address",
        "email","contact_category_id","branch_id",
        "thb_ref_code","thb_contact_source"
    ]

    df = pd.read_sql_query(
        f"SELECT {','.join(cols)} FROM master.mst_contact",
        dev_conn
    )

    existing = get_existing(prod_conn,"master.mst_contact","ref_code",df["ref_code"].unique())
    existing_set = set(existing["ref_code"]) if not existing.empty else set()

    df_insert = df[~df["ref_code"].isin(existing_set)]
    df_update = df[df["ref_code"].isin(existing_set)]

    cur = prod_conn.cursor()

    try:

        if not df_insert.empty:

            execute_values(
                cur,
                f"""
                INSERT INTO master.mst_contact ({",".join(cols)})
                VALUES %s
                """,
                df_insert[cols].values.tolist()
            )

        if not df_update.empty:

            execute_values(
                cur,
                f"""
                UPDATE master.mst_contact t
                SET
                    contact_name=v.contact_name,
                    phone=v.phone,
                    address=v.address,
                    email=v.email,
                    contact_category_id=v.contact_category_id,
                    branch_id=v.branch_id,
                    thb_ref_code=v.thb_ref_code,
                    thb_contact_source=v.thb_contact_source
                FROM (VALUES %s) AS v({",".join(cols)})
                WHERE t.ref_code=v.ref_code
                """,
                df_update[cols].values.tolist()
            )

        prod_conn.commit()

        print("Insert:",len(df_insert))
        print("Update:",len(df_update))

    except Exception as e:

        prod_conn.rollback()
        print("❌ mst_contact error",e)
        traceback.print_exc()

    finally:
        cur.close()


# =========================================================
# TRX INVOICE EXCHANGE
# =========================================================

def sync_invoice_exchange(dev_conn, prod_conn):

    print("\n🔹 Sync trx_invoice_exchange")

    cols = [
        "po_number","receipt_date","invoice_number","po_date",
        "due_date","supplier_code","supplier_name",
        "total_transaction","status","updated_by",
        "inserted_date","updated_date","journal_date"
    ]

    df = pd.read_sql_query(
        f"SELECT {','.join(cols)} FROM trx_invoice_exchange",
        dev_conn
    )

    existing = get_existing(prod_conn,"trx_invoice_exchange","invoice_number",df["invoice_number"].unique())
    existing_set = set(existing["invoice_number"]) if not existing.empty else set()

    df_insert = df[~df["invoice_number"].isin(existing_set)]
    df_update = df[df["invoice_number"].isin(existing_set)]

    cur = prod_conn.cursor()

    try:

        if not df_insert.empty:

            execute_values(
                cur,
                f"""
                INSERT INTO trx_invoice_exchange ({",".join(cols)})
                VALUES %s
                """,
                df_insert[cols].values.tolist()
            )

        if not df_update.empty:

            execute_values(
                cur,
                f"""
                UPDATE trx_invoice_exchange t
                SET
                    po_number=v.po_number,
                    receipt_date=v.receipt_date,
                    po_date=v.po_date,
                    due_date=v.due_date,
                    supplier_code=v.supplier_code,
                    supplier_name=v.supplier_name,
                    total_transaction=v.total_transaction,
                    updated_by=v.updated_by,
                    inserted_date=v.inserted_date,
                    updated_date=v.updated_date,
                    journal_date=v.journal_date
                FROM (VALUES %s) AS v({",".join(cols)})
                WHERE t.invoice_number=v.invoice_number
                """,
                df_update[cols].values.tolist()
            )

        prod_conn.commit()

        print("Insert:",len(df_insert))
        print("Update:",len(df_update))

    except Exception as e:

        prod_conn.rollback()
        print("❌ trx_invoice_exchange error",e)

    finally:
        cur.close()


# =========================================================
# TRANSACTION
# =========================================================

def sync_transaction(dev_conn, prod_conn, table):

    print(f"\n🔹 Sync {table}")

    cols = [
        "transaction_id","transaction_date","transaction_type",
        "transaction_no","transaction_ref","transaction_name",
        "description","coa_code","coa_name",
        "coa_parent_code","coa_parent_name",
        "status","branch_id","created_by",
        "amount","created_date","posted_date","journal_no"
    ]

    df = pd.read_sql_query(
        f"SELECT {','.join(cols)} FROM {table}",
        dev_conn
    )

    # gunakan transaction_id karena ini PK
    existing = get_existing(prod_conn,table,"transaction_id",df["transaction_id"].unique())
    existing_set = set(existing["transaction_id"]) if not existing.empty else set()

    df_insert = df[~df["transaction_id"].isin(existing_set)]
    df_update = df[df["transaction_id"].isin(existing_set)]

    cur = prod_conn.cursor()

    try:

        if not df_insert.empty:

            execute_values(
                cur,
                f"INSERT INTO {table} ({','.join(cols)}) VALUES %s",
                df_insert[cols].values.tolist()
            )

        if not df_update.empty:

            execute_values(
                cur,
                f"""
                UPDATE {table} t
                SET
                    transaction_date=v.transaction_date,
                    transaction_type=v.transaction_type,
                    transaction_ref=v.transaction_ref,
                    transaction_name=v.transaction_name,
                    description=v.description,
                    coa_code=v.coa_code,
                    coa_name=v.coa_name,
                    coa_parent_code=v.coa_parent_code,
                    coa_parent_name=v.coa_parent_name,
                    branch_id=v.branch_id,
                    created_by=v.created_by,
                    amount=v.amount,
                    created_date=v.created_date,
                    posted_date=v.posted_date,
                    journal_no=v.journal_no
                FROM (VALUES %s) AS v({",".join(cols)})
                WHERE t.transaction_id=v.transaction_id
                """,
                df_update[cols].values.tolist()
            )

        prod_conn.commit()

        print("Insert:",len(df_insert))
        print("Update:",len(df_update))

    except Exception as e:

        prod_conn.rollback()
        print(f"❌ {table} error",e)

    finally:
        cur.close()


# =========================================================
# JOURNAL
# =========================================================

def sync_journal(dev_conn, prod_conn, table):

    # ------------------------------------------------------------------
    # DINONAKTIFKAN - butuh kunci pencocokan baru sebelum boleh dipakai lagi.
    #
    # Fungsi ini memakai journal_no sebagai kunci upsert trx_journal. Itu tidak
    # pernah benar dan sekarang jelas berbahaya:
    #   * trx_journal.journal_no mayoritas KOSONG - aplikasi tidak pernah
    #     mengisinya (kolomnya bahkan tidak di-map di entity Journal), dan
    #     scheduler sudah dihentikan menulis ke sana. Baris ber-journal_no NULL
    #     tidak pernah cocok dengan existing_set, sehingga SELALU masuk
    #     df_insert -> seluruh isi trx_journal digandakan tiap kali sync jalan.
    #   * journal_no juga tidak unik (ada ~182 ribu nomor kembar sepanjang 2026),
    #     jadi "UPDATE .. WHERE t.journal_no = v.journal_no" menimpa BANYAK baris
    #     sekaligus dengan nilai dari satu baris.
    #
    # Kunci yang benar adalah journal_id (PK trx_journal), tapi itu sequence yang
    # berjalan sendiri-sendiri di dev dan prod - mencocokkan langsung justru bisa
    # menimpa baris yang salah. Perlu keputusan lebih dulu: pakai journal_id
    # (kalau kedua DB memang sequence-nya sinkron) atau tambahkan kolom kunci
    # bisnis yang benar-benar unik per baris jurnal.
    #
    # Sengaja gagal keras daripada diam-diam merusak data.
    # ------------------------------------------------------------------
    raise RuntimeError(
        "sync_journal dinonaktifkan: journal_no tidak bisa dipakai sebagai kunci "
        "upsert trx_journal (mayoritas NULL dan tidak unik). Tentukan kunci "
        "pencocokan yang benar dulu - lihat komentar di atas."
    )

    print(f"\n🔹 Sync {table}")

    cols = [
        "journal_desc","transaction_id","journal_ref","coa_code",
        "coa_name","coa_parent_code","coa_parent_name",
        "status","branch_id","created_by","created_date",
        "is_premier","debit_amount","credit_amount","source",
        "clinic_code","clinic_name","ward_code","ward_name",
        "care_type","journal_no","admission_date","discharge_date",
        "thb_po_number","thb_receipt_date","thb_po_date",
        "thb_supplier_name","thb_total_transaction",
        "doctor_code","doctor_name","medical_record_number",
        "patient_name","payer_code","payer_name"
    ]

    df = pd.read_sql_query(
        f"SELECT {','.join(cols)} FROM {table}",
        dev_conn
    )

    df = clean_dataframe(df)

    existing = get_existing(prod_conn,table,"journal_no",df["journal_no"].unique())
    existing_set = set(existing["journal_no"]) if not existing.empty else set()

    df_insert = df[~df["journal_no"].isin(existing_set)]
    df_update = df[df["journal_no"].isin(existing_set)]

    cur = prod_conn.cursor()

    try:

        if not df_insert.empty:

            execute_values(
                cur,
                f"INSERT INTO {table} ({','.join(cols)}) VALUES %s",
                df_insert[cols].values.tolist()
            )

        if not df_update.empty:

            execute_values(
                cur,
                f"""
                UPDATE {table} t
                SET
                    journal_desc=v.journal_desc,
                    transaction_id=v.transaction_id,
                    journal_ref=v.journal_ref,
                    coa_code=v.coa_code,
                    coa_name=v.coa_name,
                    coa_parent_code=v.coa_parent_code,
                    coa_parent_name=v.coa_parent_name,
                    branch_id=v.branch_id,
                    created_by=v.created_by,
                    created_date=v.created_date::timestamp,
                    is_premier=v.is_premier,
                    debit_amount=v.debit_amount,
                    credit_amount=v.credit_amount,
                    source=v.source,
                    clinic_code=v.clinic_code,
                    clinic_name=v.clinic_name,
                    ward_code=v.ward_code,
                    ward_name=v.ward_name,
                    care_type=v.care_type,
                    admission_date=v.admission_date::timestamp,
                    discharge_date=v.discharge_date::timestamp,
                    thb_po_number=v.thb_po_number,
                    thb_receipt_date=v.thb_receipt_date::date,
                    thb_po_date=v.thb_po_date::date,
                    thb_supplier_name=v.thb_supplier_name,
                    thb_total_transaction=v.thb_total_transaction,
                    doctor_code=v.doctor_code,
                    doctor_name=v.doctor_name,
                    medical_record_number=v.medical_record_number,
                    patient_name=v.patient_name,
                    payer_code=v.payer_code,
                    payer_name=v.payer_name
                FROM (VALUES %s) AS v({",".join(cols)})
                WHERE t.journal_no=v.journal_no
                """,
                df_update[cols].values.tolist()
            )
        prod_conn.commit()

        print("Insert:",len(df_insert))
        print("Update:",len(df_update))

    except Exception as e:

        prod_conn.rollback()
        print(f"❌ {table} error",e)

    finally:
        cur.close()


# =========================================================
# MAIN
# =========================================================

def main():

    print(f"\n🚀 DEV → PROD SYNC {datetime.now()}")

    dev_conn = get_dev_connection()
    prod_conn = get_production_connection()

    # sync_mst_contact(dev_conn,prod_conn)
    # sync_invoice_exchange(dev_conn,prod_conn)

    # sync_transaction(dev_conn,prod_conn,"trx_transaction")
    # sync_transaction(dev_conn,prod_conn,"temp_transaction")

    sync_journal(dev_conn,prod_conn,"trx_journal")
    sync_journal(dev_conn,prod_conn,"temp_journal")

    dev_conn.close()
    prod_conn.close()

    print("\n✅ SYNC FINISHED")


if __name__ == "__main__":
    main()