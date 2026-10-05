namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_balance", Schema = "public")]
public class TrxBalance
{
    [Key]
    [Column("id")]
    public int BalanceId { get; set; }

    [Column("period_date")]
    public DateTime PeriodDate { get; set; }

    [Column("coa_code")]
    public string CoaCode { get; set; } = string.Empty;

    [Column("opening_balance")]
    public double OpeningBalance { get; set; } = 0;

    [Column("debit_amount")]
    public double DebitAmount { get; set; } = 0;

    [Column("credit_amount")]
    public double CreditAmount { get; set; } = 0;

    [Column("closing_balance")]
    public double ClosingBalance { get; set; } = 0;

    [Column("branch_id")]
    public int? BranchId { get; set; }

    // Membedakan sumber baris checkpoint: 'Opening' = saldo awal input manual (modul Saldo Awal),
    // 'Checkpoint'/null = checkpoint hasil job/tutup buku. Lihat GenerateMonthlyCheckpoint & OpeningBalanceService.
    [Column("entry_type")]
    public string? EntryType { get; set; }

    [Column("note")]
    public string? Note { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    [NotMapped]
    public string? CoaName { get; set; }

    [NotMapped]
    public string? NormalPosition { get; set; }

    [NotMapped]
    public string? CreatedName { get; set; }

    [ForeignKey(nameof(CoaCode))]
    public Coa? Coa { get; set; }

    [ForeignKey(nameof(CreatedBy))]
    public User? Created { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }
}
