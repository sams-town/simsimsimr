<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Physiotherapy_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Physiotherapy_package_model", "Physiotherapy_package");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_insurance_model", "Registration_insurance");
        // Load Helper
        $this->load->helper('queue');

        $this->create_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim|required'),
            array('field' => 'physiotherapy_request_items[]', 'label' => 'physiotherapy_request_items', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Physiotherapy_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/health_facility/physiotherapy_request
     **/
    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $soap_id = $dt_post['soap_id'];
        $date_request = $dt_post['date_request'];
        $physiotherapy_request_items_arr = $dt_post['physiotherapy_request_items'];

        $soap = $this->Soap->select_id($soap_id);

        if ($soap == '') {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        // NOTE - DEPRECATED, DELETED SOON!
        // // Count no_request
        // $this->$model->whereFields['a.is_delete'] = 0;
        // $this->$model->whereFields['a.date_request >='] = $date_request .' 00:00:00';
        // $this->$model->whereFields['a.date_request <='] = $date_request .' 23:59:59';
        // $this->$model->orderFields = array('a.no_request' => 'DESC');
        // $this->$model->limit = 1;
        // $tmp = $this->$model->read(0,false);

        // $datetime = strtotime($date_request);
        // $waktu = date("Ymd", $datetime);

        // if($tmp != []) {
        //     $tmp_raw_date = substr($tmp[0]->no_request,3,8);
        //     $tmp_epoch_date = strtotime($tmp_raw_date);
        //     $tmp_date = date("Ymd", $tmp_epoch_date);

        //     if ($tmp_date != $waktu) {
        //         $no_request = 'RHM' . $waktu . '0001';
        //     } else {
        //         $no_request = 'RHM' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
        //     }
        // } else {
        //     $no_request = 'RHM' . $waktu . '0001';
        // }

        // // Count queue
        // $this->$model->whereFields['a.is_delete'] = 0;
        // $this->$model->whereFields['a.date_request >='] =  $date_request .' 00:00:00';
        // $this->$model->whereFields['a.date_request <='] =  $date_request .' 23:59:59';
        // $this->$model->orderFields = array('a.queue' => 'DESC');
        // $this->$model->limit = 1;
        // $tmp = $this->$model->read(0,false);

        // $datetime = strtotime($date_request);
        // $waktu = date("Ymd", $datetime);

        // if($tmp != []) {
        //     $tmp_raw_date = substr($tmp[0]->no_request,3,8);
        //     $tmp_epoch_date = strtotime($tmp_raw_date);
        //     $tmp_date = date("Ymd", $tmp_epoch_date);

        //     if ($tmp_date != $waktu) {
        //         $queue = 1;
        //     } else {
        //         $queue = $tmp[0]->queue + 1;
        //     }
        // } else {
        //     $queue = 1;
        // }

        // count no_request & queue
        $prefix = 'RHM';
        $epoch = strtotime($date_request);
        $dt_queue = array(
            'var_2' => date('Y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('Y-m-d', $epoch),
        );

        $no_request = counter_queue($prefix, $dt_queue, 4);
        $queue = round((int)substr($no_request, -4));

        /* Start Transaction */
        $this->db->trans_start();

        $dt_physiotherapy_request = array(
            'institute_branch_id'   => $institute_branch_id,
            'soap_id'               => $soap_id,
            'no_request'            => $no_request,
            'queue'                 => $queue,
            'date_request'          => $date_request
        );
        $id = $this->$model->create($dt_physiotherapy_request);
    
        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_physiotherapy_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create physiotherapy_request', $response_data);
            return;
        }

        for ($i=0; $i < count($physiotherapy_request_items_arr) ; $i++) {
            if (!isset($physiotherapy_request_items_arr[$i]['physiotherapy_package_id'])) {
                $this->REST_Return(400, "physiotherapy_package_id field in physiotherapy_request_items is required!");
                return;
            }

            if (!isset($physiotherapy_request_items_arr[$i]['service_class_id'])) {
                $this->REST_Return(400, "service_class_id field in physiotherapy_request_items is required!");
                return;
            }

            $physiotherapy_package_id = $physiotherapy_request_items_arr[$i]['physiotherapy_package_id'];
            $service_class_id = $physiotherapy_request_items_arr[$i]['service_class_id'];

            $physiotherapy_package = $this->Physiotherapy_package->read($physiotherapy_package_id, false);
            $service_class = $this->Service_class->read($service_class_id, false);

            if ($physiotherapy_package == []) {
                $this->REST_Return(404, "there is no physiotherapy_package with id " . $physiotherapy_package_id);
                return;
            }

            if ($service_class == []) {
                $this->REST_Return(404, "there is no service_class with id " . $service_class_id);
                return;
            }

            $costing_id = $physiotherapy_package[0]->costing_id;

            $costing = $this->Costing->read($costing_id, false);
            
            $this->Costing_services_class->whereFields['a.costing_id'] = $costing_id;
            $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class_id;
            $costing_services_class = $this->Costing_services_class->read(0, false);
            
            if ($costing == []) {
                $this->REST_Return(404, "there is no costing with id " . $costing_id);
                return;
            }

            if ($costing_services_class == []) {
                $this->REST_Return(404, "there is no costing_services_class with service_class_id " . $service_class_id);
                return;
            }

            $costing_name = $costing[0]->name;

            $costing_services_class_var_1 = $costing_services_class[0]->var_1;
            $costing_services_class_var_2 = $costing_services_class[0]->var_2;
            $costing_services_class_var_3 = $costing_services_class[0]->var_3;

            /* Start Transaction */
            $this->db->trans_start();
            
            $dt_physiotherapy_request_item = array(
                'physiotherapy_request_id'  => $id,
                'physiotherapy_package_id'  => $physiotherapy_package_id,
                'service_class_id'          => $service_class_id,
                'var_1'                     => $costing_services_class_var_1,
                'var_2'                     => $costing_services_class_var_2,
                'var_3'                     => $costing_services_class_var_3,
                'costing_id'                => $costing_id,
                'costing_name'              => $costing_name,
                'date_request'              => $date_request,
                'status'                    => 1
            );
            $this->Physiotherapy_request_item->create($dt_physiotherapy_request_item);

            $this->db->trans_complete();
            /* End Transaction */

            if ($this->db->trans_status() === false) {
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_physiotherapy_request_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to create physiotherapy_request_item', $response_data);
                return;
            }
        }
                    
        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;            
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
                $this->Registration->whereFields['b.id'] =  $output[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                $this->Physiotherapy_request_item->whereFields['a.is_delete'] = 0;
                $physiotherapy_request_item = $this->Physiotherapy_request_item->read($output[$i]->id);
                $output[$i]->regData = $resgistration_data;
                $no=1;
                if ($physiotherapy_request_item != []) {
                    for ($j=0; $j < count($physiotherapy_request_item); $j++) { 
                        $output[$i]->physiotherapy_request_item[$j]['no'] = $no;
                        $output[$i]->physiotherapy_request_item[$j]['id'] = $physiotherapy_request_item[$j]->id;
                        $output[$i]->physiotherapy_request_item[$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
                        $output[$i]->physiotherapy_request_item[$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
                        $output[$i]->physiotherapy_request_item[$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
                        $output[$i]->physiotherapy_request_item[$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
                        $output[$i]->physiotherapy_request_item[$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
                        $output[$i]->physiotherapy_request_item[$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
                        $output[$i]->physiotherapy_request_item[$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
                        $output[$i]->physiotherapy_request_item[$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                        $output[$i]->physiotherapy_request_item[$j]['status'] = $physiotherapy_request_item[$j]->status;
                        $output[$i]->physiotherapy_request_item[$j]['date_request'] = $physiotherapy_request_item[$j]->date_request;
                        $output[$i]->physiotherapy_request_item[$j]['date_process'] = $physiotherapy_request_item[$j]->date_process;
                        $output[$i]->physiotherapy_request_item[$j]['date_result'] = $physiotherapy_request_item[$j]->date_result;
                        $output[$i]->physiotherapy_request_item[$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                        $output[$i]->physiotherapy_request_item[$j]['status'] = $physiotherapy_request_item[$j]->status;
                        $no++;
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
    

    public function history_get($p = 0){
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
            if ($q_strings['norm']){
                $this->$model->whereFields['r.norm'] = $q_strings['norm']; unset($q_strings['norm']);
                $this->$model->history();
            }
            // $this->$model->whereLike = $q_strings;
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $resgistration_data = $this->Registration->read(0, false)[0];
                $insurance_data = $this->Registration_insurance->read($output->rows[$i]->registration_destination_id, false)[0];
                $physiotherapy_request_item = $this->Physiotherapy_request_item->read($output->rows[$i]->id);
                $output->rows[$i]->code = $resgistration_data->code;
                $output->rows[$i]->register_date = $resgistration_data->register_date;
                $output->rows[$i]->insurance_name = $insurance_data->insurance_name;
                $output->rows[$i]->type_how_to_enter_name = $resgistration_data->type_how_to_enter_name;
                $output->rows[$i]->exit_name = $resgistration_data->exit_name;
                $output->rows[$i]->norm = $resgistration_data->norm;
                $output->rows[$i]->alias_name = $resgistration_data->alias_name;
                $output->rows[$i]->name = $resgistration_data->name;
                $output->rows[$i]->is_new = $resgistration_data->is_new;
                $output->rows[$i]->birth_date = $resgistration_data->birth_date;
                $output->rows[$i]->age = $resgistration_data->age;
                $output->rows[$i]->sex_name = $resgistration_data->sex_name;
                $output->rows[$i]->health_facility_name = $resgistration_data->health_facility_name;
                $output->rows[$i]->health_facility_item_name = $resgistration_data->health_facility_item_name;
                $output->rows[$i]->doctor_in_charge_name = $resgistration_data->doctor_in_charge_name;
                $output->rows[$i]->queue = $resgistration_data->queue;
                $no=1;
                if ($physiotherapy_request_item != []) {
                    for ($j=0; $j < count($physiotherapy_request_item); $j++) { 
                        $output->rows[$i]->physiotherapy_request_item[$j]['no'] = $no;
                        $output->rows[$i]->physiotherapy_request_item[$j]['id'] = $physiotherapy_request_item[$j]->id;
                        $output->rows[$i]->physiotherapy_request_item[$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
                        $output->rows[$i]->physiotherapy_request_item[$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
                        $output->rows[$i]->physiotherapy_request_item[$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
                        $output->rows[$i]->physiotherapy_request_item[$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
                        $output->rows[$i]->physiotherapy_request_item[$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
                        $output->rows[$i]->physiotherapy_request_item[$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
                        $output->rows[$i]->physiotherapy_request_item[$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
                        $output->rows[$i]->physiotherapy_request_item[$j]['date_request'] = $physiotherapy_request_item[$j]->date_request;
                        $output->rows[$i]->physiotherapy_request_item[$j]['date_process'] = $physiotherapy_request_item[$j]->date_process;
                        $output->rows[$i]->physiotherapy_request_item[$j]['date_result'] = $physiotherapy_request_item[$j]->date_result;
                        $output->rows[$i]->physiotherapy_request_item[$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                        $output->rows[$i]->physiotherapy_request_item[$j]['status'] = $physiotherapy_request_item[$j]->status;
                        $no++;
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

    public function history_v2_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $where_main = [];

        $norm = $q_strings['norm'] ?? null;

        if ($norm == null) {
            $registration_destination_id = $q_strings['registration_destination_id'] ?? null;
            if ($registration_destination_id == null) {
                $this->REST_Return(400, 'registration_destination_id is required if norm is not provided');
                return;
            }
            $where_main['s.registration_destination_id'] = $registration_destination_id;
        } else {
            $where_main['r.norm'] = $norm;
        }

        $limit = $q_strings['limit'] ?? 10; // FIXME - HARDCODE LIMIT SEMENTARA
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        unset($q_strings['limit']);
        
        $this->$model->offset = $offset; 
        unset($q_strings['offset']);
    
        $select = 'a.id, a.no_request, a.date_request, a.status, s.id as soap_id, a.doctor_in_charge_id, e.fullname as doctor_in_charge_name, a.queue,
        rd.health_facility_item_id,
        hfi.name as health_facility_item_name,
        hfi.health_facility_id,
        hf.name as health_facility_name
        ';
        $where_main['a.is_delete'] = 0;
        $join_main = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
        );
        $order_main = array('a.date_request' => 'DESC');
        $output = $this->Physiotherapy_request->select(0, $select, $where_main, $join_main, $order_main);

        for ($i=0; $i < count($output); $i++) { 
            $physiotherapy_request_id = $output[$i]->id;

            $select = 'a.id, a.physiotherapy_request_id, a.physiotherapy_package_id, a.service_class_id, c.name as physiotherapy_package_name';
            $where = array(
                'a.physiotherapy_request_id' => $physiotherapy_request_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('physiotherapy_package pp', 'a.physiotherapy_package_id = pp.id', 'INNER'),
                array('costing c', 'pp.costing_id = c.id', 'INNER'),
            );
            $physiotherapy_request_items = $this->Physiotherapy_request_item->select(0, $select, $where, $join);

            $output[$i]->physiotherapy_request_items = $physiotherapy_request_items;
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select_main = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select_main, $where_main, $join_main)[0]->iTotal;   

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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
            // $this->$model->whereLike = $q_strings;
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Registration->whereFields['b.id'] = $output->rows[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                $insurance_data = $this->Registration_insurance->read($output->rows[$i]->registration_destination_id, false)[0];
                $physiotherapy_request_item = $this->Physiotherapy_request_item->read($output->rows[$i]->id);
                $output->rows[$i]->code = $resgistration_data->code;
                $output->rows[$i]->register_date = $resgistration_data->register_date;
                $output->rows[$i]->insurance_name = $insurance_data->insurance_name;
                $this->Registration_insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
                $this->Registration_insurance->whereFields['a.is_primary'] = 1;
                $this->Registration_insurance->whereFields['a.registration_destination_id'] = $output->rows[$i]->registration_destination_id;
                $reg_insurance = $this->Registration_insurance->read();
                for ($j=0; $j < count($reg_insurance); $j++) { 
                    $output->rows[$i]->registration_insurance[$j] = $reg_insurance[$j];
                }

                // count age
                $age = (date('Y') - date('Y', strtotime($resgistration_data->birth_date))); // Get the current year and the birth year
                if (date('md', strtotime($resgistration_data->birth_date)) > date('md')) { $age--; } // Check if the birthday has occurred this year or not (to adjust the age)
                                
                $output->rows[$i]->type_how_to_enter_name = $resgistration_data->type_how_to_enter_name;
                $output->rows[$i]->exit_name = $resgistration_data->exit_name;
                $output->rows[$i]->norm = $resgistration_data->norm;
                $output->rows[$i]->alias_name = $resgistration_data->alias_name;
                $output->rows[$i]->name = $resgistration_data->name;
                $output->rows[$i]->is_new = $resgistration_data->is_new;
                $output->rows[$i]->birth_date = $resgistration_data->birth_date;
                $output->rows[$i]->age = $age;
                $output->rows[$i]->sex_name = $resgistration_data->sex_name;
                $output->rows[$i]->health_facility_name = $resgistration_data->health_facility_name;
                $output->rows[$i]->health_facility_item_name = $resgistration_data->health_facility_item_name;
                $output->rows[$i]->doctor_in_charge_name = $resgistration_data->doctor_in_charge_name;
                $output->rows[$i]->queue = $resgistration_data->queue;
                $no=1;
                if ($physiotherapy_request_item != []) {
                    for ($j=0; $j < count($physiotherapy_request_item); $j++) { 
                        $output->rows[$i]->physiotherapy_request_item[$j]['no'] = $no;
                        $output->rows[$i]->physiotherapy_request_item[$j]['id'] = $physiotherapy_request_item[$j]->id;
                        $output->rows[$i]->physiotherapy_request_item[$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
                        $output->rows[$i]->physiotherapy_request_item[$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
                        $output->rows[$i]->physiotherapy_request_item[$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
                        $output->rows[$i]->physiotherapy_request_item[$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
                        $output->rows[$i]->physiotherapy_request_item[$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
                        $output->rows[$i]->physiotherapy_request_item[$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
                        $output->rows[$i]->physiotherapy_request_item[$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
                        $output->rows[$i]->physiotherapy_request_item[$j]['date_request'] = $physiotherapy_request_item[$j]->date_request;
                        $output->rows[$i]->physiotherapy_request_item[$j]['date_process'] = $physiotherapy_request_item[$j]->date_process;
                        $output->rows[$i]->physiotherapy_request_item[$j]['date_result'] = $physiotherapy_request_item[$j]->date_result;
                        $output->rows[$i]->physiotherapy_request_item[$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                        $output->rows[$i]->physiotherapy_request_item[$j]['status'] = $physiotherapy_request_item[$j]->status;
                        $no++;
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
     * Physiotherapy_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/mr/physiotherapy_request
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
            $this->form_validation->set_rules('physiotherapy_request_items[]', 'physiotherapy_request_items', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;

                $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
                if ($doctor_in_charge_id != null) {
                    $employee = $this->Employee->select_id($doctor_in_charge_id);
                    
                    if ($employee == "") {
                        $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
                        return;
                    }
                }

                /* Start Transaction */
                $this->db->trans_start();
                $physiotherapy_request = $this->$model->read($id);
                if($physiotherapy_request != []) {
                    $dt_physiotherapy_request = array(
                        'id'              => $id,
                        'soap_id'         => $dt_put['soap_id'],
                        'date_request'    => $dt_put['date_request'],
                    );
                    $this->$model->update($id, $dt_physiotherapy_request);

                    $this->Physiotherapy_request_item->whereFields['physiotherapy_request_id'] = $id;
                    $physiotherapy_request_item = $this->Physiotherapy_request_item->read(0,false);
                    $package_arr = $dt_put['physiotherapy_request_items'];
                    $tmp_delete_package_arr = array();

                    // adding all current items (db) to temporary variable
                    for ($i=0; $i < count($physiotherapy_request_item); $i++) { 
                        $tmp_delete_package_arr[$physiotherapy_request_item[$i]->physiotherapy_package_id] = $physiotherapy_request_item[$i]->id;
                    }

                    // delete all items if no item provided
                    if(count($dt_put['physiotherapy_request_items']) == 0) {
                        for ($i=0; $i < count($physiotherapy_request_item); $i++) { 
                            if ($physiotherapy_request_item[$i]->id) {
                                $dt_physiotherapy_request_item = array(
                                    'id'                        => $physiotherapy_request_item[$i]->id,
                                    'physiotherapy_request_id'  => $physiotherapy_request_item[$i]->physiotherapy_request_id,
                                    'physiotherapy_package_id'  => $physiotherapy_request_item[$i]->physiotherapy_package_id,
                                    'is_delete'                 => 1,
                                );
                                $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
                            }
                        }
                    }

                    for ($i=0; $i < count($dt_put['physiotherapy_request_items']); $i++) { 
                        for ($i=0; $i < count($package_arr) ; $i++) { 
                            $package_id = $package_arr[$i]['physiotherapy_package_id'];
                            $service_class_id = $package_arr[$i]['service_class_id'];

                            $package = $this->Physiotherapy_package->select_id($package_id);
                            if($package == "") {
                                $this->REST_Return(404, 'There is no physiotherapy_package with id ' . $package_id);
                                return;
                            }

                            $service_class = $this->Service_class->select_id($service_class_id);
                            if($service_class == "") {
                                $this->REST_Return(404, 'There is no service_class with id ' . $service_class_id);
                                return;
                            }

                            $select = "a.costing_id";
                            $costing_id = $this->Physiotherapy_package->select($package_id, $select)[0]->costing_id;
                            $costing = $this->Costing->read($costing_id,false);

                            $this->Costing_services_class->whereFields['costing_id'] = $costing_id;
                            $costing_services_class = $this->Costing_services_class->read(0,false);
                            
                            $service_class = $this->Service_class->read($service_class_id,false);

                            $dt_physiotherapy_request_item = array(
                                'physiotherapy_request_id'  => $id,
                                'physiotherapy_package_id'  => $package_id,
                                'service_class_id'          => $service_class[0]->id,
                                'var_1'                     => $costing_services_class[0]->var_1,
                                'var_2'                     => $costing_services_class[0]->var_2,
                                'var_3'                     => $costing_services_class[0]->var_3,
                                'costing_id'                => $costing[0]->id,
                                'costing_name'              => $costing[0]->name,
                                'status'                    => $dt_put['physiotherapy_request_items'][$i]['status'],
                                'is_delete'                 => 0
                            );
    
                            // create or update items according to the items provided and unset used items from temporary variable
                            if(array_key_exists($package_id,$tmp_delete_package_arr) == 1) {
                                $dt_physiotherapy_request_item['id'] = $tmp_delete_package_arr[$package_id];
                                $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
    
                                unset($tmp_delete_package_arr[$package_id]);
                            } else {
                                $this->Physiotherapy_request_item->create($dt_physiotherapy_request_item);
                            }
                        }
                    
                        // delete all items in temporary variable
                        foreach ($tmp_delete_package_arr as $package_id => $physiotherapy_request_item_id) {
                            if ($physiotherapy_request_item_id) {
                                $dt_physiotherapy_request_item = array(
                                    'id'                        => $physiotherapy_request_item_id,
                                    'physiotherapy_request_id'     => $id,
                                    'physiotherapy_package_id'     => $package_id,
                                    'is_delete'                 => 1,
                                );
                                $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
                            }
                        }
                    }

                    // NOTE - DEPRECATED, DELETED SOON!
                    // for ($i=0; $i < count($dt_put['physiotherapy_request_items']); $i++) {
                    //     $this->Physiotherapy_request_item->whereFields['a.id'] = $dt_put['physiotherapy_request_items'][$i]['id'];
                    //     $physiotherapy_request_item = $this->Physiotherapy_request_item->read(0,false); 
                    //     if($physiotherapy_request_item != []) {
                    //         $physiotherapy_package = $this->Physiotherapy_package->read($dt_put['physiotherapy_request_items'][$i]['physiotherapy_package_id'], false);
                    //         $service_class = $this->Service_class->read($dt_put['physiotherapy_request_items'][$i]['service_class_id'],false);
                    //         if($physiotherapy_package != [] && $service_class != []) {
                    //             $costing = $this->Costing->read($physiotherapy_package[0]->costing_id,false);
                    //             $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                    //             $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                    //             $costing_services_class = $this->Costing_services_class->read(0,false);
                    //             $dt_physiotherapy_request_item = array(
                    //                 'id'                        => $dt_put['physiotherapy_request_items'][$i]['id'],
                    //                 'physiotherapy_request_id'  => $id,
                    //                 'physiotherapy_package_id'   => $dt_put['physiotherapy_request_items'][$i]['physiotherapy_package_id'],
                    //                 'service_class_id'          => $service_class[0]->id,
                    //                 'var_1'                     => $costing_services_class[0]->var_1,
                    //                 'var_2'                     => $costing_services_class[0]->var_2,
                    //                 'var_3'                     => $costing_services_class[0]->var_3,
                    //                 'costing_id'                => $costing[0]->id,
                    //                 'costing_name'              => $costing[0]->name,
                    //                 'notes'                     => $dt_put['physiotherapy_request_items'][$i]['notes'],
                    //                 'status'                    => $dt_put['physiotherapy_request_items'][$i]['status'],
                    //             );
                    //             $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
                    //         } else {
                    //             $this->REST_Return(404, 'There is no physiotherapy_package with id ' . $dt_put['physiotherapy_request_items'][$i]['physiotherapy_package_id'] . ' or service_class with id ' . $dt_put['physiotherapy_request_items'][$i]['service_class_id']);
                    //             return;
                    //         }
                    //     } else {
                    //         $this->REST_Return(404, 'Physiotherapy_request_item with id ' . $dt_put['physiotherapy_request_items'][$i]['id'] . ' does not exist!');
                    //         return;
                    //     }
                    // }
                } else {
                    $this->REST_Return(404, 'Physiotherapy_request with id ' . $id . ' does not exist!');
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

    public function process_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        // $this->form_validation->set_data($dt_put);
        // $this->form_validation->set_rules($this->process_rules);
        // if ($this->form_validation->run() == FALSE){
        //     // Form Validation Errors
        //     $response_data = $this->form_validation->error_array();
        //     $this->REST_Return(422, 'Format error', $response_data);
        //     return;
        // }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $physiotherapy_request_id = $id;

        if ($physiotherapy_request_id === 0 || $physiotherapy_request_id === "0") {
            $this->REST_Return(400, 'physiotherapy_request_id is required');
            return;
        }

        $select = 'a.id, a.status';
        $physiotherapy_request = $this->$model->select($physiotherapy_request_id, $select);
        if (count($physiotherapy_request) == 0) {
            $this->REST_Return(404, 'there is no physiotherapy_request with id ' . $physiotherapy_request_id);
            return;
        }

        $status = $physiotherapy_request[0]->status;

        if ($status == 2) {
            $this->REST_Return(409, 'physioptherapy_request is already processed');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_physiotherapy_request = array(
            'status' => 2,
            'date_process' => date('Y-m-d H:i:s'),
        );

        $this->$model->update_v2($physiotherapy_request_id, $dt_physiotherapy_request);

        $dt_physiotherapy_request_item = array(
            'status' => 2,
            'date_process' => date('Y-m-d H:i:s'),
        );

        $where = array(
            'physiotherapy_request_id' => $physiotherapy_request_id,
            'is_delete' => 0,
        );
        $this->Physiotherapy_request_item->update_v2(0, $dt_physiotherapy_request_item, $where);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_physiotherapy_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update physiotherapy_request', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function verify_put($p = 0) {
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
            // $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_lab_id', 'doctor_lab_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('conclusion', 'conclusion', 'trim');
            $this->form_validation->set_rules('notes', 'notes', 'trim');
            $this->form_validation->set_rules('advice', 'advice', 'trim');
            // $this->form_validation->set_rules('physiotherapy_request_items[]', 'physiotherapy_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                // $this->Physiotherapy_request_item->whereFields['a.id'] = $id;
                $physiotherapy_request = $this->Physiotherapy_request->read($id);
                if($physiotherapy_request) {
                    $dt_physiotherapy_request = array(
                        'id'              => $id,
                        'date_result'     => date('Y-m-d H:i:s'),
                        'doctor_lab_id'   => $dt_put['doctor_lab_id']['value'],
                        'user_lab_id'     => $dt_put['user_lab_id']['value'],
                        'conclusion'      => $dt_put['conclusion'],
                        'notes'           => $dt_put['notes'],
                        'advice'          => $dt_put['advice'],
                        'status'          => 3,
                    );
                    $this->Physiotherapy_request->update($id,$dt_physiotherapy_request);
                    $outputdata = $this->Physiotherapy_request_item->read($id);
                    if($outputdata){
                        for ($i=0; $i < count($outputdata); $i++) { 
                            $dt_physiotherapy_request_item = array(
                                'id'             => $outputdata[$i]->id,
                                'date_result'    => date('Y-m-d H:i:s'),
                            );
                            $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Physiotherapy_request with id ' . $id . ' does not exist!');
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
            // }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}