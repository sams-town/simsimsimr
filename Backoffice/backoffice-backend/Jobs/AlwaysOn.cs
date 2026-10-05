using Microsoft.Extensions.Options;
using Newtonsoft.Json;
using Quartz;
using RSSMBackend.Helpers;

public class AlwaysOn : IJob
{
    private const string TAG = "AlwaysOn";
    private readonly ILogger<AlwaysOn> _logger;
    private readonly AppSettings _appSettings;

    public AlwaysOn(ILogger<AlwaysOn> logger, IOptions<AppSettings> appSettings)
    {
        _logger = logger;
        _appSettings = appSettings.Value;
    }

    public async Task Execute(IJobExecutionContext context)
    {
        await WakeUP(context);
    }

    public async Task WakeUP(IJobExecutionContext context)
    {
        try
        {

            using (var httpClient = new HttpClient())
            {
                httpClient.BaseAddress = new Uri(_appSettings.BackendUrl);
                HttpResponseMessage response = await httpClient.GetAsync("");
                string responseContent = await response.Content.ReadAsStringAsync();
                _logger.LogInformation($"[SAR_SCHEDULER] {responseContent}");
            }

        }
        catch (Exception e)
        {
            _logger.LogError($"[SAR_SCHEDULER] Exception: {JsonConvert.SerializeObject(e)}");
        }
    }
}