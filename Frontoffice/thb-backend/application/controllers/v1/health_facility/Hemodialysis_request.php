<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Hemodialysis_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Hemodialysis_package_model", "Hemodialysis_package");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/mr/Soap_model", "Soap");
        // Load Helper 
        $this->load->helper('queue');

        $this->update_rules = array(
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Hemodialysis_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/health_facility/hemodialysis_request
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
            $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            $this->form_validation->set_rules('hemodialysis_request_items[]', 'hemodialysis_request_items', 'trim|required');
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
                $no_request = no_request($this->name,'HEM');

                // Check soap_id
                if($this->Soap->read($dt_post['soap_id'],false) != []) {
                    $dt_hemodialysis_request = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'no_request'            => $no_request['no_request'],
                        'soap_id'               => $dt_post['soap_id'],
                        'date_request'          => $dt_post['date_request'],
                        'queue'                 => $no_request['queue'],
                    );
                    $id = $this->$model->create($dt_hemodialysis_request);
                    
                    // INIT_HEMODIALYSIS_REQUEST_ITEM
                    for ($i=0; $i < count($dt_post['hemodialysis_request_items']) ; $i++) {
                        $hemodialysis_package = $this->Hemodialysis_package->read($dt_post['hemodialysis_request_items'][$i]['hemodialysis_package_id'], false);
                        $service_class = $this->Service_class->read($dt_post['hemodialysis_request_items'][$i]['service_class_id']);
                        // Check hemodialysis_package_id & service_class_id
                        if($hemodialysis_package != [] && $service_class != []) {
                            $costing = $this->Costing->read($hemodialysis_package[0]->costing_id,false);
                            $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                            $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                            $costing_services_class = $this->Costing_services_class->read(0,false);
                            $dt_hemodialysis_request_item = array(
                                'hemodialysis_request_id'      => $id,
                                'hemodialysis_package_id'      => $dt_post['hemodialysis_request_items'][$i]['hemodialysis_package_id'],
                                'service_class_id'              => $service_class[0]->id,
                                'var_1'                         => $costing_services_class[0]->var_1,
                                'var_2'                         => $costing_services_class[0]->var_2,
                                'var_3'                         => $costing_services_class[0]->var_3,
                                'costing_id'                    => $costing[0]->id,
                                'costing_name'                  => $costing[0]->name,
                                'notes'                         => $dt_post['hemodialysis_request_items'][$i]['notes'],
                                'status'                        => 1
                            );
                            $this->Hemodialysis_request_item->create($dt_hemodialysis_request_item);
                        } else {
                            $this->REST_Return(404, 'There is no hemodialysis_package with id ' . $dt_post['hemodialysis_request_items'][$i]['hemodialysis_package_id'] . ' or service_class with id ' . $dt_post['hemodialysis_request_items'][$i]['service_class_id']);
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
                            'id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                    return;
                } else {
                    $this->REST_Return(404, 'Wrong soap_id!');
                    return;
                }
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $hemodialysis_request_item = $this->Hemodialysis_request_item->read($output[$i]->id);
                if ($hemodialysis_request_item != []) {
                    for ($j=0; $j < count($hemodialysis_request_item); $j++) { 
                        $output[$i]->hemodialysis_request_item[$j]['id'] = $hemodialysis_request_item[$j]->id;
                        $output[$i]->hemodialysis_request_item[$j]['hemodialysis_request_id'] = $hemodialysis_request_item[$j]->hemodialysis_request_id;
                        $output[$i]->hemodialysis_request_item[$j]['hemodialysis_package_id'] = $hemodialysis_request_item[$j]->hemodialysis_package_id;                        
                        $output[$i]->hemodialysis_request_item[$j]['costing_id'] = $hemodialysis_request_item[$j]->costing_id;
                        $output[$i]->hemodialysis_request_item[$j]['costing_name'] = $hemodialysis_request_item[$j]->costing_name;
                        $output[$i]->hemodialysis_request_item[$j]['var_1'] = $hemodialysis_request_item[$j]->var_1;
                        $output[$i]->hemodialysis_request_item[$j]['var_2'] = $hemodialysis_request_item[$j]->var_2;
                        $output[$i]->hemodialysis_request_item[$j]['var_3'] = $hemodialysis_request_item[$j]->var_3;
                        $output[$i]->hemodialysis_request_item[$j]['notes'] = $hemodialysis_request_item[$j]->notes;
                        $output[$i]->hemodialysis_request_item[$j]['status'] = $hemodialysis_request_item[$j]->status;
                    }
                } 
            }
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $hemodialysis_request_item = $this->Hemodialysis_request_item->read($output->rows[$i]->id);
                if ($hemodialysis_request_item != []) {
                    for ($j=0; $j < count($hemodialysis_request_item); $j++) { 
                        $output->rows[$i]->hemodialysis_request_item[$j]['id'] = $hemodialysis_request_item[$j]->id;
                        $output->rows[$i]->hemodialysis_request_item[$j]['hemodialysis_request_id'] = $hemodialysis_request_item[$j]->hemodialysis_request_id;
                        $output->rows[$i]->hemodialysis_request_item[$j]['hemodialysis_package_id'] = $hemodialysis_request_item[$j]->hemodialysis_package_id;                        
                        $output->rows[$i]->hemodialysis_request_item[$j]['costing_id'] = $hemodialysis_request_item[$j]->costing_id;
                        $output->rows[$i]->hemodialysis_request_item[$j]['costing_name'] = $hemodialysis_request_item[$j]->costing_name;
                        $output->rows[$i]->hemodialysis_request_item[$j]['var_1'] = $hemodialysis_request_item[$j]->var_1;
                        $output->rows[$i]->hemodialysis_request_item[$j]['var_2'] = $hemodialysis_request_item[$j]->var_2;
                        $output->rows[$i]->hemodialysis_request_item[$j]['var_3'] = $hemodialysis_request_item[$j]->var_3;
                        $output->rows[$i]->hemodialysis_request_item[$j]['notes'] = $hemodialysis_request_item[$j]->notes;
                        $output->rows[$i]->hemodialysis_request_item[$j]['status'] = $hemodialysis_request_item[$j]->status;
                    }
                } 
            }
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
     * Hemodialysis_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/mr/hemodialysis_request
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
            $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            $this->form_validation->set_rules('hemodialysis_request_items[]', 'hemodialysis_request_items', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $hemodialysis_request = $this->$model->read($id);
                if($hemodialysis_request != []) {
                    $hemodialysis_request = array(
                        'id'              => $id,
                        'soap_id'         => $dt_put['soap_id'],
                        'date_request'    => $dt_put['date_request'],
                    );
                    $this->$model->update($id, $hemodialysis_request);
                    for ($i=0; $i < count($dt_put['hemodialysis_request_items']); $i++) {
                        $this->Hemodialysis_request_item->whereFields['a.id'] = $dt_put['hemodialysis_request_items'][$i]['id'];
                        $hemodialysis_request_item = $this->Hemodialysis_request_item->read(0,false); 
                        if($hemodialysis_request_item != []) {
                            $hemodialysis_package = $this->Hemodialysis_package->read($dt_put['hemodialysis_request_items'][$i]['hemodialysis_package_id'], false);
                            $service_class = $this->Service_class->read($dt_put['hemodialysis_request_items'][$i]['service_class_id'],false);
                            if($hemodialysis_package != [] && $service_class != []) {
                                $costing = $this->Costing->read($hemodialysis_package[0]->costing_id,false);
                                $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                                $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                                $costing_services_class = $this->Costing_services_class->read(0,false);
                                $hemodialysis_request_item = array(
                                    'id'                        => $dt_put['hemodialysis_request_items'][$i]['id'],
                                    'hemodialysis_request_id'   => $id,
                                    'hemodialysis_package_id'   => $dt_put['hemodialysis_request_items'][$i]['hemodialysis_package_id'],
                                    'service_class_id'          => $service_class[0]->id,
                                    'var_1'                     => $costing_services_class[0]->var_1,
                                    'var_2'                     => $costing_services_class[0]->var_2,
                                    'var_3'                     => $costing_services_class[0]->var_3,
                                    'costing_id'                => $costing[0]->id,
                                    'costing_name'              => $costing[0]->name,
                                    'notes'                     => $dt_put['hemodialysis_request_items'][$i]['notes'],
                                    'status'                    => $dt_put['hemodialysis_request_items'][$i]['status'],
                                );
                                $this->Hemodialysis_request_item->replace($hemodialysis_request_item);
                            } else {
                                $this->REST_Return(404, 'There is no hemodialysis_package with id ' . $dt_put['hemodialysis_request_items'][$i]['hemodialysis_package_id'] . ' or service_class with id ' . $dt_put['hemodialysis_request_items'][$i]['service_class_id']);
                                return;
                            }
                        } else {
                            $this->REST_Return(404, 'Hemodialysis_request_item with id ' . $dt_put['hemodialysis_request_items'][$i]['id'] . ' does not exist!');
                            return;
                        }
                    }
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