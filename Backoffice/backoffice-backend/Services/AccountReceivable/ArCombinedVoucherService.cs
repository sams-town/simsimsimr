using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services.AccountReceivable;

public class ArCombinedVoucherService : IArCombinedVoucherService
{
    private readonly RSSMDbContext _context;

    public ArCombinedVoucherService(RSSMDbContext context)
    {
        _context = context;
    }

    private static double Effective(RSSMBackend.Entities.AccountReceivable x) =>
        x.AdjustmentAmount != null && x.AdjustmentAmount != 0
            ? (double)x.AdjustmentAmount.Value
            : x.TotalAmount;

    /// <summary>
    /// Base query for AR items eligible to be combined:
    /// finalized (has voucher number), status Posting (not yet paid), and active (not void).
    /// </summary>
    private IQueryable<RSSMBackend.Entities.AccountReceivable> EligibleBaseQuery()
    {
        return _context.AccountReceivables.Where(x =>
            x.VoucherNumber != null && x.VoucherNumber != "" &&
            x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting" &&
            (x.Status == null || x.Status.ToLower() != "void"));
    }

    public async Task<object> GetEligibleItems(
        string? payerType,
        string? payerId,
        DateTime? startDate,
        DateTime? endDate,
        string? keyword,
        int page,
        int pageSize,
        int? excludeCombinedId = null)
    {
        var query = EligibleBaseQuery();

        // Exclude AR items already attached to a combined batch (except the one we are editing)
        var usedArIds = await _context.ArCombinedVoucherItems
            .Where(i => excludeCombinedId == null || i.CombinedId != excludeCombinedId.Value)
            .Select(i => i.ArId)
            .ToListAsync();

        if (usedArIds.Count > 0)
            query = query.Where(x => !usedArIds.Contains(x.ArId));

        if (!string.IsNullOrEmpty(payerType))
        {
                query = query.Where(x =>
                    x.PayerType != null && x.PayerType.ToLower().Equals(payerType.ToLower()));
           
        }

        if (!string.IsNullOrEmpty(payerId))
            query = query.Where(x => x.PayerId == payerId);

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.VoucherDate >= start);
        }

        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(x => x.VoucherDate <= end);
        }

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x =>
                (x.PatientName != null && x.PatientName.ToLower().Contains(kw)) ||
                (x.MedicalRecordNo != null && x.MedicalRecordNo.ToLower().Contains(kw)) ||
                (x.VoucherNumber != null && x.VoucherNumber.ToLower().Contains(kw)) ||
                (x.SepNo != null && x.SepNo.ToLower().Contains(kw)));
        }

        query = query.OrderByDescending(x => x.VoucherDate).ThenBy(x => x.PatientName);

        int total = await query.CountAsync();

        if (page != -1)
            query = query.Skip((page - 1) * pageSize).Take(pageSize);

        var data = await query.ToListAsync();

        var result = data.Select(x => new
        {
            x.ArId,
            x.ArNumber,
            x.PatientName,
            x.MedicalRecordNo,
            x.SepNo,
            x.PayerId,
            x.PayerName,
            x.PayerType,
            ServiceType = x.ServiceType,
            x.VoucherNumber,
            x.VoucherDate,
            x.TransactionDate,
            x.TotalAmount,
            x.AdjustmentAmount,
            EffectiveAmount = Effective(x)
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<object> GetList(
        string? keyword,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize)
    {
        IQueryable<ArCombinedVoucher> query = _context.ArCombinedVouchers
            .Where(x => x.Status == null || x.Status.ToLower() != "deleted");

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x =>
                x.BatchNumber.ToLower().Contains(kw) ||
                (x.Description != null && x.Description.ToLower().Contains(kw)));
        }

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.BatchDate >= start);
        }

        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(x => x.BatchDate <= end);
        }

        query = query.OrderByDescending(x => x.BatchDate).ThenByDescending(x => x.Id);

        int total = await query.CountAsync();

        var batches = page != -1
            ? await query.Skip((page - 1) * pageSize).Take(pageSize).ToListAsync()
            : await query.ToListAsync();

        var batchIds = batches.Select(b => b.Id).ToList();

        // Load items joined with AR for the batches on this page
        var items = await _context.ArCombinedVoucherItems
            .Where(i => batchIds.Contains(i.CombinedId))
            .Join(_context.AccountReceivables,
                i => i.ArId,
                a => a.ArId,
                (i, a) => new { i.CombinedId, a })
            .ToListAsync();

        var itemsByBatch = items.GroupBy(x => x.CombinedId)
            .ToDictionary(g => g.Key, g => g.ToList());

        var result = batches.Select(b =>
        {
            var list = itemsByBatch.ContainsKey(b.Id) ? itemsByBatch[b.Id] : new();
            return new
            {
                b.Id,
                b.BatchNumber,
                b.BatchDate,
                b.Description,
                b.Status,
                b.CreatedDate,
                ItemCount = list.Count,
                PayerCount = list.Select(x => x.a.PayerId).Distinct().Count(),
                TotalAmount = list.Sum(x => Effective(x.a))
            };
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<object> Create(DateTime batchDate, string? description, List<int> arIds, int userId)
    {
        var vDate = DateTime.SpecifyKind(batchDate.Date, DateTimeKind.Utc);
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Filter the provided AR ids down to eligible + not-yet-batched
        var validArIds = await GetAttachableArIds(arIds, null);

        // Determine branch from the first valid AR item
        int? branchId = null;
        if (validArIds.Count > 0)
        {
            branchId = await _context.AccountReceivables
                .Where(x => validArIds.Contains(x.ArId))
                .Select(x => x.BranchId)
                .FirstOrDefaultAsync();
        }

        var batchNumber = await GenerateBatchNumber(vDate);

        var batch = new ArCombinedVoucher
        {
            BatchNumber = batchNumber,
            BatchDate = vDate,
            Description = description,
            Status = "ACTIVE",
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow
        };

        await _context.ArCombinedVouchers.AddAsync(batch);
        await _context.SaveChangesAsync();

        if (validArIds.Count > 0)
        {
            var newItems = validArIds.Select(arId => new ArCombinedVoucherItem
            {
                CombinedId = batch.Id,
                ArId = arId,
                CreatedDate = utcNow
            }).ToList();

            await _context.ArCombinedVoucherItems.AddRangeAsync(newItems);
            await _context.SaveChangesAsync();
        }

        return new { id = batch.Id, batchNumber = batch.BatchNumber, addedCount = validArIds.Count };
    }

    public async Task<object?> GetDetail(int id)
    {
        var batch = await _context.ArCombinedVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (batch == null) return null;

        var items = await _context.ArCombinedVoucherItems
            .Where(i => i.CombinedId == id)
            .Join(_context.AccountReceivables,
                i => i.ArId,
                a => a.ArId,
                (i, a) => a)
            .OrderBy(a => a.PayerName).ThenBy(a => a.PatientName)
            .ToListAsync();

        var itemList = items.Select(a => new
        {
            a.ArId,
            a.ArNumber,
            a.PatientName,
            a.MedicalRecordNo,
            a.SepNo,
            a.PayerId,
            a.PayerName,
            a.PayerType,
            a.ServiceType,
            a.VoucherNumber,
            a.VoucherDate,
            a.TransactionDate,
            a.DueDate,
            a.TotalAmount,
            a.AdjustmentAmount,
            a.Notes,
            a.NoteHistory,
            EffectiveAmount = Effective(a)
        }).ToList();

        return new
        {
            batch.Id,
            batch.BatchNumber,
            batch.BatchDate,
            batch.Description,
            batch.Status,
            batch.CreatedDate,
            ItemCount = itemList.Count,
            PayerCount = itemList.Select(x => x.PayerId).Distinct().Count(),
            TotalAmount = itemList.Sum(x => x.EffectiveAmount),
            Items = itemList
        };
    }

    public async Task<bool> AddItems(int id, List<int> arIds, int userId)
    {
        var batch = await _context.ArCombinedVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (batch == null) return false;

        var validArIds = await GetAttachableArIds(arIds, id);
        if (validArIds.Count == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        var newItems = validArIds.Select(arId => new ArCombinedVoucherItem
        {
            CombinedId = id,
            ArId = arId,
            CreatedDate = utcNow
        }).ToList();

        await _context.ArCombinedVoucherItems.AddRangeAsync(newItems);

        batch.UpdatedBy = userId;
        batch.UpdatedDate = utcNow;
        _context.ArCombinedVouchers.Update(batch);

        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> RemoveItems(int id, List<int> arIds, int userId)
    {
        if (arIds == null || arIds.Count == 0) return false;

        var batch = await _context.ArCombinedVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (batch == null) return false;

        var items = await _context.ArCombinedVoucherItems
            .Where(i => i.CombinedId == id && arIds.Contains(i.ArId))
            .ToListAsync();

        if (items.Count == 0) return false;

        _context.ArCombinedVoucherItems.RemoveRange(items);

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        batch.UpdatedBy = userId;
        batch.UpdatedDate = utcNow;
        _context.ArCombinedVouchers.Update(batch);

        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> Delete(int id)
    {
        var batch = await _context.ArCombinedVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (batch == null) return false;

        var items = await _context.ArCombinedVoucherItems
            .Where(i => i.CombinedId == id)
            .ToListAsync();

        if (items.Count > 0)
            _context.ArCombinedVoucherItems.RemoveRange(items);

        _context.ArCombinedVouchers.Remove(batch);
        await _context.SaveChangesAsync();
        return true;
    }

    /// <summary>
    /// From a list of requested AR ids, return those that are eligible (finalized, not paid, active)
    /// and not already attached to another combined batch.
    /// </summary>
    private async Task<List<int>> GetAttachableArIds(List<int> arIds, int? currentBatchId)
    {
        if (arIds == null || arIds.Count == 0) return new List<int>();

        var distinctIds = arIds.Distinct().ToList();

        var eligibleIds = await EligibleBaseQuery()
            .Where(x => distinctIds.Contains(x.ArId))
            .Select(x => x.ArId)
            .ToListAsync();

        var usedArIds = await _context.ArCombinedVoucherItems
            .Where(i => distinctIds.Contains(i.ArId) &&
                        (currentBatchId == null || i.CombinedId != currentBatchId.Value))
            .Select(i => i.ArId)
            .ToListAsync();

        return eligibleIds.Where(arId => !usedArIds.Contains(arId)).ToList();
    }

    private async Task<string> GenerateBatchNumber(DateTime batchDate)
    {
        var prefix = $"GAB-{batchDate:yyyyMMdd}";
        var last = await _context.ArCombinedVouchers
            .Where(x => x.BatchNumber.StartsWith(prefix))
            .OrderByDescending(x => x.BatchNumber)
            .FirstOrDefaultAsync();

        int sequence = 1;
        if (last != null)
        {
            var parts = last.BatchNumber.Split('-');
            if (parts.Length == 3 && int.TryParse(parts[2], out int lastSeq))
                sequence = lastSeq + 1;
        }

        return $"{prefix}-{sequence:D4}";
    }
}
