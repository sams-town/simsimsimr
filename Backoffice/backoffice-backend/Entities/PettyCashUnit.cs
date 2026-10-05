namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_petty_cash_unit", Schema = "public")]
public class PettyCashUnit
{
    [Key]
    [Column("unit_id")]
    public int UnitId { get; set; }

    [Column("unit_code")]
    public string UnitCode { get; set; } = string.Empty;

    [Column("unit_name")]
    public string UnitName { get; set; } = string.Empty;

    [Column("coa_code")]
    public string CoaCode { get; set; } = string.Empty;

    [Column("max_limit_per_transaction")]
    public decimal? MaxLimitPerTransaction { get; set; }

    [Column("max_limit_per_period")]
    public decimal? MaxLimitPerPeriod { get; set; }

    [Column("period_type")]
    public string PeriodType { get; set; } = "monthly";

    [Column("current_balance")]
    public decimal CurrentBalance { get; set; }

    [Column("is_active")]
    public bool IsActive { get; set; } = true;

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
    public List<PettyCashUnitUser>? AssignedUsers { get; set; }

    [NotMapped]
    public string? CoaName { get; set; }

    [NotMapped]
    public string? CreatedByName { get; set; }
}
