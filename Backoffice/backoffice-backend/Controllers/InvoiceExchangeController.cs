namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Storage;

[ApiController]
[Route("[controller]")]
public class InvoiceExchangeController : BaseController
{
    private IInvoiceExchangeService invoiceExchangeService;
    private readonly ILogger<InvoiceExchangeController> _logger;
    private const string TAG = "InvoiceExchange {0}";
    private const string LOG = "InvoiceExchange {x}";
    private RSSMDbContext dbContext;

    public InvoiceExchangeController(RSSMDbContext dbContext, IInvoiceExchangeService invoiceExchangeService,
        ILogger<InvoiceExchangeController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.invoiceExchangeService = invoiceExchangeService;
        _logger = logger;
        this.dbContext = dbContext;
    }
    
    [Authorize(Page.TrxInvoiceExchange, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await invoiceExchangeService.GetInvoiceExchange(keyword, supplierName, status, supplierType, startDate, endDate, page, pageSize, orderBy, orderType);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxInvoiceExchange, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string type = "xlsx"
    )
    {
        try
        {
            // For export, if no date filter provided, use a wide date range to get all data
            DateTime? exportStartDate = startDate;
            DateTime? exportEndDate = endDate;
            if (startDate == null && endDate == null)
            {
                // Set wide date range (10 years back to now) for export to get all data
                exportEndDate = DateTime.UtcNow;
                exportStartDate = exportEndDate.Value.AddYears(-10);
            }

            var data = await invoiceExchangeService.GetInvoiceExchange(keyword, supplierName, status, supplierType, exportStartDate, exportEndDate, -1, -1, orderBy, orderType);
            if (data == null || data.Data == null || data.Data.Count == 0)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            // Flatten the DataInvoices from all groups and remove InvoiceExchangeId
            var flatData = data.Data
                .Where(x => x.DataInvoices != null && x.DataInvoices.Count > 0)
                .SelectMany(x => x.DataInvoices)
                .Select(x => new DataInvoiceExchangeExportResponse
                {
                    PONumber = x.PONumber,
                    ReceiptDate = x.ReceiptDate,
                    PODate = x.PODate,
                    DueDate = x.DueDate,
                    SupplierName = x.SupplierName,
                    TotalTransaction = x.TotalTransaction,
                    Status = x.Status
                })
                .ToList();

            if (flatData == null || flatData.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            byte[] fileContent;
            string fileName = $"InvoiceExchange.";

            // Tukar Faktur sits under the Account Payable menu, so its output follows the same
            // whole-rupiah rule. Both exporters below are generic dumpers shared with other
            // modules and emit no total row, so round the rows once, here.
            MoneyRounding.RoundForExport(flatData);

            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower()))
            {
                fileContent = ExportToDocs.ConvertToExcel<DataInvoiceExchangeExportResponse>(flatData);
                fileName += ExportType.XLSX.ToString().ToLower();
            }
            else
            {
                fileContent = ExportToDocs.ConvertToCsv<DataInvoiceExchangeExportResponse>(flatData);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxInvoiceExchange, Action.Update)]
    [HttpPut("UpdateStatus")]
    public async Task<IActionResult> UpdateStatus([FromBody] InvoiceExchangeUpdateStatusRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (string.IsNullOrEmpty(request.Id))
                return _BadRequest(string.Format(TAG, "Id is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await invoiceExchangeService.UpdateStatus(request.Id, userSession.UserID, userSession.BranchId ?? 1, request.DueDate, request.ExchangeDate);
            if (result > 0)
            {
                return _Ok(new { updatedCount = result }, string.Format(TAG, $"{result} record(s) status has been successfully updated"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No records to update. All selected items may already be marked as 'Sent'"));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Manually create an invoice exchange (non-PO) directly from Daftar AP.</summary>
    [Authorize(Page.TrxInvoiceExchange, Action.Create)]
    [HttpPost("CreateManual")]
    public async Task<IActionResult> CreateManual([FromBody] ManualInvoiceExchangeInput request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var entity = await invoiceExchangeService.CreateManual(request, userSession.UserID);
            return _Ok(new { invoiceExchangeId = entity.InvoiceExchangeId }, string.Format(TAG, "Data berhasil ditambahkan"));
        }
        catch (InvalidOperationException ioe)
        {
            return _BadRequest(ioe.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest("Terjadi kesalahan saat menyimpan data. Silakan coba lagi.");
        }
    }

    [Authorize(Page.TrxInvoiceExchange, Action.Read)]
    [HttpGet("Option/Supplier")]
    public async Task<IActionResult> GetSupplierOptions(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "suppliername",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await invoiceExchangeService.GetSupplierOptions(keyword, page, pageSize, orderBy, orderType);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxInvoiceExchange, Action.Read)]
    [HttpGet("Transaction")]
    public async Task<IActionResult> GetTransactions(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 50,
        [FromQuery(Name = "orderBy")] string? orderBy = "suppliername",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await invoiceExchangeService.GetTransactions(
                keyword,
                supplierName,
                startDate,
                endDate,
                status,
                supplierType,
                page,
                pageSize,
                orderBy,
                orderType
            );
            
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxInvoiceExchange, Action.Read)]
    [HttpGet("Transaction/Detail")]
    public async Task<IActionResult> GetTransactionDetail(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "invoiceExchangeId")] int? invoiceExchangeId = null,
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "supplierType")] string? supplierType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "suppliername",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
    )
    {
        try
        {
            var data = await invoiceExchangeService.GetTransactionDetail(
                keyword,
                invoiceExchangeId,
                supplierName,
                status,
                supplierType,
                startDate,
                endDate,
                page,
                pageSize,
                orderBy,
                orderType
            );
            
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get print receipt (resi) data for invoice exchange - returns JSON for frontend to render
    /// </summary>
    [HttpGet("PrintResi")]
    public async Task<IActionResult> PrintResi(
        [FromQuery(Name = "supplierName")] string? supplierName = null,
        [FromQuery(Name = "batchNumber")] string? batchNumber = null
    )
    {
        try
        {
            if (string.IsNullOrEmpty(supplierName))
                return _BadRequest("Supplier name is required");

            // Get exchanged invoices for the supplier
            var data = await invoiceExchangeService.GetTransactionDetail(
                null, // keyword
                null, // invoiceExchangeId
                supplierName,
                "Sent", // Only sent/exchanged invoices
                null, // supplierType
                null,
                null,
                -1, // Get all
                -1,
                "receiptdate",
                "desc"
            );

            if (data == null || data.Data == null || data.Data.Count == 0)
                return _NotFound("No exchanged invoices found for this supplier");

            var invoices = data.Data;
            var totalAmount = invoices.Sum(x => x.TotalTransaction ?? 0);
            var firstInvoice = invoices.First();
            var userName = userSession?.DisplayName ?? "System";

            // Return JSON data for frontend to render
            var result = new
            {
                supplierName = supplierName,
                batchNumber = firstInvoice.BatchNumber ?? "-",
                exchangeDate = firstInvoice.ExchangeDate?.ToString("o"),
                items = invoices.Select(inv => new
                {
                    invoiceExchangeId = inv.InvoiceExchangeId,
                    poNumber = inv.PONumber ?? "-",
                    invoiceNumber = inv.InvoiceNumber ?? "-",
                    receiptDate = inv.ReceiptDate?.ToString("o"),
                    dueDate = inv.DueDate?.ToString("o"),
                    totalTransaction = inv.TotalTransaction ?? 0
                }).ToList(),
                totalAmount = totalAmount,
                printedDate = DateTime.Now.ToString("o"),
                printedBy = userName
            };

            return _Ok(result);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get print receipt (resi) data for single invoice exchange by ID
    /// </summary>
    [HttpGet("PrintResiById")]
    public async Task<IActionResult> PrintResiById(
        [FromQuery(Name = "invoiceExchangeId")] int invoiceExchangeId
    )
    {
        try
        {
            if (invoiceExchangeId <= 0)
                return _BadRequest("Invoice Exchange ID is required");

            // Get invoice by ID using the existing service
            var invoice = await invoiceExchangeService.GetByID(invoiceExchangeId);

            if (invoice == null)
                return _NotFound("Invoice not found");

            var userName = userSession?.DisplayName ?? "System";

            // Return JSON data for frontend to render
            var result = new
            {
                supplierName = invoice.SupplierName ?? "Unknown Supplier",
                batchNumber = invoice.BatchNumber ?? "-",
                exchangeDate = invoice.ExchangeDate?.ToString("o"),
                items = new[] 
                {
                    new
                    {
                        invoiceExchangeId = invoice.InvoiceExchangeId,
                        poNumber = invoice.PONumber ?? "-",
                        invoiceNumber = invoice.InvoiceNumber ?? "-",
                        receiptDate = invoice.ReceiptDate?.ToString("o"),
                        dueDate = invoice.DueDate?.ToString("o"),
                        totalTransaction = invoice.TotalTransaction ?? 0
                    }
                },
                totalAmount = invoice.TotalTransaction ?? 0,
                printedDate = DateTime.Now.ToString("o"),
                printedBy = userName
            };

            return _Ok(result);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}