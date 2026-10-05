using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services {
    public interface IContactService {
        Task<PagedResult<Contact>> GetAll(string? keyword, string? email, string? phone, int page, int pageSize, string? orderBy, string? orderType);
        Task<int> Add(Contact item);
        Task<Contact> GetByID(int id);
        Task<Contact> GetByRefCode(string  refCode);
        Task<int> Update(Contact item);
        Task<int> Remove(Contact item);
        Task<bool> CheckCityIsExists(int id);
    }
}