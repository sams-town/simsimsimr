using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;

using Newtonsoft.Json;
using System.Numerics;
using TDPBackend.Helpers;

namespace RSSMBackend.Services
{
    public class InvoiceExchangeService : IInvoiceExchangeService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        private readonly IJournalService _journalService;
        private readonly ITransactionService _transactionService;
        private readonly IOptionService _optionService;
        private readonly ICOAService _coaService;
        private readonly AuditTrailUtils _auditTrailUtils;

        public InvoiceExchangeService(
            RSSMDbContext context,
            IOptions<AppSettings> appSettings,
            IJournalService journalService,
            ITransactionService transactionService,
            IOptionService optionService,
            ICOAService coaService,
            AuditTrailUtils auditTrailUtils)
        {
            _context = context;
            _appSettings = appSettings.Value;
            _journalService = journalService;
            _transactionService = transactionService;
            _optionService = optionService;
            _coaService = coaService;
            _auditTrailUtils = auditTrailUtils;
        }

        public async Task<PagedResult<InvoiceExchangeResponse>> GetInvoiceExchange(
            string? keyword,
            string? supplierName,
            string? status,
            string? supplierType,
            DateTime? startDate,
            DateTime? endDate,
            int page,
            int pageSize,
            string? orderBy,
            string? orderType)
        {
            IQueryable<InvoiceExchange> query = _context.InvoiceExchanges;

            // Apply keyword filter
            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x =>
                    (x.PONumber != null && x.PONumber.ToLower().Contains(keyword.ToLower())) ||
                    (x.SupplierName != null && x.SupplierName.ToLower().Contains(keyword.ToLower())) ||
                    (x.Status != null && x.Status.ToLower().Contains(keyword.ToLower()))
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

            // Apply status filter
            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(x => x.Status != null && x.Status.ToLower() == status.ToLower());
            }
            
            if (startDate != null || endDate != null)
            {
                if (startDate != null && endDate != null)
                {
                    var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                    var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate >= start && x.ReceiptDate <= end);
                }
                else if (startDate != null && endDate == null)
                {
                    var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate >= start);
                }
                else if (startDate == null && endDate != null)
                {
                    var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate <= end);
                }
            }

            // Apply ordering
            orderBy = string.IsNullOrEmpty(orderBy) ? "receiptdate" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "journalid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.JournalId) : query.OrderByDescending(x => x.JournalId);
                    break;
                case "ponumber":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.PONumber) : query.OrderByDescending(x => x.PONumber);
                    break;
                case "receiptdate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.ReceiptDate) : query.OrderByDescending(x => x.ReceiptDate);
                    break;
                case "podate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.PODate) : query.OrderByDescending(x => x.PODate);
                    break;
                case "suppliername":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.SupplierName) : query.OrderByDescending(x => x.SupplierName);
                    break;
                case "totaltransaction":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.TotalTransaction) : query.OrderByDescending(x => x.TotalTransaction);
                    break;
                case "status":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Status) : query.OrderByDescending(x => x.Status);
                    break;
                case "updatedby":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.UpdatedBy) : query.OrderByDescending(x => x.UpdatedBy);
                    break;
                case "inserteddate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.InsertedDate) : query.OrderByDescending(x => x.InsertedDate);
                    break;
                case "updateddate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.UpdatedDate) : query.OrderByDescending(x => x.UpdatedDate);
                    break;
                default:
                    query = query.OrderByDescending(x => x.ReceiptDate);
                    break;
            }

            // Group by SupplierName and create response
            var groupedQuery = query.GroupBy(i => i.SupplierName)
                .Select(r => new InvoiceExchangeResponse
                {
                    SupplierName = r.Key,
                    TotalRow = r.Count(),
                    DataInvoices = r.Select(i => new DataInvoiceExchangeResponse
                    {
                        InvoiceExchangeId = i.InvoiceExchangeId,
                        PONumber = i.PONumber,
                        ReceiptDate = i.ReceiptDate,
                        PODate = i.PODate,
                        DueDate = i.DueDate,
                        SupplierName = i.SupplierName,
                        TotalTransaction = i.TotalTransaction,
                        Status = i.Status,
                        PaymentStatus = i.PaymentStatus ?? "Unpaid",
                        ManualPaid = i.ManualPaid,
                    }).ToList()
                });
            
            int total = await groupedQuery.CountAsync();
            if (page != -1)
                groupedQuery = groupedQuery.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<InvoiceExchangeResponse>(await groupedQuery.ToListAsync(), total);
        }

        public async Task<InvoiceExchange?> GetByID(int id)
        {
            return await _context.InvoiceExchanges
                .FirstOrDefaultAsync(x => x.InvoiceExchangeId == id);
        }

        /// <summary>
        /// Manually create an invoice-exchange record for AP data that did NOT come through a PO.
        /// The record is created already "exchanged" (Status = Sent), Unpaid, with no PO number,
        /// so it appears directly in Daftar AP / Pembayaran / Form Maker.
        /// </summary>
        public async Task<InvoiceExchange> CreateManual(ManualInvoiceExchangeInput input, int userId)
        {
            if (string.IsNullOrWhiteSpace(input.SupplierName))
                throw new InvalidOperationException("Nama supplier wajib diisi.");
            if (string.IsNullOrWhiteSpace(input.InvoiceNumber))
                throw new InvalidOperationException("No. invoice wajib diisi.");
            if (input.TotalTransaction <= 0)
                throw new InvalidOperationException("Jumlah harus lebih dari 0.");

            var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

            // Prevent duplicate invoice for the same supplier.
            var supplierName = input.SupplierName.Trim();
            var invoiceNumber = input.InvoiceNumber.Trim();
            bool exists = await _context.InvoiceExchanges.AnyAsync(x =>
                x.InvoiceNumber != null && x.InvoiceNumber.ToLower() == invoiceNumber.ToLower() &&
                x.SupplierName != null && x.SupplierName.ToLower() == supplierName.ToLower());
            if (exists)
                throw new InvalidOperationException($"Invoice \"{invoiceNumber}\" untuk supplier \"{supplierName}\" sudah terdaftar.");

            var discount = input.DiscountAmount < 0 ? 0 : input.DiscountAmount;
            var pph23 = input.Pph23Amount < 0 ? 0 : input.Pph23Amount;
            var ppn = input.PpnAmount < 0 ? 0 : input.PpnAmount;

            // Hutang = (nilai barang - diskon) + PPN + PPh23.
            // PPN dan PPh23 MENAMBAH tagihan di sini - berbeda dari jalur PO, di mana
            // PPN hanya informasi dan PPh23 dipotong. Rumus ini mengikuti kebijakan
            // yang berlaku untuk invoice non-PO.
            var outstanding = (input.TotalTransaction - discount) + ppn + pph23;
            if (outstanding < 0) outstanding = 0;

            var entity = new InvoiceExchange
            {
                SupplierName = supplierName,
                SupplierCode = string.IsNullOrWhiteSpace(input.SupplierCode) ? null : input.SupplierCode.Trim(),
                InvoiceNumber = invoiceNumber,
                PONumber = null,
                PODate = null,
                ReceiptDate = input.ReceiptDate.HasValue ? DateTime.SpecifyKind(input.ReceiptDate.Value.Date, DateTimeKind.Utc) : now,
                DueDate = input.DueDate.HasValue ? DateTime.SpecifyKind(input.DueDate.Value.Date, DateTimeKind.Utc) : (DateTime?)null,
                TotalTransaction = input.TotalTransaction,
                DiscountPercent = input.DiscountPercent,
                DiscountAmount = discount,
                Pph23Percent = input.Pph23Percent,
                Pph23Amount = pph23,
                PpnPercent = input.PpnPercent,
                PpnAmount = ppn,
                OutstandingAmount = outstanding,
                PaidAmount = 0,
                PaymentStatus = "Unpaid",
                Status = "Sent",
                BatchNumber = "MANUAL",
                ExchangeDate = now,
                ExchangeBy = userId,
                InsertedDate = now,
                UpdatedDate = now,
                UpdatedBy = userId
            };

            await _context.InvoiceExchanges.AddAsync(entity);
            await _context.SaveChangesAsync();

            await _auditTrailUtils.LogAction(
                AuditModule.InvoiceExchange, "Dibuat Manual",
                entity.InvoiceExchangeId.ToString(), userId, null,
                new
                {
                    entity.InvoiceNumber,
                    entity.SupplierName,
                    entity.TotalTransaction,
                    entity.DiscountAmount,
                    entity.Pph23Amount,
                    entity.PpnAmount,
                    entity.OutstandingAmount,
                    entity.DueDate
                });
            return entity;
        }

        public async Task<int> UpdateStatus(string ids, int? updatedBy, int branchId, DateTime? dueDate = null, DateTime? exchangeDate = null)
        {
            if (string.IsNullOrEmpty(ids))
                return 0;

            // Split IDs by comma and parse to integers
            var idList = new List<int>();
            foreach (var idStr in ids.Split(',', StringSplitOptions.RemoveEmptyEntries))
            {
                if (int.TryParse(idStr.Trim(), out var id) && id > 0)
                {
                    idList.Add(id);
                }
            }

            if (idList.Count == 0)
                return 0;

            // Get all records with matching IDs that are NOT already sent
            var dataList = await _context.InvoiceExchanges
                .Where(x => idList.Contains(x.InvoiceExchangeId) && 
                       (x.Status == null || x.Status.ToLower() != "sent"))
                .ToListAsync();

            if (dataList.Count == 0)
                return 0;

            // Update all records
            var utcNow = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
            var batchNumber = await GenerateBatchNumber();
            int updatedCount = 0;
            decimal totalAmount = 0;
            string? supplierName = null;
            
            foreach (var data in dataList)
            {
                data.Status = "Sent";
                data.UpdatedBy = updatedBy;
                data.UpdatedDate = utcNow;
                data.BatchNumber = batchNumber;
                data.ExchangeDate = exchangeDate.HasValue
                    ? DateTime.SpecifyKind(exchangeDate.Value.Date, DateTimeKind.Utc)
                    : utcNow;
                data.ExchangeBy = updatedBy;
                
                // Update DueDate if provided
                if (dueDate.HasValue)
                {
                    data.DueDate = DateTime.SpecifyKind(dueDate.Value.Date, DateTimeKind.Utc);
                }
                
                updatedCount++;
                totalAmount += (decimal)(data.TotalTransaction ?? 0);
                supplierName ??= data.SupplierName;
                
                // Ensure all DateTime fields are UTC to avoid PostgreSQL errors
                if (data.ReceiptDate.HasValue && data.ReceiptDate.Value.Kind != DateTimeKind.Utc)
                    data.ReceiptDate = DateTime.SpecifyKind(data.ReceiptDate.Value, DateTimeKind.Utc);
                if (data.PODate.HasValue && data.PODate.Value.Kind != DateTimeKind.Utc)
                    data.PODate = DateTime.SpecifyKind(data.PODate.Value, DateTimeKind.Utc);
                if (data.DueDate.HasValue && data.DueDate.Value.Kind != DateTimeKind.Utc)
                    data.DueDate = DateTime.SpecifyKind(data.DueDate.Value, DateTimeKind.Utc);
                if (data.ExchangeDate.HasValue && data.ExchangeDate.Value.Kind != DateTimeKind.Utc)
                    data.ExchangeDate = DateTime.SpecifyKind(data.ExchangeDate.Value, DateTimeKind.Utc);
                if (data.InsertedDate.HasValue && data.InsertedDate.Value.Kind != DateTimeKind.Utc)
                    data.InsertedDate = DateTime.SpecifyKind(data.InsertedDate.Value, DateTimeKind.Utc);
            }

            _context.InvoiceExchanges.UpdateRange(dataList);
            await _context.SaveChangesAsync();

            await _auditTrailUtils.LogActionEntries(
                AuditModule.InvoiceExchange, "Tukar Faktur",
                dataList.Select(d => (d.InvoiceExchangeId.ToString(), (object?)new
                {
                    BatchNumber = batchNumber,
                    d.ExchangeDate,
                    d.InvoiceNumber,
                    d.SupplierName,
                    d.TotalTransaction,
                    d.DueDate
                })),
                updatedBy, branchId);

            // Create journal entries for tukar faktur
            if (totalAmount > 0)
            {
                await CreateTukarFakturJournal(
                    dataList,
                    batchNumber,
                    totalAmount,
                    supplierName ?? "Unknown Supplier",
                    updatedBy,
                    utcNow,
                    branchId
                );
            }

            return updatedCount;
        }

        /// <summary>
        /// Creates journal entries for tukar faktur (invoice exchange)
        /// Debit: Utang Supplier (201001) - reduce liability
        /// Credit: Persediaan/Inventory (105001) - recognize inventory received
        /// </summary>
        private async Task CreateTukarFakturJournal(
            List<InvoiceExchange> invoices,
            string batchNumber,
            decimal totalAmount,
            string supplierName,
            int? userId,
            DateTime transactionDate,
            int branchId)
        {

            // Get COA codes from options or use defaults
            var optionDebit = await _optionService.GetByName("ACCOUNT_TUKAR_FAKTUR_CREDIT", branchId);
            var optionCredit = await _optionService.GetByName("ACCOUNT_TUKAR_FAKTUR_DEBIT", branchId);
            
            string coaDebitCode = optionDebit?.OptionValue ?? "201001"; // Utang Supplier
            string coaCreditCode = optionCredit?.OptionValue ?? "204099"; // Persediaan Obat

            // Get COA details
            var coaDebit = await _coaService.GetByCode(coaDebitCode, branchId);
            var coaCredit = await _coaService.GetByCode(coaCreditCode, branchId);

            if (coaDebit == null)
            {
                throw new Exception($"COA Debit tidak ditemukan: {coaDebitCode}. Pastikan opsi ACCOUNT_TUKAR_FAKTUR_DEBIT sudah di-setting dengan benar.");
            }
            
            if (coaCredit == null)
            {
                throw new Exception($"COA Credit tidak ditemukan: {coaCreditCode}. Pastikan opsi ACCOUNT_TUKAR_FAKTUR_CREDIT sudah di-setting dengan benar.");
            }

            // Generate transaction ID using GUID like other transactions
            string transactionId = Guid.NewGuid().ToString();
            string transactionNo = $"TF-{batchNumber.Replace("/", "")}";
            string journalNo = await _journalService.GenerateJournalNo(transactionDate);

            // Create Transaction record
            var transaction = new Transaction
            {
                TransactionId = transactionId,
                TransactionDate = transactionDate,
                TransactionNo = transactionNo,
                JournalNo = journalNo,
                TransactionType = "TUKAR_FAKTUR",
                Description = $"Tukar Faktur - {supplierName} - Batch: {batchNumber}",
                CoaCode = coaDebitCode,
                CoaName = coaDebit.CoaName,
                Amount = (double)totalAmount,
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = transactionDate,
                status = "Published",
                IsAutoId = true
            };

            await _transactionService.Add(transaction);

            // Create Debit Journal Entry (Utang Supplier decreases - liability goes down)
            var journalDebit = new Journal
            {
                TransactionId = transactionId,
                JournalDesc = $"Tukar Faktur - {supplierName}",
                JournalRef = batchNumber,
                CoaCode = coaDebitCode,
                CoaName = coaDebit.CoaName,
                DebitAmount = (double)totalAmount,
                CreditAmount = 0,
                Source = "TUKAR_FAKTUR",
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = transactionDate,
                status = "Published",
                SupplierName = supplierName,
                TotalTransaction = (double)totalAmount
            };

            // Create Credit Journal Entry (Persediaan/Inventory increases)
            var journalCredit = new Journal
            {
                TransactionId = transactionId,
                JournalDesc = $"Tukar Faktur - {supplierName}",
                JournalRef = batchNumber,
                CoaCode = coaCreditCode,
                CoaName = coaCredit.CoaName,
                DebitAmount = 0,
                CreditAmount = (double)totalAmount,
                Source = "TUKAR_FAKTUR",
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = transactionDate,
                status = "Published",
                SupplierName = supplierName,
                TotalTransaction = (double)totalAmount
            };

            await _journalService.Add(journalDebit);
            await _journalService.Add(journalCredit);

            // Update invoice exchange records with journal reference
            foreach (var invoice in invoices)
            {
                invoice.JournalId = (int?)journalDebit.JournalId;
            }
            _context.InvoiceExchanges.UpdateRange(invoices);
            await _context.SaveChangesAsync();
        }

        public async Task<PagedResult<OptionResponse>> GetSupplierOptions(string? keyword, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<InvoiceExchange> query = _context.InvoiceExchanges;

            // Apply keyword filter
            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x => 
                    x.SupplierName != null && x.SupplierName.ToLower().Contains(keyword.ToLower())
                );
            }

            // Filter out null SupplierName
            query = query.Where(x => x.SupplierName != null && !string.IsNullOrEmpty(x.SupplierName));

            // Group by SupplierName and select distinct
            var groupedQuery = query
                .GroupBy(x => x.SupplierName)
                .Select(g => new OptionResponse
                {
                    label = g.Key ?? string.Empty,
                    value = g.Key ?? string.Empty,
                    desc = string.Empty
                });

            // Apply ordering
            orderBy = string.IsNullOrEmpty(orderBy) ? "suppliername" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "suppliername":
                    groupedQuery = orderType.Equals("asc") 
                        ? groupedQuery.OrderBy(x => x.label) 
                        : groupedQuery.OrderByDescending(x => x.label);
                    break;
                default:
                    groupedQuery = groupedQuery.OrderBy(x => x.label);
                    break;
            }

            // Get total count before pagination
            int total = await groupedQuery.CountAsync();

            // Apply pagination
            if (page != -1)
            {
                groupedQuery = groupedQuery.Skip((page - 1) * pageSize).Take(pageSize);
            }

            return new PagedResult<OptionResponse>(await groupedQuery.ToListAsync(), total);
        }

        public async Task<PagedResult<InvoiceExchangeTransactionResponse>> GetTransactions(
            string? keyword,
            string? supplierName,
            DateTime? startDate,
            DateTime? endDate,
            string? status,
            string? supplierType,
            int page,
            int pageSize,
            string? orderBy,
            string? orderType)
        {
            IQueryable<InvoiceExchange> query = _context.InvoiceExchanges;

            // Apply status filter (if provided)
            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(x => x.Status != null && x.Status.ToLower() == status.ToLower());
            }

            // Apply keyword filter
            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x =>
                    (x.PONumber != null && x.PONumber.ToLower().Contains(keyword.ToLower())) ||
                    (x.SupplierName != null && x.SupplierName.ToLower().Contains(keyword.ToLower())) ||
                    (x.Status != null && x.Status.ToLower().Contains(keyword.ToLower()))
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

            // Apply date filter
            if (startDate != null || endDate != null)
            {
                if (startDate != null && endDate != null)
                {
                    var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                    var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate >= start && x.ReceiptDate <= end);
                }
                else if (startDate != null && endDate == null)
                {
                    var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate >= start);
                }
                else if (startDate == null && endDate != null)
                {
                    var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate <= end);
                }
            }

            // Filter out null SupplierName
            query = query.Where(x => x.SupplierName != null && !string.IsNullOrEmpty(x.SupplierName));

            // Materialize the query first
            var dataList = await query.ToListAsync();

            // Group by Status and SupplierName in memory and calculate totals
            var groupedData = dataList
                .GroupBy(x => new { x.Status, x.SupplierName })
                .Select(g => new InvoiceExchangeTransactionResponse
                {
                    InvoiceExchangeId = g.First().InvoiceExchangeId, // Get InvoiceExchangeId from first item
                    SupplierName = g.Key.SupplierName,
                    TotalRow = g.Count(),
                    TotalTransaction = g.Sum(x => x.TotalTransaction ?? 0),
                    Status = g.Key.Status
                })
                .ToList();

            // Apply ordering for grouped results
            orderBy = string.IsNullOrEmpty(orderBy) ? "suppliername" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            IEnumerable<InvoiceExchangeTransactionResponse> orderedData = groupedData;
            switch (orderBy)
            {
                case "suppliername":
                    orderedData = orderType.Equals("asc")
                        ? groupedData.OrderBy(x => x.SupplierName)
                        : groupedData.OrderByDescending(x => x.SupplierName);
                    break;
                case "status":
                    orderedData = orderType.Equals("asc")
                        ? groupedData.OrderBy(x => x.Status)
                        : groupedData.OrderByDescending(x => x.Status);
                    break;
                case "totalrow":
                    orderedData = orderType.Equals("asc")
                        ? groupedData.OrderBy(x => x.TotalRow)
                        : groupedData.OrderByDescending(x => x.TotalRow);
                    break;
                case "totaltransaction":
                    orderedData = orderType.Equals("asc")
                        ? groupedData.OrderBy(x => x.TotalTransaction)
                        : groupedData.OrderByDescending(x => x.TotalTransaction);
                    break;
                default:
                    orderedData = groupedData.OrderBy(x => x.SupplierName);
                    break;
            }

            // Get total count before pagination
            int total = orderedData.Count();

            // Apply pagination
            List<InvoiceExchangeTransactionResponse> pagedData;
            if (page != -1)
            {
                pagedData = orderedData.Skip((page - 1) * pageSize).Take(pageSize).ToList();
            }
            else
            {
                pagedData = orderedData.ToList();
            }

            return new PagedResult<InvoiceExchangeTransactionResponse>(pagedData, total);
        }

        public async Task<PagedResult<InvoiceExchange>> GetTransactionDetail(
            string? keyword,
            int? invoiceExchangeId,
            string? supplierName,
            string? status,
            string? supplierType,
            DateTime? startDate,
            DateTime? endDate,
            int page,
            int pageSize,
            string? orderBy,
            string? orderType)
        {
            IQueryable<InvoiceExchange> query = _context.InvoiceExchanges;

            // Apply keyword filter
            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x =>
                    (x.PONumber != null && x.PONumber.ToLower().Contains(keyword.ToLower())) ||
                    (x.InvoiceNumber != null && x.InvoiceNumber.ToLower().Contains(keyword.ToLower()))
                );
            }

            // Apply InvoiceExchangeId filter
            if (invoiceExchangeId.HasValue && invoiceExchangeId.Value > 0)
            {
                query = query.Where(x => x.InvoiceExchangeId == invoiceExchangeId.Value);
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

            // Apply status filter
            if (!string.IsNullOrEmpty(status))
            {
                query = query.Where(x => x.Status != null && x.Status.ToLower() == status.ToLower());
            }

            // Apply date filter
            if (startDate != null || endDate != null)
            {
                if (startDate != null && endDate != null)
                {
                    var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                    var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate >= start && x.ReceiptDate <= end);
                }
                else if (startDate != null && endDate == null)
                {
                    var start = DateTime.SpecifyKind(startDate.Value.Date, DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate >= start);
                }
                else if (startDate == null && endDate != null)
                {
                    var end = DateTime.SpecifyKind(endDate.Value.Date.AddDays(1).AddTicks(-1), DateTimeKind.Utc);
                    query = query.Where(x => x.ReceiptDate != null && x.ReceiptDate <= end);
                }
            }

            // Filter out null SupplierName
            query = query.Where(x => x.SupplierName != null && !string.IsNullOrEmpty(x.SupplierName));

            // Apply ordering
            orderBy = string.IsNullOrEmpty(orderBy) ? "suppliername" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "ponumber":
                    query = orderType.Equals("asc") 
                        ? query.OrderBy(x => x.PONumber) 
                        : query.OrderByDescending(x => x.PONumber);
                    break;
                case "receiptdate":
                    query = orderType.Equals("asc") 
                        ? query.OrderBy(x => x.ReceiptDate) 
                        : query.OrderByDescending(x => x.ReceiptDate);
                    break;
                case "podate":
                    query = orderType.Equals("asc") 
                        ? query.OrderBy(x => x.PODate) 
                        : query.OrderByDescending(x => x.PODate);
                    break;
                case "suppliername":
                    query = orderType.Equals("asc") 
                        ? query.OrderBy(x => x.SupplierName) 
                        : query.OrderByDescending(x => x.SupplierName);
                    break;
                case "totaltransaction":
                    query = orderType.Equals("asc") 
                        ? query.OrderBy(x => x.TotalTransaction) 
                        : query.OrderByDescending(x => x.TotalTransaction);
                    break;
                case "status":
                    query = orderType.Equals("asc")
                        ? query.OrderBy(x => x.Status)
                        : query.OrderByDescending(x => x.Status);
                    break;
                case "batchnumber":
                    query = orderType.Equals("asc")
                        ? query.OrderBy(x => x.BatchNumber)
                        : query.OrderByDescending(x => x.BatchNumber);
                    break;
                default:
                    query = query.OrderBy(x => x.SupplierName);
                    break;
            }

            // Get total count before pagination
            int total = await query.CountAsync();

            // Apply pagination
            if (page != -1)
            {
                query = query.Skip((page - 1) * pageSize).Take(pageSize);
            }

            return new PagedResult<InvoiceExchange>(await query.ToListAsync(), total);
        }

        private async Task<string> GenerateBatchNumber()
        {
            var today = DateTime.UtcNow;
            var prefix = $"{today:yyyy}/{today:MM}/{today:dd}/";
            
            var lastRecord = await _context.InvoiceExchanges
                .Where(x => x.BatchNumber != null && x.BatchNumber.StartsWith(prefix))
                .OrderByDescending(x => x.BatchNumber)
                .FirstOrDefaultAsync();

            int sequence = 1;
            if (lastRecord != null && !string.IsNullOrEmpty(lastRecord.BatchNumber))
            {
                var lastSequenceStr = lastRecord.BatchNumber.Substring(prefix.Length);
                if (int.TryParse(lastSequenceStr, out int lastSequence))
                {
                    sequence = lastSequence + 1;
                }
            }

            return $"{prefix}{sequence:D4}";
        }
    }
}