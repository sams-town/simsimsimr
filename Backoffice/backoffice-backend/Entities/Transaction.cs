namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_transaction", Schema = "public")]
public class Transaction
{
    [Key]
    [Column("transaction_id")]
    public string TransactionId { get; set; }

    [Column("transaction_date")]
    public DateTime? TransactionDate { get; set; }

    [Column("posted_date")]
    public DateTime? PostedDate { get; set; }

    [Column("journal_no")]
    public string? JournalNo { get; set; }

    [Column("coa_code")]
    public string? CoaCode { get; set; }

    [Column("coa_name")]
    public string? CoaName { get; set; }

    [Column("coa_parent_code")]
    public string? CoaParentCode { get; set; }

    [Column("coa_parent_name")]
    public string? CoaParentName { get; set; }

    //Revisi Typo
    [Column("description")]
    public string? Description { get; set; }

    [Column("transaction_no")]
    public string? TransactionNo { get; set; }

    [Column("transaction_type")]
    public string? TransactionType { get; set; }

    [Column("transaction_name")]
    public string? TransactionName { get; set; }

    [Column("transaction_ref")]
    public string? TransactionRef { get; set; }

    [Column("amount")]
    public double? Amount { get; set; }

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [NotMapped]
    public string? BranchName { get; set;}

    [Column("contact_id")]
    public int? ContactId { get; set; }

    [NotMapped]
    public string? ContactName { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [NotMapped]
    public string? CreatedName { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("status")]
    public string? status { get; set; }

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [NotMapped]
    public string? UpdatedName { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    [Column("is_auto_id")]
    public bool? IsAutoId { get; set; }

    [InverseProperty("Transaction")]
    public List<Journal>? Details { get; set; }

    [Column("payment_no")]
    public string? PaymentNo { get; set; }

    [Column("payment_status")]
    public string? PaymentStatus { get; set; }

    [Column("payment_date")]
    public DateTime? PaymentDate { get; set; }

    [ForeignKey(nameof(BranchId))]
    public Branch? Branch { get; set; }

    [ForeignKey(nameof(CoaCode))]
    public Coa? Coa { get; set; }

    [ForeignKey(nameof(CoaParentCode))]
    public Coa? CoaParent { get; set; }

    [ForeignKey(nameof(ContactId))]
    public Contact? Contact { get; set; }

    [ForeignKey(nameof(CreatedBy))]
    public User? Created { get; set; }

    [ForeignKey(nameof(UpdatedBy))]
    public User? Updated { get; set; }
}