using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    public interface IAuditLogService
    {
        Task<PagedResult<AuditLog>> GetAll(
            string? keyword,
            int? branchId,
            string? status,
            DateTime? createdAtFrom,
            DateTime? createdAtTo,
            string? module,
            string? operation,
            int? userId,
            string? orderBy,
            string? orderType,
            int page,
            int pageSize
        );

        /// <summary>
        /// Every logged event for one record, newest first. Backs the Riwayat timeline drawer.
        /// </summary>
        Task<List<AuditLog>> GetTimeline(string module, string refId);

        Task<AuditLog?> GetByID(long id);

        /// <summary>
        /// Distinct modules, operations and actors present in the log -- fills the Audit Trail filter dropdowns.
        /// </summary>
        Task<AuditLogFilterOptions> GetFilterOptions();

        Task<long> Add(AuditLog item);
        Task AddRange(List<AuditLog> items);
        Task<long> Update(AuditLog item);
    }
}
