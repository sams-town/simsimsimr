namespace RSSMBackend.Entities;

public class JobJournalRequest
{
    public DateTime? JournalDate { get; set; }
}

public class JobJournalResult
{
    public DateTime? Tanggal { get; set; }
    public string? KodeAkun { get; set; }
    public string? NoJurnal { get; set; }
    public string? NoBukti { get; set; }
    public string? NamaAkun { get; set; }
    public string? Keterangan { get; set; }
    public float? Debet { get; set; }
    public float? Kredit { get; set; }
    public string? CaraBayar { get; set; }
    public string? NamaPoli  { get; set; }
    public string? NoPO  { get; set; }
    public DateTime? TglPenerimaan { get; set; }
    public DateTime? TglPO { get; set; }
    public string? NamaSuplier { get; set; }
    public double? JmlTransaksi { get; set; }
}

public class JobTransactionResult
{
    public string? NoJurnal { get; set; }
    public DateTime? Tanggal { get; set; }
    public string? Keterangan { get; set; }
}