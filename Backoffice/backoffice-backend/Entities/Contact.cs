namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;
using RSSMBackend.Models;
using System.Text.Json.Serialization;

[Table("mst_contact", Schema = "master")]
public class Contact
{
    [Key]
    [Column("contact_id")]
    public int ContactId { get; set; }

    [Column("contact_name")]
    public string ContactName { get; set; }

    [Column("phone")]
    public string? Phone { get; set; }

    [Column("address")]
    public string? Address { get; set; }

    [Column("email")]
    public string? Email { get; set; }

    [Column("contact_category_id")]
    public int ContactCategoryId { get; set; }

    [NotMapped]
    public string? ContactCategoryName { get; set; }

    [Column("ref_code")]
    public string? RefCode { get; set; }

    [Column("due_date")]
    public long? DueDate { get; set; }

    [Column("status")]
    public string? Status { get; set; } = "Active";

    [Column("npwp")]
    [JsonPropertyName("npwp")]
    public string? NPWP { get; set; }

    [Column("pph21")]
    [JsonPropertyName("pph21")]
    public bool? PPh21 { get; set; }

    [Column("doctor_share")]
    [JsonPropertyName("doctorShare")]
    public decimal? DoctorShare { get; set; }

    [Column("province_id")]
    public int? ProvinceId { get; set; }

    [NotMapped]
    public string? ProviceName { get; set; }

    [Column("city_id")]
    public int? CityId { get; set; }

    [NotMapped]
    public string? CityName { get; set; }

    [NotMapped]
    public List<ContactAccount>? AccountDetail { get; set; }

    [ForeignKey(nameof(CityId))]
    public City? City { get; set; }

    [ForeignKey(nameof(ProvinceId))]
    public Province? Province { get; set; }

    [ForeignKey(nameof(ContactCategoryId))]
    public ContactCategory? ContactCategory { get; set; }
}