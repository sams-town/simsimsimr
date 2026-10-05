namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_coa", Schema = "master")]
public class Coa
{
    [Key]
    [Column("coa_id")]
    public int CoaId { get; set; }

    [Column("coa_code")]
    public string? CoaCode { get; set; }

    [Column("coa_name")]
    public string? CoaName { get; set; }

    [Column("account_type_id")]
    public int? AccountTypeId { get; set; }

    [NotMapped]
    public string? AccountTypeName { get; set; }

    [Column("coa_level")]
    public int? CoaLevel { get; set; }

    [Column("parent_id")]
    public int? ParentId { get; set; }

    [NotMapped]
    public string? ParentName { get; set; }

    [Column("parent_code")]
    public string? ParentCode { get; set; }

    [Column("normal_position")]
    public string? NormalPosition { get; set; }

    [Column("account_group_id")]
    public int? AccountGroupId { get; set; }

    [NotMapped]
    public string? AccountGroupName { get; set; }

    [Column("is_cash_flow")]
    public bool? IsCashFlow { get; set; }

    [Column("is_cash_bank")]
    public bool? IsCashBank { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [Column("balance")]
    public double? Balance { get; set; }

    [Column("currency_id")]
    public int? CurrencyId { get; set; }

    [NotMapped]
    public string? CurrencyName { get; set; }

    [Column("status")]
    public string? Status { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [NotMapped]
    public string? CreatedName { get; set; }


    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [NotMapped]
    public string? UpdatedName { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    [Column("last_reconciled_date")]
    public DateTime? ReconciledDate { get; set; }

    [NotMapped]
    public List<Journal>? Journals { get; set; }

    [ForeignKey(nameof(AccountGroupId))]
    public AccountGroup? AccountGroup { get; set; }

    [ForeignKey(nameof(AccountTypeId))]
    public AccountType? AccountType { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }

    [ForeignKey(nameof(CurrencyId))]
    public Currency? Currency { get; set; }

    [ForeignKey(nameof(ParentId))]
    public Coa? Parent { get; set; }

    [ForeignKey(nameof(CreatedBy))]
    public User? Created { get; set; }

    [ForeignKey(nameof(UpdatedBy))]
    public User? Updated { get; set; }
}