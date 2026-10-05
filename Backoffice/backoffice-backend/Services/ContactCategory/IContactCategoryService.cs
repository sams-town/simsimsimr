using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface IContactCategoryService
    {
        Task<PagedResult<ContactCategory>> GetAll(
            string? keyword,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        );

        Task<List<OptionResponse>> GetOption(string? keyword, int? branchId, string? orderBy, string? orderType,int page, int pageSize);

        Task<ContactCategory> GetByID(int id);

        Task<int> Add(ContactCategory item);

        Task<int> Update(ContactCategory item);

        Task<int> Remove(ContactCategory item);
    }
}
