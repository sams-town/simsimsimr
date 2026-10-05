namespace RSSMBackend.DTOs.AccountReceivable;

public class AdjustArRequest
{
    public int ArId { get; set; }
    public decimal AdjustmentAmount { get; set; }
    public string Reason { get; set; } = string.Empty;
}
