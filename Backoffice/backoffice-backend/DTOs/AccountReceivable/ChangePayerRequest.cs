namespace RSSMBackend.DTOs.AccountReceivable;

public class ChangePayerRequest
{
    public int ArId { get; set; }
    public string NewPayerId { get; set; } = string.Empty;
    public string? NewPayerName { get; set; }
    public string Reason { get; set; } = string.Empty;
}
