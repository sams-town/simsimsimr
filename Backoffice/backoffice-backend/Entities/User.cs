namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_users", Schema = "master")]
public class User
{

    [Key]
    [Column("user_id")]
    public int UserID { get; set; }

    [Column("email")]
    public string Email { get; set; }

    [Column("password")]
    public string? Password { get; set; }

    [Column("display_name")]
    public string DisplayName { get; set; }

    [Column("created_date")]
    public DateTime CreatedDate { get; set; }

    [Column("last_login")]
    public DateTime? LastLogin { get; set; }

    [Column("status")]
    public string Status { get; set; }

    [Column("salt")]
    public string? Salt { get; set; }

    [Column("phone")]
    public string? Phone { get; set; }

    [Column("profile_picture")]
    public string? ProfilePicture { get; set; }

    [Column("role_id")]
    public int RoleID { get; set; }

    [NotMapped]
    public string? RoleName { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [ForeignKey(nameof(RoleID))]
    public Role? Role { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}