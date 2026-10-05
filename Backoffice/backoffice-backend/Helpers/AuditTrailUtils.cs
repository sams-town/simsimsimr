
namespace TDPBackend.Helpers;
using System;
using System.Threading.Tasks;
using Newtonsoft.Json;
using RSSMBackend.Entities;
using RSSMBackend.Services;

public class AuditTrailUtils
{
    private readonly ILogger<AuditTrailUtils> _logger;
    private readonly IAuditLogService _auditLogService;
    private readonly IHttpContextAccessor? _httpContextAccessor;

    private static readonly JsonSerializerSettings SerializerSettings = new()
    {
        ReferenceLoopHandling = ReferenceLoopHandling.Ignore
    };

    public AuditTrailUtils(ILogger<AuditTrailUtils> logger, IAuditLogService auditLogService, IHttpContextAccessor? httpContextAccessor = null)
    {
        _logger = logger;
        _auditLogService = auditLogService;
        _httpContextAccessor = httpContextAccessor;
    }

    public async Task<long> Add(string module, string action, string RefId, object BeforeData, object AfterData, int user, string status, string IpAddress, int BranchId)
    {

        var logs = new AuditLog
        {
            Module = module,
            RefId = RefId,
            Operation = action,
            BeforeData = BeforeData != null ? JsonConvert.SerializeObject(BeforeData, new JsonSerializerSettings {ReferenceLoopHandling = ReferenceLoopHandling.Ignore}) : null,
            AfterData = AfterData != null ? JsonConvert.SerializeObject(AfterData, new JsonSerializerSettings {ReferenceLoopHandling = ReferenceLoopHandling.Ignore}) : null,
            UserId = user,
            IpAddress = IpAddress,
            BranchId = BranchId,
            Status = status,
            CreatedAt = DateTime.UtcNow
        };

        await _auditLogService.Add(logs);
        return logs.Id;
    }

    public async Task<AuditLog?> Update(long logId, object AfterData,  string status)
    {
        var logs = await _auditLogService.GetByID(logId);
        if (logs == null) return null;

        logs.AfterData = AfterData != null ? JsonConvert.SerializeObject(AfterData, new JsonSerializerSettings { ReferenceLoopHandling = ReferenceLoopHandling.Ignore }) : null;
        logs.Status = status;

        await _auditLogService.Update(logs);
        return logs;
    }

    /// <summary>
    /// Records one business action on the activity timeline.
    ///
    /// Call this AFTER the caller's own SaveChangesAsync. The DbContext is scoped and shared,
    /// so this write flushes every tracked change -- calling it mid-method would commit the
    /// caller's staged entities early.
    ///
    /// Never throws: a failed log write must not roll back the action it describes.
    /// </summary>
    public async Task LogAction(
        string module,
        string operation,
        string refId,
        int? userId,
        int? branchId,
        object? detail = null,
        string status = "Success")
    {
        try
        {
            var log = new AuditLog
            {
                Module = module,
                RefId = refId,
                Operation = operation,
                BeforeData = null,
                AfterData = detail != null ? JsonConvert.SerializeObject(detail, SerializerSettings) : null,
                UserId = userId,
                IpAddress = ResolveIpAddress(),
                BranchId = branchId,
                Status = status,
                CreatedAt = DateTime.UtcNow
            };

            await _auditLogService.Add(log);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to write activity log for {Module}/{Operation} ref {RefId}", module, operation, refId);
        }
    }

    /// <summary>
    /// Records the same action against several records in one go (batch posting, bulk payment).
    /// </summary>
    public async Task LogActionRange(
        string module,
        string operation,
        IEnumerable<string> refIds,
        int? userId,
        int? branchId,
        object? detail = null,
        string status = "Success")
    {
        try
        {
            var payload = detail != null ? JsonConvert.SerializeObject(detail, SerializerSettings) : null;
            var ip = ResolveIpAddress();
            var now = DateTime.UtcNow;

            var logs = refIds.Select(refId => new AuditLog
            {
                Module = module,
                RefId = refId,
                Operation = operation,
                BeforeData = null,
                AfterData = payload,
                UserId = userId,
                IpAddress = ip,
                BranchId = branchId,
                Status = status,
                CreatedAt = now
            }).ToList();

            if (logs.Count > 0)
                await _auditLogService.AddRange(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to write activity logs for {Module}/{Operation}", module, operation);
        }
    }

    /// <summary>
    /// Same action across several records, each with its own payload. Use when the detail
    /// differs per record -- e.g. cancelling a posting, where each record carries the
    /// posting_by/posting_date about to be erased.
    /// </summary>
    public async Task LogActionEntries(
        string module,
        string operation,
        IEnumerable<(string RefId, object? Detail)> entries,
        int? userId,
        int? branchId,
        string status = "Success")
    {
        try
        {
            var ip = ResolveIpAddress();
            var now = DateTime.UtcNow;

            var logs = entries.Select(e => new AuditLog
            {
                Module = module,
                RefId = e.RefId,
                Operation = operation,
                BeforeData = null,
                AfterData = e.Detail != null ? JsonConvert.SerializeObject(e.Detail, SerializerSettings) : null,
                UserId = userId,
                IpAddress = ip,
                BranchId = branchId,
                Status = status,
                CreatedAt = now
            }).ToList();

            if (logs.Count > 0)
                await _auditLogService.AddRange(logs);
        }
        catch (Exception ex)
        {
            _logger.LogError(ex, "Failed to write activity logs for {Module}/{Operation}", module, operation);
        }
    }

    // HttpContext.Connection.RemoteIpAddress is null for in-process and test calls.
    private string? ResolveIpAddress()
    {
        return _httpContextAccessor?.HttpContext?.Connection?.RemoteIpAddress?.ToString();
    }
}
