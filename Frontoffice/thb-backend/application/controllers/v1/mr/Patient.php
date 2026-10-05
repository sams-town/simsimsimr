<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Patient extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Appointment_model", "Appointment");
        $this->load->model("v1/mr/Document_model", "Document");
        $this->load->model("v1/mr/Insurance_model", "Insurance");
        $this->load->model("v1/mr/Patient_allergy_model", "Patient_allergy");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Responsible_person_model", "Responsible_person");
        $this->load->model("v1/mr/Registration_insurance_model", "Registration_insurance");
        $this->load->model("v1/mr/Prb_bpjs_model", "Prb_bpjs");
        $this->load->model("v1/mr/Refer_bpjs_model", "Refer_bpjs");
        $this->load->model("v1/mr/Control_bpjs_model", "Control_bpjs");
        $this->load->model("v1/md/Allergy_model", "Allergy");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/third_party/Company_model", "Company");
        $this->load->model("v1/Institute_branch_model", "Institute_branch");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        
        $this->create_rules = array(
            array('field' => 'idcard', 'label' => 'idcard', 'rules' => 'trim|required'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'fullname', 'label' => 'fullname', 'rules' => 'trim|required'),
            array('field' => 'birth_date', 'label' => 'birth_date', 'rules' => 'trim|required'),
            array('field' => 'sex', 'label' => 'sex', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'idcard', 'label' => 'idcard', 'rules' => 'trim|required'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'fullname', 'label' => 'fullname', 'rules' => 'trim|required'),
            array('field' => 'birth_date', 'label' => 'birth_date', 'rules' => 'trim|required'),
            array('field' => 'sex', 'label' => 'sex', 'rules' => 'trim|required'),
        );

        $this->merge_norm_rules = array(
            array('field' => 'old_norm', 'label' => 'old_norm', 'rules' => 'trim|required|max_length[12]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Patient API
     * --------------------
     * @param: institute_branch_id
     * @param: norm_mother
     * @param: idcard
     * @param: typeidcard
     * @param: alias
     * @param: f_title
     * @param: name
     * @param: b_title
     * @param: fullname
     * @param: birth_date
     * @param: birth_place
     * @param: address
     * @param: sex
     * @param: blood
     * @param: married_id
     * @param: profession_id
     * @param: citizenship_id
     * @param: religion_id
     * @param: last_edu_id
     * @param: phone
     * @param: email
     * @param: address_id
     * @param: zip_code
     * @param: patient_issue_id
     * @param: patient_issue_text
     * @param: type_allergy_id
     * @param: allergy_id
     * @param: name
     * @param: date_tragedy
     * @param: side_effect
     * @param: reaction
     * @param: severity
     * @param: responsible_id
     * @param: responsible_person_idcard
     * @param: responsible_person_typeidcard
     * @param: responsible_person_name
     * @param: responsible_person_phone
     * @param: responsible_person_address_id
     * @param: type_document_id
     * @param: url
     * @param: insurance_id
     * @param: company_id
     * @param: number_insurance
     * @param: expired
     * @param: service_class_id
     * @param: appointment_id
     * --------------------
     * @method : POST
     * @link: version/mr/patient
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
            // DATA PATIENT
            $this->form_validation->set_rules('appointment_id', 'appointment_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('norm_mother', 'norm_mother', 'trim|max_length[12]');
            $this->form_validation->set_rules('idcard', 'idcard', 'trim|max_length[20]');
            $this->form_validation->set_rules('typeidcard', 'typeidcard', 'trim|max_length[1]');
            $this->form_validation->set_rules('alias', 'alias', 'trim|max_length[3]');
            $this->form_validation->set_rules('f_title', 'f_title', 'trim|max_length[15]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('b_title', 'b_title', 'trim|max_length[15]');
            $this->form_validation->set_rules('fullname', 'fullname', 'trim|max_length[130]');
            $this->form_validation->set_rules('birth_date', 'birth_date', 'trim');
            $this->form_validation->set_rules('birth_place', 'birth_place', 'trim|max_length[50]');
            $this->form_validation->set_rules('address', 'address', 'trim');
            $this->form_validation->set_rules('sex', 'sex', 'trim|required|max_length[1]');
            $this->form_validation->set_rules('blood', 'blood', 'trim|max_length[2]');
            $this->form_validation->set_rules('married_id', 'married_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('profession_id', 'profession_id', 'trim|max_length[2]');
            $this->form_validation->set_rules('citizenship_id', 'citizenship_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('religion_id', 'religion_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('last_edu_id', 'last_edu_id', 'trim|max_length[2]');
            $this->form_validation->set_rules('phone', 'phone', 'trim|max_length[15]');
            $this->form_validation->set_rules('email', 'email', 'trim|max_length[100]');
            $this->form_validation->set_rules('address_id', 'address_id', 'trim|max_length[30]');
            $this->form_validation->set_rules('zip_code', 'zip_code', 'trim|max_length[6]');
            $this->form_validation->set_rules('patient_issue_id', 'patient_issue_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('patient_issue_text', 'patient_issue_text', 'trim|max_length[150]');
            $this->form_validation->set_rules('is_new', 'is_new', 'trim|max_length[1]');
            // DATA PATIENT_ALLERGY
            $this->form_validation->set_rules('allergies[]', 'allergies', 'trim');
            // $this->form_validation->set_rules('type_allergy_id', 'type_allergy_id', 'trim|max_length[1]');
            // $this->form_validation->set_rules('allergy_id', 'allergy_id', 'trim|max_length[36]');
            // $this->form_validation->set_rules('allergy_name', 'allergy_name', 'trim|max_length[255]');
            // $this->form_validation->set_rules('date_tragedy', 'date_tragedy', 'trim');
            // $this->form_validation->set_rules('side_effect', 'side_effect', 'trim|max_length[100]');
            // $this->form_validation->set_rules('reaction', 'reaction', 'trim|max_length[100]');
            // $this->form_validation->set_rules('severity', 'severity', 'trim|max_length[50]');
            // DATA RESPONSIBLE_PERSON
            $this->form_validation->set_rules('responsible_id', 'responsible_id', 'trim|max_length[2]');
            $this->form_validation->set_rules('responsible_person_idcard', 'responsible_person_idcard', 'trim|max_length[20]');
            $this->form_validation->set_rules('responsible_person_typeidcard', 'responsible_person_typeidcard', 'trim|max_length[1]');
            $this->form_validation->set_rules('responsible_person_name', 'responsible_person_name', 'trim|max_length[255]');
            $this->form_validation->set_rules('responsible_person_phone', 'responsible_person_phone', 'trim|max_length[15]');
            $this->form_validation->set_rules('responsible_person_address_id', 'responsible_person_address_id', 'trim');
            // DATA DOCUMENT
            $this->form_validation->set_rules('type_document_id', 'type_document_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('url', 'url', 'trim');
            // DATA INSURANCE
            $this->form_validation->set_rules('insurances[]', 'insurances', 'trim');
            // $this->form_validation->set_rules('insurance_id', 'insurance_id', 'trim|max_length[1]');
            // $this->form_validation->set_rules('company_id', 'company_id', 'trim|max_length[36]');
            // $this->form_validation->set_rules('number_insurance', 'number_insurance', 'trim|max_length[20]');
            // $this->form_validation->set_rules('expired', 'expired', 'trim');
            // $this->form_validation->set_rules('service_class_id', 'service_class_id', 'trim|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();

                    // INIT_PATIENT
                    // Count norm
                    $this->$model->limit = 1;
                    $this->$model->orderFields = array('a.norm' => 'DESC');
                    $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
                    $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
                    $this->$model->whereFields['g.institute_branch_id'] = $institute_branch_id;
                    $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                    $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                    $tmp = $this->$model->read(0,false);
                    // var_dump($tmp[0]->norm);
                    if($tmp != []) {
                        $norm = str_pad(($tmp[0]->norm) + 1, 6, "0", STR_PAD_LEFT);  
                        // $norm = str_pad(intval(substr($tmp[0]->norm,2)) + 1, 6, '0', STR_PAD_LEFT);
                    } else {
                        $norm = '000001';
                    }
                    // var_dump($norm);
                    // die();
                    $dt_patient = array(
                        'norm'                  => $norm,
                        'institute_branch_id'   => $institute_branch_id,
                        'idcard'                => isset($dt_post['idcard']) ? $dt_post['idcard'] : '',
                        'typeidcard'            => isset($dt_post['typeidcard']) ? $dt_post['typeidcard'] : '',
                        'alias'                 => isset($dt_post['alias']) ? $dt_post['alias'] : '',
                        'f_title'               => isset($dt_post['f_title']) ? $dt_post['f_title'] : '',
                        'name'                  => $dt_post['name'],
                        'b_title'               => isset($dt_post['b_title']) ? $dt_post['b_title'] : '',
                        'fullname'              => isset($dt_post['fullname']) ? $dt_post['fullname'] : '',
                        'birth_date'            => isset($dt_post['birth_date']) ? $dt_post['birth_date'] : '',
                        'birth_place'           => isset($dt_post['birth_place']) ? $dt_post['birth_place'] : '',
                        'address'               => isset($dt_post['address']) ? $dt_post['address'] : '',
                        'sex'                   => $dt_post['sex'],
                        'blood'                 => isset($dt_post['blood']) ? $dt_post['blood'] : '',
                        'married_id'            => isset($dt_post['married_id']) ? $dt_post['married_id'] : '',
                        'profession_id'         => isset($dt_post['profession_id']) ? $dt_post['profession_id'] : '',
                        'citizenship_id'        => isset($dt_post['citizenship_id']) ? $dt_post['citizenship_id'] : '',
                        'religion_id'           => isset($dt_post['religion_id']) ? $dt_post['religion_id'] : '',
                        'last_edu_id'           => isset($dt_post['last_edu_id']) ? $dt_post['last_edu_id'] : '',
                        'phone'                 => isset($dt_post['phone']) ? $dt_post['phone'] : '',
                        'email'                 => isset($dt_post['email']) ? $dt_post['email'] : '',
                        'address_id'            => isset($dt_post['address_id']) ? $dt_post['address_id'] : '',
                        'zip_code'              => isset($dt_post['zip_code']) ? $dt_post['zip_code'] : '',
                        'patient_issue_id'      => isset($dt_post['patient_issue_id']) ? $dt_post['patient_issue_id'] : '',
                        'patient_issue_text'    => isset($dt_post['patient_issue_text']) ? $dt_post['patient_issue_text'] : '',
                        'user_added'            => $user_id,
                        'date_added'            => date("Y-m-d H:i:s",time()),
                        'date_updated'          => date("Y-m-d H:i:s",time()),
                        'type_patient_id'       => '1',
                        'is_new'                => 'Y',
                    );

                    // insert norm in appointment
                    if($dt_post['appointment_id'] != '') {
                        // Check appointment_id
                        if($this->Appointment->read($dt_post['appointment_id'],false) != []) {
                            $dt_appointment = array (
                                'id'    => $dt_post['appointment_id'],
                                'norm'  => $norm,
                            );
                            $this->Appointment->replace($dt_appointment);
                        }
                    }

                    // is norm_mother?
                    if(isset($dt_post['norm_mother'])) {
                        // Check norm_mother
                        if($this->$model->read($dt_post['norm_mother'],false) != []) {
                            $dt_patient['norm_mother'] = $dt_post['norm_mother'];
                        }
                    }
                    $id = $this->$model->replace($dt_patient);

                    // INIT_PATIENT_ALLERGY
                    if(isset($dt_post['allergies'])) {
                        for ($i=0; $i < count($dt_post['allergies']); $i++) {
                            $this->Allergy->whereFields['name'] = $dt_post['allergies'][$i]['name'];
                            $allergy = $this->Allergy->read(0, false);
                            if($allergy != []) {
                                $dt_patient_allergy = array(
                                    'norm'              => $norm,
                                    'type_allergy_id'   => isset($dt_post['allergies'][$i]['type_allergy_id']) ? $dt_post['allergies'][$i]['type_allergy_id'] : '',
                                    'allergy_id'        => $allergy[0]->id,
                                    'name'              => $dt_post['allergies'][$i]['name'],
                                    'date_tragedy'      => isset($dt_post['allergies'][$i]['date_tragedy']) ? $dt_post['allergies'][$i]['date_tragedy'] : '',
                                    'side_effect'       => isset($dt_post['allergies'][$i]['side_effect']) ? $dt_post['allergies'][$i]['side_effect'] : '',
                                    'reaction'          => isset($dt_post['allergies'][$i]['reaction']) ? $dt_post['allergies'][$i]['reaction'] : '',
                                    'severity'          => isset($dt_post['allergies'][$i]['severity']) ? $dt_post['allergies'][$i]['severity'] : ''
                                );
                                $this->Patient_allergy->replace($dt_patient_allergy);
                            } else {
                                $dt_allergy = array(
                                    'institute_branch_id'   => $institute_branch_id,
                                    'name'                  => $dt_post['allergies'][$i]['name'],
                                );
                                $allergy_id = $this->Allergy->create($dt_allergy);

                                $dt_patient_allergy = array(
                                    'norm'              => $norm,
                                    'type_allergy_id'   => isset($dt_post['allergies'][$i]['type_allergy_id']) ? $dt_post['allergies'][$i]['type_allergy_id'] : '',
                                    'allergy_id'        => $allergy_id,
                                    'name'              => $dt_post['allergies'][$i]['name'],
                                    'date_tragedy'      => isset($dt_post['allergies'][$i]['date_tragedy']) ? $dt_post['allergies'][$i]['date_tragedy'] : '',
                                    'side_effect'       => isset($dt_post['allergies'][$i]['side_effect']) ? $dt_post['allergies'][$i]['side_effect'] : '',
                                    'reaction'          => isset($dt_post['allergies'][$i]['reaction']) ? $dt_post['allergies'][$i]['reaction'] : '',
                                    'severity'          => isset($dt_post['allergies'][$i]['severity']) ? $dt_post['allergies'][$i]['severity'] : ''
                                );
                                $this->Patient_allergy->replace($dt_patient_allergy);
                            }
                        }
                    }

                    // INIT_RESPONSIBLE_PERSON
                    if(isset($dt_post['responsible_id'])) {
                        $dt_responsible_person = array(
                            'norm'                  => $norm,
                            'responsible_id'        => isset($dt_post['responsible_id']) ? $dt_post['responsible_id'] : '',
                            'idcard'                => isset($dt_post['responsible_person_idcard']) ? $dt_post['responsible_person_idcard'] : '',
                            'typeidcard'            => isset($dt_post['responsible_person_typeidcard']) ? $dt_post['responsible_person_typeidcard'] : '',
                            'name'                  => isset($dt_post['responsible_person_name']) ? $dt_post['responsible_person_name'] : '',
                            'phone'                 => isset($dt_post['responsible_person_phone']) ? $dt_post['responsible_person_phone'] : '',
                            'address_id'            => isset($dt_post['responsible_person_address_id']) ? $dt_post['responsible_person_address_id'] : '',
                        );
                        $this->Responsible_person->replace($dt_responsible_person);
                    }

                    // INIT_DOCUMENT
                    if(isset($dt_post['type_document_id'])) {
                        $dt_document = array(
                            'norm'                  => $norm,
                            'type_document_id'      => isset($dt_post['type_document_id']) ? $dt_post['type_document_id'] : '',
                            'url'                   => isset($dt_post['url']) ? $dt_post['url'] : '',
                        );
                        $this->Document->replace($dt_document);
                    }

                    // INIT_INSURANCES
                    if(isset($dt_post['insurances'])) {
                        for ($i=0; $i < count($dt_post['insurances']); $i++) { 
                            if($dt_post['insurances'][$i]['insurance_id'] == 1) {
                                $dt_insurance = array(
                                    'norm'                  => $norm,
                                    'institute_branch_id'   => $institute_branch_id,
                                    'insurance_id'          => isset($dt_post['insurances'][$i]['insurance_id']) ? $dt_post['insurances'][$i]['insurance_id'] : '',
                                    'number_insurance'      => isset($dt_post['insurances'][$i]['number_insurance']) ? $dt_post['insurances'][$i]['number_insurance'] : '',
                                );
                                $this->Insurance->replace($dt_insurance);
                            } else {
                                if(isset($dt_post['insurances'][$i]['company_id']) && isset($dt_post['insurances'][$i]['company_id'])) {
                                    if($this->Company->read($dt_post['insurances'][$i]['company_id'], false) != [] && $this->Service_class->read($dt_post['insurances'][$i]['service_class_id'], false) != []) {
                                        $dt_insurance = array(
                                            'norm'                  => $norm,
                                            'insurance_id'          => isset($dt_post['insurances'][$i]['insurance_id']) ? $dt_post['insurances'][$i]['insurance_id'] : '',
                                            'company_id'            => $dt_post['insurances'][$i]['company_id'],
                                            'number_insurance'      => isset($dt_post['insurances'][$i]['number_insurance']) ? $dt_post['insurances'][$i]['number_insurance'] : '',
                                            'expired'               => isset($dt_post['insurances'][$i]['expired']) ? $dt_post['insurances'][$i]['expired'] : '',
                                            'service_class_id'      => $dt_post['insurances'][$i]['service_class_id'],
                                        );
                                        // echo "<pre>";
                                        // var_dump($dt_post['insurances']);
                                        // echo "</pre>";
                                        // die();
                                        $this->Insurance->replace($dt_insurance);
                                    } else {
                                        $this->REST_Return(404, 'Wrong company_id ' . $dt_post['insurances'][$i]['company_id'] .  ' or service_class_id ' . $dt_post['insurances'][$i]['service_class_id']);
                                        return;
                                    }
                                } else {
                                    $this->REST_Return(400, 'company_id & service_class_id is required for insurance_id type 2');
                                    return;
                                }
                            }
                        }
                    }
                    // die();
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'norm'     => $norm
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
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
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        // count norm
        $select = 'a.norm';
        $where = array();
        $join = array();
        $order = array('a.norm' => 'DESC');
        $this->$model->limit = 1;
        $patient_last = $this->$model->select(0, $select, $where, $join, $order);

        $norm = '000001';

        if(count($patient_last) > 0) {
            $patient_last_norm = $patient_last[0]->norm;

            $norm = str_pad(($patient_last_norm) + 1, 6, '0', STR_PAD_LEFT);  
        }

        $idcard = $dt_post['idcard'];
        $idcardparent = $dt_post['idcardparent'];
        $typeidcard = $dt_post['typeidcard'];
        $alias = $dt_post['alias'];
        $f_title = $dt_post['f_title'];
        $name = $dt_post['name'];
        $b_title = $dt_post['b_title'];
        $fullname = $dt_post['fullname'];
        $birth_date = $dt_post['birth_date'];
        $birth_place = $dt_post['birth_place'];
        $address = $dt_post['address'];
        $sex = $dt_post['sex'];
        $blood = $dt_post['blood'];
        $married_id = $dt_post['married_id'];
        $profession_id = $dt_post['profession_id'];
        $citizenship_id = $dt_post['citizenship_id'];
        $religion_id = $dt_post['religion_id'];
        $last_edu_id = $dt_post['last_edu_id'];
        $phone = $dt_post['phone'];
        $email = $dt_post['email'];
        $address_id = $dt_post['address_id'];
        $zip_code = $dt_post['zip_code'];
        $patient_issue_id = $dt_post['patient_issue_id'];
        $patient_issue_text = $dt_post['patient_issue_text'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_patient = array(
            'norm'                  => $norm,
            'institute_branch_id'   => $institute_branch_id,
            'idcard'                => $idcard,
            'idcardparent'          => $idcardparent,
            'typeidcard'            => $typeidcard,
            'alias'                 => $alias,
            'f_title'               => $f_title,
            'name'                  => $name,
            'b_title'               => $b_title,
            'fullname'              => $fullname,
            'birth_date'            => $birth_date,
            'birth_place'           => $birth_place,
            'address'               => $address,
            'sex'                   => $sex,
            'blood'                 => $blood,
            'married_id'            => $married_id,
            'profession_id'         => $profession_id,
            'citizenship_id'        => $citizenship_id,
            'religion_id'           => $religion_id,
            'last_edu_id'           => $last_edu_id,
            'phone'                 => $phone,
            'email'                 => $email,
            'address_id'            => $address_id,
            'zip_code'              => $zip_code,
            'patient_issue_id'      => $patient_issue_id,
            'patient_issue_text'    => $patient_issue_text,
            'user_added'            => $user_id,
            'date_added'            => date("Y-m-d H:i:s",time()),
            'date_updated'          => date("Y-m-d H:i:s",time()),
            'type_patient_id'       => 1,
            'is_new'                => 'Y',
        );

        $appointment_id = $dt_post['appointment_id'];

        // insert norm in appointment
        if($appointment_id != null) {
            $select = 'a.id';
            $appointment = $this->Appointment->select($appointment_id, $select);

            if (count($appointment) == 0) {
                $this->REST_Return(404, 'there is no appointment with id ' . $appointment_id);
                return;
            }

            $dt_appointment = array (
                'id'    => $appointment_id,
                'norm'  => $norm,
            );
            $this->Appointment->replace($dt_appointment);
        }

        // norm_mother
        $norm_mother = $dt_post['norm_mother'];

        if($norm_mother != null) {
            $select = 'a.norm';
            $patient_mother = $this->$model->select($norm_mother, $select);

            if (count($patient_mother) == 0) {
                $this->REST_Return(404, 'there is no norm mother ' . $norm_mother);
                return;
            }
            
            $dt_patient['norm_mother'] = $norm_mother;
        }

        $id = $this->$model->replace($dt_patient);

        // init patient_allergy
        $allergies_arr = $dt_post['allergies'];

        for ($i=0; $i < count($allergies_arr); $i++) {
            $name = $allergies_arr[$i]['name'];
            $type_allergy_id = $allergies_arr[$i]['type_allergy_id'];
            $date_tragedy = $allergies_arr[$i]['date_tragedy'];
            $side_effect = $allergies_arr[$i]['side_effect'];
            $reaction = $allergies_arr[$i]['reaction'];
            $severity = $allergies_arr[$i]['severity'];

            $dt_patient_allergy = array(
                'norm'              => $norm,
                'type_allergy_id'   => $type_allergy_id,
                'name'              => $name,
                'date_tragedy'      => $date_tragedy,
                'side_effect'       => $side_effect,
                'reaction'          => $reaction,
                'severity'          => $severity,
            );

            $select = 'a.id';
            $where = array(
                'a.name' => $name
            );
            $allergy = $this->Allergy->select(0, $select, $where);

            if (count($allergy) == 0) {
                $dt_allergy = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'name'                  => $allergies_arr[$i]['name'],
                );
                $allergy_id = $this->Allergy->create($dt_allergy);
            } else {
                $allergy_id = $allergy[0]->id;
            }

            $dt_patient_allergy['allergy_id'] = $allergy_id;

            $this->Patient_allergy->replace($dt_patient_allergy);
        }

        // init responsible_person
        $responsible_id = $dt_post['responsible_id'];

        if($responsible_id != null) {
            $responsible_id = $dt_post['responsible_id'];
            $responsible_idcard = $dt_post['responsible_person_idcard'];
            $responsible_person_profession_id = $dt_post['responsible_person_profession_id'];
            $responsible_typeidcard = $dt_post['responsible_person_typeidcard'];
            $responsible_name = $dt_post['responsible_person_name'];
            $responsible_phone = $dt_post['responsible_person_phone'];
            $responsible_address_id = $dt_post['responsible_person_address_id'];

            $dt_responsible_person = array(
                'norm'                  => $norm,
                'responsible_id'        => $responsible_id,
                'idcard'                => $responsible_idcard,
                'profession_id'         => $responsible_person_profession_id,
                'typeidcard'            => $responsible_typeidcard,
                'name'                  => $responsible_name,
                'phone'                 => $responsible_phone,
                'address_id'            => $responsible_address_id,
            );

            $this->Responsible_person->replace($dt_responsible_person);
        }

        // init document
        $type_document_id = $dt_post['type_document_id'];

        if($type_document_id != null) {
            $url = $dt_post['url'];

            $dt_document = array(
                'norm'                  => $norm,
                'type_document_id'      => $type_document_id,
                'url'                   => $url,
            );

            $this->Document->replace($dt_document);
        }

        // init insurances
        $insurances_arr = $dt_post['insurances'];

        for ($i=0; $i < count($insurances_arr); $i++) { 
            $insurance_id = $insurances_arr[$i]['insurance_id'];
            $number_insurance = $insurances_arr[$i]['number_insurance'];
            $is_primary = $insurances_arr[$i]['is_primary'];

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
                $dt_insurance = array(
                    'norm'                          => $norm,
                    'institute_branch_id'           => $institute_branch_id,
                    'insurance_id'                  => $insurance_id,
                    'number_insurance'              => $number_insurance,
                    'is_primary'                    => $is_primary,
                );

                $this->Insurance->replace($dt_insurance);
            } else {
                $company_id = $insurances_arr[$i]['company_id'];
                $service_class_id = $insurances_arr[$i]['service_class_id'];
                $expired = $insurances_arr[$i]['expired'];

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
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_patient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create patient', $response_data);
            return;
        }
    
        $response_data = array(
            'norm' => $norm
        );
    
        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }

    public function check_duplicate_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $name = $q_strings['name'];
        $birth_date = $q_strings['birth_date'];
        $birth_place = $q_strings['birth_place'];
        $idcard = $q_strings['idcard'];

        if (isset($idcard)) {
            $select = 'a.norm';
            $where = array(
                'idcard' => $idcard 
            );
            $join = array();
            $patient_idcard = $this->Patient->select(0, $select, $where, $join);
    
            if (count($patient_idcard) > 0) {
                $norm = $patient_idcard[0]->norm;
                $this->REST_Return(409, 'there is already patient with idcard ' . $idcard . ', norm ' . $norm);
                return;
            }
        }

        if (isset($name) && isset($birth_date) && isset($birth_place)) {
            $select = 'a.norm';
            $where = array(
                'a.name'          => $name,
                'a.birth_date'    => $birth_date,
                'a.birth_place'   => $birth_place,
            );
            $join = array();
            $patient_name = $this->Patient->select(0, $select, $where, $join);

            if (count($patient_name) > 0) {
                $norm = $patient_name[0]->norm;
                $this->REST_Return(409, 'there is already patient with name ' . $name . ' and birth date ' . $birth_date . ' ' . $birth_place . ', norm ' . $norm);
                return;
            }
        }

        $response_data = 'PASSED';

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }


    private function tomiliseconds($tanggal) {
        // date_default_timezone_set('Asia/Jakarta');
        return strtotime($tanggal) * 1000;
    }

    public function create_sep_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];

                    $noKartu = $dt_post['noKartu'];
                    $tglSep = date("Y-m-d", strtotime($dt_post['tglSep']));
                    $ppkPelayanan = '0070R032';
                    $jnsPelayanan = $dt_post['jnsPelayanan'];
                    if (isset($dt_post['klsRawat'])) { $klsRawat = $dt_post['klsRawat']; }else{$klsRawat = '';}
                    $noMR = $dt_post['noMr'];
                    if (isset($dt_post['asalRujukan'])) { $asalRujukan = $dt_post['asalRujukan']; }else{$asalRujukan = '';}
                    if (isset($dt_post['tglRujukan'])) { $tglRujukan = $dt_post['tglRujukan']; }else{$tglRujukan = '';}
                    if (isset($dt_post['noRujukan'])) { $noRujukan = $dt_post['noRujukan']; }else{$noRujukan = '';}
                    if (isset($dt_post['ppkRujukan'])) { $ppkRujukan = $dt_post['ppkRujukan']; }else{$ppkRujukan = '';}
                    $catatan = $dt_post['catatan'];
                    $diagAwal = $dt_post['diagAwal'];
                    // $this->load->model("v1/mr/Icd10_model", "Icd10");
                    // $diagAwal = $this->Icd10->read($diagAwal, false)[0]->code;
                    if (isset($dt_post['tujuan'])) { $tujuan = $dt_post['tujuan']; }else{$tujuan = '';}
                    if (isset($dt_post['eksekutif'])) { $eksekutif = $dt_post['eksekutif'] == '' ? 0 : $dt_post['eksekutif']; }else{$eksekutif = 0;}
                    if (isset($dt_post['cob'])) { $cob = $dt_post['cob'] == '' ? 0 : $dt_post['cob']; }else{$cob = 0;}
                    if (isset($dt_post['katarak'])) { $katarak = $dt_post['katarak']; }else{$katarak = 0;}
                    if (isset($dt_post['kontrol'])) { $kontrol = $dt_post['kontrol']; }else{$kontrol = 0;}
                    if (isset($dt_post['lakaLantas'])) { $lakaLantas = $dt_post['lakaLantas'] == '' ? 0 : $dt_post['lakaLantas']; }else{$lakaLantas = 0;}
                    if (isset($dt_post['penjamin'])) { $penjamin = implode(',', $dt_post['penjamin']); }else{$penjamin = '';}
                    $tglKejadian = $dt_post['tglKejadian'];
                    $suplesi = $dt_post['suplesi'];
                    $noSepSuplesi = $dt_post['noSepSuplesi'];
                    $kdPropinsi = $dt_post['kodepropinsi'];
                    $kdKabupaten = $dt_post['kodekabupaten'];
                    $kdKecamatan = $dt_post['kodekecamatan'];
                    $keteranganKll = $dt_post['keteranganKll'];
                    if (isset($dt_post['tujuanKunj'])) { $tujuanKunj = $dt_post['tujuanKunj']; }else{$tujuanKunj = 0;}
                    if (isset($dt_post['flagProcedure'])) { $flagProcedure = $dt_post['flagProcedure']; }else{$flagProcedure = '';}
                    if (isset($dt_post['kdPenunjang'])) { $kdPenunjang = $dt_post['kdPenunjang']; }else{$kdPenunjang = '';}
                    if (isset($dt_post['assesmentPel'])) { $assesmentPel = $dt_post['assesmentPel']; }else{$assesmentPel = '';}
                    if (isset($dt_post['klsRawatNaik'])) { $klsRawatNaik = $dt_post['klsRawatNaik']; }else{$klsRawatNaik = '';}
                    if (isset($dt_post['pembiayaan'])) { $pembiayaan = $dt_post['pembiayaan']; }else{$pembiayaan = '';}
                    if (isset($dt_post['penanggungJawab'])) { $penanggungJawab = $dt_post['penanggungJawab']; }else{$penanggungJawab = '';}
                    // if (isset($dt_post['dpjpLayan'])) { $dpjpLayan = $dt_post['dpjpLayan']; }else{$dpjpLayan = '';}
                    if (isset($dt_post['doctor_in_charge_id'])) { $kodeDPJP = $dt_post['doctor_in_charge_id']; $dpjpLayan = $dt_post['doctor_in_charge_id']; }else{$kodeDPJP = '';}
                    if ($dt_post['poli_destination'] == 'RAWAT INAP') {
                        $kodeDPJP = $dt_post['kodeDPJP'];
                        $dpjpLayan = "";
                    } else {
                        $kodeDPJP = $this->Employee->read($kodeDPJP,false)[0]->bride_bpjs;
                        $dpjpLayan = $this->Employee->read($kodeDPJP,false)[0]->bride_bpjs;
                    }
                    if (isset($dt_post['noSurat'])) { $noSurat = $dt_post['noSurat']; }else{$noSurat = '';}
                    if (isset($dt_post['noLP'])) { $noLP = $dt_post['noLP']; }else{$noLP = '';}
                    $noTelp = $dt_post['noTelp'];
                    $user = $dt_post['user'];
                    // $codelayan = $this->vclaim_sep->request("/referensi/dokter/pelayanan/2"."/tglPelayanan/".date('Y-m-d').'/Spesialis/'.$tujuan, "GET");
                    // $response_codelayan = json_decode($codelayan);
                    // var_dump($response_codelayan->response->list);
                    // for ($rscr=0; $rscr < count($response_codelayan->response->list); $rscr++) {
                    //     if ($response_codelayan->response->list[$rscr]->kode == $kodeDPJP) {
                    //         $dpjpLayan = $response_codelayan->response->list[$rscr]->kode;
                    //     }
                    // }
                    // die();
                    $DataPost = array();
                    if($klsRawat == '1'){
                        $hakkelas = '3';
                    } else if ($klsRawat == '2') {
                        $hakkelas = '4';
                    } else if ($klsRawat == '3') {
                        $hakkelas = '5';
                    }
                    $DataPost['request']['t_sep'] = array(
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
                        'asalRujukan'       => $dt_post['poli_destination'] == 'RAWAT INAP' ? '2' : $asalRujukan,
                        'tglRujukan'        => $tglRujukan,
                        'noRujukan'         => $dt_post['poli_destination'] == 'RAWAT INAP' ? $noSurat : $noRujukan,
                        'ppkRujukan'        => $ppkRujukan
                        ),
                        'catatan'             => $catatan,
                        'diagAwal'            => $diagAwal,
                        'poli'                => 
                        array(
                        'tujuan'            => $dt_post['poli_destination'] == 'RAWAT INAP' ? "" : $dataRegistration->bride_bpjs,
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
                        'dpjpLayan'           => $dt_post['poli_destination'] == 'RAWAT INAP' ? "" : $kodeDPJP,
                        'noTelp'              => $noTelp,
                        'user'                => $user,
                    );
                    // // var_dump($DataPost);
                    // echo json_encode($DataPost);
                    // // echo "<br>";
                    // die();    
                    $hasil = $this->vclaim_sep->request("/SEP/2.0/insert", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    // var_dump($d);
                    // var_dump($d['metaData']['code']);
                    // $dataEklaim['metadata']['method'] = "new_claim";
                    // $dataEklaim['data']['nomor_kartu'] = $noKartu;
                    // $dataEklaim['data']['nomor_sep'] = $d['response']['sep']['noSep'];
                    // $dataEklaim['data']['nomor_rm'] = $noMR;
                    // $dataEklaim['data']['nama_pasien'] = $dt_post['nama'];
                    // $dataEklaim['data']['tgl_lahir'] = $dt_post['tgl_lahir'];
                    // $dataEklaim['data']['gender'] = ($dt_post['kelamin'] == 1) ? "1" : "2";
                    // $hasilEklaim = $this->eklaim->claim(json_encode($dataEklaim));
                    $hasilEklaim = '';
                    // $DataGET = array(['noSEP' => $d['response']['sep']['noSep'],
                    //                     'diagnosa' => $d['response']['sep']['diagnosa'],
                    //                 'jnsPelayananNama' => $d['response']['sep']['jnsPelayanan'],
                    //                 'kelasRawat' => $d['response']['sep']['kelasRawat'],
                    //                 'asuransi' => $d['response']['sep']['peserta']['asuransi'],
                    //                 'hakKelas' => $d['response']['sep']['peserta']['hakKelas'],
                    //                 'jnsPeserta' => $d['response']['sep']['peserta']['jnsPeserta'],
                    //                 'jnsPelayanan' => $d['response']['sep']['peserta']['jnsPeserta'],
                    //                 'kelamin' => $d['response']['sep']['peserta']['kelamin'],
                    //                 'code' => $d['metaData']['code'],
                    //                 'message' => $d['metaData']['message'],
                    //                 'eklaim' => $hasilEklaim]);
                    if ($d['metaData']['code'] == "200"){
                        $this->db->where('registration_destination_id', $dt_post['registration_destination_id']);
                        $this->db->where('insurance_id',2);
                        $this->db->where('is_primary',1);
                        $this->db->update('registration_insurance', array('sep'=>$d['response']['sep']['noSep'],'sep_json'=>json_encode($d)));
                        if ($dt_post['poli_destination'] !== 'IGD' && $dt_post['poli_destination'] !== 'RAWAT INAP') {
                            //validasi task id jika hapus sep
                            // $update_antrean_3 = array(
                            //     'kodebooking'       => $dataRegistration->code_booking,
                            //     'taskid'            => 3,
                            //     'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s"))
                            // );
                            // $this->load->library('Vclaim_antrol','vclaim_antrol');
                            // $updatewaktu_antrean_3 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_3));
                            // $json_update_antrean_3 = json_decode($updatewaktu_antrean_3, true);
                            // $json_update_antrean_3 = json_decode($json_update_antrean_3);
                            // // jika gagal update antrean bpjs
                            // // tambahin jika delete sep dan create sep ulang tidak update tassk id 3 lagi
                    
                            // // var_dump($json_update_antrean_3);
                            // if($json_update_antrean_3->metadata->code != 200) {
                            //     // rollback
                            //     $this->db->trans_rollback();
                            //     $this->REST_Return(422, $json_update_antrean_3->metadata->message, $json_update_antrean_3->metadata->message);
                            //     return;
                            // } else {
                            //     $this->db->where('id', $dt_post['registration_destination_id']);
                            //     $this->db->where('registration_id',$dataRegistration->registration_id);
                            //     $this->db->where('health_facility_item_id',$dataRegistration->health_facility_item_id);
                            //     $this->db->update('registration_destination', array('date_task_3'=>date("Y-m-d H:i:s")));
                            // }
                        }
                    } else {
                        // var_dump(array('registration_destination_id'=>$dt_post['registration_destination_id'],'sep'=>$d->metaData->code));
                        $d = json_decode($d, true);
                        $response_data = $d;
                        $this->REST_Return(200, 'SUCCESS', $response_data);
                        return;
                    }
                    
                    // die();
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'sep'     => $d['response']['sep']['noSep']
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_sprik_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    if ($dt_post['jnsPelayanan'] == 1) {
                        if ($dt_post['noSuratKontrol'] == '') {
                            $DataPost['request'] = array(
                                'noKartu'             => $dt_post['noKartu'],
                                'kodeDokter'          => $dt_post['kodeDokter'],
                                'poliKontrol'         => $dt_post['poliKontrol'],
                                'tglRencanaKontrol'   => $dt_post['tglRencanaKontrol'],
                                'user'                => $dt_post['user']
                            );         
                            // var_dump($DataPost);die();
                            $hasil = $this->vclaim_sep->request("/RencanaKontrol/InsertSPRI", "POST", json_encode($DataPost));
                        } else {
                            $DataPUT['request'] = array(
                                'noSPRI'              => $dt_post['noSuratKontrol'],
                                'noKartu'             => $dt_post['noKartu'],
                                'kodeDokter'          => $dt_post['kodeDokter'],
                                'poliKontrol'         => $dt_post['poliKontrol'],
                                'tglRencanaKontrol'   => $dt_post['tglRencanaKontrol'],
                                'user'                => $dt_post['user']
                            );         
                            // var_dump($DataPUT);die();
                            $hasil = $this->vclaim_sep->request("/RencanaKontrol/UpdateSPRI", "PUT", json_encode($DataPUT));
                        }
                    } else if ($dt_post['jnsPelayanan'] == 2) {
                        // Ambil tanggal rencana kontrol dari input
                        $tglRencana = strtotime($dt_post['tglRencanaKontrol']);

                        // Hitung tanggal hari ini + 8 hari
                        $tanggalMinimum = strtotime('+7 days'); // default: dari hari ini

                        // Cek apakah tanggal rencana kontrol kurang dari 8 hari dari sekarang
                        if ($tglRencana < $tanggalMinimum) {
                            $this->REST_Return(422, 'TGL KONTROL < 8 HARI DARI HARI INI', array());
                            return;
                        }
                        if ($dt_post['noSuratKontrol'] == '') {
                            $DataPost['request'] = array(
                                'noSEP'               => $dt_post['noSep'],
                                'kodeDokter'          => $dt_post['kodeDokter'],
                                'poliKontrol'         => $dt_post['poliKontrol'],
                                'tglRencanaKontrol'   => $dt_post['tglRencanaKontrol'],
                                'user'                => $dt_post['user']
                            );  
                            // var_dump($DataPost);die();
                            $hasil = $this->vclaim_sep->request("/RencanaKontrol/insert", "POST", json_encode($DataPost));    
                        } else {
                            $DataPUT['request'] = array(
                                'noSuratKontrol'      => $dt_post['noSuratKontrol'],
                                'noSEP'               => $dt_post['noSep'],
                                'kodeDokter'          => $dt_post['kodeDokter'],
                                'poliKontrol'         => $dt_post['poliKontrol'],
                                'tglRencanaKontrol'   => $dt_post['tglRencanaKontrol'],
                                'user'                => $dt_post['user']
                            );  
                            // var_dump($DataPUT);die();
                            $hasil = $this->vclaim_sep->request("/RencanaKontrol/Update", "PUT", json_encode($DataPUT));
                        }
                    }
                    $d = json_decode($hasil, true);
                    // var_dump($d);
                    // var_dump($d['response']['noSuratKontrol']);
                    if ($d['metaData']['code'] == '200'){
                        if ($dt_post['jnsPelayanan'] == 1) {
                            $noSuratKontrol = $d['response']['noSPRI'];
                        } else if ($dt_post['jnsPelayanan'] == 2) {
                            $noSuratKontrol = $d['response']['noSuratKontrol'];
                        }
                        $dataSave = array(
                            "no_control"          => $noSuratKontrol,
                            "noSep"               => $dt_post['noSep'],
                            "jnsPelayanan"        => $dt_post['jnsPelayanan'],
                            "registration_destination_id"  => $dt_post['registration_destination_id'],
                            "registration_id"     => $dt_post['registration_id'],
                            "kodeDokter"          => $dt_post['kodeDokter'],
                            "poliTujuan"          => $dt_post['poliKontrol'],
                            "tglRencanaKontrol"   => $d['response']['tglRencanaKontrol'],
                            "namaDokter"          => $d['response']['namaDokter'],
                            "noKartu"             => $d['response']['noKartu'],
                            "nama"                => $d['response']['nama'],
                            "kelamin"             => $d['response']['kelamin'],
                            "tglLahir"            => $d['response']['tglLahir'],
                            "user_added"          => $dt_post['user'],
                            "date_added"          => date('Y-m-d H:i:s')
                        );
                        date_default_timezone_set('Asia/Jakarta');
                        $this->Control_bpjs->replace($dataSave);
                        $this->db->trans_complete();
                        if ($this->db->trans_status() === false) {
                            $response_data = array(
                                'code'    => '404',
                                'message' => 'Gagal'
                            );
                            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                        } else {
                            $response_data = array(
                                'noSPRI'  => $noSuratKontrol,
                                'code'    => $d['metaData']['code'],
                                'message' => $d['metaData']['message']
                            );
                            $this->REST_Return($d['metaData']['code'], 'SUCCESS', $response_data);
                        }
                    } else {
                        $d = json_decode($d, true);
                        $response_data = array(
                            'code'    => 202,
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, $d['metaData']['message'], $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function update_sprik_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    if ($dt_post['jnsPelayanan'] == 1) {
                        $DataPost['request'] = array(
                        'noSuratKontrol'      => $dt_post['noSuratKontrol'],
                        'noKartu'             => $dt_post['noKartu'],
                        'kodeDokter'          => $dt_post['kodeDokter'],
                        'poliKontrol'         => $dt_post['poliKontrol'],
                        'tglRencanaKontrol'   => $dt_post['tglRencanaKontrol'],
                        'user'                => $dt_post['user']
                        );         
                        // var_dump($DataPost);die();
                        $hasil = $this->vclaim_sep->request("/RencanaKontrol/UpdateSPRI", "PUT", json_encode($DataPost));
                    } else if ($dt_post['jnsPelayanan'] == 2) {
                        $DataPost['request'] = array(
                        'noSuratKontrol'      => $dt_post['noSuratKontrol'],
                        'noSEP'               => $dt_post['noSep'],
                        'kodeDokter'          => $dt_post['kodeDokter'],
                        'poliKontrol'         => $dt_post['poliKontrol'],
                        'tglRencanaKontrol'   => $dt_post['tglRencanaKontrol'],
                        'user'                => $dt_post['user']
                        );  
                        // var_dump($DataPost);die();
                        $hasil = $this->vclaim_sep->request("/RencanaKontrol/Update", "PUT", json_encode($DataPost));
                    }
                    $d = json_decode($hasil, true);
                    // var_dump($d);
                    // var_dump($d['response']['noSuratKontrol']);
                    if ($d['metaData']['code'] == '200'){
                        if ($dt_post['jnsPelayanan'] == 1) {
                            $noSuratKontrol = $d['response']['noSPRI'];
                        } else if ($dt_post['jnsPelayanan'] == 2) {
                            $noSuratKontrol = $d['response']['noSuratKontrol'];
                        }
                        $response_data = array(
                            'noSPRI'  => $noSuratKontrol,
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    } else {
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d
                        );
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_checkout_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    $DataPost['request']['t_sep'] = array(
                        'noSep'               => $dt_post['noSep'],
                        'statusPulang'        => $dt_post['statusPulang'],
                        'noSuratMeninggal'    => $dt_post['noSuratMeninggal'],
                        'tglMeninggal'        => $dt_post['tglMeninggal'],
                        'tglPulang'           => $dt_post['tglPulang'],
                        'noLPManual'          => $dt_post['noLPManual'],
                        'user'                => $dt_post['user']
                    );         
                    // var_dump($DataPost);
                    $hasil = $this->vclaim_sep->request("/SEP/2.0/updtglplg", "PUT", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    $d = json_decode($d, true);
                    if ($d['metaData']['code'] == '200'){
                        $this->db->where('registration_destination_id', $dt_post['registration_destination_id']);
                        $this->db->where('insurance_id',2);
                        $this->db->where('is_primary',1);
                        $this->db->update('registration_insurance', array('tglPulang'=>$dt_post['tglPulang']));
                        $this->db->trans_complete();
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'Success', $response_data);
                    } else {
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $d);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_backdate_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    $DataPost['request']['t_sep'] = array(
                        'noKartu'             => $dt_post['noKartu'],
                        'tglSep'              => $dt_post['tglSep'],
                        'jnsPelayanan'        => $dt_post['jnsPelayanan'],
                        'jnsPengajuan'        => $dt_post['jnsPengajuan'],
                        'keterangan'          => $dt_post['keterangan'],
                        'user'                => $dt_post['user']
                    );         
                    $hasil = $this->vclaim_sep->request("/Sep/pengajuanSEP", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    // var_dump($d);
                    $d = json_decode($d, true);
                    if ($d['metaData']['code'] == '200'){
                        $this->db->where('registration_destination_id', $dt_post['registration_destination_id']);
                        $this->db->where('insurance_id',2);
                        $this->db->where('is_primary',1);
                        $this->db->update('registration_insurance', array('sepBackdate'=>$dt_post['tglSep'],'jnsPelayanan'=>$dt_post['jnsPelayanan'],'jnsPengajuan'=>$dt_post['jnsPengajuan'],'keterangan'=>$dt_post['keterangan']));
                        $this->db->trans_complete();
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'Success', $response_data);
                    } else {
                        $this->REST_Return(201, $d['metaData']['message'], $d);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_refer_special_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    $diagnosa = array();
                    $procedure = array();
                    if (isset($dt_post['diagnosa'])) {
                        foreach ($dt_post['diagnosa'] as $didx => $rd) {
                            if ($didx == 0){
                                $diagnosa[$didx] = array('kode' => 'P;'.$dt_post['diagnosa'][$didx]['kode']);
                            } else {
                                $diagnosa[$didx] = array('kode' => 'S;'.$dt_post['diagnosa'][$didx]['kode']);
                            }
                        }
                    }
                    if (isset($dt_post['procedure'])) {
                        foreach ($dt_post['procedure'] as $didx => $rd) {
                            $procedure[$didx] = array('kode' => $dt_post['procedure'][$didx]['kode']);
                        }
                    }
                    $DataPost = array(
                        'noRujukan'             => $dt_post['noRujukan'],
                        'diagnosa'              => $diagnosa,
                        'procedure'             => $procedure,
                        'user'                  => $dt_post['user']
                    );         
                    $hasil = $this->vclaim_sep->request("/Rujukan/Khusus/insert", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    // var_dump($DataPost);
                    $d = json_decode($d, true);
                    if ($d['metaData']['code'] == '200'){
                        $this->db->trans_complete();
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'Success', $response_data);
                    } else {
                        $this->REST_Return(201, $d['metaData']['message'], $d);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_prb_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    $obat = array();
                    if (!empty($dt_post['obat'])) {
                      foreach ($dt_post['obat'] as $oix => $robt) {
                        $obat[$oix] = array(
                          'kdObat' => $dt_post['obat'][$oix]['kdObat'],
                          'signa1' => $dt_post['obat'][$oix]['signa1'],
                          'signa2' => $dt_post['obat'][$oix]['signa2'],
                          'jmlObat' => $dt_post['obat'][$oix]['jmlObat'],
                        );
                      }
                    }
                    $kodeDPJP = $this->Employee->read($dt_post['doctor_in_charge_id'],false)[0]->bride_bpjs;
                    $DataPost['request']['t_prb'] = array(
                        'noSep'               => $dt_post['noSep'],
                        'noKartu'             => $dt_post['noKartu'],
                        'alamat'              => $dt_post['alamat'],
                        'email'               => $dt_post['email'],
                        'programPRB'          => $dt_post['programPRB'],
                        'kodeDPJP'            => $kodeDPJP,
                        'keterangan'          => $dt_post['keterangan'],
                        'saran'               => $dt_post['saran'],
                        'user'                => "1234567",
                        'obat'                => $obat
                    );         
                    // var_dump($DataPost);die();
                    $hasil = $this->vclaim_sep->request("/PRB/insert", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    // var_dump($DataPost);
                    // var_dump($d);
                    if ($d['metaData']['code'] == '200'){
                        $dataSave = array(
                            "noSRB"               => $d['response']['noSRB'],
                            "noSep"               => $dt_post['noSep'],
                            "registration_destination_id"  => $dt_post['registration_destination_id'],
                            "registration_id"     => $dt_post['registration_id'],
                            "kodeDPJP"            => $d['response']['DPJP']['kode'],
                            "namaDokter"          => $d['response']['DPJP']['nama'],
                            "keterangan"          => $d['response']['keterangan'],
                            "obatreturn"          => json_encode($d['response']['obat']['list']),
                            "obatpost"            => json_encode($obat),
                            "alamat"              => $d['response']['peserta']['alamat'],
                            "kodeasalFaskes"      => $d['response']['peserta']['asalFaskes']['kode'],
                            "namaasalFaskes"      => $d['response']['peserta']['asalFaskes']['nama'],
                            "email"               => $d['response']['peserta']['email'],
                            "kelamin"             => $d['response']['peserta']['kelamin'],
                            "namaPasien"          => $d['response']['peserta']['nama'],
                            "noKartu"             => $d['response']['peserta']['noKartu'],
                            "noTelepon"           => $d['response']['peserta']['noTelepon'],
                            "tglLahir"            => $d['response']['peserta']['tglLahir'],
                            "programPRB"          => $d['response']['programPRB']['kode'],
                            "namaprogramPRB"      => $d['response']['programPRB']['nama'],
                            "saran"               => $d['response']['saran'],
                            "tglSRB"              => $d['response']['tglSRB'],
                            "user_added"          => $dt_post['user'],
                            "date_added"          => date('Y-m-d H:i:s')
                        );   
                        $this->Prb_bpjs->replace($dataSave);
                        $this->db->trans_complete();
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'Success', $response_data);
                    } else {
                        $this->REST_Return(201, $d['metaData']['message'], $d);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_refer_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    $DataPost['request']['t_rujukan'] = array(
                        'noSep'               => $dt_post['noSep'],
                        'tglRujukan'          => $dt_post['tglRujukan'],
                        'tglRencanaKunjungan' => $dt_post['tglRencanaKunjungan'],
                        'ppkDirujuk'          => $dt_post['ppkDirujuk'],
                        'jnsPelayanan'        => $dt_post['jnsPelayanan'],
                        'catatan'             => $dt_post['note'],
                        'catatan'             => $dt_post['note'],
                        'diagRujukan'         => $dt_post['diagRujukan'],
                        'tipeRujukan'         => $dt_post['tipeRujukan'],
                        'poliRujukan'         => $dt_post['poliRujukan'],
                        'user'                => $dt_post['user']
                    );         
                    // var_dump($DataPost);die();
                    $hasil = $this->vclaim_sep->request("/Rujukan/2.0/insert", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    // var_dump($DataPost);
                    // var_dump($d);
                    if ($d['metaData']['code'] == '200'){
                        $dataSave = array(
                            "no_refer"            => $d['response']['rujukan']['noRujukan'],
                            "noSep"               => $dt_post['noSep'],
                            "registration_destination_id"  => $dt_post['registration_destination_id'],
                            "registration_id"     => $dt_post['registration_id'],
                            "jnsPelayanan"        => $dt_post['jnsPelayanan'],
                            "tipeRujukan"         => $dt_post['tipeRujukan'],
                            "tglRencanaKontrol"   => $dt_post['tglRencanaKunjungan'],
                            'catatan'             => $dt_post['note'],
                            "noKartu"             => $dt_post['noKartu'],
                            "objects"             => json_encode($d['response']),
                            "user_added"          => $dt_post['user'],
                            "date_added"          => date('Y-m-d H:i:s')
                        );   
                        $this->Refer_bpjs->replace($dataSave);
                        $this->db->trans_complete();
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'Success', $response_data);
                    } else {
                        $this->REST_Return(201, $d['metaData']['message'], $d);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_aproval_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dataRegistration = $this->Registration_destination->read($dt_post['registration_destination_id'])[0];
                    $DataPost = array();
                    $DataPost['request']['t_sep'] = array(
                        'noKartu'             => $dt_post['noKartu'],
                        'tglSep'              => $dt_post['tglSep'],
                        'jnsPelayanan'        => $dt_post['jnsPelayanan'],
                        'jnsPengajuan'        => $dt_post['jnsPengajuan'],
                        'keterangan'          => $dt_post['keterangan'],
                        'user'                => $dt_post['user']
                    );         
                    $hasil = $this->vclaim_sep->request("/Sep/aprovalSEP", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    // var_dump($d);
                    $d = json_decode($d, true);
                    if ($d['metaData']['code'] == '200'){
                        $this->db->where('registration_destination_id', $dt_post['registration_destination_id']);
                        $this->db->where('insurance_id',2);
                        $this->db->where('is_primary',1);
                        $this->db->update('registration_insurance', array('sepBackdate'=>$dt_post['tglSep']));
                        $this->db->trans_complete();
                        $response_data = array(
                            'code'    => $d['metaData']['code'],
                            'message' => $d['metaData']['message']
                        );
                        $this->REST_Return(201, 'Success', $response_data);
                    } else {
                        $this->REST_Return(201, $d['metaData']['message'], $d);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function delete_sep_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $sep = $dt_post['sep'];
                    $user = $dt_post['user'];
                    $DataPost = '{
                    "request": {
                        "t_sep": {
                            "noSep": "'.$sep.'",
                            "user": "'.$user.'"
                        }
                    }
                    }';
                    $hasil = $this->vclaim_sep->request("/SEP/2.0/delete", "DELETE", $DataPost);
                    $d = json_decode($hasil, true);
                    var_dump($d);
                    if ($d['metaData']['code'] == "200"){
                        $this->db->where('registration_destination_id', $dt_post['registration_destination_id']);
                        $this->db->where('insurance_id',2);
                        $this->db->where('is_primary',1);
                        $this->db->update('registration_insurance', array('sep'=>null,'sep_json'=>null,'user_deleted'=>$user_id,'date_deleted'=>date('Y-m-d H:i:s')));
                    } else {
                        // var_dump(array('registration_destination_id'=>$dt_post['registration_destination_id'],'sep'=>$d->metaData->code));
                        $d = json_decode($d, true);
                        $response_data = $d;
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                        return;
                    }
                    
                    // die();
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'sep'     => $d['response']
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function delete_control_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $noSuratKontrol = $dt_post['noSuratKontrol'];
                    $user = $dt_post['user'];
                    $DataPost = '{
                    "request": {
                        "t_suratkontrol": {
                            "noSuratKontrol": "'.$noSuratKontrol.'",
                            "user": "'.$user.'"
                        }
                    }
                    }';
                    $hasil = $this->vclaim_sep->request("/RencanaKontrol/Delete", "DELETE", $DataPost);
                    $d = json_decode($hasil, true);
                    if ($d['metaData']['code'] == "200"){
                    } else {
                        // var_dump(array('registration_destination_id'=>$dt_post['registration_destination_id'],'sep'=>$d->metaData->code));
                        $d = json_decode($d, true);
                        $response_data = $d;
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                        return;
                    }
                    
                    // die();
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'sep'     => $d['response']
                        );
                        $this->db->where('no_control', $noSuratKontrol);
                        $this->db->update('control_bpjs', array('is_delete'=>1,'user_deleted'=>$user_id,'date_deleted'=>date('Y-m-d H:i:s')));
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function delete_rujukan_post() {
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
            // DATA PATIENT
            $this->form_validation->set_rules('noka', 'noka', 'trim|max_length[18]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check institute_branch_id
                $this->load->library('Vclaim_sep','vclaim_sep');
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($this->Institute_branch->read($institute_branch_id, false) != []) {
                    /* Start Transaction */
                    $this->db->trans_start();
                    $noRujukan = $dt_post['noRujukan'];
                    $user = $dt_post['user'];
                    $DataPost = '{
                    "request": {
                        "t_rujukan": {
                            "noRujukan": "'.$noRujukan.'",
                            "user": "'.$user.'"
                        }
                    }
                    }';
                    $hasil = $this->vclaim_sep->request("/Rujukan/delete", "DELETE", $DataPost);
                    $d = json_decode($hasil, true);
                    if ($d['metaData']['code'] == "200"){
                        $this->db->where('no_refer', $dt_post['noRujukan']);
                        $this->db->update('refer_bpjs', array('is_delete'=>1,'user_deleted'=>$user_id,'date_deleted'=>date('Y-m-d H:i:s')));
                    } else {
                        $this->db->where('no_refer', $dt_post['noRujukan']);
                        $this->db->update('refer_bpjs', array('is_delete'=>1,'user_deleted'=>$user_id,'date_deleted'=>date('Y-m-d H:i:s')));
                        // var_dump(array('registration_destination_id'=>$dt_post['registration_destination_id'],'sep'=>$d->metaData->code));
                        $d = json_decode($d, true);
                        $response_data = $d;
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                        return;
                    }
                    
                    // die();
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'sep'     => $d['response']
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong institute_branch_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function delete_refer_sort_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_sep','vclaim_sep');

        $DataPost = '{
        "request": {
            "t_rujukan": {
                "noRujukan": "'.$id.'",
                "user": "test"
            }
        }
        }';
        $hasil = $this->vclaim_sep->request("/Rujukan/delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        var_dump($d);
        $DataGET = array(['noRujukan' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
    }

    public function delete_sep_sort_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_sep','vclaim_sep');

        $DataPost = '{
        "request": {
            "t_sep": {
                "noSep": "'.$id.'",
                "user": "test"
            }
        }
        }';
        $hasil = $this->vclaim_sep->request("/SEP/2.0/delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        var_dump($d);
        $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
    }

    public function batal_antrean_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_antrol','vclaim_antrol');

        $DataPost = '{
        "kodebooking":"'.$p.'",
        "keterangan":"batal '.$p.'"}';
        $hasil = $this->vclaim_antrol->request("/antrean/batal", "POST", $DataPost);
        $d = json_decode($hasil, true);
        var_dump($d);
        $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
    }

    public function delete_booking_sort_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_control','vclaim_control');

        $DataPost = '{
        "request": {
            "t_control": {
                "noSep": "'.$id.'",
                "user": "test"
            }
        }
        }';
        $hasil = $this->vclaim_control->request("/SEP/2.0/delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        var_dump($d);
        $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
    }

    public function delete_prb_sort_get($p = 0,$sep = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_sep','vclaim_sep');

        $DataPost = '{
        "request": {
            "t_prb": {
                "noSrb": "'.$id.'",
                "noSep": "'.$sep.'",
                "user": "123456"
            }
        }
        }';
        $hasil = $this->vclaim_sep->request("/PRB/Delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        var_dump($d);
        $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
    }

    public function ceksep_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".$p."/JnsPelayanan/2", "GET");
        // $hasil = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".date("Y-m-d")."/JnsPelayanan/2", "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }
    
    public function pulang_get($p = '0',$JnsPelayanan = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/Sep/updtglplg/list/bulan/".$p."/tahun/".$JnsPelayanan."/", "GET");
        // $hasil = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".date("Y-m-d")."/JnsPelayanan/2", "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function lpk_get($p = '0',$JnsPelayanan = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/LPK/TglMasuk/".$p."/JnsPelayanan/".$JnsPelayanan, "GET");
        // $hasil = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".date("Y-m-d")."/JnsPelayanan/2", "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function rujukan_keluar_rs_get($p = '0',$JnsPelayanan = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/Rujukan/Keluar/List/tglMulai/".$p."/tglAkhir/".$JnsPelayanan, "GET");
        // $hasil = $this->vclaim_sep->request("/Monitoring/Kunjungan/Tanggal/".date("Y-m-d")."/JnsPelayanan/2", "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function refkamar_get($p = '0') {
        $this->load->library('Vclaim_aplicares','vclaim_aplicares');
        $hasil = $this->vclaim_aplicares->request("/rest/bed/read/0070R032/1/100", "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function cektaskid_get($p = '0') {
        $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
        $this->load->library('Vclaim_antrol','vclaim_antrol');
        // $output = $this->vclaim_antrol_compress->request("/antrean/getlisttask", "POST", json_encode(array('kodebooking'=>$p)));
        // $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/kodebooking/".$p, "GET");
        $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/tanggal/".$p, "GET");
        // $output = $this->vclaim_antrol->request("/antrean/pendaftaran/aktif", "GET");
        $d = json_decode($output, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function cektaskbook_get($p = '0') {
        $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
        // $this->load->library('Vclaim_antrol','vclaim_antrol');
        $output = $this->vclaim_antrol_compress->request("/antrean/getlisttask", "POST", json_encode(array('kodebooking'=>$p)));
        // $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/kodebooking/".$p, "GET");
        // $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/tanggal/".$p, "GET");
        // $output = $this->vclaim_antrol->request("/antrean/pendaftaran/aktif", "GET");
        $d = json_decode($output, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function icare_get($p = '0',$kodedokter = '0') {
        $this->load->library('Vclaim_icare','vclaim_icare');
        // $this->load->library('Vclaim_antrol','vclaim_antrol');
        // echo json_encode(array('param'=>$p,'kodedokter'=>(int)$kodedokter));die();
        $output = $this->vclaim_icare->request("/api/rs/validate", "POST", json_encode(array('param'=>$p,'kodedokter'=>(int)$kodedokter)));
        // $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/kodebooking/".$p, "GET");
        // $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/tanggal/".$p, "GET");
        // $output = $this->vclaim_antrol->request("/antrean/pendaftaran/aktif", "GET");
        $d = json_decode($output, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function rujukan_keluar_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/Rujukan/Keluar/".$p, "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function sep_search_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/SEP/".$p, "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function sep_internal_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/SEP/Internal/".$p, "GET");
        $d = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $d);
        return;
    }

    public function propinsi_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/referensi/propinsi", "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
        $dataFaskes[$key] = array('kodepropinsi' => $value['kode'],'namapropinsi' => $value['nama']);
        }
        $this->REST_Return(200, 'SUCCESS', $dataFaskes);
        return;
    }

    public function kabupaten_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/referensi/kabupaten/propinsi/".$p, "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
        $dataFaskes[$key] = array('kodekabupaten' => $value['kode'],'namakabupaten' => $value['nama']);
        }
        $this->REST_Return(200, 'SUCCESS', $dataFaskes);
        return;
    }

    public function kecamatan_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/referensi/kecamatan/kabupaten/".$p, "GET");
        $d = json_decode($hasil, true);
        foreach ($d['response']['list'] as $key => $value) {
        $dataFaskes[$key] = array('kodekecamatan' => $value['kode'],'namakecamatan' => $value['nama']);
        }
        $this->REST_Return(200, 'SUCCESS', $dataFaskes);
        return;
    }

    public function suplesi_get($p = '0',$tgl = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/sep/JasaRaharja/Suplesi/".$p."/tglPelayanan/".$tgl, "GET");
        $response_data = json_decode($hasil, true);
        if ($response_data['metaData']['code'] == '200'){
            $this->REST_Return(200, 'SUCCESS', $response_data);
        } else {
            $response_data = json_decode($response_data, true);
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }
        return;
    }

    public function datakll_get($p = '0') {
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil = $this->vclaim_sep->request("/sep/KllInduk/List/".$p, "GET");
        $response_data = json_decode($hasil, true);
        if ($response_data['metaData']['code'] == '200'){
            $this->REST_Return(200, 'SUCCESS', $response_data);
        } else {
            $response_data = json_decode($response_data, true);
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }
        return;
    }
    
    public function refdokter_get($p = '0',$tgl = '0') {
        
        $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
        // $output = $this->vclaim_antrol_compress->request("/ref/poli", "GET");
        $hasil = $this->vclaim_antrol_compress->request("/ref/dokter", "GET");
        // $this->load->library('Vclaim_sep','vclaim_sep');
        // $hasil = $this->vclaim_sep->request("/referensi/dokter/pelayanan/2/tglPelayanan/2024-10-01/Spesialis/SAR", "GET");
        $response_data = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
    public function update_jadwal_dokter_get($p = '426312') {
        $this->load->library('Vclaim_antrol','vclaim_antrol');
        $this->load->model("v1/medical_support/Doctor_schedule_item_model", "Doctor_schedule_item");
        $this->Doctor_schedule_item->whereFields['d.bride_bpjs'] = $p;
        $this->Doctor_schedule_item->whereFields['a.is_delete'] = 0;
        $data = $this->Doctor_schedule_item->read(0,false);
        if($data){
            echo '<pre>';
            $jadwal = array();
            for ($i=0; $i < count($data); $i++) { 
                $jadwal[$i] = array(
                    'hari' => $data[$i]->day,
                    'buka' => preg_replace('/(\d{2}:\d{2}):00/', '${1}', $data[$i]->hour_start),
                    'tutup' => preg_replace('/(\d{2}:\d{2}):00/', '${1}', $data[$i]->hour_end)
                );
            }
            $DataPost = array(
                "kodepoli"          => $data[0]->bride_bpjs_poli,
                "kodesubspesialis"  => $data[0]->bride_bpjsspecialist,
                "kodedokter"        => $data[0]->bride_bpjs,
                "jadwal"            => $jadwal
            );
            
            $hasil = $this->vclaim_antrol->request("/jadwaldokter/updatejadwaldokter", "POST", json_encode($DataPost));
            $d = json_decode($hasil, true);
            echo '<pre>';
            var_dump($DataPost);
            echo '</pre>';
            var_dump($d);
            $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
        }
    }
    
    public function refpoli_get($p = '0',$tgl = '0') {
        // $this->load->library('Vclaim_sep','vclaim_sep');
        // $hasil = $this->vclaim_sep->request("/referensi/poli/dalam", "GET");
        $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
        // $output = $this->vclaim_antrol_compress->request("/ref/poli", "GET");
        $hasil = $this->vclaim_antrol_compress->request("/ref/poli", "GET");
        $response_data = json_decode($hasil, true);
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function read_faskes_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        if ($q_strings['faskes']) {
            $faskes = $q_strings['faskes'];
        } else {
            $faskes = 2;
        }
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
        //     $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if (isset($q_strings['keywords'])) {
                $this->load->library('Vclaim_sep','vclaim_sep');
                $hasil = $this->vclaim_sep->request("/referensi/faskes/".$q_strings['keywords']."/".$faskes, "GET");
                $d = json_decode($hasil, true);
                foreach ($d['response']['faskes'] as $key => $value) {
                    $response_data[$key] = array('kodetujuanRujukan' => $value['kode'],'namatujuanRujukan' => $value['nama']);
                }
            } else {
                $response_data = array();
            }
            $this->REST_Return(200, 'SUCCESS', $response_data);
            return;
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
	}

    public function ppkspesialishfis_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if (isset($q_strings['PPKRujukan'])) {
                $this->load->library('Vclaim_sep','vclaim_sep');
                if (!empty($q_strings['PPKRujukan'])) {
                    $hasil = $this->vclaim_sep->request("/Rujukan/ListSpesialistik/PPKRujukan/".$q_strings['PPKRujukan']."/TglRujukan/".$q_strings['TglRujukan']."", "GET");
                    $d = json_decode($hasil, true);
                    $response_data = $d['response']['list'];
                } else {
                    $response_data = array();
                }
            } else {
                $response_data = array();
            }
            $this->REST_Return(200, 'SUCCESS', $response_data);
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function delete_control_sort_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_sep','vclaim_sep');
        $DataPost = '{
        "request": {
            "t_suratkontrol": {
                "noSuratKontrol": "'.$id.'",
                "user": "administration"
            }
        }
        }';
        $hasil = $this->vclaim_sep->request("/RencanaKontrol/Delete", "DELETE", $DataPost);
        $d = json_decode($hasil, true);
        var_dump($d);
        $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
    }

    /**
     * GET Patient with API
     * -------------------------
     * @method: GET
     *//**
     * GET Patient with API
     * -------------------------
     * @method: GET
     */

    public function noka_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/Peserta/nokartu/"."$p"."/tglSEP/".date('Y-m-d'), "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $output = json_decode($output,true);
                if($output['metaData']['code'] == '200') {
                    $response_data = $output;
                } else {
                    $response_data = json_decode($output);
                }
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
    
    public function ref_poli_get($p = 0,$nomor = 0,$tanggal = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/RencanaKontrol/ListSpesialistik/JnsKontrol/"."$p"."/nomor/".$nomor."/TglRencanaKontrol/".$tanggal, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function updtglplg_list_get($p = 0,$year = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/Sep/updtglplg/list/bulan/"."$p"."/tahun/".$year."/tahun", "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function aproval_get($p = 0,$year = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("Sep/persetujuanSEP/list/bulan/"."$p"."/tahun/".$year, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function ref_dokter_get($p = 0,$kdpoli = 0,$tanggal = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/RencanaKontrol/JadwalPraktekDokter/JnsKontrol/"."$p"."/KdPoli/".$kdpoli."/TglRencanaKontrol/".$tanggal, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function obat_prb_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/referensi/obatprb/".$p, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function diagnosa_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/referensi/diagnosa/".$p, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function procedure_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/referensi/procedure/".$p, "GET");
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = json_decode($output);
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = json_decode($output);
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
    }
    
    public function kelas_rawat_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/referensi/kelasrawat", "GET");
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = json_decode($output);
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = json_decode($output);
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
    }
    
    public function spesialistik_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/referensi/spesialistik", "GET");
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = json_decode($output);
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = json_decode($output);
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
    }
    
    public function carakeluar_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/referensi/carakeluar", "GET");
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = json_decode($output);
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = json_decode($output);
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
    }
    
    public function pascapulang_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/referensi/pascapulang", "GET");
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = json_decode($output);
            $error   = false;
        } else if($output != [] or $id == 0) {
            $response_data = json_decode($output);
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
    }
    
    public function total_sep_get($p = 0,$kdpoli = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/Rujukan/JumlahSEP/"."$p"."/".$kdpoli, "GET");
            $d = json_decode($output, true);
            if($d['metaData']['code']!=='200'){
                $this->total_sep_get(1,$kdpoli);
                return;
            }
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function monitoring_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/monitoring/HistoriPelayanan/NoKartu/"."$p"."/tglMulai/2024-10-01/tglAkhir/2024-10-29", "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function jadwal_dokter_get($p = 0,$kdpoli = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
            // $output = $this->vclaim_antrol_compress->request("/ref/poli", "GET");
            $output = $this->vclaim_antrol_compress->request("/jadwaldokter/kodepoli/"."$p"."/tanggal/".$kdpoli, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function cekantrean_get($p = 0,$kdpoli = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
            // $output = $this->vclaim_antrol_compress->request("/ref/poli", "GET");
            $output = $this->vclaim_antrol_compress->request("/antrean/pendaftaran/kodebooking/".$p, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }

    public function antrean_active_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_antrol','vclaim_antrol');
            $output = $this->vclaim_antrol->request("/dashboard/waktutunggu/tanggal/".date('Y-m-d').'/waktu/rs', "GET");
            $output = json_decode($output);
            $output = json_decode($output);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }
    
    public function nik_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/Peserta/nik/"."$p"."/tglSEP/".date('Y-m-d'), "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $output = json_decode($output,true);
                if($output['metaData']['code'] == '200') {
                    $response_data = $output;
                } else {
                    $response_data = json_decode($output);
                }
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

    public function sep_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/SEP/".$p, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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

    public function control_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/RencanaKontrol/noSuratKontrol/".$p, "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
    
    public function fix_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
            $this->load->library('Vclaim_sep','vclaim_sep');
            $output = $this->vclaim_sep->request("/Peserta/nokartu/"."$p"."/tglSEP/".date('Y-m-d'), "GET");
        $response_data = json_decode($output);
        var_dump($response_data);
        $response_data = array();
        if ($output != [] && $id != 0) {
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
    }
    
    public function backdate_get($p = 0,$mr = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/Peserta/nokartu/"."$p"."/tglSEP/".date('Y-m-d'), "GET");
        $response_data = json_decode($output);
        if($output){
            if($response_data->response->peserta->mr->noMR){
                $dt_patient = array(
                    'norm'                  => $response_data->response->peserta->mr->noMR,
                    'institute_branch_id'   => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                    'idcard'                => $response_data->response->peserta->nik,
                    'typeidcard'            => 1,
                    'alias'                 => $response_data->response->peserta->sex == "P" ? "5" : '6',
                    'name'                  => $response_data->response->peserta->nama,
                    'fullname'              => $response_data->response->peserta->nama,
                    'birth_date'            => $response_data->response->peserta->tglLahir,
                    'sex'                   => $response_data->response->peserta->sex == "P" ? 0 : 1,
                    'phone'                 => $response_data->response->peserta->mr->noTelepon,
                    'user_added'            => 'fb783efe-6e0e-4308-b384-05cdc540d346',
                    'date_added'            => date("Y-m-d H:i:s",time()),
                    'date_updated'          => date("Y-m-d H:i:s",time()),
                    'type_patient_id'       => '1',
                    'is_new'                => 'N',
                );
                // echo "<pre>";
                // var_dump($dt_patient);
                // echo "</pre>";
                // die();
                $this->Patient->replace($dt_patient);
            }
        }
        $this->REST_Return(201, 'SUCCESS', $mr);
        return;
    }

    
    
    public function finger_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $db_sirs = $this->load->database('antrol', true);
        $db_sirs->select("*");
        $db_sirs->from("bpjs_jkn_antrean");
        $db_sirs->where("bpjs_jkn_antrean.kodebooking",$p);
        $data_booking = $db_sirs->get()->result()[0];
        $output = $this->vclaim_sep->request("/SEP/FingerPrint/Peserta/"."$data_booking->nomorkartu"."/TglPelayanan/".date('Y-m-d'), "GET");
        $response_data = json_decode($output);
        if($response_data){
            
        }
        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }

    
    
    public function cekfinger_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/SEP/FingerPrint/Peserta/"."$id"."/TglPelayanan/".date('Y-m-d'), "GET");
        $response_data = json_decode($output);
        if($response_data){
            
        }
        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }
    

    public function RujukanList_get($p = 0,$type = 'KL') {
       header("Access-Control-Allow-Origin: *");
       parse_str($_SERVER['QUERY_STRING'],$q_strings);
       $error  = true;
       $id     = $this->security->xss_clean($p);
       $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            // $output = $this->vclaim_sep->request("/Rujukan/RS/Peserta/"."$p", "GET");
            // $output = $this->vclaim_sep->request("/Rujukan/List/Peserta/"."$p", "GET");
            $count_find = 0;
            $found_data = false;
            if ($type == 'RS') {
                $category_store = array('noka'=>array('/Rujukan/RS/List/Peserta/'));
            } else {
                $category_store = array('noka'=>array('/Rujukan/List/Peserta/'));
            }
            $category_store = $category_store['noka'];
            while ($count_find < 2 && $found_data == false) {
                $cat    = $category_store[$count_find];
                $hasil  = $this->vclaim_sep->request($cat.$p, "GET");
                $d      = json_decode($hasil, true);
                // if (isset($d['metaData']['code'])) {
                    if($d['metaData']['code'] == '200') {
                        foreach ($d['response']['rujukan'] as $key => $value) {
                            if ($count_find == 0) { //Faskes 1
                                $dataRujukan[$key] = ['noKunjungan' => $value['noKunjungan'], 'pelayanan' => $value['pelayanan']['kode'], 'nmAsuransi' => $value['peserta']['cob']['nmAsuransi'],'noAsuransi' => $value['peserta']['cob']['noAsuransi'],'tglTATcob' => $value['peserta']['cob']['tglTAT'],'tglTMTcob' => $value['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $value['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $value['peserta']['hakKelas']['kode'],'dinsos' => $value['peserta']['informasi']['dinsos'],'noSKTM' => $value['peserta']['informasi']['noSKTM'],'prolanisPRB' => $value['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $value['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $value['peserta']['jenisPeserta']['kode'],'noMR' => $value['peserta']['mr']['noMR'],'noTelepon' => $value['peserta']['mr']['noTelepon'],'nama' => $value['peserta']['nama'],'nik' => $value['peserta']['nik'],'noKartu' => $value['peserta']['noKartu'],'pisa' => $value['peserta']['pisa'],'kdProvider' => $value['peserta']['provUmum']['kdProvider'],'nmProvider' => $value['peserta']['provUmum']['nmProvider'],'sex' => $value['peserta']['sex'],'keteranganstatusPeserta' => $value['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $value['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $value['peserta']['tglCetakKartu'],'tglLahir' => $value['peserta']['tglLahir'],'tglTAT' => $value['peserta']['tglTAT'],'tglTMT' => $value['peserta']['tglTMT'],'umurSaatPelayanan' => $value['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $value['peserta']['umur']['umurSekarang'],'kodediagnosa' => $value['diagnosa']['kode'],'namadiagnosa' => $value['diagnosa']['nama'],'kd_poli' =>  $value['poliRujukan']['kode'],'nama_poli' =>  $value['poliRujukan']['nama'],'nmProviderPerujuk' => $value['provPerujuk']['nama'],'tglKunjungan' => $value['tglKunjungan'],'ppkRujukan' => $value['provPerujuk']['kode'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']];
                            } else if ($count_find == 1) { //Faskes 2
                                $dataRujukan = array(['noKunjungan' => $d['response']['rujukan']['noKunjungan'], 'nmAsuransi' => $d['response']['rujukan']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['rujukan']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['rujukan']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['rujukan']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['rujukan']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['rujukan']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['rujukan']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['rujukan']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['rujukan']['peserta']['mr']['noTelepon'],'nama' => $d['response']['rujukan']['peserta']['nama'],'nik' => $d['response']['rujukan']['peserta']['nik'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'pisa' => $d['response']['rujukan']['peserta']['pisa'],'kdProvider' => $d['response']['rujukan']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['rujukan']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['rujukan']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['rujukan']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglTAT' => $d['response']['rujukan']['peserta']['tglTAT'],'tglTMT' => $d['response']['rujukan']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['rujukan']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['rujukan']['peserta']['umur']['umurSekarang'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'kd_poli' =>  $d['response']['rujukan']['poliRujukan']['kode'],'nama_poli' =>  $d['response']['rujukan']['poliRujukan']['nama'],'tglKunjungan' => $d['response']['rujukan']['tglKunjungan'],'ppkRujukan' => $d['response']['rujukan']['provPerujuk']['kode'],'nmProviderPerujuk' => $d['response']['rujukan']['provPerujuk']['nama'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
                            }
                        }
                        if ($count_find == 0) { //Faskes 1
                            $dataRujukan[0]['AsalRujukanFaskes'] = '3';
                        } else if ($count_find == 1) { //Faskes 2
                            $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                        } 
                        $found_data = true;
                    } else {
                        // $this->RujukanList_get($id,'RS');
                        $hasil  = $this->vclaim_sep->request('/Rujukan/RS/List/Peserta/'.$p, "GET");
                        $d      = json_decode($hasil, true);
                        var_dump($hasil);
                        $found_data=true;
                        return;
                    }
                // }
                $count_find++;
            }
            $output = json_encode($dataRujukan);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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

    public function RujukanNoka_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        $this->load->library('Vclaim_sep','vclaim_sep');
        $hasil  = $this->vclaim_sep->request('/Rujukan/Peserta/'.$p, "GET");
        $d      = json_decode($hasil, true);
        if (isset($d['metaData']['code'])) {
            if($d['metaData']['code'] == '200') {
                foreach ($d['response']['rujukan'] as $key => $value) {
                    $dataRujukan[$key] = ['noKunjungan' => $value['noKunjungan'], 'nmAsuransi' => $value['peserta']['cob']['nmAsuransi'],'noAsuransi' => $value['peserta']['cob']['noAsuransi'],'tglTATcob' => $value['peserta']['cob']['tglTAT'],'tglTMTcob' => $value['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $value['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $value['peserta']['hakKelas']['kode'],'dinsos' => $value['peserta']['informasi']['dinsos'],'noSKTM' => $value['peserta']['informasi']['noSKTM'],'prolanisPRB' => $value['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $value['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $value['peserta']['jenisPeserta']['kode'],'noMR' => $value['peserta']['mr']['noMR'],'noTelepon' => $value['peserta']['mr']['noTelepon'],'nama' => $value['peserta']['nama'],'nik' => $value['peserta']['nik'],'noKartu' => $value['peserta']['noKartu'],'pisa' => $value['peserta']['pisa'],'kdProvider' => $value['peserta']['provUmum']['kdProvider'],'nmProvider' => $value['peserta']['provUmum']['nmProvider'],'sex' => $value['peserta']['sex'],'keteranganstatusPeserta' => $value['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $value['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $value['peserta']['tglCetakKartu'],'tglLahir' => $value['peserta']['tglLahir'],'tglTAT' => $value['peserta']['tglTAT'],'tglTMT' => $value['peserta']['tglTMT'],'umurSaatPelayanan' => $value['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $value['peserta']['umur']['umurSekarang'],'kodediagnosa' => $value['diagnosa']['kode'],'namadiagnosa' => $value['diagnosa']['nama'],'kd_poli' =>  $value['poliRujukan']['kode'],'nama_poli' =>  $value['poliRujukan']['nama'],'nmProviderPerujuk' => $value['provPerujuk']['nama'],'tglKunjungan' => $value['tglKunjungan'],'ppkRujukan' => $value['provPerujuk']['kode'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']];
                }
                if ($count_find == 0) { //Faskes 1
                    $dataRujukan[0]['AsalRujukanFaskes'] = '3';
                } else if ($count_find == 1) { //Faskes 2
                    $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                } 
                $found_data = true;
            }
        }
        $output = json_encode($dataRujukan);
        $response_data = array();
        if ($output != [] && $id != 0) {
            $response_data = json_decode($output);
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
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }

    public function Rujukan_get($p = 0,$type = 'KL') {
       header("Access-Control-Allow-Origin: *");
       parse_str($_SERVER['QUERY_STRING'],$q_strings);
       $error  = true;
       $id     = $this->security->xss_clean($p);
       $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            // $output = $this->vclaim_sep->request("/Rujukan/RS/Peserta/"."$p", "GET");
            // $output = $this->vclaim_sep->request("/Rujukan/List/Peserta/"."$p", "GET");
            $count_find = 0;
            $found_data = false;
            // harus bisa bedain rs sama klinik
            if ($type == 'RS') {
                $category_store = array('noru'=>array('/Rujukan/RS/'));
            } else {
                $category_store = array('noru'=>array('/Rujukan/'));
            }
            // $category_store = array('noka'=>array('/Rujukan/List/Peserta/','/Rujukan/RS/Peserta/'));
            $category_store = $category_store['noru'];
            while ($count_find < 2 && $found_data == false) {
                $cat    = $category_store[$count_find];
                $hasil  = $this->vclaim_sep->request($cat.$p, "GET");
                $d = json_decode($hasil, true);
                // if(isset($d['metaData']['code'])){
                    if($d['metaData']['code']=='200'){
                        if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
                            $dataRujukan = array(['noKunjungan' => $d['response']['rujukan']['noKunjungan'], 'nmAsuransi' => $d['response']['rujukan']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['rujukan']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['rujukan']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['rujukan']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['rujukan']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['rujukan']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['rujukan']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['rujukan']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['rujukan']['peserta']['mr']['noTelepon'],'nama' => $d['response']['rujukan']['peserta']['nama'],'nik' => $d['response']['rujukan']['peserta']['nik'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'pisa' => $d['response']['rujukan']['peserta']['pisa'],'kdProvider' => $d['response']['rujukan']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['rujukan']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['rujukan']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['rujukan']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglTAT' => $d['response']['rujukan']['peserta']['tglTAT'],'tglTMT' => $d['response']['rujukan']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['rujukan']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['rujukan']['peserta']['umur']['umurSekarang'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'kd_poli' =>  $d['response']['rujukan']['poliRujukan']['kode'],'nama_poli' =>  $d['response']['rujukan']['poliRujukan']['nama'],'tglKunjungan' => $d['response']['rujukan']['tglKunjungan'],'ppkRujukan' => $d['response']['rujukan']['provPerujuk']['kode'],'nmProviderPerujuk' => $d['response']['rujukan']['provPerujuk']['nama'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
                            if($count_find==0) {
                                if($type == 'RS') {
                                    $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                                } else {
                                    $dataRujukan[0]['AsalRujukanFaskes'] = '3';
                                }
                            } //Faskes 1
                            else if($count_find==1)
                            {
                            if (@$value['provPerujuk']['kode'] == '0070R032') {
                                $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                            }else{
                                $dataRujukan[0]['AsalRujukanFaskes'] = '6';
                            }
                            } //Faskes 2
                            $found_data=true;
                        }
                    } else {
                        $d = json_decode($d, true);
                        // var_dump($d['metaData']);die();
                        if ($d['metaData']['message'] == 'Data Rujukan Tidak Ditemukan.') {
                            $dataRujukan = array(['code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
                            $found_data=true;
                        } else {
                            $this->Rujukan_get($id,'RS');
                            $found_data=true;
                            return;
                        }
                    }
                // }
            }
            $output = json_encode($dataRujukan);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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

    public function Rujukan_first_get($p = 0,$type = 'KL') {
       header("Access-Control-Allow-Origin: *");
       parse_str($_SERVER['QUERY_STRING'],$q_strings);
       $error  = true;
       $id     = $this->security->xss_clean($p);
       $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            // $output = $this->vclaim_sep->request("/Rujukan/RS/Peserta/"."$p", "GET");
            // $output = $this->vclaim_sep->request("/Rujukan/List/Peserta/"."$p", "GET");
            $count_find = 0;
            $found_data = false;
            // harus bisa bedain rs sama klinik
            if ($type == 'RS') {
                $category_store = array('noru'=>array('/Rujukan/RS/'));
            } else {
                $category_store = array('noru'=>array('/Rujukan/'));
            }
            // $category_store = array('noru'=>array('/Rujukan/RS/'));
            // $category_store = array('noka'=>array('/Rujukan/List/Peserta/','/Rujukan/RS/Peserta/'));
            $category_store = $category_store['noru'];
            while ($count_find < 2 && $found_data == false) {
                $cat    = $category_store[$count_find];
                $hasil  = $this->vclaim_sep->request($cat.$p, "GET");
                $d = json_decode($hasil, true);
                // var_dump($d);die();
                // if(isset($d['metaData']['code'])){
                    if($d['metaData']['code']=='200'){
                        if(!empty($d['response']['rujukan']['peserta']['noKartu'])){
                            $dataRujukan = array(['noKunjungan' => $d['response']['rujukan']['noKunjungan'], 'nmAsuransi' => $d['response']['rujukan']['peserta']['cob']['nmAsuransi'],'noAsuransi' => $d['response']['rujukan']['peserta']['cob']['noAsuransi'],'tglTATcob' => $d['response']['rujukan']['peserta']['cob']['tglTAT'],'tglTMTcob' => $d['response']['rujukan']['peserta']['cob']['tglTMT'],'keteranganhakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['keterangan'],'kodehakKelas' => $d['response']['rujukan']['peserta']['hakKelas']['kode'],'dinsos' => $d['response']['rujukan']['peserta']['informasi']['dinsos'],'noSKTM' => $d['response']['rujukan']['peserta']['informasi']['noSKTM'],'prolanisPRB' => $d['response']['rujukan']['peserta']['informasi']['prolanisPRB'],'keteranganjenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['keterangan'],'kodejenisPeserta' => $d['response']['rujukan']['peserta']['jenisPeserta']['kode'],'noMR' => $d['response']['rujukan']['peserta']['mr']['noMR'],'noTelepon' => $d['response']['rujukan']['peserta']['mr']['noTelepon'],'nama' => $d['response']['rujukan']['peserta']['nama'],'nik' => $d['response']['rujukan']['peserta']['nik'],'noKartu' => $d['response']['rujukan']['peserta']['noKartu'],'pisa' => $d['response']['rujukan']['peserta']['pisa'],'kdProvider' => $d['response']['rujukan']['peserta']['provUmum']['kdProvider'],'nmProvider' => $d['response']['rujukan']['peserta']['provUmum']['nmProvider'],'sex' => $d['response']['rujukan']['peserta']['sex'],'keteranganstatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['keterangan'],'kodestatusPeserta' => $d['response']['rujukan']['peserta']['statusPeserta']['kode'],'tglCetakKartu' => $d['response']['rujukan']['peserta']['tglCetakKartu'],'tglLahir' => $d['response']['rujukan']['peserta']['tglLahir'],'tglTAT' => $d['response']['rujukan']['peserta']['tglTAT'],'tglTMT' => $d['response']['rujukan']['peserta']['tglTMT'],'umurSaatPelayanan' => $d['response']['rujukan']['peserta']['umur']['umurSaatPelayanan'],'umurSekarang' => $d['response']['rujukan']['peserta']['umur']['umurSekarang'],'kodediagnosa' => $d['response']['rujukan']['diagnosa']['kode'],'namadiagnosa' => $d['response']['rujukan']['diagnosa']['nama'],'kd_poli' =>  $d['response']['rujukan']['poliRujukan']['kode'],'nama_poli' =>  $d['response']['rujukan']['poliRujukan']['nama'],'tglKunjungan' => $d['response']['rujukan']['tglKunjungan'],'ppkRujukan' => $d['response']['rujukan']['provPerujuk']['kode'],'nmProviderPerujuk' => $d['response']['rujukan']['provPerujuk']['nama'],'code' => $d['metaData']['code'],'message' => $d['metaData']['message']]);
                            if($count_find==0) {$dataRujukan[0]['AsalRujukanFaskes'] = '3';} //Faskes 1
                            else if($count_find==1)
                            {
                                if (@$value['provPerujuk']['kode'] == '0070R032') {
                                    $dataRujukan[0]['AsalRujukanFaskes'] = '4';
                                }else{
                                    $dataRujukan[0]['AsalRujukanFaskes'] = '6';
                                }
                            } //Faskes 2
                            $found_data=true;
                        }
                    } else {
                        $this->Rujukan_first_get($id,'RS');
                        $found_data=true;
                        return;
                    }
                // }
            }
            $output = json_encode($dataRujukan);
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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

    public function RujukanKontrolList_get($p = 0) {
       header("Access-Control-Allow-Origin: *");
       parse_str($_SERVER['QUERY_STRING'],$q_strings);
       $error  = true;
       $id     = $this->security->xss_clean($p);
       $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $this->load->library('Vclaim_sep','vclaim_sep');
            // $output = $this->vclaim_sep->request("/RencanaKontrol/ListRencanaKontrol/tglAwal/".date('Y-m-d')."/tglAkhir/".date('Y-m-d')."/filter/2", "GET");
            $output = $this->vclaim_sep->request("/RencanaKontrol/ListRencanaKontrol/Bulan/".date('m')."/Tahun/".date('Y')."/Nokartu/".$p."/filter/2", "GET");
            $response_data = array();
            if ($output != [] && $id != 0) {
                $response_data = json_decode($output);
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
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $this->$model->whereFields['a.is_delete'] = '0';
            // $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['g.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
            // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
            if ($q_strings != []){
                $q_strings['institute_branch_id'] = $institute_branch_id;
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $this->Patient_allergy->whereFields['norm'] = $output[$i]->norm;
                $this->Insurance->whereFields['norm'] = $output[$i]->norm;
                $this->Insurance->whereFields['a.institute_branch_id'] = $institute_branch_id;
                $this->Insurance->whereFields['c.institute_branch_id'] = $institute_branch_id;
                $this->Responsible_person->whereFields['norm'] = $output[$i]->norm;

                $patient_allergy = $this->Patient_allergy->read();
                $insurance = $this->Insurance->read();
                $responsible_person = $this->Responsible_person->read();

                $output[$i]->patient_allergy = $patient_allergy;
                $output[$i]->patient_insurance = $insurance;
                if($responsible_person != []) $output[$i]->patient_responsible_person = $responsible_person[0];
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
            $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        }
        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
        }
        if(isset($q_strings['name'])) $this->$model->whereLike['a.name'] = $q_strings['name']; unset($q_strings['name']);

        $where = $q_strings;

        $select = 'a.*, h.name as alias_name, h2.name as religion_name,
								c.id_village as village_id,c.nm_village as village_name,
								d.id_district as district_id,d.nm_district as district_name,
								e.id_regency as regency_id,e.nm_regency as regency_name,
								f.id_province as province_id,f.nm_province as province_name';
        $where['a.is_delete'] = 0;
        $join = array(
            array('helper h', "a.alias = h.code and h.variable='alias'", 'LEFT'),
            array('helper h2', "a.religion_id = h2.code and h2.variable='religion'", 'LEFT'),
			array('village c', 'a.address_id = c.id_village', 'LEFT'),
			array('district d', 'c.id_district = d.id_district', 'LEFT'),
			array('regency e', 'd.id_regency = e.id_regency', 'LEFT'),
			array('province f', 'e.id_province = f.id_province', 'LEFT'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        for ($i=0; $i < count($output); $i++) { 
            $norm = $output[$i]->norm;
            
            $select = 'a.*';
            $where_patient_allergy = array(
                'a.norm' => $norm
            );
            $patient_allergy = $this->Patient_allergy->select(0, $select, $where_patient_allergy);

            $output[$i]->patient_allergy = $patient_allergy;

            $select = 'a.*';
            $where_insurance = array(
                'a.norm' => $norm
            );
            $insurance = $this->Insurance->select(0, $select, $where_insurance);

            $output[$i]->patient_insurance = $insurance;

            $select = 'a.*';
            $where_responsible_person = array(
                'a.norm' => $norm
            );
            $responsible_person = $this->Responsible_person->select(0, $select, $where_responsible_person);

            $output[$i]->patient_responsible_person = $responsible_person[0];
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

    public function sync_get($p = 0){
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
            $q_strings['institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['g.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            if ($q_strings != []){
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                $this->Patient_allergy->whereFields['norm'] = $output->rows[$i]->norm;
                $this->Insurance->whereFields['norm'] = $output->rows[$i]->norm;
                $this->Insurance->whereFields['a.institute_branch_id'] = $institute_branch_id;
                $this->Insurance->whereFields['c.institute_branch_id'] = $institute_branch_id;
                $this->Responsible_person->whereFields['norm'] = $output->rows[$i]->norm;

                $patient_allergy = $this->Patient_allergy->read();
                $insurance = $this->Insurance->read();
                $responsible_person = $this->Responsible_person->read();

                $output->rows[$i]->patient_allergy = $patient_allergy;
                $output->rows[$i]->patient_insurance = $insurance;
                if($responsible_person != []) $output->rows[$i]->patient_responsible_person = $responsible_person[0];
            }
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $json_body = array();
                // $json_body_create = array();
                // $json_body_replace = array();
                // $return_bride_get = $this->bride_accurate('api/customer/list.do?fields=id,customerNo,name&sp.pageSize=600&sp.limit=600');
                // for ($i = 0; $i < count($return_bride_get['d']); $i++) {
                //     $this->Patient->whereFields['a.norm'] = $return_bride_get['d'][$i]['customerNo'];
                //     $dpatient = $this->Patient->read(0,false);
                //     if ($dpatient[0]->norm == $return_bride_get['d'][$i]['customerNo']) {
                //         $json_body_replace = array(
                //             'norm' => $dpatient[0]->norm,
                //             'institute_branch_id' => $institute_branch_id,
                //             'name' => $dpatient[0]->name,
                //             'fullname' => $dpatient[0]->fullname,
                //             'bride_cust' => $return_bride_get['d'][$i]['customerNo'],
                //             'bride_cust_id' => $return_bride_get['d'][$i]['id']
                //         );
                //         $this->Patient->replace($json_body_replace);
                //         // var_dump($json_body_replace);
                //     } else {
                //         $json_body_create = array(
                //             'norm' => $return_bride_get['d'][$i]['customerNo'],
                //             'institute_branch_id' => $institute_branch_id,
                //             'name' => $return_bride_get['d'][$i]['name'],
                //             'fullname' => $return_bride_get['d'][$i]['name'],
                //             'bride_cust' => $return_bride_get['d'][$i]['customerNo'],
                //             'bride_cust_id' => $return_bride_get['d'][$i]['id']
                //         );
                //         $this->Patient->replace($json_body_create);
                //         // var_dump($json_body_create);
                //     }
                // }
                // var_dump(count($return_bride_get['d']));
                // die();
                for ($i = 0; $i < count($output->rows); $i++) {
                    if ($output->rows[$i]->bride_cust_id == "") {
                        $json_body =array(
                            'customerNo' => $output->rows[$i]->norm,
                            'name' => $output->rows[$i]->name,
                            'billStreet' => $output->rows[$i]->address,
                            'detailContact' => [array(
                                'name' => $output->rows[$i]->name,
                                'mobilePhone' => $output->rows[$i]->phone
                            )]
                        );
                        // $return_bride = $this->bride_accurate('api/department/delete.do?id='.$output[$i]->bride_id, json_encode($json_body));
                        $return_bride = $this->bride_accurate('api/customer/save.do', json_encode($json_body));
                        // // var_dump($return_bride['r']['parent']['id']);
                        // // die();
                        if ($return_bride['s'] == true) {
                            $data_bride_health_facility = array(
                                'norm' => $output->rows[$i]->norm,
                                'institute_branch_id' => $institute_branch_id,
                                'bride_cust' => $output->rows[$i]->norm,
                                'bride_cust_id' => $return_bride['r']['id'],
                            );
                            $this->Patient->replace($data_bride_health_facility);
                        } else {
                            var_dump($return_bride);
                            $this->REST_Return(404, 'Failed Accurate = ');
                            return;
                        }
                    }
                }
                $response_data = $return_bride;
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

    public function bride_accurate($url = null,$param = null) {
        $url_acc   = 'https://zeus.accurate.id/accurate/';
        $SESSIONID = 'a70e6f70-f3cf-47d0-a3b0-10f8edad8b45';
        $token = 'cd2795d1-0496-41a9-9d72-062f3e0bdf2e';
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        $authorization[] = "Authorization:Bearer ".$token; 
        $authorization[] = "X-Session-ID:".$SESSIONID; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
    }

    /**
     * Patient API
     * --------------------
     * @param: idcard
     * @param: typeidcard
     * @param: alias
     * @param: f_title
     * @param: name
     * @param: b_title
     * @param: fullname
     * @param: birth_date
     * @param: birth_place
     * @param: address
     * @param: sex
     * @param: blood
     * @param: married_id
     * @param: profession_id
     * @param: citizenship_id
     * @param: religion_id
     * @param: last_edu_id
     * @param: phone
     * @param: email
     * @param: address_id
     * @param: patient_issue_id
     * --------------------
     * @method : POST
     * @link: version/mr/patient
     **/

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
    
        $norm = $id;
    
        if ($norm === 0 || $norm === "0") {
            $this->REST_Return(400, 'norm is required');
            return;
        }
    
        $select = "a.norm";
        $patient = $this->$model->select($norm, $select);
    
        if (count($patient) == 0) {
            $this->REST_Return(404, 'patient with norm ' . $norm . ' does not exist!');
            return;
        }

        $idcard = $dt_put['idcard'];
        $typeidcard = $dt_put['typeidcard'];
        $alias = $dt_put['alias'];
        $f_title = $dt_put['f_title'];
        $name = $dt_put['name'];
        $b_title = $dt_put['b_title'];
        $fullname = $dt_put['fullname'];
        $birth_date = $dt_put['birth_date'];
        $birth_place = $dt_put['birth_place'];
        $address = $dt_put['address'];
        $sex = $dt_put['sex'];
        $blood = $dt_put['blood'];
        $married_id = $dt_put['married_id'];
        $profession_id = $dt_put['profession_id'];
        $citizenship_id = $dt_put['citizenship_id'];
        $religion_id = $dt_put['religion_id'];
        $last_edu_id = $dt_put['last_edu_id'];
        $phone = $dt_put['phone'];
        $email = $dt_put['email'];
        $zip_code = $dt_put['zip_code'];
        $address_id = $dt_put['address_id'];
        $patient_issue_id = $dt_put['patient_issue_id'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_patient = array(
            'idcard'                => $idcard,
            'typeidcard'            => $typeidcard,
            'alias'                 => $alias,
            'f_title'               => $f_title,
            'name'                  => $name,
            'b_title'               => $b_title,
            'fullname'              => $fullname,
            'birth_date'            => $birth_date,
            'birth_place'           => $birth_place,
            'address'               => $address,
            'sex'                   => $sex,
            'blood'                 => $blood,
            'married_id'            => $married_id,
            'profession_id'         => $profession_id,
            'citizenship_id'        => $citizenship_id,
            'religion_id'           => $religion_id,
            'last_edu_id'           => $last_edu_id,
            'phone'                 => $phone,
            'email'                 => $email,
            'zip_code'              => $zip_code,
            'address_id'            => $address_id,
            'patient_issue_id'      => $patient_issue_id,
            'date_updated'          => date('Y-m-d H:i:s',time()),
            'user_updated'          => $user_id
        );

        // init responsible_person
        $responsible_id = $dt_put['responsible_id'];

        if($responsible_id != null) {
            $responsible_id = $dt_put['responsible_id'];
            $responsible_idcard = $dt_put['responsible_person_idcard'];
            $responsible_typeidcard = $dt_put['responsible_person_typeidcard'];
            $responsible_person_profession_id = $dt_put['responsible_person_profession_id'];
            $responsible_name = $dt_put['responsible_person_name'];
            $responsible_phone = $dt_put['responsible_person_phone'];
            $responsible_address_id = $dt_put['responsible_person_address_id'];

            $dt_responsible_person = array(
                'norm'                  => $norm,
                'responsible_id'        => $responsible_id,
                'idcard'                => $responsible_idcard,
                'typeidcard'            => $responsible_typeidcard,
                'profession_id'         => $responsible_person_profession_id,
                'name'                  => $responsible_name,
                'phone'                 => $responsible_phone,
                'address_id'            => $responsible_address_id,
            );

            $this->Responsible_person->replace($dt_responsible_person);
        }
    
        $this->$model->update($norm, $dt_patient);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_patient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update patient', $response_data);
            return;
        }
    
        $response_data = array(
            'norm'     => $norm
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    /**
     * Patient API
     * --------------------
     * @param: logo
     * --------------------
     * @method : POST
     * @link: version/mr/patient
     **/

     public function upload_photo_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('photo', 'photo', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $data = $this->$model->read($id,false);
                if($data != []) {
                    # Upload Photo
                    $_FILES['images']['name']= $_FILES['photo']['name'];
                    $_FILES['images']['type']= $_FILES['photo']['type'];
                    $_FILES['images']['tmp_name']= $_FILES['photo']['tmp_name'];
                    $_FILES['images']['error']= $_FILES['photo']['error'];
                    $_FILES['images']['size']= $_FILES['photo']['size'];
                    $files_file = $_FILES['photo']['name'];
                    $config['upload_path'] = './assets/document/patient/photo';
                    $config['allowed_types'] = 'jpeg|png|jpg|PNG|JPG';
                    $config['overwrite'] = true;	
                    $config['file_name'] = $_FILES['photo']['name'];
                    $this->load->library('upload', $config, 'photo_upload');
                    $this->photo_upload->initialize($config);
                    if ($this->photo_upload->do_upload('images') ) {
                        $fileData = $this->photo_upload->data();
                    } else {                        
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $this->photo_upload->display_errors());
                        return;
                    }
    
                    $dt_document = array(
                        'norm'              => $id,
                        'type_document_id'  => 1,
                        'url'               => $files_file
                    );
                    $id = $this->Document->replace($dt_document);
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

    public function KontrolList_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
        $this->load->library('Vclaim_sep','vclaim_sep');
        $output = $this->vclaim_sep->request("/RencanaKontrol/ListRencanaKontrol/tglAwal/".date('Y-m-d')."/tglAkhir/".date('Y-m-d')."/filter/2", "GET");
        $response_data = array();
        if ($output) {
            $response_data = json_decode($output);
            $error   = false;
        } else {
            $this->REST_Return(404, $this->name . ' data not exist!');
            return;
        }
        if ($error == true) {
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }
        return;
    }

    public function merge_norm_put($p = 0) {
        $this->load->helper('logger');
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $norm         = $this->security->xss_clean($p);
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->merge_norm_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        Logger::info($this->debug_uniq, "|---- start merge norm ----|");

        $old_norm = $dt_put['old_norm'];
    
        if ($norm === 0 || $norm === "0") {
            $this->REST_Return(400, 'norm is required');
            return;
        }

        $select = 'a.norm';
        $patient = $this->Patient->select($norm, $select);

        if (count($patient) == 0) {
            $this->REST_Return(404, 'there is no patient with norm ' . $norm);
            return;
        }

        $select = 'a.id';
        $where = array('a.norm' => $norm);
        $join = array(
            array('patient p', 'a.norm = p.norm', 'INNER'),
        );
        $registration = $this->Registration->select(0, $select, $where, $join);

        $registration_exist = 0;
        if (count($registration) > 0) {
            $registration_exist = 1;
        }

        $select = 'a.*';
        $where = array('a.norm' => $norm);
        $insurance = $this->Insurance->select(0, $select, $where);

        $insurance_exist = 0;
        if (count($insurance) > 0) {
            $insurance_exist = 1;
        }

        $select = 'a.norm';
        $where = array('a.norm' => $old_norm);
        $old_insurance = $this->Insurance->select(0, $select, $where);

        $old_insurance_exist = 0;
        if (count($old_insurance) > 0) {
            $old_insurance_exist = 1;
        }

        $select = 'a.*';
        $where = array('a.norm' => $norm);
        $responsible_person = $this->Responsible_person->select(0, $select, $where);

        $responsible_person_exist = 0;
        if (count($responsible_person) > 0) {
            $responsible_person_exist = 1;
        }

        $select = 'a.norm, a.responsible_id';
        $where = array('a.norm' => $old_norm);
        $old_responsible_person = $this->Responsible_person->select(0, $select, $where);

        $old_responsible_person_exist = 0;
        if (count($old_responsible_person) > 0) {
            $old_responsible_person_exist = 1;
        }

        $select = 'a.norm';
        $old_patient = $this->Patient->select($old_norm, $select);

        $select = 'a.*';
        $where = array('a.norm' => $norm);
        $prescription = $this->Prescription->select(0, $select, $where);

        $prescription_exist = 0;
        if (count($prescription) > 0) {
            $prescription_exist = 1;
        }

        $select = 'a.norm';
        $old_patient = $this->Patient->select($old_norm, $select);
    
        /* Start Transaction */
        $this->db->trans_start();

        if (count($old_patient) > 0) {
            $old_norm = $old_patient[0]->norm;

            if ($registration_exist == 1) {
                for ($i=0; $i < count($registration); $i++) { 
                    $registration_id = $registration[$i]->id;

                    $dt_registration = array(
                        'norm' => $old_norm
                    );
    
                    $affected_rows = $this->Registration->update_v2($registration_id, $dt_registration);

                    Logger::info($this->debug_uniq, 'registration affected rows: ' . $affected_rows);
                    Logger::info($this->debug_uniq, 'update registration query: ' . $this->db->last_query());
                    
                    if ($affected_rows <= 0) {
                        $this->REST_Return(422, 'failed changing registration with norm ' . $norm . ' to ' . $old_norm);
                        return;
                    }
                }
            }

            if ($old_insurance_exist == 1) {
                if ($insurance_exist == 1) {
                    Logger::info($this->debug_uniq, 'delete insurance data: ' . json_encode($insurance));

                    $total_delete = count($insurance);

                    $where = array('norm' => $norm);
                    $affected_rows = $this->Insurance->delete_v2(0, $where);

                    Logger::info($this->debug_uniq, 'insurance affected rows: ' . $affected_rows);
                    Logger::info($this->debug_uniq, 'delete insurance query: ' . $this->db->last_query());
                    
                    if ($affected_rows != $total_delete) {
                        $this->REST_Return(422, 'cannot delete insurance, the total_delete and affected rows do not match');
                        return;
                    }
                }
            } else {
                if ($insurance_exist == 1) {
                    Logger::info($this->debug_uniq, 'update insurance data: ' . json_encode($insurance));

                    $dt_insurance = array(
                        'norm' => $old_norm
                    );

                    $where = array('norm' => $norm);
                    $affected_rows = $this->Insurance->update_v2(0, $dt_insurance, $where);

                    Logger::info($this->debug_uniq, 'insurance affected rows: ' . $affected_rows);
                    Logger::info($this->debug_uniq, 'update insurance query: ' . $this->db->last_query());
                    
                    if ($affected_rows <= 0) {
                        $this->REST_Return(422, 'failed changing insurance with norm ' . $norm . ' to ' . $old_norm);
                        return;
                    }
                }
            }

            if ($old_responsible_person_exist == 1) {
                if ($responsible_person_exist == 1) {
                    Logger::info($this->debug_uniq, 'delete responsible_person data: ' . json_encode($responsible_person));

                    $total_delete = count($responsible_person);

                    $where = array('norm' => $norm);
                    $affected_rows = $this->Responsible_person->delete_v2(0, $where);

                    Logger::info($this->debug_uniq, 'responsible_person affected rows: ' . $affected_rows);
                    Logger::info($this->debug_uniq, 'delete responsible_person query: ' . $this->db->last_query());

                    if ($affected_rows != $total_delete) {
                        $this->REST_Return(422, 'cannot delete responsible_person, the total_delete and affected rows do not match');
                        return;
                    }
                }
            } else {
                if ($responsible_person_exist == 1) {
                    Logger::info($this->debug_uniq, 'update responsible_person data: ' . json_encode($responsible_person));

                    $responsible_id = $responsible_person[0]->responsible_id;
        
                    $dt_responsible_person = array(
                        'norm'           => $old_norm,
                        'responsible_id' => $responsible_id
                    );

                    $where = array('norm' => $norm);
                    $affected_rows = $this->Responsible_person->update_v2(0, $dt_responsible_person, $where);

                    Logger::info($this->debug_uniq, 'responsible_person affected rows: ' . $affected_rows);
                    Logger::info($this->debug_uniq, 'update responsible_person query: ' . $this->db->last_query());

                    if ($affected_rows <= 0) {
                        $response_data = array(
                            'query' => $this->db->last_query(),
                        );
                        $this->REST_Return(422, 'failed changing responsible_person with norm ' . $norm . ' to ' . $old_norm, $response_data);
                        return;
                    }
                }
            }
            
            if ($prescription_exist == 1) {                
                for ($i=0; $i < count($prescription); $i++) { 
                    $prescription_id = $prescription[$i]->id;
                    
                    $dt_prescription = array(
                        'norm' => $old_norm
                    );

                    $affected_rows = $this->Prescription->update_v2($prescription_id, $dt_prescription);

                    Logger::info($this->debug_uniq, 'prescription affected rows: ' . $affected_rows);
                    Logger::info($this->debug_uniq, 'update prescription query: ' . $this->db->last_query());
                    
                    if ($affected_rows <= 0) {
                        $this->REST_Return(422, 'failed changing prescription with norm ' . $norm . ' to ' . $old_norm);
                        return;
                    }
                }
            }

            Logger::info($this->debug_uniq, "|---- finish merge norm ----|");
        }

        // echo "ok";
        // die();

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to merge norm patient', $response_data);
            return;
        }
    
        $response_data = array(
            'norm'     => $old_norm
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}