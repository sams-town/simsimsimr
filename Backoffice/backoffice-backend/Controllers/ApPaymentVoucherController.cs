namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services.AccountPayable;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.DTOs.ApPaymentVoucher;

[ApiController]
[Route("AccountPayable/PaymentVoucher")]
public class ApPaymentVoucherController : BaseController
{
    private readonly IApPaymentVoucherService _service;
    private readonly ILogger<ApPaymentVoucherController> _logger;
    private const string TAG = "ApPaymentVoucher {0}";

    public ApPaymentVoucherController(
        IApPaymentVoucherService service,
        ILogger<ApPaymentVoucherController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
        _logger = logger;
    }

    /// <summary>AP invoices eligible to be added (Unpaid/Partial, not already in another voucher).</summary>
    [HttpGet("EligibleItems")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetEligibleItems(
        [FromQuery] string? supplierName = null,
        [FromQuery] string? supplierType = null,
        [FromQuery] string? paymentStatus = null,
        [FromQuery] string? keyword = null,
        [FromQuery] DateTime? startDate = null,
        [FromQuery] DateTime? endDate = null,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10,
        [FromQuery] int? excludeVoucherId = null)
    {
        try
        {
            var data = await _service.GetEligibleItems(supplierName, supplierType, paymentStatus, keyword, startDate, endDate, page, pageSize, excludeVoucherId);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetEligibleItems Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Paginated list of saved AP payment vouchers.</summary>
    [HttpGet("List")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetList(
        [FromQuery] string? supplierName = null,
        [FromQuery] string? keyword = null,
        [FromQuery] DateTime? startDate = null,
        [FromQuery] DateTime? endDate = null,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var data = await _service.GetList(supplierName, keyword, startDate, endDate, page, pageSize);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Detail of a single AP payment voucher with its invoice items.</summary>
    [HttpGet("{id:int}")]
    [Authorize(Page.TrxAccountPayable, Action.Read)]
    public async Task<IActionResult> GetDetail(int id)
    {
        try
        {
            var data = await _service.GetDetail(id);
            if (data == null) return _NotFound(string.Format(TAG, "Voucher tidak ditemukan"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetDetail Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>Create a new AP payment voucher with initial invoice items.</summary>
    [HttpPost("Create")]
    [Authorize(Page.TrxAccountPayable, Action.Create)]
    public async Task<IActionResult> Create([FromBody] CreateApPaymentVoucherRequest request)
    {
        try
        {
            if (request == null || request.InvoiceExchangeIds == null || request.InvoiceExchangeIds.Count == 0)
                return _BadRequest("Minimal satu invoice harus dipilih.");

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest("Sesi pengguna tidak ditemukan, silakan login ulang.");

            var voucherDate = request.VoucherDate ?? DateTime.UtcNow;
            var data = await _service.Create(voucherDate, request.PaymentType ?? "CASH", request.InvoiceExchangeIds, userSession.UserID);
            return _Ok(data, "Bukti pengeluaran berhasil dibuat.");
        }
        catch (InvalidOperationException ioe)
        {
            return _BadRequest(ioe.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Create Error");
            if (IsUniqueViolation(e))
                return _BadRequest("Invoice sudah terdaftar di voucher lain. Silakan periksa kembali pilihan invoice.");
            return _BadRequest("Terjadi kesalahan saat menyimpan data. Silakan coba lagi.");
        }
    }

    /// <summary>Update voucher header (date and/or payment type).</summary>
    [HttpPost("{id:int}/Update")]
    [Authorize(Page.TrxAccountPayable, Action.Update)]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateApPaymentVoucherRequest request)
    {
        try
        {
            if (request == null || (request.VoucherDate == null && string.IsNullOrEmpty(request.PaymentType)))
                return _BadRequest("Tidak ada data yang diubah.");

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest("Sesi pengguna tidak ditemukan, silakan login ulang.");

            var result = await _service.UpdateVoucher(id, request.VoucherDate, request.PaymentType, userSession.UserID);
            if (result) return _Ok(new { success = true }, "Voucher berhasil diperbarui.");
            return _NotFound("Voucher tidak ditemukan.");
        }
        catch (InvalidOperationException ioe)
        {
            return _BadRequest(ioe.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Update Error");
            return _BadRequest("Terjadi kesalahan saat memperbarui voucher. Silakan coba lagi.");
        }
    }

    /// <summary>Add invoice items to an existing voucher.</summary>
    [HttpPost("{id:int}/AddItems")]
    [Authorize(Page.TrxAccountPayable, Action.Update)]
    public async Task<IActionResult> AddItems(int id, [FromBody] ApPaymentVoucherItemsRequest request)
    {
        try
        {
            if (request == null || request.InvoiceExchangeIds == null || request.InvoiceExchangeIds.Count == 0)
                return _BadRequest("Minimal satu invoice harus dipilih.");

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest("Sesi pengguna tidak ditemukan, silakan login ulang.");

            var result = await _service.AddItems(id, request.InvoiceExchangeIds, userSession.UserID);
            if (result) return _Ok(new { success = true }, "Invoice berhasil ditambahkan.");
            return _BadRequest("Invoice tidak dapat ditambahkan: semua invoice yang dipilih sudah terdaftar di voucher ini atau voucher lain, atau statusnya sudah lunas.");
        }
        catch (InvalidOperationException ioe)
        {
            return _BadRequest(ioe.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "AddItems Error");
            if (IsUniqueViolation(e))
                return _BadRequest("Invoice sudah terdaftar di voucher ini. Tidak dapat menambahkan invoice yang sama dua kali.");
            return _BadRequest("Terjadi kesalahan saat menyimpan data. Silakan coba lagi.");
        }
    }

    /// <summary>Remove invoice items from a voucher.</summary>
    [HttpPost("{id:int}/RemoveItems")]
    [Authorize(Page.TrxAccountPayable, Action.Update)]
    public async Task<IActionResult> RemoveItems(int id, [FromBody] ApPaymentVoucherItemsRequest request)
    {
        try
        {
            if (request == null || request.InvoiceExchangeIds == null || request.InvoiceExchangeIds.Count == 0)
                return _BadRequest("Minimal satu invoice harus dipilih.");

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest("Sesi pengguna tidak ditemukan, silakan login ulang.");

            var result = await _service.RemoveItems(id, request.InvoiceExchangeIds, userSession.UserID);
            if (result) return _Ok(new { success = true }, "Invoice berhasil dihapus dari voucher.");
            return _NotFound("Invoice tidak ditemukan di voucher ini.");
        }
        catch (InvalidOperationException ioe)
        {
            return _BadRequest(ioe.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "RemoveItems Error");
            return _BadRequest("Terjadi kesalahan saat menghapus invoice. Silakan coba lagi.");
        }
    }

    /// <summary>Delete an AP payment voucher and its items.</summary>
    [HttpDelete("{id:int}")]
    [Authorize(Page.TrxAccountPayable, Action.Delete)]
    public async Task<IActionResult> Delete(int id)
    {
        try
        {
            var result = await _service.Delete(id);
            if (result) return _Ok(new { success = true }, "Voucher berhasil dihapus.");
            return _NotFound("Voucher tidak ditemukan.");
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Delete Error");
            return _BadRequest("Terjadi kesalahan saat menghapus voucher. Silakan coba lagi.");
        }
    }

    private static bool IsUniqueViolation(Exception e)
    {
        var msg = (e.InnerException?.Message ?? e.Message).ToLower();
        return msg.Contains("23505") || msg.Contains("unique") || msg.Contains("duplicate");
    }
}

