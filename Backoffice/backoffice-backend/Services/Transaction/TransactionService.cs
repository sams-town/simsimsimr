using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using Microsoft.EntityFrameworkCore;
using RSSMBackend.Models;
using Microsoft.Extensions.Options;
using RSSMBackend.Helpers;
using OfficeOpenXml;
using OfficeOpenXml.Style;
using System.Drawing;

namespace RSSMBackend.Services
{
    public class TransactionService : ITransactionService
    {
        private readonly RSSMDbContext _context;
        private readonly AppSettings _appSettings;
        public TransactionService(RSSMDbContext context, IOptions<AppSettings> appSettings)
        {
            _context = context;
            _appSettings = appSettings.Value;
        }

        public async Task<PagedResult<Transaction>> GetAll(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Transaction> query = _context.Transactions;
            query = query.Include(x => x.Branch)
                .Include(x => x.Coa)
                .Include(x => x.CoaParent)
                .Include(x => x.Contact)
                .Include(x => x.Created)
                .Include(x => x.Updated);

            if (branchId != null)
            {
                query = query.Where(m => m.BranchId == branchId);
            }

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.CoaCode.ToLower().Contains(keyword.ToLower()) ||
                    m.CoaName.ToLower().Contains(keyword.ToLower()) ||
                    m.TransactionName.ToLower().Contains(keyword.ToLower()) ||
                    m.TransactionNo.ToLower().Contains(keyword.ToLower()) ||
                    m.TransactionRef.ToLower().Contains(keyword.ToLower()) ||
                    m.JournalNo.ToLower().Contains(keyword.ToLower()) ||
                    m.status.ToLower().Contains(keyword.ToLower()) ||
                    m.Coa.CoaName.ToLower().Contains(keyword.ToLower()) ||
                    m.CoaParent.CoaName.ToLower().Contains(keyword.ToLower()) ||
                    m.Contact.ContactName.ToLower().Contains(keyword.ToLower()) ||
                    m.Created.DisplayName.ToLower().Contains(keyword.ToLower()) ||
                    m.Updated.DisplayName.ToLower().Contains(keyword.ToLower())  
                );
            }

            if (coa != null)
            {
                query = query.Where(m => m.CoaCode.ToLower().Equals(coa.ToLower()));
            }

            if (status != null)
            {
                query = query.Where(m => m.status.ToLower().Equals(status.ToLower()));
            }

            if (transactionType != null)
            {
                query = query.Where(m => m.TransactionType.ToLower().Equals(transactionType.ToLower()));
            }

            if (contactId != null)
            {
                query = query.Where(m => m.ContactId == contactId);
            }


            if (parent != null)
            {
                query = query.Where(m => m.CoaParentCode.ToLower().Equals(parent));
            }


            if(endDate != null)
                endDate = endDate.Value.Date.AddDays(1).AddTicks(-1);

            if (startDate != null || endDate != null)
            {
                if (startDate != null && endDate != null)
                    query = query.Where(m => m.TransactionDate >= startDate.Value.Date 
                                            && m.TransactionDate <= endDate.Value); // ⚡ tanpa .Date
                else if (startDate != null && endDate == null)
                    query = query.Where(m => m.TransactionDate >= startDate.Value.Date);
                else if (startDate == null && endDate != null)
                    query = query.Where(m => m.TransactionDate <= endDate.Value); // ⚡ tanpa .Date
            }

             query = query.Where(m => m.status != Status.Deleted.ToString());

            orderBy = string.IsNullOrEmpty(orderBy) ? "createddate" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "desc" : orderType.ToLower();

            switch (orderBy?.ToLower())
            {
                case "transactionid":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionId) : query.OrderByDescending(x => x.TransactionId);
                    break;
                case "transactiondate":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionDate) : query.OrderByDescending(x => x.TransactionDate);
                    break;
                case "coacode":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaCode) : query.OrderByDescending(x => x.CoaCode);
                    break;
                case "coaname":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaName) : query.OrderByDescending(x => x.CoaName);
                    break;
                case "description":
                    query = orderType == "asc" ? query.OrderBy(x => x.Description) : query.OrderByDescending(x => x.Description);
                    break;
                case "transactionno":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionNo) : query.OrderByDescending(x => x.TransactionNo);
                    break;
                case "transactiontype":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionType) : query.OrderByDescending(x => x.TransactionType);
                    break;
                case "contactid":
                    query = orderType == "asc" ? query.OrderBy(x => x.ContactId) : query.OrderByDescending(x => x.ContactId);
                    break;
                case "branchid":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "createdby":
                    query = orderType == "asc" ? query.OrderBy(x => x.CreatedBy) : query.OrderByDescending(x => x.CreatedBy);
                    break;
                case "createddate":
                    query = orderType == "asc" ? query.OrderBy(x => x.CreatedDate) : query.OrderByDescending(x => x.CreatedDate);
                    break;
                case "status":
                    query = orderType == "asc" ? query.OrderBy(x => x.status) : query.OrderByDescending(x => x.status);
                    break;
                case "updatedby":
                    query = orderType == "asc" ? query.OrderBy(x => x.UpdatedBy) : query.OrderByDescending(x => x.UpdatedBy);
                    break;
                case "updateddate":
                    query = orderType == "asc" ? query.OrderBy(x => x.UpdatedDate) : query.OrderByDescending(x => x.UpdatedDate);
                    break;
                case "coaparentcode":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaParentCode) : query.OrderByDescending(x => x.CoaParentCode);
                    break;
                case "coaparentname":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaParentName) : query.OrderByDescending(x => x.CoaParentName);
                    break;
                case "transactionref":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionRef) : query.OrderByDescending(x => x.TransactionRef);
                    break;
                case "transactionname":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionName) : query.OrderByDescending(x => x.TransactionName);
                    break;
                case "amount":
                    query = orderType == "asc" ? query.OrderBy(x => x.Amount) : query.OrderByDescending(x => x.Amount);
                    break;
                case "isautoid":
                    query = orderType == "asc" ? query.OrderBy(x => x.IsAutoId) : query.OrderByDescending(x => x.IsAutoId);
                    break;
                case "paymentstatus":
                    query = orderType == "asc" ? query.OrderBy(x => x.PaymentStatus) : query.OrderByDescending(x => x.PaymentStatus);
                    break;
                case "paymentno":
                    query = orderType == "asc" ? query.OrderBy(x => x.PaymentNo) : query.OrderByDescending(x => x.PaymentNo);
                    break;
                case "paymentdate":
                    query = orderType == "asc" ? query.OrderBy(x => x.PaymentDate) : query.OrderByDescending(x => x.PaymentDate);
                    break;
                default:
                    // Fallback order
                    query = query.OrderByDescending(x => x.CreatedDate);
                    break;
            }

            query = query.Select(x => new Transaction
            {
                TransactionId = x.TransactionId,
                TransactionDate = x.TransactionDate,
                JournalNo = x.JournalNo,
                CoaCode = x.CoaCode,
                CoaName = x.Coa.CoaName,
                Description = x.Description,
                TransactionNo = x.TransactionNo,
                TransactionType = x.TransactionType,
                ContactId = x.ContactId,
                ContactName = x.Contact.ContactName,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                CreatedBy = x.CreatedBy,
                CreatedName = x.Created.DisplayName,
                CreatedDate = x.CreatedDate,
                status = x.status,
                UpdatedBy = x.UpdatedBy,
                UpdatedName = x.Updated.DisplayName,
                UpdatedDate = x.UpdatedDate,
                CoaParentCode = x.CoaParentCode,
                CoaParentName = x.CoaParent.CoaName,
                TransactionRef = x.TransactionRef,
                TransactionName = x.TransactionName,
                Amount = x.Amount,
                IsAutoId = x.IsAutoId,
                PaymentStatus = x.PaymentStatus,
                PaymentNo = x.PaymentNo,
                PaymentDate = x.PaymentDate
            });


            int total = await query.CountAsync();
            if (page != -1)
                query = query.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Transaction>(await query.ToListAsync(), total);
        }

        public async Task<PagedResult<Transaction>> GetAllDetail(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, int page, int pageSize, string? orderBy, string? orderType)
        {

            IQueryable<Transaction> query = _context.Transactions;
            IQueryable<Journal> journals = _context.Journals;
            query = query.Include(x => x.Branch)
            .Include(x => x.Coa)
            .Include(x => x.CoaParent)
            .Include(x => x.Contact)
            .Include(x => x.Created)
            .Include(x => x.Updated);

            // var emptyJournals = new List<Journal>();
            // var query = trxQuery.GroupJoin(_context.Journals, trx => trx.TransactionId, jurnal => jurnal.TransactionId, (trx, jurnal) => new { trx, jurnal })
            //           .Select(x => new { x.trx, Journal = x.jurnal.Any() ? x.jurnal : emptyJournals });


            if (branchId != null)
            {
                query = query.Where(m => m.BranchId == branchId);
            }

            if (!string.IsNullOrEmpty(keyword))
            {
                query = query.Where(m => m.CoaCode.ToLower().Contains(keyword.ToLower()) ||
                     m.CoaName.ToLower().Contains(keyword.ToLower()) ||
                     m.TransactionName.ToLower().Contains(keyword.ToLower()) ||
                     m.TransactionNo.ToLower().Contains(keyword.ToLower()) ||
                     m.TransactionRef.ToLower().Contains(keyword.ToLower()) ||
                     m.JournalNo.ToLower().Contains(keyword.ToLower()) ||
                      m.status.ToLower().Contains(keyword.ToLower())
                );
            }

            if (coa != null)
            {
                query = query.Where(m => m.CoaCode.ToLower().Equals(coa.ToLower()));
            }

            if (status != null)
            {
                query = query.Where(m => m.status.ToLower().Equals(status.ToLower()));
            }

            if (transactionType != null)
            {
                query = query.Where(m => m.TransactionType.ToLower().Equals(transactionType.ToLower()));
            }

            if (contactId != null)
            {
                query = query.Where(m => m.ContactId == contactId);
            }


            if (parent != null)
            {
                query = query.Where(m => m.CoaParentCode.ToLower().Equals(parent));
            }

            if(endDate != null)
                endDate = endDate.Value.Date.AddDays(1).AddTicks(-1);


            if (startDate != null || endDate != null)
            {
                if (startDate != null && endDate != null)
                    query = query.Where(m => m.TransactionDate >= startDate.Value.Date 
                                            && m.TransactionDate <= endDate.Value); // ⚡ tanpa .Date
                else if (startDate != null && endDate == null)
                    query = query.Where(m => m.TransactionDate >= startDate.Value.Date);
                else if (startDate == null && endDate != null)
                    query = query.Where(m => m.TransactionDate <= endDate.Value); // ⚡ tanpa .Date
            }

            query = query.Where(m => m.status != Status.Deleted.ToString());

            orderBy = string.IsNullOrEmpty(orderBy) ? "createddate" : orderBy.ToLower();
            orderType = string.IsNullOrEmpty(orderType) ? "desc" : orderType.ToLower();

            switch (orderBy?.ToLower())
            {
                case "transactionid":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionId) : query.OrderByDescending(x => x.TransactionId);
                    break;
                case "transactiondate":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionDate) : query.OrderByDescending(x => x.TransactionDate);
                    break;
                case "coacode":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaCode) : query.OrderByDescending(x => x.CoaCode);
                    break;
                case "coaname":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaName) : query.OrderByDescending(x => x.CoaName);
                    break;
                case "description":
                    query = orderType == "asc" ? query.OrderBy(x => x.Description) : query.OrderByDescending(x => x.Description);
                    break;
                case "transactionno":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionNo) : query.OrderByDescending(x => x.TransactionNo);
                    break;
                case "transactiontype":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionType) : query.OrderByDescending(x => x.TransactionType);
                    break;
                case "contactid":
                    query = orderType == "asc" ? query.OrderBy(x => x.ContactId) : query.OrderByDescending(x => x.ContactId);
                    break;
                case "branchid":
                    query = orderType == "asc" ? query.OrderBy(x => x.BranchId) : query.OrderByDescending(x => x.BranchId);
                    break;
                case "createdby":
                    query = orderType == "asc" ? query.OrderBy(x => x.CreatedBy) : query.OrderByDescending(x => x.CreatedBy);
                    break;
                case "createddate":
                    query = orderType == "asc" ? query.OrderBy(x => x.CreatedDate) : query.OrderByDescending(x => x.CreatedDate);
                    break;
                case "status":
                    query = orderType == "asc" ? query.OrderBy(x => x.status) : query.OrderByDescending(x => x.status);
                    break;
                case "updatedby":
                    query = orderType == "asc" ? query.OrderBy(x => x.UpdatedBy) : query.OrderByDescending(x => x.UpdatedBy);
                    break;
                case "updateddate":
                    query = orderType == "asc" ? query.OrderBy(x => x.UpdatedDate) : query.OrderByDescending(x => x.UpdatedDate);
                    break;
                case "coaparentcode":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaParentCode) : query.OrderByDescending(x => x.CoaParentCode);
                    break;
                case "coaparentname":
                    query = orderType == "asc" ? query.OrderBy(x => x.CoaParentName) : query.OrderByDescending(x => x.CoaParentName);
                    break;
                case "transactionref":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionRef) : query.OrderByDescending(x => x.TransactionRef);
                    break;
                case "transactionname":
                    query = orderType == "asc" ? query.OrderBy(x => x.TransactionName) : query.OrderByDescending(x => x.TransactionName);
                    break;
                case "amount":
                    query = orderType == "asc" ? query.OrderBy(x => x.Amount) : query.OrderByDescending(x => x.Amount);
                    break;
                case "isautoid":
                    query = orderType == "asc" ? query.OrderBy(x => x.IsAutoId) : query.OrderByDescending(x => x.IsAutoId);
                    break;
                case "paymentstatus":
                    query = orderType == "asc" ? query.OrderBy(x => x.PaymentStatus) : query.OrderByDescending(x => x.PaymentStatus);
                    break;
                case "paymentno":
                    query = orderType == "asc" ? query.OrderBy(x => x.PaymentNo) : query.OrderByDescending(x => x.PaymentNo);
                    break;
                case "paymentdate":
                    query = orderType == "asc" ? query.OrderBy(x => x.PaymentDate) : query.OrderByDescending(x => x.PaymentDate);
                    break;
                default:
                    // Fallback order
                    query = query.OrderByDescending(x => x.CreatedDate);
                    break;
            }


            var q = query.Select(x =>
            new Transaction
            {
                TransactionId = x.TransactionId,
                TransactionDate = x.TransactionDate,
                JournalNo = x.JournalNo,
                PostedDate = x.PostedDate,
                CoaCode = x.CoaCode,
                CoaName = x.Coa.CoaName,
                CoaParentCode = x.CoaParentCode,
                CoaParentName = x.CoaParent.CoaName,
                Description = x.Description,
                TransactionNo = x.TransactionNo,
                TransactionType = x.TransactionType,
                TransactionName = x.TransactionName,
                TransactionRef = x.TransactionRef,
                Amount = x.Amount,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                ContactId = x.ContactId,
                ContactName = x.Contact.ContactName,
                CreatedBy = x.CreatedBy,
                CreatedName = x.Created.DisplayName,
                CreatedDate = x.CreatedDate,
                status = x.status,
                UpdatedBy = x.UpdatedBy,
                UpdatedName = x.Updated.DisplayName,
                UpdatedDate = x.UpdatedDate,
                IsAutoId = x.IsAutoId,
                Details = (List<Journal>)journals
                .Where(y => y.TransactionId.Equals(x.TransactionId))
                .OrderByDescending(y => y.DebitAmount)
                .ToList(),
                PaymentNo = x.PaymentNo,
                PaymentStatus = x.PaymentStatus,
                PaymentDate = x.PaymentDate,
            });

            int total = await q.CountAsync();
            if (page != -1)
                q = q.Skip((page - 1) * pageSize).Take(pageSize);

            return new PagedResult<Transaction>(await q.ToListAsync(), total);
        }

        public async Task<Transaction> GetByID(string id, int? branchId)
        {
            IQueryable<Transaction> query = _context.Transactions;
            IQueryable<Journal> journals = _context.Journals;
            query = query.Include(x => x.Branch)
            .Include(x => x.Coa)
            .Include(x => x.CoaParent)
            .Include(x => x.Contact)
            .Include(x => x.Created)
            .Include(x => x.Updated);

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Where(m => m.status != Status.Deleted.ToString());

            query = query.Select(x =>
            new Transaction
            {
                TransactionId = x.TransactionId,
                TransactionDate = x.TransactionDate,
                JournalNo = x.JournalNo,
                CoaCode = x.CoaCode,
                CoaName = x.Coa.CoaName,
                CoaParentCode = x.CoaParentCode,
                CoaParentName = x.CoaParent.CoaName,
                Description = x.Description,
                TransactionNo = x.TransactionNo,
                TransactionType = x.TransactionType,
                TransactionName = x.TransactionName,
                TransactionRef = x.TransactionRef,
                Amount = x.Amount,
                BranchId = x.BranchId,
                BranchName = x.Branch.BranchName,
                ContactId = x.ContactId,
                ContactName = x.Contact.ContactName,
                CreatedBy = x.CreatedBy,
                CreatedName = x.Created.DisplayName,
                CreatedDate = x.CreatedDate,
                status = x.status,
                UpdatedBy = x.UpdatedBy,
                UpdatedName = x.Updated.DisplayName,
                UpdatedDate = x.UpdatedDate,
                IsAutoId = x.IsAutoId,
                Details = (List<Journal>)journals
                .Where(y => y.TransactionId.Equals(x.TransactionId))
                .OrderByDescending(y => y.DebitAmount)
                .ToList(),
                PaymentNo = x.PaymentNo,
                PaymentStatus = x.PaymentStatus,
                PaymentDate = x.PaymentDate,
            });
            return query.SingleOrDefault(x => x.TransactionId.Equals(id));
        }

        public async Task<Transaction> GetForUpdateByID(string id, int? branchId)
        {
            IQueryable<Transaction> query = _context.Transactions;

            if (branchId != null)
                query = query.Where(x => x.BranchId == branchId);

            query = query.Where(m => m.status != Status.Deleted.ToString());

            return query.SingleOrDefault(x => x.TransactionId.Equals(id));
        }

        public async Task<string> GetCode(string type, int? branchId)
        {
            IQueryable<AutoNumber> queryAuto = _context.CfgAutoNumber;
            if (branchId != null)
                queryAuto = queryAuto.Where(x => x.BranchId == branchId);

            var autoNumber = queryAuto.SingleOrDefault(x => x.Code.ToLower().Equals(type.ToLower()));
            
            if (autoNumber == null)
            {
                throw new Exception($"Auto number configuration tidak ditemukan untuk type: {type}. Silakan tambahkan record di tabel cfg_autonumber.");
            }

            string code = autoNumber.LastCode.Substring(0, autoNumber.LastCode.Length - 3);

            int number = int.Parse(autoNumber.LastCode.Substring(autoNumber.LastCode.Length - 3, 3));
            number++;

            string numberStr = number.ToString().PadLeft(autoNumber.LastCode.Substring(autoNumber.LastCode.Length - 3, 3).Length, '0');
            string transactionNo = $"{code}{numberStr}";

            return transactionNo;
        }

        public async Task<string> UpdateCode(string type, string lastCode, int? branchId)
        {
            IQueryable<AutoNumber> queryAuto = _context.CfgAutoNumber;
            if (branchId != null)
                queryAuto = queryAuto.Where(x => x.BranchId == branchId);

            var data = queryAuto.SingleOrDefault(x => x.Code.ToLower().Equals(type.ToLower()));
            data.LastCode = lastCode;
            _context.CfgAutoNumber.Update(data);
            await _context.SaveChangesAsync();
            return data.Code;
        }

        public async Task<string> Add(Transaction item)
        {

            IQueryable<Transaction> query = _context.Transactions;

            if (item.BranchId != null)
                query = query.Where(x => x.BranchId == item.BranchId);
            

            // if (query.Any(u =>  u.status != Status.Deleted.ToString() && u.TransactionNo.ToLower().Equals(item.TransactionNo.ToLower()) && u.TransactionType.ToLower().Equals(item.TransactionType.ToLower())))
            //     throw new ApplicationException("Transaction No is already in use.");

            _context.Transactions.Add(item);
            _context.SaveChanges();
            return item.TransactionId;
        }

        public async Task<string> Update(Transaction item)
        {

            var data = await GetByID(item.TransactionId, item.BranchId);
            if (data == null)
                throw new ApplicationException("Transaction Not Found");

            data.status = item.status;
            data.UpdatedBy = item.UpdatedBy;
            data.UpdatedDate = item.UpdatedDate;

            _context.Transactions.Update(data);
            _context.SaveChanges();

            return data.TransactionId;
        }

        

        public async Task<string> UpdatePayment(PaymentUpdateRequest item)
        {
            var data = await GetByID(item.TransactionId, null);
            if (data == null)
                throw new ApplicationException("Transaction Not Found");

            data.PaymentStatus = item.PaymentStatus;
            data.PaymentNo = item.PaymentNo;
            data.PaymentDate = item.PaymentDate;

            _context.Transactions.Update(data);
            _context.SaveChanges();

            return data.TransactionId;
        }

        public async Task<string> Remove(Transaction item)
        {

            _context.Transactions.Update(item);
            _context.SaveChanges();
            return item.TransactionId;
        }

        public async Task<string> Void(Transaction item)
        {

            item.status = Status.Void.ToString();
            item.UpdatedBy = item.UpdatedBy;
            item.UpdatedDate = item.UpdatedDate;

            _context.Transactions.Update(item);
            _context.SaveChanges();
            return item.TransactionId;
        }

        public async Task<bool> CheckIfExistContactId(int id)
        {
            return await _context.Transactions.AnyAsync(x => x.ContactId.Equals(id));
        }
        
        public async Task<bool> CheckIfExistTransactionNo(string transactionNo,string? transactionType,int branchId)
        {
            IQueryable<Transaction> query = _context.Transactions;

            query = query.Where(m => m.status != Status.Deleted.ToString());
            
            if (!string.IsNullOrEmpty(transactionType))
                 query = query.Where(m => m.TransactionType == transactionType);

                return await query.AnyAsync(x => x.BranchId == branchId && x.TransactionNo.Equals(transactionNo));
        }

        public async Task<Object> GetTotalTransactionByPeriod(DateTime transactionDate, string? transactionType, string? status)
        {
            IQueryable<Transaction> query = _context.Transactions;

            query = query.Where(m => m.status != Status.Deleted.ToString());

            if (!string.IsNullOrEmpty(transactionType))
                query = query.Where(x => x.TransactionType.ToLower().Equals(transactionType.ToLower()));

            if (!string.IsNullOrEmpty(status))
                query = query.Where(x => x.status.ToLower().Equals(status.ToLower()));

            var current = await GetTotalTransactionByDate(transactionDate, query);
            var dayBefore = await GetTotalTransactionByDate(transactionDate.AddDays(-1), query);
            var weekBefore = await GetTotalTransactionByDate(transactionDate.AddDays(-7), query);
            var monthBefore = await GetTotalTransactionByDate(transactionDate.AddMonths(-1), query);

            var response = new TotalTransactionByTimeResponse
            {
                Current = current,
                DayBefore = dayBefore,
                WeekBefore = weekBefore,
                MonthBefore = monthBefore
            };

            return response;
        }

        private async Task<DataTotalTransaction> GetTotalTransactionByDate(DateTime date, IQueryable<Transaction> query)
        {
            date = DateTime.SpecifyKind(date, DateTimeKind.Utc).Date;
            var total = query.Where(x => x.TransactionDate.Value.Date == date)
                                            .GroupBy(x => x.TransactionDate.Value.Date)
                                            .Select(x => (double?)x.Sum(a => a.Amount))
                                            .SingleOrDefault() ?? 0.0;

            return new DataTotalTransaction
            {
                Date = date,
                Total = total
            };
        }
        public async Task<byte[]> ExportJournal(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType)
        {
            ExcelPackage.LicenseContext = LicenseContext.NonCommercial;
            var data = await GetAllDetail(keyword, coa, transactionType, contactId, branchId, status, parent, startDate, endDate, 1, int.MaxValue, orderBy, orderType);
            
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("Journal Report");
                
                int row = 1;

                foreach (var trx in data.Data)
                {
                    // Transaction Header Block - Blue Background
                    var headerRange = sheet.Cells[row, 1, row, 12];
                    headerRange.Style.Font.Bold = true;
                    headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
                    headerRange.Style.Fill.BackgroundColor.SetColor(Color.FromArgb(67, 97, 238));
                    headerRange.Style.Font.Color.SetColor(Color.White);

                    // Row 1: TransNo, Date, Status
                    sheet.Cells[row, 1].Value = "NO TRANSAKSI";
                    sheet.Cells[row, 2].Value = trx.TransactionNo;
                    sheet.Cells[row, 3].Value = "TANGGAL";
                    sheet.Cells[row, 4].Value = trx.TransactionDate?.ToString("dd/MM/yyyy");
                    sheet.Cells[row, 5].Value = "STATUS";
                    sheet.Cells[row, 6].Value = trx.status;
                    row++;

                    // Row 2: JournalNo, Khanza Journal No
                    sheet.Cells[row, 1].Value = "NO JURNAL";
                    sheet.Cells[row, 2].Value = trx.JournalNo;
                    sheet.Cells[row, 3].Value = "REF NO";
                    sheet.Cells[row, 4].Value = trx.TransactionNo;
                    row++;

                    // Row 3: Description, Total

                    // Detail Header
                    var detailHeaderStyle = sheet.Cells[row, 1, row, 12].Style;
                    detailHeaderStyle.Font.Bold = true;
                    detailHeaderStyle.Border.Bottom.Style = ExcelBorderStyle.Thin;

                    sheet.Cells[row, 1].Value = "NAMA AKUN";
                    sheet.Cells[row, 2].Value = "NO AKUN";
                    sheet.Cells[row, 3].Value = "DESKRIPSI";
                    sheet.Cells[row, 4].Value = "DEBIT";
                    sheet.Cells[row, 5].Value = "KREDIT";
                    sheet.Cells[row, 6].Value = "NAMA POLI";
                    sheet.Cells[row, 7].Value = "CARA BAYAR";
                    sheet.Cells[row, 8].Value = "TGL MASUK";
                    sheet.Cells[row, 9].Value = "TGL KELUAR";
                    sheet.Cells[row, 4].Style.HorizontalAlignment = ExcelHorizontalAlignment.Right;
                    sheet.Cells[row, 5].Style.HorizontalAlignment = ExcelHorizontalAlignment.Right;
                    row++;

                    // Details
                    if (trx.Details != null)
                    {
                        foreach (var detail in trx.Details)
                        {
                            sheet.Cells[row, 1].Value = detail.CoaName;
                            sheet.Cells[row, 2].Value = detail.CoaCode;
                            sheet.Cells[row, 3].Value = detail.JournalDesc;
                            
                            sheet.Cells[row, 4].Value = detail.DebitAmount;
                            sheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";
                            
                            sheet.Cells[row, 5].Value = detail.CreditAmount;
                            sheet.Cells[row, 5].Style.Numberformat.Format = "#,##0.00";
                            
                            sheet.Cells[row, 6].Value = detail.ClinicName ?? "";
                            sheet.Cells[row, 7].Value = detail.PayerName ?? "";
                            sheet.Cells[row, 8].Value = detail.AdmissionDate?.ToString("dd/MM/yyyy") ?? "";
                            sheet.Cells[row, 9].Value = detail.DischargeDate?.ToString("dd/MM/yyyy") ?? "";
                            
                            row++;
                        }
                    }
                    
                    row++; // Spacer
                }

                sheet.Column(1).Width = 25;
                sheet.Column(2).Width = 20;
                sheet.Column(3).Width = 30;
                sheet.Column(4).Width = 18;
                sheet.Column(5).Width = 18;
                sheet.Column(6).Width = 20;
                sheet.Column(7).Width = 20;
                sheet.Column(8).Width = 15;
                sheet.Column(9).Width = 15;

                return package.GetAsByteArray();
            }
        }

        public async Task<byte[]> ExportTransactionList(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType)
        {
            ExcelPackage.LicenseContext = LicenseContext.NonCommercial;
            var data = await GetAllDetail(keyword, coa, transactionType, contactId, branchId, status, parent, startDate, endDate, 1, int.MaxValue, orderBy, orderType);
            
            if (transactionType?.ToLower() == "transfer")
            {
                return ExportToDocs.ConvertTransferListToExcel(data.Data);
            }
            else if (transactionType?.ToLower() == "revenue" || transactionType?.ToLower() == "deposit")
            {
                return ExportToDocs.ConvertRevenueExpenseListToExcel(data.Data, "Daftar Penerimaan");
            }
             else if (transactionType?.ToLower() == "expense" || transactionType?.ToLower() == "payment")
            {
                return ExportToDocs.ConvertRevenueExpenseListToExcel(data.Data, "Daftar Pengeluaran");
            }

            // Default fallback or error? Let's default to Revenue/Expense style but generic title
            return ExportToDocs.ConvertRevenueExpenseListToExcel(data.Data, "Daftar Transaksi");
        }

        public async Task<byte[]> ExportTransactionListFlat(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType)
        {
            ExcelPackage.LicenseContext = LicenseContext.NonCommercial;
            var data = await GetAllDetail(keyword, coa, transactionType, contactId, branchId, status, parent, startDate, endDate, 1, int.MaxValue, orderBy, orderType);
            
            if (transactionType?.ToLower() == "transfer")
            {
               return ExportToDocs.ConvertTransferListToExcel(data.Data);
            }
            else if (transactionType?.ToLower() == "revenue" || transactionType?.ToLower() == "deposit")
            {
                return ExportToDocs.ConvertRevenueExpenseListToExcelFlat(data.Data, "Daftar Penerimaan");
            }
             else if (transactionType?.ToLower() == "expense" || transactionType?.ToLower() == "payment")
            {
                return ExportToDocs.ConvertRevenueExpenseListToExcelFlat(data.Data, "Daftar Pengeluaran");
            }

            return ExportToDocs.ConvertRevenueExpenseListToExcelFlat(data.Data, "Daftar Transaksi");
        }

        public async Task<byte[]> ExportJournalFlat(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType)
        {
            ExcelPackage.LicenseContext = LicenseContext.NonCommercial;
            var data = await GetAllDetail(keyword, coa, transactionType, contactId, branchId, status, parent, startDate, endDate, 1, int.MaxValue, orderBy, orderType);
            
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("Journal Report");

                // Header Row
                var headers = new[] { "NO JURNAL", "REF NO", "TANGGAL", "STATUS", "NAMA AKUN", "NO AKUN", "DESKRIPSI", "DEBIT", "KREDIT", "NAMA POLI", "CARA BAYAR", "TGL MASUK", "TGL KELUAR" };
                for (int i = 0; i < headers.Length; i++)
                {
                    sheet.Cells[1, i + 1].Value = headers[i];
                }

                var headerRange = sheet.Cells[1, 1, 1, headers.Length];
                headerRange.Style.Font.Bold = true;
                headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
                headerRange.Style.Fill.BackgroundColor.SetColor(Color.FromArgb(67, 97, 238));
                headerRange.Style.Font.Color.SetColor(Color.White);

                int row = 2;
                foreach (var trx in data.Data ?? new List<Transaction>())
                {
                    if (trx.Details != null)
                    {
                        foreach (var detail in trx.Details)
                        {
                            sheet.Cells[row, 1].Value = trx.JournalNo;
                            sheet.Cells[row, 2].Value = trx.TransactionNo;
                            sheet.Cells[row, 3].Value = trx.TransactionDate?.ToString("dd/MM/yyyy");
                            sheet.Cells[row, 4].Value = trx.status;
                            sheet.Cells[row, 5].Value = detail.CoaName;
                            sheet.Cells[row, 6].Value = detail.CoaCode;
                            sheet.Cells[row, 7].Value = detail.JournalDesc;
                            
                            sheet.Cells[row, 8].Value = detail.DebitAmount;
                            sheet.Cells[row, 8].Style.Numberformat.Format = "#,##0.00";
                            
                            sheet.Cells[row, 9].Value = detail.CreditAmount;
                            sheet.Cells[row, 9].Style.Numberformat.Format = "#,##0.00";
                            
                            sheet.Cells[row, 10].Value = detail.ClinicName ?? "";
                            sheet.Cells[row, 11].Value = detail.PayerName ?? "";
                            sheet.Cells[row, 12].Value = detail.AdmissionDate?.ToString("dd/MM/yyyy") ?? "";
                            sheet.Cells[row, 13].Value = detail.DischargeDate?.ToString("dd/MM/yyyy") ?? "";
                            
                            row++;
                        }
                    }
                }

                // Column widths
                sheet.Column(1).Width = 20;
                sheet.Column(2).Width = 25;
                sheet.Column(3).Width = 15;
                sheet.Column(4).Width = 12;
                sheet.Column(5).Width = 25;
                sheet.Column(6).Width = 15;
                sheet.Column(7).Width = 30;
                sheet.Column(8).Width = 18;
                sheet.Column(9).Width = 18;
                sheet.Column(10).Width = 20;
                sheet.Column(11).Width = 20;
                sheet.Column(12).Width = 15;
                sheet.Column(13).Width = 15;

                return package.GetAsByteArray();
            }
        }
    }
}