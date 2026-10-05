namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("mst_bank", Schema = "master")]
public class Bank {
    [Key]
    [Column("bank_code")]
    public string BankCode { get; set; }

    [Column("bank_name")]
    public string BankName { get; set; }
}