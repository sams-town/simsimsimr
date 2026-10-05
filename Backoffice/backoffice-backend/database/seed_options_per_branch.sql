-- =============================================================================
-- SQL Script: Seed Options for New Branch
-- =============================================================================
-- Usage: Replace @branch_id with the actual branch ID before executing.
--        e.g., SET @branch_id = 2;
--
-- This script inserts all required configuration options for a new branch.
-- Run this when creating a new outlet/tenant.
-- =============================================================================

-- Set the target branch ID here
-- PostgreSQL: Use a temporary table or DO block
DO $$
DECLARE
    v_branch_id INT := 2;  -- << CHANGE THIS to the new branch ID
BEGIN

-- ============================================================================
-- 1. Account Payable (AP) COA Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('ACCOUNT_PAYABLE', '201001', 'COA', 'Utang Supplier', 'COA untuk hutang supplier (Account Payable)', v_branch_id),
    ('ACCOUNT_UTANG_SUPPLIER', '201001', 'COA', 'Utang Supplier', 'COA utang supplier default', v_branch_id),
    ('ACCOUNT_AP_PPH23', '206002', 'COA', 'PPh 23 Payable', 'COA PPh 23 dalam pembayaran AP', v_branch_id),
    ('ACCOUNT_AP_DISCOUNT', '404001', 'COA', 'Discount/Pendapatan Lain', 'COA untuk diskon dalam pembayaran AP', v_branch_id),
    ('ACCOUNT_PPN_MASUKAN', '108006', 'COA', 'PPN Masukan', 'COA untuk PPN masukan (Input VAT)', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 2. Tukar Faktur (Invoice Exchange) COA Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('ACCOUNT_TUKAR_FAKTUR_DEBIT', '201001', 'COA', 'Tukar Faktur - Debit', 'COA debit untuk tukar faktur (Utang Supplier)', v_branch_id),
    ('ACCOUNT_TUKAR_FAKTUR_CREDIT', '204099', 'COA', 'Tukar Faktur - Credit', 'COA credit untuk tukar faktur (Persediaan Obat)', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 3. Account Receivable (AR) COA Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('ACCOUNT_RECEIVABLE', '103005', 'COA', 'Piutang Usaha', 'COA piutang usaha default', v_branch_id),
    ('ACCOUNT_DISCOUNT_AR', '404001', 'COA', 'Diskon AR', 'COA diskon untuk AR settlement', v_branch_id),
    ('ACCOUNT_PIUTANG_USAHA_RAJAL_BPJS', '103001', 'COA', 'Piutang Rajal BPJS', 'COA piutang rawat jalan BPJS', v_branch_id),
    ('ACCOUNT_PIUTANG_USAHA_RAJAL_NON_BPJS', '103002', 'COA', 'Piutang Rajal Non-BPJS', 'COA piutang rawat jalan non-BPJS', v_branch_id),
    ('ACCOUNT_PIUTANG_USAHA_RANAP_BPJS', '103003', 'COA', 'Piutang Ranap BPJS', 'COA piutang rawat inap BPJS', v_branch_id),
    ('ACCOUNT_PIUTANG_USAHA_RANAP_NON_BPJS', '103004', 'COA', 'Piutang Ranap Non-BPJS', 'COA piutang rawat inap non-BPJS', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 4. Bank & Cash COA Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('ACCOUNT_BANK', '101003', 'COA', 'Bank', 'COA bank default', v_branch_id),
    ('ACCOUNT_CASH', '101001', 'COA', 'Kas', 'COA kas default', v_branch_id),
    ('ACCOUNT_ACCOUNT_CASH_BANK', '101', 'COA', 'Kas & Bank Parent', 'Parent COA untuk kas dan bank', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 5. Medical Fee (Jasa Medis) COA Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('ACCOUNT_JASA_MEDIS_PRIBADI', '201004', 'COA', 'Jasa Medis Pribadi', 'COA hutang jasa medis pribadi', v_branch_id),
    ('ACCOUNT_JASA_MEDIS_ASURANSI', '201005', 'COA', 'Jasa Medis Asuransi', 'COA hutang jasa medis asuransi', v_branch_id),
    ('ACCOUNT_JASA_MEDIS_SELISIH', '722098', 'COA', 'Selisih Jasa Medis', 'COA selisih jasa medis', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 6. Petty Cash Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('PETTY_CASH_MAIN_COA', '101001001', 'COA', 'Petty Cash COA', 'COA utama kas kecil', v_branch_id),
    ('PETTY_CASH_LIMIT_PER_TRX', '5000000', 'CONFIG', 'Limit Per Transaksi', 'Batas maksimum per transaksi kas kecil', v_branch_id),
    ('PETTY_CASH_LIMIT_MONTHLY', '50000000', 'CONFIG', 'Limit Bulanan', 'Batas maksimum bulanan kas kecil', v_branch_id),
    ('OPTION_PETTY_CASH_TOPUP_COA', '101004001', 'COA', 'Petty Cash Topup COA', 'COA untuk topup kas kecil', v_branch_id),
    ('OPTION_PETTY_CASH_RECEIVABLE_COA', '104003', 'COA', 'Piutang Karyawan', 'COA piutang karyawan dari kas kecil', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 7. Revenue COA Prefix Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('COA_PREFIX_REVENUE', '401', 'CONFIG', 'Prefix Pendapatan', 'Prefix COA pendapatan', v_branch_id),
    ('COA_PREFIX_REVENUE_ALT', '4', 'CONFIG', 'Prefix Pendapatan Alt', 'Prefix alternatif COA pendapatan', v_branch_id),
    ('COA_PREFIX_REVENUE_OPD', '401201', 'CONFIG', 'Prefix Pendapatan Rajal', 'Prefix COA pendapatan rawat jalan', v_branch_id),
    ('COA_PREFIX_REVENUE_IPD', '401101', 'CONFIG', 'Prefix Pendapatan Ranap', 'Prefix COA pendapatan rawat inap', v_branch_id),
    ('COA_PREFIX_EXPENSE', '5', 'CONFIG', 'Prefix Beban', 'Prefix COA beban/biaya', v_branch_id),
    ('COA_PREFIX_EXPENSE_ALT', '6', 'CONFIG', 'Prefix Beban Alt', 'Prefix alternatif COA beban', v_branch_id),
    ('COA_CASHFLOW_SPECIAL', '2', 'CONFIG', 'Cashflow Special', 'Prefix COA khusus untuk cashflow', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 8. Report Configuration Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('CFG_REPORT_BALANCESHEET', '{}', 'REPORT', 'Config Balance Sheet', 'Konfigurasi laporan neraca', v_branch_id),
    ('CFG_REPORT_PROFITLOSS', '{}', 'REPORT', 'Config Laba Rugi', 'Konfigurasi laporan laba rugi', v_branch_id),
    ('CFG_REPORT_CASHFLOW', '{}', 'REPORT', 'Config Cash Flow', 'Konfigurasi laporan arus kas', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 9. Journal Import Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('DEFAULT_JOURNAL_IMPORT_STATUS', 'Published', 'CONFIG', 'Default Journal Import Status', 'Status default saat import jurnal', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 10. General / Hospital Options
-- ============================================================================
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('HOSPITAL_NAME', 'RS. Taman Harapan Baru', 'CONFIG', 'Nama Rumah Sakit', 'Nama rumah sakit untuk cetak slip/resi', v_branch_id),
    ('PAYER_CODE_PRIVATE', 'PRIVATE', 'CONFIG', 'Kode Payer Pribadi', 'Kode payer untuk pasien umum/pribadi', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 11. SMTP Configuration (Email)
-- ============================================================================
-- NOTE: Update the JSON values below with the actual SMTP config for the branch.
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('SMTP_CONFIG', '{"Host":"smtp.gmail.com","Port":587,"Username":"your-email@gmail.com","Password":"your-app-password","FromEmail":"your-email@gmail.com","FromName":"Backoffice","Encryption":"tls"}', 'CONFIG', 'SMTP Configuration', 'Konfigurasi SMTP untuk pengiriman email', v_branch_id)
ON CONFLICT DO NOTHING;

-- ============================================================================
-- 12. Khanza EHR Integration (if applicable)
-- ============================================================================
-- NOTE: Update values below with the actual Khanza DB connection for this branch.
-- Skip this section if the branch does not use Khanza EHR integration.
/*
INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES
    ('KHANZA_DB_HOST', 'localhost', 'KHANZA', 'Khanza DB Host', 'Host database Khanza', v_branch_id),
    ('KHANZA_DB_PORT', '3306', 'KHANZA', 'Khanza DB Port', 'Port database Khanza', v_branch_id),
    ('KHANZA_DB_NAME', 'rsthb', 'KHANZA', 'Khanza DB Name', 'Nama database Khanza', v_branch_id),
    ('KHANZA_DB_USER', 'root', 'KHANZA', 'Khanza DB User', 'Username database Khanza', v_branch_id),
    ('KHANZA_DB_PASS', '', 'KHANZA', 'Khanza DB Password', 'Password database Khanza', v_branch_id)
ON CONFLICT DO NOTHING;
*/

RAISE NOTICE 'Options seeded successfully for branch_id = %', v_branch_id;

END $$;
