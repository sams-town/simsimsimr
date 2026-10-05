-- Add adjustment_history column to trx_account_receivable
-- Stores per-row adjustment audit trail (amount before/after, reason, user, date)
ALTER TABLE public.trx_account_receivable
  ADD COLUMN IF NOT EXISTS adjustment_history JSONB NULL;
