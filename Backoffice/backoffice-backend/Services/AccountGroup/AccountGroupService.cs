using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;


namespace RSSMBackend.Services
{
    public class AccountGroupService : IAccountGroupService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public AccountGroupService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<AccountGroup>> GetAll(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<AccountGroup> query = _context.AccountGroups;
            query = query.Include(x => x.Branch);


            if (branchId != null)
            {
                query = query.Where(m => m.Branch.BranchId == branchId);
            }


            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.AccountGroupName.ToLower().Contains(keyword.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "accountgroupname" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "accountgroupid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountGroupId) : query.OrderByDescending(x => x.AccountGroupId);
                    break;
                case "accountgroupname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountGroupName) : query.OrderByDescending(x => x.AccountGroupName);
                    break;
                case "branchid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.AccountGroupName);
                    break;
            }

            var q = query.Select(x =>
            new AccountGroup
            {
                AccountGroupId = x.AccountGroupId,
                AccountGroupName = x.AccountGroupName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<AccountGroup>(await q.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<AccountGroup> query = _context.AccountGroups;
            query = query.Include(x => x.Branch);

            if (branchId != null)
            {
                query = query.Where(m => m.BranchId == branchId);
            }


            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.AccountGroupName.ToLower().Contains(keyword.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "accountgroupname" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "accountgroupid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountGroupId) : query.OrderByDescending(x => x.AccountGroupId);
                    break;
                case "accountgroupname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountGroupName) : query.OrderByDescending(x => x.AccountGroupName);
                    break;
                case "branchid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.AccountGroupName);
                    break;
            }

            int total = await query.CountAsync();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return await query.Select(g => new
               OptionResponse
            {
                label = g.AccountGroupName,
                value = g.AccountGroupId.ToString(),
            }).ToListAsync();
        }

        public async Task<AccountGroup> GetByID(int id, int? branchId)
        {
            IQueryable<AccountGroup> query = _context.AccountGroups;
            query = query.Include(x => x.Branch);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Select(x =>
            new AccountGroup
            {
                AccountGroupId = x.AccountGroupId,
                AccountGroupName = x.AccountGroupName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
            });

            return query.SingleOrDefault(x => x.AccountGroupId == id);
        }

        public async Task<AccountGroup> GetByName(string name, int? branchId)
        {
            IQueryable<AccountGroup> query = _context.AccountGroups;
            query = query.Include(x => x.Branch);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Select(x =>
            new AccountGroup
            {
                AccountGroupId = x.AccountGroupId,
                AccountGroupName = x.AccountGroupName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
            });

            return query.SingleOrDefault(x => x.AccountGroupName.ToLower().Equals(name.ToLower()));
        }

        public async Task<int> Add(AccountGroup item)
        {
            IQueryable<AccountGroup> query = _context.AccountGroups;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);


            if (query.Any(u => u.AccountGroupName.ToLower().Equals(item.AccountGroupName.ToLower())))
                throw new ApplicationException("AccountTypeName is already in use.");


            _context.AccountGroups.Add(item);
            _context.SaveChanges();
            return item.AccountGroupId;
        }

        public async Task<int> Remove(AccountGroup item)
        {

            _context.AccountGroups.Remove(item);
            _context.SaveChanges();
            return item.AccountGroupId;
        }

        public async Task<int> Update(AccountGroup item)
        {

            var data = await GetByID(item.AccountGroupId, item.BranchId);
            if (data == null)
                throw new ApplicationException("AccountType Not Found");

            IQueryable<AccountGroup> query = _context.AccountGroups;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.AccountGroupName.ToLower().Equals(item.AccountGroupName.ToLower()) && u.AccountGroupId != item.AccountGroupId))
                throw new ApplicationException("AccountType is already in use.");

            data.AccountGroupName = item.AccountGroupName;

            _context.AccountGroups.Update(data);
            _context.SaveChanges();
            return data.AccountGroupId;
        }

        public async Task<bool> CheckIfExistInCoa(int accountGroupId)
        {
            return await _context.Coa.AnyAsync(x => x.AccountGroupId.Equals(accountGroupId));
        }

        public async Task<bool> CheckIfExistName(string name, int? accountGroupId)
        {
            return await _context.AccountGroups.AnyAsync(x =>
            x.AccountGroupName.ToLower().Equals(name.ToLower()) &&
            (!accountGroupId.HasValue && x.AccountGroupId != accountGroupId));
        }
    }
}