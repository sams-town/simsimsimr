using AngleSharp.Common;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services {
    public interface IProvinceService {
        Task<PagedResult<Province>> GetAll(string? keyword, int page, int pageSize, string? orderBy, string? orderType);
        Task<List<OptionResponse>> GetAllOption(string? keyword, int page, int pageSize, string? orderBy, string? orderType);
        Task<Province> GetByID(int provinceid);
        Task<int> Add (Province item);
        Task<int> SaveToDatabase(IFormFile fileJson);
        Task<int> Update(Province item);
        Task<int> Remove(Province item);
    }
}