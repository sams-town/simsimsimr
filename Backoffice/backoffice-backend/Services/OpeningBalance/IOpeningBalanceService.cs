using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface IOpeningBalanceService
    {
        // Daftar saldo awal (baris trx_balance entry_type='Opening'), difilter & dipaginasi.
        Task<PagedResult<TrxBalance>> GetAll(string? coaCode, int? branchId, DateTime? startDate, DateTime? endDate, int page, int pageSize);

        Task<TrxBalance?> GetById(int id);

        // Buat / perbarui saldo awal (upsert per coa+branch+asOfDate) lalu recompute checkpoint hilir.
        // Menulis audit (module 'OpeningBalance'). Mengembalikan baris tersimpan.
        Task<TrxBalance> Save(OpeningBalanceRequest request, int userId, string ipAddress);

        Task<bool> Remove(int id, int userId, string ipAddress);
    }
}
