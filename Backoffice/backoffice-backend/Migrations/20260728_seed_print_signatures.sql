-- Seed default print signature options for all branches
-- Run this once to set defaults. Settings page will allow overriding.

INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
SELECT
    'PRINT_SIGNER_LEFT_NAME',
    'Naurah Nazhifah Salim',
    'PRINT_SIGNATURES',
    'Nama TTD Kiri',
    'Nama penandatangan bagian kiri dokumen cetak AR',
    branch_id
FROM master.mst_branch
WHERE NOT EXISTS (
    SELECT 1 FROM cfg_options WHERE option_name = 'PRINT_SIGNER_LEFT_NAME' AND cfg_options.branch_id = master.mst_branch.branch_id
);

INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
SELECT
    'PRINT_SIGNER_LEFT_TITLE',
    'Bagian Penagihan',
    'PRINT_SIGNATURES',
    'Jabatan TTD Kiri',
    'Jabatan penandatangan bagian kiri dokumen cetak AR',
    branch_id
FROM master.mst_branch
WHERE NOT EXISTS (
    SELECT 1 FROM cfg_options WHERE option_name = 'PRINT_SIGNER_LEFT_TITLE' AND cfg_options.branch_id = master.mst_branch.branch_id
);

INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
SELECT
    'PRINT_SIGNER_RIGHT_NAME',
    'Herlan Mars, S.Ak',
    'PRINT_SIGNATURES',
    'Nama TTD Kanan',
    'Nama penandatangan bagian kanan dokumen cetak AR',
    branch_id
FROM master.mst_branch
WHERE NOT EXISTS (
    SELECT 1 FROM cfg_options WHERE option_name = 'PRINT_SIGNER_RIGHT_NAME' AND cfg_options.branch_id = master.mst_branch.branch_id
);

INSERT INTO cfg_options (option_name, option_value, option_type, option_label, option_desc, branch_id)
SELECT
    'PRINT_SIGNER_RIGHT_TITLE',
    'Manajer Keu & Akuntansi',
    'PRINT_SIGNATURES',
    'Jabatan TTD Kanan',
    'Jabatan penandatangan bagian kanan dokumen cetak AR',
    branch_id
FROM master.mst_branch
WHERE NOT EXISTS (
    SELECT 1 FROM cfg_options WHERE option_name = 'PRINT_SIGNER_RIGHT_TITLE' AND cfg_options.branch_id = master.mst_branch.branch_id
);
