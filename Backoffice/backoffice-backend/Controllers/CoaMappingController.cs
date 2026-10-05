namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class CoaMappingController : BaseController
{
    private ICoaMappingService coaMappingService;
    private readonly ILogger<CoaMappingController> _logger;
    private const string TAG = "CoaMapping {0}";
    private const string LOG = "CoaMapping {x}";

    public CoaMappingController(ICoaMappingService coaMappingService, ILogger<CoaMappingController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.coaMappingService = coaMappingService;
        _logger = logger;
    }

    [Authorize(Page.MasterCOA, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "Id",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
    )
    {
        try
        {
            var data = await coaMappingService.GetAll(keyword, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterCOA, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetDetail(int id)
    {
        try
        {
            var data = await coaMappingService.GetByID(id);
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

    [Authorize(Page.MasterCOA, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CoaMapping item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new CoaMappingValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                var id = await coaMappingService.Add(item);
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

    [Authorize(Page.MasterCOA, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] CoaMapping item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new CoaMappingValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                var id = await coaMappingService.Update(item);
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

    [Authorize(Page.MasterCOA, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await coaMappingService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            await coaMappingService.Remove(data);
            return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}
