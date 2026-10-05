using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services
{
    /// <summary>Manual (non-PO) invoice exchange entry payload.</summary>
    public class ManualInvoiceExchangeInput
    {
        public string SupplierName { get; set; } = string.Empty;
        public string? SupplierCode { get; set; }
        public string InvoiceNumber { get; set; } = string.Empty;
        public DateTime? ReceiptDate { get; set; }
        public DateTime? DueDate { get; set; }
        public double TotalTransaction { get; set; }
        public double DiscountPercent { get; set; }
        public double DiscountAmount { get; set; }
        public double Pph23Percent { get; set; }
        public double Pph23Amount { get; set; }
        public double PpnPercent { get; set; }
        public double PpnAmount { get; set; }
    }

    public interface IInvoiceExchangeService
    {
        Task<PagedResult<InvoiceExchangeResponse>> GetInvoiceExchange(
            string? keyword,
            string? supplierName,
            string? status,
            string? supplierType,
            DateTime? startDate,
            DateTime? endDate,
            int page,
            int pageSize,
            string? orderBy,
            string? orderType );
        
        Task<InvoiceExchange?> GetByID(int id);
        Task<int> UpdateStatus(string ids, int? updatedBy, int branchId, DateTime? dueDate = null, DateTime? exchangeDate = null);
        Task<InvoiceExchange> CreateManual(ManualInvoiceExchangeInput input, int userId);
        Task<PagedResult<OptionResponse>> GetSupplierOptions(string? keyword, int page, int pageSize, string? orderBy, string? orderType);
        Task<PagedResult<InvoiceExchangeTransactionResponse>> GetTransactions(
            string? keyword,
            string? supplierName,
            DateTime? startDate,
            DateTime? endDate,
            string? status,
            string? supplierType,
            int page,
            int pageSize,
            string? orderBy,
            string? orderType);
        Task<PagedResult<InvoiceExchange>> GetTransactionDetail(
            string? keyword,
            int? invoiceExchangeId,
            string? supplierName,
            string? status,
            string? supplierType,
            DateTime? startDate,
            DateTime? endDate,
            int page,
            int pageSize,
            string? orderBy,
            string? orderType);
    }
}