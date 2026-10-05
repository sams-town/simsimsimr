namespace RSSMBackend.DTOs.AccountReceivable;

public class AccountReceivablePaymentRequest
{
    public List<int>? ArIds { get; set; }
    public string? PayerId { get; set; }
    public DateTime PaymentDate { get; set; }
    public string PaymentCoaCode { get; set; } = string.Empty;
    public string? PaymentRef { get; set; }
}
