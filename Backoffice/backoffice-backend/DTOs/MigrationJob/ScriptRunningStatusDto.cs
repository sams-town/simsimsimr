namespace RSSMBackend.DTOs.MigrationJob;

public class ScriptRunningStatusDto
{
    public string ScriptName { get; set; }
    public bool IsRunning { get; set; }
    public int? ActiveJobId { get; set; }
    public string? ActiveJobStatus { get; set; }
}
