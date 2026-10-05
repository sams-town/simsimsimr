<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Radiology_request extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/{$this->name}_item_image_model", "{$this->name}_item_image");
        $this->load->model("$this->directory/Radiology_package_model", "Radiology_package");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Registration_insurance_model", "Registration_insurance");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/Display_queue_model", "Queue_display");
        $this->load->model("v1/setting/Queue_status_model", "Queue_status");
        // Load Helper
        $this->load->helper('queue');

        $this->create_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim|required'),
            array('field' => 'radiology_request_items[]', 'label' => 'radiology_request_items', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->queue_next_rules = array(
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );
    }

    /**
     * Radiology_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/health_facility/radiology_request
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
        $clinical_indications = $dt_post['clinical_indications'];
        $radiology_request_items_arr = $dt_post['radiology_request_items'];

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

        // TODO - DEPRECATED, DELETE SOON!
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
        //         $no_request = 'RAD' . $waktu . '0001';
        //     } else {
        //         $no_request = 'RAD' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
        //     }
        // } else {
        //     $no_request = 'RAD' . $waktu . '0001';
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
        $prefix = 'RAD';
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

        $dt_radiology_request = array(
            'institute_branch_id'   => $institute_branch_id,
            'soap_id'               => $soap_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'no_request'            => $no_request,
            'clinical_indications'  => $clinical_indications,
            'queue'                 => $queue,
            'date_request'          => $date_request
        );
        $id = $this->$model->create($dt_radiology_request);
    
        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_radiology_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create radiology_request', $response_data);
            return;
        }

        for ($i=0; $i < count($radiology_request_items_arr) ; $i++) {
            if (!isset($radiology_request_items_arr[$i]['radiology_package_id'])) {
                $this->REST_Return(400, "radiology_package_id field in radiology_request_items is required!");
                return;
            }

            if (!isset($radiology_request_items_arr[$i]['service_class_id'])) {
                $this->REST_Return(400, "service_class_id field in radiology_request_items is required!");
                return;
            }

            $radiology_package_id = $radiology_request_items_arr[$i]['radiology_package_id'];
            $service_class_id = $radiology_request_items_arr[$i]['service_class_id'];

            $radiology_package = $this->Radiology_package->read($radiology_package_id, false);
            $service_class = $this->Service_class->read($service_class_id, false);

            if ($radiology_package == []) {
                $this->REST_Return(404, "there is no radiology_package with id " . $radiology_package_id);
                return;
            }

            if ($service_class == []) {
                $this->REST_Return(404, "there is no service_class with id " . $service_class_id);
                return;
            }

            $costing_id = $radiology_package[0]->costing_id;

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
            
            $dt_radiology_request_item = array(
                'radiology_request_id'      => $id,
                'radiology_package_id'      => $radiology_package_id,
                'service_class_id'          => $service_class_id,
                'var_1'                     => $costing_services_class_var_1,
                'var_2'                     => $costing_services_class_var_2,
                'var_3'                     => $costing_services_class_var_3,
                'costing_id'                => $costing_id,
                'costing_name'              => $costing_name,
                'date_request'              => $date_request,
                'status'                    => 1
            );
            $this->Radiology_request_item->create($dt_radiology_request_item);

            $this->db->trans_complete();
            /* End Transaction */

            if ($this->db->trans_status() === false) {
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_radiology_request_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to create radiology_request_item', $response_data);
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
            if(isset($q_strings['doctor_in_charge_id'])) $this->$model->whereFields['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id']; unset($this->$model->whereFields['doctor_in_charge_id']);
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $this->Registration->whereFields['b.id'] =  $output[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                $this->Radiology_request_item->whereFields['a.is_delete'] = 0;
                $radiology_request_item = $this->Radiology_request_item->read($output[$i]->id);
                $output[$i]->regData = $resgistration_data;
                $no=1;
                if ($radiology_request_item != []) {
                    for ($j=0; $j < count($radiology_request_item); $j++) { 
                        $output[$i]->radiology_request_item[$j]['no'] = $no;
                        $output[$i]->radiology_request_item[$j]['id'] = $radiology_request_item[$j]->id;
                        $output[$i]->radiology_request_item[$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
                        $output[$i]->radiology_request_item[$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
                        $output[$i]->radiology_request_item[$j]['service_class_id'] = $radiology_request_item[$j]->service_class_id;
                        $output[$i]->radiology_request_item[$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
                        $output[$i]->radiology_request_item[$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
                        $output[$i]->radiology_request_item[$j]['var_1'] = $radiology_request_item[$j]->var_1;
                        $output[$i]->radiology_request_item[$j]['var_2'] = $radiology_request_item[$j]->var_2;
                        $output[$i]->radiology_request_item[$j]['var_3'] = $radiology_request_item[$j]->var_3;
                        $this->Radiology_request_item_image->whereFields['a.is_delete'] = 0;
                        $url = $this->Radiology_request_item_image->read($radiology_request_item[$j]->id);
                        $output[$i]->radiology_request_item[$j]['url'] = $url;
                        for ($k=0; $k < count($url); $k++) { 
                            $url_raw = $url[$k]->url;
                            $name = substr($url_raw, 20);

                            $output[$i]->radiology_request_item[$j]['url'][$k]->name = $name;
                        }
                        // $output[$i]->radiology_request_item[$j]['url']->name = $url;
                        $output[$i]->radiology_request_item[$j]['date_request'] = $radiology_request_item[$j]->date_request;
                        $output[$i]->radiology_request_item[$j]['date_process'] = $radiology_request_item[$j]->date_process;
                        $output[$i]->radiology_request_item[$j]['date_result'] = $radiology_request_item[$j]->date_result;
                        $output[$i]->radiology_request_item[$j]['notes'] = $radiology_request_item[$j]->notes;
                        $output[$i]->radiology_request_item[$j]['status'] = $radiology_request_item[$j]->status;
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
            $this->Radiology_request->whereFields['a.is_delete'] = 0;
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Registration->whereFields['b.id'] = $output->rows[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                // $insurance_data = $this->Registration_insurance->read($output->rows[$i]->registration_destination_id, false)[0];
                $this->Radiology_request_item->whereFields['a.is_delete'] = 0;
                $radiology_request_item = $this->Radiology_request_item->read($output->rows[$i]->id);
                $output->rows[$i]->code = $resgistration_data->code;
                $output->rows[$i]->register_date = $resgistration_data->register_date;
                // $output->rows[$i]->insurance_name = $insurance_data->insurance_name;
                $this->Registration_insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
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
                $output->rows[$i]->idcard = $resgistration_data->idcard;
                $output->rows[$i]->alias_name = $resgistration_data->alias_name;
                $output->rows[$i]->name = $resgistration_data->name;
                $output->rows[$i]->is_new = $resgistration_data->is_new;
                $output->rows[$i]->birth_date = $resgistration_data->birth_date;
                $output->rows[$i]->age = $age;
                $output->rows[$i]->sex_name = $resgistration_data->sex_name;
                $output->rows[$i]->health_facility_name = $resgistration_data->health_facility_name;
                $output->rows[$i]->health_facility_item_name = $resgistration_data->health_facility_item_name;
                // $output->rows[$i]->doctor_in_charge_name = $resgistration_data->doctor_in_charge_name;
                $output->rows[$i]->queue = $resgistration_data->queue;
                if ($radiology_request_item != []) {
                    for ($j=0; $j < count($radiology_request_item); $j++) { 
                        $output->rows[$i]->radiology_request_item[$j]['id'] = $radiology_request_item[$j]->id;
                        $output->rows[$i]->radiology_request_item[$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
                        $output->rows[$i]->radiology_request_item[$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
                        $output->rows[$i]->radiology_request_item[$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
                        $output->rows[$i]->radiology_request_item[$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
                        $output->rows[$i]->radiology_request_item[$j]['var_1'] = $radiology_request_item[$j]->var_1;
                        $output->rows[$i]->radiology_request_item[$j]['url'] = $this->Radiology_request_item_image->read($radiology_request_item[$j]->id);
                        $output->rows[$i]->radiology_request_item[$j]['var_2'] = $radiology_request_item[$j]->var_2;
                        $output->rows[$i]->radiology_request_item[$j]['var_3'] = $radiology_request_item[$j]->var_3;
                        $output->rows[$i]->radiology_request_item[$j]['notes'] = $radiology_request_item[$j]->notes;
                        $output->rows[$i]->radiology_request_item[$j]['status'] = $radiology_request_item[$j]->status;
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

        $qparam_expertise_status = $q_strings['expertise_status'] ?? null;
        if ($qparam_expertise_status != null) {
            unset($q_strings['expertise_status']);
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
        rd.id as registration_destination_id,
        rd.health_facility_item_id,
        hfi.name as health_facility_item_name,
        r.register_date,
        r.how_to_enter_id,
        hte.name as type_how_to_enter_name,
        a.date_request, 
        a.date_process, 
        a.date_result, 
        a.doctor_in_charge_id, 
        e.fullname as doctor_in_charge_name, 
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

        $where['a.is_delete'] = 0;
        
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('how_to_enter hte', 'r.how_to_enter_id = hte.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', 'p.sex = h.code and h.variable="sex"', 'INNER'),
            array('helper h2', 'p.alias = h2.code and h2.variable="alias"', 'INNER'),
        );

        $output = $this->$model->select(0, $select, $where, $join);

        $expertise_output = [];

        for ($i=0; $i < count($output); $i++) { 
            $radiology_request_id = $output[$i]->id;
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
                'a.name' => 'RAD',
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

            /* 
                Expertise Status
                Untuk mengetahui apakah order radiologi sudah terisi expertise
            */
            $expertise_status = 1;
            $select = 'a.id';
            $where_rri = array(
                'a.radiology_request_id' => $radiology_request_id,
                'a.is_delete' => 0,
            );
            $radiology_request_item = $this->Radiology_request_item->select(0, $select, $where_rri);
            for ($j=0; $j < count($radiology_request_item); $j++) { 
                $radiology_request_item_id = $radiology_request_item[$j]->id;

                $select = 'a.id, a.status';
                $where_rrii = array(
                    'a.radiology_request_item_id' => $radiology_request_item_id,
                );
                $radiology_request_item_image = $this->Radiology_request_item_image->select($radiology_request_item_id, $select, $where_rrii);

                $item_done = false;
                for ($k=0; $k < count($radiology_request_item_image); $k++) { 
                    $radiology_request_item_image_status = $radiology_request_item_image[$k]->status;
                    if ($radiology_request_item_image_status == 2) {
                        $item_done = true;
                        break;
                    }
                }

                if (!$item_done) {
                    $expertise_status = 1;
                    break;
                }

                $expertise_status = 2;
            }
            $output[$i]->expertise_status = $expertise_status;

            if ($qparam_expertise_status != null && $qparam_expertise_status == $expertise_status) {
                array_push($expertise_output, $output[$i]);
            }
        }

        if ($qparam_expertise_status != null) {
            $output = $expertise_output;
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

        $code_prefix = 'RAD';

        $date = $q_strings['date'];

        $date_start = $date . ' 00:00:00';
        $date_end = $date . ' 23:59:59';

        $select_rad = 'a.no_request, a.queue';
        $where_rad = array(
            'a.is_delete' => 0,
            'a.date_request >=' => $date_start,
            'a.date_request <=' => $date_end,
            'hfi.health_facility_id' => '3cb626f5-9148-4fb1-8663-3d7f028b01a6'
        );
        $join_rad = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
        );
        $order_rad = array('a.queue' => 'ASC');
        $radiology_request = $this->$model->select(0, $select_rad, $where_rad, $join_rad, $order_rad);

        $data_queue = [];

        for ($i=0; $i < count($radiology_request); $i++) { 
            $queue_number = $radiology_request[$i]->queue;
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

                $select_rad = 'a.no_request, a.queue, rd.code_queue as registration_destination_code_queue, rd.queue as registration_destination_queue';
                $where_rad = array(
                    'a.is_delete' => 0,
                    'a.queue' => $queue_number,
                    'a.date_request >=' => $date_start,
                    'a.date_request <=' => $date_end,
                );
                $join_rad = array(
                    array('soap s', 'a.soap_id = s.id', 'INNER'),
                    array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
                    array('registration r', 'rd.registration_id = r.id', 'INNER'),
                    // array('patient ptn', 'r.norm = ptn.norm', 'INNER'),
                );
                $radiology_request = $this->$model->select(0, $select_rad, $where_rad, $join_rad);

                $no_request = $radiology_request[0]->no_request;
                // $patient_name = $radiology_request[0]->patient_name;
                $registration_destination_code_queue = $radiology_request[0]->registration_destination_code_queue;
                $registration_destination_queue = $radiology_request[0]->registration_destination_queue;

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

        $code_prefix = 'RAD';

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

        $code_prefix = 'RAD';
        
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
     * Radiology_request API
     * --------------------
     * @param: soap_id
     * @param: date_request
     * --------------------
     * @method : POST
     * @link: version/mr/radiology_request
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
            $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim|required');
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

                if(count($dt_put['radiology_request_items']) == 0) {
                    $this->REST_Return(400, 'Pilih minimal satu pemeriksaan');
                    return;
                }
                
                /* Start Transaction */
                $this->db->trans_start();
                $radiology_request = $this->$model->read($id);
                if($radiology_request != []) {
                    $dt_radiology_request = array(
                        'id'              => $id,
                        'soap_id'         => $dt_put['soap_id'],
                        'date_request'    => $dt_put['date_request'],
                        'clinical_indications' => $dt_put['clinical_indications'],
                    );
                    $this->$model->update($id, $dt_radiology_request);

                    $this->Radiology_request_item->whereFields['radiology_request_id'] = $id;
                    $radiology_request_item = $this->Radiology_request_item->read(0,false);
                    $package_arr = $dt_put['radiology_request_items'];
                    $tmp_delete_package_arr = array();

                    // adding all current items (db) to temporary variable
                    for ($i=0; $i < count($radiology_request_item); $i++) { 
                        $tmp_delete_package_arr[$radiology_request_item[$i]->radiology_package_id] = $radiology_request_item[$i]->id;
                    }

                    // delete all items if no item provided
                    if(count($dt_put['radiology_request_items']) == 0) {
                        for ($i=0; $i < count($radiology_request_item); $i++) { 
                            if ($radiology_request_item[$i]->id) {
                                $dt_radiology_request_item = array(
                                    'id'                        => $radiology_request_item[$i]->id,
                                    'radiology_request_id'      => $radiology_request_item[$i]->radiology_request_id,
                                    'radiology_package_id'      => $radiology_request_item[$i]->radiology_package_id,
                                    'is_delete'                 => 1,
                                );
                                $this->Radiology_request_item->replace($dt_radiology_request_item);
                            }
                        }
                    }

                    for ($i=0; $i < count($dt_put['radiology_request_items']); $i++) { 
                        for ($i=0; $i < count($package_arr) ; $i++) { 
                            $package_id = $package_arr[$i]['radiology_package_id'];
                            $service_class_id = $package_arr[$i]['service_class_id'];

                            $package = $this->Radiology_package->select_id($package_id);
                            if($package == "") {
                                $this->REST_Return(404, 'There is no radiology_package with id ' . $package_id);
                                return;
                            }

                            $service_class = $this->Service_class->select_id($service_class_id);
                            if($service_class == "") {
                                $this->REST_Return(404, 'There is no service_class with id ' . $service_class_id);
                                return;
                            }

                            $select = "a.costing_id";
                            $costing_id = $this->Radiology_package->select($package_id, $select)[0]->costing_id;
                            $costing = $this->Costing->read($costing_id,false);

                            $this->Costing_services_class->whereFields['costing_id'] = $costing_id;
                            $costing_services_class = $this->Costing_services_class->read(0,false);
                            
                            $service_class = $this->Service_class->read($service_class_id,false);

                            $dt_radiology_request_item = array(
                                'radiology_request_id'      => $id,
                                'radiology_package_id'      => $package_id,
                                'service_class_id'          => $service_class[0]->id,
                                'var_1'                     => $costing_services_class[0]->var_1,
                                'var_2'                     => $costing_services_class[0]->var_2,
                                'var_3'                     => $costing_services_class[0]->var_3,
                                'costing_id'                => $costing[0]->id,
                                'costing_name'              => $costing[0]->name,
                                'date_request'              => $dt_put['date_request'],
                                'status'                    => $dt_put['radiology_request_items'][$i]['status'],
                                'is_delete'                 => 0
                            );
    
                            // create or update items according to the items provided and unset used items from temporary variable
                            if(array_key_exists($package_id,$tmp_delete_package_arr) == 1) {
                                $dt_radiology_request_item['id'] = $tmp_delete_package_arr[$package_id];
                                $this->Radiology_request_item->replace($dt_radiology_request_item);
    
                                unset($tmp_delete_package_arr[$package_id]);
                            } else {
                                $this->Radiology_request_item->create($dt_radiology_request_item);
                            }
                        }
                    
                        // delete all items in temporary variable
                        foreach ($tmp_delete_package_arr as $package_id => $radiology_request_item_id) {
                            if ($radiology_request_item_id) {
                                $dt_radiology_request_item = array(
                                    'id'                        => $radiology_request_item_id,
                                    'radiology_request_id'      => $id,
                                    'radiology_package_id'      => $package_id,
                                    'is_delete'                 => 1,
                                );
                                $this->Radiology_request_item->replace($dt_radiology_request_item);
                            }
                        }
                    }


                    // for ($i=0; $i < count($dt_put['radiology_request_items']); $i++) {
                    //     $this->Radiology_request_item->whereFields['a.id'] = $dt_put['radiology_request_items'][$i]['id'];
                    //     $radiology_request_item = $this->Radiology_request_item->read(0,false); 
                    //     if($radiology_request_item != []) {
                    //         $radiology_package = $this->Radiology_package->read($dt_put['radiology_request_items'][$i]['radiology_package_id'], false);
                    //         $service_class = $this->Service_class->read($dt_put['radiology_request_items'][$i]['service_class_id'],false);
                    //         if($radiology_package != [] && $service_class != []) {
                    //             $costing = $this->Costing->read($radiology_package[0]->costing_id,false);
                    //             $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                    //             $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                    //             $costing_services_class = $this->Costing_services_class->read(0,false);
                    //             $dt_radiology_request_item = array(
                    //                 'id'                        => $dt_put['radiology_request_items'][$i]['id'],
                    //                 'radiology_request_id'      => $id,
                    //                 'radiology_package_id'      => $dt_put['radiology_request_items'][$i]['radiology_package_id'],
                    //                 'service_class_id'          => $service_class[0]->id,
                    //                 'var_1'                     => $costing_services_class[0]->var_1,
                    //                 'var_2'                     => $costing_services_class[0]->var_2,
                    //                 'var_3'                     => $costing_services_class[0]->var_3,
                    //                 'costing_id'                => $costing[0]->id,
                    //                 'costing_name'              => $costing[0]->name,
                    //                 'notes'                     => $dt_put['radiology_request_items'][$i]['notes'],
                    //                 'status'                    => $dt_put['radiology_request_items'][$i]['status'],
                    //             );
                    //             $this->Radiology_request_item->replace($dt_radiology_request_item);
                    //         } else {
                    //             $this->REST_Return(404, 'There is no radiology_package with id ' . $dt_put['radiology_request_items'][$i]['radiology_package_id'] . ' or service_class with id ' . $dt_put['radiology_request_items'][$i]['service_class_id']);
                    //             return;
                    //         }
                    //     } else {
                    //         $this->REST_Return(404, 'Radiology_request_item with id ' . $dt_put['radiology_request_items'][$i]['id'] . ' does not exist!');
                    //         return;
                    //     }
                    // }
                } else {
                    $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
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
            // $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                // $this->Radiology_request_item->whereFields['a.id'] = $id;
                $radiology_request = $this->Radiology_request->read($id);
                if($radiology_request) {
                    $dt_radiology_request = array(
                        'id'              => $id,
                        'date_process'    => date('Y-m-d H:i:s'),
                        'status'          => 2,
                    );
                    $this->Radiology_request->replace($dt_radiology_request);
                    $outputdata = $this->Radiology_request_item->read($id);
                    if($outputdata){
                        for ($i=0; $i < count($outputdata); $i++) { 
                            $dt_radiology_request_item = array(
                                'id'              => $outputdata[$i]->id,
                                'date_process'    => date('Y-m-d H:i:s'),
                                'status'          => 2,
                            );
                            $this->Radiology_request_item->replace($dt_radiology_request_item);
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
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
                $this->Radiology_request_item->whereFields['a.id'] = $id;
                $outputdata = $this->Radiology_request_item->read(0,false);
                if($outputdata){
                    for ($i=0; $i < count($outputdata); $i++) { 
                        $radiology_request_id = $outputdata[$i]->radiology_request_id;
                        $select = 'a.id, a.soap_id';
                        $radiology_request = $this->Radiology_request->select($radiology_request_id, $select);

                        if (count($radiology_request) == 0) {
                            $this->REST_Return(404, 'there is no radiology_request with id ' . $radiology_request_id);
                            return;
                        }

                        $soap_id = $radiology_request[0]->soap_id;

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

                        $dt_radiology_request_item = array(
                            'radiology_request_item_id' => $id,
                            'status'                    => 1,
                            'url'                       => '',
                            'date_added'                => date('Y-m-d H:i:s')
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

                            if ($extension == NULL) {
                                $this->REST_Return(400, 'extension field is required');
                                return;
                            }

                            $extension = explode('/', $extension)[1];

                            $bytes = random_bytes(6 / 2);
                            $random_6 = substr(bin2hex($bytes), 0, 6);
                      
                            $url = $this->base64ToFile($result, $random_6 . '-' . $costing_name . '-' . $date . '-' . $norm . '-' . $patient_name . '.' . $extension);
                            
                            $dt_radiology_request_item['url'] = $url;
                        } 

                        $id = $this->Radiology_request_item_image->create($dt_radiology_request_item);
                    }
                } else {
                    $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
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
        define('UPLOAD_DIR', 'assets/document/usg/');
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name;
        $success      = file_put_contents($file, $data);
        return $file;
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
                $this->Registration->whereFields['b.id'] = $output->rows[$i]->registration_destination_id;
                $resgistration_data = $this->Registration->read(0, false)[0];
                $insurance_data = $this->Registration_insurance->read($output->rows[$i]->registration_destination_id, false)[0];
                $this->Radiology_request_item->whereFields['a.is_delete'] = 0;
                $radiology_request_item = $this->Radiology_request_item->read($output->rows[$i]->id);
                $output->rows[$i]->code = $resgistration_data->code;
                $output->rows[$i]->register_date = $resgistration_data->register_date;
                $output->rows[$i]->insurance_name = $insurance_data->insurance_name;
                $this->Registration_insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
                $this->Registration_Insurance->whereFields['a.is_primary'] = 1; 
                $this->Registration_insurance->whereFields['a.registration_destination_id'] = $output->rows[$i]->registration_destination_id;
                $reg_insurance = $this->Registration_insurance->read();
                for ($j=0; $j < count($reg_insurance); $j++) { 
                    $output->rows[$i]->registration_insurance[$j] = $reg_insurance[$j];
                }
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
                if ($radiology_request_item != []) {
                    for ($j=0; $j < count($radiology_request_item); $j++) { 
                        $output->rows[$i]->radiology_request_item[$j]['no'] = $no;
                        $output->rows[$i]->radiology_request_item[$j]['id'] = $radiology_request_item[$j]->id;
                        $output->rows[$i]->radiology_request_item[$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
                        $output->rows[$i]->radiology_request_item[$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
                        $output->rows[$i]->radiology_request_item[$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
                        $output->rows[$i]->radiology_request_item[$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
                        $output->rows[$i]->radiology_request_item[$j]['var_1'] = $radiology_request_item[$j]->var_1;
                        $output->rows[$i]->radiology_request_item[$j]['url'] = $this->Radiology_request_item_image->read($radiology_request_item[$j]->id);
                        $output->rows[$i]->radiology_request_item[$j]['var_2'] = $radiology_request_item[$j]->var_2;
                        $output->rows[$i]->radiology_request_item[$j]['var_3'] = $radiology_request_item[$j]->var_3;
                        $output->rows[$i]->radiology_request_item[$j]['date_request'] = $radiology_request_item[$j]->date_request;
                        $output->rows[$i]->radiology_request_item[$j]['date_process'] = $radiology_request_item[$j]->date_process;
                        $output->rows[$i]->radiology_request_item[$j]['notes'] = $radiology_request_item[$j]->notes;
                        $output->rows[$i]->radiology_request_item[$j]['status'] = $radiology_request_item[$j]->status;
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
            $registration_id = $q_strings['registration_id'] ?? null;
            if ($registration_destination_id == null) {
                if ($registration_id == null) {
                    $this->REST_Return(400, 'registration_id is required if registration_destination_id is not provided');
                    return;
                } else {
                    $where_main['rd.registration_id'] = $registration_id;
                    $where_main['a.status'] = $q_strings['status'];
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
    
        $select = 'a.id, a.no_request, a.date_request, a.status, a.clinical_indications, s.id as soap_id, a.doctor_in_charge_id, e.fullname as doctor_in_charge_name, a.queue,
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
        $output = $this->Radiology_request->select(0, $select, $where_main, $join_main, $order_main);

        for ($i=0; $i < count($output); $i++) { 
            $radiology_request_id = $output[$i]->id;

            $select = 'a.id, a.radiology_request_id, a.radiology_package_id, rp.name as radiology_package_name, a.service_class_id, a.status';
            $where = array(
                'a.radiology_request_id' => $radiology_request_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('radiology_package rp', 'a.radiology_package_id = rp.id', 'INNER'),
            );
            $radiology_request_items = $this->Radiology_request_item->select(0, $select, $where, $join);

            $url_true = 0;
            
            for ($k=0; $k < count($radiology_request_items); $k++) { 
                $radiology_request_item_id = $radiology_request_items[$k]->id;

                $select = 'a.id,
                a.radiology_request_item_id,
                e.fullname as doctor_lab_name,
                e2.fullname as user_lab_name,
                a.date_request,
                a.date_process,
                a.date_result,
                a.conclusion,
                a.advice,
                a.url
                ';
                $where = array(
                    'a.radiology_request_item_id' => $radiology_request_item_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('employee e', 'a.doctor_lab_id = e.id', 'LEFT'),
                    array('employee e2', 'a.user_lab_id = e2.id', 'LEFT'),
                );
                $url = $this->Radiology_request_item_image->select(0, $select, $where, $join);

                for ($l=0; $l < count($url); $l++) { 
                    $url_raw = $url[$l]->url;
                    $name = substr($url_raw, 20);

                    $url[$l]->radiology_request_id = $radiology_request_id;
                    $url[$l]->name = $name;
                    $url_true = 1;
                }

                $radiology_request_items[$k]->url = $url;
            }

            $output[$i]->radiology_request_items = $radiology_request_items;

            if ($url_true == 1) {
                $output[$i]->result_done = 1;
            } else {
                $output[$i]->result_done = 0;
            }
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
            // $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                /* Start Transaction */
                $this->db->trans_start();
                // $this->Radiology_request_item->whereFields['a.id'] = $id;
                $laboratory_request = $this->Radiology_request->read($id);
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
                    $this->Radiology_request->update($id,$dt_laboratory_request);
                    $outputdata = $this->Radiology_request_item->read($id);
                    if($outputdata){
                        for ($i=0; $i < count($outputdata); $i++) { 
                            $dt_laboratory_request_item = array(
                                'id'             => $outputdata[$i]->id,
                                'date_result'    => date('Y-m-d H:i:s'),
                            );
                            $this->Radiology_request_item->replace($dt_laboratory_request_item);
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Radiology_request with id ' . $id . ' does not exist!');
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
    
        $radiology_request_id = $this->$model->select_id($id);
    
        if ($radiology_request_id == "") {
            $this->REST_Return(404, 'there is no radiology_request with id ' . $id);
            return;
        }

        $select = 'a.id';
        $where = array('radiology_request_id' => $radiology_request_id);
        $radiology_request_item = $this->Radiology_request_item->select(0, $select, $where);

        if (count($radiology_request_item) > 0) {
            $dt_radiology_request_item = array(
                'is_delete' => $is_delete,
            );

            $this->Radiology_request_item->update($radiology_request_id, $dt_radiology_request_item);

            if ($this->db->trans_status() === false) {
                $this->db->trans_rollback();
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_radiology_request_item,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to delete radiology_request_item', $response_data);
                return;
            }
        } 
        
        $dt_radiology_request = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($radiology_request_id, $dt_radiology_request);
    
        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_radiology_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete radiology_request', $response_data);
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
    public function report_detail_detail_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $sheet->setCellValue('A2', "NORM"); 
        $sheet->setCellValue('B2', "Nama Pasien"); 
        $sheet->setCellValue('C2', "Penjamin"); 
        $sheet->setCellValue('D2', "Unit");
        $sheet->setCellValue('E2', "Poli/Ruangan"); 
        $sheet->setCellValue('F2', "Tgl.Permintaan"); 
        $sheet->setCellValue('G2', "Kategori"); 
        $sheet->setCellValue('J2', "Nama Pemeriksaan"); 
        $response_data_rad = $this->db->query("
        SELECT `si`.`registration_destination_id`, `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, `a`.`date_request`, `bb`.`id` as `result`,  `bb`.`radiology_request_item_id`, `b`.`radiology_request_id`
        FROM `radiology_request` as `a` 
        LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
        INNER JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
        LEFT JOIN costing c ON b.costing_id = c.id
        LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
        LEFT JOIN soap si ON a.soap_id = si.id
        WHERE `a`.`date_request` >= '2025-09-01 00:00:00' AND `a`.`date_request` <= '2025-09-31 23:59:59' 
        AND `a`.`is_delete` = 0 AND `a`.`status` = 2 AND `bb`.`status` = 2 AND `b`.`is_delete` = 0 AND `bb`.`is_delete` = 0 GROUP BY `bb`.`id` ORDER BY `a`.`date_request` ASC")->result();
        // $sheet->setCellValue('A'.$numrow, $output[$i]->norm);
        // $sheet->setCellValue('B'.$numrow, $output[$i]->nama_pasien);
        // $sheet->setCellValue('C'.$numrow, $output[$i]->insurance.' '.$output[$i]->penjamin);
        // $sheet->setCellValue('D'.$numrow, $output[$i]->Unit);
        // $sheet->setCellValue('E'.$numrow, $output[$i]->Unit_sub);
        // $sheet->setCellValue('F'.$numrow, date('d/m/Y', strtotime($output[$i]->tgl_masuk)));
        // $sheet->setCellValue('G'.$numrow, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
        $numrow_detail = 3;
        $numrow++;
        for ($rad=0; $rad < count($response_data_rad); $rad++) {
            $output = $this->db->query("
			SELECT `r`.`id`, `r`.`code` as `registrasi_code`, `h2`.`name` as `insurance`,  `ri`.`is_cob`, `r`.`norm`, `c`.`name` as `penjamin` , `cc`.`name` as `penjamin_cob`, `r`.`register_date`, p.fullname as nama_pasien, `rd`.`id` as `registration_destination_id`, `rd`.`id` as `registration_destination_id`, `hf`.`name` as Unit, `hfi`.`name` as Unit_sub, `rd`.`checkin_date` as `tgl_masuk`, `rd`.`checkout_date` as `tgl_keluar`
            FROM `registration` as `r` 
            LEFT JOIN `registration_destination` `rd` ON `r`.`id` = `rd`.`registration_id` 
            LEFT JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary` = 1
            INNER JOIN patient p ON p.norm = r.norm
            LEFT JOIN helper h2 ON h2.code = ri.insurance_id AND h2.variable = 'insurance'
            LEFT JOIN company c ON ri.company_id = c.id
            LEFT JOIN company cc ON ri.company_id_cob = cc.id
            INNER JOIN health_facility_item hfi ON hfi.id = rd.health_facility_item_id
            INNER JOIN health_facility hf ON hf.id = hfi.health_facility_id
            WHERE `r`.`is_cancel` = 0 AND
            `rd`.`id` = '{$response_data_rad[$rad]->registration_destination_id}'  AND `ri`.`is_primary` = 1 
            ORDER BY `r`.`register_date` ASC
            ")->result();
            $cob = $output[0]->is_cob == 1 ? 'COB'.' '. $output[0]->penjamin_cob : '';
            $sheet->setCellValue('A'.$numrow_detail, $output[0]->norm);
            $sheet->setCellValue('B'.$numrow_detail, $output[0]->nama_pasien);
            $sheet->setCellValue('C'.$numrow_detail, $output[0]->insurance.' '.$output[0]->penjamin .' '.$cob);
            $sheet->setCellValue('D'.$numrow_detail, $output[0]->Unit);
            $sheet->setCellValue('E'.$numrow_detail, $output[0]->Unit_sub);
            $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($response_data_rad[$rad]->date_request)));
            $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($response_data_rad[$rad]->date_request ? $response_data_rad[$rad]->date_request : $response_data_rad[$rad]->date_request)));
            $sheet->setCellValue('H'.$numrow_detail, $response_data_rad[$rad]->desc);
            $sheet->setCellValue('I'.$numrow_detail, $response_data_rad[$rad]->bride_department_sub);
            $sheet->setCellValue('J'.$numrow_detail, $response_data_rad[$rad]->costing_name);
            $sheet->setCellValue('K'.$numrow_detail, $response_data_rad[$rad]->tarif);
            $sheet->setCellValue('L'.$numrow_detail, 1);
            $sheet->setCellValue('M'.$numrow_detail, $response_data_rad[$rad]->total);
            $sheet->setCellValue('N'.$numrow_detail, $response_data_rad[$rad]->date_request);
            $sheet->setCellValue('O'.$numrow_detail, 'tet');
            $sheet->setCellValue('P'.$numrow_detail, 'http://192.168.100.3:8080/radiology/list/process/print-expertise/'.$response_data_rad[$rad]->radiology_request_id.'/'.$response_data_rad[$rad]->radiology_request_item_id.'/'.$response_data_rad[$rad]->result);


            $numrow_detail++; // Tambah 1 setiap kali looping
            $numrow++;
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Laporan Pemeriksaan Radiologi");
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Pemeriksaan Radiologi.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
	}
}