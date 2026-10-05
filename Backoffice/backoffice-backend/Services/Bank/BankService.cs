using Microsoft.EntityFrameworkCore;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.Extensions.Options;

namespace RSSMBackend.Services
{
    public class BankService : IBankService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public BankService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }


        public async Task<PagedResult<Bank>> GetAll(string? keyword, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<Bank> query = _context.Banks;

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.BankName.ToLower().Contains(keyword.ToLower()) || x.BankCode.ToLower().Contains(keyword.ToLower()));

            orderBy = string.IsNullOrEmpty(orderBy) ? "bankcode" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "bankcode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BankCode) : query.OrderByDescending(x => x.BankCode);
                    break;
                case "bankname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BankName) : query.OrderByDescending(x => x.BankName);
                    break;
                default:
                    query = query.OrderBy(x => x.BankCode);
                    break;
            }

            int total = await query.CountAsync();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Bank>(await query.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<Bank> query = _context.Banks;

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.BankName.ToLower().Contains(keyword.ToLower()) || x.BankCode.ToLower().Contains(keyword.ToLower()));


            orderBy = string.IsNullOrEmpty(orderBy) ? "bankcode" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "bankcode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BankCode) : query.OrderByDescending(x => x.BankCode);
                    break;
                case "bankname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BankName) : query.OrderByDescending(x => x.BankName);
                    break;
                default:
                    query = query.OrderBy(x => x.BankCode);
                    break;
            }

            var q = query.Select(x => new OptionResponse
            {
                label = x.BankName,
                value = x.BankCode
            });

            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<Bank> GetByCode(string code)
        {
            return await _context.Banks.FirstOrDefaultAsync(x => x.BankCode.Equals(code));
        }

        public async Task<string> Add(Bank item)
        {
            if (_context.Banks.Any(x => x.BankCode.Equals(item.BankCode)))
                throw new ApplicationException("Bank Code is Already Exists");

            if (_context.Banks.Any(x => x.BankName.ToLower().Equals(item.BankName.ToLower())))
                throw new ApplicationException("Bank Name is Already Exists");

            _context.Banks.Add(item);
            await _context.SaveChangesAsync();
            return item.BankCode;
        }

        public async Task<string> Update(Bank item)
        {
            var data = await GetByCode(item.BankCode);
            if (data == null)
                throw new ApplicationException("Data Bank Not Found");

            if (_context.Banks.Any(x => x.BankName.ToLower().Equals(item.BankName.ToLower()) && x.BankCode != item.BankCode))
                throw new ApplicationException("Bank Name Already Exists");

            data.BankName = item.BankName;

            _context.Banks.Update(data);
            await _context.SaveChangesAsync();
            return item.BankCode;
        }

        public async Task<string> Remove(Bank item)
        {
            _context.Banks.Remove(item);
            await _context.SaveChangesAsync();
            return item.BankCode;
        }
    }
}