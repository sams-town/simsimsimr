<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Operating_room extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_operator_model", "{$this->name}_operator");
        $this->load->model("$this->directory/Bed_model", "Bed");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Icd9cm_model", "Icd9cm");
        $this->load->model("v1/mr/Icd10_model", "Icd10");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Registration_insurance_model", "Registration_insurance");
        $this->load->model("v1/mr/Registration_insurance_model", "Registration_insurance");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_a_model", "Soap_a");
        $this->load->model("v1/mr/Soap_a_action_model", "Soap_a_action");
        $this->load->model("v1/mr/Soap_p_model", "Soap_p");
        $this->load->model("v1/mr/Soap_p_action_model", "Soap_p_action");
        $this->load->model("v1/mr/Rm_bpjs_operasi_model", "Rm_bpjs_operasi");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/mr/Rm_bpjs_operasi_model", "Rm_bpjs_operasi");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/User_model", "User");
        // Load Helper
        $this->load->helper('queue');
        $this->load->helper('exists');
        $this->load->helper('performance');

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'main_operator_id', 'label' => 'main_operator_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'main_anesthesia_id', 'label' => 'main_anesthesia_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'operation_date', 'label' => 'operation_date', 'rules' => 'trim|required'),
            array('field' => 'infectious_type_id', 'label' => 'infectious_type_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'operation_type_id', 'label' => 'operation_type_id', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->approve_rules = array(
            array('field' => 'bed_id', 'label' => 'bed_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'operation_approved_date', 'label' => 'operation_approved_date', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'main_operator_id', 'label' => 'main_operator_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'main_anesthesia_id', 'label' => 'main_anesthesia_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->add_operator_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'operators[]', 'label' => 'operators', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function detail_get($p = 0) {
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
    
        $operating_room_id = $id;

        $where = [];
    
        if ($operating_room_id === 0 || $operating_room_id === "0") {
            $registration_destination_id = $q_strings['registration_destination_id'] ?? null;
            if ($registration_destination_id == null) {
                $this->REST_Return(400, 'registration_destination_id is required if operating_room_id is not provided');
                return;    
            }
            $where['a.registration_destination_id'] = $registration_destination_id;
        }
    
        $select = 'a.*, s.doctor_in_charge_id, e.fullname as main_operator_name';
        $where['a.is_delete'] = 0;
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('employee e', 'a.main_operator_id = e.id', 'INNER'),
        );
        if ($operating_room_id === 0 || $operating_room_id === "0") {
            $output = $this->$model->select(0, $select, $where, $join);
        } else {
            $output = $this->$model->select($operating_room_id, $select, $where, $join);
        }

        if (count($output) > 0) {
            $soap_id = $output[0]->soap_id;
            $doctor_in_charge_id = $output[0]->doctor_in_charge_id;
            
            $pre_icd10 = [];
            $pre_non_icd10 = [];

            $select = 'a.id, a.non_icd10';
            $where = array(
                'soap_id' => $soap_id
            );
            $soap_a = $this->Soap_a->select(0, $select, $where);
            if (count($soap_a) > 0) {
                $soap_a_id = $soap_a[0]->id;
                $pre_non_icd10 = ($soap_a[0]->non_icd10 != "") ? explode('|', $soap_a[0]->non_icd10) : [];
    
                $select = 'i.id as icd10_id, i.name as icd10_name, i.code as icd10_code';
                $where = array(
                    'a.soap_a_id' => $soap_a_id,
                );
                $join = array(
                    array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
                );
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join);
    
                $pre_icd10 = $soap_a_action;
            }

            $select = 'a.id, a.therapy_plan';
            $where = array(
                'soap_id' => $soap_id,
                'doctor_in_charge_id' => $doctor_in_charge_id,
            );
            $soap_p = $this->Soap_p->select(0, $select, $where);

            $therapy_plan = $soap_p[0]->therapy_plan;

            $output[0]->pre_icd10 = $pre_icd10;
            $output[0]->pre_non_icd10 = $pre_non_icd10;
            $output[0]->therapy_plan = $therapy_plan;
        }

        $response_data = array();
    
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
    public function list_get($p = 0){
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
    
        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        unset($q_strings['limit']);
        
        $this->$model->offset = $offset; 
        unset($q_strings['offset']);
    
        $date_start = date('Y-m-d') . ' 00:00:00';
        $date_end = date('Y-m-d') . ' 23:59:59';

        $where_main = $q_strings;
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_type = $q_strings['date_type'] ?? null;

            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            if ($date_type == 'approved_date') {
                $where_main['a.operation_approved_date >='] = $date_start;
                $where_main['a.operation_approved_date <='] = $date_end;
            } else {
                $where_main['a.operation_date >='] = $date_start;
                $where_main['a.operation_date <='] = $date_end;
            }
            
            unset($where_main['date_start']);
            unset($where_main['date_end']);
            unset($where_main['date_type']);
        } 
        
        if(isset($q_strings['norm'])) {
            $where_main['p.norm'] = $q_strings['norm'];
            unset($where_main['norm']);
        }
        
        if(isset($q_strings['registration_id'])) {
            $where_main['rd.registration_id'] = $q_strings['registration_id'];
            unset($where_main['registration_id']);
        }

        if(isset($q_strings['patient_name'])) {
            $this->$model->whereLike['p.name'] = $q_strings['patient_name'];
            unset($where_main['patient_name']);
        }

        if(isset($q_strings['registration_id'])) {
            $where_main['rd.registration_id'] = $q_strings['registration_id'];
            unset($where_main['registration_id']);
        }
        
        $select = 'a.*,
        hfi.name as health_facility_item_name,
        p.norm, 
        p.fullname as patient_name, 
        p.birth_date, 
        j.name as sex_name, 
        h.name as alias_name, 
        b.name as bed_name, 
        rm.id as room_id, 
        rm.name as room_name, 
        rm.number as room_number, 
        e.fullname as employee_approved_name,
        sa.id as soap_a_id, 
        sa.icd10_notes,
        sa.non_icd10,
        c.name as company_name,
        ri.service_class_name
        ';
        $where_main['a.is_delete'] = 0;
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id', 'INNER'),
            array('company c', 'ri.company_id = c.id OR (ri.insurance_id = "1" AND c.id = "e5a1e978-af77-4901-9ef8-4ebaeee77dfa")', 'LEFT'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', "p.alias = h.code and variable='alias'", 'INNER'),
            array('helper j', 'p.sex = j.code AND j.variable="sex"', 'INNER'),
            array('bed b', 'a.bed_id = b.id', 'LEFT'),
            array('room rm', 'b.room_id = rm.id', 'LEFT'),
            array('employee e', 'a.employee_approved_id = e.id', 'LEFT'),
            array('soap_a sa', 'a.soap_id = sa.soap_id', 'LEFT'),
        );
        $output = $this->$model->select(0, $select, $where_main, $join);

        for ($i=0; $i < count($output); $i++) { 
            $operating_room_id = $output[$i]->id;
            $soap_id = $output[$i]->soap_id;
            $soap_a_id = $output[$i]->soap_a_id;
            $soap_p_id = $output[$i]->soap_p_id;
            
            $output[$i]->non_icd10 = ($output[$i]->non_icd10 == null) ? array() : explode('|', $output[$i]->non_icd10);
            $output[$i]->accomodation_arry = ($output[$i]->accomodation_arry != "") ? explode(',', $output[$i]->accomodation_arry) : [];

            if ($date_type == 'approved_date_estimate') {
                $output[$i]->operation_approved_date = $output[$i]->operation_date;
            }

            $select = 'a.id';
            $soap = $this->Soap->select($soap_id, $select);

            $soap_id = $soap[0]->id;

            // SOAP_A
            $output[$i]->icd10 = [];

            if ($soap_a_id != '') { 
                
                $select = 'a.id, a.icd10_id, a.index, a.is_delete, i.code as icd10_code, i.name as icd10_name';
                $where = array('soap_a_id' => $soap_a_id);
                $join = array(
                    array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
                );
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join);
    
                $icd10 = $soap_a_action;

                $output[$i]->icd10 = $icd10;
            }

            // SOAP_P
            // $output[$i]->icd9cm = [];

            // if ($soap_p_id != '') {
            //     $select = 'a.id, a.icd9cm_id, a.is_delete, i.code as icd9cm_code, i.name as icd9cm_name';
            //     $where = array('soap_p_id' => $soap_p_id, 'a.is_delete' => 0);
            //     $join = array(
            //         array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
            //     );
            //     $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);
    
            //     $icd9cm = $soap_p_action;
    
            //     $output[$i]->icd9cm = $icd9cm;
            // }

            $select = 'a.*,e.fullname as operator_name';
            $where = array('operating_room_id' => $operating_room_id);
            $join = array(
                array('employee e', 'a.operator_id = e.id', 'LEFT'),
            );
            $operators = $this->Operating_room_operator->select(0, $select, $where, $join);

            $output[$i]->operators = $operators;
        }

        $join = 'empty_join';

        if(isset($q_strings['norm']) || isset($q_strings['patient_name']) || isset($q_strings['registration_id'])) {
            $join = array(
                array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
                array('registration r', 'rd.registration_id = r.id', 'INNER'),
                array('patient p', 'r.norm = p.norm', 'INNER'),
            );
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where_main, $join)[0]->iTotal;   

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function operator_get($p = 0) {
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
    
        $operating_room_id = $id;
    
        if ($operating_room_id === 0 || $operating_room_id === "0") {
            $this->REST_Return(400, 'operating_room_id is required');
            return;
        }

        $select = 'a.id';
        $operating_room = $this->$model->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }
    
        $select = 'a.*, e.fullname as operator_name, e.ttd';
        $where = array('operating_room_id' => $operating_room_id, 'a.is_delete' => 0);
        $operators = $this->Operating_room_operator->select(0, $select, $where);

        $output = $operators;
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

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
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $registration_destination_id = $dt_post['registration_destination_id'];
        $soap_id = $dt_post['soap_id'];
        $main_operator_id = $dt_post['main_operator_id'];

        $select = 'a.id, a.registration_id, a.health_facility_item_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $registration_id = $registration_destination[0]->registration_id;
        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $registration_id = $registration_destination[0]->registration_id;
        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id';
        $soap = $this->Soap->select($soap_id, $select);

        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($main_operator_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $main_operator_id);
            return;
        }

        $operation_date = $dt_post['operation_date'];
        $infectious_type_id = $dt_post['infectious_type_id'];
        $operation_type_id = $dt_post['operation_type_id'];
        $main_anesthesia_id = $dt_post['main_anesthesia_id'];
        $duration = $dt_post['duration'];
        // $accomodation_arry = implode(',', $dt_post['accomodation_arry']);
        
        // SOAP_P
        $icd9cm_arr = $dt_post['icd9cm'];
        $therapy_plan = $dt_post['therapy_plan'];

        // count no_order
        $prefix = 'OK';
        $epoch = strtotime($operation_date);
        $dt_queue = array(
            'var_2' => date('Y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('Y-m-d', $epoch),
        );

        /* Start Transaction */
        $this->db->trans_start();

        $no_order = counter_queue($prefix, $dt_queue, 4);
    
        $dt_operating_room = array(
            'no_order'                      => $no_order,
            'registration_destination_id'   => $registration_destination_id,
            'soap_id'                       => $soap_id,
            'main_operator_id'              => $main_operator_id,
            'operation_date'                => $operation_date,
            'infectious_type_id'            => $infectious_type_id,
            'operation_type_id'             => $operation_type_id,
            'therapy_plan'                  => $therapy_plan,
            'duration'                      => $duration,
            // 'accomodation_arry'             => $accomodation_arry,
            'status'                        => 1,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_operating_room);

        $dt_operating_room_operator_1 = array(
            'operating_room_id'     => $id,
            'operator_id'           => $main_operator_id,
            'operator_role_type_id' => 1,
        );

        $this->Operating_room_operator->create($dt_operating_room_operator_1);

        if ($main_anesthesia_id != '' && $main_anesthesia_id != null) {
            $dt_operating_room_operator_2 = array(
                'operating_room_id'     => $id,
                'operator_id'           => $main_anesthesia_id,
                'operator_role_type_id' => 5,
            );
    
            $this->Operating_room_operator->create($dt_operating_room_operator_2);
        }


        // SOAP_P
        $select = 'a.id, a.doctor_in_charge_id';
        $where = array('registration_destination_id' => $registration_destination_id);
        $join = array();
        $order = array('a.date_added' => 'DESC');
        $soap = $this->Soap->select(0, $select, $where, $join, $order);

        $soap_id = $soap[0]->id;
        $doctor_in_charge_id = $soap[0]->doctor_in_charge_id;

        // $dt_soap_p = array(
        //     'soap_id' => $soap_id,
        //     'doctor_in_charge_id' => $doctor_in_charge_id,
        //     'therapy_plan' => $therapy_plan,
        // );

        // $select = 'a.id';
        // $where = array('soap_id' => $soap_id);
        // $soap_p = $this->Soap_p->select(0, $select, $where);
        // if (count($soap_p) == 0) {
        //     $soap_p_id = $this->Soap_p->create($dt_soap_p);
        // } else {
        //     $soap_p_id = $soap_p[0]->id;
        //     $this->Soap_p->update_v2($soap_p_id, $dt_soap_p);
        // }

        // SOAP_P_ACTION
        if(isset($dt_post['icd9cm'])) {
            for ($i=0; $i < count($icd9cm_arr) ; $i++) { 
                if (!isset($icd9cm_arr[$i]['icd9cm_id'])) {
                    $this->REST_Return(400, 'icd9cm_id in icd0cm is required');
                    return;
                }

                $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];

                $select = 'a.id';
                $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);

                if (count($icd9cm) == 0) {
                    $this->REST_Return(404, 'there is no icd9cm with id ' . $icd9cm_id);
                    return;
                }
                
                $dt_soap_p_action = array(
                    'soap_p_id'         => $soap_p_id,
                    'icd9cm_id'         => $icd9cm_id
                );

                $this->Soap_p_action->create($dt_soap_p_action);
            }
        }

        // INPUT BPJS ----------------------------------
        $select = 'a.id, a.norm, p.fullname';
        $where = array();
        $join = array(
            array('patient p', 'a.norm = p.norm', 'LEFT'),
        );
        $registration = $this->Registration->select($registration_id, $select, $where, $join);

        $nm_mst_pasien = $registration[0]->fullname;

        $select = 'a.registration_destination_id, a.number_insurance';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_primary' => 1,
        );
        $registration_insurance = $this->Registration_insurance->select(0, $select, $where);

        $nomorkartu = $registration_insurance[0]->number_insurance;

        $select = 'a.id, a.bride_bpjs, a.bride_bpjs_name';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

        $kodepoli = $health_facility_item[0]->bride_bpjs;
        $namapoli = $health_facility_item[0]->bride_bpjs_name;

        if ($kodepoli == null || $namapoli == null) {
            // $this->REST_Return(422, 'bride_bpjs_code & bride_bpjs_code cannot be null');
            // return;
        } else {
            $tindakan_id = $therapy_plan;
    
            // $select = 'a.code, a.name';
            // $tindakan = $this->Icd9cm->select($tindakan_id, $select);
    
            $kodetindakan = NULL;
            $namatindakan = $tindakan_id;
    
            if ($operation_type_id == 1) {
                $jenis_operasi = 'CITO';
            } elseif ($operation_type_id == 2) {
                $jenis_operasi = 'ELECTIVE';
            } elseif ($operation_type_id == 3) {
                $jenis_operasi = 'One Day Care (ODC)';
            } 
    
            $jenis_operasi = $operation_type_id;
    
            $tanggal = date('Y-m-d', strtotime($operation_date));
            $jam_operasi = date('H:i:s', strtotime($operation_date));
    
            // count kodebooking
            $prefix = '0070R032OK';
            $epoch = strtotime($tanggal);
            $dt_queue = array(
                'var_2' => date('y', $epoch),
                'var_3' => date('m', $epoch),
                'var_4' => date('d', $epoch),
                'var_5' => date('ymd', $epoch),
            );
    
            $kodebooking = counter_queue($prefix, $dt_queue, 4);
            $queue = round((int)substr($kodebooking, -4));
    
            $dt_rm_bpjs_operasi = array(
                'kodebooking'                   => $kodebooking,
                'operating_room_id'             => $id,
                'registration_destination_id'   => $registration_destination_id,
                'nm_mst_pasien'                 => $nm_mst_pasien,
                'kodepoli'                      => $kodepoli,
                'namapoli'                      => $namapoli,
                'tindakan'                      => $tindakan_id,
                'kodetindakan'                  => $kodetindakan,
                'namatindakan'                  => $namatindakan,
                'tanggal'                       => $tanggal,
                'jam_operasi'                   => $jam_operasi,
                'queue'                         => $queue,
                'status'                        => 0,
                'nomorkartu'                    => $nomorkartu,
                'asa'                           => '',
                'intubasi'                      => '',
                'jenis_operasi'                 => $jenis_operasi,
                'flag_prioritas'                => 0,
                'flag_infectious'               => 0,
                'flag_acc'                      => 0,
            );
        
            $this->Rm_bpjs_operasi->replace($dt_rm_bpjs_operasi);
        }
        

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operating_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create operating_room', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;         
    }

    public function update_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $operating_room_id = $id;
    
        if ($operating_room_id === 0 || $operating_room_id === "0") {
            $this->REST_Return(400, 'operating_room_id is required');
            return;
        }
    
        $select = "a.id, a.registration_destination_id, a.soap_id, a.operation_approved_date, a.employee_approved_date";
        $operating_room = $this->$model->select($operating_room_id, $select);
        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'operating_room_id with id ' . $operating_room_id . ' does not exist!');
            return;
        }

        $employee_approved_date = $operating_room[0]->employee_approved_date;
        $old_operation_approved_date = $operating_room[0]->operation_approved_date;

        $main_operator_id = $dt_put['main_operator_id'];
        $main_anesthesia_id = $dt_put['main_anesthesia_id'];
        // $operation_date = $dt_put['operation_date'];
        $infectious_type_id = $dt_put['infectious_type_id'];
        $operation_type_id = $dt_put['operation_type_id'];
        $operation_approved_date = $dt_put['operation_approved_date'];
        // $accomodation_arry = implode(',', $dt_put['accomodation_arry']);
        $duration = $dt_put['duration'];
        $bed_id = $dt_put['bed_id'];

        $employee_approved_date = $operating_room[0]->employee_approved_date;
        if ($operation_approved_date != $old_operation_approved_date) {
            $employee_approved_date = date('Y-m-d H:i:s');
        }

        $select = 'a.id';
        $employee = $this->Employee->select($main_operator_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $main_operator_id);
            return;
        }

        $select = 'a.id';
        $employee2 = $this->Employee->select($main_anesthesia_id, $select);
        if (count($employee2) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $main_anesthesia_id);
            return;
        }

        $select = 'a.id';
        $bed = $this->Bed->select($bed_id, $select);
        if (count($bed) == 0) {
            $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();
        $therapy_plan = $dt_put['therapy_plan'];
    
        $dt_operating_room = array(
            'main_operator_id'      => $main_operator_id,
            'bed_id'                => $bed_id,
            // 'operation_date'     => $operation_date,
            'therapy_plan'          => $therapy_plan,
            'duration'              => $duration,
            'infectious_type_id'    => $infectious_type_id,
            'operation_type_id'     => $operation_type_id,
            'operation_approved_date' => $operation_approved_date,
            'employee_approved_date'  => $employee_approved_date,
            // 'accomodation_arry'     => $accomodation_arry,
            'user_updated'          => $user_id,
            'date_updated'          => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update_v2($operating_room_id, $dt_operating_room);

        // main operator
        $select = 'a.id';
        $where = array('operating_room_id' => $operating_room_id, 'operator_role_type_id' => 1, 'a.is_delete' => 0);
        $operating_room_operator = $this->Operating_room_operator->select(0, $select, $where);
        if (count($operating_room_operator) > 0) {
            $operating_room_operator_id = $operating_room_operator[0]->id;
    
            $dt_operating_room_operator = array(
                'operator_id' => $main_operator_id
            );
    
            $this->Operating_room_operator->update_v2($operating_room_operator_id, $dt_operating_room_operator);
        }

        // main anesthesia
        $select = 'a.id';
        $where = array('operating_room_id' => $operating_room_id, 'operator_role_type_id' => 5, 'a.is_delete' => 0);
        $operating_room_operator = $this->Operating_room_operator->select(0, $select, $where);
        if (count($operating_room_operator) > 0) {
            $operating_room_operator_id = $operating_room_operator[0]->id;
    
            $dt_operating_room_operator = array(
                'operator_id' => $main_anesthesia_id
            );
    
            $this->Operating_room_operator->update_v2($operating_room_operator_id, $dt_operating_room_operator);
        }

        // // SOAP_P
        // $soap_id = $operating_room[0]->soap_id;

        // $select = 'a.id';
        // $where = array('soap_id' => $soap_id);
        // $soap_p = $this->Soap_p->select(0, $select, $where);

        // $soap_p_id = $soap_p[0]->id;

        // $therapy_plan = $dt_put['therapy_plan'];

        // $dt_soap_p = array(
        //     'therapy_plan' => $therapy_plan,
        // );
        // $this->Soap_p->update_v2($soap_p_id, $dt_soap_p);

        // SOAP_P_ACTION
        if(isset($dt_put['icd9cm'])) {
            $this->Soap_p_action->whereFields['soap_p_id'] = $soap_p_id;
            $soap_p_action = $this->Soap_p_action->read(0,false);
            $icd9cm_arr = $dt_put['icd9cm'];
            $tmp_delete_icd9cm_arr = array();
            // adding all current items (db) to temporary variable
            for ($i=0; $i < count($soap_p_action); $i++) { 
                $tmp_delete_icd9cm_arr[$soap_p_action[$i]->icd9cm_id] = $soap_p_action[$i]->id;
            }
            for ($i=0; $i < count($icd9cm_arr) ; $i++) { 
                $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];
                if(is_exist('Icd9cm', $icd9cm_id) == []) {
                    $this->REST_Return(404, 'There is no icd9cm with id ' . $icd9cm_id);
                    return;
                }

                $dt_soap_p_action = array(
                    'soap_p_id'         => $soap_p_id,
                    'icd9cm_id'         => $icd9cm_id,
                    'is_delete'         => 0,
                    'user_added'        => $user_id,
                    'user_updated'      => $user_id,
                );

                // create or update items according to the items provided and unset used items from temporary variable
                if(array_key_exists($icd9cm_id,$tmp_delete_icd9cm_arr) == 1) {
                    $dt_soap_p_action['id'] = $tmp_delete_icd9cm_arr[$icd9cm_id];
                    $this->Soap_p_action->replace($dt_soap_p_action);

                    unset($tmp_delete_icd9cm_arr[$icd9cm_id]);
                } else {
                    $this->Soap_p_action->create($dt_soap_p_action);
                }

            }

            // delete all items in temporary variable
            foreach ($tmp_delete_icd9cm_arr as $icd9cm_id => $soap_p_action_id) {
                $dt_soap_p_action = array(
                    'id'                => $soap_p_action_id,
                    'soap_p_id'         => $soap_p_id,
                    'icd9cm_id'         => $icd9cm_id,
                    'is_delete'         => 1,
                    'user_updated'      => $user_id,
                    'user_deleted'      => $user_id,
                );
                $this->Soap_p_action->replace($dt_soap_p_action);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operating_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update operating_room_id', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function approve_put($p = 0) {
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
        $this->form_validation->set_rules($this->approve_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);

        $employee_approved_id = $user[0]->employee_id;
    
        $operating_room_id = $id;
    
        if ($operating_room_id === 0 || $operating_room_id === "0") {
            $this->REST_Return(400, 'operating_room_id is required');
            return;
        }

        $select = 'a.id, a.status';
        $operating_room = $this->$model->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }

        $operating_room_status = $operating_room[0]->status;

        if ($operating_room_status != 1) {
            $this->REST_Return(409, 'operating_room with id ' . $operating_room_id . ' is already approved');
            return;
        }

        $bed_id = $dt_put['bed_id'];

        $select = 'a.id, a.type_status_bed_id';
        $bed = $this->Bed->select($bed_id, $select);

        if (count($bed) == 0) {
            $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
            return;
        }

        $bed_status_id = $bed[0]->type_status_bed_id;

        if ($bed_status_id != 1) {
            $this->REST_Return(409, 'bed with id ' . $bed_id . ' is currently not available');
            return;
        }

        $status = $dt_put['status'];
        $operation_approved_date = $dt_put['operation_approved_date'];
    
        /* Start Transaction */
        $this->db->trans_start();

        // update status bed to filled
        // $dt_bed = array(
        //     'type_status_bed_id' => 2,
        // );

        // $this->Bed->update($bed_id, $dt_bed);
    
        $dt_operating_room = array(
            'bed_id' => $bed_id,
            'status' => $status,
            'operation_approved_date' => $operation_approved_date,
            'employee_approved_date' => date('Y-m-d H:i:s'),
            'employee_approved_id' => $employee_approved_id,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($operating_room_id, $dt_operating_room);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operating_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to approve operating_room', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function add_operator_post() {
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
        $this->form_validation->set_rules($this->add_operator_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $operating_room_id = $dt_post['operating_room_id'];
        $operators_arr = $dt_post['operators'];

        $select = 'a.id';
        $operating_room = $this->$model->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($operators_arr); $i++) { 
            if (!isset($operators_arr[$i]['operator_id'])) {
                $this->REST_Return(400, 'operator_id field in operators is required');
                return;
            }

            if (!isset($operators_arr[$i]['operator_role_type_id'])) {
                $this->REST_Return(400, 'operator_role_type_id field in operators is required');
                return;
            }

            $operator_id = $operators_arr[$i]['operator_id'];
            $operator_role_type_id = $operators_arr[$i]['operator_role_type_id'];

            $select = 'a.id';
            $operator = $this->Employee->select($operator_id, $select);

            if (count($operator) == 0) {
                $this->REST_Return(404, 'there is no operator with id ' . $operator_id);
                return;
            }

            $dt_operating_room_operator = array(
                'operating_room_id' => $operating_room_id,
                'operator_id' => $operator_id,
                'operator_role_type_id' => $operator_role_type_id,
            );
        
            $this->Operating_room_operator->create($dt_operating_room_operator);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operating_room_operator,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create operating_room_operator', $response_data);
            return;
        }
    
        $response_data = array(
            'operating_room_id'     => $operating_room_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
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
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'operating_room_id is required');
            return;
        }
    
        $operating_room_id = $this->$model->select_id($id);
    
        if ($operating_room_id == "") {
            $this->REST_Return(404, 'there is no operating_room with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_operating_room = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($operating_room_id, $dt_operating_room);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operating_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete operating_room', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function delete_operator_delete($p = 0) {
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
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'operating_room_operator_id is required');
            return;
        }
    
        $operating_room_operator_id = $this->Operating_room_operator->select_id($id);
    
        if ($operating_room_operator_id == "") {
            $this->REST_Return(404, 'there is no operating_room_operator with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_operating_room_operator = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->Operating_room_operator->update($operating_room_operator_id, $dt_operating_room_operator);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operating_room_operator,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete operating_room_operator', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}