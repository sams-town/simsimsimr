<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Monitored_anesthesia extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");

        $this->create_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|max_length[36]'),
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
    
        $monitored_anesthesia_id = $id;
    
        if ($monitored_anesthesia_id === 0 || $monitored_anesthesia_id === "0") {
            $this->REST_Return(400, 'monitored_anesthesia_id is required');
            return;
        }
    
        $output = $this->$model->read($monitored_anesthesia_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->physiological_status_monitoring = json_decode($output[0]->physiological_status_monitoring);
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

        $operating_room_id = $dt_post['operating_room_id'];

        $select = 'a.id';
        $operating_room = $this->Operating_room->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }
    
        $past_medical_history = $dt_post['past_medical_history'];
        $allergy_history = $dt_post['allergy_history'];
        $physiological_status_monitoring = $dt_post['physiological_status_monitoring'];

        $physiological_status_monitoring_data = array(
            'data' => array(),
        );

        for ($i=0; $i < count($physiological_status_monitoring); $i++) { 
            $monitoring_time = $physiological_status_monitoring[$i]['monitoring_time'];
            $awareness = $physiological_status_monitoring[$i]['awareness'];
            $systolic = $physiological_status_monitoring[$i]['systolic'];
            $diastolic = $physiological_status_monitoring[$i]['diastolic'];
            $pulse_frequency = $physiological_status_monitoring[$i]['pulse_frequency'];
            $breath_frequency = $physiological_status_monitoring[$i]['breath_frequency'];
            $temperature = $physiological_status_monitoring[$i]['temperature'];
            $fluid = $physiological_status_monitoring[$i]['fluid'];
            $drugs = $physiological_status_monitoring[$i]['drugs'];
            $information = $physiological_status_monitoring[$i]['information'];

            
            $dt_physiological_status_monitoring = array(
                'monitoring_time'   => $monitoring_time,
                'awareness'         => $awareness,
                'systolic'          => $systolic,
                'diastolic'         => $diastolic,
                'pulse_frequency'   => $pulse_frequency,
                'breath_frequency'  => $breath_frequency,
                'temperature'       => $temperature,
                'fluid'             => $fluid,
                'drugs'             => $drugs,
                'information'       => $information,
            );

            array_push($physiological_status_monitoring_data['data'], $dt_physiological_status_monitoring);
        }

        $physiological_status_monitoring_encode = json_encode($physiological_status_monitoring_data);

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_preanesthesia_evaluation = array(
            'operating_room_id'                 => $operating_room_id,
            'past_medical_history'              => $past_medical_history,
            'allergy_history'                   => $allergy_history,
            'physiological_status_monitoring'   => $physiological_status_monitoring_encode,
            'user_added'                        => $user_id,
            'date_added'                        => date('Y-m-d H:i:s',time()),
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
        
        $id = $this->$model->create($dt_preanesthesia_evaluation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_preanesthesia_evaluation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create preanesthesia_evaluation', $response_data);
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
    
        $monitored_anesthesia_id = $id;
    
        if ($monitored_anesthesia_id === 0 || $monitored_anesthesia_id === "0") {
            $this->REST_Return(400, 'monitored_anesthesia_id is required');
            return;
        }

        $select = 'a.id';
        $monitored_anesthesia = $this->$model->select($monitored_anesthesia_id, $select);

        if (count($monitored_anesthesia) == 0) {
            $this->REST_Return(404, 'there is no monitored_anesthesia with id ' . $monitored_anesthesia_id);
            return;
        }

        $past_medical_history = $dt_put['past_medical_history'];
        $allergy_history = $dt_put['allergy_history'];
        $physiological_status_monitoring = $dt_put['physiological_status_monitoring'];

        $physiological_status_monitoring_data = array(
            'data' => array(),
        );

        for ($i=0; $i < count($physiological_status_monitoring); $i++) { 
            $monitoring_time = $physiological_status_monitoring[$i]['monitoring_time'];
            $awareness = $physiological_status_monitoring[$i]['awareness'];
            $systolic = $physiological_status_monitoring[$i]['systolic'];
            $diastolic = $physiological_status_monitoring[$i]['diastolic'];
            $pulse_frequency = $physiological_status_monitoring[$i]['pulse_frequency'];
            $breath_frequency = $physiological_status_monitoring[$i]['breath_frequency'];
            $temperature = $physiological_status_monitoring[$i]['temperature'];
            $fluid = $physiological_status_monitoring[$i]['fluid'];
            $drugs = $physiological_status_monitoring[$i]['drugs'];
            $information = $physiological_status_monitoring[$i]['information'];

            
            $dt_physiological_status_monitoring = array(
                'monitoring_time'   => $monitoring_time,
                'awareness'         => $awareness,
                'systolic'          => $systolic,
                'diastolic'         => $diastolic,
                'pulse_frequency'   => $pulse_frequency,
                'breath_frequency'  => $breath_frequency,
                'temperature'       => $temperature,
                'fluid'             => $fluid,
                'drugs'             => $drugs,
                'information'       => $information,
            );

            array_push($physiological_status_monitoring_data['data'], $dt_physiological_status_monitoring);
        }

        $physiological_status_monitoring_encode = json_encode($physiological_status_monitoring_data);
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_monitored_anesthesia = array(
            'past_medical_history'              => $past_medical_history,
            'allergy_history'                   => $allergy_history,
            'physiological_status_monitoring'   => $physiological_status_monitoring_encode,
            'user_updated'                      => $user_id,
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($monitored_anesthesia_id, $dt_monitored_anesthesia);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_monitored_anesthesia,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update monitored_anesthesia', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_post_surgery_put($p = 0) {
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
    
        $monitored_anesthesia_id = $id;
    
        if ($monitored_anesthesia_id === 0 || $monitored_anesthesia_id === "0") {
            $this->REST_Return(400, 'monitored_anesthesia_id is required');
            return;
        }

        $select = 'a.id';
        $monitored_anesthesia = $this->$model->select($monitored_anesthesia_id, $select);

        if (count($monitored_anesthesia) == 0) {
            $this->REST_Return(404, 'there is no monitored_anesthesia with id ' . $monitored_anesthesia_id);
            return;
        }

        $post_surgery_awareness = $dt_put['post_surgery_awareness'];
        $post_surgery_systolic = $dt_put['post_surgery_systolic'];
        $post_surgery_diastolic = $dt_put['post_surgery_diastolic'];
        $post_surgery_pulse = $dt_put['post_surgery_pulse'];
        $post_surgery_respiratory_rate = $dt_put['post_surgery_respiratory_rate'];
        $post_surgery_temperature = $dt_put['post_surgery_temperature'];
        $post_surgery_allergic_reaction = $dt_put['post_surgery_allergic_reaction'];
        $post_surgery_other_complications = $dt_put['post_surgery_other_complications'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_monitored_anesthesia = array(
            'post_surgery_awareness'            => $post_surgery_awareness,
            'post_surgery_systolic'             => $post_surgery_systolic,
            'post_surgery_diastolic'            => $post_surgery_diastolic,
            'post_surgery_pulse'                => $post_surgery_pulse,
            'post_surgery_respiratory_rate'     => $post_surgery_respiratory_rate,
            'post_surgery_temperature'          => $post_surgery_temperature,
            'post_surgery_allergic_reaction'    => $post_surgery_allergic_reaction,
            'post_surgery_other_complications'  => $post_surgery_other_complications,
            'user_updated'                      => $user_id,
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($monitored_anesthesia_id, $dt_monitored_anesthesia);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_monitored_anesthesia,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update monitored_anesthesia', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}