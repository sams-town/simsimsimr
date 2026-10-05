namespace RSSMBackend.Models;

public class ApproveRequestModel
{
    public decimal ApprovedAmount { get; set; }
    public string? Notes { get; set; }
}

public class RejectRequestModel
{
    public string Reason { get; set; } = string.Empty;
}

/// <summary>Body opsional untuk pembatalan settlement.</summary>
public class CancelSettlementModel
{
    public string? Reason { get; set; }
}

public class CreateSettlementModel
{
    public DateTime SettlementDate { get; set; }
    public int UnitId { get; set; }
    public int? RequestId { get; set; } // Now in header
    public decimal ReturnAmount { get; set; }
    public string? Notes { get; set; }
    public List<SettlementDetailModel> Details { get; set; } = new();
}

public class SettlementDetailModel
{
    public string ExpenseCoaCode { get; set; } = string.Empty;
    public string? ExpenseCoaName { get; set; }
    public string? Description { get; set; }
    public decimal Amount { get; set; }
    public string? ReceiptNo { get; set; }
    public DateTime? ReceiptDate { get; set; }
}

public class UnitModel
{
    public int UnitId { get; set; }
    public string UnitCode { get; set; } = string.Empty;
    public string UnitName { get; set; } = string.Empty;
    public string CoaCode { get; set; } = string.Empty;
    public decimal? MaxLimitPerTransaction { get; set; }
    public decimal? MaxLimitPerPeriod { get; set; }
    public string PeriodType { get; set; } = "Monthly";
    public decimal CurrentBalance { get; set; }
    public bool IsActive { get; set; } = true;
    public int? BranchId { get; set; }
    public List<int>? AssignedUserIds { get; set; }
}
