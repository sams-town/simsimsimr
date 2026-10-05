-- Add note_history column to trx_account_receivable
-- Stores per-row free-text note audit trail (note text, user, date).
-- The latest note text is mirrored into the existing `notes` column for quick display.
ALTER TABLE public.trx_account_receivable
  ADD COLUMN IF NOT EXISTS note_history JSONB NULL;
