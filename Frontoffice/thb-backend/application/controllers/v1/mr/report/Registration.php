<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Registration extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        $this->load->library('Vclaim_sep','vclaim_sep');
        // Load Model
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_Destination");
        $this->load->model("v1/mr/inpatient/Vital_signs_monitoring_model", "Vital_signs_monitoring");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
        $this->load->model("v1/health_facility/Laboratory_request_model", "Laboratory_request");
        $this->load->model("v1/health_facility/Radiology_request_model", "Radiology_request");
        // Load Helper
        $this->load->helper("excel");
    }

    public function export_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        

        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->Registration->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->Registration->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->Registration->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            $where['rd.is_checkout'] = $is_checkout;
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        // if(isset($q_strings['insurance_id'])) {
        //     $where['ri.insurance_id'] = $q_strings['insurance_id'];
        //     unset($where['insurance_id']);
        // }

        // $where['ri.insurance_id !='] = '2';
        $where['ri.company_id !='] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';

        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
    
            $select = 'r.id';
            $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->Registration->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
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
            'No. Registrasi' => array(
                'width' => 15
            ),
            'Tgl. Registrasi' => array(
                'width' => 15
            ),
            'Tgl. Checkout' => array(
                'width' => 15
            ),
            'No. RM' => array(
                'width' => 15
            ),
            'Penjamin' => array(
                'width' => 15
            ),
            'No. SEP' => array(
                'width' => 15
            ),
            'Nama' => array(
                'width' => 15
            ),
            'Kelamin' => array(
                'width' => 15
            ),
            'Tanggal' => array(
                'width' => 15
            ),
            'Unit' => array(
                'width' => 15
            ),
            'Poliklinik/Ruangan' => array(
                'width' => 15
            ),
            'DPJP' => array(
                'width' => 15
            ),
            'DIAGNOSA' => array(
                'width' => 15
            ),
            'NO TELEPON' => array(
                'width' => 15
            )
        );

        $where['a.is_cancel'] = 0;
    
        $select = 'a.id, a.code_booking,i.code as icd_code, s.id as soap_id,p.phone, i.name as icd_name, rd.id as registration_destination_id, ri.is_cob, ri.sep, ri.insurance_id, ri.number_insurance, rd.code_queue, rd.checkin_date as register_date, rd.checkin_date, rd.exit_id, ex.name as exit_name, rd.sts, rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7, a.code, a.how_to_enter_id, hte.name as how_to_enter_name, h5.name as type_visit_name , h2.name as how_to_pay, c.name as insurance_name, a.type_register_id, h.name as type_register_name, a.norm, p.name, h4.name as alias, p.is_new, p.birth_date, h3.name as sex_name, hf.id as health_facility_id, hf.name as health_facility_name, rd.health_facility_item_id, hfi.name as health_facility_item_name, rd.doctor_in_charge_id, b.id as bed_id, b.name as bed_name, e.fullname as doctor_in_charge_name, e.bride_bpjs, rd.queue';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', "a.type_register_id = h.code and h.variable='type_register'", 'INNER'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('helper h4', "p.alias = h4.code and h4.variable='alias'", 'INNER'),
            array('helper h5', "a.type_visit_id = h5.code and h5.variable='type_visit'", 'LEFT'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            array('soap_a_action saa', "sa.id = saa.soap_a_id AND saa.index=0", 'INNER'),
            array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );

        $startTime = microtime(true);

        Logger::info($this->debug_uniq, "|---- start export registration ----|");

        $order = array('rd.register_date' => 'desc');
        $this->db->group_by(array("s.id"));
        $registration = $this->Registration->select(0, $select, $where, $join, $order);
        
        Logger::info($this->debug_uniq, 'total rows: ' . count($registration));

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

        $count = 0;
        for ($i=0; $i < count($registration); $i++) { 
            $count += 1;

            $activeWorksheet->getstyle('A1')->applyFromArray($style);
            $activeWorksheet->getstyle('B1')->applyFromArray($style);
            $activeWorksheet->getstyle('C1')->applyFromArray($style);
            $activeWorksheet->getstyle('D1')->applyFromArray($style);
            $activeWorksheet->getstyle('E1')->applyFromArray($style);
            $activeWorksheet->getstyle('F1')->applyFromArray($style);
            $activeWorksheet->getstyle('G1')->applyFromArray($style);
            $activeWorksheet->getstyle('H1')->applyFromArray($style);
            $activeWorksheet->getstyle('I1')->applyFromArray($style);
            $activeWorksheet->getstyle('J1')->applyFromArray($style);
            $activeWorksheet->getstyle('K1')->applyFromArray($style);
            $activeWorksheet->getstyle('L1')->applyFromArray($style);
            $activeWorksheet->getstyle('M1')->applyFromArray($style);
            $activeWorksheet->getstyle('N1')->applyFromArray($style);

            $code_registration = $registration[$i]->code;
            $checkin_date = $registration[$i]->checkin_date;
            $checkout_date = $registration[$i]->checkout_date;
            $norm = $registration[$i]->norm;
            $insurance_id = $registration[$i]->insurance_id;
            $insurance_name = $registration[$i]->insurance_name;
            $sep = $registration[$i]->sep;
            $name = $registration[$i]->name;
            $sex_name = $registration[$i]->sex_name;
            $birth_date = $registration[$i]->birth_date;
            $health_facility_name = $registration[$i]->health_facility_name;
            $health_facility_item_name = $registration[$i]->health_facility_item_name;
            $doctor_name = $registration[$i]->doctor_in_charge_name;
            $icd_code = $registration[$i]->icd_code;
            $icd_name = $registration[$i]->icd_name;
            $phone = $registration[$i]->phone;

            $coordinate = $i + 2;
            $activeWorksheet->setCellValue('A' . $coordinate, $code_registration);
            $activeWorksheet->setCellValue('B' . $coordinate, date('Y-m-d', strtotime($checkin_date)));
            $activeWorksheet->setCellValue('C' . $coordinate, ($checkout_date != null) ? date('Y-m-d', strtotime($checkout_date)) : date('Y-m-d', strtotime($checkin_date)));
            $activeWorksheet->setCellValue('D' . $coordinate, $norm);
            $activeWorksheet->setCellValue('E' . $coordinate, ($insurance_id != 1) ? $insurance_name : 'Pribadi');
            $activeWorksheet->setCellValue('F' . $coordinate, $sep);
            $activeWorksheet->setCellValue('G' . $coordinate, $name);
            $activeWorksheet->setCellValue('H' . $coordinate, $sex_name);
            $activeWorksheet->setCellValue('I' . $coordinate, $birth_date);
            $activeWorksheet->setCellValue('J' . $coordinate, $health_facility_name);
            $activeWorksheet->setCellValue('K' . $coordinate, $health_facility_item_name);
            $activeWorksheet->setCellValue('L' . $coordinate, $doctor_name);
            $activeWorksheet->setCellValue('L' . $coordinate, $doctor_name);
            $activeWorksheet->setCellValue('M' . $coordinate, $icd_code.'-'.$icd_name);
            $activeWorksheet->setCellValue('N' . $coordinate, "'".$phone);

            if ($count % 1000 == 0) {
                Logger::info($this->debug_uniq, 'current processed rows: ' . $count);
            }
        }

        $writer = new Xlsx($spreadsheet);

        $filename = $this->name . '_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        Logger::info($this->debug_uniq, "|---- finish export registration ----|");
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedTime = number_format($executionTime, 3, '.', '');
        Logger::info($this->debug_uniq, "execution time: " . $formattedTime . " seconds");

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function export_refer_internal_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "POLI R.INTERNAL"); 
        $sheet->setCellValue('J3', "DPJP R.INTERNAL "); 
        $sheet->setCellValue('K3', "SEP"); 
        // $sheet->setCellValue('L3', "DPJP5"); 
        // $sheet->setCellValue('M3', "DIAGNOSA"); 
        // $sheet->setCellValue('N3', "PENJAMIN"); 
        // $sheet->setCellValue('O3', "JENIS KELAMIN"); 
        // $sheet->setCellValue('P3', "TANGGAL LAHIR"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        $no=1;
        
        
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                $where['rd.is_checkout'] = 0;
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
    
            if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $where_doc = array('a.is_checkout' => 0);
            } else {
                $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
            }
            $select = 'r.id';
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }

        $where['a.is_cancel'] = 0;
        $select = 'a.norm, rd.checkin_date as register_date, p.idcard, p.name, p.address, p.phone, p.sex, p.birth_date, e.fullname as doctor_in_charge_name, e_internal.fullname as doctor_in_charge_twoo, 
        hfi.name as health_facility_item_name,hfi_two.name as health_facility_item_name_twoo,ri.sep';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('refer_internal internal', 'rd.id = internal.registration_destination_id', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility_item hfi_two', 'internal.health_facility_item_id = hfi_two.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('employee e_internal', 'internal.doctor_in_charge_id = e_internal.id', 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        for ($i=0; $i < count($output); $i++) {
            $sheet->setCellValue('A'.$numrow, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('B'.$numrow, "'".$output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, "'".$output[$i]->idcard);
            $sheet->setCellValue('D'.$numrow, $output[$i]->name);
            $sheet->setCellValue('E'.$numrow, $output[$i]->address);
            $sheet->setCellValue('F'.$numrow, $output[$i]->phone);
            $sheet->setCellValue('G'.$numrow, $output[$i]->health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $output[$i]->health_facility_item_name_twoo);
            $sheet->setCellValue('J'.$numrow, $output[$i]->doctor_in_charge_twoo);
            $sheet->setCellValue('K'.$numrow, $output[$i]->sep);
            // $sheet->setCellValue('L'.$numrow, '');
            // $sheet->setCellValue('M'.$numrow, '');
            // $sheet->setCellValue('N'.$numrow, '$output[$i]->how_to_pay.'-'.$output[$i]->insurance_name');
            // $sheet->setCellValue('O'.$numrow, $output[$i]->birth_date);
            // $sheet->setCellValue('P'.$numrow, $output[$i]->sex);
            $numrow_detail = $numrow+1;
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function req_mar_1_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "DESA/KELURAHAN"); 
        $sheet->setCellValue('J3', "JENIS KELAMIN"); 
        $sheet->setCellValue('K3', "TGL.LAHIR"); 
        $sheet->setCellValue('L3', "USIA"); 
        $sheet->setCellValue('M3', "DIAGNOSA"); 
        $sheet->setCellValue('N3', "NO ASURANSI"); 
        $sheet->setCellValue('O3', "LAMA/BARU"); 
        // $sheet->setCellValue('P3', "TANGGAL LAHIR"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        $no=1;
        
        
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                $where['rd.is_checkout'] = 0;
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
    
            if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $where_doc = array('a.is_checkout' => 0);
            } else {
                $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
            }
            $select = 'r.id';
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }
        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051
        $where['a.is_cancel'] = 0;
        $where['p.birth_date >='] = '1910-10-29';
        $where['p.birth_date <='] = '1965-10-29';
        // $this->$model->whereInFields['p.address_id'] = array('2171051004','2171051002','2171051003','2171051006','2171051001','2171050003');
        $select = 'a.norm, rd.checkin_date as register_date, p.idcard, p.name, p.address, p.phone, p.sex, p.birth_date, e.fullname as doctor_in_charge_name, av.nm_village as village_name, h3.name as sex_name,
        i.code as icd_code, s.id as soap_id, i.name as icd_name,h2.name as how_to_pay, c.name as insurance_name, rd.is_new,
        hfi.name as health_facility_item_name,ri.sep,ri.number_insurance';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('village av', 'p.address_id = av.id_village', 'LEFT'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            array('soap_a_action saa', "sa.id = saa.soap_a_id", 'INNER'),
            array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);
        for ($i=0; $i < count($output); $i++) {
            $age = (date('Y') - date('Y', strtotime($output[$i]->birth_date))); // Get the current year and the birth year
            if (date('md', strtotime($output[$i]->birth_date)) > date('md')) { $age--; } // Check if the birthday has occurred this year or not (to adjust the age)
            $sheet->setCellValue('A'.$numrow, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('B'.$numrow, "'".$output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, "'".$output[$i]->idcard);
            $sheet->setCellValue('D'.$numrow, $output[$i]->name);
            $sheet->setCellValue('E'.$numrow, $output[$i]->address);
            $sheet->setCellValue('F'.$numrow, $output[$i]->phone);
            $sheet->setCellValue('G'.$numrow, $output[$i]->health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $output[$i]->village_name);
            $sheet->setCellValue('J'.$numrow, $output[$i]->sex_name);
            $sheet->setCellValue('K'.$numrow, $output[$i]->birth_date);
            $sheet->setCellValue('L'.$numrow, $age);
            $sheet->setCellValue('M'.$numrow, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
            // $sheet->setCellValue('N'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            $sheet->setCellValue('N'.$numrow, "'".$output[$i]->number_insurance);
            $sheet->setCellValue('O'.$numrow, $output[$i]->is_new == 'N' ? 'Lama' : 'Baru');
            // $sheet->setCellValue('P'.$numrow, $output[$i]->sex);
            $numrow_detail = $numrow+1;
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function req_mar_how_to_enter_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "DESA/KELURAHAN"); 
        $sheet->setCellValue('J3', "JENIS KELAMIN"); 
        $sheet->setCellValue('K3', "TGL.LAHIR"); 
        $sheet->setCellValue('L3', "USIA"); 
        $sheet->setCellValue('M3', "DIAGNOSA"); 
        $sheet->setCellValue('N3', "PENJAMIN"); 
        $sheet->setCellValue('O3', "LAMA/BARU"); 
        $sheet->setCellValue('P3', "FASKES PERUJUK"); 
        // $sheet->setCellValue('P3', "TANGGAL LAHIR"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        $no=1;
        
        
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                $where['rd.is_checkout'] = 0;
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
    
            if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $where_doc = array('a.is_checkout' => 0);
            } else {
                $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
            }
            $select = 'r.id';
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }
        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051
        $where['a.is_cancel'] = 0;
        // $this->$model->whereInFields['p.address_id'] = array('2171051004','2171051002','2171051003','2171051006','2171051001','2171050003');
        $select = 'a.norm, rd.checkin_date as register_date, p.idcard, p.name, p.address, p.phone, p.sex, p.birth_date, e.fullname as doctor_in_charge_name, av.nm_village as village_name, h3.name as sex_name,
        i.code as icd_code, s.id as soap_id, i.name as icd_name,h2.name as how_to_pay, c.name as insurance_name, rd.is_new, htw.name as how_to_enter_name,
        hfi.name as health_facility_item_name,ri.sep';
        $join = array(
            array('how_to_enter htw', 'a.how_to_enter_id = htw.id', 'INNER'),
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('village av', 'p.address_id = av.id_village', 'LEFT'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            array('soap_a_action saa', "sa.id = saa.soap_a_id", 'INNER'),
            array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);
        for ($i=0; $i < count($output); $i++) {
            $age = (date('Y') - date('Y', strtotime($output[$i]->birth_date))); // Get the current year and the birth year
            if (date('md', strtotime($output[$i]->birth_date)) > date('md')) { $age--; } // Check if the birthday has occurred this year or not (to adjust the age)
            $sheet->setCellValue('A'.$numrow, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('B'.$numrow, "'".$output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, "'".$output[$i]->idcard);
            $sheet->setCellValue('D'.$numrow, $output[$i]->name);
            $sheet->setCellValue('E'.$numrow, $output[$i]->address);
            $sheet->setCellValue('F'.$numrow, $output[$i]->phone);
            $sheet->setCellValue('G'.$numrow, $output[$i]->health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $output[$i]->village_name);
            $sheet->setCellValue('J'.$numrow, $output[$i]->sex_name);
            $sheet->setCellValue('K'.$numrow, $output[$i]->birth_date);
            $sheet->setCellValue('L'.$numrow, $age);
            $sheet->setCellValue('M'.$numrow, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
            $sheet->setCellValue('N'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            $sheet->setCellValue('O'.$numrow, $output[$i]->is_new == 'N' ? 'Lama' : 'Baru');
            $sheet->setCellValue('P'.$numrow, $output[$i]->how_to_enter_name);
            $numrow_detail = $numrow+1;
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function mr_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA"
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "JENIS KELAMIN"); 
        $sheet->setCellValue('J3', "TGL.LAHIR"); 
        $sheet->setCellValue('K3', "PENJAMIN"); 
        $sheet->setCellValue('L3', "DIAGNOSA"); 
        $sheet->setCellValue('M3', "BERAT BADAN"); 
        $sheet->setCellValue('N3', "TINGGI BADAN"); 
        $sheet->setCellValue('O3', "TEKANAN DARAH"); 
        $sheet->setCellValue('P3', "PENUNJANG"); 
        $sheet->setCellValue('Q3', "HASIL"); 
        
        $where = $q_strings;
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051

        $select = '
        rd.id as registration_destination_id,
        ri.sep,
        a.norm, 
        s.id as soap_id, 
        h2.name as how_to_pay, 
        c.name as insurance_name,
        rd.checkin_date as register_date, 
        hfi.name as health_facility_item_name,
        p.idcard, 
        p.name, 
        p.address, 
        p.phone, 
        p.sex, 
        h3.name as sex_name,
        p.birth_date, 
        e.fullname as doctor_in_charge_name, 
        i.code as icd_code, 
        i.name as icd_name
        ';
        $where['a.is_cancel'] = 0;
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            array('soap_a_action saa', "sa.id = saa.soap_a_id AND saa.index=0", 'INNER'),
            array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);

        // var_dump($this->db->last_query());
        // die();

        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4

        for ($i=0; $i < count($output); $i++) {
            $soap_id = $output[$i]->soap_id;
            $registration_destination_id = $output[$i]->registration_destination_id;
            $birth_date = $output[$i]->birth_date;
            $register_date = $output[$i]->register_date;
            $register_date = date('d/m/Y H:i:s', strtotime($register_date));
            $norm = $output[$i]->norm;
            $idcard = $output[$i]->idcard;
            $name = $output[$i]->name;
            $address = $output[$i]->address;
            $phone = $output[$i]->phone;
            $health_facility_item_name = $output[$i]->health_facility_item_name;
            $doctor_in_charge_name = $output[$i]->doctor_in_charge_name;
            $sex_name = $output[$i]->sex_name;
            $how_to_pay = $output[$i]->how_to_pay;
            $insurance_name = $output[$i]->insurance_name;
            $icd_code = $output[$i]->icd_code;
            $icd_name = $output[$i]->icd_name;

            $select = 'a.id, lri.costing_name, lp.code_lis';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
                'lri.is_delete' => 0,
            );
            $join = array(
                array('laboratory_request_item lri', "a.id = lri.laboratory_request_id", 'INNER'),
                array('laboratory_package lp', 'lri.laboratory_package_id = lp.id', 'INNER'),
            );
            $response_data_lab = $this->Laboratory_request->select(0, $select, $where, $join);
            
            $select = 'rri.costing_name';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.status' => 2,
                'rri.status' => 2,
                'rrii.status' => 2,
                'a.is_delete' => 0,
                'rri.is_delete' => 0,
                'rrii.is_delete' => 0,
            );
            $join = array(
                array('radiology_request_item rri', "a.id = rri.radiology_request_id", 'INNER'),
                array('radiology_request_item_image rrii', "rri.id = rrii.radiology_request_item_id", 'INNER'),
            );
            $this->db->group_by(array('rrii.id'));
            $response_data_rad = $this->Radiology_request->select(0, $select, $where, $join);

            $select = 'a.id, a.weight, a.height, a.systolic, a.diastolic';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.is_delete' => 0,
            );
            $join = array();
            $order = array('a.observation_date' => 'DESC');
            $this->Vital_signs_monitoring->limit = 1;
            $vital_signs_monitoring = $this->Vital_signs_monitoring->select(0, $select, $where, $join, $order);
            $this->Vital_signs_monitoring->limit = 0;

            $weight = '';
            $height = '';
            $blood_pressure = '';

            if (count($vital_signs_monitoring) > 0) {
                $weight = $vital_signs_monitoring[0]->weight;
                $height = $vital_signs_monitoring[0]->height;
                $systolic = $vital_signs_monitoring[0]->systolic;
                $diastolic = $vital_signs_monitoring[0]->diastolic;
                $blood_pressure = $systolic . '/' . $diastolic;
            }

            $sheet->setCellValue('A'.$numrow, $register_date);
            $sheet->setCellValue('B'.$numrow, $norm);
            $sheet->setCellValue('C'.$numrow, $idcard);
            $sheet->setCellValue('D'.$numrow, $name);
            $sheet->setCellValue('E'.$numrow, $address);
            $sheet->setCellValue('F'.$numrow, $phone);
            $sheet->setCellValue('G'.$numrow, $health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $sex_name);
            $sheet->setCellValue('J'.$numrow, $birth_date);
            $sheet->setCellValue('K'.$numrow, $how_to_pay.'-'.$insurance_name);
            $sheet->setCellValue('L'.$numrow, $icd_code.'-'.$icd_name);
            $sheet->setCellValue('M'.$numrow, $weight);
            $sheet->setCellValue('N'.$numrow, $height);
            $sheet->setCellValue('O'.$numrow, $blood_pressure);

            $data_lis_response = [];
            $numrow_detail = $numrow+1;

            for ($lab=0; $lab < count($response_data_lab); $lab++) {
                $laboratory_request_id = $response_data_lab[$lab]->id;
                $costing_name = $response_data_lab[$lab]->costing_name;
                $code_lis = $response_data_lab[$lab]->code_lis;
                $result = "http://192.168.100.3:9000/laboratory/print/" . $laboratory_request_id;

                // if (!key_exists($laboratory_request_id, $data_lis_response)) {
                //     // LIS RESULT
                //     $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                //     $data_lis_response[$laboratory_request_id] = $lis_response;
                // }

                // $item_result = [];

                // if (count($data_lis_response[$laboratory_request_id]) > 0) {
                //     $result = $data_lis_response[$laboratory_request_id][0]['result_detail'];
    
                //     for ($l=0; $l < count($result); $l++) { 
                //         $flag = $result[$l]['flag'];
                //         $parameter_id = $result[$l]['ParameterID'];

                //         // keluarin semua hasil lab
                //         if ($parameter_id == $code_lis) {
                //             array_push($item_result, $result[$l]);
                //         }
                //     }
                // }

                // for ($l=0; $l < count($item_result); $l++) { 
                //     var_dump('hasil=' . $item_result[$l]['hasil'] . '|flag=' . $item_result[$l]['flag'] . '|satuan=' . $item_result[$l]['satuan'] . '|metoda=' . $item_result[$l]['metoda'] . '|n_rujukan=' . $item_result[$l]['n_rujukan']);
                //     echo "<br>";
                // }

                $sheet->setCellValue('A'.$numrow_detail, $register_date);
                $sheet->setCellValue('B'.$numrow_detail, $norm);
                $sheet->setCellValue('C'.$numrow_detail, $idcard);
                $sheet->setCellValue('D'.$numrow_detail, $name);
                $sheet->setCellValue('E'.$numrow_detail, $address);
                $sheet->setCellValue('F'.$numrow_detail, $phone);
                $sheet->setCellValue('G'.$numrow_detail, $health_facility_item_name);
                $sheet->setCellValue('H'.$numrow_detail, $doctor_in_charge_name);
                $sheet->setCellValue('I'.$numrow_detail, $sex_name);
                $sheet->setCellValue('J'.$numrow_detail, $birth_date);
                $sheet->setCellValue('K'.$numrow_detail, $how_to_pay.'-'.$insurance_name);
                $sheet->setCellValue('L'.$numrow_detail, $icd_code.'-'.$icd_name);
                $sheet->setCellValue('M'.$numrow_detail, $weight);
                $sheet->setCellValue('N'.$numrow_detail, $height);
                $sheet->setCellValue('O'.$numrow_detail, $blood_pressure);
                $sheet->setCellValue('P'.$numrow_detail, $costing_name);
                $sheet->setCellValue('Q'.$numrow_detail, $result);
                
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }

            for ($rad=0; $rad < count($response_data_rad); $rad++) {
                $costing_name = $response_data_rad[$rad]->costing_name;

                $sheet->setCellValue('A'.$numrow_detail, $register_date);
                $sheet->setCellValue('B'.$numrow_detail, $norm);
                $sheet->setCellValue('C'.$numrow_detail, $idcard);
                $sheet->setCellValue('D'.$numrow_detail, $name);
                $sheet->setCellValue('E'.$numrow_detail, $address);
                $sheet->setCellValue('F'.$numrow_detail, $phone);
                $sheet->setCellValue('G'.$numrow_detail, $health_facility_item_name);
                $sheet->setCellValue('H'.$numrow_detail, $doctor_in_charge_name);
                $sheet->setCellValue('I'.$numrow_detail, $sex_name);
                $sheet->setCellValue('J'.$numrow_detail, $birth_date);
                $sheet->setCellValue('K'.$numrow_detail, $how_to_pay.'-'.$insurance_name);
                $sheet->setCellValue('L'.$numrow_detail, $icd_code.'-'.$icd_name);
                $sheet->setCellValue('M'.$numrow_detail, $weight);
                $sheet->setCellValue('N'.$numrow_detail, $height);
                $sheet->setCellValue('O'.$numrow_detail, $blood_pressure);
                $sheet->setCellValue('P'.$numrow_detail, $costing_name);
                
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }

            $numrow++;
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function mr_kll_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA"
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "JENIS KELAMIN"); 
        $sheet->setCellValue('J3', "TGL.LAHIR"); 
        $sheet->setCellValue('K3', "PENJAMIN"); 
        $sheet->setCellValue('L3', "ASSESMENT"); 
        $sheet->setCellValue('M3', "KODE DIAGNOSA"); 
        $sheet->setCellValue('N3', "DIAGNOSA PENYAKIT"); 
        $sheet->setCellValue('O3', "STATUS PULANG"); 
        
        $where = $q_strings;
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051

        $select = '
        rd.id as registration_destination_id,
        rd.exit_reason,
        ri.sep,
        a.norm, 
        s.id as soap_id, 
        h2.name as how_to_pay, 
        c.name as insurance_name,
        rd.checkin_date as register_date, 
        hfi.name as health_facility_item_name,
        p.idcard, 
        p.name, 
        p.address, 
        p.phone, 
        p.sex, 
        h3.name as sex_name,
        p.birth_date, 
        e.fullname as doctor_in_charge_name,ss.history_disease_now,
        i.code as icd_code, 
        i.name as icd_name
        ';
        $where['a.is_cancel'] = 0;
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('soap_s ss', "s.id = ss.soap_id", 'INNER'),
            array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            array('soap_a_action saa', "sa.id = saa.soap_a_id AND saa.index=0", 'INNER'),
            array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $this->db->like('history_disease_now', 'kll');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        // var_dump($this->db->last_query());
        // die();

        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4

        for ($i=0; $i < count($output); $i++) {
            $soap_id = $output[$i]->soap_id;
            $registration_destination_id = $output[$i]->registration_destination_id;
            $birth_date = $output[$i]->birth_date;
            $exit_reason = $output[$i]->exit_reason;
            $register_date = $output[$i]->register_date;
            $register_date = date('d/m/Y H:i:s', strtotime($register_date));
            $norm = $output[$i]->norm;
            $idcard = $output[$i]->idcard;
            $name = $output[$i]->name;
            $address = $output[$i]->address;
            $phone = $output[$i]->phone;
            $health_facility_item_name = $output[$i]->health_facility_item_name;
            $doctor_in_charge_name = $output[$i]->doctor_in_charge_name;
            $sex_name = $output[$i]->sex_name;
            $how_to_pay = $output[$i]->how_to_pay;
            $insurance_name = $output[$i]->insurance_name;
            $history_disease_now = $output[$i]->history_disease_now;
            $icd_code = $output[$i]->icd_code;
            $icd_name = $output[$i]->icd_name;

            $sheet->setCellValue('A'.$numrow, $register_date);
            $sheet->setCellValue('B'.$numrow, $norm);
            $sheet->setCellValue('C'.$numrow, $idcard);
            $sheet->setCellValue('D'.$numrow, $name);
            $sheet->setCellValue('E'.$numrow, $address);
            $sheet->setCellValue('F'.$numrow, $phone);
            $sheet->setCellValue('G'.$numrow, $health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $sex_name);
            $sheet->setCellValue('J'.$numrow, $birth_date);
            $sheet->setCellValue('K'.$numrow, $how_to_pay.'-'.$insurance_name);
            $sheet->setCellValue('L'.$numrow, $history_disease_now);
            $sheet->setCellValue('M'.$numrow, $icd_code);
            $sheet->setCellValue('N'.$numrow, $icd_name);
            $sheet->setCellValue('O'.$numrow, $exit_reason);

            $numrow++;
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function mr_insurance_up_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA"
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "JENIS KELAMIN"); 
        $sheet->setCellValue('J3', "TGL.LAHIR"); 
        $sheet->setCellValue('K3', "PENJAMIN"); 
        $sheet->setCellValue('L3', "PENJAMIN DEFAULT"); 
        
        $where = $q_strings;
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['ri.company_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';
        $where['hfi.health_facility_id'] = '40f06534-07ec-4221-ad9d-dea08196079f';
        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051

        $select = '
        rd.id as registration_destination_id,
        ri.sep,
        a.norm, 
        h2.name as how_to_pay, 
        c.name as insurance_name, ri.service_class_name,
        rd.checkin_date as register_date, 
        hfi.name as health_facility_item_name,
        p.idcard, 
        p.name, 
        p.address, 
        sc.name as service_class_name_master, 
        p.phone, 
        p.sex, 
        h3.name as sex_name,
        p.birth_date, 
        e.fullname as doctor_in_charge_name, ri.number_insurance
        ';
        $where['a.is_cancel'] = 0;
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('insurance i', 'a.norm = i.norm AND i.company_id="1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8"', 'LEFT'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('service_class sc', "i.service_class_id = sc.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        // var_dump($this->db->last_query());
        // die();

        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4

        for ($i=0; $i < count($output); $i++) {
            $number_insurance = $output[$i]->number_insurance;
            if ($number_insurance != null && $number_insurance != '') {
                $output_patient_bpjs = $this->vclaim_sep->request("/Peserta/nokartu/".$number_insurance."/tglSEP/".date('Y-m-d'), "GET");
                $output_patient_bpjs = json_decode($output_patient_bpjs);
                $output_patient_bpjs = $output_patient_bpjs->response->peserta;
                $service_class_name = $output[$i]->service_class_name;
                if ($output_patient_bpjs->hakKelas->kode != $service_class_name) {
                    $soap_id = $output[$i]->soap_id;
                    $registration_destination_id = $output[$i]->registration_destination_id;
                    $birth_date = $output[$i]->birth_date;
                    $register_date = $output[$i]->register_date;
                    $register_date = date('d/m/Y H:i:s', strtotime($register_date));
                    $norm = $output[$i]->norm;
                    $idcard = $output[$i]->idcard;
                    $name = $output[$i]->name;
                    $address = $output[$i]->address;
                    $phone = $output[$i]->phone;
                    $health_facility_item_name = $output[$i]->health_facility_item_name;
                    $doctor_in_charge_name = $output[$i]->doctor_in_charge_name;
                    $sex_name = $output[$i]->sex_name;
                    $how_to_pay = $output[$i]->how_to_pay;
                    $insurance_name = $output[$i]->insurance_name;
        
                    $sheet->setCellValue('A'.$numrow, $register_date);
                    $sheet->setCellValue('B'.$numrow, $norm);
                    $sheet->setCellValue('C'.$numrow, $idcard);
                    $sheet->setCellValue('D'.$numrow, $name);
                    $sheet->setCellValue('E'.$numrow, $address);
                    $sheet->setCellValue('F'.$numrow, $phone);
                    $sheet->setCellValue('G'.$numrow, $health_facility_item_name);
                    $sheet->setCellValue('H'.$numrow, $doctor_in_charge_name);
                    $sheet->setCellValue('I'.$numrow, $sex_name);
                    $sheet->setCellValue('J'.$numrow, $birth_date);
                    $sheet->setCellValue('K'.$numrow, $service_class_name);
                    $sheet->setCellValue('L'.$numrow, $output_patient_bpjs->hakKelas->kode);
        
                    $numrow++;
                }
            }
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function mr_lansia_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA"
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NAMA LANSIA"); 
        $sheet->setCellValue('C3', "JENIS KELAMIN"); 
        $sheet->setCellValue('D3', "TEMPAT LAHIR"); 
        $sheet->setCellValue('E3', "TANGGAL LAHIR");
        $sheet->setCellValue('F3', "UMUR"); 
        $sheet->setCellValue('G3', "NO NIK"); 
        $sheet->setCellValue('H3', "TANGGAL PELAYANAN KESEHATAN"); 
        $sheet->setCellValue('I3', "NO ANSURANSI"); 
        $sheet->setCellValue('J3', "KEMANDIRIAN"); 
        $sheet->setCellValue('K3', "DIAGNOSA PENYAKIT"); 
        $sheet->setCellValue('L3', "ALAMAT"); 

        
        $where = $q_strings;
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['p.birth_date >='] = '1910-10-29';
        $where['p.birth_date <='] = '1965-10-29';
        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051

        $select = '
        rd.id as registration_destination_id,
        ri.sep,
        ri.number_insurance,
        a.norm, 
        s.id as soap_id, 
        h2.name as how_to_pay, 
        c.name as insurance_name,
        rd.checkin_date as register_date, 
        hfi.name as health_facility_item_name,
        p.idcard, 
        p.birth_place, 
        p.name, 
        p.address, 
        p.phone, 
        p.sex, 
        h3.name as sex_name,
        p.birth_date, 
        e.fullname as doctor_in_charge_name, 
        i.code as icd_code, 
        i.name as icd_name
        ';
        $where['a.is_cancel'] = 0;
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            array('soap_a_action saa', "sa.id = saa.soap_a_id AND saa.index=0", 'INNER'),
            array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);

        // var_dump($this->db->last_query());
        // die();

        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4

        for ($i=0; $i < count($output); $i++) {
            $soap_id = $output[$i]->soap_id;
            $registration_destination_id = $output[$i]->registration_destination_id;
            $birth_date = $output[$i]->birth_date;
            $register_date = $output[$i]->register_date;
            $register_date = date('d/m/Y H:i:s', strtotime($register_date));
            $norm = $output[$i]->norm;
            $idcard = $output[$i]->idcard;
            $birth_place = $output[$i]->birth_place;
            $number_insurance = $output[$i]->number_insurance;
            $name = $output[$i]->name;
            $address = $output[$i]->address;
            $age = (date('Y') - date('Y', strtotime($output[$i]->birth_date))); // Get the current year and the birth year
            $phone = $output[$i]->phone;
            $health_facility_item_name = $output[$i]->health_facility_item_name;
            $doctor_in_charge_name = $output[$i]->doctor_in_charge_name;
            $sex_name = $output[$i]->sex_name;
            $how_to_pay = $output[$i]->how_to_pay;
            $insurance_name = $output[$i]->insurance_name;
            $icd_code = $output[$i]->icd_code;
            $icd_name = $output[$i]->icd_name;

            $select = 'a.id, a.adl_type_id';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.is_delete' => 0,
            );
            $join = array();
            $order = array('a.date_added' => 'DESC');
            $this->Nurse->limit = 1;
            $nurse = $this->Nurse->select(0, $select, $where, $join, $order);
            $this->Nurse->limit = 0;

            $adl = '';

            if (count($nurse) > 0) {
                if ($nurse[0]->adl_type_id == '1') {
                    $adl = 'A';
                } else if ($nurse[0]->adl_type_id == '2') {
                    $adl = 'C';
                } else if ($nurse[0]->adl_type_id == '3') {
                    $adl = 'B';
                }
            }

            $sheet->setCellValue('A'.$numrow, $register_date);
            $sheet->setCellValue('B'.$numrow, $name);
            $sheet->setCellValue('C'.$numrow, $sex_name);
            $sheet->setCellValue('D'.$numrow, $birth_place);
            $sheet->setCellValue('E'.$numrow, $birth_date);
            $sheet->setCellValue('F'.$numrow, $age);
            $sheet->setCellValue('G'.$numrow, "'".$idcard);
            $sheet->setCellValue('H'.$numrow, $register_date);
            $sheet->setCellValue('I'.$numrow, $number_insurance);
            $sheet->setCellValue('J'.$numrow, $adl);
            $sheet->setCellValue('K'.$numrow, $icd_name);
            $sheet->setCellValue('L'.$numrow, $address);

            $numrow++;
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('DATA LANSIA');
        $writer = new Xlsx($spreadsheet);

        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}
	
    public function icd_req_get($p = 0)
    {
        // header("Access-Control-Allow-Origin: *");
        // parse_str($_SERVER['QUERY_STRING'],$q_strings);
        // $id     = $this->security->xss_clean($p);
        // $model  = $this->name;http://localhost/be-ci/v1/mr/report/registration/icd_req?date_start=2025-09-01&date_end=2025-11-25

        $registration = $this->db
            ->query(
                "SELECT r.id, r.code, rd.checkin_date, rd.checkout_date, rd.id as registration_destination_id, hf.name as health_facility_name, ri.insurance_id, h2.name as how_to_pay, c.name as insurance_name, r.register_date, r.norm, p.name, p.idcard, p.birth_date, hf.name AS sex, h.name AS sex, i.code as icd10_code, i.name as icd10_name, hfi.name as health_facility_item_name, e.fullname as doctor_in_charge_name
            FROM registration_destination rd
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            LEFT JOIN company c ON ri.company_id = c.id
            INNER JOIN helper h2 ON ri.insurance_id = h2.code and h2.variable='insurance'
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            INNER JOIN patient p ON r.norm = p.norm
            INNER JOIN helper h ON p.sex = h.code AND h.variable ='sex'
            INNER JOIN soap s ON rd.id = s.registration_destination_id
            INNER JOIN soap_a sa ON s.id = sa.soap_id
            INNER JOIN soap_a_action saa ON sa.id = saa.soap_a_id
            INNER JOIN icd10 i ON saa.icd10_id = i.id
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            WHERE rd.checkin_date >= '2025-09-01 00:00:00'
            AND rd.checkin_date <= '2025-11-30 23:59:59'
            AND saa.icd10_id IN ('372a27f3-2a10-4c32-b956-106f1f0f0775','372a27f3-2a10-4c32-b956-106f1f0f0775')
            AND saa.is_delete = 0
            GROUP BY s.id
            ORDER BY rd.register_date DESC",
            )
            ->result();
        // dbd 372a27f3-2a10-4c32-b956-106f1f0f0775,372a27f3-2a10-4c32-b956-106f1f0f0775
        // '0256d7e2-1659-4b22-abe7-e913965543a6','514ec2b2-8105-4cbe-ae19-63dc334d55b3','75746717-d920-4148-89c8-ad0f600b8207','87cd59a4-c2c6-4bc9-95d8-15d5b4ebbdbd','ae9abb2d-1edf-4ba3-aabd-559eeae14b0a','cad38aaf-a3a1-409c-9627-900e9818a5b4','de53ebd2-d06f-4d01-88d4-9fd95369cfa1','e766d1e1-ee14-4c97-b0ef-e22b77982a71'
        $style = [
            "borders" => [
                "allBorders" => [
                    "borderStyle" =>
                        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    "color" => [
                        "argb" => "FF000000",
                    ],
                ],
            ],
        ];

        $headers = [
            "No. Registrasi" => [
                "width" => 15,
            ],
            "Tgl. Registrasi" => [
                "width" => 15,
            ],
            "Tgl. Checkout" => [
                "width" => 15,
            ],
            "No. RM" => [
                "width" => 15,
            ],
            "Penjamin" => [
                "width" => 15,
            ],
            "No. SEP" => [
                "width" => 15,
            ],
            "Nama" => [
                "width" => 15,
            ],
            "Kelamin" => [
                "width" => 15,
            ],
            "Tanggal" => [
                "width" => 15,
            ],
            "Unit" => [
                "width" => 15,
            ],
            "Poliklinik/Ruangan" => [
                "width" => 15,
            ],
            "DPJP" => [
                "width" => 15,
            ],
            "DIAGNOSA" => [
                "width" => 15,
            ],
            "NO TELEPON" => [
                "width" => 15,
            ],
        ];

        $startTime = microtime(true);

        Logger::info(
            $this->debug_uniq,
            "|---- start export registration ----|",
        );

        Logger::info($this->debug_uniq, "total rows: " . count($registration));

        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();

        $column = 1;
        $row = 1;
        foreach ($headers as $key => $val) {
            foreach ($val as $key_val => $val_val) {
                if ($key_val == "width") {
                    $activeWorksheet
                        ->getColumnDimensionByColumn($column)
                        ->setWidth($val_val);
                }
            }

            // applying style
            $activeWorksheet
                ->getStyleByColumnAndRow($column, $row, count($headers), $row)
                ->applyFromArray($style);
            // bold text
            $activeWorksheet
                ->getStyleByColumnAndRow($column, $row, count($headers), $row)
                ->getAlignment()
                ->setHorizontal("center");
            // bold text
            $activeWorksheet
                ->getStyleByColumnAndRow($column, $row, count($headers), $row)
                ->getFont()
                ->setBold(true);
            // color cell
            $activeWorksheet
                ->getStyleByColumnAndRow($column, $row, count($headers), $row)
                ->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()
                ->setARGB("FFFFFF00");
            // set cell value
            $activeWorksheet->setCellValueByColumnAndRow($column, $row, $key);

            $column += 1;
        }

        $count = 0;
        for ($i = 0; $i < count($registration); $i++) {
            $count += 1;

            $activeWorksheet->getstyle("A1")->applyFromArray($style);
            $activeWorksheet->getstyle("B1")->applyFromArray($style);
            $activeWorksheet->getstyle("C1")->applyFromArray($style);
            $activeWorksheet->getstyle("D1")->applyFromArray($style);
            $activeWorksheet->getstyle("E1")->applyFromArray($style);
            $activeWorksheet->getstyle("F1")->applyFromArray($style);
            $activeWorksheet->getstyle("G1")->applyFromArray($style);
            $activeWorksheet->getstyle("H1")->applyFromArray($style);
            $activeWorksheet->getstyle("I1")->applyFromArray($style);
            $activeWorksheet->getstyle("J1")->applyFromArray($style);
            $activeWorksheet->getstyle("K1")->applyFromArray($style);
            $activeWorksheet->getstyle("L1")->applyFromArray($style);
            $activeWorksheet->getstyle("M1")->applyFromArray($style);
            $activeWorksheet->getstyle("N1")->applyFromArray($style);

            $code_registration = $registration[$i]->code;
            $checkin_date = $registration[$i]->checkin_date;
            $checkout_date = $registration[$i]->checkout_date;
            $norm = $registration[$i]->norm;
            $insurance_id = $registration[$i]->insurance_id;
            $insurance_name = $registration[$i]->insurance_name;
            $sep = $registration[$i]->sep;
            $name = $registration[$i]->name;
            $sex_name = $registration[$i]->sex_name;
            $birth_date = $registration[$i]->birth_date;
            $health_facility_name = $registration[$i]->health_facility_name;
            $health_facility_item_name =
                $registration[$i]->health_facility_item_name;
            $doctor_name = $registration[$i]->doctor_in_charge_name;
            $icd_code = $registration[$i]->icd10_code;
            $icd_name = $registration[$i]->icd10_name;
            $phone = $registration[$i]->phone;

            $coordinate = $i + 2;
            $activeWorksheet->setCellValue(
                "A" . $coordinate,
                $code_registration,
            );
            $activeWorksheet->setCellValue(
                "B" . $coordinate,
                date("Y-m-d", strtotime($checkin_date)),
            );
            $activeWorksheet->setCellValue(
                "C" . $coordinate,
                $checkout_date != null
                    ? date("Y-m-d", strtotime($checkout_date))
                    : date("Y-m-d", strtotime($checkin_date)),
            );
            $activeWorksheet->setCellValue("D" . $coordinate, $norm);
            $activeWorksheet->setCellValue(
                "E" . $coordinate,
                $insurance_id != 1 ? $insurance_name : "Pribadi",
            );
            $activeWorksheet->setCellValue("F" . $coordinate, $sep);
            $activeWorksheet->setCellValue("G" . $coordinate, $name);
            $activeWorksheet->setCellValue("H" . $coordinate, $sex_name);
            $activeWorksheet->setCellValue("I" . $coordinate, $birth_date);
            $activeWorksheet->setCellValue(
                "J" . $coordinate,
                $health_facility_name,
            );
            $activeWorksheet->setCellValue(
                "K" . $coordinate,
                $health_facility_item_name,
            );
            $activeWorksheet->setCellValue("L" . $coordinate, $doctor_name);
            $activeWorksheet->setCellValue("L" . $coordinate, $doctor_name);
            $activeWorksheet->setCellValue(
                "M" . $coordinate,
                $icd_code . "-" . $icd_name,
            );
            $activeWorksheet->setCellValue("N" . $coordinate, "'" . $phone);

            if ($count % 1000 == 0) {
                Logger::info(
                    $this->debug_uniq,
                    "current processed rows: " . $count,
                );
            }
        }

        $writer = new Xlsx($spreadsheet);

        $filename = $this->name . "_" . date("Ymd_His");

        header(
            "Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        );
        header(
            'Content-Disposition: attachment; filename="' .
                $filename .
                '.xlsx"',
        );
        header("Cache-Control: max-age=0");
        $writer->save("php://output");

        Logger::info(
            $this->debug_uniq,
            "|---- finish export registration ----|",
        );
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        $formattedTime = number_format($executionTime, 3, ".", "");
        Logger::info(
            $this->debug_uniq,
            "execution time: " . $formattedTime . " seconds",
        );

        ini_set("memory_limit", "156M");
        ini_set("max_execution_time", 30);

        // var_dump($data);
    }


    public function mr_doctor_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "JENIS KELAMIN"); 
        $sheet->setCellValue('J3', "TGL.LAHIR"); 
        $sheet->setCellValue('K3', "PENJAMIN"); 
        $sheet->setCellValue('L3', "DIAGNOSA"); 
        $sheet->setCellValue('M3', "DPJP"); 
        $sheet->setCellValue('N3', "DPJP2"); 
        $sheet->setCellValue('O3', "DPJP3"); 
        $sheet->setCellValue('P3', "DPJP4"); 
        $sheet->setCellValue('Q3', "DPJP5"); 
        // $sheet->setCellValue('O3', "JENIS KELAMIN"); 
        // $sheet->setCellValue('P3', "TANGGAL LAHIR"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        $no=1;
        
        
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                $where['rd.is_checkout'] = 0;
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }
        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051
        $where['a.is_cancel'] = 0;
        $select = 'a.norm, rd.checkin_date as register_date, p.idcard, p.name, p.address, p.phone, p.sex, p.birth_date, e.fullname as doctor_in_charge_name, h3.name as sex_name,
        h2.name as how_to_pay, c.name as insurance_name,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_2) as doctor_in_charge_name_2,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_3) as doctor_in_charge_name_3,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_4) as doctor_in_charge_name_4,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_5) as doctor_in_charge_name_5,
        hfi.name as health_facility_item_name,ri.sep';
        // i.code as icd_code, s.id as soap_id, i.name as icd_name,
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'LEFT'),
            // array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            // array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            // array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            // array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            // array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            // array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            // array('soap_a_action saa', "sa.id = saa.soap_a_id", 'INNER'),
            // array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        // $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);
        for ($i=0; $i < count($output); $i++) {
            $age = (date('Y') - date('Y', strtotime($output[$i]->birth_date))); // Get the current year and the birth year
            if (date('md', strtotime($output[$i]->birth_date)) > date('md')) { $age--; } // Check if the birthday has occurred this year or not (to adjust the age)
            $sheet->setCellValue('A'.$numrow, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('B'.$numrow, "'".$output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, "'".$output[$i]->idcard);
            $sheet->setCellValue('D'.$numrow, $output[$i]->name);
            $sheet->setCellValue('E'.$numrow, $output[$i]->address);
            $sheet->setCellValue('F'.$numrow, $output[$i]->phone);
            $sheet->setCellValue('G'.$numrow, $output[$i]->health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $output[$i]->sex_name);
            $sheet->setCellValue('J'.$numrow, $output[$i]->birth_date);
            $sheet->setCellValue('K'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            $sheet->setCellValue('L'.$numrow, '');
            $sheet->setCellValue('M'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('N'.$numrow, $output[$i]->doctor_in_charge_name_2);
            $sheet->setCellValue('O'.$numrow, $output[$i]->doctor_in_charge_name_3);
            $sheet->setCellValue('P'.$numrow, $output[$i]->doctor_in_charge_name_4);
            $sheet->setCellValue('Q'.$numrow, $output[$i]->doctor_in_charge_name_5);
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}

    public function bride_lis($url = null,$param = null) {
        // url : 192.168.100.153/api_lis/index.php
        $url_acc   = '192.168.100.153';
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        // $authorization[] = "Authorization:Bearer ".$token; 
        // $authorization[] = "X-Session-ID:".$SESSIONID; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json;charset=utf-8"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
    }

    public function mcu_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI/RUANGAN"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "JENIS KELAMIN"); 
        $sheet->setCellValue('J3', "TGL.LAHIR"); 
        $sheet->setCellValue('K3', "PENJAMIN"); 
        $sheet->setCellValue('L3', "Morphine/Opiate(MOP)"); 
        $sheet->setCellValue('M3', "Amphetamine(AMP)"); 
        $sheet->setCellValue('N3', "THC (Mariyuana)"); 
        $sheet->setCellValue('O3', "Methampetamine(MET)"); 
        $sheet->setCellValue('P3', "Cocaine(COC)"); 
        $sheet->setCellValue('Q3', "Benzodiazephine(BZD)"); 
        $sheet->setCellValue('R3', "URL"); 
        $sheet->setCellValue('S3', "URL Narkoba"); 
        // $sheet->setCellValue('M3', "PENUNJANG"); 
        // $sheet->setCellValue('O3', "JENIS KELAMIN"); 
        // $sheet->setCellValue('P3', "TANGGAL LAHIR"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        $no=1;
        
        
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                $where['rd.is_checkout'] = 0;
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'];
            $date_end = $q_strings['date_end'];
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
    
            if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $where_doc = array('a.is_checkout' => 0);
            } else {
                $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
            }
            $select = 'r.id';
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }
        // 1. Berdasarkan 6 kelurahan (Teluk tering, baloi permai, belian, sukajadi, taman baloi, batu besar)
        // 2171051
        $where['a.is_cancel'] = 0;
        $select = 'a.norm, rd.checkin_date as register_date, p.idcard, p.name, p.address, p.phone, p.sex, p.birth_date, e.fullname as doctor_in_charge_name, h3.name as sex_name,
        mcu.fit_to_work, mcu.soap_id, h2.name as how_to_pay, c.name as insurance_name,
        hfi.name as health_facility_item_name,ri.sep';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            array('medical_check_up mcu', "s.id = mcu.soap_id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);
        // $this->REST_Return(200, 'SUCCESS', $output);
        // return;
        $result = array();
        $result_lab = array();
        $lis_response = array();
        $l=0;
        for ($i=0; $i < count($output); $i++) {
            if ($output[$i]->soap_id) {
                $response_data_lab = $this->db->query("
                    SELECT `b`.`costing_name`,`b`.`laboratory_request_id`,`lb`.`code_lis`
                    FROM `laboratory_request` as `a` 
                    LEFT JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
                    LEFT JOIN `laboratory_package` `lb` ON `b`.`laboratory_package_id` = `lb`.`id` 
                    WHERE `a`.`soap_id` = '{$output[$i]->soap_id}'
                    AND `b`.`costing_name` = 'TES NARKOBA 6 ANALIT'
                    AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
                // LIS RESULT
                $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $response_data_lab[0]->laboratory_request_id);

                $code_lis = $response_data_lab[0]->code_lis;

                if (count($lis_response) > 0) {
                    $result = $lis_response[0]['result_detail'];

                    for ($l=0; $l < count($result); $l++) { 
                        $flag = $result[$l]['flag'];
                        $parameter_id = $result[$l]['ParameterID'];

                        // keluarin semua hasil lab
                        // $flag != '' && 
                        if ($parameter_id == $code_lis) {
                            array_push($result_lab, $result[$l]);
                        }
                    }
                }
                // $response_data_rad = $this->db->query("
                //     SELECT `b`.`costing_name`
                //     FROM `radiology_request` as `a` 
                //     LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
                //     INNER JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
                //     WHERE `a`.`soap_id` = '{$output[$i]->soap_id}'
                //     AND `a`.`is_delete` = 0 AND `a`.`status` = 2 AND `b`.`status` = 2 AND `bb`.`status` = 2 AND `b`.`is_delete` = 0 AND `bb`.`is_delete` = 0 GROUP BY `bb`.`id`")->result();
            }
            $age = (date('Y') - date('Y', strtotime($output[$i]->birth_date))); // Get the current year and the birth year
            if (date('md', strtotime($output[$i]->birth_date)) > date('md')) { $age--; } // Check if the birthday has occurred this year or not (to adjust the age)
            $sheet->getHyperlink('R'. $numrow)->setUrl('http://192.168.100.3:9000/prints/mcu-result/'.$output[$i]->soap_id); // Hijau muda
            $sheet->getHyperlink('S'. $numrow)->setUrl('http://192.168.100.3:9000/prints/mcu-drug/'.$output[$i]->soap_id); // Hijau muda
            $sheet->setCellValue('A'.$numrow, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('B'.$numrow, "'".$output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, "'".$output[$i]->idcard);
            $sheet->setCellValue('D'.$numrow, $output[$i]->name);
            $sheet->setCellValue('E'.$numrow, $output[$i]->address);
            $sheet->setCellValue('F'.$numrow, $output[$i]->phone);
            $sheet->setCellValue('G'.$numrow, $output[$i]->health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $output[$i]->sex_name);
            $sheet->setCellValue('J'.$numrow, $output[$i]->birth_date);
            $sheet->setCellValue('K'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            $sheet->setCellValue('L'.$numrow, $result_lab[0]['hasil']);
            $sheet->setCellValue('M'.$numrow, $result_lab[1]['hasil']);
            $sheet->setCellValue('N'.$numrow, $result_lab[2]['hasil']);
            $sheet->setCellValue('O'.$numrow, $result_lab[3]['hasil']);
            $sheet->setCellValue('P'.$numrow, $result_lab[4]['hasil']);
            $sheet->setCellValue('Q'.$numrow, $result_lab[5]['hasil']);
            $sheet->setCellValue('R'.$numrow, 'Lihat Hasil Pemeriksaan');
            $sheet->setCellValue('S'.$numrow, 'Lihat Hasil Narkoba');
            // $sheet->setCellValue('M'.$numrow, "http://192.168.100.3:9000/prints/mcu-result/".$output[$i]->soap_id);
            // $sheet->setCellValue('M'.$numrow, '');
            // $sheet->setCellValue('N'.$numrow, '$output[$i]->how_to_pay.'-'.$output[$i]->insurance_name');
            // $sheet->setCellValue('O'.$numrow, $output[$i]->birth_date);
            // $sheet->setCellValue('P'.$numrow, $output[$i]->sex);
            // $numrow_detail = $numrow+1;
            // for ($lab=0; $lab < count($response_data_lab); $lab++) {
            //     $sheet->setCellValue('A'.$numrow_detail, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            //     $sheet->setCellValue('B'.$numrow_detail, "'".$output[$i]->norm);
            //     $sheet->setCellValue('C'.$numrow_detail, "'".$output[$i]->idcard);
            //     $sheet->setCellValue('D'.$numrow_detail, $output[$i]->name);
            //     $sheet->setCellValue('E'.$numrow_detail, $output[$i]->address);
            //     $sheet->setCellValue('F'.$numrow_detail, $output[$i]->phone);
            //     $sheet->setCellValue('G'.$numrow_detail, $output[$i]->health_facility_item_name);
            //     $sheet->setCellValue('H'.$numrow_detail, $output[$i]->doctor_in_charge_name);
            //     $sheet->setCellValue('I'.$numrow, $output[$i]->sex_name);
            //     $sheet->setCellValue('J'.$numrow, $output[$i]->birth_date);
            //     $sheet->setCellValue('K'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            //     $sheet->setCellValue('L'.$numrow_detail, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
            //     $sheet->setCellValue('M'.$numrow_detail, $response_data_lab[$lab]->costing_name);
            //     $numrow_detail++; // Tambah 1 setiap kali looping
            //     $numrow++;
            // }
            // for ($rad=0; $rad < count($response_data_rad); $rad++) {
            //     $sheet->setCellValue('A'.$numrow_detail, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            //     $sheet->setCellValue('B'.$numrow_detail, "'".$output[$i]->norm);
            //     $sheet->setCellValue('C'.$numrow_detail, "'".$output[$i]->idcard);
            //     $sheet->setCellValue('D'.$numrow_detail, $output[$i]->name);
            //     $sheet->setCellValue('E'.$numrow_detail, $output[$i]->address);
            //     $sheet->setCellValue('F'.$numrow_detail, $output[$i]->phone);
            //     $sheet->setCellValue('G'.$numrow_detail, $output[$i]->health_facility_item_name);
            //     $sheet->setCellValue('H'.$numrow_detail, $output[$i]->doctor_in_charge_name);
            //     $sheet->setCellValue('I'.$numrow, $output[$i]->sex_name);
            //     $sheet->setCellValue('J'.$numrow, $output[$i]->birth_date);
            //     $sheet->setCellValue('K'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            //     $sheet->setCellValue('L'.$numrow_detail, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
            //     $sheet->setCellValue('M'.$numrow_detail, $response_data_rad[$rad]->costing_name);
            //     $numrow_detail++; // Tambah 1 setiap kali looping
            //     $numrow++;
            // }
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
	}
}