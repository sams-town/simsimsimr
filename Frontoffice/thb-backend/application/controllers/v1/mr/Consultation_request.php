<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Consultation_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'consultation_date', 'label' => 'consultation_date', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'answer', 'label' => 'answer', 'rules' => 'trim|required|max_length[255]'),
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
    
        $consultation_id = $id;
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($consultation_id);
        $response_data = array();

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->consultation_arry = explode(',', $output[$i]->consultation_arry);
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
            $output->rows[$i]->consultation_arry = explode(',', $output->rows[$i]->consultation_arry);
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
        $health_facility_item_id = $dt_post['health_facility_item_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        $soap_id = $dt_post['soap_id'];

        $select = 'a.id, a.registration_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $registration_id = $registration_destination[0]->registration_id;

        $select = 'a.id, a.norm';
        $registration = $this->Registration->select($registration_id, $select);

        $norm = $registration[0]->norm;

        $select = 'a.id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $select = 'a.id';
        $soap = $this->Soap->select($soap_id, $select);

        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        $consultation_date = $dt_post['consultation_date'];
        $consultation_type_id = $dt_post['consultation_type_id'];
        $consultation_arry = implode(',', $dt_post['consultation_arry']);
        $issue = $dt_post['issue'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_consultation_request = array(
            'registration_destination_id' => $registration_destination_id,
            'health_facility_item_id' => $health_facility_item_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'norm' => $norm,
            'consultation_date' => $consultation_date,
            'consultation_type_id' => $consultation_type_id,
            'consultation_arry' => $consultation_arry,
            'issue' => $issue,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_consultation_request);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create consultation_request', $response_data);
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

        $consultation_request_id = $id;

        if ($consultation_request_id === 0 || $consultation_request_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }

        $select = 'a.id';
        $consultation_request = $this->$model->select($consultation_request_id, $select);

        if (count($consultation_request) == 0) {
            $this->REST_Return(404, 'there is no consultation_request with id ' . $consultation_request_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $answer = $dt_put['answer'];
    
        $dt_consultation_request = array(
            'answer' => $answer,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($consultation_request_id, $dt_consultation_request);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update consultation_request', $response_data);
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
    
        $consultation_request_id = $id;

        if ($consultation_request_id === 0 || $consultation_request_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }

        $select = 'a.id';
        $consultation_request = $this->$model->select($consultation_request_id, $select);

        if (count($consultation_request) == 0) {
            $this->REST_Return(404, 'there is no consultation_request with id ' . $consultation_request_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_consultation_request = array(
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($consultation_request_id, $dt_consultation_request);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete consultation_request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}