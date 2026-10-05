namespace RSSMBackend.Services.PettyCash;

using RSSMBackend.Entities;
using RSSMBackend.Models;

public interface IPettyCashService
{
    // ========== Dashboard ==========
    Task<object> GetDashboard(int branchId, int roleId, int userId);
    
    // ========== Unit Management ==========
    Task<object> GetUnits(int branchId, string? keyword, int page, int pageSize);
    Task<object> GetUnitOptions(int branchId);
    Task<object> GetMyUnit(int userId);
    Task<PettyCashUnit?> GetUnitById(int id);
    Task<int> AddUnit(UnitModel model, int userId);
    Task<bool> UpdateUnit(UnitModel model, int userId);
    Task<bool> DeleteUnit(int id, int userId);
    
    // ========== Request ==========
    Task<object> GetRequests(int branchId, int? unitId, string? status, string? settlementStatus, DateTime? startDate, DateTime? endDate, string? keyword, int page, int pageSize, int roleId, int userId);
    Task<PettyCashRequest?> GetRequestById(int id);
    Task<string> GenerateRequestNo(DateTime date);
    Task<int> AddRequest(PettyCashRequest request, int userId);
    Task<bool> UpdateRequest(PettyCashRequest request, int userId);
    Task<bool> DeleteRequest(int id, int userId);
    Task<bool> SubmitRequest(int id, int userId);
    Task<bool> ApproveRequest(int id, decimal approvedAmount, int userId);
    Task<bool> RejectRequest(int id, string reason, int userId);
    Task<object?> GetRequestReceipt(int id);
    
    // ========== Settlement (with auto journal) ==========
    Task<object> GetSettlements(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId);
    Task<object?> GetSettlementById(int id);

    /// <summary>Semua percobaan settlement untuk satu request, termasuk yang dibatalkan.</summary>
    Task<object> GetSettlementHistoryByRequest(int requestId);
    Task<string> GenerateSettlementNo(DateTime date);
    Task<object> CreateSettlement(PettyCashSettlement settlement, List<PettyCashSettlementDetail> details, int branchId, int userId);
    Task<bool> PostSettlement(int id, int branchId, int userId);
    Task<bool> VoidSettlement(int id, int userId);

    /// <summary>
    /// Membatalkan settlement yang sudah diposting dan mengembalikan request-nya
    /// agar bisa di-settle ulang. Membuat jurnal pembalik, bukan menghapus data.
    /// Hanya Administrator/Manager/Supervisor.
    /// </summary>
    Task<object> CancelSettlement(int id, string? reason, int roleId, int branchId, int userId);
    
    // ========== TopUp (with auto journal) ==========
    Task<object> GetTopUps(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId);
    Task<PettyCashTopUp?> GetTopUpById(int id);
    Task<string> GenerateTopUpNo(DateTime date);
    Task<int> AddTopUp(PettyCashTopUp topUp, int userId, int roleId);
    Task<string> GetTopUpDefaultCOA(int branchId);
    Task<bool> ApproveTopUp(int id, decimal approvedAmount, string? notes, int branchId, int userId);
    Task<bool> RejectTopUp(int id, string reason, int userId);
    Task<bool> VoidTopUp(int id, int userId);
    Task<object?> GetTopUpReceipt(int id);
    
    // ========== Ledger ==========
    Task<object> GetLedger(int unitId, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId);
    
    // ========== Export ==========
    Task<byte[]> ExportRequests(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int roleId, int userId);
    Task<byte[]> ExportSettlements(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId);
    Task<byte[]> ExportTopUps(int branchId, int? unitId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, int roleId, int userId);
    Task<byte[]> ExportLedger(int unitId, DateTime? startDate, DateTime? endDate, int roleId, int userId);
}
