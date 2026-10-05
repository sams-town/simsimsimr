INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
VALUES ('OPTION_PETTY_CASH_TOPUP_COA', '101004001', 'PettyCash', 'TopUp Default Source COA', 'Default Source COA for Petty Cash TopUp (Role 5)', 1)
ON CONFLICT (option_name, branch_id) DO NOTHING;
