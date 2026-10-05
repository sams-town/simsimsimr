-- Add SEP No, Is Printed, and Status columns to Account Receivable table

ALTER TABLE public.trx_account_receivable 
ADD COLUMN IF NOT EXISTS sep_no VARCHAR(50);

ALTER TABLE public.trx_account_receivable 
ADD COLUMN IF NOT EXISTS is_printed BOOLEAN DEFAULT FALSE;

ALTER TABLE public.trx_account_receivable 
ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'ACTIVE';

-- Index for searching by SEP
CREATE INDEX IF NOT EXISTS trx_ar_sep_no_idx ON public.trx_account_receivable (sep_no);
