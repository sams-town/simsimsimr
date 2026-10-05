#!/usr/bin/env python3
"""Migration job worker.

Polls public.trx_migration_job for rows with status='pending', claims one at a
time (FOR UPDATE SKIP LOCKED), executes the matching Khanza migration script
inside the repo's virtualenv, and writes the result (stdout/stderr/exit_code)
back to the same row.

Deployed on the host machine at /root/migration-khanza, separate from the
Docker-hosted backend. The backend only queues rows; this process does the work.
"""

import logging
import os
import re
import socket
import subprocess
import sys
import time
from datetime import datetime, timezone
from pathlib import Path

import psycopg2
import psycopg2.extras

# --- Configuration -----------------------------------------------------------

# Absolute path to the folder that holds run_ar.py, run_faktur.py, etc.
SCRIPT_DIR = Path(os.environ.get("MIGRATION_SCRIPT_DIR", "/root/migration-khanza/script"))

# Python interpreter to use. Default = venv python at /root/migration-khanza/venv/bin/python.
PYTHON_BIN = os.environ.get("MIGRATION_PYTHON_BIN", "/root/migration-khanza/venv/bin/python")

# Working directory for the subprocess (so relative paths like `script/run_ar.py` resolve).
WORKING_DIR = Path(os.environ.get("MIGRATION_WORKING_DIR", "/root/migration-khanza"))

# Postgres DSN — must point at the same DB the backend writes to.
DB_DSN = os.environ.get("MIGRATION_DB_DSN")

POLL_INTERVAL_SECONDS = int(os.environ.get("MIGRATION_POLL_SECONDS", "5"))
JOB_TIMEOUT_SECONDS   = int(os.environ.get("MIGRATION_JOB_TIMEOUT", str(60 * 60 * 6)))  # 6 hours default
MAX_LOG_CHARS         = int(os.environ.get("MIGRATION_MAX_LOG_CHARS", "500000"))

# Must match the whitelist in MigrationJobService.cs on the backend.
ALLOWED_SCRIPTS = {
    "run_ar",
    "run_faktur",
    "run_medical_fee",
    "run_purchase_order",
    "run_journal",
}

SCRIPT_NAME_RE = re.compile(r"^[a-z_][a-z0-9_]*$")
WORKER_HOST = socket.gethostname()

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s %(levelname)s %(message)s",
)
log = logging.getLogger("migration-worker")


# --- DB helpers --------------------------------------------------------------

def connect():
    if not DB_DSN:
        log.error("MIGRATION_DB_DSN env var is not set — cannot connect to Postgres")
        sys.exit(2)
    return psycopg2.connect(DB_DSN)


def claim_one_pending_job(conn):
    """Atomically claim the oldest pending job. Returns a dict or None."""
    with conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor) as cur:
        cur.execute("""
            UPDATE public.trx_migration_job
               SET status      = 'running',
                   started_at  = (NOW() AT TIME ZONE 'UTC'),
                   worker_host = %s,
                   worker_pid  = %s
             WHERE id = (
                SELECT id
                  FROM public.trx_migration_job
                 WHERE status = 'pending'
                 ORDER BY id ASC
                 FOR UPDATE SKIP LOCKED
                 LIMIT 1
             )
         RETURNING id, script_name, target_date, update_columns;
        """, (WORKER_HOST, os.getpid()))
        row = cur.fetchone()
    conn.commit()
    return row


def finalize_job(conn, job_id, status, exit_code, stdout, stderr, command, error_message=None):
    stdout_trim = (stdout or "")[-MAX_LOG_CHARS:]
    stderr_trim = (stderr or "")[-MAX_LOG_CHARS:]
    with conn.cursor() as cur:
        cur.execute("""
            UPDATE public.trx_migration_job
               SET status        = %s,
                   finished_at   = (NOW() AT TIME ZONE 'UTC'),
                   exit_code     = %s,
                   stdout_log    = %s,
                   stderr_log    = %s,
                   command       = %s,
                   error_message = %s
             WHERE id = %s;
        """, (status, exit_code, stdout_trim, stderr_trim, command, error_message, job_id))
    conn.commit()


# --- Job execution -----------------------------------------------------------

def build_command(script_name, target_date, update_columns):
    """Return argv list for subprocess. Raises ValueError for invalid input."""
    if script_name not in ALLOWED_SCRIPTS or not SCRIPT_NAME_RE.match(script_name):
        raise ValueError(f"script '{script_name}' is not whitelisted")

    script_path = SCRIPT_DIR / f"{script_name}.py"
    if not script_path.is_file():
        raise ValueError(f"script file not found: {script_path}")

    if isinstance(target_date, datetime):
        date_str = target_date.date().isoformat()
    else:
        date_str = target_date.isoformat()

    argv = [PYTHON_BIN, str(script_path), "--date", date_str]
    if update_columns == "all":
        argv.append("--update-columns=all")
    return argv


def run_job(conn, job):
    job_id         = job["id"]
    script_name    = job["script_name"]
    target_date    = job["target_date"]
    update_columns = job["update_columns"]

    try:
        argv = build_command(script_name, target_date, update_columns)
    except ValueError as ve:
        log.error("job %s invalid: %s", job_id, ve)
        finalize_job(conn, job_id, "failed", None, "", "", "", error_message=str(ve))
        return

    command_str = " ".join(argv)
    log.info("job %s START: %s", job_id, command_str)

    try:
        result = subprocess.run(
            argv,
            cwd=str(WORKING_DIR),
            capture_output=True,
            text=True,
            timeout=JOB_TIMEOUT_SECONDS,
        )
        status = "success" if result.returncode == 0 else "failed"
        err_msg = None if status == "success" else _first_error_line(result.stderr) or f"exit code {result.returncode}"
        finalize_job(
            conn, job_id, status,
            exit_code=result.returncode,
            stdout=result.stdout,
            stderr=result.stderr,
            command=command_str,
            error_message=err_msg,
        )
        log.info("job %s DONE status=%s exit=%s", job_id, status, result.returncode)
    except subprocess.TimeoutExpired as te:
        log.error("job %s TIMEOUT after %ss", job_id, JOB_TIMEOUT_SECONDS)
        finalize_job(
            conn, job_id, "failed",
            exit_code=None,
            stdout=te.stdout or "",
            stderr=(te.stderr or "") + f"\n[worker] timeout after {JOB_TIMEOUT_SECONDS}s",
            command=command_str,
            error_message=f"timeout after {JOB_TIMEOUT_SECONDS}s",
        )
    except Exception as e:
        log.exception("job %s CRASHED", job_id)
        finalize_job(
            conn, job_id, "failed",
            exit_code=None,
            stdout="",
            stderr=str(e),
            command=command_str,
            error_message=f"worker error: {e}",
        )


def _first_error_line(stderr):
    if not stderr:
        return None
    for line in stderr.splitlines()[::-1]:
        s = line.strip()
        if s:
            return s[:500]
    return None


# --- Main loop ---------------------------------------------------------------

def main():
    log.info("migration worker starting: host=%s pid=%s script_dir=%s python=%s",
             WORKER_HOST, os.getpid(), SCRIPT_DIR, PYTHON_BIN)

    conn = None
    while True:
        try:
            if conn is None or conn.closed:
                conn = connect()

            job = claim_one_pending_job(conn)
            if job is None:
                time.sleep(POLL_INTERVAL_SECONDS)
                continue

            run_job(conn, job)
        except (psycopg2.InterfaceError, psycopg2.OperationalError) as db_err:
            log.error("DB connection lost: %s — reconnecting in %ss", db_err, POLL_INTERVAL_SECONDS)
            try:
                if conn is not None:
                    conn.close()
            except Exception:
                pass
            conn = None
            time.sleep(POLL_INTERVAL_SECONDS)
        except KeyboardInterrupt:
            log.info("shutdown requested")
            break
        except Exception:
            log.exception("unexpected worker error — sleeping and continuing")
            time.sleep(POLL_INTERVAL_SECONDS)


if __name__ == "__main__":
    main()

