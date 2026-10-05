namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_ar_combined_voucher_item", Schema = "public")]
public class ArCombinedVoucherItem
{
    [Key]
    [Column("id")]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    public int Id { get; set; }

    [Column("combined_id")]
    public int CombinedId { get; set; }

    [Column("ar_id")]
    public int ArId { get; set; }

    [Column("created_date")]
    public DateTime CreatedDate { get; set; } = DateTime.UtcNow;

    [ForeignKey(nameof(CombinedId))]
    public ArCombinedVoucher? CombinedVoucher { get; set; }

    [ForeignKey(nameof(ArId))]
    public AccountReceivable? AccountReceivable { get; set; }
}
