namespace RSSMBackend.DTOs.ApPaymentVoucher;

public class ApPaymentVoucherItemsRequest
{
    public List<int> InvoiceExchangeIds { get; set; } = new();
}
