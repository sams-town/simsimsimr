-- Migration: Jasa Medis Enhancement
-- Date: 2025-01-09
-- Purpose: Add fields for batch posting, payment voucher, and payer tracking

-- Add new columns to trx_medical_fee
ALTER TABLE public.trx_medical_fee
ADD COLUMN IF NOT EXISTS batch_number VARCHAR(20),
ADD COLUMN IF NOT EXISTS payment_voucher VARCHAR(30),
ADD COLUMN IF NOT EXISTS doctor_share DECIMAL(5,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS procedure_date DATE,
ADD COLUMN IF NOT EXISTS procedure_name VARCHAR(255),
ADD COLUMN IF NOT EXISTS payer_code VARCHAR(50),
ADD COLUMN IF NOT EXISTS payer_name VARCHAR(255);

-- Create indexes for new fields
CREATE INDEX IF NOT EXISTS idx_medical_fee_batch ON public.trx_medical_fee(batch_number);
CREATE INDEX IF NOT EXISTS idx_medical_fee_voucher ON public.trx_medical_fee(payment_voucher);
CREATE INDEX IF NOT EXISTS idx_medical_fee_payer ON public.trx_medical_fee(payer_code);
CREATE INDEX IF NOT EXISTS idx_medical_fee_procedure_date ON public.trx_medical_fee(procedure_date);

-- Update existing records to have proper status values (normalize)
UPDATE public.trx_medical_fee SET status = 'Pending' WHERE status IS NULL OR status = '';

-- Add comments
COMMENT ON COLUMN public.trx_medical_fee.batch_number IS 'Batch number for grouped posting (format: YYYYMMDD-XXXXX)';
COMMENT ON COLUMN public.trx_medical_fee.payment_voucher IS 'Payment voucher number (format: VP/YYYY/MM/DD/NNNN)';
COMMENT ON COLUMN public.trx_medical_fee.doctor_share IS 'Doctor share percentage (0-100)';
COMMENT ON COLUMN public.trx_medical_fee.procedure_date IS 'Date of medical procedure';
COMMENT ON COLUMN public.trx_medical_fee.payer_code IS 'Payer reference code from mst_contact.ref_code';
COMMENT ON COLUMN public.trx_medical_fee.payer_name IS 'Payer name from mst_contact.contact_name';
