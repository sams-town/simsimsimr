namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class CompanyController: BaseController
{
    private ICompanyService companyService;
    private readonly ILogger<CompanyController> _logger;
    private const string TAG = "Company {0}";
    private const string LOG = "Company {x}";
    public CompanyController(ICompanyService companyService,ILogger<CompanyController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.companyService = companyService;
        _logger = logger;
    }
   
    [Authorize(Page.MasterCompany,Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "CompanyId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "status")] string status = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var user = await companyService.GetAll(keyword,status,page,pageSize,orderBy,orderType);
            if (user == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }

            return _Ok(user);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCompany, Action.Export)]
    [HttpGet("Export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "CompanyId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "status")] string status = null,
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try {
            var data = await companyService.GetAll(keyword,status,-1,-1,orderBy,orderType);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            byte[] fileContent;
            string fileName = $"COMPANY.";
            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())) {
                fileContent = ExportToDocs.ConvertToExcel<Company>(data.Data);
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Company>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCompany,Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "CompanyId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "status")] string status = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var user = await companyService.GetAllOption(keyword,status,page,pageSize,orderBy,orderType);
            if (user == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }

            return _Ok(user);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }

    }
    
    [Authorize(Page.MasterCompany,Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Company item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var validator = new CompanyValidator();
            var result =  validator.Validate(item);

            if(result.IsValid){
                item.FinancialClosingDate = item.FinancialClosingDate.Value.ToUniversalTime();
                var id = await companyService.Add(item);
                return _Ok(id,string.Format(TAG, "data has been successfully saved"));
            } else {
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

    [Authorize(Page.MasterCompany,Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Company item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var validator = new CompanyValidator();
            var result =  validator.Validate(item);
            if(result.IsValid){
                item.FinancialClosingDate = item.FinancialClosingDate.Value.ToUniversalTime();
                var id = await companyService.Update(item);
                return _Ok(id,string.Format(TAG, "data has been successfully updated"));
            } else {
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

    [Authorize(Page.MasterCompany,Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
         try
        {
            var data = await companyService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            var branchService = HttpContext.RequestServices.GetService(typeof(IBranchService)) as IBranchService;
            var exist = await branchService.checkIfExistCompany(data.CompanyId,userSession.BranchId);
            if(!exist){
                 await companyService.Remove(data);    
                return _Ok(id,string.Format(TAG, "data has been successfully deleted"));
            } else {
                return _BadRequest("Unable to delete: The data is currently in use");
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCompany,Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await companyService.GetByID(id);
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


