namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services.AccountReceivable;
using RSSMBackend.Helpers;
using RSSMBackend.Models;
using RSSMBackend.DTOs.AccountReceivable;

[ApiController]
[Route("[controller]")]
public class AccountReceivableController : BaseController
{
    private readonly IAccountReceivableService _accountReceivableService;
    private readonly ILogger<AccountReceivableController> _logger;
    private const string TAG = "AccountReceivable {0}";

    private string DecodeBase64UrlSafe(string encoded)
    {
        try
        {
            if (string.IsNullOrEmpty(encoded)) return encoded;
            string incoming = encoded.Replace('_', '/').Replace('-', '+');
            switch (incoming.Length % 4)
            {
                case 2: incoming += "=="; break;
                case 3: incoming += "="; break;
            }
            byte[] bytes = Convert.FromBase64String(incoming);
            return System.Text.Encoding.UTF8.GetString(bytes);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, TAG, "DecodeBase64UrlSafe Error");
            return encoded; // Return original if decode fails
        }
    }

    public AccountReceivableController(
        IAccountReceivableService accountReceivableService,
        ILogger<AccountReceivableController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _accountReceivableService = accountReceivableService;
        _logger = logger;
    }

    /// <summary>
    /// Get payer type options
    /// </summary>
    [HttpGet("PayerTypes")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetPayerTypes()
    {
        try
        {
            var data = await _accountReceivableService.GetPayerTypeOptions();
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetPayerTypes Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get list of account receivables
    /// </summary>
    [HttpGet("List")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetList(
        [FromQuery(Name = "payerType")] string? payerType = null,
        [FromQuery(Name = "paymentStatus")] string? paymentStatus = null,
        [FromQuery(Name = "serviceType")] string? serviceType = null,
        [FromQuery(Name = "payerId")] string? payerId = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "voucherNumber")] string? voucherNumber = null,
        [FromQuery(Name = "keyword")] string? keyword = null)
    {
        try
        {
            string? decodedVoucherNumber = voucherNumber;

            // If status is Final, Paid, or Vouchered, the payerId parameter might actually contain the Base64 encoded Voucher Number
            if (!string.IsNullOrEmpty(payerId) && !string.IsNullOrEmpty(paymentStatus) && 
                (paymentStatus.ToLower() == "final" || paymentStatus.ToLower() == "paid" || paymentStatus.ToLower() == "vouchered"))
            {
                // Verify if it looks like a Base64 string or just assume?
                // The frontend sends Base64 encoded voucher number in place of identifier (which maps to payerId in route, or query param?)
            }

            // Correction: The USER URL `.../payment-voucher/UFZ...` corresponds to the Detail Page.
            // The Detail Page `detail.tsx` likely calls an API to get the list of items for that voucher.
            // Let's look at `detail.tsx` again to see which API it calls.
            // It calls `useGetAccountReceivableListQuery`.
            // So we need to modify `GetList` in Controller.

            if (!string.IsNullOrEmpty(payerId) && !string.IsNullOrEmpty(paymentStatus) && 
                (paymentStatus.ToLower() == "final" || paymentStatus.ToLower() == "paid" || paymentStatus.ToLower() == "vouchered"))
            {
                 // Check if payerId (used as identifier in frontend) is actually a voucher number
                 // In `detail.tsx`: const { payerId } = useParams(); ... useGetAccountReceivableListQuery({ payerId: ... })
                 // If status is final/paid, `payerId` param from frontend is actually the base64 encoded voucher number
                 
                 // So here, `payerId` argument contains the base64 string.
                 var possibleVoucherNumber = DecodeBase64UrlSafe(payerId);
                 
                 // If it behaves like a voucher number (starts with PVR), use it
                 if (possibleVoucherNumber.StartsWith("PVR"))
                 {
                     decodedVoucherNumber = possibleVoucherNumber;
                     payerId = null; // Clear payerId because we are querying by VoucherNumber
                 }
            }

            var data = await _accountReceivableService.GetList(
                payerType, paymentStatus, serviceType, payerId, startDate, endDate, page, pageSize, decodedVoucherNumber, keyword);

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get account receivable by ID
    /// </summary>
    [HttpGet("{id}")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetById(int id)
    {
        try
        {
            var data = await _accountReceivableService.GetById(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "Data not found"));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetById Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get insurance options (contacts with category_id = 5)
    /// </summary>
    [HttpGet("InsuranceOptions")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetInsuranceOptions()
    {
        try
        {
            var data = await _accountReceivableService.GetInsuranceOptions();
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetInsuranceOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export account receivables to Excel
    /// </summary>
    [HttpGet("Export")]
    [Authorize(Page.TrxAccountReceivable, Action.Export)]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "payerType")] string? payerType = null,
        [FromQuery(Name = "paymentStatus")] string? paymentStatus = null,
        [FromQuery(Name = "serviceType")] string? serviceType = null,
        [FromQuery(Name = "payerId")] string? payerId = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "type")] string type = "xlsx",
        [FromQuery(Name = "voucherNumber")] string? voucherNumber = null)
    {
        try
        {
            var fileContent = await _accountReceivableService.Export(
                payerType, paymentStatus, serviceType, payerId, startDate, endDate, type, voucherNumber);

            string fileName = $"AccountReceivable_{DateTime.Now:yyyyMMdd}.xlsx";
            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Export Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export aging report to Excel
    /// </summary>
    [HttpGet("ExportAging")]
    [Authorize(Page.TrxAccountReceivable, Action.Export)]
    public async Task<IActionResult> ExportAging(
        [FromQuery(Name = "payerId")] string? payerId = null,
        [FromQuery(Name = "asOfDate")] DateTime? asOfDate = null,
        [FromQuery(Name = "type")] string type = "xlsx",
        [FromQuery(Name = "payerType")] string? payerType = null)
    {
        try
        {
            var fileContent = await _accountReceivableService.ExportAgingReport(payerId, asOfDate, type, payerType);
            string fileName = $"AgingReport_{DateTime.Now:yyyyMMdd}.xlsx";
            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ExportAging Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Post selected AR items (Pending -> Posting)
    /// </summary>
    [HttpPost("Post")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> PostAR([FromBody] ArIdsRequest request)
    {
        try
        {
            if (request?.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "AR IDs are required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.PostAR(request.ArIds, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "AR items have been posted"));
            else
                return _BadRequest(string.Format(TAG, "No AR items to post. They may not be in Pending status."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "PostAR Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Unpost selected AR items (Posting -> Pending, only if no voucher number)
    /// </summary>
    [HttpPost("Unpost")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> UnpostAR([FromBody] ArIdsRequest request)
    {
        try
        {
            if (request?.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "AR IDs are required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.UnpostAR(request.ArIds, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "AR items have been unposted"));
            else
                return _BadRequest(string.Format(TAG, "No AR items to unpost. They may have a voucher or not in Posting status."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "UnpostAR Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Bulk revision of status from excel (revise Posting items -> Pending or Void)
    /// </summary>
    [HttpPost("BulkRevision")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> BulkRevision(IFormFile file)
    {
        try
        {
            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.BulkRevision(file, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Bulk revision successful"));
            else
                return _BadRequest(string.Format(TAG, "Bulk revision failed or no valid items found"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "BulkRevision Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get voucher list grouped by payer
    /// </summary>
    [HttpGet("VoucherList")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetVoucherList(
        [FromQuery(Name = "payerId")] string? payerId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "voucherNumber")] string? voucherNumber = null,
        [FromQuery(Name = "payerType")] string? payerType = null)
    {
        try
        {
            var data = await _accountReceivableService.GetVoucherList(
                payerId, status, startDate, endDate, page, pageSize, voucherNumber, payerType);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetVoucherList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Finalize voucher - assign voucher number to posted AR items by payer
    /// </summary>
    [HttpPost("FinalizeVoucher")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> FinalizeVoucher([FromBody] FinalizeVoucherRequest request)
    {
        try
        {
            if (string.IsNullOrEmpty(request?.PayerId))
                return _BadRequest(string.Format(TAG, "Payer ID is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.FinalizeVoucher(
                request.PayerId, request.VoucherDate, request.DueDate, userSession.UserID, request.ArIds);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Voucher has been finalized"));
            else
                return _BadRequest(string.Format(TAG, "No AR items to finalize. They may already have a voucher number."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "FinalizeVoucher Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Cancel/Unfinalize a voucher — clears voucher number and returns items to "posting" status.
    /// Only allowed if no items in the voucher have been paid yet.
    /// </summary>
    [HttpPost("UnfinalizeVoucher")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> UnfinalizeVoucher([FromBody] UnfinalizeVoucherRequest request)
    {
        try
        {
            if (string.IsNullOrEmpty(request?.VoucherNumber))
                return _BadRequest(string.Format(TAG, "Voucher Number is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.UnfinalizeVoucher(
                request.VoucherNumber, userSession.UserID, userSession.RoleID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Voucher has been cancelled"));
            else
                return _BadRequest(string.Format(TAG, "No voucher found with that number"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "UnfinalizeVoucher Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Process settlement for AR Voucher (Payment Received) - Creates Transaction & Journals
    /// </summary>
    [HttpPost("Settlement")]
    [Authorize(Page.TrxAccountReceivable, Action.Create)]
    public async Task<IActionResult> ProcessSettlement([FromBody] SettlementRequest request)
    {
        try
        {
            if (string.IsNullOrEmpty(request?.PaymentVoucher))
                return _BadRequest(string.Format(TAG, "Payment Voucher is required"));

            if (string.IsNullOrEmpty(request.CoaCode))
                return _BadRequest(string.Format(TAG, "Bank/Cash account (COA) is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.ProcessSettlement(request, userSession.UserID);

            if (result.UpdatedCount > 0)
                return _Ok(new { success = true, result }, string.Format(TAG, "Settlement processed successfully"));
            else
                return _BadRequest(string.Format(TAG, "No items to settle or already paid."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ProcessSettlement Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Process payment for selected receivables
    /// </summary>
    [HttpPost("Payment")]
    [Authorize(Page.TrxAccountReceivable, Action.Create)]
    public async Task<IActionResult> ProcessPayment([FromBody] AccountReceivablePaymentRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if ((request.ArIds == null || request.ArIds.Count == 0) && string.IsNullOrEmpty(request.PayerId))
                return _BadRequest(string.Format(TAG, "Either AR IDs or Payer ID is required"));

            if (string.IsNullOrEmpty(request.PaymentCoaCode))
                return _BadRequest(string.Format(TAG, "Bank/Cash account (COA) is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.ProcessPayment(
                request.ArIds, request.PayerId, request.PaymentDate, request.PaymentCoaCode, request.PaymentRef ?? "", userSession.UserID);

            if (result)
            {
                return _Ok(new { success = true }, 
                    string.Format(TAG, $"Payment has been successfully processed"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No receivables to process. They may already be paid."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ProcessPayment Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get aging report grouped by payer
    /// </summary>
    [HttpGet("AgingReport")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetAgingReport(
        [FromQuery(Name = "payerId")] string? payerId = null,
        [FromQuery(Name = "asOfDate")] DateTime? asOfDate = null,
        [FromQuery(Name = "payerType")] string? payerType = null)
    {
        try
        {
            var data = await _accountReceivableService.GetAgingReport(payerId, asOfDate, payerType);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetAgingReport Error");
            return _BadRequest(e.Message);
        }
    }


    /// <summary>
    /// Import AR from Excel
    /// </summary>
    [HttpPost("Import")]
    [Authorize(Page.TrxAccountReceivable, Action.Create)]
    public async Task<IActionResult> Import([FromForm] IFormFile file)
    {
        try
        {
            if (file == null || file.Length == 0)
                return _BadRequest(string.Format(TAG, "File is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.ImportAR(file, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "AR data imported successfully"));
            else
                return _BadRequest(string.Format(TAG, "Failed to import AR data. Check format or empty file."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Import Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Update AR Status via Excel (Bulk Post)
    /// </summary>
    [HttpPost("BulkPost")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> BulkPost([FromForm] IFormFile file)
    {
        try
        {
            if (file == null || file.Length == 0)
                return _BadRequest(string.Format(TAG, "File is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.BulkUpdateStatus(file, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "AR items updated successfully"));
            else
                return _BadRequest(string.Format(TAG, "No items updated. Check file format or valid IDs."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "BulkPost Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Void AR items
    /// </summary>
    [HttpPost("Void")]
    [Authorize(Page.TrxAccountReceivable, Action.Delete)]
    public async Task<IActionResult> Void([FromBody] ArIdsRequest request)
    {
        try
        {
            if (request?.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "AR IDs are required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.VoidAR(request.ArIds, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "AR items have been voided"));
            else
                return _BadRequest(string.Format(TAG, "No AR items to void. Check if they exist or are already Paid."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Void Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Mark AR items as Printed
    /// </summary>
    [HttpPost("Print")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> Print([FromBody] ArIdsRequest request)
    {
        try
        {
            if (request?.ArIds == null || request.ArIds.Count == 0)
                return _BadRequest(string.Format(TAG, "AR IDs are required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.MarkAsPrinted(request.ArIds, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "AR items have been marked as printed"));
            else
                return _BadRequest(string.Format(TAG, "No AR items to process."));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "Print Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Upload adjustment file to update AR approved amounts (all payer types)
    /// </summary>
    [HttpPost("Upload/Adjustment")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> UploadAdjustment(IFormFile file)
    {
        try
        {
            if (file == null || file.Length == 0)
                return _BadRequest(string.Format(TAG, "File is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var allowedExtensions = new[] { ".txt", ".csv", ".xlsx", ".xls" };
            var extension = Path.GetExtension(file.FileName).ToLower();
            if (!allowedExtensions.Contains(extension))
                return _BadRequest(string.Format(TAG, "Invalid file format. Allowed: .xlsx, .xls, .txt, .csv"));

            using var stream = file.OpenReadStream();
            var result = await _accountReceivableService.ImportAdjustment(stream, file.FileName, userSession.UserID);

            return _Ok(result, string.Format(TAG, $"Adjustment imported: {result.UpdatedCount} updated, {result.SkippedCount} skipped"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "UploadAdjustment Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get payer options (contacts with category_id = 5)
    /// </summary>
    [HttpGet("PayerOptions")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetPayerOptions([FromQuery(Name = "keyword")] string? keyword = null)
    {
        try
        {
            var data = await _accountReceivableService.GetPayerOptions(keyword);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetPayerOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Change payer of an AR item
    /// </summary>
    [HttpPost("ChangePayer")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> ChangePayer([FromBody] ChangePayerRequest request)
    {
        try
        {
            if (request == null || request.ArId <= 0)
                return _BadRequest(string.Format(TAG, "AR ID is required"));

            if (string.IsNullOrEmpty(request.NewPayerId))
                return _BadRequest(string.Format(TAG, "New Payer is required"));

            if (string.IsNullOrEmpty(request.Reason))
                return _BadRequest(string.Format(TAG, "Reason is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.ChangePayer(
                request.ArId, request.NewPayerId, request.NewPayerName ?? "", request.Reason, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Payer has been changed successfully"));
            else
                return _BadRequest(string.Format(TAG, "Failed to change payer"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ChangePayer Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Adjust an AR item's approved amount (single row) and store history
    /// </summary>
    [HttpPost("Adjust")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> AdjustAr([FromBody] AdjustArRequest request)
    {
        try
        {
            if (request == null || request.ArId <= 0)
                return _BadRequest(string.Format(TAG, "AR ID is required"));

            if (request.AdjustmentAmount <= 0)
                return _BadRequest(string.Format(TAG, "Adjustment amount must be greater than 0"));

            if (string.IsNullOrEmpty(request.Reason))
                return _BadRequest(string.Format(TAG, "Reason is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.AdjustAr(
                request.ArId, request.AdjustmentAmount, request.Reason, userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Adjustment has been saved successfully"));
            else
                return _BadRequest(string.Format(TAG, "Failed to save adjustment"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "AdjustAr Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Add a free-text note to an AR item (single row) and store history
    /// </summary>
    [HttpPost("AddNote")]
    [Authorize(Page.TrxAccountReceivable, Action.Update)]
    public async Task<IActionResult> AddNote([FromBody] AddNoteRequest request)
    {
        try
        {
            if (request == null || request.ArId <= 0)
                return _BadRequest(string.Format(TAG, "AR ID is required"));

            if (string.IsNullOrWhiteSpace(request.Note))
                return _BadRequest(string.Format(TAG, "Note is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _accountReceivableService.AddNote(
                request.ArId, request.Note.Trim(), userSession.UserID);

            if (result)
                return _Ok(new { success = true }, string.Format(TAG, "Note has been saved successfully"));
            else
                return _BadRequest(string.Format(TAG, "Failed to save note"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "AddNote Error");
            return _BadRequest(e.Message);
        }
    }

    [HttpGet("PrintConfig")]
    [Authorize(Page.TrxAccountReceivable, Action.Read)]
    public async Task<IActionResult> GetPrintConfig()
    {
        try
        {
            var config = await _accountReceivableService.GetPrintConfig(userSession.BranchId ?? 1);
            return _Ok(config);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetPrintConfig Error");
            return _BadRequest(e.Message);
        }
    }
}

