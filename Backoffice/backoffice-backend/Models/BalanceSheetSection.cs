
namespace RSSMBackend.Models;

public class BalanceSheetSection
{
    public string SectionName { get; set; } = "";
    public string CoaCode { get; set; } = "";
    public bool ShowChild { get; set; }
    public double Total { get; set; }
    public List<BalanceSheetSection> SubSection { get; set; } = new();
}