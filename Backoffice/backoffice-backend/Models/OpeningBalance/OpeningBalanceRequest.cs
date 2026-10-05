namespace RSSMBackend.Models;

// Payload input saldo awal (modul Saldo Awal). Ditulis langsung sebagai baris
// checkpoint 'Opening' di public.trx_balance oleh OpeningBalanceService.
public class OpeningBalanceRequest
{
    public int? Id { get; set; }            // diisi saat update
    public string CoaCode { get; set; } = string.Empty;
    public DateTime AsOfDate { get; set; }  // tanggal cut-off saldo awal (period_date)
    public double Amount { get; set; }       // nominal saldo awal (tanda sesuai posisi normal COA)
    public string? Note { get; set; }
    public int? BranchId { get; set; }       // null -> pakai branch dari sesi user
}
