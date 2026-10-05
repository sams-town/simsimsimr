-- Create table for AR Settlement Payments (Partial Payments)
CREATE TABLE public.trx_ar_settlement_payment (
    id SERIAL PRIMARY KEY,
    voucher_number VARCHAR(255) NOT NULL,
    payment_date TIMESTAMP WITH TIME ZONE NOT NULL,
    amount DOUBLE PRECISION NOT NULL,
    discount DOUBLE PRECISION NOT NULL DEFAULT 0,
    coa_code VARCHAR(50),
    coa_name VARCHAR(255),
    payment_ref VARCHAR(255),
    paid_by INTEGER REFERENCES master.mst_users(user_id),
    created_date TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- Index for faster lookup by voucher number
CREATE INDEX idx_ar_settlement_payment_voucher ON public.trx_ar_settlement_payment(voucher_number);
