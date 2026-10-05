namespace RSSMBackend.Models;

/// <summary>
/// Fills the Audit Trail filter dropdowns. Derived from what is actually in the log rather
/// than from a hardcoded list, so new modules/operations appear without a code change.
/// </summary>
public class AuditLogFilterOptions
{
    public List<string> Modules { get; set; } = new();
    public List<string> Operations { get; set; } = new();
    public List<AuditLogUserOption> Users { get; set; } = new();
}

public class AuditLogUserOption
{
    public int UserId { get; set; }
    public string? DisplayName { get; set; }
}

/// <summary>
/// Module names used as the `module` column value. Kept here so the timeline drawer and the
/// logging call sites agree on one spelling -- the drawer queries by exact match.
/// </summary>
public static class AuditModule
{
    public const string MedicalFee = "Jasa Medis";
    public const string AccountReceivable = "Piutang AR";
    public const string AccountPayable = "Hutang AP";
    public const string PurchaseOrder = "Purchase Order";
    public const string InvoiceExchange = "Tukar Faktur";
    public const string PettyCashUnit = "Petty Cash Unit";
    public const string PettyCashRequest = "Petty Cash Request";
    public const string PettyCashSettlement = "Petty Cash Settlement";
    public const string PettyCashTopUp = "Petty Cash TopUp";
    public const string Journal = "Journal";
    public const string Transaction = "Transaction";
    public const string OpeningBalance = "OpeningBalance";
    public const string PeriodClosing = "PeriodClosing";
}
