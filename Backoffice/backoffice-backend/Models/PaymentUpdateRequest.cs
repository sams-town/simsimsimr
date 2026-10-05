namespace RSSMBackend.Models;

public class PaymentUpdateRequest {
    public string TransactionId {get; set;}
    public string PaymentStatus {get; set;}
    public string PaymentNo {get; set;}
    public DateTime PaymentDate {get; set;}
}