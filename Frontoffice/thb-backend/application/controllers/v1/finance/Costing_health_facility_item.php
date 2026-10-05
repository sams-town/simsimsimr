<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Costing_health_facility_item extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Costing_model", "Costing");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
    }

    /**
     * Costing_health_facility_item API
     * --------------------
     * @param: costing_id
     * @param: health_facility_item
     * --------------------
     * @method : POST
     * @link: version/finance/costing_health_facility_item
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
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('health_facility_item[]', 'health_facility_item', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                for ($i=0; $i < count($dt_post['health_facility_item']) ; $i++) { 
                    // Checking costing_id
                    if($this->Costing->read($dt_post['costing_id'], false) != []) {
                        // Checking health_facility_item_id
                        if($this->Health_facility_item->read($dt_post['health_facility_item'][$i]['health_facility_item_id'], false) != []) {
                            $dt_costing_health_facility_item = array(
                                'costing_id'                => $dt_post['costing_id'],
                                'health_facility_item_id'   => $dt_post['health_facility_item'][$i]['health_facility_item_id']
                            );
                            $this->$model->replace($dt_costing_health_facility_item);
                        } else {
                            $this->REST_Return(404, 'There is no health_facility_item with id ' . $dt_post['health_facility_item'][$i]['health_facility_item_id']);
                            return;
                        }
                    } else {
                        $this->REST_Return(404, 'Wrong costing_id');
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
                        'message'     => 'Created!'
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

    /**
     * GET Costing_health_facility_item with API
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
     * Costing_health_facility_item API
     * --------------------
     * @param: health_facility_item
     * --------------------
     * @method : POST
     * @link: version/finance/costing_health_facility_item
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
            $this->form_validation->set_rules('health_facility_item[]', 'health_facility_item', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Checking health_facility_item_id
                for ($i=0; $i < count($dt_put['health_facility_item']) ; $i++) {
                    if($this->Health_facility_item->read($dt_put['health_facility_item'][$i]['health_facility_item_id'], false) == []) {
                        $this->REST_Return(404, 'There is no health_facility_item with id ' . $dt_put['health_facility_item'][$i]['health_facility_item_id']);
                        return;
                    }
                }
                $health_facility_item = $this->$model->read($id);
                if($health_facility_item != []) {
                    $temp = array();
                    for ($a=0; $a < count($health_facility_item); $a++) { 
                        array_push($temp, array(
                            'costing_id'                => $id,
                            'health_facility_item_id'   => $health_facility_item[$a]->health_facility_item_id,
                            'is_delete'                 => '1',
                        ));
                    }
                    $this->$model->replace_batch($temp);
    
                    $dt_hf = array();
                    for ($i=0; $i < count($dt_put['health_facility_item']) ; $i++) {
                        array_push($dt_hf, array(
                            'costing_id'                => $id,
                            'health_facility_item_id'   => $dt_put['health_facility_item'][$i]['health_facility_item_id'],
                            'is_delete'                 => '0',
                        ));
                    }
                    $id = $this->$model->replace_batch($dt_hf);
                } else {
                    $this->REST_Return(404, 'Costing Health Facility Item with id ' . $id . ' does not exist!');
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