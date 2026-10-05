namespace RSSMBackend.Models;

public class CancelInvoiceExchangeRequest
{
    public List<int> InvoiceIds { get; set; } = new List<int>();
}
