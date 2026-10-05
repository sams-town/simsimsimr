namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.Services.JournalImport;

/// <summary>
/// Background queue for General Journal imports. Upload puts files on the queue; a Quartz
/// worker (Jobs/JournalImportWorker.cs) processes them one at a time and the frontend polls.
/// </summary>
[ApiController]
[Route("[controller]")]
public class JournalImportController : BaseController
{
    private readonly IJournalImportJobService _service;
    private readonly ILogger<JournalImportController> _logger;

    public JournalImportController(
        IJournalImportJobService service,
        ILogger<JournalImportController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
        _logger = logger;
    }

    /// <summary>Queue one or more Excel files.</summary>
    [Authorize(Page.TrxTransaction, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Enqueue(
        [FromForm] List<IFormFile> files,
        [FromForm(Name = "importType")] string importType = "standard")
    {
        // The [Authorize] filter in this codebase is async void, so the pipeline does not await
        // it and it cannot be relied on alone. This check runs before any work happens.
        if (userSession == null || userSession.UserID <= 0)
            return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var result = await _service.Enqueue(files, importType, userSession, userSession.BranchId ?? 0);
            return _Ok(result, $"{result.Queued.Count} file masuk antrian");
        }
        catch (ApplicationException ae)
        {
            return _BadRequest(ae.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, "JOURNAL_IMPORT enqueue failed");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Upload history, newest first.</summary>
    [Authorize(Page.TrxTransaction, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "importType")] string? importType = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "requestedAt",
        [FromQuery(Name = "orderType")] string? orderType = "desc")
    {
        if (userSession == null || userSession.UserID <= 0)
            return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var result = await _service.GetAll(status, importType, page, pageSize, orderBy, orderType);
            return _Ok(result.Data, result.TotalData);
        }
        catch (Exception e)
        {
            _logger.LogError(e, "JOURNAL_IMPORT list failed");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Pending/running counts. Cheap enough to poll; gates the frontend's interval.</summary>
    [Authorize(Page.TrxTransaction, Action.Read)]
    [HttpGet("status")]
    public async Task<IActionResult> GetQueueStatus()
    {
        if (userSession == null || userSession.UserID <= 0)
            return Unauthorized(new { message = "Unauthorized" });

        try
        {
            return _Ok(await _service.GetQueueStatus());
        }
        catch (Exception e)
        {
            _logger.LogError(e, "JOURNAL_IMPORT status failed");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Full detail including the per-row errors.</summary>
    // :int constraint so this can never shadow GET /JournalImport/status.
    [Authorize(Page.TrxTransaction, Action.Read)]
    [HttpGet("{id:int}")]
    public async Task<IActionResult> GetById(int id)
    {
        if (userSession == null || userSession.UserID <= 0)
            return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var data = await _service.GetById(id);
            if (data == null) return _NotFound("Import job tidak ditemukan");
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, "JOURNAL_IMPORT detail failed for {Id}", id);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Download the file as it was uploaded. Import files live outside wwwroot on purpose, so
    /// this endpoint is the only way to reach them and it stays behind the login check.
    /// </summary>
    [Authorize(Page.TrxTransaction, Action.Read)]
    [HttpGet("{id:int}/file")]
    public async Task<IActionResult> DownloadFile(int id)
    {
        if (userSession == null || userSession.UserID <= 0)
            return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var file = await _service.GetFileContent(id);
            if (file == null) return _NotFound("File tidak ditemukan");
            return _File(file.Value.Content, "xlsx", file.Value.FileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, "JOURNAL_IMPORT download failed for {Id}", id);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Cancel a job the worker has not started yet.</summary>
    [Authorize(Page.TrxTransaction, Action.Delete)]
    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Cancel(int id)
    {
        if (userSession == null || userSession.UserID <= 0)
            return Unauthorized(new { message = "Unauthorized" });

        try
        {
            var ok = await _service.Cancel(id);
            if (!ok) return _BadRequest("Job tidak ditemukan atau sudah diproses");
            return _Ok(new { id }, "Job dibatalkan");
        }
        catch (Exception e)
        {
            _logger.LogError(e, "JOURNAL_IMPORT cancel failed for {Id}", id);
            return _BadRequest(e.Message);
        }
    }
}
