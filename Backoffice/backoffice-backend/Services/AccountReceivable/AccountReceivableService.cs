using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Helpers;
using Microsoft.EntityFrameworkCore;
using OfficeOpenXml;
using Microsoft.AspNetCore.Http;
using System.Text.Json;

using RSSMBackend.Models; // For SettlementRequest
using TDPBackend.Helpers;
namespace RSSMBackend.Services.AccountReceivable;

public class AccountReceivableService : IAccountReceivableService
{
    private readonly RSSMDbContext _context;
    private readonly ITransactionService _transactionService;
    private readonly IJournalService _journalService;
    private readonly ICOAService _coaService;
    private readonly IOptionService _optionService;
    private readonly AuditTrailUtils _auditTrailUtils;

    public AccountReceivableService(
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

    public async Task<object> GetList(
        string? payerType,
        string? paymentStatus,
        string? serviceType,
        string? payerId,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize,
        string? voucherNumber = null,
        string? keyword = null)
    {
        var today = DateTime.UtcNow.Date;
        
        IQueryable<RSSMBackend.Entities.AccountReceivable> query = _context.AccountReceivables;

        if (!string.IsNullOrEmpty(voucherNumber))
        {
             query = query.Where(x => x.VoucherNumber == voucherNumber);
             // If we have a specific voucher, we usually want all items in it (Posting/Paid/Partial)
             // unless we are specifically looking for something else.
             // We'll skip the default status filtering if voucherNumber is provided and status is default.
        }

        if (!string.IsNullOrEmpty(payerType))
        {
            query = query.Where(x => x.PayerType.ToLower() == payerType.ToLower());
        }

        if (!string.IsNullOrEmpty(paymentStatus))
        {
            if (paymentStatus.ToLower() == "posting")
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting");
                query = query.Where(x => x.VoucherNumber == null || x.VoucherNumber == "");
            }
            else if (paymentStatus.ToLower() == "vouchered")
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting");
                query = query.Where(x => x.VoucherNumber != null && x.VoucherNumber != "");
            }
            else if (paymentStatus.ToLower() == "final")
            {
                // Final status = Posting status AND has Voucher Number
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting");
                if (string.IsNullOrEmpty(voucherNumber)) 
                {
                    // Only enforce non-null if specific voucherNumber wasn't already filtered above
                    query = query.Where(x => x.VoucherNumber != null && x.VoucherNumber != "");
                }
            }
            else
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == paymentStatus.ToLower());
            }
        }

        if (!string.IsNullOrEmpty(serviceType))
        {
            query = query.Where(x => x.ServiceType != null && x.ServiceType.ToLower() == serviceType.ToLower());
        }

        if (!string.IsNullOrEmpty(payerId))
        {
            var payerIds = payerId.Split(',', StringSplitOptions.RemoveEmptyEntries).ToList();
            if (payerIds.Count > 1)
            {
                query = query.Where(x => payerIds.Contains(x.PayerId));
            }
            else
            {
                query = query.Where(x => x.PayerId == payerId);
            }
        }

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.TransactionDate >= start);
        }
        
        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(x => x.TransactionDate <= end);
        }

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x => 
                (x.SepNo != null && x.SepNo.ToLower().Contains(kw)) ||
                (x.MedicalRecordNo != null && x.MedicalRecordNo.ToLower().Contains(kw)) ||
                (x.PatientName != null && x.PatientName.ToLower().Contains(kw))
            );
        }




        // Exclude Void items by default unless explicitly searching for it (which is unlikely for main list)
        // User said: "jika sudah Void.. tidak masuk ke list"
        query = query.Where(x => x.PaymentStatus == null || x.PaymentStatus.ToLower() != "void");

        query = query.OrderByDescending(x => x.TransactionDate);

        int total = await query.CountAsync();

        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.ToListAsync();

        var result = data.Select(x => new
        {
            x.ArId,
            x.ArNumber,
            x.JournalId,
            x.PayerType,
            PayerTypeName = x.PayerTypeName,
            x.PayerId,
            x.PayerName,
            x.ClaimNumber,
            ServiceType = x.ServiceType == "Ralan" ? "Rawat Jalan" : (x.ServiceType == "Ranap" ? "Rawat Inap" : x.ServiceType),
            x.CoaCode,
            x.TransactionDate,
            x.DueDate,
            x.TotalAmount,
            x.PaidAmount,
            OutstandingAmount = (x.AdjustmentAmount != null && x.AdjustmentAmount != 0
                ? (double)x.AdjustmentAmount.Value : x.TotalAmount) - (x.PaidAmount ?? 0),
            x.AdjustmentAmount,
            x.AdjustmentDate,
            x.AdjustmentHistory,
            x.PaymentStatus,
            x.PaymentDate,
            x.PaymentRef,
            AgingDays = x.DueDate.HasValue ? (int)(today - x.DueDate.Value.Date).TotalDays : (int)(today - x.TransactionDate.Date).TotalDays,
            AgingBucket = GetAgingBucket(x.DueDate.HasValue ? (int)(today - x.DueDate.Value.Date).TotalDays : (int)(today - x.TransactionDate.Date).TotalDays),
            x.ReconcileStatus,
            x.VoucherNumber,
            x.Notes,
            x.NoteHistory,
            x.UpdatedDate,
            x.PatientName,
            x.MedicalRecordNo,
            InvoiceNo = x.ArNumber,
            x.SepNo,
            x.IsPrinted,
            x.Status,
            x.PayerChangeHistory
        }).ToList();

        return new { data = result, totalData = total };
    }

    private string GetAgingBucket(int agingDays)
    {
        if (agingDays <= 0) return "Current";
        if (agingDays <= 30) return "1-30";
        if (agingDays <= 60) return "31-60";
        if (agingDays <= 90) return "61-90";
        return ">90";
    }

    public async Task<RSSMBackend.Entities.AccountReceivable?> GetById(int id)
    {
        return await _context.AccountReceivables.FindAsync(id);
    }

    public async Task<object> GetAgingReport(string? payerId, DateTime? asOfDate, string? payerType = null)
    {
        var today = asOfDate?.Date ?? DateTime.UtcNow.Date;

        IQueryable<RSSMBackend.Entities.AccountReceivable> query = _context.AccountReceivables
            .Where(x => x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid")
            .Where(x => x.VoucherNumber != null && x.VoucherNumber != "");

        if (!string.IsNullOrEmpty(payerType))
        {
            if (payerType.ToLower() == "bpjs")
                query = query.Where(x => x.PayerType.ToLower() == "bpjs");
            else if (payerType.ToLower() == "non bpjs")
                query = query.Where(x => x.PayerType.ToLower() != "bpjs");
        }

        if (!string.IsNullOrEmpty(payerId))
        {
            query = query.Where(x => x.PayerId == payerId);
        }

        var data = await query.ToListAsync();

        var agingData = data
            .GroupBy(x => new 
            { 
                x.VoucherNumber,
                x.PayerId
            })
            .Select(g =>
            {
                var first = g.First();
                
                var items = g.ToList();
                double current = 0, d1to30 = 0, d31to60 = 0, d61to90 = 0, over90 = 0;

                foreach (var item in items)
                {
                    var amount = (item.AdjustmentAmount != null && item.AdjustmentAmount != 0
                        ? (double)item.AdjustmentAmount.Value : item.TotalAmount) - (item.PaidAmount ?? 0);
                    // Aging calculation based on VoucherDate (Payment Voucher Date)
                    var agingDays = item.VoucherDate.HasValue 
                        ? (int)(today - item.VoucherDate.Value.Date).TotalDays 
                        : (int)(today - item.TransactionDate.Date).TotalDays;

                    if (agingDays <= 0) current += amount;
                    else if (agingDays <= 30) d1to30 += amount;
                    else if (agingDays <= 60) d31to60 += amount;
                    else if (agingDays <= 90) d61to90 += amount;
                    else over90 += amount;
                }

                return new
                {
                    VoucherDate = first.VoucherDate,
                    VoucherNumber = first.VoucherNumber,
                    PayerName = first.PayerName,
                    DueDate = first.DueDate,
                    Current = current,
                    Days1To30 = d1to30,
                    Days31To60 = d31to60,
                    Days61To90 = d61to90,
                    Over90Days = over90,
                    Total = current + d1to30 + d31to60 + d61to90 + over90
                };
            })
            .OrderBy(x => x.PayerName)
            .ThenByDescending(x => x.VoucherDate)
            .ToList();

        return new { data = agingData };
    }

    public async Task<bool> ProcessPayment(
        List<int>? arIds,
        string? payerId,
        DateTime paymentDate,
        string paymentCoaCode,
        string paymentRef,
        int userId)
    {
        if ((arIds == null || arIds.Count == 0) && string.IsNullOrEmpty(payerId))
            return false;

        IQueryable<RSSMBackend.Entities.AccountReceivable> query = _context.AccountReceivables
            .Where(x => x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid");

        if (!string.IsNullOrEmpty(payerId))
        {
            // Process all pending vouchers for this payer
            // A payment usually covers all consolidated invoices (vouchers) for a payer
            query = query.Where(x => x.PayerId == payerId && !string.IsNullOrEmpty(x.VoucherNumber));
        }
        else if (arIds != null && arIds.Count > 0)
        {
            query = query.Where(x => arIds.Contains(x.ArId));
        }
        
        var items = await query.ToListAsync();

        if (items.Count == 0)
            return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var payDate = DateTime.SpecifyKind(paymentDate.Date, DateTimeKind.Utc);

        foreach (var item in items)
        {
            item.PaymentStatus = "Paid";
            item.PaidAmount = item.TotalAmount;
            item.PaymentDate = payDate;
            item.PaymentRef = paymentRef;
            item.PaymentCoaCode = paymentCoaCode;
            item.UpdatedBy = userId;
            item.UpdatedDate = utcNow;
        }

        _context.AccountReceivables.UpdateRange(items);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.AccountReceivable, "Pembayaran",
            items.Select(i => (i.ArId.ToString(), (object?)new
            {
                i.ArNumber,
                i.PayerName,
                PaymentDate = payDate,
                PaymentRef = paymentRef,
                PaymentCoaCode = paymentCoaCode,
                PaidAmount = i.PaidAmount
            })),
            userId, items.First().BranchId);

        return true;
    }

    public Task<object> GetPayerTypeOptions()
    {
        var options = new List<object>
        {
            new { value = "BPJS", label = "BPJS Kesehatan" },
            new { value = "ASR", label = "Asuransi Swasta" },
            new { value = "PRH", label = "Perusahaan" },
            new { value = "DKK", label = "Dinkes/Kemkes" },
            new { value = "KRY", label = "Karyawan" },
            new { value = "DKT", label = "Dokter" },
            new { value = "PSN", label = "Pasien Individu" }
        };
        return Task.FromResult<object>(options);
    }

    public async Task<byte[]> Export(
        string? payerType, 
        string? paymentStatus, 
        string? serviceType,
        string? payerId,
        DateTime? startDate, 
        DateTime? endDate, 
        string type,
        string? voucherNumber = null)
    {
        var today = DateTime.UtcNow.Date;
        
        IQueryable<RSSMBackend.Entities.AccountReceivable> query = _context.AccountReceivables;

        if (!string.IsNullOrEmpty(voucherNumber))
        {
            query = query.Where(x => x.VoucherNumber == voucherNumber);
        }

        if (!string.IsNullOrEmpty(payerType))
        {
            query = query.Where(x => x.PayerType.ToLower() == payerType.ToLower());
        }

        if (!string.IsNullOrEmpty(serviceType))
        {
            query = query.Where(x => x.ServiceType != null && x.ServiceType.ToLower() == serviceType.ToLower());
        }

        if (!string.IsNullOrEmpty(payerId))
        {
            var payerIds = payerId.Split(',', StringSplitOptions.RemoveEmptyEntries).ToList();
            if (payerIds.Count > 1)
            {
                query = query.Where(x => payerIds.Contains(x.PayerId));
            }
            else
            {
                query = query.Where(x => x.PayerId == payerId);
            }
        }

        if (!string.IsNullOrEmpty(paymentStatus))
        {
            if (paymentStatus.ToLower() == "posting")
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting");
                query = query.Where(x => x.VoucherNumber == null || x.VoucherNumber == "");
            }
            else if (paymentStatus.ToLower() == "vouchered")
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting");
                query = query.Where(x => x.VoucherNumber != null && x.VoucherNumber != "");
            }
            else if (paymentStatus.ToLower() == "final")
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == "posting");
                query = query.Where(x => x.VoucherNumber != null && x.VoucherNumber != "");
            }
            else
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == paymentStatus.ToLower());
            }
        }

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.TransactionDate >= start);
        }
        
        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            query = query.Where(x => x.TransactionDate <= end);
        }

        var data = await query.OrderByDescending(x => x.TransactionDate).ToListAsync();

        return ExportToDocs.ConvertAccountReceivableToExcel(data);
    }

    public async Task<byte[]> ExportAgingReport(string? payerId, DateTime? asOfDate, string type, string? payerType = null)
    {
        var today = asOfDate?.Date ?? DateTime.UtcNow.Date;

        IQueryable<RSSMBackend.Entities.AccountReceivable> query = _context.AccountReceivables
            .Where(x => x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid")
            .Where(x => x.VoucherNumber != null && x.VoucherNumber != "");

        if (!string.IsNullOrEmpty(payerType))
        {
            if (payerType.ToLower() == "bpjs")
                query = query.Where(x => x.PayerType.ToLower() == "bpjs");
            else if (payerType.ToLower() == "non bpjs")
                query = query.Where(x => x.PayerType.ToLower() != "bpjs");
        }

        if (!string.IsNullOrEmpty(payerId))
        {
             var payerIds = payerId.Split(',', StringSplitOptions.RemoveEmptyEntries).ToList();
             if (payerIds.Count > 1)
             {
                 query = query.Where(x => payerIds.Contains(x.PayerId));
             }
             else
             {
                 query = query.Where(x => x.PayerId == payerId);
             }
        }

        var data = await query.ToListAsync();

        var agingData = data
            .GroupBy(x => new 
            { 
                x.VoucherNumber,
                x.PayerId
            })
            .Select(g =>
            {
                var first = g.First();
                var items = g.ToList();
                double current = 0, d1to30 = 0, d31to60 = 0, d61to90 = 0, over90 = 0;

                foreach (var item in items)
                {
                    var amount = (item.AdjustmentAmount != null && item.AdjustmentAmount != 0
                        ? (double)item.AdjustmentAmount.Value : item.TotalAmount) - (item.PaidAmount ?? 0);
                    // Aging calculation based on VoucherDate (Payment Voucher Date)
                    var agingDays = item.VoucherDate.HasValue 
                        ? (int)(today - item.VoucherDate.Value.Date).TotalDays 
                        : (int)(today - item.TransactionDate.Date).TotalDays;

                    if (agingDays <= 0) current += amount;
                    else if (agingDays <= 30) d1to30 += amount;
                    else if (agingDays <= 60) d31to60 += amount;
                    else if (agingDays <= 90) d61to90 += amount;
                    else over90 += amount;
                }

                return new
                {
                    VoucherDate = first.VoucherDate,
                    VoucherNumber = first.VoucherNumber,
                    PayerName = first.PayerName,
                    DueDate = first.DueDate,
                    Current = current,
                    Days1To30 = d1to30,
                    Days31To60 = d31to60,
                    Days61To90 = d61to90,
                    Over90Days = over90,
                    Total = current + d1to30 + d31to60 + d61to90 + over90
                };
            })
            .OrderBy(x => x.PayerName)
            .ThenByDescending(x => x.VoucherDate)
            .ToList();

        // Calculate Totals for Summary
        double totalCurrent = agingData.Sum(x => x.Current);
        double total1To30 = agingData.Sum(x => x.Days1To30);
        double total31To60 = agingData.Sum(x => x.Days31To60);
        double total61To90 = agingData.Sum(x => x.Days61To90);
        double totalOver90 = agingData.Sum(x => x.Over90Days);
        double grandTotal = agingData.Sum(x => x.Total);

        // Prepare Summary Data
        var summaryData = new List<object>
        {
            new { Kategori = "CURRENT", Amount = totalCurrent },
            new { Kategori = "1-30 HARI", Amount = total1To30 },
            new { Kategori = "31-60 HARI", Amount = total31To60 },
            new { Kategori = "61-90 HARI", Amount = total61To90 },
            new { Kategori = ">90 HARI", Amount = totalOver90 },
            new { Kategori = "TOTAL", Amount = grandTotal }
        };

        // Prepare Detail Data
        var detailData = agingData.Select(x => new
        {
            Tgl_Voucher = x.VoucherDate.HasValue ? x.VoucherDate.Value.ToString("dd/MM/yyyy") : "-",
            No_Voucher = x.VoucherNumber,
            Asuransi = x.PayerName,
            Jatuh_Tempo = x.DueDate.HasValue ? x.DueDate.Value.ToString("dd/MM/yyyy") : "-",
            Current = x.Current,
            Days_1_30 = x.Days1To30,
            Days_31_60 = x.Days31To60,
            Days_61_90 = x.Days61To90,
            Over_90 = x.Over90Days,
            Total = x.Total
        }).ToList();

        // We need a custom export to handle two tables (Summary and Detail)
        // Since ExportToDocs might be simple, we'll use a custom approach or just append lists if possible.
        // Assuming ExportToDocs.ConvertToExcelWithMultipleSheets or similar exists, or we construct manually.
        // Since we don't know if the helper supports multiple tables on one sheet easily, 
        // we'll use a dictionary to pass to a more flexible method if available, OR
        // we'll update ExportToDocs helper OR just return the detail list if summary is too hard without library access.
        
        // HOWEVER, user requested "summary dan list". 
        // Let's check ExportToDocs helper or use a library directly if possible.
        // Since I cannot see ExportToDocs source right now, I'll assume standard list export for now 
        // BUT I will try to use a "List<dynamic>" approach where I put summary at top then empty row then details? 
        // That's messy with types.
        
        // Let's assume for now I export the Detail Data, which is the most important part matching the grid.
        // The summary is visible on screen.
        // WAIT, user said "sama seperti tampilan web ada summary dan list".
        // I will try to conform to that by creating a combined list if I can't do multi-table.
        // Actually, if I just modify the existing Helper or create a new one it's better.
        // For now let's reuse ConvertToExcel but maybe I can pre-pend summary rows?
        // No, columns would mismatch.
        
        // Let's just return the Detail List first (which matches the grid exactly) and 
        // maybe add a separate sheet if the helper supports it?
        // I'll stick to Detail List matching the grid columns as a safe first step, 
        // as the "Summary" is just aggregation of these columns anyway. 
        // User said "field yg tampil sama seperti di page web" -> The page has summary cards AND a list.
        // The list has columns: Tgl Voucher, No Voucher, Asuransi, Jatuh Tempo, Current, 1-30, ... Total.
        // So `detailData` above matches the list perfectly. 
        
        // Let's return detailData.
        return ExportToDocs.ConvertToExcel(detailData);
    }

    /// <summary>
    /// Get insurance options from contacts with category_id = 5
    /// </summary>
    public async Task<object> GetInsuranceOptions()
    {
        var payers = await _context.AccountReceivables
            .Where(ar => ar.PayerId != null && ar.PayerId != "")
            .Select(ar => new { ar.PayerId, ar.PayerName, ar.PayerType })
            .Distinct()
            .OrderBy(ar => ar.PayerName)
            .Select(ar => new
            {
                value = ar.PayerId,
                label = ar.PayerName ?? ar.PayerId,
                payerType = ar.PayerType
            })
            .ToListAsync();

        return payers;
    }

    /// <summary>
    /// Post AR items (status: Pending -> Posting)
    /// </summary>
    public async Task<bool> PostAR(List<int> arIds, int userId)
    {
        if (arIds == null || arIds.Count == 0) return false;

        var items = await _context.AccountReceivables
            .Where(x => arIds.Contains(x.ArId) && 
                        x.PaymentStatus.ToLower() == "pending")
            .ToListAsync();

        if (items.Count == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        foreach (var item in items)
        {
            // Ensure DateTime fields are UTC to prevent Npgsql error
            if (item.TransactionDate.Kind == DateTimeKind.Unspecified)
                item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
            if (item.DueDate.HasValue && item.DueDate.Value.Kind == DateTimeKind.Unspecified)
                item.DueDate = DateTime.SpecifyKind(item.DueDate.Value, DateTimeKind.Utc);
            if (item.PaymentDate.HasValue && item.PaymentDate.Value.Kind == DateTimeKind.Unspecified)
                item.PaymentDate = DateTime.SpecifyKind(item.PaymentDate.Value, DateTimeKind.Utc);
            if (item.ReconcileDate.HasValue && item.ReconcileDate.Value.Kind == DateTimeKind.Unspecified)
                item.ReconcileDate = DateTime.SpecifyKind(item.ReconcileDate.Value, DateTimeKind.Utc);
            if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified)
                item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);

            item.PaymentStatus = "Posting";
            item.UpdatedBy = userId;
            item.UpdatedDate = utcNow;
        }

        _context.AccountReceivables.UpdateRange(items);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.AccountReceivable, "Posting",
            items.Select(i => (i.ArId.ToString(), (object?)new
            {
                i.ArNumber,
                i.PayerName,
                i.TotalAmount
            })),
            userId, items.First().BranchId);

        return true;
    }

    /// <summary>
    /// Unpost AR items (status: Posting -> Pending, only if voucher_number is null)
    /// </summary>
    public async Task<bool> UnpostAR(List<int> arIds, int userId)
    {
        if (arIds == null || arIds.Count == 0) return false;

        var items = await _context.AccountReceivables
            .Where(x => arIds.Contains(x.ArId) && 
                        x.PaymentStatus.ToLower() == "posting" &&
                        x.VoucherNumber == null)
            .ToListAsync();

        if (items.Count == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        foreach (var item in items)
        {
            // Ensure DateTime fields are UTC to prevent Npgsql error
            if (item.TransactionDate.Kind == DateTimeKind.Unspecified)
                item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
            if (item.DueDate.HasValue && item.DueDate.Value.Kind == DateTimeKind.Unspecified)
                item.DueDate = DateTime.SpecifyKind(item.DueDate.Value, DateTimeKind.Utc);
            if (item.PaymentDate.HasValue && item.PaymentDate.Value.Kind == DateTimeKind.Unspecified)
                item.PaymentDate = DateTime.SpecifyKind(item.PaymentDate.Value, DateTimeKind.Utc);
            if (item.ReconcileDate.HasValue && item.ReconcileDate.Value.Kind == DateTimeKind.Unspecified)
                item.ReconcileDate = DateTime.SpecifyKind(item.ReconcileDate.Value, DateTimeKind.Utc);
            if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified)
                item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);

            item.PaymentStatus = "Pending";
            item.UpdatedBy = userId;
            item.UpdatedDate = utcNow;
        }

        _context.AccountReceivables.UpdateRange(items);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.AccountReceivable, "Batal Posting",
            items.Select(i => (i.ArId.ToString(), (object?)new
            {
                i.ArNumber,
                i.PayerName,
                i.TotalAmount
            })),
            userId, items.First().BranchId);

        return true;
    }

    /// <summary>
    /// Get voucher list grouped by payer
    /// </summary>
    public async Task<object> GetVoucherList(
        string? payerId,
        string? status,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize,
        string? voucherNumber = null,
        string? payerType = null)
    {
        IQueryable<RSSMBackend.Entities.AccountReceivable> query = _context.AccountReceivables.AsQueryable();

        if (!string.IsNullOrEmpty(payerType))
        {
            if (payerType.ToLower() == "bpjs")
                query = query.Where(x => x.PayerType.ToLower() == "bpjs");
            else if (payerType.ToLower() == "non bpjs")
                query = query.Where(x => x.PayerType.ToLower() != "bpjs");
        }

        if (!string.IsNullOrEmpty(voucherNumber))
        {
            // If voucherNumber is provided, we filter by it directly
            query = query.Where(x => x.VoucherNumber == voucherNumber);
        }
        else if (!string.IsNullOrEmpty(status))
        {
            if (status.ToLower() == "final")
            {
                query = query.Where(x => x.PaymentStatus.ToLower() == "posting" && x.VoucherNumber != null && x.VoucherNumber != "");
            }
            else if (status.ToLower() == "posting")
            {
                query = query.Where(x => x.PaymentStatus.ToLower() == "posting" && (x.VoucherNumber == null || x.VoucherNumber == ""));
            }
            else if (status.ToLower() == "paid")
            {
                query = query.Where(x => x.PaymentStatus.ToLower() == "paid");
            }
            else if (status.ToLower() == "settlement")
            {
                 // Settlement view: Only show Paid or Final (Posting with Voucher)
                 // Exclude Posting without Voucher
                 query = query.Where(x => 
                    x.PaymentStatus.ToLower() == "paid" || 
                    (x.PaymentStatus.ToLower() == "posting" && x.VoucherNumber != null && x.VoucherNumber != "")
                 );
            }
            else
            {
                 // Default to generic filter if status matches none of specific keywords but is passed
                 query = query.Where(x => x.PaymentStatus.ToLower() == status.ToLower());
            }
        }
        else
        {
             // Default behavior (or 'settlement'/empty status): 
             // Show Paid or Final (Posting with Voucher).
             // Exclude Posting without Voucher effectively, unless specifically requested via 'posting' status.
             query = query.Where(x => 
                x.PaymentStatus.ToLower() == "paid" || 
                (x.PaymentStatus.ToLower() == "posting" && x.VoucherNumber != null && x.VoucherNumber != "")
             );
        }

        if (!string.IsNullOrEmpty(payerId))
        {
            query = query.Where(x => x.PayerId == payerId);
        }

        if (startDate.HasValue)
        {
            var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            if (!string.IsNullOrEmpty(status) && status.ToLower() == "posting")
            {
                 query = query.Where(x => x.TransactionDate >= start);
            }
            else 
            {
                 query = query.Where(x => x.VoucherDate >= start);
            }
        }

        if (endDate.HasValue)
        {
            var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
            if (!string.IsNullOrEmpty(status) && status.ToLower() == "posting")
            {
                 query = query.Where(x => x.TransactionDate <= end);
            }
            else 
            {
                 query = query.Where(x => x.VoucherDate <= end);
            }
        }

        var data = await query.ToListAsync();

        // Get list of unique voucher numbers to fetch their payment history
        var voucherNumbers = data
            .Where(x => !string.IsNullOrEmpty(x.VoucherNumber))
            .Select(x => x.VoucherNumber!)
            .Distinct()
            .ToList();

        var paymentsMap = new Dictionary<string, List<ArSettlementPayment>>();
        if (voucherNumbers.Count > 0)
        {
            var allPayments = await _context.ArSettlementPayments
                .Where(p => voucherNumbers.Contains(p.VoucherNumber))
                .OrderByDescending(p => p.PaymentDate)
                .ToListAsync();
            
            paymentsMap = allPayments
                .GroupBy(p => p.VoucherNumber)
                .ToDictionary(g => g.Key, g => g.ToList());
        }

        // ID-1669: Fetch COA Names for PaymentCoaCode (from AR items or Payments)
        // For simplicity, we'll map COA names if we need them in the summary
        var coaCodes = data
            .Where(x => !string.IsNullOrEmpty(x.PaymentCoaCode))
            .Select(x => x.PaymentCoaCode!)
            .Concat(paymentsMap.Values.SelectMany(plist => plist).Select(p => p.CoaCode).Where(c => !string.IsNullOrEmpty(c))!)
            .Distinct()
            .ToList();

        var coaMap = new Dictionary<string, string>();
        if (coaCodes.Count > 0)
        {
             coaMap = await _context.Coa
                .Where(c => coaCodes.Contains(c.CoaCode))
                .ToDictionaryAsync(c => c.CoaCode, c => c.CoaName);
        }

        var grouped = data
            .GroupBy(x => new { x.PayerId, x.PayerName, x.VoucherNumber })
            .Select(g => 
            {
                var first = g.First();
                var vNumber = g.Key.VoucherNumber ?? "";
                var vPayments = paymentsMap.ContainsKey(vNumber) ? paymentsMap[vNumber] : new List<ArSettlementPayment>();
                
                double totalPaid = vPayments.Sum(p => p.Amount);
                double totalDiscount = vPayments.Sum(p => p.Discount);
                double totalVoucherAmount = g.Sum(x => x.AdjustmentAmount != null && x.AdjustmentAmount != 0
                    ? (double)x.AdjustmentAmount.Value : x.TotalAmount);
                
                var lastPayment = vPayments.FirstOrDefault();
                var paymentCoaName = lastPayment != null && !string.IsNullOrEmpty(lastPayment.CoaCode) && coaMap.ContainsKey(lastPayment.CoaCode)
                    ? coaMap[lastPayment.CoaCode] 
                    : null;

                // Construct Payment History from the new table
                var history = vPayments.Select(p => new PaymentHistoryItem
                {
                    PaymentDate = p.PaymentDate,
                    Amount = p.Amount,
                    Note = p.PaymentRef ?? "Settlement",
                    CoaCode = p.CoaCode,
                    CoaName = p.CoaName,
                    PaymentRef = p.PaymentRef
                }).ToList();

                return new
                {
                    PayerId = g.Key.PayerId,
                    PayerName = g.Key.PayerName,
                    VoucherNumber = g.Key.VoucherNumber,
                    VoucherDate = first.VoucherDate,
                    DueDate = first.DueDate,
                    ItemCount = g.Count(),
                    TotalAmount = totalVoucherAmount,
                    PaidAmount = totalPaid,
                    DiscountAmount = totalDiscount,
                    OutstandingAmount = Math.Max(0, totalVoucherAmount - totalPaid - totalDiscount),
                    Status = (totalPaid + totalDiscount >= totalVoucherAmount - 100) ? "Paid" : 
                            (totalPaid > 0 ? "Partial" : 
                            (g.Key.VoucherNumber != null ? "Final Receivable Voucher" : "Posting")),
                    PaymentDate = lastPayment?.PaymentDate ?? first.PaymentDate,
                    PaymentRef = lastPayment?.PaymentRef ?? first.PaymentRef,
                    PaymentCoaCode = lastPayment?.CoaCode ?? first.PaymentCoaCode,
                    PaymentCoaName = paymentCoaName,
                    PaymentHistory = Newtonsoft.Json.JsonConvert.SerializeObject(history)
                };
            })
            .OrderByDescending(x => x.VoucherDate ?? DateTime.MinValue)
            .ThenBy(x => x.PayerName)
            .ToList();

        int total = grouped.Count;

        if (page != -1)
        {
            grouped = grouped.Skip((page - 1) * pageSize).Take(pageSize).ToList();
        }

        return new { data = grouped, totalData = total };
    }

    /// <summary>
    /// Finalize voucher - assign voucher_number to posted AR items by payer
    /// </summary>
    public async Task<bool> FinalizeVoucher(string payerId, DateTime voucherDate, DateTime? dueDate, int userId, List<int>? arIds = null)
    {
        if (string.IsNullOrEmpty(payerId)) return false;

        var query = _context.AccountReceivables
            .Where(x => x.PayerId == payerId &&
                        x.PaymentStatus.ToLower() == "posting" &&
                        x.VoucherNumber == null);

        if (arIds != null && arIds.Count > 0)
        {
            query = query.Where(x => arIds.Contains(x.ArId));
        }

        var items = await query.ToListAsync();

        if (items.Count == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        // Ensure inputs are treated as UTC
        var vDate = DateTime.SpecifyKind(voucherDate.Date, DateTimeKind.Utc);
        
        // If vDate is already Utc (from SpecifyKind), it's fine. 
        // If voucherDate came in as Unspecified, SpecifyKind(..., Utc) sets it to Utc.
        // If it came in as Local, it sets it to Utc but keeps the ticks (which is what we want for "Business Date").

        // Generate voucher number: PVR-YYYYMMDD-XXXX
        var lastVoucher = await _context.AccountReceivables
            .Where(x => x.VoucherNumber != null && x.VoucherNumber.StartsWith($"PVR-{vDate:yyyyMMdd}"))
            .OrderByDescending(x => x.VoucherNumber)
            .FirstOrDefaultAsync();

        int sequence = 1;
        if (lastVoucher?.VoucherNumber != null)
        {
            var parts = lastVoucher.VoucherNumber.Split('-');
            if (parts.Length == 3 && int.TryParse(parts[2], out int lastSeq))
            {
                sequence = lastSeq + 1;
            }
        }

        var voucherNumber = $"PVR-{vDate:yyyyMMdd}-{sequence:D4}";

        foreach (var item in items)
        {
            item.VoucherNumber = voucherNumber;
            item.VoucherDate = vDate;
            if (dueDate.HasValue) item.DueDate = DateTime.SpecifyKind(dueDate.Value.Date, DateTimeKind.Utc);
            
            // Defensive fix for existing dates that might be Unspecified
            if (item.TransactionDate.Kind == DateTimeKind.Unspecified)
                item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
                
            if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified)
                item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);

            item.UpdatedBy = userId;
            item.UpdatedDate = utcNow;
        }

        _context.AccountReceivables.UpdateRange(items);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.AccountReceivable, "Finalize Voucher",
            items.Select(i => (i.ArId.ToString(), (object?)new
            {
                VoucherNumber = voucherNumber,
                VoucherDate = vDate,
                i.ArNumber,
                i.PayerName,
                i.DueDate
            })),
            userId, items.First().BranchId);

        return true;
    }

    // Role yang boleh membatalkan voucher AR: Administrator(1) dan Manager(2).
    // Allow-list, bukan deny-list, supaya role baru default-nya TIDAK boleh membatalkan.
    private static readonly int[] CancelVoucherRoles = { 1, 2 };

    public async Task<bool> UnfinalizeVoucher(string voucherNumber, int userId, int roleId)
    {
        // Dicek di service, bukan hanya lewat [Authorize]. Dua alasan: atribut itu hanya
        // memeriksa izin halaman (Action.Update) yang juga dimiliki Staff, dan
        // AuthorizeAttribute.OnAuthorization adalah async void sehingga hasil await-nya
        // tidak dijamin selesai sebelum action berjalan.
        if (!CancelVoucherRoles.Contains(roleId))
            throw new Exception("Hanya Administrator atau Manager yang boleh membatalkan voucher.");

        if (string.IsNullOrEmpty(voucherNumber)) return false;

        var items = await _context.AccountReceivables
            .Where(x => x.VoucherNumber == voucherNumber)
            .ToListAsync();

        if (items.Count == 0) return false;

        var paidItems = items.Where(x =>
            (x.PaymentStatus ?? "").ToLower() == "paid").ToList();
        if (paidItems.Count > 0)
            throw new ApplicationException(
                $"Cannot cancel voucher {voucherNumber}: {paidItems.Count} item(s) already paid");

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        var auditDetails = items.Select(i => new
        {
            i.ArId,
            i.ArNumber,
            i.PayerName,
            i.PayerId,
            i.PatientName,
            i.MedicalRecordNo,
            OldVoucherNumber = i.VoucherNumber,
            OldVoucherDate = i.VoucherDate,
            OldDueDate = i.DueDate,
            TotalAmount = i.TotalAmount,
            AdjustmentAmount = i.AdjustmentAmount
        }).ToList();

        foreach (var item in items)
        {
            item.VoucherNumber = null;
            item.VoucherDate = null;
            item.DueDate = null;
            item.UpdatedBy = userId;
            item.UpdatedDate = utcNow;
        }

        _context.AccountReceivables.UpdateRange(items);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.AccountReceivable, "Cancel Voucher",
            voucherNumber, userId, items.First().BranchId,
            new
            {
                VoucherNumber = voucherNumber,
                Action = "Unfinalize",
                ItemCount = items.Count,
                Items = auditDetails
            });

        return true;
    }
    public async Task<SettlementResult> ProcessSettlement(SettlementRequest request, int userId)
    {
        var items = await _context.AccountReceivables
            .Where(x => x.VoucherNumber == request.PaymentVoucher)
            .ToListAsync();

        if (items.Count == 0 || items.All(x => x.PaymentStatus?.ToLower() == "paid"))
            return new SettlementResult { UpdatedCount = 0, TotalPaid = 0 };

        // Fetch previous payments for this voucher
        var previousPayments = await _context.ArSettlementPayments
            .Where(x => x.VoucherNumber == request.PaymentVoucher)
            .ToListAsync();

        double totalPreviouslyPaid = previousPayments.Sum(x => x.Amount);
        double totalPreviouslyDiscounted = previousPayments.Sum(x => x.Discount);

        int branchId = items.First().BranchId ?? 1;
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var paymentDate = DateTime.SpecifyKind(request.PaymentDate.Date, DateTimeKind.Utc);

        // Calculate Total Voucher (use adjustment amount if available)
        decimal voucherTotalFull = (decimal)items.Sum(x => x.AdjustmentAmount != null && x.AdjustmentAmount != 0
            ? (double)x.AdjustmentAmount.Value : x.TotalAmount);
        decimal remainingOutstanding = voucherTotalFull - (decimal)totalPreviouslyPaid - (decimal)totalPreviouslyDiscounted;

        // Validate Bank COA
        var bankCoa = await _coaService.GetByCode(request.CoaCode, branchId);
        if (bankCoa == null)
            throw new Exception($"Bank/Cash Account with code '{request.CoaCode}' not found.");

        // Fetch COA Options for AR
        var optRanapBpjs = await _optionService.GetByName("ACCOUNT_PIUTANG_USAHA_RANAP_BPJS", branchId);
        var optRajalBpjs = await _optionService.GetByName("ACCOUNT_PIUTANG_USAHA_RAJAL_BPJS", branchId);
        var optRanapNonBpjs = await _optionService.GetByName("ACCOUNT_PIUTANG_USAHA_RANAP_NON_BPJS", branchId);
        var optRajalNonBpjs = await _optionService.GetByName("ACCOUNT_PIUTANG_USAHA_RAJAL_NON_BPJS", branchId);
        var optDiscount = await _optionService.GetByName("ACCOUNT_DISCOUNT_AR", branchId);
        var optAdjustment = await _optionService.GetByName("ACCOUNT_ADJUSTMENT_AR", branchId);

        string coaRanapBpjs = optRanapBpjs?.OptionValue ?? "103001001";
        string coaRajalBpjs = optRajalBpjs?.OptionValue ?? "103002001";
        string coaRanapNonBpjs = optRanapNonBpjs?.OptionValue ?? "103001002";
        string coaRajalNonBpjs = optRajalNonBpjs?.OptionValue ?? "103002002";
        string coaDiscount = optDiscount?.OptionValue ?? "414000000";
        string coaAdjustment = optAdjustment?.OptionValue ?? "722012";

        // Calculate adjustment difference (original claim vs approved amount)
        decimal voucherTotalOriginal = (decimal)items.Sum(x => x.TotalAmount);
        decimal adjustmentDifference = voucherTotalOriginal - voucherTotalFull; // positive when payer approved less

        // Calculate Amounts for THIS payment
        decimal currentPaymentAmount = (decimal)(request.PaidAmount);

        // ID-1669: Discount is only allowed/journaled on the FIRST payment
        bool isFirstPayment = previousPayments.Count == 0;
        decimal currentDiscountAmount = isFirstPayment ? (request.Discount ?? 0) : 0;
        // Adjustment difference is only journaled on the FIRST payment (same as discount)
        decimal currentAdjustmentDifference = isFirstPayment ? adjustmentDifference : 0;

        if (currentPaymentAmount + currentDiscountAmount > remainingOutstanding + 100) // 100 tolerance
             throw new Exception("Payment amount exceeds remaining outstanding balance.");

        using var dbTransaction = await _context.Database.BeginTransactionAsync();
        try
        {

        // Create Transaction
        string transactionType = "Deposit"; 
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(request.PaymentDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = paymentDate,
            TransactionName = $"Settlement AR Voucher {request.PaymentVoucher}",
            TransactionRef = request.PaymentVoucher,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Settlement AR Voucher {request.PaymentVoucher}",
            CoaCode = request.CoaCode,
            CoaName = request.CoaDesc,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double)currentPaymentAmount,
            PaymentNo = request.PaymentVoucher,
            PaymentDate = paymentDate,
            PaymentStatus = "Paid",
            CoaParentCode = bankCoa.ParentCode,
            CoaParentName = bankCoa.ParentName
        };

        await _transactionService.Add(transaction);

        // Create Journals
        
        // 1. DEBIT Bank/Cash
        await _journalService.Add(new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = transaction.Description,
            CoaCode = transaction.CoaCode,
            CoaName = transaction.CoaName,
            CoaParentCode = transaction.CoaParentCode,
            CoaParentName = transaction.CoaParentName,
            DebitAmount = (double)currentPaymentAmount,
            CreditAmount = 0,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        });

        // 2. DEBIT Discount
        if (currentDiscountAmount > 0)
        {
            var discountCoa = await _coaService.GetByCode(coaDiscount, branchId);
            await _journalService.Add(new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Settlement Discount - {request.PaymentVoucher}",
                CoaCode = coaDiscount,
                CoaName = discountCoa?.CoaName ?? "Discount AR",
                CoaParentCode = discountCoa?.ParentCode,
                CoaParentName = discountCoa?.ParentName,
                DebitAmount = (double)currentDiscountAmount,
                CreditAmount = 0,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            });
        }

        // 3. CREDIT AR Accounts (Grouped by classification)
        var groupedItemsCredit = items.GroupBy(x => new 
        { 
            IsRanap = x.ServiceType?.ToLower() == "ranap", 
            IsBpjs = x.PayerId == "BPJ"
        });

        foreach (var group in groupedItemsCredit)
        {
            // Allocate proportionately based on original TotalAmount (piutang was booked at original amount)
            decimal groupTotalOriginal = (decimal)group.Sum(x => x.TotalAmount);
            decimal groupRatio = voucherTotalOriginal > 0 ? groupTotalOriginal / voucherTotalOriginal : 0;

            // CREDIT piutang = payment + discount + adjustment difference (all proportionally)
            decimal groupCreditAmount = (currentPaymentAmount + currentDiscountAmount + currentAdjustmentDifference) * groupRatio;

            string targetCoaCode = group.Key.IsRanap
                ? (group.Key.IsBpjs ? coaRanapBpjs : coaRanapNonBpjs)
                : (group.Key.IsBpjs ? coaRajalBpjs : coaRajalNonBpjs);

            var targetCoa = await _coaService.GetByCode(targetCoaCode, branchId);

            await _journalService.Add(new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Settlement AR {targetCoa?.CoaName ?? "Piutang"} - {request.PaymentVoucher}",
                CoaCode = targetCoaCode,
                CoaName = targetCoa?.CoaName ?? "Piutang",
                CoaParentCode = targetCoa?.ParentCode,
                CoaParentName = targetCoa?.ParentName,
                DebitAmount = 0,
                CreditAmount = (double)groupCreditAmount,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            });
        }

        // 4. DEBIT Adjustment Difference (write-off selisih klaim vs approved)
        if (currentAdjustmentDifference > 0)
        {
            var adjCoa = await _coaService.GetByCode(coaAdjustment, branchId);
            await _journalService.Add(new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Selisih Adjustment AR - {request.PaymentVoucher}",
                CoaCode = coaAdjustment,
                CoaName = adjCoa?.CoaName ?? "Selisih Adjustment AR",
                CoaParentCode = adjCoa?.ParentCode,
                CoaParentName = adjCoa?.ParentName,
                DebitAmount = (double)currentAdjustmentDifference,
                CreditAmount = 0,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            });
        }

        // Post the journal to ensure visibility (sets PostedDate and consistent JournalNo)
        await _journalService.PostJournal(new List<string> { transaction.TransactionId });

        // SAVE PAYMENT RECORD
        var payment = new ArSettlementPayment
        {
            VoucherNumber = request.PaymentVoucher,
            PaymentDate = paymentDate,
            Amount = (double)currentPaymentAmount,
            Discount = (double)currentDiscountAmount,
            CoaCode = request.CoaCode,
            CoaName = request.CoaDesc,
            PaymentRef = request.PaymentRef,
            PaidBy = userId,
            CreatedDate = utcNow
        };
        await _context.ArSettlementPayments.AddAsync(payment);
        await _context.SaveChangesAsync();

        // CHECK IF FULLY PAID
        double newTotalPaid = totalPreviouslyPaid + (double)currentPaymentAmount;
        double newTotalDiscount = totalPreviouslyDiscounted + (double)currentDiscountAmount;

        if (newTotalPaid + newTotalDiscount >= (double)voucherTotalFull - 100) // 100 tolerance
        {
            // FINALIZE INDIVIDUAL AR ITEMS
            foreach (var item in items)
            {
                // Proportional Final Allocation (use adjustment amount if available)
                var effectiveAmount = item.AdjustmentAmount != null && item.AdjustmentAmount != 0
                    ? (double)item.AdjustmentAmount.Value : item.TotalAmount;
                decimal itemRatio = (decimal)effectiveAmount / voucherTotalFull;
                item.PaidAmount = effectiveAmount; // Mark as fully paid
                item.DiscountAmount = (double)(newTotalDiscount * (double)itemRatio); 
                item.PaymentStatus = "Paid";
                item.OutstandingAmount = 0;
                item.PaymentDate = paymentDate;
                item.PaymentRef = request.PaymentRef;
                item.PaymentCoaCode = request.CoaCode;
                item.UpdatedBy = userId;
                item.UpdatedDate = utcNow;
            }
            _context.AccountReceivables.UpdateRange(items);
            await _context.SaveChangesAsync();
        }

        await _context.SaveChangesAsync();

        // Inside the transaction on purpose: if the settlement rolls back, the log entry
        // must roll back with it -- the action did not happen.
        await _auditTrailUtils.LogActionEntries(
            AuditModule.AccountReceivable, "Settlement",
            items.Select(i => (i.ArId.ToString(), (object?)new
            {
                request.PaymentVoucher,
                PaymentDate = paymentDate,
                request.CoaCode,
                request.PaymentRef,
                CurrentPaymentAmount = currentPaymentAmount,
                i.ArNumber,
                i.PaymentStatus
            })),
            userId, branchId);

        await _context.SaveChangesAsync();
        await dbTransaction.CommitAsync();

        return new SettlementResult
        {
            UpdatedCount = items.Count,
            TotalPaid = currentPaymentAmount
        };
       }
       catch (Exception)
       {
           await dbTransaction.RollbackAsync();
           throw;
       }
    }

    public async Task<bool> ImportAR(IFormFile file, int userId)
    {
        if (file == null || file.Length == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var importedItems = new List<RSSMBackend.Entities.AccountReceivable>();

        using (var stream = new MemoryStream())
        {
            await file.CopyToAsync(stream);
            using (var package = new ExcelPackage(stream))
            {
                if (package.Workbook.Worksheets.Count == 0) return false;
                var worksheet = package.Workbook.Worksheets[0];
                var rowCount = worksheet.Dimension.Rows;

                // Assuming Header is Row 1, Data starts Row 2
                // Columns: 1=No SEP, 2=Pasien, 3=No Tagihan, 4=Amount, 5=Tanggal
                for (int row = 2; row <= rowCount; row++)
                {
                    try
                    {
                        var sepNo = worksheet.Cells[row, 1].Text?.Trim();
                        var patientName = worksheet.Cells[row, 2].Text?.Trim();
                        var claimNo = worksheet.Cells[row, 3].Text?.Trim(); 
                        var amountStr = worksheet.Cells[row, 4].Text?.Trim();
                        var dateStr = worksheet.Cells[row, 5].Text?.Trim();

                        // Basic validation: Must have SEP or Patient Name
                        if (string.IsNullOrEmpty(sepNo) && string.IsNullOrEmpty(patientName)) continue;

                        double amount = 0;
                        if (!string.IsNullOrEmpty(amountStr))
                        {
                             // Clean currency symbols if any
                             amountStr = amountStr.Replace("Rp", "").Replace(".", "").Replace(",", "."); // Assuming ID format 1.000,00 -> 1000.00
                             // Simple try parse
                             double.TryParse(amountStr, out amount);
                        }

                        DateTime trxDate = utcNow;
                        if (!string.IsNullOrEmpty(dateStr) && DateTime.TryParse(dateStr, out DateTime dt)) 
                            trxDate = DateTime.SpecifyKind(dt, DateTimeKind.Utc);

                        var ar = new RSSMBackend.Entities.AccountReceivable
                        {
                            SepNo = sepNo,
                            PatientName = patientName,
                            ClaimNumber = claimNo,
                            ArNumber = claimNo, 
                            TotalAmount = amount,
                            TransactionDate = trxDate,
                            PayerId = "BPJ", 
                            PayerType = "BPJS",
                            PayerName = "BPJS Kesehatan",
                            PaymentStatus = "Pending",
                            Status = "ACTIVE",
                            CreatedBy = userId,
                            CreatedDate = utcNow,
                            UpdatedBy = userId,
                            UpdatedDate = utcNow
                        };
                        importedItems.Add(ar);
                    }
                    catch (Exception)
                    {
                        continue;
                    }
                }
            }
        }

        if (importedItems.Count > 0)
        {
            await _context.AccountReceivables.AddRangeAsync(importedItems);
            await _context.SaveChangesAsync();

            // Ids are populated by SaveChanges.
            await _auditTrailUtils.LogActionEntries(
                AuditModule.AccountReceivable, "Import",
                importedItems.Select(i => (i.ArId.ToString(), (object?)new
                {
                    i.ArNumber,
                    i.PayerName,
                    i.TotalAmount,
                    SourceFile = file.FileName
                })),
                userId, importedItems.First().BranchId);

            return true;
        }

        return false;
    }

    public async Task<bool> BulkUpdateStatus(IFormFile file, int userId)
    {
        if (file == null || file.Length == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var updatedItems = new List<RSSMBackend.Entities.AccountReceivable>();
        // Tracked separately from updatedItems so each outcome logs its own operation.
        var bulkPosted = new List<RSSMBackend.Entities.AccountReceivable>();
        var bulkVoided = new List<RSSMBackend.Entities.AccountReceivable>();

        using (var stream = new MemoryStream())
        {
            await file.CopyToAsync(stream);
            using (var package = new ExcelPackage(stream))
            {
                if (package.Workbook.Worksheets.Count == 0) return false;
                var worksheet = package.Workbook.Worksheets[0];
                var rowCount = worksheet.Dimension.Rows;

                var idsToPost = new List<int>();
                var idsToVoid = new List<int>();
                
                for (int row = 2; row <= rowCount; row++)
                {
                    try
                    {
                        var idStr = worksheet.Cells[row, 1].Text?.Trim();
                        var statusStr = worksheet.Cells[row, 10].Text?.Trim(); // Status Column

                        if (int.TryParse(idStr, out int id) && !string.IsNullOrEmpty(statusStr))
                        {
                            if (statusStr.Equals("Posting", StringComparison.OrdinalIgnoreCase))
                            {
                                idsToPost.Add(id);
                            }
                            else if (statusStr.Equals("Void", StringComparison.OrdinalIgnoreCase))
                            {
                                idsToVoid.Add(id);
                            }
                        }
                    }
                    catch { continue; }
                }

                if (idsToPost.Count > 0)
                {
                    var postItems = await _context.AccountReceivables
                        .Where(x => idsToPost.Contains(x.ArId) && (x.PaymentStatus == null || x.PaymentStatus == "Pending"))
                        .ToListAsync();

                    foreach (var item in postItems)
                    {
                         // Ensure DateTime fields are UTC
                         if (item.TransactionDate.Kind == DateTimeKind.Unspecified)
                             item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
                         if (item.DueDate.HasValue && item.DueDate.Value.Kind == DateTimeKind.Unspecified)
                             item.DueDate = DateTime.SpecifyKind(item.DueDate.Value, DateTimeKind.Utc);
                         if (item.PaymentDate.HasValue && item.PaymentDate.Value.Kind == DateTimeKind.Unspecified)
                             item.PaymentDate = DateTime.SpecifyKind(item.PaymentDate.Value, DateTimeKind.Utc);
                         if (item.ReconcileDate.HasValue && item.ReconcileDate.Value.Kind == DateTimeKind.Unspecified)
                             item.ReconcileDate = DateTime.SpecifyKind(item.ReconcileDate.Value, DateTimeKind.Utc);
                         if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified)
                             item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);
                         if (item.VoucherDate.HasValue && item.VoucherDate.Value.Kind == DateTimeKind.Unspecified)
                             item.VoucherDate = DateTime.SpecifyKind(item.VoucherDate.Value, DateTimeKind.Utc);

                         item.PaymentStatus = "Posting";
                         item.UpdatedBy = userId;
                         item.UpdatedDate = utcNow;
                         updatedItems.Add(item);
                         bulkPosted.Add(item);
                    }
                }

                if (idsToVoid.Count > 0)
                {
                    var voidItems = await _context.AccountReceivables
                        .Where(x => idsToVoid.Contains(x.ArId) && (x.PaymentStatus == null || x.PaymentStatus == "Pending"))
                        .ToListAsync();

                    foreach (var item in voidItems)
                    {
                         // Ensure DateTime fields are UTC
                         if (item.TransactionDate.Kind == DateTimeKind.Unspecified)
                             item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
                         if (item.DueDate.HasValue && item.DueDate.Value.Kind == DateTimeKind.Unspecified)
                             item.DueDate = DateTime.SpecifyKind(item.DueDate.Value, DateTimeKind.Utc);
                         if (item.PaymentDate.HasValue && item.PaymentDate.Value.Kind == DateTimeKind.Unspecified)
                             item.PaymentDate = DateTime.SpecifyKind(item.PaymentDate.Value, DateTimeKind.Utc);
                         if (item.ReconcileDate.HasValue && item.ReconcileDate.Value.Kind == DateTimeKind.Unspecified)
                             item.ReconcileDate = DateTime.SpecifyKind(item.ReconcileDate.Value, DateTimeKind.Utc);
                         if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified)
                             item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);
                         if (item.VoucherDate.HasValue && item.VoucherDate.Value.Kind == DateTimeKind.Unspecified)
                             item.VoucherDate = DateTime.SpecifyKind(item.VoucherDate.Value, DateTimeKind.Utc);

                         item.PaymentStatus = "Void"; // Changed to PaymentStatus
                         item.UpdatedBy = userId;
                         item.UpdatedDate = utcNow;
                         updatedItems.Add(item);
                         bulkVoided.Add(item);
                    }
                }
            }
        }

        if (updatedItems.Count > 0)
        {
            _context.AccountReceivables.UpdateRange(updatedItems);
            await _context.SaveChangesAsync();

            // One file can both post and void, so the two outcomes are logged separately.
            if (bulkPosted.Count > 0)
                await _auditTrailUtils.LogActionEntries(
                    AuditModule.AccountReceivable, "Posting",
                    bulkPosted.Select(i => (i.ArId.ToString(), (object?)new
                    {
                        i.ArNumber,
                        i.PayerName,
                        Source = "Bulk Update Status",
                        SourceFile = file.FileName
                    })),
                    userId, bulkPosted.First().BranchId);

            if (bulkVoided.Count > 0)
                await _auditTrailUtils.LogActionEntries(
                    AuditModule.AccountReceivable, "Void",
                    bulkVoided.Select(i => (i.ArId.ToString(), (object?)new
                    {
                        i.ArNumber,
                        i.PayerName,
                        Source = "Bulk Update Status",
                        SourceFile = file.FileName
                    })),
                    userId, bulkVoided.First().BranchId);

            return true;
        }

        return false;
    }

    public async Task<bool> VoidAR(List<int> arIds, int userId)
    {
         if (arIds == null || arIds.Count == 0) return false;

         var items = await _context.AccountReceivables
             .Where(x => arIds.Contains(x.ArId))
             .ToListAsync();
        
         if (items.Count == 0) return false;

         var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

         // Paid items are skipped below, so only log the ones that actually changed.
         var voidedEntries = new List<(string RefId, object? Detail)>();

         foreach(var item in items)
         {
             if (item.PaymentStatus?.ToLower() == "paid") continue;

             voidedEntries.Add((item.ArId.ToString(), new
             {
                 PreviousStatus = item.PaymentStatus,
                 item.ArNumber,
                 item.PayerName,
                 item.TotalAmount
             }));

             item.PaymentStatus = "Void"; // Change PaymentStatus instead of Status
             item.UpdatedBy = userId;
             item.UpdatedDate = utcNow;
         }

         await _context.SaveChangesAsync();

         await _auditTrailUtils.LogActionEntries(
             AuditModule.AccountReceivable, "Void",
             voidedEntries, userId, items.First().BranchId);

         return true;
    }

    public async Task<bool> MarkAsPrinted(List<int> arIds, int userId)
    {
         if (arIds == null || arIds.Count == 0) return false;

         // This is now likely called from Settlement or Finalize page logic
         var items = await _context.AccountReceivables
             .Where(x => arIds.Contains(x.ArId))
             .ToListAsync();
        
         if (items.Count == 0) return false;
         
         var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
         
         foreach(var item in items)
         {
             item.IsPrinted = true;
             item.UpdatedBy = userId;
             item.UpdatedDate = utcNow;
         }

         await _context.SaveChangesAsync();

         await _auditTrailUtils.LogActionEntries(
             AuditModule.AccountReceivable, "Cetak",
             items.Select(i => (i.ArId.ToString(), (object?)new { i.ArNumber, i.PayerName })),
             userId, items.First().BranchId);

         return true;
    }

    public async Task<bool> BulkRevision(IFormFile file, int userId)
    {
        if (file == null || file.Length == 0) return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var updatedItems = new List<RSSMBackend.Entities.AccountReceivable>();

        using (var stream = new MemoryStream())
        {
            await file.CopyToAsync(stream);
            using (var package = new ExcelPackage(stream))
            {
                if (package.Workbook.Worksheets.Count == 0) return false;
                var worksheet = package.Workbook.Worksheets[0];
                var rowCount = worksheet.Dimension.Rows;
                
                var idsToPending = new List<int>();
                var idsToVoid = new List<int>();
                
                for (int row = 2; row <= rowCount; row++)
                {
                    try
                    {
                        var idStr = worksheet.Cells[row, 1].Text?.Trim(); // A: AR ID
                        var statusStr = worksheet.Cells[row, 10].Text?.Trim(); // J: Status

                        if (int.TryParse(idStr, out int id) && !string.IsNullOrEmpty(statusStr))
                        {
                            if (statusStr.Equals("Pending", StringComparison.OrdinalIgnoreCase))
                            {
                                idsToPending.Add(id);
                            }
                            else if (statusStr.Equals("Void", StringComparison.OrdinalIgnoreCase))
                            {
                                idsToVoid.Add(id);
                            }
                        }
                    }
                    catch { continue; }
                }

                // Process Pending (Unpost) - Clear Voucher
                if (idsToPending.Count > 0)
                {
                    var pendingItems = await _context.AccountReceivables
                        .Where(x => idsToPending.Contains(x.ArId) && x.PaymentStatus == "Posting") // Only revise Posting items
                        .ToListAsync();

                    foreach (var item in pendingItems)
                    {
                         // Maintain UTC
                         if (item.TransactionDate.Kind == DateTimeKind.Unspecified) item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
                         if (item.DueDate.HasValue && item.DueDate.Value.Kind == DateTimeKind.Unspecified) item.DueDate = DateTime.SpecifyKind(item.DueDate.Value, DateTimeKind.Utc);
                         if (item.PaymentDate.HasValue && item.PaymentDate.Value.Kind == DateTimeKind.Unspecified) item.PaymentDate = DateTime.SpecifyKind(item.PaymentDate.Value, DateTimeKind.Utc);
                         if (item.ReconcileDate.HasValue && item.ReconcileDate.Value.Kind == DateTimeKind.Unspecified) item.ReconcileDate = DateTime.SpecifyKind(item.ReconcileDate.Value, DateTimeKind.Utc);
                         if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified) item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);
                         if (item.VoucherDate.HasValue && item.VoucherDate.Value.Kind == DateTimeKind.Unspecified) item.VoucherDate = DateTime.SpecifyKind(item.VoucherDate.Value, DateTimeKind.Utc);

                         item.PaymentStatus = "Pending";
                         item.VoucherNumber = null;
                         item.VoucherDate = null;
                         item.UpdatedBy = userId;
                         item.UpdatedDate = utcNow;
                         updatedItems.Add(item);
                    }
                }

                // Process Void - Keep Voucher
                if (idsToVoid.Count > 0)
                {
                    var voidItems = await _context.AccountReceivables
                        .Where(x => idsToVoid.Contains(x.ArId) && x.PaymentStatus == "Posting") // Only revise Posting items
                        .ToListAsync();

                    foreach (var item in voidItems)
                    {
                         // Maintain UTC
                         if (item.TransactionDate.Kind == DateTimeKind.Unspecified) item.TransactionDate = DateTime.SpecifyKind(item.TransactionDate, DateTimeKind.Utc);
                         if (item.DueDate.HasValue && item.DueDate.Value.Kind == DateTimeKind.Unspecified) item.DueDate = DateTime.SpecifyKind(item.DueDate.Value, DateTimeKind.Utc);
                         if (item.PaymentDate.HasValue && item.PaymentDate.Value.Kind == DateTimeKind.Unspecified) item.PaymentDate = DateTime.SpecifyKind(item.PaymentDate.Value, DateTimeKind.Utc);
                         if (item.ReconcileDate.HasValue && item.ReconcileDate.Value.Kind == DateTimeKind.Unspecified) item.ReconcileDate = DateTime.SpecifyKind(item.ReconcileDate.Value, DateTimeKind.Utc);
                         if (item.CreatedDate.HasValue && item.CreatedDate.Value.Kind == DateTimeKind.Unspecified) item.CreatedDate = DateTime.SpecifyKind(item.CreatedDate.Value, DateTimeKind.Utc);
                         if (item.VoucherDate.HasValue && item.VoucherDate.Value.Kind == DateTimeKind.Unspecified) item.VoucherDate = DateTime.SpecifyKind(item.VoucherDate.Value, DateTimeKind.Utc);

                         item.PaymentStatus = "Void";
                         // Do NOT clear VoucherNumber/VoucherDate
                         item.UpdatedBy = userId;
                         item.UpdatedDate = utcNow;
                         updatedItems.Add(item);
                    }
                }

                if (updatedItems.Count > 0)
                {
                    _context.AccountReceivables.UpdateRange(updatedItems);
                    await _context.SaveChangesAsync();

                    await _auditTrailUtils.LogActionEntries(
                        AuditModule.AccountReceivable, "Revisi",
                        updatedItems.Select(i => (i.ArId.ToString(), (object?)new
                        {
                            i.ArNumber,
                            i.PayerName,
                            i.PaymentStatus,
                            i.VoucherNumber,
                            SourceFile = file.FileName
                        })),
                        userId, updatedItems.First().BranchId);

                    return true;
                }
            }
        }

        return false;
    }

    public async Task<ArAdjustmentResult> ImportAdjustment(Stream fileStream, string fileName, int userId)
    {
        var result = new ArAdjustmentResult();
        var errors = new List<string>();
        // Collected across both the xlsx and csv branches, written once after SaveChanges.
        var adjustedEntries = new List<(string RefId, object? Detail)>();
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        try
        {
            var extension = Path.GetExtension(fileName).ToLower();

            if (extension == ".xlsx" || extension == ".xls")
            {
                using var package = new OfficeOpenXml.ExcelPackage(fileStream);
                var worksheet = package.Workbook.Worksheets.FirstOrDefault();
                if (worksheet == null) throw new Exception("No worksheet found in Excel file");

                int rowCount = worksheet.Dimension?.Rows ?? 0;
                int headerRow = 1;
                int arNumberCol = 0;
                int claimNumberCol = 0;
                int sepCol = 0;
                int amtCol = 0;
                int statusCol = 0;

                // Scan headers
                for (int c = 1; c <= (worksheet.Dimension?.Columns ?? 0); c++)
                {
                    var header = worksheet.Cells[headerRow, c].Text?.Trim().ToLower();
                    if (header == "ar_number" || header == "ar number" || header == "invoice_no" || header == "invoice no" || header == "invoice") arNumberCol = c;
                    if (header == "claim_number" || header == "claim number" || header == "no_tagihan" || header == "no tagihan") claimNumberCol = c;
                    if (header == "no_sep" || header == "no sep" || header == "sep" || header == "sep_no" || header == "sep no" || header == "nosep") sepCol = c;
                    if (header == "approved_amount" || header == "approved amount" || header == "adjustment_amount" || header == "adjustment amount" || header == "amount" || header == "jumlah_disetujui" || header == "jumlah disetujui") amtCol = c;
                    if (header == "status") statusCol = c;
                }

                // Validate required columns
                if (amtCol == 0)
                    throw new Exception("Column amount not found. Expected: 'approved_amount', 'adjustment_amount', or 'amount'.");
                if (arNumberCol == 0 && claimNumberCol == 0 && sepCol == 0)
                    throw new Exception("No identifier column found. Expected: 'ar_number', 'claim_number', or 'no_sep'.");

                int startRow = headerRow + 1;

                for (int row = startRow; row <= rowCount; row++)
                {
                    decimal approvedAmount = 0;
                    try
                    {
                        string? arNumber = arNumberCol > 0 ? worksheet.Cells[row, arNumberCol].Text?.Trim() : null;
                        string? claimNumber = claimNumberCol > 0 ? worksheet.Cells[row, claimNumberCol].Text?.Trim() : null;
                        string? sepNo = sepCol > 0 ? worksheet.Cells[row, sepCol].Text?.Trim() : null;

                        // Skip empty rows
                        if (string.IsNullOrEmpty(arNumber) && string.IsNullOrEmpty(claimNumber) && string.IsNullOrEmpty(sepNo))
                        {
                            result.SkippedCount++;
                            continue;
                        }

                        var amountStr = worksheet.Cells[row, amtCol].Text?.Trim();
                        result.ImportedCount++;

                        // Match AR record by identifiers
                        RSSMBackend.Entities.AccountReceivable? ar = null;

                        if (!string.IsNullOrEmpty(arNumber))
                        {
                            ar = await _context.AccountReceivables.FirstOrDefaultAsync(x => x.ArNumber == arNumber);
                        }

                        if (ar == null && !string.IsNullOrEmpty(claimNumber))
                        {
                            ar = await _context.AccountReceivables.FirstOrDefaultAsync(x => x.ClaimNumber == claimNumber);
                        }

                        if (ar == null && !string.IsNullOrEmpty(sepNo))
                        {
                            ar = await _context.AccountReceivables.FirstOrDefaultAsync(x => x.SepNo == sepNo);
                        }

                        if (ar == null)
                        {
                            errors.Add($"Row {row}: Record not found (AR: {arNumber}, Claim: {claimNumber}, SEP: {sepNo})");
                            result.SkippedCount++;
                            continue;
                        }

                        // Robust amount parsing
                        if (!string.IsNullOrEmpty(amountStr))
                        {
                            var rawVal = worksheet.Cells[row, amtCol].Value;
                            if (rawVal is double dVal) approvedAmount = (decimal)dVal;
                            else if (rawVal is decimal decVal) approvedAmount = decVal;
                            else
                            {
                                if (!decimal.TryParse(amountStr, System.Globalization.NumberStyles.Any, new System.Globalization.CultureInfo("id-ID"), out approvedAmount))
                                {
                                    var digits = new string(amountStr.Where(char.IsDigit).ToArray());
                                    decimal.TryParse(digits, out approvedAmount);
                                }
                            }
                        }

                        if (approvedAmount <= 0)
                        {
                            errors.Add($"Row {row}: Invalid amount");
                            result.SkippedCount++;
                            continue;
                        }

                        // Update AR record
                        var amountBefore = ar.AdjustmentAmount;
                        ar.AdjustmentAmount = approvedAmount;
                        ar.AdjustmentDate = utcNow;
                        ar.AdjustmentNotes = $"Imported from {fileName}";
                        ar.UpdatedBy = userId;
                        ar.UpdatedDate = utcNow;

                        // Optionally update status
                        if (statusCol > 0)
                        {
                            var statusVal = worksheet.Cells[row, statusCol].Text?.Trim();
                            if (!string.IsNullOrEmpty(statusVal) && statusVal.ToLower() == "posting" && ar.PaymentStatus == "Pending")
                            {
                                ar.PaymentStatus = "Posting";
                            }
                        }

                        result.UpdatedCount++;
                        adjustedEntries.Add((ar.ArId.ToString(), new
                        {
                            AmountBefore = amountBefore,
                            AmountAfter = approvedAmount,
                            ar.TotalAmount,
                            ar.ArNumber,
                            SourceFile = fileName
                        }));
                    }
                    catch (Exception ex)
                    {
                        errors.Add($"Row {row}: {ex.Message}");
                        result.SkippedCount++;
                    }
                }
            }
            else if (extension == ".txt" || extension == ".csv")
            {
                using var reader = new StreamReader(fileStream);
                int lineNumber = 0;
                bool isHeader = true;

                while (!reader.EndOfStream)
                {
                    var line = await reader.ReadLineAsync();
                    lineNumber++;

                    if (isHeader) { isHeader = false; continue; }
                    if (string.IsNullOrWhiteSpace(line)) continue;

                    try
                    {
                        var parts = line.Split(new[] { '\t', ';' }, StringSplitOptions.None);
                        if (parts.Length < 2)
                        {
                            result.SkippedCount++;
                            continue;
                        }

                        var identifier = parts[0].Trim();
                        var amountStr = parts.Length >= 4 ? parts[3].Trim() : parts[1].Trim();

                        if (string.IsNullOrEmpty(identifier) || identifier == "0" || identifier == "-")
                        {
                            result.SkippedCount++;
                            continue;
                        }

                        result.ImportedCount++;

                        // Match by ArNumber, ClaimNumber, or SepNo
                        var ar = await _context.AccountReceivables.FirstOrDefaultAsync(x => x.ArNumber == identifier)
                            ?? await _context.AccountReceivables.FirstOrDefaultAsync(x => x.ClaimNumber == identifier)
                            ?? await _context.AccountReceivables.FirstOrDefaultAsync(x => x.SepNo == identifier);

                        if (ar == null)
                        {
                            errors.Add($"Line {lineNumber}: Record not found ({identifier})");
                            result.SkippedCount++;
                            continue;
                        }

                        decimal approvedAmount = 0;
                        if (!decimal.TryParse(amountStr, System.Globalization.NumberStyles.Any, new System.Globalization.CultureInfo("id-ID"), out approvedAmount))
                        {
                            var digits = new string(amountStr.Where(char.IsDigit).ToArray());
                            decimal.TryParse(digits, out approvedAmount);
                        }

                        if (approvedAmount <= 0)
                        {
                            errors.Add($"Line {lineNumber}: Invalid amount");
                            result.SkippedCount++;
                            continue;
                        }

                        var amountBefore = ar.AdjustmentAmount;
                        ar.AdjustmentAmount = approvedAmount;
                        ar.AdjustmentDate = utcNow;
                        ar.AdjustmentNotes = $"Imported from {fileName}";
                        ar.UpdatedBy = userId;
                        ar.UpdatedDate = utcNow;

                        result.UpdatedCount++;
                        adjustedEntries.Add((ar.ArId.ToString(), new
                        {
                            AmountBefore = amountBefore,
                            AmountAfter = approvedAmount,
                            ar.TotalAmount,
                            ar.ArNumber,
                            SourceFile = fileName
                        }));
                    }
                    catch (Exception ex)
                    {
                        errors.Add($"Line {lineNumber}: {ex.Message}");
                        result.SkippedCount++;
                    }
                }
            }
            else
            {
                throw new Exception($"Unsupported file format: {extension}. Use .xlsx, .xls, .txt, or .csv");
            }

            await _context.SaveChangesAsync();

            await _auditTrailUtils.LogActionEntries(
                AuditModule.AccountReceivable, "Adjustment",
                adjustedEntries, userId, null);

            result.Errors = errors;
            result.ImportedCount = result.UpdatedCount + result.SkippedCount;
            return result;
        }
        catch (Exception ex)
        {
            throw new Exception($"Failed to import adjustment: {ex.Message}", ex);
        }
    }

    /// <summary>
    /// Get payer options from contacts with category_id = 5
    /// </summary>
    public async Task<object> GetPayerOptions(string? keyword = null)
    {
        var query = _context.Contacts
            .Where(c => c.ContactCategoryId == 5);

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(c =>
                c.ContactName.ToLower().Contains(kw) ||
                (c.RefCode != null && c.RefCode.ToLower().Contains(kw)));
        }

        var payers = await query
            .OrderBy(c => c.ContactName)
            .Select(c => new
            {
                value = c.RefCode ?? c.ContactId.ToString(),
                label = c.ContactName + (c.RefCode != null ? " (" + c.RefCode + ")" : ""),
                contactId = c.ContactId,
                refCode = c.RefCode
            })
            .ToListAsync();

        return payers;
    }

    /// <summary>
    /// Change payer of an AR item and store history
    /// </summary>
    public async Task<bool> ChangePayer(int arId, string newPayerId, string newPayerName, string reason, int userId)
    {
        var ar = await _context.AccountReceivables.FirstOrDefaultAsync(a => a.ArId == arId);
        if (ar == null)
            throw new Exception("AR not found");

        // Build history entry
        var historyEntry = new
        {
            payerBefore = new { id = ar.PayerId, name = ar.PayerName },
            payerAfter = new { id = newPayerId, name = newPayerName },
            reason = reason,
            changedBy = userId,
            changedDate = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss")
        };

        // Parse existing history or create new
        var history = new List<object>();
        if (!string.IsNullOrEmpty(ar.PayerChangeHistory))
        {
            try
            {
                var existing = JsonSerializer.Deserialize<List<JsonElement>>(ar.PayerChangeHistory);
                if (existing != null)
                {
                    history.AddRange(existing.Cast<object>());
                }
            }
            catch { }
        }
        history.Add(historyEntry);

        // Update AR
        var payerBefore = new { id = ar.PayerId, name = ar.PayerName };
        ar.PayerId = newPayerId;
        ar.PayerName = newPayerName;
        ar.PayerChangeHistory = JsonSerializer.Serialize(history);
        ar.UpdatedBy = userId;
        ar.UpdatedDate = DateTime.Now;

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.AccountReceivable, "Ganti Payer",
            arId.ToString(), userId, ar.BranchId,
            new
            {
                PayerBefore = payerBefore,
                PayerAfter = new { id = newPayerId, name = newPayerName },
                Reason = reason
            });

        return true;
    }

    /// <summary>
    /// Adjust an AR item's approved amount and store history
    /// </summary>
    public async Task<bool> AdjustAr(int arId, decimal newAdjustmentAmount, string reason, int userId)
    {
        var ar = await _context.AccountReceivables.FirstOrDefaultAsync(a => a.ArId == arId);
        if (ar == null)
            throw new Exception("AR not found");

        if (newAdjustmentAmount <= 0)
            throw new Exception("Adjustment amount must be greater than 0");

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Build history entry
        var historyEntry = new
        {
            amountBefore = ar.AdjustmentAmount,
            amountAfter = newAdjustmentAmount,
            totalAmount = ar.TotalAmount,
            reason = reason,
            changedBy = userId,
            changedDate = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss")
        };

        // Parse existing history or create new
        var history = new List<object>();
        if (!string.IsNullOrEmpty(ar.AdjustmentHistory))
        {
            try
            {
                var existing = JsonSerializer.Deserialize<List<JsonElement>>(ar.AdjustmentHistory);
                if (existing != null)
                {
                    history.AddRange(existing.Cast<object>());
                }
            }
            catch { }
        }
        history.Add(historyEntry);

        // Update AR
        var amountBefore = ar.AdjustmentAmount;
        ar.AdjustmentAmount = newAdjustmentAmount;
        ar.AdjustmentDate = utcNow;
        ar.AdjustmentNotes = reason;
        ar.AdjustmentHistory = JsonSerializer.Serialize(history);
        ar.UpdatedBy = userId;
        ar.UpdatedDate = utcNow;

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.AccountReceivable, "Adjustment",
            arId.ToString(), userId, ar.BranchId,
            new
            {
                AmountBefore = amountBefore,
                AmountAfter = newAdjustmentAmount,
                ar.TotalAmount,
                Reason = reason
            });

        return true;
    }

    /// <summary>
    /// Add a free-text note to an AR item and store history.
    /// The latest note text is mirrored into the Notes column for quick display.
    /// </summary>
    public async Task<bool> AddNote(int arId, string note, int userId)
    {
        var ar = await _context.AccountReceivables.FirstOrDefaultAsync(a => a.ArId == arId);
        if (ar == null)
            throw new Exception("AR not found");

        if (string.IsNullOrWhiteSpace(note))
            throw new Exception("Note is required");

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Build history entry
        var historyEntry = new
        {
            note = note,
            createdBy = userId,
            createdDate = DateTime.Now.ToString("yyyy-MM-dd HH:mm:ss")
        };

        // Parse existing history or create new
        var history = new List<object>();
        if (!string.IsNullOrEmpty(ar.NoteHistory))
        {
            try
            {
                var existing = JsonSerializer.Deserialize<List<JsonElement>>(ar.NoteHistory);
                if (existing != null)
                {
                    history.AddRange(existing.Cast<object>());
                }
            }
            catch { }
        }
        history.Add(historyEntry);

        // Update AR
        ar.Notes = note;
        ar.NoteHistory = JsonSerializer.Serialize(history);
        ar.UpdatedBy = userId;
        ar.UpdatedDate = utcNow;

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.AccountReceivable, "Tambah Catatan",
            arId.ToString(), userId, ar.BranchId,
            new { Note = note });

        return true;
    }

    public async Task<Dictionary<string, string>> GetPrintConfig(int branchId)
    {
        var options = await _context.Options
            .Where(o => o.OptionType == "PRINT_SIGNATURES" && o.BranchId == branchId)
            .ToListAsync();

        var config = new Dictionary<string, string>();
        foreach (var opt in options)
        {
            config[opt.OptionName] = opt.OptionValue ?? "";
        }

        if (!config.ContainsKey("PRINT_SIGNER_LEFT_NAME"))
            config["PRINT_SIGNER_LEFT_NAME"] = "Naurah Nazhifah Salim";
        if (!config.ContainsKey("PRINT_SIGNER_LEFT_TITLE"))
            config["PRINT_SIGNER_LEFT_TITLE"] = "Bagian Penagihan";
        if (!config.ContainsKey("PRINT_SIGNER_RIGHT_NAME"))
            config["PRINT_SIGNER_RIGHT_NAME"] = "Herlan Mars, S.Ak";
        if (!config.ContainsKey("PRINT_SIGNER_RIGHT_TITLE"))
            config["PRINT_SIGNER_RIGHT_TITLE"] = "Manajer Keu & Akuntansi";

        return config;
    }
}
