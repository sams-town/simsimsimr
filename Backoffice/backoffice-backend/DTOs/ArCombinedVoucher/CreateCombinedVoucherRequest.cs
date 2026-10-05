namespace RSSMBackend.DTOs.ArCombinedVoucher;

public class CreateCombinedVoucherRequest
{
    public DateTime? BatchDate { get; set; }
    public string? Description { get; set; }
    public List<int> ArIds { get; set; } = new();
}
