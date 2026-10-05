namespace RSSMBackend.Services.AccountReceivable;

public interface IArCombinedVoucherService
{
    /// <summary>
    /// AR items eligible to be added to a combined voucher:
    /// already finalized (has voucher_number), not yet paid, and not already in another batch.
    /// </summary>
    Task<object> GetEligibleItems(
        string? payerType,
        string? payerId,
        DateTime? startDate,
        DateTime? endDate,
        string? keyword,
        int page,
        int pageSize,
        int? excludeCombinedId = null);

    /// <summary>List of combined voucher batches (summary).</summary>
    Task<object> GetList(
        string? keyword,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize);

    /// <summary>Create a new combined batch with initial AR items.</summary>
    Task<object> Create(DateTime batchDate, string? description, List<int> arIds, int userId);

    /// <summary>Get a single combined batch with its items.</summary>
    Task<object?> GetDetail(int id);

    /// <summary>Add AR items to an existing batch.</summary>
    Task<bool> AddItems(int id, List<int> arIds, int userId);

    /// <summary>Remove AR items from a batch.</summary>
    Task<bool> RemoveItems(int id, List<int> arIds, int userId);

    /// <summary>Delete a combined batch (and its item links).</summary>
    Task<bool> Delete(int id);
}
