<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Marking_operation_location extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");
        $this->load->model("v1/hr/Employee_model", "Employee");
        // Load Helper
        $this->load->helper("upload");

        $this->create_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|max_length[1]'),
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

        $select = 'a.*';
        $where = array('a.is_delete' => 0);
        $output = $this->$model->select(0, $select, $where);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function upload_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $operating_room_id = $id;

        if ($operating_room_id === 0 || $operating_room_id === "0") {
            $this->REST_Return(400, 'operating_room_id is required');
            return;
        }

        $select = 'a.id';
        $operating_room = $this->Operating_room->select($operating_room_id, $select);
        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }

        $doctor_in_charge_id = $_POST['doctor_in_charge_id'];
        $part = $_POST['part'];

        if ($doctor_in_charge_id == null) {
            $this->REST_Return(400, 'field doctor_in_charge_id is required');
            return;
        }

        if ($part == null) {
            $this->REST_Return(400, 'field part is required');
            return;
        }

        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $filename = $part . '_' . $operating_room_id . '_' . date('Ymd');

        $upload = upload_file($_FILES['file'], $filename, 'assets/document/operating/marking');
        if ($upload['status'] == 'failed') {
            $this->REST_Return(422, $upload['message'], array());
            return;
        }
        
        $ext_type = $upload['type'];
        $filename = $filename . '.' . $ext_type;
        $url = 'assets/document/operating/marking/' . $filename;

        /* Start Transaction */
        $this->db->trans_start();

        $dt_marking_operation_location = array(
            'operating_room_id'     => $operating_room_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'part'                  => $part,
            'url'                   => $url,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s'),
            'date_updated'          => date('Y-m-d H:i:s'),
        );

        $this->$model->create($dt_marking_operation_location);
       
        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_marking_operation_location,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update marking_operation_location', $response_data);
            return;
        }

        $response_data = $id;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
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
            $this->REST_Return(400, 'marking_operation_location_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $marking_operation_location_id = $this->$model->select_id($id);
    
        if ($marking_operation_location_id == "") {
            $this->REST_Return(404, 'there is no marking_operation_location with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_marking_operation_location = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($marking_operation_location_id, $dt_marking_operation_location);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_marking_operation_location,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete marking_operation_location', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}