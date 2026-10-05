using Quartz;
using RSSMBackend.Services;

// Job bulanan: membuat checkpoint saldo untuk bulan yang BARU SAJA berakhir.
// Dijadwalkan via Quartz (lihat Program.cs, cron "AppSettings:MonthlyCheckpoint").
// Bisa juga dipicu manual lewat API: POST /Journal/Checkpoint?year=YYYY&month=MM&allBranch=true
public class MonthlyCheckpointJob : IJob
{
    private const string TAG = "MonthlyCheckpoint";
    private readonly ILogger<MonthlyCheckpointJob> _logger;
    private readonly IServiceScopeFactory _scopeFactory;

    public MonthlyCheckpointJob(ILogger<MonthlyCheckpointJob> logger, IServiceScopeFactory scopeFactory)
    {
        _logger = logger;
        _scopeFactory = scopeFactory;
    }

    public async Task Execute(IJobExecutionContext context)
    {
        try
        {
            // Bulan sebelumnya, robust walau cron jalan di tanggal berapa pun:
            var firstOfThisMonth = new DateTime(DateTime.UtcNow.Year, DateTime.UtcNow.Month, 1, 0, 0, 0, DateTimeKind.Utc);
            var prevMonth = firstOfThisMonth.AddDays(-1);
            int year = prevMonth.Year;
            int month = prevMonth.Month;

            // Service ber-scope (DbContext) -> buat scope di dalam job.
            using var scope = _scopeFactory.CreateScope();
            var journalService = scope.ServiceProvider.GetRequiredService<IJournalService>();

            int written = await journalService.GenerateMonthlyCheckpoint(year, month, null);
            _logger.LogInformation($"[{TAG}] Saldo {year}-{month:D2}: {written} COA checkpoint ditulis.");
        }
        catch (Exception e)
        {
            _logger.LogError($"[{TAG}] Exception: {e.Message}");
        }
    }
}
