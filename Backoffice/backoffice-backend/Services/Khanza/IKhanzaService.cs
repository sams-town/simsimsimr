using RSSMBackend.Models;
using RSSMBackend.Entities;

namespace RSSMBackend.Services
{
    public interface IKhanzaService
    {
        Task<PagedResult<KhanzaJournalDto>> GetJournals(DateTime startDate, DateTime endDate, int page, int pageSize, int branchId);
        Task<byte[]> ExportJournals(DateTime startDate, DateTime endDate, int branchId);
    }
}
