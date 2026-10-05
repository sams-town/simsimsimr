namespace RSSMBackend.Models;

/// <summary>
/// DTO for Khanza Journal data from SIMRS MySQL
/// </summary>
public class KhanzaJournalDto
{
    public string? Tanggal { get; set; }
    public string? KodeAkun { get; set; }
    public string? NoJurnal { get; set; }
    public string? NoBukti { get; set; }
    public string? JamJurnal { get; set; }
    public string? NamaAkun { get; set; }
    public string? Keterangan { get; set; }
    public decimal Debet { get; set; }
    public decimal Kredit { get; set; }

    // Backward-compatible alias (previously read from non-existent cara_bayar column)
    // Prefer using NamaPenjab moving forward.
    public string? CaraBayar { get; set; }

    public string? KodePoli { get; set; }
    public string? NamaPoli { get; set; }

    public string? NoRekamMedis { get; set; }
    public string? NamaPasien { get; set; }

    public string? KodeDokter { get; set; }
    public string? NamaDokter { get; set; }

    public string? KodePenjab { get; set; }
    public string? NamaPenjab { get; set; }

    public string? TglMasuk { get; set; }
    public string? TglKeluar { get; set; }

    // Purchasing / Supplier Fields
    public string? NoPo { get; set; }
    public string? TglPenerimaan { get; set; }
    public string? TglPo { get; set; }
    public string? KodeSuplier { get; set; }
    public string? NamaSuplier { get; set; }
    public decimal JmlTransaksi { get; set; }
    public string? NoFaktur { get; set; }
    
    // Bangsal
    public string? KodeBangsal { get; set; }
    public string? NamaBangsal { get; set; }
    public string? StatusLanjutan { get; set; }
}

/// <summary>
/// Request parameters for Khanza Journal sync
/// </summary>
public class KhanzaJournalRequest
{
    public DateTime StartDate { get; set; }
    public DateTime EndDate { get; set; }
}

/// <summary>
/// Response for Khanza Journal sync with pagination
/// </summary>
public class KhanzaJournalResponse
{
    public List<KhanzaJournalDto> Data { get; set; } = new();
    public int TotalRecords { get; set; }
    public DateTime? StartDate { get; set; }
    public DateTime? EndDate { get; set; }
    public decimal TotalDebet { get; set; }
    public decimal TotalKredit { get; set; }
}
