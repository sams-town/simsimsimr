using RSSMBackend.Models;

namespace RSSMBackend.DTOs.Journal
{
    public class CashFlowResponse
    {
        public CashFlowSummary Summary { get; set; } = new CashFlowSummary();
        public List<BalanceSheetSection> Data { get; set; } = new List<BalanceSheetSection>();
    }

    public class CashFlowSummary
    {
        public double OperatingActivities { get; set; }
        public double InvestingActivities { get; set; }
        public double FinancingActivities { get; set; }
        public double NetCashFlow { get; set; }
        public double CashBeginning { get; set; }
        public double CashEnding { get; set; }
    }
}
