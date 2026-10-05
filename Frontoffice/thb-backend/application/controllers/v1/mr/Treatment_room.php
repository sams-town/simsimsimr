<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Treatment_room extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/inpatient/Vital_signs_monitoring_model", "Vital_signs_monitoring");
        $this->load->model("$this->directory/inpatient/Early_warning_score_model", "Early_warning_score");
        $this->load->model("$this->directory/inpatient/Early_warning_score_item_model", "Early_warning_score_item");
        $this->load->model("$this->directory/inpatient/Temperature_chart_model", "Temperature_chart");
        $this->load->model("$this->directory/inpatient/Temperature_chart_item_model", "Temperature_chart_item");
        $this->load->model("$this->directory/inpatient/Pews_model", "Pews");
        $this->load->model("$this->directory/inpatient/Pews_item_model", "Pews_item");
        $this->load->model("$this->directory/inpatient/Consultation_model", "Consultation");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Nurse_model", "Nurse");
        $this->load->model("$this->directory/Humpty_dumpty_scale_fall_risk_model", "Humpty_dumpty_scale_fall_risk");
        $this->load->model("$this->directory/Morse_scale_fall_risk_model", "Morse_scale_fall_risk");
        $this->load->model("$this->directory/Soap_inpatient_model", "Soap_inpatient");
        $this->load->model("v1/health_facility/Bed_model", "Bed");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/health_facility/Room_setting_model", "Room_setting");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/hr/Position_model", "Position");
        $this->load->model("v1/tr/Transaction_bed_model", "Transaction_bed");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        // Load Helper
        $this->load->helper('ews');
        $this->load->helper('utils');

        $this->transfer_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'bed_id', 'label' => 'bed_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'nurse_id', 'label' => 'nurse_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );
        
        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        $treatment_room_id = $id;
        
        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;

        $select = 'a.*, r.id as registration_id, r.type_visit_id, rd.type_emergency_id,
        e.name as doctor_in_charge_name,
        e2.name as doctor_in_charge_name_2,
        e3.name as doctor_in_charge_name_3,
        e4.name as doctor_in_charge_name_4,
        e5.name as doctor_in_charge_name_5,
        e6.name as employee_nurse_name,
        e7.name as employee_doctor_name
        ';
        $where = array();
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('employee e2', 'a.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            array('employee e3', 'a.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            array('employee e4', 'a.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            array('employee e5', 'a.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('employee e6', 'a.employee_nurse_id = e6.id', 'LEFT'),
            array('employee e7', 'a.employee_doctor_id = e7.id', 'LEFT'),
        );

        if ($treatment_room_id !== 0 && $treatment_room_id !== "0") {
            $output = $this->$model->select($treatment_room_id, $select, $where, $join);
        } else {
            $where['a.registration_destination_id'] = $registration_destination_id;
            $output = $this->$model->select(0, $select, $where, $join);
        }

        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_id = $output[$i]->registration_destination_id;
            $registration_id = $output[$i]->registration_id;

            $select = 'a.id';
            $where = array(
                'a.registration_id' => $registration_id,
            );
            $this->Registration_destination->whereNotInFields['a.id'] = $registration_destination_id;
            $past_registration_destination = $this->Registration_destination->select(0, $select, $where);

            $past_registration_destination_id = $past_registration_destination[0]->id;

            $select = 'a.condition, a.indication, a.plan';
            $where = array(
                'a.registration_destination_id' => $past_registration_destination_id
            );
            $soap_inpatient = $this->Soap_inpatient->select(0, $select, $where);

            $condition = $soap_inpatient[0]->condition;
            $indication = $soap_inpatient[0]->indication;
            $plan = $soap_inpatient[0]->plan;

            $output[$i]->condition = $condition;
            $output[$i]->indication = $indication;
            $output[$i]->plan = $plan;

            $from_unit = $output[$i]->from_unit;

            $select = 'a.id as health_facility_item_id, a.name as health_facility_item_name, hf.id as health_facility_id, hf.name as health_facility_name';
            $where = array();
            $join = array(
                array('health_facility hf', 'a.health_facility_id = hf.id', 'INNER'),
            );
            $health_facility_item = $this->Health_facility_item->select($from_unit, $select, $where, $join);

            $output[$i]->past_health_facility_id = $health_facility_item[0]->health_facility_id;
            $output[$i]->past_health_facility_name = $health_facility_item[0]->health_facility_name;
            $output[$i]->past_health_facility_item_id = $health_facility_item[0]->health_facility_item_id;
            $output[$i]->past_health_facility_item_name = $health_facility_item[0]->health_facility_item_name;
        }

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    
        $this->$model->whereFields = $q_strings;
        $output = $this->$model->read_ssr($id);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function transfer_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->transfer_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        
        $select = 'a.employee_id';
        $user = $this->User->select($user_id, $select);
        $employee_added = $user[0]->employee_id;

        $registration_destination_id = $dt_post['registration_destination_id'];
        $bed_id = $dt_post['bed_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        $doctor_in_charge_id_2 = $dt_post['doctor_in_charge_id_2'];
        $doctor_in_charge_id_3 = $dt_post['doctor_in_charge_id_3'];
        $doctor_in_charge_id_4 = $dt_post['doctor_in_charge_id_4'];
        $doctor_in_charge_id_5 = $dt_post['doctor_in_charge_id_5'];
        $doctor_in_charge_id_6 = $dt_post['doctor_in_charge_id_6'];
        $doctor_in_charge_id_7 = $dt_post['doctor_in_charge_id_7'];
        $doctor_in_charge_id_8 = $dt_post['doctor_in_charge_id_8'];
        $doctor_in_charge_id_9 = $dt_post['doctor_in_charge_id_9'];
        $doctor_in_charge_id_10 = $dt_post['doctor_in_charge_id_10'];
        $doctor_in_charge_id_11 = $dt_post['doctor_in_charge_id_11'];
        $doctor_in_charge_id_12 = $dt_post['doctor_in_charge_id_12'];
        $nurse_id = clean_postput($dt_post['nurse_id']);
        // $from_unit = $dt_post['from_unit'];
        // $to_unit = $dt_post['to_unit'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];
        $employee_doctor_id = $dt_post['employee_doctor_id'];

        if ($nurse_id == null) {
            $this->REST_Return(400, 'Asesmen perawat belum dibuat');
            return;
        }

        $select = 'a.id, a.bed_id, b.room_id, p.birth_date, a.health_facility_item_id, hfi.name as health_facility_item_name, p.name as patient_name, h.name as patient_alias, r.norm';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
            array('bed b', 'a.bed_id = b.id', 'INNER'),

        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $health_facility_item_past_id = $registration_destination[0]->health_facility_item_id;
        $health_facility_item_id_ok = 'cd72c7f0-920b-4260-b602-541d0a15305a';
        $health_facility_item_past_name = $registration_destination[0]->health_facility_item_name;
        $norm = $registration_destination[0]->norm;
        $patient_alias = $registration_destination[0]->patient_alias;
        $patient_name = $registration_destination[0]->patient_name;
        $patient_birth_date = $registration_destination[0]->birth_date;
        $patient_age = count_age($patient_birth_date);

        $employee = $this->Employee->select_id($doctor_in_charge_id);
        
        if ($employee == "") {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }
                
        if ($doctor_in_charge_id_2 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_2);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_2);
                return;
            }
        }
        
        if ($doctor_in_charge_id_3 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_3);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_3);
                return;
            }
        }
        
        if ($doctor_in_charge_id_4 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_4);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_4);
                return;
            }
        }

        if ($doctor_in_charge_id_5 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_5);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_5);
                return;
            }
        }

        if ($doctor_in_charge_id_6 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_6);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_6);
                return;
            }
        }

        if ($doctor_in_charge_id_7 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_7);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_7);
                return;
            }
        }

        if ($doctor_in_charge_id_8 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_8);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_8);
                return;
            }
        }

        if ($doctor_in_charge_id_9 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_9);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_9);
                return;
            }
        }

        if ($doctor_in_charge_id_10 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_10);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_10);
                return;
            }
        }

        if ($doctor_in_charge_id_11 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_11);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_11);
                return;
            }
        }

        if ($doctor_in_charge_id_12 != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id_12);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_12);
                return;
            }
        }

        if ($health_facility_item_past_id != $health_facility_item_id_ok) {
            if ($nurse_id != null) {
                $select = 'a.id';
                $nurse = $this->Nurse->select($nurse_id, $select);
                
                if (count($nurse) == 0) {
                    $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                    return;
                }
            }
        }

        $select = 'a.id, a.name, a.type_status_bed_id, a.room_id';
        $bed = $this->Bed->select($bed_id, $select);

        if (count($bed) == 0) {
            $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
            return;
        }

        $bed_name = $bed[0]->name;
        $bed_status = $bed[0]->type_status_bed_id;
        $room_id = $bed[0]->room_id;

        $select = 'a.id, a.costing_id, a.service_class_id, a.health_facility_item_id, a.number';
        $room = $this->Room->select($room_id, $select);

        $room_number = $room[0]->number;
        $room_costing_id = $room[0]->costing_id;
        $room_service_class_id = $room[0]->service_class_id;

        $health_facility_item_id = $room[0]->health_facility_item_id;

        $select = 'a.id, a.health_facility_id, a.name';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

        $health_facility_item_name = $health_facility_item[0]->name;
        $health_facility_id = $health_facility_item[0]->health_facility_id;

        if ($health_facility_id != '40f06534-07ec-4221-ad9d-dea08196079f') {
            $this->REST_Return(409, 'cannot be transferred between departements');
            return;
        }

        if ($bed_status == 2 || $bed_status == 3) {
            $this->REST_Return(400, 'bed with id ' . $bed_id . ' is currently not available');
            return;
        }

        // if ($from_unit != null) {
        //     $select = 'a.id';
        //     $from_unit = $this->Health_facility_item->select($from_unit, $select);
        //     if (count($from_unit) == 0) {
        //         $this->REST_Return(404, 'from_unit is not valid');
        //         return;
        //     }
        // }

        // if ($to_unit != null) {
        //     $select = 'a.id';
        //     $to_unit = $this->Health_facility_item->select($to_unit, $select);
        //     if (count($to_unit) == 0) {
        //         $this->REST_Return(404, 'to_unit is not valid');
        //         return;
        //     }
        // }

        if ($employee_nurse_id != null) {
            $select = 'a.id';
            $employee_nurse = $this->Employee->select($employee_nurse_id, $select);
            if (count($employee_nurse) == 0) {
                $this->REST_Return(404, 'employee_nurse_id is not valid');
                return;
            }
        }

        if ($employee_doctor_id != null) {
            $select = 'a.id';
            $employee_doctor = $this->Employee->select($employee_doctor_id, $select);
            if (count($employee_doctor) == 0) {
                $this->REST_Return(404, 'employee_doctor_id is not valid');
                return;
            }
        }

        // $general_condition = $dt_post['general_condition'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $pulse_frequency = $dt_post['pulse_frequency'];
        $breath_frequency = $dt_post['breath_frequency'];
        $oximetry_frequency = $dt_post['oximetry_frequency'];
        $oxygen_type_id = $dt_post['oxygen_type_id'];
        $temperature = $dt_post['temperature'];
        $aware = $dt_post['aware'];
        $eye_score = $dt_post['eye_score'];
        $verbal_score = $dt_post['verbal_score'];
        $motor_score = $dt_post['motor_score'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $skin_color = $dt_post['skin_color'];
        $behavior = $dt_post['behavior'];
        // $sore_score = $dt_post['sore_score'];
        // $risk_falling_arry = implode(',', $dt_post['risk_falling_arry']);
        // $phlebitis = $dt_post['phlebitis'];
        // $decubitus = $dt_post['decubitus'];
        // $follow_up_condition = $dt_post['follow_up_condition'];
        // $performed_actions = $dt_post['performed_actions'];
        $ews_downe_score = $dt_post['ews_downe_score'];
        // $risks_during_transfer = $dt_post['risks_during_transfer'];
        // $anticipation = $dt_post['anticipation'];
        $time_out = $dt_post['time_out'];
        // $arrival_time = $dt_post['arrival_time'];
        // $notes = $dt_post['notes'];
        $mobilization = $dt_post['mobilization'];

        /* Start Transaction */
        $this->db->trans_start();

        // STATUS BED
        // 1 = AVAILABLE
        // 2 = FILLED
        // 3 = LINEN
        // set bed destination status to linen
        $dt_bed = array(
            'id'                  => $bed_id,
            'type_status_bed_id'  => 3,
        );
        $this->Bed->replace($dt_bed);

        $past_bed_id = $registration_destination[0]->bed_id;
        $past_room_id = $registration_destination[0]->room_id;

        if ($past_bed_id == '') {
            $this->REST_Return(400, 'registration_destination is not inpatient');
            return;
        } else {
            // count bed past
            $select = 'a.id, a.name, a.room_id, a.type_status_bed_id';
            $bed_past = $this->Bed->select($past_bed_id, $select);
            if (count($bed_past) == 0) {
                $this->REST_Return(404, 'there is no bed with id ' . $past_bed_id);
                return;
            }

            $select = 'a.id,a.checkin_date';
            $where = array(
                'a.bed_id' => $past_bed_id,
                'a.registration_destination_id' => $registration_destination_id
            );
            $transaction_bed_past = $this->Transaction_bed->select(0, $select, $where);

            $room_id_past = $bed_past[0]->room_id;

            $select = 'a.id, a.costing_id, a.service_class_id, a.name, a.number';
            $room_past = $this->Room->select($room_id_past, $select);

            // check costing
            $costing_id_past = $room_past[0]->costing_id;
            $service_class_id_past = $room_past[0]->service_class_id;

            $select = 'a.id, a.var_1';
            $where = array(
                'costing_id' => $costing_id_past,
                'service_class_id' => $service_class_id_past,
            );
            $costing_services_class_past = $this->Costing_services_class->select(0, $select, $where);

            $price_past = $costing_services_class_past[0]->var_1;

            // Calculate costs bed past
                $checkin   = $transaction_bed_past[0]->checkin_date;
                $checkout  = date('Y-m-d H:i:s',time());
                $start     = new DateTime($checkin);
                $end       = new DateTime($checkout);

                $diff       = $end->diff($start);

                // Hitung total hari (dibulatkan ke atas jika ada jam lebih dari 0)
                $totalDays_past = $diff->days;
                if ($diff->h > 0 || $diff->i > 0) {
                    $totalDays_past += 1; // kalau ada sisa jam/menit, dianggap 1 hari penuh
                }

                // Hitung total harga berdasarkan hari
                $total_price_past = $price_past * $totalDays_past;
            // Calculate costs bed past
            $dt_transaction_bed_past = array(
                'id'                            => $transaction_bed_past[0]->id,
                'var_1'                         => $price_past,
                'var_2'                         => $totalDays_past,
                'var_3'                         => $total_price_past,
                'checkout_date'                 => date('Y-m-d H:i:s',time()),
                'is_non_prorate'                => 1
            );
            $this->Transaction_bed->replace($dt_transaction_bed_past);
        }

        // set bed past status to available
        $dt_bed = array(
            'id'                  => $past_bed_id,
            'type_status_bed_id'  => 1,
        );

        $this->Bed->replace($dt_bed);

        // Set room setting to default
        $this->Room_setting->reset_default_if_empty($past_room_id);

        // update bed in registration_destination
        $dt_registration_destination = array(
            'health_facility_item_id' => $health_facility_item_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'doctor_in_charge_id_2' => $doctor_in_charge_id_2,
            'doctor_in_charge_id_3' => $doctor_in_charge_id_3,
            'doctor_in_charge_id_4' => $doctor_in_charge_id_4,
            'doctor_in_charge_id_5' => $doctor_in_charge_id_5,
            'doctor_in_charge_id_6' => $doctor_in_charge_id_6,
            'doctor_in_charge_id_7' => $doctor_in_charge_id_7,
            'doctor_in_charge_id_8' => $doctor_in_charge_id_8,
            'doctor_in_charge_id_9' => $doctor_in_charge_id_9,
            'doctor_in_charge_id_10' => $doctor_in_charge_id_10,
            'doctor_in_charge_id_11' => $doctor_in_charge_id_11,
            'doctor_in_charge_id_12' => $doctor_in_charge_id_12,
            'bed_id' => $bed_id,
            'is_transfer' => 0
        );

        $this->Registration_destination->update_v2($registration_destination_id, $dt_registration_destination);

        // INIT_TRANSACTION_BED
        $dt_transaction_bed = array(
            'registration_destination_id'   => $registration_destination_id,
            'bed_id'                        => $bed_id,
            'bed_name'                      => $bed_name,
            'costing_id'                    => $room_costing_id,
            'service_class_id'              => $room_service_class_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'checkin_date'                  => date('Y-m-d H:i:s',time())
        );

        $this->Transaction_bed->create($dt_transaction_bed);

        // STATUS TREATMENT_ROOM
        // 1 = IN
        // 2 = OUT
        $select = 'a.id';
        $where = array('registration_destination_id' => $registration_destination_id, 'status' => 1);
        $treatment_room_out = $this->$model->select(0, $select, $where);

        for ($i=0; $i < count($treatment_room_out); $i++) { 
            $treatment_room_out_id = $treatment_room_out[$i]->id;
            
            $dt_treatment_room_out = array(
                'status'        => 2,
                'date_updated'  => date('Y-m-d H:i:s', time()),
            );

            $this->$model->update_v2($treatment_room_out_id, $dt_treatment_room_out);
        }

        $dt_treatment_room = array(
            'registration_destination_id'   => $registration_destination_id,
            'bed_id'                        => $bed_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'doctor_in_charge_id_2'         => $doctor_in_charge_id_2,
            'doctor_in_charge_id_3'         => $doctor_in_charge_id_3,
            'doctor_in_charge_id_4'         => $doctor_in_charge_id_4,
            'doctor_in_charge_id_5'         => $doctor_in_charge_id_5,
            'doctor_in_charge_id_6'         => $doctor_in_charge_id_6,
            'doctor_in_charge_id_7'         => $doctor_in_charge_id_7,
            'doctor_in_charge_id_8'         => $doctor_in_charge_id_8,
            'doctor_in_charge_id_9'         => $doctor_in_charge_id_9,
            'doctor_in_charge_id_10'         => $doctor_in_charge_id_10,
            'doctor_in_charge_id_11'         => $doctor_in_charge_id_11,
            'doctor_in_charge_id_12'         => $doctor_in_charge_id_12,
            'from_unit'                     => $health_facility_item_past_id,
            'to_unit'                       => $health_facility_item_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'employee_doctor_id'            => $employee_doctor_id,
            // 'notes'                         => $notes,
            // 'general_condition'             => $general_condition,
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse_frequency'               => $pulse_frequency,
            'breath_frequency'              => $breath_frequency,
            'temperature'                   => $temperature,
            'oxygen_saturation'             => $oximetry_frequency,
            'oxygen_type_id'                => $oxygen_type_id,
            'weight'                        => $weight,
            'height'                        => $height,
            'skin_color'                    => $skin_color,
            'behavior'                      => $behavior,
            // 'sore_score'                    => $sore_score,
            // 'risk_falling_type_id'          => $risk_falling_type_id,
            // 'phlebitis_type_id'             => $phlebitis_type_id,
            // 'phlebitis'                     => $phlebitis,
            // 'decubitus_type_id'             => $decubitus_type_id,
            // 'decubitus'                     => $decubitus,
            // 'follow_up_condition'           => $follow_up_condition,
            // 'performed_actions'             => $performed_actions,
            'ews_downe_score'               => $ews_downe_score,
            // 'risks_during_transfer'         => $risks_during_transfer,
            // 'anticipation'                  => $anticipation,
            'mobilization'                  => $mobilization,
            'time_out'                      => $time_out,
            // 'arrival_time'                  => $arrival_time,
            'status'                        => 1,
            'date_added'                    => date('Y-m-d H:i:s', time()),
            'date_updated'                  => date('Y-m-d H:i:s', time()),
        );

        $id = $this->$model->create($dt_treatment_room);

        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_added,
            'source_id'                     => $id,
            'observation_date'              => date('Y-m-d H:i:s'),
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse'                         => $pulse_frequency,
            'temperature'                   => $temperature,
            'respiratory_rate'              => $breath_frequency,
            'oxygen_saturation'             => $oximetry_frequency,
            'oxygen_type_id'                => $oxygen_type_id,
            'weight'                        => $weight,
            'height'                        => $height,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $this->Vital_signs_monitoring->create($dt_vital_signs_monitoring);
        
        if ($patient_age > 18) {
            // INPUT EWS
            $select = 'a.id';
            $where = array(
                'registration_destination_id' => $registration_destination_id, 
                'observation_date' => date('Y-m-d'), 
                'is_delete' => 0
            );
            $early_warning_score = $this->Early_warning_score->select(0, $select, $where);

            if (count($early_warning_score) == 0) {
                $dt_early_warning_score = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'observation_date'              => date('Y-m-d'),
                    'user_added'                    => $user_id,
                    'date_added'                    => date('Y-m-d H:i:s',time()),
                    'date_updated'                  => date('Y-m-d H:i:s',time()),
                );

                $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
            } else {
                $early_warning_score_id = $early_warning_score[0]->id;
            }

            $observation_time = date('H:i:s');
            $blood_pressure = (float) $systolic;
            $pulse = (float) $pulse_frequency;
            $breathing = (float) $breath_frequency;
            $temperature = (float) $temperature;
            $oxygen_saturation = (float) $oximetry_frequency;
            $oxygen = $oxygen_type_id != 2 ? 2 : 0;

            $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
        
            $dt_early_warning_score_item = array(
                'early_warning_score_id' => $early_warning_score_id,
                'employee_nurse_id'      => $employee_added,
                'source_id'              => $id,
                'observation_time'       => $observation_time,
                'aware'                  => $aware,
                'eye_score'              => $eye_score,
                'verbal_score'           => $verbal_score,
                'motor_score'            => $motor_score,
                'blood_pressure'         => $blood_pressure,
                'pulse'                  => $pulse,
                'breathing'              => $breathing,
                'temperature'            => $temperature,
                'oxygen_saturation'      => $oxygen_saturation,
                'oxygen'                 => $oxygen,
                'total'                  => $ews_score,
                'user_added'             => $user_id,
                'date_added'             => date('Y-m-d H:i:s',time()),
                'date_updated'           => date('Y-m-d H:i:s',time()),
            );

            $select = 'a.id';
            $where = array(
                'a.early_warning_score_id' => $early_warning_score_id,
                'a.source_id' => $nurse_id,
                'a.is_delete' => 0,
            );
            $ewsi = $this->Early_warning_score_item->select(0, $select, $where);
            if (count($ewsi) == 0) {
                $this->Early_warning_score_item->create($dt_early_warning_score_item);
            } else {
                $ewsi_id = $ewsi[0]->id;
                unset($dt_early_warning_score_item['user_added']);
                unset($dt_early_warning_score_item['date_added']);
                $this->Early_warning_score_item->update_v2($ewsi_id, $dt_early_warning_score_item);
            }
        } else {
            // INPUT PEWS
            $observation_date = date('Y-m-d');
            $observation_time = date('H:i:s');
            $breathing = (float) $breath_frequency;
            $oxygen = (int) $oxygen_type_id;
            $oxygen_saturation = (float) $oximetry_frequency;
            $pulse = (float) $pulse_frequency;
            $skin_color = (int) $skin_color;
            $systolic = (float) $systolic;
            $behavior = (int) $behavior;

            $data_pews = array(
                'birth_date'        => $patient_birth_date,
                'source_id'         => $id,
                'employee_nurse_id' => $employee_added,
                'user_id'           => $user_id,
                'observation_time'  => $observation_time,
                'breathing'         => $breathing,
                'oxygen'            => $oxygen,
                'oxygen_saturation' => $oxygen_saturation,
                'pulse'             => $pulse,
                'skin_color'        => $skin_color,
                'systolic'          => $systolic,
                'behavior'          => $behavior,
            );

            $this->Pews->source_create($registration_destination_id, $observation_date, $data_pews);
        }
        
        // INPUT TEMPERATURE CHART
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'monitoring_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $temperature_chart = $this->Temperature_chart->select(0, $select, $where);
        if (count($temperature_chart) == 0) {
            $dt_temperature_chart = array(
                'registration_destination_id' => $registration_destination_id,
                'employee_nurse_id'           => $employee_nurse_id,
                'monitoring_date'             => date('Y-m-d'),
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s',time()),
                'date_updated'                => date('Y-m-d H:i:s',time()),
            );
        
            $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
        } else {
            $temperature_chart_id = $temperature_chart[0]->id;
        }

        if ($health_facility_item_past_id != $health_facility_item_id_ok) {
            $dt_temperature_chart_item = array(
                'temperature_chart_id'  => $temperature_chart_id,
                'observation_hours'     => date('H:i:s'),
                'source_id'             => $id,
                'rr'                    => $breath_frequency,
                'pulse'                 => $pulse_frequency,
                'temperature'           => $temperature,
                'user_added'            => $user_id,
                'date_added'            => date('Y-m-d H:i:s',time()),
                'date_updated'          => date('Y-m-d H:i:s',time()),
            );
        
            $select = 'a.id';
            $where = array(
                'a.temperature_chart_id' => $temperature_chart_id,
                'a.source_id' => $nurse_id,
                'a.is_delete' => 0,
            );
            $tci = $this->Temperature_chart_item->select(0, $select, $where);
            if (count($tci) == 0) {
                $this->Temperature_chart_item->create($dt_temperature_chart_item);
            } else {
                $tci_id = $tci[0]->id;
                unset($dt_temperature_chart_item['user_added']);
                unset($dt_temperature_chart_item['date_added']);
                $this->Temperature_chart_item->update_v2($tci_id, $dt_temperature_chart_item);
            }

            // NURSE PAIN SCORE & SCALE RISK
            $pain_score = $dt_post['pain_score'];

            $dt_functional = array(
                'pain_score'    => $pain_score,
            );

            $st_functional = $this->Nurse->update_v2($nurse_id, $dt_functional);
            if ($st_functional < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }

            // FALL RISK
            if ($patient_age < 18) {
                $age = $dt_post['humpty_dumpty']['age'] ?? null;
                $gender = $dt_post['humpty_dumpty']['gender'] ?? null;
                $diagnosis = $dt_post['humpty_dumpty']['diagnosis'] ?? null;
                $cognitive_impairment = $dt_post['humpty_dumpty']['cognitive_impairment'] ?? null;
                $environmental_factors = $dt_post['humpty_dumpty']['environmental_factors'] ?? null;
                $surgery_sedation_anesthesia = $dt_post['humpty_dumpty']['surgery_sedation_anesthesia'] ?? null;
                $use_of_medication = $dt_post['humpty_dumpty']['use_of_medication'] ?? null;
                $total_score = $dt_post['humpty_dumpty']['total_score'] ?? null;
                
                $dt_humpty_dumpty = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'nurse_id'                      => $nurse_id,
                    'age'                           => $age,
                    'gender'                        => $gender,
                    'diagnosis'                     => $diagnosis,
                    'cognitive_impairment'          => $cognitive_impairment,
                    'environmental_factors'         => $environmental_factors,
                    'surgery_sedation_anesthesia'   => $surgery_sedation_anesthesia,
                    'use_of_medication'             => $use_of_medication,
                    'total_score'                   => $total_score,
                    'user_added'                    => $user_id,
                    'user_updated'                  => $user_id,
                    'date_added'                    => date('Y-m-d H:i:s'),
                    'date_updated'                  => date('Y-m-d H:i:s'),
                );

                $select = 'a.id';
                $where = array(
                    'a.nurse_id' => $nurse_id
                );
                $humpty_dumpty = $this->Humpty_dumpty_scale_fall_risk->select(0, $select, $where);
                if (count($humpty_dumpty) > 0) {
                    $humpty_dumpty_id = $humpty_dumpty[0]->id;
                    unset($dt_humpty_dumpty['user_added']);
                    unset($dt_humpty_dumpty['date_added']);
                    $st_humpty_dumpty = $this->Humpty_dumpty_scale_fall_risk->update_v2($humpty_dumpty_id, $dt_humpty_dumpty);
                    if ($st_humpty_dumpty < 0) {
                        $this->REST_Return(422, 'something when wrong');
                        return;
                    }
                } else {
                    $this->Humpty_dumpty_scale_fall_risk->create($dt_humpty_dumpty);
                }
            } else {
                $history_of_falls = $dt_post['morse']['history_of_falls'];
                $secondary_diagnosis = $dt_post['morse']['secondary_diagnosis'];
                $walking_ambulation = $dt_post['morse']['walking_ambulation'];
                $side_effects_of_falling = $dt_post['morse']['side_effects_of_falling'];
                $way_of_walking = $dt_post['morse']['way_of_walking'];
                $mental_status = $dt_post['morse']['mental_status'];
                $total_score = $dt_post['morse']['total_score'];

                $dt_morse = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'nurse_id'                      => $nurse_id,
                    'history_of_falls'              => $history_of_falls,
                    'secondary_diagnosis'           => $secondary_diagnosis,
                    'walking_ambulation'            => $walking_ambulation,
                    'side_effects_of_falling'       => $side_effects_of_falling,
                    'way_of_walking'                => $way_of_walking,
                    'mental_status'                 => $mental_status,
                    'total_score'                   => $total_score,
                    'user_added'                    => $user_id,
                    'user_updated'                  => $user_id,
                    'date_added'                    => date('Y-m-d H:i:s'),
                    'date_updated'                  => date('Y-m-d H:i:s'),
                );

                $select = 'a.id';
                $where = array(
                    'a.nurse_id' => $nurse_id,
                );
                $morse = $this->Morse_scale_fall_risk->select(0, $select, $where);
                if (count($morse) > 0) {
                    $morse_id = $morse[0]->id;
                    unset($dt_morse['user_added']);
                    unset($dt_morse['date_added']);
                    $st_morse = $this->Morse_scale_fall_risk->update_v2($morse_id, $dt_morse);
                    if ($st_morse < 0) {
                        $this->REST_Return(422, 'something when wrong');
                        return;
                    }
                } else {
                    $this->Morse_scale_fall_risk->create($dt_morse);
                }
            }
        }

        // NOTIFICATIONS
        // cb8fea0f-ce57-4596-b901-4671c8a2ef29 RUANG JIWA
        // 7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c ICCU
        // ff1f252e-daab-4bdb-b308-e9f4cb11a47d ICU ISOLASI
        // 5515af72-6dcb-415d-89e4-2433e7b0149d NICU
        // e66e6ed4-4ef4-4375-a6d0-0445e3ec3039 PICU 
        // 05079cd9-07c6-4cf9-beb0-271eb301b7c1 PERINATOLOGI
        if ($health_facility_item_id == 'cb8fea0f-ce57-4596-b901-4671c8a2ef29') { // RUANG JIWA
            $position = array(
                (object) array(
                    'id' => 'c1002e5b-5f1f-4837-af45-122673052259' // G. ANAK
                ),
            );
        } elseif ($health_facility_item_id == '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c' || 
        $health_facility_item_id == 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d' ||
        $health_facility_item_id == '5515af72-6dcb-415d-89e4-2433e7b0149d' ||
        $health_facility_item_id == 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039' ||
        $health_facility_item_id == '05079cd9-07c6-4cf9-beb0-271eb301b7c1') {
            $position = array(
                (object) array(
                    'id' => '6c7d7e53-ff24-4246-85b2-07ea77ac801f' // ICU
                ),
            );
        } else {
            $select = 'a.id';
            $where = array(
                'a.health_facility_item_id' => $health_facility_item_id,
            );
            $position = $this->Position->select(0, $select, $where);
        }
        
        if (count($position) > 0) {
            $position_id = $position[0]->id;

            // TRANSFER PASIEN
            // PASIEN A NAIK DARI B KE C RUANGAN D DENGAN BED NOMOR E
            $title = 'Transfer Pasien';
            $message = "Pasien $patient_alias $patient_name ($norm) pindah dari $health_facility_item_past_name ke $health_facility_item_name ruangan $room_number dengan bed nomor $bed_name";

            $this->Notifications->createNotification(null, $position_id, $title, $message, 'transfer', ['treatment_room_id' => $id]);

            // GIZI
            $this->Notifications->createNotification(null, '8ca42f62-85bb-45f8-bf32-152a792e0f40', $title, $message, 'transfer', ['treatment_room_id' => $id]);
        }

        for ($i=2; $i < 13; $i++) { 
            $var_1 = "doctor_in_charge_id_$i";

            if ($$var_1 != '' && $$var_1 != null) {
                $select = 'a.id';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.doctor_in_charge_id' => $doctor_in_charge_id,
                    'a.consultant_id' => $$var_1,
                );
                $consultation = $this->Consultation->select(0, $select, $where);
                if (count($consultation) == 0) {
                    $dt_consultation = array(
                        'registration_destination_id' => $registration_destination_id,
                        'doctor_in_charge_id' => $doctor_in_charge_id,
                        'consultant_id' => $$var_1,
                        'consultation_type_id' => 1,
                        'user_added' => $user_id,
                        'date_added' => date('Y-m-d H:i:s'),
                    );

                    $this->Consultation->create($dt_consultation);
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_treatment_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create treatment_room', $response_data);
            return;
        }

        // REGISTRATION_INSURANCE
        // for ($a=0; $a < count($dt_post['insurances']); $a++) { 
        //     $insurances_arr = $dt_post['insurances'];

        //     $insurance_id = $insurances_arr[$a]['insurance_id'];
        //     $number_insurance = $insurances_arr[$a]['number_insurance'];
        //     $expired = $insurances_arr[$a]['expired'];

        //     $dt_registration_insurance = array(
        //         'registration_destination_id'   => $id,
        //         'insurance_id'                  => $insurance_id,
        //         'number_insurance'              => $number_insurance,
        //         'expired'                       => $expired,
        //     );

        //     if ($insurance_id != '1') {
        //         $company_id = $insurances_arr[$a]['company_id'];
        //         $service_class_id = $insurances_arr[$a]['service_class_id'];

        //         if ($company_id == null) {
        //             $this->REST_Return(400, 'field company_id in insurances is required if insurance_id not 1');
        //             return;
        //         }

        //         if ($service_class_id == null) {
        //             $this->REST_Return(400, 'field service_class_id in insurances is required if insurance_id not 1');
        //             return;
        //         }

        //         $company = $this->Company->select_id($company_id);
        //         if ($company == '') {
        //             $this->REST_Return(404, 'there is no company with id ' . $company_id);
        //             return;
        //         }

        //         $select = 'a.id, a.name';
        //         $service_class = $this->Service_class->select($service_class_id, $select);
        //         if (count($service_class) == 0) {
        //             $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
        //             return;
        //         }

        //         $service_class_name = $service_class[0]->name;

        //         $dt_registration_insurance['company_id'] = $company_id;
        //         $dt_registration_insurance['service_class_id'] = $service_class_id;
        //         $dt_registration_insurance['service_class_name'] = $service_class_name;
        //     }

        //     /* Start Transaction */
        //     $this->db->trans_start();

        //     $this->Registration_insurance->replace($dt_registration_insurance);

        //     $this->db->trans_complete();
        //     /* End Transaction */

        //     if ($this->db->trans_status() === false) {
        // $this->db->trans_rollback();
        //         $response_data = array(
        //             'error'     => $this->db->error(),
        //             'data'      => $dt_registration_insurance,
        //             'query'     => $this->db->last_query()
        //         );
        //         $this->REST_Return(500, 'error while trying to create registration_insurance', $response_data);
        //         return;
        //     }
        // }
        
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $treatment_room_id = $id;
    
        if ($treatment_room_id === 0 || $treatment_room_id === "0") {
            $this->REST_Return(400, 'treatment_room_id is required');
            return;
        }

        $select = 'a.id';
        $treatment_room = $this->$model->select($treatment_room_id, $select);
        if (count($treatment_room) == 0) {
            $this->REST_Return(404, 'treatment_room_id is invalid');
            return;
        }

        $general_condition = $dt_put['general_condition'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $temperature = $dt_put['temperature'];
        $sore_score = $dt_put['sore_score'];
        $risk_falling_arry = implode(',', $dt_put['risk_falling_arry']);
        $phlebitis = $dt_put['phlebitis'];
        $decubitus = $dt_put['decubitus'];
        $follow_up_condition = $dt_put['follow_up_condition'];
        $performed_actions = $dt_put['performed_actions'];
        $ews_downe_score = $dt_put['ews_downe_score'];
        $risks_during_transfer = $dt_put['risks_during_transfer'];
        $anticipation = $dt_put['anticipation'];
        $time_out = $dt_put['time_out'];
        $arrival_time = $dt_put['arrival_time'];
        $notes = $dt_put['notes'];

        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_treatment_room = array(
            'general_condition'     => $general_condition,
            'systolic'              => $systolic,
            'diastolic'             => $diastolic,
            'pulse_frequency'       => $pulse_frequency,
            'breath_frequency'      => $breath_frequency,
            'temperature'           => $temperature,
            'sore_score'            => $sore_score,
            'risk_falling_arry'     => $risk_falling_arry,
            'phlebitis'             => $phlebitis,
            'decubitus'             => $decubitus,
            'follow_up_condition'   => $follow_up_condition,
            'performed_actions'     => $performed_actions,
            'ews_downe_score'       => $ews_downe_score,
            'risks_during_transfer' => $risks_during_transfer,
            'anticipation'          => $anticipation,
            'time_out'              => $time_out,
            'arrival_time'          => $arrival_time,
            'notes'                 => $notes,
            'date_updated'          => date('Y-m-d H:i:s', time()),
        );
    
        $this->$model->update($treatment_room_id, $dt_treatment_room);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_treatment_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update treatment_room', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}

