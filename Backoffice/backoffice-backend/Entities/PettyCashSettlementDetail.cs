namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_petty_cash_settlement_detail", Schema = "public")]
public class PettyCashSettlementDetail
{
    [Key]
    [Column("detail_id")]
    public int DetailId { get; set; }

    [Column("settlement_id")]
    public int SettlementId { get; set; }

    [Column("expense_coa_code")]
    public string ExpenseCoaCode { get; set; } = string.Empty;

    [Column("expense_coa_name")]
    public string? ExpenseCoaName { get; set; }

    [Column("description")]
    public string? Description { get; set; }

    [Column("amount")]
    public decimal Amount { get; set; }

    [Column("receipt_no")]
    public string? ReceiptNo { get; set; }

    [Column("receipt_date")]
    public DateTime? ReceiptDate { get; set; }

    // Navigation Properties
    [ForeignKey("SettlementId")]
    public virtual PettyCashSettlement? Settlement { get; set; }
}
