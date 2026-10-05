namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_account_receivable", Schema = "public")]
public class AccountReceivable
{
    [Key]
    [Column("ar_id")]
    public int ArId { get; set; }

    [Column("ar_number")]
    public string? ArNumber { get; set; }

    // Relation to Journal
    [Column("journal_id")]
    public string? JournalId { get; set; }

    // Payer Info
    [Column("payer_type")]
    public string PayerType { get; set; } = string.Empty; // BPJS, ASR, PRH, DKK, KRY, DKT, PSN

    [Column("payer_id")]
    public string? PayerId { get; set; }

    [Column("payer_name")]
    public string? PayerName { get; set; }

    // Claim Info
    [Column("claim_number")]
    public string? ClaimNumber { get; set; }

    [Column("service_type")]
    public string? ServiceType { get; set; } // Ranap/Rajal

    [Column("coa_code")]
    public string? CoaCode { get; set; }

    // Patient Info (Added as per user request)
    [Column("patient_name")]
    public string? PatientName { get; set; }

    [Column("medical_record_no")]
    public string? MedicalRecordNo { get; set; }
    // Amount
    [Column("transaction_date")]
    public DateTime TransactionDate { get; set; }

    [Column("due_date")]
    public DateTime? DueDate { get; set; }

    [Column("total_amount")]
    public Double TotalAmount { get; set; }

    [Column("paid_amount")]
    public Double? PaidAmount { get; set; }

    [Column("discount_percent")]
    public Double? DiscountPercent { get; set; } = 0;

    [Column("discount_amount")]
    public Double? DiscountAmount { get; set; } = 0;

    [Column("outstanding_amount")]
    public Double? OutstandingAmount { get; set; }

    [Column("adjustment_amount", TypeName = "decimal(18, 2)")]
    public decimal? AdjustmentAmount { get; set; }

    [Column("adjustment_date")]
    public DateTime? AdjustmentDate { get; set; }

    [Column("adjustment_notes")]
    public string? AdjustmentNotes { get; set; }

    [Column("adjustment_history", TypeName = "jsonb")]
    public string? AdjustmentHistory { get; set; }

    [NotMapped]
    public int AgingDays => (DateTime.Now - TransactionDate).Days;

    // Status
    [Column("payment_status")]
    public string PaymentStatus { get; set; } = "Pending"; // Pending/Partial/Paid

    [Column("payment_date")]
    public DateTime? PaymentDate { get; set; }

    [Column("payment_ref")]
    public string? PaymentRef { get; set; }

    [Column("payment_coa_code")]
    public string? PaymentCoaCode { get; set; }

    [Column("payment_by")]
    public int? PaymentBy { get; set; }

    // Voucher Info (for Payment Voucher AR flow)
    [Column("voucher_number")]
    public string? VoucherNumber { get; set; }

    [Column("voucher_date")]
    public DateTime? VoucherDate { get; set; }

    [Column("payment_journal_id")]
    public string? PaymentJournalId { get; set; }

    // Reconciliation
    [Column("reconcile_status")]
    public string? ReconcileStatus { get; set; }

    [Column("reconcile_bpjs_amount")]
    public decimal? ReconcileBpjsAmount { get; set; }

    [Column("reconcile_difference")]
    public decimal? ReconcileDifference { get; set; }

    [Column("reconcile_date")]
    public DateTime? ReconcileDate { get; set; }

    // Audit
    [Column("notes")]
    public string? Notes { get; set; }

    [Column("note_history", TypeName = "jsonb")]
    public string? NoteHistory { get; set; }

    [Column("payment_history", TypeName = "jsonb")]
    public string? PaymentHistory { get; set; }

    [Column("payer_change_history", TypeName = "jsonb")]
    public string? PayerChangeHistory { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    // New Features (SEP, Print, Void)
    [Column("no_sep")]
    public string? SepNo { get; set; }

    [Column("is_printed")]
    public bool IsPrinted { get; set; } = false;

    [Column("status")]
    public string Status { get; set; } = "ACTIVE"; // ACTIVE, VOID

    // Navigation Properties
    [NotMapped]
    public string? PayerTypeName => PayerType switch
    {
        "BPJS" => "BPJS Kesehatan",
        "ASR" => "Asuransi Swasta",
        "PRH" => "Perusahaan",
        "DKK" => "Dinkes/Kemkes",
        "KRY" => "Karyawan",
        "DKT" => "Dokter",
        "PSN" => "Pasien Individu",
        _ => PayerType
    };

    [NotMapped]
    public string? CreatedName { get; set; }

    [NotMapped]
    public string? UpdatedName { get; set; }

    [NotMapped]
    public string? BranchName { get; set; }

    [NotMapped]
    public string? CoaName { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }

    [ForeignKey(nameof(CreatedBy))]
    public User? Created { get; set; }

    [ForeignKey(nameof(UpdatedBy))]
    public User? Updated { get; set; }
}
