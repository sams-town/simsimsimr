-- Permissions for Petty Cash Request
UPDATE master.mst_role_page
SET "action" = 'Create,Read,Update,Delete,Approve,Reject,Submit,Export'
WHERE page_code = 'TrxPettyCashRequest';

-- Permissions for Petty Cash Settlement
UPDATE master.mst_role_page
SET "action" = 'Create,Read,Update,Delete,Post,Void,Export'
WHERE page_code = 'TrxPettyCashSettlement';

-- Grant Request Export to Admin, Manager (Roles 1-4)
UPDATE master.mst_role_page_access
SET "action" = 'Create,Read,Update,Delete,Approve,Reject,Submit,Export'
WHERE page_code = 'TrxPettyCashRequest' AND role_id IN (1, 2, 3, 4);

-- Grant Settlement Export to Admin, Manager (Roles 1-4)
UPDATE master.mst_role_page_access
SET "action" = 'Create,Read,Update,Delete,Post,Void,Export'
WHERE page_code = 'TrxPettyCashSettlement' AND role_id IN (1, 2, 3, 4);

-- Grant Request Export to Role 5 (Petty Cash Staff)
-- Role 5 usually has: Create, Read, Update, Delete, Submit (No Approve/Reject)
UPDATE master.mst_role_page_access
SET "action" = 'Create,Read,Update,Delete,Submit,Export'
WHERE page_code = 'TrxPettyCashRequest' AND role_id = 5;

-- Grant Settlement Export to Role 5 (Petty Cash Staff)
-- Role 5 usually has: Create, Read, Update, Delete, Post (No Void perhaps?) - Assuming Post is allowed based on previous setup
UPDATE master.mst_role_page_access
SET "action" = 'Create,Read,Update,Delete,Post,Export'
WHERE page_code = 'TrxPettyCashSettlement' AND role_id = 5;
