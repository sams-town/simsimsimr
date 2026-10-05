namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_company", Schema = "master")]
public class Company
{
    [Key]
    [Column("company_id")]
    public int CompanyId { get; set; }

    [Required]
    [Column("company_name")]
    public string? CompanyName { get; set; }

    [Column("phone")]
    public string? Phone { get; set; }

    [Column("email")]
    public string? Email { get; set; }

    [Column("address")]
    public string? Address { get; set; }

    [Column("financial_closing_date")]
    public DateTime? FinancialClosingDate { get; set; }

    [Column("currency_id")]
    public int? CurrencyId { get; set; }

    [NotMapped]
    public string? CurrencyName { get; set; }

    [Column("status")]
    public string Status { get; set; }

    [ForeignKey(nameof(CurrencyId))]
    public Currency? Currency { get; set; }
}