<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Template_anamnesis extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Setting_model", "Setting");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]')
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]')
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

        $select = 'a.id';
        $where = array('a.user_id' => $user_id);
        $setting = $this->Setting->select(0, $select, $where);

        $setting_id = $setting[0]->id;

        $select = 'a.*';
        $where = array(
            'a.setting_id' => $setting_id,
            'a.is_delete' => 0,
        );
        $output = $this->$model->select(0, $select, $where);
        
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

        $select = 'a.id';
        $where = array('a.user_id' => $user_id);
        $setting = $this->Setting->select(0, $select, $where);

        $setting_id = $setting[0]->id;

        $name = $dt_post['name'];
        $anamesa_type_id = $dt_post['anamesa_type_id'];
        $family_name = $dt_post['family_name'];
        $family_relation_id = $dt_post['family_relation_id'];
        $main_complaint = $dt_post['main_complaint'];
        $history_disease_now = $dt_post['history_disease_now'];
        $history_disease_past = $dt_post['history_disease_past'];
        $history_drugs = $dt_post['history_drugs'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_template_anamnesis = array(
            'setting_id'           => $setting_id,
            'name'                 => $name,
            'anamesa_type_id'      => $anamesa_type_id,
            'family_name'          => $family_name,
            'family_relation_id'   => $family_relation_id,
            'main_complaint'       => $main_complaint,
            'history_disease_now'  => $history_disease_now,
            'history_disease_past' => $history_disease_past,
            'history_drugs'        => $history_drugs,
            'date_added'           => date('Y-m-d H:i:s',time()),
            'date_updated'         => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_template_anamnesis);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_template_anamnesis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create template_anamnesis', $response_data);
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

        $template_anamnesis_id = $id;
    
        if ($template_anamnesis_id === 0 || $template_anamnesis_id === "0") {
            $this->REST_Return(400, 'template_anamnesis_id is required');
            return;
        }

        $select = 'a.id';
        $where = array(
            'a.is_delete' => 0,
        );
        $template_anamnesis = $this->$model->select($template_anamnesis_id, $select, $where);
        if (count($template_anamnesis) == 0) {
            $this->REST_Return(404, 'there is no template_anamnesis with id ' . $template_anamnesis_id);
            return;
        }

        $name = $dt_put['name'];
        $anamesa_type_id = $dt_put['anamesa_type_id'];
        $family_name = $dt_put['family_name'];
        $family_relation_id = $dt_put['family_relation_id'];
        $main_complaint = $dt_put['main_complaint'];
        $history_disease_now = $dt_put['history_disease_now'];
        $history_disease_past = $dt_put['history_disease_past'];
        $history_drugs = $dt_put['history_drugs'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_template_anamnesis = array(
            'name'                 => $name,
            'anamesa_type_id'      => $anamesa_type_id,
            'family_name'          => $family_name,
            'family_relation_id'   => $family_relation_id,
            'main_complaint'       => $main_complaint,
            'history_disease_now'  => $history_disease_now,
            'history_disease_past' => $history_disease_past,
            'history_drugs'        => $history_drugs,
        );
    
        $this->$model->update_v2($template_anamnesis_id, $dt_template_anamnesis);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_template_anamnesis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update template_anamnesis', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function delete_delete($p = 0) {
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
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'template_anamnesis_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $template_anamnesis_id = $this->$model->select_id($id);
    
        if ($template_anamnesis_id == "") {
            $this->REST_Return(404, 'there is no template_anamnesis with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_template_anamnesis = array(
            'date_updated' => date('Y-m-d H:i:s'),
            'is_delete' => $is_delete,
        );
    
        $this->$model->update_v2($template_anamnesis_id, $dt_template_anamnesis);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_template_anamnesis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete template_anamnesis', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}