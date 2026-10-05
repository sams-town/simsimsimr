ALTER TABLE public.trx_petty_cash_topup
ADD COLUMN approved_amount numeric(18,2) DEFAULT 0,
ADD COLUMN approved_notes text;
