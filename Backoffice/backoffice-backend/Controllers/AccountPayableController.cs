namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Helpers;
using RSSMBackend.Models;

[ApiController]
[Route("[controller]")]
public class AccountPayableController : BaseController
{
    private readonly IAccountPayableService _accountPayableService;
    private readonly ILogger<AccountPayableController> _logger;
    private const string TAG = "AccountPayable {0}";

    public AccountPayableController(
        IAccountPayableService accountPayableService,
        ILogger<AccountPayableController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _accountPayableService = accountPayableService;
        _logger = logger;
    }

    /// <summary>
    /// Get list of account payables (exchanged invoices) with aging information
    /// </summary>
    [HttpGet("List")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetList(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "paymentStatus")] string? paymentStatus = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "duedate",
        [FromQuery(Name = "orderType")] string? orderType = "asc")
    {
        try
        {
            var data = await _accountPayableService.GetAccountPayableList(
                keyword, supplierName, paymentStatus, supplierType, startDate, endDate, page, pageSize, orderBy, orderType);

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get single account payable by ID for payment detail page
    /// </summary>
    [HttpGet("{id}")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetById(int id)
    {
        try
        {
            var data = await _accountPayableService.GetAccountPayableById(id, userSession?.BranchId ?? 1);
            if (data == null)
                return _NotFound("Invoice tidak ditemukan");

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetById Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export account payables to Excel
    /// </summary>
    [HttpGet("Export")]
    [Authorize(Page.TrxAccountPayable, Action.Export)]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "paymentStatus")] string? paymentStatus = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "duedate",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string type = "xlsx")
    {
        try
        {
            var data = await _accountPayableService.GetAccountPayableList(
                keyword, supplierName, paymentStatus, supplierType, startDate, endDate, -1, -1, orderBy, orderType);

            if (data == null || data.Data == null || data.Data.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            byte[] fileContent;
            string fileName = $"AccountPayable_{DateTime.Now:yyyyMMdd}.";

            if (type.ToLower() == "xlsx")
            {
                // Rounds per cell itself so the TOTAL row can be rounded from the exact sum.
                fileContent = ExportToDocs.ConvertAccountPayableToExcel(data.Data, startDate, endDate);
                fileName += "xlsx";
            }
            else
            {
                // ConvertToCsv is shared with the master-data modules and must stay untouched,
                // so round the AP rows here instead. No total row in CSV, so per-row is exact.
                MoneyRounding.RoundForExport(data.Data);
                fileContent = ExportToDocs.ConvertToCsv(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Export Error");
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxAccountPayable, Action.Export)]
    [HttpGet("ExportResi/{id}")]
    public async Task<IActionResult> ExportResi(int id)
    {
        try
        {
            var data = await _accountPayableService.GetAccountPayableById(id, userSession?.BranchId ?? 1);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, $"ID {id} Not Found"));
            }

            string printedBy = userSession?.DisplayName ?? "System";
            byte[] fileContent = ExportToDocs.ConvertAccountPayableResiToExcel(data, printedBy);
            string fileName = $"Resi_AP_{data.InvoiceNumber}_{DateTime.Now:yyyyMMdd}.xlsx";

            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Export Resi Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Process payment for selected invoices
    /// </summary>
    [HttpPost("Payment")]
    [Authorize(Page.TrxAccountPayable, Action.Create)]
    public async Task<IActionResult> ProcessPayment([FromBody] AccountPayablePaymentRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (request.InvoiceIds == null || request.InvoiceIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Invoice IDs are required"));

            if (string.IsNullOrEmpty(request.CoaCode))
                return _BadRequest(string.Format(TAG, "Bank/Cash account (COA) is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountPayableService.ProcessPayment(request, userSession.UserID, userSession.BranchId ?? 1);

            if (result > 0)
            {
                return _Ok(new { updatedCount = result }, 
                    string.Format(TAG, $"{result} invoice(s) payment has been successfully processed"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No invoices to process. They may already be paid."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ProcessPayment Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Process batch payment for multiple invoices with per-invoice discount and per-supplier PPH23
    /// </summary>
    [HttpPost("BatchPayment")]
    [Authorize(Page.TrxAccountPayable, Action.Create)]
    public async Task<IActionResult> ProcessBatchPayment([FromBody] AccountPayableBatchPaymentRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (request.Invoices == null || request.Invoices.Count == 0)
                return _BadRequest(string.Format(TAG, "Invoice items are required"));

            if (string.IsNullOrEmpty(request.CoaCode))
                return _BadRequest(string.Format(TAG, "Bank/Cash account (COA) is required"));

            if (request.TotalAmount <= 0)
                return _BadRequest(string.Format(TAG, "Total amount must be greater than 0"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountPayableService.ProcessBatchPayment(request, userSession.UserID, userSession.BranchId ?? 1);

            if (result > 0)
            {
                return _Ok(new { updatedCount = result },
                    string.Format(TAG, $"{result} invoice(s) batch payment has been successfully processed"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No invoices to process. They may already be paid."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ProcessBatchPayment Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get batch payment detail by batch number
    /// </summary>
    [HttpGet("BatchPayment/{batchNumber}")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetBatchPaymentDetail(string batchNumber)
    {
        try
        {
            var data = await _accountPayableService.GetBatchPaymentDetail(batchNumber);
            if (data == null)
                return _NotFound("Batch payment tidak ditemukan");

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetBatchPaymentDetail Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get aging report grouped by supplier
    /// </summary>
    [HttpGet("AgingReport")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetAgingReport(
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "asOfDate")] DateTime? asOfDate = null,
        [FromQuery(Name = "dueStatus")] string? dueStatus = null,
        [FromQuery(Name = "keyword")] string? keyword = null)
    {
        try
        {
            var data = await _accountPayableService.GetAgingReport(supplierName, supplierType, asOfDate, dueStatus, keyword);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetAgingReport Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export aging report to Excel
    /// </summary>
    [HttpGet("AgingReport/Export")]
    [Authorize(Page.TrxAccountPayable, Action.Export)]
    public async Task<IActionResult> ExportAgingReport(
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "asOfDate")] DateTime? asOfDate = null,
        [FromQuery(Name = "dueStatus")] string? dueStatus = null,
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "type")] string type = "xlsx")
    {
        try
        {
            var data = await _accountPayableService.GetAgingReport(supplierName, supplierType, asOfDate, dueStatus, keyword);

            if (data == null || data.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            byte[] fileContent;
            string fileName = $"AgingReport_{DateTime.Now:yyyyMMdd}.";

            // Both exporters below are generic dumpers shared with other modules: they cannot
            // round per cell, and neither emits a total row. Round the AP rows once, here.
            MoneyRounding.RoundForExport(data);

            if (type.ToLower() == "xlsx")
            {
                fileContent = ExportToDocs.ConvertToExcel(data);
                fileName += "xlsx";
            }
            else
            {
                fileContent = ExportToDocs.ConvertToCsv(data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ExportAgingReport Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get PPN amounts for multiple invoices (from journal by PO number)
    /// </summary>
    [HttpPost("BatchPpn")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetBatchPpn([FromBody] BatchPpnRequest request)
    {
        try
        {
            if (request?.InvoiceExchangeIds == null || request.InvoiceExchangeIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Invoice IDs are required"));

            var data = await _accountPayableService.GetBatchPpnByInvoiceIds(request.InvoiceExchangeIds, userSession?.BranchId ?? 1);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetBatchPpn Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Membatalkan pembayaran satu invoice. Membuat jurnal pembalik dan mengembalikan
    /// invoice ke Unpaid/Partial. Tidak menghapus data.
    /// </summary>
    [HttpPost("Payment/{invoiceExchangeId}/Cancel")]
    [Authorize(Page.TrxAccountPayable, Action.Delete)]
    public async Task<IActionResult> CancelInvoicePayment(int invoiceExchangeId, [FromBody] CancelPaymentModel? model)
    {
        try
        {
            var result = await _accountPayableService.CancelInvoicePayment(
                invoiceExchangeId, model?.Reason, userSession.RoleID, userSession.BranchId ?? 1, userSession.UserID);
            return _Ok(result, "Pembayaran invoice dibatalkan dan jurnal pembalik dibuat");
        }
        catch (Exception e)
        {
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Membatalkan SELURUH pembayaran dengan payment_ref yang sama - semua invoice di
    /// dalamnya kembali Unpaid/Partial.
    /// </summary>
    [HttpPost("BatchPayment/Cancel")]
    [Authorize(Page.TrxAccountPayable, Action.Delete)]
    public async Task<IActionResult> CancelBatchPayment([FromQuery] string paymentRef, [FromBody] CancelPaymentModel? model)
    {
        try
        {
            var result = await _accountPayableService.CancelBatchPayment(
                paymentRef, model?.Reason, userSession.RoleID, userSession.BranchId ?? 1, userSession.UserID);
            return _Ok(result, "Seluruh pembayaran dibatalkan dan jurnal pembalik dibuat");
        }
        catch (Exception e)
        {
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Cancel/Void invoice exchange - revert status from Sent to not sent
    /// </summary>
    [HttpPut("Cancel")]
    [Authorize(Page.TrxAccountPayable, Action.Update)]
    public async Task<IActionResult> CancelInvoiceExchange([FromBody] CancelInvoiceExchangeRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (request.InvoiceIds == null || request.InvoiceIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Invoice IDs are required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountPayableService.CancelInvoiceExchange(request.InvoiceIds, userSession.UserID, userSession.BranchId ?? 1);

            if (result > 0)
            {
                return _Ok(new { cancelledCount = result }, 
                    string.Format(TAG, $"{result} invoice(s) have been cancelled and reverted to 'not sent' status"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No invoices to cancel. They may already be cancelled or paid."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "CancelInvoiceExchange Error");
            return _BadRequest(e.Message);
        }
    }
}
