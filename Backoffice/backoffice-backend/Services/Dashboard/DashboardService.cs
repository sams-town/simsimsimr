using Microsoft.EntityFrameworkCore;
using RSSMBackend.DbContext;
using RSSMBackend.Models;
using RSSMBackend.Entities;
using System.Linq.Expressions;

namespace RSSMBackend.Services;

public class DashboardService : IDashboardService
{
    private readonly RSSMDbContext _context;
    private IOptionService optionService;

    public DashboardService(RSSMDbContext context, IOptionService optionService)
    {
        _context = context;
        this.optionService = optionService;
    }

    // Helper: get option value with fallback
    private async Task<string> GetOptionValue(string name, int branchId, string fallback)
    {
        var opt = await optionService.GetByName(name, branchId);
        return !string.IsNullOrEmpty(opt?.OptionValue) ? opt.OptionValue : fallback;
    }

    public async Task<DashboardSummaryDto> GetSummaryAsync(DateTime startDate, DateTime endDate, int? branchId = null)
    {
        var CashAccount = await optionService.GetByName("ACCOUNT_CASH", branchId ?? 0);
        var BankAccount = await optionService.GetByName("ACCOUNT_BANK", branchId ?? 0);
        var ReceivableAccount = await optionService.GetByName("ACCOUNT_RECEIVABLE", branchId ?? 0);
        var PayableAccount = await optionService.GetByName("ACCOUNT_PAYABLE", branchId ?? 0);

        string[] CashAccounts = CashAccount?.OptionValue?.Split(',', StringSplitOptions.RemoveEmptyEntries)
                                .Select(x => x.Trim())
                                .Distinct()
                                .ToArray() ?? Array.Empty<string>();

        string[] BankAccounts = BankAccount?.OptionValue?.Split(',', StringSplitOptions.RemoveEmptyEntries)
                                .Select(x => x.Trim())
                                .Distinct()
                                .ToArray() ?? Array.Empty<string>();
        string[] ReceivableAccounts = ReceivableAccount?.OptionValue?.Split(',', StringSplitOptions.RemoveEmptyEntries)
                                .Select(x => x.Trim())
                                .Distinct()
                                .ToArray() ?? Array.Empty<string>();
        string[] PayableAccounts = PayableAccount?.OptionValue?.Split(',', StringSplitOptions.RemoveEmptyEntries)
                                .Select(x => x.Trim())
                                .Distinct()
                                .ToArray() ?? Array.Empty<string>();


        double totalCash = await SumFromCoaArray(CashAccounts, startDate, endDate, branchId);
        double totalBank = await SumFromCoaArray(BankAccounts, startDate, endDate, branchId);
        double totalReceivable = await SumFromCoaArray(ReceivableAccounts, startDate, endDate, branchId);
        double totalPayable = await SumFromCoaArray(PayableAccounts, startDate, endDate, branchId);

        return new DashboardSummaryDto
        {
            TotalCashOnHand = totalCash,
            TotalBankBalance = totalBank,
            TotalReceivable = totalReceivable,
            TotalPayable = totalPayable
        };
    }

    public async Task<double> SumFromCoaArray(string[] coaArray, DateTime startDate, DateTime endDate, int? branchId = null)
    {
        if (coaArray == null || coaArray.Length == 0) return 0;

        return await _context.Journals
            .Include(j => j.Transaction)
            .Where(j =>
                coaArray.Any(prefix => j.CoaCode != null && j.CoaCode.StartsWith(prefix)) &&
                j.Transaction != null &&
                j.Transaction.TransactionDate >= startDate &&
                j.Transaction.TransactionDate <= endDate &&
                j.status == "Published"
            )
            .SumAsync(j => (j.DebitAmount ?? 0) - (j.CreditAmount ?? 0));
    }

    private async Task<MetricData> GetMetricByFilterAsync(Expression<Func<Journal, bool>> filter, DateTime today, DateTime yesterday, DateTime thisMonthStart, DateTime lastMonthStart, DateTime lastMonthEnd, int? branchId)
    {
        var query = _context.Journals
            .Include(j => j.Transaction)
            .Where(j => j.status == "Published" && j.Transaction != null)
            .Where(filter);

        if (branchId.HasValue && branchId.Value > 0)
            query = query.Where(j => j.BranchId == branchId.Value);

        var todayStart = today;
        var todayEnd = today.AddDays(1).AddTicks(-1);
        var yesterdayStart = yesterday;
        var yesterdayEnd = yesterday.AddDays(1).AddTicks(-1);
        var thisMonthEnd = DateTime.UtcNow;

        double todayValue = await query.Where(j => j.Transaction!.TransactionDate >= todayStart && j.Transaction!.TransactionDate <= todayEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        double yesterdayValue = await query.Where(j => j.Transaction!.TransactionDate >= yesterdayStart && j.Transaction!.TransactionDate <= yesterdayEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        double thisMonthValue = await query.Where(j => j.Transaction!.TransactionDate >= thisMonthStart && j.Transaction!.TransactionDate <= thisMonthEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        double lastMonthValue = await query.Where(j => j.Transaction!.TransactionDate >= lastMonthStart && j.Transaction!.TransactionDate <= lastMonthEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        return new MetricData
        {
            Today = todayValue,
            Yesterday = yesterdayValue,
            ThisMonth = thisMonthValue,
            LastMonth = lastMonthValue
        };
    }

    public async Task<DashboardFullDto> GetFullDashboardAsync(int? branchId = null)
    {
        var now = DateTime.UtcNow;
        var today = now.Date;
        var yesterday = today.AddDays(-1);
        var thisMonthStart = new DateTime(now.Year, now.Month, 1, 0, 0, 0, DateTimeKind.Utc);
        var lastMonthStart = thisMonthStart.AddMonths(-1);
        var lastMonthEnd = thisMonthStart.AddTicks(-1);
        int bid = branchId ?? 0;

        // Load all config from options
        string revenuePrefix = await GetOptionValue("COA_PREFIX_REVENUE", bid, "4");
        string expensePrefixRaw = await GetOptionValue("COA_PREFIX_EXPENSE", bid, "5,6");
        string[] expensePrefixes = expensePrefixRaw.Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()).ToArray();
        string opdPrefix = await GetOptionValue("COA_PREFIX_REVENUE_OPD", bid, "401201");
        string ipdPrefix = await GetOptionValue("COA_PREFIX_REVENUE_IPD", bid, "401101");
        string clinicCodeIgd = await GetOptionValue("CLINIC_CODE_IGD", bid, "IGDK");
        string clinicCodeFisio = await GetOptionValue("CLINIC_CODE_FISIO", bid, "FISIO");
        string payerCodeBpjs = await GetOptionValue("PAYER_CODE_BPJS", bid, "BPJ");

        var departmentFilters = new Dictionary<string, string[]>
        {
            { "Pharmacy", new[] { "Farmasi", "Obat", "Apotik" } },
            { "Laboratory", new[] { "Lab", "Laboratorium" } },
            { "Radiology", new[] { "Radiologi", "Rontgen", "X-Ray" } },
        };

        var result = new DashboardFullDto();

        // Revenue metric
        result.Metrics.Revenue = await GetMetricDataAsync(revenuePrefix, today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId, true);

        // Rajal: opdPrefix AND ClinicCode != IGD
        result.Metrics.Outpatient = await GetMetricByFilterAsync(
            j => j.CoaCode != null && j.CoaCode.StartsWith(opdPrefix) && j.ClinicCode != clinicCodeIgd,
            today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);

        // Ranap: ipdPrefix
        result.Metrics.Inpatient = await GetMetricByFilterAsync(
            j => j.CoaCode != null && j.CoaCode.StartsWith(ipdPrefix),
            today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);

        // IGD: (ipdPrefix OR opdPrefix) AND ClinicCode == IGD
        result.Metrics.Igd = await GetMetricByFilterAsync(
            j => j.CoaCode != null && (j.CoaCode.StartsWith(ipdPrefix) || j.CoaCode.StartsWith(opdPrefix)) && j.ClinicCode == clinicCodeIgd,
            today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);

        // Polyclinic: (ipdPrefix OR opdPrefix) AND ClinicName starts with "poli"
        result.Metrics.Polyclinic = await GetMetricByFilterAsync(
             j => j.CoaCode != null && (j.CoaCode.StartsWith(ipdPrefix) || j.CoaCode.StartsWith(opdPrefix)) &&
                  j.ClinicName != null && j.ClinicName.ToLower().StartsWith("poli"),
             today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);

        // Department metrics
        result.Metrics.Pharmacy = await GetDepartmentMetricAsync(revenuePrefix, departmentFilters["Pharmacy"], today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);
        result.Metrics.Laboratory = await GetDepartmentMetricAsync(revenuePrefix, departmentFilters["Laboratory"], today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);
        result.Metrics.Radiology = await GetDepartmentMetricAsync(revenuePrefix, departmentFilters["Radiology"], today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);

        // Physiotherapy: (ipdPrefix OR opdPrefix) AND ClinicCode == FISIO
        result.Metrics.Physiotherapy = await GetMetricByFilterAsync(
             j => j.CoaCode != null && (j.CoaCode.StartsWith(ipdPrefix) || j.CoaCode.StartsWith(opdPrefix)) && j.ClinicCode == clinicCodeFisio,
             today, yesterday, thisMonthStart, lastMonthStart, lastMonthEnd, branchId);

        // Charts
        result.MonthlyTrend = await GetMonthlyTrendAsync(now.Year, branchId, revenuePrefix, expensePrefixes);
        result.RevenueComposition = await GetRevenueCompositionAsync(thisMonthStart, today, branchId, revenuePrefix, opdPrefix, ipdPrefix, clinicCodeIgd);
        result.TopRevenueAccounts = await GetTopRevenueAccountsAsync(thisMonthStart, today, 5, branchId, revenuePrefix);
        result.PaymentMethods = await GetPaymentMethodsAsync(thisMonthStart, today, branchId, revenuePrefix, payerCodeBpjs);

        return result;
    }

    private async Task<MetricData> GetMetricDataAsync(string coaPrefix, DateTime today, DateTime yesterday, DateTime thisMonthStart, DateTime lastMonthStart, DateTime lastMonthEnd, int? branchId, bool isRevenue)
    {
        var query = _context.Journals
            .Include(j => j.Transaction)
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(coaPrefix) &&
                       j.status == "Published" && j.Transaction != null);

        if (branchId.HasValue && branchId.Value > 0)
            query = query.Where(j => j.BranchId == branchId.Value);

        var todayStart = today;
        var todayEnd = today.AddDays(1).AddTicks(-1);
        var yesterdayStart = yesterday;
        var yesterdayEnd = yesterday.AddDays(1).AddTicks(-1);
        var thisMonthEnd = DateTime.UtcNow;

        double todayValue = await query.Where(j => j.Transaction!.TransactionDate >= todayStart && j.Transaction!.TransactionDate <= todayEnd)
            .SumAsync(j => isRevenue ? (j.CreditAmount ?? 0) : (j.DebitAmount ?? 0));

        double yesterdayValue = await query.Where(j => j.Transaction!.TransactionDate >= yesterdayStart && j.Transaction!.TransactionDate <= yesterdayEnd)
            .SumAsync(j => isRevenue ? (j.CreditAmount ?? 0) : (j.DebitAmount ?? 0));

        double thisMonthValue = await query.Where(j => j.Transaction!.TransactionDate >= thisMonthStart && j.Transaction!.TransactionDate <= thisMonthEnd)
            .SumAsync(j => isRevenue ? (j.CreditAmount ?? 0) : (j.DebitAmount ?? 0));

        double lastMonthValue = await query.Where(j => j.Transaction!.TransactionDate >= lastMonthStart && j.Transaction!.TransactionDate <= lastMonthEnd)
            .SumAsync(j => isRevenue ? (j.CreditAmount ?? 0) : (j.DebitAmount ?? 0));

        return new MetricData
        {
            Today = todayValue,
            Yesterday = yesterdayValue,
            ThisMonth = thisMonthValue,
            LastMonth = lastMonthValue
        };
    }

    private async Task<MetricData> GetDepartmentMetricAsync(string coaPrefix, string[] keywords, DateTime today, DateTime yesterday, DateTime thisMonthStart, DateTime lastMonthStart, DateTime lastMonthEnd, int? branchId)
    {
        var query = _context.Journals
            .Include(j => j.Transaction)
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(coaPrefix) &&
                       j.status == "Published" && j.Transaction != null &&
                       keywords.Any(k => (j.CoaName ?? "").ToLower().Contains(k.ToLower())));

        if (branchId.HasValue && branchId.Value > 0)
            query = query.Where(j => j.BranchId == branchId.Value);

        var todayStart = today;
        var todayEnd = today.AddDays(1).AddTicks(-1);
        var yesterdayStart = yesterday;
        var yesterdayEnd = yesterday.AddDays(1).AddTicks(-1);
        var thisMonthEnd = DateTime.UtcNow;

        double todayValue = await query.Where(j => j.Transaction!.TransactionDate >= todayStart && j.Transaction!.TransactionDate <= todayEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        double yesterdayValue = await query.Where(j => j.Transaction!.TransactionDate >= yesterdayStart && j.Transaction!.TransactionDate <= yesterdayEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        double thisMonthValue = await query.Where(j => j.Transaction!.TransactionDate >= thisMonthStart && j.Transaction!.TransactionDate <= thisMonthEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        double lastMonthValue = await query.Where(j => j.Transaction!.TransactionDate >= lastMonthStart && j.Transaction!.TransactionDate <= lastMonthEnd)
            .SumAsync(j => j.CreditAmount ?? 0);

        return new MetricData
        {
            Today = todayValue,
            Yesterday = yesterdayValue,
            ThisMonth = thisMonthValue,
            LastMonth = lastMonthValue
        };
    }

    private async Task<MonthlyTrendDto> GetMonthlyTrendAsync(int year, int? branchId, string revenuePrefix, string[] expensePrefixes)
    {
        var result = new MonthlyTrendDto();
        var monthNames = new[] { "Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec" };

        for (int month = 1; month <= 12; month++)
        {
            var monthStart = new DateTime(year, month, 1, 0, 0, 0, DateTimeKind.Utc);
            var monthEnd = monthStart.AddMonths(1).AddTicks(-1);

            var revenueQuery = _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix) &&
                           j.status == "Published" && j.Transaction != null &&
                           j.Transaction.TransactionDate >= monthStart &&
                           j.Transaction.TransactionDate <= monthEnd);

            var expenseQuery = _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.CoaCode != null && expensePrefixes.Any(p => j.CoaCode.StartsWith(p)) &&
                           j.status == "Published" && j.Transaction != null &&
                           j.Transaction.TransactionDate >= monthStart &&
                           j.Transaction.TransactionDate <= monthEnd);

            if (branchId.HasValue && branchId.Value > 0)
            {
                revenueQuery = revenueQuery.Where(j => j.BranchId == branchId.Value);
                expenseQuery = expenseQuery.Where(j => j.BranchId == branchId.Value);
            }

            double revenue = await revenueQuery.SumAsync(j => j.CreditAmount ?? 0);
            double expense = await expenseQuery.SumAsync(j => j.DebitAmount ?? 0);

            result.Months.Add(monthNames[month - 1]);
            result.Revenue.Add(Math.Round(revenue / 1_000_000, 1));
            result.Expense.Add(Math.Round(expense / 1_000_000, 1));
        }

        return result;
    }

    private async Task<RevenueCompositionDto> GetRevenueCompositionAsync(DateTime startDate, DateTime endDate, int? branchId, string revenuePrefix, string opdPrefix, string ipdPrefix, string clinicCodeIgd)
    {
        var result = new RevenueCompositionDto();
        double total = 0;
        var values = new List<(string name, double value)>();

        var queryBase = _context.Journals
            .Include(j => j.Transaction)
            .Where(j => j.status == "Published" && j.Transaction != null &&
                        j.Transaction.TransactionDate >= startDate &&
                        j.Transaction.TransactionDate <= endDate);

        if (branchId.HasValue && branchId.Value > 0)
            queryBase = queryBase.Where(j => j.BranchId == branchId.Value);

        // 1. Rawat Jalan (Rajal): opdPrefix && ClinicCode != IGD
        double valRajal = await queryBase
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(opdPrefix) && j.ClinicCode != clinicCodeIgd)
            .SumAsync(j => j.CreditAmount ?? 0);
        values.Add(("Rawat Jalan", valRajal));
        total += valRajal;

        // 2. IGD: ipdPrefix && ClinicCode == IGD
        double valIGD = await queryBase
             .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(ipdPrefix) && j.ClinicCode == clinicCodeIgd)
             .SumAsync(j => j.CreditAmount ?? 0);
        values.Add(("IGD", valIGD));
        total += valIGD;

        // 3. Rawat Inap (Ranap): ipdPrefix AND ClinicCode != IGD
        double valRanap = await queryBase
             .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(ipdPrefix) && j.ClinicCode != clinicCodeIgd)
             .SumAsync(j => j.CreditAmount ?? 0);
        values.Add(("Rawat Inap", valRanap));
        total += valRanap;

        // 4. Farmasi (Keywords)
        double valFarmasi = await queryBase
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix) &&
                   new[] { "Farmasi", "Obat" }.Any(k => (j.CoaName ?? "").ToLower().Contains(k.ToLower())))
            .SumAsync(j => j.CreditAmount ?? 0);
        values.Add(("Farmasi", valFarmasi));
        total += valFarmasi;

        // 5. Lab (Keywords)
        double valLab = await queryBase
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix) &&
                   new[] { "Lab", "Laboratorium" }.Any(k => (j.CoaName ?? "").ToLower().Contains(k.ToLower())))
            .SumAsync(j => j.CreditAmount ?? 0);
        values.Add(("Lab", valLab));
        total += valLab;

        // 6. Radiologi (Keywords)
        double valRad = await queryBase
           .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix) &&
                  new[] { "Radiologi", "Rontgen" }.Any(k => (j.CoaName ?? "").ToLower().Contains(k.ToLower())))
           .SumAsync(j => j.CreditAmount ?? 0);
        values.Add(("Radiologi", valRad));
        total += valRad;

        // Total revenue for "Lainnya"
        double totalRevenue = await queryBase
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix))
            .SumAsync(j => j.CreditAmount ?? 0);

        double lainnya = totalRevenue - total;
        if (lainnya > 0)
        {
            values.Add(("Lainnya", lainnya));
        }

        // Convert to percentages
        if (totalRevenue > 0)
        {
            foreach (var v in values)
            {
                result.Labels.Add(v.name);
                result.Series.Add(Math.Round((v.value / totalRevenue) * 100, 0));
            }
        }

        return result;
    }

    private async Task<List<TopRevenueAccountDto>> GetTopRevenueAccountsAsync(DateTime startDate, DateTime endDate, int limit, int? branchId, string revenuePrefix)
    {
        var query = _context.Journals
            .Include(j => j.Transaction)
            .Include(j => j.Coa)
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix) &&
                       j.status == "Published" && j.Transaction != null &&
                       j.Transaction.TransactionDate >= startDate &&
                       j.Transaction.TransactionDate <= endDate);

        if (branchId.HasValue && branchId.Value > 0)
            query = query.Where(j => j.BranchId == branchId.Value);

        var grouped = await query
            .GroupBy(j => new { j.CoaCode, j.CoaName, j.CoaParentName })
            .Select(g => new
            {
                g.Key.CoaCode,
                g.Key.CoaName,
                g.Key.CoaParentName,
                Revenue = g.Sum(j => j.CreditAmount ?? 0)
            })
            .OrderByDescending(x => x.Revenue)
            .Take(limit)
            .ToListAsync();

        return grouped.Select((x, idx) => new TopRevenueAccountDto
        {
            Id = idx + 1,
            CoaCode = x.CoaCode,
            AccountName = x.CoaName,
            Group = x.CoaParentName ?? "Pendapatan",
            Revenue = x.Revenue
        }).ToList();
    }

    private async Task<PaymentMethodsDto> GetPaymentMethodsAsync(DateTime startDate, DateTime endDate, int? branchId, string revenuePrefix, string payerCodeBpjs)
    {
        var result = new PaymentMethodsDto();

        var baseQuery = _context.Journals
            .Include(j => j.Transaction)
            .Where(j => j.CoaCode != null && j.CoaCode.StartsWith(revenuePrefix) &&
                       j.status == "Published" && j.Transaction != null &&
                       j.Transaction.TransactionDate >= startDate &&
                       j.Transaction.TransactionDate <= endDate);

        if (branchId.HasValue && branchId.Value > 0)
            baseQuery = baseQuery.Where(j => j.BranchId == branchId.Value);

        // Get BPJS revenue (filter by payer_code from config)
        double bpjsRevenue = await baseQuery
            .Where(j => j.PayerCode == payerCodeBpjs)
            .SumAsync(j => j.CreditAmount ?? 0);

        // Get total revenue
        double totalRevenue = await baseQuery.SumAsync(j => j.CreditAmount ?? 0);

        // Non-BPJS = Total - BPJS
        double nonBpjsRevenue = totalRevenue - bpjsRevenue;

        // Calculate percentages
        if (totalRevenue > 0)
        {
            double bpjsPercent = Math.Round((bpjsRevenue / totalRevenue) * 100, 0);
            double nonBpjsPercent = 100 - bpjsPercent;

            result.Labels = new List<string> { "BPJS",  "Non-BPJS" };
            result.Series = new List<double> { bpjsPercent, nonBpjsPercent };
        }
        else
        {
            result.Labels = new List<string> { "BPJS", "Non-BPJS" };
            result.Series = new List<double> { 50, 50 };
        }

        return result;
    }
}
