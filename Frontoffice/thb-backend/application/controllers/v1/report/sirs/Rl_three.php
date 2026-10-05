<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Rl_three extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        // Load Helper
        $this->load->helper("excel");
    }

    public function rl33_get() {
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
            'TINDAKAN' => array(
                'width' => 35
            ),
            'KELAS' => array(
                'width' => 10
            ),
            'JUMLAH' => array(
                'width' => 10
            ),
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

        $tr_doctor = $this->db->query("
            SELECT c.name as costing_name, sc.name as service_class_name, COUNT(*) as total 
            FROM transaction_doctor td
            INNER JOIN soap s ON td.soap_id = s.id
            INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
            INNER JOIN costing_services_class csc ON td.costing_id = csc.id
            INNER JOIN costing c ON csc.costing_id = c.id
            INNER JOIN service_class sc ON csc.service_class_id = sc.id
            WHERE td.is_delete = 0 
            AND td.date_added >= '$date_start 00:00:00'
            AND td.date_added <= '$date_end 23:59:59'
            AND s.is_delete = 0 
            AND rd.health_facility_item_id = '38b10a7b-7006-451b-aa30-c11fa98635e0'
            GROUP BY td.costing_id
            ORDER BY c.name
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($tr_doctor); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('D'.$coordinate)->applyFromArray($style);

            $name = $tr_doctor[$i]->costing_name;
            $service_class_name = $tr_doctor[$i]->service_class_name;
            $total = $tr_doctor[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $service_class_name);
            $activeWorksheet->setCellValue('D' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_33_GIGI_DAN_MULUT_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl34_get() {
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
            'TINDAKAN' => array(
                'width' => 35
            ),
            'KELAS' => array(
                'width' => 10
            ),
            'JUMLAH' => array(
                'width' => 10
            ),
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

        $tr_doctor = $this->db->query("
            SELECT td.costing_name, sc.name as service_class_name, COUNT(*) as total 
            FROM transaction_doctor td
            INNER JOIN soap s ON td.soap_id = s.id
            INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
            INNER JOIN costing_services_class csc ON td.costing_id = csc.id
            INNER JOIN service_class sc ON csc.service_class_id = sc.id
            WHERE td.is_delete = 0 
            AND td.date_added >= '$date_start 00:00:00'
            AND td.date_added <= '$date_end 23:59:59'
            AND td.costing_name LIKE '%caesarea%' 
            OR td.costing_name LIKE '%partus%' 
            AND s.is_delete = 0 
            AND rd.health_facility_item_id = '3905d3e8-26a3-4166-965a-a91b5edad463'
            GROUP BY td.costing_id
            ORDER By td.costing_name
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($tr_doctor); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('D'.$coordinate)->applyFromArray($style);

            $name = $tr_doctor[$i]->costing_name;
            $service_class_name = $tr_doctor[$i]->service_class_name;
            $total = $tr_doctor[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $service_class_name);
            $activeWorksheet->setCellValue('D' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_34_KEBIDANAN_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl37_get() {
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
            'PAKET' => array(
                'width' => 25
            ),
            'JUMLAH' => array(
                'width' => 15
            ),
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

        $package = $this->db->query("
            SELECT rp.name, COUNT(rri.id) as total 
            FROM radiology_package rp 
            LEFT JOIN radiology_request_item rri ON rri.radiology_package_id = rp.id 
            AND rri.is_delete = 0 
            AND rri.date_request >= '$date_start 00:00:00' 
            AND rri.date_request <= '$date_end 23:59:59'
            WHERE rp.is_delete = 0
            GROUP BY rp.id
            ORDER BY total DESC
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($package); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);

            $name = $package[$i]->name;
            $total = $package[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_37_RADIOLOGI_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl38_get() {
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
            'PAKET' => array(
                'width' => 25
            ),
            'JUMLAH' => array(
                'width' => 15
            ),
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

        $package = $this->db->query("
            SELECT rp.name, COUNT(lri.id) as total 
            FROM laboratory_package rp 
            LEFT JOIN laboratory_request_item lri ON lri.laboratory_package_id = rp.id 
            AND lri.is_delete = 0 
            AND lri.date_request >= '$date_start 00:00:00' 
            AND lri.date_request <= '$date_end 23:59:59'
            WHERE rp.is_delete = 0
            GROUP BY rp.id
            ORDER BY total DESC
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($package); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);

            $name = $package[$i]->name;
            $total = $package[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_38_LABORATORIUM_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl39_get() {
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
            'PAKET' => array(
                'width' => 25
            ),
            'JUMLAH' => array(
                'width' => 15
            ),
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

        $package = $this->db->query("
            SELECT rp.name, COUNT(pri.id) as total 
            FROM physiotherapy_package rp 
            LEFT JOIN physiotherapy_request_item pri ON pri.physiotherapy_package_id = rp.id 
            AND pri.is_delete = 0 
            AND pri.date_request >= '$date_start 00:00:00' 
            AND pri.date_request <= '$date_end 23:59:59'
            WHERE rp.is_delete = 0
            GROUP BY rp.id
            ORDER BY total DESC
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($package); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);

            $name = $package[$i]->name;
            $total = $package[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_39_REHABILITASI_MEDIK_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl311_get() {
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
            'TINDAKAN' => array(
                'width' => 35
            ),
            'KELAS' => array(
                'width' => 10
            ),
            'JUMLAH' => array(
                'width' => 10
            ),
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

        $tr_doctor = $this->db->query("
            SELECT c.name as costing_name, sc.name as service_class_name, COUNT(*) as total 
            FROM transaction_doctor td
            INNER JOIN soap s ON td.soap_id = s.id
            INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
            INNER JOIN costing_services_class csc ON td.costing_id = csc.id
            INNER JOIN costing c ON csc.costing_id = c.id
            INNER JOIN service_class sc ON csc.service_class_id = sc.id
            WHERE td.is_delete = 0 
            AND td.date_added >= '$date_start 00:00:00'
            AND td.date_added <= '$date_end 23:59:59'
            AND s.is_delete = 0 
            AND rd.health_facility_item_id = '246f50f0-d6b1-40be-8602-af11e105c9c4'
            GROUP BY td.costing_id
            ORDER By c.name
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($tr_doctor); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('D'.$coordinate)->applyFromArray($style);

            $name = $tr_doctor[$i]->costing_name;
            $service_class_name = $tr_doctor[$i]->service_class_name;
            $total = $tr_doctor[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $service_class_name);
            $activeWorksheet->setCellValue('D' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_311_KESEHATAN_JIWA_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl312_get() {
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
            'NAMA' => array(
                'width' => 40
            ),
            'JUMLAH' => array(
                'width' => 10
            ),
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
            AND i.code LIKE 'Z30%'
            AND rd.health_facility_item_id = '0fd51271-a0e5-4db2-bc6e-12064508e82b'
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
        
        $filename = 'LAPORAN_RL_312_KELUARGA_BERENCANA_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function rl315_get() {
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
            'CARA BAYAR' => array(
                'width' => 25
            ),
            'JUMLAH PASIEN RAWAT JALAN' => array(
                'width' => 35
            ),
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

        $insurance = $this->db->query("
            SELECT ri.company_id, c.name, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            LEFT JOIN company c ON ri.company_id = c.id
            WHERE rd.is_cancel = 0
            AND rd.checkin_date >= '$date_start 00:00:00'
            AND rd.checkin_date <= '$date_end 23:59:59'
            AND hfi.health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            GROUP BY ri.company_id
            ORDER BY total DESC
        ")->result();

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($insurance); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);

            $name = $insurance[$i]->name == null ? 'Tunai' : $insurance[$i]->name;
            $total = $insurance[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_315_CARA_BAYAR_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}