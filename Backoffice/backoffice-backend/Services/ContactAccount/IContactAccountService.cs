using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services {
    public interface IContactAccountService {
        Task<int> Add(List<ContactAccount> items);
        Task<List<ContactAccount>> GetByContactID(int id);
        Task<int> Remove(List<ContactAccount> items);
        Task<bool> CheckIfExistAccount(string bankCode);
    }
}