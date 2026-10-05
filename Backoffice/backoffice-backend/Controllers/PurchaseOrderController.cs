using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Models;
using RSSMBackend.Entities;
using RSSMBackend.Helpers;

namespace RSSMBackend.Controllers;

[ApiController]
[Route("[controller]")]
public class PurchaseOrderController : BaseController
{
    private readonly IPurchaseOrderService _service;

    public PurchaseOrderController(IPurchaseOrderService service, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _service = service;
    }

    [HttpGet]
    [Authorize(Page.TrxPurchaseOrder, RSSMBackend.Models.Action.Read)]
    public async Task<IActionResult> GetList(
        string search = "",
        string status = "",
        string? supplierType = null,
        DateTime? startDate = null,
        DateTime? endDate = null,
        int page = 1,
        int pageSize = 10,
        // true = tampilkan HANYA PO yang sudah ditandai Paid manual (arsip, untuk Undo).
        bool markedPaid = false)
    {
        var result = await _service.GetList(search, status, supplierType, startDate, endDate, page, pageSize, markedPaid);
        return _Ok(result.Data, result.TotalRecords);
    }

    [HttpGet("Export")]
    [Authorize(Page.TrxPurchaseOrder, RSSMBackend.Models.Action.Read)]
    public async Task<IActionResult> Export(
        string search = "",
        string status = "",
        string? supplierType = null,
        DateTime? startDate = null,
        DateTime? endDate = null,
        bool markedPaid = false)
    {
        try
        {
            var result = await _service.ExportList(search, status, supplierType, startDate, endDate, markedPaid);
            return _File(result, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "Purchase_Order.xlsx");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("{orderNumber}")]
    [Authorize(Page.TrxPurchaseOrder, RSSMBackend.Models.Action.Read)]
    public async Task<IActionResult> GetByNumber(string orderNumber)
    {
        var result = await _service.GetByNumber(orderNumber);
        if (result == null) return _NotFound("Purchase Order not found");
        return _Ok(result);
    }

    [HttpPost("{orderNumber}/MarkAsPaid")]
    [Authorize(Page.TrxPurchaseOrder, RSSMBackend.Models.Action.Update)]
    public async Task<IActionResult> MarkAsPaid(string orderNumber, [FromBody] MarkAsPaidRequest request)
    {
        // Role gating: only above Staff (Administrator/Manager/Supervisor) may mark as paid.
        // Staff(4) and Petty Cash(5) are blocked. Checked synchronously here rather than via
        // [Authorize], whose OnAuthorization is async void (see AuditLogController).
        if (userSession == null || !PurchaseOrderService.MarkAsPaidRoles.Contains(userSession.RoleID))
            return Unauthorized(new { message = "Anda tidak memiliki akses untuk menandai PO sebagai Paid." });

        if (request == null || string.IsNullOrWhiteSpace(request.Note))
            return _BadRequest("Catatan (note) wajib diisi.");

        try
        {
            var settled = await _service.MarkAsPaid(orderNumber, request.Note, userSession.UserID, userSession.BranchId ?? 1);
            return _Ok(new { invoiceSettled = settled },
                settled > 0
                    ? $"Purchase Order berhasil ditandai sebagai Paid. {settled} faktur ikut dilunasi di Daftar AP / Pembayaran / Tukar Faktur."
                    : "Purchase Order berhasil ditandai sebagai Paid. Tidak ada faktur terkait yang perlu dilunasi.");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    /// <summary>
    /// Reverses Mark as Paid: the PO returns to the outstanding list and every invoice it
    /// settled goes back to Unpaid. Same role gating as MarkAsPaid.
    /// </summary>
    [HttpPost("{orderNumber}/UnmarkAsPaid")]
    [Authorize(Page.TrxPurchaseOrder, RSSMBackend.Models.Action.Update)]
    public async Task<IActionResult> UnmarkAsPaid(string orderNumber, [FromBody] MarkAsPaidRequest request)
    {
        if (userSession == null || !PurchaseOrderService.MarkAsPaidRoles.Contains(userSession.RoleID))
            return Unauthorized(new { message = "Anda tidak memiliki akses untuk membatalkan tanda Paid." });

        if (request == null || string.IsNullOrWhiteSpace(request.Note))
            return _BadRequest("Alasan pembatalan wajib diisi.");

        try
        {
            var reverted = await _service.UnmarkAsPaid(orderNumber, request.Note, userSession.UserID, userSession.BranchId ?? 1);
            return _Ok(new { invoiceReverted = reverted },
                $"Tanda Paid dibatalkan. {reverted} faktur dikembalikan ke status Unpaid.");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("DownPayment")]
    [Authorize(Page.TrxPurchaseOrder, RSSMBackend.Models.Action.Create)]
    public async Task<IActionResult> AddDownPayment([FromBody] DPRequest request)
    {
        try
        {
            var user = (User)HttpContext.Items["User"];
            await _service.AddDownPayment(request.OrderNumber, request.Amount, request.Date, request.Note, request.CoaCode, request.CoaName, user.UserID, user.BranchId ?? 1);
            return _Ok(null, "Down Payment added successfully");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }
}
