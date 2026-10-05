namespace RSSMBackend.Models;

using System.ComponentModel.DataAnnotations;

public class UpdateStatusRequest
{
    [Required]
    public string TransactionId { get; set; }

    [Required]
    public string Status { get; set; }
}