"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Khanza Purchase Order Migration
  Created   : 2025
========================================================
"""
import sys
import os
project_root = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
sys.path.insert(0, project_root)

from libs import get_khanza_connection, get_production_connection, create_log, date_range, date_month

from datetime import datetime
import pandas as pd
import warnings
import argparse
import traceback
from psycopg2.extras import execute_values

warnings.filterwarnings("ignore")


# ================================
# CONFIG UPDATE COLUMNS
# ================================
ALL_UPDATE_COLUMNS = [
    "supplier_code",
    "supplier_name",
    "nip",
    "date",
    "total",
    "discount",
    "grand_total",
    "ppn",
    "stamp_duty",
    "invoice_amount",
    "status",
    "source",
    "updated_date",
    "updated_by"
]

KEY_COLUMNS = ["order_number"]


# ================================
# GET DATA (UNCHANGED)
# ================================
def get_po(mysql_conn, dt_value, use_delta=False):

    date_str = dt_value.strftime("'%Y-%m-%d'")

    if use_delta:
        where_clause = f"WHERE spm.tanggal >= {date_str}"
    else:
        where_clause = f"WHERE spm.tanggal = {date_str}"

    sql = f"""
        select
            spm.no_pemesanan as order_number,
            concat('M', substr(spm.kode_suplier,2)) as supplier_code,
            ds.nama_suplier as supplier_name,
            spm.nip as nip,
            spm.tanggal as date,
            spm.total1 as total,
            spm.potongan as discount,
            spm.total2 as grand_total,
            spm.ppn as ppn,
            spm.meterai as stamp_duty,
            spm.tagihan as invoice_amount,
            spm.status as status,
            'pemesanan medis' as source
        from surat_pemesanan_medis spm
        left join datasuplier ds on ds.kode_suplier = spm.kode_suplier
        {where_clause}

        union all

        select
            spm.no_pemesanan as order_number,
            concat('DP', substr(spm.kode_suplier,2)) as supplier_code,
            ds.nama_suplier as supplier_name,
            spm.nip as nip,
            spm.tanggal as date,
            spm.subtotal as total,
            spm.potongan as discount,
            spm.total as grand_total,
            spm.ppn as ppn,
            spm.meterai as stamp_duty,
            spm.tagihan as invoice_amount,
            spm.status as status,
            'pemesanan dapur' as source
        from surat_pemesanan_dapur spm
        left join dapursuplier ds on ds.kode_suplier = spm.kode_suplier
        {where_clause}

        union all

        select
            spm.no_pemesanan as order_number,
            concat('NM', substr(spm.kode_suplier,2)) as supplier_code,
            ds.nama_suplier as supplier_name,
            spm.nip as nip,
            spm.tanggal as date,
            spm.subtotal as total,
            spm.potongan as discount,
            spm.total as grand_total,
            spm.ppn as ppn,
            spm.meterai as stamp_duty,
            spm.tagihan as invoice_amount,
            spm.status as status,
            'pemesanan non medis' as source
        from surat_pemesanan_non_medis spm
        left join ipsrssuplier ds on ds.kode_suplier = spm.kode_suplier
        {where_clause}

        order by date;
    """

    return pd.read_sql_query(sql, mysql_conn)


# ================================
# PROCESS (NO ON CONFLICT)
# ================================
def process_po(cur, df, update_columns):

    if df.empty:
        print("⚠️ No data")
        return

    order_numbers = tuple(df["order_number"].unique())

    existing_df = pd.read_sql_query(
        """
        SELECT order_number
        FROM public.thb_purchase_order
        WHERE order_number IN %s
        """,
        cur.connection,
        params=(order_numbers,)
    )

    existing_set = set(existing_df["order_number"])

    df_insert = df[~df["order_number"].isin(existing_set)]
    df_update = df[df["order_number"].isin(existing_set)]

    # INSERT
    if not df_insert.empty:
        execute_values(
            cur,
            """
            INSERT INTO public.thb_purchase_order (
                order_number, supplier_code, supplier_name, nip, date,
                total, discount, grand_total, ppn, stamp_duty,
                invoice_amount, status, source,
                created_date, updated_date, updated_by
            ) VALUES %s
            """,
            df_insert.values.tolist()
        )
        print(f"🆕 Insert: {len(df_insert)}")

    # UPDATE
    if not df_update.empty and update_columns:

        value_columns = ["order_number"] + update_columns

        set_clause = ", ".join([f"{col} = v.{col}" for col in update_columns])

        update_sql = f"""
        UPDATE public.thb_purchase_order t
        SET {set_clause}
        FROM (VALUES %s) AS v({", ".join(value_columns)})
        WHERE t.order_number = v.order_number
        """

        execute_values(
            cur,
            update_sql,
            df_update[value_columns].values.tolist()
        )

        print(f"🔁 Update: {len(df_update)}")

    elif not update_columns:
        print("⏭️ Update skipped")


# ================================
# MAIN
# ================================
def main(po, update_columns):

    print(f"\n🔸 Processing Purchase Order {datetime.now()}")

    if po.empty:
        print("⚠️ No data found.")
        return

    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    df_po = pd.DataFrame({
        "order_number": po["order_number"],
        "supplier_code": po["supplier_code"],
        "supplier_name": po["supplier_name"],
        "nip": po["nip"],
        "date": po["date"],
        "total": po["total"],
        "discount": po["discount"],
        "grand_total": po["grand_total"],
        "ppn": po["ppn"],
        "stamp_duty": po["stamp_duty"],
        "invoice_amount": po["invoice_amount"],
        "status": po["status"],
        "source": po["source"],
        "created_date": datetime.now(),
        "updated_date": datetime.now(),
        "updated_by": 1
    }).drop_duplicates(subset=["order_number"])

    try:
        process_po(cur, df_po, update_columns)

        prod_conn.commit()
        print(f"✅ Done Purchase Order {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Purchase Order','error')
        print(f"❌ Error Purchase Order: {e}")
        traceback.print_exc()

    finally:
        cur.close()
        prod_conn.close()


# ================================
# RUNNER
# ================================
if __name__ == "__main__":

    parser = argparse.ArgumentParser(description="Purchase Order Runner")

    group = parser.add_mutually_exclusive_group(required=False)

    group.add_argument("--date")
    group.add_argument("--range", nargs=2)
    group.add_argument("--month", nargs=2)
    group.add_argument("--delta")

    parser.add_argument(
        "--update-columns",
        nargs="+",
        choices=["all"] + ALL_UPDATE_COLUMNS,
        help="Gunakan 'all' untuk update semua kolom"
    )

    args = parser.parse_args()

    if args.update_columns and "all" in args.update_columns:
        update_columns = ALL_UPDATE_COLUMNS
    else:
        update_columns = args.update_columns or []

    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Purchase Order','start')

    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        main(get_po(mysql_conn, d), update_columns)

    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            main(get_po(mysql_conn, dt), update_columns)

    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            main(get_po(mysql_conn, dt), update_columns)

    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        main(get_po(mysql_conn, delta_dt, True), update_columns)

    else:
        main(get_po(mysql_conn, datetime.now(), True), update_columns)

    create_log('Job Khanza Purchase Order','finish')
    mysql_conn.close()