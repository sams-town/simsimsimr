<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Preoperative_nursing extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
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
    
        $preoperative_nursing_id = $id;
    
        if ($preoperative_nursing_id === 0 || $preoperative_nursing_id === "0") {
            $this->REST_Return(400, 'preoperative_nursing_id is required');
            return;
        }
    
        $output = $this->$model->read($preoperative_nursing_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->medical_history_arry = ($output[0]->medical_history_arry != "") ? explode(',', $output[0]->medical_history_arry) : [];
            $output[0]->patient_verification = json_decode($output[0]->patient_verification);
            $output[0]->physical_preparation = json_decode($output[0]->physical_preparation);
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function create_post() {
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
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $operating_room_id = $dt_post['operating_room_id'];

        $select = 'a.id';
        $operating_room = $this->Operating_room->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }
        
        $ttv_temperature = $dt_post['ttv_temperature'];
        $ttv_pulse = $dt_post['ttv_pulse'];
        $ttv_rr = $dt_post['ttv_rr'];
        $ttv_systolic = $dt_post['ttv_systolic'];
        $ttv_diastolic = $dt_post['ttv_diastolic'];
        $ttv_sore_score = $dt_post['ttv_sore_score'];
        $ttv_weight = $dt_post['ttv_weight'];
        $ttv_height = $dt_post['ttv_height'];
        $mental_status_type_id = $dt_post['mental_status_type_id'];
        $medical_history_arry = implode(',', $dt_post['medical_history_arry']);
        $medical_history = $dt_post['medical_history'];
        $current_treatment = $dt_post['current_treatment'];
        $tools_used = $dt_post['tools_used'];
        $prev_operations_type_id = $dt_post['prev_operations_type_id'];
        $prev_operations_date = $dt_post['prev_operations_date'];
        $prev_operations_place = $dt_post['prev_operations_place'];
        $allergy_type_id = $dt_post['allergy_type_id'];
        $allergy = $dt_post['allergy'];
        $cough_flu_fever_type_id = $dt_post['cough_flu_fever_type_id'];
        $period_type_id = $dt_post['period_type_id'];
        $checked_room_nurse_hours = $dt_post['checked_room_nurse_hours'];
        $checked_uko_nurse_hours = $dt_post['checked_uko_nurse_hours'];

        if ($dt_post['checked_room_nurse_id'] != "" || $dt_post['checked_room_nurse_id'] != null) {
            $checked_room_nurse_id = $dt_post['checked_room_nurse_id'];

            if ($checked_room_nurse_id == '') {
                $this->REST_Return(400, 'checked_room_nurse_id cannot be empty');
                return;
            }
            
            $select = 'a.id';
            $checked_room_nurse = $this->Employee->select($checked_room_nurse_id, $select);

            if (count($checked_room_nurse) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $checked_room_nurse_id);
                return;
            }
        } else {
            $checked_room_nurse_id = null;
        }

        if ($dt_post['checked_uko_nurse_id'] != "" || $dt_post['checked_uko_nurse_id'] != null) {
            $checked_uko_nurse_id = $dt_post['checked_uko_nurse_id'];

            if ($checked_uko_nurse_id == '') {
                $this->REST_Return(400, 'checked_uko_nurse_id cannot be empty');
                return;
            }
            
            $select = 'a.id';
            $checked_uko_nurse = $this->Employee->select($checked_uko_nurse_id, $select);

            if (count($checked_uko_nurse) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $checked_uko_nurse_id);
                return;
            }
        } else {
            $checked_uko_nurse_id = null;
        }

        $patient_verification_data = (object) array(
            'data' => array(
                'check_identity_arry' => $dt_post['patient_verification']['check_identity_arry'],
                'check_identity' => $dt_post['patient_verification']['check_identity'],
                'check_bracelet_arry' => $dt_post['patient_verification']['check_bracelet_arry'],
                'check_bracelet' => $dt_post['patient_verification']['check_bracelet'],
                'operation_scheduling_arry' => $dt_post['patient_verification']['operation_scheduling_arry'],
                'operation_scheduling' => $dt_post['patient_verification']['operation_scheduling'],
                'type_and_location_arry' => $dt_post['patient_verification']['type_and_location_arry'],
                'type_and_location' => $dt_post['patient_verification']['type_and_location'],
                'languages_issue_arry' => $dt_post['patient_verification']['languages_issue_arry'],
                'languages_issue' => $dt_post['patient_verification']['languages_issue'],
                'check_surgical_consent_arry' => $dt_post['patient_verification']['check_surgical_consent_arry'],
                'check_surgical_consent' => $dt_post['patient_verification']['check_surgical_consent'],
                'check_anesthesia_consent_arry' => $dt_post['patient_verification']['check_anesthesia_consent_arry'],
                'check_anesthesia_consent' => $dt_post['patient_verification']['check_anesthesia_consent'],
                'icu_availability_arry' => $dt_post['patient_verification']['icu_availability_arry'],
                'icu_availability' => $dt_post['patient_verification']['icu_availability'],
                'check_status_arry' => $dt_post['patient_verification']['check_status_arry'],
                'check_status' => $dt_post['patient_verification']['check_status'],
                'check_examination_arry' => $dt_post['patient_verification']['check_examination_arry'],
                'check_examination' => $dt_post['patient_verification']['check_examination'],
                'pre_anesthetic_evaluation_arry' => $dt_post['patient_verification']['pre_anesthetic_evaluation_arry'],
                'pre_anesthetic_evaluation' => $dt_post['patient_verification']['pre_anesthetic_evaluation'],
                'patient_questionnaire_arry' => $dt_post['patient_verification']['patient_questionnaire_arry'],
                'patient_questionnaire' => $dt_post['patient_verification']['patient_questionnaire'],
            ),
        );

        $physical_preparation_data = (object) array(
            'data' => array(
                'last_fast_arry' => $dt_post['physical_preparation']['last_fast_arry'],
                'last_fast' => $dt_post['physical_preparation']['last_fast'],
                'external_prosthesis_arry' => $dt_post['physical_preparation']['external_prosthesis_arry'],
                'external_prosthesis' => $dt_post['physical_preparation']['external_prosthesis'],
                'internal_prosthesis_arry' => $dt_post['physical_preparation']['internal_prosthesis_arry'],
                'internal_prosthesis' => $dt_post['physical_preparation']['internal_prosthesis'],
                'accessories_arry' => $dt_post['physical_preparation']['accessories_arry'],
                'accessories' => $dt_post['physical_preparation']['accessories'],
                'skin_preparation_arry' => $dt_post['physical_preparation']['skin_preparation_arry'],
                'skin_preparation' => $dt_post['physical_preparation']['skin_preparation'],
                'bladder_emptying_arry' => $dt_post['physical_preparation']['bladder_emptying_arry'],
                'bladder_emptying' => $dt_post['physical_preparation']['bladder_emptying'],
                'blood_supply_arry' => $dt_post['physical_preparation']['blood_supply_arry'],
                'blood_supply' => $dt_post['physical_preparation']['blood_supply'],
                'aids_arry' => $dt_post['physical_preparation']['aids_arry'],
                'aids' => $dt_post['physical_preparation']['aids'],
                'included_medications_arry' => $dt_post['physical_preparation']['included_medications_arry'],
                'included_medications' => $dt_post['physical_preparation']['included_medications'],
                'last_medications_arry' => $dt_post['physical_preparation']['last_medications_arry'],
                'last_medications' => $dt_post['physical_preparation']['last_medications'],
                'vascular_access_arry' => $dt_post['physical_preparation']['vascular_access_arry'],
                'vasvular_access' => $dt_post['physical_preparation']['vasvular_access'],
            ),
        );

        $patient_verification_encode = json_encode($patient_verification_data);
        $physical_preparation_encode = json_encode($physical_preparation_data);
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_preoperative_nursing = array(
            'operating_room_id'         => $operating_room_id,
            'ttv_temperature'           => $ttv_temperature,
            'ttv_pulse'                 => $ttv_pulse,
            'ttv_rr'                    => $ttv_rr,
            'ttv_systolic'              => $ttv_systolic,
            'ttv_diastolic'             => $ttv_diastolic,
            'ttv_sore_score'            => $ttv_sore_score,
            'ttv_weight'                => $ttv_weight,
            'ttv_height'                => $ttv_height,
            'mental_status_type_id'     => $mental_status_type_id,
            'medical_history_arry'      => $medical_history_arry,
            'medical_history'           => $medical_history,
            'current_treatment'         => $current_treatment,
            'tools_used'                => $tools_used,
            'prev_operations_type_id'   => $prev_operations_type_id,
            'prev_operations_date'      => $prev_operations_date,
            'prev_operations_place'     => $prev_operations_place,
            'allergy_type_id'           => $allergy_type_id,
            'allergy'                   => $allergy,
            'cough_flu_fever_type_id'   => $cough_flu_fever_type_id,
            'period_type_id'            => $period_type_id,
            'patient_verification'      => $patient_verification_encode,
            'physical_preparation'      => $physical_preparation_encode,
            'checked_room_nurse_id'     => $checked_room_nurse_id,
            'checked_room_nurse_hours'  => $checked_room_nurse_hours,
            'checked_uko_nurse_id'      => $checked_uko_nurse_id,
            'checked_uko_nurse_hours'   => $checked_uko_nurse_hours,
            'user_added'                => $user_id,
            'date_added'                => date('Y-m-d H:i:s',time()),
            'date_updated'              => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_preoperative_nursing);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_preoperative_nursing,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create preoperative_nursing', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }
}