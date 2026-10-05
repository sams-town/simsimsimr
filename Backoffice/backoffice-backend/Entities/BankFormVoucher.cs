namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

/// <summary>
/// Grouped voucher for bank-book transactions (Penerimaan / Pengeluaran) — "Form Maker".
/// VoucherType discriminates: "PENERIMAAN" (Deposit) or "PENGELUARAN" (Payment).
/// </summary>
[Table("trx_bank_form_voucher", Schema = "public")]
public class BankFormVoucher
{
    [Key]
    [Column("id")]
    [DatabaseGenerated(DatabaseGeneratedOption.Identity)]
    public int Id { get; set; }

    [Column("voucher_number")]
    public string VoucherNumber { get; set; } = string.Empty;

    [Column("voucher_date")]
    public DateTime VoucherDate { get; set; }

    [Column("voucher_type")]
    public string VoucherType { get; set; } = "PENERIMAAN"; // PENERIMAAN | PENGELUARAN

    [Column("payment_type")]
    public string PaymentType { get; set; } = "CASH"; // CASH | BANK

    [Column("status")]
    public string Status { get; set; } = "ACTIVE"; // ACTIVE | DELETED

    // --- Manual input fields (Form Maker) ---
    [Column("person_name")]
    public string? PersonName { get; set; } // Nama Penerima / Nama Menyerahkan

    [Column("coa_code")]
    public string? CoaCode { get; set; } // Akun Bayar / Akun Tujuan (Kas/Bank)

    [Column("coa_name")]
    public string? CoaName { get; set; }

    [Column("amount")]
    public decimal Amount { get; set; } // Jumlah Dikeluarkan / Diserahkan

    [Column("discount_percent")]
    public decimal DiscountPercent { get; set; }

    [Column("discount_amount")]
    public decimal DiscountAmount { get; set; }

    [Column("pph23_enabled")]
    public bool Pph23Enabled { get; set; }

    [Column("pph23_percent")]
    public decimal Pph23Percent { get; set; }

    [Column("pph23_amount")]
    public decimal Pph23Amount { get; set; }

    [Column("purpose")]
    public string? Purpose { get; set; } // Keperluan

    [Column("total_amount")]
    public decimal TotalAmount { get; set; }

    [Column("notes")]
    public string? Notes { get; set; }

    [Column("recipient_bank_name")]
    public string? RecipientBankName { get; set; } // Nama Bank Penerima (Pengeluaran)

    [Column("recipient_account_name")]
    public string? RecipientAccountName { get; set; } // Nama Rekening Penerima (Pengeluaran)

    [Column("recipient_account_number")]
    public string? RecipientAccountNumber { get; set; } // No Rekening Penerima (Pengeluaran)

    [Column("branch_id")]
    public int? BranchId { get; set; }

    [Column("created_by")]
    public int? CreatedBy { get; set; }

    [Column("created_date")]
    public DateTime CreatedDate { get; set; } = DateTime.UtcNow;

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }
}
