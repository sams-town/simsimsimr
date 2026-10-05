namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services.AccountReceivable;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.DTOs.ArCombinedVoucher;

[ApiController]
[Route("AccountReceivable/Combined")]
public class ArCombinedVoucherController : BaseController
{
    private readonly IArCombinedVoucherService _service;
    private readonly ILogger<ArCombinedVoucherController> _logger;
    private const string TAG = "ArCombinedVoucher {0}";

    public ArCombinedVoucherController(
        IArCombinedVoucherService service,
        ILogger<ArCombinedVoucherController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
        _logger = logger;
    }

    /// <summary>
    /// AR items eligible to be added to a combined voucher (finalized, not paid, not already batched).
    /// </summary>
    [HttpGet("EligibleItems")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetEligibleItems(
        [FromQuery(Name = "payerType")] string? payerType = null,
        [FromQuery(Name = "payerId")] string? payerId = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "excludeCombinedId")] int? excludeCombinedId = null)
    {
        try
        {
            var data = await _service.GetEligibleItems(payerType, payerId, startDate, endDate, keyword, page, pageSize, excludeCombinedId);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetEligibleItems Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>List of combined voucher batches.</summary>
    [HttpGet("List")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetList(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await _service.GetList(keyword, startDate, endDate, page, pageSize);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Create a new combined batch with initial AR items.</summary>
    [HttpPost("Create")]
    [Authorize(Page.TrxAccountReceivable, Action.Create)]
    public async Task<IActionResult> Create([FromBody] CreateCombinedVoucherRequest request)
    {
        try
        {
            if (request == null || request.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Minimal satu item harus dipilih"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var batchDate = request.BatchDate ?? DateTime.UtcNow;
            var data = await _service.Create(batchDate, request.Description, request.ArIds, userSession.UserID);
            return _Ok(data, string.Format(TAG, "Combined voucher dibuat"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Create Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Get a single combined batch with its items.</summary>
    [HttpGet("{id:int}")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetDetail(int id)
    {
        try
        {
            var data = await _service.GetDetail(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "Combined voucher tidak ditemukan"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetDetail Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Add AR items to an existing batch.</summary>
    [HttpPost("{id:int}/AddItems")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> AddItems(int id, [FromBody] CombinedVoucherItemsRequest request)
    {
        try
        {
            if (request == null || request.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Minimal satu item harus dipilih"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _service.AddItems(id, request.ArIds, userSession.UserID);
            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Item ditambahkan"));
            return _BadRequest(string.Format(TAG, "Tidak ada item valid untuk ditambahkan (mungkin sudah masuk batch lain atau sudah lunas)"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "AddItems Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Remove AR items from a batch.</summary>
    [HttpPost("{id:int}/RemoveItems")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> RemoveItems(int id, [FromBody] CombinedVoucherItemsRequest request)
    {
        try
        {
            if (request == null || request.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Minimal satu item harus dipilih"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _service.RemoveItems(id, request.ArIds, userSession.UserID);
            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Item dihapus dari batch"));
            return _BadRequest(string.Format(TAG, "Item tidak ditemukan di batch ini"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "RemoveItems Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Delete a combined batch.</summary>
    [HttpDelete("{id:int}")]
    [Authorize(Page.TrxAccountReceivable, Action.Delete)]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var result = await _service.Delete(id);
            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Combined voucher dihapus"));
            return _NotFound(string.Format(TAG, "Combined voucher tidak ditemukan"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Delete Error");
            return _BadRequest(e.Message);
        }
    }
}

