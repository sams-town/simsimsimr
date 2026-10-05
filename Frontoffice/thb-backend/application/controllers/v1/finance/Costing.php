<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Costing extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_services_class_model", "{$this->name}_services_class");
        $this->load->model("$this->directory/{$this->name}_value_model", "{$this->name}_value");
        $this->load->model("$this->directory/{$this->name}_health_facility_item_model", "{$this->name}_health_facility_item");
        $this->load->model("v1/third_party/Company_model", "Company");
        $this->load->model("v1/finance/Costing_services_class_class_model", "Costing_services_class_class");
        $this->load->model("v1/health_facility/Laboratory_package_model", "Laboratory_package");
        $this->load->model("v1/health_facility/Radiology_package_model", "Radiology_package");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");

        $this->update_rules = array(
            array('field' => 'type_costing_id', 'label' => 'type_costing_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[150]'),
            array('field' => 'desc', 'label' => 'desc', 'rules' => 'trim|required|max_length[150]'),
            array('field' => 'is_ref', 'label' => 'is_ref', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'costing_service_class[]', 'label' => 'costing_service_class', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->create_v2_rules = array(
            array('field' => 'type_costing_id', 'label' => 'type_costing_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'classification_one_type_id', 'label' => 'classification_one_type_id', 'rules' => 'trim|max_length[2]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'is_single', 'label' => 'is_single', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[250]'),
            array('field' => 'var_1', 'label' => 'desc', 'rules' => 'trim|required|max_length[15]'),
        );
    }

    /**
     * GET Costing with API
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
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            for ($i=0; $i < count($output); $i++) {
                $this->Costing_services_class->whereFields['a.costing_id'] = $output[$i]->id;
                $output_csc = $this->Costing_services_class->read(0,false);
                $output[$i]->costing_csc = $output_csc;
                for ($isc=0; $isc < count($output[$i]->costing_csc); $isc++) {
                    $service_class = $this->Service_class->read($output[$i]->costing_csc[$isc]->service_class_id,false)[0];
                    $service_class = array(
                        'value'=> $service_class->id,
                        'title'=> $service_class->name
                    );
                    $output[$i]->costing_csc[$isc]->service_class = $service_class;
                    $company_insurance = $this->Company->read($output[$i]->costing_csc[$isc]->company_insurance_id,false)[0];
                    $company_insurance = array(
                        'value'=> $company_insurance->id,
                        'title'=> $company_insurance->name
                    );
                    $output[$i]->costing_csc[$isc]->company_insurance = $company_insurance;
                    $this->Costing_value->whereFields['a.costing_services_class_id'] = $output[$i]->costing_csc[$isc]->id;
                    $output_csc = $this->Costing_value->read(0,false);
                    $output[$i]->costing_csc[$isc]->costing_value = $output_csc;
                }
            }
            // for ($i=0; $i < count($output); $i++) {
            //     for ($isc=0; $isc < count($output[$i]->costing_csc); $isc++) {
            //         for ($iv=0; $iv < count($output[$i]->costing_csc[$isc]->costing_value); $iv++) {
            //             $output[$i]->costing_value = array_merge($output[$i]->costing_csc[$isc]->costing_value);
            //         }
            //     }
            // }
            $response_data = array();
            if ($output != [] && $id != null) {
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
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    
        $this->$model->whereFields = $q_strings;
        // $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
        // $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id); 

        for ($i=0; $i < count($output->rows); $i++) { 
            $costing_id = $output->rows[$i]->id;
            
            $select = 'a.*';
            $where = array('costing_id' => $costing_id);
            $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

            $output->rows[$i]->health_facility_item_id = $costing_services_class[0]->health_facility_item_id;
            $output->rows[$i]->service_class_id = $costing_services_class[0]->service_class_id;
            $output->rows[$i]->company_insurance_id = $costing_services_class[0]->company_insurance_id;
            $output->rows[$i]->bride_code = $costing_services_class[0]->bride_code;
            $output->rows[$i]->bride_satuan = $costing_services_class[0]->bride_satuan;
            $output->rows[$i]->var_1 = $costing_services_class[0]->var_1;
            $output->rows[$i]->var_2 = $costing_services_class[0]->var_2;
            $output->rows[$i]->var_3 = $costing_services_class[0]->var_3;
            $output->rows[$i]->persediaan = $costing_services_class[0]->persediaan;
            $output->rows[$i]->penjualan = $costing_services_class[0]->penjualan;
            $output->rows[$i]->disc_penjualan = $costing_services_class[0]->disc_penjualan;
            $output->rows[$i]->bride_department = $costing_services_class[0]->bride_department;
            $output->rows[$i]->bride_department_sub = $costing_services_class[0]->bride_department_sub;
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function service_class_get($p = 0) {
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
        
        $where = array('a.is_delete' => 0);

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;
        if ($health_facility_item_id != null && $health_facility_item_id != 'd158a64e-6246-444a-97b7-8d0a2d90e53a') {
            $where['a.health_facility_item_id'] = $health_facility_item_id;
        }

        $type_costing_id = $q_strings['type_costing_id'] ?? null;
        if ($type_costing_id != null) {
            $where['c.type_costing_id'] = $type_costing_id;
        }

        $classification_one_type_id = $q_strings['classification_one_type_id'] ?? null;
        if ($classification_one_type_id != null && $classification_one_type_id != 17) {
           $where['c.classification_one_type_id'] = $classification_one_type_id;
        }

        $service_class_id = $q_strings['service_class_id'] ?? null;
        if ($service_class_id != null) {
            $where['a.service_class_id'] = $service_class_id;
        }

        $company_insurance_id = $q_strings['company_insurance_id'] ?? null;
        if ($company_insurance_id != null) {
            if($company_insurance_id == 'bb0d633f-99e1-4990-901b-0417a14e3cb6' && $health_facility_item_id == null){
                $where['a.company_insurance_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';
            } else if($company_insurance_id == '8a322cf3-e135-4909-bc25-48e31adadb0e'){
                $where['a.company_insurance_id'] = $q_strings['company_insurance_id'] ?? null;
            } else if($company_insurance_id == '315fe232-e034-4b45-bf0d-e8cbb1f04c1a'){
                $where['a.company_insurance_id'] = $q_strings['company_insurance_id'] ?? null;
            } else if($company_insurance_id == 'bb0d633f-99e1-4990-901b-0417a14e3cb6' && $health_facility_item_id != null){
                $where['a.company_insurance_id'] = 'bb0d633f-99e1-4990-901b-0417a14e3cb6';
            } else if($company_insurance_id == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8' || $company_insurance_id == 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'){
                $company_id = $q_strings['company_insurance_id'] ?? null;
                if ($company_id != null) {
                    $where['a.company_insurance_id'] = $company_id;
                }
            } else {
                $where['a.company_insurance_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';
            }
            if ($health_facility_item_id != null) {
                if ($health_facility_item_id != 'd158a64e-6246-444a-97b7-8d0a2d90e53a' && $service_class_id == null) {
                    $where['a.service_class_id'] = 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4';
                }
            }
        }
    
        $select = 'a.id,
        a.id as costing_id,
        a.health_facility_item_id,
        a.service_class_id,
        a.company_insurance_id,
        a.bride_code,
        a.bride_satuan,
        a.var_1,
        a.var_2,
        a.var_3,
        a.var_4,
        a.var_5,
        a.is_delete,
        a.is_new,
        a.persediaan,
        a.penjualan,
        a.disc_penjualan,
        a.bride_department,
        a.bride_department_sub,
        c.name,
        c.is_ref,
        sc.name as service_class
        ';
        $join = array(
            array('service_class sc', 'a.service_class_id = sc.id', 'INNER'),
            array('costing c', 'a.costing_id = c.id', 'INNER'),
        );
        $order = array();
        $output = $this->Costing_services_class->select(0, $select, $where, $join, $order);
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Costing with API
     * -------------------------
     * @method: GET
     */

    public function service_class_backup_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            // $this->$model->whereFields = $q_strings;
            if (isset($q_strings['health_facility_item_id'])) {
                $this->Costing_services_class_class->whereFields['a.health_facility_item_id'] = $q_strings['health_facility_item_id'];
            }
            if (isset($q_strings['type_costing_id'])) {
                $this->Costing_services_class_class->whereFields['c.type_costing_id'] = $q_strings['type_costing_id'];
            }
            if (isset($q_strings['classification_one_type_id'])) {
                $this->Costing_services_class_class->whereFields['c.classification_one_type_id'] = $q_strings['classification_one_type_id'];
            }
            if (isset($q_strings['service_class_id'])) {
                $this->Costing_services_class_class->whereFields['a.service_class_id'] = $q_strings['service_class_id'];
            }
            if (isset($q_strings['company_insurance_id'])) {
                if($q_strings['company_insurance_id'] == 'bb0d633f-99e1-4990-901b-0417a14e3cb6' && $q_strings['health_facility_item_id'] == ''){
                    $this->Costing_services_class_class->whereFields['a.company_insurance_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';
                } else {
                    $this->Costing_services_class_class->whereFields['a.company_insurance_id'] = $q_strings['company_id'];
                }
            }
            $this->Costing_services_class_class->whereFields['c.is_delete'] = '0';
            $output = $this->Costing_services_class_class->read(0,false);
            $this->Costing_services_class_class->whereFields = array();
            $response_data = array();
            if ($output || $output == [] && $id == '0') {
                if (isset($q_strings['is_nurse'])) {
                } else {
                    for ($i=0; $i < count($output); $i++) { 
                        $this->Costing_services_class->whereFields = array();
                        if (isset($q_strings['type_costing_id'])) {
                            $this->Costing_services_class->whereFields['c.type_costing_id'] = $q_strings['type_costing_id'];
                        }
                        if (isset($q_strings['classification_one_type_id'])) {
                            $this->Costing_services_class->whereFields['c.classification_one_type_id'] = $q_strings['classification_one_type_id'];
                        }
                        if (isset($q_strings['company_id'])) {
                            $this->Costing_services_class->whereFields['a.company_insurance_id'] = $q_strings['company_id'];
                        }
                        if (isset($q_strings['service_class_id'])) {
                            $this->Costing_services_class->whereFields['a.service_class_id'] = $q_strings['service_class_id'];
                        }                        
                        if (isset($q_strings['company_insurance_id'])) {
                            if($q_strings['company_insurance_id'] == 'bb0d633f-99e1-4990-901b-0417a14e3cb6' && $q_strings['health_facility_item_id'] == ''){
                                $this->Costing_services_class->whereFields['a.company_insurance_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';
                            } else {
                                $this->Costing_services_class->whereFields['a.company_insurance_id'] = $q_strings['company_id'];
                            }
                        }
                        // if (isset($q_strings['health_facility_item_id'])) {
                        //     $this->Costing_services_class->whereFields['a.health_facility_item_id'] = $q_strings['health_facility_item_id'];
                        // }
                        $this->Costing_services_class->whereFields['a.costing_id'] = $output[$i]->costing_id;
                        $costing_services_class = $this->Costing_services_class->read(0,false);
                        $output[$i]->costing_id = $costing_services_class[0]->id;
                        $output[$i]->var_1 = $costing_services_class[0]->var_1;
                    }
                }
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
    
    public function service_class_class_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            // $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            if (isset($q_strings['type_costing_id'])) {
                $this->$model->whereFields['a.type_costing_id'] = $q_strings['type_costing_id'];
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output || $output == [] && $id == '0') {
                for ($i=0; $i < count($output); $i++) { 
                    if (isset($q_strings['company_id'])) {
                        $this->Costing_services_class->whereFields['a.company_insurance_id'] = $q_strings['company_id'];
                    }
                    if (isset($q_strings['service_class_id'])) {
                        $this->Costing_services_class->whereFields['a.service_class_id'] = $q_strings['service_class_id'];
                    }
                    if (isset($q_strings['health_facility_item_id'])) {
                        $this->Costing_services_class->whereFields['a.health_facility_item_id'] = $q_strings['health_facility_item_id'];
                    }
                    $this->Costing_services_class->whereFields['a.costing_id'] = $output[$i]->id;
                    $costing_services_class = $this->Costing_services_class->read(0,false);
                    if (@$costing_services_class[0]->var_1) {
                        $output[$i]->costing_id = $costing_services_class[0]->id;
                        $output[$i]->var_1 = $costing_services_class[0]->var_1;
                    }
                }
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

    public function service_class_list_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        $queryString = str_replace('+', '%2B', $_SERVER['QUERY_STRING']);
        parse_str($queryString, $q_strings);

        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if (isset($q_strings['name'])) {
                $this->$model->whereLike['a.name'] = $q_strings['name'];
                unset($q_strings['name']);
            }
            $where = $q_strings;
            $where['a.institute_branch_id'] = $institute_branch_id;
            if (isset($q_strings['type_costing_id'])) {
                $where['a.type_costing_id'] = $q_strings['type_costing_id'];
            }
            if (isset($q_strings['health_facility_item_id'])) {
                $where['csc.health_facility_item_id'] = $q_strings['health_facility_item_id'];
            }
            if (isset($q_strings['type_costing_id'])) {
                $where['a.type_costing_id'] = $q_strings['type_costing_id'];
            }
            if (isset($q_strings['company_insurance_id'])) {
                $where['csc.company_insurance_id'] = $q_strings['company_insurance_id'];
            }
            $where['a.is_delete'] = '0';
            
            $select = 'a.*,b.name as type_costing_name';
            $join = array(
                array('helper b', 'a.type_costing_id = b.code AND b.variable="type_costing"', 'LEFT'),
                array('costing_services_class csc', 'a.id = csc.costing_id', 'LEFT'),
            );
            $output = $this->Costing->select(0, $select, $where, $join);
            $response_data = array();
            if ($output || $output == [] && $id == '0') {
                for ($i=0; $i < count($output); $i++) { 
                    //    if (isset($q_strings['company_id'])) {
                    //        $this->Costing_services_class->whereFields['a.company_insurance_id'] = $q_strings['company_id'];
                    //    }
                    //    if (isset($q_strings['service_class_id'])) {
                    //        $this->Costing_services_class->whereFields['a.service_class_id'] = $q_strings['service_class_id'];
                    //    }
                    //    $this->Costing_services_class->whereFields['a.costing_id'] = $output[$i]->id;
                    //    $costing_services_class = $this->Costing_services_class->read(0,false);
                    //    if (@$costing_services_class[0]->var_1) {
                    //     //    $output[$i]->health_facility_item_name = $costing_services_class[0]->health_facility_item_name;
                    //     //    $output[$i]->service_class_name = $costing_services_class[0]->service_class_name;
                    //     //    $output[$i]->company_name = $costing_services_class[0]->company_name;
                    //        $output[$i]->costing_id = $costing_services_class[0]->id;
                    //        $output[$i]->var_1 = $costing_services_class[0]->var_1;
                    //    }
                    $select = 'a.*,c.name,hfi.name as health_facility_item_name,sc.name as service_class_name,co.name as company_name';
                    $where_csc = array('costing_id' => $output[$i]->id);
                    $costing_services_class = $this->Costing_services_class->select(0, $select, $where_csc);

                    $output[$i]->health_facility_item_id = $costing_services_class[0]->health_facility_item_id;
                    $output[$i]->service_class_id = $costing_services_class[0]->service_class_id;
                    $output[$i]->service_class_name = $costing_services_class[0]->service_class_name;
                    $output[$i]->company_insurance_id = $costing_services_class[0]->company_insurance_id;
                    $output[$i]->company_name = $costing_services_class[0]->company_name;
                    $output[$i]->health_facility_item_name = $costing_services_class[0]->health_facility_item_name;
                    $output[$i]->bride_code = $costing_services_class[0]->bride_code;
                    $output[$i]->bride_satuan = $costing_services_class[0]->bride_satuan;
                    $output[$i]->var_1 = $costing_services_class[0]->var_1;
                    $output[$i]->var_2 = $costing_services_class[0]->var_2;
                    $output[$i]->var_3 = $costing_services_class[0]->var_3;
                    $output[$i]->persediaan = $costing_services_class[0]->persediaan;
                    $output[$i]->penjualan = $costing_services_class[0]->penjualan;
                    $output[$i]->disc_penjualan = $costing_services_class[0]->disc_penjualan;
                    $output[$i]->bride_department = $costing_services_class[0]->bride_department;
                    $output[$i]->bride_department_sub = $costing_services_class[0]->bride_department_sub;
                }
                $response_data = $output;
                
                $this->Costing->limit = null;
                $this->Costing->offset = null;
                $select = 'count(*) as iTotal';
                $iTotal = $this->Costing->select(0, $select, $where, $join)[0]->iTotal;
                
                $response_data = (object) array(
                    'rows' => $output,
                    'start' => ($offset + $limit),
                    'iTotal' => $iTotal
                );
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
     * Costing API
     * --------------------
     * @param: type_costing_id
     * @param: code
     * @param: name
     * @param: costing_service_class
     * @param: costing_health_facility_item
     * --------------------
     * @method : POST
     * @link: version/finance/costing
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
            $this->form_validation->set_rules('type_costing_id', 'type_costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('desc', 'desc', 'trim|required');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('costing_service_class[]', 'costing_service_class', 'trim');
            $this->form_validation->set_rules('costing_value[]', 'costing_value', 'trim');
            $this->form_validation->set_rules('costing_health_facility_item[]', 'costing_health_facility_item', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();

                $type_costing_id = $dt_post['type_costing_id'];
                $name = $dt_post['name'];
                $desc = $dt_post['desc'];
                
                $dt_costing = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'type_costing_id'       => $type_costing_id,
                    'name'                  => $name,
                    'desc'                  => $desc,
                    // 'is_ref'                => $dt_post['is_ref'],
                );
                $id = $this->$model->create($dt_costing);
                
                // INIT_COSTING_SERVICE_CLASS
                if(isset($dt_post['costing_service_class'])) {
                    $costing_services_class = array();
                    for ($i=0; $i < count($dt_post['costing_service_class']) ; $i++) {
                        // Check service_class_id and company_id
                        if($this->Service_class->read($dt_post['costing_service_class'][$i]['service_class_id'], false) != [] && $this->Company->read($dt_post['costing_service_class'][$i]['company_insurance_id'], false) != []) {

                            $health_facility_item_id = $dt_post['costing_service_class'][$i]['health_facility_item_id'];
                            $health_facility_item = $this->Health_facility_item->select_id($health_facility_item_id);

                            if ($health_facility_item == "") {
                                $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
                                return;
                            }

                            $dt_costing_item = array(
                                'costing_id'                => $id,
                                'health_facility_item_id'   => $health_facility_item_id,
                                'service_class_id'          => $dt_post['costing_service_class'][$i]['service_class_id'],
                                'company_insurance_id'      => $dt_post['costing_service_class'][$i]['company_insurance_id'],
                                'bride_code'                => isset($dt_post['costing_service_class'][$i]['bride_code']) ? $dt_post['costing_service_class'][$i]['bride_code'] : '',
                                'bride_satuan'              => isset($dt_post['costing_service_class'][$i]['bride_satuan']) ? $dt_post['costing_service_class'][$i]['bride_satuan'] : '',
                                'var_1'                     => isset($dt_post['costing_service_class'][$i]['var_1']) ? $dt_post['costing_service_class'][$i]['var_1'] : '',
                                'var_2'                     => isset($dt_post['costing_service_class'][$i]['var_2']) ? $dt_post['costing_service_class'][$i]['var_2'] : '',
                                'var_3'                     => isset($dt_post['costing_service_class'][$i]['var_3']) ? $dt_post['costing_service_class'][$i]['var_3'] : '',
                                'persediaan'                => isset($dt_post['costing_service_class'][$i]['persediaan']) ? $dt_post['costing_service_class'][$i]['persediaan'] : '',
                                'penjualan'                 => isset($dt_post['costing_service_class'][$i]['penjualan']) ? $dt_post['costing_service_class'][$i]['penjualan'] : '',
                                'disc_penjualan'            => isset($dt_post['costing_service_class'][$i]['disc_penjualan']) ? $dt_post['costing_service_class'][$i]['disc_penjualan'] : '',
                                'bride_department'          => isset($dt_post['costing_service_class'][$i]['bride_department']) ? $dt_post['costing_service_class'][$i]['bride_department'] : '',
                                'bride_department_sub'      => isset($dt_post['costing_service_class'][$i]['bride_department_sub']) ? $dt_post['costing_service_class'][$i]['bride_department_sub'] : '',
                            );
                            $costing_services_class_id = $this->Costing_services_class->create($dt_costing_item);
                            $costing_services_class[$i] = (object) array(
                                'costing_services_class_id' => $costing_services_class_id,
                                'service_class_id'          => $dt_post['costing_service_class'][$i]['service_class_id'],
                                'company_insurance_id'      => $dt_post['costing_service_class'][$i]['company_insurance_id'],
                            );
                        } else {
                            $this->REST_Return(404, 'There is no service_class with id ' . $dt_post['costing_service_class'][$i]['service_class_id'] . ' or company_insurance_id');
                            return;
                        }
                    }
                }
                
                // INIT_LABORATORY_PACKAGE
                if ($type_costing_id == 4) {
                    $code = $dt_post['code'];
                    $category = $dt_post['category'];

                    $dt_laboratory_package = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'costing_id'            => $id,
                        'name'                  => $name,
                        'code'                  => $code,
                        'category'              => $category,
                    );

                    $this->Laboratory_package->create($dt_laboratory_package);
                }

                // INIT_RADIOLOGY_PACKAGE
                if ($type_costing_id == 5) {
                    $code = $dt_post['code'];
                    $category = $dt_post['category'];

                    $dt_radiology_package = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'costing_id'            => $id,
                        'name'                  => $name,
                        'code'                  => $code,
                        'category'              => $category,
                    );

                    $this->Radiology_package->create($dt_radiology_package);
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

    public function create_v2_post() {
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
        $this->form_validation->set_rules($this->create_v2_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $classification_one_type_id = $dt_post['classification_one_type_id'];
        $health_facility_item_id = $dt_post['health_facility_item_id'] ?? null;
        $service_class_id = $dt_post['service_class_id'] ?? null;
        $company_insurance_id = $dt_post['company_insurance_id'] ?? null;
        $type_costing_id = $dt_post['type_costing_id'] ?? null;
        $name = $dt_post['name'];
        $var_1 = $dt_post['var_1'];
        $is_single = $dt_post['is_single'];
        $is_margin = $dt_post['is_margin'];

        if ($health_facility_item_id != null) {
            $select = 'a.id';
            $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
            if (count($health_facility_item) == 0) {
                $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
                return;
            }
        }
        
        /* Start Transaction */
        $this->db->trans_start();

        $service_class = array(
            'Kelas 3' => [
                'id' => 'd168732a-fdef-456d-836c-750e003187e0',
                'percent' => $is_margin == 2 ? 90 : 100,
            ],
            'Kelas 2' => [
                'id' => 'f39fbb8f-082e-4c71-8802-92943b6e4683',
                'percent' => $is_margin == 2 ? 100 : 100,
            ],
            'Kelas 1' => [
                'id' => '8d3b8d25-4606-4db5-9ec1-59ec22514210',
                'percent' => $is_margin == 2 ? 110 : 100,
            ],
            'VIP' => [
                'id' => '52d76ebf-aa04-4308-a1a1-61e325333787',
                'percent' => $is_margin == 2 ? 120 : 100,
            ],
            'VVIP' => [
                'id' => '8b252126-de37-4a74-b303-2b9da5e447bf',
                'percent' => $is_margin == 2 ? 130 : 100,
            ],
            'President Suite' => [
                'id' => 'b1c28ddc-18bd-4e3d-aa94-7ededcc9d151',
                'percent' => $is_margin == 2 ? 140 : 100,
            ],
            'Intensive' => [
                'id' => '01a37f8e-2baf-4057-b8fb-7a825cb12e19',
                'percent' => $is_margin == 2 ? 150 : 100,
            ],
            'Isolasi' => [
                'id' => '7560e211-15ed-4113-bd9a-943a1130d306',
                'percent' => $is_margin == 2 ? 100 : 100,
            ],
        );


        $dt_costing = array(
            'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
            'type_costing_id' => $type_costing_id,
            'classification_one_type_id' => $classification_one_type_id,
            'classification_two_type_id' => null,
            'classification_three_type_id' => null,
            'name' => $name,
            'desc' => '',
            'is_new' => 1,
        );

        $dt_costing_services_class = array(
            'costing_id' => '',
            'health_facility_item_id' => $health_facility_item_id,
            'service_class_id' => '',
            'company_insurance_id' => '',
            'var_1' => '',
            'is_new' => 1,
        );

        $output = [];

        // if single tarif
        if ($is_single == 2) {
            $costing_id = $this->$model->create($dt_costing);
            $dt_costing_services_class = array(
                'costing_id' => $costing_id,
                'health_facility_item_id' => $health_facility_item_id,
                'service_class_id' => $service_class_id,
                'company_insurance_id' => $company_insurance_id,
                'var_1' => $var_1,
                'is_new' => 1,
            );
            $this->Costing_services_class->create($dt_costing_services_class);
            // INIT_LABORATORY_PACKAGE
            if ($type_costing_id == 4) {
                $code = $dt_post['code'];
                $category = $dt_post['category'];

                $dt_laboratory_package = array(
                    'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                    'costing_id'            => $costing_id,
                    'name'                  => $name,
                    'code'                  => $code,
                    'category'              => $category,
                    'is_new'                => 1,
                );

                $this->Laboratory_package->create($dt_laboratory_package);
            }

            // INIT_RADIOLOGY_PACKAGE
            if ($type_costing_id == 5) {
                $code = $dt_post['code'];
                $category = $dt_post['category'];
                $dt_radiology_package = array(
                    'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                    'costing_id'            => $costing_id,
                    'name'                  => $name,
                    'code'                  => $code,
                    'category'              => $category,
                    'is_new'                => 1,
                );

                $this->Radiology_package->create($dt_radiology_package);
            }
        } else {
    
            foreach ($service_class as $key => $val) {
                $dt_costing['desc'] = $name . ' - UMUM - ' . $key;
        
                $costing_id = $this->$model->create($dt_costing);
    
                array_push($output, $dt_costing);
    
                $dt_costing_services_class['costing_id'] = $costing_id;
                $dt_costing_services_class['service_class_id'] = $val['id'];
                $dt_costing_services_class['company_insurance_id'] = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa';
                $dt_costing_services_class['var_1'] = $var_1 * $val['percent'] / 100;
    
                $this->Costing_services_class->create($dt_costing_services_class);
    
                array_push($output, $dt_costing_services_class);
            }

            foreach ($service_class as $key => $val) {
                $dt_costing['desc'] = $name . ' - BPJS - ' . $key;
    
                $costing_id = $this->$model->create($dt_costing);
    
                array_push($output, $dt_costing);
    
                $dt_costing_services_class['costing_id'] = $costing_id;
                $dt_costing_services_class['service_class_id'] = $val['id'];
                $dt_costing_services_class['company_insurance_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8';
                $dt_costing_services_class['var_1'] = $var_1 * $val['percent'] / 100;
    
                $this->Costing_services_class->create($dt_costing_services_class);
    
                array_push($output, $dt_costing_services_class);
            }
    
            foreach ($service_class as $key => $val) {
                $dt_costing['desc'] = $name . ' - BPJS TK - ' . $key;
        
                $costing_id = $this->$model->create($dt_costing);
    
                array_push($output, $dt_costing);
    
                $dt_costing_services_class['costing_id'] = $costing_id;
                $dt_costing_services_class['service_class_id'] = $val['id'];
                $dt_costing_services_class['company_insurance_id'] = 'bb0d633f-99e1-4990-901b-0417a14e3cb6';
                $dt_costing_services_class['var_1'] = $var_1 * $val['percent'] / 100;
    
                $this->Costing_services_class->create($dt_costing_services_class);
    
                array_push($output, $dt_costing_services_class);
            }
        }


        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_costing,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create costing', $response_data);
            return;
        }

        $response_data = array(
            'data' => $output
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

        $costing_id = $id;

        $select = 'a.id';
        $costing = $this->$model->select($costing_id, $select);
    
        if (count($costing) == 0) {
            $this->REST_Return(404, 'there is no costing with id ' . $costing_id);
            return;
        }

        $type_costing_id = $dt_put['type_costing_id'];
        $name = $dt_put['name'];
        $desc = $dt_put['desc'];
        $is_ref = $dt_put['is_ref'];
        $costing_service_class_arr = $dt_put['costing_service_class'];

        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_costing = array(
            'type_costing_id'   => $type_costing_id,
            'name'              => $name,
            'desc'              => $desc,
            'is_ref'            => $is_ref,
        );

        $this->$model->update($costing_id, $dt_costing);

        for ($i=0; $i < count($costing_service_class_arr); $i++) { 
            $select = 'a.id';
            $where = array('costing_id' => $costing_id);
            $costing_service_class = $this->Costing_services_class->select(0, $select, $where);
            if (count($costing_service_class) == 0) {
                $this->REST_Return(404, 'there is no costing_service_class with costing_id ' . $costing_id);
                return;
            }

            $costing_service_class_id = $costing_service_class[0]->id;
            $company_insurance_id = $costing_service_class_arr[$i]['company_insurance_id'];
            $service_class_id = $costing_service_class_arr[$i]['service_class_id'];
            $health_facility_item_id = $costing_service_class_arr[$i]['health_facility_item_id'];
            $bride_code = $costing_service_class_arr[$i]['bride_code'];
            $bride_satuan = $costing_service_class_arr[$i]['bride_satuan'];
            $var_1 = $costing_service_class_arr[$i]['var_1'];
            $var_2 = $costing_service_class_arr[$i]['var_2'];
            $var_3 = $costing_service_class_arr[$i]['var_3'];
            $persediaan = $costing_service_class_arr[$i]['persediaan'];
            $penjualan = $costing_service_class_arr[$i]['penjualan'];
            $disc_penjualan = $costing_service_class_arr[$i]['disc_penjualan'];
            $bride_department = $costing_service_class_arr[$i]['bride_department'];
            $bride_department_sub = $costing_service_class_arr[$i]['bride_department_sub'];

            if ($company_insurance_id == null || $company_insurance_id == '') {
                $this->REST_Return(400, 'field company_insurance_id in costing_service_class is required');
                return;
            }

            if ($service_class_id == null || $service_class_id == '') {
                $this->REST_Return(400, 'field service_class_id in costing_service_class is required');
                return;
            }

            if ($health_facility_item_id == null || $health_facility_item_id == '') {
                $this->REST_Return(400, 'field health_facility_item_id in costing_service_class is required');
                return;
            }

            $company_insurance = $this->Company->select($company_insurance_id);
            if (count($company_insurance) == 0) {
                $this->REST_Return(404, 'there is no company_insurance with id ' . $company_insurance_id);
                return;
            }

            $service_class = $this->Service_class->select($service_class_id);
            if (count($service_class) == 0) {
                $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
                return;
            }

            $health_facility_item = $this->Health_facility_item->select($health_facility_item_id);
            if (count($health_facility_item) == 0) {
                $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
                return;
            }

            $dt_costing_services_class = array(
                'company_insurance_id' => $company_insurance_id,
                'service_class_id' => $service_class_id,
                'health_facility_item_id' => $health_facility_item_id,
                'bride_code' => $bride_code,
                'bride_satuan' => $bride_satuan,
                'var_1' => $var_1,
                'var_2' => $var_2,
                'var_3' => $var_3,
                'persediaan' => $persediaan,
                'penjualan' => $penjualan,
                'disc_penjualan' => $disc_penjualan,
                'bride_department' => $bride_department,
                'bride_department_sub' => $bride_department_sub,
            );

            $this->Costing_services_class->update($costing_service_class_id, $dt_costing_services_class);
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_costing,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update costing', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $costing_id
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
    
        $costing_id = $this->$model->select_id($id);
    
        if ($costing_id == "") {
            $this->REST_Return(404, 'there is no costing with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_costing = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($costing_id, $dt_costing);

        $select = 'a.id';
        $where = array('costing_id' => $costing_id);
        $costing_service_class = $this->Costing_services_class->select(0, $select, $where);

        for ($i=0; $i < count($costing_service_class); $i++) { 
            $costing_service_class_id = $costing_service_class[$i]->id;

            $this->Costing_services_class->update($costing_service_class_id, $dt_costing);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_costing,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete costing', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}
