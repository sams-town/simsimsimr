using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface IPeriodClosingService
    {
        Task<PagedResult<PeriodClosing>> GetAll(string? periodType, int? year, string? status, int? branchId, int page, int pageSize);
        Task<PeriodClosing?> GetById(int id);

        // Tutup buku periode: buat checkpoint + (opsional) jurnal penutup + kunci periode + catat audit.
        Task<PeriodClosing> ClosePeriod(PeriodClosingRequest req, int? branchId, int userId, string ipAddress);

        // Buka kembali periode: void jurnal penutup (bila ada), recompute checkpoint, catat audit.
        Task<PeriodClosing> ReopenPeriod(int id, string? note, int userId, string ipAddress);

        // True bila tanggal transaksi berada dalam periode yang berstatus 'Closed'.
        Task<bool> IsPeriodClosed(DateTime transactionDate, int? branchId);

        // Lempar ApplicationException bila periode tanggal transaksi sudah ditutup.
        Task EnsureNotClosed(DateTime? transactionDate, int? branchId);
    }
}
