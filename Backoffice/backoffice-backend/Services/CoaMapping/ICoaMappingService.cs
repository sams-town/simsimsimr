using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface ICoaMappingService
    {
        Task<PagedResult<CoaMapping>> GetAll(string? keyword, int page, int pageSize, string? orderBy, string? orderType);
        Task<CoaMapping> GetByID(int id);
        Task<int> Add(CoaMapping item);
        Task<int> Remove(CoaMapping item);
        Task<int> Update(CoaMapping item);
    }
}
