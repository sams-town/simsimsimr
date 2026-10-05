-- Add rejected_by and rejected_date columns to trx_petty_cash_topup table
ALTER TABLE public.trx_petty_cash_topup
ADD COLUMN IF NOT EXISTS rejected_by INT NULL,
ADD COLUMN IF NOT EXISTS rejected_date TIMESTAMP WITHOUT TIME ZONE NULL;

-- Add foreign key constraint for rejected_by
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_constraint WHERE conname = 'fk_trx_petty_cash_topup_rejected_by'
    ) THEN
        ALTER TABLE public.trx_petty_cash_topup
        ADD CONSTRAINT fk_trx_petty_cash_topup_rejected_by
        FOREIGN KEY (rejected_by)
        REFERENCES master.mst_user (user_id);
    END IF;
END $$;
