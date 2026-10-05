<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Hemodialysis_initiation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
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
    
        $hemodialysis_initiation_id = $id;
    
        if ($hemodialysis_initiation_id === 0 || $hemodialysis_initiation_id === "0") {
            $this->REST_Return(400, 'hemodialysis_initiation_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($hemodialysis_initiation_id);
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
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
    
        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
    
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }
    
        $main_diagnosis = $dt_post['main_diagnosis'];
        $etiology = $dt_post['etiology'];
        $comorbidities = $dt_post['comorbidities'];
        $prescription_hd = $dt_post['prescription_hd'];
        $frequency_hd = $dt_post['frequency_hd'];
        $vascular_access = $dt_post['vascular_access'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_hemodialysis_initiation = array(
            'registration_destination_id'   => $registration_destination_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'main_diagnosis'                => $main_diagnosis,
            'etiology'                      => $etiology,
            'comorbidities'                 => $comorbidities,
            'prescription_hd'               => $prescription_hd,
            'frequency_hd'                  => $frequency_hd,
            'vascular_access'               => $vascular_access,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_hemodialysis_initiation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create hemodialysis_initiation', $response_data);
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
    
        $hemodialysis_initiation_id = $id;
    
        if ($hemodialysis_initiation_id === 0 || $hemodialysis_initiation_id === "0") {
            $this->REST_Return(400, 'hemodialysis_initiation_id is required');
            return;
        }

        $select = 'a.id';
        $hemodialysis_initiation = $this->$model->select($hemodialysis_initiation_id, $select);

        if (count($hemodialysis_initiation) == 0) {
            $this->REST_Return(404, 'there is no hemodialysis_initiation with id ' . $hemodialysis_initiation_id);
            return;
        }

        $main_diagnosis = $dt_put['main_diagnosis'];
        $etiology = $dt_put['etiology'];
        $comorbidities = $dt_put['comorbidities'];
        $prescription_hd = $dt_put['prescription_hd'];
        $frequency_hd = $dt_put['frequency_hd'];
        $vascular_access = $dt_put['vascular_access'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_hemodialysis_initiation = array(
            'main_diagnosis'    => $main_diagnosis,
            'etiology'          => $etiology,
            'comorbidities'     => $comorbidities,
            'prescription_hd'   => $prescription_hd,
            'frequency_hd'      => $frequency_hd,
            'vascular_access'   => $vascular_access,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($hemodialysis_initiation_id, $dt_hemodialysis_initiation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update hemodialysis_initiation', $response_data);
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
            $this->REST_Return(400, 'hemodialysis_initiation_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $hemodialysis_initiation_id = $this->$model->select_id($id);
    
        if ($hemodialysis_initiation_id == "") {
            $this->REST_Return(404, 'there is no hemodialysis_initiation with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_hemodialysis_initiation = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($hemodialysis_initiation_id, $dt_hemodialysis_initiation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete hemodialysis_initiation', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}