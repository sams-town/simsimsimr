using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface IAccountGroupService
    {
        Task<PagedResult<AccountGroup>> GetAll(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType);
        Task<AccountGroup> GetByID(int id, int? branchId);
        Task<AccountGroup> GetByName(string name, int? branchId);
        Task<int> Add(AccountGroup item);
        Task<int> Remove(AccountGroup id);
        Task<int> Update(AccountGroup item);
        Task<bool> CheckIfExistInCoa(int accountGroupId);
        Task<bool> CheckIfExistName(string name, int? accountGroupId);
    }
}