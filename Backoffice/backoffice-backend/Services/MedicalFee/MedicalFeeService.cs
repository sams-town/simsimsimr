using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using Microsoft.EntityFrameworkCore;

using RSSMBackend.Services;
using Microsoft.Extensions.Logging;
using TDPBackend.Helpers;

public class MedicalFeeService : IMedicalFeeService
{
    private readonly RSSMDbContext _context;
    private readonly IOptionService _optionService;
    private readonly ITransactionService _transactionService;
    private readonly IJournalService _journalService;
    private readonly ICOAService _coaService;
    private readonly IHttpContextAccessor _httpContextAccessor;
    private readonly IConfiguration _configuration; // Added for IConfiguration
    private readonly ILogger<MedicalFeeService> _logger;
    private readonly AuditTrailUtils _auditTrailUtils;
    private readonly User? _user;

    public MedicalFeeService(
        RSSMDbContext context,
        IOptionService optionService,
        ITransactionService transactionService,
        IJournalService journalService,
        ICOAService coaService,
        IHttpContextAccessor httpContextAccessor, // Added parameter
        ILogger<MedicalFeeService> logger,
        AuditTrailUtils auditTrailUtils,
        IConfiguration configuration) // Added parameter
    {
        _context = context;
        _optionService = optionService;
        _transactionService = transactionService;
        _journalService = journalService;
        _coaService = coaService;
        _httpContextAccessor = httpContextAccessor; // Added assignment
        _logger = logger;
        _auditTrailUtils = auditTrailUtils;
        _configuration = configuration; // Added assignment
        _user = (User?)_httpContextAccessor.HttpContext?.Items["User"]; // Added assignment
    }

    // Branch for the activity log. _user is null when the request had no authenticated user.
    private int? AuditBranchId => _user?.BranchId;

    public async Task<PagedResult<MedicalFeeResponse>> GetList(
        string? keyword,
        string? doctorName,
        string? doctorId,
        string? patientType,
        string? payerCode,
        string? status,
        int? periodMonth,
        int? periodYear,
        DateTime? procedureDateFrom,
        DateTime? procedureDateTo,
        string? medicalProcedure,
        int page,
        int pageSize,
        string? orderBy,
        string? orderType)
    {
        IQueryable<MedicalFee> query = _context.MedicalFees;

        // COMBINED: Filter by keyword (searches multiple fields)
        if (!string.IsNullOrEmpty(keyword))
        {
            var searchTerm = keyword.ToLower();
            query = query.Where(x =>
                (x.DoctorName != null && x.DoctorName.ToLower().Contains(searchTerm)) ||
                (x.PaymentRef != null && x.PaymentRef.ToLower().Contains(searchTerm)) ||
                (x.BatchNumber != null && x.BatchNumber.ToLower().Contains(searchTerm)) ||
                (x.ProcedureName != null && x.ProcedureName.ToLower().Contains(searchTerm)) ||
                (x.VisitNo != null && x.VisitNo.ToLower().Contains(searchTerm)) ||
                (x.SepNo != null && x.SepNo.ToLower().Contains(searchTerm))
            );
        }

         if (!string.IsNullOrEmpty(doctorId))
        {
            query = query.Where(x => x.DoctorCode != null && x.DoctorCode.ToLower().Contains(doctorId.ToLower()));
        }
        else if (!string.IsNullOrEmpty(doctorName))
        {
            query = query.Where(x => x.DoctorName != null && x.DoctorName.ToLower().Contains(doctorName.ToLower()));
        }

        if (!string.IsNullOrEmpty(patientType))
        {
            query = query.Where(x => x.PatientType != null && x.PatientType.ToLower() == patientType.ToLower());
        }

        if (!string.IsNullOrEmpty(payerCode))
        {
            var payerCodes = payerCode.Split(',', StringSplitOptions.RemoveEmptyEntries)
                                      .Select(p => p.Trim().ToLower())
                                      .ToList();
            if (payerCodes.Count > 1)
            {
                query = query.Where(x => x.PayerCode != null && payerCodes.Contains(x.PayerCode.ToLower()));
            }
            else
            {
                query = query.Where(x => x.PayerCode != null && x.PayerCode.ToLower() == payerCode.ToLower());
            }
        }

        if (!string.IsNullOrEmpty(status))
        {
            // If filtering for "Posting", user wants to see "Unpaid" items (as per requirement)
            if (status.ToLower() == "posting")
            {
                // Show Posting items AND Vouchered Items (Unpaid)
                // User requirement: "keluarkan juga yg sudah punya voucher code"
                query = query.Where(x => x.Status != null && (x.Status.ToLower() == "posting" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null)));
            }
            else
            {
                query = query.Where(x => x.Status != null && x.Status.ToLower() == status.ToLower());
            }
        }
        
        if (periodMonth.HasValue)
        {
            query = query.Where(x => x.PeriodMonth == periodMonth.Value);
        }

        if (periodYear.HasValue)
        {
            query = query.Where(x => x.PeriodYear == periodYear.Value);
        }

        if (procedureDateFrom.HasValue)
        {
            var fromDate = DateTime.SpecifyKind(procedureDateFrom.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.ProcedureDate >= fromDate);
        }

        if (procedureDateTo.HasValue)
        {
            var toDate = DateTime.SpecifyKind(procedureDateTo.Value.Date.AddDays(1), DateTimeKind.Utc);
            query = query.Where(x => x.ProcedureDate < toDate);
        }

        // Filter by medical procedure (Rawat Jalan / Rawat Inap)
        if (!string.IsNullOrEmpty(medicalProcedure))
        {
            query = query.Where(x => x.MedicalProcedure != null && x.MedicalProcedure.ToLower().Contains(medicalProcedure.ToLower()));
        }

        orderBy = string.IsNullOrEmpty(orderBy) ? "doctorname" : orderBy.ToLower();
        orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

        query = orderBy switch
        {
            "doctorname" => orderType == "asc" ? query.OrderBy(x => x.DoctorName) : query.OrderByDescending(x => x.DoctorName),
            "totalfee" => orderType == "asc" ? query.OrderBy(x => x.TotalFee) : query.OrderByDescending(x => x.TotalFee),
            "period" => orderType == "asc" ? query.OrderBy(x => x.PeriodYear).ThenBy(x => x.PeriodMonth) : query.OrderByDescending(x => x.PeriodYear).ThenByDescending(x => x.PeriodMonth),
            "proceduredate" => orderType == "asc" ? query.OrderBy(x => x.ProcedureDate) : query.OrderByDescending(x => x.ProcedureDate),
            "createddate" => orderType == "asc" ? query.OrderBy(x => x.CreatedDate) : query.OrderByDescending(x => x.CreatedDate),
            _ => query.OrderByDescending(x => x.CreatedDate)
        };

        int total = await query.CountAsync();

        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.ToListAsync();

        // Get doctor codes from Contact table (separate query to avoid join issues)
      

        var result = data.Select(x => new MedicalFeeResponse
        {
            MedicalFeeId = x.MedicalFeeId,
            DoctorId = x.DoctorId,
            DoctorCode = x.DoctorCode,
            DoctorName = x.DoctorName,
            PeriodMonth = x.PeriodMonth,
            PeriodYear = x.PeriodYear,
            Period = x.PeriodMonth.HasValue && x.PeriodYear.HasValue 
                ? $"{x.PeriodMonth:00}/{x.PeriodYear}" 
                : "-",
            PatientName = x.PatientName,
            MedicalRecordNo = x.MedicalRecordNo,
            PatientType = x.PatientType,
            PayerCode = x.PayerCode,
            PayerName = x.PayerName,
            AdjustmentAmount = x.AdjustmentAmount, // Map AdjustmentAmount
            TotalFee = (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee,
            PaidAmount = x.PaidAmount,
            OutstandingAmount = ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) - (x.PaidAmount ?? 0),
            // Map "Unpaid" to "Posting" for display
            Status = x.Status?.ToLower() == "unpaid" ? "Posting" : (x.Status ?? "Pending"),
            PaymentDate = x.PaymentDate,
            PaymentRef = x.PaymentRef,
            BatchNumber = x.BatchNumber,
            PaymentVoucher = x.PaymentVoucher,
            DoctorShare = x.DoctorShare,
            ProcedureDate = x.ProcedureDate,
            ProcedureName = x.ProcedureName,
            SepNo = x.SepNo,
            MedicalProcedure = x.MedicalProcedure,
        }).ToList();

        return new PagedResult<MedicalFeeResponse>(result, total);
    }

    public async Task<int> ProcessPayment(MedicalFeePaymentRequest request, int userId)
    {
        if (request.FeeIds == null || request.FeeIds.Count == 0)
            return 0;

        var fees = await _context.MedicalFees
            .Where(x => request.FeeIds.Contains(x.MedicalFeeId) && 
                       (x.Status == null || x.Status.ToLower() != "paid"))
            .ToListAsync();

        if (fees.Count == 0)
            return 0;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var paymentDate = DateTime.SpecifyKind(request.PaymentDate.Date, DateTimeKind.Utc);
        int updatedCount = 0;

        foreach (var fee in fees)
        {
            fee.Status = "Paid";
            fee.PaidAmount = (fee.AdjustmentAmount != null && fee.AdjustmentAmount != 0) ? fee.AdjustmentAmount.Value : fee.TotalFee;
            fee.PaymentDate = paymentDate;
            fee.PaymentRef = request.PaymentRef;
            fee.PaymentCoaCode = request.CoaCode;
            fee.UpdatedBy = userId;
            fee.UpdatedDate = utcNow;
            updatedCount++;
        }

        _context.MedicalFees.UpdateRange(fees);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionRange(
            AuditModule.MedicalFee, "Pembayaran",
            fees.Select(f => f.MedicalFeeId.ToString()),
            userId, AuditBranchId,
            new
            {
                PaymentDate = paymentDate,
                request.PaymentRef,
                request.CoaCode,
                TotalAmount = fees.Sum(f => f.PaidAmount ?? 0),
                FeeCount = updatedCount
            });

        return updatedCount;
    }

    // ============ Posting ============
    public async Task<BatchPostingResult> PostingBatch(BatchPostingRequest request, int userId)
    {
        if (request.FeeIds == null || request.FeeIds.Count == 0)
            return new BatchPostingResult { PostedCount = 0, BatchNumber = "" };

        var fees = await _context.MedicalFees
            .Where(x => request.FeeIds.Contains(x.MedicalFeeId) && 
                       (x.Status == null || x.Status.ToLower() == "pending"))
            .ToListAsync();

        if (fees.Count == 0)
            return new BatchPostingResult { PostedCount = 0, BatchNumber = "" };

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        
        // Generate batch number: YYYYMMDD-XXXXX
        var datePrefix = utcNow.ToString("yyyyMMdd");
        var existingBatches = await _context.MedicalFees
            .Where(x => x.BatchNumber != null && x.BatchNumber.StartsWith(datePrefix))
            .Select(x => x.BatchNumber)
            .Distinct()
            .ToListAsync();
        
        int nextSeq = existingBatches.Count + 1;
        var batchNumber = $"{datePrefix}-{nextSeq:D5}";

        foreach (var fee in fees)
        {
            fee.Status = "Posting";
            fee.BatchNumber = batchNumber;
            fee.PostingDate = utcNow;
            fee.PostingBy = userId;
            fee.UpdatedBy = userId;
            fee.UpdatedDate = utcNow;
        }

        _context.MedicalFees.UpdateRange(fees);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionRange(
            AuditModule.MedicalFee, "Posting",
            fees.Select(f => f.MedicalFeeId.ToString()),
            userId, AuditBranchId,
            new { BatchNumber = batchNumber, FeeCount = fees.Count });

        return new BatchPostingResult
        {
            PostedCount = fees.Count,
            BatchNumber = batchNumber
        };
    }

    public async Task<int> CancelPostingBatch(List<int> feeIds, int userId)
    {
        if (feeIds == null || feeIds.Count == 0)
            return 0;

        var fees = await _context.MedicalFees
            .Where(x => feeIds.Contains(x.MedicalFeeId) && 
                       (x.Status != null && (x.Status.ToLower() == "posting" || x.Status.ToLower() == "unpaid")))
            .ToListAsync();

        if (fees.Count == 0)
            return 0;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        int updatedCount = 0;

        // Snapshot before the loop nulls these out -- the log is the only place the erased
        // posting_by/posting_date survive.
        var priorState = fees.ToDictionary(
            f => f.MedicalFeeId.ToString(),
            f => (object)new
            {
                PreviousStatus = f.Status,
                PreviousBatchNumber = f.BatchNumber,
                PreviousPostingBy = f.PostingBy,
                PreviousPostingDate = f.PostingDate
            });

        foreach (var fee in fees)
        {
            fee.Status = "Pending";
            fee.BatchNumber = null;
            fee.PostingDate = null;
            fee.PostingBy = null;

            if (fee.Status?.ToLower() == "unpaid")
            {
                fee.PaymentVoucher = null;
                fee.VoucherDate = null;
                fee.VoucherBy = null;
                fee.DoctorShare = null;
            }

            fee.UpdatedBy = userId;
            fee.UpdatedDate = utcNow;
            updatedCount++;
        }

        _context.MedicalFees.UpdateRange(fees);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.MedicalFee, "Batal Posting",
            priorState.Select(kv => (kv.Key, (object?)kv.Value)),
            userId, AuditBranchId);

        return updatedCount;
    }

    public async Task<bool> CancelPosting(int medicalFeeId, int userId)
    {
        var fee = await _context.MedicalFees.FindAsync(medicalFeeId);
        
        // Allow cancelling if status is 'Posting' OR 'Unpaid'
        if (fee == null || (fee.Status?.ToLower() != "posting" && fee.Status?.ToLower() != "unpaid"))
            return false;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Snapshot before the nulling below -- the log is the only place these survive.
        var priorState = new
        {
            PreviousStatus = fee.Status,
            PreviousBatchNumber = fee.BatchNumber,
            PreviousPostingBy = fee.PostingBy,
            PreviousPostingDate = fee.PostingDate
        };

        fee.Status = "Pending";
        fee.BatchNumber = null;
        fee.PostingDate = null;
        fee.PostingBy = null;
        // Clear voucher info if it was Unpaid
        if (fee.Status?.ToLower() == "unpaid")
        {
            fee.PaymentVoucher = null;
            fee.VoucherDate = null;
            fee.VoucherBy = null;
            fee.DoctorShare = null;
        }

        fee.UpdatedBy = userId;
        fee.UpdatedDate = utcNow;

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.MedicalFee, "Batal Posting",
            medicalFeeId.ToString(), userId, AuditBranchId, priorState);

        return true;
    }

    // ============ Payment Voucher ============
    public async Task<PagedResult<DoctorVoucherSummary>> GetVoucherListGroupByDoctor(
        string? doctorCode,
        string? status,
        int page,
        int pageSize)
    {
        IQueryable<MedicalFee> query = _context.MedicalFees;

        // Status Filtering
        if (!string.IsNullOrEmpty(status))
        {
            if (status.ToLower() == "posting")
            {
                // Posting (Unpaid) -> Status 'Posting' OR ('Unpaid' with null PaymentVoucher)
                query = query.Where(x => x.Status != null && 
                    (x.Status.ToLower() == "posting" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher == null)));
            }
            else if (status.ToLower() == "final")
            {
                // Final Payment Voucher -> Status 'Unpaid' AND PaymentVoucher != null
                query = query.Where(x => x.Status != null && x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null);
            }
            else if (status.ToLower() == "paid")
            {
                // Paid -> Status 'Paid' AND PaymentVoucher != null
                query = query.Where(x => x.Status != null && x.Status.ToLower() == "paid" && x.PaymentVoucher != null);
            }
            // Fallback/Default if needed, currently only these two are requested
        }
        else
        {
            // Default behavior if no status provided (backwards compatibility or default view)
            // Assuming "Posting" is the default view if nothing selected, or maybe all valid statuses?
            // Let's keep it broad but usually we want to see pending items to be vouchered.
            // For safety, let's default to logic similar to 'Posting' if that was the previous behavior
            // Previous behavior was: x.Status.ToLower() == "posting"
            query = query.Where(x => x.Status != null && x.Status.ToLower() == "posting");
        }

        if (!string.IsNullOrEmpty(doctorCode))
        {
            query = query.Where(x => x.DoctorCode == doctorCode);
        }

        // For "paid" or "final" status, group by PaymentVoucher; for other statuses, group by DoctorId
        bool isPaidStatus = !string.IsNullOrEmpty(status) && (status.ToLower() == "paid" || status.ToLower() == "final");

        if (isPaidStatus)
        {
            // Group by PaymentVoucher for paid status
            var groupedVoucher = await query
                .GroupBy(x => new { x.PaymentVoucher, x.DoctorId, x.DoctorCode, x.DoctorName })
                .Select(g => new DoctorVoucherSummary
                {
                    DoctorId = g.Key.DoctorId,
                    DoctorCode = g.Key.DoctorCode ?? "",
                    DoctorName = g.Key.DoctorName ?? "",
                    ItemCount = g.Count(),
                    TotalFee = g.Sum(x => (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee),
                    Period = g.Key.PaymentVoucher ?? ""
                })
                .OrderByDescending(x => x.Period)
                .ThenBy(x => x.DoctorName)
                .ToListAsync();

            int totalVoucher = groupedVoucher.Count;

            if (page != -1)
            {
                groupedVoucher = groupedVoucher.Skip((page - 1) * pageSize).Take(pageSize).ToList();
            }

            return new PagedResult<DoctorVoucherSummary>(groupedVoucher, totalVoucher);
        }

        // Original logic for posting/final status - group by DoctorId
        var rawGrouped = await query
            .GroupBy(x => new { x.DoctorId, x.DoctorCode, x.DoctorName })
            .Select(g => new
            {
                DoctorId = g.Key.DoctorId,
                DoctorCode = g.Key.DoctorCode ?? "",
                DoctorName = g.Key.DoctorName ?? "",
                ItemCount = g.Count(),
                TotalFee = g.Sum(x => (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee),
                MinMonth = g.Min(x => x.PeriodMonth),
                MinYear = g.Min(x => x.PeriodYear),
                MaxMonth = g.Max(x => x.PeriodMonth),
                MaxYear = g.Max(x => x.PeriodYear)
            })
            .OrderBy(x => x.DoctorName)
            .ToListAsync();

        var grouped = rawGrouped.Select(g => new DoctorVoucherSummary
        {
            DoctorId = g.DoctorId,
            DoctorCode = g.DoctorCode,
            DoctorName = g.DoctorName,
            ItemCount = g.ItemCount,
            TotalFee = g.TotalFee,
            Period = FormatPeriodRange(g.MinMonth, g.MinYear, g.MaxMonth, g.MaxYear)
        }).ToList();

        int total = grouped.Count;

        if (page != -1)
        {
            grouped = grouped.Skip((page - 1) * pageSize).Take(pageSize).ToList();
        }

        return new PagedResult<DoctorVoucherSummary>(grouped, total);
    }

    private static string FormatPeriodRange(int? minMonth, int? minYear, int? maxMonth, int? maxYear)
    {
        var minPeriod = minMonth.HasValue && minYear.HasValue 
            ? $"{minMonth:00}/{minYear}" : "-";
        var maxPeriod = maxMonth.HasValue && maxYear.HasValue 
            ? $"{maxMonth:00}/{maxYear}" : "-";
        return $"{minPeriod} - {maxPeriod}";
    }

    public async Task<VoucherDetailResponse> GetVoucherDetail(string doctorCode, string? status = null)
    {
        // For paid/final status, the identifier is PaymentVoucher; for other statuses, it's DoctorCode
        bool isPaidStatus = !string.IsNullOrEmpty(status) && (status.ToLower() == "paid" || status.ToLower() == "final");
        
        IQueryable<MedicalFee> query;
        
        if (isPaidStatus)
        {
            // For paid status, doctorCode parameter actually contains PaymentVoucher
            query = _context.MedicalFees.Where(x => x.PaymentVoucher == doctorCode);
            if (status.ToLower() == "paid")
                query = query.Where(x => x.Status != null && x.Status.ToLower() == "paid");
            else if (status.ToLower() == "final")
                 query = query.Where(x => x.Status != null && x.Status.ToLower() == "unpaid");
        }
        else
        {
            query = _context.MedicalFees.Where(x => x.DoctorCode == doctorCode);
            
            // Status Filtering
            if (!string.IsNullOrEmpty(status))
            {
                if (status.ToLower() == "posting")
                {
                    // Posting (Unpaid) -> Status 'Posting' OR ('Unpaid' with null PaymentVoucher)
                    query = query.Where(x => x.Status != null && 
                        (x.Status.ToLower() == "posting" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher == null)));
                }
                else if (status.ToLower() == "final")
                {
                    // Final Payment Voucher -> Status 'Unpaid' AND PaymentVoucher != null
                    query = query.Where(x => x.Status != null && x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null);
                }
            }
            else
            {
                // Default behavior if no status provided
                query = query.Where(x => x.Status != null && x.Status.ToLower() == "posting");
            }
        }
        
        // Get doctor name from first item
        var doctorName = await query.Select(x => x.DoctorName).FirstOrDefaultAsync() ?? "";
        
        // Find DoctorId (for response)
        // Find DoctorId (for response) based on Code
        var actualCode = await query.Select(x => x.DoctorCode).FirstOrDefaultAsync();
        
        // If query empty, and filtering by doctor code (not voucher), use param
        if (string.IsNullOrEmpty(actualCode) && !isPaidStatus)
        {
            actualCode = doctorCode;
        }

        int doctorId = 0;
        if (!string.IsNullOrEmpty(actualCode))
        {
             var contact = await _context.Contacts.FirstOrDefaultAsync(c => c.RefCode == actualCode);
             if (contact != null)
             {
                 doctorId = contact.ContactId;
                 doctorName = contact.ContactName ?? "";
             }
        }
        
        var rawItems = await query
            .OrderBy(x => x.ProcedureDate)
            .Select(x => new 
            {
                x.MedicalFeeId,
                x.PeriodMonth,
                x.PeriodYear,
                x.ProcedureDate,
                x.ProcedureName,
                x.PayerName,
                x.TotalFee,
                x.BatchNumber,
                x.PaymentVoucher,
                x.DoctorShare, // Fetch DoctorShare
                x.Tax,
                x.AdjustmentAmount,
                x.PatientName,
                x.MedicalRecordNo,
                x.VisitNo,
                x.SepNo
            })
            .ToListAsync();

        // Parse PostingDate from BatchNumber (format: YYYYMMDD-XXXXX)
        var items = rawItems.Select(x => new VoucherDetailItem
        {
            MedicalFeeId = x.MedicalFeeId,
            Period = x.PeriodMonth.HasValue && x.PeriodYear.HasValue 
                ? $"{x.PeriodMonth:00}/{x.PeriodYear}" : "-",
            ProcedureDate = x.ProcedureDate,
            ProcedureName = x.ProcedureName,
            PayerName = x.PayerName,
            TotalFee = (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee,
            PostingDate = ParsePostingDateFromBatchNumber(x.BatchNumber),
            PaymentVoucher = x.PaymentVoucher, // Include PaymentVoucher
            PatientName = x.PatientName,
            MedicalRecordNo = x.MedicalRecordNo,
            VisitNo = x.VisitNo,
            SepNo = x.SepNo, // Populate
            AdjustmentAmount = x.AdjustmentAmount
        }).ToList();


        var totalFee = items.Sum(x => x.TotalFee);
        
        // DoctorShare in DB is Percentage. (If 0 or null, assume 100%)
        var totalDoctorShareAmount = rawItems.Sum(x => ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) > 0 ? (x.DoctorShare ?? 0) : 100) / 100);
        
        decimal doctorSharePercentage = 0;
        
        if (totalFee > 0)
        {
            doctorSharePercentage = (totalDoctorShareAmount / totalFee) * 100;
             // Round to 4 decimal places to support higher precision
            doctorSharePercentage = Math.Round(doctorSharePercentage, 4);
        }

        // For paid/final status, we need to ensure DoctorCode is correct (not the voucher number)
        // We can fetch it from the first record or lookup if needed
        var actualDoctorCode = await query.Select(x => x.DoctorCode).FirstOrDefaultAsync();
        
        decimal taxAmount = 0;
        decimal afterTaxAmount = totalDoctorShareAmount;
        decimal previousIncome = 0;
        bool isPPh21 = false;
        var taxDetails = new List<TaxTierDetail>();
        
        // Fetch Doctor PPh21 status
        // Fetch Doctor PPh21 status (Use RefCode)
        var doctorContact = !string.IsNullOrEmpty(actualDoctorCode) 
            ? await _context.Contacts.FirstOrDefaultAsync(c => c.RefCode == actualDoctorCode) 
            : null;
        if (doctorContact != null && doctorContact.PPh21 == true)
        {
            isPPh21 = true;
            actualDoctorCode = doctorContact.RefCode;

            // Determine Previous YTD Income
            int currentYear = rawItems.FirstOrDefault()?.ProcedureDate?.Year ?? DateTime.Now.Year;
            
            // Base query for YTD Income (Paid Doctor Shares)
            var ytdQuery = _context.MedicalFees
                .Where(x => x.DoctorCode == actualDoctorCode 
                        && x.PeriodYear == currentYear 
                        && x.Status != null && x.Status.ToLower() == "paid");

            // If we are looking at a PAID voucher (status = 'paid'),
            // we must exclude the current voucher items from "Previous" income
            // to avoid double counting in Frontend.
            // Note: For 'final' (Unpaid), current items are Unpaid, so they are naturally excluded from 'paid' query.
            if (status != null && status.ToLower() == "paid")
            {
               ytdQuery = ytdQuery.Where(x => x.PaymentVoucher != doctorCode);
            }

            var ytdItems = await ytdQuery
                .Select(x => new { x.AdjustmentAmount, x.TotalFee, x.DoctorShare })
                .ToListAsync();

            var previousIncomeYTD = ytdItems.Sum(x => ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) > 0 ? (x.DoctorShare ?? 0) : 100) / 100);

            // Assign for response
            previousIncome = previousIncomeYTD;

            if (status != null && status.ToLower() == "paid")
            {
                // If strictly PAID, sum stored tax from rawItems.
                // For 'Final' (Unpaid), we calculate it dynamically below because it might not be stored yet (or is 0).
                taxAmount = rawItems.Sum(x => x.Tax ?? 0);
            }
            else
            {
                // Calculate Progressive Tax
                    
                decimal currentVoucherIncome = totalDoctorShareAmount;
                decimal totalAnnualIncome = previousIncomeYTD + currentVoucherIncome;
                
                // DPP (Taxable Income Base) = 50% of Gross Income
                decimal dppTotal = totalAnnualIncome * 0.5m;
                decimal dppPrevious = previousIncomeYTD * 0.5m;
                
                decimal taxTotal = CalculatePPh21(dppTotal, true);
                decimal taxPrevious = CalculatePPh21(dppPrevious, true);
                
                taxAmount = taxTotal - taxPrevious;
                if (taxAmount < 0) taxAmount = 0;

                // Populate Tax Details
                taxDetails = CalculatePPh21Detailed(dppPrevious, currentVoucherIncome * 0.5m);
            }
            
            afterTaxAmount = totalDoctorShareAmount - taxAmount;
        }
        else if (string.IsNullOrEmpty(actualDoctorCode) && doctorId > 0)
        {
             // Fallback if contact logic didn't run or doctor doesn't have PPh21
             var contact = doctorContact ?? await _context.Contacts.FirstOrDefaultAsync(c => c.ContactId == doctorId);
             if (contact != null) 
             {
                 actualDoctorCode = contact.RefCode;
                 doctorContact = contact;
             }
        }

        return new VoucherDetailResponse
        {
            DoctorId = doctorId,
            DoctorCode = actualDoctorCode ?? doctorCode, // Fallback to input if somehow null, but should be actual code
            DoctorName = doctorName,
            ItemCount = items.Count,
            TotalFee = totalFee,
            DoctorShareAmount = totalDoctorShareAmount,
            DoctorSharePercentage = doctorSharePercentage,
            TaxAmount = taxAmount,
            AfterTaxAmount = afterTaxAmount,
            PreviousPeriodIncome = previousIncome,
            IsPPh21 = isPPh21,
            TaxDetails = taxDetails,
            DefaultDoctorShare = doctorContact?.DoctorShare ?? 0,
            Items = rawItems.Select(x => new VoucherDetailItem
            {
                MedicalFeeId = x.MedicalFeeId,
                Period = x.PeriodMonth.HasValue && x.PeriodYear.HasValue 
                    ? $"{x.PeriodMonth:00}/{x.PeriodYear}" : "-",
                ProcedureDate = x.ProcedureDate,
                ProcedureName = x.ProcedureName,
                PayerName = x.PayerName,
                TotalFee = (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee,
                PostingDate = ParsePostingDateFromBatchNumber(x.BatchNumber),
                PaymentVoucher = x.PaymentVoucher, // Include PaymentVoucher
                PatientName = x.PatientName,
                MedicalRecordNo = x.MedicalRecordNo,
                VisitNo = x.VisitNo,
                SepNo = x.SepNo, // Populate
                AdjustmentAmount = x.AdjustmentAmount
            }).ToList()
        };
    }

    private static DateTime? ParsePostingDateFromBatchNumber(string? batchNumber)
    {
        if (string.IsNullOrEmpty(batchNumber) || batchNumber.Length < 8)
            return null;
        
        // BatchNumber format: YYYYMMDD-XXXXX
        var datePart = batchNumber.Substring(0, 8);
        if (DateTime.TryParseExact(datePart, "yyyyMMdd", null, System.Globalization.DateTimeStyles.None, out var date))
            return date;
        
        return null;
    }

    public async Task<GenerateVoucherResult> GenerateVoucher(GenerateVoucherRequest request, int userId)
    {
        var fees = await _context.MedicalFees
            .Where(x => x.DoctorCode == request.DoctorCode &&
                       x.Status != null && x.Status.ToLower() == "posting")
            .ToListAsync();

        if (fees.Count == 0)
            return new GenerateVoucherResult { UpdatedCount = 0, PaymentVoucher = "" };

        // Validation: Check BPJS Adjustment
        var bpjsPending = fees.Any(x => x.PayerCode != null && x.PayerCode.Trim().ToUpper() == "BPJ" && x.AdjustmentDate == null);
        if (bpjsPending)
        {
             throw new Exception("Belum ada aproval amount dari BPJS");
        }

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        
        // Generate voucher number: VP/YYYY/MM/DD/NNNN
        var datePrefix = $"VP/{utcNow:yyyy/MM/dd}";
        var existingVouchers = await _context.MedicalFees
            .Where(x => x.PaymentVoucher != null && x.PaymentVoucher.StartsWith(datePrefix))
            .Select(x => x.PaymentVoucher)
            .Distinct()
            .ToListAsync();
        
        int nextSeq = existingVouchers.Count + 1;
        var voucherNumber = $"{datePrefix}/{nextSeq:D4}";

        decimal totalFee = fees.Sum(x => (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee);
        decimal shareAmount = totalFee * (request.DoctorSharePercent / 100);

        foreach (var fee in fees)
        {
            fee.Status = "Unpaid";
            fee.PaymentVoucher = voucherNumber;
            fee.DoctorShare = request.DoctorSharePercent;
            fee.VoucherDate = utcNow;
            fee.VoucherBy = userId;
            fee.UpdatedBy = userId;
            fee.UpdatedDate = utcNow;
        }

        _context.MedicalFees.UpdateRange(fees);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionRange(
            AuditModule.MedicalFee, "Generate Voucher",
            fees.Select(f => f.MedicalFeeId.ToString()),
            userId, AuditBranchId,
            new
            {
                PaymentVoucher = voucherNumber,
                request.DoctorCode,
                request.DoctorSharePercent,
                TotalFee = totalFee,
                DoctorShareAmount = shareAmount,
                FeeCount = fees.Count
            });

        return new GenerateVoucherResult
        {
            PaymentVoucher = voucherNumber,
            UpdatedCount = fees.Count,
            TotalFee = totalFee,
            DoctorShareAmount = shareAmount
        };
    }

    // ============ Settlement ============
    public async Task<PagedResult<SettlementSummary>> GetSettlementList(
        string? doctorCode,
        string? paymentVoucher,
        string? status,
        DateTime? voucherDateFrom,
        DateTime? voucherDateTo,
        int page,
        int pageSize)
    {
        var query = _context.MedicalFees.AsQueryable();

        if (!string.IsNullOrEmpty(status))
        {
            if (status.ToLower() == "paid")
            {
                 query = query.Where(x => x.Status != null && x.Status.ToLower() == "paid");
            }
            else if (status.ToLower() == "final")
            {
                 // Final = Unpaid BUT has a voucher number
                 query = query.Where(x => x.Status != null && x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null);
            }
            else if (status.ToLower() == "settlement")
            {
                 // Settlement = Paid OR (Unpaid with Voucher)
                 query = query.Where(x => x.Status != null && 
                    (x.Status.ToLower() == "paid" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null)));
            }
            else
            {
                 // Generic filter
                 query = query.Where(x => x.Status != null && x.Status.ToLower() == status.ToLower());
            }
        }
        else
        {
             // Default behavior: Settlement list usually wants Paid or Ready to Pay (Unpaid with Voucher)
             // Similar to "settlement" status logic
             query = query.Where(x => x.Status != null && 
                (x.Status.ToLower() == "paid" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null)));
        }

        if (!string.IsNullOrEmpty(doctorCode))
        {
            query = query.Where(x => x.DoctorCode == doctorCode);
        }

        if (!string.IsNullOrEmpty(paymentVoucher))
        {
            query = query.Where(x => x.PaymentVoucher != null && x.PaymentVoucher.Contains(paymentVoucher));
        }

        if (voucherDateFrom.HasValue)
        {
            var fromDate = DateTime.SpecifyKind(voucherDateFrom.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.VoucherDate >= fromDate);
        }

        if (voucherDateTo.HasValue)
        {
            var toDate = DateTime.SpecifyKind(voucherDateTo.Value.Date.AddDays(1), DateTimeKind.Utc);
            query = query.Where(x => x.VoucherDate < toDate);
        }

        var results = await query
            .GroupBy(x => new { x.DoctorId, x.DoctorName, x.PaymentVoucher, x.DoctorShare, x.VoucherDate, x.Zakat })
            .Select(g => new
            {
                g.Key.DoctorId,
                g.Key.DoctorName,
                g.Key.PaymentVoucher,
                g.Key.VoucherDate,
                g.Key.DoctorShare,
                g.Key.Zakat,
                TotalFee = g.Sum(x => x.TotalFee),
                TotalTax = g.Sum(x => x.Tax ?? 0),
                ItemCount = g.Count(),
                Status = g.Max(x => x.Status)
            })
            .OrderBy(x => x.DoctorName).ThenBy(x => x.PaymentVoucher)
            .ToListAsync();

        var grouped = results.Select(r =>
        {
            decimal shareRatio = (r.DoctorShare ?? 0) / 100;
            decimal zakatRatio = (r.Zakat ?? 0) / 100;
            decimal grossShare = r.TotalFee * shareRatio;
            decimal afterTax = grossShare - r.TotalTax;
            decimal totalZakat = Math.Round(afterTax * zakatRatio, 2);
            decimal totalNet = afterTax - totalZakat;

            return new SettlementSummary
            {
                DoctorId = r.DoctorId,
                DoctorName = r.DoctorName ?? "",
                PaymentVoucher = r.PaymentVoucher ?? "",
                VoucherDate = r.VoucherDate,
                DoctorShare = r.DoctorShare ?? 0,
                TotalFee = r.TotalFee,
                TotalTax = r.TotalTax,
                TotalZakat = totalZakat,
                TotalAfterShare = totalNet,
                ItemCount = r.ItemCount,
                Status = r.Status ?? ""
            };
        }).ToList();

        int total = grouped.Count;

        if (page != -1)
        {
            grouped = grouped.Skip((page - 1) * pageSize).Take(pageSize).ToList();
        }

        return new PagedResult<SettlementSummary>(grouped, total);
    }

    public async Task<SettlementDetailResponse> GetSettlementDetail(string paymentVoucher)
    {
        // Get all items for this voucher (Unpaid or Paid)
        var rawItems = await _context.MedicalFees
            .Where(x => x.PaymentVoucher == paymentVoucher && x.Status != null)
            .ToListAsync();

        if (rawItems.Count == 0)
            return new SettlementDetailResponse();

        // Check PPh21 status and Calculate Dynamic Tax if needed (for Unpaid/Final/Settlement)
        // Similar to GetVoucherDetail logic
        bool isPPh21 = false;
        string? actualDoctorCode = rawItems.FirstOrDefault()?.DoctorCode;
        int doctorId = rawItems.FirstOrDefault()?.DoctorId ?? 0;
        
        // Fetch Doctor Contact for PPh21 status
        var doctorContact = !string.IsNullOrEmpty(actualDoctorCode) 
            ? await _context.Contacts.FirstOrDefaultAsync(c => c.RefCode == actualDoctorCode) 
            : null;

        decimal previousIncomeYTD = 0;
        decimal taxAmountTotal = 0;
        var taxDetails = new List<TaxTierDetail>();
        decimal totalDoctorShareAmount = rawItems.Sum(x => ((x.AdjustmentAmount ?? 0) != 0 ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) > 0 ? (x.DoctorShare ?? 0) : 100) / 100);

        if (doctorContact != null && doctorContact.PPh21 == true)
        {
            isPPh21 = true;
            actualDoctorCode = doctorContact.RefCode; // Ensure we use the RefCode

            // Calculate YTD Income
            int currentYear = rawItems.FirstOrDefault()?.ProcedureDate?.Year ?? DateTime.Now.Year;
            var ytdQuery = _context.MedicalFees
                .Where(x => x.DoctorCode == actualDoctorCode 
                        && x.PeriodYear == currentYear 
                        && x.Status != null && x.Status.ToLower() == "paid");
            
            // If strictly PAID status, exclude current voucher from YTD
            string currentStatus = rawItems.FirstOrDefault()?.Status?.ToLower() ?? "";
            if (currentStatus == "paid")
            {
               ytdQuery = ytdQuery.Where(x => x.PaymentVoucher != paymentVoucher);
            }
            var ytdItems = await ytdQuery
                .Select(x => new { x.AdjustmentAmount, x.TotalFee, x.DoctorShare })
                .ToListAsync();

            previousIncomeYTD = ytdItems.Sum(x => ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) > 0 ? (x.DoctorShare ?? 0) : 100) / 100);

            // Calculate Progressive Tax
            decimal currentVoucherIncome = totalDoctorShareAmount;
            decimal dppPrevious = previousIncomeYTD * 0.5m;

            if (currentStatus == "paid")
            {
                // If Paid, use stored tax
                taxAmountTotal = rawItems.Sum(x => x.Tax ?? 0);
            }
            else
            {
                // Calculate Dynamically
                decimal totalAnnualIncome = previousIncomeYTD + currentVoucherIncome;
                decimal dppTotal = totalAnnualIncome * 0.5m;
                
                decimal taxTotal = CalculatePPh21(dppTotal, true);
                decimal taxPrevious = CalculatePPh21(dppPrevious, true);
                
                taxAmountTotal = taxTotal - taxPrevious;
                if (taxAmountTotal < 0) taxAmountTotal = 0;
            }

            // Populate Tax Details for visualization
            taxDetails = CalculatePPh21Detailed(dppPrevious, currentVoucherIncome * 0.5m);
        }

        var resultItems = new List<SettlementDetailItem>();
        decimal calculatedTaxSum = 0;
        decimal calculatedZakatSum = 0;
        
        // Distribute Tax Pro-rata if calculated dynamically and not stored
        // Or if stored (Paid), use stored.
        bool useStoredTax = (rawItems.FirstOrDefault()?.Status?.ToLower() == "paid");
        decimal totalShareForRatio = totalDoctorShareAmount > 0 ? totalDoctorShareAmount : 1; // Avoid div/0

        foreach (var x in rawItems)
        {
            var share = (x.TotalFee * (x.DoctorShare ?? 0) / 100);
            decimal tax = 0;
            decimal zakat = 0;

            if (useStoredTax)
            {
                // stored tax is Amount
                tax = x.Tax ?? 0;
                // stored zakat is Rate (%) in MedicalFee entity? No, looking at SalarySlip it's Rate.
                // Wait, MedicalFee entity has zakat column. Is it Amount or Rate?
                // Let's check GetSalarySlip logic again.
                // var zakatRate = x.Zakat ?? 0;
                // var zakatAmt = Math.Round(afterTaxNode * (zakatRate / 100), 2);
                // It's a Rate.
                
                decimal taxRate = x.Tax ?? 0;
                decimal zakatRate = x.Zakat ?? 0;
                
                // If it's PAID, the Tax column might be already the Amount?
                // Let's check ProcessSettlement again: fee.Tax = allocatedTax; // Save as AMOUNT
                // Yes, Tax is saved as AMOUNT in ProcessSettlement.
                
                tax = x.Tax ?? 0;
                
                // What about Zakat? ProcessSettlement doesn't seem to save Zakat amount.
                // It treats Zakat as a rate in SalarySlip.
                var afterTaxNode = share - tax;
                zakat = Math.Round(afterTaxNode * (zakatRate / 100), 2);
            }
            else
            {
                // Pro-rate the total calculated tax based on share amount
                if (isPPh21 && totalDoctorShareAmount > 0)
                {
                     decimal ratio = share / totalDoctorShareAmount;
                     tax = Math.Round(taxAmountTotal * ratio, 2);
                }
                
                // Zakat calculation for non-paid (draft)
                // Use the rate from the first item or from the fee itself
                decimal zakatRate = x.Zakat ?? 0;
                zakat = Math.Round((share - tax) * (zakatRate / 100), 2);
            }

            var afterTaxAndZakat = share - tax - zakat;
            calculatedTaxSum += tax;
            calculatedZakatSum += zakat;

            resultItems.Add(new SettlementDetailItem
            {
                MedicalFeeId = x.MedicalFeeId,
                ProcedureDate = x.ProcedureDate,
                ProcedureName = x.ProcedureName,
                PayerName = x.PayerName,
                PatientName = x.PatientName,
                MedicalRecordNo = x.MedicalRecordNo,
                TotalFee = x.TotalFee,
                DoctorShare = share,
                TaxAmount = tax,
                ZakatAmount = zakat,
                AmountAfterShare = afterTaxAndZakat
            });
        }
        
        // Adjust total tax sum to match strictly calculated total (handle rounding errors if needed)
        // If dynamic, use taxAmountTotal as the source of truth for the summary
        decimal finalTotalTax = useStoredTax ? calculatedTaxSum : (isPPh21 ? taxAmountTotal : 0);
        decimal finalTotalZakat = calculatedZakatSum;
        decimal finalTotalNet = totalDoctorShareAmount - finalTotalTax - finalTotalZakat;

        return new SettlementDetailResponse
        {
            PaymentVoucher = paymentVoucher,
            VoucherDate = rawItems.FirstOrDefault()?.VoucherDate, // Or Voucher Date if separate
            Status = rawItems.All(x => x.Status == "Paid") ? "Paid" : "Unpaid", 
            PaymentDate = rawItems.FirstOrDefault()?.PaymentDate,
            PaymentRef = rawItems.FirstOrDefault()?.PaymentRef,
            DoctorSharePercentage = rawItems.FirstOrDefault()?.DoctorShare ?? 0,
            CoaCode = rawItems.FirstOrDefault()?.PaymentCoaCode,
            CoaDesc = rawItems.FirstOrDefault()?.PaymentCoaDesc,
            Items = resultItems,
            TotalAmount = totalDoctorShareAmount, // Gross Share
            TotalTax = finalTotalTax,
            TotalZakat = finalTotalZakat,
            TotalNet = finalTotalNet,
            IsPPh21 = isPPh21,
            PreviousPeriodIncome = previousIncomeYTD,
            TaxDetails = taxDetails
        };
    }

    public async Task<SettlementResult> ProcessSettlement(SettlementRequest request, int userId)
    {
        var fees = await _context.MedicalFees
            .Where(x => x.PaymentVoucher == request.PaymentVoucher && 
                       x.Status != null && x.Status.ToLower() == "unpaid")
            .ToListAsync();

        if (fees.Count == 0)
            return new SettlementResult { UpdatedCount = 0, TotalPaid = 0 };

        int branchId = fees.First().BranchId ?? 1; // Fallback to 1 if null
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var paymentDate = DateTime.SpecifyKind(request.PaymentDate.Date, DateTimeKind.Utc);
        
        // 1. Calculate Expected Amounts
        // Transaction Amount is the SUM of TOTAL FEE (Full Amount)
        // Doctor Share Amount is the portion for the doctor
        decimal totalFeeAmount = fees.Sum(x => (x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee);
        decimal totalDoctorShareAmount = fees.Sum(x => ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) / 100));
        
        // Use totalFeeAmount as the Paid Amount (Bank Credit)
        decimal paidAmount = totalFeeAmount;

        // 2. Fetch Options
        var optionPribadi = await _optionService.GetByName("ACCOUNT_JASA_MEDIS_PRIBADI", branchId);
        var optionAsuransi = await _optionService.GetByName("ACCOUNT_JASA_MEDIS_ASURANSI", branchId);
        var optionSelisih = await _optionService.GetByName("ACCOUNT_JASA_MEDIS_SELISIH", branchId);

        string coaPribadi = optionPribadi?.OptionValue ?? "201004";
        string coaAsuransi = optionAsuransi?.OptionValue ?? "201005";
        string coaSelisih = optionSelisih?.OptionValue ?? "722098";

        // Validate COAs exist
        var bankCoa = await _coaService.GetByCode(request.CoaCode, branchId);
        if (bankCoa == null)
            throw new Exception($"Bank/Cash Account with code '{request.CoaCode}' not found.");

        // 3. Create Transaction Header
        string transactionType = "Payment"; // Using standard Payment type
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(request.PaymentDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = paymentDate,
            TransactionName = $"Payment Voucher {request.PaymentVoucher}",
            TransactionRef = request.PaymentVoucher, // Reference the Voucher
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Payment for Voucher {request.PaymentVoucher}",
            CoaCode = request.CoaCode, // Bank Account
            CoaName = request.CoaDesc,
            status = Status.Published.ToString(), // Or Published? Usually Pending then user posts? Or direct? Default Pending.
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double?)paidAmount, // Use Total Fee
            PaymentNo = request.PaymentVoucher,
            PaymentDate = paymentDate,
            PaymentStatus = "Paid",
            CoaParentCode = bankCoa.ParentCode,
            CoaParentName = bankCoa.ParentName
        };

        // Bank COA details already fetched above, assigned in initializer.

        await _transactionService.Add(transaction);

        // 4. Create Journals
        
        // A. Credit Bank (Main Account)
        var journalBank = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = transaction.Description,
            CoaCode = transaction.CoaCode,
            CoaName = transaction.CoaName,
            CoaParentCode = transaction.CoaParentCode,
            CoaParentName = transaction.CoaParentName,
            DebitAmount = 0,
            CreditAmount = (double?)paidAmount, // Credit the FULL amount
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalBank);

        // B. Debit Fee Accounts (Group by PayerCode)
        // Logic: PayerCode 'Prb' -> Pribadi, Else -> Asuransi
        var groupedFees = fees.GroupBy(x => 
            (x.PayerCode != null && x.PayerCode.Equals("Prb", StringComparison.OrdinalIgnoreCase)) ? "PRIBADI" : "ASURANSI"
        );

        foreach (var group in groupedFees)
        {
            decimal groupShareAmount = group.Sum(x => ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) / 100));
            string targetCoaCode = group.Key == "PRIBADI" ? coaPribadi : coaAsuransi;
            
            var targetCoa = await _coaService.GetByCode(targetCoaCode, branchId);
            
            var journalDebit = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Jasa Medis {group.Key} - {request.PaymentVoucher}",
                CoaCode = targetCoaCode,
                CoaName = targetCoa?.CoaName ?? "",
                CoaParentCode = targetCoa?.ParentCode,
                CoaParentName = targetCoa?.ParentName,
                DebitAmount = (double?)groupShareAmount,
                CreditAmount = 0,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow
            };
            await _journalService.Add(journalDebit);
        }

        // C-2. Handle PPh21 Tax (Credit) & Adjustment to Fees
        // ----------------------------------------------------
        // Logic: Calculate Dynamic PPh21 (YTD Based), Distribute to Items, Save to DB, Create Journal
        
        bool isPPh21 = false;
        string? actualDoctorCode = fees.FirstOrDefault()?.DoctorCode;
        
        // Fetch Doctor Contact for PPh21 status
        var doctorContact = !string.IsNullOrEmpty(actualDoctorCode) 
            ? await _context.Contacts.FirstOrDefaultAsync(c => c.RefCode == actualDoctorCode) 
            : null;

        decimal previousIncomeYTD = 0;
        decimal taxAmountTotal = 0;
        
        if (doctorContact != null && doctorContact.PPh21 == true)
        {
            isPPh21 = true;
            actualDoctorCode = doctorContact.RefCode; 

            // Calculate YTD Income (Same logic as GetSettlementDetail/GetVoucherDetail)
            int currentYear = fees.FirstOrDefault()?.ProcedureDate?.Year ?? DateTime.Now.Year;
            
            // For ProcessSettlement, existing status is "Unpaid" so they are NOT in the "Paid" list yet.
            // So we just sum all "Paid" vouchers for this doctor/year.
            var ytdQuery = _context.MedicalFees
                .Where(x => x.DoctorCode == actualDoctorCode 
                        && x.PeriodYear == currentYear 
                        && x.Status != null && x.Status.ToLower() == "paid");
            
            var ytdItems = await ytdQuery
                .Select(x => new { x.AdjustmentAmount, x.TotalFee, x.DoctorShare })
                .ToListAsync();

            previousIncomeYTD = ytdItems.Sum(x => ((x.AdjustmentAmount != null && x.AdjustmentAmount != 0) ? x.AdjustmentAmount.Value : x.TotalFee) * ((x.DoctorShare ?? 0) > 0 ? (x.DoctorShare ?? 0) : 100) / 100);

            // Calculate Progressive Tax
            decimal currentVoucherIncome = totalDoctorShareAmount;
            decimal totalAnnualIncome = previousIncomeYTD + currentVoucherIncome;
            
            decimal dppTotal = totalAnnualIncome * 0.5m;
            decimal dppPrevious = previousIncomeYTD * 0.5m;
            
            decimal taxTotal = CalculatePPh21(dppTotal, true);
            decimal taxPrevious = CalculatePPh21(dppPrevious, true);
            
            taxAmountTotal = taxTotal - taxPrevious;
            if (taxAmountTotal < 0) taxAmountTotal = 0;
        }

        // Distribute Tax Amount to Items and Update fees in DB
        if (taxAmountTotal > 0 && totalDoctorShareAmount > 0)
        {
             foreach (var fee in fees)
             {
                 decimal effectiveFee = (fee.AdjustmentAmount != null && fee.AdjustmentAmount != 0) ? fee.AdjustmentAmount.Value : fee.TotalFee;
                 decimal share = effectiveFee * ((fee.DoctorShare ?? 0) / 100);
                 decimal ratio = share / totalDoctorShareAmount;
                 decimal allocatedTax = Math.Round(taxAmountTotal * ratio, 2);
                 fee.Tax = allocatedTax; // Save as AMOUNT
             }
             // Reconcile rounding diffs if necessary (optional for now)
        }
        else
        {
             // Ensure Tax is 0 if no tax
             foreach (var fee in fees) fee.Tax = 0;
        }

        // Journal for Tax
        if (taxAmountTotal > 0)
        {
            // PPh21 Payable Account
            string coaTaxPayable = "2104001"; // "Hutang PPH 21"
            var taxCoa = await _coaService.GetByCode(coaTaxPayable, branchId);

            var journalTax = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"PPh21 Jasa Medis - {request.PaymentVoucher}",
                CoaCode = coaTaxPayable,
                CoaName = taxCoa?.CoaName ?? "Hutang PPH 21",
                CoaParentCode = taxCoa?.ParentCode,
                CoaParentName = taxCoa?.ParentName,
                DebitAmount = 0,
                CreditAmount = (double?)taxAmountTotal,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow
            };
            await _journalService.Add(journalTax);
        }

        // C. Handle Difference (Selisih)
        // Difference = TotalFee (Paid) - (TotalDoctorShare - TotalTax)
        decimal netPayable = totalDoctorShareAmount - taxAmountTotal;
        decimal difference = paidAmount - netPayable;

        if (difference != 0)
        {
            // Difference is usually POSITIVE context (Total > Share).
            // We need to BALANCE the journal.
            // Credit Bank (Total) > Debits (Share).
            // We need more DEBIT to balance.
            
            var selisihCoa = await _coaService.GetByCode(coaSelisih, branchId);
            bool isDebit = difference > 0;
            decimal absDiff = Math.Abs(difference);

            var journalDiff = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Selisih Bayar Jasa Medis - {request.PaymentVoucher}",
                CoaCode = coaSelisih,
                CoaName = selisihCoa?.CoaName ?? "",
                CoaParentCode = selisihCoa?.ParentCode,
                CoaParentName = selisihCoa?.ParentName,
                DebitAmount = (double?)(isDebit ? absDiff : 0),
                CreditAmount = (double?)(isDebit ? 0 : absDiff),
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow
            };
            await _journalService.Add(journalDiff);
        }

        // 5. Update Medical Fees
        foreach (var fee in fees)
        {
            var shareAmount = fee.TotalFee * ((fee.DoctorShare ?? 0) / 100);
            fee.Status = "Paid";
            fee.PaidAmount = shareAmount; // Record the SHARE amount as paid for that item? Or pro-rate the actual paid? Usually keep share amount as reference.
            fee.PaymentDate = paymentDate;
            fee.PaymentRef = journalNo; // Use Journal No as Payment Ref
            fee.PaymentCoaCode = request.CoaCode;
            fee.PaymentCoaDesc = request.CoaDesc;
            fee.UpdatedBy = userId;
            fee.UpdatedDate = utcNow;
            // Note: DB constraints might require TransactionId linkage? MedicalFee doesn't have TransactionId column shown in insert statement, only JournalId?
            // User INSERT showed 'journal_id' column. Maybe link it there?
            // "journal_id" usually refers to the Header ID (TransactionId)? Or specific journal line?
            // Let's assume TransactionId for now or leave null if not critical. 
             // The INSERT structure showed "journal_id" column.
             // I'll try to set JournalId = TransactionId (parsed to Int? No, TransactionId is GUID string).
             // Wait, TransactionId in Transaction table is String (GUID). JournalId in MedicalFee is Int?
             // Checking MedicalFee entity...
        }

        _context.MedicalFees.UpdateRange(fees);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionRange(
            AuditModule.MedicalFee, "Settlement",
            fees.Select(f => f.MedicalFeeId.ToString()),
            userId, branchId,
            new
            {
                request.PaymentVoucher,
                PaymentDate = paymentDate,
                request.CoaCode,
                request.PaidAmount,
                TotalFeeAmount = totalFeeAmount,
                TotalDoctorShareAmount = totalDoctorShareAmount,
                FeeCount = fees.Count
            });

        return new SettlementResult
        {
            UpdatedCount = fees.Count,
            TotalPaid = request.PaidAmount
        };
    }

    // ============ Lookup ============
    public async Task<List<dynamic>> GetDoctorOptions(string? keyword)
    {
        var query = _context.Contacts
            .Where(x => x.ContactCategoryId == Constants.CONTACT_CATEGORY_DOCTOR); // Doctor category

        if (!string.IsNullOrEmpty(keyword))
        {
            query = query.Where(x => 
                (x.ContactName != null && x.ContactName.ToLower().Contains(keyword.ToLower())) ||
                (x.RefCode != null && x.RefCode.ToLower().Contains(keyword.ToLower()))
            );
        }

        var result = await query
            .OrderBy(x => x.ContactName)
            .Take(50)
            .Select(x => new { x.RefCode, x.ContactName, x.ContactId })
            .ToListAsync();

        return result.Select(x => (dynamic)new { 
            value = x.RefCode ?? "", 
            label = $"{x.RefCode} - {x.ContactName}",
            desc = x.RefCode
        }).ToList();
    }

    public async Task<List<dynamic>> GetPayerOptions(string? keyword)
    {
        var query = _context.Contacts
            .Where(x => x.ContactCategoryId == Constants.CONTACT_CATEGORY_PAYER); // Payer category

        if (!string.IsNullOrEmpty(keyword))
        {
            query = query.Where(x => 
                (x.ContactName != null && x.ContactName.ToLower().Contains(keyword.ToLower())) ||
                (x.RefCode != null && x.RefCode.ToLower().Contains(keyword.ToLower()))
            );
        }

        var result = await query
            .OrderBy(x => x.ContactName)
            .Take(50)
            .Select(x => new { x.RefCode, x.ContactName, x.ContactId })
            .ToListAsync();

        return result.Select(x => (dynamic)new { 
            value = x.RefCode ?? "", 
            label = $"{x.RefCode} - {x.ContactName}",
            desc = x.RefCode 
        }).ToList();
    }

    public async Task<List<dynamic>> GetPatientOptions(string? keyword)
    {
        var query = _context.Contacts
            .Where(x => x.ContactCategoryId == Constants.CONTACT_CATEGORY_PATIENT);

        if (!string.IsNullOrEmpty(keyword))
        {
            query = query.Where(x =>
                (x.ContactName != null && x.ContactName.ToLower().Contains(keyword.ToLower())) ||
                (x.RefCode != null && x.RefCode.ToLower().Contains(keyword.ToLower()))
            );
        }

        var result = await query
            .OrderBy(x => x.ContactName)
            .Take(50)
            .Select(x => new { x.RefCode, x.ContactName, x.ContactId })
            .ToListAsync();

        return result.Select(x => (dynamic)new {
            value = x.ContactId,
            label = $"{x.RefCode} - {x.ContactName}",
            desc = x.RefCode,
            name = x.ContactName
        }).ToList();
    }

    public async Task<List<dynamic>> GetVisitNoOptions(string medicalRecordNo)
    {
        var result = await _context.MedicalFees
            .Where(x => x.MedicalRecordNo == medicalRecordNo && x.VisitNo != null && x.VisitNo != "")
            .Select(x => x.VisitNo!)
            .Distinct()
            .OrderByDescending(x => x)
            .Take(50)
            .ToListAsync();

        return result.Select(x => (dynamic)new {
            value = x,
            label = x
        }).ToList();
    }

    public async Task<List<dynamic>> GetSepNoOptions(string medicalRecordNo)
    {
        var result = await _context.MedicalFees
            .Where(x => x.MedicalRecordNo == medicalRecordNo && x.SepNo != null && x.SepNo != "")
            .Select(x => x.SepNo!)
            .Distinct()
            .OrderByDescending(x => x)
            .Take(50)
            .ToListAsync();

        return result.Select(x => (dynamic)new {
            value = x,
            label = x
        }).ToList();
    }

    public async Task<List<dynamic>> GetPaymentMethodOptions(string? keyword)
    {
        var query = _context.MedicalFees
            .Where(x => x.PayerCode != null && x.PayerName != null);

        if (!string.IsNullOrEmpty(keyword))
        {
            query = query.Where(x => 
                (x.PayerName != null && x.PayerName.ToLower().Contains(keyword.ToLower())) ||
                (x.PayerCode != null && x.PayerCode.ToLower().Contains(keyword.ToLower()))
            );
        }

        var result = await query
            .Select(x => new { x.PayerCode, x.PayerName })
            .Distinct()
            .OrderBy(x => x.PayerName)
            .Take(50)
            .ToListAsync();

        return result.Select(x => (dynamic)new { 
            value = x.PayerCode ?? "", 
            label = x.PayerName ?? ""
        }).ToList();
    }

    public async Task<SalarySlipResponse> GetSalarySlip(string paymentVoucher, string printedBy, int branchId = 1)
    {
        var fees = await _context.MedicalFees
            .Include(x => x.Doctor)
            .Where(x => x.PaymentVoucher == paymentVoucher && x.Status != null && x.Status.ToLower() == "paid")
            .OrderBy(x => x.ProcedureDate)
            .ToListAsync();

        if (!fees.Any())
            throw new Exception("Settlement not found or not paid.");

        var firstItem = fees.First();
        var doctorName = firstItem.DoctorName ?? firstItem.Doctor?.ContactName ?? "-";
        
        // Get Hospital Name from OptionService
        var hospitalNameOption = await _optionService.GetByName("HOSPITAL_NAME", branchId);
        var hospitalName = hospitalNameOption?.OptionValue ?? "RS. Taman Harapan Baru";

        // Get CostCenters map
        var costCenterIds = fees.Select(x => x.CostCenterId).Distinct().Where(x => x.HasValue).Select(x => x.Value).ToList();
        var costCenters = await _context.CostCenters
            .Where(x => costCenterIds.Contains(x.CostcenterId))
            .ToDictionaryAsync(x => x.CostcenterId, x => x.CostCenterName);

        var minDate = fees.Min(x => x.ProcedureDate) ?? DateTime.MinValue;
        var maxDate = fees.Max(x => x.ProcedureDate) ?? DateTime.MinValue;
        var period = $"{minDate:dd/MM/yyyy} - {maxDate:dd/MM/yyyy}";

        decimal subTotal = 0;
        decimal totalTax = 0;
        decimal totalZakat = 0;

        var items = new List<SalarySlipItem>();

        foreach (var x in fees)
        {
            var share = x.PaidAmount ?? 0;
            // Tax is stored as AMOUNT
            var taxAmt = x.Tax ?? 0;
            var afterTaxNode = share - taxAmt;
            
            // Zakat is stored as RATE (%)
            var zakatRate = x.Zakat ?? 0;
            var zakatAmt = Math.Round(afterTaxNode * (zakatRate / 100), 2);

            subTotal += share;
            totalTax += taxAmt;
            totalZakat += zakatAmt;

            items.Add(new SalarySlipItem
            {
                RegistrationNo = x.BatchNumber ?? "-", 
                InvoiceNo = !string.IsNullOrEmpty(x.VisitNo) ? x.VisitNo : (x.PaymentRef ?? "-"), // Priority: VisitNo
                MedicalRecordNo = x.MedicalRecordNo ?? "-",
                PatientName = x.PatientName ?? "-",
                Polyclinic = (x.CostCenterId.HasValue && costCenters.ContainsKey(x.CostCenterId.Value)) ? costCenters[x.CostCenterId.Value] : "-",
                ServiceName = x.ProcedureName ?? "-",
                PaymentType = x.PatientType != null ? x.PatientType.ToUpper() : "TUNAI",
                DoctorShare = share
            });
        }

        var afterTaxTotal = subTotal - totalTax;
        var netAmount = afterTaxTotal - totalZakat;
        
        // Effective Rates for display? Or use first item's rate?
        // If items have different rates, this might be misleading, but we need to pass something to the model.
        // Let's use the first item's rate as "Representative" rate or 0 if varied.
        // Or updated model to not display rate if 0. User didn't ask to change model.
        decimal displayTaxRate = firstItem.Tax ?? 0;
        decimal displayZakatRate = firstItem.Zakat ?? 0;

        return new SalarySlipResponse
        {
            HospitalName = hospitalName,
            DoctorName = doctorName,
            Period = period,
            Items = items,
            SubTotal = subTotal,
            TaxAmount = totalTax,
            TaxRate = displayTaxRate,
            AfterTaxAmount = afterTaxTotal,
            ZakatAmount = totalZakat,
            ZakatRate = displayZakatRate,
            NetAmount = netAmount,
            PrintedDate = DateTime.Now,
            PrintedBy = printedBy
        };

    }

    public async Task<VoucherDetailResponse> GetVoucherDetailExport(string doctorCode, string status, string? paymentVoucher = null)
    {
         if (!string.IsNullOrEmpty(paymentVoucher))
         {
             // If Payment Voucher is provided, we treat it as "Paid" lookup (where doctorCode param = paymentVoucher)
             // This leverages existing logic in GetVoucherDetail that handles filtering by PaymentVoucher
             return await GetVoucherDetail(paymentVoucher, "Paid");
         }

         // Fallback to existing logic if no paymentVoucher (e.g. Export pending by Doctor)
         return await GetVoucherDetail(doctorCode, status);
    }

    // ============ Settlement Export ============
    public async Task<List<SettlementSummary>> GetSettlementExport(
        string? doctorCode,
        string? paymentVoucher,
        string? status,
        DateTime? voucherDateFrom,
        DateTime? voucherDateTo)
    {
        var query = _context.MedicalFees.AsQueryable();

        if (!string.IsNullOrEmpty(status))
        {
            if (status.ToLower() == "paid")
                query = query.Where(x => x.Status != null && x.Status.ToLower() == "paid");
            else if (status.ToLower() == "final")
                query = query.Where(x => x.Status != null && x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null);
            else if (status.ToLower() == "settlement")
                query = query.Where(x => x.Status != null &&
                    (x.Status.ToLower() == "paid" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null)));
        }
        else
        {
            query = query.Where(x => x.Status != null &&
                (x.Status.ToLower() == "paid" || (x.Status.ToLower() == "unpaid" && x.PaymentVoucher != null)));
        }

        if (!string.IsNullOrEmpty(doctorCode))
            query = query.Where(x => x.DoctorCode == doctorCode);

        if (!string.IsNullOrEmpty(paymentVoucher))
            query = query.Where(x => x.PaymentVoucher != null && x.PaymentVoucher.Contains(paymentVoucher));

        if (voucherDateFrom.HasValue)
        {
            var fromDate = DateTime.SpecifyKind(voucherDateFrom.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.VoucherDate >= fromDate);
        }

        if (voucherDateTo.HasValue)
        {
            var toDate = DateTime.SpecifyKind(voucherDateTo.Value.Date.AddDays(1), DateTimeKind.Utc);
            query = query.Where(x => x.VoucherDate < toDate);
        }

        var results = await query
            .GroupBy(x => new { x.DoctorId, x.DoctorName, x.PaymentVoucher, x.DoctorShare, x.VoucherDate, x.Zakat })
            .Select(g => new
            {
                g.Key.DoctorId,
                g.Key.DoctorName,
                g.Key.PaymentVoucher,
                g.Key.VoucherDate,
                g.Key.DoctorShare,
                g.Key.Zakat,
                TotalFee = g.Sum(x => x.TotalFee),
                TotalTax = g.Sum(x => x.Tax ?? 0),
                ItemCount = g.Count(),
                Status = g.Max(x => x.Status)
            })
            .OrderBy(x => x.DoctorName).ThenBy(x => x.PaymentVoucher)
            .ToListAsync();

        var grouped = results.Select(r =>
        {
            decimal shareRatio = (r.DoctorShare ?? 0) / 100;
            decimal zakatRatio = (r.Zakat ?? 0) / 100;
            decimal grossShare = r.TotalFee * shareRatio;
            decimal afterTax = grossShare - r.TotalTax;
            decimal totalZakat = Math.Round(afterTax * zakatRatio, 2);
            decimal totalNet = afterTax - totalZakat;

            return new SettlementSummary
            {
                DoctorId = r.DoctorId,
                DoctorName = r.DoctorName ?? "",
                PaymentVoucher = r.PaymentVoucher ?? "",
                VoucherDate = r.VoucherDate,
                DoctorShare = r.DoctorShare ?? 0,
                TotalFee = r.TotalFee,
                TotalTax = r.TotalTax,
                TotalZakat = totalZakat,
                TotalAfterShare = totalNet,
                ItemCount = r.ItemCount,
                Status = r.Status ?? ""
            };
        }).ToList();

        return grouped;
    }

    public async Task<List<SettlementMakerExport>> GetSettlementMakerExport(
        string? doctorCode,
        string? paymentVoucher,
        string? status,
        DateTime? voucherDateFrom,
        DateTime? voucherDateTo)
    {
        var summaries = await GetSettlementExport(doctorCode, paymentVoucher, status, voucherDateFrom, voucherDateTo);
        var result = new List<SettlementMakerExport>();

        foreach (var summary in summaries)
        {
            var detail = await GetSettlementDetail(summary.PaymentVoucher);
            
            // Get DoctorCode from the first item of the voucher
            var firstFee = await _context.MedicalFees.FirstOrDefaultAsync(x => x.PaymentVoucher == summary.PaymentVoucher);
            var feeDoctorCode = firstFee?.DoctorCode;

            string bankName = "-";
            string accountNo = "-";

            if (!string.IsNullOrEmpty(feeDoctorCode))
            {
                var contact = await _context.Contacts
                    .FirstOrDefaultAsync(c => c.RefCode == feeDoctorCode);
                
                if (contact != null)
                {
                    var account = await _context.ContactAccounts
                        .Include(a => a.Bank)
                        .FirstOrDefaultAsync(a => a.ContactId == contact.ContactId);
                    
                    if (account != null)
                    {
                        bankName = account.Bank?.BankName ?? "-";
                        accountNo = account.AccountNo ?? "-";
                    }
                }
            }

            result.Add(new SettlementMakerExport
            {
                DoctorName = summary.DoctorName,
                HonorNetto = detail.TotalNet,
                BankName = bankName,
                AccountNo = accountNo
            });
        }

        return result;
    }

    // ============ PPh21 6-Tier Calculation ============
    public decimal CalculatePPh21(decimal grossAmount, bool isPPh21Subject)
    {
        if (!isPPh21Subject || grossAmount <= 0)
            return 0;

        // Indonesian PPh21 Progressive Tax Brackets (Annual)
        // Tier 1: 0 - 60M: 5%
        // Tier 2: 60M - 250M: 15%
        // Tier 3: 250M - 500M: 25%
        // Tier 4: 500M - 5B: 30%
        // Tier 5: > 5B: 35%
        
        decimal tax = 0;
        decimal remaining = grossAmount;

        // Tier 1: 0 - 60,000,000 @ 5%
        const decimal tier1Limit = 60_000_000m;
        if (remaining > 0)
        {
            var taxable = Math.Min(remaining, tier1Limit);
            tax += taxable * 0.05m;
            remaining -= taxable;
        }

        // Tier 2: 60M - 250M @ 15%
        const decimal tier2Limit = 190_000_000m; // 250M - 60M
        if (remaining > 0)
        {
            var taxable = Math.Min(remaining, tier2Limit);
            tax += taxable * 0.15m;
            remaining -= taxable;
        }

        // Tier 3: 250M - 500M @ 25%
        const decimal tier3Limit = 250_000_000m; // 500M - 250M
        if (remaining > 0)
        {
            var taxable = Math.Min(remaining, tier3Limit);
            tax += taxable * 0.25m;
            remaining -= taxable;
        }

        // Tier 4: 500M - 5B @ 30%
        const decimal tier4Limit = 4_500_000_000m; // 5B - 500M
        if (remaining > 0)
        {
            var taxable = Math.Min(remaining, tier4Limit);
            tax += taxable * 0.30m;
            remaining -= taxable;
        }

        // Tier 5: > 5B @ 35%
        if (remaining > 0)
        {
            tax += remaining * 0.35m;
        }

        return Math.Round(tax, 2);
    }

    public List<TaxTierDetail> CalculatePPh21Detailed(decimal previousDpp, decimal currentDpp)
    {
        var tiers = new List<(string Name, decimal Limit, decimal Rate)>
        {
            ("Tier 1 (5%)", 60_000_000m, 0.05m),
            ("Tier 2 (15%)", 190_000_000m, 0.15m), // 250M - 60M
            ("Tier 3 (25%)", 250_000_000m, 0.25m), // 500M - 250M
            ("Tier 4 (30%)", 4_500_000_000m, 0.30m), // 5B - 500M
            ("Tier 5 (35%)", decimal.MaxValue, 0.35m)
        };

        var result = new List<TaxTierDetail>();
        decimal remainingPrevious = previousDpp;
        decimal remainingCurrent = currentDpp;

        foreach (var tier in tiers)
        {
            if (remainingCurrent <= 0) break;

            decimal tierCapacity = tier.Limit;
            
            // Amount already used by previous periods in this tier
            decimal usedInThisTier = Math.Min(remainingPrevious, tierCapacity);
            remainingPrevious -= usedInThisTier;

            // Amount available in this tier for current period
            decimal availableInThisTier = tierCapacity - usedInThisTier;
            
            if (availableInThisTier > 0)
            {
                decimal taxableInThisTier = Math.Min(remainingCurrent, availableInThisTier);
                if (taxableInThisTier > 0)
                {
                    result.Add(new TaxTierDetail
                    {
                        TierName = tier.Name,
                        Rate = tier.Rate * 100,
                        TaxableAmount = taxableInThisTier,
                        TaxValue = Math.Round(taxableInThisTier * tier.Rate, 2)
                    });
                }
                remainingCurrent -= taxableInThisTier;
            }
        }

        return result;
    }

    // ============ BPJS Adjustment Import ============
    public async Task<BpjsAdjustmentResult> ImportBpjsAdjustment(Stream fileStream, string fileName, int userId)
    {
        var result = new BpjsAdjustmentResult();
        var errors = new List<string>();
        // Collected across both the xlsx and csv branches, written once after SaveChanges.
        var adjustedEntries = new List<(string RefId, object? Detail)>();
        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        _logger.LogInformation($"Starting ImportBpjsAdjustment for file: {fileName}");

        try
        {
            var extension = Path.GetExtension(fileName).ToLower();
            int lineNumber = 0; // Declare outside


            if (extension == ".xlsx")
            {
                 using var package = new OfficeOpenXml.ExcelPackage(fileStream);
                 var worksheet = package.Workbook.Worksheets.FirstOrDefault();
                 if (worksheet == null) throw new Exception("No worksheet found in Excel file");

                 int rowCount = worksheet.Dimension?.Rows ?? 0;
                 int startRow = 2; // Default for simple list
                 int idCol = 0;    // Ref ID Column
                 int sepCol = 0;   // SEP Column
                 int amtCol = 0;   // Amount Column
                 int statusCol = 0; // Status Column

                 // Detect if this is our "Rincian Jasa Medis" export (Old Format)
                 var titleCell = worksheet.Cells[1, 1].Text;
                 int headerRow = 1;

                 if (titleCell != null && titleCell.Contains("RINCIAN JASA MEDIS", StringComparison.OrdinalIgnoreCase))
                 {
                     headerRow = 7;
                 }
                                  // Scan headers
                  for (int c = 1; c <= worksheet.Dimension?.Columns; c++)
                  {
                      var header = worksheet.Cells[headerRow, c].Text?.Trim().ToLower();
                      if (header == "ref id" || header == "ref_id" || header == "id") idCol = c;
                      if (header == "no sep" || header == "nosep" || header == "no_sep" || header == "sep" || header == "sep no") sepCol = c;
                      if (header == "bpjs approve" || header == "bpjs_approve" || header == "approved_amount" || header == "approved amount" || header == "amount") amtCol = c;
                      if (header == "status") statusCol = c;
                  }
                 
                 _logger.LogInformation($"Header Scan Result: IdCol={idCol}, SepCol={sepCol}, AmtCol={amtCol}, StartRow={headerRow + 1}");

                 // Validate required columns
                 if (amtCol == 0)
                     throw new Exception("Column 'BPJS Approve' not found.");
                 if (idCol == 0 && sepCol == 0)
                     throw new Exception("Neither 'Ref ID' nor 'No SEP' column found.");

                 startRow = headerRow + 1;

                 for (int row = startRow; row <= rowCount; row++)

                 {
                     lineNumber = row;
                     decimal approvedAmount = 0; 
                     try
                     {
                         string? refIdStr = idCol > 0 ? worksheet.Cells[row, idCol].Text?.Trim() : null;
                         var sepNo = sepCol > 0 ? worksheet.Cells[row, sepCol].Text?.Trim() : "";

                         // Validation: Skip if SEP is empty or "0"
                         if (string.IsNullOrEmpty(sepNo) || sepNo == "0" || sepNo == "-") 
                         {
                             result.SkippedCount++;
                             continue;
                         }
                         var amountStr = worksheet.Cells[row, amtCol].Text?.Trim();

                         MedicalFee? fee = null;

                         if (!string.IsNullOrEmpty(refIdStr) && int.TryParse(refIdStr, out int feeId))
                         {
                             fee = await _context.MedicalFees.FindAsync(feeId);
                             if (fee == null) 
                                 _logger.LogWarning($"Row {row}: Ref ID '{feeId}' not found in DB");
                         }
                         
                         if (fee == null && !string.IsNullOrEmpty(sepNo))
                          {
                               // Lookup by SEP (Primary for this use case)
                               fee = await _context.MedicalFees.FirstOrDefaultAsync(x => x.VisitNo == sepNo || x.SepNo == sepNo);
                          }
                          
                          // Filter: Only Allow Payer BPJS
                          if (fee != null && (fee.PayerCode == null || !fee.PayerCode.ToUpper().Contains("BPJ")))
                          {
                              _logger.LogWarning($"Row {row}: Skipped - Payer '{fee.PayerCode}' is not BPJS");
                              // errors.Add($"Row {row}: Skipped (Non-BPJS)"); // Optional: Don't treat as error, just skip
                              result.SkippedCount++;
                              continue;
                          }

                         if (fee == null)
                         {
                             _logger.LogWarning($"Row {row}: Record not found (Ref ID: {refIdStr}, SEP: {sepNo})");
                             errors.Add($"Row {row}: Record not found");
                             result.SkippedCount++;
                             continue;
                         }

                         if (!string.IsNullOrEmpty(amountStr))
                         {
                             // Robust Parsing
                             var rawVal = worksheet.Cells[row, amtCol].Value;
                             if (rawVal is double dVal) approvedAmount = (decimal)dVal;
                             else if (rawVal is decimal decVal) approvedAmount = decVal;
                             else 
                             {
                                 // Try ID Culture (100.000 = 100k)
                                 if (!decimal.TryParse(amountStr, System.Globalization.NumberStyles.Any, new System.Globalization.CultureInfo("id-ID"), out approvedAmount))
                                 {
                                      // Fallback: Digits only
                                      var digits = new string(amountStr.Where(char.IsDigit).ToArray());
                                      decimal.TryParse(digits, out approvedAmount);
                                 }
                             }
                             _logger.LogInformation($"Row {row}: Parsed Amount: {approvedAmount} (Raw: {worksheet.Cells[row, amtCol].Text})");
                         }

                         _logger.LogInformation($"Row {row}: Updating Fee {fee.MedicalFeeId}. AdjAmount: {fee.AdjustmentAmount} -> {approvedAmount}");

                         var previousAmount = fee.AdjustmentAmount;
                         fee.AdjustmentAmount = approvedAmount;
                         var statusStr = statusCol > 0 ? worksheet.Cells[row, statusCol].Text?.Trim() : "Posting";
                         if (fee.Status == "Pending" && !string.IsNullOrEmpty(statusStr) && statusStr == "Posting")
                         {
                             fee.Status = statusStr;
                         }
                         fee.AdjustmentDate = utcNow; // Set Adjustment Date
                         fee.UpdatedBy = userId;
                         fee.UpdatedDate = utcNow;

                         _context.Entry(fee).State = EntityState.Modified;
                         result.UpdatedCount++;
                         adjustedEntries.Add((fee.MedicalFeeId.ToString(), new
                         {
                             AmountBefore = previousAmount,
                             AmountAfter = approvedAmount,
                             TotalFee = fee.TotalFee,
                             SourceFile = fileName
                         }));
                     }
                     catch (Exception ex)
                     {
                         errors.Add($"Row {row}: {ex.Message}");
                         result.SkippedCount++;
                     }
                 }
                 result.ImportedCount = rowCount - 1;
            }
            else
            {
                using var reader = new StreamReader(fileStream);
            string? line;
            lineNumber = 0; // Reset

            while ((line = await reader.ReadLineAsync()) != null)
            {
                lineNumber++;
                if (lineNumber == 1) continue; // Skip header row

                try
                {
                    // Expected TXT format (tab or semicolon separated):
                    // SEP_NO ; PATIENT_NAME ; CLAIM_AMOUNT ; APPROVED_AMOUNT ; STATUS
                    var parts = line.Split(new[] { '\t', ';' }, StringSplitOptions.RemoveEmptyEntries);
                    
                    if (parts.Length < 4)
                    {
                        errors.Add($"Line {lineNumber}: Invalid format - expected at least 4 columns");
                        result.SkippedCount++;
                        continue;
                    }

                    var sepNo = parts[0].Trim();
                    var approvedAmountStr = parts[3].Trim().Replace(",", "").Replace(".", "");
                    
                    if (!decimal.TryParse(approvedAmountStr, out decimal approvedAmount))
                    {
                        errors.Add($"Line {lineNumber}: Invalid approved amount '{parts[3]}'");
                        result.SkippedCount++;
                        continue;
                    }

                    // Find matching MedicalFee by VisitNo (SEP)
                    var fee = await _context.MedicalFees
                        .FirstOrDefaultAsync(x => x.VisitNo == sepNo);

                    if (fee == null)
                    {
                        errors.Add($"Line {lineNumber}: SEP '{sepNo}' not found");
                        result.SkippedCount++;
                        continue;
                    }

                    // Update the fee with approved amount (Adjustment)
                    var previousAmount = fee.AdjustmentAmount;
                    fee.AdjustmentAmount = approvedAmount;
                    var statusStr = parts.Length > 4 ? parts[4].Trim() : "Posted";
                    if (fee.Status == "Pending" && !string.IsNullOrEmpty(statusStr))
                    {
                        fee.Status = statusStr;
                    }
                    fee.UpdatedBy = userId;
                    fee.UpdatedDate = utcNow;

                    result.UpdatedCount++;
                    adjustedEntries.Add((fee.MedicalFeeId.ToString(), new
                    {
                        AmountBefore = previousAmount,
                        AmountAfter = approvedAmount,
                        TotalFee = fee.TotalFee,
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

            result.ImportedCount = lineNumber - 1; // Exclude header
            await _context.SaveChangesAsync();
        }
        catch (Exception ex)
        {
            errors.Add($"File processing error: {ex.Message}");
        }

        await _auditTrailUtils.LogActionEntries(
            AuditModule.MedicalFee, "Adjustment BPJS",
            adjustedEntries, userId, AuditBranchId);

        result.Errors = errors;
        return result;
    }

    public async Task<MedicalFeeResponse> CreateManual(CreateMedicalFeeRequest request, int userId)
    {
        var now = DateTime.UtcNow;
        var entity = new MedicalFee
        {
            DoctorId = request.DoctorId,
            DoctorCode = request.DoctorCode,
            DoctorName = request.DoctorName,
            PatientName = request.PatientName,
            MedicalRecordNo = request.MedicalRecordNo,
            SepNo = request.SepNo,
            ProcedureName = request.ProcedureName,
            ProcedureDate = request.ProcedureDate,
            MedicalProcedure = request.MedicalProcedure,
            PayerCode = request.PayerCode,
            PayerName = request.PayerName,
            VisitNo = request.VisitNo,
            TotalFee = request.TotalFee,
            Status = "Pending",
            PeriodMonth = request.ProcedureDate?.Month ?? now.Month,
            PeriodYear = request.ProcedureDate?.Year ?? now.Year,
            BranchId = 1,
            CreatedBy = userId,
            CreatedDate = now,
        };

        _context.MedicalFees.Add(entity);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.MedicalFee, "Dibuat Manual",
            entity.MedicalFeeId.ToString(), userId, entity.BranchId,
            new
            {
                entity.DoctorCode,
                entity.DoctorName,
                entity.PatientName,
                entity.ProcedureName,
                entity.TotalFee,
                entity.Status
            });

        return new MedicalFeeResponse
        {
            MedicalFeeId = entity.MedicalFeeId,
            DoctorId = entity.DoctorId,
            DoctorCode = entity.DoctorCode,
            DoctorName = entity.DoctorName,
            PatientName = entity.PatientName,
            MedicalRecordNo = entity.MedicalRecordNo,
            SepNo = entity.SepNo,
            ProcedureName = entity.ProcedureName,
            ProcedureDate = entity.ProcedureDate,
            MedicalProcedure = entity.MedicalProcedure,
            PayerCode = entity.PayerCode,
            PayerName = entity.PayerName,
            TotalFee = entity.TotalFee,
            Status = entity.Status,
        };
    }

    public async Task<BulkManualImportResult> ImportBulkManual(Stream fileStream, string fileName, int userId)
    {
        var result = new BulkManualImportResult();
        var errors = new List<string>();
        var errorRows = new List<BulkManualErrorRow>();
        var utcNow = DateTime.UtcNow;
        var extension = Path.GetExtension(fileName).ToLower();

        _logger.LogInformation($"Starting ImportBulkManual for file: {fileName}");

        try
        {
            // Pre-load contacts for validation (lowercase RefCode for case-insensitive lookup)
            var doctors = await _context.Contacts
                .Where(c => c.ContactCategoryId == 3)
                .Select(c => new { Code = c.RefCode!.ToLower(), c.ContactName, c.ContactId })
                .ToListAsync();
            var doctorLookup = doctors.ToDictionary(d => d.Code, d => d);

            var patients = await _context.Contacts
                .Where(c => c.ContactCategoryId == 2)
                .Select(c => new { Code = c.RefCode!.ToLower(), c.ContactName, c.ContactId })
                .ToListAsync();
            var patientLookup = patients.ToDictionary(p => p.Code, p => p);

            var payers = await _context.Contacts
                .Where(c => c.ContactCategoryId == 5)
                .Select(c => new { Code = c.RefCode!.ToLower(), c.ContactName, c.ContactId })
                .ToListAsync();
            var payerLookup = payers.ToDictionary(p => p.Code, p => p);

            // Parse rows from file
            var parsedRows = new List<(string[] raw, CreateMedicalFeeRequest req)>();

            if (extension == ".csv")
            {
                using var reader = new StreamReader(fileStream);
                string? headerLine = await reader.ReadLineAsync();
                if (string.IsNullOrWhiteSpace(headerLine))
                    throw new Exception("File kosong atau tidak memiliki header");

                int lineNumber = 1;
                while (!reader.EndOfStream)
                {
                    lineNumber++;
                    var line = await reader.ReadLineAsync();
                    if (string.IsNullOrWhiteSpace(line)) continue;

                    var cols = ParseCsvLine(line);
                    var parseResult = ParseRowData(cols, lineNumber);
                    if (parseResult.error != null)
                    {
                        errors.Add(parseResult.error);
                        errorRows.Add(BuildErrorRow(cols, parseResult.error));
                        result.SkippedCount++;
                    }
                    else
                    {
                        parsedRows.Add((cols, parseResult.request!));
                    }
                }
            }
            else if (extension == ".xlsx")
            {
                using var package = new OfficeOpenXml.ExcelPackage(fileStream);
                var worksheet = package.Workbook.Worksheets.FirstOrDefault();
                if (worksheet == null) throw new Exception("Worksheet tidak ditemukan dalam file Excel");

                int rowCount = worksheet.Dimension?.Rows ?? 0;
                for (int row = 2; row <= rowCount; row++)
                {
                    var cols = new string[12];
                    for (int c = 0; c < 12; c++)
                        cols[c] = worksheet.Cells[row, c + 1].Text?.Trim() ?? "";

                    if (string.IsNullOrEmpty(cols[0]) && string.IsNullOrEmpty(cols[6])) continue;

                    // Handle Excel date values
                    DateTime? excelDate = null;
                    var rawDate = worksheet.Cells[row, 8].Value;
                    if (rawDate is DateTime dtVal) excelDate = dtVal;
                    else if (rawDate is double dblVal) excelDate = DateTime.FromOADate(dblVal);

                    // Handle Excel numeric fee
                    decimal? excelFee = null;
                    var rawFee = worksheet.Cells[row, 12].Value;
                    if (rawFee is double dFee) excelFee = (decimal)dFee;
                    else if (rawFee is decimal decFee) excelFee = decFee;

                    var parseResult = ParseRowData(cols, row, excelDate, excelFee);
                    if (parseResult.error != null)
                    {
                        errors.Add(parseResult.error);
                        errorRows.Add(BuildErrorRow(cols, parseResult.error));
                        result.SkippedCount++;
                    }
                    else
                    {
                        parsedRows.Add((cols, parseResult.request!));
                    }
                }
            }
            else
            {
                throw new Exception("Format file tidak didukung. Gunakan CSV atau XLSX.");
            }

            // Validate against Contacts table
            var validRows = new List<CreateMedicalFeeRequest>();
            foreach (var (raw, req) in parsedRows)
            {
                var rowErrors = new List<string>();

                // Validate Doctor (ContactCategoryId = 3)
                var dCode = (req.DoctorCode ?? "").ToLower();
                if (!string.IsNullOrEmpty(dCode) && doctorLookup.TryGetValue(dCode, out var doctor))
                {
                    req.DoctorName = doctor.ContactName;
                    req.DoctorId = doctor.ContactId;
                }
                else
                {
                    rowErrors.Add($"KodeDokter '{req.DoctorCode}' tidak ditemukan di master kontak");
                }

                // Validate Patient (ContactCategoryId = 2) - if NoRekamMedis provided
                if (!string.IsNullOrEmpty(req.MedicalRecordNo))
                {
                    var pCode = req.MedicalRecordNo.ToLower();
                    if (patientLookup.TryGetValue(pCode, out var patient))
                    {
                        req.PatientName = patient.ContactName;
                    }
                    else
                    {
                        rowErrors.Add($"NoRekamMedis '{req.MedicalRecordNo}' tidak ditemukan di master kontak");
                    }
                }

                // Validate Payer (ContactCategoryId = 5) - if PayerCode provided
                if (!string.IsNullOrEmpty(req.PayerCode))
                {
                    var payCode = req.PayerCode.ToLower();
                    if (payerLookup.TryGetValue(payCode, out var payer))
                    {
                        req.PayerName = payer.ContactName;
                    }
                    else
                    {
                        rowErrors.Add($"KodeCaraBayar '{req.PayerCode}' tidak ditemukan di master kontak");
                    }
                }

                if (rowErrors.Count > 0)
                {
                    var errorMsg = string.Join("; ", rowErrors);
                    errors.Add(errorMsg);
                    errorRows.Add(BuildErrorRow(raw, errorMsg));
                    result.SkippedCount++;
                }
                else
                {
                    validRows.Add(req);
                }
            }

            // Bulk insert valid rows
            if (validRows.Count > 0)
            {
                var entities = validRows.Select(r => new MedicalFee
                {
                    DoctorId = r.DoctorId,
                    DoctorCode = r.DoctorCode,
                    DoctorName = r.DoctorName,
                    PatientName = r.PatientName,
                    MedicalRecordNo = r.MedicalRecordNo,
                    SepNo = r.SepNo,
                    ProcedureName = r.ProcedureName,
                    ProcedureDate = r.ProcedureDate,
                    MedicalProcedure = r.MedicalProcedure,
                    PayerCode = r.PayerCode,
                    PayerName = r.PayerName,
                    VisitNo = r.VisitNo,
                    TotalFee = r.TotalFee,
                    Status = "Pending",
                    PeriodMonth = r.ProcedureDate?.Month ?? utcNow.Month,
                    PeriodYear = r.ProcedureDate?.Year ?? utcNow.Year,
                    BranchId = 1,
                    CreatedBy = userId,
                    CreatedDate = utcNow,
                }).ToList();

                await _context.MedicalFees.AddRangeAsync(entities);
                await _context.SaveChangesAsync();
                result.InsertedCount = entities.Count;

                // Ids are populated by SaveChanges.
                await _auditTrailUtils.LogActionEntries(
                    AuditModule.MedicalFee, "Import Manual",
                    entities.Select(e => (e.MedicalFeeId.ToString(), (object?)new
                    {
                        e.DoctorCode,
                        e.DoctorName,
                        e.PatientName,
                        e.TotalFee,
                        SourceFile = fileName
                    })),
                    userId, AuditBranchId);
            }
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "ImportBulkManual Error");
            throw;
        }

        result.Errors = errors;
        result.ErrorRows = errorRows;
        return result;
    }

    /// <summary>
    /// Generate Excel file containing error rows from bulk import
    /// </summary>
    public byte[] GenerateBulkManualErrorExcel(List<BulkManualErrorRow> errorRows)
    {
        using var package = new OfficeOpenXml.ExcelPackage();
        var ws = package.Workbook.Worksheets.Add("Error Import");

        // Headers (same as template + Error column)
        var headers = new[] { "KodeDokter", "NamaDokter", "NamaPasien", "NoRekamMedis", "NoRegistrasi", "NoSEP", "NamaTindakan", "TanggalTindakan", "JenisPelayanan", "KodeCaraBayar", "NamaCaraBayar", "TotalFee", "Error" };
        for (int i = 0; i < headers.Length; i++)
        {
            ws.Cells[1, i + 1].Value = headers[i];
            ws.Cells[1, i + 1].Style.Font.Bold = true;
            ws.Cells[1, i + 1].Style.Fill.PatternType = OfficeOpenXml.Style.ExcelFillStyle.Solid;
            ws.Cells[1, i + 1].Style.Fill.BackgroundColor.SetColor(System.Drawing.Color.FromArgb(220, 230, 241));
        }
        // Error column header highlight red
        ws.Cells[1, 13].Style.Fill.BackgroundColor.SetColor(System.Drawing.Color.FromArgb(255, 199, 206));

        for (int r = 0; r < errorRows.Count; r++)
        {
            var row = errorRows[r];
            ws.Cells[r + 2, 1].Value = row.KodeDokter;
            ws.Cells[r + 2, 2].Value = row.NamaDokter;
            ws.Cells[r + 2, 3].Value = row.NamaPasien;
            ws.Cells[r + 2, 4].Value = row.NoRekamMedis;
            ws.Cells[r + 2, 5].Value = row.NoRegistrasi;
            ws.Cells[r + 2, 6].Value = row.NoSEP;
            ws.Cells[r + 2, 7].Value = row.NamaTindakan;
            ws.Cells[r + 2, 8].Value = row.TanggalTindakan;
            ws.Cells[r + 2, 9].Value = row.JenisPelayanan;
            ws.Cells[r + 2, 10].Value = row.KodeCaraBayar;
            ws.Cells[r + 2, 11].Value = row.NamaCaraBayar;
            ws.Cells[r + 2, 12].Value = row.TotalFee;
            ws.Cells[r + 2, 13].Value = row.Error;
            ws.Cells[r + 2, 13].Style.Font.Color.SetColor(System.Drawing.Color.Red);
        }

        ws.Cells.AutoFitColumns();
        return package.GetAsByteArray();
    }

    /// <summary>
    /// Generate Excel template for bulk manual medical fee upload
    /// </summary>
    public byte[] GenerateBulkManualTemplate()
    {
        using var package = new OfficeOpenXml.ExcelPackage();
        var ws = package.Workbook.Worksheets.Add("Template Bulk Jasa Medis");

        var headers = new[] { "KodeDokter", "NamaDokter", "NamaPasien", "NoRekamMedis", "NoRegistrasi", "NoSEP", "NamaTindakan", "TanggalTindakan", "JenisPelayanan", "KodeCaraBayar", "NamaCaraBayar", "TotalFee" };
        for (int i = 0; i < headers.Length; i++)
        {
            ws.Cells[1, i + 1].Value = headers[i];
            ws.Cells[1, i + 1].Style.Font.Bold = true;
            ws.Cells[1, i + 1].Style.Fill.PatternType = OfficeOpenXml.Style.ExcelFillStyle.Solid;
            ws.Cells[1, i + 1].Style.Fill.BackgroundColor.SetColor(System.Drawing.Color.FromArgb(68, 114, 196));
            ws.Cells[1, i + 1].Style.Font.Color.SetColor(System.Drawing.Color.White);
        }

        // Example row 1
        ws.Cells[2, 1].Value = "DK001";
        ws.Cells[2, 2].Value = "Dr. Ahmad Surya";
        ws.Cells[2, 3].Value = "Budi Santoso";
        ws.Cells[2, 4].Value = "RM00001";
        ws.Cells[2, 5].Value = "REG001";
        ws.Cells[2, 6].Value = "SEP001";
        ws.Cells[2, 7].Value = "Konsultasi Spesialis";
        ws.Cells[2, 8].Value = "2026-01-15";
        ws.Cells[2, 9].Value = "Rawat Jalan";
        ws.Cells[2, 10].Value = "BPJS01";
        ws.Cells[2, 11].Value = "BPJS Kesehatan";
        ws.Cells[2, 12].Value = 500000;
        ws.Cells[2, 12].Style.Numberformat.Format = "#,##0";

        // Example row 2
        ws.Cells[3, 1].Value = "DK002";
        ws.Cells[3, 2].Value = "Dr. Siti Nurhaliza";
        ws.Cells[3, 3].Value = "Dewi Lestari";
        ws.Cells[3, 4].Value = "RM00002";
        ws.Cells[3, 5].Value = "REG002";
        ws.Cells[3, 6].Value = "";
        ws.Cells[3, 7].Value = "Operasi Minor";
        ws.Cells[3, 8].Value = "2026-01-16";
        ws.Cells[3, 9].Value = "Rawat Inap";
        ws.Cells[3, 10].Value = "UMUM";
        ws.Cells[3, 11].Value = "Umum / Pribadi";
        ws.Cells[3, 12].Value = 1500000;
        ws.Cells[3, 12].Style.Numberformat.Format = "#,##0";

        // Style example rows as italic gray to indicate sample data
        for (int row = 2; row <= 3; row++)
        {
            for (int col = 1; col <= 12; col++)
            {
                ws.Cells[row, col].Style.Font.Italic = true;
                ws.Cells[row, col].Style.Font.Color.SetColor(System.Drawing.Color.Gray);
            }
        }

        // Add data validation for JenisPelayanan column (dropdown)
        var validation = ws.DataValidations.AddListValidation(ws.Cells["I2:I1000"].Address);
        validation.Formula.Values.Add("Rawat Jalan");
        validation.Formula.Values.Add("Rawat Inap");
        validation.ShowErrorMessage = true;
        validation.ErrorTitle = "Invalid";
        validation.Error = "Pilih 'Rawat Jalan' atau 'Rawat Inap'";

        ws.Cells.AutoFitColumns();
        // Set minimum column widths for readability
        ws.Column(7).Width = 25; // NamaTindakan
        ws.Column(8).Width = 16; // TanggalTindakan

        return package.GetAsByteArray();
    }

    private static (CreateMedicalFeeRequest? request, string? error) ParseRowData(
        string[] cols, int lineNumber, DateTime? excelDate = null, decimal? excelFee = null)
    {
        if (cols.Length < 12)
            return (null, $"Baris {lineNumber}: Kolom kurang dari 12 (ditemukan {cols.Length})");

        var doctorCode = cols[0]?.Trim();
        var doctorName = cols[1]?.Trim();
        var patientName = cols[2]?.Trim();
        var medicalRecordNo = cols[3]?.Trim();
        var visitNo = cols[4]?.Trim();
        var sepNo = cols[5]?.Trim();
        var procedureName = cols[6]?.Trim();
        var procedureDateStr = cols[7]?.Trim();
        var medicalProcedure = cols[8]?.Trim();
        var payerCode = cols[9]?.Trim();
        var payerName = cols[10]?.Trim();
        var totalFeeStr = cols[11]?.Trim();

        if (string.IsNullOrEmpty(doctorCode))
            return (null, $"Baris {lineNumber}: KodeDokter kosong");
        if (string.IsNullOrEmpty(procedureName))
            return (null, $"Baris {lineNumber}: NamaTindakan kosong");
        if (string.IsNullOrEmpty(medicalProcedure) || (medicalProcedure != "Rawat Jalan" && medicalProcedure != "Rawat Inap"))
            return (null, $"Baris {lineNumber}: JenisPelayanan harus 'Rawat Jalan' atau 'Rawat Inap'");

        // Parse date
        DateTime? procedureDate = excelDate.HasValue
            ? DateTime.SpecifyKind(excelDate.Value, DateTimeKind.Utc)
            : null;

        if (procedureDate == null)
        {
            if (!string.IsNullOrEmpty(procedureDateStr))
            {
                if (DateTime.TryParseExact(procedureDateStr, new[] { "yyyy-MM-dd", "dd/MM/yyyy", "dd-MM-yyyy" },
                    System.Globalization.CultureInfo.InvariantCulture, System.Globalization.DateTimeStyles.None, out var dt))
                {
                    procedureDate = DateTime.SpecifyKind(dt, DateTimeKind.Utc);
                }
                else
                {
                    return (null, $"Baris {lineNumber}: Format TanggalTindakan tidak valid (gunakan yyyy-MM-dd)");
                }
            }
            else
            {
                return (null, $"Baris {lineNumber}: TanggalTindakan kosong");
            }
        }

        // Parse fee
        decimal totalFee = excelFee ?? 0;
        if (totalFee == 0 && !string.IsNullOrEmpty(totalFeeStr))
        {
            var digits = new string(totalFeeStr.Where(c => char.IsDigit(c)).ToArray());
            decimal.TryParse(digits, out totalFee);
        }
        if (totalFee <= 0)
            return (null, $"Baris {lineNumber}: TotalFee harus lebih dari 0");

        return (new CreateMedicalFeeRequest
        {
            DoctorCode = doctorCode,
            DoctorName = doctorName,
            PatientName = patientName,
            MedicalRecordNo = medicalRecordNo,
            VisitNo = visitNo,
            SepNo = string.IsNullOrEmpty(sepNo) ? null : sepNo,
            ProcedureName = procedureName,
            ProcedureDate = procedureDate,
            MedicalProcedure = medicalProcedure,
            PayerCode = payerCode,
            PayerName = payerName,
            TotalFee = totalFee,
        }, null);
    }

    private static BulkManualErrorRow BuildErrorRow(string[] cols, string error)
    {
        return new BulkManualErrorRow
        {
            KodeDokter = cols.Length > 0 ? cols[0]?.Trim() : "",
            NamaDokter = cols.Length > 1 ? cols[1]?.Trim() : "",
            NamaPasien = cols.Length > 2 ? cols[2]?.Trim() : "",
            NoRekamMedis = cols.Length > 3 ? cols[3]?.Trim() : "",
            NoRegistrasi = cols.Length > 4 ? cols[4]?.Trim() : "",
            NoSEP = cols.Length > 5 ? cols[5]?.Trim() : "",
            NamaTindakan = cols.Length > 6 ? cols[6]?.Trim() : "",
            TanggalTindakan = cols.Length > 7 ? cols[7]?.Trim() : "",
            JenisPelayanan = cols.Length > 8 ? cols[8]?.Trim() : "",
            KodeCaraBayar = cols.Length > 9 ? cols[9]?.Trim() : "",
            NamaCaraBayar = cols.Length > 10 ? cols[10]?.Trim() : "",
            TotalFee = cols.Length > 11 ? cols[11]?.Trim() : "",
            Error = error,
        };
    }

    private static string[] ParseCsvLine(string line)
    {
        var result = new List<string>();
        bool inQuotes = false;
        var current = new System.Text.StringBuilder();

        for (int i = 0; i < line.Length; i++)
        {
            char c = line[i];
            if (c == '"')
            {
                inQuotes = !inQuotes;
            }
            else if (c == ',' && !inQuotes)
            {
                result.Add(current.ToString());
                current.Clear();
            }
            else
            {
                current.Append(c);
            }
        }
        result.Add(current.ToString());
        return result.ToArray();
    }
}

