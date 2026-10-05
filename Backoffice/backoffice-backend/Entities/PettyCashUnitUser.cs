namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_petty_cash_unit_user", Schema = "public")]
public class PettyCashUnitUser
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("unit_id")]
    public int UnitId { get; set; }

    [Column("user_id")]
    public int UserId { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    // Navigation Properties
    [ForeignKey("UnitId")]
    public PettyCashUnit? Unit { get; set; }

    [NotMapped]
    public string? DisplayName { get; set; }
}
