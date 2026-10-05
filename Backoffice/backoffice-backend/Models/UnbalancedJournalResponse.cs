namespace RSSMBackend.Models;

public class UnbalancedJournalResponse
{
    public string TransactionId { get; set; }
    public string TransactionNo { get; set; }
    public DateTime TransactionDate { get; set; }
    public string Description { get; set; }
    public double TotalDebit { get; set; }
    public double TotalCredit { get; set; }
    public double Difference { get; set; }
    public string Status { get; set; }
}
