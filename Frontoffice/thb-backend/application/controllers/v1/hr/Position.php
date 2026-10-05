<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Position extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/Department_model", "Department");

        $this->create_rules = array(
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'department_id', 'label' => 'department_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
        );

        $this->update_rules = array(
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'department_id', 'label' => 'department_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
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

        $position_id = $id;

        // if ($position_id === 0 || $position_id === "0") {
        //     $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;
        //     if ($health_facility_item_id == null) {
        //         $this->REST_Return(400, 'health_facility_item_id is required if position_id is required');
        //         return;
        //     }
        // }

        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($position_id);
        $response_data = array();


        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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

        $select = 'a.*, hfi.health_facility_id, hfi.name as health_facility_item_name, d.name as department_name';
        $where = $q_strings;
        $where['a.is_delete'] = 0;
        $join = array(
		    array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
		    array('department d', 'a.department_id = d.id', 'LEFT'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;   

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
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        $health_facility_item_id = $dt_post['health_facility_item_id'];
        $department_id = $dt_post['department_id'];
        $name = $dt_post['name'];

        $select = 'a.id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $select = 'a.id';
        $department = $this->Department->select($department_id, $select);
        if (count($department) == 0) {
            $this->REST_Return(404, 'there is no department with id ' . $department_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_position = array(
            'institute_branch_id'     => $institute_branch_id,
            'health_facility_item_id' => $health_facility_item_id,
            'department_id'           => $department_id,
            'name'                    => $name,
        );

        $id = $this->$model->create($dt_position);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_position,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create position', $response_data);
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

        $position_id = $id;

        if ($position_id === 0 || $position_id === "0") {
            $this->REST_Return(400, 'position_id is required');
            return;
        }

        $health_facility_item_id = $dt_put['health_facility_item_id'];
        $department_id = $dt_put['department_id'];
        $name = $dt_put['name'];

        $select = 'a.id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $select = 'a.id';
        $department = $this->Department->select($department_id, $select);
        if (count($department) == 0) {
            $this->REST_Return(404, 'there is no department with id ' . $department_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_position = array(
            'health_facility_item_id' => $health_facility_item_id,
            'department_id' => $department_id,
            'name' => $name,
        );

        $this->$model->update_v2($position_id, $dt_position);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_position,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update position', $response_data);
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
            $this->REST_Return(400, 'position_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $position_id = $this->$model->select_id($id);

        if ($position_id == "") {
            $this->REST_Return(404, 'there is no position with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_position = array(
            'is_delete' => $is_delete,
        );

        $this->$model->update_v2($position_id, $dt_position);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_position,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete position', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}
