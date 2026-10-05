-- =====================================================
-- Dummy Data for Petty Cash Units
-- Run this script to populate default/test units
-- =====================================================

INSERT INTO public.mst_petty_cash_unit (
    unit_code, 
    unit_name, 
    coa_code, 
    max_limit_per_transaction, 
    max_limit_per_period, 
    period_type, 
    current_balance, 
    is_active, 
    branch_id,
    created_by,
    created_date
) VALUES 
(
    'PC-HQ-01', 
    'Kas Kecil Operasional Pusat', 
    '1101001', -- Sesuaikan dengan Kode Admin/Kas di mst_coa
    1000000.00, 
    10000000.00, 
    'monthly', 
    5000000.00, 
    true, 
    1,
    1,
    NOW()
),
(
    'PC-MKT-01', 
    'Kas Kecil Marketing', 
    '1101002', 
    500000.00, 
    5000000.00, 
    'monthly', 
    2500000.00, 
    true, 
    1,
    1,
    NOW()
),
(
    'PC-IT-01', 
    'Kas Kecil IT & Support', 
    '1101003', 
    2000000.00, 
    15000000.00, 
    'monthly', 
    750000.00, 
    true, 
    1,
    1,
    NOW()
);

-- =====================================================
-- Optional: Dummy Data for Requests if needed
-- =====================================================
/*
INSERT INTO public.trx_petty_cash_request (
    request_no,
    request_date,
    unit_id,
    requester_name,
    purpose,
    amount,
    expense_coa_code,
    status,
    notes,
    created_by,
    created_date
) VALUES
(
    'PCR/2024/02/001',
    NOW(),
    1, -- Use actual unit_id
    'Budi Santoso',
    'Pembelian ATK',
    150000,
    '6101001',
    'Submitted',
    'Segera dibutuhkan',
    1,
    NOW()
);
*/
