using System.Threading.Tasks;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services.AccountReceivable;

using RSSMBackend.Services.Interface;

public interface IAccountReceivableService
{
    Task<object> GetList(
        string? payerType,
        string? paymentStatus,
        string? serviceType,
        string? payerId,
        DateTime? startDate,
        DateTime? endDate,

        int page,
        int pageSize,
        string? voucherNumber = null,
        string? keyword = null
    );

    Task<RSSMBackend.Entities.AccountReceivable?> GetById(int id);

    Task<object> GetAgingReport(string? payerId, DateTime? asOfDate, string? payerType = null);

    Task<bool> ProcessPayment(
        List<int>? arIds,
        string? payerId,
        DateTime paymentDate,
        string paymentCoaCode,
        string paymentRef,
        int userId
    );

    Task<object> GetPayerTypeOptions();
    Task<object> GetInsuranceOptions();
    Task<byte[]> Export(string? payerType, string? paymentStatus, string? serviceType, string? payerId, DateTime? startDate, DateTime? endDate, string type, string? voucherNumber = null);
    Task<byte[]> ExportAgingReport(string? payerId, DateTime? asOfDate, string type, string? payerType = null);
    
    // New methods for AR flow
    Task<bool> PostAR(List<int> arIds, int userId);
    Task<bool> UnpostAR(List<int> arIds, int userId);
    Task<object> GetVoucherList(string? payerId, string? status, DateTime? startDate, DateTime? endDate, int page, int pageSize, string? voucherNumber = null, string? payerType = null);
    Task<bool> FinalizeVoucher(string payerId, DateTime voucherDate, DateTime? dueDate, int userId, List<int>? arIds = null);
    Task<SettlementResult> ProcessSettlement(SettlementRequest request, int userId);

    // New Features
    Task<bool> ImportAR(Microsoft.AspNetCore.Http.IFormFile file, int userId);
    Task<bool> BulkUpdateStatus(Microsoft.AspNetCore.Http.IFormFile file, int userId);
    Task<bool> VoidAR(List<int> arIds, int userId);
    Task<bool> MarkAsPrinted(List<int> arIds, int userId);
    Task<bool> BulkRevision(Microsoft.AspNetCore.Http.IFormFile file, int userId);
    Task<ArAdjustmentResult> ImportAdjustment(Stream fileStream, string fileName, int userId);
    Task<object> GetPayerOptions(string? keyword = null);
    Task<bool> ChangePayer(int arId, string newPayerId, string newPayerName, string reason, int userId);
    Task<bool> AdjustAr(int arId, decimal newAdjustmentAmount, string reason, int userId);
    Task<bool> AddNote(int arId, string note, int userId);
    Task<Dictionary<string, string>> GetPrintConfig(int branchId);
    /// <summary>Membatalkan voucher AR. Hanya Administrator/Manager (dijaga di service).</summary>
    Task<bool> UnfinalizeVoucher(string voucherNumber, int userId, int roleId);
}
