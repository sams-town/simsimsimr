-- Insert new options for Hardcoded Refactoring
-- PPh 23 COA for AP
INSERT INTO options (option_name, option_value, description, branch_id)
SELECT 'ACCOUNT_AP_PPH23', '206002', 'Account Code for PPh 23 Payable in AP Payment', 1
WHERE NOT EXISTS (SELECT 1 FROM options WHERE option_name = 'ACCOUNT_AP_PPH23' AND branch_id = 1);

-- Revenue Prefix for Outpatient (Rawat Jalan / Poli)
INSERT INTO options (option_name, option_value, description, branch_id)
SELECT 'COA_PREFIX_REVENUE_OPD', '401201', 'COA Prefix for Outpatient (Rawat Jalan) Revenue', 1
WHERE NOT EXISTS (SELECT 1 FROM options WHERE option_name = 'COA_PREFIX_REVENUE_OPD' AND branch_id = 1);

-- Revenue Prefix for Inpatient (Rawat Inap)
INSERT INTO options (option_name, option_value, description, branch_id)
SELECT 'COA_PREFIX_REVENUE_IPD', '401101', 'COA Prefix for Inpatient (Rawat Inap) Revenue', 1
WHERE NOT EXISTS (SELECT 1 FROM options WHERE option_name = 'COA_PREFIX_REVENUE_IPD' AND branch_id = 1);
