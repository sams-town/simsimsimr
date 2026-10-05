<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Sbar extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("$this->directory/Soap_p_model", "Soap_p");
        $this->load->model("v1/health_facility/Laboratory_request_model", "Laboratory_request");
        $this->load->model("v1/health_facility/Laboratory_request_item_model", "Laboratory_request_item");
        $this->load->model("v1/health_facility/Laboratory_package_model", "Laboratory_package");
        $this->load->model("v1/health_facility/Radiology_request_model", "Radiology_request");
        $this->load->model("v1/health_facility/Radiology_request_item_model", "Radiology_request_item");
        $this->load->model("v1/health_facility/Radiology_request_item_image_model", "Radiology_request_item_image");
        $this->load->model("v1/health_facility/Radiology_package_model", "Radiology_package");
        $this->load->model("v1/health_facility/Physiotherapy_request_model", "Physiotherapy_request");
        $this->load->model("v1/health_facility/Physiotherapy_request_item_model", "Physiotherapy_request_item");
        $this->load->model("v1/health_facility/Physiotherapy_package_model", "Physiotherapy_package");
        $this->load->model("v1/pharmacy/Catalog_model", "Catalog");
        $this->load->model("v1/pharmacy/Depo_model", "Depo");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");
        $this->load->model("v1/pharmacy/Kardex_model", "Kardex");
        $this->load->model("v1/pharmacy/Kardex_item_model", "Kardex_item");
        $this->load->model("v1/pharmacy/Kardex_non_pharmacological_model", "Kardex_non_pharmacological");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        // Load Helper
        $this->load->helper('queue');

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'advice', 'label' => 'advice', 'rules' => 'trim'),
        );

        $this->validate_rules = array(
            array('field' => 'validation_status', 'label' => 'validation_status', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->verify_rules = array(
            array('field' => 'verification_status', 'label' => 'verification_status', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->decline_rules = array(
            array('field' => 'decline_reason', 'label' => 'decline_reason', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->save_verification_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id_sbar', 'label' => 'doctor_in_charge_id_sbar', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'consultation_via_type_id', 'label' => 'consultation_via_type_id', 'rules' => 'trim'),
            array('field' => 'consultation_via_anything_else', 'label' => 'consultation_via_anything_else', 'rules' => 'trim'),
        );

        $this->save_sbar_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'sbar', 'label' => 'sbar', 'rules' => 'trim'),
            array('field' => 'advice', 'label' => 'advice', 'rules' => 'trim'),
        );

        $this->save_laboratory_request_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'laboratory_request_items[]', 'label' => 'laboratory_request_items', 'rules' => 'trim'),
        );

        $this->save_radiology_request_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'radiology_request_items[]', 'label' => 'radiology_request_items', 'rules' => 'trim'),
        );

        $this->save_physiotherapy_request_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'physiotherapy_request_items[]', 'label' => 'physiotherapy_request_items', 'rules' => 'trim'),
        );

        $this->save_prescription_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'pharmacy_depo_id', 'label' => 'pharmacy_depo_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_prescription_id', 'label' => 'type_prescription_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'form_prescription[]', 'label' => 'form_prescription', 'rules' => 'trim'),
            array('field' => 'form_prescription_parent[]', 'label' => 'form_prescription_parent', 'rules' => 'trim'),
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
    
        $sbar_id = $id;
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($sbar_id);
        $response_data = array();
    
        if (count($output) == 0) {
            $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
            return;
        }

        $user_added = $output[0]->user_added;
        $sbar_id = $output[0]->id;
        $soap_id = $output[0]->soap_id;

        $select = 'a.id, a.employee_id, ug.group_id';
        $where = array();
        $join = array(
            array('user_group ug', 'a.user_id = ug.user_id', 'INNER'),
        );
        $user = $this->User->select($user_added, $select);

        $user_added_group_id = $user[0]->group_id;
        $employee_id = $user[0]->employee_id;

        $output[0]->user_added_group_id = $user_added_group_id;

        $select = 'a.id, a.fullname, p.name as position_name, a.ttd';
        $where = array();
        $join = array(
            array('employee_position ep', 'a.id = ep.employee_id', 'INNER'),
            array('position p', 'ep.position_id = p.id', 'INNER'),
        );
        $order = array('a.id' => 'ASC');
        $employee = $this->Employee->select($employee_id, $select, $where, $join, $order);

        $output[0]->user_added_name = $employee[0]->fullname;
        $output[0]->user_added_role = $employee[0]->position_name;
        $output[0]->user_added_ttd = $employee[0]->ttd;

        // LABORATORY_REQUEST
        $select = 'a.id, a.no_request, a.status, a.date_request';
        $where = array(
            'a.sbar_id' => $sbar_id,
            'a.soap_id' => $soap_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
        );
        $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

        $output[0]->laboratory_request = $laboratory_request;

        for ($j=0; $j < count($laboratory_request); $j++) { 
            $laboratory_request_id = $laboratory_request[$j]->id;

            $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, a.costing_name, a.service_class_id, lp.code_lis';
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

                        // keluarin semua hasil lab
                        // $flag != '' && 
                        if ($parameter_id == $code_lis) {
                            array_push($laboratory_request_items[$k]->result, $result[$l]);
                        }
                    }
                }
            }

            $output[0]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
        }

        // RADIOLOGY_REQUEST
        $select = 'a.id, a.date_request, a.no_request, a.queue, a.clinical_indications, a.status';
        $where = array(
            'a.sbar_id' => $sbar_id,
            'a.soap_id' => $soap_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
        );
        $radiology_request = $this->Radiology_request->select(0, $select, $where);

        $output[0]->radiology_request = $radiology_request;

        for ($j=0; $j < count($radiology_request); $j++) { 
            $radiology_request_id = $radiology_request[$j]->id;

            $select = 'a.id, a.radiology_request_id, a.radiology_package_id, rp.name as radiology_package_name, a.service_class_id';
            $where = array(
                'a.radiology_request_id' => $radiology_request_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('radiology_package rp', 'a.radiology_package_id = rp.id', 'INNER'),
            );
            $radiology_request_items = $this->Radiology_request_item->select(0, $select, $where, $join);

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
                }

                $radiology_request_items[$k]->url = $url;
            }

            $output[0]->radiology_request[$j]->radiology_request_items = $radiology_request_items;
        }

        // PHYSIOTHERAPY_REQUEST
        $select = 'a.id, a.date_request, a.no_request, a.queue';
        $where = array(
            'a.sbar_id' => $sbar_id,
            'a.soap_id' => $soap_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
        );
        $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);

        $output[0]->physiotherapy_request = $physiotherapy_request;

        for ($j=0; $j < count($physiotherapy_request); $j++) { 
            $physiotherapy_request_id = $physiotherapy_request[$j]->id;

            $select = 'a.id, a.physiotherapy_request_id, a.physiotherapy_package_id, a.costing_name as physiotherapy_package_name, a.service_class_id';
            $where = array(
                'a.physiotherapy_request_id' => $physiotherapy_request_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('physiotherapy_package rp', 'a.physiotherapy_package_id = rp.id', 'INNER'),
            );
            $physiotherapy_request_items = $this->Physiotherapy_request_item->select(0, $select, $where, $join);

            $output[0]->physiotherapy_request[$j]->physiotherapy_request_items = $physiotherapy_request_items;
        }

        // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
        $select = 'a.id, a.no_order, a.type_prescription_id, a.flag, a.date_added';
        $where = array(
            'a.sbar_id' => $sbar_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('sbar c', 'a.sbar_id = c.id', 'INNER'),
        );
        $prescription = $this->Prescription->select(0, $select, $where, $join);

        $output[0]->prescription = [];

        for ($k=0; $k < count($prescription); $k++) { 
            $pharmacy_prescription_id = $prescription[$k]->id;

            $select = '
                a.pharmacy_catalog_id, 
                pc.name as pharmacy_catalog_name, 
                pc.code as pharmacy_catalog_code,
                a.prescription_item_type_id, 
                a.prescription_item_type_used, 
                a.prescription_item_type_value,
                a.prescription_item_type_package,
                a.package_id,
                a.package_name, 
                a.signatura_times_id,
                a.administer_id,
                a.dose, 
                p.name as dose_name, 
                a.qty, 
                a.sort, 
                a.used, 
                a.desc, 
                a.qty_doctor
            ';
            $where = array(
                'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'pc.dose_id = p.id', 'LEFT'),
            );
            $prescription_item = $this->Prescription_item->select(0, $select, $where, $join);

            $prescription[$k]->form_prescription = $prescription_item;

            $form_prescription_parent = [];

            //------------------- detail form_prescription_parent -//
            for ($l=0; $l < count($prescription_item); $l++) { 
                $parent_id = $prescription_item[$l]->prescription_item_type_id; 
                $parent_used = $prescription_item[$l]->prescription_item_type_used; 
                $parent_value = $prescription_item[$l]->prescription_item_type_value; 
                $parent_package = $prescription_item[$l]->prescription_item_type_package; 
                $parent_signatura_times_id = $prescription_item[$l]->signatura_times_id; 
                $parent_administer_id = $prescription_item[$l]->administer_id; 

                $dt_form_prescription_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                    'package' => $parent_package,
                    'signatura_times_id' => $parent_signatura_times_id,
                    'administer_id' => $parent_administer_id,
                );

                if (count($form_prescription_parent) == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }

                $skip = 0;
                for ($m=0; $m < count($form_prescription_parent); $m++) { 
                    if ($form_prescription_parent[$m]['id'] == $parent_id) {
                        $skip = 1;
                    }
                }

                if ($skip == 0) {
                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                }
            }
            //----------------------------------------------------//

            $prescription[$k]->form_prescription_parent = $form_prescription_parent;
        }

        $output[0]->prescription = array_merge($output[0]->prescription, $prescription);

        // KARDEX
        $select = 'a.id, a.non_pharmacological_id, np.name, a.doctor_added, e.fullname as doctor_added_name';
        $where = array(
            'ks.source_id' => $sbar_id,
            'a.is_change' => 1,
            'ks.source_type_id' => 3,
        );
        $join = array(
            array('kardex k', 'a.kardex_id = k.id', 'INNER'),
            array('kardex_source ks', 'k.id = ks.kardex_id', 'LEFT'),
            array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
            array('employee e', 'a.doctor_added = e.id', 'INNER'),
        );
        $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
        if (count($kardex_non_pharmacological) == 0) {
            $where['k.sbar_id'] = $sbar_id;
            unset($where['ks.source_id']);
            unset($where['ks.source_type_id']);
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
        }

        $output[0]->kardex_non_pharmacological = $kardex_non_pharmacological;

        // pp.date_added replace punya kardex
        $select_kardex = 'a.*, pp.date_added as date_added, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
        $where_kardex = array(
            // 'a.sbar_id' => $sbar_id,
            'ks.source_id' => $sbar_id,
            'a.is_delete' => 0,
            'ks.source_type_id' => 3,
        );
        $join_kardex = array(
            array('kardex_source ks', 'a.id = ks.kardex_id', 'LEFT'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('user u', 'a.user_added = u.id', 'INNER'),
            array('employee e2', 'u.employee_id = e2.id', 'INNER'),
            array('pharmacy_prescription pp', 'a.pharmacy_prescription_id = pp.id', 'LEFT'),
        );
        $order_kardex = array(
            'a.date_added' => 'DESC',
        );
        $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);
        if (count($kardex) == 0) {
            $where_kardex['a.sbar_id'] = $sbar_id;
            unset($where_kardex['ks.source_id']);
            unset($where_kardex['ks.source_type_id']);
            $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
            $this->db->where($where_kar);
            $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);
        }

        $clean_kardex = [];
        for ($k=0; $k < count($kardex); $k++) { 
            $kardex_id = $kardex[$k]->id;

            $select_kardex_item = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name';
            $where_kardex_item = array(
                'a.kardex_id' => $kardex_id,
                'a.is_change' => 1,
            );
            $join_item = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('package p', 'a.package_id = p.id', 'INNER'),
            );
            $kardex_item = $this->Kardex_item->select(0, $select_kardex_item, $where_kardex_item, $join_item);

            $kardex[$k]->form_prescription = $kardex_item;

            $kardex_parent = [];

            //------------------- detail dt_kardex_parent -//
            for ($l=0; $l < count($kardex_item); $l++) { 
                $parent_id = $kardex_item[$l]->prescription_item_type_id; 
                $parent_used = $kardex_item[$l]->prescription_item_type_used; 
                $parent_value = $kardex_item[$l]->prescription_item_type_value; 
                $parent_package = $kardex_item[$l]->prescription_item_type_package; 
                $parent_desc = $kardex_item[$l]->prescription_item_type_desc; 
                $parent_signatura_times_id = $kardex_item[$l]->signatura_times_id; 
                $parent_administer_id = $kardex_item[$l]->administer_id; 

                $dt_kardex_parent = array(
                    'id' => $parent_id,
                    'used' => $parent_used,
                    'value' => $parent_value,
                    'package' => $parent_package,
                    'desc' => $parent_desc,
                    'signatura_times_id' => $parent_signatura_times_id,
                    'administer_id' => $parent_administer_id,
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
            }
            //----------------------------------------------------//

            $kardex[$k]->form_prescription_parent = $kardex_parent;
            
            if (count($kardex_item) > 0) {
                array_push($clean_kardex, $kardex[$k]);
            }
        }

        $output[0]->prescription = array_merge($output[0]->prescription, $clean_kardex);
    
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
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);

        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;
        if ($registration_destination_id != null) {
            $this->$model->whereFields['a.registration_destination_id'] = $registration_destination_id;
            unset($q_strings['registration_destination_id']);
        }
    
        $this->$model->whereLike = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $user_added = $output->rows[$i]->user_added;
            $sbar_id = $output->rows[$i]->id;
            $soap_id = $output->rows[$i]->soap_id;

            $select = 'a.id, a.employee_id, ug.group_id';
            $where = array();
            $join = array(
                array('user_group ug', 'a.id = ug.user_id', 'INNER'),
            );
            $user = $this->User->select($user_added, $select);

            $employee_id = $user[0]->employee_id;
            $user_added_group_id = $user[0]->group_id;

            $output->rows[$i]->user_added_group_id = $user_added_group_id;

            $select = 'a.id, a.fullname, p.name as position_name, a.ttd';
            $where = array();
            $join = array(
                array('employee_position ep', 'a.id = ep.employee_id', 'INNER'),
                array('position p', 'ep.position_id = p.id', 'INNER'),
            );
            $order = array('a.id' => 'ASC');
            $employee = $this->Employee->select($employee_id, $select, $where, $join, $order);

            $output->rows[$i]->user_added_name = $employee[0]->fullname;
            $output->rows[$i]->user_added_role = $employee[0]->position_name;
            $output->rows[$i]->user_added_ttd = $employee[0]->ttd;

            // LABORATORY_REQUEST
            $select = 'a.id, a.no_request, a.date_request, a.status';
            $where = array(
                'a.sbar_id' => $sbar_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

            $output->rows[$i]->laboratory_request = $laboratory_request;

            for ($j=0; $j < count($laboratory_request); $j++) { 
                $laboratory_request_id = $laboratory_request[$j]->id;

                $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, a.costing_name, a.service_class_id, lp.code_lis';
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

                            // keluarin semua hasil lab
                            // $flag != '' && 
                            if ($parameter_id == $code_lis) {
                                array_push($laboratory_request_items[$k]->result, $result[$l]);
                            }
                        }
                    }
                }

                $output->rows[$i]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
            }

            // RADIOLOGY_REQUEST
            $select = 'a.id, a.date_request, a.no_request, a.queue, a.clinical_indications, a.status';
            $where = array(
                'a.sbar_id' => $sbar_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $radiology_request = $this->Radiology_request->select(0, $select, $where);

            $output->rows[$i]->radiology_request = $radiology_request;

            for ($j=0; $j < count($radiology_request); $j++) { 
                $radiology_request_id = $radiology_request[$j]->id;

                $select = 'a.id, a.radiology_request_id, a.radiology_package_id, rp.name as radiology_package_name, a.service_class_id';
                $where = array(
                    'a.radiology_request_id' => $radiology_request_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('radiology_package rp', 'a.radiology_package_id = rp.id', 'INNER'),
                );
                $radiology_request_items = $this->Radiology_request_item->select(0, $select, $where, $join);

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
                    }

                    $radiology_request_items[$k]->url = $url;
                }

                $output->rows[$i]->radiology_request[$j]->radiology_request_items = $radiology_request_items;
            }

            // PHYSIOTHERAPY_REQUEST
            $select = 'a.id, a.date_request, a.no_request, a.queue';
            $where = array(
                'a.sbar_id' => $sbar_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);

            $output->rows[$i]->physiotherapy_request = $physiotherapy_request;

            for ($j=0; $j < count($physiotherapy_request); $j++) { 
                $physiotherapy_request_id = $physiotherapy_request[$j]->id;

                $select = 'a.id, a.physiotherapy_request_id, a.physiotherapy_package_id, a.costing_name as physiotherapy_package_name, a.service_class_id';
                $where = array(
                    'a.physiotherapy_request_id' => $physiotherapy_request_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('physiotherapy_package rp', 'a.physiotherapy_package_id = rp.id', 'INNER'),
                );
                $physiotherapy_request_items = $this->Physiotherapy_request_item->select(0, $select, $where, $join);

                $output->rows[$i]->physiotherapy_request[$j]->physiotherapy_request_items = $physiotherapy_request_items;
            }

            // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            $select = 'a.id, a.no_order';
            $where = array(
                'a.sbar_id' => $sbar_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('sbar c', 'a.sbar_id = c.id', 'INNER'),
            );
            $prescription = $this->Prescription->select(0, $select, $where, $join);

            $output->rows[$i]->prescription = [];

            for ($k=0; $k < count($prescription); $k++) { 
                $pharmacy_prescription_id = $prescription[$k]->id;

                $select = '
                    a.pharmacy_catalog_id, 
                    pc.name as pharmacy_catalog_name, 
                    pc.code as pharmacy_catalog_code,
                    a.prescription_item_type_id, 
                    a.prescription_item_type_used, 
                    a.prescription_item_type_value,
                    a.package_id,
                    a.package_id,
                    a.package_name, 
                    a.dose, 
                    p.name as dose_name, 
                    a.qty, 
                    a.sort, 
                    a.used, 
                    a.desc, 
                    a.qty_doctor
                ';
                $where = array(
                    'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                    array('package p', 'pc.dose_id = p.id', 'LEFT'),
                );
                $prescription_item = $this->Prescription_item->select(0, $select, $where, $join);

                $prescription[$k]->form_prescription = $prescription_item;

                $form_prescription_parent = [];

                //------------------- detail form_prescription_parent -//
                for ($l=0; $l < count($prescription_item); $l++) { 
                    $parent_id = $prescription_item[$l]->prescription_item_type_id; 
                    $parent_used = $prescription_item[$l]->prescription_item_type_used; 
                    $parent_value = $prescription_item[$l]->prescription_item_type_value; 
                    $parent_package = $prescription_item[$l]->prescription_item_type_package; 

                    $dt_form_prescription_parent = array(
                        'id' => $parent_id,
                        'used' => $parent_used,
                        'value' => $parent_value,
                        'package' => $parent_package,
                    );

                    if (count($form_prescription_parent) == 0) {
                        array_push($form_prescription_parent, $dt_form_prescription_parent);
                    }

                    $skip = 0;
                    for ($m=0; $m < count($form_prescription_parent); $m++) { 
                        if ($form_prescription_parent[$m]['id'] == $parent_id) {
                            $skip = 1;
                        }
                    }

                    if ($skip == 0) {
                        array_push($form_prescription_parent, $dt_form_prescription_parent);
                    }
                }
                //----------------------------------------------------//

                $prescription[$k]->form_prescription_parent = $form_prescription_parent;
            }

            $output->rows[$i]->prescription = array_merge($output->rows[$i]->prescription, $prescription);

            // KARDEX
            $select = 'a.id, a.non_pharmacological_id, np.name, a.doctor_added, e.fullname as doctor_added_name';
            $where = array(
                'ks.source_id' => $sbar_id,
                'a.is_change' => 1,
                'ks.source_type_id' => 3,
            );
            $join = array(
                array('kardex k', 'a.kardex_id = k.id', 'INNER'),
                array('kardex_source ks', 'k.id = ks.kardex_id', 'LEFT'),
                array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'INNER'),
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
            if (count($kardex_non_pharmacological) == 0) {
                $where['k.sbar_id'] = $sbar_id;
                unset($where['ks.source_id']);
                unset($where['ks.source_type_id']);
                $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
            }

            $output->rows[$i]->kardex_non_pharmacological = $kardex_non_pharmacological;

            // pp.date_added replace punya kardex
            $select_kardex = 'a.*, pp.date_added as date_added, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            $where_kardex = array(
                // 'a.sbar_id' => $sbar_id,
                'ks.source_id' => $sbar_id,
                'a.is_delete' => 0,
                'ks.source_type_id' => 3,
            );
            $join_kardex = array(
                array('kardex_source ks', 'a.id = ks.kardex_id', 'LEFT'),
                array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                array('user u', 'a.user_added = u.id', 'INNER'),
                array('employee e2', 'u.employee_id = e2.id', 'INNER'),
                array('pharmacy_prescription pp', 'a.pharmacy_prescription_id = pp.id', 'LEFT'),
            );
            $order_kardex = array(
                'a.date_added' => 'DESC',
            );
            $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);
            if (count($kardex) == 0) {
                $where_kardex['a.sbar_id'] = $sbar_id;
                unset($where_kardex['ks.source_id']);
                unset($where_kardex['ks.source_type_id']);
                $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
                $this->db->where($where_kar);
                $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);
            }

            $clean_kardex = [];
            for ($k=0; $k < count($kardex); $k++) { 
                $kardex_id = $kardex[$k]->id;

                $select_kardex_item = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name';
                $where_kardex_item = array(
                    'a.kardex_id' => $kardex_id,
                    'a.is_change' => 1,
                );
                $join_item = array(
                    array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                    array('package p', 'a.package_id = p.id', 'INNER'),
                );
                $kardex_item = $this->Kardex_item->select(0, $select_kardex_item, $where_kardex_item, $join_item);

                $kardex[$k]->form_prescription = $kardex_item;

                $kardex_parent = [];

                //------------------- detail dt_kardex_parent -//
                for ($l=0; $l < count($kardex_item); $l++) { 
                    $parent_id = $kardex_item[$l]->prescription_item_type_id; 
                    $parent_used = $kardex_item[$l]->prescription_item_type_used; 
                    $parent_value = $kardex_item[$l]->prescription_item_type_value; 
                    $parent_package = $kardex_item[$l]->prescription_item_type_package; 
                    $parent_desc = $kardex_item[$l]->prescription_item_type_desc; 
                    $parent_signatura_times_id = $kardex_item[$l]->signatura_times_id; 
                    $parent_administer_id = $kardex_item[$l]->administer_id; 

                    $dt_kardex_parent = array(
                        'id' => $parent_id,
                        'used' => $parent_used,
                        'value' => $parent_value,
                        'package' => $parent_package,
                        'desc' => $parent_desc,
                        'signatura_times_id' => $parent_signatura_times_id,
                        'administer_id' => $parent_administer_id,
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
                }
                //----------------------------------------------------//

                $kardex[$k]->form_prescription_parent = $kardex_parent;
                
                if (count($kardex_item) > 0) {
                    array_push($clean_kardex, $kardex[$k]);
                }
            }

            $output->rows[$i]->prescription = array_merge($output->rows[$i]->prescription, $clean_kardex);
        }
        
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

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        
        $registration_destination_id = $dt_post['registration_destination_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

        $select = 'a.id, a.registration_id, a.health_facility_item_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $situation = $dt_post['situation'];
        $background = $dt_post['background'];
        $assessment = $dt_post['assessment'];
        $recommendation = $dt_post['recommendation'];
        $advice = $dt_post['advice'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_sbar = array(
            'registration_destination_id'   => $registration_destination_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'situation'                     => $situation,
            'background'                    => $background,
            'assessment'                    => $assessment,
            'recommendation'                => $recommendation,
            'advice'                        => $advice,
            'user_added'                    => $user_id,
            'user_updated'                  => $user_id,
            'status'                        => 1,
            'date_added'                    => date('Y-m-d H:i:s', time()),
            'date_updated'                  => date('Y-m-d H:i:s', time()),
        );
    
        $sbar_id = $this->$model->create($dt_sbar);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
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

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $sbar_id = $id;
    
        $select = "a.id";
        $sbar = $this->$model->select($sbar_id, $select);
    
        if (count($sbar) == 0) {
            $this->REST_Return(404, 'sbar with id ' . $sbar_id . ' does not exist!');
            return;
        }

        $situation = $dt_put['situation'];
        $background = $dt_put['background'];
        $assessment = $dt_put['assessment'];
        $recommendation = $dt_put['recommendation'];
        $advice = $dt_put['advice'];
    
        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_sbar = array(
            'situation'         => $situation,
            'background'        => $background,
            'assessment'        => $assessment,
            'recommendation'    => $recommendation,
            'advice'            => $advice,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s', time()),
        );
    
        $id = $this->$model->update($sbar_id, $dt_sbar);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function validate_put($p = 0) {
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
        $this->form_validation->set_rules($this->validate_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $sbar_id = $id;
    
        $select = "a.id, a.validation_status, a.doctor_in_charge_id_sbar, p.name as patient_name, r.norm, h.name as patient_alias, e.fullname as user_added_name";
        $where = array();
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
            array('user u', 'a.user_added = u.id', 'INNER'),
            array('employee e', 'u.employee_id = e.id', 'INNER'),
        );
        $sbar = $this->$model->select($sbar_id, $select, $where, $join);    
        if (count($sbar) == 0) {
            $this->REST_Return(404, 'sbar with id ' . $sbar_id . ' does not exist!');
            return;
        }
        
        $norm = $sbar[0]->norm;
        $patient_name = $sbar[0]->patient_name;
        $patient_alias = $sbar[0]->patient_alias;

        $doctor_in_charge_id_sbar = $sbar[0]->doctor_in_charge_id_sbar;
        $user_added_name = $sbar[0]->user_added_name;

        if ($doctor_in_charge_id_sbar == null) {
            $this->REST_Return('this sbar does not have doctor_in_charge_id_sbar');
            return;
        }

        $select = 'a.id';
        $where = array(
            'a.employee_id' => $doctor_in_charge_id_sbar,
        );
        $user = $this->User->select(0, $select, $where);

        $user_doctor_id = $user[0]->id;

        $validation_status = $sbar[0]->validation_status;

        if ($validation_status == 1) {
            $this->REST_Return(409, 'this sbar has been validated');
            return;
        }

        $dt_sbar = array(
            'validation_status' => 1,
            'validation_time' => date('Y-m-d H:i:s', time()),
            'date_updated' => date('Y-m-d H:i:s', time()),
            'user_updated' => $user_id,
        );

        /* Start Transaction */
        $this->db->trans_start();
    
        $this->$model->update_v2($sbar_id, $dt_sbar);

        $title = 'SBAR';
        $message = "Pasien $patient_alias $patient_name ($norm), Dikirim oleh $user_added_name. Mohon verifikasi SBAR ini";
        $this->Notifications->createNotification($user_doctor_id, null, $title, $message, 'sbar', ['sbar_id' => $sbar_id]);

        $select = 'a.id, a.is_suggestion, a.suggestion_status';
        $where = array(
            'a.sbar_id' => $sbar_id,
        );
        $kardex = $this->Kardex->select(0, $select, $where);
        if (count($kardex) > 0) {
            $kardex_id = $kardex[0]->id;
            $is_suggestion = $kardex[0]->is_suggestion;
            $suggestion_status = $kardex[0]->suggestion_status;

            if ($is_suggestion == 1 && $suggestion_status == 1) {
                $dt_kardex = array(
                    'suggestion_status' => 2,
                    'is_approve' => 1,
                );
                
                $this->Kardex->update_v2($kardex_id, $dt_kardex);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to verify sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function verify_put($p = 0) {
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
        $this->form_validation->set_rules($this->verify_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $sbar_id = $id;
    
        $select = "a.id, a.verification_status";
        $sbar = $this->$model->select($sbar_id, $select);    
        if (count($sbar) == 0) {
            $this->REST_Return(404, 'sbar with id ' . $sbar_id . ' does not exist!');
            return;
        }

        $verification_status = $sbar[0]->verification_status;

        if ($verification_status == 1) {
            $this->REST_Return(409, 'this sbar has been verfied');
            return;
        }

        $dt_sbar = array(
            'verification_status' => 1,
            'verification_time' => date('Y-m-d H:i:s', time()),
            'date_updated' => date('Y-m-d H:i:s', time()),
            'user_updated' => $user_id,
        );

        /* Start Transaction */
        $this->db->trans_start();
    
        $this->$model->update_v2($sbar_id, $dt_sbar);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to verify sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function verify_all_put($p = 0) {
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
        $this->form_validation->set_rules($this->verify_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $registration_destination_id = $id;
    
        $select = "a.id";
        $where = array('registration_destination_id' => $registration_destination_id);
        $sbar = $this->$model->select(0, $select, $where);
    
        if (count($sbar) == 0) {
            $this->REST_Return(404, 'sbar with registration_destination_id ' . $registration_destination_id . ' does not exist!');
            return;
        }
        
        /* Start Transaction */
        $this->db->trans_start();

        $status = $dt_put['status'];

        for ($i=0; $i < count($sbar); $i++) { 
            $sbar_id = $sbar[$i]->id;
            $dt_sbar = array(
                'status'        => $status,
                'date_updated'  => date('Y-m-d H:i:s', time()),
                'user_updated'  => $user_id,
            );

            $this->$model->update($sbar_id, $dt_sbar);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to verify all sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function decline_put($p = 0) {
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
        $this->form_validation->set_rules($this->decline_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $sbar_id = $id;
    
        $select = "a.id, a.verification_status";
        $sbar = $this->$model->select($sbar_id, $select);    
        if (count($sbar) == 0) {
            $this->REST_Return(404, 'sbar with id ' . $sbar_id . ' does not exist!');
            return;
        }

        // $verification_status = $sbar[0]->verification_status;

        // if ($verification_status == 1) {
        //     $this->REST_Return(409, 'this sbar has been verfied');
        //     return;
        // }

        $decline_reason = $dt_put['decline_reason'];

        $dt_sbar = array(
            'decline_reason' => $decline_reason,
            'date_updated' => date('Y-m-d H:i:s', time()),
            'user_updated' => $user_id,
        );

        /* Start Transaction */
        $this->db->trans_start();
    
        $this->$model->update_v2($sbar_id, $dt_sbar);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to verify sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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
    
        $sbar_id = $this->$model->select_id($id);
    
        if ($sbar_id == "") {
            $this->REST_Return(404, 'there is no sbar with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_sbar = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($sbar_id, $dt_sbar);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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

    public function save_verification_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_verification_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $doctor_in_charge_id_sbar = $dt_put['doctor_in_charge_id_sbar'];

        $select = 'a.id, r.norm';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $select = 'a.id';
        $employee2 = $this->Employee->select($doctor_in_charge_id_sbar, $select);
        if (count($employee2) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_sbar);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'doctor_in_charge_id_sbar' => $doctor_in_charge_id_sbar,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $consultation_via_type_id = $dt_put['consultation_via_type_id'];
        $consultation_via_anything_else = $dt_put['consultation_via_anything_else'];

        $dt_sbar = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'doctor_in_charge_id_sbar' => $doctor_in_charge_id_sbar,
            'consultation_via_type_id' => $consultation_via_type_id,
            'consultation_via_anything_else' => $consultation_via_anything_else,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save verification', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_sbar_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_sbar_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];

        $select = 'a.id, r.norm';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $sbar = $dt_put['sbar'];
        $advice = $dt_put['advice'];

        $dt_sbar = array(
            'sbar'         => $sbar,
            'advice'       => $advice,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save sbar', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_laboratory_request_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_laboratory_request_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $clinical_indications = $dt_put['clinical_indications'];

        $select = 'a.id, r.norm';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $dt_sbar = array(
            'user_updated'   => $user_id,
            'date_updated'   => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // LABORATORY_REQUEST
        if (isset($dt_put['laboratory_request_items'])) {
            $laboratory_request_items_arr = $dt_put['laboratory_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.sbar_id' => $sbar_id,
                'a.is_delete' => 0,
            );
            $laboratory_request = $this->Laboratory_request->select(0, $select, $where);    
            $laboratory_request_id = "";
            if (count($laboratory_request) == 0 && count($laboratory_request_items_arr) > 0) {
                // count no_request & queue
                $prefix = 'LAB';
                $epoch = time();
                $dt_queue = array(
                    'var_2' => date('Y', $epoch),
                    'var_3' => date('m', $epoch),
                    'var_4' => date('d', $epoch),
                    'var_5' => date('Y-m-d', $epoch),
                );

                $no_request = counter_queue($prefix, $dt_queue, 4);
                $queue = round((int)substr($no_request, -4));

                $dt_laboratory_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $soap_id,
                    'sbar_id'               => $sbar_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'clinical_indications'  => $clinical_indications,
                    'registration_destination_id' => $registration_destination_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'date_request'          => date('Y-m-d H:i:s',time())
                );
                $laboratory_request_id = $this->Laboratory_request->create($dt_laboratory_request);
                $laboratory_request = $this->Laboratory_request->select(0, $select, $where); // UNTUK AMBIL STATUS
            } else if (count($laboratory_request) > 0 && count($laboratory_request_items_arr) == 0) {
                $laboratory_request_id = $laboratory_request[0]->id;
                $dt_laboratory_request_delete = array(
                    'is_delete' => 1,
                );
                // delete parent
                $st_laboratory_request = $this->Laboratory_request->update_v2($laboratory_request_id, $dt_laboratory_request_delete);
                if ($st_laboratory_request < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            } else if (count($laboratory_request) > 0) {
                $laboratory_request_id = $laboratory_request[0]->id;
            }

            if ($laboratory_request_id != "") {
                $status = $laboratory_request[0]->status;
                $no_request = $laboratory_request[0]->no_request;
                if ($status != 1) {
                    $this->REST_Return(409, 'Order laboratorium dengan nomor ' . $no_request  . ' telah diproses');
                    return;
                }

                $dt_laboratory_request_items_delete = array(
                    'is_delete' => 1,
                );
                $where_delete = array(
                    'laboratory_request_id' => $laboratory_request_id,
                    'is_delete' => 0,
                );
                $st_laboratory_request_items = $this->Laboratory_request_item->update_v2(0, $dt_laboratory_request_items_delete, $where_delete);
                if ($st_laboratory_request_items < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }

                for ($i=0; $i < count($laboratory_request_items_arr); $i++) { 
                    $laboratory_package_id = $laboratory_request_items_arr[$i]['laboratory_package_id'];
                    $service_class_id = $laboratory_request_items_arr[$i]['service_class_id'];

                    $select = 'a.id, a.costing_id';
                    $laboratory_package = $this->Laboratory_package->select($laboratory_package_id, $select);
                    if (count($laboratory_package_id) == 0) {
                        $this->REST_Return(404, 'there is no laboratory_package with id ' . $laboratory_package_id);
                        return;
                    }

                    $select = 'a.id';
                    $service_class = $this->Service_class->select($service_class_id, $select);
                    if (count($service_class_id) == 0) {
                        $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
                        return;
                    }

                    $costing_id = $laboratory_package[0]->costing_id;

                    $select = 'a.id, a.name';
                    $costing = $this->Costing->select($costing_id, $select);

                    $costing_name = $costing[0]->name;

                    $select = 'a.id, a.var_1, a.var_2, a.var_3';
                    $where = array(
                        'a.costing_id' => $costing_id,
                    );
                    $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

                    $var_1 = $costing_services_class[0]->var_1;
                    $var_2 = $costing_services_class[0]->var_2;
                    $var_3 = $costing_services_class[0]->var_3;

                    $dt_laboratory_request_item = array(
                        'laboratory_request_id'     => $laboratory_request_id,
                        'laboratory_package_id'     => $laboratory_package_id,
                        'service_class_id'          => $service_class_id,
                        'var_1'                     => $var_1,
                        'var_2'                     => $var_2,
                        'var_3'                     => $var_3,
                        'costing_id'                => $costing_id,
                        'costing_name'              => $costing_name,
                        'date_request'              => date('Y-m-d H:i:s'),
                        'is_delete'                 => 0
                    );

                    $select = 'a.id';
                    $where = array(
                        'a.laboratory_request_id' => $laboratory_request_id,
                        'a.laboratory_package_id' => $laboratory_package_id,
                    );
                    $laboratory_request_item = $this->Laboratory_request_item->select(0, $select, $where);
                    if (count($laboratory_request_item) == 0) {
                        $this->Laboratory_request_item->create($dt_laboratory_request_item);
                    } else {
                        $laboratory_request_item_id = $laboratory_request_item[0]->id;
                        unset($dt_laboratory_request_item['date_request']);
                        $where_update = array(
                            'id' => $laboratory_request_item_id,
                        );
                        $st_laboratory_request_item = $this->Laboratory_request_item->update_v2(0, $dt_laboratory_request_item, $where_update);
                        if ($st_laboratory_request_item < 0) {
                            $this->REST_Return(422, 'something when wrong');
                            return;
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save laboratory request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_radiology_request_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_radiology_request_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);
        $user_employee_id = $user[0]->employee_id;
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $clinical_indications = $dt_put['clinical_indications'];

        $select = 'a.id, r.norm, a.health_facility_item_id';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );

        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $dt_sbar = array(
            'user_updated'   => $user_id,
            'date_updated'   => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }
        
        // RADIOLOGY_REQUEST
        if (isset($dt_put['radiology_request_items'])) {
            $radiology_request_items_arr = $dt_put['radiology_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.sbar_id' => $sbar_id,
                'a.is_delete' => 0,
            );
            $radiology_request = $this->Radiology_request->select(0, $select, $where);    
            $radiology_request_id = "";
            if (count($radiology_request) == 0 && count($radiology_request_items_arr) > 0) {
                // count no_request & queue
                $prefix = 'RAD';
                $epoch = time();
                $dt_queue = array(
                    'var_2' => date('Y', $epoch),
                    'var_3' => date('m', $epoch),
                    'var_4' => date('d', $epoch),
                    'var_5' => date('Y-m-d', $epoch),
                );

                $no_request = counter_queue($prefix, $dt_queue, 4);
                $queue = round((int)substr($no_request, -4));

                $dt_radiology_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $soap_id,
                    'sbar_id'               => $sbar_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'registration_destination_id' => $registration_destination_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'clinical_indications'  => $clinical_indications,
                    'date_request'          => date('Y-m-d H:i:s',time())
                );
                $radiology_request_id = $this->Radiology_request->create($dt_radiology_request);
                $radiology_request = $this->Radiology_request->select(0, $select, $where); // UNTUK AMBIL STATUS
            } else if (count($radiology_request) > 0 && count($radiology_request_items_arr) == 0) {
                $radiology_request_id = $radiology_request[0]->id;
                $dt_radiology_request_delete = array(
                    'is_delete' => 1,
                );
                // delete parent
                $st_radiology_request = $this->Radiology_request->update_v2($radiology_request_id, $dt_radiology_request_delete);
                if ($st_radiology_request < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            } else if (count($radiology_request) > 0) {
                $radiology_request_id = $radiology_request[0]->id;
            }

            if ($radiology_request_id != "") {
                $status = $radiology_request[0]->status;
                $no_request = $radiology_request[0]->no_request;
                if ($status != 1) {
                    $this->REST_Return(409, 'Order radiologi dengan nomor ' . $no_request  . ' telah diproses');
                    return;
                }

                // Update clinical_indications
                $dt_radiology_request = array(
                    'clinical_indications'  => $clinical_indications,
                );
                $this->Radiology_request->update_v2($radiology_request_id, $dt_radiology_request);

                $dt_radiology_request_items_delete = array(
                    'is_delete' => 1,
                );
                $where_delete = array(
                    'radiology_request_id' => $radiology_request_id,
                    'is_delete' => 0,
                );
                $st_radiology_request_items = $this->Radiology_request_item->update_v2(0, $dt_radiology_request_items_delete, $where_delete);
                if ($st_radiology_request_items < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }

                for ($i=0; $i < count($radiology_request_items_arr); $i++) { 
                    $radiology_package_id = $radiology_request_items_arr[$i]['radiology_package_id'];
                    $service_class_id = $radiology_request_items_arr[$i]['service_class_id'];

                    $select = 'a.id, a.costing_id';
                    $radiology_package = $this->Radiology_package->select($radiology_package_id, $select);
                    if (count($radiology_package) == 0) {
                        $this->REST_Return(404, 'there is no radiology_package with id ' . $radiology_package_id);
                        return;
                    }

                    $select = 'a.id';
                    $service_class = $this->Service_class->select($service_class_id, $select);
                    if (count($service_class) == 0) {
                        $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
                        return;
                    }

                    $costing_id = $radiology_package[0]->costing_id;

                    $select = 'a.id, a.name';
                    $costing = $this->Costing->select($costing_id, $select);

                    $costing_name = $costing[0]->name;

                    $select = 'a.id, a.var_1, a.var_2, a.var_3';
                    $where = array(
                        'a.costing_id' => $costing_id,
                    );
                    $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

                    $var_1 = $costing_services_class[0]->var_1;
                    $var_2 = $costing_services_class[0]->var_2;
                    $var_3 = $costing_services_class[0]->var_3;

                    $dt_radiology_request_item = array(
                        'radiology_request_id'     => $radiology_request_id,
                        'radiology_package_id'     => $radiology_package_id,
                        'service_class_id'          => $service_class_id,
                        'var_1'                     => $var_1,
                        'var_2'                     => $var_2,
                        'var_3'                     => $var_3,
                        'costing_id'                => $costing_id,
                        'costing_name'              => $costing_name,
                        'date_request'              => date('Y-m-d H:i:s'),
                        'is_delete'                 => 0
                    );

                    $select = 'a.id';
                    $where = array(
                        'a.radiology_request_id' => $radiology_request_id,
                        'a.radiology_package_id' => $radiology_package_id,
                    );
                    $radiology_request_item = $this->Radiology_request_item->select(0, $select, $where);
                    if (count($radiology_request_item) == 0) {
                        $this->Radiology_request_item->create($dt_radiology_request_item);
                    } else {
                        $radiology_request_item_id = $radiology_request_item[0]->id;
                        unset($dt_radiology_request_item['date_request']);
                        $where_update = array(
                            'id' => $radiology_request_item_id,
                        );
                        $st_radiology_request_item = $this->Radiology_request_item->update_v2(0, $dt_radiology_request_item, $where_update);
                        if ($st_radiology_request_item < 0) {
                            $this->REST_Return(422, 'something when wrong');
                            return;
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save radiology request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_physiotherapy_request_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_physiotherapy_request_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);
        $user_employee_id = $user[0]->employee_id;
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];

        $select = 'a.id, r.norm, a.health_facility_item_id';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );

        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $dt_sbar = array(
            'user_updated'   => $user_id,
            'date_updated'   => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // PHYSIOTHERAPY_REQUEST
        if (isset($dt_put['physiotherapy_request_items'])) {
            $physiotherapy_request_items_arr = $dt_put['physiotherapy_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.sbar_id' => $sbar_id,
                'a.is_delete' => 0,
            );
            $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);    
            $physiotherapy_request_id = "";
            if (count($physiotherapy_request) == 0 && count($physiotherapy_request_items_arr) > 0) {
                // count no_request & queue
                $prefix = 'RHM';
                $epoch = time();
                $dt_queue = array(
                    'var_2' => date('Y', $epoch),
                    'var_3' => date('m', $epoch),
                    'var_4' => date('d', $epoch),
                    'var_5' => date('Y-m-d', $epoch),
                );

                $no_request = counter_queue($prefix, $dt_queue, 4);
                $queue = round((int)substr($no_request, -4));

                $dt_physiotherapy_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $soap_id,
                    'sbar_id'               => $sbar_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'date_request'          => date('Y-m-d H:i:s',time())
                );
                $physiotherapy_request_id = $this->Physiotherapy_request->create($dt_physiotherapy_request);
                $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);    
            } else if (count($physiotherapy_request) > 0 && count($physiotherapy_request_items_arr) == 0) {
                $physiotherapy_request_id = $physiotherapy_request[0]->id;
                $dt_physiotherapy_request_delete = array(
                    'is_delete' => 1,
                );
                // delete parent
                $st_physiotherapy_request = $this->Physiotherapy_request->update_v2($physiotherapy_request_id, $dt_physiotherapy_request_delete);
                if ($st_physiotherapy_request < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            } else if (count($physiotherapy_request) > 0) {
                $physiotherapy_request_id = $physiotherapy_request[0]->id;
            }

            if ($physiotherapy_request_id != "") {
                $status = $physiotherapy_request[0]->status;
                $no_request = $physiotherapy_request[0]->no_request;
                if ($status != 1) {
                    $this->REST_Return(409, 'Order fisioterapi dengan nomor ' . $no_request  . ' telah diproses');
                    return;
                }

                $dt_physiotherapy_request_items_delete = array(
                    'is_delete' => 1,
                );
                $where_delete = array(
                    'physiotherapy_request_id' => $physiotherapy_request_id,
                    'is_delete' => 0,
                );
                $st_physiotherapy_request_items = $this->Physiotherapy_request_item->update_v2(0, $dt_physiotherapy_request_items_delete, $where_delete);
                if ($st_physiotherapy_request_items < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }

                for ($i=0; $i < count($physiotherapy_request_items_arr); $i++) { 
                    $physiotherapy_package_id = $physiotherapy_request_items_arr[$i]['physiotherapy_package_id'];
                    $service_class_id = $physiotherapy_request_items_arr[$i]['service_class_id'];

                    $select = 'a.id, a.costing_id';
                    $physiotherapy_package = $this->Physiotherapy_package->select($physiotherapy_package_id, $select);
                    if (count($physiotherapy_package) == 0) {
                        $this->REST_Return(404, 'there is no physiotherapy_package with id ' . $physiotherapy_package_id);
                        return;
                    }

                    $select = 'a.id';
                    $service_class = $this->Service_class->select($service_class_id, $select);
                    if (count($service_class) == 0) {
                        $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
                        return;
                    }

                    $costing_id = $physiotherapy_package[0]->costing_id;

                    $select = 'a.id, a.name';
                    $costing = $this->Costing->select($costing_id, $select);

                    $costing_name = $costing[0]->name;

                    $select = 'a.id, a.var_1, a.var_2, a.var_3';
                    $where = array(
                        'a.costing_id' => $costing_id,
                    );
                    $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

                    $var_1 = $costing_services_class[0]->var_1;
                    $var_2 = $costing_services_class[0]->var_2;
                    $var_3 = $costing_services_class[0]->var_3;

                    $dt_physiotherapy_request_item = array(
                        'physiotherapy_request_id'     => $physiotherapy_request_id,
                        'physiotherapy_package_id'     => $physiotherapy_package_id,
                        'service_class_id'          => $service_class_id,
                        'var_1'                     => $var_1,
                        'var_2'                     => $var_2,
                        'var_3'                     => $var_3,
                        'costing_id'                => $costing_id,
                        'costing_name'              => $costing_name,
                        'date_request'              => date('Y-m-d H:i:s'),
                        'is_delete'                 => 0
                    );

                    $select = 'a.id';
                    $where = array(
                        'a.physiotherapy_request_id' => $physiotherapy_request_id,
                        'a.physiotherapy_package_id' => $physiotherapy_package_id,
                    );
                    $physiotherapy_request_item = $this->Physiotherapy_request_item->select(0, $select, $where);
                    if (count($physiotherapy_request_item) == 0) {
                        $this->Physiotherapy_request_item->create($dt_physiotherapy_request_item);
                    } else {
                        $physiotherapy_request_item_id = $physiotherapy_request_item[0]->id;
                        unset($dt_physiotherapy_request_item['date_request']);
                        $where_update = array(
                            'id' => $physiotherapy_request_item_id,
                        );
                        $st_physiotherapy_request_item = $this->Physiotherapy_request_item->update_v2(0, $dt_physiotherapy_request_item, $where_update);
                        if ($st_physiotherapy_request_item < 0) {
                            $this->REST_Return(422, 'something when wrong');
                            return;
                        }
                    }
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save physiotherapy request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_prescription_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_prescription_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];

        $select = 'a.id, r.norm, a.health_facility_item_id';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $norm = $registration_destination[0]->norm;
        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $dt_sbar = array(
            'user_updated'   => $user_id,
            'date_updated'   => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // SOAP_P
        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'sbar_id'               => $sbar_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'sbar_id' => $sbar_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
        );
        $soap_p = $this->Soap_p->select(0, $select, $where);
        if (count($soap_p) == 0) {
            $soap_p_id = $this->Soap_p->create($dt_soap_p);
        } else {
            $soap_p_id = $soap_p[0]->id;
            $st_soap_p = $this->Soap_p->update_v2($soap_p_id, $dt_soap_p);
            if ($st_soap_p < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // PRESCRIPTION
        if (isset($dt_put['form_prescription'])) {
            $pharmacy_depo_id = $dt_put['pharmacy_depo_id'];
            $form_prescription = $dt_put['form_prescription'];
            $form_prescription_parent = $dt_put['form_prescription_parent'];
            $type_prescription_id = $dt_put['type_prescription_id'];

            $select = 'a.id';
            $depo = $this->Depo->select($pharmacy_depo_id);
            if (count($depo) == 0) {
                $this->REST_Return(404, 'there is no depo with id ' . $pharmacy_depo_id);
                return;
            }

            $prescription = $this->db->query("
                SELECT pc.id, pc.flag, pc.no_order
                FROM pharmacy_prescription pc
                WHERE pc.soap_p_id = '$soap_p_id'
                AND pc.sbar_id = '$sbar_id'
                AND pc.is_delete = 0
            ")->result();
            $prescription_id = '';
            if (count($prescription) == 0 && count($form_prescription) > 0) {
                // count no_order & queue
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
                    'norm'                      => $norm,
                    'institute_branch_id'       => $institute_branch_id,
                    'pharmacy_depo_id'          => $pharmacy_depo_id == '' || $pharmacy_depo_id == null ? '297c44f0-12ef-40e6-b9a7-c831065b3372' : $pharmacy_depo_id,
                    'type_prescription_id'      => ($type_prescription_id == null) ? 1 : $type_prescription_id,
                    'soap_p_id'                 => $soap_p_id,
                    'sbar_id'                   => $sbar_id,
                    'health_facility_item_id'   => $health_facility_item_id,
                    'doctor_in_charge_id'       => $doctor_in_charge_id,
                    'registration_destination_id' => $registration_destination_id,
                    'no_order'                  => $no_order,
                    'date_added'                => date("Y-m-d H:i:s",time()),
                    'date_0'                    => date("Y-m-d H:i:s",time()),
                    'flag'                      => 0,
                    'queue'                     => $queue,
                );
                $prescription_id = $this->Prescription->create($dt_prescription);
            } else if (count($prescription) > 0 && count($form_prescription) == 0) {
                $prescription_id = $prescription[0]->id;
                $dt_prescription_delete = array(
                    'is_delete' => 1,
                );
                // delete parent
                $st_prescription = $this->Prescription->update_v2($prescription_id, $dt_prescription_delete);
                if ($st_prescription < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            } else if (count($prescription) > 0) {
                $prescription_id = $prescription[0]->id;
                $flag = $prescription[0]->flag;
                $no_order = $prescription[0]->no_order;

                if ($flag != 0) {
                    $this->REST_Return(409, 'Order resep dengan nomor ' . $no_order  . ' telah diproses');
                    return;
                }
            }
            
            if ($prescription_id != '') {
                $dt_prescription_update_type_prescription = array(
                    'type_prescription_id' => ($type_prescription_id == null) ? 1 : $type_prescription_id,
                );
                // update parent
                $st_prescription = $this->Prescription->update_v2($prescription_id, $dt_prescription_update_type_prescription);
                if ($st_prescription < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
                
                $dt_prescription_item_delete = array(
                    'is_delete' => 1,
                );
                $where_delete = array(
                    'pharmacy_prescription_id' => $prescription_id
                );
                $st_delete = $this->Prescription_item->update_v2(0, $dt_prescription_item_delete, $where_delete);
                if ($st_delete < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }

                for ($i=0; $i < count($form_prescription); $i++) { 
                    $pharmacy_catalog_id = $form_prescription[$i]['pharmacy_catalog_id'];
                    $package_id = $form_prescription[$i]['package_id'];

                    if ($pharmacy_catalog_id == null) {
                        $this->REST_Return(400, 'pharmacy_catalog_id is required');
                        return;
                    }

                    if ($package_id == null) {
                        $this->REST_Return(400, 'package_id is required');
                        return;
                    }

                    $select = 'a.id';
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

                    $package_name = $package[0]->name;

                    $dose = $form_prescription[$i]['dose'];
                    $prescription_type_id = $form_prescription[$i]['prescription_type_id'];
                    $value_package = $form_prescription[$i]['value_package'];
                    $used = $form_prescription[$i]['used'];
                    $desc = $form_prescription[$i]['desc'];
                    $sort = $form_prescription[$i]['sort'];
                    
                    for ($j=0; $j < count($form_prescription_parent); $j++) { 
                        $id_parent = $form_prescription_parent[$j]['id'];
                        if ($id_parent == $prescription_type_id) {
                            $prescription_type_used = $form_prescription_parent[$j]['used'];
                            $prescription_type_value = $form_prescription_parent[$j]['value'];
                            $prescription_type_package = $form_prescription_parent[$j]['package'];
                        }
                    }

                    $dt_prescription_item = array(
                        'pharmacy_prescription_id'      => $prescription_id,
                        'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                        'package_id'                    => $package_id,
                        'package_name'                  => $package_name,
                        'dose'                          => $dose,
                        'prescription_item_type_id'     => $prescription_type_id,
                        'qty'                           => $value_package,
                        'used'                          => $used,
                        'desc'                          => $desc,
                        'sort'                          => $sort,
                        'prescription_item_type_used'   => $prescription_type_used,
                        'prescription_item_type_value'  => $prescription_type_value,
                        'prescription_item_type_package'=> $prescription_type_package,
                        'is_delete'                     => 0
                    );

                    $this->Prescription_item->replace($dt_prescription_item);
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save prescription', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_full_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_sbar_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $doctor_in_charge_id_sbar = $dt_put['doctor_in_charge_id_sbar'];

        $select = 'a.id, r.norm';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $select = 'a.id';
        $employee2 = $this->Employee->select($doctor_in_charge_id_sbar, $select);
        if (count($employee2) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_sbar);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
            'a.is_initial_inpatient' => 0,
        );
        $soap = $this->Soap->select(0, $select, $where);
        if (count($soap) == 0) {
            $soap_id = $this->Soap->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
        }

        // SBAR
        $dt_sbar = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'doctor_in_charge_id_sbar' => $doctor_in_charge_id_sbar,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $sbar_id = $this->$model->create($dt_sbar);
        } else {
            $sbar_id = $id;

            $select = 'a.id';
            $sbar = $this->$model->select($sbar_id, $select);
            if (count($sbar) == 0) {
                $this->REST_Return(404, 'there is no sbar with id ' . $sbar_id);
                return;
            }
        }

        $consultation_via_type_id = $dt_put['consultation_via_type_id'];
        $consultation_via_anything_else = $dt_put['consultation_via_anything_else'];
        $sbar = $dt_put['sbar'];
        $advice = $dt_put['advice'];

        $dt_sbar = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'doctor_in_charge_id_sbar' => $doctor_in_charge_id_sbar,
            'consultation_via_type_id' => $consultation_via_type_id,
            'consultation_via_anything_else' => $consultation_via_anything_else,
            'sbar' => $sbar,
            'advice' => $advice,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $st_sbar = $this->$model->update_v2($sbar_id, $dt_sbar);
        if ($st_sbar < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sbar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save full', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $sbar_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}