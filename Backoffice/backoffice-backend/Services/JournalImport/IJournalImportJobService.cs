using Microsoft.AspNetCore.Http;
using RSSMBackend.DTOs.JournalImport;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services.JournalImport;

public interface IJournalImportJobService
{
    /// <summary>Saves each accepted file to the Uploads volume and queues it as 'pending'.</summary>
    Task<JournalImportEnqueueResultDto> Enqueue(List<IFormFile> files, string importType, User? requester, int branchId);

    Task<PagedResult<JournalImportJobListItemDto>> GetAll(
        string? status, string? importType, int page, int pageSize, string? orderBy, string? orderType);

    Task<JournalImportJobDetailDto?> GetById(int id);

    /// <summary>Counts of pending/running -- the frontend uses this to gate its polling.</summary>
    Task<JournalImportQueueStatusDto> GetQueueStatus();

    /// <summary>Original uploaded file, for the download action in the history list.</summary>
    Task<(byte[] Content, string FileName)?> GetFileContent(int id);

    /// <summary>Removes a job that has not started yet, along with its stored file.</summary>
    Task<bool> Cancel(int id);
}
