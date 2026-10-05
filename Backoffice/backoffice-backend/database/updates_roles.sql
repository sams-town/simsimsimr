-- Add new roles if they don't exist
INSERT INTO master.mst_role (role_name)
SELECT 'Finance'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role WHERE role_name = 'Finance');

INSERT INTO master.mst_role (role_name)
SELECT 'Auditor'
WHERE NOT EXISTS (SELECT 1 FROM master.mst_role WHERE role_name = 'Auditor');

-- Grant permissions for Finance (Example: Full access to Transactions, Read-only to Master)
-- Assuming Role IDs will be generated. We need to look them up.

DO $$
DECLARE
    finance_id int;
    auditor_id int;
BEGIN
    SELECT role_id INTO finance_id FROM master.mst_role WHERE role_name = 'Finance';
    SELECT role_id INTO auditor_id FROM master.mst_role WHERE role_name = 'Auditor';

    -- Finance Access
    IF finance_id IS NOT NULL THEN
        DELETE FROM master.mst_role_page_access WHERE role_id = finance_id;
        
        INSERT INTO master.mst_role_page_access (role_id, page_code, "action") VALUES
        (finance_id, 'TrxAccountPayable', 'Create,Read,Update,Delete,Export'),
        (finance_id, 'TrxAccountReceivable', 'Create,Read,Update,Delete,Export'),
        (finance_id, 'TrxMedicalFee', 'Create,Read,Update,Delete,Export'),
        (finance_id, 'TrxPurchaseOrder', 'Create,Read,Update,Delete,Export'),
        (finance_id, 'TrxInvoiceExchange', 'Create,Read,Update,Delete,Export'),
        (finance_id, 'TrxJournal', 'Create,Read,Update,Export'); -- Assuming TrxJournal exists
    END IF;

    -- Auditor Access (Read Only)
    IF auditor_id IS NOT NULL THEN
        DELETE FROM master.mst_role_page_access WHERE role_id = auditor_id;
        
        INSERT INTO master.mst_role_page_access (role_id, page_code, "action") VALUES
        (auditor_id, 'TrxAccountPayable', 'Read,Export'),
        (auditor_id, 'TrxAccountReceivable', 'Read,Export'),
        (auditor_id, 'TrxMedicalFee', 'Read,Export'),
        (auditor_id, 'TrxPurchaseOrder', 'Read,Export'),
        (auditor_id, 'TrxInvoiceExchange', 'Read,Export'),
        (auditor_id, 'TrxJournal', 'Read,Export');
    END IF;
END $$;
