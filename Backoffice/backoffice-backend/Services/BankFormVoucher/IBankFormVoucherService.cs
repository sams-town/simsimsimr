namespace RSSMBackend.Services.BankFormMaker;

public interface IBankFormVoucherService
{
    Task<object> GetList(string voucherType, string? keyword, DateTime? startDate, DateTime? endDate, int page, int pageSize);
    Task<object?> GetDetail(int id);
    Task<object> Create(BankFormVoucherInput input, int? branchId, int userId);
    Task<bool> Update(int id, BankFormVoucherInput input, int userId);
    Task<bool> Delete(int id);
}

/// <summary>Manual-entry payload for a bank form voucher (Penerimaan/Pengeluaran).</summary>
public class BankFormVoucherInput
{
    public string VoucherType { get; set; } = "PENERIMAAN";
    public DateTime VoucherDate { get; set; }
    public string PaymentType { get; set; } = "CASH";
    public string? PersonName { get; set; }
    public string? CoaCode { get; set; }
    public string? CoaName { get; set; }
    public decimal Amount { get; set; }
    public decimal DiscountPercent { get; set; }
    public decimal DiscountAmount { get; set; }
    public bool Pph23Enabled { get; set; }
    public decimal Pph23Percent { get; set; }
    public decimal Pph23Amount { get; set; }
    public string? Purpose { get; set; }
    public string? Notes { get; set; }
    public string? RecipientBankName { get; set; }
    public string? RecipientAccountName { get; set; }
    public string? RecipientAccountNumber { get; set; }
}
