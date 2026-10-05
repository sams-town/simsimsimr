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

[ApiController]
[Route("[controller]")]
public class ContactController : BaseController
{
    private IContactService _contactService;
    private IProvinceService _provinceService;
    private ICityService _cityService;
    private IContactAccountService _accountService;
    private IBankService _bankService;
    private readonly ILogger<CompanyController> _logger;
    private const string TAG = "LOG {0}";
    private const string LOG = "LOG {x}";
    private RSSMDbContext _dbContext;
    public ContactController(RSSMDbContext dbContext, IContactService contactService, IProvinceService provinceService, ICityService cityService, IContactAccountService accountService, IBankService bankService, ILogger<CompanyController> logger, IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor)
    {
        _contactService = contactService;
        _provinceService = provinceService;
        _cityService = cityService;
        _accountService = accountService;
        _bankService = bankService;
        _logger = logger;
        _dbContext = dbContext;
    }

    [Authorize(Page.MasterContact, Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "email")] string? email = null,
        [FromQuery(Name = "phone")] string? phone = null,
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "pageSize")] int pageSize = 10,
        [FromQuery(Name = "orderBy")] string orderBy = "contactid",
        [FromQuery(Name = "orderType")] string orderType = "asc"
    ) {
        try {
            var data = await _contactService.GetAll(keyword,email, phone, page, pageSize, orderBy, orderType);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterContact, Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Add(Contact items) {
        try {
            if (items == null)
                return _BadRequest(string.Format(TAG, "Body is null"));

            using (var queryExecute = _dbContext.Database.BeginTransaction()) {
                _dbContext.Database.UseTransaction(queryExecute.GetDbTransaction());

                var validator = new ContactValidation();
                var result = validator.Validate(items);

                if (result.IsValid) {

                    var idContact = await _contactService.Add(items);
                    if (items.AccountDetail != null )
                    {
                       List<ContactAccount> dataAccounts = new List<ContactAccount> {};
                        var index = 1;
                        foreach (ContactAccount dataAccount in items.AccountDetail) {
                            var checkBankCode = await _bankService.GetByCode(dataAccount.BankCode);
                            if (checkBankCode == null)
                                return _NotFound(string.Format(TAG, $"BankCode not found in Account Detail: {index}"));

                            dataAccounts.Add(new ContactAccount {
                                AccountId = dataAccount.AccountId,
                                AccountName = dataAccount.AccountName,
                                BankCode = dataAccount.BankCode,
                                AccountNo = dataAccount.AccountNo,
                                ContactId = idContact
                            });
                            index++;
                        }
                        await _accountService.Add(dataAccounts);
                    }
                    
                    await queryExecute.CommitAsync();
                    
                    return _Ok(idContact);
                } else {
                    var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                    return _BadRequest(errorMessages);
                }
            }
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            if (_dbContext.Database.CurrentTransaction != null)
                _dbContext.Database.CurrentTransaction.Rollback();

            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterContact, Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> GetByID(int id) {
        try {
            var data = await _contactService.GetByID(id);
            if (data == null)
                return _NotFound(string.Format(TAG, "data not found"));

            return _Ok(data);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterContact, Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Contact items) {
        try {
            if (items == null)
                return _BadRequest(string.Format(TAG, "Body is null"));
            
            using (var queryExecute = _dbContext.Database.BeginTransaction()) {
                _dbContext.Database.UseTransaction(queryExecute.GetDbTransaction());

                var validator = new ContactValidation();
                var result = validator.Validate(items);
                if (result.IsValid) {
                    var id = await _contactService.Update(items);
                    
                    var dataAccount = await _accountService.GetByContactID(items.ContactId);
                    // return _Ok(dataAccount);
                    await _accountService.Remove(dataAccount);

                    if (items.AccountDetail != null)
                    {
                        var index = 1;
                        List<ContactAccount> listAccount = new List<ContactAccount> {};
                        foreach (ContactAccount list in items.AccountDetail) {
                            var checkBankCode = await _bankService.GetByCode(list.BankCode);
                            if (checkBankCode == null)
                                return _NotFound(string.Format(TAG, $"BankCode not found in Account Detail: {index}"));

                            listAccount.Add(new ContactAccount {
                                AccountId = 0,
                                AccountName = list.AccountName,
                                BankCode = list.BankCode,
                                AccountNo = list.AccountNo,
                                ContactId = id
                            });
                            index++;
                        }
                        await _accountService.Add(listAccount);
                    }
                    
                    queryExecute.Commit();

                    return _Ok(id);
                } else {
                    var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                    return _BadRequest(errorMessages);
                }
            }
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            if (_dbContext.Database.CurrentTransaction != null)
                _dbContext.Database.RollbackTransaction();

            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterContact, Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Remove(int id) {
        try {
            using (var queryExecute = _dbContext.Database.BeginTransaction()) {
                _dbContext.Database.UseTransaction(queryExecute.GetDbTransaction());

                var data = await _contactService.GetByID(id);
                if (data == null)
                    return _NotFound(string.Format(TAG, "data not found"));

                var transaction = HttpContext.RequestServices.GetService(typeof(ITransactionService)) as ITransactionService;
                var exist = await transaction.CheckIfExistContactId(data.ContactId);

                if (!exist) {
                    var listaccount = await _accountService.GetByContactID(id);
                    await _accountService.Remove(listaccount);
                    var result = await _contactService.Remove(data);
                    queryExecute.Commit();
                    return _Ok(result);
                } else {
                    return _BadRequest("Contact is used in transaction, cannot be deleted");
                }
            }
        } catch(Exception e) {
            _logger.LogError(LOG, e);
            if (_dbContext.Database.CurrentTransaction != null)
                _dbContext.Database.RollbackTransaction();

            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterContact, Action.Export)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try {
            var data = await _contactService.GetAll(null,null, null, -1, -1, "contactid", "asc");
            byte[] fileContent;
            string fileName = "CONTACT.";
            if (type.ToLower().Equals("xlsx")) {
                fileContent = ExportToDocs.ConvertToExcel<Contact>(data.Data);
                fileName += "xlsx";
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Contact>(data.Data);
                fileName += "csv";
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
}