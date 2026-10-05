using RSSMBackend.DTOs.MigrationJob;
using RSSMBackend.Entities;
using RSSMBackend.Models;

namespace RSSMBackend.Services.MigrationJob;

public interface IMigrationJobService
{
    Task<PagedResult<MigrationJobListItemDto>> GetAll(
        string? scriptName, string? status, int page, int pageSize, string? orderBy, string? orderType);

    Task<MigrationJobDetailDto> GetById(int id);

    Task<MigrationJobDetailDto> Create(CreateMigrationJobDto dto, User requester);

    Task<List<ScriptRunningStatusDto>> GetRunningStatus();

    IReadOnlyList<string> AllowedScripts { get; }
}
