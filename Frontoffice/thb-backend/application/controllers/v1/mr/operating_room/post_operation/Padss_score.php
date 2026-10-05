<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Padss_score extends My_Controller {
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
    
        $padss_id = $id;
    
        if ($padss_id === 0 || $padss_id === "0") {
            $this->REST_Return(400, 'padss_id is required');
            return;
        }
    
        $output = $this->$model->read($padss_id);
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

        $blood_pressure_zero = $dt_post['blood_pressure_zero'];
        $blood_pressure_zero_post_time = $dt_post['blood_pressure_zero_post_time'];
        $blood_pressure_zero_change_time = $dt_post['blood_pressure_zero_change_time'];
        $blood_pressure_one = $dt_post['blood_pressure_one'];
        $blood_pressure_one_post_time = $dt_post['blood_pressure_one_post_time'];
        $blood_pressure_one_change_time = $dt_post['blood_pressure_one_change_time'];
        $blood_pressure_two = $dt_post['blood_pressure_two'];
        $blood_pressure_two_post_time = $dt_post['blood_pressure_two_post_time'];
        $blood_pressure_two_change_time = $dt_post['blood_pressure_two_change_time'];
        $activities_zero = $dt_post['activities_zero'];
        $activities_zero_post_time = $dt_post['activities_zero_post_time'];
        $activities_zero_change_time = $dt_post['activities_zero_change_time'];
        $activities_one = $dt_post['activities_one'];
        $activities_one_post_time = $dt_post['activities_one_post_time'];
        $activities_one_change_time = $dt_post['activities_one_change_time'];
        $activities_two = $dt_post['activities_two'];
        $activities_two_post_time = $dt_post['activities_two_post_time'];
        $activities_two_change_time = $dt_post['activities_two_change_time'];
        $nausea_vomiting_zero = $dt_post['nausea_vomiting_zero'];
        $nausea_vomiting_zero_post_time = $dt_post['nausea_vomiting_zero_post_time'];
        $nausea_vomiting_zero_change_time = $dt_post['nausea_vomiting_zero_change_time'];
        $nausea_vomiting_one = $dt_post['nausea_vomiting_one'];
        $nausea_vomiting_one_post_time = $dt_post['nausea_vomiting_one_post_time'];
        $nausea_vomiting_one_change_time = $dt_post['nausea_vomiting_one_change_time'];
        $nausea_vomiting_two = $dt_post['nausea_vomiting_two'];
        $nausea_vomiting_two_post_time = $dt_post['nausea_vomiting_two_post_time'];
        $nausea_vomiting_two_change_time = $dt_post['nausea_vomiting_two_change_time'];
        $painful_one = $dt_post['painful_one'];
        $painful_one_post_time = $dt_post['painful_one_post_time'];
        $painful_one_change_time = $dt_post['painful_one_change_time'];
        $painful_two = $dt_post['painful_two'];
        $painful_two_post_time = $dt_post['painful_two_post_time'];
        $painful_two_change_time = $dt_post['painful_two_change_time'];
        $bleeding_zero = $dt_post['bleeding_zero'];
        $bleeding_zero_post_time = $dt_post['bleeding_zero_post_time'];
        $bleeding_zero_change_time = $dt_post['bleeding_zero_change_time'];
        $bleeding_one = $dt_post['bleeding_one'];
        $bleeding_one_post_time = $dt_post['bleeding_one_post_time'];
        $bleeding_one_change_time = $dt_post['bleeding_one_change_time'];
        $bleeding_two = $dt_post['bleeding_two'];
        $bleeding_two_post_time = $dt_post['bleeding_two_post_time'];
        $bleeding_two_change_time = $dt_post['bleeding_two_change_time'];
        $total_score = $dt_post['total_score'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_padss  = array(
            'operating_room_id'                 => $operating_room_id,
            'blood_pressure_zero'               => $blood_pressure_zero,
            'blood_pressure_zero_post_time'     => $blood_pressure_zero_post_time,
            'blood_pressure_zero_change_time'   => $blood_pressure_zero_change_time,
            'blood_pressure_one'                => $blood_pressure_one,
            'blood_pressure_one_post_time'      => $blood_pressure_one_post_time,
            'blood_pressure_one_change_time'    => $blood_pressure_one_change_time,
            'blood_pressure_two'                => $blood_pressure_two,
            'blood_pressure_two_post_time'      => $blood_pressure_two_post_time,
            'blood_pressure_two_change_time'    => $blood_pressure_two_change_time,
            'activities_zero'                   => $activities_zero,
            'activities_zero_post_time'         => $activities_zero_post_time,
            'activities_zero_change_time'       => $activities_zero_change_time,
            'activities_one'                    => $activities_one,
            'activities_one_post_time'          => $activities_one_post_time,
            'activities_one_change_time'        => $activities_one_change_time,
            'activities_two'                    => $activities_two,
            'activities_two_post_time'          => $activities_two_post_time,
            'activities_two_change_time'        => $activities_two_change_time,
            'nausea_vomiting_zero'              => $nausea_vomiting_zero,
            'nausea_vomiting_zero_post_time'    => $nausea_vomiting_zero_post_time,
            'nausea_vomiting_zero_change_time'  => $nausea_vomiting_zero_change_time,
            'nausea_vomiting_one'               => $nausea_vomiting_one,
            'nausea_vomiting_one_post_time'     => $nausea_vomiting_one_post_time,
            'nausea_vomiting_one_change_time'   => $nausea_vomiting_one_change_time,
            'nausea_vomiting_two'               => $nausea_vomiting_two,
            'nausea_vomiting_two_post_time'     => $nausea_vomiting_two_post_time,
            'nausea_vomiting_two_change_time'   => $nausea_vomiting_two_change_time,
            'painful_one'                       => $painful_one,
            'painful_one_post_time'             => $painful_one_post_time,
            'painful_one_change_time'           => $painful_one_change_time,
            'painful_two'                       => $painful_two,
            'painful_two_post_time'             => $painful_two_post_time,
            'painful_two_change_time'           => $painful_two_change_time,
            'bleeding_zero'                     => $bleeding_zero,
            'bleeding_zero_post_time'           => $bleeding_zero_post_time,
            'bleeding_zero_change_time'         => $bleeding_zero_change_time,
            'bleeding_one'                      => $bleeding_one,
            'bleeding_one_post_time'            => $bleeding_one_post_time,
            'bleeding_one_change_time'          => $bleeding_one_change_time,
            'bleeding_two'                      => $bleeding_two,
            'bleeding_two_post_time'            => $bleeding_two_post_time,
            'bleeding_two_change_time'          => $bleeding_two_change_time,
            'total_score'                       => $total_score,
            'user_added'                        => $user_id,
            'date_added'                        => date('Y-m-d H:i:s',time()),
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_padss);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_padss ,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create padss_score', $response_data);
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
    
        $padss_id = $id;
    
        if ($padss_id === 0 || $padss_id === "0") {
            $this->REST_Return(400, 'padss_id is required');
            return;
        }

        $select = 'a.id';
        $padss = $this->$model->select($padss_id, $select);

        if (count($padss) == 0) {
            $this->REST_Return(404, 'there is no padss with id ' . $padss_id);
            return;
        }

        $blood_pressure_zero = $dt_put['blood_pressure_zero'];
        $blood_pressure_zero_post_time = $dt_put['blood_pressure_zero_post_time'];
        $blood_pressure_zero_change_time = $dt_put['blood_pressure_zero_change_time'];
        $blood_pressure_one = $dt_put['blood_pressure_one'];
        $blood_pressure_one_post_time = $dt_put['blood_pressure_one_post_time'];
        $blood_pressure_one_change_time = $dt_put['blood_pressure_one_change_time'];
        $blood_pressure_two = $dt_put['blood_pressure_two'];
        $blood_pressure_two_post_time = $dt_put['blood_pressure_two_post_time'];
        $blood_pressure_two_change_time = $dt_put['blood_pressure_two_change_time'];
        $activities_zero = $dt_put['activities_zero'];
        $activities_zero_post_time = $dt_put['activities_zero_post_time'];
        $activities_zero_change_time = $dt_put['activities_zero_change_time'];
        $activities_one = $dt_put['activities_one'];
        $activities_one_post_time = $dt_put['activities_one_post_time'];
        $activities_one_change_time = $dt_put['activities_one_change_time'];
        $activities_two = $dt_put['activities_two'];
        $activities_two_post_time = $dt_put['activities_two_post_time'];
        $activities_two_change_time = $dt_put['activities_two_change_time'];
        $nausea_vomiting_zero = $dt_put['nausea_vomiting_zero'];
        $nausea_vomiting_zero_post_time = $dt_put['nausea_vomiting_zero_post_time'];
        $nausea_vomiting_zero_change_time = $dt_put['nausea_vomiting_zero_change_time'];
        $nausea_vomiting_one = $dt_put['nausea_vomiting_one'];
        $nausea_vomiting_one_post_time = $dt_put['nausea_vomiting_one_post_time'];
        $nausea_vomiting_one_change_time = $dt_put['nausea_vomiting_one_change_time'];
        $nausea_vomiting_two = $dt_put['nausea_vomiting_two'];
        $nausea_vomiting_two_post_time = $dt_put['nausea_vomiting_two_post_time'];
        $nausea_vomiting_two_change_time = $dt_put['nausea_vomiting_two_change_time'];
        $painful_one = $dt_put['painful_one'];
        $painful_one_post_time = $dt_put['painful_one_post_time'];
        $painful_one_change_time = $dt_put['painful_one_change_time'];
        $painful_two = $dt_put['painful_two'];
        $painful_two_post_time = $dt_put['painful_two_post_time'];
        $painful_two_change_time = $dt_put['painful_two_change_time'];
        $bleeding_zero = $dt_put['bleeding_zero'];
        $bleeding_zero_post_time = $dt_put['bleeding_zero_post_time'];
        $bleeding_zero_change_time = $dt_put['bleeding_zero_change_time'];
        $bleeding_one = $dt_put['bleeding_one'];
        $bleeding_one_post_time = $dt_put['bleeding_one_post_time'];
        $bleeding_one_change_time = $dt_put['bleeding_one_change_time'];
        $bleeding_two = $dt_put['bleeding_two'];
        $bleeding_two_post_time = $dt_put['bleeding_two_post_time'];
        $bleeding_two_change_time = $dt_put['bleeding_two_change_time'];
        $total_score = $dt_put['total_score'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_padss = array(
            'blood_pressure_zero'               => $blood_pressure_zero,
            'blood_pressure_zero_post_time'     => $blood_pressure_zero_post_time,
            'blood_pressure_zero_change_time'   => $blood_pressure_zero_change_time,
            'blood_pressure_one'                => $blood_pressure_one,
            'blood_pressure_one_post_time'      => $blood_pressure_one_post_time,
            'blood_pressure_one_change_time'    => $blood_pressure_one_change_time,
            'blood_pressure_two'                => $blood_pressure_two,
            'blood_pressure_two_post_time'      => $blood_pressure_two_post_time,
            'blood_pressure_two_change_time'    => $blood_pressure_two_change_time,
            'activities_zero'                   => $activities_zero,
            'activities_zero_post_time'         => $activities_zero_post_time,
            'activities_zero_change_time'       => $activities_zero_change_time,
            'activities_one'                    => $activities_one,
            'activities_one_post_time'          => $activities_one_post_time,
            'activities_one_change_time'        => $activities_one_change_time,
            'activities_two'                    => $activities_two,
            'activities_two_post_time'          => $activities_two_post_time,
            'activities_two_change_time'        => $activities_two_change_time,
            'nausea_vomiting_zero'              => $nausea_vomiting_zero,
            'nausea_vomiting_zero_post_time'    => $nausea_vomiting_zero_post_time,
            'nausea_vomiting_zero_change_time'  => $nausea_vomiting_zero_change_time,
            'nausea_vomiting_one'               => $nausea_vomiting_one,
            'nausea_vomiting_one_post_time'     => $nausea_vomiting_one_post_time,
            'nausea_vomiting_one_change_time'   => $nausea_vomiting_one_change_time,
            'nausea_vomiting_two'               => $nausea_vomiting_two,
            'nausea_vomiting_two_post_time'     => $nausea_vomiting_two_post_time,
            'nausea_vomiting_two_change_time'   => $nausea_vomiting_two_change_time,
            'painful_one'                       => $painful_one,
            'painful_one_post_time'             => $painful_one_post_time,
            'painful_one_change_time'           => $painful_one_change_time,
            'painful_two'                       => $painful_two,
            'painful_two_post_time'             => $painful_two_post_time,
            'painful_two_change_time'           => $painful_two_change_time,
            'bleeding_zero'                     => $bleeding_zero,
            'bleeding_zero_post_time'           => $bleeding_zero_post_time,
            'bleeding_zero_change_time'         => $bleeding_zero_change_time,
            'bleeding_one'                      => $bleeding_one,
            'bleeding_one_post_time'            => $bleeding_one_post_time,
            'bleeding_one_change_time'          => $bleeding_one_change_time,
            'bleeding_two'                      => $bleeding_two,
            'bleeding_two_post_time'            => $bleeding_two_post_time,
            'bleeding_two_change_time'          => $bleeding_two_change_time,
            'total_score'                       => $total_score,
            'user_updated'                      => $user_id,
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($padss_id, $dt_padss);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_padss,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update padss', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}