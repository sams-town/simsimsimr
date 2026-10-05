using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services.AccountPayable;

public class ApPaymentVoucherService : IApPaymentVoucherService
{
    private readonly RSSMDbContext _context;

    public ApPaymentVoucherService(RSSMDbContext context)
    {
        _context = context;
    }

    /// <summary>
    /// AP invoices eligible to be added: sudah tukar faktur (Status = "Sent") DAN belum dibayar (Unpaid),
    /// serta belum terpasang di voucher mana pun. Partial/Paid tidak masuk.
    /// Status null (data lama) diperlakukan sebagai Unpaid, konsisten dengan halaman Pembayaran AP.
    /// </summary>
    private IQueryable<InvoiceExchange> EligibleBaseQuery()
    {
        return _context.InvoiceExchanges.Where(x =>
            x.Status != null && x.Status.ToLower() == "sent" &&
            (x.PaymentStatus == null || x.PaymentStatus.ToLower() == "unpaid"));
    }

    public async Task<object> GetEligibleItems(
        string? supplierName,
        string? supplierType,
        string? paymentStatus,
        string? keyword,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize,
        int? excludeVoucherId)
    {
        var query = EligibleBaseQuery();

        // Exclude invoices already attached to ANY voucher (including the current one)
        var usedIds = await _context.ApPaymentVoucherItems
            .Select(i => i.InvoiceExchangeId)
            .Distinct()
            .ToListAsync();

        if (usedIds.Count > 0)
            query = query.Where(x => !usedIds.Contains(x.InvoiceExchangeId));

        if (!string.IsNullOrEmpty(supplierName))
            query = query.Where(x => x.SupplierName != null && x.SupplierName.ToLower().Contains(supplierName.ToLower()));

        if (!string.IsNullOrEmpty(supplierType))
        {
            switch (supplierType.ToLower())
            {
                case "medis":
                    query = query.Where(x => x.SupplierCode != null && x.SupplierCode.StartsWith("M") && !x.SupplierCode.StartsWith("NM"));
                    break;
                case "umum":
                    query = query.Where(x => x.SupplierCode != null && x.SupplierCode.StartsWith("NM"));
                    break;
                case "dapur":
                    query = query.Where(x => x.SupplierCode != null && x.SupplierCode.StartsWith("DP"));
                    break;
            }
        }

        if (!string.IsNullOrEmpty(paymentStatus))
        {
            if (paymentStatus.Equals("Unpaid", StringComparison.OrdinalIgnoreCase))
                query = query.Where(x => x.PaymentStatus == null || x.PaymentStatus.ToLower() == "unpaid");
            else
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == paymentStatus.ToLower());
        }

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x =>
                (x.InvoiceNumber != null && x.InvoiceNumber.ToLower().Contains(kw)) ||
                (x.PONumber != null && x.PONumber.ToLower().Contains(kw)) ||
                (x.SupplierName != null && x.SupplierName.ToLower().Contains(kw)));
        }

        if (startDate.HasValue && endDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(x => x.DueDate != null && x.DueDate >= start && x.DueDate <= end);
        }

        query = query.OrderBy(x => x.DueDate).ThenBy(x => x.SupplierName);

        int total = await query.CountAsync();

        if (page != -1)
            query = query.Skip((page - 1) * pageSize).Take(pageSize);

        var data = await query.ToListAsync();

        var today = DateTime.UtcNow.Date;
        var result = data.Select(x =>
        {
            var outstanding = x.OutstandingAmount ?? (x.TotalTransaction ?? 0) - (x.PaidAmount ?? 0);
            var agingDays = x.DueDate.HasValue ? (int)(today - x.DueDate.Value.Date).TotalDays : 0;
            return new
            {
                x.InvoiceExchangeId,
                x.InvoiceNumber,
                PoNumber = x.PONumber,
                x.SupplierName,
                x.ReceiptDate,
                x.DueDate,
                TotalTransaction = x.TotalTransaction ?? 0,
                PaidAmount = x.PaidAmount ?? 0,
                OutstandingAmount = outstanding < 0 ? 0 : outstanding,
                PaymentStatus = x.PaymentStatus ?? "Unpaid",
                AgingDays = agingDays > 0 ? agingDays : 0,
                AgingBucket = GetAgingBucket(agingDays),
            };
        }).ToList();

        return new { data = result, totalData = total };
    }

    private static string GetAgingBucket(int agingDays)
    {
        if (agingDays <= 0) return "Current";
        if (agingDays <= 30) return "1-30";
        if (agingDays <= 60) return "31-60";
        if (agingDays <= 90) return "61-90";
        return ">90";
    }

    public async Task<object> GetList(
        string? supplierName,
        string? keyword,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize)
    {
        IQueryable<ApPaymentVoucher> query = _context.ApPaymentVouchers
            .Where(x => x.Status != "DELETED");

        if (!string.IsNullOrEmpty(supplierName))
        {
            var sn = supplierName.ToLower();
            var matchingVoucherIds = await _context.ApPaymentVoucherItems
                .Join(_context.InvoiceExchanges,
                    i => i.InvoiceExchangeId,
                    ie => ie.InvoiceExchangeId,
                    (i, ie) => new { i.VoucherId, ie.SupplierName })
                .Where(x => x.SupplierName != null && x.SupplierName.ToLower().Contains(sn))
                .Select(x => x.VoucherId)
                .Distinct()
                .ToListAsync();
            query = query.Where(x => matchingVoucherIds.Contains(x.Id));
        }

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x => x.VoucherNumber.ToLower().Contains(kw));
        }

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

        query = query.OrderByDescending(x => x.VoucherDate).ThenByDescending(x => x.Id);

        int total = await query.CountAsync();

        var vouchers = page != -1
            ? await query.Skip((page - 1) * pageSize).Take(pageSize).ToListAsync()
            : await query.ToListAsync();

        var voucherIds = vouchers.Select(v => v.Id).ToList();

        var items = await _context.ApPaymentVoucherItems
            .Where(i => voucherIds.Contains(i.VoucherId))
            .Join(_context.InvoiceExchanges,
                i => i.InvoiceExchangeId,
                ie => ie.InvoiceExchangeId,
                (i, ie) => new { i.VoucherId, ie })
            .ToListAsync();

        var itemsByVoucher = items.GroupBy(x => x.VoucherId)
            .ToDictionary(g => g.Key, g => g.ToList());

        var result = vouchers.Select(v =>
        {
            var list = itemsByVoucher.ContainsKey(v.Id) ? itemsByVoucher[v.Id] : new();
            var supplierName = list.FirstOrDefault()?.ie.SupplierName ?? "";
            return new
            {
                v.Id,
                v.VoucherNumber,
                v.VoucherDate,
                v.PaymentType,
                v.Status,
                v.CreatedDate,
                SupplierName = supplierName,
                ItemCount = list.Count,
                TotalAmount = list.Sum(x => x.ie.OutstandingAmount ?? (x.ie.TotalTransaction ?? 0) - (x.ie.PaidAmount ?? 0))
            };
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<object?> GetDetail(int id)
    {
        var voucher = await _context.ApPaymentVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return null;

        var items = await _context.ApPaymentVoucherItems
            .Where(i => i.VoucherId == id)
            .Join(_context.InvoiceExchanges,
                i => i.InvoiceExchangeId,
                ie => ie.InvoiceExchangeId,
                (i, ie) => ie)
            .OrderBy(ie => ie.DueDate).ThenBy(ie => ie.InvoiceNumber)
            .ToListAsync();

        var itemList = items.Select(ie => new
        {
            ie.InvoiceExchangeId,
            ie.InvoiceNumber,
            PoNumber = ie.PONumber,
            ie.SupplierName,
            ie.DueDate,
            TotalTransaction = ie.TotalTransaction ?? 0,
            PaidAmount = ie.PaidAmount ?? 0,
            OutstandingAmount = ie.OutstandingAmount ?? (ie.TotalTransaction ?? 0) - (ie.PaidAmount ?? 0),
            ie.PaymentStatus,
        }).ToList();

        var supplierName = itemList.FirstOrDefault()?.SupplierName ?? "";
        var supplierCode = items.FirstOrDefault()?.SupplierCode;

        // Fetch bank account: Contact where RefCode = supplierCode AND ContactCategoryId = 1
        string? supplierBankInfo = null;
        if (!string.IsNullOrEmpty(supplierCode))
        {
            var contact = await _context.Contacts
                .FirstOrDefaultAsync(c => c.RefCode == supplierCode && c.ContactCategoryId == 1);

            if (contact != null)
            {
                var account = await _context.ContactAccounts
                    .Where(a => a.ContactId == contact.ContactId)
                    .Join(_context.Banks,
                        a => a.BankCode,
                        b => b.BankCode,
                        (a, b) => new { a.AccountNo, BankName = b.BankName })
                    .FirstOrDefaultAsync();

                if (account != null)
                    supplierBankInfo = $"{account.BankName} {account.AccountNo}";
            }
        }

        return new
        {
            voucher.Id,
            voucher.VoucherNumber,
            voucher.VoucherDate,
            voucher.PaymentType,
            voucher.Status,
            voucher.CreatedDate,
            SupplierName = supplierName,
            SupplierBankInfo = supplierBankInfo,
            ItemCount = itemList.Count,
            TotalAmount = itemList.Sum(x => x.OutstandingAmount),
            Items = itemList
        };
    }

    public async Task<object> Create(DateTime voucherDate, string paymentType, List<int> invoiceExchangeIds, int userId)
    {
        var vDate = DateTime.SpecifyKind(voucherDate.Date, DateTimeKind.Utc);
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        var validIds = await GetAttachableIds(invoiceExchangeIds, null);
        if (validIds.Count == 0)
            throw new InvalidOperationException("Tidak ada invoice yang dapat diproses: semua invoice yang dipilih sudah terdaftar di voucher lain atau statusnya sudah lunas.");

        var voucherNumber = await GenerateVoucherNumber(vDate);

        var voucher = new ApPaymentVoucher
        {
            VoucherNumber = voucherNumber,
            VoucherDate = vDate,
            PaymentType = string.IsNullOrEmpty(paymentType) ? "CASH" : paymentType,
            Status = "ACTIVE",
            CreatedBy = userId,
            CreatedDate = utcNow
        };

        await _context.ApPaymentVouchers.AddAsync(voucher);
        await _context.SaveChangesAsync();

        if (validIds.Count > 0)
        {
            var newItems = validIds.Select(invoiceExchangeId => new ApPaymentVoucherItem
            {
                VoucherId = voucher.Id,
                InvoiceExchangeId = invoiceExchangeId,
                CreatedDate = utcNow
            }).ToList();

            await _context.ApPaymentVoucherItems.AddRangeAsync(newItems);
            await _context.SaveChangesAsync();
        }

        return new { id = voucher.Id, voucherNumber = voucher.VoucherNumber, addedCount = validIds.Count };
    }

    // Edit window: a voucher can only be changed within 3 days of creation (H, H+1, H+2). Locked from H+3.
    private const int EditWindowDays = 3;
    private static void EnsureEditable(DateTime createdDate)
    {
        if ((DateTime.UtcNow.Date - createdDate.Date).TotalDays >= EditWindowDays)
            throw new InvalidOperationException("Bukti pengeluaran tidak dapat diubah karena sudah lebih dari 3 hari sejak dibuat.");
    }

    public async Task<bool> UpdateVoucher(int id, DateTime? voucherDate, string? paymentType, int userId)
    {
        var voucher = await _context.ApPaymentVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return false;

        EnsureEditable(voucher.CreatedDate);

        if (voucherDate.HasValue)
            voucher.VoucherDate = DateTime.SpecifyKind(voucherDate.Value.Date, DateTimeKind.Utc);

        if (!string.IsNullOrEmpty(paymentType))
            voucher.PaymentType = paymentType;

        var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        voucher.UpdatedBy = userId;
        voucher.UpdatedDate = now;
        _context.ApPaymentVouchers.Update(voucher);

        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> AddItems(int id, List<int> invoiceExchangeIds, int userId)
    {
        var voucher = await _context.ApPaymentVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return false;

        EnsureEditable(voucher.CreatedDate);

        var validIds = await GetAttachableIds(invoiceExchangeIds, id);
        if (validIds.Count == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        var newItems = validIds.Select(invoiceExchangeId => new ApPaymentVoucherItem
        {
            VoucherId = id,
            InvoiceExchangeId = invoiceExchangeId,
            CreatedDate = utcNow
        }).ToList();

        await _context.ApPaymentVoucherItems.AddRangeAsync(newItems);

        voucher.UpdatedBy = userId;
        voucher.UpdatedDate = utcNow;
        _context.ApPaymentVouchers.Update(voucher);

        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> RemoveItems(int id, List<int> invoiceExchangeIds, int userId)
    {
        if (invoiceExchangeIds == null || invoiceExchangeIds.Count == 0) return false;

        var voucher = await _context.ApPaymentVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return false;

        EnsureEditable(voucher.CreatedDate);

        var items = await _context.ApPaymentVoucherItems
            .Where(i => i.VoucherId == id && invoiceExchangeIds.Contains(i.InvoiceExchangeId))
            .ToListAsync();

        if (items.Count == 0) return false;

        _context.ApPaymentVoucherItems.RemoveRange(items);

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        voucher.UpdatedBy = userId;
        voucher.UpdatedDate = utcNow;
        _context.ApPaymentVouchers.Update(voucher);

        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> Delete(int id)
    {
        var voucher = await _context.ApPaymentVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return false;

        var items = await _context.ApPaymentVoucherItems
            .Where(i => i.VoucherId == id)
            .ToListAsync();

        if (items.Count > 0)
            _context.ApPaymentVoucherItems.RemoveRange(items);

        _context.ApPaymentVouchers.Remove(voucher);
        await _context.SaveChangesAsync();
        return true;
    }

    private async Task<List<int>> GetAttachableIds(List<int> invoiceExchangeIds, int? currentVoucherId)
    {
        if (invoiceExchangeIds == null || invoiceExchangeIds.Count == 0) return new List<int>();

        var distinctIds = invoiceExchangeIds.Distinct().ToList();

        var eligibleIds = await EligibleBaseQuery()
            .Where(x => distinctIds.Contains(x.InvoiceExchangeId))
            .Select(x => x.InvoiceExchangeId)
            .ToListAsync();

        // Exclude invoices already in ANY voucher (other vouchers + the current one to prevent duplicates)
        var usedIds = await _context.ApPaymentVoucherItems
            .Where(i => distinctIds.Contains(i.InvoiceExchangeId))
            .Select(i => i.InvoiceExchangeId)
            .ToListAsync();

        return eligibleIds.Where(id => !usedIds.Contains(id)).ToList();
    }

    private async Task<string> GenerateVoucherNumber(DateTime voucherDate)
    {
        var prefix = $"FMR-{voucherDate:yyyyMMdd}";
        var last = await _context.ApPaymentVouchers
            .Where(x => x.VoucherNumber.StartsWith(prefix))
            .OrderByDescending(x => x.VoucherNumber)
            .FirstOrDefaultAsync();

        int sequence = 1;
        if (last != null)
        {
            var parts = last.VoucherNumber.Split('-');
            if (parts.Length == 3 && int.TryParse(parts[2], out int lastSeq))
                sequence = lastSeq + 1;
        }

        return $"{prefix}-{sequence:D4}";
    }
}
