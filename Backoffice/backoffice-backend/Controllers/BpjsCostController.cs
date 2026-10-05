using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using Action = RSSMBackend.Models.Action;

namespace RSSMBackend.Controllers;

[ApiController]
[Route("[controller]")]
public class BpjsCostController : BaseController
{
    private readonly IBpjsCostService bpjsCostService;
    private readonly ILogger<BpjsCostController> _logger;
    private const string TAG = "BpjsCost {0}";
    private const string LOG = "BpjsCost {x}";

    public BpjsCostController(IBpjsCostService bpjsCostService, ILogger<BpjsCostController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.bpjsCostService = bpjsCostService;
        _logger = logger;
    }

    [Authorize(Page.MasterBpjsCost, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery] string? keyword = null,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10,
        [FromQuery] string orderBy = "costid",
        [FromQuery] string orderType = "asc"
    )
    {
        try
        {
            var data = await bpjsCostService.GetAll(keyword, userSession.BranchId, orderBy, orderType, page, pageSize);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBpjsCost, Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery] string? keyword = null,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10,
        [FromQuery] string orderBy = "costid",
        [FromQuery] string orderType = "asc"
    )
    {
        try
        {
            var data = await bpjsCostService.GetOption(keyword, userSession.BranchId, orderBy, orderType, page, pageSize);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBpjsCost, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetByID(int id)
    {
        try
        {
            var data = await bpjsCostService.GetByID(id);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBpjsCost, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] BpjsCost item)
    {
        try
        {
            if (item == null) return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new BpjsCostValidation();
            var result = validator.Validate(item);
            if (!result.IsValid)
            {
                var errors = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errors);
            }

            item.BranchId = userSession.BranchId ?? 0;

            var id = await bpjsCostService.Add(item);
            return _Ok(id, string.Format(TAG, "data has been successfully saved"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBpjsCost, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] BpjsCost item)
    {
        try
        {
            if (item == null) return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new BpjsCostValidation();
            var result = validator.Validate(item);
            if (!result.IsValid)
            {
                var errors = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errors);
            }

            item.BranchId = userSession.BranchId ?? 0;

            var id = await bpjsCostService.Update(item);
            return _Ok(id, string.Format(TAG, "data has been successfully updated"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBpjsCost, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Remove(int id)
    {
        try
        {
            var data = await bpjsCostService.GetByID(id);
            if (data == null) return _NotFound(string.Format(TAG, "BpjsCost ID Not Found"));

            var deletedId = await bpjsCostService.Remove(data);
            return _Ok(deletedId, string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBpjsCost, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery] string? keyword = null,
        [FromQuery] string orderBy = "costid",
        [FromQuery] string orderType = "asc",
        [FromQuery] string type = "xlsx"
    )
    {
        try
        {
            var data = await bpjsCostService.GetAll(keyword, userSession.BranchId, orderBy, orderType, -1, -1);
            if (data.Data == null) return _NotFound("Data not found");

            byte[] fileContent;
            string fileName = "BpjsCost.";

            if (type.ToLower() == "xlsx")
            {
                fileContent = ExportToDocs.ConvertToExcel<BpjsCost>(data.Data);
                fileName += "xlsx";
            }
            else
            {
                fileContent = ExportToDocs.ConvertToCsv<BpjsCost>(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}
