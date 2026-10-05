namespace RSSMBackend.Helpers;

using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.Filters;
using RSSMBackend.Entities;
using RSSMBackend.Services;
using RSSMBackend.Models;
using System.IdentityModel.Tokens.Jwt;

[AttributeUsage(AttributeTargets.Class | AttributeTargets.Method, AllowMultiple = false)]
public class AuthorizeAttribute : Attribute, IAuthorizationFilter
{
    private readonly (Page page, Action action)[] _permissions;

    public AuthorizeAttribute(Page page, Action action)
    {
        _permissions = new[] { (page, action) };
    }

    public AuthorizeAttribute(Page page1, Action action1, Page page2, Action action2)
    {
        _permissions = new[] { (page1, action1), (page2, action2) };
    }

    public async void OnAuthorization(AuthorizationFilterContext context)
    {
        var allow = false;
        var user = (User)context.HttpContext.Items["User"];
        if (user != null)
        {
            var roleService = context.HttpContext.RequestServices.GetService(typeof(IRoleService)) as IRoleService;
            foreach (var (page, action) in _permissions)
            {
                var pageAccess = await roleService.GetPageAccessByPageCode(page, user.RoleID);
                if (pageAccess != null)
                {
                    string[] actionList = pageAccess.Action.Split(",");
                    foreach (string a in actionList)
                    {
                        if (a.Equals(action.ToString()))
                        {
                            allow = true;
                            break;
                        }
                    }
                }
                if (allow) break;
            }
        }

        if (!allow)
            context.Result = new JsonResult(new { message = "Unauthorized" }) { StatusCode = StatusCodes.Status401Unauthorized };
    }
}