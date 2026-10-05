<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Provision_medical_information extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim'),
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
    
        $provision_medical_information_id = $id;
    
        if ($provision_medical_information_id === 0 || $provision_medical_information_id === "0") {
            $this->REST_Return(400, 'provision_medical_information_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($provision_medical_information_id);
        $response_data = array();
    
    
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

        $patient_condition = $dt_post['patient_condition'];
        $diagnosis = $dt_post['diagnosis'];
        $proposed_treatment = $dt_post['proposed_treatment'];
        $procedures = $dt_post['procedures'];
        $purpose = $dt_post['purpose'];
        $risk_complications = $dt_post['risk_complications'];
        $alternative_therapy = $dt_post['alternative_therapy'];
        $prognosis = $dt_post['prognosis'];
        $possible_problem = $dt_post['possible_problem'];
        $possible_result = $dt_post['possible_result'];
        $family_response = $dt_post['family_response'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_provision_medical_information = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'patient_condition'             => $patient_condition,
            'diagnosis'                     => $diagnosis,
            'proposed_treatment'            => $proposed_treatment,
            'procedures'                    => $procedures,
            'purpose'                       => $purpose,
            'risk_complications'            => $risk_complications,
            'alternative_therapy'           => $alternative_therapy,
            'prognosis'                     => $prognosis,
            'possible_problem'              => $possible_problem,
            'possible_result'               => $possible_result,
            'family_response'               => $family_response,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_provision_medical_information);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_provision_medical_information,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create provision_medical_information', $response_data);
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
    
        $provision_medical_information_id = $id;
    
        if ($provision_medical_information_id === 0 || $provision_medical_information_id === "0") {
            $this->REST_Return(400, 'provision_medical_information_id is required');
            return;
        }

        $select = 'a.id';
        $provision_mdeical_information = $this->$model->select($provision_medical_information_id, $select);
        if (count($provision_mdeical_information) == 0) {
            $this->REST_Return(404, 'there is no provision_medical_information with id ' . $provision_medical_information_id);
            return;
        }
    
        $patient_condition = $dt_put['patient_condition'];
        $diagnosis = $dt_put['diagnosis'];
        $proposed_treatment = $dt_put['proposed_treatment'];
        $procedures = $dt_put['procedures'];
        $purpose = $dt_put['purpose'];
        $risk_complications = $dt_put['risk_complications'];
        $alternative_therapy = $dt_put['alternative_therapy'];
        $prognosis = $dt_put['prognosis'];
        $possible_problem = $dt_put['possible_problem'];
        $possible_result = $dt_put['possible_result'];
        $family_response = $dt_put['family_response'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_provision_medical_information = array(
            'patient_condition'             => $patient_condition,
            'diagnosis'                     => $diagnosis,
            'proposed_treatment'            => $proposed_treatment,
            'procedures'                    => $procedures,
            'purpose'                       => $purpose,
            'risk_complications'            => $risk_complications,
            'alternative_therapy'           => $alternative_therapy,
            'prognosis'                     => $prognosis,
            'possible_problem'              => $possible_problem,
            'possible_result'               => $possible_result,
            'family_response'               => $family_response,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($provision_medical_information_id, $dt_provision_medical_information);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_provision_medical_information,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update provision_medical_information', $response_data);
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
            $this->REST_Return(400, 'provision_medical_information_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $provision_medical_information_id = $this->$model->select_id($id);
    
        if ($provision_medical_information_id == "") {
            $this->REST_Return(404, 'there is no provision_medical_information with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_provision_medical_information = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($provision_medical_information_id, $dt_provision_medical_information);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_provision_medical_information,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete provision_medical_information', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}