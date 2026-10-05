namespace RSSMBackend.DTOs.Journal;

public class SummaryLedgerResponse
{
    public int CoaId { get; set; }
    public string CoaCode { get; set; } = string.Empty;
    public string CoaName { get; set; } = string.Empty;
    public double BeginningBalance { get; set; }
    public double Debit { get; set; }
    public double Credit { get; set; }
    public double NetChange { get; set; }
    public double EndingBalance { get; set; }
}
