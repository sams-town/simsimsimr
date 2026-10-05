namespace RSSMBackend.Helpers;

using CsvHelper;
using OfficeOpenXml;
using System.Globalization;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Text.Json;
using System.Threading.Tasks;
using System.Reflection;

using RSSMBackend.Models;
using RSSMBackend.Entities;
using RSSMBackend.DTOs.Journal;
using OfficeOpenXml.Style;
using System.Text;
using System.Drawing;
using AngleSharp.Dom;

public class ExportToDocs
{
    public static byte[] ConvertBillingToCsv(List<AgingScheduleResponse> data, string status)
    {
        var sb = new StringBuilder();

        var totalSum = data.Sum(item => item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty);
        var zeroToFortyFiveSum = data.Sum(item => item.ZeroToFortyFive);
        var fortyFiveToSixtySum = data.Sum(item => item.FortyFiveToSixty);
        var sixtyToNinetySum = data.Sum(item => item.SixtyToNinety);
        var ninetyToOneTwentySum = data.Sum(item => item.NinetyToOneTwenty);
        var greaterThanOneTwentySum = data.Sum(item => item.GreaterThanOneTwenty);
        double total = 0.0;

        switch (status.ToLower())
        {
            case "patient":
                sb.AppendLine("Name,Insurance,Billing Date,Total,Period (Days),,,,");
                sb.AppendLine(",,,,0-45,45-60,60-90,90-120,> 120");

                foreach (var item in data) {
                    var subTotal = item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty;
                    sb.AppendLine($"\"{item.PatientName}\",\"{item.InsuranceName}\",\"{item.BillingDate:dd/MM/yyyy}\",\"{subTotal}\",\"{item.ZeroToFortyFive}\",\"{item.FortyFiveToSixty}\",\"{item.SixtyToNinety}\",\"{item.NinetyToOneTwenty}\",\"{item.GreaterThanOneTwenty}\"");
                    total += (double)subTotal;
                }

                sb.AppendLine($"\"Total\",,,\"{total}\",\"{zeroToFortyFiveSum}\",\"{fortyFiveToSixtySum}\",\"{sixtyToNinetySum}\",\"{ninetyToOneTwentySum}\",\"{greaterThanOneTwentySum}\"");
                break;
            case "insurance":
                sb.AppendLine("Insurance,Total,Period (Days),,,,");
                sb.AppendLine(",,0-45,45-60,60-90,90-120,> 120");

                foreach (var item in data)
                {
                    var subTotal = item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty;
                    sb.AppendLine($"\"{item.InsuranceName}\",\"{subTotal}\",\"{item.ZeroToFortyFive}\",\"{item.FortyFiveToSixty}\",\"{item.SixtyToNinety}\",\"{item.NinetyToOneTwenty}\",\"{item.GreaterThanOneTwenty}\"");
                    total += (double)subTotal;
                }

                sb.AppendLine($"\"Total\",\"{total}\",\"{zeroToFortyFiveSum}\",\"{fortyFiveToSixtySum}\",\"{sixtyToNinetySum}\",\"{ninetyToOneTwentySum}\",\"{greaterThanOneTwentySum}\"");
                break;
            default:
                sb.AppendLine("Name,Insurance,Billing Date,Total,Period (Days),,,,");
                sb.AppendLine(",,,,0-45,45-60,60-90,90-120,> 120");

                foreach (var item in data)
                {
                    var subTotal = item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty;
                    sb.AppendLine($"\"{item.PatientName}\",\"{item.InsuranceName}\",\"{item.BillingDate:dd/MM/yyyy}\",\"{subTotal}\",\"{item.ZeroToFortyFive}\",\"{item.FortyFiveToSixty}\",\"{item.SixtyToNinety}\",\"{item.NinetyToOneTwenty}\",\"{item.GreaterThanOneTwenty}\"");
                    total += (double)subTotal;
                }

                sb.AppendLine($"\"Total\",,,\"{total}\",\"{zeroToFortyFiveSum}\",\"{fortyFiveToSixtySum}\",\"{sixtyToNinetySum}\",\"{ninetyToOneTwentySum}\",\"{greaterThanOneTwentySum}\"");
                break;
        }


        return Encoding.UTF8.GetBytes(sb.ToString());
    }

    public static byte[] ConvertPaymentToCsv(List<AgingScheduleResponse> data, string status) {
        var sb = new StringBuilder();

        switch (status.ToLower())
        {
            case "patient":
                sb.AppendLine("Name,Insurance,Billing Date,Due Date,Status,Amount");
                foreach (var item in data)
                {
                    var dueDate = item.Status.ToLower() == "past due" ? "Invalid Date" : "Valid Date";
                    var line = $"{item.PatientName},{item.InsuranceName},{item.BillingDate:dd/MM/yyyy},{dueDate},{item.Status},{item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty}";
                    sb.AppendLine(line);
                }
                break;

            case "insurance":
                sb.AppendLine("Insurance,Billing Date,Due Date,Status,Amount");
                foreach (var item in data)
                {
                    var dueDate = item.Status.ToLower() == "past due" ? "Invalid Date" : "Valid Date";
                    var line = $"{item.InsuranceName},{item.BillingDate:dd/MM/yyyy},{dueDate},{item.Status},{item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty}";
                    sb.AppendLine(line);
                }
                break;

            default:
                sb.AppendLine("Name,Insurance,Billing Date,Due Date,Status,Amount");
                foreach (var item in data)
                {
                    var dueDate = item.Status.ToLower() == "past due" ? "Invalid Date" : "Valid Date";
                    var line = $"{item.PatientName},{item.InsuranceName},{item.BillingDate:dd/MM/yyyy},{dueDate},{item.Status},{item.ZeroToFortyFive + item.FortyFiveToSixty + item.SixtyToNinety + item.NinetyToOneTwenty + item.GreaterThanOneTwenty}";
                    sb.AppendLine(line);
                }
                break;
        }

        var totalAmount = data.Sum(d => d.ZeroToFortyFive + d.FortyFiveToSixty + d.SixtyToNinety + d.NinetyToOneTwenty + d.GreaterThanOneTwenty);
        if (status.ToLower() == "patient")
        {
            sb.AppendLine($"Total,,,,,{totalAmount}");
        }
        else if (status.ToLower() == "insurance")
        {
            sb.AppendLine($"Total,,,,{totalAmount}");
        }

        return Encoding.UTF8.GetBytes(sb.ToString());
    }

    public static byte[] ConvertToCsv<T>(List<T> data)
    {
        using var memoryStream = new MemoryStream();
        using var writer = new StreamWriter(memoryStream);
        using var csv = new CsvWriter(writer, CultureInfo.InvariantCulture);

        csv.WriteRecords(data);
        writer.Flush();

        return memoryStream.ToArray();
    }

    public static byte[] ConvertBillingToExcel(List<AgingScheduleResponse> data, string status)
    {
        using var stream = new MemoryStream();
        using (var package = new ExcelPackage(stream))
        {
            var worksheet = package.Workbook.Worksheets.Add("AgingSchedulePantient");

            switch (status.ToLower())
            {
                case "patient":
                    worksheet.Cells["A1"].Value = "Name";
                    worksheet.Cells["B1"].Value = "Insurance";
                    worksheet.Cells["C1"].Value = "Billing Date";
                    worksheet.Cells["D1"].Value = "Total";

                    worksheet.Cells["E1"].Value = "Period (Days)";
                    worksheet.Cells["E2"].Value = "0-45";
                    worksheet.Cells["F2"].Value = "45-60";
                    worksheet.Cells["G2"].Value = "60-90";
                    worksheet.Cells["H2"].Value = "90-120";
                    worksheet.Cells["I2"].Value = "> 120";


                    worksheet.Cells["E1:I1"].Merge = true;
                    worksheet.Cells["E1:I1"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

                    worksheet.Cells["A1:A2"].Merge = true;
                    worksheet.Cells["A1:A2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["B1:B2"].Merge = true;
                    worksheet.Cells["B1:B2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["C1:C2"].Merge = true;
                    worksheet.Cells["C1:C2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["D1:D2"].Merge = true;
                    worksheet.Cells["D1:D2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    using (var range = worksheet.Cells["A1:I2"])
                    {
                        range.Style.Font.Bold = true;
                        range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                        range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
                    }

                    for (int i = 0; i < data.Count; i++)
                    {
                        worksheet.Cells[i + 3, 1].Value = data[i].PatientName;
                        worksheet.Cells[i + 3, 2].Value = data[i].InsuranceName;
                        worksheet.Cells[i + 3, 3].Value = data[i].BillingDate;
                        worksheet.Cells[i + 3, 3].Style.Numberformat.Format = "dd/MM/yyyy";
                        worksheet.Cells[i + 3, 4].Value = data[i].ZeroToFortyFive + data[i].FortyFiveToSixty + data[i].SixtyToNinety + data[i].NinetyToOneTwenty + data[i].GreaterThanOneTwenty;
                        worksheet.Cells[i + 3, 5].Value = data[i].ZeroToFortyFive;
                        worksheet.Cells[i + 3, 6].Value = data[i].FortyFiveToSixty;
                        worksheet.Cells[i + 3, 7].Value = data[i].SixtyToNinety;
                        worksheet.Cells[i + 3, 8].Value = data[i].NinetyToOneTwenty;
                        worksheet.Cells[i + 3, 9].Value = data[i].GreaterThanOneTwenty;
                    }

                    worksheet.Cells[data.Count + 3, 1].Value = "Total";
                    worksheet.Cells[data.Count + 3, 4].Formula = $"SUM(D3:D{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 5].Formula = $"SUM(E3:E{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 6].Formula = $"SUM(F3:F{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 7].Formula = $"SUM(G3:G{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 8].Formula = $"SUM(H3:H{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 9].Formula = $"SUM(I3:I{data.Count + 2})";
                    break;

                case "insurance":
                    worksheet.Cells["A1"].Value = "Insurance";
                    worksheet.Cells["B1"].Value = "Amount";
                    worksheet.Cells["C1"].Value = "Period (Days)";
                    worksheet.Cells["C2"].Value = "0-45";
                    worksheet.Cells["D2"].Value = "45-60";
                    worksheet.Cells["E2"].Value = "60-90";
                    worksheet.Cells["F2"].Value = "90-120";
                    worksheet.Cells["G2"].Value = "> 120";

                    worksheet.Cells["A1:A2"].Merge = true;
                    worksheet.Cells["A1:A2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["B1:B2"].Merge = true;
                    worksheet.Cells["B1:B2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["C1:G1"].Merge = true;
                    worksheet.Cells["C1:G1"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

                    using (var range = worksheet.Cells["A1:G2"])
                    {
                        range.Style.Font.Bold = true;
                        range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                        range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
                    }

                    for (int i = 0; i < data.Count; i++)
                    {
                        worksheet.Cells[i + 3, 1].Value = data[i].InsuranceName;
                        worksheet.Cells[i + 3, 2].Value = data[i].ZeroToFortyFive + data[i].FortyFiveToSixty + data[i].SixtyToNinety + data[i].NinetyToOneTwenty + data[i].GreaterThanOneTwenty;
                        worksheet.Cells[i + 3, 3].Value = data[i].ZeroToFortyFive;
                        worksheet.Cells[i + 3, 4].Value = data[i].FortyFiveToSixty;
                        worksheet.Cells[i + 3, 5].Value = data[i].SixtyToNinety;
                        worksheet.Cells[i + 3, 6].Value = data[i].NinetyToOneTwenty;
                        worksheet.Cells[i + 3, 7].Value = data[i].GreaterThanOneTwenty;
                    }

                    worksheet.Cells[data.Count + 3, 1].Value = "Total";
                    worksheet.Cells[data.Count + 3, 2].Formula = $"SUM(B3:B{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 3].Formula = $"SUM(C3:C{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 4].Formula = $"SUM(D3:D{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 5].Formula = $"SUM(E3:E{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 6].Formula = $"SUM(F3:F{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 7].Formula = $"SUM(G3:G{data.Count + 2})";
                    break;

                default:
                    worksheet.Cells["A1"].Value = "Name";
                    worksheet.Cells["B1"].Value = "Insurance";
                    worksheet.Cells["C1"].Value = "Billing Date";
                    worksheet.Cells["D1"].Value = "Total";

                    worksheet.Cells["E1"].Value = "Period (Days)";
                    worksheet.Cells["E2"].Value = "0-45";
                    worksheet.Cells["F2"].Value = "45-60";
                    worksheet.Cells["G2"].Value = "60-90";
                    worksheet.Cells["H2"].Value = "90-120";
                    worksheet.Cells["I2"].Value = "> 120";
                    worksheet.Cells["J1"].Value = "Status";


                    worksheet.Cells["E1:I1"].Merge = true;
                    worksheet.Cells["E1:I1"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

                    worksheet.Cells["A1:A2"].Merge = true;
                    worksheet.Cells["A1:A2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["B1:B2"].Merge = true;
                    worksheet.Cells["B1:B2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["C1:C2"].Merge = true;
                    worksheet.Cells["C1:C2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["D1:D2"].Merge = true;
                    worksheet.Cells["D1:D2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    worksheet.Cells["J1:J2"].Merge = true;
                    worksheet.Cells["J1:J2"].Style.VerticalAlignment = ExcelVerticalAlignment.Center;

                    using (var range = worksheet.Cells["A1:I2"])
                    {
                        range.Style.Font.Bold = true;
                        range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                        range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
                    }

                    for (int i = 0; i < data.Count; i++)
                    {
                        worksheet.Cells[i + 3, 1].Value = data[i].PatientName;
                        worksheet.Cells[i + 3, 2].Value = data[i].InsuranceName;
                        worksheet.Cells[i + 3, 3].Value = data[i].BillingDate;
                        worksheet.Cells[i + 3, 3].Style.Numberformat.Format = "dd/MM/yyyy";
                        worksheet.Cells[i + 3, 4].Value = data[i].ZeroToFortyFive + data[i].FortyFiveToSixty + data[i].SixtyToNinety + data[i].NinetyToOneTwenty + data[i].GreaterThanOneTwenty;
                        worksheet.Cells[i + 3, 5].Value = data[i].ZeroToFortyFive;
                        worksheet.Cells[i + 3, 6].Value = data[i].FortyFiveToSixty;
                        worksheet.Cells[i + 3, 7].Value = data[i].SixtyToNinety;
                        worksheet.Cells[i + 3, 8].Value = data[i].NinetyToOneTwenty;
                        worksheet.Cells[i + 3, 9].Value = data[i].GreaterThanOneTwenty;
                    }

                    worksheet.Cells[data.Count + 3, 1].Value = "Total";
                    worksheet.Cells[data.Count + 3, 4].Formula = $"SUM(D3:D{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 5].Formula = $"SUM(E3:E{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 6].Formula = $"SUM(F3:F{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 7].Formula = $"SUM(G3:G{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 8].Formula = $"SUM(H3:H{data.Count + 2})";
                    worksheet.Cells[data.Count + 3, 9].Formula = $"SUM(I3:I{data.Count + 2})";
                    break;
            }
            worksheet.Cells[worksheet.Dimension.Address].AutoFitColumns();

            package.Save();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertPaymentToExcel(List<AgingScheduleResponse> data, string status) {
        using var stream = new MemoryStream();
        using (var package = new ExcelPackage(stream)) {
            var worksheet = package.Workbook.Worksheets.Add("PaymentPeriode");

            switch (status.ToLower()) {
                case "patient":
                    worksheet.Cells["A1"].Value = "Name";
                    worksheet.Cells["B1"].Value = "Insurance";
                    worksheet.Cells["C1"].Value = "Billing Date";
                    worksheet.Cells["D1"].Value = "Due Date";
                    worksheet.Cells["E1"].Value = "Status";
                    worksheet.Cells["F1"].Value = "Amount";

                    using (var range = worksheet.Cells["A1:F1"]) {
                        range.Style.Font.Bold = true;
                        range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                        range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
                    }

                    for (int i = 0; i < data.Count; i++) {
                        worksheet.Cells[i + 2, 1].Value = data[i].PatientName;
                        worksheet.Cells[i + 2, 2].Value = data[i].InsuranceName;
                        worksheet.Cells[i + 2, 3].Value = data[i].BillingDate;
                        worksheet.Cells[i + 2, 3].Style.Numberformat.Format = "dd/MM/yyyy";
                        worksheet.Cells[i + 2, 4].Value = data[i].Status.ToLower() == "past due" ? "Invalid Date" : "Valid Date";
                        worksheet.Cells[i + 2, 5].Value = data[i].Status;

                        if (data[i].Status.ToLower() == "past due")
                            worksheet.Cells[i + 2, 5].Style.Font.Color.SetColor(Color.Red);

                        worksheet.Cells[i + 2, 6].Value = data[i].ZeroToFortyFive + data[i].FortyFiveToSixty + data[i].SixtyToNinety + data[i].NinetyToOneTwenty + data[i].GreaterThanOneTwenty;
                    }

                    worksheet.Cells[data.Count + 2, 1].Value = "Total";
                    worksheet.Cells[data.Count + 2, 1, data.Count + 2, 5].Merge = true;
                    worksheet.Cells[data.Count + 2, 1, data.Count + 2, 5].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

                    worksheet.Cells[data.Count + 2, 6].Formula = $"SUM(F2:F{data.Count + 1})";
                    break;

                case "insurance":
                    worksheet.Cells["A1"].Value = "Insurance";
                    worksheet.Cells["B1"].Value = "Billing Date";
                    worksheet.Cells["C1"].Value = "Due Date";
                    worksheet.Cells["D1"].Value = "Status";
                    worksheet.Cells["E1"].Value = "Amount";

                    using (var range = worksheet.Cells["A1:E1"]) {
                        range.Style.Font.Bold = true;
                        range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                        range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
                    }

                    for (int i = 0; i < data.Count; i++) {
                        worksheet.Cells[i + 2, 1].Value = data[i].InsuranceName;
                        worksheet.Cells[i + 2, 2].Value = data[i].BillingDate;
                        worksheet.Cells[i + 2, 2].Style.Numberformat.Format = "dd/MM/yyyy";
                        worksheet.Cells[i + 2, 3].Value = data[i].Status.ToLower() == "past due" ? "Invalid Date" : "Valid Date";
                        worksheet.Cells[i + 2, 4].Value = data[i].Status;

                        if (data[i].Status.ToLower() == "past due")
                            worksheet.Cells[i + 2, 4].Style.Font.Color.SetColor(Color.Red);

                        worksheet.Cells[i + 2, 5].Value = data[i].ZeroToFortyFive + data[i].FortyFiveToSixty + data[i].SixtyToNinety + data[i].NinetyToOneTwenty + data[i].GreaterThanOneTwenty;
                    }

                    worksheet.Cells[data.Count + 2, 1].Value = "Total";
                    worksheet.Cells[data.Count + 2, 1, data.Count + 2, 4].Merge = true;
                    worksheet.Cells[data.Count + 2, 1, data.Count + 2, 4].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

                    worksheet.Cells[data.Count + 2, 5].Formula = $"SUM(E2:E{data.Count + 1})";
                    break;

                default:
                    worksheet.Cells["A1"].Value = "Name";
                    worksheet.Cells["B1"].Value = "Insurance";
                    worksheet.Cells["C1"].Value = "Billing Date";
                    worksheet.Cells["D1"].Value = "Due Date";
                    worksheet.Cells["E1"].Value = "Status";
                    worksheet.Cells["F1"].Value = "Amount";

                    using (var range = worksheet.Cells["A1:F1"]) {
                        range.Style.Font.Bold = true;
                        range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                        range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
                    }

                    for (int i = 0; i < data.Count; i++) {
                        worksheet.Cells[i + 2, 1].Value = data[i].PatientName;
                        worksheet.Cells[i + 2, 2].Value = data[i].InsuranceName;
                        worksheet.Cells[i + 2, 3].Value = data[i].BillingDate;
                        worksheet.Cells[i + 2, 3].Style.Numberformat.Format = "dd/MM/yyyy";
                        worksheet.Cells[i + 2, 4].Value = data[i].Status.ToLower() == "past due" ? "Valid Date" : "Invalid Date";
                        worksheet.Cells[i + 2, 5].Value = data[i].Status;

                        if (data[i].Status.ToLower() == "past due")
                            worksheet.Cells[i + 2, 5].Style.Font.Color.SetColor(Color.Red);

                        worksheet.Cells[i + 2, 6].Value = data[i].ZeroToFortyFive + data[i].FortyFiveToSixty + data[i].SixtyToNinety + data[i].NinetyToOneTwenty + data[i].GreaterThanOneTwenty;
                    }

                    worksheet.Cells[data.Count + 2, 1].Value = "Total";
                    worksheet.Cells[data.Count + 2, 1, data.Count + 2, 5].Merge = true;
                    worksheet.Cells[data.Count + 2, 1, data.Count + 2, 5].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

                    worksheet.Cells[data.Count + 2, 6].Formula = $"SUM(F2:F{data.Count + 1})";
                    break;
            }

            worksheet.Cells[worksheet.Dimension.Address].AutoFitColumns();
            package.Save();
            return package.GetAsByteArray();
        }
        }
    public static byte[] ConvertBukuKasToExcel(List<Coa> data)
    {
        using var stream = new MemoryStream();
        using (var package = new ExcelPackage(stream))
        {
            var worksheet = package.Workbook.Worksheets.Add("Buku Kas");

            // Headers
            worksheet.Cells["A1"].Value = "KODE AKUN";
            worksheet.Cells["B1"].Value = "TIPE";
            worksheet.Cells["C1"].Value = "NAMA AKUN";
            worksheet.Cells["D1"].Value = "SALDO";

            using (var range = worksheet.Cells["A1:D1"])
            {
                range.Style.Font.Bold = true;
                range.Style.Fill.PatternType = ExcelFillStyle.Solid;
                range.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                range.Style.Font.Color.SetColor(Color.White);
                range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
            }

            for (int i = 0; i < data.Count; i++)
            {
                var row = i + 2;
                var item = data[i];

                worksheet.Cells[row, 1].Value = item.CoaCode;
                worksheet.Cells[row, 2].Value = item.AccountTypeName;
                
                // Indentation for Name based on Level (Frontend logic: (Level-4)*0.5rem -> we can just add spaces)
                string prefix = "";
                if(item.CoaLevel > 4) {
                   prefix = new string(' ', ((item.CoaLevel ?? 0) - 4) * 4); 
                }
                worksheet.Cells[row, 3].Value = prefix + item.CoaName;
                
                if (item.AccountTypeId == 1)
                {
                    worksheet.Cells[row, 4].Value = "-";
                    worksheet.Cells[row, 4].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                }
                else
                {
                    worksheet.Cells[row, 4].Value = item.Balance;
                    worksheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";
                }
            }

            worksheet.Cells[worksheet.Dimension.Address].AutoFitColumns();
            package.Save();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertDetailBukuKasToExcel(Coa coa, List<Journal> journals)
    {
        using var stream = new MemoryStream();
        using (var package = new ExcelPackage(stream))
        {
            var worksheet = package.Workbook.Worksheets.Add("Detail Buku Kas");

            // Header Info
            worksheet.Cells["A1"].Value = "KODE AKUN";
            worksheet.Cells["B1"].Value = coa.CoaCode;

            worksheet.Cells["A2"].Value = "NAMA AKUN";
            worksheet.Cells["B2"].Value = coa.CoaName;

            worksheet.Cells["A3"].Value = "SALDO SAAT INI";
            worksheet.Cells["B3"].Value = coa.Balance;
            worksheet.Cells["B3"].Style.Numberformat.Format = "#,##0.00";

            using (var range = worksheet.Cells["A1:A3"])
            {
                range.Style.Font.Bold = true;
            }

            // Table Header
            int row = 5;
            worksheet.Cells[row, 1].Value = "TANGGAL";
            worksheet.Cells[row, 2].Value = "NO TRANSAKSI";
            worksheet.Cells[row, 3].Value = "NO JURNAL";
            worksheet.Cells[row, 4].Value = "KETERANGAN";
            worksheet.Cells[row, 5].Value = "DEBIT";
            worksheet.Cells[row, 6].Value = "KREDIT";
            worksheet.Cells[row, 7].Value = "SALDO";

            using (var range = worksheet.Cells[row, 1, row, 7])
            {
                range.Style.Font.Bold = true;
                range.Style.Fill.PatternType = ExcelFillStyle.Solid;
                range.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                range.Style.Font.Color.SetColor(Color.White);
                range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
            }
            row++;

            // transactions
            foreach (var item in journals)
            {
                worksheet.Cells[row, 1].Value = item.CreatedDate;
                worksheet.Cells[row, 1].Style.Numberformat.Format = "dd/MM/yyyy HH:mm";

                worksheet.Cells[row, 2].Value = item.Transaction?.TransactionNo;
                worksheet.Cells[row, 3].Value = item.Transaction?.JournalNo;
                worksheet.Cells[row, 4].Value = item.JournalDesc;

                worksheet.Cells[row, 5].Value = item.DebitAmount;
                worksheet.Cells[row, 5].Style.Numberformat.Format = "#,##0.00";

                worksheet.Cells[row, 6].Value = item.CreditAmount;
                worksheet.Cells[row, 6].Style.Numberformat.Format = "#,##0.00";

                worksheet.Cells[row, 7].Value = item.CurrentBalance;
                worksheet.Cells[row, 7].Style.Numberformat.Format = "#,##0.00";

                row++;
            }

            worksheet.Cells[worksheet.Dimension.Address].AutoFitColumns();
            package.Save();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertToExcel<T>(List<T> data)
    {
        using var package = new ExcelPackage();
        var worksheet = package.Workbook.Worksheets.Add("Sheet1");

        if (data != null && data.Any())
        {
            worksheet.Cells.LoadFromCollection(data, true);
            
            // Format DateTime columns
            var properties = typeof(T).GetProperties();
            for (int i = 0; i < properties.Length; i++)
            {
                var propType = properties[i].PropertyType;
                var underlyingType = Nullable.GetUnderlyingType(propType) ?? propType;
                
                if (underlyingType == typeof(DateTime))
                {
                    // Excel columns are 1-indexed
                    worksheet.Column(i + 1).Style.Numberformat.Format = "dd/MM/yyyy";
                }
            }

            worksheet.Cells.AutoFitColumns();
        }
        else
        {
            worksheet.Cells[1, 1].Value = "No data available";
        }

        return package.GetAsByteArray();
    }

    public static byte[] GenerateLedgerExcel(List<LedgerResponse> jsonData)
    {
        using (var package = new ExcelPackage())
        {
            var ws = package.Workbook.Worksheets.Add("Report");

            int row = 1;

            foreach (var coa in jsonData)
            {
                // Header COA
                ws.Cells[row, 1].Value = $"{coa.CoaCode} - {coa.CoaName}";
                ws.Cells[row, 1, row, 7].Merge = true;
                ws.Cells[row, 1].Style.Font.Bold = true;
                ws.Cells[row, 1].Style.Font.Size = 12;
                ws.Cells[row, 1].Style.Fill.PatternType = ExcelFillStyle.Solid;
                ws.Cells[row, 1].Style.Fill.BackgroundColor.SetColor(Color.LightGray);
                row++;

                // Opening & Closing Balance
                ws.Cells[row, 1].Value = "Opening Balance";
                ws.Cells[row, 2].Value = coa.OpeningBalance;
                ws.Cells[row, 2].Style.Numberformat.Format = "#,##0.00";

                ws.Cells[row, 3].Value = "Closing Balance";
                ws.Cells[row, 4].Value = coa.ClosingBalance;
                ws.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";
                row += 2;

                // Header Transaction Table
                ws.Cells[row, 1].Value = "Date";
                ws.Cells[row, 2].Value = "JOURNAL NO";
                ws.Cells[row, 3].Value = "TRANSACTION NO";
                ws.Cells[row, 4].Value = "Description";
                ws.Cells[row, 5].Value = "Coa Mapping";
                ws.Cells[row, 6].Value = "Debit";
                ws.Cells[row, 7].Value = "Credit";
                ws.Cells[row, 8].Value = "Running Balance";

                using (var range = ws.Cells[row, 1, row, 8])
                {
                    range.Style.Font.Bold = true;
                    range.Style.Fill.PatternType = ExcelFillStyle.Solid;
                    range.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#648de1"));
                    range.Style.Font.Color.SetColor(Color.White);
                    range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                }
                row++;

                // Transactions
                foreach (var trx in coa.Transactions)
                {
                    ws.Cells[row, 1].Value = DateTime.Parse(trx.Date.ToString()).ToString("dd/MM/yyyy");
                    ws.Cells[row, 2].Value = trx.JournalNo?.ToString();
                    ws.Cells[row, 3].Value = trx.TransactionNo?.ToString();
                    ws.Cells[row, 4].Value = trx.Description?.ToString();
                    ws.Cells[row, 5].Value = trx.JournalRef?.ToString();
                    ws.Cells[row, 6].Value = trx.Debit;
                    ws.Cells[row, 7].Value = trx.Credit;
                    ws.Cells[row, 8].Value = trx.RunningBalance;

                    ws.Cells[row, 6].Style.Numberformat.Format = "#,##0.00";
                    ws.Cells[row, 7].Style.Numberformat.Format = "#,##0.00";
                    ws.Cells[row, 8].Style.Numberformat.Format = "#,##0.00";

                    row++;
                }

                // Border untuk tabel transaksi
                using (var range = ws.Cells[row - coa.Transactions.Count, 1, row - 1, 8])
                {
                    range.Style.Border.Top.Style = ExcelBorderStyle.Thin;
                    range.Style.Border.Bottom.Style = ExcelBorderStyle.Thin;
                    range.Style.Border.Left.Style = ExcelBorderStyle.Thin;
                    range.Style.Border.Right.Style = ExcelBorderStyle.Thin;
                }

                row += 2; // spacing antar COA
            }

            ws.Cells.AutoFitColumns();

            return package.GetAsByteArray();
        }
    }
    public static byte[] ConvertTransferListToExcel(List<Transaction> data)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Daftar Transfer");

            // Styles
            var headerStyle = sheet.Cells["A1:K1"].Style;
            headerStyle.Font.Bold = true;
            headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
            headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerStyle.Font.Color.SetColor(Color.White);
            headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

            // Headers
            sheet.Cells[1, 1].Value = "NO TRANSAKSI";
            sheet.Cells[1, 2].Value = "NO JURNAL"; // New
            sheet.Cells[1, 3].Value = "TANGGAL";
            sheet.Cells[1, 4].Value = "NO AKUN PENGIRIM"; // New
            sheet.Cells[1, 5].Value = "NAMA AKUN PENGIRIM"; // Renamed
            sheet.Cells[1, 6].Value = "NO AKUN PENERIMA"; // New
            sheet.Cells[1, 7].Value = "NAMA AKUN PENERIMA"; // Renamed
            sheet.Cells[1, 8].Value = "KETERANGAN";
            sheet.Cells[1, 9].Value = "JUMLAH";
            sheet.Cells[1, 10].Value = "STATUS";

            int row = 2;
            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.TransactionNo;
                sheet.Cells[row, 2].Value = item.JournalNo; // New
                
                sheet.Cells[row, 3].Value = item.TransactionDate;
                sheet.Cells[row, 3].Style.Numberformat.Format = "dd/MM/yyyy";

                sheet.Cells[row, 4].Value = item.CoaCode; // From Code
                sheet.Cells[row, 5].Value = item.CoaName; // From Name

                var receiver = item.Details?.FirstOrDefault(d => d.IsPremier == false);
                sheet.Cells[row, 6].Value = receiver?.CoaCode ?? "-"; // To Code
                sheet.Cells[row, 7].Value = receiver?.CoaName ?? "-"; // To Name

                sheet.Cells[row, 8].Value = item.Description;

                sheet.Cells[row, 9].Value = item.Amount;
                sheet.Cells[row, 9].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 10].Value = item.status;
                
                row++;
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertRevenueExpenseListToExcel(List<Transaction> data, string title)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add(title);

            int row = 1;

            foreach (var item in data)
            {
                // Transaction Header Block
                // Style: Blue Background, White Text
                var headerRange = sheet.Cells[row, 1, row, 4];
                headerRange.Style.Font.Bold = true;
                headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
                headerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                headerRange.Style.Font.Color.SetColor(Color.White);

                // Row 1: TransNo, Date, Status
                sheet.Cells[row, 1].Value = "NO TRANSAKSI";
                sheet.Cells[row, 2].Value = item.TransactionNo;
                sheet.Cells[row, 3].Value = "TANGGAL";
                sheet.Cells[row, 4].Value = item.TransactionDate?.ToString("dd/MM/yyyy");
                sheet.Cells[row, 5].Value = "STATUS";
                sheet.Cells[row, 6].Value = item.status;
                row++;

                // Row 2: JournalNo, Main Account
                sheet.Cells[row, 1].Value = "NO JURNAL";
                sheet.Cells[row, 2].Value = item.JournalNo;
                sheet.Cells[row, 3].Value = "AKUN SUMBER/TUJUAN";
                sheet.Cells[row, 4].Value = $"{item.CoaName} ({item.CoaCode})";
                row++;

                // Row 3: Description, Total
                sheet.Cells[row, 1].Value = "KETERANGAN";
                sheet.Cells[row, 2].Value = item.Description;
                sheet.Cells[row, 3].Value = "TOTAL";
                sheet.Cells[row, 4].Value = item.Amount;
                sheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";
                row++;

                // Detail Header - extended columns
                var detailHeaderStyle = sheet.Cells[row, 1, row, 4].Style;
                detailHeaderStyle.Font.Bold = true;
                detailHeaderStyle.Border.Bottom.Style = ExcelBorderStyle.Thin;
                
                sheet.Cells[row, 1].Value = "NAMA AKUN";
                sheet.Cells[row, 2].Value = "NO AKUN";
                sheet.Cells[row, 3].Value = "DESKRIPSI";
                sheet.Cells[row, 4].Value = "JUMLAH";
                sheet.Cells[row, 4].Style.HorizontalAlignment = ExcelHorizontalAlignment.Right;
                row++;

                // Details
                if (item.Details != null)
                {
                    foreach (var detail in item.Details)
                    {
                        sheet.Cells[row, 1].Value = detail.CoaName;
                        sheet.Cells[row, 2].Value = detail.CoaCode;
                        sheet.Cells[row, 3].Value = detail.JournalDesc;
                        
                        var amount = Math.Max(detail.DebitAmount ?? 0, detail.CreditAmount ?? 0);
                        sheet.Cells[row, 4].Value = amount;
                        sheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";
                        
                        row++;
                    }
                }
                
                row++; // Spacer
            }

            sheet.Column(1).Width = 25;
            sheet.Column(2).Width = 20;
            sheet.Column(3).Width = 30;
            sheet.Column(4).Width = 18;

            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertRevenueExpenseListToExcelFlat(List<Transaction> data, string title)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add(title);

            // Headers
            var headers = new string[] {
                "NO TRANSAKSI", "TANGGAL", "STATUS",
                "NO JURNAL", "AKUN UTAMA",
                "NO AKUN", "NAMA AKUN", "DESKRIPSI",
                "DEBIT", "KREDIT"
            };

            for (int i = 0; i < headers.Length; i++)
            {
                sheet.Cells[1, i + 1].Value = headers[i];
            }

            var headerRange = sheet.Cells[1, 1, 1, headers.Length];
            headerRange.Style.Font.Bold = true;
            headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
            headerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerRange.Style.Font.Color.SetColor(Color.White);
            headerRange.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            headerRange.Style.VerticalAlignment = ExcelVerticalAlignment.Center;

            int row = 2;
            foreach (var item in data)
            {
                if (item.Details != null)
                {
                    foreach (var detail in item.Details)
                    {
                        sheet.Cells[row, 1].Value = item.TransactionNo;
                        sheet.Cells[row, 2].Value = item.TransactionDate?.ToString("dd/MM/yyyy");
                        sheet.Cells[row, 3].Value = item.status;
                        
                        sheet.Cells[row, 4].Value = item.JournalNo;
                        sheet.Cells[row, 5].Value = $"{item.CoaName} ({item.CoaCode})";

                        sheet.Cells[row, 6].Value = detail.CoaCode;
                        sheet.Cells[row, 7].Value = detail.CoaName;
                        sheet.Cells[row, 8].Value = detail.JournalDesc;
                        
                        sheet.Cells[row, 9].Value = detail.DebitAmount ?? 0;
                        sheet.Cells[row, 9].Style.Numberformat.Format = "#,##0.00";

                        sheet.Cells[row, 10].Value = detail.CreditAmount ?? 0;
                        sheet.Cells[row, 10].Style.Numberformat.Format = "#,##0.00";

                        row++;
                    }
                }
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }
    public static byte[] ConvertJournalHistoryToExcel(List<Journal> data)
        {
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("History");

                // Header Style
                var headerStyle = sheet.Cells["A1:H1"].Style;
                headerStyle.Font.Bold = true;
                headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
                headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                headerStyle.Font.Color.SetColor(Color.White);
                headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

                // Header Columns
                sheet.Cells[1, 1].Value = "DATE";
                sheet.Cells[1, 2].Value = "JOURNAL NO";
                sheet.Cells[1, 3].Value = "TRANS NO";
                sheet.Cells[1, 4].Value = "ACCOUNT CODE";
                sheet.Cells[1, 5].Value = "ACCOUNT NAME";
                sheet.Cells[1, 6].Value = "DESCRIPTION";
                sheet.Cells[1, 7].Value = "DEBIT";
                sheet.Cells[1, 8].Value = "CREDIT";

                int row = 2;
                foreach (var item in data)
                {
                    sheet.Cells[row, 1].Value = item.CreatedDate;
                    sheet.Cells[row, 1].Style.Numberformat.Format = "dd/MM/yyyy HH:mm";

                    sheet.Cells[row, 2].Value = item.TransactionDetail?.JournalNo ?? "-";
                    sheet.Cells[row, 3].Value = item.TransactionDetail?.TransactionNo ?? "-";
                    
                    sheet.Cells[row, 4].Value = item.CoaCode;
                    sheet.Cells[row, 5].Value = item.CoaName;
                    sheet.Cells[row, 6].Value = item.JournalDesc;

                    sheet.Cells[row, 7].Value = item.DebitAmount;
                    sheet.Cells[row, 7].Style.Numberformat.Format = "#,##0.00";

                    sheet.Cells[row, 8].Value = item.CreditAmount;
                    sheet.Cells[row, 8].Style.Numberformat.Format = "#,##0.00";

                    row++;
                }

                sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
                return package.GetAsByteArray();
            }

        }

        public static byte[] ConvertLedgerSummaryToExcel(List<RSSMBackend.DTOs.Journal.SummaryLedgerResponse> data)
        {
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("Summary");

                // Header Style
                var headerStyle = sheet.Cells["A1:G1"].Style;
                headerStyle.Font.Bold = true;
                headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
                headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                headerStyle.Font.Color.SetColor(Color.White);
                headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

                // Header Columns
                sheet.Cells[1, 1].Value = "ACCOUNT CODE";
                sheet.Cells[1, 2].Value = "ACCOUNT NAME";
                sheet.Cells[1, 3].Value = "BEGINNING BALANCE";
                sheet.Cells[1, 4].Value = "DEBIT";
                sheet.Cells[1, 5].Value = "CREDIT";
                sheet.Cells[1, 6].Value = "NET CHANGE";
                sheet.Cells[1, 7].Value = "ENDING BALANCE";

                int row = 2;
                foreach (var item in data)
                {
                    sheet.Cells[row, 1].Value = item.CoaCode;
                    sheet.Cells[row, 2].Value = item.CoaName;

                    sheet.Cells[row, 3].Value = item.BeginningBalance;
                    sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";

                    sheet.Cells[row, 4].Value = item.Debit;
                    sheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";

                    sheet.Cells[row, 5].Value = item.Credit;
                    sheet.Cells[row, 5].Style.Numberformat.Format = "#,##0.00";

                    sheet.Cells[row, 6].Value = item.NetChange;
                    sheet.Cells[row, 6].Style.Numberformat.Format = "#,##0.00";
                    sheet.Cells[row, 6].Style.Font.Color.SetColor(item.NetChange < 0 ? Color.Red : Color.Green);

                    sheet.Cells[row, 7].Value = item.EndingBalance;
                    sheet.Cells[row, 7].Style.Numberformat.Format = "#,##0.00";

                    row++;
                }

                sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
                return package.GetAsByteArray();
            }
        }

        public static byte[] ConvertBalanceSheetToExcel(BalanceSheetResponse data, DateTime? startDate, DateTime? endDate)
        {
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("Balance Sheet");

                // Title
                sheet.Cells["A1"].Value = "LAPORAN NERACA (BALANCE SHEET)";
                sheet.Cells["A1:C1"].Merge = true;
                sheet.Cells["A1"].Style.Font.Bold = true;
                sheet.Cells["A1"].Style.Font.Size = 14;

                sheet.Cells["A2"].Value = $"Periode: {startDate?.ToString("dd/MM/yyyy")} - {endDate?.ToString("dd/MM/yyyy")}";
                sheet.Cells["A2:C2"].Merge = true;

                // Header Style
                var headerRange = sheet.Cells["A4:C4"];
                headerRange.Style.Font.Bold = true;
                headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
                headerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                headerRange.Style.Font.Color.SetColor(Color.White);

                sheet.Cells[4, 1].Value = "KODE AKUN";
                sheet.Cells[4, 2].Value = "NAMA AKUN";
                sheet.Cells[4, 3].Value = "SALDO";

                int row = 5;
                void WriteSection(BalanceSheetSection section, int level)
                {
                    string indent = new string(' ', level * 2);
                    sheet.Cells[row, 1].Value = section.CoaCode;
                    sheet.Cells[row, 2].Value = indent + section.SectionName;
                    sheet.Cells[row, 3].Value = section.Total;
                    sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";

                    if (section.SubSection != null && section.SubSection.Count > 0)
                    {
                        sheet.Cells[row, 1, row, 3].Style.Font.Bold = true;
                        sheet.Cells[row, 1, row, 3].Style.Fill.PatternType = ExcelFillStyle.Solid;
                        sheet.Cells[row, 1, row, 3].Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#e0e7ff"));
                    }
                    row++;

                    if (section.SubSection != null)
                    {
                        foreach (var sub in section.SubSection)
                            WriteSection(sub, level + 1);
                    }
                }

                foreach (var section in data.Data)
                    WriteSection(section, 0);

                // Summary
                row++;
                sheet.Cells[row, 2].Value = "TOTAL ASET";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.TotalAssets ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "TOTAL KEWAJIBAN";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.TotalLiabilities ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "TOTAL EKUITAS";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.TotalEquity ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;

                sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
                return package.GetAsByteArray();
            }
        }

        public static byte[] ConvertProfitLossToExcel(ProfitLossResponse data, DateTime? startDate, DateTime? endDate)
        {
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("Profit Loss");

                // Title
                sheet.Cells["A1"].Value = "LAPORAN LABA RUGI (PROFIT & LOSS)";
                sheet.Cells["A1:C1"].Merge = true;
                sheet.Cells["A1"].Style.Font.Bold = true;
                sheet.Cells["A1"].Style.Font.Size = 14;

                sheet.Cells["A2"].Value = $"Periode: {startDate?.ToString("dd/MM/yyyy")} - {endDate?.ToString("dd/MM/yyyy")}";
                sheet.Cells["A2:C2"].Merge = true;

                // Header Style
                var headerRange = sheet.Cells["A4:C4"];
                headerRange.Style.Font.Bold = true;
                headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
                headerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                headerRange.Style.Font.Color.SetColor(Color.White);

                sheet.Cells[4, 1].Value = "KODE AKUN";
                sheet.Cells[4, 2].Value = "NAMA AKUN";
                sheet.Cells[4, 3].Value = "JUMLAH";

                int row = 5;
                void WriteSection(BalanceSheetSection section, int level)
                {
                    string indent = new string(' ', level * 2);
                    sheet.Cells[row, 1].Value = section.CoaCode;
                    sheet.Cells[row, 2].Value = indent + section.SectionName;
                    sheet.Cells[row, 3].Value = section.Total;
                    sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";

                    if (section.SubSection != null && section.SubSection.Count > 0)
                    {
                        sheet.Cells[row, 1, row, 3].Style.Font.Bold = true;
                        sheet.Cells[row, 1, row, 3].Style.Fill.PatternType = ExcelFillStyle.Solid;
                        sheet.Cells[row, 1, row, 3].Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#e0e7ff"));
                    }
                    row++;

                    if (section.SubSection != null)
                    {
                        foreach (var sub in section.SubSection)
                            WriteSection(sub, level + 1);
                    }
                }

                foreach (var section in data.Data)
                    WriteSection(section, 0);

                // Summary
                row++;
                sheet.Cells[row, 2].Value = "LABA KOTOR";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.TotalGrossProfit ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "LABA OPERASIONAL";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.TotalOperatingProfit ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "LABA BERSIH";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.TotalNetProfit ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                sheet.Cells[row, 3].Style.Font.Color.SetColor((data.Summary?.TotalNetProfit ?? 0) >= 0 ? Color.Green : Color.Red);

                sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
                return package.GetAsByteArray();
            }
        }
        public static byte[] ConvertCashFlowToExcel(CashFlowResponse data, DateTime? startDate, DateTime? endDate)
        {
            using (var package = new ExcelPackage())
            {
                var sheet = package.Workbook.Worksheets.Add("Cash Flow");

                // Title
                sheet.Cells["A1"].Value = "LAPORAN ARUS KAS (CASH FLOW)";
                sheet.Cells["A1:C1"].Merge = true;
                sheet.Cells["A1"].Style.Font.Bold = true;
                sheet.Cells["A1"].Style.Font.Size = 14;

                sheet.Cells["A2"].Value = $"Periode: {startDate?.ToString("dd/MM/yyyy")} - {endDate?.ToString("dd/MM/yyyy")}";
                sheet.Cells["A2:C2"].Merge = true;

                // Header Style
                var headerRange = sheet.Cells["A4:C4"];
                headerRange.Style.Font.Bold = true;
                headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
                headerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
                headerRange.Style.Font.Color.SetColor(Color.White);

                sheet.Cells[4, 1].Value = "KODE AKUN";
                sheet.Cells[4, 2].Value = "KETERANGAN";
                sheet.Cells[4, 3].Value = "JUMLAH";

                int row = 5;
                void WriteSection(BalanceSheetSection section, int level)
                {
                    string indent = new string(' ', level * 2);
                    sheet.Cells[row, 1].Value = section.CoaCode;
                    sheet.Cells[row, 2].Value = indent + section.SectionName;
                    sheet.Cells[row, 3].Value = section.Total;
                    sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";

                    if (section.SubSection != null && section.SubSection.Count > 0)
                    {
                        sheet.Cells[row, 1, row, 3].Style.Font.Bold = true;
                        sheet.Cells[row, 1, row, 3].Style.Fill.PatternType = ExcelFillStyle.Solid;
                        sheet.Cells[row, 1, row, 3].Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#e0e7ff"));
                    }
                    row++;

                    if (section.SubSection != null)
                    {
                        foreach (var sub in section.SubSection)
                            WriteSection(sub, level + 1);
                    }
                }

                foreach (var section in data.Data)
                    WriteSection(section, 0);

                // Summary
                row++;
                sheet.Cells[row, 2].Value = "AKTIVITAS OPERASI";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.OperatingActivities ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "AKTIVITAS INVESTASI";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.InvestingActivities ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "AKTIVITAS PENDANAAN";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.FinancingActivities ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "KENAIKAN/(PENURUNAN) KAS BERSIH";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.NetCashFlow ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                sheet.Cells[row, 3].Style.Font.Color.SetColor((data.Summary?.NetCashFlow ?? 0) >= 0 ? Color.Green : Color.Red);
                row++;

                sheet.Cells[row, 2].Value = "SALDO AWAL KAS";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.CashBeginning ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;
                row++;

                sheet.Cells[row, 2].Value = "SALDO AKHIR KAS";
                sheet.Cells[row, 2].Style.Font.Bold = true;
                sheet.Cells[row, 3].Value = data.Summary?.CashEnding ?? 0;
                sheet.Cells[row, 3].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Style.Font.Bold = true;

                sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
                return package.GetAsByteArray();
            }
        }

        public static byte[] ConvertMedicalFeeToExcel(List<MedicalFeeResponse> data)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Medical Fees");

            // Define Headers
            var headers = new[] {
                "REF ID", "STATUS", "DOCTOR CODE", "DOCTOR NAME",
                "NO REGIST", "PATIENT NAME", "NO SEP", "PAYER", "JENIS PELAYANAN",
                "PROCEDURE DATE", "PROCEDURE NAME", "TOTAL FEE", "BPJS APPROVE"
            };

            // Styles
            var headerStyle = sheet.Cells["A1:M1"].Style;
            headerStyle.Font.Bold = true;
            headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
            headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerStyle.Font.Color.SetColor(Color.White);
            headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

            // Write Headers
            for (int i = 0; i < headers.Length; i++)
            {
                sheet.Cells[1, i + 1].Value = headers[i];
            }

            int row = 2;
            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.MedicalFeeId;
                // Map Unpaid to Posting for display consistency if needed, or just show raw status
                sheet.Cells[row, 2].Value = item.Status; 
                sheet.Cells[row, 3].Value = item.DoctorCode;
                sheet.Cells[row, 4].Value = item.DoctorName;

                sheet.Cells[row, 5].Value = item.MedicalRecordNo;
                sheet.Cells[row, 6].Value = item.PatientName;
                sheet.Cells[row, 7].Value = item.SepNo;
                sheet.Cells[row, 8].Value = item.PayerName;
                sheet.Cells[row, 9].Value = item.MedicalProcedure;

                // Format Procedure Date
                if (item.ProcedureDate.HasValue)
                {
                    sheet.Cells[row, 10].Value = item.ProcedureDate.Value;
                    sheet.Cells[row, 10].Style.Numberformat.Format = "dd/MM/yyyy";
                }
                else
                {
                    sheet.Cells[row, 10].Value = "-";
                }

                sheet.Cells[row, 11].Value = item.ProcedureName;

                sheet.Cells[row, 12].Value = item.TotalFee;
                sheet.Cells[row, 12].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 13].Value = item.AdjustmentAmount ?? 0;
                sheet.Cells[row, 13].Style.Numberformat.Format = "#,##0.00";

                row++;
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertSettlementToExcel(List<SettlementSummary> data)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Settlement");

            // Define Headers
            var headers = new[] {
                "PAYMENT VOUCHER", "DOCTOR NAME", "VOUCHER DATE",
                "DOCTOR SHARE (%)", "TOTAL FEE", "AMOUNT AFTER SHARE",
                "ITEM COUNT", "STATUS"
            };

            // Styles
            var headerStyle = sheet.Cells["A1:H1"].Style;
            headerStyle.Font.Bold = true;
            headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
            headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerStyle.Font.Color.SetColor(Color.White);
            headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

            // Write Headers
            for (int i = 0; i < headers.Length; i++)
            {
                sheet.Cells[1, i + 1].Value = headers[i];
            }

            int row = 2;
            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.PaymentVoucher;
                sheet.Cells[row, 2].Value = item.DoctorName;

                if (item.VoucherDate.HasValue)
                {
                    sheet.Cells[row, 3].Value = item.VoucherDate.Value;
                    sheet.Cells[row, 3].Style.Numberformat.Format = "dd/MM/yyyy";
                }
                else
                {
                    sheet.Cells[row, 3].Value = "-";
                }

                sheet.Cells[row, 4].Value = item.DoctorShare;
                sheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 5].Value = item.TotalFee;
                sheet.Cells[row, 5].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 6].Value = item.TotalAfterShare;
                sheet.Cells[row, 6].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 7].Value = item.ItemCount;
                sheet.Cells[row, 8].Value = item.Status;

                row++;
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertSettlementMakerToExcel(List<SettlementMakerExport> data)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Settlement Maker");

            // Define Headers
            var headers = new[] {
                "Nama Dokter", "Honor Netto", "Bank", "Rekening"
            };

            // Styles
            var headerStyle = sheet.Cells["A1:D1"].Style;
            headerStyle.Font.Bold = true;
            headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
            headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerStyle.Font.Color.SetColor(Color.White);
            headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

            // Write Headers
            for (int i = 0; i < headers.Length; i++)
            {
                sheet.Cells[1, i + 1].Value = headers[i];
            }

            int row = 2;
            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.DoctorName;
                sheet.Cells[row, 2].Value = item.HonorNetto;
                sheet.Cells[row, 2].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 3].Value = item.BankName;
                sheet.Cells[row, 4].Value = item.AccountNo;

                row++;
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertAccountPayableToExcel(List<AccountPayableResponse> data, DateTime? startDate, DateTime? endDate)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Account Payable");

            // Title
            sheet.Cells["A1"].Value = "LAPORAN ACCOUNT PAYABLE (HUTANG USAHA)";
            sheet.Cells["A1:N1"].Merge = true;
            sheet.Cells["A1"].Style.Font.Bold = true;
            sheet.Cells["A1"].Style.Font.Size = 14;
            sheet.Cells["A1"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            string period = "Semua Periode";
            if (startDate.HasValue && endDate.HasValue)
                period = $"{startDate.Value:dd/MM/yyyy} - {endDate.Value:dd/MM/yyyy}";
            else if (startDate.HasValue)
                period = $">= {startDate.Value:dd/MM/yyyy}";
            else if (endDate.HasValue)
                period = $"<= {endDate.Value:dd/MM/yyyy}";

            sheet.Cells["A2"].Value = $"Periode Jatuh Tempo: {period}";
            sheet.Cells["A2:N2"].Merge = true;
            sheet.Cells["A2"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            // Define Headers
            var headers = new[] {
                "SUPPLIER", "NO. PO", "NO. FAKTUR", "TGL TERIMA", 
                "JATUH TEMPO", "TGL BAYAR", "TOTAL TRX", "TERBAYAR", "DP", "OUTSTANDING",
                "STATUS", "STATUS BAYAR", "AGING (HARI)", "KATEGORI AGING"
            };

            // Header Style
            var headerRange = sheet.Cells[4, 1, 4, headers.Length];
            headerRange.Style.Font.Bold = true;
            headerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
            headerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerRange.Style.Font.Color.SetColor(Color.White);
            headerRange.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            // Write Headers
            for (int i = 0; i < headers.Length; i++)
            {
                sheet.Cells[4, i + 1].Value = headers[i];
            }

            int row = 5;
            double totalTrx = 0;
            double totalPaid = 0;
            double totalDP = 0;
            double totalOutstanding = 0;

            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.SupplierName;
                sheet.Cells[row, 2].Value = item.PONumber;
                sheet.Cells[row, 3].Value = item.InvoiceNumber;
                
                if (item.ReceiptDate.HasValue)
                {
                    sheet.Cells[row, 4].Value = item.ReceiptDate.Value;
                    sheet.Cells[row, 4].Style.Numberformat.Format = "dd/MM/yyyy";
                }
                
                if (item.DueDate.HasValue)
                {
                    sheet.Cells[row, 5].Value = item.DueDate.Value;
                    sheet.Cells[row, 5].Style.Numberformat.Format = "dd/MM/yyyy";
                }

                // Tanggal bayar. Kosong selama invoice belum dibayar; untuk pembayaran
                // bertahap berisi tanggal pembayaran terakhir.
                if (item.PaymentDate.HasValue)
                {
                    sheet.Cells[row, 6].Value = item.PaymentDate.Value;
                    sheet.Cells[row, 6].Style.Numberformat.Format = "dd/MM/yyyy";
                }

                sheet.Cells[row, 7].Value = MoneyRounding.Round(item.TotalTransaction);
                sheet.Cells[row, 7].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

                sheet.Cells[row, 8].Value = MoneyRounding.Round(item.PaidAmount);
                sheet.Cells[row, 8].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

                // DP PO yang dialokasikan ke invoice ini - sudah ikut mengurangi OUTSTANDING
                // di kolom sebelah, jadi harus terlihat supaya angkanya bisa ditelusuri.
                sheet.Cells[row, 9].Value = MoneyRounding.Round(item.TotalDP);
                sheet.Cells[row, 9].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

                sheet.Cells[row, 10].Value = MoneyRounding.Round(item.OutstandingAmount);
                sheet.Cells[row, 10].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

                sheet.Cells[row, 11].Value = item.Status;
                sheet.Cells[row, 12].Value = item.PaymentStatus;
                sheet.Cells[row, 13].Value = item.AgingDays ?? 0;
                sheet.Cells[row, 14].Value = item.AgingBucket;

                // Accumulate the exact values, not the rounded cells -- the total is rounded
                // once, at the point it is written.
                totalTrx += item.TotalTransaction ?? 0;
                totalPaid += item.PaidAmount ?? 0;
                totalDP += item.TotalDP ?? 0;
                totalOutstanding += item.OutstandingAmount ?? 0;
                row++;
            }

            // Totals
            var footerRange = sheet.Cells[row, 1, row, headers.Length];
            footerRange.Style.Font.Bold = true;
            footerRange.Style.Fill.PatternType = ExcelFillStyle.Solid;
            footerRange.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#f8fafc"));
            footerRange.Style.Border.Top.Style = ExcelBorderStyle.Thin;

            sheet.Cells[row, 1].Value = "TOTAL";
            sheet.Cells[row, 7].Value = MoneyRounding.Round(totalTrx);
            sheet.Cells[row, 7].Style.Numberformat.Format = MoneyRounding.ExcelFormat;
            sheet.Cells[row, 8].Value = MoneyRounding.Round(totalPaid);
            sheet.Cells[row, 8].Style.Numberformat.Format = MoneyRounding.ExcelFormat;
            sheet.Cells[row, 9].Value = MoneyRounding.Round(totalDP);
            sheet.Cells[row, 9].Style.Numberformat.Format = MoneyRounding.ExcelFormat;
            sheet.Cells[row, 10].Value = MoneyRounding.Round(totalOutstanding);
            sheet.Cells[row, 10].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }

    public static byte[] ConvertAccountPayableResiToExcel(AccountPayableDetailResponse data, string printedBy)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Resi Tukar Faktur");

            // Title
            sheet.Cells["A1"].Value = "RESI TUKAR FAKTUR";
            sheet.Cells["A1:F1"].Merge = true;
            sheet.Cells["A1"].Style.Font.Bold = true;
            sheet.Cells["A1"].Style.Font.Size = 16;
            sheet.Cells["A1"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            sheet.Cells["A2"].Value = "Invoice Exchange Receipt";
            sheet.Cells["A2:F2"].Merge = true;
            sheet.Cells["A2"].Style.Font.Italic = true;
            sheet.Cells["A2"].Style.Font.Color.SetColor(Color.Gray);
            sheet.Cells["A2"].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            // Info Section
            sheet.Cells["A4"].Value = "Supplier";
            sheet.Cells["B4"].Value = ": " + data.SupplierName;
            sheet.Cells["A4:A4"].Style.Font.Bold = true;

            sheet.Cells["A5"].Value = "No. PO";
            sheet.Cells["B5"].Value = ": " + (data.PONumber ?? "-");
            sheet.Cells["A5:A5"].Style.Font.Bold = true;

            sheet.Cells["A6"].Value = "No. Faktur";
            sheet.Cells["B6"].Value = ": " + (data.InvoiceNumber ?? "-");
            sheet.Cells["A6:A6"].Style.Font.Bold = true;

            sheet.Cells["E4"].Value = "Dicetak pada";
            sheet.Cells["F4"].Value = ": " + DateTime.Now.ToString("dd/MM/yyyy HH:mm");
            sheet.Cells["E5"].Value = "Oleh";
            sheet.Cells["F5"].Value = ": " + printedBy;

            // Table Headers
            var headers = new[] { "No", "Keterangan", "Tanggal", "Jatuh Tempo", "Jumlah" };
            int startRow = 8;
            for (int i = 0; i < headers.Length; i++)
            {
                var cell = sheet.Cells[startRow, i + 1];
                cell.Value = headers[i];
                cell.Style.Font.Bold = true;
                cell.Style.Fill.PatternType = ExcelFillStyle.Solid;
                cell.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#f1f5f9"));
                cell.Style.Border.BorderAround(ExcelBorderStyle.Thin);
            }

            // Item Row (Resi for AP is usually one item or summary)
            int row = startRow + 1;
            sheet.Cells[row, 1].Value = 1;
            sheet.Cells[row, 2].Value = $"Tukar Faktur: {data.InvoiceNumber}";
            
            if (data.ReceiptDate.HasValue)
            {
                sheet.Cells[row, 3].Value = data.ReceiptDate.Value;
                sheet.Cells[row, 3].Style.Numberformat.Format = "dd/MM/yyyy";
            }
            
            if (data.DueDate.HasValue)
            {
                sheet.Cells[row, 4].Value = data.DueDate.Value;
                sheet.Cells[row, 4].Style.Numberformat.Format = "dd/MM/yyyy";
            }

            sheet.Cells[row, 5].Value = MoneyRounding.Round(data.TotalTransaction);
            sheet.Cells[row, 5].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

            for (int i = 1; i <= 5; i++)
                sheet.Cells[row, i].Style.Border.BorderAround(ExcelBorderStyle.Thin);

            // Totals Section
            row += 2;
            sheet.Cells[row, 4].Value = "Total Transaksi";
            sheet.Cells[row, 5].Value = MoneyRounding.Round(data.TotalTransaction);
            sheet.Cells[row, 5].Style.Numberformat.Format = MoneyRounding.ExcelFormat;
            sheet.Cells[row, 4, row, 5].Style.Font.Bold = true;

            row++;
            sheet.Cells[row, 4].Value = "Total Terbayar";
            sheet.Cells[row, 5].Value = MoneyRounding.Round(data.PaidAmount);
            sheet.Cells[row, 5].Style.Numberformat.Format = MoneyRounding.ExcelFormat;

            row++;
            sheet.Cells[row, 4].Value = "Outstanding";
            sheet.Cells[row, 5].Value = MoneyRounding.Round(data.OutstandingAmount);
            sheet.Cells[row, 5].Style.Numberformat.Format = MoneyRounding.ExcelFormat;
            sheet.Cells[row, 4, row, 5].Style.Font.Bold = true;
            sheet.Cells[row, 5].Style.Font.Size = 12;

            // Signatures
            row += 4;
            sheet.Cells[row, 1].Value = "Diserahkan oleh,";
            sheet.Cells[row, 1].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            sheet.Cells[row, 5].Value = "Diterima oleh,";
            sheet.Cells[row, 5].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            row += 4;
            sheet.Cells[row, 1].Value = "( ____________________ )";
            sheet.Cells[row, 1].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            sheet.Cells[row, 5].Value = "( ____________________ )";
            sheet.Cells[row, 5].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            row++;
            sheet.Cells[row, 1].Value = "Bagian Keuangan";
            sheet.Cells[row, 1].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            sheet.Cells[row, 5].Value = "Supplier";
            sheet.Cells[row, 5].Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            sheet.Column(2).Width = 40; // Keterangan

            return package.GetAsByteArray();
        }
    }

    public static byte[] GenerateUnbalancedJournalExcel(List<UnbalancedJournalResponse> data)
        {
            using var package = new ExcelPackage();
            var sheet = package.Workbook.Worksheets.Add("Unbalanced Journals");

            // Headers
            sheet.Cells[1, 1].Value = "NO TRANSAKSI";
            sheet.Cells[1, 2].Value = "TANGGAL";
            sheet.Cells[1, 3].Value = "DESKRIPSI";
            sheet.Cells[1, 4].Value = "TOTAL DEBIT";
            sheet.Cells[1, 5].Value = "TOTAL CREDIT";
            sheet.Cells[1, 6].Value = "SELISIH";
            sheet.Cells[1, 7].Value = "STATUS";

            using (var range = sheet.Cells[1, 1, 1, 7])
            {
                range.Style.Font.Bold = true;
                range.Style.Fill.PatternType = ExcelFillStyle.Solid;
                range.Style.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#eb4034")); // Red for alert
                range.Style.Font.Color.SetColor(Color.White);
                range.Style.HorizontalAlignment = ExcelHorizontalAlignment.Center;
                range.Style.VerticalAlignment = ExcelVerticalAlignment.Center;
            }

            int row = 2;
            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.TransactionNo;
                sheet.Cells[row, 2].Value = item.TransactionDate;
                sheet.Cells[row, 2].Style.Numberformat.Format = "dd/MM/yyyy";
                sheet.Cells[row, 3].Value = item.Description;
                sheet.Cells[row, 4].Value = item.TotalDebit;
                sheet.Cells[row, 4].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 5].Value = item.TotalCredit;
                sheet.Cells[row, 5].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 6].Value = item.Difference;
                sheet.Cells[row, 6].Style.Numberformat.Format = "#,##0.00";
                sheet.Cells[row, 7].Value = item.Status;

                row++;
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }

    public static byte[] ConvertAccountReceivableToExcel(List<RSSMBackend.Entities.AccountReceivable> data)
    {
        using (var package = new ExcelPackage())
        {
            var sheet = package.Workbook.Worksheets.Add("Account Receivable");

            var headers = new[] {
                "AR NUMBER", "NO SEP", "CLAIM NUMBER", "NO RM", "PATIENT NAME",
                "TRANSACTION DATE", "PAYER NAME", "PAYER TYPE", "SERVICE TYPE",
                "TOTAL AMOUNT", "APPROVED AMOUNT", "STATUS", "VOUCHER NUMBER"
            };

            // Header styles
            var headerStyle = sheet.Cells["A1:M1"].Style;
            headerStyle.Font.Bold = true;
            headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
            headerStyle.Fill.BackgroundColor.SetColor(ColorTranslator.FromHtml("#4361ee"));
            headerStyle.Font.Color.SetColor(Color.White);
            headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
            headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

            for (int i = 0; i < headers.Length; i++)
            {
                sheet.Cells[1, i + 1].Value = headers[i];
            }

            int row = 2;
            foreach (var item in data)
            {
                sheet.Cells[row, 1].Value = item.ArNumber;
                sheet.Cells[row, 2].Value = item.SepNo;
                sheet.Cells[row, 3].Value = item.ClaimNumber;
                sheet.Cells[row, 4].Value = item.MedicalRecordNo;
                sheet.Cells[row, 5].Value = item.PatientName;

                if (item.TransactionDate != default)
                {
                    sheet.Cells[row, 6].Value = item.TransactionDate;
                    sheet.Cells[row, 6].Style.Numberformat.Format = "dd/MM/yyyy";
                }

                sheet.Cells[row, 7].Value = item.PayerName;
                sheet.Cells[row, 8].Value = item.PayerTypeName;
                sheet.Cells[row, 9].Value = item.ServiceType == "Ralan" ? "Rawat Jalan" : (item.ServiceType == "Ranap" ? "Rawat Inap" : item.ServiceType);

                sheet.Cells[row, 10].Value = item.TotalAmount;
                sheet.Cells[row, 10].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 11].Value = item.AdjustmentAmount ?? 0;
                sheet.Cells[row, 11].Style.Numberformat.Format = "#,##0.00";

                sheet.Cells[row, 12].Value = item.PaymentStatus ?? "Pending";
                sheet.Cells[row, 13].Value = item.VoucherNumber;

                row++;
            }

            sheet.Cells[sheet.Dimension.Address].AutoFitColumns();
            return package.GetAsByteArray();
        }
    }
    }


public class ImportTo
{
    public async Task<ImportFileResponse<Object>> JSONFile(string filePath)
    {
        try
        {
            var readJson = System.IO.File.ReadAllText(filePath);
            if (string.IsNullOrWhiteSpace(readJson))
                return new ImportFileResponse<Object>("File is Empty", null);

            var textToJson = JsonSerializer.Deserialize<List<Object>>(readJson);
            if (textToJson == null || textToJson.Count == 0)
                return new ImportFileResponse<Object>("File is Empty", null);

            return new ImportFileResponse<Object>("Success Read to File", textToJson);
        }
        catch (Exception e)
        {
            return new ImportFileResponse<Object>($"{e.Message}", null);
        }
    }
}