namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

// Modul Saldo Awal (Opening Balance). Menulis baris 'Opening' langsung ke public.trx_balance
// (lihat OpeningBalanceService), yang menjadi base checkpoint untuk perhitungan saldo on-read.
// Riwayat/timeline & audit dibaca dari modul AuditLog (module = 'OpeningBalance').
[ApiController]
[Route("[controller]")]
public class OpeningBalanceController : BaseController
{
    private readonly IOpeningBalanceService _service;
    private readonly ILogger<OpeningBalanceController> _logger;
    private const string TAG = "OpeningBalance {0}";
    private const string LOG = "OpeningBalance {x}";

    public OpeningBalanceController(IOpeningBalanceService service, ILogger<OpeningBalanceController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
        _logger = logger;
    }

    [Authorize(Page.TrxBank, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await _service.GetAll(coaCode, userSession.BranchId, startDate, endDate, page, pageSize);
            return _Ok(data.Data, data.TotalData);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxBank, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetDetail(int id)
    {
        try
        {
            var data = await _service.GetById(id);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxBank, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] OpeningBalanceRequest request)
    {
        try
        {
            request.Id = null;
            request.BranchId = userSession.BranchId;
            var data = await _service.Save(request, userSession.UserID, HttpContext.Connection.RemoteIpAddress?.ToString() ?? "");
            return _Ok(data, "Saldo awal berhasil disimpan");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxBank, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] OpeningBalanceRequest request)
    {
        try
        {
            if (request.Id == null) return _BadRequest("Id wajib diisi untuk update");
            request.BranchId = userSession.BranchId;
            var data = await _service.Save(request, userSession.UserID, HttpContext.Connection.RemoteIpAddress?.ToString() ?? "");
            return _Ok(data, "Saldo awal berhasil diperbarui");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxBank, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var ok = await _service.Remove(id, userSession.UserID, HttpContext.Connection.RemoteIpAddress?.ToString() ?? "");
            if (!ok) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(id, "Saldo awal berhasil dihapus");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}
