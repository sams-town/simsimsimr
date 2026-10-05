using RSSMBackend.Models;

namespace RSSMBackend.DTOs.Journal
{
    public class BalanceSheetResponse
    {
        public BalanceSheetSummary Summary { get; set; } = new BalanceSheetSummary();
        public List<BalanceSheetSection> Data { get; set; } = new List<BalanceSheetSection>();
    }

    public class BalanceSheetSummary
    {
        public double TotalAssets { get; set; }
        public double TotalLiabilities { get; set; }
        public double TotalEquity { get; set; }
    }
}
