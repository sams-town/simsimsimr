namespace RSSMBackend.Services;

using RSSMBackend.Models;

public interface IAccountPayableService
{
    Task<PagedResult<AccountPayableResponse>> GetAccountPayableList(
        string? keyword,
        string? supplierName,
        string? paymentStatus,
        string? supplierType,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize,
        string? orderBy,
        string? orderType);
    
    Task<int> ProcessPayment(AccountPayablePaymentRequest request, int userId, int branchId);

    Task<AccountPayableDetailResponse?> GetAccountPayableById(int invoiceExchangeId, int branchId = 1);

    Task<List<AccountPayableAgingResponse>> GetAgingReport(
        string? supplierName,
        string? supplierType,
        DateTime? asOfDate,
        string? dueStatus = null,
        string? keyword = null);

    Task<int> CancelInvoiceExchange(List<int> invoiceIds, int userId, int branchId);

    /// <summary>Membatalkan pembayaran SATU invoice. Hanya Administrator/Manager.</summary>
    Task<object> CancelInvoicePayment(int invoiceExchangeId, string? reason, int roleId, int branchId, int userId);

    /// <summary>Membatalkan seluruh pembayaran dengan payment_ref yang sama. Hanya Administrator/Manager.</summary>
    Task<object> CancelBatchPayment(string paymentRef, string? reason, int roleId, int branchId, int userId);

    Task<int> ProcessBatchPayment(AccountPayableBatchPaymentRequest request, int userId, int branchId);

    Task<BatchPaymentDetailResponse?> GetBatchPaymentDetail(string paymentBatchNumber);

    Task<List<BatchPpnItem>> GetBatchPpnByInvoiceIds(List<int> invoiceExchangeIds, int branchId = 1);
}
