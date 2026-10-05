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
using Newtonsoft.Json;

[ApiController]
[Route("[controller]")]
public class TransactionController : BaseController
{
    private ITransactionService transactionService;
    private IJournalService journalService;
    private AuditTrailUtils auditTrailUtils;
    private readonly ILogger<TransactionController> _logger;
    private const string TAG = "Transaction {0}";
    private const string LOG = "Transaction {x}";
    private RSSMDbContext dbContext;
    public TransactionController(RSSMDbContext dbContext,AuditTrailUtils auditTrailUtils,IJournalService journalService,ITransactionService transactionService,ILogger<TransactionController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.transactionService = transactionService;
        this.journalService = journalService;
        this.auditTrailUtils = auditTrailUtils;

        _logger = logger;
        this.dbContext = dbContext;
    }
   
    [Authorize(Page.TrxTransaction,Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "coaParentCode")] string? coaParentCode = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await transactionService.GetAll(keyword,coaCode,transactionType,contactId,userSession.BranchId,status,coaParentCode,startDate,endDate,page,pageSize,orderBy,orderType);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction,Action.Read)]
    [HttpGet("journal")]
    public async Task<IActionResult> GetAllJournal(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "coaParentCode")] string? coaParentCode = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await transactionService.GetAllDetail(keyword,coaCode,transactionType,contactId,userSession.BranchId,status,coaParentCode,startDate,endDate,page,pageSize,orderBy,orderType);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
    [Authorize(Page.TrxTransaction,Action.Read)]
    [HttpGet("journal/export")]
    public async Task<IActionResult> ExportJournal(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "coaParentCode")] string? coaParentCode = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null)
    {
        try
        {
            var fileContent = await transactionService.ExportJournal(keyword, coaCode, transactionType, contactId, userSession.BranchId, status, coaParentCode, startDate, endDate, orderBy, orderType);
            return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $"JournalReport_{DateTime.Now:yyyyMMddHHmmss}.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction,Action.Read)]
    [HttpGet("journal/export-flat")]
    public async Task<IActionResult> ExportJournalFlat(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "coaParentCode")] string? coaParentCode = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null)
    {
        try
        {
            var fileContent = await transactionService.ExportJournalFlat(keyword, coaCode, transactionType, contactId, userSession.BranchId, status, coaParentCode, startDate, endDate, orderBy, orderType);
            return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $"JournalReport_{DateTime.Now:yyyyMMddHHmmss}.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction,Action.Read)]
    [HttpGet("export")]
    public async Task<IActionResult> ExportTransaction(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "coaParentCode")] string? coaParentCode = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null)
    {
        try
        {
            var fileContent = await transactionService.ExportTransactionList(keyword, coaCode, transactionType, contactId, userSession.BranchId, status, coaParentCode, startDate, endDate, orderBy, orderType);
            string filename = "TransactionList";
            if (transactionType?.ToLower() == "transfer") filename = "DaftarTransfer";
            if (transactionType?.ToLower() == "revenue" || transactionType?.ToLower() == "deposit") filename = "DaftarPenerimaan";
            if (transactionType?.ToLower() == "expense" || transactionType?.ToLower() == "payment") filename = "DaftarPengeluaran";
            
            return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $"{filename}_{DateTime.Now:yyyyMMddHHmmss}.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction,Action.Read)]
    [HttpGet("export-flat")]
    public async Task<IActionResult> ExportTransactionFlat(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "createddate",
        [FromQuery(Name = "orderType")] string? orderType = "desc",
        [FromQuery(Name = "contactId")] int? contactId = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "coaCode")] string? coaCode = null,
        [FromQuery(Name = "coaParentCode")] string? coaParentCode = null,
        [FromQuery(Name = "transactionType")] string? transactionType = null,
        [FromQuery(Name = "startDate")] DateTime? startDate = null,
        [FromQuery(Name = "endDate")] DateTime? endDate = null)
    {
        try
        {
            var fileContent = await transactionService.ExportTransactionListFlat(keyword, coaCode, transactionType, contactId, userSession.BranchId, status, coaParentCode, startDate, endDate, orderBy, orderType);
            string filename = "TransactionList_Flat";
            if (transactionType?.ToLower() == "transfer") filename = "DaftarTransfer_Flat";
            if (transactionType?.ToLower() == "revenue" || transactionType?.ToLower() == "deposit") filename = "DaftarPenerimaan_Flat";
            if (transactionType?.ToLower() == "expense" || transactionType?.ToLower() == "payment") filename = "DaftarPengeluaran_Flat";
            
            return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", $"{filename}_{DateTime.Now:yyyyMMddHHmmss}.xlsx");
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction,Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] TransactionRequest item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            // Lock tutup buku: tolak transaksi baru pada periode yang sudah ditutup.
            var periodClosingService = HttpContext.RequestServices.GetService(typeof(IPeriodClosingService)) as IPeriodClosingService;
            await periodClosingService.EnsureNotClosed(item.TransactionDate, userSession.BranchId);

            using (var transaction = dbContext.Database.BeginTransaction())
            {
                dbContext.Database.UseTransaction(transaction.GetDbTransaction());

                var validator = new TransactionValidator();
                var result = validator.Validate(item);

                if (result.IsValid)
                {
                    var cOAService = HttpContext.RequestServices.GetService(typeof(ICOAService)) as ICOAService;

                    Coa coa = null;
                    if (item.CoaCode != null)
                        coa = await cOAService.GetByCode(item.CoaCode, userSession.BranchId);

                    // if(coa == null)
                    //     return _BadRequest(string.Format(TAG,"COA " + item.CoaCode + " Not Found"));

                    bool autoId = false;
                    if (string.IsNullOrEmpty(item.TransactionNo))
                    {
                        string autoNumber = await transactionService.GetCode(item.TransactionType, userSession.BranchId);
                        item.TransactionNo = autoNumber;
                        await transactionService.UpdateCode(item.TransactionType, autoNumber, userSession.BranchId);
                        autoId = true;
                    }

                    var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;
                    string journalNo = await journalService.GenerateJournalNo(item.TransactionDate ?? DateTime.Now);

                    var trx = new Transaction()
                    {
                        TransactionId = Guid.NewGuid().ToString(),
                        TransactionDate = item.TransactionDate,
                        TransactionName = item.TransactionName,
                        TransactionRef = item.TransactionRef,
                        TransactionType = item.TransactionType,
                        TransactionNo = item.TransactionNo,
                        JournalNo = journalNo,
                        Description = item.Description,
                        CoaCode = item.CoaCode,
                        CoaName = coa != null ? coa.CoaName : null,
                        CoaParentCode = coa != null ? coa.ParentCode : null,
                        CoaParentName = coa != null ? coa.ParentName : null,
                        status = Status.Pending.ToString(),
                        BranchId = userSession.BranchId,
                        CreatedBy = userSession.UserID,
                        CreatedDate = DateTime.UtcNow,
                        IsAutoId = autoId
                    };

                    trx.Amount = 0;
                    // For GENERAL journal, only sum debit amounts (since debit = credit for balanced journal)
                    if (JournalSource.JournalEntry.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                    {
                        foreach (TransactionRequest.TransactionDetail detail in item.Details)
                        {
                            if (detail.Position?.ToLower() == "debit")
                                trx.Amount += detail.Amount ?? 0;
                        }
                    }
                    else
                    {
                        foreach (TransactionRequest.TransactionDetail detail in item.Details)
                            trx.Amount += detail.Amount ?? 0;
                    }

                    await transactionService.Add(trx);
                    if (trx.CoaCode != null && !(trx.TransactionType?.ToLower().Equals(JournalSource.JournalEntry.ToString().ToLower()) ?? false))
                    {
                        item.Details.Insert(0, new TransactionRequest.TransactionDetail()
                        {
                            CoaCode = trx.CoaCode,
                            Description = trx.Description,
                            Amount = trx.Amount,
                            IsPremier = true
                        });
                    }

                    foreach (TransactionRequest.TransactionDetail detail in item.Details)
                    {
                        var journal = new Journal()
                        {
                            JournalRef = trx.TransactionRef,
                            JournalDesc = detail.Description,
                            CoaCode = detail.CoaCode,
                            TransactionId = trx.TransactionId
                        };

                        // return _Ok(detail);
                        var coaDetail = await cOAService.GetByCode(detail.CoaCode, userSession.BranchId);
                        if (coaDetail == null)
                            return _BadRequest(string.Format(TAG, "COA " + detail.CoaCode + " Not Found"));

                        journal.Source = trx.TransactionType;
                        journal.CoaName = coaDetail.CoaName;
                        journal.CoaParentCode = coaDetail.ParentCode;
                        journal.CoaParentName = coaDetail.ParentName;
                        journal.status = Status.Pending.ToString();
                        journal.BranchId = userSession.BranchId;
                        journal.CreatedBy = userSession.UserID;
                        journal.CreatedDate = trx.CreatedDate;
                        journal.IsPremier = detail.IsPremier != null ? (coa == null ? false : detail.IsPremier) : false;

                        if (JournalSource.Deposit.ToString().ToLower().Equals(trx.TransactionType.ToLower()))
                        {
                            if (journal.IsPremier != null && journal.IsPremier.Value)
                            {
                                journal.DebitAmount = detail.Amount;
                                journal.CreditAmount = 0;
                            }
                            else
                            {
                                journal.DebitAmount = 0;
                                journal.CreditAmount = detail.Amount;
                            }
                        }
                        else if (JournalSource.Payment.ToString().ToLower().Equals(trx.TransactionType.ToLower()) ||
                            JournalSource.Transfer.ToString().ToLower().Equals(trx.TransactionType.ToLower())
                        )
                        {
                            if (journal.IsPremier != null && journal.IsPremier.Value)
                            {
                                journal.DebitAmount = 0;
                                journal.CreditAmount = detail.Amount;
                            }
                            else
                            {
                                journal.DebitAmount = detail.Amount;
                                journal.CreditAmount = 0;
                            }
                        }
                        else if (JournalSource.JournalEntry.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                        {
                            if (detail.Position?.ToLower() == "debit")
                            {
                                journal.DebitAmount = detail.Amount ?? 0;
                                journal.CreditAmount = 0;
                            }
                            else
                            {
                                journal.DebitAmount = 0;
                                journal.CreditAmount = detail.Amount ?? 0;
                            }
                        }
                        await journalService.Add(journal);
                        
                    }

                    await auditTrailUtils.Add("Transaction", "Create " + trx.TransactionType, trx.TransactionId, null, item, userSession.UserID, trx.status, HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);
                    transaction.Commit();

                    return _Ok(trx.TransactionId, string.Format(TAG, "data has been successfully saved"));
                }
                else
                {
                    var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                    return _BadRequest(errorMessages);
                }
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

    [Authorize(Page.TrxTransaction, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Edit([FromBody] TransactionRequest item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            // Lock tutup buku: tolak perubahan bila tanggal baru berada di periode tertutup.
            var periodClosingService = HttpContext.RequestServices.GetService(typeof(IPeriodClosingService)) as IPeriodClosingService;
            await periodClosingService.EnsureNotClosed(item.TransactionDate, userSession.BranchId);

            using (var transaction = dbContext.Database.BeginTransaction())
            {
                dbContext.Database.UseTransaction(transaction.GetDbTransaction());

                var validator = new TransactionValidator();
                var result = validator.Validate(item);

                if (!result.IsValid)
                {
                    var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                    return _BadRequest(errorMessages);
                }

                var trx = await transactionService.GetForUpdateByID(item.TransactionId,userSession.BranchId);
                if (trx == null)
                    return _BadRequest(string.Format(TAG, "Transaction not found"));

                // ...dan tolak juga bila tanggal LAMA transaksi berada di periode tertutup.
                await periodClosingService.EnsureNotClosed(trx.TransactionDate, userSession.BranchId);

                var cOAService = HttpContext.RequestServices.GetService(typeof(ICOAService)) as ICOAService;
                Coa coa = await cOAService.GetByCode(item.CoaCode, userSession.BranchId);

                if(coa == null)
                   return _BadRequest(string.Format(TAG,"COA " + item.CoaCode + " Not Found"));

                var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;
                var dbJournals = await journalService.GetForUpdateByID(item.TransactionId,userSession.BranchId);
                trx.Details = dbJournals;
                
                var logsId = await auditTrailUtils.Add("Transaction", "Update " + trx.TransactionType, trx.TransactionId, trx, null, userSession.UserID, trx.status, HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);
    

                bool autoId = false;
                if (string.IsNullOrEmpty(item.TransactionNo))
                {
                    string autoNumber = await transactionService.GetCode(item.TransactionType, userSession.BranchId);
                    item.TransactionNo = autoNumber;
                    await transactionService.UpdateCode(item.TransactionType, autoNumber, userSession.BranchId);
                    autoId = true;
                }    

                trx.CoaCode = item.CoaCode;
                trx.CoaName = coa != null ? coa.CoaName : null;
                trx.CoaParentCode = coa != null ? coa.ParentCode : null;
                trx.CoaParentName = coa != null ? coa.ParentName : null;
                trx.TransactionNo = item.TransactionNo;
                trx.TransactionDate = item.TransactionDate;
                trx.TransactionName = item.TransactionName;
                trx.TransactionRef = item.TransactionRef;
                trx.TransactionType = item.TransactionType;
                trx.Description = item.Description;
                trx.UpdatedBy = userSession.UserID;
                trx.UpdatedDate = DateTime.UtcNow;

                // For GENERAL journal, only sum debit amounts (since debit = credit for balanced journal)
                trx.Amount = 0;
                if (JournalSource.JournalEntry.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                {
                    foreach (TransactionRequest.TransactionDetail detail in item.Details)
                    {
                        if (detail.Position?.ToLower() == "debit")
                            trx.Amount += detail.Amount ?? 0;
                    }
                }
                else
                {
                    foreach (TransactionRequest.TransactionDetail detail in item.Details)
                        trx.Amount += detail.Amount ?? 0;
                }
             
                if (trx.CoaCode != null && !(trx.TransactionType?.ToLower().Equals(JournalSource.JournalEntry.ToString().ToLower()) ?? false))
                {
                    item.Details.Insert(0, new TransactionRequest.TransactionDetail()
                    {
                        CoaCode = trx.CoaCode,
                        Description = trx.Description,
                        Amount = trx.Amount,
                        IsPremier = true
                    });
                }

               
                foreach (var dbJournal in dbJournals)
                {
                    if (!item.Details.Any(d => d.JournalId == dbJournal.JournalId))
                        await journalService.Remove(dbJournal);
                }

                foreach (var detail in item.Details)
                {
                    if (detail.JournalId.HasValue)
                    {
                        // Update
                        var dbJournal = dbJournals.FirstOrDefault(j => j.JournalId == detail.JournalId.Value);
                        if (dbJournal != null)
                        {
                            var coaDetail = await cOAService.GetByCode(detail.CoaCode, userSession.BranchId);
                            if (coaDetail == null)
                                return _BadRequest(string.Format(TAG, "COA " + detail.CoaCode + " Not Found"));

                            dbJournal.JournalDesc = detail.Description;
                            dbJournal.CoaCode = detail.CoaCode;
                            dbJournal.CoaName = coaDetail.CoaName;
                            dbJournal.CoaParentCode = coaDetail.ParentCode;
                            dbJournal.CoaParentName = coaDetail.ParentName;
                            dbJournal.UpdatedBy = userSession.UserID;
                            dbJournal.UpdatedDate = DateTime.UtcNow;

                            if (JournalSource.Deposit.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                            {
                                if (detail.IsPremier)
                                {
                                    dbJournal.DebitAmount = detail.Amount;
                                    dbJournal.CreditAmount = 0;
                                }
                                else
                                {
                                    dbJournal.DebitAmount = 0;
                                    dbJournal.CreditAmount = detail.Amount;
                                }
                            }
                            else if (JournalSource.Payment.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase) ||
                                    JournalSource.Transfer.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                            {
                                if (detail.IsPremier)
                                {
                                    dbJournal.DebitAmount = 0;
                                    dbJournal.CreditAmount = detail.Amount;
                                }
                                else
                                {
                                    dbJournal.DebitAmount = detail.Amount;
                                    dbJournal.CreditAmount = 0;
                                }
                            }
                            else if (JournalSource.JournalEntry.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                            {
                                if (detail.Position?.ToLower() == "debit")
                                {
                                    dbJournal.DebitAmount = detail.Amount ?? 0;
                                    dbJournal.CreditAmount = 0;
                                }
                                else
                                {
                                    dbJournal.DebitAmount = 0;
                                    dbJournal.CreditAmount = detail.Amount ?? 0;
                                }
                            }
                        }

                        await journalService.Update(dbJournal);
                    }
                    else
                    {
                        var coaDetail = await cOAService.GetByCode(detail.CoaCode, userSession.BranchId);
                        if (coaDetail == null)
                            return _BadRequest(string.Format(TAG, "COA " + detail.CoaCode + " Not Found"));

                        var journal = new Journal()
                        {
                            JournalRef = trx.TransactionRef,
                            JournalDesc = detail.Description,
                            CoaCode = detail.CoaCode,
                            CoaName = coaDetail.CoaName,
                            CoaParentCode = coaDetail.ParentCode,
                            CoaParentName = coaDetail.ParentName,
                            TransactionId = trx.TransactionId,
                            status = Status.Pending.ToString(),
                            BranchId = userSession.BranchId,
                            CreatedBy = userSession.UserID,
                            CreatedDate = DateTime.UtcNow,
                            IsPremier = detail.IsPremier,
                            Source = trx.TransactionType
                        };

                        if (JournalSource.Deposit.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                        {
                            journal.DebitAmount = detail.IsPremier ? detail.Amount : 0;
                            journal.CreditAmount = detail.IsPremier ? 0 : detail.Amount;
                        }
                        else if (JournalSource.Payment.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase) ||
                                JournalSource.Transfer.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                        {
                            journal.DebitAmount = detail.IsPremier ? 0 : detail.Amount ?? 0;
                            journal.CreditAmount = detail.IsPremier ? detail.Amount ?? 0 : 0;
                        }
                        else if (JournalSource.JournalEntry.ToString().Equals(trx.TransactionType, StringComparison.OrdinalIgnoreCase))
                        {
                            if (detail.Position?.ToLower() == "debit")
                            {
                                journal.DebitAmount = detail.Amount ?? 0;
                                journal.CreditAmount = 0;
                            }
                            else
                            {
                                journal.DebitAmount = 0;
                                journal.CreditAmount = detail.Amount ?? 0;
                            }
                        }

                        await journalService.Add(journal);
                    }
                }

                await dbContext.SaveChangesAsync();
                
                await auditTrailUtils.Update(logsId, trx, trx.status);
                transaction.Commit();

                return _Ok(trx.TransactionId, string.Format(TAG, "data has been successfully updated"));
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


    [Authorize(Page.TrxTransaction, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(string id)
    {
        try
        {
            var data = await transactionService.GetByID(id,userSession.BranchId);

            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            // Hanya transaksi yang BELUM diposting yang boleh dihapus. Jurnal Published sudah
            // ikut dihitung ke saldo (JournalService.Posted), jadi menghapusnya akan mengubah
            // neraca / laba-rugi / buku besar secara retroaktif dan membuat checkpoint
            // trx_balance (Saldo Awal) basi. Koreksi jurnal Published lewat Void + jurnal baru.
            if (string.Equals(data.status, Status.Published.ToString(), StringComparison.OrdinalIgnoreCase))
                return _BadRequest("Transaksi yang sudah diposting (Published) tidak bisa dihapus. Gunakan Void lalu buat jurnal koreksi.");

            if (string.Equals(data.status, Status.Deleted.ToString(), StringComparison.OrdinalIgnoreCase))
                return _BadRequest("Transaksi ini sudah dihapus.");

            // Lock tutup buku: tolak hapus jurnal pada periode yang sudah ditutup.
            var periodClosingService = HttpContext.RequestServices.GetService(typeof(IPeriodClosingService)) as IPeriodClosingService;
            await periodClosingService.EnsureNotClosed(data.TransactionDate, userSession.BranchId);

            using (var transaction = dbContext.Database.BeginTransaction())
            {
                dbContext.Database.UseTransaction(transaction.GetDbTransaction());

                var details = await journalService.GetForUpdateByID(id, userSession.BranchId);
                data.Details = details;

                var logId = await auditTrailUtils.Add("Transaction", "Delete " + data.TransactionType , data.TransactionId, data, null, userSession.UserID, Status.Deleted.ToString(), HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);

                data.UpdatedDate = DateTime.UtcNow;
                data.UpdatedBy = userSession.UserID;
                data.status = Status.Deleted.ToString();

                foreach (var journal in details)
                {
                    journal.status = Status.Deleted.ToString();
                    journal.UpdatedBy = userSession.UserID;
                    journal.UpdatedDate = data.UpdatedDate;

                    await journalService.Update(journal);
                }

                await transactionService.Remove(data);

                await auditTrailUtils.Update(logId, data, Status.Deleted.ToString());
                transaction.Commit();

                
                return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
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
    
    // Void dipisah dari Delete: hak hapus dibatasi Administrator/Manager, sementara Void
    // tetap boleh untuk role di bawahnya. Action.Delete tetap diterima karena hak hapus
    // mencakup void — sekaligus supaya deploy kode tidak harus menunggu migrasi grant.
    [Authorize(Page.TrxTransaction, Action.Void, Page.TrxTransaction, Action.Delete)]
    [HttpDelete("void/{id}")]
    public async Task<IActionResult> Voids(string id)
    {
        try
        {
            var data = await transactionService.GetByID(id,userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            if (string.Equals(data.status, Status.Published.ToString(), StringComparison.OrdinalIgnoreCase))
                return _BadRequest("Transaksi yang sudah diposting (Published) tidak bisa di-void.");

            // Lock tutup buku: tolak void jurnal pada periode yang sudah ditutup.
            var periodClosingService = HttpContext.RequestServices.GetService(typeof(IPeriodClosingService)) as IPeriodClosingService;
            await periodClosingService.EnsureNotClosed(data.TransactionDate, userSession.BranchId);

            var logId = await auditTrailUtils.Add("Transaction", "Void " + data.TransactionType , data.TransactionId, data, null, userSession.UserID, Status.Deleted.ToString(), HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);

            using (var transaction = dbContext.Database.BeginTransaction())
            {
                dbContext.Database.UseTransaction(transaction.GetDbTransaction());

                var details = await journalService.GetForUpdateByID(id, userSession.BranchId);
                data.Details = details;

                

                data.UpdatedDate = DateTime.UtcNow;
                data.UpdatedBy = userSession.UserID;
                data.status = Status.Void.ToString();

                foreach (var journal in details)
                {
                    journal.status = Status.Void.ToString();
                    journal.UpdatedBy = userSession.UserID;
                    journal.UpdatedDate = data.UpdatedDate;

                    await journalService.Update(journal);
                }

                await transactionService.Void(data);
                await auditTrailUtils.Update(logId, data, Status.Void.ToString());

                transaction.Commit();
                return _Ok(id, string.Format(TAG, "data has been successfully deleted"));
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
    
    [Authorize(Page.TrxTransaction, Action.Update)]
    [HttpPut("updateStatus")]
    public async Task<IActionResult> UpdateStatus([FromBody] UpdateStatusRequest request)
    {
        try
        {
            var data = await transactionService.GetByID(request.TransactionId, userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            // Lock tutup buku: tolak posting/void bila tanggal transaksi di periode tertutup.
            var periodClosingService = HttpContext.RequestServices.GetService(typeof(IPeriodClosingService)) as IPeriodClosingService;
            await periodClosingService.EnsureNotClosed(data.TransactionDate, userSession.BranchId);

            if (request.Status.ToLower().Equals(Status.Void.ToString().ToLower()) && !data.status.Equals(Status.Pending.ToString()))
                return _NotFound("Can't void published transaction");

            if (!request.Status.ToLower().Equals(Status.Void.ToString().ToLower()) && !request.Status.ToLower().Equals(Status.Published.ToString().ToLower()))
                return _NotFound("Status must be Void or Published");

            

            using var transaction = await dbContext.Database.BeginTransactionAsync();

            var details = await journalService.GetForUpdateByID(request.TransactionId, userSession.BranchId);
            data.Details = details; 

             var logId = await auditTrailUtils.Add("Transaction", "Void " + data.TransactionType , data.TransactionId, data, null, userSession.UserID, Status.Deleted.ToString(), HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);



            data.UpdatedDate = DateTime.UtcNow;
            data.UpdatedBy = userSession.BranchId;
            data.status = Utils.ToTitleCase(request.Status);
            foreach (var journal in details)
            {
                journal.status = data.status;
                journal.UpdatedBy = userSession.UserID;
                journal.UpdatedDate = data.UpdatedDate;

                await journalService.Update(journal);

            }

            await transactionService.Update(data);

            if (data.status.Equals(Status.Published.ToString(), StringComparison.OrdinalIgnoreCase))
            {
               await journalService.PostJournal(new List<string> { request.TransactionId });
            }

            await transaction.CommitAsync();

            await auditTrailUtils.Update(logId, data, data.status);

            return _Ok(request.TransactionId, "Data has been successfully updated");

            
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
             if (dbContext.Database.CurrentTransaction != null)
                dbContext.Database.RollbackTransaction();

            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(string id)
    {
        try
        {
            var data = await transactionService.GetByID(id,userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;    
            data.Details = await journalService.GetByTransactionId(id,userSession.BranchId,false);
            _logger.LogError("LOG {0}", "BRANCH ID: {BranchId}" + JsonConvert.SerializeObject(data.Details));
            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.TrxTransaction,Action.Update)]
    [HttpPut("payment")]
    public async Task<IActionResult> UpdatePayment([FromBody] PaymentUpdateRequest item) {
        try {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body request null!"));           

            var validator = new PaymentValidation();
            var result = validator.Validate(item);
            if (result.IsValid) {
                var id = await transactionService.UpdatePayment(item);
                return _Ok(id, string.Format(TAG, "data has been successfully update"));
            }
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
        return _Ok("");
    }

    [Authorize(Page.TrxTransaction, Action.Read)]
    [HttpGet("TotalsOverTime")]
    public async Task<IActionResult> GetTotalByTime(
        [FromQuery(Name = "transactionDate")] DateTime transactionDate,
        [FromQuery(Name = "transactionType")] string? transactionType,
        [FromQuery(Name = "status")] string? status
    ) {
        try {
            if (string.IsNullOrEmpty(transactionDate.ToString()))
                return _BadRequest(string.Format(TAG, "transactionDate is required!"));

            var data = await transactionService.GetTotalTransactionByPeriod(transactionDate,transactionType,status);
            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}


