<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Postoperative_nursing extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
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
    
        $postoperative_nursing_id = $id;
    
        if ($postoperative_nursing_id === 0 || $postoperative_nursing_id === "0") {
            $this->REST_Return(400, 'postoperative_nursing_id is required');
            return;
        }
    
        $output = $this->$model->read($postoperative_nursing_id);
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

        $postoperative_care_type_id = $dt_post['postoperative_care_type_id'];
        $transport_type_id = $dt_post['transport_type_id'];
        $general_condition_type_id = $dt_post['general_condition_type_id'];
        $gcs_type_id = $dt_post['gcs_type_id'];
        $gcs_e = $dt_post['gcs_e'];
        $gcs_m = $dt_post['gcs_m'];
        $gcs_v = $dt_post['gcs_v'];
        $airway_type_id = $dt_post['airway_type_id'];
        $airway_problem = $dt_post['airway_problem'];
        $breathing_type_id = $dt_post['breathing_type_id'];
        $oxygen_therapy_type_id = $dt_post['oxygen_therapy_type_id'];
        $oxygen_therapy_other = $dt_post['oxygen_therapy_other'];
        $skin_arrive_type_id = $dt_post['skin_arrive_type_id'];
        $skin_arrive_other = $dt_post['skin_arrive_other'];
        $skin_exit_type_id = $dt_post['skin_exit_type_id'];
        $skin_exit_other = $dt_post['skin_exit_other'];
        $circulation_type_id = $dt_post['circulation_type_id'];
        $position_type_id = $dt_post['position_type_id'];
        $position_other = $dt_post['position_other'];
        $pupil_type_id = $dt_post['pupil_type_id'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_postoperative_nursing = array(
            'operating_room_id'             => $operating_room_id,
            'postoperative_care_type_id'    => $postoperative_care_type_id,
            'transport_type_id'             => $transport_type_id,
            'general_condition_type_id'     => $general_condition_type_id,
            'gcs_type_id'                   => $gcs_type_id,
            'gcs_e'                         => $gcs_e,
            'gcs_m'                         => $gcs_m,
            'gcs_v'                         => $gcs_v,
            'airway_type_id'                => $airway_type_id,
            'airway_problem'                => $airway_problem,
            'breathing_type_id'             => $breathing_type_id,
            'oxygen_therapy_type_id'        => $oxygen_therapy_type_id,
            'oxygen_therapy_other'          => $oxygen_therapy_other,
            'skin_arrive_type_id'           => $skin_arrive_type_id,
            'skin_arrive_other'             => $skin_arrive_other,
            'skin_exit_type_id'             => $skin_exit_type_id,
            'skin_exit_other'               => $skin_exit_other,
            'circulation_type_id'           => $circulation_type_id,
            'position_type_id'              => $position_type_id,
            'position_other'                => $position_other,
            'pupil_type_id'                 => $pupil_type_id,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
        
        $id = $this->$model->create($dt_postoperative_nursing);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_postoperative_nursing,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create postoperative_nursing', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }
}