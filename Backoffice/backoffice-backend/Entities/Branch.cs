namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_branch", Schema = "master")]
public class Branch
{
    [Key]
    [Column("branch_id")]
    public int BranchId { get; set; }

    [Required]
    [Column("branch_name")]
    public string? BranchName { get; set; }

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

    [Column("company_id")]
    public int? CompanyId { get; set; }

    [NotMapped]
    public string? CompanyName { get; set; }

    [NotMapped]
    public string? CurrencyName { get; set; }

    [Column("status")]
    public string Status { get; set; }

    [ForeignKey(nameof(CompanyId))]
    public Company? Company { get; set; }

    [ForeignKey(nameof(CurrencyId))]
    public Currency? Currency { get; set; }     
}