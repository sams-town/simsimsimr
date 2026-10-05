<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Early_warning_score extends My_Controller {
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
            array('field' => 'early_warning_score_id', 'label' => 'early_warning_score_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]')
        );

        $this->update_rules = array(
            array('field' => 'observation_date', 'label' => 'observation_date', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_item_rules = array(
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]')
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
    
        $early_warning_score_id = $id;
    
        if ($early_warning_score_id === 0 || $early_warning_score_id === "0") {
            $this->REST_Return(400, 'early_warning_score_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($early_warning_score_id);
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
            $ews_id = $output->rows[$i]->id;

            $select = 'a.*, e.fullname as employee_nurse_name';
            $where = array('early_warning_score_id' => $ews_id, 'a.is_delete' => 0);
            $ews_item = $this->Early_warning_score_item->select(0, $select, $where);

            $output->rows[$i]->early_warning_score_item = $ews_item;
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
    
        $registration_destination = $this->Registration_destination->select_id($registration_destination_id);

        if ($registration_destination == "") {
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
        $early_warning_score = $this->$model->select(0, $select, $where);

        if (count($early_warning_score) > 0) {
            $this->REST_Return(409, 'early_warning_score with observation_date ' . $observation_date . ' is already exist');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_early_warning_score = array(
            'registration_destination_id'   => $registration_destination_id,
            'observation_date'              => $observation_date,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $id = $this->$model->create($dt_early_warning_score);
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_early_warning_score,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create early_warning_score', $response_data);
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

        $early_warning_score_id = $dt_post['early_warning_score_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $select = 'a.id';
        $early_warning_score = $this->$model->select($early_warning_score_id, $select);

        if (count($early_warning_score) == 0) {
            $this->REST_Return(404, 'there is no early_warning_score with id ' . $early_warning_score_id);
            return;
        }
    
        $observation_time = $dt_post['observation_time'];
        $aware = $dt_post['aware'];
        $eye_score = $dt_post['eye_score'];
        $verbal_score = $dt_post['verbal_score'];
        $motor_score = $dt_post['motor_score'];
        $pupil_score = $dt_post['pupil_score'];
        $verbal_response = $dt_post['verbal_response'];
        $blood_pressure = (float) $dt_post['blood_pressure'];
        $pulse = (float) $dt_post['pulse'];
        $breathing = (float) $dt_post['breathing'];
        $temperature = (float) $dt_post['temperature'];
        $oxygen_saturation = (float) $dt_post['oxygen_saturation'];
        $oxygen = $dt_post['oxygen'];
        $falling_scale = $dt_post['falling_scale'];
        $pain_scale = $dt_post['pain_scale'];
        $intake = $dt_post['intake'];
        $output = $dt_post['output'];
        $diit = $dt_post['diit'];  

        $aware_score = 0;
        if ($aware != 3) {
            $aware_score = 3;
        }

        $blood_pressure_score = 0;
        if ($blood_pressure > 210 || $blood_pressure < 81) {
            $blood_pressure_score = 3;
        } elseif ($blood_pressure > 80 && $blood_pressure < 91) {
            $blood_pressure_score = 2;
        } elseif ($blood_pressure > 90 && $blood_pressure < 101) {
            $blood_pressure_score = 1;
        }
        

        $pulse_score = 0;
        if ($pulse > 120 || $pulse < 40) {
            $pulse_score = 3;
        } elseif ($pulse > 100 && $pulse < 121) {
            $pulse_score = 2;
        } elseif ($pulse > 80 && $pulse < 101) {
            $pulse_score = 1;
        }

        $breathing_score = 0;
        if ($breathing>= 25 || $breathing<= 8) {
            $breathing_score = 3;
        } elseif ($breathing>= 21 && $breathing<= 24) {
            $breathing_score = 2;
        } elseif ($breathing>= 9 && $breathing<= 11) {
            $breathing_score = 1;
        }

        $temperature_score = 0;
        if ($temperature < 35) {
            $temperature_score = 3;
        } elseif ($temperature > 38) {
            $temperature_score = 2;
        } elseif ($temperature > 37 && $temperature < 38.1) {
            $temperature_score = 1;
        }

        $oxygen_saturation_score = 0;
        if ($oxygen_saturation <= 91) {
            $oxygen_saturation_score = 3;
        } elseif ($oxygen_saturation >= 92 && $oxygen_saturation <= 93) {
            $oxygen_saturation_score = 2;
        } elseif ($oxygen_saturation >= 94 && $oxygen_saturation <= 95) {
            $oxygen_saturation_score = 1;
        }
        
        $oxygen_score = 0;
        if ($oxygen != 2) {
            $oxygen_score = 2;
        }

        $total = $aware_score + $blood_pressure_score + $pulse_score + $breathing_score + $temperature_score + $oxygen_saturation_score + $oxygen_score;

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_early_warning_score_item = array(
            'early_warning_score_id' => $early_warning_score_id,
            'employee_nurse_id'      => $employee_nurse_id,
            'observation_time'       => $observation_time,
            'aware'                  => $aware,
            'eye_score'              => $eye_score,
            'verbal_score'           => $verbal_score,
            'motor_score'            => $motor_score,
            'pupil_score'            => $pupil_score,
            'verbal_response'        => $verbal_response,
            'blood_pressure'         => $blood_pressure,
            'pulse'                  => $pulse,
            'breathing'              => $breathing,
            'temperature'            => $temperature,
            'oxygen_saturation'      => $oxygen_saturation,
            'oxygen'                 => $oxygen,
            'total'                  => $total,
            'falling_scale'          => $falling_scale,
            'pain_scale'             => $pain_scale,
            'intake'                 => $intake,
            'output'                 => $output,
            'diit'                   => $diit,  
            'user_added'             => $user_id,
            'date_added'             => date('Y-m-d H:i:s',time()),
            'date_updated'           => date('Y-m-d H:i:s',time()),
        );

        $id = $this->Early_warning_score_item->create($dt_early_warning_score_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_early_warning_score_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create early_warning_score_item', $response_data);
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
    
        $early_warning_score_id = $id;
    
        if ($early_warning_score_id === 0 || $early_warning_score_id === "0") {
            $this->REST_Return(400, 'early_warning_score_id is required');
            return;
        }

        $select = 'a.id';
        $early_warning_score = $this->$model->select($early_warning_score_id, $select);

        if (count($early_warning_score) == 0) {
            $this->REST_Return(404, 'there is no early_warning_score with id ' . $early_warning_score_id);
            return;
        }

        $observation_date = $dt_put['observation_date'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_early_warning_score = array(
            'observation_date'  => $observation_date,
            'date_updated'      => date('Y-m-d H:i:s'),
        );

        $this->$model->update($early_warning_score_id, $dt_early_warning_score);
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update early_warning_score', $response_data);
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
    
        $early_warning_score_item_id = $id;
    
        if ($early_warning_score_item_id === 0 || $early_warning_score_item_id === "0") {
            $this->REST_Return(400, 'early_warning_score_item_id is required');
            return;
        }

        $select = 'a.id';
        $early_warning_score_item = $this->Early_warning_score_item->select($early_warning_score_item_id, $select);

        if (count($early_warning_score_item) == 0) {
            $this->REST_Return(404, 'there is no early_warning_score_item with id ' . $early_warning_score_item_id);
            return;
        }

        $employee_nurse_id = $dt_put['employee_nurse_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $observation_time = $dt_put['observation_time'];
        $aware = $dt_put['aware'];
        $eye_score = $dt_put['eye_score'];
        $verbal_score = $dt_put['verbal_score'];
        $motor_score = $dt_put['motor_score'];
        $pupil_score = $dt_put['pupil_score'];
        $verbal_response = $dt_put['verbal_response'];
        $blood_pressure = (float) $dt_put['blood_pressure'];
        $pulse = (float) $dt_put['pulse'];
        $breathing = (float) $dt_put['breathing'];
        $temperature = (float) $dt_put['temperature'];
        $oxygen_saturation = (float) $dt_put['oxygen_saturation'];
        $oxygen = $dt_put['oxygen'];
        $falling_scale = $dt_put['falling_scale'];
        $pain_scale = $dt_put['pain_scale'];
        $intake = $dt_put['intake'];
        $output = $dt_put['output'];
        $diit = $dt_put['diit'];    

        $blood_pressure_score = 0;
        if ($blood_pressure >= 220 || $blood_pressure <= 81) {
            $blood_pressure_score = 3;
        } elseif ($blood_pressure >= 91 && $blood_pressure <= 100) {
            $blood_pressure_score = 2;
        } elseif ($blood_pressure >= 92 && $blood_pressure <= 110) {
            $blood_pressure_score = 1;
        }

        $pulse_score = 0;
        if ($pulse >= 131 || $pulse <= 40) {
            $pulse_score = 3;
        } elseif ($pulse >= 111 && $pulse <= 121) {
            $pulse_score = 2;
        } elseif ($pulse >= 91 && $pulse <= 110) {
            $pulse_score = 1;
        }

        $breathing_score = 0;
        if ($breathing >= 25 || $breathing <= 8) {
            $breathing_score = 3;
        } elseif ($breathing >= 21 || $breathing <= 24) {
            $breathing_score = 2;
        } elseif ($breathing >= 9 || $breathing <= 11) {
            $breathing_score = 1;
        }

        $temperature_score = 0;
        if ($temperature <= 35) {
            $temperature_score = 3;
        } elseif ($temperature >= 39.1) {
            $temperature_score = 2;
        } elseif ($temperature >= 38.1 && $temperature <= 39) {
            $temperature_score = 1;
        }

        $oxygen_saturation_score = 0;
        if ($oxygen_saturation <= 91) {
            $oxygen_saturation_score = 3;
        } elseif ($oxygen_saturation >= 92 && $oxygen_saturation <= 93) {
            $oxygen_saturation_score = 2;
        } elseif ($oxygen_saturation >= 94 && $oxygen_saturation <= 95) {
            $oxygen_saturation_score = 1;
        }

        $total = $blood_pressure_score + $pulse_score + $breathing_score + $temperature_score + $oxygen_saturation_score;

        /* Start Transaction */
        $this->db->trans_start();

        $dt_early_warning_score_item = array(
            'employee_nurse_id'       => $employee_nurse_id,
            'observation_time'        => $observation_time,
            'aware'                   => $aware,
            'eye_score'               => $eye_score,
            'verbal_score'            => $verbal_score,
            'motor_score'             => $motor_score,
            'pupil_score'             => $pupil_score,
            'verbal_response'         => $verbal_response,
            'blood_pressure'          => $blood_pressure,
            'pulse'                   => $pulse,
            'breathing'               => $breathing,
            'temperature'             => $temperature,
            'oxygen_saturation'       => $oxygen_saturation,
            'oxygen'                  => $oxygen,
            'total'                   => $total,
            'falling_scale'           => $falling_scale,
            'pain_scale'              => $pain_scale,
            'intake'                  => $intake,
            'output'                  => $output,
            'diit'                    => $diit,
            'date_updated'            => date('Y-m-d H:i:s'),
        );

        $this->Early_warning_score_item->update($early_warning_score_item_id, $dt_early_warning_score_item);
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update early_warning_score_item', $response_data);
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
            $this->REST_Return(400, 'early_warning_score_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $early_warning_score_id = $this->$model->select_id($id);
    
        if ($early_warning_score_id == "") {
            $this->REST_Return(404, 'there is no early_warning_score with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_early_warning_score = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($early_warning_score_id, $dt_early_warning_score);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_early_warning_score,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete early_warning_score', $response_data);
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
            $this->REST_Return(400, 'early_warning_score_item_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $early_warning_score_item_id = $this->Early_warning_score_item->select_id($id);
    
        if ($early_warning_score_item_id == "") {
            $this->REST_Return(404, 'there is no early_warning_score_item with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_early_warning_score_item = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $id = $this->Early_warning_score_item->update($early_warning_score_item_id, $dt_early_warning_score_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_early_warning_score_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete early_warning_score_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}