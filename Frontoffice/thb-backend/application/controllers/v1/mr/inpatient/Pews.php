<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Pews extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_date', 'label' => 'observation_date', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->create_item_rules = array(
            array('field' => 'pews_id', 'label' => 'pews_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_time', 'label' => 'observation_time', 'rules' => 'trim|required'),
            array('field' => 'breathing', 'label' => 'breathing', 'rules' => 'trim|required'),
            array('field' => 'oxygen', 'label' => 'oxygen', 'rules' => 'trim|required'),
            array('field' => 'oxygen_saturation', 'label' => 'oxygen_saturation', 'rules' => 'trim|required'),
            array('field' => 'pulse', 'label' => 'pulse', 'rules' => 'trim|required'),
            array('field' => 'skin_color', 'label' => 'skin_color', 'rules' => 'trim|required'),
            array('field' => 'systolic', 'label' => 'systolic', 'rules' => 'trim|required'),
            array('field' => 'behavior', 'label' => 'behavior', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'observation_date', 'label' => 'observation_date', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_item_rules = array(
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_time', 'label' => 'observation_time', 'rules' => 'trim|required'),
            array('field' => 'breathing', 'label' => 'breathing', 'rules' => 'trim|required'),
            array('field' => 'oxygen', 'label' => 'oxygen', 'rules' => 'trim|required'),
            array('field' => 'oxygen_saturation', 'label' => 'oxygen_saturation', 'rules' => 'trim|required'),
            array('field' => 'pulse', 'label' => 'pulse', 'rules' => 'trim|required'),
            array('field' => 'skin_color', 'label' => 'skin_color', 'rules' => 'trim|required'),
            array('field' => 'systolic', 'label' => 'systolic', 'rules' => 'trim|required'),
            array('field' => 'behavior', 'label' => 'behavior', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|max_length[1]'),
        );

        $this->get_score_rules = array(
            array('field' => 'birth_date', 'label' => 'birth_date', 'rules' => 'trim|required'),
            array('field' => 'breathing', 'label' => 'breathing', 'rules' => 'trim|required'),
            array('field' => 'oxygen', 'label' => 'oxygen', 'rules' => 'trim|required'),
            array('field' => 'oxygen_saturation', 'label' => 'oxygen_saturation', 'rules' => 'trim|required'),
            array('field' => 'pulse', 'label' => 'pulse', 'rules' => 'trim|required'),
            array('field' => 'skin_color', 'label' => 'skin_color', 'rules' => 'trim|required'),
            array('field' => 'systolic', 'label' => 'systolic', 'rules' => 'trim|required'),
            array('field' => 'behavior', 'label' => 'behavior', 'rules' => 'trim|required'),
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
    
        $select = 'a.*';
        $where = array(
            'a.is_delete' => 0
        );
        $output = $this->$model->select(0, $select, $where);

        for ($i=0; $i < count($output); $i++) { 
            $pews_id = $output[$i]->id;

            $select = 'a.*, e.fullname as employee_nurse_name';
            $where = array('pews_id' => $pews_id, 'a.is_delete' => 0);
            $join = array(
                array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),	
            );
            $pews_item = $this->Pews_item->select(0, $select, $where, $join);

            $output[$i]->pews_item = $pews_item;
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
    
        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $observation_date = $dt_post['observation_date'];

        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id,
            'observation_date' => $observation_date,
            'is_delete' => 0,
        );
        $pews = $this->$model->select(0, $select, $where);
        if (count($pews) > 0) {
            $this->REST_Return(409, 'pews with observation_date ' . $observation_date . ' is already exist');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_pews = array(
            'registration_destination_id'   => $registration_destination_id,
            'observation_date'              => $observation_date,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $id = $this->$model->create($dt_pews);
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pews,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create pews', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function create_item_post() {
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
        $this->form_validation->set_rules($this->create_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $pews_id = $dt_post['pews_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $select = 'a.id, p.birth_date';
        $where = array();
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),	
            array('registration r', 'rd.registration_id = r.id', 'INNER'),	
            array('patient p', 'r.norm = p.norm', 'INNER'),	
        );
        $pews = $this->$model->select($pews_id, $select, $where, $join);
        if (count($pews) == 0) {
            $this->REST_Return(404, 'there is no pews with id ' . $pews_id);
            return;
        }

        $birth_date = $pews[0]->birth_date;
    
        $observation_time = $dt_post['observation_time'];
        $breathing = (float) $dt_post['breathing'];
        $oxygen = (int) $dt_post['oxygen'];
        $oxygen_saturation = (float) $dt_post['oxygen_saturation'];
        $pulse = (float) $dt_post['pulse'];
        $skin_color = (int) $dt_post['skin_color'];
        $systolic = (float) $dt_post['systolic'];
        $behavior = (int) $dt_post['behavior'];
        
        $total = $this->Pews_item->count_pews($birth_date,$breathing,$oxygen,$oxygen_saturation,$pulse,$skin_color,$systolic,$behavior);

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_pews_item = array(
            'pews_id' => $pews_id,
            'employee_nurse_id'      => $employee_nurse_id,
            'observation_time'       => $observation_time,
            'breathing'              => $breathing,
            'oxygen'                 => $oxygen,
            'oxygen_saturation'      => $oxygen_saturation,
            'pulse'                  => $pulse,
            'skin_color'             => $skin_color,
            'systolic'               => $systolic,
            'behavior'               => $behavior,
            'total'                  => $total,
            'user_added'             => $user_id,
            'date_added'             => date('Y-m-d H:i:s',time()),
            'date_updated'           => date('Y-m-d H:i:s',time()),
        );

        $id = $this->Pews_item->create($dt_pews_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pews_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create pews_item', $response_data);
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
    
        $pews_id = $id;
    
        if ($pews_id === 0 || $pews_id === "0") {
            $this->REST_Return(400, 'pews_id is required');
            return;
        }

        $select = 'a.id';
        $pews = $this->$model->select($pews_id, $select);

        if (count($pews) == 0) {
            $this->REST_Return(404, 'there is no pews with id ' . $pews_id);
            return;
        }

        $observation_date = $dt_put['observation_date'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_pews = array(
            'observation_date'  => $observation_date,
            'date_updated'      => date('Y-m-d H:i:s'),
        );

        $this->$model->update_v2($pews_id, $dt_pews);
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update pews', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_item_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $pews_item_id = $id;
    
        if ($pews_item_id === 0 || $pews_item_id === "0") {
            $this->REST_Return(400, 'pews_item_id is required');
            return;
        }

        $select = 'a.id, a.pews_id';
        $pews_item = $this->Pews_item->select($pews_item_id, $select);
        if (count($pews_item) == 0) {
            $this->REST_Return(404, 'there is no pews_item with id ' . $pews_item_id);
            return;
        }

        $pews_id = $pews_item[0]->pews_id;

        $employee_nurse_id = $dt_put['employee_nurse_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $select = 'a.id, p.birth_date';
        $where = array();
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),	
            array('registration r', 'rd.registration_id = r.id', 'INNER'),	
            array('patient p', 'r.norm = p.norm', 'INNER'),	
        );
        $pews = $this->$model->select($pews_id, $select, $where, $join);
        if (count($pews) == 0) {
            $this->REST_Return(404, 'there is no pews with id ' . $pews_id);
            return;
        }

        $birth_date = $pews[0]->birth_date;
    
        $observation_time = $dt_put['observation_time'];
        $breathing = (float) $dt_put['breathing'];
        $oxygen = (int) $dt_put['oxygen'];
        $oxygen_saturation = (float) $dt_put['oxygen_saturation'];
        $pulse = (float) $dt_put['pulse'];
        $skin_color = (int) $dt_put['skin_color'];
        $systolic = (float) $dt_put['systolic'];
        $behavior = (int) $dt_put['behavior'];

        $total = $this->Pews_item->count_pews($birth_date,$breathing,$oxygen,$oxygen_saturation,$pulse,$skin_color,$systolic,$behavior);

        /* Start Transaction */
        $this->db->trans_start();

        $dt_pews_item = array(
            'employee_nurse_id'      => $employee_nurse_id,
            'observation_time'       => $observation_time,
            'breathing'              => $breathing,
            'oxygen'                 => $oxygen,
            'oxygen_saturation'      => $oxygen_saturation,
            'pulse'                  => $pulse,
            'skin_color'             => $skin_color,
            'systolic'               => $systolic,
            'behavior'               => $behavior,
            'total'                  => $total,
            'user_updated'           => $user_id,
            'date_updated'           => date('Y-m-d H:i:s',time()),
        );

        $this->Pews_item->update_v2($pews_item_id, $dt_pews_item);
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update pews item', $response_data);
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
            $this->REST_Return(400, 'pews_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $pews_id = $this->$model->select_id($id);
    
        if ($pews_id == "") {
            $this->REST_Return(404, 'there is no pews with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_pews = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update_v2($pews_id, $dt_pews);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pews,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete pews', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function delete_item_delete($p = 0) {
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
            $this->REST_Return(400, 'pews_item_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $select = 'a.id'; 
        $pews_item = $this->Pews_item->select($id, $select);
        if (count($pews_item) == 0) {
            $this->REST_Return(404, 'there is no pews_item with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $pews_item = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->Pews_item->update_v2($id, $pews_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $pews_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete pews_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function get_score_post() {
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
        $this->form_validation->set_rules($this->get_score_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $birth_date = $dt_post['birth_date'];
        $breathing = (float) $dt_post['breathing'];
        $oxygen = (int) $dt_post['oxygen'];
        $oxygen_saturation = (float) $dt_post['oxygen_saturation'];
        $pulse = (float) $dt_post['pulse'];
        $skin_color = (int) $dt_post['skin_color'];
        $systolic = (float) $dt_post['systolic'];
        $behavior = (int) $dt_post['behavior'];
        
        $total = $this->Pews_item->count_pews($birth_date,$breathing,$oxygen,$oxygen_saturation,$pulse,$skin_color,$systolic,$behavior);
    
        $response_data = array(
            'score'     => $total
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}