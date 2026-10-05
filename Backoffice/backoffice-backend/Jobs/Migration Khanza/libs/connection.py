"""
========================================================
  Script by : Tegar
  Project   : SIM RS Migration
  Purpose   : Database connection and configuration
  Created   : 2025
========================================================
"""

import mysql.connector
import psycopg2

KHANZA_CONFIG = {
    "host": "localhost",
    "user": "khanza_user",
    "password": "CHANGE_ME",
    "database": "khanza_db"
}

PRODCUTION_CONFIG = {
    "host": "localhost",
    "user": "postgres",
    "password": "CHANGE_ME",
    "dbname": "backoffice_db",
}
def get_khanza_connection():
    return mysql.connector.connect(**KHANZA_CONFIG)

def get_production_connection():
    return psycopg2.connect(**PRODCUTION_CONFIG)

