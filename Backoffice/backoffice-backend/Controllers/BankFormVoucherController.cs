namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services.BankFormMaker;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.DTOs.BankFormVoucher;

/// <summary>
/// Form Maker for bank vouchers (Penerimaan / Pengeluaran) — manual data entry.
/// VoucherType query/body param: "PENERIMAAN" or "PENGELUARAN".
/// </summary>
[ApiController]
[Route("BankFormVoucher")]
public class BankFormVoucherController : BaseController
{
    private readonly IBankFormVoucherService _service;
    private readonly ILogger<BankFormVoucherController> _logger;
    private const string TAG = "BankFormVoucher {0}";

    public BankFormVoucherController(
        IBankFormVoucherService service,
        ILogger<BankFormVoucherController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
        _logger = logger;
    }

    /// <summary>Paginated list of saved bank vouchers for a type.</summary>
    [HttpGet("List")]
    [Authorize(Page.TrxTransaction, Action.Read)]
    public async Task<IActionResult> GetList(
        [FromQuery] string voucherType = "PENERIMAAN",
        [FromQuery] string? keyword = null,
        [FromQuery] DateTime? startDate = null,
        [FromQuery] DateTime? endDate = null,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var data = await _service.GetList(voucherType, keyword, startDate, endDate, page, pageSize);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetList Error");
            return _BadRequest("Terjadi kesalahan saat mengambil daftar voucher. Silakan coba lagi.");
        }
    }

    /// <summary>Detail of a single voucher.</summary>
    [HttpGet("{id:int}")]
    [Authorize(Page.TrxTransaction, Action.Read)]
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
            return _BadRequest("Terjadi kesalahan saat mengambil detail voucher. Silakan coba lagi.");
        }
    }

    /// <summary>Create a voucher from the input form.</summary>
    [HttpPost("Create")]
    [Authorize(Page.TrxTransaction, Action.Create)]
    public async Task<IActionResult> Create([FromBody] BankFormVoucherRequest request)
    {
        try
        {
            if (request == null) return _BadRequest("Data tidak valid.");
            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest("Sesi pengguna tidak ditemukan, silakan login ulang.");

            var input = ToInput(request);
            var data = await _service.Create(input, userSession.BranchId, userSession.UserID);
            return _Ok(data, "Bukti berhasil dibuat.");
        }
        catch (InvalidOperationException ioe)
        {
            return _BadRequest(ioe.Message);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Create Error");
            return _BadRequest("Terjadi kesalahan saat menyimpan data. Silakan coba lagi.");
        }
    }

    /// <summary>Update a voucher from the input form.</summary>
    [HttpPost("{id:int}/Update")]
    [Authorize(Page.TrxTransaction, Action.Update)]
    public async Task<IActionResult> Update(int id, [FromBody] BankFormVoucherRequest request)
    {
        try
        {
            if (request == null) return _BadRequest("Data tidak valid.");
            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest("Sesi pengguna tidak ditemukan, silakan login ulang.");

            var input = ToInput(request);
            var result = await _service.Update(id, input, userSession.UserID);
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

    /// <summary>Delete a voucher.</summary>
    [HttpDelete("{id:int}")]
    [Authorize(Page.TrxTransaction, Action.Delete)]
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

    private static BankFormVoucherInput ToInput(BankFormVoucherRequest r) => new()
    {
        VoucherType = r.VoucherType ?? "PENERIMAAN",
        VoucherDate = r.VoucherDate ?? DateTime.UtcNow,
        PaymentType = r.PaymentType ?? "CASH",
        PersonName = r.PersonName,
        CoaCode = r.CoaCode,
        CoaName = r.CoaName,
        Amount = r.Amount,
        DiscountPercent = r.DiscountPercent,
        DiscountAmount = r.DiscountAmount,
        Pph23Enabled = r.Pph23Enabled,
        Pph23Percent = r.Pph23Percent,
        Pph23Amount = r.Pph23Amount,
        Purpose = r.Purpose,
        Notes = r.Notes,
        RecipientBankName = r.RecipientBankName,
        RecipientAccountName = r.RecipientAccountName,
        RecipientAccountNumber = r.RecipientAccountNumber,
    };
}

