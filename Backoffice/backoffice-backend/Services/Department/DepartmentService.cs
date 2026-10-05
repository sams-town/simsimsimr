using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services
{
    public class DepartmentService : IDepartmentService
    {
        private readonly RSSMDbContext _dbContext;
        public DepartmentService(RSSMDbContext dbContext)
        {
            _dbContext = dbContext;
        }

        public async Task<PagedResult<Department>> GetAll(
            string? keyword,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        )
        {
            IQueryable<Department> query = _dbContext.Set<Department>();

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.DepartmentName.ToLower().Contains(keyword.ToLower()));

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            orderBy = string.IsNullOrEmpty(orderBy) ? "departmentid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

             switch (orderBy)
            {
                case "department_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.DepartmentId) : query.OrderByDescending(x => x.DepartmentId);
                    break;
                case "department_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.DepartmentName) : query.OrderByDescending(x => x.DepartmentName);
                    break;
                case "branch_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.DepartmentId);
                    break;
            }

            int total = query.Count();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Department>(await query.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetOption(string? keyword, int? branchId, string? orderBy, string? orderType, int page, int pageSize)
        {
            IQueryable<Department> query = _dbContext.Set<Department>();

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x =>x.DepartmentName.ToLower().Contains(keyword.ToLower()));

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            orderBy = string.IsNullOrEmpty(orderBy) ? "departmentid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "department_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.DepartmentId) : query.OrderByDescending(x => x.DepartmentId);
                    break;
                case "department_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.DepartmentName) : query.OrderByDescending(x => x.DepartmentName);
                    break;
                case "branch_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.DepartmentId);
                    break;
            }

            var q = query.Select(x => new OptionResponse
            {
                label = x.DepartmentName,
                value = x.DepartmentId.ToString(),
            });

            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<Department> GetByID(int id)
        {
            return await _dbContext.Set<Department>().SingleOrDefaultAsync(x => x.DepartmentId.Equals(id));
        }

        public async Task<int> Add(Department item)
        {
            if (_dbContext.Departments.Any(u => u.DepartmentName.ToLower().Equals(item.DepartmentName.ToLower())))
                throw new ApplicationException("DepartmentName is already in use.");

            _dbContext.Set<Department>().Add(item);
            await _dbContext.SaveChangesAsync();
            return item.DepartmentId;
        }

        public async Task<int> Update(Department item)
        {

            if (_dbContext.Departments.Any(u => u.DepartmentName.ToLower().Equals(item.DepartmentName.ToLower()) && u.DepartmentId != item.DepartmentId ))
                throw new ApplicationException("DepartmentName is already in use.");


            _dbContext.Set<Department>().Update(item);
            await _dbContext.SaveChangesAsync();
            return item.DepartmentId;
        }

        public async Task<int> Remove(Department item)
        {
            _dbContext.Set<Department>().Remove(item);
            await _dbContext.SaveChangesAsync();
            return item.DepartmentId;
        }
    }
}
