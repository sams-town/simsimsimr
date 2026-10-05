public class GeneralLedgerResponse
{
    public string CoaCode { get; set; }
    public string CoaName { get; set; }
    public List<JournalForLedger> Journals { get; set; } = new();
    
    public class JournalForLedger
    {
        public int JournalId { get; set; }
        public DateTime? CreatedDate { get; set; }
        public string? JournalDesc { get; set; }
        public double? Debit { get; set; }
        public double? Credit { get; set; }
        public double? CurrentBalance { get; set; }
        public double? PrevBalance { get; set; }
        public string? Source { get; set; }
    }
}
