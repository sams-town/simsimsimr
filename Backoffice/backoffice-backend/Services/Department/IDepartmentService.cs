using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface IDepartmentService
    {
        Task<PagedResult<Department>> GetAll(
            string? keyword,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        );

        Task<List<OptionResponse>> GetOption(string? keyword, int? branchId, string? orderBy, string? orderType,int page, int pageSize);

        Task<Department> GetByID(int id);

        Task<int> Add(Department item);

        Task<int> Update(Department item);

        Task<int> Remove(Department item);
    }
}
