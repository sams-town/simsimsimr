namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_journal", Schema = "public")]
public class Journal
{
    [Key]
    [Column("journal_id")]
    public int JournalId { get; set; }

    [Column("journal_desc")]
    public string? JournalDesc { get; set; }

    [Column("transaction_id")]
    public string? TransactionId { get; set; }

    [Column("journal_ref")]
    public string? JournalRef { get; set; }

    [Column("coa_code")]
    public string? CoaCode { get; set; }

    [Column("coa_name")]
    public string? CoaName { get; set; }

    [Column("coa_parent_code")]
    public string? CoaParentCode { get; set; }

    [Column("coa_parent_name")]
    public string? CoaParentName { get; set; }

    [Column("debit_amount")]
    public double? DebitAmount { get; set; }

    [Column("credit_amount")]
    public double? CreditAmount { get; set; }

    [Column("source")]
    public string? Source { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set;}

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [NotMapped]
    public string? CreatedName { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("vourcher_id")]
    public string? VourcherId { get; set; }

    [Column("status")]
    public string? status { get; set; }

    [Column("is_premier")]
    public bool? IsPremier { get; set; }

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [NotMapped]
    public string? UpdatedName { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    [Column("reconciled_date")]
    public DateTime? ReconciledDate { get; set; }

    [Column("reconciled_by")]
    public int? ReconciledBy { get; set; }

    [NotMapped]
    public string? ReconciledName { get; set; }

    [Column("current_balance")]
    public double? CurrentBalance { get; set; }

    [Column("prev_balance")]
    public double? PrevBalance { get; set; }

    [Column("thb_po_number")]
    public string? PONumber { get; set; }

    [Column("thb_receipt_date")]
    public DateTime? ReceiptDate { get; set; }

    [Column("thb_po_date")]
    public DateTime? PODate { get; set; }

    [Column("thb_supplier_name")]
    public string? SupplierName { get; set; }

    [Column("thb_total_transaction")]
    public double? TotalTransaction { get; set; }

    [Column("clinic_code")]
    public string? ClinicCode { get; set; }

    [Column("clinic_name")]
    public string? ClinicName { get; set; }

    [Column("ward_code")]
    public string? WardCode { get; set; }

    [Column("ward_name")]
    public string? WardName { get; set; }

    [Column("care_type")]
    public string? CareType { get; set; }

    [Column("payer_code")]
    public string? PayerCode { get; set; }

    [Column("payer_name")]
    public string? PayerName { get; set; }

    [Column("admission_date")]
    public DateTime? AdmissionDate { get; set; }

    [Column("discharge_date")]
    public DateTime? DischargeDate { get; set; }

    [Column("costcenter_id")]
    public int? CostcenterId { get; set; }

    [NotMapped]
    public TransactionForJournal TransactionDetail { get; set; }

    public class TransactionForJournal
    {
        public string? TransactionNo { get; set; }
        public string? TransactionType { get; set; }
        public DateTime? TransactionDate { get; set; }
        public string? JournalNo { get; set; }
        public Contact ContactDetail { get; set; }
    }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }

    [ForeignKey(nameof(CoaCode))]
    public Coa? Coa { get; set; }

    [ForeignKey(nameof(CoaParentCode))]
    public Coa? CoaParent { get; set; }

    [ForeignKey(nameof(TransactionId))]
    public Transaction? Transaction { get; set; }

    [ForeignKey(nameof(CreatedBy))]
    public User? Created { get; set; }

    [ForeignKey(nameof(UpdatedBy))]
    public User? Updated { get; set; }

    [ForeignKey(nameof(ReconciledBy))]
    public User? Reconciled { get; set; }

    [ForeignKey(nameof(CostcenterId))]
    public CostCenter? CostCenter { get; set; }
}