-- Migration: Bank Form Voucher (Form Maker Penerimaan & Pengeluaran)
-- Groups bank-book transactions (trx_transaction) into printable Bukti vouchers.
-- voucher_type discriminates: 'PENERIMAAN' (Deposit) or 'PENGELUARAN' (Payment).

CREATE TABLE IF NOT EXISTS public.trx_bank_form_voucher (
    id                SERIAL PRIMARY KEY,
    voucher_number    VARCHAR(50) NOT NULL,
    voucher_date      TIMESTAMP NOT NULL,
    voucher_type      VARCHAR(20) NOT NULL DEFAULT 'PENERIMAAN',
    payment_type      VARCHAR(10) NOT NULL DEFAULT 'CASH',
    status            VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    person_name       VARCHAR(150) NULL,
    coa_code          VARCHAR(50) NULL,
    coa_name          VARCHAR(200) NULL,
    amount            NUMERIC(18,2) NOT NULL DEFAULT 0,
    discount_percent  NUMERIC(9,4) NOT NULL DEFAULT 0,
    discount_amount   NUMERIC(18,2) NOT NULL DEFAULT 0,
    pph23_enabled     BOOLEAN NOT NULL DEFAULT FALSE,
    pph23_percent     NUMERIC(9,4) NOT NULL DEFAULT 0,
    pph23_amount      NUMERIC(18,2) NOT NULL DEFAULT 0,
    purpose           TEXT NULL,
    total_amount      NUMERIC(18,2) NOT NULL DEFAULT 0,
    notes             TEXT NULL,
    recipient_bank_name      VARCHAR(100) NULL,
    recipient_account_name   VARCHAR(150) NULL,
    recipient_account_number VARCHAR(50) NULL,
    branch_id         INT NULL,
    created_by        INT NULL,
    created_date      TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_by        INT NULL,
    updated_date      TIMESTAMP NULL
);

-- Add input columns if the table pre-existed (idempotent).
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS person_name      VARCHAR(150) NULL;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS coa_code         VARCHAR(50) NULL;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS coa_name         VARCHAR(200) NULL;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS amount           NUMERIC(18,2) NOT NULL DEFAULT 0;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS discount_percent NUMERIC(9,4) NOT NULL DEFAULT 0;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS discount_amount  NUMERIC(18,2) NOT NULL DEFAULT 0;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS pph23_enabled    BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS pph23_percent    NUMERIC(9,4) NOT NULL DEFAULT 0;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS pph23_amount     NUMERIC(18,2) NOT NULL DEFAULT 0;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS purpose          TEXT NULL;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS total_amount     NUMERIC(18,2) NOT NULL DEFAULT 0;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS recipient_bank_name      VARCHAR(100) NULL;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS recipient_account_name   VARCHAR(150) NULL;
ALTER TABLE public.trx_bank_form_voucher ADD COLUMN IF NOT EXISTS recipient_account_number VARCHAR(50) NULL;

CREATE UNIQUE INDEX IF NOT EXISTS idx_bank_form_voucher_number
    ON public.trx_bank_form_voucher (voucher_number);

CREATE INDEX IF NOT EXISTS idx_bank_form_voucher_type
    ON public.trx_bank_form_voucher (voucher_type, status);
