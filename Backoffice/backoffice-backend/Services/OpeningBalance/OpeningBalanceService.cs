using Microsoft.EntityFrameworkCore;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using TDPBackend.Helpers;

namespace RSSMBackend.Services
{
    public class OpeningBalanceService : IOpeningBalanceService
    {
        public const string EntryTypeOpening = "Opening";
        private const string Module = AuditModule.OpeningBalance;

        private readonly RSSMDbContext _context;
        private readonly AuditTrailUtils _auditTrailUtils;

        public OpeningBalanceService(RSSMDbContext context, AuditTrailUtils auditTrailUtils)
        {
            _context = context;
            _auditTrailUtils = auditTrailUtils;
        }

        public async Task<PagedResult<TrxBalance>> GetAll(string? coaCode, int? branchId, DateTime? startDate, DateTime? endDate, int page, int pageSize)
        {
            var query = _context.TrxBalances.AsNoTracking().Where(b => b.EntryType == EntryTypeOpening);

            if (!string.IsNullOrEmpty(coaCode)) query = query.Where(b => b.CoaCode == coaCode);
            if (branchId != null) query = query.Where(b => b.BranchId == branchId);
            if (startDate != null) query = query.Where(b => b.PeriodDate >= startDate.Value);
            if (endDate != null) query = query.Where(b => b.PeriodDate <= endDate.Value);

            int total = await query.CountAsync();

            var listQuery = query.OrderByDescending(b => b.PeriodDate).ThenByDescending(b => b.BalanceId);
            var data = page != -1
                ? await listQuery.Skip((page - 1) * pageSize).Take(pageSize).ToListAsync()
                : await listQuery.ToListAsync();

            await ResolveNames(data);
            return new PagedResult<TrxBalance>(data, total);
        }

        public async Task<TrxBalance?> GetById(int id)
        {
            var row = await _context.TrxBalances.AsNoTracking()
                .FirstOrDefaultAsync(b => b.BalanceId == id && b.EntryType == EntryTypeOpening);
            if (row == null) return null;
            await ResolveNames(new List<TrxBalance> { row });
            return row;
        }

        public async Task<TrxBalance> Save(OpeningBalanceRequest request, int userId, string ipAddress)
        {
            if (string.IsNullOrWhiteSpace(request.CoaCode))
                throw new ApplicationException("CoaCode wajib diisi");
            if (request.BranchId == null)
                throw new ApplicationException("BranchId wajib diisi");

            // Pastikan COA ada pada branch tsb (mencegah salah kode / kebocoran antar-branch).
            bool coaExists = await _context.Coa.AsNoTracking()
                .AnyAsync(c => c.CoaCode == request.CoaCode && c.BranchId == request.BranchId);
            if (!coaExists)
                throw new ApplicationException($"COA '{request.CoaCode}' tidak ditemukan pada branch ini");

            var asOf = DateTime.SpecifyKind(request.AsOfDate, DateTimeKind.Utc);
            var now = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);

            TrxBalance? existing;
            if (request.Id.HasValue)
            {
                existing = await _context.TrxBalances
                    .FirstOrDefaultAsync(b => b.BalanceId == request.Id.Value && b.EntryType == EntryTypeOpening);
                if (existing == null) throw new ApplicationException("Saldo awal tidak ditemukan");
            }
            else
            {
                // Upsert per (coa, branch, tanggal cut-off) untuk baris 'Opening'.
                existing = await _context.TrxBalances.FirstOrDefaultAsync(b =>
                    b.CoaCode == request.CoaCode && b.BranchId == request.BranchId
                    && b.PeriodDate == asOf && b.EntryType == EntryTypeOpening);
            }

            string action;
            object? before = null;

            if (existing == null)
            {
                action = "Create";
                existing = new TrxBalance
                {
                    CoaCode = request.CoaCode,
                    BranchId = request.BranchId,
                    PeriodDate = asOf,
                    EntryType = EntryTypeOpening,
                    CreatedBy = userId,
                    CreatedDate = now,
                };
                await _context.TrxBalances.AddAsync(existing);
            }
            else
            {
                action = "Update";
                before = Snapshot(existing);
            }

            existing.PeriodDate = asOf;
            existing.OpeningBalance = request.Amount;
            existing.DebitAmount = 0;
            existing.CreditAmount = 0;
            existing.ClosingBalance = request.Amount;
            existing.Note = request.Note;
            existing.UpdatedDate = now;

            await _context.SaveChangesAsync();

            string refId = $"{existing.CoaCode}|{existing.BranchId}|{asOf:yyyyMMdd}";
            await _auditTrailUtils.Add(Module, action, refId, before, Snapshot(existing), userId, "Saved", ipAddress, request.BranchId ?? 0);

            // Catatan: trx_balance hanya diisi dari sini (baris 'Opening'). Tidak ada recompute
            // checkpoint otomatis; saldo dihitung on-read (opening + Σ jurnal Published).

            return (await GetById(existing.BalanceId))!;
        }

        public async Task<bool> Remove(int id, int userId, string ipAddress)
        {
            var row = await _context.TrxBalances.FirstOrDefaultAsync(b => b.BalanceId == id && b.EntryType == EntryTypeOpening);
            if (row == null) return false;

            var before = Snapshot(row);
            string coaCode = row.CoaCode;
            int? branchId = row.BranchId;
            DateTime asOf = row.PeriodDate;

            _context.TrxBalances.Remove(row);
            await _context.SaveChangesAsync();

            string refId = $"{coaCode}|{branchId}|{asOf:yyyyMMdd}";
            await _auditTrailUtils.Add(Module, "Delete", refId, before, null, userId, "Deleted", ipAddress, branchId ?? 0);
            return true;
        }

        private static object Snapshot(TrxBalance b) => new
        {
            b.BalanceId, b.CoaCode, b.BranchId, b.PeriodDate,
            b.OpeningBalance, b.DebitAmount, b.CreditAmount, b.ClosingBalance,
            b.EntryType, b.Note, b.CreatedBy
        };

        // Resolusi nama COA & user via query batch di memori (dipanggil setelah baris ter-materialisasi).
        // Pola ini menghindari error EF: projeksi tidak boleh memanggil instance method yang menangkap _context.
        private async Task ResolveNames(List<TrxBalance> rows)
        {
            if (rows.Count == 0) return;

            var coaCodes = rows.Select(r => r.CoaCode).Where(c => !string.IsNullOrEmpty(c)).Distinct().ToList();
            var userIds = rows.Where(r => r.CreatedBy != null).Select(r => r.CreatedBy!.Value).Distinct().ToList();

            var coas = await _context.Coa.AsNoTracking()
                .Where(c => coaCodes.Contains(c.CoaCode))
                .Select(c => new { c.CoaCode, c.BranchId, c.CoaName, c.NormalPosition })
                .ToListAsync();
            var users = await _context.Users.AsNoTracking()
                .Where(u => userIds.Contains(u.UserID))
                .Select(u => new { u.UserID, u.DisplayName })
                .ToListAsync();

            foreach (var r in rows)
            {
                var coa = coas.FirstOrDefault(c => c.CoaCode == r.CoaCode && c.BranchId == r.BranchId)
                          ?? coas.FirstOrDefault(c => c.CoaCode == r.CoaCode);
                r.CoaName = coa?.CoaName;
                r.NormalPosition = coa?.NormalPosition;
                r.CreatedName = r.CreatedBy != null ? users.FirstOrDefault(u => u.UserID == r.CreatedBy)?.DisplayName : null;
            }
        }
    }
}
