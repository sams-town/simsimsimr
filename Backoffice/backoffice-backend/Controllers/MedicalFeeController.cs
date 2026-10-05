namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Models;
using RSSMBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class MedicalFeeController : BaseController
{
    private readonly IMedicalFeeService _medicalFeeService;
    private readonly ILogger<MedicalFeeController> _logger;
    private const string TAG = "MedicalFee {0}";

    public MedicalFeeController(
        IMedicalFeeService medicalFeeService,
        ILogger<MedicalFeeController> logger,
        IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _medicalFeeService = medicalFeeService;
        _logger = logger;
    }

    /// <summary>
    /// Get list of medical fees (jasa medis) with extended filters
    /// </summary>
    [HttpGet("List")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetList(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "doctorName")] string? doctorName = null,
        [FromQuery(Name = "doctorId")] string? doctorId = null,
        [FromQuery(Name = "patientType")] string? patientType = null,
        [FromQuery(Name = "payerCode")] string? payerCode = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "periodMonth")] int? periodMonth = null,
        [FromQuery(Name = "periodYear")] int? periodYear = null,
        [FromQuery(Name = "procedureDateFrom")] DateTime? procedureDateFrom = null,
        [FromQuery(Name = "procedureDateTo")] DateTime? procedureDateTo = null,
        [FromQuery(Name = "medicalProcedure")] string? medicalProcedure = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "doctorname",
        [FromQuery(Name = "orderType")] string? orderType = "asc")
    {
        try
        {
            var data = await _medicalFeeService.GetList(
                keyword, doctorName, doctorId, patientType, payerCode, status, 
                periodMonth, periodYear, procedureDateFrom, procedureDateTo, medicalProcedure,
                page, pageSize, orderBy, orderType);

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export medical fees to Excel
    /// </summary>
    [HttpGet("Export")]
    [Authorize(Page.TrxMedicalFee, Action.Export)]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "doctorName")] string? doctorName = null,
        [FromQuery(Name = "doctorId")] string? doctorId = null,
        [FromQuery(Name = "patientType")] string? patientType = null,
        [FromQuery(Name = "payerCode")] string? payerCode = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "periodMonth")] int? periodMonth = null,
        [FromQuery(Name = "periodYear")] int? periodYear = null,
        [FromQuery(Name = "procedureDateFrom")] DateTime? procedureDateFrom = null,
        [FromQuery(Name = "procedureDateTo")] DateTime? procedureDateTo = null,
        [FromQuery(Name = "medicalProcedure")] string? medicalProcedure = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "doctorname",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "type")] string type = "xlsx")
    {
        try
        {
            var data = await _medicalFeeService.GetList(
                keyword, doctorName, doctorId, patientType, payerCode, status, 
                periodMonth, periodYear, procedureDateFrom, procedureDateTo, medicalProcedure,
                -1, -1, orderBy, orderType);

            if (data == null || data.Data == null || data.Data.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            byte[] fileContent;
            string fileName = $"MedicalFee_{DateTime.Now:yyyyMMdd}.";

            if (type.ToLower() == "xlsx")
            {
                fileContent = ExportToDocs.ConvertMedicalFeeToExcel(data.Data);
                fileName += "xlsx";
            }
            else
            {
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

    /// <summary>
    /// Process payment for medical fees (legacy)
    /// </summary>
    [HttpPost("Payment")]
    [Authorize(Page.TrxMedicalFee, Action.Create)]
    public async Task<IActionResult> ProcessPayment([FromBody] MedicalFeePaymentRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (request.FeeIds == null || request.FeeIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Fee IDs are required"));

            if (string.IsNullOrEmpty(request.CoaCode))
                return _BadRequest(string.Format(TAG, "Bank/Cash account (COA) is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _medicalFeeService.ProcessPayment(request, userSession.UserID);

            if (result > 0)
            {
                return _Ok(new { updatedCount = result }, 
                    string.Format(TAG, $"{result} medical fee(s) payment has been successfully processed"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No fees to process. They may already be paid."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ProcessPayment Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ POSTING ============

    /// <summary>
    /// Batch posting: change status from Pending to Posting, assign batch number
    /// </summary>
    [HttpPost("Posting")]
    [Authorize(Page.TrxMedicalFee, Action.Update)]
    public async Task<IActionResult> PostingBatch([FromBody] BatchPostingRequest request)
    {
        try
        {
            if (request == null || request.FeeIds == null || request.FeeIds.Count == 0)
                return _BadRequest(string.Format(TAG, "Fee IDs are required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _medicalFeeService.PostingBatch(request, userSession.UserID);

            if (result.PostedCount > 0)
            {
                return _Ok(result, 
                    string.Format(TAG, $"{result.PostedCount} item(s) posted with batch {result.BatchNumber}"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No pending items to post."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "PostingBatch Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Cancel posting: change status from Posting to Pending, clear batch number
    /// </summary>
    [HttpPost("Posting/Cancel/{id}")]
    [Authorize(Page.TrxMedicalFee, Action.Update)]
    public async Task<IActionResult> CancelPosting(int id)
    {
        try
        {
            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _medicalFeeService.CancelPosting(id, userSession.UserID);

            if (result)
            {
                return _Ok(new { success = true }, string.Format(TAG, "Posting has been cancelled successfully"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "Failed to cancel posting. Item may not be in 'Posting' status or not found."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "CancelPosting Error");
            return _BadRequest(e.Message);
        }
    }

    [HttpPost("Posting/CancelBatch")]
    [Authorize(Page.TrxMedicalFee, Action.Update)]
    public async Task<IActionResult> CancelPostingBatch([FromBody] BatchPostingRequest request)
    {
        try
        {
            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var count = await _medicalFeeService.CancelPostingBatch(request.FeeIds, userSession.UserID);

            return _Ok(new { success = true, count }, string.Format(TAG, $"{count} items cancelled successfully"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "CancelPostingBatch Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ PAYMENT VOUCHER ============

    /// <summary>
    /// Get voucher list grouped by doctor (status = Posting)
    /// </summary>
    [HttpGet("Voucher/List")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetVoucherList(
        [FromQuery(Name = "doctorCode")] string? doctorCode = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await _medicalFeeService.GetVoucherListGroupByDoctor(doctorCode, status, page, pageSize);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetVoucherList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get voucher detail for a specific doctor (status = Posting)
    /// </summary>
    [HttpGet("Voucher/Detail/{doctorCode}")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetVoucherDetail(string doctorCode, [FromQuery] string? status = null)
    {
        try
        {
            // For paid status, the doctorCode parameter contains base64-encoded paymentVoucher
            var identifier = doctorCode;
            if (!string.IsNullOrEmpty(status) && (status.ToLower() == "paid" || status.ToLower() == "final"))
            {
                identifier = DecodeBase64UrlSafe(doctorCode);
            }
            
            var data = await _medicalFeeService.GetVoucherDetail(identifier, status);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetVoucherDetail Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Generate voucher: change status from Posting to Unpaid, assign voucher number and doctor share
    /// </summary>
    [HttpPost("Voucher/Generate")]
    [Authorize(Page.TrxMedicalFee, Action.Update)]
    public async Task<IActionResult> GenerateVoucher([FromBody] GenerateVoucherRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (request.DoctorId < 0)
                return _BadRequest(string.Format(TAG, "Doctor ID is invalid"));

            if (request.DoctorSharePercent < 0 || request.DoctorSharePercent > 100)
                return _BadRequest(string.Format(TAG, "Doctor share must be between 0 and 100"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _medicalFeeService.GenerateVoucher(request, userSession.UserID);

            if (result.UpdatedCount > 0)
            {
                return _Ok(result, 
                    string.Format(TAG, $"Voucher {result.PaymentVoucher} created for {result.UpdatedCount} item(s)"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No posting items found for this doctor."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GenerateVoucher Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ SETTLEMENT ============

    /// <summary>
    /// Get settlement list grouped by doctor and voucher (status = Unpaid)
    /// </summary>
    [HttpGet("Settlement/List")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetSettlementList(
        [FromQuery(Name = "doctorId")] string? doctorCode = null,
        [FromQuery(Name = "paymentVoucher")] string? paymentVoucher = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "voucherDateFrom")] DateTime? voucherDateFrom = null,
        [FromQuery(Name = "voucherDateTo")] DateTime? voucherDateTo = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await _medicalFeeService.GetSettlementList(doctorCode, paymentVoucher, status, voucherDateFrom, voucherDateTo, page, pageSize);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetSettlementList Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get settlement detail for a specific voucher (status = Unpaid)
    /// </summary>
    [HttpGet("Settlement/Detail/{paymentVoucher}")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetSettlementDetail(string paymentVoucher)
    {
        try
        {
            // Decode base64 URL-safe voucher ID
            var decodedVoucher = DecodeBase64UrlSafe(paymentVoucher);
            var data = await _medicalFeeService.GetSettlementDetail(decodedVoucher);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetSettlementDetail Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get salary slip data for printing
    /// </summary>
    [HttpGet("Settlement/{paymentVoucher}/Print")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetSalarySlip(string paymentVoucher)
    {
        try
        {
            var decodedVoucher = DecodeBase64UrlSafe(paymentVoucher);
            var printedBy = userSession?.DisplayName ?? "Unknown";
            var data = await _medicalFeeService.GetSalarySlip(decodedVoucher, printedBy, userSession?.BranchId ?? 1);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetSalarySlip Error");
            return _BadRequest(e.Message);
        }
    }

    private static string DecodeBase64UrlSafe(string input)
    {
        try
        {
            // Convert URL-safe base64 to standard base64
            string base64 = input.Replace('-', '+').Replace('_', '/');
            // Add padding if needed
            switch (base64.Length % 4)
            {
                case 2: base64 += "=="; break;
                case 3: base64 += "="; break;
            }
            var bytes = Convert.FromBase64String(base64);
            return System.Text.Encoding.UTF8.GetString(bytes);
        }
        catch
        {
            return input; // Return as-is if decoding fails
        }
    }

    /// <summary>
    /// Process settlement: change status from Unpaid to Paid, record payment info
    /// </summary>
    [HttpPost("Settlement")]
    [Authorize(Page.TrxMedicalFee, Action.Create)]
    public async Task<IActionResult> ProcessSettlement([FromBody] SettlementRequest request)
    {
        try
        {
            if (request == null)
                return _BadRequest(string.Format(TAG, "Request body is required"));

            if (string.IsNullOrEmpty(request.PaymentVoucher))
                return _BadRequest(string.Format(TAG, "Payment voucher is required"));

            if (string.IsNullOrEmpty(request.CoaCode))
                return _BadRequest(string.Format(TAG, "Bank/Cash account (COA) is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            // Decode base64 URL-safe voucher ID
            request.PaymentVoucher = DecodeBase64UrlSafe(request.PaymentVoucher);

            var result = await _medicalFeeService.ProcessSettlement(request, userSession.UserID);

            if (result.UpdatedCount > 0)
            {
                return _Ok(result, 
                    string.Format(TAG, $"{result.UpdatedCount} item(s) settled. Total: {result.TotalPaid:N0}"));
            }
            else
            {
                return _BadRequest(string.Format(TAG, "No unpaid items found for this voucher."));
            }
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ProcessSettlement Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ LOOKUP ============

    /// <summary>
    /// Get doctor options for dropdown
    /// </summary>
    [HttpGet("Lookup/Doctors")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetDoctorOptions([FromQuery(Name = "keyword")] string? keyword = null)
    {
        try
        {
            var data = await _medicalFeeService.GetDoctorOptions(keyword);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetDoctorOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get payer options for dropdown (from Contact category 5)
    /// </summary>
    [HttpGet("Lookup/Payers")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetPayerOptions([FromQuery(Name = "keyword")] string? keyword = null)
    {
        try
        {
            var data = await _medicalFeeService.GetPayerOptions(keyword);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetPayerOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get patient options for dropdown (from Contact category Patient)
    /// </summary>
    [HttpGet("Lookup/Patients")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetPatientOptions([FromQuery] string? keyword)
    {
        try
        {
            var data = await _medicalFeeService.GetPatientOptions(keyword);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetPatientOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get visit_no options for a patient by medical record number
    /// </summary>
    [HttpGet("Lookup/VisitNos")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetVisitNoOptions([FromQuery] string medicalRecordNo)
    {
        try
        {
            var data = await _medicalFeeService.GetVisitNoOptions(medicalRecordNo);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetVisitNoOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get SEP number options for a patient by medical record number
    /// </summary>
    [HttpGet("Lookup/SepNos")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetSepNoOptions([FromQuery] string medicalRecordNo)
    {
        try
        {
            var data = await _medicalFeeService.GetSepNoOptions(medicalRecordNo);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetSepNoOptions Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get payment method options for dropdown (from MedicalFee table - distinct PayerCode/PayerName)
    /// </summary>
    [HttpGet("Lookup/PaymentMethods")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public async Task<IActionResult> GetPaymentMethodOptions([FromQuery(Name = "keyword")] string? keyword = null)
    {
        try
        {
            var data = await _medicalFeeService.GetPaymentMethodOptions(keyword);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "GetPaymentMethodOptions Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ Settlement Export ============

    /// <summary>
    /// Export settlement data to Excel
    /// </summary>
    [HttpGet("Settlement/Export")]
    [Authorize(Page.TrxMedicalFee, Action.Export)]
    public async Task<IActionResult> ExportSettlement(
        [FromQuery(Name = "doctorId")] string? doctorCode = null,
        [FromQuery(Name = "paymentVoucher")] string? paymentVoucher = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "voucherDateFrom")] DateTime? voucherDateFrom = null,
        [FromQuery(Name = "voucherDateTo")] DateTime? voucherDateTo = null,
        [FromQuery(Name = "type")] string type = "xlsx")
    {
        try
        {
            var data = await _medicalFeeService.GetSettlementExport(doctorCode, paymentVoucher, status, voucherDateFrom, voucherDateTo);

            if (data == null || data.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            byte[] fileContent;
            string fileName = $"Settlement_{DateTime.Now:yyyyMMdd}.";

            if (type.ToLower() == "xlsx")
            {
                fileContent = Helpers.ExportToDocs.ConvertSettlementToExcel(data);
                fileName += "xlsx";
            }
            else
            {
                fileContent = Helpers.ExportToDocs.ConvertToCsv(data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ExportSettlement Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export settlement data for bank upload (Maker)
    /// </summary>
    [HttpGet("Settlement/ExportMaker")]
    [Authorize(Page.TrxMedicalFee, Action.Export)]
    public async Task<IActionResult> ExportSettlementMaker(
        [FromQuery(Name = "doctorId")] string? doctorCode = null,
        [FromQuery(Name = "paymentVoucher")] string? paymentVoucher = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "voucherDateFrom")] DateTime? voucherDateFrom = null,
        [FromQuery(Name = "voucherDateTo")] DateTime? voucherDateTo = null)
    {
        try
        {
            var data = await _medicalFeeService.GetSettlementMakerExport(doctorCode, paymentVoucher, status, voucherDateFrom, voucherDateTo);

            if (data == null || data.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            string fileName = $"Settlement_Maker_{DateTime.Now:yyyyMMdd}.xlsx";
            byte[] fileContent = Helpers.ExportToDocs.ConvertSettlementMakerToExcel(data);

            return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ExportSettlementMaker Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export Voucher Detail (Rincian Jasa Medis) to Excel
    /// </summary>
    [HttpGet("VoucherDetail/Export")]
    [Authorize(Page.TrxMedicalFee, Action.Export)]
    public async Task<IActionResult> ExportVoucherDetail(
        [FromQuery(Name = "doctorCode")] string doctorCode,
        [FromQuery(Name = "status")] string status = "")
    {
        try
        {
            var data = await _medicalFeeService.GetVoucherDetailExport(doctorCode, status);

            if (data == null || data.Items == null || data.Items.Count == 0)
            {
                return _NotFound(string.Format(TAG, "No data to export"));
            }

            string doctorName = data.DoctorName ?? "Doctor";
            string fileName = $"Rincian_JasaMedis_{doctorName}_{DateTime.Now:yyyyMMdd}.xlsx";
                        var fileContent = Helpers.VoucherExportHelper.GenerateVoucherDetailExcel(data);
                return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $"VoucherDetail_{doctorCode}.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "ExportVoucherDetail Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ Manual Entry ============

    /// <summary>
    /// Create a manual medical fee entry
    /// </summary>
    [HttpPost("Manual")]
    [Authorize(Page.TrxMedicalFee, Action.Create)]
    public async Task<IActionResult> CreateManual([FromBody] CreateMedicalFeeRequest request)
    {
        try
        {
            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var result = await _medicalFeeService.CreateManual(request, userSession.UserID);
            return _Ok(result);
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "CreateManual Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ BPJS Adjustment Upload ============

    /// <summary>
    /// Upload BPJS adjustment file (TXT) to update medical fee amounts
    /// </summary>
    [HttpPost("Upload/BpjsAdjustment")]
    [Authorize(Page.TrxMedicalFee, Action.Create)]
    public async Task<IActionResult> UploadBpjsAdjustment(IFormFile file)
    {
        try
        {
            if (file == null || file.Length == 0)
                return _BadRequest(string.Format(TAG, "File is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            // Validate file extension
            var allowedExtensions = new[] { ".txt", ".csv", ".xlsx" };
            var extension = Path.GetExtension(file.FileName).ToLower();
            if (!allowedExtensions.Contains(extension))
                return _BadRequest(string.Format(TAG, "Only TXT, CSV, and XLSX files are allowed"));

            using var stream = file.OpenReadStream();
            var result = await _medicalFeeService.ImportBpjsAdjustment(stream, file.FileName, userSession.UserID);

            return _Ok(result, string.Format(TAG, $"Import completed: {result.UpdatedCount} updated, {result.SkippedCount} skipped"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "UploadBpjsAdjustment Error");
            return _BadRequest(e.Message);
        }
    }

    // ============ Bulk Manual Upload ============

    /// <summary>
    /// Upload CSV/XLSX file to bulk create manual medical fee entries.
    /// Returns JSON if all rows succeed, or Excel file with error rows if some fail.
    /// </summary>
    [HttpPost("Upload/BulkManual")]
    [Authorize(Page.TrxMedicalFee, Action.Create)]
    public async Task<IActionResult> UploadBulkManual(IFormFile file)
    {
        try
        {
            if (file == null || file.Length == 0)
                return _BadRequest(string.Format(TAG, "File is required"));

            if (userSession == null || userSession.UserID <= 0)
                return _BadRequest(string.Format(TAG, "User session not found"));

            var allowedExtensions = new[] { ".csv", ".xlsx" };
            var extension = Path.GetExtension(file.FileName).ToLower();
            if (!allowedExtensions.Contains(extension))
                return _BadRequest(string.Format(TAG, "Hanya file CSV dan XLSX yang diizinkan"));

            using var stream = file.OpenReadStream();
            var result = await _medicalFeeService.ImportBulkManual(stream, file.FileName, userSession.UserID);

            // If there are error rows, return Excel file with error details
            if (result.ErrorRows.Count > 0)
            {
                var errorExcel = _medicalFeeService.GenerateBulkManualErrorExcel(result.ErrorRows);
                // Return as JSON with error file as base64 so FE can download it
                return _Ok(new
                {
                    result.InsertedCount,
                    result.SkippedCount,
                    result.Errors,
                    hasErrors = true,
                    errorFile = Convert.ToBase64String(errorExcel),
                    errorFileName = $"Error_BulkJasaMedis_{DateTime.Now:yyyyMMdd_HHmmss}.xlsx"
                }, string.Format(TAG, $"Import selesai: {result.InsertedCount} berhasil, {result.SkippedCount} gagal"));
            }

            return _Ok(new
            {
                result.InsertedCount,
                result.SkippedCount,
                hasErrors = false,
            }, string.Format(TAG, $"Import selesai: {result.InsertedCount} berhasil"));
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "UploadBulkManual Error");
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Download Excel template for bulk manual medical fee upload
    /// </summary>
    [HttpGet("Template/BulkManual")]
    [Authorize(Page.TrxMedicalFee, Action.Read)]
    public IActionResult DownloadBulkManualTemplate()
    {
        try
        {
            var fileBytes = _medicalFeeService.GenerateBulkManualTemplate();
            return File(fileBytes, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", "Template_Bulk_JasaMedis.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(e, TAG, "DownloadBulkManualTemplate Error");
            return _BadRequest(e.Message);
        }
    }
}

