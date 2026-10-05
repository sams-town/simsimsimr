namespace RSSMBackend.Models;

// Payload tutup buku (modul Tutup Buku).
public class PeriodClosingRequest
{
    public string PeriodType { get; set; } = "Monthly";  // 'Monthly' | 'Yearly'
    public int Year { get; set; }
    public int? Month { get; set; }                       // wajib untuk 'Monthly'
    public bool AllBranch { get; set; } = false;          // true -> branch_id null (semua branch)
    public bool GenerateClosingEntries { get; set; } = false;
    public string? Note { get; set; }
}

public class ReopenPeriodRequest
{
    public int Id { get; set; }
    public string? Note { get; set; }
}
