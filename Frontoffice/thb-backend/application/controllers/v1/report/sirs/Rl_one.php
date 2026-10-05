<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Rl_one extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/health_facility/Bed_model", "Bed");
        // Load Helper
        $this->load->helper("excel");
    }

    public function rl13_get() {
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
            'RUANGAN' => array(
                'width' => 25
            ),
            'JUMLAH' => array(
                'width' => 15
            ),
            'PRESIDENT SUITE' => array(
                'width' => 15
            ),
            'VVIP' => array(
                'width' => 15
            ),
            'VIP' => array(
                'width' => 15
            ),
            '1' => array(
                'width' => 15
            ),
            '2' => array(
                'width' => 15
            ),
            '3' => array(
                'width' => 15
            ),
            'INTENSIF CARE' => array(
                'width' => 15
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

        $output = array();

        $room = $this->db->query("
            SELECT r.id, sc.name as service_class_name, h.name as type_room_name
            FROM room r
            INNER JOIN helper h ON r.type_room_id = h.code AND h.variable = 'type_room'
            INNER JOIN service_class sc ON r.service_class_id = sc.id
            WHERE r.is_delete = 0
            ORDER BY h.name
        ")->result();

        for ($i=0; $i < count($room); $i++) { 
            $room_id = $room[$i]->id;
            $service_class_name = $room[$i]->service_class_name;
            $type_room_name = $room[$i]->type_room_name;
            
            $data = array(
                'name' => $type_room_name,
                'bed_count' => '0',
                'classes' => array(
                    '1' => 0,
                    '2' => 0,
                    '3' => 0,
                    'Intensif Care' => 0,
                    'VIP' => 0,
                    'VVIP' => 0,
                    'President Suit' => 0,
                ),
            );

            if (count($output) == 0) {
                array_push($output, $data);
            }

            $exist = 0;
            for ($j=0; $j < count($output); $j++) { 
                if (in_array($type_room_name, $output[$j]) == 1) {
                    $exist = 1;
                }
            }
            if ($exist == 0) {
                array_push($output, $data);
            }

            for ($j=0; $j < count($output); $j++) { 
                if ($output[$j]['name'] == $type_room_name) {
                    $select = 'a.id, a.room_id, a.is_delete';
                    $where = array(
                        'a.is_delete' => 0,
                        'a.room_id' => $room_id,
                    );
                    $bed = $this->Bed->select(0, $select, $where);
                    
                    $bed_count = count($bed);

                    $output[$j]['bed_count'] = intval($output[$j]['bed_count']) + $bed_count;
                    $output[$j]['classes'][$service_class_name] = $output[$j]['classes'][$service_class_name] + $bed_count;
                }
            }
        }

        $coordinate = 1;
        $count = 0;
        for ($i=0; $i < count($output); $i++) { 
            $coordinate += 1;
            $count += 1;

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

            $name = $output[$i]['name'];
            $bed_count = $output[$i]['bed_count'];
            $classes = $output[$i]['classes'];

            $activeWorksheet->setCellValue('A' . $coordinate, $count);
            $activeWorksheet->setCellValue('B' . $coordinate, $name);
            $activeWorksheet->setCellValue('C' . $coordinate, $bed_count);
            $activeWorksheet->setCellValue('D' . $coordinate, $classes['President Suit']);
            $activeWorksheet->setCellValue('E' . $coordinate, $classes['VVIP']);
            $activeWorksheet->setCellValue('F' . $coordinate, $classes['VIP']);
            $activeWorksheet->setCellValue('G' . $coordinate, $classes['1']);
            $activeWorksheet->setCellValue('H' . $coordinate, $classes['2']);
            $activeWorksheet->setCellValue('I' . $coordinate, $classes['3']);
            $activeWorksheet->setCellValue('J' . $coordinate, $classes['Intensif Care']);
        }  

        $writer = new Xlsx($spreadsheet);
        
        $filename = 'LAPORAN_RL_13_TEMPAT_TIDUR_' . date('Ymd');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}