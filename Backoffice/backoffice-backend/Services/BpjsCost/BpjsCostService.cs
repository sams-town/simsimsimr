using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services
{
    public class BpjsCostService : IBpjsCostService
    {
        private readonly RSSMDbContext _dbContext;

        public BpjsCostService(RSSMDbContext dbContext)
        {
            _dbContext = dbContext;
        }

        public async Task<PagedResult<BpjsCost>> GetAll(
            string? keyword,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        )
        {
            IQueryable<BpjsCost> query = _dbContext.Set<BpjsCost>();

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.Procedure.ToLower().Contains(keyword.ToLower()));

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            orderBy = string.IsNullOrEmpty(orderBy) ? "costid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "cost_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostId) : query.OrderByDescending(x => x.CostId);
                    break;
                case "procedure":
                    query = orderType == "asc" ? query.OrderBy(x => x.Procedure) : query.OrderByDescending(x => x.Procedure);
                    break;
                case "cost":
                    query = orderType == "asc" ? query.OrderBy(x => x.Cost) : query.OrderByDescending(x => x.Cost);
                    break;
                case "branch_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.CostId);
                    break;
            }

            int total = query.Count();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<BpjsCost>(await query.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetOption(string? keyword, int? branchId, string? orderBy, string? orderType, int page, int pageSize)
        {
            IQueryable<BpjsCost> query = _dbContext.Set<BpjsCost>();

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.Procedure.ToLower().Contains(keyword.ToLower()));

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            orderBy = string.IsNullOrEmpty(orderBy) ? "costid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "cost_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostId) : query.OrderByDescending(x => x.CostId);
                    break;
                case "procedure":
                    query = orderType == "asc" ? query.OrderBy(x => x.Procedure) : query.OrderByDescending(x => x.Procedure);
                    break;
                case "cost":
                    query = orderType == "asc" ? query.OrderBy(x => x.Cost) : query.OrderByDescending(x => x.Cost);
                    break;
                case "branch_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.CostId);
                    break;
            }

            var q = query.Select(x => new OptionResponse
            {
                label = x.Procedure,
                value = x.CostId.ToString(),
            });

            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<BpjsCost> GetByID(int id)
        {
            return await _dbContext.Set<BpjsCost>().SingleOrDefaultAsync(x => x.CostId.Equals(id));
        }

        public async Task<int> Add(BpjsCost item)
        {
            if (_dbContext.Set<BpjsCost>().Any(u => u.Procedure.ToLower().Trim().Equals(item.Procedure.ToLower().Trim())))
                throw new ApplicationException("Procedure is already in use.");

            _dbContext.Set<BpjsCost>().Add(item);
            await _dbContext.SaveChangesAsync();
            return item.CostId;
        }

        public async Task<int> Update(BpjsCost item)
        {
            if (_dbContext.Set<BpjsCost>().Any(u => u.Procedure.ToLower().Trim().Equals(item.Procedure.ToLower().Trim()) && u.CostId != item.CostId))
                throw new ApplicationException("Procedure is already in use.");

            _dbContext.Set<BpjsCost>().Update(item);
            await _dbContext.SaveChangesAsync();
            return item.CostId;
        }

        public async Task<int> Remove(BpjsCost item)
        {
            _dbContext.Set<BpjsCost>().Remove(item);
            await _dbContext.SaveChangesAsync();
            return item.CostId;
        }
    }
}
