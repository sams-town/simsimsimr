namespace RSSMBackend.Services.PettyCash;

using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;
using TDPBackend.Helpers;

public class PettyCashService : IPettyCashService
{
    private readonly RSSMDbContext _context;
    private readonly ITransactionService _transactionService;
    private readonly IJournalService _journalService;
    private readonly ICOAService _coaService;
    private readonly IOptionService _optionService;
    private readonly AuditTrailUtils _auditTrailUtils;

    public PettyCashService(
        RSSMDbContext context,
        ITransactionService transactionService,
        IJournalService journalService,
        ICOAService coaService,
        IOptionService optionService,
        AuditTrailUtils auditTrailUtils)
    {
        _context = context;
        _transactionService = transactionService;
        _journalService = journalService;
        _coaService = coaService;
        _optionService = optionService;
        _auditTrailUtils = auditTrailUtils;
    }

    #region Dashboard

    public async Task<object> GetDashboard(int branchId, int roleId, int userId)
    {
        int? filterUnitId = null;
        if (roleId == 5)
        {
            var myUnit = await _context.PettyCashUnitUsers
                .Include(uu => uu.Unit)
                .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
                .Select(uu => uu.Unit)
                .FirstOrDefaultAsync();
            if (myUnit == null)
            {
                // No unit assigned for Petty Cash Officer
                 return new
                {
                    TotalBalance = 0,
                    UnitCount = 0,
                    PendingRequests = 0,
                    PendingTopUps = 0,
                    MonthlySettlements = 0,
                    MonthlyTopUps = 0,
                    UnitBalances = new List<object>()
                };
            }
            filterUnitId = myUnit.UnitId;
        }

        var unitsQuery = _context.PettyCashUnits
            .Where(u => u.BranchId == branchId && u.IsActive);
            
        if (filterUnitId.HasValue)
            unitsQuery = unitsQuery.Where(u => u.UnitId == filterUnitId.Value);
            
        var units = await unitsQuery.ToListAsync();

        var totalBalance = 0m;
        foreach (var u in units)
        {
             // Use real-time balance
             var balance = await _journalService.GetEndingBalance(u.CoaCode, DateTime.UtcNow, u.BranchId ?? branchId);
             u.CurrentBalance = (decimal)balance;
             totalBalance += (decimal)balance;
        }
        
        var unitCount = units.Count;

        // Get pending requests
        var requestsQuery = _context.PettyCashRequests
            .Where(r => r.BranchId == branchId && (r.Status == "Submitted" || r.Status == "Draft")); // Added Draft? Or just keep original logic
            
        // Original logic: "Submitted" || "Draft". 
        // Note: For dashboard summary usually we care about actionable items.
        // If Role 5 (Petty Cash), maybe "Submitted" requests are what they need to process (approve/reject)?
        // Or "Draft" requests they made?
        // Let's stick to original logic but filter by Unit.
        
        if (filterUnitId.HasValue)
            requestsQuery = requestsQuery.Where(r => r.UnitId == filterUnitId.Value);
            
        var pendingRequests = await requestsQuery.CountAsync();

        // Get pending topups
        var topUpsQuery = _context.PettyCashTopUps
            .Where(t => t.BranchId == branchId && t.Status == "Pending");
            
        if (filterUnitId.HasValue)
            topUpsQuery = topUpsQuery.Where(t => t.UnitId == filterUnitId.Value);

        var pendingTopUps = await topUpsQuery.CountAsync();

        // Get this month transactions
        var startOfMonth = new DateTime(DateTime.UtcNow.Year, DateTime.UtcNow.Month, 1, 0, 0, 0, DateTimeKind.Utc);
        var endOfMonth = startOfMonth.AddMonths(1).AddTicks(-1);

        var settlementsQuery = _context.PettyCashSettlements
            .Where(s => s.BranchId == branchId && s.Status == "Posted" && s.SettlementDate >= startOfMonth && s.SettlementDate <= endOfMonth);
            
        if (filterUnitId.HasValue)
            settlementsQuery = settlementsQuery.Where(s => s.UnitId == filterUnitId.Value);

        // Get Grouped Settlement Data
        var settlementStats = await settlementsQuery
            .GroupBy(s => s.UnitId)
            .Select(g => new { UnitId = g.Key, Total = g.Sum(s => s.TotalAmount) })
            .ToDictionaryAsync(x => x.UnitId, x => x.Total);
            
        var monthlySettlements = settlementStats.Sum(x => x.Value);

        var monthlyRequestsQuery = _context.PettyCashRequests
            .Where(r => r.BranchId == branchId && r.Status == "Posted" && r.RequestDate >= startOfMonth && r.RequestDate <= endOfMonth);
            
        if (filterUnitId.HasValue)
            monthlyRequestsQuery = monthlyRequestsQuery.Where(r => r.UnitId == filterUnitId.Value);

        // Get Grouped Request Data
        var requestStats = await monthlyRequestsQuery
            .GroupBy(r => r.UnitId)
            .Select(g => new { UnitId = g.Key, Total = g.Sum(r => r.Amount) })
            .ToDictionaryAsync(x => x.UnitId, x => x.Total);

        var monthlyRequests = requestStats.Sum(x => x.Value);

        var totalMonthlyExpenses = monthlySettlements + monthlyRequests;

        var monthlyTopUpsQuery = _context.PettyCashTopUps
            .Where(t => t.BranchId == branchId && t.Status == "Posted" && t.TopUpDate >= startOfMonth && t.TopUpDate <= endOfMonth);
            
        if (filterUnitId.HasValue)
            monthlyTopUpsQuery = monthlyTopUpsQuery.Where(t => t.UnitId == filterUnitId.Value);

        var monthlyTopUps = await monthlyTopUpsQuery.SumAsync(t => t.Amount);

        // Unit balances
        var unitBalances = units.Select(u => {
            var unitSettlement = settlementStats.ContainsKey(u.UnitId) ? settlementStats[u.UnitId] : 0;
            var unitRequest = requestStats.ContainsKey(u.UnitId) ? requestStats[u.UnitId] : 0;
            var unitTotalExpense = unitSettlement + unitRequest;
            
            return new
            {
                u.UnitId,
                u.UnitCode,
                u.UnitName,
                u.CurrentBalance,
                u.MaxLimitPerPeriod,
                UsagePercentage = u.MaxLimitPerPeriod > 0 ? Math.Round((double)(unitTotalExpense / u.MaxLimitPerPeriod) * 100, 2) : 0
            };
        }).ToList();

        return new
        {
            TotalBalance = totalBalance,
            UnitCount = unitCount,
            PendingRequests = pendingRequests,
            PendingTopUps = pendingTopUps,
            MonthlySettlements = totalMonthlyExpenses,
            MonthlyTopUps = monthlyTopUps,
            UnitBalances = unitBalances
        };
    }

    #endregion

    #region Unit Management

    public async Task<object> GetUnits(int branchId, string? keyword, int page, int pageSize)
    {
        var query = _context.PettyCashUnits
            .Where(u => u.BranchId == branchId);

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(u => u.UnitCode.ToLower().Contains(kw) || u.UnitName.ToLower().Contains(kw));
        }

        var total = await query.CountAsync();
        
        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.OrderBy(u => u.UnitCode).ToListAsync();

        // Saldo unit = saldo COA unit (dihitung dari jurnal), SAMA SUMBER dengan dashboard/ledger.
        // Field tersimpan u.CurrentBalance sudah deprecated (selalu 0) — jangan dipakai.
        foreach (var u in data)
        {
            u.CurrentBalance = (decimal)await _journalService.GetEndingBalance(u.CoaCode, DateTime.UtcNow, u.BranchId ?? branchId);
        }

        // Get COA names
        var coaCodes = data.Select(d => d.CoaCode).Distinct().ToList();
        var coaMap = await _context.Coa
            .Where(c => coaCodes.Contains(c.CoaCode))
            .ToDictionaryAsync(c => c.CoaCode, c => c.CoaName);

        // Get assigned users for each unit
        var unitIds = data.Select(d => d.UnitId).ToList();
        var unitUsers = await _context.PettyCashUnitUsers
            .Where(uu => unitIds.Contains(uu.UnitId))
            .ToListAsync();

        // Get user display names
        var userIds = unitUsers.Select(uu => uu.UserId).Distinct().ToList();
        var userMap = await _context.Users
            .Where(u => userIds.Contains(u.UserID))
            .ToDictionaryAsync(u => u.UserID, u => u.DisplayName);

        var result = data.Select(u => new
        {
            u.UnitId,
            u.UnitCode,
            u.UnitName,
            u.CoaCode,
            CoaName = coaMap.ContainsKey(u.CoaCode) ? coaMap[u.CoaCode] : null,
            u.MaxLimitPerTransaction,
            u.MaxLimitPerPeriod,
            u.PeriodType,
            u.CurrentBalance,
            u.IsActive,
            u.CreatedDate,
            AssignedUsers = unitUsers
                .Where(uu => uu.UnitId == u.UnitId)
                .Select(uu => new
                {
                    uu.UserId,
                    DisplayName = userMap.ContainsKey(uu.UserId) ? userMap[uu.UserId] : null
                }).ToList()
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<object> GetUnitOptions(int branchId)
    {
        var units = await _context.PettyCashUnits
            .Where(u => u.BranchId == branchId && u.IsActive)
            .OrderBy(u => u.UnitName)
            .Select(u => new
            {
                value = u.UnitId,
                label = $"{u.UnitCode} - {u.UnitName}",
                coaCode = u.CoaCode,
                balance = u.CurrentBalance,
                limit = u.MaxLimitPerTransaction
            })
            .ToListAsync();

        return units;
    }

    public async Task<PettyCashUnit?> GetUnitById(int id)
    {
        var unit = await _context.PettyCashUnits.FindAsync(id);
        if (unit != null)
        {
            var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == unit.CoaCode);
            unit.CoaName = coa?.CoaName;
            
            // Get real-time balance
            if (unit.BranchId.HasValue)
            {
                unit.CurrentBalance = (decimal)await _journalService.GetEndingBalance(unit.CoaCode, DateTime.UtcNow, unit.BranchId.Value);
            }
        }
        return unit;
    }

    public async Task<object> GetMyUnit(int userId)
    {
        var unit = await _context.PettyCashUnitUsers
            .Include(uu => uu.Unit)
            .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
            .Select(uu => uu.Unit)
            .FirstOrDefaultAsync();

        if (unit == null) return null;

        if (unit.BranchId.HasValue)
        {
            // Fetch real-time balance from Journal
            var balance = await _journalService.GetEndingBalance(unit.CoaCode, DateTime.UtcNow, unit.BranchId.Value);
            unit.CurrentBalance = (decimal)balance;
        }

        var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == unit.CoaCode);
        
        return new
        {
            unit.UnitId,
            unit.UnitCode,
            unit.UnitName,
            unit.CoaCode,
            CoaName = coa?.CoaName,
            unit.MaxLimitPerTransaction,
            unit.MaxLimitPerPeriod,
            unit.PeriodType,
            unit.CurrentBalance,
            unit.IsActive
        };
    }

    private async Task<PettyCashUnit> GetUnitByUserId(int userId)
    {
        var unit = await _context.PettyCashUnitUsers
            .Include(uu => uu.Unit)
            .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
            .Select(uu => uu.Unit)
            .FirstOrDefaultAsync();
            
        if (unit == null) 
            throw new Exception("Anda tidak memiliki akses ke Unit Kas Kecil manapun. Silahkan hubungi Administrator.");
            
        return unit;
    }

    public async Task<int> AddUnit(UnitModel model, int userId)
    {
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Validate Role 5 users: can only be assigned to 1 unit
        if (model.AssignedUserIds != null && model.AssignedUserIds.Any() && model.IsActive)
        {
            await ValidateRole5Assignment(model.AssignedUserIds, null);
        }

        var unit = new PettyCashUnit
        {
            UnitCode = model.UnitCode,
            UnitName = model.UnitName,
            CoaCode = model.CoaCode,
            MaxLimitPerTransaction = model.MaxLimitPerTransaction,
            MaxLimitPerPeriod = model.MaxLimitPerPeriod,
            PeriodType = model.PeriodType,
            CurrentBalance = model.CurrentBalance,
            IsActive = model.IsActive,
            BranchId = model.BranchId,
            CreatedBy = userId,
            CreatedDate = utcNow
        };

        _context.PettyCashUnits.Add(unit);
        await _context.SaveChangesAsync();

        // Add assigned users
        if (model.AssignedUserIds != null && model.AssignedUserIds.Any())
        {
            foreach (var uid in model.AssignedUserIds)
            {
                _context.PettyCashUnitUsers.Add(new PettyCashUnitUser
                {
                    UnitId = unit.UnitId,
                    UserId = uid,
                    CreatedDate = utcNow
                });
            }
            await _context.SaveChangesAsync();
        }

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashUnit, "Dibuat",
            unit.UnitId.ToString(), userId, unit.BranchId,
            new
            {
                unit.UnitCode,
                unit.UnitName,
                unit.CoaCode,
                unit.MaxLimitPerTransaction,
                unit.MaxLimitPerPeriod,
                AssignedUserIds = model.AssignedUserIds
            });

        return unit.UnitId;
    }

    public async Task<bool> UpdateUnit(UnitModel model, int userId)
    {
        var existing = await _context.PettyCashUnits.FindAsync(model.UnitId);
        if (existing == null) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Validate Role 5 users: can only be assigned to 1 unit
        if (model.AssignedUserIds != null && model.AssignedUserIds.Any() && model.IsActive)
        {
            await ValidateRole5Assignment(model.AssignedUserIds, model.UnitId);
        }

        existing.UnitCode = model.UnitCode;
        existing.UnitName = model.UnitName;
        existing.CoaCode = model.CoaCode;
        existing.MaxLimitPerTransaction = model.MaxLimitPerTransaction;
        existing.MaxLimitPerPeriod = model.MaxLimitPerPeriod;
        existing.PeriodType = model.PeriodType;
        existing.IsActive = model.IsActive;
        existing.UpdatedBy = userId;
        existing.UpdatedDate = utcNow;

        // Remove existing assigned users and re-add
        var existingUsers = await _context.PettyCashUnitUsers
            .Where(uu => uu.UnitId == model.UnitId)
            .ToListAsync();
        _context.PettyCashUnitUsers.RemoveRange(existingUsers);

        if (model.AssignedUserIds != null && model.AssignedUserIds.Any())
        {
            foreach (var uid in model.AssignedUserIds)
            {
                _context.PettyCashUnitUsers.Add(new PettyCashUnitUser
                {
                    UnitId = model.UnitId,
                    UserId = uid,
                    CreatedDate = utcNow
                });
            }
        }

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashUnit, "Diubah",
            existing.UnitId.ToString(), userId, existing.BranchId,
            new
            {
                existing.UnitCode,
                existing.UnitName,
                existing.CoaCode,
                existing.MaxLimitPerTransaction,
                existing.MaxLimitPerPeriod,
                existing.IsActive,
                AssignedUserIds = model.AssignedUserIds
            });

        return true;
    }

    /// <summary>
    /// Validates that Role 5 (Petty Cash Officer) users are not assigned to more than one active unit.
    /// Other roles can be assigned to multiple units freely.
    /// </summary>
    private async Task ValidateRole5Assignment(List<int> userIds, int? excludeUnitId)
    {
        // Get Role 5 users from the list
        var role5Users = await _context.Users
            .Where(u => userIds.Contains(u.UserID) && u.RoleID == 5)
            .Select(u => new { u.UserID, u.DisplayName })
            .ToListAsync();

        if (!role5Users.Any()) return;

        // Check if any Role 5 user is already assigned to another active unit
        foreach (var r5User in role5Users)
        {
            var existingAssignment = await _context.PettyCashUnitUsers
                .Include(uu => uu.Unit)
                .Where(uu => uu.UserId == r5User.UserID && uu.Unit.IsActive)
                .FirstOrDefaultAsync();

            if (existingAssignment != null && (excludeUnitId == null || existingAssignment.UnitId != excludeUnitId))
            {
                throw new Exception($"User '{r5User.DisplayName}' (Role Petty Cash Officer) sudah ditugaskan ke unit '{existingAssignment.Unit?.UnitName}'. Petty Cash Officer hanya boleh di-assign ke 1 unit.");
            }
        }
    }

    public async Task<bool> DeleteUnit(int id, int userId)
    {
        var unit = await _context.PettyCashUnits.FindAsync(id);
        if (unit == null) return false;

        // Check if there are related transactions
        var hasRequests = await _context.PettyCashRequests.AnyAsync(r => r.UnitId == id);
        var hasSettlements = await _context.PettyCashSettlements.AnyAsync(s => s.UnitId == id);
        var hasTopUps = await _context.PettyCashTopUps.AnyAsync(t => t.UnitId == id);

        // Snapshot before removal -- on hard delete the log is all that remains.
        var deleted = new { unit.UnitName, unit.CoaCode, unit.BranchId };
        var branchId = unit.BranchId;

        if (hasRequests || hasSettlements || hasTopUps)
        {
            // Soft delete - just set inactive
            unit.IsActive = false;
            await _context.SaveChangesAsync();

            await _auditTrailUtils.LogAction(
                AuditModule.PettyCashUnit, "Dinonaktifkan",
                id.ToString(), userId, branchId, deleted);

            return true;
        }

        _context.PettyCashUnits.Remove(unit);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashUnit, "Dihapus",
            id.ToString(), userId, branchId, deleted);

        return true;
    }

    #endregion

    #region Request

    public async Task<object> GetRequests(int branchId, int? unitId, string? status, string? settlementStatus, DateTime? startDate, DateTime? endDate, string? keyword, int page, int pageSize, int roleId, int userId)
    {
        var query = _context.PettyCashRequests
            .Include(r => r.Unit)
            .Where(r => r.BranchId == branchId);

        // Role Access Control: If Role is Petty Cash Officer (5), restricted to assigned unit
        if (roleId == 5)
        {
            var myUnit = await _context.PettyCashUnitUsers
                .Include(uu => uu.Unit)
                .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
                .Select(uu => uu.Unit)
                .FirstOrDefaultAsync();
            if (myUnit != null)
            {
                // Force filter by my unit
                unitId = myUnit.UnitId;
            }
            else
            {
                // Has no unit assigned, return empty results
                return new { data = new List<object>(), totalData = 0 };
            }
        }

        if (unitId.HasValue)
            query = query.Where(r => r.UnitId == unitId.Value);

        if (!string.IsNullOrEmpty(status))
        {
            if (status.ToLower() == "settlement")
            {
                query = query.Where(r => r.Status.ToLower() == "posted" || r.Status.ToLower() == "settled");
            }
            else
            {
                query = query.Where(r => r.Status.ToLower() == status.ToLower());
            }
        }

        // Settlement status filter: 'settled' or 'not_settled'
        if (!string.IsNullOrEmpty(settlementStatus))
        {
            if (settlementStatus.ToLower() == "settled")
                query = query.Where(r => r.SettlementId != null);
            else if (settlementStatus.ToLower() == "not_settled")
                query = query.Where(r => r.SettlementId == null);
        }

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(r => r.RequestDate >= start);
        }

        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(r => r.RequestDate <= end);
        }

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(r => r.RequestNo.ToLower().Contains(kw) || r.RequesterName.ToLower().Contains(kw) || r.Purpose.ToLower().Contains(kw));
        }

        var total = await query.CountAsync();

        query = query.OrderByDescending(r => r.RequestDate);

        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.ToListAsync();

        // Get Journal Numbers
        var transactionIds = data.Where(r => !string.IsNullOrEmpty(r.TransactionId)).Select(r => r.TransactionId).Distinct().ToList();
        var journals = await _context.Transactions
            .Where(t => transactionIds.Contains(t.TransactionId))
            .ToDictionaryAsync(t => t.TransactionId, t => t.JournalNo);

        var result = data.Select(r => new
        {
            r.RequestId,
            r.RequestNo,
            r.RequestDate,
            r.UnitId,
            UnitCode = r.Unit?.UnitCode,
            UnitName = r.Unit?.UnitName,
            r.RequesterName,
            r.Purpose,
            r.Amount,
            r.ExpenseCoaCode,
            r.CostCenterCode,
            r.CostCenterName,
            r.Status,
            r.ApprovedAmount,
            r.ApprovedDate,
            r.SettlementId,
            r.Notes,
            r.CreatedDate,
            r.TransactionId,
            JournalNo = !string.IsNullOrEmpty(r.TransactionId) && journals.ContainsKey(r.TransactionId) ? journals[r.TransactionId] : null
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<PettyCashRequest?> GetRequestById(int id)
    {
        var request = await _context.PettyCashRequests
            .Include(r => r.Unit)
            .FirstOrDefaultAsync(r => r.RequestId == id);

        if (request != null)
        {
            request.UnitName = request.Unit?.UnitName;
            request.UnitCode = request.Unit?.UnitCode;

            if (!string.IsNullOrEmpty(request.ExpenseCoaCode))
            {
                var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == request.ExpenseCoaCode);
                request.ExpenseCoaName = coa?.CoaName;
            }

            if (!string.IsNullOrEmpty(request.TransactionId))
            {
                var transaction = await _context.Transactions
                    .Where(t => t.TransactionId == request.TransactionId)
                    .Select(t => t.JournalNo)
                    .FirstOrDefaultAsync();
                
                request.JournalNo = transaction;
            }
        }

        return request;
    }

    public async Task<string> GenerateRequestNo(DateTime date)
    {
        var prefix = $"PCR-{date:yyyyMMdd}-";
        var last = await _context.PettyCashRequests
            .Where(r => r.RequestNo.StartsWith(prefix))
            .OrderByDescending(r => r.RequestNo)
            .FirstOrDefaultAsync();

        int seq = 1;
        if (last != null)
        {
            var parts = last.RequestNo.Split('-');
            if (parts.Length >= 3 && int.TryParse(parts[2], out int lastSeq))
            {
                seq = lastSeq + 1;
            }
        }

        return $"{prefix}{seq:D4}";
    }

    public async Task<int> AddRequest(PettyCashRequest request, int userId)
    {
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        int branchId = request.BranchId ?? 1;

        // Auto-assign Unit
        var unit = await GetUnitByUserId(userId);
        request.UnitId = unit.UnitId;

        // Validate Balance
        decimal currentBalance = (decimal)await _journalService.GetEndingBalance(unit.CoaCode, utcNow, branchId);
        if (currentBalance < request.Amount)
        {
            throw new Exception($"Saldo Unit Kas Kecil tidak mencukupi untuk melakukan transaksi ini. Saldo: {currentBalance:N0}, Permintaan: {request.Amount:N0}");
        }

        // Auto-assign Receivable COA (Piutang Karyawan) from option
        string receivableCoaCode = await GetReceivableCOA(branchId);
        var receivableCoa = await _coaService.GetByCode(receivableCoaCode, branchId);
        request.ExpenseCoaCode = receivableCoaCode;
        request.ExpenseCoaName = receivableCoa?.CoaName ?? "Piutang Karyawan";

        // Generate Request No
        request.RequestNo = await GenerateRequestNo(request.RequestDate);
        request.Status = "Posted";
        request.CreatedBy = userId;
        request.CreatedDate = utcNow;
        request.RequestDate = DateTime.SpecifyKind(request.RequestDate, DateTimeKind.Utc);
        
        // Auto-approve details (approval workflow belum aktif → tetap auto-approve)
        request.ApprovedAmount = request.Amount;
        request.ApprovedBy = userId;
        // Tanggal disetujui mengikuti Tanggal Permintaan (bukan waktu submit), agar konsisten
        // dengan requestDate, tanggal transaksi & jurnal, serta tampil benar di receipt.
        request.ApprovedDate = request.RequestDate;
        request.UpdatedBy = userId;
        request.UpdatedDate = utcNow;

        // --- Create Transaction & Journals ---
        
        // Get Unit COA Info
        var unitCoa = await _coaService.GetByCode(unit.CoaCode, branchId);
        if (unitCoa == null) throw new Exception($"COA Unit {unit.CoaCode} tidak ditemukan");

        string transactionType = "PettyCashRequest";
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(request.RequestDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = request.RequestDate,
            TransactionName = $"Pengeluaran Kas Kecil {request.RequestNo}",
            TransactionRef = request.RequestNo,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = request.Purpose ?? $"Pengeluaran Kas Kecil - {unit.UnitName}",
            CoaCode = unit.CoaCode,
            CoaName = unitCoa.CoaName,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double)request.Amount,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName
        };

        await _transactionService.Add(transaction);
        request.TransactionId = transaction.TransactionId;

        // Journal Debit: Piutang Karyawan (Receivable - employee owes money)
        var journalDebit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Piutang Kas Kecil - {request.RequesterName}",
            CoaCode = receivableCoaCode,
            CoaName = receivableCoa?.CoaName ?? "Piutang Karyawan",
            CoaParentCode = receivableCoa?.ParentCode,
            CoaParentName = receivableCoa?.ParentName,
            DebitAmount = (double)request.Amount,
            CreditAmount = 0,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = false
        };
        await _journalService.Add(journalDebit);

        // Journal Credit: Petty Cash Unit (money leaves the unit)
        var journalCredit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Pengeluaran Kas Kecil - {unit.UnitName}",
            CoaCode = unit.CoaCode,
            CoaName = unitCoa.CoaName,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName,
            DebitAmount = 0,
            CreditAmount = (double)request.Amount,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalCredit);

        _context.PettyCashRequests.Add(request);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashRequest, "Dibuat",
            request.RequestId.ToString(), userId, request.BranchId,
            new
            {
                request.RequestNo,
                request.RequesterName,
                request.Amount,
                request.Purpose,
                request.ExpenseCoaCode,
                request.Status
            });

        return request.RequestId;
    }

    public async Task<bool> UpdateRequest(PettyCashRequest request, int userId)
    {
        var existing = await _context.PettyCashRequests.FindAsync(request.RequestId);
        if (existing == null || existing.Status != "Draft") return false;

        existing.RequesterName = request.RequesterName;
        existing.Purpose = request.Purpose;
        existing.Amount = request.Amount;
        existing.ExpenseCoaCode = request.ExpenseCoaCode;
        existing.CostCenterCode = request.CostCenterCode;
        existing.CostCenterName = request.CostCenterName;
        existing.Notes = request.Notes;
        existing.RequestDate = DateTime.SpecifyKind(request.RequestDate, DateTimeKind.Utc);
        existing.UpdatedBy = userId;
        existing.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashRequest, "Diubah",
            existing.RequestId.ToString(), userId, existing.BranchId,
            new
            {
                existing.RequestNo,
                existing.RequesterName,
                existing.Amount,
                existing.Purpose
            });

        return true;
    }

    public async Task<bool> DeleteRequest(int id, int userId)
    {
        var request = await _context.PettyCashRequests.FindAsync(id);
        if (request == null || request.Status != "Draft") return false;

        // Snapshot before the row is gone -- the log is all that remains of it.
        var deleted = new
        {
            request.RequestNo,
            request.RequesterName,
            request.Amount,
            request.Purpose
        };
        var branchId = request.BranchId;

        _context.PettyCashRequests.Remove(request);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashRequest, "Dihapus",
            id.ToString(), userId, branchId, deleted);

        return true;
    }

    public async Task<bool> SubmitRequest(int id, int userId)
    {
        var request = await _context.PettyCashRequests.FindAsync(id);
        if (request == null || request.Status != "Draft") return false;

        // Validate limit
        var unit = await _context.PettyCashUnits.FindAsync(request.UnitId);
        if (unit != null && unit.MaxLimitPerTransaction.HasValue && request.Amount > unit.MaxLimitPerTransaction.Value)
        {
            throw new Exception($"Amount exceeds maximum limit per transaction ({unit.MaxLimitPerTransaction.Value:N0})");
        }

        request.Status = "Submitted";
        request.UpdatedBy = userId;
        request.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashRequest, "Diajukan",
            id.ToString(), userId, request.BranchId,
            new { request.RequestNo, request.Amount, request.Purpose });

        return true;
    }

    public async Task<bool> ApproveRequest(int id, decimal approvedAmount, int userId)
    {
        var request = await _context.PettyCashRequests.FindAsync(id);
        if (request == null || request.Status != "Submitted") return false;

        var requestedAmount = request.Amount;

        request.Status = "Approved";
        request.ApprovedBy = userId;
        request.ApprovedAmount = approvedAmount;
        request.ApprovedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        request.UpdatedBy = userId;
        request.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashRequest, "Disetujui",
            id.ToString(), userId, request.BranchId,
            new
            {
                request.RequestNo,
                RequestedAmount = requestedAmount,
                ApprovedAmount = approvedAmount,
                request.Purpose
            });

        return true;
    }

    public async Task<bool> RejectRequest(int id, string reason, int userId)
    {
        var request = await _context.PettyCashRequests.FindAsync(id);
        if (request == null || request.Status != "Submitted") return false;

        request.Status = "Rejected";
        request.RejectedReason = reason;
        request.UpdatedBy = userId;
        request.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        // The entity has no RejectedBy column -- this log is the only record of who rejected it.
        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashRequest, "Ditolak",
            id.ToString(), userId, request.BranchId,
            new { request.RequestNo, request.Amount, Reason = reason });

        return true;
    }

    public async Task<object?> GetRequestReceipt(int id)
    {
        var request = await _context.PettyCashRequests
            .Include(r => r.Unit)
            .FirstOrDefaultAsync(r => r.RequestId == id);

        if (request == null) return null;

        // Expense COA
        string? expenseCoaName = null;
        if (!string.IsNullOrEmpty(request.ExpenseCoaCode))
        {
            var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == request.ExpenseCoaCode);
            expenseCoaName = coa?.CoaName;
        }

        // Unit COA (debit side, main account)
        string? unitCoaCode = request.Unit?.CoaCode;
        string? unitCoaName = null;
        if (!string.IsNullOrEmpty(unitCoaCode))
        {
            var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == unitCoaCode);
            unitCoaName = coa?.CoaName;
        }

        string? createdByName = null;
        if (request.CreatedBy.HasValue)
        {
            var user = await _context.Users.FindAsync(request.CreatedBy.Value);
            createdByName = user?.DisplayName;
        }

        string? approvedByName = null;
        if (request.ApprovedBy.HasValue)
        {
            var user = await _context.Users.FindAsync(request.ApprovedBy.Value);
            approvedByName = user?.DisplayName;
        }

        // Receiver = user assigned to the unit (Petty Cash Officer)
        string? receivedByName = null;
        if (request.Unit != null)
        {
            var unitUser = await _context.PettyCashUnitUsers
                .Where(uu => uu.UnitId == request.UnitId)
                .OrderBy(uu => uu.Id)
                .FirstOrDefaultAsync();
            if (unitUser != null)
            {
                var user = await _context.Users.FindAsync(unitUser.UserId);
                receivedByName = user?.DisplayName;
            }
        }

        // Use approved amount if available, else requested
        var amount = request.ApprovedAmount ?? request.Amount;

        return new
        {
            request.RequestId,
            VoucherNo = request.RequestNo,
            RequestDate = request.RequestDate,
            ApprovedDate = request.ApprovedDate,
            UnitName = request.Unit?.UnitName,
            UnitCode = request.Unit?.UnitCode,
            UnitCoaCode = unitCoaCode,
            UnitCoaName = unitCoaName,
            ExpenseCoaCode = request.ExpenseCoaCode,
            ExpenseCoaName = expenseCoaName,
            RequesterName = request.RequesterName,
            Purpose = request.Purpose,
            Amount = amount,
            RequestedAmount = request.Amount,
            ApprovedAmount = request.ApprovedAmount,
            request.CostCenterCode,
            request.CostCenterName,
            request.Notes,
            request.Status,
            CreatedByName = createdByName,
            ApprovedByName = approvedByName,
            ReceivedByName = receivedByName,
        };
    }

    #endregion

    #region Settlement

    public async Task<object> GetSettlements(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId)
    {
        var query = _context.PettyCashSettlements
            .Include(s => s.Unit)
            .Where(s => s.BranchId == branchId);

        // Role Access Control: If Role is Petty Cash Officer (5), restricted to assigned unit
        if (roleId == 5)
        {
            var myUnit = await _context.PettyCashUnitUsers
                .Include(uu => uu.Unit)
                .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
                .Select(uu => uu.Unit)
                .FirstOrDefaultAsync();
            if (myUnit != null)
            {
                // Force filter by my unit
                unitId = myUnit.UnitId;
            }
            else
            {
                // Has no unit assigned, return empty results
                return new { data = new List<object>(), totalData = 0 };
            }
        }

        if (unitId.HasValue)
            query = query.Where(s => s.UnitId == unitId.Value);

        if (!string.IsNullOrEmpty(status))
            query = query.Where(s => s.Status.ToLower() == status.ToLower());

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(s => s.SettlementDate >= start);
        }

        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(s => s.SettlementDate <= end);
        }

        var total = await query.CountAsync();

        query = query.OrderByDescending(s => s.SettlementDate);

        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.ToListAsync();

        // Get transaction/journal info
        var transactionIds = data.Where(d => !string.IsNullOrEmpty(d.TransactionId)).Select(d => d.TransactionId).ToList();
        var transactions = await _context.Transactions
            .Where(t => transactionIds.Contains(t.TransactionId))
            .ToDictionaryAsync(t => t.TransactionId, t => t.JournalNo);

        var result = data.Select(s => new
        {
            s.SettlementId,
            s.SettlementNo,
            s.SettlementDate,
            s.UnitId,
            UnitCode = s.Unit?.UnitCode,
            UnitName = s.Unit?.UnitName,
            s.TotalAmount,
            s.Status,
            s.TransactionId,
            JournalNo = !string.IsNullOrEmpty(s.TransactionId) && transactions.ContainsKey(s.TransactionId) ? transactions[s.TransactionId] : null,
            s.Notes,
            s.CreatedDate
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<object?> GetSettlementById(int id)
    {
        var settlement = await _context.PettyCashSettlements
            .Include(s => s.Unit)
            .Include(s => s.Request)
            .Include(s => s.Details)
            .FirstOrDefaultAsync(s => s.SettlementId == id);

        if (settlement == null) return null;

        string? journalNo = null;
        if (!string.IsNullOrEmpty(settlement.TransactionId))
        {
            var trx = await _context.Transactions.FirstOrDefaultAsync(t => t.TransactionId == settlement.TransactionId);
            journalNo = trx?.JournalNo;
        }

        string? cancelJournalNo = null;
        if (!string.IsNullOrEmpty(settlement.CancelTransactionId))
        {
            var cancelTrx = await _context.Transactions.FirstOrDefaultAsync(t => t.TransactionId == settlement.CancelTransactionId);
            cancelJournalNo = cancelTrx?.JournalNo;
        }

        return new
        {
            settlement.SettlementId,
            settlement.SettlementNo,
            settlement.SettlementDate,
            settlement.UnitId,
            UnitCode = settlement.Unit?.UnitCode,
            UnitName = settlement.Unit?.UnitName,
            settlement.TotalAmount,
            settlement.ReturnAmount,
            settlement.Status,
            settlement.TransactionId,
            settlement.Notes,
            settlement.RequestId,
            JournalNo = journalNo,
            settlement.CancelReason,
            settlement.CancelledDate,
            settlement.CancelledBy,
            CancelJournalNo = cancelJournalNo,
            Request = settlement.Request != null ? new {
                settlement.Request.RequestNo,
                settlement.Request.RequesterName,
                settlement.Request.Purpose,
                settlement.Request.Amount,
                settlement.Request.ApprovedAmount
            } : null,
            Details = settlement.Details?.Select(d => new {
                d.DetailId,
                d.ExpenseCoaCode,
                d.ExpenseCoaName,
                d.Description,
                d.Amount,
                d.ReceiptNo,
                d.ReceiptDate
            }).OrderBy(d => d.DetailId).ToList()
        };
    }

    /// <summary>
    /// Semua percobaan settlement untuk satu request, termasuk yang sudah dibatalkan,
    /// urut dari yang paling lama. Inilah jejak siklus settle -> batal -> settle lagi.
    /// </summary>
    public async Task<object> GetSettlementHistoryByRequest(int requestId)
    {
        var settlements = await _context.PettyCashSettlements
            .Where(s => s.RequestId == requestId)
            .OrderBy(s => s.CreatedDate).ThenBy(s => s.SettlementId)
            .ToListAsync();

        if (settlements.Count == 0)
            return new { data = new List<object>(), totalData = 0 };

        // Nomor jurnal asli & jurnal pembalik diambil sekaligus supaya tidak N+1.
        var trxIds = settlements
            .SelectMany(s => new[] { s.TransactionId, s.CancelTransactionId })
            .Where(x => !string.IsNullOrEmpty(x))
            .Distinct()
            .ToList()!;

        var journalNoByTrx = trxIds.Count > 0
            ? await _context.Transactions.Where(t => trxIds.Contains(t.TransactionId))
                .ToDictionaryAsync(t => t.TransactionId, t => t.JournalNo)
            : new Dictionary<string, string?>();

        var userIds = settlements
            .SelectMany(s => new[] { s.CreatedBy, s.CancelledBy })
            .Where(x => x.HasValue).Select(x => x!.Value).Distinct().ToList();
        var userById = userIds.Count > 0
            ? await _context.Users.Where(u => userIds.Contains(u.UserID))
                .ToDictionaryAsync(u => u.UserID, u => u.DisplayName)
            : new Dictionary<int, string>();

        string? JournalOf(string? trxId) =>
            !string.IsNullOrEmpty(trxId) && journalNoByTrx.TryGetValue(trxId, out var no) ? no : null;
        string? NameOf(int? uid) =>
            uid.HasValue && userById.TryGetValue(uid.Value, out var n) ? n : null;

        var data = settlements.Select((s, i) => new
        {
            Urutan = i + 1,
            s.SettlementId,
            s.SettlementNo,
            s.SettlementDate,
            s.TotalAmount,
            s.ReturnAmount,
            s.Status,
            JournalNo = JournalOf(s.TransactionId),
            CancelJournalNo = JournalOf(s.CancelTransactionId),
            s.CancelReason,
            s.CancelledDate,
            CancelledByName = NameOf(s.CancelledBy),
            s.CreatedDate,
            CreatedByName = NameOf(s.CreatedBy)
        }).ToList();

        return new
        {
            data,
            totalData = data.Count,
            aktif = data.Count(d => d.Status != "Cancelled" && d.Status != "Void"),
            dibatalkan = data.Count(d => d.Status == "Cancelled")
        };
    }

    public async Task<string> GenerateSettlementNo(DateTime date)
    {
        var prefix = $"PCS-{date:yyyyMMdd}-";
        var last = await _context.PettyCashSettlements
            .Where(s => s.SettlementNo.StartsWith(prefix))
            .OrderByDescending(s => s.SettlementNo)
            .FirstOrDefaultAsync();

        int seq = 1;
        if (last != null)
        {
            var parts = last.SettlementNo.Split('-');
            if (parts.Length >= 3 && int.TryParse(parts[2], out int lastSeq))
            {
                seq = lastSeq + 1;
            }
        }

        return $"{prefix}{seq:D4}";
    }

    // Validasi COA beban settlement: TIDAK boleh COA kas/bank (is_cash_bank) atau COA kas kecil unit itu sendiri.
    // Kalau tidak dijaga, operator bisa memilih COA petty cash sebagai "akun beban" → jurnal jadi
    // Dr Petty Cash / Cr Piutang (bukan Dr Beban), sehingga saldo kas kecil tidak turun & beban tak tercatat.
    private async Task ValidateSettlementExpenseCoas(IEnumerable<PettyCashSettlementDetail> details, string unitCoaCode, int branchId)
    {
        var codes = details
            .Where(d => !string.IsNullOrWhiteSpace(d.ExpenseCoaCode))
            .Select(d => d.ExpenseCoaCode)
            .Distinct()
            .ToList();
        if (codes.Count == 0) return;

        var cashBankCodes = await _context.Coa
            .Where(c => codes.Contains(c.CoaCode) && c.IsCashBank == true)
            .Select(c => c.CoaCode)
            .ToListAsync();

        foreach (var code in codes)
        {
            if (code == unitCoaCode)
                throw new Exception($"Akun beban tidak boleh sama dengan COA kas kecil unit ({unitCoaCode}). Pilih akun beban yang sesuai.");
            if (cashBankCodes.Contains(code))
                throw new Exception($"Akun '{code}' adalah akun kas/bank dan tidak boleh dipakai sebagai akun beban settlement. Pilih akun beban yang sesuai.");
        }
    }

    public async Task<object> CreateSettlement(PettyCashSettlement settlement, List<PettyCashSettlementDetail> details, int branchId, int userId)
    {
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Abaikan baris tanpa nilai (amount <= 0) atau tanpa akun biaya.
        // Ini mencegah jurnal Debit 0 yang sia-sia dan NullReferenceException di GetByCode
        // saat dana tidak jadi dipakai (pengembalian penuh, details kosong).
        details = (details ?? new List<PettyCashSettlementDetail>())
            .Where(d => d.Amount > 0 && !string.IsNullOrWhiteSpace(d.ExpenseCoaCode))
            .ToList();

        settlement.SettlementNo = await GenerateSettlementNo(settlement.SettlementDate);
        settlement.SettlementDate = DateTime.SpecifyKind(settlement.SettlementDate, DateTimeKind.Utc);
        settlement.TotalAmount = details.Sum(d => d.Amount);
        settlement.Status = "Posted";
        settlement.BranchId = branchId;
        
        // Auto-assign unit if not provided (though settlement creation usually implies known unit context)
        // If settlement.UnitId is 0, fetch from user
        if (settlement.UnitId == 0)
        {
            var unit = await GetUnitByUserId(userId);
            settlement.UnitId = unit.UnitId;
        }
        
        // Satu request hanya boleh punya SATU settlement aktif. Settlement yang sudah
        // dibatalkan tidak menghalangi - justru itu tujuan pembatalan: bisa settle ulang.
        // Tanpa penjagaan ini, dua tab atau dobel-klik bisa membuat dua settlement untuk
        // request yang sama sehingga piutang dikredit dua kali.
        if (settlement.RequestId.HasValue)
        {
            var activeExists = await _context.PettyCashSettlements.AnyAsync(x =>
                x.RequestId == settlement.RequestId.Value &&
                x.SettlementId != settlement.SettlementId &&
                x.Status != "Cancelled" && x.Status != "Void");
            if (activeExists)
                throw new Exception("Request ini sudah punya settlement aktif. Batalkan settlement sebelumnya kalau ingin membuat yang baru.");
        }

        // Get unit info
        var settlementUnit = await _context.PettyCashUnits.FindAsync(settlement.UnitId);
        if (settlementUnit == null) throw new Exception("Unit not found");
        
        // Get unit COA info
        var unitCoa = await _coaService.GetByCode(settlementUnit.CoaCode, branchId);
        if (unitCoa == null) throw new Exception($"COA {settlementUnit.CoaCode} not found");

        // Cegah salah pilih: akun beban tidak boleh COA kas/bank atau COA kas kecil unit.
        await ValidateSettlementExpenseCoas(details, settlementUnit.CoaCode, branchId);

        settlement.CreatedBy = userId;
        settlement.CreatedDate = utcNow;

        _context.PettyCashSettlements.Add(settlement);
        await _context.SaveChangesAsync();

        // Add details
        foreach (var detail in details)
        {
            detail.SettlementId = settlement.SettlementId;
            if (detail.ReceiptDate.HasValue)
                detail.ReceiptDate = DateTime.SpecifyKind(detail.ReceiptDate.Value, DateTimeKind.Utc);
            _context.PettyCashSettlementDetails.Add(detail);
        }

        await _context.SaveChangesAsync();

        // === CREATE JOURNAL ENTRIES ===
        // Logic:
        // 1. CLEAR Receivable (Credit) - The employee is settling the debt
        // 2. RECORD Expenses (Debit) - The actual money spent
        // 3. RETURN Cash (Debit) - Money returned to Petty Cash box

        string transactionType = "PettyCashSettlement";
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(settlement.SettlementDate);

        // Get Receivable COA (104003)
        string receivableCoaCode = await GetReceivableCOA(branchId);
        var receivableCoa = await _coaService.GetByCode(receivableCoaCode, branchId);

        decimal totalExpenses = details.Sum(d => d.Amount);

        // Look up the associated request to determine approved amount (basis pengembalian & kurang bayar)
        decimal approvedFromRequest = 0;
        if (settlement.RequestId.HasValue)
        {
            var relatedRequest = await _context.PettyCashRequests.FindAsync(settlement.RequestId.Value);
            approvedFromRequest = relatedRequest?.ApprovedAmount ?? relatedRequest?.Amount ?? 0;
        }

        // Pengembalian (lebih bayar) dihitung OTOMATIS di server: sisa dana = approved - totalExpenses (bila positif).
        // Backend jadi sumber kebenaran agar jurnal pengembalian selalu terbentuk, tidak bergantung input frontend.
        // Untuk settlement tanpa request, pakai nilai yang dikirim (tidak ada konsep dana disetujui).
        decimal returnAmount = approvedFromRequest > 0
            ? (totalExpenses < approvedFromRequest ? approvedFromRequest - totalExpenses : 0)
            : settlement.ReturnAmount;

        // Simpan kembali nilai terhitung agar konsisten di ledger & detail settlement
        settlement.ReturnAmount = returnAmount;

        decimal totalSettled = totalExpenses + returnAmount;

        // Shortfall (kurang bayar): actual expense exceeded approved amount → petty cash reimburses employee
        decimal shortfall = approvedFromRequest > 0 && totalExpenses > approvedFromRequest
            ? totalExpenses - approvedFromRequest
            : 0;

        // When shortfall exists, the piutang is cleared only by the approved amount
        // and the difference is credited from petty cash (extra outflow to reimburse employee).
        decimal receivableCredit = shortfall > 0 ? approvedFromRequest + returnAmount : totalSettled;

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = settlement.SettlementDate,
            TransactionName = $"Settlement Petty Cash {settlement.SettlementNo}",
            TransactionRef = settlement.SettlementNo,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Settlement Petty Cash - {settlementUnit.UnitName}",
            CoaCode = settlementUnit.CoaCode,
            CoaName = unitCoa.CoaName,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double)totalSettled,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName
        };

        await _transactionService.Add(transaction);

        // 1. DEBIT: Expense COAs (from details)
        foreach (var detail in details)
        {
            var expenseCoa = await _coaService.GetByCode(detail.ExpenseCoaCode, branchId);
            
            var journalDebit = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = detail.Description ?? $"Petty Cash - {detail.ExpenseCoaName}",
                CoaCode = detail.ExpenseCoaCode,
                CoaName = detail.ExpenseCoaName ?? expenseCoa?.CoaName ?? detail.ExpenseCoaCode,
                CoaParentCode = expenseCoa?.ParentCode,
                CoaParentName = expenseCoa?.ParentName,
                DebitAmount = (double)detail.Amount,
                CreditAmount = 0,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            };
            await _journalService.Add(journalDebit);
        }

        // 2. DEBIT: Petty Cash Unit COA (Return Amount) - money coming back
        if (returnAmount > 0)
        {
            var journalReturn = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Sisa Petty Cash - {settlementUnit.UnitName}",
                CoaCode = settlementUnit.CoaCode,
                CoaName = unitCoa.CoaName,
                CoaParentCode = unitCoa.ParentCode,
                CoaParentName = unitCoa.ParentName,
                DebitAmount = (double)returnAmount,
                CreditAmount = 0,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            };
            await _journalService.Add(journalReturn);
        }

        // 3. CREDIT: Receivable COA (Piutang Karyawan) - clearing the debt
        var journalCredit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Settlement Piutang - {settlementUnit.UnitName}",
            CoaCode = receivableCoaCode,
            CoaName = receivableCoa?.CoaName ?? "Piutang Karyawan",
            CoaParentCode = receivableCoa?.ParentCode,
            CoaParentName = receivableCoa?.ParentName,
            DebitAmount = 0,
            CreditAmount = (double)receivableCredit,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalCredit);

        // 4. CREDIT: Petty Cash Unit COA (Shortfall) - reimbursement to employee for over-budget expense
        if (shortfall > 0)
        {
            var journalShortfall = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Kurang Bayar Settlement {settlement.SettlementNo} - {settlementUnit.UnitName}",
                CoaCode = settlementUnit.CoaCode,
                CoaName = unitCoa.CoaName,
                CoaParentCode = unitCoa.ParentCode,
                CoaParentName = unitCoa.ParentName,
                DebitAmount = 0,
                CreditAmount = (double)shortfall,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            };
            await _journalService.Add(journalShortfall);
        }

        // Update settlement with transaction ID
        settlement.TransactionId = transaction.TransactionId;
        settlement.UpdatedBy = userId;
        settlement.UpdatedDate = utcNow;

        // Update related request (now 1:1 via header RequestId)
        if (settlement.RequestId.HasValue)
        {
            var request = await _context.PettyCashRequests.FindAsync(settlement.RequestId);
            if (request != null)
            {
                request.Status = "Settled";
                request.SettlementId = settlement.SettlementId;
                request.UpdatedBy = userId;
                request.UpdatedDate = utcNow;
            }
        }

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashSettlement, "Dibuat",
            settlement.SettlementId.ToString(), userId, branchId,
            new
            {
                settlement.SettlementNo,
                settlement.RequestId,
                settlement.TotalAmount,
                DetailCount = details?.Count ?? 0,
                JournalNo = journalNo,
                settlement.TransactionId
            });

        return new { settlementId = settlement.SettlementId, settlementNo = settlement.SettlementNo, journalNo };
    }

    public async Task<bool> PostSettlement(int id, int branchId, int userId)
    {
        var settlement = await _context.PettyCashSettlements
            .Include(s => s.Unit)
            .Include(s => s.Details)
            .FirstOrDefaultAsync(s => s.SettlementId == id);

        if (settlement == null || settlement.Status != "Pending") return false;
        if (settlement.Unit == null) throw new Exception("Unit not found");

        // Cegah salah pilih: akun beban tidak boleh COA kas/bank atau COA kas kecil unit.
        await ValidateSettlementExpenseCoas(settlement.Details ?? new List<PettyCashSettlementDetail>(), settlement.Unit.CoaCode, branchId);

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var settlementDate = DateTime.SpecifyKind(settlement.SettlementDate, DateTimeKind.Utc);

        // Validate unit balance
        if (settlement.Unit.CurrentBalance < settlement.TotalAmount)
        {
            throw new Exception($"Insufficient petty cash balance. Current: {settlement.Unit.CurrentBalance:N0}, Required: {settlement.TotalAmount:N0}");
        }

        // Get unit COA info
        var unitCoa = await _coaService.GetByCode(settlement.Unit.CoaCode, branchId);
        if (unitCoa == null) throw new Exception($"COA {settlement.Unit.CoaCode} not found");

        // Create Transaction
        string transactionType = "PettyCashSettlement";
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(settlementDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = settlementDate,
            TransactionName = $"Settlement Petty Cash {settlement.SettlementNo}",
            TransactionRef = settlement.SettlementNo,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Settlement Petty Cash - {settlement.Unit.UnitName}",
            CoaCode = settlement.Unit.CoaCode,
            CoaName = unitCoa.CoaName,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double)settlement.TotalAmount,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName
        };

        await _transactionService.Add(transaction);

        // Create Journal Entries
        // DEBIT: Expense COAs (from details)
        if (settlement.Details != null)
        {
            foreach (var detail in settlement.Details)
            {
                var expenseCoa = await _coaService.GetByCode(detail.ExpenseCoaCode, branchId);
                
                var journalDebit = new Journal
                {
                    TransactionId = transaction.TransactionId,
                    JournalRef = transaction.TransactionRef,
                    JournalDesc = detail.Description ?? $"Petty Cash - {detail.ExpenseCoaName}",
                    CoaCode = detail.ExpenseCoaCode,
                    CoaName = detail.ExpenseCoaName ?? expenseCoa?.CoaName ?? detail.ExpenseCoaCode,
                    CoaParentCode = expenseCoa?.ParentCode,
                    CoaParentName = expenseCoa?.ParentName,
                    DebitAmount = (double)detail.Amount,
                    CreditAmount = 0,
                    Source = transactionType,
                    status = transaction.status,
                    BranchId = branchId,
                    CreatedBy = userId,
                    CreatedDate = utcNow,
                    IsPremier = false
                };
                await _journalService.Add(journalDebit);
            }
        }

        // CREDIT: Petty Cash COA
        var journalCredit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Settlement Petty Cash - {settlement.Unit.UnitName}",
            CoaCode = settlement.Unit.CoaCode,
            CoaName = unitCoa.CoaName,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName,
            DebitAmount = 0,
            CreditAmount = (double)settlement.TotalAmount,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalCredit);

        // Update settlement
        settlement.Status = "Posted";
        settlement.TransactionId = transaction.TransactionId;
        settlement.UpdatedBy = userId;
        settlement.UpdatedDate = utcNow;

        // Update unit balance - DEPRECATED: Balance is now calculated from Journal
        // settlement.Unit.CurrentBalance -= settlement.TotalAmount;
        // settlement.Unit.UpdatedBy = userId;
        // settlement.Unit.UpdatedDate = utcNow;

        // Update related request (now 1:1 via header RequestId)
        if (settlement.RequestId.HasValue)
        {
            var request = await _context.PettyCashRequests.FindAsync(settlement.RequestId);
            if (request != null)
            {
                request.Status = "Settled";
                request.SettlementId = settlement.SettlementId;
                request.UpdatedBy = userId;
                request.UpdatedDate = utcNow;
            }
        }

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashSettlement, "Posting",
            id.ToString(), userId, branchId,
            new
            {
                settlement.SettlementNo,
                settlement.TotalAmount,
                settlement.RequestId,
                settlement.TransactionId
            });

        return true;
    }

    public async Task<bool> VoidSettlement(int id, int userId)
    {
        var settlement = await _context.PettyCashSettlements
            .Include(s => s.Unit)
            .FirstOrDefaultAsync(s => s.SettlementId == id);

        if (settlement == null || settlement.Status != "Pending") return false;

        settlement.Status = "Void";
        settlement.UpdatedBy = userId;
        settlement.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashSettlement, "Void",
            id.ToString(), userId, settlement.BranchId,
            new { settlement.SettlementNo, settlement.TotalAmount });

        return true;
    }

    // Role yang boleh membatalkan settlement: Administrator(1), Manager(2), Supervisor(3).
    // Allow-list, bukan deny-list, supaya role baru default-nya TIDAK boleh membatalkan.
    private static readonly int[] CancelSettlementRoles = { 1, 2, 3 };

    public async Task<object> CancelSettlement(int id, string? reason, int roleId, int branchId, int userId)
    {
        // 1) Otorisasi. Dicek di sini, bukan hanya lewat [Authorize], karena
        //    AuthorizeAttribute.OnAuthorization adalah async void sehingga hasil await-nya
        //    tidak dijamin selesai sebelum action berjalan. Pemeriksaan di dalam service
        //    ini deterministik.
        if (!CancelSettlementRoles.Contains(roleId))
            throw new Exception("Hanya Administrator, Manager, atau Supervisor yang boleh membatalkan settlement.");

        var settlement = await _context.PettyCashSettlements
            .Include(s => s.Unit)
            .Include(s => s.Details)
            .FirstOrDefaultAsync(s => s.SettlementId == id);

        if (settlement == null)
            throw new Exception("Settlement tidak ditemukan.");
        if (string.Equals(settlement.Status, "Cancelled", StringComparison.OrdinalIgnoreCase))
            throw new Exception($"Settlement {settlement.SettlementNo} sudah dibatalkan sebelumnya.");
        if (!string.Equals(settlement.Status, "Posted", StringComparison.OrdinalIgnoreCase))
            throw new Exception($"Hanya settlement berstatus Posted yang bisa dibatalkan (status saat ini: {settlement.Status}).");
        if (settlement.Unit == null)
            throw new Exception("Unit kas kecil tidak ditemukan.");
        if (string.IsNullOrEmpty(settlement.TransactionId))
            throw new Exception($"Settlement {settlement.SettlementNo} tidak punya transaksi jurnal, tidak bisa dibalik otomatis.");

        var settlementDate = DateTime.SpecifyKind(settlement.SettlementDate, DateTimeKind.Utc);
        var effectiveBranch = settlement.BranchId ?? branchId;

        // 2) Jurnal pembalik ditanggalkan SAMA dengan settlement asli supaya kedua jurnal
        //    saling meniadakan di bulan yang sama - beban dan saldo kas kecil kembali bersih.
        //    Konsekuensinya periode itu tidak boleh sudah ditutup. Query langsung ke tabel
        //    (bukan lewat IPeriodClosingService) mengikuti pola di JournalService untuk
        //    menghindari circular DI.
        bool periodClosed = await _context.PeriodClosings.AnyAsync(p => p.Status == "Closed"
            && p.Year == settlementDate.Year
            && (p.Month == null || p.Month == settlementDate.Month)
            && (p.BranchId == null || p.BranchId == effectiveBranch));
        if (periodClosed)
            throw new Exception($"Periode {settlementDate:MM/yyyy} sudah ditutup. Settlement ini tidak bisa dibatalkan.");

        // 3) Ambil jurnal asli. Inilah yang dibalik - bukan menghitung ulang dari detail,
        //    supaya pembalikan selalu persis meniadakan apa yang benar-benar tercatat,
        //    termasuk baris lebih bayar / kurang bayar.
        var deleted = Status.Deleted.ToString();
        var originalJournals = await _context.Journals
            .Where(j => j.TransactionId == settlement.TransactionId && j.status != deleted)
            .ToListAsync();

        if (originalJournals.Count == 0)
            throw new Exception($"Jurnal asli settlement {settlement.SettlementNo} tidak ditemukan, pembatalan dihentikan agar tidak merusak saldo.");

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        string transactionType = "PettyCashSettlementCancel";
        string transactionNo = $"CNL-{settlement.SettlementNo}";
        string journalNo = await _journalService.GenerateJournalNo(settlementDate);

        var unitCoa = await _coaService.GetByCode(settlement.Unit.CoaCode, effectiveBranch);

        var cancelTransaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = settlementDate,
            TransactionName = $"Pembatalan Settlement {settlement.SettlementNo}",
            TransactionRef = settlement.SettlementNo,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Pembatalan Settlement Petty Cash {settlement.SettlementNo} - {settlement.Unit.UnitName}"
                          + (string.IsNullOrWhiteSpace(reason) ? "" : $" | Alasan: {reason.Trim()}"),
            CoaCode = settlement.Unit.CoaCode,
            CoaName = unitCoa?.CoaName,
            status = Status.Published.ToString(),
            BranchId = effectiveBranch,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double)settlement.TotalAmount,
            CoaParentCode = unitCoa?.ParentCode,
            CoaParentName = unitCoa?.ParentName
        };

        await _transactionService.Add(cancelTransaction);

        // 4) Balik setiap baris: debit jadi kredit, kredit jadi debit.
        foreach (var orig in originalJournals)
        {
            var reversal = new Journal
            {
                TransactionId = cancelTransaction.TransactionId,
                JournalRef = settlement.SettlementNo,
                JournalDesc = $"Pembatalan: {orig.JournalDesc}",
                CoaCode = orig.CoaCode,
                CoaName = orig.CoaName,
                CoaParentCode = orig.CoaParentCode,
                CoaParentName = orig.CoaParentName,
                DebitAmount = orig.CreditAmount ?? 0,
                CreditAmount = orig.DebitAmount ?? 0,
                Source = transactionType,
                status = Status.Published.ToString(),
                BranchId = effectiveBranch,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = orig.IsPremier,
                CostcenterId = orig.CostcenterId
            };
            await _journalService.Add(reversal);
        }

        // 5) Tandai settlement batal. Datanya sengaja dipertahankan sebagai jejak audit.
        settlement.Status = "Cancelled";
        settlement.CancelledBy = userId;
        settlement.CancelledDate = utcNow;
        settlement.CancelReason = string.IsNullOrWhiteSpace(reason) ? null : reason.Trim();
        settlement.CancelTransactionId = cancelTransaction.TransactionId;
        settlement.UpdatedBy = userId;
        settlement.UpdatedDate = utcNow;

        // 6) Lepaskan request supaya bisa di-settle ulang.
        int? releasedRequestId = null;
        if (settlement.RequestId.HasValue)
        {
            var request = await _context.PettyCashRequests.FindAsync(settlement.RequestId.Value);
            if (request != null)
            {
                request.Status = "Posted";
                request.SettlementId = null;
                request.UpdatedBy = userId;
                request.UpdatedDate = utcNow;
                releasedRequestId = request.RequestId;
            }
        }

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashSettlement, "Dibatalkan",
            settlement.SettlementId.ToString(), userId, effectiveBranch,
            new
            {
                settlement.SettlementNo,
                settlement.TotalAmount,
                Alasan = settlement.CancelReason,
                JurnalPembalik = journalNo,
                ReversedLines = originalJournals.Count,
                RequestDilepas = releasedRequestId
            });

        return new
        {
            settlementId = settlement.SettlementId,
            settlementNo = settlement.SettlementNo,
            cancelJournalNo = journalNo,
            reversedLines = originalJournals.Count,
            requestId = releasedRequestId
        };
    }

    #endregion

    #region TopUp

    public async Task<object> GetTopUps(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId)
    {
        var query = _context.PettyCashTopUps
            .Include(t => t.Unit)
            .Where(t => t.BranchId == branchId);

        // Role Access Control: If Role is Petty Cash Officer (5), restricted to assigned unit
        if (roleId == 5)
        {
            var myUnit = await _context.PettyCashUnitUsers
                .Include(uu => uu.Unit)
                .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
                .Select(uu => uu.Unit)
                .FirstOrDefaultAsync();
            if (myUnit != null)
            {
                // Force filter by my unit
                unitId = myUnit.UnitId;
            }
            else
            {
                // Has no unit assigned, return empty results
                return new { data = new List<object>(), totalData = 0 };
            }
        }

        if (unitId.HasValue)
            query = query.Where(t => t.UnitId == unitId.Value);

        if (!string.IsNullOrEmpty(status))
            query = query.Where(t => t.Status.ToLower() == status.ToLower());

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(t => t.TopUpDate >= start);
        }

        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(t => t.TopUpDate <= end);
        }

        var total = await query.CountAsync();

        query = query.OrderByDescending(t => t.TopUpDate);

        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.ToListAsync();

        // Get transaction/journal info
        var transactionIds = data.Where(d => !string.IsNullOrEmpty(d.TransactionId)).Select(d => d.TransactionId).ToList();
        var transactions = await _context.Transactions
            .Where(t => transactionIds.Contains(t.TransactionId))
            .ToDictionaryAsync(t => t.TransactionId, t => t.JournalNo);

        var result = data.Select(t => new
        {
            t.TopUpId,
            t.TopUpNo,
            t.TopUpDate,
            t.UnitId,
            UnitCode = t.Unit?.UnitCode,
            UnitName = t.Unit?.UnitName,
            t.FromCoaCode,
            t.FromCoaName,
            t.Amount,
            t.Status,
            t.TransactionId,
            JournalNo = !string.IsNullOrEmpty(t.TransactionId) && transactions.ContainsKey(t.TransactionId) ? transactions[t.TransactionId] : null,
            t.Notes,
            t.CreatedDate
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<PettyCashTopUp?> GetTopUpById(int id)
    {
        var topUp = await _context.PettyCashTopUps
            .Include(t => t.Unit)
            .FirstOrDefaultAsync(t => t.TopUpId == id);

        if (topUp != null)
        {
            topUp.UnitName = topUp.Unit?.UnitName;
            topUp.UnitCode = topUp.Unit?.UnitCode;
            topUp.UnitCoaCode = topUp.Unit?.CoaCode;

            if (topUp.Unit != null)
            {
                var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == topUp.Unit.CoaCode);
                topUp.UnitCoaName = coa?.CoaName;
            }

            if (!string.IsNullOrEmpty(topUp.TransactionId))
            {
                var trx = await _context.Transactions.FirstOrDefaultAsync(t => t.TransactionId == topUp.TransactionId);
                topUp.JournalNo = trx?.JournalNo;
            }

            if (topUp.CreatedBy.HasValue)
            {
                var user = await _context.Users.FindAsync(topUp.CreatedBy.Value);
                topUp.CreatedByName = user?.DisplayName;
            }

            if (topUp.ApprovedBy.HasValue)
            {
                var user = await _context.Users.FindAsync(topUp.ApprovedBy.Value);
                topUp.ApprovedByName = user?.DisplayName;
            }

            if (topUp.RejectedBy.HasValue)
            {
                var user = await _context.Users.FindAsync(topUp.RejectedBy.Value);
                topUp.RejectedByName = user?.DisplayName;
            }
        }

        return topUp;
    }

    public async Task<string> GenerateTopUpNo(DateTime date)
    {
        var prefix = $"PCT-{date:yyyyMMdd}-";
        var last = await _context.PettyCashTopUps
            .Where(t => t.TopUpNo.StartsWith(prefix))
            .OrderByDescending(t => t.TopUpNo)
            .FirstOrDefaultAsync();

        int seq = 1;
        if (last != null)
        {
            var parts = last.TopUpNo.Split('-');
            if (parts.Length >= 3 && int.TryParse(parts[2], out int lastSeq))
            {
                seq = lastSeq + 1;
            }
        }

        return $"{prefix}{seq:D4}";
    }



    public async Task<string> GetTopUpDefaultCOA(int branchId)
    {
        var opt = await _optionService.GetByName("OPTION_PETTY_CASH_TOPUP_COA", branchId);
        return opt?.OptionValue ?? "101004001";
    }

    public async Task<string> GetReceivableCOA(int branchId)
    {
        var opt = await _optionService.GetByName("OPTION_PETTY_CASH_RECEIVABLE_COA", branchId);
        return opt?.OptionValue ?? "104003"; // Default: Piutang Karyawan
    }

    public async Task<int> AddTopUp(PettyCashTopUp topUp, int userId, int roleId)
    {
        int branchId = topUp.BranchId ?? 1;
        
        // Role 5 (Petty Cash Officer) - auto-assign source COA from options
        if (roleId == 5 || string.IsNullOrEmpty(topUp.FromCoaCode))
        {
            topUp.FromCoaCode = await GetTopUpDefaultCOA(branchId);
            var coa = await _coaService.GetByCode(topUp.FromCoaCode, branchId);
            topUp.FromCoaName = coa?.CoaName ?? topUp.FromCoaCode;
        }

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Auto-assign Unit
        var unit = await GetUnitByUserId(userId);
        topUp.UnitId = unit.UnitId;

        topUp.TopUpNo = await GenerateTopUpNo(topUp.TopUpDate);
        topUp.TopUpDate = DateTime.SpecifyKind(topUp.TopUpDate, DateTimeKind.Utc);
        topUp.Status = "Pending";
        topUp.CreatedBy = userId;
        topUp.CreatedDate = utcNow;

        _context.PettyCashTopUps.Add(topUp);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashTopUp, "Dibuat",
            topUp.TopUpId.ToString(), userId, topUp.BranchId,
            new { topUp.TopUpNo, topUp.Amount, topUp.FromCoaCode, topUp.FromCoaName, topUp.UnitId });

        return topUp.TopUpId;
    }

    public async Task<bool> ApproveTopUp(int id, decimal approvedAmount, string? notes, int branchId, int userId)
    {
        var topUp = await _context.PettyCashTopUps
            .Include(t => t.Unit)
            .FirstOrDefaultAsync(t => t.TopUpId == id);

        if (topUp == null || topUp.Status != "Pending") return false;
        if (topUp.Unit == null) throw new Exception("Unit not found");

        // Update Amount and Notes
        topUp.ApprovedAmount = approvedAmount;
        if (!string.IsNullOrEmpty(notes))
        {
            topUp.ApprovedNotes = notes;
        }

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var topUpDate = DateTime.SpecifyKind(topUp.TopUpDate, DateTimeKind.Utc);

        // Get COA info
        var unitCoa = await _coaService.GetByCode(topUp.Unit.CoaCode, branchId);
        if (unitCoa == null) throw new Exception($"Unit COA {topUp.Unit.CoaCode} not found");

        var sourceCoa = await _coaService.GetByCode(topUp.FromCoaCode, branchId);
        if (sourceCoa == null) throw new Exception($"Source COA {topUp.FromCoaCode} not found");

        // Create Transaction
        string transactionType = "PettyCashTopUp";
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(topUpDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = topUpDate,
            TransactionName = $"Top Up Petty Cash {topUp.TopUpNo}",
            TransactionRef = topUp.TopUpNo,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Top Up Petty Cash - {topUp.Unit.UnitName}",
            CoaCode = topUp.Unit.CoaCode,
            CoaName = unitCoa.CoaName,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double)topUp.ApprovedAmount,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName
        };

        await _transactionService.Add(transaction);

        // DEBIT: Petty Cash COA (receiving funds)
        var journalDebit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Top Up Petty Cash - {topUp.Unit.UnitName}",
            CoaCode = topUp.Unit.CoaCode,
            CoaName = unitCoa.CoaName,
            CoaParentCode = unitCoa.ParentCode,
            CoaParentName = unitCoa.ParentName,
            DebitAmount = (double)topUp.ApprovedAmount,
            CreditAmount = 0,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalDebit);

        // CREDIT: Source COA (Bank/Kas Induk)
        var journalCredit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Top Up Petty Cash dari {topUp.FromCoaName ?? sourceCoa.CoaName}",
            CoaCode = topUp.FromCoaCode,
            CoaName = topUp.FromCoaName ?? sourceCoa.CoaName,
            CoaParentCode = sourceCoa.ParentCode,
            CoaParentName = sourceCoa.ParentName,
            DebitAmount = 0,
            CreditAmount = (double)topUp.ApprovedAmount,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = false
        };
        await _journalService.Add(journalCredit);

        // Update topup
        var requestedAmount = topUp.Amount;
        topUp.Status = "Posted";
        topUp.ApprovedBy = userId;
        topUp.ApprovedDate = utcNow;
        topUp.TransactionId = transaction.TransactionId;
        topUp.UpdatedBy = userId;
        topUp.UpdatedDate = utcNow;

        // Update unit balance - DEPRECATED: Balance is now calculated from Journal
        // topUp.Unit.CurrentBalance += topUp.Amount;
        // topUp.Unit.UpdatedBy = userId;
        // topUp.Unit.UpdatedDate = utcNow;

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashTopUp, "Disetujui",
            id.ToString(), userId, branchId,
            new
            {
                topUp.TopUpNo,
                RequestedAmount = requestedAmount,
                ApprovedAmount = approvedAmount,
                Notes = notes,
                TransactionId = transaction.TransactionId
            });

        return true;
    }

    public async Task<bool> RejectTopUp(int id, string reason, int userId)
    {
        var topUp = await _context.PettyCashTopUps.FindAsync(id);
        if (topUp == null || topUp.Status != "Pending") return false;

        topUp.Status = "Rejected";
        topUp.RejectedReason = reason;
        topUp.RejectedBy = userId;
        topUp.RejectedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        topUp.UpdatedBy = userId;
        topUp.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashTopUp, "Ditolak",
            id.ToString(), userId, topUp.BranchId,
            new { topUp.TopUpNo, topUp.Amount, Reason = reason });

        return true;
    }

    public async Task<bool> VoidTopUp(int id, int userId)
    {
        var topUp = await _context.PettyCashTopUps.FindAsync(id);
        if (topUp == null || topUp.Status != "Pending") return false;

        topUp.Status = "Void";
        topUp.UpdatedBy = userId;
        topUp.UpdatedDate = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PettyCashTopUp, "Void",
            id.ToString(), userId, topUp.BranchId,
            new { topUp.TopUpNo, topUp.Amount });

        return true;
    }

    public async Task<object?> GetTopUpReceipt(int id)
    {
        var topUp = await _context.PettyCashTopUps
            .Include(t => t.Unit)
            .FirstOrDefaultAsync(t => t.TopUpId == id);

        if (topUp == null) return null;

        // Resolve branch/company info for the receipt header
        var branch = topUp.BranchId.HasValue
            ? await _context.Branches
                .Include(b => b.Company)
                .FirstOrDefaultAsync(b => b.BranchId == topUp.BranchId.Value)
            : null;

        string? createdByName = null;
        if (topUp.CreatedBy.HasValue)
        {
            var user = await _context.Users.FindAsync(topUp.CreatedBy.Value);
            createdByName = user?.DisplayName;
        }

        string? approvedByName = null;
        if (topUp.ApprovedBy.HasValue)
        {
            var user = await _context.Users.FindAsync(topUp.ApprovedBy.Value);
            approvedByName = user?.DisplayName;
        }

        // Receiver = user assigned to the unit (Petty Cash Officer)
        string? receiverName = null;
        if (topUp.Unit != null)
        {
            var unitUser = await _context.PettyCashUnitUsers
                .Where(uu => uu.UnitId == topUp.UnitId)
                .OrderBy(uu => uu.Id)
                .FirstOrDefaultAsync();
            if (unitUser != null)
            {
                var user = await _context.Users.FindAsync(unitUser.UserId);
                receiverName = user?.DisplayName;
            }
        }

        // Use approved amount if posted, else requested amount
        var amount = topUp.Status == "Posted" ? topUp.ApprovedAmount : topUp.Amount;

        return new
        {
            // Header (organization)
            CompanyName = branch?.Company?.CompanyName ?? "PT. Klinik Sehat Sejahtera",
            BranchName = branch?.BranchName,
            Address = branch?.Address ?? branch?.Company?.Address,
            Phone = branch?.Phone ?? branch?.Company?.Phone,
            Email = branch?.Email ?? branch?.Company?.Email,

            // Receipt body
            topUp.TopUpId,
            TopUpNo = topUp.TopUpNo,
            TopUpDate = topUp.TopUpDate,
            ApprovedDate = topUp.ApprovedDate,
            UnitName = topUp.Unit?.UnitName,
            UnitCode = topUp.Unit?.UnitCode,
            Amount = amount,
            RequestedAmount = topUp.Amount,
            ApprovedAmount = topUp.ApprovedAmount,
            Description = $"Top Up Petty Cash {topUp.Unit?.UnitName ?? string.Empty}".Trim(),
            topUp.Notes,
            topUp.ApprovedNotes,
            topUp.Status,

            // Signatures
            HandedByName = approvedByName ?? createdByName,
            ReceivedByName = receiverName,
            CreatedByName = createdByName,
            ApprovedByName = approvedByName,
        };
    }

    #endregion

    #region Ledger

    public async Task<object> GetLedger(int unitId, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId)
    {
        // Role Access Control: If Role is Petty Cash Officer (5), restricted to assigned unit
        if (roleId == 5)
        {
            var myUnit = await _context.PettyCashUnitUsers
                .Include(uu => uu.Unit)
                .Where(uu => uu.UserId == userId && uu.Unit.IsActive)
                .Select(uu => uu.Unit)
                .FirstOrDefaultAsync();
            if (myUnit != null)
            {
                unitId = myUnit.UnitId;
            }
            else
            {
                throw new Exception("Anda tidak memiliki akses ke Unit Kas Kecil manapun.");
            }
        }

        var unit = await _context.PettyCashUnits.FindAsync(unitId);
        if (unit == null) throw new Exception("Unit not found");

        var branchId = unit.BranchId;

        // Saldo saat ini (live) — dihitung dari JURNAL (sumber kebenaran tunggal), sama dgn kartu "Saldo Unit".
        decimal currentBalance = unit.CurrentBalance;
        if (branchId.HasValue)
            currentBalance = (decimal)await _journalService.GetEndingBalance(unit.CoaCode, DateTime.UtcNow, branchId.Value);

        // Rentang tanggal periode (UTC)
        DateTime? start = startDate.HasValue ? DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc) : (DateTime?)null;
        DateTime? end = endDate.HasValue ? DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc) : (DateTime?)null;

        // Saldo awal periode (mutasi jurnal sebelum tanggal mulai) — supaya buku kas bisa direkonsiliasi:
        // Saldo Awal + Total Masuk - Total Keluar = Saldo Akhir.
        decimal openingBalance = 0;
        if (branchId.HasValue && start.HasValue)
            openingBalance = (decimal)await _journalService.GetEndingBalance(unit.CoaCode, start.Value.AddTicks(-1), branchId.Value);

        // === Ambil SEMUA baris jurnal (buku besar) atas COA unit ini — sumber yang sama dgn saldo ===
        var published = Status.Published.ToString();
        var jq = _context.Journals
            .Where(j => j.CoaCode == unit.CoaCode
                     && j.status == published
                     && j.Transaction != null);
        if (branchId.HasValue) jq = jq.Where(j => j.BranchId == branchId.Value);
        if (start.HasValue) jq = jq.Where(j => j.Transaction!.TransactionDate >= start.Value);
        if (end.HasValue) jq = jq.Where(j => j.Transaction!.TransactionDate <= end.Value);

        var rows = await jq
            .Select(j => new
            {
                j.TransactionId,
                j.JournalRef,
                j.JournalDesc,
                j.Source,
                Debit = j.DebitAmount ?? 0,
                Credit = j.CreditAmount ?? 0,
                Date = j.Transaction!.TransactionDate,
                CreatedDate = j.CreatedDate,
                JournalNo = j.Transaction.JournalNo,
                TransactionNo = j.Transaction.TransactionNo
            })
            .ToListAsync();

        // Lookup ID entity per sumber (berdasarkan nomor / JournalRef) untuk link ke modul asal
        const string SRC_TOPUP = "PettyCashTopUp";
        const string SRC_REQUEST = "PettyCashRequest";
        const string SRC_SETTLEMENT = "PettyCashSettlement";
        const string SRC_SETTLEMENT_CANCEL = "PettyCashSettlementCancel";

        var requestRefs = rows.Where(r => r.Source == SRC_REQUEST && !string.IsNullOrEmpty(r.JournalRef)).Select(r => r.JournalRef!).Distinct().ToList();
        var settlementRefs = rows.Where(r => (r.Source == SRC_SETTLEMENT || r.Source == SRC_SETTLEMENT_CANCEL) && !string.IsNullOrEmpty(r.JournalRef)).Select(r => r.JournalRef!).Distinct().ToList();
        var topupRefs = rows.Where(r => r.Source == SRC_TOPUP && !string.IsNullOrEmpty(r.JournalRef)).Select(r => r.JournalRef!).Distinct().ToList();

        var requestIdByNo = requestRefs.Count > 0
            ? await _context.PettyCashRequests.Where(r => requestRefs.Contains(r.RequestNo)).ToDictionaryAsync(r => r.RequestNo, r => r.RequestId)
            : new Dictionary<string, int>();
        var settlementIdByNo = settlementRefs.Count > 0
            ? await _context.PettyCashSettlements.Where(s => settlementRefs.Contains(s.SettlementNo)).ToDictionaryAsync(s => s.SettlementNo, s => s.SettlementId)
            : new Dictionary<string, int>();
        var topupIdByNo = topupRefs.Count > 0
            ? await _context.PettyCashTopUps.Where(t => topupRefs.Contains(t.TopUpNo)).ToDictionaryAsync(t => t.TopUpNo, t => t.TopUpId)
            : new Dictionary<string, int>();

        // === Bangun baris ledger + klasifikasi tipe agar jelas di list ===
        // Petty cash = COA debit-normal → Debit = uang MASUK, Credit = uang KELUAR.
        var ledger = new List<object>();
        foreach (var r in rows)
        {
            var desc = (r.JournalDesc ?? string.Empty).ToLowerInvariant();
            bool isIn = r.Debit > 0;
            var src = r.Source ?? string.Empty;

            string type;
            string category;
            bool isExternal = false;
            int? settlementId = null, requestId = null, topUpId = null;

            switch (src)
            {
                case SRC_TOPUP:
                    type = "Top Up (In)";
                    category = "topup";
                    if (!string.IsNullOrEmpty(r.JournalRef) && topupIdByNo.TryGetValue(r.JournalRef, out var tid)) topUpId = tid;
                    break;

                case SRC_REQUEST:
                    type = "Pengeluaran (Out)";
                    category = "request";
                    if (!string.IsNullOrEmpty(r.JournalRef) && requestIdByNo.TryGetValue(r.JournalRef, out var rid)) requestId = rid;
                    break;

                case SRC_SETTLEMENT_CANCEL:
                    // Pembalikan: arah uangnya kebalikan dari settlement asli.
                    type = isIn ? "Pembatalan Settlement (In)" : "Pembatalan Settlement (Out)";
                    category = isIn ? "settlement_cancel_in" : "settlement_cancel_out";
                    if (!string.IsNullOrEmpty(r.JournalRef) && settlementIdByNo.TryGetValue(r.JournalRef, out var cid)) settlementId = cid;
                    break;

                case SRC_SETTLEMENT:
                    if (isIn)
                    {
                        bool lebih = desc.Contains("sisa") || desc.Contains("lebih") || desc.Contains("kembali") || desc.Contains("pengembalian");
                        type = lebih ? "Lebih Bayar (In)" : "Realisasi (In)";
                        category = lebih ? "settlement_over" : "settlement_in";
                    }
                    else
                    {
                        bool kurang = desc.Contains("kurang") || desc.Contains("talangan");
                        type = kurang ? "Kurang Bayar (Out)" : "Settlement (Out)";
                        category = kurang ? "settlement_short" : "settlement_out";
                    }
                    if (!string.IsNullOrEmpty(r.JournalRef) && settlementIdByNo.TryGetValue(r.JournalRef, out var sid)) settlementId = sid;
                    break;

                default:
                    // Sumber di luar modul petty cash (jurnal manual / scheduler / modul lain)
                    isExternal = true;
                    type = isIn ? "Jurnal Manual (In)" : "Jurnal Manual (Out)";
                    category = "external";
                    break;
            }

            ledger.Add(new
            {
                TransactionId = r.TransactionId,
                Date = r.Date ?? r.CreatedDate ?? DateTime.UnixEpoch,
                Type = type,
                Category = category,
                Source = src,
                IsExternal = isExternal,
                TransactionNo = string.IsNullOrEmpty(r.JournalRef) ? r.TransactionNo : r.JournalRef,
                Description = r.JournalDesc,
                Debit = (decimal)r.Debit,
                Credit = (decimal)r.Credit,
                JournalNo = r.JournalNo,
                SettlementId = settlementId,
                RequestId = requestId,
                TopUpId = topUpId
            });
        }

        var sorted = ledger.OrderByDescending(l => ((dynamic)l).Date).ToList();
        var total = sorted.Count;

        if (page != -1)
        {
            sorted = sorted.Skip((page - 1) * pageSize).Take(pageSize).ToList();
        }

        // === Ringkasan — dihitung dari jurnal yang SAMA → dijamin rekonsiliasi dgn saldo ===
        decimal totalIn = (decimal)rows.Sum(r => r.Debit);
        decimal totalOut = (decimal)rows.Sum(r => r.Credit);

        bool IsPc(string? s) => s == SRC_TOPUP || s == SRC_REQUEST || s == SRC_SETTLEMENT;

        return new
        {
            data = sorted,
            totalData = total,
            unitInfo = new
            {
                unit.UnitId,
                unit.UnitCode,
                unit.UnitName,
                CurrentBalance = currentBalance,
                OpeningBalance = openingBalance,
                ClosingBalance = openingBalance + totalIn - totalOut,
                TotalIn = totalIn,
                TotalOut = totalOut,
                // Rincian per sumber (agar jelas komposisinya)
                TotalTopUp = (decimal)rows.Where(r => r.Source == SRC_TOPUP).Sum(r => r.Debit),
                TotalRequest = (decimal)rows.Where(r => r.Source == SRC_REQUEST).Sum(r => r.Credit),
                TotalSettlementIn = (decimal)rows.Where(r => r.Source == SRC_SETTLEMENT).Sum(r => r.Debit),
                TotalSettlementOut = (decimal)rows.Where(r => r.Source == SRC_SETTLEMENT).Sum(r => r.Credit),
                TotalExternalIn = (decimal)rows.Where(r => !IsPc(r.Source)).Sum(r => r.Debit),
                TotalExternalOut = (decimal)rows.Where(r => !IsPc(r.Source)).Sum(r => r.Credit)
            }
        };
    }

    #endregion

    #region Export

    public async Task<byte[]> ExportRequests(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int roleId, int userId)
    {
        var result = await GetRequests(branchId, unitId, status, null, startDate, endDate, null, -1, 0, roleId, userId);
        var data = ((dynamic)result).data;

        var exportData = ((IEnumerable<dynamic>)data).Select((x, index) => new
        {
            No = index + 1,
            No_Request = x.RequestNo,
            Tanggal = ((DateTime)x.RequestDate).ToString("dd/MM/yyyy"),
            Unit = x.UnitName ?? "-",
            Pemohon = x.RequesterName,
            Keperluan = x.Purpose,
            Jumlah = (decimal)x.Amount,
            Status = x.Status,
            Jumlah_Disetujui = x.ApprovedAmount ?? 0
        }).ToList();

        return ExportToDocs.ConvertToExcel(exportData);
    }

    public async Task<byte[]> ExportSettlements(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId)
    {
        var result = await GetSettlements(branchId, unitId, status, startDate, endDate, page, pageSize, roleId, userId);
        var data = ((dynamic)result).data;

        var exportData = ((IEnumerable<dynamic>)data).Select((x, index) => new
        {
            No = index + 1,
            No_Settlement = x.SettlementNo,
            Tanggal = ((DateTime)x.SettlementDate).ToString("dd/MM/yyyy"),
            Unit = x.UnitName ?? "-",
            Total = (decimal)x.TotalAmount,
            Status = x.Status,
            No_Jurnal = x.JournalNo ?? "-"
        }).ToList();

        return ExportToDocs.ConvertToExcel(exportData);
    }

    public async Task<byte[]> ExportTopUps(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId)
    {
        var result = await GetTopUps(branchId, unitId, status, startDate, endDate, page, pageSize, roleId, userId);
        var data = ((dynamic)result).data;

        var exportData = ((IEnumerable<dynamic>)data).Select((x, index) => new
        {
            No = index + 1,
            No_TopUp = x.TopUpNo,
            Tanggal = ((DateTime)x.TopUpDate).ToString("dd/MM/yyyy"),
            Unit = x.UnitName ?? "-",
            Sumber = x.FromCoaName ?? x.FromCoaCode,
            Jumlah = (decimal)x.Amount,
            Status = x.Status,
            No_Jurnal = x.JournalNo ?? "-"
        }).ToList();

        return ExportToDocs.ConvertToExcel(exportData);
    }

    public async Task<byte[]> ExportLedger(int unitId, DateTime? startDate, DateTime? endDate, int roleId, int userId)
    {
        var result = await GetLedger(unitId, startDate, endDate, -1, 0, roleId, userId);
        var data = ((dynamic)result).data;
        var unitInfo = ((dynamic)result).unitInfo;

        // Buku kas: urut menaik + saldo berjalan mulai dari Saldo Awal periode.
        decimal running = (decimal)unitInfo.OpeningBalance;
        var ascending = ((IEnumerable<dynamic>)data).OrderBy(x => (DateTime)x.Date).ToList();

        var exportData = new List<object>();
        int no = 1;
        foreach (var x in ascending)
        {
            running += (decimal)x.Debit - (decimal)x.Credit;
            exportData.Add(new
            {
                No = no++,
                Tanggal = ((DateTime)x.Date).ToString("dd/MM/yyyy"),
                No_Transaksi = x.TransactionNo,
                No_Jurnal = x.JournalNo,
                Tipe = x.Type,
                Sumber = ((bool)x.IsExternal) ? "Luar Sistem" : (string)x.Source,
                Keterangan = x.Description,
                Masuk = (decimal)x.Debit,
                Keluar = (decimal)x.Credit,
                Saldo = running
            });
        }

        return ExportToDocs.ConvertToExcel(exportData);
    }

    #endregion
}
