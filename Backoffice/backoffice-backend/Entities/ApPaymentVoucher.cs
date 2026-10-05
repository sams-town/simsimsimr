namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_ap_payment_voucher", Schema = "public")]
public class ApPaymentVoucher
{
    [Key]
    [Column("id")]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    public int Id { get; set; }

    [Column("voucher_number")]
    public string VoucherNumber { get; set; } = string.Empty;

    [Column("voucher_date")]
    public DateTime VoucherDate { get; set; }

    [Column("payment_type")]
    public string PaymentType { get; set; } = "CASH";

    [Column("status")]
    public string Status { get; set; } = "ACTIVE";

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [Column("created_date")]
    public DateTime CreatedDate { get; set; } = DateTime.UtcNow;

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    public ICollection<ApPaymentVoucherItem> Items { get; set; } = new List<ApPaymentVoucherItem>();
}
