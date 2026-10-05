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
# CONFIG UPDATE
# ================================
ALL_UPDATE_COLUMNS_TRX = [
    "transaction_date","transaction_ref","coa_code","coa_name",
    "status","branch_id","created_by","amount","posted_date","journal_no"
]

ALL_UPDATE_COLUMNS_JOURNAL = [
    "journal_desc","journal_ref","coa_code","coa_name",
    "status","branch_id","created_by","debit_amount","credit_amount",
    "clinic_code","clinic_name","ward_code","ward_name","care_type",
    "admission_date","discharge_date","doctor_code","doctor_name",
    "medical_record_number","patient_name","payer_code","payer_name"
]


def get_transactions(conn, date, use_delta=False):
  date_str = date.strftime("'%Y-%m-%d'")
  if use_delta:
      where_clause = f"WHERE timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal) >= '{date}'"
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

def get_latest_trx_date():
    pg_conn = get_production_connection()
    cur = pg_conn.cursor()
    sql = f"SELECT max(transaction_date) as date FROM public.trx_transaction WHERE transaction_name = 'KhanzaJournal' "
    cur.execute(sql)
    row = cur.fetchone()
    cur.close()
    if row:
        return row[0]
    
    return None

def get_journal(conn,date, use_delta=False):
    date_str = date.strftime("'%Y-%m-%d'")
    if use_delta:
        where_clause = f"WHERE timestamp(jurnal.tgl_jurnal, jurnal.jam_jurnal) >= '{date}'"
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
                datasuplier.rekening,
                datasuplier.kode_suplier
            FROM pemesanan
            JOIN datasuplier ON pemesanan.kode_suplier = datasuplier.kode_suplier
            JOIN bangsal ON pemesanan.kd_bangsal = bangsal.kd_bangsal
            LEFT JOIN bayar_pemesanan ON bayar_pemesanan.no_faktur = pemesanan.no_faktur
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
                
            union all
            
            SELECT
                pemesanan.no_faktur,
                pemesanan.no_order,
                datasuplier.nama_suplier,
                pemesanan.tgl_tempo,
                pemesanan.tgl_pesan,
                pemesanan.tgl_faktur,
                null as kd_bangsal,
                null as nm_bangsal,
                pemesanan.tagihan,
                COALESCE(SUM(bayar_pemesanan.besar_bayar), 0) AS bayar,
                datasuplier.nama_bank,
                datasuplier.rekening,
                datasuplier.kode_suplier
            FROM ipsrspemesanan pemesanan
            JOIN ipsrssuplier datasuplier ON pemesanan.kode_suplier = datasuplier.kode_suplier
            LEFT JOIN bayar_pemesanan ON bayar_pemesanan.no_faktur = pemesanan.no_faktur
            GROUP BY
                pemesanan.no_faktur,
                pemesanan.no_order,
                datasuplier.nama_suplier,
                pemesanan.tgl_tempo,
                pemesanan.tgl_pesan,
                pemesanan.tgl_faktur,
                pemesanan.tagihan,
                datasuplier.nama_bank,
                datasuplier.rekening
            
            union all
            
            SELECT
                pemesanan.no_faktur,
                pemesanan.no_order,
                datasuplier.nama_suplier,
                pemesanan.tgl_tempo,
                pemesanan.tgl_pesan,
                pemesanan.tgl_faktur,
                null as kd_bangsal,
                null as nm_bangsal,
                pemesanan.tagihan,
                COALESCE(SUM(bayar_pemesanan.besar_bayar), 0) AS bayar,
                datasuplier.nama_bank,
                datasuplier.rekening,
                datasuplier.kode_suplier
            FROM dapurpemesanan pemesanan
            JOIN dapursuplier datasuplier ON pemesanan.kode_suplier = datasuplier.kode_suplier
            LEFT JOIN bayar_pemesanan ON bayar_pemesanan.no_faktur = pemesanan.no_faktur
            GROUP BY
                pemesanan.no_faktur,
                pemesanan.no_order,
                datasuplier.nama_suplier,
                pemesanan.tgl_tempo,
                pemesanan.tgl_pesan,
                pemesanan.tgl_faktur,
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
            pemesanan.kode_suplier AS kode_suplier,

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
        LEFT JOIN (
            SELECT
                no_rawat,
                tgl_masuk,
                jam_masuk,
                tgl_keluar,
                jam_keluar
            FROM (
                SELECT
                    no_rawat,
                    tgl_masuk,
                    jam_masuk,
                    tgl_keluar,
                    jam_keluar,
                    ROW_NUMBER() OVER (
                        PARTITION BY no_rawat
                        ORDER BY tgl_masuk DESC, jam_masuk DESC
                    ) AS rn
                FROM kamar_inap
            ) t
            WHERE rn = 1
        ) kamar_inap on reg_periksa.no_rawat = kamar_inap.no_rawat
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
        # Query database for last number.
        # journal_no bertipe teks, jadi ORDER BY journal_no DESC salah begitu nomor
        # tembus 4 digit: 'JRN/2026/08/9999' > 'JRN/2026/08/10050' secara teks, sehingga
        # counter mentok dan tiap run mengulang dari 10000. Ambil max secara numerik.
        cur = pg_conn.cursor()
        start = len(prefix) + 1
        query = """
            SELECT COALESCE(MAX(SUBSTRING(journal_no FROM %s::int)::bigint), 0)
            FROM trx_transaction
            WHERE journal_no LIKE %s
              AND SUBSTRING(journal_no FROM %s::int) ~ '^[0-9]+$'
        """
        cur.execute(query, (start, f"{prefix}%", start))
        row = cur.fetchone()
        cur.close()

        next_no = (row[0] if row and row[0] is not None else 0) + 1
        tracking_dict[prefix] = next_no
    
    return f"{prefix}{next_no:04d}"

def get_existing_transaction_no(prod_conn):
    cur = prod_conn.cursor()
    cur.execute("""
        SELECT transaction_no 
        FROM trx_transaction 
        WHERE transaction_name = 'KhanzaJournal'
    """)
    result = set(r[0] for r in cur.fetchall())
    cur.close()
    return result

# ================================
# MAIN
# ================================
def main(transactions, journal, date_label, update_cols_trx, update_cols_journal):

    print(f"\n🔸 Processing Transaction {datetime.now()}: {date_label}")

    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    if transactions.empty or journal.empty:
        print("⚪ No data. Skip.")
        return

    transactions["transaction_id"] = [str(uuid.uuid4()) for _ in range(len(transactions))]
    transactions["transaction_no"] = transactions["no_journal"]
    transactions["transaction_ref"] = transactions["no_bukti"]
    transactions["created_date"] = datetime.utcnow()

    journal = journal.merge(
        transactions[["no_journal", "transaction_id"]],
        left_on="no_jurnal",
        right_on="no_journal",
        how="left"
    )
    journal["created_date"] = datetime.utcnow()

    df_trx = pd.DataFrame({
        "transaction_id": transactions["transaction_id"],
        "transaction_date": transactions["Tanggal"],
        "transaction_type": "KhanzaJournal",
        "transaction_no": transactions["transaction_no"],
        "transaction_ref": transactions["transaction_ref"],
        "transaction_name": "KhanzaJournal",
        "description": None,
        "coa_code": "0",
        "coa_name": "0",
        "status": "Published",
        "branch_id": 1,
        "created_by": 1,
        "amount": None,
        "created_date": transactions["created_date"],
        "posted_date": transactions["Tanggal"],
        "journal_no": None
    })

    df_journal = pd.DataFrame({
        "journal_desc": journal["keterangan"],
        "transaction_id": journal["transaction_id"],
        "journal_ref": journal["kode_akun"],
        "coa_code": journal["kode_akun"],
        "coa_name": journal["nama_akun"],
        "status": "Published",
        "branch_id": 1,
        "created_by": 1,
        "created_date": journal["created_date"],
        "debit_amount": journal["debet"],
        "credit_amount": journal["kredit"],
        "clinic_code": journal["kode_poli"],
        "clinic_name": journal["nama_poli"],
        "ward_code": journal["kode_bangsal"],
        "ward_name": journal["nama_bangsal"],
        "care_type": journal["status_lanjutan"],
        "admission_date": journal["tgl_masuk"],
        "discharge_date": journal["tgl_keluar"],
        "doctor_code": journal["kode_dokter"],
        "doctor_name": journal["nama_dokter"],
        "medical_record_number": journal["no_rekam_medis"],
        "patient_name": journal["nama_pasien"],
        "payer_code": journal["kode_penjab"],
        "payer_name": journal["nama_penjab"],
    })

    existing = get_existing_transaction_no(prod_conn)

    df_insert = df_trx[~df_trx["transaction_no"].isin(existing)]
    df_update = df_trx[df_trx["transaction_no"].isin(existing)]

    try:
        # INSERT TRX
        if not df_insert.empty:
            execute_values(
                cur,
                """
                INSERT INTO trx_transaction (
                    transaction_id, transaction_date, transaction_type,
                    transaction_no, transaction_ref, transaction_name,
                    description, coa_code, coa_name,
                    status, branch_id, created_by,
                    amount, created_date, posted_date, journal_no
                ) VALUES %s
                """,
                df_insert.values.tolist()
            )

        # UPDATE TRX
        if not df_update.empty and update_cols_trx:
            value_cols = ["transaction_no"] + update_cols_trx
            set_clause = ", ".join([f"{c}=v.{c}" for c in update_cols_trx])

            sql = f"""
            UPDATE trx_transaction t
            SET {set_clause}
            FROM (VALUES %s) AS v({", ".join(value_cols)})
            WHERE t.transaction_no = v.transaction_no
            """
            execute_values(cur, sql, df_update[value_cols].values.tolist())

        # INSERT JOURNAL
        if not df_journal.empty:
            execute_values(
                cur,
                """
                INSERT INTO trx_journal (
                    journal_desc, transaction_id, journal_ref,
                    coa_code, coa_name, status,
                    branch_id, created_by, created_date,
                    debit_amount, credit_amount,
                    clinic_code, clinic_name, ward_code, ward_name, care_type,
                    admission_date, discharge_date,
                    doctor_code, doctor_name,
                    medical_record_number, patient_name,
                    payer_code, payer_name
                ) VALUES %s
                """,
                df_journal.values.tolist()
            )

        # UPDATE JOURNAL
        if update_cols_journal:
            value_cols = ["transaction_id"] + update_cols_journal
            set_clause = ", ".join([f"{c}=v.{c}" for c in update_cols_journal])

            sql = f"""
            UPDATE trx_journal t
            SET {set_clause}
            FROM (VALUES %s) AS v({", ".join(value_cols)})
            WHERE t.transaction_id = v.transaction_id
            """
            execute_values(cur, sql, df_journal[value_cols].values.tolist())

        prod_conn.commit()
        print(f"✅ Done Transaction {datetime.now()}")

    except Exception as e:
        prod_conn.rollback()
        create_log('Job Khanza Journal','error')
        print(f"❌ Error: {e}")
        traceback.print_exc()

    finally:
        cur.close()
        prod_conn.close()


# ================================
# RUNNER
# ================================
if __name__ == "__main__":

    parser = argparse.ArgumentParser(description="Journal Runner")

    group = parser.add_mutually_exclusive_group(required=False)
    group.add_argument("--date")
    group.add_argument("--range", nargs=2)
    group.add_argument("--month", nargs=2)
    group.add_argument("--delta")

    parser.add_argument("--update-columns-transaction", nargs="+", choices=["all"] + ALL_UPDATE_COLUMNS_TRX)
    parser.add_argument("--update-columns-jurnal", nargs="+", choices=["all"] + ALL_UPDATE_COLUMNS_JOURNAL)

    args = parser.parse_args()

    if args.update_columns_transaction and "all" in args.update_columns_transaction:
        update_cols_trx = ALL_UPDATE_COLUMNS_TRX
    else:
        update_cols_trx = args.update_columns_transaction or []

    if args.update_columns_jurnal and "all" in args.update_columns_jurnal:
        update_cols_journal = ALL_UPDATE_COLUMNS_JOURNAL
    else:
        update_cols_journal = args.update_columns_jurnal or []

    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Journal','start')

    if args.date:
        d = datetime.strptime(args.date, "%Y-%m-%d")
        main(get_transactions(mysql_conn, d), get_journal(mysql_conn, d), d, update_cols_trx, update_cols_journal)

    elif args.range:
        start = datetime.strptime(args.range[0], "%Y-%m-%d")
        end = datetime.strptime(args.range[1], "%Y-%m-%d")
        for dt in date_range(start, end):
            main(get_transactions(mysql_conn, dt), get_journal(mysql_conn, dt), dt, update_cols_trx, update_cols_journal)

    elif args.month:
        year = int(args.month[0])
        month = int(args.month[1])
        for dt in date_month(year, month):
            main(get_transactions(mysql_conn, dt), get_journal(mysql_conn, dt), dt, update_cols_trx, update_cols_journal)

    elif args.delta:
        delta_dt = datetime.strptime(args.delta, "%Y-%m-%d %H:%M:%S")
        main(get_transactions(mysql_conn, delta_dt, True), get_journal(mysql_conn, delta_dt, True), delta_dt, update_cols_trx, update_cols_journal)

    create_log('Job Khanza Journal','finish')
    mysql_conn.close()