-- Add Petty Cash Receivable COA Option (104003 Piutang Karyawan)
INSERT INTO mst_option (option_name, option_value, description, is_active, created_by, created_date, branch_id)
VALUES ('OPTION_PETTY_CASH_RECEIVABLE_COA', '104003', 'COA untuk Piutang Karyawan saat permintaan kas kecil', true, 1, NOW(), 1)
ON CONFLICT (option_name, branch_id) DO UPDATE SET option_value = EXCLUDED.option_value, description = EXCLUDED.description;
