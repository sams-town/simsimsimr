-- =====================================================
-- SQL Script: Tukar Faktur (Invoice Exchange) COA Options
-- Date: 2026-01-27
-- Description: Add COA configuration options for journal creation when exchanging invoices
-- =====================================================

-- Option 1: COA untuk debit (Utang Supplier - reduce liability when invoice is verified)
-- Default: 201001 - Utang Supplier
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES (
    'ACCOUNT_TUKAR_FAKTUR_DEBIT',
    '201001',
    'COA',
    'Akun Debit Tukar Faktur',
    'Kode akun COA yang akan di-debit saat proses tukar faktur. Default: Utang Supplier (201001)',
    1
);

-- Option 2: COA untuk credit (Persediaan/Inventory - recognize inventory received)
-- Default: 105001 - Persediaan Obat
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES (
    'ACCOUNT_TUKAR_FAKTUR_CREDIT',
    '105001',
    'COA',
    'Akun Credit Tukar Faktur',
    'Kode akun COA yang akan di-credit saat proses tukar faktur. Default: Persediaan Obat (105001)',
    1
);

-- =====================================================
-- Verification Query (Optional - run after INSERT)
-- =====================================================
-- SELECT * FROM public.cfg_options WHERE option_name LIKE 'ACCOUNT_TUKAR_FAKTUR%';
