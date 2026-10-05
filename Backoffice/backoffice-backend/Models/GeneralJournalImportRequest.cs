namespace RSSMBackend.Models;

public class GeneralJournalImportRequest
{
    public string? TransactionNo { get; set; }
    public DateTime TransactionDate { get; set; }
    public string Description { get; set; } = string.Empty;
    public List<JournalLineItem> Lines { get; set; } = new();
    
    public class JournalLineItem
    {
        public string CoaCode { get; set; } = string.Empty;
        public string Description { get; set; } = string.Empty;
        public double Debit { get; set; }
        public double Credit { get; set; }
    }
}

public class ImportValidationError
{
    public int Row { get; set; }
    public string Field { get; set; } = string.Empty;
    public string Message { get; set; } = string.Empty;
}

public class ImportResult
{
    public bool Success { get; set; }
    public string? TransactionId { get; set; }
    public string? TransactionNo { get; set; }
    public string? Message { get; set; }
    public List<ImportValidationError> Errors { get; set; } = new();
    public List<object>? CreatedTransactions { get; set; }

    /// <summary>Data rows read from the sheet. Surfaced in the import-job history summary.</summary>
    public int TotalRows { get; set; }
}

public class ImportJournalLine
{
    public int Row { get; set; }
    public string TransactionNo { get; set; } = string.Empty;
    public DateTime TransactionDate { get; set; }
    public string CoaCode { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public double Debit { get; set; }
    public double Credit { get; set; }
    // THB-specific fields
    public string? JournalRef { get; set; }
    public string? VourcherId { get; set; }
    public string? SupplierName { get; set; }
    public string? PayerName { get; set; }
    public string? PONumber { get; set; }

    /// <summary>
    /// Cost center NAME as written in the sheet. Resolved to an id (creating the master row if
    /// needed) inside the import transaction -- not during parsing, so a rejected import leaves
    /// no master data behind.
    /// </summary>
    public string? CostCenterName { get; set; }

    public int? CostcenterId { get; set; }
    public int? ContactId { get; set; }
}
