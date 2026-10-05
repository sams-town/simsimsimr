namespace RSSMBackend.DTOs.ApPaymentVoucher;

public class CreateApPaymentVoucherRequest
{
    public DateTime? VoucherDate { get; set; }
    public string? PaymentType { get; set; }
    public List<int> InvoiceExchangeIds { get; set; } = new();
}
