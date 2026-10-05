-- Migration: Create trx_account_receivable table
-- Date: 2024-12-26

CREATE TABLE IF NOT EXISTS trx_account_receivable (
    ar_id SERIAL PRIMARY KEY,
    ar_number VARCHAR(50) UNIQUE,
    
    -- Relation to Journal
    journal_id VARCHAR(100),
    
    -- Payer Info
    payer_type VARCHAR(20) NOT NULL,  -- BPJS, ASR, PRH, DKK, KRY, DKT, PSN
    payer_id VARCHAR(50),
    payer_name VARCHAR(255),
    
    -- Claim Info
    claim_number VARCHAR(50),
    service_type VARCHAR(20),  -- Ranap/Rajal
    coa_code VARCHAR(20),
    
    -- Amount
    transaction_date DATE NOT NULL,
    due_date DATE,
    total_amount DECIMAL(18,2) NOT NULL,
    paid_amount DECIMAL(18,2) DEFAULT 0,
    
    -- Status
    payment_status VARCHAR(20) DEFAULT 'Pending', -- Pending/Partial/Paid
    payment_date DATE,
    payment_ref VARCHAR(100),
    payment_coa_code VARCHAR(20),
    payment_journal_id VARCHAR(100),
    
    -- Reconciliation (BPJS)
    reconcile_status VARCHAR(20),
    reconcile_bpjs_amount DECIMAL(18,2),
    reconcile_difference DECIMAL(18,2),
    reconcile_date DATE,
    
    -- Audit
    notes TEXT,
    branch_id INT,
    created_by INT,
    created_date TIMESTAMP DEFAULT NOW(),
    updated_by INT,
    updated_date TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_ar_payer_type ON trx_account_receivable(payer_type);
CREATE INDEX IF NOT EXISTS idx_ar_payment_status ON trx_account_receivable(payment_status);
CREATE INDEX IF NOT EXISTS idx_ar_journal_id ON trx_account_receivable(journal_id);
CREATE INDEX IF NOT EXISTS idx_ar_transaction_date ON trx_account_receivable(transaction_date);
CREATE INDEX IF NOT EXISTS idx_ar_claim_number ON trx_account_receivable(claim_number);
