using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Options;

using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;

namespace RSSMBackend.Services
{
    public class ContactAccountService : IContactAccountService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public ContactAccountService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<List<ContactAccount>> GetByContactID(int id)
        {
            IQueryable<ContactAccount> query = _context.ContactAccounts;
            query = query.Where(x => x.ContactId.Equals(id));
            return await query.ToListAsync();
        }

        public async Task<int> Add(List<ContactAccount> items)
        {
            _context.ContactAccounts.AddRange(items);
            return await _context.SaveChangesAsync();
        }

        public async Task<int> Remove(List<ContactAccount> items)
        {
            _context.ContactAccounts.RemoveRange(items);
            return await _context.SaveChangesAsync();
        }

        public async Task<bool> CheckIfExistAccount(string bankCode)
        {
            return await _context.ContactAccounts.AnyAsync(x => x.BankCode.Equals(bankCode));
        }
    }
}