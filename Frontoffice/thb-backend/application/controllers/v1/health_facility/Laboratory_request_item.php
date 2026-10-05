<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Laboratory_request_item extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/Laboratory_request_read_model", "Laboratory_request_read");
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        // Load Helper
        $this->load->helper('utils');

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

        $laboratory_request_item_id = $id;
        
        $where = $q_strings;

        if ($laboratory_request_item_id === 0 || $laboratory_request_item_id === "0") {
            $registration_id = $q_strings['registration_id'] ?? null; 
            if ($registration_id == null) {
                $this->REST_Return(400, 'registration_id is required if laboratory_request_item_id is not provided');
                return;
            }
            $where['rd.registration_id'] = $registration_id;
            unset($where['registration_id']);
        }

        $select = 'a.*, lr.date_request as date_added, lp.code_lis, hfi.name as health_facility_item_name, csc.service_class_id, p.name as patient_name, h.name as alias_name';
        $where['lr.is_delete'] = 0;
        $where['a.is_delete'] = 0;
        $this->$model->whereInFields['lr.status'] = [2,3];
        $join = array(
            array('laboratory_request lr', 'a.laboratory_request_id = lr.id', 'INNER'),
            array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
            array('costing_services_class csc', 'lp.costing_id = csc.costing_id', 'INNER'),
            array('soap s', 'lr.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
        );
        if ($laboratory_request_item_id === 0 || $laboratory_request_item_id === "0") {
            $output = $this->$model->select(0, $select, $where, $join);
        } else {
            $output = $this->$model->select($laboratory_request_item_id, $select, $where, $join);
        }

        for ($i=0; $i < count($output); $i++) { 
            $laboratory_request_item_id = $output[$i]->id;
            $laboratory_request_id = $output[$i]->laboratory_request_id;

            // LIS RESULT
            $lis_response = bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

            $result = [];
            if (count($lis_response) > 0) {
                $result = $lis_response[0]['result_detail'];
            }

            $output[$i]->result = $result;
        }

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Laboratory_request_item with API
     * -------------------------
     * @method: GET
     */

     public function detail_backup_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $registration_id = '';
            if(isset($q_strings['registration_id'])){
                $registration_id = $q_strings['registration_id']; 
                unset($q_strings['registration_id']);
            }
            $this->$model->whereFields = $q_strings;
            if($registration_id != ''){
                $this->$model->whereFields['c.registration_id'] = $registration_id; 
            };
            $this->$model->whereFields['h.is_delete'] = '0';
            $this->$model->whereFields['a.is_delete'] = '0';
            $this->$model->whereInFields['h.status'] = [2,3];
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = $output;
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function list_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function delete_delete($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = "Laboratory_request_read";
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

        $laboratory_request_item_id = $this->$model->select_id($id);
        
        if ($laboratory_request_item_id == "") {
            $this->REST_Return(404, 'there is no laboratory_request_item_id with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_laboratory_request_item = array(
            'is_delete' => $is_delete,
        );

        $id = $this->$model->update($laboratory_request_item_id, $dt_laboratory_request_item);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_laboratory_request_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update laboratory_request_item_image', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}