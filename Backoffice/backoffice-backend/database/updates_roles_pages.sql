-- ==========================================
-- 1. Ensure Pages Exist (Using WHERE NOT EXISTS to avoid duplicates without unique constraint)
-- ==========================================

INSERT INTO master.mst_role_page (page_code, page, category, "action")
SELECT 'TrxAccountPayable', 'Account Payable', 'Transaction', 'Create,Read,Update,Delete,Export'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role_page WHERE page_code = 'TrxAccountPayable');

INSERT INTO master.mst_role_page (page_code, page, category, "action")
SELECT 'TrxAccountReceivable', 'Account Receivable', 'Transaction', 'Create,Read,Update,Delete,Export'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role_page WHERE page_code = 'TrxAccountReceivable');

INSERT INTO master.mst_role_page (page_code, page, category, "action")
SELECT 'TrxMedicalFee', 'Medical Fee', 'Transaction', 'Create,Read,Update,Delete,Export'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role_page WHERE page_code = 'TrxMedicalFee');

INSERT INTO master.mst_role_page (page_code, page, category, "action")
SELECT 'TrxPurchaseOrder', 'Purchase Order', 'Transaction', 'Create,Read,Update,Delete,Export'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role_page WHERE page_code = 'TrxPurchaseOrder');

INSERT INTO master.mst_role_page (page_code, page, category, "action")
SELECT 'TrxInvoiceExchange', 'Invoice Exchange', 'Transaction', 'Create,Read,Update,Delete,Export'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role_page WHERE page_code = 'TrxInvoiceExchange');


-- ==========================================
-- 2. Grant Permissions
-- Strategy: DELETE existing specific permissions for these pages/roles, then INSERT.
-- This works even without UNIQUE constraints.
-- ==========================================

-- Define the list of pages we are updating
-- Pages: TrxAccountPayable, TrxAccountReceivable, TrxMedicalFee, TrxPurchaseOrder, TrxInvoiceExchange

-- ------------------------------------------
-- ROLE 1: Administrator
-- ------------------------------------------
DELETE FROM master.mst_role_page_access 
WHERE role_id = 1 
  AND page_code IN ('TrxAccountPayable', 'TrxAccountReceivable', 'TrxMedicalFee', 'TrxPurchaseOrder', 'TrxInvoiceExchange');

INSERT INTO master.mst_role_page_access (role_id, page_code, "action") VALUES 
(1, 'TrxAccountPayable', 'Create,Read,Update,Delete,Export'),
(1, 'TrxAccountReceivable', 'Create,Read,Update,Delete,Export'),
(1, 'TrxMedicalFee', 'Create,Read,Update,Delete,Export'),
(1, 'TrxPurchaseOrder', 'Create,Read,Update,Delete,Export'),
(1, 'TrxInvoiceExchange', 'Create,Read,Update,Delete,Export');

-- ------------------------------------------
-- ROLE 2: Manager
-- ------------------------------------------
DELETE FROM master.mst_role_page_access 
WHERE role_id = 2
  AND page_code IN ('TrxAccountPayable', 'TrxAccountReceivable', 'TrxMedicalFee', 'TrxPurchaseOrder', 'TrxInvoiceExchange');

INSERT INTO master.mst_role_page_access (role_id, page_code, "action") VALUES 
(2, 'TrxAccountPayable', 'Create,Read,Update,Delete,Export'),
(2, 'TrxAccountReceivable', 'Create,Read,Update,Delete,Export'),
(2, 'TrxMedicalFee', 'Create,Read,Update,Delete,Export'),
(2, 'TrxPurchaseOrder', 'Create,Read,Update,Delete,Export'),
(2, 'TrxInvoiceExchange', 'Create,Read,Update,Delete,Export');

-- ------------------------------------------
-- ROLE 3: Supervisor
-- ------------------------------------------
DELETE FROM master.mst_role_page_access 
WHERE role_id = 3
  AND page_code IN ('TrxAccountPayable', 'TrxAccountReceivable', 'TrxMedicalFee', 'TrxPurchaseOrder', 'TrxInvoiceExchange');

INSERT INTO master.mst_role_page_access (role_id, page_code, "action") VALUES 
(3, 'TrxAccountPayable', 'Create,Read,Update,Delete,Export'),
(3, 'TrxAccountReceivable', 'Create,Read,Update,Delete,Export'),
(3, 'TrxMedicalFee', 'Create,Read,Update,Delete,Export'),
(3, 'TrxPurchaseOrder', 'Create,Read,Update,Delete,Export'),
(3, 'TrxInvoiceExchange', 'Create,Read,Update,Delete,Export');

-- ------------------------------------------
-- ROLE 4: Staff
-- ------------------------------------------
DELETE FROM master.mst_role_page_access 
WHERE role_id = 4
  AND page_code IN ('TrxAccountPayable', 'TrxAccountReceivable', 'TrxMedicalFee', 'TrxPurchaseOrder', 'TrxInvoiceExchange');

INSERT INTO master.mst_role_page_access (role_id, page_code, "action") VALUES 
(4, 'TrxAccountPayable', 'Read'),
(4, 'TrxAccountReceivable', 'Read'),
(4, 'TrxMedicalFee', 'Read'),
(4, 'TrxPurchaseOrder', 'Read'),
(4, 'TrxInvoiceExchange', 'Create,Read');
