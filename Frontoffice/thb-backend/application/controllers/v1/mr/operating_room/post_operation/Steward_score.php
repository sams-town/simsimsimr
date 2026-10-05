<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Steward_score extends My_Controller {
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
    
        $steward_id = $id;
    
        if ($steward_id === 0 || $steward_id === "0") {
            $this->REST_Return(400, 'steward_id is required');
            return;
        }
    
        $output = $this->$model->read($steward_id);
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

        $consciousness_zero = $dt_post['consciousness_zero'];
        $consciousness_one = $dt_post['consciousness_one'];
        $consciousness_two = $dt_post['consciousness_two'];
        $respiration_zero = $dt_post['respiration_zero'];
        $respiration_one = $dt_post['respiration_one'];
        $respiration_two = $dt_post['respiration_two'];
        $motor_activities_zero = $dt_post['motor_activities_zero'];
        $motor_activities_one = $dt_post['motor_activities_one'];
        $motor_activities_two = $dt_post['motor_activities_two'];
        $total_score = $dt_post['total_score'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_steward  = array(
            'operating_room_id'     => $operating_room_id,
            'consciousness_zero'    => $consciousness_zero,
            'consciousness_one'     => $consciousness_one,
            'consciousness_two'     => $consciousness_two,
            'respiration_zero'      => $respiration_zero,
            'respiration_one'       => $respiration_one,
            'respiration_two'       => $respiration_two,
            'motor_activities_zero' => $motor_activities_zero,
            'motor_activities_one'  => $motor_activities_one,
            'motor_activities_two'  => $motor_activities_two,
            'total_score'           => $total_score,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_steward);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_steward ,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create steward_score', $response_data);
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
    
        $steward_id = $id;
    
        if ($steward_id === 0 || $steward_id === "0") {
            $this->REST_Return(400, 'steward_id is required');
            return;
        }

        $select = 'a.id';
        $steward = $this->$model->select($steward_id, $select);

        if (count($steward) == 0) {
            $this->REST_Return(404, 'there is no steward with id ' . $steward_id);
            return;
        }

        $consciousness_zero = $dt_put['consciousness_zero'];
        $consciousness_one = $dt_put['consciousness_one'];
        $consciousness_two = $dt_put['consciousness_two'];
        $respiration_zero = $dt_put['respiration_zero'];
        $respiration_one = $dt_put['respiration_one'];
        $respiration_two = $dt_put['respiration_two'];
        $motor_activities_zero = $dt_put['motor_activities_zero'];
        $motor_activities_one = $dt_put['motor_activities_one'];
        $motor_activities_two = $dt_put['motor_activities_two'];
        $total_score = $dt_put['total_score'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_steward = array(
            'consciousness_zero'    => $consciousness_zero,
            'consciousness_one'     => $consciousness_one,
            'consciousness_two'     => $consciousness_two,
            'respiration_zero'      => $respiration_zero,
            'respiration_one'       => $respiration_one,
            'respiration_two'       => $respiration_two,
            'motor_activities_zero' => $motor_activities_zero,
            'motor_activities_one'  => $motor_activities_one,
            'motor_activities_two'  => $motor_activities_two,
            'total_score'           => $total_score,
            'user_updated'          => $user_id,
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($steward_id, $dt_steward);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_steward,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update steward', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}