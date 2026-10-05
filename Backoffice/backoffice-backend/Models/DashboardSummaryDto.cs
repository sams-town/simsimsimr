namespace RSSMBackend.Models;

public class DashboardSummaryDto
{
    public double TotalCashOnHand { get; set; }
    public double TotalBankBalance { get; set; }
    public double TotalReceivable { get; set; }
    public double TotalPayable { get; set; }
    public double TotalIncomeMonth { get; set; }
    public double TotalExpenseMonth { get; set; }
}

public class DashboardMetricsDto
{
    public MetricData Revenue { get; set; } = new();
    public MetricData Outpatient { get; set; } = new();
    public MetricData Inpatient { get; set; } = new();
    public MetricData Igd { get; set; } = new();
    public MetricData Polyclinic { get; set; } = new();
    public MetricData Pharmacy { get; set; } = new();
    public MetricData Laboratory { get; set; } = new();
    public MetricData Radiology { get; set; } = new();
    public MetricData Physiotherapy { get; set; } = new();
}

public class MetricData
{
    public double Today { get; set; }
    public double Yesterday { get; set; }
    public double ThisMonth { get; set; }
    public double LastMonth { get; set; }
}

public class MonthlyTrendDto
{
    public List<string> Months { get; set; } = new();
    public List<double> Revenue { get; set; } = new();
    public List<double> Expense { get; set; } = new();
}

public class RevenueCompositionDto
{
    public List<string> Labels { get; set; } = new();
    public List<double> Series { get; set; } = new();
}

public class TopRevenueAccountDto
{
    public int Id { get; set; }
    public string? CoaCode { get; set; }
    public string? AccountName { get; set; }
    public string? Group { get; set; }
    public double Revenue { get; set; }
}

public class DashboardFullDto
{
    public DashboardMetricsDto Metrics { get; set; } = new();
    public MonthlyTrendDto MonthlyTrend { get; set; } = new();
    public RevenueCompositionDto RevenueComposition { get; set; } = new();
    public List<TopRevenueAccountDto> TopRevenueAccounts { get; set; } = new();
    public PaymentMethodsDto PaymentMethods { get; set; } = new();
}

public class PaymentMethodsDto
{
    public List<string> Labels { get; set; } = new();
    public List<double> Series { get; set; } = new();
}

