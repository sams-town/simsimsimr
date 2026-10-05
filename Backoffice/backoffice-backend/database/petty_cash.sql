-- =====================================================
-- PETTY CASH MODULE - DATABASE SCHEMA
-- Created: 2026-02-04
-- =====================================================

-- Master Unit Kas Kecil
CREATE TABLE IF NOT EXISTS public.mst_petty_cash_unit (
    unit_id SERIAL PRIMARY KEY,
    unit_code VARCHAR(20) NOT NULL UNIQUE,
    unit_name VARCHAR(100) NOT NULL,
    coa_code VARCHAR(20) NOT NULL,                -- Link ke mst_coa (e.g., 101001002)
    max_limit_per_transaction DECIMAL(18,2),
    max_limit_per_period DECIMAL(18,2) DEFAULT 0,
    period_type VARCHAR(20) DEFAULT 'monthly',    -- daily, weekly, monthly
    current_balance DECIMAL(18,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT true,
    branch_id INT,
    created_by INT,
    created_date TIMESTAMP DEFAULT NOW(),
    updated_by INT,
    updated_date TIMESTAMP
);

-- Transaksi Permintaan Kas Kecil
CREATE TABLE IF NOT EXISTS public.trx_petty_cash_request (
    request_id SERIAL PRIMARY KEY,
    request_no VARCHAR(50) NOT NULL UNIQUE,
    request_date TIMESTAMP NOT NULL,
    unit_id INT NOT NULL,
    requester_name VARCHAR(100) NOT NULL,
    purpose VARCHAR(500) NOT NULL,
    amount DECIMAL(18,2) NOT NULL,
    expense_coa_code VARCHAR(20),                 -- COA Beban (e.g., 501005 - ATK)
    status VARCHAR(20) DEFAULT 'Draft',           -- Draft, Submitted, Approved, Rejected, Settled, Closed
    approved_by INT,
    approved_date TIMESTAMP,
    approved_amount DECIMAL(18,2),
    rejected_reason VARCHAR(500),
    settlement_id INT,
    notes VARCHAR(500),
    branch_id INT,
    created_by INT,
    created_date TIMESTAMP DEFAULT NOW(),
    updated_by INT,
    updated_date TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES public.mst_petty_cash_unit(unit_id)
);

-- Settlement/Realisasi Kas Kecil (menghasilkan jurnal otomatis)
CREATE TABLE IF NOT EXISTS public.trx_petty_cash_settlement (
    settlement_id SERIAL PRIMARY KEY,
    settlement_no VARCHAR(50) NOT NULL UNIQUE,
    settlement_date TIMESTAMP NOT NULL,
    unit_id INT NOT NULL,
    total_amount DECIMAL(18,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',         -- Pending, Posted
    transaction_id VARCHAR(50),                   -- Link ke trx_transaction (Jurnal)
    notes VARCHAR(500),
    branch_id INT,
    created_by INT,
    created_date TIMESTAMP DEFAULT NOW(),
    updated_by INT,
    updated_date TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES public.mst_petty_cash_unit(unit_id)
);

-- Detail Settlement (multi-expense)
CREATE TABLE IF NOT EXISTS public.trx_petty_cash_settlement_detail (
    detail_id SERIAL PRIMARY KEY,
    settlement_id INT NOT NULL,
    request_id INT,                               -- Link ke request (optional)
    expense_coa_code VARCHAR(20) NOT NULL,
    expense_coa_name VARCHAR(100),
    description VARCHAR(500),
    amount DECIMAL(18,2) NOT NULL,
    receipt_no VARCHAR(50),
    receipt_date TIMESTAMP,
    FOREIGN KEY (settlement_id) REFERENCES public.trx_petty_cash_settlement(settlement_id),
    FOREIGN KEY (request_id) REFERENCES public.trx_petty_cash_request(request_id)
);

-- Top Up Kas Kecil (menghasilkan jurnal otomatis)
CREATE TABLE IF NOT EXISTS public.trx_petty_cash_topup (
    topup_id SERIAL PRIMARY KEY,
    topup_no VARCHAR(50) NOT NULL UNIQUE,
    topup_date TIMESTAMP NOT NULL,
    unit_id INT NOT NULL,
    from_coa_code VARCHAR(20) NOT NULL,           -- COA Sumber (Bank/Kas Induk)
    from_coa_name VARCHAR(100),
    amount DECIMAL(18,2) NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',         -- Pending, Approved, Rejected, Posted
    approved_by INT,
    approved_date TIMESTAMP,
    rejected_reason VARCHAR(500),
    transaction_id VARCHAR(50),                   -- Link ke trx_transaction (Jurnal)
    notes VARCHAR(500),
    branch_id INT,
    created_by INT,
    created_date TIMESTAMP DEFAULT NOW(),
    updated_by INT,
    updated_date TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES public.mst_petty_cash_unit(unit_id)
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_pc_request_unit ON public.trx_petty_cash_request(unit_id);
CREATE INDEX IF NOT EXISTS idx_pc_request_status ON public.trx_petty_cash_request(status);
CREATE INDEX IF NOT EXISTS idx_pc_request_date ON public.trx_petty_cash_request(request_date);
CREATE INDEX IF NOT EXISTS idx_pc_settlement_unit ON public.trx_petty_cash_settlement(unit_id);
CREATE INDEX IF NOT EXISTS idx_pc_settlement_date ON public.trx_petty_cash_settlement(settlement_date);
CREATE INDEX IF NOT EXISTS idx_pc_topup_unit ON public.trx_petty_cash_topup(unit_id);
CREATE INDEX IF NOT EXISTS idx_pc_topup_date ON public.trx_petty_cash_topup(topup_date);

-- =====================================================
-- DEFAULT OPTIONS FOR PETTY CASH
-- =====================================================

INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id) VALUES
('PETTY_CASH_MAIN_COA', '101001001', 'petty_cash', 'COA Kas Induk', 'Kode akun kas induk untuk top up petty cash', 1),
('PETTY_CASH_LIMIT_PER_TRX', '5000000', 'petty_cash', 'Limit Per Transaksi', 'Maksimum nominal per transaksi petty cash (dalam Rupiah)', 1),
('PETTY_CASH_LIMIT_MONTHLY', '50000000', 'petty_cash', 'Limit Per Bulan', 'Maksimum total transaksi petty cash per bulan (dalam Rupiah)', 1)
ON CONFLICT DO NOTHING;

-- =====================================================
-- ROLE: PETTY CASH CASHIER
-- =====================================================

-- Insert Role Petty Cash Cashier (adjust branch_id as needed)
DO $$
DECLARE
    new_role_id INT;
BEGIN
    -- Check if role already exists
    IF NOT EXISTS (SELECT 1 FROM master.mst_role WHERE role_name = 'Petty Cash Cashier') THEN
        INSERT INTO master.mst_role (role_name, branch_id) 
        VALUES ('Petty Cash Cashier', 1)
        RETURNING role_id INTO new_role_id;
        
        -- Insert role page access
        INSERT INTO master.mst_role_page_access (role_id, page_code, action) VALUES
        -- Dashboard
        (new_role_id, 'TrxPettyCash', 'Read'),
        -- Unit (view only)
        (new_role_id, 'TrxPettyCashUnit', 'Read'),
        -- Request (create, update, view)
        (new_role_id, 'TrxPettyCashRequest', 'Read'),
        (new_role_id, 'TrxPettyCashRequest', 'Create'),
        (new_role_id, 'TrxPettyCashRequest', 'Update'),
        -- Settlement (create, view)
        (new_role_id, 'TrxPettyCashSettlement', 'Read'),
        (new_role_id, 'TrxPettyCashSettlement', 'Create'),
        -- TopUp (create request only, no approve)
        (new_role_id, 'TrxPettyCashTopUp', 'Read'),
        (new_role_id, 'TrxPettyCashTopUp', 'Create');
        
        RAISE NOTICE 'Petty Cash Cashier role created with ID: %', new_role_id;
    ELSE
        RAISE NOTICE 'Petty Cash Cashier role already exists';
    END IF;
END $$;

-- =====================================================
-- SAMPLE DATA (Optional - for testing)
-- =====================================================

-- Sample Petty Cash Units (uncomment to use)
/*
INSERT INTO public.mst_petty_cash_unit (unit_code, unit_name, coa_code, max_limit_per_transaction, max_limit_per_period, current_balance, branch_id, created_by, created_date) VALUES
('PC-GZ', 'Kas Kecil Dapur Gizi', '101001002', 2000000, 20000000, 5000000, 1, 1, NOW()),
('PC-KD', 'Kas Kecil Kendaraan', '101001003', 3000000, 30000000, 8000000, 1, 1, NOW()),
('PC-PR', 'Kas Kecil Personalia', '101001004', 2500000, 25000000, 6000000, 1, 1, NOW());
*/
