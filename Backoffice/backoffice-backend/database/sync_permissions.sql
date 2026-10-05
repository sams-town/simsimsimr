-- Comprehensive SQL Sync Script for Roles, Pages, and Permissions
--
-- CAKUPAN: hanya role bawaan 1-5 dan halaman yang terdaftar di bawah.
-- Role kustom (role_id 6+) yang dibuat lewat menu Master Role dan halaman di luar
-- daftar ini TIDAK tersentuh.
--
-- Tanpa TRUNCATE. TRUNCATE ... CASCADE mengosongkan seluruh tabel — termasuk akses
-- milik role kustom — dan ikut menyeret tabel lain yang punya FK ke sini, jadi tidak
-- aman dijalankan di produksi. Sebagai gantinya: role & halaman di-upsert, dan akses
-- hanya dihapus untuk role 1-5 sebelum diisi ulang.
--
-- Urutan mengikuti FK: induk (mst_role, mst_role_page) dulu, baru anak
-- (mst_role_page_access).

BEGIN;

-- 1. Roles (mst_role) — upsert. Jangan DELETE: mst_user dan mst_role_page_access
--    menunjuk ke tabel ini.
INSERT INTO master.mst_role (role_id, role_name, branch_id) VALUES
(1, 'Administrator', 1),
(2, 'Manager', 1),
(3, 'Supervisor', 1),
(4, 'Staff', 1),
(5, 'Patty Cash', 1)
ON CONFLICT (role_id) DO UPDATE
SET role_name = EXCLUDED.role_name,
    branch_id = EXCLUDED.branch_id;

-- 2. Populate Page Definitions — upsert
INSERT INTO master.mst_role_page (page_code, page, category, "action") VALUES
('ExternalAPI', 'External API', 'API', 'SubmitInvoice'),
('TrxJournal', 'Trx Journal', 'Transaction', 'Create,Read,Update,Delete,Export,Approve'),
('CfgOption', 'Option', 'Configuration', 'Create,Read,Update,Delete,Export'),
('CfgGlobalSettings', 'Global Settings', 'Configuration', 'Create,Read,Update,Delete'),
('CfgUserSettings', 'User Settings', 'Configuration', 'Read,Update'),
('Dashboard', 'Dashboard', 'Dashboard', 'Read'),
('StaffDashboard', 'Staff Dashboard', 'Dashboard', 'Read'),
('MasterInsurance', 'Master Insurance', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterUser', 'Master User', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterRole', 'Master Role', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterCurrency', 'Master Currency', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterCompany', 'Master Company', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterBranch', 'Master Branch', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterAccountGroup', 'Master AccountGroup', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterAccountType', 'Master Account Type', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterCOA', 'Master COA', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterContact', 'MasterContact', 'Contact', 'Create,Read,Update,Delete,Export'),
('MasterProvince', 'Master Province', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterCity', 'Master City', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterBank', 'Master Bank', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterDepartment', 'Master Department', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterCostCenter', 'Master Cost Center', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterContactCategory', 'Master Contact Category', 'Master', 'Create,Read,Update,Delete,Export'),
('MasterEmployee', 'Master Employee', 'Master', 'Read'),
('TrxBank', 'Trx Bank', 'Transaction', 'Create,Read,Update,Delete,SubmitPayment,SubmitDeposit,SubmitReconcile,Export'),
('TrxRecurringExpense', 'Trx Recurring', 'Recurring', 'Create,Read,Update,Delete,Export'),
('Report', 'Report', 'Report', 'Read,Export'),
('TrxAccountPayable', 'Account Payable', 'Transaction', 'Create,Read,Update,Delete,Export,Approve'),
('TrxAccountReceivable', 'Account Receivable', 'Transaction', 'Create,Read,Update,Delete,Export,Approve'),
('TrxMedicalFee', 'Medical Fee', 'Transaction', 'Create,Read,Update,Delete,Export,Approve'),
('TrxPurchaseOrder', 'Purchase Order', 'Transaction', 'Create,Read,Update,Delete,Export,Approve'),
('TrxInvoiceExchange', 'Invoice Exchange', 'Transaction', 'Create,Read,Update,Delete,Export'),
('TrxTransaction', 'General Transaction', 'Transaction', 'Create,Read,Update,Delete,Void,Export,Approve'),
('TrxPettyCash', 'Petty Cash Dashboard', 'Transaction', 'Read,Export'),
('TrxPettyCashUnit', 'Petty Cash Unit', 'Transaction', 'Create,Read,Update,Delete'),
('TrxPettyCashTopUp', 'Petty Cash Top Up', 'Transaction', 'Create,Read,Approve,Reject,Void,Export'),
('TrxPettyCashRequest', 'Petty Cash Request', 'Transaction', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export'),
('TrxPettyCashSettlement', 'Petty Cash Settlement', 'Transaction', 'Create,Read,Update,Delete,Post,Void,Export')
ON CONFLICT (page_code) DO UPDATE
SET page       = EXCLUDED.page,
    category   = EXCLUDED.category,
    "action"   = EXCLUDED."action";

-- 3. Reset Role Access — dibatasi role bawaan 1-5, role kustom dibiarkan utuh.
DELETE FROM master.mst_role_page_access WHERE role_id IN (1, 2, 3, 4, 5);

-- 4. Re-populate Role Access
-- Role 1: Administrator (All Permissions)
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('TrxJournal', 'Create,Read,Delete,Export,Approve', 1),
('MasterInsurance', 'Create,Read,Update,Delete,Export', 1),
('TrxTransaction', 'Create,Read,Update,Delete,Void,Export,Approve', 1),
('MasterUser', 'Create,Read,Update,Delete,Export', 1),
('MasterRole', 'Create,Read,Update,Delete,Export', 1),
('MasterCurrency', 'Create,Read,Update,Delete,Export', 1),
('MasterCompany', 'Create,Read,Update,Delete,Export', 1),
('MasterCOA', 'Create,Read,Update,Delete,Export', 1),
('MasterBranch', 'Create,Read,Update,Delete,Export', 1),
('MasterAccountType', 'Create,Read,Update,Delete,Export', 1),
('MasterAccountGroup', 'Create,Read,Update,Delete,Export', 1),
('CfgOption', 'Create,Read,Update,Delete,Export', 1),
('TrxBank', 'Create,Read,Update,Delete,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 1),
('MasterContact', 'Create,Read,Update,Delete,Export', 1),
('MasterProvince', 'Create,Read,Update,Delete,Export', 1),
('MasterCity', 'Create,Read,Update,Delete,Export', 1),
('MasterBank', 'Create,Read,Update,Delete,Export', 1),
('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 1),
('ExternalAPI', 'SubmitInvoice', 1),
('MasterDepartment', 'Create,Read,Update,Delete,Export', 1),
('MasterCostCenter', 'Create,Read,Update,Delete,Export', 1),
('MasterContactCategory', 'Create,Read,Update,Delete,Export', 1),
('Dashboard', 'Read', 1),
('CfgGlobalSettings', 'Create,Read,Update,Delete', 1),
('CfgUserSettings', 'Read,Update', 1),
('Report', 'Read,Export', 1),
('TrxAccountPayable', 'Create,Read,Update,Delete,Export,Approve', 1),
('TrxAccountReceivable', 'Create,Read,Update,Delete,Export,Approve', 1),
('TrxMedicalFee', 'Create,Read,Update,Delete,Export,Approve', 1),
('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export,Approve', 1),
('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 1),
('TrxPettyCash', 'Read,Export', 1),
('TrxPettyCashUnit', 'Create,Read,Update,Delete', 1),
('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 1),
('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 1),
('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 1);

-- Role 2: Manager
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('Dashboard', 'Read', 2),
('CfgGlobalSettings', 'Create,Read,Update,Delete', 2),
('CfgUserSettings', 'Read,Update', 2),
('Report', 'Read,Export', 2),
('MasterRole', 'Create,Read,Update,Delete,Export', 2),
('MasterCurrency', 'Create,Read,Update,Delete,Export', 2),
('MasterCompany', 'Create,Read,Update,Delete,Export', 2),
('MasterBranch', 'Create,Read,Update,Delete,Export', 2),
('MasterAccountGroup', 'Create,Read,Update,Delete,Export', 2),
('MasterAccountType', 'Create,Read,Update,Delete,Export', 2),
('MasterCOA', 'Create,Read,Update,Delete,Export', 2),
('MasterContact', 'Create,Read,Update,Delete,Export', 2),
('MasterProvince', 'Create,Read,Update,Delete,Export', 2),
('MasterCity', 'Create,Read,Update,Delete,Export', 2),
('MasterBank', 'Create,Read,Update,Delete,Export', 2),
('MasterDepartment', 'Create,Read,Update,Delete,Export', 2),
('MasterCostCenter', 'Create,Read,Update,Delete,Export', 2),
('MasterContactCategory', 'Create,Read,Update,Delete,Export', 2),
('MasterInsurance', 'Create,Read,Update,Delete,Export', 2),
('MasterUser', 'Read', 2),
('TrxJournal', 'Create,Read,Delete,Export,Approve', 2),
('TrxTransaction', 'Create,Read,Update,Delete,Void,Export,Approve', 2),
('TrxBank', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 2),
('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 2),
('CfgOption', 'Create,Read,Update,Delete', 2),
('ExternalAPI', 'SubmitInvoice', 2),
('TrxAccountPayable', 'Create,Read,Update,Delete,Export,Approve', 2),
('TrxAccountReceivable', 'Create,Read,Update,Delete,Export,Approve', 2),
('TrxMedicalFee', 'Create,Read,Update,Delete,Export,Approve', 2),
('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export,Approve', 2),
('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 2),
('TrxPettyCash', 'Read,Export', 2),
('TrxPettyCashUnit', 'Create,Read,Update,Delete', 2),
('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 2),
('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 2),
('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 2);

-- Role 3: Supervisor
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('Dashboard', 'Read', 3),
('CfgUserSettings', 'Read,Update', 3),
('TrxJournal', 'Create,Read,Delete,Export,Approve', 3),
-- Supervisor: boleh Void, TIDAK boleh Delete (hapus dibatasi Administrator + Manager).
('TrxTransaction', 'Create,Read,Update,Void,Export,Approve', 3),
('TrxBank', 'Create,Read,Update,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 3),
('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 3),
('MasterCOA', 'Read', 3),
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
('TrxAccountPayable', 'Create,Read,Update,Delete,Export,Approve', 3),
('TrxAccountReceivable', 'Create,Read,Update,Delete,Export,Approve', 3),
('TrxMedicalFee', 'Create,Read,Update,Delete,Export,Approve', 3),
('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export,Approve', 3),
('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 3),
('TrxPettyCash', 'Read', 3),
('TrxPettyCashUnit', 'Read', 3),
('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 3),
('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 3),
('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 3);

-- Role 4: Staff
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('StaffDashboard', 'Read', 4),
('CfgUserSettings', 'Read,Update', 4),
('TrxRecurringExpense', 'Create,Read', 4),
('MasterCOA', 'Read', 4),
('TrxInvoiceExchange', 'Create,Read', 4),
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
('TrxJournal', 'Create,Read,Export', 4),
('TrxTransaction', 'Create,Read,Export', 4),
('TrxBank', 'Create,Read,Update,Export', 4),
('TrxAccountPayable', 'Create,Read,Export', 4),
('TrxAccountReceivable', 'Read,Export', 4),
('TrxMedicalFee', 'Read,Export', 4),
('TrxPurchaseOrder', 'Read,Export', 4),
('TrxPettyCash', 'Read', 4),
('TrxPettyCashUnit', 'Read', 4),
('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 4),
('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 4),
('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 4);

-- Role 5: Patty Cash
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
('TrxPettyCash', 'Read', 5),
('TrxPettyCashUnit', 'Create,Read,Update,Delete', 5),
('MasterCOA', 'Read', 5),
('MasterDepartment', 'Read', 5),
('MasterCostCenter', 'Read', 5),
('MasterEmployee', 'Read', 5),
('Dashboard', 'Read', 5),
('CfgUserSettings', 'Read,Update', 5),
('TrxPettyCashTopUp', 'Create,Read,Export', 5),
('TrxPettyCashRequest', 'Create,Read,Update,Delete,Submit,Export', 5),
('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Export', 5);

COMMIT;
