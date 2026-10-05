<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Setting extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");

        $this->update_rules = array(
            array('field' => 'always_copy_nurse', 'label' => 'always_copy_nurse', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'anamnesis', 'label' => 'anamnesis', 'rules' => 'trim|required|max_length[1]'),
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
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $select = 'a.*';
        $where = array(
            'a.user_id' => $user_id
        );
        $output = $this->$model->select(0, $select, $where);

        if (count($output) == 0) {
            $dt_setting = array(
                'user_id' => $user_id
            );
            $this->$model->create($dt_setting);
            $output = $this->$model->select(0, $select, $where);
        }

        $response_data = $output;
    
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
    
        $select = 'a.id';
        $where = array(
            'a.user_id' => $user_id
        );
        $setting = $this->$model->select(0, $select, $where);
        
        $setting_id = $setting[0]->id;

        /* Start Transaction */
        $this->db->trans_start();

        $always_copy_nurse = $dt_put['always_copy_nurse'];
        $anamnesis = $dt_put['anamnesis'];
    
        $dt_setting = array(
            'always_copy_nurse' => $always_copy_nurse,
            'anamnesis' => $anamnesis,
        );
    
        $this->$model->update($setting_id, $dt_setting);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_setting,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update setting', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $setting_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}