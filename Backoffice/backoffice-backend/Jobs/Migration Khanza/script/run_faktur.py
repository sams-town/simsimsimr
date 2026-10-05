"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Khanza Faktur Migration
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

def get_detail_order(mysql_conn, dt_value, use_delta=False):
    date_str = dt_value.strftime("'%Y-%m-%d'")
    if use_delta:
        where_clause = f"WHERE jurnal.tgl_jurnal >= {date_str}"
    else:
        where_clause = f"WHERE jurnal.tgl_jurnal = {date_str}"
    
    sql = f"""
        select
            detailpesan.*
        from detailpesan
        left join pemesanan on detailpesan.no_faktur = pemesanan.no_faktur
        left join jurnal on jurnal.no_bukti = pemesanan.no_faktur
        {where_clause}
    """
    df = pd.read_sql_query(sql, mysql_conn)
    return df

def get_faktur(mysql_conn, dt_value, use_delta=False):
    date_str = dt_value.strftime("'%Y-%m-%d'")
    if use_delta:
        where_clause = f"WHERE jurnal.tgl_jurnal >= {date_str}"
    else:
        where_clause = f"WHERE jurnal.tgl_jurnal = {date_str}"
    
    sql = f"""
        SELECT
            x.tanggal as tanggal,
            x.no_jurnal as no_jurnal,
            x.no_bukti as journal_id,
            x.nama_akun as coa_name,
            x.debet as debit,
            x.kredit as credit,
            x.kode_akun as coa_code,
            pemesanan.no_order as po_number,
            pemesanan.tgl_faktur as receipt_date,
            pemesanan.no_faktur as invoice_number,
            pemesanan.tgl_pesan as po_date,
            pemesanan.tgl_tempo as due_date,
            pemesanan.kode_suplier as suplier_code,
            datasuplier.nama_suplier as suplier_name,
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
            ) as total_transaction,
            'not sent' as status,
            1 as updated_by,
            now() as inserted_date,
            now() as updated_date
        FROM (
            SELECT
                TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) AS tanggal,
                jurnal.no_jurnal AS no_jurnal,
                jurnal.no_bukti as no_bukti,
                CASE
                    WHEN rekening.level = 1 THEN CONCAT('  ', rekening.nm_rek)
                    ELSE rekening.nm_rek
                END AS nama_akun,
                detailjurnal.debet AS debet,
                detailjurnal.kredit AS kredit,
                detailjurnal.kd_rek AS kode_akun,
                ROW_NUMBER() OVER (
                    PARTITION BY jurnal.no_jurnal
                    ORDER BY
                        TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) ASC,
                        detailjurnal.debet DESC
                ) AS rn
            FROM jurnal
            JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
            JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
            {where_clause}
        ) x
        LEFT JOIN pemesanan on x.no_bukti = pemesanan.no_faktur
        LEFT JOIN datasuplier on pemesanan.kode_suplier = datasuplier.kode_suplier
        LEFT JOIN bangsal on pemesanan.kd_bangsal = bangsal.kd_bangsal
        LEFT JOIN petugas on pemesanan.nip = petugas.nip
        WHERE
            rn = 1
            and pemesanan.no_faktur is not null
        ORDER BY tanggal ASC, no_jurnal ASC;
    """
    df = pd.read_sql_query(sql, mysql_conn)
    return df

def main(detail_order, faktur):
    print(f"\n🔸 Processing Faktur {datetime.now()}")
    prod_conn = get_production_connection()
    cur = prod_conn.cursor()


    df_invoice_exchange = pd.DataFrame({
        "po_number": faktur["po_number"],
        "receipt_date": faktur["receipt_date"],
        "invoice_number": faktur["invoice_number"],
        "po_date": faktur["po_date"],
        "due_date": faktur["due_date"],
        "supplier_code": faktur["suplier_code"],
        "supplier_name": faktur["suplier_name"],
        "total_transaction": faktur["total_transaction"],
        "status": faktur["status"],
        "updated_by": faktur["updated_by"],
        "inserted_date": faktur["inserted_date"],
        "updated_date": faktur["updated_date"]
    })

    df_detail_order = pd.DataFrame({
        "no_invoice": detail_order["no_faktur"],
        "item_code": detail_order["kode_brng"],
        "unit_code": detail_order["kode_sat"],
        "quantity": detail_order["jumlah"],
        "order_price": detail_order["h_pesan"],
        "sub_total": detail_order["subtotal"],
        "discount": detail_order["dis"],
        "discount_amount": detail_order["besardis"],
        "grand_total": detail_order["total"],
        "no_batch": detail_order["no_batch"],
        "total_2": detail_order["jumlah2"],
        "expired": detail_order["kadaluarsa"],
        "created_date": datetime.now()
    })

    try:
        execute_values(cur,
            """
            INSERT INTO trx_invoice_exchange (
                po_number, receipt_date, invoice_number, po_date, due_date, supplier_code, supplier_name,
                total_transaction, status, updated_by, inserted_date, updated_date
            ) VALUES %s
            """,
            df_invoice_exchange.values.tolist()
        )

        execute_values(cur,
            """
            INSERT INTO thb_detail_order (
                no_invoice, item_code, unit_code, quantity, order_price, 
                sub_total, discount, discount_amount, grand_total, no_batch, 
                total_2, expired, created_date
            ) VALUES %s
            """,
            df_detail_order.values.tolist()
        )
        prod_conn.commit()
        print(f"✅ Done Faktur {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Faktur','error')
        print(f"❌ Error Faktur: {e}")
        traceback.print_exc()

    finally:
        cur.close()
        prod_conn.close()

if __name__ == "__main__":
    khanza_conn = get_khanza_connection()
    parser = argparse.ArgumentParser(description="Journal Runner (MySQL -> Postgres)")
    group = parser.add_mutually_exclusive_group(required=False)
    group.add_argument("--date", help="Single date YYYY-MM-DD")
    group.add_argument("--range", nargs=2, help="Date range YYYY-MM-DD YYYY-MM-DD")
    group.add_argument("--month", nargs=2, help="Month mode: YYYY MM")
    group.add_argument("--delta", help="Datetime start YYYY-MM-DD HH:MM:SS")
    args = parser.parse_args()
    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Faktur','start')

    # --- SINGLE DATE ---
    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        detail_order = get_detail_order(mysql_conn, d, use_delta=False)
        faktur = get_faktur(mysql_conn, d, use_delta=False)
        main(detail_order, faktur)
    
    # --- RANGE DATE (loop harian) ---
    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            detail_order = get_detail_order(mysql_conn, dt, use_delta=False)
            faktur = get_faktur(mysql_conn, dt, use_delta=False)
            main(detail_order, faktur)

    # --- MONTH MODE ---
    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            detail_order = get_detail_order(mysql_conn, dt, use_delta=False)
            faktur = get_faktur(mysql_conn, dt, use_delta=False)
            main(detail_order, faktur)

    # --- DELTA MODE (NO LOOP) ---
    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        detail_order = get_detail_order(mysql_conn, delta_dt, use_delta=False)
        faktur = get_faktur(mysql_conn, delta_dt, use_delta=False)
        main(detail_order, faktur)
    
    else:
        delta_dt = get_latest_trx_date()
        detail_order = get_detail_order(mysql_conn, delta_dt, use_delta=False)
        faktur = get_faktur(mysql_conn, delta_dt, use_delta=False)
        main(detail_order, faktur)
        
    create_log('Job Khanza Faktur','finish')
    mysql_conn.close()

    