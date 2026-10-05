namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

// Catatan tutup buku periode (bulanan/tahunan). Mengunci periode dari perubahan jurnal
// (lihat PeriodClosingService.IsPeriodClosed) + menyimpan riwayat close/reopen.
[Table("trx_period_closing", Schema = "public")]
public class PeriodClosing
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    // 'Monthly' | 'Yearly'
    [Column("period_type")]
    public string PeriodType { get; set; } = "Monthly";

    [Column("year")]
    public int Year { get; set; }

    // null untuk period_type 'Yearly'
    [Column("month")]
    public int? Month { get; set; }

    // null = berlaku untuk semua branch
    [Column("branch_id")]
    public int? BranchId { get; set; }

    // 'Closed' | 'Reopened'
    [Column("status")]
    public string Status { get; set; } = "Closed";

    [Column("generate_closing_entries")]
    public bool GenerateClosingEntries { get; set; }

    // transaction_id jurnal penutup (bila dibuat), agar bisa di-void saat reopen.
    [Column("closing_transaction_id")]
    public string? ClosingTransactionId { get; set; }

    [Column("note")]
    public string? Note { get; set; }

    [Column("closed_by")]
    public int? ClosedBy { get; set; }

    [Column("closed_date")]
    public DateTime? ClosedDate { get; set; }

    [Column("reopened_by")]
    public int? ReopenedBy { get; set; }

    [Column("reopened_date")]
    public DateTime? ReopenedDate { get; set; }

    [NotMapped]
    public string? ClosedName { get; set; }

    [NotMapped]
    public string? ReopenedName { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }
}
