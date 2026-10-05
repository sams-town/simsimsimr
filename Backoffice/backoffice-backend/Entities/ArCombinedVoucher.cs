namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_ar_combined_voucher", Schema = "public")]
public class ArCombinedVoucher
{
    [Key]
    [Column("id")]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    public int Id { get; set; }

    [Column("batch_number")]
    public string BatchNumber { get; set; } = string.Empty;

    [Column("batch_date")]
    public DateTime BatchDate { get; set; }

    [Column("description")]
    public string? Description { get; set; }

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

    public ICollection<ArCombinedVoucherItem> Items { get; set; } = new List<ArCombinedVoucherItem>();
}
