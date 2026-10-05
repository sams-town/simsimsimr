"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Helper functions for migration scripts
  Created   : 2025
========================================================
"""

import calendar
from datetime import datetime, timedelta
import pandas as pd
from libs import get_production_connection


def date_range(start_date, end_date):
    while start_date <= end_date:
        yield start_date
        start_date += timedelta(days=1)

def date_month(year: int, month: int):
    days = calendar.monthrange(year, month)[1]
    start = datetime(year, month, 1)
    end = datetime(year, month, days)
    return date_range(start, end)


def create_log(operation, status):
    pg_conn = get_production_connection()
    cur = pg_conn.cursor()
    now = datetime.now()
    sql = """ insert into logs.audit_logs (module, operation, status, created_at)
        values ('job', %s, %s, %s);"""
    cur.execute(sql, (operation, status, now, ))
    cur.close()

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