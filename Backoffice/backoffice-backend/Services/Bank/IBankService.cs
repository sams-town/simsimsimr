using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services {
    public interface IBankService {
        Task<PagedResult<Bank>> GetAll(string? keyword, int page, int pageSize, string? orderBy, string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword, int page, int pageSize, string? orderBy, string? orderType);
        Task<Bank> GetByCode(string code);
        Task<string> Add(Bank item);
        Task<string> Update(Bank item);
        Task<string> Remove(Bank item);
    }
}