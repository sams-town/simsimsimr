"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Khanza Journal Migration
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

def get_transactions(conn, date, use_delta=False):
  date_str = date.strftime("'%Y-%m-%d'")
  if use_delta:
      where_clause = f"WHERE tgl_jurnal >= {date_str}"
  else:
      where_clause = f"WHERE tgl_jurnal = {date_str}"

  sql = f"""
      SELECT
          jurnal.no_jurnal AS no_journal,
          TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) AS Tanggal,
          jurnal.keterangan,
          jurnal.no_bukti as no_bukti
      FROM jurnal
      {where_clause}
    """
  df = pd.read_sql_query(sql, conn)

  return df


def get_journal(conn,date, use_delta=False):
    date_str = date.strftime("'%Y-%m-%d'")
    if use_delta:
        where_clause = f"WHERE jurnal.tgl_jurnal >= {date_str}"
    else:
        where_clause = f"WHERE jurnal.tgl_jurnal = {date_str}"

    sql = f"""
        WITH pemesanan AS (
        SELECT
            pemesanan.no_faktur,
            pemesanan.no_order,
            datasuplier.nama_suplier,
            pemesanan.tgl_tempo,
            pemesanan.tgl_pesan,
            pemesanan.tgl_faktur,
            pemesanan.kd_bangsal,
            bangsal.nm_bangsal,
            pemesanan.tagihan,
            COALESCE(SUM(bayar_pemesanan.besar_bayar), 0) AS bayar,
            datasuplier.nama_bank,
            datasuplier.rekening
        FROM pemesanan
        JOIN datasuplier
            ON pemesanan.kode_suplier = datasuplier.kode_suplier
        JOIN bangsal
            ON pemesanan.kd_bangsal = bangsal.kd_bangsal
        LEFT JOIN bayar_pemesanan
            ON bayar_pemesanan.no_faktur = pemesanan.no_faktur
        GROUP BY
            pemesanan.no_faktur,
            pemesanan.no_order,
            datasuplier.nama_suplier,
            pemesanan.tgl_tempo,
            pemesanan.tgl_pesan,
            pemesanan.tgl_faktur,
            pemesanan.kd_bangsal,
            bangsal.nm_bangsal,
            pemesanan.tagihan,
            datasuplier.nama_bank,
            datasuplier.rekening
    )

    SELECT
        TIMESTAMP(jurnal.tgl_jurnal, jurnal.jam_jurnal) AS tanggal,
        jurnal.no_jurnal AS no_jurnal,
        jurnal.no_bukti AS no_bukti,
        jurnal.jam_jurnal AS jam_jurnal,

        CASE
            WHEN rekening.level = 1 THEN CONCAT('  ', rekening.nm_rek)
            ELSE rekening.nm_rek
        END AS nama_akun,
        CONCAT(
            'No.Jur ', jurnal.no_jurnal,
            ', No.Buk ', jurnal.no_bukti,
            ', ', jurnal.keterangan
        ) AS keterangan,

        detailjurnal.debet AS debet,
        detailjurnal.kredit AS kredit,
        detailjurnal.kd_rek AS kode_akun,

        poliklinik.kd_poli AS kode_poli,
        poliklinik.nm_poli AS nama_poli,

        bangsal.kd_bangsal AS kode_bangsal,
        bangsal.nm_bangsal AS nama_bangsal,

        reg_periksa.status_lanjut AS status_lanjutan,

        pemesanan.no_order AS no_po,
        pemesanan.tgl_tempo AS tgl_penerimaan,
        pemesanan.tgl_pesan AS tgl_po,
        pemesanan.nama_suplier AS nama_suplier,
        pemesanan.bayar AS jml_transaksi,
        pemesanan.no_faktur AS no_faktur,

        pasien.no_rkm_medis AS no_rekam_medis,
        pasien.nm_pasien AS nama_pasien,

        dokter.kd_dokter AS kode_dokter,
        dokter.nm_dokter AS nama_dokter,

        penjab.kd_pj AS kode_penjab,
        penjab.png_jawab AS nama_penjab,

        IF(reg_periksa.status_lanjut = 'Ranap', timestamp(kamar_inap.tgl_masuk, kamar_inap.jam_masuk), timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg)) as tgl_masuk,
        IF(reg_periksa.status_lanjut = 'Ranap', timestamp(kamar_inap.tgl_keluar, kamar_inap.jam_keluar), timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg)) as tgl_keluar
    FROM jurnal
    JOIN detailjurnal ON jurnal.no_jurnal = detailjurnal.no_jurnal
    JOIN rekening ON detailjurnal.kd_rek = rekening.kd_rek
    LEFT JOIN reg_periksa ON reg_periksa.no_rawat = jurnal.no_bukti
    LEFT JOIN penjab ON penjab.kd_pj = reg_periksa.kd_pj
    LEFT JOIN poliklinik ON poliklinik.kd_poli = reg_periksa.kd_poli
    LEFT JOIN pemesanan ON pemesanan.no_faktur = jurnal.no_bukti
    LEFT JOIN bangsal ON pemesanan.kd_bangsal = bangsal.kd_bangsal
    LEFT JOIN dokter ON reg_periksa.kd_dokter = dokter.kd_dokter
    LEFT JOIN pasien ON reg_periksa.no_rkm_medis = pasien.no_rkm_medis
    LEFT JOIN kamar_inap on reg_periksa.no_rawat = kamar_inap.no_rawat
    {where_clause}
    ORDER BY
        tanggal ASC,
        jam_jurnal ASC,
        no_jurnal ASC,
        debet DESC;
    """

    df = pd.read_sql_query(sql, conn)
    return df

def generate_journal_no_with_tracking(date_val, pg_conn, tracking_dict):
    if pd.isna(date_val):
        return ""
    
    # Ensure date_val is datetime
    if isinstance(date_val, str):
        date_val = pd.to_datetime(date_val)
    elif not isinstance(date_val, datetime):
        date_val = pd.to_datetime(date_val)
    
    prefix = f"JRN/{date_val.year:04d}/{date_val.month:02d}/"
    
    # Check in-memory tracking first
    if prefix in tracking_dict:
        tracking_dict[prefix] += 1
        next_no = tracking_dict[prefix]
    else:
        # Query database for last number
        cur = pg_conn.cursor()
        query = """
            SELECT journal_no 
            FROM trx_transaction 
            WHERE journal_no IS NOT NULL 
            AND journal_no LIKE %s
            ORDER BY journal_no DESC 
            LIMIT 1
        """
        cur.execute(query, (f"{prefix}%",))
        row = cur.fetchone()
        cur.close()
        
        next_no = 1
        if row and row[0]:
            journal_no = row[0]
            if len(journal_no) > len(prefix):
                try:
                    current = int(journal_no[len(prefix):])
                    next_no = current + 1
                except ValueError:
                    pass
        
        tracking_dict[prefix] = next_no
    
    return f"{prefix}{next_no:04d}"

def main(transactions, journal, date_label):
    print(f"\n🔸 Processing Transaction {datetime.now()}: {date_label}")
    prod_conn = get_production_connection()
    prod_cur = prod_conn.cursor()

    if len(transactions) == 0:
        print(f"⚪ No transaction found. Skipping.")
        return

    if len(journal) == 0:
        print(f"⚪ No journal found. Skipping.")
        return

    mapping_coa = pd.read_sql_query("SELECT coa_source, coa_dest FROM master.mst_coa_mapping",prod_conn)
    mapping_coa.columns = ["kode_akun", "coa_dest"]

    journal = journal.merge(mapping_coa, on="kode_akun", how="left")

    coa = pd.read_sql_query("SELECT coa_code, coa_name FROM master.mst_coa",prod_conn)
    coa.columns = ["coa_dest", "coa_name"]

    journal = journal.merge(coa, on="coa_dest", how="left")

    journal["coa_dest"].fillna("0", inplace=True)
    journal["coa_name"].fillna("0", inplace=True)

    transactions["transaction_id"] = transactions.apply(lambda _: str(uuid.uuid4()), axis=1)
    transactions["transaction_no"] = transactions["no_journal"]
    transactions["transaction_ref"] = transactions["no_bukti"]
    transactions["created_date"] = datetime.utcnow()

    journal = journal.merge(
        transactions[["no_journal", "transaction_id"]], left_on="no_jurnal", right_on="no_journal", how="left"
    )
    journal["created_date"] = datetime.utcnow()

    coa_tranasctions = journal.groupby('transaction_id').agg({
        'coa_dest': 'first',
        'coa_name': 'first'
    }).reset_index()
    coa_tranasctions.columns = ['transaction_id', 'coa_code', 'coa_name']
    
    transactions = transactions.merge(coa_tranasctions, on='transaction_id', how='left')
    transactions['coa_code'].fillna("0", inplace=True)
    transactions['coa_name'].fillna("0", inplace=True)

    # Deduplicate transactions by transaction_no to ensure same journal_no for same transaction_no
    # Sort by Tanggal first to ensure consistent date selection for duplicate transaction_no
    transactions_sorted = transactions.sort_values('Tanggal').copy()
    
    # Group by transaction_no only, take first occurrence (earliest date)
    transactions_grouped = transactions_sorted.groupby('transaction_no').first().reset_index()
    
    # Generate journal_no untuk unique transactions
    date_prefix_to_last_no = {}
    transactions_grouped['journal_no'] = transactions_grouped['Tanggal'].apply(
        lambda x: generate_journal_no_with_tracking(x, prod_conn, date_prefix_to_last_no)
    )
    
    # Map journal_no back ke semua transactions berdasarkan transaction_no
    journal_no_map = transactions_grouped.set_index('transaction_no')['journal_no'].to_dict()
    transactions['journal_no'] = transactions['transaction_no'].map(journal_no_map)
    
    # Map journal_no ke journal berdasarkan transaction_id
    journal_no_map_by_id = transactions.set_index('transaction_id')['journal_no'].to_dict()
    journal['journal_no'] = journal['transaction_id'].map(journal_no_map_by_id)

    df_final_transactions = pd.DataFrame({
        "transaction_id": transactions["transaction_id"],
        "transaction_date": transactions["Tanggal"],
        "transaction_type": "Payment",
        "transaction_no": transactions["transaction_no"],
        "transaction_ref": transactions["transaction_ref"],
        "transaction_name": "KhanzaJournal",
        "description": None,
        "coa_code": transactions["coa_code"],
        "coa_name": transactions["coa_name"],
        "coa_parent_code": None,
        "coa_parent_name": None,
        "status": "Published",
        "branch_id": 1,
        "created_by": 1,
        "amount": None,
        "created_date": transactions["created_date"],
        "posted_date" : transactions["Tanggal"],
        "journal_no": transactions["journal_no"]
    })

    df_final_journal = pd.DataFrame({
        "journal_desc": journal["keterangan"],
        "transaction_id": journal["transaction_id"],
        "journal_ref": journal["kode_akun"],
        "coa_code": journal["coa_dest"],
        "coa_name": journal["coa_name"],
        "coa_parent_code": None,
        "coa_parent_name": None,
        "status": "Published",
        "branch_id": 1,
        "created_by": 1,
        "created_date": journal["created_date"],
        "is_premier": False,
        "debit_amount": journal["debet"],
        "credit_amount": journal["kredit"],
        "source": "KhanzaJournal",
        "clinic_code": journal["kode_poli"],
        "clinic_name": journal["nama_poli"],
        "ward_code": journal["kode_bangsal"],
        "ward_name": journal["nama_bangsal"],
        "care_type": journal["status_lanjutan"],
        "journal_no": journal["journal_no"],
        "admission_date": journal["tgl_masuk"],
        "discharge_date": journal["tgl_keluar"]
    })

    # Calculate amount from debit and credit in df_final_journal grouped by transaction_id
    amount_by_transaction = df_final_journal.groupby('transaction_id').agg({
        'debit_amount': 'sum',
        'credit_amount': 'sum'
    }).reset_index()
    amount_by_transaction['amount'] = amount_by_transaction['debit_amount'] - amount_by_transaction['credit_amount']
    
    # Merge amount to df_final_transactions and replace the amount column
    amount_map = amount_by_transaction.set_index('transaction_id')['amount'].to_dict()
    df_final_transactions['amount'] = df_final_transactions['transaction_id'].map(amount_map)

    try:
        execute_values(prod_cur,
            """
            INSERT INTO trx_transaction (
                transaction_id, transaction_date, transaction_type, transaction_no, transaction_ref, transaction_name,
                description, coa_code, coa_name, coa_parent_code, coa_parent_name,
                status, branch_id, created_by, amount, created_date, posted_date, journal_no
            ) VALUES %s
            """,
            df_final_transactions.values.tolist()
        )

        # execute_values(prod_cur,
        #     """
        #     INSERT INTO trx_journal (
        #         journal_desc, transaction_id, journal_ref, coa_code, coa_name,
        #         coa_parent_code, coa_parent_name, status, branch_id, created_by,
        #         created_date, is_premier, debit_amount, credit_amount, source,
        #         clinic_code, clinic_name, ward_code, ward_name, care_type, journal_no
        #         admission_date, discharge_date
        #     ) VALUES %s
        #     """,
        #     df_final_journal.values.tolist()
        # )
        prod_conn.commit()
        print(f"✅ Done Transaction {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Journal','error')
        print(f"❌ Error Transaction {datetime.now()}: {e}")

    finally:
        prod_cur.close()
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
    create_log('Job Khanza Journal','start')

    # --- SINGLE DATE ---
    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        transactions = get_transactions(khanza_conn, d, use_delta=False)
        journal = get_journal(khanza_conn, d, use_delta=False)
        main(transactions, journal, d.strftime("%Y-%m-%d"))

    # --- RANGE DATE (loop harian) ---
    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            transactions = get_transactions(khanza_conn, dt, use_delta=False)
            journal = get_journal(khanza_conn, dt, use_delta=False)
            main(transactions, journal, dt.strftime("%Y-%m-%d"))

    # --- MONTH MODE ---
    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            transactions = get_transactions(khanza_conn, dt, use_delta=False)
            journal = get_journal(khanza_conn, dt, use_delta=False)
            main(transactions, journal, dt.strftime("%Y-%m-%d"))

    # --- DELTA MODE (NO LOOP) ---
    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        transactions = get_transactions(khanza_conn, delta_dt, use_delta=False)
        journal = get_journal(khanza_conn, delta_dt, use_delta=False)
        main(transactions, journal, f"delta >= {args.delta}")
    
    else:
        delta_dt = get_latest_trx_date()
        transactions = get_transactions(khanza_conn, delta_dt, use_delta=False)
        journal = get_journal(khanza_conn, delta_dt, use_delta=False)
        main(transactions, journal, f"delta >= {delta_dt}")
    
    create_log('Job Khanza Journal','finish')
    mysql_conn.close()


