namespace RSSMBackend.Models;
public class AgingScheduleResponse {
    public string? PatientName {get; set;}
    public string? InsuranceName {get; set;}
    public DateTime? BillingDate {get; set;}
    public double? ZeroToFortyFive {get; set;}
    public double? FortyFiveToSixty {get; set;}
    public double? SixtyToNinety {get; set;}
    public double? NinetyToOneTwenty {get; set;}
    public double? GreaterThanOneTwenty {get; set;}
    public string? Status {get;set;}
}