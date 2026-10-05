namespace RSSMBackend.DbContext;

using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Storage.ValueConversion;
using RSSMBackend.Entities;

public class RSSMDbContext : DbContext {
    public RSSMDbContext(DbContextOptions<RSSMDbContext> context) : base(context){}
    public DbSet<User> Users { get; set; }
    public DbSet<Role> Roles { get; set; }
    public DbSet<RolePageAccess> RolePageAccesses { get; set; }
    public DbSet<RolePage> RolePages { get; set; }
    public DbSet<AccountGroup> AccountGroups { get; set; }
    public DbSet<AccountType> AccountTypes { get; set; }
    public DbSet<Branch> Branches { get; set; }
    public DbSet<Coa> Coa { get; set; }
    public DbSet<Company> Companies { get; set; }
    public DbSet<Currency> Currencies { get; set; }
    public DbSet<Journal> Journals { get; set; }
    public DbSet<Option> Options { get; set; }
    public DbSet<Transaction> Transactions { get; set; }
    public DbSet<Contact> Contacts { get; set; }
    public DbSet<Province> Provinces { get; set; }
    public DbSet<City> Cities { get; set; }
    public DbSet<ContactAccount> ContactAccounts { get; set; }
    public DbSet<Bank> Banks { get; set; }
    public DbSet<AutoNumber> CfgAutoNumber { get; set; }
    public DbSet<Department> Departments { get; set; }
    public DbSet<CostCenter> CostCenters { get; set; }
    public DbSet<ContactCategory> ContactCategories { get; set; }
    public DbSet<CoaMapping> CoaMappings { get; set; }
    public DbSet<TrxBalance> TrxBalances { get; set; }
    public DbSet<PeriodClosing> PeriodClosings { get; set; }

    public DbSet<AuditLog> AuditLogs { get; set; }
    public DbSet<InvoiceExchange> InvoiceExchanges { get; set; }
    public DbSet<MedicalFee> MedicalFees { get; set; }
    public DbSet<AccountReceivable> AccountReceivables { get; set; }
    public DbSet<ArSettlementPayment> ArSettlementPayments { get; set; }
    public DbSet<ArCombinedVoucher> ArCombinedVouchers { get; set; }
    public DbSet<ArCombinedVoucherItem> ArCombinedVoucherItems { get; set; }
    public DbSet<ApPaymentVoucher> ApPaymentVouchers { get; set; }
    public DbSet<ApPaymentVoucherItem> ApPaymentVoucherItems { get; set; }
    public DbSet<BankFormVoucher> BankFormVouchers { get; set; }
    public DbSet<PurchaseOrder> PurchaseOrders { get; set; }
    
    // Petty Cash
    public DbSet<PettyCashUnit> PettyCashUnits { get; set; }
    public DbSet<PettyCashUnitUser> PettyCashUnitUsers { get; set; }
    public DbSet<PettyCashRequest> PettyCashRequests { get; set; }
    public DbSet<PettyCashSettlement> PettyCashSettlements { get; set; }
    public DbSet<PettyCashSettlementDetail> PettyCashSettlementDetails { get; set; }
    public DbSet<PettyCashTopUp> PettyCashTopUps { get; set; }

    public DbSet<MigrationJob> MigrationJobs { get; set; }
    public DbSet<JournalImportJob> JournalImportJobs { get; set; }
    public DbSet<BpjsCost> BpjsCosts { get; set; }
    protected override void OnModelCreating(ModelBuilder modelBuilder)
    {
        modelBuilder.Entity<RolePageAccess>().HasKey(x => new { x.PageCode, x.Action, x.RoleID });
        modelBuilder.Entity<RolePageAccess>().ToTable("mst_role_page_access", "master");

        modelBuilder.Entity<Coa>()
        .HasAlternateKey(x => x.CoaCode)
        .HasName("ak_coa_code");


        modelBuilder.Entity<Journal>()
        .HasOne(x => x.Coa)
        .WithMany()
        .HasForeignKey(x => x.CoaCode)
        .HasPrincipalKey(x => x.CoaCode);

        modelBuilder.Entity<Transaction>()
        .HasOne(x => x.Coa)
        .WithMany()
        .HasForeignKey(x => x.CoaCode)
        .HasPrincipalKey(x => x.CoaCode);

        modelBuilder.Entity<Transaction>()
        .HasOne(x => x.CoaParent)
        .WithMany()
        .HasForeignKey(x => x.CoaParentCode)
        .HasPrincipalKey(x => x.CoaCode);

        modelBuilder.Entity<Journal>()
        .HasOne(x => x.CoaParent)
        .WithMany()
        .HasForeignKey(x => x.CoaParentCode)
        .HasPrincipalKey(x => x.CoaCode);

        modelBuilder.Entity<InvoiceExchange>()
        .Property(x => x.TotalTransaction)
        .HasColumnType("numeric(18,2)");

        modelBuilder.Entity<TrxBalance>()
        .HasOne(x => x.Coa)
        .WithMany()
        .HasForeignKey(x => x.CoaCode)
        .HasPrincipalKey(x => x.CoaCode);

        // Global DateTime UTC Conversion for PostgreSQL
        foreach (var entityType in modelBuilder.Model.GetEntityTypes())
        {
            foreach (var property in entityType.GetProperties())
            {
                if (property.ClrType == typeof(DateTime) || property.ClrType == typeof(DateTime?))
                {
                    property.SetValueConverter(new ValueConverter<DateTime, DateTime>(
                        v => v.Kind == DateTimeKind.Utc ? v : DateTime.SpecifyKind(v, DateTimeKind.Utc),
                        v => v.Kind == DateTimeKind.Utc ? v : DateTime.SpecifyKind(v, DateTimeKind.Utc)
                    ));
                }
            }
        }
    }
}