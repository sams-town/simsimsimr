namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_petty_cash_request", Schema = "public")]
public class PettyCashRequest
{
    [Key]
    [Column("request_id")]
    public int RequestId { get; set; }

    [Column("transaction_id")]
    public string? TransactionId { get; set; }

    [Column("request_no")]
    public string RequestNo { get; set; } = string.Empty;

    [Column("request_date")]
    public DateTime RequestDate { get; set; }

    [Column("unit_id")]
    public int UnitId { get; set; }

    [Column("requester_name")]
    public string RequesterName { get; set; } = string.Empty;

    [Column("purpose")]
    public string Purpose { get; set; } = string.Empty;

    [Column("amount")]
    public decimal Amount { get; set; }

    [Column("expense_coa_code")]
    public string? ExpenseCoaCode { get; set; }

    [Column("cost_center_code")]
    public string? CostCenterCode { get; set; }

    [Column("cost_center_name")]
    public string? CostCenterName { get; set; }

    [Column("status")]
    public string Status { get; set; } = "Draft";

    [Column("approved_by")]
    public int? ApprovedBy { get; set; }

    [Column("approved_date")]
    public DateTime? ApprovedDate { get; set; }

    [Column("approved_amount")]
    public decimal? ApprovedAmount { get; set; }

    [Column("rejected_reason")]
    public string? RejectedReason { get; set; }

    [Column("settlement_id")]
    public int? SettlementId { get; set; }

    [Column("notes")]
    public string? Notes { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    // Navigation Properties
    [ForeignKey("UnitId")]
    public virtual PettyCashUnit? Unit { get; set; }

    [NotMapped]
    public string? UnitName { get; set; }

    [NotMapped]
    public string? UnitCode { get; set; }
    
    [NotMapped]
    public string? JournalNo { get; set; }

    [NotMapped]
    public string? ExpenseCoaName { get; set; }

    [NotMapped]
    public string? ApprovedByName { get; set; }

    [NotMapped]
    public string? CreatedByName { get; set; }
}
