using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface ITransactionService
    {
        Task<PagedResult<Transaction>> GetAll(string? keyword,string? coa,string? transactionType,int? contactId,int? branchId,string? status,string? parent,DateTime? startDate,DateTime? endDate,int page, int pageSize,string? orderBy,string? orderType);
        Task<PagedResult<Transaction>> GetAllDetail(string? keyword,string? coa,string? transactionType,int? contactId,int? branchId,string? status,string? parent,DateTime? startDate,DateTime? endDate,int page, int pageSize,string? orderBy,string? orderType);
        Task<Transaction> GetByID(string id,int? branchId);
        Task<Transaction> GetForUpdateByID(string id,int? branchId);
        Task<string> GetCode(string type, int? branchId);
        Task<string> UpdateCode(string type, string lastCode, int? branchId);
        Task<string> Add(Transaction item);
        Task<string> Update(Transaction item);
        Task<string> UpdatePayment(PaymentUpdateRequest item);
        Task<string> Remove(Transaction item);
        Task<string> Void(Transaction item);
        Task<bool> CheckIfExistContactId(int contactId);
        Task<bool> CheckIfExistTransactionNo(string transactionNo,string? transactionType, int branchId);
        Task<Object> GetTotalTransactionByPeriod(DateTime transactionDate, string? transactionType, string? status); 
        Task<byte[]> ExportJournal(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType);
        Task<byte[]> ExportJournalFlat(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType);
        Task<byte[]> ExportTransactionList(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType); 
        Task<byte[]> ExportTransactionListFlat(string? keyword, string? coa, string? transactionType, int? contactId, int? branchId, string? status, string? parent, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType); 
    }
}