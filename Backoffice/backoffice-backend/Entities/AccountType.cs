namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_account_type", Schema = "master")]
public class AccountType
{
    [Key]
    [Column("account_type_id")]
    public int AccountTypeId { get; set; }

    [Required]
    [Column("account_type_name")]
    public string? AccountTypeName { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}