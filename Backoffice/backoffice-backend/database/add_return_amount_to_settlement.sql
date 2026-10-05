-- Add return_amount to trx_petty_cash_settlement
ALTER TABLE trx_petty_cash_settlement ADD COLUMN IF NOT EXISTS return_amount DECIMAL(18,2) DEFAULT 0;
