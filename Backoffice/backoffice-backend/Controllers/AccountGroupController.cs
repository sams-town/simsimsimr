namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class AccountGroupController : BaseController
{
    private IAccountGroupService accountGroupService;
    private readonly ILogger<AccountGroupController> _logger;
    private const string TAG = "AccountGroup {0}";
    private const string LOG = "AccountGroup {x}";
    public AccountGroupController(IAccountGroupService accountGroupService, ILogger<AccountGroupController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.accountGroupService = accountGroupService;
        _logger = logger;
    }

    [Authorize(Page.MasterAccountGroup, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "AccountGroupId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await accountGroupService.GetAll(keyword, userSession.BranchId, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterAccountGroup, Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "AccountGroupId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await accountGroupService.GetAllOption(keyword, userSession.BranchId, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterAccountGroup, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export (
        [FromQuery(Name = "type")] string type = "xlsx",
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "AccountGroupId",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
        ) {
        try {
            var data = await accountGroupService.GetAll(keyword,userSession.BranchId, -1, -1, orderBy, orderType);
            if (data == null) 
                return _NotFound(string.Format(TAG, "Not Found"));
            
            string fileName = $"ACCOUNT_GROUP.";
            byte[] fileContent = null;
            if(type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())){
                fileContent = ExportToDocs.ConvertToExcel<AccountGroup>(data.Data);
                fileName += "xlsx";
            } else {
                fileContent = ExportToDocs.ConvertToCsv<AccountGroup>(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
            
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountGroup, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] AccountGroup item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new AccountGroupValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await accountGroupService.Add(item);
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

    [Authorize(Page.MasterAccountGroup, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] AccountGroup item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new AccountGroupValidator();
            var result = validator.Validate(item);
            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await accountGroupService.Update(item);
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

    [Authorize(Page.MasterAccountGroup, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await accountGroupService.GetByID(id, userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            var cOAService = HttpContext.RequestServices.GetService(typeof(ICOAService)) as ICOAService;
            var exist = await cOAService.checkIfExistGroup(data.AccountGroupId, userSession.BranchId);
            if (!exist)
            {
                await accountGroupService.Remove(data);
                return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
            }
            else
            {
                return _BadRequest("Unable to delete: The data is currently in use");
            }

        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountGroup, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await accountGroupService.GetByID(id, userSession.BranchId);
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


