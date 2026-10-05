namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("cfg_auto_number", Schema = "public")]
public class AutoNumber
{
    [Key]
    [Column("code")]
    public string Code { get; set; }

    [Column("name")]
    public string Name { get; set; }

    [Column("prefix")]
    public string Prefix { get; set; }

    [Column("last_code")]
    public string LastCode { get; set; }

    [Column("branch_id")]
    public int BranchId { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}