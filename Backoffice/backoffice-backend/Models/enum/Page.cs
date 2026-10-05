namespace RSSMBackend.Models;
public enum Page
{
    MasterUser,
    MasterRole,
    MasterCurrency,
    MasterAccountGroup,
    MasterAccountType,
    MasterCOA,
    MasterCompany,
    MasterBranch,
    ExternalAPI,
    TrxJournal,
    TrxTransaction,
    CfgOption,
    TrxBank,
    MasterInsurance,
    MasterContact,
    MasterProvince,
    MasterCity,
    MasterBank,
    MasterDepartment,
    MasterCostCenter,
    MasterContactCategory,
    TrxRecurringExpense,
    TrxInvoiceExchange,
    // New pages for role-based access
    Dashboard,
    StaffDashboard,
    CfgGlobalSettings,
    CfgUserSettings,
    Report,
    TrxPurchaseOrder,
    TrxAccountPayable,
    TrxAccountReceivable,
    TrxMedicalFee,
    
    // Petty Cash Module
    TrxPettyCash,
    TrxPettyCashUnit,
    TrxPettyCashRequest,
    TrxPettyCashSettlement,
    TrxPettyCashTopUp,

    // Admin: manual Khanza migration script runner
    CfgMigrationJob,

    MasterBpjsCost,

    // Admin: central audit trail listing
    CfgAuditTrail,

    // Admin/Manager: sinkronisasi satuan data hasil tarikan Khanza (bandingkan & terapkan per field)
    CfgKhanzaSync
}