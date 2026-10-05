namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("trx_invoice_exchange", Schema = "public")]
public class InvoiceExchange
{
    [Key] 
    [Column("invoice_exchange_id")] 
    public int InvoiceExchangeId { get; set; }
    
    [Column("journal_id")]
    public int? JournalId { get; set; }

    [Column("po_number")]
    public string? PONumber { get; set; }

    [Column("receipt_date")]
    public DateTime? ReceiptDate { get; set; }

    [Column("po_date")]
    public DateTime? PODate { get; set; }

    [Column("due_date")]
    public DateTime? DueDate { get; set; }

    [Column("supplier_name")]
    public string? SupplierName { get; set; }

    [Column("total_transaction")]
    public Double? TotalTransaction { get; set; }

    [Column("supplier_code")]
    public string? SupplierCode { get; set; }
    
    [Column("status")]
    public string? Status { get; set; }
    
    [Column("updated_by")]
    public int? UpdatedBy { get; set; }
    
    [Column("inserted_date")]
    public DateTime? InsertedDate { get; set; }
    
    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }
    
    [Column("invoice_number")]
    public string? InvoiceNumber { get; set; }
    
    // Payment tracking fields
    [Column("payment_status")]
    public string? PaymentStatus { get; set; } = "Unpaid"; // Unpaid, Paid, Partial
    
    [Column("paid_amount")]
    public Double? PaidAmount { get; set; } = 0;
    
    [Column("payment_date")]
    public DateTime? PaymentDate { get; set; }
    
    [Column("payment_ref")]
    public string? PaymentRef { get; set; }
    
    [Column("payment_coa_code")]
    public string? PaymentCoaCode { get; set; }
    
    [Column("payment_coa_desc")]
    public string? PaymentCoaDesc { get; set; }

    [Column("payment_by")]
    public int? PaymentBy { get; set; }

    [Column("journal_id_payment")]
    public int? JournalIdPayment { get; set; }

    [Column("batch_number")]
    public string? BatchNumber { get; set; }

    [Column("exchange_date")]
    public DateTime? ExchangeDate { get; set; }

    [Column("exchange_by")]
    public int? ExchangeBy { get; set; }

    // Discount fields
    [Column("discount_percent")]
    public Double? DiscountPercent { get; set; } = 0;
    
    [Column("discount_amount")]
    public Double? DiscountAmount { get; set; } = 0;

    // PPH23 fields
    [Column("pph23_percent")]
    public Double? Pph23Percent { get; set; } = 0;
    
    [Column("pph23_amount")]
    public Double? Pph23Amount { get; set; } = 0;

    // PPN fields. Hanya terisi untuk invoice non-PO yang diinput manual;
    // untuk invoice via PO, PPN tetap dibaca dari jurnal PO (ACCOUNT_PPN_MASUKAN).
    [Column("ppn_percent")]
    public Double? PpnPercent { get; set; } = 0;

    [Column("ppn_amount")]
    public Double? PpnAmount { get; set; } = 0;

    [Column("outstanding_amount")]
    public Double? OutstandingAmount { get; set; }

    [Column("payment_batch_number")]
    public string? PaymentBatchNumber { get; set; }

    [Column("payment_history", TypeName = "jsonb")]
    public string? PaymentHistory { get; set; }

    // Pembeda pelunasan lewat "Mark as Paid" di halaman Purchase Order vs pembayaran
    // normal lewat menu Pembayaran. Saat manual_paid = true: payment_ref, payment_coa_*,
    // payment_batch_number, dan payment_history sengaja dibiarkan kosong, dan TIDAK ada
    // jurnal yang diposting. Lihat PurchaseOrderService.MarkAsPaid / UnmarkAsPaid.
    // Jejak pembatalan pembayaran. Pembayaran yang dibatalkan tidak dihapus: jurnal
    // aslinya tetap ada dan dibalik oleh transaksi PaymentCancel di bawah ini.
    [Column("payment_cancelled_by")]
    public int? PaymentCancelledBy { get; set; }

    [Column("payment_cancelled_date")]
    public DateTime? PaymentCancelledDate { get; set; }

    [Column("payment_cancel_reason")]
    public string? PaymentCancelReason { get; set; }

    [Column("payment_cancel_transaction_id")]
    public string? PaymentCancelTransactionId { get; set; }

    [Column("manual_paid")]
    public bool ManualPaid { get; set; } = false;

    [Column("manual_paid_note")]
    public string? ManualPaidNote { get; set; }

    [Column("manual_paid_by")]
    public int? ManualPaidBy { get; set; }

    [Column("manual_paid_at")]
    public DateTime? ManualPaidAt { get; set; }
}