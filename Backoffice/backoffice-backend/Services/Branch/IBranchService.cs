using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface IBranchService
    {
        Task<PagedResult<Branch>> GetAll(string? keyword,string? status,int? company,int page, int pageSize,string? orderBy,string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword,string? status,int? company,int page, int pageSize,string? orderBy,string? orderType);
        Task<Branch> GetByID(int id);
        Task<int> Add(Branch item);
        Task<int> Remove(Branch id);
        Task<int> Update(Branch item);

        Task<bool> checkIfExistCompany(int companyId,int? branchId);
    }
}