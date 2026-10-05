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


def get_ar(mysql_conn, dt_value, use_delta=False):

    date_str = dt_value.strftime("'%Y-%m-%d'")

    sql = f"""
        select
            reg_periksa.no_rawat as ar_number,
            penjab.kd_pj as payer_id,
            penjab.nama_perusahaan as payer_name,
            if(penjab.kd_pj = 'BPJ','bpjs','non bpjs') as payer_type,
            reg_periksa.status_lanjut as service_type,
            timestamp(reg_periksa.tgl_registrasi, reg_periksa.jam_reg) as pipeline_date,
            dpp.totalpiutang as total_amount,
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
                pp.no_tagihan,
                pp.status,
                pp.tanggal,
                row_number() over (
                    partition by dpp.no_rawat
                    order by pp.tanggal desc, pp.no_tagihan desc
                ) as rn
            from detail_penagihan_piutang dpp
            join penagihan_piutang pp 
                on pp.no_tagihan = dpp.no_tagihan
        ) pp
        join reg_periksa on reg_periksa.no_rawat = pp.no_rawat
        left join detail_piutang_pasien dpp on dpp.no_rawat = pp.no_rawat 
        left join penjab on dpp.kd_pj = penjab.kd_pj
        left join pasien on pasien.no_rkm_medis = reg_periksa.no_rkm_medis
        left join poliklinik poli on poli.kd_poli = reg_periksa.kd_poli
        left join bridging_sep on reg_periksa.no_rawat = bridging_sep.no_rujukan
        where
            pp.rn = 1
            and penjab.nama_perusahaan not in ('***',' ','-','','Pribadi')
            and  pp.tanggal = {date_str}
        order by ar_number;
    """

    return pd.read_sql_query(sql, mysql_conn)


def get_existing_ar(prod_conn, ar_numbers, payer_ids):

    if len(ar_numbers) == 0:
        return pd.DataFrame()

    sql = """
        SELECT
            ar_number,
            payer_id
        FROM trx_account_receivable
        WHERE (ar_number, payer_id) IN (
            SELECT * FROM unnest(%s, %s)
        )
    """

    return pd.read_sql_query(
        sql,
        prod_conn,
        params=(list(ar_numbers), list(payer_ids))
    )


def main(ar_data):

    print(f"\n🔸 Processing Pipeline Update {datetime.now()}")

    prod_conn = get_production_connection()
    cur = prod_conn.cursor()

    if ar_data.empty:
        print("⚠️ No data from source")
        cur.close()
        prod_conn.close()
        return

    df_pipeline = pd.DataFrame({
        "ar_number": ar_data["ar_number"],
        "payer_id": ar_data["payer_id"],
        "transaction_date": pd.to_datetime(ar_data["transaction_date"], errors="coerce")
    })

    df_pipeline = df_pipeline.where(pd.notna(df_pipeline), None)

    existing = get_existing_ar(
        prod_conn,
        df_pipeline["ar_number"],
        df_pipeline["payer_id"]
    )

    if existing.empty:
        print("⚠️ No matching AR in production")
        cur.close()
        prod_conn.close()
        return

    df_update = df_pipeline.merge(
        existing,
        on=["ar_number", "payer_id"],
        how="inner"
    )

    try:

        if not df_update.empty:

            update_sql = """
            UPDATE trx_account_receivable t
            SET
                transaction_date = v.transaction_date
            FROM (VALUES %s) AS v(
                ar_number,
                payer_id,
                transaction_date
            )
            WHERE
                t.ar_number = v.ar_number
                AND t.payer_id = v.payer_id
            """

            execute_values(
                cur,
                update_sql,
                df_update.values.tolist()
            )

            prod_conn.commit()

        print(f"✅ Updated Pipeline Date : {len(df_update)}")
        print(f"✅ Done Pipeline Update {datetime.now()}")

    except Exception as e:

        prod_conn.rollback()
        create_log('Job Khanza Account Receivable','error')

        print(f"❌ Error : {e}")
        traceback.print_exc()

    finally:

        cur.close()
        prod_conn.close()


if __name__ == "__main__":

    parser = argparse.ArgumentParser(description="Account Receivable Runner")

    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument("--range", nargs=2)

    args = parser.parse_args()

    mysql_conn = get_khanza_connection()
    create_log('Job Khanza Account Receivable','start')

    start = datetime.strptime(args.range[0], "%Y-%m-%d")
    end = datetime.strptime(args.range[1], "%Y-%m-%d")

    for dt in date_range(start, end):
        ar_data = get_ar(mysql_conn, dt)
        main(ar_data)

    mysql_conn.close()

    create_log('Job Khanza Account Receivable','finish')