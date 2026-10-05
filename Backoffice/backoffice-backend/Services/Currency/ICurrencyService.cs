using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface ICurrencyService
    {
        Task<PagedResult<Currency>> GetAll(string? keyword,int? branchId,int page, int pageSize,string? orderBy,string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword,int? branchId,int page, int pageSize,string? orderBy,string? orderType);
        Task<Currency> GetByID(int id,int? branchId);
        Task<int> Add(Currency item);
        Task<int> Remove(Currency id);
        Task<int> Update(Currency item);
    }
}