namespace RSSMBackend.Services.Interface;

using RSSMBackend.Entities;

public interface IAccountReceivableService
{
    Task<object> GetList(
        string? payerType,
        string? paymentStatus,
        string? serviceType,
        DateTime? startDate,
        DateTime? endDate,
        int page,
        int pageSize
    );

    Task<AccountReceivable?> GetById(int id);

    Task<object> GetAgingReport(
        string? payerType,
        DateTime? asOfDate
    );

    Task<bool> ProcessPayment(
        List<int> arIds,
        DateTime paymentDate,
        string paymentCoaCode,
        string paymentRef,
        int userId
    );

    Task<object> GetPayerTypeOptions();
    Task<object> Export(string? payerType, string? paymentStatus, DateTime? startDate, DateTime? endDate, string type);
}
