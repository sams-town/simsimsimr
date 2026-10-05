-- Combined / Gabungan AR Payment Voucher
-- A grouping layer ON TOP of the per-payer voucher. Does NOT touch voucher_number
-- of trx_account_receivable and does NOT create journals (grouping & print only).
-- A combined batch may contain patients across multiple payers (insurances).

-- Header
CREATE TABLE IF NOT EXISTS public.trx_ar_combined_voucher (
    id SERIAL PRIMARY KEY,
    batch_number VARCHAR(50) NOT NULL UNIQUE,
    batch_date TIMESTAMP WITH TIME ZONE NOT NULL,
    description VARCHAR(500),
    status VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    branch_id INTEGER,
    created_by INTEGER,
    created_date TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_by INTEGER,
    updated_date TIMESTAMP WITH TIME ZONE
);

-- Items (join to trx_account_receivable). One AR item can belong to at most ONE batch
-- (UNIQUE ar_id) to prevent double billing.
CREATE TABLE IF NOT EXISTS public.trx_ar_combined_voucher_item (
    id SERIAL PRIMARY KEY,
    combined_id INTEGER NOT NULL REFERENCES public.trx_ar_combined_voucher(id) ON DELETE CASCADE,
    ar_id INTEGER NOT NULL UNIQUE REFERENCES public.trx_account_receivable(ar_id) ON DELETE CASCADE,
    created_date TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_ar_combined_voucher_batch ON public.trx_ar_combined_voucher(batch_number);
CREATE INDEX IF NOT EXISTS idx_ar_combined_voucher_item_combined ON public.trx_ar_combined_voucher_item(combined_id);
CREATE INDEX IF NOT EXISTS idx_ar_combined_voucher_item_ar ON public.trx_ar_combined_voucher_item(ar_id);
