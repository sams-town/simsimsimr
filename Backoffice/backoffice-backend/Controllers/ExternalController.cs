namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using RSSMBackend.DbContext;
using Microsoft.EntityFrameworkCore.Storage;
using Microsoft.EntityFrameworkCore;
using System.Globalization;
using Newtonsoft.Json;
using AngleSharp.Common;
using TDPBackend.Helpers;

[ApiController]
[Route("[controller]")]
public class ExternalController : BaseController
{
    private readonly ILogger<ExternalController> _logger;
    private const string TAG = "External {0}";
    private const string LOG = "External {x}";
    private RSSMDbContext dbContext;
    private IJournalService journalService;
    private ITransactionService transactionService;
    private ICOAService cOAService;
    private IContactService contactService;
    private AuditTrailUtils auditTrailUtils;
    public ExternalController(RSSMDbContext dbContext, AuditTrailUtils auditTrailUtils, ITransactionService transactionService, ICOAService cOAService, IJournalService journalService, IContactService contactService, ILogger<ExternalController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        this.journalService = journalService;
        this.cOAService = cOAService;
        this.contactService = contactService;
        _logger = logger;
        this.dbContext = dbContext;
        this.transactionService = transactionService;
        this.auditTrailUtils = auditTrailUtils;
    }

    [Authorize(Page.ExternalAPI, Action.SubmitInvoice)]
    [HttpPost("admission-invoice")]
    public async Task<IActionResult> AdmissionInvoice([FromBody] InvoiceRequestModel item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));

            var logsId = await auditTrailUtils.Add("External API", "Submit Invoice", item.Number, null, item, userSession.UserID, "Received", HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);
           

            var validator = new InvoiceValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {
                if (await transactionService.CheckIfExistTransactionNo(item.Number,JournalSource.Invoice.ToString(), userSession.BranchId ?? 0))
                    return _BadRequest(string.Format(TAG, item.Number + " already inserted!!"));

                using (var transaction = dbContext.Database.BeginTransaction())
                {
                    dbContext.Database.UseTransaction(transaction.GetDbTransaction());

                    var checkPatient = await contactService.GetByRefCode(item.CustomerNo);
                    if (checkPatient == null)
                    {
                        var contactPatient = new Contact
                        {
                            ContactId = 0,
                            ContactName = item.CustomerName,
                            Phone = item.CustomerPhone,
                            Email = item.CustomerEmail,
                            Address = item.CustomerAddress,
                            ContactCategoryId = Constants.CONTACT_CATEGORY_PATIENT,
                            RefCode = item.CustomerNo,
                            DueDate = null,
                            Status = null,
                        };
                        await contactService.Add(contactPatient);
                    }

                    item.CustomerName = string.IsNullOrEmpty(item.CustomerName) && checkPatient != null ? checkPatient.ContactName : item.CustomerName;
                    DateTime transDate = DateTime.ParseExact(
                        item.TransDate,
                        "dd/MM/yyyy",
                        CultureInfo.InvariantCulture
                    );

                    var coa = Utils.getCOAInvoice();
                    string invoiceJournalNo = await journalService.GenerateJournalNo(transDate.ToUniversalTime());
                    var trx = new Transaction()
                    {
                        TransactionId = Guid.NewGuid().ToString(),
                        TransactionDate = transDate.ToUniversalTime(),
                        TransactionName = "Auto Invoice THB",
                        TransactionRef = item.Number,
                        TransactionType = JournalSource.Invoice.ToString(),
                        TransactionNo = item.Number,
                        JournalNo = invoiceJournalNo,
                        Description = null,
                        CoaCode = coa.CoaCode,
                        CoaName = coa != null ? coa.CoaName : null,
                        CoaParentCode = coa != null ? coa.ParentCode : null,
                        CoaParentName = coa != null ? coa.ParentName : null,
                        status = Status.Pending.ToString(),
                        BranchId = userSession.BranchId,
                        CreatedBy = userSession.UserID,
                        CreatedDate = DateTime.UtcNow,
                        IsAutoId = false
                    };

                    trx.Amount = 0;
                    foreach (InvoiceRequestModel.InvoiceDetailModel detail in item.DetailItem)
                        trx.Amount += detail.TotalPrice;


                    await transactionService.Add(trx);

                    var jounal = new List<Journal>();

                    await journalService.Add(new Journal
                    {
                        JournalRef = trx.TransactionRef,
                        JournalDesc = trx.TransactionRef,
                        CoaCode = trx.CoaCode,
                        TransactionId = trx.TransactionId,
                        Source = trx.TransactionType,
                        CoaName = trx.CoaName,
                        CoaParentCode = trx.CoaParentCode,
                        CoaParentName = trx.CoaParentName,
                        status = Status.Pending.ToString(),
                        BranchId = userSession.BranchId,
                        CreatedBy = userSession.UserID,
                        CreatedDate = trx.CreatedDate,
                        IsPremier = true,
                        DebitAmount = trx.Amount,
                        CreditAmount = 0,
                    });

                    foreach (InvoiceRequestModel.InvoiceDetailModel detail in item.DetailItem)
                    {
                        var coaCode = Utils.getCOAByDepartmentName(detail.DepartmentName);
                        var journal = new Journal()
                        {
                            JournalRef = trx.TransactionRef,
                            JournalDesc = detail.ItemName,
                            CoaCode = coaCode,
                            TransactionId = trx.TransactionId
                        };

                        var coaDetail = await cOAService.GetByCode(coaCode, userSession.BranchId);
                        if (coaDetail == null)
                            return _BadRequest(string.Format(TAG, "COA " + coaCode + " Not Found"));

                        journal.Source = trx.TransactionType;
                        journal.CoaName = coaDetail.CoaName;
                        journal.CoaParentCode = coaDetail.ParentCode;
                        journal.CoaParentName = coaDetail.ParentName;
                        journal.status = Status.Pending.ToString();
                        journal.BranchId = userSession.BranchId;
                        journal.CreatedBy = userSession.UserID;
                        journal.CreatedDate = trx.CreatedDate;
                        journal.IsPremier = false;
                        journal.DebitAmount = 0;
                        journal.CreditAmount = detail.TotalPrice;


                        if(detail.DepartmentName.ToUpper() == "FARMASI")
                        {
                            // AKUN HPP FARMASI
                            var coaCodeFarmasiHpp = Utils.getCOAByDepartmentName("HPPFARMASI");
                            var journalFarmasiHpp = new Journal()
                            {
                                JournalRef = trx.TransactionRef,
                                JournalDesc = detail.ItemName,
                                CoaCode = coaCodeFarmasiHpp,
                                TransactionId = trx.TransactionId
                            };

                            var coaDetailFarmasiHpp = await cOAService.GetByCode(coaCodeFarmasiHpp, userSession.BranchId);
                            if (coaDetailFarmasiHpp == null)
                                return _BadRequest(string.Format(TAG, "COA " + coaCodeFarmasiHpp + " Not Found"));

                            journalFarmasiHpp.Source = trx.TransactionType;
                            journalFarmasiHpp.CoaName = coaDetailFarmasiHpp.CoaName;
                            journalFarmasiHpp.CoaParentCode = coaDetailFarmasiHpp.ParentCode;
                            journalFarmasiHpp.CoaParentName = coaDetailFarmasiHpp.ParentName;
                            journalFarmasiHpp.status = Status.Pending.ToString();
                            journalFarmasiHpp.BranchId = userSession.BranchId;
                            journalFarmasiHpp.CreatedBy = userSession.UserID;
                            journalFarmasiHpp.CreatedDate = trx.CreatedDate;
                            journalFarmasiHpp.IsPremier = false;
                            journalFarmasiHpp.DebitAmount = detail.TotalPrice * 0.8;
                            journalFarmasiHpp.CreditAmount = 0;

                            await journalService.Add(journalFarmasiHpp);

                            // AKUN PERSEDIAAN FARMASI
                            var coaCodeFarmasiPersediaan = Utils.getCOAByDepartmentName("PERSEDIAANFARMASI");
                            var journalFarmasiPersediaan = new Journal()
                            {
                                JournalRef = trx.TransactionRef,
                                JournalDesc = detail.ItemName,
                                CoaCode = coaCodeFarmasiPersediaan,
                                TransactionId = trx.TransactionId
                            };

                            var coaDetailFarmasiPersediaan = await cOAService.GetByCode(coaCodeFarmasiPersediaan, userSession.BranchId);
                            if (coaDetailFarmasiPersediaan == null)
                                return _BadRequest(string.Format(TAG, "COA " + coaCodeFarmasiPersediaan + " Not Found"));

                            journalFarmasiPersediaan.Source = trx.TransactionType;
                            journalFarmasiPersediaan.CoaName = coaDetailFarmasiPersediaan.CoaName;
                            journalFarmasiPersediaan.CoaParentCode = coaDetailFarmasiPersediaan.ParentCode;
                            journalFarmasiPersediaan.CoaParentName = coaDetailFarmasiPersediaan.ParentName;
                            journalFarmasiPersediaan.status = Status.Pending.ToString();
                            journalFarmasiPersediaan.BranchId = userSession.BranchId;
                            journalFarmasiPersediaan.CreatedBy = userSession.UserID;
                            journalFarmasiPersediaan.CreatedDate = trx.CreatedDate;
                            journalFarmasiPersediaan.IsPremier = false;
                            journalFarmasiPersediaan.DebitAmount = 0; //detail.TotalPrice * 0.8;
                            journalFarmasiPersediaan.CreditAmount = detail.TotalPrice * 0.8;  
                            await journalService.Add(journalFarmasiPersediaan);                  
                        }


                        if (!string.IsNullOrEmpty(detail.DoctorID))
                        {
                            var checkDokter = await contactService.GetByRefCode(detail.DoctorID ?? "");
                            if (checkDokter == null)
                            {
                                var contactPatient = new Contact
                                {
                                    ContactId = 0,
                                    ContactName = detail.doctorName ?? "N/A",
                                    ContactCategoryId = Constants.CONTACT_CATEGORY_DOCTOR,
                                    RefCode = detail.DoctorID ?? "N/A",
                                    DueDate = null,
                                    Status = null,
                                };
                                await contactService.Add(contactPatient);
                            }

                            // AKUN JASA DOKTER 70%
                            var coaCodeDokter = Utils.getCOAByDepartmentName("DOKTER");
                            var journalDokter = new Journal()
                            {
                                JournalRef = trx.TransactionRef,
                                JournalDesc = detail.doctorName ?? detail.DoctorID ?? "Jasa Medis Dokter",
                                CoaCode = coaCodeDokter,
                                TransactionId = trx.TransactionId
                            };

                            var coaDetailDokter = await cOAService.GetByCode(coaCodeDokter, userSession.BranchId);
                            if (coaDetailDokter == null)
                                return _BadRequest(string.Format(TAG, "COA " + coaCodeDokter + " Not Found"));

                            journalDokter.Source = trx.TransactionType;
                            journalDokter.CoaName = coaDetailDokter.CoaName;
                            journalDokter.CoaParentCode = coaDetailDokter.ParentCode;
                            journalDokter.CoaParentName = coaDetailDokter.ParentName;
                            journalDokter.status = Status.Pending.ToString();
                            journalDokter.BranchId = userSession.BranchId;
                            journalDokter.CreatedBy = userSession.UserID;
                            journalDokter.CreatedDate = trx.CreatedDate;
                            journalDokter.IsPremier = false;
                            journalDokter.DebitAmount = 0;
                            journalDokter.CreditAmount = detail.TotalPrice * 0.7;

                            await journalService.Add(journalDokter);

                            // AKUN HPP JASA DOKTER 70%
                            var coaCodeDokterHpp = Utils.getCOAByDepartmentName("HPPDOKTER");
                            var journalDokterHpp = new Journal()
                            {
                                JournalRef = trx.TransactionRef,
                                JournalDesc = detail.doctorName ?? detail.DoctorID ?? "Jasa Medis Dokter",
                                CoaCode = coaCodeDokterHpp,
                                TransactionId = trx.TransactionId
                            };

                            var coaDetailDokterHpp = await cOAService.GetByCode(coaCodeDokterHpp, userSession.BranchId);
                            if (coaDetailDokterHpp == null)
                                return _BadRequest(string.Format(TAG, "COA " + coaCodeDokterHpp + " Not Found"));

                            journalDokterHpp.Source = trx.TransactionType;
                            journalDokterHpp.CoaName = coaDetailDokterHpp.CoaName;
                            journalDokterHpp.CoaParentCode = coaDetailDokterHpp.ParentCode;
                            journalDokterHpp.CoaParentName = coaDetailDokterHpp.ParentName;
                            journalDokterHpp.status = Status.Pending.ToString();
                            journalDokterHpp.BranchId = userSession.BranchId;
                            journalDokterHpp.CreatedBy = userSession.UserID;
                            journalDokterHpp.CreatedDate = trx.CreatedDate;
                            journalDokterHpp.IsPremier = false;
                            journalDokterHpp.DebitAmount = detail.TotalPrice * 0.7;
                            journalDokterHpp.CreditAmount = 0;

                            await journalService.Add(journalDokterHpp);

                        }

                        await journalService.Add(journal);
                       
                        

                    }

                    transaction.Commit();
                    
                    await auditTrailUtils.Update(logsId, null, "Inserted");

                    return _Ok(trx.TransactionId, "Invoice has been successfully saved");
                }

            }
            else
            {
                var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errorMessages);
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
    
    [Authorize(Page.ExternalAPI,Action.SubmitInvoice)]
    [HttpPost("admission-sales-receive")]
    public async Task<IActionResult> AdmissionSalesReceive([FromBody] SalesReceiveRequestModel item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG, "Body Null"));


            var RefId = item.detailInvoice != null && item.detailInvoice.GetItemByIndex(0) != null ? item.detailInvoice.GetItemByIndex(0).invoiceNo : "N/A";
            var logsId = await auditTrailUtils.Add("External API", "Submit Sales Receive", RefId, null, item, userSession.UserID, "Received", HttpContext.Connection.RemoteIpAddress.ToString(), userSession.BranchId ?? 0);

            var validator = new SalesReceiveValidator();
            var result = validator.Validate(item);

            if (result.IsValid)
            {

                var invoice = item.detailInvoice.GetItemByIndex(0);


                if (await transactionService.CheckIfExistTransactionNo(invoice.invoiceNo,JournalSource.SalesReceive.ToString(), userSession.BranchId ?? 0))
                    return _BadRequest(string.Format(TAG, invoice.invoiceNo + " already inserted!!"));

                using (var transaction = dbContext.Database.BeginTransaction())
                {
                    dbContext.Database.UseTransaction(transaction.GetDbTransaction());

                    var checkPatient = await contactService.GetByRefCode(item.customerNo);
                    if (checkPatient == null)
                    {
                        var contactPatient = new Contact
                        {
                            ContactId = 0,
                            ContactName = item.customerNo,
                            ContactCategoryId = Constants.CONTACT_CATEGORY_PATIENT,
                            RefCode = item.customerNo,
                            DueDate = null,
                            Status = null,
                        };
                        await contactService.Add(contactPatient);
                    }

                    DateTime transDate = DateTime.ParseExact(
                        item.TransDate,
                        "dd/MM/yyyy",
                        CultureInfo.InvariantCulture
                    );

                    var coa = Utils.getCOAInvoice();
                    string salesJournalNo = await journalService.GenerateJournalNo(transDate.ToUniversalTime());
                    var trx = new Transaction()
                    {
                        TransactionId = Guid.NewGuid().ToString(),
                        TransactionDate = transDate.ToUniversalTime(),
                        TransactionName = "Auto Sales Receive THB",
                        TransactionRef = invoice.invoiceNo,
                        TransactionType = JournalSource.SalesReceive.ToString(),
                        TransactionNo = invoice.invoiceNo,
                        JournalNo = salesJournalNo,
                        Description = null,
                        CoaCode = coa.CoaCode,
                        CoaName = coa != null ? coa.CoaName : null,
                        CoaParentCode = coa != null ? coa.ParentCode : null,
                        CoaParentName = coa != null ? coa.ParentName : null,
                        status = Status.Pending.ToString(),
                        BranchId = userSession.BranchId,
                        CreatedBy = userSession.UserID,
                        CreatedDate = DateTime.UtcNow,
                        IsAutoId = false
                    };

                    trx.Amount = invoice.paymentAmount;
                    await transactionService.Add(trx);

                    var jounal = new List<Journal>();
                    await journalService.Add(new Journal
                    {
                        JournalRef = trx.TransactionRef,
                        JournalDesc = trx.TransactionRef,
                        CoaCode = trx.CoaCode,
                        TransactionId = trx.TransactionId,
                        Source = trx.TransactionType,
                        CoaName = trx.CoaName,
                        CoaParentCode = trx.CoaParentCode,
                        CoaParentName = trx.CoaParentName,
                        status = Status.Pending.ToString(),
                        BranchId = userSession.BranchId,
                        CreatedBy = userSession.UserID,
                        CreatedDate = trx.CreatedDate,
                        IsPremier = false,
                        DebitAmount = 0,
                        CreditAmount = trx.Amount,
                    });

                    
                    var coaCode = Utils.getCOAByDepartmentName(item.bankName.ToUpper());
                    var journal = new Journal()
                    {
                        JournalRef = trx.TransactionRef,
                        JournalDesc = invoice.invoiceNo,
                        CoaCode = coaCode,
                        TransactionId = trx.TransactionId
                    };

                    var coaDetail = await cOAService.GetByCode(coaCode, userSession.BranchId);
                    if (coaDetail == null)
                        return _BadRequest(string.Format(TAG, "COA " + coaCode + " Not Found"));

                    journal.Source = trx.TransactionType;
                    journal.CoaName = coaDetail.CoaName;
                    journal.CoaParentCode = coaDetail.ParentCode;
                    journal.CoaParentName = coaDetail.ParentName;
                    journal.status = Status.Pending.ToString();
                    journal.BranchId = userSession.BranchId;
                    journal.CreatedBy = userSession.UserID;
                    journal.CreatedDate = trx.CreatedDate;
                    journal.IsPremier = true;

                    journal.DebitAmount = invoice.paymentAmount;
                    journal.CreditAmount = 0;

                    await journalService.Add(journal);

                    transaction.Commit();
                    
                    await auditTrailUtils.Update(logsId, null, "Inserted");


                    return _Ok(trx.TransactionId, "Sales receive has been successfully saved");
                }

            } else {
                    var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                    return _BadRequest(errorMessages);
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


