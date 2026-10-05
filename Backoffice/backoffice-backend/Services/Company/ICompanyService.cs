using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface ICompanyService
    {
        Task<PagedResult<Company>> GetAll(string? keyword,string? status,int page, int pageSize,string? orderBy,string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword,string? status,int page, int pageSize,string? orderBy,string? orderType);
        Task<Company> GetByID(int id);
        Task<int> Add(Company item);
        Task<int> Remove(Company id);
        Task<int> Update(Company item);
    }
}