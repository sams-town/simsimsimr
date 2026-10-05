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

from libs import get_khanza_connection, get_production_connection, create_log,date_range,date_month
import calendar
from datetime import datetime, timedelta
import uuid
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
    "receipt_date",
    "po_date",
    "due_date",
    "supplier_code",
    "supplier_name",
    "total_transaction",
    "status",
    "updated_by",
    "updated_date",
    "journal_date"
]

KEY_COLUMNS = ["po_number", "invoice_number"]


# ================================
# ORIGINAL FUNCTIONS (UNCHANGED)
# ================================
def get_detail_order(mysql_conn, dt_value, use_delta=False):
    date_str = dt_value.strftime("'%Y-%m-%d'")
    if use_delta:
        where_clause = f"WHERE timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal) >= '{dt_value}'"
    else:
        where_clause = f"WHERE jurnal.tgl_jurnal = {date_str}"
    
    sql = f"""
        select
            detailpesan.no_faktur,
            detailpesan.kode_brng,
            detailpesan.kode_sat,
            detailpesan.jumlah,
            detailpesan.h_pesan,
            detailpesan.subtotal,
            detailpesan.dis,
            detailpesan.besardis,
            detailpesan.total,
            detailpesan.no_batch,
            detailpesan.jumlah2,
            detailpesan.kadaluarsa,
            timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal) as tgl_jurnal
        from detailpesan
        left join pemesanan on detailpesan.no_faktur = pemesanan.no_faktur
        left join jurnal on jurnal.no_bukti = pemesanan.no_faktur
        {where_clause}

        union all

        select
            idp.no_faktur,
            idp.kode_brng,
            idp.kode_sat,
            idp.jumlah,
            null as h_pesan,
            idp.subtotal,
            idp.dis,
            idp.besardis,
            idp.total,
            null as no_batch,
            null as jumlah2,
            null as kadaluarsa,
            timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal)  as tgl_jurnal
        from ipsrsdetailpesan idp
        left join ipsrspemesanan ip on idp.no_faktur = ip.no_faktur
        left join jurnal on jurnal.no_bukti = ip.no_faktur
        {where_clause}

        union all

        select
            idp.no_faktur,
            idp.kode_brng,
            idp.kode_sat,
            idp.jumlah,
            null as h_pesan,
            idp.subtotal,
            idp.dis,
            idp.besardis,
            idp.total,
            null as no_batch,
            null as jumlah2,
            null as kadaluarsa,
            timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal) as tgl_jurnal
        from dapurdetailpesan idp
        left join ipsrspemesanan ip on idp.no_faktur = ip.no_faktur
        left join jurnal on jurnal.no_bukti = ip.no_faktur
        {where_clause}
    """
    return pd.read_sql_query(sql, mysql_conn)


def get_faktur(mysql_conn, dt_value, use_delta=False):

    date_str = dt_value.strftime("'%Y-%m-%d'")

    if use_delta:
        where_clause = f"WHERE timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal) >= '{dt_value}'"
    else:
        where_clause = f"WHERE jurnal.tgl_jurnal = {date_str}"

    sql = f"""
        SELECT
            x.tanggal,
            x.no_jurnal,
            x.no_bukti,
            x.nama_akun,
            x.debet,
            x.kredit,
            x.kode_akun,

            ipsrspemesanan.no_order as po_number,
            ipsrspemesanan.tgl_faktur as receipt_date,
            ipsrspemesanan.no_faktur as invoice_number,
            ipsrspemesanan.tgl_pesan as po_date,
            ipsrspemesanan.tgl_tempo as due_date,

            concat('NM', substr(datasuplier.kode_suplier,2)) as supplier_code,

            ipsrspemesanan.kode_suplier as suplier_code,
            datasuplier.nama_suplier as suplier_name,
            datasuplier.nama_suplier as contact_name,
            datasuplier.no_telp as phone,
            datasuplier.alamat as address,
            null as email,

            1 as contact_category_id,
            1 as branch_id,
            datasuplier.kode_suplier as thb_ref_code,
            'pemesanan non medis' as thb_contact_source,
            'NM' as source_type,

            cast(x.kredit as double) as total_transaction,
            'not sent' as status,
            1 as updated_by,
            now() as inserted_date,
            now() as updated_date

        FROM (
            SELECT
                TIMESTAMP(jurnal.tgl_jurnal,jurnal.jam_jurnal) AS tanggal,
                jurnal.no_jurnal,
                jurnal.no_bukti,
                rekening.nm_rek as nama_akun,
                detailjurnal.debet,
                detailjurnal.kredit,
                detailjurnal.kd_rek as kode_akun
            FROM jurnal
            JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
            JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
            {where_clause}
        ) x
        LEFT JOIN ipsrspemesanan on x.no_bukti = ipsrspemesanan.no_faktur
        LEFT JOIN ipsrssuplier datasuplier on ipsrspemesanan.kode_suplier = datasuplier.kode_suplier
        WHERE ipsrspemesanan.no_faktur is not null and x.kredit != 0

        UNION ALL

        SELECT
            x.tanggal,
            x.no_jurnal,
            x.no_bukti,
            x.nama_akun,
            x.debet,
            x.kredit,
            x.kode_akun,

            pemesanan.no_order,
            pemesanan.tgl_faktur,
            pemesanan.no_faktur,
            pemesanan.tgl_pesan,
            pemesanan.tgl_tempo,

            concat('M', substr(datasuplier.kode_suplier,2)) as supplier_code,

            pemesanan.kode_suplier,
            datasuplier.nama_suplier,
            datasuplier.nama_suplier,
            datasuplier.no_telp,
            datasuplier.alamat,
            null,
            1,
            1,
            datasuplier.kode_suplier,
            'pemesanan medis',
            'M',

            cast(x.kredit as double),
            'not sent',
            1,
            now(),
            now()

        FROM (
            SELECT
                TIMESTAMP(jurnal.tgl_jurnal,jurnal.jam_jurnal) AS tanggal,
                jurnal.no_jurnal,
                jurnal.no_bukti,
                rekening.nm_rek as nama_akun,
                detailjurnal.debet,
                detailjurnal.kredit,
                detailjurnal.kd_rek as kode_akun
            FROM jurnal
            JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
            JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
            {where_clause}
        ) x
        LEFT JOIN pemesanan on x.no_bukti = pemesanan.no_faktur
        LEFT JOIN datasuplier on pemesanan.kode_suplier = datasuplier.kode_suplier
        WHERE pemesanan.no_faktur is not null and x.kredit != 0

        UNION ALL

        SELECT
            x.tanggal,
            x.no_jurnal,
            x.no_bukti,
            x.nama_akun,
            x.debet,
            x.kredit,
            x.kode_akun,

            pemesanan.no_order,
            pemesanan.tgl_faktur,
            pemesanan.no_faktur,
            pemesanan.tgl_pesan,
            pemesanan.tgl_tempo,

            concat('DP', substr(datasuplier.kode_suplier,2)) as supplier_code,

            pemesanan.kode_suplier,
            datasuplier.nama_suplier,
            datasuplier.nama_suplier,
            datasuplier.no_telp,
            datasuplier.alamat,
            null,
            1,
            1,
            datasuplier.kode_suplier,
            'pemesanan dapur',
            'DP',

            cast(x.kredit as double),
            'not sent',
            1,
            now(),
            now()

        FROM (
            SELECT
                TIMESTAMP(jurnal.tgl_jurnal,jurnal.jam_jurnal) AS tanggal,
                jurnal.no_jurnal,
                jurnal.no_bukti,
                rekening.nm_rek as nama_akun,
                detailjurnal.debet,
                detailjurnal.kredit,
                detailjurnal.kd_rek as kode_akun
            FROM jurnal
            JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
            JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
            {where_clause}
        ) x
        LEFT JOIN dapurpemesanan pemesanan on x.no_bukti = pemesanan.no_faktur
        LEFT JOIN dapursuplier datasuplier on pemesanan.kode_suplier = datasuplier.kode_suplier
        WHERE pemesanan.no_faktur is not null and x.kredit != 0

        ORDER BY tanggal,no_jurnal
    """
    return pd.read_sql_query(sql, mysql_conn)


# ================================
# MAIN (MODIFIED PART ONLY)
# ================================
def main(detail_order, faktur, update_columns):

    print(f"\n🔸 Processing Faktur {datetime.now()}")

    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    df_invoice = pd.DataFrame({
        "po_number": faktur["po_number"],
        "receipt_date": faktur["receipt_date"],
        "invoice_number": faktur["invoice_number"],
        "po_date": faktur["po_date"],
        "due_date": faktur["due_date"],
        "supplier_code": faktur["supplier_code"],
        "supplier_name": faktur["suplier_name"],
        "total_transaction": faktur["total_transaction"],
        "status": faktur["status"],
        "updated_by": faktur["updated_by"],
        "inserted_date": faktur["inserted_date"],
        "updated_date": faktur["updated_date"],
        "journal_date": faktur["tanggal"],
        "contact_name": faktur.get("contact_name", None),
        "phone": faktur.get("phone", None),
        "address": faktur.get("address", None),
        "email": faktur.get("email", None),
        "contact_category_id": faktur.get("contact_category_id", None),
        "branch_id": faktur.get("branch_id", None),
        "ref_code": faktur.get("thb_ref_code", None)
    }).drop_duplicates(subset=["po_number", "invoice_number"])

    df_detail = pd.DataFrame({
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
        "created_date": datetime.now(),
        "journal_date": detail_order["tgl_jurnal"]
    })

    # 🔥 GET EXISTING
    keys = list(zip(df_invoice["po_number"], df_invoice["invoice_number"]))

    if not keys:
        existing_df = pd.DataFrame(columns=["po_number", "invoice_number"])
    else:
        existing_df = pd.read_sql_query(
            """
            SELECT po_number, invoice_number
            FROM trx_invoice_exchange
            WHERE (po_number, invoice_number) IN %s
            """,
            prod_conn,
            params=(tuple(keys),)
        )

    existing_set = set(zip(existing_df["po_number"], existing_df["invoice_number"]))

    df_insert = df_invoice[
        ~df_invoice.apply(lambda r: (r["po_number"], r["invoice_number"]) in existing_set, axis=1)
    ]

    df_update = df_invoice[
        df_invoice.apply(lambda r: (r["po_number"], r["invoice_number"]) in existing_set, axis=1)
    ]

    try:
        # INSERT
        if not df_insert.empty:
            execute_values(
                cur,
                """
                INSERT INTO trx_invoice_exchange (
                    po_number, receipt_date, invoice_number, po_date, due_date,
                    supplier_code, supplier_name, total_transaction,
                    status, updated_by, inserted_date, updated_date, journal_date
                ) VALUES %s
                """,
                df_insert[[
                    "po_number", "receipt_date", "invoice_number", "po_date", "due_date",
                    "supplier_code", "supplier_name", "total_transaction",
                    "status", "updated_by", "inserted_date", "updated_date", "journal_date"
                ]].values.tolist()
            )

        # UPDATE (DYNAMIC)
        if not df_update.empty and update_columns:

            value_columns = KEY_COLUMNS + update_columns

            set_clause = ", ".join([f"{col} = v.{col}" for col in update_columns])

            update_sql = f"""
            UPDATE trx_invoice_exchange t
            SET {set_clause}
            FROM (VALUES %s) AS v({", ".join(value_columns)})
            WHERE t.po_number = v.po_number
              AND t.invoice_number = v.invoice_number
            """

            execute_values(
                cur,
                update_sql,
                df_update[value_columns].values.tolist()
            )

        # CONTACT (mst_contact)
        if not df_insert.empty:
            df_contact_new = df_insert.drop_duplicates(subset=["supplier_code"]).copy()
            
            # Ensure correct column order for INSERT
            columns_order_contact = ['contact_name', 'phone', 'address', 'email', 'contact_category_id', 'branch_id', 'ref_code', 'due_date']
            df_contact_new = df_contact_new[columns_order_contact].copy()
            
            # Convert integer columns - convert to numeric, then to object to avoid NAType - fully vectorized
            integer_columns = ['contact_category_id', 'branch_id', 'due_date']
            numeric_values = df_contact_new[integer_columns].apply(pd.to_numeric, errors='coerce')
            mask_contact = pd.notna(numeric_values)
            placeholder = -999999
            temp_filled = numeric_values.fillna(placeholder)
            int_values = temp_filled.astype(float).astype(int)
            df_contact_new[integer_columns] = int_values.astype('object')
            df_contact_new[integer_columns] = df_contact_new[integer_columns].where(mask_contact, None)
            
            # Convert string columns - keep None as None, convert others to string - fully vectorized
            string_columns = ['contact_name', 'phone', 'address', 'email', 'ref_code']
            df_contact_new[string_columns] = df_contact_new[string_columns].astype('object')
            mask_contact = pd.notna(df_contact_new[string_columns])
            df_contact_new[string_columns] = df_contact_new[string_columns].where(
                ~mask_contact, df_contact_new[string_columns].astype(str)
            )
            
            # Replace all remaining NaN/NaT with None for proper database insertion
            df_contact_new = df_contact_new.where(pd.notna(df_contact_new), None)
            df_contact_new = df_contact_new.replace({pd.NA: None, pd.NaT: None})

            # Get existing contacts to separate INSERT and UPDATE
            contact_keys = tuple(df_contact_new["ref_code"].dropna().unique().tolist())
            if contact_keys:
                existing_contact_df = pd.read_sql_query(
                    """
                    SELECT ref_code
                    FROM master.mst_contact
                    WHERE ref_code IN %s
                    """,
                    prod_conn,
                    params=(contact_keys,)
                )
                existing_contact_set = set(existing_contact_df["ref_code"])
            else:
                existing_contact_set = set()

            df_contact_insert = df_contact_new[~df_contact_new["ref_code"].isin(existing_contact_set)]
            df_contact_update = df_contact_new[df_contact_new["ref_code"].isin(existing_contact_set)]

            if not df_contact_insert.empty:
                execute_values(
                    cur,
                    """
                    INSERT INTO master.mst_contact (
                        contact_name, phone, address, email, contact_category_id, branch_id, ref_code, due_date
                    ) VALUES %s
                    """,
                    df_contact_insert.values.tolist()
                )

            if not df_contact_update.empty:
                # Update existing contact
                update_cols_contact = ['contact_name', 'phone', 'address', 'email', 'contact_category_id', 'branch_id', 'due_date']
                set_clause_contact = ", ".join([f"{col} = v.{col}::bigint" if col == 'due_date' else f"{col} = v.{col}" for col in update_cols_contact])
                value_columns_contact = ['ref_code'] + update_cols_contact
                
                update_sql_contact = f"""
                UPDATE master.mst_contact t
                SET {set_clause_contact}
                FROM (VALUES %s) AS v({", ".join(value_columns_contact)})
                WHERE t.ref_code = v.ref_code
                """
                
                execute_values(
                    cur,
                    update_sql_contact,
                    df_contact_update[value_columns_contact].values.tolist()
                )

        # DETAIL
        execute_values(
            cur,
            """
            INSERT INTO thb_detail_order (
                no_invoice, item_code, unit_code, quantity, order_price, 
                sub_total, discount, discount_amount, grand_total, no_batch, 
                total_2, expired, created_date, journal_date
            ) VALUES %s
            """,
            df_detail.values.tolist()
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


# ================================
# RUNNER
# ================================
if __name__ == "__main__":

    parser = argparse.ArgumentParser(description="Faktur Runner")

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
    create_log('Job Khanza Faktur','start')

    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        main(get_detail_order(mysql_conn, d), get_faktur(mysql_conn, d), update_columns)

    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            main(get_detail_order(mysql_conn, dt), get_faktur(mysql_conn, dt), update_columns)

    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            main(get_detail_order(mysql_conn, dt), get_faktur(mysql_conn, dt), update_columns)

    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        main(get_detail_order(mysql_conn, delta_dt, True), get_faktur(mysql_conn, delta_dt, True), update_columns)

    else:
        main(get_detail_order(mysql_conn, datetime.now(), True), get_faktur(mysql_conn, datetime.now(), True), update_columns)

    create_log('Job Khanza Faktur','finish')
    mysql_conn.close()