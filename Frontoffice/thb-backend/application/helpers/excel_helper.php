<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;

function create_excel($headers, $rows, $style) {
    $spreadsheet = new Spreadsheet();
    $activeWorksheet = $spreadsheet->getActiveSheet();

    $column = 1;
    $row = 1;

    foreach ($headers as $key => $val) {
        foreach ($val as $key_val => $val_val) {
            if ($key_val == 'width') {
                $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
            }
        }

        // applying style
        $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->applyFromArray($style);
        // bold text
        $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getAlignment()->setHorizontal('center');
        // bold text
        $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFont()->setBold(true);
        // color cell
        $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
        // set cell value
        $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

        $column += 1;
    }

    for ($i=0; $i < count($rows); $i++) { 
        $row += 1;
        $column = 1;

        for ($j=0; $j < count($rows[$i]); $j++) {             
            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->applyFromArray($style);
            // set auto size width
            // $activeWorksheet->getColumnDimensionByColumn($column)->setAutoSize(true);

            // condition to merge cell
            if (count($rows[$i]) == 1) {
                $activeWorksheet->mergeCellsByColumnAndRow($column,$row,count($headers),$row);
                // bold text
                $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFont()->setBold(true);
                // set cell value
                $activeWorksheet->setCellValueByColumnAndRow($column, $row, $rows[$i]);
            } else {
                // set cell value
                $activeWorksheet->setCellValueByColumnAndRow($column, $row, $rows[$i][$j]);
            }
            
            $column += 1;
        }
    }

    return $spreadsheet;
}