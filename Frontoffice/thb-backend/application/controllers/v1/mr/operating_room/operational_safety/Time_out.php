<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Time_out extends My_Controller {
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
    
        $time_out_id = $id;
    
        if ($time_out_id === 0 || $time_out_id === "0") {
            $this->REST_Return(400, 'time_out_id is required');
            return;
        }
    
        $output = $this->$model->read($time_out_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->reading_verbally_arry = ($output[0]->reading_verbally_arry != "") ? explode(',', $output[0]->reading_verbally_arry) : [];
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

        $completeness_team_type_id = $dt_post['completeness_team_type_id'];
        $reading_verbally_arry = implode(',', $dt_post['reading_verbally_arry']);
        $premedication = $dt_post['premedication'];
        $given_hours = $dt_post['given_hours'];
        $prohilaxix = $dt_post['prohilaxix'];
        $prohilaxix_hours = $dt_post['prohilaxix_hours'];
        $rechecking_sterilization = $dt_post['rechecking_sterilization'];
        $radiology_photo_type_id = $dt_post['radiology_photo_type_id'];
        $checking = $dt_post['checking'];
        $things_to_note = $dt_post['things_to_note'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_time_out = array(
            'operating_room_id'            => $operating_room_id,
            'completeness_team_type_id'    => $completeness_team_type_id,
            'reading_verbally_arry'        => $reading_verbally_arry,
            'premedication'                => $premedication,
            'given_hours'                  => $given_hours,
            'prohilaxix'                   => $prohilaxix,
            'prohilaxix_hours'             => $prohilaxix_hours,
            'rechecking_sterilization'     => $rechecking_sterilization,
            'radiology_photo_type_id'      => $radiology_photo_type_id,
            'checking'                     => $checking,
            'things_to_note'               => $things_to_note,
            'user_added'                   => $user_id,
            'date_added'                   => date('Y-m-d H:i:s',time()),
            'date_updated'                 => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_time_out);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_time_out,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create time_out', $response_data);
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
    
        $time_out_id = $id;
    
        if ($time_out_id === 0 || $time_out_id === "0") {
            $this->REST_Return(400, 'time_out_id is required');
            return;
        }

        $select = 'a.id';
        $time_out = $this->$model->select($time_out_id, $select);

        if (count($time_out) == 0) {
            $this->REST_Return(404, 'there is no time_out with id ' . $time_out_id);
            return;
        }

        $completeness_team_type_id = $dt_put['completeness_team_type_id'];
        $reading_verbally_arry = implode(',', $dt_put['reading_verbally_arry']);
        $premedication = $dt_put['premedication'];
        $given_hours = $dt_put['given_hours'];
        $prohilaxix = $dt_put['prohilaxix'];
        $prohilaxix_hours = $dt_put['prohilaxix_hours'];
        $rechecking_sterilization = $dt_put['rechecking_sterilization'];
        $radiology_photo_type_id = $dt_put['radiology_photo_type_id'];
        $checking = $dt_put['checking'];
        $things_to_note = $dt_put['things_to_note'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_time_out = array(
            'completeness_team_type_id'    => $completeness_team_type_id,
            'reading_verbally_arry'        => $reading_verbally_arry,
            'premedication'                => $premedication,
            'given_hours'                  => $given_hours,
            'prohilaxix'                   => $prohilaxix,
            'prohilaxix_hours'             => $prohilaxix_hours,
            'rechecking_sterilization'     => $rechecking_sterilization,
            'radiology_photo_type_id'      => $radiology_photo_type_id,
            'checking'                     => $checking,
            'things_to_note'               => $things_to_note,
            'user_updated'                 => $user_id,
            'date_updated'                 => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($time_out_id, $dt_time_out);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_time_out,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update time_out', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}