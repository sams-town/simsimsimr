<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Cppt extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Icd10_model", "Icd10");
        $this->load->model("$this->directory/Icd9cm_model", "Icd9cm");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("$this->directory/Soap_a_model", "Soap_a");
        $this->load->model("$this->directory/Soap_a_action_model", "Soap_a_action");
        $this->load->model("$this->directory/Soap_p_model", "Soap_p");
        $this->load->model("$this->directory/Soap_p_action_model", "Soap_p_action");
        $this->load->model("$this->directory/inpatient/Vital_signs_monitoring_model", "Vital_signs_monitoring");
        $this->load->model("$this->directory/inpatient/Early_warning_score_model", "Early_warning_score");
        $this->load->model("$this->directory/inpatient/Early_warning_score_item_model", "Early_warning_score_item");
        $this->load->model("$this->directory/inpatient/Temperature_chart_model", "Temperature_chart");
        $this->load->model("$this->directory/inpatient/Temperature_chart_item_model", "Temperature_chart_item");
        $this->load->model("$this->directory/inpatient/Pews_model", "Pews");
        $this->load->model("$this->directory/inpatient/Pews_item_model", "Pews_item");
        $this->load->model("$this->directory/inpatient/Consultation_model", "Consultation");
        $this->load->model("$this->directory/Resume_model", "Resume");
        $this->load->model("$this->directory/Origin_model", "Origin");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
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
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/pharmacy/Catalog_model", "Catalog");
        $this->load->model("v1/pharmacy/Depo_model", "Depo");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");
        $this->load->model("v1/pharmacy/Kardex_model", "Kardex");
        $this->load->model("v1/pharmacy/Kardex_item_model", "Kardex_item");
        $this->load->model("v1/pharmacy/Kardex_non_pharmacological_model", "Kardex_non_pharmacological");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/tr/Transaction_doctor_model", "Transaction_doctor");
        // Load Helper
        $this->load->helper('exists');
        $this->load->helper('queue');
        $this->load->helper('ews');
        $this->load->helper('utils');

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->verify_rules = array(
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->update_rules = array(
            array('field' => 'instructions', 'label' => 'instructions', 'rules' => 'trim'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->save_anamnesis_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'subjective', 'label' => 'subjective', 'rules' => 'trim'),
            array('field' => 'nursing_objective', 'label' => 'nursing_objective', 'rules' => 'trim'),
        );

        $this->save_ttv_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'systolic_blood_pressure', 'label' => 'systolic_blood_pressure', 'rules' => 'trim'),
            array('field' => 'diastolic_blood_pressure', 'label' => 'diastolic_blood_pressure', 'rules' => 'trim'),
            array('field' => 'pulse_frequency', 'label' => 'pulse_frequency', 'rules' => 'trim'),
            array('field' => 'breath_frequency', 'label' => 'breath_frequency', 'rules' => 'trim'),
            array('field' => 'oximetry_frequency', 'label' => 'oximetry_frequency', 'rules' => 'trim'),
            array('field' => 'temperature', 'label' => 'temperature', 'rules' => 'trim'),
            array('field' => 'aware', 'label' => 'aware', 'rules' => 'trim'),
            array('field' => 'oxygen_type_id', 'label' => 'oxygen_type_id', 'rules' => 'trim'),
            array('field' => 'eye_score', 'label' => 'eye_score', 'rules' => 'trim'),
            array('field' => 'verbal_score', 'label' => 'verbal_score', 'rules' => 'trim'),
            array('field' => 'motor_score', 'label' => 'motor_score', 'rules' => 'trim'),
            array('field' => 'weight', 'label' => 'weight', 'rules' => 'trim'),
            array('field' => 'height', 'label' => 'height', 'rules' => 'trim'),
            array('field' => 'imt', 'label' => 'imt', 'rules' => 'trim'),
        );

        $this->save_st_generalist_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'head', 'label' => 'head', 'rules' => 'trim'),
            array('field' => 'head_hair_color', 'label' => 'head_hair_color', 'rules' => 'trim'),
            array('field' => 'head_hair_condition', 'label' => 'head_hair_condition', 'rules' => 'trim'),
            array('field' => 'eye_conjunctive', 'label' => 'eye_conjunctive', 'rules' => 'trim'),
            array('field' => 'eye_sclera', 'label' => 'eye_sclera', 'rules' => 'trim'),
            array('field' => 'eye_pupil', 'label' => 'eye_pupil', 'rules' => 'trim'),
            array('field' => 'eye_light_reflex', 'label' => 'eye_light_reflex', 'rules' => 'trim'),
            array('field' => 'mouth_mucosa', 'label' => 'mouth_mucosa', 'rules' => 'trim'),
            array('field' => 'mouth_discoloration', 'label' => 'mouth_discoloration', 'rules' => 'trim'),
            array('field' => 'mouth_tongue', 'label' => 'mouth_tongue', 'rules' => 'trim'),
            array('field' => 'mouth_caries', 'label' => 'mouth_caries', 'rules' => 'trim'),
            array('field' => 'neck_kgb', 'label' => 'neck_kgb', 'rules' => 'trim'),
            array('field' => 'neck_trachea_position', 'label' => 'neck_trachea_position', 'rules' => 'trim'),
            array('field' => 'neck_massa', 'label' => 'neck_massa', 'rules' => 'trim'),
            array('field' => 'thorax_insp_chest', 'label' => 'thorax_insp_chest', 'rules' => 'trim'),
            array('field' => 'thorax_insp_retraction', 'label' => 'thorax_insp_retraction', 'rules' => 'trim'),
            array('field' => 'thorax_palp_chest', 'label' => 'thorax_palp_chest', 'rules' => 'trim'),
            array('field' => 'thorax_palp_fremitus', 'label' => 'thorax_palp_fremitus', 'rules' => 'trim'),
            array('field' => 'thorax_palp_crepitation', 'label' => 'thorax_palp_crepitation', 'rules' => 'trim'),
            array('field' => 'thorax_ausk_pul_vasiculer', 'label' => 'thorax_ausk_pul_vasiculer', 'rules' => 'trim'),
            array('field' => 'thorax_ausk_pul_rhonki', 'label' => 'thorax_ausk_pul_rhonki', 'rules' => 'trim'),
            array('field' => 'thorax_ausk_pul_wheezing', 'label' => 'thorax_ausk_pul_wheezing', 'rules' => 'trim'),
            array('field' => 'thorax_ausk_cor_heart_sound', 'label' => 'thorax_ausk_cor_heart_sound', 'rules' => 'trim'),
            array('field' => 'thorax_ausk_cor_murmur', 'label' => 'thorax_ausk_cor_murmur', 'rules' => 'trim'),
            array('field' => 'thorax_ausk_cor_gallop', 'label' => 'thorax_ausk_cor_gallop', 'rules' => 'trim'),
            array('field' => 'abdomen_supel', 'label' => 'abdomen_supel', 'rules' => 'trim'),
            array('field' => 'abdomen_bowel_sounds', 'label' => 'abdomen_bowel_sounds', 'rules' => 'trim'),
            array('field' => 'abdomen_tenderness', 'label' => 'abdomen_tenderness', 'rules' => 'trim'),
            array('field' => 'abdomen_hepar_lien', 'label' => 'abdomen_hepar_lien', 'rules' => 'trim'),
            array('field' => 'abdomen_other_physical_check', 'label' => 'abdomen_other_physical_check', 'rules' => 'trim'),
            array('field' => 'extremity_acral', 'label' => 'extremity_acral', 'rules' => 'trim'),
            array('field' => 'extremity_crt', 'label' => 'extremity_crt', 'rules' => 'trim'),
            array('field' => 'extremity_edema', 'label' => 'extremity_edema', 'rules' => 'trim'),
            array('field' => 'genitalia', 'label' => 'genitalia', 'rules' => 'trim'),
            array('field' => 'localist_status', 'label' => 'localist_status', 'rules' => 'trim'),
            array('field' => 'ophthal_status', 'label' => 'ophthal_status', 'rules' => 'trim'),
            array('field' => 'neurologist_status', 'label' => 'neurologist_status', 'rules' => 'trim'),
        );

        $this->save_diagnose_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'icd10[]', 'label' => 'icd10', 'rules' => 'trim'),
            array('field' => 'icd10_notes', 'label' => 'icd10_notes', 'rules' => 'trim'),
            array('field' => 'non_icd10[]', 'label' => 'non_icd10', 'rules' => 'trim'),
            array('field' => 'nursing_diagnose', 'label' => 'nursing_diagnose', 'rules' => 'trim'),
        );

        $this->save_procedure_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'icd9cm[]', 'label' => 'icd9cm', 'rules' => 'trim'),
            array('field' => 'therapy_plan', 'label' => 'therapy_plan', 'rules' => 'trim'),
            array('field' => 'transaction_doctor[]', 'label' => 'transaction_doctor', 'rules' => 'trim'),
            array('field' => 'advice', 'label' => 'advice', 'rules' => 'trim'),
        );

        $this->save_plan_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'plan', 'label' => 'plan', 'rules' => 'trim'),
            array('field' => 'nursing_plan', 'label' => 'nursing_plan', 'rules' => 'trim'),
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

        $this->save_nutrition_rules = array(
            array('field' => 'id', 'label' => 'id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'nutrition_assessment', 'label' => 'nutrition_assessment', 'rules' => 'trim'),
            array('field' => 'nutrition_diagnosis', 'label' => 'nutrition_diagnosis', 'rules' => 'trim'),
            array('field' => 'nutrition_intervention', 'label' => 'nutrition_intervention', 'rules' => 'trim'),
            array('field' => 'nutrition_monitoring_evaluation', 'label' => 'nutrition_monitoring_evaluation', 'rules' => 'trim'),
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
    
        $cppt_id = $id;

        if ($cppt_id === 0 || $cppt_id === "0") {
            $this->REST_Return(400, 'cppt_id is required');
            return;
        }

        $select = 'a.id,
        a.soap_id,
        a.registration_destination_id,
        a.date_added,
        a.user_added,
        e.id as doctor_in_charge_id,
        e.fullname as doctor_in_charge_name,
        COALESCE(o.health_facility_item_id, rd.health_facility_item_id) as health_facility_item_id,
        COALESCE(hfio.name, hfi.name) as health_facility_item_name,
        a.subjective,
        a.nursing_objective,
        a.plan,
        a.pharmacological_plan,
        a.nursing_plan,
        a.advice,
        a.nutrition_assessment,
        a.nutrition_diagnosis,
        a.nutrition_intervention,
        a.nutrition_monitoring_evaluation,
        a.systolic_blood_pressure,
        a.diastolic_blood_pressure,
        a.pulse_frequency,
        a.breath_frequency,
        a.oximetry_frequency,
        a.temperature,
        a.aware,
        a.oxygen_type_id,
        a.eye_score,
        a.verbal_score,
        a.motor_score,
        a.skin_color,
        a.behavior,
        a.weight,
        a.height,
        a.imt,
        a.head,
        a.head_hair_color,
        a.head_hair_condition,
        a.eye_conjunctive,
        a.eye_sclera,
        a.eye_pupil,
        a.eye_light_reflex,
        a.mouth_mucosa,
        a.mouth_discoloration,
        a.mouth_tongue,
        a.mouth_caries,
        a.neck_kgb,
        a.neck_trachea_position,
        a.neck_massa,
        a.neck_jvp,
        a.thorax_insp_chest,
        a.thorax_insp_retraction,
        a.thorax_palp_chest,
        a.thorax_palp_fremitus,
        a.thorax_palp_crepitation,
        a.thorax_ausk_pul_vasiculer,
        a.thorax_ausk_pul_rhonki,
        a.thorax_ausk_pul_wheezing,
        a.thorax_ausk_cor_heart_sound,
        a.thorax_ausk_cor_murmur,
        a.thorax_ausk_cor_gallop,
        a.abdomen_supel,
        a.abdomen_bowel_sounds,
        a.abdomen_tenderness,
        a.abdomen_hepar_lien,
        a.abdomen_other_physical_check,
        a.extremity_acral,
        a.extremity_crt,
        a.extremity_edema,
        a.genitalia,
        a.localist_status,
        a.ophthal_status,
        a.neurologist_status';
        $where = array(
            'a.is_delete' => 0,
        );
        $join = array(
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('origin o', 'a.id = o.source_id AND o.source_type="cppt"', 'LEFT'),
            array('health_facility_item hfio', 'o.health_facility_item_id = hfio.id', 'LEFT'),
        );
        $output = $this->$model->select($cppt_id, $select, $where, $join);
        if (count($output) == 0) {
            $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
            return;
        }

        for ($i=0; $i < count($output); $i++) { 
            $user_added = $output[$i]->user_added;

            $select = 'a.id, a.employee_id, a.username, e.fullname as user_added_name, g.name as user_role, ep.position_id as user_added_position_id';
            $where = array();
            $join = array(
                array('user_group ug', 'a.id = ug.user_id', 'INNER'),
                array('group g', 'ug.group_id = g.id', 'INNER'),
                array('employee e', 'a.employee_id = e.id', 'INNER'),
                array('employee_position ep', 'e.id = ep.employee_id', 'LEFT'),
            );
            $user = $this->User->select($user_added, $select, $where, $join);

            $cppt_doctor_added = $user[0]->employee_id;

            $output[$i]->user_added_name = $user[0]->user_added_name;
            $output[$i]->user_added_role = $user[0]->user_role;
            $output[$i]->user_added_employee_id = $user[0]->employee_id;
            $output[$i]->user_added_position_id = $user[0]->user_added_position_id;
            $output[$i]->group_name = $user[0]->user_role;

            $cppt_id = $output[$i]->id;
            $soap_id = $output[$i]->soap_id;

            $output[$i]->icd10_notes = '';
            $output[$i]->non_icd10 = [];
            $output[$i]->icd10 = [];

            // SOAP_A
            $select = 'a.id, a.icd10_notes, a.non_icd10, a.nursing_diagnose';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_a = $this->Soap_a->select(0, $select, $where);
            if (count($soap_a) > 0) {
                $soap_a_id = $soap_a[0]->id;

                $output[$i]->icd10_notes = $soap_a[0]->icd10_notes;
                $output[$i]->non_icd10 = $soap_a[0]->non_icd10 ?? '' != '' ? explode('|', $soap_a[0]->non_icd10) : [];
                $output[$i]->nursing_diagnose = $soap_a[0]->nursing_diagnose;

                $select = 'a.id, a.icd10_id, icd.code as icd10_code, icd.name as icd10_name';
                $where = array(
                    'a.soap_a_id' => $soap_a_id,
                    'a.is_delete' => 0
                );
                $join = array(
                    array('icd10 icd', 'a.icd10_id = icd.id', 'INNER'),
                );
                $order = array('a.index' => 'ASC');
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);

                $output[$i]->icd10 = $soap_a_action;
            }

            // SOAP_P
            $select = 'a.id, a.therapy_plan';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_p = $this->Soap_p->select(0, $select, $where);
            if (count($soap_p) > 0) {
                $soap_p_id = $soap_p[0]->id;

                $output[$i]->therapy_plan = $soap_p[0]->therapy_plan;

                $select = 'a.id, a.icd9cm_id, icd.code as icd9cm_code, icd.name as icd9cm_name';
                $where = array(
                    'a.soap_p_id' => $soap_p_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd9cm icd', 'a.icd9cm_id = icd.id', 'INNER'),
                );
                $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);

                $output[$i]->icd9cm = $soap_p_action;
            }

            // TRANSACTION_DOCTOR
            $select = 'a.id, a.costing_id, a.costing_name, a.var_2';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.is_delete' => 0,
            );
            $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);
            
            $output[$i]->transaction_doctor = $transaction_doctor;

            // LABORATORY_REQUEST
            $select = 'a.id, a.date_request, a.no_request, a.queue, a.status';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

            $output[$i]->laboratory_request = $laboratory_request;

            for ($j=0; $j < count($laboratory_request); $j++) { 
                $laboratory_request_id = $laboratory_request[$j]->id;

                $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis';
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

                $output[$i]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
            }

            // RADIOLOGY_REQUEST
            $select = 'a.id, a.date_request, a.no_request, a.queue, a.clinical_indications, a.status';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $radiology_request = $this->Radiology_request->select(0, $select, $where);

            $output[$i]->radiology_request = $radiology_request;

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

                $output[$i]->radiology_request[$j]->radiology_request_items = $radiology_request_items;
            }

            // PHYSIOTHERAPY_REQUEST
            $select = 'a.id, a.date_request, a.no_request, a.queue';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);

            $output[$i]->physiotherapy_request = $physiotherapy_request;

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

                $output[$i]->physiotherapy_request[$j]->physiotherapy_request_items = $physiotherapy_request_items;
            }

            // PRESCRIPTION
            // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            $select = 'a.id, a.date_added, a.no_order, a.flag, a.pharmacy_depo_id, a.type_prescription_id';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('cppt c', 'a.cppt_id = c.id', 'INNER'),
            );
            $prescription = $this->Prescription->select(0, $select, $where, $join);

            $output[$i]->prescription = [];

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

            $output[$i]->prescription = array_merge($output[$i]->prescription, $prescription);
            
            // KARDEX
            $select = 'a.id, a.kardex_id, a.non_pharmacological_id, np.name, a.doctor_added, e.fullname as doctor_added_name, k.is_suggestion, k.is_approve';
            $where = array(
                'ks.source_id' => $cppt_id,
                'a.is_change' => 1,
                'a.is_delete' => 0,
                'ks.source_type_id' => 2,
                'ks.is_delete' => 0,
                'a.doctor_added' => $cppt_doctor_added,
            );
            $join = array(
                array('kardex k', 'a.kardex_id = k.id', 'INNER'),
                array('kardex_source ks', 'a.kardex_id = ks.kardex_id', 'LEFT'),
                array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'INNER'),
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
            if (count($kardex_non_pharmacological) == 0) { // NOTE - KALAU UDAH LAMA BOLEH DIHAPUS
                $where['k.cppt_id'] = $cppt_id;
                unset($where['ks.source_id']);
                unset($where['ks.source_type_id']);
                $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
            }

            $output[$i]->kardex_non_pharmacological = $kardex_non_pharmacological;

            // pp.date_added replace punya kardex
            $select_kardex = 'a.*, pp.flag, pp.date_added as date_added, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            $where_kardex = array(
                'ks.source_id' => $cppt_id,
                'a.is_delete' => 0,
                'ks.source_type_id' => 2,
                'ks.is_delete' => 0,
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
                $where_kardex['a.cppt_id'] = $cppt_id;
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
                    'a.doctor_added' => $cppt_doctor_added,
                    'a.is_change' => 1,
                    'a.is_delete' => 0,
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

                // $select = 'a.id, a.non_pharmacological_id, np.name, a.doctor_added, e.fullname as doctor_added_name';
                // $where = array(
                //     'a.kardex_id' => $kardex_id,
                //     'a.is_change' => 1,
                //     'a.is_delete' => 0,
                // );
                // $join = array(
                //     array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                //     array('employee e', 'a.doctor_added = e.id', 'INNER'),
                // );
                // $order = array(
                //     'a.date' => 'ASC',
                // );
                // $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join, $order);

                // $select = 'a.id, a.non_pharmacological_id, np.name, a.doctor_added, e.fullname as doctor_added_name';
                // $where = array(
                //     'k.cppt_id' => $cppt_id,
                //     'a.is_change' => 1,
                // );
                // $join = array(
                //     array('kardex k', 'a.kardex_id = k.id', 'INNER'),
                //     array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                //     array('employee e', 'a.doctor_added = e.id', 'INNER'),
                // );
                // $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);

                $kardex[$k]->kardex_non_pharmacological = $kardex_non_pharmacological;
                
                if (count($kardex_item) > 0) {
                    array_push($clean_kardex, $kardex[$k]);
                }
            }

            $output[$i]->prescription = array_merge($output[$i]->prescription, $clean_kardex);
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
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);

        if (isset($q_strings['doctor_in_charge_id'])) {
            $q_strings['a.doctor_in_charge_id'] = $q_strings['doctor_in_charge_id'];
            unset($q_strings['doctor_in_charge_id']);
        }

        if (isset($q_strings['user_added'])) {
            $q_strings['a.user_added'] = $q_strings['user_added'];
            unset($q_strings['user_added']);
        }

        if (isset($q_strings['date_added'])) {
            $q_strings['a.date_added >= '] = $q_strings['date_added'] . ' 00:00:00';
            $q_strings['a.date_added <= '] = $q_strings['date_added'] . ' 23:59:59';
            unset($q_strings['date_added']);
        }

        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $cppt_id = $output->rows[$i]->id;
            $soap_id = $output->rows[$i]->soap_id;
            $user_added = $output->rows[$i]->user_added;

            $select = 'a.id, a.employee_id, g.name as group_name, g.name as user_role';
            $where = array();
            $join = array(
                array('user_group ug', 'a.id = ug.user_id', 'INNER'),
                array('group g', 'ug.group_id = g.id', 'INNER'),
                array('employee e', 'a.employee_id = e.id', 'INNER'),
            );
            $user = $this->User->select($user_added, $select, $where, $join);

            $employee_id = $user[0]->employee_id;
            $group_name = $user[0]->group_name;

            $output->rows[$i]->group_name = $group_name;

            $select = 'a.id, a.fullname, p.name as position_name';
            $where = array();
            $join = array(
                array('employee_position ep', 'a.id = ep.employee_id', 'INNER'),
                array('position p', 'ep.position_id = p.id', 'INNER'),
            );
            $order = array('a.id' => 'ASC');
            $employee = $this->Employee->select($employee_id, $select, $where, $join, $order);

            $output->rows[$i]->user_added_name = $employee[0]->fullname;
            $output->rows[$i]->user_added_role = $user[0]->user_role;

            $output->rows[$i]->icd10_notes = '';
            $output->rows[$i]->non_icd10 = [];
            $output->rows[$i]->nursing_diagnose = '';
            $output->rows[$i]->icd10 = [];

            // SOAP_A
            $select = 'a.id, a.icd10_notes, a.non_icd10, a.nursing_diagnose';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_a = $this->Soap_a->select(0, $select, $where);
            if (count($soap_a) > 0) {
                $soap_a_id = $soap_a[0]->id;

                $output->rows[$i]->icd10_notes = $soap_a[0]->icd10_notes;
                $output->rows[$i]->non_icd10 = $soap_a[0]->non_icd10 ?? '' != '' ? explode('|', $soap_a[0]->non_icd10) : [];
                $output->rows[$i]->nursing_diagnose = $soap_a[0]->nursing_diagnose;

                $select = 'a.id, a.icd10_id, icd.code as icd10_code, icd.name as icd10_name';
                $where = array(
                    'a.soap_a_id' => $soap_a_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd10 icd', 'a.icd10_id = icd.id', 'INNER'),
                );
                $order = array('a.index' => 'ASC');
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);

                $output->rows[$i]->icd10 = $soap_a_action;
            }

            // SOAP_P
            $select = 'a.id, a.therapy_plan';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_p = $this->Soap_p->select(0, $select, $where);
            if (count($soap_p) > 0) {
                $soap_p_id = $soap_p[0]->id;

                $output->rows[$i]->therapy_plan = $soap_p[0]->therapy_plan;

                $select = 'a.id, a.icd9cm_id, icd.code as icd9cm_code, icd.name as icd9cm_name';
                $where = array(
                    'a.soap_p_id' => $soap_p_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd9cm icd', 'a.icd9cm_id = icd.id', 'INNER'),
                );
                $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);

                $output->rows[$i]->icd9cm = $soap_p_action;
            }

            // TRANSACTION_DOCTOR
            $select = 'a.id, a.costing_id, a.costing_name, a.var_2';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);
            
            $output->rows[$i]->transaction_doctor = $transaction_doctor;

            // LABORATORY_REQUEST
            $select = 'a.id, a.no_request, a.queue, a.status, a.date_request';
            $where = array(
                'a.cppt_id' => $cppt_id,
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

                $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis';
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
            $select = 'a.id, a.no_request, a.queue, a.status, a.date_request';
            $where = array(
                'a.cppt_id' => $cppt_id,
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
            $select = 'a.id, a.no_request, a.queue, a.date_request';
            $where = array(
                'a.cppt_id' => $cppt_id,
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

            // PRESCRIPTION
            // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            $select = 'a.id, a.no_order, a.flag, a.pharmacy_depo_id, a.type_prescription_id, a.date_added';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('cppt c', 'a.cppt_id = c.id', 'INNER'),
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
                    a.prescription_item_type_package,
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
                'ks.source_id' => $cppt_id,
                'a.is_change' => 1,
                'a.is_delete' => 0,
                'ks.source_type_id' => 2,
                'a.doctor_added' => $employee_id,
            );
            $join = array(
                array('kardex k', 'a.kardex_id = k.id', 'INNER'),
                array('kardex_source ks', 'a.kardex_id = ks.kardex_id', 'LEFT'),
                array('non_pharmacological np', 'a.non_pharmacological_id = np.id', 'INNER'),
                array('employee e', 'a.doctor_added = e.id', 'INNER'),
            );
            $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
            if (count($kardex_non_pharmacological) == 0) { // NOTE - KALAU UDAH LAMA BOLEH DIHAPUS
                $where['k.cppt_id'] = $cppt_id;
                unset($where['ks.source_id']);
                unset($where['ks.source_type_id']);
                $kardex_non_pharmacological = $this->Kardex_non_pharmacological->select(0, $select, $where, $join);
            }

            $output->rows[$i]->kardex_non_pharmacological = $kardex_non_pharmacological;

            // $select = 'a.id, a.note, a.date, a.doctor_added, e.fullname as doctor_added_name';
            // $where = array(
            //     'ks.source_id' => $cppt_id,
            //     'a.is_change' => 1,
            //     'a.is_delete' => 0,
            //     'ks.source_type_id' => 2,
            //     'a.doctor_added' => $employee_id,
            // );
            // $join = array(
            //     array('kardex k', 'a.kardex_id = k.id', 'INNER'),
            //     array('kardex_source ks', 'a.kardex_id = ks.kardex_id', 'LEFT'),
            //     array('employee e', 'a.doctor_added = e.id', 'INNER'),
            // );
            // $kardex_pharmacological_note = $this->Kardex_pharmacological_note->select(0, $select, $where, $join);

            // $output->rows[$i]->kardex_pharmacological_note = $kardex_pharmacological_note;

            // pp.date_added replace punya kardex
            $select_kardex = 'a.*, pp.flag, pp.date_added as date_added, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            $where_kardex = array(
                'ks.source_id' => $cppt_id,
                'a.is_delete' => 0,
                'ks.source_type_id' => 2,
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
                $where_kardex['a.cppt_id'] = $cppt_id;
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
                    'a.doctor_added' => $employee_id,
                    'a.is_change' => 1,
                    'a.is_delete' => 0,
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

            // SOAP_P
            // $this->Soap_p->whereFields['soap_id'] = $output->rows[$i]->soap_id;
            // $soap_p = $this->Soap_p->read();
            // if($soap_p != []) {
            //     // PRESCRIPTION
            //     // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            //     $select = 'a.id, a.no_order, a.flag, a.pharmacy_depo_id, a.type_prescription_id';
            //     $where = array(
            //         'a.cppt_id' => $output->rows[$i]->id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('cppt c', 'a.cppt_id = c.id', 'INNER'),
            //     );
            //     $prescription = $this->Prescription->select(0, $select, $where, $join);

            //     $output->rows[$i]->prescription = [];

            //     for ($k=0; $k < count($prescription); $k++) { 
            //         $pharmacy_prescription_id = $prescription[$k]->id;

            //         $select = '
            //             a.pharmacy_catalog_id, 
            //             pc.name as pharmacy_catalog_name, 
            //             pc.code as pharmacy_catalog_code,
            //             a.prescription_item_type_id, 
            //             a.prescription_item_type_used, 
            //             a.prescription_item_type_value,
            //             a.package_id,
            //             a.package_id,
            //             a.package_name, 
            //             a.dose, 
            //             p.name as dose_name, 
            //             a.qty, 
            //             a.sort, 
            //             a.used, 
            //             a.desc, 
            //             a.qty_doctor
            //         ';
            //         $where = array(
            //             'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
            //             'a.is_delete' => 0,
            //         );
            //         $join = array(
            //             array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            //             array('package p', 'pc.dose_id = p.id', 'LEFT'),
            //         );
            //         $prescription_item = $this->Prescription_item->select(0, $select, $where, $join);

            //         $prescription[$k]->form_prescription = $prescription_item;

            //         $form_prescription_parent = [];

            //         //------------------- detail form_prescription_parent -//
            //         for ($l=0; $l < count($prescription_item); $l++) { 
            //             $parent_id = $prescription_item[$l]->prescription_item_type_id; 
            //             $parent_used = $prescription_item[$l]->prescription_item_type_used; 
            //             $parent_value = $prescription_item[$l]->prescription_item_type_value; 
            //             $parent_package = $prescription_item[$l]->prescription_item_type_package; 

            //             $dt_form_prescription_parent = array(
            //                 'id' => $parent_id,
            //                 'used' => $parent_used,
            //                 'value' => $parent_value,
            //                 'package' => $parent_package,
            //             );

            //             if (count($form_prescription_parent) == 0) {
            //                 array_push($form_prescription_parent, $dt_form_prescription_parent);
            //             }

            //             $skip = 0;
            //             for ($m=0; $m < count($form_prescription_parent); $m++) { 
            //                 if ($form_prescription_parent[$m]['id'] == $parent_id) {
            //                     $skip = 1;
            //                 }
            //             }

            //             if ($skip == 0) {
            //                 array_push($form_prescription_parent, $dt_form_prescription_parent);
            //             }
            //         }
            //         //----------------------------------------------------//

            //         $prescription[$k]->form_prescription_parent = $form_prescription_parent;
            //     }

            //     $output->rows[$i]->prescription = array_merge($output->rows[$i]->prescription, $prescription);
            // }

            // SOAP_A
            // $this->Soap_a->whereFields['soap_id'] = $output->rows[$i]->soap_id;
            // $this->Soap_a->whereFields['cppt_id'] = $output->rows[$i]->id;
            // $soap_a = $this->Soap_a->read();
            // if($soap_a != []) {
            //     $output->rows[$i]->development_notes = $soap_a[0]->development_notes;
            //     $output->rows[$i]->soap_a_anythingelse = $soap_a[0]->anythingelse;

            //     // SOAP_A_ACTION
            //     $this->Soap_a_action->whereFields['is_delete'] = 0;
            //     $this->Soap_a_action->whereFields['soap_a_id'] = $soap_a[0]->id;
            //     $soap_a_action = $this->Soap_a_action->read();
            //     if($soap_a_action != []) {
            //         for ($j=0; $j < count($soap_a_action); $j++) { 
            //             $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
            //             $output->rows[$i]->icd10[$j]['icd10_id'] = $soap_a_action[$j]->icd10_id;
            //             $output->rows[$i]->icd10[$j]['code'] = @$icd10[0]->code;
            //             $output->rows[$i]->icd10[$j]['code_s'] = @$icd10[0]->code_s;
            //             $output->rows[$i]->icd10[$j]['name'] = @$icd10[0]->name;
            //             $output->rows[$i]->icd10[$j]['date_added'] = $soap_a_action[$j]->date_added;
            //         }
            //     }
            // }

            // LABORATORY_REQUEST
            // $this->Laboratory_request->whereFields['a.soap_id'] = $output->rows[$i]->soap_id;
            // $this->Laboratory_request->whereFields['a.cppt_id'] = $output->rows[$i]->id;
            // $this->Laboratory_request->whereFields['a.is_delete'] = 0;
            // $laboratory_request = $this->Laboratory_request->read();
            // if($laboratory_request != []) {
            //     for ($k=0; $k < count($laboratory_request); $k++) { 
            //         $output->rows[$i]->laboratory_request['id'] = $laboratory_request[$k]->id;
            //         $output->rows[$i]->laboratory_request['institute_branch_id'] = $laboratory_request[$k]->institute_branch_id;
            //         $output->rows[$i]->laboratory_request['no_request'] = $laboratory_request[$k]->no_request;
            //         $output->rows[$i]->laboratory_request['status'] = $laboratory_request[$k]->status;
            //         $output->rows[$i]->laboratory_request['soap_id'] = $laboratory_request[$k]->soap_id;
            //         $output->rows[$i]->laboratory_request['doctor_in_charge_id'] = $laboratory_request[$k]->doctor_in_charge_id;
            //         $output->rows[$i]->laboratory_request['doctor_in_charge_name'] = $laboratory_request[$k]->doctor_in_charge_name;
            //         $output->rows[$i]->laboratory_request['date_request'] = $laboratory_request[$k]->date_request;
            //         $output->rows[$i]->laboratory_request['queue'] = $laboratory_request[$k]->queue;
            //         $output->rows[$i]->laboratory_request['is_delete'] = $laboratory_request[$k]->is_delete;

            //         // LABORATORY_REQUEST_ITEM
            //         $this->Laboratory_request_item->whereFields['laboratory_request_id'] = $laboratory_request[$k]->id;
            //         $this->Laboratory_request_item->whereFields['a.is_delete'] = 0;
            //         $laboratory_request_item = $this->Laboratory_request_item->read(0,false);
            //         if($laboratory_request_item != []) {
            //             for ($j=0; $j < count($laboratory_request_item); $j++) { 
            //                 $output->rows[$i]->laboratory_request_items[$j]['id'] = $laboratory_request_item[$j]->id;
            //                 $output->rows[$i]->laboratory_request_items[$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
            //                 $output->rows[$i]->laboratory_request_items[$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;                        
            //                 $output->rows[$i]->laboratory_request_items[$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
            //                 $output->rows[$i]->laboratory_request_items[$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
            //                 $output->rows[$i]->laboratory_request_items[$j]['var_1'] = $laboratory_request_item[$j]->var_1;
            //                 $output->rows[$i]->laboratory_request_items[$j]['var_2'] = $laboratory_request_item[$j]->var_2;
            //                 $output->rows[$i]->laboratory_request_items[$j]['var_3'] = $laboratory_request_item[$j]->var_3;
            //                 $output->rows[$i]->laboratory_request_items[$j]['service_class_id'] = $laboratory_request_item[$j]->service_class_id;
            //                 $output->rows[$i]->laboratory_request_items[$j]['notes'] = $laboratory_request_item[$j]->notes;
            //                 $output->rows[$i]->laboratory_request_items[$j]['status'] = $laboratory_request_item[$j]->status;
            //             }
            //         }
            //     }
            // }

            // RADIOLOGY_REQUEST
            // $this->Radiology_request->whereFields['a.soap_id'] = $output->rows[$i]->soap_id;
            // $this->Radiology_request->whereFields['a.cppt_id'] = $output->rows[$i]->id;
            // $this->Radiology_request->whereFields['a.is_delete'] = 0;
            // $radiology_request = $this->Radiology_request->read();
            // if($radiology_request != []) {
            //     for ($k=0; $k < count($radiology_request); $k++) { 
            //         $output->rows[$i]->radiology_request['id'] = $radiology_request[$k]->id;
            //         $output->rows[$i]->radiology_request['institute_branch_id'] = $radiology_request[$k]->institute_branch_id;
            //         $output->rows[$i]->radiology_request['no_request'] = $radiology_request[$k]->no_request;
            //         $output->rows[$i]->radiology_request['status'] = $radiology_request[$k]->status;
            //         $output->rows[$i]->radiology_request['soap_id'] = $radiology_request[$k]->soap_id;
            //         $output->rows[$i]->radiology_request['doctor_in_charge_id'] = $radiology_request[$k]->doctor_in_charge_id;
            //         $output->rows[$i]->radiology_request['doctor_in_charge_name'] = $radiology_request[$k]->doctor_in_charge_name;
            //         $output->rows[$i]->radiology_request['date_request'] = $radiology_request[$k]->date_request;
            //         $output->rows[$i]->radiology_request['queue'] = $radiology_request[$k]->queue;
            //         $output->rows[$i]->radiology_request['is_delete'] = $radiology_request[$k]->is_delete;

            //         // RADIOLOGY_REQUEST_ITEM
            //         $this->Radiology_request_item->whereFields['radiology_request_id'] = $radiology_request[$k]->id;
            //         $this->Radiology_request_item->whereFields['a.is_delete'] = 0;
            //         $radiology_request_item = $this->Radiology_request_item->read(0,false);
            //         if($radiology_request_item != []) {
            //             for ($j=0; $j < count($radiology_request_item); $j++) { 
            //                 $output->rows[$i]->radiology_request_items[$j]['id'] = $radiology_request_item[$j]->id;
            //                 $output->rows[$i]->radiology_request_items[$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
            //                 $output->rows[$i]->radiology_request_items[$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
            //                 $output->rows[$i]->radiology_request_items[$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
            //                 $output->rows[$i]->radiology_request_items[$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
            //                 $output->rows[$i]->radiology_request_items[$j]['var_1'] = $radiology_request_item[$j]->var_1;
            //                 $output->rows[$i]->radiology_request_items[$j]['var_2'] = $radiology_request_item[$j]->var_2;
            //                 $output->rows[$i]->radiology_request_items[$j]['var_3'] = $radiology_request_item[$j]->var_3;
            //                 $output->rows[$i]->radiology_request_items[$j]['service_class_id'] = $radiology_request_item[$j]->service_class_id;
            //                 $output->rows[$i]->radiology_request_items[$j]['notes'] = $radiology_request_item[$j]->notes;
            //                 $output->rows[$i]->radiology_request_items[$j]['status'] = $radiology_request_item[$j]->status;
                            
            //                 $this->Radiology_request_item_image->whereFields['a.is_delete'] = 0;
            //                 $url = $this->Radiology_request_item_image->read($radiology_request_item[$j]->id);
            //                 $output->rows[$i]->radiology_request_items[$j]['url'] = $url;

            //                 for ($kk=0; $kk < count($url); $kk++) { 
            //                     $url_raw = $url[$kk]->url;
            //                     $name = substr($url_raw, 20);
                                
            //                     $output->rows[$i]->radiology_request_items[$j]['url'][$kk]->radiology_request_id = $radiology_request_item[$j]->radiology_request_id;
            //                     $output->rows[$i]->radiology_request_items[$j]['url'][$kk]->name = $name;
            //                 }
            //             }
            //         }
            //     }
            // }

            // PHYSIOTHERAPY_REQUEST
            // $this->Physiotherapy_request->whereFields['a.soap_id'] = $output->rows[$i]->soap_id;
            // $this->Physiotherapy_request->whereFields['a.cppt_id'] = $output->rows[$i]->id;
            // $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
            // $physiotherapy_request = $this->Physiotherapy_request->read();
            // if($physiotherapy_request != []) {
            //     for ($k=0; $k < count($physiotherapy_request); $k++) { 
            //         $output->rows[$i]->physiotherapy_request['id'] = $physiotherapy_request[$k]->id;
            //         $output->rows[$i]->physiotherapy_request['institute_branch_id'] = $physiotherapy_request[$k]->institute_branch_id;
            //         $output->rows[$i]->physiotherapy_request['no_request'] = $physiotherapy_request[$k]->no_request;
            //         $output->rows[$i]->physiotherapy_request['status'] = $physiotherapy_request[$k]->status;
            //         $output->rows[$i]->physiotherapy_request['soap_id'] = $physiotherapy_request[$k]->soap_id;
            //         $output->rows[$i]->physiotherapy_request['doctor_in_charge_id'] = $physiotherapy_request[$k]->doctor_in_charge_id;
            //         $output->rows[$i]->physiotherapy_request['doctor_in_charge_name'] = $physiotherapy_request[$k]->doctor_in_charge_name;
            //         $output->rows[$i]->physiotherapy_request['date_request'] = $physiotherapy_request[$k]->date_request;
            //         $output->rows[$i]->physiotherapy_request['queue'] = $physiotherapy_request[$k]->queue;
            //         $output->rows[$i]->physiotherapy_request['is_delete'] = $physiotherapy_request[$k]->is_delete;

            //         // PHYSIOTHERAPY_REQUEST_ITEM
            //         $this->Physiotherapy_request_item->whereFields['physiotherapy_request_id'] = $physiotherapy_request[$k]->id;
            //         $this->Physiotherapy_request_item->whereFields['a.is_delete'] = 0;
            //         $physiotherapy_request_item = $this->Physiotherapy_request_item->read(0,false);
            //         if($physiotherapy_request_item != []) {
            //             for ($j=0; $j < count($physiotherapy_request_item); $j++) { 
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['id'] = $physiotherapy_request_item[$j]->id;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['service_class_id'] = $physiotherapy_request_item[$j]->service_class_id;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['notes'] = $physiotherapy_request_item[$j]->notes;
            //                 $output->rows[$i]->physiotherapy_request_items[$j]['status'] = $physiotherapy_request_item[$j]->status;
            //             }
            //         }
            //     }
            // }
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function history_get($p = 0){
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
    
        $limit = $q_strings['limit'] ?? 20; // FIXME - HARDCODE LIMIT SEMENTARA
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        unset($q_strings['limit']);
        
        $this->$model->offset = $offset; 
        unset($q_strings['offset']);
    
        $where_main = $q_strings;

        if(isset($q_strings['registration_id'])) {
            $registration_id = $q_strings['registration_id'];
            $where_main['r.id'] = $registration_id;
            unset($where_main['registration_id']);
        }

        $select = 'a.id,
        a.soap_id,
        a.registration_destination_id,
        COALESCE(o.health_facility_item_id, rd.health_facility_item_id) as health_facility_item_id,
        COALESCE(hfio.name, hfi.name) as health_facility_item_name,
        a.subjective,
        a.nursing_objective,
        a.objective,
        a.assessment,
        a.plan,
        a.pharmacological_plan,
        a.nursing_plan,
        a.advice,
        a.nutrition_assessment,
        a.nutrition_diagnosis,
        a.nutrition_intervention,
        a.nutrition_monitoring_evaluation,
        a.aware,
        a.eye_score,
        a.verbal_score,
        a.motor_score,
        a.pupil_score,
        a.verbal_response,
        a.oxygen_type_id,
        a.systolic_blood_pressure,
        a.diastolic_blood_pressure,
        a.pulse_frequency,
        a.breath_frequency,
        a.oximetry_frequency,
        a.temperature,
        a.blood_sugar,
        a.skin_color,
        a.behavior,
        a.sore_score,
        a.ews,
        a.weight,
        a.height,
        a.imt,
        a.head_circumference,
        a.chest_circumference,
        a.arm_circumference,
        a.stomach_circumference,
        a.knee_circumference,
        a.head,
        a.head_hair_color,
        a.head_hair_condition,
        a.eye_conjunctive,
        a.eye_sclera,
        a.eye_pupil,
        a.eye_light_reflex,
        a.mouth_mucosa,
        a.mouth_discoloration,
        a.mouth_tongue,
        a.mouth_caries,
        a.neck_kgb,
        a.neck_trachea_position,
        a.neck_massa,
        a.neck_jvp,
        a.thorax_insp_chest,
        a.thorax_insp_retraction,
        a.thorax_palp_chest,
        a.thorax_palp_fremitus,
        a.thorax_palp_crepitation,
        a.thorax_ausk_pul_vasiculer,
        a.thorax_ausk_pul_rhonki,
        a.thorax_ausk_pul_wheezing,
        a.thorax_ausk_cor_heart_sound,
        a.thorax_ausk_cor_murmur,
        a.thorax_ausk_cor_gallop,
        a.abdomen_supel,
        a.abdomen_bowel_sounds,
        a.abdomen_tenderness,
        a.abdomen_hepar_lien,
        a.abdomen_other_physical_check,
        a.extremity_acral,
        a.extremity_crt,
        a.extremity_edema,
        a.genitalia,
        a.localist_status,
        a.ophthal_status,
        a.neurologist_status,
        a.instructions,
        e.fullname as user_added_name,
        p.name as user_added_role,
        u.employee_id as user_added_id,
        ug.group_id,
        g.name as group_name,
        a.date_added
        ';
        $where_main['a.is_delete'] = 0;
        $where_main['r.norm'] = $norm;
        unset($where_main['norm']);
        $join_main = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('user u', 'a.user_added = u.id', 'INNER'),
            array('employee e', 'u.employee_id = e.id', 'INNER'),
            array('employee_position ep', 'e.id = ep.employee_id', 'LEFT'),
            array('position p', 'ep.position_id = p.id', 'LEFT'),
            array('user_group ug', 'a.user_added = ug.user_id', 'INNER'),
            array('group g', 'ug.group_id = g.id', 'INNER'),
            array('origin o', 'a.id = o.source_id AND o.source_type="cppt"', 'LEFT'),
            array('health_facility_item hfio', 'o.health_facility_item_id = hfio.id', 'LEFT'),
        );
        $order = array('a.date_added' => 'DESC');
        $output = $this->$model->select(0, $select, $where_main, $join_main, $order);

        for ($i=0; $i < count($output); $i++) { 
            $cppt_id = $output[$i]->id;
            $soap_id = $output[$i]->soap_id;
            $registration_destination_id = $output[$i]->registration_destination_id;

            $output[$i]->icd10_notes = '';
            $output[$i]->non_icd10 = [];
            $output[$i]->icd10 = [];

            // SOAP_A
            $select = 'a.id, a.icd10_notes, a.non_icd10, a.nursing_diagnose';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_a = $this->Soap_a->select(0, $select, $where);
            if (count($soap_a) > 0) {
                $soap_a_id = $soap_a[0]->id;

                $output[$i]->icd10_notes = $soap_a[0]->icd10_notes;
                $output[$i]->non_icd10 = $soap_a[0]->non_icd10 ?? '' != '' ? explode('|', $soap_a[0]->non_icd10) : [];
                $output[$i]->nursing_diagnose = $soap_a[0]->nursing_diagnose;

                $select = 'a.id, a.icd10_id, icd.code as icd10_code, icd.name as icd10_name';
                $where = array(
                    'a.soap_a_id' => $soap_a_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd10 icd', 'a.icd10_id = icd.id', 'INNER'),
                );
                $order = array('a.index' => 'ASC');
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);

                $output[$i]->icd10 = $soap_a_action;
            }

            $output[$i]->icd9cm = [];
            $output[$i]->therapy_plan = "";

            // SOAP_P
            $select = 'a.id, a.therapy_plan';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_p = $this->Soap_p->select(0, $select, $where);
            if (count($soap_p) > 0) {
                $soap_p_id = $soap_p[0]->id;

                $output[$i]->therapy_plan = $soap_p[0]->therapy_plan;

                $select = 'a.id, a.icd9cm_id, icd.code as icd9cm_code, icd.name as icd9cm_name';
                $where = array(
                    'a.soap_p_id' => $soap_p_id,
                );
                $join = array(
                    array('icd9cm icd', 'a.icd9cm_id = icd.id', 'INNER'),
                );
                $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);

                $output[$i]->icd9cm = $soap_p_action;
            }

            // TRANSACTION_DOCTOR
            $select = 'a.id, a.costing_id, a.costing_name, a.var_2';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);
            
            $output[$i]->transaction_doctor = $transaction_doctor;

            $select = 'a.id, a.no_request';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

            $output[$i]->laboratory_request = $laboratory_request;

            for ($j=0; $j < count($laboratory_request); $j++) { 
                $laboratory_request_id = $laboratory_request[$j]->id;

                $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis';
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

                $output[$i]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
            }

            $select = 'a.id, a.no_request, a.clinical_indications';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $radiology_request = $this->Radiology_request->select(0, $select, $where);

            $output[$i]->radiology_request = $radiology_request;

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

                $output[$i]->radiology_request[$j]->radiology_request_items = $radiology_request_items;
            }

            $select = 'a.id, a.no_request';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);

            $output[$i]->physiotherapy_request = $physiotherapy_request;

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

                $output[$i]->physiotherapy_request[$j]->physiotherapy_request_items = $physiotherapy_request_items;
            }

            // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            $select = 'a.id, a.no_order, a.type_prescription_id, a.pharmacy_depo_id';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('cppt c', 'a.cppt_id = c.id', 'INNER'),
            );
            $prescription = $this->Prescription->select(0, $select, $where, $join);

            $output[$i]->prescription = [];

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
                    a.dose_unit_id, 
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

            // $output[$i]->prescription = array_merge($output[$i]->prescription, $prescription);

            // NOTE - KARDEX HISTORY
            $select = 'a.*, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            $where = array(
                'a.cppt_id' => $cppt_id,
                'a.is_delete' => 0,
            );
            $where_kardex = 'a.pharmacy_prescription_id IS NOT NULL';
            $this->db->where($where_kardex);
            $join = array(
                array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                array('user u', 'a.user_added = u.id', 'INNER'),
                array('employee e2', 'u.employee_id = e2.id', 'INNER'),
            );
            $order = array(
                'a.date_added' => 'DESC',
            );
            $kardex = $this->Kardex->select(0, $select, $where, $join, $order);

            $clean_kardex = [];

            for ($k=0; $k < count($kardex); $k++) { 
                $kardex_id = $kardex[$k]->id;

                $select = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name';
                $where = array(
                    'a.kardex_id' => $kardex_id,
                    'a.is_change' => 1,
                );
                $join = array(
                    array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                    array('package p', 'a.package_id = p.id', 'INNER'),
                );
                $kardex_item = $this->Kardex_item->select(0, $select, $where, $join);

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

            $output[$i]->prescription = array_merge($output[$i]->prescription, $prescription, $clean_kardex);
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

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);
        $user_employee_id = $user[0]->employee_id;
        
        $registration_destination_id = $dt_post['registration_destination_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

        $select = 'a.id, a.registration_id, a.health_facility_item_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;
        $registration_id = $registration_destination[0]->registration_id;

        $select = 'a.id, a.norm';
        $registration = $this->Registration->select($registration_id, $select);

        $norm = $registration[0]->norm;

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $instructions = $dt_post['instructions'];
        $subjective = $dt_post['subjective'];
        $assessment = $dt_post['assessment'];
        $objective = $dt_post['objective'];
        $plan = $dt_post['plan'];
        $aware = $dt_post['aware'];
        $eye_score = $dt_post['eye_score'];
        $verbal_score = $dt_post['verbal_score'];
        $motor_score = $dt_post['motor_score'];
        $pupil_score = $dt_post['pupil_score'];
        $verbal_response = $dt_post['verbal_response'];
        $oxygen_type_id = $dt_post['oxygen_type_id'];
        $systolic_blood_pressure = $dt_post['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_post['diastolic_blood_pressure'];
        $classification_blood_pressure_type_id = $dt_post['classification_blood_pressure_type_id'];
        $pulse_frequency = $dt_post['pulse_frequency'];
        $breath_frequency = $dt_post['breath_frequency'];
        $oximetry_frequency = $dt_post['oximetry_frequency'];
        $classification_oximetry_type_id = $dt_post['classification_oximetry_type_id'];
        $temperature = $dt_post['temperature'];
        $classification_temperature_type_id = $dt_post['classification_temperature_type_id'];
        $blood_sugar = $dt_post['blood_sugar'];
        $sore_score = $dt_post['sore_score'];
        $ews = $dt_post['ews'];
        $weight = $dt_post['weight'];
        $classification_weight_type_id = $dt_post['classification_weight_type_id'];
        $height = $dt_post['height'];
        $imt = $dt_post['imt'];
        $head_circumference = $dt_post['head_circumference'];
        $chest_circumference = $dt_post['chest_circumference'];
        $arm_circumference = $dt_post['arm_circumference'];
        $stomach_circumference = $dt_post['stomach_circumference'];
        $knee_circumference = $dt_post['knee_circumference'];


        /* Start Transaction */
        $this->db->trans_start();

        $select = 'a.id';
        $where = array('registration_destination_id' => $registration_destination_id, 'is_resume_exit' => 0);
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

        $dt_cppt = array(
            'registration_destination_id'           => $registration_destination_id,
            'soap_id'                               => $soap_id,
            'doctor_in_charge_id'                   => $user_employee_id,
            'instructions'                          => $instructions,
            'subjective'                            => $subjective,
            'assessment'                            => $assessment,
            'objective'                             => $objective,
            'plan'                                  => $plan,
            'aware'                                 => $aware,
            'eye_score'                             => $eye_score,
            'verbal_score'                          => $verbal_score,
            'motor_score'                           => $motor_score,
            'pupil_score'                           => $pupil_score,
            'verbal_response'                       => $verbal_response,
            'oxygen_type_id'                        => $oxygen_type_id,
            'systolic_blood_pressure'               => $systolic_blood_pressure,
            'diastolic_blood_pressure'              => $diastolic_blood_pressure,
            'classification_blood_pressure_type_id' => $classification_blood_pressure_type_id,
            'pulse_frequency'                       => $pulse_frequency,
            'breath_frequency'                      => $breath_frequency,
            'oximetry_frequency'                    => $oximetry_frequency,
            'classification_oximetry_type_id'       => $classification_oximetry_type_id,
            'temperature'                           => $temperature,
            'classification_temperature_type_id'    => $classification_temperature_type_id,
            'blood_sugar'                       => $blood_sugar,
            'sore_score'                        => $sore_score,
            'ews'                               => $ews,
            'weight'                            => $weight,
            'classification_weight_type_id'     => $classification_weight_type_id,
            'height'                            => $height,
            'imt'                               => $imt,
            'head_circumference'                => $head_circumference,
            'chest_circumference'               => $chest_circumference,
            'arm_circumference'                 => $arm_circumference,
            'stomach_circumference'             => $stomach_circumference,
            'knee_circumference'                => $knee_circumference,
            'user_added'                        => $user_id,
            'user_updated'                      => $user_id,
            'status'                            => 1,
            'date_added'                        => date('Y-m-d H:i:s', time()),
            'date_updated'                      => date('Y-m-d H:i:s', time()),
        );
    
        $cppt_id = $this->$model->create($dt_cppt);

        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $user_employee_id,
            'observation_date'              => date('Y-m-d H:i:s'),
            'systolic'                      => $systolic_blood_pressure,
            'diastolic'                     => $diastolic_blood_pressure,
            'pulse'                         => $pulse_frequency,
            'temperature'                   => $temperature,
            'respiratory_rate'              => $breath_frequency,
            'oxygen_saturation'             => $oximetry_frequency,
            'verbal_response'               => $verbal_response,
            'oxygen_type_id'                => $oxygen_type_id,
            'sore_score'                    => $sore_score,
            'ews'                           => $ews,
            'blood_sugar'                   => $blood_sugar,
            'weight'                        => $weight,
            'height'                        => $height,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $this->Vital_signs_monitoring->create($dt_vital_signs_monitoring);

        // INPUT EWS
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'observation_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $early_warning_score = $this->Early_warning_score->select(0, $select, $where);

        if (count($early_warning_score) == 0) {
            $dt_early_warning_score = array(
                'registration_destination_id'   => $registration_destination_id,
                'observation_date'              => date('Y-m-d'),
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'date_updated'                  => date('Y-m-d H:i:s',time()),
            );

            $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
        } else {
            $early_warning_score_id = $early_warning_score[0]->id;
        }

        $observation_time = date('H:i:s');
        $blood_pressure = (float) $systolic_blood_pressure;
        $pulse = (float) $pulse_frequency;
        $breathing = (float) $breath_frequency;
        $temperature = (float) $temperature;
        $oxygen_saturation = (float) $oximetry_frequency;
        $oxygen = $oxygen_type_id == 1 ? 2 : 0; // NOTE - oxygen jadi 2,3,4,5..

        $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
    
        $dt_early_warning_score_item = array(
            'early_warning_score_id' => $early_warning_score_id,
            'employee_nurse_id'      => $user_employee_id,
            'observation_time'       => $observation_time,
            'aware'                  => $aware,
            'eye_score'              => $eye_score,
            'verbal_score'           => $verbal_score,
            'motor_score'            => $motor_score,
            'pupil_score'            => $pupil_score,
            'verbal_response'        => $verbal_response,
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

        // INPUT TEMPERATURE CHART
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'monitoring_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $temperature_chart = $this->Temperature_chart->select(0, $select, $where);
        if (count($temperature_chart) == 0) {
        
            $dt_temperature_chart = array(
                'registration_destination_id' => $registration_destination_id,
                'employee_nurse_id'           => $user_employee_id,
                'monitoring_date'             => date('Y-m-d'),
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s',time()),
                'date_updated'                => date('Y-m-d H:i:s',time()),
            );
        
            $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
        } else {
            $temperature_chart_id = $temperature_chart[0]->id;
        }
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => date('H:i:s'),
            'rr'                    => $breath_frequency,
            'pulse'                 => $pulse_frequency,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->Temperature_chart_item->create($dt_temperature_chart_item);

        // INIT_SOAP_A
        $dt_soap_a = array(
            'soap_id'               => $soap_id,
            'cppt_id'               => $cppt_id,
            'date_added'            => date("Y-m-d H:i:s", time())
        );
        $soap_a_id = $this->Soap_a->create($dt_soap_a);

        // INIT_SOAP_A_ACTION
        $icd10_arr = $dt_post['icd10'];
        for ($i=0; $i < count($icd10_arr) ; $i++) { 
            $icd10_id = $icd10_arr[$i]['icd10_id'];

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if(count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            } 
            
            $dt_soap_a_action = array(
                'soap_a_id'         => $soap_a_id,
                'icd10_id'          => $icd10_id,
                'index'             => $i
            );
            $this->Soap_a_action->create($dt_soap_a_action);
        }

        // INIT_PRESCRIPTION
        $form_prescription = $dt_post['form_prescription'];
        $form_prescription_parent = $dt_post['form_prescription_parent'];
        $type_prescription_id = $dt_post['type_prescription_id'];
        if(count($form_prescription) > 0) {
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

            $select = 'a.id';
            $where = array('soap_id' => $soap_id);
            $soap_p = $this->Soap_p->select(0, $select, $where);
            if (count($soap_p) == 0) {
                $dt_soap_p = array(
                    'soap_id' => $soap_id,
                );
                $soap_p_id = $this->Soap_p->create($dt_soap_p);
            } else {
                $soap_p_id = $soap_p[0]->id;
            }

            $pharmacy_depo_id = $dt_post['pharmacy_depo_id'];

            $dt_prescription = array(
                'norm'                      => $norm,
                'institute_branch_id'       => $institute_branch_id,
                'registration_destination_id' => $registration_destination_id,
                'pharmacy_depo_id'          => $pharmacy_depo_id,
                'type_prescription_id'      => ($type_prescription_id == null) ? 1 : $type_prescription_id,
                'soap_p_id'                 => $soap_p_id,
                'cppt_id'                   => $cppt_id,
                'health_facility_item_id'   => $health_facility_item_id,
                'doctor_in_charge_id'       => $user_employee_id,
                'no_order'                  => $no_order,
                'date_added'                => date("Y-m-d H:i:s",time()),
                'date_0'                    => date("Y-m-d H:i:s",time()),
                'flag'                      => 0,
                'queue'                     => $queue,
            );
            $prescription_id = $this->Prescription->create($dt_prescription);

            // INIT_PRESCRIPTION_ITEM
            for ($i=0; $i < count($form_prescription); $i++) { 
                for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                    if ($form_prescription_parent[$ip]['id'] == $form_prescription[$i]['prescription_type_id']) {
                        $dt_prescription_item = array(
                            'pharmacy_prescription_id'      => $prescription_id,
                            'pharmacy_catalog_id'           => $form_prescription[$i]['pharmacy_catalog_id'],
                            'prescription_item_type_id'     => $form_prescription[$i]['prescription_type_id'],
                            'prescription_item_type_used'   => $form_prescription_parent[$ip]['used'],
                            'prescription_item_type_value'  => $form_prescription_parent[$ip]['value'],
                            'package_id'                    => $form_prescription[$i]['package_id'],
                            'package_name'                  => $form_prescription[$i]['package_name'],
                            'dose'                          => isset($form_prescription[$i]['dose']) ? $form_prescription[$i]['dose'] : '',
                            'dose_doctor'                   => isset($form_prescription[$i]['sum_dose']) ? intval($form_prescription[$i]['sum_dose']) : '',
                            'qty_doctor'                    => isset($form_prescription[$i]['value_package']) ? $form_prescription[$i]['value_package'] : '',
                            'qty'                           => isset($form_prescription[$i]['value_package']) ? $form_prescription[$i]['value_package'] : '',
                            'qty_copy'                      => isset($form_prescription[$i]['qty_copy']) ? $form_prescription[$i]['qty_copy'] : '',
                            'sort'                          => isset($form_prescription[$i]['sort']) ? $form_prescription[$i]['sort'] : '',
                            'used'                          => isset($form_prescription[$i]['used']) ? $form_prescription[$i]['used'] : '',
                            'desc'                          => isset($form_prescription[$i]['desc']) ? $form_prescription[$i]['desc'] : '',
                        );
                    }
                }
                $this->Prescription_item->replace($dt_prescription_item);
            }
        }

        // INIT_LABORATORY_REQUEST
        $laboratory_request_items = $dt_post['laboratory_request_items'];
        if(count($laboratory_request_items) > 0){
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

            $laboratory_clinical_indications = $dt_post['laboratory_clinical_indications'];

            $dt_laboratory_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $soap_id,
                'cppt_id'               => $cppt_id,
                'doctor_in_charge_id'   => $user_employee_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'clinical_indications'  => $laboratory_clinical_indications,
                'date_request'          => date('Y-m-d H:i:s',time())
            );
            $id_laboratory_request = $this->Laboratory_request->create($dt_laboratory_request);
            // INIT_LABORATORY_REQUEST_ITEM
            for ($i=0; $i < count($laboratory_request_items) ; $i++) {
                $laboratory_package = $this->Laboratory_package->read($laboratory_request_items[$i]['laboratory_package_id'], false);
                $service_class = $this->Service_class->read($laboratory_request_items[$i]['service_class_id']);
                // Check laboratory_package_id & service_class_id
                if($laboratory_package != [] && $service_class != []) {
                    $costing = $this->Costing->read($laboratory_package[0]->costing_id,false);
                    $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                    // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                    $costing_services_class = $this->Costing_services_class->read(0,false);
                    $dt_laboratory_request_item = array(
                        'laboratory_request_id'     => $id_laboratory_request,
                        'laboratory_package_id'     => $laboratory_request_items[$i]['laboratory_package_id'],
                        'service_class_id'          => $service_class[0]->id,
                        'var_1'                     => $costing_services_class[0]->var_1,
                        'var_2'                     => $costing_services_class[0]->var_2,
                        'var_3'                     => $costing_services_class[0]->var_3,
                        'costing_id'                => $costing[0]->id,
                        'costing_name'              => $costing[0]->name,
                        'date_request'              => date('Y-m-d H:i:s'),
                        'status'                    => 1
                    );
                    $this->Laboratory_request_item->create($dt_laboratory_request_item);
                } else {
                    $this->REST_Return(404, 'There is no laboratory_package with id ' . $laboratory_request_items[$i]['laboratory_package_id'] . ' or service_class with id ' . $laboratory_request_items[$i]['service_class_id']);
                    return;
                }
            }
        }

        // INIT_RADIOLOGY_REQUEST
        $radiology_request_items = $dt_post['radiology_request_items'];
        if(count($radiology_request_items) > 0){
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

            $radiology_clinical_indications = $dt_post['radiology_clinical_indications'];

            $dt_radiology_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $soap_id,
                'cppt_id'               => $cppt_id,
                'doctor_in_charge_id'   => $user_employee_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'clinical_indications'  => $radiology_clinical_indications,
                'date_request'          => date('Y-m-d H:i:s',time())
            );
            $id_radiology_request = $this->Radiology_request->create($dt_radiology_request);
            // INIT_RADIOLOGY_REQUEST_ITEM
            for ($i=0; $i < count($radiology_request_items) ; $i++) {
                $radiology_package = $this->Radiology_package->read($radiology_request_items[$i]['radiology_package_id'], false);
                $service_class = $this->Service_class->read($radiology_request_items[$i]['service_class_id']);
                // Check radiology_package_id & service_class_id
                if($radiology_package != [] && $service_class != []) {
                    $costing = $this->Costing->read($radiology_package[0]->costing_id,false);
                    $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                    // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                    $costing_services_class = $this->Costing_services_class->read(0,false);
                    $dt_radiology_request_item = array(
                        'radiology_request_id'      => $id_radiology_request,
                        'radiology_package_id'      => $radiology_request_items[$i]['radiology_package_id'],
                        'service_class_id'          => $service_class[0]->id,
                        'var_1'                     => $costing_services_class[0]->var_1,
                        'var_2'                     => $costing_services_class[0]->var_2,
                        'var_3'                     => $costing_services_class[0]->var_3,
                        'costing_id'                => $costing[0]->id,
                        'costing_name'              => $costing[0]->name,
                        'date_request'              => date('Y-m-d H:i:s'),
                        'status'                    => 1
                    );
                    $this->Radiology_request_item->create($dt_radiology_request_item);
                } else {
                    $this->REST_Return(404, 'There is no radiology_package with id ' . $radiology_request_items[$i]['radiology_package_id'] . ' or service_class with id ' . $radiology_request_items[$i]['service_class_id']);
                    return;
                }
            }
        }

        // INIT_PHYSIOTHERAPY_REQUEST
        $physiotherapy_request_items = $dt_post['physiotherapy_request_items'];
        if(count($physiotherapy_request_items) > 0){
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

            $physiotherapy_clinical_indications = $dt_post['physiotherapy_clinical_indications'];

            $dt_physiotherapy_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $soap_id,
                'cppt_id'               => $cppt_id,
                'doctor_in_charge_id'   => $user_employee_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'date_request'          => date('Y-m-d H:i:s',time())
            );
            $id_physiotherapy_request = $this->Physiotherapy_request->create($dt_physiotherapy_request);
            // INIT_physiotherapy_REQUEST_ITEM
            for ($i=0; $i < count($physiotherapy_request_items) ; $i++) {
                $physiotherapy_package = $this->Physiotherapy_package->read($physiotherapy_request_items[$i]['physiotherapy_package_id'], false);
                $service_class = $this->Service_class->read($physiotherapy_request_items[$i]['service_class_id']);
                // Check physiotherapy_package_id & service_class_id
                if($physiotherapy_package != [] && $service_class != []) {
                    $costing = $this->Costing->read($physiotherapy_package[0]->costing_id,false);
                    $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                    // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                    $costing_services_class = $this->Costing_services_class->read(0,false);
                    $dt_physiotherapy_request_item = array(
                        'physiotherapy_request_id'      => $id_physiotherapy_request,
                        'physiotherapy_package_id'      => $physiotherapy_request_items[$i]['physiotherapy_package_id'],
                        'service_class_id'          => $service_class[0]->id,
                        'var_1'                     => $costing_services_class[0]->var_1,
                        'var_2'                     => $costing_services_class[0]->var_2,
                        'var_3'                     => $costing_services_class[0]->var_3,
                        'costing_id'                => $costing[0]->id,
                        'costing_name'              => $costing[0]->name,
                        'date_request'              => date('Y-m-d H:i:s'),
                        'status'                    => 1
                    );
                    $this->Physiotherapy_request_item->create($dt_physiotherapy_request_item);
                } else {
                    $this->REST_Return(404, 'There is no physiotherapy_package with id ' . $physiotherapy_request_items[$i]['physiotherapy_package_id'] . ' or service_class with id ' . $physiotherapy_request_items[$i]['service_class_id']);
                    return;
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create cppt', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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
    
        $cppt_id = $id;
    
        $select = "a.id";
        $cppt = $this->$model->select($cppt_id, $select);
    
        if (count($cppt) == 0) {
            $this->REST_Return(404, 'cppt with id ' . $cppt_id . ' does not exist!');
            return;
        }

        $status = $dt_put['status'];
    
        $dt_cppt = array(
            'status'        => $status,
            'date_updated'  => date('Y-m-d H:i:s', time()),
            'user_updated'  => $user_id,
        );

        /* Start Transaction */
        $this->db->trans_start();
    
        $id = $this->$model->update($cppt_id, $dt_cppt);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to verify ccpt', $response_data);
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
    
        $soap_id = $id;
    
        $select = "a.id";
        $where = array('soap_id' => $soap_id);
        $cppt = $this->$model->select(0, $select, $where);
    
        if (count($cppt) == 0) {
            $this->REST_Return(404, 'cppt with soap_id ' . $soap_id . ' does not exist!');
            return;
        }
        
        /* Start Transaction */
        $this->db->trans_start();

        $status = $dt_put['status'];

        for ($i=0; $i < count($cppt); $i++) { 
            $cppt_id = $cppt[$i]->id;
            $dt_cppt = array(
                'status'        => $status,
                'date_updated'  => date('Y-m-d H:i:s', time()),
                'user_updated'  => $user_id,
            );

            $this->$model->update($cppt_id, $dt_cppt);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to verify all ccpt', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);
        $user_employee_id = $user[0]->employee_id;

        $cppt_id = $id;
    
        $select = "a.id, a.soap_id, a.doctor_in_charge_id, a.registration_destination_id";
        $cppt = $this->$model->select($cppt_id, $select);
    
        if (count($cppt) == 0) {
            $this->REST_Return(404, 'cppt with id ' . $cppt_id . ' does not exist!');
            return;
        }

        $soap_id = $cppt[0]->soap_id;
        $doctor_in_charge_id = $cppt[0]->doctor_in_charge_id;
        $registration_destination_id = $cppt[0]->registration_destination_id;

        $instructions = $dt_put['instructions'];
        $subjective = $dt_put['subjective'];
        $assessment = $dt_put['assessment'];
        $objective = $dt_put['objective'];
        $plan = $dt_put['plan'];
        $aware = $dt_put['aware'];
        $eye_score = $dt_put['eye_score'];
        $verbal_score = $dt_put['verbal_score'];
        $motor_score = $dt_put['motor_score'];
        $pupil_score = $dt_put['pupil_score'];
        $verbal_response = $dt_put['verbal_response'];
        $oxygen_type_id = $dt_put['oxygen_type_id'];
        $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
        $classification_blood_pressure_type_id = $dt_put['classification_blood_pressure_type_id'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $oximetry_frequency = $dt_put['oximetry_frequency'];
        $classification_oximetry_type_id = $dt_put['classification_oximetry_type_id'];
        $temperature = $dt_put['temperature'];
        $classification_temperature_type_id = $dt_put['classification_temperature_type_id'];
        $blood_sugar = $dt_put['blood_sugar'];
        $sore_score = $dt_put['sore_score'];
        $ews = $dt_put['ews'];
        $weight = $dt_put['weight'];
        $classification_weight_type_id = $dt_put['classification_weight_type_id'];
        $height = $dt_put['height'];
        $imt = $dt_put['imt'];
        $head_circumference = $dt_put['head_circumference'];
        $chest_circumference = $dt_put['chest_circumference'];
        $arm_circumference = $dt_put['arm_circumference'];
        $stomach_circumference = $dt_put['stomach_circumference'];
        $knee_circumference = $dt_put['knee_circumference'];
    
        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_cppt = array(
            'instructions'                          => $instructions,
            'subjective'                            => $subjective,
            'assessment'                            => $assessment,
            'objective'                             => $objective,
            'plan'                                  => $plan,
            'aware'                                 => $aware,
            'eye_score'                             => $eye_score,
            'verbal_score'                          => $verbal_score,
            'motor_score'                           => $motor_score,
            'pupil_score'                           => $pupil_score,
            'verbal_response'                       => $verbal_response,
            'oxygen_type_id'                        => $oxygen_type_id,
            'systolic_blood_pressure'               => $systolic_blood_pressure,
            'diastolic_blood_pressure'              => $diastolic_blood_pressure,
            'classification_blood_pressure_type_id' => $classification_blood_pressure_type_id,
            'pulse_frequency'                       => $pulse_frequency,
            'breath_frequency'                      => $breath_frequency,
            'oximetry_frequency'                    => $oximetry_frequency,
            'classification_oximetry_type_id'       => $classification_oximetry_type_id,
            'temperature'                           => $temperature,
            'classification_temperature_type_id'    => $classification_temperature_type_id,
            'blood_sugar'                           => $blood_sugar,
            'sore_score'                            => $sore_score,
            'ews'                                   => $ews,
            'weight'                                => $weight,
            'classification_weight_type_id'         => $classification_weight_type_id,
            'height'                                => $height,
            'imt'                                   => $imt,
            'head_circumference'                    => $head_circumference,
            'chest_circumference'                   => $chest_circumference,
            'arm_circumference'                     => $arm_circumference,
            'stomach_circumference'                 => $stomach_circumference,
            'knee_circumference'                    => $knee_circumference,
            'user_updated'                          => $user_id,
            'date_updated'                          => date('Y-m-d H:i:s', time()),
        );
    
        $this->$model->update($cppt_id, $dt_cppt);

        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $user_employee_id,
            'observation_date'              => date('Y-m-d H:i:s'),
            'systolic'                      => $systolic_blood_pressure,
            'diastolic'                     => $diastolic_blood_pressure,
            'pulse'                         => $pulse_frequency,
            'temperature'                   => $temperature,
            'respiratory_rate'              => $breath_frequency,
            'oxygen_saturation'             => $oximetry_frequency,
            'verbal_response'               => $verbal_response,
            'oxygen_type_id'                => $oxygen_type_id,
            'sore_score'                    => $sore_score,
            'ews'                           => $ews,
            'blood_sugar'                   => $blood_sugar,
            'weight'                        => $weight,
            'height'                        => $height,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $this->Vital_signs_monitoring->create($dt_vital_signs_monitoring);

        // INPUT EWS
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'observation_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $early_warning_score = $this->Early_warning_score->select(0, $select, $where);
        if (count($early_warning_score) == 0) {
            $dt_early_warning_score = array(
                'registration_destination_id'   => $registration_destination_id,
                'observation_date'              => date('Y-m-d'),
                'user_added'                    => $user_id,
                'date_added'                    => date('Y-m-d H:i:s',time()),
                'date_updated'                  => date('Y-m-d H:i:s',time()),
            );

            $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
        } else {
            $early_warning_score_id = $early_warning_score[0]->id;
        }

        $observation_time = date('H:i:s');
        $blood_pressure = (float) $systolic_blood_pressure;
        $pulse = (float) $pulse_frequency;
        $breathing = (float) $breath_frequency;
        $temperature = (float) $temperature;
        $oxygen_saturation = (float) $oximetry_frequency;
        $oxygen = $oxygen_type_id == 1 ? 2 : 0;

        $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);

        $dt_early_warning_score_item = array(
            'early_warning_score_id' => $early_warning_score_id,
            'employee_nurse_id'      => $user_employee_id,
            'observation_time'       => $observation_time,
            'aware'                  => $aware,
            'eye_score'              => $eye_score,
            'verbal_score'           => $verbal_score,
            'motor_score'            => $motor_score,
            'pupil_score'            => $pupil_score,
            'verbal_response'        => $verbal_response,
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

        // INPUT TEMPERATURE CHART
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'monitoring_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $temperature_chart = $this->Temperature_chart->select(0, $select, $where);
        if (count($temperature_chart) == 0) {
            $dt_temperature_chart = array(
                'registration_destination_id' => $registration_destination_id,
                'employee_nurse_id'           => $user_employee_id,
                'monitoring_date'             => date('Y-m-d'),
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s',time()),
                'date_updated'                => date('Y-m-d H:i:s',time()),
            );
        
            $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
        } else {
            $temperature_chart_id = $temperature_chart[0]->id;
        }
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => date('H:i:s'),
            'rr'                    => $breath_frequency,
            'pulse'                 => $pulse_frequency,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->Temperature_chart_item->create($dt_temperature_chart_item);

        // SOAP_A_ACTION
        $icd10_arr = $dt_put['icd10'];
        if(isset($icd10_arr)) {
            $select = 'a.id';
            $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id);
            $soap_a = $this->Soap_a->select(0, $select, $where);
            if (count($soap_a) == 0) {
                $dt_soap_a = array(
                    'soap_id'               => $soap_id,
                    'cppt_id'               => $cppt_id,
                    'date_added'            => date("Y-m-d H:i:s", time())
                );
                $soap_a_id = $this->Soap_a->create($dt_soap_a);
            } else {
                $soap_a_id = $soap_a[0]->id;
            }

            $select = "a.id, icd10_id";
            $where = array('soap_a_id' => $soap_a_id);
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where);

            $tmp_delete_icd10_arr = [];

            // adding all current items (db) to temporary variable
            for ($i=0; $i < count($soap_a_action); $i++) { 
                $icd10_id = $soap_a_action[$i]->icd10_id;
                $soap_a_action_id = $soap_a_action[$i]->id;

                $tmp_delete_icd10_arr[$icd10_id] = $soap_a_action_id;
            }
            
            for ($i=0; $i < count($icd10_arr) ; $i++) { 
                $icd10_id = $icd10_arr[$i]['icd10_id'];

                $icd10 = $this->Icd10->select_id($icd10_id);
                if($icd10 == "") {
                    $this->REST_Return(404, 'There is no icd10 with id ' . $icd10_id);
                    return;
                }

                $dt_soap_a_action = array(
                    'soap_a_id'         => $soap_a_id,
                    'icd10_id'          => $icd10_id,
                    'index'             => $i,
                    'is_delete'         => 0,
                    'user_added'        => $user_id,
                    'user_updated'      => $user_id,
                );

                // create or update items according to the items provided and unset used items from temporary variable
                if(array_key_exists($icd10_id,$tmp_delete_icd10_arr) == 1) {
                    $soap_a_action_id = $tmp_delete_icd10_arr[$icd10_id];

                    $dt_soap_a_action['id'] = $soap_a_action_id;
                    $this->Soap_a_action->replace($dt_soap_a_action);

                    unset($tmp_delete_icd10_arr[$icd10_id]);
                } else {
                    $this->Soap_a_action->create($dt_soap_a_action);
                }
            }

            // delete all items in temporary variable
            foreach ($tmp_delete_icd10_arr as $icd10_id => $soap_a_action_id) {
                $dt_soap_a_action = array(
                    'id'                => $soap_a_action_id,
                    'soap_a_id'         => $soap_a_id,
                    'icd10_id'          => $icd10_id,
                    'is_delete'         => 1,
                    'user_updated'      => $user_id,
                    'user_deleted'      => $user_id,
                );
                $this->Soap_a_action->replace($dt_soap_a_action);
            }
        }

        // LABORATORY_REQUEST
        if(isset($dt_put['laboratory_request_items'])) {
            $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
            $laboratory_request_id = $this->Laboratory_request->select_id(0, $where);

            // Create parent lab
            if($laboratory_request_id == "" && count($dt_put['laboratory_request_items']) > 0) {
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

                $laboratory_clinical_indications = $dt_put['laboratory_clinical_indications'];

                $dt_laboratory_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $soap_id,
                    'cppt_id'               => $cppt_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'clinical_indications'  => $laboratory_clinical_indications,
                    'date_request'          => date('Y-m-d H:i:s',time())
                );
                $id_laboratory_request = $this->Laboratory_request->create($dt_laboratory_request);

                $laboratory_request_id = $this->Laboratory_request->select_id($id_laboratory_request);
            } 
            
            if (count($dt_put['laboratory_request_items']) == 0) {
                $select = 'a.id, a.no_request, a.status';
                $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
                $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

                if (count($laboratory_request) > 0) {
                    $lab_no_request = $laboratory_request[0]->no_request;
                    $lab_status = $laboratory_request[0]->status;

                    if ($lab_status != 1) {
                        $this->REST_Return(409, 'laboratory request with no ' . $lab_no_request . ' is already processed');
                        return;
                    }
                }

                $dt_laboratory_request = array(
                    'is_delete' => 1
                );

                $this->Laboratory_request->update($laboratory_request_id, $dt_laboratory_request);
            }

            if ($laboratory_request_id != "") {
                $this->Laboratory_request_item->whereFields['laboratory_request_id'] = $laboratory_request_id;
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
                        if(is_exist('Laboratory_package', $package_id) == []) {
                            $this->REST_Return(404, 'There is no laboratory_package with id ' . $package_id);
                            return;
                        }

                        if(is_exist('Service_class', $service_class_id) == []) {
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
                            'laboratory_request_id'     => $laboratory_request_id,
                            'laboratory_package_id'     => $package_id,
                            'service_class_id'          => $service_class[0]->id,
                            'var_1'                     => $costing_services_class[0]->var_1,
                            'var_2'                     => $costing_services_class[0]->var_2,
                            'var_3'                     => $costing_services_class[0]->var_3,
                            'costing_id'                => $costing[0]->id,
                            'costing_name'              => $costing[0]->name,
                            'date_request'              => date('Y-m-d H:i:s'),
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
                                'laboratory_request_id'     => $laboratory_request_id,
                                'laboratory_package_id'     => $package_id,
                                'is_delete'                 => 1,
                            );
                            $this->Laboratory_request_item->replace($dt_laboratory_request_item);
                        }
                    }
                }
            }
        }

        // RADIOLOGY_REQUEST
        if(isset($dt_put['radiology_request_items'])) {
            $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
            $radiology_request_id = $this->Radiology_request->select_id(0, $where);

            // Create parent rad
            if($radiology_request_id == "" && count($dt_put['radiology_request_items']) > 0) {
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

                $radiology_clinical_indications = $dt_put['radiology_clinical_indications'];

                $dt_radiology_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $soap_id,
                    'cppt_id'               => $cppt_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'clinical_indications'  => $radiology_clinical_indications,
                    'date_request'          => date('Y-m-d H:i:s',time())
                );
                $id_radiology_request = $this->Radiology_request->create($dt_radiology_request);

                $radiology_request_id = $this->Radiology_request->select_id($id_radiology_request,false);
            }

            if (count($dt_put['radiology_request_items']) == 0) {
                $select = 'a.id, a.no_request, a.status';
                $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
                $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

                if (count($laboratory_request) > 0) {
                    $lab_no_request = $laboratory_request[0]->no_request;
                    $lab_status = $laboratory_request[0]->status;

                    if ($lab_status != 1) {
                        $this->REST_Return(409, 'laboratory request with no ' . $lab_no_request . ' is already processed');
                        return;
                    }
                }

                $dt_radiology_request = array(
                    'is_delete' => 1
                );

                $this->Radiology_request->update($radiology_request_id, $dt_radiology_request);
            }

            if ($radiology_request_id != "") {
                $this->Radiology_request_item->whereFields['radiology_request_id'] = $radiology_request_id;
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
                        if(is_exist('Radiology_package', $package_id) == []) {
                            $this->REST_Return(404, 'There is no radiology_package with id ' . $package_id);
                            return;
                        }

                        if(is_exist('Service_class', $service_class_id) == []) {
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
                            'radiology_request_id'      => $radiology_request_id,
                            'radiology_package_id'      => $package_id,
                            'service_class_id'          => $service_class[0]->id,
                            'var_1'                     => $costing_services_class[0]->var_1,
                            'var_2'                     => $costing_services_class[0]->var_2,
                            'var_3'                     => $costing_services_class[0]->var_3,
                            'costing_id'                => $costing[0]->id,
                            'costing_name'              => $costing[0]->name,
                            'date_request'              => date('Y-m-d H:i:s'),
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
                                'radiology_request_id'      => $radiology_request_id,
                                'radiology_package_id'      => $package_id,
                                'is_delete'                 => 1,
                            );
                            $this->Radiology_request_item->replace($dt_radiology_request_item);
                        }
                    }
                }
            }
        }

        // PHYSIOTHERAPY_REQUEST
        if(isset($dt_put['physiotherapy_request_items'])) {
            $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
            $physiotherapy_request_id = $this->Physiotherapy_request->select_id(0, $where);

            // Create parent rad
            if($physiotherapy_request_id == "" && count($dt_put['physiotherapy_request_items']) > 0) {
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

                $dt_physiotherapy_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $soap_id,
                    'cppt_id'               => $cppt_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'date_request'          => date('Y-m-d H:i:s',time())
                );
                $id_physiotherapy_request = $this->Physiotherapy_request->create($dt_physiotherapy_request);

                $physiotherapy_request_id = $this->Physiotherapy_request->select_id($id_physiotherapy_request,false);
            }

            if (count($dt_put['physiotherapy_request_items']) == 0) {
                $select = 'a.id, a.no_request, a.status';
                $where = array('soap_id' => $soap_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
                $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

                if (count($laboratory_request) > 0) {
                    $lab_no_request = $laboratory_request[0]->no_request;
                    $lab_status = $laboratory_request[0]->status;

                    if ($lab_status != 1) {
                        $this->REST_Return(409, 'laboratory request with no ' . $lab_no_request . ' is already processed');
                        return;
                    }
                }

                $dt_physiotherapy_request = array(
                    'is_delete' => 1
                );

                $this->Physiotherapy_request->update($physiotherapy_request_id, $dt_physiotherapy_request);
            }

            if ($physiotherapy_request_id != "") {
                $this->Physiotherapy_request_item->whereFields['physiotherapy_request_id'] = $physiotherapy_request_id;
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
                                'physiotherapy_request_id'      => $physiotherapy_request_item[$i]->physiotherapy_request_id,
                                'physiotherapy_package_id'      => $physiotherapy_request_item[$i]->physiotherapy_package_id,
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
                        if(is_exist('Physiotherapy_package', $package_id) == []) {
                            $this->REST_Return(404, 'There is no physiotherapy_package with id ' . $package_id);
                            return;
                        }

                        if(is_exist('Service_class', $service_class_id) == []) {
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
                            'physiotherapy_request_id'      => $physiotherapy_request_id,
                            'physiotherapy_package_id'      => $package_id,
                            'service_class_id'          => $service_class[0]->id,
                            'var_1'                     => $costing_services_class[0]->var_1,
                            'var_2'                     => $costing_services_class[0]->var_2,
                            'var_3'                     => $costing_services_class[0]->var_3,
                            'costing_id'                => $costing[0]->id,
                            'costing_name'              => $costing[0]->name,
                            'date_request'              => date('Y-m-d H:i:s'),
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
                                'physiotherapy_request_id'      => $physiotherapy_request_id,
                                'physiotherapy_package_id'      => $package_id,
                                'is_delete'                 => 1,
                            );
                            $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
                        }
                    }
                }
            }
        }

        // PRESCRIPTION_ITEM
        if(isset($dt_put['form_prescription'])) {
            $select = 'a.id';
            $where = array('soap_id' => $soap_id);
            $soap_p = $this->Soap_p->select(0, $select, $where);
            $soap_p_id = $soap_p[0]->id;

            $this->Prescription->whereFields['a.soap_p_id'] = $soap_p_id;
            $this->Prescription->whereFields['a.cppt_id'] = $cppt_id;
            $this->Prescription->whereFields['a.is_delete'] = 0;
            $pharmacy_prescription = $this->Prescription->read(0,false);
            if(count($pharmacy_prescription) == 0) {
                $registration_destination_id = $dt_put['registration_destination_id'];

                $this->Registration->limit = 1;
                $this->Registration->whereFields['b.id'] = $registration_destination_id;
                $dt_reg = $this->Registration->read(0,false)[0];
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
                    'norm'                      => $dt_reg->norm,
                    'institute_branch_id'       => $institute_branch_id,
                    'registration_destination_id' => $registration_destination_id,
                    'pharmacy_depo_id'          => $dt_put['pharmacy_depo_id'],
                    'type_prescription_id'      => $dt_put['form_prescription_parent'][0]['type_prescription_id'],
                    'soap_p_id'                 => $soap_p_id,
                    'cppt_id'                   => $cppt_id,
                    'health_facility_item_id'   => $dt_reg->health_facility_item_id,
                    'doctor_in_charge_id'       => $doctor_in_charge_id,
                    'no_order'                  => $no_order,
                    'date_added'                => date("Y-m-d H:i:s",time()),
                    'date_0'                    => date("Y-m-d H:i:s",time()),
                    'flag'                      => 0,
                    'queue'                     => $queue,
                );
                $pharmacy_prescription_id = $this->Prescription->create($dt_prescription);
            } else {
                $pharmacy_prescription_id = $pharmacy_prescription[0]->id;
                $pharmacy_prescription_flag = $pharmacy_prescription[0]->flag;
                $pharmacy_prescription_no_order = $pharmacy_prescription[0]->no_order;

                if ($pharmacy_prescription_flag != 0) {
                    $this->REST_Return(409, 'prescription with no_order ' . $pharmacy_prescription_no_order . ' is already processed');
                    return;
                }
            }

            if (count($dt_put['form_prescription']) == 0) {
                $select = 'a.id, a.no_order, a.flag';
                $where = array('soap_p_id' => $soap_p_id, 'cppt_id' => $cppt_id, 'a.is_delete' => 0);
                $prescription = $this->Prescription->select(0, $select, $where);

                if (count($prescription) > 0) {
                    $prescription_no_order = $prescription[0]->no_order;
                    $prescription_flag = $prescription[0]->flag;

                    if ($prescription_flag != 0) {
                        $this->REST_Return(409, 'prescription order with no ' . $prescription_no_order . ' is already processed');
                        return;
                    }
                }

                $dt_prescription = array(
                    'is_delete' => 1
                );

                $this->Prescription->whereFields = array();
                $this->Prescription->update($pharmacy_prescription_id, $dt_prescription);
            } 

            $pharmacy_depo_id = $dt_put['pharmacy_depo_id'];
            $type_prescription_id = $dt_put['type_prescription_id'];

            $dt_prescription = array(
                'pharmacy_depo_id' => ($pharmacy_depo_id == null) ? '297c44f0-12ef-40e6-b9a7-c831065b3372' : $pharmacy_depo_id,
                'type_prescription_id' => ($type_prescription_id == null) ? 1 : $type_prescription_id,
            );

            $this->Prescription->whereFields = array();
            $this->Prescription->update($pharmacy_prescription_id, $dt_prescription);

            $this->Prescription_item->whereFields['pharmacy_prescription_id'] = $pharmacy_prescription_id;
            $pharmacy_prescription_item = $this->Prescription_item->read(0,false);
            $package_arr = $dt_put['form_prescription'];
            $tmp_delete_package_arr = array();
            // adding all current items (db) to temporary variable
            for ($i=0; $i < count($pharmacy_prescription_item); $i++) { 
                $tmp_delete_package_arr[$pharmacy_prescription_item[$i]->package_id] = $pharmacy_prescription_item[$i]->pharmacy_catalog_id;
            }
            // delete all items if no item provided
            if(count($dt_put['form_prescription']) == 0) {
                for ($i=0; $i < count($pharmacy_prescription_item); $i++) { 
                    $dt_pharmacy_prescription_item = array(
                        'pharmacy_prescription_id'  => $pharmacy_prescription_item[$i]->pharmacy_prescription_id,
                        'pharmacy_catalog_id'       => $pharmacy_prescription_item[$i]->pharmacy_catalog_id,
                        'is_delete'                 => 1,
                    );
                    $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                }
            }
            for ($i=0; $i < count($dt_put['form_prescription']); $i++) { 
                for ($i=0; $i < count($package_arr) ; $i++) { 
                    $package_id = $package_arr[$i]['package_id'];
                    if(is_exist('Package', $package_id) == []) {
                        $this->REST_Return(404, 'There is no package with id ' . $package_id);
                        return;
                    }

                    $this->Package->whereFields['a.id'] = $package_id;
                    $package = $this->Package->read(0,false);

                    if (isset($dt_put['form_prescription_parent'])) {
                        for ($ip=0; $ip < count($dt_put['form_prescription_parent']); $ip++) { 
                            if ($dt_put['form_prescription_parent'][$ip]['id'] == $dt_put['form_prescription'][$i]['prescription_type_id']) {
                                $dt_pharmacy_prescription_item = array(
                                    'pharmacy_catalog_id'           => $package_arr[$i]['pharmacy_catalog_id'],
                                    'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
                                    'prescription_item_type_used'   => $dt_put['form_prescription_parent'][$ip]['used'],
                                    'prescription_item_type_value'  => $dt_put['form_prescription_parent'][$ip]['value'],
                                    'package_id'                    => $package_id,
                                    'package_name'                  => $package[0]->name,
                                    'dose'                          => isset($dt_put['form_prescription'][$i]['dose']) ? $dt_put['form_prescription'][$i]['dose'] : '',
                                    'dose_doctor'                   => isset($dt_put['form_prescription'][$i]['sum_dose']) ? intval($dt_put['form_prescription'][$i]['sum_dose']) : '',
                                    'qty_doctor'                    => isset($dt_put['form_prescription'][$i]['value_package']) ? $dt_put['form_prescription'][$i]['value_package'] : '',
                                    'qty'                           => isset($dt_put['form_prescription'][$i]['value_package']) ? $dt_put['form_prescription'][$i]['value_package'] : '',
                                    'qty_copy'                      => isset($dt_put['form_prescription'][$i]['qty_copy']) ? $dt_put['form_prescription'][$i]['qty_copy'] : '',
                                    'sort'                          => isset($dt_put['form_prescription'][$i]['sort']) ? $dt_put['form_prescription'][$i]['sort'] : '',
                                    'used'                          => isset($dt_put['form_prescription'][$i]['used']) ? $dt_put['form_prescription'][$i]['used'] : '',
                                    'desc'                          => isset($dt_put['form_prescription'][$i]['desc']) ? $dt_put['form_prescription'][$i]['desc'] : '',
                                    'is_delete'                     => 0,
                                );
                            }
                        }
                    }
                    
                    // create or update items according to the items provided and unset used items from temporary variable
                    if(array_key_exists($package_id,$tmp_delete_package_arr) == 1) {
                        $dt_pharmacy_prescription_item['pharmacy_prescription_id'] = $pharmacy_prescription_id;
                        $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                        
                        unset($tmp_delete_package_arr[$package_id]);
                    } else {
                        $dt_pharmacy_prescription_item['pharmacy_prescription_id'] = $pharmacy_prescription_id;
                        $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                    }

                }
            
                // delete all items in temporary variable
                foreach ($tmp_delete_package_arr as $package_id => $pharmacy_catalog_id) {
                    $dt_pharmacy_prescription_item = array(
                        'pharmacy_prescription_id'  => $pharmacy_prescription[0]->id,
                        'pharmacy_catalog_id'       => $pharmacy_catalog_id,
                        'package_id'                => $package_id,
                        'is_delete'                 => 1,
                    );
                    $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                }
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update cppt', $response_data);
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
    
        $cppt_id = $this->$model->select_id($id);
    
        if ($cppt_id == "") {
            $this->REST_Return(404, 'there is no cppt with id ' . $id);
            return;
        }

        $select = 'a.id, a.status';
        $where = array('a.cppt_id' => $cppt_id);
        $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

        for ($i=0; $i < count($laboratory_request); $i++) { 
            $status = $laboratory_request[$i]->status;
            if ($status != 1) {
                $this->REST_Return(409, 'cannot delete cppt, there is a request that has been processed');
                return;
            }
        }

        $select = 'a.id, a.status';
        $where = array('a.cppt_id' => $cppt_id);
        $radiology_request = $this->Radiology_request->select(0, $select, $where);

        for ($i=0; $i < count($radiology_request); $i++) { 
            $status = $radiology_request[$i]->status;
            if ($status != 1) {
                $this->REST_Return(409, 'cannot delete cppt, there is a request that has been processed');
                return;
            }
        }

        $select = 'a.id, a.flag';
        $where = array('a.cppt_id' => $cppt_id);
        $prescription = $this->Prescription->select(0, $select, $where);

        for ($i=0; $i < count($prescription); $i++) { 
            $flag = $prescription[$i]->flag;
            if ($flag != 0) {
                $this->REST_Return(409, 'cannot delete cppt, there is a request that has been processed');
                return;
            }
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_cppt = array(
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($cppt_id, $dt_cppt);

        // DELETE LABORATORY REQUEST
        for ($i=0; $i < count($laboratory_request); $i++) { 
            $laboratory_request_id = $laboratory_request[$i]->id;
            $dt_laboratory_request = array(
                'is_delete' => $is_delete,
            );
            $this->Laboratory_request->update($laboratory_request_id, $dt_laboratory_request);

            $this->Laboratory_request_item->whereFields['laboratory_request_id'] = $laboratory_request_id;
            $this->Laboratory_request_item->update(0, $dt_laboratory_request);
        }

        // DELETE RADIOLOGY REQUEST
        for ($i=0; $i < count($radiology_request); $i++) { 
            $radiology_request_id = $radiology_request[$i]->id;
            $dt_radiology_request = array(
                'is_delete' => $is_delete,
            );
            $this->Radiology_request->update($radiology_request_id, $dt_radiology_request);

            $this->Radiology_request_item->whereFields['radiology_request_id'] = $radiology_request_id;
            $this->Radiology_request_item->update(0, $dt_radiology_request);
        }

        // DELETE PRESCRIPTION ORDER
        for ($i=0; $i < count($prescription); $i++) { 
            $prescription_id = $prescription[$i]->id;
            $dt_prescription = array(
                'is_delete' => $is_delete,
            );
            $this->Prescription->update($prescription_id, $dt_prescription);

            $this->Prescription_item->whereFields['pharmacy_prescription_id'] = $prescription_id;
            $this->Prescription_item->update(0, $dt_prescription);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete cppt', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_anamnesis_put() {
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
        $this->form_validation->set_rules($this->save_anamnesis_rules);
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);
            
            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        $subjective = $dt_put['subjective'];
        $nursing_objective = $dt_put['nursing_objective'];

        $dt_cppt = array(
            'subjective'        => $subjective,
            'nursing_objective' => $nursing_objective,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s'),
        );

        $st_cppt = $this->$model->update_v2($cppt_id, $dt_cppt);
        if ($st_cppt < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save anamnesis', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_ttv_put() {
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
        $this->form_validation->set_rules($this->save_ttv_rules);
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

        $select = 'a.id, r.norm, p.birth_date, a.health_facility_item_id';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('patient p', 'r.norm = p.norm', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $birth_date = $registration_destination[0]->birth_date;
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $oximetry_frequency = $dt_put['oximetry_frequency'];
        $temperature = $dt_put['temperature'];
        $aware = $dt_put['aware'];
        $oxygen_type_id = $dt_put['oxygen_type_id'];
        $eye_score = $dt_put['eye_score'];
        $verbal_score = $dt_put['verbal_score'];
        $motor_score = $dt_put['motor_score'];
        $skin_color = $dt_put['skin_color'];
        $behavior = $dt_put['behavior'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $imt = $dt_put['imt'];
        
        $dt_ttv = array(
            'systolic_blood_pressure'  => $systolic_blood_pressure,
            'diastolic_blood_pressure' => $diastolic_blood_pressure,
            'pulse_frequency'          => $pulse_frequency,
            'breath_frequency'         => $breath_frequency,
            'oximetry_frequency'       => $oximetry_frequency,
            'temperature'              => $temperature,
            'aware'                    => $aware,
            'oxygen_type_id'           => $oxygen_type_id,
            'eye_score'                => $eye_score,
            'verbal_score'             => $verbal_score,
            'motor_score'              => $motor_score,
            'skin_color'               => $skin_color,
            'behavior'                 => $behavior,
            'weight'                   => $weight,
            'height'                   => $height,
            'imt'                      => $imt,
            'user_updated'             => $user_id,
            'date_updated'             => date('Y-m-d H:i:s'),
        );

        $st_ttv = $this->$model->update_v2($cppt_id, $dt_ttv);
        if ($st_ttv < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $user_employee_id,
            'source_id'                     => $cppt_id,
            'observation_date'              => date('Y-m-d H:i:s'),
            'systolic'                      => $systolic_blood_pressure,
            'diastolic'                     => $diastolic_blood_pressure,
            'pulse'                         => $pulse_frequency,
            'temperature'                   => $temperature,
            'respiratory_rate'              => $breath_frequency,
            'oxygen_saturation'             => $oximetry_frequency,
            'oxygen_type_id'                => $oxygen_type_id,
            'weight'                        => $weight,
            'height'                        => $height,
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'skin_color'                    => $skin_color,
            'behavior'                      => $behavior,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.source_id' => $cppt_id,
            'a.is_delete' => 0,
        );
        $vsm = $this->Vital_signs_monitoring->select(0, $select, $where);
        if (count($vsm) == 0) {
            $this->Vital_signs_monitoring->create($dt_vital_signs_monitoring);
        } else {
            $vsm_id = $vsm[0]->id;
            unset($dt_vital_signs_monitoring['date_added']);
            $this->Vital_signs_monitoring->update_v2($vsm_id, $dt_vital_signs_monitoring);
        }

        $age = count_age($birth_date);
        if ($age > 18) {
            // INPUT EWS
            $select = 'a.id';
            $where = array(
                'registration_destination_id' => $registration_destination_id, 
                'observation_date' => date('Y-m-d'), 
                'is_delete' => 0
            );
            $early_warning_score = $this->Early_warning_score->select(0, $select, $where);
    
            if (count($early_warning_score) == 0) {
                $dt_early_warning_score = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'observation_date'              => date('Y-m-d'),
                    'user_added'                    => $user_id,
                    'date_added'                    => date('Y-m-d H:i:s',time()),
                    'date_updated'                  => date('Y-m-d H:i:s',time()),
                );
    
                $early_warning_score_id = $this->Early_warning_score->create($dt_early_warning_score);
            } else {
                $early_warning_score_id = $early_warning_score[0]->id;
            }
    
            $observation_time = date('H:i:s');
            $blood_pressure = (float) $systolic_blood_pressure;
            $pulse = (float) $pulse_frequency;
            $breathing = (float) $breath_frequency;
            $temperature = (float) $temperature;
            $oxygen_saturation = (float) $oximetry_frequency;
            $oxygen = $oxygen_type_id != 2 ? 2 : 0;
    
            $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
        
            $dt_early_warning_score_item = array(
                'early_warning_score_id' => $early_warning_score_id,
                'employee_nurse_id'      => $user_employee_id,
                'source_id'              => $cppt_id,
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
    
            $select = 'a.id';
            $where = array(
                'a.early_warning_score_id' => $early_warning_score_id,
                'a.source_id' => $cppt_id,
                'a.is_delete' => 0,
            );
            $ewsi = $this->Early_warning_score_item->select(0, $select, $where);
            if (count($ewsi) == 0) {
                $this->Early_warning_score_item->create($dt_early_warning_score_item);
            } else {
                $ewsi_id = $ewsi[0]->id;
                unset($dt_early_warning_score_item['user_added']);
                unset($dt_early_warning_score_item['date_added']);
                $this->Early_warning_score_item->update_v2($ewsi_id, $dt_early_warning_score_item);
            }
        } else {
            // INPUT PEWS
            $observation_date = date('Y-m-d');
            $observation_time = date('H:i:s');
            $breathing = (float) $breath_frequency;
            $oxygen = (int) $oxygen_type_id;
            $oxygen_saturation = (float) $oximetry_frequency;
            $pulse = (float) $pulse_frequency;
            $skin_color = (int) $skin_color;
            $systolic = (float) $systolic_blood_pressure;
            $behavior = (int) $behavior;

            $data_pews = array(
                'birth_date'        => $birth_date,
                'source_id'         => $cppt_id,
                'employee_nurse_id' => $user_employee_id,
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

            $this->Pews->source_create($registration_destination_id, $observation_date, $data_pews);
        }

        // INPUT TEMPERATURE CHART
        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id, 
            'monitoring_date' => date('Y-m-d'), 
            'is_delete' => 0
        );
        $temperature_chart = $this->Temperature_chart->select(0, $select, $where);
        if (count($temperature_chart) == 0) {
            $dt_temperature_chart = array(
                'registration_destination_id' => $registration_destination_id,
                'employee_nurse_id'           => $user_employee_id,
                'monitoring_date'             => date('Y-m-d'),
                'user_added'                  => $user_id,
                'date_added'                  => date('Y-m-d H:i:s',time()),
                'date_updated'                => date('Y-m-d H:i:s',time()),
            );
        
            $temperature_chart_id = $this->Temperature_chart->create($dt_temperature_chart);
        } else {
            $temperature_chart_id = $temperature_chart[0]->id;
        }
    
        $dt_temperature_chart_item = array(
            'temperature_chart_id'  => $temperature_chart_id,
            'observation_hours'     => date('H:i:s'),
            'source_id'             => $cppt_id,
            'rr'                    => $breath_frequency,
            'pulse'                 => $pulse_frequency,
            'temperature'           => $temperature,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s',time()),
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $select = 'a.id';
        $where = array(
            'a.temperature_chart_id' => $temperature_chart_id,
            'a.source_id' => $cppt_id,
            'a.is_delete' => 0,
        );
        $tci = $this->Temperature_chart_item->select(0, $select, $where);
        if (count($tci) == 0) {
            $this->Temperature_chart_item->create($dt_temperature_chart_item);
        } else {
            $tci_id = $tci[0]->id;
            unset($dt_temperature_chart_item['user_added']);
            unset($dt_temperature_chart_item['date_added']);
            $this->Temperature_chart_item->update_v2($tci_id, $dt_temperature_chart_item);
        }

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_ttv,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save ttv', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_st_generalist_put() {
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
        $this->form_validation->set_rules($this->save_st_generalist_rules);
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        $head = $dt_put['head'];
        $head_hair_color = $dt_put['head_hair_color'];
        $head_hair_condition = $dt_put['head_hair_condition'];
        $eye_conjunctive = $dt_put['eye_conjunctive'];
        $eye_sclera = $dt_put['eye_sclera'];
        $eye_pupil = $dt_put['eye_pupil'];
        $eye_light_reflex = $dt_put['eye_light_reflex'];
        $mouth_mucosa = $dt_put['mouth_mucosa'];
        $mouth_discoloration = $dt_put['mouth_discoloration'];
        $mouth_tongue = $dt_put['mouth_tongue'];
        $mouth_caries = $dt_put['mouth_caries'];
        $neck_kgb = $dt_put['neck_kgb'];
        $neck_trachea_position = $dt_put['neck_trachea_position'];
        $neck_massa = $dt_put['neck_massa'];
        $neck_jvp = $dt_put['neck_jvp'];
        $thorax_insp_chest = $dt_put['thorax_insp_chest'];
        $thorax_insp_retraction = $dt_put['thorax_insp_retraction'];
        $thorax_palp_chest = $dt_put['thorax_palp_chest'];
        $thorax_palp_fremitus = $dt_put['thorax_palp_fremitus'];
        $thorax_palp_crepitation = $dt_put['thorax_palp_crepitation'];
        $thorax_ausk_pul_vasiculer = $dt_put['thorax_ausk_pul_vasiculer'];
        $thorax_ausk_pul_rhonki = $dt_put['thorax_ausk_pul_rhonki'];
        $thorax_ausk_pul_wheezing = $dt_put['thorax_ausk_pul_wheezing'];
        $thorax_ausk_cor_heart_sound = $dt_put['thorax_ausk_cor_heart_sound'];
        $thorax_ausk_cor_murmur = $dt_put['thorax_ausk_cor_murmur'];
        $thorax_ausk_cor_gallop = $dt_put['thorax_ausk_cor_gallop'];
        $abdomen_supel = $dt_put['abdomen_supel'];
        $abdomen_bowel_sounds = $dt_put['abdomen_bowel_sounds'];
        $abdomen_tenderness = $dt_put['abdomen_tenderness'];
        $abdomen_hepar_lien = $dt_put['abdomen_hepar_lien'];
        $abdomen_other_physical_check = $dt_put['abdomen_other_physical_check'];
        $extremity_acral = $dt_put['extremity_acral'];
        $extremity_crt = $dt_put['extremity_crt'];
        $extremity_edema = $dt_put['extremity_edema'];
        $genitalia = $dt_put['genitalia'];
        $localist_status = $dt_put['localist_status'];
        $ophthal_status = $dt_put['ophthal_status'];
        $neurologist_status = $dt_put['neurologist_status'];

        $dt_cppt = array(
            'head'                         => $head,
            'head_hair_color'              => $head_hair_color,
            'head_hair_condition'          => $head_hair_condition,
            'eye_conjunctive'              => $eye_conjunctive,
            'eye_sclera'                   => $eye_sclera,
            'eye_pupil'                    => $eye_pupil,
            'eye_light_reflex'             => $eye_light_reflex,
            'mouth_mucosa'                 => $mouth_mucosa,
            'mouth_discoloration'          => $mouth_discoloration,
            'mouth_tongue'                 => $mouth_tongue,
            'mouth_caries'                 => $mouth_caries,
            'neck_kgb'                     => $neck_kgb,
            'neck_trachea_position'        => $neck_trachea_position,
            'neck_massa'                   => $neck_massa,
            'neck_jvp'                     => $neck_jvp,
            'thorax_insp_chest'            => $thorax_insp_chest,
            'thorax_insp_retraction'       => $thorax_insp_retraction,
            'thorax_palp_chest'            => $thorax_palp_chest,
            'thorax_palp_fremitus'         => $thorax_palp_fremitus,
            'thorax_palp_crepitation'      => $thorax_palp_crepitation,
            'thorax_ausk_pul_vasiculer'    => $thorax_ausk_pul_vasiculer,
            'thorax_ausk_pul_rhonki'       => $thorax_ausk_pul_rhonki,
            'thorax_ausk_pul_wheezing'     => $thorax_ausk_pul_wheezing,
            'thorax_ausk_cor_heart_sound'  => $thorax_ausk_cor_heart_sound,
            'thorax_ausk_cor_murmur'       => $thorax_ausk_cor_murmur,
            'thorax_ausk_cor_gallop'       => $thorax_ausk_cor_gallop,
            'abdomen_supel'                => $abdomen_supel,
            'abdomen_bowel_sounds'         => $abdomen_bowel_sounds,
            'abdomen_tenderness'           => $abdomen_tenderness,
            'abdomen_hepar_lien'           => $abdomen_hepar_lien,
            'abdomen_other_physical_check' => $abdomen_other_physical_check,
            'extremity_acral'              => $extremity_acral,
            'extremity_crt'                => $extremity_crt,
            'extremity_edema'              => $extremity_edema,
            'genitalia'                    => $genitalia,
            'localist_status'              => $localist_status,
            'ophthal_status'               => $ophthal_status,
            'neurologist_status'           => $neurologist_status,
            'user_updated'                 => $user_id,
            'date_updated'                 => date('Y-m-d H:i:s'),
        );

        $st_cppt = $this->$model->update_v2($cppt_id, $dt_cppt);
        if ($st_cppt < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save st generalist', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_diagnose_put() {
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
        $this->form_validation->set_rules($this->save_diagnose_rules);
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        // SOAP_A
        $icd10_notes = $dt_put['icd10_notes'] ?? null;
        $non_icd10 = isset($dt_put['non_icd10']) ? implode('|', $dt_put['non_icd10']) : '';
        $nursing_diagnose = $dt_put['nursing_diagnose'] ?? null;

        $dt_soap_a = array(
            'soap_id'       => $soap_id,
            'cppt_id'       => $cppt_id,
            'icd10_notes'   => $icd10_notes,
            'non_icd10'     => $non_icd10,
            'nursing_diagnose' => $nursing_diagnose,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'cppt_id' => $cppt_id,
        );
        $soap_a = $this->Soap_a->select(0, $select, $where);
        if (count($soap_a) == 0) {
            $dt_soap_a['date_added'] = date('Y-m-d H:i:s');
            $soap_a_id = $this->Soap_a->create($dt_soap_a);
        } else {
            $soap_a_id = $soap_a[0]->id;
            $st_soap_a = $this->Soap_a->update_v2($soap_a_id, $dt_soap_a);
            if ($st_soap_a < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_A_ACTION
        $icd10_arr = $dt_put['icd10'];

        $dt_soap_a_action_delete = array(
            'date_deleted' => date('Y-m-d H:i:s'),
            'user_deleted' => $user_id,
            'is_delete' => 1,
        );
        $where_delete = array(
            'soap_a_id' => $soap_a_id,
            'is_delete' => 0
        );
        $st_soap_a_action = $this->Soap_a_action->update_v2(0, $dt_soap_a_action_delete, $where_delete);
        if ($st_soap_a_action < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        for ($i=0; $i < count($icd10_arr); $i++) { 
            $icd10_id = $icd10_arr[$i]['icd10_id'];

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);
            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_soap_a_action = array(
                'soap_a_id' => $soap_a_id,
                'icd10_id'  => $icd10_id,
                'index'     => $i,
                'user_deleted' => '',
                'date_deleted' => '',
                'is_delete' => 0,
            );
   
            $select = 'a.id';
            $where = array(
                'a.soap_a_id' => $soap_a_id,
                'a.icd10_id' => $icd10_id,
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where);
            if (count($soap_a_action) == 0) {
                $this->Soap_a_action->create($dt_soap_a_action);
            } else {
                $soap_a_action_id = $soap_a_action[0]->id;
                $st_soap_a_action = $this->Soap_a_action->update_v2($soap_a_action_id, $dt_soap_a_action);
                if ($st_soap_a_action < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            }
        }

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save diagnose', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_procedure_put() {
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
        $this->form_validation->set_rules($this->save_procedure_rules);
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
        $advice = $dt_put['advice'];

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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'advice' => $advice,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }

            unset($dt_cppt['user_added']);
            unset($dt_cppt['date_added']);
            $st_cppt = $this->$model->update_v2($cppt_id, $dt_cppt);
            if ($st_cppt < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_P
        $therapy_plan = $dt_put['therapy_plan'];

        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'cppt_id'               => $cppt_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'therapy_plan'          => $therapy_plan,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'cppt_id' => $cppt_id,
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

        // SOAP_P_ACTION
        $icd9cm_arr = $dt_put['icd9cm'];

        $dt_soap_p_action_delete = array(
            'date_deleted' => date('Y-m-d H:i:s'),
            'user_deleted' => $user_id,
            'is_delete' => 1,
        );
        $where_delete = array(
            'soap_p_id' => $soap_p_id,
            'is_delete' => 0
        );
        $st_soap_p_action = $this->Soap_p_action->update_v2(0, $dt_soap_p_action_delete, $where_delete);
        if ($st_soap_p_action < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        for ($i=0; $i < count($icd9cm_arr); $i++) { 
            $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];

            $select = 'a.id';
            $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);
            if (count($icd9cm) == 0) {
                $this->REST_Return(404, 'there is no icd9cm with id ' . $icd9cm_id);
                return;
            }

            $dt_soap_p_action = array(
                'soap_p_id' => $soap_p_id,
                'icd9cm_id'  => $icd9cm_id,
                'user_deleted' => '',
                'date_deleted' => '',
                'is_delete' => 0,
            );
   
            $select = 'a.id';
            $where = array(
                'a.soap_p_id' => $soap_p_id,
                'a.icd9cm_id' => $icd9cm_id,
            );
            $soap_p_action = $this->Soap_p_action->select(0, $select, $where);
            if (count($soap_p_action) == 0) {
                $this->Soap_p_action->create($dt_soap_p_action);
            } else {
                $soap_p_action_id = $soap_p_action[0]->id;
                $st_soap_p_action = $this->Soap_p_action->update_v2($soap_p_action_id, $dt_soap_p_action);
                if ($st_soap_p_action < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            }
        }

        // TRANSACTION_DOCTOR
        $transaction_doctor_arr = $dt_put['transaction_doctor'];

        $dt_transaction_doctor_delete = array(
            'date_deleted' => date('Y-m-d H:i:s'),
            'user_deleted' => $user_id,
            'is_delete' => 1,
        );
        $where_delete = array(
            'soap_id' => $soap_id,
            'cppt_id' => $cppt_id,
            'is_delete' => 0
        );
        $st_transaction_doctor = $this->Transaction_doctor->update_v2(0, $dt_transaction_doctor_delete, $where_delete);
        if ($st_transaction_doctor < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        for ($i=0; $i < count($transaction_doctor_arr); $i++) { 
            $costing_id = $transaction_doctor_arr[$i]['costing_id'];
            $var_1 = $transaction_doctor_arr[$i]['var_1'];
            $var_2 = $transaction_doctor_arr[$i]['var_2'];
            $var_3 = $var_1 * $var_2;

            $select = 'a.id, c.name';
            $costing = $this->Costing_services_class->select($costing_id, $select);
            if (count($costing) == 0) {
                $this->REST_Return(404, 'there is no costing with id ' . $costing_id);
                return;
            }

            $costing_name = $costing[0]->name;

            $dt_transaction_doctor = array(
                'soap_id'               => $soap_id,
                'cppt_id'               => $cppt_id,
                'doctor_in_charge_id'   => $user_employee_id,
                'registration_destination_id' => $registration_destination_id,
                'costing_id'            => $costing_id,
                'costing_name'          => $costing_name,
                'var_1'                 => $var_1,
                'var_2'                 => $var_2,
                'var_3'                 => $var_3,
                'user_deleted'          => '',
                'date_deleted'          => '',
                'is_delete'             => 0,
            );
   
            $select = 'a.id';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.cppt_id' => $cppt_id,
                'a.costing_id' => $costing_id,
            );
            $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);
            if (count($transaction_doctor) == 0) {
                $dt_transaction_doctor['date_added'] = date('Y-m-d H:i:s');
                $this->Transaction_doctor->create($dt_transaction_doctor);
            } else {
                $transaction_doctor_id = $transaction_doctor[0]->id;
                $st_transaction_doctor = $this->Transaction_doctor->update_v2($transaction_doctor_id, $dt_transaction_doctor);
                if ($st_soap_p_action < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            }
        }

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save procedure', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_plan_put() {
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
        $this->form_validation->set_rules($this->save_plan_rules);
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
        $pharmacological_plan = $this->input->post('pharmacological_plan', FALSE);

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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        $plan = $this->input->post('plan', FALSE);
        $nursing_plan = $this->input->post('nursing_plan', FALSE);
        // $plan = $dt_put['plan'] ?? null;
        // $nursing_plan = $dt_put['nursing_plan'] ?? null;

        $dt_cppt = array(
            'plan'          => $plan,
            'pharmacological_plan' => $pharmacological_plan,
            'nursing_plan'  => $nursing_plan,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s'),
        );

        $st_cppt = $this->$model->update_v2($cppt_id, $dt_cppt);
        if ($st_cppt < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save plan', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        // LABORATORY_REQUEST
        if (isset($dt_put['laboratory_request_items'])) {
            $laboratory_request_items_arr = $dt_put['laboratory_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.cppt_id' => $cppt_id,
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
                    'cppt_id'               => $cppt_id,
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
                    if (count($laboratory_package) == 0) {
                        $this->REST_Return(404, 'there is no laboratory_package with id ' . $laboratory_package_id);
                        return;
                    }

                    $select = 'a.id';
                    $service_class = $this->Service_class->select($service_class_id, $select);
                    if (count($service_class) == 0) {
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

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save laboratory request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        // RADIOLOGY_REQUEST
        if (isset($dt_put['radiology_request_items'])) {
            $radiology_request_items_arr = $dt_put['radiology_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.cppt_id' => $cppt_id,
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
                    'cppt_id'               => $cppt_id,
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

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save radiology request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        // PHYSIOTHERAPY_REQUEST
        if (isset($dt_put['physiotherapy_request_items'])) {
            $physiotherapy_request_items_arr = $dt_put['physiotherapy_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.cppt_id' => $cppt_id,
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
                    'cppt_id'               => $cppt_id,
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

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save physiotherapy request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        // SOAP_P
        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'cppt_id'               => $cppt_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'cppt_id' => $cppt_id,
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
                AND pc.cppt_id = '$cppt_id'
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
                    'cppt_id'                   => $cppt_id,
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

        $this->Consultation->check_if_answering($registration_destination_id, $user_employee_id, $cppt_id);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save prescription', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function statistics_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // } 

        $output = $this->db->query("
            SELECT rd.id, rd.doctor_in_charge_id, e.fullname as doctor_name, p.fullname as patient_name, rd.checkin_date, rd.checkout_date
            FROM registration_destination rd 
            INNER JOIN health_facility_item hfi ON rd.health_facility_item_id = hfi.id
            INNER JOIN employee e ON rd.doctor_in_charge_id = e.id
            INNER JOIN registration r ON rd.registration_id = r.id
            INNER JOIN patient p ON r.norm = p.norm
            WHERE hfi.health_facility_id = '40f06534-07ec-4221-ad9d-dea08196079f'
            AND rd.checkin_date >= '2025-08-01 00:00:00'
            AND rd.checkin_date <= '2025-08-31 23:59:59'
            AND rd.is_checkout = 1
            AND rd.is_cancel = 0
            ORDER BY rd.checkin_date DESC
        ")->result();

        $stats = array();

        $data_sec = array(
            ['registration_destination_id','doctor_name','patient_name','checkin_date','checkout_date','must_filled','actual','percentage','resume_percentage'],
        );

        for ($i=0; $i < count($output); $i++) { 
            $registration_destination_id = $output[$i]->id;
            $doctor_in_charge_id = $output[$i]->doctor_in_charge_id;
            $doctor_name = $output[$i]->doctor_name;
            $patient_name = $output[$i]->patient_name;
            $checkin_date = new DateTime($output[$i]->checkin_date);
            $checkout_date = new DateTime($output[$i]->checkout_date);
            $interval = $checkin_date->diff($checkout_date);
            $interval_days = $interval->days + 1;

            $select = 'a.id';
            $where = array(
                'a.employee_id' => $doctor_in_charge_id
            );
            $user = $this->User->select(0, $select, $where);

            $user_id = $user[0]->id;

            $select = 'count(*) as total';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.user_added' => $user_id,
            );
            $cppt = $this->Cppt->select(0, $select, $where);

            $total_cppt = $cppt[0]->total;

            $percentage = ($total_cppt / $interval_days) * 100;

            $select = 'count(*) as total';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
            );
            $resume = $this->Resume->select(0, $select, $where);

            $total_resume = $resume[0]->total;
            $resume_percentage = number_format(($total_resume / 1) * 100, 2) . '%';
            
            $output[$i]->must_filled = $interval_days;
            $output[$i]->actual = $total_cppt;
            $output[$i]->percentage = number_format($percentage, 2) . '%';
            $output[$i]->resume = $resume_percentage;

            array_push($data_sec, array($registration_destination_id, $doctor_name, $patient_name, $output[$i]->checkin_date, $output[$i]->checkout_date, $interval_days, $total_cppt, number_format($percentage, 2) . '%', $resume_percentage));

            if (!key_exists($doctor_in_charge_id, $stats)) {
                $stats[$doctor_in_charge_id] = array(
                    'name' => $doctor_name,
                    'must_filled' => 0,
                    'actual' => 0,
                    'resume_must_filled' => 0,
                    'resume_actual' => 0,
                );
            }

            // if ($doctor_in_charge_id == "598a6fb8-8e41-423a-9251-c226b0c9f2cd") {
            //     echo $stats[$doctor_in_charge_id]['must_filled'];
            //     echo "<br>";
            //     // echo $interval_days;
            //     // echo "<br>";

            // }


            $stats[$doctor_in_charge_id]['must_filled'] = $stats[$doctor_in_charge_id]['must_filled'] + (int) $interval_days;
            $stats[$doctor_in_charge_id]['actual'] = $stats[$doctor_in_charge_id]['actual'] + (int) $total_cppt;
            $stats[$doctor_in_charge_id]['resume_must_filled'] = $stats[$doctor_in_charge_id]['resume_must_filled'] + 1;
            $stats[$doctor_in_charge_id]['resume_actual'] = $stats[$doctor_in_charge_id]['resume_actual'] + (int) $total_resume;
        }

        $data = [
            ['name', 'must_filled', 'actual', 'percentage', 'resume_must_filled', 'resume_actual', 'resume_percentage'],
        ];

        foreach ($stats as $key => $val) {
            $name = $val['name'];
            $must_filled = $val['must_filled'];
            $actual = $val['actual'];
            $percentage = ($actual / $must_filled) * 100;
            $percentage = number_format($percentage, 2) . '%';

            $resume_must_filled = $val['resume_must_filled'];
            $resume_actual = $val['resume_actual'];
            $resume_percentage = ($resume_actual / $resume_must_filled) * 100;
            $resume_percentage = number_format($resume_percentage, 2) . '%';

            array_push($data, array($name, $must_filled, $actual, $percentage, $resume_must_filled, $resume_actual, $resume_percentage));
        }

        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="output.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // Write each row to CSV
        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        // Close the stream
        fclose($output);
        exit;



        $this->REST_Return(200, 'SUCCESS', $dt_stats);
        return;
    }

    public function last_doctor_get($p = 0) {
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

        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;

        if ($registration_destination_id == null) {
            $this->REST_Return(400, 'registration_destination_id is required');
            return;
        }

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $where = array(
            'a.user_added' => $user_id,
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
        );
        $join = array();
        $order = array('a.date_added' => 'DESC');
        $this->$model->limit = 1;
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
        if (count($output) > 0) {
            $cppt_id = $output[0]->id;
            
            $output[0]->icd10_notes = '';
            $output[0]->non_icd10 = [];
            $output[0]->icd10 = [];

            // SOAP_A
            $select = 'a.id, a.icd10_notes, a.non_icd10, a.nursing_diagnose';
            $where = array(
                'a.cppt_id' => $cppt_id
            );
            $soap_a = $this->Soap_a->select(0, $select, $where);
            if (count($soap_a) > 0) {
                $soap_a_id = $soap_a[0]->id;

                $output[0]->icd10_notes = $soap_a[0]->icd10_notes;
                $output[0]->non_icd10 = $soap_a[0]->non_icd10 ?? '' != '' ? explode('|', $soap_a[0]->non_icd10) : [];

                $select = 'a.id, a.icd10_id, icd.code as icd10_code, icd.name as icd10_name';
                $where = array(
                    'a.soap_a_id' => $soap_a_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd10 icd', 'a.icd10_id = icd.id', 'INNER'),
                );
                $order = array('a.index' => 'ASC');
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);

                $output[0]->icd10 = $soap_a_action;
            }
        } else {
            $output = array(
                'id' => '',
                'icd10_notes' => '',
                'non_icd10' => [],
                'icd10' => [],
            );
        }

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_nutrition_put() {
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
        $this->form_validation->set_rules($this->save_nutrition_rules);
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

        // CPPT
        $dt_cppt = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $cppt_id = $this->$model->create($dt_cppt);

            $this->Origin->insert_origin('cppt', $cppt_id, $health_facility_item_id, $user_id);
        } else {
            $cppt_id = $id;

            $select = 'a.id';
            $cppt = $this->$model->select($cppt_id, $select);
            if (count($cppt) == 0) {
                $this->REST_Return(404, 'there is no cppt with id ' . $cppt_id);
                return;
            }
        }

        $nutrition_assessment = $dt_put['nutrition_assessment'];
        $nutrition_diagnosis = $dt_put['nutrition_diagnosis'];
        $nutrition_intervention = $dt_put['nutrition_intervention'];
        $nutrition_monitoring_evaluation = $dt_put['nutrition_monitoring_evaluation'];

        $dt_cppt = array(
            'nutrition_assessment'            => $nutrition_assessment,
            'nutrition_diagnosis'             => $nutrition_diagnosis,
            'nutrition_intervention'          => $nutrition_intervention,
            'nutrition_monitoring_evaluation' => $nutrition_monitoring_evaluation,
            'user_updated'                    => $user_id,
            'date_updated'                    => date('Y-m-d H:i:s'),
        );

        $st_cppt = $this->$model->update_v2($cppt_id, $dt_cppt);
        if ($st_cppt < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_cppt,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save nutrition', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $cppt_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}