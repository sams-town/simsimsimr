-- Migration: Create trx_journal_import_job — background queue for General Journal imports
-- Date: 2026-07-18
-- Purpose: Import Jurnal Umum moves from a synchronous request to a queue. The user uploads
--          one or more Excel files from /jurnal-umum/import; each file becomes one row here.
--          A Quartz worker in the backend claims rows one at a time and processes them; the
--          frontend polls every 10s. Each row keeps its own summary so the history page can
--          show what succeeded and what failed per file.

CREATE TABLE IF NOT EXISTS public.trx_journal_import_job (
    id                   SERIAL PRIMARY KEY,
    file_name            VARCHAR(255) NOT NULL,
    stored_path          VARCHAR(500) NOT NULL,
    file_size            BIGINT       NULL,
    import_type          VARCHAR(20)  NOT NULL,
    status               VARCHAR(20)  NOT NULL DEFAULT 'pending',
    branch_id            INT          NULL,
    requested_by         INT          NULL,
    requested_by_name    VARCHAR(100) NULL,
    requested_at         TIMESTAMP    NOT NULL DEFAULT (NOW() AT TIME ZONE 'UTC'),
    started_at           TIMESTAMP    NULL,
    finished_at          TIMESTAMP    NULL,
    total_rows           INT          NULL,
    journal_count        INT          NULL,
    error_count          INT          NULL,
    errors               JSONB        NULL,
    created_transactions JSONB        NULL,
    error_message        VARCHAR(1000) NULL,
    worker_host          VARCHAR(100) NULL
);

COMMENT ON TABLE  public.trx_journal_import_job IS 'Queue + history of General Journal Excel imports. One row per uploaded file.';
COMMENT ON COLUMN public.trx_journal_import_job.file_name   IS 'Original name as uploaded; shown in the history list and used for the download filename';
COMMENT ON COLUMN public.trx_journal_import_job.stored_path IS 'Path relative to the app root, under the mounted Uploads volume, e.g. Uploads/journal-import/2026/07/<ticks>_<name>.xlsx';
COMMENT ON COLUMN public.trx_journal_import_job.import_type IS 'standard = 6-column template | thb = 18-column THB layout';
COMMENT ON COLUMN public.trx_journal_import_job.status      IS 'pending | running | success | failed -- same vocabulary as trx_migration_job';
COMMENT ON COLUMN public.trx_journal_import_job.total_rows  IS 'Data rows read from the sheet (excludes headers)';
COMMENT ON COLUMN public.trx_journal_import_job.journal_count IS 'Transactions created on success';
COMMENT ON COLUMN public.trx_journal_import_job.errors      IS 'Validation errors as [{row, field, message}]';
COMMENT ON COLUMN public.trx_journal_import_job.error_message IS 'Fatal error (unreadable file, worker crash) -- distinct from row-level validation errors';

-- Queue claim: WHERE status = 'pending' ORDER BY id
CREATE INDEX IF NOT EXISTS idx_journal_import_job_status
    ON public.trx_journal_import_job(status);

-- History listing: ORDER BY requested_at DESC
CREATE INDEX IF NOT EXISTS idx_journal_import_job_requested_at
    ON public.trx_journal_import_job(requested_at DESC);

-- NOTE: deliberately NO unique constraint here. trx_migration_job has a partial unique index
-- allowing only one active job per script; this queue is the opposite -- queueing several
-- files at once, including the same file twice, is the feature.
