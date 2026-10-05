-- Migration: Add payment_batch_number to trx_invoice_exchange
-- Date: 2026-03-17
-- Purpose: Enable batch payment tracking with format APP-yyyy-MM-dd-0001

ALTER TABLE public.trx_invoice_exchange
ADD COLUMN IF NOT EXISTS payment_batch_number VARCHAR(50);

-- Create index for batch payment queries
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_payment_batch_number
ON public.trx_invoice_exchange(payment_batch_number);
