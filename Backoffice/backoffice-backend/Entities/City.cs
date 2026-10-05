namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_city", Schema = "master")]
public class City
{
    [Key]
    [Column("city_id")]
    public int CityId { get; set; }

    [Column("city_name")]
    public string CityName { get; set; }

    [Column("province_id")]
    public int ProvinceId { get; set; }

    [NotMapped]
    public string? ProvinceName { get; set; }

    [ForeignKey(nameof(ProvinceId))]
    public Province? Province { get; set; }
}