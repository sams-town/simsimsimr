<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Catalog_instruction extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]'),
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]'),
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

        $limit = $q_strings['limit'] ?? 100;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 
        
        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }

        if(isset($q_strings['name'])) {
            $this->$model->whereLike['a.name'] = $q_strings['name'];
            unset($q_strings['name']);
        }

        $select = 'a.*';
        $where = $q_strings;
        $where['a.is_delete'] = 0;
        $join = array();
        $order = array(
            'a.name' => 'ASC',
        );
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select)[0]->iTotal;   

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

        $dt_catalog_instruction = array(
            'name' => $name,
        );

        $id = $this->$model->create($dt_catalog_instruction);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_catalog_instruction,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create catalog_instruction', $response_data);
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

        $catalog_instruction_id = $id;

        if ($catalog_instruction_id === 0 || $catalog_instruction_id === "0") {
            $this->REST_Return(400, 'catalog_instruction_id is required');
            return;
        }

        $select = 'a.id';
        $catalog_instruction = $this->$model->select($catalog_instruction_id, $select);
        if (count($catalog_instruction) == 0) {
            $this->REST_Return(404, 'there is no catalog_instruction with id ' . $catalog_instruction_id);
            return;
        }

        $name = $dt_put['name'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_catalog_instruction = array(
            'name' => $name,
        );

        $this->$model->update_v2($catalog_instruction_id, $dt_catalog_instruction);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_catalog_instruction,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update catalog_instruction', $response_data);
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
            $this->REST_Return(400, 'catalog_instruction_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $catalog_instruction_id = $this->$model->select_id($id);

        if ($catalog_instruction_id == "") {
            $this->REST_Return(404, 'there is no catalog_instruction with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_catalog_instruction = array(
            'is_delete' => $is_delete,
        );

        $this->$model->update_v2($catalog_instruction_id, $dt_catalog_instruction);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_catalog_instruction,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete catalog_instruction', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}