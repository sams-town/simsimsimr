using RSSMBackend.Models;

namespace RSSMBackend.DTOs.Journal
{
    public class ProfitLossResponse
    {
        public ProfitLossSummary Summary { get; set; } = new ProfitLossSummary();
        public List<BalanceSheetSection> Data { get; set; } = new List<BalanceSheetSection>();
    }

    public class ProfitLossSummary
    {
        public double TotalGrossProfit { get; set; } // Laba Kotor
        public double TotalOperatingProfit { get; set; } // Laba Operasional
        public double TotalNetProfit { get; set; } // Laba Bersih
    }
}
