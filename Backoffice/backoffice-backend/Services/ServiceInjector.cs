using RSSMBackend.Entities;
using RSSMBackend.Models;
using TDPBackend.Helpers;

namespace RSSMBackend.Services;

public static class ServiceInjector
{
    public static void Inject(IServiceCollection services)
    {
        services.AddScoped<IUserService, UserService>();
        services.AddScoped<IRoleService, RoleService>();
        services.AddScoped<ICurrencyService, CurrencyService>();
        services.AddScoped<ICompanyService, CompanyService>();
        services.AddScoped<IBranchService, BranchService>();
        services.AddScoped<IAccountTypeService, AccountTypeService>();
        services.AddScoped<IAccountGroupService, AccountGroupService>();
        services.AddScoped<ICOAService, COAService>();
        services.AddScoped<ICoaMappingService, CoaMappingService>();
        services.AddScoped<IJournalService, JournalService>();
        services.AddScoped<IOpeningBalanceService, OpeningBalanceService>();
        services.AddScoped<IPeriodClosingService, PeriodClosingService>();
        services.AddScoped<IOptionService, OptionService>();
        services.AddScoped<ITransactionService, TransactionService>();
        services.AddScoped<IContactService, ContactService>();
        services.AddScoped<IProvinceService, ProvinceService>();
        services.AddScoped<ICityService, CityService>();
        services.AddScoped<IContactAccountService, ContactAccountService>();
        services.AddScoped<IBankService, BankService>();
        services.AddScoped<IDepartmentService, DepartmentService>();
        services.AddScoped<ICostCenterService, CostCenterService>();
        services.AddScoped<IContactCategoryService, ContactCategoryService>();
        services.AddScoped<IAuditLogService, AuditLogService>();

        services.AddScoped<AuditTrailUtils>();
        services.AddScoped<IJournalJobService, JournalJobService>();
        services.AddScoped<IInvoiceExchangeService, InvoiceExchangeService>();
        services.AddScoped<IAccountPayableService, AccountPayableService>();
        services.AddScoped<IMedicalFeeService, MedicalFeeService>();
        services.AddScoped<IDashboardService, DashboardService>();
        services.AddScoped<AccountReceivable.IAccountReceivableService, AccountReceivable.AccountReceivableService>();
        services.AddScoped<AccountReceivable.IArCombinedVoucherService, AccountReceivable.ArCombinedVoucherService>();
        services.AddScoped<AccountPayable.IApPaymentVoucherService, AccountPayable.ApPaymentVoucherService>();
        services.AddScoped<BankFormMaker.IBankFormVoucherService, BankFormMaker.BankFormVoucherService>();
        services.AddScoped<IEmailService, EmailService>();
        services.AddScoped<IPurchaseOrderService, PurchaseOrderService>();
        services.AddScoped<IKhanzaService, KhanzaService>();
        services.AddScoped<PettyCash.IPettyCashService, PettyCash.PettyCashService>();
        services.AddScoped<MigrationJob.IMigrationJobService, MigrationJob.MigrationJobService>();
        services.AddScoped<KhanzaSync.IKhanzaConnectionFactory, KhanzaSync.KhanzaConnectionFactory>();
        services.AddScoped<KhanzaSync.IKhanzaSyncService, KhanzaSync.KhanzaSyncService>();
        services.AddScoped<JournalImport.IJournalImportJobService, JournalImport.JournalImportJobService>();
        services.AddScoped<IBpjsCostService, BpjsCostService>();
    }
}