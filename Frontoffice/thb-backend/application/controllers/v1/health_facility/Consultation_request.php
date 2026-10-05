<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Consultation_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        // Load Helper 
        $this->load->helper('queue');

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Consultation_request API
     * --------------------
     * @param: soap_id
     * @param: health_facility_item_id
     * @param: doctor_id
     * @param: consul_type_id
     * @param: consultation_type_id
     * @param: consultation_arry
     * @param: notes_or_problems
     * @param: consul_date
     * --------------------
     * @method : POST
     * @link: version/health_facility/consultation_request
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
            $this->form_validation->set_rules('health_facility_item_id', 'health_facility_item_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_id', 'doctor_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('consul_type_id', 'consul_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('consultation_type_id', 'consultation_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('consultation_arry', 'consultation_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('notes_or_problems', 'notes_or_problems', 'trim');
            $this->form_validation->set_rules('consul_date', 'consul_date', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();

                // GET NO_REQUEST AND QUEUE
                $no_request = no_request($this->name,'CON');

                // Check soap_id or health_facility_item_id or doctor_id 
                if($this->Soap->read($dt_post['soap_id'],false) != [] && $this->Health_facility_item->read($dt_post['health_facility_item_id'],false) != [] && $this->Employee->read($dt_post['doctor_id'],false) != []) {
                    $dt_consultation_request = array(
                        'no_request'                => $no_request['no_request'],
                        'soap_id'                   => $dt_post['soap_id'],
                        'health_facility_item_id'   => $dt_post['health_facility_item_id'],
                        'doctor_id'                 => $dt_post['doctor_id'],
                        'consul_type_id'            => $dt_post['consul_type_id'],
                        'notes_or_problems'         => $dt_post['notes_or_problems'],
                        'consul_date'               => $dt_post['consul_date'],
                        'queue'                     => $no_request['queue'],
                        'date_added'                => date('Y-m-d H:i:s',time()),
                    );
                    if($dt_post['consul_type_id'] == 1) {
                        $dt_consultation_request['consultation_type_id'] = $dt_post['consultation_type_id'];
                        $dt_consultation_request['consultation_arry'] = implode(',',$dt_post['consultation_arry']);
                    }

                    $id = $this->$model->create($dt_consultation_request);
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
                    return;
                } else {
                    $this->REST_Return(404, 'Wrong soap_id or health_facility_item_id or doctor_id!');
                    return;
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Consultation_request with API
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
     * Consultation_request API
     * --------------------
     * @param: soap_id
     * @param: health_facility_item_id
     * @param: doctor_id
     * @param: consul_type_id
     * @param: consultation_type_id
     * @param: consultation_arry
     * @param: notes_or_problems
     * @param: consul_date
     * --------------------
     * @method : POST
     * @link: version/mr/consultation_request
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
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('health_facility_item_id', 'health_facility_item_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_id', 'doctor_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('consul_type_id', 'consul_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('consultation_type_id', 'consultation_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('consultation_arry', 'consultation_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('notes_or_problems', 'notes_or_problems', 'trim');
            $this->form_validation->set_rules('consul_date', 'consul_date', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $consultation_request = $this->$model->read($id);
                if($consultation_request != []) {
                    $dt_consultation_request = array(
                        'soap_id'                   => $dt_put['soap_id'],
                        'health_facility_item_id'   => $dt_put['health_facility_item_id'],
                        'doctor_id'                 => $dt_put['doctor_id'],
                        'consul_type_id'            => $dt_put['consul_type_id'],
                        'notes_or_problems'         => $dt_put['notes_or_problems'],
                        'consul_date'               => $dt_put['consul_date'],
                    );
                    if($dt_put['consul_type_id'] == 1) {
                        $dt_consultation_request['consultation_type_id'] = $dt_put['consultation_type_id'];
                        $dt_consultation_request['consultation_arry'] = implode(',',$dt_put['consultation_arry']);
                    }
                    $this->$model->update($id, $dt_consultation_request);
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