namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.EntityFrameworkCore;
using TDPBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class JournalController: BaseController
{
    private IJournalService journalService;
    private IKhanzaService khanzaService;
    private IOptionService optionService;
    private readonly ILogger<JournalController> _logger;
    private const string TAG = "Journal {0}";
    private const string LOG = "Journal {x}";
    private RSSMDbContext dbContext;
    public JournalController(RSSMDbContext dbContext, IJournalService journalService, IKhanzaService khanzaService, IOptionService optionService, ILogger<JournalController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.journalService = journalService;
        this.khanzaService = khanzaService;
        this.optionService = optionService;
        _logger = logger;
        this.dbContext = dbContext;
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "transactionNo")] string? transactionNo = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
        )
    {
        try
        {
            var data = await journalService.GetAll(keyword, status, coaCode, contactId, userSession.BranchId, transactionNo, transactionType, null, startDate, endDate, page, pageSize, orderBy, orderType);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("ledgers")]
    public async Task<IActionResult> GetLedgers(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] string? coaCode,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var data = await journalService.GetLedgers(startDate, endDate, coaCode, page, pageSize);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("summary")]
    public async Task<IActionResult> GetSummary(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] string? coaCode,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10,
        [FromQuery] string? coaType = null)
    {
        try
        {
            var data = await journalService.GetLedgerSummary(startDate, endDate, coaCode, page, pageSize, coaType);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("ledgers/export")]
    public async Task<IActionResult> LedgersExport(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] string? coaCode,
        [FromQuery] int page = -1,
        [FromQuery] int pageSize = -1)
    {
        try
        {

            var data = await journalService.GetLedgers(startDate, endDate, coaCode, page, pageSize);
            if (data == null)
            {
                return _NotFound(string.Format(TAG, "Not Found"));
            }

            string fileName = $"Legder-{DateTime.Now.ToString("yyyyMMddHHmmss")}.xlsx";
            byte[] fileContent = ExportToDocs.GenerateLedgerExcel(data.Data);

            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("balance-sheet")]
    public async Task<IActionResult> GetBalanceSheet(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        var data = await journalService.GetBalanceSheetAsync(startDate, endDate, userSession.BranchId ?? 0);

        return _Ok(data, "Balance sheet fetched successfully");
    }
    
    [Authorize(Page.TrxJournal,Action.Read)]
    [HttpGet("profit-loss")]
    public async Task<IActionResult> GetProfitLossSheet(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        var data = await journalService.GetProfitAndLosAsync(startDate, endDate, userSession.BranchId?? 0);

        return _Ok(data, "Profit Loss fetched successfully");
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("balance-sheet/export")]
    public async Task<IActionResult> ExportBalanceSheet(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var fileContent = await journalService.ExportBalanceSheet(startDate, endDate, userSession.BranchId ?? 0);
            string fileName = $"BalanceSheet_{DateTime.Now:yyyyMMddHHmmss}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("profit-loss/export")]
    public async Task<IActionResult> ExportProfitLoss(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var fileContent = await journalService.ExportProfitLoss(startDate, endDate, userSession.BranchId ?? 0);
            string fileName = $"ProfitLoss_{DateTime.Now:yyyyMMddHHmmss}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("cash-flow")]
    public async Task<IActionResult> GetCashFlow(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        var data = await journalService.GetCashFlowAsync(startDate, endDate, userSession.BranchId ?? 0);

        return _Ok(data, "Cash flow fetched successfully");
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("cash-flow/export")]
    public async Task<IActionResult> ExportCashFlow(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var fileContent = await journalService.ExportCashFlow(startDate, endDate, userSession.BranchId ?? 0);
            string fileName = $"CashFlow_{DateTime.Now:yyyyMMddHHmmss}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("history/export")]
    public async Task<IActionResult> ExportHistory(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "transactionNo")] string? transactionNo = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "asc"
        )
    {
        try
        {
            var fileContent = await journalService.ExportHistory(keyword, status, coaCode, contactId, userSession.BranchId, transactionNo, transactionType, null, startDate, endDate, orderBy, orderType);
            string fileName = $"JournalHistory_{DateTime.Now:yyyyMMddHHmmss}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("summary/export")]
    public async Task<IActionResult> ExportSummary(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] string? coaCode,
        [FromQuery] string? coaType = null)
    {
        try
        {
            var fileContent = await journalService.ExportLedgerSummary(startDate, endDate, coaCode, coaType);
            string fileName = $"LedgerSummary_{DateTime.Now:yyyyMMddHHmmss}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxJournal, Action.Update)]
    [HttpPost("posting")]
    public async Task<IActionResult> PostJournal([FromBody] List<string> transactionIds)
    {
        try
        {
            var result = await journalService.PostJournal(transactionIds);
            return _Ok(new { message = "Posting Successful", count = result });
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Get journal data from Khanza SIMRS MySQL database with pagination
    /// </summary>
    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("khanza-sync")]
    public async Task<IActionResult> GetKhanzaJournals(
        [FromQuery] DateTime startDate,
        [FromQuery] DateTime endDate,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 50)
    {
        try
        {
            var result = await khanzaService.GetJournals(startDate, endDate, page, pageSize, userSession.BranchId ?? 0);
            return _Ok(result);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export Khanza journal data to Excel
    /// </summary>
    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("khanza-sync/export")]
    public async Task<IActionResult> ExportKhanzaJournals(
        [FromQuery] DateTime startDate,
        [FromQuery] DateTime endDate)
    {
        try
        {
            var fileContent = await khanzaService.ExportJournals(startDate, endDate, userSession.BranchId ?? 0);
            if (fileContent == null)
            {
                return _NotFound("Tidak ada data untuk periode tersebut.");
            }

            string fileName = $"JurnalKhanza_{startDate:yyyyMMdd}_{endDate:yyyyMMdd}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Download General Journal Excel template
    /// </summary>
    [HttpGet("general-journal-template")]
    public IActionResult DownloadGeneralJournalTemplate()
    {
        try
        {
            string projectDirectory = AppContext.BaseDirectory;
            string templatePath = Path.Combine(projectDirectory, "Files", "Templates", "General Journal Template.xlsx");

            if (!System.IO.File.Exists(templatePath))
            {
                _logger.LogError("Template file not found at: {path}", templatePath);
                return _NotFound("Template file not found");
            }

            var fileBytes = System.IO.File.ReadAllBytes(templatePath);
            return _File(fileBytes, "xlsx", "General Journal Template.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Import General Journal from Excel file - supports multiple journals grouped by No Transaksi
    /// </summary>
    [Authorize(Page.TrxTransaction, Action.Create)]
    [HttpPost("import-general-journal")]
    public async Task<IActionResult> ImportGeneralJournal([FromForm] FileUpload fileUpload)
    {
        try
        {
            var result = await journalService.ImportGeneralJournal(fileUpload, userSession.BranchId ?? 0, userSession.UserID);
            
            if (result.Success)
            {
                return _Ok(result);
            }
            else
            {
                // If validation errors, return BadRequest with error details
                // Format matching what FE expects if possible
                return _BadRequest(result);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Import General Journal from THB Excel file - different column layout, auto-detects header row
    /// </summary>
    [Authorize(Page.TrxTransaction, Action.Create)]
    [HttpPost("import-general-journal-thb")]
    public async Task<IActionResult> ImportGeneralJournalTHB([FromForm] FileUpload fileUpload)
    {
        try
        {
            var result = await journalService.ImportGeneralJournalTHB(fileUpload, userSession.BranchId ?? 0, userSession.UserID);
            
            if (result.Success)
            {
                return _Ok(result);
            }
            else
            {
                return _BadRequest(result);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
    /// <summary>
    /// Get Unbalanced Journals
    /// </summary>
    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("unbalanced")]
    public async Task<IActionResult> GetUnbalancedJournals(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate,
        [FromQuery] int page = 1,
        [FromQuery] int pageSize = 10)
    {
        try
        {
            var data = await journalService.GetUnbalancedJournals(startDate, endDate, page, pageSize);
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    /// <summary>
    /// Export Unbalanced Journals
    /// </summary>
    [Authorize(Page.TrxJournal, Action.Read)]
    [HttpGet("unbalanced/export")]
    public async Task<IActionResult> ExportUnbalancedJournals(
        [FromQuery] DateTime? startDate,
        [FromQuery] DateTime? endDate)
    {
        try
        {
            var fileContent = await journalService.ExportUnbalancedJournals(startDate, endDate);
            string fileName = $"UnbalancedJournals_{DateTime.Now:yyyyMMddHHmmss}.xlsx";
            return _File(fileContent, "xlsx", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    // Buat/perbarui checkpoint saldo akhir bulan (optimasi performa saldo on-read).
    // Contoh: POST /Journal/Checkpoint?year=2026&month=5  (allBranch=true untuk semua branch)
    [Authorize(Page.TrxJournal, Action.Update)]
    [HttpPost("Checkpoint")]
    public async Task<IActionResult> GenerateMonthlyCheckpoint(
        [FromQuery(Name = "year")] int year,
        [FromQuery(Name = "month")] int month,
        [FromQuery(Name = "allBranch")] bool allBranch = false)
    {
        // [DINONAKTIFKAN] Penulisan checkpoint ke trx_balance dimatikan sesuai kebijakan:
        // trx_balance HANYA boleh diisi dari halaman Saldo Awal (/saldo-awal). Endpoint ini
        // sengaja tidak lagi menulis apa pun agar data saldo tidak "ngaco".
        await Task.CompletedTask;
        return _BadRequest("Checkpoint otomatis dinonaktifkan. Saldo awal hanya lewat menu Saldo Awal.");
    }
}

// Helper class for import



