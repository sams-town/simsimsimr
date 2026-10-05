using Microsoft.EntityFrameworkCore;
using RSSMBackend.DbContext;
using RSSMBackend.DTOs.MigrationJob;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services.MigrationJob;

public class MigrationJobService : IMigrationJobService
{
    private readonly RSSMDbContext _context;

    // Whitelist of scripts the UI is allowed to trigger.
    // Any name not in this list is rejected — prevents a user from asking the worker to run arbitrary code.
    private static readonly string[] _allowedScripts = new[]
    {
        "run_ar",
        "run_faktur",
        "run_medical_fee",
        "run_purchase_order",
        "run_journal"
    };

    private static readonly string[] _activeStatuses = new[] { "pending", "running" };

    public IReadOnlyList<string> AllowedScripts => _allowedScripts;

    public MigrationJobService(RSSMDbContext context)
    {
        _context = context;
    }

    public async Task<PagedResult<MigrationJobListItemDto>> GetAll(
        string? scriptName, string? status, int page, int pageSize, string? orderBy, string? orderType)
    {
        IQueryable<Entities.MigrationJob> query = _context.MigrationJobs;

        if (!string.IsNullOrWhiteSpace(scriptName))
            query = query.Where(x => x.ScriptName == scriptName);

        if (!string.IsNullOrWhiteSpace(status))
            query = query.Where(x => x.Status == status);

        orderBy = string.IsNullOrWhiteSpace(orderBy) ? "requestedat" : orderBy.ToLower();
        orderType = string.IsNullOrWhiteSpace(orderType) ? "desc" : orderType.ToLower();

        query = (orderBy, orderType) switch
        {
            ("id", "asc")          => query.OrderBy(x => x.Id),
            ("id", _)              => query.OrderByDescending(x => x.Id),
            ("scriptname", "asc")  => query.OrderBy(x => x.ScriptName),
            ("scriptname", _)      => query.OrderByDescending(x => x.ScriptName),
            ("targetdate", "asc")  => query.OrderBy(x => x.TargetDate),
            ("targetdate", _)      => query.OrderByDescending(x => x.TargetDate),
            ("status", "asc")      => query.OrderBy(x => x.Status),
            ("status", _)          => query.OrderByDescending(x => x.Status),
            ("startedat", "asc")   => query.OrderBy(x => x.StartedAt),
            ("startedat", _)       => query.OrderByDescending(x => x.StartedAt),
            ("finishedat", "asc")  => query.OrderBy(x => x.FinishedAt),
            ("finishedat", _)      => query.OrderByDescending(x => x.FinishedAt),
            (_, "asc")             => query.OrderBy(x => x.RequestedAt),
            _                      => query.OrderByDescending(x => x.RequestedAt)
        };

        int total = await query.CountAsync();
        if (page != -1)
            query = query.Skip((page - 1) * pageSize).Take(pageSize);

        var rows = await query.ToListAsync();
        return new PagedResult<MigrationJobListItemDto>(rows.Select(ToListDto).ToList(), total);
    }

    public async Task<MigrationJobDetailDto> GetById(int id)
    {
        var row = await _context.MigrationJobs.AsNoTracking().FirstOrDefaultAsync(x => x.Id == id);
        return row == null ? null : ToDetailDto(row);
    }

    public async Task<MigrationJobDetailDto> Create(CreateMigrationJobDto dto, User requester)
    {
        if (dto == null)
            throw new ApplicationException("Request body is required");

        var script = (dto.ScriptName ?? "").Trim().ToLower();
        if (!_allowedScripts.Contains(script))
            throw new ApplicationException($"Script '{dto.ScriptName}' is not allowed");

        if (dto.TargetDate == default)
            throw new ApplicationException("Target date is required");

        // Reject if another job for the same script is already pending/running.
        // The unique partial index on (script_name) WHERE status IN ('pending','running') also enforces this at the DB layer.
        var active = await _context.MigrationJobs
            .AsNoTracking()
            .Where(x => x.ScriptName == script && _activeStatuses.Contains(x.Status))
            .FirstOrDefaultAsync();

        if (active != null)
            throw new ApplicationException($"Script '{script}' is already {active.Status} (job #{active.Id})");

        var job = new Entities.MigrationJob
        {
            ScriptName      = script,
            TargetDate      = DateTime.SpecifyKind(dto.TargetDate.Date, DateTimeKind.Utc),
            UpdateColumns   = dto.UpdateAllColumns ? "all" : null,
            Status          = "pending",
            RequestedBy     = requester?.UserID,
            RequestedByName = requester?.DisplayName,
            RequestedAt     = DateTime.UtcNow
        };

        _context.MigrationJobs.Add(job);
        try
        {
            await _context.SaveChangesAsync();
        }
        catch (DbUpdateException)
        {
            // Race with another admin — the partial unique index rejected the insert.
            throw new ApplicationException($"Script '{script}' is already queued or running");
        }

        return ToDetailDto(job);
    }

    public async Task<List<ScriptRunningStatusDto>> GetRunningStatus()
    {
        var active = await _context.MigrationJobs
            .AsNoTracking()
            .Where(x => _activeStatuses.Contains(x.Status))
            .Select(x => new { x.ScriptName, x.Id, x.Status })
            .ToListAsync();

        return _allowedScripts.Select(name =>
        {
            var a = active.FirstOrDefault(x => x.ScriptName == name);
            return new ScriptRunningStatusDto
            {
                ScriptName      = name,
                IsRunning       = a != null,
                ActiveJobId     = a?.Id,
                ActiveJobStatus = a?.Status
            };
        }).ToList();
    }

    private static MigrationJobListItemDto ToListDto(Entities.MigrationJob x) => new()
    {
        Id              = x.Id,
        ScriptName      = x.ScriptName,
        TargetDate      = x.TargetDate,
        UpdateColumns   = x.UpdateColumns,
        Status          = x.Status,
        RequestedBy     = x.RequestedBy,
        RequestedByName = x.RequestedByName,
        RequestedAt     = x.RequestedAt,
        StartedAt       = x.StartedAt,
        FinishedAt      = x.FinishedAt,
        ExitCode        = x.ExitCode,
        ErrorMessage    = x.ErrorMessage,
        DurationSeconds = ComputeDuration(x)
    };

    private static MigrationJobDetailDto ToDetailDto(Entities.MigrationJob x) => new()
    {
        Id              = x.Id,
        ScriptName      = x.ScriptName,
        TargetDate      = x.TargetDate,
        UpdateColumns   = x.UpdateColumns,
        Status          = x.Status,
        RequestedBy     = x.RequestedBy,
        RequestedByName = x.RequestedByName,
        RequestedAt     = x.RequestedAt,
        StartedAt       = x.StartedAt,
        FinishedAt      = x.FinishedAt,
        ExitCode        = x.ExitCode,
        ErrorMessage    = x.ErrorMessage,
        DurationSeconds = ComputeDuration(x),
        Command         = x.Command,
        StdoutLog       = x.StdoutLog,
        StderrLog       = x.StderrLog,
        WorkerHost      = x.WorkerHost,
        WorkerPid       = x.WorkerPid
    };

    private static int? ComputeDuration(Entities.MigrationJob x)
    {
        if (x.StartedAt == null) return null;
        var end = x.FinishedAt ?? DateTime.UtcNow;
        return (int)Math.Max(0, (end - x.StartedAt.Value).TotalSeconds);
    }
}
