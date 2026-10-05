namespace RSSMBackend.Models;

public class InvoiceExchangeResponse
{
    public string? SupplierName { get; set; }
    public int? TotalRow { get; set; }
    public List<DataInvoiceExchangeResponse>? DataInvoices { get; set; }
}

public class DataInvoiceExchangeResponse
{
    public int? InvoiceExchangeId { get; set; }
    public string? PONumber  { get; set; }
    public DateTime? ReceiptDate { get; set; }
    public DateTime? PODate { get; set; }
    public DateTime? DueDate { get; set; }
    public string? SupplierName { get; set; }
    public double? TotalTransaction { get; set; }
    public string? Status { get; set; }

    // Status pembayaran (Unpaid / Partial / Paid) supaya halaman Tukar Faktur ikut
    // memperlihatkan faktur yang sudah lunas -- termasuk yang dilunasi lewat
    // "Mark as Paid" di halaman Purchase Order (ManualPaid = true).
    public string? PaymentStatus { get; set; }
    public bool ManualPaid { get; set; }
}

public class InvoiceExchangeTransactionResponse
{
    public int? InvoiceExchangeId { get; set; }
    public string? SupplierName { get; set; }
    public int TotalRow { get; set; }
    public double TotalTransaction { get; set; }
    public string? Status { get; set; }
}

public class DataInvoiceExchangeExportResponse
{
    public string? PONumber { get; set; }
    public DateTime? ReceiptDate { get; set; }
    public DateTime? PODate { get; set; }
    public DateTime? DueDate { get; set; }
    public string? SupplierName { get; set; }
    public double? TotalTransaction { get; set; }
    public string? Status { get; set; }
}