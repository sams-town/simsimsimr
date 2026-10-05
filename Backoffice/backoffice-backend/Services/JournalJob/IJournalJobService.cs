using Microsoft.EntityFrameworkCore.Storage;
using RSSMBackend.DbContext;
using RSSMBackend.Entities;
using RSSMBackend.Models;
namespace RSSMBackend.Services
{
    public interface IJournalJobService
    {
        Task<List<JobJournalResult>> Get(DateTime? journalDate);
        Task<List<JobTransactionResult>> GetTransaction(DateTime? journalDate);
    }
}