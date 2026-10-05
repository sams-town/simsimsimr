using Microsoft.EntityFrameworkCore;
using Newtonsoft.Json;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using TDPBackend.Helpers;

namespace RSSMBackend.Services;


public class PurchaseOrderService : IPurchaseOrderService
{
    private readonly RSSMDbContext _context;
    private readonly IJournalService _journalService;
    private readonly ITransactionService _transactionService;
    private readonly AuditTrailUtils _auditTrailUtils;

    // A PO with a journal line on this COA (Persediaan Obat) tagged with its order
    // number has already been recorded/paid in the source accounting (Khanza import),
    // so it drops off the outstanding-PO list. Requested explicitly by the business.
    private const string PaidInventoryCoa = "204099";

    // Roles allowed to Mark as Paid: Administrator(1), Manager(2), Supervisor(3).
    // Staff(4) and Petty Cash(5) are excluded. Mirrors AuditLogController's role gating.
    public static readonly int[] MarkAsPaidRoles = { 1, 2, 3 };

    public PurchaseOrderService(
        RSSMDbContext context,
        IJournalService journalService,
        ITransactionService transactionService,
        AuditTrailUtils auditTrailUtils)
    {
        _context = context;
        _journalService = journalService;
        _transactionService = transactionService;
        _auditTrailUtils = auditTrailUtils;
    }

    /// <summary>
    /// Builds the outstanding-PO query shared by the on-screen list and the Excel export,
    /// so both hide the exact same rows. A PO is hidden once it has moved on: it was paid
    /// through Account Payable (linked invoice fully Paid), it carries a 204099 journal line
    /// for its order number, or it was manually marked as paid.
    ///
    /// <paramref name="markedPaid"/> flips this around: it returns ONLY the manually
    /// marked-as-paid POs, which is the archive view the Undo action works from — without
    /// it those rows are unreachable from the UI once marked.
    /// </summary>
    private IQueryable<PurchaseOrder> BuildFilteredQuery(string search, string status, string? supplierType, DateTime? startDate, DateTime? endDate, bool markedPaid = false)
    {
        var query = _context.PurchaseOrders.AsQueryable();

        if (!string.IsNullOrEmpty(search))
        {
            search = search.ToLower();
            query = query.Where(x =>
                (x.OrderNumber != null && x.OrderNumber.ToLower().Contains(search)) ||
                (x.SupplierName != null && x.SupplierName.ToLower().Contains(search)) ||
                (x.SupplierCode != null && x.SupplierCode.ToLower().Contains(search)));
        }

        if (!string.IsNullOrEmpty(status) && status != "All")
        {
            query = query.Where(x => x.Status == status);
        }

        // Apply supplier type filter based on supplier_code prefix
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

        if (startDate.HasValue)
        {
            var startUtc = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
            query = query.Where(x => x.Date >= startUtc);
        }

        if (endDate.HasValue)
        {
            var endUtc = DateTime.SpecifyKind(endDate.Value.Date, DateTimeKind.Utc).AddDays(1).AddTicks(-1);
            query = query.Where(x => x.Date <= endUtc);
        }

        if (markedPaid)
        {
            // Archive view: only what Mark as Paid hid, so it can be reviewed and undone.
            return query.Where(po => po.ManualPaid);
        }

        // Hide POs that have already been paid / recorded elsewhere:
        query = query.Where(po =>
            // (c) manually marked as paid via an untrackable channel
            !po.ManualPaid
            // (b) a 204099 (Persediaan Obat) journal line exists for this order number
            && !_context.Journals.Any(j => j.CoaCode == PaidInventoryCoa && j.PONumber == po.OrderNumber)
            // (a) paid through Account Payable -- linked invoice exchange fully Paid
            && !_context.InvoiceExchanges.Any(ie =>
                    ie.PONumber == po.OrderNumber
                    && ie.PaymentStatus != null
                    && ie.PaymentStatus.ToLower() == "paid"));

        return query;
    }

    public async Task<(List<PurchaseOrder> Data, int TotalRecords)> GetList(string search, string status, string? supplierType, DateTime? startDate, DateTime? endDate, int page, int pageSize, bool markedPaid = false)
    {
        var query = BuildFilteredQuery(search, status, supplierType, startDate, endDate, markedPaid);

        var totalRecords = await query.CountAsync();

        if (page > 0 && pageSize > 0)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.OrderByDescending(x => x.Date).ToListAsync();
        return (data, totalRecords);
    }

    /// <summary>
    /// Excel of the outstanding-PO list. Uses the same visibility filter as GetList, so
    /// paid / marked-as-paid POs never appear in the download either.
    /// </summary>
    public async Task<byte[]> ExportList(string search, string status, string? supplierType, DateTime? startDate, DateTime? endDate, bool markedPaid = false)
    {
        var data = await BuildFilteredQuery(search, status, supplierType, startDate, endDate, markedPaid)
            .OrderByDescending(x => x.Date)
            .ToListAsync();

        var exportData = data.Select((x, index) => new
        {
            No = index + 1,
            No_PO = x.OrderNumber,
            Tanggal = x.Date?.ToString("dd/MM/yyyy") ?? "-",
            Supplier = x.SupplierName ?? "-",
            Total = x.GrandTotal ?? x.Total ?? 0,
            Total_DP = x.DownPayment ?? 0,
            Status = x.Status ?? "-"
        }).ToList();

        return ExportToDocs.ConvertToExcel(exportData);
    }

    /// <summary>
    /// Amount that settles an invoice-exchange row: the full billed amount net of the
    /// discount and PPh23 already recorded on it. Mirrors the outstanding formula used by
    /// AccountPayableService.GetAccountPayableList, so the row lands on exactly 0 there.
    /// </summary>
    private static double SettlementAmount(InvoiceExchange ie)
    {
        var amount = (ie.TotalTransaction ?? 0) - (ie.DiscountAmount ?? 0) - (ie.Pph23Amount ?? 0);
        return amount > 0 ? amount : 0;
    }

    /// <summary>
    /// Manually flags a PO as paid (for payments made outside account-payable/pembayaran)
    /// and settles every invoice-exchange row carrying the same PO number, so the invoice
    /// also reads Paid in Tukar Faktur / Daftar AP / Pembayaran -- all three are views over
    /// public.trx_invoice_exchange.
    ///
    /// Deliberately posts NO journal: the payment happened outside the system and is
    /// normally already recorded from another source (e.g. the Khanza import writing a
    /// 204099 line), so posting again would double-count. The GL liability on Utang
    /// Supplier therefore does not move -- that gap is intentional and is what the
    /// manual_paid flag makes visible.
    ///
    /// Refuses outright if any invoice of this PO has already been touched by the real
    /// payment flow, so a genuine payment record is never overwritten.
    /// Role gating (above Staff) is enforced by the controller.
    /// Returns the number of invoice-exchange rows settled.
    /// </summary>
    public async Task<int> MarkAsPaid(string orderNumber, string note, int userId, int branchId)
    {
        if (string.IsNullOrWhiteSpace(note))
            throw new Exception("Catatan (note) wajib diisi.");

        var po = await _context.PurchaseOrders.FirstOrDefaultAsync(x => x.OrderNumber == orderNumber);
        if (po == null) throw new Exception("Purchase Order not found");

        if (po.ManualPaid)
            throw new Exception("Purchase Order ini sudah ditandai sebagai Paid.");

        // Every invoice of this PO, including rows still "not sent" (belum tukar faktur),
        // so the Tukar Faktur page reflects it too.
        var invoices = await _context.InvoiceExchanges
            .Where(x => x.PONumber == orderNumber)
            .ToListAsync();

        // A row already carrying a real payment must not be overwritten.
        var alreadyPaid = invoices
            .Where(x => !x.ManualPaid
                        && ((x.PaidAmount ?? 0) > 0
                            || (x.PaymentStatus != null
                                && !x.PaymentStatus.Equals("Unpaid", StringComparison.OrdinalIgnoreCase))))
            .ToList();

        if (alreadyPaid.Count > 0)
        {
            var rincian = string.Join(", ", alreadyPaid.Select(x =>
                $"{x.InvoiceNumber ?? $"#{x.InvoiceExchangeId}"} ({x.PaymentStatus ?? "Unpaid"})"));
            throw new Exception(
                $"PO ini tidak bisa ditandai Paid karena {alreadyPaid.Count} faktur sudah dibayar lewat menu Pembayaran: {rincian}. " +
                "Selesaikan sisa pembayarannya lewat menu Pembayaran.");
        }

        var now = DateTime.UtcNow;

        po.ManualPaid = true;
        po.ManualPaidNote = note.Trim();
        po.ManualPaidBy = userId;
        po.ManualPaidAt = now;
        po.UpdatedBy = userId;
        po.UpdatedDate = now;

        foreach (var ie in invoices)
        {
            ie.ManualPaid = true;
            ie.ManualPaidNote = po.ManualPaidNote;
            ie.ManualPaidBy = userId;
            ie.ManualPaidAt = now;

            ie.PaymentStatus = "Paid";
            ie.PaidAmount = SettlementAmount(ie);
            ie.OutstandingAmount = 0;
            ie.PaymentDate = now;

            // PaymentRef / PaymentCoa* / PaymentBatchNumber / PaymentHistory stay untouched:
            // they belong to the real payment flow and their emptiness is the differentiator.
            ie.UpdatedBy = userId;
            ie.UpdatedDate = now;
        }

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PurchaseOrder, "Mark as Paid",
            orderNumber, userId, branchId,
            new
            {
                po.OrderNumber,
                po.SupplierName,
                Total = po.GrandTotal ?? po.Total,
                Note = po.ManualPaidNote,
                MarkedAt = po.ManualPaidAt,
                InvoiceSettled = invoices.Count,
                InvoiceNumbers = invoices.Select(x => x.InvoiceNumber).ToList()
            });

        return invoices.Count;
    }

    /// <summary>
    /// Reverses <see cref="MarkAsPaid"/>: clears the PO flag and puts every invoice row it
    /// settled back to Unpaid. Only rows flagged manual_paid are touched, so an invoice paid
    /// through the real payment flow can never be reopened by this path.
    /// Returns the number of invoice-exchange rows reverted.
    /// </summary>
    public async Task<int> UnmarkAsPaid(string orderNumber, string note, int userId, int branchId)
    {
        if (string.IsNullOrWhiteSpace(note))
            throw new Exception("Alasan pembatalan wajib diisi.");

        var po = await _context.PurchaseOrders.FirstOrDefaultAsync(x => x.OrderNumber == orderNumber);
        if (po == null) throw new Exception("Purchase Order not found");

        if (!po.ManualPaid)
            throw new Exception("Purchase Order ini tidak sedang ditandai Paid.");

        var invoices = await _context.InvoiceExchanges
            .Where(x => x.PONumber == orderNumber && x.ManualPaid)
            .ToListAsync();

        var now = DateTime.UtcNow;
        var previousNote = po.ManualPaidNote;
        var previousMarkedAt = po.ManualPaidAt;

        po.ManualPaid = false;
        po.ManualPaidNote = null;
        po.ManualPaidBy = null;
        po.ManualPaidAt = null;
        po.UpdatedBy = userId;
        po.UpdatedDate = now;

        foreach (var ie in invoices)
        {
            ie.ManualPaid = false;
            ie.ManualPaidNote = null;
            ie.ManualPaidBy = null;
            ie.ManualPaidAt = null;

            ie.PaymentStatus = "Unpaid";
            ie.PaidAmount = 0;
            ie.OutstandingAmount = SettlementAmount(ie);
            ie.PaymentDate = null;

            ie.UpdatedBy = userId;
            ie.UpdatedDate = now;
        }

        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogAction(
            AuditModule.PurchaseOrder, "Batal Mark as Paid",
            orderNumber, userId, branchId,
            new
            {
                po.OrderNumber,
                po.SupplierName,
                Total = po.GrandTotal ?? po.Total,
                Reason = note.Trim(),
                PreviousNote = previousNote,
                PreviousMarkedAt = previousMarkedAt,
                InvoiceReverted = invoices.Count,
                InvoiceNumbers = invoices.Select(x => x.InvoiceNumber).ToList()
            });

        return invoices.Count;
    }

    public async Task<PurchaseOrder?> GetByNumber(string orderNumber)
    {
        return await _context.PurchaseOrders
            .FirstOrDefaultAsync(x => x.OrderNumber == orderNumber);
    }

    public async Task<double> GetTotalDownPayment(string orderNumber)
    {
        var po = await _context.PurchaseOrders
            .Where(x => x.OrderNumber == orderNumber)
            .Select(x => x.DownPayment)
            .FirstOrDefaultAsync();
            
        return po ?? 0;
    }

    public async Task AddDownPayment(string orderNumber, double amount, DateTime date, string note, string coaCode, string coaName, int userId, int branchId)
    {
        var po = await _context.PurchaseOrders.FirstOrDefaultAsync(x => x.OrderNumber == orderNumber);
        if (po == null) throw new Exception("Purchase Order not found");

        var utcDate = DateTime.SpecifyKind(date, DateTimeKind.Utc);

        // Update Total DP
        po.DownPayment = (po.DownPayment ?? 0) + amount;
        po.UpdatedBy = userId;
        po.UpdatedDate = DateTime.UtcNow;

        // Update History
        var historyItem = new DPHistoryItem
        {
            Date = utcDate,
            Amount = amount,
            Note = note,
            CoaCode = coaCode,
            CoaName = coaName,
            CreatedBy = userId
        };

        var historyList = new List<DPHistoryItem>();
        if (!string.IsNullOrEmpty(po.DownPaymentHistory))
        {
            try
            {
                historyList = JsonConvert.DeserializeObject<List<DPHistoryItem>>(po.DownPaymentHistory) ?? new List<DPHistoryItem>();
            }
            catch { /* Start fresh */ }
        }
        
        historyList.Add(historyItem);
        po.DownPaymentHistory = JsonConvert.SerializeObject(historyList);

        // Create Journal for DP
        // Debit: Advance Payment (Uang Muka Pembelian)
        // Credit: Bank/Cash
        
        // TODO: Get Advance Payment COA from Options? For now hardcoded or passed?
        // Let's assume a default Advance Payment COA or use generic logic
        string advancePaymentCoa = "107002"; // Uang Muka Pembelian - Example code

        string journalNo = await _journalService.GenerateJournalNo(utcDate);
        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = utcDate,
            TransactionName = $"Down Payment PO {orderNumber}",
            TransactionRef = orderNumber,
            TransactionType = "DOWN_PAYMENT",
            Description = $"DP for PO {orderNumber} - {note}",
            CoaCode = coaCode, // Credit Account
            CoaName = coaName,
            Amount = amount,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = DateTime.UtcNow,
            status = "Published",
            IsAutoId = true,
            JournalNo = journalNo,
            PaymentDate = utcDate,
            PaymentStatus = "Paid"
        };
        
        await _transactionService.Add(transaction);

        // Journal Debit (Uang Muka)
        var journalDebit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalDesc = $"DP Purchase Order {orderNumber}",
            JournalRef = orderNumber,
            CoaCode = advancePaymentCoa, 
            CoaName = "Uang Muka Pembelian", // Should fetch real name
            DebitAmount = amount,
            CreditAmount = 0,
            Source = "DOWN_PAYMENT",
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = DateTime.UtcNow,
            // WAJIB. Journal.status nullable tanpa default, sedangkan SELURUH perhitungan
            // saldo menyaring status == "Published". Tanpa baris ini jurnal DP tersimpan
            // dengan status NULL dan sama sekali tidak masuk buku besar - bank tidak
            // berkurang dan Uang Muka tidak muncul, padahal uangnya sudah keluar.
            status = transaction.status
        };

        // Journal Credit (Bank)
        var journalCredit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalDesc = $"DP Purchase Order {orderNumber}",
            JournalRef = orderNumber,
            CoaCode = coaCode,
            CoaName = coaName,
            DebitAmount = 0,
            CreditAmount = amount,
            Source = "DOWN_PAYMENT",
            BranchId = branchId,
            CreatedBy = userId,
            status = transaction.status,
            CreatedDate = DateTime.UtcNow
        };

        await _journalService.Add(journalDebit);
        await _journalService.Add(journalCredit);

        await _context.SaveChangesAsync();
    }
}
