using Microsoft.EntityFrameworkCore;
using Newtonsoft.Json;
using Quartz;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Services;

// Worker antrian import Jurnal Umum. Dijadwalkan via Quartz (lihat Program.cs, cron
// "AppSettings:JournalImport", default tiap 10 detik). Mengambil satu job 'pending' per tick,
// memprosesnya, lalu menulis ringkasannya ke baris job. Frontend polling ke
// GET /JournalImport/status.
//
// [DisallowConcurrentExecution] wajib: trigger 10 detik sementara satu import bisa berjalan
// lebih lama, tanpa ini Quartz menembakkan instance yang tumpang tindih.
[DisallowConcurrentExecution]
public class JournalImportWorker : IJob
{
    private const string TAG = "JournalImport";

    /// <summary>
    /// A job left 'running' longer than this is assumed dead (process killed mid-import) and
    /// marked failed. Deliberately not re-queued: the import transaction may already have
    /// committed, and re-running it would either duplicate work or fail confusingly on the
    /// duplicate-transaction-no check. Let a human decide to re-upload.
    /// </summary>
    private static readonly TimeSpan StaleAfter = TimeSpan.FromMinutes(30);

    private readonly ILogger<JournalImportWorker> _logger;
    private readonly IServiceScopeFactory _scopeFactory;

    public JournalImportWorker(ILogger<JournalImportWorker> logger, IServiceScopeFactory scopeFactory)
    {
        _logger = logger;
        _scopeFactory = scopeFactory;
    }

    public async Task Execute(IJobExecutionContext context)
    {
        try
        {
            // Service ber-scope (DbContext) -> buat scope di dalam job.
            using var scope = _scopeFactory.CreateScope();
            var db = scope.ServiceProvider.GetRequiredService<RSSMDbContext>();
            var journalService = scope.ServiceProvider.GetRequiredService<IJournalService>();

            await FailStaleJobs(db);

            var job = await ClaimNextPendingJob(db);
            if (job == null) return;

            _logger.LogInformation($"[{TAG}] Job #{job.Id} claimed: {job.FileName} ({job.ImportType})");
            await Process(db, journalService, job);
        }
        catch (Exception e)
        {
            // Never let this escape: with a 10s trigger a throwing job floods the log.
            _logger.LogError($"[{TAG}] Exception: {e.Message}");
        }
    }

    /// <summary>
    /// Claims one pending job by marking it running in the same statement, mirroring
    /// migration-worker/worker.py:70-95. FOR UPDATE SKIP LOCKED keeps it correct even if more
    /// than one instance ever runs this job.
    ///
    /// Executed over the raw ADO connection rather than EF's SqlQueryRaw on purpose: EF may
    /// wrap a raw query as `SELECT * FROM (<sql>)` to keep it composable, and an
    /// UPDATE ... RETURNING is not valid inside a subquery in Postgres. Going through
    /// DbCommand guarantees the statement reaches the server exactly as written.
    /// </summary>
    private static async Task<JournalImportRow?> ClaimNextPendingJob(RSSMDbContext db)
    {
        const string sql = @"
            UPDATE public.trx_journal_import_job
               SET status = 'running',
                   started_at = (NOW() AT TIME ZONE 'UTC'),
                   worker_host = @host
             WHERE id = ( SELECT id FROM public.trx_journal_import_job
                           WHERE status = 'pending'
                           ORDER BY id ASC
                           FOR UPDATE SKIP LOCKED
                           LIMIT 1 )
            RETURNING id, stored_path, file_name, import_type, branch_id, requested_by;";

        var connection = db.Database.GetDbConnection();
        if (connection.State != System.Data.ConnectionState.Open)
            await connection.OpenAsync();

        using var command = connection.CreateCommand();
        command.CommandText = sql;

        var hostParam = command.CreateParameter();
        hostParam.ParameterName = "host";
        hostParam.Value = Environment.MachineName;
        command.Parameters.Add(hostParam);

        using var reader = await command.ExecuteReaderAsync();
        if (!await reader.ReadAsync()) return null;

        return new JournalImportRow
        {
            Id = reader.GetInt32(0),
            StoredPath = reader.GetString(1),
            FileName = reader.GetString(2),
            ImportType = reader.GetString(3),
            BranchId = reader.IsDBNull(4) ? null : reader.GetInt32(4),
            RequestedBy = reader.IsDBNull(5) ? null : reader.GetInt32(5),
        };
    }

    private async Task Process(RSSMDbContext db, IJournalService journalService, JournalImportRow row)
    {
        var absolutePath = Path.Combine(
            Directory.GetCurrentDirectory(),
            row.StoredPath.Replace('/', Path.DirectorySeparatorChar));

        try
        {
            if (!File.Exists(absolutePath))
            {
                await Finalize(db, row.Id, JournalImportStatus.Failed,
                    errorMessage: $"File tidak ditemukan di server: {row.StoredPath}");
                return;
            }

            ImportResult result;
            using (var stream = new FileStream(absolutePath, FileMode.Open, FileAccess.Read))
            {
                result = row.ImportType == JournalImportType.Thb
                    ? await journalService.ImportGeneralJournalThbStream(stream, row.BranchId ?? 0, row.RequestedBy ?? 0)
                    : await journalService.ImportGeneralJournalStream(stream, row.BranchId ?? 0, row.RequestedBy ?? 0);
            }

            await Finalize(
                db,
                row.Id,
                result.Success ? JournalImportStatus.Success : JournalImportStatus.Failed,
                totalRows: result.TotalRows,
                journalCount: result.CreatedTransactions?.Count ?? 0,
                errorCount: result.Errors?.Count ?? 0,
                errors: result.Errors != null && result.Errors.Count > 0 ? JsonConvert.SerializeObject(result.Errors) : null,
                createdTransactions: result.CreatedTransactions != null && result.CreatedTransactions.Count > 0
                    ? JsonConvert.SerializeObject(result.CreatedTransactions)
                    : null,
                errorMessage: result.Success ? null : Truncate(result.Message, 1000));

            _logger.LogInformation($"[{TAG}] Job #{row.Id} {(result.Success ? "success" : "failed")}: {result.Message}");
        }
        catch (Exception e)
        {
            // A crash here must not leave the row stuck on 'running'.
            _logger.LogError($"[{TAG}] Job #{row.Id} exception: {e.Message}");
            await Finalize(db, row.Id, JournalImportStatus.Failed,
                errorMessage: Truncate($"Gagal memproses file: {e.Message}", 1000));
        }
    }

    private static async Task Finalize(
        RSSMDbContext db,
        int id,
        string status,
        int? totalRows = null,
        int? journalCount = null,
        int? errorCount = null,
        string? errors = null,
        string? createdTransactions = null,
        string? errorMessage = null)
    {
        var job = await db.JournalImportJobs.FirstOrDefaultAsync(x => x.Id == id);
        if (job == null) return;

        job.Status = status;
        job.FinishedAt = DateTime.UtcNow;
        job.TotalRows = totalRows;
        job.JournalCount = journalCount;
        job.ErrorCount = errorCount;
        job.Errors = errors;
        job.CreatedTransactions = createdTransactions;
        job.ErrorMessage = errorMessage;

        await db.SaveChangesAsync();
    }

    private async Task FailStaleJobs(RSSMDbContext db)
    {
        var cutoff = DateTime.UtcNow - StaleAfter;

        var stale = await db.JournalImportJobs
            .Where(x => x.Status == JournalImportStatus.Running
                        && x.StartedAt != null
                        && x.StartedAt < cutoff)
            .ToListAsync();

        if (stale.Count == 0) return;

        foreach (var job in stale)
        {
            job.Status = JournalImportStatus.Failed;
            job.FinishedAt = DateTime.UtcNow;
            job.ErrorMessage = "Proses berhenti mendadak (worker restart). Silakan upload ulang file ini.";
            _logger.LogWarning($"[{TAG}] Job #{job.Id} stale, marked failed.");
        }

        await db.SaveChangesAsync();
    }

    private static string? Truncate(string? value, int max) =>
        value == null || value.Length <= max ? value : value.Substring(0, max);

    /// <summary>
    /// Projection for the claim query. Named apart from the JournalImportJob entity so EF does
    /// not treat it as a mapped type.
    /// </summary>
    public class JournalImportRow
    {
        public int Id { get; set; }
        public string StoredPath { get; set; } = string.Empty;
        public string FileName { get; set; } = string.Empty;
        public string ImportType { get; set; } = string.Empty;
        public int? BranchId { get; set; }
        public int? RequestedBy { get; set; }
    }
}
