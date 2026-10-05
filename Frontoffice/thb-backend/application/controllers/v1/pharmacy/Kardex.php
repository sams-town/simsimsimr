<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Kardex extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/{$this->name}_non_pharmacological_model", "{$this->name}_non_pharmacological");
        $this->load->model("$this->directory/{$this->name}_stock_model", "{$this->name}_stock");
        $this->load->model("$this->directory/{$this->name}_stock_concoction_model", "{$this->name}_stock_concoction");
        $this->load->model("$this->directory/{$this->name}_source_model", "{$this->name}_source");
        $this->load->model("$this->directory/Prescription_model", "Prescription");
        $this->load->model("$this->directory/Prescription_item_model", "Prescription_item");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("$this->directory/Non_pharmacological_model", "Non_pharmacological");
        $this->load->model("$this->directory/Signatura_times_model", "Signatura_times");
        $this->load->model("$this->directory/Catalog_instruction_model", "Administer");
        $this->load->model("v1/mr/inpatient/Consultation_model", "Consultation");
        // $this->load->model("v1/mr/inpatient/Nurse_tasks_model", "Nurse_tasks");
        // $this->load->model("v1/mr/inpatient/Nurse_tasks_item_model", "Nurse_tasks_item");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Cppt_model", "Cppt");
        $this->load->model("v1/mr/Sbar_model", "Sbar");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/setting/Unit_model", "Unit");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        // Load Helper
        $this->load->helper('queue');
        $this->load->helper('performance');
        $this->load->helper('logger');
        $this->load->helper('utils');

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'form_prescription', 'label' => 'form_prescription', 'rules' => 'trim'),
            array('field' => 'form_prescription_parent', 'label' => 'form_prescription_parent', 'rules' => 'trim'),
        );

        $this->order_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->take_order_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->approve_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
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

        $kardex_id = $id;

        if ($kardex_id === 0 || $kardex_id === "0") {
            $this->REST_Return(400, 'kardex_id is required');
            return;
        }

        $select = 'a.*';
        $where = array(
            'a.is_delete' => 0,
        );
        $output = $this->$model->select($kardex_id, $select, $where);

        if (count($output) > 0) {
            $kardex_id = $output[0]->id;
            $registration_destination_id = $output[0]->registration_destination_id;
                        
            $select = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name, e.name as doctor_added_name';
            $where = array(
                'a.kardex_id' => $kardex_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'a.package_id = p.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'LEFT'),
            );
            $order = array('a.doctor_added' => 'ASC', 'a.pharmacy_catalog_id' => 'ASC');
            $kardex_item = $this->Kardex_item->select(0, $select, $where, $join, $order);

            $kardex_parent = [];

            //------------------- detail dt_kardex_parent -//
            for ($l=0; $l < count($kardex_item); $l++) { 
                $pharmacy_catalog_id = $kardex_item[$l]->pharmacy_catalog_id;

                $parent_id = $kardex_item[$l]->prescription_item_type_id; 
                $parent_used = $kardex_item[$l]->prescription_item_type_used; 
                $parent_value = $kardex_item[$l]->prescription_item_type_value; 
                $parent_package = $kardex_item[$l]->prescription_item_type_package; 
                $parent_desc = $kardex_item[$l]->desc; 

                $dt_kardex_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                    'package' => $parent_package,
                    'desc' => $parent_desc,
                );

                if (count($dt_kardex_parent) == 0) {
                    array_push($kardex_parent, $dt_kardex_parent);
                }

                $skip = 0;
                for ($m=0; $m < count($kardex_parent); $m++) { 
                    if ($kardex_parent[$m]['id'] == $parent_id) {
                        $skip = 1;
                    }
                }

                if ($skip == 0) {
                    array_push($kardex_parent, $dt_kardex_parent);
                }

                $kardex_item[$l]->can_be_annulled = 0;

                $select = 'a.id, ki.id as kardex_item_id, ki.is_annulment, ki.status';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.is_active' => 0,
                    'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join = array(
                    array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                );
                $order = array('a.date_added' => 'DESC');
                $this->Kardex->limit = 1;
                $last_kardex_item = $this->Kardex->select(0, $select, $where, $join, $order);
                if (count($last_kardex_item) > 0) {
                    $is_annulment = $last_kardex_item[0]->is_annulment;
                    $status = $last_kardex_item[0]->status;

                    if ($status == 2 && $is_annulment != 1) {
                        $kardex_item[$l]->can_be_annulled = 1;
                    }
                }
            }
            //----------------------------------------------------//

            $output[0]->kardex_item = $kardex_item;
            $output[0]->kardex_parent = $kardex_parent;

            $select = 'a.id, a.non_pharmacological_id, np.name, a.date, a.doctor_added, e.fullname as doctor_added_name';
            $where = array(
                'a.kardex_id' => $kardex_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'LEFT'),
            );
            $order = array(
                'a.date' => 'ASC',
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);

            $output[0]->kardex_non_pharmacological = $kardex_non_pharmacological;
        }

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

        $where_main = array();

        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;
        if ($registration_destination_id != null) {
            $where_main['a.registration_destination_id'] = $registration_destination_id;
        }

        $is_active = $q_strings['is_active'] ?? null;
        if ($is_active != null) {
            $where_main['a.is_active'] = $is_active;
        }

        $list_type = $q_strings['list_type'] ?? null;
        $list_type_do = false;
        if ($list_type != null) {
            if ($list_type == 'do') {
                $list_type_do = true;
            }
        }

        $select = 'a.*, e.fullname as doctor_in_charge_name, e.nickname as doctor_nickname, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
        $where_main['a.is_delete'] = 0;
        $join_main = array(
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('user u', 'a.user_added = u.id', 'INNER'),
            array('employee e2', 'u.employee_id = e2.id', 'INNER'),
        );
        $order = array(
            'a.date_added' => 'DESC',
        );
        if ($list_type_do) {
            $where_main['pp.type_prescription_id'] = 4;
            $join_main[] = ['pharmacy_prescription pp', 'a.pharmacy_prescription_id = pp.id', 'INNER'];
        }
        $output = $this->$model->select(0, $select, $where_main, $join_main, $order);

        for ($i=0; $i < count($output); $i++) { 
            $kardex_id = $output[$i]->id;
            $registration_destination_id = $output[$i]->registration_destination_id;
                                    
            $select = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name, e.nickname as doctor_nickname, e.name as doctor_added_name, pc.package_id as catalog_package_id,
            ppi.notes,
            ppi.pharmacy_catalog_id_ref,
            pc2.name as pharmacy_catalog_ref_name
            ';
            $where = array(
                'a.kardex_id' => $kardex_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('kardex k', 'a.kardex_id = k.id', 'INNER'),
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'a.package_id = p.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'LEFT'),
                array('pharmacy_prescription_item ppi', 'k.pharmacy_prescription_id = ppi.pharmacy_prescription_id AND ppi.pharmacy_catalog_id = a.pharmacy_catalog_id', 'LEFT'),
                array('pharmacy_catalog pc2', 'ppi.pharmacy_catalog_id_ref = pc2.id', 'LEFT'),
            );
            $order = array('a.doctor_added' => 'ASC', 'a.pharmacy_catalog_id' => 'ASC');
            $kardex_item = $this->Kardex_item->select(0, $select, $where, $join, $order);

            $kardex_parent = [];

            //------------------- detail dt_kardex_parent -//
            for ($l=0; $l < count($kardex_item); $l++) { 
                $pharmacy_catalog_id = $kardex_item[$l]->pharmacy_catalog_id;
                $catalog_package_id = $kardex_item[$l]->catalog_package_id;

                $kardex_item[$l]->dose_unit_options = options_unit_package($catalog_package_id);

                $parent_id = $kardex_item[$l]->prescription_item_type_id; 
                $parent_used = $kardex_item[$l]->prescription_item_type_used; 
                $parent_value = $kardex_item[$l]->prescription_item_type_value; 
                $parent_package = $kardex_item[$l]->prescription_item_type_package; 
                $parent_desc = $kardex_item[$l]->desc; 
                $parent_signatura_times_id = $kardex_item[$l]->signatura_times_id; 
                $parent_administer_id = $kardex_item[$l]->administer_id; 
                $parent_status = $kardex_item[$l]->status; 

                $dt_kardex_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                    'package' => $parent_package,
                    'desc' => $parent_desc,
                    'signatura_times_id' => $parent_signatura_times_id,
                    'administer_id' => $parent_administer_id,
                    'status' => $parent_status,
                );

                if (count($dt_kardex_parent) == 0) {
                    array_push($kardex_parent, $dt_kardex_parent);
                }

                $skip = 0;
                for ($m=0; $m < count($kardex_parent); $m++) { 
                    if ($kardex_parent[$m]['id'] == $parent_id) {
                        $skip = 1;
                    }
                }

                if ($skip == 0) {
                    array_push($kardex_parent, $dt_kardex_parent);
                }

                $kardex_item[$l]->can_be_annulled = 0;

                $select = 'a.id, ki.id as kardex_item_id, ki.is_annulment, ki.status';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.is_active' => 0,
                    'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join = array(
                    array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                );
                $order = array('a.date_added' => 'DESC');
                $this->Kardex->limit = 1;
                $last_kardex_item = $this->Kardex->select(0, $select, $where, $join, $order);
                if (count($last_kardex_item) > 0) {
                    $is_annulment = $last_kardex_item[0]->is_annulment;
                    $status = $last_kardex_item[0]->status;

                    if ($status == 2 && $is_annulment != 1) {
                        $kardex_item[$l]->can_be_annulled = 1;
                    }
                }
            }
            //----------------------------------------------------//

            $output[$i]->kardex_item = $kardex_item;
            $output[$i]->kardex_parent = $kardex_parent;

            $select = 'a.id, a.non_pharmacological_id, np.name, a.doctor_added, e.fullname as doctor_added_name';
            $where = array(
                'a.kardex_id' => $kardex_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'INNER'),
            );
            $order = array(
                'a.date' => 'ASC',
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join, $order);

            $output[$i]->kardex_non_pharmacological = $kardex_non_pharmacological;
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where_main, $join_main)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_put() {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        $select = 'a.id, a.employee_id, e.name as employee_name, ep.position_id';
        $where = array();
        $join = array(
            array('employee e', 'a.employee_id = e.id', 'INNER'),  
            array('employee_position ep', 'a.employee_id = ep.employee_id', 'INNER'),  
        );
        $user = $this->User->select($user_id, $select, $where, $join);
        $user_employee_id = $user[0]->employee_id;
        $user_position_id = $user[0]->position_id;
        $doctor_added = $user[0]->employee_id;
        $user_employee_name = $user[0]->employee_name;
        
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $health_facility_item_id = $dt_put['health_facility_item_id'];
        $soap_id = ($dt_put['soap_id'] == '' ? null: $dt_put['soap_id']);
        $cppt_id = ($dt_put['cppt_id'] == '' ? null: $dt_put['cppt_id']);
        $sbar_id = ($dt_put['sbar_id'] == '' ? null: $dt_put['sbar_id']);
        $form_prescription = $dt_put['form_prescription'];
        $form_prescription_parent = $dt_put['form_prescription_parent'];
        $non_pharmacological = $dt_put['non_pharmacological'];

        $select = 'a.id, r.norm, p.name as patient_name, h.name as alias_name, a.doctor_in_charge_id, u.id as doctor_user_id,
        a.doctor_in_charge_id_2,
        a.doctor_in_charge_id_3,
        a.doctor_in_charge_id_4,
        a.doctor_in_charge_id_5,
        a.doctor_in_charge_id_6,
        a.doctor_in_charge_id_7,
        a.doctor_in_charge_id_8,
        a.doctor_in_charge_id_9,
        a.doctor_in_charge_id_10,
        a.doctor_in_charge_id_11
        ';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'LEFT'),
            array('helper h', 'h.code = p.alias AND h.variable="alias"', 'LEFT'),
            array('user u', 'a.doctor_in_charge_id = u.employee_id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $norm = $registration_destination[0]->norm;
        $patient_name = trim($registration_destination[0]->patient_name);
        $patient_alias = $registration_destination[0]->alias_name;
        $doctor_user_id = $registration_destination[0]->doctor_user_id;
        $dpjp_id = $registration_destination[0]->doctor_in_charge_id;
        $doctor_in_charge_id_2 = $registration_destination[0]->doctor_in_charge_id_2;
        $doctor_in_charge_id_3 = $registration_destination[0]->doctor_in_charge_id_3;
        $doctor_in_charge_id_4 = $registration_destination[0]->doctor_in_charge_id_4;
        $doctor_in_charge_id_5 = $registration_destination[0]->doctor_in_charge_id_5;
        $doctor_in_charge_id_6 = $registration_destination[0]->doctor_in_charge_id_6;
        $doctor_in_charge_id_7 = $registration_destination[0]->doctor_in_charge_id_7;
        $doctor_in_charge_id_8 = $registration_destination[0]->doctor_in_charge_id_8;
        $doctor_in_charge_id_9 = $registration_destination[0]->doctor_in_charge_id_9;
        $doctor_in_charge_id_10 = $registration_destination[0]->doctor_in_charge_id_10;
        $doctor_in_charge_id_11 = $registration_destination[0]->doctor_in_charge_id_11;

        $select = 'a.id';
        $where = array();
        $join = array();
        $employee = $this->Employee->select($doctor_in_charge_id, $select, $where, $join);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }        

        $select = 'a.id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        if ($soap_id != null) {
            $select = 'a.id';
            $soap = $this->Soap->select($soap_id, $select);
            if (count($soap) == 0) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
        }

        if ($cppt_id != null) {
            $select = 'a.id';
            $cppt = $this->Cppt->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        if ($sbar_id != null) {
            $select = 'a.id, a.doctor_in_charge_id_sbar';
            $sbar = $this->Sbar->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
            
            $doctor_in_charge_id_sbar = $sbar[0]->doctor_in_charge_id_sbar;

            $doctor_added = $doctor_in_charge_id_sbar;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $is_consultation = false;
        $is_suggestion = 0;
        $suggestion = false;
        if ($cppt_id != null && $sbar_id == null) {
            $consultation = $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);
            if (count($consultation) > 0) {
                $is_consultation = true;
                $consultation_type_id = $consultation[0]->consultation_type_id;
                if ($consultation_type_id == 3) {  // KONSULTASI SEWAKTU
                    $is_suggestion = 1;
                    $suggestion = true;
                }
            }
            
            if (
                (
                    $health_facility_item_id == '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c' // ICCU
                    || $health_facility_item_id == '6c7d7e53-ff24-4246-85b2-07ea77ac801f' // ICU
                    || $health_facility_item_id == 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d' // ICU ISOLASI
                ) 
                && ($user_employee_id != $dpjp_id)
            ) {
                $is_suggestion = 1;
                $suggestion = true;

                if (count($consultation) == 0) {
                    for ($i=2; $i < 12; $i++) { 
                        $var_1 = "doctor_in_charge_id_$i";
                        if ($user_employee_id == $$var_1) {
                            $is_consultation = true;
                        }
                    }
                }
            }

            // Dokter Umum bukan berupa saran
            if ($user_position_id == 'd93bd445-df1e-451a-bf95-5fc10546c133') {
                $is_suggestion = 0;
                $suggestion = false;
            }
        }

        // Data kardex
        $dt_kardex = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id'         => $doctor_in_charge_id,
            'health_facility_item_id'     => $health_facility_item_id,
            'soap_id'                     => $soap_id,
            'cppt_id'                     => $cppt_id,
            'sbar_id'                     => $sbar_id,
            'is_suggestion'               => $is_suggestion,
            'is_active'                   => 1,
            'user_added'                  => $user_id,
            'date_added'                  => date('Y-m-d H:i:s'),
            'date_updated'                => date('Y-m-d H:i:s'),
        );

        // Mencari kardex aktif untuk meng-update date_updated
        $select = 'a.id, a.cppt_id, a.is_suggestion, a.taken_suggestion_user, e.fullname as taken_suggestion_user_name, a.is_emergency_trigger';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_active' => 1,
            'a.is_delete' => 0,
        );
        $join = array(
            array('user u', 'a.taken_suggestion_user = u.id', 'LEFT'),
            array('employee e', 'u.employee_id = e.id', 'LEFT'),
        );
        $kardex = $this->Kardex->select(0, $select, $where, $join);
        if (count($kardex) == 0) {
            $id = $this->Kardex->create($dt_kardex);
            $kdx_is_suggestion = 0;
            $kdx_is_emergency_trigger = 0;
        } else {
            $id = $kardex[0]->id;
            $kdx_is_suggestion = $kardex[0]->is_suggestion;
            $kdx_is_emergency_trigger = $kardex[0]->is_emergency_trigger;
            $kdx_cppt_id = $kardex[0]->cppt_id;
            $taken_suggestion_user = $kardex[0]->taken_suggestion_user;
            $taken_suggestion_user_name = $kardex[0]->taken_suggestion_user_name;

            if ($kdx_cppt_id != null) {
                $select = 'a.id, e.fullname as cppt_doctor_name';
                $where = array();
                $join = array(
                    array('user u', 'a.user_added = u.id', 'INNER'),
                    array('employee e', 'u.employee_id = e.id', 'INNER'),
                );
                $kdx_cppt = $this->Cppt->select($cppt_id, $select, $where, $join);
                
                $cppt_doctor_name = $kdx_cppt[0]->cppt_doctor_name;
            }

            if ($sbar_id != null) {
                $dt_kardex['is_suggestion'] = $kdx_is_suggestion;
            }

            // Jika bukan usulan dan di-input dari sbar_id maka cppt_id jadi null agar resep yang di-input tidak masuk ke cppt
            if ($kdx_is_suggestion == 0 && $cppt_id != null && $sbar_id != null) {
                $dt_kardex['cppt_id'] = null;
            }

            $dt_kardex['user_updated'] = $user_id;
            $dt_kardex['date_updated'] = date('Y-m-d H:i:s');
            unset($dt_kardex['user_added']);
            unset($dt_kardex['date_added']);

            $this->Kardex->update_v2($id, $dt_kardex);
        }

        if ($kdx_is_suggestion == 1) {
            if ($taken_suggestion_user != $user_id && !$is_consultation) {
                if ($user_employee_id != $dpjp_id) { // NOTE - INI BIAR DPJP BISA LANGSUNG SAVE TANPA PERLU APPROVE DULU - SEMENTARA
                    if ($taken_suggestion_user != null) {
                        $text = "Kardex aktif milik $cppt_doctor_name belum disetujui oleh DPJP atau belum dibuatkan SBAR oleh $taken_suggestion_user_name";
                    } else {
                        $text = "Kardex aktif milik $cppt_doctor_name belum disetujui oleh DPJP atau belum diambil oleh dr jaga";
                    }

                    $this->REST_Return(409, $text);
                    return;
                }
            }
        }

        if ($soap_id != null) {
            $kardex_source = $this->Kardex_source->getSource($id, $soap_id, 1);
            if (count($kardex_source) == 0) {
                $this->Kardex_source->createSource($id, $soap_id, 1);
            }
        }

        if ($cppt_id != null) {
            $kardex_source = $this->Kardex_source->getSource($id, $cppt_id, 2);
            if (count($kardex_source) == 0) {
                $this->Kardex_source->createSource($id, $cppt_id, 2);
            }
        }

        if ($sbar_id != null) {
            $kardex_source = $this->Kardex_source->getSource($id, $sbar_id, 3);
            if (count($kardex_source) == 0) {
                $this->Kardex_source->createSource($id, $sbar_id, 3);
            }
        }

        /* 
            Membuat kardex non farmakologis sekaligus memeriksa 
            apakah item non farmakologis pada resep sudah ada pada kardex aktif
            atau tidak
            
            Jika belum ada akan dibuatkan master untuk non farmakologis baru
        */
        $kdx_non_pharmacological_not_delete_ids = []; // Kumpulan id dari kdx_non_pharmacological yang tidak ingin dihapus
        $fresh_kdx_non_pharmacological = [];
        $kdx_non_pharmacological_changes = false;

        $select = 'a.id, a.doctor_added, a.date';
        $where = array(
            'kardex_id' => $id,
            'is_delete' => 0,
        );
        $kardex_non_pharmacological_count = count($this->Kardex_non_pharmacological->select(0, $select, $where));
        if ($kardex_non_pharmacological_count != count($non_pharmacological)) {
            $kdx_non_pharmacological_changes = true; // Jika jumlah non_pharmacological baru dengan yang aktif berbeda maka terjadi perubahan
        }

        for ($i=0; $i < count($non_pharmacological); $i++) { 
            // Define data non farmakologis
            $non_pharmacological_id = $non_pharmacological[$i]['id'];
            $non_pharmacological_name = $non_pharmacological[$i]['name'];

            // Mencari existing master non farmakologis
            $select = 'a.id';
            $where = array(
                'a.name' => $non_pharmacological_name,
                'a.is_delete' => 0,
            );
            $non_phar = $this->Non_pharmacological->select(0, $select, $where);
            if (count($non_phar) == 0) {
                $dt_non_pharmacological = array(
                    'name' => $non_pharmacological_name,
                );
                $non_pharmacological_id = $this->Non_pharmacological->create($dt_non_pharmacological);
            } else {
                $non_pharmacological_id = $non_phar[0]->id;
            }

            // Data kardex non farmakologis
            $dt_kardex_non_pharmacological = array(
                'kardex_id' => $id,
                'non_pharmacological_id' => $non_pharmacological_id,
                'date' => date('Y-m-d H:i:s'),
                'doctor_added' => $doctor_added,
                'is_delete' => 0,
                'user_added' => $user_id,
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            );

            $select = 'a.id, a.doctor_added, a.date';
            $where = array(
                'kardex_id' => $id,
                'non_pharmacological_id' => $non_pharmacological_id,
                'is_delete' => 0,
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where);
            if (count($kardex_non_pharmacological) > 0) {
                $kdx_non_pharmacological_id = $kardex_non_pharmacological[0]->id;
                $kdx_non_pharmacological_doctor_added = $kardex_non_pharmacological[0]->doctor_added;
                $kdx_non_pharmacological_date = $kardex_non_pharmacological[0]->date;

                if ($kdx_non_pharmacological_date != null) {
                    $dt_kardex_non_pharmacological['date'] = $kdx_non_pharmacological_date;
                }

                $dt_kardex_non_pharmacological['doctor_added'] = $kdx_non_pharmacological_doctor_added;
                $dt_kardex_non_pharmacological['user_updated'] = $user_id;
                unset($dt_kardex_non_pharmacological['user_added']);
                unset($dt_kardex_non_pharmacological['date_added']);
                
                $this->Kardex_non_pharmacological->update_v2(0, $dt_kardex_non_pharmacological, $where);
            } else {
                $dt_kardex_non_pharmacological['is_change'] = 1;
                $dt_kardex_non_pharmacological['date'] = date('Y-m-d H:i:s');
                $kdx_non_pharmacological_id = $this->Kardex_non_pharmacological->create($dt_kardex_non_pharmacological);

                $kdx_non_pharmacological_changes = true;
            }

            array_push($kdx_non_pharmacological_not_delete_ids, $kdx_non_pharmacological_id);
            array_push($fresh_kdx_non_pharmacological, $dt_kardex_non_pharmacological);
        }

        $dt_kardex_non_pharmacological_delete = array(
            'is_delete' => 1,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );
        if (count($kdx_non_pharmacological_not_delete_ids) > 0) {
            $where_kardex_non_pharmacological = 'id NOT IN ("'.join('","', $kdx_non_pharmacological_not_delete_ids).'")';
            $this->db->where($where_kardex_non_pharmacological);
        }
        $where_kardex_non_pharmacological_delete = array(
            'kardex_id' => $id,
            'is_delete' => 0,
        );
        $this->Kardex_non_pharmacological->update_v2(0, $dt_kardex_non_pharmacological_delete, $where_kardex_non_pharmacological_delete);
        
        /* 
            Menentukan perubahan (is_change)

            Mencari perbedaan atau membandingkan satu per satu pada item resep dengan item kardex aktif

            Menentukan tanggal kapan item ditambahkan dan tanggal kapan item diberhentikan (stop)
            setelah itu membuat item dengan informasi yang baru
        */
        $kardex_item_not_delete_ids = []; // Kumpulan id dari kardex_item yang tidak ingin dihapus
        $fresh_kardex_item_want_to_create = []; // Jika yang diupdate hanya stop atau hanya non famakologis dibuatkan kardex baru

        $regular_only = false;
        $regular_changes = false;
        $not_regular_changes = false;
        $addition_changes = false;
        $extra_changes = false;
        $stop_only = false;
        $stop_changes = false;
        $not_stop_changes = false;
        $changes = false;

        for ($i=0; $i < count($form_prescription); $i++) { 
            $is_change = 0; // Default tidak ada perubahan pada item
            $change_dose_unit = false; // Default tidak ada perubahan unit dosis
            $change_dose = false; // Default tidak ada perubahan dosis

            // Define data item
            $pharmacy_catalog_id = $form_prescription[$i]['pharmacy_catalog_id'];
            $package_id = $form_prescription[$i]['package_id'];
            $signatura_times_id = $form_prescription[$i]['signatura_times_id'];
            $dose_unit_id = $form_prescription[$i]['dose_unit_id'];
            $administer_id = $form_prescription[$i]['administer_id'];
            $dose = $form_prescription[$i]['dose'];
            $desc = $form_prescription[$i]['desc'];
            $prescription_type_id = $form_prescription[$i]['prescription_type_id'];
            $sort = $form_prescription[$i]['sort'];
            $status = $form_prescription[$i]['status'];
            $is_annulment = $form_prescription[$i]['is_annulment'] ?? null;
            $annulment_note = $form_prescription[$i]['annulment_note'] ?? null;

            // Define data item parent
            $prescription_type_package = null;
            $prescription_type_value = null;
            for ($j=0; $j < count($form_prescription_parent); $j++) { 
                $id_parent = $form_prescription_parent[$j]['id'];
                if ($id_parent == 1) {
                    continue;
                }
                if ($id_parent == $prescription_type_id) {
                    $prescription_type_package = $form_prescription_parent[$j]['package'] ?? null;
                    $prescription_type_value = $form_prescription_parent[$j]['value'] ?? null;
                    $prescription_type_signatura_times_id = $form_prescription_parent[$j]['signatura_times_id'] ?? null;
                    $prescription_type_administer_id = $form_prescription_parent[$j]['administer_id'] ?? null;
                    $prescription_type_status = $form_prescription_parent[$j]['status'] ?? null;
                    $prescription_type_desc = $form_prescription_parent[$j]['desc'] ?? null;
                    // if ($prescription_type_package == "") {
                    //     $prescription_type_package = null;
                    // }
                    if ($prescription_type_signatura_times_id != "") {
                        $signatura_times_id = $prescription_type_signatura_times_id;;
                    }
                    if ($prescription_type_administer_id != "") {
                        $administer_id = $prescription_type_administer_id;;
                    }
                    if ($prescription_type_status != "") {
                        $status = $prescription_type_status;
                    }
                    if ($prescription_type_desc != "") {
                        $desc = $prescription_type_desc;
                    }
                }
            }

            if ($pharmacy_catalog_id == null) {
                $this->REST_Return(400, 'pharmacy_catalog_id is required');
                return;
            }

            if ($package_id == null) {
                $this->REST_Return(400, 'package_id is required');
                return;
            }

            if ($signatura_times_id == null) {
                $this->REST_Return(400, 'signatura_times_id is required');
                return;
            }

            if ($dose_unit_id == null) {
                $this->REST_Return(400, 'dose_unit_id is required');
                return;
            }

            if ($administer_id == null) {
                $this->REST_Return(400, 'administer_id is required');
                return;
            }

            $select = 'a.id, a.name, a.dose, a.dose_unit_id, a.packaging_value, a.packaging_unit_id';
            $catalog = $this->Catalog->select($pharmacy_catalog_id, $select);
            if (count($catalog) == 0) {
                $this->REST_Return(404, 'catalog with id ' . $pharmacy_catalog_id . ' does not exist');
                return;
            }

            $select = 'a.id, a.name';
            $package = $this->Package->select($package_id, $select);
            if (count($package) == 0) {
                $this->REST_Return(404, 'package with id ' . $package_id . ' does not exist');
                return;
            }

            $select = 'a.id';
            $signatura_times = $this->Signatura_times->select($signatura_times_id, $select);
            if (count($signatura_times) == 0) {
                $this->REST_Return(404, 'signatura times with id ' . $signatura_times_id . ' does not exist');
                return;
            }

            $select = 'a.id';
            $dose_unit = $this->Unit->select($dose_unit_id, $select);
            if (count($dose_unit) == 0) {
                $this->REST_Return(404, 'unit with id ' . $dose_unit_id . ' does not exist');
                return;
            }

            $select = 'a.id';
            $administer = $this->Administer->select($administer_id, $select);
            if (count($administer) == 0) {
                $this->REST_Return(404, 'administer with id ' . $administer_id . ' does not exist');
                return;
            }

            $package_name = $package[0]->name;

            // Define default variable
            $catalog_date = date('Y-m-d H:i:s'); // Tanggal hari ini jika belum pernah ditambahkan
            $stop_date = null; // Tanggal kosong jika belum pernah di stop
            // $doctor_added = $user_employee_id; // Dokter yang menambahkan user login
            
            // Perbarui tanggal menjadi hari ini jika status item pada resep 4 (Stop)
            if ($status == 4) {
                $stop_date = date('Y-m-d H:i:s');
            }

            // Jika anulir obat, ubah status 1 (Reguler) ke 2 (Tambahan) agar dapat diorder kembali
            if ($is_annulment == 1 && $status == 1) {
                $status = 2;
            }

            /* 
                Mencari apakah item pada resep ada pada kardex aktif, jika tidak ada
                maka langsung menentukan perubahan terjadi, jika ada mencari perbedaan 
                item pada kardex aktif dengan item pada resep

                set variable catalog_date & stop_date
            */
            $select = 'a.id,
            a.pharmacy_catalog_id,
            a.package_id,
            a.signatura_times_id,
            a.administer_id,
            a.dose,
            a.dose_unit_id,
            a.qty,
            a.used,
            a.desc,
            a.sort,
            a.prescription_item_type_id,
            a.prescription_item_type_used,
            a.prescription_item_type_value,
            a.prescription_item_type_package,
            a.prescription_item_type_desc,
            a.doctor_added,
            a.catalog_date,
            a.stop_date,
            a.status,
            a.is_change
            ';
            $where = array(
                'a.kardex_id' => $id,
                'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $kardex_item_active = $this->Kardex_item->select(0, $select, $where);
            if (count($kardex_item_active) > 0) {
                // Define data item kardex aktif
                $kardex_item_active_pharmacy_catalog_id = $kardex_item_active[0]->pharmacy_catalog_id;
                $kardex_item_active_package_id = $kardex_item_active[0]->package_id;
                $kardex_item_active_signatura_times_id = $kardex_item_active[0]->signatura_times_id;
                $kardex_item_active_administer_id = $kardex_item_active[0]->administer_id;
                $kardex_item_active_dose_unit_id = $kardex_item_active[0]->dose_unit_id;
                $kardex_item_active_dose = $kardex_item_active[0]->dose;
                $kardex_item_active_desc = $kardex_item_active[0]->desc;
                $kardex_item_active_status = $kardex_item_active[0]->status;
                $kardex_item_active_prescription_item_type_id = $kardex_item_active[0]->prescription_item_type_id;
                $kardex_item_active_prescription_item_type_package = $kardex_item_active[0]->prescription_item_type_package;
                $kardex_item_active_is_change = $kardex_item_active[0]->is_change;

                if ($kardex_item_active_is_change == 1) { // SBAR save dokter jaga kalau stop only
                    $is_change = 1;
                }

                if($pharmacy_catalog_id != $kardex_item_active_pharmacy_catalog_id) {
                    $is_change = 1;
                }
                if($package_id != $kardex_item_active_package_id) {
                    $is_change = 1;
                }
                if($signatura_times_id != $kardex_item_active_signatura_times_id) {
                    $is_change = 1;
                }
                if($administer_id != $kardex_item_active_administer_id) {
                    $is_change = 1;
                }
                if($dose_unit_id != $kardex_item_active_dose_unit_id) {
                    $change_dose_unit = true;
                    $is_change = 1;
                }
                if($dose != $kardex_item_active_dose) {
                    $change_dose = true;
                    $is_change = 1;
                }
                // if($desc != $kardex_item_active_desc) {
                //     $is_change = 1;
                // }
                if($prescription_type_id != $kardex_item_active_prescription_item_type_id) {
                    $is_change = 1;
                }
                if($prescription_type_package != $kardex_item_active_prescription_item_type_package) {
                    $is_change = 1;
                }
                if($status != $kardex_item_active_status) {
                    $is_change = 1;
                }
            } else { // if no catalog found, means new catalog and changes
                $is_change = 1;
            }

            $regular_change = false;
            if ($status == 1 && $is_change == 1) {
                // if ($change_dose_unit == false) { // Jika tidak ganti unit dosis, TAB -> TAB
                //     if ($change_dose) { // Ganti dosis
                //         if ($dose > $kardex_item_active_dose) { // Dosis baru lebih besar dari dosis lama
                //             $status = 2;
                //             $regular_change = true;
                //         }
                //     }
                // } else { // Jika ganti unit dosis, TAB -> MG
                //     if ($change_dose) { // Ganti Dosis
                //         // 1 TAB -> 250 MG
                //         $tmp_clean_kardex_item_active_dose = clean_dose_unit($kardex_item_active_dose, $kardex_item_active_dose_unit_id, $pharmacy_catalog_id);
                //         if ($dose > $tmp_clean_kardex_item_active_dose) { // Dosis baru lebih besar dari dosis lama
                //             $status = 2;
                //             $regular_change = true;
                //         }
                //     }
                // }

                $active_item_count_physical = $this->Kardex_stock->count_qty($pharmacy_catalog_id, $kardex_item_active_dose, $kardex_item_active_dose_unit_id, $kardex_item_active_signatura_times_id, $kardex_item_active_administer_id);
                $new_item_count_physical = $this->Kardex_stock->count_qty($pharmacy_catalog_id, $dose, $dose_unit_id, $signatura_times_id, $administer_id);

                // Jika kurang jadi Resep Tambahan
                if ($new_item_count_physical > $active_item_count_physical) {
                    $status = 2;
                    $regular_change = true;
                }
            }

            // Cek apakah kardex ini hanya berisi regular changes
            if ($status == 1 && $is_change == 1) {
                $regular_changes = true;
            } elseif ($status != 1 && $is_change == 1) {
                $not_regular_changes = true;
            }

            // Cek apakah kardex ini berisi tambahan
            if ($status == 2 && $is_change == 1) {
                $addition_changes = true;
            }

            // Cek apakah kardex ini berisi extra
            if ($status == 3 && $is_change == 1) {
                $extra_changes = true;
            }

            // Cek apakah kardex ini hanya berisi stop order
            if ($status == 4 && $is_change == 1) {
                $stop_changes = true;
            } elseif ($status != 4 && $is_change == 1) {
                $not_stop_changes = true;
            }

            $is_suggestion_item = 0;
            if ($suggestion) {
                $is_suggestion_item = 1;
                if ($is_change == 0) {
                    $is_suggestion_item = 0;
                }
            }

            // Define data item yang akan dibuat
            $dt_kardex_item = array(
                'kardex_id'                     => $id,
                'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                'package_id'                    => $package_id,
                'signatura_times_id'            => $signatura_times_id,
                'administer_id'                 => $administer_id,
                'dose'                          => $dose,
                'dose_unit_id'                  => $dose_unit_id,
                'qty'                           => null,
                'desc'                          => $desc,
                'sort'                          => $sort,
                'prescription_item_type_id'     => $prescription_type_id,
                'prescription_item_type_package'=> $prescription_type_package,
                'prescription_item_type_value'  => $prescription_type_value,
                'doctor_added'                  => $doctor_added,
                'catalog_date'                  => $catalog_date,
                'stop_date'                     => $stop_date,
                'status'                        => $status,
                'is_change'                     => $is_change,
                'is_suggestion'                 => $is_suggestion_item,
                'is_delete'                     => 0,
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s'),
                'date_updated'                  => date('Y-m-d H:i:s'),
            );

            $select = 'a.id, a.doctor_added, a.catalog_date, a.stop_date, a.is_change';
            $where = array(
                'a.kardex_id' => $id,
                'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $kardex_item = $this->Kardex_item->select(0, $select, $where);
            if (count($kardex_item) > 0) {
                $kdx_item_id = $kardex_item[0]->id;
                $kdx_item_doctor_added = $kardex_item[0]->doctor_added;
                $kdx_item_catalog_date = $kardex_item[0]->catalog_date;
                $kdx_item_stop_date = $kardex_item[0]->stop_date;
                $kdx_item_is_change = $kardex_item[0]->is_change;

                if ($kdx_item_doctor_added != null) {
                    $dt_kardex_item['doctor_added'] = $kdx_item_doctor_added;
                }

                if ($kdx_item_catalog_date != null) {
                    $dt_kardex_item['catalog_date'] = $kdx_item_catalog_date;
                }

                if ($kdx_item_stop_date != null) {
                    $dt_kardex_item['stop_date'] = $kdx_item_stop_date;
                    
                    if ($status != 4) {
                        $dt_kardex_item['stop_date'] = null;
                    }
                }

                if ($kdx_item_is_change != 0) {
                    $dt_kardex_item['is_change'] = $kdx_item_is_change;
                }

                $dt_kardex_item['user_updated'] = $user_id;
                unset($dt_kardex_item['user_added']);
                unset($dt_kardex_item['date_added']);

                // Membuat item kardex
                $this->Kardex_item->update_v2($kdx_item_id, $dt_kardex_item);

                if ($is_annulment == 0 || ($is_annulment == 1 && $status != 4)) { // Jika anulir obat stop atau tidak jadi, menghapus barisan dari kardex
                    array_push($kardex_item_not_delete_ids, $kdx_item_id); // Tambah ke array kardex_item_not_delete_ids agar item tidak ter-delete
                }
            } else {
                // Membuat item kardex
                $kdx_item_id = $this->Kardex_item->create($dt_kardex_item);

                array_push($kardex_item_not_delete_ids, $kdx_item_id);  // Tambah ke array kardex_item_not_delete_ids agar item tidak ter-delete
            }

            array_push($fresh_kardex_item_want_to_create, $dt_kardex_item);

            // Ganti dosis reguler update ke Kardex Stock
            if (($status == 1 && $is_change == 1) || $regular_change) {
                $select = 'a.id';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $kardex_stock = $this->Kardex_stock->select(0, $select, $where);
                if (count($kardex_stock) > 0) {
                    $kardex_stock_id = $kardex_stock[0]->id;
                    $kdx_dose = clean_dose_unit($dose, $dose_unit_id, $pharmacy_catalog_id);
                    $kdx_dose_unit_id = clean_unit($dose_unit_id);

                    $dt_kardex_stock = array(
                        'signatura_times_id' => $signatura_times_id,
                        'dose' => $kdx_dose,
                        'dose_unit_id' => $kdx_dose_unit_id,
                        'date_updated' => date('Y-m-d H:i:s'),
                    );
            
                    $this->Kardex_stock->update_v2($kardex_stock_id, $dt_kardex_stock);
                }
            }

            /* 
                Anulir obat

                menganulir katalog yang sudah di-resepkan dengan menambahkan catatan anulir pada 
                katalog di-resep sebelumnya dan set is_annulment menjadi 1
            */
            if ($is_annulment == 1) {
                $select = 'a.id, ki.id as kardex_item_id';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.is_active' => 0,
                    'ki.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join = array(
                    array('kardex_item ki', 'a.id = ki.kardex_id', 'INNER'),
                );
                $order = array('a.date_added' => 'DESC');
                $this->Kardex->limit = 1;
                $last_kardex = $this->Kardex->select(0, $select, $where, $join, $order);

                $annulment_kardex_item_id = $last_kardex[0]->kardex_item_id;

                $dt_annulment_kardex_item = array(
                    'annulment_note' => $annulment_note,
                    'is_annulment' => 1,
                    'user_updated' => $user_id,
                    'date_updated' => date('Y-m-d H:i:s'),
                );
                $this->Kardex_item->update_v2($annulment_kardex_item_id, $dt_annulment_kardex_item);
            }

            if ($is_change == 1) {
                $changes = true;
            }
        }

        if ($regular_changes && !$not_regular_changes) {
            $regular_only = true;
        }

        if ($stop_changes && !$not_stop_changes) {
            $stop_only = true;
        }

        // var_dump($kdx_non_pharmacological_changes);
        // var_dump($changes);
        // var_dump($regular_only);
        // var_dump($stop_only);
        // var_dump($regular_changes);
        // var_dump($addition_changes);
        // var_dump($extra_changes);
        // var_dump($stop_changes);
        // die();

        // jika ada $changes dan $not_regular_changes false dan $not_stop_changes false

        /* 
            - Kalau tidak ada perubahan dan Hanya perubahan non farmakologis
            - Ada perubahan dan Hanya perubahan Reguler dan Stop
            - Hanya Reguler
            - Hanya Stop
            - SBAR dan Tidak ada perubahan dan Merupakan jawaban konsul
                &&
            - Bukan jawaban konsul atau SBAR
        */
        if (
            (
                (!$changes && $kdx_non_pharmacological_changes) || 
                ($changes && $regular_changes && $stop_changes && !$addition_changes && !$extra_changes) || 
                $regular_only || 
                $stop_only || 
                ($sbar_id != null && !$changes && $kdx_is_suggestion == 1)
            ) && 
            (
                $is_suggestion == 0 || 
                $sbar_id != null
            )
            ) {
            $dt_kardex = array(
                'registration_destination_id' => $registration_destination_id,
                'doctor_in_charge_id'         => $doctor_in_charge_id,
                'health_facility_item_id'     => $health_facility_item_id,
                'soap_id'                     => null,
                'cppt_id'                     => null,
                'sbar_id'                     => null,
                'is_suggestion'               => 0,
                'is_active'                   => 1,
                'is_emergency_trigger'        => $kdx_is_emergency_trigger,
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s'),
                'date_updated'                => date('Y-m-d H:i:s'),
            );

            $fresh_kardex_id = $this->Kardex->create($dt_kardex);

            for ($i=0; $i < count($fresh_kardex_item_want_to_create); $i++) { 
                $fresh_kardex_item_want_to_create[$i]['kardex_id'] = $fresh_kardex_id;
                $fresh_kardex_item_want_to_create[$i]['is_change'] = 0; // Item yang stop is_change jadi 0
                $fresh_kardex_item_want_to_create[$i]['user_added'] = $user_id;
                $fresh_kardex_item_want_to_create[$i]['date_added'] = date('Y-m-d H:i:s');
                $fresh_kardex_item_want_to_create[$i]['user_updated'] = null;
                
                $this->Kardex_item->create($fresh_kardex_item_want_to_create[$i]);
            }

            for ($i=0; $i < count($fresh_kdx_non_pharmacological); $i++) { 
                $fresh_kdx_non_pharmacological[$i]['kardex_id'] = $fresh_kardex_id;
                $fresh_kdx_non_pharmacological[$i]['is_change'] = 0;

                $this->Kardex_non_pharmacological->create($fresh_kdx_non_pharmacological[$i]);
            }

            $dt_kardex = array(
                'is_active' => 0,
                'is_emergency_trigger' => 0,
            );
            $this->Kardex->update_v2($id, $dt_kardex);
        }

        $dt_kardex_item_delete = array(
            'is_delete' => 1,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );
        if (count($kardex_item_not_delete_ids) > 0) {
            $where_kardex_item = 'id NOT IN ("'.join('","', $kardex_item_not_delete_ids).'")';
            $this->db->where($where_kardex_item);
        }
        $where_kardex_item_delete = array(
            'kardex_id' => $id,
            'is_delete' => 0,
        );
        $this->Kardex_item->update_v2(0, $dt_kardex_item_delete, $where_kardex_item_delete);

        // Notif terikirim jika berupa saran dan terdapat perubahan pada kardex
        if ($suggestion && ($changes || $kdx_non_pharmacological_changes || $stop_only)) {
            $select = 'a.id, a.data';
            $where = array(
                'a.user_id' => $doctor_user_id,
                'a.type' => 'kardex',
            );
            $where_notif = 'a.read_at IS NULL';
            $this->db->where($where_notif);
            $notification = $this->Notifications->select(0, $select, $where);
    
            $send_notif = true;
            for ($i=0; $i < count($notification); $i++) { 
                $data = json_decode($notification[$i]->data);
                $data_kardex_id = $data->kardex_id;
                
                if ($data_kardex_id == $id) {
                    $send_notif = false;
                }
            }

            if ($send_notif) {
                $select = 'a.id, a.data';
                $where = array(
                    'a.user_id' => $doctor_user_id,
                    'a.type' => 'kardex',
                );
                $where_notif = 'a.read_at IS NOT NULL';
                $this->db->where($where_notif);
                $join = array();
                $order = array('a.read_at' => 'DESC');
                $this->Notifications->limit = 100;
                $notification = $this->Notifications->select(0, $select, $where);
                $this->Notifications->limit = 0;

                for ($i=0; $i < count($notification); $i++) { 
                    $data = json_decode($notification[$i]->data);
                    $data_kardex_id = $data->kardex_id;
                    
                    if ($data_kardex_id == $id) {
                        $send_notif = false;
                    }
                }
            }
    
            if ($send_notif) {
                $title = 'KARDEX';
                $message = "Perubahan pada kardex $patient_alias $patient_name ($norm) oleh $user_employee_name. Klik untuk lihat dan approve";
                $this->Notifications->createNotification($doctor_user_id, null, $title, $message, 'kardex', ['kardex_id' => $id, 'cppt_id' => $cppt_id]);
    
                // Dokter Umum
                $title = 'KARDEX';
                $message = "Perubahan pada kardex $patient_alias $patient_name ($norm) oleh $user_employee_name. Klik untuk lihat dan buatkan SBAR";
                $this->Notifications->createNotification(null, 'd93bd445-df1e-451a-bf95-5fc10546c133', $title, $message, 'kardex', ['kardex_id' => $id]);
            }
        }

        /* 
            Jika kardex berupa saran dan tidak ada perubahan pada kardex maka set is_suggestion = 0
            Agar Dr Jaga bisa input tanpa perlu ambil kardex (notif tidak terkirim jika tidak ada perubahan)
        */
        if ($is_suggestion == 1 && !$changes && !$stop_only && !$kdx_non_pharmacological_changes) {
            $dt_kardex = array(
                'is_suggestion' => 0,
            );
            $this->Kardex->update_v2($id, $dt_kardex);
        }

        // $this->Kardex->check_if_new_nurse_tasks($id, $user_id);
        
        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kardex,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create kardex', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function order_put($p = 0) {
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
        $this->form_validation->set_rules($this->order_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        Logger::info($this->debug_uniq, "|---- start order kardex ----|");

        $kardex_id = $id;

        if ($kardex_id === 0 || $kardex_id === "0") {
            $this->REST_Return(400, 'kardex_id is required');
            return;
        }

        $select = 'a.id, a.soap_id, a.cppt_id, a.sbar_id, a.registration_destination_id, a.health_facility_item_id, a.doctor_in_charge_id, r.norm, a.is_suggestion, a.suggestion_status, a.is_emergency_trigger, u.id as user_doctor_in_charge_id';
        $where = array();
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('user u', 'a.doctor_in_charge_id = u.employee_id', 'INNER'),
        );
        $kardex = $this->Kardex->select($kardex_id, $select, $where, $join);
        if (count($kardex) == 0) {
            $this->REST_Return(404, 'there is no kardex with id ' . $kardex_id);
            return;
        }

        $soap_id = $kardex[0]->soap_id;
        $cppt_id = $kardex[0]->cppt_id;
        $sbar_id = $kardex[0]->sbar_id;
        $registration_destination_id = $kardex[0]->registration_destination_id;
        $health_facility_item_id = $kardex[0]->health_facility_item_id;
        $doctor_in_charge_id = $kardex[0]->doctor_in_charge_id;
        $user_doctor_in_charge_id = $kardex[0]->user_doctor_in_charge_id;
        $norm = $kardex[0]->norm;
        $suggestion_status = $kardex[0]->suggestion_status;
        $is_suggestion = $kardex[0]->is_suggestion;
        $is_emergency_trigger_past = $kardex[0]->is_emergency_trigger;

        if ($is_suggestion == 1 && $suggestion_status == 0) {
            $this->REST_Return(409, 'Kardex aktif merupakan jawaban dari konsultasi menunggu untuk disetujui');
            return;
        }
        
        /* Start Transaction */
        $this->db->trans_start();

        // Membuat resep berdasarkan kardex yang dipilih
        $prefix = 'PRS';
        
        $epoch = time();
        $dt_queue = array(
            'var_2' => date('Y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('Y-m-d', $epoch),
        );

        $no_order = counter_queue($prefix, $dt_queue, 4);
        $queue = round((int)substr($no_order, -4));

        $dt_prescription = array(
            'norm'                        => $norm,
            'institute_branch_id'         => $institute_branch_id,
            'pharmacy_depo_id'            => '297c44f0-12ef-40e6-b9a7-c831065b3372',
            'type_prescription_id'        => 6,
            'health_facility_item_id'     => $health_facility_item_id,
            'doctor_in_charge_id'         => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'no_order'                    => $no_order,
            'date_added'                  => date("Y-m-d H:i:s",time()),
            'date_0'                      => date("Y-m-d H:i:s",time()),
            'flag'                        => 0,
            'queue'                       => $queue,
        );
        $prescription_id = $this->Prescription->create($dt_prescription);

        /* 
            Membuat kardex fresh dan menjadikan status 2 (Tambahan) menjadi 1 (Reguler)
            kardex fresh akan menjadi kardex aktif yang siap diresepkan kembali
        */
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
        );
        $kardex_counts = $this->Kardex->select(0, $select, $where, $join);
        $is_emergency_trigger = 0;
        if (count($kardex_counts) == 1 || $is_emergency_trigger_past == 1) {
            // $is_emergency_trigger = 1;
            // NOTE - TIDAK SET TRIGGER KARENA KARDEX DIBUAT KETIKA TRANSFER
        }

        $dt_kardex = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id'         => $doctor_in_charge_id,
            'health_facility_item_id'     => $health_facility_item_id,
            'soap_id'                     => null,
            'cppt_id'                     => null,
            'sbar_id'                     => null,
            'is_active'                   => 1,
            'is_emergency_trigger'        => $is_emergency_trigger,
            'user_added'                  => $user_id,
            'date_added'                  => date('Y-m-d H:i:s'),
            'date_updated'                => date('Y-m-d H:i:s'),
        );
        $fresh_kardex_id = $this->Kardex->create($dt_kardex);

        $select = 'a.id, 
        a.pharmacy_catalog_id,
        pc.name as pharmacy_catalog_name,
        pc.dose as catalog_dose,
        u2.name as catalog_dose_unit_name,
        pc.packaging_value as catalog_packaging_value,
        u3.name as catalog_packaging_unit_name,
        a.package_id,
        p.name as package_name,
        a.signatura_times_id,
        st.name as signatura_times_name,
        a.dose,
        a.dose_unit_id,
        u.name as dose_unit_name,
        a.administer_id,
        pci.name as administer_name,
        a.qty,
        a.desc,
        a.sort,
        a.doctor_added,
        a.catalog_date,
        a.stop_date,
        a.prescription_item_type_id,
        a.prescription_item_type_package,
        a.prescription_item_type_value,
        a.status,
        a.is_reject,
        ';
        $where = array(
            'a.kardex_id' => $kardex_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('package p', 'a.package_id = p.id', 'INNER'),
            array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
            array('unit u2', 'pc.dose_unit_id = u2.id', 'LEFT'),
            array('unit u3', 'pc.packaging_unit_id = u3.id', 'LEFT'),
            array('signatura_times st', 'a.signatura_times_id = st.id', 'LEFT'),
            array('pharmacy_catalog_instruction pci', 'a.administer_id = pci.id', 'LEFT'),
        );
        $kardex_item = $this->Kardex_item->select(0, $select, $where, $join);

        /* 
            Item yang diorder akan dimasukan diarray ini untuk di cek apakah perlu membuat
            kardex baru apabila tidak ada item yang diorder, count() = 0 
        */
        $ordered_items = []; 

        for ($i=0; $i < count($kardex_item); $i++) { 
            // Define data item kardex
            $pharmacy_catalog_id = $kardex_item[$i]->pharmacy_catalog_id;
            $pharmacy_catalog_name = $kardex_item[$i]->pharmacy_catalog_name;
            $catalog_dose = $kardex_item[$i]->catalog_dose;
            $catalog_dose_unit_name = $kardex_item[$i]->catalog_dose_unit_name;
            $catalog_packaging_value = $kardex_item[$i]->catalog_packaging_value;
            $catalog_packaging_unit_name = $kardex_item[$i]->catalog_packaging_unit_name;
            $package_id = $kardex_item[$i]->package_id;
            $package_name = $kardex_item[$i]->package_name;
            $signatura_times_id = $kardex_item[$i]->signatura_times_id;
            $signatura_times_name = $kardex_item[$i]->signatura_times_name;
            $administer_id = $kardex_item[$i]->administer_id;
            $administer_name = $kardex_item[$i]->administer_name;
            $dose = $kardex_item[$i]->dose;
            $dose_unit_id = $kardex_item[$i]->dose_unit_id;
            $dose_unit_name = $kardex_item[$i]->dose_unit_name;
            $qty = $kardex_item[$i]->qty;
            $desc = $kardex_item[$i]->desc;
            $sort = $kardex_item[$i]->sort;
            $doctor_added = $kardex_item[$i]->doctor_added;
            $catalog_date = $kardex_item[$i]->catalog_date;
            $stop_date = $kardex_item[$i]->stop_date;
            $prescription_item_type_id = $kardex_item[$i]->prescription_item_type_id;
            $prescription_item_type_package = $kardex_item[$i]->prescription_item_type_package;
            $prescription_item_type_value = $kardex_item[$i]->prescription_item_type_value;
            $status = $kardex_item[$i]->status;
            $is_reject = $kardex_item[$i]->is_reject;

            Logger::debug($this->debug_uniq, "--catalog: $pharmacy_catalog_name | $catalog_dose$catalog_dose_unit_name | $catalog_packaging_value$catalog_packaging_unit_name | status $status");
            
            Logger::debug($this->debug_uniq, "doctor: $dose $dose_unit_name | every $signatura_times_name hours | $administer_name");
            
            if ($prescription_item_type_id != 1) {
                Logger::debug($this->debug_uniq, "concoction: $prescription_item_type_id");
            }

            $skip_item = false;
            $skip_order = false;

            $old_status = $status;
            if ($status == 2) { // Untuk katalog ber-status 2 (Tambahan) akan diganti menjadi 1 (Reguler)
                $status = 1;
            } elseif ($status == 3) { // Untuk katalog ber-status 3 (Extra) tidak akan masuk kedalam kardex baru
                $skip_item = true;
            } elseif ($status == 1 || $status == 4) { // Untuk katalog ber-status 1 (Reguler) dan 4 (Stop) tidak akan di-order ke Farmasi
                $skip_order = true;
            }

            // Item yang sebagian ditolak tidak masuk kedalam kardex baru dan tidak di-order
            if ($is_reject === 1) {
                $skip_item = true;
                $skip_order = true;
            }

            /* 
                Membuat persediaan obat pada bangsal jika belum memiliki stock,
                setup kardex stock untuk dapat menghitung kekuatan dosis atau resep dari dokter menjadi stock
                yang siap digunakan. Dengan mengambil data dari master katalog
                
                cth. Sefadroksil 125 MG
                volume: 60 ML
                dosis: 1 C = 15 ML
                jam: Tiap 8 Jam
            */
            // $qty = $this->Kardex_stock->get_qty($pharmacy_catalog_id, $dose, $dose_unit_id, $signatura_times_id, $administer_id); // return null jika ada error
            $qty = $this->Kardex_stock->count_qty($pharmacy_catalog_id, $dose, $dose_unit_id, $signatura_times_id, $administer_id); // return null jika ada error


            // TODO - TIDAK BISA DILAKUKAN SEKARANG KARENA BELUM MASUK FASE 2
            // Resep Tambahan yang mengganti dosis resep reguler
            // $select = 'a.id, a.current_physical_quantity';
            // $where = array(
            //     'a.registration_destination_id' => $registration_destination_id,
            //     'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
            // );
            // $kardex_stock = $this->Kardex_stock->select(0, $select, $where);
            // if (count($kardex_stock) > 0 && $old_status == 2) {
            //     Logger::debug($this->debug_uniq, "drug_dosage_requirement: $qty $package_name");
                
            //     $current_physical_quantity = $kardex_stock[0]->current_physical_quantity;
                
            //     Logger::debug($this->debug_uniq, "current_physical_quantity: $current_physical_quantity");
                
            //     $qty = $qty - $current_physical_quantity;
            // }
            
            Logger::debug($this->debug_uniq, "qty: $qty $package_name");
                
            if (!$skip_order) {
                // Membuat resep item per katalog
                $dt_prescription_item = array(
                    'pharmacy_prescription_id'      => $prescription_id,
                    'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                    'doctor_in_charge_id'           => $doctor_added,
                    'package_id'                    => $package_id,
                    'package_name'                  => $package_name,
                    'signatura_times_id'            => $signatura_times_id,
                    'administer_id'                 => $administer_id,
                    'dose'                          => $dose,
                    'dose_unit_id'                  => $dose_unit_id,
                    'qty'                           => $qty,
                    'desc'                          => $desc,
                    'sort'                          => $sort,
                    'prescription_item_type_id'     => $prescription_item_type_id,
                    'prescription_item_type_package'=> $prescription_item_type_package,
                    'prescription_item_type_value'  => $prescription_item_type_value,
                    'is_delete'                     => 0
                );
                $this->Prescription_item->replace($dt_prescription_item);
    
                array_push($ordered_items, $dt_prescription_item); // Masukan item resep kedalam array $ordered_items
            } else {
                Logger::debug($this->debug_uniq, "skip order item");
            }

            if (!$skip_item) {
                // Membuat kardex item baru
                $dt_kardex_item = array(
                    'kardex_id'                     => $fresh_kardex_id,
                    'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                    'package_id'                    => $package_id,
                    'signatura_times_id'            => $signatura_times_id,
                    'administer_id'                 => $administer_id,
                    'dose'                          => $dose,
                    'dose_unit_id'                  => $dose_unit_id,
                    'qty'                           => null,
                    'desc'                          => $desc,
                    'sort'                          => $sort,
                    'prescription_item_type_id'     => $prescription_item_type_id,
                    'prescription_item_type_package'=> $prescription_item_type_package,
                    'prescription_item_type_value'  => $prescription_item_type_value,
                    'doctor_added'                  => $doctor_added,
                    'catalog_date'                  => $catalog_date,
                    'stop_date'                     => $stop_date,
                    'status'                        => $status,
                    'is_change'                     => 0,
                    'user_added'                    => $user_id,
                    'date_added'                    => date('Y-m-d H:i:s'),
                    'date_updated'                  => date('Y-m-d H:i:s'),
                );
                $this->Kardex_item->create($dt_kardex_item);
            } else {
                Logger::debug($this->debug_uniq, "skip input kardex item");
            }
        }

        // Membuat kardex non farmakologis ke kardex baru dengan mengamil dari karder yang sudah di-order
        $select = 'a.id, a.non_pharmacological_id, a.doctor_added, a.date';
        $where = array(
            'a.kardex_id' => $kardex_id,
            'a.is_delete' => 0,
        );
        $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where);
        for ($i=0; $i < count($kardex_non_pharmacological); $i++) { 
            $non_pharmacological_id = $kardex_non_pharmacological[$i]->non_pharmacological_id;
            $doctor_added = $kardex_non_pharmacological[$i]->doctor_added;
            $date = $kardex_non_pharmacological[$i]->date;
            
            $dt_kardex_non_pharmacological = array(
                'kardex_id' => $fresh_kardex_id,
                'non_pharmacological_id' => $non_pharmacological_id,
                'doctor_added' => $doctor_added,
                'date' => $date,
                'is_change' => 0,
            );

            $this->Kardex_non_pharmacological->create($dt_kardex_non_pharmacological);
        }

        // Kardex yang sudah di-order menjadi tidak aktif dan menempelkan order resep ke kardex tersebut
        $dt_kardex = array(
            'pharmacy_prescription_id' => $prescription_id,
            'is_active' => 0,
            'is_emergency_trigger' => 0,
        );
        $this->Kardex->update_v2($kardex_id, $dt_kardex);


        // Read notifikasi kardex terkait jika belum pernah dibaca
        $select = 'a.id, a.data';
        $where = array(
            'a.user_id' => $user_doctor_in_charge_id,
            'a.type' => 'kardex',
        );
        $where_notif = 'a.read_at IS NULL';
        $this->db->where($where_notif);
        $notification = $this->Notifications->select(0, $select, $where);

        $read_notif = false;
        for ($i=0; $i < count($notification); $i++) { 
            $data = json_decode($notification[$i]->data);
            $data_kardex_id = $data->kardex_id;
            
            if ($data_kardex_id == $id) {
                $read_notif = true;
            }
        }

        if ($read_notif) {
            // DPJP
            $this->Notifications->readNotificationWithItem($user_doctor_in_charge_id, $kardex_id, 'kardex_id');

            // Dokter Umum
            $this->Notifications->readNotificationPositionWithItem('d93bd445-df1e-451a-bf95-5fc10546c133', 'kardex', $kardex_id, 'kardex_id');
        }

        // $this->Kardex->check_if_new_nurse_tasks($kardex_id, $user_id);

        /*
            Transaksi akan dirollback apabila tidak ada item yang diorder = semua item reguler
        */
        if (count($ordered_items) == 0) {
            Logger::debug($this->debug_uniq, "rolling back transaction, there's no ordered items...");
            $this->db->trans_rollback();
            /* End Transaction */
        } else {
            $this->db->trans_complete();
            /* End Transaction */
        }

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kardex,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to order kardex', $response_data);
            return;
        }

        Logger::info($this->debug_uniq, "|---- end order kardex ----|");

        $response_data = array(
            'id'     => $fresh_kardex_id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function order_do_get($p = 0) {
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
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        $norm = $q_strings['norm'] ?? null;
        $day_offset = $q_strings['day_offset'] ?? null;

        $utils = ['user_id' => $user_id, 'norm' => $norm];

        Logger::info($this->debug_uniq, "|---- start order do ----|");

        /* Start Transaction */
        $this->db->trans_start();

        $this->new_kardex_inpatient($utils);
        
        // Mencari seluruh kardex aktif dan tidak mempunyai resep yang akan diorder ke Farmasi
        Logger::info($this->debug_uniq, "get all active kardex");
        $select = 'a.id,
        a.registration_destination_id,
        a.doctor_in_charge_id,
        a.health_facility_item_id,
        r.norm
        ';
        $where = array(
            'a.is_active' => 1,
            'a.is_delete' => 0,
        );
        if ($norm != null) {
            $where['r.norm'] = $norm;
        }
        $where_kardex = "a.pharmacy_prescription_id IS NULL";
        $this->db->where($where_kardex);
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id AND rd.is_checkout = 0', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
        );
        $output = $this->Kardex->select(0, $select, $where, $join);

        Logger::info($this->debug_uniq, "total kardex: " . count($output));

        for ($i=0; $i < count($output); $i++) { 
            $kardex_id = $output[$i]->id;
            Logger::info($this->debug_uniq, "---processing kardex_id: " . $kardex_id);

            $select = 'a.id';
            $where = array(
                'a.kardex_id' => $kardex_id,
                'a.is_delete' => 0,
            );
            $where_kardex_item = 'a.status != 4';
            $this->db->where($where_kardex_item);
            $kardex_item = $this->Kardex_item->select(0, $select, $where);
            if (count($kardex_item) == 0) {
                Logger::info($this->debug_uniq, "all catalogs are stopped, continue...");
                continue;
            }

            $registration_destination_id = $output[$i]->registration_destination_id;
            $doctor_in_charge_id = $output[$i]->doctor_in_charge_id;
            $health_facility_item_id = $output[$i]->health_facility_item_id;
            $norm = $output[$i]->norm;

            // Membuat resep
            $prefix = 'PRS';
            
            $epoch = time();
            $dt_queue = array(
                'var_2' => date('Y', $epoch),
                'var_3' => date('m', $epoch),
                'var_4' => date('d', $epoch),
                'var_5' => date('Y-m-d', $epoch),
            );

            $no_order = counter_queue($prefix, $dt_queue, 4);
            $queue = round((int)substr($no_order, -4));
 
            Logger::info($this->debug_uniq, "no_order: " . $no_order);

            $dt_prescription = array(
                'norm'                        => $norm,
                'institute_branch_id'         => $institute_branch_id,
                'pharmacy_depo_id'            => '297c44f0-12ef-40e6-b9a7-c831065b3372',
                'type_prescription_id'        => 4,
                'health_facility_item_id'     => $health_facility_item_id,
                'doctor_in_charge_id'         => $doctor_in_charge_id,
                'registration_destination_id' => $registration_destination_id,
                'no_order'                    => $no_order,
                'date_added'                  => date("Y-m-d H:i:s",time()),
                'date_0'                      => date("Y-m-d H:i:s",time()),
                'flag'                        => 0,
                'queue'                       => $queue,
            );
            $prescription_id = $this->Prescription->create($dt_prescription);

            Logger::info($this->debug_uniq, "prescription_id: " . $prescription_id);

            // Membuat kardex baru
            $dt_kardex = array(
                'registration_destination_id' => $registration_destination_id,
                'doctor_in_charge_id'         => $doctor_in_charge_id,
                'health_facility_item_id'     => $health_facility_item_id,
                'is_active'                   => 1,
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s', strtotime('+1 second')),
                'date_updated'                => date('Y-m-d H:i:s', strtotime('+1 second')),
            );
            
            $new_kardex_id = $this->Kardex->create($dt_kardex);

            Logger::info($this->debug_uniq, "new_kardex_id: " . $new_kardex_id);

            $kardex_item_want_to_create = [];
            
            $select_kardex_item = 'a.id,
            a.pharmacy_catalog_id,
            pc.name as pharmacy_catalog_name,
            pc.dose as catalog_dose,
            pc.dose_unit_id as catalog_dose_unit_id,
            u2.name as catalog_dose_unit_name,
            pc.packaging_value as catalog_packaging_value,
            pc.packaging_unit_id as catalog_packaging_unit_id,
            u3.name as catalog_packaging_unit_name,
            pc.package_id,
            p.name as package_name,
            a.signatura_times_id,
            st.name as signatura_times_name,
            a.administer_id,
            pci.name as administer_name,
            a.dose,
            a.dose_unit_id,
            u.name as dose_unit_name,
            a.qty,
            a.used,
            a.desc,
            a.sort,
            a.prescription_item_type_id,
            a.prescription_item_type_used,
            a.prescription_item_type_value,
            a.prescription_item_type_package,
            a.prescription_item_type_desc,
            a.doctor_added,
            a.catalog_date,
            a.stop_date,
            a.status,
            a.is_change
            ';
            $where = array(
                'a.kardex_id' => $kardex_id
            );
            $join_kardex_item = array(
                array('kardex k', 'k.id = a.kardex_id', 'INNER'),
                array('signatura_times st', 'a.signatura_times_id = st.id', 'LEFT'),
                array('pharmacy_catalog_instruction pci', 'a.administer_id = pci.id', 'LEFT'),
                array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'LEFT'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('unit u2', 'pc.dose_unit_id = u2.id', 'LEFT'),
                array('unit u3', 'pc.packaging_unit_id = u3.id', 'LEFT'),
            );
            $kardex_item = $this->Kardex_item->select(0, $select_kardex_item, $where, $join_kardex_item);
            $kardex_item_catalog_ids = [];

            // Memasukan semua id kardex_item ke array $kardex_item_catalog_ids
            for ($j=0; $j < count($kardex_item); $j++) { 
                $pharmacy_catalog_id = $kardex_item[$j]->pharmacy_catalog_id;
                array_push($kardex_item_catalog_ids, $pharmacy_catalog_id);
            }

            /* 
                Untuk mendapatkan katalog yang tidak ada di-kardex active, dengan cara mengambil dari stock kardex
                Extra dan Racikan
            */
            $select = 'a.pharmacy_catalog_id';
            $where_stock = array(
                'registration_destination_id' => $registration_destination_id,
            );
            if (count($kardex_item_catalog_ids) > 0) {
                $where_stock_catalog = 'pharmacy_catalog_id NOT IN ("'.join('","', $kardex_item_catalog_ids).'")';
                $this->db->where($where_stock_catalog);
            }
            $kardex_item_special = $this->Kardex_stock->select(0, $select, $where_stock);
            $kardex_item_special_ids = [];

            // Memasukan semua id kardex_item_special ke array $kardex_item_special_ids
            for ($j=0; $j < count($kardex_item_special); $j++) { 
                $kardex_item_special_id = $kardex_item_special[$j]->pharmacy_catalog_id;
                array_push($kardex_item_special_ids, $kardex_item_special_id);
            }

            // Mengambil data kardex_item (Extra dan Racikan) dengan menggunakan id dari $kardex_item_special_ids
            $where = array(
                'k.registration_destination_id' => $registration_destination_id,
                'a.prescription_item_type_id' => 1,
            );
            $where_item_catalog = 'pharmacy_catalog_id IN ("'.join('","', $kardex_item_special_ids).'")';
            $this->db->where($where_item_catalog);
            $order = array(
                'a.date_added' => 'DESC',
            );
            $this->Kardex_item->limit = 1;
            $kardex_item_special = $this->Kardex_item->select(0, $select_kardex_item, $where, $join_kardex_item, $order);
            $this->Kardex_item->limit = 0;
            $kardex_item_concoction_ids = [];

            // Memasukan semua id kardex_item_special ke array $kardex_item_special_ids dan racikan ke array $kardex_item_concoction_ids
            for ($j=0; $j < count($kardex_item_special); $j++) { 
                $kardex_item_special_prescription_item_type_id = $kardex_item_special[$j]->prescription_item_type_id;
                $kardex_item_special_id = $kardex_item_special[$j]->pharmacy_catalog_id;
                if ($kardex_item_special_prescription_item_type_id == 1) {
                    array_push($kardex_item, $kardex_item_special[$j]);
                } else {
                    array_push($kardex_item_concoction_ids, $kardex_item_special_id);
                }
            }

            Logger::info($this->debug_uniq, "total kardex_item: " . count($kardex_item));

            for ($j=0; $j < count($kardex_item); $j++) { 
                $pharmacy_catalog_id = $kardex_item[$j]->pharmacy_catalog_id;
                $pharmacy_catalog_name = $kardex_item[$j]->pharmacy_catalog_name;
                $catalog_dose = $kardex_item[$j]->catalog_dose;
                $catalog_dose_unit_id = $kardex_item[$j]->catalog_dose_unit_id;
                $catalog_dose_unit_name = $kardex_item[$j]->catalog_dose_unit_name;
                $catalog_packaging_value = $kardex_item[$j]->catalog_packaging_value;
                $catalog_packaging_unit_id = $kardex_item[$j]->catalog_packaging_unit_id;
                $catalog_packaging_unit_name = $kardex_item[$j]->catalog_packaging_unit_name;
                $package_id = $kardex_item[$j]->package_id;
                $package_name = $kardex_item[$j]->package_name;
                $signatura_times_id = $kardex_item[$j]->signatura_times_id;
                $signatura_times_name = $kardex_item[$j]->signatura_times_name;
                $administer_id = $kardex_item[$j]->administer_id;
                $administer_name = $kardex_item[$j]->administer_name;
                $dose = $kardex_item[$j]->dose;
                $dose_unit_id = $kardex_item[$j]->dose_unit_id;
                $dose_unit_name = $kardex_item[$j]->dose_unit_name;
                // $qty = $kardex_item[$j]->qty;
                $used = $kardex_item[$j]->used;
                $desc = $kardex_item[$j]->desc;
                $sort = $kardex_item[$j]->sort;
                $prescription_item_type_id = $kardex_item[$j]->prescription_item_type_id;
                $prescription_item_type_used = $kardex_item[$j]->prescription_item_type_used;
                $prescription_item_type_value = $kardex_item[$j]->prescription_item_type_value;
                $prescription_item_type_package = $kardex_item[$j]->prescription_item_type_package;
                $prescription_item_type_desc = $kardex_item[$j]->prescription_item_type_desc;
                $doctor_added = $kardex_item[$j]->doctor_added;
                $catalog_date = $kardex_item[$j]->catalog_date;
                $stop_date = $kardex_item[$j]->stop_date;
                $status = $kardex_item[$j]->status;
                $is_change = $kardex_item[$j]->is_change;

                Logger::debug($this->debug_uniq, "--catalog: $pharmacy_catalog_name | $catalog_dose$catalog_dose_unit_name | $catalog_packaging_value$catalog_packaging_unit_name | status $status | $package_name | $prescription_item_type_id");
                Logger::debug($this->debug_uniq, "catalog_id: $pharmacy_catalog_id");
            
                Logger::debug($this->debug_uniq, "doctor: $dose $dose_unit_name | every $signatura_times_name hours | $administer_name");

                $dt_kardex_item = array(
                    'kardex_id'                     => $new_kardex_id,
                    'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                    'package_id'                    => $package_id,
                    'signatura_times_id'            => $signatura_times_id,
                    'administer_id'                 => $administer_id,
                    'dose'                          => $dose,
                    'dose_unit_id'                  => $dose_unit_id,
                    'qty'                           => 0,
                    'used'                          => $used,
                    'desc'                          => $desc,
                    'sort'                          => $sort,
                    'prescription_item_type_id'     => $prescription_item_type_id,
                    'prescription_item_type_used'   => $prescription_item_type_used,
                    'prescription_item_type_value'  => $prescription_item_type_value,
                    'prescription_item_type_package'=> $prescription_item_type_package,
                    'prescription_item_type_desc'   => $prescription_item_type_desc,
                    'doctor_added'                  => $doctor_added,
                    'catalog_date'                  => $catalog_date,
                    'stop_date'                     => $stop_date,
                    'status'                        => $status,
                    'is_change'                     => 0,
                    'user_added'                    => $user_id,
                    'date_added'                    => date('Y-m-d H:i:s'),
                    'date_updated'                  => date('Y-m-d H:i:s'),
                );

                // check status
                if ($status == 2) { // 2 (tambahan) set kardex item status to 1 (regular)
                    $status = 1;
                } elseif ($status == 3) { // 3 (extra), skip extra kardex item ordering just once
                    // continue; // Continue dibawah setelah pemotongan stock kardex
                } elseif ($status == 4) { // 4 (stop order), no longer order prescription but still showing in kardex
                    array_push($kardex_item_want_to_create, $dt_kardex_item); // qty 0
                    continue;
                }

                // Cek stock untuk lihat apakah perlu diorder atau masih tersisa cukup untuk kebutuhan sehari
                $select = 'a.id, a.current_amount, a.current_amount_unit_id, u.name as current_amount_unit_name, a.current_physical_quantity';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join = array(
                    array('unit u', 'a.current_amount_unit_id = u.id', 'INNER'),
                );
                $kardex_stock = $this->Kardex_stock->select(0, $select, $where, $join);
                if (count($kardex_stock) > 0) {
                    $kardex_stock_id = $kardex_stock[0]->id;
                    $current_amount = $kardex_stock[0]->current_amount;
                    $current_amount_unit_id = $kardex_stock[0]->current_amount_unit_id;
                    $current_amount_unit_name = $kardex_stock[0]->current_amount_unit_name;
                    $current_physical_quantity = $kardex_stock[0]->current_physical_quantity;

                    if ($signatura_times_id == '95727030-63ba-4e4d-9b82-2dfb064eaa74') { // 4 JAM
                        $times = 5;
                    } elseif ($signatura_times_id == '8f20d2ac-df6f-42e5-a804-009343c0d6f8') { // 6 JAM
                        $times = 4;
                    } elseif ($signatura_times_id == '26e33f75-a6bc-4624-b75a-64f545485c56') { // 8 JAM
                        $times = 3;
                    } elseif ($signatura_times_id == '20446fea-c42d-432e-9a1a-15ffad0c9dd4') { // 12 JAM
                        $times = 2;
                    } elseif ($signatura_times_id == 'a061feed-36d3-458e-9c5f-a1ac63e92075') { // 18 JAM
                        $times = 2;
                    } elseif ($signatura_times_id == 'b69d3707-ec6e-4f58-8aa2-e874d97c97e9') { // 24 JAM
                        $times = 1;
                    } elseif ($signatura_times_id == '7783211c-4d01-4a1a-921c-252b504e6a38') { // 36 JAM
                        $times = 1;
                    } elseif ($signatura_times_id == 'ce7206d4-fb67-49d2-9a3a-3115a8c833f9') { // 1 JAM
                        $times = 24;
                    } elseif ($signatura_times_id == 'fc91b35a-9ac8-4009-bf43-531764dbeeec') { // 1 MENIT
                        $times = 1440;
                    }

                    // $select = 'a.id, a.is_change';
                    // $where = array(
                    //     'k.registration_destination_id' => $registration_destination_id,
                    //     'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
                    // );
                    // $where_kardex_item = 'k.pharmacy_prescription_id IS NOT NULL';
                    // $this->db->where($where_kardex_item);
                    // $join = array(
                    //     array('kardex k', 'a.kardex_id = k.id', 'INNER'),
                    // );
                    // $order = array(
                    //     'k.date_added' => 'DESC',
                    // );
                    // $this->Kardex_item->limit = 1;
                    // $last_kardex_item = $this->Kardex_item->select(0, $select, $where, $join, $order);
                    // $last_kardex_item_is_change = $last_kardex_item[0]->is_change;

                    // if ($last_kardex_item_is_change == 1) {
                    // }
                    /* 
                        Tidak menghitung full pemakaian seharian penuh dari signatura_times karena bisa saja sudah melewati jam administer
                        cth. Obat masuk pukul 15.00 dan Tiap 8 Jam, sudah melewati pukul 14.00 jadi yang dikurangi 2x pemakaian tidak 3x
                    */
                    // $date1 = new DateTime(date('Y-m-d', strtotime($catalog_date)));
                    // if ($day_offset != null) { // NOTE - FOR TESTING PURPOSES ONLY
                    //     $date1->modify("-$day_offset day");
                    // }
                    // $date2 = new DateTime(date('Y-m-d'));
                    // $diff = $date1->diff($date2)->days;
                    // if ($diff <= 1) {
                    //     $times = count_administer_times_now($signatura_times_id, $catalog_date);
                    // }

                    // Pemotongan stok bangsal
                    if ($times > 24) {
                        $real_current_amount = $current_amount;
                        $tmp_dose = $dose * $times;
                        list($tmp_current_amount, $current_physical_quantity) = $this->Kardex_stock->administer_patient($pharmacy_catalog_id, $real_current_amount, $current_amount_unit_id, $tmp_dose, $dose_unit_id, $catalog_dose, $catalog_dose_unit_id, $catalog_packaging_value, $catalog_packaging_unit_id);
                        $real_current_amount = $tmp_current_amount;
                    } else {
                        $real_current_amount = $current_amount;
                        for ($k=0; $k < $times; $k++) { 
                            if ($real_current_amount > 0) {
                                list($tmp_current_amount, $current_physical_quantity) = $this->Kardex_stock->administer_patient($pharmacy_catalog_id, $real_current_amount, $current_amount_unit_id, $dose, $dose_unit_id, $catalog_dose, $catalog_dose_unit_id, $catalog_packaging_value, $catalog_packaging_unit_id);
                                $real_current_amount = $tmp_current_amount;
                            }
                        }
                    }

                    $real_current_amount = is_float($real_current_amount) ? number_format($real_current_amount, 2, '.', ''): $real_current_amount;

                    if ($real_current_amount < 0) {
                        $real_current_amount = 0;
                    }

                    $dt_kardex_stock = array(
                        'current_amount' => $real_current_amount,
                        'current_physical_quantity' => $current_physical_quantity,
                        'date_updated' => date('Y-m-d H:i:s'),
                    );
                    $this->Kardex_stock->update_v2($kardex_stock_id, $dt_kardex_stock);

                    // Menghitung berapa banyak yang harus diorder dari stok terakhir
                    $require_qty = $this->Kardex_stock->quantity_require($pharmacy_catalog_id, $signatura_times_id, $real_current_amount, $current_amount_unit_id, $dose, $dose_unit_id, $catalog_dose, $catalog_dose_unit_id, $catalog_packaging_value, $catalog_packaging_unit_id);

                    Logger::debug($this->debug_uniq, "current_amount: $real_current_amount$current_amount_unit_name");
                    Logger::debug($this->debug_uniq, "current_qty: $current_physical_quantity $package_name");
                } else {
                    $require_qty = $this->Kardex_stock->count_qty($pharmacy_catalog_id, $dose, $dose_unit_id, $signatura_times_id, $administer_id, true);
                }

                if ($status == 3) {// 3 (extra), skip extra kardex item ordering just once
                    continue;
                }
                
                if ($dose == null || $dose_unit_id == null || $catalog_dose == null || $catalog_dose_unit_id == null || $catalog_packaging_value == null || $catalog_packaging_unit_id == null || $package_id == null) {
                    Logger::debug($this->debug_uniq, "incomplete master or dose doctor, default 1");
                    $require_qty = 1;
                }

                if ($require_qty === null) {
                    Logger::debug($this->debug_uniq, "something wrong counting require_qty, default 1");
                    $require_qty = 1;
                }

                // NOTE -- KARENA BELUM MASUK FASE 2, DEFAULT ORDER 1
                if ($require_qty === 0) {
                    Logger::debug($this->debug_uniq, "0 qty, defaulting 1. not yet entered phase 2");
                    $require_qty = 1;
                }

                Logger::debug($this->debug_uniq, "order_qty: $require_qty $package_name");

                $dt_kardex_item['qty'] = $require_qty;

                array_push($kardex_item_want_to_create, $dt_kardex_item);

                if ($require_qty > 0) {
                    $dt_prescription_item = array(
                        'pharmacy_prescription_id'      => $prescription_id,
                        'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                        'doctor_in_charge_id'           => $doctor_added,
                        'package_id'                    => $package_id,
                        'package_name'                  => $package_name,
                        'signatura_times_id'            => $signatura_times_id,
                        'administer_id'                 => $administer_id,
                        'dose'                          => $dose,
                        'dose_unit_id'                  => $dose_unit_id,
                        'qty'                           => $require_qty,
                        'used'                          => $used,
                        'desc'                          => $desc,
                        'sort'                          => $sort,
                        'prescription_item_type_id'     => $prescription_item_type_id,
                        'prescription_item_type_used'   => $prescription_item_type_used,
                        'prescription_item_type_value'  => $prescription_item_type_value,
                        'prescription_item_type_package'=> $prescription_item_type_package,
                        'is_delete'                     => 0
                    );
    
                    $this->Prescription_item->replace($dt_prescription_item);
                }
            }

            /* ----------------- */
            // Update stock jadi 0 kalau racikan
            $dt_kardex_stock = array(
                'current_physical_quantity' => 0,
                'date_updated' => date('Y-m-d H:i:s'),
            );
            $where_stock = array(
                'registration_destination_id' => $registration_destination_id,
            );
            if (count($kardex_item_concoction_ids) > 0) {
                $where_stock_catalog = 'pharmacy_catalog_id NOT IN ("'.join('","', $kardex_item_concoction_ids).'")';
                $this->db->where($where_stock_catalog);
                $this->Kardex_stock->update_v2(0, $dt_kardex_stock, $where_stock);
            }
            /* ----------------- */

            for ($j=0; $j < count($kardex_item_want_to_create); $j++) { 
                $this->Kardex_item->create($kardex_item_want_to_create[$j]);
            }

            // set prescription_id and inactive to current kardex
            $dt_kardex = array(
                'pharmacy_prescription_id' => $prescription_id,
                'is_active' => 0,
                'is_emergency_trigger' => 0,
            );
            $this->Kardex->update_v2($kardex_id, $dt_kardex);

            // Kardex Non Pharmacological
            $select = 'a.id, a.non_pharmacological_id, a.date, a.doctor_added';
            $where = array(
                'a.kardex_id' => $kardex_id,
                'a.is_delete' => 0,
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where);
            for ($j=0; $j < count($kardex_non_pharmacological); $j++) { 
                $non_pharmacological_id = $kardex_non_pharmacological[$j]->non_pharmacological_id;
                $non_pharmacological_date = $kardex_non_pharmacological[$j]->date;
                $non_pharmacological_doctor_added = $kardex_non_pharmacological[$j]->doctor_added;

                // Data kardex non farmakologis
                $dt_kardex_non_pharmacological = array(
                    'kardex_id' => $new_kardex_id,
                    'non_pharmacological_id' => $non_pharmacological_id,
                    'date' => $non_pharmacological_date,
                    'doctor_added' => $non_pharmacological_doctor_added,
                    'is_delete' => 0,
                    'user_added' => $user_id,
                    'date_added' => date('Y-m-d H:i:s'),
                    'date_updated' => date('Y-m-d H:i:s'),
                );

                $this->Kardex_non_pharmacological->create($dt_kardex_non_pharmacological);
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
            $this->REST_Return(500, 'error while trying to order do', $response_data);
            return;
        }

        Logger::info($this->debug_uniq, "|---- end order do ----|");

        $response_data = [];

        $this->REST_Return(200, 'OK', $response_data);
        return;
    }

    public function stock_get($p = 0) {
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

        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;

        if ($registration_destination_id == null) {
            $this->REST_Return(400, 'registration_destination_id is required');
            return;
        }

        $select = 'a.*, pc.package_id, p.name as package_name, pc.name as catalog_name, st.name as signatura_times_name, u.name as dose_unit_name, u2.name as current_amount_unit_name, pc.dose as catalog_dose, pc.dose_unit_id as catalog_dose_unit_id, u3.name as catalog_dose_unit_name, pc.packaging_value as catalog_packaging_value, pc.packaging_unit_id as catalog_packaging_unit_id, u4.name as catalog_packaging_unit_name';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'LEFT'),
            array('package p', 'pc.package_id = p.id', 'LEFT'),
            array('signatura_times st', 'a.signatura_times_id = st.id', 'LEFT'),
            array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
            array('unit u2', 'a.current_amount_unit_id = u2.id', 'LEFT'),
            array('unit u3', 'pc.dose_unit_id = u3.id', 'LEFT'),
            array('unit u4', 'pc.packaging_unit_id = u4.id', 'LEFT'),
        );
        $order = array(
            'a.sort' => 'ASC',
        );
        $output = $this->Kardex_stock->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_id = $output[$i]->registration_destination_id;
            $pharmacy_catalog_id = $output[$i]->pharmacy_catalog_id;
            $package_id = $output[$i]->package_id;
            $catalog_dose = $output[$i]->catalog_dose;
            $catalog_dose_unit_id = $output[$i]->catalog_dose_unit_id;
            $catalog_packaging_value = $output[$i]->catalog_packaging_value;
            $catalog_packaging_unit_id = $output[$i]->catalog_packaging_unit_id;

            $is_incomplete_master = 0;
            if ($package_id == null || $catalog_dose == null || $catalog_dose_unit_id == null || $catalog_packaging_value == null || $catalog_packaging_unit_id == null) {
                $is_incomplete_master = 1;
            }

            $output[$i]->is_incomplete_master = $is_incomplete_master;

            $select = 'a.id, a.status';
            $where = array(
                'k.registration_destination_id' => $registration_destination_id,
                // 'k.is_active' => 1,
                'a.pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $join = array(
                array('kardex k', 'k.id = a.kardex_id', 'INNER'),
            );
            $order = array(
                'k.date_added' => 'DESC',
            );
            $this->Kardex_item->limit = 1;
            $active_item = $this->Kardex_item->select(0, $select, $where, $join, $order);
            $active_item_status = 1;
            if (count($active_item) > 0) {
                $active_item_status = $active_item[0]->status;
            }
            $output[$i]->status = $active_item_status;

            $output[$i]->kardex_concoction = [];
            $select = 'a.id, a.dose, u.name as dose_unit_name, 
            pc.name as pharmacy_catalog_name, 
            pc.package_id, p.name as package_name, 
            pc.dose as catalog_dose, 
            pc.dose_unit_id as catalog_dose_unit_id, 
            u2.name as catalog_dose_unit_name, 
            pc.packaging_value as catalog_packaging_value, 
            pc.packaging_unit_id as catalog_packaging_unit_id,
            u3.name as catalog_packaging_unit_name';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.parent_pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $join = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
                array('unit u2', 'pc.dose_unit_id = u2.id', 'LEFT'),
                array('unit u3', 'pc.packaging_unit_id = u3.id', 'LEFT'),
            );
            $kdx_stock_concoction = $this->Kardex_stock_concoction->select(0, $select, $where, $join);
            for ($j=0; $j < count($kdx_stock_concoction); $j++) { 
                $con_package_id = $kdx_stock_concoction[$j]->package_id;
                $con_catalog_dose = $kdx_stock_concoction[$j]->catalog_dose;
                $con_catalog_dose_unit_id = $kdx_stock_concoction[$j]->catalog_dose_unit_id;
                $con_catalog_packaging_value = $kdx_stock_concoction[$j]->catalog_packaging_value;
                $con_catalog_packaging_unit_id = $kdx_stock_concoction[$j]->catalog_packaging_unit_id;
    
                $is_incomplete_master = 0;
                if ($con_package_id == null || $con_catalog_dose == null || $con_catalog_dose_unit_id == null || $con_catalog_packaging_value == null || $con_catalog_packaging_unit_id == null) {
                    $is_incomplete_master = 1;
                }

                $kdx_stock_concoction[$j]->is_incomplete_master = $is_incomplete_master;
            }
            $output[$i]->kardex_concoction = $kdx_stock_concoction;
        }

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    function new_kardex_inpatient($utils) {
        $user_id = $utils['user_id'];
        $norm = $utils['norm'];

        // Mencari registration_destination rawat inap yang tidak memiliki kardex
        $select = 'a.id, a.registration_id, a.doctor_in_charge_id, a.health_facility_item_id, k.id as kardex_id';
        $where = array(
            'hfi.health_facility_id' => '40f06534-07ec-4221-ad9d-dea08196079f', //RAWAT INAP
            'a.is_checkout' => 0,
            'r.is_cancel' => 0,
        );
        if ($norm != null) {
            $where['r.norm'] = $norm;
        }
        $where_kardex = "k.is_emergency_trigger = 1 OR k.id IS NULL";
        $this->db->where($where_kardex);
        $join = array(
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('kardex k', 'a.id = k.registration_destination_id AND k.is_active = 1', 'LEFT'),
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination_not_active = $this->Registration_destination->select(0, $select, $where, $join);
        $count_registration_destination_not_active = count($registration_destination_not_active);

        Logger::info($this->debug_uniq, "total new inpatient: $count_registration_destination_not_active");

        for ($i=0; $i < count($registration_destination_not_active); $i++) { 
            $registration_destination_not_active_id = $registration_destination_not_active[$i]->id;

            Logger::info($this->debug_uniq, "---processing registration_destination_not_active_id: $registration_destination_not_active_id");

            $registration_id = $registration_destination_not_active[$i]->registration_id;
            $doctor_in_charge_id = $registration_destination_not_active[$i]->doctor_in_charge_id;
            $health_facility_item_id = $registration_destination_not_active[$i]->health_facility_item_id;
            $kardex_id = $registration_destination_not_active[$i]->kardex_id;
            $soap_id = null;

            // Mencari soap awal rawat inap untuk menempelkan kardex
            $select = 'a.id';
            $where = array(
                'a.registration_destination_id' => $registration_destination_not_active_id,
                'a.is_initial_inpatient' => 1,
            );
            $soap = $this->Soap->select(0, $select, $where);
            if (count($soap) > 0) {
                $soap_id = $soap[0]->id;
            }
            Logger::info($this->debug_uniq, "soap_id: $soap_id");

            // Mencari registration_destination sebelum pasien masuk rawat inap
            $select = 'a.id';
            $where = array(
                'a.registration_id' => $registration_id,
            );
            $where_destination = "a.id != '$registration_destination_not_active_id' AND hfi.health_facility_id != '40f06534-07ec-4221-ad9d-dea08196079f' AND hfi.health_facility_id != '3cb626f5-9148-4fb1-8663-3d7f028b01a6'";
            $this->db->where($where_destination);
            $join = array(
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            );
            $registration_destination_past = $this->Registration_destination->select(0, $select, $where, $join);
            if (count($registration_destination_past) == 0) {
                Logger::info($this->debug_uniq, "registration_destination_past not found (direct inpatient or poliklinik), skipping");
                continue;
            }

            $registration_destination_past_id = $registration_destination_past[0]->id;
            Logger::info($this->debug_uniq, "registration_destination_past_id: $registration_destination_past_id");
            
            // Mencari order resep dari IGD/Poli yang sudah diproses agar dibuatkan kardex
            $select = 'a.id, a.doctor_in_charge_id, ep.position_id';
            $where = array(
                'a.registration_destination_id' => $registration_destination_past_id,
                'a.is_delete' => 0,
                'a.flag' => 1,
            );
            $where_prescription = "(a.soap_p_id IS NOT NULL AND a.soap_p_id != '')";
            $this->db->where($where_prescription);
            $join = array(
                array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                array('employee_position ep', 'e.id = ep.employee_id', 'INNER'),
            );
            $prescription_not_active = $this->Prescription->select(0, $select, $where, $join);

            Logger::info($this->debug_uniq, "total prescription_past: " . count($prescription_not_active));

            $kardex_want_to_create = null;
            $kardex_item_want_to_create = [];

            if (count($prescription_not_active) > 0) {
                $dt_kardex = array(
                    'registration_destination_id' => $registration_destination_not_active_id,
                    'doctor_in_charge_id'         => $doctor_in_charge_id,
                    'health_facility_item_id'     => $health_facility_item_id,
                    'soap_id'                     => $soap_id,
                    'is_active'                   => 1,
                    'user_added'                  => $user_id,
                    'date_added'                  => date('Y-m-d H:i:s'),
                    'date_updated'                => date('Y-m-d H:i:s'),
                );
                
                $kardex_want_to_create = $dt_kardex;
            }

            for ($j=0; $j < count($prescription_not_active); $j++) { 
                $prescription_not_active_id = $prescription_not_active[$j]->id;
                $prescription_not_active_doctor_in_charge_id = $prescription_not_active[$j]->doctor_in_charge_id;
                $prescription_not_active_position_id = $prescription_not_active[$j]->position_id;

                // Dokter Umum skip
                if ($prescription_not_active_position_id == 'd93bd445-df1e-451a-bf95-5fc10546c133') {
                    continue;
                }

                $select = '
                a.pharmacy_catalog_id,
                pc.category_catalog_id,
                a.package_id,
                a.package_name,
                a.signatura_times_id,
                a.administer_id,
                a.dose,
                a.dose_unit_id,
                a.qty,
                a.used,
                a.desc,
                a.status_kardex,
                a.sort,
                a.prescription_item_type_id,
                a.prescription_item_type_used,
                a.prescription_item_type_value,
                a.prescription_item_type_package
                ';
                $where = array(
                    'a.pharmacy_prescription_id' => $prescription_not_active_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'LEFT'),
                );
                $prescription_item_not_active = $this->Prescription_item->select(0, $select, $where);

                for ($k=0; $k < count($prescription_item_not_active); $k++) { 
                    $pharmacy_catalog_id = $prescription_item_not_active[$k]->pharmacy_catalog_id;
                    $category_catalog_id = $prescription_item_not_active[$k]->category_catalog_id;
                    $package_id = $prescription_item_not_active[$k]->package_id;
                    $package_name = $prescription_item_not_active[$k]->package_name;
                    $signatura_times_id = $prescription_item_not_active[$k]->signatura_times_id;
                    $administer_id = $prescription_item_not_active[$k]->administer_id;
                    $dose = $prescription_item_not_active[$k]->dose;
                    $dose_unit_id = $prescription_item_not_active[$k]->dose_unit_id;
                    $qty = $prescription_item_not_active[$k]->qty;
                    $used = $prescription_item_not_active[$k]->used;
                    $desc = $prescription_item_not_active[$k]->desc;
                    $status_kardex = $prescription_item_not_active[$k]->status_kardex;
                    $sort = $prescription_item_not_active[$k]->sort;
                    $prescription_item_type_id = $prescription_item_not_active[$k]->prescription_item_type_id;
                    $prescription_item_type_used = $prescription_item_not_active[$k]->prescription_item_type_used;
                    $prescription_item_type_value = $prescription_item_not_active[$k]->prescription_item_type_value;
                    $prescription_item_type_package = $prescription_item_not_active[$k]->prescription_item_type_package;

                    // BMHP & BMHP BPJS, JIKA EXTRA
                    if ($category_catalog_id == '12ba0be8-cd71-41a3-83f4-8d6bd6c40c9b' || $category_catalog_id == '13800b49-6681-4903-b352-034e28ccbfec' || $status_kardex == 3) {
                        continue;
                    }
    
                    $dt_kardex_item = array(
                        'kardex_id'                     => null,
                        'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                        'package_id'                    => $package_id,
                        'signatura_times_id'            => $signatura_times_id,
                        'administer_id'                 => $administer_id,
                        'dose'                          => $dose,
                        'dose_unit_id'                  => $dose_unit_id,
                        'qty'                           => $qty,
                        'used'                          => $used,
                        'desc'                          => $desc,
                        'sort'                          => $sort,
                        'prescription_item_type_id'     => $prescription_item_type_id,
                        'prescription_item_type_used'   => $prescription_item_type_used,
                        'prescription_item_type_value'  => $prescription_item_type_value,
                        'prescription_item_type_package'=> $prescription_item_type_package,
                        'catalog_date'                  => date('Y-m-d H:i:s'),
                        'doctor_added'                  => $prescription_not_active_doctor_in_charge_id,
                        'status'                        => $status_kardex,
                        'is_change'                     => 1,
                        'user_added'                    => $user_id,
                        'date_added'                    => date('Y-m-d H:i:s'),
                        'date_updated'                  => date('Y-m-d H:i:s'),
                    );

                    // if ($signatura_times_id != null && $administer_id != null && $dose != null && $dose_unit_id != null) {
                    //     array_push($kardex_item_want_to_create, $dt_kardex_item);
                    // }
                    array_push($kardex_item_want_to_create, $dt_kardex_item);
                }
            }

            // Array untuk prevent duplikat catalog di Kardex Item
            $kardex_item_catalog_ids = [];

            if (count($kardex_item_want_to_create) > 0) {
                if ($kardex_id == null) {
                    $kardex_id = $this->Kardex->create($kardex_want_to_create);
                    Logger::info($this->debug_uniq, "new kardex_id: " . $kardex_id);
                } else {
                    Logger::info($this->debug_uniq, "from trigger kardex_id: " . $kardex_id);

                    $select = 'a.id, a.pharmacy_catalog_id';
                    $where = array(
                        'a.kardex_id' => $kardex_id,
                    );
                    $kardex_item_trigger = $this->Kardex_item->select(0, $select, $where);
                    for ($j=0; $j < count($kardex_item_trigger); $j++) { 
                        $kardex_item_trigger_pharmacy_catalog_id = $kardex_item_trigger[$j]->pharmacy_catalog_id;
                        if (!in_array($kardex_item_trigger_pharmacy_catalog_id, $kardex_item_catalog_ids)) {
                            array_push($kardex_item_catalog_ids, $kardex_item_trigger_pharmacy_catalog_id); // Push ke array prevent duplikat
                        }
                    }
                }

                for ($j=0; $j < count($kardex_item_want_to_create); $j++) { 
                    $pharmacy_catalog_id = $kardex_item_want_to_create[$j]['pharmacy_catalog_id'];

                    if (!in_array($pharmacy_catalog_id, $kardex_item_catalog_ids)) {
                        $kardex_item_want_to_create[$j]['kardex_id'] = $kardex_id;
                        $this->Kardex_item->create($kardex_item_want_to_create[$j]);
                        array_push($kardex_item_catalog_ids, $pharmacy_catalog_id); // Push ke array prevent duplikat
                    }
                }
            } else {
                Logger::info($this->debug_uniq, "incomplete prescription, skip kardex...");
            }
        }

        Logger::info($this->debug_uniq, "---success input kardex for new inpatient---");
    }

    public function take_order_put($p = 0) {
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
        $this->form_validation->set_rules($this->take_order_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $kardex_id = $id;

        if ($kardex_id === 0 || $kardex_id === "0") {
            $this->REST_Return(400, 'kardex_id is required');
            return;
        }

        $select = 'a.id, a.suggestion_status, u.id as doctor_in_charge_user_id';
        $where = array();
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
            array('user u', 'rd.doctor_in_charge_id = u.employee_id', 'LEFT'),
        );
        $kardex = $this->Kardex->select($kardex_id, $select, $where, $join);
        if (count($kardex) == 0) {
            $this->REST_Return(404, 'there is no kardex with id ' . $kardex_id);
            return;
        }

        $doctor_in_charge_user_id = $kardex[0]->doctor_in_charge_user_id;
        $suggestion_status = $kardex[0]->suggestion_status;

        if ($suggestion_status != 0) {
            $this->REST_Return(409, 'kardex already taken or processed');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_kardex = array(
            'taken_suggestion_user' => $user_id,
            'suggestion_status' => 1,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $this->$model->update_v2($kardex_id, $dt_kardex);
        
        // DPJP
        if ($doctor_in_charge_user_id != null) {
            $this->Notifications->readNotificationWithItem($doctor_in_charge_user_id, $kardex_id, 'kardex_id');
        }

        // Dokter Umum
        $this->Notifications->readNotificationPositionWithItem('d93bd445-df1e-451a-bf95-5fc10546c133', 'kardex', $kardex_id, 'kardex_id');

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kardex,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to take order kardex', $response_data);
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

        $utils = ['user_id' => $user_id];

        $kardex_id = $id;

        if ($kardex_id === 0 || $kardex_id === "0") {
            $this->REST_Return(400, 'kardex_id is required');
            return;
        }

        $select = 'a.id, a.suggestion_status, a.registration_destination_id, a.doctor_in_charge_id, a.health_facility_item_id, a.soap_id, a.cppt_id, a.sbar_id';
        $kardex = $this->Kardex->select($kardex_id, $select);
        if (count($kardex) == 0) {
            $this->REST_Return(404, 'there is no kardex with id ' . $kardex_id);
            return;
        }

        $registration_destination_id = $kardex[0]->registration_destination_id;
        $doctor_in_charge_id = $kardex[0]->doctor_in_charge_id;
        $health_facility_item_id = $kardex[0]->health_facility_item_id;
        $soap_id = $kardex[0]->soap_id;
        $cppt_id = $kardex[0]->cppt_id;
        $sbar_id = $kardex[0]->sbar_id;
        
        $suggestion_status = $kardex[0]->suggestion_status;

        if ($suggestion_status != 1) {
            $this->REST_Return(409, 'kardex already processed or not taken');
            return;
        }

        $is_approve = $dt_put['is_approve'];
        $reject_items = $dt_put['reject_items'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_kardex = array(
            'is_approve' => $is_approve,
            'suggestion_status' => 2,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );
        
        if ($is_approve == 1) {
            $order = false; // Untuk cek apakah perubahan cuma stop, tidak perlu order
            
            $select = 'a.id, a.status, a.is_change';
            $where = array(
                'a.kardex_id' => $kardex_id,
            );
            $kardex_item = $this->Kardex_item->select(0, $select, $where);
            for ($i=0; $i < count($kardex_item); $i++) { 
                $status = $kardex_item[$i]->status;
                $is_change = $kardex_item[$i]->is_change;

                if ($is_change == 1 && $status != 4) {
                    $order = true;
                }
            }

            for ($j=0; $j < count($reject_items); $j++) { 
                $reject_item_id = $reject_items[$j]['id'] ?? null;
                
                if ($reject_item_id == null) {
                    $this->REST_Return(400, 'id in reject_item is required');
                    return;
                }
                
                $dt_reject_item = array(
                    'is_reject' => 1,
                    'user_updated' => $user_id,
                    'date_updated' => date('Y-m-d H:i:s'),
                );
                $this->Kardex_item->update_v2($reject_item_id, $dt_reject_item);
            }
            
            if ($order) {
                $this->order_put($kardex_id);
            } else {
                $dt_kardex['is_active'] = 0;
                $utils['kardex_id'] = $kardex_id;

                $dt_kardex_parent = array(
                    'registration_destination_id' => $registration_destination_id,
                    'doctor_in_charge_id'         => $doctor_in_charge_id,
                    'health_facility_item_id'     => $health_facility_item_id,
                    'soap_id'                     => null,
                    'cppt_id'                     => null,
                    'sbar_id'                     => null,
                    'is_active'                   => 1,
                    'user_added'                  => $user_id,
                    'date_added'                  => date('Y-m-d H:i:s'),
                    'date_updated'                => date('Y-m-d H:i:s'),
                );

                $this->kardex_fresh_after_reject($utils, $dt_kardex_parent, $registration_destination_id);
            }
        } else {
            $dt_kardex['is_active'] = 0;

            $dt_kardex_parent = array(
                'registration_destination_id' => $registration_destination_id,
                'doctor_in_charge_id'         => $doctor_in_charge_id,
                'health_facility_item_id'     => $health_facility_item_id,
                'soap_id'                     => null,
                'cppt_id'                     => null,
                'sbar_id'                     => null,
                'is_active'                   => 1,
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s'),
                'date_updated'                => date('Y-m-d H:i:s'),
            );
            
            $this->kardex_fresh_after_reject($utils, $dt_kardex_parent, $registration_destination_id);
        }

        $this->$model->update_v2($kardex_id, $dt_kardex);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kardex,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to take order kardex', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    function kardex_fresh_after_reject($utils, $dt_kardex_parent, $registration_destination_id) {
        $user_id = $utils['user_id'];
        $kardex_id = $utils['kardex_id'] ?? null;

        $fresh_kardex_id = $this->Kardex->create($dt_kardex_parent);
        
        if ($kardex_id == null) {
            $select = 'a.id';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.is_active' => 0,
            );
            $where_kardex = "(a.is_suggestion = 0 OR (a.is_suggestion = 1 AND a.is_approve = 1))";
            $this->db->where($where_kardex);
            $join = array();
            $order = array(
                'a.date_added' => 'DESC',
            );
            $this->Kardex->limit = 1;
            $kardex = $this->Kardex->select(0, $select, $where, $join, $order);

            $kardex_id = $kardex[0]->id;
        }

        $select = 'a.id, 
        a.pharmacy_catalog_id,
        pc.name as pharmacy_catalog_name,
        pc.dose as catalog_dose,
        u2.name as catalog_dose_unit_name,
        pc.packaging_value as catalog_packaging_value,
        u3.name as catalog_packaging_unit_name,
        a.package_id,
        p.name as package_name,
        a.signatura_times_id,
        st.name as signatura_times_name,
        a.dose,
        a.dose_unit_id,
        u.name as dose_unit_name,
        a.administer_id,
        pci.name as administer_name,
        a.qty,
        a.desc,
        a.sort,
        a.doctor_added,
        a.catalog_date,
        a.stop_date,
        a.prescription_item_type_id,
        a.prescription_item_type_package,
        a.prescription_item_type_value,
        a.status,
        a.is_reject
        ';
        $where = array(
            'a.kardex_id' => $kardex_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('package p', 'a.package_id = p.id', 'INNER'),
            array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            array('unit u', 'a.dose_unit_id = u.id', 'LEFT'),
            array('unit u2', 'pc.dose_unit_id = u2.id', 'LEFT'),
            array('unit u3', 'pc.packaging_unit_id = u3.id', 'LEFT'),
            array('signatura_times st', 'a.signatura_times_id = st.id', 'LEFT'),
            array('pharmacy_catalog_instruction pci', 'a.administer_id = pci.id', 'LEFT'),
        );
        $kardex_item = $this->Kardex_item->select(0, $select, $where, $join);
        for ($i=0; $i < count($kardex_item); $i++) { 
            $pharmacy_catalog_id = $kardex_item[$i]->pharmacy_catalog_id;
            $package_id = $kardex_item[$i]->package_id;
            $signatura_times_id = $kardex_item[$i]->signatura_times_id;
            $administer_id = $kardex_item[$i]->administer_id;
            $dose = $kardex_item[$i]->dose;
            $dose_unit_id = $kardex_item[$i]->dose_unit_id;
            $desc = $kardex_item[$i]->desc;
            $sort = $kardex_item[$i]->sort;
            $doctor_added = $kardex_item[$i]->doctor_added;
            $catalog_date = $kardex_item[$i]->catalog_date;
            $stop_date = $kardex_item[$i]->stop_date;
            $prescription_item_type_id = $kardex_item[$i]->prescription_item_type_id;
            $prescription_item_type_package = $kardex_item[$i]->prescription_item_type_package;
            $prescription_item_type_value = $kardex_item[$i]->prescription_item_type_value;
            $status = $kardex_item[$i]->status;
            $is_reject = $kardex_item[$i]->is_reject;

            // Jika bukan stop order, misal Obat Tambahan di kardex sebelum dijadikan 1 Reguler
            if ($status != 4) {
                $status = 1;
            }

            // Item yang ditolak
            if ($is_reject == 1) {
                continue;
            }

            // Membuat kardex item baru
            $dt_kardex_item = array(
                'kardex_id'                     => $fresh_kardex_id,
                'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                'package_id'                    => $package_id,
                'signatura_times_id'            => $signatura_times_id,
                'administer_id'                 => $administer_id,
                'dose'                          => $dose,
                'dose_unit_id'                  => $dose_unit_id,
                'qty'                           => null,
                'desc'                          => $desc,
                'sort'                          => $sort,
                'prescription_item_type_id'     => $prescription_item_type_id,
                'prescription_item_type_package'=> $prescription_item_type_package,
                'prescription_item_type_value'  => $prescription_item_type_value,
                'doctor_added'                  => $doctor_added,
                'catalog_date'                  => $catalog_date,
                'stop_date'                     => $stop_date,
                'status'                        => $status,
                'is_change'                     => 0,
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s'),
                'date_updated'                  => date('Y-m-d H:i:s'),
            );
            $this->Kardex_item->create($dt_kardex_item);
        }

        // Membuat kardex non farmakologis ke kardex baru dengan mengamil dari karder yang sudah di-order
        $select = 'a.id, a.non_pharmacological_id, a.doctor_added, a.date';
        $where = array(
            'a.kardex_id' => $kardex_id,
            'a.is_delete' => 0,
        );
        $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where);
        for ($i=0; $i < count($kardex_non_pharmacological); $i++) { 
            $non_pharmacological_id = $kardex_non_pharmacological[$i]->non_pharmacological_id;
            $non_pharmacological_doctor_added = $kardex_non_pharmacological[$i]->doctor_added;
            $non_pharmacological_date = $kardex_non_pharmacological[$i]->date;
            
            $dt_kardex_non_pharmacological = array(
                'kardex_id' => $fresh_kardex_id,
                'non_pharmacological_id' => $non_pharmacological_id,
                'doctor_added' => $non_pharmacological_doctor_added,
                'date' => $non_pharmacological_date,
                'date_added' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            );

            $this->Kardex_non_pharmacological->create($dt_kardex_non_pharmacological);
        }
    }
}