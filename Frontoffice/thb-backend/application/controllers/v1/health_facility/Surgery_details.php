<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Surgery_details extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Surgery_request_model", "Surgery_request");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }
    
    /**
     * Surgery_details API
     * --------------------
     * @param: surgery_request_id
     * @param: schedule
     * @param: start_time
     * @param: end_time
     * @param: duration
     * @param: asa_type_id
     * @param: pre_surgery_type_id
     * @param: post_surgery_type_id
     * @param: pre_surgery_date_approve
     * @param: surgery_type_id
     * @param: priority_type_id
     * @param: infectious_type_id
     * @param: accommodation_request
     * @param: difficulty_intubation
     * @param: note
     * --------------------
     * @method : POST
     * @link: version/health_facility/surgery_details
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
            $this->form_validation->set_rules('surgery_request_id', 'surgery_request_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('schedule', 'schedule', 'trim');
            $this->form_validation->set_rules('start_time', 'start_time', 'trim');
            $this->form_validation->set_rules('end_time', 'end_time', 'trim');
            $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[6]');
            $this->form_validation->set_rules('asa_type_id', 'asa_type_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('pre_surgery_type_id', 'pre_surgery_type_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('post_surgery_type_id', 'post_surgery_type_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('pre_surgery_date_approve', 'pre_surgery_date_approve', 'trim');
            $this->form_validation->set_rules('surgery_type_id', 'surgery_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('priority_type_id', 'priority_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('infectious_type_id', 'infectious_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('accommodation_request', 'accommodation_request', 'trim');
            $this->form_validation->set_rules('difficulty_intubation', 'difficulty_intubation', 'trim');
            $this->form_validation->set_rules('note', 'note', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check surgery_request_id
                if($this->Surgery_request->read($dt_post['surgery_request_id'],false) != []) {
                    $dt_surgery_details = array(
                        'surgery_request_id'        => $dt_post['surgery_request_id'],
                        'schedule'                  => isset($dt_post['schedule']) ? $dt_post['schedule'] : '',
                        'start_time'                => isset($dt_post['start_time']) ? $dt_post['start_time'] : '',
                        'end_time'                  => isset($dt_post['end_time']) ? $dt_post['end_time'] : '',
                        'duration'                  => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                        'asa_type_id'               => isset($dt_post['asa_type_id']) ? $dt_post['asa_type_id'] : '',
                        'pre_surgery_type_id'       => isset($dt_post['pre_surgery_type_id']) ? $dt_post['pre_surgery_type_id'] : '',
                        'post_surgery_type_id'      => isset($dt_post['post_surgery_type_id']) ? $dt_post['post_surgery_type_id'] : '',
                        'pre_surgery_date_approve'  => isset($dt_post['pre_surgery_date_approve']) ? $dt_post['pre_surgery_date_approve'] : '',
                        'surgery_type_id'           => isset($dt_post['surgery_type_id']) ? $dt_post['surgery_type_id'] : '',
                        'priority_type_id'          => isset($dt_post['priority_type_id']) ? $dt_post['priority_type_id'] : '',
                        'infectious_type_id'        => isset($dt_post['infectious_type_id']) ? $dt_post['infectious_type_id'] : '',
                        'accommodation_request'     => isset($dt_post['accommodation_request']) ? $dt_post['accommodation_request'] : '',
                        'difficulty_intubation'     => isset($dt_post['difficulty_intubation']) ? $dt_post['difficulty_intubation'] : '',
                        'note'                      => isset($dt_post['note']) ? $dt_post['note'] : '',
                        'date_added'                => date('Y-m-d H:i:s',time()),
                    );
                    $id = $this->$model->create($dt_surgery_details);
                } else {
                    $this->REST_Return(404, 'Wrong surgery_request_id!');
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
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                }
                return;
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

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
            $this->$model->whereFields['a.is_delete'] = '0';
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
            $this->$model->whereFields['a.is_delete'] = '0';
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
     * Surgery_details API
     * --------------------
     * @param: surgery_request_id
     * @param: schedule
     * @param: start_time
     * @param: end_time
     * @param: duration
     * @param: asa_type_id
     * @param: pre_surgery_type_id
     * @param: post_surgery_type_id
     * @param: pre_surgery_date_approve
     * @param: surgery_type_id
     * @param: priority_type_id
     * @param: infectious_type_id
     * @param: accommodation_request
     * @param: difficulty_intubation
     * @param: note
     * --------------------
     * @method : POST
     * @link: version/mr/surgery_details
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
            $this->form_validation->set_rules('surgery_request_id', 'surgery_request_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('schedule', 'schedule', 'trim');
            $this->form_validation->set_rules('start_time', 'start_time', 'trim');
            $this->form_validation->set_rules('end_time', 'end_time', 'trim');
            $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[6]');
            $this->form_validation->set_rules('asa_type_id', 'asa_type_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('pre_surgery_type_id', 'pre_surgery_type_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('post_surgery_type_id', 'post_surgery_type_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('pre_surgery_date_approve', 'pre_surgery_date_approve', 'trim');
            $this->form_validation->set_rules('surgery_type_id', 'surgery_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('priority_type_id', 'priority_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('infectious_type_id', 'infectious_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('accommodation_request', 'accommodation_request', 'trim');
            $this->form_validation->set_rules('difficulty_intubation', 'difficulty_intubation', 'trim');
            $this->form_validation->set_rules('note', 'note', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->$model->read($id) != []) {
                    if($this->Surgery_request->read($dt_put['surgery_request_id'])) {
                        $dt_surgery_details = array(
                            'id'                       => $id,
                            'surgery_request_id'       => $dt_put['surgery_request_id'],
                            'schedule'                 => $dt_put['schedule'],
                            'start_time'               => $dt_put['start_time'],
                            'end_time'                 => $dt_put['end_time'],
                            'duration'                 => $dt_put['duration'],
                            'asa_type_id'              => $dt_put['asa_type_id'],
                            'pre_surgery_type_id'      => $dt_put['pre_surgery_type_id'],
                            'post_surgery_type_id'     => $dt_put['post_surgery_type_id'],
                            'pre_surgery_date_approve' => $dt_put['pre_surgery_date_approve'],
                            'surgery_type_id'          => $dt_put['surgery_type_id'],
                            'priority_type_id'         => $dt_put['priority_type_id'],
                            'infectious_type_id'       => $dt_put['infectious_type_id'],
                            'accommodation_request'    => $dt_put['accommodation_request'],
                            'difficulty_intubation'    => $dt_put['difficulty_intubation'],
                            'note'                     => $dt_put['note'],
                        );
                        $this->$model->update($id, $dt_surgery_details);
                    } else {
                        $this->REST_Return(404, 'Wrong surgery_request_id!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Surgery_details with id ' . $id . ' does not exist!');
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