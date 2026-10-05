<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Bromage_score extends My_Controller {
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
    
        $bromage_id = $id;
    
        if ($bromage_id === 0 || $bromage_id === "0") {
            $this->REST_Return(400, 'bromage_id is required');
            return;
        }
    
        $output = $this->$model->read($bromage_id);
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

        $full_movement_check_in_rr = $dt_post['full_movement_check_in_rr'];
        $full_movement_hour = $dt_post['full_movement_hour'];
        $full_movement_change_time = $dt_post['full_movement_change_time'];
        $extend_legs_check_in_rr = $dt_post['extend_legs_check_in_rr'];
        $extend_legs_hour = $dt_post['extend_legs_hour'];
        $extend_legs_change_time = $dt_post['extend_legs_change_time'];
        $flex_knee_check_in_rr = $dt_post['flex_knee_check_in_rr'];
        $flex_knee_hour = $dt_post['flex_knee_hour'];
        $flex_knee_change_time = $dt_post['flex_knee_change_time'];
        $flex_ankle_check_in_rr = $dt_post['flex_ankle_check_in_rr'];
        $flex_ankle_hour = $dt_post['flex_ankle_hour'];
        $flex_ankle_change_time = $dt_post['flex_ankle_change_time'];
        $total_score = $dt_post['total_score'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_bromage = array(
            'operating_room_id'            => $operating_room_id,
            'full_movement_check_in_rr'    => $full_movement_check_in_rr,
            'full_movement_hour'           => $full_movement_hour,
            'full_movement_change_time'    => $full_movement_change_time,
            'extend_legs_check_in_rr'      => $extend_legs_check_in_rr,
            'extend_legs_hour'             => $extend_legs_hour,
            'extend_legs_change_time'      => $extend_legs_change_time,
            'flex_knee_check_in_rr'        => $flex_knee_check_in_rr,
            'flex_knee_hour'               => $flex_knee_hour,
            'flex_knee_change_time'        => $flex_knee_change_time,
            'flex_ankle_check_in_rr'       => $flex_ankle_check_in_rr,
            'flex_ankle_hour'              => $flex_ankle_hour,
            'flex_ankle_change_time'       => $flex_ankle_change_time,
            'total_score'                  => $total_score,
            'user_added'                   => $user_id,
            'date_added'                   => date('Y-m-d H:i:s',time()),
            'date_updated'                 => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_bromage);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_bromage,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create bromage_score', $response_data);
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
    
        $bromage_id = $id;
    
        if ($bromage_id === 0 || $bromage_id === "0") {
            $this->REST_Return(400, 'bromage_id is required');
            return;
        }

        $select = 'a.id';
        $bromage = $this->$model->select($bromage_id, $select);

        if (count($bromage) == 0) {
            $this->REST_Return(404, 'there is no bromage with id ' . $bromage_id);
            return;
        }

        $full_movement_check_in_rr = $dt_put['full_movement_check_in_rr'];
        $full_movement_hour = $dt_put['full_movement_hour'];
        $full_movement_change_time = $dt_put['full_movement_change_time'];
        $extend_legs_check_in_rr = $dt_put['extend_legs_check_in_rr'];
        $extend_legs_hour = $dt_put['extend_legs_hour'];
        $extend_legs_change_time = $dt_put['extend_legs_change_time'];
        $flex_knee_check_in_rr = $dt_put['flex_knee_check_in_rr'];
        $flex_knee_hour = $dt_put['flex_knee_hour'];
        $flex_knee_change_time = $dt_put['flex_knee_change_time'];
        $flex_ankle_check_in_rr = $dt_put['flex_ankle_check_in_rr'];
        $flex_ankle_hour = $dt_put['flex_ankle_hour'];
        $flex_ankle_change_time = $dt_put['flex_ankle_change_time'];
        $total_score = $dt_put['total_score'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_bromage = array(
            'full_movement_check_in_rr'    => $full_movement_check_in_rr,
            'full_movement_hour'           => $full_movement_hour,
            'full_movement_change_time'    => $full_movement_change_time,
            'extend_legs_check_in_rr'      => $extend_legs_check_in_rr,
            'extend_legs_hour'             => $extend_legs_hour,
            'extend_legs_change_time'      => $extend_legs_change_time,
            'flex_knee_check_in_rr'        => $flex_knee_check_in_rr,
            'flex_knee_hour'               => $flex_knee_hour,
            'flex_knee_change_time'        => $flex_knee_change_time,
            'flex_ankle_check_in_rr'       => $flex_ankle_check_in_rr,
            'flex_ankle_hour'              => $flex_ankle_hour,
            'flex_ankle_change_time'       => $flex_ankle_change_time,
            'total_score'                  => $total_score,
            'user_updated'                 => $user_id,
            'date_updated'                 => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($bromage_id, $dt_bromage);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_bromage,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update bromage', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}