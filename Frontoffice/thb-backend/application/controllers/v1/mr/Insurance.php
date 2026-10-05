<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Insurance extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/third_party/Company_model", "Company");
    }

    /**
     * Insurance API
     * --------------------
     * @param: norm
     * @param: insurance_id
     * @param: company_id
     * @param: number_insurance
     * @param: expired
     * @param: service_class_id
     * --------------------
     * @method : POST
     * @link: version/mr/insurance
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
            $count = count($dt_post['insurance']);
            for($i=0; $i < $count; $i++) {
                // Check norm and company_id
                if($this->Patient->read($dt_post['insurance'][$i]['norm'], false) != [] && $this->Company->read($dt_post['insurance'][$i]['company_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_insurance = array(
                        'norm'                  => $dt_post['insurance'][$i]['norm'],
                        'insurance_id'          => $dt_post['insurance'][$i]['insurance_id'],
                        'company_id'            => $dt_post['insurance'][$i]['company_id'],
                        'number_insurance'      => $dt_post['insurance'][$i]['number_insurance'],
                        'expired'               => $dt_post['insurance'][$i]['expired'],
                        'service_class_id'      => $dt_post['insurance'][$i]['service_class_id']
                    );
                    $id = $this->$model->replace($dt_insurance);
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
                    $this->REST_Return(404, 'Wrong norm ' . $dt_post['insurance'][$i]['norm'] .  ' or company_id ' . $dt_post['insurance'][$i]['company_id']);
                    return;
                }
            }
            return;  
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Insurance with API
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
     * Insurance API
     * --------------------
     * @param: insurance_id
     * @param: company_id
     * @param: number_insurance
     * @param: expired
     * @param: service_class_id
     * --------------------
     * @method : POST
     * @link: version/mr/insurance
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
            $this->form_validation->set_rules('insurance_id', 'insurance_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('company_id', 'company_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('number_insurance', 'number_insurance', 'trim|max_length[20]');
            $this->form_validation->set_rules('expired', 'expired', 'trim');
            $this->form_validation->set_rules('service_class_id', 'service_class_id', 'trim|required|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $insurance = $this->$model->read($id);
                if($insurance != []) {
                    // Check company_id
                    if($this->Company->read($dt_put['company_id'], false) != []) {
                        $dt_insurance = array(
                            'norm'                  => $id,
                            'insurance_id'          => isset($dt_put['insurance_id']) ? $dt_put['insurance_id'] : $insurance[0]->insurance_id,
                            'company_id'            => isset($dt_put['company_id']) ? $dt_put['company_id'] : $insurance[0]->company_id,
                            'number_insurance'      => isset($dt_put['number_insurance']) ? $dt_put['number_insurance'] : $insurance[0]->number_insurance,
                            'expired'               => isset($dt_put['expired']) ? $dt_put['expired'] : $insurance[0]->expired,
                            'service_class_id'      => isset($dt_put['service_class_id']) ? $dt_put['service_class_id'] : $insurance[0]->service_class_id
                        );
                        $id = $this->$model->update($id, $dt_insurance);
                    } else {
                        $this->REST_Return(404, 'Wrong company_id!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Insurance with id ' . $id . ' does not exist!');
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