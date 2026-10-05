using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
using RSSMBackend.DTOs.Journal;
namespace RSSMBackend.Services
{
    public interface IJournalService
    {
        // Task<PagedResult<Journal>> GetAll(string? keyword,string? status,string? parentCode,string? coaCode,string? source,int? branchId,string? vourcherId,DateTime? startDate,DateTime? endDate,int page, int pageSize,string? orderBy,string? orderType);
        Task<PagedResult<Journal>> GetAll(string? keyword, string? status, string? coaCode, int? contactId, int? branchId, string? transactionNo, string? transactionType, string? transactionStatus, DateTime? startDate, DateTime? endDate, int page, int pageSize, string? orderBy, string? orderType);
        
        Task<double> GetEndingBalance(string coaCode, DateTime endDate, int branchId);

        // Membuat/memperbarui checkpoint saldo akhir bulan (optimasi performa on-read).
        // Mengembalikan jumlah baris checkpoint yang ditulis. branchId null = semua branch.
        Task<int> GenerateMonthlyCheckpoint(int year, int month, int? branchId = null);

        Task<Journal> GetByID(int id, int? branchId);
        Task<List<Journal>> GetForUpdateByID(string id, int? branchId);
        Task<Journal> GetForUpdateOneByID(int id, int? branchId);
        Task<List<Journal>> GetByTransactionId(string id, int? branchId, bool? premier);
        Task<bool> checkIfExistCoa(string coa, int? branchId);
        Task<int> Add(Journal item);
        Task<int> Update(Journal item);
        Task<int> Remove(Journal item);
        Task<int> Void(Journal item);
        Task<int> UpdateMany(List<Journal> items);

        Task<PagedResult<LedgerResponse>> GetLedgers(DateTime? startDate, DateTime? endDate, string? coaCode, int page, int pageSize);
        Task<PagedResult<GeneralLedgerResponse>> GetGeneralLedgers(DateTime? startDate, DateTime? endDate, string? coaCode, int page, int pageSize);

        Task<BalanceSheetResponse> GetBalanceSheetAsync(DateTime? startDate, DateTime? endDate,int branchId);
        Task<ProfitLossResponse> GetProfitAndLosAsync(DateTime? startDate, DateTime? endDate,int branchId);
        Task<CashFlowResponse> GetCashFlowAsync(DateTime? startDate, DateTime? endDate, int branchId);

        Task<int> PostJournal(List<string> transactionIds);
        Task<string> GenerateJournalNo(DateTime date);

        Task<PagedResult<SummaryLedgerResponse>> GetLedgerSummary(
            DateTime? startDate,
            DateTime? endDate,
            string? coaCode = null,
            int page = 1,
            int pageSize = 10,
            string? coaType = null);

        Task<byte[]> ExportHistory(string? keyword, string? status, string? coaCode, int? contactId, int? branchId, string? transactionNo, string? transactionType, string? transactionStatus, DateTime? startDate, DateTime? endDate, string? orderBy, string? orderType);
        Task<byte[]> ExportLedgerSummary(DateTime? startDate, DateTime? endDate, string? coaCode = null, string? coaType = null);
        Task<byte[]> ExportBalanceSheet(DateTime? startDate, DateTime? endDate, int branchId);
        Task<byte[]> ExportProfitLoss(DateTime? startDate, DateTime? endDate, int branchId);
        Task<byte[]> ExportCashFlow(DateTime? startDate, DateTime? endDate, int branchId);

        Task<PagedResult<UnbalancedJournalResponse>> GetUnbalancedJournals(DateTime? startDate, DateTime? endDate, int page, int pageSize);
        Task<byte[]> ExportUnbalancedJournals(DateTime? startDate, DateTime? endDate);

        Task<ImportResult> ImportGeneralJournal(FileUpload fileUpload, int branchId, int userId);
        Task<ImportResult> ImportGeneralJournalTHB(FileUpload fileUpload, int branchId, int userId);

        // Stream overloads: same logic, but readable from a file on disk. Used by the background
        // queue worker (JournalImportJob), which has no IFormFile.
        Task<ImportResult> ImportGeneralJournalStream(Stream fileStream, int branchId, int userId);
        Task<ImportResult> ImportGeneralJournalThbStream(Stream fileStream, int branchId, int userId);
    }
}