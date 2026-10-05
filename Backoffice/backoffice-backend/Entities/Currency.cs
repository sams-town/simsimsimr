namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_currency", Schema = "master")]
public class Currency
{
    [Key]
    [Column("currency_id")]
    public int CurrencyId { get; set; }

    [Required]
    [Column("currency_name")]
    public string? CurrencyName { get; set; }

    [Required]
    [Column("currency_code")]
    public string? CurrencyCode { get; set; }

    [Required]
    [Column("symbol")]
    public string? Symbol { get; set; }

    [Column("country")]
    public string? Country { get; set; }

    [Column("exchange_rate")]
    public int? ExchangeRate { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}