namespace RSSMBackend.Entities;

using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

[Table("thb_purchase_order", Schema = "public")]
public class PurchaseOrder
{
    [Key]
    [Column("order_number")]
    public string OrderNumber { get; set; }

    [Column("supplier_code")]
    public string? SupplierCode { get; set; }

    [Column("supplier_name")]
    public string? SupplierName { get; set; }

    [Column("nip")]
    public string? Nip { get; set; }

    [Column("date")]
    public DateTime? Date { get; set; }

    [Column("total")]
    public double? Total { get; set; }

    [Column("discount")]
    public double? Discount { get; set; }

    [Column("grand_total")]
    public double? GrandTotal { get; set; }

    [Column("ppn")]
    public double? Ppn { get; set; }

    [Column("stamp_duty")]
    public double? StampDuty { get; set; }

    [Column("invoice_amount")]
    public double? InvoiceAmount { get; set; }

    [Column("status")]
    public string? Status { get; set; }

    [Column("source")]
    public string? Source { get; set; }

    [Column("down_payment")]
    public double? DownPayment { get; set; } = 0;

    [Column("down_payment_history", TypeName = "jsonb")]
    public string? DownPaymentHistory { get; set; }

    [Column("created_date")]
    public DateTime? CreatedDate { get; set; }

    [Column("updated_date")]
    public DateTime? UpdatedDate { get; set; }

    [Column("updated_by")]
    public int? UpdatedBy { get; set; }

    // Manual "Mark as Paid" -- for POs paid through an untrackable channel
    // (outside account-payable/pembayaran). Once set, the PO is hidden from the
    // list and any export. See PurchaseOrderService.GetList / MarkAsPaid.
    [Column("manual_paid")]
    public bool ManualPaid { get; set; } = false;

    [Column("manual_paid_note")]
    public string? ManualPaidNote { get; set; }

    [Column("manual_paid_by")]
    public int? ManualPaidBy { get; set; }

    [Column("manual_paid_at")]
    public DateTime? ManualPaidAt { get; set; }
}
