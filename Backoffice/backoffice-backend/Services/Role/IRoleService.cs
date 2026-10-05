using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface IRoleService
    {
        Task<PagedResult<Role>> GetAll(string? keyword,int? branchId,int page, int pageSize,string? orderBy,string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword,int? branchId,int page, int pageSize,string? orderBy,string? orderType);
        Task<Role> GetByID(int id,int? branchId);
        Task<int> Add(Role item);
        Task<int> Remove(Role item);
        Task<int> Update(Role item);

        Task<RolePageAccess>  GetPageAccessByPageCode(Page page,int roleId);
        Task<List<RolePageAccess>>  GetPageAccessByRoleID(int roleId);
        Task<List<RolePage>>  GetAllPageRole();
        Task<int>  deletePageAccessByRole(int roleId);
        Task<int> AddPageAccess(List<RolePageAccess> items);
        Task<int> AddPageAccess(RolePageAccess item);

    }
}