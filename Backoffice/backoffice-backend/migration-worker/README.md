# Migration Worker (host-side)

Agen kecil yang mengeksekusi Khanza migration script (`run_ar.py`, `run_faktur.py`, `run_medical_fee.py`, `run_purchase_order.py`, `run_journal.py`) sebagai reaksi dari job yang di-queue oleh backend lewat tabel `public.trx_migration_job`.

Backend (di Docker) tidak bisa menjalankan Python script di host, jadi pola-nya: backend INSERT baris status=`pending`, worker ini POLL tabel, kerjain, update hasilnya.

## Prasyarat

- Python 3 + `psycopg2` terinstal di venv yang dipakai script (biasanya `/root/migration-khanza/venv`).
- Migrasi SQL `20260424_create_migration_job.sql` sudah diaplikasikan ke database `RSSM_BACKOFFICE_2025`.
- Script-script migrasi ada di `/root/migration-khanza/script/`.

```bash
source /root/migration-khanza/venv/bin/activate
pip install psycopg2-binary
```

## Install sebagai systemd service

```bash
# 1) Copy worker script ke folder yang sama dengan venv (atau path lain asal konsisten dengan unit file).
cp worker.py /root/migration-khanza/worker.py
chmod +x /root/migration-khanza/worker.py

# 2) Siapkan environment file (berisi DSN DB).
cp migration-worker.env.example /etc/migration-worker.env
chmod 600 /etc/migration-worker.env
#   → edit /etc/migration-worker.env, set MIGRATION_DB_DSN yang benar.

# 3) Install unit file.
cp migration-worker.service /etc/systemd/system/migration-worker.service

# 4) Enable + start.
systemctl daemon-reload
systemctl enable --now migration-worker
systemctl status migration-worker
journalctl -u migration-worker -f        # tail log live
```

## Menjalankan manual (tanpa systemd, untuk testing)

```bash
export MIGRATION_DB_DSN="host=... port=5432 dbname=RSSM_BACKOFFICE_2025 user=root password=..."
/root/migration-khanza/venv/bin/python /root/migration-khanza/worker.py
```

## Environment variables

| Var | Default | Keterangan |
|---|---|---|
| `MIGRATION_DB_DSN` | *(wajib)* | libpq DSN ke DB Postgres backoffice |
| `MIGRATION_SCRIPT_DIR` | `/root/migration-khanza/script` | Folder berisi `run_*.py` |
| `MIGRATION_PYTHON_BIN` | `/root/migration-khanza/venv/bin/python` | Python interpreter untuk subprocess |
| `MIGRATION_WORKING_DIR` | `/root/migration-khanza` | CWD subprocess |
| `MIGRATION_POLL_SECONDS` | `5` | Interval polling |
| `MIGRATION_JOB_TIMEOUT` | `21600` | Timeout tiap job (detik). Default 6 jam. |
| `MIGRATION_MAX_LOG_CHARS` | `500000` | Potong log bila melebihi ini (ambil ekor) |

## Kontrak dengan backend

- Hanya mengeksekusi `script_name` yang ada di whitelist (`run_ar`, `run_faktur`, `run_medical_fee`, `run_purchase_order`, `run_journal`). Harus **sama persis** dengan `_allowedScripts` di [`MigrationJobService.cs`](../Services/MigrationJob/MigrationJobService.cs).
- Claim job pakai `FOR UPDATE SKIP LOCKED` — aman di-scale horizontal, tapi dalam praktiknya 1 worker sudah cukup.
- Partial unique index `uidx_migration_job_active_script` di DB menjamin hanya 1 job per `script_name` yang bisa `pending`/`running` sekaligus — backend tidak akan pernah menaruh dua.
- Job **tidak bisa dicancel**. Ini sesuai requirement; kalau butuh stop, kill service → worker exit, tapi subprocess yang sedang jalan akan tetap jalan sampai selesai kecuali kamu `kill` PID-nya manual.

## Log output di UI

Kolom `stdout_log` dan `stderr_log` di-truncate ke `MIGRATION_MAX_LOG_CHARS` karakter terakhir (default 500KB). Cukup untuk review; kalau butuh full log, cek di host: `journalctl` atau log asli dari script migrasi.
