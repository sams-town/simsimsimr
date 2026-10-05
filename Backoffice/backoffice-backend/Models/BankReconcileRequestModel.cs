namespace RSSMBackend.Models;

public class BankReconcileRequestModel
{
    public string? CoaCode { get; set; }
    public string? Desc { get; set; }
    public double? NewStatementBalance { get; set; }
    public double? TotalClear { get; set; }
    public double? TotalOutStanding { get; set; }
    public DateTime? ReconciledDate { get; set; }
    public DateTime? CreatedDate { get; set; }
    public int? CreatedBy { get; set; }
    public List<int>? clearIds { get; set; }
    
}



