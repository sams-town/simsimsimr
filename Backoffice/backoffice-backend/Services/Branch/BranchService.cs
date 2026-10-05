using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;

namespace RSSMBackend.Services
{
    public class BranchService : IBranchService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public BranchService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<Branch>> GetAll(string? keyword, string? status, int? company, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Branch> query = _context.Branches;
            query = query.Include(x => x.Company)
            .Include(x => x.Currency);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.Email.ToLower().Contains(keyword.ToLower())
                        || m.Phone.ToLower().Contains(keyword.ToLower())
                        || m.BranchName.ToLower().Contains(keyword.ToLower())
                        || m.Status.ToLower().Contains(keyword.ToLower())
                        || m.Currency.CurrencyName.ToLower().Contains(keyword.ToLower())
                         || m.Company.CompanyName.ToLower().Contains(keyword.ToLower())
                );
            }

            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }

            if (company != null)
            {
                query = query.Where(m => m.CompanyId == company);
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "branchid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "branchid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "email":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Email) : query.OrderByDescending(x => x.Email);
                    break;
                case "branchame":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchName) : query.OrderByDescending(x => x.BranchName);
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
                    query = query.OrderBy(x => x.BranchId);
                    break;
            }

            var q = query.Select(x =>
            new Branch
            {
                BranchId = x.BranchId,
                BranchName = x.BranchName,
                Phone = x.Phone,
                Email = x.Email,
                Status = x.Status,
                Address = x.Address,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency.CurrencyName,
                CompanyId = x.CompanyId,
                CompanyName = x.Company.CompanyName,
                FinancialClosingDate = x.FinancialClosingDate
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Branch>(await q.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, string? status, int? company, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Branch> query = _context.Branches;
            query = query.Include(x => x.Company)
            .Include(x => x.Currency);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.Email.ToLower().Contains(keyword.ToLower())
                    || m.Phone.ToLower().Contains(keyword.ToLower())
                    || m.BranchName.ToLower().Contains(keyword.ToLower())
                    || m.Status.ToLower().Contains(keyword.ToLower())
                    || m.Currency.CurrencyName.ToLower().Contains(keyword.ToLower())
                    || m.Company.CompanyName.Contains(keyword.ToLower()));
            }

            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }

            if (company != null)
            {
                query = query.Where(m => m.CompanyId == company);
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "branchid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "branchid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "email":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Email) : query.OrderByDescending(x => x.Email);
                    break;
                case "branchame":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchName) : query.OrderByDescending(x => x.BranchName);
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
                    query = query.OrderBy(x => x.BranchId);
                    break;
            }

            var q = query.Select(x =>
            new OptionResponse
            {
                label = x.BranchName,
                value = x.BranchId.ToString()
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<Branch> GetByID(int id)
        {
            IQueryable<Branch> query = _context.Branches;
            query = query.Include(x => x.Currency)
            .Include(x => x.Company);
            query = query.Select(x =>
            new Branch
            {
                BranchId = x.BranchId,
                BranchName = x.BranchName,
                Phone = x.Phone,
                Email = x.Email,
                Status = x.Status,
                Address = x.Address,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency.CurrencyName,
                CompanyId = x.CompanyId,
                CompanyName = x.Company.CompanyName,
                FinancialClosingDate = x.FinancialClosingDate
            });
            return query.SingleOrDefault(x => x.BranchId == id);
        }

        public async Task<int> Add(Branch item)
        {

            if (_context.Branches.Any(u => u.BranchName.ToLower().Equals(item.BranchName.ToLower()) && u.CompanyId == item.CompanyId))
                throw new ApplicationException("Branch is already in use.");

            _context.Branches.Add(item);
            _context.SaveChanges();
            return item.BranchId;
        }
        public async Task<int> Remove(Branch item)
        {
            _context.Branches.Remove(item);
            _context.SaveChanges();
            return item.BranchId;
        }

        public async Task<int> Update(Branch item)
        {

            var data = await GetByID(item.BranchId);
            if (data == null)
                throw new ApplicationException("Branch Not Found");

            if (_context.Branches.Any(u => u.BranchName.ToLower().Equals(item.BranchName.ToLower()) && u.BranchId != item.BranchId && u.CompanyId == item.CompanyId))
                throw new ApplicationException("Branch is already in use.");


            data.BranchName = item.BranchName;
            data.Phone = item.Phone;
            data.Address = item.Address;
            data.CurrencyId = item.CurrencyId;
            data.Email = item.Email;
            data.CompanyId = item.CompanyId;
            data.Status = item.Status;
            data.FinancialClosingDate = item.FinancialClosingDate;

            _context.Branches.Update(data);
            _context.SaveChanges();
            return data.BranchId;
        }

        public async Task<bool> checkIfExistCompany(int companyId, int? branchId)
        {
            IQueryable<Branch> query = _context.Branches;
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            return query.Any(x => x.CompanyId == companyId);
        }


    }
}