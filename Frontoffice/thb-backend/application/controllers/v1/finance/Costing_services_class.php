<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Costing_services_class extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Costing_model", "Costing");
        $this->load->model("v1/setting/Service_class_model", "Service_class");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Costing Services Class API
     * --------------------
     * @param: costing_id
     * @param: service_class_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: var_4
     * @param: var_5
     * @param: var_6
     * @param: var_7
     * @param: var_8
     * --------------------
     * @method : POST
     * @link: version/finance/costing_services_class
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
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('service_class_id', 'service_class_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_4', 'var_4', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_5', 'var_5', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_6', 'var_6', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_7', 'var_7', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_8', 'var_8', 'trim|max_length[8]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check costing_id service_class_id
                if($this->Service_class->read($dt_post['service_class_id'], false) != [] && $this->Costing->read($dt_post['costing_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_costing_services_class = array(
                        'costing_id'        => $dt_post['costing_id'],
                        'service_class_id'  => $dt_post['service_class_id'],
                        'var_1'             => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                        'var_2'             => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                        'var_3'             => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                        'var_4'             => isset($dt_post['var_4']) ? $dt_post['var_4'] : '',
                        'var_5'             => isset($dt_post['var_5']) ? $dt_post['var_5'] : '',
                        'var_6'             => isset($dt_post['var_6']) ? $dt_post['var_6'] : '',
                        'var_7'             => isset($dt_post['var_7']) ? $dt_post['var_7'] : '',
                        'var_8'             => isset($dt_post['var_8']) ? $dt_post['var_8'] : ''
                    );
                    $id = $this->$model->replace($dt_costing_services_class);
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
                } else {
                    $this->REST_Return(404, 'Wrong costing_id or service_class_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Costing Services Class with API
     * -------------------------
     * @method: GET
     */

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
     * Costing Services Class API
     * --------------------
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: var_4
     * @param: var_5
     * @param: var_6
     * @param: var_7
     * @param: var_8
     * --------------------
     * @method : POST
     * @link: version/finance/costing_services_class
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
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_4', 'var_4', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_5', 'var_5', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_6', 'var_6', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_7', 'var_7', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_8', 'var_8', 'trim|max_length[8]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $costing_services_class = $this->$model->read($id); 
                if($costing_services_class != []) {
                    $dt_costing_services_class = array(
                        'costing_id'        => $id,
                        'var_1'             => isset($dt_put['var_1']) ? $dt_put['var_1'] : $costing_services_class[0]->var_1,
                        'var_2'             => isset($dt_put['var_2']) ? $dt_put['var_2'] : $costing_services_class[0]->var_2,
                        'var_3'             => isset($dt_put['var_3']) ? $dt_put['var_3'] : $costing_services_class[0]->var_3,
                        'var_4'             => isset($dt_put['var_4']) ? $dt_put['var_4'] : $costing_services_class[0]->var_4,
                        'var_5'             => isset($dt_put['var_5']) ? $dt_put['var_5'] : $costing_services_class[0]->var_5,
                        'var_6'             => isset($dt_put['var_6']) ? $dt_put['var_6'] : $costing_services_class[0]->var_6,
                        'var_7'             => isset($dt_put['var_7']) ? $dt_put['var_7'] : $costing_services_class[0]->var_7,
                        'var_8'             => isset($dt_put['var_8']) ? $dt_put['var_8'] : $costing_services_class[0]->var_8
                    );
                    $id = $this->$model->update($id, $dt_costing_services_class);
                } else {
                    $this->REST_Return(404, 'Costing Service Class with id ' . $id . ' does not exist!');
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