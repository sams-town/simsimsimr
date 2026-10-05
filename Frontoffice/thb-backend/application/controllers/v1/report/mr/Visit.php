<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Visit extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        // Load Helper
        $this->load->helper("excel");
    }

    public function inpatient_get() {
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
            'NAMA DOKTER' => array(
                'width' => 40
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_sec = array(
            'NO' => array(
                'width' => 5
            ),
            'RAWAT INAP' => array(
                'width' => 20
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_third = array(
            'NO' => array(
                'width' => 10
            ),
            'USIA' => array(
                'width' => 15
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_fourth = array(
            'NO' => array(
                'width' => 10
            ),
            'JENIS KELAMIN' => array(
                'width' => 15
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_fifth = array(
            'NO' => array(
                'width' => 10
            ),
            'JENIS ASURANSI' => array(
                'width' => 30
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date_end'];

        $employee = $this->db->query("
            SELECT e.id, e.fullname, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '40f06534-07ec-4221-ad9d-dea08196079f'
            GROUP BY e.id
            ORDER BY e.name
        ")->result();

        $service_class = $this->db->query("
            SELECT sc.id, sc.name, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN bed b ON rd.bed_id = b.id
            INNER JOIN room r ON b.room_id = r.id
            INNER JOIN service_class sc ON r.service_class_id = sc.id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '40f06534-07ec-4221-ad9d-dea08196079f'
            GROUP BY sc.id
            ORDER BY sc.name
        ")->result();

        $age = $this->db->query("
            SELECT p.birth_date
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '40f06534-07ec-4221-ad9d-dea08196079f'
        ")->result();

        $sex = $this->db->query("
            SELECT p.sex, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '40f06534-07ec-4221-ad9d-dea08196079f'
            GROUP BY p.sex
        ")->result();

        $insurance = $this->db->query("
            SELECT c.id, c.name, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            LEFT JOIN company c ON ri.company_id = c.id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '40f06534-07ec-4221-ad9d-dea08196079f'
            GROUP BY c.id
            ORDER BY c.name
        ")->result();
        
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

        $column = 5;
        $column_sec = $column + 2;
        $row = 1;
        foreach ($headers_sec as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $column = 9;
        $column_third = $column + 2;
        $row = 1;
        foreach ($headers_third as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $column = 13;
        $column_fourth = $column + 2;
        $row = 1;
        foreach ($headers_fourth as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $column = 17;
        $column_fifth = $column + 2;
        $row = 1;
        foreach ($headers_fifth as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($employee); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);

            $employee_name = $employee[$i]->fullname;
            $total = $employee[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $employee_name);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
        }    

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($service_class); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('E'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('F'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('G'.($coordinate))->applyFromArray($style);

            $name = $service_class[$i]->name;
            $total = $service_class[$i]->total;

            $activeWorksheet->setCellValue('E' . $coordinate, $count);
            $activeWorksheet->setCellValue('F' . $coordinate, $name);
            $activeWorksheet->setCellValue('G' . $coordinate, $total);
        }

        $age_data = array(
            '<1' => 0,
            '1-4' => 0,
            '5-9' => 0,
            '10-14' => 0,
            '15-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '56-65' => 0,
            '66-69' => 0,
            '>70' => 0,
        );
        
        for ($i=0; $i < count($age); $i++) { 
            $birth_date = $age[$i]->birth_date;

            $age_count = date('Y') - date('Y', strtotime($birth_date));
            if (date('md') < date('md', strtotime($birth_date))) {
                $age_count--; // Adjust if the birthday hasn't occurred yet this year
            }

            if ($age_count < 1) {
                $age_data['<1'] += 1;
            } elseif ($age_count >= 1 && $age_count <= 4) {
                $age_data['1-4'] += 1;
            } elseif ($age_count >= 5 && $age_count <= 9) {
                $age_data['5-9'] += 1;
            } elseif ($age_count >= 10 && $age_count <= 14) {
                $age_data['10-14'] += 1;
            } elseif ($age_count >= 15 && $age_count <= 25) {
                $age_data['15-25'] += 1;
            } elseif ($age_count >= 26 && $age_count <= 35) {
                $age_data['26-35'] += 1;
            } elseif ($age_count >= 36 && $age_count <= 45) {
                $age_data['36-45'] += 1;
            } elseif ($age_count >= 46 && $age_count <= 55) {
                $age_data['46-55'] += 1;
            } elseif ($age_count >= 56 && $age_count <= 65) {
                $age_data['56-65'] += 1;
            } elseif ($age_count >= 66 && $age_count <= 69) {
                $age_data['66-69'] += 1;
            } elseif ($age_count >= 70) {
                $age_data['>70'] += 1;
            }
        }

        $coordinate = 1;
        $count = 0;
        foreach ($age_data as $key => $val) {
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('I'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('J'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('K'.$coordinate)->applyFromArray($style);

            $activeWorksheet->setCellValue('I' . $coordinate, $count);
            $activeWorksheet->setCellValue('J' . $coordinate, $key);
            $activeWorksheet->setCellValue('K' . $coordinate, $val);
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($sex); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('M'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('N'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('O'.($coordinate))->applyFromArray($style);

            $val = $sex[$i]->sex == 0 ? 'Perempuan' : 'Laki-laki';
            $total = $sex[$i]->total;

            $activeWorksheet->setCellValue('M' . $coordinate, $count);
            $activeWorksheet->setCellValue('N' . $coordinate, $val);
            $activeWorksheet->setCellValue('O' . $coordinate, $total);
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($insurance); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('Q'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('R'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('S'.($coordinate))->applyFromArray($style);

            $name = $insurance[$i]->name == '' ? 'Pasien Umum' : $insurance[$i]->name;
            $total = $insurance[$i]->total;

            $activeWorksheet->setCellValue('Q' . $coordinate, $count);
            $activeWorksheet->setCellValue('R' . $coordinate, $name);
            $activeWorksheet->setCellValue('S' . $coordinate, $total);
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_KUNJUNGAN_RAWAT_INAP_' . date('Ymd',strtotime($date_start)) . '_' . date('Ymd',strtotime($date_end));
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function outpatient_get() {
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
            'NAMA DOKTER' => array(
                'width' => 40
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_sec = array(
            'NO' => array(
                'width' => 5
            ),
            'POLIKLINIK' => array(
                'width' => 40
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_third = array(
            'NO' => array(
                'width' => 10
            ),
            'USIA' => array(
                'width' => 15
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_fourth = array(
            'NO' => array(
                'width' => 10
            ),
            'JENIS KELAMIN' => array(
                'width' => 15
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $headers_fifth = array(
            'NO' => array(
                'width' => 10
            ),
            'JENIS ASURANSI' => array(
                'width' => 30
            ),
            'JUMLAH' => array(
                'width' => 10
            )
        );

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date_end'];

        $employee = $this->db->query("
            SELECT e.id, e.fullname, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            GROUP BY e.id
            ORDER BY e.name
        ")->result();

        $health_facility_item = $this->db->query("
            SELECT hfi.id, hfi.name, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            GROUP BY hfi.id
            ORDER BY hfi.name
        ")->result();

        $age = $this->db->query("
            SELECT p.birth_date
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        ")->result();

        $sex = $this->db->query("
            SELECT p.sex, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            GROUP BY p.sex
        ")->result();

        $insurance = $this->db->query("
            SELECT c.id, c.name, COUNT(*) as total 
            FROM registration_destination rd
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            LEFT JOIN company c ON ri.company_id = c.id
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            WHERE rd.checkin_date >= '$date_start 00:00:00' AND rd.checkin_date <= '$date_end 23:59:59'
            AND rd.is_cancel = 0
            AND hf.id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
            GROUP BY c.id
            ORDER BY c.name
        ")->result();
        
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

        $column = 5;
        $column_sec = $column + 2;
        $row = 1;
        foreach ($headers_sec as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_sec,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $column = 9;
        $column_third = $column + 2;
        $row = 1;
        foreach ($headers_third as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_third,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $column = 13;
        $column_fourth = $column + 2;
        $row = 1;
        foreach ($headers_fourth as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fourth,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $column = 17;
        $column_fifth = $column + 2;
        $row = 1;
        foreach ($headers_fifth as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == 'width') {
                    $activeWorksheet->getColumnDimensionByColumn($column)->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->applyFromArray($style);
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->getAlignment()->setHorizontal('center');
            // bold text
            $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->getFont()->setBold(true);
            // color cell
            // $activeWorksheet->getStyleByColumnAndRow($column,$row,$column_fifth,$row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($employee); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);

            $employee_name = $employee[$i]->fullname;
            $total = $employee[$i]->total;

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $employee_name);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
        }    

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($health_facility_item); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('E'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('F'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('G'.($coordinate))->applyFromArray($style);

            $health_facility_name = $health_facility_item[$i]->name;
            $total = $health_facility_item[$i]->total;

            $activeWorksheet->setCellValue('E' . $coordinate, $count);
            $activeWorksheet->setCellValue('F' . $coordinate, $health_facility_name);
            $activeWorksheet->setCellValue('G' . $coordinate, $total);
        }

        $age_data = array(
            '<1' => 0,
            '1-4' => 0,
            '5-9' => 0,
            '10-14' => 0,
            '15-25' => 0,
            '26-35' => 0,
            '36-45' => 0,
            '46-55' => 0,
            '56-65' => 0,
            '66-69' => 0,
            '>70' => 0,
        );
        
        for ($i=0; $i < count($age); $i++) { 
            $birth_date = $age[$i]->birth_date;

            $age_count = date('Y') - date('Y', strtotime($birth_date));
            if (date('md') < date('md', strtotime($birth_date))) {
                $age_count--; // Adjust if the birthday hasn't occurred yet this year
            }

            if ($age_count < 1) {
                $age_data['<1'] += 1;
            } elseif ($age_count >= 1 && $age_count <= 4) {
                $age_data['1-4'] += 1;
            } elseif ($age_count >= 5 && $age_count <= 9) {
                $age_data['5-9'] += 1;
            } elseif ($age_count >= 10 && $age_count <= 14) {
                $age_data['10-14'] += 1;
            } elseif ($age_count >= 15 && $age_count <= 25) {
                $age_data['15-25'] += 1;
            } elseif ($age_count >= 26 && $age_count <= 35) {
                $age_data['26-35'] += 1;
            } elseif ($age_count >= 36 && $age_count <= 45) {
                $age_data['36-45'] += 1;
            } elseif ($age_count >= 46 && $age_count <= 55) {
                $age_data['46-55'] += 1;
            } elseif ($age_count >= 56 && $age_count <= 65) {
                $age_data['56-65'] += 1;
            } elseif ($age_count >= 66 && $age_count <= 69) {
                $age_data['66-69'] += 1;
            } elseif ($age_count >= 70) {
                $age_data['>70'] += 1;
            }
        }

        $coordinate = 1;
        $count = 0;
        foreach ($age_data as $key => $val) {
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('I'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('J'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('K'.$coordinate)->applyFromArray($style);

            $activeWorksheet->setCellValue('I' . $coordinate, $count);
            $activeWorksheet->setCellValue('J' . $coordinate, $key);
            $activeWorksheet->setCellValue('K' . $coordinate, $val);
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($sex); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('M'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('N'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('O'.($coordinate))->applyFromArray($style);

            $val = $sex[$i]->sex == 0 ? 'Perempuan' : 'Laki-laki';
            $total = $sex[$i]->total;

            $activeWorksheet->setCellValue('M' . $coordinate, $count);
            $activeWorksheet->setCellValue('N' . $coordinate, $val);
            $activeWorksheet->setCellValue('O' . $coordinate, $total);
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($insurance); $i++) { 
            $coordinate += 1;
            $count += 1;

            $activeWorksheet->getstyle('Q'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('R'.($coordinate))->applyFromArray($style);
            $activeWorksheet->getstyle('S'.($coordinate))->applyFromArray($style);

            $name = $insurance[$i]->name == '' ? 'Pasien Umum' : $insurance[$i]->name;
            $total = $insurance[$i]->total;

            $activeWorksheet->setCellValue('Q' . $coordinate, $count);
            $activeWorksheet->setCellValue('R' . $coordinate, $name);
            $activeWorksheet->setCellValue('S' . $coordinate, $total);
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_KUNJUNGAN_RAWAT_JALAN_' . date('Ymd',strtotime($date_start)) . '_' . date('Ymd',strtotime($date_end));
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}