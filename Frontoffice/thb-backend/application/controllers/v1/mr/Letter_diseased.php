<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Letter_diseased extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Letter_model", "Letter");
    }

    /**
     * Letter_diseased API
     * --------------------
     * @param: letter_id
     * @param: dieseased_date
     * @param: rest_day
     * @param: not_physical_exercise
     * @param: deseased
     * --------------------
     * @method : POST
     * @link: version/mr/letter_diseased
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
            $this->form_validation->set_rules('letter_id', 'letter_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('dieseased_date', 'dieseased_date', 'trim');
            $this->form_validation->set_rules('rest_day', 'rest_day', 'trim|max_length[8]');
            $this->form_validation->set_rules('not_physical_exercise', 'not_physical_exercise', 'trim|max_length[8]');
            $this->form_validation->set_rules('deseased', 'deseased', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check letter_id
                if($this->Letter->read($dt_post['letter_id'],false) != []) {
                    $t = time();
                    $dt_letter = array(
                        'letter_id'                  => isset($dt_post['letter_id']) ? $dt_post['letter_id'] : '',
                        'dieseased_date'             => isset($dt_post['dieseased_date']) ? $dt_post['dieseased_date'] : '',
                        'rest_day'                   => isset($dt_post['rest_day']) ? $dt_post['rest_day'] : '',
                        'not_physical_exercise'      => isset($dt_post['not_physical_exercise']) ? $dt_post['not_physical_exercise'] : '',
                        'deseased'                   => isset($dt_post['deseased']) ? $dt_post['deseased'] : ''
                    );
                    $id = $this->$model->create($dt_letter);
                } else {
                    $this->REST_Return(404, 'Wrong letter_id');
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

    /**
     * GET Letter_diseased with API
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
     * Letter_diseased API
     * --------------------
     * @param: letter_id
     * @param: dieseased_date
     * @param: rest_day
     * @param: not_physical_exercise
    * @param: deseased
     * --------------------
     * @method : POST
     * @link: version/mr/letter_diseased
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
            $this->form_validation->set_rules('dieseased_date', 'dieseased_date', 'trim');
            $this->form_validation->set_rules('rest_day', 'rest_day', 'trim|max_length[8]');
            $this->form_validation->set_rules('not_physical_exercise', 'not_physical_exercise', 'trim|max_length[8]');
            $this->form_validation->set_rules('deseased', 'deseased', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $letter = $this->$model->read($id); 
                // print_r($letter);
                // die();
                if($letter != []) {
                    $dt_letter = array(
                        'id'                        => $id,
                        'dieseased_date'            => isset($dt_put['dieseased_date']) ? $dt_put['dieseased_date'] : $letter[0]->dieseased_date,
                        'rest_day'                  => isset($dt_put['rest_day']) ? $dt_put['rest_day'] : $letter[0]->rest_day,
                        'not_physical_exercise'     => isset($dt_put['not_physical_exercise']) ? $dt_put['not_physical_exercise'] : $letter[0]->not_physical_exercise,
                        'deseased'                  => isset($dt_put['deseased']) ? $dt_put['deseased'] : $letter[0]->deseased
                    );
                    $id = $this->$model->update($id, $dt_letter);
                } else {
                    $this->REST_Return(404, 'Letter die with id ' . $id . ' does not exist!');
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