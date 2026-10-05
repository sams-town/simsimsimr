namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore.Storage;
using Microsoft.AspNetCore.Http.Features;
using TDPBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class JobsController : BaseController
{
    private ITransactionService transactionService;
    private IJournalService journalService;
    private AuditTrailUtils auditTrailUtils;
    private IJournalJobService journalJobService;
    private ICOAService coaService;
    private readonly ILogger<JobsController> _logger;
    private const string TAG = "Transaction {0}";
    private const string LOG = "Transaction {x}";
    private RSSMDbContext dbContext;
    private ZeemarDbContext zeemarDbContext;
    public JobsController(ZeemarDbContext zeemarDbContext,RSSMDbContext dbContext,ICOAService coaService,IJournalJobService journalJobService,AuditTrailUtils auditTrailUtils,IJournalService journalService,ITransactionService transactionService,ILogger<JobsController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.transactionService = transactionService;
        this.journalService = journalService;
        this.auditTrailUtils = auditTrailUtils;
        this.journalJobService = journalJobService;
        this.coaService = coaService;

        _logger = logger;
        this.dbContext = dbContext;
        this.zeemarDbContext = zeemarDbContext;
    }

    [Authorize(Page.TrxTransaction, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] JobJournalRequest item)
    {
        try
        {
            var resultDetails = await journalJobService.Get(item.JournalDate);
            var results = await journalJobService.GetTransaction(item.JournalDate);
            
            using (var transaction = dbContext.Database.BeginTransaction())
            {
                dbContext.Database.UseTransaction(transaction.GetDbTransaction());
                foreach (var res in results)
                {
                    string autoNumber = await transactionService.GetCode("Payment", userSession?.BranchId ?? 1);
                    DateTime trxDate = DateTime.SpecifyKind(res.Tanggal ?? DateTime.Now, DateTimeKind.Utc);
                    string jobJournalNo = await journalService.GenerateJournalNo(trxDate);

                    var trx = new Transaction()
                    {
                        TransactionId = Guid.NewGuid().ToString(),
                        TransactionDate = trxDate,
                        TransactionName = null,
                        TransactionRef = null,
                        TransactionType = "Payment",
                        TransactionNo = autoNumber,
                        JournalNo = jobJournalNo,
                        Description = null,
                        CoaCode =  "1",
                        CoaName = "A S E T",
                        CoaParentCode = null,
                        CoaParentName = null,
                        status = Status.Published.ToString(),
                        BranchId = userSession?.BranchId ?? 1,
                        CreatedBy = userSession?.UserID ?? 1,
                        Amount = null,
                        CreatedDate = DateTime.UtcNow
                    };
                    await transactionService.Add(trx);
                    
                    var detail = resultDetails.Where(x => x.NoJurnal == res.NoJurnal).ToList();
                    foreach (var d in detail)
                    {   
                        var coaMappings = await coaService.GetMappingByCode(d.KodeAkun);
                        var coaDesc = await coaService.GetByCode(coaMappings != null ? coaMappings.CoaDest : "0", userSession?.BranchId ?? 1);
                        var journal = new Journal()
                        {
                            JournalRef = d.KodeAkun,
                            JournalDesc = d.Keterangan,
                            CoaCode = coaDesc != null ? coaDesc.CoaCode : "0",
                            TransactionId = trx.TransactionId,
                            Source = trx.TransactionType,
                            CoaName = coaDesc != null ? coaDesc?.CoaName : "0",
                            CoaParentCode = null,
                            CoaParentName = null,
                            status = Status.Published.ToString(),
                            BranchId = userSession?.BranchId ?? 1,
                            CreatedBy = userSession?.UserID ?? 1,
                            CreatedDate = trx.CreatedDate,
                            IsPremier = false,
                            DebitAmount = d.Debet,
                            CreditAmount = d.Kredit,
                            PONumber = d.NoPO,
                            ReceiptDate = d.TglPenerimaan,
                            PODate = d.TglPenerimaan,
                            SupplierName = d.NamaSuplier,
                            TotalTransaction = d.JmlTransaksi
                        };
                        await journalService.Add(journal);
                    }
                }
                transaction.Commit();
                return _Ok(string.Format(TAG, "data has been successfully saved"));
            }        
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
             if (dbContext.Database.CurrentTransaction != null)
                dbContext.Database.RollbackTransaction();

            return _BadRequest(e.Message);
        }
    }
}


