namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.DTOs.MigrationJob;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.Services.MigrationJob;

[ApiController]
[Route("[controller]")]
public class MigrationJobController : BaseController
{
    private readonly IMigrationJobService _svc;
    private readonly ILogger<MigrationJobController> _logger;
    private const string TAG = "MIGRATION_JOB {0}";
    private const string LOG = "MIGRATION_JOB {x}";

    public MigrationJobController(
        IMigrationJobService svc,
        ILogger<MigrationJobController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _svc = svc;
        _logger = logger;
    }

    [Authorize(Page.CfgMigrationJob, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "scriptName")] string? scriptName = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "requestedAt",
        [FromQuery(Name = "orderType")] string? orderType = "desc")
    {
        try
        {
            var data = await _svc.GetAll(scriptName, status, page, pageSize, orderBy, orderType);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgMigrationJob, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetById(int id)
    {
        try
        {
            var data = await _svc.GetById(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "not found"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgMigrationJob, Action.Read)]
    [HttpGet("scripts")]
    public IActionResult GetScripts()
    {
        return _Ok(_svc.AllowedScripts);
    }

    [Authorize(Page.CfgMigrationJob, Action.Read)]
    [HttpGet("status")]
    public async Task<IActionResult> GetStatus()
    {
        try
        {
            var data = await _svc.GetRunningStatus();
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgMigrationJob, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateMigrationJobDto dto)
    {
        try
        {
            var data = await _svc.Create(dto, userSession);
            return _Ok(data, string.Format(TAG, "job has been queued"));
        }
        catch (ApplicationException ae)
        {
            return _BadRequest(ae.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}
