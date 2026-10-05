<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Soap_a extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_a_action_model", "Soap_a_action");
        $this->load->model("v1/mr/Icd10_model", "Icd10");
    }

    /**
     * Soap_a API
     * --------------------
     * @param: soap_id
     * @param: development_notes
     * @param: anythingelse
     * @param: icd10_id
     * --------------------
     * @method : POST
     * @link: version/mr/soap_a
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
            $this->form_validation->set_rules('development_notes', 'development_notes', 'trim');
            $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim');
            // DATA SOAP_A_ACTION
            $this->form_validation->set_rules('icd10[]', 'icd10', 'trim|required');
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

                    // INIT_SOAP_A
                    $dt_soap_a = array(
                        'soap_id'               => $dt_post['soap_id'],
                        'development_notes'     => isset($dt_post['development_notes']) ? $dt_post['development_notes'] : '',
                        'anythingelse'          => isset($dt_post['anythingelse']) ? $dt_post['anythingelse'] : ''
                    );
                    $id = $this->$model->create($dt_soap_a);

                    // INIT_SOAP_A_ACTION
                    for ($i=0; $i < count($dt_post['icd10']) ; $i++) { 
                        // Check icd10_id
                        if($this->Icd10->read($dt_post['icd10'][$i]['icd10_id'], false) != []) {
                            $dt_soap_a_action = array(
                                'soap_a_id'        => $id,
                                'icd10_id'         => $dt_post['icd10'][$i]['icd10_id']
                            );
                            $this->Soap_a_action->create($dt_soap_a_action);
                        } else {
                            $this->REST_Return(404, 'There is no icd10 with id ' . $dt_post['icd10'][$i]['icd10_id']);
                            return;
                        }
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
     * GET Soap_a with API
     * -------------------------
     * @method: GET
     */

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $this->Soap_a_action->whereFields['soap_a_id'] = $output[$i]->id; 
                $soap_a_action = $this->Soap_a_action->read();
                for ($j=0; $j < count($soap_a_action); $j++) { 
                    $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
                    $output[$i]->icd10[$j]['icd10_id'] = $soap_a_action[$j]->icd10_id;
                    $output[$i]->icd10[$j]['icd10_name'] = $icd10[0]->name;
                }
            }
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
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Soap_a_action->whereFields['soap_a_id'] = $output->rows[$i]->id; 
                $soap_a_action = $this->Soap_a_action->read();
                for ($j=0; $j < count($soap_a_action); $j++) { 
                    $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
                    $output->rows[$i]->icd10[$j]['icd10_id'] = $soap_a_action[$j]->icd10_id;
                    $output->rows[$i]->icd10[$j]['icd10_name'] = $icd10[0]->name;
                }
            }
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
     * Soap_a API
     * --------------------
     * @param: development_notes
     * @param: anythingelse
     * --------------------
     * @method : POST
     * @link: version/mr/soap_a
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
            $this->form_validation->set_rules('development_notes', 'development_notes', 'trim');
            $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $soap_a = $this->$model->read($id); 
                if($soap_a != []) {
                    $dt_soap_a = array(
                        'id'                    => $id,
                        'development_notes'     => isset($dt_put['development_notes']) ? $dt_put['development_notes'] : $soap_a[0]->development_notes,
                        'anythingelse'          => isset($dt_put['anythingelse']) ? $dt_put['anythingelse'] : $soap_a[0]->anythingelse
                    );
                    $id = $this->$model->update($id, $dt_soap_a);
                } else {
                    $this->REST_Return(404, 'Soap_a with id ' . $id . ' does not exist!');
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