namespace RSSMBackend.DTOs.MigrationJob;

public class MigrationJobDetailDto : MigrationJobListItemDto
{
    public string? Command { get; set; }
    public string? StdoutLog { get; set; }
    public string? StderrLog { get; set; }
    public string? WorkerHost { get; set; }
    public int? WorkerPid { get; set; }
}
