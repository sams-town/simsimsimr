namespace RSSMBackend.DTOs.AccountReceivable;

public class AddNoteRequest
{
    public int ArId { get; set; }
    public string Note { get; set; } = string.Empty;
}
