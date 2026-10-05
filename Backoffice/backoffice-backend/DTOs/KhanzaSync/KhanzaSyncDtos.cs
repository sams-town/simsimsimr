namespace RSSMBackend.DTOs.KhanzaSync;

public class SyncFieldMetaDto
{
    public string Column { get; set; } = "";
    public string Label { get; set; } = "";
    public string Type { get; set; } = "";
}

public class SyncModuleDto
{
    public string Code { get; set; } = "";
    public string Label { get; set; } = "";
    public string KeyLabel { get; set; } = "";
    public string LockRule { get; set; } = "";
    public List<SyncFieldMetaDto> Fields { get; set; } = new();
}

public class SyncListItemDto
{
    /// <summary>Kunci record, opaque bagi frontend - dikirim balik apa adanya saat compare/apply.</summary>
    public string Key { get; set; } = "";
    public string Title { get; set; } = "";
    public string? Subtitle { get; set; }
    public string? Info { get; set; }
    public double? Amount { get; set; }
    public DateTime? Date { get; set; }
    public bool Editable { get; set; }
    public string? LockReason { get; set; }

    /// <summary>Jumlah baris aplikasi di balik record ini. Lebih dari 1 berarti ada duplikat.</summary>
    public int RowCount { get; set; } = 1;

    /// <summary>Baris-baris duplikat punya nilai yang tidak seragam.</summary>
    public bool ValuesDiffer { get; set; }
}

public class SyncCompareRequestDto
{
    public List<string> Keys { get; set; } = new();
}

public class SyncCompareFieldDto
{
    public string Column { get; set; } = "";
    public string Label { get; set; } = "";
    public string Type { get; set; } = "";
    public string? AppValue { get; set; }
    public string? KhanzaValue { get; set; }
    public bool Equal { get; set; }
}

public class SyncCompareRecordDto
{
    public string Key { get; set; } = "";
    public string Title { get; set; } = "";
    public string? Subtitle { get; set; }
    public bool Editable { get; set; }
    public string? LockReason { get; set; }
    public bool FoundInApp { get; set; }
    public bool FoundInKhanza { get; set; }

    /// <summary>Keterangan tambahan, mis. kunci yang berubah di Khanza sehingga tidak bisa dicocokkan.</summary>
    public string? Note { get; set; }
    public int RowCount { get; set; } = 1;
    public bool AppValuesDiffer { get; set; }
    public int ChangedCount { get; set; }
    public List<SyncCompareFieldDto> Fields { get; set; } = new();
}

public class SyncApplyItemDto
{
    public string Key { get; set; } = "";
    public List<string> Fields { get; set; } = new();

    /// <summary>
    /// Nilai aplikasi yang dilihat user saat membandingkan. Kalau data di aplikasi sudah berubah
    /// sejak itu, penerapan record ini ditolak supaya perubahan orang lain tidak tertimpa.
    /// </summary>
    public Dictionary<string, string?> ExpectedAppValues { get; set; } = new();
}

public class SyncApplyRequestDto
{
    public List<SyncApplyItemDto> Items { get; set; } = new();
}

public class SyncApplyResultDto
{
    public string Key { get; set; } = "";
    public bool Success { get; set; }
    public string Message { get; set; } = "";
    public int UpdatedRows { get; set; }
    public List<string> AppliedFields { get; set; } = new();
}
