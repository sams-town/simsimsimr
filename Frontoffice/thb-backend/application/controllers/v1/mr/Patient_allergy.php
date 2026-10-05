<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Patient_allergy extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/md/Allergy_model", "Allergy");
    }

    /**
     * Patient_allergy API
     * --------------------
     * @param: norm
     * @param: type_allergy_id
     * @param: allergy_id
     * @param: name
     * @param: date_tragedy
     * @param: side_effect
     * @param: reaction
     * @param: severity
     * --------------------
     * @method : POST
     * @link: version/mr/patient_allergy
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
            $this->form_validation->set_rules('norm', 'norm', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('type_allergy_id', 'type_allergy_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('allergy_id', 'allergy_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('date_tragedy', 'date_tragedy', 'trim|required');
            $this->form_validation->set_rules('side_effect', 'side_effect', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('reaction', 'reaction', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('severity', 'severity', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check norm and allergy_id
                if($this->Patient->read($dt_post['norm'], false) != [] && $this->Allergy->read($dt_post['allergy_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    
                    $dt_patient_allergy = array(
                        'norm'              => $dt_post['norm'],
                        'type_allergy_id'   => $dt_post['type_allergy_id'],
                        'allergy_id'        => $dt_post['allergy_id'],
                        'name'              => $dt_post['name'],
                        'date_tragedy'      => $dt_post['date_tragedy'],
                        'side_effect'       => $dt_post['side_effect'],
                        'reaction'          => $dt_post['reaction'],
                        'severity'          => $dt_post['severity']
                    );
                    $id = $this->$model->replace($dt_patient_allergy);
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
                    $this->REST_Return(404, 'Wrong norm or allergy_id!');
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
     * GET Patient Allergy with API
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
     * Patient_allergy API
     * --------------------
     * @param: type_allergy_id
     * @param: allergy_id
     * @param: name
     * @param: date_tragedy
     * @param: side_effect
     * @param: reaction
     * @param: severity
     * --------------------
     * @method : POST
     * @link: version/mr/patient_allergy
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
            $this->form_validation->set_rules('type_allergy_id', 'type_allergy_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('allergy_id', 'allergy_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('date_tragedy', 'date_tragedy', 'trim|required');
            $this->form_validation->set_rules('side_effect', 'side_effect', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('reaction', 'reaction', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('severity', 'severity', 'trim|required|max_length[50]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check allergy_id
                if($this->Allergy->read($dt_put['allergy_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    if($this->$model->read($id) != []) {
                        $dt_patient_allergy = array(
                            'norm'              => $id,
                            'type_allergy_id'   => $dt_put['type_allergy_id'],
                            'allergy_id'        => $dt_put['allergy_id'],
                            'name'              => $dt_put['name'],
                            'date_tragedy'      => $dt_put['date_tragedy'],
                            'side_effect'       => $dt_put['side_effect'],
                            'reaction'          => $dt_put['reaction'],
                            'severity'          => $dt_put['severity']
                        );
                        $id = $this->$model->update($id, $dt_patient_allergy);
                    } else {
                        $this->REST_Return(404, 'Patient Allergy with id ' . $id . ' does not exist!');
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
                    $this->REST_Return(404, 'Wrong allergy_id!');
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