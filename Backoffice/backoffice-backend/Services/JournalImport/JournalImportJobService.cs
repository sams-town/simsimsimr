using Microsoft.AspNetCore.Http;
using Microsoft.EntityFrameworkCore;
using Newtonsoft.Json;
using RSSMBackend.DbContext;
using RSSMBackend.DTOs.JournalImport;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services.JournalImport;

public class JournalImportJobService : IJournalImportJobService
{
    private readonly RSSMDbContext _context;
    private readonly ILogger<JournalImportJobService> _logger;

    /// <summary>
    /// EPPlus reads the OOXML format only. The old UI advertised .xls too, which reached the
    /// parser and threw a raw exception -- reject it up front with a message instead.
    /// </summary>
    private static readonly string[] AllowedExtensions = { ".xlsx" };

    private const long MaxFileSizeBytes = 20 * 1024 * 1024; // 20 MB

    /// <summary>
    /// Relative to the app root. Under Docker the working directory is /app and
    /// /app/Uploads is a mounted volume, so queued files survive a redeploy -- unlike
    /// wwwroot/uploads (AuthController's profile-photo path), which lives inside the image.
    /// The worker may not pick a file up for minutes, so this distinction matters.
    /// </summary>
    private const string UploadSubFolder = "Uploads/journal-import";

    public JournalImportJobService(RSSMDbContext context, ILogger<JournalImportJobService> logger)
    {
        _context = context;
        _logger = logger;
    }

    public async Task<JournalImportEnqueueResultDto> Enqueue(
        List<IFormFile> files, string importType, User? requester, int branchId)
    {
        var result = new JournalImportEnqueueResultDto();

        if (files == null || files.Count == 0)
            throw new ApplicationException("Tidak ada file yang diupload.");

        if (!JournalImportType.IsValid(importType))
            throw new ApplicationException($"Jenis import '{importType}' tidak dikenali.");

        var now = DateTime.UtcNow;
        var folder = Path.Combine(Directory.GetCurrentDirectory(), UploadSubFolder, now.ToString("yyyy"), now.ToString("MM"));
        Directory.CreateDirectory(folder);

        foreach (var file in files)
        {
            var reject = Validate(file);
            if (reject != null)
            {
                result.Rejected.Add(new JournalImportRejectedFileDto { FileName = file.FileName, Reason = reject });
                continue;
            }

            // Ticks keeps concurrent uploads of the same name apart.
            var safeName = Path.GetFileName(file.FileName);
            var storedName = $"{DateTime.UtcNow.Ticks}_{safeName}";
            var absolutePath = Path.Combine(folder, storedName);

            using (var stream = new FileStream(absolutePath, FileMode.Create))
            {
                await file.CopyToAsync(stream);
            }

            var job = new JournalImportJob
            {
                FileName = safeName,
                StoredPath = Path.Combine(UploadSubFolder, now.ToString("yyyy"), now.ToString("MM"), storedName)
                                 .Replace('\\', '/'),
                FileSize = file.Length,
                ImportType = importType,
                Status = JournalImportStatus.Pending,
                BranchId = branchId,
                RequestedBy = requester?.UserID,
                RequestedByName = requester?.DisplayName,
                RequestedAt = DateTime.UtcNow
            };

            _context.JournalImportJobs.Add(job);
            await _context.SaveChangesAsync();

            result.Queued.Add(ToListItem(job));
        }

        if (result.Queued.Count == 0 && result.Rejected.Count > 0)
            throw new ApplicationException($"Semua file ditolak: {string.Join("; ", result.Rejected.Select(r => $"{r.FileName} ({r.Reason})"))}");

        return result;
    }

    private static string? Validate(IFormFile file)
    {
        if (file.Length == 0)
            return "File kosong";

        if (file.Length > MaxFileSizeBytes)
            return $"Ukuran melebihi {MaxFileSizeBytes / (1024 * 1024)} MB";

        var ext = Path.GetExtension(file.FileName)?.ToLowerInvariant();
        if (string.IsNullOrEmpty(ext) || !AllowedExtensions.Contains(ext))
            return "Hanya file .xlsx yang didukung";

        return null;
    }

    public async Task<PagedResult<JournalImportJobListItemDto>> GetAll(
        string? status, string? importType, int page, int pageSize, string? orderBy, string? orderType)
    {
        IQueryable<JournalImportJob> query = _context.JournalImportJobs.AsNoTracking();

        if (!string.IsNullOrWhiteSpace(status))
            query = query.Where(x => x.Status == status);

        if (!string.IsNullOrWhiteSpace(importType))
            query = query.Where(x => x.ImportType == importType);

        orderBy = string.IsNullOrWhiteSpace(orderBy) ? "requestedat" : orderBy.ToLower();
        orderType = string.IsNullOrWhiteSpace(orderType) ? "desc" : orderType.ToLower();
        bool asc = orderType == "asc";

        query = (orderBy, asc) switch
        {
            ("id", true) => query.OrderBy(x => x.Id),
            ("id", false) => query.OrderByDescending(x => x.Id),
            ("filename", true) => query.OrderBy(x => x.FileName),
            ("filename", false) => query.OrderByDescending(x => x.FileName),
            ("status", true) => query.OrderBy(x => x.Status),
            ("status", false) => query.OrderByDescending(x => x.Status),
            ("requestedat", true) => query.OrderBy(x => x.RequestedAt),
            _ => query.OrderByDescending(x => x.RequestedAt),
        };

        int total = await query.CountAsync();

        if (page != -1)
            query = query.Skip((page - 1) * pageSize).Take(pageSize);

        var rows = await query.ToListAsync();
        return new PagedResult<JournalImportJobListItemDto>(rows.Select(ToListItem).ToList(), total);
    }

    public async Task<JournalImportJobDetailDto?> GetById(int id)
    {
        var job = await _context.JournalImportJobs.AsNoTracking().FirstOrDefaultAsync(x => x.Id == id);
        if (job == null) return null;

        var dto = new JournalImportJobDetailDto
        {
            WorkerHost = job.WorkerHost,
            Errors = Deserialize<List<JournalImportErrorDto>>(job.Errors) ?? new(),
            CreatedTransactions = Deserialize<List<JournalImportCreatedTransactionDto>>(job.CreatedTransactions) ?? new(),
        };
        CopyListFields(job, dto);
        return dto;
    }

    public async Task<JournalImportQueueStatusDto> GetQueueStatus()
    {
        var counts = await _context.JournalImportJobs
            .AsNoTracking()
            .Where(x => x.Status == JournalImportStatus.Pending || x.Status == JournalImportStatus.Running)
            .GroupBy(x => x.Status)
            .Select(g => new { Status = g.Key, Count = g.Count() })
            .ToListAsync();

        return new JournalImportQueueStatusDto
        {
            PendingCount = counts.FirstOrDefault(c => c.Status == JournalImportStatus.Pending)?.Count ?? 0,
            RunningCount = counts.FirstOrDefault(c => c.Status == JournalImportStatus.Running)?.Count ?? 0,
        };
    }

    public async Task<(byte[] Content, string FileName)?> GetFileContent(int id)
    {
        var job = await _context.JournalImportJobs.AsNoTracking().FirstOrDefaultAsync(x => x.Id == id);
        if (job == null) return null;

        var absolutePath = ResolvePath(job.StoredPath);
        if (!File.Exists(absolutePath))
        {
            _logger.LogWarning("Journal import file missing on disk for job {Id}: {Path}", id, absolutePath);
            return null;
        }

        var content = await File.ReadAllBytesAsync(absolutePath);
        return (content, job.FileName);
    }

    public async Task<bool> Cancel(int id)
    {
        var job = await _context.JournalImportJobs.FirstOrDefaultAsync(x => x.Id == id);

        // Only a job the worker has not claimed yet. Once it is running, cancelling would
        // race the import transaction.
        if (job == null || job.Status != JournalImportStatus.Pending) return false;

        var absolutePath = ResolvePath(job.StoredPath);
        _context.JournalImportJobs.Remove(job);
        await _context.SaveChangesAsync();

        try
        {
            if (File.Exists(absolutePath)) File.Delete(absolutePath);
        }
        catch (Exception ex)
        {
            // The row is already gone; an orphan file is not worth failing the request over.
            _logger.LogWarning(ex, "Could not delete cancelled import file {Path}", absolutePath);
        }

        return true;
    }

    private static string ResolvePath(string storedPath) =>
        Path.Combine(Directory.GetCurrentDirectory(), storedPath.Replace('/', Path.DirectorySeparatorChar));

    private static T? Deserialize<T>(string? json) where T : class
    {
        if (string.IsNullOrWhiteSpace(json)) return null;
        try
        {
            return JsonConvert.DeserializeObject<T>(json);
        }
        catch
        {
            // A malformed payload should not take down the detail view.
            return null;
        }
    }

    private static JournalImportJobListItemDto ToListItem(JournalImportJob job)
    {
        var dto = new JournalImportJobListItemDto();
        CopyListFields(job, dto);
        return dto;
    }

    private static void CopyListFields(JournalImportJob job, JournalImportJobListItemDto dto)
    {
        dto.Id = job.Id;
        dto.FileName = job.FileName;
        dto.FileSize = job.FileSize;
        dto.ImportType = job.ImportType;
        dto.Status = job.Status;
        dto.BranchId = job.BranchId;
        dto.RequestedBy = job.RequestedBy;
        dto.RequestedByName = job.RequestedByName;
        dto.RequestedAt = job.RequestedAt;
        dto.StartedAt = job.StartedAt;
        dto.FinishedAt = job.FinishedAt;
        dto.TotalRows = job.TotalRows;
        dto.JournalCount = job.JournalCount;
        dto.ErrorCount = job.ErrorCount;
        dto.ErrorMessage = job.ErrorMessage;
        dto.DurationSeconds = ComputeDuration(job);
    }

    /// <summary>Null until started; a running job reports elapsed time so the UI can tick.</summary>
    private static int? ComputeDuration(JournalImportJob job)
    {
        if (job.StartedAt == null) return null;
        var end = job.FinishedAt ?? DateTime.UtcNow;
        var seconds = (end - job.StartedAt.Value).TotalSeconds;
        return seconds < 0 ? 0 : (int)seconds;
    }
}
