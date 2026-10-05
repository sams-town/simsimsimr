namespace RSSMBackend.Models;

public class BillingRequestModel
{
    public string? id { get; set; }
    public string? institute_branch_id { get; set; }
    public string? institute_branch_name { get; set; }
    public string? patient_ref_code { get; set; }
    public string? patient_name { get; set; }
    public string? cashier_id { get; set; }
    public CashierModel? cashier { get; set; }
    public List<PaymentModel>? payment { get; set; }
    public List<TransactionModel>? transactions { get; set; }

    public string? payment_id { get; set; }
    public DateTime? date_added { get; set; }
    public double? var_1 { get; set; }
    public double? var_2 { get; set; }
    public double? var_3 { get; set; }

    public class CashierModel
    {
        public string? cashier_name { get; set; }
        public string? shift { get; set; }
    }

    public class PrescriptionModel
    {
        public string? prescription_id { get; set; }
        public string? catalog_id { get; set; }
        public string? catalog_name { get; set; }
        public string? notes { get; set; }
        public DateTime? date_added { get; set; }
        public double? var_1 { get; set; }
        public double? var_2 { get; set; }
        public double? var_3 { get; set; }
        public string? coa_code { get; set; }
    }

    public class TransactionModel
    {
        public string transaction_id { get; set; }
        public int? flag_status_id { get; set; }
        public string? flag_status_name { get; set; }
        public int? type_transaction_id { get; set; }
        public string? type_transaction_name { get; set; }
        public string? costing_id { get; set; }
        public string? costing_name { get; set; }
        public string? prescription_id { get; set; }
        public List<PrescriptionModel>? prescription { get; set; }
        public string? code { get; set; }
        public string? notes { get; set; }
        public DateTime? date_added { get; set; }
        public double? var_1 { get; set; }
        public double? var_2 { get; set; }
        public double? var_3 { get; set; }
        public string? coa_code { get; set; }
    }

    public class PaymentModel
    {
        public string? payment_id { get; set; }
        public string? payment_method_id { get; set; }

        public string? payment_method_name { get; set; }
        public PaymentDetailModel? payment_method_detail { get; set; }
        public string? insurance_ref_code { get; set; }
        public string? insurance_name { get; set; }
        public double? var_1 { get; set; }
        public double? var_2 { get; set; }
        public double? var_3 { get; set; }
        public string? coa_code { get; set; }
    }

    public class PaymentDetailModel
    {
        public string? id { get; set; }
        public string? payment_method_id { get; set; }
        public string? payment_method_name { get; set; }
        public string? name { get; set; }
        public string? CARD_NUMBER { get; set; }
        public string? AUTHORIZATION_NUMBER { get; set; }
        public string? BATCH_NUMBER { get; set; }
        public double? FEE { get; set; }
        public string? SEP { get; set; }
        public string? INA_CBG { get; set; }
        public string? INA_CBG_NAME { get; set; }
        public double? TOTAL { get; set; }


    }


}



