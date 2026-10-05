using RSSMBackend.DTOs.KhanzaSync;
using RSSMBackend.Models;

namespace RSSMBackend.Services.KhanzaSync;

public interface IKhanzaSyncService
{
    List<SyncModuleDto> GetModules();

    /// <param name="lockState">editable | locked | all</param>
    Task<PagedResult<SyncListItemDto>> GetRecords(
        string module, string? search, string? lockState, int page, int pageSize, int roleId);

    /// <summary>Bandingkan record aplikasi dengan data terbaru langsung dari Khanza. Tidak mengubah apa pun.</summary>
    Task<List<SyncCompareRecordDto>> Compare(string module, List<string> keys, int roleId, int branchId);

    /// <summary>
    /// Terapkan field terpilih dari Khanza ke aplikasi. Nilai Khanza diambil ulang di server - nilai yang
    /// dikirim client tidak pernah dipakai sebagai nilai tulis.
    /// </summary>
    Task<List<SyncApplyResultDto>> Apply(string module, SyncApplyRequestDto request, int roleId, int branchId, int userId);
}
