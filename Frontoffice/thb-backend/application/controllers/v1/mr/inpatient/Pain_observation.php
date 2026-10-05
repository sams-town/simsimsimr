<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Pain_observation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Diet_model", "Diet");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/User_model", "User");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_date', 'label' => 'observation_date', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_date', 'label' => 'observation_date', 'rules' => 'trim|required'),
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
        $employee_nurse_2_id = $dt_post['employee_nurse_2_id'];

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

        if ($employee_nurse_2_id != null) {
            $select = 'a.id';
            $employee_2 = $this->Employee->select($employee_nurse_2_id, $select);
            if (count($employee_2) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_2_id);
                return;
            }
        }

        $observation_date = $dt_post['observation_date'];
        $pulse = $dt_post['pulse'];
        $location = $dt_post['location'];
        $characteristics = $dt_post['characteristics'];
        $sore_score = $dt_post['sore_score'];
        $analgesic_routine = $dt_post['analgesic_routine'];
        $non_analgesic_routine = $dt_post['non_analgesic_routine'];
        $analgesic_extra = $dt_post['analgesic_extra'];
        $non_analgesic_extra = $dt_post['non_analgesic_extra'];
        $dose = $dt_post['dose'];
        $route = $dt_post['route'];
        $sore_score_after = $dt_post['sore_score_after'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_pain_observation = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'employee_nurse_2_id'           => $employee_nurse_2_id,
            'observation_date'              => $observation_date,
            'pulse'                         => $pulse,
            'location'                      => $location,
            'characteristics'               => $characteristics,
            'sore_score'                    => $sore_score,
            'analgesic_routine'             => $analgesic_routine,
            'non_analgesic_routine'         => $non_analgesic_routine,
            'analgesic_extra'               => $analgesic_extra,
            'non_analgesic_extra'           => $non_analgesic_extra,
            'dose'                          => $dose,
            'route'                         => $route,
            'sore_score_after'              => $sore_score_after,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_pain_observation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pain_observation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create pain_observation', $response_data);
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
    
        $pain_observation_id = $id;
    
        if ($pain_observation_id === 0 || $pain_observation_id === "0") {
            $this->REST_Return(400, 'pain_observation_id is required');
            return;
        }

        $select = 'a.id';
        $pain_observation = $this->$model->select($pain_observation_id, $select);

        if (count($pain_observation) == 0) {
            $this->REST_Return(404, 'there is no pain observation with id ' . $pain_observation_id);
            return;
        }

        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $employee_nurse_2_id = $dt_put['employee_nurse_2_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        if ($employee_nurse_2_id != null) {
            $select = 'a.id';
            $employee_2 = $this->Employee->select($employee_nurse_2_id, $select);
            if (count($employee_2) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_2_id);
                return;
            }
        }

        $observation_date = $dt_put['observation_date'];
        $pulse = $dt_put['pulse'];
        $location = $dt_put['location'];
        $characteristics = $dt_put['characteristics'];
        $sore_score = $dt_put['sore_score'];
        $analgesic_routine = $dt_put['analgesic_routine'];
        $non_analgesic_routine = $dt_put['non_analgesic_routine'];
        $analgesic_extra = $dt_put['analgesic_extra'];
        $non_analgesic_extra = $dt_put['non_analgesic_extra'];
        $dose = $dt_put['dose'];
        $route = $dt_put['route'];
        $sore_score_after = $dt_put['sore_score_after'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_pain_observation = array(
            'employee_nurse_id'             => $employee_nurse_id,
            'employee_nurse_2_id'           => $employee_nurse_2_id,
            'observation_date'              => $observation_date,
            'pulse'                         => $pulse,
            'location'                      => $location,
            'characteristics'               => $characteristics,
            'sore_score'                    => $sore_score,
            'analgesic_routine'             => $analgesic_routine,
            'non_analgesic_routine'         => $non_analgesic_routine,
            'analgesic_extra'               => $analgesic_extra,
            'non_analgesic_extra'           => $non_analgesic_extra,
            'dose'                          => $dose,
            'route'                         => $route,
            'sore_score_after'              => $sore_score_after,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($pain_observation_id, $dt_pain_observation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pain_observation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update pain_observation', $response_data);
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
            $this->REST_Return(400, 'pain_observation_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $pain_observation_id = $this->$model->select_id($id);
    
        if ($pain_observation_id == "") {
            $this->REST_Return(404, 'there is no pain_observation with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_pain_observation = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($pain_observation_id, $dt_pain_observation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pain_observation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete pain_observation', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}