using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services
{
    public class ContactCategoryService : IContactCategoryService
    {
        private readonly RSSMDbContext _dbContext;
        public ContactCategoryService(RSSMDbContext dbContext)
        {
            _dbContext = dbContext;
        }

        public async Task<PagedResult<ContactCategory>> GetAll(
            string? keyword,
            int? branchId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        )
        {
            IQueryable<ContactCategory> query = _dbContext.Set<ContactCategory>();

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x => x.ContactCategoryName.ToLower().Contains(keyword.ToLower()));

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            orderBy = string.IsNullOrEmpty(orderBy) ? "contact_categoryid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

             switch (orderBy)
            {
                case "contact_category_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.ContactCategoryId) : query.OrderByDescending(x => x.ContactCategoryId);
                    break;
                case "contact_category_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.ContactCategoryName) : query.OrderByDescending(x => x.ContactCategoryName);
                    break;
                case "branch_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.ContactCategoryId);
                    break;
            }

            int total = query.Count();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<ContactCategory>(await query.ToListAsync(), total);
        }

        public async Task<List<OptionResponse>> GetOption(string? keyword, int? branchId, string? orderBy, string? orderType, int page, int pageSize)
        {
            IQueryable<ContactCategory> query = _dbContext.Set<ContactCategory>();

            if (!string.IsNullOrEmpty(keyword))
                query = query.Where(x =>x.ContactCategoryName.ToLower().Contains(keyword.ToLower()));

            if (branchId != null)
                query = query.Where(m => m.BranchId == branchId);

            orderBy = string.IsNullOrEmpty(orderBy) ? "contact_categoryid" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "contact_category_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.ContactCategoryId) : query.OrderByDescending(x => x.ContactCategoryId);
                    break;
                case "contact_category_name":
                    query = orderType == "asc" ? query.OrderBy(x => x.ContactCategoryName) : query.OrderByDescending(x => x.ContactCategoryName);
                    break;
                case "branch_id":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.ContactCategoryId);
                    break;
            }

            var q = query.Select(x => new OptionResponse
            {
                label = x.ContactCategoryName,
                value = x.ContactCategoryId.ToString(),
            });

            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<ContactCategory> GetByID(int id)
        {
            return await _dbContext.Set<ContactCategory>().SingleOrDefaultAsync(x => x.ContactCategoryId.Equals(id));
        }

        public async Task<int> Add(ContactCategory item)
        {
            if (_dbContext.ContactCategories.Any(u => u.ContactCategoryName.ToLower().Equals(item.ContactCategoryName.ToLower())))
                throw new ApplicationException("ContactCategoryName is already in use.");

            _dbContext.Set<ContactCategory>().Add(item);
            await _dbContext.SaveChangesAsync();
            return item.ContactCategoryId;
        }

        public async Task<int> Update(ContactCategory item)
        {

            if (_dbContext.ContactCategories.Any(u => u.ContactCategoryName.ToLower().Equals(item.ContactCategoryName.ToLower()) && u.ContactCategoryId != item.ContactCategoryId ))
                throw new ApplicationException("ContactCategoryName is already in use.");


            _dbContext.Set<ContactCategory>().Update(item);
            await _dbContext.SaveChangesAsync();
            return item.ContactCategoryId;
        }

        public async Task<int> Remove(ContactCategory item)
        {
            _dbContext.Set<ContactCategory>().Remove(item);
            await _dbContext.SaveChangesAsync();
            return item.ContactCategoryId;
        }
    }
}
