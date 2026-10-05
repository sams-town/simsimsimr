namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class RoleController : BaseController
{
    private IRoleService roleService;
    private readonly ILogger<RoleController> _logger;
    private const string TAG = "Role {0}";
    private const string LOG = "Role {x}";
    public RoleController(IRoleService roleService, ILogger<RoleController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.roleService = roleService;
        _logger = logger;
    }

    [Authorize(Page.MasterRole, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "RoleID",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await roleService.GetAll(keyword, userSession.BranchId, page, pageSize, orderBy, orderType);
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

    [Authorize(Page.MasterRole, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "RoleID",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string type = "xlsx"
    )
    {
        try
        {
            var data = await roleService.GetAll(keyword, userSession.BranchId, -1, -1, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG, "data not found!"));

            byte[] fileContent;
            string fileName = $"ROLE.";
            
            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower ())) {
                fileContent = ExportToDocs.ConvertToExcel<Role>(data.Data);
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Role>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterRole, Action.Read)]
    [HttpGet("options")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "RoleID",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var user = await roleService.GetAllOption(keyword, userSession.BranchId, page, pageSize, orderBy, orderType);
            if (user == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(user);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterRole, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Role item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new RoleValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await roleService.Add(item);
                if (item.AccessList != null)
                {
                    foreach (RolePageAccess access in item.AccessList)
                    {
                        access.RoleID = id;
                        await roleService.AddPageAccess(access);
                    }
                }

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

    [Authorize(Page.MasterRole, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Role item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var validator = new RoleValidator();
            var result = validator.Validate(item);
            if (result.IsValid)
            {
                item.BranchId = userSession.BranchId;
                var id = await roleService.Update(item);
                await roleService.deletePageAccessByRole(id);
                if (item.AccessList != null)
                {
                    foreach (RolePageAccess access in item.AccessList)
                    {
                        access.RoleID = id;
                        await roleService.AddPageAccess(access);
                    }
                }
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

    [Authorize(Page.MasterRole, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var data = await roleService.GetByID(id, userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            await roleService.Remove(data);
            await roleService.deletePageAccessByRole(id);

            return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterRole, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            Role data;
            if (id == 0)
            {
                data = new Role
                {
                    PageList = await roleService.GetAllPageRole()
                };
            }
            else
            {

                data = await roleService.GetByID(id, userSession.BranchId);
                if (data == null)
                    return _NotFound(string.Format(TAG, "Not Found"));

                data.AccessList = await roleService.GetPageAccessByRoleID(id);
                data.PageList = await roleService.GetAllPageRole();
            }
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterRole, Action.Read)]
    [HttpGet("page")]
    public async Task<IActionResult> getPage()
    {
        try
        {
            var data = await roleService.GetAllPageRole();
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

    // [Authorize(Page.MasterRole, Action.Export)]
    // [HttpGet("page/export")]
    // public async Task<IActionResult> ExportAllRolePage([FromQuery(Name = "fileType")] string extensionFile = "csv")
    // {
    //     try
    //     {
    //         var data = await roleService.GetAllPageRole();
    //         if (data == null)
    //             return _NotFound(string.Format(TAG, "Not Found"));

    //         byte[] fileContent = ExportToDocs.ConvertToCsv<RolePage>(data);
    //         string fileName = $"all_data_role_page.{extensionFile}";
    //         return _File(fileContent, extensionFile, fileName);
    //     } catch (Exception e) {
    //         _logger.LogError(LOG, e);
    //         return _BadRequest(e.Message);
    //     }
    // }
}


