using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface ICostCenterService
    {
        Task<PagedResult<CostCenter>> GetAll(
            string? keyword,
            int? departmentId,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        );

        Task<List<OptionResponse>> GetOption(string? keyword, int? departmentId,int? branchId, string? orderBy, string? orderType,int page, int pageSize);

        Task<CostCenter> GetByID(int id);

        Task<int> Add(CostCenter item);

        Task<int> Update(CostCenter item);

        Task<int> Remove(CostCenter item);
    }
}
