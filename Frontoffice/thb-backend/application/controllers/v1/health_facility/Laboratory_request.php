<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Laboratory_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Laboratory_package_model", "Laboratory_package");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Registration_insurance_model", "Registration_insurance");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/hr/Position_model", "Position");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/Display_queue_model", "Queue_display");
        $this->load->model("v1/setting/Queue_status_model", "Queue_status");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        // Load Helper
        $this->load->helper('queue');

        $this->create_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim|required'),
            array('field' => 'laboratory_request_items[]', 'label' => 'laboratory_request_items', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->queue_next_rules = array(
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );
    }

    /**
     * Laboratory_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/health_facility/laboratory_request
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
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        $date_request = $dt_post['date_request'];
        $laboratory_request_items_arr = $dt_post['laboratory_request_items'];

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }
        
        if ($soap_id == null) {
            $registration_destination_id = $dt_post['registration_destination_id'];
            
            if ($registration_destination_id == null) {
                $this->REST_Return(404, 'field registration_destination_id is required if soap_id isn\'t provided');
                return;
            }

            $select = 'a.id';
            $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

            if (count($registration_destination) == 0) {
                $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
                return;
            }

            $select = 'a.id';
            $where = array('a.registration_destination_id' => $registration_destination_id);
            $soap = $this->Soap->select(0, $select, $where);

            if (count($soap) == 0) {
                // CREATE SOAP MANDATORY
                $dt_soap = array(
                    'registration_destination_id'  => $registration_destination_id,
                    'doctor_in_charge_id'          => $doctor_in_charge_id,
                    'user_added'                   => $user_id,
                    'date_added'                   => date("Y-m-d H:i:s", time())
                );
        
                $soap_id = $this->Soap->create($dt_soap);
            } else {
                $soap_id = $soap[0]->id;
            }

        } else {
            $soap = $this->Soap->select_id($soap_id);

            if ($soap == "") {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
        }

        // count no_request & queue
        $prefix = 'LAB';
        $epoch = strtotime($date_request);
        $dt_queue = array(
            'var_2' => date('Y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('Y-m-d', $epoch),
        );

        $no_request = counter_queue($prefix, $dt_queue, 4);
        $queue = round((int)substr($no_request, -4));

        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        
        if ($doctor_in_charge_id != null) {
            $employee = $this->Employee->select_id($doctor_in_charge_id);
            
            if ($employee == "") {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
                return;
            }
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_laboratory_request = array(
            'institute_branch_id'   => $institute_branch_id,
            'soap_id'               => $soap_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'no_request'            => $no_request,
            'queue'                 => $queue,
            'date_request'          => $date_request
        );
        $id = $this->$model->create($dt_laboratory_request);
    
        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_laboratory_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create laboratory_request', $response_data);
            return;
        }

        for ($i=0; $i < count($laboratory_request_items_arr) ; $i++) {
            if (!isset($laboratory_request_items_arr[$i]['laboratory_package_id'])) {
                $this->REST_Return(400, "laboratory_package_id field in laboratory_request_items is required!");
                return;
            }

            if (!isset($laboratory_request_items_arr[$i]['service_class_id'])) {
                $this->REST_Return(400, "service_class_id field in laboratory_request_items is required!");
                return;
            }

            $laboratory_package_id = $laboratory_request_items_arr[$i]['laboratory_package_id'];
            $service_class_id = $laboratory_request_items_arr[$i]['service_class_id'];

            $laboratory_package = $this->Laboratory_package->read($laboratory_package_id, false);
            $service_class = $this->Service_class->read($service_class_id, false);

            if ($laboratory_package == []) {
                $this->REST_Return(404, "there is no laboratory_package with id " . $laboratory_package_id);
                return;
            }

            if ($service_class == []) {
                $this->REST_Return(404, "there is no service_class with id " . $service_class_id);
                return;
            }

            $costing_id = $laboratory_package[0]->costing_id;

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
            
            $dt_laboratory_request_item = array(
                'laboratory_request_id'     => $id,
                'laboratory_package_id'     => $laboratory_package_id,
                'service_class_id'          => $service_class_id,
                'var_1'                     => $costing_services_class_var_1,
                'var_2'                     => $costing_services_class_var_2,
                'var_3'                     => $costing_services_class_var_3,
                'costing_id'                => $costing_id,
                'costing_name'              => $costing_name,
                'date_request'              => $date_request,
                'status'                    => 1
            );
            $this->Laboratory_request_item->create($dt_laboratory_request_item);

            $this->db->trans_complete();
            /* End Transaction */

            if ($this->db->trans_status() === false) {
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_laboratory_request_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to create laboratory_request_item', $response_data);
                return;
            }
        }
                    
        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;            
    }

    /**
     * GET Laboratory_request with API
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
            if(isset($q_strings['doctor_in_charge_id'])) $this->$model->whereFields['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id']; unset($this->$model->whereFields['doctor_in_charge_id']);
            $this->$model->whereFields['a.is_delete'] = 0;
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $this->Registration->whereFields['b.id'] =  $output[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                $this->Laboratory_request_item->whereFields['a.is_delete'] = 0;
                $laboratory_request_item = $this->Laboratory_request_item->read($output[$i]->id);
                $output[$i]->regData = $resgistration_data;
                $no=1;
                if ($laboratory_request_item != []) {
                    for ($j=0; $j < count($laboratory_request_item); $j++) { 
                        $output[$i]->laboratory_request_item[$j]['no'] = $no;
                        $output[$i]->laboratory_request_item[$j]['id'] = $laboratory_request_item[$j]->id;
                        $output[$i]->laboratory_request_item[$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
                        $output[$i]->laboratory_request_item[$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;                        
                        $output[$i]->laboratory_request_item[$j]['service_class_id'] = $laboratory_request_item[$j]->service_class_id;
                        $output[$i]->laboratory_request_item[$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
                        $output[$i]->laboratory_request_item[$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
                        $output[$i]->laboratory_request_item[$j]['var_1'] = $laboratory_request_item[$j]->var_1;
                        $output[$i]->laboratory_request_item[$j]['var_2'] = $laboratory_request_item[$j]->var_2;
                        $output[$i]->laboratory_request_item[$j]['var_3'] = $laboratory_request_item[$j]->var_3;
                        $output[$i]->laboratory_request_item[$j]['date_request'] = $laboratory_request_item[$j]->date_request;
                        $output[$i]->laboratory_request_item[$j]['date_process'] = $laboratory_request_item[$j]->date_process;
                        $output[$i]->laboratory_request_item[$j]['date_result'] = $laboratory_request_item[$j]->date_result;
                        $output[$i]->laboratory_request_item[$j]['notes'] = $laboratory_request_item[$j]->notes;
                        $output[$i]->laboratory_request_item[$j]['url'] = $laboratory_request_item[$j]->url;
                        $output[$i]->laboratory_request_item[$j]['status'] = $laboratory_request_item[$j]->status;
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

    public function result_get($p = 0) {
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

        $user_id = $this->security->xss_clean($is_valid_token['data']->id); 

        if ($p == null) {
            $this->REST_Return(400, 'id is required');
            return;
        }
    
        $laboratory_request_id = $p;

        $select = 'a.id, a.date_request, a.status, rd.health_facility_item_id, hfi.health_facility_id, r.norm, p.name as patient_name, h.name as patient_alias';
        $where = array();
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', 'p.alias = h.code and h.variable="alias"', 'INNER'),
        );
        $laboratory_request = $this->$model->select($laboratory_request_id, $select, $where, $join);
        if (count($laboratory_request) == 0) {
            $this->REST_Return(404, 'there is no laboratory_request with id ' . $laboratory_request_id);
            return;
        }

        $status = $laboratory_request[0]->status;

        $resultx = array();
        $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, lp.category, a.service_class_id, lp.code_lis, a.status, a.url';
        $where = array(
            'a.laboratory_request_id' => $laboratory_request_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
            array('category_package cp', 'lp.category = cp.name AND cp.package = "LABORATORY"', 'LEFT'),
        );
        $order_main = array('cp.index' => 'ASC');
        $laboratory_request_items = $this->Laboratory_request_item->select(0, $select, $where, $join, $order);

        // LIS RESULT
        $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

        if (count($lis_response) > 0) {
            $result = $lis_response[0]['result_detail'];

            for ($k=0; $k < count($laboratory_request_items); $k++) { 
                $code_lis = $laboratory_request_items[$k]->code_lis;
                $laboratory_request_items[$k]->result = [];

                for ($l=0; $l < count($result); $l++) { 
                    $flag = $result[$l]['flag'];
                    $parameter_id = $result[$l]['ParameterID'];

                    if ($parameter_id == $code_lis && $result[$l]['Parameter'] != "") {
                        array_push($resultx, $result[$l]);
                        array_push($laboratory_request_items[$k]->result, $result[$l]);
                    }
                }
            }

            if ($status != 3) {
                $health_facility_item_id = $laboratory_request[0]->health_facility_item_id;
                $health_facility_id = $laboratory_request[0]->health_facility_id;
                $date_request = date('Y-m-d',strtotime($laboratory_request[0]->date_request));
                $patient_name = trim($laboratory_request[0]->patient_name);
                $patient_alias = $laboratory_request[0]->patient_alias;
                $norm = $laboratory_request[0]->norm;

                // Hanya pesanan laboratorium dari Rawat Inap yang masuk
                if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                    // TODO - BENERIN MASTER JABATAN
                    // ex. hfi ICU ISOLASI ga ada POSITION
                    $select = 'a.id';
                    $where = array(
                        'a.health_facility_item_id' => $health_facility_item_id,
                    );
                    $position = $this->Position->select(0, $select, $where);
                    if (count($position) > 0) {
                        /* Start Transaction */
                        $this->db->trans_start();
        
                        $dt_laboratory_request = array(
                            'date_result'     => date('Y-m-d H:i:s'),
                            'user_lab_id'     => $user_id,
                            'status'          => 3,
                        );
        
                        $this->$model->update_v2($laboratory_request_id, $dt_laboratory_request);

                        $position_id = $position[0]->id;

                        $title = 'Hasil Laboratorium';
                        $message = "Hasil laboratorium tanggal $date_request pasien $patient_alias $patient_name ($norm) sudah terbit";
                        $this->Notifications->createNotification(null, $position_id, $title, $message, 'lab_result', ['laboratory_request_id' => $laboratory_request_id]);
        
                        $this->db->trans_complete();
                        /* End Transaction */
        
                        if ($this->db->trans_status() === false) {
                            $response_data = array(
                                'error'     => $this->db->error(),
                                'data'      => $dt_laboratory_request,
                                'query'     => $this->db->last_query()
                            );
                            $this->REST_Return(500, 'error while trying to update laboratory_request', $response_data);
                            return;
                        }
                    }
                }
            }
        }
        
        $resultz = array(
            'result_detail' => array_values($resultx),
            'result_new'=> $laboratory_request_items
        );

        $this->REST_Return(200, 'SUCCESS', array($resultz));
        return;
    }

    public function bride_lis($url = null,$param = null) {
        // url : 192.168.100.153/api_lis/index.php
        $url_acc   = '192.168.100.153';
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        // $authorization[] = "Authorization:Bearer ".$token; 
        // $authorization[] = "X-Session-ID:".$SESSIONID; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json;charset=utf-8"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
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
            if ($q_strings['registration_id'] && $q_strings['registration_id'] != null && $q_strings['registration_id'] != ''){
                $this->$model->whereFields['rd.registration_id'] = $q_strings['registration_id']; unset($q_strings['registration_id']);
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
                $this->Registration->whereFields['b.id'] =  $output->rows[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                $insurance_data = $this->Registration_insurance->read($output->rows[$i]->registration_destination_id, false)[0];
                $this->Laboratory_request_item->whereFields['a.is_delete'] = 0;
                $laboratory_request_item = $this->Laboratory_request_item->read($output->rows[$i]->id);
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
                if ($laboratory_request_item != []) {
                    for ($j=0; $j < count($laboratory_request_item); $j++) { 
                        $laboratory_request_id = $laboratory_request_item[$j]->laboratory_request_id;

                        $output->rows[$i]->laboratory_request_item[$j]['no'] = $no;
                        $output->rows[$i]->laboratory_request_item[$j]['id'] = $laboratory_request_item[$j]->id;
                        $output->rows[$i]->laboratory_request_item[$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
                        $output->rows[$i]->laboratory_request_item[$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;
                        $output->rows[$i]->laboratory_request_item[$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
                        $output->rows[$i]->laboratory_request_item[$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
                        $output->rows[$i]->laboratory_request_item[$j]['var_1'] = $laboratory_request_item[$j]->var_1;
                        $output->rows[$i]->laboratory_request_item[$j]['var_2'] = $laboratory_request_item[$j]->var_2;
                        $output->rows[$i]->laboratory_request_item[$j]['var_3'] = $laboratory_request_item[$j]->var_3;
                        $output->rows[$i]->laboratory_request_item[$j]['date_request'] = $laboratory_request_item[$j]->date_request;
                        $output->rows[$i]->laboratory_request_item[$j]['date_process'] = $laboratory_request_item[$j]->date_process;
                        $output->rows[$i]->laboratory_request_item[$j]['date_result'] = $laboratory_request_item[$j]->date_result;
                        $output->rows[$i]->laboratory_request_item[$j]['notes'] = $laboratory_request_item[$j]->notes;
                        $output->rows[$i]->laboratory_request_item[$j]['status'] = $laboratory_request_item[$j]->status;
                        $output->rows[$i]->laboratory_request_item[$j]['result'] = [];
                        $no++;

                        // LIS RESULT
                        $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                        $code_lis = $laboratory_request_item[$j]->code_lis;
    
                        if (count($lis_response) > 0) {
                            $result = $lis_response[0]['result_detail'];
    
                            for ($l=0; $l < count($result); $l++) { 
                                $flag = $result[$l]['flag'];
                                $parameter_id = $result[$l]['ParameterID'];
    
                                // keluarin semua hasil lab
                                // $flag != '' && 
                                if ($parameter_id == $code_lis) {
                                    array_push($output->rows[$i]->laboratory_request_item[$j]['result'], $result[$l]);
                                }
                            }
                        }
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
            $registration_id = $q_strings['registration_id'] ?? null;
            if ($registration_destination_id == null) {
                if ($registration_id == null) {
                    $this->REST_Return(400, 'registration_id is required if registration_destination_id is not provided');
                    return;
                } else {
                    $where_main['rd.registration_id'] = $registration_id;
                    $where_main['a.status !='] = 1;
                }
            } else {
                $where_main['s.registration_destination_id'] = $registration_destination_id;
            }
        } else {
            $where_main['r.norm'] = $norm;
        }

        $limit = $q_strings['limit'] ?? 10; // FIXME - HARDCODE LIMIT SEMENTARA
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        unset($q_strings['limit']);
        
        $this->$model->offset = $offset; 
        unset($q_strings['offset']);
    
        $select = 'a.id, a.no_request, a.date_request, a.date_result, a.status, s.id as soap_id, a.doctor_in_charge_id, e.fullname as doctor_in_charge_name, a.queue, a.status,
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
        $output = $this->Laboratory_request->select(0, $select, $where_main, $join_main, $order_main);

        for ($i=0; $i < count($output); $i++) { 
            $laboratory_request_id = $output[$i]->id;

            $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis, a.status, a.url';
            $where = array(
                'a.laboratory_request_id' => $laboratory_request_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
            );
            $laboratory_request_items = $this->Laboratory_request_item->select(0, $select, $where, $join);

            // LIS RESULT
            $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

            for ($k=0; $k < count($laboratory_request_items); $k++) { 
                $code_lis = $laboratory_request_items[$k]->code_lis;
                $laboratory_request_items[$k]->result = [];

                if (count($lis_response) > 0) {
                    $result = $lis_response[0]['result_detail'];

                    for ($l=0; $l < count($result); $l++) { 
                        $flag = $result[$l]['flag'];
                        $parameter_id = $result[$l]['ParameterID'];

                        if ($parameter_id == $code_lis) {
                            array_push($laboratory_request_items[$k]->result, $result[$l]);
                        }
                    }
                }
                if ($laboratory_request_items[$k]->url) {
                    array_push($laboratory_request_items[$k]->result, array(
                        "ParameterID"=> "9999",
                        "id_lis"=> "9999",
                        "Parameter"=> "-",
                        "hasil"=> "-",
                        "flag"=> "-",
                        "satuan"=> "-",
                        "metoda"=> "",
                        "n_rujukan"=> ""
                    ));
                }

            }
            

            $output[$i]->laboratory_request_items = $laboratory_request_items;
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
            if(isset($q_strings['norm'])) $this->$model->whereFields['p.norm'] = $q_strings['norm'];
            if(isset($q_strings['name'])) $this->$model->whereLike['p.name'] = $q_strings['name']; unset($q_strings['name']);
            // $this->$model->whereLike = $q_strings;
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $this->$model->whereFields['a.date_request >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date_request <='] = $q_strings['date_end'].' 23:59:59';
            } else {
                $q_strings['date_start'] = date('Y-m-d');
                $q_strings['date_end'] = date('Y-m-d');
                $this->$model->whereFields['a.date_request >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date_request <='] = $q_strings['date_end'].' 23:59:59';
            }
            $this->Laboratory_request->whereFields['a.is_delete'] = 0;
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Registration->whereFields['b.id'] =  $output->rows[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                // print_r($resgistration_data);
                // die();
                // $insurance_data = $this->Registration_insurance->read($output->rows[$i]->registration_destination_id, false)[0];
                $this->Laboratory_request_item->whereFields['a.is_delete'] = 0;
                $laboratory_request_item = $this->Laboratory_request_item->read($output->rows[$i]->id);
                $output->rows[$i]->code = $resgistration_data->code;
                $output->rows[$i]->register_date = $resgistration_data->register_date;
                // $output->rows[$i]->insurance_name = $insurance_data->insurance_name;
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
                $output->rows[$i]->idcard = $resgistration_data->idcard;
                $output->rows[$i]->name = $resgistration_data->name;
                $output->rows[$i]->is_new = $resgistration_data->is_new;
                $output->rows[$i]->birth_date = $resgistration_data->birth_date;
                $output->rows[$i]->age = $age;
                $output->rows[$i]->sex_name = $resgistration_data->sex_name;
                $output->rows[$i]->health_facility_name = $resgistration_data->health_facility_name;
                $output->rows[$i]->health_facility_item_name = $resgistration_data->health_facility_item_name;
                // $output->rows[$i]->doctor_in_charge_name = $resgistration_data->doctor_in_charge_name;
                $output->rows[$i]->queue = $resgistration_data->queue;
                $no=1;
                if ($laboratory_request_item != []) {
                    for ($j=0; $j < count($laboratory_request_item); $j++) { 
                        $output->rows[$i]->laboratory_request_item[$j]['no'] = $no;
                        $output->rows[$i]->laboratory_request_item[$j]['id'] = $laboratory_request_item[$j]->id;
                        $output->rows[$i]->laboratory_request_item[$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
                        $output->rows[$i]->laboratory_request_item[$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;
                        $output->rows[$i]->laboratory_request_item[$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
                        $output->rows[$i]->laboratory_request_item[$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
                        $output->rows[$i]->laboratory_request_item[$j]['var_1'] = $laboratory_request_item[$j]->var_1;
                        $output->rows[$i]->laboratory_request_item[$j]['var_2'] = $laboratory_request_item[$j]->var_2;
                        $output->rows[$i]->laboratory_request_item[$j]['var_3'] = $laboratory_request_item[$j]->var_3;
                        $output->rows[$i]->laboratory_request_item[$j]['date_request'] = $laboratory_request_item[$j]->date_request;
                        $output->rows[$i]->laboratory_request_item[$j]['date_process'] = $laboratory_request_item[$j]->date_process;
                        $output->rows[$i]->laboratory_request_item[$j]['date_result'] = $laboratory_request_item[$j]->date_result;
                        $output->rows[$i]->laboratory_request_item[$j]['notes'] = $laboratory_request_item[$j]->notes;
                        $output->rows[$i]->laboratory_request_item[$j]['status'] = $laboratory_request_item[$j]->status;
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

    public function list_fast_get($p = 0){
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
    
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit;
            unset($q_strings['limit']);
        }
        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset;
            unset($q_strings['offset']);
        }

        $where = $q_strings;

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        $select = 'a.id, 
        a.no_request, 
        a.clinical_indications, 
        rd.id as registration_destination_id,
        rd.health_facility_item_id,
        rd.exit_id,
        rd.exit_reason as exit_name,
        hfi.name as health_facility_item_name,
        r.register_date,
        r.how_to_enter_id,
        hte.name as type_how_to_enter_name,
        a.date_request, 
        a.date_process, 
        a.date_result, 
        a.doctor_in_charge_id, 
        e.fullname as doctor_in_charge_name, 
        b.id as bed_id, 
        b.name as bed_name, 
        br.id as room_id,
        br.name as room_name, 
        p.norm,
        p.idcard,
        p.is_new,
        h2.name as alias_name,
        p.fullname as name,
        h.name as sex_name,
        p.birth_date,
        a.queue, 
        a.status';

        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.date_request >='] = $date_start;
        $where['a.date_request <='] = $date_end;
        
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room br', 'b.room_id = br.id', 'LEFT'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('how_to_enter hte', 'r.how_to_enter_id = hte.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', 'p.sex = h.code and h.variable="sex"', 'INNER'),
            array('helper h2', 'p.alias = h2.code and h2.variable="alias"', 'INNER'),
        );

        $where['a.is_delete'] = 0;

        $output = $this->$model->select(0, $select, $where, $join);

        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_id = $output[$i]->registration_destination_id;
            $birth_date = $output[$i]->birth_date;

            $select = 'a.registration_destination_id,
            a.insurance_id,
            a.company_id,
            a.number_insurance,
            a.service_class_id,
            a.service_class_name,
            c.name as company_name,
            h.name as insurance_name';
            $where_insu = array(
                'registration_destination_id' => $registration_destination_id,
                'is_primary' => 1,
            );
            $join_insu = array(
                array('company c', 'a.company_id = c.id', 'LEFT'),
                array('helper h', 'a.insurance_id = h.code and variable="insurance"', 'INNER'),
            );
            $registration_insurance = $this->Registration_insurance->select(0, $select, $where_insu, $join_insu);

            $output[$i]->registration_insurance = $registration_insurance;

            $age = (date('Y') - date('Y', strtotime($birth_date))); // Get the current year and the birth year
            if (date('md', strtotime($birth_date)) > date('md')) { $age--; } // Check if the birthday has occurred this year or not (to adjust the age)
            $output[$i]->age = $age;

            $date_request = $output[$i]->date_request;
            $queue = $output[$i]->queue;

            $select_queue = 'a.status, a.data';
            $where_queue = array(
                'a.name' => 'LAB',
                'a.date' => date('Y-m-d', strtotime($date_request)),
            );
            $queue_display = $this->Queue_display->select(0, $select_queue, $where_queue);

            $output[$i]->status_display = 0;

            for ($j=0; $j < count($queue_display); $j++) { 
                $queue_display_data = explode(',', $queue_display[$j]->data);
                $queue_display_status = $queue_display[$j]->status;
                $queue_display_status = $queue_display[$j]->status;

                if (in_array($queue, $queue_display_data) == 1) {
                    $output[$i]->status_display = $queue_display_status;
                }
            }
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;
        
		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
    public function queue_get($p = 0){
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

        if (!isset($q_strings['date'])) {
            $this->REST_Return(400, 'date is required');
            return;
        }

        $code_prefix = 'LAB';

        $date = $q_strings['date'];

        $date_start = $date . ' 00:00:00';
        $date_end = $date . ' 23:59:59';

        $select_lab = 'a.no_request, a.queue';
        $where_lab = array(
            'a.is_delete' => 0,
            'a.date_request >=' => $date_start,
            'a.date_request <=' => $date_end,
            'hfi.health_facility_id' => '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        );
        $join_lab = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
        );
        $order_lab = array('a.queue' => 'ASC');
        $laboratory_request = $this->$model->select(0, $select_lab, $where_lab, $join_lab, $order_lab);

        $data_queue = [];

        for ($i=0; $i < count($laboratory_request); $i++) { 
            $queue_number = $laboratory_request[$i]->queue;
            array_push($data_queue, $queue_number);
        }

        $select_queue = 'a.name, a.data, a.status';
        $where_queue = array(
            'a.name' => $code_prefix,
            'a.date' => $date,
        );
        $join_queue = array();
        $order_queue = array('a.status' => 'ASC');
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue, $join_queue, $order_queue);

        $this->db->trans_start();

        // Create queue if queue does'nt exist
        if (count($queue) == 0) {
            $select = 'a.code, a.name, a.status';
            $where = array(
                'a.code' => $code_prefix,
                'a.is_delete' => 0
            );
            $queue_status = $this->Queue_status->select(0, $select, $where);

            $dt_queue = array(
                'name' => $code_prefix,
                'data' => implode(',', $data_queue),
                'date' => $date,
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            );

            for ($i=0; $i < count($queue_status); $i++) { 
                $queue_status_num = $queue_status[$i]->status;

                $dt_queue['status'] = $queue_status_num;

                if ($i != 1) {
                    $dt_queue['data'] = '';
                }

                $this->Queue_display->replace($dt_queue);
            }

            $queue = $this->Queue_display->select(0, $select_queue, $where_queue);
        }

        // Insert queue to the first step (status one)
        $queue_data_status_one = ($queue[0]->data == '') ? [] : explode(',', $queue[0]->data);

        for ($i=0; $i < count($data_queue); $i++) { 
            $queue_number = $data_queue[$i];

            $add = 1;
            for ($j=0; $j < count($queue); $j++) { 
                $queue_data = ($queue[$j]->data == '') ? [] : explode(',', $queue[$j]->data);

                if (in_array($queue_number, $queue_data) == 1) {
                    $add = 0;
                }
            }

            if ($add == 1) {
                array_push($queue_data_status_one, $queue_number);
            }
        }

        $dt_queue = array(
            'name' => $code_prefix,
            'data' => implode(',', $queue_data_status_one),
            'date' => $date,
            'status' => 1,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $this->Queue_display->replace($dt_queue);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to get queue', $response_data);
            return;
        }

        // Output queue
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue);

        // Looping through status one, two, three ...
        for ($i=0; $i < count($queue); $i++) { 
            $status = $queue[$i]->status;
            $queue_number_arr = ($queue[$i]->data == '') ? [] : explode(',', $queue[$i]->data);
            $output_data = array();

            // Output additional data to queue
            for ($j=0; $j < count($queue_number_arr); $j++) { 
                $queue_number = $queue_number_arr[$j];

                $select_lab = 'a.no_request, a.queue, rd.code_queue as registration_destination_code_queue, rd.queue as registration_destination_queue';
                $where_lab = array(
                    'a.is_delete' => 0,
                    'a.queue' => $queue_number,
                    'a.date_request >=' => $date_start,
                    'a.date_request <=' => $date_end,
                );
                $join_lab = array(
                    array('soap s', 'a.soap_id = s.id', 'INNER'),
                    array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
                    array('registration r', 'rd.registration_id = r.id', 'INNER'),
                    // array('patient ptn', 'r.norm = ptn.norm', 'INNER'),
                );
                $laboratory_request = $this->$model->select(0, $select_lab, $where_lab, $join_lab);

                $no_request = $laboratory_request[0]->no_request;
                // $patient_name = $laboratory_request[0]->patient_name;
                $registration_destination_code_queue = $laboratory_request[0]->registration_destination_code_queue;
                $registration_destination_queue = $laboratory_request[0]->registration_destination_queue;

                $dt_output = array(
                    'queue' => $queue_number,
                    'no_request' => $no_request,
                    'registration_destination_queue' => $registration_destination_code_queue == null ? $registration_destination_queue: $registration_destination_code_queue . '-' . $registration_destination_queue,
                    // 'patient_name' => $patient_name,
                );

                array_push($output_data, $dt_output);
            }

            $select = 'a.name';
            $where = array(
                'a.code' => $code_prefix,
                'a.status' => $status,
                'a.is_delete' => 0,
            );
            $queue_status = $this->Queue_status->select(0, $select, $where);

            $queue_status_name = $queue_status[0]->name;

            $queue[$i]->data = $output_data;
            $queue[$i]->name = $queue_status_name;
        }

        $this->REST_Return(200, 'SUCCESS', $queue);
        return;
    }

    public function queue_next_put($p = 0) {
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
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->queue_next_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $queue_number = $id;
    
        if ($queue_number === 0 || $queue_number === "0") {
            $this->REST_Return(400, 'queue_number is required');
            return;
        }

        $code_prefix = 'LAB';
        
        $date = $dt_put['date'];

        $select_queue = 'a.name, a.data, a.status';
        $where_queue = array(
            'a.name' => $code_prefix,
            'a.date' => $date,
        );
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue);

        /* Start Transaction */
        $this->db->trans_start();

        $updated_status = 0;
        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if (in_array($queue_number, $queue_data) == 1) {
                $idx_remove = array_search($queue_number, $queue_data);
                unset($queue_data[$idx_remove]);

                $updated_status = $queue_status + 1;

                if ($updated_status > count($queue)) {
                    $this->REST_Return(409, 'queue number ' . $queue_number . ' is in the last step');
                    return;
                }

                $dt_queue = array(
                    'name' => $code_prefix,
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        if ($updated_status == 0) {
            $this->REST_Return(404, 'there is no queue number ' . $queue_number . ' in queue');
            return;
        }

        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = ($queue[$i]->data == '') ? [] : explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if ($updated_status == $queue_status) {
                array_push($queue_data, $queue_number);

                $dt_queue = array(
                    'name' => $code_prefix,
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to queue next', $response_data);
            return;
        }
    
        $response_data = array(
            'queue_number'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function queue_back_put($p = 0) {
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
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->queue_next_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $queue_number = $id;
    
        if ($queue_number === 0 || $queue_number === "0") {
            $this->REST_Return(400, 'queue_number is required');
            return;
        }

        $code_prefix = 'LAB';
        
        $date = $dt_put['date'];

        $select_queue = 'a.name, a.data, a.status';
        $where_queue = array(
            'a.name' => $code_prefix,
            'a.date' => $date,
        );
        $queue = $this->Queue_display->select(0, $select_queue, $where_queue);

        /* Start Transaction */
        $this->db->trans_start();

        $updated_status = 0;
        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if (in_array($queue_number, $queue_data) == 1) {
                $idx_remove = array_search($queue_number, $queue_data);
                unset($queue_data[$idx_remove]);

                $updated_status = $queue_status - 1;

                if ($updated_status < 1) {
                    $this->REST_Return(409, 'queue number ' . $queue_number . ' is in the first step');
                    return;
                }

                $dt_queue = array(
                    'name' => $code_prefix,
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        if ($updated_status == 0) {
            $this->REST_Return(404, 'there is no queue number ' . $queue_number . ' in queue');
            return;
        }

        for ($i=0; $i < count($queue); $i++) { 
            $queue_data = ($queue[$i]->data == '') ? [] : explode(',', $queue[$i]->data);
            $queue_status = $queue[$i]->status;

            if ($updated_status == $queue_status) {
                array_push($queue_data, $queue_number);

                $dt_queue = array(
                    'name' => $code_prefix,
                    'data' => implode(',', $queue_data),
                    'date' => $date,
                    'status' => $queue_status,
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Queue_display->replace($dt_queue);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to queue next', $response_data);
            return;
        }
    
        $response_data = array(
            'queue_number'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    /**
     * Laboratory_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/mr/laboratory_request
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
            $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim|required');
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

                if(count($dt_put['laboratory_request_items']) == 0) {
                    $this->REST_Return(400, 'Pilih minimal satu pemeriksaan');
                    return;
                }

                /* Start Transaction */
                $this->db->trans_start();
                $laboratory_request = $this->$model->read($id);
                if($laboratory_request != []) {
                    $dt_laboratory_request = array(
                        'id'              => $id,
                        'soap_id'         => $dt_put['soap_id'],
                        'date_request'    => $dt_put['date_request'],
                    );
                    $this->$model->update($id, $dt_laboratory_request);

                    $this->Laboratory_request_item->whereFields['laboratory_request_id'] = $id;
                    $laboratory_request_item = $this->Laboratory_request_item->read(0,false);
                    $package_arr = $dt_put['laboratory_request_items'];
                    $tmp_delete_package_arr = array();

                    // adding all current items (db) to temporary variable
                    for ($i=0; $i < count($laboratory_request_item); $i++) { 
                        $tmp_delete_package_arr[$laboratory_request_item[$i]->laboratory_package_id] = $laboratory_request_item[$i]->id;
                    }

                    // delete all items if no item provided
                    if(count($dt_put['laboratory_request_items']) == 0) {
                        for ($i=0; $i < count($laboratory_request_item); $i++) { 
                            if ($laboratory_request_item[$i]->id) {
                                $dt_laboratory_request_item = array(
                                    'id'                        => $laboratory_request_item[$i]->id,
                                    'laboratory_request_id'     => $laboratory_request_item[$i]->laboratory_request_id,
                                    'laboratory_package_id'     => $laboratory_request_item[$i]->laboratory_package_id,
                                    'is_delete'                 => 1,
                                );
                                $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                            }
                        }
                    }

                    for ($i=0; $i < count($dt_put['laboratory_request_items']); $i++) { 
                        for ($i=0; $i < count($package_arr) ; $i++) { 
                            $package_id = $package_arr[$i]['laboratory_package_id'];
                            $service_class_id = $package_arr[$i]['service_class_id'];

                            $package = $this->Laboratory_package->select_id($package_id);
                            if($package == "") {
                                $this->REST_Return(404, 'There is no laboratory_package with id ' . $package_id);
                                return;
                            }

                            $service_class = $this->Service_class->select_id($service_class_id);
                            if($service_class == "") {
                                $this->REST_Return(404, 'There is no service_class with id ' . $service_class_id);
                                return;
                            }

                            $select = "a.costing_id";
                            $costing_id = $this->Laboratory_package->select($package_id, $select)[0]->costing_id;
                            $costing = $this->Costing->read($costing_id,false);

                            $this->Costing_services_class->whereFields['costing_id'] = $costing_id;
                            $costing_services_class = $this->Costing_services_class->read(0,false);
                            
                            $service_class = $this->Service_class->read($service_class_id,false);

                            $dt_laboratory_request_item = array(
                                'laboratory_request_id'     => $id,
                                'laboratory_package_id'     => $package_id,
                                'service_class_id'          => $service_class[0]->id,
                                'var_1'                     => $costing_services_class[0]->var_1,
                                'var_2'                     => $costing_services_class[0]->var_2,
                                'var_3'                     => $costing_services_class[0]->var_3,
                                'costing_id'                => $costing[0]->id,
                                'costing_name'              => $costing[0]->name,
                                'date_request'              => $dt_put['date_request'],
                                'status'                    => $dt_put['laboratory_request_items'][$i]['status'],
                                'is_delete'                 => 0
                            );
    
                            // create or update items according to the items provided and unset used items from temporary variable
                            if(array_key_exists($package_id,$tmp_delete_package_arr) == 1) {
                                $dt_laboratory_request_item['id'] = $tmp_delete_package_arr[$package_id];
                                $this->Laboratory_request_item->replace($dt_laboratory_request_item);
    
                                unset($tmp_delete_package_arr[$package_id]);
                            } else {
                                $this->Laboratory_request_item->create($dt_laboratory_request_item);
                            }
                        }
                    
                        // delete all items in temporary variable
                        foreach ($tmp_delete_package_arr as $package_id => $laboratory_request_item_id) {
                            if ($laboratory_request_item_id) {
                                $dt_laboratory_request_item = array(
                                    'id'                        => $laboratory_request_item_id,
                                    'laboratory_request_id'     => $id,
                                    'laboratory_package_id'     => $package_id,
                                    'is_delete'                 => 1,
                                );
                                $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                            }
                        }
                    }

                    // for ($i=0; $i < count($dt_put['laboratory_request_items']); $i++) {
                    //     $this->Laboratory_request_item->whereFields['a.id'] = $dt_put['laboratory_request_items'][$i]['id'];
                    //     $laboratory_request_item = $this->Laboratory_request_item->read(0,false); 
                    //     if($laboratory_request_item != []) {
                    //         $laboratory_package = $this->Laboratory_package->read($dt_put['laboratory_request_items'][$i]['laboratory_package_id'], false);
                    //         $service_class = $this->Service_class->read($dt_put['laboratory_request_items'][$i]['service_class_id'],false);
                    //         if($laboratory_package != [] && $service_class != []) {
                    //             $costing = $this->Costing->read($laboratory_package[0]->costing_id,false);
                    //             $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                    //             // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                    //             $costing_services_class = $this->Costing_services_class->read(0,false);
                    //             $dt_laboratory_request_item = array(
                    //                 'id'                        => $dt_put['laboratory_request_items'][$i]['id'],
                    //                 'laboratory_request_id'     => $id,
                    //                 'laboratory_package_id'     => $dt_put['laboratory_request_items'][$i]['laboratory_package_id'],
                    //                 'service_class_id'          => $service_class[0]->id,
                    //                 'var_1'                     => $costing_services_class[0]->var_1,
                    //                 'var_2'                     => $costing_services_class[0]->var_2,
                    //                 'var_3'                     => $costing_services_class[0]->var_3,
                    //                 'costing_id'                => $costing[0]->id,
                    //                 'costing_name'              => $costing[0]->name,
                    //                 'notes'                     => $dt_put['laboratory_request_items'][$i]['notes'],
                    //                 'status'                    => $dt_put['laboratory_request_items'][$i]['status'],
                    //             );
                    //             $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                    //         } else {
                    //             $this->REST_Return(404, 'There is no laboratory_package with id ' . $dt_put['laboratory_request_items'][$i]['laboratory_package_id'] . ' or service_class with id ' . $dt_put['laboratory_request_items'][$i]['service_class_id']);
                    //             return;
                    //         }
                    //     } else {
                    //         $this->REST_Return(404, 'Laboratory_request_item with id ' . $dt_put['laboratory_request_items'][$i]['id'] . ' does not exist!');
                    //         return;
                    //     }
                    // }
                } else {
                    $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
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

    /**
     * Laboratory_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/mr/laboratory_request
     **/

    public function process_lis_put($p = 0) {
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
            // $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            // $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                
                
                $laboratory_request = $this->Laboratory_request->read($id);

                $this->Registration->whereFields['b.id'] =  $laboratory_request[0]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false);
                $insurance_data = $this->Registration_insurance->read($laboratory_request[0]->registration_destination_id, false);

                $ouputdb = $this->Laboratory_request_item->read($id);
                if($ouputdb){
                    for ($i=0; $i < count($ouputdb); $i++) { 
                        $layanan[$i] = array(
                            'kode'            => $ouputdb[$i]->code_lis,
                            'nama'            => $ouputdb[$i]->costing_name
                        );
                    }
                }
                $toLIS = array(
                "data"=> array(
                    "transaksi_id" => $id,
                    "kode" => $laboratory_request[0]->no_request.$laboratory_request[0]->date_request,
                    "tanggal_dibuat" => $laboratory_request[0]->date_request,
                    "dokter_pengirim" => $laboratory_request[0]->doctor_in_charge_name,
                    "jadwal_permintaan" => null,
                    "jenis_layanan" => "Biasa",
                    "lokasi" => $resgistration_data[0]->health_facility_item_name,
                    "pasien" => array(
                        "no_rm" => $resgistration_data[0]->norm,
                        "nama" => $resgistration_data[0]->name,
                        "jenis_kelamin" => $resgistration_data[0]->sex == 1 ? "LK" : "LP",
                        "nik" => $resgistration_data[0]->idcard,
                        "tanggal_lahir" => $resgistration_data[0]->birth_date,
                        "alamat" => $resgistration_data[0]->address
                    ),
                    "pembayaran" => array(
                        "perusahaan"=> $insurance_data[0]->insurance_id == 1 ? "TUNAI" : $insurance_data[0]->company_name,
                        "no_asuransi"=>$insurance_data[0]->insurance_id == 1 ? null : null,
                        "kelas"=>$insurance_data[0]->insurance_id == 1 ? null : $insurance_data[0]->service_class_name
                    ),
                    "layanan" => $layanan,
                        // [
                        //     {"kode":5528,"nama":"Darah rutin"},
                        //     {"kode":5650,"nama":"Gula darah sewaktu"}
                        // ],
                    "keterangan"=>null,
                    "creator"=>$laboratory_request[0]->doctor_in_charge_name)
                );
                $response_data = $this->bride_lis('/api_lis/index.php',json_encode($toLIS));
                $laboratory_request = $this->Laboratory_request->read($id);
                if($laboratory_request) {
                    $dt_laboratory_request = array(
                        'id'              => $id,
                        'date_lis'    => date('Y-m-d H:i:s'),
                        'is_lis'          => 1,
                    );
                    $this->Laboratory_request->replace($dt_laboratory_request);
                } else {
                    $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
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

    public function process_put($p = 0) {
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
            // $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            // $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                $this->Laboratory_request_item->whereFields['a.id'] = $id;
                $laboratory_request = $this->Laboratory_request->read($id);
                if($laboratory_request) {
                    $dt_laboratory_request = array(
                        'id'              => $id,
                        'date_process'    => date('Y-m-d H:i:s'),
                        'status'          => 2,
                    );
                    $this->Laboratory_request->replace($dt_laboratory_request);
                    $outputdata = $this->Laboratory_request_item->read($id);
                    if($outputdata){
                        for ($i=0; $i < count($outputdata); $i++) { 
                            $dt_laboratory_request_item = array(
                                'id'              => $outputdata[$i]->id,
                                'date_process'    => date('Y-m-d H:i:s'),
                                'status'          => 2,
                            );
                            $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
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
            // $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                // $this->Laboratory_request_item->whereFields['a.id'] = $id;
                $laboratory_request = $this->Laboratory_request->read($id);
                if($laboratory_request) {
                    $dt_laboratory_request = array(
                        'id'              => $id,
                        'date_result'     => date('Y-m-d H:i:s'),
                        'doctor_lab_id'   => $dt_put['doctor_lab_id']['value'],
                        'user_lab_id'     => $dt_put['user_lab_id']['value'],
                        'conclusion'      => $dt_put['conclusion'],
                        'notes'           => $dt_put['notes'],
                        'advice'          => $dt_put['advice'],
                        'status'          => 3,
                    );
                    $this->Laboratory_request->update($id,$dt_laboratory_request);
                    $outputdata = $this->Laboratory_request_item->read($id);
                    if($outputdata){
                        for ($i=0; $i < count($outputdata); $i++) { 
                            $dt_laboratory_request_item = array(
                                'id'             => $outputdata[$i]->id,
                                'date_result'    => date('Y-m-d H:i:s'),
                            );
                            $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
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

    public function delete_delete($p = 0) {
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
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        /* Start Transaction */
        $this->db->trans_begin();

        $is_delete = $dt_put['is_delete'];
    
        $laboratory_request_id = $this->$model->select_id($id);
    
        if ($laboratory_request_id == "") {
            $this->REST_Return(404, 'there is no laboratory_request with id ' . $id);
            return;
        }

        $select = 'a.id';
        $where = array('laboratory_request_id' => $laboratory_request_id);
        $laboratory_request_item = $this->Laboratory_request_item->select(0, $select, $where);

        if (count($laboratory_request_item) > 0) {
            $dt_laboratory_request_item = array(
                'is_delete' => $is_delete,
            );

            $this->Laboratory_request_item->update($laboratory_request_id, $dt_laboratory_request_item);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_laboratory_request_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to delete laboratory_request_item', $response_data);
                return;
            }
        } 
        
        $dt_laboratory_request = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($laboratory_request_id, $dt_laboratory_request);
    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_laboratory_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete laboratory_request', $response_data);
            return;
        }

        $this->db->trans_commit();
        /* End Transaction */

        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function delete_item_delete($p = 0) {
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
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        /* Start Transaction */
        $this->db->trans_begin();

        $is_delete = $dt_put['is_delete'];

        $select = 'a.id';
        $where = array('id' => $id);
        $laboratory_request_item = $this->Laboratory_request_item->select($id, $select, $where);

        if (count($laboratory_request_item) > 0) {
            $dt_laboratory_request_item = array(
                'is_delete' => $is_delete,
            );

            $this->Laboratory_request_item->update($laboratory_request_id, $dt_laboratory_request_item);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_laboratory_request_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to delete laboratory_request_item', $response_data);
                return;
            }
        } 
    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_laboratory_request_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete laboratory_request', $response_data);
            return;
        }

        $this->db->trans_commit();
        /* End Transaction */

        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
    public function sendFile_put($p = 0) {
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
            // $this->form_validation->set_rules('date_request', 'date_request', 'trim|required');
            // $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                $this->Laboratory_request_item->whereFields['a.id'] = $id;
                $outputdata = $this->Laboratory_request_item->read(0,false);
                if($outputdata){
                    for ($i=0; $i < count($outputdata); $i++) { 
                        $laboratory_request_id = $outputdata[$i]->laboratory_request_id;
                        $select = 'a.id, a.soap_id';
                        $laboratory_request = $this->Laboratory_request->select($laboratory_request_id, $select);

                        if (count($laboratory_request) == 0) {
                            $this->REST_Return(404, 'there is no laboratory_request with id ' . $laboratory_request_id);
                            return;
                        }

                        $soap_id = $laboratory_request[0]->soap_id;

                        $select = 'a.id, r.norm';
                        $soap = $this->Soap->select($soap_id, $select);

                        if (count($soap) == 0) {
                            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                            return;
                        }
                        
                        $norm = $soap[0]->norm;

                        $select = 'a.norm, a.name';
                        $patient = $this->Patient->select($norm, $select);

                        if (count($patient) == 0) {
                            $this->REST_Return(404, 'there is no patient with norm ' . $norm);
                            return;
                        }

                        $patient_name = $patient[0]->name;

                        $dt_laboratory_request_item = array(
                            'id' => $id,
                            'url'                       => '',
                        );
                        
                        if (isset($dt_put['result'])) {
                            // USG UPPER LOWER ABDOMEN-21092024-039599-RAFLY RAMDANI BUMA
                            $result = $dt_put['result'];
                            $extension = $dt_put['extension'];
                            $costing_name = $outputdata[$i]->costing_name;
                            $date_request_raw = $outputdata[$i]->date_request;
                            $unix_time = strtotime($date_request_raw);
                            $date = date('dmY', $unix_time);

                            $costing_name = str_replace(' ', '_', $costing_name);
                            $costing_name = str_replace('/', '', $costing_name);
                            $patient_name = str_replace(' ', '_', $patient_name);
                            $costing_name = str_replace('(', '', $costing_name);
                            $costing_name = str_replace(')', '', $costing_name);

                            if ($extension == NULL) {
                                $this->REST_Return(400, 'extension field is required');
                                return;
                            }

                            $extension = explode('/', $extension)[1];

                            $bytes = random_bytes(6 / 2);
                            $random_6 = substr(bin2hex($bytes), 0, 6);
                      
                            $url = $this->base64ToFile($result, $random_6 . '-' . $costing_name . '-' . $date . '-' . $norm . '-' . $patient_name . '.' . $extension);
                            
                            $dt_laboratory_request_item['url'] = $url;
                        } 

                        $id = $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                    }
                } else {
                    $this->REST_Return(404, 'Laboratory_request with id ' . $id . ' does not exist!');
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

    private function base64ToFile ($p = 0, $filename = '') {
        define('UPLOAD_DIR', 'assets/document/lab/');
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name;
        $success      = file_put_contents($file, $data);
        $file         = $file_name;
        return $file;
    }
}