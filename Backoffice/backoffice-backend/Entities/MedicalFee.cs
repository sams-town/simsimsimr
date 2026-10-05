namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_medical_fee", Schema = "public")]
public class MedicalFee
{
    [Key]
    [Column("medical_fee_id")]
    public int MedicalFeeId { get; set; }

    [Column("doctor_id")]
    public int DoctorId { get; set; }

    // Navigation property to Contact
    [ForeignKey("DoctorId")]
    public virtual Contact? Doctor { get; set; }

    [Column("doctor_name")]
    public string? DoctorName { get; set; }

     [Column("doctor_code")]
    public string? DoctorCode { get; set; }

    [Column("period_month")]
    public int? PeriodMonth { get; set; }

    [Column("period_year")]
    public int? PeriodYear { get; set; }

    [Column("patient_type")]
    public string? PatientType { get; set; } // Tunai, BPJS, Asuransi

    [Column("total_fee")]
    public decimal TotalFee { get; set; }

    [Column("paid_amount")]
    public decimal? PaidAmount { get; set; } = 0;

    [Column("status")]
    public string? Status { get; set; } = "Pending"; // Pending, Posting, Unpaid, Paid

    [Column("payment_date")]
    public DateTime? PaymentDate { get; set; }

    [Column("payment_ref")]
    public string? PaymentRef { get; set; }

    [Column("payment_coa_code")]
    public string? PaymentCoaCode { get; set; }

    [Column("payment_coa_desc")]
    public string? PaymentCoaDesc { get; set; }

    [Column("journal_id")]
    public int? JournalId { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [Column("patient_name")]
    public string? PatientName { get; set; }

    [Column("medical_record_no")]
    public string? MedicalRecordNo { get; set; }

    [Column("cost_center_id")]
    public int? CostCenterId { get; set; }

    // New fields for Jasa Medis Enhancement
    [Column("batch_number")]
    public string? BatchNumber { get; set; }

    [Column("payment_voucher")]
    public string? PaymentVoucher { get; set; }

    [Column("doctor_share", TypeName = "numeric(18, 4)")]
    public decimal? DoctorShare { get; set; }

    [Column("procedure_date")]
    public DateTime? ProcedureDate { get; set; }

    [Column("procedure_name")]
    public string? ProcedureName { get; set; }

    [Column("medical_procedure")]
    public string? MedicalProcedure { get; set; }

    [Column("payer_code")]
    public string? PayerCode { get; set; }

    [Column("payer_name")]
    public string? PayerName { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    // Audit fields for posting and voucher
    [Column("posting_date")]
    public DateTime? PostingDate { get; set; }

    [Column("posting_by")]
    public int? PostingBy { get; set; }

    [Column("voucher_date")]
    public DateTime? VoucherDate { get; set; }

    [Column("voucher_by")]
    public int? VoucherBy { get; set; }

    [Column("visit_no")]
    public string? VisitNo { get; set; }

    [Column("tax", TypeName = "decimal(18, 2)")]
    public decimal? Tax { get; set; }

    [Column("zakat", TypeName = "decimal(18, 2)")]
    public decimal? Zakat { get; set; }

    [Column("no_sep")]
    public string? SepNo { get; set; }

    [Column("adjustment_amount", TypeName = "decimal(18, 2)")]
    public decimal? AdjustmentAmount { get; set; } // Approved Amount from BPJS

    [Column("adjustment_date")]
    public DateTime? AdjustmentDate { get; set; }
}
