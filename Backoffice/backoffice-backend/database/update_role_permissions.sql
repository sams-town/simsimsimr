-- SQL Update Script for Staff, Supervisor, and Manager Permissions
-- Fixed version: Using DELETE then INSERT to avoid ON CONFLICT constraint error.
-- Standardizing TrxBank actions to include Create,Update,Delete.

BEGIN;

-- 0. Standardize TrxBank actions in master definition
UPDATE master.mst_role_page 
SET "action" = 'Create,Read,Update,Delete,SubmitPayment,SubmitDeposit,SubmitReconcile,Export'
WHERE page_code = 'TrxBank';

-- 1. Staff (Role 4) Permissions
-- Remove existing matching entries to avoid duplicates/conflicts
DELETE FROM master.mst_role_page_access 
WHERE role_id = 4 
AND page_code IN (
    'MasterInsurance', 'MasterCurrency', 'MasterCompany', 'MasterBranch', 
    'MasterAccountGroup', 'MasterAccountType', 'MasterContact', 'MasterProvince', 
    'MasterCity', 'MasterBank', 'MasterDepartment', 'MasterCostCenter', 
    'MasterContactCategory', 'MasterUser', 'Report', 'TrxJournal', 
    'TrxTransaction', 'TrxBank', 'TrxAccountPayable', 'TrxAccountReceivable', 
    'TrxMedicalFee', 'TrxPurchaseOrder', 'TrxPettyCash', 'TrxPettyCashRequest',
    'TrxPettyCashSettlement', 'TrxPettyCashTopUp', 'TrxPettyCashUnit'
);

-- Insert new permissions
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('MasterInsurance', 'Read', 4),
('MasterCurrency', 'Read', 4),
('MasterCompany', 'Read', 4),
('MasterBranch', 'Read', 4),
('MasterAccountGroup', 'Read', 4),
('MasterAccountType', 'Read', 4),
('MasterContact', 'Read', 4),
('MasterProvince', 'Read', 4),
('MasterCity', 'Read', 4),
('MasterBank', 'Read', 4),
('MasterDepartment', 'Read', 4),
('MasterCostCenter', 'Read', 4),
('MasterContactCategory', 'Read', 4),
('MasterUser', 'Read', 4),
('Report', 'Read,Export', 4),
('TrxJournal', 'Create,Read,Update,Delete,Export', 4),
('TrxTransaction', 'Create,Read,Update,Delete,Export', 4),
('TrxBank', 'Create,Read,Update,Delete,Export', 4),
('TrxAccountPayable', 'Create,Read,Update,Delete,Export', 4),
('TrxAccountReceivable', 'Create,Read,Update,Delete,Export', 4),
('TrxMedicalFee', 'Create,Read,Update,Delete,Export', 4),
('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export', 4),
('TrxPettyCash', 'Read,Export', 4),
('TrxPettyCashRequest', 'Create,Read,Update,Delete,Submit,Export', 4),
('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Submit,Export', 4),
('TrxPettyCashTopUp', 'Create,Read,Update,Delete,Submit,Export', 4);


-- 2. Supervisor (Role 3) Permissions
DELETE FROM master.mst_role_page_access 
WHERE role_id = 3 
AND page_code IN (
    'MasterInsurance', 'MasterCurrency', 'MasterCompany', 'MasterBranch', 
    'MasterAccountGroup', 'MasterAccountType', 'MasterContact', 'MasterProvince', 
    'MasterCity', 'MasterBank', 'MasterDepartment', 'MasterCostCenter', 
    'MasterContactCategory', 'MasterUser', 'Report', 'TrxBank',
    'TrxJournal', 'TrxTransaction', 'TrxAccountPayable', 'TrxAccountReceivable',
    'TrxMedicalFee', 'TrxPurchaseOrder'
);

INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('MasterInsurance', 'Read', 3),
('MasterCurrency', 'Read', 3),
('MasterCompany', 'Read', 3),
('MasterBranch', 'Read', 3),
('MasterAccountGroup', 'Read', 3),
('MasterAccountType', 'Read', 3),
('MasterContact', 'Read', 3),
('MasterProvince', 'Read', 3),
('MasterCity', 'Read', 3),
('MasterBank', 'Read', 3),
('MasterDepartment', 'Read', 3),
('MasterCostCenter', 'Read', 3),
('MasterContactCategory', 'Read', 3),
('MasterUser', 'Read', 3),
('Report', 'Read,Export', 3),
('TrxBank', 'Create,Read,Update,Delete,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 3),
('TrxJournal', 'Create,Read,Update,Delete,Approve,Export', 3),
('TrxTransaction', 'Create,Read,Update,Delete,Approve,Export', 3),
('TrxAccountPayable', 'Create,Read,Update,Delete,Approve,Export', 3),
('TrxAccountReceivable', 'Create,Read,Update,Delete,Approve,Export', 3),
('TrxMedicalFee', 'Create,Read,Update,Delete,Approve,Export', 3),
('TrxPurchaseOrder', 'Create,Read,Update,Delete,Approve,Export', 3);


-- 3. Manager (Role 2) Permissions
DELETE FROM master.mst_role_page_access 
WHERE role_id = 2 AND page_code IN ('MasterUser', 'Report');

INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('MasterUser', 'Read', 2),
('Report', 'Read,Export', 2);

COMMIT;
