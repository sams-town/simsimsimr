<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Transaction_alkes extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Transaction_alkes API
     * --------------------
     * @param: registration_destination_id
     * @param: costing_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: notes
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_alkes
     **/

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|required|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|required|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|required|max_length[8]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check registration_destination_id and costing_id
                $dt_costing = $this->Costing_services_class->read($dt_post['costing_id'], false);
                if($this->Registration_destination->read($dt_post['registration_destination_id'], false) != [] && $dt_costing != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $t = time();
                    $dt_transaction_alkes = array(
                        'registration_destination_id'   => $dt_post['registration_destination_id'],
                        'costing_id'                    => $dt_costing[0]->costing_id,
                        'costing_name'                  => $dt_costing[0]->name,
                        'var_1'                         => $dt_post['var_1'],
                        'var_2'                         => $dt_post['var_2'],
                        'var_3'                         => $dt_post['var_3'],
                        'notes'                         => isset($dt_post['notes']) ? $dt_post['notes'] : '',
                        'date_added'                    => date("Y-m-d H:i:s",$t)
                    );
                    $id = $this->$model->create($dt_transaction_alkes);
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
                    $this->REST_Return(404, 'Wrong registration_destination_id or costing_id!');
                }
                return;     
            }
        } else {
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

        $registration_destination_id = $dt_post['registration_destination_id'];
        $costing_id = $dt_post['costing_id'];
        $var_1 = $dt_post['var_1'];
        $var_2 = $dt_post['var_2'];
        $var_3 = $dt_post['var_3'];
        $notes = $dt_post['notes'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id, a.name';
        $costing = $this->Costing->select($costing_id, $select);
        if (count($costing) == 0) {
            $this->REST_Return(404, 'there is no costing with id ' . $costing_id);
            return;
        }

        $costing_name = $costing[0]->name;

        $now = date("Y-m-d H:i:s", time());

        /* Start Transaction */
        $this->db->trans_start();

        $dt_transaction_alkes = array(
            'registration_destination_id'   => $registration_destination_id,
            'costing_id'                    => $costing_id,
            'costing_name'                  => $costing_name,
            'var_1'                         => $var_1,
            'var_2'                         => $var_2,
            'var_3'                         => $var_3,
            'notes'                         => $notes,
            'date_added'                    => $now
        );

        $id = $this->$model->create($dt_transaction_alkes);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_transaction_alkes,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create transaction_alkes', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );
        $this->REST_Return(201, 'SUCCESS', $response_data);
    }

    /**
     * GET Transaction_alkes with API
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
                $this->$model->whereFields['b.registration_id'] = $registration_id; 
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

            if (isset($q_strings['registration_id'])) {
                $this->$model->whereFields['b.registration_id'] = $q_strings['registration_id'];
                unset($q_strings['registration_id']);
            }

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
     * Transaction_alkes API
     * --------------------
     * @param: costing_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: notes
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_alkes
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
                $dt_costing = $this->Costing_services_class->read($dt_put['costing_id'], false);
                if($dt_costing != []) {
                    $tr_doctor = $this->$model->read($id);
                    if($tr_doctor != []) {
                        $dt_transaction_alkes = array(
                            'id'            => $id,
                            'costing_id'    => $dt_costing[0]->costing_id,
                            'costing_name'  => $dt_costing[0]->name,
                            'var_1'         => isset($dt_put['var_1']) ? $dt_put['var_1'] : $tr_doctor[0]->var_1,
                            'var_2'         => isset($dt_put['var_2']) ? $dt_put['var_2'] : $tr_doctor[0]->var_2,
                            'var_3'         => isset($dt_put['var_3']) ? $dt_put['var_3'] : $tr_doctor[0]->var_3,
                            'notes'         => isset($dt_put['notes']) ? $dt_put['notes'] : $tr_doctor[0]->notes,
                        );
                        $id = $this->$model->update($id, $dt_transaction_alkes);
                    } else {
                        $this->REST_Return(404, 'Transaction_alkes with id ' . $id . ' does not exist!');
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
            $this->REST_Return(400, 'transaction_alkes_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $transaction_alkes_id = $this->$model->select_id($id);
    
        if ($transaction_alkes_id == "") {
            $this->REST_Return(404, 'there is no transaction_alkes with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_transaction_alkes = array(
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($transaction_alkes_id, $dt_transaction_alkes);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_transaction_alkes,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete transaction_alkes', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}