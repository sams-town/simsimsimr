<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Fetal_heart_rate_monitoring extends My_Controller {
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
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
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
    
        $registration_destination = $this->Registration_destination->select($registration_destination_id);
    
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

        $observation_date = $dt_post['observation_date'];
        $uterine_tone = $dt_post['uterine_tone'];
        $uterine_contractions = $dt_post['uterine_contractions'];
        $uterine_contractions_duration = $dt_post['uterine_contractions_duration'];
        $interval = $dt_post['interval'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $fetal_heart_rate = $dt_post['fetal_heart_rate'];
        $action_plan = $dt_post['action_plan'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'observation_date'              => $observation_date,
            'uterine_tone'                  => $uterine_tone,
            'uterine_contractions'          => $uterine_contractions,
            'uterine_contractions_duration' => $uterine_contractions_duration,
            'interval'                      => $interval,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'fetal_heart_rate'              => $fetal_heart_rate,
            'action_plan'                   => $action_plan,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_vital_signs_monitoring);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_vital_signs_monitoring,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create vital_signs_monitoring', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
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
    
        $fetal_hearth_rate_monitoring_id = $id;
    
        if ($fetal_hearth_rate_monitoring_id === 0 || $fetal_hearth_rate_monitoring_id === "0") {
            $this->REST_Return(400, 'fetal_hearth_rate_monitoring_id is required');
            return;
        }
    
        $select = "a.id";
        $fetal_hearth_rate_monitoring = $this->$model->select($fetal_hearth_rate_monitoring_id, $select);
    
        if (count($fetal_hearth_rate_monitoring) == 0) {
            $this->REST_Return(404, 'fetal_hearth_rate_monitoring with id ' . $fetal_hearth_rate_monitoring_id . ' does not exist!');
            return;
        }

        $observation_date = $dt_put['observation_date'];
        $uterine_tone = $dt_put['uterine_tone'];
        $uterine_contractions = $dt_put['uterine_contractions'];
        $uterine_contractions_duration = $dt_put['uterine_contractions_duration'];
        $interval = $dt_put['interval'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $fetal_heart_rate = $dt_put['fetal_heart_rate'];
        $action_plan = $dt_put['action_plan'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fetal_hearth_rate_monitoring = array(
            'observation_date'              => $observation_date,
            'uterine_tone'                  => $uterine_tone,
            'uterine_contractions'          => $uterine_contractions,
            'uterine_contractions_duration' => $uterine_contractions_duration,
            'interval'                      => $interval,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'fetal_heart_rate'              => $fetal_heart_rate,
            'action_plan'                   => $action_plan,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($fetal_hearth_rate_monitoring_id, $dt_fetal_hearth_rate_monitoring);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fetal_hearth_rate_monitoring,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update fetal_hearth_rate_monitoring', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
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
            $this->REST_Return(400, 'fetal_heart_rate_monitoring_id is required');
            return;
        }
    
        $fetal_heart_rate_monitoring_id = $this->$model->select_id($id);
    
        if ($fetal_heart_rate_monitoring_id == "") {
            $this->REST_Return(404, 'there is no fetal_heart_rate_monitoring with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_fetal_heart_rate_monitoring = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($fetal_heart_rate_monitoring_id, $dt_fetal_heart_rate_monitoring);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fetal_heart_rate_monitoring,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete fetal_heart_rate_monitoring', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}