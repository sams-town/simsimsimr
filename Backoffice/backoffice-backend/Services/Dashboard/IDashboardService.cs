using RSSMBackend.Models;

namespace RSSMBackend.Services;

public interface IDashboardService
{
    Task<DashboardSummaryDto> GetSummaryAsync(DateTime startDate, DateTime endDate, int? branchId = null);
    Task<DashboardFullDto> GetFullDashboardAsync(int? branchId = null);
}
