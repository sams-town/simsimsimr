<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
class Appointment extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_schedule_model", "{$this->name}_schedule");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/medical_support/Doctor_schedule_model", "Doctor_schedule");
        $this->load->model("v1/medical_support/Doctor_schedule_item_model", "Doctor_schedule_item");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/mr/Responsible_person_model", "Responsible_person");
        $this->load->model("v1/setting/Health_facility_model", "Health_facility");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        // Load Helper 
        $this->load->helper('queue');

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            // $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['e.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings);
            } else {
                $output = $this->$model->read($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                if($output[$i]->norm != '') {
                    // Patient
                    $this->Patient->whereFields['b.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['g.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['h.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['i.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['a.is_delete'] = '0';
                    $patient = $this->Patient->read($output[$i]->norm);
                    foreach ($patient[0] as $key => $val) {
                        $output[$i]->$key = $val;
                    }

                    // Responsible_person
                    $responsible_person = $this->Responsible_person->read($output[$i]->norm);
                    if($responsible_person != []) {
                        $output[$i]->responsible_person_responsible_id = $responsible_person[0]->responsible_id;
                        $output[$i]->responsible_person_idcard = $responsible_person[0]->idcard;
                        $output[$i]->responsible_person_typeidcard = $responsible_person[0]->typeidcard;
                        $output[$i]->responsible_person_name = $responsible_person[0]->name;
                        $output[$i]->responsible_person_phone = $responsible_person[0]->phone;
                        $output[$i]->responsible_person_address_id = $responsible_person[0]->address_id;
                    }
                }
                
                // Get time appointment
                $this->Appointment_schedule->whereFields['a.doctor_schedule_item_id'] = $output[$i]->doctor_schedule_item_id;
                $this->Appointment_schedule->whereFields['b.date_appointment'] = $output[$i]->date_appointment;
                $appointment_schedule = $this->Appointment_schedule->read(0,false);
                $doctor_schedule_item = $this->Doctor_schedule_item->read($output[$i]->doctor_schedule_item_id);

                $queue_time = queue_time_interval($appointment_schedule, $doctor_schedule_item[0]->hour_start, $doctor_schedule_item[0]->hour_end, $doctor_schedule_item[0]->quota, $doctor_schedule_item[0]->quota_bo, $output[$i]->date_appointment);

                // TEMPORARY FIX
                if(isset($queue_time['queue'][$output[$i]->queue])) {
                    $output[$i]->time_appointment = $queue_time['queue'][$output[$i]->queue - 1]['time'];
                }
            }
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function list_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            // $this->$model->whereLike = $q_strings;

            if(isset($q_strings['name'])) {
                $this->$model->whereFields['a.name'] = $q_strings['name'];
                unset($q_strings['name']);
            }
            
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $this->$model->whereFields['a.date_appointment >='] = $q_strings['date_start'];
                $this->$model->whereFields['a.date_appointment <='] = $q_strings['date_end'];
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                if($output->rows[$i]->norm != '') {
                    // Patient
                    $this->Patient->whereFields['b.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['g.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['h.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['i.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['a.is_delete'] = '0';
                    $patient = $this->Patient->read($output->rows[$i]->norm);
                    foreach ($patient[0] as $key => $val) {
                        $output->rows[$i]->$key = $val;
                    }

                    // Responsible_person
                    $responsible_person = $this->Responsible_person->read($output->rows[$i]->norm);
                    if($responsible_person != []) {
                        $output->rows[$i]->responsible_person_responsible_id = $responsible_person[0]->responsible_id;
                        $output->rows[$i]->responsible_person_idcard = $responsible_person[0]->idcard;
                        $output->rows[$i]->responsible_person_typeidcard = $responsible_person[0]->typeidcard;
                        $output->rows[$i]->responsible_person_name = $responsible_person[0]->name;
                        $output->rows[$i]->responsible_person_phone = $responsible_person[0]->phone;
                        $output->rows[$i]->responsible_person_address_id = $responsible_person[0]->address_id;
                    }
                }

                // Get time appointment
                $this->Appointment_schedule->whereFields['a.doctor_schedule_item_id'] = $output->rows[$i]->doctor_schedule_item_id;
                $this->Appointment_schedule->whereFields['b.date_appointment'] = $output->rows[$i]->date_appointment;
                $appointment_schedule = $this->Appointment_schedule->read(0,false);
                $doctor_schedule_item = $this->Doctor_schedule_item->read($output->rows[$i]->doctor_schedule_item_id);

                $queue_time = queue_time_interval($appointment_schedule, $doctor_schedule_item[0]->hour_start, $doctor_schedule_item[0]->hour_end, $doctor_schedule_item[0]->quota, $doctor_schedule_item[0]->quota_bo, $output->rows[$i]->date_appointment);

                if(isset($queue_time['queue'][$output->rows[$i]->queue])) {
                    $output->rows[$i]->time_appointment = $queue_time['queue'][$output->rows[$i]->queue - 1]['time'];
                }
            }
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function export_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            // $this->$model->whereLike = $q_strings;
            
            if(isset($q_strings['limit'])) $this->$model->limit = 1000; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $this->$model->whereFields['a.date_appointment >='] = $q_strings['date_start'];
                $this->$model->whereFields['a.date_appointment <='] = $q_strings['date_end'];
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            
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
            // Buat header tabel nya pada baris ke 3
            $sheet->setCellValue('A1', "phone_number"); 
            $sheet->setCellValue('B1', "full_name"); 
            $sheet->setCellValue('C1', "nama_pasien"); 
            $sheet->setCellValue('D1', "tanggal_control"); 
            $sheet->setCellValue('E1', "poliklinik_tujuan");
            $sheet->setCellValue('F1', "nama_dokter"); 
            $sheet->setCellValue('G1', "jam_dokter"); 
            $no=1;
            $numrow=2;
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                if($output->rows[$i]->norm != '') {
                    // Patient
                    $this->Patient->whereFields['b.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['g.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['h.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['i.institute_branch_id'] = $institute_branch_id;
                    $this->Patient->whereFields['a.is_delete'] = '0';
                    $patient = $this->Patient->read($output->rows[$i]->norm);
                    foreach ($patient[0] as $key => $val) {
                        $output->rows[$i]->$key = $val;
                    }

                    // Responsible_person
                    $responsible_person = $this->Responsible_person->read($output->rows[$i]->norm);
                    if($responsible_person != []) {
                        $output->rows[$i]->responsible_person_responsible_id = $responsible_person[0]->responsible_id;
                        $output->rows[$i]->responsible_person_idcard = $responsible_person[0]->idcard;
                        $output->rows[$i]->responsible_person_typeidcard = $responsible_person[0]->typeidcard;
                        $output->rows[$i]->responsible_person_name = $responsible_person[0]->name;
                        $output->rows[$i]->responsible_person_phone = $responsible_person[0]->phone;
                        $output->rows[$i]->responsible_person_address_id = $responsible_person[0]->address_id;
                    }
                }

                // Get time appointment
                $this->Appointment_schedule->whereFields['a.doctor_schedule_item_id'] = $output->rows[$i]->doctor_schedule_item_id;
                $this->Appointment_schedule->whereFields['b.date_appointment'] = $output->rows[$i]->date_appointment;
                $appointment_schedule = $this->Appointment_schedule->read(0,false);
                $doctor_schedule_item = $this->Doctor_schedule_item->read($output->rows[$i]->doctor_schedule_item_id);

                $queue_time = queue_time_interval($appointment_schedule, $doctor_schedule_item[0]->hour_start, $doctor_schedule_item[0]->hour_end, $doctor_schedule_item[0]->quota, $doctor_schedule_item[0]->quota_bo, $output->rows[$i]->date_appointment);

                if(isset($queue_time['queue'][$output->rows[$i]->queue])) {
                    $output->rows[$i]->time_appointment = $queue_time['queue'][$output->rows[$i]->queue - 1]['time'];
                }

                $sheet->setCellValue('A'.$numrow, $output->rows[$i]->phone);
                $sheet->setCellValue('B'.$numrow, $output->rows[$i]->name);
                $sheet->setCellValue('C'.$numrow, $output->rows[$i]->name);
                $sheet->setCellValue('D'.$numrow, $this->tglbln($output->rows[$i]->date_appointment));
                $sheet->setCellValue('E'.$numrow, $output->rows[$i]->health_facility_item_name);
                $sheet->setCellValue('F'.$numrow, $output->rows[$i]->doctor_in_charge_name);
                $sheet->setCellValue('G'.$numrow, $output->rows[$i]->hour_start.' - '.$output->rows[$i]->hour_end);
                $no++;
                $numrow++;
            }

            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->setTitle('Appointment');
            $writer = new Xlsx($spreadsheet);
            ob_end_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Appointment_'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            ob_end_clean();
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    /**
     * Appointment API
     * --------------------
     * @param: norm
     * @param: name
     * @param: birth_date
     * @param: birth_place
     * @param: sex
     * @param: phone
     * @param: date_appointment
     * @param: doctor_schedule_item_id
     * @param: queue
     * --------------------
     * @method : POST
     * @link: version/mr/appointment
     **/

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('norm', 'norm', 'trim|max_length[12]');
            $this->form_validation->set_rules('name', 'name', 'trim|max_length[100]');
            $this->form_validation->set_rules('birth_date', 'birth_date', 'trim');
            $this->form_validation->set_rules('birth_place', 'birth_place', 'trim|max_length[50]');
            $this->form_validation->set_rules('sex', 'sex', 'trim|max_length[1]');
            $this->form_validation->set_rules('phone', 'phone', 'trim|max_length[15]');
            $this->form_validation->set_rules('date_appointment', 'date_appointment', 'trim|required');
            $this->form_validation->set_rules('doctor_schedule_item_id', 'doctor_schedule_item_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('queue', 'queue', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                // Check doctor_schedule_item_id
                $doctor_schedule_item = $this->Doctor_schedule_item->read($dt_post['doctor_schedule_item_id'], false); 
                if($doctor_schedule_item != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $doctor_schedule = $this->Doctor_schedule->read($doctor_schedule_item[0]->doctor_schedule_id,false);
                    $health_facility_item = $this->Health_facility_item->read($doctor_schedule[0]->health_facility_item_id,false);
                    $health_facility = $this->Health_facility->read($health_facility_item[0]->health_facility_id,false);
                    $dt_appointment = array(
                        'institute_branch_id'       => $institute_branch_id,
                        'health_facility_id'        => $health_facility[0]->id,
                        'health_facility_item_id'   => $doctor_schedule[0]->health_facility_item_id,
                        'doctor_in_charge_id'       => $doctor_schedule[0]->doctor_id,
                        'date_appointment'          => $dt_post['date_appointment'],
                        'date_added'                => date("Y-m-d H:i:s",time()),
                        'flag_register'             => '1',
                    );

                    //NEW COUNTER
                    $codecounter = array(
                        'var_1' => $doctor_schedule[0]->health_facility_item_id,
                        'var_2' => $doctor_schedule[0]->doctor_id,
                        'var_3' => $dt_post['doctor_schedule_item_id'],
                        'var_4' => 1,
                        'var_5' => $dt_post['date_appointment']
                    );
                    $this->load->library("transaction");
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $checkcounter['counter_queue'];
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => $doctor_schedule[0]->health_facility_item_id,
                            'var_2' => $doctor_schedule[0]->doctor_id,
                            'var_3' => $dt_post['doctor_schedule_item_id'],
                            'var_4' => 1,
                            'var_5' => $dt_post['date_appointment'],
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = $checkcounter['counter_queue'];
                    }
                    //NEW COUNTER

                    // NORM OR NAME
                    if(isset($dt_post['norm'])) {
                        $patient = $this->Patient->read($dt_post['norm'],false);
                        if($patient != []) {
                            $dt_appointment['norm'] = $dt_post['norm'];
                            $dt_appointment['name'] = $patient[0]->name;
                            $dt_appointment['birth_date'] = $patient[0]->birth_date;
                            $dt_appointment['birth_place'] = $patient[0]->birth_place;
                            $dt_appointment['sex'] = $patient[0]->sex;
                            $dt_appointment['phone'] = $patient[0]->phone;
                        } else {
                            $this->REST_Return(404, 'Wrong norm!');
                            return;
                        }
                    } else if(isset($dt_post['name'])) {
                        $dt_appointment['name'] = $dt_post['name'];
                        $dt_appointment['birth_date'] = isset($dt_post['birth_date']) ? $dt_post['birth_date'] : '';
                        $dt_appointment['birth_place'] = isset($dt_post['birth_place']) ? $dt_post['birth_place'] : '';
                        $dt_appointment['sex'] = isset($dt_post['sex']) ? $dt_post['sex'] : '';
                        $dt_appointment['phone'] = isset($dt_post['phone']) ? $dt_post['phone'] : '';
                    }
                    $id = $this->$model->create($dt_appointment);
                    
                    // INIT_APPOINTMENT_SCHEDULE
                    $dt_appointment_schedule = array(
                        'appointment_id'            => $id,
                        'doctor_schedule_item_id'   => $dt_post['doctor_schedule_item_id'],
                        'date_added'                => date("Y-m-d H:i:s",time()),
                        'queue'                     => $code,
                    );
                    $this->Appointment_schedule->create($dt_appointment_schedule);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong doctor_schedule_item_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * Appointment API
     * --------------------
     * @param: norm
     * @param: name
     * @param: birth_date
     * @param: birth_place
     * @param: sex
     * @param: phone
     * @param: flag_register
     * --------------------
     * @method : POST
     * @link: version/mr/appointment
     **/

    public function update_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('norm', 'norm', 'trim|max_length[12]');
            $this->form_validation->set_rules('name', 'name', 'trim|max_length[100]');
            $this->form_validation->set_rules('birth_date', 'birth_date', 'trim');
            $this->form_validation->set_rules('birth_place', 'birth_place', 'trim|max_length[50]');
            $this->form_validation->set_rules('sex', 'sex', 'trim|max_length[1]');
            $this->form_validation->set_rules('phone', 'phone', 'trim|max_length[15]');
            $this->form_validation->set_rules('flag_register', 'flag_register', 'trim|max_length[1]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $appointment = $this->$model->read($id);
                if($appointment != []) {
                    $dt_appointment = array(
                        'id'                => $id,
                        'norm'              => isset($dt_put['norm']) ? $dt_put['norm'] : $appointment[0]->norm,
                        'name'              => isset($dt_put['name']) ? $dt_put['name'] : $appointment[0]->name,
                        'birth_date'        => isset($dt_put['birth_date']) ? $dt_put['birth_date'] : $appointment[0]->birth_date,
                        'birth_place'       => isset($dt_put['birth_place']) ? $dt_put['birth_place'] : $appointment[0]->birth_place,
                        'sex'               => isset($dt_put['sex']) ? $dt_put['sex'] : $appointment[0]->sex,
                        'phone'             => isset($dt_put['phone']) ? $dt_put['phone'] : $appointment[0]->phone,
                        'flag_register'     => isset($dt_put['flag_register']) ? $dt_put['flag_register'] : $appointment[0]->flag_register,
                    );
                    $id = $this->$model->update($id, $dt_appointment);
                } else {
                    $this->REST_Return(404, 'Appointment with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

	public function delete_delete($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules($this->delete_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->$model->read($id) != []) {
                    $want_to_delete = array(
                        $this->$model->primaryKey => $id,
                    );
                    $dt_appointment = $this->$model->read($id)[0];
                    if ($dt_appointment->nobooking) {
                        $this->load->library('Vclaim_antrol','vclaim_antrol');
                        $DataPostx = '{
                        "kodebooking":"'.$dt_appointment->nobooking.'",
                        "keterangan":"batal '.$dt_appointment->nobooking.'"}';
                        $this->vclaim_antrol->request("/antrean/batal", "POST", $DataPostx);
                        $db_antrol = $this->load->database('antrol', true);
                        $db_antrol->where('kodebooking', $dt_appointment->nobooking);
                        $db_antrol->update('bpjs_jkn_booking_sh_md', array('is_cancel'=>1,'date_cancel'=>date('Y-m-d H:i:s'),'desc_cancel'=>'Batal Admisi'));
                    }
					foreach ($dt_put as $key => $value) {
						$want_to_delete[$key] = $value;
					}

                    $want_to_delete['user_deleted'] = $user_id;
                    $want_to_delete['date_deleted'] = date('Y-m-d H:i:s');
                    $id = $this->$model->update($id, $want_to_delete);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    function tglbln($tanggal)
	{
		$str    = explode('-',$tanggal);
		$bulan  = array(
            '01'=>'Januari',
            '02'=>'Februari',
            '03'=>'Maret',
            '04'=>'April',
            '05'=>'Mei',
            '06'=>'Juni',
            '07'=>'Juli',
            '08'=>'Agustus',
            '09'=>'September',
            '10'=>'Oktober',
            '11'=>'November',
            '12'=>'Desember'
		);
		return $str['2']." ".$bulan[$str['1']]." ".$str['0'];
	}
}