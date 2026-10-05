namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;
using RSSMBackend.Validation;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.DbContext;

[ApiController]
[Route("[controller]")]
public class BankController : BaseController
{
    private IBankService _bankService;
    private readonly ILogger<BankController> _logger;
    private const string TAG = "BANK {0}";
    private const string LOG = "BANK {x}";
    private RSSMDbContext dbContext;
    public BankController(RSSMDbContext dbContext,IBankService bankService, ILogger<BankController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _bankService = bankService;
        _logger = logger;
        this.dbContext = dbContext;
    }

    [Authorize(Page.MasterBank, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "bankcode",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await _bankService.GetAll(keyword, page, pageSize, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "data is not found!"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
    
    [Authorize(Page.MasterBank, Action.Read)]
    [HttpGet("export")]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "bankcode",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string? type = "xlsx"
    ) {
        try {
            var data = await _bankService.GetAll(keyword, -1, -1, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "data is not found!"));

            byte[] fileContent;
            string fileName = "BANK.";
            if (type == "xlsx") {
                fileContent = ExportToDocs.ConvertToExcel<Bank>(data.Data);
                fileName += "xlsx";
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Bank>(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);


        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBank, Action.Read)]
    [HttpGet("{code}")]
    public async Task<IActionResult> GetById(string code) {
        try {
            var data = await _bankService.GetByCode(code);
            if (data == null)
                return _NotFound(string.Format(TAG, "data not found"));

            return _Ok(data);
        } catch (Exception e) {
                _logger.LogError(LOG, e);
                return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBank, Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "bankcode",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    ) {
        try {
            var data = await _bankService.GetAllOption(keyword, page, pageSize, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "data is not found!"));

            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBank, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Bank item) {
        try {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Bank is null!"));
                
            var validator = new BankValidator();
            var result = validator.Validate(item);
            if (!result.IsValid) {
                return _BadRequest(string.Format(TAG, result.ToString()));
            } else {
                var code = await _bankService.Add(item);
                return _Ok(code, string.Format(TAG, "data has been successfully saved"));
            }
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBank, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Bank item) {
        try {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Bank is null!"));
            
            var code = await _bankService.Update(item);
            return _Ok(code, string.Format(TAG, "data has been successfully updated"));
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterBank, Action.Delete)]
    [HttpDelete("{code}")]
    public async Task<IActionResult> Remove(string code) {
        try {
            var data = await _bankService.GetByCode(code);
            if (data == null)
                return _NotFound(string.Format(TAG, "Bank is not found!"));

            var accountService = HttpContext.RequestServices.GetService(typeof(IContactAccountService)) as IContactAccountService;
            var exist = await accountService.CheckIfExistAccount(data.BankCode);
            if (!exist) {
                var codeResult = await _bankService.Remove(data);
                return _Ok(codeResult, string.Format(TAG, "data has been successfully deleted"));
            } else {
                return _BadRequest(string.Format(TAG, "Bank is used by contact account!"));
            }
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    

   
            
}


