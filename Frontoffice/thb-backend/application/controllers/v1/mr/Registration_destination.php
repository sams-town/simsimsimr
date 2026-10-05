<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Registration_destination extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("$this->directory/Registration_insurance_model", "Registration_insurance");
        $this->load->model("$this->directory/Insurance_model", "Insurance");
        $this->load->model("$this->directory/Nurse_model", "Nurse");
        $this->load->model("$this->directory/Control_bpjs_model", "Control_bpjs");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("$this->directory/Soap_inpatient_model", "Soap_inpatient");
        $this->load->model("$this->directory/Soap_s_model", "Soap_s");
        $this->load->model("$this->directory/Soap_a_model", "Soap_a");
        $this->load->model("$this->directory/Soap_p_model", "Soap_p");
        $this->load->model("$this->directory/Treatment_room_model", "Treatment_room");
        $this->load->model("$this->directory/Patient_model", "Patient");
        $this->load->model("$this->directory/inpatient/Vital_signs_monitoring_model", "Vital_signs_monitoring");
        $this->load->model("$this->directory/inpatient/Early_warning_score_model", "Early_warning_score");
        $this->load->model("$this->directory/inpatient/Early_warning_score_item_model", "Early_warning_score_item");
        $this->load->model("$this->directory/inpatient/Temperature_chart_model", "Temperature_chart");
        $this->load->model("$this->directory/inpatient/Temperature_chart_item_model", "Temperature_chart_item");
        $this->load->model("$this->directory/inpatient/Pews_model", "Pews");
        $this->load->model("$this->directory/inpatient/Pews_item_model", "Pews_item");
        $this->load->model("$this->directory/inpatient/Consultation_model", "Consultation");
        // $this->load->model("$this->directory/inpatient/Patient_tasks_model", "Patient_tasks");
        // $this->load->model("$this->directory/inpatient/Patient_tasks_item_model", "Patient_tasks_item");n tra
        // $this->load->model("$this->directory/inpatient/Nurse_tasks_model", "Nurse_tasks");
        $this->load->model("$this->directory/Humpty_dumpty_scale_fall_risk_model", "Humpty_dumpty_scale_fall_risk");
        $this->load->model("$this->directory/Morse_scale_fall_risk_model", "Morse_scale_fall_risk");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/health_facility/Bed_model", "Bed");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/health_facility/Room_setting_model", "Room_setting");
        $this->load->model("v1/health_facility/Laboratory_request_model", "Laboratory_request");
        $this->load->model("v1/health_facility/Radiology_request_model", "Radiology_request");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");
        $this->load->model("v1/pharmacy/Kardex_model", "Kardex");
        $this->load->model("v1/pharmacy/Kardex_item_model", "Kardex_item");
        $this->load->model("v1/pharmacy/Kardex_non_pharmacological_model", "Kardex_non_pharmacological");
        $this->load->model("v1/hr/Position_model", "Position");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/tr/Transaction_administration_model", "Transaction_administration");
        $this->load->model("v1/tr/Transaction_doctor_model", "Transaction_doctor");
        $this->load->model("v1/setting/Exits_model", "Exit");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/third_party/Company_model", "Company");
        $this->load->model("v1/tr/Transaction_bed_model", "Transaction_bed");
        $this->load->model("v1/Queue_model", "Queue");
        // Load Helper
        $this->load->helper("upload");
        $this->load->helper("ews");
        $this->load->helper('utils');

        $this->transfer_v2_rules = array(
            array('field' => 'registration_id', 'label' => 'registration_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'registration_destination_past_id', 'label' => 'registration_destination_past_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_sec_id', 'label' => 'doctor_in_charge_sec_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'bed_id', 'label' => 'bed_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'nurse_id', 'label' => 'nurse_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->update_doctor_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->change_checkout_date_rules = array(
            array('field' => 'checkout_date', 'label' => 'checkout_date', 'rules' => 'trim'),
        );

        $this->checkout_rules = array(
            array('field' => 'exit_id', 'label' => 'exit_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->checkout_request_rules = array(
            array('field' => 'checkout_date', 'label' => 'checkout_date', 'rules' => 'trim|required'),
            array('field' => 'is_bpjs', 'label' => 'is_bpjs', 'rules' => 'trim|required'),
            array('field' => 'is_control', 'label' => 'is_control', 'rules' => 'trim|required'),
            array('field' => 'is_death', 'label' => 'is_death', 'rules' => 'trim|required'),
        );

        $this->checkout_v2_rules = array(
            array('field' => 'exit_id', 'label' => 'exit_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->icare_status_rules = array(
            array('field' => 'icare_status', 'label' => 'icare_status', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->upload_patient_signature_rules = array(
            array('field' => 'signature', 'label' => 'signature', 'rules' => 'trim|required'),
            array('field' => 'extension', 'label' => 'extension', 'rules' => 'trim|required'),
        );

        $this->confirm_transfer_rules = array(
            array('field' => 'arrival_time', 'label' => 'arrival_time', 'rules' => 'trim|required'),
        );

        $this->change_date_control_rules = array(
            array('field' => 'date_control', 'label' => 'date_control', 'rules' => 'trim'),
        );
    }

    /**
     * Registration_destination API
     * --------------------
     * @param: registration_id
     * @param: health_facility_item_id
     * @param: doctor_in_charge_id
     * @param: bed_id
     * @param: insurances
     * --------------------
     * @method : POST
     * @link: version/mr/registration_destination
     **/

    public function transfer_post() {
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
            $this->form_validation->set_rules('registration_destination_past_id', 'registration_destination_past_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('registration_id', 'registration_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('health_facility_item_id', 'health_facility_item_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('bed_id', 'bed_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('soap_s_id', 'soap_s_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('soap_a_id', 'soap_a_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_sec_id', 'doctor_in_charge_sec_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('transfer_reason', 'transfer_reason', 'trim');
            $this->form_validation->set_rules('general_reason', 'general_reason', 'trim');
            $this->form_validation->set_rules('exam_done', 'exam_done', 'trim');
            $this->form_validation->set_rules('treatment_done', 'treatment_done', 'trim');
            $this->form_validation->set_rules('infus_sts', 'infus_sts', 'trim|max_length[1]');
            $this->form_validation->set_rules('infus', 'infus', 'trim');
            $this->form_validation->set_rules('drug_inj_sts', 'drug_inj_sts', 'trim|max_length[1]');
            $this->form_validation->set_rules('drug_inj', 'drug_inj', 'trim');
            $this->form_validation->set_rules('drug_orl_sts', 'drug_orl_sts', 'trim|max_length[1]');
            $this->form_validation->set_rules('drug_orl', 'drug_orl', 'trim');
            $this->form_validation->set_rules('derajat', 'derajat', 'trim|max_length[255]');
            $this->form_validation->set_rules('insurances[]', 'insurances', 'trim|trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check registration_id & health_facility_item_id & doctor_in_charge_id & bed_id
                $registration_destination_past = $this->Registration_destination->read($dt_post['registration_destination_past_id'],false);
                $registration = $this->Registration->read($dt_post['registration_id'],false);
                $doctor_in_charge = $this->Employee->read($dt_post['doctor_in_charge_id'], false);
                $doctor_in_charge_sec = $this->Employee->read($dt_post['doctor_in_charge_sec_id'], false);
                // $nurse = $this->Nurse->read($dt_post['nurse_id'], false);
                // $soap_s = $this->Soap_s->read($dt_post['soap_s_id'], false);
                // $soap_a = $this->Soap_a->read($dt_post['soap_a_id'], false);
                $bed = $this->Bed->read($dt_post['bed_id'],false);
                if($registration_destination_past != [] && $registration_destination_past != [] && $registration != [] && $doctor_in_charge != [] && $doctor_in_charge_sec != []) {
                    // CHECK IF THE PATIENT IS NEW
                    // Set bed status to filled
                    $dt_bed = array(
                        'id'                  => $bed[0]->id,
                        'type_status_bed_id'  => 2,
                    );
                    $this->Bed->replace($dt_bed);

                    $this->Room->whereFields['a.is_delete'] = '0';
                    $room = $this->Room->read($bed[0]->room_id,false);
                    $is_new = $this->Registration->flag_patient_destination($registration[0]->norm,$dt_post['health_facility_item_id']);
                    $flag_patient_check = $this->Registration->flag_patient_check($dt_post['registration_id'],$room[0]->health_facility_item_id);
                    if ($flag_patient_check == 'N') {
                        $this->REST_Return(422, 'Pasien Sudah di Transfer!', array());
                        return;
                    }

                    $dt_past_destination = array(
                        'id'                        => $dt_post['registration_destination_past_id'],
                        'registration_id'           => $registration_destination_past[0]->registration_id,
                        'health_facility_item_id'   => $registration_destination_past[0]->health_facility_item_id,
                        'is_transfer'               => 1,
                    );
                    $this->$model->replace($dt_past_destination);

                    $dt_registration_destination = array(
                        'registration_destination_ref_id'   => $dt_post['registration_destination_past_id'],
                        'registration_id'                   => $dt_post['registration_id'],
                        'health_facility_item_id'           => $room[0]->health_facility_item_id,
                        'doctor_in_charge_id'               => $dt_post['doctor_in_charge_id'],
                        'bed_id'                            => $dt_post['bed_id'],
                        'nurse_id'                          => $dt_post['nurse_id'],
                        'soap_s_id'                         => $dt_post['soap_s_id'],
                        'soap_a_id'                         => $dt_post['soap_a_id'],
                        'doctor_in_charge_sec_id'           => $dt_post['doctor_in_charge_sec_id'],
                        'general_reason'                    => $dt_post['general_reason'],
                        'exam_done'                         => $dt_post['exam_done'],
                        'treatment_done'                    => $dt_post['treatment_done'],
                        'infus_sts'                         => $dt_post['infus_sts'],
                        'infus'                             => $dt_post['infus'],
                        'drug_inj_sts'                      => $dt_post['drug_inj_sts'],
                        'drug_inj'                          => $dt_post['drug_inj'],
                        'drug_orl_sts'                      => $dt_post['drug_orl_sts'],
                        'drug_orl'                          => $dt_post['drug_orl'],
                        'derajat'                           => $dt_post['derajat'],
                        'queue'                             => 0,
                        'register_date'                     => $registration[0]->register_date,
                        'checkin_date'                      => date("Y-m-d H:i:s",time()),
                        'is_new'                            => $is_new,
                    );
                    $id = $this->$model->create($dt_registration_destination);

                    // // Transfer reason
                    // $dt_registration_destination_past = array(
                    //     'id'                => $dt_post['registration_destination_ref_id'],
                    //     // 'transfer_reason'   => $dt_post['transfer_reason']
                    // );
                    // $this->$model->update(0,$dt_registration_destination_past);

                    //INIT_REGISTRATION_INSURANCE
                    $dt_registration_insurance = array();
                    for ($a=0; $a < count($dt_post['insurances']); $a++) { 
                        if($dt_post['insurances'][$a]['insurance_id'] == '1') {
                            $dt_registration_insurance = array(
                                'registration_destination_id'   => $id,
                                'insurance_id'                  => isset($dt_post['insurances'][$a]['insurance_id']) ? $dt_post['insurances'][$a]['insurance_id'] : '',
                                'number_insurance'              => isset($dt_post['insurances'][$a]['number_insurance']) ? $dt_post['insurances'][$a]['number_insurance'] : '',
                                'expired'                       => isset($dt_post['insurances'][$a]['expired']) ? $dt_post['insurances'][$a]['expired'] : '',
                            );
                             
                            $this->Registration_insurance->replace($dt_registration_insurance);
                        } else {
                            // Check company_id & service_class_id
                            $company = $this->Company->read($dt_post['insurances'][$a]['company_id'], false);
                            $service_class = $this->Service_class->read($dt_post['insurances'][$a]['service_class_id'], false);
                            if($company != [] && $service_class != []) {
                                $dt_registration_insurance = array(
                                    'registration_destination_id'   => $id,
                                    'company_id'                    => $dt_post['insurances'][$a]['company_id'],
                                    'insurance_id'                  => isset($dt_post['insurances'][$a]['insurance_id']) ? $dt_post['insurances'][$a]['insurance_id'] : '',
                                    'number_insurance'              => isset($dt_post['insurances'][$a]['number_insurance']) ? $dt_post['insurances'][$a]['number_insurance'] : '',
                                    'expired'                       => isset($dt_post['insurances'][$a]['expired']) ? $dt_post['insurances'][$a]['expired'] : '',
                                    'service_class_id'              => $dt_post['insurances'][$a]['service_class_id'],
                                    'service_class_name'            => $service_class[0]->name,
                                );

                                $this->Registration_insurance->replace($dt_registration_insurance);
                            } else {
                                $this->REST_Return(404, 'There is no company_id ' . $dt_post['insurances'][$a]['company_id'] . ' or service_class_id ' . $dt_post['insurances'][$a]['service_class_id']);
                                return;
                            }
                        }
                    }
                    // $this->Registration_insurance->replace_batch($dt_registration_insurance);

                    // INIT_TRANSACTION_BED
                    $dt_transaction_bed = array(
                        'registration_destination_id'   => $id,
                        'bed_id'                        => $bed[0]->id,
                        'bed_name'                      => $bed[0]->name,
                        'costing_id'                    => $room[0]->costing_id,
                        'service_class_id'              => $room[0]->service_class_id,
                        'date_added'                    => date('Y-m-d H:i:s',time()),
                        'checkin_date'                  => $register_date
                    );
                    $this->Transaction_bed->create($dt_transaction_bed);

                    // Response request
                    $response = $this->$model->read($id);
                    unset($response[0]->nurse_id);
                    unset($response[0]->soap_s_id);
                    unset($response[0]->soap_a_id);
                    // @$response[0]->nurse = @$nurse;
                    // @$response[0]->soap_s = @$soap_s;
                    // @$response[0]->soap_a = @$soap_a;
                } else {
                    $this->REST_Return(404, 'Wrong registration_destination_ref_id or registration_id or health_facility_item_id or doctor_in_charge_id or bed_id or nurse_id or doctor_in_charge_sec_id or soap_s_id or soap_a_id!');
                    return;
                }   
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'data'     => array('id' => $id)
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function transfer_v2_post() {
        // $this->load->helper('logger');
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
        $this->form_validation->set_rules($this->transfer_v2_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        
        $select = 'a.employee_id';
        $user = $this->User->select($user_id, $select);
        $employee_added = $user[0]->employee_id;

        // Logger::info($this->debug_uniq, "|---- start transfer ----|");

        $registration_id = $dt_post['registration_id'];
        $registration_destination_past_id = $dt_post['registration_destination_past_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        $doctor_in_charge_id_2 = $dt_post['doctor_in_charge_id_2'];
        $doctor_in_charge_id_3 = $dt_post['doctor_in_charge_id_3'];
        $doctor_in_charge_id_4 = $dt_post['doctor_in_charge_id_4'];
        $doctor_in_charge_id_5 = $dt_post['doctor_in_charge_id_5'];
        $doctor_in_charge_id_6 = $dt_post['doctor_in_charge_id_6'];
        $doctor_in_charge_id_7 = $dt_post['doctor_in_charge_id_7'];
        $doctor_in_charge_id_8 = $dt_post['doctor_in_charge_id_8'];
        $doctor_in_charge_id_9 = $dt_post['doctor_in_charge_id_9'];
        $doctor_in_charge_id_10 = $dt_post['doctor_in_charge_id_10'];
        $doctor_in_charge_id_11 = $dt_post['doctor_in_charge_id_11'];
        $doctor_in_charge_id_12 = $dt_post['doctor_in_charge_id_12'];
        $doctor_in_charge_sec_id = $dt_post['doctor_in_charge_sec_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];
        $employee_doctor_id = $dt_post['employee_doctor_id'];
        $nurse_id = $dt_post['nurse_id'] ?? null;
        // $soap_s_id = $dt_post['soap_s_id'];
        // $soap_a_id = $dt_post['soap_a_id'];
        $bed_id = $dt_post['bed_id'];
        // $soap_id = $dt_post['soap_id'];
        $is_surgery = $dt_post['is_surgery'];
        $is_internal = $dt_post['is_internal'];

        if ($nurse_id == null) {
            $this->REST_Return(400, 'Asesmen perawat belum dibuat');
            return;
        }

        $select = 'a.id, a.norm, a.register_date, p.birth_date, p.sex, h.name as alias_name, p.fullname as name';
        $where = array();
        $join = array(
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
        );
        $registration = $this->Registration->select($registration_id, $select, $where, $join);
        if (count($registration) == 0) {
            $this->REST_Return(404, 'there is no registration with id ' . $registration_id);
            return;
        }

        $norm = $registration[0]->norm;
        $register_date = $registration[0]->register_date;
        $patient_birth_date = $registration[0]->birth_date;
        $patient_age = count_age($patient_birth_date);
        $patient_sex = $registration[0]->sex;
        $patient_alias = $registration[0]->alias_name;
        $patient_name = $registration[0]->name;

        $select = 'a.id, a.health_facility_item_id, a.bed_id, a.doctor_in_charge_id, hfi.name as health_facility_item_name, a.is_transfer, hfi.health_facility_id';
        $where = array();
        $join = array(
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
        );
        $registration_destination_past = $this->Registration_destination->select($registration_destination_past_id, $select, $where, $join);
        if (count($registration_destination_past) == 0) {
            $this->REST_Return(404, 'there is no registration_destination_past with id ' . $registration_destination_past_id);
            return;
        }
        
        if ($registration_destination_past[0]->is_transfer == 1) {
            $this->REST_Return(404, 'this patient already transfer ' . $registration_destination_past_id);
            return;
        }

        $health_facility_item_past_id = $registration_destination_past[0]->health_facility_item_id;
        $health_facility_item_past_name = $registration_destination_past[0]->health_facility_item_name;
        $bed_past_id = $registration_destination_past[0]->bed_id;
        $doctor_past_id = $registration_destination_past[0]->doctor_in_charge_id;
        $health_facility_past_id = $registration_destination_past[0]->health_facility_id;

        $select = 'a.id,
        a.doctor_in_charge_id_2,
        a.doctor_in_charge_id_3,
        a.doctor_in_charge_id_4,
        a.doctor_in_charge_id_5,
        a.type_consultation_doctor_2,
        a.type_consultation_doctor_3,
        a.type_consultation_doctor_4,
        a.type_consultation_doctor_5,
        ';
        $where = array(
            'a.registration_destination_id' => $registration_destination_past_id
        );
        $soap_past = $this->Soap->select(0, $select, $where);
        if (count($soap_past) == 0) {
            $this->REST_Return(404, 'Belum membuat asesmen awal IGD');
            return;
        }

        $doctor_in_charge_id_2_past = $soap_past[0]->doctor_in_charge_id_2;
        $doctor_in_charge_id_3_past = $soap_past[0]->doctor_in_charge_id_3;
        $doctor_in_charge_id_4_past = $soap_past[0]->doctor_in_charge_id_4;
        $doctor_in_charge_id_5_past = $soap_past[0]->doctor_in_charge_id_5;
        $type_consultation_doctor_2 = $soap_past[0]->type_consultation_doctor_2;
        $type_consultation_doctor_3 = $soap_past[0]->type_consultation_doctor_3;
        $type_consultation_doctor_4 = $soap_past[0]->type_consultation_doctor_4;
        $type_consultation_doctor_5 = $soap_past[0]->type_consultation_doctor_5;

        // NOTE - DOUBLE SELECT, FIX LATER!!!
        if ($bed_id != null) {
            // Logger::info($this->debug_uniq, 'bed_id: ' . $bed_id);

            $select = 'a.id, a.room_id';
            $bed = $this->Bed->select($bed_id, $select);
            if (count($bed) == 0) {
                $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
                return;
            }

            $room_id = $bed[0]->room_id;

            $select = 'a.id, health_facility_item_id';
            $room = $this->Room->select($room_id, $select);

            $health_facility_item_id = $room[0]->health_facility_item_id;
        } else {
            $norm_mother = $dt_post['norm_mother'];

            if ($norm_mother == null) {
                $this->REST_Return(400, 'norm_mother is required if no bed_id');
                return;
            }

            $select = 'a.id, b.id as bed_id, r.health_facility_item_id';
            $where = array(
                'a.norm' => $norm_mother,
                'rd.is_checkout' => 0
            );
            $join = array(
                array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
                array('bed b', 'rd.bed_id = b.id', 'INNER'),
                array('room r', 'b.room_id = r.id', 'INNER'),
            );
            $order = array('a.register_date' => 'DESC');
            $registration_mother = $this->Registration->select(0, $select, $where, $join, $order);

            $bed_id = $registration_mother[0]->bed_id;
            $health_facility_item_id = $registration_mother[0]->health_facility_item_id;
        }

        // Logger::info($this->debug_uniq, 'health_facility_item_id: ' . $health_facility_item_id);

        $select = 'a.id, a.name';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $health_facility_item_name = $health_facility_item[0]->name;

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        if ($doctor_in_charge_id_2 != null) {
            $select = 'a.id';
            $employee_2 = $this->Employee->select($doctor_in_charge_id_2, $select);
            if (count($employee_2) == 0) {
                $this->REST_Return(404, 'there is no employee_2 with id ' . $doctor_in_charge_id_2);
                return;
            }
        }

        if ($doctor_in_charge_id_3 != null) {
            $select = 'a.id';
            $employee_3 = $this->Employee->select($doctor_in_charge_id_3, $select);
            if (count($employee_3) == 0) {
                $this->REST_Return(404, 'there is no employee_3 with id ' . $doctor_in_charge_id_3);
                return;
            }
        }

        if ($doctor_in_charge_id_4 != null) {
            $select = 'a.id';
            $employee_4 = $this->Employee->select($doctor_in_charge_id_4, $select);
            if (count($employee_4) == 0) {
                $this->REST_Return(404, 'there is no employee_4 with id ' . $doctor_in_charge_id_4);
                return;
            }
        }

        if ($doctor_in_charge_id_5 != null) {
            $select = 'a.id';
            $employee_5 = $this->Employee->select($doctor_in_charge_id_5, $select);
            if (count($employee_5) == 0) {
                $this->REST_Return(404, 'there is no employee_5 with id ' . $doctor_in_charge_id_5);
                return;
            }
        }

        if ($doctor_in_charge_id_6 != null) {
            $select = 'a.id';
            $employee_6 = $this->Employee->select($doctor_in_charge_id_6, $select);
            if (count($employee_6) == 0) {
                $this->REST_Return(404, 'there is no employee_6 with id ' . $doctor_in_charge_id_6);
                return;
            }
        }

        if ($doctor_in_charge_id_7 != null) {
            $select = 'a.id';
            $employee_7 = $this->Employee->select($doctor_in_charge_id_7, $select);
            if (count($employee_7) == 0) {
                $this->REST_Return(404, 'there is no employee_7 with id ' . $doctor_in_charge_id_7);
                return;
            }
        }

        if ($doctor_in_charge_id_8 != null) {
            $select = 'a.id';
            $employee_8 = $this->Employee->select($doctor_in_charge_id_8, $select);
            if (count($employee_8) == 0) {
                $this->REST_Return(404, 'there is no employee_8 with id ' . $doctor_in_charge_id_8);
                return;
            }
        }

        if ($doctor_in_charge_id_9 != null) {
            $select = 'a.id';
            $employee_9 = $this->Employee->select($doctor_in_charge_id_9, $select);
            if (count($employee_9) == 0) {
                $this->REST_Return(404, 'there is no employee_9 with id ' . $doctor_in_charge_id_9);
                return;
            }
        }

        if ($doctor_in_charge_id_10 != null) {
            $select = 'a.id';
            $employee_10 = $this->Employee->select($doctor_in_charge_id_10, $select);
            if (count($employee_10) == 0) {
                $this->REST_Return(404, 'there is no employee_10 with id ' . $doctor_in_charge_id_10);
                return;
            }
        }

        if ($doctor_in_charge_id_11 != null) {
            $select = 'a.id';
            $employee_11 = $this->Employee->select($doctor_in_charge_id_11, $select);
            if (count($employee_11) == 0) {
                $this->REST_Return(404, 'there is no employee_11 with id ' . $doctor_in_charge_id_11);
                return;
            }
        }

        if ($doctor_in_charge_id_12 != null) {
            $select = 'a.id';
            $employee_12 = $this->Employee->select($doctor_in_charge_id_12, $select);
            if (count($employee_12) == 0) {
                $this->REST_Return(404, 'there is no employee_12 with id ' . $doctor_in_charge_id_12);
                return;
            }
        }

        if ($doctor_in_charge_sec_id != null) {
            $select = 'a.id';
            $employee_sec = $this->Employee->select($doctor_in_charge_sec_id, $select);
            if (count($employee_sec) == 0) {
                $this->REST_Return(404, 'there is no employee_sec with id ' . $doctor_in_charge_sec_id);
                return;
            }
        }

        if ($employee_nurse_id != null) {
            $select = 'a.id';
            $employee_nurse = $this->Employee->select($employee_nurse_id, $select);
            if (count($employee_nurse) == 0) {
                $this->REST_Return(404, 'there is no employee_nurse with id ' . $employee_nurse_id);
                return;
            }
        }

        if ($employee_doctor_id != null) {
            $select = 'a.id';
            $employee_doctor = $this->Employee->select($employee_doctor_id, $select);
            if (count($employee_doctor) == 0) {
                $this->REST_Return(404, 'there is no employee_doctor with id ' . $employee_doctor_id);
                return;
            }
        }

        if ($nurse_id != null) {
            $select = 'a.id';
            $nurse = $this->Nurse->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // if ($soap_id != null) {
        //     $select = 'a.id';
        //     $soap = $this->Soap->select($soap_id, $select);
        //     if (count($soap) == 0) {
        //         $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
        //         return;
        //     }
        // }

        // if ($soap_s_id != null) {
        //     $select = 'a.id';
        //     $soap_s = $this->Soap_s->select($soap_s_id);
        //     if (count($soap_s) == 0) {
        //         $this->REST_Return(404, 'there is no soap_s with id ' . $soap_s_id);
        //         return;
        //     }
        // }

        // if ($soap_a_id != null) {
        //     $select = 'a.id';
        //     $soap_a = $this->Soap_a->select($soap_a_id);
        //     if (count($soap_a) == 0) {
        //         $this->REST_Return(404, 'there is no soap_a with id ' . $soap_a_id);
        //         return;
        //     }
        // }

        $is_new = $this->Registration->flag_patient_destination($norm, $health_facility_item_id);

        /* Start Transaction */
        $this->db->trans_start();

        // $transfer_reason = $dt_post['transfer_reason'];
        // $transfer_reason_type_id = $dt_post['transfer_reason_type_id'];
        $type_visit_id = $dt_post['type_visit_id'];
        $type_emergency_id = $dt_post['type_emergency_id'];

        $dt_registration = array(
            'type_visit_id'     => $type_visit_id,
        );
    
        $st_registration = $this->Registration->update_v2($registration_id, $dt_registration);
        if ($st_registration < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $dt_past_destination = array(
            'id'                        => $registration_destination_past_id,
            'registration_id'           => $registration_id,
            'health_facility_item_id'   => $health_facility_item_past_id,
            'type_emergency_id'         => $type_emergency_id,
            // 'transfer_reason'           => $transfer_reason,
            // 'transfer_reason_type_id'   => $transfer_reason_type_id,
            'exit_id'                   => '2542a5f7-570a-449c-9eee-525569c0ce1e', // Rawat Inap exit_id
            'exit_reason'               => 'Rawat Inap',
            'checkout_date'             => date('Y-m-d H:i:s'),
            'is_transfer'               => 1,
            'is_checkout'               => 1,
            'user_updated'              => $user_id,
            'date_updated'              => date('Y-m-d H:i:s'),
        );

        $this->$model->replace($dt_past_destination);

        // Logger::info($this->debug_uniq, 'first query: ' . $this->db->last_query());

        // $general_reason = $dt_post['general_reason'];
        // $exam_done = $dt_post['exam_done'];
        // $treatment_done = $dt_post['treatment_done'];
        // $infus_sts = $dt_post['infus_sts'];
        // $infus = $dt_post['infus'];
        // $drug_inj_sts = $dt_post['drug_inj_sts'];
        // $drug_inj = $dt_post['drug_inj'];
        // $drug_orl_sts = $dt_post['drug_orl_sts'];
        // $drug_orl = $dt_post['drug_orl'];
        // $derajat = $dt_post['derajat'];
        // $isTransportable = $dt_post['isTransportable'];
        // $main_complaint = $dt_post['main_complaint'];
        
        $dt_registration_destination = array(
            'registration_destination_ref_id'   => $registration_destination_past_id,
            'registration_id'                   => $registration_id,
            'health_facility_item_id'           => $health_facility_item_id,
            'doctor_in_charge_id'               => $doctor_in_charge_id,
            'doctor_in_charge_id_2'             => $doctor_in_charge_id_2,
            'doctor_in_charge_id_3'             => $doctor_in_charge_id_3,
            'doctor_in_charge_id_4'             => $doctor_in_charge_id_4,
            'doctor_in_charge_id_5'             => $doctor_in_charge_id_5,
            'doctor_in_charge_id_6'             => $doctor_in_charge_id_6,
            'doctor_in_charge_id_7'             => $doctor_in_charge_id_7,
            'doctor_in_charge_id_8'             => $doctor_in_charge_id_8,
            'doctor_in_charge_id_9'             => $doctor_in_charge_id_9,
            'doctor_in_charge_id_10'            => $doctor_in_charge_id_10,
            'doctor_in_charge_id_11'            => $doctor_in_charge_id_11,
            'doctor_in_charge_id_12'            => $doctor_in_charge_id_12,
            'doctor_in_charge_sec_id'           => $doctor_in_charge_sec_id,
            // 'nurse_id'                          => $nurse_id,
            // 'soap_s_id'                         => $soap_s_id,
            // 'soap_a_id'                         => $soap_a_id,
            'bed_id'                            => $bed_id,
            'type_emergency_id'                 => $type_emergency_id,
            // 'general_reason'                    => $general_reason,
            // 'exam_done'                         => $exam_done,
            // 'treatment_done'                    => $treatment_done,
            // 'infus_sts'                         => $infus_sts,
            // 'infus'                             => $infus,
            // 'drug_inj_sts'                      => $drug_inj_sts,
            // 'drug_inj'                          => $drug_inj,
            // 'drug_orl_sts'                      => $drug_orl_sts,
            // 'drug_orl'                          => $drug_orl,
            // 'derajat'                           => $derajat,
            // 'isTransportable'                   => $isTransportable,
            // 'main_complaint'                    => $main_complaint,
            'user_added'                        => $user_id,
            'queue'                             => 0,
            'register_date'                     => $register_date,
            'checkin_date'                      => date("Y-m-d H:i:s", time()),
            'is_new'                            => $is_new,
        );

        $id = $this->$model->create($dt_registration_destination);

        // Consultation
        $consultation_want_to_create = [];
        for ($i=2; $i <= 5; $i++) { 
            $var_1 = "doctor_in_charge_id_$i";
            
            if ($$var_1 === null || $$var_1 === '') {
                continue;
            }

            $dt_consultation = array(
                'registration_destination_id'   => $id,
                'doctor_in_charge_id'           => $doctor_in_charge_id,
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'date_updated'                  => date('Y-m-d H:i:s',time()),
            );

            $is_past_exist = false;

            for ($j=2; $j <= 5; $j++) { 
                $var_2 = "doctor_in_charge_id_{$j}_past";
                $var_3 = "type_consultation_doctor_$j";

                if ($$var_2 === null || $$var_2 == '') {
                    continue;
                }

                if ($$var_1 == $$var_2) {
                    $is_past_exist = true;

                    if ($$var_3 != 1) { // Kalau bukan raber tidak perlu
                        continue;
                    }
                    
                    if ($$var_3 === null) {
                        $$var_3 = 1;
                    }

                    $dt_consultation['consultant_id'] = $$var_2;
                    $dt_consultation['consultation_type_id'] = $$var_3;

                    array_push($consultation_want_to_create, $dt_consultation);                    
                    break;
                }
            }

            if (!$is_past_exist) {
                $dt_consultation['consultant_id'] = $$var_1;
                $dt_consultation['consultation_type_id'] = 1;
                array_push($consultation_want_to_create, $dt_consultation);
            }
        }
        for ($i=0; $i < count($consultation_want_to_create); $i++) { 
            $this->Consultation->create($consultation_want_to_create[$i]);
        }

        // Logger::info($this->debug_uniq, 'second query: ' . $this->db->last_query());

        //INIT ADM INPATIENT
        $this->Transaction_administration->create(array(
            'registration_id' => $registration_id,
            'costing_id' => '413a9aa3-dfdc-4e97-b01e-efd44773d229',
            'costing_name' => 'SERVICE CHARGE',
            'var_1' => 0,
            'date_added' => date('Y-m-d H:i:s')
        ));

        //INIT_REGISTRATION_INSURANCE
        $insurances_arr = $dt_post['insurances'];

        for ($a=0; $a < count($insurances_arr); $a++) { 
            $insurance_id = $insurances_arr[$a]['insurance_id'];
            $number_insurance = $insurances_arr[$a]['number_insurance'];
            $expired = $insurances_arr[$a]['expired'] == '0000-00-00' ? null : $insurances_arr[$a]['expired'];
            $is_primary = $insurances_arr[$a]['is_primary'];

            $service_class_id = $insurances_arr[$a]['service_class_id'];

            if ($service_class_id == null) {
                $this->REST_Return(400, 'Lengkapi kelas pelayanan penjamin');
                return;
            }

            if ($service_class_id == '689876f6-497c-4401-83a9-4064bd5b59f9') {
                $this->REST_Return(400, 'Kelas layanan tidak boleh POLIKLINIK');
                return;
            }

            $select = 'a.id, a.name';
            $service_class = $this->Service_class->select($service_class_id, $select);

            if (count($service_class) == 0) {
                $this->REST_Return(404, 'there is no service_class_id with id ' . $service_class_id);
                return;
            }

            $service_class_name = $service_class[0]->name;

            if($insurance_id == 1) {
                $dt_registration_insurance = array(
                    'registration_destination_id'   => $id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'expired'                       => $expired,
                    'service_class_id'              => $service_class_id,
                    'service_class_name'            => $service_class_name,
                    'is_primary'                    => $is_primary,
                );
                 
                $this->Registration_insurance->replace($dt_registration_insurance);
            } else {
                $company_id = $insurances_arr[$a]['company_id'];

                $select = 'a.id';
                $company = $this->Company->select($company_id, $select);

                if (count($company) == 0) {
                    $this->REST_Return(404, 'there is no company_id with id ' . $company_id);
                    return;
                }

                if ($company_id == null) {
                    $this->REST_Return(400, 'company_id field in insurances is required if insurance_id not 1');
                    return;
                }

                $dt_registration_insurance = array(
                    'registration_destination_id'   => $id,
                    'company_id'                    => $company_id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'expired'                       => $expired,
                    'service_class_id'              => $service_class_id,
                    'service_class_name'            => $service_class_name,
                    'is_primary'                    => $is_primary,
                );

                $this->Registration_insurance->replace($dt_registration_insurance);
            }
        }

        if ($bed_id != null) {
            $select = 'a.id, a.name, a.room_id, a.type_status_bed_id';
            $bed = $this->Bed->select($bed_id, $select);
            if (count($bed) == 0) {
                $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
                return;
            }

            $bed_status = $bed[0]->type_status_bed_id;
            $bed_name = $bed[0]->name;
            $room_id = $bed[0]->room_id;

            if ($bed_status != 1 && $norm_mother == null) {
                $this->REST_Return(409, 'bed with id ' . $bed_id . ' is currently not available');
                return;
            }

            $select = 'a.id, a.costing_id, a.service_class_id, a.name, a.number';
            $room = $this->Room->select($room_id, $select);

            $room_costing_id = $room[0]->costing_id;
            $room_service_class_id = $room[0]->service_class_id;
            $room_name = $room[0]->name;
            $room_number = $room[0]->number;

            // set bed status to filled
            $dt_bed = array(
                'id'                  => $bed_id,
                'type_status_bed_id'  => 3,
            );

            $this->Bed->replace($dt_bed);

            // INIT_TRANSACTION_BED
            $dt_transaction_bed = array(
                'registration_destination_id'   => $id,
                'bed_id'                        => $bed_id,
                'bed_name'                      => $bed_name,
                'costing_id'                    => $room_costing_id,
                'service_class_id'              => $room_service_class_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'checkin_date'                  => $register_date
            );

            $this->Transaction_bed->create($dt_transaction_bed);

            if ($bed_past_id != null) {
                // count bed past
                $select = 'a.id, a.name, a.room_id, a.type_status_bed_id';
                $bed_past = $this->Bed->select($bed_past_past_id, $select);
                if (count($bed_past) == 0) {
                    $this->REST_Return(404, 'there is no bed with id ' . $bed_past_id);
                    return;
                }

                $select = 'a.id,a.checkin_date';
                $where = array(
                    'a.bed_id' => $bed_past_id,
                    'a.registration_destination_id' => $registration_destination_id
                );
                $transaction_bed_past = $this->Transaction_bed->select(0, $select, $where);

                $room_id_past = $bed_past[0]->room_id;

                $select = 'a.id, a.costing_id, a.service_class_id, a.name, a.number';
                $room_past = $this->Room->select($room_id_past, $select);

                // check costing
                $costing_id_past = $room_past[0]->costing_id;
                $service_class_id_past = $room_past[0]->service_class_id;

                $select = 'a.id, a.var_1';
                $where = array(
                    'costing_id' => $costing_id_past,
                    'service_class_id' => $service_class_id_past,
                );
                $costing_services_class_past = $this->Costing_services_class->select(0, $select, $where);

                $price_past = $costing_services_class_past[0]->var_1;
                // check costing

                // // Calculate costs
                //     // Total hours
                //     $checkin   = $transaction_bed_past[0]->checkin_date;
                //     $checkout  = date('Y-m-d H:i:s',time());
                //     $start     = new DateTime($checkin);
                //     $end       = new DateTime($checkout);

                //     $diff       = $end->diff($start);
                //     $totalHours_past =  $diff->h + ($diff->days * 24) + ($diff->m > 30 ? 1 : 0);
                //     // count prorate room
                //         $price_past               = $price_past / 24;
                //         $total_price_past         = $price_past * $totalHours_past;
                //     // count prorate room
                // // Calculate costs

                // Calculate costs
                    $checkin   = $transaction_bed_past[0]->checkin_date;
                    $checkout  = date('Y-m-d H:i:s',time());
                    
                    $start = new DateTime($checkin);
                    $end   = new DateTime($checkout);

                    // Hitung selisih waktu
                    $diff = $end->diff($start);

                    // Ambil selisih hari dasar
                    $totalDays_past = $diff->days;

                    // Jika ada sisa jam/menit, tambahkan 1 hari
                    if ($diff->h > 0 || $diff->i > 0) {
                        $totalDays_past += 1;
                    }

                    // Jika total hari 0, minimal tetap 1 hari
                    if ($totalDays_past == 0) {
                        $totalDays_past = 1;
                    }

                    // Hitung total harga
                    $total_price_past = $price_past * $totalDays_past;
                // Calculate costs
                
                // set bed status to available

                $dt_bed = array(
                    'id'                  => $bed_past_id,
                    'type_status_bed_id'  => 1,
                );
    
                $this->Bed->replace($dt_bed);

                // REPLACE_TRANSACTION_BED
                if ($transaction_bed_past[0]->id != null || $transaction_bed_past[0]->id != "") {
                    $dt_transaction_bed_past = array(
                        'id'                            => $transaction_bed_past[0]->id,
                        'var_1'                         => $price_past,
                        'var_2'                         => $totalDays_past,
                        'var_3'                         => $total_price_past,
                        'checkout_date'                 => date('Y-m-d H:i:s',time()),
                        'is_non_prorate'                => 1
                    );
                    $this->Transaction_bed->replace($dt_transaction_bed_past);
                }
            }
        }

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
            $dt_room = array(
                'is_surgery' => $is_surgery,
                'is_internal' => $is_internal,
                'gender_availability' => $patient_sex,
            );
            $this->Room->update_v2($room_id, $dt_room);
        }

        // TREATMENT_ROOM
        $from_unit = $health_facility_item_past_id;
        $to_unit = $health_facility_item_id;
        // $notes = $dt_post['notes'];
        // $general_condition = $dt_post['general_condition'];
        $aware = $dt_post['aware'];
        $eye_score = $dt_post['eye_score'];
        $verbal_score = $dt_post['verbal_score'];
        $motor_score = $dt_post['motor_score'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $pulse_frequency = $dt_post['pulse_frequency'];
        $breath_frequency = $dt_post['breath_frequency'];
        $temperature = $dt_post['temperature'];
        $oximetry_frequency = $dt_post['oximetry_frequency'];
        $oxygen_type_id = $dt_post['oxygen_type_id'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $skin_color = $dt_post['skin_color'];
        $behavior = $dt_post['behavior'];
        // $sore_score = $dt_post['sore_score'];
        // $risk_falling_type_id = $dt_post['risk_falling_type_id'];
        // $phlebitis_type_id = $dt_post['phlebitis_type_id'];
        // $phlebitis = $dt_post['phlebitis'];
        // $decubitus_type_id = $dt_post['decubitus_type_id'];
        // $decubitus = $dt_post['decubitus'];
        // $follow_up_condition = $dt_post['follow_up_condition'];
        // $performed_actions = $dt_post['performed_actions'];
        $ews_downe_score = $dt_post['ews_downe_score'];
        // $risks_during_transfer = $dt_post['risks_during_transfer'];
        // $anticipation = $dt_post['anticipation'];
        $mobilization = $dt_post['mobilization'];
        $time_out = $dt_post['time_out'];
        // $arrival_time = $dt_post['arrival_time'];

        $dt_treatment_room = array(
            'registration_destination_id'   => $id,
            'bed_id'                        => $bed_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'doctor_in_charge_id_2'         => $doctor_in_charge_id_2,
            'doctor_in_charge_id_3'         => $doctor_in_charge_id_3,
            'doctor_in_charge_id_4'         => $doctor_in_charge_id_4,
            'doctor_in_charge_id_5'         => $doctor_in_charge_id_5,
            'doctor_in_charge_id_6'         => $doctor_in_charge_id_6,
            'doctor_in_charge_id_7'         => $doctor_in_charge_id_7,
            'doctor_in_charge_id_8'         => $doctor_in_charge_id_8,
            'doctor_in_charge_id_9'         => $doctor_in_charge_id_9,
            'doctor_in_charge_id_10'        => $doctor_in_charge_id_10,
            'doctor_in_charge_id_11'        => $doctor_in_charge_id_11,
            'doctor_in_charge_id_12'        => $doctor_in_charge_id_12,
            'from_unit'                     => $from_unit,
            'to_unit'                       => $to_unit,
            'employee_nurse_id'             => $employee_nurse_id,
            'employee_doctor_id'            => $employee_doctor_id,
            // 'notes'                         => $notes,
            // 'general_condition'             => $general_condition,
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse_frequency'               => $pulse_frequency,
            'breath_frequency'              => $breath_frequency,
            'temperature'                   => $temperature,
            'oxygen_saturation'             => $oximetry_frequency,
            'oxygen_type_id'                => $oxygen_type_id,
            'weight'                        => $weight,
            'height'                        => $height,
            'skin_color'                    => $skin_color,
            'behavior'                      => $behavior,
            // 'sore_score'                    => $sore_score,
            // 'risk_falling_type_id'          => $risk_falling_type_id,
            // 'phlebitis_type_id'             => $phlebitis_type_id,
            // 'phlebitis'                     => $phlebitis,
            // 'decubitus_type_id'             => $decubitus_type_id,
            // 'decubitus'                     => $decubitus,
            // 'follow_up_condition'           => $follow_up_condition,
            // 'performed_actions'             => $performed_actions,
            'ews_downe_score'               => $ews_downe_score,
            // 'risks_during_transfer'         => $risks_during_transfer,
            // 'anticipation'                  => $anticipation,
            'mobilization'                  => $mobilization,
            'time_out'                      => $time_out,
            // 'arrival_time'                  => $arrival_time,
            'status'                        => 1,
            'date_added'                    => date('Y-m-d H:i:s', time()),
            'date_updated'                  => date('Y-m-d H:i:s', time()),
        );

        $treatment_room_id = $this->Treatment_room->create($dt_treatment_room);

        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $id,
            'employee_nurse_id'             => $employee_added,
            'source_id'                     => $treatment_room_id,
            'observation_date'              => date('Y-m-d H:i:s'),
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse'                         => $pulse_frequency,
            'temperature'                   => $temperature,
            'respiratory_rate'              => $breath_frequency,
            'oxygen_saturation'             => $oximetry_frequency,
            'oxygen_type_id'                => $oxygen_type_id,
            'weight'                        => $weight,
            'height'                        => $height,
            'skin_color'                    => $skin_color,
            'behavior'                      => $behavior,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $this->Vital_signs_monitoring->create($dt_vital_signs_monitoring);
        
        $age = count_age($patient_birth_date);
        if ($age > 18) {
            // INPUT EWS
            $dt_early_warning_score = array(
                'registration_destination_id'   => $id,
                'observation_date'              => date('Y-m-d'),
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'date_updated'                  => date('Y-m-d H:i:s',time()),
            );
    
            $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
    
            $observation_time = date('H:i:s');
            $blood_pressure = (float) $systolic;
            $pulse = (float) $pulse_frequency;
            $breathing = (float) $breath_frequency;
            $temperature = (float) $temperature;
            $oxygen_saturation = (float) $oximetry_frequency;
            $oxygen = $oxygen_type_id != 2 ? 2 : 0;
    
            $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
        
            $dt_early_warning_score_item = array(
                'early_warning_score_id' => $early_warning_score_id,
                'employee_nurse_id'      => $employee_added,
                'source_id'              => $treatment_room_id,
                'observation_time'       => $observation_time,
                'aware'                  => $aware,
                'eye_score'              => $eye_score,
                'verbal_score'           => $verbal_score,
                'motor_score'            => $motor_score,
                'blood_pressure'         => $blood_pressure,
                'pulse'                  => $pulse,
                'breathing'              => $breathing,
                'temperature'            => $temperature,
                'oxygen_saturation'      => $oxygen_saturation,
                'oxygen'                 => $oxygen,
                'total'                  => $ews_score,
                'user_added'             => $user_id,
                'date_added'             => date('Y-m-d H:i:s',time()),
                'date_updated'           => date('Y-m-d H:i:s',time()),
            );
    
            $this->Early_warning_score_item->create($dt_early_warning_score_item);
        } else {
            // INPUT PEWS
            $observation_date = date('Y-m-d');
            $observation_time = date('H:i:s');
            $breathing = (float) $breath_frequency;
            $oxygen = (int) $oxygen_type_id;
            $oxygen_saturation = (float) $oximetry_frequency;
            $pulse = (float) $pulse_frequency;
            $skin_color = (int) $skin_color;
            $systolic = (float) $systolic;
            $behavior = (int) $behavior;

            $data_pews = array(
                'birth_date'        => $patient_birth_date,
                'source_id'         => $treatment_room_id,
                'employee_nurse_id' => $employee_added,
                'user_id'           => $user_id,
                'observation_time'  => $observation_time,
                'breathing'         => $breathing,
                'oxygen'            => $oxygen,
                'oxygen_saturation' => $oxygen_saturation,
                'pulse'             => $pulse,
                'skin_color'        => $skin_color,
                'systolic'          => $systolic,
                'behavior'          => $behavior,
            );

            $this->Pews->source_create($id, $observation_date, $data_pews);
        }
        
        // INPUT TEMPERATURE CHART
        $dt_temperature_chart = array(
            'registration_destination_id' => $id,
            'employee_nurse_id'           => $employee_added,
            'monitoring_date'             => date('Y-m-d'),
            'user_added'                  => $user_id,
            'date_added'                  => date('Y-m-d H:i:s',time()),
            'date_updated'                => date('Y-m-d H:i:s',time()),
        );
    
        $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => date('H:i:s'),
            'source_id'             => $treatment_room_id,
            'rr'                    => $breath_frequency,
            'pulse'                 => $pulse_frequency,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->Temperature_chart_item->create($dt_temperature_chart_item);
        
        // SPRI
        $condition = $dt_post['condition'];
        $indication = $dt_post['indication'];
        $plan = $dt_post['plan'];

        $dt_spri = array(
            'registration_destination_id' => $registration_destination_past_id,
            'doctor_in_charge_id'   => $doctor_past_id,
            'condition'             => $condition,
            'indication'            => $indication,
            'plan'                  => $plan,
            'user_added'            => $user_id,
            'date_added'            => date("Y-m-d H:i:s",time()),
        );

        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_past_id
        );
        $soap_inpatient = $this->Soap_inpatient->select(0, $select, $where);
        if (count($soap_inpatient) == 0) {
            $this->Soap_inpatient->create($dt_spri);
        } else {
            $soap_inpatient_id = $soap_inpatient[0]->id;

            unset($dt_spri['user_added']);
            unset($dt_spri['date_added']);

            $dt_spri['date_updated'] = date("Y-m-d H:i:s",time());
            $dt_spri['user_updated'] = $user_id;

            $this->Soap_inpatient->update_v2($soap_inpatient_id, $dt_spri);
        }

        // NURSE PAIN SCORE & SCALE RISK
        $pain_score = $dt_post['pain_score'];

        $dt_functional = array(
            'pain_score'    => $pain_score,
        );

        $st_functional = $this->Nurse->update_v2($nurse_id, $dt_functional);
        if ($st_functional < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // FALL RISK
        if ($patient_age < 18) {
            $age = $dt_post['humpty_dumpty']['age'] ?? null;
            $gender = $dt_post['humpty_dumpty']['gender'] ?? null;
            $diagnosis = $dt_post['humpty_dumpty']['diagnosis'] ?? null;
            $cognitive_impairment = $dt_post['humpty_dumpty']['cognitive_impairment'] ?? null;
            $environmental_factors = $dt_post['humpty_dumpty']['environmental_factors'] ?? null;
            $surgery_sedation_anesthesia = $dt_post['humpty_dumpty']['surgery_sedation_anesthesia'] ?? null;
            $use_of_medication = $dt_post['humpty_dumpty']['use_of_medication'] ?? null;
            $total_score = $dt_post['humpty_dumpty']['total_score'] ?? null;
            
            $dt_humpty_dumpty = array(
                'registration_destination_id'   => $id,
                'nurse_id'                      => $nurse_id,
                'age'                           => $age,
                'gender'                        => $gender,
                'diagnosis'                     => $diagnosis,
                'cognitive_impairment'          => $cognitive_impairment,
                'environmental_factors'         => $environmental_factors,
                'surgery_sedation_anesthesia'   => $surgery_sedation_anesthesia,
                'use_of_medication'             => $use_of_medication,
                'total_score'                   => $total_score,
                'user_added'                    => $user_id,
                'user_updated'                  => $user_id,
                'date_added'                    => date('Y-m-d H:i:s'),
                'date_updated'                  => date('Y-m-d H:i:s'),
            );

            $select = 'a.id';
            $where = array(
                'a.nurse_id' => $nurse_id
            );
            $humpty_dumpty = $this->Humpty_dumpty_scale_fall_risk->select(0, $select, $where);
            if (count($humpty_dumpty) > 0) {
                $humpty_dumpty_id = $humpty_dumpty[0]->id;
                unset($dt_humpty_dumpty['user_added']);
                unset($dt_humpty_dumpty['date_added']);
                $st_humpty_dumpty = $this->Humpty_dumpty_scale_fall_risk->update_v2($humpty_dumpty_id, $dt_humpty_dumpty);
                if ($st_humpty_dumpty < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            } else {
                $this->Humpty_dumpty_scale_fall_risk->create($dt_humpty_dumpty);
            }
        } else {
            $history_of_falls = $dt_post['morse']['history_of_falls'];
            $secondary_diagnosis = $dt_post['morse']['secondary_diagnosis'];
            $walking_ambulation = $dt_post['morse']['walking_ambulation'];
            $side_effects_of_falling = $dt_post['morse']['side_effects_of_falling'];
            $way_of_walking = $dt_post['morse']['way_of_walking'];
            $mental_status = $dt_post['morse']['mental_status'];
            $total_score = $dt_post['morse']['total_score'];

            $dt_morse = array(
                'registration_destination_id'   => $id,
                'nurse_id'                      => $nurse_id,
                'history_of_falls'              => $history_of_falls,
                'secondary_diagnosis'           => $secondary_diagnosis,
                'walking_ambulation'            => $walking_ambulation,
                'side_effects_of_falling'       => $side_effects_of_falling,
                'way_of_walking'                => $way_of_walking,
                'mental_status'                 => $mental_status,
                'total_score'                   => $total_score,
                'user_added'                    => $user_id,
                'user_updated'                  => $user_id,
                'date_added'                    => date('Y-m-d H:i:s'),
                'date_updated'                  => date('Y-m-d H:i:s'),
            );

            $select = 'a.id';
            $where = array(
                'a.nurse_id' => $nurse_id,
            );
            $morse = $this->Morse_scale_fall_risk->select(0, $select, $where);
            if (count($morse) > 0) {
                $morse_id = $morse[0]->id;
                unset($dt_morse['user_added']);
                unset($dt_morse['date_added']);
                $st_morse = $this->Morse_scale_fall_risk->update_v2($morse_id, $dt_morse);
                if ($st_morse < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            } else {
                $this->Morse_scale_fall_risk->create($dt_morse);
            }
        }

        // IGD
        if ($health_facility_past_id == '444b3134-5b75-4b67-af72-a558d46716d7') {
            $kardex_want_to_create = [];
            $kardex_item_want_to_create = [];

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

            if (count($prescription_not_active) > 0) {
                $dt_kardex = array(
                    'registration_destination_id' => $id,
                    'doctor_in_charge_id'         => $doctor_in_charge_id,
                    'health_facility_item_id'     => $health_facility_item_id,
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
                        'is_change'                     => 0,
                        'user_added'                    => $user_id,
                        'date_added'                    => date('Y-m-d H:i:s'),
                        'date_updated'                  => date('Y-m-d H:i:s'),
                    );

                    array_push($kardex_item_want_to_create, $dt_kardex_item);
                }
            }

            // Array untuk prevent duplikat catalog di Kardex Item
            $kardex_item_catalog_ids = [];

            if (count($kardex_item_want_to_create) > 0) {
                $kardex_id = $this->Kardex->create($kardex_want_to_create);

                for ($j=0; $j < count($kardex_item_want_to_create); $j++) { 
                    $pharmacy_catalog_id = $kardex_item_want_to_create[$j]['pharmacy_catalog_id'];
                    
                    $kardex_item_want_to_create[$j]['kardex_id'] = $kardex_id;
        
                    if (!in_array($pharmacy_catalog_id, $kardex_item_catalog_ids)) {
                        $this->Kardex_item->create($kardex_item_want_to_create[$j]);
                        array_push($kardex_item_catalog_ids, $pharmacy_catalog_id); // Push ke array prevent duplikat
                    }
                }
            }
        }

        // NOTIFICATIONS
        // cb8fea0f-ce57-4596-b901-4671c8a2ef29 RUANG JIWA
        // 7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c ICCU
        // ff1f252e-daab-4bdb-b308-e9f4cb11a47d ICU ISOLASI
        // 5515af72-6dcb-415d-89e4-2433e7b0149d NICU
        // e66e6ed4-4ef4-4375-a6d0-0445e3ec3039 PICU 
        // 05079cd9-07c6-4cf9-beb0-271eb301b7c1 PERINATOLOGI
        if ($health_facility_item_id == 'cb8fea0f-ce57-4596-b901-4671c8a2ef29') { // RUANG JIWA
            $position = array(
                (object) array(
                    'id' => 'c1002e5b-5f1f-4837-af45-122673052259' // G. ANAK
                ),
            );
        } elseif ($health_facility_item_id == '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c' || 
        $health_facility_item_id == 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d' ||
        $health_facility_item_id == '5515af72-6dcb-415d-89e4-2433e7b0149d' ||
        $health_facility_item_id == 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039' ||
        $health_facility_item_id == '05079cd9-07c6-4cf9-beb0-271eb301b7c1') {
            $position = array(
                (object) array(
                    'id' => '6c7d7e53-ff24-4246-85b2-07ea77ac801f' // ICU
                ),
            );
        } else {
            $select = 'a.id';
            $where = array(
                'a.health_facility_item_id' => $health_facility_item_id,
            );
            $position = $this->Position->select(0, $select, $where);
        }
        
        if (count($position) > 0) {
            $position_id = $position[0]->id;

            // TRANSFER PASIEN
            // PASIEN A NAIK DARI B KE C RUANGAN D DENGAN BED NOMOR E
            $title = 'Transfer Pasien';
            $message = "Pasien $patient_alias $patient_name ($norm) naik dari $health_facility_item_past_name ke $health_facility_item_name ruangan $room_number dengan bed nomor $bed_name";

            $this->Notifications->createNotification(null, $position_id, $title, $message, 'transfer', ['treatment_room_id' => $treatment_room_id]);

            // GIZI
            $this->Notifications->createNotification(null, '8ca42f62-85bb-45f8-bf32-152a792e0f40', $title, $message, 'transfer', ['treatment_room_id' => $treatment_room_id]);
        }


        // // ASSIGN NURSE TASKS & CREATE NEW PATIENT TASK
        // $this->Patient_tasks->new_task($id, date('Y-m-d'), $user_id);

        // // Jadwal shift pagi/sore/malam
        // $now = time();
        // $morning = jakartaTimestamp('07:00:00');
		// $afternoon = jakartaTimestamp('14:00:00');
		// $night = jakartaTimestamp('21:00:00');

        // if ($now >= $morning && $now < $afternoon) { // Jam 07.00 - 14.00
        //     $shift_type_id = 1;
        // } elseif ($now >= $afternoon && $now < $night) { // Jam 14.00 - 21.00
        //     $shift_type_id = 2;
        // } elseif ($now >= $night || $now < $morning) { // Jam 21.00 - 00.00 - 07.00
        //     $shift_type_id = 3;
        // }

        // // Mencari perawat bangsal tujuan yang memegang least pasien dan assign ke perawat itu
        // $select = 'a.id, a.nurse_id, a.nurse_schedule_item_id, e.name, COUNT(*) as total';
        // $where = array(
        //     'nsi.shift_type_id' => $shift_type_id,
        //     'nsi.date' => date('Y-m-d'),
        //     'rd.health_facility_item_id' => $health_facility_item_id,
        // );
        // $join = array(
        //     array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
        //     array('employee e', 'a.nurse_id = e.id', 'INNER'),
        //     array('nurse_schedule_item nsi', 'a.nurse_schedule_item_id = nsi.id', 'INNER'),
        // );
        // $this->db->group_by('a.nurse_id');
        // $order = array('total' => 'ASC');
        // $nurse_tasks = $this->Nurse_tasks->select(0, $select, $where, $join, $order);

        // if (count($nurse_tasks) > 0) {
        //     $nurse_id = $nurse_tasks[0]->nurse_id;
        //     $nurse_schedule_item_id = $nurse_tasks[0]->nurse_schedule_item_id;

        //     $dt_nurse_tasks = array(
        //         'registration_destination_id' => $id,
        //         'nurse_schedule_item_id' => $nurse_schedule_item_id,
        //         'nurse_id' => $nurse_id,
        //         'date_added' => date('Y-m-d H:i:s'),
        //         'date_updated' => date('Y-m-d H:i:s'),
        //         'user_added' => $user_id,
        //     );

        //     $this->Nurse_tasks->create($dt_nurse_tasks);
        // }

        $this->db->trans_complete();
        /* End Transaction */

        // Logger::info($this->debug_uniq, "|---- finish transfer ----|");

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to transfer patient', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;  
    }

    /**
     * GET Registration_destination with API
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
            $this->$model->whereFields['a.is_cancel'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output != [] && $id != 0) {
                if($output[0]->registration_destination_ref_id != '') {
                    $registration_destination_ref = $this->$model->read($output[0]->registration_destination_ref_id, false);
                    unset($output[0]->registration_destination_ref_id);
                    $output[0]->registration_destination_ref = $registration_destination_ref[0];
                }
                if($output[0]->nurse_id != '') {
                    $nurse = $this->Nurse->read($output[0]->nurse_id, false);
                    unset($output[0]->nurse_id);
                    $output[0]->nurse = $nurse[0];
                }
                if($output[0]->soap_s_id != '') {
                    $soap_s = $this->Soap_s->read($output[0]->soap_s_id, false);
                    unset($output[0]->soap_s_id);
                    $output[0]->soap_s = $soap_s[0];
                }
                if($output[0]->soap_a_id != '') {
                    $soap_a = $this->Soap_a->read($output[0]->soap_a_id, false);
                    unset($output[0]->soap_a_id);
                    $output[0]->soap_a = $soap_a[0];
                }
            
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
            $this->$model->whereFields['a.is_cancel'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                if($output->rows[$i]->registration_destination_ref_id != '') {
                    $registration_destination_ref = $this->$model->read($output->rows[$i]->registration_destination_ref_id, false);
                    unset($output->rows[$i]->registration_destination_ref_id);
                    $output->rows[$i]->registration_destination_ref = $registration_destination_ref[0];
                }
                if($output->rows[$i]->nurse_id != '') {
                    $nurse = $this->Nurse->read($output->rows[$i]->nurse_id, false);
                    unset($output->rows[$i]->nurse_id);
                    $output->rows[$i]->nurse = $nurse[0];
                }
                if($output->rows[$i]->soap_s_id != '') {
                    $soap_s = $this->Soap_s->read($output->rows[$i]->soap_s_id, false);
                    unset($output->rows[$i]->soap_s_id);
                    $output->rows[$i]->soap_s = $soap_s[0];
                }
                if($output->rows[$i]->soap_a_id != '') {
                    $soap_a = $this->Soap_a->read($output->rows[$i]->soap_a_id, false);
                    unset($output->rows[$i]->soap_a_id);
                    $output->rows[$i]->soap_a = $soap_a[0];
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
     * Registration_destination API
     * --------------------
     * @param: health_facility_item_id
     * @param: doctor_in_charge_id
     * @param: register_date
     * @param: checkin_date
     * @param: checkout_date
     * @param: exit_id
     * @param: exit_reason
     * @param: insurances
     * --------------------
     * @method : POST
     * @link: version/mr/registration_destination
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
            $this->form_validation->set_rules('health_facility_item_id', 'health_facility_item_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('register_date', 'register_date', 'trim');
            $this->form_validation->set_rules('checkin_date', 'checkin_date', 'trim');
            $this->form_validation->set_rules('checkout_date', 'checkout_date', 'trim');
            $this->form_validation->set_rules('exit_id', 'exit_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('exit_reason', 'exit_reason', 'trim');
            $this->form_validation->set_rules('insurances[]', 'insurances', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_reg_des = $this->$model->read($id,false); 
                if($dt_reg_des != []) {
                    // Check health_facility_item id & doctor_in_charge_id & exit_id
                    $health_facility_item = $this->Health_facility_item->read($dt_put['health_facility_item_id'], false);
                    $doctor_in_charge = $this->Employee->read($dt_put['doctor_in_charge_id'], false) != [];
                    $exit = $this->Exit->read($dt_put['exit_id'], false) != [];
                    if($health_facility_item != [] && $doctor_in_charge != [] && $exit != []) {
                        $dt_registration_destination = array(
                            'id'                        => $id,
                            'health_facility_item_id'   => isset($dt_put['health_facility_item_id']) ? $dt_put['health_facility_item_id'] : $dt_reg_des[0]->health_facility_item_id,
                            'doctor_in_charge_id'       => isset($dt_put['doctor_in_charge_id']) ? $dt_put['doctor_in_charge_id'] : $dt_reg_des[0]->doctor_in_charge_id,
                            'register_date'             => isset($dt_put['register_date']) ? $dt_put['register_date'] : $dt_reg_des[0]->register_date,
                            'checkin_date'              => isset($dt_put['checkin_date']) ? $dt_put['checkin_date'] : $dt_reg_des[0]->checkin_date,
                            'checkout_date'             => isset($dt_put['checkout_date']) ? $dt_put['checkout_date'] : $dt_reg_des[0]->checkout_date,
                            'exit_id'                   => isset($dt_put['exit_id']) ? $dt_put['exit_id'] : $dt_reg_des[0]->exit_id,
                            'exit_reason'               => isset($dt_put['exit_reason']) ? $dt_put['exit_reason'] : $dt_reg_des[0]->exit_reason
                        );
                        $this->$model->update($id, $dt_registration_destination);
                        
                        if(isset($dt_put['insurances'])) {
                            $reg_insu = $this->Registration_insurance->read($id,false);
                            if($reg_insu != []) {
                                $temp = array();
                                for ($a=0; $a < count($reg_insu); $a++) { 
                                    array_push($temp, array(
                                        'registration_destination_id'   => $id,
                                        'insurance_id'                  => $reg_insu[$a]->insurance_id,
                                        'company_id'                    => $reg_insu[$a]->company_id,
                                        'company_name'                  => $reg_insu[$a]->company_name,
                                        'number_insurance'              => $reg_insu[$a]->number_insurance,
                                        'expired'                       => $reg_insu[$a]->expired,
                                        'service_class_id'              => $reg_insu[$a]->service_class_id,
                                        'service_class_name'            => $reg_insu[$a]->service_class_name,
                                        'is_cancel'                     => '1',
                                    ));
                                }
                                $this->Registration_insurance->replace_batch($temp);
                
                                $dt_reg_insu = array();
                                for ($i=0; $i < count($dt_put['insurances']) ; $i++) {
                                    // Check company_id & service_class_id
                                    $company = $this->Company->read($dt_put['insurances'][$i]['company_id'], false);
                                    $service_class = $this->Service_class->read($dt_put['insurances'][$i]['service_class_id'], false);
                                    if($company != [] && $service_class != []) {
                                        array_push($dt_reg_insu, array(
                                            'registration_destination_id'   => $id,
                                            'insurance_id'                  => $dt_put['insurances'][$i]['insurance_id'],
                                            'company_id'                    => $dt_put['insurances'][$i]['company_id'],
                                            'number_insurance'              => $dt_put['insurances'][$i]['number_insurance'],
                                            'expired'                       => $dt_put['insurances'][$i]['expired'],
                                            'service_class_id'              => $dt_put['insurances'][$i]['service_class_id'],
                                            'service_class_name'            => $service_class[0]->name,
                                            'is_cancel'                     => '0'
                                        ));
                                    } else {
                                        $this->REST_Return(404, 'There is no company_id ' . $dt_put['insurances'][$i]['company_id'] . ' or service_class_id ' . $dt_put['insurances'][$i]['service_class_id']);
                                        return;
                                    }
                                }
                                $this->Registration_insurance->replace_batch($dt_reg_insu);
                            }
                        }
                    } else {
                        $this->REST_Return(404, 'There is no health_facility_item ' . $dt_put['health_facility_item_id'] . ' or doctor_in_charge ' . $dt_put['doctor_in_charge_id'] . ' or exit ' . $dt_put['exit_id']);
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Registration Destination with id ' . $id . ' does not exist!');
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

    public function update_doctor_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_doctor_rules);
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

        $select = 'a.id, a.health_facility_item_id, a.registration_id, r.norm, a.doctor_in_charge_id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $norm = $registration_destination[0]->norm;
        $cur_health_facility_item_id = $registration_destination[0]->health_facility_item_id;
        $doctor_in_charge_id_past = $registration_destination[0]->doctor_in_charge_id;
        $registration_id = $registration_destination[0]->registration_id;



        $select = 'a.id, a.health_facility_id';
        $cur_health_facility_item = $this->Health_facility_item->select($cur_health_facility_item_id, $select);

        $health_facility_id = $cur_health_facility_item[0]->health_facility_id;

        $how_to_enter_id = $dt_put['how_to_enter_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $doctor_in_charge_id_2 = $dt_put['doctor_in_charge_id_2'];
        $doctor_in_charge_id_3 = $dt_put['doctor_in_charge_id_3'];
        $doctor_in_charge_id_4 = $dt_put['doctor_in_charge_id_4'];
        $doctor_in_charge_id_5 = $dt_put['doctor_in_charge_id_5'];
        $doctor_in_charge_id_6 = $dt_put['doctor_in_charge_id_6'];
        $doctor_in_charge_id_7 = $dt_put['doctor_in_charge_id_7'];
        $doctor_in_charge_id_8 = $dt_put['doctor_in_charge_id_8'];
        $doctor_in_charge_id_9 = $dt_put['doctor_in_charge_id_9'];
        $doctor_in_charge_id_10 = $dt_put['doctor_in_charge_id_10'];
        $doctor_in_charge_id_11 = $dt_put['doctor_in_charge_id_11'];
        $doctor_in_charge_id_12 = $dt_put['doctor_in_charge_id_12'];

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        if ($doctor_in_charge_id_2 != null) {
            $select = 'a.id';
            $employee_2 = $this->Employee->select($doctor_in_charge_id_2, $select);
    
            if (count($employee_2) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_2);
                return;
            }
        }

        if ($doctor_in_charge_id_3 != null) {
            $select = 'a.id';
            $employee_3 = $this->Employee->select($doctor_in_charge_id_3, $select);
    
            if (count($employee_3) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_3);
                return;
            }
        }

        if ($doctor_in_charge_id_4 != null) {
            $select = 'a.id';
            $employee_4 = $this->Employee->select($doctor_in_charge_id_4, $select);
    
            if (count($employee_4) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_4);
                return;
            }
        }

        if ($doctor_in_charge_id_5 != null) {
            $select = 'a.id';
            $employee_5 = $this->Employee->select($doctor_in_charge_id_5, $select);
    
            if (count($employee_5) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_5);
                return;
            }
        }

        if ($doctor_in_charge_id_6 != null) {
            $select = 'a.id';
            $employee_6 = $this->Employee->select($doctor_in_charge_id_6, $select);
    
            if (count($employee_6) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_6);
                return;
            }
        }

        if ($doctor_in_charge_id_7 != null) {
            $select = 'a.id';
            $employee_7 = $this->Employee->select($doctor_in_charge_id_7, $select);
    
            if (count($employee_7) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_7);
                return;
            }
        }

        if ($doctor_in_charge_id_8 != null) {
            $select = 'a.id';
            $employee_8 = $this->Employee->select($doctor_in_charge_id_8, $select);
    
            if (count($employee_8) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_8);
                return;
            }
        }

        if ($doctor_in_charge_id_9 != null) {
            $select = 'a.id';
            $employee_9 = $this->Employee->select($doctor_in_charge_id_9, $select);
    
            if (count($employee_9) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_9);
                return;
            }
        }

        if ($doctor_in_charge_id_10 != null) {
            $select = 'a.id';
            $employee_10 = $this->Employee->select($doctor_in_charge_id_10, $select);
    
            if (count($employee_10) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_10);
                return;
            }
        }

        if ($doctor_in_charge_id_11 != null) {
            $select = 'a.id';
            $employee_11 = $this->Employee->select($doctor_in_charge_id_11, $select);
    
            if (count($employee_11) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_11);
                return;
            }
        }

        if ($doctor_in_charge_id_12 != null) {
            $select = 'a.id';
            $employee_12 = $this->Employee->select($doctor_in_charge_id_12, $select);
    
            if (count($employee_12) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id_12);
                return;
            }
        }

        $insurances = $dt_put['insurances'];

        /* Start Transaction */
        $this->db->trans_start();

        if (isset($how_to_enter_id)){
            if ($how_to_enter_id != '' && $how_to_enter_id != null && $registration_id != null) {
                $dt_registration = array(
                    'how_to_enter_id'     => $how_to_enter_id,
                );
            
                $this->Registration->update($registration_id, $dt_registration);
            }
        }

        $dt_registration_destination = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'doctor_in_charge_id_2' => $doctor_in_charge_id_2,
            'doctor_in_charge_id_3' => $doctor_in_charge_id_3,
            'doctor_in_charge_id_4' => $doctor_in_charge_id_4,
            'doctor_in_charge_id_5' => $doctor_in_charge_id_5,
            'doctor_in_charge_id_6' => $doctor_in_charge_id_6,
            'doctor_in_charge_id_7' => $doctor_in_charge_id_7,
            'doctor_in_charge_id_8' => $doctor_in_charge_id_8,
            'doctor_in_charge_id_9' => $doctor_in_charge_id_9,
            'doctor_in_charge_id_10' => $doctor_in_charge_id_10,
            'doctor_in_charge_id_11' => $doctor_in_charge_id_11,
            'doctor_in_charge_id_12' => $doctor_in_charge_id_12,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        if ($health_facility_id =='3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
            // check if not change doctor_in_charge_id dont change a queue!
            if ($dt_put['doctor_in_charge_id'] != $registration_destination[0]->doctor_in_charge_id) {
                $health_facility_item_id = $dt_put['health_facility_item_id'];
    
                if ($health_facility_item_id == null) {
                    $this->REST_Return(400, 'health_facility_item_id is required if outpatient');
                    return;
                }
    
                $select = 'a.id';
                $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
    
                if (count($health_facility_item) == 0) {
                    $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
                    return;
                }
    
                $queue = $this->Queue->get_queue_number($health_facility_item_id, $doctor_in_charge_id);
    
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
    
                $dt_registration_destination['health_facility_item_id'] = $health_facility_item_id;
                $dt_registration_destination['doctor_schedule_item_id'] = $queue['doctor_schedule_item_id'];
                $dt_registration_destination['code_queue'] = $queue['queue_code']; // if emergency or inpatient queue 0
                $dt_registration_destination['queue'] = $queue['queue']; // if emergency or inpatient queue 0
            }
            // check if not change doctor_in_charge_id dont change a queue!
        }

        for ($i=0; $i < count($insurances); $i++) { 
            if (!isset($insurances[$i]['is_primary'])) {
                $this->REST_Return(400, 'is_primary field in insurances is required');
                return;
            }

            if (!isset($insurances[$i]['insurance_id'])) {
                $this->REST_Return(400, 'insurance_id field in insurances is required');
                return;
            }

            if (!isset($insurances[$i]['number_insurance'])) {
                $this->REST_Return(400, 'number_insurance field in insurances is required');
                return;
            }

            $is_primary = $insurances[$i]['is_primary'];
            $insurance_id = $insurances[$i]['insurance_id'];
            $number_insurance = $insurances[$i]['number_insurance'];
            $expired = $insurances[$i]['expired'];
            $is_delete = $insurances[$i]['is_delete'];

            $dt_insurance = array(
                'registration_destination_id' => $registration_destination_id,
                'is_primary' => $is_primary,
                'insurance_id' => $insurance_id,
                'number_insurance' => $number_insurance,
                'expired' => $expired,
            );

            if ($insurance_id != 1) {
                if ($insurances[$i]['company_id'] == null) {
                    $this->REST_Return(400, 'company_id field in insurances is required if insurance_id is not 2');
                    return;
                }

                if ($insurances[$i]['service_class_id'] == null) {
                    $this->REST_Return(400, 'service_class_id field in insurances is required if insurance_id is not 2');
                    return;
                }

                $company_id = $insurances[$i]['company_id'];
                $company_id_cob = $insurances[$i]['company_id_cob'];
                $service_class_id = $insurances[$i]['service_class_id'];

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

                $dt_insurance['company_id'] = $company_id;
                $dt_insurance['company_id_cob'] = $company_id_cob;
                $dt_insurance['is_cob'] = ($company_id_cob == '' || $company_id_cob == null ? 0 : 1);
                $dt_insurance['service_class_id'] = $service_class_id;
                $dt_insurance['service_class_name'] = $service_class_name;
            }

            $is_primary_tmp = ($is_primary == 1) ? 0 : 1;

            $select = 'a.is_primary';
            $where = array('registration_destination_id' => $registration_destination_id, 'insurance_id' => $insurance_id, 'is_primary' => $is_primary_tmp);
            $insurance = $this->Registration_insurance->select(0, $select, $where);

            if (count($insurance) > 0) {
                $this->Registration_insurance->whereFields = array(
                    'registration_destination_id' => $registration_destination_id,
                    'insurance_id' => $insurance_id,
                    'is_primary' => $is_primary_tmp,
                );
                $this->Registration_insurance->update(0, $dt_insurance);
            } else {
                $this->Registration_insurance->replace($dt_insurance);
            }

            unset($dt_insurance['registration_destination_id']);
            unset($dt_insurance['service_class_name']);
            $dt_insurance['norm'] = $norm;
            $dt_insurance['institute_branch_id'] = '240b8d8b-245e-42ba-85e6-2ba7d8ad5640';

            $select = 'a.is_primary';
            $where = array('norm' => $norm, 'insurance_id' => $insurance_id, 'is_primary' => $is_primary_tmp);
            $insu = $this->Insurance->select(0, $select, $where);

            if (count($insu) > 0) {
                $this->Insurance->whereFields = array(
                    'norm' => $norm,
                    'company_id' => ($company_id == null) ? '' : $company_id,
                    'company_id_cob' => ($company_id_cob == null) ? '' : $company_id_cob,
                    'insurance_id' => $insurance_id,
                    'is_primary' => $is_primary_tmp,
                );
                $this->Insurance->update(0, $dt_insurance);
            } else {
                $this->Insurance->replace($dt_insurance);
            }

            if ($is_delete == 1) {
                $del_status = $this->Registration_insurance->delete_by_primary($registration_destination_id, $insurance_id, $is_primary);

                if ($del_status == 0) {
                    $this->REST_Return(422, 'there is a problem when deleting registration_insurance');
                    return;
                }
            }
        }

        $this->$model->update($registration_destination_id, $dt_registration_destination);


        // change soap doctor if emergency
        if ($health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') { // IGD
            $select = 'a.id';
            $where = array(
                'registration_destination_id' => $registration_destination_id
            );
            $soap = $this->Soap->select(0, $select, $where);

            if (count($soap) > 0) {
                $soap_id = $soap[0]->id;

                $dt_soap = array(
                    'doctor_in_charge_id' => $doctor_in_charge_id
                );

                $soap_update = $this->Soap->update_v2($soap_id, $dt_soap);

                if ($soap_update == -1) {
                    $this->REST_Return(422, 'error while trying to update soap doctor');
                    return;
                }

                // change transaction doctor
                $select = 'a.id';
                $where = array(
                    'soap_id' => $soap_id,
                    'a.doctor_in_charge_id' => $doctor_in_charge_id_past,
                );
                $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);

                for ($i=0; $i < count($transaction_doctor); $i++) { 
                    $transaction_doctor_id = $transaction_doctor[$i]->id;

                    $transaction_doctor = array(
                        'doctor_in_charge_id' => $doctor_in_charge_id
                    );

                    $transaction_doctor_update = $this->Transaction_doctor->update_v2($transaction_doctor_id, $transaction_doctor);

                    if ($transaction_doctor_update == -1) {
                        $this->REST_Return(422, 'error while trying to update transaction_doctor doctor');
                        return;
                    }
                }

                // change laboratory doctor
                $select = 'a.id';
                $where = array(
                    'soap_id' => $soap_id,
                    'a.doctor_in_charge_id' => $doctor_in_charge_id_past,
                );
                $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

                for ($i=0; $i < count($laboratory_request); $i++) { 
                    $laboratory_request_id = $laboratory_request[$i]->id;

                    $dt_laboratory_request = array(
                        'doctor_in_charge_id' => $doctor_in_charge_id
                    );

                    $laboratory_request_update = $this->Laboratory_request->update_v2($laboratory_request_id, $dt_laboratory_request);

                    if ($laboratory_request_update == -1) {
                        $this->REST_Return(422, 'error while trying to update laboratory_request doctor');
                        return;
                    }
                }

                // change radiology doctor
                $select = 'a.id';
                $where = array(
                    'soap_id' => $soap_id,
                    'a.doctor_in_charge_id' => $doctor_in_charge_id_past,
                );
                $radiology_request = $this->Radiology_request->select(0, $select, $where);

                for ($i=0; $i < count($radiology_request); $i++) { 
                    $radiology_request_id = $radiology_request[$i]->id;

                    $dt_radiology_request = array(
                        'doctor_in_charge_id' => $doctor_in_charge_id
                    );

                    $radiology_request_update = $this->Radiology_request->update_v2($radiology_request_id, $dt_radiology_request);

                    if ($radiology_request_update == -1) {
                        $this->REST_Return(422, 'error while trying to update radiology_request doctor');
                        return;
                    }
                }

                // change prescription doctor
                $select = 'a.id';
                $where = array(
                    'soap_id' => $soap_id,
                    'a.doctor_in_charge_id' => $doctor_in_charge_id_past,
                );
                $soap_p = $this->Soap_p->select(0, $select, $where);

                if (count($soap_p) > 0) {
                    $soap_p_id = $soap_p[0]->id;

                    $dt_soap_p = array(
                        'doctor_in_charge_id' => $doctor_in_charge_id
                    );
    
                    $soap_p_update = $this->Soap_p->update_v2($soap_p_id, $dt_soap_p);
    
                    if ($soap_p_update == -1) {
                        $this->REST_Return(422, 'error while trying to update soap_p doctor');
                        return;
                    }

                    $select = 'a.id';
                    $where = array(
                        'soap_p_id' => $soap_p_id,
                    );
                    $prescription = $this->Prescription->select(0, $select, $where);
    
                    for ($i=0; $i < count($prescription); $i++) { 
                        $prescription_id = $prescription[$i]->id;
    
                        $dt_prescription = array(
                            'doctor_in_charge_id' => $doctor_in_charge_id
                        );
    
                        $prescription_update = $this->Prescription->update_v2($prescription_id, $dt_prescription);
    
                        if ($prescription_update == -1) {
                            $this->REST_Return(422, 'error while trying to update prescription doctor');
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
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update doctor registration_destination', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    /**
     * Registration_destination API
     * --------------------
     * @param: institute_branch_id
     * @param: is_cancel
     * @param: restore_reason
     * --------------------
     * @method : POST
     * @link: version/mr/registration_destination
     **/

     public function checkout_put($p = 0) {
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
        $this->form_validation->set_rules($this->checkout_rules);
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

        $select = 'a.id, a.bed_id, a.checkin_date, r.norm, a.nurse_id';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->$model->select($registration_destination_id, $select, $where, $join);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        if ($registration_destination[0]->nurse_id == null && $registration_destination[0]->nurse_id == '' ) {
            $this->REST_Return(404, 'Anda belum mengisi formulir pasien pulang !' . $registration_destination_id);
            return;
        }

        $bed_id = $registration_destination[0]->bed_id;
        $checkin_date = $registration_destination[0]->checkin_date;

        $exit_id = $dt_put['exit_id'];
        $checkout_date = date('Y-m-d H:i:s');

        $select = 'a.id, a.name';
        $exit = $this->Exit->select($exit_id, $select);

        if (count($exit) == 0) {
            $this->REST_Return(404, 'there is no exit with id ' . $exit_id);
            return;
        }

        $exit_name = $exit[0]->name;

        /* Start Transaction */
        $this->db->trans_start();

        $norm = $registration_destination[0]->norm;

        $select = 'a.norm, a.norm_mother';
        $patient = $this->Patient->select($norm, $select);

        $norm_mother = $patient[0]->norm_mother;

        $registration_destination_id_mother = null;
        $registration_destination_id_baby = null;

        if ($norm_mother != null) {
            $select = 'a.id, rd.id as registration_destination_id, b.id as bed_id';
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

            $registration_destination_id_mother = $registration_mother[0]->registration_destination_id;
            $bed_id_mother = $registration_mother[0]->bed_id;
        } else {
            $select = 'a.norm, a.norm_mother';
            $where = array(
                'norm_mother' => $norm,
            );
            $patient = $this->Patient->select(0, $select, $where);
            
            if (count($patient) > 0) {
                $norm_baby = $patient[0]->norm;

                $select = 'a.id, rd.id as registration_destination_id, b.id as bed_id';
                $where = array(
                    'a.norm' => $norm_baby,
                    'rd.is_checkout' => 0
                );
                $join = array(
                    array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
                    array('bed b', 'rd.bed_id = b.id', 'INNER'),
                );
                $order = array('a.register_date' => 'DESC');
                $registration_baby = $this->Registration->select(0, $select, $where, $join, $order);

                $registration_destination_id_baby = $registration_baby[0]->registration_destination_id;
                $bed_id_baby = $registration_baby[0]->bed_id;
            }
        }

        $dt_registration_destination = array(
            'exit_id'           => $exit_id,
            'exit_reason'       => $exit_name,
            'date_task_7'       => $checkout_date,
            'is_checkout'       => 1
        );

        $this->$model->update_v2($registration_destination_id, $dt_registration_destination);

        if ($registration_destination_id_mother != null) {
            if ($bed_id == $bed_id_mother) {
                $this->$model->update_v2($registration_destination_id_mother, $dt_registration_destination);
            }
        }

        if ($registration_destination_id_baby != null) {
            if ($bed_id == $bed_id_baby) {
                $this->$model->update_v2($registration_destination_id_baby, $dt_registration_destination);
            }
        }

        $select = 'a.id, a.name, a.room_id';
        $bed = $this->Bed->select($bed_id, $select);

        $bed_name = $bed[0]->name;
        $room_id = $bed[0]->room_id;

        $select = 'a.id, a.costing_id, a.service_class_id';
        $room = $this->Room->select($room_id, $select);

        $costing_id = $room[0]->costing_id;
        $service_class_id = $room[0]->service_class_id;

        $select = 'a.id, a.var_1';
        $where = array(
            'costing_id' => $costing_id,
            'service_class_id' => $service_class_id,
        );
        $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

        $price = $costing_services_class[0]->var_1;
        
        // Calculate costs
        $startTimeStamps = $checkin_date;   $endTimeStamps = $checkout_date;
        $start = explode(' ',$startTimeStamps);     $end = explode(' ',$endTimeStamps);
        $startDate = strtotime($start[0]);      $startTime = $start[1];
        $endDate = strtotime($end[0]);      $endTime = $end[1];

        $timeDiff = abs($startDate - $endDate);
        $days_between = $timeDiff / 86400;  // 86400 seconds in one day
        (strtotime($startTime) > strtotime('18:00:00') && strtotime($startTime) < strtotime('24:00:00')) ? $count_day_in = 0 : $count_day_in = 1;
        (strtotime($endTime) < strtotime('12:00:00')) ? $count_day_out = 0 : $count_day_out = 1;
        
        $numbers_of_days = $count_day_in + $days_between + $count_day_out;
        $total_price = $price * $numbers_of_days;

        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id
        );
        $transaction_bed = $this->Transaction_bed->select(0, $select, $where);

        $transaction_bed_id = $transaction_bed[0]->id;

        // Transaction_bed
        // $dt_transaction_bed = array(
        //     'id'                            => $transaction_bed_id,
        //     'registration_destination_id'   => $registration_destination_id,
        //     'bed_id'                        => $bed_id,
        //     'bed_name'                      => $bed_name,
        //     'costing_id'                    => $costing_id,
        //     'service_class_id'              => $service_class_id,
        //     'var_1'                         => $price,
        //     'var_2'                         => $numbers_of_days,
        //     'var_3'                         => $total_price,
        //     'date_added'                    => date("Y-m-d H:i:s",time())
        // );
        // $this->Transaction_bed->replace($dt_transaction_bed);

        // Change bed status
        $select = 'a.id, a.type_status_bed_id, rd.id as registration_destination_id';
        $where = array(
            'a.is_delete' => 0,
        );
        $join = array(
            array('registration_destination rd', 'a.id = rd.bed_id AND rd.is_checkout = 0', 'LEFT'),
        );
        $check_multi_destination = $this->Bed->select($bed_id, $select, $where, $join);
        $check_multi_destination_id = $check_multi_destination[0]->registration_destination_id;
        if ($check_multi_destination_id == null) { // CHECK IF ANY OTHER PATIENT THAT HAS THE SAME BED (BUG OR HUMAN ERROR)
            $dt_bed = array(
                'id'                    => $bed_id,
                'type_status_bed_id'    => 1
            );
            $this->Bed->replace($dt_bed);
        }

        // Set room setting to default
        $this->Room_setting->reset_default_if_empty($room_id);

        $select = 'a.id';
        $where = array('registration_destination_id' => $id, 'status' => 1);
        $treatment_room = $this->Treatment_room->select(0, $select, $where);

        if (count($treatment_room) > 0) {
            $treatment_room_id = $treatment_room[0]->id;

            $dt_treatment_room = array(
                'status' => 2
            );

            $this->Treatment_room->update_v2($treatment_room_id, $dt_treatment_room);
        }

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

    public function checkout_request_put($p = 0) {
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
        $this->form_validation->set_rules($this->checkout_request_rules);
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

        //check data regist
            $select = 'a.id, a.bed_id, a.checkin_date, r.norm, a.registration_id, p.birth_date, p.sex, h.name as alias_name, p.fullname as name, hfi.name as health_facility_item_name, ri.sep, ri.number_insurance';
            $where = array();
            $join = array(
                array('registration r', 'a.registration_id = r.id', 'INNER'),
                array('registration_insurance ri', 'a.id = ri.registration_destination_id AND ri.is_primary = "1"', 'INNER'),
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                array('patient p', 'r.norm = p.norm', 'INNER'),
                array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
            );
            $registration_destination = $this->$model->select($registration_destination_id, $select, $where, $join);

            if (count($registration_destination) == 0) {
                $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
                return;
            }
        //check data regist
        
        //check resep pulang
            $select_prs = 'a.id';
            $where_prs = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.type_prescription_id' => 2
            );
            $join_prs = array();
            $prescription_data = $this->Prescription->select(0, $select_prs, $where_prs, $join_prs);
        //check resep pulang

        // set data regist & resep pulang
            $prescription_id            = $prescription_data[0]->id;
            $bed_id                     = $registration_destination[0]->bed_id;
            $registration_id            = $registration_destination[0]->registration_id;
            $checkin_date               = $registration_destination[0]->checkin_date;
            $patient_sex                = $registration_destination[0]->sex;
            $patient_birth_date         = $registration_destination[0]->birth_date;
            $patient_alias              = $registration_destination[0]->alias_name;
            $patient_name               = $registration_destination[0]->name;
            $health_facility_item_name  = $registration_destination[0]->health_facility_item_name;
            $checkout_date              = $dt_put['checkout_date'];
            $norm                       = $registration_destination[0]->norm;
        // set data regist & resep pulang

        //check data bed
            if (!$bed_id) {
                $this->REST_Return(400, 'bed_id is required');
                return;
            }

            // check master bed & room
                $select = 'a.id, a.name, a.room_id';
                $bed = $this->Bed->select($bed_id, $select);

                $bed_name = $bed[0]->name;
                $room_id = $bed[0]->room_id;

                $select = 'a.id, a.costing_id, a.service_class_id';
                $room = $this->Room->select($room_id, $select);
            // check master bed & room

            // check costing
                $costing_id = $room[0]->costing_id;
                $service_class_id = $room[0]->service_class_id;

                $select = 'a.id, a.var_1';
                $where = array(
                    'costing_id' => $costing_id,
                    'service_class_id' => $service_class_id,
                );
                $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

                $price = $costing_services_class[0]->var_1;
            // check costing

            // // Calculate costs
            //     // Total hours
            //     $checkin   = $checkin_date;
            //     $checkout  = $checkout_date;
            //     $start     = new DateTime($checkin);
            //     $end       = new DateTime($checkout);

            //     $diff       = $end->diff($start);
            //     $totalHours =  $diff->h + ($diff->days * 24) + ($diff->m > 30 ? 1 : 0);
            //     // count prorate room
            //         $price               = $price / 24;
            //         $total_price         = $price * $totalHours;
            //     // count prorate room
            // // Calculate costs

            // Calculate costs
                $checkin   = $checkin_date;
                $checkout  = $checkout_date;
                // Hitung biaya rawat inap
                $start = new DateTime($checkin);
                $end   = new DateTime($checkout);

                // Hitung selisih waktu
                $diff = $end->diff($start);

                // Ambil selisih hari dasar
                $totalDays = $diff->days;

                // Jika ada sisa jam/menit, tambahkan 1 hari
                if ($diff->h > 0 || $diff->i > 0) {
                    $totalDays += 1;
                }

                // Jika total hari 0, minimal tetap 1 hari
                if ($totalDays == 0) {
                    $totalDays = 1;
                }

                // Hitung total harga
                $total_price = $price * $totalDays;
            // Calculate costs

            // check trx bed
                $select = 'a.id';
                $where = array(
                    'a.registration_destination_id' => $registration_destination_id
                );
                $transaction_bed = $this->Transaction_bed->select(0, $select, $where);

                $transaction_bed_id = $transaction_bed[0]->id;
            // check trx bed
        //check data bed
        $is_death = $dt_put['is_death'];
        //if not death
        if ($dt_put['is_death'] == '0' && $dt_put['is_control'] == '1') {
            $control_health_facility_item_id = $dt_put['control_health_facility_item_id'];
            
            // set data control init
                $control_health_facility_item_id = $dt_put['control_health_facility_item_id'];
                $control_doctor_id               = $dt_put['control_doctor_id'];
                $noSuratKontrol                  = $dt_put['noSuratKontrol'];
                $tglRencanaKontrol               = $dt_put['control_date'];
                $noKartu                         = $registration_destination[0]->number_insurance;
                $noSep                           = $registration_destination[0]->sep;
            // set data control init

            // check data
                $select_control_hfi = 'a.id, a.name, a.bride_bpjs, a.bride_bpjsspecialist';
                $dt_health_facility_item = $this->Health_facility_item->select($control_health_facility_item_id, $select_control_hfi);
                if (count($dt_health_facility_item) == 0) {
                    $this->REST_Return(404, 'there is no control_health_facility_item_id with id ' . $control_health_facility_item_id);
                    return;
                }

                $select_control_employee = 'a.id, a.bride_bpjs, a.fullname';
                $dt_employee = $this->Employee->select($control_doctor_id, $select_control_employee);
                if (count($dt_employee) == 0) {
                    $this->REST_Return(404, 'there is no control_doctor_id with id ' . $control_doctor_id);
                    return;
                }

                $select_user = 'e.fullname as user';
                $join_user = array(
                    array('employee e', "a.employee_id = e.id", "LEFT"),
                );
                $where_user = array();
                $dt_user = $this->User->select($user_id, $select_user, $where_user, $join_user);
                if (count($dt_user) == 0) {
                    $this->REST_Return(404, 'there is no user_id with id ' . $user_id);
                    return;
                }
            // check data

            $poliTujuan     = $dt_health_facility_item[0]->id == '8798e026-a5ad-4b98-8f2b-bfc5521e5144' ? $dt_health_facility_item[0]->bride_bpjsspecialist : $dt_health_facility_item[0]->bride_bpjs;
            $namepoliTujuan = $dt_health_facility_item[0]->name;
            $kodeDokter     = $dt_employee[0]->bride_bpjs;
            $namaDokter     = $dt_employee[0]->fullname;
            $nmuser         = $dt_user[0]->user;

            $dt_save_control = array(
                "no_control"                    => $noSuratKontrol,
                "noSep"                         => $noSep != null && $noSep != "" ? $noSep : null,
                "jnsPelayanan"                  => 2,
                "registration_destination_id"   => $registration_destination_id,
                "registration_id"               => $registration_id,
                "kodeDokter"                    => $kodeDokter,
                "poliTujuan"                    => $poliTujuan,
                "tglRencanaKontrol"             => $tglRencanaKontrol,
                "namaDokter"                    => $namaDokter,
                // "noKartu"                       => $noKartu,
                "nama"                          => $patient_name,
                "kelamin"                       => $patient_sex,
                "tglLahir"                      => $patient_birth_date,
                "user_added"                    => $nmuser,
                "date_added"                    => date('Y-m-d H:i:s'),
                "is_post_inpatient"             => 1,
                "is_bpjs_non_active"            => 0
            );

            //if bpjs
            if ($dt_put['is_bpjs'] == '1') {

                $this->load->library('Vclaim_sep','vclaim_sep');

                if ($noSuratKontrol == '' && $noSuratKontrol == null) {
                    $DataPostBpjs['request'] = array(
                        'noSEP'               => $noSep,
                        'kodeDokter'          => $kodeDokter,
                        'poliKontrol'         => $poliTujuan,
                        'tglRencanaKontrol'   => $tglRencanaKontrol,
                        'user'                => $nmuser
                    );
                    $databug = $DataPostBpjs;
                    $hasil = $this->vclaim_sep->request("/RencanaKontrol/insert", "POST", json_encode($DataPostBpjs));    
                } else {
                    $DataPostBpjs['request'] = array(
                        'noSuratKontrol'      => $noSuratKontrol,
                        'noSEP'               => $noSep,
                        'kodeDokter'          => $kodeDokter,
                        'poliKontrol'         => $poliTujuan,
                        'tglRencanaKontrol'   => $tglRencanaKontrol,
                        'user'                => $nmuser
                    );  
                    $databug = $DataPostBpjs;
                    $hasil = $this->vclaim_sep->request("/RencanaKontrol/Update", "PUT", json_encode($DataPostBpjs));
                }

                date_default_timezone_set('Asia/Jakarta');

                $d = json_decode($hasil, true);

                if ($d['metaData']['code'] == '200'){
                    $dt_save_control['no_control']  = $d['response']['noSuratKontrol'];
                    $dt_save_control['namaDokter']  = $d['response']['namaDokter'];
                    $dt_save_control['noKartu']     = $d['response']['noKartu'];
                    $dt_save_control['nama']        = $d['response']['nama'];
                    $dt_save_control['kelamin']     = $d['response']['kelamin'];
                    $dt_save_control['tglLahir']    = $d['response']['tglLahir'];
                } else {   
                    $res = json_decode($d, true);
                    // var_dump($res);die();
                    if($res['metaData']['message'] == 'Status peserta tidak aktif (NON AKTIF DIAKHIR BULAN)' || $res['metaData']['message'] == 'Status peserta tidak aktif (NON AKTIF KARNA PREMI)'){
                        $this->load->library("transaction");
                        $codecounter_book = array(
                            'var_1' => 'KI',
                            'var_2' => date('ymd'),
                            'var_3' => 'KONTROLRI',
                            'var_4' => 1,
                            'var_5' => date('d')
                        );
                        $checkcounter_book = $this->transaction->counter($codecounter_book);
                        $noSuratKontrol = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue'], 4, "0", STR_PAD_LEFT);
                        if ($checkcounter_book['counter_queue'] == '0') {
                            $this->Counter_queue->replace(array(
                                'var_1' => 'KI',
                                'var_2' => date('ymd'),
                                'var_3' => 'KONTROLRI',
                                'var_4' => 1,
                                'var_5' => date('d'),
                                'counter_queue' => 1
                            ));
                            $noSuratKontrol = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue']+1, 4, "0", STR_PAD_LEFT);
                        }
                        $dt_save_control['no_control']  = $noSuratKontrol;
                        $dt_save_control['is_bpjs_non_active']  = 1;
                    } else if ($res['metaData']['message'] == 'Gagal simpan! Sudah Diterbitkan Rencana Kunjungan Kontrol Di Tanggal Yang Sama.') {
                    } else if ($res['metaData']['message'] == 'Gagal simpan! Nomor SEP Sudah pernah digunakan!.') {
                        $hasilnew = $this->vclaim_sep->request("/RencanaKontrol/nosep/".$noSep, "GET");
                        $d = json_decode($hasilnew, true);
                        $dt_save_control['no_control']  = $d['response']['provPerujuk']['noRujukan'];
                        $dt_save_control['noKartu']     = $d['response']['sep']['noKartu'];
                        $dt_save_control['namaDokter']  = $namaDokter;
                        $dt_save_control['nama']        = $d['response']['nama'];
                        $dt_save_control['kelamin']     = $d['response']['kelamin'];
                        $dt_save_control['tglLahir']    = $d['response']['tglLahir'];
                    } else {
                        $this->REST_Return(404, 'error BPJS '.$res['metaData']['message'],$res);
                        return;
                    }
                }
            } else {
                if ($noSuratKontrol == '' && $noSuratKontrol == null) {
                    $this->load->library("transaction");
                    $codecounter_book = array(
                        'var_1' => 'KI',
                        'var_2' => date('ymd'),
                        'var_3' => 'KONTROLRI',
                        'var_4' => 1,
                        'var_5' => date('d')
                    );
                    $checkcounter_book = $this->transaction->counter($codecounter_book);
                    $noSuratKontrol = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue'], 4, "0", STR_PAD_LEFT);
                    if ($checkcounter_book['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => 'KI',
                            'var_2' => date('ymd'),
                            'var_3' => 'KONTROLRI',
                            'var_4' => 1,
                            'var_5' => date('d'),
                            'counter_queue' => 1
                        ));
                        $noSuratKontrol = $codecounter_book['var_1'].$codecounter_book['var_2'].str_pad($checkcounter_book['counter_queue']+1, 4, "0", STR_PAD_LEFT);
                    }
                    $dt_save_control['no_control']  = $noSuratKontrol;
                }
            }
            
        }
        //if not death


        /* Start Transaction */
            $this->db->trans_start();

            // set to insert control
            if ($dt_put['is_death'] == '0' && $dt_put['is_control'] == '1') {
                $this->Control_bpjs->replace($dt_save_control);
            }
            // set to insert control

            // set to update reg destionation
                $dt_registration_destination = array(
                    'checkout_date'     => $checkout_date,
                    'exit_id'           => $is_death == '1' ? '4a6d28c4-e020-425f-91e1-d39699cf679b' : null,
                    'nurse_id'          => $user_id
                );

                $this->$model->update_v2($registration_destination_id, $dt_registration_destination);
            // set to update reg destionation

            // set to update Transaction_bed
                $dt_transaction_bed = array(
                    'id'                            => $transaction_bed_id,
                    'registration_destination_id'   => $registration_destination_id,
                    'bed_id'                        => $bed_id,
                    'bed_name'                      => $bed_name,
                    'costing_id'                    => $costing_id,
                    'service_class_id'              => $service_class_id,
                    'var_1'                         => $price,
                    'var_2'                         => $totalDays,
                    'var_3'                         => $total_price,
                    'date_added'                    => date("Y-m-d H:i:s",time()),
                    'checkout_date'                 => $checkout_date,
                    'is_non_prorate'                => 1
                );
                $this->Transaction_bed->replace($dt_transaction_bed);
            // set to update Transaction_bed

            // NOTIFICATIONS
                // cb8fea0f-ce57-4596-b901-4671c8a2ef29 BILLING
                $health_facility_item_id = '160b1667-c1c6-4fdd-a1aa-1322141a2166';
                $select = 'a.id';
                $where = array(
                    'a.health_facility_item_id' => $health_facility_item_id,
                );
                $position = $this->Position->select(0, $select, $where);
                
                if (count($position) > 0) {
                    $position_id = $position[0]->id;

                    // PASIEN PULANG KASIR NOTIF
                    $title = 'Formulir Pasien Pulang';
                    $message = "Pasien $patient_alias $patient_name ($norm) Rencana Pulang dari $health_facility_item_name";
                    $this->Notifications->createNotification(null, $position_id, $title, $message, 'form_checkout_patient', ['registration_id' => $registration_id]);

                    if ($prescription_id) {
                        // PASIEN PULANG FARMASI NOTIF
                        $title = 'Obat Pulang Pasien';
                        $message = "Obat Pasien Pulang $patient_alias $patient_name ($norm) dari $health_facility_item_name";
                        $this->Notifications->createNotification(null, 'a641b0bd-e185-4d33-a07f-2506c06bc639', $title, $message, 'prescription_checkout_patient', ['prescription_id' => $prescription_id]);
                    }
                }
            // NOTIFICATIONS

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
        
        $select = 'a.id,a.registration_id,a.health_facility_item_id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);
        
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $registration_id = $registration_destination[0]->registration_id;
        $exit_id = ($dt_put['exit_id'] == '') ? null : $dt_put['exit_id'];
        $type_visit_id = ($dt_put['type_visit_id'] == '') ? null : $dt_put['type_visit_id'];
        $type_emergency_id = ($dt_put['type_emergency_id'] == '') ? 0 : $dt_put['type_emergency_id'];
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
    
        if ($type_visit_id != null && $registration_id != null) {
            $dt_registration = array(
                'type_visit_id'     => $type_visit_id,
            );
        
            $this->Registration->update($registration_id, $dt_registration);
        }
    
        $dt_registration_destination = array(
            'exit_id'           => $exit_id,
            'exit_reason'       => $exit_name,
            'type_emergency_id' => $type_emergency_id,
            'checkout_date'     => date('Y-m-d H:i:s',time()),
            'is_checkout'       => 0,
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

    public function checkout_doctor_put($p = 0) {
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
        
        $select = 'a.id,a.registration_id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);
        
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $registration_id = $registration_destination[0]->registration_id;
        $exit_id = ($dt_put['exit_id'] == '') ? null : $dt_put['exit_id'];
        $type_visit_id = ($dt_put['type_visit_id'] == '') ? null : $dt_put['type_visit_id'];
        $type_emergency_id = ($dt_put['type_emergency_id'] == '') ? 0 : $dt_put['type_emergency_id'];
        $date_control = ($dt_put['date_control'] == '') ? null : date('Y-m-d', strtotime($dt_put['date_control']));
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
    
        if ($type_visit_id != null && $registration_id != null) {
            $dt_registration = array(
                'type_visit_id'     => $type_visit_id,
            );
        
            $this->Registration->update($registration_id, $dt_registration);
        }
    
        $dt_registration_destination = array(
            'exit_id'           => $exit_id,
            'exit_reason'       => $exit_name,
            'type_emergency_id' => $type_emergency_id,
            'date_control'      => $date_control,
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
            $this->REST_Return(500, 'error while trying to checkout doctor', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function change_checkout_date_put($p = 0) {
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
        $this->form_validation->set_rules($this->change_checkout_date_rules);
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

        $select = 'a.id, a.bed_id, a.checkin_date';
        $registration_destination = $this->$model->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $id);
            return;
        }

        $bed_id = $registration_destination[0]->bed_id;
        $checkin_date = $registration_destination[0]->checkin_date;

        $checkout_date = $dt_put['checkout_date'];

        $select = 'a.id, a.room_id';
        $bed = $this->Bed->select($bed_id, $select);

        $room_id = $bed[0]->room_id;
        
        $select = 'a.id, a.costing_id, a.service_class_id';
        $room = $this->Room->select($room_id, $select);

        $costing_id = $room[0]->costing_id;
        $service_class_id = $room[0]->service_class_id;

        $select = 'a.id, a.var_1';
        $where = array(
            'costing_id' => $costing_id,
            'service_class_id' => $service_class_id
        );
        $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

        $price = $costing_services_class[0]->var_1;

        // Calculate costs
        // Total hours
        $checkin   = $checkin_date;
        $checkout  = $checkout_date;
        
        $start = new DateTime($checkin_date);
        $end   = new DateTime($checkout_date);

        // Hitung selisih waktu
        $diff = $end->diff($start);

        // Ambil selisih hari dasar
        $totalDays = $diff->days;

        // Jika ada sisa jam/menit, tambahkan 1 hari
        if ($diff->h > 0 || $diff->i > 0) {
            $totalDays += 1;
        }

        // Jika total hari 0, minimal tetap 1 hari
        if ($totalDays == 0) {
            $totalDays = 1;
        }

        // Hitung total harga
        $total_price = $price * $totalDays;

        $select = 'a.id';
        $where = array('registration_destination_id' => $registration_destination_id);
        $transaction_bed = $this->Transaction_bed->select(0, $select, $where);

        if (count($transaction_bed) == 0) {
            $this->REST_Return(404, 'there is no transaction_bed with registration_destination_id ' . $registration_destination_id);
            return;
        }

        $transaction_bed_id = $transaction_bed[0]->id;

        /* Start Transaction */
        $this->db->trans_start();

        // Transaction_bed
        $dt_transaction_bed = array(
            'id'                            => $transaction_bed_id,
            'registration_destination_id'   => $id,
            'var_1'                         => $price,
            'var_2'                         => $totalHours,
            'var_3'                         => $total_price,
            'checkout_date'                 => $checkout_date
        );
        $this->Transaction_bed->replace($dt_transaction_bed);
        
        $dt_registration_destination = array(
            'checkout_date' => $checkout_date    
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
            $this->REST_Return(500, 'error while trying to change checkout date registration_destination', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function icare_status_put($p = 0) {
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
        $this->form_validation->set_rules($this->icare_status_rules);
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
    
        $select = "a.id";
        $registration_destination = $this->$model->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'registration_destination with id ' . $registration_destination_id . ' does not exist!');
            return;
        }

        $icare_status = $dt_put['icare_status'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_registration_destination = array(
            'icare_status' => $icare_status,
        );
    
        $st_registration_destination = $this->$model->update_v2($registration_destination_id, $dt_registration_destination);
        if ($st_registration_destination == -1) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update registration_destination', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function upload_patient_signature_put($p = 0) {
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
        $this->form_validation->set_rules($this->upload_patient_signature_rules);
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

        $select = 'a.id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $filename = $registration_destination_id . '_' . date('Ymd');

        // Get raw POST input
        $rawInput = file_get_contents('php://input');

        // Decode JSON to array
        $request_data = json_decode($rawInput, true);

        // Extract and clean base64 (optional: remove data URI prefix if needed)
        $base64WithPrefix = $request_data['signature'] ?? '';
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $base64WithPrefix);

        $extension = explode('/', $request_data['extension'])[1];
        
        $url = $this->base64ToFile($base64, $filename . '.' . $extension, 'assets/document/registration/patient_signature/');

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'patient_signature' => $url,
        );

        $this->$model->update_v2($registration_destination_id, $dt_registration_destination);
       
        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to upload patient_signature', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    private function base64ToFile ($p = 0, $filename = '', $upload_dir = 'assets/document/registration/patient_signature/') {
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = $upload_dir . $file_name;
        $success      = file_put_contents($file, $data);
        return $file;
    }

    public function confirm_transfer_put($p = 0) {
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
        $this->form_validation->set_rules($this->confirm_transfer_rules);
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

        $select = 'a.id, a.registration_id, a.bed_id, a.health_facility_item_id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration with id ' . $registration_destination_id);
            return;
        }

        $registration_id = $registration_destination[0]->registration_id;
        $bed_id = $registration_destination[0]->bed_id;
        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $past_registration_destination = $this->db->query("
            SELECT id
            FROM registration_destination
            WHERE registration_id = '$registration_id'
            AND id != '$registration_destination_id'
        ")->result();

        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id
        );
        $treatment_room = $this->Treatment_room->select(0, $select, $where);

        $treatment_room_id = $treatment_room[0]->id;

        $arrival_time = $dt_put['arrival_time'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_treatment_room = array(
            'arrival_time' => $arrival_time,
        );

        $st_treatment_room = $this->Treatment_room->update_v2($treatment_room_id, $dt_treatment_room);
        if ($st_treatment_room < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        if (count($past_registration_destination) > 0) {
            $past_registration_destination_id = $past_registration_destination[0]->id;

            $dt_past_registration_destination = array(
                'is_checkout' => 1,
            );
    
            $st_past_registration_destination = $this->$model->update_v2($past_registration_destination_id, $dt_past_registration_destination);
            if ($st_past_registration_destination < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $dt_registration_destination = array(
            'is_transfer' => 1,
        );

        $st_registration_destination = $this->$model->update_v2($registration_destination_id, $dt_registration_destination);
        if ($st_registration_destination < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $dt_bed = array(
            'type_status_bed_id' => 2,
        );

        $st_bed = $this->Bed->update_v2($bed_id, $dt_bed);
        if ($st_bed < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // NOTIFICATIONS
        // cb8fea0f-ce57-4596-b901-4671c8a2ef29 RUANG JIWA
        // 7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c ICCU
        // ff1f252e-daab-4bdb-b308-e9f4cb11a47d ICU ISOLASI
        // 5515af72-6dcb-415d-89e4-2433e7b0149d NICU
        // e66e6ed4-4ef4-4375-a6d0-0445e3ec3039 PICU 
        // 05079cd9-07c6-4cf9-beb0-271eb301b7c1 PERINATOLOGI
        if ($health_facility_item_id == 'cb8fea0f-ce57-4596-b901-4671c8a2ef29') { // RUANG JIWA
            $position = array(
                (object) array(
                    'id' => 'c1002e5b-5f1f-4837-af45-122673052259' // G. ANAK
                ),
            );
        } elseif ($health_facility_item_id == '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c' || 
        $health_facility_item_id == 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d' ||
        $health_facility_item_id == '5515af72-6dcb-415d-89e4-2433e7b0149d' ||
        $health_facility_item_id == 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039' ||
        $health_facility_item_id == '05079cd9-07c6-4cf9-beb0-271eb301b7c1') {
            $position = array(
                (object) array(
                    'id' => '6c7d7e53-ff24-4246-85b2-07ea77ac801f' // ICU
                ),
            );
        } else {
            $select = 'a.id';
            $where = array(
                'a.health_facility_item_id' => $health_facility_item_id,
            );
            $position = $this->Position->select(0, $select, $where);
        }
        
        if (count($position) > 0) {
            $position_id = $position[0]->id;

            $this->Notifications->readNotificationPositionWithItem($position_id, 'transfer', $treatment_room_id, 'treatment_room_id');
        
            // GIZI
            // readNotificationPositionWithItem('8ca42f62-85bb-45f8-bf32-152a792e0f40', 'transfer', $treatment_room_id, 'treatment_room_id') {
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_past_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to confirm transfer', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function change_date_control_put($p = 0) {
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
        $this->form_validation->set_rules($this->change_date_control_rules);
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

        $select = 'a.id';
        $registration_destination = $this->$model->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $date_control = $dt_put['date_control'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'date_control' => $date_control,
        );

        $this->$model->update_v2($registration_destination_id, $dt_registration_destination);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_registration_destination,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update date control', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}