namespace RSSMBackend.DTOs.AccountReceivable;

public class FinalizeVoucherRequest
{
    public string PayerId { get; set; } = string.Empty;
    public DateTime VoucherDate { get; set; }
    public DateTime? DueDate { get; set; }
    public List<int>? ArIds { get; set; }
}
