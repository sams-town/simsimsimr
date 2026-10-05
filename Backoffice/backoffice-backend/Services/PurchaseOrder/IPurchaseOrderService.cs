using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services;

public interface IPurchaseOrderService
{
    Task<(List<PurchaseOrder> Data, int TotalRecords)> GetList(string search, string status, string? supplierType, DateTime? startDate, DateTime? endDate, int page, int pageSize, bool markedPaid = false);
    Task<byte[]> ExportList(string search, string status, string? supplierType, DateTime? startDate, DateTime? endDate, bool markedPaid = false);
    Task<PurchaseOrder?> GetByNumber(string orderNumber);
    Task AddDownPayment(string orderNumber, double amount, DateTime date, string note, string coaCode, string coaName, int userId, int branchId);
    Task<int> MarkAsPaid(string orderNumber, string note, int userId, int branchId);
    Task<int> UnmarkAsPaid(string orderNumber, string note, int userId, int branchId);
    Task<double> GetTotalDownPayment(string orderNumber);
}
