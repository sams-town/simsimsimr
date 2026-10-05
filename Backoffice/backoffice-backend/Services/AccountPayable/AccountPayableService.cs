using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Helpers;
using Microsoft.EntityFrameworkCore;
using Newtonsoft.Json;
using TDPBackend.Helpers;

namespace RSSMBackend.Services;

public class AccountPayableService : IAccountPayableService
{
    private readonly RSSMDbContext _context;
    private readonly ITransactionService _transactionService;
    private readonly IJournalService _journalService;
    private readonly IOptionService _optionService;
    private readonly ICOAService _coaService;
    private readonly AuditTrailUtils _auditTrailUtils;

    public AccountPayableService(
        RSSMDbContext context,
        ITransactionService transactionService,
        IJournalService journalService,
        IOptionService optionService,
        ICOAService coaService,
        AuditTrailUtils auditTrailUtils)
    {
        _context = context;
        _transactionService = transactionService;
        _journalService = journalService;
        _optionService = optionService;
        _coaService = coaService;
        _auditTrailUtils = auditTrailUtils;
    }

    public async Task<PagedResult<AccountPayableResponse>> GetAccountPayableList(
        string? keyword,
        string? supplierName,
        string? paymentStatus,
        string? supplierType,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize,
        string? orderBy,
        string? orderType)
    {
        var today = DateTime.UtcNow.Date;

        IQueryable<InvoiceExchange> query = _context.InvoiceExchanges
            .Where(x => x.Status != null && x.Status.ToLower() == "sent"); // Only exchanged invoices

        // Apply keyword filter
        if (!string.IsNullOrEmpty(keyword))
        {
            query = query.Where(x =>
                (x.PONumber != null && x.PONumber.ToLower().Contains(keyword.ToLower())) ||
                (x.SupplierName != null && x.SupplierName.ToLower().Contains(keyword.ToLower())) ||
                (x.InvoiceNumber != null && x.InvoiceNumber.ToLower().Contains(keyword.ToLower()))
            );
        }

        // Apply supplier name filter
        if (!string.IsNullOrEmpty(supplierName))
        {
            query = query.Where(x => x.SupplierName != null && x.SupplierName.ToLower().Contains(supplierName.ToLower()));
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

        // Apply payment status filter
        if (!string.IsNullOrEmpty(paymentStatus))
        {
            if (paymentStatus.Equals("Unpaid", StringComparison.OrdinalIgnoreCase))
            {
                query = query.Where(x => x.PaymentStatus == null || x.PaymentStatus.ToLower() == "unpaid");
            }
            else
            {
                query = query.Where(x => x.PaymentStatus != null && x.PaymentStatus.ToLower() == paymentStatus.ToLower());
            }
        }

        // Apply date filter on due date
        if (startDate != null || endDate != null)
        {
            if (startDate != null && endDate != null)
            {
                var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                query = query.Where(x => x.DueDate != null && x.DueDate >= start && x.DueDate <= end);
            }
            else if (startDate != null && endDate == null)
            {
                var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                query = query.Where(x => x.DueDate != null && x.DueDate >= start);
            }
            else if (startDate == null && endDate != null)
            {
                var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                query = query.Where(x => x.DueDate != null && x.DueDate <= end);
            }
        }

        // Apply ordering
        orderBy = string.IsNullOrEmpty(orderBy) ? "duedate" : orderBy.ToLower();
        orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

        query = orderBy switch
        {
            "suppliername" => orderType == "asc" ? query.OrderBy(x => x.SupplierName) : query.OrderByDescending(x => x.SupplierName),
            "duedate" => orderType == "asc" ? query.OrderBy(x => x.DueDate) : query.OrderByDescending(x => x.DueDate),
            "totaltransaction" => orderType == "asc" ? query.OrderBy(x => x.TotalTransaction) : query.OrderByDescending(x => x.TotalTransaction),
            "ponumber" => orderType == "asc" ? query.OrderBy(x => x.PONumber) : query.OrderByDescending(x => x.PONumber),
            _ => query.OrderBy(x => x.DueDate)
        };

        int total = await query.CountAsync();

        if (page != -1)
        {
            query = query.Skip((page - 1) * pageSize).Take(pageSize);
        }

        var data = await query.ToListAsync();

        // DP PO ikut mengurangi outstanding, sama seperti di halaman detail. Sebelumnya
        // hanya detail yang menguranginya, sehingga halaman Pembayaran menampilkan tagihan
        // penuh dan supplier berisiko dibayar dobel sebesar DP yang sudah disetor.
        var dpAllocation = await AllocateDownPaymentAsync(
            data.Where(x => !string.IsNullOrEmpty(x.PONumber)).Select(x => x.PONumber!).Distinct().ToList());

        var result = data.Select(x =>
        {
            var agingDays = x.DueDate.HasValue ? (int)(today - x.DueDate.Value.Date).TotalDays : 0;

            double dpApplied = 0;
            if (!string.IsNullOrEmpty(x.PONumber) && dpAllocation.TryGetValue(x.InvoiceExchangeId, out var dpForInvoice))
                dpApplied = dpForInvoice;

            var outstandingAmount = (x.TotalTransaction ?? 0) - (x.DiscountAmount ?? 0) - (x.Pph23Amount ?? 0) - dpApplied - (x.PaidAmount ?? 0);
            if (outstandingAmount < 0) outstandingAmount = 0;

            return new AccountPayableResponse
            {
                InvoiceExchangeId = x.InvoiceExchangeId,
                SupplierName = x.SupplierName,
                PONumber = x.PONumber,
                InvoiceNumber = x.InvoiceNumber,
                ReceiptDate = x.ReceiptDate,
                DueDate = x.DueDate,
                TotalTransaction = x.TotalTransaction,
                PaidAmount = x.PaidAmount,
                OutstandingAmount = outstandingAmount,
                Status = x.Status,
                PaymentStatus = x.PaymentStatus ?? "Unpaid",
                AgingDays = agingDays > 0 ? agingDays : 0,
                AgingBucket = GetAgingBucket(agingDays),
                PaymentRef = x.PaymentRef,
                PaymentDate = x.PaymentDate,
                PaymentCoaCode = x.PaymentCoaCode,
                PaymentCoaDesc = x.PaymentCoaDesc,
                TotalDP = dpApplied,
                ManualPaid = x.ManualPaid,
                ManualPaidNote = x.ManualPaidNote,
                ManualPaidAt = x.ManualPaidAt
            };
        }).ToList();

        return new PagedResult<AccountPayableResponse>(result, total);
    }

    /// <summary>
    /// Membagi DP sebuah PO ke SEMUA invoice-nya secara proporsional terhadap nilai
    /// bersih masing-masing invoice. Hasilnya: berapa rupiah DP yang boleh dikurangkan
    /// dari satu invoice tertentu.
    ///
    /// Contoh - PO dengan DP 3.000.000 dan dua invoice (6jt dan 4jt, total 10jt):
    ///   invoice 6jt -> 3jt x 6/10 = 1.800.000
    ///   invoice 4jt -> 3jt x 4/10 = 1.200.000
    ///
    /// Tanpa pembagian ini, DP PENUH akan dikurangkan di setiap invoice - satu PO dengan
    /// DP 3jt dan 2 invoice akan memotong 6jt. Saat ini ada 27 PO dengan lebih dari satu
    /// invoice, jadi ini bukan kasus teoretis. Untuk PO dengan satu invoice hasilnya sama
    /// persis dengan perilaku lama.
    /// </summary>
    // Role yang boleh membatalkan pembayaran: Administrator(1) dan Manager(2).
    // Allow-list, bukan deny-list, supaya role baru default-nya TIDAK boleh membatalkan.
    private static readonly int[] CancelPaymentRoles = { 1, 2 };

    private void EnsureCanCancelPayment(int roleId)
    {
        // Dicek di service, bukan hanya lewat [Authorize], karena
        // AuthorizeAttribute.OnAuthorization adalah async void sehingga hasil await-nya
        // tidak dijamin selesai sebelum action berjalan.
        if (!CancelPaymentRoles.Contains(roleId))
            throw new Exception("Hanya Administrator atau Manager yang boleh membatalkan pembayaran.");
    }

    /// <summary>Berapa yang harus dibalik dari satu invoice untuk sebuah payment_ref.</summary>
    private (double Paid, double Discount, double Pph23, List<PaymentHistoryItem> Sisa) ResolveReversal(
        InvoiceExchange invoice, string paymentRef)
    {
        var history = new List<PaymentHistoryItem>();
        if (!string.IsNullOrEmpty(invoice.PaymentHistory))
        {
            try { history = JsonConvert.DeserializeObject<List<PaymentHistoryItem>>(invoice.PaymentHistory) ?? new(); }
            catch { history = new(); }
        }

        var dibatalkan = history.Where(h => h.PaymentRef == paymentRef).ToList();
        var sisa = history.Where(h => h.PaymentRef != paymentRef).ToList();

        if (dibatalkan.Count > 0)
        {
            double paid = dibatalkan.Sum(h => h.Amount);
            // Entri lama belum menyimpan diskon/PPh23. Kalau pembatalan ini mengembalikan
            // invoice ke belum-bayar sama sekali, nilai di invoice memang milik pembayaran
            // itu; kalau masih ada cicilan lain, tidak ada dasar untuk membaginya sehingga
            // sengaja 0 - lebih baik kurang membalik daripada membalik yang bukan haknya.
            bool adaNilaiTercatat = dibatalkan.Any(h => h.DiscountAmount.HasValue || h.Pph23Amount.HasValue);
            double disc = adaNilaiTercatat
                ? dibatalkan.Sum(h => h.DiscountAmount ?? 0)
                : (sisa.Count == 0 ? invoice.DiscountAmount ?? 0 : 0);
            double pph = adaNilaiTercatat
                ? dibatalkan.Sum(h => h.Pph23Amount ?? 0)
                : (sisa.Count == 0 ? invoice.Pph23Amount ?? 0 : 0);
            return (paid, disc, pph, sisa);
        }

        // Riwayat ADA tapi tidak satu pun cocok dengan ref ini: jangan menebak. Membalik
        // "seluruh yang terbayar" di sini akan menghapus riwayat cicilan lain yang sah.
        if (history.Count > 0)
            throw new Exception(
                $"Invoice {invoice.InvoiceNumber} tidak punya riwayat pembayaran dengan referensi {paymentRef}. " +
                "Pembatalan dihentikan agar riwayat pembayaran lain tidak ikut terhapus.");

        // Benar-benar tanpa riwayat (pembayaran lama, sebelum payment_history dipakai):
        // balikkan seluruh yang tercatat terbayar di invoice.
        return (invoice.PaidAmount ?? 0, invoice.DiscountAmount ?? 0, invoice.Pph23Amount ?? 0, new List<PaymentHistoryItem>());
    }

    /// <summary>
    /// Membatalkan pembayaran sejumlah invoice yang berbagi satu payment_ref, dalam SATU
    /// transaksi jurnal pembalik. Dipakai baik untuk pembatalan satu invoice maupun
    /// seluruh batch - bedanya cuma daftar invoice yang dikirim.
    ///
    /// Tidak menghapus apa pun: jurnal pembayaran asli tetap ada, dan pembalikannya
    /// dicatat sebagai transaksi PaymentCancel tersendiri.
    /// </summary>
    private async Task<object> ReversePaymentsAsync(
        List<InvoiceExchange> invoices, string paymentRef, string? reason, int branchId, int userId)
    {
        if (invoices.Count == 0)
            throw new Exception("Tidak ada invoice yang bisa dibatalkan pembayarannya.");

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

        // Jurnal pembalik ditanggalkan sama dengan pembayaran aslinya supaya keduanya
        // saling meniadakan di bulan yang sama. Karena itu periodenya tidak boleh ditutup.
        var paymentDate = invoices.Select(i => i.PaymentDate).FirstOrDefault(d => d.HasValue)
                          ?? DateTime.SpecifyKind(utcNow.Date, DateTimeKind.Utc);
        paymentDate = DateTime.SpecifyKind(paymentDate.Date, DateTimeKind.Utc);

        bool periodClosed = await _context.PeriodClosings.AnyAsync(p => p.Status == "Closed"
            && p.Year == paymentDate.Year
            && (p.Month == null || p.Month == paymentDate.Month)
            && (p.BranchId == null || p.BranchId == branchId));
        if (periodClosed)
            throw new Exception($"Periode {paymentDate:MM/yyyy} sudah ditutup. Pembayaran ini tidak bisa dibatalkan.");

        // COA yang dipakai balik: bank dari invoice, utang/diskon/pph dari konfigurasi.
        var optionSupplierDebt = await _optionService.GetByName("ACCOUNT_UTANG_SUPPLIER", branchId);
        string coaSupplierDebt = optionSupplierDebt?.OptionValue ?? "201001";
        var supplierDebtCoa = await _coaService.GetByCode(coaSupplierDebt, branchId);

        var optionDiscount = await _optionService.GetByName("ACCOUNT_AP_DISCOUNT", branchId);
        string coaDiscountCode = optionDiscount?.OptionValue ?? "404001";
        var discountCoa = await _coaService.GetByCode(coaDiscountCode, branchId);

        var optionPPh = await _optionService.GetByName("ACCOUNT_AP_PPH23", branchId);
        string coaPPh23Code = optionPPh?.OptionValue ?? "206002";
        var pphCoa = await _coaService.GetByCode(coaPPh23Code, branchId);

        string bankCoaCode = invoices.Select(i => i.PaymentCoaCode).FirstOrDefault(c => !string.IsNullOrEmpty(c)) ?? "";
        if (string.IsNullOrEmpty(bankCoaCode))
            throw new Exception("COA bank pembayaran tidak diketahui, pembatalan dihentikan agar jurnal tidak timpang.");
        var bankCoa = await _coaService.GetByCode(bankCoaCode, branchId);

        // Hitung dulu semua pembalikan sebelum menulis apa pun.
        var reversals = new List<(InvoiceExchange Inv, double Paid, double Disc, double Pph, List<PaymentHistoryItem> Sisa)>();
        foreach (var inv in invoices)
        {
            var r = ResolveReversal(inv, paymentRef);
            if (r.Paid <= 0 && r.Discount <= 0 && r.Pph23 <= 0) continue;
            reversals.Add((inv, r.Paid, r.Discount, r.Pph23, r.Sisa));
        }

        if (reversals.Count == 0)
            throw new Exception("Tidak ada nilai pembayaran yang bisa dibalik pada invoice tersebut.");

        double totalPaid = reversals.Sum(r => r.Paid);
        double totalDisc = reversals.Sum(r => r.Disc);
        double totalPph = reversals.Sum(r => r.Pph);

        string transactionType = "PaymentCancel";
        string transactionNo = $"CNL-{paymentRef}";
        string journalNo = await _journalService.GenerateJournalNo(paymentDate);

        var cancelTrx = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = paymentDate,
            TransactionName = $"Pembatalan Pembayaran AP {paymentRef}",
            TransactionRef = paymentRef,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Pembatalan Pembayaran AP Ref: {paymentRef} - {reversals.Count} invoice"
                          + (string.IsNullOrWhiteSpace(reason) ? "" : $" | Alasan: {reason.Trim()}"),
            CoaCode = bankCoaCode,
            CoaName = bankCoa?.CoaName,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = totalPaid,
            CoaParentCode = bankCoa?.ParentCode,
            CoaParentName = bankCoa?.ParentName
        };
        await _transactionService.Add(cancelTrx);

        // Kebalikan dari jurnal pembayaran: dulu Cr Bank / Dr Utang, sekarang Dr Bank / Cr Utang.
        if (totalPaid > 0)
        {
            await _journalService.Add(new Journal
            {
                TransactionId = cancelTrx.TransactionId,
                JournalRef = paymentRef,
                JournalDesc = $"Pembatalan Pembayaran AP {paymentRef}",
                CoaCode = bankCoaCode,
                CoaName = bankCoa?.CoaName,
                CoaParentCode = bankCoa?.ParentCode,
                CoaParentName = bankCoa?.ParentName,
                DebitAmount = totalPaid,
                CreditAmount = 0,
                Source = transactionType,
                status = cancelTrx.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = true
            });
        }

        foreach (var r in reversals)
        {
            double utang = r.Paid + r.Disc + r.Pph;
            if (utang > 0)
            {
                await _journalService.Add(new Journal
                {
                    TransactionId = cancelTrx.TransactionId,
                    JournalRef = paymentRef,
                    JournalDesc = $"Pembatalan Hutang {r.Inv.SupplierName} - {r.Inv.InvoiceNumber}",
                    CoaCode = coaSupplierDebt,
                    CoaName = supplierDebtCoa?.CoaName,
                    CoaParentCode = supplierDebtCoa?.ParentCode,
                    CoaParentName = supplierDebtCoa?.ParentName,
                    DebitAmount = 0,
                    CreditAmount = utang,
                    Source = transactionType,
                    status = cancelTrx.status,
                    BranchId = branchId,
                    CreatedBy = userId,
                    CreatedDate = utcNow,
                    IsPremier = false
                });
            }
        }

        if (totalDisc > 0)
        {
            await _journalService.Add(new Journal
            {
                TransactionId = cancelTrx.TransactionId,
                JournalRef = paymentRef,
                JournalDesc = $"Pembatalan Diskon Pembayaran {paymentRef}",
                CoaCode = coaDiscountCode,
                CoaName = discountCoa?.CoaName,
                CoaParentCode = discountCoa?.ParentCode,
                CoaParentName = discountCoa?.ParentName,
                DebitAmount = totalDisc,
                CreditAmount = 0,
                Source = transactionType,
                status = cancelTrx.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            });
        }

        if (totalPph > 0)
        {
            await _journalService.Add(new Journal
            {
                TransactionId = cancelTrx.TransactionId,
                JournalRef = paymentRef,
                JournalDesc = $"Pembatalan PPh23 Pembayaran {paymentRef}",
                CoaCode = coaPPh23Code,
                CoaName = pphCoa?.CoaName,
                CoaParentCode = pphCoa?.ParentCode,
                CoaParentName = pphCoa?.ParentName,
                DebitAmount = totalPph,
                CreditAmount = 0,
                Source = transactionType,
                status = cancelTrx.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            });
        }

        // Kembalikan kondisi invoice.
        foreach (var r in reversals)
        {
            var inv = r.Inv;
            double sisaTerbayar = Math.Max((inv.PaidAmount ?? 0) - r.Paid, 0);

            inv.PaidAmount = sisaTerbayar;
            inv.PaymentHistory = r.Sisa.Count > 0 ? JsonConvert.SerializeObject(r.Sisa) : null;
            inv.PaymentStatus = sisaTerbayar > 0 ? "Partial" : "Unpaid";

            if (sisaTerbayar <= 0)
            {
                inv.PaymentRef = null;
                inv.PaymentDate = null;
                inv.PaymentCoaCode = null;
                inv.PaymentCoaDesc = null;
                inv.PaymentBatchNumber = null;
                inv.PaymentBy = null;
                // Diskon & PPh23 ikut dikembalikan hanya bila memang dibalik, supaya
                // outstanding invoice kembali seperti sebelum dibayar.
                if (r.Disc > 0) inv.DiscountAmount = Math.Max((inv.DiscountAmount ?? 0) - r.Disc, 0);
                if (r.Pph > 0) inv.Pph23Amount = Math.Max((inv.Pph23Amount ?? 0) - r.Pph, 0);
            }

            inv.PaymentCancelledBy = userId;
            inv.PaymentCancelledDate = utcNow;
            inv.PaymentCancelReason = string.IsNullOrWhiteSpace(reason) ? null : reason.Trim();
            inv.PaymentCancelTransactionId = cancelTrx.TransactionId;
            inv.UpdatedBy = userId;
            inv.UpdatedDate = utcNow;
        }

        await _context.SaveChangesAsync();

        // Satu entri audit PER INVOICE dengan ref_id = InvoiceExchangeId, mengikuti pola
        // "Pembayaran" dan "Pembayaran Batch". Timeline invoice memfilter berdasarkan
        // (module, ref_id), jadi kalau dicatat sebagai satu entri dengan ref_id = payment_ref
        // pembatalannya tidak akan pernah muncul di riwayat invoice mana pun.
        await _auditTrailUtils.LogActionEntries(
            AuditModule.InvoiceExchange, "Batal Pembayaran",
            reversals.Select(r => (r.Inv.InvoiceExchangeId.ToString(), (object?)new
            {
                r.Inv.InvoiceNumber,
                r.Inv.SupplierName,
                PaymentRef = paymentRef,
                DibalikNominal = r.Paid,
                DibalikDiskon = r.Disc,
                DibalikPph23 = r.Pph,
                PaidAmountSesudah = r.Inv.PaidAmount,
                PaymentStatusSesudah = r.Inv.PaymentStatus,
                JurnalPembalik = journalNo,
                CakupanPembatalan = reversals.Count == 1 ? "Per invoice" : $"Batch {reversals.Count} invoice",
                Alasan = reason
            })),
            userId, branchId);

        return new
        {
            paymentRef,
            cancelJournalNo = journalNo,
            invoiceCount = reversals.Count,
            totalReversed = totalPaid,
            invoiceIds = reversals.Select(r => r.Inv.InvoiceExchangeId).ToList()
        };
    }

    /// <summary>Membatalkan pembayaran SATU invoice saja; invoice lain di ref yang sama tidak tersentuh.</summary>
    public async Task<object> CancelInvoicePayment(int invoiceExchangeId, string? reason, int roleId, int branchId, int userId)
    {
        EnsureCanCancelPayment(roleId);

        var invoice = await _context.InvoiceExchanges
            .FirstOrDefaultAsync(x => x.InvoiceExchangeId == invoiceExchangeId);

        if (invoice == null)
            throw new Exception("Invoice tidak ditemukan.");
        if ((invoice.PaidAmount ?? 0) <= 0)
            throw new Exception($"Invoice {invoice.InvoiceNumber} belum ada pembayaran yang bisa dibatalkan.");

        string paymentRef = invoice.PaymentRef ?? $"INV-{invoice.InvoiceExchangeId}";
        return await ReversePaymentsAsync(new List<InvoiceExchange> { invoice }, paymentRef, reason, branchId, userId);
    }

    /// <summary>Membatalkan seluruh pembayaran dengan payment_ref yang sama (satu batch).</summary>
    public async Task<object> CancelBatchPayment(string paymentRef, string? reason, int roleId, int branchId, int userId)
    {
        EnsureCanCancelPayment(roleId);

        if (string.IsNullOrWhiteSpace(paymentRef))
            throw new Exception("Nomor referensi pembayaran wajib diisi.");

        var invoices = await _context.InvoiceExchanges
            .Where(x => x.PaymentRef == paymentRef && x.PaidAmount > 0)
            .ToListAsync();

        if (invoices.Count == 0)
            throw new Exception($"Tidak ada invoice terbayar dengan referensi {paymentRef}.");

        return await ReversePaymentsAsync(invoices, paymentRef, reason, branchId, userId);
    }

    private async Task<Dictionary<int, double>> AllocateDownPaymentAsync(List<string> poNumbers)
    {
        var allocation = new Dictionary<int, double>();
        if (poNumbers.Count == 0) return allocation;

        var dpByPo = await _context.PurchaseOrders
            .Where(p => poNumbers.Contains(p.OrderNumber) && p.DownPayment != null && p.DownPayment > 0)
            .Select(p => new { p.OrderNumber, Dp = p.DownPayment ?? 0 })
            .ToDictionaryAsync(p => p.OrderNumber, p => p.Dp);

        if (dpByPo.Count == 0) return allocation;

        // Semua invoice dari PO ber-DP diambil, bukan hanya yang sedang ditampilkan, supaya
        // pembaginya (total nilai invoice) sama di halaman mana pun. Kalau hanya memakai
        // invoice yang tampil di satu halaman, proporsinya akan berubah-ubah mengikuti filter.
        var poWithDp = dpByPo.Keys.ToList();
        var invoices = await _context.InvoiceExchanges
            .Where(i => i.PONumber != null && poWithDp.Contains(i.PONumber))
            .Select(i => new
            {
                i.InvoiceExchangeId,
                i.PONumber,
                i.ReceiptDate,
                i.TotalTransaction,
                i.DiscountAmount,
                i.Pph23Amount
            })
            .ToListAsync();

        foreach (var group in invoices.GroupBy(i => i.PONumber!))
        {
            double dpTotal = dpByPo[group.Key];

            var eligible = group
                .Select(i => new
                {
                    i.InvoiceExchangeId,
                    i.ReceiptDate,
                    Nett = Math.Max((i.TotalTransaction ?? 0) - (i.DiscountAmount ?? 0) - (i.Pph23Amount ?? 0), 0)
                })
                .Where(i => i.Nett > 0)
                .OrderBy(i => i.ReceiptDate ?? DateTime.MaxValue)
                .ThenBy(i => i.InvoiceExchangeId)
                .ToList();

            if (eligible.Count == 0) continue;

            double totalNett = eligible.Sum(i => i.Nett);

            // DP tidak boleh melebihi total nilai invoice-nya. Kalau DP lebih besar,
            // kelebihannya tidak dialokasikan ke mana pun dan tetap menggantung di akun
            // Uang Muka - itu memang kondisi yang perlu ditindaklanjuti manual.
            double dpToShare = Math.Min(dpTotal, totalNett);

            double assigned = 0;
            for (int idx = 0; idx < eligible.Count; idx++)
            {
                var inv = eligible[idx];

                // Invoice terakhir menyerap sisa pembulatan, supaya total alokasi persis
                // sama dengan DP - tidak ada rupiah yang hilang maupun tercipta.
                double share = idx == eligible.Count - 1
                    ? dpToShare - assigned
                    : MoneyRounding.Round(dpToShare * inv.Nett / totalNett);

                if (share > inv.Nett) share = inv.Nett;
                if (share <= 0) continue;

                allocation[inv.InvoiceExchangeId] = share;
                assigned += share;
            }
        }

        return allocation;
    }

    private string GetAgingBucket(int agingDays)
    {
        if (agingDays <= 0) return "Current";
        if (agingDays <= 30) return "1-30";
        if (agingDays <= 60) return "31-60";
        if (agingDays <= 90) return "61-90";
        return ">90";
    }

    public async Task<AccountPayableDetailResponse?> GetAccountPayableById(int invoiceExchangeId, int branchId = 1)
    {
        var today = DateTime.Today;
        
        var invoice = await _context.InvoiceExchanges
            .Where(x => x.InvoiceExchangeId == invoiceExchangeId && x.Status == "Sent")
            .FirstOrDefaultAsync();

        if (invoice == null)
            return null;

        var agingDays = invoice.DueDate.HasValue ? (int)(today - invoice.DueDate.Value.Date).TotalDays : 0;
        var totalTransaction = invoice.TotalTransaction ?? 0;
        var paidAmount = invoice.PaidAmount ?? 0;
        var outstandingAmount = totalTransaction - paidAmount;

        // Discount calculation
        double discountPercent = invoice.DiscountPercent ?? 0;
        double discountAmount = invoice.DiscountAmount ?? 0;
        double totalAfterDiscount = totalTransaction - discountAmount;
        
        // PPH23 calculation
        double pph23Percent = invoice.Pph23Percent ?? 0;
        double pph23Amount = invoice.Pph23Amount ?? 0;

        // PPN Calculation (Fetch from Journal)
        double ppnAmount = 0;
        if (!string.IsNullOrEmpty(invoice.PONumber))
        {
            var ppnOption = await _optionService.GetByName("ACCOUNT_PPN_MASUKAN", branchId);
            var ppnCoaCode = ppnOption?.OptionValue ?? "108006"; // Default PPN Masukan
            
            // PPN = SELURUH mutasi akun PPN Masukan atas PO ini, bukan satu baris saja.
            // Khanza memposting satu baris per penerimaan barang, dan pembatalan
            // penerimaan diposting sebagai KREDIT ke akun yang sama. Mengambil satu baris
            // terbaru (perilaku lama) membuat PO dengan beberapa penerimaan tampil
            // sebagian saja, dan pada PO yang punya penerimaan + pembatalan bertimestamp
            // sama hasilnya bahkan tidak menentu karena urutannya seri.
            ppnAmount = await _context.Journals
                .Where(x => x.PONumber == invoice.PONumber && x.CoaCode == ppnCoaCode)
                .SumAsync(x => (x.DebitAmount ?? 0) - (x.CreditAmount ?? 0));
        }
        else
        {
            // Invoice non-PO (input manual): PPN disimpan langsung di invoice,
            // bukan diturunkan dari jurnal PO.
            ppnAmount = invoice.PpnAmount ?? 0;
        }

        // DP Calculation from Purchase Order
        var dpList = new List<DPItemResponse>();
        double totalDP = 0;

        if (!string.IsNullOrEmpty(invoice.PONumber))
        {
            var po = await _context.PurchaseOrders
                .Where(x => x.OrderNumber == invoice.PONumber)
                .Select(x => new { x.DownPayment, x.DownPaymentHistory })
                .FirstOrDefaultAsync();

            if (po != null)
            {
                // DP yang boleh dikurangkan dari invoice INI - bukan DP penuh PO. Untuk PO
                // dengan satu invoice hasilnya identik; untuk PO bermultipel invoice, DP
                // dibagi proporsional ke semua invoice supaya tidak terpotong berkali-kali.
                var dpAllocation = await AllocateDownPaymentAsync(new List<string> { invoice.PONumber });
                totalDP = dpAllocation.TryGetValue(invoice.InvoiceExchangeId, out var dpForInvoice) ? dpForInvoice : 0;

                if (!string.IsNullOrEmpty(po.DownPaymentHistory))
                {
                    try
                    {
                        var history = JsonConvert.DeserializeObject<List<DPHistoryItem>>(po.DownPaymentHistory);
                        if (history != null)
                        {
                            dpList = history.Select(x => new DPItemResponse
                            {
                                DpDate = x.Date,
                                Amount = x.Amount,
                                Note = x.Note,
                                PaymentMethod = x.CoaName // Using COA Name as method
                            }).ToList();
                        }
                    }
                    catch { }
                }
            }
        }
        
        // Final Outstanding Calculation: Total - Disc - PPH - DP - Paid
        outstandingAmount = totalTransaction - discountAmount - pph23Amount - totalDP - paidAmount;
        if (outstandingAmount < 0) outstandingAmount = 0;

        // Parse Payment History
        var paymentHistory = new List<PaymentHistoryItem>();
        if (!string.IsNullOrEmpty(invoice.PaymentHistory))
        {
            try
            {
                paymentHistory = JsonConvert.DeserializeObject<List<PaymentHistoryItem>>(invoice.PaymentHistory) ?? new List<PaymentHistoryItem>();
            }
            catch { }
        }

        // Nama petugas yang menandai Paid manual -- ditampilkan di panel khusus pada detail,
        // menggantikan blok pembayaran normal yang memang kosong untuk jalur ini.
        string? manualPaidByName = null;
        if (invoice.ManualPaid && invoice.ManualPaidBy != null)
        {
            manualPaidByName = await _context.Users
                .Where(u => u.UserID == invoice.ManualPaidBy)
                .Select(u => u.DisplayName)
                .FirstOrDefaultAsync();
        }

        return new AccountPayableDetailResponse
        {
            InvoiceExchangeId = invoice.InvoiceExchangeId,
            SupplierName = invoice.SupplierName,
            PONumber = invoice.PONumber,
            InvoiceNumber = invoice.InvoiceNumber,
            ReceiptDate = invoice.ReceiptDate,
            DueDate = invoice.DueDate,
            TotalTransaction = totalTransaction,
            Status = invoice.Status,
            PaymentStatus = invoice.PaymentStatus ?? "Unpaid",
            AgingDays = agingDays > 0 ? agingDays : 0,
            AgingBucket = GetAgingBucket(agingDays),
            PaymentRef = invoice.PaymentRef,
            PaymentDate = invoice.PaymentDate,
            PaymentCoaCode = invoice.PaymentCoaCode,
            PaymentCoaDesc = invoice.PaymentCoaDesc,
            PaidAmount = paidAmount,
            DiscountPercent = discountPercent,
            DiscountAmount = discountAmount,
            TotalAfterDiscount = totalAfterDiscount,
            Pph23Percent = pph23Percent,
            Pph23Amount = pph23Amount,
            PpnAmount = ppnAmount,
            DPList = dpList,
            TotalDP = totalDP,
            PpnPercent = invoice.PpnPercent,
            OutstandingAmount = outstandingAmount,
            PaymentBatchNumber = invoice.PaymentBatchNumber,
            PaymentHistory = paymentHistory,
            ManualPaid = invoice.ManualPaid,
            ManualPaidNote = invoice.ManualPaidNote,
            ManualPaidAt = invoice.ManualPaidAt,
            ManualPaidBy = invoice.ManualPaidBy,
            ManualPaidByName = manualPaidByName
        };
    }

    public async Task<int> ProcessPayment(AccountPayablePaymentRequest request, int userId, int branchId)
    {
        if (request.InvoiceIds == null || request.InvoiceIds.Count == 0)
            return 0;

        var invoices = await _context.InvoiceExchanges
            .Where(x => request.InvoiceIds.Contains(x.InvoiceExchangeId) && 
                       (x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid"))
            .ToListAsync();

        if (invoices.Count == 0)
            return 0;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var paymentDate = DateTime.SpecifyKind(request.PaymentDate.Date, DateTimeKind.Utc);
        int updatedCount = 0;
        double totalPaidAmount = 0;
        double totalIncrementalDiscount = 0;
        double totalIncrementalPPh23 = 0;

        // 1. Fetch Configuration (COA)
        var optionSupplierDebt = await _optionService.GetByName("ACCOUNT_UTANG_SUPPLIER", branchId);
        string coaSupplierDebt = optionSupplierDebt?.OptionValue ?? "201001"; // Default Utang Usaha / Hutang Supplier

        // Validate Bank COA
        var bankCoa = await _coaService.GetByCode(request.CoaCode, branchId);
        if (bankCoa == null)
            throw new Exception($"Bank/Cash Account with code '{request.CoaCode}' not found.");

        foreach (var invoice in invoices)
        {
            // Handle Partial Payment
            double currentPaidAmount = invoice.PaidAmount ?? 0;
            double transactionAmount = request.Amount; // This is the amount being paid NOW
            
            // If paying multiple invoices, we might need logic to distribute amount. 
            // BUT usually bulk payment matches total selected.
            // For single invoice payment or full payment of multiple, we assume request.Amount in total matches.
            
            // However, if we support partial payment for specific invoice, the request really should handle per-invoice amount.
            // Current request model `AccountPayablePaymentRequest` has `Amount` (total) and `InvoiceIds` (list).
            // If list > 1, assume full payment for all? Or distributed?
            // User requirement says "support data partial payment".
            // Let's assume for now if count > 1 it's full payment for all (complex to split manual amount).
            // If count == 1, we use request.Amount.

            double amountToPayForThisInvoice = 0;
            if (invoices.Count == 1)
            {
                amountToPayForThisInvoice = request.Amount;
            }
            else
            {
                // Bulk payment - assume paying off the outstanding balance for each
                // OR user sends exact total matches sum of all outstanding.
                // Safest default for bulk is Pay Full Outstanding.
                double invTotal = invoice.TotalTransaction ?? 0;
                double invPaid = invoice.PaidAmount ?? 0;
                amountToPayForThisInvoice = invTotal - invPaid - (invoice.DiscountAmount ?? 0) - (invoice.Pph23Amount ?? 0) - /* TotalDP from PO? N/A in this scope yet */ 0; 
            }

            // Update Paid Amount
            invoice.PaidAmount = currentPaidAmount + amountToPayForThisInvoice;
            
            // Calculate increments for journaling
            double oldDiscount = invoice.DiscountAmount ?? 0;
            double oldPPh23 = invoice.Pph23Amount ?? 0;
            double newDiscount = request.DiscountAmount ?? 0;
            double newPPh23 = request.Pph23Amount ?? 0;

            totalIncrementalDiscount += (newDiscount - oldDiscount);
            totalIncrementalPPh23 += (newPPh23 - oldPPh23);

            // Set discount and PPH23 info
            invoice.DiscountPercent = request.DiscountPercent;
            invoice.DiscountAmount = request.DiscountAmount;
            invoice.Pph23Percent = request.Pph23Percent;
            invoice.Pph23Amount = request.Pph23Amount;

            // Recalculate Outstanding
            double totalTrx = invoice.TotalTransaction ?? 0;
            double disc = invoice.DiscountAmount ?? 0;
            double pph = invoice.Pph23Amount ?? 0;
            double paid = invoice.PaidAmount ?? 0;
            
            double outstanding = totalTrx - disc - pph - paid;
            invoice.OutstandingAmount = outstanding;

            // Determine Status
            if (outstanding <= 100) // Tolerance for small rounding diffs
            {
                invoice.PaymentStatus = "Paid";
                invoice.OutstandingAmount = 0; // Force 0 if within tolerance
            }
            else
            {
                invoice.PaymentStatus = "Partial";
            }

            invoice.PaymentDate = paymentDate;
            invoice.PaymentRef = request.PaymentRef;
            invoice.PaymentCoaCode = request.CoaCode;
            invoice.PaymentCoaDesc = request.CoaDesc;
            invoice.PaymentBy = userId;
            invoice.UpdatedBy = userId;
            invoice.UpdatedDate = utcNow;
            
            // Update Payment History
            var historyItem = new PaymentHistoryItem
            {
                PaymentDate = paymentDate,
                Amount = amountToPayForThisInvoice,
                Note = $"Payment Ref: {request.PaymentRef}",
                CoaCode = request.CoaCode,
                CoaName = request.CoaDesc,
                PaymentRef = request.PaymentRef,
                PaidBy = userId,
                // Dicatat agar pembatalan pembayaran bisa membalik jurnal dengan angka
                // yang persis, bukan menebak dari nilai invoice saat ini.
                DiscountAmount = newDiscount - oldDiscount,
                Pph23Amount = newPPh23 - oldPPh23
            };
            
            var historyList = new List<PaymentHistoryItem>();
            if (!string.IsNullOrEmpty(invoice.PaymentHistory))
            {
                try 
                {
                    historyList = JsonConvert.DeserializeObject<List<PaymentHistoryItem>>(invoice.PaymentHistory) ?? new List<PaymentHistoryItem>();
                }
                catch { /* Ignore parse error, start fresh */ }
            }
            
            historyList.Add(historyItem);
            invoice.PaymentHistory = JsonConvert.SerializeObject(historyList);

            // Ensure UTC for all date fields
            if (invoice.ReceiptDate.HasValue && invoice.ReceiptDate.Value.Kind != DateTimeKind.Utc)
                invoice.ReceiptDate = DateTime.SpecifyKind(invoice.ReceiptDate.Value, DateTimeKind.Utc);
            if (invoice.PODate.HasValue && invoice.PODate.Value.Kind != DateTimeKind.Utc)
                invoice.PODate = DateTime.SpecifyKind(invoice.PODate.Value, DateTimeKind.Utc);
            if (invoice.DueDate.HasValue && invoice.DueDate.Value.Kind != DateTimeKind.Utc)
                invoice.DueDate = DateTime.SpecifyKind(invoice.DueDate.Value, DateTimeKind.Utc);
            if (invoice.InsertedDate.HasValue && invoice.InsertedDate.Value.Kind != DateTimeKind.Utc)
                invoice.InsertedDate = DateTime.SpecifyKind(invoice.InsertedDate.Value, DateTimeKind.Utc);
                
            totalPaidAmount += amountToPayForThisInvoice;
            updatedCount++;
        }

        // 2. Create Transaction Header (One transaction for the bulk payment or per invoice? 
        // Request comes with one PaymentRef and one Total Amount (calced from invoices).
        // Usually bulk payment = 1 Transaction.

        // Calculate Total Paid
        double totalPaid = totalPaidAmount;
        // Fix: Use incremental values for journaling to avoid double-counting in partial payments
        double totalDiscount = totalIncrementalDiscount;
        double totalPPh23 = totalIncrementalPPh23;

        string transactionType = "Payment";
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(request.PaymentDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = paymentDate,
            TransactionName = $"Payment AP {request.PaymentRef}",
            TransactionRef = request.PaymentRef,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Payment AP Ref: {request.PaymentRef} - {invoices.Count} Invoices",
            CoaCode = request.CoaCode, // Credit Account (Bank)
            CoaName = request.CoaDesc,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double?)totalPaid,
            PaymentNo = request.PaymentRef,
            PaymentDate = paymentDate,
            PaymentStatus = "Paid",
            CoaParentCode = bankCoa.ParentCode,
            CoaParentName = bankCoa.ParentName
        };

        await _transactionService.Add(transaction);

        // 3. Create Journals

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
            CreditAmount = (double?)totalPaid,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalBank);

        // A.2 Credit Discount (If any)
        if (totalDiscount > 0)
        {
            var optionDiscount = await _optionService.GetByName("ACCOUNT_AP_DISCOUNT", branchId);
            var coaDiscount = optionDiscount?.OptionValue ?? "404001"; // Pendapatan Lain-Lain / Discount
            var discountCoa = await _coaService.GetByCode(coaDiscount, branchId);
            
            var journalDiscount = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"Discount - {transaction.Description}",
                CoaCode = coaDiscount,
                CoaName = discountCoa?.CoaName ?? "Discount",
                CoaParentCode = discountCoa?.ParentCode,
                CoaParentName = discountCoa?.ParentName,
                DebitAmount = 0,
                CreditAmount = (double?)totalDiscount,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            };
            await _journalService.Add(journalDiscount);
        }

        // A.3 Credit PPh 23 (If any)
        if (totalPPh23 > 0)
        {
            var optionPPh = await _optionService.GetByName("ACCOUNT_AP_PPH23", branchId);
            var coaPPh23 = optionPPh?.OptionValue ?? "206002"; // Pph Pasal 23 / 26
            var pphCoa = await _coaService.GetByCode(coaPPh23, branchId);
            
            var journalPPh = new Journal
            {
                TransactionId = transaction.TransactionId,
                JournalRef = transaction.TransactionRef,
                JournalDesc = $"PPh 23 - {transaction.Description}",
                CoaCode = coaPPh23,
                CoaName = pphCoa?.CoaName ?? "PPh 23 Payable",
                CoaParentCode = pphCoa?.ParentCode,
                CoaParentName = pphCoa?.ParentName,
                DebitAmount = 0,
                CreditAmount = (double?)totalPPh23,
                Source = transactionType,
                status = transaction.status,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = utcNow,
                IsPremier = false
            };
            await _journalService.Add(journalPPh);
        }

        // B. Debit AP Account (Debet Utang)
        // We aggregate the total amount to debit the AP account
        
        var supplierDebtCoa = await _coaService.GetByCode(coaSupplierDebt, branchId);
        
        var journalDebit = new Journal
        {
            TransactionId = transaction.TransactionId,
            JournalRef = transaction.TransactionRef,
            JournalDesc = $"Pembayaran Hutang Supplier - {request.PaymentRef}",
            CoaCode = coaSupplierDebt,
            CoaName = supplierDebtCoa?.CoaName ?? "",
            CoaParentCode = supplierDebtCoa?.ParentCode,
            CoaParentName = supplierDebtCoa?.ParentName,
            DebitAmount = (double?)(totalPaid + totalDiscount + totalPPh23),
            CreditAmount = 0,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = false
        };
        await _journalService.Add(journalDebit);

        _context.InvoiceExchanges.UpdateRange(invoices);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.InvoiceExchange, "Pembayaran",
            invoices.Select(i => (i.InvoiceExchangeId.ToString(), (object?)new
            {
                i.InvoiceNumber,
                i.SupplierName,
                PaymentDate = paymentDate,
                request.PaymentRef,
                request.CoaCode,
                request.CoaDesc,
                i.PaidAmount,
                i.OutstandingAmount,
                i.PaymentStatus
            })),
            userId, branchId);

        return updatedCount;
    }

    public async Task<int> ProcessBatchPayment(AccountPayableBatchPaymentRequest request, int userId, int branchId)
    {
        if (request.Invoices == null || request.Invoices.Count == 0)
            return 0;

        var invoiceIds = request.Invoices.Select(x => x.InvoiceExchangeId).ToList();
        var invoices = await _context.InvoiceExchanges
            .Where(x => invoiceIds.Contains(x.InvoiceExchangeId) &&
                       (x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid"))
            .ToListAsync();

        if (invoices.Count == 0)
            return 0;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        var paymentDate = DateTime.SpecifyKind(request.PaymentDate.Date, DateTimeKind.Utc);

        // Generate Payment Batch Number (APP-yyyy-MM-dd-0001)
        string datePrefix = $"APP-{paymentDate:yyyy-MM-dd}-";
        var lastBatch = await _context.InvoiceExchanges
            .Where(x => x.PaymentBatchNumber != null && x.PaymentBatchNumber.StartsWith(datePrefix))
            .OrderByDescending(x => x.PaymentBatchNumber)
            .Select(x => x.PaymentBatchNumber)
            .FirstOrDefaultAsync();

        int sequence = 1;
        if (!string.IsNullOrEmpty(lastBatch))
        {
            var lastSeq = lastBatch.Substring(datePrefix.Length);
            if (int.TryParse(lastSeq, out int parsed))
                sequence = parsed + 1;
        }
        string paymentBatchNumber = $"{datePrefix}{sequence:D4}";

        // Build lookup
        var invoiceItemLookup = request.Invoices.ToDictionary(x => x.InvoiceExchangeId);

        // Fetch Configuration (COA)
        var optionSupplierDebt = await _optionService.GetByName("ACCOUNT_UTANG_SUPPLIER", branchId);
        string coaSupplierDebt = optionSupplierDebt?.OptionValue ?? "201001";

        var bankCoa = await _coaService.GetByCode(request.CoaCode, branchId);
        if (bankCoa == null)
            throw new Exception($"Bank/Cash Account with code '{request.CoaCode}' not found.");

        double totalPaidAmount = 0;
        double totalIncrementalDiscount = 0;
        double totalIncrementalPPh23 = 0;
        int updatedCount = 0;

        // Track per-invoice amounts for journal detail
        var invoiceJournalDetails = new List<(string InvoiceNumber, string SupplierName, double PaidAmount, double IncrementalDiscount, double IncrementalPPh23)>();

        foreach (var invoice in invoices)
        {
            var invoiceItem = invoiceItemLookup.GetValueOrDefault(invoice.InvoiceExchangeId);

            // Per-invoice discount
            double newDiscountPercent = invoiceItem?.DiscountPercent ?? 0;
            double newDiscountAmount = invoiceItem?.DiscountAmount ?? 0;
            double oldDiscount = invoice.DiscountAmount ?? 0;
            totalIncrementalDiscount += (newDiscountAmount - oldDiscount);

            // Per-invoice PPH23
            double newPph23Percent = invoiceItem?.Pph23Percent ?? 0;
            double newPph23Amount = invoiceItem?.Pph23Amount ?? 0;
            double oldPPh23 = invoice.Pph23Amount ?? 0;
            totalIncrementalPPh23 += (newPph23Amount - oldPPh23);

            // Calculate amount to pay for this invoice (full payment, no partial)
            double totalTrx = invoice.TotalTransaction ?? 0;
            double currentPaid = invoice.PaidAmount ?? 0;
            double amountToPayForThisInvoice = totalTrx - currentPaid - newDiscountAmount - newPph23Amount;
            if (amountToPayForThisInvoice < 0) amountToPayForThisInvoice = 0;

            // Update invoice
            invoice.PaidAmount = currentPaid + amountToPayForThisInvoice;
            invoice.DiscountPercent = newDiscountPercent;
            invoice.DiscountAmount = newDiscountAmount;
            invoice.Pph23Percent = newPph23Percent;
            invoice.Pph23Amount = newPph23Amount;

            // Force full payment - set as Paid
            invoice.PaymentStatus = "Paid";
            invoice.OutstandingAmount = 0;

            invoice.PaymentDate = paymentDate;
            invoice.PaymentRef = request.PaymentRef;
            invoice.PaymentCoaCode = request.CoaCode;
            invoice.PaymentCoaDesc = request.CoaDesc;
            invoice.PaymentBy = userId;
            invoice.PaymentBatchNumber = paymentBatchNumber;
            invoice.UpdatedBy = userId;
            invoice.UpdatedDate = utcNow;

            // Update Payment History
            var historyItem = new PaymentHistoryItem
            {
                PaymentDate = paymentDate,
                Amount = amountToPayForThisInvoice,
                Note = $"Batch Payment Ref: {request.PaymentRef}",
                CoaCode = request.CoaCode,
                CoaName = request.CoaDesc,
                PaymentRef = request.PaymentRef,
                PaidBy = userId,
                DiscountAmount = newDiscountAmount - oldDiscount,
                Pph23Amount = newPph23Amount - oldPPh23
            };

            var historyList = new List<PaymentHistoryItem>();
            if (!string.IsNullOrEmpty(invoice.PaymentHistory))
            {
                try
                {
                    historyList = JsonConvert.DeserializeObject<List<PaymentHistoryItem>>(invoice.PaymentHistory) ?? new List<PaymentHistoryItem>();
                }
                catch { }
            }
            historyList.Add(historyItem);
            invoice.PaymentHistory = JsonConvert.SerializeObject(historyList);

            // Ensure UTC for all date fields
            if (invoice.ReceiptDate.HasValue && invoice.ReceiptDate.Value.Kind != DateTimeKind.Utc)
                invoice.ReceiptDate = DateTime.SpecifyKind(invoice.ReceiptDate.Value, DateTimeKind.Utc);
            if (invoice.PODate.HasValue && invoice.PODate.Value.Kind != DateTimeKind.Utc)
                invoice.PODate = DateTime.SpecifyKind(invoice.PODate.Value, DateTimeKind.Utc);
            if (invoice.DueDate.HasValue && invoice.DueDate.Value.Kind != DateTimeKind.Utc)
                invoice.DueDate = DateTime.SpecifyKind(invoice.DueDate.Value, DateTimeKind.Utc);
            if (invoice.InsertedDate.HasValue && invoice.InsertedDate.Value.Kind != DateTimeKind.Utc)
                invoice.InsertedDate = DateTime.SpecifyKind(invoice.InsertedDate.Value, DateTimeKind.Utc);

            totalPaidAmount += amountToPayForThisInvoice;

            invoiceJournalDetails.Add((
                invoice.InvoiceNumber ?? $"INV-{invoice.InvoiceExchangeId}",
                invoice.SupplierName ?? "Unknown",
                amountToPayForThisInvoice,
                newDiscountAmount - oldDiscount,
                newPph23Amount - oldPPh23
            ));

            updatedCount++;
        }

        // Validate total amount matches (tolerance 100 Rp)
        if (Math.Abs(totalPaidAmount - request.TotalAmount) > 100)
            throw new Exception($"Total amount mismatch. Expected: {request.TotalAmount}, Calculated: {totalPaidAmount}");

        // Create Transaction Header
        double totalPaid = totalPaidAmount;
        double totalDiscount = totalIncrementalDiscount;
        double totalPPh23 = totalIncrementalPPh23;

        string transactionType = "Payment";
        string transactionNo = await _transactionService.GetCode(transactionType, branchId);
        await _transactionService.UpdateCode(transactionType, transactionNo, branchId);
        string journalNo = await _journalService.GenerateJournalNo(request.PaymentDate);

        var transaction = new Transaction
        {
            TransactionId = Guid.NewGuid().ToString(),
            TransactionDate = paymentDate,
            TransactionName = $"Batch Payment AP {request.PaymentRef}",
            TransactionRef = request.PaymentRef,
            TransactionType = transactionType,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            Description = $"Batch Payment AP Ref: {request.PaymentRef} - {invoices.Count} Invoices",
            CoaCode = request.CoaCode,
            CoaName = request.CoaDesc,
            status = Status.Published.ToString(),
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsAutoId = true,
            Amount = (double?)totalPaid,
            PaymentNo = request.PaymentRef,
            PaymentDate = paymentDate,
            PaymentStatus = "Paid",
            CoaParentCode = bankCoa.ParentCode,
            CoaParentName = bankCoa.ParentName
        };

        await _transactionService.Add(transaction);

        // Create Journals

        // A. Credit Bank (1 line for total paid)
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
            CreditAmount = (double?)totalPaid,
            Source = transactionType,
            status = transaction.status,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = utcNow,
            IsPremier = true
        };
        await _journalService.Add(journalBank);

        // B. Per-invoice journal lines (Hutang Supplier, Discount, PPh23)
        var supplierDebtCoa = await _coaService.GetByCode(coaSupplierDebt, branchId);

        // Fetch COA for discount & PPh23 once (reused per invoice)
        var optionDiscount = await _optionService.GetByName("ACCOUNT_AP_DISCOUNT", branchId);
        var coaDiscountCode = optionDiscount?.OptionValue ?? "404001";
        var discountCoa = await _coaService.GetByCode(coaDiscountCode, branchId);

        var optionPPh = await _optionService.GetByName("ACCOUNT_AP_PPH23", branchId);
        var coaPPh23Code = optionPPh?.OptionValue ?? "206002";
        var pphCoa = await _coaService.GetByCode(coaPPh23Code, branchId);

        foreach (var detail in invoiceJournalDetails)
        {
            double invoiceDebitTotal = detail.PaidAmount + detail.IncrementalDiscount + detail.IncrementalPPh23;

            // Debit Hutang Supplier per faktur
            if (invoiceDebitTotal > 0)
            {
                await _journalService.Add(new Journal
                {
                    TransactionId = transaction.TransactionId,
                    JournalRef = transaction.TransactionRef,
                    JournalDesc = $"Hutang {detail.SupplierName} - {detail.InvoiceNumber}",
                    CoaCode = coaSupplierDebt,
                    CoaName = supplierDebtCoa?.CoaName ?? "",
                    CoaParentCode = supplierDebtCoa?.ParentCode,
                    CoaParentName = supplierDebtCoa?.ParentName,
                    DebitAmount = (double?)invoiceDebitTotal,
                    CreditAmount = 0,
                    Source = transactionType,
                    status = transaction.status,
                    BranchId = branchId,
                    CreatedBy = userId,
                    CreatedDate = utcNow,
                    IsPremier = false
                });
            }

            // Credit Discount per faktur
            if (detail.IncrementalDiscount > 0)
            {
                await _journalService.Add(new Journal
                {
                    TransactionId = transaction.TransactionId,
                    JournalRef = transaction.TransactionRef,
                    JournalDesc = $"Diskon {detail.SupplierName} - {detail.InvoiceNumber}",
                    CoaCode = coaDiscountCode,
                    CoaName = discountCoa?.CoaName ?? "Discount",
                    CoaParentCode = discountCoa?.ParentCode,
                    CoaParentName = discountCoa?.ParentName,
                    DebitAmount = 0,
                    CreditAmount = (double?)detail.IncrementalDiscount,
                    Source = transactionType,
                    status = transaction.status,
                    BranchId = branchId,
                    CreatedBy = userId,
                    CreatedDate = utcNow,
                    IsPremier = false
                });
            }

            // Credit PPh 23 per faktur
            if (detail.IncrementalPPh23 > 0)
            {
                await _journalService.Add(new Journal
                {
                    TransactionId = transaction.TransactionId,
                    JournalRef = transaction.TransactionRef,
                    JournalDesc = $"PPh 23 {detail.SupplierName} - {detail.InvoiceNumber}",
                    CoaCode = coaPPh23Code,
                    CoaName = pphCoa?.CoaName ?? "PPh 23 Payable",
                    CoaParentCode = pphCoa?.ParentCode,
                    CoaParentName = pphCoa?.ParentName,
                    DebitAmount = 0,
                    CreditAmount = (double?)detail.IncrementalPPh23,
                    Source = transactionType,
                    status = transaction.status,
                    BranchId = branchId,
                    CreatedBy = userId,
                    CreatedDate = utcNow,
                    IsPremier = false
                });
            }
        }

        _context.InvoiceExchanges.UpdateRange(invoices);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.InvoiceExchange, "Pembayaran Batch",
            invoices.Select(i => (i.InvoiceExchangeId.ToString(), (object?)new
            {
                PaymentBatchNumber = paymentBatchNumber,
                i.InvoiceNumber,
                i.SupplierName,
                i.PaidAmount,
                i.OutstandingAmount,
                i.PaymentStatus
            })),
            userId, branchId);

        return updatedCount;
    }

    public async Task<BatchPaymentDetailResponse?> GetBatchPaymentDetail(string paymentBatchNumber)
    {
        if (string.IsNullOrEmpty(paymentBatchNumber))
            return null;

        var invoices = await _context.InvoiceExchanges
            .Where(x => x.PaymentBatchNumber == paymentBatchNumber)
            .ToListAsync();

        if (invoices.Count == 0)
            return null;

        var first = invoices.First();

        return new BatchPaymentDetailResponse
        {
            PaymentBatchNumber = paymentBatchNumber,
            PaymentDate = first.PaymentDate,
            PaymentRef = first.PaymentRef,
            PaymentCoaCode = first.PaymentCoaCode,
            PaymentCoaDesc = first.PaymentCoaDesc,
            TotalPaid = invoices.Sum(x => x.PaidAmount ?? 0),
            TotalDiscount = invoices.Sum(x => x.DiscountAmount ?? 0),
            TotalPph23 = invoices.Sum(x => x.Pph23Amount ?? 0),
            InvoiceCount = invoices.Count,
            Invoices = invoices.Select(x => new BatchPaymentInvoiceDetail
            {
                InvoiceExchangeId = x.InvoiceExchangeId,
                SupplierName = x.SupplierName,
                PONumber = x.PONumber,
                InvoiceNumber = x.InvoiceNumber,
                DueDate = x.DueDate,
                TotalTransaction = x.TotalTransaction,
                DiscountAmount = x.DiscountAmount,
                DiscountPercent = x.DiscountPercent,
                Pph23Amount = x.Pph23Amount,
                Pph23Percent = x.Pph23Percent,
                PaidAmount = x.PaidAmount,
                PaymentStatus = x.PaymentStatus ?? "Unpaid"
            }).ToList()
        };
    }

    public async Task<List<AccountPayableAgingResponse>> GetAgingReport(
        string? supplierName,
        string? supplierType,
        DateTime? asOfDate,
        string? dueStatus = null,
        string? keyword = null)
    {
        var today = asOfDate?.Date ?? DateTime.UtcNow.Date;

        IQueryable<InvoiceExchange> query = _context.InvoiceExchanges
            .Where(x => x.Status != null && x.Status.ToLower() == "sent" &&
                       (x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid"));

        if (!string.IsNullOrEmpty(supplierName))
        {
            query = query.Where(x => x.SupplierName != null && x.SupplierName.ToLower().Contains(supplierName.ToLower()));
        }

        if (!string.IsNullOrEmpty(keyword))
        {
            var kw = keyword.ToLower();
            query = query.Where(x =>
                (x.InvoiceNumber != null && x.InvoiceNumber.ToLower().Contains(kw)) ||
                (x.PONumber != null && x.PONumber.ToLower().Contains(kw)));
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

        var data = await query.ToListAsync();

        // Filter per-invoice berdasarkan status jatuh tempo
        if (!string.IsNullOrEmpty(dueStatus))
        {
            var normalized = dueStatus.ToLower();
            data = data.Where(item =>
            {
                var agingDays = item.DueDate.HasValue ? (int)(today - item.DueDate.Value.Date).TotalDays : 0;
                if (normalized == "overdue") return agingDays > 0;
                if (normalized == "not_overdue") return agingDays <= 0;
                return true;
            }).ToList();
        }

        var agingData = data
            .Select(item =>
            {
                var amount = (item.TotalTransaction ?? 0) - (item.PaidAmount ?? 0);
                var agingDays = item.DueDate.HasValue ? (int)(today - item.DueDate.Value.Date).TotalDays : 0;

                double current = 0, d1to30 = 0, d31to60 = 0, d61to90 = 0, over90 = 0;
                if (agingDays <= 0) current = amount;
                else if (agingDays <= 30) d1to30 = amount;
                else if (agingDays <= 60) d31to60 = amount;
                else if (agingDays <= 90) d61to90 = amount;
                else over90 = amount;

                return new AccountPayableAgingResponse
                {
                    BatchNumber = item.BatchNumber,
                    SupplierName = item.SupplierName,
                    PONumber = item.PONumber,
                    InvoiceNumber = item.InvoiceNumber,
                    DueDate = item.DueDate,
                    Current = current,
                    Days1To30 = d1to30,
                    Days31To60 = d31to60,
                    Days61To90 = d61to90,
                    Over90Days = over90,
                    Total = amount
                };
            })
            .OrderBy(x => x.SupplierName)
            .ThenBy(x => x.DueDate)
            .ToList();

        return agingData;
    }

    /// <summary>
    /// Cancel invoice exchange - revert status from Sent to not sent
    /// Only works if the invoice is not already paid
    /// Also creates void/reversal journal entries
    /// </summary>
    public async Task<int> CancelInvoiceExchange(List<int> invoiceIds, int userId, int branchId)
    {
        if (invoiceIds == null || invoiceIds.Count == 0)
            return 0;

        // Get invoices that are Sent but not Paid
        var invoices = await _context.InvoiceExchanges
            .Where(x => invoiceIds.Contains(x.InvoiceExchangeId) &&
                       x.Status != null && x.Status.ToLower() == "sent" &&
                       (x.PaymentStatus == null || x.PaymentStatus.ToLower() != "paid"))
            .ToListAsync();

        if (invoices.Count == 0)
            return 0;

        var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
        decimal totalAmount = 0;
        string? supplierName = null;
        string? batchNumber = null;

        // Snapshot before the nulling below -- the log is the only place the erased
        // exchange_by/exchange_date survive.
        var priorState = new List<(string RefId, object? Detail)>();

        foreach (var invoice in invoices)
        {
            totalAmount += (decimal)(invoice.TotalTransaction ?? 0);
            supplierName ??= invoice.SupplierName;
            batchNumber ??= invoice.BatchNumber;

            priorState.Add((invoice.InvoiceExchangeId.ToString(), new
            {
                PreviousStatus = invoice.Status,
                PreviousExchangeBy = invoice.ExchangeBy,
                PreviousExchangeDate = invoice.ExchangeDate,
                PreviousBatchNumber = invoice.BatchNumber,
                invoice.InvoiceNumber,
                invoice.SupplierName,
                invoice.TotalTransaction
            }));

            invoice.Status = "not sent";
            invoice.UpdatedBy = userId;
            invoice.UpdatedDate = utcNow;
            invoice.ExchangeDate = null;
            invoice.ExchangeBy = null;
            invoice.BatchNumber = null;
            invoice.JournalId = null; // Clear journal reference

            // Ensure all DateTime fields are UTC to avoid PostgreSQL errors
            if (invoice.ReceiptDate.HasValue && invoice.ReceiptDate.Value.Kind != DateTimeKind.Utc)
                invoice.ReceiptDate = DateTime.SpecifyKind(invoice.ReceiptDate.Value, DateTimeKind.Utc);
            if (invoice.PODate.HasValue && invoice.PODate.Value.Kind != DateTimeKind.Utc)
                invoice.PODate = DateTime.SpecifyKind(invoice.PODate.Value, DateTimeKind.Utc);
            if (invoice.DueDate.HasValue && invoice.DueDate.Value.Kind != DateTimeKind.Utc)
                invoice.DueDate = DateTime.SpecifyKind(invoice.DueDate.Value, DateTimeKind.Utc);
            if (invoice.InsertedDate.HasValue && invoice.InsertedDate.Value.Kind != DateTimeKind.Utc)
                invoice.InsertedDate = DateTime.SpecifyKind(invoice.InsertedDate.Value, DateTimeKind.Utc);
        }

        _context.InvoiceExchanges.UpdateRange(invoices);
        await _context.SaveChangesAsync();

        await _auditTrailUtils.LogActionEntries(
            AuditModule.InvoiceExchange, "Batal Tukar Faktur",
            priorState, userId, branchId);

        // Create void/reversal journal entries
        if (totalAmount > 0)
        {
            await CreateVoidTukarFakturJournal(
                totalAmount,
                supplierName ?? "Unknown Supplier",
                batchNumber ?? "VOID",
                userId,
                utcNow,
                branchId
            );
        }

        return invoices.Count;
    }

    /// <summary>
    /// Creates void/reversal journal entries for cancelled tukar faktur
    /// This is the opposite of the original journal - Credit becomes Debit and vice versa
    /// </summary>
    private async Task CreateVoidTukarFakturJournal(
        decimal totalAmount,
        string supplierName,
        string originalBatchNumber,
        int userId,
        DateTime transactionDate,
        int branchId)
    {

        // Get COA codes from options or use defaults
        var optionDebit = await _optionService.GetByName("ACCOUNT_TUKAR_FAKTUR_CREDIT", branchId);
        var optionCredit = await _optionService.GetByName("ACCOUNT_TUKAR_FAKTUR_DEBIT", branchId);
        
        // For void, we reverse: original Debit becomes Credit, original Credit becomes Debit
        string coaOriginalDebitCode = optionDebit?.OptionValue ?? "201001"; // Utang Supplier
        string coaOriginalCreditCode = optionCredit?.OptionValue ?? "204099"; // Persediaan Obat

        // Get COA details
        var coaOriginalDebit = await _coaService.GetByCode(coaOriginalDebitCode, branchId);
        var coaOriginalCredit = await _coaService.GetByCode(coaOriginalCreditCode, branchId);

        if (coaOriginalDebit == null)
        {
            throw new Exception($"COA Debit tidak ditemukan: {coaOriginalDebitCode}. Pastikan opsi ACCOUNT_TUKAR_FAKTUR_DEBIT sudah di-setting dengan benar.");
        }
        
        if (coaOriginalCredit == null)
        {
            throw new Exception($"COA Credit tidak ditemukan: {coaOriginalCreditCode}. Pastikan opsi ACCOUNT_TUKAR_FAKTUR_CREDIT sudah di-setting dengan benar.");
        }

        // Generate transaction ID using GUID like other transactions
        string transactionId = Guid.NewGuid().ToString();
        string transactionNo = $"VOID-TF-{DateTime.Now:yyyyMMddHHmmss}";
        string journalNo = await _journalService.GenerateJournalNo(transactionDate);

        // Create Transaction record for void
        var transaction = new Transaction
        {
            TransactionId = transactionId,
            TransactionDate = transactionDate,
            TransactionNo = transactionNo,
            JournalNo = journalNo,
            TransactionType = "VOID_TUKAR_FAKTUR",
            Description = $"Void Tukar Faktur - {supplierName} - Original Batch: {originalBatchNumber}",
            CoaCode = coaOriginalCreditCode,
            CoaName = coaOriginalCredit.CoaName,
            Amount = (double)totalAmount,
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = transactionDate,
            status = "Published",
            IsAutoId = true
        };

        await _transactionService.Add(transaction);

        // VOID: Debit Persediaan (reverse the original Credit)
        var journalDebit = new Journal
        {
            TransactionId = transactionId,
            JournalDesc = $"Void Tukar Faktur - {supplierName}",
            JournalRef = $"VOID-{originalBatchNumber}",
            CoaCode = coaOriginalCreditCode, // Persediaan - now Debit
            CoaName = coaOriginalCredit.CoaName,
            DebitAmount = (double)totalAmount,
            CreditAmount = 0,
            Source = "VOID_TUKAR_FAKTUR",
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = transactionDate,
            status = "Published",
            SupplierName = supplierName,
            TotalTransaction = (double)totalAmount
        };

        // VOID: Credit Utang Supplier (reverse the original Debit)
        var journalCredit = new Journal
        {
            TransactionId = transactionId,
            JournalDesc = $"Void Tukar Faktur - {supplierName}",
            JournalRef = $"VOID-{originalBatchNumber}",
            CoaCode = coaOriginalDebitCode, // Utang Supplier - now Credit
            CoaName = coaOriginalDebit.CoaName,
            DebitAmount = 0,
            CreditAmount = (double)totalAmount,
            Source = "VOID_TUKAR_FAKTUR",
            BranchId = branchId,
            CreatedBy = userId,
            CreatedDate = transactionDate,
            status = "Published",
            SupplierName = supplierName,
            TotalTransaction = (double)totalAmount
        };

        await _journalService.Add(journalDebit);
        await _journalService.Add(journalCredit);
    }

    public async Task<List<BatchPpnItem>> GetBatchPpnByInvoiceIds(List<int> invoiceExchangeIds, int branchId = 1)
    {
        var ppnOption = await _optionService.GetByName("ACCOUNT_PPN_MASUKAN", branchId);
        var ppnCoaCode = ppnOption?.OptionValue ?? "108006";

        var invoices = await _context.InvoiceExchanges
            .Where(x => invoiceExchangeIds.Contains(x.InvoiceExchangeId))
            .Select(x => new { x.InvoiceExchangeId, x.PONumber, x.PpnAmount })
            .ToListAsync();

        var poNumbers = invoices
            .Where(x => !string.IsNullOrEmpty(x.PONumber))
            .Select(x => x.PONumber!)
            .Distinct()
            .ToList();

        // Net debit - kredit per PO, alasannya sama seperti di GetAccountPayableById.
        var ppnByPo = await _context.Journals
            .Where(x => poNumbers.Contains(x.PONumber!) && x.CoaCode == ppnCoaCode)
            .GroupBy(x => x.PONumber)
            .Select(g => new
            {
                PONumber = g.Key,
                PpnAmount = g.Sum(x => (x.DebitAmount ?? 0) - (x.CreditAmount ?? 0))
            })
            .ToListAsync();

        var ppnLookup = ppnByPo.ToDictionary(x => x.PONumber!, x => x.PpnAmount);

        return invoices.Select(inv => new BatchPpnItem
        {
            InvoiceExchangeId = inv.InvoiceExchangeId,
            PONumber = inv.PONumber,
            PpnAmount = !string.IsNullOrEmpty(inv.PONumber) && ppnLookup.ContainsKey(inv.PONumber)
                ? ppnLookup[inv.PONumber]
                : (inv.PpnAmount ?? 0)   // non-PO: PPN tersimpan di invoice
        }).ToList();
    }
}
