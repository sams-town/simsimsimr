
namespace RSSMBackend.Models;

public class DPRequest
{
    public string OrderNumber { get; set; }
    public double Amount { get; set; }
    public DateTime Date { get; set; }
    public string Note { get; set; }
    public string CoaCode { get; set; }
    public string CoaName { get; set; }
}

public class MarkAsPaidRequest
{
    public string Note { get; set; }
}

public class DPHistoryItem
{
    public DateTime Date { get; set; }
    public double Amount { get; set; }
    public string Note { get; set; }
    public string CoaCode { get; set; }
    public string CoaName { get; set; }
    public int CreatedBy { get; set; }
}
