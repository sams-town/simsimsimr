using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;
using Microsoft.EntityFrameworkCore.Storage;

namespace RSSMBackend.Services
{
    public class COAService : ICOAService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        private readonly IOptionService _optionService; // Injected
        public COAService(RSSMDbContext context, IOptions<AppSettings> appSettings, IOptionService optionService)
        {
            _context = context;
            _appSettings = appSettings.Value;
            _optionService = optionService;
        }

        public async Task<PagedResult<Coa>> GetAll(string? keyword, string? status, int? level, int? branch, string? NormalPosition, bool? IsCashBank, bool? IsCashFlow, int? parent, int? accountGroup, int? accountType, int? currancy, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Coa> query = _context.Coa;

            query = query.Include(x => x.AccountGroup)
            .Include(x => x.AccountType)
            .Include(x => x.Branch)
            .Include(x => x.Currency)
            .Include(x => x.Parent)
            .Include(x => x.Created)
            .Include(x => x.Updated);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.CoaCode.ToLower().Contains(keyword.ToLower())
                        || m.CoaName.ToLower().Contains(keyword.ToLower())
                        || (m.AccountGroup != null && m.AccountGroup.AccountGroupName.ToLower().Contains(keyword.ToLower()))
                        ||  (m.AccountType != null && m.AccountType.AccountTypeName.ToLower().Contains(keyword.ToLower()))
                        || m.Status.ToLower().Contains(keyword.ToLower())
                        // || (m.Parent != null && m.Parent.CoaName.ToLower().Contains(keyword.ToLower()))
                );
            }

            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }

            if (branch != null)
            {
                query = query.Where(m => m.BranchId == branch);
            }

            if (NormalPosition != null)
            {
                query = query.Where(m => m.NormalPosition.ToLower().Equals(NormalPosition));
            }

            if (currancy != null)
            {
                query = query.Where(m => m.CurrencyId == currancy);
            }

            if (IsCashBank != null)
            {
                if (IsCashBank == true) 
                {
                     string prefix = "101";
                     if (branch != null) {
                        var option = await _optionService.GetByName("ACCOUNT_ACCOUNT_CASH_BANK", branch.Value);
                        if (option != null && !string.IsNullOrEmpty(option.OptionValue)) prefix = option.OptionValue;
                     }
                     query = query.Where(x => x.CoaCode.StartsWith(prefix));
                }
                else
                {
                    query = query.Where(m => m.IsCashBank == IsCashBank);
                }
            }

            if (IsCashFlow != null)
            {
                query = query.Where(m => m.IsCashFlow == IsCashFlow);
            }

            if (parent != null)
            {
                query = query.Where(m => m.ParentId == parent);
            }

            if (accountGroup != null)
            {
                query = query.Where(m => m.AccountGroupId == accountGroup);
            }

            if (accountType != null)
            {
                query = query.Where(m => m.AccountTypeId == accountType);
            }

            if (level != null)
            {
                query = query.Where(m => m.CoaLevel == level);
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "coacode" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "coacode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaCode) : query.OrderByDescending(x => x.CoaCode);
                    break;
                case "coaname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaName) : query.OrderByDescending(x => x.CoaName);
                    break;
                case "accountgroup":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountGroup.AccountGroupName) : query.OrderByDescending(x => x.AccountGroup.AccountGroupName);
                    break;
                case "accounttype":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountType.AccountTypeName) : query.OrderByDescending(x => x.AccountType.AccountTypeName);
                    break;
                case "status":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break;
                default:
                    query = query.OrderBy(x => x.CoaCode);
                    break;
            }

            var q = query.Select(x =>
            new Coa
            {
                CoaId = x.CoaId,
                CoaCode = x.CoaCode,
                CoaName = x.CoaName,
                CoaLevel = x.CoaLevel,
                AccountTypeId = x.AccountTypeId,
                AccountTypeName =  x.AccountType != null ? x.AccountType.AccountTypeName : null,
                AccountGroupId = x.AccountGroupId,
                AccountGroupName =  x.AccountGroup != null ? x.AccountGroup.AccountGroupName : null,
                ParentId = x.ParentId,
                ParentName =  x.Parent != null ? x.Parent.CoaName : null,
                ParentCode = x.ParentCode,
                IsCashBank = x.IsCashBank,
                IsCashFlow = x.IsCashFlow,
                Balance = x.Balance,
                NormalPosition = x.NormalPosition,
                Status = x.Status,
                BranchId = x.BranchId,
                BranchName =  x.Branch != null ? x.Branch.BranchName : null,
                CreatedBy = x.CreatedBy,
                CreatedDate = x.CreatedDate,
                UpdatedBy = x.UpdatedBy,
                UpdatedDate = x.UpdatedDate,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency != null ? x.Currency.CurrencyName :null,
                CreatedName = x.Created != null ? x.Created.DisplayName : null,
                UpdatedName = x.Updated != null ? x.Updated.DisplayName: null,
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Coa>(await q.ToListAsync(), total);
        }
        public async Task<List<OptionResponse>> GetAllOption(string? keyword, string? status, int? level, int? branch, string? NormalPosition, bool? IsCashBank, bool? IsCashFlow, string? parentCode, string? startswith, int? parent, int? accountGroup, int? accountType, int? currancyId, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Coa> query = _context.Coa;

            query = query.Include(x => x.AccountGroup)
            .Include(x => x.AccountType)
            .Include(x => x.Branch)
            .Include(x => x.Currency)
            .Include(x => x.Parent)
            .Include(x => x.Created)
            .Include(x => x.Updated);

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.CoaCode.ToLower().Contains(keyword.ToLower())
                        || m.CoaName.ToLower().Contains(keyword.ToLower())
                        || (m.AccountGroup != null && m.AccountGroup.AccountGroupName.ToLower().Contains(keyword.ToLower()))
                        ||  (m.AccountType != null && m.AccountType.AccountTypeName.ToLower().Contains(keyword.ToLower()))
                        || m.Status.ToLower().Contains(keyword.ToLower())
                        // || (m.Parent != null && m.Parent.ParentName.ToLower().Contains(keyword.ToLower()))
                );
            }

            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(m => m.Status.ToLower().Equals(status.ToLower()));
            }

            if (branch != null)
            {
                query = query.Where(m => m.BranchId == branch);
            }

            if (NormalPosition != null)
            {
                query = query.Where(m => m.NormalPosition.ToLower().Equals(NormalPosition));
            }

            if (IsCashBank != null)
            {
                query = query.Where(m => m.IsCashBank == IsCashBank);
            }

            if (IsCashFlow != null)
            {
                query = query.Where(m => m.IsCashFlow == IsCashFlow);
            }

            if (parent != null)
            {
                query = query.Where(m => m.ParentId == parent);
            }

            if (parentCode != null)
            {
                query = query.Where(m => m.Parent.CoaCode.Equals(parentCode));
            }

            if (!string.IsNullOrEmpty(startswith))
            {
                query = query.Where(m => m.CoaCode.StartsWith(startswith));
            }

            if (accountGroup != null)
            {
                query = query.Where(m => m.AccountGroupId == accountGroup);
            }

            if (accountType != null)
            {
                query = query.Where(m => m.AccountTypeId == accountType);
            }

            if (level != null)
            {
                query = query.Where(m => m.CoaLevel == level);
            }

            if (currancyId != null)
            {
                query = query.Where(m => m.CurrencyId == currancyId);
            }


            orderBy = string.IsNullOrEmpty(orderBy) ? "coacode" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "coacode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaCode) : query.OrderByDescending(x => x.CoaCode);
                    break;
                case "coaname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaName) : query.OrderByDescending(x => x.CoaName);
                    break;
                case "accountgroup":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountGroup.AccountGroupName) : query.OrderByDescending(x => x.AccountGroup.AccountGroupName);
                    break;
                case "accounttype":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.AccountType.AccountTypeName) : query.OrderByDescending(x => x.AccountType.AccountTypeName);
                    break;
                case "status":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break;
                default:
                    query = query.OrderBy(x => x.CoaCode);
                    break;
            }

            var q = query.Select(x =>
            new OptionResponse
            {
                value = x.CoaCode,
                label = @$"[{x.CoaCode}] {x.CoaName}",
                desc = x.CoaCode,
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return await q.ToListAsync();
        }

        public async Task<Coa> GetByID(int id, int? branchId)
        {
            IQueryable<Coa> query = _context.Coa;

            query = query.Include(x => x.AccountGroup)
            .Include(x => x.AccountType)
            .Include(x => x.Branch)
            .Include(x => x.Currency)
            .Include(x => x.Parent)
            .Include(x => x.Created)
            .Include(x => x.Updated);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

                
            query = query.Select(x =>
            new Coa
            {
                CoaId = x.CoaId,
                CoaCode = x.CoaCode,
                CoaName = x.CoaName,
                CoaLevel = x.CoaLevel,
                AccountTypeId = x.AccountTypeId,
                AccountTypeName =  x.AccountType != null ? x.AccountType.AccountTypeName : null,
                AccountGroupId = x.AccountGroupId,
                AccountGroupName =  x.AccountGroup != null ? x.AccountGroup.AccountGroupName : null,
                ParentId = x.ParentId,
                ParentName =  x.Parent != null ? x.Parent.CoaName : null,
                ParentCode = x.ParentCode,
                IsCashBank = x.IsCashBank,
                IsCashFlow = x.IsCashFlow,
                Balance = x.Balance,
                NormalPosition = x.NormalPosition,
                Status = x.Status,
                BranchId = x.BranchId,
                BranchName =  x.Branch != null ? x.Branch.BranchName : null,
                CreatedBy = x.CreatedBy,
                CreatedDate = x.CreatedDate,
                UpdatedBy = x.UpdatedBy,
                UpdatedDate = x.UpdatedDate,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency != null ? x.Currency.CurrencyName :null,
                CreatedName = x.Created != null ? x.Created.DisplayName : null,
                UpdatedName = x.Updated != null ? x.Updated.DisplayName: null,
            });

            return query.SingleOrDefault(x => x.CoaId == id);
        }

        public async Task<Coa> GetByCode(string code, int? branchId)
        {
            IQueryable<Coa> query = _context.Coa;
            
            query = query.Include(x => x.AccountGroup)
            .Include(x => x.AccountType)
            .Include(x => x.Branch)
            .Include(x => x.Currency)
            .Include(x => x.Parent)
            .Include(x => x.Created)
            .Include(x => x.Updated);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Select(x =>
            new Coa
            {
                CoaId = x.CoaId,
                CoaCode = x.CoaCode,
                CoaName = x.CoaName,
                CoaLevel = x.CoaLevel,
                AccountTypeId = x.AccountTypeId,
                AccountTypeName = x.AccountType.AccountTypeName,
                AccountGroupId = x.AccountGroupId,
                AccountGroupName = x.AccountGroup.AccountGroupName,
                ParentId = x.ParentId,
                ParentName = x.Parent != null ? x.Parent.CoaName : null,
                ParentCode = x.ParentCode,
                IsCashBank = x.IsCashBank,
                IsCashFlow = x.IsCashFlow,
                Balance = x.Balance,
                NormalPosition = x.NormalPosition,
                Status = x.Status,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                CreatedBy = x.CreatedBy,
                CreatedDate = x.CreatedDate,
                UpdatedBy = x.UpdatedBy,
                UpdatedDate = x.UpdatedDate,
                CurrencyId = x.CurrencyId,
                CurrencyName = x.Currency.CurrencyName,
                CreatedName = x.Created.DisplayName,
                UpdatedName = x.Updated.DisplayName,
            });

            return query.SingleOrDefault(x => x.CoaCode.ToLower().Equals(code.ToLower()));
        }

        public async Task<int> Add(Coa item)
        {

            IQueryable<Coa> query = _context.Coa;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            if (query.Any(u => u.CoaCode.ToLower().Equals(item.CoaCode.ToLower())))
                throw new ApplicationException($"COA '{item.CoaCode}' is already in use.");

            if (item.ParentId != null)
            {
                var parent = _context.Coa.SingleOrDefault(x => x.CoaId == item.ParentId);
                
                // If not found by ID, try finding by Code (frontend might send Code in ParentId field)
                if (parent == null)
                {
                     string parentCodeVal = item.ParentId.ToString();
                     // Check current branch first if applicable, or just code
                     parent = _context.Coa.FirstOrDefault(x => x.CoaCode == parentCodeVal && x.BranchId == item.BranchId);
                     
                     if (parent != null)
                     {
                         // Found by code, so update the ParentId to the real ID
                         item.ParentId = parent.CoaId;
                         item.ParentCode = parent.CoaCode;
                     }
                }

                if (parent != null)
                {
                    item.CoaLevel = (parent.CoaLevel ?? 0) + 1;
                    item.ParentCode = parent.CoaCode;
                }
                else
                {
                     // Parent specified but not found by ID or Code -> Invalid
                     throw new ApplicationException($"Parent COA not found (ID/Code: {item.ParentId})");
                }
            }
            else
            {
                item.CoaLevel = 1;
            }

            _context.Coa.Add(item);
            _context.SaveChanges();
            return item.CoaId;
        }
        public async Task<int> Remove(Coa item)
        {
            _context.Coa.Remove(item);
            _context.SaveChanges();
            return item.CoaId;
        }

        public async Task<int> Update(Coa item, bool fullmode)
        {
            var data = await GetByID(item.CoaId, item.BranchId);
            if (data == null)
                throw new ApplicationException("COA Not Found");

            if (_context.Coa.Any(u => u.CoaCode.ToLower().Equals(item.CoaCode.ToLower()) && u.CoaId != item.CoaId))
                throw new ApplicationException("COA is already in use.");

            if (fullmode)
            {
                if (item.ParentId != null)
                {
                    var parent = _context.Coa.SingleOrDefault(x => x.CoaId == item.ParentId);
                    
                    // If not found by ID, try finding by Code (frontend might send Code in ParentId field)
                    if (parent == null)
                    {
                         string parentCodeVal = item.ParentId.ToString();
                         parent = _context.Coa.FirstOrDefault(x => x.CoaCode == parentCodeVal && x.BranchId == item.BranchId);
                         
                         if (parent != null)
                         {
                             item.ParentId = parent.CoaId;
                             item.ParentCode = parent.CoaCode;
                         }
                    }

                    if (parent != null)
                    {
                         // Parent found, we'll settle CoaLevel below
                    }
                     else
                    {
                         throw new ApplicationException($"Parent COA not found (ID/Code: {item.ParentId})");
                    }
                }
                
                data.CoaCode = item.CoaCode;
                data.CoaName = item.CoaName;
                data.NormalPosition = item.NormalPosition;
                data.AccountGroupId = item.AccountGroupId;
                data.AccountTypeId = item.AccountTypeId;
                data.IsCashBank = item.IsCashBank;
                data.IsCashFlow = item.IsCashFlow;
                data.ParentId = item.ParentId;
                data.ParentCode = item.ParentCode;
                
                // Also update CoaLevel if Parent Changed
                if (item.ParentId != null) {
                    var p = _context.Coa.FirstOrDefault(x => x.CoaId == item.ParentId);
                    if(p!=null) data.CoaLevel = (p.CoaLevel ?? 0) + 1;
                    else data.CoaLevel = 1; 
                } else {
                     data.CoaLevel = 1;
                }

                data.Balance = item.Balance;
                data.CurrencyId = item.CurrencyId;
                data.ReconciledDate = item.ReconciledDate;
            }
            else
            {
                data.CoaName = item.CoaName;
            }
            
            data.Status = item.Status;

            data.UpdatedBy = item.UpdatedBy;
            data.UpdatedDate = item.UpdatedDate;

            _context.Coa.Update(data);
            _context.SaveChanges();
            return data.CoaId;
        }
        public async Task<bool> checkIfExistGroup(int id, int? branchId)
        {
            IQueryable<Coa> query = _context.Coa;
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            return query.Any(x => x.AccountGroupId == id);
        }


        public async Task<CoaMapping> GetMappingByCode(string code)
        {
            IQueryable<CoaMapping> query = _context.CoaMappings;

            query = query.Where(x => x.CoaSource.Equals(code));

            return await query.FirstOrDefaultAsync();
        }

        public async Task<bool> checkIfExistType(int id, int? branchId)
        {
            IQueryable<Coa> query = _context.Coa;
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            return query.Any(x => x.AccountTypeId == id);
        }

        public async Task<bool> checkIfExistCoa(string coa, int? branchId)
        {
            IQueryable<Coa> query = _context.Coa;
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            return query.Any(x => x.CoaCode.Equals(coa));
        }

        public async Task RemoveAllByBranch(int? branchId)
        {
            var items = _context.Coa.Where(x => x.BranchId == branchId).ToList();
            _context.Coa.RemoveRange(items);
            await _context.SaveChangesAsync();
        }
    }
}