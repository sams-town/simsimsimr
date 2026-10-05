<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Soap_s extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Soap_model", "Soap");
    }

    /**
     * Soap_s API
     * --------------------
     * @param: soap_id
     * @param: anamesa_type_id
     * @param: family_name
     * @param: family_relation_id
     * @param: main_complaint
     * @param: history_disease_now
     * @param: history_disease_past
     * @param: history_drugs
     * @param: history_allergy
     * --------------------
     * @method : POST
     * @link: version/mr/soap_s
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
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('history_drugs', 'history_drugs', 'trim');
            $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check soap_id
                if($this->Soap->read($dt_post['soap_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_soap_s = array(
                        'soap_id'               => $dt_post['soap_id'],
                        'anamesa_type_id'       => isset($dt_post['anamesa_type_id']) ? $dt_post['anamesa_type_id'] : '',
                        'family_name'           => isset($dt_post['family_name']) ? $dt_post['family_name'] : '',
                        'family_relation_id'    => isset($dt_post['family_relation_id']) ? $dt_post['family_relation_id'] : '',
                        'main_complaint'        => isset($dt_post['main_complaint']) ? $dt_post['main_complaint'] : '',
                        'history_disease_now'   => isset($dt_post['history_disease_now']) ? $dt_post['history_disease_now'] : '',
                        'history_disease_past'  => isset($dt_post['history_disease_past']) ? $dt_post['history_disease_past'] : '',
                        'history_drugs'         => isset($dt_post['history_drugs']) ? $dt_post['history_drugs'] : '',
                        'history_allergy'       => isset($dt_post['history_allergy']) ? $dt_post['history_allergy'] : '',
                    );
                    $id = $this->$model->create($dt_soap_s);
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
                    $this->REST_Return(404, 'Wrong soap_id!');
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
     * GET Soap_s with API
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


    /**
     * Soap_s API
     * --------------------
     * @param: anamesa_type_id
     * @param: family_name
     * @param: family_relation_id
     * @param: main_complaint
     * @param: history_disease_now
     * @param: history_disease_past
     * @param: history_drugs
     * @param: history_allergy
     * --------------------
     * @method : POST
     * @link: version/mr/soap_s
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
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('history_drugs', 'history_drugs', 'trim');
            $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $soap_s = $this->$model->read($id); 
                if($soap_s != []) {
                    $dt_soap_s = array(
                        'id'                    => $id,
                        'anamesa_type_id'       => isset($dt_put['anamesa_type_id']) ? $dt_put['anamesa_type_id'] : $soap_s[0]->anamesa_type_id,
                        'family_name'           => isset($dt_put['family_name']) ? $dt_put['family_name'] : $soap_s[0]->family_name,
                        'family_relation_id'    => isset($dt_put['family_relation_id']) ? $dt_put['family_relation_id'] : $soap_s[0]->family_relation_id,
                        'main_complaint'        => isset($dt_put['main_complaint']) ? $dt_put['main_complaint'] : $soap_s[0]->main_complaint,
                        'history_disease_now'   => isset($dt_put['history_disease_now']) ? $dt_put['history_disease_now'] : $soap_s[0]->history_disease_now,
                        'history_disease_past'  => isset($dt_put['history_disease_past']) ? $dt_put['history_disease_past'] : $soap_s[0]->history_disease_past,
                        'history_drugs'         => isset($dt_put['history_drugs']) ? $dt_put['history_drugs'] : $soap_s[0]->history_drugs,
                        'history_allergy'       => isset($dt_put['history_allergy']) ? $dt_put['history_allergy'] : $soap_s[0]->history_allergy,
                    );
                    $id = $this->$model->update($id, $dt_soap_s);
                } else {
                    $this->REST_Return(404, 'Soap_s with id ' . $id . ' does not exist!');
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