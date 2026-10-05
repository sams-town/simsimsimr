namespace RSSMBackend.Models;

public class SalesReceiveRequestModel
{
    public string bankNo { get; set; }
    public string bankName { get; set; }
    public double? chequeAmount { get; set; }
    public string customerNo { get; set; }
    public string TransDate { get; set; }
    public List<SalesReceiveInvoiceDetailModel> detailInvoice { get; set; }

    public class SalesReceiveInvoiceDetailModel
    {
        public string invoiceNo { get; set; }
        public double paymentAmount { get; set; }
    }
}



