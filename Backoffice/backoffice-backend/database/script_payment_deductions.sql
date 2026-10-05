-- Add discount and PPH23 columns to trx_invoice_exchange table
ALTER TABLE trx_invoice_exchange ADD COLUMN IF NOT EXISTS discount_percent double precision DEFAULT 0;
ALTER TABLE trx_invoice_exchange ADD COLUMN IF NOT EXISTS discount_amount double precision DEFAULT 0;
ALTER TABLE trx_invoice_exchange ADD COLUMN IF NOT EXISTS pph23_percent double precision DEFAULT 0;
ALTER TABLE trx_invoice_exchange ADD COLUMN IF NOT EXISTS pph23_amount double precision DEFAULT 0;

-- Add outstanding_amount and payment_history columns
ALTER TABLE trx_invoice_exchange ADD COLUMN IF NOT EXISTS outstanding_amount double precision;
ALTER TABLE trx_invoice_exchange ADD COLUMN IF NOT EXISTS payment_history jsonb;

-- Initialize outstanding_amount for existing records
UPDATE trx_invoice_exchange 
SET outstanding_amount = (COALESCE(total_transaction, 0) - COALESCE(paid_amount, 0) - COALESCE(discount_amount, 0) - COALESCE(pph23_amount, 0))
WHERE outstanding_amount IS NULL;
