namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_migration_job", Schema = "public")]
public class MigrationJob
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    [Column("script_name")]
    public string ScriptName { get; set; }

    [Column("target_date")]
    public DateTime TargetDate { get; set; }

    [Column("update_columns")]
    public string? UpdateColumns { get; set; }

    [Column("status")]
    public string Status { get; set; }

    [Column("requested_by")]
    public int? RequestedBy { get; set; }

    [Column("requested_by_name")]
    public string? RequestedByName { get; set; }

    [Column("requested_at")]
    public DateTime RequestedAt { get; set; }

    [Column("started_at")]
    public DateTime? StartedAt { get; set; }

    [Column("finished_at")]
    public DateTime? FinishedAt { get; set; }

    [Column("exit_code")]
    public int? ExitCode { get; set; }

    [Column("command")]
    public string? Command { get; set; }

    [Column("stdout_log")]
    public string? StdoutLog { get; set; }

    [Column("stderr_log")]
    public string? StderrLog { get; set; }

    [Column("error_message")]
    public string? ErrorMessage { get; set; }

    [Column("worker_host")]
    public string? WorkerHost { get; set; }

    [Column("worker_pid")]
    public int? WorkerPid { get; set; }
}
