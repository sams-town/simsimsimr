INSERT INTO master.mst_role
(role_id, role_name, branch_id)
VALUES(1, 'Administrator', 1);
INSERT INTO master.mst_role
(role_id, role_name, branch_id)
VALUES(2, 'Manager', 1);
INSERT INTO master.mst_role
(role_id, role_name, branch_id)
VALUES(3, 'Supervisor', 1);
INSERT INTO master.mst_role
(role_id, role_name, branch_id)
VALUES(4, 'Staff', 1);
INSERT INTO master.mst_role
(role_id, role_name, branch_id)
VALUES(5, 'Patty Cash', 1);

INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('ExternalAPI', 'External API', 'API', 'SubmitInvoice');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxJournal', 'Trx Journal', 'Transaction', 'Create,Read,Delete');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('CfgOption', 'Option', 'Configuration', 'Create,Read,Update,Delete');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterInsurance', 'Master Insurance', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterUser', 'Master User', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterRole', 'Master Role', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterCurrency', 'Master Currency', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterCompany', 'Master Company', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterBranch', 'Master Branch', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterAccountGroup', 'Master AccountGroup', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterAccountType', 'Master Account Type', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterCOA', 'Master COA', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxBank', 'Trx Bank', 'Transaction', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterContact', 'MasterContact', 'Contact', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterProvince', 'Master Province', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterCity', 'Master City', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterBank', 'Master Bank', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxRecurringExpense', 'Trx Recurring', 'Recurring', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterDepartment', 'Master Department', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterCostCenter', 'Master Cost Center', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('MasterContactCategory', 'Master Contact Category', 'Master', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('Report', 'Report', 'Report', 'Read,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('Report', 'Report', 'Report', 'Read,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxAccountPayable', 'Account Payable', 'Transaction', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxAccountReceivable', 'Account Receivable', 'Transaction', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxMedicalFee', 'Medical Fee', 'Transaction', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPurchaseOrder', 'Purchase Order', 'Transaction', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxInvoiceExchange', 'Invoice Exchange', 'Transaction', 'Create,Read,Update,Delete,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCash', 'Petty Cash Dashboard', 'Transaction', 'Read');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashUnit', 'Petty Cash Unit', 'Transaction', 'Create,Read,Update,Delete');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCash', 'Petty Cash Dashboard', 'Transaction', 'Read');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashUnit', 'Petty Cash Unit', 'Transaction', 'Create,Read,Update,Delete');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashTopUp', 'Petty Cash Top Up', 'Transaction', 'Create,Read,Approve,Reject,Void,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashTopUp', 'Petty Cash Top Up', 'Transaction', 'Create,Read,Approve,Reject,Void,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashRequest', 'Petty Cash Request', 'Transaction', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashRequest', 'Petty Cash Request', 'Transaction', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashSettlement', 'Petty Cash Settlement', 'Transaction', 'Create,Read,Update,Delete,Post,Void,Export');
INSERT INTO master.mst_role_page
(page_code, page, category, "action")
VALUES('TrxPettyCashSettlement', 'Petty Cash Settlement', 'Transaction', 'Create,Read,Update,Delete,Post,Void,Export');

INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxJournal', 'Create,Read,Delete', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterInsurance', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxTransaction', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterUser', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterRole', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCurrency', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCompany', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCOA', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBranch', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountType', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountGroup', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgOption', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxBank', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContact', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterProvince', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCity', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBank', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('ExternalAPI', 'SubmitInvoice', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterDepartment', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCostCenter', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContactCategory', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Dashboard', 'Read', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgGlobalSettings', 'Create,Read,Update,Delete', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Report', 'Read,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Dashboard', 'Read', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgGlobalSettings', 'Create,Read,Update,Delete', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Report', 'Read,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterRole', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCurrency', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCompany', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBranch', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountGroup', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountType', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCOA', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContact', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterProvince', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCity', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBank', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterDepartment', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCostCenter', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContactCategory', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterInsurance', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxJournal', 'Create,Read,Delete', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxTransaction', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxBank', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgOption', 'Create,Read,Update,Delete', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('ExternalAPI', 'SubmitInvoice', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Dashboard', 'Read', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgGlobalSettings', 'Create,Read,Update,Delete', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Report', 'Read,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Dashboard', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxJournal', 'Create,Read,Delete', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxTransaction', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxBank', 'Read,SubmitPayment,SubmitDeposit,SubmitReconcile,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxRecurringExpense', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('StaffDashboard', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxRecurringExpense', 'Create,Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCOA', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCOA', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountPayable', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountReceivable', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxMedicalFee', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountPayable', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountReceivable', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxMedicalFee', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountPayable', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountReceivable', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxMedicalFee', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPurchaseOrder', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxInvoiceExchange', 'Create,Read,Update,Delete,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxInvoiceExchange', 'Create,Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCash', 'Read', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashUnit', 'Create,Read,Update,Delete', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCOA', 'Read', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterDepartment', 'Read', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCostCenter', 'Read', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterEmployee', 'Read', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Dashboard', 'Read', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('CfgUserSettings', 'Read,Update', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Approve,Reject,Void,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashTopUp', 'Create,Read,Export', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterUser', 'Read', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Approve,Reject,Submit,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 1);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 2);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Void,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashRequest', 'Create,Read,Update,Delete,Submit,Export', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPettyCashSettlement', 'Create,Read,Update,Delete,Post,Export', 5);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterInsurance', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCurrency', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCompany', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBranch', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountGroup', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountType', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContact', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterProvince', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCity', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBank', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterDepartment', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCostCenter', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContactCategory', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterUser', 'Read', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Report', 'Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxJournal', 'Create,Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxTransaction', 'Create,Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxBank', 'Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountPayable', 'Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxAccountReceivable', 'Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxMedicalFee', 'Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('TrxPurchaseOrder', 'Read,Export', 4);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterInsurance', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCurrency', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCompany', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBranch', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountGroup', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterAccountType', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContact', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterProvince', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCity', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterBank', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterDepartment', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterCostCenter', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterContactCategory', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('MasterUser', 'Read', 3);
INSERT INTO master.mst_role_page_access
(page_code, "action", role_id)
VALUES('Report', 'Read,Export', 3);