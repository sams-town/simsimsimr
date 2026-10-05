-- Migration: AP Payment Voucher (Form Maker)
-- Creates tables for saving Bukti Pengeluaran AP

CREATE TABLE IF NOT EXISTS public.trx_ap_payment_voucher (
    id                SERIAL PRIMARY KEY,
    voucher_number    VARCHAR(50) NOT NULL,
    voucher_date      TIMESTAMP NOT NULL,
    payment_type      VARCHAR(10) NOT NULL DEFAULT 'CASH',
    status            VARCHAR(20) NOT NULL DEFAULT 'ACTIVE',
    branch_id         INT NULL,
    created_by        INT NULL,
    created_date      TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_by        INT NULL,
    updated_date      TIMESTAMP NULL
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_ap_payment_voucher_number
    ON public.trx_ap_payment_voucher (voucher_number);

CREATE TABLE IF NOT EXISTS public.trx_ap_payment_voucher_item (
    id                    SERIAL PRIMARY KEY,
    voucher_id            INT NOT NULL REFERENCES public.trx_ap_payment_voucher(id),
    invoice_exchange_id   INT NOT NULL REFERENCES public.trx_invoice_exchange(invoice_exchange_id),
    created_date          TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_ap_pv_item_voucher_id
    ON public.trx_ap_payment_voucher_item (voucher_id);

CREATE UNIQUE INDEX IF NOT EXISTS idx_ap_pv_item_unique
    ON public.trx_ap_payment_voucher_item (voucher_id, invoice_exchange_id);
