namespace RSSMBackend.DTOs.MigrationJob;

public class CreateMigrationJobDto
{
    public string ScriptName { get; set; }
    public DateTime TargetDate { get; set; }
    public bool UpdateAllColumns { get; set; }
}
