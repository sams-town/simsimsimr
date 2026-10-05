-- Insert AutoNumber configuration for Petty Cash transactions
-- Assumes Branch ID 1 exists. If multiple branches exist, this needs to be duplicated for each branch or adjusted.

INSERT INTO public.cfg_auto_number (code, name, prefix, last_code, branch_id)
SELECT 'PettyCashTopUp', 'Petty Cash Top Up Transaction', 'TRX-PCT-', 'TRX-PCT-00000', 1
WHERE NOT EXISTS (SELECT 1 FROM public.cfg_auto_number WHERE code = 'PettyCashTopUp' AND branch_id = 1);

INSERT INTO public.cfg_auto_number (code, name, prefix, last_code, branch_id)
SELECT 'PettyCashSettlement', 'Petty Cash Settlement Transaction', 'TRX-PCS-', 'TRX-PCS-00000', 1
WHERE NOT EXISTS (SELECT 1 FROM public.cfg_auto_number WHERE code = 'PettyCashSettlement' AND branch_id = 1);
INSERT INTO public.cfg_auto_number (code, name, prefix, last_code, branch_id)
SELECT 'PettyCashRequest', 'Petty Cash Request Transaction', 'TRX-PCR-', 'TRX-PCR-00000', 1
WHERE NOT EXISTS (SELECT 1 FROM public.cfg_auto_number WHERE code = 'PettyCashRequest' AND branch_id = 1);
