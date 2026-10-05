namespace RSSMBackend.Models;

public class MedicalFeeResponse
{
    public int MedicalFeeId { get; set; }
    public int DoctorId { get; set; }
    public string? DoctorCode { get; set; }
    public string? DoctorName { get; set; }
    public int? PeriodMonth { get; set; }
    public int? PeriodYear { get; set; }
    public string? Period { get; set; }
    public string? PatientName { get; set; }
    public string? MedicalRecordNo { get; set; }
    public string? PatientType { get; set; }
    public string? PayerCode { get; set; }
    public string? PayerName { get; set; }
    public decimal TotalFee { get; set; }
    public decimal? AdjustmentAmount { get; set; } // Added for Export
    public decimal? PaidAmount { get; set; }
    public decimal OutstandingAmount { get; set; }
    public string? Status { get; set; }
    public DateTime? PaymentDate { get; set; }
    public string? PaymentRef { get; set; }
    public string? BatchNumber { get; set; }
    public string? PaymentVoucher { get; set; }
    public decimal? DoctorShare { get; set; }
    public DateTime? ProcedureDate { get; set; }
    public string? ProcedureName { get; set; }
    public string? SepNo { get; set; }
    public string? MedicalProcedure { get; set; } // Rawat Jalan / Rawat Inap
}

public class MedicalFeePaymentRequest
{
    public List<int> FeeIds { get; set; } = new();
    public DateTime PaymentDate { get; set; }
    public string CoaCode { get; set; } = string.Empty;
    public string? PaymentRef { get; set; }
}

// ============ Posting ============
public class BatchPostingRequest
{
    public List<int> FeeIds { get; set; } = new();
}

public class BatchPostingResult
{
    public int PostedCount { get; set; }
    public string BatchNumber { get; set; } = string.Empty;
}

// ============ Payment Voucher ============
public class DoctorVoucherSummary
{
    public int DoctorId { get; set; }
    public string DoctorCode { get; set; } = string.Empty;
    public string DoctorName { get; set; } = string.Empty;
    public string Period { get; set; } = string.Empty;
    public int ItemCount { get; set; }
    public decimal TotalFee { get; set; }
}

public class VoucherDetailResponse
{
    public int DoctorId { get; set; }
    public string DoctorCode { get; set; } = string.Empty;
    public string DoctorName { get; set; } = string.Empty;
    public int ItemCount { get; set; }
    public decimal TotalFee { get; set; }
    public decimal DoctorShareAmount { get; set; }
    public decimal DoctorSharePercentage { get; set; }
    public decimal DefaultDoctorShare { get; set; } // Added for Prefill
    public decimal TaxAmount { get; set; }
    public decimal AfterTaxAmount { get; set; }
    public decimal PreviousPeriodIncome { get; set; }
    public bool IsPPh21 { get; set; }
    public List<TaxTierDetail> TaxDetails { get; set; } = new();
    public List<VoucherDetailItem> Items { get; set; } = new();
}

public class TaxTierDetail
{
    public string TierName { get; set; } = string.Empty;
    public decimal Rate { get; set; }
    public decimal TaxableAmount { get; set; }
    public decimal TaxValue { get; set; }
}

public class VoucherDetailItem
{
    public int MedicalFeeId { get; set; }
    public string Period { get; set; } = string.Empty;
    public DateTime? ProcedureDate { get; set; }
    public string? ProcedureName { get; set; }
    public string? PayerCode { get; set; }
    public string? PayerName { get; set; }
    public decimal TotalFee { get; set; }
    public DateTime? PostingDate { get; set; }
    public string? PaymentVoucher { get; set; }
    public string? PatientName { get; set; }
    public string? MedicalRecordNo { get; set; }
    public string? VisitNo { get; set; }  // Keep for reference
    public string? SepNo { get; set; }    // Expose explicit SepNo
    public decimal? AdjustmentAmount { get; set; } // For Export/Import
}

public class GenerateVoucherRequest
{
    public int DoctorId { get; set; }
    public string? DoctorCode { get; set; }
    public decimal DoctorSharePercent { get; set; }
}

public class GenerateVoucherResult
{
    public string PaymentVoucher { get; set; } = string.Empty;
    public int UpdatedCount { get; set; }
    public decimal TotalFee { get; set; }
    public decimal DoctorShareAmount { get; set; }
}

// ============ Settlement ============
public class SettlementSummary
{
    public int DoctorId { get; set; }
    public string DoctorName { get; set; } = string.Empty;
    public string PaymentVoucher { get; set; } = string.Empty;
    public DateTime? VoucherDate { get; set; }
    public decimal DoctorShare { get; set; }
    public decimal TotalFee { get; set; }
    public decimal TotalAfterShare { get; set; }
    public decimal TotalTax { get; set; }
    public decimal TotalZakat { get; set; }
    public int ItemCount { get; set; }
    public string Status { get; set; } = string.Empty;
}

public class SettlementMakerExport
{
    public string DoctorName { get; set; } = string.Empty;
    public decimal HonorNetto { get; set; }
    public string BankName { get; set; } = string.Empty;
    public string AccountNo { get; set; } = string.Empty;
}

public class SettlementDetailItem
{
    public int MedicalFeeId { get; set; }
    public DateTime? ProcedureDate { get; set; }
    public string? ProcedureName { get; set; }
    public string? PayerName { get; set; }
    public string? PatientName { get; set; }
    public string? MedicalRecordNo { get; set; }
    public decimal TotalFee { get; set; }
    public decimal DoctorShare { get; set; }
    public decimal TaxAmount { get; set; }
    public decimal ZakatAmount { get; set; }
    public decimal AmountAfterShare { get; set; }
}

public class SettlementDetailResponse
{
    public List<SettlementDetailItem> Items { get; set; } = new();
    public string PaymentVoucher { get; set; } = string.Empty;
    public DateTime? VoucherDate { get; set; }
    public string Status { get; set; } = string.Empty;
    public DateTime? PaymentDate { get; set; }
    public string? PaymentRef { get; set; }
    public decimal DoctorSharePercentage { get; set; }
    public string? CoaCode { get; set; }
    public string? CoaDesc { get; set; }
    public decimal TotalAmount { get; set; }
    public decimal TotalTax { get; set; }
    public decimal TotalZakat { get; set; }
    public decimal TotalNet { get; set; }
    public bool IsPPh21 { get; set; } // Added for Badge Display
    public decimal PreviousPeriodIncome { get; set; }
    public List<TaxTierDetail> TaxDetails { get; set; } = new();
}

public class SettlementRequest
{
    public string PaymentVoucher { get; set; } = string.Empty;
    public string CoaCode { get; set; } = string.Empty;
    public string CoaDesc { get; set; } = string.Empty;
    public DateTime PaymentDate { get; set; }
    public string PaymentRef { get; set; } = string.Empty;
    public decimal PaidAmount { get; set; }
    public decimal? Discount { get; set; }
}

public class SettlementResult
{
    public int UpdatedCount { get; set; }
    public decimal TotalPaid { get; set; }
}

public class SalarySlipResponse
{
    public string HospitalName { get; set; } = "RS SETIA MITRA";
    public string DoctorName { get; set; } = string.Empty;
    public string Period { get; set; } = string.Empty;
    public List<SalarySlipItem> Items { get; set; } = new();
    public decimal SubTotal { get; set; }
    public decimal TaxAmount { get; set; }
    public decimal TaxRate { get; set; } = 2.5m; // Default 2.5%
    public decimal AfterTaxAmount { get; set; }
    public decimal ZakatAmount { get; set; }
    public decimal ZakatRate { get; set; } = 2.5m; // Default 2.5%
    public decimal NetAmount { get; set; }
    public DateTime PrintedDate { get; set; }
    public string PrintedBy { get; set; } = string.Empty;
}

public class SalarySlipItem
{
    public string RegistrationNo { get; set; } = string.Empty;
    public string InvoiceNo { get; set; } = string.Empty;
    public string MedicalRecordNo { get; set; } = string.Empty;
    public string PatientName { get; set; } = string.Empty;
    public string Polyclinic { get; set; } = string.Empty;
    public string ServiceName { get; set; } = string.Empty;
    public string PaymentType { get; set; } = string.Empty;
    public decimal DoctorShare { get; set; }
}

// ============ BPJS Adjustment ============
public class BpjsAdjustmentResult
{
    public int ImportedCount { get; set; }
    public int UpdatedCount { get; set; }
    public int SkippedCount { get; set; }
    public List<string> Errors { get; set; } = new();
}

public class BpjsAdjustmentItem
{
    public string SepNo { get; set; } = string.Empty;
    public string PatientName { get; set; } = string.Empty;
    public decimal ClaimAmount { get; set; }
    public decimal ApprovedAmount { get; set; }
    public string Status { get; set; } = string.Empty;
}

// ============ AR Adjustment ============
public class ArAdjustmentResult
{
    public int ImportedCount { get; set; }
    public int UpdatedCount { get; set; }
    public int SkippedCount { get; set; }
    public List<string> Errors { get; set; } = new();
}

// ============ Manual Entry ============
public class CreateMedicalFeeRequest
{
    public int DoctorId { get; set; }
    public string? DoctorCode { get; set; }
    public string? DoctorName { get; set; }
    public string? PatientName { get; set; }
    public string? MedicalRecordNo { get; set; }
    public string? SepNo { get; set; }
    public string? ProcedureName { get; set; }
    public DateTime? ProcedureDate { get; set; }
    public string? MedicalProcedure { get; set; } // Rawat Jalan / Rawat Inap
    public string? PayerCode { get; set; }
    public string? PayerName { get; set; }
    public string? VisitNo { get; set; }
    public decimal TotalFee { get; set; }
}

// ============ Bulk Manual Entry ============
public class BulkManualImportResult
{
    public int InsertedCount { get; set; }
    public int SkippedCount { get; set; }
    public List<string> Errors { get; set; } = new();
    public List<BulkManualErrorRow> ErrorRows { get; set; } = new();
}

public class BulkManualErrorRow
{
    public string? KodeDokter { get; set; }
    public string? NamaDokter { get; set; }
    public string? NamaPasien { get; set; }
    public string? NoRekamMedis { get; set; }
    public string? NoRegistrasi { get; set; }
    public string? NoSEP { get; set; }
    public string? NamaTindakan { get; set; }
    public string? TanggalTindakan { get; set; }
    public string? JenisPelayanan { get; set; }
    public string? KodeCaraBayar { get; set; }
    public string? NamaCaraBayar { get; set; }
    public string? TotalFee { get; set; }
    public string? Error { get; set; }
}