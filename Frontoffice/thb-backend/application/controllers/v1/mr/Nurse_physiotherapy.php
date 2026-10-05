<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_physiotherapy extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
    }
    
    /**
     * Nurse_physiotherapy API
     * --------------------
     * @param: nurse_id
     * @param: medical_diagnosis
     * @param: support_diagnosis
     * @param: general_rr
     * @param: general_inspection
     * @param: general_palpation
     * @param: general_active
     * @param: general_passive
     * @param: general_isometric
     * @param: general_auscultation
     * @param: general_percussion
     * @param: special_muscle_test
     * @param: special_rom_test
     * @param: special_anthropometry_test
     * @param: special_sore_scale
     * @param: special_specific_test
     * @param: functional_abilities
     * @param: physiotherapy_diagnosis
     * @param: physiotherapy_program
     * @param: evaluation_follow_up
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_physiotherapy
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
            $this->form_validation->set_rules('medical_diagnosis', 'medical_diagnosis', 'trim');
            $this->form_validation->set_rules('support_diagnosis', 'support_diagnosis', 'trim');
            $this->form_validation->set_rules('general_rr', 'general_rr', 'trim|max_length[4]');
            $this->form_validation->set_rules('general_inspection', 'general_inspection', 'trim');
            $this->form_validation->set_rules('general_palpation', 'general_palpation', 'trim');
            $this->form_validation->set_rules('general_active', 'general_active', 'trim|max_length[100]');
            $this->form_validation->set_rules('general_passive', 'general_passive', 'trim|max_length[100]');
            $this->form_validation->set_rules('general_isometric', 'general_isometric', 'trim|max_length[100]');
            $this->form_validation->set_rules('general_auscultation', 'general_auscultation', 'trim');
            $this->form_validation->set_rules('general_percussion', 'general_percussion', 'trim');
            $this->form_validation->set_rules('special_muscle_test', 'special_muscle_test', 'trim');
            $this->form_validation->set_rules('special_rom_test', 'special_rom_test', 'trim');
            $this->form_validation->set_rules('special_anthropometry_test', 'special_anthropometry_test', 'trim');
            $this->form_validation->set_rules('special_sore_scale', 'special_sore_scale', 'trim|max_length[100]');
            $this->form_validation->set_rules('special_specific_test', 'special_specific_test', 'trim');
            $this->form_validation->set_rules('functional_abilities', 'functional_abilities', 'trim');
            $this->form_validation->set_rules('physiotherapy_diagnosis', 'physiotherapy_diagnosis', 'trim');
            $this->form_validation->set_rules('physiotherapy_program', 'physiotherapy_program', 'trim');
            $this->form_validation->set_rules('evaluation_follow_up', 'evaluation_follow_up', 'trim');
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
                    $dt_nurse_physiotherapy = array(
                        'nurse_id'                      => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
                        'medical_diagnosis'             => isset($dt_post['medical_diagnosis']) ? $dt_post['medical_diagnosis'] : '',
                        'support_diagnosis'             => isset($dt_post['support_diagnosis']) ? $dt_post['support_diagnosis'] : '',
                        'general_rr'                    => isset($dt_post['general_rr']) ? $dt_post['general_rr'] : '',
                        'general_inspection'            => isset($dt_post['general_inspection']) ? $dt_post['general_inspection'] : '',
                        'general_palpation'             => isset($dt_post['general_palpation']) ? $dt_post['general_palpation'] : '',
                        'general_active'                => isset($dt_post['general_active']) ? $dt_post['general_active'] : '',
                        'general_passive'               => isset($dt_post['general_passive']) ? $dt_post['general_passive'] : '',
                        'general_isometric'             => isset($dt_post['general_isometric']) ? $dt_post['general_isometric'] : '',
                        'general_auscultation'          => isset($dt_post['general_auscultation']) ? $dt_post['general_auscultation'] : '',
                        'general_percussion'            => isset($dt_post['general_percussion']) ? $dt_post['general_percussion'] : '',
                        'special_muscle_test'           => isset($dt_post['special_muscle_test']) ? $dt_post['special_muscle_test'] : '',
                        'special_rom_test'              => isset($dt_post['special_rom_test']) ? $dt_post['special_rom_test'] : '',
                        'special_anthropometry_test'    => isset($dt_post['special_anthropometry_test']) ? $dt_post['special_anthropometry_test'] : '',
                        'special_sore_scale'            => isset($dt_post['special_sore_scale']) ? $dt_post['special_sore_scale'] : '',
                        'special_specific_test'         => isset($dt_post['special_specific_test']) ? $dt_post['special_specific_test'] : '',
                        'functional_abilities'          => isset($dt_post['functional_abilities']) ? $dt_post['functional_abilities'] : '',
                        'physiotherapy_diagnosis'       => isset($dt_post['physiotherapy_diagnosis']) ? $dt_post['physiotherapy_diagnosis'] : '',
                        'physiotherapy_program'         => isset($dt_post['physiotherapy_program']) ? $dt_post['physiotherapy_program'] : '',
                        'evaluation_follow_up'          => isset($dt_post['evaluation_follow_up']) ? $dt_post['evaluation_follow_up'] : '',
                    );
                    $id = $this->$model->create($dt_nurse_physiotherapy);
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
     * GET Nurse_physiotherapy with API
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
     * Nurse_physiotherapy API
     * --------------------
     * @param: medical_diagnosis
     * @param: support_diagnosis
     * @param: general_rr
     * @param: general_inspection
     * @param: general_palpation
     * @param: general_active
     * @param: general_passive
     * @param: general_isometric
     * @param: general_auscultation
     * @param: general_percussion
     * @param: special_muscle_test
     * @param: special_rom_test
     * @param: special_anthropometry_test
     * @param: special_sore_scale
     * @param: special_specific_test
     * @param: functional_abilities
     * @param: physiotherapy_diagnosis
     * @param: physiotherapy_program
     * @param: evaluation_follow_up
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_physiotherapy
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
            $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('medical_diagnosis', 'medical_diagnosis', 'trim');
            $this->form_validation->set_rules('support_diagnosis', 'support_diagnosis', 'trim');
            $this->form_validation->set_rules('general_rr', 'general_rr', 'trim|max_length[4]');
            $this->form_validation->set_rules('general_inspection', 'general_inspection', 'trim');
            $this->form_validation->set_rules('general_palpation', 'general_palpation', 'trim');
            $this->form_validation->set_rules('general_active', 'general_active', 'trim|max_length[100]');
            $this->form_validation->set_rules('general_passive', 'general_passive', 'trim|max_length[100]');
            $this->form_validation->set_rules('general_isometric', 'general_isometric', 'trim|max_length[100]');
            $this->form_validation->set_rules('general_auscultation', 'general_auscultation', 'trim');
            $this->form_validation->set_rules('general_percussion', 'general_percussion', 'trim');
            $this->form_validation->set_rules('special_muscle_test', 'special_muscle_test', 'trim');
            $this->form_validation->set_rules('special_rom_test', 'special_rom_test', 'trim');
            $this->form_validation->set_rules('special_anthropometry_test', 'special_anthropometry_test', 'trim');
            $this->form_validation->set_rules('special_sore_scale', 'special_sore_scale', 'trim|max_length[100]');
            $this->form_validation->set_rules('special_specific_test', 'special_specific_test', 'trim');
            $this->form_validation->set_rules('functional_abilities', 'functional_abilities', 'trim');
            $this->form_validation->set_rules('physiotherapy_diagnosis', 'physiotherapy_diagnosis', 'trim');
            $this->form_validation->set_rules('physiotherapy_program', 'physiotherapy_program', 'trim');
            $this->form_validation->set_rules('evaluation_follow_up', 'evaluation_follow_up', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse_physiotherapy = $this->$model->read($id); 
                if($nurse_physiotherapy != []) {
                    $dt_nurse_physiotherapy = array(
                        'id'                            => $id,
                        'medical_diagnosis'             => isset($dt_put['medical_diagnosis']) ? $dt_put['medical_diagnosis'] : $nurse_physiotherapy[0]->medical_diagnosis,
                        'support_diagnosis'             => isset($dt_put['support_diagnosis']) ? $dt_put['support_diagnosis'] : $nurse_physiotherapy[0]->support_diagnosis,
                        'general_rr'                    => isset($dt_put['general_rr']) ? $dt_put['general_rr'] : $nurse_physiotherapy[0]->general_rr,
                        'general_inspection'            => isset($dt_put['general_inspection']) ? $dt_put['general_inspection'] : $nurse_physiotherapy[0]->general_inspection,
                        'general_palpation'             => isset($dt_put['general_palpation']) ? $dt_put['general_palpation'] : $nurse_physiotherapy[0]->general_palpation,
                        'general_active'                => isset($dt_put['general_active']) ? $dt_put['general_active'] : $nurse_physiotherapy[0]->general_active,
                        'general_passive'               => isset($dt_put['general_passive']) ? $dt_put['general_passive'] : $nurse_physiotherapy[0]->general_passive,
                        'general_isometric'             => isset($dt_put['general_isometric']) ? $dt_put['general_isometric'] : $nurse_physiotherapy[0]->general_isometric,
                        'general_auscultation'          => isset($dt_put['general_auscultation']) ? $dt_put['general_auscultation'] : $nurse_physiotherapy[0]->general_auscultation,
                        'general_percussion'            => isset($dt_put['general_percussion']) ? $dt_put['general_percussion'] : $nurse_physiotherapy[0]->general_percussion,
                        'special_muscle_test'           => isset($dt_put['special_muscle_test']) ? $dt_put['special_muscle_test'] : $nurse_physiotherapy[0]->special_muscle_test,
                        'special_rom_test'              => isset($dt_put['special_rom_test']) ? $dt_put['special_rom_test'] : $nurse_physiotherapy[0]->special_rom_test,
                        'special_anthropometry_test'    => isset($dt_put['special_anthropometry_test']) ? $dt_put['special_anthropometry_test'] : $nurse_physiotherapy[0]->special_anthropometry_test,
                        'special_sore_scale'            => isset($dt_put['special_sore_scale']) ? $dt_put['special_sore_scale'] : $nurse_physiotherapy[0]->special_sore_scale,
                        'special_specific_test'         => isset($dt_put['special_specific_test']) ? $dt_put['special_specific_test'] : $nurse_physiotherapy[0]->special_specific_test,
                        'functional_abilities'          => isset($dt_put['functional_abilities']) ? $dt_put['functional_abilities'] : $nurse_physiotherapy[0]->functional_abilities,
                        'physiotherapy_diagnosis'       => isset($dt_put['physiotherapy_diagnosis']) ? $dt_put['physiotherapy_diagnosis'] : $nurse_physiotherapy[0]->physiotherapy_diagnosis,
                        'physiotherapy_program'         => isset($dt_put['physiotherapy_program']) ? $dt_put['physiotherapy_program'] : $nurse_physiotherapy[0]->physiotherapy_program,
                        'evaluation_follow_up'          => isset($dt_put['evaluation_follow_up']) ? $dt_put['evaluation_follow_up'] : $nurse_physiotherapy[0]->evaluation_follow_up,
                    );
                    $id = $this->$model->update($id, $dt_nurse_physiotherapy);
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