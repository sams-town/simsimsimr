-- Migration: Extend logs.audit_logs to serve as the transaction activity/timeline log
-- Date: 2026-07-17
-- Purpose: One append-only event log powering both the per-transaction Riwayat timeline
--          (Supervisor/Manager/Administrator) and the central Audit Trail tab (Administrator).
--          The table already existed but was only written by External API / Import Journal /
--          Transaction create, had varchar payload columns, and had no index beyond the PK.

-- =====================================================
-- 1. Payload columns: varchar -> jsonb
-- =====================================================
-- Existing rows hold Newtonsoft-serialized objects, so they cast cleanly. Rows that are
-- not valid JSON would abort the cast; the guard below converts them to NULL instead of
-- failing the whole migration.
DO $$
BEGIN
    IF EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'logs' AND table_name = 'audit_logs'
          AND column_name = 'before_data' AND data_type <> 'jsonb'
    ) THEN
        UPDATE logs.audit_logs
           SET before_data = NULL
         WHERE before_data IS NOT NULL
           AND NOT (before_data ~ '^\s*[\[{]');

        ALTER TABLE logs.audit_logs
            ALTER COLUMN before_data TYPE jsonb USING before_data::jsonb;
    END IF;

    IF EXISTS (
        SELECT 1 FROM information_schema.columns
        WHERE table_schema = 'logs' AND table_name = 'audit_logs'
          AND column_name = 'after_data' AND data_type <> 'jsonb'
    ) THEN
        UPDATE logs.audit_logs
           SET after_data = NULL
         WHERE after_data IS NOT NULL
           AND NOT (after_data ~ '^\s*[\[{]');

        ALTER TABLE logs.audit_logs
            ALTER COLUMN after_data TYPE jsonb USING after_data::jsonb;
    END IF;
END $$;

-- =====================================================
-- 2. created_at default
-- =====================================================
-- The column is `timestamp` (no tz) and the app always writes DateTime.UtcNow explicitly,
-- but the old DEFAULT now() yields server-local time. Align the default so any row that
-- slips through without an explicit value is still UTC.
ALTER TABLE logs.audit_logs
    ALTER COLUMN created_at SET DEFAULT (NOW() AT TIME ZONE 'UTC');

-- =====================================================
-- 3. Indexes
-- =====================================================
-- Timeline drawer: WHERE module = ? AND ref_id = ? ORDER BY created_at DESC
CREATE INDEX IF NOT EXISTS idx_audit_logs_module_ref
    ON logs.audit_logs(module, ref_id, created_at DESC);

-- Audit Trail tab default listing: ORDER BY created_at DESC
CREATE INDEX IF NOT EXISTS idx_audit_logs_created_at
    ON logs.audit_logs(created_at DESC);

-- Audit Trail tab filters
CREATE INDEX IF NOT EXISTS idx_audit_logs_user_id
    ON logs.audit_logs(user_id);

CREATE INDEX IF NOT EXISTS idx_audit_logs_operation
    ON logs.audit_logs(operation);

CREATE INDEX IF NOT EXISTS idx_audit_logs_status
    ON logs.audit_logs(status);

COMMENT ON TABLE  logs.audit_logs           IS 'Append-only activity log: one row per business action. Powers the Riwayat timeline and the Audit Trail tab.';
COMMENT ON COLUMN logs.audit_logs.module    IS 'Business module, e.g. Jasa Medis, Piutang AR, Tukar Faktur, Petty Cash Request';
COMMENT ON COLUMN logs.audit_logs.ref_id    IS 'Primary key of the affected record as text (medical_fee_id, ar_id, ...), or a voucher/batch number';
COMMENT ON COLUMN logs.audit_logs.operation IS 'Action label shown on the timeline, e.g. Posting, Batal Posting, Pembayaran, Disetujui';
COMMENT ON COLUMN logs.audit_logs.user_id   IS 'master.mst_users.user_id of the actor. NULL when the action had no authenticated user.';
COMMENT ON COLUMN logs.audit_logs.status    IS 'Success | Failed | Backfill | (External API two-phase: Received -> Inserted)';
COMMENT ON COLUMN logs.audit_logs.after_data IS 'Compact action detail payload rendered in the Audit Trail detail popup';

-- =====================================================
-- 4. Role page registration
-- =====================================================
-- NOTE: deliberately NOT using ON CONFLICT here. master.mst_role_page has no unique
-- constraint on page_code at all, and master.mst_role_page_access is unique on
-- (role_id, page_code) -- not (page_code, action, role_id). An ON CONFLICT clause naming
-- those columns aborts with "no unique or exclusion constraint matching". WHERE NOT EXISTS
-- is idempotent regardless of which constraints a given environment actually has.
INSERT INTO master.mst_role_page (page_code, page, category, "action")
SELECT 'CfgAuditTrail', 'Audit Trail', 'Settings', 'Read'
WHERE NOT EXISTS (
    SELECT 1 FROM master.mst_role_page WHERE page_code = 'CfgAuditTrail'
);

-- Administrator (role_id = 1) only.
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
SELECT 'CfgAuditTrail', 'Read', 1
WHERE NOT EXISTS (
    SELECT 1 FROM master.mst_role_page_access
     WHERE page_code = 'CfgAuditTrail' AND role_id = 1
);
