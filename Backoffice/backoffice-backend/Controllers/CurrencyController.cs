namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class CurrencyController: BaseController
{
    private ICurrencyService currencyService;
    private readonly ILogger<CurrencyController> _logger;
    private const string TAG = "Currency {0}";
    private const string LOG = "Currency {x}";
    public CurrencyController(ICurrencyService currencyService,ILogger<CurrencyController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.currencyService = currencyService;
        _logger = logger;
    }
   
    [Authorize(Page.MasterCurrency,Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "CurrrencyId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var user = await currencyService.GetAll(keyword,userSession.BranchId,page,pageSize,orderBy,orderType);
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

    [Authorize(Page.MasterCurrency, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "CurrrencyId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try {
            var data = await currencyService.GetAll(keyword,userSession.BranchId,-1,-1,orderBy,orderType);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            byte[] fileContent;
            string fileName = $"CURRENCY.";
            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())) {
                fileContent = ExportToDocs.ConvertToExcel<Currency>(data.Data);
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Currency>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }
            
            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCurrency,Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "CurrrencyId",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "fileType")] string fileType = null
        )
    {
        try
        {
            var data = await currencyService.GetAllOption(keyword,userSession.BranchId,page,pageSize,orderBy,orderType);
            if (data == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }

    }
    
    [Authorize(Page.MasterCurrency,Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Currency item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var validator = new CurrencyValidator();
            var result =  validator.Validate(item);

            if(result.IsValid){
                item.BranchId = userSession.BranchId;
                var id = await currencyService.Add(item);
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

    [Authorize(Page.MasterCurrency,Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Currency item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var validator = new CurrencyValidator();
            var result =  validator.Validate(item);
            if(result.IsValid){
                item.BranchId = userSession.BranchId;
                var id = await currencyService.Update(item);
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

    [Authorize(Page.MasterCurrency,Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
         try
        {
            var data = await currencyService.GetByID(id,userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            await currencyService.Remove(data);    

            return _Ok(id,string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCurrency,Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await currencyService.GetByID(id,userSession.BranchId);
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


