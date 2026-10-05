namespace RSSMBackend.Models;

public class AccountPayableResponse
{
    public int InvoiceExchangeId { get; set; }
    public string? SupplierName { get; set; }
    public string? PONumber { get; set; }
    public string? InvoiceNumber { get; set; }
    public DateTime? ReceiptDate { get; set; }
    public DateTime? DueDate { get; set; }
    public double? TotalTransaction { get; set; }
    public double? PaidAmount { get; set; }
    public double? OutstandingAmount { get; set; }
    public string? Status { get; set; } // Exchange status: not sent, Sent
    public string? PaymentStatus { get; set; } // Unpaid, Paid, Partial
    public int? AgingDays { get; set; }
    public string? AgingBucket { get; set; } // Current, 1-30, 31-60, 61-90, >90
    public string? PaymentRef { get; set; }
    public DateTime? PaymentDate { get; set; }
    public string? PaymentCoaCode { get; set; }
    public string? PaymentCoaDesc { get; set; }

    /// <summary>DP PO yang dialokasikan ke invoice ini. Sudah ikut dikurangkan dari OutstandingAmount.</summary>
    public double? TotalDP { get; set; }

    // TRUE bila lunas lewat "Mark as Paid" di halaman Purchase Order, bukan lewat menu
    // Pembayaran. Dipakai UI untuk membedakan badge "Paid" vs "Paid (Manual)".
    public bool ManualPaid { get; set; }
    public string? ManualPaidNote { get; set; }
    public DateTime? ManualPaidAt { get; set; }
}

public class AccountPayablePaymentRequest
{
    public List<int> InvoiceIds { get; set; } = new();
    public DateTime PaymentDate { get; set; }
    public string CoaCode { get; set; } = string.Empty;
    public string CoaDesc { get; set; } = string.Empty;
    public string? PaymentRef { get; set; }
    public double Amount { get; set; }
    
    // Discount fields
    public double? DiscountPercent { get; set; } = 0;
    public double? DiscountAmount { get; set; } = 0;
    
    // PPH23 fields
    public double? Pph23Percent { get; set; } = 0;
    public double? Pph23Amount { get; set; } = 0;
}

public class AccountPayableAgingResponse
{
    public string? BatchNumber { get; set; }
    public string? SupplierName { get; set; }
    public string? PONumber { get; set; }
    public string? InvoiceNumber { get; set; }
    public DateTime? DueDate { get; set; }
    public double Current { get; set; }
    public double Days1To30 { get; set; }
    public double Days31To60 { get; set; }
    public double Days61To90 { get; set; }
    public double Over90Days { get; set; }
    public double Total { get; set; }
}

public class AccountPayableDetailResponse
{
    // Invoice info
    public int InvoiceExchangeId { get; set; }
    public string? SupplierName { get; set; }
    public string? PONumber { get; set; }
    public string? InvoiceNumber { get; set; }
    public DateTime? ReceiptDate { get; set; }
    public DateTime? DueDate { get; set; }
    public double? TotalTransaction { get; set; }
    public string? Status { get; set; }
    public string? PaymentStatus { get; set; }
    public int? AgingDays { get; set; }
    public string? AgingBucket { get; set; }
    
    // Payment info (if already paid)
    public string? PaymentRef { get; set; }
    public DateTime? PaymentDate { get; set; }
    public string? PaymentCoaCode { get; set; }
    public string? PaymentCoaDesc { get; set; }
    public double? PaidAmount { get; set; }
    
    // Discount info
    public double? DiscountPercent { get; set; }
    public double? DiscountAmount { get; set; }
    public double? TotalAfterDiscount { get; set; }
    
    // PPH23 info
    public double? Pph23Percent { get; set; }
    public double? Pph23Amount { get; set; }
    
    // PPN Info
    public double? PpnPercent { get; set; }
    public double? PpnAmount { get; set; }
    
    // DP list placeholder (untuk future development)
    public List<DPItemResponse> DPList { get; set; } = new();
    public double? TotalDP { get; set; }
    
    // Grand total
    public double? OutstandingAmount { get; set; }

    // Payment Batch
    public string? PaymentBatchNumber { get; set; }

    // Payment History List
    public List<PaymentHistoryItem> PaymentHistory { get; set; } = new();

    // Info khusus pelunasan lewat "Mark as Paid" (PO). Saat ManualPaid = true, blok
    // pembayaran normal di atas (PaymentRef, PaymentCoa*, PaymentBatchNumber,
    // PaymentHistory) memang kosong -- tidak ada transaksi bank & tidak ada jurnal.
    public bool ManualPaid { get; set; }
    public string? ManualPaidNote { get; set; }
    public DateTime? ManualPaidAt { get; set; }
    public int? ManualPaidBy { get; set; }
    public string? ManualPaidByName { get; set; }
}

public class DPItemResponse
{
    public int DpId { get; set; }
    public DateTime? DpDate { get; set; }
    public double Amount { get; set; }
    public string? Note { get; set; }
    public string? PaymentMethod { get; set; }
}

public class PaymentHistoryItem
{
    public DateTime PaymentDate { get; set; }
    public double Amount { get; set; }
    public string? Note { get; set; }
    public string? CoaCode { get; set; }
    public string? CoaName { get; set; }
    public string? PaymentRef { get; set; }
    public int? PaidBy { get; set; }

    // Diskon & PPh23 yang diakui pada pembayaran INI. Dicatat sejak fitur batal
    // pembayaran ada, supaya pembalikan jurnal bisa persis. Null untuk entri lama -
    // pembatalan akan memakai nilai dari invoice sebagai gantinya.
    public double? DiscountAmount { get; set; }
    public double? Pph23Amount { get; set; }
}

/// <summary>Body pembatalan pembayaran (per invoice maupun per batch).</summary>
public class CancelPaymentModel
{
    public string? Reason { get; set; }
}

// Batch Payment Models
public class BatchPaymentInvoiceItem
{
    public int InvoiceExchangeId { get; set; }
    public double? DiscountPercent { get; set; } = 0;
    public double? DiscountAmount { get; set; } = 0;
    public double? Pph23Percent { get; set; } = 0;
    public double? Pph23Amount { get; set; } = 0;
}

public class AccountPayableBatchPaymentRequest
{
    public DateTime PaymentDate { get; set; }
    public string CoaCode { get; set; } = string.Empty;
    public string CoaDesc { get; set; } = string.Empty;
    public string? PaymentRef { get; set; }
    public double TotalAmount { get; set; }
    public List<BatchPaymentInvoiceItem> Invoices { get; set; } = new();
}

// Batch Payment Detail Response
public class BatchPaymentDetailResponse
{
    public string PaymentBatchNumber { get; set; } = string.Empty;
    public DateTime? PaymentDate { get; set; }
    public string? PaymentRef { get; set; }
    public string? PaymentCoaCode { get; set; }
    public string? PaymentCoaDesc { get; set; }
    public double TotalPaid { get; set; }
    public double TotalDiscount { get; set; }
    public double TotalPph23 { get; set; }
    public int InvoiceCount { get; set; }
    public List<BatchPaymentInvoiceDetail> Invoices { get; set; } = new();
}

public class BatchPaymentInvoiceDetail
{
    public int InvoiceExchangeId { get; set; }
    public string? SupplierName { get; set; }
    public string? PONumber { get; set; }
    public string? InvoiceNumber { get; set; }
    public DateTime? DueDate { get; set; }
    public double? TotalTransaction { get; set; }
    public double? DiscountAmount { get; set; }
    public double? DiscountPercent { get; set; }
    public double? Pph23Amount { get; set; }
    public double? Pph23Percent { get; set; }
    public double? PaidAmount { get; set; }
    public string? PaymentStatus { get; set; }
}

// Batch PPN Lookup
public class BatchPpnRequest
{
    public List<int> InvoiceExchangeIds { get; set; } = new();
}

public class BatchPpnItem
{
    public int InvoiceExchangeId { get; set; }
    public string? PONumber { get; set; }
    public double PpnAmount { get; set; }
}
