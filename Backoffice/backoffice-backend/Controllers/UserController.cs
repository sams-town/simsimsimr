namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class UserController : BaseController
{
    private IUserService userService;
    private readonly ILogger<UserController> _logger;
    private const string TAG = "User {0}";
    private const string LOG = "User {x}";
    public UserController(IUserService userService, ILogger<UserController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.userService = userService;
        _logger = logger;
    }

    [Authorize(Page.MasterUser, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "UserID",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "role")] int? role = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await userService.GetAll(keyword, status, role, userSession.BranchId, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterUser, Action.Export)]
    [HttpGet("Export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "UserID",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "role")] int? role = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try {
            var data = await userService.GetAll(keyword, status, role, userSession.BranchId, -1, -1, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "Data not found!"));

            byte[] fileContent;
            string fileName = $"USER.";

            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())) {
                fileContent = ExportToDocs.ConvertToExcel<User>(data.Data);
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<User>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterUser, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] User item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var userValidator = new UserValidator();
            var result = userValidator.Validate(item);

            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await userService.Add(item);
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

    [Authorize(Page.MasterUser, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] User item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var userValidator = new UserValidator();
            var result = userValidator.Validate(item);
            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await userService.Update(item);
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

    [Authorize(Page.MasterUser, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await userService.GetByID(id, userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            await userService.Remove(data);

            return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterUser, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await userService.GetByID(id, userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            data.Password = "";
            data.Salt = "";
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}


