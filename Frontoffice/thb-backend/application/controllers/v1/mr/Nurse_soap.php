<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_soap extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
    }

    /**
     * Nurse_soap API
     * --------------------
     * @param: nurse_id
     * @param: s
     * @param: o
     * @param: a
     * @param: p
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_soap
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
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('s', 's', 'trim');
            $this->form_validation->set_rules('o', 'o', 'trim');
            $this->form_validation->set_rules('a', 'a', 'trim');
            $this->form_validation->set_rules('p', 'p', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check nurse_id
                if($this->Nurse->read($dt_post['nurse_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_nurse_soap = array(
                        'nurse_id'      => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
                        's'             => isset($dt_post['s']) ? $dt_post['s'] : '',
                        'o'             => isset($dt_post['o']) ? $dt_post['o'] : '',
                        'a'             => isset($dt_post['a']) ? $dt_post['a'] : '',
                        'p'             => isset($dt_post['p']) ? $dt_post['p'] : '',
                        'user_added'    => $user_id,
                        'date_added'    => date('Y-m-d H:i:s')
                    );
                    $id = $this->$model->create($dt_nurse_soap);
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
                    $this->REST_Return(404, 'Wrong nurse_id!');
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
     * GET Nurse_soap with API
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
     * Nurse_soap API
     * --------------------
     * @param: nurse_id
     * @param: s
     * @param: o
     * @param: a
     * @param: p
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_soap
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
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('s', 's', 'trim');
            $this->form_validation->set_rules('o', 'o', 'trim');
            $this->form_validation->set_rules('a', 'a', 'trim');
            $this->form_validation->set_rules('p', 'p', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse_soap = $this->$model->read($id); 
                if($nurse_soap != []) {
                    $dt_nurse_soap = array(
                        'id'            => $id,
                        's'             => isset($dt_put['s']) ? $dt_put['s'] : $nurse_soap[0]->s,
                        'o'             => isset($dt_put['o']) ? $dt_put['o'] : $nurse_soap[0]->o,
                        'a'             => isset($dt_put['a']) ? $dt_put['a'] : $nurse_soap[0]->a,
                        'p'             => isset($dt_put['p']) ? $dt_put['p'] : $nurse_soap[0]->p,
                        'user_updated'    => $user_id,
                        'date_updated'    => date('Y-m-d H:i:s')
                    );
                    $id = $this->$model->update($id, $dt_nurse_soap);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
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