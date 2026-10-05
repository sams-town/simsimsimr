namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Services.PettyCash;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class PettyCashController : BaseController
{
    private readonly IPettyCashService _pettyCashService;

    public PettyCashController(
        IPettyCashService pettyCashService,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _pettyCashService = pettyCashService;
    }

    #region Dashboard

    [HttpGet("Dashboard")]
    [Authorize(Page.TrxPettyCash, Action.Read)]
    public async Task<IActionResult> GetDashboard()
    {
        try
        {
            var result = await _pettyCashService.GetDashboard(userSession.BranchId ?? 1, userSession.RoleID, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    #endregion

    #region Unit Management

    [HttpGet("Unit")]
    [Authorize(Page.TrxPettyCashUnit, Action.Read)]
    public async Task<IActionResult> GetUnits(
        [FromQuery] string? keyword,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var result = await _pettyCashService.GetUnits(userSession.BranchId ?? 1, keyword, page, pageSize);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Unit/Options")]
    [Authorize(Page.TrxPettyCashUnit, Action.Read)]
    public async Task<IActionResult> GetUnitOptions()
    {
        try
        {
            var result = await _pettyCashService.GetUnitOptions(userSession.BranchId ?? 1);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Unit/MyUnit")]
    [Authorize(Page.TrxPettyCashRequest, Action.Read)]
    public async Task<IActionResult> GetMyUnit()
    {
        try
        {
            var result = await _pettyCashService.GetMyUnit(userSession.UserID);
            if (result == null) return _NotFound("Anda tidak memiliki akses ke Unit Kas Kecil manapun.");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Unit/{id}")]
    [Authorize(Page.TrxPettyCashUnit, Action.Read)]
    public async Task<IActionResult> GetUnitById(int id)
    {
        try
        {
            var result = await _pettyCashService.GetUnitById(id);
            if (result == null) return _NotFound("Unit not found");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Unit")]
    [Authorize(Page.TrxPettyCashUnit, Action.Create)]
    public async Task<IActionResult> AddUnit([FromBody] UnitModel model)
    {
        try
        {
            model.BranchId = userSession.BranchId ?? 1;
            var result = await _pettyCashService.AddUnit(model, userSession.UserID);
            return _Ok(new { unitId = result, message = "Unit created successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPut("Unit")]
    [Authorize(Page.TrxPettyCashUnit, Action.Update)]
    public async Task<IActionResult> UpdateUnit([FromBody] UnitModel model)
    {
        try
        {
            var result = await _pettyCashService.UpdateUnit(model, userSession.UserID);
            if (!result) return _NotFound("Unit not found");
            return _Ok(new { message = "Unit updated successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpDelete("Unit/{id}")]
    [Authorize(Page.TrxPettyCashUnit, Action.Delete)]
    public async Task<IActionResult> DeleteUnit(int id)
    {
        try
        {
            if (userSession == null || userSession.UserID <= 0) return _BadRequest("Invalid user session");
            var result = await _pettyCashService.DeleteUnit(id, userSession.UserID);
            if (!result) return _NotFound("Unit not found");
            return _Ok(new { message = "Unit deleted successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    #endregion

    #region Request

    [HttpGet("Request")]
    [Authorize(Page.TrxPettyCashRequest, Action.Read)]
    public async Task<IActionResult> GetRequests(
        [FromQuery] int? unitId,
        [FromQuery] string? status,
        [FromQuery] string? settlementStatus,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] string? keyword,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var result = await _pettyCashService.GetRequests(userSession.BranchId ?? 1, unitId, status, settlementStatus, startDate, endDate, keyword, page, pageSize, userSession.RoleID, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Request/{id}")]
    [Authorize(Page.TrxPettyCashRequest, Action.Read)]
    public async Task<IActionResult> GetRequestById(int id)
    {
        try
        {
            var result = await _pettyCashService.GetRequestById(id);
            if (result == null) return _NotFound("Request not found");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Request")]
    [Authorize(Page.TrxPettyCashRequest, Action.Create)]
    public async Task<IActionResult> AddRequest([FromBody] PettyCashRequest request)
    {
        try
        {
            request.BranchId = userSession.BranchId ?? 1;
            var result = await _pettyCashService.AddRequest(request, userSession.UserID);
            return _Ok(new { requestId = result, message = "Request created successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPut("Request")]
    [Authorize(Page.TrxPettyCashRequest, Action.Update)]
    public async Task<IActionResult> UpdateRequest([FromBody] PettyCashRequest request)
    {
        try
        {
            var result = await _pettyCashService.UpdateRequest(request, userSession.UserID);
            if (!result) return _BadRequest("Request not found or cannot be updated");
            return _Ok(new { message = "Request updated successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpDelete("Request/{id}")]
    [Authorize(Page.TrxPettyCashRequest, Action.Delete)]
    public async Task<IActionResult> DeleteRequest(int id)
    {
        try
        {
            if (userSession == null || userSession.UserID <= 0) return _BadRequest("Invalid user session");
            var result = await _pettyCashService.DeleteRequest(id, userSession.UserID);
            if (!result) return _BadRequest("Request not found or cannot be deleted");
            return _Ok(new { message = "Request deleted successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Request/{id}/Submit")]
    [Authorize(Page.TrxPettyCashRequest, Action.Update)]
    public async Task<IActionResult> SubmitRequest(int id)
    {
        try
        {
            var result = await _pettyCashService.SubmitRequest(id, userSession.UserID);
            if (!result) return _BadRequest("Request not found or cannot be submitted");
            return _Ok(new { message = "Request submitted for approval" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Request/{id}/Approve")]
    [Authorize(Page.TrxPettyCashRequest, Action.Update)]
    public async Task<IActionResult> ApproveRequest(int id, [FromBody] ApproveRequestModel model)
    {
        try
        {
            var result = await _pettyCashService.ApproveRequest(id, model.ApprovedAmount, userSession.UserID);
            if (!result) return _BadRequest("Request not found or cannot be approved");
            return _Ok(new { message = "Request approved successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Request/{id}/Reject")]
    [Authorize(Page.TrxPettyCashRequest, Action.Update)]
    public async Task<IActionResult> RejectRequest(int id, [FromBody] RejectRequestModel model)
    {
        try
        {
            var result = await _pettyCashService.RejectRequest(id, model.Reason, userSession.UserID);
            if (!result) return _BadRequest("Request not found or cannot be rejected");
            return _Ok(new { message = "Request rejected successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Request/{id}/Receipt")]
    [Authorize(Page.TrxPettyCashRequest, Action.Read)]
    public async Task<IActionResult> GetRequestReceipt(int id)
    {
        try
        {
            var result = await _pettyCashService.GetRequestReceipt(id);
            if (result == null) return _NotFound("Request not found");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Request/Export")]
    [Authorize(Page.TrxPettyCashRequest, Action.Export)]
    public async Task<IActionResult> ExportRequests(
        [FromQuery] int? unitId,
        [FromQuery] string? status,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var result = await _pettyCashService.ExportRequests(userSession.BranchId ?? 1, unitId, status, startDate, endDate, userSession.RoleID, userSession.UserID);
            return _File(result, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "PettyCash_Requests.xlsx");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    #endregion

    #region Settlement

    [HttpGet("Settlement")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Read)]
    public async Task<IActionResult> GetSettlements(
        [FromQuery] int? unitId,
        [FromQuery] string? status,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var result = await _pettyCashService.GetSettlements(userSession.BranchId ?? 1, unitId, status, startDate, endDate, page, pageSize, userSession.RoleID, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Settlement/{id}")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Read)]
    public async Task<IActionResult> GetSettlementById(int id)
    {
        try
        {
            var result = await _pettyCashService.GetSettlementById(id);
            if (result == null) return _NotFound("Settlement not found");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    /// <summary>Riwayat percobaan settlement sebuah request: settle, batal, settle lagi.</summary>
    [HttpGet("Settlement/History/{requestId}")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Read)]
    public async Task<IActionResult> GetSettlementHistory(int requestId)
    {
        try
        {
            var result = await _pettyCashService.GetSettlementHistoryByRequest(requestId);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Settlement")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Create)]
    public async Task<IActionResult> CreateSettlement([FromBody] CreateSettlementModel model)
    {
        try
        {
            var settlement = new PettyCashSettlement
            {
                SettlementDate = model.SettlementDate,
                UnitId = model.UnitId,
                RequestId = model.RequestId, // RequestId now in header
                ReturnAmount = model.ReturnAmount,
                Notes = model.Notes
            };

            var details = model.Details.Select(d => new PettyCashSettlementDetail
            {
                ExpenseCoaCode = d.ExpenseCoaCode,
                ExpenseCoaName = d.ExpenseCoaName,
                Description = d.Description,
                Amount = d.Amount,
                ReceiptNo = d.ReceiptNo,
                ReceiptDate = d.ReceiptDate
            }).ToList();

            var result = await _pettyCashService.CreateSettlement(settlement, details, userSession.BranchId ?? 1, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Settlement/{id}/Post")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Update)]
    public async Task<IActionResult> PostSettlement(int id)
    {
        try
        {
            var result = await _pettyCashService.PostSettlement(id, userSession.BranchId ?? 1, userSession.UserID);
            if (!result) return _BadRequest("Settlement not found or cannot be posted");
            return _Ok(new { message = "Settlement posted and journal created successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("Settlement/{id}/Void")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Delete)]
    public async Task<IActionResult> VoidSettlement(int id)
    {
        try
        {
            var result = await _pettyCashService.VoidSettlement(id, userSession.UserID);
            if (!result) return _BadRequest("Settlement not found or cannot be voided");
            return _Ok(new { message = "Settlement voided successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    /// <summary>
    /// Membatalkan settlement yang sudah diposting: membuat jurnal pembalik dan
    /// mengembalikan request agar bisa di-settle ulang. Tidak menghapus data.
    /// </summary>
    [HttpPost("Settlement/{id}/Cancel")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Delete)]
    public async Task<IActionResult> CancelSettlement(int id, [FromBody] CancelSettlementModel? model)
    {
        try
        {
            var result = await _pettyCashService.CancelSettlement(
                id, model?.Reason, userSession.RoleID, userSession.BranchId ?? 1, userSession.UserID);
            return _Ok(result, "Settlement dibatalkan dan jurnal pembalik dibuat");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Settlement/Export")]
    [Authorize(Page.TrxPettyCashSettlement, Action.Export)]
    public async Task<IActionResult> ExportSettlements(
        [FromQuery] int? unitId,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var result = await _pettyCashService.ExportSettlements(userSession.BranchId ?? 1, unitId, null, startDate, endDate, -1, 0, userSession.RoleID, userSession.UserID);
            return _File(result, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "PettyCash_Settlements.xlsx");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    #endregion

    #region TopUp

    [HttpGet("TopUp")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Read)]
    public async Task<IActionResult> GetTopUps(
        [FromQuery] int? unitId,
        [FromQuery] string? status,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var result = await _pettyCashService.GetTopUps(userSession.BranchId ?? 1, unitId, status, startDate, endDate, page, pageSize, userSession.RoleID, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("TopUp/{id}")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Read)]
    public async Task<IActionResult> GetTopUpById(int id)
    {
        try
        {
            var result = await _pettyCashService.GetTopUpById(id);
            if (result == null) return _NotFound("Top up not found");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("TopUp/DefaultCOA")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Read)]
    public async Task<IActionResult> GetTopUpDefaultCOA()
    {
        try
        {
            var result = await _pettyCashService.GetTopUpDefaultCOA(userSession.BranchId ?? 1);
            return _Ok(new { coaCode = result });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("TopUp")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Create)]
    public async Task<IActionResult> AddTopUp([FromBody] PettyCashTopUp topUp)
    {
        try
        {
            topUp.BranchId = userSession.BranchId ?? 1;
            var result = await _pettyCashService.AddTopUp(topUp, userSession.UserID, userSession.RoleID);
            return _Ok(new { topUpId = result, message = "Top up request created successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("TopUp/{id}/Approve")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Approve)]
    public async Task<IActionResult> ApproveTopUp(int id, [FromBody] ApproveRequestModel model)
    {
        try
        {
            var result = await _pettyCashService.ApproveTopUp(id, model.ApprovedAmount, model.Notes, userSession.BranchId ?? 1, userSession.UserID);
            if (!result) return _BadRequest("Top up not found or cannot be approved");
            return _Ok(new { message = "Top up approved and journal created successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("TopUp/{id}/Reject")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Reject)]
    public async Task<IActionResult> RejectTopUp(int id, [FromBody] RejectRequestModel model)
    {
        try
        {
            var result = await _pettyCashService.RejectTopUp(id, model.Reason, userSession.UserID);
            if (!result) return _BadRequest("Top up not found or cannot be rejected");
            return _Ok(new { message = "Top up rejected successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpPost("TopUp/{id}/Void")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Void)]
    public async Task<IActionResult> VoidTopUp(int id)
    {
        try
        {
            var result = await _pettyCashService.VoidTopUp(id, userSession.UserID);
            if (!result) return _BadRequest("Top up not found or cannot be voided");
            return _Ok(new { message = "Top up voided successfully" });
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("TopUp/{id}/Receipt")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Read)]
    public async Task<IActionResult> GetTopUpReceipt(int id)
    {
        try
        {
            var result = await _pettyCashService.GetTopUpReceipt(id);
            if (result == null) return _NotFound("Top up not found");
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("TopUp/Export")]
    [Authorize(Page.TrxPettyCashTopUp, Action.Export)]
    public async Task<IActionResult> ExportTopUps(
        [FromQuery] int? unitId,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var result = await _pettyCashService.ExportTopUps(userSession.BranchId ?? 1, unitId, null, startDate, endDate, -1, 0, userSession.RoleID, userSession.UserID);
            return _File(result, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "PettyCash_TopUps.xlsx");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    #endregion

    #region Ledger

    [HttpGet("Ledger/{unitId}")]
    [Authorize(Page.TrxPettyCash, Action.Read)]
    public async Task<IActionResult> GetLedger(
        int unitId,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var result = await _pettyCashService.GetLedger(unitId, startDate, endDate, page, pageSize, userSession.RoleID, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    [HttpGet("Ledger/{unitId}/Export")]
    [Authorize(Page.TrxPettyCash, Action.Read)]
    public async Task<IActionResult> ExportLedger(
        int unitId,
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var result = await _pettyCashService.ExportLedger(unitId, startDate, endDate, userSession.RoleID, userSession.UserID);
            return _File(result, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "PettyCash_Ledger.xlsx");
        }
        catch (Exception ex)
        {
            return _BadRequest(ex.Message);
        }
    }

    #endregion
}


