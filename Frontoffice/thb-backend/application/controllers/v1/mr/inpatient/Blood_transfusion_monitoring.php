<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Blood_transfusion_monitoring extends My_Controller {
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
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->create_item_rules = array(
            array('field' => 'blood_transfusion_id', 'label' => 'blood_transfusion_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_item_rules = array(
            array('field' => 'blood_transfusion_id', 'label' => 'blood_transfusion_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->delete_item_rules = array(
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
    
        $blood_transfusion_id = $id;
    
        if ($blood_transfusion_id === 0 || $blood_transfusion_id === "0") {
            $this->REST_Return(400, 'blood_transfusion_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($blood_transfusion_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->reaction_arry = ($output[0]->reaction_arry != '') ? explode(',',$output[0]->reaction_arry) : [];
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
            $blood_transfusion_id = $output->rows[$i]->id;

            $select = 'a.*';
            $where = array('blood_transfusion_monitoring_id' => $blood_transfusion_id, 'a.is_delete' => 0);
            $blood_transfusion_items = $this->Blood_transfusion_monitoring_item->select(0, $select, $where);

            $output->rows[$i]->blood_transfusion_items = $blood_transfusion_items;

            $output->rows[$i]->reaction_arry = ($output->rows[$i]->reaction_arry != '') ? explode(',',$output->rows[$i]->reaction_arry) : [];
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

        $bag = $dt_post['bag'];
        $bag_drops = $dt_post['bag_drops'];
        $blood_group = $dt_post['blood_group'];
        $blood_velocity = $dt_post['blood_velocity'];
        $blood_type = $dt_post['blood_type'];
        $blood_amount = $dt_post['blood_amount'];
        $bag_number = $dt_post['bag_number'];
        $expired_date = $dt_post['expired_date'];
        $start_datetime = $dt_post['start_datetime'];
        $finish_datetime = $dt_post['finish_datetime'];
        $reaction_time = $dt_post['reaction_time'];
        $reaction_arry = implode(',', $dt_post['reaction_arry']);
        $reaction = $dt_post['reaction'];
        $transfusion_process_type_id = $dt_post['transfusion_process_type_id'];
        $transfusion_process_time = $dt_post['transfusion_process_time'];
        $intake = $dt_post['intake'];
        $output = $dt_post['output'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_blood_transfusion = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'bag'                           => $bag,
            'bag_drops'                     => $bag_drops,
            'blood_group'                   => $blood_group,
            'blood_velocity'                => $blood_velocity,
            'blood_type'                    => $blood_type,
            'blood_amount'                  => $blood_amount,
            'bag_number'                    => $bag_number,
            'expired_date'                  => $expired_date,
            'start_datetime'                => $start_datetime,
            'finish_datetime'               => $finish_datetime,
            'reaction_time'                 => $reaction_time,
            'reaction_arry'                 => $reaction_arry,
            'reaction'                      => $reaction,
            'transfusion_process_type_id'   => $transfusion_process_type_id,
            'transfusion_process_time'      => $transfusion_process_time,
            'intake'                        => $intake,
            'output'                        => $output,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_blood_transfusion);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_blood_transfusion,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create blood_transfusion', $response_data);
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
    
        $blood_transfusion_id = $id;
    
        if ($blood_transfusion_id === 0 || $blood_transfusion_id === "0") {
            $this->REST_Return(400, 'blood_transfusion_id is required');
            return;
        }

        $select = 'a.id';
        $blood_transfusion = $this->$model->select($blood_transfusion_id, $select);

        if (count($blood_transfusion) == 0) {
            $this->REST_Return(404, 'there is no blood_transfusion with id ' . $blood_transfusion_id);
            return;
        }

        $bag = $dt_put['bag'];
        $bag_drops = $dt_put['bag_drops'];
        $blood_group = $dt_put['blood_group'];
        $blood_velocity = $dt_put['blood_velocity'];
        $blood_type = $dt_put['blood_type'];
        $blood_amount = $dt_put['blood_amount'];
        $bag_number = $dt_put['bag_number'];
        $expired_date = $dt_put['expired_date'];
        $start_datetime = $dt_put['start_datetime'];
        $finish_datetime = $dt_put['finish_datetime'];
        $reaction_time = $dt_put['reaction_time'];
        $reaction_arry = implode(',', $dt_put['reaction_arry']);
        $reaction = $dt_put['reaction'];
        $transfusion_process_type_id = $dt_put['transfusion_process_type_id'];
        $transfusion_process_time = $dt_put['transfusion_process_time'];
        $intake = $dt_put['intake'];
        $output = $dt_put['output'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_blood_transfusion = array(
            'bag'                           => $bag,
            'bag_drops'                     => $bag_drops,
            'blood_group'                   => $blood_group,
            'blood_velocity'                => $blood_velocity,
            'blood_type'                    => $blood_type,
            'blood_amount'                  => $blood_amount,
            'bag_number'                    => $bag_number,
            'expired_date'                  => $expired_date,
            'start_datetime'                => $start_datetime,
            'finish_datetime'               => $finish_datetime,
            'reaction_time'                 => $reaction_time,
            'reaction_arry'                 => $reaction_arry,
            'reaction'                      => $reaction,
            'transfusion_process_type_id'   => $transfusion_process_type_id,
            'transfusion_process_time'      => $transfusion_process_time,
            'intake'                        => $intake,
            'output'                        => $output,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($blood_transfusion_id, $dt_blood_transfusion);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_blood_transfusion,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update blood_transfusion', $response_data);
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
            $this->REST_Return(400, 'blood_transfusion_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $blood_transfusion_id = $this->$model->select_id($id);
    
        if ($blood_transfusion_id == "") {
            $this->REST_Return(404, 'there is no blood_transfusion with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_blood_transfusion = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($blood_transfusion_id, $dt_blood_transfusion);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_blood_transfusion,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete blood_transfusion', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
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

        $blood_transfusion_id = $dt_post['blood_transfusion_id'];

        $select = 'a.id';
        $blood_transfusion = $this->$model->select($blood_transfusion_id, $select);

        if (count($blood_transfusion) == 0) {
            $this->REST_Return(404, 'there is no blood_transfusion with id ' . $blood_transfusion_id);
            return;
        }

        $time = $dt_post['time'];
        $consciousness = $dt_post['consciousness'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $pulse = $dt_post['pulse'];
        $respiratory_rate = $dt_post['respiratory_rate'];
        $temperature = $dt_post['temperature'];
        $sore = $dt_post['sore'];
        $intravenous_access_conditions = $dt_post['intravenous_access_conditions'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_blood_transfusion_item = array(
            'blood_transfusion_monitoring_id' => $blood_transfusion_id,
            'time'                            => $time,
            'consciousness'                   => $consciousness,
            'systolic'                        => $systolic,
            'diastolic'                       => $diastolic,
            'pulse'                           => $pulse,
            'respiratory_rate'                => $respiratory_rate,
            'temperature'                     => $temperature,
            'sore'                            => $sore,
            'intravenous_access_conditions'   => $intravenous_access_conditions,
            'user_added'                      => $user_id,
            'date_added'                      => date('Y-m-d H:i:s',time()),
            'date_updated'                    => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Blood_transfusion_monitoring_item->create($dt_blood_transfusion_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_blood_transfusion_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create blood_transfusion_item', $response_data);
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
    
        $blood_transfusion_item_id = $id;
    
        if ($blood_transfusion_item_id === 0 || $blood_transfusion_item_id === "0") {
            $this->REST_Return(400, 'blood_transfusion_item_id is required');
            return;
        }

        $select = 'a.id';
        $blood_transfusion_item = $this->Blood_transfusion_monitoring_item->select($blood_transfusion_item_id, $select);

        if (count($blood_transfusion_item) == 0) {
            $this->REST_Return(404, 'there is no blood_transfusion_item with id ' . $blood_transfusion_item_id);
            return;
        }

        $time = $dt_put['time'];
        $consciousness = $dt_put['consciousness'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $pulse = $dt_put['pulse'];
        $respiratory_rate = $dt_put['respiratory_rate'];
        $temperature = $dt_put['temperature'];
        $sore = $dt_put['sore'];
        $intravenous_access_conditions = $dt_put['intravenous_access_conditions'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_blood_transfusion_item = array(
            'time'                          => $time,
            'consciousness'                 => $consciousness,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse'                         => $pulse,
            'respiratory_rate'              => $respiratory_rate,
            'temperature'                   => $temperature,
            'sore'                          => $sore,
            'intravenous_access_conditions' => $intravenous_access_conditions,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s'),
        );
    
        $this->Blood_transfusion_monitoring_item->update($blood_transfusion_item_id, $dt_blood_transfusion_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_blood_transfusion_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update blood_transfusion_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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
        $this->form_validation->set_rules($this->delete_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'blood_transfusion_item_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $blood_transfusion_item_id = $this->Blood_transfusion_monitoring_item->select_id($id);
    
        if ($blood_transfusion_item_id == "") {
            $this->REST_Return(404, 'there is no blood_transfusion_item with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_blood_transfusion_item = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->Blood_transfusion_monitoring_item->update($blood_transfusion_item_id, $dt_blood_transfusion_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_blood_transfusion_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete blood_transfusion_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}