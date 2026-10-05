<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class sign_out extends My_Controller {
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
    
        $sign_out_id = $id;
    
        if ($sign_out_id === 0 || $sign_out_id === "0") {
            $this->REST_Return(400, 'sign_out_id is required');
            return;
        }
    
        $output = $this->$model->read($sign_out_id);
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

        $is_read_verbally = $dt_post['is_read_verbally'];
        $is_read_amount = $dt_post['is_read_amount'];
        $is_read_completeness = $dt_post['is_read_completeness'];
        $is_read_completeness_document = $dt_post['is_read_completeness_document'];
        $throat_pack_type_id = $dt_post['throat_pack_type_id'];
        $network_lift_type_id = $dt_post['network_lift_type_id'];
        $implant_installation_type_id = $dt_post['implant_installation_type_id'];
        $tool_damaged_type_id = $dt_post['tool_damaged_type_id'];
        $tool_damaged = $dt_post['tool_damaged'];
        $information = $dt_post['information'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_sign_out = array(
            'operating_room_id'            => $operating_room_id,
            'is_read_verbally'              => $is_read_verbally,
            'is_read_amount'                => $is_read_amount,
            'is_read_completeness'          => $is_read_completeness,
            'is_read_completeness_document' => $is_read_completeness_document,
            'throat_pack_type_id'           => $throat_pack_type_id,
            'network_lift_type_id'          => $network_lift_type_id,
            'implant_installation_type_id'  => $implant_installation_type_id,
            'tool_damaged_type_id'          => $tool_damaged_type_id,
            'tool_damaged'                  => $tool_damaged,
            'information'                   => $information,
            'user_added'                    => $user_id,
            'user_updated'                  => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_sign_out);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sign_out,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create sign_out', $response_data);
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
    
        $sign_out_id = $id;
    
        if ($sign_out_id === 0 || $sign_out_id === "0") {
            $this->REST_Return(400, 'sign_out_id is required');
            return;
        }

        $select = 'a.id';
        $sign_out = $this->$model->select($sign_out_id, $select);

        if (count($sign_out) == 0) {
            $this->REST_Return(404, 'there is no sign_out with id ' . $sign_out_id);
            return;
        }

        $is_read_verbally = $dt_put['is_read_verbally'];
        $is_read_amount = $dt_put['is_read_amount'];
        $is_read_completeness = $dt_put['is_read_completeness'];
        $is_read_completeness_document = $dt_put['is_read_completeness_document'];
        $throat_pack_type_id = $dt_put['throat_pack_type_id'];
        $network_lift_type_id = $dt_put['network_lift_type_id'];
        $implant_installation_type_id = $dt_put['implant_installation_type_id'];
        $tool_damaged_type_id = $dt_put['tool_damaged_type_id'];
        $tool_damaged = $dt_put['tool_damaged'];
        $information = $dt_put['information'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_sign_out = array(
            'is_read_verbally'              => $is_read_verbally,
            'is_read_amount'                => $is_read_amount,
            'is_read_completeness'          => $is_read_completeness,
            'is_read_completeness_document' => $is_read_completeness_document,
            'throat_pack_type_id'           => $throat_pack_type_id,
            'network_lift_type_id'          => $network_lift_type_id,
            'implant_installation_type_id'  => $implant_installation_type_id,
            'tool_damaged_type_id'          => $tool_damaged_type_id,
            'tool_damaged'                  => $tool_damaged,
            'information'                   => $information,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($sign_out_id, $dt_sign_out);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sign_out,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update sign_out', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}