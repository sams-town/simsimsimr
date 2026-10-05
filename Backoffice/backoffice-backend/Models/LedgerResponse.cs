public class LedgerResponse
{
    public int CoaId { get; set; }
    public string CoaCode { get; set; }
    public string CoaName { get; set; }
    public double? OpeningBalance { get; set; }
    public double? ClosingBalance { get; set; }
    public int TotalTransactions { get; set; }
    public List<LedgerTransaction> Transactions { get; set; } = new();

    public class LedgerTransaction
    {
        public DateTime? Date { get; set; }
        public string TransactionNo { get; set; }
        public string JournalNo { get; set; } // Added
        public string JournalRef { get; set; }
        public string TransactionId { get; set; }
        public string Description { get; set; }
        public double? Debit { get; set; }
        public double? Credit { get; set; }
        public double RunningBalance { get; set; }
    }
}

