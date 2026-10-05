namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_ap_payment_voucher_item", Schema = "public")]
public class ApPaymentVoucherItem
{
    [Key]
    [Column("id")]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    public int Id { get; set; }

    [Column("voucher_id")]
    public int VoucherId { get; set; }

    [Column("invoice_exchange_id")]
    public int InvoiceExchangeId { get; set; }

    [Column("created_date")]
    public DateTime CreatedDate { get; set; } = DateTime.UtcNow;

    [ForeignKey(nameof(VoucherId))]
    public ApPaymentVoucher? Voucher { get; set; }

    [ForeignKey(nameof(InvoiceExchangeId))]
    public InvoiceExchange? InvoiceExchange { get; set; }
}
