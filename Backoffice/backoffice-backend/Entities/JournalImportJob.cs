namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

/// <summary>
/// One queued General Journal import file. Written by the upload endpoint as 'pending',
/// claimed and completed by JournalImportWorker (Quartz).
/// </summary>
[Table("trx_journal_import_job", Schema = "public")]
public class JournalImportJob
{
    [Key]
    [Column("id")]
    public int Id { get; set; }

    /// <summary>Original upload name -- shown in the list and used as the download filename.</summary>
    [Column("file_name")]
    public string FileName { get; set; } = string.Empty;

    /// <summary>Path relative to the app root, under the mounted Uploads volume.</summary>
    [Column("stored_path")]
    public string StoredPath { get; set; } = string.Empty;

    [Column("file_size")]
    public long? FileSize { get; set; }

    /// <summary>See JournalImportType.</summary>
    [Column("import_type")]
    public string ImportType { get; set; } = JournalImportType.Standard;

    /// <summary>See JournalImportStatus.</summary>
    [Column("status")]
    public string Status { get; set; } = JournalImportStatus.Pending;

    [Column("branch_id")]
    public int? BranchId { get; set; }

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

    [Column("total_rows")]
    public int? TotalRows { get; set; }

    [Column("journal_count")]
    public int? JournalCount { get; set; }

    [Column("error_count")]
    public int? ErrorCount { get; set; }

    /// <summary>Row-level validation errors as [{row, field, message}].</summary>
    [Column("errors", TypeName = "jsonb")]
    public string? Errors { get; set; }

    [Column("created_transactions", TypeName = "jsonb")]
    public string? CreatedTransactions { get; set; }

    /// <summary>Fatal error (unreadable file, worker crash) -- not a row-level validation error.</summary>
    [Column("error_message")]
    public string? ErrorMessage { get; set; }

    [Column("worker_host")]
    public string? WorkerHost { get; set; }
}

/// <summary>Same vocabulary as trx_migration_job / migration-worker.</summary>
public static class JournalImportStatus
{
    public const string Pending = "pending";
    public const string Running = "running";
    public const string Success = "success";
    public const string Failed = "failed";

    /// <summary>Statuses that mean the worker still has something to do -- gates the frontend polling.</summary>
    public static readonly string[] Active = { Pending, Running };
}

public static class JournalImportType
{
    /// <summary>6-column template (Files/Templates/General Journal Template.xlsx).</summary>
    public const string Standard = "standard";

    /// <summary>18-column THB layout with auto-detected header row.</summary>
    public const string Thb = "thb";

    public static bool IsValid(string? value) =>
        value == Standard || value == Thb;
}
