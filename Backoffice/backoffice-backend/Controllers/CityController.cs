namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using Serilog;
using System.Text.Json;

[ApiController]
[Route("[controller]")]
public class CityController : BaseController {
    private ICityService _cityService;
    private IContactService _contactService;
    private readonly ILogger<CityController> _logger;
    private const string TAG = "City {0}";
    private const string LOG = "City {x}";
    public CityController(ICityService cityService, IContactService contactService, ILogger<CityController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor) {
        _cityService = cityService;
        _contactService = contactService;
        _logger = logger;
    }

    [Authorize(Page.MasterCity,Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "provinceid")] int? provinceid = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "cityid",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    ) {
        try {
            var data = await _cityService.GetAll(keyword,provinceid,page,pageSize,orderBy,orderType);
            if (data == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetByID(int id) {
        try {
            var data = await _cityService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));
            
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity,Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "provinceid")] int? provinceid = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "cityid",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    ) {
        try {
            var data = await _cityService.GetAllOption(keyword,provinceid,page,pageSize,orderBy,orderType);
            if (data == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity,Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] City item) {
        try {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Item is null"));

            var validator = new CityValidation();
            var result = validator.Validate(item);
            if (!result.IsValid) {
                return _BadRequest(string.Format(TAG, result.ToString()));  
            } else {
                var id = await _cityService.Add(item);
                return _Ok(id);
            }
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity,Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] City item) {
        try {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Item is null"));
                
            var id = await _cityService.Update(item);
            return _Ok(id);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity,Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Remove(int id) {
        try {
            var data = await _cityService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            var exists = await _contactService.CheckCityIsExists(id);
            if (!exists) {
                var result = await _cityService.Remove(data);
                return _Ok(result);
            } else {
                return _BadRequest(string.Format(TAG,"Can't delete city because it is in use"));
            }

        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity,Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export([FromQuery(Name = "type")] string type = "xlsx") {
        try {
            var data = await _cityService.GetAll(null, null, -1, -1, "cityid", "asc");
            byte[] fileContent;
            string fileName = "CITY.";
            if (type == "xlsx") {
                fileContent = ExportToDocs.ConvertToExcel<City>(data.Data);
                fileName += "xlsx";
            } else {
                fileContent = ExportToDocs.ConvertToCsv<City>(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCity, Action.Export)]
    [HttpPost("import")]
    public async Task<IActionResult> Import(IFormFile dataImport) {
        try {
            if (dataImport == null || dataImport.Length == 0)
                return _BadRequest(string.Format(TAG,"File is empty"));

            if (!dataImport.ContentType.Equals("application/json", System.StringComparison.OrdinalIgnoreCase))
                return _BadRequest(string.Format(TAG,"File is not json"));

            var result = await _cityService.SaveToDatabase(dataImport);
            return _Ok(result);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}