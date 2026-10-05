<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Sign_in extends My_Controller {
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
    
        $sign_in_id = $id;
    
        if ($sign_in_id === 0 || $sign_in_id === "0") {
            $this->REST_Return(400, 'sign_in_id is required');
            return;
        }
    
        $output = $this->$model->read($sign_in_id);
        $response_data = array();
    
        if (count($output) > 0) {
            $output[0]->confirmation_arry = ($output[0]->confirmation_arry != "") ? explode(',', $output[0]->confirmation_arry) : [];
            $output[0]->supporting_results_arry = ($output[0]->supporting_results_arry != "") ? explode(',', $output[0]->supporting_results_arry) : [];
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

        $confirmation_arry = implode(',', $dt_post['confirmation_arry']);
        $location_operation = $dt_post['location_operation'];
        $operating_procedure = $dt_post['operating_procedure'];
        $operating_area_type_id = $dt_post['operating_area_type_id'];
        $is_check_completeness = $dt_post['is_check_completeness'];
        $is_check_anesthesia = $dt_post['is_check_anesthesia'];
        $is_check_tools = $dt_post['is_check_tools'];
        $is_check_sterility = $dt_post['is_check_sterility'];
        $is_fasting = $dt_post['is_fasting'];
        $fasting_hours = $dt_post['fasting_hours'];
        $history_asthma_type_id = $dt_post['history_asthma_type_id'];
        $allergy_history_type_id = $dt_post['allergy_history_type_id'];
        $allergy_history_name = $dt_post['allergy_history_name'];
        $special_equipment_type_id = $dt_post['special_equipment_type_id'];
        $special_equipment_name = $dt_post['special_equipment_name'];
        $supporting_results_arry = implode(',', $dt_post['supporting_results_arry']);
        $blood_loss_type_id = $dt_post['blood_loss_type_id'];
        $access_iv_no = $dt_post['access_iv_no'];
        $blood_preparation = $dt_post['blood_preparation'];
        $blood_type = $dt_post['blood_type'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_sign_in = array(
            'operating_room_id'            => $operating_room_id,
            'confirmation_arry'            => $confirmation_arry,
            'location_operation'           => $location_operation,
            'operating_procedure'          => $operating_procedure,
            'operating_area_type_id'       => $operating_area_type_id,
            'is_check_completeness'        => $is_check_completeness,
            'is_check_anesthesia'          => $is_check_anesthesia,
            'is_check_tools'               => $is_check_tools,
            'is_check_sterility'           => $is_check_sterility,
            'is_fasting'                   => $is_fasting,
            'fasting_hours'                => $fasting_hours,
            'history_asthma_type_id'       => $history_asthma_type_id,
            'allergy_history_type_id'      => $allergy_history_type_id,
            'allergy_history_name'         => $allergy_history_name,
            'special_equipment_type_id'    => $special_equipment_type_id,
            'special_equipment_name'       => $special_equipment_name,
            'supporting_results_arry'      => $supporting_results_arry,
            'blood_loss_type_id'           => $blood_loss_type_id,
            'access_iv_no'                 => $access_iv_no,
            'blood_preparation'            => $blood_preparation,
            'blood_type'                   => $blood_type,
            'user_added'                   => $user_id,
            'date_added'                   => date('Y-m-d H:i:s',time()),
            'date_updated'                 => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_sign_in);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sign_in,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create sign_in', $response_data);
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
    
        $sign_in_id = $id;
    
        if ($sign_in_id === 0 || $sign_in_id === "0") {
            $this->REST_Return(400, 'sign_in_id is required');
            return;
        }

        $select = 'a.id';
        $sign_in = $this->$model->select($sign_in_id, $select);

        if (count($sign_in) == 0) {
            $this->REST_Return(404, 'there is no sign_in with id ' . $sign_in_id);
            return;
        }

        $confirmation_arry = implode(',', $dt_put['confirmation_arry']);
        $location_operation = $dt_put['location_operation'];
        $operating_procedure = $dt_put['operating_procedure'];
        $operating_area_type_id = $dt_put['operating_area_type_id'];
        $is_check_completeness = $dt_put['is_check_completeness'];
        $is_check_anesthesia = $dt_put['is_check_anesthesia'];
        $is_check_tools = $dt_put['is_check_tools'];
        $is_check_sterility = $dt_put['is_check_sterility'];
        $is_fasting = $dt_put['is_fasting'];
        $fasting_hours = $dt_put['fasting_hours'];
        $history_asthma_type_id = $dt_put['history_asthma_type_id'];
        $allergy_history_type_id = $dt_put['allergy_history_type_id'];
        $allergy_history_name = $dt_put['allergy_history_name'];
        $special_equipment_type_id = $dt_put['special_equipment_type_id'];
        $special_equipment_name = $dt_put['special_equipment_name'];
        $supporting_results_arry = implode(',', $dt_put['supporting_results_arry']);
        $blood_loss_type_id = $dt_put['blood_loss_type_id'];
        $access_iv_no = $dt_put['access_iv_no'];
        $blood_preparation = $dt_put['blood_preparation'];
        $blood_type = $dt_put['blood_type'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_sign_in = array(
            'confirmation_arry'            => $confirmation_arry,
            'location_operation'           => $location_operation,
            'operating_procedure'          => $operating_procedure,
            'operating_area_type_id'       => $operating_area_type_id,
            'is_check_completeness'        => $is_check_completeness,
            'is_check_anesthesia'          => $is_check_anesthesia,
            'is_check_tools'               => $is_check_tools,
            'is_check_sterility'           => $is_check_sterility,
            'is_fasting'                   => $is_fasting,
            'fasting_hours'                => $fasting_hours,
            'history_asthma_type_id'       => $history_asthma_type_id,
            'allergy_history_type_id'      => $allergy_history_type_id,
            'allergy_history_name'         => $allergy_history_name,
            'special_equipment_type_id'    => $special_equipment_type_id,
            'special_equipment_name'       => $special_equipment_name,
            'supporting_results_arry'      => $supporting_results_arry,
            'blood_loss_type_id'           => $blood_loss_type_id,
            'access_iv_no'                 => $access_iv_no,
            'blood_preparation'            => $blood_preparation,
            'blood_type'                   => $blood_type,
            'user_updated'                 => $user_id,
            'date_updated'                 => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($sign_in_id, $dt_sign_in);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sign_in,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update sign_in', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}