namespace RSSMBackend.DTOs.JournalImport;

/// <summary>
/// Row shape for the import history list. Deliberately excludes the errors payload -- a
/// rejected file can carry hundreds of entries and the list would balloon.
/// </summary>
public class JournalImportJobListItemDto
{
    public int Id { get; set; }
    public string FileName { get; set; } = string.Empty;
    public long? FileSize { get; set; }
    public string ImportType { get; set; } = string.Empty;
    public string Status { get; set; } = string.Empty;
    public int? BranchId { get; set; }
    public int? RequestedBy { get; set; }
    public string? RequestedByName { get; set; }
    public DateTime RequestedAt { get; set; }
    public DateTime? StartedAt { get; set; }
    public DateTime? FinishedAt { get; set; }
    public int? TotalRows { get; set; }
    public int? JournalCount { get; set; }
    public int? ErrorCount { get; set; }
    public string? ErrorMessage { get; set; }
    public int? DurationSeconds { get; set; }
}

/// <summary>
/// Detail view: everything in the list row plus the heavy payloads.
/// </summary>
public class JournalImportJobDetailDto : JournalImportJobListItemDto
{
    /// <summary>Row-level validation errors, [{row, field, message}].</summary>
    public List<JournalImportErrorDto> Errors { get; set; } = new();

    public List<JournalImportCreatedTransactionDto> CreatedTransactions { get; set; } = new();

    public string? WorkerHost { get; set; }
}

public class JournalImportErrorDto
{
    public int Row { get; set; }
    public string Field { get; set; } = string.Empty;
    public string Message { get; set; } = string.Empty;
}

public class JournalImportCreatedTransactionDto
{
    public string? TransactionId { get; set; }
    public string? TransactionNo { get; set; }
}

/// <summary>Result of enqueuing an upload -- one entry per accepted file, plus any rejects.</summary>
public class JournalImportEnqueueResultDto
{
    public List<JournalImportJobListItemDto> Queued { get; set; } = new();

    /// <summary>Files refused before queueing (wrong extension, empty, too large).</summary>
    public List<JournalImportRejectedFileDto> Rejected { get; set; } = new();
}

public class JournalImportRejectedFileDto
{
    public string FileName { get; set; } = string.Empty;
    public string Reason { get; set; } = string.Empty;
}

/// <summary>Lightweight poll payload: lets the frontend stop polling when the queue is idle.</summary>
public class JournalImportQueueStatusDto
{
    public int PendingCount { get; set; }
    public int RunningCount { get; set; }
    public bool HasActiveJob => PendingCount > 0 || RunningCount > 0;
}
