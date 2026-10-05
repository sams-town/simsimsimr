-- Add request_id to settlement header (1:1 relationship with Request)
ALTER TABLE trx_petty_cash_settlement 
ADD COLUMN IF NOT EXISTS request_id INT REFERENCES trx_petty_cash_request(request_id);

-- Create index for performance
CREATE INDEX IF NOT EXISTS idx_settlement_request_id ON trx_petty_cash_settlement(request_id);

-- Note: Keeping request_id in trx_petty_cash_settlement_detail for backward compatibility
-- The detail table now serves as COA breakdown (1 settlement = many COAs)
