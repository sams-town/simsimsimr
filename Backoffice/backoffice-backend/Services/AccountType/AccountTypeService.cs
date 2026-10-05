using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;


namespace RSSMBackend.Services
{
    public class AccountTypeService : IAccountTypeService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public AccountTypeService(RSSMDbContext context,IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<AccountType>> GetAll(string? keyword,int? branchId,int page, int pageSize,string? orderBy,string? orderType)
        {
            
            IQueryable<AccountType> query = _context.AccountTypes;
            query = query.Include(x => x.Branch);

            if (branchId != null)
            {
                query = query.Where(m => m.BranchId == branchId);
            }

        
            if (!string.IsNullOrEmpty(keyword)){
                query = query.Where( m => m.AccountTypeName.ToLower().Contains(keyword.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "accounttypename" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "accounttypeid": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountTypeId) : query.OrderByDescending(x => x.AccountTypeId);
                    break;
                case "accounttypename": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountTypeName) : query.OrderByDescending(x => x.AccountTypeName);
                     break;
                case "branchid": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.AccountTypeName);
                    break;
            }

            var q = query.Select(x => new AccountType {
                AccountTypeId = x.AccountTypeId,
                AccountTypeName = x.AccountTypeName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });

            int total = await q.CountAsync();
            if(page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<AccountType>(await q.ToListAsync(),total);
        }

        public async Task<List<OptionResponse>> GetAllOption(string? keyword,int? branchId,int page, int pageSize,string? orderBy,string? orderType)
        {
            
            IQueryable<AccountType> query = _context.AccountTypes;
            
            
            if(branchId  != null){
                  query = query.Where( m => m.BranchId == branchId);
            }

        
            if (!string.IsNullOrEmpty(keyword)){
                query = query.Where( m => m.AccountTypeName.ToLower().Contains(keyword.ToLower()));
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "accounttypename" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "accounttypeid": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountTypeId) : query.OrderByDescending(x => x.AccountTypeId);
                    break;
                case "accounttypename": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountTypeName) : query.OrderByDescending(x => x.AccountTypeName);
                     break;
                case "branchid": 
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                default:
                    query = query.OrderBy(x => x.AccountTypeName);
                    break;
            }
           
            int total = await query.CountAsync();
            if(page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

             return await query.Select(g => new
                OptionResponse {
                    label = g.AccountTypeName,
                    value = g.AccountTypeId.ToString(),
                }).ToListAsync();
        }

        public async Task<AccountType> GetByID(int id,int? branchId){
            IQueryable<AccountType> query = _context.AccountTypes;

            query = query.Include(x => x.Branch);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
  
            query  = query.Select(x => new AccountType {
                AccountTypeId = x.AccountTypeId,
                AccountTypeName = x.AccountTypeName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });

            return query.SingleOrDefault(x => x.AccountTypeId == id);
        }

         public async Task<AccountType> GetByName(string name,int? branchId){
            IQueryable<AccountType> query = _context.AccountTypes;
            query = query.Include(x => x.Branch);
            query = query.Include(x => x.Branch);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query  = query.Select(x => new AccountType {
                AccountTypeId = x.AccountTypeId,
                AccountTypeName = x.AccountTypeName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName
            });
            return query.SingleOrDefault(x => x.AccountTypeName.ToLower().Equals(name.ToLower()));
        }

        public async Task<int> Add(AccountType item){
            IQueryable<AccountType> query = _context.AccountTypes;
            if(item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);


            if (query.Any(u => u.AccountTypeName.ToLower().Equals(item.AccountTypeName.ToLower())))
                throw new ApplicationException("AccountTypeName is already in use.");


            _context.AccountTypes.Add(item);
            _context.SaveChanges();
            return item.AccountTypeId;
        }
        public async Task<int> Remove(AccountType item){
            
             _context.AccountTypes.Remove(item);
            _context.SaveChanges();
            return item.AccountTypeId;
        }

        public async Task<int> Update(AccountType item){
            
            var data = await GetByID(item.AccountTypeId,item.BranchId);
            if(data == null)
                throw new ApplicationException("AccountType Not Found");

            IQueryable<AccountType> query = _context.AccountTypes;
            if(item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.AccountTypeName.ToLower().Equals(item.AccountTypeName.ToLower()) && u.AccountTypeId != item.AccountTypeId))
               throw new ApplicationException("AccountType is already in use.");
        

            data.AccountTypeName = item.AccountTypeName;
          

            _context.AccountTypes.Update(data);
            _context.SaveChanges();
            return data.AccountTypeId;
        }

        public async Task<bool> CheckIfExistInCoa(int accountTypeId)
        {
            return await _context.Coa.AnyAsync(x => x.AccountTypeId.Equals(accountTypeId));
        }  
    }
}