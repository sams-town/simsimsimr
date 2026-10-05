-- Migration: Create trx_migration_job table for manual Python migration script runs
-- Date: 2026-04-24
-- Purpose: Admin-triggered Khanza migration scripts (run_ar, run_faktur, run_medical_fee, run_purchase_order, run_journal)
--          Backend (Docker) inserts pending jobs; Python worker on host (/root/migration-khanza) polls & executes.

CREATE TABLE IF NOT EXISTS public.trx_migration_job (
    id              SERIAL PRIMARY KEY,
    script_name     VARCHAR(50)  NOT NULL,
    target_date     DATE         NOT NULL,
    update_columns  VARCHAR(20)  NULL,
    status          VARCHAR(20)  NOT NULL DEFAULT 'pending',
    requested_by    INT          NULL,
    requested_by_name VARCHAR(100) NULL,
    requested_at    TIMESTAMP    NOT NULL DEFAULT (NOW() AT TIME ZONE 'UTC'),
    started_at      TIMESTAMP    NULL,
    finished_at     TIMESTAMP    NULL,
    exit_code       INT          NULL,
    command         TEXT         NULL,
    stdout_log      TEXT         NULL,
    stderr_log      TEXT         NULL,
    error_message   VARCHAR(500) NULL,
    worker_host     VARCHAR(100) NULL,
    worker_pid      INT          NULL
);

COMMENT ON TABLE  public.trx_migration_job IS 'Queue + history of manually-triggered Khanza migration script executions';
COMMENT ON COLUMN public.trx_migration_job.script_name    IS 'One of: run_ar, run_faktur, run_medical_fee, run_purchase_order, run_journal';
COMMENT ON COLUMN public.trx_migration_job.target_date    IS 'Value passed to the script --date argument';
COMMENT ON COLUMN public.trx_migration_job.update_columns IS 'NULL = skip existing (default); "all" = pass --update-columns=all';
COMMENT ON COLUMN public.trx_migration_job.status         IS 'pending | running | success | failed';
COMMENT ON COLUMN public.trx_migration_job.command        IS 'Exact shell command assembled by the worker';
COMMENT ON COLUMN public.trx_migration_job.worker_host    IS 'Hostname of the machine that executed the job';

-- Indexes for queue lookup & listing
CREATE INDEX IF NOT EXISTS idx_migration_job_status
    ON public.trx_migration_job(status);

CREATE INDEX IF NOT EXISTS idx_migration_job_requested_at
    ON public.trx_migration_job(requested_at DESC);

-- Enforce: only one active (pending OR running) job per script_name at any time.
-- A second admin clicking Run for the same script while one is live will hit this.
CREATE UNIQUE INDEX IF NOT EXISTS uidx_migration_job_active_script
    ON public.trx_migration_job(script_name)
    WHERE status IN ('pending', 'running');

-- =====================================================
-- Role page access registration
-- =====================================================
-- Register the new page so it shows up in the role-management UI.
INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES ('CfgMigrationJob', 'Migrasi Data Khanza', 'Settings', 'Create,Read')
ON CONFLICT (page_code) DO UPDATE
    SET page = EXCLUDED.page,
        category = EXCLUDED.category,
        "action" = EXCLUDED."action";

-- Administrator (role_id = 1) — only Admin can trigger migration scripts.
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES ('CfgMigrationJob', 'Create,Read', 1)
ON CONFLICT (page_code, "action", role_id) DO NOTHING;
