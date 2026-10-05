namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

// Modul Tutup Buku (Period Closing). Bulanan/tahunan, kunci periode + reopen, audit.
// Riwayat/timeline dibaca dari modul AuditLog (module = 'PeriodClosing').
[ApiController]
[Route("[controller]")]
public class PeriodClosingController : BaseController
{
    private readonly IPeriodClosingService _service;
    private readonly ILogger<PeriodClosingController> _logger;
    private const string TAG = "PeriodClosing {0}";
    private const string LOG = "PeriodClosing {x}";

    public PeriodClosingController(IPeriodClosingService service, ILogger<PeriodClosingController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
        _logger = logger;
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "periodType")] string? periodType = null,
        [FromQuery(Name = "year")] int? year = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await _service.GetAll(periodType, year, status, userSession.BranchId, page, pageSize);
            return _Ok(data.Data, data.TotalData);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
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

    // Cek status tutup buku untuk sebuah tanggal (dipakai FE untuk mengunci form input jurnal).
    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("Status")]
    public async Task<IActionResult> GetStatus([FromQuery(Name = "date")] DateTime date)
    {
        try
        {
            bool closed = await _service.IsPeriodClosed(date, userSession.BranchId);
            return _Ok(new { date, branchId = userSession.BranchId, closed });
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Update)]
    [HttpPost("Close")]
    public async Task<IActionResult> Close([FromBody] PeriodClosingRequest request)
    {
        try
        {
            var data = await _service.ClosePeriod(request, userSession.BranchId, userSession.UserID,
                HttpContext.Connection.RemoteIpAddress?.ToString() ?? "");
            return _Ok(data, $"Tutup buku {(request.PeriodType == "Yearly" ? request.Year.ToString() : $"{request.Year}-{request.Month:D2}")} berhasil");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Update)]
    [HttpPost("Reopen")]
    public async Task<IActionResult> Reopen([FromBody] ReopenPeriodRequest request)
    {
        try
        {
            var data = await _service.ReopenPeriod(request.Id, request.Note, userSession.UserID,
                HttpContext.Connection.RemoteIpAddress?.ToString() ?? "");
            return _Ok(data, "Periode berhasil dibuka kembali");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}
