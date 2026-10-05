namespace RSSMBackend.Services;

using RSSMBackend.Models;

public interface IMedicalFeeService
{
    // Existing List method with extended filters
    Task<PagedResult<MedicalFeeResponse>> GetList(
        string? keyword,
        string? doctorName,
        string? doctorId,
        string? patientType,
        string? payerCode,
        string? status,
        int? periodMonth,
        int? periodYear,
        DateTime? procedureDateFrom,
        DateTime? procedureDateTo,
        string? medicalProcedure,
        int page,
        int pageSize,
        string? orderBy,
        string? orderType);
    
    // Legacy payment (kept for backward compatibility)
    Task<int> ProcessPayment(MedicalFeePaymentRequest request, int userId);

    // ============ Posting ============
    Task<BatchPostingResult> PostingBatch(BatchPostingRequest request, int userId);
    Task<bool> CancelPosting(int medicalFeeId, int userId);
    Task<int> CancelPostingBatch(List<int> feeIds, int userId);

    // ============ Payment Voucher ============
    Task<PagedResult<DoctorVoucherSummary>> GetVoucherListGroupByDoctor(
        string? doctorCode,
        string? status,
        int page,
        int pageSize);

    Task<VoucherDetailResponse> GetVoucherDetail(string doctorCode, string? status = null);

    Task<GenerateVoucherResult> GenerateVoucher(GenerateVoucherRequest request, int userId);

    // ============ Settlement ============
    Task<PagedResult<SettlementSummary>> GetSettlementList(
        string? doctorCode,
        string? paymentVoucher,
        string? status,
        DateTime? voucherDateFrom,
        DateTime? voucherDateTo,
        int page,
        int pageSize);

    Task<SettlementDetailResponse> GetSettlementDetail(string paymentVoucher);

    Task<SettlementResult> ProcessSettlement(SettlementRequest request, int userId);

    // ============ Lookup ============
    Task<List<dynamic>> GetDoctorOptions(string? keyword);
    Task<List<dynamic>> GetPayerOptions(string? keyword);
    Task<List<dynamic>> GetPatientOptions(string? keyword);
    Task<List<dynamic>> GetVisitNoOptions(string medicalRecordNo);
    Task<List<dynamic>> GetSepNoOptions(string medicalRecordNo);
    Task<List<dynamic>> GetPaymentMethodOptions(string? keyword);
    Task<SalarySlipResponse> GetSalarySlip(string paymentVoucher, string printedBy, int branchId = 1);
    
    // ============ Settlement Export & BPJS Adjustment ============
    Task<List<SettlementSummary>> GetSettlementExport(string? doctorCode, string? paymentVoucher, string? status, DateTime? voucherDateFrom, DateTime? voucherDateTo);
    Task<List<SettlementMakerExport>> GetSettlementMakerExport(string? doctorCode, string? paymentVoucher, string? status, DateTime? voucherDateFrom, DateTime? voucherDateTo);
    Task<VoucherDetailResponse> GetVoucherDetailExport(string doctorCode, string status, string? paymentVoucher = null);
    Task<BpjsAdjustmentResult> ImportBpjsAdjustment(Stream fileStream, string fileName, int userId);
    
    // ============ Manual Entry ============
    Task<MedicalFeeResponse> CreateManual(CreateMedicalFeeRequest request, int userId);
    Task<BulkManualImportResult> ImportBulkManual(Stream fileStream, string fileName, int userId);
    byte[] GenerateBulkManualErrorExcel(List<BulkManualErrorRow> errorRows);
    byte[] GenerateBulkManualTemplate();

    // ============ Tax Calculation ============
    decimal CalculatePPh21(decimal grossAmount, bool isPPh21Subject);

}
