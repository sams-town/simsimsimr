<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Surgery_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Icd10_model", "Icd10");
        $this->load->model("v1/mr/Icd9cm_model", "Icd9cm");
        $this->load->model("v1/mr/Soap_model", "Soap");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }
    
    /**
     * Surgery_request API
     * --------------------
     * @param: soap_id
     * @param: surgery_plan
     * @param: surgery_type_id
     * @param: infectious_type_id
     * @param: priority_type_id
     * @param: operator_id
     * @param: icd10_id
     * @param: icd9cm_id
     * @param: accommodation_request
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/health_facility/surgery_request
     **/
    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('surgery_plan', 'surgery_plan', 'trim|max_length[100]');
            $this->form_validation->set_rules('surgery_type_id', 'surgery_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('infectious_type_id', 'infectious_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('priority_type_id', 'priority_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('operator_id', 'operator_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('icd10_id', 'icd10_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('icd9cm_id', 'icd9cm_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('accommodation_request', 'accommodation_request', 'trim');
            $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Count no_request
                $this->$model->whereFields['a.is_delete'] = 0;
                $this->$model->orderFields = array('a.no_request' => 'DESC');
                $this->$model->limit = 1;
                $tmp = $this->$model->read(0,false);
                $waktu = date("Ymd",time());
                if($tmp != []) {
                    $no_request = 'SRG' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                    // Check if reset is needed
                    if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        $no_request = 'SRG' . $waktu . '0001';
                    }
                } else {
                    $no_request = 'SRG' . $waktu . '0001';
                }

                // Count queue
                $this->$model->whereFields['a.is_delete'] = 0;
                $this->$model->orderFields = array('a.queue' => 'DESC');
                $this->$model->limit = 1;
                $tmp = $this->$model->read(0,false);
                if($tmp != []) {
                    $queue = $tmp[0]->queue + 1;
                    // Check if reset is needed
                    if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        $queue = 1;
                    }
                } else {
                    $queue = 1;
                }

                // Check soap_id & operator_id & icd10_id & icd9cm_id
                if($this->Soap->read($dt_post['soap_id'],false) != [] && $this->Employee->read($dt_post['operator_id'],false) != [] && $this->Icd10->read($dt_post['icd10_id'],false) != [] && $this->Icd9cm->read($dt_post['icd9cm_id'],false) != []) {
                    $dt_surgery_request = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'no_request'            => $no_request,
                        'soap_id'               => $dt_post['soap_id'],
                        'surgery_plan'          => isset($dt_post['surgery_plan']) ? $dt_post['surgery_plan'] : '',
                        'surgery_type_id'       => isset($dt_post['surgery_type_id']) ? $dt_post['surgery_type_id'] : '',
                        'infectious_type_id'    => isset($dt_post['infectious_type_id']) ? $dt_post['infectious_type_id'] : '',
                        'priority_type_id'      => isset($dt_post['priority_type_id']) ? $dt_post['priority_type_id'] : '',
                        'operator_id'           => $dt_post['operator_id'],
                        'icd10_id'              => $dt_post['icd10_id'],
                        'icd9cm_id'             => $dt_post['icd9cm_id'],
                        'accommodation_request' => isset($dt_post['accommodation_request']) ? $dt_post['accommodation_request'] : '',
                        'date_request'          => $dt_post['date_request'],
                        'queue'                 => $queue,
                    );
                    $id = $this->$model->create($dt_surgery_request);
                } else {
                    $this->REST_Return(404, 'Wrong soap_id or operator_id or icd10_id or icd9cm_id!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $id
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                }
                return;
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function detail_get($p = 0){
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
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
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

    /**
     * Surgery_request API
     * --------------------
     * @param: soap_id
     * @param: surgery_plan
     * @param: surgery_type_id
     * @param: infectious_type_id
     * @param: priority_type_id
     * @param: operator_id
     * @param: icd10_id
     * @param: icd9cm_id
     * @param: accommodation_request
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/mr/surgery_request
     **/

     public function update_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('surgery_plan', 'surgery_plan', 'trim|max_length[100]');
            $this->form_validation->set_rules('surgery_type_id', 'surgery_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('infectious_type_id', 'infectious_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('priority_type_id', 'priority_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('operator_id', 'operator_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('icd10_id', 'icd10_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('icd9cm_id', 'icd9cm_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('accommodation_request', 'accommodation_request', 'trim');
            $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->$model->read($id) != []) {
                    // Check soap_id & operator_id & icd10_id & icd9cm_id
                    if($this->Soap->read($dt_put['soap_id'],false) != [] && $this->Employee->read($dt_put['operator_id'],false) != [] && $this->Icd10->read($dt_put['icd10_id'],false) != [] && $this->Icd9cm->read($dt_put['icd9cm_id'],false) != []) {
                        $dt_surgery_request = array(
                            'id'                        => $id,
                            'soap_id'                   => $dt_put['soap_id'],
                            'surgery_plan'              => $dt_put['surgery_plan'],
                            'surgery_type_id'           => $dt_put['surgery_type_id'],
                            'infectious_type_id'        => $dt_put['infectious_type_id'],
                            'priority_type_id'          => $dt_put['priority_type_id'],
                            'operator_id'               => $dt_put['operator_id'],
                            'icd10_id'                  => $dt_put['icd10_id'],
                            'icd9cm_id'                 => $dt_put['icd9cm_id'],
                            'accommodation_request'     => $dt_put['accommodation_request'],
                            'date_request'              => $dt_put['date_request'],
                        );
                        $this->$model->update($id, $dt_surgery_request);
                    } else {
                        $this->REST_Return(404, 'Wrong soap_id or operator_id or icd10_id or icd9cm_id!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Surgery_request with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}