-- =====================================================
-- NEW ROLES AND PERMISSIONS SQL SCRIPT
-- =====================================================
-- Run this script to add Manager, Supervisor, Staff roles
-- and their corresponding page access permissions
-- =====================================================

-- 1. INSERT NEW ROLES
-- =====================================================
INSERT INTO master.mst_role (role_name, branch_id) VALUES
    ('Manager', 1),
    ('Supervisor', 1),
    ('Staff', 1);

-- 2. INSERT NEW mst_role_page ENTRIES (if not exists)
-- =====================================================
-- Add new page definitions for role-based access control
INSERT INTO master.mst_role_page (page_code, page, category, "action") VALUES
    ('Dashboard', 'Dashboard', 'Dashboard', 'Read'),
    ('StaffDashboard', 'Staff Dashboard', 'Dashboard', 'Read'),
    ('CfgGlobalSettings', 'Global Settings', 'Configuration', 'Create,Read,Update,Delete'),
    ('CfgUserSettings', 'User Settings', 'Configuration', 'Read,Update'),
    ('Report', 'Report', 'Report', 'Read,Export')
ON CONFLICT DO NOTHING;

-- 3. ROLE PAGE ACCESS - ADMINISTRATOR (role_id = 1)
-- =====================================================
-- Administrator has ALL access (already exists, but ensure Dashboard/Settings)
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
    ('Dashboard', 'Read', 1),
    ('CfgGlobalSettings', 'Create,Read,Update,Delete', 1),
    ('CfgUserSettings', 'Read,Update', 1),
    ('Report', 'Read,Export', 1)
ON CONFLICT DO NOTHING;

-- 4. ROLE PAGE ACCESS - MANAGER (role_id = 2)
-- =====================================================
-- Manager: All access EXCEPT MasterUser
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
    -- Dashboard
    ('Dashboard', 'Read', 2),
    ('CfgGlobalSettings', 'Create,Read,Update,Delete', 2),
    ('CfgUserSettings', 'Read,Update', 2),
    ('Report', 'Read,Export', 2),
    -- Master (except User)
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
    -- Transactions
    ('TrxJournal', 'Create,Read,Delete', 2),
    ('TrxTransaction', 'Create,Read,Update,Delete,Export', 2),
    ('TrxBank', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 2),
    ('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 2),
    ('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 2),
    -- Configuration
    ('CfgOption', 'Create,Read,Update,Delete', 2),
    -- External API
    ('ExternalAPI', 'SubmitInvoice', 2);

-- 5. ROLE PAGE ACCESS - SUPERVISOR (role_id = 3)
-- =====================================================
-- Supervisor: Transactions only, no Master, no Global Settings
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
    -- Dashboard (full)
    ('Dashboard', 'Read', 3),
    -- User Settings only (no Global)
    ('CfgUserSettings', 'Read,Update', 3),
    -- Reports
    ('Report', 'Read,Export', 3),
    -- Transactions (full access)
    ('TrxJournal', 'Create,Read,Delete', 3),
    ('TrxTransaction', 'Create,Read,Update,Delete,Export', 3),
    ('TrxBank', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 3),
    ('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 3),
    ('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 3);

-- 6. ROLE PAGE ACCESS - STAFF (role_id = 4)
-- =====================================================
-- Staff: Limited transactions (Read, Create only), no Reports, no Settings
INSERT INTO master.mst_role_page_access (page_code, "action", role_id) VALUES
    -- Staff Dashboard (different from main dashboard)
    ('StaffDashboard', 'Read', 4),
    -- User Settings only
    ('CfgUserSettings', 'Read,Update', 4),
    -- Transactions (Create, Read only - NO Update, Delete, Export)
    ('TrxJournal', 'Create,Read', 4),
    ('TrxTransaction', 'Create,Read', 4),
    ('TrxBank', 'Read', 4),
    ('TrxRecurringExpense', 'Create,Read', 4),
    ('TrxInvoiceExchange', 'Create,Read', 4);

-- =====================================================
-- END OF SCRIPT
-- =====================================================
