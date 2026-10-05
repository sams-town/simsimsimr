using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;


namespace RSSMBackend.Services
{
    public class CurrencyService : ICurrencyService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public CurrencyService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<Currency>> GetAll(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Currency> query = _context.Currencies;
            query = query.Include(x => x.Branch);

            if (branchId != null)
            {
                query = query.Where(m => m.BranchId == branchId);
            }


            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.CurrencyName.ToLower().Contains(keyword.ToLower()) ||
                                         m.CurrencyCode.ToLower().Contains(keyword.ToLower()) ||
                                         m.Symbol.ToLower().Contains(keyword.ToLower()) ||
                                         m.Country.ToLower().Contains(keyword.ToLower()) ||
                                         m.Branch.BranchName.ToLower().Contains(keyword.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "currencyname" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "currencyid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyId) : query.OrderByDescending(x => x.CurrencyId);
                    break;
                case "currencyname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyName) : query.OrderByDescending(x => x.CurrencyName);
                    break;
                case "currencycode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyCode) : query.OrderByDescending(x => x.CurrencyCode);
                    break;
                case "symbol":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Symbol) : query.OrderByDescending(x => x.Symbol);
                    break;
                case "country":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Country) : query.OrderByDescending(x => x.Country);
                    break;
                default:
                    query = query.OrderBy(x => x.CurrencyName);
                    break;
            }

            var q = query.Select(x => new Currency
            {
                CurrencyId = x.CurrencyId,
                CurrencyName = x.CurrencyName,
                CurrencyCode = x.CurrencyCode,
                Symbol = x.Symbol,
                Country = x.Country,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Currency>(await q.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Currency> query = _context.Currencies;

            if (branchId != null)
            {
                query = query.Where(m => m.BranchId == branchId);
            }


            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.CurrencyName.ToLower().Contains(keyword.ToLower()) ||
                                         m.CurrencyCode.ToLower().Contains(keyword.ToLower()) ||
                                         m.Symbol.ToLower().Contains(keyword.ToLower()) ||
                                         m.Country.ToLower().Contains(keyword.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "currencyname" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "currencyid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyId) : query.OrderByDescending(x => x.CurrencyId);
                    break;
                case "currencyname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyName) : query.OrderByDescending(x => x.CurrencyName);
                    break;
                case "currencycode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyCode) : query.OrderByDescending(x => x.CurrencyCode);
                    break;
                case "symbol":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Symbol) : query.OrderByDescending(x => x.Symbol);
                    break;
                case "country":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Country) : query.OrderByDescending(x => x.Country);
                    break;
                default:
                    query = query.OrderBy(x => x.CurrencyName);
                    break;
            }

            int total = await query.CountAsync();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return await query.Select(g => new
                OptionResponse
            {
                label = g.CurrencyName,
                value = g.CurrencyId.ToString(),
                desc = g.CurrencyCode
            }).ToListAsync();
        }

        public async Task<Currency> GetByID(int id, int? branchId)
        {
            IQueryable<Currency> query = _context.Currencies;
            query = query.Include(x => x.Branch);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            query = query.Select(x => new Currency
            {
                CurrencyId = x.CurrencyId,
                CurrencyName = x.CurrencyName,
                CurrencyCode = x.CurrencyCode,
                Symbol = x.Symbol,
                Country = x.Country,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });

            return query.SingleOrDefault(x => x.CurrencyId.Equals(id));
        }

        public async Task<int> Add(Currency item)
        {

            IQueryable<Currency> query = _context.Currencies;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.CurrencyCode == item.CurrencyCode))
                throw new ApplicationException("Currency is already in use.");


            _context.Currencies.Add(item);
            _context.SaveChanges();
            return item.CurrencyId;
        }
        public async Task<int> Remove(Currency item)
        {
            _context.Currencies.Remove(item);
            _context.SaveChanges();
            return item.CurrencyId;
        }

        public async Task<int> Update(Currency item)
        {

            var data = await GetByID(item.CurrencyId, item.BranchId);
            if (data == null)
                throw new ApplicationException("Currency Not Found");

            IQueryable<Currency> query = _context.Currencies;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.CurrencyCode == item.CurrencyCode && u.CurrencyId != item.CurrencyId))
                throw new ApplicationException("Currency is already in use.");


            data.CurrencyCode = item.CurrencyCode;
            data.CurrencyName = item.CurrencyName;
            data.Symbol = item.Symbol;
            data.Country = item.Country;
            data.ExchangeRate = item.ExchangeRate;

            _context.Currencies.Update(data);
            _context.SaveChanges();
            return data.CurrencyId;
        }


    }
}