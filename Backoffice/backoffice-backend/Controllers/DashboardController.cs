using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using Action = RSSMBackend.Models.Action;

namespace RSSMBackend.Controllers;

[ApiController]
[Route("[controller]")]
public class DashboardController: BaseController
{
    private readonly IDashboardService _dashboardService;

    public DashboardController(IDashboardService dashboardService,IHttpContextAccessor httpContextAccessor) : base (httpContextAccessor){
        _dashboardService = dashboardService;
    }

    [HttpGet("summary")]
    [Authorize(Page.Dashboard, Action.Read)]
    public async Task<IActionResult> GetSummary( 
        [FromQuery(Name = "startDate")] DateTime startDate,
        [FromQuery(Name = "endDate")] DateTime endDate)
    {
        var result = await _dashboardService.GetSummaryAsync(startDate, endDate,userSession.BranchId);
        return _Ok(result, "Get Dashboard Summary Success");
    }

    [HttpGet("full")]
    [Authorize(Page.Dashboard, Action.Read)]
    public async Task<IActionResult> GetFullDashboard()
    {
        var result = await _dashboardService.GetFullDashboardAsync(userSession.BranchId);
        return _Ok(result, "Get Full Dashboard Success");
    }
}
