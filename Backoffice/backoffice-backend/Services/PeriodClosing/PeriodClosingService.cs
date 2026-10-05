using Microsoft.EntityFrameworkCore;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using TDPBackend.Helpers;

namespace RSSMBackend.Services
{
    public class PeriodClosingService : IPeriodClosingService
    {
        public const string StatusClosed = "Closed";
        public const string StatusReopened = "Reopened";
        public const string PeriodMonthly = "Monthly";
        public const string PeriodYearly = "Yearly";
        public const string ClosingEntrySource = "ClosingEntry";
        private const string Module = AuditModule.PeriodClosing;

        private readonly RSSMDbContext _context;
        private readonly AuditTrailUtils _auditTrailUtils;
        private readonly IJournalService _journalService;
        private readonly ITransactionService _transactionService;
        private readonly IOptionService _optionService;

        public PeriodClosingService(RSSMDbContext context, AuditTrailUtils auditTrailUtils,
            IJournalService journalService, ITransactionService transactionService, IOptionService optionService)
        {
            _context = context;
            _auditTrailUtils = auditTrailUtils;
            _journalService = journalService;
            _transactionService = transactionService;
            _optionService = optionService;
        }

        public async Task<PagedResult<PeriodClosing>> GetAll(string? periodType, int? year, string? status, int? branchId, int page, int pageSize)
        {
            var query = _context.PeriodClosings.AsNoTracking().AsQueryable();
            if (!string.IsNullOrEmpty(periodType)) query = query.Where(p => p.PeriodType == periodType);
            if (year != null) query = query.Where(p => p.Year == year);
            if (!string.IsNullOrEmpty(status)) query = query.Where(p => p.Status == status);
            if (branchId != null) query = query.Where(p => p.BranchId == branchId || p.BranchId == null);

            int total = await query.CountAsync();

            var listQuery = query.OrderByDescending(p => p.Year).ThenByDescending(p => p.Month).ThenByDescending(p => p.Id);
            var rows = page != -1
                ? await listQuery.Skip((page - 1) * pageSize).Take(pageSize).ToListAsync()
                : await listQuery.ToListAsync();

            await ResolveNames(rows);
            return new PagedResult<PeriodClosing>(rows, total);
        }

        public async Task<PeriodClosing?> GetById(int id)
        {
            var row = await _context.PeriodClosings.AsNoTracking().FirstOrDefaultAsync(p => p.Id == id);
            if (row == null) return null;
            await ResolveNames(new List<PeriodClosing> { row });
            return row;
        }

        public async Task<PeriodClosing> ClosePeriod(PeriodClosingRequest req, int? branchId, int userId, string ipAddress)
        {
            string type = req.PeriodType == PeriodYearly ? PeriodYearly : PeriodMonthly;
            if (type == PeriodMonthly && (req.Month == null || req.Month < 1 || req.Month > 12))
                throw new ApplicationException("Bulan wajib 1-12 untuk tutup buku bulanan");
            int? month = type == PeriodYearly ? null : req.Month;
            int? branch = req.AllBranch ? (int?)null : branchId;

            var existing = await _context.PeriodClosings.FirstOrDefaultAsync(p =>
                p.PeriodType == type && p.Year == req.Year && p.Month == month && p.BranchId == branch);
            if (existing != null && existing.Status == StatusClosed)
                throw new ApplicationException("Periode ini sudah ditutup buku");

            // 1) [DINONAKTIFKAN] Penulisan checkpoint saldo ke trx_balance.
            // Kebijakan: trx_balance hanya diisi manual dari halaman Saldo Awal. Tutup buku
            // TIDAK lagi menulis checkpoint bulanan; nilainya = kunci periode + audit (+ opsi
            // jurnal penutup). Saldo tetap benar via on-read (opening + Σ jurnal Published).
            // var months = type == PeriodYearly ? Enumerable.Range(1, 12).ToList() : new List<int> { month!.Value };
            // foreach (var m in months)
            //     await _journalService.GenerateMonthlyCheckpoint(req.Year, m, branch);

            // 2) Jurnal penutup (opsional).
            string? closingTrxId = null;
            if (req.GenerateClosingEntries)
                closingTrxId = await GenerateClosingEntry(type, req.Year, month, branch, userId);

            var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
            object? before = existing != null ? Snapshot(existing) : null;

            if (existing == null)
            {
                existing = new PeriodClosing
                {
                    PeriodType = type,
                    Year = req.Year,
                    Month = month,
                    BranchId = branch,
                };
                await _context.PeriodClosings.AddAsync(existing);
            }

            existing.Status = StatusClosed;
            existing.GenerateClosingEntries = req.GenerateClosingEntries;
            existing.ClosingTransactionId = closingTrxId ?? existing.ClosingTransactionId;
            existing.Note = req.Note;
            existing.ClosedBy = userId;
            existing.ClosedDate = now;
            existing.ReopenedBy = null;
            existing.ReopenedDate = null;

            await _context.SaveChangesAsync();

            string refId = RefId(existing);
            await _auditTrailUtils.Add(Module, "Close", refId, before, Snapshot(existing), userId, StatusClosed, ipAddress, branch ?? 0);

            return (await GetById(existing.Id))!;
        }

        public async Task<PeriodClosing> ReopenPeriod(int id, string? note, int userId, string ipAddress)
        {
            var row = await _context.PeriodClosings.FirstOrDefaultAsync(p => p.Id == id);
            if (row == null) throw new ApplicationException("Data tutup buku tidak ditemukan");
            if (row.Status != StatusClosed) throw new ApplicationException("Periode belum ditutup / sudah dibuka");

            var before = Snapshot(row);
            var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

            // Void jurnal penutup (bila ada) supaya saldo kembali.
            if (!string.IsNullOrEmpty(row.ClosingTransactionId))
            {
                var closingJournals = await _context.Journals.Where(j => j.TransactionId == row.ClosingTransactionId).ToListAsync();
                foreach (var j in closingJournals) { j.status = Status.Void.ToString(); j.UpdatedBy = userId; j.UpdatedDate = now; }
                var trx = await _context.Transactions.FirstOrDefaultAsync(t => t.TransactionId == row.ClosingTransactionId);
                if (trx != null) { trx.status = Status.Void.ToString(); trx.UpdatedBy = userId; trx.UpdatedDate = now; }
                await _context.SaveChangesAsync();

                // [DINONAKTIFKAN] Recompute checkpoint. trx_balance tidak lagi ditulis otomatis;
                // saldo dihitung on-read dari jurnal Published, jadi void jurnal penutup sudah cukup.
            }

            row.Status = StatusReopened;
            row.ReopenedBy = userId;
            row.ReopenedDate = now;
            if (!string.IsNullOrWhiteSpace(note)) row.Note = note;

            await _context.SaveChangesAsync();

            await _auditTrailUtils.Add(Module, "Reopen", RefId(row), before, Snapshot(row), userId, StatusReopened, ipAddress, row.BranchId ?? 0);
            return (await GetById(row.Id))!;
        }

        public async Task<bool> IsPeriodClosed(DateTime transactionDate, int? branchId)
        {
            var d = transactionDate;
            return await _context.PeriodClosings.AnyAsync(p => p.Status == StatusClosed
                && p.Year == d.Year
                && (p.Month == null || p.Month == d.Month)
                && (p.BranchId == null || p.BranchId == branchId));
        }

        public async Task EnsureNotClosed(DateTime? transactionDate, int? branchId)
        {
            if (transactionDate == null) return;
            if (await IsPeriodClosed(transactionDate.Value, branchId))
                throw new ApplicationException($"Periode {transactionDate.Value:MM/yyyy} sudah ditutup buku. Buka kembali (reopen) periode untuk mengubah jurnal.");
        }

        // ------------------------------------------------------------------
        // Jurnal penutup: menolkan akun pendapatan/beban periode & memindahkan
        // laba/rugi bersih ke akun Laba Ditahan (COA_RETAINED_EARNINGS).
        // Balanced by construction. Return transaction_id (null bila tak ada yg ditutup).
        // ------------------------------------------------------------------
        private async Task<string?> GenerateClosingEntry(string type, int year, int? month, int? branchId, int userId)
        {
            int cfgBranch = branchId ?? 1;
            var reOpt = await _optionService.GetByName("COA_RETAINED_EARNINGS", cfgBranch);
            if (reOpt == null || string.IsNullOrWhiteSpace(reOpt.OptionValue))
                throw new ApplicationException("COA_RETAINED_EARNINGS belum dikonfigurasi. Set kode COA Laba Ditahan sebelum membuat jurnal penutup.");
            string retainedCoa = reOpt.OptionValue.Trim();

            DateTime start = type == PeriodYearly
                ? new DateTime(year, 1, 1, 0, 0, 0, DateTimeKind.Utc)
                : new DateTime(year, month!.Value, 1, 0, 0, 0, DateTimeKind.Utc);
            DateTime end = type == PeriodYearly
                ? start.AddYears(1).AddTicks(-1)
                : start.AddMonths(1).AddTicks(-1);

            var (revenuePrefixes, expensePrefixes) = await LoadPlPrefixes(cfgBranch);

            // Ambil akun L/R pada branch (kalau branch null, semua).
            var coaQuery = _context.Coa.AsNoTracking().Where(c => c.CoaCode != null);
            if (branchId != null) coaQuery = coaQuery.Where(c => c.BranchId == branchId);
            var coas = await coaQuery.Select(c => new { c.CoaCode, c.CoaName, c.ParentCode, c.ParentName, c.NormalPosition, c.BranchId }).ToListAsync();

            var revenueCoas = coas.Where(c => revenuePrefixes.Any(p => c.CoaCode!.StartsWith(p))).Select(c => c.CoaCode).ToHashSet();
            var expenseCoas = coas.Where(c => expensePrefixes.Any(p => c.CoaCode!.StartsWith(p))).Select(c => c.CoaCode).ToHashSet();

            // Mutasi Published per akun L/R dalam periode.
            var jq = _context.Journals.Include(j => j.Transaction)
                .Where(j => j.status == Status.Published.ToString()
                    && j.CoaCode != null
                    && j.Transaction.TransactionDate >= start && j.Transaction.TransactionDate <= end
                    && (revenueCoas.Contains(j.CoaCode) || expenseCoas.Contains(j.CoaCode)));
            if (branchId != null) jq = jq.Where(j => j.BranchId == branchId);

            var perCoa = await jq
                .GroupBy(j => j.CoaCode)
                .Select(g => new { CoaCode = g.Key, Debit = g.Sum(x => x.DebitAmount ?? 0), Credit = g.Sum(x => x.CreditAmount ?? 0) })
                .ToListAsync();

            var lines = new List<(string CoaCode, double Debit, double Credit)>();
            double totalRevenue = 0, totalExpense = 0;
            foreach (var p in perCoa)
            {
                if (p.CoaCode == null) continue;
                if (revenueCoas.Contains(p.CoaCode))
                {
                    double net = p.Credit - p.Debit;          // pendapatan (credit-normal)
                    if (Math.Abs(net) < 0.005) continue;
                    totalRevenue += net;
                    // menolkan: debit sebesar net (bila net>0) atau credit (bila net<0)
                    if (net >= 0) lines.Add((p.CoaCode, net, 0)); else lines.Add((p.CoaCode, 0, -net));
                }
                else if (expenseCoas.Contains(p.CoaCode))
                {
                    double net = p.Debit - p.Credit;          // beban (debit-normal)
                    if (Math.Abs(net) < 0.005) continue;
                    totalExpense += net;
                    if (net >= 0) lines.Add((p.CoaCode, 0, net)); else lines.Add((p.CoaCode, -net, 0));
                }
            }

            double netIncome = totalRevenue - totalExpense;
            if (lines.Count == 0 && Math.Abs(netIncome) < 0.005)
                return null; // tidak ada yang perlu ditutup

            // Baris penyeimbang ke Laba Ditahan.
            if (netIncome >= 0) lines.Add((retainedCoa, 0, netIncome)); else lines.Add((retainedCoa, -netIncome, 0));

            // Buat transaksi + jurnal penutup (Published).
            var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
            string periodLabel = type == PeriodYearly ? $"{year}" : $"{year}-{month:D2}";
            string transactionNo = $"CLOSE/{periodLabel}" + (branchId != null ? $"/B{branchId}" : "");
            string journalNo = await _journalService.GenerateJournalNo(end);

            var coaMap = coas.ToDictionary(c => c.CoaCode!, c => c);
            var trx = new Transaction
            {
                TransactionId = Guid.NewGuid().ToString(),
                TransactionDate = end,
                TransactionName = $"Jurnal Penutup {periodLabel}",
                TransactionType = JournalSource.JournalEntry.ToString(),
                TransactionNo = transactionNo,
                JournalNo = journalNo,
                Description = $"Jurnal penutup periode {periodLabel}",
                status = Status.Published.ToString(),
                BranchId = branchId,
                CreatedBy = userId,
                CreatedDate = now,
                PostedDate = now,
                IsAutoId = false,
                Amount = lines.Sum(l => l.Debit),
            };
            await _transactionService.Add(trx);

            foreach (var l in lines)
            {
                coaMap.TryGetValue(l.CoaCode, out var c);
                var journal = new Journal
                {
                    JournalDesc = $"Penutup {periodLabel}",
                    CoaCode = l.CoaCode,
                    CoaName = c?.CoaName,
                    CoaParentCode = c?.ParentCode,
                    CoaParentName = c?.ParentName,
                    TransactionId = trx.TransactionId,
                    Source = ClosingEntrySource,
                    status = Status.Published.ToString(),
                    BranchId = branchId,
                    CreatedBy = userId,
                    CreatedDate = now,
                    IsPremier = false,
                    DebitAmount = l.Debit,
                    CreditAmount = l.Credit,
                };
                await _journalService.Add(journal);
            }

            return trx.TransactionId;
        }

        private async Task<(List<string> revenue, List<string> expense)> LoadPlPrefixes(int branchId)
        {
            var revenue = new List<string>();
            var expense = new List<string>();

            var revOpt = await _optionService.GetByName("COA_PREFIX_REVENUE", branchId);
            revenue.Add(revOpt?.OptionValue ?? "4");
            var revAlt = await _optionService.GetByName("COA_PREFIX_REVENUE_ALT", branchId);
            if (!string.IsNullOrWhiteSpace(revAlt?.OptionValue))
                revenue.AddRange(revAlt.OptionValue.Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()));

            var expOpt = await _optionService.GetByName("COA_PREFIX_EXPENSE", branchId);
            expense.AddRange((expOpt?.OptionValue ?? "5,6").Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()));
            var expAlt = await _optionService.GetByName("COA_PREFIX_EXPENSE_ALT", branchId);
            if (!string.IsNullOrWhiteSpace(expAlt?.OptionValue))
                expense.AddRange(expAlt.OptionValue.Split(',', StringSplitOptions.RemoveEmptyEntries).Select(x => x.Trim()));

            return (revenue, expense);
        }

        private static string RefId(PeriodClosing p) => $"{p.PeriodType}|{p.Year}|{p.Month?.ToString() ?? "-"}|{p.BranchId?.ToString() ?? "ALL"}";

        private static object Snapshot(PeriodClosing p) => new
        {
            p.Id, p.PeriodType, p.Year, p.Month, p.BranchId, p.Status,
            p.GenerateClosingEntries, p.ClosingTransactionId, p.Note,
            p.ClosedBy, p.ClosedDate, p.ReopenedBy, p.ReopenedDate
        };

        // Resolusi nama user & branch via query batch di memori (dipanggil setelah baris ter-materialisasi).
        // Menghindari error EF: projeksi tidak boleh memanggil instance method yang menangkap _context.
        private async Task ResolveNames(List<PeriodClosing> rows)
        {
            if (rows.Count == 0) return;

            var userIds = rows.SelectMany(r => new[] { r.ClosedBy, r.ReopenedBy })
                .Where(x => x != null).Select(x => x!.Value).Distinct().ToList();
            var branchIds = rows.Where(r => r.BranchId != null).Select(r => r.BranchId!.Value).Distinct().ToList();

            var users = await _context.Users.AsNoTracking()
                .Where(u => userIds.Contains(u.UserID)).Select(u => new { u.UserID, u.DisplayName }).ToListAsync();
            var branches = await _context.Branches.AsNoTracking()
                .Where(b => branchIds.Contains(b.BranchId)).Select(b => new { b.BranchId, b.BranchName }).ToListAsync();

            foreach (var r in rows)
            {
                r.ClosedName = r.ClosedBy != null ? users.FirstOrDefault(u => u.UserID == r.ClosedBy)?.DisplayName : null;
                r.ReopenedName = r.ReopenedBy != null ? users.FirstOrDefault(u => u.UserID == r.ReopenedBy)?.DisplayName : null;
                r.BranchName = r.BranchId != null ? branches.FirstOrDefault(b => b.BranchId == r.BranchId)?.BranchName : null;
            }
        }
    }
}
