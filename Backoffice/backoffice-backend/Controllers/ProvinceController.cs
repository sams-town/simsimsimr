namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using System.Net;

[ApiController]
[Route("[controller]")]
public class ProvinceController : BaseController
{
    private IProvinceService provinceService;
    private readonly ILogger<ProvinceController> _logger;
    private const string TAG = "Province {0}";
    private const string LOG = "Province {x}";
    public ProvinceController(IProvinceService provinceService, ILogger<ProvinceController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.provinceService = provinceService;
        _logger = logger;
    }

    [Authorize(Page.MasterProvince, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "ProvinceId",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await provinceService.GetAll(keyword, page, pageSize, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "data province not found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterProvince, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetById(int id) {
        try  {
            var data = await provinceService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "data not found"));
            
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterProvince, Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "ProvinceId",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await provinceService.GetAllOption(keyword, page, pageSize, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "data province not found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterProvince, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Province item)
    {
        try
        {
            if (item == null)
                return _BadRequest("Province is null");

            var validator = new ProvinceValidation();
            var result = validator.Validate(item);
            if (!result.IsValid) {
                return _BadRequest(string.Format(TAG, result.ToString()));
            } else {
                var id = await provinceService.Add(item);
                return _Ok(id);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterProvince, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Province item)
    {
        try
        {
            if (item == null)
                return _BadRequest("Province is null");

            var id = await provinceService.Update(item);
            return _Ok(id);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterProvince, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await provinceService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "data province not found"));

            var cityService = HttpContext.RequestServices.GetService(typeof(ICityService)) as ICityService;
            var exist = await cityService.CheckIfExistProvinceId(data.ProvinceId);
            if (!exist)
            {
                var result = await provinceService.Remove(data);
                return _Ok(result);
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

    [Authorize(Page.MasterProvince, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "type")] string type = "xlsx"
    )
    {
        try
        {
            var data = await provinceService.GetAll(null, -1, -1, "ProvinceId", "asc");
            byte[] fileContent;
            string fileName = "PROVINCE.";
            if (type.Equals("xlsx"))
            {
                fileContent = ExportToDocs.ConvertToExcel<Province>(data.Data);
                fileName += "xlsx";
            }
            else
            {
                fileContent = ExportToDocs.ConvertToCsv<Province>(data.Data);
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

    [Authorize(Page.MasterProvince, Action.Export)]
    [HttpPost("import")]
    public async Task<IActionResult> Import(IFormFile dataImport) {
        try {
            if (dataImport == null || dataImport.Length == 0)
                return _BadRequest(string.Format(TAG,"File is empty"));

            if (!dataImport.ContentType.Equals("application/json", System.StringComparison.OrdinalIgnoreCase))
                return _BadRequest(string.Format(TAG,"File is not json"));

            var result = await provinceService.SaveToDatabase(dataImport);
            return _Ok(result);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}