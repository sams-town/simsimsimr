<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Mr_timestamp_patient extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Timestamp_model", "Timestamp");
    }

    /**
     * Mr_timestamp_patient API
     * --------------------
     * @param: registration_id
     * @param: timestamp_id
     * @param: officer_id
     * @param: date_before
     * @param: date_after
     * --------------------
     * @method : POST
     * @link: version/mr/mr_timestamp_patient
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
            $this->form_validation->set_rules('registration_id', 'registration_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('timestamp_id', 'timestamp_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('officer_id', 'officer_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('date_before', 'date_before', 'trim|required');
            $this->form_validation->set_rules('date_after', 'date_after', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check registration_id and timestamp_id and officer_id
                $employee = $this->Employee->read($dt_post['officer_id']);
                $timestamp = $this->Timestamp->read($dt_post['timestamp_id']);
                if($this->Registration->read($dt_post['registration_id'], false) != [] && $timestamp != [] && $employee != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_mr_timestamp_patient = array(
                        'registration_id'     => $dt_post['registration_id'],
                        'timestamp_id'        => $dt_post['timestamp_id'],
                        'timestamp_name'      => $timestamp[0]->name,
                        'officer_id'          => $dt_post['officer_id'],
                        'officer_name'        => $employee[0]->name,
                        'date_before'         => $dt_post['date_before'],
                        'date_after'          => $dt_post['date_after']
                    );
                    $id = $this->$model->replace($dt_mr_timestamp_patient);
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
                    $this->REST_Return(404, 'Wrong registration_id or timestamp_id or officer_id!');
                    return;
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
     * GET Mr_timestamp_patient with API
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
     * Mr_timestamp_patient API
     * --------------------
     * @param: timestamp_id
     * @param: officer_id
     * @param: date_before
     * @param: date_after
     * --------------------
     * @method : POST
     * @link: version/mr/mr_timestamp_patient
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
            $this->form_validation->set_rules('timestamp_id', 'timestamp_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('officer_id', 'officer_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('date_before', 'date_before', 'trim|required');
            $this->form_validation->set_rules('date_after', 'date_after', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $employee = $this->Employee->read($dt_put['officer_id']);
                $timestamp = $this->Timestamp->read($dt_put['timestamp_id']);
                if($timestamp != [] && $employee != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $mr_timestamp_patient = $this->$model->read($id); 
                    if($mr_timestamp_patient != []) {
                        $dt_mr_timestamp_patient = array(
                            'registration_id'     => $id,
                            'timestamp_id'        => $dt_put['timestamp_id'],
                            'timestamp_name'      => $timestamp[0]->name,
                            'officer_id'          => $dt_put['officer_id'],
                            'officer_name'        => $employee[0]->name,
                            'date_before'         => $dt_put['date_before'],
                            'date_after'          => $dt_put['date_after']
                        );
                        $id = $this->$model->update($id, $dt_mr_timestamp_patient);
                    } else {
                        $this->REST_Return(404, 'MR Timestamp Patient with id ' . $id . ' does not exist!');
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
                } else {
                    $this->REST_Return(404, 'Wrong timestamp_id or officer_id!');
                    return;
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}