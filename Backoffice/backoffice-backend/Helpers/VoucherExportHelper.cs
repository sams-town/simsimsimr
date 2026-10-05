using System;
using System.IO;
using System.Linq;
using OfficeOpenXml;
using OfficeOpenXml.Style;
using RSSMBackend.Models;

namespace RSSMBackend.Helpers;

public class VoucherExportHelper
{
    public static byte[] GenerateVoucherDetailExcel(VoucherDetailResponse data)
    {
        using var package = new ExcelPackage();
        var worksheet = package.Workbook.Worksheets.Add("Rincian Jasa Medis");

        // Define Headers
        var headers = new[] {
            "REF ID","PAYMENT VOUCHER", "DOCTOR CODE", "DOCTOR NAME",
            "NO REGIST", "PATIENT NAME", "NO SEP", "PAYER",
            "PROCEDURE DATE", "PROCEDURE NAME", "TOTAL FEE", "BPJS APPROVE"
        };

        // Styles
        var headerStyle = worksheet.Cells["A1:L1"].Style;
        headerStyle.Font.Bold = true;
        headerStyle.Fill.PatternType = ExcelFillStyle.Solid;
        headerStyle.Fill.BackgroundColor.SetColor(System.Drawing.ColorTranslator.FromHtml("#4361ee"));
        headerStyle.Font.Color.SetColor(System.Drawing.Color.White);
        headerStyle.HorizontalAlignment = ExcelHorizontalAlignment.Center;
        headerStyle.VerticalAlignment = ExcelVerticalAlignment.Center;

        // Write Headers
        for (int i = 0; i < headers.Length; i++)
        {
            worksheet.Cells[1, i + 1].Value = headers[i];
        }

        int row = 2;
        foreach (var item in data.Items)
        {
            worksheet.Cells[row, 1].Value = item.MedicalFeeId;
            worksheet.Cells[row, 2].Value = item.PaymentVoucher ?? "-";
            worksheet.Cells[row, 3].Value = data.DoctorCode;
            worksheet.Cells[row, 4].Value = data.DoctorName;
            
            worksheet.Cells[row, 5].Value = item.MedicalRecordNo;
            worksheet.Cells[row, 6].Value = item.PatientName;
            worksheet.Cells[row, 7].Value = item.SepNo ?? item.VisitNo ?? "";
            worksheet.Cells[row, 8].Value = item.PayerName;
            
            // Procedure Date
            if (item.ProcedureDate.HasValue)
            {
                worksheet.Cells[row, 9].Value = item.ProcedureDate.Value;
                worksheet.Cells[row, 9].Style.Numberformat.Format = "dd/MM/yyyy";
            }
            else
            {
                worksheet.Cells[row, 9].Value = "-";
            }

            worksheet.Cells[row, 10].Value = item.ProcedureName;
            
            worksheet.Cells[row, 11].Value = item.TotalFee;
            worksheet.Cells[row, 11].Style.Numberformat.Format = "#,##0.00";
            
            // BPJS Approve
            var bpjsApprove = (item.AdjustmentAmount != null && item.AdjustmentAmount != 0) ? item.AdjustmentAmount : item.TotalFee;
            worksheet.Cells[row, 12].Value = bpjsApprove;
            worksheet.Cells[row, 12].Style.Numberformat.Format = "#,##0.00";

            row++;
        }

        worksheet.Cells[worksheet.Dimension.Address].AutoFitColumns();

        return package.GetAsByteArray();
    }
}
