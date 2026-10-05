namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;

[ApiController]
[Route("[controller]")]
public class AuditLogController : BaseController
{
    private readonly IAuditLogService _auditLogService;
    private readonly ILogger<AuditLogController> _logger;

    // Administrator, Manager, Supervisor. Staff (4) and Petty Cash (5) never see the trail.
    private static readonly int[] TimelineRoles = { 1, 2, 3 };

    // Administrator only.
    private const int AdministratorRole = 1;

    public AuditLogController(
        IAuditLogService auditLogService,
        ILogger<AuditLogController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _auditLogService = auditLogService;
        _logger = logger;
    }

    /// <summary>
    /// All logged events for one record, newest first. Powers the Riwayat drawer.
    /// </summary>
    [HttpGet("Timeline")]
    public async Task<IActionResult> GetTimeline(
        [FromQuery(Name = "module")] string? module,
        [FromQuery(Name = "refId")] string? refId)
    {
        if (!HasRole(TimelineRoles)) return Unauthorized(new { message = "Unauthorized" });

        if (string.IsNullOrWhiteSpace(module) || string.IsNullOrWhiteSpace(refId))
            return _BadRequest("module and refId are required");

        try
        {
            var data = await _auditLogService.GetTimeline(module, refId);
            return _Ok(data.Select(ToTimelineDto));
        }
        catch (Exception e)
        {
            _logger.LogError(e, "AUDIT_LOG timeline failed for {Module}/{RefId}", module, refId);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Paged, filterable listing for the Audit Trail settings tab.
    /// </summary>
    [HttpGet("List")]
    public async Task<IActionResult> GetList(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "module")] string? module = null,
        [FromQuery(Name = "operation")] string? operation = null,
        [FromQuery(Name = "userId")] int? userId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "branchId")] int? branchId = null,
        [FromQuery(Name = "from")] DateTime? from = null,
        [FromQuery(Name = "to")] DateTime? to = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "created_at",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        if (!HasRole(AdministratorRole)) return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var result = await _auditLogService.GetAll(
                keyword, branchId, status, from, to, module, operation, userId,
                orderBy, orderType, page, pageSize);

            return _Ok(result.Data.Select(ToListDto), result.TotalData);
        }
        catch (Exception e)
        {
            _logger.LogError(e, "AUDIT_LOG list failed");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Full row including payload, for the detail popup.
    /// </summary>
    [HttpGet("Detail/{id}")]
    public async Task<IActionResult> GetDetail(long id)
    {
        if (!HasRole(AdministratorRole)) return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var item = await _auditLogService.GetByID(id);
            if (item == null) return _NotFound("Audit log not found");

            return _Ok(new
            {
                id = item.Id,
                module = item.Module,
                refId = item.RefId,
                operation = item.Operation,
                userId = item.UserId,
                userName = item.UserName,
                roleName = item.RoleName,
                branchId = item.BranchId,
                branchName = item.BranchName,
                ipAddress = item.IpAddress,
                status = item.Status,
                createdAt = item.CreatedAt,
                beforeData = item.BeforeData,
                afterData = item.AfterData
            });
        }
        catch (Exception e)
        {
            _logger.LogError(e, "AUDIT_LOG detail failed for {Id}", id);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Distinct modules / operations / actors for the filter dropdowns.
    /// </summary>
    [HttpGet("Options")]
    public async Task<IActionResult> GetOptions()
    {
        if (!HasRole(AdministratorRole)) return Unauthorized(new { message = "Unauthorized" });

        try
        {
            return _Ok(await _auditLogService.GetFilterOptions());
        }
        catch (Exception e)
        {
            _logger.LogError(e, "AUDIT_LOG options failed");
            return _BadRequest(e.Message);
        }
    }

    // Deliberately not using [Authorize(Page, Action)]: that filter's OnAuthorization is
    // `async void` (Helpers/AuthorizeAttribute.cs:25), so the pipeline does not await it and
    // the action can run before context.Result is set. These checks are synchronous and
    // return before any work happens.
    private bool HasRole(params int[] allowed) =>
        userSession != null && allowed.Contains(userSession.RoleID);

    private static object ToTimelineDto(Entities.AuditLog x) => new
    {
        id = x.Id,
        operation = x.Operation,
        userId = x.UserId,
        userName = x.UserName,
        roleName = x.RoleName,
        status = x.Status,
        createdAt = x.CreatedAt,
        detail = x.AfterData
    };

    private static object ToListDto(Entities.AuditLog x) => new
    {
        id = x.Id,
        module = x.Module,
        refId = x.RefId,
        operation = x.Operation,
        userId = x.UserId,
        userName = x.UserName,
        roleName = x.RoleName,
        branchId = x.BranchId,
        branchName = x.BranchName,
        ipAddress = x.IpAddress,
        status = x.Status,
        createdAt = x.CreatedAt
    };
}
