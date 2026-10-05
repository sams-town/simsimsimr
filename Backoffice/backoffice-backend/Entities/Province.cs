namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_province", Schema = "master")]
public class Province {
    [Key]
    [Column("province_id")]
    public int ProvinceId { get; set; }

    [Column("province_name")]
    public string ProvinceName { get; set; }
}