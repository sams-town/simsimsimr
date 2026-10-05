namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_petty_cash_topup", Schema = "public")]
public class PettyCashTopUp
{
    [Key]
    [Column("topup_id")]
    public int TopUpId { get; set; }

    [Column("topup_no")]
    public string TopUpNo { get; set; } = string.Empty;

    [Column("topup_date")]
    public DateTime TopUpDate { get; set; }

    [Column("unit_id")]
    public int UnitId { get; set; }

    [Column("from_coa_code")]
    public string FromCoaCode { get; set; } = string.Empty;

    [Column("from_coa_name")]
    public string? FromCoaName { get; set; }

    [Column("amount")]
    public decimal Amount { get; set; }

    [Column("status")]
    public string Status { get; set; } = "Pending";

    [Column("approved_by")]
    public int? ApprovedBy { get; set; }

    [Column("approved_date")]
    public DateTime? ApprovedDate { get; set; }

    [Column("rejected_reason")]
    public string? RejectedReason { get; set; }

    [Column("rejected_by")]
    public int? RejectedBy { get; set; }

    [Column("rejected_date")]
    public DateTime? RejectedDate { get; set; }

    [Column("transaction_id")]
    public string? TransactionId { get; set; }

    [Column("notes")]
    public string? Notes { get; set; }

    [Column("approved_amount")]
    public decimal ApprovedAmount { get; set; }

    [Column("approved_notes")]
    public string? ApprovedNotes { get; set; }

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
    public string? UnitCoaCode { get; set; }

    [NotMapped]
    public string? UnitCoaName { get; set; }

    [NotMapped]
    public string? ApprovedByName { get; set; }

    [NotMapped]
    public string? CreatedByName { get; set; }

    [NotMapped]
    public string? RejectedByName { get; set; }

    [NotMapped]
    public string? JournalNo { get; set; }
}
