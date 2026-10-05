namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("cfg_options")]
public class Option
{
    [Key]
    [Column("option_id")]
    public int OptionId { get; set; }

    [Column("option_name")]
    public string OptionName { get; set; }

    [Column("option_value")]
    public string? OptionValue { get; set; }

    [Column("option_type")]
    public string? OptionType { get; set; }

    [Column("option_label")]
    public string? OptionLabel { get; set; }

    [Column("option_desc")]
    public string? OptionDesc { get; set; }
    
    [Column("branch_id")]
    public int BranchId { get; set; }
}