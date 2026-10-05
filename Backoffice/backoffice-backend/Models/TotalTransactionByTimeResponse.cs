namespace RSSMBackend.Models;
public class TotalTransactionByTimeResponse {
    public DataTotalTransaction? Current { get; set; }
    public DataTotalTransaction? DayBefore { get; set; }
    public DataTotalTransaction? WeekBefore { get; set; }
    public DataTotalTransaction? MonthBefore { get; set; }
}

public class DataTotalTransaction {
    public DateTime Date { get; set; }
    public double? Total { get; set; }
}