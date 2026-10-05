-- Migration: Add payment fields to trx_invoice_exchange
-- Date: 2024-12-26
-- Purpose: Enable AP payment tracking

-- Add payment columns to trx_invoice_exchange
ALTER TABLE public.trx_invoice_exchange 
ADD COLUMN IF NOT EXISTS payment_status VARCHAR(20) DEFAULT 'Unpaid',
ADD COLUMN IF NOT EXISTS paid_amount DECIMAL(18,2) DEFAULT 0,
ADD COLUMN IF NOT EXISTS payment_date TIMESTAMPTZ,
ADD COLUMN IF NOT EXISTS payment_ref VARCHAR(100),
ADD COLUMN IF NOT EXISTS payment_coa_code VARCHAR(20),
ADD COLUMN IF NOT EXISTS journal_id_payment INT;

-- Create index for payment status queries
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_payment_status 
ON public.trx_invoice_exchange(payment_status);

-- Create index for due date aging queries  
CREATE INDEX IF NOT EXISTS idx_invoice_exchange_due_date 
ON public.trx_invoice_exchange(due_date);

-- Create trx_medical_fee table for Jasa Medis
CREATE TABLE IF NOT EXISTS public.trx_medical_fee (
    medical_fee_id SERIAL PRIMARY KEY,
    doctor_id INT NOT NULL,
    doctor_name VARCHAR(255),
    period_month INT,
    period_year INT,
    patient_type VARCHAR(50), -- Tunai, BPJS, Asuransi
    total_fee DECIMAL(18,2) NOT NULL DEFAULT 0,
    paid_amount DECIMAL(18,2) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'Pending', -- Pending, Paid, Partial
    payment_date TIMESTAMPTZ,
    payment_ref VARCHAR(100),
    payment_coa_code VARCHAR(20),
    journal_id INT,
    branch_id INT,
    cost_center_id INT,
    created_by INT,
    created_date TIMESTAMPTZ DEFAULT NOW(),
    updated_by INT,
    updated_date TIMESTAMPTZ
);

-- Create indexes for medical fee queries
CREATE INDEX IF NOT EXISTS idx_medical_fee_doctor 
ON public.trx_medical_fee(doctor_id);

CREATE INDEX IF NOT EXISTS idx_medical_fee_period 
ON public.trx_medical_fee(period_year, period_month);

CREATE INDEX IF NOT EXISTS idx_medical_fee_status 
ON public.trx_medical_fee(status);

-- Comment the tables
COMMENT ON TABLE public.trx_medical_fee IS 'Tracks doctor medical fee payments (Jasa Medis)';
