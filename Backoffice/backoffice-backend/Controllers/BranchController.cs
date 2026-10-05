namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class BranchController : BaseController
{
    private IBranchService branchService;
    private readonly ILogger<BranchController> _logger;
    private const string TAG = "Branch {0}";
    private const string LOG = "Branch {x}";
    public BranchController(IBranchService branchService, ILogger<BranchController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.branchService = branchService;
        _logger = logger;
    }

    [Authorize(Page.MasterBranch, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "BranchId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "company")] int? company = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "status")] string status = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await branchService.GetAll(keyword, status, company, page, pageSize, orderBy, orderType);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
    [Authorize(Page.MasterBranch, Action.Export)]
    [HttpGet("Export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "BranchId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "company")] int? company = null,
        [FromQuery(Name = "status")] string status = null,
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try
        {
            var data = await branchService.GetAll(keyword, status, company, -1, -1, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            byte[] fileContent;
            string fileName = $"Branch.";
            if  (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())) {
                fileContent = ExportToDocs.ConvertToExcel<Branch>(data.Data);
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Branch>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBranch, Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "BranchId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
         [FromQuery(Name = "company")] int? company = null,
        [FromQuery(Name = "status")] string status = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await branchService.GetAllOption(keyword, status, company, page, pageSize, orderBy, orderType);
            if (data == null) {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBranch, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Branch item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new BranchValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                item.FinancialClosingDate = item.FinancialClosingDate.Value.ToUniversalTime();
                var id = await branchService.Add(item);
                return _Ok(id, string.Format(TAG, "data has been successfully saved"));
            }
            else
            {
                var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errorMessages);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBranch, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Branch item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new BranchValidator();
            var result = validator.Validate(item);
            if (result.IsValid)
            {
                item.FinancialClosingDate = item.FinancialClosingDate.Value.ToUniversalTime();
                var id = await branchService.Update(item);
                return _Ok(id, string.Format(TAG, "data has been successfully updated"));
            }
            else
            {
                var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errorMessages);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBranch, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await branchService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            await branchService.Remove(data);

            return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBranch, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await branchService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}


