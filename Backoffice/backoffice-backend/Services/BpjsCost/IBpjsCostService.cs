using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface IBpjsCostService
    {
        Task<PagedResult<BpjsCost>> GetAll(
            string? keyword,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        );

        Task<List<OptionResponse>> GetOption(string? keyword, int? branchId, string? orderBy, string? orderType, int page, int pageSize);

        Task<BpjsCost> GetByID(int id);

        Task<int> Add(BpjsCost item);

        Task<int> Update(BpjsCost item);

        Task<int> Remove(BpjsCost item);
    }
}
