using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;


namespace RSSMBackend.Services
{
    public class RoleService : IRoleService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public RoleService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<Role>> GetAll(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Role> query = _context.Roles;
            query = query.Include(x => x.Branch);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.RoleName.ToLower().Contains(keyword.ToLower()));
            }

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);


            orderBy = string.IsNullOrEmpty(orderBy) ? "rolename" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "roleid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.RoleID) : query.OrderByDescending(x => x.RoleID);
                    break;
                default:
                    query = query.OrderBy(x => x.RoleName);
                    break;
            }

            var q = query.Select(x => new Role
            {
                RoleID = x.RoleID,
                RoleName = x.RoleName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Role>(await q.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword, int? branchId, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<Role> query = _context.Roles;

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.RoleName.ToLower().Contains(keyword.ToLower()));
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "rolename" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            switch (orderBy)
            {
                case "roleid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.RoleID) : query.OrderByDescending(x => x.RoleID);
                    break;
                default:
                    query = query.OrderBy(x => x.RoleName);
                    break;
            }

            int total = await query.CountAsync();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return await query.Select(g => new
                OptionResponse
            {
                label = g.RoleName,
                value = g.RoleID.ToString()
            }).ToListAsync();
        }

        public async Task<Role> GetByID(int id, int? branchId)
        {
            IQueryable<Role> query = _context.Roles;
            query = query.Include(x => x.Branch);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            query = query.Select(x => new Role
            {
                RoleID = x.RoleID,
                RoleName = x.RoleName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });
            return query.SingleOrDefault(x => x.RoleID.Equals(id));
        }

        public async Task<int> Add(Role item)
        {

            IQueryable<Role> query = _context.Roles;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.RoleName.ToLower().Equals(item.RoleName.ToLower())))
                throw new ApplicationException("RoleName is already in use.");

            _context.Roles.Add(item);
            _context.SaveChanges();
            return item.RoleID;
        }
        public async Task<int> Remove(Role item)
        {
            _context.Roles.Remove(item);
            _context.SaveChanges();
            return item.RoleID;
        }

        public async Task<int> Update(Role item)
        {
            var data = await GetByID(item.RoleID, item.BranchId);
            if (data == null)
                throw new ApplicationException("Role Not Found");

            IQueryable<Role> query = _context.Roles;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.RoleName.ToLower().Equals(item.RoleName.ToLower()) && u.RoleID != item.RoleID))
                throw new ApplicationException("Email is already in use.");

            data.RoleName = item.RoleName;
            _context.Roles.Update(data);
            _context.SaveChanges();
            return data.RoleID;
        }

        public async Task<RolePageAccess> GetPageAccessByPageCode(Page page, int roleId)
        {
            return _context.RolePageAccesses.FirstOrDefault(x => x.RoleID.Equals(roleId) && x.PageCode.Equals(page.ToString()));
        }

        public async Task<List<RolePageAccess>> GetPageAccessByRoleID(int roleId)
        {
            return _context.RolePageAccesses.Where(m => m.RoleID == roleId).ToListAsync().Result;
        }

        public async Task<List<RolePage>> GetAllPageRole()
        {
            return _context.RolePages.ToList();
        }

        public async Task<int> deletePageAccessByRole(int roleId)
        {
            var data = _context.RolePageAccesses.Where(x => x.RoleID == roleId).ToList();
            _context.RolePageAccesses.RemoveRange(data);
            return _context.SaveChanges();
        }

        public async Task<int> AddPageAccess(List<RolePageAccess> items)
        {
            _context.RolePageAccesses.AddRange(items);
            return _context.SaveChanges();
        }
        public async Task<int> AddPageAccess(RolePageAccess item)
        {
            _context.RolePageAccesses.Add(item);
            return _context.SaveChanges();
        }

    }


}