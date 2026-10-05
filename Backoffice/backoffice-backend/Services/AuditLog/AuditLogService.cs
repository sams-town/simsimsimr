using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services
{
    public class AuditLogService : IAuditLogService
    {
        private readonly RSSMDbContext _dbContext;
        public AuditLogService(RSSMDbContext dbContext)
        {
            _dbContext = dbContext;
        }

        public async Task<PagedResult<AuditLog>> GetAll(
            string? keyword,
            int? branchId,
            string? status,
            DateTime? createdAtFrom,
            DateTime? createdAtTo,
            string? module,
            string? operation,
            int? userId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        )
        {
            IQueryable<AuditLog> query = _dbContext.Set<AuditLog>();

            if (!string.IsNullOrEmpty(keyword))
            {
                var k = keyword.ToLower();
                query = query.Where(x =>
                    (x.Module != null && x.Module.ToLower().Contains(k)) ||
                    (x.RefId != null && x.RefId.ToLower().Contains(k)) ||
                    (x.Operation != null && x.Operation.ToLower().Contains(k)) ||
                    (x.IpAddress != null && x.IpAddress.ToLower().Contains(k)) ||
                    (x.Status != null && x.Status.ToLower().Contains(k)));
            }

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            if (!string.IsNullOrEmpty(status))
                query = query.Where(m => m.Status != null && m.Status.ToLower() == status.ToLower());

            if (!string.IsNullOrEmpty(module))
                query = query.Where(m => m.Module == module);

            if (!string.IsNullOrEmpty(operation))
                query = query.Where(m => m.Operation == operation);

            if (userId != null)
                query = query.Where(m => m.UserId == userId);

            if (createdAtFrom != null)
                query = query.Where(m => m.CreatedAt >= createdAtFrom.Value.Date);

            // Inclusive of the whole end day: the caller passes a date, rows carry a timestamp.
            if (createdAtTo != null)
                query = query.Where(m => m.CreatedAt < createdAtTo.Value.Date.AddDays(1));

            orderBy = string.IsNullOrEmpty(orderBy) ? "created_at" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "desc" : orderType.ToLower();

            switch (orderBy)
            {
                case "id":
                    query = orderType == "asc" ? query.OrderBy(x => x.Id) : query.OrderByDescending(x => x.Id);
                    break;
                case "module":
                    query = orderType == "asc" ? query.OrderBy(x => x.Module) : query.OrderByDescending(x => x.Module);
                    break;
                case "ref_id":
                case "refid":
                    query = orderType == "asc" ? query.OrderBy(x => x.RefId) : query.OrderByDescending(x => x.RefId);
                    break;
                case "operation":
                    query = orderType == "asc" ? query.OrderBy(x => x.Operation) : query.OrderByDescending(x => x.Operation);
                    break;
                case "user_id":
                case "userid":
                    query = orderType == "asc" ? query.OrderBy(x => x.UserId) : query.OrderByDescending(x => x.UserId);
                    break;
                case "ip_address":
                case "ipaddress":
                    query = orderType == "asc" ? query.OrderBy(x => x.IpAddress) : query.OrderByDescending(x => x.IpAddress);
                    break;
                case "branch_id":
                case "branchid":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "status":
                    query = orderType == "asc" ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break;
                case "created_at":
                case "createdat":
                default:
                    query = orderType == "asc" ? query.OrderBy(x => x.CreatedAt) : query.OrderByDescending(x => x.CreatedAt);
                    break;
            }

            int total = await query.CountAsync();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            var items = await query.ToListAsync();
            await ResolveNames(items);

            return new PagedResult<AuditLog>(items, total);
        }

        public async Task<List<AuditLog>> GetTimeline(string module, string refId)
        {
            var items = await _dbContext.Set<AuditLog>()
                .Where(x => x.Module == module && x.RefId == refId)
                .OrderByDescending(x => x.CreatedAt)
                .ThenByDescending(x => x.Id)
                .ToListAsync();

            await ResolveNames(items);
            return items;
        }

        public async Task<AuditLog?> GetByID(long id)
        {
            var item = await _dbContext.Set<AuditLog>().SingleOrDefaultAsync(x => x.Id.Equals(id));
            if (item != null)
                await ResolveNames(new List<AuditLog> { item });
            return item;
        }

        public async Task<AuditLogFilterOptions> GetFilterOptions()
        {
            var modules = await _dbContext.Set<AuditLog>()
                .Where(x => x.Module != null)
                .Select(x => x.Module)
                .Distinct()
                .OrderBy(x => x)
                .ToListAsync();

            var operations = await _dbContext.Set<AuditLog>()
                .Where(x => x.Operation != null)
                .Select(x => x.Operation!)
                .Distinct()
                .OrderBy(x => x)
                .ToListAsync();

            // Only users that actually appear in the log, so the dropdown stays short.
            var userIds = await _dbContext.Set<AuditLog>()
                .Where(x => x.UserId != null)
                .Select(x => x.UserId!.Value)
                .Distinct()
                .ToListAsync();

            var intUserIds = userIds.Select(x => (int)x).ToList();
            var users = await _dbContext.Users
                .Where(u => intUserIds.Contains(u.UserID))
                .Select(u => new AuditLogUserOption { UserId = u.UserID, DisplayName = u.DisplayName })
                .OrderBy(u => u.DisplayName)
                .ToListAsync();

            return new AuditLogFilterOptions
            {
                Modules = modules,
                Operations = operations,
                Users = users
            };
        }

        public async Task<long> Add(AuditLog item)
        {
            _dbContext.Set<AuditLog>().Add(item);
            await _dbContext.SaveChangesAsync();
            return item.Id;
        }

        public async Task AddRange(List<AuditLog> items)
        {
            _dbContext.Set<AuditLog>().AddRange(items);
            await _dbContext.SaveChangesAsync();
        }

        public async Task<long> Update(AuditLog item)
        {
            _dbContext.Set<AuditLog>().Update(item);
            await _dbContext.SaveChangesAsync();
            return item.Id;
        }

        /// <summary>
        /// Fills the NotMapped display fields. The log stores ids only; names are resolved on read
        /// so a renamed user shows their current name everywhere.
        /// </summary>
        private async Task ResolveNames(List<AuditLog> items)
        {
            if (items.Count == 0) return;

            var userIds = items.Where(x => x.UserId != null)
                               .Select(x => (int)x.UserId!.Value)
                               .Distinct()
                               .ToList();

            var branchIds = items.Where(x => x.BranchId != null)
                                 .Select(x => x.BranchId!.Value)
                                 .Distinct()
                                 .ToList();

            var users = userIds.Count == 0
                ? new Dictionary<int, (string? Name, string? Role)>()
                : await _dbContext.Users
                    .Where(u => userIds.Contains(u.UserID))
                    .Select(u => new { u.UserID, u.DisplayName, RoleName = u.Role != null ? u.Role.RoleName : null })
                    .ToDictionaryAsync(u => u.UserID, u => (Name: (string?)u.DisplayName, Role: (string?)u.RoleName));

            var branches = branchIds.Count == 0
                ? new Dictionary<int, string?>()
                : await _dbContext.Branches
                    .Where(b => branchIds.Contains(b.BranchId))
                    .Select(b => new { b.BranchId, b.BranchName })
                    .ToDictionaryAsync(b => b.BranchId, b => (string?)b.BranchName);

            foreach (var item in items)
            {
                if (item.UserId != null && users.TryGetValue((int)item.UserId.Value, out var u))
                {
                    item.UserName = u.Name;
                    item.RoleName = u.Role;
                }

                if (item.BranchId != null && branches.TryGetValue(item.BranchId.Value, out var b))
                    item.BranchName = b;
            }
        }
    }
}
