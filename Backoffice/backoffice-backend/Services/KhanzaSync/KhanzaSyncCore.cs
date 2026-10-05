using System.Data.Common;
using System.Globalization;

namespace RSSMBackend.Services.KhanzaSync;

public enum SyncFieldType
{
    Text,
    Number,
    Integer,
    /// <summary>Tanggal saja (yyyy-MM-dd).</summary>
    Date,
    /// <summary>Jam dinding tanpa zona waktu (yyyy-MM-dd HH:mm:ss), mis. kolom timestamp without time zone.</summary>
    DateTime
}

/// <param name="Column">Nama kolom di tabel aplikasi. Selalu dari definisi statis, tidak pernah dari input user.</param>
/// <param name="PgCast">Ekspresi penulisan dengan {0} sebagai placeholder parameter, mis. "{0}::date".</param>
public sealed record SyncFieldDef(string Column, string Label, SyncFieldType Type, string PgCast);

internal static class SyncValue
{
    private static readonly CultureInfo Inv = CultureInfo.InvariantCulture;

    // Pemisah komponen kunci komposit. Karakter kontrol ini tidak mungkin muncul di data teks.
    public const char KeySep = '\u001F';

    public static string KeyOf(params string?[] parts) => string.Join(KeySep, parts.Select(p => p ?? ""));

    public static string[] SplitKey(string key) => (key ?? "").Split(KeySep);

    /// <summary>
    /// Bentuk kanonik sebuah nilai untuk dibandingkan dan ditampilkan. Kedua sisi (aplikasi dan Khanza)
    /// selalu dilewatkan ke sini, jadi perbedaan tipe driver (double vs decimal, DateTime vs date) tidak
    /// dianggap sebagai perubahan.
    /// </summary>
    public static string? Normalize(object? raw, SyncFieldType type)
    {
        if (raw == null || raw is DBNull) return null;

        switch (type)
        {
            case SyncFieldType.Text:
            {
                var s = Convert.ToString(raw, Inv)?.Trim();
                return string.IsNullOrEmpty(s) ? null : s;
            }
            case SyncFieldType.Number:
            {
                if (raw is double d && (double.IsNaN(d) || double.IsInfinity(d))) return null;
                if (!TryDecimal(raw, out var dec)) return null;
                return Math.Round(dec, 2, MidpointRounding.AwayFromZero).ToString("0.##", Inv);
            }
            case SyncFieldType.Integer:
            {
                if (!TryDecimal(raw, out var dec)) return null;
                return Math.Round(dec, 0, MidpointRounding.AwayFromZero).ToString("0", Inv);
            }
            case SyncFieldType.Date:
            {
                var dt = ToDateTime(raw);
                return dt?.ToString("yyyy-MM-dd", Inv);
            }
            case SyncFieldType.DateTime:
            {
                var dt = ToDateTime(raw);
                return dt?.ToString("yyyy-MM-dd HH:mm:ss", Inv);
            }
            default:
                return Convert.ToString(raw, Inv);
        }
    }

    /// <summary>Ubah nilai kanonik kembali menjadi nilai parameter database.</summary>
    public static object ToDbValue(string? normalized, SyncFieldType type)
    {
        if (normalized == null) return DBNull.Value;
        return type switch
        {
            SyncFieldType.Number => decimal.Parse(normalized, Inv),
            SyncFieldType.Integer => long.Parse(normalized, Inv),
            SyncFieldType.Date => System.DateTime.SpecifyKind(
                System.DateTime.ParseExact(normalized, "yyyy-MM-dd", Inv), DateTimeKind.Unspecified),
            SyncFieldType.DateTime => System.DateTime.SpecifyKind(
                System.DateTime.ParseExact(normalized, "yyyy-MM-dd HH:mm:ss", Inv), DateTimeKind.Unspecified),
            _ => normalized
        };
    }

    public static DateTime? ToDateTime(object? raw)
    {
        switch (raw)
        {
            case null:
            case DBNull:
                return null;
            case System.DateTime dt:
                // MySQL "0000-00-00" dibaca sebagai MinValue (Convert Zero Datetime) - anggap kosong.
                return dt.Year < 1900 ? null : dt;
            case DateOnly d:
                return d.ToDateTime(TimeOnly.MinValue);
            case DateTimeOffset dto:
                return dto.UtcDateTime;
            case string s when System.DateTime.TryParse(s, Inv, DateTimeStyles.None, out var parsed):
                return parsed.Year < 1900 ? null : parsed;
            default:
                return null;
        }
    }

    private static bool TryDecimal(object raw, out decimal value)
    {
        try
        {
            value = raw switch
            {
                decimal m => m,
                string s => decimal.Parse(s, NumberStyles.Any, Inv),
                _ => Convert.ToDecimal(raw, Inv)
            };
            return true;
        }
        catch
        {
            value = 0;
            return false;
        }
    }

    /// <summary>Baca seluruh baris reader menjadi kamus kolom -> nilai (DBNull menjadi null).</summary>
    public static async Task<List<Dictionary<string, object?>>> ReadAllAsync(DbDataReader reader)
    {
        var rows = new List<Dictionary<string, object?>>();
        while (await reader.ReadAsync())
        {
            var row = new Dictionary<string, object?>(StringComparer.OrdinalIgnoreCase);
            for (int i = 0; i < reader.FieldCount; i++)
            {
                object? v;
                try
                {
                    v = reader.IsDBNull(i) ? null : reader.GetValue(i);
                }
                catch
                {
                    // Nilai yang tidak bisa dikonversi driver (mis. tanggal rusak di Khanza) diperlakukan kosong.
                    v = null;
                }
                row[reader.GetName(i)] = v;
            }
            rows.Add(row);
        }
        return rows;
    }

    public static object? Get(this Dictionary<string, object?> row, string column)
        => row.TryGetValue(column, out var v) ? v : null;
}
