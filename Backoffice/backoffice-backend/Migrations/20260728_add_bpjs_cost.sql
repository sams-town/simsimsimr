-- Migration: BPJS Cost Master Data
-- Date: 2026-07-28
-- Purpose: Create master table for BPJS procedure cost reference

CREATE TABLE IF NOT EXISTS master.mst_bpjs_cost (
    cost_id SERIAL PRIMARY KEY,
    procedure VARCHAR(255) NOT NULL,
    cost DECIMAL(18, 2) NOT NULL DEFAULT 0,
    branch_id INT
);

COMMENT ON TABLE master.mst_bpjs_cost IS 'Master data BPJS procedure cost reference';
COMMENT ON COLUMN master.mst_bpjs_cost.procedure IS 'Nama prosedur/tindakan BPJS';
COMMENT ON COLUMN master.mst_bpjs_cost.cost IS 'Biaya/honor BPJS';

CREATE UNIQUE INDEX IF NOT EXISTS idx_bpjs_cost_procedure_branch
    ON master.mst_bpjs_cost(LOWER(TRIM(procedure)), branch_id);

-- Seed default role page and access for Administrator (role_id=1) and Manager (role_id=2)
INSERT INTO master.mst_role_page (page_code, page_name, actions)
VALUES ('MasterBpjsCost', 'BPJS Cost', 'Create,Read,Update,Delete,Export')
ON CONFLICT (page_code) DO NOTHING;

INSERT INTO master.mst_role_page_access (role_id, page_code, action)
SELECT role_id, 'MasterBpjsCost', 'Create,Read,Update,Delete,Export'
FROM master.mst_role
WHERE role_id IN (1, 2)
AND NOT EXISTS (
    SELECT 1 FROM master.mst_role_page_access
    WHERE page_code = 'MasterBpjsCost' AND role_id = master.mst_role.role_id
);
