<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Signatura_times extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/setting/Package_model", "Package");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    
    public function list_get($p = 0){
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

        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 
        
        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }
        
        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($id);

        usort($output, function ($a, $b) {
            return $a->name <=> $b->name; // ascending order
        });

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $where = array(
            'a.is_delete' => 0
        );
        $iTotal = $this->$model->select(0, $select, $where)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
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

        $name = $dt_post['name'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_signatura_times = array(
            'name'          => $name,
            'user_added'    => $user_id,
            'date_added'    => date('Y-m-d H:i:s',time()),
            'date_updated'  => date('Y-m-d H:i:s',time()),
        );

        $id = $this->$model->create($dt_signatura_times);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_signatura_times,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create signatura_times', $response_data);
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

        $signatura_times_id = $id;

        if ($signatura_times_id === 0 || $signatura_times_id === "0") {
            $this->REST_Return(400, 'signatura_times_id is required');
            return;
        }

        $select = 'a.id';
        $signatura_times = $this->$model->select($signatura_times_id, $select);
        if (count($signatura_times) == 0) {
            $this->REST_Return(404, 'there is no signature_dose with id ' . $signatura_times_id);
            return;
        }

        $name = $dt_put['name'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_signatura_times = array(
            'name'       => $name,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $this->$model->update_v2($signatura_times_id, $dt_signatura_times);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_signatura_times,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update signatura_times', $response_data);
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
            $this->REST_Return(400, 'signature_dose_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $signature_dose_id = $this->$model->select_id($id);

        if ($signature_dose_id == "") {
            $this->REST_Return(404, 'there is no signature_dose with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_signature_dose = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
            'is_delete' => $is_delete,
        );

        $this->$model->update_v2($signature_dose_id, $dt_signature_dose);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_signature_dose,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete signature_dose', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}