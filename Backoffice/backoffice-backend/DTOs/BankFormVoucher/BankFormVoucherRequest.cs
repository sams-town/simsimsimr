namespace RSSMBackend.DTOs.BankFormVoucher;

public class BankFormVoucherRequest
{
    public string? VoucherType { get; set; }
    public DateTime? VoucherDate { get; set; }
    public string? PaymentType { get; set; }
    public string? PersonName { get; set; }
    public string? CoaCode { get; set; }
    public string? CoaName { get; set; }
    public decimal Amount { get; set; }
    public decimal DiscountPercent { get; set; }
    public decimal DiscountAmount { get; set; }
    public bool Pph23Enabled { get; set; }
    public decimal Pph23Percent { get; set; }
    public decimal Pph23Amount { get; set; }
    public string? Purpose { get; set; }
    public string? Notes { get; set; }
    public string? RecipientBankName { get; set; }
    public string? RecipientAccountName { get; set; }
    public string? RecipientAccountNumber { get; set; }
}
