using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface IAccountTypeService
    {
        Task<PagedResult<AccountType>> GetAll(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType);
        Task<AccountType> GetByID(int id, int? branchId);
        Task<AccountType> GetByName(string name, int? branchId);
        Task<int> Add(AccountType item);
        Task<int> Remove(AccountType id);
        Task<int> Update(AccountType item);
        Task<bool> CheckIfExistInCoa(int accountTypeId);
    }
}