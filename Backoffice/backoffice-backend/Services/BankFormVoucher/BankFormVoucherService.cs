using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;

namespace RSSMBackend.Services.BankFormMaker;

public class BankFormVoucherService : IBankFormVoucherService
{
    private readonly RSSMDbContext _context;

    public BankFormVoucherService(RSSMDbContext context)
    {
        _context = context;
    }

    private static string PrefixFor(string voucherType) =>
        voucherType.Equals("PENGELUARAN", StringComparison.OrdinalIgnoreCase) ? "BPG" : "BPN";

    private static string NormalizeType(string voucherType) =>
        voucherType.Equals("PENGELUARAN", StringComparison.OrdinalIgnoreCase) ? "PENGELUARAN" : "PENERIMAAN";

    // Edit window: a voucher can only be changed within 3 days of creation (H, H+1, H+2). Locked from H+3.
    private const int EditWindowDays = 3;
    private static void EnsureEditable(DateTime createdDate)
    {
        if ((DateTime.UtcNow.Date - createdDate.Date).TotalDays >= EditWindowDays)
            throw new InvalidOperationException("Bukti tidak dapat diubah karena sudah lebih dari 3 hari sejak dibuat.");
    }

    // Net amount actually disbursed/received = amount - discount - pph23 (pph23 only when enabled).
    private static decimal ComputeTotal(BankFormVoucherInput input)
    {
        var pph = input.Pph23Enabled ? input.Pph23Amount : 0;
        var total = input.Amount - input.DiscountAmount - pph;
        return total < 0 ? 0 : total;
    }

    public async Task<object> GetList(
        string voucherType,
        string? keyword,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize)
    {
        var vType = NormalizeType(voucherType);

        IQueryable<BankFormVoucher> query = _context.BankFormVouchers
            .Where(x => x.Status != "DELETED" && x.VoucherType == vType);

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x =>
                x.VoucherNumber.ToLower().Contains(kw) ||
                (x.PersonName != null && x.PersonName.ToLower().Contains(kw)) ||
                (x.CoaName != null && x.CoaName.ToLower().Contains(kw)));
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

        var result = vouchers.Select(v => new
        {
            v.Id,
            v.VoucherNumber,
            v.VoucherDate,
            v.VoucherType,
            v.PaymentType,
            v.Status,
            v.PersonName,
            v.CoaName,
            v.Amount,
            v.TotalAmount,
            v.CreatedDate
        }).ToList();

        return new { data = result, totalData = total };
    }

    public async Task<object?> GetDetail(int id)
    {
        var v = await _context.BankFormVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (v == null) return null;

        return new
        {
            v.Id,
            v.VoucherNumber,
            v.VoucherDate,
            v.VoucherType,
            v.PaymentType,
            v.Status,
            v.PersonName,
            v.CoaCode,
            v.CoaName,
            v.Amount,
            v.DiscountPercent,
            v.DiscountAmount,
            v.Pph23Enabled,
            v.Pph23Percent,
            v.Pph23Amount,
            v.Purpose,
            v.TotalAmount,
            v.Notes,
            v.RecipientBankName,
            v.RecipientAccountName,
            v.RecipientAccountNumber,
            v.CreatedDate
        };
    }

    public async Task<object> Create(BankFormVoucherInput input, int? branchId, int userId)
    {
        var vType = NormalizeType(input.VoucherType);
        var vDate = DateTime.SpecifyKind(input.VoucherDate.Date, DateTimeKind.Utc);
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        if (input.Amount <= 0)
            throw new InvalidOperationException("Jumlah harus lebih dari 0.");

        var voucherNumber = await GenerateVoucherNumber(vType, vDate);

        // Penerimaan has no discount / PPh23.
        var isPengeluaran = vType == "PENGELUARAN";

        var voucher = new BankFormVoucher
        {
            VoucherNumber = voucherNumber,
            VoucherDate = vDate,
            VoucherType = vType,
            PaymentType = string.IsNullOrEmpty(input.PaymentType) ? "CASH" : input.PaymentType,
            Status = "ACTIVE",
            PersonName = input.PersonName,
            CoaCode = input.CoaCode,
            CoaName = input.CoaName,
            Amount = input.Amount,
            DiscountPercent = isPengeluaran ? input.DiscountPercent : 0,
            DiscountAmount = isPengeluaran ? input.DiscountAmount : 0,
            Pph23Enabled = isPengeluaran && input.Pph23Enabled,
            Pph23Percent = isPengeluaran ? input.Pph23Percent : 0,
            Pph23Amount = isPengeluaran ? input.Pph23Amount : 0,
            Purpose = input.Purpose,
            Notes = input.Notes,
            RecipientBankName = isPengeluaran ? input.RecipientBankName : null,
            RecipientAccountName = isPengeluaran ? input.RecipientAccountName : null,
            RecipientAccountNumber = isPengeluaran ? input.RecipientAccountNumber : null,
            TotalAmount = isPengeluaran ? ComputeTotal(input) : input.Amount,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow
        };

        await _context.BankFormVouchers.AddAsync(voucher);
        await _context.SaveChangesAsync();

        return new { id = voucher.Id, voucherNumber = voucher.VoucherNumber };
    }

    public async Task<bool> Update(int id, BankFormVoucherInput input, int userId)
    {
        var voucher = await _context.BankFormVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return false;

        EnsureEditable(voucher.CreatedDate);

        var isPengeluaran = voucher.VoucherType == "PENGELUARAN";

        voucher.VoucherDate = DateTime.SpecifyKind(input.VoucherDate.Date, DateTimeKind.Utc);
        voucher.PaymentType = string.IsNullOrEmpty(input.PaymentType) ? voucher.PaymentType : input.PaymentType;
        voucher.PersonName = input.PersonName;
        voucher.CoaCode = input.CoaCode;
        voucher.CoaName = input.CoaName;
        voucher.Amount = input.Amount;
        voucher.DiscountPercent = isPengeluaran ? input.DiscountPercent : 0;
        voucher.DiscountAmount = isPengeluaran ? input.DiscountAmount : 0;
        voucher.Pph23Enabled = isPengeluaran && input.Pph23Enabled;
        voucher.Pph23Percent = isPengeluaran ? input.Pph23Percent : 0;
        voucher.Pph23Amount = isPengeluaran ? input.Pph23Amount : 0;
        voucher.Purpose = input.Purpose;
        voucher.Notes = input.Notes;
        voucher.RecipientBankName = isPengeluaran ? input.RecipientBankName : null;
        voucher.RecipientAccountName = isPengeluaran ? input.RecipientAccountName : null;
        voucher.RecipientAccountNumber = isPengeluaran ? input.RecipientAccountNumber : null;
        voucher.TotalAmount = isPengeluaran ? ComputeTotal(input) : input.Amount;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        voucher.UpdatedBy = userId;
        voucher.UpdatedDate = utcNow;
        _context.BankFormVouchers.Update(voucher);

        await _context.SaveChangesAsync();
        return true;
    }

    public async Task<bool> Delete(int id)
    {
        var voucher = await _context.BankFormVouchers.FirstOrDefaultAsync(x => x.Id == id);
        if (voucher == null) return false;

        _context.BankFormVouchers.Remove(voucher);
        await _context.SaveChangesAsync();
        return true;
    }

    private async Task<string> GenerateVoucherNumber(string voucherType, DateTime voucherDate)
    {
        var prefix = $"{PrefixFor(voucherType)}-{voucherDate:yyyyMMdd}";
        var last = await _context.BankFormVouchers
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
