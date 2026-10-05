using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;
using RSSMBackend.DTOs.Journal;

using Newtonsoft.Json;
using System.Numerics;
using OfficeOpenXml;
using System.Globalization;
using TDPBackend.Helpers;

namespace RSSMBackend.Services
{

    public class JournalService : IJournalService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        private readonly ICOAService _coaService;
        private readonly ITransactionService _transactionService;
        private readonly IOptionService _optionService;
        private readonly AuditTrailUtils _auditTrailUtils;

        public JournalService(RSSMDbContext context, IOptions<AppSettings> appSettings,
            ICOAService coaService, ITransactionService transactionService,
            IOptionService optionService, AuditTrailUtils auditTrailUtils)
        {
            _context = context;
            _appSettings = appSettings.Value;
            _coaService = coaService;
            _transactionService = transactionService;
            _optionService = optionService;
            _auditTrailUtils = auditTrailUtils;
        }

        // Status jurnal yang IKUT dihitung ke saldo COA. Hanya jurnal yang sudah diposting
        // (Published) yang menambah saldo — Pending/Void/Deleted diabaikan. Konsisten dgn:
        //  * Khanza scheduler yang meng-insert jurnal langsung dgn status 'Published',
        //  * posting aplikasi yang men-set journal.status = Published (TransactionController.UpdateStatus).
        private static readonly string Posted = Status.Published.ToString();

        /// <summary>
        /// Checks if a NormalPosition value indicates Credit-normal.
        /// Supports multiple formats: 'C', 'Credit', 'CREDIT', 'K', 'Kredit', 'KREDIT'
        /// </summary>
        private static bool IsCreditNormal(string? normalPosition)
        {
            if (string.IsNullOrEmpty(normalPosition)) return false;
            var upper = normalPosition.Trim().ToUpper();
            return upper == "C" || upper == "CREDIT" || upper == "K" || upper == "KREDIT";
        }

        // Lock tutup buku untuk jalur impor. Query langsung trx_period_closing (bukan lewat
        // IPeriodClosingService) untuk menghindari circular DI: PeriodClosingService -> IJournalService.
        private async Task<bool> IsPeriodClosedRaw(DateTime date, int branchId)
        {
            return await _context.PeriodClosings.AnyAsync(p => p.Status == "Closed"
                && p.Year == date.Year
                && (p.Month == null || p.Month == date.Month)
                && (p.BranchId == null || p.BranchId == branchId));
        }

        public async Task<PagedResult<UnbalancedJournalResponse>> GetUnbalancedJournals(DateTime? startDate, DateTime? endDate, int page, int pageSize)
        {
            var query = _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.status != Status.Deleted.ToString());

            if (startDate.HasValue)
                query = query.Where(j => j.Transaction.TransactionDate >= startDate.Value);
            if (endDate.HasValue)
                query = query.Where(j => j.Transaction.TransactionDate <= endDate.Value);

            // Group by Transaction
            var groupedQuery = query.GroupBy(j => new { j.TransactionId, j.Transaction.TransactionNo, j.Transaction.TransactionDate, j.Transaction.Description, j.Transaction.status })
                .Select(g => new
                {
                    TransactionId = g.Key.TransactionId,
                    TransactionNo = g.Key.TransactionNo,
                    TransactionDate = g.Key.TransactionDate,
                    Description = g.Key.Description,
                    Status = g.Key.status,
                    TotalDebit = g.Sum(x => x.DebitAmount ?? 0),
                    TotalCredit = g.Sum(x => x.CreditAmount ?? 0)
                });

            var unbalancedQuery = groupedQuery
                .Where(x => Math.Abs(x.TotalDebit - x.TotalCredit) > 1); // Tolerance 1 rupiah

            int total = await unbalancedQuery.CountAsync();

            var data = await unbalancedQuery
                .OrderByDescending(x => x.TransactionDate)
                .Skip((page - 1) * pageSize)
                .Take(pageSize)
                .Select(x => new UnbalancedJournalResponse
                {
                    TransactionId = x.TransactionId,
                    TransactionNo = x.TransactionNo,
                    TransactionDate = (DateTime)x.TransactionDate,
                    Description = x.Description,
                    Status = x.Status,
                    TotalDebit = x.TotalDebit,
                    TotalCredit = x.TotalCredit,
                    Difference = x.TotalDebit - x.TotalCredit
                })
                .ToListAsync();

            return new PagedResult<UnbalancedJournalResponse>(data, total);
        }

        public async Task<byte[]> ExportUnbalancedJournals(DateTime? startDate, DateTime? endDate)
        {
             var query = _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.status != Status.Deleted.ToString());

            if (startDate.HasValue)
                query = query.Where(j => j.Transaction.TransactionDate >= startDate.Value);
            if (endDate.HasValue)
                query = query.Where(j => j.Transaction.TransactionDate <= endDate.Value);

            var groupedQuery = query.GroupBy(j => new { j.TransactionId, j.Transaction.TransactionNo, j.Transaction.TransactionDate, j.Transaction.Description, j.Transaction.status })
                .Select(g => new
                {
                    TransactionId = g.Key.TransactionId,
                    TransactionNo = g.Key.TransactionNo,
                    TransactionDate = g.Key.TransactionDate,
                    Description = g.Key.Description,
                    Status = g.Key.status,
                    TotalDebit = g.Sum(x => x.DebitAmount ?? 0),
                    TotalCredit = g.Sum(x => x.CreditAmount ?? 0)
                });

            var unbalancedQuery = groupedQuery
                .Where(x => Math.Abs(x.TotalDebit - x.TotalCredit) > 1);

            var data = await unbalancedQuery
                .OrderByDescending(x => x.TransactionDate)
                .Select(x => new UnbalancedJournalResponse
                {
                    TransactionId = x.TransactionId,
                    TransactionNo = x.TransactionNo,
                    TransactionDate = (DateTime)x.TransactionDate,
                    Description = x.Description,
                    TotalDebit = x.TotalDebit,
                    TotalCredit = x.TotalCredit,
                    Difference = x.TotalDebit - x.TotalCredit,
                    Status = x.Status
                })
                .ToListAsync();
            
            return ExportToDocs.GenerateUnbalancedJournalExcel(data);
        }

        public async Task<PagedResult<Journal>> GetAll(string? keyword, string? status, string? coaCode, int? contactId, int? branchId, string? transactionNo, string? transactionType, string? transactionStatus, DateTime? startDate, DateTime? endDate, int page, int pageSize, string? orderBy, string? orderType)
        {
            IQueryable<Journal> query = _context.Journals;
            query = query.Include(x => x.Branch)
            .Include(x => x.Coa)
            .Include(x => x.CoaParent)
            .Include(x => x.Created)
            .Include(x => x.Reconciled)
            .Include(x => x.Transaction)
            .ThenInclude(x => x.Contact)
            .Include(x => x.Updated);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Where(m => m.status != Status.Deleted.ToString());

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(x =>
                        x.CoaName.ToLower().Contains(keyword.ToLower()) ||
                        x.Source.ToLower().Contains(keyword.ToLower()) ||
                        x.CoaParentName.ToLower().Contains(keyword.ToLower()) ||
                        x.Branch.BranchName.ToLower().Contains(keyword.ToLower()) ||
                        x.Created.DisplayName.ToLower().Contains(keyword.ToLower()) ||
                        x.Reconciled.DisplayName.ToLower().Contains(keyword.ToLower()) ||
                        x.Transaction.TransactionName.ToLower().Contains(keyword.ToLower()) ||
                        x.Updated.DisplayName.ToLower().Contains(keyword.ToLower())
                );
            }

            if (status != null)
                query = query.Where(x => x.status.ToLower().Equals(status.ToLower()));

            if (transactionStatus != null)
                query = query.Where(x => x.Transaction.status.ToLower().Equals(transactionStatus.ToLower()));

            if (coaCode != null)
                query = query.Where(x => x.CoaCode.ToLower().Equals(coaCode.ToLower()));

            if (transactionNo != null)
                query = query.Where(x => x.Transaction.TransactionNo.ToLower().Equals(transactionNo.ToLower()));

            if (transactionType != null)
                query = query.Where(x => x.Transaction.TransactionType.ToLower().Equals(transactionType.ToLower()));

            if (contactId != null)
                query = query.Where(x => x.Transaction.ContactId.Equals(contactId));

            if (startDate != null || endDate != null)
            {
                // Basis periode = transaction_date (bukan created_date) agar daftar riwayat
                // konsisten dengan perhitungan saldo (GetEndingBalance/GetLedgers) yang juga
                // memakai Transaction.TransactionDate. AddDays(1) agar endDate inklusif satu hari penuh.
                if (startDate != null && endDate != null)
                {
                    DateTime end = endDate.Value.Date.AddDays(1);
                    query = query.Where(x => x.Transaction.TransactionDate >= startDate.Value.Date && x.Transaction.TransactionDate < end);
                }
                else if (startDate != null && endDate == null)
                    query = query.Where(x => x.Transaction.TransactionDate >= startDate.Value.Date);
                else if (startDate == null && endDate != null)
                {
                     DateTime end = endDate.Value.Date.AddDays(1);
                     query = query.Where(x => x.Transaction.TransactionDate < end);
                }
            }

            orderBy = string.IsNullOrEmpty(orderBy) ? "createddate" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "asc" : orderType.ToLower();

            switch (orderBy)
            {
                case "createddate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CreatedDate) : query.OrderByDescending(x => x.CreatedDate);
                    break;
                case "transactiondate":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.Transaction.TransactionDate) : query.OrderByDescending(x => x.Transaction.TransactionDate);
                    break;
                case "journalid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.JournalId) : query.OrderByDescending(x => x.JournalId);
                    break;
                case "coacode":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaCode) : query.OrderByDescending(x => x.CoaCode);
                    break;
                case "coaname":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CoaName) : query.OrderByDescending(x => x.CoaParentName);
                    break;
                case "branchid":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "source":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "debitamount":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.DebitAmount) : query.OrderByDescending(x => x.DebitAmount);
                    break;
                case "creditamount":
                    query = orderType.Equals("asc") ? query.OrderBy(x => x.CreditAmount) : query.OrderByDescending(x => x.CreditAmount);
                    break;
                default:
                    query = query.OrderByDescending(x => x.CreatedDate);
                    break;
            }

            var q = query.Select(x => new Journal
            {
                JournalId = x.JournalId,
                JournalDesc = x.JournalDesc,
                TransactionId = x.TransactionId,
                JournalRef = x.JournalRef,
                CoaCode = x.CoaCode,
                CoaName = x.CoaName,
                CoaParentCode = x.CoaParentCode,
                CoaParentName = x.CoaParentName,
                DebitAmount = x.DebitAmount,
                CreditAmount = x.CreditAmount,
                Source = x.Source,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                CreatedBy = x.CreatedBy,
                CreatedName = x.Created.DisplayName,
                CreatedDate = x.CreatedDate,
                VourcherId = x.VourcherId,
                status = x.status,
                IsPremier = x.IsPremier,
                UpdatedBy = x.UpdatedBy,
                UpdatedName = x.Updated.DisplayName,
                UpdatedDate = x.UpdatedDate,
                ReconciledDate = x.ReconciledDate,
                ReconciledBy = x.ReconciledBy,
                ReconciledName = x.Reconciled.DisplayName,
                TransactionDetail = new Journal.TransactionForJournal
                {
                    TransactionNo = x.Transaction.TransactionNo,
                    TransactionType = x.Transaction.TransactionType,
                    TransactionDate = x.Transaction.TransactionDate,
                    JournalNo = x.Transaction.JournalNo,
                    ContactDetail = x.Transaction.Contact.ContactId != null ? (Contact)x.Transaction.Contact : null
                }
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Journal>(await q.ToListAsync(), total);
        }

        public async Task<double> GetEndingBalance(string coaCode, DateTime endDate, int branchId)
        {
            // Saldo dihitung ON-READ: checkpoint snapshot terakhir (<= endDate) + Σ mutasi jurnal
            // dengan tanggal transaksi SETELAH tanggal checkpoint s/d endDate. Karena dijumlahkan
            // langsung dari tabel jurnal, hasilnya benar untuk semua sumber insert (scheduler & aplikasi)
            // dan kebal urutan posting (backdate). Tanda mengikuti normal_position COA.
            //
            // Konvensi checkpoint: closing_balance = saldo per AKHIR period_date (sudah memuat jurnal
            // s/d period_date). Mutasi yang dijumlahkan = jurnal dgn tanggal transaksi STRICTLY > period_date.
            // Jika tidak ada checkpoint, base = 0 dan seluruh jurnal s/d endDate dijumlahkan (saldo murni jurnal).
            //
            // Catatan: pola ini selaras dengan GetLedgers (buku besar). Trigger saldo incremental
            // sebaiknya dipensiun (lihat SQL/retire_balance_triggers.sql) + jalankan SQL/rebalance_migration.sql,
            // karena snapshot per-transaksi lama tidak konsisten dengan model checkpoint+mutasi ini.

            var coa = await _context.Coa.AsNoTracking()
                .FirstOrDefaultAsync(c => c.CoaCode == coaCode);
            bool isCreditNormal = IsCreditNormal(coa?.NormalPosition);

            // 1) Base = closing dari checkpoint terakhir untuk (coa, branch) dengan period_date <= endDate.
            var checkpoint = await _context.TrxBalances
                .Where(b => b.CoaCode == coaCode && b.BranchId == branchId && b.PeriodDate <= endDate)
                .OrderByDescending(b => b.PeriodDate).ThenByDescending(b => b.CreatedDate)
                .Select(b => new { b.PeriodDate, b.ClosingBalance })
                .FirstOrDefaultAsync();

            double baseBalance = checkpoint?.ClosingBalance ?? 0;
            DateTime since = checkpoint != null ? checkpoint.PeriodDate : DateTime.MinValue;

            // 2) Σ mutasi jurnal (tidak terhapus) dgn tanggal transaksi STRICTLY > tanggal checkpoint dan <= endDate.
            var stats = await _context.Journals
                .Where(j => j.CoaCode == coaCode
                    && j.BranchId == branchId
                    && j.status == Posted
                    && j.Transaction != null
                    && j.Transaction.TransactionDate > since
                    && j.Transaction.TransactionDate <= endDate)
                .GroupBy(x => 1)
                .Select(g => new
                {
                    Debit = g.Sum(x => x.DebitAmount ?? 0),
                    Credit = g.Sum(x => x.CreditAmount ?? 0)
                })
                .FirstOrDefaultAsync();

            double movements = 0;
            if (stats != null)
                movements = isCreditNormal ? (stats.Credit - stats.Debit) : (stats.Debit - stats.Credit);

            return baseBalance + movements;
        }

        // Membuat/memperbarui checkpoint saldo akhir bulan agar GetEndingBalance untuk tanggal
        // sesudahnya cukup menjumlah mutasi bulan berjalan (bukan seluruh histori).
        // Idempotent: aman dijalankan ulang untuk periode yang sama (recompute dari checkpoint
        // sebelumnya + mutasi). closing_balance = saldo per AKHIR bulan; period_date = akhir bulan.
        public async Task<int> GenerateMonthlyCheckpoint(int year, int month, int? branchId = null)
        {
            var monthEnd = new DateTime(year, month, 1, 0, 0, 0, DateTimeKind.Utc).AddMonths(1).AddTicks(-1);
            var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

            // Pasangan (coa, branch) yang punya jurnal s/d akhir bulan ini.
            var pairs = await _context.Journals
                .Where(j => j.status == Posted
                    && j.CoaCode != null && j.BranchId != null
                    && j.Transaction != null && j.Transaction.TransactionDate <= monthEnd
                    && (branchId == null || j.BranchId == branchId))
                .Select(j => new { j.CoaCode, j.BranchId })
                .Distinct()
                .ToListAsync();

            int count = 0;
            foreach (var p in pairs)
            {
                var coa = await _context.Coa.AsNoTracking()
                    .FirstOrDefaultAsync(c => c.CoaCode == p.CoaCode);
                bool isCreditNormal = IsCreditNormal(coa?.NormalPosition);

                // Checkpoint sebelumnya (STRICTLY < akhir bulan ini) -> abaikan checkpoint yang sedang dibuat ulang.
                var prior = await _context.TrxBalances
                    .Where(b => b.CoaCode == p.CoaCode && b.BranchId == p.BranchId && b.PeriodDate < monthEnd)
                    .OrderByDescending(b => b.PeriodDate).ThenByDescending(b => b.CreatedDate)
                    .Select(b => new { b.PeriodDate, b.ClosingBalance })
                    .FirstOrDefaultAsync();

                double opening = prior?.ClosingBalance ?? 0;
                DateTime since = prior != null ? prior.PeriodDate : DateTime.MinValue;

                // Mutasi dari checkpoint sebelumnya s/d akhir bulan ini.
                var stats = await _context.Journals
                    .Where(j => j.CoaCode == p.CoaCode && j.BranchId == p.BranchId
                        && j.status == Posted
                        && j.Transaction != null
                        && j.Transaction.TransactionDate > since
                        && j.Transaction.TransactionDate <= monthEnd)
                    .GroupBy(x => 1)
                    .Select(g => new
                    {
                        Debit = g.Sum(x => x.DebitAmount ?? 0),
                        Credit = g.Sum(x => x.CreditAmount ?? 0)
                    })
                    .FirstOrDefaultAsync();

                double debit = stats?.Debit ?? 0;
                double credit = stats?.Credit ?? 0;
                double net = isCreditNormal ? (credit - debit) : (debit - credit);
                double closing = opening + net;

                // Upsert checkpoint (coa, period_date = akhir bulan, branch).
                var existing = await _context.TrxBalances
                    .FirstOrDefaultAsync(b => b.CoaCode == p.CoaCode && b.BranchId == p.BranchId && b.PeriodDate == monthEnd);
                if (existing != null)
                {
                    existing.OpeningBalance = opening;
                    existing.DebitAmount = debit;
                    existing.CreditAmount = credit;
                    existing.ClosingBalance = closing;
                    existing.UpdatedDate = now;
                }
                else
                {
                    _context.TrxBalances.Add(new TrxBalance
                    {
                        CoaCode = p.CoaCode!,
                        BranchId = p.BranchId,
                        PeriodDate = monthEnd,
                        OpeningBalance = opening,
                        DebitAmount = debit,
                        CreditAmount = credit,
                        ClosingBalance = closing,
                        CreatedDate = now,
                        UpdatedDate = now
                    });
                }
                count++;
            }

            await _context.SaveChangesAsync();
            return count;
        }

        public async Task<Journal> GetByID(int id, int? branchId)
        {
            IQueryable<Journal> query = _context.Journals;
            query = query.Include(x => x.Branch)
            .Include(x => x.Coa)
            .Include(x => x.CoaParent)
            .Include(x => x.Created)
            .Include(x => x.Reconciled)
            .Include(x => x.Transaction)
            .ThenInclude(x => x.Contact)
            .Include(x => x.Updated);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Where(m => m.status != Status.Deleted.ToString());

            query = query.Select(x => new Journal
            {
                JournalId = x.JournalId,
                JournalDesc = x.JournalDesc,
                TransactionId = x.TransactionId,
                JournalRef = x.JournalRef,
                CoaCode = x.CoaCode,
                CoaName = x.CoaName,
                CoaParentCode = x.CoaParentCode,
                CoaParentName = x.CoaParentName,
                DebitAmount = x.DebitAmount,
                CreditAmount = x.CreditAmount,
                Source = x.Source,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                CreatedBy = x.CreatedBy,
                CreatedName = x.Created.DisplayName,
                CreatedDate = x.CreatedDate,
                VourcherId = x.VourcherId,
                status = x.status,
                IsPremier = x.IsPremier,
                UpdatedBy = x.UpdatedBy,
                UpdatedName = x.Updated.DisplayName,
                UpdatedDate = x.UpdatedDate,
                ReconciledDate = x.ReconciledDate,
                ReconciledBy = x.ReconciledBy,
                ReconciledName = x.Reconciled.DisplayName,
                TransactionDetail = new Journal.TransactionForJournal
                {
                    TransactionNo = x.Transaction.TransactionNo,
                    TransactionType = x.Transaction.TransactionType,
                    TransactionDate = x.Transaction.TransactionDate,
                    JournalNo = x.Transaction.JournalNo,
                    ContactDetail = x.Transaction.Contact.ContactId != null ? (Contact)x.Transaction.Contact : null
                }
            });
            return query.SingleOrDefault(x => x.JournalId == id);
        }

        public async Task<List<Journal>> GetForUpdateByID(string id, int? branchId)
        {
            IQueryable<Journal> query = _context.Journals;
            query = query.Where(x => x.TransactionId.Equals(id));

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            return await query.ToListAsync();
        }

        public async Task<Journal> GetForUpdateOneByID(int id, int? branchId)
        {
            IQueryable<Journal> query = _context.Journals;
            query = query.Where(x => x.JournalId == id);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            return await query.SingleOrDefaultAsync();
        }

        public async Task<List<Journal>> GetByTransactionId(string id, int? branchId, bool? premier)
        {
            IQueryable<Journal> query = _context.Journals;
            query = query.Include(x => x.Branch)
            .Include(x => x.Coa)
            .Include(x => x.CoaParent)
            .Include(x => x.Created)
            .Include(x => x.Reconciled)
            .Include(x => x.Transaction)
            .ThenInclude(x => x.Contact)
            .Include(x => x.Updated);
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            if (premier != null)
                query = query.Where(x => x.IsPremier == premier);

            query = query.Where(m => m.status != Status.Deleted.ToString());

            query = query.Select(x => new Journal
            {
                JournalId = x.JournalId,
                JournalDesc = x.JournalDesc,
                TransactionId = x.TransactionId,
                JournalRef = x.JournalRef,
                CoaCode = x.CoaCode,
                CoaName = x.CoaName,
                CoaParentCode = x.CoaParentCode,
                CoaParentName = x.CoaParentName,
                DebitAmount = x.DebitAmount,
                CreditAmount = x.CreditAmount,
                Source = x.Source,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                CreatedBy = x.CreatedBy,
                CreatedName = x.Created.DisplayName,
                CreatedDate = x.CreatedDate,
                VourcherId = x.VourcherId,
                status = x.status,
                IsPremier = x.IsPremier,
                UpdatedBy = x.UpdatedBy,
                UpdatedName = x.Updated.DisplayName,
                UpdatedDate = x.UpdatedDate,
                ReconciledDate = x.ReconciledDate,
                ReconciledBy = x.ReconciledBy,
                ReconciledName = x.Reconciled.DisplayName,
                TransactionDetail = new Journal.TransactionForJournal
                {
                    TransactionNo = x.Transaction.TransactionNo,
                    TransactionType = x.Transaction.TransactionType,
                    TransactionDate = x.Transaction.TransactionDate,
                    JournalNo = x.Transaction.JournalNo,
                    ContactDetail = x.Transaction.Contact.ContactId != null ? (Contact)x.Transaction.Contact : null
                }
            });

            query = query.Where(x => x.TransactionId.Equals(id));
            query = query.OrderByDescending(x => x.JournalId);

            return await query.ToListAsync();
        }

        public async Task<bool> checkIfExistCoa(string coa, int? branchId)
        {
            IQueryable<Journal> query = _context.Journals;
            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);
            return query.Any(x => x.CoaCode.Equals(coa) || x.CoaCode.Equals(coa));
        }

        public async Task<int> Add(Journal item)
        {

            IQueryable<Journal> query = _context.Journals;
            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);

            _context.Journals.Add(item);
            _context.SaveChanges();
            return item.JournalId;
        }

        public async Task<int> Update(Journal item)
        {
            _context.Journals.Update(item);
            _context.SaveChanges();
            return item.JournalId;
        }

        public async Task<int> UpdateMany(List<Journal> item)
        {
            _context.Journals.UpdateRange(item);
            _context.SaveChanges();
            return 1;
        }

        public async Task<int> Remove(Journal item)
        {

            item.status = Status.Deleted.ToString();
            item.UpdatedBy = item.UpdatedBy;
            item.UpdatedDate = item.UpdatedDate;

            _context.Journals.Update(item);
            _context.SaveChanges();
            return item.JournalId;
        }

        public async Task<int> Void(Journal item)
        {

            item.status = Status.Void.ToString();
            item.UpdatedBy = item.UpdatedBy;
            item.UpdatedDate = item.UpdatedDate;

            _context.Journals.Update(item);
            _context.SaveChanges();
            return item.JournalId;
        }

        public async Task<PagedResult<LedgerResponse>> GetLedgers(
            DateTime? startDate,
            DateTime? endDate,
            string? coaCode,
            int page = 1,
            int pageSize = 10)
        {
            var coaQuery = _context.Coa.AsQueryable();

            if (!string.IsNullOrEmpty(coaCode))
                coaQuery = coaQuery.Where(c => c.CoaCode == coaCode);
            else
                coaQuery = coaQuery.Where(c => _context.Journals.Any(j => j.CoaCode == c.CoaCode));

            int totalCoas = await coaQuery.CountAsync();

            // Paginate COAs only if we are NOT filtering by a specific COA (List Mode)
            var coas = await coaQuery
                .OrderBy(c => c.CoaCode)
                .Skip((!string.IsNullOrEmpty(coaCode) || page == -1) ? 0 : (page - 1) * pageSize)
                .Take((!string.IsNullOrEmpty(coaCode) || page == -1) ? 1000 : pageSize)
                .ToListAsync();

            var result = new List<GeneralLedgerResponse>(); // Note: Keeping original logic structure but mapping to LedgerResponse

            var ledgers = new List<LedgerResponse>();

            foreach (var coa in coas)
            {
                // 1. Calculate Opening Balance (Work Forward from TrxBalance History)
                double openingBalance = 0;
                DateTime transactionFetchStartDate = DateTime.MinValue;

                if (startDate.HasValue)
                {
                    // Find the latest TrxBalance mark PRIOR to or ON the start date?
                    // TrxBalance usually stores Monthly Opening.
                    // If we find TrxBalance for Jan 1, and StartDate is Jan 15.
                    // Opening = Jan 1 Opening + Net(Jan 1 to Jan 15).
                    
                    var lastBalanceDate = await _context.TrxBalances
                        .Where(tb => tb.CoaCode == coa.CoaCode && tb.PeriodDate <= startDate.Value)
                        .OrderByDescending(tb => tb.PeriodDate)
                        .Select(tb => tb.PeriodDate)
                        .FirstOrDefaultAsync();

                    if (lastBalanceDate != default(DateTime))
                    {
                        if (lastBalanceDate < startDate.Value)
                        {
                            // Case A: Checkpoint is in the PAST (e.g. Jan 1, Request Dec 1).
                            // We take the CLOSING Balance of that day (End of Day State).
                            // Then add transactions STRICTLY AFTER that day.
                            
                            // For each branch, get the LATEST record of that day
                            var latestBalances = await _context.TrxBalances
                                .Where(tb => tb.CoaCode == coa.CoaCode && tb.PeriodDate == lastBalanceDate)
                                .GroupBy(tb => tb.BranchId)
                                .Select(g => g.OrderByDescending(x => x.CreatedDate).ThenByDescending(x => x.BalanceId).FirstOrDefault())
                                .ToListAsync();

                            openingBalance = latestBalances.Sum(x => x?.ClosingBalance ?? 0);
                            
                            transactionFetchStartDate = lastBalanceDate;
                        }
                        else
                        {
                            // Case B: Checkpoint is ON the Start Date (e.g. Dec 25, Request Dec 25).
                            // We take the OPENING Balance of that day (Start of Day State).
                            
                            // For each branch, get the EARLIEST record of that day
                            var earliestBalances = await _context.TrxBalances
                                .Where(tb => tb.CoaCode == coa.CoaCode && tb.PeriodDate == lastBalanceDate)
                                .GroupBy(tb => tb.BranchId)
                                .Select(g => g.OrderBy(x => x.CreatedDate).ThenBy(x => x.BalanceId).FirstOrDefault())
                                .ToListAsync();

                            openingBalance = earliestBalances.Sum(x => x?.OpeningBalance ?? 0);
                            
                            transactionFetchStartDate = lastBalanceDate;
                        }
                    }

                    // Add Net Change from TrxBalance Date up to Start Date
                    if (startDate.Value > transactionFetchStartDate)
                    {
                        var interimQuery = _context.Journals
                            .Include(j => j.Transaction)
                            .Where(j => j.CoaCode == coa.CoaCode && j.status == Posted);

                        // IMPORTANT: Strictly Greater Than transactionFetchStartDate because:
                        // If Case A (Past): We took Closing Balance of that day. So we exclude transactions of that day. Txs > Day.
                        // If Case B (Same): transactionFetchStartDate == startDate. Range is Empty. Correct.
                        interimQuery = interimQuery.Where(j => j.Transaction.TransactionDate > transactionFetchStartDate && j.Transaction.TransactionDate < startDate.Value);

                        var interimStats = await interimQuery
                            .GroupBy(x => 1)
                            .Select(g => new
                            {
                                Debit = g.Sum(x => x.DebitAmount ?? 0),
                                Credit = g.Sum(x => x.CreditAmount ?? 0)
                            }).FirstOrDefaultAsync();

                        if (interimStats != null)
                        {
                            double netInterim = 0;
                            if (IsCreditNormal(coa.NormalPosition))
                                netInterim = interimStats.Credit - interimStats.Debit;
                            else
                                netInterim = interimStats.Debit - interimStats.Credit;

                            openingBalance += netInterim;
                        }
                    }
                }
                else
                {
                    // If no Start Date, Opening Balance is effectively 0 (From beginning of time)
                    // Or we could take the Master Balance?
                    // If No Start Date, we usually show All History? 
                    // If showing all history, opening balance of the VIEW is 0.
                    openingBalance = 0; 
                }

                // 2. Fetch Transactions for the Period
                var journalQuery = _context.Journals
                    .Include(j => j.Transaction)
                    .Where(j => j.CoaCode == coa.CoaCode && j.status == Posted);

                if (startDate.HasValue)
                    journalQuery = journalQuery.Where(j => j.Transaction.TransactionDate >= startDate.Value);
                if (endDate.HasValue)
                    journalQuery = journalQuery.Where(j => j.Transaction.TransactionDate <= endDate.Value); // Inclusive

                int totalTx = await journalQuery.CountAsync();

                // Sort: TransactionDate, then CreatedDate, then ID
                journalQuery = journalQuery.OrderBy(j => j.Transaction.TransactionDate)
                                           .ThenBy(j => j.CreatedDate)
                                           .ThenBy(j => j.JournalId);

                // Pagination Logic (Only if Specific COA)
                List<Journal> journals;
                if (!string.IsNullOrEmpty(coaCode) && page != -1 && pageSize != -1)
                {
                    // If paginating transactions, we need to adjust 'starting running balance' for the page
                    // by adding the Net Change of the Skipped transactions.
                    if (page > 1)
                    {
                        var skippedStats = await journalQuery
                            .Take((page - 1) * pageSize)
                            .GroupBy(x => 1)
                            .Select(g => new
                            {
                                Debit = g.Sum(x => x.DebitAmount ?? 0),
                                Credit = g.Sum(x => x.CreditAmount ?? 0)
                            }).FirstOrDefaultAsync();

                        if (skippedStats != null)
                        {
                            double skippedNet = 0;
                            if (IsCreditNormal(coa.NormalPosition))
                                skippedNet = skippedStats.Credit - skippedStats.Debit;
                            else
                                skippedNet = skippedStats.Debit - skippedStats.Credit;

                            openingBalance += skippedNet;
                        }
                    }

                    journals = await journalQuery.Skip((page - 1) * pageSize).Take(pageSize).ToListAsync();
                }
                else
                {
                    // List Mode: Limit to 10 transactions for preview
                    journals = await journalQuery.Take(10).ToListAsync();
                }

                // 3. Map Transactions & Run Balance
                double runningBalance = openingBalance;
                var transactions = new List<LedgerResponse.LedgerTransaction>();

                foreach (var j in journals)
                {
                    double debit = j.DebitAmount ?? 0;
                    double credit = j.CreditAmount ?? 0;

                    if (IsCreditNormal(coa.NormalPosition))
                        runningBalance += (credit - debit);
                    else
                        runningBalance += (debit - credit);

                    transactions.Add(new LedgerResponse.LedgerTransaction
                    {
                        Date = j.Transaction.TransactionDate,
                        TransactionNo = j.Transaction.TransactionNo,
                        JournalNo = j.Transaction.JournalNo,
                        JournalRef = j.JournalRef,
                        TransactionId = j.TransactionId,
                        Description = j.JournalDesc,
                        Debit = j.DebitAmount,
                        Credit = j.CreditAmount,
                        RunningBalance = runningBalance
                    });
                }

                // 4. Calculate Final Closing Balance (At EndDate)
                double periodNet = 0;
                // We need sum of ALL transactions in valid period (ignoring pagination)
                // Re-run summary query or reuse query object?
                // Reuse query filters but without Skip/Take
                var periodStatsQuery = _context.Journals
                    .Include(j => j.Transaction)
                    .Where(j => j.CoaCode == coa.CoaCode && j.status == Posted);

                if (startDate.HasValue) periodStatsQuery = periodStatsQuery.Where(j => j.Transaction.TransactionDate >= startDate.Value);
                if (endDate.HasValue) periodStatsQuery = periodStatsQuery.Where(j => j.Transaction.TransactionDate <= endDate.Value);

                var periodStats = await periodStatsQuery
                     .GroupBy(x => 1)
                    .Select(g => new
                    {
                        Debit = g.Sum(x => x.DebitAmount ?? 0),
                        Credit = g.Sum(x => x.CreditAmount ?? 0)
                    }).FirstOrDefaultAsync();

                if (periodStats != null)
                {
                    if (IsCreditNormal(coa.NormalPosition))
                        periodNet = periodStats.Credit - periodStats.Debit;
                    else
                        periodNet = periodStats.Debit - periodStats.Credit;
                }

                // Closing Balance = Opening Balance (at StartDate) + Net Change (StartDate to EndDate)
                double closingBalance = openingBalance + periodNet;
                
                // Wait, futureStats is nullable.
                // Let's simplify:
                // Closing Balance = OpeningBalance (Initial) + PeriodNet.

                ledgers.Add(new LedgerResponse
                {
                    CoaId = coa.CoaId,
                    CoaCode = coa.CoaCode,
                    CoaName = coa.CoaName,
                    OpeningBalance = openingBalance,
                    ClosingBalance = closingBalance,
                    TotalTransactions = totalTx,
                    Transactions = transactions
                });
            }

            // Decide TotalData for Pagination
            int finalTotal = totalCoas;
            if (!string.IsNullOrEmpty(coaCode) && ledgers.Count > 0)
            {
                finalTotal = ledgers[0].TotalTransactions;
            }

            return new PagedResult<LedgerResponse>(ledgers, finalTotal);
        }

        public async Task<PagedResult<GeneralLedgerResponse>> GetGeneralLedgers(
            DateTime? startDate,
            DateTime? endDate,
            string? coaCode,
            int page = 1,
            int pageSize = 10)
        {

            var coaQuery = _context.Coa
                .Where(c => _context.Journals.Any(j => j.CoaCode == c.CoaCode));

            if (!string.IsNullOrEmpty(coaCode))
                coaQuery = coaQuery.Where(c => c.CoaCode == coaCode);

            var totalData = await coaQuery.CountAsync();

            var coas = await coaQuery
                .OrderBy(c => c.CoaCode)
                .Skip((page - 1) * pageSize)
                .Take(pageSize)
                .ToListAsync();

            var result = new List<GeneralLedgerResponse>();

            foreach (var coa in coas)
            {
                var journalQuery = _context.Journals
                    .Where(j => j.CoaCode == coa.CoaCode);

                if (startDate.HasValue)
                    journalQuery = journalQuery.Where(j => j.CreatedDate >= startDate.Value);

                if (endDate.HasValue)
                    journalQuery = journalQuery.Where(j => j.CreatedDate <= endDate.Value);

                var journals = await journalQuery
                    .OrderBy(j => j.CreatedDate)
                    .Select(j => new GeneralLedgerResponse.JournalForLedger
                    {
                        JournalId = j.JournalId,
                        CreatedDate = j.CreatedDate,
                        JournalDesc = j.JournalDesc,
                        Debit = j.DebitAmount,
                        Credit = j.CreditAmount,
                        CurrentBalance = j.CurrentBalance,
                        PrevBalance = j.PrevBalance,
                        Source = j.Source,
                    })
                    .ToListAsync();

                result.Add(new GeneralLedgerResponse
                {
                    CoaCode = coa.CoaCode,
                    CoaName = coa.CoaName,
                    Journals = journals
                });
            }

            return new PagedResult<GeneralLedgerResponse>(result, totalData);
        }

        public async Task<PagedResult<SummaryLedgerResponse>> GetLedgerSummary(
            DateTime? startDate,
            DateTime? endDate,
            string? coaCode = null,
            int page = 1,
            int pageSize = 10,
            string? coaType = null)
        {
            // 1. Fetch COAs (similar to General Ledger)
            var coaQuery = _context.Coa
                .Where(c => _context.Journals.Any(j => j.CoaCode == c.CoaCode) || _context.TrxBalances.Any(tb => tb.CoaCode == c.CoaCode));

            if (!string.IsNullOrEmpty(coaCode))
                coaQuery = coaQuery.Where(c => c.CoaCode == coaCode);

             if (!string.IsNullOrEmpty(coaType))
                 coaQuery = coaQuery.Where(c => c.AccountType.AccountTypeName == coaType);

            // Filter out Headers usually, but GL Summary might want to see all active? 
            // Assuming user wants transaction-level accounts mostly, but let's keep all valid ones.
            
            var totalData = await coaQuery.CountAsync();

            var coaListQuery = coaQuery.OrderBy(c => c.CoaCode);
            var coas = new List<Coa>();

            if (page == -1 && pageSize == -1)
            {
                coas = await coaListQuery.ToListAsync();
            }
            else
            {
                coas = await coaListQuery
                    .Skip((page - 1) * pageSize)
                    .Take(pageSize)
                    .ToListAsync();
            }

            var summaryList = new List<SummaryLedgerResponse>();
            
            Console.WriteLine($"[GL Summary] Start: {startDate}, End: {endDate}, COA: {coaCode}");

            foreach (var coa in coas)
            {
                // 2. Calculate Beginning Balance (Work Forward Logic from GetLedgers)
                double beginningBalance = 0;
                DateTime txFetchStart = DateTime.MinValue;

                if (startDate.HasValue)
                {
                   // Find the latest balance record STRICTLY BEFORE startDate
                   // allowing us to take its ClosingBalance as the starting point.
                   var lastBalanceDate = await _context.TrxBalances
                        .Where(tb => tb.CoaCode == coa.CoaCode && tb.PeriodDate < startDate.Value)
                        .OrderByDescending(tb => tb.PeriodDate)
                        .ThenByDescending(tb => tb.BalanceId)
                        .Select(tb => tb.PeriodDate)
                        .FirstOrDefaultAsync();

                    Console.WriteLine($"[GL Summary] COA {coa.CoaCode}: LastBalanceDate {lastBalanceDate}");

                    if (lastBalanceDate != default(DateTime))
                    {
                         var balanceRecord = await _context.TrxBalances
                            .Where(tb => tb.CoaCode == coa.CoaCode && tb.PeriodDate == lastBalanceDate)
                            .OrderByDescending(tb => tb.BalanceId)
                            .FirstOrDefaultAsync();
                            
                        if (balanceRecord != null)
                        {
                            // Use Closing Balance of the previous period as the Opening for this one
                            beginningBalance = balanceRecord.ClosingBalance;
                            txFetchStart = lastBalanceDate;
                            Console.WriteLine($"[GL Summary] COA {coa.CoaCode}: Found Base Balance (Closing) {beginningBalance} at {lastBalanceDate}");
                        }
                    }

                    // If startDate is newer than our fetch start, add interim transactions
                    // Logic: Base Balance (End of txFetchStart) + Journals (Strictly > txFetchStart AND < startDate)
                    if (startDate.Value > txFetchStart)
                    {
                         var interimQuery = _context.Journals
                            .Include(j => j.Transaction)
                            .Where(j => j.CoaCode == coa.CoaCode && j.status == Posted);

                        // Strictly greater than txFetchStart because we used ClosingBalance of that date
                        interimQuery = interimQuery.Where(j => j.Transaction.TransactionDate > txFetchStart && j.Transaction.TransactionDate < startDate.Value);

                        var interimStats = await interimQuery
                            .GroupBy(x => 1)
                            .Select(g => new
                            {
                                Debit = g.Sum(x => x.DebitAmount ?? 0),
                                Credit = g.Sum(x => x.CreditAmount ?? 0)
                            }).FirstOrDefaultAsync();
                            
                        if (interimStats != null)
                        {
                            double netInterim = 0;
                            if (IsCreditNormal(coa.NormalPosition))
                                netInterim = interimStats.Credit - interimStats.Debit;
                            else
                                netInterim = interimStats.Debit - interimStats.Credit;

                            beginningBalance += netInterim;
                            Console.WriteLine($"[GL Summary] COA {coa.CoaCode}: Add Interim Net {netInterim}. New Beg: {beginningBalance}");
                        }
                    }
                }

                // 3. Calculate Period Movements (Start to End)
                double periodDebit = 0;
                double periodCredit = 0;

                var periodQuery = _context.Journals
                    .Include(j => j.Transaction)
                    .Where(j => j.CoaCode == coa.CoaCode && j.status == Posted);

                if (startDate.HasValue) periodQuery = periodQuery.Where(j => j.Transaction.TransactionDate >= startDate.Value);
                if (endDate.HasValue) periodQuery = periodQuery.Where(j => j.Transaction.TransactionDate <= endDate.Value);

                var periodStats = await periodQuery
                    .GroupBy(x => 1)
                    .Select(g => new
                    {
                        Debit = g.Sum(x => x.DebitAmount ?? 0),
                        Credit = g.Sum(x => x.CreditAmount ?? 0)
                    }).FirstOrDefaultAsync();

                if (periodStats != null)
                {
                    periodDebit = periodStats.Debit;
                    periodCredit = periodStats.Credit;
                }

                // 4. Calculate Net Change & Ending Balance
                double netChange = 0;
                if (IsCreditNormal(coa.NormalPosition))
                    netChange = periodCredit - periodDebit;
                else
                    netChange = periodDebit - periodCredit;

                double endingBalance = beginningBalance + netChange;

                summaryList.Add(new SummaryLedgerResponse
                {
                    CoaId = coa.CoaId,
                    CoaCode = coa.CoaCode,
                    CoaName = coa.CoaName,
                    BeginningBalance = beginningBalance,
                    Debit = periodDebit,
                    Credit = periodCredit,
                    NetChange = netChange,
                    EndingBalance = endingBalance
                });
            }

            return new PagedResult<SummaryLedgerResponse>(summaryList, totalData);
        }

        public async Task<BalanceSheetResponse> GetBalanceSheetAsync(DateTime? startDate, DateTime? endDate, int branchId)
        {
            var dataOption = await _optionService.GetByName("CFG_REPORT_BALANCESHEET", branchId);
            if (dataOption == null) throw new ApplicationException("Configuration CFG_REPORT_BALANCESHEET not found");
            var sections = JsonConvert.DeserializeObject<List<BalanceSheetSection>>(dataOption.OptionValue);

            var response = new BalanceSheetResponse();
            response.Data = sections;

            // Pre-calculation: Identify all relevant COAs for the Balance Sheet to optimize fetching
            // However, since structure is recursive, we'll iterate. To optimize, we should implement a better strategy later.
            // For now, let's fix the logic first.
            
            // "Balance Sheet" is typically "As Of" endDate.
            // We need to calculate the accumulative balance for each account up to endDate.
            
            DateTime targetDate = endDate ?? DateTime.UtcNow;
            if (targetDate.Kind == DateTimeKind.Unspecified)
                targetDate = DateTime.SpecifyKind(targetDate, DateTimeKind.Utc);
            
            // --- OPTIMIZATION START ---
            
            // --- OPTIMIZATION START ---
            
            // 1. Fetch ALL Active COAs or just relevant info
            var allCoas = await _context.Coa.Where(c => c.BranchId == branchId && c.Status != "Deleted").ToListAsync();
            
            // 2. Pre-calculate Balances for ALL COAs as of targetDate
            var bulkBalances = await GetBulkBalancesAsOf(targetDate);
            
            // 3. Pre-fetch Year Journals for Current Year Earnings (StartOfYear to TargetDate)
            DateTime startOfYear = new DateTime(targetDate.Year, 1, 1, 0, 0, 0, DateTimeKind.Utc);
            var yearJournals = await _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.status == Posted && j.Transaction.TransactionDate >= startOfYear && j.Transaction.TransactionDate <= targetDate)
                .AsNoTracking()
                .ToListAsync();

            // Calculate Current Year Earnings
            double currentYearEarnings = await CalculateCurrentYearEarningsBulk(targetDate, allCoas, yearJournals, branchId);

            foreach (var section in sections)
            {
                // Pass the pre-calculated dictionary
                await FillBalanceSection(section, bulkBalances, currentYearEarnings);
            }

            // --- OPTIMIZATION END ---

            // Calculate Summary
            double totalAssets = sections.Where(s => s.SectionName.ToUpper().Contains("AKTIVA") || s.SectionName.ToUpper().Contains("ASSET")).Sum(s => s.Total);
            double totalLiabilities = sections.Where(s => s.SectionName.ToUpper().Contains("KEWAJIBAN") || s.SectionName.ToUpper().Contains("HUTANG") || s.SectionName.ToUpper().Contains("LIABILITAS")).Sum(s => s.Total);
            double totalEquity = sections.Where(s => s.SectionName.ToUpper().Contains("EKUITAS") || s.SectionName.ToUpper().Contains("MODAL")).Sum(s => s.Total);

            response.Summary = new BalanceSheetSummary
            {
                TotalAssets = totalAssets,
                TotalLiabilities = totalLiabilities,
                TotalEquity = totalEquity
            };

            return response;
        }

        public async Task<ProfitLossResponse> GetProfitAndLosAsync(DateTime? startDate, DateTime? endDate, int branchId)
        {
            var dataOption = await _optionService.GetByName("CFG_REPORT_PROFITLOSS", branchId);
            if (dataOption == null) throw new ApplicationException("Configuration CFG_REPORT_PROFITLOSS not found");
            var sections = JsonConvert.DeserializeObject<List<BalanceSheetSection>>(dataOption.OptionValue);

            // --- OPTIMIZATION START ---
            DateTime start = startDate ?? DateTime.UtcNow.AddMonths(-1);
            DateTime end = endDate ?? DateTime.UtcNow;

            if (start.Kind == DateTimeKind.Unspecified) start = DateTime.SpecifyKind(start, DateTimeKind.Utc);
            if (end.Kind == DateTimeKind.Unspecified) end = DateTime.SpecifyKind(end, DateTimeKind.Utc);

            var allCoas = await _context.Coa.Where(c => c.BranchId == branchId && c.Status != "Deleted").ToListAsync();
            var bulkMovements = await GetBulkMovements(start, end);
            
            foreach (var section in sections)
            {
                await FillSectionPeriod(section, allCoas, bulkMovements);
            }
            // --- OPTIMIZATION END ---

            // --- OPTIMIZATION END ---

            // Calculate Summaries
            // Logic:
            // Pendapatan (Revenue) -> Positive
            // Beban (Expense) -> Positive Magnitude from Helper
            // Profit = Revenue - Expense

            double pendapatan = sections.Where(s => s.SectionName.ToUpper().Contains("PENDAPATAN") && !s.SectionName.ToUpper().Contains("LAIN")).Sum(s => s.Total);
            double bebanLangsung = sections.Where(s => s.SectionName.ToUpper().Contains("BEBAN") && s.SectionName.ToUpper().Contains("LANGSUNG")).Sum(s => s.Total);
            double bebanTidakLangsung = sections.Where(s => s.SectionName.ToUpper().Contains("BEBAN") && s.SectionName.ToUpper().Contains("TIDAK LANGSUNG")).Sum(s => s.Total);
            double lainLain = sections.Where(s => s.SectionName.ToUpper().Contains("LAIN")).Sum(s => s.Total);

            // Gross Profit = Revenue - COGS
            double grossProfit = pendapatan - bebanLangsung;
            
            // Operating Profit = Gross - OpEx
            double operatingProfit = grossProfit - bebanTidakLangsung;
            
            // Net Profit = Operating + Other (Assuming Other is Net, or usually +Revenue -Expense)
            // If LainLain is "Pendapatan (Beban) Lain-Lain", it might be mixed. 
            // If it's pure Revenue, +; if pure Cost, -.
            // Assuming the value logic handles the net of that section.
            // If the section sums positive for revenue and positive for expense, this is risky.
            // But usually "Lain-Lain" is a single section.
            // Let's assume Additive for now, but strictly it depends on content.
            double netProfit = operatingProfit + lainLain;

            return new ProfitLossResponse
            {
                Data = sections,
                Summary = new ProfitLossSummary
                {
                    TotalGrossProfit = grossProfit,
                    TotalOperatingProfit = operatingProfit,
                    TotalNetProfit = netProfit
                }
            };
        }

        // --- OPTIMIZED BULK METHODS ---

        private async Task FillSectionPeriod(BalanceSheetSection section, List<Coa> allCoas, Dictionary<string, double> coaBalances)
        {
            // 1. Process Children First
            foreach (var sub in section.SubSection)
            {
                await FillSectionPeriod(sub, allCoas, coaBalances);
            }

            // 2. Calculate Total
            if (!string.IsNullOrEmpty(section.CoaCode))
            {
                // Has Code -> Use Code Logic (StartsWith)
                // Filter all COAs that match the prefix
                // Note: allCoas here is the List of COA entities, coaBalances is Code -> Value.
                // We don't strictly need allCoas if we just scan dictionary keys, BUT dictionary keys are strict codes.
                // If section.CoaCode is "4", we need to sum all dictionary keys starting with "4".
                
                double aggregatedTotal = 0;
                foreach(var kvp in coaBalances)
                {
                    if(kvp.Key.StartsWith(section.CoaCode))
                    {
                        aggregatedTotal += kvp.Value;
                    }
                }
                section.Total = aggregatedTotal;
            }
            else
            {
                // No Code -> Sum Children
                section.Total = section.SubSection.Sum(s => s.Total);
            }
        }

        private async Task FillBalanceSection(BalanceSheetSection section, Dictionary<string, double> balances, double? currentEarnings = null)
        {
             // 1. Process Children First
            foreach (var sub in section.SubSection)
            {
                await FillBalanceSection(sub, balances, currentEarnings);
            }

            // 2. Check Injection
            if (currentEarnings.HasValue && 
                (section.SectionName.Equals("Laba Periode Tahun Berjalan", StringComparison.OrdinalIgnoreCase)
                 || section.SectionName.Equals("Laba Tahun Berjalan", StringComparison.OrdinalIgnoreCase)))
            {
                section.Total = currentEarnings.Value;
            }
            else if (!string.IsNullOrEmpty(section.CoaCode))
            {
                // Has Code -> Use Code Logic (StartsWith)
                double aggregatedTotal = 0;
                foreach(var kvp in balances)
                {
                    if(kvp.Key.StartsWith(section.CoaCode))
                    {
                        aggregatedTotal += kvp.Value;
                    }
                }
                section.Total = aggregatedTotal;
            }
            else
            {
                // No Code -> Sum Children
                section.Total = section.SubSection.Sum(s => s.Total);
            }
        }

        private async Task<double> CalculateCurrentYearEarningsBulk(DateTime asOfDate, List<Coa> allCoas, List<Journal> yearJournals, int branchId)
        {
             // Revenue & Expense prefixes from config
            DateTime startOfYear = new DateTime(asOfDate.Year, 1, 1, 0, 0, 0, DateTimeKind.Utc);

            var revenuePrefixOpt = await _optionService.GetByName("COA_PREFIX_REVENUE", branchId);
            var revenueAltPrefixOpt = await _optionService.GetByName("COA_PREFIX_REVENUE_ALT", branchId);
            var expensePrefixOpt = await _optionService.GetByName("COA_PREFIX_EXPENSE", branchId);
            var expenseAltPrefixOpt = await _optionService.GetByName("COA_PREFIX_EXPENSE_ALT", branchId);

            var revenuePrefixes = new List<string> { revenuePrefixOpt?.OptionValue ?? "4" };
            if (revenueAltPrefixOpt != null && !string.IsNullOrEmpty(revenueAltPrefixOpt.OptionValue))
                revenuePrefixes.AddRange(revenueAltPrefixOpt.OptionValue.Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()));

            var expensePrefixes = new List<string>();
            var expenseRaw = expensePrefixOpt?.OptionValue ?? "5,6";
            expensePrefixes.AddRange(expenseRaw.Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()));
            if (expenseAltPrefixOpt != null && !string.IsNullOrEmpty(expenseAltPrefixOpt.OptionValue))
                expensePrefixes.AddRange(expenseAltPrefixOpt.OptionValue.Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()));

            var revenueCoas = allCoas.Where(c => c.CoaCode != null && revenuePrefixes.Any(p => c.CoaCode.StartsWith(p))).Select(c => c.CoaCode).ToHashSet();
            var expenseCoas = allCoas.Where(c => c.CoaCode != null && expensePrefixes.Any(p => c.CoaCode.StartsWith(p))).Select(c => c.CoaCode).ToHashSet();

            double totalRevenue = 0;
            double totalExpense = 0;

            // Iterate ALL journals once
            foreach (var j in yearJournals)
            {
                // Only consider journals up to asOfDate (redundant if yearJournals is already filtered, but safe)
                if (j.Transaction == null || j.Transaction.TransactionDate > asOfDate) continue;

                if (j.CoaCode != null)
                {
                    // Check Revenue (Normal Credit)
                    // If we use StartsWith for each journal it's slow. Better to check if CoaCode is in the Set.
                    // But Set contains full codes.
                    // Assuming yearJournals has strict CoaCode.
                    
                    if (revenueCoas.Contains(j.CoaCode))
                    {
                        // Revenue Normal Credit: Credit - Debit
                        totalRevenue += (j.CreditAmount ?? 0) - (j.DebitAmount ?? 0);
                    }
                    else if (expenseCoas.Contains(j.CoaCode))
                    {
                         // Expense Normal Debit: Debit - Credit
                        totalExpense += (j.DebitAmount ?? 0) - (j.CreditAmount ?? 0);
                    }
                }
            }

            return totalRevenue - totalExpense;
        }


        // Replaces recursive SQL calls with Bulk Calculation
        private async Task<Dictionary<string, double>> GetBulkBalancesAsOf(DateTime asOfDate)
        {
            // 1. Get Latest TrxBalance for ALL COAs <= asOfDate
            // Postgres Distinct On is great, but EF Core support varies. Using GroupBy.
            
            // Raw SQL for performance and correctness
            var latestBalances = await _context.TrxBalances
                .FromSqlRaw(@"
                    SELECT DISTINCT ON (coa_code) *
                    FROM public.trx_balance
                    WHERE period_date <= {0}
                    ORDER BY coa_code, period_date DESC, id DESC", asOfDate)
                .AsNoTracking()
                .ToListAsync();

            var balanceDict = latestBalances.ToDictionary(b => b.CoaCode, b => b.ClosingBalance);
            var lastBalanceDates = latestBalances.ToDictionary(b => b.CoaCode, b => b.PeriodDate);

            // 2. Scan Journals from [Earliest Balance Date] to [AsOfDate]
            // Optimization: Find the global minimum date we need to scan from.
            DateTime minDate = lastBalanceDates.Values.DefaultIfEmpty(DateTime.MinValue).Min();
            if (minDate == DateTime.MinValue) minDate = new DateTime(2000,1,1).ToUniversalTime(); // Fallback

            // Fetch Journals
            // We need TransactionDate, CoaCode, Debit, Credit
            var movements = await _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.status == Posted && j.Transaction.TransactionDate > minDate && j.Transaction.TransactionDate <= asOfDate)
                .Select(j => new { j.CoaCode, j.DebitAmount, j.CreditAmount, j.Transaction.TransactionDate })
                .AsNoTracking()
                .ToListAsync();

            // 3. Update dictionary
            // We need the Normal Position of every COA to apply Debit/Credit correctly?
            // Actually, Balances in DB are usually ABSOLUTE? Or signed?
            // Assuming ClosingBalance is Signed or Standard.
            // Let's fetch COA Normal Positions
            var coaNormals = await _context.Coa.Select(c => new { c.CoaCode, c.NormalPosition }).ToDictionaryAsync(c => c.CoaCode, c => c.NormalPosition);

            // Process movements
            foreach (var mov in movements)
            {
                if (mov.CoaCode == null) continue;

                // Only apply movement if it is AFTER the last balance date for this SPECIFIC COA
                if (lastBalanceDates.TryGetValue(mov.CoaCode, out DateTime lastDate))
                {
                    if (mov.TransactionDate <= lastDate) continue; 
                }
                
                // Add to dict (Initialize if not exists)
                if (!balanceDict.ContainsKey(mov.CoaCode)) balanceDict[mov.CoaCode] = 0;

                string? normalPos = coaNormals.ContainsKey(mov.CoaCode) ? coaNormals[mov.CoaCode] : null;
                
                double debit = mov.DebitAmount ?? 0;
                double credit = mov.CreditAmount ?? 0;

                if (IsCreditNormal(normalPos))
                    balanceDict[mov.CoaCode] += (credit - debit);
                else
                    balanceDict[mov.CoaCode] += (debit - credit);
            }

            return balanceDict;
        }

        // Helper for Profit Loss Bulk
        private async Task<Dictionary<string, double>> GetBulkMovements(DateTime startDate, DateTime endDate)
        {
             var movements = await _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.status == Posted && j.Transaction.TransactionDate >= startDate && j.Transaction.TransactionDate <= endDate)
                .Select(j => new { j.CoaCode, j.DebitAmount, j.CreditAmount })
                .AsNoTracking()
                .ToListAsync();
            
            var coaNormals = await _context.Coa.Select(c => new { c.CoaCode, c.NormalPosition }).ToDictionaryAsync(c => c.CoaCode, c => c.NormalPosition);
            var result = new Dictionary<string, double>();

             foreach (var mov in movements)
            {
                if (string.IsNullOrEmpty(mov.CoaCode)) continue;
                
                 if (!result.ContainsKey(mov.CoaCode)) result[mov.CoaCode] = 0;

                string? normalPos = coaNormals.ContainsKey(mov.CoaCode) ? coaNormals[mov.CoaCode] : null;
                
                double debit = mov.DebitAmount ?? 0;
                double credit = mov.CreditAmount ?? 0;

                if (IsCreditNormal(normalPos))
                    result[mov.CoaCode] += (credit - debit);
                else
                    result[mov.CoaCode] += (debit - credit);
            }

            return result;
        }

        private async Task<double> GetCoaBalance(Coa coa, DateTime? startDate, DateTime? endDate)
        {
            var query = _context.Journals
                .Where(j => j.status == Posted && j.CoaCode != null && j.CoaCode.StartsWith(coa.CoaCode!));

            if (startDate.HasValue)
                query = query.Where(j =>
                    j.Transaction != null &&
                    j.Transaction.TransactionDate >= startDate.Value);

            if (endDate.HasValue)
                query = query.Where(j =>
                    j.Transaction != null &&
                    j.Transaction.TransactionDate <= endDate.Value);

            var list = await query.ToListAsync();

            double debit = list.Sum(x => x.DebitAmount ?? 0);
            double credit = list.Sum(x => x.CreditAmount ?? 0);

            if (IsCreditNormal(coa.NormalPosition))
                return credit - debit;
            else
                return debit - credit;
        }

        public async Task<int> PostJournal(List<string> transactionIds)
        {
            var transactions = await _context.Transactions
                .Include(t => t.Details)
                .Where(t => transactionIds.Contains(t.TransactionId) && t.PostedDate == null)
                .ToListAsync();

            if (!transactions.Any()) return 0;

            DateTime now = DateTime.UtcNow;

            foreach (var trx in transactions)
            {
                // Nomor jurnal bersifat final sejak transaksi dibuat: posting TIDAK
                // menomori ulang. Sebelumnya baris ini memanggil GenerateJournalNo(now)
                // tanpa syarat, yang menimbulkan dua masalah:
                //   1. nomor lama tertimpa, dan memakai bulan posting alih-alih bulan
                //      transaksi (transaksi Juli yang di-post Agustus jadi JRN/2026/08/..),
                //   2. dalam satu batch semua transaksi mendapat nomor identik, karena
                //      SaveChanges baru dipanggil di akhir sehingga MAX di DB tidak
                //      bergerak selama loop.
                // Yang tersisa di sini hanya mengisi transaksi yang memang belum bernomor,
                // memakai tanggal transaksinya sendiri.
                if (string.IsNullOrEmpty(trx.JournalNo))
                    trx.JournalNo = await GenerateJournalNo(trx.TransactionDate ?? now);

                trx.PostedDate = now;
                trx.UpdatedDate = now;

                // Logic moved to Database Trigger (see triggers.sql)
                // if (trx.Details != null)
                // {
                //     foreach (var detail in trx.Details)
                //     {
                //         if (string.IsNullOrEmpty(detail.CoaCode)) continue;

                //         // Update TrxBalance
                //         await UpdateTrxBalance(detail.CoaCode, trx.TransactionDate ?? now, detail.DebitAmount ?? 0, detail.CreditAmount ?? 0, trx.BranchId);

                //         // Update Master Coa Balance
                //         var coa = await _context.Coa.FirstOrDefaultAsync(c => c.CoaCode == detail.CoaCode);
                //         if (coa != null)
                //         {
                //             double debit = detail.DebitAmount ?? 0;
                //             double credit = detail.CreditAmount ?? 0;

                //             if (coa.NormalPosition?.ToUpper() == "CREDIT")
                //                 coa.Balance = (coa.Balance ?? 0) + (credit - debit);
                //             else
                //                 coa.Balance = (coa.Balance ?? 0) + (debit - credit);

                //             _context.Coa.Update(coa);
                //         }
                //     }
                // }
                _context.Transactions.Update(trx);
            }

            return await _context.SaveChangesAsync();
        }

        private async Task UpdateTrxBalance(string coaCode, DateTime date, double debit, double credit, int? branchId)
        {
            DateTime period = DateTime.SpecifyKind(new DateTime(date.Year, date.Month, 1), DateTimeKind.Utc);

            var balance = await _context.TrxBalances
                .FirstOrDefaultAsync(b => b.CoaCode == coaCode && b.PeriodDate == period && b.BranchId == branchId);

            if (balance == null)
            {
                // Get previous closing balance
                var prevPeriod = period.AddMonths(-1);
                var prevBalance = await _context.TrxBalances
                     .FirstOrDefaultAsync(b => b.CoaCode == coaCode && b.PeriodDate == prevPeriod && b.BranchId == branchId);

                double opening = prevBalance?.ClosingBalance ?? 0;

                balance = new TrxBalance
                {
                    CoaCode = coaCode,
                    PeriodDate = period,
                    OpeningBalance = opening,
                    DebitAmount = 0,
                    CreditAmount = 0,
                    ClosingBalance = opening,
                    BranchId = branchId,
                    CreatedDate = DateTime.UtcNow,
                    UpdatedDate = DateTime.UtcNow
                };
                await _context.TrxBalances.AddAsync(balance);
            }

            balance.DebitAmount += debit;
            balance.CreditAmount += credit;

            // Recalculate Closing
            var coa = await _context.Coa.AsNoTracking().FirstOrDefaultAsync(c => c.CoaCode == coaCode);
            bool isCreditNormal = IsCreditNormal(coa?.NormalPosition);

            if (isCreditNormal)
            {
                balance.ClosingBalance = balance.OpeningBalance + balance.CreditAmount - balance.DebitAmount;
            }
            else
            {
                balance.ClosingBalance = balance.OpeningBalance + balance.DebitAmount - balance.CreditAmount;
            }

            balance.UpdatedDate = DateTime.UtcNow;
        }

        /// <summary>
        /// Menomori jurnal dengan pola JRN/{yyyy}/{MM}/{urut}, urut reset tiap bulan.
        ///
        /// Nomor diambil dari counter khusus (mst_journal_counter), BUKAN dari
        /// MAX(journal_no) di trx_transaction. Dua alasan:
        ///   1. journal_no bertipe teks. Cara lama memakai ORDER BY journal_no DESC,
        ///      yang salah begitu nomor tembus 4 digit ('JRN/2026/08/9999' lebih besar
        ///      daripada 'JRN/2026/08/10050' secara teks). Akibatnya counter mentok dan
        ///      semua transaksi baru memakai nomor yang sama.
        ///   2. Pola baca-MAX-lalu-tambah-satu tidak aman terhadap race: dua request
        ///      bersamaan membaca MAX yang sama. INSERT .. ON CONFLICT DO UPDATE di
        ///      bawah mengunci baris counter, jadi penomoran terserialisasi.
        ///
        /// Counter di-seed oleh SQL/fix_duplicate_journal_no.sql (STEP 5b). Bulan yang
        /// belum punya baris counter mulai dari 1.
        /// </summary>
        public async Task<string> GenerateJournalNo(DateTime date)
        {
            string prefix = $"JRN/{date:yyyy}/{date:MM}/";

            // INSERT dibungkus CTE dan hasilnya diambil dengan ToListAsync (bukan
            // SingleAsync): operator LINQ seperti Single/First membuat EF membungkus
            // SQL ini dalam subquery, dan statement data-modifying tidak sah di sana.
            var rows = await _context.Database
                .SqlQuery<long>($@"
                    WITH ins AS (
                        INSERT INTO public.mst_journal_counter AS c (prefix, last_no)
                        VALUES ({prefix}, 1)
                        ON CONFLICT (prefix) DO UPDATE SET last_no = c.last_no + 1
                        RETURNING c.last_no
                    )
                    SELECT last_no AS ""Value"" FROM ins")
                .ToListAsync();

            return $"{prefix}{rows.Single():D4}";
        }

        public async Task<byte[]> ExportHistory(string? keyword, string? status, string? coaCode, int? contactId, int? branchId, string? transactionNo, string? transactionType, string? transactionStatus, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType)
        {
            var result = await GetAll(keyword, status, coaCode, contactId, branchId, transactionNo, transactionType, transactionStatus, startDate, endDate, -1, -1, orderBy, orderType);
            return ExportToDocs.ConvertJournalHistoryToExcel(result.Data);
        }

        public async Task<byte[]> ExportLedgerSummary(DateTime? startDate, DateTime? endDate, string? coaCode = null, string? coaType = null)
        {
            var result = await GetLedgerSummary(startDate, endDate, coaCode, -1, -1, coaType);
            return ExportToDocs.ConvertLedgerSummaryToExcel(result.Data);
        }

        public async Task<byte[]> ExportBalanceSheet(DateTime? startDate, DateTime? endDate, int branchId)
        {
            var result = await GetBalanceSheetAsync(startDate, endDate, branchId);
            return ExportToDocs.ConvertBalanceSheetToExcel(result, startDate, endDate);
        }

        public async Task<byte[]> ExportProfitLoss(DateTime? startDate, DateTime? endDate, int branchId)
        {
            var result = await GetProfitAndLosAsync(startDate, endDate, branchId);
            return ExportToDocs.ConvertProfitLossToExcel(result, startDate, endDate);
        }

        public async Task<CashFlowResponse> GetCashFlowAsync(DateTime? startDate, DateTime? endDate, int branchId)
        {
            var dataOption = await _optionService.GetByName("CFG_REPORT_CASHFLOW", branchId);
            if (dataOption == null) throw new ApplicationException("Configuration CFG_REPORT_CASHFLOW not found");
            var sections = JsonConvert.DeserializeObject<List<BalanceSheetSection>>(dataOption.OptionValue);

            DateTime start = startDate ?? DateTime.UtcNow.AddMonths(-1);
            DateTime end = endDate ?? DateTime.UtcNow;

            if (start.Kind == DateTimeKind.Unspecified) start = DateTime.SpecifyKind(start, DateTimeKind.Utc);
            if (end.Kind == DateTimeKind.Unspecified) end = DateTime.SpecifyKind(end, DateTimeKind.Utc);

            var allCoas = await _context.Coa.Where(c => c.BranchId == branchId && c.Status != "Deleted").ToListAsync();
            var bulkMovements = await GetBulkMovements(start, end);

            // Special handling: COA uses payer_code filter instead of coa_code
            var specialCoaOpt = await _optionService.GetByName("COA_CASHFLOW_SPECIAL", branchId);
            var payerPrivateOpt = await _optionService.GetByName("PAYER_CODE_PRIVATE", branchId);
            var revenuePrefixOpt = await _optionService.GetByName("COA_PREFIX_REVENUE", branchId);
            string specialCoaCode = specialCoaOpt?.OptionValue ?? "401101004";
            string payerCodePrivate = payerPrivateOpt?.OptionValue ?? "Prb";
            string revPrefix = revenuePrefixOpt?.OptionValue ?? "4";
            var specialMovements = await _context.Journals
                .Include(j => j.Transaction)
                .Where(j => j.status == Posted && j.Transaction.TransactionDate >= start && j.Transaction.TransactionDate <= end
                    && j.CoaCode != null && j.CoaCode.StartsWith(revPrefix)
                    && j.PayerCode == payerCodePrivate)
                .Select(j => new { j.DebitAmount, j.CreditAmount })
                .AsNoTracking()
                .ToListAsync();

            if (specialMovements.Any())
            {
                var coaNormalPos = await _context.Coa.Where(c => c.CoaCode == specialCoaCode).Select(c => c.NormalPosition).FirstOrDefaultAsync();
                double specialTotal = 0;
                foreach (var mov in specialMovements)
                {
                    double debit = mov.DebitAmount ?? 0;
                    double credit = mov.CreditAmount ?? 0;
                    specialTotal += IsCreditNormal(coaNormalPos) ? (credit - debit) : (debit - credit);
                }
                bulkMovements[specialCoaCode] = specialTotal;
            }

            foreach (var section in sections)
            {
                await FillSectionPeriod(section, allCoas, bulkMovements);
            }

            // Calculate summaries by matching section names
            double operating = sections.Where(s => s.SectionName.ToUpper().Contains("OPERATING") || s.SectionName.ToUpper().Contains("OPERASI")).Sum(s => s.Total);
            double investing = sections.Where(s => s.SectionName.ToUpper().Contains("INVESTING") || s.SectionName.ToUpper().Contains("INVESTASI")).Sum(s => s.Total);
            double financing = sections.Where(s => s.SectionName.ToUpper().Contains("FINANCING") || s.SectionName.ToUpper().Contains("PENDANAAN")).Sum(s => s.Total);
            double netCashFlow = operating + investing + financing;

            // Cash Beginning: sum of cash/bank COA balances at start of period
            var cashCoas = allCoas.Where(c => c.IsCashBank == true && !string.IsNullOrEmpty(c.CoaCode)).Select(c => c.CoaCode).ToList();
            double cashBeginning = 0;
            if (cashCoas.Any())
            {
                var cashBalances = await GetBulkBalancesAsOf(start.AddDays(-1));
                cashBeginning = cashCoas.Where(c => cashBalances.ContainsKey(c)).Sum(c => cashBalances[c]);
            }

            double cashEnding = cashBeginning + netCashFlow;

            return new CashFlowResponse
            {
                Data = sections,
                Summary = new CashFlowSummary
                {
                    OperatingActivities = operating,
                    InvestingActivities = investing,
                    FinancingActivities = financing,
                    NetCashFlow = netCashFlow,
                    CashBeginning = cashBeginning,
                    CashEnding = cashEnding
                }
            };
        }

        public async Task<byte[]> ExportCashFlow(DateTime? startDate, DateTime? endDate, int branchId)
        {
            var result = await GetCashFlowAsync(startDate, endDate, branchId);
            return ExportToDocs.ConvertCashFlowToExcel(result, startDate, endDate);
        }

        public async Task<ImportResult> ImportGeneralJournal(FileUpload fileUpload, int branchId, int userId)
        {
            if (fileUpload?.File == null || fileUpload.File.Length == 0)
            {
                return new ImportResult { Success = false, Message = "No file uploaded." };
            }

            using var stream = fileUpload.File.OpenReadStream();
            return await ImportGeneralJournalStream(stream, branchId, userId);
        }

        /// <summary>
        /// Core of the standard-template import, reading straight from a stream so both the
        /// synchronous endpoint and the background queue worker (which reads a file off disk)
        /// share one implementation.
        /// </summary>
        public async Task<ImportResult> ImportGeneralJournalStream(Stream fileStream, int branchId, int userId)
        {
            var errors = new List<ImportValidationError>();
            var journalGroups = new Dictionary<string, List<ImportJournalLine>>();
            int totalRows = 0;

            // Get default status from options
            var defaultStatusOption = await _optionService.GetByName("DEFAULT_JOURNAL_IMPORT_STATUS", branchId);
            string importStatus = defaultStatusOption?.OptionValue ?? Status.Pending.ToString();

            using (var package = new ExcelPackage(fileStream))
            {
                var worksheet = package.Workbook.Worksheets[0];
                int rowCount = worksheet.Dimension?.Rows ?? 0;

                for (int row = 2; row <= rowCount; row++)
                {
                    var transactionNoValue = worksheet.Cells[row, 1].GetValue<string>();

                    if (string.IsNullOrWhiteSpace(transactionNoValue))
                        continue;

                    totalRows++;
                    string transactionNo = transactionNoValue.Trim();
                    DateTime transactionDate = DateTime.MinValue;
                    var dateCellValue = worksheet.Cells[row, 2].Value;
                    string? dateString = worksheet.Cells[row, 2].GetValue<string>();

                    if (dateCellValue is DateTime dt)
                    {
                        transactionDate = DateTime.SpecifyKind(dt, DateTimeKind.Utc);
                    }
                    else if (dateCellValue is double excelDate)
                    {
                        var parsedDate = DateTime.FromOADate(excelDate);
                        transactionDate = DateTime.SpecifyKind(parsedDate, DateTimeKind.Utc);
                    }
                    else if (!string.IsNullOrWhiteSpace(dateString))
                    {
                        if (DateTime.TryParseExact(dateString, "dd/MM/yyyy", CultureInfo.InvariantCulture, DateTimeStyles.None, out var parsed))
                        {
                            transactionDate = DateTime.SpecifyKind(parsed, DateTimeKind.Utc);
                        }
                        else
                        {
                            errors.Add(new ImportValidationError { Row = row, Field = "Tanggal Transaksi", Message = "Format tanggal harus dd/MM/yyyy" });
                            continue;
                        }
                    }
                    else
                    {
                        errors.Add(new ImportValidationError { Row = row, Field = "Tanggal Transaksi", Message = "Tanggal Transaksi wajib diisi" });
                        continue;
                    }

                    var akunValue = worksheet.Cells[row, 3].GetValue<string>();
                    if (string.IsNullOrWhiteSpace(akunValue))
                    {
                        errors.Add(new ImportValidationError { Row = row, Field = "Akun", Message = "Akun wajib diisi" });
                        continue;
                    }

                    string coaCode;
                    int separatorIndex = akunValue.IndexOf(" - ");
                    if (separatorIndex > 0)
                    {
                        coaCode = akunValue.Substring(0, separatorIndex).Trim();
                    }
                    else
                    {
                        coaCode = akunValue.Trim();
                    }

                    string? lineDescription = worksheet.Cells[row, 4].GetValue<string>();
                    if (string.IsNullOrWhiteSpace(lineDescription))
                    {
                        errors.Add(new ImportValidationError { Row = row, Field = "Keterangan", Message = "Keterangan wajib diisi" });
                        continue;
                    }

                    double debit = worksheet.Cells[row, 5].GetValue<double>();
                    double credit = worksheet.Cells[row, 6].GetValue<double>();

                    if (!journalGroups.ContainsKey(transactionNo))
                    {
                        journalGroups[transactionNo] = new List<ImportJournalLine>();
                    }

                    journalGroups[transactionNo].Add(new ImportJournalLine
                    {
                        Row = row,
                        TransactionNo = transactionNo,
                        TransactionDate = transactionDate,
                        CoaCode = coaCode,
                        Description = lineDescription,
                        Debit = debit,
                        Credit = credit
                    });
                }
            }

            // Resolve every referenced COA and transaction no in two queries rather than one per
            // line -- a large file previously issued thousands of round trips, and the same COA
            // lookups were repeated again in the insert loop below.
            var coaLookup = await LoadCoaLookup(journalGroups, branchId);
            var existingTransactionNos = await LoadExistingTransactionNos(journalGroups.Keys, branchId);

            foreach (var group in journalGroups)
            {
                string transactionNo = group.Key;
                var lines = group.Value;

                if (lines.Count < 2)
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "Detail", Message = $"No Transaksi '{transactionNo}' harus memiliki minimal 2 baris akun" });
                }

                double totalDebit = lines.Sum(l => l.Debit);
                double totalCredit = lines.Sum(l => l.Credit);
                if (Math.Abs(totalDebit - totalCredit) > 0.01)
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "Total", Message = $"No Transaksi '{transactionNo}': Total Debit ({totalDebit:N2}) harus sama dengan Total Kredit ({totalCredit:N2})" });
                }

                if (existingTransactionNos.Contains(transactionNo))
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "No Transaksi", Message = $"No Transaksi '{transactionNo}' sudah ada di database" });
                }

                // Lock tutup buku: tolak baris impor yang jatuh di periode tertutup.
                if (await IsPeriodClosedRaw(lines.First().TransactionDate, branchId))
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "Tanggal Transaksi", Message = $"No Transaksi '{transactionNo}': Periode {lines.First().TransactionDate:MM/yyyy} sudah ditutup buku" });
                }

                foreach (var line in lines)
                {
                    if (!coaLookup.ContainsKey(line.CoaCode))
                    {
                        errors.Add(new ImportValidationError { Row = line.Row, Field = "Akun", Message = $"Kode akun '{line.CoaCode}' tidak ditemukan" });
                    }
                }
            }

            if (errors.Count > 0)
            {
                return new ImportResult
                {
                    Success = false,
                    Message = "Validasi gagal",
                    Errors = errors,
                    TotalRows = totalRows
                };
            }

            var createdTransactions = new List<object>();
            using (var transaction = _context.Database.BeginTransaction())
            {
                try
                {
                    foreach (var group in journalGroups)
                    {
                        string transactionNo = group.Key;
                        var lines = group.Value;
                        var firstLine = lines.First();

                        double totalDebit = lines.Sum(l => l.Debit);
                        string journalNo = await GenerateJournalNo(firstLine.TransactionDate);

                        var trx = new Transaction()
                        {
                            TransactionId = Guid.NewGuid().ToString(),
                            TransactionDate = firstLine.TransactionDate,
                            TransactionName = firstLine.Description,
                            TransactionType = JournalSource.JournalEntry.ToString(),
                            TransactionNo = transactionNo,
                            JournalNo = journalNo,
                            Description = firstLine.Description,
                            status = importStatus,
                            BranchId = branchId,
                            CreatedBy = userId,
                            CreatedDate = DateTime.UtcNow,
                            IsAutoId = false,
                            Amount = totalDebit
                        };

                        await _transactionService.Add(trx);

                        foreach (var line in lines)
                        {
                            var coa = coaLookup[line.CoaCode];

                            var journal = new Journal()
                            {
                                JournalDesc = line.Description,
                                CoaCode = line.CoaCode,
                                CoaName = coa.CoaName,
                                CoaParentCode = coa.ParentCode,
                                CoaParentName = coa.ParentName,
                                TransactionId = trx.TransactionId,
                                Source = JournalSource.JournalEntry.ToString(),
                                status = importStatus,
                                BranchId = branchId,
                                CreatedBy = userId,
                                CreatedDate = trx.CreatedDate,
                                IsPremier = false,
                                DebitAmount = line.Debit,
                                CreditAmount = line.Credit
                            };

                            await Add(journal);
                        }

                        await _auditTrailUtils.Add("Transaction", "Import JournalEntry", trx.TransactionId, null, new { TransactionNo = transactionNo, Lines = lines.Count }, userId, trx.status, "Import", branchId);

                        createdTransactions.Add(new { trx.TransactionId, trx.TransactionNo });
                    }

                    transaction.Commit();

                    return new ImportResult
                    {
                        Success = true,
                        Message = $"Import berhasil: {createdTransactions.Count} jurnal dibuat",
                        CreatedTransactions = createdTransactions,
                        TotalRows = totalRows
                    };
                }
                catch (Exception)
                {
                    transaction.Rollback();
                    throw;
                }
            }
        }

        /// <summary>
        /// One query for every COA code referenced by the file, keyed by code.
        /// Replaces a per-line GetByCode that ran once during validation and again on insert.
        /// </summary>
        private async Task<Dictionary<string, Coa>> LoadCoaLookup(
            Dictionary<string, List<ImportJournalLine>> journalGroups, int branchId)
        {
            var codes = journalGroups.Values
                .SelectMany(lines => lines)
                .Select(l => l.CoaCode)
                .Where(c => !string.IsNullOrWhiteSpace(c))
                .Distinct()
                .ToList();

            if (codes.Count == 0) return new Dictionary<string, Coa>();

            var rows = await _context.Coa
                .AsNoTracking()
                .Where(c => codes.Contains(c.CoaCode) && c.BranchId == branchId)
                .ToListAsync();

            // A code can legitimately appear more than once across branches; the filter above
            // already pins the branch, so first-wins is safe here.
            return rows
                .GroupBy(c => c.CoaCode)
                .ToDictionary(g => g.Key, g => g.First());
        }

        /// <summary>
        /// One query for the transaction numbers already in the database, replacing a
        /// CheckIfExistTransactionNo call per group.
        /// </summary>
        private async Task<HashSet<string>> LoadExistingTransactionNos(
            IEnumerable<string> transactionNos, int branchId)
        {
            var wanted = transactionNos.Distinct().ToList();
            if (wanted.Count == 0) return new HashSet<string>();

            var source = JournalSource.JournalEntry.ToString();
            var found = await _context.Transactions
                .AsNoTracking()
                .Where(t => t.TransactionNo != null
                            && wanted.Contains(t.TransactionNo)
                            && t.TransactionType == source
                            && t.BranchId == branchId)
                .Select(t => t.TransactionNo!)
                .ToListAsync();

            return new HashSet<string>(found);
        }

        public async Task<ImportResult> ImportGeneralJournalTHB(FileUpload fileUpload, int branchId, int userId)
        {
            if (fileUpload?.File == null || fileUpload.File.Length == 0)
            {
                return new ImportResult { Success = false, Message = "No file uploaded." };
            }

            using var stream = fileUpload.File.OpenReadStream();
            return await ImportGeneralJournalThbStream(stream, branchId, userId);
        }

        /// <summary>
        /// Core of the THB import, reading straight from a stream so both the synchronous
        /// endpoint and the background queue worker share one implementation.
        ///
        /// Parsing does not touch the database. Supplier / asuransi / cost center rows missing
        /// from master are still created automatically, but that now happens inside the import
        /// transaction (see ResolveThbMasterData) -- previously they were saved during parsing,
        /// so a file rejected by validation still left new master records behind for good.
        /// </summary>
        public async Task<ImportResult> ImportGeneralJournalThbStream(Stream fileStream, int branchId, int userId)
        {
            var errors = new List<ImportValidationError>();
            var journalGroups = new Dictionary<string, List<ImportJournalLine>>();
            int totalRows = 0;

            // Get default status from options
            var defaultStatusOption = await _optionService.GetByName("DEFAULT_JOURNAL_IMPORT_STATUS", branchId);
            string importStatus = defaultStatusOption?.OptionValue ?? Status.Pending.ToString();

            using (var package = new ExcelPackage(fileStream))
            {
                var worksheet = package.Workbook.Worksheets[0];
                int rowCount = worksheet.Dimension?.Rows ?? 0;

                // Auto-detect header row
                int dataStartRow = 2;
                string[] knownHeaders = { "Tanggal", "COA", "Debit", "Kredit", "Voucher" };
                bool row1IsHeader = false;
                bool row2IsHeader = false;

                for (int col = 1; col <= 18; col++)
                {
                    var cellVal = worksheet.Cells[1, col].GetValue<string>();
                    if (!string.IsNullOrWhiteSpace(cellVal) && knownHeaders.Any(h => cellVal.IndexOf(h, StringComparison.OrdinalIgnoreCase) >= 0))
                    {
                        row1IsHeader = true;
                        break;
                    }
                }

                if (!row1IsHeader)
                {
                    for (int col = 1; col <= 18; col++)
                    {
                        var cellVal = worksheet.Cells[2, col].GetValue<string>();
                        if (!string.IsNullOrWhiteSpace(cellVal) && knownHeaders.Any(h => cellVal.IndexOf(h, StringComparison.OrdinalIgnoreCase) >= 0))
                        {
                            row2IsHeader = true;
                            break;
                        }
                    }
                }

                if (row2IsHeader)
                {
                    dataStartRow = 3;
                }

                // THB Column mapping (1-indexed):
                // A-E(1-5) = Transaction No (concatenated)
                // F(6) = Tanggal Transaksi
                // H(8) = Kode COA Source (lookup mst_coa_mapping -> mst_coa)
                // I(9) = Nama COA
                // J(10) = Voucher -> vourcher_id
                // K(11) = Nama Supplier (lookup/create mst_contact category 1)
                // L(12) = Asuransi (lookup/create mst_contact category 5)
                // M(13) = PO -> thb_po_number
                // O(15) = Profit Cost Center (lookup/create mst_cost_center)
                // P(16) = Debit
                // Q(17) = Kredit
                // R(18) = Keterangan
                const int colTanggal = 6;
                const int colCoaSource = 8;
                const int colCoaName = 9;
                const int colVoucher = 10;
                const int colSupplier = 11;
                const int colAsuransi = 12;
                const int colPO = 13;
                const int colCostCenter = 15;
                const int colDebit = 16;
                const int colKredit = 17;
                const int colKeterangan = 18;

                for (int row = dataStartRow; row <= rowCount; row++)
                {
                    // Transaction No: concatenate columns A-E
                    var parts = new List<string>();
                    for (int col = 1; col <= 5; col++)
                    {
                        var val = worksheet.Cells[row, col].GetValue<string>();
                        if (!string.IsNullOrWhiteSpace(val))
                            parts.Add(val.Trim());
                    }
                    string transactionNo = string.Join("-", parts);

                    if (string.IsNullOrWhiteSpace(transactionNo))
                        continue;

                    // Parse date from column F
                    DateTime transactionDate = DateTime.MinValue;
                    var dateCellValue = worksheet.Cells[row, colTanggal].Value;
                    string? dateString = worksheet.Cells[row, colTanggal].GetValue<string>();

                    if (dateCellValue is DateTime dt)
                    {
                        transactionDate = DateTime.SpecifyKind(dt, DateTimeKind.Utc);
                    }
                    else if (dateCellValue is double excelDate)
                    {
                        var parsedDate = DateTime.FromOADate(excelDate);
                        transactionDate = DateTime.SpecifyKind(parsedDate, DateTimeKind.Utc);
                    }
                    else if (!string.IsNullOrWhiteSpace(dateString))
                    {
                        string[] dateFormats = { "dd/MM/yyyy", "d/M/yyyy", "dd-MM-yyyy", "d-M-yyyy", "yyyy-MM-dd" };
                        bool dateParsed = false;
                        foreach (var fmt in dateFormats)
                        {
                            if (DateTime.TryParseExact(dateString, fmt, CultureInfo.InvariantCulture, DateTimeStyles.None, out var parsed))
                            {
                                transactionDate = DateTime.SpecifyKind(parsed, DateTimeKind.Utc);
                                dateParsed = true;
                                break;
                            }
                        }
                        if (!dateParsed)
                        {
                            errors.Add(new ImportValidationError { Row = row, Field = "Tanggal", Message = "Format tanggal tidak dikenali" });
                            continue;
                        }
                    }
                    else
                    {
                        errors.Add(new ImportValidationError { Row = row, Field = "Tanggal", Message = "Tanggal wajib diisi" });
                        continue;
                    }

                    // COA Source from column H - lookup via mst_coa_mapping then mst_coa
                    var coaSourceValue = worksheet.Cells[row, colCoaSource].GetValue<string>();
                    if (string.IsNullOrWhiteSpace(coaSourceValue))
                    {
                        errors.Add(new ImportValidationError { Row = row, Field = "Kode COA", Message = "Kode COA wajib diisi" });
                        continue;
                    }
                    string coaSourceCode = coaSourceValue.Trim();

                    // Lookup COA mapping: source -> dest
                    var coaMapping = await _coaService.GetMappingByCode(coaSourceCode);
                    string resolvedCoaCode = coaMapping != null ? coaMapping.CoaDest ?? "0" : coaSourceCode;

                    // Voucher from column J
                    string? voucherId = worksheet.Cells[row, colVoucher].GetValue<string>()?.Trim();

                    // Supplier (K), Asuransi (L) and Cost Center (O) are only read here. Rows
                    // missing from master are created later, inside the import transaction, so
                    // that a file rejected by validation leaves master data untouched.
                    string? supplierName = worksheet.Cells[row, colSupplier].GetValue<string>()?.Trim();
                    string? asuransiName = worksheet.Cells[row, colAsuransi].GetValue<string>()?.Trim();

                    // PO from column M
                    string? poNumber = worksheet.Cells[row, colPO].GetValue<string>()?.Trim();

                    // Cost Center from column O
                    string? costCenterName = worksheet.Cells[row, colCostCenter].GetValue<string>()?.Trim();

                    // Description from column R
                    string? keterangan = worksheet.Cells[row, colKeterangan].GetValue<string>();
                    string? coaNameValue = worksheet.Cells[row, colCoaName].GetValue<string>();
                    string lineDescription = !string.IsNullOrWhiteSpace(keterangan) ? keterangan.Trim() : (coaNameValue?.Trim() ?? "");

                    double debit = worksheet.Cells[row, colDebit].GetValue<double>();
                    double credit = worksheet.Cells[row, colKredit].GetValue<double>();

                    if (!journalGroups.ContainsKey(transactionNo))
                    {
                        journalGroups[transactionNo] = new List<ImportJournalLine>();
                    }

                    totalRows++;
                    journalGroups[transactionNo].Add(new ImportJournalLine
                    {
                        Row = row,
                        TransactionNo = transactionNo,
                        TransactionDate = transactionDate,
                        CoaCode = resolvedCoaCode,
                        Description = lineDescription,
                        Debit = debit,
                        Credit = credit,
                        JournalRef = coaSourceCode,
                        VourcherId = voucherId,
                        SupplierName = supplierName,
                        // Column L feeds journal.payer_name directly; the matching contact row is
                        // ensured in the transaction below.
                        PayerName = string.IsNullOrWhiteSpace(asuransiName) ? null : asuransiName,
                        PONumber = poNumber,
                        CostCenterName = costCenterName
                        // CostcenterId / ContactId are filled in by ResolveThbMasterData.
                    });
                }
            }

            // Two queries instead of one per line / per group.
            var coaLookup = await LoadCoaLookup(journalGroups, branchId);
            var existingTransactionNos = await LoadExistingTransactionNos(journalGroups.Keys, branchId);

            // Validation. COA is the only master data that must already exist -- supplier,
            // asuransi and cost center are created on demand further down.
            foreach (var group in journalGroups)
            {
                string transactionNo = group.Key;
                var lines = group.Value;

                if (lines.Count < 2)
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "Detail", Message = $"No Transaksi '{transactionNo}' harus memiliki minimal 2 baris akun" });
                }

                double totalDebit = lines.Sum(l => l.Debit);
                double totalCredit = lines.Sum(l => l.Credit);
                if (Math.Abs(totalDebit - totalCredit) > 0.01)
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "Total", Message = $"No Transaksi '{transactionNo}': Total Debit ({totalDebit:N2}) harus sama dengan Total Kredit ({totalCredit:N2})" });
                }

                if (existingTransactionNos.Contains(transactionNo))
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "No Transaksi", Message = $"No Transaksi '{transactionNo}' sudah ada di database" });
                }

                // Lock tutup buku: tolak baris impor THB yang jatuh di periode tertutup.
                if (await IsPeriodClosedRaw(lines.First().TransactionDate, branchId))
                {
                    errors.Add(new ImportValidationError { Row = lines.First().Row, Field = "Tanggal Transaksi", Message = $"No Transaksi '{transactionNo}': Periode {lines.First().TransactionDate:MM/yyyy} sudah ditutup buku" });
                }

                foreach (var line in lines)
                {
                    if (!coaLookup.ContainsKey(line.CoaCode))
                    {
                        errors.Add(new ImportValidationError { Row = line.Row, Field = "COA Code", Message = $"Kode akun '{line.CoaCode}' tidak ditemukan (source: {line.JournalRef})" });
                    }
                }
            }

            if (errors.Count > 0)
            {
                return new ImportResult
                {
                    Success = false,
                    Message = "Validasi gagal",
                    Errors = errors,
                    TotalRows = totalRows
                };
            }

            // Create transactions
            var createdTransactions = new List<object>();
            using (var transaction = _context.Database.BeginTransaction())
            {
                try
                {
                    // Inside the transaction on purpose: any master data created here rolls back
                    // with the import if a later step throws.
                    await ResolveThbMasterData(journalGroups, branchId);

                    foreach (var group in journalGroups)
                    {
                        string transactionNo = group.Key;
                        var lines = group.Value;
                        var firstLine = lines.First();

                        double totalDebit = lines.Sum(l => l.Debit);
                        string journalNo = await GenerateJournalNo(firstLine.TransactionDate);

                        var trx = new Transaction()
                        {
                            TransactionId = Guid.NewGuid().ToString(),
                            TransactionDate = firstLine.TransactionDate,
                            TransactionName = firstLine.Description,
                            TransactionType = JournalSource.JournalEntry.ToString(),
                            TransactionNo = transactionNo,
                            JournalNo = journalNo,
                            Description = firstLine.Description,
                            status = importStatus,
                            BranchId = branchId,
                            CreatedBy = userId,
                            CreatedDate = DateTime.UtcNow,
                            IsAutoId = false,
                            Amount = totalDebit
                        };

                        await _transactionService.Add(trx);

                        foreach (var line in lines)
                        {
                            var coa = coaLookup[line.CoaCode];

                            var journal = new Journal()
                            {
                                JournalDesc = line.Description,
                                CoaCode = line.CoaCode,
                                CoaName = coa?.CoaName,
                                CoaParentCode = coa?.ParentCode,
                                CoaParentName = coa?.ParentName,
                                TransactionId = trx.TransactionId,
                                Source = JournalSource.JournalEntry.ToString(),
                                status = importStatus,
                                BranchId = branchId,
                                CreatedBy = userId,
                                CreatedDate = trx.CreatedDate,
                                IsPremier = false,
                                DebitAmount = line.Debit,
                                CreditAmount = line.Credit,
                                JournalRef = line.JournalRef,
                                VourcherId = line.VourcherId,
                                SupplierName = line.SupplierName,
                                PayerName = line.PayerName,
                                PONumber = line.PONumber,
                                CostcenterId = line.CostcenterId
                            };

                            await Add(journal);
                        }

                        await _auditTrailUtils.Add("Transaction", "Import JournalEntry THB", trx.TransactionId, null, new { TransactionNo = transactionNo, Lines = lines.Count }, userId, trx.status, "Import", branchId);

                        createdTransactions.Add(new { trx.TransactionId, trx.TransactionNo });
                    }

                    transaction.Commit();

                    return new ImportResult
                    {
                        Success = true,
                        Message = $"Import berhasil: {createdTransactions.Count} jurnal dibuat",
                        CreatedTransactions = createdTransactions,
                        TotalRows = totalRows
                    };
                }
                catch (Exception)
                {
                    transaction.Rollback();
                    throw;
                }
            }
        }

        /// <summary>
        /// Fills in ContactId / CostcenterId on every THB line, creating the supplier, asuransi
        /// and cost center master rows that do not exist yet.
        ///
        /// Call this INSIDE the import transaction. Master data used to be created during
        /// parsing with its own SaveChangesAsync, which meant a file rejected by validation
        /// still left the new contacts and cost centers behind permanently.
        ///
        /// Existing rows are read once into dictionaries -- the old per-row lookups used
        /// LOWER(contact_name), which cannot use an index and ran once per line.
        /// </summary>
        private async Task ResolveThbMasterData(
            Dictionary<string, List<ImportJournalLine>> journalGroups, int branchId)
        {
            var allLines = journalGroups.Values.SelectMany(l => l).ToList();

            var supplierNames = DistinctNames(allLines.Select(l => l.SupplierName));
            var payerNames = DistinctNames(allLines.Select(l => l.PayerName));
            var costCenterNames = DistinctNames(allLines.Select(l => l.CostCenterName));

            var suppliers = await LoadContactLookup(supplierNames, Constants.CONTACT_CATEGORY_SUPPLIER);
            var payers = await LoadContactLookup(payerNames, Constants.CONTACT_CATEGORY_PAYER);

            // Cost centers are branch-scoped: the same name may legitimately exist per branch.
            // Contacts are not -- mst_contact has no branch column, they are global master data.
            var costCenters = (await _context.CostCenters
                    .Where(c => c.BranchId == branchId && c.CostCenterName != null)
                    .ToListAsync())
                .GroupBy(c => c.CostCenterName!.Trim().ToLowerInvariant())
                .ToDictionary(g => g.Key, g => g.First());

            foreach (var name in supplierNames)
            {
                if (suppliers.ContainsKey(Key(name))) continue;
                var contact = new Contact
                {
                    ContactName = name,
                    ContactCategoryId = Constants.CONTACT_CATEGORY_SUPPLIER,
                    RefCode = await GenerateContactRefCode(ContactRefCodePrefixSupplier),
                    Status = "Active"
                };
                _context.Contacts.Add(contact);
                await _context.SaveChangesAsync();
                suppliers[Key(name)] = contact;
            }

            foreach (var name in payerNames)
            {
                if (payers.ContainsKey(Key(name))) continue;
                var contact = new Contact
                {
                    ContactName = name,
                    ContactCategoryId = Constants.CONTACT_CATEGORY_PAYER,
                    RefCode = await GenerateContactRefCode(ContactRefCodePrefixPayer),
                    Status = "Active"
                };
                _context.Contacts.Add(contact);
                await _context.SaveChangesAsync();
                payers[Key(name)] = contact;
            }

            foreach (var name in costCenterNames)
            {
                if (costCenters.ContainsKey(Key(name))) continue;
                var costCenter = new CostCenter
                {
                    CostCenterCode = name.Replace(" ", "_").ToUpper(),
                    CostCenterName = name,
                    DepartmentId = 1,
                    BranchId = branchId
                };
                _context.CostCenters.Add(costCenter);
                await _context.SaveChangesAsync();
                costCenters[Key(name)] = costCenter;
            }

            foreach (var line in allLines)
            {
                if (!string.IsNullOrWhiteSpace(line.SupplierName) &&
                    suppliers.TryGetValue(Key(line.SupplierName), out var supplier))
                {
                    line.ContactId = supplier.ContactId;
                }

                if (!string.IsNullOrWhiteSpace(line.CostCenterName) &&
                    costCenters.TryGetValue(Key(line.CostCenterName), out var cc))
                {
                    line.CostcenterId = cc.CostcenterId;
                }
            }
        }

        private const string ContactRefCodePrefixSupplier = "SUP";
        private const string ContactRefCodePrefixPayer = "ASR";

        /// <summary>
        /// Next free ref_code for an auto-created contact, e.g. SUP-000001.
        ///
        /// ref_code is expected to be unique (ContactService.Add:181 rejects duplicates) but the
        /// import used to leave it null entirely. The prefix keeps these clear of Khanza-issued
        /// codes, which use a different shape. The sequence is derived from the highest existing
        /// code with the same prefix; only one import runs at a time
        /// ([DisallowConcurrentExecution] on JournalImportWorker), so no two can pick the same
        /// number. Migrations/20260718_unique_contact_ref_code.sql backs this with a unique index.
        /// </summary>
        private async Task<string> GenerateContactRefCode(string prefix)
        {
            string pattern = prefix + "-";

            var codes = await _context.Contacts
                .Where(c => c.RefCode != null && c.RefCode.StartsWith(pattern))
                .Select(c => c.RefCode!)
                .ToListAsync();

            int next = 1;
            foreach (var code in codes)
            {
                var suffix = code.Substring(pattern.Length);
                if (int.TryParse(suffix, out int seq) && seq >= next)
                    next = seq + 1;
            }

            return $"{pattern}{next:D6}";
        }

        private async Task<Dictionary<string, Contact>> LoadContactLookup(List<string> names, int categoryId)
        {
            if (names.Count == 0) return new Dictionary<string, Contact>();

            var rows = await _context.Contacts
                .Where(c => c.ContactCategoryId == categoryId && c.ContactName != null)
                .ToListAsync();

            return rows
                .GroupBy(c => c.ContactName.Trim().ToLowerInvariant())
                .ToDictionary(g => g.Key, g => g.First());
        }

        private static List<string> DistinctNames(IEnumerable<string?> values) =>
            values
                .Where(v => !string.IsNullOrWhiteSpace(v))
                .Select(v => v!.Trim())
                .GroupBy(v => v.ToLowerInvariant())
                .Select(g => g.First())
                .ToList();

        private static string Key(string? name) => (name ?? string.Empty).Trim().ToLowerInvariant();
    }
}