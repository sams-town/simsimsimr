<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Transaction_nurse extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Transaction_nurse API
     * --------------------
     * @param: nurse_id
     * @param: costing_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: notes
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_nurse
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
            $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('user_nurse_id', 'user_nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|required|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|required|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|required|max_length[8]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check registration_id and costing_id
                $dt_costing = $this->Costing_services_class->read($dt_post['costing_id'], false);
                if($this->Nurse->read($dt_post['nurse_id'], false) != [] && $dt_costing != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $t = time();
                    $dt_transaction_nurse = array(
                        'nurse_id'              => $dt_post['nurse_id'],
                        'user_nurse_id'         => $dt_post['user_nurse_id'],
                        'costing_id'            => $dt_costing[0]->costing_id,
                        'costing_name'          => $dt_costing[0]->name,
                        'var_1'                 => $dt_post['var_1'],
                        'var_2'                 => $dt_post['var_2'],
                        'var_3'                 => $dt_post['var_2']*$dt_post['var_1'],
                        'notes'                 => isset($dt_post['notes']) ? $dt_post['notes'] : '',
                        'date_added'            => isset($dt_post['date_added']) ? $dt_post['date_added'] : date("Y-m-d H:i:s",$t)
                    );
                    $id = $this->$model->create($dt_transaction_nurse);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(200, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong nurse_id or costing_id!');
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
     * GET Transaction_nurse with API
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
            $registration_id = '';
            if(isset($q_strings['registration_id'])){
                $registration_id = $q_strings['registration_id']; 
                unset($q_strings['registration_id']);
            }
            $this->$model->whereFields = $q_strings;
            if($registration_id != ''){
                $this->$model->whereFields['c.registration_id'] = $registration_id; 
            };
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
            
            // filter by param nurse_id
            if(isset($q_strings['nurse_id'])) $this->$model->whereFields['a.nurse_id'] = $q_strings['nurse_id']; unset($q_strings['nurse_id']);
            
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
     * Transaction_nurse API
     * --------------------
     * @param: costing_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: notes
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_nurse
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
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('user_nurse_id', 'user_nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_costing = $this->Costing_services_class->read($dt_put['costing_id'],false);
                if($dt_costing != []) {
                    $tr_nurse = $this->$model->read($id);
                    if($tr_nurse != []) {
                        $dt_transaction_nurse = array(
                            'id'            => $id,
                            'costing_id'    => $dt_costing[0]->costing_id,
                            'costing_name'  => $dt_costing[0]->name,
                            'date_added'    => isset($dt_put['date_added']) ? $dt_put['date_added'] : $tr_nurse[0]->date_added,
                            'user_nurse_id' => isset($dt_put['user_nurse_id']) ? $dt_put['user_nurse_id'] : $tr_nurse[0]->user_nurse_id,
                            'var_1'         => isset($dt_put['var_1']) ? $dt_put['var_1'] : $tr_nurse[0]->var_1,
                            'var_2'         => isset($dt_put['var_2']) ? $dt_put['var_2'] : $tr_nurse[0]->var_2,
                            'var_3'         => $dt_put['var_3'] != null && $dt_put['var_3'] != 0 && $dt_put['var_3'] != '' ? $dt_put['var_1']*$dt_put['var_2'] : $tr_nurse[0]->var_3,
                            'notes'         => isset($dt_put['notes']) ? $dt_put['notes'] : $tr_nurse[0]->notes,
                        );
                        $id = $this->$model->update($id, $dt_transaction_nurse);
                    } else {
                        $this->REST_Return(404, 'Transaction_nurse with id ' . $id . ' does not exist!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Wrong costing_id!');
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