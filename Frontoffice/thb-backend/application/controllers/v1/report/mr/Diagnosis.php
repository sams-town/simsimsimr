<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Diagnosis extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        // Load Helper
        $this->load->helper("excel");
    }

    public function get_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        }

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        }

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date_end'];

        $style = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array(
                        'argb' => 'FF000000',
                    ),
                ),
            ),
        );

        $headers = array(
            'NO' => array(
                'width' => 5
            ),
            'KODE' => array(
                'width' => 10
            ),
            'DIAGNOSA' => array(
                'width' => 50
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

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
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $icd10 = $this->db->query("
            SELECT i.code, i.name, COUNT(*) as total
            FROM soap_a_action saa
            INNER JOIN icd10 i ON saa.icd10_id = i.id
            INNER JOIN soap_a sa ON saa.soap_a_id = sa.id
            INNER JOIN soap s ON sa.soap_id = s.id
            INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
            WHERE saa.is_delete = 0
            AND saa.index = 0
            AND s.is_delete = 0
            AND s.is_resume_exit = 0
            AND rd.is_cancel = 0
            AND rd.checkin_date >= '$date_start 00:00:00'
            AND rd.checkin_date <= '$date_end 23:59:59'
            GROUP BY saa.icd10_id
            ORDER BY total DESC
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($icd10); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('D'.$coordinate)->applyFromArray($style);

            $code = $icd10[$i]->code;
            $name = $icd10[$i]->name;
            $total = $icd10[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $code);
            $activeWorksheet->setCellValue('C' . $coordinate, $name);
            $activeWorksheet->setCellValue('D' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_KUNJUNGAN_PER_DIAGNOSA_' . date('Ymd',strtotime($date_start)) . '_' . date('Ymd',strtotime($date_end));
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}