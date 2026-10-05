namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_account_group", Schema = "master")] // Menentukan nama tabel dan skema
public class AccountGroup
{
    [Key]
    [Column("account_group_id")]
    public int AccountGroupId { get; set; }

    [Required]
    [Column("account_group_name")]
    public string? AccountGroupName { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}