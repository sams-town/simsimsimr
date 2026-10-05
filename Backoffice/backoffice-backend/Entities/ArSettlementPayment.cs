namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_ar_settlement_payment", Schema = "public")]
public class ArSettlementPayment
{
    [Key]
    [Column("id")]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    public int Id { get; set; }

    [Column("voucher_number")]
    public string VoucherNumber { get; set; } = string.Empty;

    [Column("payment_date")]
    public DateTime PaymentDate { get; set; }

    [Column("amount")]
    public double Amount { get; set; }

    [Column("discount")]
    public double Discount { get; set; } = 0;

    [Column("coa_code")]
    public string? CoaCode { get; set; }

    [Column("coa_name")]
    public string? CoaName { get; set; }

    [Column("payment_ref")]
    public string? PaymentRef { get; set; }

    [Column("paid_by")]
    public int? PaidBy { get; set; }

    [Column("created_date")]
    public DateTime CreatedDate { get; set; } = DateTime.UtcNow;

    [ForeignKey(nameof(PaidBy))]
    public User? User { get; set; }
}
