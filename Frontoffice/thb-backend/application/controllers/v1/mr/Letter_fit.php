<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Letter_fit extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Letter_model", "Letter");
    }

    /**
     * Letter_fit API
     * --------------------
     * @param: letter_id
     * @param: physical_examination_date
     * @param: weight
     * @param: height
     * @param: visus_od
     * @param: blood_type_id
     * @param: blind_color_type_id
     * @param: fit_type_id
     * @param: letter_for
     * --------------------
     * @method : POST
     * @link: version/mr/letter_fit
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
            $this->form_validation->set_rules('physical_examination_date', 'physical_examination_date', 'trim');
            $this->form_validation->set_rules('weight', 'weight', 'trim|max_length[8]');
            $this->form_validation->set_rules('height', 'height', 'trim|max_length[8]');
            $this->form_validation->set_rules('visus_od', 'visus_od', 'trim|max_length[200]');
            $this->form_validation->set_rules('blood_type_id', 'blood_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('blind_color_type_id', 'blind_color_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('fit_type_id', 'fit_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('letter_for', 'letter_for', 'trim');
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
                        'letter_id'                     => isset($dt_post['letter_id']) ? $dt_post['letter_id'] : '',
                        'physical_examination_date'     => isset($dt_post['physical_examination_date']) ? $dt_post['physical_examination_date'] : '',
                        'weight'                        => isset($dt_post['weight']) ? $dt_post['weight'] : '',
                        'height'                        => isset($dt_post['height']) ? $dt_post['height'] : '',
                        'visus_od'                      => isset($dt_post['visus_od']) ? $dt_post['visus_od'] : '',
                        'blood_type_id'                 => isset($dt_post['blood_type_id']) ? $dt_post['blood_type_id'] : '',
                        'blind_color_type_id'           => isset($dt_post['blind_color_type_id']) ? $dt_post['blind_color_type_id'] : '',
                        'fit_type_id'                   => isset($dt_post['fit_type_id']) ? $dt_post['fit_type_id'] : '',
                        'letter_for'                    => isset($dt_post['letter_for']) ? $dt_post['letter_for'] : ''
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
     * GET Letter_fit with API
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
     * Letter_fit API
     * --------------------
     * @param: letter_id
     * @param: physical_examination_date
     * @param: weight
     * @param: height
     * @param: visus_od
     * @param: blood_type_id
     * @param: blind_color_type_id
     * @param: fit_type_id
     * @param: letter_for
     * --------------------
     * @method : POST
     * @link: version/mr/letter_fit
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
            $this->form_validation->set_rules('physical_examination_date', 'physical_examination_date', 'trim');
            $this->form_validation->set_rules('weight', 'weight', 'trim|max_length[8]');
            $this->form_validation->set_rules('height', 'height', 'trim|max_length[8]');
            $this->form_validation->set_rules('visus_od', 'visus_od', 'trim|max_length[200]');
            $this->form_validation->set_rules('blood_type_id', 'blood_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('blind_color_type_id', 'blind_color_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('fit_type_id', 'fit_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('letter_for', 'letter_for', 'trim');
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
                        'id'                            => $id,
                        'physical_examination_date'     => isset($dt_put['physical_examination_date']) ? $dt_put['physical_examination_date'] : $letter[0]->physical_examination_date,
                        'weight'                        => isset($dt_put['weight']) ? $dt_put['weight'] : $letter[0]->weight,
                        'height'                        => isset($dt_put['height']) ? $dt_put['height'] : $letter[0]->height,
                        'visus_od'                      => isset($dt_put['visus_od']) ? $dt_put['visus_od'] : $letter[0]->visus_od,
                        'blood_type_id'                 => isset($dt_put['blood_type_id']) ? $dt_put['blood_type_id'] : $letter[0]->blood_type_id,
                        'blind_color_type_id'           => isset($dt_put['blind_color_type_id']) ? $dt_put['blind_color_type_id'] : $letter[0]->blind_color_type_id,
                        'fit_type_id'                   => isset($dt_put['fit_type_id']) ? $dt_put['fit_type_id'] : $letter[0]->fit_type_id,
                        'letter_for'                    => isset($dt_put['letter_for']) ? $dt_put['letter_for'] : $letter[0]->letter_for,
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