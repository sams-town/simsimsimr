namespace RSSMBackend.Entities;

public class TransactionRequest
{
    public string? TransactionId { get; set; }
    public DateTime? TransactionDate { get; set; }
    public string? CoaCode { get; set; }
    public string? Description { get; set; }
    public string? TransactionNo { get; set; }
    public string? TransactionType { get; set; }
    public string? TransactionName { get; set; }
    public string? TransactionRef { get; set; }
    public string? PaymentNo { get; set; }
    public string? PaymentStatus { get; set; }
    public DateTime? PaymentDate { get; set; }
    public int? ContactId { get; set; }
    public  List<TransactionDetail> Details  { get; set; }

    public class TransactionDetail {

        public int? JournalId { get; set; }
        public string? CoaCode { get; set; }
        public string? Description{ get; set; }
        public double? Amount{ get; set; } 

        public bool IsPremier { get; set; }
        public string? Position { get; set; } // "Debit" or "Credit"
    }    
}