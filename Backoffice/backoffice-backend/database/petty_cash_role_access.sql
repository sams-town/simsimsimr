-- =====================================================
-- Petty Cash Role Page Access
-- Run this script after petty_cash.sql
-- =====================================================

-- 1. Tambahkan Page Codes untuk Petty Cash
INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES('TrxPettyCash', 'Petty Cash Dashboard', 'Transaction', 'Read');
INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES('TrxPettyCashUnit', 'Petty Cash Unit', 'Transaction', 'Create,Read,Update,Delete');
INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES('TrxPettyCashRequest', 'Petty Cash Request', 'Transaction', 'Create,Read,Update,Delete,Submit,Approve,Reject');
INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES('TrxPettyCashSettlement', 'Petty Cash Settlement', 'Transaction', 'Create,Read,Post,Void');
INSERT INTO master.mst_role_page (page_code, page, category, "action")
VALUES('TrxPettyCashTopUp', 'Petty Cash Top Up', 'Transaction', 'Create,Read,Approve,Reject,Void');

-- =====================================================
-- 2. Administrator (role_id=1) - Full access semua
-- =====================================================
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 1);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 1);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Submit,Approve,Reject', 1);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Post,Void', 1);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void', 1);

-- =====================================================
-- 3. Manager (role_id=2) - Manage Unit + Approval
-- =====================================================
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 2);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 2);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Submit,Approve,Reject', 2);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Post,Void', 2);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void', 2);

-- =====================================================
-- 4. Supervisor (role_id=3) - Approval only, no unit management
-- =====================================================
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 3);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Read', 3);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Read,Approve,Reject', 3);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Read,Post', 3);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Read,Approve,Reject', 3);

-- =====================================================
-- 5. Staff (role_id=4) - Create request & settlement only
-- =====================================================
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 4);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Read', 4);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Submit', 4);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read', 4);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read', 4);

-- =====================================================
-- 6. Petty Cash Officer (role_id=5) - Access to Petty Cash module ONLY
--    Also needs access to Master Data for context (COA, etc.)
-- =====================================================

-- First ensure the role "Petty Cash Officer" exists if not already
INSERT INTO master.mst_role (role_id, role_name, branch_id)
OVERRIDING SYSTEM VALUE
VALUES(5, 'Petty Cash Officer', 1)
ON CONFLICT (role_id) DO NOTHING;

-- CLEAR EXISTING PERMISSIONS FOR ROLE 5 TO AVOID DUPLICATES/STALE DATA
DELETE FROM master.mst_role_page_access WHERE role_id = 5;

-- Grant access to Petty Cash modules
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Submit,Approve,Reject', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Post,Void', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void', 5);

-- Grant access to necessary Master Data (Read-only)
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('MasterCOA', 'Read', 5); -- Needed for unit creation & transactions
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('MasterDepartment', 'Read', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('MasterCostCenter', 'Read', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('MasterEmployee', 'Read', 5); -- If employee selection is needed

-- Grant access to Dashboard & Settings (Standard for all users)
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('Dashboard', 'Read', 5);
INSERT INTO master.mst_role_page_access (page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 5);
