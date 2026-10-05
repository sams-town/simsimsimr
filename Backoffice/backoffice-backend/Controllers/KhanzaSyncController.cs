namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.DTOs.KhanzaSync;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.Services.KhanzaSync;

[ApiController]
[Route("[controller]")]
public class KhanzaSyncController : BaseController
{
    private readonly IKhanzaSyncService _svc;
    private readonly ILogger<KhanzaSyncController> _logger;
    private const string LOG = "KHANZA_SYNC {x}";

    public KhanzaSyncController(
        IKhanzaSyncService svc,
        ILogger<KhanzaSyncController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _svc = svc;
        _logger = logger;
    }

    [Authorize(Page.CfgKhanzaSync, Action.Read)]
    [HttpGet("modules")]
    public IActionResult GetModules() => _Ok(_svc.GetModules());

    [Authorize(Page.CfgKhanzaSync, Action.Read)]
    [HttpGet("{module}/records")]
    public async Task<IActionResult> GetRecords(
        string module,
        [FromQuery(Name = "search")] string? search = null,
        [FromQuery(Name = "lockState")] string? lockState = "editable",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 20)
    {
        try
        {
            var data = await _svc.GetRecords(module, search, lockState, page, pageSize, userSession.RoleID);
            return _Ok(data);
        }
        catch (ApplicationException ae)
        {
            return _BadRequest(ae.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, LOG, "GetRecords");
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgKhanzaSync, Action.Read)]
    [HttpPost("{module}/compare")]
    public async Task<IActionResult> Compare(string module, [FromBody] SyncCompareRequestDto request)
    {
        try
        {
            var data = await _svc.Compare(module, request?.Keys ?? new(), userSession.RoleID, userSession.BranchId ?? 1);
            return _Ok(data);
        }
        catch (ApplicationException ae)
        {
            return _BadRequest(ae.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, LOG, "Compare");
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.CfgKhanzaSync, Action.Update)]
    [HttpPost("{module}/apply")]
    public async Task<IActionResult> Apply(string module, [FromBody] SyncApplyRequestDto request)
    {
        try
        {
            var data = await _svc.Apply(module, request ?? new(), userSession.RoleID, userSession.BranchId ?? 1, userSession.UserID);
            var ok = data.Count(r => r.Success);
            return _Ok(data, $"{ok} dari {data.Count} data berhasil diterapkan");
        }
        catch (ApplicationException ae)
        {
            return _BadRequest(ae.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, LOG, "Apply");
            return _BadRequest(e.Message);
        }
    }
}
