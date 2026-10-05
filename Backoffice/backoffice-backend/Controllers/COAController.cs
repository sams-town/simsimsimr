namespace RSSMBackend.Controllers;

using Microsoft.AspNetCore.Mvc;
using RSSMBackend.Services;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.Validation;
using RSSMBackend.Helpers;
using OfficeOpenXml;
using CsvHelper;
using CsvHelper.Configuration;
using System.Globalization;
using Newtonsoft.Json;

[ApiController]
[Route("[controller]")]
public class COAController: BaseController
{
    private ICOAService cOAService;
    private readonly ILogger<COAController> _logger;
    private const string TAG = "COA {0}";
    private const string LOG = "COA {x}";
    public COAController(ICOAService cOAService,ILogger<COAController> logger,IHttpContextAccessor httpContextAccessor) : base(httpContextAccessor){
        this.cOAService = cOAService;
        _logger = logger;
    }
   
    [Authorize(Page.MasterCOA,Action.Read)]
    [HttpGet]
    public async Task<IActionResult> GetAll(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "coacode",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "accountgroup")] int? accountGroup = null,
        [FromQuery(Name = "accounttype")] int? accountType = null,
        [FromQuery(Name = "parent")] int? parent = null,
        [FromQuery(Name = "level")] int? level = null,
        [FromQuery(Name = "currency")] int? currency = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "mormalPosition")] string? mormalPosition = null,
        [FromQuery(Name = "iscashbank")] bool? IsCashBank = null,
        [FromQuery(Name = "iscashflow")] bool? isCashFlow = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await cOAService.GetAll(keyword,status,level,userSession.BranchId,mormalPosition,IsCashBank,isCashFlow,parent,accountGroup,accountType,currency,page,pageSize,orderBy,orderType);
            if (data == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }

            // Daftar Buku Kas (iscashbank=true) menampilkan kolom Saldo. Hitung ON-READ
            // (checkpoint saldo awal + Σ jurnal Published) alih-alih membaca kolom mst_coa.balance
            // yang sudah tidak dipelihara (boleh di-nol-kan). Hanya untuk halaman ini agar ringan.
            if (IsCashBank == true && data.Data != null && data.Data.Count > 0)
            {
                var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;
                var asOf = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
                int branchId = userSession.BranchId ?? 0;
                foreach (var coa in data.Data)
                {
                    if (string.IsNullOrEmpty(coa.CoaCode)) continue;
                    coa.Balance = await journalService.GetEndingBalance(coa.CoaCode, asOf, branchId);
                }
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCOA, Action.Export, Page.TrxBank, Action.Read)]
    [HttpGet("export")]
    public async Task<IActionResult> Export(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "coacode",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "accountgroup")] int? accountGroup = null,
        [FromQuery(Name = "accounttype")] int? accountType = null,
        [FromQuery(Name = "parent")] int? parent = null,
        [FromQuery(Name = "level")] int? level = null,
        [FromQuery(Name = "currency")] int? currency = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "mormalPosition")] string? mormalPosition = null,
        [FromQuery(Name = "iscashbank")] bool? IsCashBank = null,
        [FromQuery(Name = "iscashbankexport")] bool? IsCashBankExport = null,
        [FromQuery(Name = "iscashflow")] bool? isCashFlow = null,
        [FromQuery(Name = "type")] string type = "xlsx"
    ) {
        try {
            var data = await cOAService.GetAll(keyword,status,level,userSession.BranchId,mormalPosition,IsCashBank,isCashFlow,parent,accountGroup,accountType,currency,-1,-1,orderBy,orderType);
            if (data == null)
                return _NotFound(string.Format(TAG,"Not Found"));

            // Export Buku Kas: saldo dihitung ON-READ (bukan dari kolom mst_coa.balance).
            if (IsCashBankExport == true && data.Data != null && data.Data.Count > 0)
            {
                var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;
                var asOf = DateTime.SpecifyKind(DateTime.UtcNow, DateTimeKind.Utc);
                int branchId = userSession.BranchId ?? 0;
                foreach (var coa in data.Data)
                {
                    if (string.IsNullOrEmpty(coa.CoaCode)) continue;
                    coa.Balance = await journalService.GetEndingBalance(coa.CoaCode, asOf, branchId);
                }
            }

            byte[] fileContent;
            string fileName = $"COA.";
            if (type.ToLower().Equals(ExportType.XLSX.ToString().ToLower())) {
                if (IsCashBankExport == true) {
                    fileContent = ExportToDocs.ConvertBukuKasToExcel(data.Data);
                    fileName += "_BukuKas_";
                } else {
                    fileContent = ExportToDocs.ConvertToExcel<Coa>(data.Data);
                }
                fileName += ExportType.XLSX.ToString().ToLower();
            } else {
                fileContent = ExportToDocs.ConvertToCsv<Coa>(data.Data);
                fileName += ExportType.CSV.ToString().ToLower();
            }

            return _File(fileContent, type, fileName);
        } catch (Exception e) {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCOA,Action.Read)]
    [HttpGet("option")]
    public async Task<IActionResult> GetAllOption(
        [FromQuery(Name = "keyword")] string? keyword = null,
        [FromQuery(Name = "orderBy")] string? orderBy = "coacode",
        [FromQuery(Name = "orderType")] string? orderType = "asc",
        [FromQuery(Name = "page")] int page = 1,
        [FromQuery(Name = "accountgroup")] int? accountGroup = null,
        [FromQuery(Name = "accounttype")] int? accountType = null,
        [FromQuery(Name = "parent")] int? parent = null,
        [FromQuery(Name = "level")] int? level = null,
        [FromQuery(Name = "currency")] int? currency = null,
        [FromQuery(Name = "status")] string? status = null,
        [FromQuery(Name = "mormalPosition")] string? mormalPosition = null,
        [FromQuery(Name = "iscashbank")] bool? IsCashBank = null,
        [FromQuery(Name = "iscashflow")] bool? isCashFlow = null,
        [FromQuery(Name = "startswith")] string? startswith = null,
        [FromQuery(Name = "pageSize")] int pageSize = 10
        )
    {
        try
        {
            var data = await cOAService.GetAllOption(keyword,status,level,userSession.BranchId,mormalPosition,IsCashBank,isCashFlow,null,startswith,parent,accountGroup,accountType,currency,page,pageSize,orderBy,orderType);
            if (data == null) {
                return _NotFound(string.Format(TAG,"Not Found"));
            }

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }
    
    [Authorize(Page.MasterCOA,Action.Create)]
    [HttpPost]
    public async Task<IActionResult> Create([FromBody] Coa item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var validator = new COAValidator();
            var result =  validator.Validate(item);

            if(result.IsValid){
                item.BranchId = userSession.BranchId;
                // CoaLevel and ParentId resolution is now handled in COAService.Add
                var id = await cOAService.Add(item);
                return _Ok(id,string.Format(TAG, "data has been successfully saved"));
            } else {
                var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errorMessages);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCOA,Action.Create)]
    [HttpPost("upload")]
    public async Task<IActionResult> UploadExcel([FromForm] FileUpload fileUpload)
    {
        
        try
        {
            if (fileUpload.File == null || fileUpload.File.Length == 0)
            {
                return _BadRequest("No file uploaded.");
            }

            string importMode = (fileUpload.ImportMode ?? "upsert").ToLower();

            using (var stream = fileUpload.File.OpenReadStream())
            {
                using (var package = new ExcelPackage(stream))
                {
                    var worksheet = package.Workbook.Worksheets[0];
                    var rowCount = worksheet.Dimension.Rows;
                    List<COAFile> errors = new List<COAFile>();

                    // Parse all rows from Excel
                    List<Coa> parsedRows = new List<Coa>();

                    // Build header-to-column map from row 1
                    var colCount = worksheet.Dimension.Columns;
                    var headerMap = new Dictionary<string, int>(StringComparer.OrdinalIgnoreCase);
                    for (int col = 1; col <= colCount; col++)
                    {
                        var header = worksheet.Cells[1, col].GetValue<string>();
                        if (!string.IsNullOrEmpty(header))
                            headerMap[header.Trim()] = col;
                    }

                    // Resolve column indices by header name (supports multiple naming conventions)
                    int colCoaCode = headerMap.ContainsKey("CoaCode") ? headerMap["CoaCode"] : (headerMap.ContainsKey("coa_code") ? headerMap["coa_code"] : 1);
                    int colCoaName = headerMap.ContainsKey("CoaName") ? headerMap["CoaName"] : (headerMap.ContainsKey("coa_name") ? headerMap["coa_name"] : 2);
                    int colCoaLevel = headerMap.ContainsKey("CoaLevel") ? headerMap["CoaLevel"] : (headerMap.ContainsKey("coa_level") ? headerMap["coa_level"] : 3);
                    int colAccountType = headerMap.ContainsKey("AccountTypeName") ? headerMap["AccountTypeName"] : (headerMap.ContainsKey("AccountType") ? headerMap["AccountType"] : 4);
                    int colParentCode = headerMap.ContainsKey("ParentCode") ? headerMap["ParentCode"] : (headerMap.ContainsKey("parent_code") ? headerMap["parent_code"] : 5);
                    int colNormalPosition = headerMap.ContainsKey("NormalPosition") ? headerMap["NormalPosition"] : (headerMap.ContainsKey("normal_position") ? headerMap["normal_position"] : 6);
                    int colAccountGroup = headerMap.ContainsKey("AccountGroupName") ? headerMap["AccountGroupName"] : (headerMap.ContainsKey("AccountGroup") ? headerMap["AccountGroup"] : 7);
                    int colIsCashFlow = headerMap.ContainsKey("IsCashFlow") ? headerMap["IsCashFlow"] : (headerMap.ContainsKey("is_cash_flow") ? headerMap["is_cash_flow"] : 8);
                    int colIsCashBank = headerMap.ContainsKey("IsCashBank") ? headerMap["IsCashBank"] : (headerMap.ContainsKey("is_cash_bank") ? headerMap["is_cash_bank"] : 9);
                    int colStatus = headerMap.ContainsKey("Status") ? headerMap["Status"] : (headerMap.ContainsKey("status") ? headerMap["status"] : 10);
                    int colBalance = headerMap.ContainsKey("Balance") ? headerMap["Balance"] : (headerMap.ContainsKey("balance") ? headerMap["balance"] : 11);

                    for (int row = 2; row <= rowCount; row++)
                    {
                        var coaCode = worksheet.Cells[row, colCoaCode].GetValue<string>();
                        var coaName = worksheet.Cells[row, colCoaName].GetValue<string>();

                        if(string.IsNullOrEmpty(coaName) || string.IsNullOrEmpty(coaCode))
                            continue;

                        // Safely parse CoaLevel
                        int coaLevel = 1;
                        try { coaLevel = worksheet.Cells[row, colCoaLevel].GetValue<int>(); } catch { }

                        // Safely parse booleans (handle both native bool and string "TRUE"/"FALSE")
                        bool isCashFlow = false;
                        try {
                            var cfVal = worksheet.Cells[row, colIsCashFlow].Value;
                            if (cfVal is bool cfBool) isCashFlow = cfBool;
                            else if (cfVal != null) isCashFlow = cfVal.ToString().Trim().ToLower() == "true" || cfVal.ToString().Trim() == "1";
                        } catch { }
                        bool isCashBank = false;
                        try {
                            var cbVal = worksheet.Cells[row, colIsCashBank].Value;
                            if (cbVal is bool cbBool) isCashBank = cbBool;
                            else if (cbVal != null) isCashBank = cbVal.ToString().Trim().ToLower() == "true" || cbVal.ToString().Trim() == "1";
                        } catch { }

                        // Safely parse status (could be bool or string)
                        string statusStr = "Active";
                        try {
                            var statusVal = worksheet.Cells[row, colStatus].Value;
                            if (statusVal is bool boolVal) {
                                statusStr = boolVal ? Status.Active.ToString() : Status.InActive.ToString();
                            } else {
                                statusStr = statusVal?.ToString() ?? Status.Active.ToString();
                            }
                        } catch { }

                        // Safely parse balance
                        double balance = 0;
                        try { balance = worksheet.Cells[row, colBalance].GetValue<double>(); } catch { }

                        Coa data = new Coa() {
                            CoaCode = coaCode,
                            CoaName = coaName,
                            CoaLevel = coaLevel,
                            AccountTypeName = worksheet.Cells[row, colAccountType].GetValue<string>(),
                            ParentCode = worksheet.Cells[row, colParentCode].GetValue<string>(),
                            NormalPosition = worksheet.Cells[row, colNormalPosition].GetValue<string>(),
                            AccountGroupName = worksheet.Cells[row, colAccountGroup].GetValue<string>(),
                            IsCashFlow = isCashFlow,
                            IsCashBank = isCashBank,
                            BranchId = userSession.BranchId,
                            Status = statusStr,
                            Balance = balance,
                        };

                        parsedRows.Add(data);
                    }

                    // Resolve AccountGroup and AccountType for all rows
                    var accountGroupService = HttpContext.RequestServices.GetService(typeof(IAccountGroupService)) as IAccountGroupService;
                    var accountTypeService = HttpContext.RequestServices.GetService(typeof(IAccountTypeService)) as IAccountTypeService;

                    foreach (var data in parsedRows)
                    {
                        List<string> error = new List<string>();

                        if(!string.IsNullOrEmpty(data.AccountGroupName)){
                            if(!data.AccountGroupName.ToLower().Equals("null")){
                                var group = await accountGroupService.GetByName(data.AccountGroupName,userSession.BranchId);
                                if(group == null)
                                    error.Add("Account Group Not Found");
                                else data.AccountGroupId = group.AccountGroupId; 
                            }
                        }

                        if(!string.IsNullOrEmpty(data.AccountTypeName)){
                            if(!data.AccountTypeName.ToLower().Equals("null")){
                                var type = await accountTypeService.GetByName(data.AccountTypeName,userSession.BranchId);
                                if(type == null)
                                    error.Add("Account Type Not Found");
                                else data.AccountTypeId = type.AccountTypeId;  
                            }
                        }

                        if(error.Count != 0){
                            COAFile fileError = new COAFile(data) { Error = string.Join("|", error) };
                            errors.Add(fileError);
                        }
                    }

                    // Remove errored rows from processing
                    var errorCodes = errors.Select(e => e.CoaCode).ToHashSet();
                    var validRows = parsedRows.Where(r => !errorCodes.Contains(r.CoaCode)).ToList();

                    // Execute import based on mode
                    if (importMode == "truncate")
                    {
                        // Mode 1: Truncate + Insert
                        // Pass 1: Delete all existing COA for this branch
                        await cOAService.RemoveAllByBranch(userSession.BranchId);

                        // Pass 2: Insert all valid rows (ordered by level to handle parents first)
                        var orderedRows = validRows.OrderBy(r => r.CoaLevel ?? 1).ToList();
                        foreach (var data in orderedRows)
                        {
                            // Resolve ParentId from ParentCode (parent should already be inserted since ordered by level)
                            if(!string.IsNullOrEmpty(data.ParentCode) && !data.ParentCode.ToLower().Equals("null")){
                                var parent = await cOAService.GetByCode(data.ParentCode, userSession.BranchId);
                                if(parent != null){
                                    data.ParentId = parent.CoaId;
                                }
                            }

                            data.CreatedBy = userSession.UserID;
                            data.CreatedDate = DateTime.UtcNow;
                            await cOAService.Add(data);
                        }
                    }
                    else if (importMode == "skip")
                    {
                        // Mode 2: Insert new, Skip existing
                        foreach (var data in validRows)
                        {
                            var exist = await cOAService.GetByCode(data.CoaCode, userSession.BranchId);
                            if(exist == null){
                                // Resolve ParentId
                                if(!string.IsNullOrEmpty(data.ParentCode) && !data.ParentCode.ToLower().Equals("null")){
                                    var parent = await cOAService.GetByCode(data.ParentCode, userSession.BranchId);
                                    if(parent != null){
                                        data.ParentId = parent.CoaId;
                                    }
                                }
                                data.CreatedBy = userSession.UserID;
                                data.CreatedDate = DateTime.UtcNow;
                                await cOAService.Add(data);
                            }
                            // else: skip, do nothing
                        }
                    }
                    else
                    {
                        // Mode 3 (default "upsert"): Insert new, Update existing
                        foreach (var data in validRows)
                        {
                            // Resolve ParentId
                            if(!string.IsNullOrEmpty(data.ParentCode) && !data.ParentCode.ToLower().Equals("null")){
                                var parent = await cOAService.GetByCode(data.ParentCode, userSession.BranchId);
                                if(parent != null){
                                    data.ParentId = parent.CoaId;
                                }
                            }

                            var exist = await cOAService.GetByCode(data.CoaCode, userSession.BranchId);
                            if(exist == null){
                                data.CreatedBy = userSession.UserID;
                                data.CreatedDate = DateTime.UtcNow;
                                await cOAService.Add(data);
                            } else {
                                data.CoaId = exist.CoaId;
                                data.UpdatedBy = userSession.UserID;
                                data.UpdatedDate = DateTime.UtcNow;
                                await cOAService.Update(data, false); 
                            }
                        }
                    }

                if(errors.Count != 0){
                    string projectDirectory = AppContext.BaseDirectory;
                    string downloadPath = projectDirectory + @"Files/Download/";
                    if (!System.IO.File.Exists(downloadPath))
                        Directory.CreateDirectory(downloadPath);

                    Console.WriteLine("ERROR 2" + JsonConvert.SerializeObject(errors));
                    string name = "Coa_Upload_Error_" + DateTime.Now.ToString("yyyyMMdd_HHmmss") + ".csv";
                    string fullName = downloadPath + name;

                    using (var writer = new StreamWriter(fullName))
                    {
                        var csvError = new CsvWriter(writer, new CsvConfiguration(CultureInfo.InvariantCulture));
                        csvError.WriteRecords(errors);
                    }

                    var url = $"{HttpContext.Request.Scheme}://{HttpContext.Request.Host}/download/{name}";

                    return _Ok(url,string.Format("Error Upload {0} of {1}",errors.Count.ToString(),rowCount.ToString()));
                }
            }

               
                
            }

             return _Ok(true,"COA File uploaded and processed successfully.");
            
        }
        catch (Exception e)
        {
            _logger.LogError(TAG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterCOA,Action.Update)]
    [HttpPut]
    public async Task<IActionResult> Update([FromBody] Coa item)
    {
        try
        {
            if (item == null)
                return _BadRequest(string.Format(TAG,"Body Null"));

            var validator = new COAValidator();
            var result =  validator.Validate(item);
            if(result.IsValid){
                item.BranchId = userSession.BranchId;
                // CoaLevel and ParentId resolution is now handled in COAService.Update
                var id = await cOAService.Update(item,true);
                return _Ok(id,string.Format(TAG, "data has been successfully updated"));
            } else {
                var errorMessages = result.Errors.Select(x => x.ErrorMessage).ToList();
                return _BadRequest(errorMessages);
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountGroup,Action.Delete)]
    [HttpDelete("{id}")]
    public async Task<IActionResult> Delete(int id)
    {
         try
        {
            var data = await cOAService.GetByID(id,userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;
            var exist = await journalService.checkIfExistCoa(data.CoaCode,userSession.BranchId);
            if(!exist){
                 await cOAService.Remove(data);    
                return _Ok(id,string.Format(TAG, "data has been successfully deleted"));
            } else {
                return _BadRequest("Unable to delete: The data is currently in use");
            }
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountGroup,Action.Read)]
    [HttpGet("{id}")]
    public async Task<IActionResult> getDetail(int id)
    {
        try
        {
            var data = await cOAService.GetByID(id,userSession.BranchId);
            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            return _Ok(data);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountGroup,Action.Read)]
    [HttpGet("/code/{coa}")]
    public async Task<IActionResult> getDetailByCoa(string coa,
     [FromQuery(Name = "startDate")] DateTime? startDate = null,
     [FromQuery(Name = "endDate")] DateTime? endDate = null,
     [FromQuery(Name = "page")] int page = 1,
     [FromQuery(Name = "pageSize")] int pageSize = 10)
    {
        try
        {
            var data = await cOAService.GetByCode(coa,userSession.BranchId);
            var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;

            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            PagedResult<Journal> journals = new PagedResult<Journal>(new List<Journal>(), 0);

            if(startDate != null && endDate != null){
                // Filter by Transaction Status "Published" instead of Journal Status
                // Pass transactionStatus = "Published", status = null
                journals = await journalService.GetAll("", null, coa, null, userSession.BranchId, null, null, "Published", startDate, endDate, page, pageSize, "createddate", "desc");
                
                // Get Historical/Latest Balance for the period
                var endingBalance = await journalService.GetEndingBalance(coa, endDate.Value, userSession.BranchId ?? 0);
                data.Balance = endingBalance;
            }

            return _Ok(new { coa = data, journals = journals });
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

    [Authorize(Page.MasterAccountGroup, Action.Read, Page.TrxBank, Action.Read)]
    [HttpGet("/code/{coa}/export")]
    public async Task<IActionResult> exportDetailByCoa(string coa,
     [FromQuery(Name = "startDate")] DateTime? startDate = null,
     [FromQuery(Name = "endDate")] DateTime? endDate = null)
    {
        try
        {
            var data = await cOAService.GetByCode(coa,userSession.BranchId);
            var journalService = HttpContext.RequestServices.GetService(typeof(IJournalService)) as IJournalService;

            if (data == null)
                return _NotFound(string.Format(TAG, "Not Found"));

            List<Journal> journals = new List<Journal>();

            if(startDate != null && endDate != null){
                 var result = await journalService.GetAll("", null, coa, null, userSession.BranchId, null, null, "Published", startDate, endDate, -1, -1, "createddate", "desc");
                 journals = result.Data;

                var endingBalance = await journalService.GetEndingBalance(coa, endDate.Value, userSession.BranchId ?? 0);
                data.Balance = endingBalance;
            }

            var fileContent = ExportToDocs.ConvertDetailBukuKasToExcel(data, journals);
            var fileName = $"BukuKas_{coa}_{DateTime.Now:yyyyMMddHHmmss}.xlsx";

            return File(fileContent, "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet", fileName);
        }
        catch (Exception e)
        {
            _logger.LogError(LOG, e);
            return _BadRequest(e.Message);
        }
    }

}


