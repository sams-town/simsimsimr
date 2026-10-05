using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;

namespace RSSMBackend.Services
{
    public class CompanyService : ICompanyService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public CompanyService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<Company>> GetAll(string? keyword, string? status, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Company> query = _context.Companies;
            query = query.Include(x => x.Currency);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.Email.ToLower().Contains(keyword.ToLower())
                        || m.Phone.ToLower().Contains(keyword.ToLower())
                        || m.CompanyName.ToLower().Contains(keyword.ToLower())
                        || m.Status.ToLower().Contains(keyword.ToLower())
                        || m.Currency.CurrencyName.ToLower().Contains(keyword.ToLower())
                );
            }

            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "companyid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "companyid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CompanyId) : query.OrderByDescending(x => x.CompanyId);
                    break;
                case "email":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Email) : query.OrderByDescending(x => x.Email);
                    break;
                case "companyname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CompanyName) : query.OrderByDescending(x => x.CompanyName);
                    break;
                case "status":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break;
                case "phone":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Phone) : query.OrderByDescending(x => x.Phone);
                    break;
                case "currency":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Currency.CurrencyName) : query.OrderByDescending(x => x.Currency.CurrencyName);
                    break;
                case "address":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Address) : query.OrderByDescending(x => x.Address);
                    break;
                case "currencyid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyId) : query.OrderByDescending(x => x.CurrencyId);
                    break;
                case "financialclosingdate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.FinancialClosingDate) : query.OrderByDescending(x => x.FinancialClosingDate);
                    break;

                default:
                    query = query.OrderBy(x => x.CompanyId);
                    break;
            }

            var q = query.Select(x =>
            new Company
            {
                CompanyId = x.CompanyId,
                CompanyName = x.CompanyName,
                Phone = x.Phone,
                Email = x.Email,
                Status = x.Status,
                Address = x.Address,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency.CurrencyName,
                FinancialClosingDate = x.FinancialClosingDate
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Company>(await q.ToListAsync(), total);
        }




        public async Task<List<OptionResponse>> GetAllOption(string? keyword, string? status, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Company> query = _context.Companies;
            query = query.Include(x => x.Currency);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.Email.ToLower().Contains(keyword.ToLower())
                        || m.Phone.ToLower().Contains(keyword.ToLower())
                        || m.CompanyName.ToLower().Contains(keyword.ToLower())
                        || m.Status.ToLower().Contains(keyword.ToLower())
                        || m.Currency.CurrencyName.ToLower().Contains(keyword.ToLower())
                );
            }

            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "companyid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "companyid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CompanyId) : query.OrderByDescending(x => x.CompanyId);
                    break;
                case "email":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Email) : query.OrderByDescending(x => x.Email);
                    break;
                case "companyname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CompanyName) : query.OrderByDescending(x => x.CompanyName);
                    break;
                case "status":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break;
                case "phone":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Phone) : query.OrderByDescending(x => x.Phone);
                    break;
                case "currency":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Currency.CurrencyName) : query.OrderByDescending(x => x.Currency.CurrencyName);
                    break;
                case "address":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Address) : query.OrderByDescending(x => x.Address);
                    break;
                case "currencyid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CurrencyId) : query.OrderByDescending(x => x.CurrencyId);
                    break;
                case "financialclosingdate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.FinancialClosingDate) : query.OrderByDescending(x => x.FinancialClosingDate);
                    break;
                default:
                    query = query.OrderBy(x => x.CompanyId);
                    break;
            }

            var q = query.Select(x =>
            new OptionResponse
            {
                label = x.CompanyName,
                value = x.CompanyId.ToString()
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<Company> GetByID(int id)
        {
            IQueryable<Company> query = _context.Companies;
            query = query.Include(x => x.Currency);
            query = query.Select(x =>
            new Company
            {
                CompanyId = x.CompanyId,
                CompanyName = x.CompanyName,
                Phone = x.Phone,
                Email = x.Email,
                Status = x.Status,
                Address = x.Address,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency.CurrencyName,
                FinancialClosingDate = x.FinancialClosingDate
            });

            return query.SingleOrDefault(x => x.CompanyId.Equals(id));
        }

        public async Task<int> Add(Company item)
        {

            if (_context.Companies.Any(u => u.CompanyName.ToLower().Equals(item.CompanyName.ToLower())))
                throw new ApplicationException("Company is already in use.");


            _context.Companies.Add(item);
            _context.SaveChanges();
            return item.CompanyId;
        }
        public async Task<int> Remove(Company item)
        {
            _context.Companies.Remove(item);
            _context.SaveChanges();
            return item.CompanyId;
        }

        public async Task<int> Update(Company item)
        {

            var data = await GetByID(item.CompanyId);
            if (data == null)
                throw new ApplicationException("Company Not Found");

            if (_context.Companies.Any(u => u.CompanyName.ToLower().Equals(item.CompanyName.ToLower()) && u.CompanyId != item.CompanyId))
                throw new ApplicationException("CompanyName is already in use.");


            data.CompanyName = item.CompanyName;
            data.Phone = item.Phone;
            data.Address = item.Address;
            data.CurrencyId = item.CurrencyId;
            data.Email = item.Email;
            data.Status = item.Status;

            _context.Companies.Update(data);
            _context.SaveChanges();
            return data.CompanyId;
        }


    }
}