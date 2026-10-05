using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services {
    public interface ICityService {
        Task<PagedResult<City>> GetAll(string? keyword, int? provinceid, int page, int pageSize, string? orderBy, string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword, int? provinceid, int page, int pageSize, string? orderBy, string? orderType);
        Task<City> GetByID(int id);
        Task<int> Add(City item);
        Task<int> SaveToDatabase(IFormFile fileJson);
        Task<int> Update(City item);
        Task<int> Remove(City item);
        Task<bool> CheckIfExistProvinceId(int provinceid);
    }
}