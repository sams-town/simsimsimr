namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class AccountTypeController : BaseController
{
    private IAccountTypeService accountTypeService;
    private readonly ILogger<AccountTypeController> _logger;
    private const string TAG = "AccountType {0}";
    private const string LOG = "AccountType {x}";
    public AccountTypeController(IAccountTypeService accountTypeService, ILogger<AccountTypeController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.accountTypeService = accountTypeService;
        _logger = logger;
    }

    [Authorize(Page.MasterAccountType, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "AccountTypeId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await accountTypeService.GetAll(keyword, userSession.BranchId, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterAccountType, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "AccountTypeId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try {
            var data = await accountTypeService.GetAll(keyword, userSession.BranchId, -1, -1, orderBy, orderType);
            if (data == null) 
                return _NotFound(string.Format(TAG, "Not Found"));
            
            byte[] fileContent;
            string fileName = $"ACCOUNT_TYPE.";
            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())) {
                fileContent = ExportToDocs.ConvertToExcel<AccountType>(data.Data);
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<AccountType>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountType, Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOPtion(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "AccountTypeId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await accountTypeService.GetAllOption(keyword, userSession.BranchId, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterAccountType, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] AccountType item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new AccountTypeValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await accountTypeService.Add(item);
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

    [Authorize(Page.MasterAccountType, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] AccountType item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new AccountTypeValidator();
            var result = validator.Validate(item);
            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await accountTypeService.Update(item);
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

    [Authorize(Page.MasterAccountType, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await accountTypeService.GetByID(id, userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            var cOAService = HttpContext.RequestServices.GetService(typeof(ICOAService)) as ICOAService;
            var exist = await cOAService.checkIfExistType(data.AccountTypeId, userSession.BranchId);
            if (!exist)
            {
                await accountTypeService.Remove(data);
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

    [Authorize(Page.MasterAccountType, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await accountTypeService.GetByID(id, userSession.BranchId);
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


