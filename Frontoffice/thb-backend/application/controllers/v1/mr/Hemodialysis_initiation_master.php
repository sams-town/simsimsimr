<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Hemodialysis_initiation_master extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|required'),
            array('field' => 'number', 'label' => 'number', 'rules' => 'trim|required'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|required'),
            array('field' => 'number', 'label' => 'number', 'rules' => 'trim|required'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
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
    
        $this->$model->whereLike = $q_strings;
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
        
        $code = $dt_post['code'];
        $number = $dt_post['number'];
        $name = $dt_post['name'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_hemodialysis_initiation_master = array(
            'code' => $code,
            'number' => $number,
            'name' => $name,
        );
    
        $id = $this->$model->create($dt_hemodialysis_initiation_master);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation_master,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create hemodialysis_initiation_master', $response_data);
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
    
        $hemodialysis_initiation_master_id = $id;
    
        if ($hemodialysis_initiation_master_id === 0 || $hemodialysis_initiation_master_id === "0") {
            $this->REST_Return(400, 'hemodialysis_initiation_master_id is required');
            return;
        }

        $select = 'a.id';
        $hemodialysis_initiation_master = $this->$model->select($hemodialysis_initiation_master_id, $select);

        if (count($hemodialysis_initiation_master) == 0) {
            $this->REST_Return(404, 'there is no hemodialysis_initiation_master with id ' . $hemodialysis_initiation_master_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $code = $dt_put['code'];
        $number = $dt_put['number'];
        $name = $dt_put['name'];
    
        $dt_hemodialysis_initiation_master = array(
            'code' => $code,
            'number' => $number,
            'name' => $name,
        );
    
        $this->$model->update($hemodialysis_initiation_master_id, $dt_hemodialysis_initiation_master);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation_master,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update hemodialysis_initiation_master', $response_data);
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
            $this->REST_Return(400, 'hemodialysis_initiation_master_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $hemodialysis_initiation_master_id = $this->$model->select_id($id);
    
        if ($hemodialysis_initiation_master_id == "") {
            $this->REST_Return(404, 'there is no hemodialysis_initiation_master with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_hemodialysis_initiation_master = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($hemodialysis_initiation_master_id, $dt_hemodialysis_initiation_master);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation_master,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete hemodialysis_initiation_master', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}