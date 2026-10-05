namespace RSSMBackend.Services.AccountPayable;

public interface IApPaymentVoucherService
{
    Task<object> GetEligibleItems(string? supplierName, string? supplierType, string? paymentStatus, string? keyword, DateTime? startDate, DateTime? endDate, int page, int pageSize, int? excludeVoucherId);
    Task<object> GetList(string? supplierName, string? keyword, DateTime? startDate, DateTime? endDate, int page, int pageSize);
    Task<object?> GetDetail(int id);
    Task<object> Create(DateTime voucherDate, string paymentType, List<int> invoiceExchangeIds, int userId);
    Task<bool> UpdateVoucher(int id, DateTime? voucherDate, string? paymentType, int userId);
    Task<bool> AddItems(int id, List<int> invoiceExchangeIds, int userId);
    Task<bool> RemoveItems(int id, List<int> invoiceExchangeIds, int userId);
    Task<bool> Delete(int id);
}
