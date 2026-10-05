namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_contact_account", Schema = "master")]
public class ContactAccount
{
    [Key]
    [Column("account_id")]
    public int AccountId { get; set; }

    [Column("account_name")]
    public string AccountName { get; set; }

    [Column("bank_code")]
    public string BankCode { get; set; }

    [NotMapped]
    public string? BankName { get; set; }

    [Column("account_no")]
    public string AccountNo { get; set; }

    [Column("contact_id")]
    public int ContactId { get; set; }

    [ForeignKey(nameof(BankCode))]
    public Bank? Bank { get; set; }

    [ForeignKey(nameof(ContactId))]
    public Contact? Contact { get; set; }
}