<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_sdki_slki_siki extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
        $this->load->model("v1/mr/Sdki_model", "Sdki");
        $this->load->model("v1/mr/Slki_model", "Slki");
        $this->load->model("v1/mr/Siki_model", "Siki");
    }

    /**
     * Nurse_sdki_slki_siki API
     * --------------------
     * @param: nurse_id
     * @param: sdki_id
     * @param: slki_id
     * @param: siki_id
     * @param: associated_with_arry
     * @param: outcome_criteria_arry
     * @param: observation_arry
     * @param: collaboration_arry
     * @param: therapeutic_arry
     * @param: education_arry
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_sdki_slki_siki
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
            $this->form_validation->set_rules('sdki_id', 'sdki_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('slki_id', 'slki_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('siki_id', 'siki_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('associated_with_arry', 'associated_with_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('outcome_criteria_arry', 'outcome_criteria_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('observation_arry', 'observation_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('collaboration_arry', 'collaboration_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('therapeutic_arry', 'therapeutic_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('education_arry', 'education_arry', 'trim|max_length[50]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check nurse_id or sdki_id or slki_id or siki_id
                if($this->Nurse->read($dt_post['nurse_id'], false) != [] && $this->Sdki->read($dt_post['sdki_id'], false) != [] && $this->Slki->read($dt_post['slki_id'], false) != [] && $this->Siki->read($dt_post['siki_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_nurse_sdki_slki_siki = array(
                        'nurse_id'                  => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
                        'sdki_id'                   => isset($dt_post['sdki_id']) ? $dt_post['sdki_id'] : '',
                        'slki_id'                   => isset($dt_post['slki_id']) ? $dt_post['slki_id'] : '',
                        'siki_id'                   => isset($dt_post['siki_id']) ? $dt_post['siki_id'] : '',
                        'associated_with_arry'      => isset($dt_post['associated_with_arry']) ? implode(',',$dt_post['associated_with_arry']) : '',
                        'outcome_criteria_arry'     => isset($dt_post['outcome_criteria_arry']) ? implode(',',$dt_post['outcome_criteria_arry']) : '',
                        'observation_arry'          => isset($dt_post['observation_arry']) ? implode(',',$dt_post['observation_arry']) : '',
                        'collaboration_arry'        => isset($dt_post['collaboration_arry']) ? implode(',',$dt_post['collaboration_arry']) : '',
                        'therapeutic_arry'          => isset($dt_post['therapeutic_arry']) ? implode(',',$dt_post['therapeutic_arry']) : '',
                        'education_arry'            => isset($dt_post['education_arry']) ? implode(',',$dt_post['education_arry']) : '',
                    );
                    $id = $this->$model->create($dt_nurse_sdki_slki_siki);
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
                    $this->REST_Return(404, 'Wrong nurse_id or sdki_id or slki_id or siki_id!');
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
     * GET Nurse_sdki_slki_siki with API
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
     * Nurse_sdki_slki_siki API
     * --------------------
     * @param: sdki_id
     * @param: slki_id
     * @param: siki_id
     * @param: associated_with_arry
     * @param: outcome_criteria_arry
     * @param: observation_arry
     * @param: collaboration_arry
     * @param: therapeutic_arry
     * @param: education_arry
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_sdki_slki_siki
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
            $this->form_validation->set_rules('sdki_id', 'sdki_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('slki_id', 'slki_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('siki_id', 'siki_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('associated_with_arry', 'associated_with_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('outcome_criteria_arry', 'outcome_criteria_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('observation_arry', 'observation_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('collaboration_arry', 'collaboration_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('therapeutic_arry', 'therapeutic_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('education_arry', 'education_arry', 'trim|max_length[50]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check sdki_id or slki_id or siki_id
                if($this->Sdki->read($dt_put['sdki_id'], false) != [] && $this->Slki->read($dt_put['slki_id'], false) != [] && $this->Siki->read($dt_put['siki_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $nurse_sdki_slki_siki = $this->$model->read($id); 
                    if($nurse_sdki_slki_siki != []) {
                        $dt_nurse_sdki_slki_siki = array(
                            'id'                        => $id,
                            'sdki_id'                   => isset($dt_put['sdki_id']) ? $dt_put['sdki_id'] : $nurse_sdki_slki_siki[0]->sdki_id,
                            'slki_id'                   => isset($dt_put['slki_id']) ? $dt_put['slki_id'] : $nurse_sdki_slki_siki[0]->slki_id,
                            'siki_id'                   => isset($dt_put['siki_id']) ? $dt_put['siki_id'] : $nurse_sdki_slki_siki[0]->siki_id,
                            'associated_with_arry'      => isset($dt_put['associated_with_arry']) ? implode(',',$dt_put['associated_with_arry']) : $nurse_sdki_slki_siki[0]->associated_with_arry,
                            'outcome_criteria_arry'     => isset($dt_put['outcome_criteria_arry']) ? implode(',',$dt_put['outcome_criteria_arry']) : $nurse_sdki_slki_siki[0]->outcome_criteria_arry,
                            'observation_arry'          => isset($dt_put['observation_arry']) ? implode(',',$dt_put['observation_arry']) : $nurse_sdki_slki_siki[0]->observation_arry,
                            'collaboration_arry'        => isset($dt_put['collaboration_arry']) ? implode(',',$dt_put['collaboration_arry']) : $nurse_sdki_slki_siki[0]->collaboration_arry,
                            'therapeutic_arry'          => isset($dt_put['therapeutic_arry']) ? implode(',',$dt_put['therapeutic_arry']) : $nurse_sdki_slki_siki[0]->therapeutic_arry,
                            'education_arry'            => isset($dt_put['education_arry']) ? implode(',',$dt_put['education_arry']) : $nurse_sdki_slki_siki[0]->education_arry,
                        );
                        $id = $this->$model->update($id, $dt_nurse_sdki_slki_siki);
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
                } else {
                    $this->REST_Return(404, 'Wrong nurse_id or sdki_id or slki_id or siki_id!');
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