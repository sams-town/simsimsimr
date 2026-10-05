DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_schema = 'public' AND table_name = 'trx_account_receivable' AND column_name = 'discount_amount') THEN
        ALTER TABLE public.trx_account_receivable ADD COLUMN discount_amount double precision DEFAULT 0;
    END IF;
END $$;
