namespace RSSMBackend.Models;

public class InvoiceRequestModel
{
    public string Number { get; set; }
    public string CustomerNo { get; set; }
    public string? CustomerName { get; set; }
    public string? CustomerPhone { get; set; }
    public string? CustomerEmail { get; set; }
    public string? CustomerAddress { get; set; }
    public string TransDate { get; set; }

    public List<InvoiceDetailModel> DetailItem { get; set; }

    public class InvoiceDetailModel
    {
        public double? ItemNo { get; set; }
        public double UnitPrice { get; set; }
        public double? Quantity { get; set; }
        public double? TotalPrice { get; set; }
        public string? ItemName { get; set; }
        public string DepartmentName { get; set; }
        public string? DoctorID { get; set; }
        public string? doctorName { get; set; }
    }
}



