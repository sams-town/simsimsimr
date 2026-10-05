<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Temperature_chart extends My_Controller {
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
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'monitoring_date', 'label' => 'monitoring_date', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->create_item_rules = array(
            array('field' => 'temperature_chart_id', 'label' => 'temperature_chart_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_hours', 'label' => 'observation_hours', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_item_rules = array(
            array('field' => 'rr', 'label' => 'rr', 'rules' => 'trim|required'),
            array('field' => 'pulse', 'label' => 'pulse', 'rules' => 'trim|required'),
            array('field' => 'temperature', 'label' => 'temperature', 'rules' => 'trim|required'),
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
    
        $temperature_chart_id = $id;
    
        if ($temperature_chart_id === 0 || $temperature_chart_id === "0") {
            $this->REST_Return(400, 'temperature_chart_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($temperature_chart_id);
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

        for ($i=0; $i < count($output->rows); $i++) { 
            $temperature_chart_id = $output->rows[$i]->id;

            $select = 'a.*';
            $where = array('temperature_chart_id' => $temperature_chart_id, 'is_delete' => 0);
            $temperature_chart_items = $this->Temperature_chart_item->select(0, $select, $where);

            $output->rows[$i]->temperature_chart_items = $temperature_chart_items;
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_v2_get($p = 0){
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

        $registration_destination_id = $q_strings['registration_destination_id'];

        if ($registration_destination_id == null) {
            $this->REST_Return(400, 'registration_destination_id is required');
            return;
        }

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $date_start = $q_strings['date_start'];
        $date_end = $q_strings['date_end'];

        if ($date_start == null || $date_end == null) {
            $this->REST_Return('date_start & date_end is required');
            return;
        }
    
        $select = 'a.*, tc.monitoring_date';
        $where = array(
            'tc.registration_destination_id' => $registration_destination_id, 
            'tc.monitoring_date >=' => $date_start . ' 00:00:00', 
            'tc.monitoring_date <=' => $date_end . ' 23:59:59', 
            'tc.is_delete' => 0,
            'a.is_delete' => 0,
        );
        $join = array(
            array('temperature_chart tc', 'a.temperature_chart_id = tc.id', 'INNER'),
        );
        $order = array(
            'a.date_added' => 'ASC'
        );
        $output = $this->Temperature_chart_item->select(0, $select, $where, $join, $order);
        
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

        $monitoring_date = $dt_post['monitoring_date'];
        $diet = $dt_post['diet'];
        $defecation_form = $dt_post['defecation_form'];
        $defecation_frequency = $dt_post['defecation_frequency'];
        $intake = $dt_post['intake'];
        $output = $dt_post['output'];
        $systolic_morning = $dt_post['systolic_morning'];
        $diastolic_morning = $dt_post['diastolic_morning'];
        $systolic_afternoon = $dt_post['systolic_afternoon'];
        $diastolic_afternoon = $dt_post['diastolic_afternoon'];
        $systolic_evening = $dt_post['systolic_evening'];
        $diastolic_evening = $dt_post['diastolic_evening'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_temperature_chart = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id'           => $employee_nurse_id,
            'monitoring_date'             => $monitoring_date,
            'diet'                        => $diet,
            'defecation_form'             => $defecation_form,
            'defecation_frequency'        => $defecation_frequency,
            'intake'                      => $intake,
            'output'                      => $output,
            'systolic_morning'            => $systolic_morning,
            'diastolic_morning'           => $diastolic_morning,
            'systolic_afternoon'          => $systolic_afternoon,
            'diastolic_afternoon'         => $diastolic_afternoon,
            'systolic_evening'            => $systolic_evening,
            'diastolic_evening'           => $diastolic_evening,
            'user_added'                  => $user_id,
            'date_added'                  => date('Y-m-d H:i:s',time()),
            'date_updated'                => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_temperature_chart);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_temperature_chart,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create temperature_chart', $response_data);
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
    
        $temperature_chart_id = $id;
    
        if ($temperature_chart_id === 0 || $temperature_chart_id === "0") {
            $this->REST_Return(400, 'temperature_chart_id is required');
            return;
        }
        
        $select = 'a.id';
        $temperature_chart = $this->$model->select($temperature_chart_id, $select);
        
        if (count($temperature_chart) == 0) {
            $this->REST_Return(404, 'there is no temperature_chart with id ' . $temperature_chart_id);
            return;
        }

        $diet = $dt_put['diet'];
        $defecation_form = $dt_put['defecation_form'];
        $defecation_frequency = $dt_put['defecation_frequency'];
        $intake = $dt_put['intake'];
        $output = $dt_put['output'];
        $systolic_morning = $dt_put['systolic_morning'];
        $diastolic_morning = $dt_put['diastolic_morning'];
        $systolic_afternoon = $dt_put['systolic_afternoon'];
        $diastolic_afternoon = $dt_put['diastolic_afternoon'];
        $systolic_evening = $dt_put['systolic_evening'];
        $diastolic_evening = $dt_put['diastolic_evening'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_temperature_chart = array(
            'diet'                  => $diet,
            'defecation_form'       => $defecation_form,
            'defecation_frequency'  => $defecation_frequency,
            'intake'                => $intake,
            'output'                => $output,
            'systolic_morning'      => $systolic_morning,
            'diastolic_morning'     => $diastolic_morning,
            'systolic_afternoon'    => $systolic_afternoon,
            'diastolic_afternoon'   => $diastolic_afternoon,
            'systolic_evening'      => $systolic_evening,
            'diastolic_evening'     => $diastolic_evening,
            'user_updated'          => $user_id,
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($temperature_chart_id, $dt_temperature_chart);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_temperature_chart,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update temperature_chart', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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

        $temperature_chart_id = $dt_post['temperature_chart_id'];

        $select = 'a.id';
        $temperature_chart = $this->$model->select($temperature_chart_id, $select);

        if (count($temperature_chart) == 0) {
            $this->REST_Return(404, 'there is no temperature_chart with id ' . $temperature_chart_id);
            return;
        }

        $observation_hours = $dt_post['observation_hours'];
        $rr = $dt_post['rr'];
        $pulse = $dt_post['pulse'];
        $temperature = $dt_post['temperature'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => $observation_hours,
            'rr'                    => $rr,
            'pulse'                 => $pulse,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Temperature_chart_item->create($dt_temperature_chart_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_temperature_chart_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create temperature_chart_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
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
    
        $temperature_chart_item_id = $id;
    
        if ($temperature_chart_item_id === 0 || $temperature_chart_item_id === "0") {
            $this->REST_Return(400, 'temperature_chart_item_id is required');
            return;
        }
        
        $select = 'a.id';
        $temperature_chart_item = $this->Temperature_chart_item->select($temperature_chart_item_id, $select);
        
        if (count($temperature_chart_item) == 0) {
            $this->REST_Return(404, 'there is no temperature_chart_item with id ' . $temperature_chart_item_id);
            return;
        }

        $rr = $dt_put['rr'];
        $pulse = $dt_put['pulse'];
        $temperature = $dt_put['temperature'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_temperature_chart_item = array(
            'rr'            => $rr,
            'pulse'         => $pulse,
            'temperature'   => $temperature,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s',time()),
        );
    
        $this->Temperature_chart_item->update($temperature_chart_item_id, $dt_temperature_chart_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_temperature_chart_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update temperature_chart_item', $response_data);
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
            $this->REST_Return(400, 'temperature_chart_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $temperature_chart_id = $this->$model->select_id($id);
    
        if ($temperature_chart_id == "") {
            $this->REST_Return(404, 'there is no temperature_chart with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_temperature_chart = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($temperature_chart_id, $dt_temperature_chart);

        $where = array(
            'temperature_chart_id' => $temperature_chart_id
        );
        $this->Temperature_chart_item->update_v2(0, $dt_temperature_chart, $where);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_temperature_chart,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete temperature_chart', $response_data);
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
            $this->REST_Return(400, 'temperature_chart_item_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $temperature_chart_item_id = $this->Temperature_chart_item->select_id($id);
    
        if ($temperature_chart_item_id == "") {
            $this->REST_Return(404, 'there is no temperature_chart_item with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_temperature_chart_item = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
            'is_delete' => $is_delete,
        );
    
        $this->Temperature_chart_item->update($temperature_chart_item_id, $dt_temperature_chart_item);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_temperature_chart_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete temperature_chart_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}