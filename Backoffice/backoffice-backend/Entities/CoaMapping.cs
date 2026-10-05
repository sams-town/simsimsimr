namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_coa_mapping", Schema = "master")]
public class CoaMapping
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Required]
    [Column("coa_source")]
    public string? CoaSource { get; set; }

    [Column("coa_source_name")]
    public string? CoaSourceName { get; set; }

    [Column("coa_dest")]
    public string? CoaDest { get; set; }

    [NotMapped]
    public string? CoaDestName { get; set; }
}
