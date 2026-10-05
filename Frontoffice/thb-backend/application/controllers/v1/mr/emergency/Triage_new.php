<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Triage_new extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
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
    
        $triage_new_id = $id;
    
        if ($triage_new_id === 0 || $triage_new_id === "0") {
            $this->REST_Return(400, 'triage_new_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($triage_new_id);
        $response_data = array();

        if (count($output) > 0) { 
            $output[0]->breathing_path_arry = ($output[0]->breathing_path_arry != "") ? explode(',', $output[0]->breathing_path_arry) : [];
            $output[0]->breathing_arry = ($output[0]->breathing_arry != "") ? explode(',', $output[0]->breathing_arry) : [];
            $output[0]->circulation_arry = ($output[0]->circulation_arry != "") ? explode(',', $output[0]->circulation_arry) : [];
            $output[0]->consciousness_arry = ($output[0]->consciousness_arry != "") ? explode(',', $output[0]->consciousness_arry) : [];
            $output[0]->resource_arry = ($output[0]->resource_arry != "") ? explode(',', $output[0]->resource_arry) : [];
            $output[0]->resuscitation_resource_arry = ($output[0]->resuscitation_resource_arry != "") ? explode(',', $output[0]->resuscitation_resource_arry) : [];
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
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $output->rows[$i]->breathing_path_arry = ($output->rows[$i]->breathing_path_arry != "") ? explode(',', $output->rows[$i]->breathing_path_arry) : [];
            $output->rows[$i]->breathing_arry = ($output->rows[$i]->breathing_arry != "") ? explode(',', $output->rows[$i]->breathing_arry) : [];
            $output->rows[$i]->circulation_arry = ($output->rows[$i]->circulation_arry != "") ? explode(',', $output->rows[$i]->circulation_arry) : [];
            $output->rows[$i]->consciousness_arry = ($output->rows[$i]->consciousness_arry != "") ? explode(',', $output->rows[$i]->consciousness_arry) : [];
            $output->rows[$i]->resource_arry = ($output->rows[$i]->resource_arry != "") ? explode(',', $output->rows[$i]->resource_arry) : [];
            $output->rows[$i]->resuscitation_resource_arry = ($output->rows[$i]->resuscitation_resource_arry != "") ? explode(',', $output->rows[$i]->resuscitation_resource_arry) : [];
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
    
        $registration_destination_id = $dt_post['registration_destination_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $referral_type_id = $dt_post['referral_type_id'];
        $referral = $dt_post['referral'];
        $level_of_consciousness_type_id = $dt_post['level_of_consciousness_type_id'];
        $type_of_cases_type_id = $dt_post['type_of_cases_type_id'];
        $trauma_type_id = $dt_post['trauma_type_id'];
        $immunization_status_type_id = $dt_post['immunization_status_type_id'];
        $main_complaint = $dt_post['main_complaint'];
        $drug_allergy_type_id = $dt_post['drug_allergy_type_id'];
        $drug_allergy = $dt_post['drug_allergy'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $pulse_frequency = $dt_post['pulse_frequency'];
        $breath_saturation = $dt_post['breath_saturation'];
        $oxygen_saturation = $dt_post['oxygen_saturation'];
        $temperature = $dt_post['temperature'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $pain_area = $dt_post['pain_area'];
        $pain_score = $dt_post['pain_score'];
        $breathing_path_arry = implode(',', $dt_post['breathing_path_arry']);
        $breathing_arry = implode(',', $dt_post['breathing_arry']);
        $circulation_arry = implode(',', $dt_post['circulation_arry']);
        $consciousness_arry = implode(',', $dt_post['consciousness_arry']);
        $resource_arry = implode(',', $dt_post['resource_arry']);
        $resuscitation_resource_arry = implode(',', $dt_post['resuscitation_resource_arry']);
        $age_type_id = $dt_post['age_type_id'];
        $triage_result = $dt_post['triage_result'];
        $zone_type_id = $dt_post['zone_type_id'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_triage_new = array(
            'registration_destination_id'    => $registration_destination_id,
            'employee_nurse_id'              => $employee_nurse_id,
            'referral_type_id'               => $referral_type_id,
            'referral'                       => $referral,
            'level_of_consciousness_type_id' => $level_of_consciousness_type_id,
            'type_of_cases_type_id'          => $type_of_cases_type_id,
            'trauma_type_id'                 => $trauma_type_id,
            'immunization_status_type_id'    => $immunization_status_type_id,
            'main_complaint'                 => $main_complaint,
            'drug_allergy_type_id'           => $drug_allergy_type_id,
            'drug_allergy'                   => $drug_allergy,
            'systolic'                       => $systolic,
            'diastolic'                      => $diastolic,
            'pulse_frequency'                => $pulse_frequency,
            'breath_saturation'              => $breath_saturation,
            'oxygen_saturation'              => $oxygen_saturation,
            'temperature'                    => $temperature,
            'weight'                         => $weight,
            'height'                         => $height,
            'pain_area'                      => $pain_area,
            'pain_score'                     => $pain_score,
            'breathing_path_arry'            => $breathing_path_arry,
            'breathing_arry'                 => $breathing_arry,
            'circulation_arry'               => $circulation_arry,
            'consciousness_arry'             => $consciousness_arry,
            'resource_arry'                  => $resource_arry,
            'resuscitation_resource_arry'    => $resuscitation_resource_arry,
            'age_type_id'                    => $age_type_id,
            'triage_result'                  => $triage_result,
            'zone_type_id'                   => $zone_type_id,
            'user_added'                     => $user_id,
            'date_added'                     => date('Y-m-d H:i:s',time()),
            'date_updated'                   => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_triage_new);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_triage_new,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create triage_new', $response_data);
            return;
        }
    
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
    
        $triage_new_id = $id;
    
        if ($triage_new_id === 0 || $triage_new_id === "0") {
            $this->REST_Return(400, 'triage_new_id is required');
            return;
        }
    
        $referral_type_id = $dt_put['referral_type_id'];
        $referral = $dt_put['referral'];
        $level_of_consciousness_type_id = $dt_put['level_of_consciousness_type_id'];
        $type_of_cases_type_id = $dt_put['type_of_cases_type_id'];
        $trauma_type_id = $dt_put['trauma_type_id'];
        $immunization_status_type_id = $dt_put['immunization_status_type_id'];
        $main_complaint = $dt_put['main_complaint'];
        $drug_allergy_type_id = $dt_put['drug_allergy_type_id'];
        $drug_allergy = $dt_put['drug_allergy'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_saturation = $dt_put['breath_saturation'];
        $oxygen_saturation = $dt_put['oxygen_saturation'];
        $temperature = $dt_put['temperature'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $pain_area = $dt_put['pain_area'];
        $pain_score = $dt_put['pain_score'];
        $breathing_path_arry = implode(',', $dt_put['breathing_path_arry']);
        $breathing_arry = implode(',', $dt_put['breathing_arry']);
        $circulation_arry = implode(',', $dt_put['circulation_arry']);
        $consciousness_arry = implode(',', $dt_put['consciousness_arry']);
        $resource_arry = implode(',', $dt_put['resource_arry']);
        $resuscitation_resource_arry = implode(',', $dt_put['resuscitation_resource_arry']);
        $age_type_id = $dt_put['age_type_id'];
        $triage_result = $dt_put['triage_result'];
        $zone_type_id = $dt_put['zone_type_id'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_triage_new = array(
            'referral_type_id'               => $referral_type_id,
            'referral'                       => $referral,
            'level_of_consciousness_type_id' => $level_of_consciousness_type_id,
            'type_of_cases_type_id'          => $type_of_cases_type_id,
            'trauma_type_id'                 => $trauma_type_id,
            'immunization_status_type_id'    => $immunization_status_type_id,
            'main_complaint'                 => $main_complaint,
            'drug_allergy_type_id'           => $drug_allergy_type_id,
            'drug_allergy'                   => $drug_allergy,
            'systolic'                       => $systolic,
            'diastolic'                      => $diastolic,
            'pulse_frequency'                => $pulse_frequency,
            'breath_saturation'              => $breath_saturation,
            'oxygen_saturation'              => $oxygen_saturation,
            'temperature'                    => $temperature,
            'weight'                         => $weight,
            'height'                         => $height,
            'pain_area'                      => $pain_area,
            'pain_score'                     => $pain_score,
            'breathing_path_arry'            => $breathing_path_arry,
            'breathing_arry'                 => $breathing_arry,
            'circulation_arry'               => $circulation_arry,
            'consciousness_arry'             => $consciousness_arry,
            'resource_arry'                  => $resource_arry,
            'resuscitation_resource_arry'    => $resuscitation_resource_arry,
            'age_type_id'                    => $age_type_id,
            'triage_result'                  => $triage_result,
            'zone_type_id'                   => $zone_type_id,
            'user_updated'                   => $user_id,
            'date_updated'                   => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($triage_new_id, $dt_triage_new);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_triage_new,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update triage_new', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function delete_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'triage_new_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $triage_new_id = $this->$model->select_id($id);
    
        if ($triage_new_id == "") {
            $this->REST_Return(404, 'there is no triage_new with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_triage_new = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($triage_new_id, $dt_triage_new);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_triage_new,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete triage_new', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}