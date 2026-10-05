namespace RSSMBackend.DTOs.ApPaymentVoucher;

public class UpdateApPaymentVoucherRequest
{
    public DateTime? VoucherDate { get; set; }
    public string? PaymentType { get; set; }
}
