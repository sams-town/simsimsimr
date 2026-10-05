using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services
{
    public class CostCenterService : ICostCenterService
    {
        private readonly RSSMDbContext _dbContext;
        public CostCenterService(RSSMDbContext dbContext)
        {
            _dbContext = dbContext;
        }

        public async Task<PagedResult<CostCenter>> GetAll(
            string? keyword,
            int? departmentId,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        )
        {
            IQueryable<CostCenter> query = _dbContext.Set<CostCenter>();

            query = query.Include(x => x.Department)
                .Include(x => x.Branch);

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.CostCenterCode.ToLower().Contains(keyword.ToLower())
                    || (x.CostCenterName != null && x.CostCenterName.ToLower().Contains(keyword.ToLower()))
                    || (x.Department != null && x.Department.DepartmentName.ToLower().Contains(keyword.ToLower()))
                );

            if (departmentId != null)
                query = query.Where(m => m.DepartmentId == departmentId);

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);    

            orderBy = string.IsNullOrEmpty(orderBy) ? "costcenterid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "costcenter_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostcenterId) : query.OrderByDescending(x => x.CostcenterId);
                    break;
                case "cost_center_code":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostCenterCode) : query.OrderByDescending(x => x.CostCenterCode);
                    break;
                case "cost_center_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostCenterName) : query.OrderByDescending(x => x.CostCenterName);
                    break;
                case "department_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.DepartmentId) : query.OrderByDescending(x => x.DepartmentId);
                    break;
                case "department_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.Department.DepartmentName) : query.OrderByDescending(x => x.Department.DepartmentName);
                    break;    
                default:
                    query = query.OrderBy(x => x.CostcenterId);
                    break;
            }

            int total = query.Count();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<CostCenter>(await query.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetOption(string? keyword, int? departmentId,int? branchId, string? orderBy, string? orderType, int page, int pageSize)
        {
            IQueryable<CostCenter> query = _dbContext.Set<CostCenter>();

             query = query.Include(x => x.Department)
                .Include(x => x.Branch);

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.CostCenterCode.ToLower().Contains(keyword.ToLower())
                    || (x.Department != null && x.Department.DepartmentName.ToLower().Contains(keyword.ToLower()))
                );

            if (departmentId != null)
                query = query.Where(m => m.DepartmentId == departmentId);

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);     

            orderBy = string.IsNullOrEmpty(orderBy) ? "costcenterid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "costcenter_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostcenterId) : query.OrderByDescending(x => x.CostcenterId);
                    break;
                case "cost_center_code":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostCenterCode) : query.OrderByDescending(x => x.CostCenterCode);
                    break;
                case "cost_center_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.CostCenterName) : query.OrderByDescending(x => x.CostCenterName);
                    break;
                case "department_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.DepartmentId) : query.OrderByDescending(x => x.DepartmentId);
                    break;
                default:
                    query = query.OrderBy(x => x.CostcenterId);
                    break;
            }

            var q = query.Select(x => new OptionResponse
            {
                label = x.CostCenterCode,
                value = x.CostcenterId.ToString(),
                desc = x.CostCenterName
            });

            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<CostCenter> GetByID(int id)
        {
            IQueryable<CostCenter> query = _dbContext.Set<CostCenter>();

             query = query.Include(x => x.Department)
                .Include(x => x.Branch);

            return await query.SingleOrDefaultAsync(x => x.CostcenterId.Equals(id));
        }

        public async Task<int> Add(CostCenter item)
        {
             if (_dbContext.CostCenters.Any(u => u.CostCenterCode.ToLower().Equals(item.CostCenterCode.ToLower())))
                throw new ApplicationException("CostCenterCode is already in use.");

            _dbContext.Set<CostCenter>().Add(item);
            await _dbContext.SaveChangesAsync();
            return item.CostcenterId;
        }

        public async Task<int> Update(CostCenter item)
        {

             if (_dbContext.CostCenters.Any(u => u.CostCenterCode.ToLower().Equals(item.CostCenterCode.ToLower()) && u.CostcenterId != item.CostcenterId ))
                throw new ApplicationException("CostCenterCode is already in use.");

            _dbContext.Set<CostCenter>().Update(item);
            await _dbContext.SaveChangesAsync();
            return item.CostcenterId;
        }

        public async Task<int> Remove(CostCenter item)
        {
            _dbContext.Set<CostCenter>().Remove(item);
            await _dbContext.SaveChangesAsync();
            return item.CostcenterId;
        }
    }
}
