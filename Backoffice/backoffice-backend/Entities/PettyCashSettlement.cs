namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_petty_cash_settlement", Schema = "public")]
public class PettyCashSettlement
{
    [Key]
    [Column("settlement_id")]
    public int SettlementId { get; set; }

    [Column("settlement_no")]
    public string SettlementNo { get; set; } = string.Empty;

    [Column("settlement_date")]
    public DateTime SettlementDate { get; set; }

    [Column("unit_id")]
    public int UnitId { get; set; }

    [Column("total_amount")]
    public decimal TotalAmount { get; set; }

    [Column("return_amount")]
    public decimal ReturnAmount { get; set; }

    [Column("status")]
    public string Status { get; set; } = "Pending";

    [Column("transaction_id")]
    public string? TransactionId { get; set; }

    [Column("notes")]
    public string? Notes { get; set; }

    [Column("request_id")]
    public int? RequestId { get; set; }

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

    // Jejak pembatalan. Settlement yang dibatalkan TIDAK dihapus: statusnya jadi
    // "Cancelled", jurnal aslinya tetap ada, dan jurnal pembaliknya ditunjuk oleh
    // CancelTransactionId.
    [Column("cancelled_by")]
    public int? CancelledBy { get; set; }

    [Column("cancelled_date")]
    public DateTime? CancelledDate { get; set; }

    [Column("cancel_reason")]
    public string? CancelReason { get; set; }

    [Column("cancel_transaction_id")]
    public string? CancelTransactionId { get; set; }

    // Navigation Properties
    [ForeignKey("UnitId")]
    public virtual PettyCashUnit? Unit { get; set; }

    [ForeignKey("RequestId")]
    public virtual PettyCashRequest? Request { get; set; }

    public virtual ICollection<PettyCashSettlementDetail>? Details { get; set; }

    [NotMapped]
    public string? UnitName { get; set; }

    [NotMapped]
    public string? UnitCode { get; set; }

    [NotMapped]
    public string? UnitCoaCode { get; set; }

    [NotMapped]
    public string? CreatedByName { get; set; }

    [NotMapped]
    public string? JournalNo { get; set; }
}
