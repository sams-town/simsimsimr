namespace RSSMBackend.DTOs.MigrationJob;

public class MigrationJobListItemDto
{
    public int Id { get; set; }
    public string ScriptName { get; set; }
    public DateTime TargetDate { get; set; }
    public string? UpdateColumns { get; set; }
    public string Status { get; set; }
    public int? RequestedBy { get; set; }
    public string? RequestedByName { get; set; }
    public DateTime RequestedAt { get; set; }
    public DateTime? StartedAt { get; set; }
    public DateTime? FinishedAt { get; set; }
    public int? ExitCode { get; set; }
    public string? ErrorMessage { get; set; }
    public int? DurationSeconds { get; set; }
}
