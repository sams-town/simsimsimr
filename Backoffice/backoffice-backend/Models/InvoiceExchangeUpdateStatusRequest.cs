namespace RSSMBackend.Models;

using System.ComponentModel.DataAnnotations;

public class InvoiceExchangeUpdateStatusRequest
{
    [Required]
    public required string Id { get; set; }
    
    public DateTime? DueDate { get; set; }

    public DateTime? ExchangeDate { get; set; }
}

