namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_role", Schema = "master")]
public class Role
{
    [Key]
    [Column("role_id")]
    public int RoleID { get; set; }

    [Column("role_name")]
    public string RoleName { get; set; }

    [NotMapped]
    public List<RolePageAccess>? AccessList { get; set; }

    [NotMapped]
    public List<RolePage>? PageList { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}