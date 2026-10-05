-- Add adjustment columns to trx_account_receivable
-- Allows payer-approved amounts to be stored separately from original total_amount
ALTER TABLE public.trx_account_receivable
  ADD COLUMN IF NOT EXISTS adjustment_amount DECIMAL(18, 2) NULL,
  ADD COLUMN IF NOT EXISTS adjustment_date TIMESTAMP WITHOUT TIME ZONE NULL,
  ADD COLUMN IF NOT EXISTS adjustment_notes TEXT NULL;

-- Add COA option for AR adjustment difference (write-off selisih klaim vs approved)
-- Default 414000001 — sesuaikan dengan COA yang ada di sistem
INSERT INTO mst_option (option_name, option_value, branch_id, description)
SELECT 'ACCOUNT_ADJUSTMENT_AR', '722012', branch_id, 'COA untuk selisih adjustment AR (klaim vs disetujui)'
FROM mst_branch
WHERE NOT EXISTS (
    SELECT 1 FROM mst_option WHERE option_name = 'ACCOUNT_ADJUSTMENT_AR' AND mst_option.branch_id = mst_branch.branch_id
);
