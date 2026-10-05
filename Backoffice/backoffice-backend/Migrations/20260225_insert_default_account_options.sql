-- ============================================================
-- DEFAULT ACCOUNT OPTIONS
-- Date: 2026-02-25
-- Purpose: Insert all default account options for Settings tab
-- ============================================================

-- ============================================================
-- 0. Ensure unique constraint exists for ON CONFLICT to work
-- ============================================================
ALTER TABLE public.cfg_options
    ADD CONSTRAINT uq_cfg_options_name_branch UNIQUE (option_name, branch_id);

-- ============================================================
-- 1. Update existing options to type DEFAULT_ACCOUNT
-- ============================================================
UPDATE public.cfg_options SET option_type = 'DEFAULT_ACCOUNT' WHERE option_name IN (
    'ACCOUNT_CASH', 'ACCOUNT_BANK', 'ACCOUNT_RECEIVABLE', 'ACCOUNT_PAYABLE',
    'COA_PREFIX_REVENUE_OPD', 'COA_PREFIX_REVENUE_IPD',
    'ACCOUNT_PIUTANG_USAHA_RANAP_BPJS', 'ACCOUNT_PIUTANG_USAHA_RAJAL_BPJS',
    'ACCOUNT_PIUTANG_USAHA_RANAP_NON_BPJS', 'ACCOUNT_PIUTANG_USAHA_RAJAL_NON_BPJS',
    'ACCOUNT_DISCOUNT_AR',
    'ACCOUNT_PPN_MASUKAN', 'ACCOUNT_UTANG_SUPPLIER', 'ACCOUNT_AP_DISCOUNT', 'ACCOUNT_AP_PPH23',
    'ACCOUNT_TUKAR_FAKTUR_CREDIT', 'ACCOUNT_TUKAR_FAKTUR_DEBIT',
    'ACCOUNT_JASA_MEDIS_PRIBADI', 'ACCOUNT_JASA_MEDIS_ASURANSI', 'ACCOUNT_JASA_MEDIS_SELISIH',
    'OPTION_PETTY_CASH_TOPUP_COA', 'OPTION_PETTY_CASH_RECEIVABLE_COA',
    'ACCOUNT_ACCOUNT_CASH_BANK'
);

-- ============================================================
-- 2. Insert new options (hardcoded values that need config)
-- ============================================================

-- Dashboard & Journal: Revenue/Expense prefixes
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('COA_PREFIX_REVENUE', '4', 'DEFAULT_ACCOUNT', 'Prefix COA Pendapatan', 'Prefix kode COA untuk akun pendapatan (startsWith)', 1),
('COA_PREFIX_REVENUE_ALT', '7', 'DEFAULT_ACCOUNT', 'Prefix COA Pendapatan Alternatif', 'Prefix alternatif kode COA pendapatan', 1),
('COA_PREFIX_EXPENSE', '5,6', 'DEFAULT_ACCOUNT', 'Prefix COA Beban', 'Prefix kode COA untuk akun beban (comma-separated)', 1),
('COA_PREFIX_EXPENSE_ALT', '8', 'DEFAULT_ACCOUNT', 'Prefix COA Beban Alternatif', 'Prefix alternatif kode COA beban', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- Cash Flow: Special COA
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('COA_CASHFLOW_SPECIAL', '401101004', 'DEFAULT_ACCOUNT', 'COA Khusus Cash Flow', 'COA yang menggunakan payer_code sebagai filter di laporan arus kas', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- Payer codes
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('PAYER_CODE_BPJS', 'BPJ', 'DEFAULT_ACCOUNT', 'Kode Payer BPJS', 'Kode payer untuk identifikasi BPJS', 1),
('PAYER_CODE_PRIVATE', 'Prb', 'DEFAULT_ACCOUNT', 'Kode Payer Pribadi', 'Kode payer untuk identifikasi pasien pribadi/umum', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- Clinic codes
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('CLINIC_CODE_IGD', 'IGDK', 'DEFAULT_ACCOUNT', 'Kode Klinik IGD', 'Kode klinik untuk filter IGD di dashboard', 1),
('CLINIC_CODE_FISIO', 'FISIO', 'DEFAULT_ACCOUNT', 'Kode Klinik Fisioterapi', 'Kode klinik untuk filter fisioterapi di dashboard', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- ============================================================
-- 3. Insert existing options if not yet in DB (safe inserts)
-- ============================================================

-- Dashboard Summary
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('ACCOUNT_CASH', '', 'DEFAULT_ACCOUNT', 'Akun Kas', 'Kode COA kas (comma-separated untuk multiple)', 1),
('ACCOUNT_BANK', '', 'DEFAULT_ACCOUNT', 'Akun Bank', 'Kode COA bank (comma-separated untuk multiple)', 1),
('ACCOUNT_RECEIVABLE', '', 'DEFAULT_ACCOUNT', 'Akun Piutang', 'Kode COA piutang (comma-separated untuk multiple)', 1),
('ACCOUNT_PAYABLE', '', 'DEFAULT_ACCOUNT', 'Akun Hutang', 'Kode COA hutang (comma-separated untuk multiple)', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- Dashboard Prefixes
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('COA_PREFIX_REVENUE_OPD', '401201', 'DEFAULT_ACCOUNT', 'Prefix COA Rawat Jalan', 'Prefix kode COA pendapatan rawat jalan (OPD)', 1),
('COA_PREFIX_REVENUE_IPD', '401101', 'DEFAULT_ACCOUNT', 'Prefix COA Rawat Inap', 'Prefix kode COA pendapatan rawat inap (IPD)', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- AR
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('ACCOUNT_PIUTANG_USAHA_RANAP_BPJS', '103001001', 'DEFAULT_ACCOUNT', 'Piutang Ranap BPJS', 'COA piutang usaha rawat inap BPJS', 1),
('ACCOUNT_PIUTANG_USAHA_RAJAL_BPJS', '103002001', 'DEFAULT_ACCOUNT', 'Piutang Rajal BPJS', 'COA piutang usaha rawat jalan BPJS', 1),
('ACCOUNT_PIUTANG_USAHA_RANAP_NON_BPJS', '103001002', 'DEFAULT_ACCOUNT', 'Piutang Ranap Non-BPJS', 'COA piutang usaha rawat inap non-BPJS', 1),
('ACCOUNT_PIUTANG_USAHA_RAJAL_NON_BPJS', '103002002', 'DEFAULT_ACCOUNT', 'Piutang Rajal Non-BPJS', 'COA piutang usaha rawat jalan non-BPJS', 1),
('ACCOUNT_DISCOUNT_AR', '414000000', 'DEFAULT_ACCOUNT', 'Diskon AR', 'COA diskon piutang usaha', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- AP
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('ACCOUNT_PPN_MASUKAN', '108006', 'DEFAULT_ACCOUNT', 'PPN Masukan', 'COA PPN masukan', 1),
('ACCOUNT_UTANG_SUPPLIER', '201001', 'DEFAULT_ACCOUNT', 'Utang Supplier', 'COA utang supplier', 1),
('ACCOUNT_AP_DISCOUNT', '404001', 'DEFAULT_ACCOUNT', 'Diskon AP', 'COA diskon hutang usaha', 1),
('ACCOUNT_AP_PPH23', '206002', 'DEFAULT_ACCOUNT', 'PPh Pasal 23', 'COA PPh pasal 23', 1),
('ACCOUNT_TUKAR_FAKTUR_CREDIT', '201001', 'DEFAULT_ACCOUNT', 'Tukar Faktur (Credit)', 'COA credit untuk tukar faktur', 1),
('ACCOUNT_TUKAR_FAKTUR_DEBIT', '204099', 'DEFAULT_ACCOUNT', 'Tukar Faktur (Debit)', 'COA debit untuk tukar faktur', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- Medical Fee
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('ACCOUNT_JASA_MEDIS_PRIBADI', '201004', 'DEFAULT_ACCOUNT', 'Jasa Medis Pribadi', 'COA hutang jasa medis pribadi', 1),
('ACCOUNT_JASA_MEDIS_ASURANSI', '201005', 'DEFAULT_ACCOUNT', 'Jasa Medis Asuransi', 'COA hutang jasa medis asuransi', 1),
('ACCOUNT_JASA_MEDIS_SELISIH', '722098', 'DEFAULT_ACCOUNT', 'Jasa Medis Selisih', 'COA selisih jasa medis', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- Petty Cash
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('OPTION_PETTY_CASH_TOPUP_COA', '101004001', 'DEFAULT_ACCOUNT', 'COA TopUp Petty Cash', 'COA sumber default untuk top-up petty cash', 1),
('OPTION_PETTY_CASH_RECEIVABLE_COA', '104003', 'DEFAULT_ACCOUNT', 'COA Piutang Karyawan', 'COA piutang karyawan untuk petty cash', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;

-- General
INSERT INTO public.cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
('ACCOUNT_ACCOUNT_CASH_BANK', '101', 'DEFAULT_ACCOUNT', 'Prefix Kas & Bank', 'Prefix kode COA untuk filter akun kas dan bank', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;
