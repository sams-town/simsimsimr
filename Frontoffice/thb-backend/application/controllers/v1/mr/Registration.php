<?php defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Restserver\Libraries\REST_Controller;
class Registration extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_insurance_model", "{$this->name}_Insurance");
        $this->load->model("$this->directory/{$this->name}_destination_model", "{$this->name}_Destination");
        $this->load->model("$this->directory/Registration_short_model", "Registration_short");
        $this->load->model("$this->directory/Registration_inv_model", "Registration_inv");
        $this->load->model("$this->directory/Registration_search_model", "Registration_search");
        $this->load->model("$this->directory/Appointment_model", "Appointment");
        $this->load->model("$this->directory/Document_model", "Document");
        $this->load->model("$this->directory/Insurance_model", "Insurance");
        $this->load->model("$this->directory/Patient_model", "Patient");
        $this->load->model("$this->directory/Registration_val_model", "Registration_val");
        $this->load->model("$this->directory/Patient_val_model", "Patient_val");
        $this->load->model("$this->directory/Icd10_model", "Icd10");
        $this->load->model("$this->directory/Responsible_person_model", "Responsible_person");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("$this->directory/Refer_internal_model", "Refer_internal");
        $this->load->model("$this->directory/Patient_allergy_model", "Patient_allergy");
        $this->load->model("$this->directory/inpatient/Consultation_model", "Consultation");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/health_facility/Bed_model", "Bed");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/medical_support/Doctor_schedule_item_model", "Doctor_schedule_item");
        $this->load->model("v1/health_facility/Laboratory_package_model", "Laboratory_package");
        $this->load->model("v1/health_facility/Laboratory_request_model", "Laboratory_request");
        $this->load->model("v1/health_facility/Laboratory_request_item_model", "Laboratory_request_item");
        $this->load->model("v1/health_facility/Radiology_package_model", "Radiology_package");
        $this->load->model("v1/health_facility/Radiology_request_model", "Radiology_request");
        $this->load->model("v1/health_facility/Radiology_request_item_model", "Radiology_request_item");
        $this->load->model("v1/health_facility/Medical_support_bundle_model", "Medical_support_bundle");
        $this->load->model("v1/health_facility/Medical_support_bundle_item_model", "Medical_support_bundle_item");
        $this->load->model("v1/tr/Transaction_administration_model", "Transaction_administration");
        $this->load->model("v1/hr/Position_model", "Position");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/setting/How_to_enter_model", "How_to_enter");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/Exits_model", "Exit");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/setting/Helper_model", "Helper");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/third_party/Company_model", "Company");
        $this->load->model("v1/tr/Transaction_bed_model", "Transaction_bed");
        $this->load->model("v1/tr/Transaction_invoice_model", "Transaction_invoice");
        $this->load->model("v1/Queue_model", "Queue");
        $this->load->model("v1/Queue_loket_model", "Queue_loket");
        $this->load->model("Taskid_model", "Taskid");
        // Load Helper
        $this->load->helper('queue');
        $this->load->helper('variable');
        $this->load->helper('utils');

        $this->create_external_rules = array(
            array('field' => 'norm', 'label' => 'norm', 'rules' => 'trim|required'),
            array('field' => 'type_register_id', 'label' => 'type_register_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'how_to_enter_id', 'label' => 'how_to_enter_id', 'rules' => 'trim|required|max_length[36]'),
            // array('field' => 'icd10_id', 'label' => 'icd10_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'destination[]', 'label' => 'destination', 'rules' => 'trim|required'),
        );

        $this->create_inpatient_rules = array(
            array('field' => 'norm', 'label' => 'norm', 'rules' => 'trim|required'),
            array('field' => 'type_register_id', 'label' => 'type_register_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_surgery', 'label' => 'is_surgery', 'rules' => 'trim|max_length[1]'),
            array('field' => 'how_to_enter_id', 'label' => 'how_to_enter_id', 'rules' => 'trim|required|max_length[36]'),
            // array('field' => 'icd10_id', 'label' => 'icd10_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'destination[]', 'label' => 'destination', 'rules' => 'trim|required'),
        );
    }

    /**
     * Registration API
     * --------------------
     * @param: norm
     * @param: type_register_id
     * @param: how_to_enter_id
     * @param: icd10_id
     * @param: type_document_id
     * @param: url
     * @param: destinations
     * --------------------
     * @method : POST
     * @link: version/mr/registration
     **/
    
    private function get_estimasi($kodepoli, $tanggalperiksa)
    {
        /* perhitungan estimasi disesuaikan sendiri dengan sistem antrian RS */
        $stamp = strtotime($tanggalperiksa);
        $time_in_ms = $stamp * 1000;
        // var_dump($time_in_ms);
        return $time_in_ms;
    }

    private function tomiliseconds($tanggal)
    {
        date_default_timezone_set('Asia/Jakarta');
        return strtotime($tanggal) * 1000;
    }

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
            // DATA REGISTRATION
            $this->form_validation->set_rules('norm', 'norm', 'trim|max_length[12]');
            $this->form_validation->set_rules('type_register_id', 'type_register_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('how_to_enter_id', 'how_to_enter_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('icd10_id', 'icd10_id', 'trim|max_length[36]');
            // DATA DOCUMENT
            $this->form_validation->set_rules('type_document_id', 'type_document_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('url', 'url', 'trim');
            // DATA REGISTRATION_DESTINATION
            $this->form_validation->set_rules('destinations[]', 'destinations', 'trim');
            // $this->form_validation->set_rules('insurances[]', 'insurances', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check how_to_enter
                if($this->How_to_enter->read($dt_post['how_to_enter_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    // INIT_REGISTRATION | code | norm | type_register_id | how_to_enter_id | icd10_id | icd10_code | icd10_name | register_date
                    
                    $dt_registration = array(
                        'type_register_id'     => $dt_post['type_register_id'],
                        'how_to_enter_id'      => $dt_post['how_to_enter_id'],
                        'type_visit_id'        => $dt_post['type_visit_id'],
                        'register_date'        => date("Y-m-d H:i:s", time())
                    );

                    // is External?
                    if($dt_post['type_register_id'] == '2') {
                        // Count code
                        $this->$model->orderFields = array('a.code' => 'DESC');
                        $this->$model->whereFields['type_register_id'] = 2;
                        // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                        $this->$model->limit = 1;
                        $tmp = $this->$model->read(0,false);
                        $waktu = date("Ymd",time());
                        if($tmp != []) {
                            $code = 'EREG' . $waktu . str_pad(intval(substr($tmp[0]->code,12)) + 1, 3, '0', STR_PAD_LEFT);
                            // Check if reset is needed
                            if(substr($tmp[0]->code,4,8) != date('Ymd',time())) {
                                $code = 'EREG' . $waktu . '001';
                            }
                        } else {
                            $code = 'EREG' . $waktu . '001';
                        }
                        
                        $dt_registration['code']   = $code;

                        // Count norm
                        $this->Patient->limit = 1;
                        $this->Patient->orderFields = array('a.norm' => 'DESC');
                        $this->Patient->whereFields['b.institute_branch_id'] = $institute_branch_id;
                        $this->Patient->whereFields['g.institute_branch_id'] = $institute_branch_id;
                        $this->Patient->whereFields['h.institute_branch_id'] = $institute_branch_id;
                        $this->Patient->whereFields['i.institute_branch_id'] = $institute_branch_id;
                        $this->Patient->whereFields['type_patient_id'] = 2;
                        $this->Patient->limit = 1;
                        $tmp = $this->Patient->read(0,false);

                        if($tmp != []) {
                            $norm = 'EPI' . str_pad(intval(substr($tmp[0]->norm,3)) + 1, 7, '0', STR_PAD_LEFT);
                        } else {
                            $norm = 'EPI' . '0000001';
                        }

                        // Flag patient
                        $this->Registration->flag_patient($norm);

                        $dt_registration['norm']           = $norm;
                        $dt_patient['norm']                = $norm;
                        $dt_patient['institute_branch_id'] = $institute_branch_id;
                        $dt_patient['idcard']              = isset($dt_post['patient']['idcard']) ? $dt_post['patient']['idcard'] : '';
                        $dt_patient['typeidcard']          = isset($dt_post['patient']['typeidcard']) ? $dt_post['patient']['typeidcard'] : '';
                        $dt_patient['alias']               = isset($dt_post['patient']['alias']) ? $dt_post['patient']['alias'] : '';
                        $dt_patient['f_title']             = isset($dt_post['patient']['f_title']) ? $dt_post['patient']['f_title'] : '';
                        $dt_patient['name']                = isset($dt_post['patient']['name']) ? $dt_post['patient']['name'] : '';
                        $dt_patient['b_title']             = isset($dt_post['patient']['b_title']) ? $dt_post['patient']['b_title'] : '';
                        $dt_patient['fullname']            = isset($dt_post['patient']['fullname']) ? $dt_post['patient']['fullname'] : '';
                        $dt_patient['birth_date']          = isset($dt_post['patient']['birth_date']) ? $dt_post['patient']['birth_date'] : '';
                        $dt_patient['birth_place']         = isset($dt_post['patient']['birth_place']) ? $dt_post['patient']['birth_place'] : '';
                        $dt_patient['address']             = isset($dt_post['patient']['address']) ? $dt_post['patient']['address'] : '';
                        $dt_patient['sex']                 = isset($dt_post['patient']['sex']) ? $dt_post['patient']['sex'] : '';
                        $dt_patient['blood']               = isset($dt_post['patient']['blood']) ? $dt_post['patient']['blood'] : '';
                        $dt_patient['married_id']          = isset($dt_post['patient']['married_id']) ? $dt_post['patient']['married_id'] : '';
                        $dt_patient['profession_id']       = isset($dt_post['patient']['profession_id']) ? $dt_post['patient']['profession_id'] : '';
                        $dt_patient['citizenship_id']      = isset($dt_post['patient']['citizenship_id']) ? $dt_post['patient']['citizenship_id'] : '';
                        $dt_patient['religion_id']         = isset($dt_post['patient']['religion_id']) ? $dt_post['patient']['religion_id'] : '';
                        $dt_patient['last_edu_id']         = isset($dt_post['patient']['last_edu_id']) ? $dt_post['patient']['last_edu_id'] : '';
                        $dt_patient['phone']               = isset($dt_post['patient']['phone']) ? $dt_post['patient']['phone'] : '';
                        $dt_patient['email']               = isset($dt_post['patient']['email']) ? $dt_post['patient']['email'] : '';
                        $dt_patient['address_id']          = isset($dt_post['patient']['address_id']) ? $dt_post['patient']['address_id'] : '';
                        $dt_patient['zip_code']            = isset($dt_post['patient']['zip_code']) ? $dt_post['patient']['zip_code'] : '';
                        $dt_patient['patient_issue_id']    = isset($dt_post['patient']['patient_issue_id']) ? $dt_post['patient']['patient_issue_id'] : '';
                        $dt_patient['patient_issue_text']  = isset($dt_post['patient']['patient_issue_text']) ? $dt_post['patient']['patient_issue_text'] : '';
                        $dt_patient['date_added']          = date("Y-m-d H:i:s",time());
                        $dt_patient['date_updated']        = date("Y-m-d H:i:s",time());
                        $dt_patient['type_patient_id']     = '2';
                        $dt_patient['is_new']              = 'Y';

                        $this->Patient->replace($dt_patient);
                    } else if($dt_post['type_register_id'] == '3'){ // EMERGENCY PATIENT
                        // Count code
                        // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                        // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                        $this->$model->limit = 1;
                        $this->$model->whereFields['a.register_date >='] = date('Y-m-d').' 00:00:00';
                        $this->$model->whereFields['a.register_date <='] = date('Y-m-d').' 23:59:59';
                        $this->$model->orderFields = array('a.code' => 'DESC');
                        $tmp = $this->$model->read(0,false);
                        $waktu = date("Ymd",time());
                        if($tmp != []) {
                            $code = 'REG' . $waktu . str_pad(intval(substr($tmp[0]->code,11)) + 1, 4, '0', STR_PAD_LEFT);
                            // Check if reset is needed
                            if(substr($tmp[0]->code,3,8) != date('Ymd',time())) {
                                $code = 'REG' . $waktu . '0001';
                            }
                        } else {
                            $code = 'REG' . $waktu . '0001';
                        }

                        $dt_registration['code'] = $code;

                        // old patient
                        if($dt_post['norm'] != '') {
                            $norm = $dt_post['norm'];
                        } else { // new patient
                            // Count norm
                            $this->Patient->limit = 1;
                            $this->Patient->orderFields = array('a.norm' => 'DESC');
                            $this->Patient->whereFields['a.type_patient_id'] = '1';
                            $this->Patient->whereFields['b.institute_branch_id'] = $institute_branch_id;
                            $this->Patient->whereFields['g.institute_branch_id'] = $institute_branch_id;
                            $this->Patient->whereFields['h.institute_branch_id'] = $institute_branch_id;
                            $this->Patient->whereFields['i.institute_branch_id'] = $institute_branch_id;
                            $tmp = $this->Patient->read(0,false);
                            if($tmp != []) {
                                $norm = str_pad(($tmp[0]->norm) + 1, 6, "0", STR_PAD_LEFT);  
                                // $norm = str_pad(intval(substr($tmp[0]->norm,2)) + 1, 6, '0', STR_PAD_LEFT);
                            } else {
                                $norm = '000001';
                                $response_data = array();
                                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                                return;
                            }

                            // Patient
                            $dt_patient['norm']                = $norm;
                            $dt_patient['institute_branch_id'] = $institute_branch_id;
                            $dt_patient['name']                = isset($dt_post['patient']['name']) ? $dt_post['patient']['name'] : '';
                            $dt_patient['fullname']            = isset($dt_post['patient']['name']) ? $dt_post['patient']['name'] : '';
                            $dt_patient['idcard']              = isset($dt_post['patient']['idcard']) ? $dt_post['patient']['idcard'] : '';
                            $dt_patient['typeidcard']          = isset($dt_post['patient']['typeidcard']) ? $dt_post['patient']['typeidcard'] : '1';
                            $dt_patient['alias']               = isset($dt_post['patient']['alias']) ? $dt_post['patient']['alias'] : '';
                            $dt_patient['sex']                 = isset($dt_post['patient']['sex']) ? $dt_post['patient']['sex'] : '';
                            $dt_patient['birth_date']          = isset($dt_post['patient']['birth_date']) ? $dt_post['patient']['birth_date'] : '';
                            $dt_patient['birth_place']         = isset($dt_post['patient']['birth_place']) ? $dt_post['patient']['birth_place'] : '';
                            $dt_patient['address']             = isset($dt_post['patient']['address']) ? $dt_post['patient']['address'] : '';
                            $dt_patient['phone']               = isset($dt_post['patient']['phone']) ? $dt_post['patient']['phone'] : '';
                            $dt_patient['date_added']          = date("Y-m-d H:i:s",time());
                            $dt_patient['date_updated']        = date("Y-m-d H:i:s",time());
                            $dt_patient['type_patient_id']     = '1';
                            $dt_patient['is_new']              = 'Y';
                            $this->Patient->replace($dt_patient);
    
                            // Responsible_person 
                            $dt_responsible_person = array(
                                'norm'                  => $norm,
                                'responsible_id'        => isset($dt_post['patient']['responsible_id']) ? $dt_post['patient']['responsible_id'] : '',
                                'idcard'                => isset($dt_post['patient']['responsible_person_idcard']) ? $dt_post['patient']['responsible_person_idcard'] : '',
                                'typeidcard'            => isset($dt_post['patient']['responsible_person_typeidcard']) ? $dt_post['patient']['responsible_person_typeidcard'] : '',
                                'name'                  => isset($dt_post['patient']['responsible_person_name']) ? $dt_post['patient']['responsible_person_name'] : '',
                                'phone'                 => isset($dt_post['patient']['responsible_person_phone']) ? $dt_post['patient']['responsible_person_phone'] : '',
                            );
                            $this->Responsible_person->replace($dt_responsible_person);
                        }
                        
                        $dt_registration['norm'] = $norm;
                        // Flag patient
                        $this->Registration->flag_patient($norm);
                        $this->Patient_val->limit = 1;
                        // $this->Patient_val->whereFields['b.institute_branch_id'] = $institute_branch_id;
                        // $this->Patient_val->whereFields['g.institute_branch_id'] = $institute_branch_id;
                        // $this->Patient_val->whereFields['h.institute_branch_id'] = $institute_branch_id;
                        // $this->Patient_val->whereFields['i.institute_branch_id'] = $institute_branch_id;
                        $patient = $this->Patient_val->read($norm, false);
                    } else {
                        // norm is required for outpatient or inpatient
                        if($dt_post['norm'] != '') {
                            // Check norm
                            $this->Patient_val->limit = 1;
                            // $this->Patient_val->whereFields['b.institute_branch_id'] = $institute_branch_id;
                            // $this->Patient_val->whereFields['g.institute_branch_id'] = $institute_branch_id;
                            // $this->Patient_val->whereFields['h.institute_branch_id'] = $institute_branch_id;
                            // $this->Patient_val->whereFields['i.institute_branch_id'] = $institute_branch_id;
                            $patient = $this->Patient_val->read($dt_post['norm'], false);
                            if($patient != []) {
                                // Flag patient
                                $this->Registration->flag_patient($dt_post['norm']);
    
                                // Count code
                                $this->Registration_val->orderFields = array('a.code' => 'DESC');
                                // $this->Registration_val->whereFields['type_register_id'] = 1;
                                // $this->Registration_val->whereFields['h.institute_branch_id'] = $institute_branch_id;
                                // $this->Registration_val->whereFields['i.institute_branch_id'] = $institute_branch_id;
                                // $this->Registration_val->whereFields['j.institute_branch_id'] = $institute_branch_id;
                                // $this->Registration_val->whereFields['l.institute_branch_id'] = $institute_branch_id;
                                // $this->Registration_val->whereFields['m.institute_branch_id'] = $institute_branch_id;
                                $this->Registration_val->limit = 1;
                                $tmp = $this->Registration_val->read(0,false);
                                $waktu = date("Ymd",time());
                                if($tmp != []) {
                                    $code = 'REG' . $waktu . str_pad(intval(substr($tmp[0]->code,11)) + 1, 4, '0', STR_PAD_LEFT);
                                    // Check if reset is needed
                                    if(substr($tmp[0]->code,3,8) != date('Ymd',time())) {
                                        $code = 'REG' . $waktu . '0001';
                                    }
                                } else {
                                    $code = 'REG' . $waktu . '0001';
                                }
                                $norm = $dt_post['norm'];
    
                                $dt_registration['code']   = $code;
                                $dt_registration['norm']   = $norm;
                            } else {
                                $this->REST_Return(404, 'Wrong norm!');
                                return;
                            }
                        } else {
                            $this->REST_Return(400, 'norm is required!');
                            return;
                        }
                    }

                    // is Icd10?
                    if(isset($dt_post['icd10_id'])) {
                        $dt_icd10 = $this->Icd10->read($dt_post['icd10_id'], false);
                        
                        // Check icd10_id
                        if($dt_icd10 != []) {
                            $dt_registration['icd10_id'] = $dt_post['icd10_id'];
                            $dt_registration['icd10_code'] = $dt_icd10[0]->code;
                            $dt_registration['icd10_name'] = $dt_icd10[0]->name;
                        } else {
                            $this->REST_Return(404, 'Wrong icd10_id!');
                            return;
                        }
                    }
                    // var_dump($norm);
                    // var_dump($dt_registration);
                    // die();
                    $dt_registration['type_visit_id']   = $dt_post['type_visit_id'];
                    $dt_registration['code_booking']   = $dt_post['code_booking'];
                    $dt_registration['appointment_id']   = $dt_post['destinations'][0]['appointment_id'] != null && $dt_post['destinations'][0]['appointment_id'] != '' ? $dt_post['destinations'][0]['appointment_id'] : null;
                    $id = $this->$model->create($dt_registration);

                    // if($patient) {
                        
                    // }

                    // INIT_DOCUMENT
                    // if(isset($dt_post['type_document_id'])) {
                    //     $dt_document = array(
                    //         'norm'                  => $dt_post['norm'],
                    //         'type_document_id'      => isset($dt_post['type_document_id']) ? $dt_post['type_document_id'] : '',
                    //         'url'                   => isset($dt_post['url']) ? $dt_post['url'] : '',
                    //     );
                    //     $this->Document->replace($dt_document);
                    // }
                    
                    if(isset($dt_post['destinations'])) {
                        // INIT_REGISTRATION_DESTINATION | registration_id | health_facility_item_id | service_class_id
                        for ($i=0; $i < count($dt_post['destinations']) ; $i++) { 
                            // Check health_facility_item id & doctor_in_charge_id
                            $health_facility_item = $this->Health_facility_item->read($dt_post['destinations'][$i]['health_facility_item_id'], false);
                            $health_facility_item_id = $dt_post['destinations'][$i]['health_facility_item_id'];
                            $health_facility_item_name = $health_facility_item[0]->name;
                            $doctor_in_charge = $this->Employee->read($dt_post['destinations'][$i]['doctor_in_charge_id'], false);
                            if($health_facility_item != [] && $doctor_in_charge != []) {

                                // CHECK IF THE PATIENT IS DOUBLE DESTINATION
                                if($dt_post['type_register_id'] != '3'){
                                    // if ($dt_post['destinations'][0]['insurances'][0]['company_id'] != '' && $dt_post['destinations'][0]['insurances'][0]['company_id'] != null) {
                                    //     $flag_patient_double = $this->Registration->flag_patient_double_insurance($norm,$dt_post['destinations'][$i]['health_facility_item_id'],$dt_post['destinations'][$i]['doctor_in_charge_id'],$dt_post['destinations'][0]['insurances'][0]['company_id']);
                                    // } else {
                                        $flag_patient_double = $this->Registration->flag_patient_double($norm,$dt_post['destinations'][$i]['health_facility_item_id'],$dt_post['destinations'][$i]['doctor_in_charge_id']);
                                    // }
                                    if ($flag_patient_double == 'N') {
                                        $this->REST_Return(422, 'Pasien Sudah di Daftarkan ke '.$health_facility_item[0]->name.', '.$doctor_in_charge[0]->fullname.'. Pada hari ini!');
                                        return;
                                    }

                                    $flag_patient_appointment = $this->Registration->flag_appointment($norm,$dt_post['destinations'][$i]['health_facility_item_id'],$dt_post['destinations'][$i]['doctor_in_charge_id']);
                                    
                                    if ($flag_patient_appointment == 'MJKN') {
                                        $this->REST_Return(422, 'Pasien Sudah mendaftar MJKN Pada hari ini');
                                        return;
                                    }
                                    
                                    // if ($flag_patient_appointment == 'AP') {
                                    //     if($dt_post['destinations'][$i]['appointment_id'] == '') { 
                                    //         $this->REST_Return(422, 'Pasien Sudah Appointment Pada hari ini, Silahkan Pilih appointment');
                                    //         return;
                                    //     }
                                    // }

                                    if ($dt_post['destinations'][0]['jeniskunjungan'] != 2 && $dt_post['destinations'][0]['insurances'][0]['company_id'] == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                                        $flag_check_bpjs_feasible = $this->Registration->check_bpjs_feasible($norm);
                                        if ($flag_check_bpjs_feasible == 0) {
                                            $this->REST_Return(422, 'Pasien Sudah di Daftarkan kurang dari 7 hari sebelumnya (fragmentasi tl)');
                                            return;
                                        }
                                    }
                                } else {
                                    $check_emergency_double = $this->Registration->check_emergency_double($norm, $health_facility_item_id, $dt_post['destinations'][$i]['doctor_in_charge_id']);

                                    if ($check_emergency_double == 0) {
                                        $this->REST_Return(409, 'Pasien sudah di daftarkan ke ' . $health_facility_item_name . ' pada hari ini!');    
                                        return;
                                    }
                                    //ADM PASIEN
                                        $normint = (int)$norm;
                                        if ($dt_post['destinations'][0]['insurances'][0]['company_id'] == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                                            $idcostadmigdold = 'f443a668-b962-4ca1-a1be-692f1b95c77f';
                                            $idcostadmigdnew = '94f7ec3d-4944-404c-8bcb-367fd04312a3';
                                        }else{
                                            $idcostadmigdold = 'a29a2072-8f91-4331-bd9b-92ef86cf3faf';
                                            $idcostadmigdnew = 'fb766af1-895b-4f90-9f5d-be2f01d5adf4';
                                        }
                                        if ($normint <= 37774) {
                                            //ADM PASIEN BARU UMUM
                                            $this->Transaction_administration->create(array(
                                                'registration_id' => $id,
                                                'costing_id' => $idcostadmigdold,
                                                'costing_name' => 'Administrasi Pasien IGD (Lama)',
                                                'var_1' => 35000,
                                                'date_added' => date('Y-m-d H:i:s')
                                            ));
                                        } else {
                                            if ($patient[0]->is_new == 'Y'){
                                                //ADM PASIEN BARU UMUM
                                                $this->Transaction_administration->create(array(
                                                    'registration_id' => $id,
                                                    'costing_id' => $idcostadmigdnew,
                                                    'costing_name' => 'Administrasi Pasien IGD (Baru)',
                                                    'var_1' => 50000,
                                                    'date_added' => date('Y-m-d H:i:s')
                                                ));
                                                $this->Patient->replace(array('norm'=>$norm,'is_new'=>'N'));
                                            } else {
                                                //ADM PASIEN LAMA UMUM
                                                $this->Transaction_administration->create(array(
                                                    'registration_id' => $id,
                                                    'costing_id' => $idcostadmigdold,
                                                    'costing_name' => 'Administrasi Pasien IGD (Lama)',
                                                    'var_1' => 35000,
                                                    'date_added' => date('Y-m-d H:i:s')
                                                ));
                                            }
                                        }
                                    //ADM PASIEN
                                }
                                // CHECK IF THE PATIENT IS NEW
                                $is_new = $this->Registration->flag_patient_destination($norm,$dt_post['destinations'][$i]['health_facility_item_id']);

                                // GET QUEUE NUMBER
                                if($dt_post['destinations'][$i]['appointment_id'] != '') {
                                    $this->Appointment->whereFields['e.institute_branch_id'] = $institute_branch_id;
                                    $appointment = $this->Appointment->read($dt_post['destinations'][$i]['appointment_id'],false);
                                    // Check appointment_id
                                    if($appointment != []) {
                                        $queue['doctor_schedule_item_id'] = $appointment[0]->doctor_schedule_item_id;
                                        $queue['queue_code'] = $appointment[0]->code;
                                        $queue['queue'] = $appointment[0]->queue;
                                        // $queue = $this->Queue->get_queue_number($health_facility_item_id,$doctor_in_charge_id,$dt_post['destinations'][$i]['appointment_id']); 
                                    } else {
                                        $this->REST_Return(404, 'Wrong appointment_id!');
                                        return;
                                    }
                                } else if($dt_post['type_register_id'] == '3' || $dt_post['type_register_id'] == '4') { // No queue if emergency or inpatient
                                    $queue = $this->Queue->get_queue_number($dt_post['destinations'][$i]['health_facility_item_id'],$dt_post['destinations'][$i]['doctor_in_charge_id'],'0',true);
                                } else { 
                                    $queue = $this->Queue->get_queue_number($dt_post['destinations'][$i]['health_facility_item_id'],$dt_post['destinations'][$i]['doctor_in_charge_id']);
                                }

                                // Checking if error happen
                                if(isset($queue['error0'])) { // 'Doctor schedule not found!'
                                    $this->REST_Return(404, $queue['error0']);
                                    return;
                                } else if(isset($queue['error1'])) { // 'No schedule today!'
                                    $this->REST_Return(404, $queue['error1']);
                                    return;
                                } else if(isset($queue['error2'])) { // 'You missed the schedule!'
                                    $this->REST_Return(422, $queue['error2']);
                                    return;
                                } else if(isset($queue['error3'])) { // 'The quota is full for today!'
                                    $this->REST_Return(422, $queue['error3']);
                                    return;
                                }
                                // var_dump($queue);
                                // die();

                                // if Inpatient set $bed
                                if($dt_post['type_register_id'] == '4') {
                                    // Check bed_id
                                    $this->Bed->whereFields['a.is_delete'] = '0';
                                    $this->Bed->whereFields['b.institute_branch_id'] = $institute_branch_id;
                                    $bed = $this->Bed->read($dt_post['destinations'][$i]['bed_id']);
                                    if($bed == []) {
                                        $this->REST_Return(404, 'There is no bed with id ' . $dt_post['destinations'][$i]['bed_id']);
                                        return;
                                    }
                                }

                                if ($dt_post['type_register_id'] == '1'){ // RAWAT JALAN
                                    if ($dt_post['code_booking'] == "") {
                                        $this->load->library("transaction");
                                        //NEW COUNTER KODEBOOKING
                                        $codecounter_book = array(
                                            'var_1' => '01',
                                            'var_2' => date('ymd'),
                                            'var_3' => 'BOOKPOLI',
                                            'var_4' => 1,
                                            'var_5' => date('d')
                                        );
                                        $checkcounter_book = $this->transaction->counter($codecounter_book);
                                        $kodebooking = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue'], 4, "0", STR_PAD_LEFT);
                                        if ($checkcounter_book['counter_queue'] == '0') {
                                            $this->Counter_queue->replace(array(
                                                'var_1' => '01',
                                                'var_2' => date('ymd'),
                                                'var_3' => 'BOOKPOLI',
                                                'var_4' => 1,
                                                'var_5' => date('d'),
                                                'counter_queue' => 1
                                            ));
                                            $kodebooking = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue']+1, 4, "0", STR_PAD_LEFT);
                                        }
                                        $dt_post['code_booking'] = $kodebooking;
                                        $this->$model->replace(array('id'=>$id,'code_booking'=>$kodebooking));
                                    } else {
                                        $this->Queue_loket->whereFields['a.code_booking'] = $dt_post['code_booking'];
                                        $dataLoket = $this->Queue_loket->read(0,false)[0];
                                        if(!$dataLoket) {
                                            $this->REST_Return(404, 'No Kode Booking');
                                            return;
                                        }
                                    }
                                }

                                $dt_registration_destination = array(
                                    'registration_id'               => $id,
                                    'health_facility_item_id'       => isset($health_facility_item_id) ? $health_facility_item_id : $dt_post['destinations'][$i]['health_facility_item_id'],
                                    'bed_id'                        => isset($bed) ? $bed[0]->id : '',
                                    'doctor_in_charge_id'           => isset($doctor_in_charge_id) ? $doctor_in_charge_id : $dt_post['destinations'][$i]['doctor_in_charge_id'],
                                    'doctor_schedule_item_id'       => $queue['doctor_schedule_item_id'],
                                    'register_date'                 => date("Y-m-d H:i:s",time()),
                                    'checkin_date'                  => date("Y-m-d H:i:s",time()),
                                    'user_added'                    => $user_id,
                                    'date_task_1'                   => ($dataLoket) ? $dataLoket->date_task_1 : NULL,
                                    'date_task_2'                   => ($dataLoket) ? date("Y-m-d H:i:s",time()) : NULL,
                                    'is_new'                        => $is_new,
                                    'code_queue'                    => ($dt_post['type_register_id'] == 3 || $dt_post['type_register_id'] == 4) ? NULL : $queue['queue_code'], // if emergency or inpatient queue 0
                                    'queue'                         => ($dt_post['type_register_id'] == 3 || $dt_post['type_register_id'] == 4) ? 0 : ($dt_post['queue_manual'] != "" ? $dt_post['queue_manual'] : $queue['queue']), // if emergency or inpatient queue 0
                                    'type_emergency_id'             => isset($dt_post['destinations'][$i]['type_emergency_id']) ? $dt_post['destinations'][$i]['type_emergency_id'] : '',
                                    'type_emergency_case_id'        => isset($dt_post['destinations'][$i]['type_emergency_case_id']) ? $dt_post['destinations'][$i]['type_emergency_case_id'] : '',
                                    'case_location'                 => isset($dt_post['destinations'][$i]['case_location']) ? $dt_post['destinations'][$i]['case_location'] : '',
                                    'name_location'                 => isset($dt_post['destinations'][$i]['name_location']) ? $dt_post['destinations'][$i]['name_location'] : '',
                                );

                                if ($dt_post['type_register_id'] == '1'){ // RAWAT JALAN

                                    $dataPoli = $this->Health_facility_item->read($dt_post['destinations'][$i]['health_facility_item_id'],false)[0];
                                    $dataDokter = $this->Employee->read($dt_post['destinations'][$i]['doctor_in_charge_id'],false)[0];
                                    $dataSchedule = $this->Doctor_schedule_item->read($queue['doctor_schedule_item_id'],false)[0];
                                    // $jeniskunjungan = $dataPoli->bride_bpjs != $dt_post['poliRujukan'] ? '2' : $dt_post['destinations'][0]['jeniskunjungan'];
                                    $jeniskunjungan = $dt_post['destinations'][0]['jeniskunjungan'];
                                    // $jeniskunjungan = 2;
                                    // Tambah Antrean BPJS RIN2024111800000003
                                    // JKN
                                    if ($dt_post['destinations'][0]['insurances'][0]['company_id'] == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                                        //ADM PASIEN
                                            $normint = (int)$norm;
                                            if ($normint <= 37774) {
                                                //ADM PASIEN BARU BPJS
                                                $this->Transaction_administration->create(array(
                                                    'registration_id' => $id,
                                                    'costing_id' => 'fc2e9ae3-32d0-4780-b206-85586abd977d',
                                                    'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                                    'var_1' => 35000,
                                                    'date_added' => date('Y-m-d H:i:s')
                                                ));
                                            } else {
                                                if ($patient[0]->is_new == 'Y'){
                                                    //ADM PASIEN BARU BPJS
                                                    $this->Transaction_administration->create(array(
                                                        'registration_id' => $id,
                                                        'costing_id' => '6b9f2fad-dd24-40a3-b5bd-b24e03cfe1c7',
                                                        'costing_name' => 'Administrasi Pasien Rawat Jalan (Baru)',
                                                        'var_1' => 50000,
                                                        'date_added' => date('Y-m-d H:i:s')
                                                    ));
                                                    $this->Patient->replace(array('norm'=>$norm,'is_new'=>'N'));
                                                } else {
                                                    //ADM PASIEN LAMA BPJS
                                                    $this->Transaction_administration->create(array(
                                                        'registration_id' => $id,
                                                        'costing_id' => 'fc2e9ae3-32d0-4780-b206-85586abd977d',
                                                        'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                                        'var_1' => 35000,
                                                        'date_added' => date('Y-m-d H:i:s')
                                                    ));
                                                }
                                            }
                                        //ADM PASIEN
                                        $data_antrean = array(
                                            'kodebooking'=> $dt_post['code_booking'],
                                            'jenispasien'=> ($dt_post['destinations'][0]['insurances'][0]['number_insurance'] != '') ? "JKN" : "NON JKN",
                                            'nomorkartu'=> $dt_post['destinations'][0]['insurances'][0]['number_insurance'],
                                            'nik'=> $patient[0]->idcard,
                                            'nohp'=> $patient[0]->phone ? $patient[0]->phone : 0,
                                            'kodepoli'=> $dataPoli->bride_bpjs,
                                            'namapoli'=> $dataPoli->bride_bpjs_name,
                                            'pasienbaru'=> $is_new == 'Y' ? 1 : 0,
                                            'norm'=> $norm,
                                            'tanggalperiksa'=> date('Y-m-d'),
                                            'kodedokter'=> $dataDokter->bride_bpjs,
                                            'namadokter'=> $dataDokter->fullname,
                                            'jampraktek' => substr($dataSchedule->hour_start, 0, -3).'-'.substr($dataSchedule->hour_end, 0, -3),
                                            'jeniskunjungan' => $jeniskunjungan,
                                            'nomorreferensi' => $dt_post['noKunjungan'] != "" ? $dt_post['noKunjungan'] : $dt_post['destinations'][0]['insurances'][0]['noKunjungan'],
                                            'nomorantrean'=> $queue['queue_code'].'-'.$queue['queue'],
                                            'angkaantrean'=> $queue['queue'],
                                            'estimasidilayani'=> $this->get_estimasi($dataPoli->bride_bpjs,date('Y-m-d') . ' ' . substr(substr($dataSchedule->hour_start, 0, -3).'-'.substr($dataSchedule->hour_end, 0, -3).'',0,5).'+'.(5*$queue['queue']).' minutes'),
                                            'sisakuotajkn'=> (int) $dataSchedule->quota_jkn-$queue['queue'],
                                            'kuotajkn'=> (int) $dataSchedule->quota_jkn,
                                            'sisakuotanonjkn'=> (int) $dataSchedule->quota_jkn,
                                            'kuotanonjkn'=> (int) $dataSchedule->quota_bo,
                                            'keterangan'=> 'Peserta harap 60 menit lebih awal guna pencatatan administrasi.',
                                        );

                                    } else {
                                    // NON JKN
                                        //ADM PASIEN
                                            $normint = (int)$norm;
                                            if ($normint <= 37774) {
                                                //ADM PASIEN BARU UMUM
                                                $this->Transaction_administration->create(array(
                                                    'registration_id' => $id,
                                                    'costing_id' => 'a6bff965-aadc-4554-b4ef-cc9d534aeb30',
                                                    'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                                    'var_1' => 35000,
                                                    'date_added' => date('Y-m-d H:i:s')
                                                ));
                                            } else {
                                                if ($patient[0]->is_new == 'Y'){
                                                    //ADM PASIEN BARU UMUM
                                                    $this->Transaction_administration->create(array(
                                                        'registration_id' => $id,
                                                        'costing_id' => 'ef619d4d-d2ca-4434-b26f-217803863d4b',
                                                        'costing_name' => 'Administrasi Pasien Rawat Jalan (Baru)',
                                                        'var_1' => 50000,
                                                        'date_added' => date('Y-m-d H:i:s')
                                                    ));
                                                    $this->Patient->replace(array('norm'=>$norm,'is_new'=>'N'));
                                                } else {
                                                    //ADM PASIEN LAMA UMUM
                                                    $this->Transaction_administration->create(array(
                                                        'registration_id' => $id,
                                                        'costing_id' => 'a6bff965-aadc-4554-b4ef-cc9d534aeb30',
                                                        'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                                        'var_1' => 35000,
                                                        'date_added' => date('Y-m-d H:i:s')
                                                    ));
                                                }
                                            }
                                        //ADM PASIEN
                                        $jeniskunjungan = 3;
                                        $data_antrean = array(
                                            'kodebooking'=> $dt_post['code_booking'],
                                            'jenispasien'=> "NON JKN",
                                            'nomorkartu'=> "",
                                            'nik'=> $patient[0]->idcard,
                                            'nohp'=> $patient[0]->phone ? $patient[0]->phone : 0,
                                            'kodepoli'=> $dataPoli->bride_bpjs,
                                            'namapoli'=> $dataPoli->bride_bpjs_name,
                                            'pasienbaru'=> $is_new == 'Y' ? 1 : 0,
                                            'norm'=> $norm,
                                            'tanggalperiksa'=> date('Y-m-d'),
                                            'kodedokter'=> $dataDokter->bride_bpjs,
                                            'namadokter'=> $dataDokter->fullname,
                                            'jampraktek' => substr($dataSchedule->hour_start, 0, -3).'-'.substr($dataSchedule->hour_end, 0, -3),
                                            'jeniskunjungan' => $jeniskunjungan,
                                            'nomorreferensi' => "",
                                            'nomorantrean'=> $queue['queue_code'].'-'.$queue['queue'],
                                            'angkaantrean'=> $queue['queue'],
                                            'estimasidilayani'=> $this->get_estimasi($dataPoli->bride_bpjs,date('Y-m-d') . ' ' . substr(substr($dataSchedule->hour_start, 0, -3).'-'.substr($dataSchedule->hour_end, 0, -3).'',0,5).'+'.(5*$queue['queue']).' minutes'),
                                            'sisakuotajkn'=> (int) $dataSchedule->quota_jkn-$queue['queue'],
                                            'kuotajkn'=> (int) $dataSchedule->quota_jkn,
                                            'sisakuotanonjkn'=> (int) $dataSchedule->quota_jkn,
                                            'kuotanonjkn'=> (int) $dataSchedule->quota_bo,
                                            'keterangan'=> 'Peserta harap 60 menit lebih awal guna pencatatan administrasi.',
                                        );
                                    }
                                    // var_dump($data_antrean);die();
                                    if ($dataPoli->bride_bpjs_name && $dataPoli->bride_bpjs_name != 'GIGI') {
                                        // if ($patient[0]->idcard != '' && $patient[0]->idcard != null){
                                        // $this->load->library('Vclaim_antrol','vclaim_antrol');
                                        // $tambah_antrean = $this->vclaim_antrol->request("/antrean/add", "POST", json_encode($data_antrean));
                                        // $json_tambah_antrean = json_decode($tambah_antrean, true);
                                        // $json_tambah_antrean = json_decode($json_tambah_antrean);
                                        // // jika gagal tambah antrean bpjs
                                        // if($json_tambah_antrean->metadata->code != 200) {
                                        //     // rollback
                                        //     if ($json_tambah_antrean->metadata->message != 'Terdapat duplikasi Kode Booking') {
                                        //         $this->db->trans_rollback();
                                        //         $this->REST_Return(422, $json_tambah_antrean->metadata->message, $json_tambah_antrean->metadata->message);
                                        //         return;
                                        //     }
                                        // } 
                                        // // Update Waktu Antrean BPJS 1
                                        // if ($jeniskunjungan == 1 || $jeniskunjungan == 4) {
                                        //     if ($dataLoket->date_task_1 && $is_new == 'Y') {
                                        //         $update_antrean_1 = array(
                                        //             'kodebooking'       => $dt_post['code_booking'],
                                        //             'taskid'            => 1,
                                        //             'waktu'             => $this->tomiliseconds($dataLoket->date_task_1)
                                        //         );
                                        //         $this->Taskid->replace(array('code_booking'=> $dt_post['code_booking'],'date_task_1'=> $dataLoket->date_task_1,'mils_task_1'=>$this->tomiliseconds($dataLoket->date_task_1)));
                                        //         $updatewaktu_antrean = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_1));
                                        //         $json_update_antrean = json_decode($updatewaktu_antrean, true);
                                        //         $json_update_antrean = json_decode($json_update_antrean);
                                        //         // jika gagal update antrean bpjs
                                        //         // var_dump($json_update_antrsean);
                                        //         // if($json_update_antrean->metadata->code != 200) {
                                        //         //     // rollback
                                        //         //     $this->db->trans_rollback();
                                        //         //     $this->REST_Return(422, $json_update_antrean->metadata->message, $json_update_antrean->metadata->message);
                                        //         //     return;
                                        //         // } 
                                        //         // Update Waktu Antrean BPJS 2
                                        //         $update_antrean_2 = array(
                                        //             'kodebooking'       => $dt_post['code_booking'],
                                        //             'taskid'            => 2,
                                        //             'waktu'             => $this->tomiliseconds($dataLoket->date_task_2)
                                        //         );
                                        //         $this->Taskid->replace(array('code_booking'=> $dt_post['code_booking'],'date_task_2'=> $dataLoket->date_task_2,'mils_task_2'=>$this->tomiliseconds($dataLoket->date_task_2)));
                                        //         $updatewaktu_antrean_2 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_2));
                                        //         $json_update_antrean_2 = json_decode($updatewaktu_antrean_2, true);
                                        //         $json_update_antrean_2 = json_decode($json_update_antrean_2);
                                        //         // jika gagal update antrean bpjs
                                        //         // var_dump($json_update_antrean_2);
                                        //         // if($json_update_antrean_2->metadata->code != 200) {
                                        //         //     // rollback
                                        //         //     $this->db->trans_rollback();
                                        //         //     $this->REST_Return(422, $json_update_antrean_2->metadata->message, $json_update_antrean_2->metadata->message);
                                        //         //     return;
                                        //         // } 
                                        //     }
                                        // } else if ($jeniskunjungan == 3) {
    
                                        // }
                                        // } else {
                                            $dt_registration_destination['date_task_1'] = date("Y-m-d H:i:s",time());
                                            $dt_registration_destination['date_task_2'] = date("Y-m-d H:i:s",time());
                                            $dt_registration_destination['call_nurse_stamp'] = date("Y-m-d H:i:s",time());
                                            $dt_registration_destination['call_doctor_stamp'] = date("Y-m-d H:i:s",time());
                                            $dt_registration_destination['done_doctor_stamp'] = date("Y-m-d H:i:s",time());
                                            $dt_registration_destination['sts'] = 3;
                                            $this->$model->replace(array('id'=>$id,'code_booking'=>NULL));
                                        // }
                                    } else {
                                        $dt_registration_destination['date_task_1'] = date("Y-m-d H:i:s",time());
                                        $dt_registration_destination['date_task_2'] = date("Y-m-d H:i:s",time());
                                        $dt_registration_destination['call_nurse_stamp'] = date("Y-m-d H:i:s",time());
                                        $dt_registration_destination['call_doctor_stamp'] = date("Y-m-d H:i:s",time());
                                        $dt_registration_destination['done_doctor_stamp'] = date("Y-m-d H:i:s",time());
                                        $dt_registration_destination['sts'] = 3;
                                        $this->$model->replace(array('id'=>$id,'code_booking'=>NULL));
                                    }
                                }

                                // var_dump($dt_registration_destination);
                                // die();
                                $reg_destination = $this->Registration_Destination->create($dt_registration_destination);
                                
                                if(isset($dt_post['destinations'][$i]['insurances'])) {
                                    if(count($dt_post['destinations'][$i]['insurances']) > 0) {
                                        // INIT_REGISTRATION_INSURANCE | registration_id | company_id | service_class_id
                                        for ($a=0; $a < count($dt_post['destinations'][$i]['insurances']) ; $a++) { 
                                            if($dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] == 1) {
                                                if ($dt_post['type_register_id'] == '3') {
                                                    $dt_registration_insurance = array(
                                                        'registration_destination_id'   => $reg_destination,
                                                        'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                        'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                        'is_primary'                    => 1,
                                                    );
                                                } else {
                                                    $dt_registration_insurance = array(
                                                        'registration_destination_id'   => $reg_destination,
                                                        'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                        'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                        'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                    );
                                                }
                                                $this->Registration_Insurance->replace($dt_registration_insurance);
                                                $dt_insurance = array(
                                                    'norm'                          => $norm,
                                                    'institute_branch_id'           => $institute_branch_id,
                                                    'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                    'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                    'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                );
                                                $this->Insurance->replace($dt_insurance);
                                            } else {
                                                $company = $this->Company->read($dt_post['destinations'][$i]['insurances'][$a]['company_id'], false);
                                                $service_class = $this->Service_class->read($dt_post['destinations'][$i]['insurances'][$a]['service_class_id'], false);
                                                if($company != [] && $service_class != []) {
                                                    if ($dt_post['type_register_id'] == '3') {
                                                        $dt_registration_insurance = array(
                                                            'registration_destination_id'   => $reg_destination,
                                                            'company_id'                    => $dt_post['destinations'][$i]['insurances'][$a]['company_id'],
                                                            'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                            'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                            'expired'                       => isset($dt_post['destinations'][$i]['insurances'][$a]['expired']) ? $dt_post['destinations'][$i]['insurances'][$a]['expired'] : date('Y-m-d'),
                                                            'is_primary'                    => 1,
                                                            // 'noKunjungan'                   => isset($dt_post['destinations'][$i]['insurances'][$a]['noKunjungan']) ? $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'] : '',
                                                            'noKunjungan'                   => $dt_post['noKunjungan'] != "" ? $dt_post['noKunjungan'] : $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'],
                                                            'service_class_id'              => $dt_post['destinations'][$i]['insurances'][$a]['service_class_id'],
                                                            'service_class_name'            => $service_class[0]->name,
                                                        );
                                                    } else {
                                                        $dt_registration_insurance = array(
                                                            'registration_destination_id'   => $reg_destination,
                                                            'company_id'                    => $dt_post['destinations'][$i]['insurances'][$a]['company_id'],
                                                            'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                            'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                            'expired'                       => isset($dt_post['destinations'][$i]['insurances'][$a]['expired']) ? $dt_post['destinations'][$i]['insurances'][$a]['expired'] : '',
                                                            'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                            // 'noKunjungan'                   => isset($dt_post['destinations'][$i]['insurances'][$a]['noKunjungan']) ? $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'] : '',
                                                            'noKunjungan'                   => $dt_post['noKunjungan'] != "" ? $dt_post['noKunjungan'] : $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'],
                                                            'service_class_id'              => $dt_post['destinations'][$i]['insurances'][$a]['service_class_id'],
                                                            'service_class_name'            => $service_class[0]->name,
                                                        );
                                                    }
                                                    $this->Registration_Insurance->replace($dt_registration_insurance);
                                                    $dt_insurance = array(
                                                        'norm'                          => $norm,
                                                        'institute_branch_id'           => $institute_branch_id,
                                                        'company_id'                    => $dt_post['destinations'][$i]['insurances'][$a]['company_id'],
                                                        'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                        'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                        'expired'                       => isset($dt_post['destinations'][$i]['insurances'][$a]['expired']) ? $dt_post['destinations'][$i]['insurances'][$a]['expired'] : '',
                                                        'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                        'service_class_id'              => $dt_post['destinations'][$i]['insurances'][$a]['service_class_id'],
                                                    );
                                                    $this->Insurance->replace($dt_insurance);
                                                    if ($dt_post['destinations'][$i]['insurances'][$a]['company_id'] == 'a85dc65b-3343-4056-b761-269d0901e840') {
                                                        $this->Transaction_administration->whereFields['a.registration_id'] = $id; 
                                                        $dt_tr_adm = $this->Transaction_administration->read(0,false)[0];
                                                        if($dt_tr_adm->id){
                                                            $this->Transaction_administration->replace(array(
                                                                'id' => $dt_tr_adm->id,
                                                                'costing_id' => '9c016b83-0820-4dcd-b2cf-19ba3f2aef2d',
                                                                'costing_name' => 'ADMINISTRASI PASIEN ALODOK',
                                                                'var_1' => 30000,
                                                            ));
                                                        }
                                                    // ADMIN TK HARCODE
                                                    } else if ($dt_post['destinations'][$i]['insurances'][$a]['company_id'] == 'bb0d633f-99e1-4990-901b-0417a14e3cb6') {
                                                        $this->Transaction_administration->whereFields['a.registration_id'] = $id; 
                                                        $dt_tr_adm = $this->Transaction_administration->read(0,false)[0];
                                                        if($dt_tr_adm->id){
                                                            if ($dt_tr_adm->costing_id == 'a6bff965-aadc-4554-b4ef-cc9d534aeb30') {
                                                                $this->Transaction_administration->replace(array(
                                                                    'id' => $dt_tr_adm->id,
                                                                    'costing_id' => '631f6c1b-9764-4acf-a69a-7fcb828c1c45',
                                                                    'costing_name' => 'ADMINISTRASI PASIEN LAMA BPJS TK',
                                                                    'var_1' => 20000,
                                                                ));
                                                            } else {
                                                                $this->Transaction_administration->replace(array(
                                                                    'id' => $dt_tr_adm->id,
                                                                    'costing_id' => '5bfc4f2e-f843-4356-bc78-0fbead0f031b',
                                                                    'costing_name' => 'ADMINISTRASI PASIEN BARU BPJS TK',
                                                                    'var_1' => 30000,
                                                                ));
                                                            }
                                                        }
                                                    // ADMIN MI MC HARCODE
                                                    } else if ($dt_post['destinations'][$i]['insurances'][$a]['company_id'] == '315fe232-e034-4b45-bf0d-e8cbb1f04c1a') {
                                                        $this->Transaction_administration->whereFields['a.registration_id'] = $id; 
                                                        $dt_tr_adm = $this->Transaction_administration->read(0,false)[0];
                                                        if($dt_tr_adm->id){
                                                            if ($dt_tr_adm->costing_id == 'a6bff965-aadc-4554-b4ef-cc9d534aeb30') {
                                                                $this->Transaction_administration->replace(array(
                                                                    'id' => $dt_tr_adm->id,
                                                                    'costing_id' => 'f9db2634-7827-40a6-8228-e4514c5488e2',
                                                                    'costing_name' => 'Administrasi Pasien (Lama)',
                                                                    'var_1' => 11000,
                                                                ));
                                                            } else {
                                                                $this->Transaction_administration->replace(array(
                                                                    'id' => $dt_tr_adm->id,
                                                                    'costing_id' => 'bfd47d71-313e-43b9-b017-ec231b3d67ec',
                                                                    'costing_name' => 'Administrasi Pasien (Baru)',
                                                                    'var_1' => 16500,
                                                                ));
                                                            }
                                                        }
                                                    // ADMIN MI IDEN HARCODE
                                                    } else if ($dt_post['destinations'][$i]['insurances'][$a]['company_id'] == '8a322cf3-e135-4909-bc25-48e31adadb0e') {
                                                        $this->Transaction_administration->whereFields['a.registration_id'] = $id; 
                                                        $dt_tr_adm = $this->Transaction_administration->read(0,false)[0];
                                                        if($dt_tr_adm->id){
                                                            if ($dt_tr_adm->costing_id == 'a6bff965-aadc-4554-b4ef-cc9d534aeb30') {
                                                                $this->Transaction_administration->replace(array(
                                                                    'id' => $dt_tr_adm->id,
                                                                    'costing_id' => '32d17998-9ba2-47a9-9539-0de76707cc9c',
                                                                    'costing_name' => 'Administrasi Pasien (Lama)',
                                                                    'var_1' => 20000,
                                                                ));
                                                            } else {
                                                                $this->Transaction_administration->replace(array(
                                                                    'id' => $dt_tr_adm->id,
                                                                    'costing_id' => '0f49cf82-b43a-4726-8148-761f4cc6ad2c',
                                                                    'costing_name' => 'Administrasi Pasien (Baru)',
                                                                    'var_1' => 30000,
                                                                ));
                                                            }
                                                        }
                                                    }
                                                } else {
                                                    $this->REST_Return(404, 'There is no company_id ' . $dt_post['destinations'][$i]['insurances'][$a]['company_id'] . ' or service_class_id ' . $dt_post['destinations'][$i]['insurances'][$a]['service_class_id']);
                                                    return;
                                                }
                                            }
                                        }
                                    } else {
                                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', 'Insurance field is required');
                                        return;
                                    }
                                } else {
                                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', 'Insurance field is required');
                                    return;
                                }

                                // Inpatient
                                if(isset($bed)) {
                                    // Set bed status to filled
                                    $dt_bed = array(
                                        'id'                  => $bed[0]->id,
                                        'type_status_bed_id'  => 2,
                                    );
                                    $this->Bed->replace($dt_bed);
    
                                    $this->Room->whereFields['a.is_delete'] = '0';
                                    $this->Room->whereFields['b.institute_branch_id'] = $institute_branch_id;
                                    $room = $this->Room->read($bed[0]->room_id,false);
                                    // INIT_TRANSACTION_BED
                                    $dt_transaction_bed = array(
                                        'registration_destination_id'   => $reg_destination,
                                        'bed_id'                        => $bed[0]->id,
                                        'bed_name'                      => $bed[0]->name,
                                        'costing_id'                    => $room[0]->costing_id,
                                        'service_class_id'              => $room[0]->service_class_id,
                                        'date_added'                    => date('Y-m-d H:i:s',time()),
                                        'checkin_date'                  => date('Y-m-d H:i:s',time())
                                    );
                                    $this->Transaction_bed->create($dt_transaction_bed);
                                }
                            } else {
                                $this->REST_Return(404, 'There is no health_facility_item ' . $dt_post['destinations'][$i]['health_facility_item_id'] . ' or doctor_in_charge ' . $dt_post['destinations'][$i]['doctor_in_charge_id']);
                                return;
                            }
                        }
                    }
    
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'registration_id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong how_to_enter_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    // public function createAPM_post() {
    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] , $response_data);
    //         return;
    //     } 
    //         // $this->REST_Return(201, 'SUCCESS', $response_data);
    //         $this->REST_Return(201, 'SUCCESS', json_decode('{"registration_id":"1b3733be-0427-4f84-8cb8-d611e6605cb1","queue":"BC-8","sep":"0070R0320625V001466","norm":"015072","destination":"PARU","birth_date":"1961-10-03","sex":"L","name":"JUBEL SIMATUPANG","dpjp":"dr. Fersia Iranita Liza, Sp. P","test":{"request":{"t_sep":{"noKartu":"0000022848636","tglSep":"2025-06-14","ppkPelayanan":"0070R032","jnsPelayanan":"2","klsRawat":{"klsRawatHak":"1","klsRawatNaik":"","pembiayaan":"","penanggungJawab":""},"noMR":"015072","rujukan":{"asalRujukan":"1","tglRujukan":"2025-06-14","noRujukan":"007000050625Y000725","ppkRujukan":"00700005"},"catatan":"","diagAwal":"Z09.8","poli":{"tujuan":"PAR","eksekutif":"0"},"cob":{"cob":""},"katarak":{"katarak":"0"},"jaminan":{"lakaLantas":"0","noLP":"","penjamin":{"tglKejadian":"","keterangan":"","suplesi":{"suplesi":"","noSepSuplesi":"","lokasiLaka":{"kdPropinsi":"","kdKabupaten":"","kdKecamatan":""}}}},"tujuanKunj":"0","flagProcedure":"","kdPenunjang":"","assesmentPel":"","skdp":{"noSurat":"","kodeDPJP":""},"dpjpLayan":8879,"noTelp":"081371990737","user":"Administrasi"}}},"code_booking":null}'));
    //     return;
    // }

    public function createAPM_post() {
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
            // DATA REGISTRATION
            $this->form_validation->set_rules('norm', 'norm', 'trim|max_length[12]');
            $this->form_validation->set_rules('type_register_id', 'type_register_id', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('how_to_enter_id', 'how_to_enter_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('icd10_id', 'icd10_id', 'trim|max_length[36]');
            // DATA DOCUMENT
            $this->form_validation->set_rules('type_document_id', 'type_document_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('url', 'url', 'trim');
            // DATA REGISTRATION_DESTINATION
            $this->form_validation->set_rules('destinations[]', 'destinations', 'trim');
            // $this->form_validation->set_rules('insurances[]', 'insurances', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check how_to_enter
                if($this->How_to_enter->read($dt_post['how_to_enter_id'], false) != []) {
                    $model = $this->name;

                    //CEK DULU APAKAH SUDAH TER REGISTER
                        if($dt_post['norm'] != '') {
                            $select_checks = 'a.id,a.code,p.norm,rd.code_queue,rd.queue,hfi.name as health_facility_item_name,ri.sep,p.name,p.sex,p.birth_date,e.fullname as doctor_in_charge_name';
                            $where_checks['a.norm'] = $dt_post['norm'];
                            $where_checks['a.register_date >='] = date('Y-m-d') . ' 00:00:00';
                            $where_checks['a.register_date <='] = date('Y-m-d') . ' 23:59:59';
                            $join_checks = array(
                                array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
                                array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
                                array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
                                array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
                                array('patient p', 'a.norm = p.norm', 'INNER'),
                            );
                            $order = array('a.code' => 'DESC');
                            $read_checks = $this->Registration_val->select(0, $select_checks, $where_checks, $join_checks, $order);
                            if ($read_checks != []) {
                                $response_data = array(
                                    'registration_id'     => $read_checks[0]->id,
                                    'queue'               => $read_checks[0]->code_queue.'-'.$read_checks[0]->queue,
                                    'sep'                 => $read_checks[0]->sep,
                                    'norm'                => $read_checks[0]->norm,  
                                    'destination'         => $read_checks[0]->health_facility_item_name,
                                    'birth_date'          => $read_checks[0]->birth_date,
                                    'sex'                 => $read_checks[0]->sex == '1' ? 'L' : 'P',
                                    'name'                => $read_checks[0]->name,
                                    'dpjp'                => $read_checks[0]->doctor_in_charge_name,
                                    'test'                => null,
                                    'code_booking'        => null
                                );
                                $this->REST_Return(201, 'SUCCESS', $response_data);
                                return; 
                            }
                        } else {
                            $this->REST_Return(400, 'norm is required!');
                            return;
                        }
                    //CEK DULU APAKAH SUDAH TER REGISTER

                    /* Start Transaction */
                    $this->db->trans_start();
                    // INIT_REGISTRATION | code | norm | type_register_id | how_to_enter_id | icd10_id | icd10_code | icd10_name | register_date
                    $type_visit_id = 1;
                    // if jeniskunjungan = 1 , nomorreferensi = Rujukan FKTP,
                    // if jeniskunjungan 2 , nomorreferensi = Rujukan Internal
                    // if jeniskunjungan = 3 , nomorreferensi = Kontrol
                    // if jeniskunjungan = 4 , nomorreferensi = Rujukan Antar RS
                    if ($dt_post['destinations'][0]['jeniskunjungan'] == 1) {
                        $type_visit_id = 1;
                    } else if ($dt_post['destinations'][0]['jeniskunjungan'] == 2) {
                        $type_visit_id = 1;
                    } else if ($dt_post['destinations'][0]['jeniskunjungan'] == 4) {
                        $type_visit_id = 1;
                    } else if ($dt_post['destinations'][0]['jeniskunjungan'] == 3) {
                        $type_visit_id = 1;
                    } else {
                        $type_visit_id = 1;
                    }

                    $dt_registration = array(
                        'type_register_id'     => $dt_post['type_register_id'],
                        'how_to_enter_id'      => $dt_post['how_to_enter_id'],
                        'type_visit_id'        => $type_visit_id,
                        'register_date'        => date("Y-m-d H:i:s", time())
                    );
                    
                    // norm is required for outpatient or inpatient
                    if($dt_post['norm'] != '') {
                        // Check norm
                        $this->Patient_val->limit = 1;
                        $patient = $this->Patient_val->read($dt_post['norm'], false);
                        if($patient != []) {
                            // Flag patient
                            $this->Registration->flag_patient($dt_post['norm']);

                            // Count code
                            $select = 'a.code';
                            $where = array();
                            $join = array(
                                array('how_to_enter hte', 'a.how_to_enter_id = hte.id', 'LEFT'),
                            );
                            $order = array('a.code' => 'DESC');
                            $tmp = $this->Registration->select(0, $select, $where, $join, $order);
                            $waktu = date("Ymd",time());

                            if(count($tmp) > 0) {
                                $code = 'REG' . $waktu . str_pad(intval(substr($tmp[0]->code,11)) + 1, 4, '0', STR_PAD_LEFT);

                                // Check if reset is needed
                                if(substr($tmp[0]->code,3,8) != date('Ymd',time())) {
                                    $code = 'REG' . $waktu . '0001';
                                }
                            } else {
                                $code = 'REG' . $waktu . '0001';
                            }
                            $norm = $dt_post['norm'];

                            $dt_registration['code']   = $code;
                            $dt_registration['norm']   = $norm;
                        } else {
                            $this->REST_Return(404, 'Wrong norm!');
                            return;
                        }
                    } else {
                        $this->REST_Return(400, 'norm is required!');
                        return;
                    }

                    // is Icd10?
                    if(isset($dt_post['icd10_id'])) {
                        $dt_icd10 = $this->Icd10->read($dt_post['icd10_id'], false);
                        
                        // Check icd10_id
                        if($dt_icd10 != []) {
                            $dt_registration['icd10_id'] = $dt_post['icd10_id'];
                            $dt_registration['icd10_code'] = $dt_icd10[0]->code;
                            $dt_registration['icd10_name'] = $dt_icd10[0]->name;
                        } else {
                            $this->REST_Return(404, 'Wrong icd10_id!');
                            return;
                        }
                    }
                    // var_dump($norm);
                    // var_dump($dt_registration);
                    // die();
                    $dt_registration['type_visit_id']   = $type_visit_id;
                    $dt_registration['code_booking']   = $dt_post['code_booking'];
                    $id = $this->$model->create($dt_registration);

                    //ADM PASIEN
                        $normint = (int)$norm;
                        if ($normint <= 37774) {
                            //ADM PASIEN BARU BPJS
                            $this->Transaction_administration->create(array(
                                'registration_id' => $id,
                                'costing_id' => 'fc2e9ae3-32d0-4780-b206-85586abd977d',
                                'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                'var_1' => 35000,
                                'date_added' => date('Y-m-d H:i:s')
                            ));
                        } else {
                            if ($patient[0]->is_new == 'Y'){
                                //ADM PASIEN BARU BPJS
                                $this->Transaction_administration->create(array(
                                    'registration_id' => $id,
                                    'costing_id' => '6b9f2fad-dd24-40a3-b5bd-b24e03cfe1c7',
                                    'costing_name' => 'Administrasi Pasien Rawat Jalan (Baru)',
                                    'var_1' => 50000,
                                    'date_added' => date('Y-m-d H:i:s')
                                ));
                                $this->Patient->replace(array('norm'=>$norm,'is_new'=>'N'));
                            } else {
                                //ADM PASIEN LAMA BPJS
                                $this->Transaction_administration->create(array(
                                    'registration_id' => $id,
                                    'costing_id' => 'fc2e9ae3-32d0-4780-b206-85586abd977d',
                                    'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                    'var_1' => 35000,
                                    'date_added' => date('Y-m-d H:i:s')
                                ));
                            }
                        }
                    //ADM PASIEN

                    if(isset($dt_post['destinations'])) {
                        // INIT_REGISTRATION_DESTINATION | registration_id | health_facility_item_id | service_class_id
                        for ($i=0; $i < count($dt_post['destinations']) ; $i++) { 
                            // Check health_facility_item id & doctor_in_charge_id
                            $health_facility_item = $this->Health_facility_item->read($dt_post['destinations'][$i]['health_facility_item_id'], false);
                            $health_facility_item_id = $dt_post['destinations'][$i]['health_facility_item_id'];
                            $health_facility_item_name = $health_facility_item[0]->name;
                            $doctor_in_charge = $this->Employee->read($dt_post['destinations'][$i]['doctor_in_charge_id'], false);
                            if($health_facility_item != [] && $doctor_in_charge != []) {

                                // CHECK IF THE PATIENT IS DOUBLE DESTINATION
                                if($dt_post['type_register_id'] != '3'){
                                    // if ($dt_post['destinations'][0]['insurances'][0]['company_id'] != '' && $dt_post['destinations'][0]['insurances'][0]['company_id'] != null) {
                                    //     $flag_patient_double = $this->Registration->flag_patient_double_insurance($norm,$dt_post['destinations'][$i]['health_facility_item_id'],$dt_post['destinations'][$i]['doctor_in_charge_id'],$dt_post['destinations'][0]['insurances'][0]['company_id']);
                                    // } else {
                                        $flag_patient_double = $this->Registration->flag_patient_double($norm,$doctor_in_charge[0]->health_facility_item_id,$dt_post['destinations'][$i]['doctor_in_charge_id']);
                                    // }
                                    if ($flag_patient_double == 'N') {
                                        $this->REST_Return(422, 'Pasien Sudah di Daftarkan ke '.$health_facility_item[0]->name.', '.$doctor_in_charge[0]->fullname.'. Pada hari ini!');
                                        return;
                                    }

                                    $flag_patient_appointment = $this->Registration->flag_appointment($norm,$doctor_in_charge[0]->health_facility_item_id,$dt_post['destinations'][$i]['doctor_in_charge_id']);
                                    
                                    if ($flag_patient_appointment == 'MJKN') {
                                        $this->REST_Return(422, 'Pasien Sudah mendaftar MJKN Pada hari ini');
                                        return;
                                    }
                                    // if ($dt_post['destinations'][0]['jeniskunjungan'] != 2 && $dt_post['destinations'][0]['insurances'][0]['company_id'] == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                                    //     $flag_check_bpjs_feasible = $this->Registration->check_bpjs_feasible($norm);
                                    //     if ($flag_check_bpjs_feasible == 0) {
                                    //         $this->REST_Return(422, 'Pasien Sudah di Daftarkan kurang dari 7 hari sebelumnya (fragmentasi tl)');
                                    //         return;
                                    //     }
                                    // }
                                } else {
                                }
                                // CHECK IF THE PATIENT IS NEW
                                $is_new = $this->Registration->flag_patient_destination($norm,$doctor_in_charge[0]->health_facility_item_id);

                                // GET QUEUE NUMBER
                                if($dt_post['destinations'][$i]['appointment_id'] != '') {
                                    $this->Appointment->whereFields['e.institute_branch_id'] = $institute_branch_id;
                                    $appointment = $this->Appointment->read($dt_post['destinations'][$i]['appointment_id'],false);
                                    // Check appointment_id
                                    if($appointment != []) {
                                        $queue['doctor_schedule_item_id'] = $appointment[0]->doctor_schedule_item_id;
                                        $queue['queue_code'] = $appointment[0]->code;
                                        $queue['queue'] = $appointment[0]->queue;
                                        // $queue = $this->Queue->get_queue_number($health_facility_item_id,$doctor_in_charge_id,$dt_post['destinations'][$i]['appointment_id']); 
                                    } else {
                                        $this->REST_Return(404, 'Wrong appointment_id!');
                                        return;
                                    }
                                } else if($dt_post['type_register_id'] == '3' || $dt_post['type_register_id'] == '4') { // No queue if emergency or inpatient
                                    $queue = $this->Queue->get_queue_number($doctor_in_charge[0]->health_facility_item_id,$dt_post['destinations'][$i]['doctor_in_charge_id'],'0',true);
                                } else { 
                                    $queue = $this->Queue->get_queue_number($doctor_in_charge[0]->health_facility_item_id,$dt_post['destinations'][$i]['doctor_in_charge_id']);
                                }

                                // Checking if error happen
                                if(isset($queue['error0'])) { // 'Doctor schedule not found!'
                                    $this->REST_Return(404, $queue['error0']);
                                    return;
                                } else if(isset($queue['error1'])) { // 'No schedule today!'
                                    $this->REST_Return(404, $queue['error1']);
                                    return;
                                } else if(isset($queue['error2'])) { // 'You missed the schedule!'
                                    $this->REST_Return(422, $queue['error2']);
                                    return;
                                } else if(isset($queue['error3'])) { // 'The quota is full for today!'
                                    $this->REST_Return(422, $queue['error3']);
                                    return;
                                }
                                // var_dump($queue);
                                // die();

                                if ($dt_post['type_register_id'] == '1'){ // RAWAT JALAN
                                    if ($dt_post['code_booking'] == "") {
                                        $this->load->library("transaction");
                                        //NEW COUNTER KODEBOOKING
                                        $codecounter_book = array(
                                            'var_1' => '01',
                                            'var_2' => date('ymd'),
                                            'var_3' => 'BOOKPOLI',
                                            'var_4' => 1,
                                            'var_5' => date('d')
                                        );
                                        $checkcounter_book = $this->transaction->counter($codecounter_book);
                                        $kodebooking = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue'], 4, "0", STR_PAD_LEFT);
                                        if ($checkcounter_book['counter_queue'] == '0') {
                                            $this->Counter_queue->replace(array(
                                                'var_1' => '01',
                                                'var_2' => date('ymd'),
                                                'var_3' => 'BOOKPOLI',
                                                'var_4' => 1,
                                                'var_5' => date('d'),
                                                'counter_queue' => 1
                                            ));
                                            $kodebooking = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue']+1, 4, "0", STR_PAD_LEFT);
                                        }
                                        $dt_post['code_booking'] = $kodebooking;
                                        $this->$model->replace(array('id'=>$id,'code_booking'=>$kodebooking));
                                    }
                                }

                                $dt_registration_destination = array(
                                    'registration_id'               => $id,
                                    'health_facility_item_id'       => isset($health_facility_item_id) ? $health_facility_item_id : $doctor_in_charge[0]->health_facility_item_id,
                                    'doctor_in_charge_id'           => isset($doctor_in_charge_id) ? $doctor_in_charge_id : $dt_post['destinations'][$i]['doctor_in_charge_id'],
                                    'doctor_schedule_item_id'       => $queue['doctor_schedule_item_id'],
                                    'register_date'                 => date("Y-m-d H:i:s",time()),
                                    'checkin_date'                  => date("Y-m-d H:i:s",time()),
                                    'user_added'                    => $user_id,
                                    'is_new'                        => $is_new,
                                    'code_queue'                    => $queue['queue_code'], // if emergency or inpatient queue 0
                                    'queue'                         => $queue['queue'], // if emergency or inpatient queue 0
                                    'type_emergency_id'             => isset($dt_post['destinations'][$i]['type_emergency_id']) ? $dt_post['destinations'][$i]['type_emergency_id'] : '',
                                    'type_emergency_case_id'        => isset($dt_post['destinations'][$i]['type_emergency_case_id']) ? $dt_post['destinations'][$i]['type_emergency_case_id'] : '',
                                    'case_location'                 => isset($dt_post['destinations'][$i]['case_location']) ? $dt_post['destinations'][$i]['case_location'] : '',
                                    'name_location'                 => isset($dt_post['destinations'][$i]['name_location']) ? $dt_post['destinations'][$i]['name_location'] : '',
                                );

                                if ($dt_post['type_register_id'] == '1'){ // RAWAT JALAN

                                    $dataPoli = $this->Health_facility_item->read($doctor_in_charge[0]->health_facility_item_id,false)[0];
                                    $dataDokter = $this->Employee->read($dt_post['destinations'][$i]['doctor_in_charge_id'],false)[0];
                                    $dataSchedule = $this->Doctor_schedule_item->read($queue['doctor_schedule_item_id'],false)[0];
                                    // $jeniskunjungan = $dataPoli->bride_bpjs != $dt_post['poliRujukan'] ? '2' : $dt_post['destinations'][0]['jeniskunjungan'];
                                    $jeniskunjungan = $dt_post['destinations'][0]['jeniskunjungan'];
                                    // $jeniskunjungan = 2;
                                    // Tambah Antrean BPJS RIN2024111800000003
                                    // JKN
                                    $fullnamepatient = $patient[0]->name;
                                    if ($dt_post['destinations'][0]['insurances'][0]['company_id'] == '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                                        $data_antrean = array(
                                            'kodebooking'=> $dt_post['code_booking'],
                                            'jenispasien'=> ($dt_post['destinations'][0]['insurances'][0]['number_insurance'] != '') ? "JKN" : "NON JKN",
                                            'nomorkartu'=> $dt_post['destinations'][0]['insurances'][0]['number_insurance'],
                                            'nik'=> $patient[0]->idcard,
                                            'nohp'=> $patient[0]->phone ? $patient[0]->phone : $dt_post['patient']['phone'],
                                            'kodepoli'=> $dataPoli->bride_bpjs,
                                            'namapoli'=> $dataPoli->bride_bpjs_name,
                                            'pasienbaru'=> $is_new == 'Y' ? 1 : 0,
                                            'norm'=> $norm,
                                            'tanggalperiksa'=> date('Y-m-d'),
                                            'kodedokter'=> $dataDokter->bride_bpjs,
                                            'namadokter'=> $dataDokter->fullname,
                                            'jampraktek' => substr($dataSchedule->hour_start, 0, -3).'-'.substr($dataSchedule->hour_end, 0, -3),
                                            'jeniskunjungan' => $jeniskunjungan,
                                            'nomorreferensi' => $dt_post['noKunjungan'] != "" ? $dt_post['noKunjungan'] : $dt_post['destinations'][0]['insurances'][0]['noKunjungan'],
                                            'nomorantrean'=> $queue['queue_code'].'-'.$queue['queue'],
                                            'angkaantrean'=> $queue['queue'],
                                            'estimasidilayani'=> $this->get_estimasi($dataPoli->bride_bpjs,date('Y-m-d') . ' ' . substr(substr($dataSchedule->hour_start, 0, -3).'-'.substr($dataSchedule->hour_end, 0, -3).'',0,5).'+'.(5*$queue['queue']).' minutes'),
                                            'sisakuotajkn'=> (int) $dataSchedule->quota_jkn-$queue['queue'],
                                            'kuotajkn'=> (int) $dataSchedule->quota_jkn,
                                            'sisakuotanonjkn'=> (int) $dataSchedule->quota_jkn,
                                            'kuotanonjkn'=> (int) $dataSchedule->quota_bo,
                                            'keterangan'=> 'Peserta harap 60 menit lebih awal guna pencatatan administrasi.',
                                        );
                                    } 
                                    // var_dump($data_antrean);die();
                                    $this->load->library('Vclaim_antrol','vclaim_antrol');
                                    $tambah_antrean = $this->vclaim_antrol->request("/antrean/add", "POST", json_encode($data_antrean));
                                    $json_tambah_antrean = json_decode($tambah_antrean, true);
                                    $json_tambah_antrean = json_decode($json_tambah_antrean);
                                    // jika gagal tambah antrean bpjs
                                    if($json_tambah_antrean->metadata->code != 200) {
                                        // rollback
                                        if ($json_tambah_antrean->metadata->message != 'Terdapat duplikasi Kode Booking') {
                                            $this->db->trans_rollback();
                                            $this->REST_Return(422, $json_tambah_antrean->metadata->message, $json_tambah_antrean->metadata->message);
                                            return;
                                        }
                                    } 
                                    // BIKIN SEP
                                        $noSurat = '';
                                        $this->load->library('Vclaim_sep','vclaim_sep');
                                        if ($jeniskunjungan == '1'|| $jeniskunjungan == '4') {
                                            if ($jeniskunjungan == '1') {
                                                $asalRujukan = '1';
                                                $dataRujukanVclaim = $this->vclaim_sep->request("/Rujukan/".$data_antrean['nomorreferensi'], "GET");
                                                $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                                                if ($dataRujukanVclaim['metaData']['code'] == "201") {
                                                    $this->db->trans_rollback();
                                                    $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                                                    return;
                                                }
                                            } else if ($jeniskunjungan == '4') {
                                                $asalRujukan = '2';
                                                $dataRujukanVclaim = $this->vclaim_sep->request("/Rujukan/RS/".$data_antrean['nomorreferensi'], "GET");
                                                $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                                                if ($dataRujukanVclaim['metaData']['code'] == "201") {
                                                    $this->db->trans_rollback();
                                                    $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                                                    return;
                                                }
                                            }
                                            $noMR = $norm;
                                            $klsRawat = $dataRujukanVclaim['response']['rujukan']['peserta']['hakKelas']['kode'];
                                            $tglRujukan = $dataRujukanVclaim['response']['rujukan']['tglKunjungan'];
                                            $noRujukan = $dataRujukanVclaim['response']['rujukan']['noKunjungan'];
                                            $ppkRujukan = $dataRujukanVclaim['response']['rujukan']['provPerujuk']['kode'];
                                            $catatan = '';
                                            $diagAwal = $dataRujukanVclaim['response']['rujukan']['diagnosa']['kode'];
                                            $tujuanKunj = '0';
                                            $flagProcedure = '';
                                            $kdPenunjang = '';
                                            $assesmentPel = '';
                                        } else if ($jeniskunjungan == '3') {
                                            $dataRujukanVclaim = $this->vclaim_sep->request("/RencanaKontrol/noSuratKontrol/".$data_antrean['nomorreferensi'], "GET");
                                            $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                                            if ($dataRujukanVclaim['metaData']['code'] == "201") {
                                                $this->db->trans_rollback();
                                                $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                                                return;
                                            }
                                            // var_dump($dataRujukanVclaim);
                                            $noMR = $norm;
                                            $klsRawat = $dataRujukanVclaim['response']['sep']['peserta']['hakKelas'];
                                            $tglRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['tglRujukan'];
                                            $noRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['noRujukan'];
                                            $ppkRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['kdProviderPerujuk'];
                                            $asalRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['asalRujukan'];
                                            $catatan = '';
                                            $diagAwal = preg_replace('/\s+/', '', strtok($dataRujukanVclaim['response']['sep']['diagnosa'], '-'));
                                            $noSurat = $data_antrean['nomorreferensi'];
                                            if($dataRujukanVclaim['response']['sep']['provPerujuk']['kdProviderPerujuk'] == '0070R032'){
                                                $tujuanKunj = '0';
                                                $noRujukan = $dataRujukanVclaim['response']['sep']['noSep'];
                                                $tglRujukan = $dataRujukanVclaim['response']['tglRencanaKontrol'];
                                                $flagProcedure = '';
                                                $kdPenunjang = '';
                                                $assesmentPel = '';
                                            } else {
                                                $tujuanKunj = '2';
                                                $flagProcedure = '';
                                                $kdPenunjang = '';
                                                $assesmentPel = '5';
                                            }
                                        } else if ($jeniskunjungan == '2') {
                                            $dataRujukanVclaim = $this->vclaim_sep->request("/RencanaKontrol/noSuratKontrol/".$data_antrean['nomorreferensi'], "GET");
                                            $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                                            if ($dataRujukanVclaim['metaData']['code'] == "201") {
                                                $this->db->trans_rollback();
                                                $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                                                return;
                                            }
                                        }
                                        //SET DATA SEP
                                            $noKartu = $data_antrean['nomorkartu'];
                                            $tglSep = date("Y-m-d");
                                            $ppkPelayanan = '0070R032';
                                            $jnsPelayanan = '2';
                                            $katarak = '0';
                                            $kontrol = '';
                                            $eksekutif = '0';
                                            $klsRawatNaik = '';
                                            $pembiayaan = '';
                                            $penanggungJawab = '';
                                            $cob = '';
                                            $lakaLantas = '0';
                                            $penjamin = '';
                                            $tglKejadian = '';
                                            $suplesi = '';
                                            $noSepSuplesi = '';
                                            $kdPropinsi = '';
                                            $kdKabupaten = '';
                                            $kdKecamatan = '';
                                            $keteranganKll = '';
                                            $noLP = '';
                                            $tujuan = $dataPoli->bride_bpjs;
                                            $kodeDPJP = $dataDokter->bride_bpjs;
                                            $dpjpLayan = $dataDokter->bride_bpjs;
                                            $noTelp = $data_antrean['nohp'];
                                            $user = 'Administrasi';
                                            $DataPostSep = array();
                                        //SET DATA SEP
                                        if($klsRawat == '1'){
                                            $hakkelas = '3';
                                        } else if ($klsRawat == '2') {
                                            $hakkelas = '4';
                                        } else if ($klsRawat == '3') {
                                            $hakkelas = '5';
                                        }
                                        $DataPostSep['request']['t_sep'] = array(
                                            'noKartu'             => $noKartu,
                                            'tglSep'              => $tglSep,
                                            'ppkPelayanan'        => $ppkPelayanan,
                                            'jnsPelayanan'        => $jnsPelayanan,
                                            'klsRawat'            => 
                                            array(
                                            'klsRawatHak'       => $klsRawat,
                                            'klsRawatNaik'      => $hakkelas == $klsRawatNaik ? "" : $klsRawatNaik,
                                            'pembiayaan'        => $pembiayaan,
                                            'penanggungJawab'   => $penanggungJawab
                                            ),
                                            'noMR'                => $noMR,
                                            'rujukan'             => 
                                            array(
                                            'asalRujukan'       => $asalRujukan,
                                            'tglRujukan'        => $tglRujukan,
                                            'noRujukan'         => $noRujukan,
                                            'ppkRujukan'        => $ppkRujukan
                                            ),
                                            'catatan'             => $catatan,
                                            'diagAwal'            => $diagAwal,
                                            'poli'                => 
                                            array(
                                            'tujuan'            => $tujuan,
                                            'eksekutif'         => $eksekutif
                                            ),
                                            'cob'                 =>
                                            array(
                                                'cob'             => $cob
                                            ),
                                            'katarak'             =>
                                            array(
                                            'katarak'           => $katarak
                                            ),
                                            'jaminan'             =>
                                            array(
                                            'lakaLantas'        => $lakaLantas,
                                            'noLP'              => $noLP,
                                            'penjamin'          =>
                                            array(
                                                'tglKejadian'     => $tglKejadian,
                                                'keterangan'      => $keteranganKll,
                                                'suplesi'         => 
                                                array(
                                                'suplesi'       => $suplesi,
                                                'noSepSuplesi'  => $noSepSuplesi,
                                                'lokasiLaka'    =>
                                                array(
                                                    'kdPropinsi'  => $kdPropinsi,
                                                    'kdKabupaten' => $kdKabupaten,
                                                    'kdKecamatan' => $kdKecamatan
                                                ),
                                                ),
                                            ),
                                            ),
                                            'tujuanKunj'          => $tujuanKunj,
                                            'flagProcedure'       => $flagProcedure,
                                            'kdPenunjang'         => $kdPenunjang,
                                            'assesmentPel'        => $assesmentPel,
                                            'skdp'                => 
                                            array(
                                            'noSurat'           => $noSurat,
                                            'kodeDPJP'          => $noSurat == "" ? "" : $kodeDPJP
                                            ),
                                            'dpjpLayan'           => $kodeDPJP,
                                            'noTelp'              => $noTelp,
                                            'user'                => $user,
                                        );
                                        $hasil = $this->vclaim_sep->request("/SEP/2.0/insert", "POST", json_encode($DataPostSep));
                                        $d = json_decode($hasil, true);
                                        $noSepMjkn = '';
                                        // var_dump($d);
                                        // echo json_encode($DataPostSep); die();
                                        if ($d['metaData']['code'] == "200"){
                                            $noSepMjkn = $d['response']['sep']['noSep'];
                                        } else {
                                            // var_dump(array('registration_destination_id'=>$reg_destination,'sep'=>$d->metaData->code));
                                            //CEK DATA JIKA SUDAH TERBUAT SEP NAMUN GAGAL REGIST KE SIMRS
                                                $hasilCekSEP = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".date("Y-m-d")."/JnsPelayanan/2", "GET");
                                                $dcekSep = json_decode($hasilCekSEP, true);
                                                if ($dcekSep['metaData']['code'] == "200"){
                                                    for ($i=0; $i < count($dcekSep['response']['sep']); $i++) { 
                                                        if ($dcekSep['response']['sep'][$i]['noKartu'] == $data_antrean['nomorkartu']) {
                                                            $noSepMjkn = $dcekSep['response']['sep'][$i]['noSep'];
                                                            $d = $dcekSep['response']['sep'][$i];
                                                        }
                                                    }
                                                    if ($noSepMjkn == '') {
                                                        $d = json_decode($d, true);
                                                        $response_data = $d;
                                                        $this->REST_Return(422, 'FAILED '.$d['metaData']['message'], $DataPostSep);
                                                        return;
                                                    } 
                                                } else {
                                                    $d = json_decode($d, true);
                                                    $response_data = $d;
                                                    $this->REST_Return(422, 'FAILED '.$d['metaData']['message'], $DataPostSep);
                                                    return;
                                                }
                                            //CEK DATA JIKA SUDAH TERBUAT SEP NAMUN GAGAL REGIST KE SIMRS
                                        }
                                    // BIKIN SEP
                                }

                                // var_dump($dt_registration_destination);
                                // die();
                                $reg_destination = $this->Registration_Destination->create($dt_registration_destination);
                                
                                if(isset($dt_post['destinations'][$i]['insurances'])) {
                                    if(count($dt_post['destinations'][$i]['insurances']) > 0) {
                                        // INIT_REGISTRATION_INSURANCE | registration_id | company_id | service_class_id
                                        for ($a=0; $a < count($dt_post['destinations'][$i]['insurances']) ; $a++) { 
                                            if($dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] == 1) {
                                                if ($dt_post['type_register_id'] == '3') {
                                                    $dt_registration_insurance = array(
                                                        'registration_destination_id'   => $reg_destination,
                                                        'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                        'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                        'is_primary'                    => 1,
                                                    );
                                                } else {
                                                    $dt_registration_insurance = array(
                                                        'registration_destination_id'   => $reg_destination,
                                                        'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                        'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                        'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                    );
                                                }
                                                $this->Registration_Insurance->replace($dt_registration_insurance);
                                                $dt_insurance = array(
                                                    'norm'                          => $norm,
                                                    'institute_branch_id'           => $institute_branch_id,
                                                    'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                    'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                    'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                );
                                                $this->Insurance->replace($dt_insurance);
                                            } else {
                                                $company = $this->Company->read($dt_post['destinations'][$i]['insurances'][$a]['company_id'], false);
                                                $service_class = $this->Service_class->read($dt_post['destinations'][$i]['insurances'][$a]['service_class_id'], false);
                                                if($company != [] && $service_class != []) {
                                                    if ($dt_post['type_register_id'] == '3') {
                                                        $dt_registration_insurance = array(
                                                            'registration_destination_id'   => $reg_destination,
                                                            'company_id'                    => $dt_post['destinations'][$i]['insurances'][$a]['company_id'],
                                                            'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                            'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                            'expired'                       => isset($dt_post['destinations'][$i]['insurances'][$a]['expired']) ? $dt_post['destinations'][$i]['insurances'][$a]['expired'] : date('Y-m-d'),
                                                            'is_primary'                    => 1,
                                                            // 'noKunjungan'                   => isset($dt_post['destinations'][$i]['insurances'][$a]['noKunjungan']) ? $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'] : '',
                                                            'noKunjungan'                   => $dt_post['noKunjungan'] != "" ? $dt_post['noKunjungan'] : $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'],
                                                            'service_class_id'              => $dt_post['destinations'][$i]['insurances'][$a]['service_class_id'],
                                                            'service_class_name'            => $service_class[0]->name,
                                                        );
                                                    } else {
                                                        $dt_registration_insurance = array(
                                                            'registration_destination_id'   => $reg_destination,
                                                            'company_id'                    => $dt_post['destinations'][$i]['insurances'][$a]['company_id'],
                                                            'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                            'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                            'expired'                       => isset($dt_post['destinations'][$i]['insurances'][$a]['expired']) ? $dt_post['destinations'][$i]['insurances'][$a]['expired'] : '',
                                                            'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                            // 'noKunjungan'                   => isset($dt_post['destinations'][$i]['insurances'][$a]['noKunjungan']) ? $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'] : '',
                                                            'noKunjungan'                   => $dt_post['noKunjungan'] != "" ? $dt_post['noKunjungan'] : $dt_post['destinations'][$i]['insurances'][$a]['noKunjungan'],
                                                            'service_class_id'              => $dt_post['destinations'][$i]['insurances'][$a]['service_class_id'],
                                                            'service_class_name'            => $service_class[0]->name,
                                                        );
                                                    }
                                                    $this->Registration_Insurance->replace($dt_registration_insurance);
                                                    $dt_insurance = array(
                                                        'norm'                          => $norm,
                                                        'institute_branch_id'           => $institute_branch_id,
                                                        'company_id'                    => $dt_post['destinations'][$i]['insurances'][$a]['company_id'],
                                                        'insurance_id'                  => isset($dt_post['destinations'][$i]['insurances'][$a]['insurance_id']) ? $dt_post['destinations'][$i]['insurances'][$a]['insurance_id'] : '',
                                                        'number_insurance'              => isset($dt_post['destinations'][$i]['insurances'][$a]['number_insurance']) ? $dt_post['destinations'][$i]['insurances'][$a]['number_insurance'] : '',
                                                        'expired'                       => isset($dt_post['destinations'][$i]['insurances'][$a]['expired']) ? $dt_post['destinations'][$i]['insurances'][$a]['expired'] : '',
                                                        'is_primary'                    => isset($dt_post['destinations'][$i]['insurances'][$a]['is_primary']) ? $dt_post['destinations'][$i]['insurances'][$a]['is_primary'] : '',
                                                        'service_class_id'              => $dt_post['destinations'][$i]['insurances'][$a]['service_class_id'],
                                                    );
                                                    $this->Insurance->replace($dt_insurance);
                                                } else {
                                                    $this->REST_Return(404, 'There is no company_id ' . $dt_post['destinations'][$i]['insurances'][$a]['company_id'] . ' or service_class_id ' . $dt_post['destinations'][$i]['insurances'][$a]['service_class_id']);
                                                    return;
                                                }
                                            }
                                        }
                                    } else {
                                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', 'Insurance field is required');
                                        return;
                                    }
                                } else {
                                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', 'Insurance field is required');
                                    return;
                                }
                            } else {
                                $this->REST_Return(404, 'There is no health_facility_item ' . $dt_post['destinations'][$i]['health_facility_item_id'] . ' or doctor_in_charge ' . $dt_post['destinations'][$i]['doctor_in_charge_id']);
                                return;
                            }
                        }
                    }
    
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $this->db->where('registration_destination_id', $reg_destination);
                        $this->db->where('insurance_id',2);
                        $this->db->where('is_primary',1);
                        $this->db->limit(1);
                        $this->db->update('registration_insurance', array('sep'=>$noSepMjkn,'sep_json'=>json_encode($d)));
                        // $response_data = array(
                        //     'registration_destination_id'     => $reg_destination,
                        //     'registration_id'     => $reg_destination
                        // );
                        // $this->REST_Return(201, 'SUCCESS', $response_data);
                        $response_data = array(
                            'registration_id'     => $id,
                            'queue'               => $queue['queue_code'].'-'.$queue['queue'],
                            'sep'                 => $noSepMjkn,
                            'norm'                => $noMR,  
                            'destination'         => $dataPoli->bride_bpjs_name,
                            'birth_date'          => $patient[0]->birth_date,
                            'sex'                 => $patient[0]->sex == '1' ? 'L' : 'P',
                            'name'                => $fullnamepatient,
                            'dpjp'                => $dataDokter->fullname,
                            'test'                => $DataPostSep,
                            'code_booking'        => null
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                        return; 
                    }
                } else {
                    $this->REST_Return(404, 'Wrong how_to_enter_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_inpatient_post() {
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
        $this->form_validation->set_rules($this->create_inpatient_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        /* Start Transaction */
        $this->db->trans_start();

        $norm = $dt_post['norm'];
        $icd10_id = $dt_post['icd10_id'];
        $type_register_id = $dt_post['type_register_id'];
        $how_to_enter_id = $dt_post['how_to_enter_id'];
        $type_visit_id = $dt_post['type_visit_id'];
        $is_surgery = $dt_post['is_surgery'];
        $is_internal = $dt_post['is_internal'] ?? 0;

        $select = 'a.norm, a.is_new, a.sex, h.name as alias, a.name';
        $where = array();
        $join = array(
            array('helper h', 'a.alias = h.code AND h.variable="alias"', 'LEFT'),
        );
        $patient = $this->Patient->select($norm, $select, $where, $join);

        if (count($patient) == 0) {
            $this->REST_Return(404, 'there is no patient with norm ' . $norm);
            return;
        }

        $patient_is_new = $patient[0]->is_new;
        $patient_sex = $patient[0]->sex;
        $patient_alias = $patient[0]->alias;
        $patient_name = $patient[0]->name;

        $select = 'a.id, a.code, a.name';
        $icd10 = $this->Icd10->select($icd10_id, $select);

        if (count($icd10) == 0) {
            $this->REST_Return(404, 'there is no icd10 with icd10_id ' . $icd10_id);
            return;
        }

        $icd10_code = $icd10[0]->code;
        $icd10_name = $icd10[0]->name;

        // Flag patient
        $this->$model->flag_patient($norm);

        // Count code
        $select = 'a.code';
        $where = array();
        $join = array(
            array('how_to_enter hte', 'a.how_to_enter_id = hte.id', 'LEFT'),
        );
        $order = array('a.code' => 'DESC');
        $tmp = $this->$model->select(0, $select, $where, $join, $order);

        $now = date("Ymd",time());

        if(count($tmp) > 0) {
            $code = 'REG' . $now . str_pad(intval(substr($tmp[0]->code,11)) + 1, 4, '0', STR_PAD_LEFT);

            // Check if reset is needed
            if(substr($tmp[0]->code,3,8) != date('Ymd',time())) {
                $code = 'REG' . $now . '0001';
            }
        } else {
            $code = 'REG' . $now . '0001';
        }

        $dt_registration = array(
            'code'              => $code,
            'norm'              => $norm,
            'icd10_id'          => $icd10_id,
            'icd10_code'        => $icd10_code,
            'icd10_name'        => $icd10_name,
            'type_register_id'  => $type_register_id,
            'how_to_enter_id'   => $how_to_enter_id,
            'type_visit_id'     => $type_visit_id,
            'register_date'     => date("Y-m-d H:i:s", time())
        );

        $id = $this->$model->create($dt_registration);

        // $normint = (int) $norm;
        // if ($normint <= 37774) {
        //     $this->Transaction_administration->create(array(
        //         'registration_id' => $id,
        //         'costing_id' => 'a6bff965-aadc-4554-b4ef-cc9d534aeb30',
        //         'costing_name' => 'ADMINISTRASI PASIEN LAMA',
        //         'var_1' => 35000,
        //         'date_added' => date('Y-m-d H:i:s')
        //     ));
        // } else {
        //     if ($patient_is_new == 'Y'){
        //         $this->Transaction_administration->create(array(
        //             'registration_id' => $id,
        //             'costing_id' => 'c78b8ce0-7983-4077-a12a-291bdae71016',
        //             'costing_name' => 'ADMINISTRASI PASIEN BARU',
        //             'var_1' => 50000,
        //             'date_added' => date('Y-m-d H:i:s')
        //         ));
        //         $this->Patient->replace(array('norm'=>$norm,'is_new'=>'N'));
        //     //PASIEN LAMA
        //     } else {
        //         $this->Transaction_administration->create(array(
        //             'registration_id' => $id,
        //             'costing_id' => 'a6bff965-aadc-4554-b4ef-cc9d534aeb30',
        //             'costing_name' => 'ADMINISTRASI PASIEN LAMA',
        //             'var_1' => 35000,
        //             'date_added' => date('Y-m-d H:i:s')
        //         ));
        //     }
        // }

        $this->Transaction_administration->create(array(
            'registration_id' => $id,
            'costing_id' => '413a9aa3-dfdc-4e97-b01e-efd44773d229',
            'costing_name' => 'SERVICE CHARGE',
            'var_1' => 0,
            'date_added' => date('Y-m-d H:i:s')
        ));

        $destinations_arr = $dt_post['destination'];
        
        for ($i=0; $i < count($destinations_arr) ; $i++) { 
            $doctor_in_charge_id = $destinations_arr[$i]['doctor_in_charge_id'];
            $bed_id = $destinations_arr[$i]['bed_id'];
            
            if ($doctor_in_charge_id == null) {
                $this->REST_Return(400, 'doctor_in_charge_id in destinations is required');
                return;
            }

            $select = 'a.id, a.fullname';
            $employee = $this->Employee->select($doctor_in_charge_id, $select);

            if (count($employee) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
                return;
            }

            $doctor_in_charge_name = $employee[0]->fullname;

            $norm_mother = $dt_post['norm_mother'];

            if ($bed_id == null) {
                if ($norm_mother == null) {
                    $this->REST_Return(400, 'Mohon isi NORM ibu bayi');
                    return;
                }

                $select = 'a.id, b.id as bed_id, rd.health_facility_item_id';
                $where = array(
                    'a.norm' => $norm_mother,
                    'rd.is_checkout' => 0
                );
                $join = array(
                    array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
                    array('bed b', 'rd.bed_id = b.id', 'INNER'),
                );
                $order = array('a.register_date' => 'DESC');
                $registration_mother = $this->Registration->select(0, $select, $where, $join, $order);

                if (count($registration_mother) == 0) {
                    $this->REST_Return(409, 'Mohon menaikan ibu bayi terlebih dahulu');
                    return;
                }

                $bed_id = $registration_mother[0]->bed_id;
            }

            $select = 'a.id, a.name, a.room_id, a.type_status_bed_id';
            $where = array('a.is_delete' => 0);
            $bed = $this->Bed->select($bed_id, $select, $where);

            if (count($bed) == 0) {
                $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
                return;
            }

            $bed_name = $bed[0]->name;
            $bed_status = $bed[0]->type_status_bed_id;
            $room_id = $bed[0]->room_id;

            if ($bed_status != 1 && $norm_mother == null) {
                $this->REST_Return(409, 'bed with id ' . $bed_id . ' is currently not available');
                return;
            }

            $select = 'a.id, a.name, a.costing_id, a.service_class_id, a.health_facility_item_id, a.number';
            $where = array('a.is_delete' => 0);
            $room = $this->Room->select($room_id, $select, $where);

            $room_name = $room[0]->name;
            $room_number = $room[0]->number;
            $room_costing_id = $room[0]->costing_id;
            $room_service_class_id = $room[0]->service_class_id;

            $health_facility_item_id = $room[0]->health_facility_item_id;

            $select = 'a.id, a.name';
            $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

            $health_facility_item_name = $health_facility_item[0]->name;

            $flag_patient_double = $this->Registration->flag_patient_double($norm,$health_facility_item_id,$doctor_in_charge_id);
            if ($flag_patient_double == 'N') {
                $this->REST_Return(422, 'Pasien Sudah di Daftarkan ke '. $health_facility_item_name . ', ' . $doctor_in_charge_name . '. Pada hari ini!');
                return;
            }

            // CHECK IF THE PATIENT IS NEW
            $is_new = $this->Registration->flag_patient_destination($norm, $health_facility_item_id);

            $dt_registration_destination = array(
                'registration_id'               => $id,
                'health_facility_item_id'       => $health_facility_item_id,
                'bed_id'                        => $bed_id,
                'doctor_in_charge_id'           => $doctor_in_charge_id,
                'register_date'                 => date("Y-m-d H:i:s",time()),
                'checkin_date'                  => date("Y-m-d H:i:s",time()),
                'user_added'                    => $user_id,
                'is_new'                        => $is_new,
                'code_queue'                    => NULL,
                'queue'                         => 0,
                'is_transfer'                   => 1,
            );

            $registration_destination_id = $this->Registration_Destination->create($dt_registration_destination);
            
            $insurances_arr = $destinations_arr[$i]['insurances'];

            for ($a=0; $a < count($insurances_arr) ; $a++) { 
                $insurance_id = $insurances_arr[$a]['insurance_id'];
                $number_insurance = $insurances_arr[$a]['number_insurance'];
                $is_primary = $insurances_arr[$a]['is_primary'];

                if ($insurance_id == null) {
                    $this->REST_Return(400, 'field insurance_id in insurances is required');
                    return;
                }

                if ($number_insurance == null) {
                    $this->REST_Return(400, 'field number_insurance in insurances is required');
                    return;
                }

                if ($is_primary == null) {
                    $this->REST_Return(400, 'field is_primary in insurances is required');
                    return;
                }

                if($insurance_id == 1) {
                    $dt_registration_insurance = array(
                        'registration_destination_id'   => $registration_destination_id,
                        'insurance_id'                  => $insurance_id,
                        'number_insurance'              => $number_insurance,
                        'is_primary'                    => $is_primary,
                    );

                    $this->Registration_Insurance->replace($dt_registration_insurance);

                    $dt_insurance = array(
                        'norm'                          => $norm,
                        'institute_branch_id'           => $institute_branch_id,
                        'insurance_id'                  => $insurance_id,
                        'number_insurance'              => $number_insurance,
                        'is_primary'                    => $is_primary,
                    );

                    $this->Insurance->replace($dt_insurance);
                } else {
                    $company_id = $insurances_arr[$a]['company_id'];
                    $service_class_id = $insurances_arr[$a]['service_class_id'];
                    $expired = $insurances_arr[$a]['expired'];

                    if ($company_id == null) {
                        $this->REST_Return(400, 'field company_id in insurances is required');
                        return;
                    }

                    if ($service_class_id == null) {
                        $this->REST_Return(400, 'field service_class_id in insurances is required');
                        return;
                    }
                
                    $select = 'a.id';
                    $company = $this->Company->select($company_id, $select);

                    if (count($company) == 0) {
                        $this->REST_Return(404, 'there is no company with id ' . $doctor_in_charge_id);
                        return;
                    }

                    $select = 'a.id, a.name';
                    $service_class = $this->Service_class->select($service_class_id, $select);

                    if (count($service_class) == 0) {
                        $this->REST_Return(404, 'there is no service_class with id ' . $doctor_in_charge_id);
                        return;
                    }

                    $service_class_name = $service_class[0]->name;

                    $dt_registration_insurance = array(
                        'registration_destination_id'   => $registration_destination_id,
                        'company_id'                    => $company_id,
                        'insurance_id'                  => $insurance_id,
                        'number_insurance'              => $number_insurance,
                        'expired'                       => $expired,
                        'is_primary'                    => $is_primary,
                        'service_class_id'              => $service_class_id,
                        'service_class_name'            => $service_class_name,
                    );

                    $this->Registration_Insurance->replace($dt_registration_insurance);

                    $dt_insurance = array(
                        'norm'                          => $norm,
                        'institute_branch_id'           => $institute_branch_id,
                        'company_id'                    => $company_id,
                        'insurance_id'                  => $insurance_id,
                        'number_insurance'              => $number_insurance,
                        'expired'                       => $expired,
                        'is_primary'                    => $is_primary,
                        'service_class_id'              => $service_class_id,
                    );

                    $this->Insurance->replace($dt_insurance);
                }
            }
            
            // Set bed status to filled
            $dt_bed = array(
                'type_status_bed_id'  => 2,
            );

            $this->Bed->update($bed_id, $dt_bed);
 
            // INIT_TRANSACTION_BED
            $dt_transaction_bed = array(
                'registration_destination_id'   => $registration_destination_id,
                'bed_id'                        => $bed_id,
                'bed_name'                      => $bed_name,
                'costing_id'                    => $room_costing_id,
                'service_class_id'              => $room_service_class_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'checkin_date'                  => date('Y-m-d H:i:s',time())
            );

            $this->Transaction_bed->create($dt_transaction_bed);

            $health_facility_item_gender = [
                'c1002e5b-5f1f-4837-af45-122673052259', // G. ANAK
                '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c', // ICCU
                '6c7d7e53-ff24-4246-85b2-07ea77ac801f', // ICU
                'ff1f252e-daab-4bdb-b308-e9f4cb11a47d', // ICU ISOLASI
                '5515af72-6dcb-415d-89e4-2433e7b0149d', // NICU
                'cd72c7f0-920b-4260-b602-541d0a15305a', // OK
                'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039', // PICU
            ];

            // Update temporary room setting
            if ($room_id != null) {
                if (in_array($health_facility_item_id, $health_facility_item_gender)) {
                    $patient_sex = null;
                }
                if ($norm_mother != null) { // Jika pasien bayi laki-laki tidak perlu merubah gender ruangan G.KEBIDANAN
                    $patient_sex = 0;
                }
                $dt_room = array(
                    'is_surgery' => $is_surgery,
                    'is_internal' => $is_internal,
                    'gender_availability' => $patient_sex,
                );
                $this->Room->update_v2($room_id, $dt_room);
            }
        }

        // NOTIFICATIONS
        $select = 'a.id';
        $where = array(
            'a.health_facility_item_id' => $health_facility_item_id,
        );
        $position = $this->Position->select(0, $select, $where);
        if (count($position) > 0) {
            $position_id = $position[0]->id;

            // TRANSFER PASIEN
            // PASIEN A NAIK DARI B KE C RUANGAN D DENGAN BED NOMOR E
            $title = 'Pendaftaran Ranap';
            $message = "Pasien $patient_alias $patient_name ($norm) telah didaftarkan ke $health_facility_item_name ruangan $room_number dengan bed nomor $bed_name";

            $this->Notifications->createNotification(null, $position_id, $title, $message, 'regist_inpatient', ['health_facility_item_id' => $health_facility_item_id]);

            // GIZI
            $this->Notifications->createNotification(null, '8ca42f62-85bb-45f8-bf32-152a792e0f40', $title, $message, 'regist_inpatient', ['health_facility_item_id' => $health_facility_item_id]);
        }
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create registration_inpatient', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id,
            'registration_destination_id' => $registration_destination_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function create_checkin_post() {
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
            // DATA REGISTRATION
            $this->form_validation->set_rules('booking_code', 'booking_code', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;
                
                //CHECK APPOINTMENT X BOOKING
                $this->Appointment->whereFields['a.nobooking'] = $dt_post['booking_code']; 
                $this->Appointment->whereFields['a.date_appointment'] = date('Y-m-d'); 
                $dt_appoitnment = $this->Appointment->read(0,false)[0];
                if ($dt_appoitnment){  
                    //GET PATIENT 
                    $this->Patient_val->limit = 1;
                    $patient = $this->Patient_val->read($dt_appoitnment->norm, false);
                    $norm = $dt_appoitnment->norm;
                    $dataPoli = $this->Health_facility_item->read($dt_appoitnment->health_facility_item_id,false)[0];
                    $dataDokter = $this->Employee->read($dt_appoitnment->doctor_in_charge_id,false)[0];
                    $dataSchedule = $this->Doctor_schedule_item->read($dt_appoitnment->doctor_schedule_item_id,false)[0];
                    
                    $db_sirs = $this->load->database('antrol', true);
                    $db_sirs->select("*");
                    $db_sirs->from("bpjs_jkn_antrean");
                    $db_sirs->where("bpjs_jkn_antrean.kodebooking",$dt_post['booking_code']);
                    $data_booking = $db_sirs->get()->result()[0];
                    $jeniskunjungan = $data_booking->jeniskunjungan;
                    $this->load->library('Vclaim_sep','vclaim_sep');
                    $noSurat = '';
                    if ($jeniskunjungan == '1'|| $jeniskunjungan == '4') {
                        if ($jeniskunjungan == '1') {
                            $asalRujukan = '1';
                            $dataRujukanVclaim = $this->vclaim_sep->request("/Rujukan/"."$data_booking->nomorreferensi", "GET");
                            $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                            if ($dataRujukanVclaim['metaData']['code'] == "201") {
                                $this->db->trans_rollback();
                                $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                                return;
                            }
                        } else if ($jeniskunjungan == '4') {
                            $asalRujukan = '2';
                            $dataRujukanVclaim = $this->vclaim_sep->request("/Rujukan/RS/"."$data_booking->nomorreferensi", "GET");
                            $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                            if ($dataRujukanVclaim['metaData']['code'] == "201") {
                                $this->db->trans_rollback();
                                $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                                return;
                            }
                        }
                        $noMR = $norm;
                        $klsRawat = $dataRujukanVclaim['response']['rujukan']['peserta']['hakKelas']['kode'];
                        $tglRujukan = $dataRujukanVclaim['response']['rujukan']['tglKunjungan'];
                        $noRujukan = $dataRujukanVclaim['response']['rujukan']['noKunjungan'];
                        $ppkRujukan = $dataRujukanVclaim['response']['rujukan']['provPerujuk']['kode'];
                        $catatan = '';
                        $diagAwal = $dataRujukanVclaim['response']['rujukan']['diagnosa']['kode'];
                        $tujuanKunj = '0';
                        $flagProcedure = '';
                        $kdPenunjang = '';
                        $assesmentPel = '';
                    } else if ($jeniskunjungan == '3') {
                        $dataRujukanVclaim = $this->vclaim_sep->request("/RencanaKontrol/noSuratKontrol/"."$data_booking->nomorreferensi", "GET");
                        $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                        if ($dataRujukanVclaim['metaData']['code'] == "201") {
                            $this->db->trans_rollback();
                            $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                            return;
                        }
                        // var_dump($dataRujukanVclaim);
                        $noMR = $norm;
                        $klsRawat = $dataRujukanVclaim['response']['rujukan']['peserta']['hakKelas']['kode'];
                        $tglRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['tglRujukan'];
                        $noRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['noRujukan'];
                        $ppkRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['kdProviderPerujuk'];
                        $asalRujukan = $dataRujukanVclaim['response']['sep']['provPerujuk']['asalRujukan'];
                        $catatan = '';
                        $diagAwal = preg_replace('/\s+/', '', strtok($dataRujukanVclaim['response']['sep']['diagnosa'], '-'));
                        $noSurat = $data_booking->nomorreferensi;
                        if($dataRujukanVclaim['response']['sep']['provPerujuk']['kdProviderPerujuk'] == '0070R032'){
                            $tujuanKunj = '0';
                            $noRujukan = $dataRujukanVclaim['response']['sep']['noSep'];
                            $tglRujukan = $dataRujukanVclaim['response']['tglRencanaKontrol'];
                            $flagProcedure = '';
                            $kdPenunjang = '';
                            $assesmentPel = '';
                        } else {
                            $tujuanKunj = '2';
                            $flagProcedure = '';
                            $kdPenunjang = '';
                            $assesmentPel = '5';
                        }
                    } else if ($jeniskunjungan == '2') {
                        $dataRujukanVclaim = $this->vclaim_sep->request("/RencanaKontrol/noSuratKontrol/"."$data_booking->nomorreferensi", "GET");
                        $dataRujukanVclaim = json_decode($dataRujukanVclaim,true);
                        if ($dataRujukanVclaim['metaData']['code'] == "201") {
                            $this->db->trans_rollback();
                            $this->REST_Return(422, $dataRujukanVclaim->metaData->message, $dataRujukanVclaim->metaData->message);
                            return;
                        }
                    }
                    //SET DATA SEP
                        $noKartu = $data_booking->nomorkartu;
                        $tglSep = date("Y-m-d");
                        $ppkPelayanan = '0070R032';
                        $jnsPelayanan = '2';
                        $katarak = '0';
                        $kontrol = '';
                        $eksekutif = '0';
                        $klsRawatNaik = '';
                        $pembiayaan = '';
                        $penanggungJawab = '';
                        $cob = '';
                        $lakaLantas = '0';
                        $penjamin = '';
                        $tglKejadian = '';
                        $suplesi = '';
                        $noSepSuplesi = '';
                        $kdPropinsi = '';
                        $kdKabupaten = '';
                        $kdKecamatan = '';
                        $keteranganKll = '';
                        $noLP = '';
                        $tujuan = $dataPoli->bride_bpjs;
                        $kodeDPJP = $dataDokter->bride_bpjs;
                        $dpjpLayan = $dataDokter->bride_bpjs;
                        $noTelp = $data_booking->notelp != '-' && $data_booking->notelp != null ? $data_booking->notelp : $patient[0]->phone;
                        $user = 'Administrasi';
                        $DataPostSep = array();
                    //SET DATA SEP
                    if($klsRawat == '1'){
                        $hakkelas = '3';
                    } else if ($klsRawat == '2') {
                        $hakkelas = '4';
                    } else if ($klsRawat == '3') {
                        $hakkelas = '5';
                    }
                    $DataPostSep['request']['t_sep'] = array(
                        'noKartu'             => $noKartu,
                        'tglSep'              => $tglSep,
                        'ppkPelayanan'        => $ppkPelayanan,
                        'jnsPelayanan'        => $jnsPelayanan,
                        'klsRawat'            => 
                        array(
                        'klsRawatHak'       => $klsRawat,
                        'klsRawatNaik'      => $hakkelas == $klsRawatNaik ? "" : $klsRawatNaik,
                        'pembiayaan'        => $pembiayaan,
                        'penanggungJawab'   => $penanggungJawab
                        ),
                        'noMR'                => $noMR,
                        'rujukan'             => 
                        array(
                        'asalRujukan'       => $asalRujukan,
                        'tglRujukan'        => $tglRujukan,
                        'noRujukan'         => $noRujukan,
                        'ppkRujukan'        => $ppkRujukan
                        ),
                        'catatan'             => $catatan,
                        'diagAwal'            => $diagAwal,
                        'poli'                => 
                        array(
                        'tujuan'            => $tujuan,
                        'eksekutif'         => $eksekutif
                        ),
                        'cob'                 =>
                        array(
                            'cob'             => $cob
                        ),
                        'katarak'             =>
                        array(
                        'katarak'           => $katarak
                        ),
                        'jaminan'             =>
                        array(
                        'lakaLantas'        => $lakaLantas,
                        'noLP'              => $noLP,
                        'penjamin'          =>
                        array(
                            'tglKejadian'     => $tglKejadian,
                            'keterangan'      => $keteranganKll,
                            'suplesi'         => 
                            array(
                            'suplesi'       => $suplesi,
                            'noSepSuplesi'  => $noSepSuplesi,
                            'lokasiLaka'    =>
                            array(
                                'kdPropinsi'  => $kdPropinsi,
                                'kdKabupaten' => $kdKabupaten,
                                'kdKecamatan' => $kdKecamatan
                            ),
                            ),
                        ),
                        ),
                        'tujuanKunj'          => $tujuanKunj,
                        'flagProcedure'       => $flagProcedure,
                        'kdPenunjang'         => $kdPenunjang,
                        'assesmentPel'        => $assesmentPel,
                        'skdp'                => 
                        array(
                        'noSurat'           => $noSurat,
                        'kodeDPJP'          => $noSurat == "" ? "" : $kodeDPJP
                        ),
                        'dpjpLayan'           => $kodeDPJP,
                        'noTelp'              => $noTelp,
                        'user'                => $user,
                    );
                    $hasil = $this->vclaim_sep->request("/SEP/2.0/insert", "POST", json_encode($DataPostSep));
                    $d = json_decode($hasil, true);
                    $noSepMjkn = '';
                    // var_dump($d);
                    // echo json_encode($DataPostSep); die();
                    if ($d['metaData']['code'] == "200"){
                        $noSepMjkn = $d['response']['sep']['noSep'];
                    } else {
                        // var_dump(array('registration_destination_id'=>$reg_destination,'sep'=>$d->metaData->code));
                        //CEK DATA JIKA SUDAH TERBUAT SEP NAMUN GAGAL REGIST KE SIMRS
                            $hasilCekSEP = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".date("Y-m-d")."/JnsPelayanan/2", "GET");
                            $dcekSep = json_decode($hasilCekSEP, true);
                            if ($dcekSep['metaData']['code'] == "200"){
                                for ($i=0; $i < count($dcekSep['response']['sep']); $i++) { 
                                    if ($dcekSep['response']['sep'][$i]['noKartu'] == $data_booking->nomorkartu) {
                                        $noSepMjkn = $dcekSep['response']['sep'][$i]['noSep'];
                                        $d = $dcekSep['response']['sep'][$i];
                                    }
                                }
                                if ($noSepMjkn == '') {
                                    $d = json_decode($d, true);
                                    $response_data = $d;
                                    $this->REST_Return(422, 'FAILED '.$d['metaData']['message'], $DataPostSep);
                                    return;
                                } 
                            } else {
                                $d = json_decode($d, true);
                                $response_data = $d;
                                $this->REST_Return(422, 'FAILED '.$d['metaData']['message'], $DataPostSep);
                                return;
                            }
                        //CEK DATA JIKA SUDAH TERBUAT SEP NAMUN GAGAL REGIST KE SIMRS
                    }
                } else {
                    $response_data = array();
                    $this->REST_Return(422, 'KODE BOOKING TIDAK VALID', $response_data);
                    return;
                }
                // return;
                // var_dump($dt_appoitnment);
                $dt_registration = array();
                date_default_timezone_set('Asia/Jakarta');
                if($patient != []) {
                    // Flag patient
                    $this->Registration->flag_patient($dt_appoitnment->norm);
                    // Count code
                    $this->Registration_val->orderFields = array('a.code' => 'DESC');
                    $this->Registration_val->limit = 1;
                    $tmp = $this->Registration_val->read(0,false);
                    $waktu = date("Ymd",time());
                    if($tmp != []) {
                        $code = 'REG' . $waktu . str_pad(intval(substr($tmp[0]->code,11)) + 1, 4, '0', STR_PAD_LEFT);
                        // Check if reset is needed
                        if(substr($tmp[0]->code,3,8) != date('Ymd',time())) {
                            $code = 'REG' . $waktu . '0001';
                        }
                    } else {
                        $code = 'REG' . $waktu . '0001';
                    }
                    $norm = $dt_appoitnment->norm;

                    $dt_registration['code']   = $code;
                    $dt_registration['norm']   = $norm;
                    $dt_registration['type_register_id']   = 1;
                    $dt_registration['how_to_enter_id']   = 'cd67965b-b409-474c-a6a9-b4be1a1a9d5a';
                    $dt_registration['register_date']   = date("Y-m-d H:i:s");
                    $dt_registration['code_booking']   = $dt_post['booking_code'];
                } else {
                    $this->REST_Return(404, 'Wrong norm!');
                    return;
                }
                // var_dump($dt_registration);

                //CEK DULU APAKAH SUDAH TER REGISTER
                $this->Registration_val->whereFields['a.code_booking'] = $dt_post['booking_code']; 
                $this->Registration_val->whereFields['a.norm'] = $norm; 
                $this->Registration_val->limit = 1;
                $cekisregist = $this->Registration_val->read(0,false);
                if ($cekisregist != []) {
                    $response_data = array(
                        'registration_id'     => $cekisregist[0]->id,
                        'queue'               => $dt_appoitnment->code.'-'.$dt_appoitnment->queue,
                        'sep'                 => $noSepMjkn,
                        'norm'                => $norm,  
                        'destination'         => $dt_appoitnment->health_facility_item_name,
                        'birth_date'          => $patient[0]->birth_date,
                        'sex'                 => $patient[0]->sex == '1' ? 'L' : 'P',
                        'name'                => $dt_appoitnment->name,
                        'dpjp'                => $dt_appoitnment->doctor_in_charge_name,
                        'test'                => $DataPostSep,
                        'code_booking'        => $dt_post['booking_code']
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                    return; 
                }
                //CEK DULU APAKAH SUDAH TER REGISTER

                //REGISTER
                $this->db->trans_start();

                $id = $this->$model->create($dt_registration);
                // $id = 1;
                //ADM PASIEN
                    $normint = (int)$norm;
                    if ($normint <= 37774) {
                        //ADM PASIEN BARU BPJS
                        $this->Transaction_administration->create(array(
                            'registration_id' => $id,
                            'costing_id' => 'fc2e9ae3-32d0-4780-b206-85586abd977d',
                            'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                            'var_1' => 35000,
                            'date_added' => date('Y-m-d H:i:s')
                        ));
                    } else {
                        if ($patient[0]->is_new == 'Y'){
                            //ADM PASIEN BARU BPJS
                            $this->Transaction_administration->create(array(
                                'registration_id' => $id,
                                'costing_id' => '6b9f2fad-dd24-40a3-b5bd-b24e03cfe1c7',
                                'costing_name' => 'Administrasi Pasien Rawat Jalan (Baru)',
                                'var_1' => 50000,
                                'date_added' => date('Y-m-d H:i:s')
                            ));
                            $this->Patient->replace(array('norm'=>$norm,'is_new'=>'N'));
                        } else {
                            //ADM PASIEN LAMA BPJS
                            $this->Transaction_administration->create(array(
                                'registration_id' => $id,
                                'costing_id' => 'fc2e9ae3-32d0-4780-b206-85586abd977d',
                                'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                                'var_1' => 35000,
                                'date_added' => date('Y-m-d H:i:s')
                            ));
                        }
                    }
                //ADM PASIEN
                // CHECK IF THE PATIENT IS NEW
                $is_new = $this->Registration->flag_patient_destination($norm,$dt_appoitnment->health_facility_item_id);
                //DESTINATION
                $dt_registration_destination = array(
                    'registration_id'               => $id,
                    'health_facility_item_id'       => $dt_appoitnment->health_facility_item_id,
                    'doctor_in_charge_id'           => $dt_appoitnment->doctor_in_charge_id,
                    'doctor_schedule_item_id'       => $dt_appoitnment->doctor_schedule_item_id,
                    'register_date'                 => date("Y-m-d H:i:s"),
                    'checkin_date'                  => date("Y-m-d H:i:s"),
                    'user_added'                    => $user_id,
                    'is_new'                        => $is_new,
                    'code_queue'                    => $dt_appoitnment->code,
                    'queue'                         => $dt_appoitnment->queue,
                );
                // var_dump($dt_registration_destination);
                $reg_destination = $this->Registration_Destination->create($dt_registration_destination);
                // $reg_destination = 1;
                if ($reg_destination) {
                    $dt_registration_insurance = array(
                        'registration_destination_id'   => $reg_destination,
                        'company_id'                    => '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
                        'insurance_id'                  => '2',
                        'number_insurance'              => $data_booking->nomorkartu,
                        // 'expired'                       => ,
                        'is_primary'                    => 1,
                        'noKunjungan'                   => $data_booking->nomorreferensi,
                        // 'service_class_id'              => ,
                        // 'service_class_name'            => $service_class[0]->name,
                    );
                    $this->Registration_Insurance->replace($dt_registration_insurance);
                    $dt_insurance = array(
                        'norm'                          => $norm,
                        'institute_branch_id'           => $institute_branch_id,
                        'company_id'                    => '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8',
                        'insurance_id'                  => '2',
                        'number_insurance'              => $data_booking->nomorkartu,
                        // 'expired'                       => ,
                        // 'is_primary'                    => 1,
                        // 'service_class_id'              => ,
                    );
                    $this->Insurance->replace($dt_insurance);
                    $this->db->where('registration_destination_id', $reg_destination);
                    $this->db->where('insurance_id',2);
                    $this->db->where('is_primary',1);
                    $this->db->limit(1);
                    $this->db->update('registration_insurance', array('sep'=>$noSepMjkn,'sep_json'=>json_encode($d)));
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    return;
                } else {
                    $response_data = array(
                        'registration_id'     => $id,
                        'queue'               => $dt_appoitnment->code.'-'.$dt_appoitnment->queue,
                        'sep'                 => $noSepMjkn,
                        'norm'                => $noMR,  
                        'destination'         => $dt_appoitnment->health_facility_item_name,
                        'birth_date'          => $patient[0]->birth_date,
                        'sex'                 => $patient[0]->sex == '1' ? 'L' : 'P',
                        'name'                => $dt_appoitnment->name,
                        'dpjp'                => $dt_appoitnment->doctor_in_charge_name,
                        'test'                => $DataPostSep,
                        'code_booking'        => $dt_post['booking_code']
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                    return; 
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_external_post() {
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
        $this->form_validation->set_rules($this->create_external_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $norm = $dt_post['norm'];
        $type_register_id = $dt_post['type_register_id'];
        $how_to_enter_id = $dt_post['how_to_enter_id'];
        $icd10_id = $dt_post['icd10_id'];

        $select = 'a.norm';
        $patient = $this->Patient->select($norm, $select);

        if (count($patient) == 0) {
            $this->REST_Return(404, 'there is no patient with norm ' . $norm);
            return;
        }

        $select = 'a.id';
        $how_to_enter = $this->How_to_enter->select($how_to_enter_id, $select);

        if (count($how_to_enter) == 0) {
            $this->REST_Return(404, 'there is no how_to_enter with id ' . $how_to_enter_id);
            return;
        }

        $select = 'a.id';
        $icd10 = $this->Icd10->select($icd10_id, $select);

        if (count($icd10) == 0) {
            $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
            return;
        }

        $now = date('Y-m-d H:i:s', time());

        // count code
        $prefix = 'EREG';
        $epoch = time();
        $dt_queue = array(
            'var_2' => date('Y', $epoch),
            'var_3' => date('m', $epoch),
            'var_4' => date('d', $epoch),
            'var_5' => date('Y-m-d', $epoch),
        );

        $code = $this->Queue->counter_queue($prefix, $dt_queue, 4);

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration = array(
            'code'              => $code,
            'norm'              => $norm,
            'type_register_id'  => $type_register_id,
            'how_to_enter_id'   => $how_to_enter_id,
            'icd10_id'          => $icd10_id,
            'register_date'     => $now,
        );
    
        $id = $this->$model->create($dt_registration);

        // ADMINISTRASI PASIEN BARU
        $flag_check_new_old_hf = $this->Registration->check_new_old_hf($norm);
        if ($flag_check_new_old_hf == 0) {
            $this->Transaction_administration->create(array(
                'registration_id' => $id,
                'costing_id' => 'ef619d4d-d2ca-4434-b26f-217803863d4b',
                'costing_name' => 'Administrasi Pasien Rawat Jalan (Baru)',
                'var_1' => 50000,
                'date_added' => date('Y-m-d H:i:s')
            ));
        // ADMINISTRASI PASIEN LAMA
        } else {
            $this->Transaction_administration->create(array(
                'registration_id' => $id,
                'costing_id' => 'a6bff965-aadc-4554-b4ef-cc9d534aeb30',
                'costing_name' => 'Administrasi Pasien Rawat Jalan (Lama)',
                'var_1' => 35000,
                'date_added' => date('Y-m-d H:i:s')
            ));
        }

        $health_facility_item_id = $dt_post['destination'][0]['health_facility_item_id'];
        $doctor_in_charge_id = 'f5fb7c36-647d-4f9a-870d-5b2174ec1db0';
        $insurances_arr = $dt_post['destination'][0]['insurances'];

        if ($insurances_arr == null) {
            $this->REST_Return(400, 'field insurances in destination is required');
            return;
        }
        
        $select = 'a.id, a.name';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);

        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $select = 'a.id, a.fullname';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $is_new = $this->Registration->flag_patient_destination($norm, $health_facility_item_id);

        // INIT_REGISTRATION_DESTINATION
        $dt_registration_destination = array(
            'registration_id'               => $id,
            'health_facility_item_id'       => $health_facility_item_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'register_date'                 => date("Y-m-d H:i:s", time()),
            'checkin_date'                  => date("Y-m-d H:i:s", time()),
            'user_added'                    => $user_id,
            'is_new'                        => $is_new,
        );

        $registration_destination_id = $this->Registration_Destination->create($dt_registration_destination);

        // INIT_REGISTRATION_INSURANCE
        for ($a=0; $a < count($insurances_arr) ; $a++) { 
            $insurance_id = $insurances_arr[$a]['insurance_id'];
            $number_insurance = $insurances_arr[$a]['number_insurance'];
            $is_primary = $insurances_arr[$a]['is_primary'];

            if ($insurance_id == null) {
                $this->REST_Return(400, 'field insurance_id in insurances is required');
                return;
            }

            if ($number_insurance == null) {
                $this->REST_Return(400, 'field number_insurance in insurances is required');
                return;
            }

            if ($is_primary == null) {
                $this->REST_Return(400, 'field is_primary in insurances is required');
                return;
            }

            if($insurance_id == 1) {
                $dt_registration_insurance = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'is_primary'                    => $is_primary,
                );

                $this->Registration_Insurance->replace($dt_registration_insurance);

                $dt_insurance = array(
                    'norm'                          => $norm,
                    'institute_branch_id'           => $institute_branch_id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'is_primary'                    => $is_primary,
                );

                $this->Insurance->replace($dt_insurance);
            } else {
                $company_id = $insurances_arr[$a]['company_id'];
                $service_class_id = $insurances_arr[$a]['service_class_id'];
                $expired = $insurances_arr[$a]['expired'];

                if ($company_id == null) {
                    $this->REST_Return(400, 'field company_id in insurances is required');
                    return;
                }

                if ($service_class_id == null) {
                    $this->REST_Return(400, 'field service_class_id in insurances is required');
                    return;
                }
            
                $select = 'a.id';
                $company = $this->Company->select($company_id, $select);

                if (count($company) == 0) {
                    $this->REST_Return(404, 'there is no company with id ' . $company_id);
                    return;
                }

                $select = 'a.id, a.name';
                $service_class = $this->Service_class->select($service_class_id, $select);

                if (count($service_class) == 0) {
                    $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
                    return;
                }

                $service_class_name = $service_class[0]->name;

                $dt_registration_insurance = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'company_id'                    => $company_id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'expired'                       => $expired,
                    'is_primary'                    => $is_primary,
                    'service_class_id'              => $service_class_id,
                    'service_class_name'            => $service_class_name,
                );

                $this->Registration_Insurance->replace($dt_registration_insurance);

                $dt_insurance = array(
                    'norm'                          => $norm,
                    'institute_branch_id'           => $institute_branch_id,
                    'company_id'                    => $company_id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'expired'                       => $expired,
                    'is_primary'                    => $is_primary,
                    'service_class_id'              => $service_class_id,
                );

                $this->Insurance->replace($dt_insurance);
            }
        }

        // INIT_SOAP
        $dt_soap = array(
            'registration_destination_id'  => $registration_destination_id,
            'doctor_in_charge_id'          => $doctor_in_charge_id,
            'user_added'                   => $user_id,
            'date_added'                   => $now,
            'date_updated'                 => $now
        );

        $soap_id = $this->Soap->create($dt_soap);

        $medical_support_bundle_id = $dt_post['medical_support_bundle_id'];

        if ($medical_support_bundle_id != null) {
            $date_request = $dt_post['date_request'];

            if ($date_request == null) {
                $this->REST_Return(400, 'date_request is required if choosing bundle');
                return;
            }

            $select = 'a.id';
            $bundle = $this->Medical_support_bundle->select($medical_support_bundle_id, $select);

            if (count($bundle) == 0) {
                $this->REST_Return(404, 'there is no medical_support_bundle with id ' . $medical_support_bundle_id);
                return;
            }

            $select = 'a.package_id, a.item_type_id';
            $where = array('a.medical_support_bundle_id');
            $bundle_item = $this->Medical_support_bundle_item->select(0, $select, $where);

            $laboratory_request_id = '';
            $radiology_request_id = '';

            for ($i=0; $i < count($bundle_item); $i++) { 
                $bundle_item_package_id = $bundle_item[$i]->package_id;
                $bundle_item_type_id = $bundle_item[$i]->item_type_id;
                $req_create = 0;

                if ($bundle_item_type_id == 1) {
                    $select = 'a.id, a.costing_id';
                    $where = array('a.is_delete' => 0);
                    $lab_package = $this->Laboratory_package->select($bundle_item_package_id, $select, $where);

                    if (count($lab_package) == 0) {
                        $this->REST_Return(404, 'there is no laboratory_package with id ' . $bundle_item_package_id);
                        return;
                    }

                    $package_costing_id = $lab_package[0]->costing_id;

                    if ($laboratory_request_id == '') {
                        $req_create = 1;
                    }

                    $prefix = 'LAB';
                    $request = 'Laboratory_request';
                    $request_item = $request.'_item';
                    $request_name = 'laboratory';
                } elseif ($bundle_item_type_id == 2) {
                    $select = 'a.id, a.costing_id';
                    $where = array('a.is_delete' => 0);
                    $rad_package = $this->Radiology_package->select($bundle_item_package_id, $select, $where);

                    if (count($rad_package) == 0) {
                        $this->REST_Return(404, 'there is no radiology_package with id ' . $bundle_item_package_id);
                        return;
                    }

                    $package_costing_id = $rad_package[0]->costing_id;

                    if ($radiology_request_id == '') {
                        $req_create = 1;
                    }

                    $prefix = 'RAD';
                    $request = 'Radiology_request';
                    $request_item = $request.'_item';
                    $request_name = 'radiology';
                }

                if ($req_create == 1) {
                    // count no_request & queue
                    $epoch = strtotime($date_request);
                    $dt_queue = array(
                        'var_2' => date('Y', $epoch),
                        'var_3' => date('m', $epoch),
                        'var_4' => date('d', $epoch),
                        'var_5' => date('Y-m-d', $epoch),
                    );
            
                    $no_request = $this->Queue->counter_queue($prefix, $dt_queue, 4);
                    $queue = round((int)substr($no_request, -4));
            
                    $dt_request = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'soap_id'               => $soap_id,
                        'doctor_in_charge_id'   => $doctor_in_charge_id,
                        'no_request'            => $no_request,
                        'queue'                 => $queue,
                        'date_request'          => $date_request,
                    );
    
                    $request_id = $this->$request->create($dt_request);
    
                    if ($request_name == 'laboratory') {
                        $laboratory_request_id = $request_id;
                    } elseif ($request_name == 'radiology') {
                        $radiology_request_id = $request_id;
                    }
                }

                $select = 'a.id, a.name';
                $costing = $this->Costing->select($package_costing_id, $select);

                $package_costing_name = $costing[0]->name;

                $select = 'a.id, a.var_1, a.var_2, a.var_3';
                $package_costing_sc = $this->Costing_services_class->select($package_costing_id, $select);

                $package_costing_sc_var_1 = $package_costing_sc[0]->var_1;
                $package_costing_sc_var_2 = $package_costing_sc[0]->var_2;
                $package_costing_sc_var_3 = $package_costing_sc[0]->var_3;

                $dt_request_item = array(
                    $request_name.'_request_id' => $request_id,
                    $request_name.'_package_id' => $bundle_item_package_id,
                    'service_class_id'          => $service_class_id,
                    'var_1'                     => $package_costing_sc_var_1,
                    'var_2'                     => $package_costing_sc_var_2,
                    'var_3'                     => $package_costing_sc_var_3,
                    'costing_id'                => $package_costing_id,
                    'costing_name'              => $package_costing_name,
                    'date_request'              => $date_request,
                    'status'                    => 1
                );

                $this->$request_item->create($dt_request_item);
            }
        }

        // INIT_PENUNJANG
        $laboratory_request = $dt_post['laboratory_request'];
        if ($laboratory_request != null) {
            $date_request = $laboratory_request['date_request'];
            // count no_request & queue
            $prefix = 'LAB';
            $epoch = strtotime($date_request);
            $dt_queue = array(
                'var_2' => date('Y', $epoch),
                'var_3' => date('m', $epoch),
                'var_4' => date('d', $epoch),
                'var_5' => date('Y-m-d', $epoch),
            );
    
            $no_request = $this->Queue->counter_queue($prefix, $dt_queue, 4);
            $queue = round((int)substr($no_request, -4));
    
            $dt_laboratory_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $soap_id,
                'doctor_in_charge_id'   => $doctor_in_charge_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'date_request'          => $date_request,
            );

            $laboratory_request_id = $this->Laboratory_request->create($dt_laboratory_request);

            $laboratory_request_items_arr = $laboratory_request['laboratory_request_items'];

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
                
                $dt_laboratory_request_item = array(
                    'laboratory_request_id'     => $laboratory_request_id,
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
            }
        }

        $radiology_request = $dt_post['radiology_request'];
        if ($radiology_request != null) {
            $date_request = $radiology_request['date_request'];
            // count no_request & queue
            $prefix = 'RAD';
            $epoch = strtotime($date_request);
            $dt_queue = array(
                'var_2' => date('Y', $epoch),
                'var_3' => date('m', $epoch),
                'var_4' => date('d', $epoch),
                'var_5' => date('Y-m-d', $epoch),
            );
    
            $no_request = $this->Queue->counter_queue($prefix, $dt_queue, 4);
            $queue = round((int)substr($no_request, -4));
   
            $dt_radiology_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $soap_id,
                'doctor_in_charge_id'   => $doctor_in_charge_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'date_request'          => $date_request
            );

            $radiology_request_id = $this->Radiology_request->create($dt_radiology_request);
        
            $radiology_request_items_arr = $radiology_request['radiology_request_items'];
    
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

                $dt_radiology_request_item = array(
                    'radiology_request_id'      => $radiology_request_id,
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
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create registration external', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }

    /**
     * GET Registration with API
     * -------------------------
     * @method: GET
     */

    public function detail_backup_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_cancel'] = '0';
            $this->$model->orderFields = array('b.checkin_date' => 'DESC');
            if ($q_strings != []){
                $this->$model->orderFields = array('b.checkin_date' => 'DESC');
                $output = $this->$model->filter_data($q_strings);
            } else {
                $this->$model->orderFields = array('b.checkin_date' => 'DESC');
                $output = $this->$model->read($id);
            }
            for ($i=0; $i < count($output); $i++) {
                $registration_id = $output[$i]->registration_id;

                $this->Registration_Insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
                $this->Registration_Insurance->whereFields['a.is_primary'] = 1; 
                $this->Registration_Insurance->whereFields['a.registration_destination_id'] = $output[$i]->registration_destination_id;
                $reg_insurance = $this->Registration_Insurance->read();
                for ($j=0; $j < count($reg_insurance); $j++) { 
                    $output[$i]->registration_insurance[$j] = $reg_insurance[$j];
                }

                $registration_destination_id = $output[$i]->registration_destination_id;

                // EWS SCORE
                $ews = $this->db->query("
                    SELECT ewsi.total as ews_score, ewsi.aware
                    FROM early_warning_score ews 
                    INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                    WHERE ews.registration_destination_id ='$registration_destination_id'
                    AND ews.is_delete = 0
                    AND ewsi.is_delete = 0
                    ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                    LIMIT 1
                ")->result();

                if (count($ews) == 0) {
                    $ews = $this->db->query("
                        SELECT ewsi.total as ews_score, ewsi.aware
                        FROM early_warning_score ews 
                        INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                        INNER JOIN registration_destination rd ON ews.registration_destination_id = rd.id
                        WHERE rd.registration_id ='$registration_id'
                        AND ews.is_delete = 0
                        AND ewsi.is_delete = 0
                        ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                        LIMIT 1
                    ")->result();
                }
                
                if (count($ews) > 0) {
                    $output[$i]->aware = $ews[0]->aware;
                    $output[$i]->ews_score = $ews[0]->ews_score;
                } else {
                    $output[$i]->aware = 0;
                    $output[$i]->ews_score = 0;
                }

                // LAST TTV
                $ttv = $this->db->query("
                    SELECT vsm.observation_date,
                    vsm.systolic,
                    vsm.diastolic,
                    vsm.pulse,
                    vsm.temperature,
                    vsm.respiratory_rate,
                    vsm.oxygen_saturation,
                    vsm.verbal_response,
                    vsm.oxygen_type_id,
                    vsm.sore_score,
                    vsm.ews,
                    vsm.blood_sugar,
                    vsm.weight,
                    vsm.height
                    FROM vital_signs_monitoring vsm 
                    WHERE vsm.registration_destination_id ='$registration_destination_id'
                    AND vsm.is_delete = 0
                    ORDER BY vsm.observation_date DESC
                    LIMIT 1
                ")->result();

                if (count($ttv) == 0) {
                    $ttv = $this->db->query("
                        SELECT vsm.observation_date,
                        vsm.systolic,
                        vsm.diastolic,
                        vsm.pulse,
                        vsm.temperature,
                        vsm.respiratory_rate,
                        vsm.oxygen_saturation,
                        vsm.verbal_response,
                        vsm.oxygen_type_id,
                        vsm.sore_score,
                        vsm.ews,
                        vsm.blood_sugar,
                        vsm.weight,
                        vsm.height
                        FROM vital_signs_monitoring vsm 
                        INNER JOIN registration_destination rd ON vsm.registration_destination_id = rd.id
                        WHERE rd.registration_id ='$registration_id'
                        AND vsm.is_delete = 0
                        ORDER BY vsm.observation_date DESC
                        LIMIT 1
                    ")->result();
                }

                if (count($ttv) > 0) {
                    $output[$i]->ttv['observation_date'] = $ttv[0]->observation_date;
                    $output[$i]->ttv['systolic'] = $ttv[0]->systolic;
                    $output[$i]->ttv['diastolic'] = $ttv[0]->diastolic;
                    $output[$i]->ttv['pulse'] = $ttv[0]->pulse;
                    $output[$i]->ttv['temperature'] = $ttv[0]->temperature;
                    $output[$i]->ttv['respiratory_rate'] = $ttv[0]->respiratory_rate;
                    $output[$i]->ttv['oxygen_saturation'] = $ttv[0]->oxygen_saturation;
                    $output[$i]->ttv['verbal_response'] = $ttv[0]->verbal_response;
                    $output[$i]->ttv['oxygen_type_id'] = $ttv[0]->oxygen_type_id;
                    $output[$i]->ttv['sore_score'] = $ttv[0]->sore_score;
                    $output[$i]->ttv['ews'] = $ttv[0]->ews;
                    $output[$i]->ttv['blood_sugar'] = $ttv[0]->blood_sugar;
                    $output[$i]->ttv['weight'] = $ttv[0]->weight;
                    $output[$i]->ttv['height'] = $ttv[0]->height;
                } else {
                    $output[$i]->ttv['observation_date'] = '';
                    $output[$i]->ttv['systolic'] = 0;
                    $output[$i]->ttv['diastolic'] = 0;
                    $output[$i]->ttv['pulse'] = 0;
                    $output[$i]->ttv['temperature'] = 0;
                    $output[$i]->ttv['respiratory_rate'] = 0;
                    $output[$i]->ttv['oxygen_saturation'] = 0;
                    $output[$i]->ttv['verbal_response'] = '';
                    $output[$i]->ttv['oxygen_type_id'] = 0;
                    $output[$i]->ttv['sore_score'] = 0;
                    $output[$i]->ttv['ews'] = 0;
                    $output[$i]->ttv['blood_sugar'] = 0;
                    $output[$i]->ttv['weight'] = 0;
                    $output[$i]->ttv['height'] = 0;
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

        $where = $q_strings;

        // daftar field yang dianggap penting (wajib ada salah satunya)
        $requiredFields = [
            'registration_id',
            'registration_destination_id',
            'is_new',
            'sex',
            'code',
            'norm',
            'name',
            'health_facility_id',
            'health_facility_item_id'
        ];

        // cek apakah ada salah satu field yang tidak kosong
        $hasRequired = false;
        foreach ($requiredFields as $field) {
            if (!empty($q_strings[$field])) {
                $hasRequired = true;
                break;
            }
        }

        if (!$hasRequired) {
            $this->REST_Return(400, 'Minimal salah satu parameter wajib diisi.');
            return;
        }

        if (isset($q_strings['registration_destination_id'])) {
            $where['rd.id'] = $q_strings['registration_destination_id'];
            unset($where['registration_destination_id']);
        }

        if(isset($q_strings['is_new'])) { 
            $where['p.is_new'] = $q_strings['is_new'];
            unset($where['is_new']);
        }

		if(isset($q_strings['sex'])) { 
            $where['p.sex'] = $q_strings['sex'];
            unset($where['sex']);
        }

		if(isset($q_strings['code'])) { 
            $where['a.code'] = $q_strings['code'];
            unset($where['code']);
        }

		if(isset($q_strings['norm'])) { 
            $where['p.norm'] = $q_strings['norm'];
            unset($where['norm']);
        }

		if(isset($q_strings['name'])) { 
            $where['p.name'] = $q_strings['name'];
            unset($where['name']);
        }

		if(isset($q_strings['health_facility_id'])) { 
            $where['hfi.health_facility_id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }

		if(isset($q_strings['health_facility_item_id'])) { 
            $where['rd.health_facility_item_id'] = $q_strings['health_facility_item_id'];
            unset($where['health_facility_item_id']);
        }
        
        $select = 'rd.id as id, 
        rd.id as registration_destination_id, 
        a.id as registration_id, 
        rd.health_facility_item_id,
        hfi.name as health_facility_item_name,
        hfi.health_facility_id,
        hf.name as health_facility_name,
        a.how_to_enter_id,
        hte.name as how_to_enter_name,
        hte.type_how_to_enter_id, 
        rd.bed_id,
        b.name as bed_name,
        b.room_id,
        r.name as room_name,
        r.code as room_code,
        r.service_class_id,
        rd.doctor_in_charge_id,
        e.fullname as doctor_in_charge_name,
        e.ttd as employee_signature,
        e.sip,
        ps.id as doctor_in_charge_position_id,
        ps.name as doctor_in_charge_position_name,
        e.bride_bpjs,
        e2.fullname as doctor_in_charge_2_name,
        e3.fullname as doctor_in_charge_3_name,
        e4.fullname as doctor_in_charge_4_name,
        e5.fullname as doctor_in_charge_5_name,
        e6.fullname as doctor_in_charge_6_name,
        e7.fullname as doctor_in_charge_7_name,
        e8.fullname as doctor_in_charge_8_name,
        e9.fullname as doctor_in_charge_9_name,
        e10.fullname as doctor_in_charge_10_name,
        e11.fullname as doctor_in_charge_11_name,
        e12.fullname as doctor_in_charge_12_name,
        rd.doctor_in_charge_id_2,
        rd.doctor_in_charge_id_3,
        rd.doctor_in_charge_id_4,
        rd.doctor_in_charge_id_5,
        rd.doctor_in_charge_id_6,
        rd.doctor_in_charge_id_7,
        rd.doctor_in_charge_id_8,
        rd.doctor_in_charge_id_9,
        rd.doctor_in_charge_id_10,
        rd.doctor_in_charge_id_11,
        rd.doctor_in_charge_id_12,
        rd.doctor_schedule_item_id,
        rd.soap_s_id,
        rd.soap_a_id,
        rd.nurse_id,
        rd.doctor_in_charge_sec_id,
        rd.registration_destination_ref_id,
        a.register_date,
        rd.checkin_date,
        rd.checkout_date,
        a.code, 
        a.norm, 
        a.type_register_id, 
        a.type_visit_id,
        rd.type_emergency_id,
        rd.type_emergency_case_id,
        rd.case_location,
        rd.name_location,
        a.icd10_id,
        a.icd10_code,
        a.icd10_name,
        rd.is_checkout,
        rd.is_transfer,
        a.is_cancel,
        a.is_cob,
        a.is_booking,
        a.code_booking,
        a.faskes_code,
        a.faskes_name,
        a.pic_id,
        a.is_prb,
        a.prb,
        rd.checkin_date,
        rd.checkout_date,
        rd.exit_id,
        ex.name as exit_name,
        rd.exit_reason,
        rd.cancel_reason,
        rd.restore_reason,
        rd.transfer_reason,
        rd.transfer_reason_type_id,
        rd.is_new,
        rd.is_new as registration_destination_is_new,
        rd.code_queue,
        rd.queue,
        rd.sts,
        rd.general_reason,
        rd.exam_done,
        rd.treatment_done,
        rd.infus_sts,
        rd.infus,
        rd.drug_inj_sts,
        rd.drug_inj,
        rd.drug_orl_sts,
        rd.drug_orl,
        rd.derajat,
        rd.isTransportable,
        rd.main_complaint,
        rd.call_nurse_stamp,
        rd.call_doctor_stamp,
        rd.done_doctor_stamp,
        rd.call_pharmacy_stamp,
        rd.done_pharmacy_stamp,
        rd.user_added,
        rd.user_cancel,
        rd.date_cancel,
        rd.user_updated,
        rd.date_updated,
        rd.date_task_1,
        rd.date_task_2,
        rd.date_task_3,
        rd.date_task_4,
        rd.date_task_5,
        rd.date_task_6,
        rd.date_task_7,
        rd.date_control,
        p.norm_mother,
        p.institute_branch_id,
        p.idcard,
        p.typeidcard,
        p.alias,
        p.f_title,
        p.name,
        p.b_title,
        p.fullname,
        p.birth_date,
        p.birth_place,
        p.address,
        p.sex,
        p.blood,
        p.married_id,
        p.profession_id,
        p.citizenship_id,
        p.religion_id,
        p.last_edu_id,
        p.phone,
        p.email,
        p.profession_id,
        p.address_id,
        p.zip_code,
        p.photo,
        p.patient_issue_id,
        p.patient_issue_text,
        p.date_added,
        p.type_patient_id,
        p.is_delete,
        p.bride_cust,
        p.bride_cust_id,
        p.user_deleted,
        p.date_deleted,
        p.rt,
        p.rw,
        p.bride_no_bpjs,
        p.idcardfam,
        p.idcardparent,
        dsi.hour_start,
        dsi.hour_end,
        c.logo_url,
        rd.icare_status,
        rd.patient_signature,
        rp.name as responsible_person_name,
        rp.address_id as responsible_person_address,
        rp.phone as responsible_person_phone,
        rp.idcard as responsible_person_idcard,
        rp.responsible_id as responsible_person_responsible_id,
        rp.birth_date as responsible_person_birth_date,
        tn.triage_result
        ';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('employee_position ep', 'e.id = ep.employee_id', 'LEFT'),
            array('position ps', 'ep.position_id = ps.id', 'LEFT'),
            array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('employee e6', 'rd.doctor_in_charge_id_6 = e6.id', 'LEFT'),
            array('employee e7', 'rd.doctor_in_charge_id_7 = e7.id', 'LEFT'),
            array('employee e8', 'rd.doctor_in_charge_id_8 = e8.id', 'LEFT'),
            array('employee e9', 'rd.doctor_in_charge_id_9 = e9.id', 'LEFT'),
            array('employee e10', 'rd.doctor_in_charge_id_10 = e10.id', 'LEFT'),
            array('employee e11', 'rd.doctor_in_charge_id_11 = e11.id', 'LEFT'),
            array('employee e12', 'rd.doctor_in_charge_id_12 = e11.id', 'LEFT'),
            array('how_to_enter hte', 'a.how_to_enter_id = hte.id', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room r', 'b.room_id = r.id', 'LEFT'),
            array('doctor_schedule_item dsi', 'rd.doctor_schedule_item_id = dsi.id', 'LEFT'),
            array('company c', 'ri.company_id = c.id OR (ri.insurance_id = "1" AND c.id = "e5a1e978-af77-4901-9ef8-4ebaeee77dfa")', 'LEFT'),
            array('responsible_person rp', 'a.norm = rp.norm', 'LEFT'),
            array('triage_new tn', 'rd.id = tn.registration_destination_id', 'LEFT'),
        );
        $order = array('rd.checkin_date' => 'DESC');
        // if ($hf_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // ORDER BY TRIASE
        //     $select = $select . ', tn.triage_result';
        //     $join[] = array('triage_new tn', 'rd.id = tn.registration_destination_id', 'LEFT');
        //     unset($order['rd.checkin_date']);
        //     $order = array('tn.triage_result' => 'asc');
        // }
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $alias_arr = get_variable('alias');
        $type_how_to_enter_arr = get_variable('type_how_to_enter');
        $sex_arr = get_variable('sex');
        $type_register_arr = get_variable('type_register');
        $type_emergency_arr = get_variable('type_emergency');
        $type_emergency_case_arr = get_variable('type_emergency_case');
        $type_visit_arr = get_variable('type_visit');
        $responsible_arr = get_variable('responsible');
        $religion_arr = get_variable('religion');
        $profession_arr = get_variable('profession');
        $blood_arr = get_variable('blood');

        for ($i=0; $i < count($output); $i++) { 
            $registration_id = $output[$i]->registration_id;
            $registration_destination_id = $output[$i]->registration_destination_id;
            $norm = $output[$i]->norm;
            $alias = $output[$i]->alias;
            $type_how_to_enter_id = $output[$i]->type_how_to_enter_id;
            $sex = $output[$i]->sex;
            $type_register_id = $output[$i]->type_register_id;
            $type_emergency_id = $output[$i]->type_emergency_id;
            $type_emergency_case_id = $output[$i]->type_emergency_case_id;
            $type_visit_id = $output[$i]->type_visit_id;
            $responsible_id = $output[$i]->responsible_person_responsible_id;
            $profession_id = $output[$i]->profession_id;
            $religion_id = $output[$i]->religion_id;
            $room_name = $output[$i]->room_name;
            $blood = $output[$i]->blood;
            $room_code = $output[$i]->room_code;

            $output[$i]->alias_name = $alias_arr[$alias];
            $output[$i]->type_how_to_enter_name = $type_how_to_enter_arr[$type_how_to_enter_id];
            $output[$i]->sex_name = $sex_arr[$sex];
            $output[$i]->type_register_name = $type_register_arr[$type_register_id];
            $output[$i]->type_emergency_name = $type_emergency_arr[$type_emergency_id];
            $output[$i]->type_emergency_case_name = $type_emergency_case_arr[$type_emergency_case_id];
            $output[$i]->type_visit_name = $type_visit_arr[$type_visit_id];
            $output[$i]->responsible_person_responsible_name = $responsible_arr[$responsible_id];
            $output[$i]->blood = $blood_arr[$blood];
            $output[$i]->religion_name = $religion_arr[$religion_id];
            $output[$i]->profession_name = $profession_arr[$profession_id];
            
            $doctor_in_charge_id_2 = $output[$i]->doctor_in_charge_id_2;
            $doctor_in_charge_id_3 = $output[$i]->doctor_in_charge_id_3;
            $doctor_in_charge_id_4 = $output[$i]->doctor_in_charge_id_4;
            $doctor_in_charge_id_5 = $output[$i]->doctor_in_charge_id_5;
            for ($k=2; $k < 6; $k++) { 
                $var_1 = "doctor_in_charge_id_$k";
                if ($$var_1 != null) {
                    $select = 'a.id, ep.position_id, ps.name as position_name';
                    $where = array();
                    $join = array(
                        array('employee_position ep', 'a.id = ep.employee_id', 'LEFT'),
                        array('position ps', 'ep.position_id = ps.id', 'LEFT'),
                    );
                    $employee = $this->Employee->select($$var_1, $select, $where, $join);
                    $employee_position_id = $employee[0]->position_id;
                    $employee_position_name = $employee[0]->position_name;

                    if ($var_1 == "doctor_in_charge_id_2") {
                        $output[$i]->doctor_in_charge_2_position_id = $employee_position_id;
                        $output[$i]->doctor_in_charge_2_position_name = $employee_position_name;
                    } elseif ($var_1 == "doctor_in_charge_id_3") {
                        $output[$i]->doctor_in_charge_3_position_id = $employee_position_id;
                        $output[$i]->doctor_in_charge_3_position_name = $employee_position_name;
                    } elseif ($var_1 == "doctor_in_charge_id_4") {
                        $output[$i]->doctor_in_charge_4_position_id = $employee_position_id;
                        $output[$i]->doctor_in_charge_4_position_name = $employee_position_name;
                    } elseif ($var_1 == "doctor_in_charge_id_5") {
                        $output[$i]->doctor_in_charge_5_position_id = $employee_position_id;
                        $output[$i]->doctor_in_charge_5_position_name = $employee_position_name;
                    }
                }
            }

            $room_explode = explode('-', $room_name);
            $is_number = 0;
            for ($j=0; $j < count($room_explode); $j++) { 
                if (is_numeric($room_explode[$j])) {
                    $is_number = 1;
                    $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
                }
            }
            if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }

            $select = 'a.*, h.name as insurance_name, c.name as company_name';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.is_primary' => 1,
            );
            $join = array(
                array('helper h', 'a.insurance_id = h.code AND h.variable="insurance"', 'LEFT'),
                array('company c', 'a.company_id = c.id', 'LEFT'),
            );
            $registration_insurance = $this->Registration_Insurance->select(0, $select, $where, $join);

            $output[$i]->registration_insurance = $registration_insurance;
            
            // EWS SCORE
                $ews = $this->db->query("
                SELECT ewsi.total as ews_score, ewsi.aware
                FROM early_warning_score ews 
                INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                WHERE ews.registration_destination_id ='$registration_destination_id'
                AND ews.is_delete = 0
                AND ewsi.is_delete = 0
                ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                LIMIT 1
            ")->result();

            if (count($ews) == 0) {
                $ews = $this->db->query("
                    SELECT ewsi.total as ews_score, ewsi.aware
                    FROM early_warning_score ews 
                    INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                    INNER JOIN registration_destination rd ON ews.registration_destination_id = rd.id
                    WHERE rd.registration_id ='$registration_id'
                    AND ews.is_delete = 0
                    AND ewsi.is_delete = 0
                    ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                    LIMIT 1
                ")->result();
            }
            
            if (count($ews) > 0) {
                $output[$i]->aware = $ews[0]->aware;
                $output[$i]->ews_score = $ews[0]->ews_score;
            } else {
                $output[$i]->aware = 0;
                $output[$i]->ews_score = 0;
            }

            // LAST TTV
            $ttv = $this->db->query("
                SELECT vsm.observation_date,
                vsm.systolic,
                vsm.diastolic,
                vsm.pulse,
                vsm.temperature,
                vsm.respiratory_rate,
                vsm.oxygen_saturation,
                vsm.verbal_response,
                vsm.oxygen_type_id,
                vsm.sore_score,
                vsm.ews,
                vsm.blood_sugar,
                vsm.weight,
                vsm.height
                FROM vital_signs_monitoring vsm 
                WHERE vsm.registration_destination_id ='$registration_destination_id'
                AND vsm.is_delete = 0
                ORDER BY vsm.observation_date DESC
                LIMIT 1
            ")->result();

            if (count($ttv) == 0) {
                $ttv = $this->db->query("
                    SELECT vsm.observation_date,
                    vsm.systolic,
                    vsm.diastolic,
                    vsm.pulse,
                    vsm.temperature,
                    vsm.respiratory_rate,
                    vsm.oxygen_saturation,
                    vsm.verbal_response,
                    vsm.oxygen_type_id,
                    vsm.sore_score,
                    vsm.ews,
                    vsm.blood_sugar,
                    vsm.weight,
                    vsm.height
                    FROM vital_signs_monitoring vsm 
                    INNER JOIN registration_destination rd ON vsm.registration_destination_id = rd.id
                    WHERE rd.registration_id ='$registration_id'
                    AND vsm.is_delete = 0
                    ORDER BY vsm.observation_date DESC
                    LIMIT 1
                ")->result();
            }

            if (count($ttv) > 0) {
                $output[$i]->ttv['observation_date'] = $ttv[0]->observation_date;
                $output[$i]->ttv['systolic'] = $ttv[0]->systolic;
                $output[$i]->ttv['diastolic'] = $ttv[0]->diastolic;
                $output[$i]->ttv['pulse'] = $ttv[0]->pulse;
                $output[$i]->ttv['temperature'] = $ttv[0]->temperature;
                $output[$i]->ttv['respiratory_rate'] = $ttv[0]->respiratory_rate;
                $output[$i]->ttv['oxygen_saturation'] = $ttv[0]->oxygen_saturation;
                $output[$i]->ttv['verbal_response'] = $ttv[0]->verbal_response;
                $output[$i]->ttv['oxygen_type_id'] = $ttv[0]->oxygen_type_id;
                $output[$i]->ttv['sore_score'] = $ttv[0]->sore_score;
                $output[$i]->ttv['ews'] = $ttv[0]->ews;
                $output[$i]->ttv['blood_sugar'] = $ttv[0]->blood_sugar;
                $output[$i]->ttv['weight'] = $ttv[0]->weight;
                $output[$i]->ttv['height'] = $ttv[0]->height;
            } else {
                $output[$i]->ttv['observation_date'] = '';
                $output[$i]->ttv['systolic'] = 0;
                $output[$i]->ttv['diastolic'] = 0;
                $output[$i]->ttv['pulse'] = 0;
                $output[$i]->ttv['temperature'] = 0;
                $output[$i]->ttv['respiratory_rate'] = 0;
                $output[$i]->ttv['oxygen_saturation'] = 0;
                $output[$i]->ttv['verbal_response'] = '';
                $output[$i]->ttv['oxygen_type_id'] = 0;
                $output[$i]->ttv['sore_score'] = 0;
                $output[$i]->ttv['ews'] = 0;
                $output[$i]->ttv['blood_sugar'] = 0;
                $output[$i]->ttv['weight'] = 0;
                $output[$i]->ttv['height'] = 0;
            }

            $select = 'a.name';
            $where = array(
                'a.norm' => $norm,
                'a.is_delete' => 0,
            );
            $patient_allergy = $this->Patient_allergy->select(0, $select, $where);

            $output[$i]->patient_allergy = $patient_allergy;
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    // public function list_new_get($p = 0){
	// 	header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = "Registration_short";

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    //         if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
    //         if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    //         if(isset($q_strings['name'])) $this->$model->whereLike['c.name'] = $q_strings['name']; unset($q_strings['name']);
    //         if(isset($q_strings['health_facility_id'])) $this->$model->whereFields['e.id'] = $q_strings['health_facility_id'];
    //         if(isset($q_strings['health_facility_item_id'])) $this->$model->whereFields['d.id'] = $q_strings['health_facility_item_id']; unset($q_strings['health_facility_item_id']);
    //         // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
    //         // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
    //         // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
    //         // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
    //         // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
    //         // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
    //         // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
    //         if ($q_strings['health_facility_id'] !== '40f06534-07ec-4221-ad9d-dea08196079f') {
    //             if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
    //                 $this->$model->whereFields['a.register_date >='] = $q_strings['date_start'].' 00:00:00';
    //                 $this->$model->whereFields['a.register_date <='] = $q_strings['date_end'].' 23:59:59';
    //             } else {
    //                 $q_strings['date_start'] = date('Y-m-d');
    //                 $q_strings['date_end'] = date('Y-m-d');
    //                 $this->$model->whereFields['a.register_date >='] = $q_strings['date_start'].' 00:00:00';
    //                 $this->$model->whereFields['a.register_date <='] = $q_strings['date_end'].' 23:59:59';
    //             }
    //         }
    //         $this->$model->whereFields['a.is_booking'] = '1';
    //         $this->$model->whereFields['a.is_cancel'] = '0';
    //         $this->$model->whereFields['b.is_checkout'] = '0';
    //         if ($q_strings != []){
    //             $output = $this->$model->filter_data($q_strings,true);
    //         } else {
    //             $output = $this->$model->read_ssr($id);
    //         }
    //         for ($i=0; $i < count($output->rows); $i++) {
    //             $this->Registration_Insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
    //             $this->Registration_Insurance->whereFields['a.is_primary'] = 1; 
    //             $this->Registration_Insurance->whereFields['a.registration_destination_id'] = $output->rows[$i]->registration_destination_id;
    //             $reg_insurance = $this->Registration_Insurance->read();
    //             $checkin = new DateTime($output->rows[$i]->checkin_date);
    //             $checkout = new DateTime(date('Y-m-d H:i:s'));
    //             $diff_checkin = $checkout->diff($checkin);
    //             $output->rows[$i]->los = $diff_checkin->d." Hari ". $diff_checkin->h." Jam";
    //             for ($j=0; $j < count($reg_insurance); $j++) { 
    //                 $output->rows[$i]->registration_insurance[$j] = $reg_insurance[$j];
    //             }
    //         }
    //         if ($output->rows != [] && $id != 0) {
    //             $response_data = $output->rows[0];
    //             $error   = false;
    //         } else if($output->rows != [] or $id == 0) {
    //             $response_data = $output;
    //             $error   = false;
    //         } else {
    //             $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
    //             return;
    //         }
    //         if ($error == true) {
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $this->REST_Return(200, 'SUCCESS', $response_data);
    //         }
    //         return;
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
	// }

    public function list_new_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['a.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }

        if(isset($q_strings['health_facility_item_id'])) {
            $where['hfi.id'] = $q_strings['health_facility_item_id'];
            unset($where['health_facility_item_id']);
        }

        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
        
        if ($where['hf.id'] !== '40f06534-07ec-4221-ad9d-dea08196079f') {
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $date_start = $q_strings['date_start'] . ' 00:00:00';
                $date_end = $q_strings['date_end'] . ' 23:59:59';
            }

            $where['a.register_date >='] = $date_start;
            $where['a.register_date <='] = $date_end;
        } 

        unset($where['date_start']);
        unset($where['date_end']);
        
        $select = 'rd.id, 
        rd.checkin_date, 
        rd.register_date, 
        a.norm, 
        a.code, 
        rd.health_facility_item_id, 
        hfi.name as health_facility_item_name, 
        hf.id as health_facility_id, 
        hf.name as health_facility_name, 
        rd.id as registration_destination_id, 
        rd.is_new as registration_destination_is_new, 
        rd.doctor_in_charge_id, 
        e.fullname as doctor_in_charge_name, 
        hte.type_how_to_enter_id, 
        h5.name as type_how_to_enter_name, 
        b.name as bed_name, 
        r.name as room_name';

        $where['a.is_booking'] = '1';
        $where['a.is_cancel'] = '0';
        $where['rd.is_checkout'] = '0';

        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room r', 'b.room_id = r.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', "a.type_register_id = h.code and h.variable='type_register'", 'INNER'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
            array('helper h5', "hte.type_how_to_enter_id = h5.code and h5.variable='type_how_to_enter'", 'INNER'),
        );
        if (isset($q_strings['name']) || isset($q_strings['norm']) || isset($q_strings['sex']) || isset($q_strings['is_new'])) {
            $join[] = array('patient p', 'a.norm = p.norm', 'INNER');
        }
        $output = $this->$model->select(0, $select, $where, $join);

        $norms = [];

        for ($i=0; $i < count($output); $i++) {
            $norms[] = $output[$i]->norm;
            $registration_destination_id = $output[$i]->registration_destination_id;
            $checkin_date = $output[$i]->register_date;

            $select = 'h.name as insurance_name, c.name as company_name';
            $where_insu = array('a.is_primary' => 1);
            $join_insu = array(
                array('helper h', 'a.insurance_id = h.code AND h.variable="insurance"', 'LEFT'),
		        array('company c', 'a.company_id = c.id', 'LEFT'),
            );
            $registration_insurance = $this->Registration_Insurance->select($registration_destination_id, $select, $where_insu, $join_insu);

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime(date('Y-m-d H:i:s'));
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";
            
            if ($registration_insurance) {
                $output[$i]->registration_insurance[0] = $registration_insurance[0];
            }
        }

        if (count($norms) > 0) {
            $param = implode(',',$norms);
    
            $patient = $this->db->query("
                SELECT p.norm, p.name, p.fullname, p.is_new, p.birth_date, p.birth_place, p.address, p.phone, h.name as sex_name, h2.name AS alias_name
                FROM patient p
                INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
                INNER JOIN helper h2 ON p.alias = h2.code AND h2.variable = 'alias'
                WHERE p.norm in ($param)
            ")->result();
    
            $dt_patient = [];
            for ($i=0; $i < count($patient); $i++) { 
                $dt_patient[$patient[$i]->norm] = array(
                    'name' => $patient[$i]->name,
                    'fullname' => $patient[$i]->fullname,
                    'is_new' => $patient[$i]->is_new,
                    'birth_date' => $patient[$i]->birth_date,
                    'birth_place' => $patient[$i]->birth_place,
                    'address' => $patient[$i]->address,
                    'phone' => $patient[$i]->phone,
                    'sex_name' => $patient[$i]->sex_name,
                    'alias_name' => $patient[$i]->alias_name,
                );
            }
    
            for ($i=0; $i < count($output); $i++) { 
                $norm = $output[$i]->norm;
                $output[$i]->name = $dt_patient[$norm]['name'];
                $output[$i]->fullname = $dt_patient[$norm]['fullname'];
                $output[$i]->is_new = $dt_patient[$norm]['is_new'];
                $output[$i]->birth_date = $dt_patient[$norm]['birth_date'];
                $output[$i]->birth_place = $dt_patient[$norm]['birth_place'];
                $output[$i]->address = $dt_patient[$norm]['address'];
                $output[$i]->phone = $dt_patient[$norm]['phone'];
                $output[$i]->sex_name = $dt_patient[$norm]['sex_name'];
                $output[$i]->alias_name = $dt_patient[$norm]['alias_name'];
            }
        }

        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
        );

        if (isset($q_strings['name']) || isset($q_strings['norm']) || isset($q_strings['sex']) || isset($q_strings['is_new'])) {
            $join[] = array('patient p', 'a.norm = p.norm', 'INNER');
        }
        if (isset($q_strings['sep']) || isset($q_strings['insurance_id']) || isset($q_strings['company_id'])) {
            $join[] = array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER');
        }
        if (isset($q_strings['health_facility_id']) || isset($q_strings['health_facility_item_id'])) {
            $join[] = array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER');
            $join[] = array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER');
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

    public function list_new_inv_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Registration_inv";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['a.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }

        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }

        if(isset($q_strings['health_facility_item_id'])) {
            $where['hfi.id'] = $q_strings['health_facility_item_id'];
            unset($where['health_facility_item_id']);
        }

        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
        
        if ($where['hf.id'] !== '40f06534-07ec-4221-ad9d-dea08196079f') {
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $date_start = $q_strings['date_start'] . ' 00:00:00';
                $date_end = $q_strings['date_end'] . ' 23:59:59';
            }

            $where['a.register_date >='] = $date_start;
            $where['a.register_date <='] = $date_end;
        } 

        unset($where['date_start']);
        unset($where['date_end']);
        
        $select = 'rd.id, rd.registration_id, rd.register_date, rd.checkin_date, a.norm, a.code, rd.health_facility_item_id, hfi.name as health_facility_item_name, hf.id as health_facility_id, hf.name as health_facility_name, rd.id as registration_destination_id, rd.is_new as registration_destination_is_new, rd.doctor_in_charge_id, e.fullname as doctor_in_charge_name, hte.type_how_to_enter_id, h5.name as type_how_to_enter_name, p.name, p.fullname, h4.name as alias_name, p.birth_date, p.birth_place, p.address, p.phone, h3.name as sex_name, b.name as bed_name, r.name as room_name';

        $where['a.is_booking'] = '1';
        $where['a.is_cancel'] = '0';
        // $where['rd.is_checkout'] = '0';

        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id', 'LEFT'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room r', 'b.room_id = r.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', "a.type_register_id = h.code and h.variable='type_register'", 'INNER'),
            // array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'LEFT'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('helper h4', "p.alias = h4.code and h4.variable='alias'", 'INNER'),
            // array('company c', "ri.company_id = c.id", 'LEFT'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
            array('helper h5', "hte.type_how_to_enter_id = h5.code and h5.variable='type_how_to_enter'", 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        for ($i=0; $i < count($output); $i++) {
            $registration_destination_id = $output[$i]->registration_destination_id;
            $registration_id = $output[$i]->registration_id;
            $checkin_date = $output[$i]->register_date;

            $select = 'h.name as insurance_name, c.name as company_name';
            $where_insu = array('a.is_primary' => 1);
            $join_insu = array(
                array('helper h', 'a.insurance_id = h.code AND h.variable="insurance"', 'LEFT'),
                array('company c', 'a.company_id = c.id', 'LEFT'),
            );
            $registration_insurance = $this->Registration_Insurance->select($registration_destination_id, $select, $where_insu, $join_insu);

            
            $select = 'a.id as invoice_id';
            $where_inv = array('a.registration_id' => $registration_id,'a.is_delete' => 0);
            $inv = $this->Transaction_invoice->select(0, $select, $where_inv);

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime(date('Y-m-d H:i:s'));
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";
            
            if ($registration_insurance) {
                $output[$i]->registration_insurance[0] = $registration_insurance[0];
            }
            
            if ($inv) {
                $output[$i]->inv = $inv;
            } else {
                $output[$i]->inv = null;
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
            if(isset($q_strings['name'])) $this->$model->whereLike['c.name'] = $q_strings['name']; unset($q_strings['name']);
            if(isset($q_strings['health_facility_id'])) $this->$model->whereFields['e.id'] = $q_strings['health_facility_id'];
            if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                if(isset($q_strings['is_checkout'])) $this->$model->whereFields['b.is_checkout'] = $q_strings['is_checkout'];
            } else {
                unset($q_strings['is_checkout']);
            }
            // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
            if ($q_strings['health_facility_id'] !== '40f06534-07ec-4221-ad9d-dea08196079f') {
                if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                    $this->$model->whereFields['a.register_date >='] = $q_strings['date_start'].' 00:00:00';
                    $this->$model->whereFields['a.register_date <='] = $q_strings['date_end'].' 23:59:59';
                } else {
                    $q_strings['date_start'] = date('Y-m-d');
                    $q_strings['date_end'] = date('Y-m-d');
                    $this->$model->whereFields['a.register_date >='] = $q_strings['date_start'].' 00:00:00';
                    $this->$model->whereFields['a.register_date <='] = $q_strings['date_end'].' 23:59:59';
                }
            }
            $this->$model->whereFields['a.is_booking'] = '1';
            $this->$model->whereFields['a.is_cancel'] = '0';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Registration_Insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
                $this->Registration_Insurance->whereFields['a.is_primary'] = 1; 
                $this->Registration_Insurance->whereFields['a.registration_destination_id'] = $output->rows[$i]->registration_destination_id;
                $reg_insurance = $this->Registration_Insurance->read();
                for ($j=0; $j < count($reg_insurance); $j++) { 
                    $output->rows[$i]->registration_insurance[$j] = $reg_insurance[$j];
                }
                if ($output->rows[$i]->health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                    $output->rows[$i]->health_facility_item_name = $output->rows[$i]->room_name.', BED - '.$output->rows[$i]->bed_name;
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

    public function list_fast_backup_get($p = 0){
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
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->$model->whereLike['p.name'] = $name;
            unset($where['name']);
        }

        if(isset($q_strings['norm_like'])) {
            $norm_like = $q_strings['norm_like'];
            $this->$model->whereLike['p.norm'] = $norm_like;
            unset($where['norm_like']);
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
            unset($where['norm']);
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['birth_date'])) {
            $birth_date = $q_strings['birth_date'];
            $where['p.birth_date'] = $birth_date;
            unset($where['birth_date']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                    $where['rd.is_checkout'] = 0;
                }
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        $hf_id = $q_strings['health_facility_id'] ?? null;

        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_item_id'])) {
            $where['hfi.id'] = $q_strings['health_facility_item_id'];
            unset($where['health_facility_item_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
        
        if(isset($q_strings['exit_id'])) {
            $where['rd.exit_id'] = $q_strings['exit_id'];
            unset($where['exit_id']);
        }

        if(isset($q_strings['type_visit_id'])) {
            $where['a.type_visit_id'] = $q_strings['type_visit_id'];
            unset($where['type_visit_id']);
        }
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
            $where['rd.checkin_date >='] = $date_start;
            $where['rd.checkin_date <='] = $date_end;
            $where_doc['a.checkin_date >='] = $date_start;
            $where_doc['a.checkin_date <='] = $date_end;

            unset($where['date_start']);
            unset($where['date_end']);
        } 

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
            
            $select = 'r.id';
            // $is_checkout = $q_strings['is_checkout'];
            // if ($is_checkout != null) {
            //     $where_doc = array('a.is_checkout' => $is_checkout);
            // } else {
            //     $where_doc = array();
            // }

            if (isset($where['hf.id'])) {
                $where_doc['hfi.health_facility_id'] = $where['hf.id'];
            }
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);

            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);

            $where_doc['a.doctor_in_charge_id_6'] = $doctor_in_charge_id;
            $employee_6 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_6']);

            $where_doc['a.doctor_in_charge_id_7'] = $doctor_in_charge_id;
            $employee_7 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_7']);

            $where_doc['a.doctor_in_charge_id_8'] = $doctor_in_charge_id;
            $employee_8 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_8']);

            $where_doc['a.doctor_in_charge_id_9'] = $doctor_in_charge_id;
            $employee_9 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_9']);

            $where_doc['a.doctor_in_charge_id_10'] = $doctor_in_charge_id;
            $employee_10 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_10']);

            $where_doc['a.doctor_in_charge_id_11'] = $doctor_in_charge_id;
            $employee_11 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_11']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5, $employee_6, $employee_7, $employee_8, $employee_9, $employee_10, $employee_11);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            } else {
                $this->$model->whereInFields['a.id'] = array('999');
            }
        }

        $where['a.is_cancel'] = 0;
    
        $select = 'a.id, 
        a.code_booking, 
        rd.id as registration_destination_id, 
        ri.is_cob, 
        ri.sep, 
        ri.number_insurance, 
        ri.insurance_id, 
        rd.code_queue, 
        rd.checkin_date as register_date, 
        rd.checkin_date, 
        rd.is_checkout, 
        rd.exit_id, 
        ex.name as exit_name, 
        rd.sts, 
        rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7, 
        a.code, 
        a.how_to_enter_id, 
        hte.name as how_to_enter_name, 
        c.name as company_name, 
        a.type_register_id,
        a.type_visit_id, 
        a.norm, 
        hf.id as health_facility_id, 
        hf.name as health_facility_name, 
        rd.health_facility_item_id, 
        hfi.name as health_facility_item_name, 
        rd.doctor_in_charge_id, 
        rd.doctor_in_charge_id_2, 
        rd.doctor_in_charge_id_3, 
        rd.doctor_in_charge_id_4, 
        rd.doctor_in_charge_id_5, 
        b.id as bed_id, 
        b.name as bed_name, 
        br.id as room_id,
        br.name as room_name, 
        br.type_room_id,
        br.code as room_code,
        e.nickname as doctor_nickname, 
        e.fullname as doctor_in_charge_name, 
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_2) as doctor_in_charge_name_2,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_3) as doctor_in_charge_name_3,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_4) as doctor_in_charge_name_4,
        (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_5) as doctor_in_charge_name_5,
        e.bride_bpjs, 
        ms.is_special_cmg, 
        ms.code_inacbg, 
        ms.desc_inacbg, 
        ms.percentage, 
        ms.cost_inacbg, 
        rd.queue,
        rd.is_transfer,
        rd.date_control
        ';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room br', 'b.room_id = br.id', 'LEFT'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            // array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            // array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            // array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            // array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
            array('medical_services ms', 'rd.id = ms.registration_destination_id', 'LEFT'),
        );

        $type_register_arr = get_variable('type_register');
        $type_visit_arr = get_variable('type_visit');
        $type_room_arr = get_variable('type_room');
        $insurance_arr = get_variable('insurance');

        if (isset($q_strings['name']) || isset($q_strings['norm']) || isset($q_strings['sex']) || isset($q_strings['is_new']) || isset($q_strings['norm_like']) || isset($q_strings['birth_date'])) {            
            $health_facility_id = $where['hf.id'];

            $wherein = '';
            if ($this->$model->whereInFields != null) {
                $wherein = 'AND a.id IN ("'.join('","', $this->$model->whereInFields['a.id']).'")';
            }

            $name_like = '';
            $norm_like_q = '';

            if ($name != null) {
                $name_like = "AND  `p`.`name` LIKE '%$name%' ESCAPE '!'";
            }

            if ($norm_like != null) {
                $norm_like_q = "AND  `p`.`norm` LIKE '%$norm_like%' ESCAPE '!'";
            }

            $name_data = $this->db->query("
                SELECT `a`.`id`
                FROM `registration` as `a`
                INNER JOIN `registration_destination` `rd` ON `a`.`id` = `rd`.`registration_id`
                INNER JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary`=1
                INNER JOIN `health_facility_item` `hfi` ON `rd`.`health_facility_item_id` = `hfi`.`id`
                INNER JOIN `health_facility` `hf` ON `hfi`.`health_facility_id` = `hf`.`id`
                INNER JOIN `patient` `p` ON `a`.`norm` = `p`.`norm`
                AND `hf`.`id` = '$health_facility_id'
                AND `a`.`is_cancel` = 0
                $name_like
                $norm_like_q
                $wherein
                ")->result();
                // WHERE `rd`.`is_checkout` = '$is_checkout'

            if (count($name_data) > 0 && $this->$model->whereInFields == null) {
                $this->$model->whereInFields['a.id'] = [];
            } else {
                $this->$model->whereInFields['a.id'] = array('999');
            }
            
            for ($n=0; $n < count($name_data); $n++) { 
                $name_id = $name_data[$n]->id;
                array_push($this->$model->whereInFields['a.id'], $name_id);
            }

            // $join[] = array('patient p', 'a.norm = p.norm', 'INNER');
            unset($this->$model->whereLike['p.name']);
            unset($this->$model->whereLike['p.norm']);
        }
        $order = array('rd.checkin_date' => 'desc');
        if ($hf_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // ORDER BY TRIASE
            $select = $select . ', tn.triage_result';
            $join[] = array('triage_new tn', 'rd.id = tn.registration_destination_id', 'LEFT');
            unset($order['rd.checkin_date']);
            $order = array('tn.triage_result' => 'asc');
        }
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $norms = [];
        for ($i=0; $i < count($output); $i++) { 
            $norms[] = $output[$i]->norm;
            $checkin_date = $output[$i]->checkin_date;

            $registration_destination_id = $output[$i]->registration_destination_id;

            $type_register_id = $output[$i]->type_register_id;
            $type_visit_id = $output[$i]->type_visit_id;
            $insurance_id = $output[$i]->insurance_id;
            
            if ($hf_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $type_room_id = $output[$i]->type_room_id;
                $output[$i]->type_room_name = $type_room_arr[$type_room_id];
            }

            if ($type_visit_id != null) {
                $output[$i]->type_visit_name = $type_visit_arr[$type_visit_id];
            }

            $output[$i]->type_register_name = $type_register_arr[$type_register_id];
            $output[$i]->insurance_name = $insurance_arr[$insurance_id];

            $room_name = $output[$i]->room_name;
            $room_code = $output[$i]->room_code;

            $room_explode = explode('-', $room_name);
            $is_number = 0;
            for ($j=0; $j < count($room_explode); $j++) { 
                if (is_numeric($room_explode[$j])) {
                    $is_number = 1;
                    $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
                }
            }
            if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime(date('Y-m-d H:i:s'));
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";

            $output[$i]->consultation_type_id = 0;
            if (isset($q_strings['doctor_in_charge_id'])) {
                $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

                $select_con = 'a.consultation_type_id, a.status';
                $where_con = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.consultant_id' => $doctor_in_charge_id,
                    'a.is_cancel' => 0,
                );
                $consultation = $this->Consultation->select(0, $select_con, $where_con);
                if (count($consultation) > 0) {
                    $consultation_type_id = $consultation[0]->consultation_type_id;
                    $status = $consultation[0]->status;

                    $output[$i]->consultation_type_id = $consultation_type_id;
                    
                    if ($consultation_type_id == 2) {
                        if ($status != 0) {
                            $output[$i]->consultation_type_id = 0;
                        }
                    }
                }
            }
        }

        if (count($norms) > 0) {
            $param = implode(',',$norms);
    
            $patient = $this->db->query("
                SELECT p.norm, p.name, p.is_new, p.birth_date, h.name as sex_name, h2.name AS alias
                FROM patient p
                INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
                INNER JOIN helper h2 ON p.alias = h2.code AND h2.variable = 'alias'
                WHERE p.norm in ($param)
            ")->result();
    
            $dt_patient = [];
            for ($i=0; $i < count($patient); $i++) { 
                $dt_patient[$patient[$i]->norm] = array(
                    'name' => $patient[$i]->name,
                    'is_new' => $patient[$i]->is_new,
                    'birth_date' => $patient[$i]->birth_date,
                    'sex_name' => $patient[$i]->sex_name,
                    'alias' => $patient[$i]->alias,
                );
            }
    
            for ($i=0; $i < count($output); $i++) { 
                $norm = $output[$i]->norm;
                $output[$i]->name = $dt_patient[$norm]['name'];
                $output[$i]->is_new = $dt_patient[$norm]['is_new'];
                $output[$i]->birth_date = $dt_patient[$norm]['birth_date'];
                $output[$i]->sex_name = $dt_patient[$norm]['sex_name'];
                $output[$i]->alias = $dt_patient[$norm]['alias'];

                $birth_date = $dt_patient[$norm]['birth_date'];

                $age = count_age($birth_date);

                if ($age > 18) {
                    // EWS SCORE
                    $ews = $this->db->query("
                        SELECT ewsi.total
                        FROM early_warning_score ews 
                        INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                        WHERE ews.registration_destination_id ='$registration_destination_id'
                        AND ews.is_delete = 0
                        AND ewsi.is_delete = 0
                        ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                        LIMIT 1
                    ")->result();

                    if (count($ews) > 0) {
                        $output[$i]->ews_score = $ews[0]->total;
                    } else {
                        $output[$i]->ews_score = 99;
                    }
                } else {
                    // PEWS SCORE
                    $pews = $this->db->query("
                        SELECT pi.total 
                        FROM pews p 
                        INNER JOIN pews_item pi ON p.id = pi.pews_id 
                        WHERE p.registration_destination_id = '$registration_destination_id'
                        AND p.is_delete = 0
                        AND pi.is_delete = 0
                        ORDER BY p.observation_date DESC, pi.date_added DESC
                        LIMIT 1
                    ")->result();

                    if (count($pews) > 0) {
                        $output[$i]->ews_score = $pews[0]->total;
                    } else {
                        $output[$i]->ews_score = 99;
                    }
                }
            }

            if ($hf_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                usort($output, function ($a, $b) {
                    return (float)$b->ews_score <=> (float)$a->ews_score;
                });
            }
        }

        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER')
        );

        if (isset($q_strings['name']) || isset($q_strings['norm']) || isset($q_strings['sex']) || isset($q_strings['is_new']) || isset($q_strings['norm_like']) || isset($q_strings['birth_date'])) {
            $join[] = array('patient p', 'a.norm = p.norm', 'INNER');
        }
        if (isset($q_strings['health_facility_id']) || isset($q_strings['health_facility_item_id'])) {
            $join[] = array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER');
            $join[] = array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER');
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
            
        $limit = $q_strings['limit'] ?? 50;
        $offset = $q_strings['offset'] ?? 0;

        $this->Registration_Destination->limit = $limit;
        $this->Registration_Destination->offset = $offset;

        $where = array();

        $health_facility_id = '';
        if(isset($q_strings['health_facility_id'])) {
            $health_facility_id = $q_strings['health_facility_id'];
            $where['hfi.health_facility_id'] = $health_facility_id;
        }

        if(isset($q_strings['health_facility_item_id'])) {
            $health_facility_item_id = $q_strings['health_facility_item_id'];
            $where['hfi.id'] = $health_facility_item_id;
        }

        // $range_date = '';
        $date_start = '';
        $date_end = '';
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
        } 

        $is_checkout = $q_strings['is_checkout'] ?? null;
        if ($is_checkout == null) {
            $is_checkout = 0;
            $where['a.is_checkout'] = 0;
        } else {
            $where['a.is_checkout'] = $is_checkout;
            if ($is_checkout != 0) {
                if ($date_start == '' || $date_end == '') {
                    $this->REST_Return(400, 'date_start & date_end is required if is_checkout other than 0');
                    return;
                }

                // $range_date = "AND a.checkin_date BETWEEN '$date_start' AND '$date_end'";

                $where['a.is_checkout'] = $is_checkout;
                $where['a.checkin_date >='] = $date_start;
                $where['a.checkin_date <='] = $date_end;
            }
        }

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->Registration_Destination->whereLike['p.name'] = $name;
        }

        if(isset($q_strings['norm_like'])) {
            $norm_like = $q_strings['norm_like'];
            $this->Registration_Destination->whereLike['p.norm'] = $norm_like;
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
        }

        if(isset($q_strings['exit_id']) && $q_strings['exit_id'] != 'null') {
            $exit_id = $q_strings['exit_id'];
            $where['a.exit_id'] = $exit_id;
        }

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

            $where_doctor = "(
                a.doctor_in_charge_id = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_2 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_3 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_4 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_5 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_6 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_7 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_8 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_9 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_10 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_11 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_12 = '$doctor_in_charge_id'
            )";

            $this->db->where($where_doctor);
        }

        // if(isset($q_strings['is_new'])) {
        //     $is_new = $q_strings['is_new'];
        //     $where['p.is_new'] = $is_new;
        //     unset($where['is_new']);
        // }
        
        // if(isset($q_strings['insurance_id'])) {
        //     $where['ri.insurance_id'] = $q_strings['insurance_id'];
        //     unset($where['insurance_id']);
        // }
        
        // if(isset($q_strings['company_id'])) {
        //     $where['ri.company_id'] = $q_strings['company_id'];
        //     unset($where['company_id']);
        // }
        
        // if(isset($q_strings['exit_id'])) {
        //     $where['rd.exit_id'] = $q_strings['exit_id'];
        //     unset($where['exit_id']);
        // }

        // if(isset($q_strings['type_visit_id'])) {
        //     $where['a.type_visit_id'] = $q_strings['type_visit_id'];
        //     unset($where['type_visit_id']);
        // }

        // if(isset($q_strings['sep'])) {
        //     $sep = $q_strings['sep'];
        //     $where['ri.sep'] = $sep;
        //     unset($where['sep']);
        // }

        // if(isset($q_strings['code'])) {
        //     $code = $q_strings['code'];
        //     $where['a.code'] = $code;
        //     unset($where['code']);
        // }

        // if(isset($q_strings['sex'])) {
        //     $sex = $q_strings['sex'];
        //     $where['p.sex'] = $sex;
        //     unset($where['sex']);
        // }

        // if(isset($q_strings['birth_date'])) {
        //     $birth_date = $q_strings['birth_date'];
        //     $where['p.birth_date'] = $birth_date;
        //     unset($where['birth_date']);
        // }

        // $output = $this->db->query("
        // SELECT SQL_NO_CACHE 
        // r.id,
        // r.code_booking, 
        // rd.id AS registration_destination_id, 
        // ri.is_cob, 
        // ri.sep, 
        // ri.number_insurance, 
        // ri.insurance_id, 
        // rd.code_queue, 
        // rd.checkin_date AS register_date, 
        // rd.checkin_date, 
        // rd.is_checkout, 
        // rd.exit_id,
        // ex.name as exit_name, 
        // rd.sts, 
        // rd.call_nurse_stamp,
        // rd.call_doctor_stamp,
        // rd.done_doctor_stamp,
        // rd.call_pharmacy_stamp,
        // rd.done_pharmacy_stamp,
        // rd.date_task_1,
        // rd.date_task_2,
        // rd.date_task_3,
        // rd.date_task_4,
        // rd.date_task_5,
        // rd.date_task_6,
        // rd.date_task_7,
        // r.code, 
        // r.how_to_enter_id, 
        // hte.name as how_to_enter_name, 
        // c.name as company_name, 
        // r.type_register_id,
        // r.type_visit_id, 
        // r.norm, 
        // hf.id as health_facility_id, 
        // hf.name as health_facility_name, 
        // rd.health_facility_item_id, 
        // hfi.name as health_facility_item_name, 
        // rd.doctor_in_charge_id, 
        // rd.doctor_in_charge_id_2, 
        // rd.doctor_in_charge_id_3, 
        // rd.doctor_in_charge_id_4, 
        // rd.doctor_in_charge_id_5, 
        // b.id as bed_id, 
        // b.name as bed_name, 
        // br.id as room_id,
        // br.name as room_name, 
        // br.type_room_id,
        // br.code as room_code,
        // e.nickname as doctor_nickname, 
        // e.fullname as doctor_in_charge_name,
        // e2.fullname as doctor_in_charge_name_2,
        // e3.fullname as doctor_in_charge_name_3,
        // e4.fullname as doctor_in_charge_name_4,
        // e5.fullname as doctor_in_charge_name_5,
        // e.bride_bpjs, 
        // ms.is_special_cmg, 
        // ms.code_inacbg, 
        // ms.desc_inacbg, 
        // ms.percentage, 
        // ms.cost_inacbg, 
        // rd.queue,
        // rd.is_transfer,
        // rd.date_control,
        // tn.triage_result,
        // p.name,
        // p.is_new,
        // p.birth_date,
        // h.name as type_register_name,
        // h2.name as type_visit_name,
        // h3.name as type_room_name,
        // h4.name as insurance_name,
        // h5.name as sex_name,
        // h6.name as alias_name
        // FROM registration_destination rd
        // INNER JOIN registration r ON rd.registration_id = r.id
        // INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        // INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        // INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
        // INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
        // INNER JOIN how_to_enter hte ON r.how_to_enter_id = hte.id
        // INNER JOIN patient p ON r.norm = p.norm
        // LEFT JOIN helper h ON r.type_register_id = h.code AND h.variable = 'type_register'
        // LEFT JOIN helper h2 ON r.type_visit_id = h2.code AND h2.variable = 'type_visit'
        // LEFT JOIN helper h4 ON ri.insurance_id = h4.code AND h4.variable = 'insurance'
        // LEFT JOIN helper h5 ON p.sex = h5.code AND h5.variable = 'sex'
        // LEFT JOIN helper h6 ON p.alias = h6.code AND h6.variable = 'alias'
        // LEFT JOIN bed b ON rd.bed_id = b.id
        // LEFT JOIN room br ON b.room_id = br.id
        // LEFT JOIN helper h3 ON br.type_room_id = h3.code AND h3.variable = 'type_room'  
        // LEFT JOIN `exit` ex ON rd.exit_id = ex.id
        // LEFT JOIN company c ON ri.company_id = c.id
        // LEFT JOIN medical_services ms ON rd.id = ms.registration_destination_id
        // LEFT JOIN triage_new tn ON rd.id = tn.registration_destination_id
        // LEFT JOIN employee e2 ON rd.doctor_in_charge_id_2 = e2.id
        // LEFT JOIN employee e3 ON rd.doctor_in_charge_id_3 = e3.id
        // LEFT JOIN employee e4 ON rd.doctor_in_charge_id_4 = e4.id
        // LEFT JOIN employee e5 ON rd.doctor_in_charge_id_5 = e5.id
        // WHERE $health_facility
        // rd.is_checkout = $is_checkout
        // AND r.is_cancel = 0
        // $range_date
        // LIMIT $limit
        // OFFSET $offset
        // ")->result();

        $select = '
            r.id,
            r.code_booking, 
            a.id AS registration_destination_id, 
            ri.is_cob, 
            ri.sep, 
            ri.number_insurance, 
            ri.insurance_id, 
            ri.service_class_name, 
            a.code_queue, 
            a.register_date, 
            a.checkin_date, 
            a.is_checkout, 
            a.exit_id,
            ex.name as exit_name, 
            a.sts, 
            a.call_nurse_stamp,
            a.call_doctor_stamp,
            a.done_doctor_stamp,
            a.call_pharmacy_stamp,
            a.done_pharmacy_stamp,
            a.date_task_1,
            a.date_task_2,
            a.date_task_3,
            a.date_task_4,
            a.date_task_5,
            a.date_task_6,
            a.date_task_7,
            r.code, 
            r.how_to_enter_id, 
            hte.name as how_to_enter_name, 
            c.name as company_name, 
            r.type_register_id,
            r.type_visit_id, 
            r.norm, 
            hf.id as health_facility_id, 
            hf.name as health_facility_name, 
            a.health_facility_item_id, 
            hfi.name as health_facility_item_name, 
            a.doctor_in_charge_id, 
            a.doctor_in_charge_id_2, 
            a.doctor_in_charge_id_3, 
            a.doctor_in_charge_id_4, 
            a.doctor_in_charge_id_5, 
            a.doctor_in_charge_id_6, 
            a.doctor_in_charge_id_7, 
            a.doctor_in_charge_id_8, 
            a.doctor_in_charge_id_9, 
            a.doctor_in_charge_id_10, 
            a.doctor_in_charge_id_11, 
            a.doctor_in_charge_id_12, 
            b.id as bed_id, 
            b.name as bed_name, 
            br.id as room_id,
            br.name as room_name, 
            br.type_room_id,
            br.code as room_code,
            e.nickname as doctor_nickname, 
            e.fullname as doctor_in_charge_name,
            e2.fullname as doctor_in_charge_name_2,
            e3.fullname as doctor_in_charge_name_3,
            e4.fullname as doctor_in_charge_name_4,
            e5.fullname as doctor_in_charge_name_5,
            e6.fullname as doctor_in_charge_name_6,
            e7.fullname as doctor_in_charge_name_7,
            e8.fullname as doctor_in_charge_name_8,
            e9.fullname as doctor_in_charge_name_9,
            e10.fullname as doctor_in_charge_name_10,
            e11.fullname as doctor_in_charge_name_11,
            e11.fullname as doctor_in_charge_name_12,
            e.bride_bpjs, 
            ms.is_special_cmg, 
            ms.code_inacbg, 
            ms.desc_inacbg, 
            ms.percentage, 
            ms.cost_inacbg, 
            a.queue,
            a.registration_destination_ref_id,
            a.is_transfer,
            a.date_control,
            tn.triage_result,
            p.name,
            p.is_new,
            p.birth_date,
            h.name as type_register_name,
            h2.name as type_visit_name,
            h3.name as type_room_name,
            h4.name as insurance_name,
            h5.name as sex_name,
            h6.name as alias
        ';
        $where['r.is_cancel'] = 0;
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('registration_insurance ri', 'a.id = ri.registration_destination_id AND ri.is_primary = 1', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('how_to_enter hte', 'r.how_to_enter_id = hte.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', 'r.type_register_id = h.code AND h.variable = "type_register"', 'LEFT'),
            array('helper h2', 'r.type_visit_id = h2.code AND h2.variable = "type_visit"', 'LEFT'),
            array('helper h4', 'ri.insurance_id = h4.code AND h4.variable = "insurance"', 'LEFT'),
            array('helper h5', 'p.sex = h5.code AND h5.variable = "sex"', 'LEFT'),
            array('helper h6', 'p.alias = h6.code AND h6.variable = "alias"', 'LEFT'),
            array('bed b', 'a.bed_id = b.id', 'LEFT'),
            array('room br', 'b.room_id = br.id', 'LEFT'),
            array('helper h3', 'br.type_room_id = h3.code AND h3.variable = "type_room"', 'LEFT'),
            array('exit ex', 'a.exit_id = ex.id', 'LEFT'),
            array('company c', 'ri.company_id = c.id', 'LEFT'),
            array('medical_services ms', 'a.id = ms.registration_destination_id', 'LEFT'),
            array('triage_new tn', 'a.id = tn.registration_destination_id', 'LEFT'),
            array('employee e2', 'a.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            array('employee e3', 'a.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            array('employee e4', 'a.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            array('employee e5', 'a.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('employee e6', 'a.doctor_in_charge_id_6 = e6.id', 'LEFT'),
            array('employee e7', 'a.doctor_in_charge_id_7 = e7.id', 'LEFT'),
            array('employee e8', 'a.doctor_in_charge_id_8 = e8.id', 'LEFT'),
            array('employee e9', 'a.doctor_in_charge_id_9 = e9.id', 'LEFT'),
            array('employee e10', 'a.doctor_in_charge_id_10 = e10.id', 'LEFT'),
            array('employee e11', 'a.doctor_in_charge_id_11 = e11.id', 'LEFT'),
            array('employee e12', 'a.doctor_in_charge_id_12 = e11.id', 'LEFT'),
        );
        $order = array('a.checkin_date' => 'desc');
        if ($health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // ORDER BY TRIASE
            $order = array('tn.triage_result' => 'asc');
        } else if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // ORDER BY ANTRIAN
            $order = array('a.queue' => 'asc');
        }
        $output = $this->Registration_Destination->select(0, $select, $where, $join, $order);

        // var_dump($this->db->last_query());
        // die();

        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_id = $output[$i]->registration_destination_id;
            $registration_destination_ref_id = $output[$i]->registration_destination_ref_id;
            if ($registration_destination_ref_id) {
                $select_ref = "
                    a.health_facility_item_id,
                ";
                $this->Registration_Destination->whereFields = array();
                $this->Registration_Destination->whereLike = array();
                $dt_reg_ref = $this->Registration_Destination->select($registration_destination_ref_id, $select_ref, array('a.id'=>$registration_destination_ref_id),array(array('registration r', 'a.registration_id = r.id', 'INNER')))[0];
                if ($dt_reg_ref->health_facility_item_id != 'cca59ec4-d77a-45fd-b51f-aae069da22ea') {
                    $checkin_date = $output[$i]->checkin_date;
                } else {
                    $checkin_date = $output[$i]->register_date;
                }
            } else {
                $checkin_date = $output[$i]->register_date;
            }
            $birth_date = $output[$i]->birth_date;

            $room_name = $output[$i]->room_name;
            $room_code = $output[$i]->room_code;

            $room_explode = explode('-', $room_name);
            $is_number = 0;
            for ($j=0; $j < count($room_explode); $j++) { 
                if (is_numeric($room_explode[$j])) {
                    $is_number = 1;
                    $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
                }
            }
            if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime(date('Y-m-d H:i:s'));
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->m > 0 ? $diff_checkin->m . " Bulan " . $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam" : $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";

            $output[$i]->consultation_type_id = 0;
            if (isset($q_strings['doctor_in_charge_id'])) {
                $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

                $select_con = 'a.consultation_type_id, a.status';
                $where_con = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.consultant_id' => $doctor_in_charge_id,
                    'a.is_cancel' => 0,
                );
                $consultation = $this->Consultation->select(0, $select_con, $where_con);
                if (count($consultation) > 0) {
                    $consultation_type_id = $consultation[0]->consultation_type_id;
                    $status = $consultation[0]->status;

                    $output[$i]->consultation_type_id = $consultation_type_id;
                    
                    if ($consultation_type_id == 2) {
                        if ($status != 0) {
                            $output[$i]->consultation_type_id = 0;
                        }
                    }
                }
            }

            $age = count_age($birth_date);

            if ($age > 18) {
                // EWS SCORE
                $ews = $this->db->query("
                    SELECT ewsi.total
                    FROM early_warning_score ews 
                    INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                    WHERE ews.registration_destination_id ='$registration_destination_id'
                    AND ews.is_delete = 0
                    AND ewsi.is_delete = 0
                    ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                    LIMIT 1
                ")->result();

                if (count($ews) > 0) {
                    $output[$i]->ews_score = $ews[0]->total;
                } else {
                    $output[$i]->ews_score = 99;
                }
            } else {
                // PEWS SCORE
                $pews = $this->db->query("
                    SELECT pi.total 
                    FROM pews p 
                    INNER JOIN pews_item pi ON p.id = pi.pews_id 
                    WHERE p.registration_destination_id = '$registration_destination_id'
                    AND p.is_delete = 0
                    AND pi.is_delete = 0
                    ORDER BY p.observation_date DESC, pi.date_added DESC
                    LIMIT 1
                ")->result();

                if (count($pews) > 0) {
                    $output[$i]->ews_score = $pews[0]->total;
                } else {
                    $output[$i]->ews_score = 99;
                }
            }
        }

        if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') { // ORDER BY EWS
            usort($output, function ($a, $b) {
                return (float)$b->ews_score <=> (float)$a->ews_score;
            });
        }

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

            $where_doctor = "(
                a.doctor_in_charge_id = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_2 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_3 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_4 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_5 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_6 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_7 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_8 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_9 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_10 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_11 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_12 = '$doctor_in_charge_id'
            )";

            $this->db->where($where_doctor);
            unset($where['doctor_in_charge_id']);
        }

        if(isset($q_strings['norm_like'])) {
            $norm_like = $q_strings['norm_like'];
            $this->Registration_Destination->whereLike['p.norm'] = $norm_like;
        }

        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('registration_insurance ri', 'a.id = ri.registration_destination_id AND ri.is_primary = 1', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        
        $this->Registration_Destination->limit = null;
        $this->Registration_Destination->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->Registration_Destination->select(0, $select, $where, $join)[0]->iTotal;

        // var_dump($this->db->last_query());
        // die();

        // $iTotal = $this->db->query("
        // SELECT COUNT(*) AS iTotal
        // FROM registration_destination rd
        // INNER JOIN registration r ON rd.registration_id = r.id
        // INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        // INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        // INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
        // INNER JOIN patient p ON r.norm = p.norm
        // WHERE hfi.health_facility_id = '444b3134-5b75-4b67-af72-a558d46716d7'
        // AND rd.is_checkout = 0
        // AND r.is_cancel = 0
        // ")->result()[0]->iTotal;        

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_fast_register_get($p = 0){
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
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

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

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                    $where['rd.is_checkout'] = 0;
                }
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_item_id'])) {
            $where['hfi.id'] = $q_strings['health_facility_item_id'];
            unset($where['health_facility_item_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
    
            $select = 'r.id';
            $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
            
            if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                unset($where['doctor_in_charge_id']);
                $is_checkout = $q_strings['is_checkout'];
                if ($is_checkout != null) {
                    $where_doc = array('a.is_checkout' => $is_checkout);
                } else {
                    $where_doc = array();
                }
            }

            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);

            $where_doc['a.doctor_in_charge_id_6'] = $doctor_in_charge_id;
            $employee_6 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_6']);

            $where_doc['a.doctor_in_charge_id_7'] = $doctor_in_charge_id;
            $employee_7 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_7']);

            $where_doc['a.doctor_in_charge_id_8'] = $doctor_in_charge_id;
            $employee_8 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_8']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5, $employee_6, $employee_7, $employee_8);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }

        $where['a.is_cancel'] = 0;
    
        $select = 'a.id, a.code_booking, rd.id as registration_destination_id, ri.is_cob, ri.sep, ri.number_insurance, rd.code_queue, rd.checkin_date as register_date, rd.checkin_date, rd.exit_id, ex.name as exit_name, rd.sts, rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7, a.code, a.how_to_enter_id, hte.name as how_to_enter_name, h5.name as type_visit_name , h2.name as how_to_pay, c.name as insurance_name, a.type_register_id, h.name as type_register_name, a.norm, p.name, h4.name as alias, p.is_new, p.birth_date, h3.name as sex_name, hf.id as health_facility_id, hf.name as health_facility_name, rd.health_facility_item_id, hfi.name as health_facility_item_name, rd.doctor_in_charge_id, b.id as bed_id, b.name as bed_name, br.name as room_name, e.fullname as doctor_in_charge_name, e.bride_bpjs, rd.queue';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room br', 'b.room_id = br.id', 'LEFT'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', "a.type_register_id = h.code and h.variable='type_register'", 'INNER'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('helper h4', "p.alias = h4.code and h4.variable='alias'", 'INNER'),
            array('helper h5', "a.type_visit_id = h5.code and h5.variable='type_visit'", 'LEFT'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
        );
        $order = array('rd.checkin_date' => 'desc');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
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

    public function list_fast_jkn_get($p = 0){
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
            
        $limit = $q_strings['limit'] ?? 50;
        $offset = $q_strings['offset'] ?? 0;

        $this->Registration_Destination->limit = $limit;
        $this->Registration_Destination->offset = $offset;

        $where = array();

        $health_facility_id = '';
        if(isset($q_strings['health_facility_id'])) {
            $health_facility_id = $q_strings['health_facility_id'];
            $where['hfi.health_facility_id'] = $health_facility_id;
        }

        if(isset($q_strings['health_facility_item_id'])) {
            $health_facility_item_id = $q_strings['health_facility_item_id'];
            $where['hfi.id'] = $health_facility_item_id;
        }

        // $range_date = '';
        $date_start = '';
        $date_end = '';
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
        } 

        if ($date_start == '' || $date_end == '') {
            $this->REST_Return(400, 'date_start & date_end is required if is_checkout other than 0');
            return;
        }

        // $range_date = "AND a.checkin_date BETWEEN '$date_start' AND '$date_end'";

        $where['a.checkin_date >='] = $date_start;
        $where['a.checkin_date <='] = $date_end;

        if(isset($q_strings['name'])) {
            $name = $q_strings['name'];
            $this->Registration_Destination->whereLike['p.name'] = $name;
        }

        if(isset($q_strings['norm_like'])) {
            $norm_like = $q_strings['norm_like'];
            $this->Registration_Destination->whereLike['p.norm'] = $norm_like;
        }

        if(isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['p.norm'] = $norm;
        }

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
        }

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

            $where_doctor = "(
                a.doctor_in_charge_id = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_2 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_3 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_4 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_5 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_6 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_7 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_8 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_9 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_10 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_11 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_12 = '$doctor_in_charge_id'
            )";

            $this->db->where($where_doctor);
        }

        $select = '
            r.id,
            r.code_booking, 
            a.id AS registration_destination_id, 
            ri.is_cob, 
            ri.sep, 
            ri.number_insurance, 
            ri.insurance_id, 
            a.code_queue, 
            a.register_date, 
            a.checkin_date, 
            a.is_checkout, 
            a.exit_id,
            ex.name as exit_name, 
            a.sts, 
            a.call_nurse_stamp,
            a.call_doctor_stamp,
            a.done_doctor_stamp,
            a.call_pharmacy_stamp,
            a.done_pharmacy_stamp,
            a.date_task_1,
            a.date_task_2,
            a.date_task_3,
            a.date_task_4,
            a.date_task_5,
            a.date_task_6,
            a.date_task_7,
            r.code, 
            r.how_to_enter_id, 
            hte.name as how_to_enter_name, 
            c.name as company_name, 
            r.type_register_id,
            r.type_visit_id, 
            r.norm, 
            hf.id as health_facility_id, 
            hf.name as health_facility_name, 
            a.health_facility_item_id, 
            hfi.name as health_facility_item_name, 
            a.doctor_in_charge_id, 
            a.doctor_in_charge_id_2, 
            a.doctor_in_charge_id_3, 
            a.doctor_in_charge_id_4, 
            a.doctor_in_charge_id_5, 
            a.doctor_in_charge_id_6, 
            a.doctor_in_charge_id_7, 
            a.doctor_in_charge_id_8, 
            a.doctor_in_charge_id_9, 
            a.doctor_in_charge_id_10, 
            a.doctor_in_charge_id_11, 
            a.doctor_in_charge_id_12, 
            b.id as bed_id, 
            b.name as bed_name, 
            br.id as room_id,
            br.name as room_name, 
            br.type_room_id,
            br.code as room_code,
            e.nickname as doctor_nickname, 
            e.fullname as doctor_in_charge_name,
            e2.fullname as doctor_in_charge_name_2,
            e3.fullname as doctor_in_charge_name_3,
            e4.fullname as doctor_in_charge_name_4,
            e5.fullname as doctor_in_charge_name_5,
            e6.fullname as doctor_in_charge_name_6,
            e7.fullname as doctor_in_charge_name_7,
            e8.fullname as doctor_in_charge_name_8,
            e9.fullname as doctor_in_charge_name_9,
            e10.fullname as doctor_in_charge_name_10,
            e11.fullname as doctor_in_charge_name_11,
            e11.fullname as doctor_in_charge_name_12,
            e.bride_bpjs, 
            ms.is_special_cmg, 
            ms.code_inacbg, 
            ms.desc_inacbg, 
            ms.percentage, 
            ms.cost_inacbg, 
            a.queue,
            a.registration_destination_ref_id,
            a.is_transfer,
            a.date_control,
            tn.triage_result,
            p.name,
            p.is_new,
            p.birth_date,
            h.name as type_register_name,
            h2.name as type_visit_name,
            h3.name as type_room_name,
            h4.name as insurance_name,
            h5.name as sex_name,
            h6.name as alias
        ';
        $where['r.is_cancel'] = 0;
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('registration_insurance ri', 'a.id = ri.registration_destination_id AND ri.is_primary = 1', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('how_to_enter hte', 'r.how_to_enter_id = hte.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('helper h', 'r.type_register_id = h.code AND h.variable = "type_register"', 'LEFT'),
            array('helper h2', 'r.type_visit_id = h2.code AND h2.variable = "type_visit"', 'LEFT'),
            array('helper h4', 'ri.insurance_id = h4.code AND h4.variable = "insurance"', 'LEFT'),
            array('helper h5', 'p.sex = h5.code AND h5.variable = "sex"', 'LEFT'),
            array('helper h6', 'p.alias = h6.code AND h6.variable = "alias"', 'LEFT'),
            array('bed b', 'a.bed_id = b.id', 'LEFT'),
            array('room br', 'b.room_id = br.id', 'LEFT'),
            array('helper h3', 'br.type_room_id = h3.code AND h3.variable = "type_room"', 'LEFT'),
            array('exit ex', 'a.exit_id = ex.id', 'LEFT'),
            array('company c', 'ri.company_id = c.id', 'LEFT'),
            array('medical_services ms', 'a.id = ms.registration_destination_id', 'LEFT'),
            array('triage_new tn', 'a.id = tn.registration_destination_id', 'LEFT'),
            array('employee e2', 'a.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            array('employee e3', 'a.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            array('employee e4', 'a.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            array('employee e5', 'a.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('employee e6', 'a.doctor_in_charge_id_6 = e6.id', 'LEFT'),
            array('employee e7', 'a.doctor_in_charge_id_7 = e7.id', 'LEFT'),
            array('employee e8', 'a.doctor_in_charge_id_8 = e8.id', 'LEFT'),
            array('employee e9', 'a.doctor_in_charge_id_9 = e9.id', 'LEFT'),
            array('employee e10', 'a.doctor_in_charge_id_10 = e10.id', 'LEFT'),
            array('employee e11', 'a.doctor_in_charge_id_11 = e11.id', 'LEFT'),
            array('employee e12', 'a.doctor_in_charge_id_12 = e11.id', 'LEFT'),
        );
        $output = $this->Registration_Destination->select(0, $select, $where, $join);

        // var_dump($this->db->last_query());
        // die();

        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_id = $output[$i]->registration_destination_id;
            $checkin_date = $output[$i]->register_date;
            $birth_date = $output[$i]->birth_date;

            $room_name = $output[$i]->room_name;
            $room_code = $output[$i]->room_code;

            $room_explode = explode('-', $room_name);
            $is_number = 0;
            for ($j=0; $j < count($room_explode); $j++) { 
                if (is_numeric($room_explode[$j])) {
                    $is_number = 1;
                    $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
                }
            }
            if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime(date('Y-m-d H:i:s'));
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->m > 0 ? $diff_checkin->m . " Bulan " . $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam" : $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";

            $output[$i]->consultation_type_id = 0;
            if (isset($q_strings['doctor_in_charge_id'])) {
                $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

                $select_con = 'a.consultation_type_id, a.status';
                $where_con = array(
                    'a.registration_destination_id' => $registration_destination_id,
                    'a.consultant_id' => $doctor_in_charge_id,
                    'a.is_cancel' => 0,
                );
                $consultation = $this->Consultation->select(0, $select_con, $where_con);
                if (count($consultation) > 0) {
                    $consultation_type_id = $consultation[0]->consultation_type_id;
                    $status = $consultation[0]->status;

                    $output[$i]->consultation_type_id = $consultation_type_id;
                    
                    if ($consultation_type_id == 2) {
                        if ($status != 0) {
                            $output[$i]->consultation_type_id = 0;
                        }
                    }
                }
            }

            $age = count_age($birth_date);

            if ($age > 18) {
                // EWS SCORE
                $ews = $this->db->query("
                    SELECT ewsi.total
                    FROM early_warning_score ews 
                    INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
                    WHERE ews.registration_destination_id ='$registration_destination_id'
                    AND ews.is_delete = 0
                    AND ewsi.is_delete = 0
                    ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                    LIMIT 1
                ")->result();

                if (count($ews) > 0) {
                    $output[$i]->ews_score = $ews[0]->total;
                } else {
                    $output[$i]->ews_score = 99;
                }
            } else {
                // PEWS SCORE
                $pews = $this->db->query("
                    SELECT pi.total 
                    FROM pews p 
                    INNER JOIN pews_item pi ON p.id = pi.pews_id 
                    WHERE p.registration_destination_id = '$registration_destination_id'
                    AND p.is_delete = 0
                    AND pi.is_delete = 0
                    ORDER BY p.observation_date DESC, pi.date_added DESC
                    LIMIT 1
                ")->result();

                if (count($pews) > 0) {
                    $output[$i]->ews_score = $pews[0]->total;
                } else {
                    $output[$i]->ews_score = 99;
                }
            }
        }

        if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') { // ORDER BY EWS
            usort($output, function ($a, $b) {
                return (float)$b->ews_score <=> (float)$a->ews_score;
            });
        }

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];

            $where_doctor = "(
                a.doctor_in_charge_id = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_2 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_3 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_4 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_5 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_6 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_7 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_8 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_9 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_10 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_11 = '$doctor_in_charge_id' OR 
                a.doctor_in_charge_id_12 = '$doctor_in_charge_id'
            )";

            $this->db->where($where_doctor);
            unset($where['doctor_in_charge_id']);
        }

        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('registration_insurance ri', 'a.id = ri.registration_destination_id AND ri.is_primary = 1', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        
        $this->Registration_Destination->limit = null;
        $this->Registration_Destination->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->Registration_Destination->select(0, $select, $where, $join)[0]->iTotal;

        // var_dump($this->db->last_query());
        // die();

        // $iTotal = $this->db->query("
        // SELECT COUNT(*) AS iTotal
        // FROM registration_destination rd
        // INNER JOIN registration r ON rd.registration_id = r.id
        // INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
        // INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
        // INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
        // INNER JOIN patient p ON r.norm = p.norm
        // WHERE hfi.health_facility_id = '444b3134-5b75-4b67-af72-a558d46716d7'
        // AND rd.is_checkout = 0
        // AND r.is_cancel = 0
        // ")->result()[0]->iTotal;        

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
                
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    // public function list_fast_jkn_get($p = 0){
    //     header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $id     = $this->security->xss_clean($p);
    //     $model  = $this->name;
    
    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] , $response_data);
    //         return;
    //     } 
    
    //     $where = $q_strings;
        
    //     if(isset($q_strings['limit'])) {
    //         $limit = $q_strings['limit'];
    //         $this->$model->limit = $limit; 
    //         unset($where['limit']);
    //     }

    //     if(isset($q_strings['offset'])) {
    //         $offset = $q_strings['offset'];
    //         $this->$model->offset = $offset; 
    //         unset($where['offset']);
    //     }

    //     if(isset($q_strings['name'])) {
    //         $name = $q_strings['name'];
    //         $this->$model->whereLike['p.name'] = $name;
    //         unset($where['name']);
    //     }

    //     if(isset($q_strings['norm_like'])) {
    //         $norm_like = $q_strings['norm_like'];
    //         $this->$model->whereLike['p.norm'] = $norm_like;
    //         unset($where['norm_like']);
    //     }

    //     if(isset($q_strings['norm'])) {
    //         $norm = $q_strings['norm'];
    //         $where['p.norm'] = $norm;
    //         unset($where['norm']);
    //     }

    //     if(isset($q_strings['sep'])) {
    //         $sep = $q_strings['sep'];
    //         $where['ri.sep'] = $sep;
    //         unset($where['sep']);
    //     }

    //     if(isset($q_strings['code'])) {
    //         $code = $q_strings['code'];
    //         $where['a.code'] = $code;
    //         unset($where['code']);
    //     }

    //     if(isset($q_strings['sex'])) {
    //         $sex = $q_strings['sex'];
    //         $where['p.sex'] = $sex;
    //         unset($where['sex']);
    //     }

    //     if(isset($q_strings['birth_date'])) {
    //         $birth_date = $q_strings['birth_date'];
    //         $where['p.birth_date'] = $birth_date;
    //         unset($where['birth_date']);
    //     }

    //     // if(isset($q_strings['is_checkout'])) {
    //     //     $is_checkout = $q_strings['is_checkout'];
    //     //     if ($is_checkout == 'checkout') {
    //     //         if ($q_strings['health_facility_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
    //     //             $where['rd.is_checkout'] = 0;
    //     //         }
    //     //     } else {
    //     //         $where['rd.is_checkout'] = $is_checkout;
    //     //     }
    //         unset($where['is_checkout']);
    //     // }

    //     if(isset($q_strings['is_new'])) {
    //         $is_new = $q_strings['is_new'];
    //         $where['p.is_new'] = $is_new;
    //         unset($where['is_new']);
    //     }
        
    //     $hf_id = $q_strings['health_facility_id'] ?? null;

    //     if(isset($q_strings['health_facility_id'])) {
    //         $where['hf.id'] = $q_strings['health_facility_id'];
    //         unset($where['health_facility_id']);
    //     }
        
    //     if(isset($q_strings['health_facility_item_id'])) {
    //         $where['hfi.id'] = $q_strings['health_facility_item_id'];
    //         unset($where['health_facility_item_id']);
    //     }
        
    //     if(isset($q_strings['insurance_id'])) {
    //         $where['ri.insurance_id'] = $q_strings['insurance_id'];
    //         unset($where['insurance_id']);
    //     }
        
    //     if(isset($q_strings['company_id'])) {
    //         $where['ri.company_id'] = $q_strings['company_id'];
    //         unset($where['company_id']);
    //     }
        
    //     if(isset($q_strings['exit_id'])) {
    //         $where['rd.exit_id'] = $q_strings['exit_id'];
    //         unset($where['exit_id']);
    //     }
    
    //     if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
    //         $date_start = $q_strings['date_start'] . ' 00:00:00';
    //         $date_end = $q_strings['date_end'] . ' 23:59:59';
    //         $where['rd.checkin_date >='] = $date_start;
    //         $where['rd.checkin_date <='] = $date_end;

    //         unset($where['date_start']);
    //         unset($where['date_end']);
    //     } 

    //     if (isset($q_strings['doctor_in_charge_id'])) {
    //         $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
    //         unset($where['doctor_in_charge_id']);
            
    //         $select = 'r.id';
    //         // $is_checkout = $q_strings['is_checkout'];
    //         // if ($is_checkout != null) {
    //         //     $where_doc = array('a.is_checkout' => $is_checkout);
    //         // } else {
    //         //     $where_doc = array();
    //         // }

    //         if (isset($where['hf.id'])) {
    //             $where_doc['hfi.health_facility_id'] = $where['hf.id'];
    //         }
    
    //         $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
    //         $employee = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id']);

    //         $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
    //         $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_2']);
    
    //         $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
    //         $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_3']);
    
    //         $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
    //         $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_4']);
    
    //         $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
    //         $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_5']);

    //         $where_doc['a.doctor_in_charge_id_6'] = $doctor_in_charge_id;
    //         $employee_6 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_6']);

    //         $where_doc['a.doctor_in_charge_id_7'] = $doctor_in_charge_id;
    //         $employee_7 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_7']);

    //         $where_doc['a.doctor_in_charge_id_8'] = $doctor_in_charge_id;
    //         $employee_8 = $this->Registration_Destination->select(0, $select, $where_doc);
    //         unset($where_doc['a.doctor_in_charge_id_8']);
    
    //         $reg_id_arr = [];
    //         $employee_arr = [];
    //         array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5, $employee_6, $employee_7, $employee_8);
    
    //         for ($i=0; $i < count($employee_arr); $i++) { 
    //             for ($j=0; $j < count($employee_arr[$i]); $j++) { 
    //                 array_push($reg_id_arr, $employee_arr[$i][$j]->id);
    //             }
    //         }
    
    //         if (count($reg_id_arr) > 0) {
    //             $this->$model->whereInFields['a.id'] = $reg_id_arr;
    //         } else {
    //             $this->$model->whereInFields['a.id'] = array('999');
    //         }
    //     }

    //     $where['a.is_cancel'] = 0;
    
    //     $select = 'a.id, 
    //     a.code_booking, 
    //     rd.id as registration_destination_id, 
    //     ri.is_cob, 
    //     ri.sep, 
    //     ri.number_insurance, 
    //     ri.insurance_id, 
    //     rd.code_queue, 
    //     rd.checkin_date as register_date, 
    //     rd.checkin_date, 
    //     rd.is_checkout, 
    //     rd.exit_id, 
    //     ex.name as exit_name, 
    //     rd.sts, 
    //     rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7, 
    //     a.code, 
    //     a.how_to_enter_id, 
    //     hte.name as how_to_enter_name, 
    //     c.name as company_name, 
    //     a.type_register_id,
    //     a.type_visit_id, 
    //     a.norm, 
    //     hf.id as health_facility_id, 
    //     hf.name as health_facility_name, 
    //     rd.health_facility_item_id, 
    //     hfi.name as health_facility_item_name, 
    //     rd.doctor_in_charge_id, 
    //     rd.doctor_in_charge_id_2, 
    //     rd.doctor_in_charge_id_3, 
    //     rd.doctor_in_charge_id_4, 
    //     rd.doctor_in_charge_id_5, 
    //     rd.doctor_in_charge_id_6, 
    //     rd.doctor_in_charge_id_7, 
    //     rd.doctor_in_charge_id_8, 
    //     b.id as bed_id, 
    //     b.name as bed_name, 
    //     br.id as room_id,
    //     br.name as room_name, 
    //     br.type_room_id,
    //     br.code as room_code,
    //     e.nickname as doctor_nickname, 
    //     e.fullname as doctor_in_charge_name, 
    //     e2.fullname as doctor_in_charge_name_2, 
    //     e3.fullname as doctor_in_charge_name_3, 
    //     e4.fullname as doctor_in_charge_name_4, 
    //     e5.fullname as doctor_in_charge_name_5, 
    //     e6.fullname as doctor_in_charge_name_6, 
    //     e7.fullname as doctor_in_charge_name_7, 
    //     e8.fullname as doctor_in_charge_name_8, 
    //     e.bride_bpjs, 
    //     ms.is_special_cmg, 
    //     ms.code_inacbg, 
    //     ms.desc_inacbg, 
    //     ms.percentage, 
    //     ms.cost_inacbg, 
    //     rd.queue,
    //     rd.is_transfer';
    //     $join = array(
    //         array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
    //         array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
    //         array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
    //         array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
    //         array('bed b', 'rd.bed_id = b.id', 'LEFT'),
    //         array('room br', 'b.room_id = br.id', 'LEFT'),
    //         array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
    //         array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
    //         array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
    //         array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
    //         array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
    //         array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
    //         array('employee e6', 'rd.doctor_in_charge_id_6 = e6.id', 'LEFT'),
    //         array('employee e7', 'rd.doctor_in_charge_id_7 = e7.id', 'LEFT'),
    //         array('employee e8', 'rd.doctor_in_charge_id_8 = e8.id', 'LEFT'),
    //         array('company c', "ri.company_id = c.id", 'LEFT'),
    //         array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
    //         array('medical_services ms', 'rd.id = ms.registration_destination_id', 'LEFT'),
    //     );

    //     $type_register_arr = get_variable('type_register');
    //     $type_visit_arr = get_variable('type_visit');
    //     $type_room_arr = get_variable('type_room');
    //     $insurance_arr = get_variable('insurance');

    //     if (isset($q_strings['name']) || isset($q_strings['norm']) || isset($q_strings['sex']) || isset($q_strings['is_new']) || isset($q_strings['norm_like']) || isset($q_strings['birth_date'])) {            
    //         $health_facility_id = $where['hf.id'];

    //         $wherein = '';
    //         if ($this->$model->whereInFields != null) {
    //             $wherein = 'AND a.id IN ("'.join('","', $this->$model->whereInFields['a.id']).'")';
    //         }

    //         $name_like = '';
    //         $norm_like_q = '';

    //         if ($name != null) {
    //             $name_like = "AND  `p`.`name` LIKE '%$name%' ESCAPE '!'";
    //         }

    //         if ($norm_like != null) {
    //             $norm_like_q = "AND  `p`.`norm` LIKE '%$norm_like%' ESCAPE '!'";
    //         }

    //         $name_data = $this->db->query("
    //             SELECT `a`.`id`
    //             FROM `registration` as `a`
    //             INNER JOIN `registration_destination` `rd` ON `a`.`id` = `rd`.`registration_id`
    //             INNER JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary`=1
    //             INNER JOIN `health_facility_item` `hfi` ON `rd`.`health_facility_item_id` = `hfi`.`id`
    //             INNER JOIN `health_facility` `hf` ON `hfi`.`health_facility_id` = `hf`.`id`
    //             INNER JOIN `patient` `p` ON `a`.`norm` = `p`.`norm`
    //             AND `hf`.`id` = '$health_facility_id'
    //             AND `a`.`is_cancel` = 0
    //             $name_like
    //             $norm_like_q
    //             $wherein
    //             ")->result();
    //             // WHERE `rd`.`is_checkout` = '$is_checkout'

    //         if (count($name_data) > 0 && $this->$model->whereInFields == null) {
    //             $this->$model->whereInFields['a.id'] = [];
    //         } else {
    //             $this->$model->whereInFields['a.id'] = array('999');
    //         }
            
    //         for ($n=0; $n < count($name_data); $n++) { 
    //             $name_id = $name_data[$n]->id;
    //             array_push($this->$model->whereInFields['a.id'], $name_id);
    //         }

    //         // $join[] = array('patient p', 'a.norm = p.norm', 'INNER');
    //         unset($this->$model->whereLike['p.name']);
    //         unset($this->$model->whereLike['p.norm']);
    //     }
    //     $order = array('rd.checkin_date' => 'desc');
    //     if ($hf_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // ORDER BY TRIASE
    //         $select = $select . ', tn.triage_result';
    //         $join[] = array('triage_new tn', 'rd.id = tn.registration_destination_id', 'LEFT');
    //         unset($order['rd.checkin_date']);
    //         $order = array('tn.triage_result' => 'asc');
    //     }
    //     $output = $this->$model->select(0, $select, $where, $join, $order);

    //     $norms = [];
    //     for ($i=0; $i < count($output); $i++) { 
    //         $norms[] = $output[$i]->norm;
    //         $checkin_date = $output[$i]->checkin_date;

    //         $registration_destination_id = $output[$i]->registration_destination_id;

    //         $type_register_id = $output[$i]->type_register_id;
    //         $type_visit_id = $output[$i]->type_visit_id;
    //         $insurance_id = $output[$i]->insurance_id;
            
    //         if ($hf_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
    //             $type_room_id = $output[$i]->type_room_id;
    //             $output[$i]->type_room_name = $type_room_arr[$type_room_id];
    //         }

    //         if ($type_visit_id != null) {
    //             $output[$i]->type_visit_name = $type_visit_arr[$type_visit_id];
    //         }

    //         $output[$i]->type_register_name = $type_register_arr[$type_register_id];
    //         $output[$i]->insurance_name = $insurance_arr[$insurance_id];

    //         $room_name = $output[$i]->room_name;
    //         $room_code = $output[$i]->room_code;

    //         $room_explode = explode('-', $room_name);
    //         $is_number = 0;
    //         for ($j=0; $j < count($room_explode); $j++) { 
    //             if (is_numeric($room_explode[$j])) {
    //                 $is_number = 1;
    //                 $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
    //             }
    //         }
    //         if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }

    //         // EWS SCORE
    //         $ews = $this->db->query("
    //             SELECT ewsi.total as ews_score, ewsi.date_added as ews_date_added
    //             FROM early_warning_score ews 
    //             INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id 
    //             WHERE ews.registration_destination_id ='$registration_destination_id'
    //             AND ews.is_delete = 0
    //             AND ewsi.is_delete = 0
    //             ORDER BY ews.observation_date DESC, ewsi.date_added DESC
    //             LIMIT 1
    //         ")->result();

    //         if (count($ews) > 0) {
    //             $output[$i]->ews_score = $ews[0]->ews_score;
    //             $output[$i]->ews_date_added = $ews[0]->ews_date_added;
    //         } else {
    //             $output[$i]->ews_score = 99;
    //             $output[$i]->ews_date_added = null;
    //         }

    //         $checkin = new DateTime($checkin_date);
    //         $checkout = new DateTime(date('Y-m-d H:i:s'));
    //         $diff_checkin = $checkout->diff($checkin);

    //         $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";

    //         $status = $this->db->query("
    //             SELECT lr.no_request as lab, 
    //             lr.status as lab_status, 
    //             rr.no_request as rad, 
    //             rr.status as rad_status, 
    //             pr.no_request as phy, pr.status as phy_status, 
    //             pp.no_order as pre,
    //             pp.flag as pre_flag
    //             FROM soap s
    //             LEFT JOIN laboratory_request lr ON s.id = lr.soap_id
    //             LEFT JOIN radiology_request rr ON s.id = rr.soap_id
    //             LEFT JOIN physiotherapy_request pr ON s.id = pr.soap_id
    //             LEFT JOIN pharmacy_prescription pp ON s.registration_destination_id = pp.registration_destination_id 
    //             WHERE s.registration_destination_id = '$registration_destination_id'
    //         ")->result();

    //         $statuses = [
    //             'laboratory' => [],
    //             'radiology' => [],
    //             'physiotherapy' => [],
    //             'prescription' => [],
    //         ];

    //         $tmp_statuses = [
    //             'laboratory' => [],
    //             'radiology' => [],
    //             'physiotherapy' => [],
    //             'prescription' => [],
    //         ];

    //         for ($j=0; $j < count($status); $j++) { 
    //             $lab = $status[$j]->lab;
    //             $lab_status = $status[$j]->lab_status;
    //             $rad = $status[$j]->rad;
    //             $rad_status = $status[$j]->rad_status;
    //             $phy = $status[$j]->phy;
    //             $phy_status = $status[$j]->phy_status;
    //             $pre = $status[$j]->pre;
    //             $pre_flag = $status[$j]->pre_flag;

    //             if (!key_exists($lab, $tmp_statuses['laboratory']) && $lab != null) {
    //                 $tmp_statuses['laboratory'][$lab] = $lab_status;
    //             }

    //             if (!key_exists($rad, $tmp_statuses['radiology']) && $rad != null) {
    //                 $tmp_statuses['radiology'][$rad] = $rad_status;
    //             }

    //             if (!key_exists($phy, $tmp_statuses['physiotherapy']) && $phy != null) {
    //                 $tmp_statuses['physiotherapy'][$phy] = $phy_status;
    //             }

    //             if (!key_exists($pre, $tmp_statuses['prescription']) && $pre != null) {
    //                 $tmp_statuses['prescription'][$pre] = $pre_flag;
    //             }
    //         }

    //         foreach ($tmp_statuses as $key => $val) {
    //             foreach ($val as $key2 => $val2) {
    //                 if ($key == "prescription") {
    //                     array_push($statuses[$key], ["no_order" => $key2, "flag" => $val2]);
    //                 } else {
    //                     array_push($statuses[$key], ["no_request" => $key2, "status" => $val2]);
    //                 }
    //             }
    //         }

    //         $output[$i]->statuses = $statuses;
    //     }

    //     if ($hf_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
    //         usort($output, function ($a, $b) {
    //             return (float)$b->ews_score <=> (float)$a->ews_score;
    //         });
    //     }

    //     if (count($norms) > 0) {
    //         $param = implode(',',$norms);
    
    //         $patient = $this->db->query("
    //             SELECT p.norm, p.name, p.is_new, p.birth_date, h.name as sex_name, h2.name AS alias
    //             FROM patient p
    //             INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
    //             INNER JOIN helper h2 ON p.alias = h2.code AND h2.variable = 'alias'
    //             WHERE p.norm in ($param)
    //         ")->result();
    
    //         $dt_patient = [];
    //         for ($i=0; $i < count($patient); $i++) { 
    //             $dt_patient[$patient[$i]->norm] = array(
    //                 'name' => $patient[$i]->name,
    //                 'is_new' => $patient[$i]->is_new,
    //                 'birth_date' => $patient[$i]->birth_date,
    //                 'sex_name' => $patient[$i]->sex_name,
    //                 'alias' => $patient[$i]->alias,
    //             );
    //         }
    
    //         for ($i=0; $i < count($output); $i++) { 
    //             $norm = $output[$i]->norm;
    //             $output[$i]->name = $dt_patient[$norm]['name'];
    //             $output[$i]->is_new = $dt_patient[$norm]['is_new'];
    //             $output[$i]->birth_date = $dt_patient[$norm]['birth_date'];
    //             $output[$i]->sex_name = $dt_patient[$norm]['sex_name'];
    //             $output[$i]->alias = $dt_patient[$norm]['alias'];
    //         }
    //     }

    //     $join = array(
    //         array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
    //         array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER')
    //     );

    //     if (isset($q_strings['name']) || isset($q_strings['norm']) || isset($q_strings['sex']) || isset($q_strings['is_new']) || isset($q_strings['norm_like']) || isset($q_strings['birth_date'])) {
    //         $join[] = array('patient p', 'a.norm = p.norm', 'INNER');
    //     }
    //     if (isset($q_strings['health_facility_id']) || isset($q_strings['health_facility_item_id'])) {
    //         $join[] = array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER');
    //         $join[] = array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER');
    //     }
        
    //     $this->$model->limit = null;
    //     $this->$model->offset = null;
    //     $select = 'count(*) as iTotal';
    //     $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;

	// 	$response_data = (object) array(
	// 		'rows' => $output,
	// 		'start' => ($offset + $limit),
	// 		'iTotal' => $iTotal
	// 	);
                
    //     $this->REST_Return(200, 'SUCCESS', $response_data);
    //     return;
    // }

    public function list_patient_visit_get($p = 0){
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

        $norm = $q_strings['norm'];
        if ($norm == null) {
            $this->REST_Return(400, 'norm is required');
            return;
        }
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
        }
    
        $select = 'a.id, 
        a.code_booking, 
        rd.id as registration_destination_id, 
        ri.is_cob, 
        ri.sep, 
        ri.number_insurance, 
        ri.insurance_id, 
        rd.code_queue, 
        rd.register_date, 
        rd.checkin_date, 
        rd.checkout_date, 
        rd.is_checkout, 
        rd.exit_id, 
        ex.name as exit_name, 
        rd.sts, 
        rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7, 
        a.code, 
        a.how_to_enter_id, 
        hte.name as how_to_enter_name, 
        h5.name as type_visit_name , 
        h2.name as insurance_name, 
        c.name as company_name, 
        a.type_register_id, 
        h.name as type_register_name, 
        a.norm, 
        hf.id as health_facility_id, 
        hf.name as health_facility_name, 
        rd.health_facility_item_id, 
        hfi.name as health_facility_item_name, 
        rd.doctor_in_charge_id, 
        rd.doctor_in_charge_id_2, 
        rd.doctor_in_charge_id_3, 
        rd.doctor_in_charge_id_4, 
        rd.doctor_in_charge_id_5, 
        rd.doctor_in_charge_id_6, 
        rd.doctor_in_charge_id_7, 
        rd.doctor_in_charge_id_8, 
        rd.doctor_in_charge_id_9, 
        rd.doctor_in_charge_id_10, 
        rd.doctor_in_charge_id_11, 
        rd.doctor_in_charge_id_12, 
        b.id as bed_id, 
        b.name as bed_name, 
        br.id as room_id,
        br.name as room_name, 
        br.type_room_id,
        h6.name as type_room_name,
        e.fullname as doctor_in_charge_name, 
        e2.fullname as doctor_in_charge_name_2, 
        e3.fullname as doctor_in_charge_name_3, 
        e4.fullname as doctor_in_charge_name_4, 
        e5.fullname as doctor_in_charge_name_5, 
        e6.fullname as doctor_in_charge_name_6, 
        e7.fullname as doctor_in_charge_name_7, 
        e8.fullname as doctor_in_charge_name_8, 
        e9.fullname as doctor_in_charge_name_9, 
        e10.fullname as doctor_in_charge_name_10, 
        e11.fullname as doctor_in_charge_name_11, 
        e11.fullname as doctor_in_charge_name_12, 
        e.bride_bpjs, 
        rd.queue,
        rd.user_added';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            // array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room br', 'b.room_id = br.id', 'LEFT'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('employee e6', 'rd.doctor_in_charge_id_6 = e6.id', 'LEFT'),
            array('employee e7', 'rd.doctor_in_charge_id_7 = e7.id', 'LEFT'),
            array('employee e8', 'rd.doctor_in_charge_id_8 = e8.id', 'LEFT'),
            array('employee e9', 'rd.doctor_in_charge_id_9 = e9.id', 'LEFT'),
            array('employee e10', 'rd.doctor_in_charge_id_10 = e10.id', 'LEFT'),
            array('employee e11', 'rd.doctor_in_charge_id_11 = e11.id', 'LEFT'),
            array('employee e12', 'rd.doctor_in_charge_id_12 = e12.id', 'LEFT'),
            array('helper h', "a.type_register_id = h.code and h.variable='type_register'", 'INNER'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('helper h5', "a.type_visit_id = h5.code and h5.variable='type_visit'", 'LEFT'),
            array('helper h6', "br.type_room_id = h6.code and h6.variable='type_room'", 'LEFT'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
        );
        $where = array(
            'a.norm' => $norm
        );
        $order = array('rd.checkin_date' => 'desc');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        $patient = $this->db->query("
            SELECT p.norm, p.name, p.is_new, p.birth_date, h.name as sex_name, h2.name AS alias
            FROM patient p
            INNER JOIN helper h ON p.sex = h.code AND h.variable = 'sex'
            INNER JOIN helper h2 ON p.alias = h2.code AND h2.variable = 'alias'
            WHERE p.norm = $norm
        ")->result();

        $dt_patient = array(
            'name' => $patient[0]->name,
            'is_new' => $patient[0]->is_new,
            'birth_date' => $patient[0]->birth_date,
            'sex_name' => $patient[0]->sex_name,
            'alias' => $patient[0]->alias,
        );
        
        for ($i=0; $i < count($output); $i++) { 
            $norms[] = $output[$i]->norm;
            $checkin_date = $output[$i]->register_date;
            $checkout_date = $output[$i]->checkout_date;
            $user_added = $output[$i]->user_added;

            $select = 'a.id, e.name';
            $join_user = array(
                array('employee e', "a.employee_id = e.id", 'INNER'),
            );
            $user = $this->User->select($user_added, $select, [], $join_user);
            
            $output[$i]->user_added_name = $user[0]->name;

            $registration_destination_id = $output[$i]->registration_destination_id;

            $select = '
            a.is_primary,
            a.insurance_id,
            a.company_id,
            a.number_insurance,
            a.expired,
            a.service_class_id,
            h.name as insurance_name,
            c.name as company_name,
            sc.name as service_class_name';
            $where_insu = array(
                'a.registration_destination_id' => $registration_destination_id
            );
            $join_insu = array(
                array('helper h', "a.insurance_id = h.code and h.variable='insurance'", 'INNER'),
                array('company c', "a.company_id = c.id", 'LEFT'),
                array('service_class sc', "a.service_class_id = sc.id", 'LEFT'),
            );
            $registration_insurances = $this->Registration_Insurance->select(0, $select, $where_insu, $join_insu);

            $output[$i]->registration_insurances = $registration_insurances;

            $output[$i]->name = $dt_patient['name'];
            $output[$i]->is_new = $dt_patient['is_new'];
            $output[$i]->birth_date = $dt_patient['birth_date'];
            $output[$i]->sex_name = $dt_patient['sex_name'];
            $output[$i]->alias_name = $dt_patient['alias'];

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime($checkout_date);
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";
        }        

        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER')
        );
        
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

    public function list_master_data_get($p = 0){
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

        $date_start = $q_strings['date_start'] ?? date('Y-m-d');
        $date_end = $q_strings['date_end'] ?? null;
        if ($date_end == null) {
            $date_end = date('Y-m-d', strtotime('+1 day'));
        } else {
            $date_end = date('Y-m-d', strtotime('+1 day', strtotime($date_end)));
        }

        $limit = $q_strings['limit'] ?? '0'; $limit = $limit == '0' ? '': 'LIMIT ' . $limit;
        $offset = $q_strings['offset'] ?? '0'; $offset = $offset == '0' ? '': 'OFFSET ' . $offset;

        $filter = array();

        $filter_name = $q_strings['name'] ?? null; $filter_name = $filter_name == null ? '': $filter[] = array('p.name' => 'LIKE "%' . $filter_name . '%"');
        $filter_norm_like = $q_strings['norm_like'] ?? null; $filter_norm_like = $filter_norm_like == null ? '': $filter[] = array('p.norm' => 'LIKE "%' . $filter_norm_like . '%"');
        $filter_norm = $q_strings['norm'] ?? null; $filter_norm == null ? '': $filter[] = array('p.norm' => '= "' . $filter_norm . '"');
        // $filter_sep = $q_strings['sep'] ?? null; $filter_sep == null ? '': $filter[] = array('ri.sep' => '= "' . $filter_sep . '"');
        // $filter_code = $q_strings['code'] ?? null; $filter_code == null ? '': $filter[] = array('r.code' => '= "' . $filter_code . '"');
        // $filter_sex = $q_strings['sex'] ?? null; $filter_sex == null ? '': $filter[] = array('p.sex' => '= "' . $filter_sex . '"');
        // $filter_birth_date = $q_strings['birth_date'] ?? null; $filter_birth_date == null ? '': $filter[] = array('p.birth_date' => '= "' . $filter_birth_date . '"');
        // $filter_is_new = $q_strings['is_new'] ?? null; $filter_is_new == null ? '': $filter[] = array('p.is_new' => '= "' . $filter_is_new . '"');
        $filter_health_facility_id = $q_strings['health_facility_id'] ?? null; $filter_health_facility_id == null ? '': $filter[] = array('hf.id' => '= "' . $filter_health_facility_id . '"');
        $filter_health_facility_item_id = $q_strings['health_facility_item_id'] ?? null; $filter_health_facility_item_id == null ? '': $filter[] = array('hfi.id' => '= "' . $filter_health_facility_item_id . '"');
        // $filter_insurance_id = $q_strings['insurance_id'] ?? null; $filter_insurance_id == null ? '': $filter[] = array('ri.insurance_id' => '= "' . $filter_insurance_id . '"');
        // $filter_company_id = $q_strings['company_id'] ?? null; $filter_company_id == null ? '': $filter[] = array('ri.company_id' => '= "' . $filter_company_id . '"');
        // $filter_is_checkout = $q_strings['is_checkout'] ?? null; $filter_is_checkout == null ? '': $filter[] = array('rd.is_checkout' => '= "' . $filter_is_checkout . '"');
        $filter_doctor_in_charge_id = $q_strings['doctor_in_charge_id'] ?? null; $filter_doctor_in_charge_id == null ? '': $filter[] = array('rd.doctor_in_charge_id' => '= "' . $filter_doctor_in_charge_id . '"');

        $filter_text = '';

        for ($i=0; $i < count($filter); $i++) { 
            $key = key($filter[$i]);
            if ($i == 0) {
                $filter_text = 'WHERE '  . $key . ' ' . $filter[$i][$key];
            } else {
                $filter_text = $filter_text . ' AND ' . $key . ' ' . $filter[$i][$key];
            }
        }
        
        $output = $this->db->query("
            SELECT
            full_r.id,
            rd.id AS registration_destination_id,
            rd.checkin_date,
            rd.is_checkout, 
            rd.exit_id,
            ex.name as exit_name,
            ri.is_cob, 
            ri.sep,
            ri.number_insurance, 
            ri.insurance_id,
            (SELECT name FROM helper WHERE ri.insurance_id = code AND variable = 'insurance') as insurance_name,
            hfi.health_facility_id,
            hf.name as health_facility_name,
            rd.health_facility_item_id, 
            hfi.name as health_facility_item_name,
            b.id as bed_id, 
            b.name as bed_name, 
            br.id as room_id,
            br.name as room_name, 
            br.type_room_id,
            br.code as room_code,
            rd.doctor_in_charge_id,
            e.nickname as doctor_nickname, 
            e.fullname as doctor_in_charge_name,
            full_r.norm,
            p.name,
            p.is_new, 
            p.birth_date, 
            (SELECT name FROM helper WHERE p.sex = code AND variable = 'sex') as sex_name,
            (SELECT name FROM helper WHERE p.alias = code AND variable = 'alias') as alias
            FROM (
                SELECT id
                FROM registration
                WHERE register_date >= '$date_start 00:00:00'
                AND register_date < '$date_end 00:00:00'
                AND is_cancel = 0
            ) AS r
            INNER JOIN registration full_r ON r.id = full_r.id
            INNER JOIN registration_destination rd ON full_r.id = rd.registration_id
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            INNER JOIN how_to_enter hte ON full_r.how_to_enter_id = hte.id
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            INNER JOIN patient p ON full_r.norm = p.norm
            LEFT JOIN `exit` ex ON rd.exit_id = ex.id
            LEFT JOIN company c ON ri.company_id = c.id
            LEFT JOIN bed b ON rd.bed_id = b.id
            LEFT JOIN room br ON b.room_id = br.id
            $filter_text
            ORDER BY rd.checkin_date DESC
            $limit
            $offset
        ")->result();

        // var_dump($this->db->last_query());
        // var_dump($output);
        // die();

        for ($i=0; $i < count($output); $i++) { 
            // $checkin_date = $output[$i]->checkin_date;
            // $room_name = $output[$i]->room_name;
            // $room_code = $output[$i]->room_code;

            // $room_explode = explode('-', $room_name);
            // $is_number = 0;
            // for ($j=0; $j < count($room_explode); $j++) { 
            //     if (is_numeric($room_explode[$j])) {
            //         $is_number = 1;
            //         $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
            //     }
            // }
            // if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }
        }

        $join_total = "";
        $done_key = [];

        for ($i=0; $i < count($filter); $i++) { 
            $key = explode('.', key($filter[$i]))[0];
            
            if ($key == 'p') {
                $key_p = $done_key['p'] ?? null;
                if ($key_p == null) {
                    $join_total = $join_total .  ' INNER JOIN patient p ON r.norm = p.norm';
                    $done_key['p'] = 1;
                } 
            } elseif ($key == 'ri') {
                $key_ri = $done_key['ri'] ?? null;
                if ($key_ri == null) {
                    $join_total = $join_total . ' INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1';
                    $done_key['ri'] = 1;
                }
            } elseif ($key == 'hf' || $key == 'hfi') {
                $key_hfhfi = $done_key['hfhfi'] ?? null;
                if ($key_hfhfi == null) {
                    $join_total = $join_total . ' INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id';
                    $done_key['hfhfi'] = 1;
                }
            }
        }

        $iTotal = $this->db->query("
            SELECT COUNT(*) AS iTotal
            FROM (
                SELECT id
                FROM registration
                WHERE register_date >= '$date_start 00:00:00'
                AND register_date < '$date_end 00:00:00'
                AND is_cancel = 0
            ) as r_sub
            INNER JOIN registration r ON r_sub.id = r.id
            INNER JOIN registration_destination rd ON r.id = rd.registration_id
            $join_total
            $filter_text
        ")->result()[0]->iTotal;

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($q_strings['offset'] ?? 0) + ($q_strings['limit'] ?? 0),
			'iTotal' => $iTotal
		);
            
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_admission_get($p = 0){
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

        $date_start = $q_strings['date_start'] ?? date('Y-m-d');
        $date_end = $q_strings['date_end'] ?? null;
        if ($date_end == null) {
            $date_end = date('Y-m-d', strtotime('+1 day'));
        } else {
            $date_end = date('Y-m-d', strtotime('+1 day', strtotime($date_end)));
        }

        $limit = $q_strings['limit'] ?? '0'; $limit = $limit == '0' ? '': 'LIMIT ' . $limit;
        $offset = $q_strings['offset'] ?? '0'; $offset = $offset == '0' ? '': 'OFFSET ' . $offset;

        $filter = array();

        $filter_name = $q_strings['name'] ?? null; $filter_name = $filter_name == null ? '': $filter[] = array('p.name' => 'LIKE "%' . $filter_name . '%"');
        $filter_norm_like = $q_strings['norm_like'] ?? null; $filter_norm_like = $filter_norm_like == null ? '': $filter[] = array('p.norm' => 'LIKE "%' . $filter_norm_like . '%"');
        $filter_norm = $q_strings['norm'] ?? null; $filter_norm == null ? '': $filter[] = array('p.norm' => '= "' . $filter_norm . '"');
        $filter_sep = $q_strings['sep'] ?? null; $filter_sep == null ? '': $filter[] = array('ri.sep' => '= "' . $filter_sep . '"');
        $filter_code = $q_strings['code'] ?? null; $filter_code == null ? '': $filter[] = array('r.code' => '= "' . $filter_code . '"');
        $filter_sex = $q_strings['sex'] ?? null; $filter_sex == null ? '': $filter[] = array('p.sex' => '= "' . $filter_sex . '"');
        $filter_birth_date = $q_strings['birth_date'] ?? null; $filter_birth_date == null ? '': $filter[] = array('p.birth_date' => '= "' . $filter_birth_date . '"');
        $filter_is_new = $q_strings['is_new'] ?? null; $filter_is_new == null ? '': $filter[] = array('p.is_new' => '= "' . $filter_is_new . '"');
        $filter_health_facility_id = $q_strings['health_facility_id'] ?? null; $filter_health_facility_id == null ? '': $filter[] = array('hf.id' => '= "' . $filter_health_facility_id . '"');
        $filter_health_facility_item_id = $q_strings['health_facility_item_id'] ?? null; $filter_health_facility_item_id == null ? '': $filter[] = array('hfi.id' => '= "' . $filter_health_facility_item_id . '"');
        $filter_insurance_id = $q_strings['insurance_id'] ?? null; $filter_insurance_id == null ? '': $filter[] = array('ri.insurance_id' => '= "' . $filter_insurance_id . '"');
        $filter_company_id = $q_strings['company_id'] ?? null; $filter_company_id == null ? '': $filter[] = array('ri.company_id' => '= "' . $filter_company_id . '"');
        $filter_is_checkout = $q_strings['is_checkout'] ?? null; $filter_is_checkout == null ? '': $filter[] = array('rd.is_checkout' => '= "' . $filter_is_checkout . '"');
        $filter_doctor_in_charge_id = $q_strings['doctor_in_charge_id'] ?? null; $filter_doctor_in_charge_id == null ? '': $filter[] = array('rd.doctor_in_charge_id' => '= "' . $filter_doctor_in_charge_id . '"');

        $filter_text = '';

        for ($i=0; $i < count($filter); $i++) { 
            $key = key($filter[$i]);
            if ($i == 0) {
                $filter_text = 'WHERE '  . $key . ' ' . $filter[$i][$key];
            } else {
                $filter_text = $filter_text . ' AND ' . $key . ' ' . $filter[$i][$key];
            }
        }
        
        $output = $this->db->query("
            SELECT
            full_r.id,
            rd.id AS registration_destination_id,
            full_r.code_booking,
            ri.is_cob, 
            ri.sep, 
            ri.number_insurance, 
            ri.insurance_id,
            rd.code_queue, 
            rd.checkin_date as register_date, 
            rd.checkin_date, 
            rd.is_checkout, 
            rd.exit_id,
            ex.name as exit_name, 
            rd.sts,
            rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7,
            full_r.code, 
            full_r.how_to_enter_id, 
            hte.name as how_to_enter_name,
            c.name as company_name,
            full_r.type_register_id,
            full_r.type_visit_id, 
            full_r.norm,
            hf.id as health_facility_id, 
            hf.name as health_facility_name,
            hfi.name as health_facility_item_name, 
            rd.health_facility_item_id, 
            rd.doctor_in_charge_id, 
            rd.doctor_in_charge_id_2, 
            rd.doctor_in_charge_id_3, 
            rd.doctor_in_charge_id_4, 
            rd.doctor_in_charge_id_5,
            rd.doctor_in_charge_id_6,
            rd.doctor_in_charge_id_7,
            rd.doctor_in_charge_id_8,
            rd.doctor_in_charge_id_9,
            rd.doctor_in_charge_id_10,
            rd.doctor_in_charge_id_11,
            rd.doctor_in_charge_id_12,
            b.id as bed_id, 
            b.name as bed_name, 
            br.id as room_id,
            br.name as room_name, 
            br.type_room_id,
            br.code as room_code,
            e.nickname as doctor_nickname, 
            e.fullname as doctor_in_charge_name,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_2) as doctor_in_charge_name_2,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_3) as doctor_in_charge_name_3,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_4) as doctor_in_charge_name_4,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_5) as doctor_in_charge_name_5,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_6) as doctor_in_charge_name_6,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_7) as doctor_in_charge_name_7,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_8) as doctor_in_charge_name_8,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_9) as doctor_in_charge_name_9,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_10) as doctor_in_charge_name_10,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_11) as doctor_in_charge_name_11,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_12) as doctor_in_charge_name_12,
            (SELECT name FROM helper WHERE full_r.type_register_id = code AND variable = 'type_register') as type_register_name,
            (SELECT name FROM helper WHERE full_r.type_visit_id = code AND variable = 'type_visit') as type_vist_name,
            (SELECT name FROM helper WHERE br.type_room_id = code AND variable = 'type_room') as type_room_name,
            (SELECT name FROM helper WHERE ri.insurance_id = code AND variable = 'insurance') as insurance_name,
            e.bride_bpjs, 
            rd.queue,
            p.name,
            p.is_new, 
            p.birth_date, 
            (SELECT name FROM helper WHERE p.sex = code AND variable = 'sex') as sex_name,
            (SELECT name FROM helper WHERE p.alias = code AND variable = 'alias') as alias,
            (
                SELECT ewsi.total as ews_score
                FROM early_warning_score ews
                INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id
                WHERE ews.registration_destination_id = rd.id
                AND ews.is_delete = 0
                AND ewsi.is_delete = 0
                ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                LIMIT 1
            ) as ews_score
            FROM (
                SELECT id
                FROM registration
                WHERE register_date >= '$date_start 00:00:00'
                AND register_date < '$date_end 00:00:00'
                AND is_cancel = 0
            ) AS r
            INNER JOIN registration full_r ON r.id = full_r.id
            INNER JOIN registration_destination rd ON full_r.id = rd.registration_id
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            INNER JOIN how_to_enter hte ON full_r.how_to_enter_id = hte.id
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            INNER JOIN patient p ON full_r.norm = p.norm
            LEFT JOIN `exit` ex ON rd.exit_id = ex.id
            LEFT JOIN company c ON ri.company_id = c.id
            LEFT JOIN bed b ON rd.bed_id = b.id
            LEFT JOIN room br ON b.room_id = br.id
            $filter_text
            ORDER BY full_r.register_date DESC
            $limit
            $offset
        ")->result();

        for ($i=0; $i < count($output); $i++) { 
            $checkin_date = $output[$i]->checkin_date;
            $room_name = $output[$i]->room_name;
            $room_code = $output[$i]->room_code;
            $ews_score = $output[$i]->ews_score;

            $room_explode = explode('-', $room_name);
            $is_number = 0;
            for ($j=0; $j < count($room_explode); $j++) { 
                if (is_numeric($room_explode[$j])) {
                    $is_number = 1;
                    $output[$i]->room_abbreviation = $room_code . ' - ' . trim($room_explode[$j]);
                }
            }
            if ($is_number == 0) { $output[$i]->room_abbreviation = $room_code; }

            if (count($ews_score) == null) {
                $output[$i]->ews_score = 99;
            }

            $checkin = new DateTime($checkin_date);
            $checkout = new DateTime(date('Y-m-d H:i:s'));
            $diff_checkin = $checkout->diff($checkin);

            $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";
        }

        $join_total = "";
        $done_key = [];

        for ($i=0; $i < count($filter); $i++) { 
            $key = explode('.', key($filter[$i]))[0];
            
            if ($key == 'p') {
                $key_p = $done_key['p'] ?? null;
                if ($key_p == null) {
                    $join_total = $join_total .  ' INNER JOIN patient p ON r.norm = p.norm';
                    $done_key['p'] = 1;
                } 
            } elseif ($key == 'ri') {
                $key_ri = $done_key['ri'] ?? null;
                if ($key_ri == null) {
                    $join_total = $join_total . ' INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1';
                    $done_key['ri'] = 1;
                }
            } elseif ($key == 'hf' || $key == 'hfi') {
                $key_hfhfi = $done_key['hfhfi'] ?? null;
                if ($key_hfhfi == null) {
                    $join_total = $join_total . ' INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id';
                    $done_key['hfhfi'] = 1;
                }
            }
        }

        $iTotal = $this->db->query("
            SELECT COUNT(*) AS iTotal
            FROM (
                SELECT id
                FROM registration
                WHERE register_date >= '$date_start 00:00:00'
                AND register_date < '$date_end 00:00:00'
                AND is_cancel = 0
            ) as r_sub
            INNER JOIN registration r ON r_sub.id = r.id
            INNER JOIN registration_destination rd ON r.id = rd.registration_id
            $join_total
            $filter_text
        ")->result()[0]->iTotal;

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($q_strings['offset'] ?? 0) + ($q_strings['limit'] ?? 0),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_antrol_get($p = 0){
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

        $date_start = $q_strings['date_start'] ?? date('Y-m-d');
        $date_end = $q_strings['date_end'] ?? null;
        if ($date_end == null) {
            $date_end = date('Y-m-d', strtotime('+1 day'));
        } else {
            $date_end = date('Y-m-d', strtotime('+1 day', strtotime($date_end)));
        }

        $limit = $q_strings['limit'] ?? '0'; $limit = $limit == '0' ? '': 'LIMIT ' . $limit;
        $offset = $q_strings['offset'] ?? '0'; $offset = $offset == '0' ? '': 'OFFSET ' . $offset;

        $filter = array();

        $filter_name = $q_strings['name'] ?? null; $filter_name = $filter_name == null ? '': $filter[] = array('p.name' => 'LIKE "%' . $filter_name . '%"');
        $filter_norm_like = $q_strings['norm_like'] ?? null; $filter_norm_like = $filter_norm_like == null ? '': $filter[] = array('p.norm' => 'LIKE "%' . $filter_norm_like . '%"');
        $filter_norm = $q_strings['norm'] ?? null; $filter_norm == null ? '': $filter[] = array('p.norm' => '= "' . $filter_norm . '"');
        $filter_sep = $q_strings['sep'] ?? null; $filter_sep == null ? '': $filter[] = array('ri.sep' => '= "' . $filter_sep . '"');
        $filter_code = $q_strings['code'] ?? null; $filter_code == null ? '': $filter[] = array('r.code' => '= "' . $filter_code . '"');
        $filter_sex = $q_strings['sex'] ?? null; $filter_sex == null ? '': $filter[] = array('p.sex' => '= "' . $filter_sex . '"');
        $filter_birth_date = $q_strings['birth_date'] ?? null; $filter_birth_date == null ? '': $filter[] = array('p.birth_date' => '= "' . $filter_birth_date . '"');
        $filter_is_new = $q_strings['is_new'] ?? null; $filter_is_new == null ? '': $filter[] = array('p.is_new' => '= "' . $filter_is_new . '"');
        $filter_health_facility_id = '3cb626f5-9148-4fb1-8663-3d7f028b01a6'; $filter[] = array('hf.id' => '= "' . $filter_health_facility_id . '"');
        $filter_health_facility_item_id = $q_strings['health_facility_item_id'] ?? null; $filter_health_facility_item_id == null ? '': $filter[] = array('hfi.id' => '= "' . $filter_health_facility_item_id . '"');
        $filter_insurance_id = $q_strings['insurance_id'] ?? null; $filter_insurance_id == null ? '': $filter[] = array('ri.insurance_id' => '= "' . $filter_insurance_id . '"');
        $filter_company_id = $q_strings['company_id'] ?? null; $filter_company_id == null ? '': $filter[] = array('ri.company_id' => '= "' . $filter_company_id . '"');
        $filter_is_checkout = $q_strings['is_checkout'] ?? null; $filter_is_checkout == null ? '': $filter[] = array('rd.is_checkout' => '= "' . $filter_is_checkout . '"');
        $filter_doctor_in_charge_id = $q_strings['doctor_in_charge_id'] ?? null; $filter_doctor_in_charge_id == null ? '': $filter[] = array('rd.doctor_in_charge_id' => '= "' . $filter_doctor_in_charge_id . '"');

        $filter_text = '';

        for ($i=0; $i < count($filter); $i++) { 
            $key = key($filter[$i]);
            if ($i == 0) {
                $filter_text = 'WHERE '  . $key . ' ' . $filter[$i][$key];
            } else {
                $filter_text = $filter_text . ' AND ' . $key . ' ' . $filter[$i][$key];
            }
        }
        
        $output = $this->db->query("
            SELECT
            full_r.id,
            rd.id AS registration_destination_id,
            full_r.code_booking,
            ri.is_cob, 
            ri.sep, 
            ri.number_insurance, 
            ri.insurance_id,
            rd.code_queue, 
            rd.checkin_date as register_date, 
            rd.checkin_date, 
            rd.is_checkout, 
            rd.registration_id,
            rd.exit_id,
            ex.name as exit_name, 
            rd.sts,
            rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7,
            full_r.code, 
            full_r.how_to_enter_id, 
            hte.name as how_to_enter_name,
            c.name as company_name,
            full_r.code_booking,
            full_r.type_register_id,
            full_r.type_visit_id, 
            full_r.norm,
            hf.id as health_facility_id, 
            hf.name as health_facility_name,
            hfi.name as health_facility_item_name, 
            rd.health_facility_item_id, 
            rd.doctor_in_charge_id, 
            rd.doctor_in_charge_id_2, 
            rd.doctor_in_charge_id_3, 
            rd.doctor_in_charge_id_4, 
            rd.doctor_in_charge_id_5,
            rd.doctor_in_charge_id_6,
            rd.doctor_in_charge_id_7,
            rd.doctor_in_charge_id_8,
            b.id as bed_id, 
            b.name as bed_name, 
            br.id as room_id,
            br.name as room_name, 
            br.type_room_id,
            br.code as room_code,
            e.nickname as doctor_nickname, 
            e.fullname as doctor_in_charge_name,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_2) as doctor_in_charge_name_2,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_3) as doctor_in_charge_name_3,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_4) as doctor_in_charge_name_4,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_5) as doctor_in_charge_name_5,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_6) as doctor_in_charge_name_6,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_7) as doctor_in_charge_name_7,
            (SELECT fullname FROM employee WHERE id=rd.doctor_in_charge_id_8) as doctor_in_charge_name_8,
            (SELECT name FROM helper WHERE full_r.type_register_id = code AND variable = 'type_register') as type_register_name,
            (SELECT name FROM helper WHERE full_r.type_visit_id = code AND variable = 'type_visit') as type_vist_name,
            (SELECT name FROM helper WHERE br.type_room_id = code AND variable = 'type_room') as type_room_name,
            (SELECT name FROM helper WHERE ri.insurance_id = code AND variable = 'insurance') as insurance_name,
            e.bride_bpjs, 
            rd.queue,
            p.name,
            p.is_new, 
            p.birth_date, 
            (SELECT name FROM helper WHERE p.sex = code AND variable = 'sex') as sex_name,
            (SELECT name FROM helper WHERE p.alias = code AND variable = 'alias') as alias,
            (
                SELECT ewsi.total as ews_score
                FROM early_warning_score ews
                INNER JOIN early_warning_score_item ewsi ON ews.id = ewsi.early_warning_score_id
                WHERE ews.registration_destination_id = rd.id
                AND ews.is_delete = 0
                AND ewsi.is_delete = 0
                ORDER BY ews.observation_date DESC, ewsi.date_added DESC
                LIMIT 1
            ) as ews_score
            FROM (
                SELECT id
                FROM registration
                WHERE register_date >= '$date_start 00:00:00'
                AND register_date < '$date_end 00:00:00'
                AND is_cancel = 0
            ) AS r
            INNER JOIN registration full_r ON r.id = full_r.id
            INNER JOIN registration_destination rd ON full_r.id = rd.registration_id
            INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id
            INNER JOIN how_to_enter hte ON full_r.how_to_enter_id = hte.id
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            INNER JOIN patient p ON full_r.norm = p.norm
            LEFT JOIN `exit` ex ON rd.exit_id = ex.id
            LEFT JOIN company c ON ri.company_id = c.id
            LEFT JOIN bed b ON rd.bed_id = b.id
            LEFT JOIN room br ON b.room_id = br.id
            $filter_text
            ORDER BY full_r.register_date DESC
            $limit
            $offset
        ")->result();
        
        $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
        $output_bridging = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/tanggal/".$date_start, "GET");
        $output_bridging = json_decode($output_bridging);
        $bridging_map = [];
        foreach ($output_bridging->response as $item) {
            if (!empty($item->kodebooking)) {
                $bridging_map[trim($item->kodebooking)] = $item;
            }
        }

        $gabungan = [];
        foreach ($output as $row) {
            $code = trim($row->code_booking);

            // Cocokkan dengan bridging
            if (isset($bridging_map[$code])) {
                $bridge = $bridging_map[$code];

                // Hanya ambil yang status "Selesai dilayani"
                if (strcasecmp(trim($bridge->status), 'Selesai dilayani') === 0) {
                    // Gabungkan data
                    $data = (object) array_merge((array) $row, (array) [
                        'status_antrol' => $bridge->status,
                        'taskid'        => $bridge->taskid ?? null,
                        'waktu'         => $bridge->waktu ?? null
                    ]);

                    $gabungan[] = $data;
                }
            }
        }

        $output = $gabungan;

        for ($i=0; $i < count($output); $i++) { 
            $output_cek = $this->vclaim_antrol_compress->request("/antrean/getlisttask", "POST", json_encode(array('kodebooking'=>$output[$i]->code_booking)));
            $output_cek = json_decode($output_cek);
            $output[$i]->totaltask = $output_cek->response;
        }

        $join_total = "";
        $done_key = [];

        for ($i=0; $i < count($filter); $i++) { 
            $key = explode('.', key($filter[$i]))[0];
            
            if ($key == 'p') {
                $key_p = $done_key['p'] ?? null;
                if ($key_p == null) {
                    $join_total = $join_total .  ' INNER JOIN patient p ON r.norm = p.norm';
                    $done_key['p'] = 1;
                } 
            } elseif ($key == 'ri') {
                $key_ri = $done_key['ri'] ?? null;
                if ($key_ri == null) {
                    $join_total = $join_total . ' INNER JOIN registration_insurance ri ON rd.id = ri.registration_destination_id AND ri.is_primary = 1';
                    $done_key['ri'] = 1;
                }
            } elseif ($key == 'hf' || $key == 'hfi') {
                $key_hfhfi = $done_key['hfhfi'] ?? null;
                if ($key_hfhfi == null) {
                    $join_total = $join_total . ' INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id INNER JOIN health_facility hf ON hfi.health_facility_id = hf.id';
                    $done_key['hfhfi'] = 1;
                }
            }
        }

        $iTotal = $this->db->query("
            SELECT COUNT(*) AS iTotal
            FROM (
                SELECT id
                FROM registration
                WHERE register_date >= '$date_start 00:00:00'
                AND register_date < '$date_end 00:00:00'
                AND is_cancel = 0
            ) as r_sub
            INNER JOIN registration r ON r_sub.id = r.id
            INNER JOIN registration_destination rd ON r.id = rd.registration_id
            $join_total
            $filter_text
        ")->result()[0]->iTotal;

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($q_strings['offset'] ?? 0) + ($q_strings['limit'] ?? 0),
			'iTotal' => $iTotal
		);
            
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function cancel_antrol_delete($p = 0) {
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
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        /* Start Transaction */
        $this->db->trans_start();
        if($this->$model->read($id,false) != []) {
            $dt_registration = array(
                'id'                   => $id,
                'is_cancel'            => 1
            );
            $this->$model->replace($dt_registration);

            $this->Registration_Destination->whereFields['registration_id'] = $id;
            $reg_destination = $this->Registration_Destination->read(0,false);
            // Cancel registration_destination
            for ($i=0; $i < count($reg_destination); $i++) { 

                $dt_registration_destination = array(
                    'id'            => $reg_destination[$i]->id,
                    'is_cancel'     => 1,
                    'is_checkout'   => 1,
                    'user_cancel'   => $user_id,
                    'date_cancel'   => date("Y-m-d H:i:s",time()),
                    'cancel_reason' => 'cancel antrol'
                );
                $this->Registration_Destination->update($reg_destination[$i]->id,$dt_registration_destination);
            }
        } else {
            $this->REST_Return(404, 'Registration with id ' . $id . ' does not exist!');
            return;
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

        $this->load->library('Vclaim_antrol','vclaim_antrol');

        $DataPost = '{
        "kodebooking":"'.$this->$model->read($id,false)[0]->code_booking.'",
        "keterangan":"batal '.$this->$model->read($id,false)[0]->code_booking.'"}';
        $hasil = $this->vclaim_antrol->request("/antrean/batal", "POST", $DataPost);
        $d = json_decode($hasil, true);
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_inpatient_get($p = 0){
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
    
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

        // Tanggal ✔
        // Jam	✔
        // Nama	✔
        // No.RM ✔	
        // JK (L/P) ✔
        // Pembayaran ✔	
        // No. SEP ✔
        // Unit Pengirim ✔
        // Unit Tujuan ✔
        // DPJP Utama ✔
        // Keterangan	
        // Tanggal Pulang ✔	
        // Jam Pulang ✔	
        // Unit yang Memulangkan ✔
        // Tanggal Kontrol 
        // DPJP Tujuan ✔
        // DPJP Tujuan2	
        // Reminder H-3	
        // Reminder H-1

        $where['a.is_cancel'] = 0;
        $where['hf.id'] = '40f06534-07ec-4221-ad9d-dea08196079f';
    
        $select = '
        rd.checkin_date, 
        ptn.fullname as patient_name, 
        a.norm, 
        h2.name as sex_name,
        h.name as insurance_name, 
        ri.insurance_id, 
        ri.company_id, 
        ri.number_insurance, 
        rd.registration_destination_ref_id,
        hfi.name as health_facility_item_name,
        e.fullname as doctor_in_charge_name,
        rd.checkout_date
        ';
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'INNER'),
            array('helper h', 'ri.insurance_id = h.code AND h.variable = "insurance"', 'INNER'),
            array('patient ptn', 'a.norm = ptn.norm', 'INNER'),
            array('helper h2', 'ptn.sex = h2.code AND h2.variable = "sex"', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        $order = array('rd.checkin_date' => 'desc');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_ref_id = $output[$i]->registration_destination_ref_id;
            $select_ref = 'a.id, hfi.name as health_facility_item_name, e.fullname as doctor_in_charge_name';
            $where_ref = array();
            $join_ref = array(
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            );
            $registration_destination_ref = $this->Registration_Destination->select($registration_destination_ref_id, $select_ref, $where_ref, $join_ref);
            
            $health_facility_item_ref_name = $registration_destination_ref[0]->health_facility_item_name;
            $doctor_in_charge_ref_name = $registration_destination_ref[0]->doctor_in_charge_name;

            $output[$i]->health_facility_item_ref_name = $health_facility_item_ref_name;
            $output[$i]->doctor_in_charge_ref_name = $doctor_in_charge_ref_name;
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

    public function pharmacy_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Registration_search";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if(isset($q_strings['name'])) $this->$model->whereLike['c.name'] = $q_strings['name']; unset($q_strings['name']);
            if(isset($q_strings['health_facility_id'])) $this->$model->whereFields['d.health_facility_id'] = $q_strings['health_facility_id'];
            // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
            if ($q_strings['health_facility_id'] !== '40f06534-07ec-4221-ad9d-dea08196079f') {
                if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                    $this->$model->whereFields['a.register_date >='] = $q_strings['date_start'].' 00:00:00';
                    $this->$model->whereFields['a.register_date <='] = $q_strings['date_end'].' 23:59:59';
                } else {
                    $q_strings['date_start'] = date('Y-m-d');
                    $q_strings['date_end'] = date('Y-m-d');
                    $this->$model->whereFields['a.register_date >='] = $q_strings['date_start'].' 00:00:00';
                    $this->$model->whereFields['a.register_date <='] = $q_strings['date_end'].' 23:59:59';
                }
                // $this->$model->whereFields['b.is_checkout'] = '0';
            } else {
                $this->$model->whereFields['b.is_checkout'] = '0';
            }
            $this->$model->whereFields['a.is_booking'] = '1';
            $this->$model->whereFields['a.is_cancel'] = '0';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
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

    public function export_kunjungan_get($p = 0){
		header("Access-Control-Allow-Origin: *");
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
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        $sheet->setCellValue('B3', "NORM"); 
        $sheet->setCellValue('C3', "NIK"); 
        $sheet->setCellValue('D3', "NAMA"); 
        $sheet->setCellValue('E3', "ALAMAT");
        $sheet->setCellValue('F3', "NO HP"); 
        $sheet->setCellValue('G3', "POLI"); 
        $sheet->setCellValue('H3', "DPJP"); 
        $sheet->setCellValue('I3', "DPJP2"); 
        $sheet->setCellValue('J3', "DPJP3"); 
        $sheet->setCellValue('K3', "DPJP4"); 
        $sheet->setCellValue('L3', "DPJP5"); 
        $sheet->setCellValue('M3', "DIAGNOSA"); 
        $sheet->setCellValue('N3', "PENJAMIN"); 
        $sheet->setCellValue('O3', "JENIS KELAMIN"); 
        $sheet->setCellValue('P3', "TANGGAL LAHIR"); 
        // $sheet->setCellValue('K3', "STOK AWAL"); 
        // $sheet->setCellValue('L3', "JUMLAH FISIK"); 
        // $sheet->setCellValue('M3', "SELISIH"); 
        // $sheet->setCellValue('N3', "NILAI"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        $no=1;
        
        
        $where = $q_strings;
        
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($where['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($where['offset']);
        }

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

        if(isset($q_strings['sep'])) {
            $sep = $q_strings['sep'];
            $where['ri.sep'] = $sep;
            unset($where['sep']);
        }

        if(isset($q_strings['code'])) {
            $code = $q_strings['code'];
            $where['a.code'] = $code;
            unset($where['code']);
        }

        if(isset($q_strings['sex'])) {
            $sex = $q_strings['sex'];
            $where['p.sex'] = $sex;
            unset($where['sex']);
        }

        if(isset($q_strings['is_checkout'])) {
            $is_checkout = $q_strings['is_checkout'];
            if ($is_checkout == 'checkout') {
                $where['rd.is_checkout'] = 0;
            } else {
                $where['rd.is_checkout'] = $is_checkout;
            }
            unset($where['is_checkout']);
        }

        if(isset($q_strings['is_new'])) {
            $is_new = $q_strings['is_new'];
            $where['p.is_new'] = $is_new;
            unset($where['is_new']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['health_facility_id'])) {
            $where['hf.id'] = $q_strings['health_facility_id'];
            unset($where['health_facility_id']);
        }
        
        if(isset($q_strings['insurance_id'])) {
            $where['ri.insurance_id'] = $q_strings['insurance_id'];
            unset($where['insurance_id']);
        }
        
        if(isset($q_strings['company_id'])) {
            $where['ri.company_id'] = $q_strings['company_id'];
            unset($where['company_id']);
        }
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 

        $where['a.register_date >='] = $date_start . ' 00:00:00';
        $where['a.register_date <='] = $date_end . ' 23:59:59';

        if (isset($q_strings['doctor_in_charge_id'])) {
            $doctor_in_charge_id = $q_strings['doctor_in_charge_id'];
            unset($where['doctor_in_charge_id']);
    
            if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                $where_doc = array('a.is_checkout' => 0);
            } else {
                $where_doc = array('a.register_date >=' => $date_start, 'a.register_date <=' => $date_end);
            }
            $select = 'r.id';
    
            $where_doc['a.doctor_in_charge_id'] = $doctor_in_charge_id;
            $employee = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id']);
    
            $where_doc['a.doctor_in_charge_id_2'] = $doctor_in_charge_id;
            $employee_2 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_2']);
    
            $where_doc['a.doctor_in_charge_id_3'] = $doctor_in_charge_id;
            $employee_3 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_3']);
    
            $where_doc['a.doctor_in_charge_id_4'] = $doctor_in_charge_id;
            $employee_4 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_4']);
    
            $where_doc['a.doctor_in_charge_id_5'] = $doctor_in_charge_id;
            $employee_5 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_5']);

            $where_doc['a.doctor_in_charge_id_6'] = $doctor_in_charge_id;
            $employee_6 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_6']);

            $where_doc['a.doctor_in_charge_id_7'] = $doctor_in_charge_id;
            $employee_7 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_7']);

            $where_doc['a.doctor_in_charge_id_8'] = $doctor_in_charge_id;
            $employee_8 = $this->Registration_Destination->select(0, $select, $where_doc);
            unset($where_doc['a.doctor_in_charge_id_8']);
    
            $reg_id_arr = [];
            $employee_arr = [];
            array_push($employee_arr, $employee, $employee_2, $employee_3, $employee_4, $employee_5, $employee_6, $employee_7, $employee_8);
    
            for ($i=0; $i < count($employee_arr); $i++) { 
                for ($j=0; $j < count($employee_arr[$i]); $j++) { 
                    array_push($reg_id_arr, $employee_arr[$i][$j]->id);
                }
            }
    
            if (count($reg_id_arr) > 0) {
                $this->$model->whereInFields['a.id'] = $reg_id_arr;
            }
        }

        // not setting date_start & date_end if inpatient
        if ($where['hf.id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
            unset($where['a.register_date >=']);
            unset($where['a.register_date <=']);
        }

        $where['a.is_cancel'] = 0;
    
        // e2.fullname as doctor_in_charge_name_2, 
        // e3.fullname as doctor_in_charge_name_3, 
        // e4.fullname as doctor_in_charge_name_4, 
        // e5.fullname as doctor_in_charge_name_5, 
        $select = '
        a.id, a.code_booking, rd.id as registration_destination_id,  ri.is_cob, ri.sep, ri.number_insurance, e_internal.fullname as doctor_in_charge_twoo, rd.code_queue, rd.checkin_date as register_date, rd.checkin_date, rd.exit_id, ex.name as exit_name, rd.sts, rd.call_nurse_stamp,rd.call_doctor_stamp,rd.done_doctor_stamp,rd.call_pharmacy_stamp,rd.done_pharmacy_stamp,rd.date_task_1,rd.date_task_2,rd.date_task_3,rd.date_task_4,rd.date_task_5,rd.date_task_6,rd.date_task_7, a.code, a.how_to_enter_id, hte.name as how_to_enter_name, h5.name as type_visit_name , h2.name as how_to_pay, c.name as insurance_name, a.type_register_id, h.name as type_register_name, a.norm, p.idcard, p.name, h4.name as alias, p.is_new, p.birth_date, p.idcard, p.address, p.phone, h3.name as sex_name, hf.id as health_facility_id, hf.name as health_facility_name, rd.health_facility_item_id, hfi.name as health_facility_item_name, rd.doctor_in_charge_id, b.id as bed_id, b.name as bed_name, e.fullname as doctor_in_charge_name, e.bride_bpjs, rd.queue';
        // i.code as icd_code, s.id as soap_id, i.name as icd_name,
        $join = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
            array('refer_internal internal', 'rd.id = internal.registration_destination_id', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('employee e_internal', 'internal.doctor_in_charge_id = e.id', 'INNER'),
            array('helper h', "a.type_register_id = h.code and h.variable='type_register'", 'INNER'),
            array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
            array('helper h3', "p.sex = h3.code and h3.variable='sex'", 'INNER'),
            array('helper h4', "p.alias = h4.code and h4.variable='alias'", 'INNER'),
            array('helper h5', "a.type_visit_id = h5.code and h5.variable='type_visit'", 'LEFT'),
            array('company c', "ri.company_id = c.id", 'LEFT'),
            array('how_to_enter hte', "a.how_to_enter_id = hte.id", 'INNER'),
            // array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            // array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            // array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            // array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            // array('soap s', "rd.id = s.registration_destination_id", 'INNER'),
            // array('soap_a sa', "s.id = sa.soap_id", 'INNER'),
            // array('soap_a_action saa', "sa.id = saa.soap_a_id", 'INNER'),
            // array('icd10 i', "saa.icd10_id = i.id", 'INNER'),
        );
        $order = array('rd.register_date' => 'desc');
        // $this->db->group_by(array("s.id"));
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        // $sheet->setCellValue('A3', "TANGGAL KUNJUNGAN"); 
        // $sheet->setCellValue('B3', "NIK"); 
        // $sheet->setCellValue('C3', "NAMA"); 
        // $sheet->setCellValue('D3', "ALAMAT");
        // $sheet->setCellValue('E3', "NO HP"); 
        // $sheet->setCellValue('F3', "POLI"); 
        // $sheet->setCellValue('G3', "DPJP"); 
        // $sheet->setCellValue('H3', "DIAGNOSA"); 
        for ($i=0; $i < count($output); $i++) {
            // if ($output[$i]->soap_id) {
            //     $response_data_lab = $this->db->query("
            //         SELECT `b`.`costing_name`
            //         FROM `laboratory_request` as `a` 
            //         LEFT JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
            //         WHERE `a`.`soap_id` = '{$output[$i]->soap_id}'
            //         AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
            //     $response_data_rad = $this->db->query("
            //         SELECT `b`.`costing_name`
            //         FROM `radiology_request` as `a` 
            //         LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
            //         INNER JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
            //         WHERE `a`.`soap_id` = '{$output[$i]->soap_id}'
            //         AND `a`.`is_delete` = 0 AND `a`.`status` = 2 AND `b`.`status` = 2 AND `bb`.`status` = 2 AND `b`.`is_delete` = 0 AND `bb`.`is_delete` = 0 GROUP BY `bb`.`id`")->result();
            // }

            $sheet->setCellValue('A'.$numrow, date('d/m/Y H:i:s', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('B'.$numrow, "'".$output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, "'".$output[$i]->idcard);
            $sheet->setCellValue('D'.$numrow, $output[$i]->name);
            $sheet->setCellValue('E'.$numrow, $output[$i]->address);
            $sheet->setCellValue('F'.$numrow, $output[$i]->phone);
            $sheet->setCellValue('G'.$numrow, $output[$i]->health_facility_item_name);
            $sheet->setCellValue('H'.$numrow, $output[$i]->doctor_in_charge_name);
            $sheet->setCellValue('I'.$numrow, $output[$i]->doctor_in_charge_twoo);
            $sheet->setCellValue('J'.$numrow, $output[$i]->sep);
            $sheet->setCellValue('K'.$numrow, '');
            $sheet->setCellValue('L'.$numrow, '');
            $sheet->setCellValue('M'.$numrow, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
            $sheet->setCellValue('N'.$numrow, $output[$i]->how_to_pay.'-'.$output[$i]->insurance_name);
            $sheet->setCellValue('O'.$numrow, $output[$i]->birth_date);
            $sheet->setCellValue('P'.$numrow, $output[$i]->sex_name);
            $numrow_detail = $numrow+1;
            for ($lab=0; $lab < count($response_data_lab); $lab++) {
                $sheet->setCellValue('A'.$numrow_detail, date('d/m/Y H:i:s', strtotime($output[$i]->checkin_date)));
                $sheet->setCellValue('B'.$numrow_detail, "'".$output[$i]->norm);
                $sheet->setCellValue('C'.$numrow_detail, "'".$output[$i]->idcard);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->name);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->address);
                $sheet->setCellValue('F'.$numrow_detail, $output[$i]->phone);
                $sheet->setCellValue('G'.$numrow_detail, $output[$i]->health_facility_item_name);
                $sheet->setCellValue('H'.$numrow_detail, $output[$i]->doctor_in_charge_name);
                $sheet->setCellValue('I'.$numrow_detail, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_lab[$lab]->costing_name);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            for ($rad=0; $rad < count($response_data_rad); $rad++) {
                $sheet->setCellValue('A'.$numrow_detail, date('d/m/Y H:i:s', strtotime($output[$i]->checkin_date)));
                $sheet->setCellValue('B'.$numrow_detail, "'".$output[$i]->norm);
                $sheet->setCellValue('C'.$numrow_detail, "'".$output[$i]->idcard);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->name);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->address);
                $sheet->setCellValue('F'.$numrow_detail, $output[$i]->phone);
                $sheet->setCellValue('G'.$numrow_detail, $output[$i]->health_facility_item_name);
                $sheet->setCellValue('H'.$numrow_detail, $output[$i]->doctor_in_charge_name);
                $sheet->setCellValue('I'.$numrow_detail, $output[$i]->icd_code.'-'.$output[$i]->icd_name);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_rad[$rad]->costing_name);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            // $sheet->setCellValue('I'.$numrow, $data->hp);
            // $sheet->setCellValue('J'.$numrow, $data->hna);
            // $sheet->setCellValue('K'.$numrow, $data->sum_adm);
            // $sheet->setCellValue('L'.$numrow, $data->accumulation);
            // $sheet->setCellValue('M'.$numrow, $data->accumulation-$data->sum_adm);
            // $sheet->setCellValue('N'.$numrow, $data->accumulation*$data->hp);
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();

        // for ($i=0; $i < count($doctor); $i++) {
        //     var_dump($doctor[$i]->fullname);
        // }
	}

    public function export_penjamin_get($p = 0){
		header("Access-Control-Allow-Origin: *");
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
        $sheet->setCellValue('A1', 'LAPORAN KUNJUNGAN PASIEN'); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A2', "PENJAMIN"); 
        $sheet->setCellValue('B2', "KUNJUNGAN"); 
        // $sheet->setCellValue('D3', "TIMESTAMP");
        // $sheet->setCellValue('E3', "USER"); 
        // $sheet->setCellValue('F3', "NAMA"); 
        // $sheet->setCellValue('G3', "JENIS BARANG"); 
        // $sheet->setCellValue('H3', "EXD"); 
        // $sheet->setCellValue('I3', "HARGA BELI"); 
        // $sheet->setCellValue('J3', "HARGA JUAL"); 
        // $sheet->setCellValue('K3', "STOK AWAL"); 
        // $sheet->setCellValue('L3', "JUMLAH FISIK"); 
        // $sheet->setCellValue('M3', "SELISIH"); 
        // $sheet->setCellValue('N3', "NILAI"); 
        $numrow = 5; // Set baris pertama untuk isi tabel adalah baris ke 4
        // $this->Opname->whereFields['a.pharmacy_activation_id'] = $response_data[0]->id; 
        // $output_opname = $this->Opname->read(0,false)[0];
        // $this->Opname_item->whereFields['poa.id'] = $response_data[0]->id; 
        // $output_opname_item = $this->Opname_item->read(0,false);
        // var_dump($output_opname_item);die();
        $no=1;
        
        
        
        $this->Registration_Insurance->whereFields['r.register_date >='] = date('Y-m-d').' 00:00:00';
        $this->Registration_Insurance->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
        $this->Registration_Insurance->whereFields['r.is_cancel'] = '0';
        $this->Registration_Insurance->whereFields['a.insurance_id'] = '1';
        $this->Registration_Insurance->whereFields['r.type_register_id'] = '1';
        $this->Registration_Insurance->whereFields['a.is_primary'] = '1';
        $visit_tunai = $this->Registration_Insurance->read(0,false);

        $this->Registration_Insurance->whereFields = array();
        
        $sheet->setCellValue('A4', "TUNAI");
        $sheet->setCellValue('B4', count($visit_tunai));

        $this->Company->whereFields['a.flag'] = '1';
        $penjamin = $this->Company->read(0,false);
        for ($i=0; $i < count($penjamin); $i++) {
            // var_dump($doctor[$i]->health_facility_item_name);
            // die();
            $this->Registration_Insurance->whereFields['a.company_id'] = $penjamin[$i]->id;
            $this->Registration_Insurance->whereFields['r.register_date >='] = date('Y-m-d').' 00:00:00';
            $this->Registration_Insurance->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
            $this->Registration_Insurance->whereFields['r.is_cancel'] = '0';
            $this->Registration_Insurance->whereFields['a.is_primary'] = '1';
            $this->Registration_Insurance->whereFields['r.type_register_id'] = '1';
            $visit = $this->Registration_Insurance->read(0,false);
            $sheet->setCellValue('A'.$numrow, $penjamin[$i]->name);
            $sheet->setCellValue('B'.$numrow, count($visit));
            // $sheet->setCellValue('D'.$numrow, date('d/m/Y H:i:s', strtotime($data->timestampt)));
            // $sheet->setCellValue('E'.$numrow, $data->user_bak);
            // $sheet->setCellValue('F'.$numrow, $data->catalog_name);
            // $sheet->setCellValue('G'.$numrow, $data->category_name);
            // $sheet->setCellValue('H'.$numrow, $data->expired_date);
            // $sheet->setCellValue('I'.$numrow, $data->hp);
            // $sheet->setCellValue('J'.$numrow, $data->hna);
            // $sheet->setCellValue('K'.$numrow, $data->sum_adm);
            // $sheet->setCellValue('L'.$numrow, $data->accumulation);
            // $sheet->setCellValue('M'.$numrow, $data->accumulation-$data->sum_adm);
            // $sheet->setCellValue('N'.$numrow, $data->accumulation*$data->hp);
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Kunjungan Pasien');
        $writer = new Xlsx($spreadsheet);
        ob_end_clean();

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.date('Y-m-d H:i:s').'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();

        // for ($i=0; $i < count($doctor); $i++) {
        //     var_dump($doctor[$i]->fullname);
        // }
	}

    public function list_cancelled_get($p = 0){
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
            // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_cancel'] = '1';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Registration_Insurance->whereFields['b.institute_branch_id'] = $institute_branch_id; 
                $this->Registration_Insurance->whereFields['a.is_primary'] = 1; 
                $this->Registration_Insurance->whereFields['a.registration_destination_id'] = $output->rows[$i]->registration_destination_id;
                $reg_insurance = $this->Registration_Insurance->read();
                for ($j=0; $j < count($reg_insurance); $j++) { 
                    $output->rows[$i]->registration_insurance[$j] = $reg_insurance[$j];
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
     * Registration API
     * --------------------
     * @param: institute_branch_id
     * @param: is_cancel
     * @param: cancel_reason
     * --------------------
     * @method : POST
     * @link: version/mr/registration
     **/

    public function change_put($p = 0) {
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
            // $this->form_validation->set_data($dt_put);
            // // $this->form_validation->set_rules('is_cancel', 'is_cancel', 'trim|required|max_length[1]');
            // // $this->form_validation->set_rules('cancel_reason', 'cancel_reason', 'trim|required');
            // if ($this->form_validation->run() == FALSE){
            //     // Form Validation Errors
            //     $response_data = $this->form_validation->error_array();
            //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            // } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                /* Start Transaction */
                $this->db->trans_start();
                // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
                if($this->Registration_Destination->read($id,false) != []) {
                    // $dt_registration = array(
                    //     'id'                   => $id,
                    //     'is_cancel'            => 1
                    // );
                    // $this->$model->replace($dt_registration);
                    $reg_destination = $this->Registration_Destination->read($id,false)[0];
                    $reg_data = $this->Registration->read($reg_destination->registration_id,false)[0];
                    if($reg_destination){
                        $dpjp_change = array();
                        $insurance_change = array();
                        // CHECK CHANGE DPJP
                        if ($reg_destination->doctor_in_charge_id != $dt_put['doctor_in_charge_id']) {
                            // CHECK CHANGE SCHEDULE ANTRIAN RESET
                            // RESET QUEUE NUMBER
                            // $this->Queue->reset_queue_number($reg_destination->health_facility_item_id,$reg_destination->doctor_in_charge_id);
                            // GET QUEUE NUMBER
                            if ($dt_put['health_facility_id']=='3cb626f5-9148-4fb1-8663-3d7f028b01a6') {
                                $queue = $this->Queue->get_queue_number($dt_put['health_facility_item_id'],$dt_put['doctor_in_charge_id']);
    
                                // Checking if error happen
                                if(isset($queue['error0'])) { // 'Doctor schedule not found!'
                                    $this->REST_Return(404, $queue['error0']);
                                    return;
                                } else if(isset($queue['error1'])) { // 'No schedule today!'
                                    $this->REST_Return(404, $queue['error1']);
                                    return;
                                } else if(isset($queue['error2'])) { // 'You missed the schedule!'
                                    $this->REST_Return(422, $queue['error2']);
                                    return;
                                } else if(isset($queue['error3'])) { // 'The quota is full for today!'
                                    $this->REST_Return(422, $queue['error3']);
                                    return;
                                }
                                $dpjp_change = array(
                                    'health_facility_item_id' => $dt_put['health_facility_item_id'],
                                    'doctor_in_charge_id'     => $dt_put['doctor_in_charge_id'],
                                    'doctor_schedule_item_id' => $queue['doctor_schedule_item_id'],
                                    'code_queue'              => $queue['queue_code'], // if emergency or inpatient queue 0
                                    'queue'                   => $queue['queue'], // if emergency or inpatient queue 0
                                );
                            } else {
                                $dpjp_change = array(
                                    'health_facility_item_id' => $dt_put['health_facility_item_id'],
                                    'doctor_in_charge_id'     => $dt_put['doctor_in_charge_id'],
                                );
                            }
                        }
                        $reg_destination_ins = $this->Registration_Insurance->read($id,false);
                        for ($a=0; $a < count($reg_destination_ins) ; $a++) { 
                            for ($b=0; $b < count($dt_put['insuranceTable']['items']) ; $b++) {
                                if ($reg_destination_ins[$a]->insurance_id != $dt_put['insuranceToInput'][$b]['insurance_id']  || $reg_destination_ins[$a]->company_id != $dt_put['insuranceToInput'][$b]['company_id'] || $reg_destination_ins[$a]->number_insurance != $dt_put['insuranceToInput'][$b]['number_insurance'] || $reg_destination_ins[$a]->is_primary != $dt_put['insuranceToInput'][$b]['is_primary']) {
                                    if($dt_put['insuranceToInput'][$b]['insurance_id'] == 1) {
                                        $dt_registration_insurance = array(
                                            'registration_destination_id'   => $id,
                                            'insurance_id'                  => isset($dt_put['insuranceToInput'][$b]['insurance_id']) ? $dt_put['insuranceToInput'][$b]['insurance_id'] : '',
                                            'number_insurance'              => isset($dt_put['insuranceToInput'][$b]['number_insurance']) ? $dt_put['insuranceToInput'][$b]['number_insurance'] : '0',
                                            'is_primary'                    => isset($dt_put['insuranceToInput'][$b]['is_primary']) ? $dt_put['insuranceToInput'][$b]['is_primary'] : '',
                                        );
                                        $this->db->where('registration_destination_id', $id);
                                        $this->db->where('insurance_id', $reg_destination_ins[$a]->insurance_id);
                                        $this->db->where('is_primary', isset($dt_put['insuranceToInput'][$b]['is_primary']) ? $dt_put['insuranceToInput'][$b]['is_primary'] : '');
                                        $this->db->update('registration_insurance', $dt_registration_insurance);
                                        $dt_insurance = array(
                                            'norm'                          => $reg_data->norm,
                                            'institute_branch_id'           => $institute_branch_id,
                                            'insurance_id'                  => isset($dt_put['insuranceToInput'][$b]['insurance_id']) ? $dt_put['insuranceToInput'][$b]['insurance_id'] : '',
                                            'number_insurance'              => isset($dt_put['insuranceToInput'][$b]['number_insurance']) ? $dt_put['insuranceToInput'][$b]['number_insurance'] : '',
                                            'is_primary'                    => isset($dt_put['insuranceToInput'][$b]['is_primary']) ? $dt_put['insuranceToInput'][$b]['is_primary'] : '',
                                        );
                                        $this->Insurance->replace($dt_insurance);
                                    } else {
                                        // Check company_id & service_class_id
                                        $company = $this->Company->read($dt_put['insuranceToInput'][$b]['company_id'], false);
                                        $service_class = $this->Service_class->read($dt_put['insuranceToInput'][$b]['service_class_id'], false);
                                        if($company != [] && $service_class != []) {
                                            $dt_registration_insurance = array(
                                                'registration_destination_id'   => $id,
                                                'company_id'                    => $dt_put['insuranceToInput'][$b]['company_id'],
                                                'insurance_id'                  => isset($dt_put['insuranceToInput'][$b]['insurance_id']) ? $dt_put['insuranceToInput'][$b]['insurance_id'] : '',
                                                'number_insurance'              => isset($dt_put['insuranceToInput'][$b]['number_insurance']) ? $dt_put['insuranceToInput'][$b]['number_insurance'] : '',
                                                'expired'                       => isset($dt_put['insuranceToInput'][$b]['expired']) ? $dt_put['insuranceToInput'][$b]['expired'] : '',
                                                'is_primary'                    => isset($dt_put['insuranceToInput'][$b]['is_primary']) ? $dt_put['insuranceToInput'][$b]['is_primary'] : '',
                                                'service_class_id'              => $dt_put['insuranceToInput'][$b]['service_class_id'],
                                                'service_class_name'            => $service_class[0]->name,
                                            );
                                            $this->db->where('registration_destination_id', $id);
                                            $this->db->where('insurance_id', $reg_destination_ins[$a]->insurance_id);
                                            $this->db->where('is_primary', isset($dt_put['insuranceToInput'][$b]['is_primary']) ? $dt_put['insuranceToInput'][$b]['is_primary'] : '');
                                            $this->db->update('registration_insurance', $dt_registration_insurance);
                                            // $this->Registration_Insurance->replace($dt_registration_insurance);
                                            $dt_insurance = array(
                                                'norm'                          => $reg_data->norm,
                                                'institute_branch_id'           => $institute_branch_id,
                                                'company_id'                    => $dt_put['insuranceToInput'][$b]['company_id'],
                                                'insurance_id'                  => isset($dt_put['insuranceToInput'][$b]['insurance_id']) ? $dt_put['insuranceToInput'][$b]['insurance_id'] : '',
                                                'number_insurance'              => isset($dt_put['insuranceToInput'][$b]['number_insurance']) ? $dt_put['insuranceToInput'][$b]['number_insurance'] : '',
                                                'expired'                       => isset($dt_put['insuranceToInput'][$b]['expired']) ? $dt_put['insuranceToInput'][$b]['expired'] : '',
                                                'is_primary'                    => isset($dt_put['insuranceToInput'][$b]['is_primary']) ? $dt_put['insuranceToInput'][$b]['is_primary'] : '',
                                                'service_class_id'              => $dt_put['insuranceToInput'][$b]['service_class_id'],
                                            );
                                            $this->Insurance->replace($dt_insurance);
                                        } else {
                                            $this->REST_Return(404, 'There is no company_id ' . $dt_put['insuranceToInput'][$b]['company_id'] . ' or service_class_id ' . $dt_put['insuranceToInput'][$b]['service_class_id']);
                                            return;
                                        }
                                    }
                                } 
                            }
                        }
                        if ($reg_destination->doctor_in_charge_id != $dt_put['doctor_in_charge_id']) {
                            $dt_registration_destination = array(
                                'id'                        => $reg_destination->id,
                                'registration_id'           => $reg_destination->registration_id,
                                'user_updated'              => $user_id,
                                'date_updated'              => date("Y-m-d H:i:s",time())
                            );
                            $dt_registration_destination = array_merge($dt_registration_destination,$dpjp_change);
                        } else {
                            $dt_registration_destination = array(
                                'id'                        => $reg_destination->id,
                                'registration_id'           => $reg_destination->registration_id,
                                'health_facility_item_id'   => $reg_destination->health_facility_item_id,
                                'user_updated'              => $user_id,
                                'date_updated'              => date("Y-m-d H:i:s",time())
                            );
                        }
                        $this->Registration_Destination->replace($dt_registration_destination);
                    }
                } else {
                    $this->REST_Return(404, 'Registration with id ' . $id . ' does not exist!');
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

    /**
     * Registration API
     * --------------------
     * @param: institute_branch_id
     * @param: is_cancel
     * @param: cancel_reason
     * --------------------
     * @method : POST
     * @link: version/mr/registration
     **/

    public function cancel_put($p = 0) {
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
            $this->form_validation->set_rules('is_cancel', 'is_cancel', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('cancel_reason', 'cancel_reason', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
                if($this->$model->read($id,false) != []) {
                    $dt_registration = array(
                        'id'                   => $id,
                        'is_cancel'            => 1
                    );
                    $this->$model->replace($dt_registration);

                    $this->Registration_Destination->whereFields['registration_id'] = $id;
                    $reg_destination = $this->Registration_Destination->read(0,false);
                    // Cancel registration_destination
                    for ($i=0; $i < count($reg_destination); $i++) { 
                        $bed_id = $reg_destination[$i]->bed_id;

                        if ($bed_id != null) {
                            $dt_bed = array(
                                'type_status_bed_id' => 1
                            );
                            $this->Bed->update_v2($bed_id, $dt_bed);
                        }

                        $dt_registration_destination = array(
                            'id'            => $reg_destination[$i]->id,
                            'is_cancel'     => 1,
                            'is_checkout'   => 1,
                            'user_cancel'   => $user_id,
                            'date_cancel'   => date("Y-m-d H:i:s",time()),
                            'cancel_reason' => $dt_put['cancel_reason']
                        );
                        $this->Registration_Destination->update($reg_destination[$i]->id,$dt_registration_destination);
                    }
                } else {
                    $this->REST_Return(404, 'Registration with id ' . $id . ' does not exist!');
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
     * Registration API
     * --------------------
     * @param: institute_branch_id
     * @param: is_cancel
     * @param: restore_reason
     * --------------------
     * @method : POST
     * @link: version/mr/registration
     **/

     public function restore_put($p = 0) {
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
            $this->form_validation->set_rules('is_cancel', 'is_cancel', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('restore_reason', 'restore_reason', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
                if($this->$model->read($id,false) != []) {
                    $dt_registration = array(
                        'id'                   => $id,
                        'is_cancel'            => 0
                    );
                    $this->$model->replace($dt_registration);

                    $this->Registration_Destination->whereFields['registration_id'] = $id;
                    $reg_destination = $this->Registration_Destination->read(0,false);
                    // Cancel registration_destination
                    for ($i=0; $i < count($reg_destination); $i++) { 
                        $dt_registration_destination = array(
                            'id'                => $reg_destination[$i]->id,
                            'is_cancel'         => 0,
                            'restore_reason'    => $dt_put['restore_reason']
                        );
                        $this->Registration_Destination->update($reg_destination[$i]->id,$dt_registration_destination);
                    }
                } else {
                    $this->REST_Return(404, 'Registration with id ' . $id . ' does not exist!');
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
     * Refer Internal API
     * --------------------
     * @param: institute_branch_id
     * @param: is_cancel
     * @param: restore_reason
     * --------------------
     * @method : POST
     * @link: version/mr/registration
     **/

     public function refer_internal_post($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_post     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_post);
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $reg_destination = $this->Registration_Destination->read($dt_post['registration_destination_id'],false)[0];
                // Create Refer Internal
                $codecounter = array(
                    'var_1' => 'RIN',
                    'var_2' => date('Y'),
                    'var_3' => date('md'),
                    'var_5' => date('Y-m-d')
                );
                $this->load->library("transaction");
                $checkcounter = $this->transaction->counter($codecounter);
                $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 8, "0", STR_PAD_LEFT);
                if ($checkcounter['counter_queue'] == '0') {
                    $this->Counter_queue->replace(array(
                        'var_1' => 'RIN',
                        'var_2' => date('Y'),
                        'var_3' => date('md'),
                        'var_5' => date('Y-m-d'),
                        'counter_queue' => 0
                    ));
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 8, "0", STR_PAD_LEFT);
                }
                $dt_refer_internal = array(
                    'no_refer'                      => $code,
                    'institute_branch_id'           => $institute_branch_id,
                    'registration_destination_id'   => $dt_post['registration_destination_id'],
                    'health_facility_item_id'       => $dt_post['health_facility_item_id'],
                    'doctor_in_charge_id'           => $dt_post['doctor_in_charge_id'],
                    'norm'                          => $reg_destination->norm,
                    'consultation_date'             => $dt_post['consultation_date']
                );
                $this->Refer_internal->create($dt_refer_internal);
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

    public function checkout_v2_put($p = 0) {
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
        $this->form_validation->set_rules($this->checkout_v2_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $registration_destination_id = $id;
    
        if ($registration_destination_id === 0 || $registration_destination_id === "0") {
            $this->REST_Return(400, 'registration_destination_id is required');
            return;
        }
        
        $select = 'a.id,a.health_facility_item_id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $exit_id = ($dt_put['exit_id'] == '') ? null : $dt_put['exit_id'];
        $exit_name = null;

        if ($exit_id != null) {
            $select = 'a.id, a.name';
            $exit = $this->Exit->select($exit_id, $select);

            if (count($exit) == 0) {
                $this->REST_Return(404, 'there is no exit with id ' . $exit_id);
                return;
            }

            $exit_name = $exit[0]->name;
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_registration_destination = array(
            'exit_id'           => $exit_id,
            'exit_reason'       => $exit_name,
            'checkout_date'     => date('Y-m-d H:i:s',time()),
            'is_checkout'       => $registration_destination[0]->health_facility_item_id == 'cca59ec4-d77a-45fd-b51f-aae069da22ea' ? 0 : 1,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($registration_destination_id, $dt_registration_destination);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to checkout v2 registration_destination', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}