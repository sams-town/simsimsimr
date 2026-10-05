namespace RSSMBackend.Helpers;

using RSSMBackend.Models;

/// <summary>
/// Whole-rupiah rounding for Account Payable output (Excel/CSV exports and printed docs).
///
/// Presentation only -- stored and posted amounts keep their 2 decimals. The rule is: run
/// every calculation at full precision and round only the final figure that is written out.
/// Never round before summing, or the error compounds across each line.
///
/// Rounds half away from zero (2.5 -> 3, -2.5 -> -3), matching the accounting convention and
/// the frontend's FormatNumberRounded. .NET's default Math.Round is banker's rounding
/// (2.5 -> 2), which would disagree with what the UI shows for exact-half amounts.
/// </summary>
public static class MoneyRounding
{
    public static double Round(double value) => Math.Round(value, MidpointRounding.AwayFromZero);

    public static double Round(double? value) => Round(value ?? 0);

    public static decimal Round(decimal value) => Math.Round(value, MidpointRounding.AwayFromZero);

    public static decimal Round(decimal? value) => Round(value ?? 0);

    /// <summary>Excel number format for a rounded rupiah amount.</summary>
    public const string ExcelFormat = "#,##0";

    /// <summary>
    /// Rounds the money fields in place, for export paths that hand the list to a generic
    /// dumper (ConvertToCsv / ConvertToExcel) which cannot round per cell itself.
    ///
    /// Safe to mutate: these are plain DTOs built per-request for the export, never EF-tracked
    /// entities, so nothing here can reach the database. Neither dumper emits a total row, so
    /// rounding each row independently introduces no summing error.
    /// </summary>
    public static void RoundForExport(List<AccountPayableResponse> data)
    {
        foreach (var item in data)
        {
            item.TotalTransaction = Round(item.TotalTransaction);
            item.PaidAmount = Round(item.PaidAmount);
            item.OutstandingAmount = Round(item.OutstandingAmount);
        }
    }

    /// <inheritdoc cref="RoundForExport(List{AccountPayableResponse})"/>
    public static void RoundForExport(List<DataInvoiceExchangeExportResponse> data)
    {
        foreach (var item in data)
        {
            item.TotalTransaction = Round(item.TotalTransaction);
        }
    }

    /// <inheritdoc cref="RoundForExport(List{AccountPayableResponse})"/>
    public static void RoundForExport(List<AccountPayableAgingResponse> data)
    {
        foreach (var item in data)
        {
            item.Current = Round(item.Current);
            item.Days1To30 = Round(item.Days1To30);
            item.Days31To60 = Round(item.Days31To60);
            item.Days61To90 = Round(item.Days61To90);
            item.Over90Days = Round(item.Over90Days);
            item.Total = Round(item.Total);
        }
    }
}
