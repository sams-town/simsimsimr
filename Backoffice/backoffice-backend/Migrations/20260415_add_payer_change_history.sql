-- Migration: Add payer_change_history column to trx_account_receivable
-- Date: 2026-04-15

ALTER TABLE trx_account_receivable
ADD COLUMN IF NOT EXISTS payer_change_history JSONB DEFAULT NULL;

COMMENT ON COLUMN trx_account_receivable.payer_change_history IS 'JSON array storing payer change history: [{payerBefore, payerAfter, reason, changedBy, changedDate}]';
