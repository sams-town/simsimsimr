<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Vital_signs_monitoring extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Early_warning_score_model", "Early_warning_score");
        $this->load->model("$this->directory/Early_warning_score_item_model", "Early_warning_score_item");
        $this->load->model("$this->directory/Temperature_chart_model", "Temperature_chart");
        $this->load->model("$this->directory/Temperature_chart_item_model", "Temperature_chart_item");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/User_model", "User");
        // Load Helper
        $this->load->helper('ews');

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
    
        $vital_signs_monitoring_id = $id;
    
        if ($vital_signs_monitoring_id === 0 || $vital_signs_monitoring_id === "0") {
            $this->REST_Return(400, 'vital_signs_monitoring_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($vital_signs_monitoring_id);
        $response_data = array();
    
        if (count($output) > 0) {

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
    
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $q_strings['limit']; 
            unset($q_strings['limit']);
        } 
        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($q_strings['offset']);
        } 
    
        $registration_id = $q_strings['registration_id'] ?? null;
        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;

        if ($registration_id == null && $registration_destination_id == null) {
            $this->REST_Return(400, 'registration_id or registration_destination_id is required');
            return;
        }

        if ($registration_destination_id != null) {
            $select = 'a.id, a.registration_id';
            $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
    
            if (count($registration_destination) == 0) {
                $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
                return;
            }
        }

        if ($registration_id != null) {
            $select = 'a.id';
            $registration = $this->Registration->select($registration_id, $select);
    
            if (count($registration) == 0) {
                $this->REST_Return(404, 'there is no registration with id ' . $registration_id);
                return;
            }
        }

        if ($registration_id != null) {
            $q_strings['rd.registration_id'] = $registration_id;
            unset($q_strings['registration_id']);
        } else {
            $registration_id = $registration_destination[0]->registration_id;
        }

        $select = 'a.*, e.fullname as employee_nurse_name';
        $where = $q_strings;
        $where['a.is_delete'] = 0;
        $join = array(
            array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
        );
        $order = array('a.observation_date' => 'DESC');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        if (count($output) == 0) {
            $select = 'a.*, e.fullname as employee_nurse_name';
            $where = array('rd.registration_id' => $registration_id, 'a.is_delete' => 0);
            $join = array(
                array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),
                array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            );
            $order = array('a.observation_date' => 'DESC');
            $output = $this->$model->select(0, $select, $where, $join, $order);
        }

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;
        
		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
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
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $pulse = $dt_post['pulse'];
        $temperature = $dt_post['temperature'];
        $respiratory_rate = $dt_post['respiratory_rate'];
        $oxygen_saturation = $dt_post['oxygen_saturation'];
        $oxygen_type_id = $dt_post['oxygen_type_id'];
        $aware = $dt_post['aware'];
        $eye_score = $dt_post['eye_score'];
        $verbal_score = $dt_post['verbal_score'];
        $motor_score = $dt_post['motor_score'];
        $skin_color = $dt_post['skin_color'];
        $behavior = $dt_post['behavior'];
        $notes = $dt_post['notes'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'observation_date'              => $observation_date,
            'weight'                        => $weight,
            'height'                        => $height,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse'                         => $pulse,
            'temperature'                   => $temperature,
            'respiratory_rate'              => $respiratory_rate,
            'oxygen_saturation'             => $oxygen_saturation,
            'oxygen_type_id'                => $oxygen_type_id,
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'skin_color'                    => $skin_color,
            'behavior'                      => $behavior,
            'notes'                         => $notes,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_vital_signs_monitoring);

        // INPUT EWS
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'observation_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $early_warning_score = $this->Early_warning_score->select(0, $select, $where);

        if (count($early_warning_score) == 0) {
            $dt_early_warning_score = array(
                'registration_destination_id'   => $registration_destination_id,
                'observation_date'              => date('Y-m-d'),
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'date_updated'                  => date('Y-m-d H:i:s',time()),
            );

            $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
        } else {
            $early_warning_score_id = $early_warning_score[0]->id;
        }

        $observation_time = date('H:i:s');
        $blood_pressure = (float) $systolic;
        $pulse = (float) $pulse;
        $breathing = (float) $respiratory_rate;
        $temperature = (float) $temperature;
        $oxygen_saturation = (float) $oxygen_saturation;
        $oxygen = $oxygen_type_id != 2 ? 2 : 0;

        $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
    
        $dt_early_warning_score_item = array(
            'early_warning_score_id' => $early_warning_score_id,
            'employee_nurse_id'      => $employee_nurse_id,
            'observation_time'       => $observation_time,
            'aware'                  => $aware,
            'eye_score'              => $eye_score,
            'verbal_score'           => $verbal_score,
            'motor_score'            => $motor_score,
            'blood_pressure'         => $blood_pressure,
            'pulse'                  => $pulse,
            'breathing'              => $breathing,
            'temperature'            => $temperature,
            'oxygen_saturation'      => $oxygen_saturation,
            'oxygen'                 => $oxygen,
            'total'                  => $ews_score,
            'user_added'             => $user_id,
            'date_added'             => date('Y-m-d H:i:s',time()),
            'date_updated'           => date('Y-m-d H:i:s',time()),
        );

        $this->Early_warning_score_item->create($dt_early_warning_score_item);

        // INPUT TEMPERATURE CHART
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'monitoring_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $temperature_chart = $this->Temperature_chart->select(0, $select, $where);
        if (count($temperature_chart) == 0) {
            $dt_temperature_chart = array(
                'registration_destination_id' => $registration_destination_id,
                'employee_nurse_id'           => $employee_nurse_id,
                'monitoring_date'             => date('Y-m-d'),
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s',time()),
                'date_updated'                => date('Y-m-d H:i:s',time()),
            );
        
            $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
        } else {
            $temperature_chart_id = $temperature_chart[0]->id;
        }
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => date('H:i:s'),
            'rr'                    => $respiratory_rate,
            'pulse'                 => $pulse,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->Temperature_chart_item->create($dt_temperature_chart_item);
    
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

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);
        $user_employee_id = $user[0]->employee_id;
    
        $vital_signs_monitoring_id = $id;
    
        if ($vital_signs_monitoring_id === 0 || $vital_signs_monitoring_id === "0") {
            $this->REST_Return(400, 'vital_signs_monitoring_id is required');
            return;
        }
    
        $select = "a.id, a.registration_destination_id";
        $vital_signs_monitoring = $this->$model->select($vital_signs_monitoring_id, $select);
    
        if (count($vital_signs_monitoring) == 0) {
            $this->REST_Return(404, 'vital_signs_monitoring with id ' . $vital_signs_monitoring_id . ' does not exist!');
            return;
        }
        
        $registration_destination_id = $vital_signs_monitoring[0]->registration_destination_id;

        $observation_date = $dt_put['observation_date'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $pulse = $dt_put['pulse'];
        $temperature = $dt_put['temperature'];
        $respiratory_rate = $dt_put['respiratory_rate'];
        $oxygen_saturation = $dt_put['oxygen_saturation'];
        $oxygen_type_id = $dt_put['oxygen_type_id'];
        $aware = $dt_put['aware'];
        $eye_score = $dt_put['eye_score'];
        $verbal_score = $dt_put['verbal_score'];
        $motor_score = $dt_put['motor_score'];
        $skin_color = $dt_put['skin_color'];
        $behavior = $dt_put['behavior'];
        $notes = $dt_put['notes'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_vital_signs_monitoring = array(
            'observation_date'  => $observation_date,
            'weight'            => $weight,
            'height'            => $height,
            'systolic'          => $systolic,
            'diastolic'         => $diastolic,
            'pulse'             => $pulse,
            'temperature'       => $temperature,
            'respiratory_rate'  => $respiratory_rate,
            'oxygen_saturation' => $oxygen_saturation,
            'oxygen_type_id'    => $oxygen_type_id,
            'aware'             => $aware,
            'eye_score'         => $eye_score,
            'verbal_score'      => $verbal_score,
            'motor_score'       => $motor_score,
            'skin_color'        => $skin_color,
            'behavior'          => $behavior,
            'notes'             => $notes,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($vital_signs_monitoring_id, $dt_vital_signs_monitoring);

        // INPUT EWS
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'observation_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $early_warning_score = $this->Early_warning_score->select(0, $select, $where);

        if (count($early_warning_score) == 0) {
            $dt_early_warning_score = array(
                'registration_destination_id'   => $registration_destination_id,
                'observation_date'              => date('Y-m-d'),
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'date_updated'                  => date('Y-m-d H:i:s',time()),
            );

            $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
        } else {
            $early_warning_score_id = $early_warning_score[0]->id;
        }

        $observation_time = date('H:i:s');
        $blood_pressure = (float) $systolic;
        $pulse = (float) $pulse;
        $breathing = (float) $respiratory_rate;
        $temperature = (float) $temperature;
        $oxygen_saturation = (float) $oxygen_saturation;
        $oxygen = $oxygen_type_id == 1 ? 2 : 0;

        $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
    
        $dt_early_warning_score_item = array(
            'early_warning_score_id' => $early_warning_score_id,
            'employee_nurse_id'      => $user_employee_id,
            'observation_time'       => $observation_time,
            'aware'                  => $aware,
            'eye_score'              => $eye_score,
            'verbal_score'           => $verbal_score,
            'motor_score'            => $motor_score,
            'blood_pressure'         => $blood_pressure,
            'pulse'                  => $pulse,
            'breathing'              => $breathing,
            'temperature'            => $temperature,
            'oxygen_saturation'      => $oxygen_saturation,
            'oxygen'                 => $oxygen,
            'total'                  => $ews_score,
            'user_added'             => $user_id,
            'date_added'             => date('Y-m-d H:i:s',time()),
            'date_updated'           => date('Y-m-d H:i:s',time()),
        );

        $this->Early_warning_score_item->create($dt_early_warning_score_item);

        // INPUT TEMPERATURE CHART
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'monitoring_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $temperature_chart = $this->Temperature_chart->select(0, $select, $where);
        if (count($temperature_chart) == 0) {
            $dt_temperature_chart = array(
                'registration_destination_id' => $registration_destination_id,
                'employee_nurse_id'           => $user_employee_id,
                'monitoring_date'             => date('Y-m-d'),
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s',time()),
                'date_updated'                => date('Y-m-d H:i:s',time()),
            );
        
            $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
        } else {
            $temperature_chart_id = $temperature_chart[0]->id;
        }
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => date('H:i:s'),
            'rr'                    => $respiratory_rate,
            'pulse'                 => $pulse,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->Temperature_chart_item->create($dt_temperature_chart_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_vital_signs_monitoring,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update vital_signs_monitoring', $response_data);
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
            $this->REST_Return(400, 'vital_signs_monitoring_id is required');
            return;
        }
    
        $vital_signs_monitoring_id = $this->$model->select_id($id);
    
        if ($vital_signs_monitoring_id == "") {
            $this->REST_Return(404, 'there is no vital_signs_monitoring with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_vital_signs_monitoring = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($vital_signs_monitoring_id, $dt_vital_signs_monitoring);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_vital_signs_monitoring,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete vital_signs_monitoring', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}