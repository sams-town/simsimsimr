<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Anesthesia_consent extends My_Controller {
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
    
        $anesthesia_consent_id = $id;
    
        if ($anesthesia_consent_id === 0 || $anesthesia_consent_id === "0") {
            $this->REST_Return(400, 'anesthesia_consent_id is required');
            return;
        }
    
        $output = $this->$model->read($anesthesia_consent_id);
        $response_data = array();

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

        $anesthesia_type_id = $dt_post['anesthesia_type_id'];
        $analgesia_type_id = $dt_post['analgesia_type_id'];
        $patient_specific_conditions = $dt_post['patient_specific_conditions'];
        $complicating_factors = $dt_post['complicating_factors'];
        $risks_after_surgery = $dt_post['risks_after_surgery'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_anesthesia_consent = array(
            'operating_room_id'             => $operating_room_id,
            'anesthesia_type_id'            => $anesthesia_type_id,
            'analgesia_type_id'             => $analgesia_type_id,
            'patient_specific_conditions'   => $patient_specific_conditions,
            'complicating_factors'          => $complicating_factors,
            'risks_after_surgery'           => $risks_after_surgery,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
        
        $id = $this->$model->create($dt_anesthesia_consent);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_anesthesia_consent,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create anesthesia_consent', $response_data);
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
    
        $anesthesia_consent_id = $id;
    
        if ($anesthesia_consent_id === 0 || $anesthesia_consent_id === "0") {
            $this->REST_Return(400, 'anesthesia_consent_id is required');
            return;
        }

        $select = 'a.id';
        $anesthesia_consent = $this->$model->select($anesthesia_consent_id, $select);

        if (count($anesthesia_consent) == 0) {
            $this->REST_Return(404, 'there is no anesthesia_consent with id ' . $anesthesia_consent_id);
            return;
        }

        $anesthesia_type_id = $dt_put['anesthesia_type_id'];
        $analgesia_type_id = $dt_put['analgesia_type_id'];
        $patient_specific_conditions = $dt_put['patient_specific_conditions'];
        $complicating_factors = $dt_put['complicating_factors'];
        $risks_after_surgery = $dt_put['risks_after_surgery'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_anesthesia_consent = array(
            'anesthesia_type_id'            => $anesthesia_type_id,
            'analgesia_type_id'             => $analgesia_type_id,
            'patient_specific_conditions'   => $patient_specific_conditions,
            'complicating_factors'          => $complicating_factors,
            'risks_after_surgery'           => $risks_after_surgery,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($anesthesia_consent_id, $dt_anesthesia_consent);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_anesthesia_consent,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update anesthesia_consent', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}