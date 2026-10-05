using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using RSSMBackend.Models;

namespace RSSMBackend.Controllers;

[ApiController]
[Route("[controller]")]
public class CostCenterController : BaseController {
    private readonly ICostCenterService costCenterService;
    private readonly ILogger<CostCenterController> _logger;
    private const string TAG = "CostCenter {0}";
    private const string LOG = "CostCenter {x}";

    public CostCenterController(ICostCenterService costCenterService, ILogger<CostCenterController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)  {
        this.costCenterService = costCenterService;
        _logger = logger;
    }

    [Authorize(Page.MasterCostCenter, Models.Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery] string? keyword = null,
        [FromQuery] int? departmentId = null,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10,
        [FromQuery] string orderBy = "costcenterid",
        [FromQuery] string orderType = "asc"
    ) {
        try {
            var data = await costCenterService.GetAll(keyword, departmentId,userSession.BranchId,orderBy, orderType, page, pageSize);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCostCenter, Models.Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery] string? keyword = null,
        [FromQuery] int? departmentId = null,
        [FromQuery] string orderBy = "costcenterid",
        [FromQuery] string orderType = "asc"
    ) {
        try {
            var data = await costCenterService.GetOption(keyword, departmentId,userSession.BranchId,orderBy, orderType, -1, -1);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

   [Authorize(Page.MasterCostCenter, Models.Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetByID(int id) {
        try {
            var data = await costCenterService.GetByID(id);
            if (data == null) return _NotFound(string.Format(TAG, "Not Found"));
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCostCenter, Models.Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CostCenter item) {
        try {
            if (item == null) return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new CostCenterValidation();
            var result = validator.Validate(item);
            if (!result.IsValid) {
                var errors = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errors);
            }

            item.BranchId = userSession.BranchId ?? 0;

            var id = await costCenterService.Add(item);
            return _Ok(id, string.Format(TAG, "data has been successfully saved"));
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCostCenter, Models.Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] CostCenter item) {
        try {
            if (item == null) return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new CostCenterValidation();
            var result = validator.Validate(item);
            if (!result.IsValid) {
                var errors = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errors);
            }

            item.BranchId = userSession.BranchId ?? 0;

            var id = await costCenterService.Update(item);
            return _Ok(id, string.Format(TAG, "data has been successfully updated"));
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCostCenter, Models.Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Remove(int id) {
        try {
            var data = await costCenterService.GetByID(id);
            if (data == null) return _NotFound(string.Format(TAG, "CostCenter ID Not Found"));

            var deletedId = await costCenterService.Remove(data);
            return _Ok(deletedId, string.Format(TAG, "data has been successfully deleted"));
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

   [Authorize(Page.MasterCostCenter, Models.Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery] string? keyword = null,
        [FromQuery] int? departmentId = null,
        [FromQuery] string orderBy = "costcenterid",
        [FromQuery] string orderType = "asc",
        [FromQuery] string type = "xlsx"
    ) {
        try {
            var data = await costCenterService.GetAll(keyword, departmentId,userSession.BranchId,orderBy, orderType, -1, -1);
            if (data.Data == null) return _NotFound("Data not found");

            byte[] fileContent;
            string fileName = "CostCenter.";

            if (type.ToLower() == "xlsx") {
                fileContent = ExportToDocs.ConvertToExcel<CostCenter>(data.Data);
                fileName += "xlsx";
            } else {
                fileContent = ExportToDocs.ConvertToCsv<CostCenter>(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}
