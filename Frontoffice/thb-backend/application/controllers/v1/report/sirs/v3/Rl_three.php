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

    public function rl32_get() {
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
            'JENIS PELAYANAN' => array(
                'width' => 35
            ),
            'JML PASIEN AWAL' => array(
                'width' => 10
            ),
            'PASIEN PRIA MATI' => array(
                'width' => 13
            ),
            'PASIEN WANITA MATI' => array(
                'width' => 13
            ),
            'JML LAMA DIRAWAT (hari)' => array(
                'width' => 13
            ),
            'PRESIDENT SUITE' => array(
                'width' => 10
            ),
            'VVIP' => array(
                'width' => 10
            ),
            'VIP' => array(
                'width' => 10
            ),
            '1' => array(
                'width' => 10
            ),
            '2' => array(
                'width' => 10
            ),
            '3' => array(
                'width' => 10
            ),
            'ISOLASI' => array(
                'width' => 10
            ),
            'INTENSIVE CARE' => array(
                'width' => 10
            ),
            'JML ALOKASI TT' => array(
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
            // word wrap
            $activeWorksheet->getStyleByColumnAndRow($column,$row,count($headers),$row)->getAlignment()->setWrapText(true);
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

        $health_facility_item = $this->db->query("
            SELECT hfi.name
            FROM health_facility_item hfi
            WHERE hfi.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
            AND hfi.is_delete = 0
            ORDER BY hfi.name
        ")->result();

        $health_facility_item_data = array();

        for ($i=0; $i < count($health_facility_item); $i++) { 
            $name = $health_facility_item[$i]->name;
            $health_facility_item_data[$name] = array(
                'class_3' => 0,
                'class_2' => 0,
                'class_1' => 0,
                'vip' => 0,
                'vvip' => 0,
                'president_suite' => 0,
                'isolasi' => 0,
                'intensive_care' => 0,
                'total' => 0,
                'total_los' => 0,
                'total_bed' => 0,
                'total_men_death' => 0,
                'total_women_death' => 0,
            );
        }

        $bed_count = $this->db->query("
            SELECT hfi.name, COUNT(*) AS total 
            FROM bed b 
            INNER JOIN room r ON b.room_id = r.id
            INNER JOIN health_facility_item hfi ON r.health_facility_item_id = hfi.id 
            WHERE b.is_delete = 0
            AND r.is_delete = 0
            AND hfi.is_delete = 0
            GROUP BY hfi.id
            ORDER BY hfi.name        
        ")->result();

        for ($i=0; $i < count($bed_count); $i++) { 
            $name = $bed_count[$i]->name;
            $total = $bed_count[$i]->total;

            $health_facility_item_data[$name]['total_bed'] = $total;
        }

        $registration_destination = $this->db->query("
            SELECT hfi.name, rd.id, rd.checkin_date, rd.checkout_date, rd.exit_id, p.sex
            FROM registration_destination rd 
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            WHERE rd.checkin_date >= '$date_start 00:00:00'
            AND rd.checkout_date <= '$date_end 23:59:59'
            AND hfi.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
            AND hfi.is_delete = 0
            AND rd.is_cancel = 0
            ORDER BY hfi.name
        ")->result();

        for ($i=0; $i < count($registration_destination); $i++) { 
            $name = $registration_destination[$i]->name;
            $checkin_date = $registration_destination[$i]->checkin_date;
            $checkout_date = $registration_destination[$i]->checkout_date;
            $exit_id = $registration_destination[$i]->exit_id;
            $sex = $registration_destination[$i]->sex;

            $checkin_time = new DateTime($checkin_date);
            $checkout_time = new DateTime($checkout_date);
            $diff_time = $checkout_time->diff($checkin_time);
            $day = $diff_time->d;
            
            $health_facility_item_data[$name]['total_los'] += (int) $day;

            if ($exit_id == '4a6d28c4-e020-425f-91e1-d39699cf679b') {
                if ($sex == 0) {
                    $health_facility_item_data[$name]['total_women_death'] += 1;
                } elseif ($sex == 1) {
                    $health_facility_item_data[$name]['total_men_death'] += 1;
                }
            }
        }

        $inpatient = $this->db->query("
            SELECT hfi.name, sc.id as service_class_id, COUNT(rd.id) as total
            FROM health_facility_item hfi 
            LEFT JOIN registration_destination rd ON hfi.id = rd.health_facility_item_id 
            INNER JOIN bed b ON rd.bed_id = b.id
            INNER JOIN room r ON b.room_id = r.id
            INNER JOIN service_class sc ON r.service_class_id = sc.id
            WHERE rd.checkin_date >= '$date_start 00:00:00'
            AND rd.checkout_date <= '$date_end 23:59:59'
            AND hfi.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
            AND hfi.is_delete = 0
            AND rd.is_cancel = 0
            GROUP BY hfi.id, sc.id
            ORDER BY hfi.name
        ")->result();

        for ($i=0; $i < count($inpatient); $i++) { 
            $name = $inpatient[$i]->name;
            $service_class_id = $inpatient[$i]->service_class_id;
            $total = $inpatient[$i]->total;

            if ($service_class_id == 'd168732a-fdef-456d-836c-750e003187e0') {
                $health_facility_item_data[$name]['class_3'] += $total;
            } elseif ($service_class_id == 'f39fbb8f-082e-4c71-8802-92943b6e4683') {
                $health_facility_item_data[$name]['class_2'] += $total;
            } elseif ($service_class_id == '8d3b8d25-4606-4db5-9ec1-59ec22514210') {
                $health_facility_item_data[$name]['class_1'] += $total;
            } elseif ($service_class_id == '52d76ebf-aa04-4308-a1a1-61e325333787') {
                $health_facility_item_data[$name]['vip'] += $total;
            } elseif ($service_class_id == '8b252126-de37-4a74-b303-2b9da5e447bf') {
                $health_facility_item_data[$name]['vvip'] += $total;
            } elseif ($service_class_id == 'b1c28ddc-18bd-4e3d-aa94-7ededcc9d151') {
                $health_facility_item_data[$name]['president_suite'] += $total;
            } elseif ($service_class_id == '7560e211-15ed-4113-bd9a-943a1130d306') {
                $health_facility_item_data[$name]['isolasi'] += $total;
            } elseif ($service_class_id == '01a37f8e-2baf-4057-b8fb-7a825cb12e19') {
                $health_facility_item_data[$name]['intensive_care'] += $total;
            } 

            $health_facility_item_data[$name]['total'] += $total;
        }

        $coordinate = 1;
        $count = 0;
        foreach ($health_facility_item_data as $key => $val) {
            $coordinate += 1;
            $count += 1;

            $class_3 = $val['class_3'];
            $class_2 = $val['class_2'];
            $class_1 = $val['class_1'];
            $vip = $val['vip'];
            $vvip = $val['vvip'];
            $president_suite = $val['president_suite'];
            $isolasi = $val['isolasi'];
            $intensive_care = $val['intensive_care'];
            $total = $val['total'];
            $total_los = $val['total_los'];
            $total_bed = $val['total_bed'];
            $total_men_death = $val['total_men_death'];
            $total_women_death = $val['total_women_death'];

            $activeWorksheet->getstyle('A'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('B'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('C'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('D'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('E'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('F'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('G'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('H'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('I'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('J'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('K'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('L'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('M'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('N'.$coordinate)->applyFromArray($style);
            $activeWorksheet->getstyle('O'.$coordinate)->applyFromArray($style);

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $key);
            $activeWorksheet->setCellValue('C' . $coordinate, $total);
            $activeWorksheet->setCellValue('D' . $coordinate, $total_men_death);
            $activeWorksheet->setCellValue('E' . $coordinate, $total_women_death);
            $activeWorksheet->setCellValue('F' . $coordinate, $total_los);
            $activeWorksheet->setCellValue('G' . $coordinate, $president_suite);
            $activeWorksheet->setCellValue('H' . $coordinate, $vvip);
            $activeWorksheet->setCellValue('I' . $coordinate, $vip);
            $activeWorksheet->setCellValue('J' . $coordinate, $class_1);
            $activeWorksheet->setCellValue('K' . $coordinate, $class_2);
            $activeWorksheet->setCellValue('L' . $coordinate, $class_3);
            $activeWorksheet->setCellValue('M' . $coordinate, $isolasi);
            $activeWorksheet->setCellValue('N' . $coordinate, $intensive_care);
            $activeWorksheet->setCellValue('O' . $coordinate, $total_bed);
        }

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_32_RAWAT_INAP_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}