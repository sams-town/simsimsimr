-- Add columns to support partial payment and proper accounting in account_receivable
ALTER TABLE trx_account_receivable
ADD COLUMN IF NOT EXISTS discount_percent double precision DEFAULT 0,
ADD COLUMN IF NOT EXISTS discount_amount double precision DEFAULT 0,
ADD COLUMN IF NOT EXISTS outstanding_amount double precision DEFAULT 0,
ADD COLUMN IF NOT EXISTS payment_history jsonb;

-- Initialize outstanding_amount for existing records
-- If status is 'Paid', outstanding is 0.
-- If status is not 'Paid', outstanding is total_amount - paid_amount.
UPDATE trx_account_receivable
SET outstanding_amount = CASE 
    WHEN payment_status = 'Paid' THEN 0
    ELSE total_amount - COALESCE(paid_amount, 0)
END;
