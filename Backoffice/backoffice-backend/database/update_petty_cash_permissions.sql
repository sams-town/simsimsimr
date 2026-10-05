-- Updated permissions for Petty Cash Top Up
-- Ensure Export is available as an action for the page
UPDATE master.mst_role_page
SET "action" = 'Create,Read,Approve,Reject,Void,Export'
WHERE page_code = 'TrxPettyCashTopUp';

-- Grant permissions to Roles 1, 2, 3, 4 (Admin, Manager, etc.)
-- They get full access: Create, Read, Approve, Reject, Void, Export
DELETE FROM master.mst_role_page_access 
WHERE page_code = 'TrxPettyCashTopUp' AND role_id IN (1, 2, 3, 4);

INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES ('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 1);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES ('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 2);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES ('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 3);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES ('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 4);

-- Grant permissions to Role 5 (Petty Cash Staff)
-- Restricted access: Create, Read, Export (NO Approve, NO Reject, NO Void)
DELETE FROM master.mst_role_page_access 
WHERE page_code = 'TrxPettyCashTopUp' AND role_id = 5;

INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES ('TrxPettyCashTopUp', 'Create,Read,Export', 5);
