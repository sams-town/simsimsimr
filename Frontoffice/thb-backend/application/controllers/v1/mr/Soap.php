<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller; //TEST COMMIT!!!
class Soap extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        $this->load->helper('url');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Soap_s_model", "Soap_s");
        $this->load->model("$this->directory/Soap_o_model", "Soap_o");
        $this->load->model("$this->directory/Soap_o_action_model", "Soap_o_action");
        $this->load->model("$this->directory/Soap_a_model", "Soap_a");
        $this->load->model("$this->directory/Soap_a_action_model", "Soap_a_action");
        $this->load->model("$this->directory/Soap_p_model", "Soap_p");
        $this->load->model("$this->directory/Soap_inpatient_model", "Soap_inpatient");
        $this->load->model("$this->directory/Soap_p_action_model", "Soap_p_action");
        $this->load->model("$this->directory/Icd10_model", "Icd10");
        $this->load->model("$this->directory/Icd9cm_model", "Icd9cm");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Registration_insurance_model", "Registration_insurance");
        $this->load->model("$this->directory/Nurse_model", "Nurse");
        $this->load->model("$this->directory/Cppt_model", "Cppt");
        $this->load->model("$this->directory/Sbar_model", "Sbar");
        $this->load->model("v1/mr/emergency/Triage_new_model", "Triage_new");
        $this->load->model("$this->directory/Insurance_model", "Insurance");
        $this->load->model("$this->directory/Responsible_person_model", "Responsible_person");
        $this->load->model("$this->directory/Patient_allergy_model", "Patient_allergy");
        $this->load->model("$this->directory/Medical_rehabilitation_model", "Medical_rehabilitation");
        $this->load->model("$this->directory/Medical_check_up_model", "Medical_check_up");
        $this->load->model("$this->directory/Hemodialysis_initiation_model", "Hemodialysis_initiation");
        $this->load->model("$this->directory/Refer_internal_model", "Refer_internal");
        $this->load->model("$this->directory/Refer_internal_model", "Refer_internal");
        $this->load->model("$this->directory/inpatient/Vital_signs_monitoring_model", "Vital_signs_monitoring");
        $this->load->model("$this->directory/inpatient/Early_warning_score_model", "Early_warning_score");
        $this->load->model("$this->directory/inpatient/Early_warning_score_item_model", "Early_warning_score_item");
        $this->load->model("$this->directory/inpatient/Temperature_chart_model", "Temperature_chart");
        $this->load->model("$this->directory/inpatient/Temperature_chart_item_model", "Temperature_chart_item");
        $this->load->model("$this->directory/inpatient/Pews_model", "Pews");
        $this->load->model("$this->directory/inpatient/Pews_item_model", "Pews_item");
        $this->load->model("v1/health_facility/Laboratory_package_model", "Laboratory_package");
        $this->load->model("v1/health_facility/Laboratory_request_model", "Laboratory_request");
        $this->load->model("v1/health_facility/Laboratory_request_item_model", "Laboratory_request_item");
        $this->load->model("v1/health_facility/Physiotherapy_package_model", "Physiotherapy_package");
        $this->load->model("v1/health_facility/Physiotherapy_request_model", "Physiotherapy_request");
        $this->load->model("v1/health_facility/Physiotherapy_request_item_model", "Physiotherapy_request_item");
        $this->load->model("v1/health_facility/Radiology_package_model", "Radiology_package");
        $this->load->model("v1/health_facility/Radiology_request_model", "Radiology_request");
        $this->load->model("v1/health_facility/Radiology_request_item_model", "Radiology_request_item");
        $this->load->model("v1/health_facility/Radiology_request_item_image_model", "Radiology_request_item_image");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/md/Allergy_model", "Allergy");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/setting/Unit_model", "Unit");
        $this->load->model("v1/tr/Transaction_doctor_model", "Transaction_doctor");
        $this->load->model("v1/tr/Transaction_physiotherapy_model", "Transaction_physiotherapy");
        $this->load->model("v1/pharmacy/Catalog_model", "Catalog");
        $this->load->model("v1/pharmacy/Depo_model", "Depo");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");
        $this->load->model("v1/pharmacy/Kardex_model", "Kardex");
        $this->load->model("v1/pharmacy/Kardex_item_model", "Kardex_item");
        $this->load->model("v1/pharmacy/Signatura_times_model", "Signatura_times");
        $this->load->model("v1/pharmacy/Catalog_instruction_model", "Administer");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/setting/Exits_model", "Exits");
        // Load Helper
        $this->load->helper('exists');
        $this->load->helper('queue');
        $this->load->helper('ews');
        $this->load->helper('utils');
        $this->load->helper('variable');

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->create_full_book_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim|required'),
            // array('field' => 'icd10', 'label' => 'icd10', 'rules' => 'trim|required'),
            // array('field' => 'laboratory_request_items', 'label' => 'laboratory_request_items', 'rules' => 'trim'),
            // array('field' => 'radiology_request_items', 'label' => 'radiology_request_items', 'rules' => 'trim'),
            // array('field' => 'physiotherapy_request_items', 'label' => 'physiotherapy_request_items', 'rules' => 'trim'),
        );

        $this->create_inpatient_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            // array('field' => 'result', 'label' => 'result', 'rules' => 'trim|required'),
            array('field' => 'condition', 'label' => 'condition', 'rules' => 'trim|required'),
            array('field' => 'plan', 'label' => 'plan', 'rules' => 'trim|required'),
            array('field' => 'indication', 'label' => 'indication', 'rules' => 'trim|required'),
            // array('field' => 'type_room_id', 'label' => 'type_room_id', 'rules' => 'trim|required'),
        );

        $this->update_inpatient_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            // array('field' => 'result', 'label' => 'result', 'rules' => 'trim|required'),
            array('field' => 'condition', 'label' => 'condition', 'rules' => 'trim|required'),
            array('field' => 'plan', 'label' => 'plan', 'rules' => 'trim|required'),
            array('field' => 'indication', 'label' => 'indication', 'rules' => 'trim|required'),
            // array('field' => 'type_room_id', 'label' => 'type_room_id', 'rules' => 'trim|required'),
        );

        $this->resume_update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'summary_medical_history', 'label' => 'summary_medical_history', 'rules' => 'trim'),
            array('field' => 'significant_medications', 'label' => 'significant_medications', 'rules' => 'trim'),
            array('field' => 'take_home_medications', 'label' => 'take_home_medications', 'rules' => 'trim'),
            array('field' => 'care_instruction_control', 'label' => 'care_instruction_control', 'rules' => 'trim|max_length[50]'),
            array('field' => 'care_instruction_control_date', 'label' => 'care_instruction_control_date', 'rules' => 'trim'),
            array('field' => 'care_instruction_anythingelse', 'label' => 'care_instruction_anythingelse', 'rules' => 'trim|max_length[50]'),
            array('field' => 'exit_id', 'label' => 'exit_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'exit_anythingelse', 'label' => 'exit_anythingelse', 'rules' => 'trim|max_length[50]'),
            array('field' => 'exit_condition_type_id', 'label' => 'exit_condition_type_id', 'rules' => 'trim|max_length[1]'),
            array('field' => 'exit_condition_anythingelse', 'label' => 'exit_condition_anythingelse', 'rules' => 'trim|max_length[50]'),
            array('field' => 'home_education', 'label' => 'home_education', 'rules' => 'trim'),
            array('field' => 'physical_check', 'label' => 'physical_check', 'rules' => 'trim'),
            array('field' => 'transfer_reason', 'label' => 'transfer_reason', 'rules' => 'trim'),
            array('field' => 'is_resume_exit', 'label' => 'is_resume_exit', 'rules' => 'trim|max_length[1]'),
        );

        $this->create_soap_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_jkn_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim'),
        );

        $this->update_ophthal_rules = array(
            array('field' => 'ophthal_od', 'label' => 'ophthal_od', 'rules' => 'trim'),
            array('field' => 'ophthal_os', 'label' => 'ophthal_os', 'rules' => 'trim'),
        );

        $this->save_anamnesis_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'anamesa_type_id', 'label' => 'anamesa_type_id', 'rules' => 'trim'),
            array('field' => 'family_name', 'label' => 'family_name', 'rules' => 'trim'),
            array('field' => 'family_relation_id', 'label' => 'family_relation_id', 'rules' => 'trim'),
            array('field' => 'main_complaint', 'label' => 'main_complaint', 'rules' => 'trim'),
            array('field' => 'history_disease_now', 'label' => 'history_disease_now', 'rules' => 'trim'),
            array('field' => 'history_disease_past', 'label' => 'history_disease_past', 'rules' => 'trim'),
            array('field' => 'history_disease_family', 'label' => 'history_disease_family', 'rules' => 'trim'),
            array('field' => 'history_habitude', 'label' => 'history_habitude', 'rules' => 'trim'),
            array('field' => 'history_drugs', 'label' => 'history_drugs', 'rules' => 'trim'),
            array('field' => 'history_allergy', 'label' => 'history_allergy', 'rules' => 'trim'),
            array('field' => 'allergies[]', 'label' => 'allergies', 'rules' => 'trim'),
        );

        $this->save_ttv_antropometri_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'aware', 'label' => 'aware', 'rules' => 'trim'),
            array('field' => 'eye_score', 'label' => 'eye_score', 'rules' => 'trim'),
            array('field' => 'verbal_score', 'label' => 'verbal_score', 'rules' => 'trim'),
            array('field' => 'motor_score', 'label' => 'motor_score', 'rules' => 'trim'),
            array('field' => 'pupil_score', 'label' => 'pupil_score', 'rules' => 'trim'),
            array('field' => 'verbal_response', 'label' => 'verbal_response', 'rules' => 'trim'),
            array('field' => 'oxygen_type_id', 'label' => 'oxygen_type_id', 'rules' => 'trim'),
            array('field' => 'systolic_blood_pressure', 'label' => 'systolic_blood_pressure', 'rules' => 'trim'),
            array('field' => 'diastolic_blood_pressure', 'label' => 'diastolic_blood_pressure', 'rules' => 'trim'),
            array('field' => 'pulse_frequency', 'label' => 'pulse_frequency', 'rules' => 'trim'),
            array('field' => 'breath_frequency', 'label' => 'breath_frequency', 'rules' => 'trim'),
            array('field' => 'oximetry_frequency', 'label' => 'oximetry_frequency', 'rules' => 'trim'),
            array('field' => 'temperature', 'label' => 'temperature', 'rules' => 'trim'),
            array('field' => 'weight', 'label' => 'weight', 'rules' => 'trim'),
            array('field' => 'height', 'label' => 'height', 'rules' => 'trim'),
            array('field' => 'imt', 'label' => 'imt', 'rules' => 'trim'),
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim'),
            array('field' => 'chest_circumference', 'label' => 'chest_circumference', 'rules' => 'trim'),
            array('field' => 'arm_circumference', 'label' => 'arm_circumference', 'rules' => 'trim'),
            array('field' => 'stomach_circumference', 'label' => 'stomach_circumference', 'rules' => 'trim'),
            array('field' => 'knee_circumference', 'label' => 'knee_circumference', 'rules' => 'trim'),
        );

        $this->save_st_generalist_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'head', 'label' => 'head', 'rules' => 'trim'),
            array('field' => 'head_hair_color', 'label' => 'head_hair_color', 'rules' => 'trim'),
            array('field' => 'head_hair_condition', 'label' => 'head_hair_condition', 'rules' => 'trim'),
            array('field' => 'eye_conjunctive', 'label' => 'eye_conjunctive', 'rules' => 'trim'),
            array('field' => 'eye_sclera', 'label' => 'eye_sclera', 'rules' => 'trim'),
            array('field' => 'eye_pupil', 'label' => 'eye_pupil', 'rules' => 'trim'),
            array('field' => 'eye_light_reflex', 'label' => 'eye_light_reflex', 'rules' => 'trim'),
            array('field' => 'eye_blind_color', 'label' => 'eye_blind_color', 'rules' => 'trim'),
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
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim'),
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim'),
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim'),
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim'),
            array('field' => 'chest_circumference', 'label' => 'chest_circumference', 'rules' => 'trim'),
            array('field' => 'arm_circumference', 'label' => 'arm_circumference', 'rules' => 'trim'),
            array('field' => 'stomach_circumference', 'label' => 'stomach_circumference', 'rules' => 'trim'),
            array('field' => 'knee_circumference', 'label' => 'knee_circumference', 'rules' => 'trim'),
            array('field' => 'ophthal_od', 'label' => 'ophthal_od', 'rules' => 'trim'),
            array('field' => 'ophthal_os', 'label' => 'ophthal_os', 'rules' => 'trim'),
        );

        $this->save_diagnose_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'icd10[]', 'label' => 'icd10', 'rules' => 'trim'),
            array('field' => 'icd10_notes', 'label' => 'icd10_notes', 'rules' => 'trim'),
            array('field' => 'non_icd10[]', 'label' => 'non_icd10', 'rules' => 'trim'),
        );

        $this->save_procedure_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'icd9cm[]', 'label' => 'icd9cm', 'rules' => 'trim'),
            array('field' => 'therapy_plan', 'label' => 'therapy_plan', 'rules' => 'trim'),
            array('field' => 'transaction_doctor[]', 'label' => 'transaction_doctor', 'rules' => 'trim'),
            array('field' => 'advice', 'label' => 'advice', 'rules' => 'trim'),
        );

        $this->save_discharge_planning_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'estimated_duration_treatment', 'label' => 'estimated_duration_treatment', 'rules' => 'trim'),
            array('field' => 'follow_up_care_arry[]', 'label' => 'follow_up_care_arry', 'rules' => 'trim'),
            array('field' => 'diet_and_nutrition_type_id', 'label' => 'diet_and_nutrition_type_id', 'rules' => 'trim'),
            array('field' => 'diet_and_nutrition', 'label' => 'diet_and_nutrition', 'rules' => 'trim'),
            array('field' => 'wound_care_type_id', 'label' => 'wound_care_type_id', 'rules' => 'trim'),
            array('field' => 'wound_care', 'label' => 'wound_care', 'rules' => 'trim'),
            array('field' => 'medical_rehabilitation_type_id', 'label' => 'medical_rehabilitation_type_id', 'rules' => 'trim'),
            array('field' => 'medical_rehabilitation', 'label' => 'medical_rehabilitation', 'rules' => 'trim'),
            array('field' => 'pain_management_type_id', 'label' => 'pain_management_type_id', 'rules' => 'trim'),
            array('field' => 'pain_management', 'label' => 'pain_management', 'rules' => 'trim'),
            array('field' => 'discharge_planning_other', 'label' => 'discharge_planning_other', 'rules' => 'trim'),
        );

        $this->save_hemodialysis_initiation_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'main_diagnosis', 'label' => 'main_diagnosis', 'rules' => 'trim'),
            array('field' => 'etiology', 'label' => 'etiology', 'rules' => 'trim'),
            array('field' => 'comorbidities', 'label' => 'comorbidities', 'rules' => 'trim'),
            array('field' => 'frequency_hd', 'label' => 'frequency_hd', 'rules' => 'trim'),
            array('field' => 'vascular_access', 'label' => 'vascular_access', 'rules' => 'trim'),
        );

        $this->save_medical_rehabilitation_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'is_initial_inpatient', 'label' => 'is_initial_inpatient', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_hemodialysis', 'label' => 'is_hemodialysis', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_rehabilitation', 'label' => 'is_medical_rehabilitation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_medical_check_up', 'label' => 'is_medical_check_up', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'functional_testing', 'label' => 'functional_testing', 'rules' => 'trim'),
            array('field' => 'suspected_disease_type_id', 'label' => 'suspected_disease_type_id', 'rules' => 'trim'),
            array('field' => 'suspected_disease', 'label' => 'suspected_disease', 'rules' => 'trim'),
            array('field' => 'result', 'label' => 'result', 'rules' => 'trim'),
            array('field' => 'conclusion', 'label' => 'conclusion', 'rules' => 'trim'),
            array('field' => 'recommendation', 'label' => 'recommendation', 'rules' => 'trim'),
            array('field' => 'functional_diagnosis', 'label' => 'functional_diagnosis', 'rules' => 'trim'),
            array('field' => 'medical_diagnosis', 'label' => 'medical_diagnosis', 'rules' => 'trim'),
        );

        $this->save_laboratory_request_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'laboratory_request_items[]', 'label' => 'laboratory_request_items', 'rules' => 'trim'),
        );

        $this->save_radiology_request_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'radiology_request_items[]', 'label' => 'radiology_request_items', 'rules' => 'trim'),
        );

        $this->save_physiotherapy_request_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'physiotherapy_request_items[]', 'label' => 'physiotherapy_request_items', 'rules' => 'trim'),
        );

        $this->save_prescription_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'pharmacy_depo_id', 'label' => 'pharmacy_depo_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'form_prescription[]', 'label' => 'form_prescription', 'rules' => 'trim'),
            array('field' => 'form_prescription_parent[]', 'label' => 'form_prescription_parent', 'rules' => 'trim'),
        );

        $this->update_status_bpjs_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->update_book_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date_request', 'label' => 'date_request', 'rules' => 'trim|required'),
        );

        $this->transfer_booking_request_rules = array(
            array('field' => 'booking_id', 'label' => 'booking_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'request_id', 'label' => 'request_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_request_id', 'label' => 'type_request_id', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->save_medical_check_up_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'fit_to_work', 'label' => 'fit_to_work', 'rules' => 'trim'),
            array('field' => 'consultation', 'label' => 'consultation', 'rules' => 'trim'),
        );
    }

    private function base64ToFile ($p = 0, $id = 0) {
        // define('UPLOAD_DIR', 'assets/document/usg/');
        $data         = base64_decode($p);
        $file_name    = 'image_usg_'.$id; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name . '.png';
        $success      = file_put_contents($file, $data);
        return $file;
    }

    /**
     * Soap API
     * --------------------
     * @param: registration_destination_id
     * @param: doctor_in_charge_id
     * --------------------
     * @method : POST
     * @link: version/mr/soap
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
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|required|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check registration_destination_id & doctor_in_charge_id
                if($this->Registration_destination->read($dt_post['registration_destination_id'], false) != [] && $this->Employee->read($dt_post['doctor_in_charge_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_soap = array(
                        'registration_destination_id'  => $dt_post['registration_destination_id'],
                        'doctor_in_charge_id'          => $dt_post['doctor_in_charge_id'],
                    );
                    $id = $this->$model->create($dt_soap);
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
                } else {
                    $this->REST_Return(404, 'Wrong registration_destination_id or doctor_in_charge_id!');
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
     * Soap API
     * --------------------
     * DATA SOAP
     * @param: registration_destination_id
     * @param: doctor_in_charge_id
     * DATA SOAP_S
     * @param: anamesa_type_id
     * @param: family_name
     * @param: family_relation_id
     * @param: main_complaint
     * @param: history_disease_now
     * @param: history_disease_past
     * @param: history_disease_family
     * @param: history_habitude
     * @param: history_drugs
     * @param: history_allergy
     * DATA SOAP_O
     * @param: physical_check
     * @param: soap_o_anythingelse
     * @param: systolic_blood_pressure
     * @param: diastolic_blood_pressure
     * @param: classification_blood_pressure_type_id
     * @param: pulse_frequency
     * @param: breath_frequency
     * @param: oximetry_frequency
     * @param: classification_oximetry_type_id
     * @param: temperature
     * @param: classification_temperature_type_id
     * @param: blood_sugar
     * @param: weight
     * @param: classification_weight_type_id
     * @param: height
     * @param: imt
     * @param: head_circumference
     * @param: chest_circumference
     * @param: arm_circumference
     * @param: stomach_circumference
     * @param: knee_circumference
     * DATA SOAP_O_ACTION
     * @param: canvasing_type_id
     * @param: canvasing_text
     * @param: attr1
     * @param: attr2
     * DATA SOAP_A
     * @param: development_notes
     * @param: soap_a_anythingelse
     * DATA SOAP_A_ACTION
     * @param: icd10_id[]
     * DATA SOAP_P
     * @param: therapy_plan
     * DATA SOAP_P_ACTION
     * @param: icd9cm_id[]
     * DATA TRANSACTION_DOCTOR
     * @param: tr_doctor_costing_id
     * @param: tr_doctor_var_1
     * @param: tr_doctor_var_2
     * @param: tr_doctor_var_3
     * @param: tr_doctor_notes
     * DATA TRANSACTION_PHYSIOTHERAPY
     * @param: tr_physiotherapy_costing_id
     * @param: tr_physiotherapy_var_1
     * @param: tr_physiotherapy_var_2
     * @param: tr_physiotherapy_var_3
     * @param: tr_physiotherapy_notes
     * DATA LABORATORY_REQUEST
     * @param: laboratory_date_request
     * @param: laboratory_request_items
     * DATA RADIOLOGY_REQUEST
     * @param: radiology_date_request
     * @param: radiology_request_items
     * DATA PHYSIOTHERAPY_REQUEST
     * @param: physiotherapy_date_request
     * @param: physiotherapy_request_items
     * --------------------
     * @method : POST
     * @link: version/mr/soap
     **/

    public function create_full_post() {
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
            // DATA SOAP
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|required|max_length[36]');
            // DATA SOAP_S
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('history_drugs', 'history_drugs', 'trim');
            $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim');
            // DATA SOAP_O
            $this->form_validation->set_rules('physical_check', 'physical_check', 'trim');
            $this->form_validation->set_rules('soap_o_anythingelse', 'soap_o_anythingelse', 'trim');
            $this->form_validation->set_rules('systolic_blood_pressure', 'systolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('diastolic_blood_pressure', 'diastolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_blood_pressure_type_id', 'classification_blood_pressure_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('pulse_frequency', 'pulse_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('breath_frequency', 'breath_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('oximetry_frequency', 'oximetry_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_oximetry_type_id', 'classification_oximetry_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('temperature', 'temperature', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_temperature_type_id', 'classification_temperature_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('blood_sugar', 'blood_sugar', 'trim|max_length[8]');
            $this->form_validation->set_rules('weight', 'weight', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_weight_type_id', 'classification_weight_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('height', 'height', 'trim|max_length[8]');
            $this->form_validation->set_rules('imt', 'imt', 'trim|max_length[8]');
            $this->form_validation->set_rules('head_circumference', 'head_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('chest_circumference', 'chest_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('arm_circumference', 'arm_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('stomach_circumference', 'stomach_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('knee_circumference', 'knee_circumference', 'trim|max_length[8]');
            // DATA SOAP_O_ACTION
            $this->form_validation->set_rules('canvasing_type_id', 'canvasing_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('canvasing_text', 'canvasing_text', 'trim');
            $this->form_validation->set_rules('attr1', 'attr1', 'trim');
            $this->form_validation->set_rules('attr2', 'attr2', 'trim');
            // DATA SOAP_A
            $this->form_validation->set_rules('development_notes', 'development_notes', 'trim');
            $this->form_validation->set_rules('soap_a_anythingelse', 'soap_a_anythingelse', 'trim');
            // DATA SOAP_A_ACTION
            $this->form_validation->set_rules('icd10[]', 'icd10', 'trim');
            // DATA SOAP_P
            $this->form_validation->set_rules('therapy_plan', 'therapy_plan', 'trim');
            // DATA SOAP_P_ACTION
            $this->form_validation->set_rules('icd9cm[]', 'icd9cm', 'trim');
            // DATA TRANSACTION_DOCTOR
            $this->form_validation->set_rules('transaction_doctor', 'transaction_doctor', 'trim');
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            // DATA TRANSACTION_PHYSIOTHERAPY
            $this->form_validation->set_rules('transaction_physiotherapy', 'transaction_physiotherapy', 'trim');
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            // DATA LABORATORY_REQUEST
            $this->form_validation->set_rules('laboratory_date_request', 'laboratory_date_request', 'trim');
            $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim');
            // DATA RADIOLOGY_REQUEST
            $this->form_validation->set_rules('radiology_date_request', 'radiology_date_request', 'trim');
            $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim');
            // DATA PHYSIOTHERAPY_REQUEST
            $this->form_validation->set_rules('physiotherapy_date_request', 'physiotherapy_date_request', 'trim');
            $this->form_validation->set_rules('physiotherapy_request_items[]', 'physiotherapy_request_items', 'trim');
            // DATA PRESCRIPTION
            $this->form_validation->set_rules('form_prescription[]', 'form_prescription', 'trim');
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

                $advice = $dt_post['advice'];
                $registration_destination_id = $dt_post['registration_destination_id'];

                $registration_destination = $this->Registration_destination->select_id($registration_destination_id);
                if ($registration_destination == '') {
                    $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
                    return;
                }

                $this->Registration->limit = 1;
                $this->Registration->whereFields['b.id'] = $dt_post['registration_destination_id'];
                $dt_reg = $this->Registration->read(0,false)[0];
                $dt_post['doctor_in_charge_id'] = $dt_reg->doctor_in_charge_id;
                
                $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
                $is_initial_inpatient = $dt_post['is_initial_inpatient'];
                $is_medical_rehabilitation = $dt_post['is_medical_rehabilitation'];
                $is_medical_check_up = $dt_post['is_medical_check_up'];
                $is_hemodialysis = $dt_post['is_hemodialysis'];

                $user_added = $this->User->read($user_id,false)[0]->employee_id;
                $dt_soap = array(
                    'registration_destination_id'  => $dt_post['registration_destination_id'],
                    'doctor_in_charge_id'          => $doctor_in_charge_id,
                    'advice'                       => $advice,
                    'is_initial_inpatient'         => $is_initial_inpatient,
                    'is_medical_rehabilitation'    => $is_medical_rehabilitation,
                    'is_medical_check_up'          => $is_medical_check_up,
                    'is_hemodialysis'              => $is_hemodialysis,
                    'user_added'                   => $user_added,
                    'date_added'                   => date("Y-m-d H:i:s",time())
                );
                $id = $this->$model->create($dt_soap);
                if ($this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->sts == 4) {
                    $this->Registration_destination->replace(array('id'=>$dt_post['registration_destination_id'],'health_facility_item_id'=>$this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->health_facility_item_id,'registration_id'=>$this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->registration_id, 'done_doctor_stamp'=>date('Y-m-d H:i:s',time()), 'sts' => 5));
                }

                // SOAP_S
                $dt_soap_s = array(
                    'soap_id'               => $id,
                    'anamesa_type_id'       => isset($dt_post['anamesa_type_id']) ? $dt_post['anamesa_type_id'] : '',
                    'family_name'           => isset($dt_post['family_name']) ? $dt_post['family_name'] : '',
                    'family_relation_id'    => isset($dt_post['family_relation_id']) ? $dt_post['family_relation_id'] : '',
                    'main_complaint'        => isset($dt_post['main_complaint']) ? $dt_post['main_complaint'] : '',
                    'history_disease_now'   => isset($dt_post['history_disease_now']) ? $dt_post['history_disease_now'] : '',
                    'history_disease_past'  => isset($dt_post['history_disease_past']) ? $dt_post['history_disease_past'] : '',
                    'history_drugs'         => isset($dt_post['history_drugs']) ? $dt_post['history_drugs'] : '',
                    'history_allergy'       => isset($dt_post['history_allergy']) ? $dt_post['history_allergy'] : '',
                );
                $this->Soap_s->create($dt_soap_s);

                $sore_score = $dt_post['sore_score'];
                $ews = $dt_post['ews'];

                // status generalis
                $head = $dt_post['head'];
                $head_hair_color = $dt_post['head_hair_color'];
                $head_hair_condition = $dt_post['head_hair_condition'];
                $eye_conjunctive = $dt_post['eye_conjunctive'];
                $eye_sclera = $dt_post['eye_sclera'];
                $eye_pupil = $dt_post['eye_pupil'];
                $eye_light_reflex = $dt_post['eye_light_reflex'];
                $eye_blind_color = $dt_post['eye_blind_color'];
                $mouth_mucosa = $dt_post['mouth_mucosa'];
                $mouth_discoloration = $dt_post['mouth_discoloration'];
                $mouth_tongue = $dt_post['mouth_tongue'];
                $mouth_caries = $dt_post['mouth_caries'];
                $neck_kgb = $dt_post['neck_kgb'];
                $neck_trachea_position = $dt_post['neck_trachea_position'];
                $neck_massa = $dt_post['neck_massa'];
                $thorax_insp_chest = $dt_post['thorax_insp_chest'];
                $thorax_insp_retraction = $dt_post['thorax_insp_retraction'];
                $thorax_palp_chest = $dt_post['thorax_palp_chest'];
                $thorax_palp_fremitus = $dt_post['thorax_palp_fremitus'];
                $thorax_palp_crepitation = $dt_post['thorax_palp_crepitation'];
                $thorax_ausk_pul_vasiculer = $dt_post['thorax_ausk_pul_vasiculer'];
                $thorax_ausk_pul_rhonki = $dt_post['thorax_ausk_pul_rhonki'];
                $thorax_ausk_pul_wheezing = $dt_post['thorax_ausk_pul_wheezing'];
                $thorax_ausk_cor_heart_sound = $dt_post['thorax_ausk_cor_heart_sound'];
                $thorax_ausk_cor_murmur = $dt_post['thorax_ausk_cor_murmur'];
                $thorax_ausk_cor_gallop = $dt_post['thorax_ausk_cor_gallop'];
                $abdomen_supel = $dt_post['abdomen_supel'];
                $abdomen_bowel_sounds = $dt_post['abdomen_bowel_sounds'];
                $abdomen_tenderness = $dt_post['abdomen_tenderness'];
                $abdomen_hepar_lien = $dt_post['abdomen_hepar_lien'];
                $abdomen_other_physical_check = $dt_post['abdomen_other_physical_check'];
                $extremity_acral = $dt_post['extremity_acral'];
                $extremity_crt = $dt_post['extremity_crt'];
                $extremity_edema = $dt_post['extremity_edema'];
                $genitalia = $dt_post['genitalia'];
                $localist_status = $dt_post['localist_status'];
                $ophthal_status = $dt_post['ophthal_status'];
                $neurologist_status = $dt_post['neurologist_status'];

                // SOAP_O
                $dt_soap_o = array(
                    'soap_id'                                   => $id,
                    'physical_check'                            => isset($dt_post['physical_check']) ? $dt_post['physical_check'] : '',
                    'anythingelse'                              => isset($dt_post['soap_o_anythingelse']) ? $dt_post['soap_o_anythingelse'] : '',
                    'aware'                                     => isset($dt_post['aware']) ? $dt_post['aware'] : '',
                    'eye_score'                                 => isset($dt_post['eye_score']) ? $dt_post['eye_score'] : '',
                    'verbal_score'                              => isset($dt_post['verbal_score']) ? $dt_post['verbal_score'] : '',
                    'motor_score'                               => isset($dt_post['motor_score']) ? $dt_post['motor_score'] : '',
                    'pupil_score'                               => isset($dt_post['pupil_score']) ? $dt_post['pupil_score'] : '',
                    'verbal_response'                           => isset($dt_post['verbal_response']) ? $dt_post['verbal_response'] : '',
                    'oxygen_type_id'                            => isset($dt_post['oxygen_type_id']) ? $dt_post['oxygen_type_id'] : '',
                    'systolic_blood_pressure'                   => isset($dt_post['systolic_blood_pressure']) ? $dt_post['systolic_blood_pressure'] : '',
                    'diastolic_blood_pressure'                  => isset($dt_post['diastolic_blood_pressure']) ? $dt_post['diastolic_blood_pressure'] : '',
                    'classification_blood_pressure_type_id'     => isset($dt_post['classification_blood_pressure_type_id']) ? $dt_post['classification_blood_pressure_type_id'] : '',
                    'pulse_frequency'                           => isset($dt_post['pulse_frequency']) ? $dt_post['pulse_frequency'] : '',
                    'breath_frequency'                          => isset($dt_post['breath_frequency']) ? $dt_post['breath_frequency'] : '',
                    'oximetry_frequency'                        => isset($dt_post['oximetry_frequency']) ? $dt_post['oximetry_frequency'] : '',
                    'classification_oximetry_type_id'           => isset($dt_post['classification_oximetry_type_id']) ? $dt_post['classification_oximetry_type_id'] : '',
                    'temperature'                               => isset($dt_post['temperature']) ? $dt_post['temperature'] : '',
                    'classification_temperature_type_id'        => isset($dt_post['classification_temperature_type_id']) ? $dt_post['classification_temperature_type_id'] : '',
                    'blood_sugar'                               => isset($dt_post['blood_sugar']) ? $dt_post['blood_sugar'] : '',
                    'weight'                                    => isset($dt_post['weight']) ? $dt_post['weight'] : '',
                    'classification_weight_type_id'             => isset($dt_post['classification_weight_type_id']) ? $dt_post['classification_weight_type_id'] : '',
                    'height'                                    => isset($dt_post['height']) ? $dt_post['height'] : '',
                    'imt'                                       => isset($dt_post['imt']) ? $dt_post['imt'] : '',
                    'head_circumference'                        => isset($dt_post['head_circumference']) ? $dt_post['head_circumference'] : '',
                    'chest_circumference'                       => isset($dt_post['chest_circumference']) ? $dt_post['chest_circumference'] : '',
                    'arm_circumference'                         => isset($dt_post['arm_circumference']) ? $dt_post['arm_circumference'] : '',
                    'stomach_circumference'                     => isset($dt_post['stomach_circumference']) ? $dt_post['stomach_circumference'] : '',
                    'knee_circumference'                        => isset($dt_post['knee_circumference']) ? $dt_post['knee_circumference'] : '',
                    'sore_score'                                => $sore_score,
                    'ews'                                       => $ews,
                    'uub_type_id'                               => isset($dt_post['uub_type_id']) ? $dt_post['uub_type_id'] : '',
                    'eye_anemic_conjunctiva'                    => isset($dt_post['eye_anemic_conjunctiva']) ? $dt_post['eye_anemic_conjunctiva'] : '',
                    'eye_interic_sclera'                        => isset($dt_post['eye_interic_sclera']) ? $dt_post['eye_interic_sclera'] : '',
                    'eye_anythingelse'                          => isset($dt_post['eye_anythingelse']) ? $dt_post['eye_anythingelse'] : '',
                    'tht_hyperemic_pharynx'                     => isset($dt_post['tht_hyperemic_pharynx']) ? $dt_post['tht_hyperemic_pharynx'] : '',
                    'tht_tonsil'                                => isset($dt_post['tht_tonsil']) ? $dt_post['tht_tonsil'] : '',
                    'tht_ear'                                   => isset($dt_post['tht_ear']) ? $dt_post['tht_ear'] : '',
                    'tht_anythingelse'                          => isset($dt_post['tht_anythingelse']) ? $dt_post['tht_anythingelse'] : '',
                    'neck_lymph_gland'                          => isset($dt_post['neck_lymph_gland']) ? $dt_post['neck_lymph_gland'] : '',
                    'thorax_heart_type_id'                      => isset($dt_post['thorax_heart_type_id']) ? $dt_post['thorax_heart_type_id'] : '',
                    'thorax_murmur'                             => isset($dt_post['thorax_murmur']) ? $dt_post['thorax_murmur'] : '',
                    'thorax_gallop'                             => isset($dt_post['thorax_gallop']) ? $dt_post['thorax_gallop'] : '',
                    'thorax_anythingelse'                       => isset($dt_post['thorax_anythingelse']) ? $dt_post['thorax_anythingelse'] : '',
                    'pulmo_arry'                                => isset($dt_post['pulmo_arry']) ? implode(',', $dt_post['pulmo_arry']) : '',
                    'pulmo_anythingelse'                        => isset($dt_post['pulmo_anythingelse']) ? $dt_post['pulmo_anythingelse'] : '',
                    'pulmo_breath_movement'                     => isset($dt_post['pulmo_breath_movement']) ? $dt_post['pulmo_breath_movement'] : '',
                    'pulmo_breath_sound'                        => isset($dt_post['pulmo_breath_sound']) ? $dt_post['pulmo_breath_sound'] : '',
                    'extremity_acral_type_id'                   => isset($dt_post['extremity_acral_type_id']) ? $dt_post['extremity_acral_type_id'] : '',
                    'extremity_capillary_refilling_time'        => isset($dt_post['extremity_capillary_refilling_time']) ? $dt_post['extremity_capillary_refilling_time'] : '',
                    'extremity_anythingelse'                    => isset($dt_post['extremity_anythingelse']) ? $dt_post['extremity_anythingelse'] : '',
                    'abdomen_inspection_type_id'                => isset($dt_post['abdomen_inspection_type_id']) ? $dt_post['abdomen_inspection_type_id'] : '',
                    'abdomen_palpation_type_id'                 => isset($dt_post['abdomen_palpation_type_id']) ? $dt_post['abdomen_palpation_type_id'] : '',
                    'abdomen_sore_press'                        => isset($dt_post['abdomen_sore_press']) ? $dt_post['abdomen_sore_press'] : '',
                    'abdomen_sore_press_location'               => isset($dt_post['abdomen_sore_press_location']) ? $dt_post['abdomen_sore_press_location'] : '',
                    'abdomen_hepar'                             => isset($dt_post['abdomen_hepar']) ? $dt_post['abdomen_hepar'] : '',
                    'abdomen_bu'                                => isset($dt_post['abdomen_bu']) ? $dt_post['abdomen_bu'] : '',
                    'abdomen_lien'                              => isset($dt_post['abdomen_lien']) ? $dt_post['abdomen_lien'] : '',
                    'head'                                      => $head,
                    'head_hair_color'                           => $head_hair_color,
                    'head_hair_condition'                       => $head_hair_condition,
                    'eye_conjunctive'                           => $eye_conjunctive,
                    'eye_sclera'                                => $eye_sclera,
                    'eye_pupil'                                 => $eye_pupil,
                    'eye_light_reflex'                          => $eye_light_reflex,
                    'eye_blind_color'                           => $eye_blind_color,
                    'mouth_mucosa'                              => $mouth_mucosa,
                    'mouth_discoloration'                       => $mouth_discoloration,
                    'mouth_tongue'                              => $mouth_tongue,
                    'mouth_caries'                              => $mouth_caries,
                    'neck_kgb'                                  => $neck_kgb,
                    'neck_trachea_position'                     => $neck_trachea_position,
                    'neck_massa'                                => $neck_massa,
                    'thorax_insp_chest'                         => $thorax_insp_chest,
                    'thorax_insp_retraction'                    => $thorax_insp_retraction,
                    'thorax_palp_chest'                         => $thorax_palp_chest,
                    'thorax_palp_fremitus'                      => $thorax_palp_fremitus,
                    'thorax_palp_crepitation'                   => $thorax_palp_crepitation,
                    'thorax_ausk_pul_vasiculer'                 => $thorax_ausk_pul_vasiculer,
                    'thorax_ausk_pul_rhonki'                    => $thorax_ausk_pul_rhonki,
                    'thorax_ausk_pul_wheezing'                  => $thorax_ausk_pul_wheezing,
                    'thorax_ausk_cor_heart_sound'               => $thorax_ausk_cor_heart_sound,
                    'thorax_ausk_cor_murmur'                    => $thorax_ausk_cor_murmur,
                    'thorax_ausk_cor_gallop'                    => $thorax_ausk_cor_gallop,
                    'abdomen_supel'                             => $abdomen_supel,
                    'abdomen_bowel_sounds'                      => $abdomen_bowel_sounds,
                    'abdomen_tenderness'                        => $abdomen_tenderness,
                    'abdomen_hepar_lien'                        => $abdomen_hepar_lien,
                    'abdomen_other_physical_check'              => $abdomen_other_physical_check,
                    'extremity_acral'                           => $extremity_acral,
                    'extremity_crt'                             => $extremity_crt,
                    'extremity_edema'                           => $extremity_edema,
                    'genitalia'                                 => $genitalia,
                    'localist_status'                           => $localist_status,
                    'ophthal_status'                            => $ophthal_status,
                    'neurologist_status'                        => $neurologist_status,
                );
                $id_soap_o = $this->Soap_o->create($dt_soap_o);

                // SOAP_O_ACTION
                $dt_soap_o_action = array(
                    'soap_o_id'                 => $id_soap_o,
                    'canvasing_type_id'         => isset($dt_post['canvasing_type_id']) ? $dt_post['canvasing_type_id'] : '',
                    'canvasing_text'            => isset($dt_post['canvasing_text']) ? $dt_post['canvasing_text'] : '',
                    'attr1'                     => isset($dt_post['attr1']) ? $dt_post['attr1'] : '',
                    'attr2'                     => isset($dt_post['attr2']) ? $dt_post['attr2'] : ''
                );
                $this->Soap_o_action->create($dt_soap_o_action);

                // INPUT TTV
                $verbal_response = $dt_post['verbal_response'];
                $oxygen_type_id = $dt_post['oxygen_type_id'];
                $systolic_blood_pressure = $dt_post['systolic_blood_pressure'];
                $diastolic_blood_pressure = $dt_post['diastolic_blood_pressure'];
                $pulse_frequency = $dt_post['pulse_frequency'];
                $breath_frequency = $dt_post['breath_frequency'];
                $oximetry_frequency = $dt_post['oximetry_frequency'];
                $temperature = $dt_post['temperature'];
                $sore_score = $dt_post['sore_score'];
                $ews = $dt_post['ews'];
                $blood_sugar = $dt_post['blood_sugar'];
                $weight = $dt_post['weight'];
                $height = $dt_post['height'];

                $dt_vital_signs_monitoring = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'employee_nurse_id'             => $doctor_in_charge_id,
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

                $aware = $dt_post['aware'];
                $eye_score = $dt_post['eye_score'];
                $verbal_score = $dt_post['verbal_score'];
                $motor_score = $dt_post['motor_score'];
                $pupil_score = $dt_post['pupil_score'];
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
                    'employee_nurse_id'      => $doctor_in_charge_id,
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
                        'employee_nurse_id'           => $doctor_in_charge_id,
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

                // SOAP_A
                $dt_soap_a = array(
                    'soap_id'               => $id,
                    'development_notes'     => isset($dt_post['development_notes']) ? $dt_post['development_notes'] : '',
                    'anythingelse'          => isset($dt_post['soap_a_anythingelse']) ? $dt_post['soap_a_anythingelse'] : '',
                    'icd10_notes'           => isset($dt_post['icd10_notes']) ? $dt_post['icd10_notes'] : '',
                    'non_icd10'             => isset($dt_post['non_icd10']) ? implode('|', $dt_post['non_icd10']) : '',
                    'obgyn_diag_date'       => isset($dt_post['obgyn_diag_date']) ? $dt_post['obgyn_diag_date'] : '',                   
                    'obgyn_diag_clinis'     => isset($dt_post['obgyn_diag_clinis']) ? $dt_post['obgyn_diag_clinis'] : '',                 
                    'obgyn_diag_hpht'       => isset($dt_post['obgyn_diag_hpht']) ? $dt_post['obgyn_diag_hpht'] : '',                   
                    'obgyn_s_d'             => isset($dt_post['obgyn_s_d']) ? $dt_post['obgyn_s_d'] : '',                 
                    'obgyn_j_exam'          => isset($dt_post['obgyn_j_exam']) ? $dt_post['obgyn_j_exam'] : '',                  
                    'obgyn_j_pos'           => isset($dt_post['obgyn_j_pos']) ? $dt_post['obgyn_j_pos'] : '',                   
                    'obgyn_j_plas'          => isset($dt_post['obgyn_j_plas']) ? $dt_post['obgyn_j_plas'] : '',                  
                    'obgyn_j_k'             => isset($dt_post['obgyn_j_k']) ? $dt_post['obgyn_j_k'] : '',                 
                    'obgyn_j_sex'           => isset($dt_post['obgyn_j_sex']) ? $dt_post['obgyn_j_sex'] : '',                   
                    'obgyn_j_weight'        => isset($dt_post['obgyn_j_weight']) ? $dt_post['obgyn_j_weight'] : '',                    
                    'obgyn_size_usg'        => isset($dt_post['obgyn_size_usg']) ? $dt_post['obgyn_size_usg'] : '',                    
                    'obgyn_usg_desc'        => isset($dt_post['obgyn_usg_desc']) ? $dt_post['obgyn_usg_desc'] : '',                    
                    'obgyn_usg_file'        => isset($dt_post['obgyn_usg_file']) ? $this->base64ToFile($dt_post['obgyn_usg_file'], $id) : '',
                    'date_added'            => date("Y-m-d H:i:s")
                );
                $id_soap_a = $this->Soap_a->create($dt_soap_a);

                // SOAP_A_ACTION
                if(isset($dt_post['icd10'])) {
                    for ($i=0; $i < count($dt_post['icd10']) ; $i++) { 
                        // Check icd10_id
                        if($this->Icd10->read($dt_post['icd10'][$i]['icd10_id'], false) != []) {
                            $index = $dt_post['icd10'][$i]['index'];
                            $dt_soap_a_action = array(
                                'soap_a_id'         => $id_soap_a,
                                'icd10_id'          => $dt_post['icd10'][$i]['icd10_id'],
                                'index'             => $i
                            );
                            $this->Soap_a_action->create($dt_soap_a_action);
                        } else {
                            $this->REST_Return(404, 'There is no icd10 with id ' . $dt_post['icd10'][$i]['icd10_id']);
                            return;
                        }
                    }
                }

                $therapy_plan = $dt_post['therapy_plan'];
                $estimated_duration_treatment = $dt_post['estimated_duration_treatment'];
                $follow_up_care_arry = implode(',', $dt_post['follow_up_care_arry']);
                $diet_and_nutrition_type_id = $dt_post['diet_and_nutrition_type_id'];
                $diet_and_nutrition = $dt_post['diet_and_nutrition'];
                $wound_care_type_id = $dt_post['wound_care_type_id'];
                $wound_care = $dt_post['wound_care'];
                $medical_rehabilitation_type_id = $dt_post['medical_rehabilitation_type_id'];
                $medical_rehabilitation = $dt_post['medical_rehabilitation'];
                $pain_management_type_id = $dt_post['pain_management_type_id'];
                $pain_management = $dt_post['pain_management'];
                $discharge_planning_other = $dt_post['discharge_planning_other'];

                // SOAP_P
                $dt_soap_p = array(
                    'soap_id'                        => $id,
                    'doctor_in_charge_id'            => $doctor_in_charge_id,
                    'therapy_plan'                   => $therapy_plan,
                    'estimated_duration_treatment'   => $estimated_duration_treatment,
                    'follow_up_care_arry'            => $follow_up_care_arry,
                    'diet_and_nutrition_type_id'     => $diet_and_nutrition_type_id,
                    'diet_and_nutrition'             => $diet_and_nutrition,
                    'wound_care_type_id'             => $wound_care_type_id,
                    'wound_care'                     => $wound_care,
                    'medical_rehabilitation_type_id' => $medical_rehabilitation_type_id,
                    'medical_rehabilitation'         => $medical_rehabilitation,
                    'pain_management_type_id'        => $pain_management_type_id,
                    'pain_management'                => $pain_management,
                    'discharge_planning_other'       => $discharge_planning_other,
                );
                $id_soap_p = $this->Soap_p->create($dt_soap_p);

                // SOAP_P_ACTION
                if(isset($dt_post['icd9cm'])) {
                    for ($i=0; $i < count($dt_post['icd9cm']) ; $i++) { 
                        // Check icd9cm_id
                        if($this->Icd9cm->read($dt_post['icd9cm'][$i]['icd9cm_id'], false) != []) {
                            $dt_soap_p_action = array(
                                'soap_p_id'         => $id_soap_p,
                                'icd9cm_id'         => $dt_post['icd9cm'][$i]['icd9cm_id']
                            );
                            $this->Soap_p_action->create($dt_soap_p_action);
                        } else {
                            $this->REST_Return(404, 'There is no icd9cm with id ' . $dt_post['icd9cm'][$i]['icd9cm_id']);
                            return;
                        }
                    }
                }

                // INIT_PATIENT_ALLERGY
                $norm = $dt_reg->norm;
                $allergies_arr = $dt_post['allergies'];

                for ($i=0; $i < count($allergies_arr); $i++) {
                    $allergy_name = $allergies_arr[$i]['name'];
                    $allergy_type_allergy_id = $allergies_arr[$i]['type_allergy_id'];
                    $allergy_date_tragedy = $allergies_arr[$i]['date_tragedy'];
                    $allergy_side_effect = $allergies_arr[$i]['side_effect'];
                    $allergy_reaction = $allergies_arr[$i]['reaction'];
                    $allergy_severity = $allergies_arr[$i]['severity'];

                    $dt_patient_allergy = array(
                        'norm'              => $norm,
                        'type_allergy_id'   => $allergy_type_allergy_id,
                        'name'              => $allergy_name,
                        'date_tragedy'      => $allergy_date_tragedy,
                        'side_effect'       => $allergy_side_effect,
                        'reaction'          => $allergy_reaction,
                        'severity'          => $allergy_severity,
                    );

                    $select = 'a.id';
                    $where = array('name' => $allergy_name);
                    $allergy = $this->Allergy->select(0, $select, $where);

                    if(count($allergy) > 0) {
                        $allergy_id = $allergy[0]->id;

                        $dt_patient_allergy['allergy_id'] = $allergy_id;

                        $this->Patient_allergy->replace($dt_patient_allergy);
                    } else {
                        $dt_allergy = array(
                            'institute_branch_id'   => $institute_branch_id,
                            'name'                  => $allergy_name,
                        );

                        $allergy_id = $this->Allergy->create($dt_allergy);

                        $dt_patient_allergy['allergy_id'] = $allergy_id;
                        
                        $this->Patient_allergy->replace($dt_patient_allergy);
                    }
                }

                // INIT_TRANSACTION_DOCTOR
                if(isset($dt_post['transaction_doctor'])) {
                    for ($i=0; $i < count($dt_post['transaction_doctor']); $i++) { 
                        $costing = $this->Costing_services_class->read($dt_post['transaction_doctor'][$i]['costing_id'],false);
                        if($costing != []) {
                            $dt_transaction_doctor = array(
                                'soap_id'               => $id,
                                'doctor_in_charge_id'   => $doctor_in_charge_id,
                                'costing_id'            => $dt_post['transaction_doctor'][$i]['costing_id'],
                                'costing_name'          => $costing[0]->name,
                                'var_1'                 => isset($dt_post['transaction_doctor'][$i]['var_1']) ? $dt_post['transaction_doctor'][$i]['var_1'] : '',
                                'var_2'                 => isset($dt_post['transaction_doctor'][$i]['var_2']) ? $dt_post['transaction_doctor'][$i]['var_2'] : '',
                                'var_3'                 => isset($dt_post['transaction_doctor'][$i]['var_1']) ? $dt_post['transaction_doctor'][$i]['var_1']*$dt_post['transaction_doctor'][$i]['var_2'] : '',
                                'doctor_ref_id'         => isset($dt_post['transaction_doctor'][$i]['doctor_ref_id']) ? $dt_post['transaction_doctor'][$i]['doctor_ref_id'] : '',
                                'notes'                 => isset($dt_post['transaction_doctor'][$i]['notes']) ? $dt_post['transaction_doctor'][$i]['notes'] : '',
                                'date_added'            => date("Y-m-d H:i:s",time())
                            );
                            $this->Transaction_doctor->create($dt_transaction_doctor);
                        } else {
                            $this->REST_Return(404, 'There is no costing with id ' . $dt_post['transaction_doctor'][$i]['costing_id']);
                            return;
                        }
                    }
                }

                // INIT_TRANSACTION_PHYSIOTHERAPY
                if(isset($dt_post['transaction_physiotherapy'])) {
                    if(count($dt_post['transaction_physiotherapy']) > 0){
                        for ($i=0; $i < count($dt_post['transaction_physiotherapy']); $i++) { 
                            $costing = $this->Costing->read($dt_post['transaction_physiotherapy'][$i]['costing_id'],false);
                            if($costing != []) {
                                $dt_transaction_physiotherapy = array(
                                    'soap_id'           => $id,
                                    'costing_id'        => $dt_post['transaction_physiotherapy'][$i]['costing_id'],
                                    'costing_name'      => $costing[0]->name,
                                    'var_1'             => isset($dt_post['transaction_physiotherapy'][$i]['var_1']) ? $dt_post['transaction_physiotherapy'][$i]['var_1'] : '',
                                    'var_2'             => isset($dt_post['transaction_physiotherapy'][$i]['var_2']) ? $dt_post['transaction_physiotherapy'][$i]['var_2'] : '',
                                    'var_3'             => isset($dt_post['transaction_physiotherapy'][$i]['var_3']) ? $dt_post['transaction_physiotherapy'][$i]['var_3'] : '',
                                    'notes'             => isset($dt_post['transaction_physiotherapy'][$i]['notes']) ? $dt_post['transaction_physiotherapy'][$i]['notes'] : '',
                                    'date_added'        => date("Y-m-d H:i:s",time())
                                );
                                $this->Transaction_physiotherapy->create($dt_transaction_physiotherapy);
                            } else {
                                $this->REST_Return(404, 'There is no costing with id ' . $dt_post['transaction_physiotherapy'][$i]['costing_id']);
                                return;
                            }
                        }
                    }
                }

                // INIT_TRANSACTION_PRESCRIPTION
                if(isset($dt_post['form_prescription'])) {
                    if(count($dt_post['form_prescription']) > 0) {
                        // count no_order & queue
                        $depo_id = $dt_post['pharmacy_depo_id'];

                        if ($dt_reg->health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') {
                            $prefix = 'PRJ';
                            $depo_id = '972dcaba-cea0-4515-9756-75ea675c6322';
                            $epoch = time();
                            $dt_queue = array(
                                'var_2' => date('Y', $epoch),
                                'var_3' => date('m', $epoch),
                                'var_4' => date('d', $epoch),
                                'var_5' => date('Y-m-d', $epoch),
                            );
                        } else {
                            $prefix = 'PRS';
                            $epoch = time();
                            $dt_queue = array(
                                'var_2' => date('Y', $epoch),
                                'var_3' => date('m', $epoch),
                                'var_4' => date('d', $epoch),
                                'var_5' => date('Y-m-d', $epoch),
                            );
                        }

                        $no_order = counter_queue($prefix, $dt_queue, 4);
                        $queue = round((int)substr($no_order, -4));
                        $queueresep = $queue;

                        $dt_prescription = array(
                            'norm'                      => $dt_reg->norm,
                            'institute_branch_id'       => $institute_branch_id,
                            'pharmacy_depo_id'          => $dt_post['pharmacy_depo_id'] == '' ? $depo_id : $depo_id,
                            'soap_p_id'                 => $id_soap_p,
                            'health_facility_item_id'   => $dt_reg->health_facility_item_id,
                            'doctor_in_charge_id'       => $doctor_in_charge_id,
                            'no_order'                  => $no_order,
                            // 'var_1'                     => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                            // 'var_2'                     => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                            // 'var_3'                     => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                            'date_added'                => date("Y-m-d H:i:s",time()),
                            'date_0'                    => date("Y-m-d H:i:s",time()),
                            'flag'                      => 0,
                            'queue'                     => $queue,
                        );
                        if ($dt_reg->code_booking && $dt_reg->health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') {
                            $dt_prescription['code_booking'] = $dt_reg->code_booking;
                        }
                        $id_prescription = $this->Prescription->create($dt_prescription);
    
                        // INIT_PRESCRIPTION_ITEM
                        for ($i=0; $i < count($dt_post['form_prescription']); $i++) { 
                            for ($ip=0; $ip < count($dt_post['form_prescription_parent']); $ip++) { 
                                if ($dt_post['form_prescription_parent'][$ip]['id'] == $dt_post['form_prescription'][$i]['prescription_type_id']) {
                                    $dt_prescription_item = array(
                                        'pharmacy_prescription_id'      => $id_prescription,
                                        'pharmacy_catalog_id'           => $dt_post['form_prescription'][$i]['pharmacy_catalog_id'],
                                        'prescription_item_type_id'     => $dt_post['form_prescription'][$i]['prescription_type_id'],
                                        'prescription_item_type_used'   => $dt_post['form_prescription_parent'][$ip]['used'],
                                        'prescription_item_type_value'  => $dt_post['form_prescription_parent'][$ip]['value'],
                                        'package_id'                    => $dt_post['form_prescription'][$i]['package_id'],
                                        'package_name'                  => $dt_post['form_prescription'][$i]['package_name'],
                                        'dose'                          => isset($dt_post['form_prescription'][$i]['dose']) ? $dt_post['form_prescription'][$i]['dose'] : '',
                                        'dose_doctor'                   => isset($dt_post['form_prescription'][$i]['sum_dose']) ? intval($dt_post['form_prescription'][$i]['sum_dose']) : '',
                                        'qty_doctor'                    => isset($dt_post['form_prescription'][$i]['value_package']) ? $dt_post['form_prescription'][$i]['value_package'] : '',
                                        'qty'                           => isset($dt_post['form_prescription'][$i]['value_package']) ? $dt_post['form_prescription'][$i]['value_package'] : '',
                                        'qty_copy'                      => isset($dt_post['form_prescription'][$i]['qty_copy']) ? $dt_post['form_prescription'][$i]['qty_copy'] : '',
                                        // 'var_1'                         => isset($dt_post['form_prescription'][$i]['var_1']) ? $dt_post['form_prescription'][$i]['var_1'] : '',
                                        // 'var_2'                         => isset($dt_post['form_prescription'][$i]['var_2']) ? $dt_post['form_prescription'][$i]['var_2'] : '',
                                        // 'var_3'                         => isset($dt_post['form_prescription'][$i]['var_3']) ? $dt_post['form_prescription'][$i]['var_3'] : '',
                                        'sort'                          => isset($dt_post['form_prescription'][$i]['sort']) ? $dt_post['form_prescription'][$i]['sort'] : '',
                                        'used'                          => isset($dt_post['form_prescription'][$i]['used']) ? $dt_post['form_prescription'][$i]['used'] : '',
                                        'desc'                          => isset($dt_post['form_prescription'][$i]['desc']) ? $dt_post['form_prescription'][$i]['desc'] : '',
                                    );
                                }
                            }
                            $this->Prescription_item->replace($dt_prescription_item);
                        }
                    }
                }

                // INIT_LABORATORY_REQUEST
                if(isset($dt_post['laboratory_request_items'])) {
                    if(count($dt_post['laboratory_request_items']) > 0){
                        // TODO - DEPRECATED, DELETE SOON!
                        // // Count no_request
                        // $this->Laboratory_request->whereFields['a.is_delete'] = 0;
                        // $this->Laboratory_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Laboratory_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Laboratory_request->orderFields = array('a.no_request' => 'DESC');
                        // $this->Laboratory_request->limit = 1;
                        // $tmp = $this->Laboratory_request->read(0,false);
                        // $waktu = date("Ymd",time());
                        // if($tmp != []) {
                        //     $no_request = 'LAB' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $no_request = 'LAB' . $waktu . '0001';
                        //     }
                        // } else {
                        //     $no_request = 'LAB' . $waktu . '0001';
                        // }

                        // // Count queue
                        // $this->Laboratory_request->whereFields['a.is_delete'] = 0;
                        // $this->Laboratory_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Laboratory_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Laboratory_request->orderFields = array('a.queue' => 'DESC');
                        // $this->Laboratory_request->limit = 1;
                        // $tmp = $this->Laboratory_request->read(0,false);
                        // if($tmp != []) {
                        //     $queue = $tmp[0]->queue + 1;
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $queue = 1;
                        //     }
                        // } else {
                        //     $queue = 1;
                        // }

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
                            'soap_id'               => $id,
                            'doctor_in_charge_id'   => $doctor_in_charge_id,
                            'no_request'            => $no_request,
                            'queue'                 => $queue,
                            'clinical_indications'  => $laboratory_clinical_indications,
                            'date_request'          => date('Y-m-d H:i:s',time())
                        );
                        $id_laboratory_request = $this->Laboratory_request->create($dt_laboratory_request);
                        // INIT_LABORATORY_REQUEST_ITEM
                        for ($i=0; $i < count($dt_post['laboratory_request_items']) ; $i++) {
                            $laboratory_package = $this->Laboratory_package->read($dt_post['laboratory_request_items'][$i]['laboratory_package_id'], false);
                            $service_class = $this->Service_class->read($dt_post['laboratory_request_items'][$i]['service_class_id']);
                            // Check laboratory_package_id & service_class_id
                            if($laboratory_package != [] && $service_class != []) {
                                $costing = $this->Costing->read($laboratory_package[0]->costing_id,false);
                                $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                                // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                                $costing_services_class = $this->Costing_services_class->read(0,false);
                                $dt_laboratory_request_item = array(
                                    'laboratory_request_id'     => $id_laboratory_request,
                                    'laboratory_package_id'     => $dt_post['laboratory_request_items'][$i]['laboratory_package_id'],
                                    'service_class_id'          => $service_class[0]->id,
                                    'var_1'                     => $costing_services_class[0]->var_1,
                                    'var_2'                     => $costing_services_class[0]->var_2,
                                    'var_3'                     => $costing_services_class[0]->var_3,
                                    'costing_id'                => $costing[0]->id,
                                    'costing_name'              => $costing[0]->name,
                                    'date_request'              => date('Y-m-d H:i:s'),
                                    'status'                    => 1
                                );
                                // var_dump($dt_laboratory_request_item);die();
                                $this->Laboratory_request_item->create($dt_laboratory_request_item);
                            } else {
                                $this->REST_Return(404, 'There is no laboratory_package with id ' . $dt_post['laboratory_request_items'][$i]['laboratory_package_id'] . ' or service_class with id ' . $dt_post['laboratory_request_items'][$i]['service_class_id']);
                                return;
                            }
                        }
                    }
                }

                // INIT_RADIOLOGY_REQUEST
                if(isset($dt_post['radiology_request_items'])) {
                    if(count($dt_post['radiology_request_items']) > 0){
                        // TODO - DEPRECATED, DELETE SOON
                        // // Count no_request
                        // $this->Radiology_request->whereFields['a.is_delete'] = 0;
                        // $this->Radiology_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Radiology_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Radiology_request->orderFields = array('a.no_request' => 'DESC');
                        // $this->Radiology_request->limit = 1;
                        // $tmp = $this->Radiology_request->read(0,false);
                        // $waktu = date("Ymd",time());
                        // if($tmp != []) {
                        //     $no_request = 'RAD' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $no_request = 'RAD' . $waktu . '0001';
                        //     }
                        // } else {
                        //     $no_request = 'RAD' . $waktu . '0001';
                        // }

                        // // Count queue
                        // $this->Radiology_request->whereFields['a.is_delete'] = 0;
                        // $this->Radiology_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Radiology_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Radiology_request->orderFields = array('a.queue' => 'DESC');
                        // $this->Radiology_request->limit = 1;
                        // $tmp = $this->Radiology_request->read(0,false);
                        // if($tmp != []) {
                        //     $queue = $tmp[0]->queue + 1;
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $queue = 1;
                        //     }
                        // } else {
                        //     $queue = 1;
                        // }

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
                            'soap_id'               => $id,
                            'doctor_in_charge_id'   => $doctor_in_charge_id,
                            'no_request'            => $no_request,
                            'queue'                 => $queue,
                            'clinical_indications'  => $radiology_clinical_indications,
                            'date_request'          => date('Y-m-d H:i:s',time())
                        );
                        $id_radiology_request = $this->Radiology_request->create($dt_radiology_request);
                        // INIT_RADIOLOGY_REQUEST_ITEM
                        for ($i=0; $i < count($dt_post['radiology_request_items']) ; $i++) {
                            $radiology_package = $this->Radiology_package->read($dt_post['radiology_request_items'][$i]['radiology_package_id'], false);
                            $service_class = $this->Service_class->read($dt_post['radiology_request_items'][$i]['service_class_id']);
                            // Check radiology_package_id & service_class_id
                            if($radiology_package != [] && $service_class != []) {
                                $costing = $this->Costing->read($radiology_package[0]->costing_id,false);
                                $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                                // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                                $costing_services_class = $this->Costing_services_class->read(0,false);
                                $dt_radiology_request_item = array(
                                    'radiology_request_id'      => $id_radiology_request,
                                    'radiology_package_id'      => $dt_post['radiology_request_items'][$i]['radiology_package_id'],
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
                                $this->REST_Return(404, 'There is no radiology_package with id ' . $dt_post['radiology_request_items'][$i]['radiology_package_id'] . ' or service_class with id ' . $dt_post['radiology_request_items'][$i]['service_class_id']);
                                return;
                            }
                        }
                    }
                }

                // INIT_PHYSIOTHERAPY_REQUEST
                if(isset($dt_post['physiotherapy_request_items'])) {
                    if(count($dt_post['physiotherapy_request_items']) > 0){
                        // NOTE - DEPRECATED, DELETED SOON!
                        // // Count no_request
                        // $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
                        // $this->Physiotherapy_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Physiotherapy_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Physiotherapy_request->orderFields = array('a.no_request' => 'DESC');
                        // $this->Physiotherapy_request->limit = 1;
                        // $tmp = $this->Physiotherapy_request->read(0,false);
                        // $waktu = date("Ymd",time());
                        // if($tmp != []) {
                        //     $no_request = 'RHM' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $no_request = 'RHM' . $waktu . '0001';
                        //     }
                        // } else {
                        //     $no_request = 'RHM' . $waktu . '0001';
                        // }

                        // // Count queue
                        // $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
                        // $this->Physiotherapy_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Physiotherapy_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Physiotherapy_request->orderFields = array('a.queue' => 'DESC');
                        // $this->Physiotherapy_request->limit = 1;
                        // $tmp = $this->Physiotherapy_request->read(0,false);
                        // if($tmp != []) {
                        //     $queue = $tmp[0]->queue + 1;
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $queue = 1;
                        //     }
                        // } else {
                        //     $queue = 1;
                        // }

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
                            'soap_id'               => $id,
                            'doctor_in_charge_id'   => $doctor_in_charge_id,
                            'no_request'            => $no_request,
                            'queue'                 => $queue,
                            'date_request'          => date('Y-m-d H:i:s',time())
                        );
                        $id_physiotherapy_request = $this->Physiotherapy_request->create($dt_physiotherapy_request);
                        // INIT_PHYSIOTHERAPY_REQUEST_ITEM
                        for ($i=0; $i < count($dt_post['physiotherapy_request_items']) ; $i++) {
                            $physiotherapy_package = $this->Physiotherapy_package->read($dt_post['physiotherapy_request_items'][$i]['physiotherapy_package_id'], false);
                            $service_class = $this->Service_class->read($dt_post['physiotherapy_request_items'][$i]['service_class_id']);
                            // Check physiotherapy_package_id & service_class_id
                            if($physiotherapy_package != [] && $service_class != []) {
                                $costing = $this->Costing->read($physiotherapy_package[0]->costing_id,false);
                                $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
                                // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
                                $costing_services_class = $this->Costing_services_class->read(0,false);
                                $dt_physiotherapy_request_item = array(
                                    'physiotherapy_request_id'  => $id_physiotherapy_request,
                                    'physiotherapy_package_id'  => $dt_post['physiotherapy_request_items'][$i]['physiotherapy_package_id'],
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
                                $this->REST_Return(404, 'There is no physiotherapy_package with id ' . $dt_post['physiotherapy_request_items'][$i]['physiotherapy_package_id'] . ' or service_class with id ' . $dt_post['physiotherapy_request_items'][$i]['service_class_id']);
                                return;
                            }
                        }
                    }
                }

                // MEDICAL_REHABILITATION
                $is_medical_rehabilitation = $dt_post['is_medical_rehabilitation'];

                if ($is_medical_rehabilitation == 1) {
                    $service_date = $dt_post['service_date'];
                    $anamnesa = $dt_post['anamnesa'];
                    $functional_testing = $dt_post['functional_testing'];
                    $functional_diagnosis = $dt_post['functional_diagnosis'];
                    $medical_diagnosis = $dt_post['medical_diagnosis'];
                    $support_examination = $dt_post['support_examination'];
                    $advice_rehab = $dt_post['advice_rehab'];
                    $evaluation = $dt_post['evaluation'];
                    $suspected_disease_type_id = $dt_post['suspected_disease_type_id'];
                    $suspected_disease = $dt_post['suspected_disease'];
                    $result = $dt_post['result'];
                    $conclusion = $dt_post['conclusion'];
                    $recommendation = $dt_post['recommendation'];

                    $dt_medical_rehabilitation = array(
                        'soap_id'                   => $id,
                        'service_date'              => $service_date,
                        'anamnesa'                  => $anamnesa,
                        'functional_testing'        => $functional_testing,
                        'functional_diagnosis'      => $functional_diagnosis,
                        'medical_diagnosis'         => $medical_diagnosis,
                        'support_examination'       => $support_examination,
                        'advice'                    => $advice_rehab,
                        'evaluation'                => $evaluation,
                        'suspected_disease_type_id' => $suspected_disease_type_id,
                        'suspected_disease'         => $suspected_disease,
                        'result'                    => $result,
                        'conclusion'                => $conclusion,
                        'recommendation'            => $recommendation,
                        'user_added'                => $user_id,
                        'date_added'                => date('Y-m-d H:i:s',time()),
                        'date_updated'              => date('Y-m-d H:i:s',time()),
                    );

                    $this->Medical_rehabilitation->create($dt_medical_rehabilitation);
                }

                // HEMODIALYSIS
                $is_hemodialysis = $dt_post['is_hemodialysis'];

                if ($is_hemodialysis == 1) {
                    $main_diagnosis = $dt_post['main_diagnosis'];
                    $etiology = $dt_post['etiology'];
                    $comorbidities = $dt_post['comorbidities'];
                    $prescription_hd = $dt_post['prescription_hd'];
                    $frequency_hd = $dt_post['frequency_hd'];
                    $vascular_access = $dt_post['vascular_access'];

                    $dt_hemodialysis_initiation = array(
                        'soap_id'           => $id,
                        'main_diagnosis'    => $main_diagnosis,
                        'etiology'          => $etiology,
                        'comorbidities'     => $comorbidities,
                        'prescription_hd'   => $prescription_hd,
                        'frequency_hd'      => $frequency_hd,
                        'vascular_access'   => $vascular_access,
                        'user_added'        => $user_id,
                        'date_added'        => date('Y-m-d H:i:s',time()),
                        'date_updated'      => date('Y-m-d H:i:s',time()),
                    );
                
                    $this->Hemodialysis_initiation->create($dt_hemodialysis_initiation);
                }

                // MEDICAL_CHECK_UP
                $is_medical_check_up = $dt_post['is_medical_check_up'];

                if ($is_medical_check_up == 1) {
                    $fit_to_work = $dt_post['fit_to_work'];
                    $consultation = $dt_post['consultation'];

                    $dt_medical_check_up = array(
                        'soap_id'           => $id,
                        'consultation'      => $consultation,
                        'fit_to_work'       => $fit_to_work,
                        'user_added'        => $user_id,
                        'date_added'        => date('Y-m-d H:i:s',time()),
                        'date_updated'      => date('Y-m-d H:i:s',time()),
                    );
                
                    $this->Medical_check_up->create($dt_hemodialysis_initiation);
                }

                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $jenisresep = 'Non racikan';
                    if ($dt_reg->code_booking && $dt_reg->sts == 4) {
                        $dt_Registration = array(
                            'id' => $dt_reg->id,
                            'registration_id' => $dt_reg->registration_id,
                            'health_facility_item_id' => $dt_reg->health_facility_item_id,
                            'sts' => 5,
                            'date_task_5' => date('Y-m-d H:i:s'),
                            'done_doctor_stamp' => date('Y-m-d H:i:s')
                        );
                        $this->Registration_destination->replace($dt_Registration);
                        $this->load->library('Vclaim_antrol','vclaim_antrol');
                        $update_antrean_5 = array(
                            'kodebooking'       => $dt_reg->code_booking,
                            'taskid'            => 5,
                            'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s", strtotime('+1 minutes')))
                        );
                        if ($queueresep) {
                            $update_antrean_5['jenisresep'] = $jenisresep;
                        } else {
                            $update_antrean_5['jenisresep'] = 'Tidak ada';
                        }
                        // var_dump($update_antrean_5);
                        // die();
                        $updatewaktu_antrean_5 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_5));
                        // $json_update_antrean_5 = json_decode($updatewaktu_antrean_5, true);
                        // $json_update_antrean_5 = json_decode($json_update_antrean_5);
                        // // jika gagal update antrean bpjs
                        // // var_dump($json_update_antrean_5);
                        // if($json_update_antrean_5->metadata->code != 200) {
                        //     // rollback
                        //     $this->db->trans_rollback();
                        //     $this->REST_Return(422, $json_update_antrean_5->metadata->message, $json_update_antrean_5->metadata->message);
                        //     return;
                        // }
                        if($queueresep){
                            $farmasiAntrean = array(
                                "kodebooking" => $dt_reg->code_booking,
                                "jenisresep" => $jenisresep == 'Non racikan' ? 'non racikan' : 'racikan',
                                "nomorantrean" => $queueresep,
                                "keterangan" => "resep"
                            );
                            $resepantrean = $this->vclaim_antrol->request("/antrean/farmasi/add", "POST", json_encode($farmasiAntrean));
                            // $resepantrean = json_decode($resepantrean, true);
                            // $resepantrean = json_decode($resepantrean);
                            // if($resepantrean->metadata->code != 200) { // rollback
                            //     $this->db->trans_rollback();
                            //     $this->REST_Return(422, $resepantrean->metadata->message, $resepantrean->metadata->message);
                            //     return;
                            // }
                        }
                    }
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

    private function tomiliseconds($tanggal)
    {
        date_default_timezone_set('Asia/Jakarta');
        return strtotime($tanggal) * 1000;
    }

    public function create_full_book_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post     = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->create_full_book_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        } 

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        // INIT_SOAP
        $registration_destination_id = $dt_post['registration_destination_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

        $select = "a.id, a.sts, a.health_facility_item_id, a.registration_id, r.norm";
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        $doctor_in_charge = $this->Employee->select_id($doctor_in_charge_id);

        if ($registration_destination == []) {
            $this->REST_Return(404, "there is no registration_destination with id " . $registration_destination_id);
            return;
        }

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;
        $norm = $registration_destination[0]->norm;

        if ($doctor_in_charge == "") {
            $this->REST_Return(404, "there is no employee with id " . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_soap = array(
            'registration_destination_id'  => $registration_destination_id,
            'doctor_in_charge_id'          => $doctor_in_charge_id,
            'user_added'                   => $user_id,
            'date_added'                   => date("Y-m-d H:i:s",time()),
            'is_booking'                   => 1
        );
        $id = $this->$model->create($dt_soap);
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create soap', $response_data);
            return;
        }

        $registration_destination_sts = $registration_destination[0]->sts;
        $registration_destination_health_facility_item_id = $registration_destination[0]->health_facility_item_id;
        $registration_destination_registration_id = $registration_destination[0]->registration_id;

        // BRIDGING BPJS
        if ($registration_destination_sts == 4) {
            $dt_registration_destination = array(
                'id'                        => $dt_post['registration_destination_id'],
                'health_facility_item_id'   => $registration_destination_health_facility_item_id,
                'registration_id'           => $registration_destination_registration_id, 
                'done_doctor_stamp'         => date('Y-m-d H:i:s',time()), 
                'sts'                       => 5
            );
            $this->Registration_destination->replace($dt_registration_destination);

            if ($this->db->trans_status() === false) {
                $response_data = array(
                    'error'     => $this->db->error(),
                    'data'      => $dt_registration_destination,
                    'query'     => $this->db->last_query()
                );
                $this->REST_Return(500, 'error while trying to BRIDGING BPJS replace registration_destination', $response_data);
                return;
            }
        }

        // INIT_SOAP_A
        $development_notes = $dt_post['development_notes'];
        $anythingelse = $dt_post['anythingelse'];
        $icd10_notes = $dt_post['icd10_notes'];
        $obgyn_diag_date = $dt_post['obgyn_diag_date'];
        $obgyn_diag_clinis = $dt_post['obgyn_diag_clinis'];
        $obgyn_diag_hpht = $dt_post['obgyn_diag_hpht'];
        $obgyn_s_d = $dt_post['obgyn_s_d'];
        $obgyn_j_exam = $dt_post['obgyn_j_exam'];
        $obgyn_j_pos = $dt_post['obgyn_j_pos'];
        $obgyn_j_plas = $dt_post['obgyn_j_plas'];
        $obgyn_j_k = $dt_post['obgyn_j_k'];
        $obgyn_j_sex = $dt_post['obgyn_j_sex'];
        $obgyn_j_weight = $dt_post['obgyn_j_weight'];
        $obgyn_size_usg = $dt_post['obgyn_size_usg'];
        $obgyn_usg_desc = $dt_post['obgyn_usg_desc'];
        $obgyn_usg_file = $dt_post['obgyn_usg_file'];

        $dt_soap_a = array(
            'soap_id'               => $id,
            'development_notes'     => $development_notes,
            'anythingelse'          => $anythingelse,
            'icd10_notes'           => $icd10_notes,
            'obgyn_diag_date'       => $obgyn_diag_date,                   
            'obgyn_diag_clinis'     => $obgyn_diag_clinis,                 
            'obgyn_diag_hpht'       => $obgyn_diag_hpht,                   
            'obgyn_s_d'             => $obgyn_s_d,                 
            'obgyn_j_exam'          => $obgyn_j_exam,                  
            'obgyn_j_pos'           => $obgyn_j_pos,                   
            'obgyn_j_plas'          => $obgyn_j_plas,                  
            'obgyn_j_k'             => $obgyn_j_k,                 
            'obgyn_j_sex'           => $obgyn_j_sex,                   
            'obgyn_j_weight'        => $obgyn_j_weight,                    
            'obgyn_size_usg'        => $obgyn_size_usg,                    
            'obgyn_usg_desc'        => $obgyn_usg_desc,                    
            'obgyn_usg_file'        => isset($obgyn_usg_file) ? $this->base64ToFile($obgyn_usg_file, $id) : '',
            'date_added'            => date("Y-m-d H:i:s")
        );
        $soap_a_id = $this->Soap_a->create($dt_soap_a);

        // INIT_SOAP_A_ACTION
        $icd10_arr = $dt_post['icd10'];
        for ($i=0; $i < count($icd10_arr) ; $i++) { 
            if (!isset($icd10_arr[$i]['icd10_id'])) {
                $this->REST_Return(400, "icd10_id field in icd10 is required!");
                return;
            }

            $icd10_id = $icd10_arr[$i]['icd10_id'];
            $index = $icd10_arr[$i]['index'];

            $icd10 = $this->Icd10->select_id($icd10_id);

            if ($icd10 == "") {
                $this->REST_Return(404, "there is no icd10 with id " . $icd10_id);
                return;
            }

            $dt_soap_a_action = array(
                'soap_a_id'         => $soap_a_id,
                'icd10_id'          => $icd10_id,
                'index'             => $i
            );
            $this->Soap_a_action->create($dt_soap_a_action);
        }

        // INIT_LABORATORY_REQUEST
        $laboratory_request_items_arr = $dt_post['laboratory_request_items'];

        if ($laboratory_request_items_arr != []) {
            $date_request = $dt_post['date_request'];

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

            $dt_laboratory_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $id,
                'doctor_in_charge_id'   => $doctor_in_charge_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'date_request'          => $date_request
            );
            $laboratory_request_id = $this->Laboratory_request->create($dt_laboratory_request);

            // INIT_LABORATORY_REQUEST_ITEM
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

        // INIT_RADIOLOGY_REQUEST
        $radiology_request_items_arr = $dt_post['radiology_request_items'];

        if ($radiology_request_items_arr != []) {
            $date_request = $dt_post['date_request'];

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

            $dt_radiology_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $id,
                'doctor_in_charge_id'   => $doctor_in_charge_id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'date_request'          => $date_request
            );
            $radiology_request_id = $this->Radiology_request->create($dt_radiology_request);

            // INIT_RADIOLOGY_REQUEST_ITEM
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
                    'radiology_request_id'     => $radiology_request_id,
                    'radiology_package_id'     => $radiology_package_id,
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

        // INIT_PHYSIOTHERAPY_REQUEST
        $physiotherapy_request_items_arr = $dt_post['physiotherapy_request_items'];

        if ($physiotherapy_request_items_arr != []) {
            $date_request = $dt_post['date_request'];

            // count no_request & queue
            $prefix = 'RHM';
            $epoch = strtotime($date_request);
            $dt_queue = array(
                'var_2' => date('Y', $epoch),
                'var_3' => date('m', $epoch),
                'var_4' => date('d', $epoch),
                'var_5' => date('Y-m-d', $epoch),
            );

            $no_request = counter_queue($prefix, $dt_queue, 4);
            $queue = round((int)substr($no_request, -4));

            $physiotherapy_request = array(
                'institute_branch_id'   => $institute_branch_id,
                'soap_id'               => $id,
                'no_request'            => $no_request,
                'queue'                 => $queue,
                'date_request'          => $date_request
            );
            $physiotherapy_request_id = $this->Physiotherapy_request->create($physiotherapy_request);

            // INIT_PHYSIOTHERAPY_REQUEST_ITEM
            for ($i=0; $i < count($physiotherapy_request_items_arr) ; $i++) {
                if (!isset($physiotherapy_request_items_arr[$i]['physiotherapy_package_id'])) {
                    $this->REST_Return(400, "physiotherapy_package_id field in physiotherapy_request_items is required!");
                    return;
                }

                if (!isset($physiotherapy_request_items_arr[$i]['service_class_id'])) {
                    $this->REST_Return(400, "service_class_id field in physiotherapy_request_items is required!");
                    return;
                }

                $physiotherapy_package_id = $physiotherapy_request_items_arr[$i]['physiotherapy_package_id'];
                $service_class_id = $physiotherapy_request_items_arr[$i]['service_class_id'];

                $physiotherapy_package = $this->Physiotherapy_package->read($physiotherapy_package_id, false);
                $service_class = $this->Service_class->read($service_class_id, false);

                if ($physiotherapy_package == []) {
                    $this->REST_Return(404, "there is no physiotherapy_package with id " . $physiotherapy_package_id);
                    return;
                }

                if ($service_class == []) {
                    $this->REST_Return(404, "there is no service_class with id " . $service_class_id);
                    return;
                }

                $costing_id = $physiotherapy_package[0]->costing_id;

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

                $physiotherapy_request_item = array(
                    'physiotherapy_request_id'     => $physiotherapy_request_id,
                    'physiotherapy_package_id'     => $physiotherapy_package_id,
                    'service_class_id'          => $service_class_id,
                    'var_1'                     => $costing_services_class_var_1,
                    'var_2'                     => $costing_services_class_var_2,
                    'var_3'                     => $costing_services_class_var_3,
                    'costing_id'                => $costing_id,
                    'costing_name'              => $costing_name,
                    'date_request'              => $date_request,
                    'status'                    => 1
                );
                $this->Physiotherapy_request_item->create($physiotherapy_request_item);
            }

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

            $consultation_date = $date_request;

            $dt_refer_internal = array(
                'no_refer'                      => $code,
                'institute_branch_id'           => $institute_branch_id,
                'registration_destination_id'   => $registration_destination_id,
                'health_facility_item_id'       => $health_facility_item_id,
                'doctor_in_charge_id'           => $doctor_in_charge_id,
                'norm'                          => $norm,
                'consultation_date'             => $consultation_date
            );
            $this->Refer_internal->create($dt_refer_internal);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create soap', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }

    public function create_soap_post() {
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
        $this->form_validation->set_rules($this->create_soap_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $registration_destination_id = $dt_post['registration_destination_id'];

        $registration_destination = $this->Registration_destination->select_id($registration_destination_id);

        if ($registration_destination == '') {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

        $employee = $this->Employee->select_id($doctor_in_charge_id);

        if ($employee == '') {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }
    
        // INIT SOAP
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_soap = array(
            'registration_destination_id'  => $registration_destination_id,
            'doctor_in_charge_id'          => $doctor_in_charge_id,
            'user_added'                   => $user_id,
            'date_added'                   => date("Y-m-d H:i:s",time())
        );
        $soap_id = $this->$model->create($dt_soap);

        // INIT SOAP_S
        $anamesa_type_id = $dt_post['anamesa_type_id'];
        $family_name = $dt_post['family_name'];
        $family_relation_id = $dt_post['family_relation_id'];
        $main_complaint = $dt_post['main_complaint'];
        $history_disease_now = $dt_post['history_disease_now'];
        $history_disease_past = $dt_post['history_disease_past'];
        $history_drugs = $dt_post['history_drugs'];
        $history_allergy = $dt_post['history_allergy'];

        $dt_soap_s = array(
            'soap_id'               => $soap_id,
            'anamesa_type_id'       => $anamesa_type_id,
            'family_name'           => $family_name,
            'family_relation_id'    => $family_relation_id,
            'main_complaint'        => $main_complaint,
            'history_disease_now'   => $history_disease_now,
            'history_disease_past'  => $history_disease_past,
            'history_drugs'         => $history_drugs,
            'history_allergy'       => $history_allergy,
        );
        $this->Soap_s->create($dt_soap_s);

        // INIT SOAP_O

        $physical_check = $dt_post['physical_check'];
        $anythingelse = $dt_post['soap_o_anythingelse'];
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
        $weight = $dt_post['weight'];
        $classification_weight_type_id = $dt_post['classification_weight_type_id'];
        $height = $dt_post['height'];
        $imt = $dt_post['imt'];
        $head_circumference = $dt_post['head_circumference'];
        $chest_circumference = $dt_post['chest_circumference'];
        $arm_circumference = $dt_post['arm_circumference'];
        $stomach_circumference = $dt_post['stomach_circumference'];
        $knee_circumference = $dt_post['knee_circumference'];
        
        $sore_score = $dt_post['sore_score'];
        $ews = $dt_post['ews'];
        $non_icd10 = implode('|', $dt_post['non_icd10']);
        $head = $dt_post['head'];
        $head_hair_color = $dt_post['head_hair_color'];
        $head_hair_condition = $dt_post['head_hair_condition'];
        $eye_conjunctive = $dt_post['eye_conjunctive'];
        $eye_sclera = $dt_post['eye_sclera'];
        $eye_pupil = $dt_post['eye_pupil'];
        $eye_light_reflex = $dt_post['eye_light_reflex'];
        $eye_blind_color = $dt_post['eye_blind_color'];
        $mouth_mucosa = $dt_post['mouth_mucosa'];
        $mouth_discoloration = $dt_post['mouth_discoloration'];
        $mouth_tongue = $dt_post['mouth_tongue'];
        $mouth_caries = $dt_post['mouth_caries'];
        $neck_kgb = $dt_post['neck_kgb'];
        $neck_trachea_position = $dt_post['neck_trachea_position'];
        $neck_massa = $dt_post['neck_massa'];
        $thorax_insp_chest = $dt_post['thorax_insp_chest'];
        $thorax_insp_retraction = $dt_post['thorax_insp_retraction'];
        $thorax_palp_chest = $dt_post['thorax_palp_chest'];
        $thorax_palp_fremitus = $dt_post['thorax_palp_fremitus'];
        $thorax_palp_crepitation = $dt_post['thorax_palp_crepitation'];
        $thorax_ausk_pul_vasiculer = $dt_post['thorax_ausk_pul_vasiculer'];
        $thorax_ausk_pul_rhonki = $dt_post['thorax_ausk_pul_rhonki'];
        $thorax_ausk_pul_wheezing = $dt_post['thorax_ausk_pul_wheezing'];
        $thorax_ausk_cor_heart_sound = $dt_post['thorax_ausk_cor_heart_sound'];
        $thorax_ausk_cor_murmur = $dt_post['thorax_ausk_cor_murmur'];
        $thorax_ausk_cor_gallop = $dt_post['thorax_ausk_cor_gallop'];
        $abdomen_supel = $dt_post['abdomen_supel'];
        $abdomen_bowel_sounds = $dt_post['abdomen_bowel_sounds'];
        $abdomen_tenderness = $dt_post['abdomen_tenderness'];
        $abdomen_hepar_lien = $dt_post['abdomen_hepar_lien'];
        $abdomen_other_physical_check = $dt_post['abdomen_other_physical_check'];
        $extremity_acral = $dt_post['extremity_acral'];
        $extremity_crt = $dt_post['extremity_crt'];
        $extremity_edema = $dt_post['extremity_edema'];
        $genitalia = $dt_post['genitalia'];
        $localist_status = $dt_post['localist_status'];
        $ophthal_status = $dt_post['ophthal_status'];
        $neurologist_status = $dt_post['neurologist_status'];

        $ophthal_od = (object) array(
            'visus' => $dt_post['ophthal_od']['visus'],
            'tio_digital' => $dt_post['ophthal_od']['tio_digital'],
            'tio_tonometri_sciots' => $dt_post['ophthal_od']['tio_tonometri_sciots'],
            'eye_position' => $dt_post['ophthal_od']['eye_position'],
            'eye_movement' => $dt_post['ophthal_od']['eye_movement'],
            'super_silia' => $dt_post['ophthal_od']['super_silia'],
            'palpebra_edema' => $dt_post['ophthal_od']['palpebra_edema'],
            'palpebra_ptosis' => $dt_post['ophthal_od']['palpebra_ptosis'],
            'palpebra_lagophtalmus' => $dt_post['ophthal_od']['palpebra_lagophtalmus'],
            'palpebra_tumor' => $dt_post['ophthal_od']['palpebra_tumor'],
            'apparatus_lakrimalis' => $dt_post['ophthal_od']['apparatus_lakrimalis'],
            'silia' => $dt_post['ophthal_od']['silia'],
            'conjunctive_hiperemis' => $dt_post['ophthal_od']['conjunctive_hiperemis'],
            'conjunctive_bleed' => $dt_post['ophthal_od']['conjunctive_bleed'],
            'cornea' => $dt_post['ophthal_od']['cornea'],
            'camera_oculi' => $dt_post['ophthal_od']['camera_oculi'],
            'iris_color' => $dt_post['ophthal_od']['iris_color'],
            'iris_cripte' => $dt_post['ophthal_od']['iris_cripte'],
            'pupil_form' => $dt_post['ophthal_od']['pupil_form'],
            'pupil_diameter' => $dt_post['ophthal_od']['pupil_diameter'],
            'pupil_direct_light' => $dt_post['ophthal_od']['pupil_direct_light'],
            'pupil_indirect_light' => $dt_post['ophthal_od']['pupil_indirect_light'],
            'lens' => $dt_post['ophthal_od']['lens'],
            'shadow_test' => $dt_post['ophthal_od']['shadow_test'],
            'glandula' => $dt_post['ophthal_od']['glandula'],
            'field_view' => $dt_post['ophthal_od']['field_view'],
            'fundus' => $dt_post['ophthal_od']['fundus'],
        );

        $ophthal_os = (object) array(
            'visus' => $dt_post['ophthal_os']['visus'],
            'tio_digital' => $dt_post['ophthal_os']['tio_digital'],
            'tio_tonometri_sciots' => $dt_post['ophthal_os']['tio_tonometri_sciots'],
            'eye_position' => $dt_post['ophthal_os']['eye_position'],
            'eye_movement' => $dt_post['ophthal_os']['eye_movement'],
            'super_silia' => $dt_post['ophthal_os']['super_silia'],
            'palpebra_edema' => $dt_post['ophthal_os']['palpebra_edema'],
            'palpebra_ptosis' => $dt_post['ophthal_os']['palpebra_ptosis'],
            'palpebra_lagophtalmus' => $dt_post['ophthal_os']['palpebra_lagophtalmus'],
            'palpebra_tumor' => $dt_post['ophthal_os']['palpebra_tumor'],
            'apparatus_lakrimalis' => $dt_post['ophthal_os']['apparatus_lakrimalis'],
            'silia' => $dt_post['ophthal_os']['silia'],
            'conjunctive_hiperemis' => $dt_post['ophthal_os']['conjunctive_hiperemis'],
            'conjunctive_bleed' => $dt_post['ophthal_os']['conjunctive_bleed'],
            'cornea' => $dt_post['ophthal_os']['cornea'],
            'camera_oculi' => $dt_post['ophthal_os']['camera_oculi'],
            'iris_color' => $dt_post['ophthal_os']['iris_color'],
            'iris_cripte' => $dt_post['ophthal_os']['iris_cripte'],
            'pupil_form' => $dt_post['ophthal_os']['pupil_form'],
            'pupil_diameter' => $dt_post['ophthal_os']['pupil_diameter'],
            'pupil_direct_light' => $dt_post['ophthal_os']['pupil_direct_light'],
            'pupil_indirect_light' => $dt_post['ophthal_os']['pupil_indirect_light'],
            'lens' => $dt_post['ophthal_os']['lens'],
            'shadow_test' => $dt_post['ophthal_os']['shadow_test'],
            'glandula' => $dt_post['ophthal_os']['glandula'],
            'field_view' => $dt_post['ophthal_os']['field_view'],
            'fundus' => $dt_post['ophthal_os']['fundus'],
        );

        $ophthal_od_encode = json_encode($ophthal_od);
        $ophthal_os_encode = json_encode($ophthal_os);

        $dt_soap_o = array(
            'soap_id'                                   => $soap_id,
            'physical_check'                            => $physical_check,
            'anythingelse'                              => $anythingelse,
            'systolic_blood_pressure'                   => $systolic_blood_pressure,
            'diastolic_blood_pressure'                  => $diastolic_blood_pressure,
            'classification_blood_pressure_type_id'     => $classification_blood_pressure_type_id,
            'pulse_frequency'                           => $pulse_frequency,
            'breath_frequency'                          => $breath_frequency,
            'oximetry_frequency'                        => $oximetry_frequency,
            'classification_oximetry_type_id'           => $classification_oximetry_type_id,
            'temperature'                               => $temperature,
            'classification_temperature_type_id'        => $classification_temperature_type_id,
            'blood_sugar'                               => $blood_sugar,
            'weight'                                    => $weight,
            'classification_weight_type_id'             => $classification_weight_type_id,
            'height'                                    => $height,
            'imt'                                       => $imt,
            'head_circumference'                        => $head_circumference,
            'chest_circumference'                       => $chest_circumference,
            'arm_circumference'                         => $arm_circumference,
            'stomach_circumference'                     => $stomach_circumference,
            'knee_circumference'                        => $knee_circumference,
            'sore_score'                                => $sore_score,
            'ews'                                       => $ews,
            'non_icd10'                                 => $non_icd10,
            'head'                                      => $head,
            'head_hair_color'                           => $head_hair_color,
            'head_hair_condition'                       => $head_hair_condition,
            'eye_conjunctive'                           => $eye_conjunctive,
            'eye_sclera'                                => $eye_sclera,
            'eye_pupil'                                 => $eye_pupil,
            'eye_light_reflex'                          => $eye_light_reflex,
            'eye_blind_color'                           => $eye_blind_color,
            'mouth_mucosa'                              => $mouth_mucosa,
            'mouth_discoloration'                       => $mouth_discoloration,
            'mouth_tongue'                              => $mouth_tongue,
            'mouth_caries'                              => $mouth_caries,
            'neck_kgb'                                  => $neck_kgb,
            'neck_trachea_position'                     => $neck_trachea_position,
            'neck_massa'                                => $neck_massa,
            'thorax_insp_chest'                         => $thorax_insp_chest,
            'thorax_insp_retraction'                    => $thorax_insp_retraction,
            'thorax_palp_chest'                         => $thorax_palp_chest,
            'thorax_palp_fremitus'                      => $thorax_palp_fremitus,
            'thorax_palp_crepitation'                   => $thorax_palp_crepitation,
            'thorax_ausk_pul_vasiculer'                 => $thorax_ausk_pul_vasiculer,
            'thorax_ausk_pul_rhonki'                    => $thorax_ausk_pul_rhonki,
            'thorax_ausk_pul_wheezing'                  => $thorax_ausk_pul_wheezing,
            'thorax_ausk_cor_heart_sound'               => $thorax_ausk_cor_heart_sound,
            'thorax_ausk_cor_murmur'                    => $thorax_ausk_cor_murmur,
            'thorax_ausk_cor_gallop'                    => $thorax_ausk_cor_gallop,
            'abdomen_supel'                             => $abdomen_supel,
            'abdomen_bowel_sounds'                      => $abdomen_bowel_sounds,
            'abdomen_tenderness'                        => $abdomen_tenderness,
            'abdomen_hepar_lien'                        => $abdomen_hepar_lien,
            'abdomen_other_physical_check'              => $abdomen_other_physical_check,
            'extremity_acral'                           => $extremity_acral,
            'extremity_crt'                             => $extremity_crt,
            'extremity_edema'                           => $extremity_edema,
            'genitalia'                                 => $genitalia,
            'localist_status'                           => $localist_status,
            'ophthal_status'                            => $ophthal_status,
            'neurologist_status'                        => $neurologist_status,
            'ophthal_od'                                => $ophthal_od_encode,
            'ophthal_os'                                => $ophthal_os_encode,
        );
        $soap_o_id = $this->Soap_o->create($dt_soap_o);

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_o,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create soap_o', $response_data);
            return;
        }

        // INIT SOAP_O_ACTION
        $canvasing_type_id = $dt_post['canvasing_type_id'];
        $canvasing_text = $dt_post['canvasing_text'];
        $attr1 = $dt_post['attr1'];
        $attr2 = $dt_post['attr2'];

        $dt_soap_o_action = array(
            'soap_o_id'                 => $soap_o_id,
            'canvasing_type_id'         => $canvasing_type_id,
            'canvasing_text'            => $canvasing_text,
            'attr1'                     => $attr1,
            'attr2'                     => $attr2,
        );
        $this->Soap_o_action->create($dt_soap_o_action);

        // INIT SOAP_A
        $development_notes = $dt_post['development_notes'];
        $anythingelse = $dt_post['soap_a_anythingelse'];
        $icd10_notes = $dt_post['icd10_notes'];
        $non_icd10 = implode('|', $dt_post['non_icd10']);
        $obgyn_diag_date = $dt_post['obgyn_diag_date'];
        $obgyn_diag_clinis = $dt_post['obgyn_diag_clinis'];
        $obgyn_diag_hpht = $dt_post['obgyn_diag_hpht'];
        $obgyn_s_d = $dt_post['obgyn_s_d'];
        $obgyn_j_exam = $dt_post['obgyn_j_exam'];
        $obgyn_j_pos = $dt_post['obgyn_j_pos'];
        $obgyn_j_plas = $dt_post['obgyn_j_plas'];
        $obgyn_j_k = $dt_post['obgyn_j_k'];
        $obgyn_j_sex = $dt_post['obgyn_j_sex'];
        $obgyn_j_weight = $dt_post['obgyn_j_weight'];
        $obgyn_size_usg = $dt_post['obgyn_size_usg'];
        $obgyn_usg_desc = $dt_post['obgyn_usg_desc'];
        $obgyn_usg_file = $dt_post['obgyn_usg_file'];

        $dt_soap_a = array(
            'soap_id'               => $soap_id,
            'development_notes'     => $development_notes,
            'anythingelse'          => $anythingelse,
            'icd10_notes'           => $icd10_notes,
            'non_icd10'             => $non_icd10,
            'obgyn_diag_date'       => $obgyn_diag_date,                   
            'obgyn_diag_clinis'     => $obgyn_diag_clinis,                 
            'obgyn_diag_hpht'       => $obgyn_diag_hpht,                   
            'obgyn_s_d'             => $obgyn_s_d,                 
            'obgyn_j_exam'          => $obgyn_j_exam,                  
            'obgyn_j_pos'           => $obgyn_j_pos,                   
            'obgyn_j_plas'          => $obgyn_j_plas,                  
            'obgyn_j_k'             => $obgyn_j_k,                 
            'obgyn_j_sex'           => $obgyn_j_sex,                   
            'obgyn_j_weight'        => $obgyn_j_weight,                    
            'obgyn_size_usg'        => $obgyn_size_usg,                    
            'obgyn_usg_desc'        => $obgyn_usg_desc,                    
            'obgyn_usg_file'        => isset($obgyn_usg_file) ? $this->base64ToFile($obgyn_usg_file, $soap_id) : '',
            'date_added'            => date("Y-m-d H:i:s")
        );
        $soap_a_id = $this->Soap_a->create($dt_soap_a);

        // INIT_SOAP_A_ACTION
        $icd10_arr = $dt_post['icd10'];
        for ($i=0; $i < count($icd10_arr) ; $i++) { 
            if (!isset($icd10_arr[$i]['icd10_id'])) {
                $this->REST_Return(400, "icd10_id field in icd10 is required!");
                return;
            }

            $icd10_id = $icd10_arr[$i]['icd10_id'];

            $icd10 = $this->Icd10->select_id($icd10_id);

            if ($icd10 == "") {
                $this->REST_Return(404, "there is no icd10 with id " . $icd10_id);
                return;
            }

            $dt_soap_a_action = array(
                'soap_a_id'         => $soap_a_id,
                'icd10_id'          => $icd10_id,
                'index'             => $i
            );
            $this->Soap_a_action->create($dt_soap_a_action);
        }

        // INIT SOAP_S
        $therapy_plan = $dt_post['therapy_plan'];

        $dt_soap_p = array(
            'soap_id'            => $soap_id,
            'therapy_plan'       => $therapy_plan,
            'doctor_in_charge_id' => $doctor_in_charge_id,
        );
        $soap_p_id = $this->Soap_p->create($dt_soap_p);

        // SOAP_P_ACTION
        $icd9cm_arr = $dt_post['icd9cm'];
        for ($i=0; $i < count($icd9cm_arr) ; $i++) { 
            if (!isset($icd9cm_arr[$i]['icd9cm_id'])) {
                $this->REST_Return(400, "icd9cm_id field in icd9cm is required!");
                return;
            }

            $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];

            $icd9cm = $this->Icd9cm->select_id($icd9cm_id);

            if ($icd9cm == "") {
                $this->REST_Return(404, "there is no icd9cm with id " . $icd9cm_id);
                return;
            }

            $dt_soap_p_action = array(
                'soap_p_id' => $soap_p_id,
                'icd9cm_id' => $icd9cm_id
            );
            $this->Soap_p_action->create($dt_soap_p_action);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create soap', $response_data);
            return;
        }

        // // INIT NURSE
        // /* Start Transaction */
        // $this->db->trans_start();

        // $dt_nurse = array(
        //     'registration_destination_id'           => $registration_destination_id,
        //     'employee_nurse_id'                     => $doctor_in_charge_id,
        //     'anamesa_type_id'                       => $anamesa_type_id,
        //     'family_name'                           => $family_name,
        //     'family_relation_id'                    => $family_relation_id,
        //     'main_complaint'                        => $main_complaint,
        //     'history_disease_now'                   => $history_disease_now,
        //     'history_disease_past'                  => $history_disease_past,
        //     'systolic_blood_pressure'               => $systolic_blood_pressure,
        //     'diastolic_blood_pressure'              => $diastolic_blood_pressure,
        //     'classification_blood_pressure_type_id' => $classification_blood_pressure_type_id,
        //     'pulse_frequency'                       => $pulse_frequency,
        //     'breath_frequency'                      => $breath_frequency,
        //     'oximetry_frequency'                    => $oximetry_frequency,
        //     'classification_oximetry_type_id'       => $classification_oximetry_type_id,
        //     'temperature'                           => $temperature,
        //     'classification_temperature_type_id'    => $classification_temperature_type_id,
        //     'blood_sugar'                           => $blood_sugar,
        //     'weight'                                => $weight,
        //     'classification_weight_type_id'         => $classification_weight_type_id,
        //     'height'                                => $height,
        //     'imt'                                   => $imt,
        //     'head_circumference'                    => $head_circumference,
        //     'chest_circumference'                   => $chest_circumference,
        //     'arm_circumference'                     => $arm_circumference,
        //     'stomach_circumference'                 => $stomach_circumference,
        //     'knee_circumference'                    => $knee_circumference,
        //     'date_added'                            => date("Y-m-d H:i:s",time()),
        // );
        // $this->Nurse->create($dt_nurse);
        
        // $this->db->trans_complete();
        // /* End Transaction */

        // if ($this->db->trans_status() === false) {
        //     $response_data = array(
        //         'error'     => $this->db->error(),
        //         'data'      => $dt_nurse,
        //         'query'     => $this->db->last_query()
        //     );
        //     $this->REST_Return(500, 'error while trying to create nurse', $response_data);
        //     return;
        // }
    
        $response_data = array(
            'id'     => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }

    /**
     * GET Soap with API
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
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if(isset($q_strings['norm'])) $this->$model->whereFields['r.norm'] = $q_strings['norm'];
            $this->$model->whereFields = $q_strings;
            if (isset($q_strings['is_booking'])) {
                $this->$model->whereFields['a.is_booking'] = $q_strings['is_booking'];
                unset($this->$model->whereFields['is_booking']);
            }
            if (isset($q_strings['not_registration_id'])) {
                if ($q_strings['norm'] == null) {
                    $this->REST_Return(400, 'norm is required if filtering not_registration_id');
                    return;
                }
                $not_registration_id = $q_strings['not_registration_id'];
                $this->$model->whereNotInFields['registration_id'] = $not_registration_id;
                unset($this->$model->whereFields['not_registration_id']);
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            $this->$model->whereFields['a.is_resume_exit'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $soap_id = $output[$i]->id;
                $doctor_in_charge_id = $output[$i]->doctor_in_charge_id;
                $health_facility_id = $output[$i]->health_facility_id;
                $is_initial_inpatient = $output[$i]->is_initial_inpatient;
                $checkin_date = $output[$i]->register_date;

                $checkin = new DateTime($checkin_date);
                $checkout = new DateTime(date('Y-m-d H:i:s'));
                $diff_checkin = $checkout->diff($checkin);

                $output[$i]->los = $diff_checkin->d . " Hari " . $diff_checkin->h . " Jam";

                // CONDITION IF INPATIENT DON'T SHOW CPPT IN LIST SOAP
                // if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                //     if ($is_initial_inpatient == 0) {
                //         unset($output[$i]);
                //         continue;
                //     }
                // }

                // SOAP_S
                $this->Soap_s->whereFields['soap_id'] = $output[$i]->id;
                $soap_s = $this->Soap_s->read();
                if($soap_s != []) {
                    $output[$i]->soap_s_id = $soap_s[0]->id;
                    $output[$i]->anamesa_type_id = $soap_s[0]->anamesa_type_id;
                    $output[$i]->family_name = $soap_s[0]->family_name;
                    $output[$i]->family_relation_id = $soap_s[0]->family_relation_id;
                    $output[$i]->main_complaint = $soap_s[0]->main_complaint;
                    $output[$i]->history_disease_now = $soap_s[0]->history_disease_now;
                    $output[$i]->history_disease_past = $soap_s[0]->history_disease_past;
                    $output[$i]->history_disease_family = $soap_s[0]->history_disease_family;
                    $output[$i]->history_habitude = $soap_s[0]->history_habitude;
                    $output[$i]->history_drugs = $soap_s[0]->history_drugs;
                    $output[$i]->history_allergy = $soap_s[0]->history_allergy;
                }
                
                // SOAP_O
                $this->Soap_o->whereFields['soap_id'] = $output[$i]->id;
                $soap_o = $this->Soap_o->read();
                if($soap_o != []) {
                    $output[$i]->soap_o_id = $soap_o[0]->id;
                    $output[$i]->physical_check = $soap_o[0]->physical_check;
                    $output[$i]->soap_o_anythingelse = $soap_o[0]->anythingelse;
                    $output[$i]->aware = $soap_o[0]->aware;
                    $output[$i]->eye_score = $soap_o[0]->eye_score;
                    $output[$i]->verbal_score = $soap_o[0]->verbal_score;
                    $output[$i]->motor_score = $soap_o[0]->motor_score;
                    $output[$i]->pupil_score = $soap_o[0]->pupil_score;
                    $output[$i]->verbal_response = $soap_o[0]->verbal_response;
                    $output[$i]->systolic_blood_pressure = $soap_o[0]->systolic_blood_pressure;
                    $output[$i]->diastolic_blood_pressure = $soap_o[0]->diastolic_blood_pressure;
                    $output[$i]->classification_blood_pressure_type_id = $soap_o[0]->classification_blood_pressure_type_id;
                    $output[$i]->verbal_response = $soap_o[0]->verbal_response;
                    $output[$i]->oxygen_type_id = $soap_o[0]->oxygen_type_id;
                    $output[$i]->pulse_frequency = $soap_o[0]->pulse_frequency;
                    $output[$i]->breath_frequency = $soap_o[0]->breath_frequency;
                    $output[$i]->oximetry_frequency = $soap_o[0]->oximetry_frequency;
                    $output[$i]->classification_oximetry_type_id = $soap_o[0]->classification_oximetry_type_id;
                    $output[$i]->temperature = $soap_o[0]->temperature;
                    $output[$i]->classification_temperature_type_id = $soap_o[0]->classification_temperature_type_id;
                    $output[$i]->blood_sugar = $soap_o[0]->blood_sugar;
                    $output[$i]->skin_color = $soap_o[0]->skin_color;
                    $output[$i]->behavior = $soap_o[0]->behavior;
                    $output[$i]->weight = $soap_o[0]->weight;
                    $output[$i]->classification_weight_type_id = $soap_o[0]->classification_weight_type_id;
                    $output[$i]->height = $soap_o[0]->height;
                    $output[$i]->imt = $soap_o[0]->imt;
                    $output[$i]->head_circumference = $soap_o[0]->head_circumference;
                    $output[$i]->chest_circumference = $soap_o[0]->chest_circumference;
                    $output[$i]->arm_circumference = $soap_o[0]->arm_circumference;
                    $output[$i]->stomach_circumference = $soap_o[0]->stomach_circumference;
                    $output[$i]->knee_circumference = $soap_o[0]->knee_circumference;
                    $output[$i]->sore_score = $soap_o[0]->sore_score;
                    $output[$i]->ews = $soap_o[0]->ews;
                    // NOTE - pakai non_icd10 yang di soap_a
                    // $output[$i]->non_icd10 = ($soap_o[0]->non_icd10 == null) ? array() : explode(',', $soap_o[0]->non_icd10); 
                    $output[$i]->head = $soap_o[0]->head;
                    $output[$i]->head_hair_color = $soap_o[0]->head_hair_color;
                    $output[$i]->head_hair_condition = $soap_o[0]->head_hair_condition;
                    $output[$i]->eye_conjunctive = $soap_o[0]->eye_conjunctive;
                    $output[$i]->eye_sclera = $soap_o[0]->eye_sclera;
                    $output[$i]->eye_pupil = $soap_o[0]->eye_pupil;
                    $output[$i]->eye_light_reflex = $soap_o[0]->eye_light_reflex;
                    $output[$i]->eye_blind_color = $soap_o[0]->eye_blind_color;
                    $output[$i]->mouth_mucosa = $soap_o[0]->mouth_mucosa;
                    $output[$i]->mouth_discoloration = $soap_o[0]->mouth_discoloration;
                    $output[$i]->mouth_tongue = $soap_o[0]->mouth_tongue;
                    $output[$i]->mouth_caries = $soap_o[0]->mouth_caries;
                    $output[$i]->neck_kgb = $soap_o[0]->neck_kgb;
                    $output[$i]->neck_trachea_position = $soap_o[0]->neck_trachea_position;
                    $output[$i]->neck_massa = $soap_o[0]->neck_massa;
                    $output[$i]->neck_jvp = $soap_o[0]->neck_jvp;
                    $output[$i]->thorax_insp_chest = $soap_o[0]->thorax_insp_chest;
                    $output[$i]->thorax_insp_retraction = $soap_o[0]->thorax_insp_retraction;
                    $output[$i]->thorax_palp_chest = $soap_o[0]->thorax_palp_chest;
                    $output[$i]->thorax_palp_fremitus = $soap_o[0]->thorax_palp_fremitus;
                    $output[$i]->thorax_palp_crepitation = $soap_o[0]->thorax_palp_crepitation;
                    $output[$i]->thorax_ausk_pul_vasiculer = $soap_o[0]->thorax_ausk_pul_vasiculer;
                    $output[$i]->thorax_ausk_pul_rhonki = $soap_o[0]->thorax_ausk_pul_rhonki;
                    $output[$i]->thorax_ausk_pul_wheezing = $soap_o[0]->thorax_ausk_pul_wheezing;
                    $output[$i]->thorax_ausk_cor_heart_sound = $soap_o[0]->thorax_ausk_cor_heart_sound;
                    $output[$i]->thorax_ausk_cor_murmur = $soap_o[0]->thorax_ausk_cor_murmur;
                    $output[$i]->thorax_ausk_cor_gallop = $soap_o[0]->thorax_ausk_cor_gallop;
                    $output[$i]->abdomen_supel = $soap_o[0]->abdomen_supel;
                    $output[$i]->abdomen_bowel_sounds = $soap_o[0]->abdomen_bowel_sounds;
                    $output[$i]->abdomen_tenderness = $soap_o[0]->abdomen_tenderness;
                    $output[$i]->abdomen_hepar_lien = $soap_o[0]->abdomen_hepar_lien;
                    $output[$i]->abdomen_other_physical_check = $soap_o[0]->abdomen_other_physical_check;
                    $output[$i]->extremity_acral = $soap_o[0]->extremity_acral;
                    $output[$i]->extremity_crt = $soap_o[0]->extremity_crt;
                    $output[$i]->extremity_edema = $soap_o[0]->extremity_edema;
                    $output[$i]->ophthal_status = $soap_o[0]->ophthal_status;
                    $output[$i]->neurologist_status = $soap_o[0]->neurologist_status;

                    $ophthal_od_decode = json_decode($soap_o[0]->ophthal_od);
                    if (is_iterable($ophthal_od_decode)) {
                        foreach ($ophthal_od_decode as $key => $val) {
                            if (is_numeric($val)) {
                                $ophthal_od_decode->$key = (int) $val;
                            }
                        }
                    }

                    $ophthal_os_decode = json_decode($soap_o[0]->ophthal_os);
                    if (is_iterable($ophthal_os_decode)) {
                        foreach ($ophthal_os_decode as $key => $val) {
                            if (is_numeric($val)) {
                                $ophthal_os_decode->$key = (int) $val;
                            }
                        }
                    }

                    $output[$i]->ophthal_od = $ophthal_od_decode;
                    $output[$i]->ophthal_os = $ophthal_os_decode;
                    $output[$i]->uub_type_id = $soap_o[0]->uub_type_id;
                    $output[$i]->eye_anemic_conjunctiva = $soap_o[0]->eye_anemic_conjunctiva;
                    $output[$i]->eye_interic_sclera = $soap_o[0]->eye_interic_sclera;
                    $output[$i]->eye_anythingelse = $soap_o[0]->eye_anythingelse;
                    $output[$i]->tht_hyperemic_pharynx = $soap_o[0]->tht_hyperemic_pharynx;
                    $output[$i]->tht_tonsil = $soap_o[0]->tht_tonsil;
                    $output[$i]->tht_ear = $soap_o[0]->tht_ear;
                    $output[$i]->tht_anythingelse = $soap_o[0]->tht_anythingelse;
                    $output[$i]->neck_lymph_gland = $soap_o[0]->neck_lymph_gland;
                    $output[$i]->thorax_heart_type_id = $soap_o[0]->thorax_heart_type_id;
                    $output[$i]->thorax_murmur = $soap_o[0]->thorax_murmur;
                    $output[$i]->thorax_gallop = $soap_o[0]->thorax_gallop;
                    $output[$i]->thorax_anythingelse = $soap_o[0]->thorax_anythingelse;
                    $output[$i]->pulmo_arry = ($soap_o[0]->pulmo_arry != "" ? explode(',', $soap_o[0]->pulmo_arry) : []);
                    $output[$i]->pulmo_anythingelse = $soap_o[0]->pulmo_anythingelse;
                    $output[$i]->pulmo_breath_movement = $soap_o[0]->pulmo_breath_movement;
                    $output[$i]->pulmo_breath_sound = $soap_o[0]->pulmo_breath_sound;
                    $output[$i]->extremity_acral_type_id = $soap_o[0]->extremity_acral_type_id;
                    $output[$i]->extremity_capillary_refilling_time = $soap_o[0]->extremity_capillary_refilling_time;
                    $output[$i]->extremity_anythingelse = $soap_o[0]->extremity_anythingelse;
                    $output[$i]->abdomen_inspection_type_id = $soap_o[0]->abdomen_inspection_type_id;
                    $output[$i]->abdomen_palpation_type_id = $soap_o[0]->abdomen_palpation_type_id;
                    $output[$i]->abdomen_sore_press = $soap_o[0]->abdomen_sore_press;
                    $output[$i]->abdomen_sore_press_location = $soap_o[0]->abdomen_sore_press_location;
                    $output[$i]->abdomen_hepar = $soap_o[0]->abdomen_hepar;
                    $output[$i]->abdomen_bu = $soap_o[0]->abdomen_bu;
                    $output[$i]->localist_status = $soap_o[0]->localist_status;
                    // $output[$i]->localist_status = $soap_o[0]->physical_check; // NOTE - SEMENTARA DARI physical_check
                    $output[$i]->abdomen_lien = $soap_o[0]->abdomen_lien;
                    $output[$i]->genitalia = $soap_o[0]->genitalia;

                    // SOAP_O_ACTION
                    $this->Soap_o_action->whereFields['soap_o_id'] = $soap_o[0]->id;
                    $soap_o_action = $this->Soap_o_action->read();
                    if($soap_o_action != []) {
                        $output[$i]->soap_o_action_id = $soap_o_action[0]->id;
                        $output[$i]->canvasing_type_id = $soap_o_action[0]->canvasing_type_id;
                        $output[$i]->canvasing_text = $soap_o_action[0]->canvasing_text;
                        $output[$i]->attr1 = $soap_o_action[0]->attr1;
                        $output[$i]->attr2 = $soap_o_action[0]->attr2;
                    }
                }

                // SOAP_A
                // $this->Soap_a->whereFields['soap_id'] = $output[$i]->id;
                // $soap_a = $this->Soap_a->read();
                $soap_a = $this->db->query("
                    SELECT a.*
                    FROM soap_a as a
                    WHERE soap_id = '$soap_id'
                    AND (a.cppt_id IS NULL OR a.cppt_id = '')
                    ORDER BY a.id ASC
                ")->result();
                if($soap_a != []) {
                    $output[$i]->soap_a_id = $soap_a[0]->id;
                    $output[$i]->development_notes = $soap_a[0]->development_notes;
                    $output[$i]->soap_a_anythingelse = $soap_a[0]->anythingelse;
                    if ($soap_a[0]->obgyn_usg_file) {
                        $soap_a[0]->obgyn_usg_file = base_url().$soap_a[0]->obgyn_usg_file;
                    }
                    $soap_a[0]->non_icd10 = ($soap_a[0]->non_icd10 == null) ? array() : explode('|', $soap_a[0]->non_icd10);
                    $output[$i]->soap_a = $soap_a[0];
                    $output[$i]->icd10_notes = $soap_a[0]->icd10_notes;
                    $output[$i]->non_icd10 = $soap_a[0]->non_icd10;

                    // SOAP_A_ACTION
                    $this->Soap_a_action->whereFields['soap_a_id'] = $soap_a[0]->id;
                    $this->Soap_a_action->whereFields['is_delete'] = 0;
                    $soap_a_action = $this->Soap_a_action->read();
                    if($soap_a_action != []) {
                        for ($j=0; $j < count($soap_a_action); $j++) { 
                            $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
                            $output[$i]->icd10[$j]['id'] = $soap_a_action[$j]->id;
                            $output[$i]->icd10[$j]['icd10_id'] = $soap_a_action[$j]->icd10_id;
                            $output[$i]->icd10[$j]['code'] = $icd10[0]->code;
                            $output[$i]->icd10[$j]['code_s'] = $icd10[0]->code_s;
                            $output[$i]->icd10[$j]['name'] = $icd10[0]->name;
                            $output[$i]->icd10[$j]['date_added'] = $soap_a_action[$j]->date_added;
                            $output[$i]->icd10[$j]['index'] = $soap_a_action[$j]->index;
                        }
                    }
                }

                $output[$i]->prescription_new = [];

                // SOAP_P
                // $this->Soap_p->whereFields['soap_id'] = $output[$i]->id;
                // $soap_p = $this->Soap_p->read();
                $soap_p = $this->db->query("
                    SELECT a.*
                    FROM soap_p as a
                    WHERE soap_id = '$soap_id'
                    AND (a.cppt_id IS NULL OR a.cppt_id = '')
                    AND a.doctor_in_charge_id = '$doctor_in_charge_id'
                    ORDER BY a.id ASC
                ")->result();
                $output[$i]->icd9cm = array();
                // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
                if($soap_p != []) {
                    $idx_prescription = 0;
                    for ($spx=0; $spx < count($soap_p); $spx++) { 
                        // PRESCRIPTION
                        $soap_p_id = $soap_p[$spx]->id;

                        // $this->db->cache_off();
                        $prescription = $this->db->query("
                            SELECT p.id,
                            p.no_order,
                            p.queue,
                            p.type_prescription_id,
                            h.name AS flag_prescription,
                            p.pharmacy_depo_id,
                            p.soap_p_id,
                            p.doctor_in_charge_id,
                            e.fullname AS doctor_in_charge_name,
                            p.health_facility_item_id,
                            p.flag,
                            p.is_retur,
                            p.date_added
                            FROM pharmacy_prescription p
                            LEFT JOIN employee e ON p.doctor_in_charge_id = e.id
                            LEFT JOIN helper h ON p.type_prescription_id = h.code AND h.variable = 'type_prescription'
                            WHERE p.soap_p_id = '$soap_p_id'
                            AND p.is_delete = 0
                            AND (p.cppt_id IS NULL OR p.cppt_id = '')
                            ORDER BY p.date_added DESC
                        ")->result();
                        if ($prescription != []) {
                            for ($k=0; $k < count($prescription); $k++) { 
                                $output[$i]->prescription['no_order'] = $prescription[$k]->no_order;
                                $output[$i]->prescription['queue'] = $prescription[$k]->queue;
                                $output[$i]->prescription['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                $output[$i]->prescription['type_prescription_name'] = $prescription[$k]->flag_prescription;
                                $output[$i]->prescription['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                                $output[$i]->prescription['soap_p_id'] = $prescription[$k]->soap_p_id;
                                $output[$i]->prescription['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                                $output[$i]->prescription['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                                $output[$i]->prescription['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                                $output[$i]->prescription['flag'] = $prescription[$k]->flag;
                                $output[$i]->prescription['is_retur'] = $prescription[$k]->is_retur;
                                
                                $output[$i]->prescription_new[$idx_prescription]['no_order'] = $prescription[$k]->no_order;
                                $output[$i]->prescription_new[$idx_prescription]['queue'] = $prescription[$k]->queue;
                                $output[$i]->prescription_new[$idx_prescription]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                $output[$i]->prescription_new[$idx_prescription]['type_prescription_name'] = $prescription[$k]->flag_prescription;
                                $output[$i]->prescription_new[$idx_prescription]['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                                $output[$i]->prescription_new[$idx_prescription]['soap_p_id'] = $prescription[$k]->soap_p_id;
                                $output[$i]->prescription_new[$idx_prescription]['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                                $output[$i]->prescription_new[$idx_prescription]['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                                $output[$i]->prescription_new[$idx_prescription]['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                                $output[$i]->prescription_new[$idx_prescription]['flag'] = $prescription[$k]->flag;
                                $output[$i]->prescription_new[$idx_prescription]['is_retur'] = $prescription[$k]->is_retur;
                                $output[$i]->prescription_new[$idx_prescription]['date_added'] = $prescription[$k]->date_added;
                                
                                $prescription_id = $prescription[$k]->id;
                                // // $this->db->cache_off();
                                $form_prescription = $this->db->query("
                                    SELECT
                                    pi.pharmacy_prescription_id,
                                    pi.pharmacy_catalog_id,
                                    pc.name,
                                    pc.code,
                                    pi.prescription_item_type_id,
                                    pi.prescription_item_type_used,
                                    pi.prescription_item_type_value,
                                    pi.prescription_item_type_package,
                                    pi.package_id,
                                    pi.package_name,
                                    pi.signatura_times_id,
                                    st.name as signatura_times_name,
                                    pi.administer_id,
                                    pci.name as administer_name,
                                    pi.dose,
                                    pi.dose_unit_id,
                                    u.name as dose_unit_name,
                                    pi.dose_doctor,
                                    pi.batch,
                                    pi.expired,
                                    pi.qty_doctor,
                                    pi.qty,
                                    pi.qty_copy,
                                    pi.used,
                                    pi.desc,
                                    pi.status_kardex,
                                    pi.sort
                                    FROM pharmacy_prescription_item pi
                                    LEFT JOIN pharmacy_catalog pc ON pi.pharmacy_catalog_id = pc.id
                                    LEFT JOIN unit u ON pi.dose_unit_id = u.id
                                    LEFT JOIN signatura_times st ON pi.signatura_times_id = st.id
                                    LEFT JOIN pharmacy_catalog_instruction pci ON pi.administer_id = pci.id
                                    WHERE pi.is_delete = 0
                                    AND pi.pharmacy_prescription_id = '$prescription_id'
                                    ORDER BY pi.sort ASC
                                ")->result();
                                if ($form_prescription != []) {
                                    $form_prescription_parent = array();

                                    for ($j=0; $j < count($form_prescription); $j++) { 
                                        $package_id = $form_prescription[$j]->package_id;

                                        // $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                                        $output[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                        $output[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                        $output[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                        $output[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                        $output[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                        $output[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                                        $output[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                                        $output[$i]->form_prescription[$j]['signatura_times_id'] = $form_prescription[$j]->signatura_times_id;
                                        $output[$i]->form_prescription[$j]['signatura_times_name'] = $form_prescription[$j]->signatura_times_name;
                                        $output[$i]->form_prescription[$j]['administer_id'] = $form_prescription[$j]->administer_id;
                                        $output[$i]->form_prescription[$j]['administer_name'] = $form_prescription[$j]->administer_name;
                                        $output[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                                        $output[$i]->form_prescription[$j]['dose_unit_id'] = $form_prescription[$j]->dose_unit_id;
                                        $output[$i]->form_prescription[$j]['dose_unit_name'] = $form_prescription[$j]->dose_unit_name;
                                        // $output[$i]->form_prescription[$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                        $output[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                        $output[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                                        $output[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                                        $output[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                        $output[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                                        $output[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                        $output[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                                        $output[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                                        $output[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;

                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['package_id'] = $form_prescription[$j]->package_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['package_name'] = $form_prescription[$j]->package_name;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['signatura_times_id'] = $form_prescription[$j]->signatura_times_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['signatura_times_name'] = $form_prescription[$j]->signatura_times_name;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['administer_id'] = $form_prescription[$j]->administer_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['administer_name'] = $form_prescription[$j]->administer_name;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose'] = $form_prescription[$j]->dose;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_unit_id'] = $form_prescription[$j]->dose_unit_id;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_unit_name'] = $form_prescription[$j]->dose_unit_name;
                                        // $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['batch'] = $form_prescription[$j]->batch;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['expired'] = $form_prescription[$j]->expired;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty'] = $form_prescription[$j]->qty;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['used'] = $form_prescription[$j]->used;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['desc'] = $form_prescription[$j]->desc;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['status_kardex'] = $form_prescription[$j]->status_kardex;
                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['sort'] = $form_prescription[$j]->sort;

                                        $output[$i]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_unit_options'] = options_unit_package($package_id);
                                    
                                        //------------------- detail form_prescription_parent -//
                                        $parent_id = $form_prescription[$j]->prescription_item_type_id; 
                                        $parent_used = $form_prescription[$j]->prescription_item_type_used; 
                                        $parent_value = $form_prescription[$j]->prescription_item_type_value; 
                                        $parent_package = $form_prescription[$j]->prescription_item_type_package; 
                                        $parent_signatura_times_id = $form_prescription[$j]->signatura_times_id; 
                                        $parent_administer_id = $form_prescription[$j]->administer_id; 
                                        $parent_status_kardex = $form_prescription[$j]->status_kardex; 
                                        $parent_desc = $form_prescription[$j]->desc; 

                                        $dt_form_prescription_parent = array(
                                            'id' => $parent_id,
                                            'used' => $parent_used,
                                            'value' => $parent_value,
                                            'package' => $parent_package,
                                            'signatura_times_id' => $parent_signatura_times_id,
                                            'administer_id' => $parent_administer_id,
                                            'status_kardex' => $parent_status_kardex,
                                            'desc' => $parent_desc,
                                        );

                                        if (count($form_prescription_parent) == 0) {
                                            array_push($form_prescription_parent, $dt_form_prescription_parent);
                                        }

                                        $skip = 0;
                                        for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                                            if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                                                $skip = 1;
                                            }
                                        }

                                        if ($skip == 0) {
                                            array_push($form_prescription_parent, $dt_form_prescription_parent);
                                        }
                                        //----------------------------------------------------//
                                    }
                                    
                                    $output[$i]->prescription_new[$idx_prescription]['form_prescription_parent'] = $form_prescription_parent;
                                    
                                    $output[$i]->prescription_new = array_values($output[$i]->prescription_new);
                                } 

                                $idx_prescription++;
                            }
                        }
                    }

                    $output[$i]->soap_p_id = $soap_p[0]->id;
                    $output[$i]->therapy_plan = $soap_p[0]->therapy_plan;
                    $output[$i]->estimated_duration_treatment = $soap_p[0]->estimated_duration_treatment;
                    $output[$i]->follow_up_care_arry = ($soap_p[0]->follow_up_care_arry != '') ? explode(',', $soap_p[0]->follow_up_care_arry): [];
                    $output[$i]->diet_and_nutrition_type_id = $soap_p[0]->diet_and_nutrition_type_id;
                    $output[$i]->diet_and_nutrition = $soap_p[0]->diet_and_nutrition;
                    $output[$i]->wound_care_type_id = $soap_p[0]->wound_care_type_id;
                    $output[$i]->wound_care = $soap_p[0]->wound_care;
                    $output[$i]->medical_rehabilitation_type_id = $soap_p[0]->medical_rehabilitation_type_id;
                    $output[$i]->medical_rehabilitation = $soap_p[0]->medical_rehabilitation;
                    $output[$i]->pain_management_type_id = $soap_p[0]->pain_management_type_id;
                    $output[$i]->pain_management = $soap_p[0]->pain_management;
                    $output[$i]->discharge_planning_other = $soap_p[0]->discharge_planning_other;

                    // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
                    for ($sp=0; $sp < count($soap_p); $sp++) { 
                        // SOAP_P_ACTION
                        $this->Soap_p_action->whereFields['soap_p_id'] = $soap_p[$sp]->id;
                        $this->Soap_p_action->whereFields['a.is_delete'] = 0;
                        $soap_p_action = $this->Soap_p_action->read();
                        if($soap_p_action != []) {
                            for ($j=0; $j < count($soap_p_action); $j++) { 
                                $icd9cm = $this->Icd9cm->read($soap_p_action[$j]->icd9cm_id);
                                
                                $dt_icd9cm = array(
                                    'icd9cm_id' => $icd9cm[0]->id,
                                    'code' => $icd9cm[0]->code,
                                    'code_s' => $icd9cm[0]->code_s,
                                    'name' => $icd9cm[0]->name,
                                );

                                array_push($output[$i]->icd9cm, $dt_icd9cm);
                            }
                        }
                    }
                }

                // KARDEX
                $select_kardex = 'a.*, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
                $where_kardex = array(
                    'a.soap_id' => $soap_id,
                    'a.is_delete' => 0,
                );
                $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
                $this->db->where($where_kar);
                $join_kardex = array(
                    array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                    array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                    array('user u', 'a.user_added = u.id', 'INNER'),
                    array('employee e2', 'u.employee_id = e2.id', 'INNER'),
                );
                $order_kardex = array(
                    'a.date_added' => 'DESC',
                );
                $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);

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
                    }
                    //----------------------------------------------------//

                    $kardex[$k]->form_prescription_parent = $kardex_parent;
                    
                    if (count($kardex_item) > 0) {
                        array_push($clean_kardex, $kardex[$k]);
                    }
                }

                $output[$i]->prescription_new = array_merge($output[$i]->prescription_new, $clean_kardex);

                $select = 'a.name';
                $where = array('norm' => $output[$i]->norm, 'a.is_delete' => 0);
                $join = array(
                    array('helper h', 'a.type_allergy_id = h.code AND h.variable = "type_allergy"', 'LEFT'),
                );
                $allergies = $this->Patient_allergy->select(0, $select, $where, $join);
                if (count($allergies) > 0) {
                    $output[$i]->allergies = $allergies;
                } else {
                    $output[$i]->allergies = [];
                }
                
                // TRANSACTION_DOCTOR
                $output[$i]->transaction_doctor = [];
                $this->Transaction_doctor->whereFields['soap_id'] = $output[$i]->id;
                $this->Transaction_doctor->whereFields['a.is_delete'] = 0;
                $transaction_doctor = $this->Transaction_doctor->read();
                if($transaction_doctor != []) {
                    for ($j=0; $j < count($transaction_doctor); $j++) { 
                        $output[$i]->transaction_doctor[$j]['id'] = $transaction_doctor[$j]->id;
                        $output[$i]->transaction_doctor[$j]['costing_id'] = $transaction_doctor[$j]->costing_id;
                        $output[$i]->transaction_doctor[$j]['costing_name'] = $transaction_doctor[$j]->costing_name;
                        $output[$i]->transaction_doctor[$j]['doctor_in_charge_id'] = $transaction_doctor[$j]->doctor_in_charge_id;
                        $output[$i]->transaction_doctor[$j]['var_1'] = $transaction_doctor[$j]->var_1;
                        $output[$i]->transaction_doctor[$j]['var_2'] = $transaction_doctor[$j]->var_2;
                        $output[$i]->transaction_doctor[$j]['var_3'] = $transaction_doctor[$j]->var_3;
                        $output[$i]->transaction_doctor[$j]['doctor_ref_id'] = $transaction_doctor[$j]->doctor_ref_id;
                        $output[$i]->transaction_doctor[$j]['notes'] = $transaction_doctor[$j]->notes;
                        $output[$i]->transaction_doctor[$j]['date_added'] = $transaction_doctor[$j]->date_added;
                    }
                }

                // TRANSACTION_PHYSIOTHERAPY
                $this->Transaction_physiotherapy->whereFields['soap_id'] = $output[$i]->id;
                $this->Transaction_physiotherapy->whereFields['a.is_delete'] = 0;
                $transaction_physiotherapy = $this->Transaction_physiotherapy->read();
                if($transaction_physiotherapy != []) {
                    for ($j=0; $j < count($transaction_physiotherapy); $j++) { 
                        $output[$i]->transaction_physiotherapy[$j]['id'] = $transaction_physiotherapy[$j]->id;
                        $output[$i]->transaction_physiotherapy[$j]['costing_id'] = $transaction_physiotherapy[$j]->costing_id;
                        $output[$i]->transaction_physiotherapy[$j]['costing_name'] = $transaction_physiotherapy[$j]->costing_name;
                        $output[$i]->transaction_physiotherapy[$j]['var_1'] = $transaction_physiotherapy[$j]->var_1;
                        $output[$i]->transaction_physiotherapy[$j]['var_2'] = $transaction_physiotherapy[$j]->var_2;
                        $output[$i]->transaction_physiotherapy[$j]['var_3'] = $transaction_physiotherapy[$j]->var_3;
                        $output[$i]->transaction_physiotherapy[$j]['service_class_id'] = $transaction_physiotherapy[$j]->service_class_id;
                        $output[$i]->transaction_physiotherapy[$j]['notes'] = $transaction_physiotherapy[$j]->notes;
                        $output[$i]->transaction_physiotherapy[$j]['date_added'] = $transaction_physiotherapy[$j]->date_added;
                    }
                }

                // LABORATORY_REQUEST
                // $this->Laboratory_request->whereFields['a.soap_id'] = $output[$i]->id;
                // $this->Laboratory_request->whereFields['a.is_delete'] = 0;
                // $laboratory_request = $this->Laboratory_request->read();
                $laboratory_request = $this->db->query("
                    SELECT a.*, e.fullname as doctor_in_charge_name
                    FROM laboratory_request as a
                    INNER JOIN employee e ON a.doctor_in_charge_id = e.id
                    WHERE soap_id = '$soap_id'
                    AND a.is_delete = 0
                    AND (a.cppt_id IS NULL OR a.cppt_id = '')
                    ORDER BY a.date_request DESC
                ")->result();
                if($laboratory_request != []) {
                    for ($k=0; $k < count($laboratory_request); $k++) { 
                        $laboratory_request_id = $laboratory_request[$k]->id;

                        // LIS RESULT
                        $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                        $output[$i]->laboratory_request['id'] = $laboratory_request[$k]->id;
                        $output[$i]->laboratory_request['institute_branch_id'] = $laboratory_request[$k]->institute_branch_id;
                        $output[$i]->laboratory_request['no_request'] = $laboratory_request[$k]->no_request;
                        $output[$i]->laboratory_request['status'] = $laboratory_request[$k]->status;
                        $output[$i]->laboratory_request['soap_id'] = $laboratory_request[$k]->soap_id;
                        $output[$i]->laboratory_request['doctor_in_charge_id'] = $laboratory_request[$k]->doctor_in_charge_id;
                        $output[$i]->laboratory_request['doctor_in_charge_name'] = $laboratory_request[$k]->doctor_in_charge_name;
                        $output[$i]->laboratory_request['clinical_indications'] = $laboratory_request[$k]->clinical_indications;
                        $output[$i]->laboratory_request['date_request'] = $laboratory_request[$k]->date_request;
                        $output[$i]->laboratory_request['queue'] = $laboratory_request[$k]->queue;
                        $output[$i]->laboratory_request['is_delete'] = $laboratory_request[$k]->is_delete;

                        $output[$i]->laboratory_request_new[$k]['id'] = $laboratory_request[$k]->id;
                        $output[$i]->laboratory_request_new[$k]['institute_branch_id'] = $laboratory_request[$k]->institute_branch_id;
                        $output[$i]->laboratory_request_new[$k]['no_request'] = $laboratory_request[$k]->no_request;
                        $output[$i]->laboratory_request_new[$k]['status'] = $laboratory_request[$k]->status;
                        $output[$i]->laboratory_request_new[$k]['soap_id'] = $laboratory_request[$k]->soap_id;
                        $output[$i]->laboratory_request_new[$k]['doctor_in_charge_id'] = $laboratory_request[$k]->doctor_in_charge_id;
                        $output[$i]->laboratory_request_new[$k]['doctor_in_charge_name'] = $laboratory_request[$k]->doctor_in_charge_name;
                        $output[$i]->laboratory_request_new[$k]['clinical_indications'] = $laboratory_request[$k]->clinical_indications;
                        $output[$i]->laboratory_request_new[$k]['date_request'] = $laboratory_request[$k]->date_request;
                        $output[$i]->laboratory_request_new[$k]['queue'] = $laboratory_request[$k]->queue;
                        $output[$i]->laboratory_request_new[$k]['is_delete'] = $laboratory_request[$k]->is_delete;

                        // LABORATORY_REQUEST_ITEM
                        $this->Laboratory_request_item->whereFields['laboratory_request_id'] = $laboratory_request[$k]->id;
                        $this->Laboratory_request_item->whereFields['a.is_delete'] = 0;
                        $laboratory_request_item = $this->Laboratory_request_item->read(0,false);
                        if($laboratory_request_item != []) {
                            for ($j=0; $j < count($laboratory_request_item); $j++) { 
                                $output[$i]->laboratory_request_items[$j]['id'] = $laboratory_request_item[$j]->id;
                                $output[$i]->laboratory_request_items[$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
                                $output[$i]->laboratory_request_items[$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;                        
                                $output[$i]->laboratory_request_items[$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
                                $output[$i]->laboratory_request_items[$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
                                $output[$i]->laboratory_request_items[$j]['var_1'] = $laboratory_request_item[$j]->var_1;
                                $output[$i]->laboratory_request_items[$j]['var_2'] = $laboratory_request_item[$j]->var_2;
                                $output[$i]->laboratory_request_items[$j]['var_3'] = $laboratory_request_item[$j]->var_3;
                                $output[$i]->laboratory_request_items[$j]['service_class_id'] = $laboratory_request_item[$j]->service_class_id;
                                $output[$i]->laboratory_request_items[$j]['notes'] = $laboratory_request_item[$j]->notes;
                                $output[$i]->laboratory_request_items[$j]['status'] = $laboratory_request_item[$j]->status;
                                $output[$i]->laboratory_request_items[$j]['result'] = [];

                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['id'] = $laboratory_request_item[$j]->id;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;                        
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['var_1'] = $laboratory_request_item[$j]->var_1;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['var_2'] = $laboratory_request_item[$j]->var_2;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['var_3'] = $laboratory_request_item[$j]->var_3;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['service_class_id'] = $laboratory_request_item[$j]->service_class_id;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['notes'] = $laboratory_request_item[$j]->notes;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['status'] = $laboratory_request_item[$j]->status;
                                $output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['result'] = [];

                                // LIS RESULT
                                // $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                                $code_lis = $laboratory_request_item[$j]->code_lis;

                                if (count($lis_response) > 0) {
                                    $result = $lis_response[0]['result_detail'];
                                    
                                    for ($l=0; $l < count($result); $l++) { 
                                        $flag = $result[$l]['flag'];
                                        $parameter_id = $result[$l]['ParameterID'];
                                        
                                        // keluarin semua hasil lab
                                        // $flag != '' && 
                                        if ($parameter_id == $code_lis) {
                                            array_push($output[$i]->laboratory_request_items[$j]['result'], $result[$l]);
                                            array_push($output[$i]->laboratory_request_new[$k]['laboratory_request_items'][$j]['result'], $result[$l]);
                                        }
                                    }
                                }
                            }
                        }

                        $output[$i]->laboratory_request['result'] = $lis_response;
                    }
                }

                // RADIOLOGY_REQUEST
                // $this->Radiology_request->whereFields['a.soap_id'] = $output[$i]->id;
                // $this->Radiology_request->whereFields['a.is_delete'] = 0;
                // $radiology_request = $this->Radiology_request->read();
                $radiology_request = $this->db->query("
                    SELECT a.*, e.fullname as doctor_in_charge_name
                    FROM radiology_request as a
                    INNER JOIN employee e ON a.doctor_in_charge_id = e.id
                    WHERE soap_id = '$soap_id'
                    AND a.is_delete = 0
                    AND a.is_cancel = 0
                    AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                    ORDER BY a.date_request DESC
                ")->result();
                if($radiology_request != []) {
                    for ($k=0; $k < count($radiology_request); $k++) { 
                        $output[$i]->radiology_request['id'] = $radiology_request[$k]->id;
                        $output[$i]->radiology_request['institute_branch_id'] = $radiology_request[$k]->institute_branch_id;
                        $output[$i]->radiology_request['no_request'] = $radiology_request[$k]->no_request;
                        $output[$i]->radiology_request['status'] = $radiology_request[$k]->status;
                        $output[$i]->radiology_request['soap_id'] = $radiology_request[$k]->soap_id;
                        $output[$i]->radiology_request['doctor_in_charge_id'] = $radiology_request[$k]->doctor_in_charge_id;
                        $output[$i]->radiology_request['doctor_in_charge_name'] = $radiology_request[$k]->doctor_in_charge_name;
                        $output[$i]->radiology_request['clinical_indications'] = $radiology_request[$k]->clinical_indications;
                        $output[$i]->radiology_request['date_request'] = $radiology_request[$k]->date_request;
                        $output[$i]->radiology_request['queue'] = $radiology_request[$k]->queue;
                        $output[$i]->radiology_request['is_delete'] = $radiology_request[$k]->is_delete;

                        $output[$i]->radiology_request_new[$k]['id'] = $radiology_request[$k]->id;
                        $output[$i]->radiology_request_new[$k]['institute_branch_id'] = $radiology_request[$k]->institute_branch_id;
                        $output[$i]->radiology_request_new[$k]['no_request'] = $radiology_request[$k]->no_request;
                        $output[$i]->radiology_request_new[$k]['status'] = $radiology_request[$k]->status;
                        $output[$i]->radiology_request_new[$k]['soap_id'] = $radiology_request[$k]->soap_id;
                        $output[$i]->radiology_request_new[$k]['doctor_in_charge_id'] = $radiology_request[$k]->doctor_in_charge_id;
                        $output[$i]->radiology_request_new[$k]['doctor_in_charge_name'] = $radiology_request[$k]->doctor_in_charge_name;
                        $output[$i]->radiology_request_new[$k]['clinical_indications'] = $radiology_request[$k]->clinical_indications;
                        $output[$i]->radiology_request_new[$k]['date_request'] = $radiology_request[$k]->date_request;
                        $output[$i]->radiology_request_new[$k]['queue'] = $radiology_request[$k]->queue;
                        $output[$i]->radiology_request_new[$k]['is_delete'] = $radiology_request[$k]->is_delete;
                        
                        // RADIOLOGY_REQUEST_ITEM
                        $this->Radiology_request_item->whereFields['radiology_request_id'] = $radiology_request[$k]->id;
                        $this->Radiology_request_item->whereFields['a.is_delete'] = 0;
                        $radiology_request_item = $this->Radiology_request_item->read(0,false);
                        if($radiology_request_item != []) {
                            for ($j=0; $j < count($radiology_request_item); $j++) { 
                                $output[$i]->radiology_request_items[$j]['id'] = $radiology_request_item[$j]->id;
                                $output[$i]->radiology_request_items[$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
                                $output[$i]->radiology_request_items[$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
                                $output[$i]->radiology_request_items[$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
                                $output[$i]->radiology_request_items[$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
                                $output[$i]->radiology_request_items[$j]['var_1'] = $radiology_request_item[$j]->var_1;
                                $output[$i]->radiology_request_items[$j]['var_2'] = $radiology_request_item[$j]->var_2;
                                $output[$i]->radiology_request_items[$j]['var_3'] = $radiology_request_item[$j]->var_3;
                                $output[$i]->radiology_request_items[$j]['service_class_id'] = $radiology_request_item[$j]->service_class_id;
                                $output[$i]->radiology_request_items[$j]['notes'] = $radiology_request_item[$j]->notes;
                                $output[$i]->radiology_request_items[$j]['status'] = $radiology_request_item[$j]->status;
                                
                                $this->Radiology_request_item_image->whereFields['a.is_delete'] = 0;
                                $url = $this->Radiology_request_item_image->read($radiology_request_item[$j]->id);
                                $output[$i]->radiology_request_items[$j]['url'] = $url;

                                for ($kk=0; $kk < count($url); $kk++) { 
                                    $url_raw = $url[$kk]->url;
                                    $name = substr($url_raw, 20);
                                    
                                    $output[$i]->radiology_request_items[$j]['url'][$kk]->radiology_request_id = $radiology_request_item[$j]->radiology_request_id;
                                    $output[$i]->radiology_request_items[$j]['url'][$kk]->name = $name;
                                }

                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['id'] = $radiology_request_item[$j]->id;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['var_1'] = $radiology_request_item[$j]->var_1;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['var_2'] = $radiology_request_item[$j]->var_2;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['var_3'] = $radiology_request_item[$j]->var_3;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['service_class_id'] = $radiology_request_item[$j]->service_class_id;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['notes'] = $radiology_request_item[$j]->notes;
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['status'] = $radiology_request_item[$j]->status;
                                
                                $this->Radiology_request_item_image->whereFields['a.is_delete'] = 0;
                                $url = $this->Radiology_request_item_image->read($radiology_request_item[$j]->id);
                                $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['url'] = $url;
                                
                                for ($kk=0; $kk < count($url); $kk++) { 
                                    $url_raw = $url[$kk]->url;
                                    $name = substr($url_raw, 20);
                                    
                                    $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['url'][$kk]->radiology_request_id = $radiology_request_item[$j]->radiology_request_id;
                                    $output[$i]->radiology_request_new[$k]['radiology_request_items'][$j]['url'][$kk]->name = $name;
                                }
                            }
                        }
                    }
                }

                // PHYSIOTHERAPY_REQUEST
                $this->Physiotherapy_request->whereFields['a.soap_id'] = $output[$i]->id;
                $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
                $physiotherapy_request = $this->Physiotherapy_request->read();
                if($physiotherapy_request != []) {
                    for ($k=0; $k < count($physiotherapy_request); $k++) { 
                        $output[$i]->physiotherapy_request[$k]['id'] = $physiotherapy_request[$k]->id;
                        $output[$i]->physiotherapy_request[$k]['institute_branch_id'] = $physiotherapy_request[$k]->institute_branch_id;
                        $output[$i]->physiotherapy_request[$k]['no_request'] = $physiotherapy_request[$k]->no_request;
                        $output[$i]->physiotherapy_request[$k]['soap_id'] = $physiotherapy_request[$k]->soap_id;
                        $output[$i]->physiotherapy_request[$k]['date_request'] = $physiotherapy_request[$k]->date_request;
                        $output[$i]->physiotherapy_request[$k]['queue'] = $physiotherapy_request[$k]->queue;
                        $output[$i]->physiotherapy_request[$k]['is_delete'] = $physiotherapy_request[$k]->is_delete;

                        // PHYSIOTHERAPY_REQUEST_ITEM
                        $this->Physiotherapy_request_item->whereFields['physiotherapy_request_id'] = $physiotherapy_request[$k]->id;
                        $this->Physiotherapy_request_item->whereFields['a.is_delete'] = 0;
                        $physiotherapy_request_item = $this->Physiotherapy_request_item->read(0, false);
                        if($physiotherapy_request_item != []) {
                            for ($j=0; $j < count($physiotherapy_request_item); $j++) { 
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['id'] = $physiotherapy_request_item[$j]->id;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['service_class_id'] = $physiotherapy_request_item[$j]->service_class_id;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                                $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['status'] = $physiotherapy_request_item[$j]->status;

                                $output[$i]->physiotherapy_request_items[$j]['id'] = $physiotherapy_request_item[$j]->id;
                                $output[$i]->physiotherapy_request_items[$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
                                $output[$i]->physiotherapy_request_items[$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
                                $output[$i]->physiotherapy_request_items[$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
                                $output[$i]->physiotherapy_request_items[$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
                                $output[$i]->physiotherapy_request_items[$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
                                $output[$i]->physiotherapy_request_items[$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
                                $output[$i]->physiotherapy_request_items[$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
                                $output[$i]->physiotherapy_request_items[$j]['service_class_id'] = $physiotherapy_request_item[$j]->service_class_id;
                                $output[$i]->physiotherapy_request_items[$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                                $output[$i]->physiotherapy_request_items[$j]['status'] = $physiotherapy_request_item[$j]->status;
                            }
                        }
                    }
                }

                $soap_id = $output[$i]->id;
                
                $is_medical_rehabilitation = $output[$i]->is_medical_rehabilitation;
                if ($is_medical_rehabilitation == 1) {
                    $select = 'a.*';
                    $where = array(
                        'soap_id' => $soap_id
                    );
                    $medical_rehabilitation = $this->Medical_rehabilitation->select(0, $select, $where);

                    if (count($medical_rehabilitation) > 0) {
                        $output[$i]->service_date = $medical_rehabilitation[0]->service_date;
                        $output[$i]->anamnesa = $medical_rehabilitation[0]->anamnesa;
                        $output[$i]->functional_testing = $medical_rehabilitation[0]->functional_testing;
                        $output[$i]->functional_diagnosis = $medical_rehabilitation[0]->functional_diagnosis;
                        $output[$i]->medical_diagnosis = $medical_rehabilitation[0]->medical_diagnosis;
                        $output[$i]->support_examination = $medical_rehabilitation[0]->support_examination;
                        $output[$i]->advice_rehab = $medical_rehabilitation[0]->advice;
                        $output[$i]->evaluation = $medical_rehabilitation[0]->evaluation;
                        $output[$i]->suspected_disease_type_id = $medical_rehabilitation[0]->suspected_disease_type_id;
                        $output[$i]->suspected_disease = $medical_rehabilitation[0]->suspected_disease;
                        $output[$i]->result = $medical_rehabilitation[0]->result;
                        $output[$i]->conclusion = $medical_rehabilitation[0]->conclusion;
                        $output[$i]->recommendation = $medical_rehabilitation[0]->recommendation;
                    }
                }

                $is_hemodialysis = $output[$i]->is_hemodialysis;
                if ($is_hemodialysis == 1) {
                    $select = 'a.*';
                    $where = array(
                        'soap_id' => $soap_id,
                    );
                    $hemodialysis_initiation = $this->Hemodialysis_initiation->select(0, $select, $where);

                    if (count($hemodialysis_initiation) > 0 ) {
                        $output[$i]->main_diagnosis = $hemodialysis_initiation[0]->main_diagnosis;
                        $output[$i]->etiology = $hemodialysis_initiation[0]->etiology;
                        $output[$i]->comorbidities = $hemodialysis_initiation[0]->comorbidities;
                        $output[$i]->prescription_hd = $hemodialysis_initiation[0]->prescription_hd;
                        $output[$i]->frequency_hd = $hemodialysis_initiation[0]->frequency_hd;
                        $output[$i]->vascular_access = $hemodialysis_initiation[0]->vascular_access;
                    }
                }
                
                $is_medical_check_up = $output[$i]->is_medical_check_up;
                if ($is_medical_check_up == 1) {
                    $select = 'a.*';
                    $where = array(
                        'soap_id' => $soap_id
                    );
                    $medical_check_up = $this->Medical_check_up->select(0, $select, $where);

                    if (count($medical_check_up) > 0) {
                        $output[$i]->fit_to_work = $medical_check_up[0]->fit_to_work;
                        $output[$i]->consultation = $medical_check_up[0]->consultation;
                    }
                }
            }
            $output = array_values($output);
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

    public function get_cppt($registration_id = null) {
        if ($registration_id == null) {
            return [];
        }

        // $select = 'a.id,
        // a.soap_id,
        // a.registration_destination_id,
        // u.group_id,
        // g.name as group_name,
        // a.user_added,
        // a.date_added,
        // e.id as doctor_in_charge_id,
        // e.fullname as doctor_in_charge_name,
        // COALESCE(o.health_facility_item_id, rd.health_facility_item_id) as health_facility_item_id,
        // COALESCE(hfio.name, hfi.name) as health_facility_item_name,
        // a.subjective,
        // a.nursing_objective,
        // a.plan,
        // a.pharmacological_plan,
        // a.nursing_plan,
        // a.advice,
        // a.nutrition_assessment,
        // a.nutrition_diagnosis,
        // a.nutrition_intervention,
        // a.nutrition_monitoring_evaluation,
        // a.systolic_blood_pressure,
        // a.diastolic_blood_pressure,
        // a.pulse_frequency,
        // a.breath_frequency,
        // a.oximetry_frequency,
        // a.temperature,
        // a.aware,
        // a.oxygen_type_id,
        // a.eye_score,
        // a.verbal_score,
        // a.motor_score,
        // a.skin_color,
        // a.behavior,
        // a.weight,
        // a.height,
        // a.imt,
        // a.head,
        // a.head_hair_color,
        // a.head_hair_condition,
        // a.eye_conjunctive,
        // a.eye_sclera,
        // a.eye_pupil,
        // a.eye_light_reflex,
        // a.mouth_mucosa,
        // a.mouth_discoloration,
        // a.mouth_tongue,
        // a.mouth_caries,
        // a.neck_kgb,
        // a.neck_trachea_position,
        // a.neck_massa,
        // a.neck_jvp,
        // a.thorax_insp_chest,
        // a.thorax_insp_retraction,
        // a.thorax_palp_chest,
        // a.thorax_palp_fremitus,
        // a.thorax_palp_crepitation,
        // a.thorax_ausk_pul_vasiculer,
        // a.thorax_ausk_pul_rhonki,
        // a.thorax_ausk_pul_wheezing,
        // a.thorax_ausk_cor_heart_sound,
        // a.thorax_ausk_cor_murmur,
        // a.thorax_ausk_cor_gallop,
        // a.abdomen_supel,
        // a.abdomen_bowel_sounds,
        // a.abdomen_tenderness,
        // a.abdomen_hepar_lien,
        // a.abdomen_other_physical_check,
        // a.extremity_acral,
        // a.extremity_crt,
        // a.extremity_edema,
        // a.genitalia,
        // a.localist_status,
        // a.ophthal_status
        // ';

        $select = 'a.id,
        a.registration_destination_id,
        u.group_id,
        g.name as group_name,
        a.user_added,
        a.date_added,
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
        a.eye_blind_color,
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
        a.registration_destination_id
        ';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('user_group u', 'a.user_added = u.user_id', 'INNER'),
            array('group g', 'u.group_id = g.id', 'INNER'),
            array('origin o', 'a.id = o.source_id AND o.source_type="cppt"', 'LEFT'),
            array('health_facility_item hfio', 'o.health_facility_item_id = hfio.id', 'LEFT'),
        );
        $order = array(
            'a.date_added' => 'DESC',
        );
        $cppt = $this->Cppt->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($cppt); $i++) { 
            $cppt_id = $cppt[$i]->id;
            // $soap_id = $cppt[$i]->soap_id;
            $user_added = $cppt[$i]->user_added;

            $select = 'a.id, a.username, e.fullname as user_added_name, g.name as user_role';
            $where = array();
            $join = array(
                array('user_group ug', 'a.id = ug.user_id', 'INNER'),
                array('group g', 'ug.group_id = g.id', 'INNER'),
                array('employee e', 'a.employee_id = e.id', 'INNER'),
            );
            $user = $this->User->select($user_added, $select, $where, $join);

            $cppt[$i]->user_added_name = $user[0]->user_added_name;
            $cppt[$i]->user_added_role = $user[0]->user_role;

            // $cppt[$i]->icd10_notes = '';
            // $cppt[$i]->non_icd10 = [];
            // $cppt[$i]->icd10 = [];

            // // SOAP_A
            // $select = 'a.id, a.icd10_notes, a.non_icd10, a.nursing_diagnose';
            // $where = array(
            //     'a.cppt_id' => $cppt_id
            // );
            // $soap_a = $this->Soap_a->select(0, $select, $where);
            // if (count($soap_a) > 0) {
            //     $soap_a_id = $soap_a[0]->id;

            //     $cppt[$i]->icd10_notes = $soap_a[0]->icd10_notes;
            //     $cppt[$i]->non_icd10 = $soap_a[0]->non_icd10 ?? '' != '' ? explode('|', $soap_a[0]->non_icd10) : [];
            //     $cppt[$i]->nursing_diagnose = $soap_a[0]->nursing_diagnose;

            //     $select = 'a.id, a.icd10_id, icd.code as icd10_code, icd.name as icd10_name';
            //     $where = array(
            //         'a.soap_a_id' => $soap_a_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('icd10 icd', 'a.icd10_id = icd.id', 'INNER'),
            //     );
            //     $order = array('a.index' => 'ASC');
            //     $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);

            //     $cppt[$i]->icd10 = $soap_a_action;
            // }

            // // SOAP_P
            // $select = 'a.id, a.therapy_plan';
            // $where = array(
            //     'a.cppt_id' => $cppt_id
            // );
            // $soap_p = $this->Soap_p->select(0, $select, $where);
            // if (count($soap_p) > 0) {
            //     $soap_p_id = $soap_p[0]->id;

            //     $cppt[$i]->therapy_plan = $soap_p[0]->therapy_plan;

            //     $select = 'a.id, a.icd9cm_id, icd.code as icd9cm_code, icd.name as icd9cm_name';
            //     $where = array(
            //         'a.soap_p_id' => $soap_p_id,
            //     );
            //     $join = array(
            //         array('icd9cm icd', 'a.icd9cm_id = icd.id', 'INNER'),
            //     );
            //     $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);

            //     $cppt[$i]->icd9cm = $soap_p_action;
            // }

            // // TRANSACTION_DOCTOR
            // $select = 'a.id, a.var_2, a.costing_id, a.costing_name';
            // $where = array(
            //     'a.cppt_id' => $cppt_id,
            //     'a.is_delete' => 0,
            // );
            // $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);
            
            // $cppt[$i]->transaction_doctor = $transaction_doctor;

            // // LABORATORY_REQUEST
            // $select = 'a.id, a.no_request, a.date_request';
            // $where = array(
            //     'a.cppt_id' => $cppt_id,
            //     'a.soap_id' => $soap_id,
            //     'a.is_delete' => 0,
            // );
            // $join = array(
            //     array('soap s', 'a.soap_id = s.id', 'INNER'),
            // );
            // $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

            // $cppt[$i]->laboratory_request = $laboratory_request;

            // for ($j=0; $j < count($laboratory_request); $j++) { 
            //     $laboratory_request_id = $laboratory_request[$j]->id;

            //     $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis';
            //     $where = array(
            //         'a.laboratory_request_id' => $laboratory_request_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
            //     );
            //     $laboratory_request_items = $this->Laboratory_request_item->select(0, $select, $where, $join);

            //     // LIS RESULT
            //     $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

            //     for ($k=0; $k < count($laboratory_request_items); $k++) { 
            //         $code_lis = $laboratory_request_items[$k]->code_lis;
            //         $laboratory_request_items[$k]->result = [];

            //         if (count($lis_response) > 0) {
            //             $result = $lis_response[0]['result_detail'];

            //             for ($l=0; $l < count($result); $l++) { 
            //                 $flag = $result[$l]['flag'];
            //                 $parameter_id = $result[$l]['ParameterID'];

            //                 // keluarin semua hasil lab
            //                 // $flag != '' && 
            //                 if ($parameter_id == $code_lis) {
            //                     array_push($laboratory_request_items[$k]->result, $result[$l]);
            //                 }
            //             }
            //         }
            //     }

            //     $cppt[$i]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
            // }

            // // RADIOLOGY_REQUEST
            // $select = 'a.id, a.no_request, a.date_request, a.clinical_indications';
            // $where = array(
            //     'a.cppt_id' => $cppt_id,
            //     'a.soap_id' => $soap_id,
            //     'a.is_delete' => 0,
            // );
            // $join = array(
            //     array('soap s', 'a.soap_id = s.id', 'INNER'),
            // );
            // $radiology_request = $this->Radiology_request->select(0, $select, $where);

            // $cppt[$i]->radiology_request = $radiology_request;

            // for ($j=0; $j < count($radiology_request); $j++) { 
            //     $radiology_request_id = $radiology_request[$j]->id;

            //     $select = 'a.id, a.radiology_request_id, a.radiology_package_id, rp.name as radiology_package_name, a.service_class_id';
            //     $where = array(
            //         'a.radiology_request_id' => $radiology_request_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('radiology_package rp', 'a.radiology_package_id = rp.id', 'INNER'),
            //     );
            //     $radiology_request_items = $this->Radiology_request_item->select(0, $select, $where, $join);

            //     for ($k=0; $k < count($radiology_request_items); $k++) { 
            //         $radiology_request_item_id = $radiology_request_items[$k]->id;

            //         $select = 'a.id,
            //         a.radiology_request_item_id,
            //         e.fullname as doctor_lab_name,
            //         e2.fullname as user_lab_name,
            //         a.date_request,
            //         a.date_process,
            //         a.date_result,
            //         a.conclusion,
            //         a.advice,
            //         a.url
            //         ';
            //         $where = array(
            //             'a.radiology_request_item_id' => $radiology_request_item_id,
            //             'a.is_delete' => 0,
            //         );
            //         $join = array(
            //             array('employee e', 'a.doctor_lab_id = e.id', 'LEFT'),
            //             array('employee e2', 'a.user_lab_id = e2.id', 'LEFT'),
            //         );
            //         $url = $this->Radiology_request_item_image->select(0, $select, $where, $join);

            //         for ($l=0; $l < count($url); $l++) { 
            //             $url_raw = $url[$l]->url;
            //             $name = substr($url_raw, 20);

            //             $url[$l]->radiology_request_id = $radiology_request_id;
            //             $url[$l]->name = $name;
            //         }

            //         $radiology_request_items[$k]->url = $url;
            //     }

            //     $cppt[$i]->radiology_request[$j]->radiology_request_items = $radiology_request_items;
            // }

            // // PHYSIOTHERAPY_REQUEST
            // $select = 'a.id, a.no_request, a.date_request';
            // $where = array(
            //     'a.cppt_id' => $cppt_id,
            //     'a.soap_id' => $soap_id,
            //     'a.is_delete' => 0,
            // );
            // $join = array(
            //     array('soap s', 'a.soap_id = s.id', 'INNER'),
            // );
            // $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);

            // $cppt[$i]->physiotherapy_request = $physiotherapy_request;

            // for ($j=0; $j < count($physiotherapy_request); $j++) { 
            //     $physiotherapy_request_id = $physiotherapy_request[$j]->id;

            //     $select = 'a.id, a.physiotherapy_request_id, a.physiotherapy_package_id, a.costing_name as physiotherapy_package_name, a.service_class_id';
            //     $where = array(
            //         'a.physiotherapy_request_id' => $physiotherapy_request_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('physiotherapy_package rp', 'a.physiotherapy_package_id = rp.id', 'INNER'),
            //     );
            //     $physiotherapy_request_items = $this->Physiotherapy_request_item->select(0, $select, $where, $join);

            //     $cppt[$i]->physiotherapy_request[$j]->physiotherapy_request_items = $physiotherapy_request_items;
            // }

            // // PRESCRIPTION
            // // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            // $select = 'a.id, a.no_order, a.flag, a.date_added';
            // $where = array(
            //     'a.cppt_id' => $cppt_id,
            //     'a.is_delete' => 0,
            // );
            // $join = array(
            //     array('cppt c', 'a.cppt_id = c.id', 'INNER'),
            // );
            // $prescription = $this->Prescription->select(0, $select, $where, $join);

            // $cppt[$i]->prescription = [];

            // for ($k=0; $k < count($prescription); $k++) { 
            //     $pharmacy_prescription_id = $prescription[$k]->id;

            //     $select = '
            //         a.pharmacy_catalog_id, 
            //         pc.name as pharmacy_catalog_name, 
            //         pc.code as pharmacy_catalog_code,
            //         a.prescription_item_type_id, 
            //         a.prescription_item_type_used, 
            //         a.prescription_item_type_value,
            //         a.prescription_item_type_package,
            //         a.package_id,
            //         a.package_id,
            //         a.package_name, 
            //         a.dose, 
            //         p.name as dose_name, 
            //         a.qty, 
            //         a.sort, 
            //         a.used, 
            //         a.desc, 
            //         a.qty_doctor
            //     ';
            //     $where = array(
            //         'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            //         array('package p', 'pc.dose_id = p.id', 'LEFT'),
            //     );
            //     $prescription_item = $this->Prescription_item->select(0, $select, $where, $join);

            //     $prescription[$k]->form_prescription = $prescription_item;

            //     $form_prescription_parent = [];

            //     //------------------- detail form_prescription_parent -//
            //     for ($l=0; $l < count($prescription_item); $l++) { 
            //         $parent_id = $prescription_item[$l]->prescription_item_type_id; 
            //         $parent_used = $prescription_item[$l]->prescription_item_type_used; 
            //         $parent_value = $prescription_item[$l]->prescription_item_type_value; 
            //         $parent_package = $prescription_item[$l]->prescription_item_type_package; 

            //         $dt_form_prescription_parent = array(
            //             'id' => $parent_id,
            //             'used' => $parent_used,
            //             'value' => $parent_value,
            //             'package' => $parent_package,
            //         );

            //         if (count($form_prescription_parent) == 0) {
            //             array_push($form_prescription_parent, $dt_form_prescription_parent);
            //         }

            //         $skip = 0;
            //         for ($m=0; $m < count($form_prescription_parent); $m++) { 
            //             if ($form_prescription_parent[$m]['id'] == $parent_id) {
            //                 $skip = 1;
            //             }
            //         }

            //         if ($skip == 0) {
            //             array_push($form_prescription_parent, $dt_form_prescription_parent);
            //         }
            //     }
            //     //----------------------------------------------------//

            //     $prescription[$k]->form_prescription_parent = $form_prescription_parent;
            // }

            // // KARDEX
            // $select_kardex = 'a.*, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            // $where_kardex = array(
            //     'a.cppt_id' => $cppt_id,
            //     'a.is_delete' => 0,
            // );
            // $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
            // $this->db->where($where_kar);
            // $join_kardex = array(
            //     array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            //     array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            //     array('user u', 'a.user_added = u.id', 'INNER'),
            //     array('employee e2', 'u.employee_id = e2.id', 'INNER'),
            // );
            // $order_kardex = array(
            //     'a.date_added' => 'DESC',
            // );
            // $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);

            // $clean_kardex = [];

            // for ($k=0; $k < count($kardex); $k++) { 
            //     $kardex_id = $kardex[$k]->id;

            //     $select_kardex_item = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name';
            //     $where_kardex_item = array(
            //         'a.kardex_id' => $kardex_id,
            //         'a.is_change' => 1,
            //     );
            //     $join_item = array(
            //         array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            //         array('package p', 'a.package_id = p.id', 'INNER'),
            //     );
            //     $kardex_item = $this->Kardex_item->select(0, $select_kardex_item, $where_kardex_item, $join_item);

            //     $kardex[$k]->form_prescription = $kardex_item;

            //     $kardex_parent = [];

            //     //------------------- detail dt_kardex_parent -//
            //     for ($l=0; $l < count($kardex_item); $l++) { 
            //         $parent_id = $kardex_item[$l]->prescription_item_type_id; 
            //         $parent_used = $kardex_item[$l]->prescription_item_type_used; 
            //         $parent_value = $kardex_item[$l]->prescription_item_type_value; 
            //         $parent_package = $kardex_item[$l]->prescription_item_type_package; 
            //         $parent_desc = $kardex_item[$l]->prescription_item_type_desc; 

            //         $dt_kardex_parent = array(
            //             'id' => $parent_id,
            //             'used' => $parent_used,
            //             'value' => $parent_value,
            //             'package' => $parent_package,
            //             'desc' => $parent_desc,
            //         );

            //         if (count($dt_kardex_parent) == 0) {
            //             array_push($kardex_parent, $dt_kardex_parent);
            //         }

            //         $skip = 0;
            //         for ($m=0; $m < count($kardex_parent); $m++) { 
            //             if ($kardex_parent[$m]['id'] == $parent_id) {
            //                 $skip = 1;
            //             }
            //         }

            //         if ($skip == 0) {
            //             array_push($kardex_parent, $dt_kardex_parent);
            //         }
            //     }
            //     //----------------------------------------------------//

            //     $kardex[$k]->form_prescription_parent = $kardex_parent;
                
            //     if (count($kardex_item) > 0) {
            //         array_push($clean_kardex, $kardex[$k]);
            //     }
            // }

            // $cppt[$i]->prescription = array_merge($cppt[$i]->prescription, $prescription, $clean_kardex);
        }

        return $cppt;
    }

    public function get_sbar($registration_id = null) {
        if ($registration_id == null) {
            return [];
        }

        // $select = 'a.id,
        // a.soap_id,
        // a.registration_destination_id,
        // u.group_id,
        // g.name as group_name,
        // a.user_added,
        // a.date_added,
        // e.id as doctor_in_charge_id,
        // e.fullname as doctor_in_charge_name,
        // e2.id as doctor_in_charge_id_sbar,
        // e2.fullname as doctor_in_charge_name_sbar,
        // e.ttd as doctor_in_charge_ttd,
        // e2.ttd as doctor_in_charge_ttd_sbar,
        // rd.health_facility_item_id,
        // hfi.name as health_facility_item_name,
        // a.consultation_via_type_id,
        // a.consultation_via_anything_else,
        // a.sbar,
        // a.advice,
        // a.decline_reason,
        // a.validation_status,
        // a.verification_status,
        // a.validation_time,
        // a.verification_time
        // ';

        $select = 'a.id,
        a.registration_destination_id,
        u.group_id,
        g.name as group_name,
        a.user_added,
        a.date_added,
        rd.health_facility_item_id,
        hfi.name as health_facility_item_name,
        a.decline_reason,
        a.validation_status,
        a.verification_status,
        a.validation_time,
        a.verification_time
        ';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('employee e2', 'a.doctor_in_charge_id_sbar = e2.id', 'LEFT'),
            array('user_group u', 'a.user_added = u.user_id', 'INNER'),
            array('group g', 'u.group_id = g.id', 'INNER'),
        );
        $order = array(
            'a.date_added' => 'DESC',
        );
        $sbar = $this->Sbar->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($sbar); $i++) { 
            $user_added = $sbar[$i]->user_added;
            $sbar_id = $sbar[$i]->id;
            // $soap_id = $sbar[$i]->soap_id;

            $select = 'a.id, a.employee_id';
            $user = $this->User->select($user_added, $select);

            $employee_id = $user[0]->employee_id;

            $select = 'a.id, a.fullname, p.name as position_name, a.ttd';
            $where = array();
            $join = array(
                array('employee_position ep', 'a.id = ep.employee_id', 'LEFT'),
                array('position p', 'ep.position_id = p.id', 'LEFT'),
            );
            $order = array('a.id' => 'ASC');
            $employee = $this->Employee->select($employee_id, $select, $where, $join, $order);

            $sbar[$i]->user_added_name = $employee[0]->fullname;
            $sbar[$i]->user_added_role = $employee[0]->position_name;
            $sbar[$i]->user_added_ttd = $employee[0]->ttd;

            // $select = 'a.id, a.no_request, a.status, a.date_request';
            // $where = array(
            //     'a.sbar_id' => $sbar_id,
            //     'a.soap_id' => $soap_id,
            //     'a.is_delete' => 0,
            // );
            // $join = array(
            //     array('soap s', 'a.soap_id = s.id', 'INNER'),
            // );
            // $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

            // $sbar[$i]->laboratory_request = $laboratory_request;

            // for ($j=0; $j < count($laboratory_request); $j++) { 
            //     $laboratory_request_id = $laboratory_request[$j]->id;

            //     $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, a.costing_name, a.service_class_id, lp.code_lis';
            //     $where = array(
            //         'a.laboratory_request_id' => $laboratory_request_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
            //     );
            //     $laboratory_request_items = $this->Laboratory_request_item->select(0, $select, $where, $join);

            //     // LIS RESULT
            //     $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

            //     for ($k=0; $k < count($laboratory_request_items); $k++) { 
            //         $code_lis = $laboratory_request_items[$k]->code_lis;
            //         $laboratory_request_items[$k]->result = [];

            //         if (count($lis_response) > 0) {
            //             $result = $lis_response[0]['result_detail'];

            //             for ($l=0; $l < count($result); $l++) { 
            //                 $flag = $result[$l]['flag'];
            //                 $parameter_id = $result[$l]['ParameterID'];

            //                 // keluarin semua hasil lab
            //                 // $flag != '' && 
            //                 if ($parameter_id == $code_lis) {
            //                     array_push($laboratory_request_items[$k]->result, $result[$l]);
            //                 }
            //             }
            //         }
            //     }

            //     $sbar[$i]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
            // }

            // // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
            // $select = 'a.id, a.no_order, a.type_prescription_id, a.flag, a.date_added';
            // $where = array(
            //     'a.sbar_id' => $sbar_id,
            //     'a.is_delete' => 0,
            // );
            // $join = array(
            //     array('sbar c', 'a.sbar_id = c.id', 'INNER'),
            // );
            // $prescription = $this->Prescription->select(0, $select, $where, $join);

            // $sbar[$i]->prescription = [];

            // for ($k=0; $k < count($prescription); $k++) { 
            //     $pharmacy_prescription_id = $prescription[$k]->id;

            //     $select = '
            //         a.pharmacy_catalog_id, 
            //         pc.name as pharmacy_catalog_name, 
            //         pc.code as pharmacy_catalog_code,
            //         a.prescription_item_type_id, 
            //         a.prescription_item_type_used, 
            //         a.prescription_item_type_value,
            //         a.prescription_item_type_package,
            //         a.package_id,
            //         a.package_id,
            //         a.package_name, 
            //         a.dose, 
            //         p.name as dose_name, 
            //         a.qty, 
            //         a.sort, 
            //         a.used, 
            //         a.desc, 
            //         a.qty_doctor
            //     ';
            //     $where = array(
            //         'a.pharmacy_prescription_id' => $pharmacy_prescription_id,
            //         'a.is_delete' => 0,
            //     );
            //     $join = array(
            //         array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            //         array('package p', 'pc.dose_id = p.id', 'LEFT'),
            //     );
            //     $prescription_item = $this->Prescription_item->select(0, $select, $where, $join);

            //     $prescription[$k]->form_prescription = $prescription_item;

            //     $form_prescription_parent = [];

            //     //------------------- detail form_prescription_parent -//
            //     for ($l=0; $l < count($prescription_item); $l++) { 
            //         $parent_id = $prescription_item[$l]->prescription_item_type_id; 
            //         $parent_used = $prescription_item[$l]->prescription_item_type_used; 
            //         $parent_value = $prescription_item[$l]->prescription_item_type_value; 
            //         $parent_package = $prescription_item[$l]->prescription_item_type_package; 

            //         $dt_form_prescription_parent = array(
            //             'id' => $parent_id,
            //             'used' => $parent_used,
            //             'value' => $parent_value,
            //             'package' => $parent_package,
            //         );

            //         if (count($form_prescription_parent) == 0) {
            //             array_push($form_prescription_parent, $dt_form_prescription_parent);
            //         }

            //         $skip = 0;
            //         for ($m=0; $m < count($form_prescription_parent); $m++) { 
            //             if ($form_prescription_parent[$m]['id'] == $parent_id) {
            //                 $skip = 1;
            //             }
            //         }

            //         if ($skip == 0) {
            //             array_push($form_prescription_parent, $dt_form_prescription_parent);
            //         }
            //     }
            //     //----------------------------------------------------//

            //     $prescription[$k]->form_prescription_parent = $form_prescription_parent;
            // }

            // // KARDEX
            // $select_kardex = 'a.*, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            // $where_kardex = array(
            //     'a.sbar_id' => $sbar_id,
            //     'a.is_delete' => 0,
            // );
            // $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
            // $this->db->where($where_kar);
            // $join_kardex = array(
            //     array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            //     array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            //     array('user u', 'a.user_added = u.id', 'INNER'),
            //     array('employee e2', 'u.employee_id = e2.id', 'INNER'),
            // );
            // $order_kardex = array(
            //     'a.date_added' => 'DESC',
            // );
            // $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);

            // $clean_kardex = [];

            // for ($k=0; $k < count($kardex); $k++) { 
            //     $kardex_id = $kardex[$k]->id;

            //     $select_kardex_item = 'a.*, pc.name as pharmacy_catalog_name, p.name as package_name';
            //     $where_kardex_item = array(
            //         'a.kardex_id' => $kardex_id,
            //         'a.is_change' => 1,
            //     );
            //     $join_item = array(
            //         array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
            //         array('package p', 'a.package_id = p.id', 'INNER'),
            //     );
            //     $kardex_item = $this->Kardex_item->select(0, $select_kardex_item, $where_kardex_item, $join_item);

            //     $kardex[$k]->form_prescription = $kardex_item;

            //     $kardex_parent = [];

            //     //------------------- detail dt_kardex_parent -//
            //     for ($l=0; $l < count($kardex_item); $l++) { 
            //         $parent_id = $kardex_item[$l]->prescription_item_type_id; 
            //         $parent_used = $kardex_item[$l]->prescription_item_type_used; 
            //         $parent_value = $kardex_item[$l]->prescription_item_type_value; 
            //         $parent_package = $kardex_item[$l]->prescription_item_type_package; 
            //         $parent_desc = $kardex_item[$l]->prescription_item_type_desc; 

            //         $dt_kardex_parent = array(
            //             'id' => $parent_id,
            //             'used' => $parent_used,
            //             'value' => $parent_value,
            //             'package' => $parent_package,
            //             'desc' => $parent_desc,
            //         );

            //         if (count($dt_kardex_parent) == 0) {
            //             array_push($kardex_parent, $dt_kardex_parent);
            //         }

            //         $skip = 0;
            //         for ($m=0; $m < count($kardex_parent); $m++) { 
            //             if ($kardex_parent[$m]['id'] == $parent_id) {
            //                 $skip = 1;
            //             }
            //         }

            //         if ($skip == 0) {
            //             array_push($kardex_parent, $dt_kardex_parent);
            //         }
            //     }
            //     //----------------------------------------------------//

            //     $kardex[$k]->form_prescription_parent = $kardex_parent;
                
            //     if (count($kardex_item) > 0) {
            //         array_push($clean_kardex, $kardex[$k]);
            //     }
            // }

            // $sbar[$i]->prescription = array_merge($sbar[$i]->prescription, $prescription, $clean_kardex);
        }

        return $sbar;
    }

    /**
     * GET Soap with API
     * -------------------------
     * @method: GET
     */

    public function detail_inpatient_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Soap_inpatient";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            if(isset($q_strings['registration_destination_id'])) {
                $registration_destination_id = $q_strings['registration_destination_id']; 
                unset($q_strings['registration_destination_id']);
            }
            $this->$model->whereFields = $q_strings;
            if($registration_destination_id) {
                $this->$model->whereFields['s.registration_destination_id'] = $registration_destination_id;
            }
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            for ($i=0; $i < count($output); $i++) { 
                // INSURANCE
                $this->Insurance->whereFields['norm'] = $output[$i]->norm;
                $this->Insurance->whereFields['insurance_id'] = $output[$i]->insurance_id;
                $insurance = $this->Insurance->read();
                if($insurance != []) {
                    $output[$i]->insurance = $insurance;
                }
                // RESPONSIBLE
                $this->Responsible_person->whereFields['norm'] = $output[$i]->norm;
                $this->Responsible_person->whereFields['responsible_id'] = $output[$i]->responsible_id;
                $responsible_person = $this->Responsible_person->read();
                if($responsible_person != []) {
                    $output[$i]->responsible_person = $responsible_person[0];
                }
            }
            $response_data = array();
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
            $this->$model->limit = $limit; 
            unset($q_strings['limit']);
        } 
        
        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($q_strings['offset']);
        } else {
            $offset = 0;
        }

        $where = $q_strings;

        if (isset($q_strings['norm'])) {
            $norm = $q_strings['norm'];
            $where['r.norm'] = $norm;
            unset($where['norm']);
        }
        
        $cppt = [];
        $sbar = [];

        if (isset($q_strings['registration_id'])) {
            $registration_id = $q_strings['registration_id'];
            $cppt = $this->get_cppt($registration_id);
            $sbar = $this->get_sbar($registration_id);
        } 

        if (isset($q_strings['not_registration_id'])) {
            if ($q_strings['norm'] == null) {
                $this->REST_Return(400, 'norm is required if filtering not_registration_id');
                return;
            }
            $not_registration_id = $q_strings['not_registration_id'];
            $this->$model->whereNotInFields['registration_id'] = $not_registration_id;
            unset($where['not_registration_id']);
        }
        
        $select = 'a.*,
        e.name as doctor_in_charge_name,
        r.norm,
        r.register_date,
        rd.checkin_date,
        rd.health_facility_item_id,
        hfi.name as health_facility_item_name,
        hfi.health_facility_id,
        hf.name as health_facility_name
        ';
        $where['a.is_delete'] = 0;
        $where['a.is_resume_exit'] = 0;
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
        );
        $order = array('a.date_added' => 'DESC');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
                $soap_id = $output[$i]->id;
                $norm = $output[$i]->norm;
                // $health_facility_id = $output[$i]->health_facility_id;
                $is_initial_inpatient = $output[$i]->is_initial_inpatient;
                
                // LOS
                $checkin_date = $output[$i]->register_date;
                $checkin = new DateTime($checkin_date);
                $now = new DateTime(date('Y-m-d H:i:s'));
                $diff = $now->diff($checkin);
                $output[$i]->los = $diff->d . " Hari " . $diff->h . " Jam";

                // // CONDITION IF INPATIENT DON'T SHOW CPPT IN LIST SOAP
                // // if ($health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                // //     if ($is_initial_inpatient == 0) {
                // //         unset($output[$i]);
                // //         continue;
                // //     }
                // // }

                // SOAP_S
                $output[$i]->soap_s_id = null;
                $output[$i]->anamesa_type_id = null;
                $output[$i]->family_name = null;
                $output[$i]->family_relation_id = null;
                $output[$i]->main_complaint = null;
                $output[$i]->history_disease_now = null;
                $output[$i]->history_disease_past = null;
                $output[$i]->history_disease_family = null;
                $output[$i]->history_habitude = null;
                $output[$i]->history_drugs = null;
                $output[$i]->history_allergy = null;

                $select_soap_s = 'a.id,
                a.anamesa_type_id,
                a.family_name,
                a.family_relation_id,
                a.main_complaint,
                a.history_disease_now,
                a.history_disease_past,
                a.history_disease_family,
                a.history_habitude,
                a.history_drugs,
                a.history_allergy
                ';
                $where_soap_s = array(
                    'a.soap_id' => $soap_id
                );
                $soap_s = $this->Soap_s->select(0, $select_soap_s, $where_soap_s);
                if(count($soap_s) > 0) {
                    $output[$i]->soap_s_id = $soap_s[0]->id;
                    $output[$i]->anamesa_type_id = $soap_s[0]->anamesa_type_id;
                    $output[$i]->family_name = $soap_s[0]->family_name;
                    $output[$i]->family_relation_id = $soap_s[0]->family_relation_id;
                    $output[$i]->main_complaint = $soap_s[0]->main_complaint;
                    $output[$i]->history_disease_now = $soap_s[0]->history_disease_now;
                    $output[$i]->history_disease_past = $soap_s[0]->history_disease_past;
                    $output[$i]->history_disease_family = $soap_s[0]->history_disease_family;
                    $output[$i]->history_habitude = $soap_s[0]->history_habitude;
                    $output[$i]->history_drugs = $soap_s[0]->history_drugs;
                    $output[$i]->history_allergy = $soap_s[0]->history_allergy;
                }

                // SOAP_O
                $output[$i]->soap_o_id = null;
                $output[$i]->physical_check = null;
                $output[$i]->soap_o_anythingelse = null;
                $output[$i]->aware = null;
                $output[$i]->eye_score = null;
                $output[$i]->verbal_score = null;
                $output[$i]->motor_score = null;
                $output[$i]->pupil_score = null;
                $output[$i]->verbal_response = null;
                $output[$i]->systolic_blood_pressure = null;
                $output[$i]->diastolic_blood_pressure = null;
                $output[$i]->classification_blood_pressure_type_id = null;
                $output[$i]->verbal_response = null;
                $output[$i]->oxygen_type_id = null;
                $output[$i]->pulse_frequency = null;
                $output[$i]->breath_frequency = null;
                $output[$i]->oximetry_frequency = null;
                $output[$i]->classification_oximetry_type_id = null;
                $output[$i]->temperature = null;
                $output[$i]->classification_temperature_type_id = null;
                $output[$i]->blood_sugar = null;
                $output[$i]->skin_color = null;
                $output[$i]->behavior = null;
                $output[$i]->weight = null;
                $output[$i]->classification_weight_type_id = null;
                $output[$i]->height = null;
                $output[$i]->imt = null;
                $output[$i]->head_circumference = null;
                $output[$i]->chest_circumference = null;
                $output[$i]->arm_circumference = null;
                $output[$i]->stomach_circumference = null;
                $output[$i]->knee_circumference = null;
                $output[$i]->sore_score = null;
                $output[$i]->ews = null;
                $output[$i]->head = null;
                $output[$i]->head_hair_color = null;
                $output[$i]->head_hair_condition = null;
                $output[$i]->eye_conjunctive = null;
                $output[$i]->eye_sclera = null;
                $output[$i]->eye_pupil = null;
                $output[$i]->eye_light_reflex = null;
                $output[$i]->eye_blind_color = null;
                $output[$i]->mouth_mucosa = null;
                $output[$i]->mouth_discoloration = null;
                $output[$i]->mouth_tongue = null;
                $output[$i]->mouth_caries = null;
                $output[$i]->neck_kgb = null;
                $output[$i]->neck_trachea_position = null;
                $output[$i]->neck_massa = null;
                $output[$i]->neck_jvp = null;
                $output[$i]->thorax_insp_chest = null;
                $output[$i]->thorax_insp_retraction = null;
                $output[$i]->thorax_palp_chest = null;
                $output[$i]->thorax_palp_fremitus = null;
                $output[$i]->thorax_palp_crepitation = null;
                $output[$i]->thorax_ausk_pul_vasiculer = null;
                $output[$i]->thorax_ausk_pul_rhonki = null;
                $output[$i]->thorax_ausk_pul_wheezing = null;
                $output[$i]->thorax_ausk_cor_heart_sound = null;
                $output[$i]->thorax_ausk_cor_murmur = null;
                $output[$i]->thorax_ausk_cor_gallop = null;
                $output[$i]->abdomen_supel = null;
                $output[$i]->abdomen_bowel_sounds = null;
                $output[$i]->abdomen_tenderness = null;
                $output[$i]->abdomen_hepar_lien = null;
                $output[$i]->abdomen_other_physical_check = null;
                $output[$i]->extremity_acral = null;
                $output[$i]->extremity_crt = null;
                $output[$i]->extremity_edema = null;
                $output[$i]->ophthal_status = null;
                $output[$i]->neurologist_status = null;
                $output[$i]->ophthal_od = null;
                $output[$i]->ophthal_os = null;
                $output[$i]->uub_type_id = null;
                $output[$i]->eye_anemic_conjunctiva = null;
                $output[$i]->eye_interic_sclera = null;
                $output[$i]->eye_anythingelse = null;
                $output[$i]->tht_hyperemic_pharynx = null;
                $output[$i]->tht_tonsil = null;
                $output[$i]->tht_ear = null;
                $output[$i]->tht_anythingelse = null;
                $output[$i]->neck_lymph_gland = null;
                $output[$i]->thorax_heart_type_id = null;
                $output[$i]->thorax_murmur = null;
                $output[$i]->thorax_gallop = null;
                $output[$i]->thorax_anythingelse = null;
                $output[$i]->pulmo_arry = null;
                $output[$i]->pulmo_anythingelse = null;
                $output[$i]->pulmo_breath_movement = null;
                $output[$i]->pulmo_breath_sound = null;
                $output[$i]->extremity_acral_type_id = null;
                $output[$i]->extremity_capillary_refilling_time = null;
                $output[$i]->extremity_anythingelse = null;
                $output[$i]->abdomen_inspection_type_id = null;
                $output[$i]->abdomen_palpation_type_id = null;
                $output[$i]->abdomen_sore_press = null;
                $output[$i]->abdomen_sore_press_location = null;
                $output[$i]->abdomen_hepar = null;
                $output[$i]->abdomen_bu = null;
                $output[$i]->localist_status = null;
                $output[$i]->abdomen_lien = null;
                $output[$i]->genitalia = null;

                $select_soap_o = 'a.*';
                $where_soap_o = array(
                    'a.soap_id' => $soap_id
                );
                $soap_o = $this->Soap_o->select(0, $select_soap_o, $where_soap_o);
                if(count($soap_o) > 0) {
                    $soap_o_id = $soap_o[0]->id;

                    $output[$i]->soap_o_id = $soap_o_id;
                    $output[$i]->physical_check = $soap_o[0]->physical_check;
                    $output[$i]->soap_o_anythingelse = $soap_o[0]->anythingelse;
                    $output[$i]->aware = $soap_o[0]->aware;
                    $output[$i]->eye_score = $soap_o[0]->eye_score;
                    $output[$i]->verbal_score = $soap_o[0]->verbal_score;
                    $output[$i]->motor_score = $soap_o[0]->motor_score;
                    $output[$i]->pupil_score = $soap_o[0]->pupil_score;
                    $output[$i]->verbal_response = $soap_o[0]->verbal_response;
                    $output[$i]->systolic_blood_pressure = $soap_o[0]->systolic_blood_pressure;
                    $output[$i]->diastolic_blood_pressure = $soap_o[0]->diastolic_blood_pressure;
                    $output[$i]->classification_blood_pressure_type_id = $soap_o[0]->classification_blood_pressure_type_id;
                    $output[$i]->verbal_response = $soap_o[0]->verbal_response;
                    $output[$i]->oxygen_type_id = $soap_o[0]->oxygen_type_id;
                    $output[$i]->pulse_frequency = $soap_o[0]->pulse_frequency;
                    $output[$i]->breath_frequency = $soap_o[0]->breath_frequency;
                    $output[$i]->oximetry_frequency = $soap_o[0]->oximetry_frequency;
                    $output[$i]->classification_oximetry_type_id = $soap_o[0]->classification_oximetry_type_id;
                    $output[$i]->temperature = $soap_o[0]->temperature;
                    $output[$i]->classification_temperature_type_id = $soap_o[0]->classification_temperature_type_id;
                    $output[$i]->blood_sugar = $soap_o[0]->blood_sugar;
                    $output[$i]->skin_color = $soap_o[0]->skin_color;
                    $output[$i]->behavior = $soap_o[0]->behavior;
                    $output[$i]->weight = $soap_o[0]->weight;
                    $output[$i]->classification_weight_type_id = $soap_o[0]->classification_weight_type_id;
                    $output[$i]->height = $soap_o[0]->height;
                    $output[$i]->imt = $soap_o[0]->imt;
                    $output[$i]->head_circumference = $soap_o[0]->head_circumference;
                    $output[$i]->chest_circumference = $soap_o[0]->chest_circumference;
                    $output[$i]->arm_circumference = $soap_o[0]->arm_circumference;
                    $output[$i]->stomach_circumference = $soap_o[0]->stomach_circumference;
                    $output[$i]->knee_circumference = $soap_o[0]->knee_circumference;
                    $output[$i]->sore_score = $soap_o[0]->sore_score;
                    $output[$i]->ews = $soap_o[0]->ews;
                    $output[$i]->head = $soap_o[0]->head;
                    $output[$i]->head_hair_color = $soap_o[0]->head_hair_color;
                    $output[$i]->head_hair_condition = $soap_o[0]->head_hair_condition;
                    $output[$i]->eye_conjunctive = $soap_o[0]->eye_conjunctive;
                    $output[$i]->eye_sclera = $soap_o[0]->eye_sclera;
                    $output[$i]->eye_pupil = $soap_o[0]->eye_pupil;
                    $output[$i]->eye_light_reflex = $soap_o[0]->eye_light_reflex;
                    $output[$i]->eye_blind_color = $soap_o[0]->eye_blind_color;
                    $output[$i]->mouth_mucosa = $soap_o[0]->mouth_mucosa;
                    $output[$i]->mouth_discoloration = $soap_o[0]->mouth_discoloration;
                    $output[$i]->mouth_tongue = $soap_o[0]->mouth_tongue;
                    $output[$i]->mouth_caries = $soap_o[0]->mouth_caries;
                    $output[$i]->neck_kgb = $soap_o[0]->neck_kgb;
                    $output[$i]->neck_trachea_position = $soap_o[0]->neck_trachea_position;
                    $output[$i]->neck_massa = $soap_o[0]->neck_massa;
                    $output[$i]->neck_jvp = $soap_o[0]->neck_jvp;
                    $output[$i]->thorax_insp_chest = $soap_o[0]->thorax_insp_chest;
                    $output[$i]->thorax_insp_retraction = $soap_o[0]->thorax_insp_retraction;
                    $output[$i]->thorax_palp_chest = $soap_o[0]->thorax_palp_chest;
                    $output[$i]->thorax_palp_fremitus = $soap_o[0]->thorax_palp_fremitus;
                    $output[$i]->thorax_palp_crepitation = $soap_o[0]->thorax_palp_crepitation;
                    $output[$i]->thorax_ausk_pul_vasiculer = $soap_o[0]->thorax_ausk_pul_vasiculer;
                    $output[$i]->thorax_ausk_pul_rhonki = $soap_o[0]->thorax_ausk_pul_rhonki;
                    $output[$i]->thorax_ausk_pul_wheezing = $soap_o[0]->thorax_ausk_pul_wheezing;
                    $output[$i]->thorax_ausk_cor_heart_sound = $soap_o[0]->thorax_ausk_cor_heart_sound;
                    $output[$i]->thorax_ausk_cor_murmur = $soap_o[0]->thorax_ausk_cor_murmur;
                    $output[$i]->thorax_ausk_cor_gallop = $soap_o[0]->thorax_ausk_cor_gallop;
                    $output[$i]->abdomen_supel = $soap_o[0]->abdomen_supel;
                    $output[$i]->abdomen_bowel_sounds = $soap_o[0]->abdomen_bowel_sounds;
                    $output[$i]->abdomen_tenderness = $soap_o[0]->abdomen_tenderness;
                    $output[$i]->abdomen_hepar_lien = $soap_o[0]->abdomen_hepar_lien;
                    $output[$i]->abdomen_other_physical_check = $soap_o[0]->abdomen_other_physical_check;
                    $output[$i]->extremity_acral = $soap_o[0]->extremity_acral;
                    $output[$i]->extremity_crt = $soap_o[0]->extremity_crt;
                    $output[$i]->extremity_edema = $soap_o[0]->extremity_edema;
                    $output[$i]->ophthal_status = $soap_o[0]->ophthal_status;
                    $output[$i]->neurologist_status = $soap_o[0]->neurologist_status;

                    $ophthal_od_decode = json_decode($soap_o[0]->ophthal_od);
                    if (is_iterable($ophthal_od_decode)) {
                        foreach ($ophthal_od_decode as $key => $val) {
                            if (is_numeric($val)) {
                                $ophthal_od_decode->$key = (int) $val;
                            }
                        }
                    }

                    $ophthal_os_decode = json_decode($soap_o[0]->ophthal_os);
                    if (is_iterable($ophthal_os_decode)) {
                        foreach ($ophthal_os_decode as $key => $val) {
                            if (is_numeric($val)) {
                                $ophthal_os_decode->$key = (int) $val;
                            }
                        }
                    }

                    $output[$i]->ophthal_od = $ophthal_od_decode;
                    $output[$i]->ophthal_os = $ophthal_os_decode;
                    $output[$i]->uub_type_id = $soap_o[0]->uub_type_id;
                    $output[$i]->eye_anemic_conjunctiva = $soap_o[0]->eye_anemic_conjunctiva;
                    $output[$i]->eye_interic_sclera = $soap_o[0]->eye_interic_sclera;
                    $output[$i]->eye_anythingelse = $soap_o[0]->eye_anythingelse;
                    $output[$i]->tht_hyperemic_pharynx = $soap_o[0]->tht_hyperemic_pharynx;
                    $output[$i]->tht_tonsil = $soap_o[0]->tht_tonsil;
                    $output[$i]->tht_ear = $soap_o[0]->tht_ear;
                    $output[$i]->tht_anythingelse = $soap_o[0]->tht_anythingelse;
                    $output[$i]->neck_lymph_gland = $soap_o[0]->neck_lymph_gland;
                    $output[$i]->thorax_heart_type_id = $soap_o[0]->thorax_heart_type_id;
                    $output[$i]->thorax_murmur = $soap_o[0]->thorax_murmur;
                    $output[$i]->thorax_gallop = $soap_o[0]->thorax_gallop;
                    $output[$i]->thorax_anythingelse = $soap_o[0]->thorax_anythingelse;
                    $output[$i]->pulmo_arry = ($soap_o[0]->pulmo_arry != "" ? explode(',', $soap_o[0]->pulmo_arry) : []);
                    $output[$i]->pulmo_anythingelse = $soap_o[0]->pulmo_anythingelse;
                    $output[$i]->pulmo_breath_movement = $soap_o[0]->pulmo_breath_movement;
                    $output[$i]->pulmo_breath_sound = $soap_o[0]->pulmo_breath_sound;
                    $output[$i]->extremity_acral_type_id = $soap_o[0]->extremity_acral_type_id;
                    $output[$i]->extremity_capillary_refilling_time = $soap_o[0]->extremity_capillary_refilling_time;
                    $output[$i]->extremity_anythingelse = $soap_o[0]->extremity_anythingelse;
                    $output[$i]->abdomen_inspection_type_id = $soap_o[0]->abdomen_inspection_type_id;
                    $output[$i]->abdomen_palpation_type_id = $soap_o[0]->abdomen_palpation_type_id;
                    $output[$i]->abdomen_sore_press = $soap_o[0]->abdomen_sore_press;
                    $output[$i]->abdomen_sore_press_location = $soap_o[0]->abdomen_sore_press_location;
                    $output[$i]->abdomen_hepar = $soap_o[0]->abdomen_hepar;
                    $output[$i]->abdomen_bu = $soap_o[0]->abdomen_bu;
                    $output[$i]->localist_status = $soap_o[0]->localist_status;
                    $output[$i]->abdomen_lien = $soap_o[0]->abdomen_lien;
                    $output[$i]->genitalia = $soap_o[0]->genitalia;

                    // SOAP_O_ACTION
                    $output[$i]->soap_o_action_id = null;
                    $output[$i]->canvasing_type_id = null;
                    $output[$i]->canvasing_text = null;
                    $output[$i]->attr1 = null;
                    $output[$i]->attr2 = null;

                    $select_soap_o_action = 'a.*';
                    $where_soap_o_action = array(
                        'a.soap_o_id' => $soap_o_id
                    );
                    $soap_o_action = $this->Soap_o_action->select(0, $select_soap_o_action, $where_soap_o_action);
                    if(count($soap_o_action) > 0) {
                        $output[$i]->soap_o_action_id = $soap_o_action[0]->id;
                        $output[$i]->canvasing_type_id = $soap_o_action[0]->canvasing_type_id;
                        $output[$i]->canvasing_text = $soap_o_action[0]->canvasing_text;
                        $output[$i]->attr1 = $soap_o_action[0]->attr1;
                        $output[$i]->attr2 = $soap_o_action[0]->attr2;
                    }
                }

                // SOAP_A
                $output[$i]->soap_a_id = null;
                $output[$i]->development_notes = null;
                $output[$i]->soap_a_anythingelse = null;
                $output[$i]->icd10_notes = null;
                $output[$i]->non_icd10 = null;
                $output[$i]->icd10 = [];

                $soap_a = $this->db->query("
                    SELECT a.*
                    FROM soap_a as a
                    WHERE soap_id = '$soap_id'
                    AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                    ORDER BY a.id ASC
                ")->result();
                if(count($soap_a) > 0) {
                    $soap_a_id = $soap_a[0]->id;

                    $output[$i]->soap_a_id = $soap_a_id;
                    $output[$i]->development_notes = $soap_a[0]->development_notes;
                    $output[$i]->soap_a_anythingelse = $soap_a[0]->anythingelse;
                    if ($soap_a[0]->obgyn_usg_file) {
                        $soap_a[0]->obgyn_usg_file = base_url().$soap_a[0]->obgyn_usg_file;
                    }
                    $soap_a[0]->non_icd10 = ($soap_a[0]->non_icd10 == null) ? array() : explode('|', $soap_a[0]->non_icd10);
                    $output[$i]->icd10_notes = $soap_a[0]->icd10_notes;
                    $output[$i]->non_icd10 = $soap_a[0]->non_icd10;

                    // SOAP_A_ACTION
                    $select_soap_a_action = 'a.id, a.icd10_id, a.date_added, a.index, i.code as icd10_code, i.code_s as icd10_code_s, i.name as icd10_name';
                    $where_soap_a_action = array(
                        'a.soap_a_id' => $soap_a_id,
                        'a.is_delete' => 0,
                    );
                    $join_soap_a_action = array(
                        array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
                    );
                    $order_soap_a_action = array(
                        'a.index' => 'ASC',
                    );
                    $soap_a_action = $this->Soap_a_action->select(0, $select_soap_a_action, $where_soap_a_action, $join_soap_a_action, $order_soap_a_action);
                    if(count($soap_a_action) > 0) {
                        $output[$i]->icd10 = $soap_a_action;
                    }
                }

                $output[$i]->soap_a = $soap_a;

                // SOAP_P
                $output[$i]->icd9cm = [];
                $output[$i]->prescription = [];

                $select_soap_p = 'a.*';
                $where_soap_p = array(
                    'a.soap_id' => $soap_id
                );
                $soap_p = $this->Soap_p->select(0, $select_soap_p, $where_soap_p);
                if(count($soap_p) > 0) {
                    $idx_prescription = 0;

                    for ($spx=0; $spx < count($soap_p); $spx++) { 
                        // PRESCRIPTION
                        $soap_p_id = $soap_p[$spx]->id;

                        $prescription = $this->db->query("
                            SELECT p.id,
                            p.no_order,
                            p.queue,
                            p.type_prescription_id,
                            h.name AS flag_prescription,
                            p.pharmacy_depo_id,
                            p.soap_p_id,
                            p.doctor_in_charge_id,
                            e.fullname AS doctor_in_charge_name,
                            p.health_facility_item_id,
                            p.flag,
                            p.is_retur,
                            p.date_added
                            FROM pharmacy_prescription p
                            LEFT JOIN employee e ON p.doctor_in_charge_id = e.id
                            LEFT JOIN helper h ON p.type_prescription_id = h.code AND h.variable = 'type_prescription'
                            WHERE p.soap_p_id = '$soap_p_id'
                            AND p.is_delete = 0
                            AND (p.cppt_id IS NULL OR p.cppt_id = '') 
                            ORDER BY p.date_added DESC
                        ")->result();
                        for ($k=0; $k < count($prescription); $k++) { 
                            $prescription_id = $prescription[$k]->id;

                            $output[$i]->prescription[$idx_prescription]['no_order'] = $prescription[$k]->no_order;
                            $output[$i]->prescription[$idx_prescription]['queue'] = $prescription[$k]->queue;
                            $output[$i]->prescription[$idx_prescription]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                            $output[$i]->prescription[$idx_prescription]['type_prescription_name'] = $prescription[$k]->flag_prescription;
                            $output[$i]->prescription[$idx_prescription]['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                            $output[$i]->prescription[$idx_prescription]['soap_p_id'] = $prescription[$k]->soap_p_id;
                            $output[$i]->prescription[$idx_prescription]['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                            $output[$i]->prescription[$idx_prescription]['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                            $output[$i]->prescription[$idx_prescription]['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                            $output[$i]->prescription[$idx_prescription]['flag'] = $prescription[$k]->flag;
                            $output[$i]->prescription[$idx_prescription]['is_retur'] = $prescription[$k]->is_retur;
                            $output[$i]->prescription[$idx_prescription]['date_added'] = $prescription[$k]->date_added;
                            
                            $form_prescription_parent = [];

                            $form_prescription = $this->db->query("
                                SELECT
                                pi.pharmacy_prescription_id,
                                pi.pharmacy_catalog_id,
                                pc.name,
                                pc.code,
                                pi.prescription_item_type_id,
                                pi.prescription_item_type_used,
                                pi.prescription_item_type_value,
                                pi.prescription_item_type_package,
                                pi.package_id,
                                pi.package_name,
                                pi.signatura_times_id,
                                pi.administer_id,
                                pi.dose,
                                pi.dose_unit_id,
                                pi.dose_doctor,
                                pi.batch,
                                pi.expired,
                                pi.qty_doctor,
                                pi.qty,
                                pi.qty_copy,
                                pi.used,
                                pi.desc,
                                pi.sort
                                FROM pharmacy_prescription_item pi
                                LEFT JOIN pharmacy_catalog pc ON pi.pharmacy_catalog_id = pc.id
                                WHERE pi.is_delete = 0
                                AND pi.pharmacy_prescription_id = '$prescription_id'
                                ORDER BY pi.sort ASC
                            ")->result();
                            for ($j=0; $j < count($form_prescription); $j++) { 
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['package_id'] = $form_prescription[$j]->package_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['package_name'] = $form_prescription[$j]->package_name;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['signatura_times_id'] = $form_prescription[$j]->signatura_times_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['administer_id'] = $form_prescription[$j]->administer_id;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['dose'] = $form_prescription[$j]->dose;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['dose_unit_id'] = $form_prescription[$j]->dose_unit_id;
                                // $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['batch'] = $form_prescription[$j]->batch;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['expired'] = $form_prescription[$j]->expired;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['qty'] = $form_prescription[$j]->qty;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['used'] = $form_prescription[$j]->used;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['desc'] = $form_prescription[$j]->desc;
                                $output[$i]->prescription[$idx_prescription]['form_prescription'][$j]['sort'] = $form_prescription[$j]->sort;
                            
                                //------------------- detail form_prescription_parent -//
                                $parent_id = $form_prescription[$j]->prescription_item_type_id; 
                                $parent_used = $form_prescription[$j]->prescription_item_type_used; 
                                $parent_value = $form_prescription[$j]->prescription_item_type_value; 
                                $parent_package = $form_prescription[$j]->prescription_item_type_package; 
                                $parent_signatura_times_id = $form_prescription[$j]->signatura_times_id; 
                                $parent_administer_id = $form_prescription[$j]->administer_id; 

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
                                for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                                    if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                                        $skip = 1;
                                    }
                                }

                                if ($skip == 0) {
                                    array_push($form_prescription_parent, $dt_form_prescription_parent);
                                }
                                //----------------------------------------------------//
                            }
                            
                            $output[$i]->prescription[$idx_prescription]['form_prescription_parent'] = $form_prescription_parent;
                            
                            $output[$i]->prescription = array_values($output[$i]->prescription);

                            $idx_prescription++;
                        }

                        // SOAP_P_ACTION
                        $select_soap_p_action = 'a.id, a.icd9cm_id, a.date_added, i.code as icd9cm_code, i.code_s as icd9cm_code_s, i.name as icd9cm_name';
                        $where_soap_p_action = array(
                            'a.soap_p_id' => $soap_p_id,
                            'a.is_delete' => 0,
                        );
                        $join_soap_p_action = array(
                            array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
                        );
                        $soap_p_action = $this->Soap_p_action->select(0, $select_soap_p_action, $where_soap_p_action, $join_soap_p_action);
                        if(count($soap_p_action) > 0) {
                            $output[$i]->icd9cm = $soap_p_action;
                        }
                    }

                    $output[$i]->soap_p_id = $soap_p[0]->id;
                    $output[$i]->therapy_plan = $soap_p[0]->therapy_plan;
                    $output[$i]->estimated_duration_treatment = $soap_p[0]->estimated_duration_treatment;
                    $output[$i]->follow_up_care_arry = ($soap_p[0]->follow_up_care_arry != '') ? explode(',', $soap_p[0]->follow_up_care_arry): [];
                    $output[$i]->diet_and_nutrition_type_id = $soap_p[0]->diet_and_nutrition_type_id;
                    $output[$i]->diet_and_nutrition = $soap_p[0]->diet_and_nutrition;
                    $output[$i]->wound_care_type_id = $soap_p[0]->wound_care_type_id;
                    $output[$i]->wound_care = $soap_p[0]->wound_care;
                    $output[$i]->medical_rehabilitation_type_id = $soap_p[0]->medical_rehabilitation_type_id;
                    $output[$i]->medical_rehabilitation = $soap_p[0]->medical_rehabilitation;
                    $output[$i]->pain_management_type_id = $soap_p[0]->pain_management_type_id;
                    $output[$i]->pain_management = $soap_p[0]->pain_management;
                    $output[$i]->discharge_planning_other = $soap_p[0]->discharge_planning_other;
                }

                // KARDEX
                $select_kardex = 'a.*, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
                $where_kardex = array(
                    'a.soap_id' => $soap_id,
                    'a.is_delete' => 0,
                );
                $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
                $this->db->where($where_kar);
                $join_kardex = array(
                    array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                    array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                    array('user u', 'a.user_added = u.id', 'INNER'),
                    array('employee e2', 'u.employee_id = e2.id', 'INNER'),
                );
                $order_kardex = array(
                    'a.date_added' => 'DESC',
                );
                $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);

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
                    }
                    //----------------------------------------------------//

                    $kardex[$k]->form_prescription_parent = $kardex_parent;
                    
                    if (count($kardex_item) > 0) {
                        array_push($clean_kardex, $kardex[$k]);
                    }
                }

                $output[$i]->prescription = array_merge($output[$i]->prescription, $clean_kardex);

                // ALLERGIES
                $select_allergies = 'a.name';
                $where_allergies = array(
                    'a.norm' => $norm, 
                    'a.is_delete' => 0
                );
                $join_allergies = array(
                    array('helper h', 'a.type_allergy_id = h.code AND h.variable = "type_allergy"', 'LEFT'),
                );
                $allergies = $this->Patient_allergy->select(0, $select_allergies, $where_allergies, $join_allergies);
                
                $output[$i]->allergies = $allergies;
                
                // TRANSACTION_DOCTOR
                $select_doctor = 'a.id,
                a.costing_id,
                a.costing_name,
                a.var_1,
                a.var_2,
                a.var_3,
                a.doctor_ref_id,
                a.notes,
                a.date_added
                ';
                $where_doctor = array(
                    'a.soap_id' => $soap_id, 
                    'a.is_delete' => 0
                );
                $transaction_doctor = $this->Transaction_doctor->select(0, $select_doctor, $where_doctor);
                
                $output[$i]->transaction_doctor = $transaction_doctor;

                // TRANSACTION_PHYSIOTHERAPY
                // $select_physiotherapy = 'a.id,
                // a.costing_id,
                // a.costing_name,
                // a.var_1,
                // a.var_2,
                // a.var_3,
                // a.service_class_id,
                // a.notes,
                // a.date_added
                // ';
                // $where_physiotherapy = array(
                //     'a.soap_id' => $soap_id, 
                //     'a.is_delete' => 0
                // );
                // $transaction_physiotherapy = $this->Transaction_physiotherapy->select(0, $select_physiotherapy, $where_physiotherapy);

                // $output[$i]->transaction_physiotherapy = $transaction_physiotherapy;

                // LABORATORY_REQUEST
                $output[$i]->laboratory_request = [];
                $laboratory_request = $this->db->query("
                    SELECT a.*, e.fullname as doctor_in_charge_name
                    FROM laboratory_request as a
                    INNER JOIN employee e ON a.doctor_in_charge_id = e.id
                    WHERE soap_id = '$soap_id'
                    AND a.is_delete = 0
                    AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                    ORDER BY a.date_request DESC
                ")->result();
                for ($k=0; $k < count($laboratory_request); $k++) { 
                    $laboratory_request_id = $laboratory_request[$k]->id;

                    // LIS RESULT
                    $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                    $output[$i]->laboratory_request[$k]['id'] = $laboratory_request[$k]->id;
                    $output[$i]->laboratory_request[$k]['institute_branch_id'] = $laboratory_request[$k]->institute_branch_id;
                    $output[$i]->laboratory_request[$k]['no_request'] = $laboratory_request[$k]->no_request;
                    $output[$i]->laboratory_request[$k]['status'] = $laboratory_request[$k]->status;
                    $output[$i]->laboratory_request[$k]['soap_id'] = $laboratory_request[$k]->soap_id;
                    $output[$i]->laboratory_request[$k]['doctor_in_charge_id'] = $laboratory_request[$k]->doctor_in_charge_id;
                    $output[$i]->laboratory_request[$k]['doctor_in_charge_name'] = $laboratory_request[$k]->doctor_in_charge_name;
                    $output[$i]->laboratory_request[$k]['clinical_indications'] = $laboratory_request[$k]->clinical_indications;
                    $output[$i]->laboratory_request[$k]['date_request'] = $laboratory_request[$k]->date_request;
                    $output[$i]->laboratory_request[$k]['queue'] = $laboratory_request[$k]->queue;
                    $output[$i]->laboratory_request[$k]['is_delete'] = $laboratory_request[$k]->is_delete;

                    // LABORATORY_REQUEST_ITEM
                    $select_lab_item = 'a.*, lp.code_lis';
                    $where_lab_item = array(
                        'a.laboratory_request_id' => $laboratory_request_id, 
                        'a.is_delete' => 0
                    );
                    $join_lab_item = array(
                        array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
                    );
                    $laboratory_request_item = $this->Laboratory_request_item->select(0, $select_lab_item, $where_lab_item, $join_lab_item);
                    for ($j=0; $j < count($laboratory_request_item); $j++) { 
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['id'] = $laboratory_request_item[$j]->id;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['laboratory_request_id'] = $laboratory_request_item[$j]->laboratory_request_id;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['laboratory_package_id'] = $laboratory_request_item[$j]->laboratory_package_id;                        
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['costing_id'] = $laboratory_request_item[$j]->costing_id;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['costing_name'] = $laboratory_request_item[$j]->costing_name;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['var_1'] = $laboratory_request_item[$j]->var_1;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['var_2'] = $laboratory_request_item[$j]->var_2;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['var_3'] = $laboratory_request_item[$j]->var_3;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['service_class_id'] = $laboratory_request_item[$j]->service_class_id;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['notes'] = $laboratory_request_item[$j]->notes;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['status'] = $laboratory_request_item[$j]->status;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['url'] = $laboratory_request_item[$j]->url;
                        $output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['result'] = [];

                        // LIS RESULT
                        $code_lis = $laboratory_request_item[$j]->code_lis;

                        if (count($lis_response) > 0) {
                            $result = $lis_response[0]['result_detail'];
                            
                            for ($l=0; $l < count($result); $l++) { 
                                $flag = $result[$l]['flag'];
                                $parameter_id = $result[$l]['ParameterID'];
                                
                                // keluarin semua hasil lab
                                // $flag != '' && 
                                if ($parameter_id == $code_lis) {
                                    array_push($output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['result'], $result[$l]);
                                }
                            }
                        }
                        if ($output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['url']) {
                            array_push($output[$i]->laboratory_request[$k]['laboratory_request_items'][$j]['result'], array(
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
                }

                // RADIOLOGY_REQUEST
                $output[$i]->radiology_request = [];
                $radiology_request = $this->db->query("
                    SELECT a.*, e.fullname as doctor_in_charge_name
                    FROM radiology_request as a
                    INNER JOIN employee e ON a.doctor_in_charge_id = e.id
                    WHERE soap_id = '$soap_id'
                    AND a.is_delete = 0
                    AND a.is_cancel = 0
                    AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                    ORDER BY a.date_request DESC
                ")->result();
                for ($k=0; $k < count($radiology_request); $k++) { 
                    $radiology_request_id = $radiology_request[$k]->id;

                    $output[$i]->radiology_request[$k]['id'] = $radiology_request_id;
                    $output[$i]->radiology_request[$k]['institute_branch_id'] = $radiology_request[$k]->institute_branch_id;
                    $output[$i]->radiology_request[$k]['no_request'] = $radiology_request[$k]->no_request;
                    $output[$i]->radiology_request[$k]['status'] = $radiology_request[$k]->status;
                    $output[$i]->radiology_request[$k]['soap_id'] = $radiology_request[$k]->soap_id;
                    $output[$i]->radiology_request[$k]['doctor_in_charge_id'] = $radiology_request[$k]->doctor_in_charge_id;
                    $output[$i]->radiology_request[$k]['doctor_in_charge_name'] = $radiology_request[$k]->doctor_in_charge_name;
                    $output[$i]->radiology_request[$k]['clinical_indications'] = $radiology_request[$k]->clinical_indications;
                    $output[$i]->radiology_request[$k]['date_request'] = $radiology_request[$k]->date_request;
                    $output[$i]->radiology_request[$k]['queue'] = $radiology_request[$k]->queue;
                    $output[$i]->radiology_request[$k]['is_delete'] = $radiology_request[$k]->is_delete;
                    
                    // RADIOLOGY_REQUEST_ITEM
                    $output[$i]->physiotherapy_request = [];
                    $select_rad_item = 'a.*';
                    $where_rad_item = array(
                        'a.radiology_request_id' => $radiology_request_id, 
                        'a.is_delete' => 0
                    );
                    $radiology_request_item = $this->Radiology_request_item->select(0, $select_rad_item, $where_rad_item);
                    for ($j=0; $j < count($radiology_request_item); $j++) { 
                        $radiology_request_item_id = $radiology_request_item[$j]->id;

                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['id'] = $radiology_request_item[$j]->id;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['radiology_request_id'] = $radiology_request_item[$j]->radiology_request_id;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['radiology_package_id'] = $radiology_request_item[$j]->radiology_package_id;                        
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['costing_id'] = $radiology_request_item[$j]->costing_id;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['costing_name'] = $radiology_request_item[$j]->costing_name;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['var_1'] = $radiology_request_item[$j]->var_1;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['var_2'] = $radiology_request_item[$j]->var_2;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['var_3'] = $radiology_request_item[$j]->var_3;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['service_class_id'] = $radiology_request_item[$j]->service_class_id;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['notes'] = $radiology_request_item[$j]->notes;
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['status'] = $radiology_request_item[$j]->status;
                        
                        $select_rad_image = 'a.*';
                        $where_rad_image = array(
                            'a.radiology_request_item_id' => $radiology_request_item_id, 
                            'a.is_delete' => 0
                        );
                        $radiology_request_item_image = $this->Radiology_request_item_image->select(0, $select_rad_image, $where_rad_image);
                        
                        $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['url'] = $radiology_request_item_image;
                        
                        for ($kk=0; $kk < count($radiology_request_item_image); $kk++) { 
                            $url_raw = $radiology_request_item_image[$kk]->url;
                            $name = substr($url_raw, 20);
                            
                            $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['url'][$kk]->radiology_request_id = $radiology_request_item[$j]->radiology_request_id;
                            $output[$i]->radiology_request[$k]['radiology_request_items'][$j]['url'][$kk]->name = $name;
                        }
                    }
                }

                // PHYSIOTHERAPY_REQUEST
                $select_physiotherapy = 'a.*';
                $where_physiotherapy = array(
                    'a.soap_id' => $soap_id, 
                    'a.is_delete' => 0
                );
                $physiotherapy_request = $this->Physiotherapy_request->select(0, $select_physiotherapy, $where_physiotherapy);
                for ($k=0; $k < count($physiotherapy_request); $k++) { 
                    $physiotherapy_request_id = $physiotherapy_request[$k]->id;

                    $output[$i]->physiotherapy_request[$k]['id'] = $physiotherapy_request[$k]->id;
                    $output[$i]->physiotherapy_request[$k]['institute_branch_id'] = $physiotherapy_request[$k]->institute_branch_id;
                    $output[$i]->physiotherapy_request[$k]['no_request'] = $physiotherapy_request[$k]->no_request;
                    $output[$i]->physiotherapy_request[$k]['soap_id'] = $physiotherapy_request[$k]->soap_id;
                    $output[$i]->physiotherapy_request[$k]['date_request'] = $physiotherapy_request[$k]->date_request;
                    $output[$i]->physiotherapy_request[$k]['queue'] = $physiotherapy_request[$k]->queue;
                    $output[$i]->physiotherapy_request[$k]['is_delete'] = $physiotherapy_request[$k]->is_delete;

                    // PHYSIOTHERAPY_REQUEST_ITEM
                    $select_phy_item = 'a.*';
                    $where_phy_item = array(
                        'a.physiotherapy_request_id' => $physiotherapy_request_id, 
                        'a.is_delete' => 0
                    );
                    $physiotherapy_request_item = $this->Physiotherapy_request_item->select(0, $select_phy_item, $where_phy_item);
                    for ($j=0; $j < count($physiotherapy_request_item); $j++) { 
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['id'] = $physiotherapy_request_item[$j]->id;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['physiotherapy_request_id'] = $physiotherapy_request_item[$j]->physiotherapy_request_id;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['physiotherapy_package_id'] = $physiotherapy_request_item[$j]->physiotherapy_package_id;                        
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['costing_id'] = $physiotherapy_request_item[$j]->costing_id;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['costing_name'] = $physiotherapy_request_item[$j]->costing_name;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['var_1'] = $physiotherapy_request_item[$j]->var_1;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['var_2'] = $physiotherapy_request_item[$j]->var_2;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['var_3'] = $physiotherapy_request_item[$j]->var_3;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['service_class_id'] = $physiotherapy_request_item[$j]->service_class_id;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['notes'] = $physiotherapy_request_item[$j]->notes;
                        $output[$i]->physiotherapy_request[$k]['physiotherapy_request_items'][$j]['status'] = $physiotherapy_request_item[$j]->status;
                    }
                }

                // MEDICAL_REHABILITATION
                $is_medical_rehabilitation = $output[$i]->is_medical_rehabilitation;
                if ($is_medical_rehabilitation == 1) {
                    $output[$i]->service_date = null;
                    $output[$i]->anamnesa = null;
                    $output[$i]->functional_testing = null;
                    $output[$i]->functional_diagnosis = null;
                    $output[$i]->medical_diagnosis = null;
                    $output[$i]->support_examination = null;
                    $output[$i]->advice_rehab = null;
                    $output[$i]->evaluation = null;
                    $output[$i]->suspected_disease_type_id = null;
                    $output[$i]->suspected_disease = null;
                    $output[$i]->result = null;
                    $output[$i]->conclusion = null;
                    $output[$i]->recommendation = null;

                    $select = 'a.*';
                    $where_mr = array(
                        'soap_id' => $soap_id
                    );
                    $medical_rehabilitation = $this->Medical_rehabilitation->select(0, $select, $where_mr);
                    if (count($medical_rehabilitation) > 0) {
                        $output[$i]->service_date = $medical_rehabilitation[0]->service_date;
                        $output[$i]->anamnesa = $medical_rehabilitation[0]->anamnesa;
                        $output[$i]->functional_testing = $medical_rehabilitation[0]->functional_testing;
                        $output[$i]->functional_diagnosis = $medical_rehabilitation[0]->functional_diagnosis;
                        $output[$i]->medical_diagnosis = $medical_rehabilitation[0]->medical_diagnosis;
                        $output[$i]->support_examination = $medical_rehabilitation[0]->support_examination;
                        $output[$i]->advice_rehab = $medical_rehabilitation[0]->advice;
                        $output[$i]->evaluation = $medical_rehabilitation[0]->evaluation;
                        $output[$i]->suspected_disease_type_id = $medical_rehabilitation[0]->suspected_disease_type_id;
                        $output[$i]->suspected_disease = $medical_rehabilitation[0]->suspected_disease;
                        $output[$i]->result = $medical_rehabilitation[0]->result;
                        $output[$i]->conclusion = $medical_rehabilitation[0]->conclusion;
                        $output[$i]->recommendation = $medical_rehabilitation[0]->recommendation;
                    }
                }

                $is_hemodialysis = $output[$i]->is_hemodialysis;
                if ($is_hemodialysis == 1) {
                    $output[$i]->main_diagnosis = null;
                    $output[$i]->etiology = null;
                    $output[$i]->comorbidities = null;
                    $output[$i]->prescription_hd = null;
                    $output[$i]->frequency_hd = null;
                    $output[$i]->vascular_access = null;

                    $select = 'a.*';
                    $where_hd = array(
                        'soap_id' => $soap_id,
                    );
                    $hemodialysis_initiation = $this->Hemodialysis_initiation->select(0, $select, $where_hd);
                    if (count($hemodialysis_initiation) > 0 ) {
                        $output[$i]->main_diagnosis = $hemodialysis_initiation[0]->main_diagnosis;
                        $output[$i]->etiology = $hemodialysis_initiation[0]->etiology;
                        $output[$i]->comorbidities = $hemodialysis_initiation[0]->comorbidities;
                        $output[$i]->prescription_hd = $hemodialysis_initiation[0]->prescription_hd;
                        $output[$i]->frequency_hd = $hemodialysis_initiation[0]->frequency_hd;
                        $output[$i]->vascular_access = $hemodialysis_initiation[0]->vascular_access;
                    }
                }

                // MEDICAL_CHECK_UP
                $is_medical_check_up = $output[$i]->is_medical_check_up;
                if ($is_medical_check_up == 1) {
                    $output[$i]->fit_to_work = null;
                    $output[$i]->consultation = null;

                    $select = 'a.*';
                    $where_mr = array(
                        'soap_id' => $soap_id
                    );
                    $medical_check_up = $this->Medical_check_up->select(0, $select, $where_mr);
                    if (count($medical_check_up) > 0) {
                        $output[$i]->fit_to_work = $medical_check_up[0]->fit_to_work;
                        $output[$i]->consultation = $medical_check_up[0]->consultation;
                    }
                }

                if ($is_initial_inpatient == 1) {
                    // CPPT
                    $output[$i]->cppt = $cppt;
    
                    // SBAR
                    $output[$i]->sbar = $sbar;
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
    
        $limit = $q_strings['limit'] ?? 4; // FIXME - HARDCODE LIMIT SEMENTARA
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        unset($q_strings['limit']);
        
        $this->$model->offset = $offset; 
        unset($q_strings['offset']);
    
        $where_main = $q_strings;

        $select = 'a.id,
            a.registration_destination_id,
            ss.anamesa_type_id,
            ss.family_name,
            ss.family_relation_id,
            ss.main_complaint,
            ss.history_disease_now,
            ss.history_disease_past,
            ss.history_disease_family,
            ss.history_habitude,
            ss.history_drugs,
            so.verbal_response,
            so.oxygen_type_id,
            so.aware,
            so.eye_score,
            so.verbal_score,
            so.motor_score,
            so.pupil_score,
            so.systolic_blood_pressure, 
            so.diastolic_blood_pressure, 
            so.pulse_frequency,
            so.breath_frequency,
            so.oximetry_frequency,
            so.temperature,
            so.blood_sugar,
            so.skin_color,
            so.behavior,
            so.sore_score,
            so.ews,
            so.weight,
            so.height,
            so.imt,
            so.head_circumference,
            so.chest_circumference,
            so.arm_circumference,
            so.stomach_circumference,
            so.knee_circumference,
            so.physical_check,
            so.physical_check as localist_status,
            so.anythingelse as soap_o_anythingelse,
            a.advice,
            hf.id as health_facility_id,
            hf.name as health_facility_name,
            hfi.id as health_facility_item_id,
            hfi.name as health_facility_item_name,
            a.doctor_in_charge_id,
            e.fullname as doctor_in_charge_name,
            a.date_added
        ';
        $where_main['a.is_delete'] = 0;
        $where_main['r.norm'] = $norm;
        unset($where_main['norm']);
        $join_main = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('soap_s ss', 'a.id = ss.soap_id', 'INNER'), // INNER untuk mencegah cppt masuk ke history dokter
            array('soap_o so', 'a.id = so.soap_id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        $order = array('a.date_added' => 'DESC');
        $output = $this->$model->select(0, $select, $where_main, $join_main, $order);

        for ($i=0; $i < count($output); $i++) { 
            $soap_id = $output[$i]->id;
            $registration_destination_id = $output[$i]->registration_destination_id;

            $soap_a = $this->db->query("
                SELECT a.id, a.non_icd10, a.icd10_notes, a.anythingelse
                FROM soap_a as a
                WHERE soap_id = '$soap_id'
                AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                ORDER BY a.id ASC
            ")->result();

            $output[$i]->soap_a_id = "";
            $output[$i]->non_icd10 = [];
            $output[$i]->icd10_notes = "";
            $output[$i]->anythingelse = "";
            $output[$i]->icd10 = [];
            
            if (count($soap_a) > 0) {
                $soap_a_id = $soap_a[0]->id;
                $non_icd10 = ($soap_a[0]->non_icd10 == null) ? array() : explode('|', $soap_a[0]->non_icd10);
                $icd10_notes = $soap_a[0]->icd10_notes;
                $anythingelse = $soap_a[0]->anythingelse;
    
                $output[$i]->soap_a_id = $soap_a_id;
                $output[$i]->non_icd10 = $non_icd10;
                $output[$i]->icd10_notes = $icd10_notes;
                $output[$i]->anythingelse = $anythingelse;
    
                $select = 'a.id, i.id as icd10_id, i.code, i.code_s, i.name, a.index';
                $where = array(
                    'a.soap_a_id' => $soap_a_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
                );
                $order = array('a.index' => 'ASC');
                $icd10 = $this->Soap_a_action->select(0, $select, $where, $join, $order);
    
                $output[$i]->icd10 = $icd10;
            }
            $output[$i]->soap_p = [];
            $output[$i]->prescription = [];
            
            // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
            $select = 'a.id, a.therapy_plan';
            $where = array('soap_id' => $soap_id);
            $soap_p = $this->Soap_p->select(0, $select, $where);
            for ($j=0; $j < count($soap_p); $j++) { 
                $soap_p_id = $soap_p[$j]->id;
                $therapy_plan = $soap_p[$j]->therapy_plan;

                $output[$i]->soap_p[$j]['therapy_plan'] = $therapy_plan;
                $output[$i]->soap_p[$j]['icd9cm'] = [];

                $select = 'a.id, i.id as icd9cm_id, i.code, i.code_s, i.name';
                $where = array(
                    'a.soap_p_id' => $soap_p_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
                );
                $icd9cm = $this->Soap_p_action->select(0, $select, $where, $join);

                $output[$i]->soap_p[$j]['icd9cm'] = array_merge($output[$i]->soap_p[$j]['icd9cm'], $icd9cm);

                // NOTE - comment karena resep dari cppt tidak masuk pada banner dokter
                // TODO - SELECT RESEP LAG, TABLE KURANG INDEXING
                // $select = 'a.id, a.no_order';
                // $where = array(
                //     'a.soap_p_id' => $soap_p_id,
                //     'a.is_delete' => 0,
                // );
                // $join = array(
                //     array('soap_p sp', 'a.soap_p_id = sp.id', 'INNER'),
                // );
                // $prescription = $this->Prescription->select(0, $select, $where, $join);

                $prescription = $this->db->query("
                    SELECT a.id, a.no_order
                    FROM pharmacy_prescription as a
                    WHERE a.soap_p_id = '$soap_p_id'
                    AND a.is_delete = 0
                    AND (a.cppt_id IS NULL OR a.cppt_id = '')
                    ORDER BY a.date_added DESC
                ")->result();

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
                        a.status_kardex, 
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

                    for ($l=0; $l < count($prescription_item); $l++) { 
                        $package_id = $prescription_item[$l]->package_id;
                        $prescription_item[$l]->dose_unit_options = options_unit_package($package_id);
                    }

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
                        $parent_desc = $prescription_item[$l]->desc; 
                        $parent_status_kardex = $prescription_item[$l]->status_kardex; 

                        $dt_form_prescription_parent = array(
                            'id' => $parent_id,
                            'used' => $parent_used,
                            'value' => $parent_value,
                            'package' => $parent_package,
                            'signatura_times_id' => $parent_signatura_times_id,
                            'administer_id' => $parent_administer_id,
                            'desc' => $parent_desc,
                            'status_kardex' => $parent_status_kardex,
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
            }
            
            // KARDEX
            $select_kardex = 'a.*, e.fullname as doctor_in_charge_name, hfi.name as health_facility_item_name, e2.fullname as user_added_name';
            $where_kardex = array(
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $where_kar = 'a.pharmacy_prescription_id IS NOT NULL';
            $this->db->where($where_kar);
            $join_kardex = array(
                array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                array('user u', 'a.user_added = u.id', 'INNER'),
                array('employee e2', 'u.employee_id = e2.id', 'INNER'),
            );
            $order_kardex = array(
                'a.date_added' => 'DESC',
            );
            $kardex = $this->Kardex->select(0, $select_kardex, $where_kardex, $join_kardex, $order_kardex);

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

                for ($l=0; $l < count($kardex_item); $l++) { 
                    $package_id = $kardex_item[$l]->package_id;
                    $kardex_item[$l]->dose_unit_options = options_unit_package($package_id);
                }

                $kardex[$k]->form_prescription = $kardex_item;

                $kardex_parent = [];

                //------------------- detail dt_kardex_parent -//
                for ($l=0; $l < count($kardex_item); $l++) { 
                    $parent_id = $kardex_item[$l]->prescription_item_type_id; 
                    $parent_used = $kardex_item[$l]->prescription_item_type_used; 
                    $parent_value = $kardex_item[$l]->prescription_item_type_value; 
                    $parent_package = $kardex_item[$l]->prescription_item_type_package; 
                    $parent_signatura_times_id = $kardex_item[$l]->signatura_times_id; 
                    $parent_administer_id = $kardex_item[$l]->administer_id; 
                    $parent_desc = $kardex_item[$l]->desc; 
                    $parent_status_kardex = $kardex_item[$l]->status; 

                    $dt_kardex_parent = array(
                        'id' => $parent_id,
                        'used' => $parent_used,
                        'value' => $parent_value,
                        'package' => $parent_package,
                        'signatura_times_id' => $parent_signatura_times_id,
                        'administer_id' => $parent_administer_id,
                        'desc' => $parent_desc,
                        'status_kardex' => $parent_status_kardex,
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

            $output[$i]->prescription = array_merge($output[$i]->prescription, $clean_kardex);

            $select = 'a.id, a.costing_id, a.costing_name';
            $where = array(
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0,
            );
            $transaction_doctor = $this->Transaction_doctor->select(0, $select, $where);

            $output[$i]->transaction_doctor = $transaction_doctor;

            // NOTE - comment karena laboratory dari cppt tidak masuk pada banner dokter
            // $select = 'a.id, a.no_request';
            // $where = array(
            //     'a.soap_id' => $soap_id,
            //     'a.is_delete' => 0,

            // );
            // $join = array(
            //     array('soap s', 'a.soap_id = s.id', 'INNER'),
            // );
            // $laboratory_request = $this->Laboratory_request->select(0, $select, $where);

            $laboratory_request = $this->db->query("
                SELECT a.id, a.no_request
                FROM laboratory_request as a
                WHERE soap_id = '$soap_id'
                AND a.is_delete = 0
                AND (a.cppt_id IS NULL OR a.cppt_id = '')
                ORDER BY a.date_request DESC
            ")->result();

            $output[$i]->laboratory_request = $laboratory_request;

            for ($j=0; $j < count($laboratory_request); $j++) { 
                $laboratory_request_id = $laboratory_request[$j]->id;

                $select = 'a.id, a.laboratory_request_id, a.laboratory_package_id, a.url, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis';
                $where = array(
                    'a.laboratory_request_id' => $laboratory_request_id,
                    'a.is_delete' => 0,
                );
                $join = array(
                    array('laboratory_package lp', 'a.laboratory_package_id = p.id', 'INNER'),
                );
                $laboratory_request_items = $this->Laboratory_request_item->select(0, $select, $where);

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

                $output[$i]->laboratory_request[$j]->laboratory_request_items = $laboratory_request_items;
            }

            // NOTE - comment karena radiology dari cppt tidak masuk pada banner dokter
            // $select = 'a.id, a.no_request';
            // $where = array(
            //     'a.soap_id' => $soap_id,
            //     'a.is_delete' => 0
            // );
            // $join = array(
            //     array('soap s', 'a.soap_id = s.id', 'INNER'),
            // );
            // $radiology_request = $this->Radiology_request->select(0, $select, $where);

            $radiology_request = $this->db->query("
                SELECT a.id, a.no_request, a.clinical_indications
                FROM radiology_request as a
                WHERE soap_id = '$soap_id'
                AND a.is_delete = 0
                AND (a.cppt_id IS NULL OR a.cppt_id = '')
                ORDER BY a.date_request DESC
            ")->result();

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
                'a.soap_id' => $soap_id,
                'a.is_delete' => 0
            );
            $join = array(
                array('soap s', 'a.soap_id = s.id', 'INNER'),
            );
            $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);

            $output[$i]->physiotherapy_request = $physiotherapy_request;

            for ($j=0; $j < count($physiotherapy_request); $j++) { 
                $physiotherapy_request_id = $physiotherapy_request[$j]->id;

                $select = 'a.physiotherapy_package_id, a.costing_name as physiotherapy_package_name, a.service_class_id';
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

    public function resume_update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $id         = $this->security->xss_clean($p);
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
        $this->form_validation->set_rules($this->resume_update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        } 

        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $summary_medical_history = $dt_put['summary_medical_history'];
        $significant_medications = $dt_put['significant_medications'];
        $take_home_medications = $dt_put['take_home_medications'];
        $exit_id = $dt_put['exit_id'];
        $exit_anythingelse = $dt_put['exit_anythingelse'];
        $exit_condition_type_id = $dt_put['exit_condition_type_id'];
        $exit_condition_anythingelse = $dt_put['exit_condition_anythingelse'];
        $care_instruction_control = $dt_put['care_instruction_control'];
        $care_instruction_control_date = $dt_put['care_instruction_control_date'];
        $care_instruction_anythingelse = $dt_put['care_instruction_anythingelse'];
        $home_education = $dt_put['home_education'];
        $physical_check = $dt_put['physical_check'];
        $transfer_reason = $dt_put['transfer_reason'];
        $is_resume_exit = $dt_put['is_resume_exit'];

        $registration_destination = $this->Registration_destination->select_id($registration_destination_id);

        if ($registration_destination == "") {
            $this->REST_Return(404, 'registration_destination with id ' . $registration_destination_id . ' does not exist!');
            return;
        }

        $doctor_in_charge_id = $this->Employee->select_id($doctor_in_charge_id);
        
        if ($doctor_in_charge_id == "") {
            $this->REST_Return(404, 'doctor_in_charge_id with id ' . $doctor_in_charge_id . ' does not exist!');
            return;
        }

        if ($exit_id != "") {
            $exit = $this->Exits->select_id($exit_id);
            if ($exit == "") {
                $this->REST_Return(404, 'exit with exit_id ' . $exit_id . ' does not exist!');
                return;
            }
        }

        $where = array('soap_id' => $id);
        $soap_o_id = $this->Soap_o->select_id(0, $where);

        if ($soap_o_id == "") {
            $this->REST_Return(404, 'soap_o with soap_id ' . $id . ' does not exist!');
            return;
        }

        $soap = $this->$model->select_id($id);

        if ($soap == "") {
            $this->REST_Return(404, 'soap with id ' . $id . ' does not exist!');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'transfer_reason'   => $transfer_reason
        );

        $this->Registration_destination->update_new($registration_destination_id, $dt_registration_destination);

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

        /* Start Transaction */
        $this->db->trans_start();

        $dt_soap_o = array(
            'physical_check'   => $physical_check,
        );

        $this->Soap_o->update_new($soap_o_id, $dt_soap_o);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_o,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update soap_o', $response_data);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_soap = array(
            'registration_destination_id'       => $registration_destination_id,
            'doctor_in_charge_id'               => $doctor_in_charge_id,
            'summary_medical_history'           => $summary_medical_history,
            'significant_medications'           => $significant_medications,
            'take_home_medications'             => $take_home_medications,
            'exit_id'                           => $exit_id,
            'exit_anythingelse'                 => $exit_anythingelse,
            'exit_condition_type_id'            => $exit_condition_type_id,
            'exit_condition_anythingelse'       => $exit_condition_anythingelse,
            'care_instruction_control'          => $care_instruction_control,
            'care_instruction_control_date'     => $care_instruction_control_date,
            'care_instruction_anythingelse'     => $care_instruction_anythingelse,
            'home_education'                    => $home_education,
            'is_resume_exit'                    => $is_resume_exit,
        );

        $this->$model->whereFields = array();
        $id = $this->$model->update_new($id, $dt_soap);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update soap', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }

    /**
     * Soap API
     * --------------------
     * @param: registration_destination_id
     * @param: doctor_in_charge_id
     * --------------------
     * @method : POST
     * @link: version/mr/soap
     **/

    public function update_full_put($p = 0) {
        // $id         = $this->security->xss_clean($p);
        // // CALL JSON POST
        // $data       = $this->json_put();
        // // DEFINE ATTR
        // $dt_put     = $data['dt_postput'];
        // $device     = $data['dt_server']['HTTP_USER_AGENT'];
        // $ip         = $this->get_client_ip();
        // $error      = TRUE;

        // // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
        //     # Form Validation
        //     $this->form_validation->set_data($dt_put);
        //     // DATA SOAP
        //     $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
        //     $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|required|max_length[36]');
        //     // DATA SOAP_S
        //     $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
        //     $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
        //     $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
        //     $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
        //     $this->form_validation->set_rules('history_drugs', 'history_drugs', 'trim');
        //     $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim');
        //     // DATA SOAP_O
        //     $this->form_validation->set_rules('physical_check', 'physical_check', 'trim');
        //     $this->form_validation->set_rules('soap_o_anythingelse', 'soap_o_anythingelse', 'trim');
        //     $this->form_validation->set_rules('systolic_blood_pressure', 'systolic_blood_pressure', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('diastolic_blood_pressure', 'diastolic_blood_pressure', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('classification_blood_pressure_type_id', 'classification_blood_pressure_type_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('pulse_frequency', 'pulse_frequency', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('breath_frequency', 'breath_frequency', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('oximetry_frequency', 'oximetry_frequency', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('classification_oximetry_type_id', 'classification_oximetry_type_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('temperature', 'temperature', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('classification_temperature_type_id', 'classification_temperature_type_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('blood_sugar', 'blood_sugar', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('weight', 'weight', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('classification_weight_type_id', 'classification_weight_type_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('height', 'height', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('imt', 'imt', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('head_circumference', 'head_circumference', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('chest_circumference', 'chest_circumference', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('arm_circumference', 'arm_circumference', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('stomach_circumference', 'stomach_circumference', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('knee_circumference', 'knee_circumference', 'trim|max_length[8]');
        //     // DATA SOAP_O_ACTION
        //     $this->form_validation->set_rules('canvasing_type_id', 'canvasing_type_id', 'trim|max_length[1]');
        //     $this->form_validation->set_rules('canvasing_text', 'canvasing_text', 'trim');
        //     $this->form_validation->set_rules('attr1', 'attr1', 'trim');
        //     $this->form_validation->set_rules('attr2', 'attr2', 'trim');
        //     // DATA SOAP_A
        //     $this->form_validation->set_rules('development_notes', 'development_notes', 'trim');
        //     $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim');
        //     // DATA SOAP_A_ACTION
        //     $this->form_validation->set_rules('icd10[]', 'icd10', 'trim');
        //     // DATA SOAP_P
        //     $this->form_validation->set_rules('therapy_plan', 'therapy_plan', 'trim');
        //     // DATA SOAP_P_ACTION
        //     $this->form_validation->set_rules('icd9cm[]', 'icd9cm', 'trim');
        //     // DATA TRANSACTION_DOCTOR
        //     $this->form_validation->set_rules('transaction_doctor', 'transaction_doctor', 'trim');
        //     $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|max_length[36]');
        //     $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
        //     // DATA TRANSACTION_PHYSIOTHERAPY
        //     $this->form_validation->set_rules('transaction_physiotherapy', 'transaction_physiotherapy', 'trim');
        //     $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|max_length[36]');
        //     $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[8]');
        //     $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
        //     // DATA LABORATORY_REQUEST
        //     $this->form_validation->set_rules('laboratory_date_request', 'laboratory_date_request', 'trim');
        //     $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim');
        //     // DATA RADIOLOGY_REQUEST
        //     $this->form_validation->set_rules('radiology_date_request', 'radiology_date_request', 'trim');
        //     $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim');
        //     // DATA PHYSIOTHERAPY_REQUEST
        //     $this->form_validation->set_rules('physiotherapy_date_request', 'physiotherapy_date_request', 'trim');
        //     $this->form_validation->set_rules('physiotherapy_request_items[]', 'physiotherapy_request_items', 'trim');
        //     // DATA PRESCRIPTION
        //     $this->form_validation->set_rules('form_prescription[]', 'form_prescription', 'trim');
        //     if ($this->form_validation->run() == FALSE){
        //         // Form Validation Errors
        //         $response_data = $this->form_validation->error_array();
        //         $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        //     } else {
        //         $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        //         $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        //         // Check registration_destination_id & doctor_in_charge_id
        //         $model = $this->name;
        //         /* Start Transaction */
        //         $this->db->trans_start();
        //         $dt_soap = array(
        //             'id'                             => $id,
        //             'user_updated'                   => $user_id,
        //             'date_updated'                   => date("Y-m-d H:i:s",time())
        //         );
        //         // echo "<pre>";
        //         // var_dump($dt_soap);//coba
        //         $this->$model->replace($dt_soap);

        //         // SOAP_S
        //         $dt_soap_s = array(
        //             'id'                    => isset($dt_put['soap_s_id']) ? $dt_put['soap_s_id'] : '',
        //             'anamesa_type_id'       => isset($dt_put['anamesa_type_id']) ? $dt_put['anamesa_type_id'] : '',
        //             'family_name'           => isset($dt_put['family_name']) ? $dt_put['family_name'] : '',
        //             'family_relation_id'    => isset($dt_put['family_relation_id']) ? $dt_put['family_relation_id'] : '',
        //             'main_complaint'        => isset($dt_put['main_complaint']) ? $dt_put['main_complaint'] : '',
        //             'history_disease_now'   => isset($dt_put['history_disease_now']) ? $dt_put['history_disease_now'] : '',
        //             'history_disease_past'  => isset($dt_put['history_disease_past']) ? $dt_put['history_disease_past'] : '',
        //             'history_drugs'         => isset($dt_put['history_drugs']) ? $dt_put['history_drugs'] : '',
        //             'history_allergy'       => isset($dt_put['history_allergy']) ? $dt_put['history_allergy'] : '',
        //             'user_updated'          => $user_id,
        //             'date_updated'          => date("Y-m-d H:i:s",time())
        //         );
        //         // var_dump($dt_soap_s);//coba
        //         $this->Soap_s->replace($dt_soap_s);

        //         // SOAP_O
        //         $dt_soap_o = array(
        //             'id'                                        => isset($dt_put['soap_o_id']) ? $dt_put['soap_o_id'] : '',
        //             'physical_check'                            => isset($dt_put['physical_check']) ? $dt_put['physical_check'] : '',
        //             'anythingelse'                              => isset($dt_put['soap_o_anythingelse']) ? $dt_put['soap_o_anythingelse'] : '',
        //             'systolic_blood_pressure'                   => isset($dt_put['systolic_blood_pressure']) ? $dt_put['systolic_blood_pressure'] : '',
        //             'diastolic_blood_pressure'                  => isset($dt_put['diastolic_blood_pressure']) ? $dt_put['diastolic_blood_pressure'] : '',
        //             'classification_blood_pressure_type_id'     => isset($dt_put['classification_blood_pressure_type_id']) ? $dt_put['classification_blood_pressure_type_id'] : '',
        //             'pulse_frequency'                           => isset($dt_put['pulse_frequency']) ? $dt_put['pulse_frequency'] : '',
        //             'breath_frequency'                          => isset($dt_put['breath_frequency']) ? $dt_put['breath_frequency'] : '',
        //             'oximetry_frequency'                        => isset($dt_put['oximetry_frequency']) ? $dt_put['oximetry_frequency'] : '',
        //             'classification_oximetry_type_id'           => isset($dt_put['classification_oximetry_type_id']) ? $dt_put['classification_oximetry_type_id'] : '',
        //             'temperature'                               => isset($dt_put['temperature']) ? $dt_put['temperature'] : '',
        //             'classification_temperature_type_id'        => isset($dt_put['classification_temperature_type_id']) ? $dt_put['classification_temperature_type_id'] : '',
        //             'blood_sugar'                               => isset($dt_put['blood_sugar']) ? $dt_put['blood_sugar'] : '',
        //             'weight'                                    => isset($dt_put['weight']) ? $dt_put['weight'] : '',
        //             'classification_weight_type_id'             => isset($dt_put['classification_weight_type_id']) ? $dt_put['classification_weight_type_id'] : '',
        //             'height'                                    => isset($dt_put['height']) ? $dt_put['height'] : '',
        //             'imt'                                       => isset($dt_put['imt']) ? $dt_put['imt'] : '',
        //             'head_circumference'                        => isset($dt_put['head_circumference']) ? $dt_put['head_circumference'] : '',
        //             'chest_circumference'                       => isset($dt_put['chest_circumference']) ? $dt_put['chest_circumference'] : '',
        //             'arm_circumference'                         => isset($dt_put['arm_circumference']) ? $dt_put['arm_circumference'] : '',
        //             'stomach_circumference'                     => isset($dt_put['stomach_circumference']) ? $dt_put['stomach_circumference'] : '',
        //             'knee_circumference'                        => isset($dt_put['knee_circumference']) ? $dt_put['knee_circumference'] : '',
        //             'user_updated'                              => $user_id,
        //             'date_updated'                              => date("Y-m-d H:i:s",time())
        //         );
        //         // var_dump($dt_soap_o);//coba
        //         $this->Soap_o->replace($dt_soap_o);

        //         // // SOAP_O_ACTION
        //         // $dt_soap_o_action = array(
        //         //     'id'                        => isset($dt_put['soap_o_id']) ? $dt_put['soap_o_id'] : '',,
        //         //     'soap_o_id'                 => $id_soap_o,
        //         //     'canvasing_type_id'         => isset($dt_put['canvasing_type_id']) ? $dt_put['canvasing_type_id'] : '',
        //         //     'canvasing_text'            => isset($dt_put['canvasing_text']) ? $dt_put['canvasing_text'] : '',
        //         //     'attr1'                     => isset($dt_put['attr1']) ? $dt_put['attr1'] : '',
        //         //     'attr2'                     => isset($dt_put['attr2']) ? $dt_put['attr2'] : ''
        //         // );
        //         // $this->Soap_o_action->create($dt_soap_o_action);

        //         // SOAP_A
        //         $dt_soap_a = array(
        //             'id'                    => isset($dt_put['soap_a_id']) ? $dt_put['soap_a_id'] : '',
        //             'development_notes'     => isset($dt_put['development_notes']) ? $dt_put['development_notes'] : '',
        //             'anythingelse'          => isset($dt_put['soap_a_anythingelse']) ? $dt_put['soap_a_anythingelse'] : '',
        //             'obgyn_diag_date'       => isset($dt_put['obgyn_diag_date']) ? $dt_put['obgyn_diag_date'] : '',                   
        //             'obgyn_diag_clinis'     => isset($dt_put['obgyn_diag_clinis']) ? $dt_put['obgyn_diag_clinis'] : '',                 
        //             'obgyn_diag_hpht'       => isset($dt_put['obgyn_diag_hpht']) ? $dt_put['obgyn_diag_hpht'] : '',                   
        //             'obgyn_s_d'             => isset($dt_put['obgyn_s_d']) ? $dt_put['obgyn_s_d'] : '',                 
        //             'obgyn_j_exam'          => isset($dt_put['obgyn_j_exam']) ? $dt_put['obgyn_j_exam'] : '',                  
        //             'obgyn_j_pos'           => isset($dt_put['obgyn_j_pos']) ? $dt_put['obgyn_j_pos'] : '',                   
        //             'obgyn_j_plas'          => isset($dt_put['obgyn_j_plas']) ? $dt_put['obgyn_j_plas'] : '',                  
        //             'obgyn_j_k'             => isset($dt_put['obgyn_j_k']) ? $dt_put['obgyn_j_k'] : '',                 
        //             'obgyn_j_sex'           => isset($dt_put['obgyn_j_sex']) ? $dt_put['obgyn_j_sex'] : '',                   
        //             'obgyn_j_weight'        => isset($dt_put['obgyn_j_weight']) ? $dt_put['obgyn_j_weight'] : '',                    
        //             'obgyn_size_usg'        => isset($dt_put['obgyn_size_usg']) ? $dt_put['obgyn_size_usg'] : '',                    
        //             'obgyn_usg_desc'        => isset($dt_put['obgyn_usg_desc']) ? $dt_put['obgyn_usg_desc'] : '',                    
        //             'obgyn_usg_file'        => isset($dt_put['obgyn_usg_file']) ? $this->base64ToFile($dt_put['obgyn_usg_file'], $id) : '',
        //             'user_updated'          => $user_id,
        //             'date_updated'          => date("Y-m-d H:i:s",time())
        //         );
        //         // var_dump($dt_soap_a);//coba
        //         $this->Soap_a->replace($dt_soap_a);

        //         // SOAP_A_ACTION
        //         if(isset($dt_put['icd10'])) {
        //             for ($i=0; $i < count($dt_put['icd10']) ; $i++) { 
        //                 // Check icd10_id
        //                 if(isset($dt_put['soap_a_id']) || $dt_put['soap_a_id'] !== '' || $dt_put['soap_a_id'] !== NULL) {
        //                     if ($dt_put['icd10'][$i]['id'] == '' || $dt_put['icd10'][$i]['id'] == NULL) {
        //                         //CREATED
        //                         $dt_soap_a_action = array(
        //                             'soap_a_id'         => $dt_put['soap_a_id'],
        //                             'icd10_id'          => $dt_put['icd10'][$i]['icd10_id'],
        //                             'user_added'        => $user_id,
        //                             'date_added'        => date('Y-m-d H:i:s')
        //                         );
        //                     } else {
        //                         $this->Soap_a_action->whereFields = array();$this->Soap_a_action->whereFields['soap_a_id'] = $dt_put['soap_a_id'];
        //                         $dt_s_aa = $this->Soap_a_action->read();
        //                         if (count($dt_s_aa) > 1) {
        //                             for ($a=0; $a < count($dt_s_aa); $a++) {
        //                                 if ($dt_s_aa[$a]->id == $dt_put['icd10'][$i]['id'] && $dt_put['icd10'][$i]['id'] != null && $dt_put['icd10'][$i]['id'] != "") {
        //                                     // UPDATED
        //                                     if ($dt_s_aa[$a]->icd10_id != $dt_put['icd10'][$i]['id']) {
        //                                         $dt_soap_a_action = array(
        //                                             'id'                => $dt_s_aa[$a]->id,
        //                                             'soap_a_id'         => $dt_put['soap_a_id'],
        //                                             'icd10_id'          => $dt_put['icd10'][$i]['icd10_id'],
        //                                             'user_updated'      => $user_id,
        //                                             'date_updated'      => date('Y-m-d H:i:s')
        //                                         );
        //                                     }
        //                                 // DELETED
        //                                 } else {
        //                                     $dt_soap_a_action = array(
        //                                         'id'                => $dt_s_aa[$a]->id,
        //                                         'is_delete'         => 1,
        //                                         'user_deleted'      => $user_id,
        //                                         'date_deleted'      => date('Y-m-d H:i:s')
        //                                     );
        //                                 }
        //                             }
        //                         }
        //                     }
        //                     var_dump($dt_soap_a_action);//coba
        //                     // $this->Soap_a_action->replace($dt_soap_a_action);
        //                 }
        //             }
        //         }
        //         die();
        //         echo "<pre>";

        //         // SOAP_P
        //         $dt_soap_p = array(
        //             'id'                 => isset($dt_put['soap_p_id']) ? $dt_put['soap_p_id'] : '',
        //             'therapy_plan'       => isset($dt_put['therapy_plan']) ? $dt_put['therapy_plan'] : '',
        //         );
        //         var_dump($dt_soap_p);//coba
        //         // $this->Soap_p->replace($dt_soap_p);

        //         // SOAP_P_ACTION
        //         if(isset($dt_put['icd9cm'])) {
        //             for ($i=0; $i < count($dt_put['icd9cm']) ; $i++) { 
        //                 // Check icd9cm_id
        //                 if($this->Icd9cm->read($dt_put['icd9cm'][$i]['icd9cm_id'], false) != []) {
        //                     $dt_soap_p_action = array(
        //                         'id'                => $dt_put['icd9cm'][$i]['id'],
        //                         'soap_p_id'         => $id_soap_p,
        //                         'icd9cm_id'         => $dt_put['icd9cm'][$i]['icd9cm_id']
        //                     );
        //                     var_dump($dt_soap_p_action);//coba
        //                     // $this->Soap_p_action->replace($dt_soap_p_action);
        //                 } else {
        //                     $this->REST_Return(404, 'There is no icd9cm with id ' . $dt_put['icd9cm'][$i]['icd9cm_id']);
        //                     return;
        //                 }
        //             }
        //         }

        //         // INIT_TRANSACTION_DOCTOR
        //         // if(isset($dt_put['transaction_doctor'])) {
        //         //     for ($i=0; $i < count($dt_put['transaction_doctor']); $i++) { 
        //         //         $costing = $this->Costing_services_class->read($dt_put['transaction_doctor'][$i]['costing_id'],false);
        //         //         if($costing != []) {
        //         //             $dt_transaction_doctor = array(
        //         //                 'id'                    => isset($dt_put['transaction_doctor'][$i]['id']) ? $dt_put['transaction_doctor'][$i]['id'] : '',
        //         //                 'soap_id'               => $id,
        //         //                 'costing_id'            => $dt_put['transaction_doctor'][$i]['costing_id'],
        //         //                 'costing_name'          => $costing[0]->name,
        //         //                 'var_1'                 => isset($dt_put['transaction_doctor'][$i]['var_1']) ? $dt_put['transaction_doctor'][$i]['var_1'] : '',
        //         //                 'var_2'                 => isset($dt_put['transaction_doctor'][$i]['var_2']) ? $dt_put['transaction_doctor'][$i]['var_2'] : '',
        //         //                 'var_3'                 => isset($dt_put['transaction_doctor'][$i]['var_3']) ? $dt_put['transaction_doctor'][$i]['var_3'] : '',
        //         //                 'notes'                 => isset($dt_put['transaction_doctor'][$i]['notes']) ? $dt_put['transaction_doctor'][$i]['notes'] : '',
        //         //                 'date_added'            => date("Y-m-d H:i:s",time())
        //         //             );
        //         //             var_dump($dt_transaction_doctor);//coba
        //         //             // $this->Transaction_doctor->replace($dt_transaction_doctor);
        //         //         } else {
        //         //             $this->REST_Return(404, 'There is no costing with id ' . $dt_put['transaction_doctor'][$i]['costing_id']);
        //         //             return;
        //         //         }
        //         //     }
        //         // }

        //         // INIT_TRANSACTION_PHYSIOTHERAPY
        //         // if(isset($dt_put['transaction_physiotherapy'])) {
        //         //     if(count($dt_put['transaction_physiotherapy']) > 0){
        //         //         for ($i=0; $i < count($dt_put['transaction_physiotherapy']); $i++) { 
        //         //             $costing = $this->Costing->read($dt_put['transaction_physiotherapy'][$i]['costing_id'],false);
        //         //             if($costing != []) {
        //         //                 $dt_transaction_physiotherapy = array(
        //         //                     'id'                => isset($dt_put['transaction_physiotherapy'][$i]['id']) ? $dt_put['transaction_physiotherapy'][$i]['id'] : '',
        //         //                     'soap_id'           => $id,
        //         //                     'costing_id'        => $dt_put['transaction_physiotherapy'][$i]['costing_id'],
        //         //                     'costing_name'      => $costing[0]->name,
        //         //                     'var_1'             => isset($dt_put['transaction_physiotherapy'][$i]['var_1']) ? $dt_put['transaction_physiotherapy'][$i]['var_1'] : '',
        //         //                     'var_2'             => isset($dt_put['transaction_physiotherapy'][$i]['var_2']) ? $dt_put['transaction_physiotherapy'][$i]['var_2'] : '',
        //         //                     'var_3'             => isset($dt_put['transaction_physiotherapy'][$i]['var_3']) ? $dt_put['transaction_physiotherapy'][$i]['var_3'] : '',
        //         //                     'notes'             => isset($dt_put['transaction_physiotherapy'][$i]['notes']) ? $dt_put['transaction_physiotherapy'][$i]['notes'] : '',
        //         //                     'date_added'        => date("Y-m-d H:i:s",time())
        //         //                 );
        //         //                 var_dump($dt_transaction_physiotherapy);//coba
        //         //                 // $this->Transaction_physiotherapy->replace($dt_transaction_physiotherapy);
        //         //             } else {
        //         //                 $this->REST_Return(404, 'There is no costing with id ' . $dt_put['transaction_physiotherapy'][$i]['costing_id']);
        //         //                 return;
        //         //             }
        //         //         }
        //         //     }
        //         // }

        //         // INIT_TRANSACTION_PRESCRIPTION
        //         // if(isset($dt_put['form_prescription'])) {
        //         //     // Count no_request
        //         //     // $dt_prescription = array(
        //         //     //     'date_update'                => date("Y-m-d H:i:s",time()),
        //         //     // );
        //         //     // var_dump($dt_prescription);//coba
        //         //     // $id_prescription = $this->Prescription->replace($dt_prescription);

        //         //     // INIT_PRESCRIPTION_ITEM
        //         //     for ($i=0; $i < count($dt_put['form_prescription']); $i++) { 
        //         //         // Check pharmacy_catalog_id
        //         //         if($this->Catalog->read($dt_put['form_prescription'][$i]['pharmacy_catalog_id'], false) != []) {
        //         //             for ($i=0; $i < count($dt_put['form_prescription_parent']); $i++) { 
        //         //                 $dt_prescription_item = array(
        //         //                     'pharmacy_prescription_id'      => $dt_put['form_prescription'][$i]['pharmacy_prescription_id'],
        //         //                     'pharmacy_catalog_id'           => $dt_put['form_prescription'][$i]['pharmacy_catalog_id'],
        //         //                     'prescription_item_type_id'     => $dt_put['form_prescription'][$i]['prescription_type_id'],
        //         //                     'prescription_item_type_used'   => $dt_put['form_prescription'][$i]['prescription_type_used'],
        //         //                     'prescription_item_type_value'  => $dt_put['form_prescription'][$i]['prescription_type_value'],
        //         //                     'package_id'                    => $dt_put['form_prescription'][$i]['package_id'],
        //         //                     'package_name'                  => $dt_put['form_prescription'][$i]['package_name'],
        //         //                     'dose'                          => isset($dt_put['form_prescription'][$i]['dose']) ? $dt_put['form_prescription'][$i]['dose'] : '',
        //         //                     'dose_doctor'                   => isset($dt_put['form_prescription'][$i]['sum_dose']) ? intval($dt_put['form_prescription'][$i]['sum_dose']) : '',
        //         //                     'qty_doctor'                    => isset($dt_put['form_prescription'][$i]['value_package']) ? $dt_put['form_prescription'][$i]['value_package'] : '',
        //         //                     'qty'                           => isset($dt_put['form_prescription'][$i]['value_package']) ? $dt_put['form_prescription'][$i]['value_package'] : '',
        //         //                     'qty_copy'                      => isset($dt_put['form_prescription'][$i]['qty_copy']) ? $dt_put['form_prescription'][$i]['qty_copy'] : '',
        //         //                     'var_1'                         => isset($dt_put['form_prescription'][$i]['var_1']) ? $dt_put['form_prescription'][$i]['var_1'] : '',
        //         //                     'var_2'                         => isset($dt_put['form_prescription'][$i]['var_2']) ? $dt_put['form_prescription'][$i]['var_2'] : '',
        //         //                     'var_3'                         => isset($dt_put['form_prescription'][$i]['var_3']) ? $dt_put['form_prescription'][$i]['var_3'] : '',
        //         //                     'sort'                          => isset($dt_put['form_prescription'][$i]['sort']) ? $dt_put['form_prescription'][$i]['sort'] : '',
        //         //                     'used'                          => isset($dt_put['form_prescription'][$i]['used']) ? $dt_put['form_prescription'][$i]['used'] : '',
        //         //                     'desc'                          => isset($dt_put['form_prescription'][$i]['desc']) ? $dt_put['form_prescription'][$i]['desc'] : '',
        //         //                 );
        //         //             }
        //         //             var_dump($dt_prescription_item);//coba
        //         //             // $this->Prescription_item->replace($dt_prescription_item);
        //         //         } else {
        //         //             $this->REST_Return(404, 'There is no pharmacy_catalog_id with id ' . $dt_put['form_prescription'][$i]['pharmacy_catalog_id']);
        //         //             return;
        //         //         }
        //         //     }
        //         // }

        //         // INIT_LABORATORY_REQUEST
        //         // if(isset($dt_put['laboratory_request_items'])) {
        //         //     if(count($dt_put['laboratory_request_items']) > 0){
        //         //         $dt_laboratory_request = array(
        //         //             'institute_branch_id'   => $institute_branch_id,
        //         //             'soap_id'               => $id,
        //         //             'no_request'            => $no_request,
        //         //             'queue'                 => $queue,
        //         //             'date_updated'          => date('Y-m-d H:i:s',time())
        //         //         );
        //         //         var_dump($dt_prescription_item);//coba
        //         //         // $id_laboratory_request = $this->Laboratory_request->replace($dt_laboratory_request);
        //         //         // INIT_LABORATORY_REQUEST_ITEM
        //         //         for ($i=0; $i < count($dt_put['laboratory_request_items']) ; $i++) {
        //         //             $laboratory_package = $this->Laboratory_package->read($dt_put['laboratory_request_items'][$i]['laboratory_package_id'], false);
        //         //             $service_class = $this->Service_class->read($dt_put['laboratory_request_items'][$i]['service_class_id']);
        //         //             // Check laboratory_package_id & service_class_id
        //         //             if($laboratory_package != [] && $service_class != []) {
        //         //                 $costing = $this->Costing->read($laboratory_package[0]->costing_id,false);
        //         //                 $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
        //         //                 // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
        //         //                 $costing_services_class = $this->Costing_services_class->read(0,false);
        //         //                 $dt_laboratory_request_item = array(
        //         //                     'laboratory_request_id'     => $id_laboratory_request,
        //         //                     'laboratory_package_id'     => $dt_put['laboratory_request_items'][$i]['laboratory_package_id'],
        //         //                     'service_class_id'          => $service_class[0]->id,
        //         //                     'var_1'                     => $costing_services_class[0]->var_1,
        //         //                     'var_2'                     => $costing_services_class[0]->var_2,
        //         //                     'var_3'                     => $costing_services_class[0]->var_3,
        //         //                     'costing_id'                => $costing[0]->id,
        //         //                     'costing_name'              => $costing[0]->name,
        //         //                     'date_request'              => date('Y-m-d H:i:s'),
        //         //                     'status'                    => 1
        //         //                 );
        //         //                 // var_dump($dt_laboratory_request_item);die();
        //         //                 // $this->Laboratory_request_item->replace($dt_laboratory_request_item);
        //         //             } else {
        //         //                 $this->REST_Return(404, 'There is no laboratory_package with id ' . $dt_put['laboratory_request_items'][$i]['laboratory_package_id'] . ' or service_class with id ' . $dt_put['laboratory_request_items'][$i]['service_class_id']);
        //         //                 return;
        //         //             }
        //         //         }
        //         //     }
        //         // }

        //         // INIT_RADIOLOGY_REQUEST
        //         // if(isset($dt_put['radiology_request_items'])) {
        //         //     if(count($dt_put['radiology_request_items']) > 0){
        //         //         $dt_radiology_request = array(
        //         //             'institute_branch_id'   => $institute_branch_id,
        //         //             'soap_id'               => $id,
        //         //             'no_request'            => $no_request,
        //         //             'queue'                 => $queue,
        //         //             'date_request'          => date('Y-m-d H:i:s',time())
        //         //         );
        //         //         $id_radiology_request = $this->Radiology_request->create($dt_radiology_request);
        //         //         // INIT_RADIOLOGY_REQUEST_ITEM
        //         //         for ($i=0; $i < count($dt_put['radiology_request_items']) ; $i++) {
        //         //             $radiology_package = $this->Radiology_package->read($dt_put['radiology_request_items'][$i]['radiology_package_id'], false);
        //         //             $service_class = $this->Service_class->read($dt_put['radiology_request_items'][$i]['service_class_id']);
        //         //             // Check radiology_package_id & service_class_id
        //         //             if($radiology_package != [] && $service_class != []) {
        //         //                 $costing = $this->Costing->read($radiology_package[0]->costing_id,false);
        //         //                 $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
        //         //                 // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
        //         //                 $costing_services_class = $this->Costing_services_class->read(0,false);
        //         //                 $dt_radiology_request_item = array(
        //         //                     'radiology_request_id'      => $id_radiology_request,
        //         //                     'radiology_package_id'      => $dt_put['radiology_request_items'][$i]['radiology_package_id'],
        //         //                     'service_class_id'          => $service_class[0]->id,
        //         //                     'var_1'                     => $costing_services_class[0]->var_1,
        //         //                     'var_2'                     => $costing_services_class[0]->var_2,
        //         //                     'var_3'                     => $costing_services_class[0]->var_3,
        //         //                     'costing_id'                => $costing[0]->id,
        //         //                     'costing_name'              => $costing[0]->name,
        //         //                     'date_request'              => date('Y-m-d H:i:s'),
        //         //                     'status'                    => 1
        //         //                 );
        //         //                 // $this->Radiology_request_item->replace($dt_radiology_request_item);
        //         //             } else {
        //         //                 $this->REST_Return(404, 'There is no radiology_package with id ' . $dt_put['radiology_request_items'][$i]['radiology_package_id'] . ' or service_class with id ' . $dt_put['radiology_request_items'][$i]['service_class_id']);
        //         //                 return;
        //         //             }
        //         //         }
        //         //     }
        //         // }

        //         // INIT_PHYSIOTHERAPY_REQUEST
        //         // if(isset($dt_put['physiotherapy_request_items'])) {
        //         //     if(count($dt_put['physiotherapy_request_items']) > 0){
        //         //         $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
        //         //         $this->Physiotherapy_request->orderFields = array('a.queue' => 'DESC');
        //         //         $this->Physiotherapy_request->limit = 1;
        //         //         $tmp = $this->Physiotherapy_request->read(0,false);
        //         //         if($tmp != []) {
        //         //             $queue = $tmp[0]->queue + 1;
        //         //             // Check if reset is needed
        //         //             if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
        //         //                 $queue = 1;
        //         //             }
        //         //         } else {
        //         //             $queue = 1;
        //         //         }

        //         //         $dt_physiotherapy_request = array(
        //         //             'institute_branch_id'   => $institute_branch_id,
        //         //             'soap_id'               => $id,
        //         //             'no_request'            => $no_request,
        //         //             'queue'                 => $queue,
        //         //             'date_request'          => date('Y-m-d H:i:s',time())
        //         //         );
        //         //         // $id_physiotherapy_request = $this->Physiotherapy_request->replace($dt_physiotherapy_request);
        //         //         // INIT_PHYSIOTHERAPY_REQUEST_ITEM
        //         //         for ($i=0; $i < count($dt_put['physiotherapy_request_items']) ; $i++) {
        //         //             $physiotherapy_package = $this->Physiotherapy_package->read($dt_put['physiotherapy_request_items'][$i]['physiotherapy_package_id'], false);
        //         //             $service_class = $this->Service_class->read($dt_put['physiotherapy_request_items'][$i]['service_class_id']);
        //         //             // Check physiotherapy_package_id & service_class_id
        //         //             if($physiotherapy_package != [] && $service_class != []) {
        //         //                 $costing = $this->Costing->read($physiotherapy_package[0]->costing_id,false);
        //         //                 $this->Costing_services_class->whereFields['a.costing_id'] = $costing[0]->id;
        //         //                 // $this->Costing_services_class->whereFields['a.service_class_id'] = $service_class[0]->id;
        //         //                 $costing_services_class = $this->Costing_services_class->read(0,false);
        //         //                 $dt_physiotherapy_request_item = array(
        //         //                     'physiotherapy_request_id'  => $id_physiotherapy_request,
        //         //                     'physiotherapy_package_id'  => $dt_put['physiotherapy_request_items'][$i]['physiotherapy_package_id'],
        //         //                     'service_class_id'          => $service_class[0]->id,
        //         //                     'var_1'                     => $costing_services_class[0]->var_1,
        //         //                     'var_2'                     => $costing_services_class[0]->var_2,
        //         //                     'var_3'                     => $costing_services_class[0]->var_3,
        //         //                     'costing_id'                => $costing[0]->id,
        //         //                     'costing_name'              => $costing[0]->name,
        //         //                     'date_request'              => date('Y-m-d H:i:s'),
        //         //                     'status'                    => 1
        //         //                 );
        //         //                 // $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
        //         //             } else {
        //         //                 $this->REST_Return(404, 'There is no physiotherapy_package with id ' . $dt_put['physiotherapy_request_items'][$i]['physiotherapy_package_id'] . ' or service_class with id ' . $dt_put['physiotherapy_request_items'][$i]['service_class_id']);
        //         //                 return;
        //         //             }
        //         //         }
        //         //     }
        //         // }

        //         $this->db->trans_complete();
        //         /* End Transaction */
        //         if ($this->db->trans_status() === false) {
        //             $response_data = $this->db->trans_status();
        //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        //         } else {
        //             $response_data = array(
        //                 'id'     => $id
        //             );
        //             $this->REST_Return(201, 'SUCCESS', $response_data);
        //         }
        //         return;     
        //     }
        // } else {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }
    }

    public function create_inpatient_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = "Soap_inpatient";
        
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

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        // INIT_SOAP_INPATIENT
        $soap_id = $dt_post['soap_id'];
        $registration_destination_id = $dt_post['registration_destination_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
        $condition = $dt_post['condition'];
        $indication = $dt_post['indication'];
        $plan = $dt_post['plan'];
        // $result = $dt_post['result'];
        // $type_room_id = $dt_post['type_room_id'];
        $doctor_in_charge = $this->Employee->select_id($doctor_in_charge_id);


        if ($doctor_in_charge == "") {
            $this->REST_Return(404, "there is no employee with id " . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_soap = array(
            'soap_id'                      => $soap_id,
            'registration_destination_id'  => $registration_destination_id,
            'doctor_in_charge_id'          => $doctor_in_charge_id,
            'condition'                    => $condition,
            'indication'                   => $indication,
            'plan'                         => $plan,
            // 'result'                       => $result,
            // 'type_room_id'                 => $type_room_id,
            'user_added'                   => $user_id,
            'date_added'                   => date("Y-m-d H:i:s",time()),
        );
        $id = $this->$model->create($dt_soap);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create soap', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }

    public function update_inpatient_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = "Soap_inpatient";
        
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_inpatient_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        } 

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        // INIT_SOAP_INPATIENT
        $soap_id = $dt_put['soap_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $condition = $dt_put['condition'];
        $indication = $dt_put['indication'];
        $plan = $dt_put['plan'];
        $result = $dt_put['result'];
        $type_room_id = $dt_put['type_room_id'];
        $doctor_in_charge = $this->Employee->select_id($doctor_in_charge_id);


        if ($doctor_in_charge == "") {
            $this->REST_Return(404, "there is no employee with id " . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_soap = array(
            'id'                           => $id,
            'doctor_in_charge_id'          => $doctor_in_charge_id,
            'condition'                    => $condition,
            'indication'                   => $indication,
            'plan'                         => $plan,
            'result'                       => $result,
            'type_room_id'                 => $type_room_id,
            'user_updated'                 => $user_id,
            'date_updated'                 => date("Y-m-d H:i:s",time()),
        );

        if(isset($dt_put['responsible']['name'])){
            $norm = $dt_put['norm'];
            $responsible_id = $dt_put['responsible']['responsible_id'];
            $name = $dt_put['responsible']['name'];
            $phone = $dt_put['responsible']['phone'];
            $address_id = $dt_put['responsible']['address_id'];
            $birth_date = $dt_put['responsible']['birth_date'];
            $insurance_id = $dt_put['insuranceTable']['items'][0]['insurance_id'];
            $company_id = $dt_put['insuranceTable']['items'][0]['company_id'];
            $number_insurance = $dt_put['insuranceTable']['items'][0]['number_insurance'] == "" ? "-" : $dt_put['insuranceTable']['items'][0]['number_insurance'];
            $expired = $dt_put['insuranceTable']['items'][0]['expired'] == "" ? null : $dt_put['insuranceTable']['items'][0]['expired'];
            $service_class_id = $dt_put['insuranceTable']['items'][0]['service_class_id'];
            $service_class_id_right = $dt_put['insuranceTable']['items'][0]['service_class_id_right'];
            $is_up = $dt_put['insuranceTable']['items'][0]['service_class_id_right'] != "" && $dt_put['insuranceTable']['items'][0]['service_class_id_right'] != $dt_put['insuranceTable']['items'][0]['service_class_id'] ? 1 : 0;
            $is_primary = $dt_put['insuranceTable']['items'][0]['is_primary'];

            $dt_soap = array(
                'id'                           => $id,
                'doctor_in_charge_id'          => $doctor_in_charge_id,
                'condition'                    => $condition,
                'indication'                   => $indication,
                'plan'                         => $plan,
                'result'                       => $result,
                'responsible_id'               => $responsible_id,
                'insurance_id'                 => $insurance_id,
                'type_room_id'                 => $type_room_id,
                'user_updated'                 => $user_id,
                'date_updated'                 => date("Y-m-d H:i:s",time()),
            );

            $dt_responsible = array(
                'norm'            => $norm,
                'responsible_id'  => $responsible_id,
                'name'            => $name,
                'phone'           => $phone,
                'address_id'      => $address_id,
                'birth_date'      => $birth_date,
            );
    
            $dt_insurance = array(
                'norm'                         => $norm,
                'institute_branch_id'          => $institute_branch_id,
                'insurance_id'                 => $insurance_id,
                'company_id'                   => $company_id,
                'number_insurance'             => $number_insurance,
                'expired'                      => $expired,
                'service_class_id'             => $service_class_id,
                'service_class_id_right'       => $service_class_id_right,
                'is_up'                        => $is_up,
                'is_primary'                   => $is_primary
            );
            $this->Insurance->replace($dt_insurance);
            $this->Responsible_person->replace($dt_responsible);
            // echo "<pre>";
            // var_dump($dt_soap);
            // var_dump($dt_insurance);
            // var_dump($dt_responsible);
            // echo "</pre>";
            // die();
        }
        $this->$model->replace($dt_soap);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create soap', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }

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
            // DATA SOAP
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|required|max_length[36]');
            // DATA SOAP_S
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[2]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('history_drugs', 'history_drugs', 'trim');
            $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim');
            // DATA SOAP_O
            $this->form_validation->set_rules('physical_check', 'physical_check', 'trim');
            $this->form_validation->set_rules('soap_o_anythingelse', 'soap_o_anythingelse', 'trim');
            $this->form_validation->set_rules('systolic_blood_pressure', 'systolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('diastolic_blood_pressure', 'diastolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_blood_pressure_type_id', 'classification_blood_pressure_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('pulse_frequency', 'pulse_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('breath_frequency', 'breath_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('oximetry_frequency', 'oximetry_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_oximetry_type_id', 'classification_oximetry_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('temperature', 'temperature', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_temperature_type_id', 'classification_temperature_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('blood_sugar', 'blood_sugar', 'trim|max_length[8]');
            $this->form_validation->set_rules('weight', 'weight', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_weight_type_id', 'classification_weight_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('height', 'height', 'trim|max_length[8]');
            $this->form_validation->set_rules('imt', 'imt', 'trim|max_length[8]');
            $this->form_validation->set_rules('head_circumference', 'head_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('chest_circumference', 'chest_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('arm_circumference', 'arm_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('stomach_circumference', 'stomach_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('knee_circumference', 'knee_circumference', 'trim|max_length[8]');
            // DATA SOAP_O_ACTION
            $this->form_validation->set_rules('canvasing_type_id', 'canvasing_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('canvasing_text', 'canvasing_text', 'trim');
            $this->form_validation->set_rules('attr1', 'attr1', 'trim');
            $this->form_validation->set_rules('attr2', 'attr2', 'trim');
            // DATA SOAP_A
            $this->form_validation->set_rules('development_notes', 'development_notes', 'trim');
            $this->form_validation->set_rules('soap_a_anythingelse', 'soap_a_anythingelse', 'trim');
            // DATA SOAP_A_ACTION
            $this->form_validation->set_rules('icd10[]', 'icd10', 'trim');
            // DATA SOAP_P
            $this->form_validation->set_rules('therapy_plan', 'therapy_plan', 'trim');
            // DATA SOAP_P_ACTION
            $this->form_validation->set_rules('icd9cm[]', 'icd9cm', 'trim');
            // DATA TRANSACTION_DOCTOR
            $this->form_validation->set_rules('transaction_doctor[]', 'transaction_doctor', 'trim');
            // DATA TRANSACTION_PHYSIOTHERAPY
            $this->form_validation->set_rules('transaction_physiotherapy[]', 'transaction_physiotherapy', 'trim');
            // DATA LABORATORY_REQUEST
            $this->form_validation->set_rules('laboratory_request_items[]', 'laboratory_request_items', 'trim');
            // DATA RADIOLOGY_REQUEST
            $this->form_validation->set_rules('radiology_request_items[]', 'radiology_request_items', 'trim');
            // DATA PHYSIOTHERAPY_REQUEST
            $this->form_validation->set_rules('physiotherapy_request_items[]', 'physiotherapy_request_items', 'trim');
            // DATA PRESCRIPTION
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('form_prescription[]', 'form_prescription', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                /* Start Transaction */
                $this->db->trans_start();
                $current_user = $this->User->read($user_id,false)[0]->employee_id;
                $registration_destination_id = $dt_put['registration_destination_id'];
                $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
                $doctor_in_charge_id_1 = $dt_put['doctor_in_charge_id_1'];
                $doctor_in_charge_id_2 = $dt_put['doctor_in_charge_id_2'];
                $doctor_in_charge_id_3 = $dt_put['doctor_in_charge_id_3'];
                $doctor_in_charge_id_4 = $dt_put['doctor_in_charge_id_4'];
                $doctor_in_charge_id_5 = $dt_put['doctor_in_charge_id_5'];
                $type_consultation_doctor_2 = clean_postput($dt_put['type_consultation_doctor_2'] ?? null);
                $type_consultation_doctor_3 = clean_postput($dt_put['type_consultation_doctor_3'] ?? null);
                $type_consultation_doctor_4 = clean_postput($dt_put['type_consultation_doctor_4'] ?? null);
                $type_consultation_doctor_5 = clean_postput($dt_put['type_consultation_doctor_5'] ?? null);
                // $advice = $dt_put['advice'];

                $select = 'a.id, a.doctor_in_charge_id_1, a.doctor_in_charge_id_2, a.doctor_in_charge_id_3, a.doctor_in_charge_id_4, a.doctor_in_charge_id_5';
                $soap = $this->$model->select($id, $select);
                if(count($soap) == 0) {
                    $this->REST_Return(404, 'Soap with id ' . $id . ' does not exist!');
                    return;
                }

                $soap_id = $soap[0]->id;

                $doctor_in_charge_id_1_exist = $soap[0]->doctor_in_charge_id_1;
                $doctor_in_charge_id_2_exist = $soap[0]->doctor_in_charge_id_2;
                $doctor_in_charge_id_3_exist = $soap[0]->doctor_in_charge_id_3;
                $doctor_in_charge_id_4_exist = $soap[0]->doctor_in_charge_id_4;
                $doctor_in_charge_id_5_exist = $soap[0]->doctor_in_charge_id_5;

                $doctor_in_charge_exist = array(
                    $doctor_in_charge_id_1_exist,
                    $doctor_in_charge_id_2_exist,
                    $doctor_in_charge_id_3_exist,
                    $doctor_in_charge_id_4_exist,
                    $doctor_in_charge_id_5_exist,
                );

                $doctor_in_charge_change = array();

                $select = 'a.id, a.registration_id';
                $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
                if(count($registration_destination) == 0) {
                    $this->REST_Return(404, 'Registration_destination with id ' . $registration_destination_id . ' does not exist!');
                    return;
                }

                $registration_id = $registration_destination[0]->registration_id;

                $select = 'a.id, a.norm, p.birth_date';
                $where = array();
                $join = array(
                    array('patient p', 'a.norm = p.norm', 'INNER'),
                );
                $registration = $this->Registration->select($registration_id, $select, $where, $join);

                $norm = $registration[0]->norm;
                $birth_date = $registration[0]->birth_date;

                $doctor_in_charge = $this->Employee->select_id($doctor_in_charge_id);
                if($doctor_in_charge == "") {
                    $this->REST_Return(404, 'Employee with id ' . $doctor_in_charge_id . ' does not exist!');
                    return;
                }

                if ($doctor_in_charge_id_1 != null) {
                    $employee_1 = $this->Employee->select_id($doctor_in_charge_id_1);
                    
                    if ($employee_1 == "") {
                        $this->REST_Return(404, 'there is no employee_1 with id ' . $doctor_in_charge_id_1);
                        return;
                    }

                    if (in_array($doctor_in_charge_id_1, $doctor_in_charge_exist) != 1) {
                        $doctor_in_charge_change[$doctor_in_charge_id_1] = $doctor_in_charge_exist[0];
                    }
                }

                if ($doctor_in_charge_id_2 != null) {
                    $employee_2 = $this->Employee->select_id($doctor_in_charge_id_2);
                    
                    if ($employee_2 == "") {
                        $this->REST_Return(404, 'there is no employee_2 with id ' . $doctor_in_charge_id_2);
                        return;
                    }
                    
                    if (in_array($doctor_in_charge_id_2, $doctor_in_charge_exist) != 1) {
                        $doctor_in_charge_change[$doctor_in_charge_id_2] = $doctor_in_charge_exist[1];
                    }
                }

                if ($doctor_in_charge_id_3 != null) {
                    $employee_3 = $this->Employee->select_id($doctor_in_charge_id_3);
                    
                    if ($employee_3 == "") {
                        $this->REST_Return(404, 'there is no employee_3 with id ' . $doctor_in_charge_id_3);
                        return;
                    }

                    if (in_array($doctor_in_charge_id_3, $doctor_in_charge_exist) != 1) {
                        $doctor_in_charge_change[$doctor_in_charge_id_3] = $doctor_in_charge_exist[2];
                    }
                }

                if ($doctor_in_charge_id_4 != null) {
                    $employee_4 = $this->Employee->select_id($doctor_in_charge_id_4);
                    
                    if ($employee_4 == "") {
                        $this->REST_Return(404, 'there is no employee_4 with id ' . $doctor_in_charge_id_4);
                        return;
                    }

                    if (in_array($doctor_in_charge_id_4, $doctor_in_charge_exist) != 1) {
                        $doctor_in_charge_change[$doctor_in_charge_id_4] = $doctor_in_charge_exist[3];
                    }
                }

                if ($doctor_in_charge_id_5 != null) {
                    $employee_5 = $this->Employee->select_id($doctor_in_charge_id_5);
                    
                    if ($employee_5 == "") {
                        $this->REST_Return(404, 'there is no employee_5 with id ' . $doctor_in_charge_id_5);
                        return;
                    }

                    if (in_array($doctor_in_charge_id_5, $doctor_in_charge_exist) != 1) {
                        $doctor_in_charge_change[$doctor_in_charge_id_5] = $doctor_in_charge_exist[4];
                    }
                }

                foreach ($doctor_in_charge_change as $key => $val) {
                    $new_doctor_in_charge_id = $key;
                    $old_doctor_in_charge_id = $val;
                    
                    $select = 'a.id';
                    $where = array('a.soap_id' => $id, 'a.doctor_in_charge_id' => $old_doctor_in_charge_id);
                    $soap_p_change = $this->Soap_p->select(0, $select, $where);
                    
                    if (count($soap_p_change) > 0) {
                        $dt_soap_p_change = array(
                            'doctor_in_charge_id' => $new_doctor_in_charge_id,
                        );

                        $where = array('soap_id' => $id, 'doctor_in_charge_id' => $old_doctor_in_charge_id);
                        $this->Soap_p->update_v2(0, $dt_soap_p_change, $where);

                        $soap_p_change_id = $soap_p_change[0]->id;

                        $select = 'a.id';
                        $where = array('a.soap_p_id' => $soap_p_change_id, 'a.doctor_in_charge_id' => $old_doctor_in_charge_id);
                        $prescription_change = $this->Prescription->select(0, $select, $where);
                        if (count($prescription_change) > 0) {
                            $dt_prescription_change = array(
                                'doctor_in_charge_id' => $new_doctor_in_charge_id,
                            );
                            
                            $where = array('soap_p_id' => $soap_p_change_id, 'doctor_in_charge_id' => $old_doctor_in_charge_id);
                            $this->Prescription->update_v2(0, $dt_prescription_change, $where);
                        }
                    }

                    $select = 'a.id';
                    $where = array('a.soap_id' => $id, 'a.doctor_in_charge_id' => $old_doctor_in_charge_id);
                    $transaction_doctor_change = $this->Transaction_doctor->select(0, $select, $where);

                    if (count($transaction_doctor_change) > 0) {
                        $dt_transaction_doctor_change = array(
                            'doctor_in_charge_id' => $new_doctor_in_charge_id,
                        );
                        
                        $where = array('soap_id' => $id, 'doctor_in_charge_id' => $old_doctor_in_charge_id);
                        $this->Transaction_doctor->update_v2(0, $dt_transaction_doctor_change, $where);
                    }
                    
                    $select = 'a.id';
                    $where = array('a.soap_id' => $id, 'a.doctor_in_charge_id' => $old_doctor_in_charge_id);
                    $laboratory_request_change = $this->Laboratory_request->select(0, $select, $where);

                    if (count($laboratory_request_change) > 0) {
                        $dt_laboratory_request_change = array(
                            'doctor_in_charge_id' => $new_doctor_in_charge_id,
                        );

                        $where = array('soap_id' => $id, 'doctor_in_charge_id' => $old_doctor_in_charge_id);
                        $this->Laboratory_request->update_v2(0, $dt_laboratory_request_change, $where);
                    }

                    $select = 'a.id';
                    $where = array('a.soap_id' => $id, 'a.doctor_in_charge_id' => $old_doctor_in_charge_id);
                    $radiology_request_change = $this->Radiology_request->select(0, $select, $where);

                    if (count($radiology_request_change) > 0) {
                        $dt_radiology_request_change = array(
                            'doctor_in_charge_id' => $new_doctor_in_charge_id,
                        );

                        $where = array('soap_id' => $id, 'doctor_in_charge_id' => $old_doctor_in_charge_id);
                        $this->Radiology_request->update_v2(0, $dt_radiology_request_change, $where);
                    }
                }

                // REGISTRATION_DESTINATION
                $dt_registration_destination = array(
                    'doctor_in_charge_id_2' => $doctor_in_charge_id_1,
                    'doctor_in_charge_id_3' => $doctor_in_charge_id_2,
                    'doctor_in_charge_id_4' => $doctor_in_charge_id_3,
                    'doctor_in_charge_id_5' => $doctor_in_charge_id_4,
                    'doctor_in_charge_id_6' => $doctor_in_charge_id_5,
                    'user_updated'          => $user_id,
                    'date_updated'          => date('Y-m-d H:i:s'),
                );

                $this->Registration_destination->update_v2($registration_destination_id, $dt_registration_destination);

                // SOAP
                $dt_soap = array(
                    'id'                    => $id,
                    'doctor_in_charge_id_1' => $doctor_in_charge_id_1,
                    'doctor_in_charge_id_2' => $doctor_in_charge_id_2,
                    'doctor_in_charge_id_3' => $doctor_in_charge_id_3,
                    'doctor_in_charge_id_4' => $doctor_in_charge_id_4,
                    'doctor_in_charge_id_5' => $doctor_in_charge_id_5,
                    'type_consultation_doctor_2' => $type_consultation_doctor_2,
                    'type_consultation_doctor_3' => $type_consultation_doctor_3,
                    'type_consultation_doctor_4' => $type_consultation_doctor_4,
                    'type_consultation_doctor_5' => $type_consultation_doctor_5,
                    // 'advice'                => $advice,
                    'user_updated'          => $user_id,
                    'date_updated'          => date("Y-m-d H:i:s",time())
                );
                $this->$model->replace($dt_soap);

                // SOAP_S
                $this->Soap_s->whereFields['soap_id'] = $id;
                $soap_s = $this->Soap_s->read(0, false)[0]; 
                $dt_soap_s = array(
                    'id'                    => $soap_s->id,
                    'soap_id'               => $id,
                    'anamesa_type_id'       => isset($dt_put['anamesa_type_id']) ? $dt_put['anamesa_type_id'] : $soap_s->anamesa_type_id,
                    'family_name'           => isset($dt_put['family_name']) ? $dt_put['family_name'] : $soap_s->family_name,
                    'family_relation_id'    => isset($dt_put['family_relation_id']) ? $dt_put['family_relation_id'] : $soap_s->family_relation_id,
                    'main_complaint'        => isset($dt_put['main_complaint']) ? $dt_put['main_complaint'] : $soap_s->main_complaint,
                    'history_disease_now'   => isset($dt_put['history_disease_now']) ? $dt_put['history_disease_now'] : $soap_s->history_disease_now,
                    'history_disease_past'  => isset($dt_put['history_disease_past']) ? $dt_put['history_disease_past'] : $soap_s->history_disease_past,
                    'history_drugs'         => isset($dt_put['history_drugs']) ? $dt_put['history_drugs'] : $soap_s->history_drugs,
                    'history_allergy'       => isset($dt_put['history_allergy']) ? $dt_put['history_allergy'] : $soap_s->history_allergy,
                    'user_updated'          => $current_user,
                );
                $this->Soap_s->replace($dt_soap_s);

                // SOAP_O
                $this->Soap_o->whereFields['soap_id'] = $id;
                $soap_o = $this->Soap_o->read(0, false)[0]; 

                $dt_soap_o = array(
                    'id'                                        => $soap_o->id,
                    'soap_id'                                   => $id,
                    'physical_check'                            => isset($dt_put['physical_check']) ? $dt_put['physical_check'] : $soap_o->physical_check,
                    'anythingelse'                              => isset($dt_put['soap_o_anythingelse']) ? $dt_put['soap_o_anythingelse'] : $soap_o->anythingelse,
                    'aware'                                     => isset($dt_put['aware']) ? $dt_put['aware'] : $soap_o->aware,
                    'eye_score'                                 => isset($dt_put['eye_score']) ? $dt_put['eye_score'] : $soap_o->eye_score,
                    'verbal_score'                              => isset($dt_put['verbal_score']) ? $dt_put['verbal_score'] : $soap_o->verbal_score,
                    'motor_score'                               => isset($dt_put['motor_score']) ? $dt_put['motor_score'] : $soap_o->motor_score,
                    'pupil_score'                               => isset($dt_put['pupil_score']) ? $dt_put['pupil_score'] : $soap_o->pupil_score,
                    'verbal_response'                           => isset($dt_put['verbal_response']) ? $dt_put['verbal_response'] : $soap_o->verbal_response,
                    'oxygen_type_id'                            => isset($dt_put['oxygen_type_id']) ? $dt_put['oxygen_type_id'] : $soap_o->oxygen_type_id,
                    'systolic_blood_pressure'                   => isset($dt_put['systolic_blood_pressure']) ? $dt_put['systolic_blood_pressure'] : $soap_o->systolic_blood_pressure,
                    'diastolic_blood_pressure'                  => isset($dt_put['diastolic_blood_pressure']) ? $dt_put['diastolic_blood_pressure'] : $soap_o->diastolic_blood_pressure,
                    'classification_blood_pressure_type_id'     => isset($dt_put['classification_blood_pressure_type_id']) ? $dt_put['classification_blood_pressure_type_id'] : $soap_o->classification_blood_pressure_type_id,
                    'pulse_frequency'                           => isset($dt_put['pulse_frequency']) ? $dt_put['pulse_frequency'] : $soap_o->pulse_frequency,
                    'breath_frequency'                          => isset($dt_put['breath_frequency']) ? $dt_put['breath_frequency'] : $soap_o->breath_frequency,
                    'oximetry_frequency'                        => isset($dt_put['oximetry_frequency']) ? $dt_put['oximetry_frequency'] : $soap_o->oximetry_frequency,
                    'classification_oximetry_type_id'           => isset($dt_put['classification_oximetry_type_id']) ? $dt_put['classification_oximetry_type_id'] : $soap_o->classification_oximetry_type_id,
                    'temperature'                               => isset($dt_put['temperature']) ? $dt_put['temperature'] : $soap_o->temperature,
                    'classification_temperature_type_id'        => isset($dt_put['classification_temperature_type_id']) ? $dt_put['classification_temperature_type_id'] : $soap_o->classification_temperature_type_id,
                    'blood_sugar'                               => isset($dt_put['blood_sugar']) ? $dt_put['blood_sugar'] : $soap_o->blood_sugar,
                    'weight'                                    => isset($dt_put['weight']) ? $dt_put['weight'] : $soap_o->weight,
                    'classification_weight_type_id'             => isset($dt_put['classification_weight_type_id']) ? $dt_put['classification_weight_type_id'] : $soap_o->classification_weight_type_id,
                    'height'                                    => isset($dt_put['height']) ? $dt_put['height'] : $soap_o->height,
                    'imt'                                       => isset($dt_put['imt']) ? $dt_put['imt'] : $soap_o->imt,
                    'head_circumference'                        => isset($dt_put['head_circumference']) ? $dt_put['head_circumference'] : $soap_o->head_circumference,
                    'chest_circumference'                       => isset($dt_put['chest_circumference']) ? $dt_put['chest_circumference'] : $soap_o->chest_circumference,
                    'arm_circumference'                         => isset($dt_put['arm_circumference']) ? $dt_put['arm_circumference'] : $soap_o->arm_circumference,
                    'stomach_circumference'                     => isset($dt_put['stomach_circumference']) ? $dt_put['stomach_circumference'] : $soap_o->stomach_circumference,
                    'knee_circumference'                        => isset($dt_put['knee_circumference']) ? $dt_put['knee_circumference'] : $soap_o->knee_circumference,
                    'sore_score'                                => isset($dt_put['sore_score']) ? $dt_put['sore_score'] : $soap_o->sore_score,
                    'ews'                                       => isset($dt_put['ews']) ? $dt_put['ews'] : $soap_o->ews,
                    // NOTE - non_icd10 pakai yang soap_a
                    // 'non_icd10'                                 => isset($dt_put['non_icd10']) ? implode('|', $dt_put['non_icd10']) : $soap_o->non_icd10,
                    'head'                                      => isset($dt_put['head']) ? $dt_put['head'] : $soap_o->head,
                    'head_hair_color'                           => isset($dt_put['head_hair_color']) ? $dt_put['head_hair_color'] : $soap_o->head_hair_color,
                    'head_hair_condition'                       => isset($dt_put['head_hair_condition']) ? $dt_put['head_hair_condition'] : $soap_o->head_hair_condition,
                    'eye_conjunctive'                           => isset($dt_put['eye_conjunctive']) ? $dt_put['eye_conjunctive'] : $soap_o->eye_conjunctive,
                    'eye_sclera'                                => isset($dt_put['eye_sclera']) ? $dt_put['eye_sclera'] : $soap_o->eye_sclera,
                    'eye_pupil'                                 => isset($dt_put['eye_pupil']) ? $dt_put['eye_pupil'] : $soap_o->eye_pupil,
                    'eye_light_reflex'                          => isset($dt_put['eye_light_reflex']) ? $dt_put['eye_light_reflex'] : $soap_o->eye_light_reflex,
                    'eye_blind_color'                           => isset($dt_put['eye_blind_color']) ? $dt_put['eye_blind_color'] : $soap_o->eye_blind_color,
                    'mouth_mucosa'                              => isset($dt_put['mouth_mucosa']) ? $dt_put['mouth_mucosa'] : $soap_o->mouth_mucosa,
                    'mouth_discoloration'                       => isset($dt_put['mouth_discoloration']) ? $dt_put['mouth_discoloration'] : $soap_o->mouth_discoloration,
                    'mouth_tongue'                              => isset($dt_put['mouth_tongue']) ? $dt_put['mouth_tongue'] : $soap_o->mouth_tongue,
                    'mouth_caries'                              => isset($dt_put['mouth_caries']) ? $dt_put['mouth_caries'] : $soap_o->mouth_caries,
                    'neck_kgb'                                  => isset($dt_put['neck_kgb']) ? $dt_put['neck_kgb'] : $soap_o->neck_kgb,
                    'neck_trachea_position'                     => isset($dt_put['neck_trachea_position']) ? $dt_put['neck_trachea_position'] : $soap_o->neck_trachea_position,
                    'neck_massa'                                => isset($dt_put['neck_massa']) ? $dt_put['neck_massa'] : $soap_o->neck_massa,
                    'neck_jvp'                                  => isset($dt_put['neck_jvp']) ? $dt_put['neck_jvp'] : $soap_o->neck_jvp,
                    'thorax_insp_chest'                         => isset($dt_put['thorax_insp_chest']) ? $dt_put['thorax_insp_chest'] : $soap_o->thorax_insp_chest,
                    'thorax_insp_retraction'                    => isset($dt_put['thorax_insp_retraction']) ? $dt_put['thorax_insp_retraction'] : $soap_o->thorax_insp_retraction,
                    'thorax_palp_chest'                         => isset($dt_put['thorax_palp_chest']) ? $dt_put['thorax_palp_chest'] : $soap_o->thorax_palp_chest,
                    'thorax_palp_fremitus'                      => isset($dt_put['thorax_palp_fremitus']) ? $dt_put['thorax_palp_fremitus'] : $soap_o->thorax_palp_fremitus,
                    'thorax_palp_crepitation'                   => isset($dt_put['thorax_palp_crepitation']) ? $dt_put['thorax_palp_crepitation'] : $soap_o->thorax_palp_crepitation,
                    'thorax_ausk_pul_vasiculer'                 => isset($dt_put['thorax_ausk_pul_vasiculer']) ? $dt_put['thorax_ausk_pul_vasiculer'] : $soap_o->thorax_ausk_pul_vasiculer,
                    'thorax_ausk_pul_rhonki'                    => isset($dt_put['thorax_ausk_pul_rhonki']) ? $dt_put['thorax_ausk_pul_rhonki'] : $soap_o->thorax_ausk_pul_rhonki,
                    'thorax_ausk_pul_wheezing'                  => isset($dt_put['thorax_ausk_pul_wheezing']) ? $dt_put['thorax_ausk_pul_wheezing'] : $soap_o->thorax_ausk_pul_wheezing,
                    'thorax_ausk_cor_heart_sound'               => isset($dt_put['thorax_ausk_cor_heart_sound']) ? $dt_put['thorax_ausk_cor_heart_sound'] : $soap_o->thorax_ausk_cor_heart_sound,
                    'thorax_ausk_cor_murmur'                    => isset($dt_put['thorax_ausk_cor_murmur']) ? $dt_put['thorax_ausk_cor_murmur'] : $soap_o->thorax_ausk_cor_murmur,
                    'thorax_ausk_cor_gallop'                    => isset($dt_put['thorax_ausk_cor_gallop']) ? $dt_put['thorax_ausk_cor_gallop'] : $soap_o->thorax_ausk_cor_gallop,
                    'abdomen_supel'                             => isset($dt_put['abdomen_supel']) ? $dt_put['abdomen_supel'] : $soap_o->abdomen_supel,
                    'abdomen_bowel_sounds'                      => isset($dt_put['abdomen_bowel_sounds']) ? $dt_put['abdomen_bowel_sounds'] : $soap_o->abdomen_bowel_sounds,
                    'abdomen_tenderness'                        => isset($dt_put['abdomen_tenderness']) ? $dt_put['abdomen_tenderness'] : $soap_o->abdomen_tenderness,
                    'abdomen_hepar_lien'                        => isset($dt_put['abdomen_hepar_lien']) ? $dt_put['abdomen_hepar_lien'] : $soap_o->abdomen_hepar_lien,
                    'abdomen_other_physical_check'              => isset($dt_put['abdomen_other_physical_check']) ? $dt_put['abdomen_other_physical_check'] : $soap_o->abdomen_other_physical_check,
                    'extremity_acral'                           => isset($dt_put['extremity_acral']) ? $dt_put['extremity_acral'] : $soap_o->extremity_acral,
                    'extremity_crt'                             => isset($dt_put['extremity_crt']) ? $dt_put['extremity_crt'] : $soap_o->extremity_crt,
                    'extremity_edema'                           => isset($dt_put['extremity_edema']) ? $dt_put['extremity_edema'] : $soap_o->extremity_edema,
                    'ophthal_status'                            => isset($dt_put['ophthal_status']) ? $dt_put['ophthal_status'] : $soap_o->ophthal_status,
                    'neurologist_status'                        => isset($dt_put['neurologist_status']) ? $dt_put['neurologist_status'] : $soap_o->neurologist_status,
                    'uub_type_id'                               => isset($dt_put['uub_type_id']) ? $dt_put['uub_type_id'] : $soap_o->uub_type_id,
                    'eye_anemic_conjunctiva'                    => isset($dt_put['eye_anemic_conjunctiva']) ? $dt_put['eye_anemic_conjunctiva'] : $soap_o->eye_anemic_conjunctiva,
                    'eye_interic_sclera'                        => isset($dt_put['eye_interic_sclera']) ? $dt_put['eye_interic_sclera'] : $soap_o->eye_interic_sclera,
                    'eye_anythingelse'                          => isset($dt_put['eye_anythingelse']) ? $dt_put['eye_anythingelse'] : $soap_o->eye_anythingelse,
                    'tht_hyperemic_pharynx'                     => isset($dt_put['tht_hyperemic_pharynx']) ? $dt_put['tht_hyperemic_pharynx'] : $soap_o->tht_hyperemic_pharynx,
                    'tht_tonsil'                                => isset($dt_put['tht_tonsil']) ? $dt_put['tht_tonsil'] : $soap_o->tht_tonsil,
                    'tht_ear'                                   => isset($dt_put['tht_ear']) ? $dt_put['tht_ear'] : $soap_o->tht_ear,
                    'tht_anythingelse'                          => isset($dt_put['tht_anythingelse']) ? $dt_put['tht_anythingelse'] : $soap_o->tht_anythingelse,
                    'neck_lymph_gland'                          => isset($dt_put['neck_lymph_gland']) ? $dt_put['neck_lymph_gland'] : $soap_o->neck_lymph_gland,
                    'thorax_heart_type_id'                      => isset($dt_put['thorax_heart_type_id']) ? $dt_put['thorax_heart_type_id'] : $soap_o->thorax_heart_type_id,
                    'thorax_murmur'                             => isset($dt_put['thorax_murmur']) ? $dt_put['thorax_murmur'] : $soap_o->thorax_murmur,
                    'thorax_gallop'                             => isset($dt_put['thorax_gallop']) ? $dt_put['thorax_gallop'] : $soap_o->thorax_gallop,
                    'thorax_anythingelse'                       => isset($dt_put['thorax_anythingelse']) ? $dt_put['thorax_anythingelse'] : $soap_o->thorax_anythingelse,
                    'pulmo_arry'                                => isset($dt_put['pulmo_arry']) ? implode(',', $dt_put['pulmo_arry']) : $soap_o->pulmo_arry,
                    'pulmo_anythingelse'                        => isset($dt_put['pulmo_anythingelse']) ? $dt_put['pulmo_anythingelse'] : $soap_o->pulmo_anythingelse,
                    'pulmo_breath_movement'                     => isset($dt_put['pulmo_breath_movement']) ? $dt_put['pulmo_breath_movement'] : $soap_o->pulmo_breath_movement,
                    'pulmo_breath_sound'                        => isset($dt_put['pulmo_breath_sound']) ? $dt_put['pulmo_breath_sound'] : $soap_o->pulmo_breath_sound,
                    'extremity_acral_type_id'                   => isset($dt_put['extremity_acral_type_id']) ? $dt_put['extremity_acral_type_id'] : $soap_o->extremity_acral_type_id,
                    'extremity_capillary_refilling_time'        => isset($dt_put['extremity_capillary_refilling_time']) ? $dt_put['extremity_capillary_refilling_time'] : $soap_o->extremity_capillary_refilling_time,
                    'extremity_anythingelse'                    => isset($dt_put['extremity_anythingelse']) ? $dt_put['extremity_anythingelse'] : $soap_o->extremity_anythingelse,
                    'abdomen_inspection_type_id'                => isset($dt_put['abdomen_inspection_type_id']) ? $dt_put['abdomen_inspection_type_id'] : $soap_o->abdomen_inspection_type_id,
                    'abdomen_palpation_type_id'                 => isset($dt_put['abdomen_palpation_type_id']) ? $dt_put['abdomen_palpation_type_id'] : $soap_o->abdomen_palpation_type_id,
                    'abdomen_sore_press'                        => isset($dt_put['abdomen_sore_press']) ? $dt_put['abdomen_sore_press'] : $soap_o->abdomen_sore_press,
                    'abdomen_sore_press_location'               => isset($dt_put['abdomen_sore_press_location']) ? $dt_put['abdomen_sore_press_location'] : $soap_o->abdomen_sore_press_location,
                    'abdomen_hepar'                             => isset($dt_put['abdomen_hepar']) ? $dt_put['abdomen_hepar'] : $soap_o->abdomen_hepar,
                    'abdomen_bu'                                => isset($dt_put['abdomen_bu']) ? $dt_put['abdomen_bu'] : $soap_o->abdomen_bu,
                    'localist_status'                           => isset($dt_put['localist_status']) ? $dt_put['localist_status'] : $soap_o->localist_status,
                    'abdomen_lien'                              => isset($dt_put['abdomen_lien']) ? $dt_put['abdomen_lien'] : $soap_o->abdomen_lien,
                    'genitalia'                                 => isset($dt_put['genitalia']) ? $dt_put['genitalia'] : $soap_o->genitalia,
                    'user_updated'                              => $current_user,
                );
                $this->Soap_o->replace($dt_soap_o);

                // SOAP_O_ACTION
                $this->Soap_o_action->whereFields['soap_o_id'] = $soap_o->id;
                $soap_o_action = $this->Soap_o_action->read(0, false)[0]; 
                $dt_soap_o_action = array(
                    'id'                        => $soap_o_action->id,
                    'soap_o_id'                 => $soap_o->id,
                    'canvasing_type_id'         => isset($dt_put['canvasing_type_id']) ? $dt_put['canvasing_type_id'] : $soap_o_action->canvasing_type_id,
                    'canvasing_text'            => isset($dt_put['canvasing_text']) ? $dt_put['canvasing_text'] : $soap_o_action->canvasing_text,
                    'attr1'                     => isset($dt_put['attr1']) ? $dt_put['attr1'] : $soap_o_action->attr1,
                    'attr2'                     => isset($dt_put['attr2']) ? $dt_put['attr2'] : $soap_o_action->attr2
                );
                $this->Soap_o_action->replace($dt_soap_o_action);

                // INPUT TTV
                $aware = $dt_put['aware'];
                $eye_score = $dt_put['eye_score'];
                $verbal_score = $dt_put['verbal_score'];
                $motor_score = $dt_put['motor_score'];
                $skin_color = $dt_put['skin_color'];
                $behavior = $dt_put['behavior'];
                $motor_score = $dt_put['motor_score'];
                $oxygen_type_id = $dt_put['oxygen_type_id'];
                $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
                $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
                $pulse_frequency = $dt_put['pulse_frequency'];
                $breath_frequency = $dt_put['breath_frequency'];
                $oximetry_frequency = $dt_put['oximetry_frequency'];
                $temperature = $dt_put['temperature'];
                $weight = $dt_put['weight'];
                $height = $dt_put['height'];

                $dt_vital_signs_monitoring = array(
                    'registration_destination_id'   => $registration_destination_id,
                    'employee_nurse_id'             => $current_user,
                    'source_id'                     => $soap_id,
                    'observation_date'              => date('Y-m-d H:i:s'),
                    'aware'                         => $aware,
                    'eye_score'                     => $eye_score,
                    'verbal_score'                  => $verbal_score,
                    'motor_score'                   => $motor_score,
                    'systolic'                      => $systolic_blood_pressure,
                    'diastolic'                     => $diastolic_blood_pressure,
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

                $select = 'a.id';
                $where = array(
                    'a.source_id' => $soap_id,
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
                        'employee_nurse_id'      => $current_user,
                        'source_id'              => $soap_id,
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
                        'a.source_id' => $soap_id,
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
                        'source_id'         => $soap_id,
                        'employee_nurse_id' => $current_user,
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
                        'employee_nurse_id'           => $current_user,
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
                    'source_id'             => $soap_id,
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
                    'a.source_id' => $soap_id,
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

                // SOAP_A
                $soap_a = $this->db->query("
                    SELECT a.id
                    FROM soap_a as a
                    WHERE soap_id = '$id'
                    AND (a.cppt_id IS NULL OR a.cppt_id = '')
                    ORDER BY a.id ASC
                ")->result()[0]; // NOTE -- untuk bug update soap_a punya cppt
                $dt_soap_a = array(
                    'id'                    => $soap_a->id,
                    'soap_id'               => $id,
                    // 'development_notes'     => isset($dt_put['development_notes']) ? $dt_put['development_notes'] : $soap_a->development_notes,
                    // 'anythingelse'          => isset($dt_put['soap_a_anythingelse']) ? $dt_put['soap_a_anythingelse'] : $soap_a->anythingelse,
                    'icd10_notes'           => isset($dt_put['icd10_notes']) ? $dt_put['icd10_notes'] : $soap_a->icd10_notes,
                    'non_icd10'             => isset($dt_put['non_icd10']) ? implode('|', $dt_put['non_icd10']) : $soap_a->non_icd10,
                    // 'obgyn_diag_date'       => isset($dt_put['obgyn_diag_date']) ? $dt_put['obgyn_diag_date'] : $soap_a->obgyn_diag_date,                   
                    // 'obgyn_diag_clinis'     => isset($dt_put['obgyn_diag_clinis']) ? $dt_put['obgyn_diag_clinis'] : $soap_a->obgyn_diag_clinis,                 
                    // 'obgyn_diag_hpht'       => isset($dt_put['obgyn_diag_hpht']) ? $dt_put['obgyn_diag_hpht'] : $soap_a->obgyn_diag_hpht,                   
                    // 'obgyn_s_d'             => isset($dt_put['obgyn_s_d']) ? $dt_put['obgyn_s_d'] : $soap_a->obgyn_s_d,                 
                    // 'obgyn_j_exam'          => isset($dt_put['obgyn_j_exam']) ? $dt_put['obgyn_j_exam'] : $soap_a->obgyn_j_exam,                  
                    // 'obgyn_j_pos'           => isset($dt_put['obgyn_j_pos']) ? $dt_put['obgyn_j_pos'] : $soap_a->obgyn_j_pos,                   
                    // 'obgyn_j_plas'          => isset($dt_put['obgyn_j_plas']) ? $dt_put['obgyn_j_plas'] : $soap_a->obgyn_j_plas,                  
                    // 'obgyn_j_k'             => isset($dt_put['obgyn_j_k']) ? $dt_put['obgyn_j_k'] : $soap_a->obgyn_j_k,                 
                    // 'obgyn_j_sex'           => isset($dt_put['obgyn_j_sex']) ? $dt_put['obgyn_j_sex'] : $soap_a->obgyn_j_sex,                   
                    // 'obgyn_j_weight'        => isset($dt_put['obgyn_j_weight']) ? $dt_put['obgyn_j_weight'] : $soap_a->obgyn_j_weight,                    
                    // 'obgyn_size_usg'        => isset($dt_put['obgyn_size_usg']) ? $dt_put['obgyn_size_usg'] : $soap_a->obgyn_size_usg,                    
                    // 'obgyn_usg_desc'        => isset($dt_put['obgyn_usg_desc']) ? $dt_put['obgyn_usg_desc'] : $soap_a->obgyn_usg_desc,                    
                    // 'obgyn_usg_file'        => isset($dt_put['obgyn_usg_file']) ? $this->base64ToFile($dt_put['obgyn_usg_file'], $id) : $soap_a->obgyn_usg_file,
                    'date_updated'            => date("Y-m-d H:i:s"),
                    'user_updated'          => $current_user,
                );
                $this->Soap_a->replace($dt_soap_a);

                // SOAP_A_ACTION
                if(isset($dt_put['icd10'])) {
                    $soap_a_id = $soap_a->id;

                    $select = "a.id, icd10_id";
                    $where = array('soap_a_id' => $soap_a_id);
                    $soap_a_action = $this->Soap_a_action->select(0, $select, $where);

                    $icd10_arr = $dt_put['icd10'];
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
                            'user_added'        => $current_user,
                            'user_updated'      => $current_user,
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
                            'user_updated'      => $current_user,
                            'user_deleted'      => $current_user,
                        );
                        $this->Soap_a_action->replace($dt_soap_a_action);
                    }
                }

                // SOAP_P
                $therapy_plan = $dt_put['therapy_plan'];
                // $estimated_duration_treatment = $dt_put['estimated_duration_treatment'];
                // $follow_up_care_arry = implode(',', $dt_put['follow_up_care_arry']);
                // $diet_and_nutrition_type_id = $dt_put['diet_and_nutrition_type_id'];
                // $diet_and_nutrition = $dt_put['diet_and_nutrition'];
                // $wound_care_type_id = $dt_put['wound_care_type_id'];
                // $wound_care = $dt_put['wound_care'];
                // $medical_rehabilitation_type_id = $dt_put['medical_rehabilitation_type_id'];
                // $medical_rehabilitation = $dt_put['medical_rehabilitation'];
                // $pain_management_type_id = $dt_put['pain_management_type_id'];
                // $pain_management = $dt_put['pain_management'];
                // $discharge_planning_other = $dt_put['discharge_planning_other'];
                
                $this->Soap_p->whereFields['soap_id'] = $id;
                $this->Soap_p->whereFields['doctor_in_charge_id'] = $doctor_in_charge_id; // NOTE - multiple soap_p emergency specialist
                $soap_p = $this->Soap_p->read(0, false); 

                if (count($soap_p) == 0) {
                    $select = 'a.id';
                    $where = array('soap_id' => $id);
                    $soap_p = $this->Soap_p->select(0, $select, $where); 
                }
    
                $soap_p_id = $soap_p[0]->id;

                $dt_soap_p = array(
                    'id'                             => $soap_p_id,
                    'soap_id'                        => $id,
                    'doctor_in_charge_id'            => $doctor_in_charge_id,
                    'therapy_plan'                   => $therapy_plan,
                    // 'estimated_duration_treatment'   => $estimated_duration_treatment,
                    // 'follow_up_care_arry'            => $follow_up_care_arry,
                    // 'diet_and_nutrition_type_id'     => $diet_and_nutrition_type_id,
                    // 'diet_and_nutrition'             => $diet_and_nutrition,
                    // 'wound_care_type_id'             => $wound_care_type_id,
                    // 'wound_care'                     => $wound_care,
                    // 'medical_rehabilitation_type_id' => $medical_rehabilitation_type_id,
                    // 'medical_rehabilitation'         => $medical_rehabilitation,
                    // 'pain_management_type_id'        => $pain_management_type_id,
                    // 'pain_management'                => $pain_management,
                    // 'discharge_planning_other'       => $discharge_planning_other,
                );
                $this->Soap_p->replace($dt_soap_p);

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

                        if ($icd9cm_id == null) {
                            $this->REST_Return(400, 'icd9cm_id in icd9cm is required');
                            return;
                        }
                        
                        $select = 'a.id';
                        $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);
                        if(count($icd9cm) == 0) {
                            $this->REST_Return(404, 'There is no icd9cm with id ' . $icd9cm_id);
                            return;
                        }

                        $dt_soap_p_action = array(
                            'soap_p_id'         => $soap_p_id,
                            'icd9cm_id'         => $icd9cm_id,
                            'is_delete'         => 0,
                            'user_added'        => $current_user,
                            'user_updated'      => $current_user,
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
                            'user_updated'      => $current_user,
                            'user_deleted'      => $current_user,
                        );
                        $this->Soap_p_action->replace($dt_soap_p_action);
                    }
                }

                // PATIENT_ALLERGY
                // $allergies_arr = $dt_put['allergies'];

                // $select = 'a.norm, a.allergy_id';
                // $where = array('norm' => $norm);
                // $patient_allergy = $this->Patient_allergy->select(0, $select, $where);

                // for ($j=0; $j < count($patient_allergy); $j++) { 
                //     $dt_patient_allergy = array(
                //         'is_delete' => 1,
                //     );

                //     $this->Patient_allergy->update($norm, $dt_patient_allergy);
                // }

                // for ($i=0; $i < count($allergies_arr); $i++) {
                //     $allergy_name = $allergies_arr[$i]['name'];
                //     $allergy_type_allergy_id = $allergies_arr[$i]['type_allergy_id'];
                //     $allergy_date_tragedy = $allergies_arr[$i]['date_tragedy'];
                //     $allergy_side_effect = $allergies_arr[$i]['side_effect'];
                //     $allergy_reaction = $allergies_arr[$i]['reaction'];
                //     $allergy_severity = $allergies_arr[$i]['severity'];

                //     $dt_patient_allergy = array(
                //         'norm'              => $norm,
                //         'type_allergy_id'   => $allergy_type_allergy_id,
                //         'name'              => $allergy_name,
                //         'date_tragedy'      => $allergy_date_tragedy,
                //         'side_effect'       => $allergy_side_effect,
                //         'reaction'          => $allergy_reaction,
                //         'severity'          => $allergy_severity,
                //         'is_delete'         => 0,
                //     );

                //     $select = 'a.id';
                //     $where = array('name' => $allergy_name);
                //     $allergy = $this->Allergy->select(0, $select, $where);

                //     if(count($allergy) > 0) {
                //         $allergy_id = $allergy[0]->id;
                //     } else {
                //         $dt_allergy = array(
                //             'institute_branch_id'   => $institute_branch_id,
                //             'name'                  => $allergy_name,
                //         );

                //         $allergy_id = $this->Allergy->create($dt_allergy);    
                //     }

                //     $dt_patient_allergy['allergy_id'] = $allergy_id;

                //     $this->Patient_allergy->replace($dt_patient_allergy);
                // }

                // TRANSACTION_DOCTOR
                if(isset($dt_put['transaction_doctor'])) {
                    $this->Transaction_doctor->whereFields['soap_id'] = $id;
                    $transaction_doctor = $this->Transaction_doctor->read(0,false);
                    $costing_arr = $dt_put['transaction_doctor'];
                    $tmp_delete_costing_arr = array();
                    // adding all current items (db) to temporary variable
                    for ($i=0; $i < count($transaction_doctor); $i++) { 
                        $tmp_delete_costing_arr[$transaction_doctor[$i]->costing_id] = $transaction_doctor[$i]->id;
                    }
                    for ($i=0; $i < count($dt_put['transaction_doctor']); $i++) { 
                        for ($i=0; $i < count($costing_arr) ; $i++) { 
                            $costing_id = $costing_arr[$i]['costing_id'];
                            if(is_exist('Costing_services_class', $costing_id) == []) {
                                $this->REST_Return(404, 'There is no costing_services_class with id ' . $costing_id);
                                return;
                            }
                            
                            $costing = $this->Costing_services_class->read($costing_id,false)[0];
                            $dt_transaction_doctor = array(
                                'soap_id'               => $id,
                                'doctor_in_charge_id'   => $doctor_in_charge_id,
                                'costing_id'            => $dt_put['transaction_doctor'][$i]['costing_id'],
                                'costing_name'          => $costing->name,
                                'var_1'                 => $dt_put['transaction_doctor'][$i]['var_1'],
                                'var_2'                 => $dt_put['transaction_doctor'][$i]['var_2'],
                                'var_3'                 => $dt_put['transaction_doctor'][$i]['var_1']*$dt_put['transaction_doctor'][$i]['var_2'],
                                'doctor_ref_id'         => $dt_put['transaction_doctor'][$i]['doctor_ref_id'],
                                'notes'                 => $dt_put['transaction_doctor'][$i]['notes'],
                                'is_delete'             => 0,
                                'date_added'            => date("Y-m-d H:i:s",time())
                            );
    
                            // create or update items according to the items provided and unset used items from temporary variable
                            if(array_key_exists($costing_id,$tmp_delete_costing_arr) == 1) {
                                $dt_transaction_doctor['id'] = $tmp_delete_costing_arr[$costing_id];
                                unset($dt_transaction_doctor['date_added']);
                                $this->Transaction_doctor->replace($dt_transaction_doctor);
    
                                unset($tmp_delete_costing_arr[$costing_id]);
                            } else {
                                $this->Transaction_doctor->create($dt_transaction_doctor);
                            }
    
                        }
    
                        // delete all items in temporary variable
                        foreach ($tmp_delete_costing_arr as $costing_id => $transaction_doctor_id) {
                            $dt_transaction_doctor = array(
                                'id'                => $transaction_doctor_id,
                                'costing_id'        => $costing_id,
                                'is_delete'         => 1,
                            );
                            $this->Transaction_doctor->replace($dt_transaction_doctor);
                        }
                    }
                }

                // TRANSACTION_PHYSIOTHERAPY
                // if(isset($dt_put['transaction_physiotherapy'])) {
                //     $this->Transaction_doctor->whereFields['soap_id'] = $id;
                //     $transaction_physiotherapy = $this->Transaction_physiotherapy->read(0,false);
                //     $costing_arr = $dt_put['transaction_physiotherapy'];
                //     $tmp_delete_costing_arr = array();
                //     // adding all current items (db) to temporary variable
                //     for ($i=0; $i < count($transaction_physiotherapy); $i++) { 
                //         $tmp_delete_costing_arr[$transaction_physiotherapy[$i]->costing_id] = $transaction_physiotherapy[$i]->id;
                //     }
                //     for ($i=0; $i < count($dt_put['transaction_physiotherapy']); $i++) { 
                //         for ($i=0; $i < count($costing_arr) ; $i++) { 
                //             $costing_id = $costing_arr[$i]['costing_id'];
                //             if(is_exist('Costing_services_class', $costing_id) == []) {
                //                 $this->REST_Return(404, 'There is no costing_services_class with id ' . $costing_id);
                //                 return;
                //             }
                            
                //             $costing = $this->Costing_services_class->read($costing_id,false)[0];
                //             $dt_transaction_physiotherapy = array(
                //                 'soap_id'               => $id,
                //                 'costing_id'            => $dt_put['transaction_physiotherapy'][$i]['costing_id'],
                //                 'costing_name'          => $costing->name,
                //                 'var_1'                 => $dt_put['transaction_physiotherapy'][$i]['var_1'],
                //                 'var_2'                 => $dt_put['transaction_physiotherapy'][$i]['var_2'],
                //                 'var_3'                 => $dt_put['transaction_physiotherapy'][$i]['var_1'],
                //                 'notes'                 => $dt_put['transaction_physiotherapy'][$i]['notes'],
                //                 'is_delete'             => 0,
                //                 'date_added'            => date("Y-m-d H:i:s",time())
                //             );
    
                //             // create or update items according to the items provided and unset used items from temporary variable
                //             if(array_key_exists($costing_id,$tmp_delete_costing_arr) == 1) {
                //                 $dt_transaction_physiotherapy['id'] = $tmp_delete_costing_arr[$costing_id];
                //                 unset($dt_transaction_physiotherapy['date_added']);
                //                 $this->Transaction_physiotherapy->replace($dt_transaction_physiotherapy);
    
                //                 unset($tmp_delete_costing_arr[$costing_id]);
                //             } else {
                //                 $this->Transaction_physiotherapy->create($dt_transaction_physiotherapy);
                //             }
    
                //         }
                        
                //         // delete all items in temporary variable
                //         foreach ($tmp_delete_costing_arr as $costing_id => $transaction_physiotherapy_id) {
                //             $dt_transaction_physiotherapy = array(
                //                 'id'                => $transaction_physiotherapy_id,
                //                 'costing_id'        => $costing_id,
                //                 'is_delete'         => 1,
                //             );
                //             $this->Transaction_physiotherapy->replace($dt_transaction_physiotherapy);
                //         }
                //     }
                // }

                // LABORATORY_REQUEST
                if(isset($dt_put['laboratory_request_items'])) {
                    $where = array('soap_id' => $id);
                    $laboratory_request_id = $this->Laboratory_request->select_id(0, $where);

                    // Create parent lab
                    if($laboratory_request_id == "" && count($dt_put['laboratory_request_items']) > 0) {
                        // // Count no_request
                        // $this->Laboratory_request->whereFields['a.is_delete'] = 0;
                        // $this->Laboratory_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Laboratory_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Laboratory_request->orderFields = array('a.no_request' => 'DESC');
                        // $this->Laboratory_request->limit = 1;
                        // $tmp = $this->Laboratory_request->read(0,false);
                        // $waktu = date("Ymd",time());
                        // if($tmp != []) {
                        //     $no_request = 'LAB' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $no_request = 'LAB' . $waktu . '0001';
                        //     }
                        // } else {
                        //     $no_request = 'LAB' . $waktu . '0001';
                        // }

                        // // Count queue
                        // $this->Laboratory_request->whereFields['a.is_delete'] = 0;
                        // $this->Laboratory_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Laboratory_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Laboratory_request->orderFields = array('a.queue' => 'DESC');
                        // $this->Laboratory_request->limit = 1;
                        // $tmp = $this->Laboratory_request->read(0,false);
                        // if($tmp != []) {
                        //     $queue = $tmp[0]->queue + 1;
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $queue = 1;
                        //     }
                        // } else {
                        //     $queue = 1;
                        // }

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
                            'soap_id'               => $id,
                            'doctor_in_charge_id'   => $doctor_in_charge_id,
                            'no_request'            => $no_request,
                            'queue'                 => $queue,
                            'date_request'          => date('Y-m-d H:i:s',time())
                        );
                        $id_laboratory_request = $this->Laboratory_request->create($dt_laboratory_request);

                        $laboratory_request_id = $this->Laboratory_request->select_id($id_laboratory_request);
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
                    $where = array('soap_id' => $id);
                    $radiology_request_id = $this->Radiology_request->select_id(0, $where);

                    // Create parent rad
                    if($radiology_request_id == "" && count($dt_put['radiology_request_items']) > 0) {
                        // Count no_request
                        // $this->Radiology_request->whereFields['a.is_delete'] = 0;
                        // $this->Radiology_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Radiology_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Radiology_request->orderFields = array('a.no_request' => 'DESC');
                        // $this->Radiology_request->limit = 1;
                        // $tmp = $this->Radiology_request->read(0,false);
                        // $waktu = date("Ymd",time());
                        // if($tmp != []) {
                        //     $no_request = 'RAD' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $no_request = 'RAD' . $waktu . '0001';
                        //     }
                        // } else {
                        //     $no_request = 'RAD' . $waktu . '0001';
                        // }

                        // // Count queue
                        // $this->Radiology_request->whereFields['a.is_delete'] = 0;
                        // $this->Radiology_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Radiology_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Radiology_request->orderFields = array('a.queue' => 'DESC');
                        // $this->Radiology_request->limit = 1;
                        // $tmp = $this->Radiology_request->read(0,false);
                        // if($tmp != []) {
                        //     $queue = $tmp[0]->queue + 1;
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $queue = 1;
                        //     }
                        // } else {
                        //     $queue = 1;
                        // }

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
                            'soap_id'               => $id,
                            'doctor_in_charge_id'   => $doctor_in_charge_id,
                            'no_request'            => $no_request,
                            'queue'                 => $queue,
                            'date_request'          => date('Y-m-d H:i:s',time())
                        );
                        $id_radiology_request = $this->Radiology_request->create($dt_radiology_request);

                        $radiology_request_id = $this->Radiology_request->select_id($id_radiology_request,false);
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
                    $where = array('soap_id' => $id);
                    $physiotherapy_request_id = $this->Physiotherapy_request->select_id(0, $where);

                    // Create parent lab
                    if($physiotherapy_request_id == "" && count($dt_put['physiotherapy_request_items']) > 0) {
                        // NOTE - DEPRECATED, DELETED SOON!
                        // // Count no_request
                        // $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
                        // $this->Physiotherapy_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Physiotherapy_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Physiotherapy_request->orderFields = array('a.no_request' => 'DESC');
                        // $this->Physiotherapy_request->limit = 1;
                        // $tmp = $this->Physiotherapy_request->read(0,false);
                        // $waktu = date("Ymd",time());
                        // if($tmp != []) {
                        //     $no_request = 'RHM' . $waktu . str_pad(intval(substr($tmp[0]->no_request,11)) + 1, 4, '0', STR_PAD_LEFT);
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $no_request = 'RHM' . $waktu . '0001';
                        //     }
                        // } else {
                        //     $no_request = 'RHM' . $waktu . '0001';
                        // }

                        // // Count queue
                        // $this->Physiotherapy_request->whereFields['a.is_delete'] = 0;
                        // $this->Physiotherapy_request->whereFields['a.date_request >='] = date('Y-m-d').' 00:00:00';
                        // $this->Physiotherapy_request->whereFields['a.date_request <='] = date('Y-m-d').' 23:59:59';
                        // $this->Physiotherapy_request->orderFields = array('a.queue' => 'DESC');
                        // $this->Physiotherapy_request->limit = 1;
                        // $tmp = $this->Physiotherapy_request->read(0,false);
                        // if($tmp != []) {
                        //     $queue = $tmp[0]->queue + 1;
                        //     // Check if reset is needed
                        //     if(substr($tmp[0]->no_request,3,8) != date('Ymd',time())) {
                        //         $queue = 1;
                        //     }
                        // } else {
                        //     $queue = 1;
                        // }

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
                            'soap_id'               => $id,
                            'doctor_in_charge_id'   => $doctor_in_charge_id,
                            'no_request'            => $no_request,
                            'queue'                 => $queue,
                            'date_request'          => date('Y-m-d H:i:s',time())
                        );
                        $id_physiotherapy_request = $this->Physiotherapy_request->create($dt_physiotherapy_request);

                        $physiotherapy_request_id = $this->Physiotherapy_request->select_id($id_physiotherapy_request, false);
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
                                        'physiotherapy_request_id'  => $physiotherapy_request_item[$i]->physiotherapy_request_id,
                                        'physiotherapy_package_id'  => $physiotherapy_request_item[$i]->physiotherapy_package_id,
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
                                    'physiotherapy_request_id'  => $physiotherapy_request_id,
                                    'physiotherapy_package_id'  => $package_id,
                                    'service_class_id'          => $service_class[0]->id,
                                    'var_1'                     => $costing_services_class[0]->var_1,
                                    'var_2'                     => $costing_services_class[0]->var_2,
                                    'var_3'                     => $costing_services_class[0]->var_3,
                                    'costing_id'                => $costing[0]->id,
                                    'costing_name'              => $costing[0]->name,
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
                                        'physiotherapy_request_id'  => $physiotherapy_request_id,
                                        'physiotherapy_package_id'  => $package_id,
                                        'is_delete'                 => 1,
                                    );
                                    $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
                                }
                            }
                        }
                    }
                }

                // TODO - HAPUS YANG DI COMMENT KALAU LANCAR
                // PRESCRIPTION_ITEM
                if(isset($dt_put['form_prescription'])) {
                    $select_prescription_processed = 'a.id, a.flag, a.no_order';
                    $where_prescription_processed = array('a.soap_p_id' => $soap_p_id,'a.flag' => 1);
                    $pharmacy_prescription_processed = $this->Prescription->select(0, $select_prescription_processed, $where_prescription_processed);
                    if (count($dt_put['form_prescription']) > 0 && count($pharmacy_prescription_processed) == 0) {
                        $select_prescription = 'a.id, a.flag, a.no_order';
                        $where_prescription = array('a.soap_p_id' => $soap_p_id);
                        $pharmacy_prescription = $this->Prescription->select(0, $select_prescription, $where_prescription);
                        if(count($pharmacy_prescription) == 0) {
                            $this->Registration->limit = 1;
                            $this->Registration->whereFields['b.id'] = $dt_put['registration_destination_id'];
                            $dt_reg = $this->Registration->read(0,false)[0];
    
                            $health_facility_id = $dt_reg->health_facility_id;
                            $depo_id = $dt_put['pharmacy_depo_id'];
                            // count no_order & queue
                            if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
                                $prefix = 'PRJ';
                                $depo_id = '972dcaba-cea0-4515-9756-75ea675c6322';
                            } else {
                                $prefix = 'PRS';
                            }
                            
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
                                'pharmacy_depo_id'          => $dt_put['pharmacy_depo_id'] == '' || $dt_put['pharmacy_depo_id'] == null ? $depo_id : $depo_id,
                                'registration_destination_id' => $registration_destination_id,
                                'soap_p_id'                 => $soap_p_id,
                                'health_facility_item_id'   => $dt_reg->health_facility_item_id,
                                'doctor_in_charge_id'       => $doctor_in_charge_id,
                                'no_order'                  => $no_order,
                                // 'var_1'                     => isset($dt_put['var_1']) ? $dt_put['var_1'] : '',
                                // 'var_2'                     => isset($dt_put['var_2']) ? $dt_put['var_2'] : '',
                                // 'var_3'                     => isset($dt_put['var_3']) ? $dt_put['var_3'] : '',
                                'date_added'                => date("Y-m-d H:i:s",time()),
                                'date_0'                    => date("Y-m-d H:i:s",time()),
                                'flag'                      => 0,
                                'queue'                     => $queue,
                            );
                            $id_prescription = $this->Prescription->create($dt_prescription);
    
                            $pharmacy_prescription_id = $this->Prescription->select_id($id_prescription, false);

                            $pharmacy_prescription_flag = 0;
                        } else {
                            $pharmacy_prescription_id = $pharmacy_prescription[0]->id;
                            $pharmacy_prescription_flag = $pharmacy_prescription[0]->flag;
                            $pharmacy_prescription_no_order = $pharmacy_prescription[0]->no_order;

                            // NOTE - issue kalau resep udah diproses ga bisa edit field yang lain
                            // if ($pharmacy_prescription_flag != 0) {
                            //     $this->REST_Return(409, 'prescription with no_order ' . $pharmacy_prescription_no_order . ' is already processed');
                            //     return;
                            // }
                        }

                        if ($pharmacy_prescription_flag == 0) {
                            $this->Prescription_item->whereFields['pharmacy_prescription_id'] = $pharmacy_prescription_id;
                            $pharmacy_prescription_item = $this->Prescription_item->read(0,false);
                            $package_arr = $dt_put['form_prescription'];
                            // $tmp_delete_package_arr = array();
                            // // adding all current items (db) to temporary variable
                            // for ($i=0; $i < count($pharmacy_prescription_item); $i++) { 
                            //     $tmp_delete_package_arr[$pharmacy_prescription_item[$i]->package_id] = $pharmacy_prescription_item[$i]->pharmacy_catalog_id;
                            // }
    
                            if ($pharmacy_prescription_id != '' && $pharmacy_prescription_id != null) {
                                // delete all items
                                $dt_pharmacy_prescription_item = array(
                                    'is_delete'                 => 1,
                                );
                                $this->Prescription_item->update($pharmacy_prescription_id, $dt_pharmacy_prescription_item);
                            }
    
                            
                            // var_dump($this->db->last_query());
                            // die();
                            // if(count($dt_put['form_prescription']) == 0) {
                            //     for ($i=0; $i < count($pharmacy_prescription_item); $i++) { 
                            //     }
                            // }
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
                                                    'pharmacy_prescription_id'      => $pharmacy_prescription_id,
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
                                                    // 'var_1'                         => isset($dt_put['form_prescription'][$i]['var_1']) ? $dt_put['form_prescription'][$i]['var_1'] : '',
                                                    // 'var_2'                         => isset($dt_put['form_prescription'][$i]['var_2']) ? $dt_put['form_prescription'][$i]['var_2'] : '',
                                                    // 'var_3'                         => isset($dt_put['form_prescription'][$i]['var_3']) ? $dt_put['form_prescription'][$i]['var_3'] : '',
                                                    'sort'                          => isset($dt_put['form_prescription'][$i]['sort']) ? $dt_put['form_prescription'][$i]['sort'] : '',
                                                    'used'                          => isset($dt_put['form_prescription'][$i]['used']) ? $dt_put['form_prescription'][$i]['used'] : '',
                                                    'desc'                          => isset($dt_put['form_prescription'][$i]['desc']) ? $dt_put['form_prescription'][$i]['desc'] : '',
                                                    'is_delete'                     => 0,
                                                );
                                            }
                                            $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                                        }
                                    }
                                    
                                    // // create or update items according to the items provided and unset used items from temporary variable
                                    // if(array_key_exists($package_id,$tmp_delete_package_arr) == 1) {
                                    //     $dt_pharmacy_prescription_item['pharmacy_prescription_id'] = $pharmacy_prescription_id;
                                    //     $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                                        
                                    //     unset($tmp_delete_package_arr[$package_id]);
                                    // } else {
                                    //     $dt_pharmacy_prescription_item['pharmacy_prescription_id'] = $pharmacy_prescription_id;
                                    //     $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                                    // }
        
                                }
    
                                // var_dump($tmp_delete_package_arr);
                                // die();
                            
                                // // delete all items in temporary variable
                                // foreach ($tmp_delete_package_arr as $package_id => $pharmacy_catalog_id) {
                                //     $dt_pharmacy_prescription_item = array(
                                //         'pharmacy_prescription_id'  => $pharmacy_prescription_id,
                                //         'pharmacy_catalog_id'       => $pharmacy_catalog_id,
                                //         'package_id'                => $package_id,
                                //         'is_delete'                 => 1,
                                //     );
                                //     $this->Prescription_item->replace($dt_pharmacy_prescription_item);
                                // }
                            }
                        }
                    }
                }

                // if (isset($dt_put['instructions'])) {
                //     $select = 'a.id';
                //     $where = array('a.soap_id' => $id);
                //     $cppt_id = $this->Cppt->select();

                //     if ($cppt_id == '') {
                //         $this->REST_Return(404, 'there is no cppt with soap_id ' . $id);
                //         return;
                //     }

                //     $instructions = $dt_put['instructions'];

                //     $dt_cppt = array(
                //         'instructions'  => $instructions,
                //     );
            
                //     $this->Cppt->update($cppt_id, $dt_cppt);
                // }

                // MEDICAL_REHABILITATION
                // $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];

                // if ($is_medical_rehabilitation == 1) {
                //     $soap_id = $id;

                //     $select = 'a.id';
                //     $where = array(
                //         'soap_id' => $soap_id
                //     );
                //     $medical_rehabilitation = $this->Medical_rehabilitation->select(0, $select, $where);

                //     if (count($medical_rehabilitation) > 0) {
                //         $medical_rehabilitation_id = $medical_rehabilitation[0]->id;

                //         $service_date = $dt_put['service_date'];
                //         $anamnesa = $dt_put['anamnesa'];
                //         $functional_testing = $dt_put['functional_testing'];
                //         $functional_diagnosis = $dt_put['functional_diagnosis'];
                //         $medical_diagnosis = $dt_put['medical_diagnosis'];
                //         $support_examination = $dt_put['support_examination'];
                //         $advice_rehab = $dt_put['advice_rehab'];
                //         $evaluation = $dt_put['evaluation'];
                //         $suspected_disease_type_id = $dt_put['suspected_disease_type_id'];
                //         $suspected_disease = $dt_put['suspected_disease'];
                //         $result = $dt_put['result'];
                //         $conclusion = $dt_put['conclusion'];
                //         $recommendation = $dt_put['recommendation'];

                //         $dt_medical_rehabilitation = array(
                //             'service_date'              => $service_date,
                //             'anamnesa'                  => $anamnesa,
                //             'functional_testing'        => $functional_testing,
                //             'functional_diagnosis'      => $functional_diagnosis,
                //             'medical_diagnosis'         => $medical_diagnosis,
                //             'support_examination'       => $support_examination,
                //             'advice'                    => $advice_rehab,
                //             'evaluation'                => $evaluation,
                //             'suspected_disease_type_id' => $suspected_disease_type_id,
                //             'suspected_disease'         => $suspected_disease,
                //             'result'                    => $result,
                //             'conclusion'                => $conclusion,
                //             'recommendation'            => $recommendation,
                //             'user_updated'                => $user_id,
                //             'date_updated'              => date('Y-m-d H:i:s',time()),
                //         );

                //         $this->Medical_rehabilitation->update_v2($medical_rehabilitation_id, $dt_medical_rehabilitation);
                //     }
                // }

                // HEMODIALYSIS
                // $is_hemodialysis = $dt_put['is_hemodialysis'];

                // if ($is_hemodialysis == 1) {
                //     $soap_id = $id;

                //     $select = 'a.id';
                //     $where = array(
                //         'soap_id' => $soap_id
                //     );
                //     $hemodialysis_initiation = $this->Hemodialysis_initiation->select(0, $select, $where);

                //     if (count($hemodialysis_initiation) > 0) {
                //         $hemodialysis_initiation_id = $hemodialysis_initiation[0]->id;

                //         $main_diagnosis = $dt_put['main_diagnosis'];
                //         $etiology = $dt_put['etiology'];
                //         $comorbidities = $dt_put['comorbidities'];
                //         $prescription_hd = $dt_put['prescription_hd'];
                //         $frequency_hd = $dt_put['frequency_hd'];
                //         $vascular_access = $dt_put['vascular_access'];
                    
                //         $dt_hemodialysis_initiation = array(
                //             'main_diagnosis'    => $main_diagnosis,
                //             'etiology'          => $etiology,
                //             'comorbidities'     => $comorbidities,
                //             'prescription_hd'   => $prescription_hd,
                //             'frequency_hd'      => $frequency_hd,
                //             'vascular_access'   => $vascular_access,
                //             'user_updated'      => $user_id,
                //             'date_updated'      => date('Y-m-d H:i:s',time()),
                //         );

                //         $this->Hemodialysis_initiation->update_v2($hemodialysis_initiation_id, $dt_hemodialysis_initiation);
                //     }
                // }

                $this->db->trans_complete();
                /* End Transaction */
            
                if ($this->db->trans_status() === false) {
                    $response_data = array(
                        'error'     => $this->db->error(),
                        'data'      => $dt_soap,
                        'query'     => $this->db->last_query()
                    );
                    $this->REST_Return(500, 'error while trying to update soap', $response_data);
                    return;
                }

                $response_data = $id;
                $this->REST_Return(200, 'SUCCESS', $response_data);
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function bundle_get($p = 0) {
        $this->load->model("$this->directory/Resume_model", "Resume");
        $this->load->model("$this->directory/Resume_item_model", "Resume_item");
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
    
        $norm = $q_strings['norm'] ?? null;
        $register_date = $q_strings['register_date'] ?? null;
        $sep = $q_strings['sep'] ?? null;
        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;

        if ($sep == 0 || $sep == "0" || $sep == null) {
            if ($registration_destination_id == null || $registration_destination_id == "") {
                if ($norm == null || $norm == "") {
                    $this->REST_Return(400, 'sep or registration_destination_id is required');
                    return;
                } else {
                    $where['r.norm'] = $norm;
                }
            } else {
                $where['a.registration_destination_id'] = $registration_destination_id;
            }
        } else {
            $where['a.sep'] = $sep;
        }

        if ($register_date != null || $register_date != "" ) {
            $where['r.register_date >='] = $register_date.' 00:00:00';
            $where['r.register_date <='] = $register_date.' 23:59:59';
        }

        $where['hfi.health_facility_id'] = '40f06534-07ec-4221-ad9d-dea08196079f';

        $dt_sep = (object) array();

        $select = 'r.norm,r.register_date,rd.registration_id,a.registration_destination_id,a.sep';
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
            array('registration r', 'rd.registration_id = r.id', 'LEFT'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'LEFT'),
        );
        $dt_sep = $this->Registration_insurance->select(0, $select, $where, $join, array('r.register_date','ASC'));
        if (count($dt_sep) == 0) {
            $this->REST_Return(404, 'there is no sep with sep ' . $sep);
            return;
        }

        $yesno_arr = get_variable_assesment('yesno');
        $immunization_status_type_arr = get_variable_assesment('immunization_status_type');
        $drug_allergy_type_arr = get_variable_assesment('drug_allergy_type');
        $type_of_cases_type_arr = get_variable_assesment('type_of_cases_type');
        $level_of_consciousness_type_arr = get_variable_assesment('level_of_consciousness_type');
        
        for ($i=0; $i < count($dt_sep); $i++) { 

            $dt_sep[$i]->sep =  $dt_sep[$i]->sep;
            
            $registration_destination_id =  $dt_sep[$i]->registration_destination_id;
            $registration_id =  $dt_sep[$i]->registration_id;
            
            $select = "
                r.norm,
                CONCAT(
                    TIMESTAMPDIFF(YEAR, p.birth_date, CURDATE()) 
                    - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(p.birth_date, '%m%d')), ' th, ',
                    PERIOD_DIFF(DATE_FORMAT(CURDATE(), '%Y%m'), DATE_FORMAT(p.birth_date, '%Y%m')) % 12, ' bln, ',
                    DATEDIFF(
                    CURDATE(),
                    DATE_ADD(
                        p.birth_date,
                        INTERVAL TIMESTAMPDIFF(MONTH, p.birth_date, CURDATE()) MONTH
                    )
                    ), ' hr'
                ) AS umur,
                CASE 
                    WHEN p.sex = '1' THEN 'Laki - Laki'
                    WHEN p.sex = '0' THEN 'Perempuan'
                    ELSE NULL
                END AS jenis_kelamin,
                CASE 
                    WHEN a.bed_id != NULL THEN 'Rawat Inap'
                    WHEN a.bed_id != '' THEN 'Rawat Inap'
                    ELSE 'Rawat Jalan'
                END AS jenis_perawatan,
                ri.service_class_name as kelas_perawatan,
                a.exit_reason as cara_pulang,
                DATEDIFF(a.checkout_date, a.register_date) AS LOS,
                ms.code_inacbg,
                ms.desc_inacbg
            ";
            $join = array(
                array('registration r', 'a.registration_id = r.id', 'INNER'),
                array('registration_insurance ri', 'a.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
                array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
                array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
                array('how_to_enter hte', 'r.how_to_enter_id = hte.id', 'INNER'),
                array('patient p', 'r.norm = p.norm', 'INNER'),
                array('medical_services ms', 'a.id = ms.registration_destination_id', 'LEFT'),
            );
            $dt_reg = $this->Registration_destination->select($registration_destination_id, $select, array(), $join);
            
            $dt_sep[$i]->info_claim =  $dt_reg;
            
            $dt_sep[$i]->triage =  array();
            $dt_sep[$i]->nurse =  array();
            $dt_sep[$i]->assesment =  array();
            
            $registration_destination_ref_id = $this->Registration_destination->select($registration_destination_id, 'a.registration_destination_ref_id');
            if (count($registration_destination_ref_id) > 0) {
                $registration_destination_ref_id = $registration_destination_ref_id[0]->registration_destination_ref_id ? $registration_destination_ref_id[0]->registration_destination_ref_id : $registration_destination_id; 
                $select = "
                    a.referral_type_id as referral_type,
                    a.level_of_consciousness_type_id as level_of_consciousness_type,
                    a.immunization_status_type_id as immunization_status_type,
                    a.drug_allergy_type_id as drug_allergy_type,
                    a.main_complaint,
                    a.type_of_cases_type_id as type_of_cases_type,
                    a.pain_area,
                    a.pain_score,
                    a.breathing_path_arry,
                    a.breathing_arry,
                    a.circulation_arry,
                    a.consciousness_arry,
                    a.resource_arry,
                    a.triage_result
                ";
                $dt_triage = $this->Triage_new->select(0, $select, array('a.registration_destination_id'=>$registration_destination_ref_id));
                if (count($dt_triage) > 0) {
                    for ($d=0; $d < count($dt_triage); $d++) { 
                        $referral_type = $dt_triage[$d]->referral_type;
                        $immunization_status_type = $dt_triage[$d]->immunization_status_type;
                        $drug_allergy_type = $dt_triage[$d]->drug_allergy_type;
                        $type_of_cases_type = $dt_triage[$d]->type_of_cases_type;
                        $level_of_consciousness_type = $dt_triage[$d]->level_of_consciousness_type;
                        $breathing_path_arry = get_variable_assesment_arry('breathing_path_arry',$dt_triage[$d]->breathing_path_arry);
                        $breathing_arry = get_variable_assesment_arry('breathing_arry',$dt_triage[$d]->breathing_arry);
                        $circulation_arry = get_variable_assesment_arry('circulation_arry',$dt_triage[$d]->breathing_arry);
                        $consciousness_arry = get_variable_assesment_arry('consciousness_arry',$dt_triage[$d]->breathing_arry);
                        $resource_arry = get_variable_assesment_arry('resource_arry',$dt_triage[$d]->breathing_arry);
                        
                        $dt_triage[$d]->referral_type = $yesno_arr[$referral_type];
                        $dt_triage[$d]->immunization_status_type = $immunization_status_type_arr[$immunization_status_type];
                        $dt_triage[$d]->drug_allergy_type = $drug_allergy_type_arr[$drug_allergy_type];
                        $dt_triage[$d]->type_of_cases_type = $type_of_cases_type_arr[$type_of_cases_type];
                        $dt_triage[$d]->level_of_consciousness_type = $level_of_consciousness_type_arr[$level_of_consciousness_type];
                        $dt_triage[$d]->breathing_path_arry = $breathing_path_arry;
                        $dt_triage[$d]->circulation_arry = $circulation_arry;
                        $dt_triage[$d]->consciousness_arry = $consciousness_arry;
                        $dt_triage[$d]->breathing_arry = $breathing_arry;
                        $dt_triage[$d]->resource_arry = $resource_arry;
                    }
                    $dt_sep[$i]->triage =  $dt_triage;
                }

                // a.history_drugs,
                $select = "
                    a.anamesa_type_id as anamesa_type,
                    a.family_name,
                    a.family_relation_id as family_relation,
                    a.main_complaint,
                    a.history_disease_now,
                    a.history_disease_past,
                    a.aids,
                    a.prothesa,
                    a.disabled,
                    a.adl_type_id as adl_type,
                    a.systolic_blood_pressure,
                    a.diastolic_blood_pressure,
                    a.pulse_frequency,
                    a.breath_frequency,
                    a.oximetry_frequency,
                    a.temperature,
                    a.sore_score,
                    a.ews,
                    a.blood_sugar,
                    a.weight,
                    a.height,
                    a.imt,
                    a.height,
                    a.head_circumference,
                    a.chest_circumference,
                    a.arm_circumference,
                    a.stomach_circumference,
                    a.knee_circumference,
                    na.been_hospitalized_type_id,
                    na.been_hospitalized_answer,
                    na.any_history_family_disease_type_id,
                    na.any_history_family_answer_arry,
                    na.any_history_family_answer,
                    na.history_allergy_type_id,
                    na.history_allergy,
                    ne.any_surgery_type_id,
                    ne.any_surgery_date,
                    na.history_psychological_arry,
                    na.fall_risk_type_id,
                    na.patient_food_intake,
                    ne.six_month_lose_weight,
                    ne.lose_weight,
                    na.patient_disease_condition,
                    na.patient_certain_diets,
                    na.nutrition_total_score,
                    ne.nursing_diagnoses_arry,
                    ne.nursing_diagnoses,
                    ne.implementation_arry,
                    ne.implementation
                ";
                $join = array(
                    array('nurse_adult na', 'a.id = na.nurse_id', 'LEFT'),
                    array('nurse_emergency ne', 'a.id = ne.nurse_id', 'LEFT'),
                );
                $dt_nurse = $this->Nurse->select(0, $select, array('a.registration_destination_id'=>$registration_destination_ref_id),$join);
                if (count($dt_nurse) > 0) {
                    $dt_sep[$i]->nurse =  $dt_nurse;
                }
                
                $select = "
                    a.id,a.doctor_in_charge_id,
                ";
                $join = array(
                    array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
                );
                $dt_soap = $this->Soap->select(0, $select, array('a.registration_destination_id'=>$registration_destination_ref_id),$join);
                if ($dt_soap) {
                    if (count($dt_soap) > 0) {
                        for ($x=0; $x < count($dt_soap); $x++) { 
                            // var_dump($dt_soap[$x]);die();
                            // SOAP_S
                            $soap_id = $dt_soap[$x]->id;
                            $this->Soap_s->whereFields['soap_id'] = $soap_id;
                            $soap_s = $this->Soap_s->read();
                            if($soap_s != []) {
                                $dt_soap[$x]->soap_s_id = $soap_s[0]->id;
                                $dt_soap[$x]->anamesa_type_id = $soap_s[0]->anamesa_type_id;
                                $dt_soap[$x]->family_name = $soap_s[0]->family_name;
                                $dt_soap[$x]->family_relation_id = $soap_s[0]->family_relation_id;
                                $dt_soap[$x]->main_complaint = $soap_s[0]->main_complaint;
                                $dt_soap[$x]->history_disease_now = $soap_s[0]->history_disease_now;
                                $dt_soap[$x]->history_disease_past = $soap_s[0]->history_disease_past;
                                $dt_soap[$x]->history_drugs = $soap_s[0]->history_drugs;
                                $dt_soap[$x]->history_allergy = $soap_s[0]->history_allergy;
                            }
                            
                            // SOAP_O
                            $this->Soap_o->whereFields['soap_id'] = $soap_id;
                            $soap_o = $this->Soap_o->read();
                            if($soap_o != []) {
                                $dt_soap[$x]->soap_o_id = $soap_o[0]->id;
                                $dt_soap[$x]->physical_check = $soap_o[0]->physical_check;
                                $dt_soap[$x]->soap_o_anythingelse = $soap_o[0]->anythingelse;
                                $dt_soap[$x]->aware = $soap_o[0]->aware;
                                $dt_soap[$x]->eye_score = $soap_o[0]->eye_score;
                                $dt_soap[$x]->verbal_score = $soap_o[0]->verbal_score;
                                $dt_soap[$x]->motor_score = $soap_o[0]->motor_score;
                                $dt_soap[$x]->pupil_score = $soap_o[0]->pupil_score;
                                $dt_soap[$x]->verbal_response = $soap_o[0]->verbal_response;
                                $dt_soap[$x]->systolic_blood_pressure = $soap_o[0]->systolic_blood_pressure;
                                $dt_soap[$x]->diastolic_blood_pressure = $soap_o[0]->diastolic_blood_pressure;
                                $dt_soap[$x]->classification_blood_pressure_type_id = $soap_o[0]->classification_blood_pressure_type_id;
                                $dt_soap[$x]->verbal_response = $soap_o[0]->verbal_response;
                                $dt_soap[$x]->oxygen_type_id = $soap_o[0]->oxygen_type_id;
                                $dt_soap[$x]->pulse_frequency = $soap_o[0]->pulse_frequency;
                                $dt_soap[$x]->breath_frequency = $soap_o[0]->breath_frequency;
                                $dt_soap[$x]->oximetry_frequency = $soap_o[0]->oximetry_frequency;
                                $dt_soap[$x]->classification_oximetry_type_id = $soap_o[0]->classification_oximetry_type_id;
                                $dt_soap[$x]->temperature = $soap_o[0]->temperature;
                                $dt_soap[$x]->classification_temperature_type_id = $soap_o[0]->classification_temperature_type_id;
                                $dt_soap[$x]->blood_sugar = $soap_o[0]->blood_sugar;
                                $dt_soap[$x]->skin_color = $soap_o[0]->skin_color;
                                $dt_soap[$x]->behavior = $soap_o[0]->behavior;
                                $dt_soap[$x]->weight = $soap_o[0]->weight;
                                $dt_soap[$x]->classification_weight_type_id = $soap_o[0]->classification_weight_type_id;
                                $dt_soap[$x]->height = $soap_o[0]->height;
                                $dt_soap[$x]->imt = $soap_o[0]->imt;
                                $dt_soap[$x]->head_circumference = $soap_o[0]->head_circumference;
                                $dt_soap[$x]->chest_circumference = $soap_o[0]->chest_circumference;
                                $dt_soap[$x]->arm_circumference = $soap_o[0]->arm_circumference;
                                $dt_soap[$x]->stomach_circumference = $soap_o[0]->stomach_circumference;
                                $dt_soap[$x]->knee_circumference = $soap_o[0]->knee_circumference;
                                $dt_soap[$x]->sore_score = $soap_o[0]->sore_score;
                                $dt_soap[$x]->ews = $soap_o[0]->ews;
                                // NOTE - pakai non_icd10 yang di soap_a
                                // $dt_soap[$x]->non_icd10 = ($soap_o[0]->non_icd10 == null) ? array() : explode(',', $soap_o[0]->non_icd10); 
                                $dt_soap[$x]->head = $soap_o[0]->head;
                                $dt_soap[$x]->head_hair_color = $soap_o[0]->head_hair_color;
                                $dt_soap[$x]->head_hair_condition = $soap_o[0]->head_hair_condition;
                                $dt_soap[$x]->eye_conjunctive = $soap_o[0]->eye_conjunctive;
                                $dt_soap[$x]->eye_sclera = $soap_o[0]->eye_sclera;
                                $dt_soap[$x]->eye_pupil = $soap_o[0]->eye_pupil;
                                $dt_soap[$x]->eye_light_reflex = $soap_o[0]->eye_light_reflex;
                                $dt_soap[$x]->eye_blind_color = $soap_o[0]->eye_blind_color;
                                $dt_soap[$x]->mouth_mucosa = $soap_o[0]->mouth_mucosa;
                                $dt_soap[$x]->mouth_discoloration = $soap_o[0]->mouth_discoloration;
                                $dt_soap[$x]->mouth_tongue = $soap_o[0]->mouth_tongue;
                                $dt_soap[$x]->mouth_caries = $soap_o[0]->mouth_caries;
                                $dt_soap[$x]->neck_kgb = $soap_o[0]->neck_kgb;
                                $dt_soap[$x]->neck_trachea_position = $soap_o[0]->neck_trachea_position;
                                $dt_soap[$x]->neck_massa = $soap_o[0]->neck_massa;
                                $dt_soap[$x]->neck_jvp = $soap_o[0]->neck_jvp;
                                $dt_soap[$x]->thorax_insp_chest = $soap_o[0]->thorax_insp_chest;
                                $dt_soap[$x]->thorax_insp_retraction = $soap_o[0]->thorax_insp_retraction;
                                $dt_soap[$x]->thorax_palp_chest = $soap_o[0]->thorax_palp_chest;
                                $dt_soap[$x]->thorax_palp_fremitus = $soap_o[0]->thorax_palp_fremitus;
                                $dt_soap[$x]->thorax_palp_crepitation = $soap_o[0]->thorax_palp_crepitation;
                                $dt_soap[$x]->thorax_ausk_pul_vasiculer = $soap_o[0]->thorax_ausk_pul_vasiculer;
                                $dt_soap[$x]->thorax_ausk_pul_rhonki = $soap_o[0]->thorax_ausk_pul_rhonki;
                                $dt_soap[$x]->thorax_ausk_pul_wheezing = $soap_o[0]->thorax_ausk_pul_wheezing;
                                $dt_soap[$x]->thorax_ausk_cor_heart_sound = $soap_o[0]->thorax_ausk_cor_heart_sound;
                                $dt_soap[$x]->thorax_ausk_cor_murmur = $soap_o[0]->thorax_ausk_cor_murmur;
                                $dt_soap[$x]->thorax_ausk_cor_gallop = $soap_o[0]->thorax_ausk_cor_gallop;
                                $dt_soap[$x]->abdomen_supel = $soap_o[0]->abdomen_supel;
                                $dt_soap[$x]->abdomen_bowel_sounds = $soap_o[0]->abdomen_bowel_sounds;
                                $dt_soap[$x]->abdomen_tenderness = $soap_o[0]->abdomen_tenderness;
                                $dt_soap[$x]->abdomen_hepar_lien = $soap_o[0]->abdomen_hepar_lien;
                                $dt_soap[$x]->abdomen_other_physical_check = $soap_o[0]->abdomen_other_physical_check;
                                $dt_soap[$x]->extremity_acral = $soap_o[0]->extremity_acral;
                                $dt_soap[$x]->extremity_crt = $soap_o[0]->extremity_crt;
                                $dt_soap[$x]->extremity_edema = $soap_o[0]->extremity_edema;
                                $dt_soap[$x]->ophthal_status = $soap_o[0]->ophthal_status;
                                $dt_soap[$x]->neurologist_status = $soap_o[0]->neurologist_status;
    
                                $ophthal_od_decode = json_decode($soap_o[0]->ophthal_od);
                                if (is_iterable($ophthal_od_decode)) {
                                    foreach ($ophthal_od_decode as $key => $val) {
                                        if (is_numeric($val)) {
                                            $ophthal_od_decode->$key = (int) $val;
                                        }
                                    }
                                }
    
                                $ophthal_os_decode = json_decode($soap_o[0]->ophthal_os);
                                if (is_iterable($ophthal_os_decode)) {
                                    foreach ($ophthal_os_decode as $key => $val) {
                                        if (is_numeric($val)) {
                                            $ophthal_os_decode->$key = (int) $val;
                                        }
                                    }
                                }
    
                                $dt_soap[$x]->ophthal_od = $ophthal_od_decode;
                                $dt_soap[$x]->ophthal_os = $ophthal_os_decode;
                                $dt_soap[$x]->uub_type_id = $soap_o[0]->uub_type_id;
                                $dt_soap[$x]->eye_anemic_conjunctiva = $soap_o[0]->eye_anemic_conjunctiva;
                                $dt_soap[$x]->eye_interic_sclera = $soap_o[0]->eye_interic_sclera;
                                $dt_soap[$x]->eye_anythingelse = $soap_o[0]->eye_anythingelse;
                                $dt_soap[$x]->tht_hyperemic_pharynx = $soap_o[0]->tht_hyperemic_pharynx;
                                $dt_soap[$x]->tht_tonsil = $soap_o[0]->tht_tonsil;
                                $dt_soap[$x]->tht_ear = $soap_o[0]->tht_ear;
                                $dt_soap[$x]->tht_anythingelse = $soap_o[0]->tht_anythingelse;
                                $dt_soap[$x]->neck_lymph_gland = $soap_o[0]->neck_lymph_gland;
                                $dt_soap[$x]->thorax_heart_type_id = $soap_o[0]->thorax_heart_type_id;
                                $dt_soap[$x]->thorax_murmur = $soap_o[0]->thorax_murmur;
                                $dt_soap[$x]->thorax_gallop = $soap_o[0]->thorax_gallop;
                                $dt_soap[$x]->thorax_anythingelse = $soap_o[0]->thorax_anythingelse;
                                $dt_soap[$x]->pulmo_arry = ($soap_o[0]->pulmo_arry != "" ? explode(',', $soap_o[0]->pulmo_arry) : []);
                                $dt_soap[$x]->pulmo_anythingelse = $soap_o[0]->pulmo_anythingelse;
                                $dt_soap[$x]->pulmo_breath_movement = $soap_o[0]->pulmo_breath_movement;
                                $dt_soap[$x]->pulmo_breath_sound = $soap_o[0]->pulmo_breath_sound;
                                $dt_soap[$x]->extremity_acral_type_id = $soap_o[0]->extremity_acral_type_id;
                                $dt_soap[$x]->extremity_capillary_refilling_time = $soap_o[0]->extremity_capillary_refilling_time;
                                $dt_soap[$x]->extremity_anythingelse = $soap_o[0]->extremity_anythingelse;
                                $dt_soap[$x]->abdomen_inspection_type_id = $soap_o[0]->abdomen_inspection_type_id;
                                $dt_soap[$x]->abdomen_palpation_type_id = $soap_o[0]->abdomen_palpation_type_id;
                                $dt_soap[$x]->abdomen_sore_press = $soap_o[0]->abdomen_sore_press;
                                $dt_soap[$x]->abdomen_sore_press_location = $soap_o[0]->abdomen_sore_press_location;
                                $dt_soap[$x]->abdomen_hepar = $soap_o[0]->abdomen_hepar;
                                $dt_soap[$x]->abdomen_bu = $soap_o[0]->abdomen_bu;
                                $dt_soap[$x]->localist_status = $soap_o[0]->localist_status;
                                // $dt_soap[$x]->localist_status = $soap_o[0]->physical_check; // NOTE - SEMENTARA DARI physical_check
                                $dt_soap[$x]->abdomen_lien = $soap_o[0]->abdomen_lien;
                                $dt_soap[$x]->genitalia = $soap_o[0]->genitalia;
    
                                // SOAP_O_ACTION
                                $this->Soap_o_action->whereFields['soap_o_id'] = $soap_o[0]->id;
                                $soap_o_action = $this->Soap_o_action->read();
                                if($soap_o_action != []) {
                                    $dt_soap[$x]->soap_o_action_id = $soap_o_action[0]->id;
                                    $dt_soap[$x]->canvasing_type_id = $soap_o_action[0]->canvasing_type_id;
                                    $dt_soap[$x]->canvasing_text = $soap_o_action[0]->canvasing_text;
                                    $dt_soap[$x]->attr1 = $soap_o_action[0]->attr1;
                                    $dt_soap[$x]->attr2 = $soap_o_action[0]->attr2;
                                }
                            }
    
                            $soap_a = $this->db->query("
                                SELECT a.*
                                FROM soap_a as a
                                WHERE soap_id = '$soap_id'
                                AND (a.cppt_id IS NULL OR a.cppt_id = '')
                                ORDER BY a.id ASC
                            ")->result();
                            if($soap_a != []) {
                                $dt_soap[$x]->soap_a_id = $soap_a[0]->id;
                                $dt_soap[$x]->development_notes = $soap_a[0]->development_notes;
                                $dt_soap[$x]->soap_a_anythingelse = $soap_a[0]->anythingelse;
                                if ($soap_a[0]->obgyn_usg_file) {
                                    $soap_a[0]->obgyn_usg_file = base_url().$soap_a[0]->obgyn_usg_file;
                                }
                                $soap_a[0]->non_icd10 = ($soap_a[0]->non_icd10 == null) ? array() : explode('|', $soap_a[0]->non_icd10);
                                $dt_soap[$x]->soap_a = $soap_a[0];
                                $dt_soap[$x]->icd10_notes = $soap_a[0]->icd10_notes;
                                $dt_soap[$x]->non_icd10 = $soap_a[0]->non_icd10;
    
                                // SOAP_A_ACTION
                                $this->Soap_a_action->whereFields['soap_a_id'] = $soap_a[0]->id;
                                $this->Soap_a_action->whereFields['is_delete'] = 0;
                                $soap_a_action = $this->Soap_a_action->read();
                                if($soap_a_action) {
                                    for ($j=0; $j < count($soap_a_action); $j++) { 
                                        $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
                                        $dt_soap[$x]->icd10[$j]['id'] = $soap_a_action[$j]->id;
                                        $dt_soap[$x]->icd10[$j]['icd10_id'] = $soap_a_action[$j]->icd10_id;
                                        $dt_soap[$x]->icd10[$j]['code'] = $icd10[0]->code;
                                        $dt_soap[$x]->icd10[$j]['code_s'] = $icd10[0]->code_s;
                                        $dt_soap[$x]->icd10[$j]['name'] = $icd10[0]->name;
                                        $dt_soap[$x]->icd10[$j]['date_added'] = $soap_a_action[$j]->date_added;
                                        $dt_soap[$x]->icd10[$j]['index'] = $soap_a_action[$j]->index;
                                    }
                                }
                            }
                            $doctor_in_charge_id = $dt_soap[$x]->doctor_in_charge_id;
                            $soap_p = $this->db->query("
                                SELECT a.*
                                FROM soap_p as a
                                WHERE soap_id = '$soap_id'
                                ORDER BY a.id ASC
                            ")->result();
                            $dt_soap[$x]->icd9cm = array();
                            // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
                            if($soap_p) {
                                $idx_prescription = 0;
                                for ($spx=0; $spx < count($soap_p); $spx++) { 
                                    // PRESCRIPTION
                                    $soap_p_id = $soap_p[$spx]->id;
    
                                    // $this->db->cache_off();
                                    $prescription = $this->db->query("
                                        SELECT p.id,
                                        p.no_order,
                                        p.queue,
                                        p.type_prescription_id,
                                        h.name AS flag_prescription,
                                        p.pharmacy_depo_id,
                                        p.soap_p_id,
                                        p.doctor_in_charge_id,
                                        e.fullname AS doctor_in_charge_name,
                                        p.health_facility_item_id,
                                        p.flag,
                                        p.is_retur,
                                        p.date_added
                                        FROM pharmacy_prescription p
                                        LEFT JOIN employee e ON p.doctor_in_charge_id = e.id
                                        LEFT JOIN helper h ON p.type_prescription_id = h.code AND h.variable = 'type_prescription'
                                        WHERE p.soap_p_id = '$soap_p_id'
                                        AND p.is_delete = 0
                                        ORDER BY p.date_added DESC
                                    ")->result();
                                    if ($prescription) {
                                        for ($k=0; $k < count($prescription); $k++) { 
                                            $dt_soap[$x]->prescription['no_order'] = $prescription[$k]->no_order;
                                            $dt_soap[$x]->prescription['queue'] = $prescription[$k]->queue;
                                            $dt_soap[$x]->prescription['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                            $dt_soap[$x]->prescription['type_prescription_name'] = $prescription[$k]->flag_prescription;
                                            $dt_soap[$x]->prescription['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                                            $dt_soap[$x]->prescription['soap_p_id'] = $prescription[$k]->soap_p_id;
                                            $dt_soap[$x]->prescription['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                                            $dt_soap[$x]->prescription['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                                            $dt_soap[$x]->prescription['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                                            $dt_soap[$x]->prescription['flag'] = $prescription[$k]->flag;
                                            $dt_soap[$x]->prescription['is_retur'] = $prescription[$k]->is_retur;
                                            
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['no_order'] = $prescription[$k]->no_order;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['queue'] = $prescription[$k]->queue;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['type_prescription_name'] = $prescription[$k]->flag_prescription;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['soap_p_id'] = $prescription[$k]->soap_p_id;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['flag'] = $prescription[$k]->flag;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['is_retur'] = $prescription[$k]->is_retur;
                                            $dt_soap[$x]->prescription_new[$idx_prescription]['date_added'] = $prescription[$k]->date_added;
                                            
                                            $prescription_id = $prescription[$k]->id;
                                            // // $this->db->cache_off();
                                            $form_prescription = $this->db->query("
                                                SELECT
                                                pi.pharmacy_prescription_id,
                                                pi.pharmacy_catalog_id,
                                                pc.name,
                                                pc.code,
                                                pi.prescription_item_type_id,
                                                pi.prescription_item_type_used,
                                                pi.prescription_item_type_value,
                                                pi.prescription_item_type_package,
                                                pi.package_id,
                                                pi.package_name,
                                                pi.dose,
                                                pi.dose_doctor,
                                                pi.batch,
                                                pi.expired,
                                                pi.qty_doctor,
                                                pi.qty,
                                                pi.qty_copy,
                                                pi.used,
                                                pi.desc,
                                                pi.sort
                                                FROM pharmacy_prescription_item pi
                                                LEFT JOIN pharmacy_catalog pc ON pi.pharmacy_catalog_id = pc.id
                                                WHERE pi.is_delete = 0
                                                AND pi.pharmacy_prescription_id = '$prescription_id'
                                                ORDER BY pi.sort ASC
                                            ")->result();
                                            if ($form_prescription != []) {
                                                $form_prescription_parent = array();
    
                                                for ($j=0; $j < count($form_prescription); $j++) { 
                                                    // $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                                                    $dt_soap[$x]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                                    $dt_soap[$x]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                                    $dt_soap[$x]->form_prescription[$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                                    $dt_soap[$x]->form_prescription[$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                                    $dt_soap[$x]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                                    $dt_soap[$x]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                                                    $dt_soap[$x]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                                                    $dt_soap[$x]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                                                    // $dt_soap[$x]->form_prescription[$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                                    $dt_soap[$x]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                                    $dt_soap[$x]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                                                    $dt_soap[$x]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                                                    $dt_soap[$x]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                                    $dt_soap[$x]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                                                    $dt_soap[$x]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                                    $dt_soap[$x]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                                                    $dt_soap[$x]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                                                    $dt_soap[$x]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
    
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['package_id'] = $form_prescription[$j]->package_id;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['package_name'] = $form_prescription[$j]->package_name;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose'] = $form_prescription[$j]->dose;
                                                    // $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['batch'] = $form_prescription[$j]->batch;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['expired'] = $form_prescription[$j]->expired;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty'] = $form_prescription[$j]->qty;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['used'] = $form_prescription[$j]->used;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['desc'] = $form_prescription[$j]->desc;
                                                    $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['sort'] = $form_prescription[$j]->sort;
                                                
                                                    //------------------- detail form_prescription_parent -//
                                                    $parent_id = $form_prescription[$j]->prescription_item_type_id; 
                                                    $parent_used = $form_prescription[$j]->prescription_item_type_used; 
                                                    $parent_value = $form_prescription[$j]->prescription_item_type_value; 
                                                    $parent_package = $form_prescription[$j]->prescription_item_type_package; 
    
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
                                                    for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                                                        if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                                                            $skip = 1;
                                                        }
                                                    }
    
                                                    if ($skip == 0) {
                                                        array_push($form_prescription_parent, $dt_form_prescription_parent);
                                                    }
                                                    //----------------------------------------------------//
                                                }
                                                
                                                $dt_soap[$x]->prescription_new[$idx_prescription]['form_prescription_parent'] = $form_prescription_parent;
                                                
                                                $dt_soap[$x]->prescription_new = array_values($dt_soap[$x]->prescription_new);
                                            } 
    
                                            $idx_prescription++;
                                        }
                                    }
                                }
    
                                $dt_soap[$x]->soap_p_id = $soap_p[0]->id;
                                $dt_soap[$x]->therapy_plan = $soap_p[0]->therapy_plan;
                                $dt_soap[$x]->estimated_duration_treatment = $soap_p[0]->estimated_duration_treatment;
                                $dt_soap[$x]->follow_up_care_arry = ($soap_p[0]->follow_up_care_arry != '') ? explode(',', $soap_p[0]->follow_up_care_arry): [];
                                $dt_soap[$x]->diet_and_nutrition_type_id = $soap_p[0]->diet_and_nutrition_type_id;
                                $dt_soap[$x]->diet_and_nutrition = $soap_p[0]->diet_and_nutrition;
                                $dt_soap[$x]->wound_care_type_id = $soap_p[0]->wound_care_type_id;
                                $dt_soap[$x]->wound_care = $soap_p[0]->wound_care;
                                $dt_soap[$x]->medical_rehabilitation_type_id = $soap_p[0]->medical_rehabilitation_type_id;
                                $dt_soap[$x]->medical_rehabilitation = $soap_p[0]->medical_rehabilitation;
                                $dt_soap[$x]->pain_management_type_id = $soap_p[0]->pain_management_type_id;
                                $dt_soap[$x]->pain_management = $soap_p[0]->pain_management;
                                $dt_soap[$x]->discharge_planning_other = $soap_p[0]->discharge_planning_other;
    
                                // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
                                for ($sp=0; $sp < count($soap_p); $sp++) { 
                                    // SOAP_P_ACTION
                                    $this->Soap_p_action->whereFields['soap_p_id'] = $soap_p[$sp]->id;
                                    $this->Soap_p_action->whereFields['a.is_delete'] = 0;
                                    $soap_p_action = $this->Soap_p_action->read();
                                    if($soap_p_action) {
                                        for ($j=0; $j < count($soap_p_action); $j++) { 
                                            $icd9cm = $this->Icd9cm->read($soap_p_action[$j]->icd9cm_id);
                                            
                                            $dt_icd9cm = array(
                                                'icd9cm_id' => $icd9cm[0]->id,
                                                'code' => $icd9cm[0]->code,
                                                'code_s' => $icd9cm[0]->code_s,
                                                'name' => $icd9cm[0]->name,
                                            );
    
                                            array_push($dt_soap[$x]->icd9cm, $dt_icd9cm);
                                        }
                                    }
                                }
                            }
    
                            $select_a = 'a.name';
                            $where_a = array('norm' => $dt_sep[0]->norm, 'a.is_delete' => 0);
                            $join_a = array(
                                array('helper h', 'a.type_allergy_id = h.code AND h.variable = "type_allergy"', 'LEFT'),
                            );
                            $allergies = $this->Patient_allergy->select(0, $select_a, $where_a, $join_a);
                            if (count($allergies) > 0) {
                                $dt_soap[$x]->allergies = $allergies;
                            } else {
                                $dt_soap[$x]->allergies = [];
                            }
                            $dt_sep[$i]->assesment =  $dt_soap;

                        }
                    }
                }
                // SOAP RANAP
                
                $dt_soap_inpatient = $this->Soap->select(0, $select, array('a.registration_destination_id'=>$registration_destination_id),$join);
                if ($dt_soap_inpatient) {
                    if (count($dt_soap_inpatient) > 0) {
                        for ($x=0; $x < count($dt_soap_inpatient); $x++) { 
                            // var_dump($dt_soap_inpatient[$x]);die();
                            // SOAP_S
                            $soap_id = $dt_soap_inpatient[$x]->id;
                            $this->Soap_s->whereFields['soap_id'] = $soap_id;
                            $soap_s = $this->Soap_s->read();
                            if($soap_s != []) {
                                $dt_soap_inpatient[$x]->soap_s_id = $soap_s[0]->id;
                                $dt_soap_inpatient[$x]->anamesa_type_id = $soap_s[0]->anamesa_type_id;
                                $dt_soap_inpatient[$x]->family_name = $soap_s[0]->family_name;
                                $dt_soap_inpatient[$x]->family_relation_id = $soap_s[0]->family_relation_id;
                                $dt_soap_inpatient[$x]->main_complaint = $soap_s[0]->main_complaint;
                                $dt_soap_inpatient[$x]->history_disease_now = $soap_s[0]->history_disease_now;
                                $dt_soap_inpatient[$x]->history_disease_past = $soap_s[0]->history_disease_past;
                                $dt_soap_inpatient[$x]->history_drugs = $soap_s[0]->history_drugs;
                                $dt_soap_inpatient[$x]->history_allergy = $soap_s[0]->history_allergy;
                            }
                            
                            // SOAP_O
                            $this->Soap_o->whereFields['soap_id'] = $soap_id;
                            $soap_o = $this->Soap_o->read();
                            if($soap_o != []) {
                                $dt_soap_inpatient[$x]->soap_o_id = $soap_o[0]->id;
                                $dt_soap_inpatient[$x]->physical_check = $soap_o[0]->physical_check;
                                $dt_soap_inpatient[$x]->soap_o_anythingelse = $soap_o[0]->anythingelse;
                                $dt_soap_inpatient[$x]->aware = $soap_o[0]->aware;
                                $dt_soap_inpatient[$x]->eye_score = $soap_o[0]->eye_score;
                                $dt_soap_inpatient[$x]->verbal_score = $soap_o[0]->verbal_score;
                                $dt_soap_inpatient[$x]->motor_score = $soap_o[0]->motor_score;
                                $dt_soap_inpatient[$x]->pupil_score = $soap_o[0]->pupil_score;
                                $dt_soap_inpatient[$x]->verbal_response = $soap_o[0]->verbal_response;
                                $dt_soap_inpatient[$x]->systolic_blood_pressure = $soap_o[0]->systolic_blood_pressure;
                                $dt_soap_inpatient[$x]->diastolic_blood_pressure = $soap_o[0]->diastolic_blood_pressure;
                                $dt_soap_inpatient[$x]->classification_blood_pressure_type_id = $soap_o[0]->classification_blood_pressure_type_id;
                                $dt_soap_inpatient[$x]->verbal_response = $soap_o[0]->verbal_response;
                                $dt_soap_inpatient[$x]->oxygen_type_id = $soap_o[0]->oxygen_type_id;
                                $dt_soap_inpatient[$x]->pulse_frequency = $soap_o[0]->pulse_frequency;
                                $dt_soap_inpatient[$x]->breath_frequency = $soap_o[0]->breath_frequency;
                                $dt_soap_inpatient[$x]->oximetry_frequency = $soap_o[0]->oximetry_frequency;
                                $dt_soap_inpatient[$x]->classification_oximetry_type_id = $soap_o[0]->classification_oximetry_type_id;
                                $dt_soap_inpatient[$x]->temperature = $soap_o[0]->temperature;
                                $dt_soap_inpatient[$x]->classification_temperature_type_id = $soap_o[0]->classification_temperature_type_id;
                                $dt_soap_inpatient[$x]->blood_sugar = $soap_o[0]->blood_sugar;
                                $dt_soap_inpatient[$x]->skin_color = $soap_o[0]->skin_color;
                                $dt_soap_inpatient[$x]->behavior = $soap_o[0]->behavior;
                                $dt_soap_inpatient[$x]->weight = $soap_o[0]->weight;
                                $dt_soap_inpatient[$x]->classification_weight_type_id = $soap_o[0]->classification_weight_type_id;
                                $dt_soap_inpatient[$x]->height = $soap_o[0]->height;
                                $dt_soap_inpatient[$x]->imt = $soap_o[0]->imt;
                                $dt_soap_inpatient[$x]->head_circumference = $soap_o[0]->head_circumference;
                                $dt_soap_inpatient[$x]->chest_circumference = $soap_o[0]->chest_circumference;
                                $dt_soap_inpatient[$x]->arm_circumference = $soap_o[0]->arm_circumference;
                                $dt_soap_inpatient[$x]->stomach_circumference = $soap_o[0]->stomach_circumference;
                                $dt_soap_inpatient[$x]->knee_circumference = $soap_o[0]->knee_circumference;
                                $dt_soap_inpatient[$x]->sore_score = $soap_o[0]->sore_score;
                                $dt_soap_inpatient[$x]->ews = $soap_o[0]->ews;
                                // NOTE - pakai non_icd10 yang di soap_a
                                // $dt_soap_inpatient[$x]->non_icd10 = ($soap_o[0]->non_icd10 == null) ? array() : explode(',', $soap_o[0]->non_icd10); 
                                $dt_soap_inpatient[$x]->head = $soap_o[0]->head;
                                $dt_soap_inpatient[$x]->head_hair_color = $soap_o[0]->head_hair_color;
                                $dt_soap_inpatient[$x]->head_hair_condition = $soap_o[0]->head_hair_condition;
                                $dt_soap_inpatient[$x]->eye_conjunctive = $soap_o[0]->eye_conjunctive;
                                $dt_soap_inpatient[$x]->eye_sclera = $soap_o[0]->eye_sclera;
                                $dt_soap_inpatient[$x]->eye_pupil = $soap_o[0]->eye_pupil;
                                $dt_soap_inpatient[$x]->eye_light_reflex = $soap_o[0]->eye_light_reflex;
                                $dt_soap_inpatient[$x]->eye_blind_color = $soap_o[0]->eye_blind_color;
                                $dt_soap_inpatient[$x]->mouth_mucosa = $soap_o[0]->mouth_mucosa;
                                $dt_soap_inpatient[$x]->mouth_discoloration = $soap_o[0]->mouth_discoloration;
                                $dt_soap_inpatient[$x]->mouth_tongue = $soap_o[0]->mouth_tongue;
                                $dt_soap_inpatient[$x]->mouth_caries = $soap_o[0]->mouth_caries;
                                $dt_soap_inpatient[$x]->neck_kgb = $soap_o[0]->neck_kgb;
                                $dt_soap_inpatient[$x]->neck_trachea_position = $soap_o[0]->neck_trachea_position;
                                $dt_soap_inpatient[$x]->neck_massa = $soap_o[0]->neck_massa;
                                $dt_soap_inpatient[$x]->neck_jvp = $soap_o[0]->neck_jvp;
                                $dt_soap_inpatient[$x]->thorax_insp_chest = $soap_o[0]->thorax_insp_chest;
                                $dt_soap_inpatient[$x]->thorax_insp_retraction = $soap_o[0]->thorax_insp_retraction;
                                $dt_soap_inpatient[$x]->thorax_palp_chest = $soap_o[0]->thorax_palp_chest;
                                $dt_soap_inpatient[$x]->thorax_palp_fremitus = $soap_o[0]->thorax_palp_fremitus;
                                $dt_soap_inpatient[$x]->thorax_palp_crepitation = $soap_o[0]->thorax_palp_crepitation;
                                $dt_soap_inpatient[$x]->thorax_ausk_pul_vasiculer = $soap_o[0]->thorax_ausk_pul_vasiculer;
                                $dt_soap_inpatient[$x]->thorax_ausk_pul_rhonki = $soap_o[0]->thorax_ausk_pul_rhonki;
                                $dt_soap_inpatient[$x]->thorax_ausk_pul_wheezing = $soap_o[0]->thorax_ausk_pul_wheezing;
                                $dt_soap_inpatient[$x]->thorax_ausk_cor_heart_sound = $soap_o[0]->thorax_ausk_cor_heart_sound;
                                $dt_soap_inpatient[$x]->thorax_ausk_cor_murmur = $soap_o[0]->thorax_ausk_cor_murmur;
                                $dt_soap_inpatient[$x]->thorax_ausk_cor_gallop = $soap_o[0]->thorax_ausk_cor_gallop;
                                $dt_soap_inpatient[$x]->abdomen_supel = $soap_o[0]->abdomen_supel;
                                $dt_soap_inpatient[$x]->abdomen_bowel_sounds = $soap_o[0]->abdomen_bowel_sounds;
                                $dt_soap_inpatient[$x]->abdomen_tenderness = $soap_o[0]->abdomen_tenderness;
                                $dt_soap_inpatient[$x]->abdomen_hepar_lien = $soap_o[0]->abdomen_hepar_lien;
                                $dt_soap_inpatient[$x]->abdomen_other_physical_check = $soap_o[0]->abdomen_other_physical_check;
                                $dt_soap_inpatient[$x]->extremity_acral = $soap_o[0]->extremity_acral;
                                $dt_soap_inpatient[$x]->extremity_crt = $soap_o[0]->extremity_crt;
                                $dt_soap_inpatient[$x]->extremity_edema = $soap_o[0]->extremity_edema;
                                $dt_soap_inpatient[$x]->ophthal_status = $soap_o[0]->ophthal_status;
                                $dt_soap_inpatient[$x]->neurologist_status = $soap_o[0]->neurologist_status;
    
                                $ophthal_od_decode = json_decode($soap_o[0]->ophthal_od);
                                if (is_iterable($ophthal_od_decode)) {
                                    foreach ($ophthal_od_decode as $key => $val) {
                                        if (is_numeric($val)) {
                                            $ophthal_od_decode->$key = (int) $val;
                                        }
                                    }
                                }
    
                                $ophthal_os_decode = json_decode($soap_o[0]->ophthal_os);
                                if (is_iterable($ophthal_os_decode)) {
                                    foreach ($ophthal_os_decode as $key => $val) {
                                        if (is_numeric($val)) {
                                            $ophthal_os_decode->$key = (int) $val;
                                        }
                                    }
                                }
    
                                $dt_soap_inpatient[$x]->ophthal_od = $ophthal_od_decode;
                                $dt_soap_inpatient[$x]->ophthal_os = $ophthal_os_decode;
                                $dt_soap_inpatient[$x]->uub_type_id = $soap_o[0]->uub_type_id;
                                $dt_soap_inpatient[$x]->eye_anemic_conjunctiva = $soap_o[0]->eye_anemic_conjunctiva;
                                $dt_soap_inpatient[$x]->eye_interic_sclera = $soap_o[0]->eye_interic_sclera;
                                $dt_soap_inpatient[$x]->eye_anythingelse = $soap_o[0]->eye_anythingelse;
                                $dt_soap_inpatient[$x]->tht_hyperemic_pharynx = $soap_o[0]->tht_hyperemic_pharynx;
                                $dt_soap_inpatient[$x]->tht_tonsil = $soap_o[0]->tht_tonsil;
                                $dt_soap_inpatient[$x]->tht_ear = $soap_o[0]->tht_ear;
                                $dt_soap_inpatient[$x]->tht_anythingelse = $soap_o[0]->tht_anythingelse;
                                $dt_soap_inpatient[$x]->neck_lymph_gland = $soap_o[0]->neck_lymph_gland;
                                $dt_soap_inpatient[$x]->thorax_heart_type_id = $soap_o[0]->thorax_heart_type_id;
                                $dt_soap_inpatient[$x]->thorax_murmur = $soap_o[0]->thorax_murmur;
                                $dt_soap_inpatient[$x]->thorax_gallop = $soap_o[0]->thorax_gallop;
                                $dt_soap_inpatient[$x]->thorax_anythingelse = $soap_o[0]->thorax_anythingelse;
                                $dt_soap_inpatient[$x]->pulmo_arry = ($soap_o[0]->pulmo_arry != "" ? explode(',', $soap_o[0]->pulmo_arry) : []);
                                $dt_soap_inpatient[$x]->pulmo_anythingelse = $soap_o[0]->pulmo_anythingelse;
                                $dt_soap_inpatient[$x]->pulmo_breath_movement = $soap_o[0]->pulmo_breath_movement;
                                $dt_soap_inpatient[$x]->pulmo_breath_sound = $soap_o[0]->pulmo_breath_sound;
                                $dt_soap_inpatient[$x]->extremity_acral_type_id = $soap_o[0]->extremity_acral_type_id;
                                $dt_soap_inpatient[$x]->extremity_capillary_refilling_time = $soap_o[0]->extremity_capillary_refilling_time;
                                $dt_soap_inpatient[$x]->extremity_anythingelse = $soap_o[0]->extremity_anythingelse;
                                $dt_soap_inpatient[$x]->abdomen_inspection_type_id = $soap_o[0]->abdomen_inspection_type_id;
                                $dt_soap_inpatient[$x]->abdomen_palpation_type_id = $soap_o[0]->abdomen_palpation_type_id;
                                $dt_soap_inpatient[$x]->abdomen_sore_press = $soap_o[0]->abdomen_sore_press;
                                $dt_soap_inpatient[$x]->abdomen_sore_press_location = $soap_o[0]->abdomen_sore_press_location;
                                $dt_soap_inpatient[$x]->abdomen_hepar = $soap_o[0]->abdomen_hepar;
                                $dt_soap_inpatient[$x]->abdomen_bu = $soap_o[0]->abdomen_bu;
                                $dt_soap_inpatient[$x]->localist_status = $soap_o[0]->localist_status;
                                // $dt_soap_inpatient[$x]->localist_status = $soap_o[0]->physical_check; // NOTE - SEMENTARA DARI physical_check
                                $dt_soap_inpatient[$x]->abdomen_lien = $soap_o[0]->abdomen_lien;
                                $dt_soap_inpatient[$x]->genitalia = $soap_o[0]->genitalia;
    
                                // SOAP_O_ACTION
                                $this->Soap_o_action->whereFields['soap_o_id'] = $soap_o[0]->id;
                                $soap_o_action = $this->Soap_o_action->read();
                                if($soap_o_action != []) {
                                    $dt_soap_inpatient[$x]->soap_o_action_id = $soap_o_action[0]->id;
                                    $dt_soap_inpatient[$x]->canvasing_type_id = $soap_o_action[0]->canvasing_type_id;
                                    $dt_soap_inpatient[$x]->canvasing_text = $soap_o_action[0]->canvasing_text;
                                    $dt_soap_inpatient[$x]->attr1 = $soap_o_action[0]->attr1;
                                    $dt_soap_inpatient[$x]->attr2 = $soap_o_action[0]->attr2;
                                }
                            }
    
                            $soap_a = $this->db->query("
                                SELECT a.*
                                FROM soap_a as a
                                WHERE soap_id = '$soap_id'
                                AND (a.cppt_id IS NULL OR a.cppt_id = '')
                                ORDER BY a.id ASC
                            ")->result();
                            if($soap_a != []) {
                                $dt_soap_inpatient[$x]->soap_a_id = $soap_a[0]->id;
                                $dt_soap_inpatient[$x]->development_notes = $soap_a[0]->development_notes;
                                $dt_soap_inpatient[$x]->soap_a_anythingelse = $soap_a[0]->anythingelse;
                                if ($soap_a[0]->obgyn_usg_file) {
                                    $soap_a[0]->obgyn_usg_file = base_url().$soap_a[0]->obgyn_usg_file;
                                }
                                $soap_a[0]->non_icd10 = ($soap_a[0]->non_icd10 == null) ? array() : explode('|', $soap_a[0]->non_icd10);
                                $dt_soap_inpatient[$x]->soap_a = $soap_a[0];
                                $dt_soap_inpatient[$x]->icd10_notes = $soap_a[0]->icd10_notes;
                                $dt_soap_inpatient[$x]->non_icd10 = $soap_a[0]->non_icd10;
    
                                // SOAP_A_ACTION
                                $this->Soap_a_action->whereFields['soap_a_id'] = $soap_a[0]->id;
                                $this->Soap_a_action->whereFields['is_delete'] = 0;
                                $soap_a_action = $this->Soap_a_action->read();
                                if($soap_a_action) {
                                    for ($j=0; $j < count($soap_a_action); $j++) { 
                                        $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
                                        $dt_soap_inpatient[$x]->icd10[$j]['id'] = $soap_a_action[$j]->id;
                                        $dt_soap_inpatient[$x]->icd10[$j]['icd10_id'] = $soap_a_action[$j]->icd10_id;
                                        $dt_soap_inpatient[$x]->icd10[$j]['code'] = $icd10[0]->code;
                                        $dt_soap_inpatient[$x]->icd10[$j]['code_s'] = $icd10[0]->code_s;
                                        $dt_soap_inpatient[$x]->icd10[$j]['name'] = $icd10[0]->name;
                                        $dt_soap_inpatient[$x]->icd10[$j]['date_added'] = $soap_a_action[$j]->date_added;
                                        $dt_soap_inpatient[$x]->icd10[$j]['index'] = $soap_a_action[$j]->index;
                                    }
                                }
                            }
                            $doctor_in_charge_id = $dt_soap_inpatient[$x]->doctor_in_charge_id;
                            $soap_p = $this->db->query("
                                SELECT a.*
                                FROM soap_p as a
                                LEFT JOIN soap s ON a.soap_id = s.id
                                WHERE s.registration_destination_id = '$registration_destination_id'
                                ORDER BY a.id ASC
                            ")->result();
                            // AND (a.cppt_id IS NULL OR a.cppt_id = '')
                            $dt_soap_inpatient[$x]->icd9cm = array();
                            // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
                            if($soap_p) {
                                $idx_prescription = 0;
                                for ($spx=0; $spx < count($soap_p); $spx++) { 
                                    // PRESCRIPTION
                                    $soap_p_id = $soap_p[$spx]->id;
    
                                    // $this->db->cache_off();
                                    $prescription = $this->db->query("
                                        SELECT p.id,
                                        p.no_order,
                                        p.queue,
                                        p.type_prescription_id,
                                        h.name AS flag_prescription,
                                        p.pharmacy_depo_id,
                                        p.soap_p_id,
                                        p.doctor_in_charge_id,
                                        e.fullname AS doctor_in_charge_name,
                                        p.health_facility_item_id,
                                        p.flag,
                                        p.is_retur,
                                        p.date_added
                                        FROM pharmacy_prescription p
                                        LEFT JOIN employee e ON p.doctor_in_charge_id = e.id
                                        LEFT JOIN helper h ON p.type_prescription_id = h.code AND h.variable = 'type_prescription'
                                        WHERE p.soap_p_id = '$soap_p_id'
                                        AND p.is_delete = 0
                                        ORDER BY p.date_added DESC
                                    ")->result();
                                    // AND p.registration_destination_id = '$registration_destination_id'
                                        // AND (p.cppt_id IS NULL OR p.cppt_id = '')
                                    if ($prescription) {
                                        for ($k=0; $k < count($prescription); $k++) { 
                                            $dt_soap_inpatient[$x]->prescription['no_order'] = $prescription[$k]->no_order;
                                            $dt_soap_inpatient[$x]->prescription['queue'] = $prescription[$k]->queue;
                                            $dt_soap_inpatient[$x]->prescription['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                            $dt_soap_inpatient[$x]->prescription['type_prescription_name'] = $prescription[$k]->flag_prescription;
                                            $dt_soap_inpatient[$x]->prescription['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                                            $dt_soap_inpatient[$x]->prescription['soap_p_id'] = $prescription[$k]->soap_p_id;
                                            $dt_soap_inpatient[$x]->prescription['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                                            $dt_soap_inpatient[$x]->prescription['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                                            $dt_soap_inpatient[$x]->prescription['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                                            $dt_soap_inpatient[$x]->prescription['flag'] = $prescription[$k]->flag;
                                            $dt_soap_inpatient[$x]->prescription['is_retur'] = $prescription[$k]->is_retur;
                                            
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['no_order'] = $prescription[$k]->no_order;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['queue'] = $prescription[$k]->queue;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['type_prescription_name'] = $prescription[$k]->flag_prescription;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['pharmacy_depo_id'] = $prescription[$k]->pharmacy_depo_id;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['soap_p_id'] = $prescription[$k]->soap_p_id;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['doctor_in_charge_id'] = $prescription[$k]->doctor_in_charge_id;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['doctor_in_charge_name'] = $prescription[$k]->doctor_in_charge_name;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['health_facility_item_id'] = $prescription[$k]->health_facility_item_id;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['flag'] = $prescription[$k]->flag;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['is_retur'] = $prescription[$k]->is_retur;
                                            $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['date_added'] = $prescription[$k]->date_added;
                                            
                                            $prescription_id = $prescription[$k]->id;
                                            // // $this->db->cache_off();
                                            $form_prescription = $this->db->query("
                                                SELECT
                                                pi.pharmacy_prescription_id,
                                                pi.pharmacy_catalog_id,
                                                pc.name,
                                                pc.code,
                                                pi.prescription_item_type_id,
                                                pi.prescription_item_type_used,
                                                pi.prescription_item_type_value,
                                                pi.prescription_item_type_package,
                                                pi.package_id,
                                                pi.package_name,
                                                pi.dose,
                                                pi.dose_doctor,
                                                pi.batch,
                                                pi.expired,
                                                pi.qty_doctor,
                                                pi.qty,
                                                pi.qty_copy,
                                                pi.used,
                                                pi.desc,
                                                pi.sort
                                                FROM pharmacy_prescription_item pi
                                                LEFT JOIN pharmacy_catalog pc ON pi.pharmacy_catalog_id = pc.id
                                                WHERE pi.is_delete = 0
                                                AND pi.pharmacy_prescription_id = '$prescription_id'
                                                ORDER BY pi.sort ASC
                                            ")->result();
                                            if ($form_prescription != []) {
                                                $form_prescription_parent = array();
    
                                                for ($j=0; $j < count($form_prescription); $j++) { 
                                                    // $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                                                    // $dt_soap_inpatient[$x]->form_prescription[$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                                                    $dt_soap_inpatient[$x]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;
    
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_name'] = $form_prescription[$j]->name;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['pharmacy_catalog_code'] = $form_prescription[$j]->code;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['type_prescription_id'] = $prescription[$k]->type_prescription_id;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['package_id'] = $form_prescription[$j]->package_id;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['package_name'] = $form_prescription[$j]->package_name;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose'] = $form_prescription[$j]->dose;
                                                    // $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_name'] = $form_prescription[$j]->dose_name;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['batch'] = $form_prescription[$j]->batch;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['expired'] = $form_prescription[$j]->expired;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty'] = $form_prescription[$j]->qty;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['used'] = $form_prescription[$j]->used;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['desc'] = $form_prescription[$j]->desc;
                                                    $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription'][$j]['sort'] = $form_prescription[$j]->sort;
                                                
                                                    //------------------- detail form_prescription_parent -//
                                                    $parent_id = $form_prescription[$j]->prescription_item_type_id; 
                                                    $parent_used = $form_prescription[$j]->prescription_item_type_used; 
                                                    $parent_value = $form_prescription[$j]->prescription_item_type_value; 
                                                    $parent_package = $form_prescription[$j]->prescription_item_type_package; 
    
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
                                                    for ($ip=0; $ip < count($form_prescription_parent); $ip++) { 
                                                        if ($form_prescription_parent[$ip]['id'] == $parent_id) {
                                                            $skip = 1;
                                                        }
                                                    }
    
                                                    if ($skip == 0) {
                                                        array_push($form_prescription_parent, $dt_form_prescription_parent);
                                                    }
                                                    //----------------------------------------------------//
                                                }
                                                
                                                $dt_soap_inpatient[$x]->prescription_new[$idx_prescription]['form_prescription_parent'] = $form_prescription_parent;
                                                
                                                $dt_soap_inpatient[$x]->prescription_new = array_values($dt_soap_inpatient[$x]->prescription_new);
                                            } 
    
                                            $idx_prescription++;
                                        }
                                    }
                                }
    
                                $dt_soap_inpatient[$x]->soap_p_id = $soap_p[0]->id;
                                $dt_soap_inpatient[$x]->therapy_plan = $soap_p[0]->therapy_plan;
                                $dt_soap_inpatient[$x]->estimated_duration_treatment = $soap_p[0]->estimated_duration_treatment;
                                $dt_soap_inpatient[$x]->follow_up_care_arry = ($soap_p[0]->follow_up_care_arry != '') ? explode(',', $soap_p[0]->follow_up_care_arry): [];
                                $dt_soap_inpatient[$x]->diet_and_nutrition_type_id = $soap_p[0]->diet_and_nutrition_type_id;
                                $dt_soap_inpatient[$x]->diet_and_nutrition = $soap_p[0]->diet_and_nutrition;
                                $dt_soap_inpatient[$x]->wound_care_type_id = $soap_p[0]->wound_care_type_id;
                                $dt_soap_inpatient[$x]->wound_care = $soap_p[0]->wound_care;
                                $dt_soap_inpatient[$x]->medical_rehabilitation_type_id = $soap_p[0]->medical_rehabilitation_type_id;
                                $dt_soap_inpatient[$x]->medical_rehabilitation = $soap_p[0]->medical_rehabilitation;
                                $dt_soap_inpatient[$x]->pain_management_type_id = $soap_p[0]->pain_management_type_id;
                                $dt_soap_inpatient[$x]->pain_management = $soap_p[0]->pain_management;
                                $dt_soap_inpatient[$x]->discharge_planning_other = $soap_p[0]->discharge_planning_other;
    
                                // NOTE - KHUSUS KAJIAN IGD KARENA ADA SPESIALIS SOAP_P BISA LEBIH DARI SATU
                                for ($sp=0; $sp < count($soap_p); $sp++) { 
                                    // SOAP_P_ACTION
                                    $this->Soap_p_action->whereFields['soap_p_id'] = $soap_p[$sp]->id;
                                    $this->Soap_p_action->whereFields['a.is_delete'] = 0;
                                    $soap_p_action = $this->Soap_p_action->read();
                                    if($soap_p_action) {
                                        for ($j=0; $j < count($soap_p_action); $j++) { 
                                            $icd9cm = $this->Icd9cm->read($soap_p_action[$j]->icd9cm_id);
                                            
                                            $dt_icd9cm = array(
                                                'icd9cm_id' => $icd9cm[0]->id,
                                                'code' => $icd9cm[0]->code,
                                                'code_s' => $icd9cm[0]->code_s,
                                                'name' => $icd9cm[0]->name,
                                            );
    
                                            array_push($dt_soap_inpatient[$x]->icd9cm, $dt_icd9cm);
                                        }
                                    }
                                }
                            }
    
                            $select = 'a.name';
                            $where = array('norm' => $dt_sep[0]->norm, 'a.is_delete' => 0);
                            $join = array(
                                array('helper h', 'a.type_allergy_id = h.code AND h.variable = "type_allergy"', 'LEFT'),
                            );
                            $allergies = $this->Patient_allergy->select(0, $select, $where, $join);
                            if (count($allergies) > 0) {
                                $dt_soap_inpatient[$x]->allergies = $allergies;
                            } else {
                                $dt_soap_inpatient[$x]->allergies = [];
                            }
                            $dt_sep[$i]->assesment_inpatient =  $dt_soap_inpatient;
                        }
                    }
                }
                
                // LABOR

                $where_lab = [];
                $where_lab['rd.registration_id'] = $dt_sep[$i]->registration_id;
                $select_lab = 'a.id, a.no_request, a.date_request, a.date_result, a.status, s.id as soap_id, a.doctor_in_charge_id, e.fullname as doctor_in_charge_name, a.queue, a.status,
                rd.health_facility_item_id,
                hfi.name as health_facility_item_name,
                hfi.health_facility_id,
                hf.name as health_facility_name
                ';
                $where_lab['a.is_delete'] = 0;
                $join_main = array(
                    array('soap s', 'a.soap_id = s.id', 'INNER'),
                    array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
                    array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
                    array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
                    array('registration r', 'rd.registration_id = r.id', 'INNER'),
                    array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
                );
                $order_main = array('a.date_request' => 'DESC');
                $output_lab = $this->Laboratory_request->select(0, $select_lab, $where_lab, $join_main, $order_main);

                for ($i_lab=0; $i_lab < count($output_lab); $i_lab++) { 
                    $laboratory_request_id = $output_lab[$i_lab]->id;

                    $select_lab_item = 'a.id, a.laboratory_request_id, a.laboratory_package_id, lp.name as laboratory_package_name, a.service_class_id, lp.code_lis, a.status, a.url';
                    $where_lab_item = array(
                        'a.laboratory_request_id' => $laboratory_request_id,
                        'a.is_delete' => 0,
                    );
                    $join_lab_item = array(
                        array('laboratory_package lp', 'a.laboratory_package_id = lp.id', 'INNER'),
                    );
                    $laboratory_request_items = $this->Laboratory_request_item->select(0, $select_lab_item, $where_lab_item, $join_lab_item);

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
                    }

                    $dt_sep[$i]->laboratory = $laboratory_request_items;
                }
            
                // LABOR

                // RADIOLOGI

                $where_rad = [];
                $where_rad['rd.registration_id'] = $dt_sep[$i]->registration_id;
                
                $select_rad = 'a.id, a.no_request, a.date_request, a.status, a.clinical_indications, s.id as soap_id, a.doctor_in_charge_id, e.fullname as doctor_in_charge_name, a.queue,
                rd.health_facility_item_id,
                hfi.name as health_facility_item_name,
                hfi.health_facility_id,
                hf.name as health_facility_name
                ';
                $where_rad['a.is_delete'] = 0;
                $join_rad = array(
                    array('soap s', 'a.soap_id = s.id', 'INNER'),
                    array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
                    array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
                    array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
                    array('registration r', 'rd.registration_id = r.id', 'INNER'),
                    array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
                );
                $order_rad = array('a.date_request' => 'DESC');
                $output_rad = $this->Radiology_request->select(0, $select_rad, $where_rad, $join_rad, $order_rad);

                for ($i_rad=0; $i_rad < count($output_rad); $i_rad++) { 
                    $radiology_request_id = $output_rad[$i_rad]->id;

                    $select_rad_item = 'a.id, a.radiology_request_id, a.radiology_package_id, rp.name as radiology_package_name, a.service_class_id, a.status';
                    $where_rad_item = array(
                        'a.radiology_request_id' => $radiology_request_id,
                        'a.is_delete' => 0,
                    );
                    $join_rad_item = array(
                        array('radiology_package rp', 'a.radiology_package_id = rp.id', 'INNER'),
                    );
                    $radiology_request_items = $this->Radiology_request_item->select(0, $select_rad_item, $where_rad_item, $join_rad_item);

                    $url_true = 0;
                    
                    for ($k=0; $k < count($radiology_request_items); $k++) { 
                        $radiology_request_item_id = $radiology_request_items[$k]->id;

                        $select_rad_item_img = 'a.id,
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
                        $where_rad_item_img = array(
                            'a.radiology_request_item_id' => $radiology_request_item_id,
                            'a.is_delete' => 0,
                        );
                        $join_rad_item_img = array(
                            array('employee e', 'a.doctor_lab_id = e.id', 'LEFT'),
                            array('employee e2', 'a.user_lab_id = e2.id', 'LEFT'),
                        );
                        $url = $this->Radiology_request_item_image->select(0, $select_rad_item_img, $where_rad_item_img, $join_rad_item_img);

                        for ($l=0; $l < count($url); $l++) { 
                            $url_raw = $url[$l]->url;
                            $name = substr($url_raw, 20);

                            $url[$l]->radiology_request_id = $radiology_request_id;
                            $url[$l]->name = $name;
                            $url_true = 1;
                        }

                        $radiology_request_items[$k]->url = $url;
                    }

                    $dt_sep[$i]->radiology = $radiology_request_items;
                }

                // RADIOLOGI

                // RESUME

                $this->Resume->whereFields['a.is_delete'] = 0;
                $this->Resume->whereFields['a.registration_destination_id'] = $registration_destination_id;
                $output_resume = $this->Resume->read(0,false);

                if ($output_resume) {
                    for ($i_resume=0; $i_resume < count($output_resume); $i_resume++) { 
                        $id_resume = $output_resume[$i_resume]->id;
                        $soap_resume_id = $output_resume[$i_resume]->soap_id;
                        $registration_destination_id_resume = $output_resume[$i_resume]->registration_destination_id;
                        
                        $select_resume = 'a.significant_medications, a.take_home_medications, a.exit_id, a.exit_anythingelse, a.exit_condition_type_id, a.exit_condition_anythingelse, a.care_instruction_control, a.care_instruction_control_date, a.care_instruction_anythingelse, a.summary_medical_history, a.home_education, a.indication_for_hospitalization';
                        $soap_resume = $this->Soap->select($soap_resume_id, $select_resume);
                        foreach ($soap[0] as $key => $val) {
                            $output_resume[$i_resume]->$key = $val;
                        }
    
                        $select_resume_o = '
                        a.head,
                        a.head_hair_color,
                        a.head_hair_condition,
                        a.eye_conjunctive,
                        a.eye_sclera,
                        a.eye_pupil,
                        a.eye_light_reflex,
                        a.eye_blind_color,
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
                        a.neurologist_status
                        ';
                        $where_soap_o = array('a.soap_id' => $soap_resume_id);
                        $soap_o_resume = $this->Soap_o->select(0, $select_resume_o, $where_soap_o);
    
                        $output_resume[$i_resume]->head = $soap_o_resume[0]->head;
                        $output_resume[$i_resume]->head_hair_color = $soap_o_resume[0]->head_hair_color;
                        $output_resume[$i_resume]->head_hair_condition = $soap_o_resume[0]->head_hair_condition;
                        $output_resume[$i_resume]->eye_conjunctive = $soap_o_resume[0]->eye_conjunctive;
                        $output_resume[$i_resume]->eye_sclera = $soap_o_resume[0]->eye_sclera;
                        $output_resume[$i_resume]->eye_pupil = $soap_o_resume[0]->eye_pupil;
                        $output_resume[$i_resume]->eye_light_reflex = $soap_o_resume[0]->eye_light_reflex;
                        $output_resume[$i_resume]->eye_blind_color = $soap_o_resume[0]->eye_blind_color;
                        $output_resume[$i_resume]->mouth_mucosa = $soap_o_resume[0]->mouth_mucosa;
                        $output_resume[$i_resume]->mouth_discoloration = $soap_o_resume[0]->mouth_discoloration;
                        $output_resume[$i_resume]->mouth_tongue = $soap_o_resume[0]->mouth_tongue;
                        $output_resume[$i_resume]->mouth_caries = $soap_o_resume[0]->mouth_caries;
                        $output_resume[$i_resume]->neck_kgb = $soap_o_resume[0]->neck_kgb;
                        $output_resume[$i_resume]->neck_trachea_position = $soap_o_resume[0]->neck_trachea_position;
                        $output_resume[$i_resume]->neck_massa = $soap_o_resume[0]->neck_massa;
                        $output_resume[$i_resume]->neck_jvp = $soap_o_resume[0]->neck_jvp;
                        $output_resume[$i_resume]->thorax_insp_chest = $soap_o_resume[0]->thorax_insp_chest;
                        $output_resume[$i_resume]->thorax_insp_retraction = $soap_o_resume[0]->thorax_insp_retraction;
                        $output_resume[$i_resume]->thorax_palp_chest = $soap_o_resume[0]->thorax_palp_chest;
                        $output_resume[$i_resume]->thorax_palp_fremitus = $soap_o_resume[0]->thorax_palp_fremitus;
                        $output_resume[$i_resume]->thorax_palp_crepitation = $soap_o_resume[0]->thorax_palp_crepitation;
                        $output_resume[$i_resume]->thorax_ausk_pul_vasiculer = $soap_o_resume[0]->thorax_ausk_pul_vasiculer;
                        $output_resume[$i_resume]->thorax_ausk_pul_rhonki = $soap_o_resume[0]->thorax_ausk_pul_rhonki;
                        $output_resume[$i_resume]->thorax_ausk_pul_wheezing = $soap_o_resume[0]->thorax_ausk_pul_wheezing;
                        $output_resume[$i_resume]->thorax_ausk_cor_heart_sound = $soap_o_resume[0]->thorax_ausk_cor_heart_sound;
                        $output_resume[$i_resume]->thorax_ausk_cor_murmur = $soap_o_resume[0]->thorax_ausk_cor_murmur;
                        $output_resume[$i_resume]->thorax_ausk_cor_gallop = $soap_o_resume[0]->thorax_ausk_cor_gallop;
                        $output_resume[$i_resume]->abdomen_supel = $soap_o_resume[0]->abdomen_supel;
                        $output_resume[$i_resume]->abdomen_bowel_sounds = $soap_o_resume[0]->abdomen_bowel_sounds;
                        $output_resume[$i_resume]->abdomen_tenderness = $soap_o_resume[0]->abdomen_tenderness;
                        $output_resume[$i_resume]->abdomen_hepar_lien = $soap_o_resume[0]->abdomen_hepar_lien;
                        $output_resume[$i_resume]->abdomen_other_physical_check = $soap_o_resume[0]->abdomen_other_physical_check;
                        $output_resume[$i_resume]->extremity_acral = $soap_o_resume[0]->extremity_acral;
                        $output_resume[$i_resume]->extremity_crt = $soap_o_resume[0]->extremity_crt;
                        $output_resume[$i_resume]->extremity_edema = $soap_o_resume[0]->extremity_edema;
                        $output_resume[$i_resume]->genitalia = $soap_o_resume[0]->genitalia;
                        $output_resume[$i_resume]->localist_status = $soap_o_resume[0]->localist_status;
                        $output_resume[$i_resume]->ophthal_status = $soap_o_resume[0]->ophthal_status;
                        $output_resume[$i_resume]->neurologist_status = $soap_o_resume[0]->neurologist_status;
    
                        $soap_a_resume = $this->db->query("
                            SELECT a.id
                            FROM soap_a as a
                            WHERE soap_id = '$soap_resume_id'
                            AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                            ORDER BY a.id ASC
                        ")->result();
                        
                        $soap_a_resume_id = $soap_a_resume[0]->id;
    
                        $select_soap_a_action = 'a.icd10_id, i.code as icd10_code, i.code_s as icd10_code_s, i.name as icd10_name';
                        $where_soap_a_action = array('soap_a_id' => $soap_a_resume_id, 'a.is_delete' => 0);
                        $join_soap_a_action = array(
                            array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
                        );
                        $order_soap_a_action = array(
                            'a.index' => 'ASC',
                        );
                        $soap_a_resume_action = $this->Soap_a_action->select(0, $select_soap_a_action, $where_soap_a_action, $join_soap_a_action, $order_soap_a_action);
                        
                        $output_resume[$i_resume]->icd10 = $soap_a_resume_action;
    
                        $select_soap_p = 'a.id';
                        $where_soap_p = array('soap_id' => $soap_resume_id);
                        $soap_p_resume = $this->Soap_p->select(0, $select_soap_p, $where_soap_p);
                        
                        $soap_p_resume_id = $soap_p_resume[0]->id;
    
                        $select_soap_p_action = 'a.icd9cm_id, i.code as icd9cm_code, i.code_s as icd9cm_code_s, i.name as icd9cm_name';
                        $where_soap_p_action = array('soap_p_id' => $soap_p_resume_id, 'a.is_delete' => 0);
                        $join_soap_p_action = array(
                            array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
                        );
                        $soap_p_resume_action = $this->Soap_p_action->select(0, $select_soap_p_action, $where_soap_p_action, $join_soap_p_action);
                        
                        $output_resume[$i_resume]->icd9cm = $soap_p_resume_action;
    
                        $output_resume[$i_resume]->condition = "";
                        $output_resume[$i_resume]->indication = "";
                        $output_resume[$i_resume]->plan = "";
    
                        $select_soap_inpatient_resume = 'a.condition, a.indication, a.plan';
                        $where_soap_inpatient_resume = array(
                            'a.registration_destination_id' => $registration_destination_id_resume
                        );
                        $soap_inpatient = $this->Soap_inpatient->select(0, $select_soap_inpatient_resume, $where_soap_inpatient_resume);
    
                        if (count($soap_inpatient) > 0) {
                            $condition = $soap_inpatient[0]->condition;
                            $i_resumendication = $soap_inpatient[0]->indication;
                            $plan = $soap_inpatient[0]->plan;
    
                            $output_resume[$i_resume]->condition = $condition;
                            $output_resume[$i_resume]->indication = $i_resumendication;
                            $output_resume[$i_resume]->plan = $plan;
                        }
    
                        $select_resume_item = 'a.item_id, lri.costing_name, lri.laboratory_package_id, lri.laboratory_request_id, lp.code_lis';
                        $where_resume_item = array('resume_item_type_id' => 1, 'a.is_delete' => 0);
                        $join_resume_item = array(
                            array('laboratory_request_item lri', 'a.item_id = lri.id', 'INNER'),
                            array('laboratory_package lp', 'lri.laboratory_package_id = lp.id', 'INNER'),
                        );
                        $laboratory_request_items_resume = $this->Resume_item->select($id_resume, $select_resume_item, $where_resume_item, $join_resume_item);
                        
                        for ($k=0; $k < count($laboratory_request_items_resume); $k++) { 
                            $laboratory_request_id = $laboratory_request_items_resume[$k]->laboratory_request_id;
                            
                            // LIS RESULT
                            $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);
    
                            $code_lis = $laboratory_request_items_resume[$k]->code_lis;
                            $laboratory_request_items_resume[$k]->result = [];
    
                            if (count($lis_response) > 0) {
                                $result = $lis_response[0]['result_detail'];
    
                                for ($l=0; $l < count($result); $l++) { 
                                    $flag = $result[$l]['flag'];
                                    $parameter_id = $result[$l]['ParameterID'];
                                    
                                    if ($parameter_id == $code_lis) {
                                        array_push($laboratory_request_items_resume[$k]->result, $result[$l]);
                                    }
                                }
                            }
                        }
    
                        $output_resume[$i_resume]->laboratory_request_items_resume = $laboratory_request_items_resume;
    
                        $select_resume_item = 'a.item_id, rri.costing_name, rri.radiology_package_id, rri.radiology_request_id';
                        $where_resume_item = array('resume_item_type_id' => 2, 'a.is_delete' => 0);
                        $join_resume_item = array(
                            array('radiology_request_item rri', 'a.item_id = rri.id', 'INNER'),
                        );
                        $radiology_request_items_resume = $this->Resume_item->select($id_resume, $select_resume_item, $where_resume_item, $join_resume_item);
                        
                        for ($k=0; $k < count($radiology_request_items_resume); $k++) { 
                            $radiology_request_item_id = $radiology_request_items_resume[$k]->item_id;
                            
                            $select_rad_image = 'a.*';
                            $where_rad_image = array(
                                'a.radiology_request_item_id' => $radiology_request_item_id, 
                                'a.is_delete' => 0
                            );
                            $radiology_request_item_image = $this->Radiology_request_item_image->select(0, $select_rad_image, $where_rad_image);
                            
                            for ($kk=0; $kk < count($radiology_request_item_image); $kk++) { 
                                $url_raw = $radiology_request_item_image[$kk]->url;
                                $name = substr($url_raw, 20);
    
                                $radiology_request_item_image[$kk]->radiology_request_id = $radiology_request_items_resume[$k]->radiology_request_id;
                                $radiology_request_item_image[$kk]->name = $name;
                            }
                            
                            $radiology_request_items_resume[$k]->url = $radiology_request_item_image;
                        }
    
    
                        $output_resume[$i_resume]->radiology_request_items_resume = $radiology_request_items_resume;
    
                        $select_resume_item = 'a.item_id, pri.costing_name, pri.physiotherapy_package_id';
                        $where_resume_item = array('resume_item_type_id' => 3, 'a.is_delete' => 0);
                        $join_resume_item = array(
                            array('physiotherapy_request_item pri', 'a.item_id = pri.id', 'INNER'),
                        );
                        $physiotherapy_request_items_resume = $this->Resume_item->select($i_resumed, $select_resume_item, $where_resume_item, $join_resume_item);
    
                        $output_resume[$i_resume]->physiotherapy_request_items_resume = $physiotherapy_request_items_resume;
                    }
                
                    $dt_sep[$i]->resume = $output_resume;
                }


                // RESUME

            }
        }

        $response_data = $dt_sep;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function detail_jkn_get($p = 0) {
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
    
        $soap_id = $id;
    
        if ($soap_id === 0 || $soap_id === "0") {
            $this->REST_Return(400, 'soap_id is required');
            return;
        }
    
        $select = 'a.id, a.registration_destination_id';
        $soap = $this->$model->select($soap_id, $select);
    
        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        $registration_destination_id = $soap[0]->registration_destination_id;

        $output = $soap;

        if (count($output) > 0) {

            $select = 'a.id, a.exit_id, a.exit_reason, p.name, e.name as doctor_in_charge_name, a.type_emergency_id, a.register_date, a.checkout_date, ri.sep, hfi.health_facility_id, h.name as type_emergency_name, r.type_visit_id, h2.name as type_visit_name, a.sts';
            $where = array();
            $join = array(
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'LEFT'),
                array('registration r', 'a.registration_id = r.id', 'LEFT'),
                array('patient p', 'r.norm = p.norm', 'LEFT'),
                array('employee e', 'a.doctor_in_charge_id = e.id', 'LEFT'),
                array('registration_insurance ri', 'a.id = ri.registration_destination_id', 'LEFT'),
                array('helper h', "a.type_emergency_id = h.code and h.variable='type_emergency'", 'LEFT'),
                array('helper h2', "r.type_visit_id = h2.code and h2.variable='type_visit'", 'LEFT'),
            );
            $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where, $join);

            if ($registration_destination[0]->sts != 8) {
                // $this->update_eklaim_get($soap_id);
            }

            $output[0]->exit_id = $registration_destination[0]->exit_id;
            $output[0]->exit_reason = $registration_destination[0]->exit_reason;
            $output[0]->type_emergency_id = $registration_destination[0]->type_emergency_id;
            $output[0]->type_emergency_name = $registration_destination[0]->type_emergency_name;
            $output[0]->type_visit_id = $registration_destination[0]->type_visit_id;
            $output[0]->type_visit_name = $registration_destination[0]->type_visit_name;

            $select = 'a.id, a.anamesa_type_id, a.main_complaint, a.history_disease_now, a.history_disease_past, a.history_drugs, a.family_name, a.family_relation_id';
            $where = array('soap_id' => $soap_id);
            $soap_s = $this->Soap_s->select(0, $select, $where);
    
            $select = 'a.id, a.anythingelse, a.icd10_notes, a.non_icd10';
            $where = array('soap_id' => $soap_id);
            $soap_a = $this->Soap_a->select(0, $select, $where);

            $soap_a[0]->non_icd10 = ($soap_a[0]->non_icd10 != "") ? explode('|', $soap_a[0]->non_icd10) : [];

            $soap_a_id = $soap_a[0]->id;

            $select = 'a.id, a.icd10_id, i.code as icd10_code, i.name as icd10_name';
            $where = array('soap_a_id' => $soap_a_id, 'a.is_delete' => 0);
            $join = array(
                array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join);

            $select = 'a.id, a.therapy_plan';
            $where = array('soap_id' => $soap_id);
            $soap_p = $this->Soap_p->select(0, $select, $where);
            $soap_p_id = $soap_p[0]->id;
            if ($soap_p_id) {
                $select = 'a.id, a.icd9cm_id, i.code as icd9cm_code, i.name as icd9cm_name';
                $where = array('soap_p_id' => $soap_p_id, 'a.is_delete' => 0);
                $join = array(
                    array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
                );
                $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);
            }

            $select = 'a.id, a.systolic_blood_pressure, a.diastolic_blood_pressure, a.pulse_frequency, a.breath_frequency, a.oximetry_frequency, a.temperature, a.blood_sugar, a.sore_score, a.ews, a.weight, a.height, a.imt, a.head_circumference, a.chest_circumference, a.arm_circumference, a.stomach_circumference, a.knee_circumference';
            $where = array('a.registration_destination_id' => $registration_destination_id, 'a.is_delete' => 0);
            $join = array();
            $order = array('a.date_added' => 'DESC');
            $nurse = $this->Nurse->select(0, $select, $where, $join, $order);

            $output[0]->soap_s = $soap_s[0];
            $output[0]->soap_a = $soap_a[0];
            $output[0]->soap_p = $soap_p[0];
            $output[0]->icd10 = $soap_a_action;
            $output[0]->icd9cm = $soap_p_action;
            $output[0]->nurse = $nurse[0];
            $this->load->model("v1/finance/Medical_services_model", "Medical_services");
            $select_m = 'a.id,a.json_obj'; 
            $join_m = array();
            $where_m['a.registration_destination_id'] = $registration_destination_id;
            $medical_services = $this->Medical_services->select(0, $select_m, $where_m);
            // if ($registration_destination[0]->health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                if (!$medical_services) {
                    $request ='{
                                "metadata": {
                                    "method":"get_claim_data"
                                },
                                "data": {
                                    "nomor_sep":"'.$registration_destination[0]->sep.'"
                                }
                            }';
                    $msg = $this->Request($request);
                    $output[0]->inacbgs = $msg;
                    $dt_medical_services = array(
                        'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                        'registration_destination_id' => $registration_destination_id,
                        'sep' => $registration_destination[0]->sep,
                        'name' => $registration_destination[0]->name,
                        'doctor_in_charge_name' => $registration_destination[0]->doctor_in_charge_name,
                        'health_facility_id' => $registration_destination[0]->health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' ? 'RAWAT INAP' : 'RAWAT JALAN',
                        'checkin_date' => $registration_destination[0]->register_date,
                        'checkout_date' => $registration_destination[0]->checkout_date,
                        'percentage' => 25,
                    );
                    // echo "Data : ".var_dump($dt_medical_services);
                    // echo "Data : ".var_dump($msg);die();
                    $this->Medical_services->create($dt_medical_services);
                } else {
                    $request ='{
                                "metadata": {
                                    "method":"get_claim_data"
                                },
                                "data": {
                                    "nomor_sep":"'.$registration_destination[0]->sep.'"
                                }
                            }';
                    $msg = $this->Request($request);
                    
                    // echo "Data : ".$request;
                    // echo "Data : ".var_dump($msg);die();
                    $cbg                = $msg['response']['data']['grouper']['response']['cbg']['tariff'];
                    $sub_acute          = $msg['response']['data']['sub_acute']['tariff'];
                    $chronic            = $msg['response']['data']['chronic']['tariff'];
                    $add_payment_amt    = $msg['response']['data']['add_payment_amt'];
                    
                    // echo "Data : ".var_dump($msg['response']['data']['grouper']['response']['cbg']);
                    // echo "Data : ".var_dump($msg['response']);
                    $dt_medical_services = array(
                        'id' => $medical_services[0]->id,
                        'code_inacbg' => $msg['response']['data']['grouper']['response']['cbg']['code'],
                        'desc_inacbg' => $msg['response']['data']['grouper']['response']['cbg']['description'],
                        'cost_inacbg' => $cbg+$sub_acute+$chronic+$add_payment_amt,
                        'json_obj' => json_encode($msg),
                    );
                    // echo "Data : ".var_dump($dt_medical_services);die();
                    $this->Medical_services->replace($dt_medical_services);

                    $select_m = 'a.json_obj'; 
                    $join_m = array();
                    $where_m['a.registration_destination_id'] = $registration_destination_id;
                    $medical_services = $this->Medical_services->select(0, $select_m, $where_m);

                    $output[0]->inacbgs = $medical_services[0]->json_obj ? json_decode($medical_services[0]->json_obj) : null;
                }
            // } else {
            //     $output[0]->inacbgs = $medical_services[0]->json_obj ? json_decode($medical_services[0]->json_obj) : null;
            // }
        }
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_bulk_eklaim_get($p = 0) {
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
    
        if ($q_strings['date_start'] == null || $q_strings['date_start'] == "" || $q_strings['date_end'] == null || $q_strings['date_end'] == "") {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        }
        $where = $q_strings;
    
        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d');
    
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
    
            unset($where['date_start']);
            unset($where['date_end']);
        } 
        
        // $where['ri.sep'] = '0070R0321025V002797';
        // $where['ri.sep'] = '0070R0321025V004733';
        // $where['hfi.health_facility_id'] = '3cb626f5-9148-4fb1-8663-3d7f028b01a6';
        // $where['hfi.health_facility_id'] = '40f06534-07ec-4221-ad9d-dea08196079f';
        $where['hfi.health_facility_id != '] = '40f06534-07ec-4221-ad9d-dea08196079f';
        $where['rd.register_date >='] = $date_start;
        $where['rd.register_date <='] = $date_end;
        $where['ms.cost_inacbg <='] = 2;
        $where['rd.is_cancel'] = 0;
    
        $select = 'a.id, ms.code_inacbg,rd.id as registration_destination_id,rd.register_date,rd.doctor_in_charge_id,p.name,rd.checkin_date,rd.checkout_date, rd.sts, ri.sep, e.fullname as doctor_in_charge_name, hf.name as health_facility_name, hf.id as health_facility_id';
        $join = array(
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('medical_services ms', 'rd.id = ms.registration_destination_id', 'LEFT'),
        );
        $data = $this->Registration->select(0, $select, $where, $join);
        // http://localhost/be-ci/v1/mr/soap/update_bulk_eklaim?date_start=2025-08-15&date_end=2025-08-31
    
        if (count($data) == 0) {
            $this->REST_Return(404, 'there is no data' . $data);
            return;
        }

        if (count($data) > 0) {
            for ($i=0; $i < count($data); $i++) { 
                // PX BPJS
                if ($data[$i]->sep) {
                    $this->load->model("v1/finance/Medical_services_model", "Medical_services");
                    $select_m = 'a.id,a.json_obj,a.code_inacbg'; 
                    $join_m = array();
                    $where_m['a.registration_destination_id'] = $data[$i]->registration_destination_id;
                    $medical_services = $this->Medical_services->select(0, $select_m, $where_m);
                    if ($medical_services) {
                    //     $request ='{
                    //                 "metadata": {
                    //                     "method":"get_claim_data"
                    //                 },
                    //                 "data": {
                    //                     "nomor_sep":"'.$data[$i]->sep.'"
                    //                 }
                    //             }';
                    //     $msg = $this->Request($request);
                    //     $output[0]->inacbgs = $msg;
                    //     $dt_medical_services = array(
                    //         'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                    //         'registration_destination_id' => $data[$i]->registration_destination_id,
                    //         'sep' => $data[$i]->sep,
                    //         'name' => $data[$i]->name,
                    //         'doctor_in_charge_name' => $data[$i]->doctor_in_charge_id,
                    //         'health_facility_id' => $data[$i]->health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f' ? '40f06534-07ec-4221-ad9d-dea08196079f' : '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
                    //         'checkin_date' => $data[$i]->register_date,
                    //         'checkout_date' => $data[$i]->checkout_date,
                    //         'percentage' => 25,
                    //     );
                    //     // echo "Data : ".var_dump($dt_medical_services);
                    //     // echo "Data : ".var_dump($msg);die();
                    //     $this->Medical_services->create($dt_medical_services);
                    // } else {
                        // if ($medical_services[0]->code_inacbg == null) {
                            $request ='{
                                        "metadata": {
                                            "method":"get_claim_data"
                                        },
                                        "data": {
                                            "nomor_sep":"'.$data[$i]->sep.'"
                                        }
                                    }';
                            $msg = $this->Request($request);
                            if ($msg['metadata']['code'] != 400) {
                                $cbg                = $msg['response']['data']['grouper']['response_inacbg']['base_tariff'];
                                $tariff             = $msg['response']['data']['grouper']['response_inacbg']['tariff'];
                                $sub_acute          = 0;
                                $chronic            = 0;
                                $add_payment_amt    = 0;
                                // $sub_acute          = $msg['response']['data']['sub_acute']['tariff'];
                                // $chronic            = $msg['response']['data']['chronic']['tariff'];
                                // $add_payment_amt    = $msg['response']['data']['add_payment_amt'];
    
                                $prosedur_non_bedah = $msg['response']['data']['tarif_rs']['prosedur_non_bedah'];
                                $prosedur_bedah     = $msg['response']['data']['tarif_rs']['prosedur_bedah'];
                                $konsultasi         = $msg['response']['data']['tarif_rs']['konsultasi'];
                                $tenaga_ahli        = $msg['response']['data']['tarif_rs']['tenaga_ahli'];
                                $keperawatan        = $msg['response']['data']['tarif_rs']['keperawatan'];
                                $penunjang          = $msg['response']['data']['tarif_rs']['penunjang'];
                                $radiologi          = $msg['response']['data']['tarif_rs']['radiologi'];
                                $laboratorium       = $msg['response']['data']['tarif_rs']['laboratorium'];
                                $pelayanan_darah    = $msg['response']['data']['tarif_rs']['pelayanan_darah'];
                                $rehabilitasi       = $msg['response']['data']['tarif_rs']['rehabilitasi'];
                                $kamar              = $msg['response']['data']['tarif_rs']['kamar'];
                                $rawat_intensif     = $msg['response']['data']['tarif_rs']['rawat_intensif'];
                                $obat               = $msg['response']['data']['tarif_rs']['obat'];
                                $obat_kronis        = $msg['response']['data']['tarif_rs']['obat_kronis'];
                                $obat_kemoterapi    = $msg['response']['data']['tarif_rs']['obat_kemoterapi'];
                                $alkes              = $msg['response']['data']['tarif_rs']['alkes'];
                                $bmhp               = $msg['response']['data']['tarif_rs']['bmhp'];
                                $sewa_alat          = $msg['response']['data']['tarif_rs']['sewa_alat'];
    
                                $cost_hospital = $prosedur_non_bedah+$prosedur_bedah+$konsultasi+$tenaga_ahli+$keperawatan+$penunjang+$radiologi+$laboratorium+$pelayanan_darah+$rehabilitasi+$kamar+$rawat_intensif+$obat+$obat_kronis+$obat_kemoterapi+$alkes+$bmhp+$sewa_alat;
    
                                // echo "<pre>";
                                // echo "Data : ".$request;
                                // echo "Data : ".var_dump($msg);die();
                                // echo "Data : ".var_dump($msg['response']['data']['grouper']['response']['cbg']);
                                // echo "Data : ".var_dump($msg['response']);
                                $dt_medical_services = array(
                                    'id' => $medical_services[0]->id,
                                    'code_inacbg' => $msg['response']['data']['grouper']['response_inacbg']['cbg']['code'],
                                    'desc_inacbg' => $msg['response']['data']['grouper']['response_inacbg']['cbg']['description'],
                                    'doctor_in_charge_name' => $data[$i]->doctor_in_charge_id,
                                    'cost_inacbg' => $cbg+$sub_acute+$chronic+$add_payment_amt,
                                    'cost_inacbg_tariff' => $tariff,
                                    'cost_hostpital' => $cost_hospital,
                                    'json_obj' => json_encode($msg),
                                );
                                // echo "Data : ".var_dump($dt_medical_services);die();
                                $this->Medical_services->replace($dt_medical_services);
                
                                // $select_m = 'a.json_obj'; 
                                // $join_m = array();
                                // $where_m['a.registration_destination_id'] = $registration_destination_id;
                                // $medical_services = $this->Medical_services->select(0, $select_m, $where_m);
                            }
                            // }
                    }
                    // $data[$i]->inacbgs = $medical_services[0]->json_obj ? json_decode($medical_services[0]->json_obj) : null;
                }
                // PX NON BPJS

            }
        }
        $response_data = $data;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_jkn_put($p = 0) {
        $this->load->helper('logger');
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
        $this->form_validation->set_rules($this->update_jkn_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        Logger::info($this->debug_uniq, "|---- start update jkn ----|");
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $soap_id = $id;
    
        if ($soap_id === 0 || $soap_id === "0") {
            $this->REST_Return(400, 'soap_id is required');
            return;
        }

        $select = 'a.id, a.registration_destination_id';
        $soap = $this->$model->select($soap_id, $select);
    
        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        $registration_destination_id = $soap[0]->registration_destination_id;

        $select = 'a.id, a.registration_id, a.health_facility_item_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        $registration_id = $registration_destination[0]->registration_id;
        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;

        $select = 'a.id, a.systolic_blood_pressure, a.diastolic_blood_pressure, a.pulse_frequency, a.breath_frequency, a.oximetry_frequency, a.temperature, a.blood_sugar, a.sore_score, a.ews, a.weight, a.height, a.imt, a.head_circumference, a.chest_circumference, a.arm_circumference, a.stomach_circumference, a.knee_circumference';
        $where = array('a.registration_destination_id' => $registration_destination_id, 'a.is_delete' => 0);
        $join = array();
        $order = array('a.date_added' => 'DESC');
        $nurse = $this->Nurse->select(0, $select, $where, $join, $order);

        $nurse_id = $nurse[0]->id;

        Logger::info($this->debug_uniq, 'nurse_id: ' . $nurse_id);

        $select = 'a.id';
        $where = array('soap_id' => $soap_id);
        $soap_s = $this->Soap_s->select(0, $select, $where);

        $soap_s_id = $soap_s[0]->id;

        Logger::info($this->debug_uniq, 'soap_s_id: ' . $soap_s_id);

        $select = 'a.id';
        $where = array('soap_id' => $soap_id);
        $soap_o = $this->Soap_o->select(0, $select, $where);

        $soap_o_id = $soap_o[0]->id;

        Logger::info($this->debug_uniq, 'soap_o_id: ' . $soap_o_id);

        $select = 'a.id';
        $where = array('soap_id' => $soap_id);
        $soap_a = $this->Soap_a->select(0, $select, $where);

        $soap_a_id = $soap_a[0]->id;

        Logger::info($this->debug_uniq, 'soap_a_id: ' . $soap_a_id);

        $select = 'a.id';
        $where = array('soap_id' => $soap_id);
        $soap_p = $this->Soap_p->select(0, $select, $where);

        $soap_p_id = $soap_p[0]->id;

        Logger::info($this->debug_uniq, 'soap_p_id: ' . $soap_p_id);

        // data registration
        $type_visit_id = $dt_put['type_visit_id'];

        // data registration_destination
        $type_emergency_id = $dt_put['type_emergency_id'];
        $exit_id = $dt_put['exit_id'] != null ? $dt_put['exit_id'] : "";

        // data soap_s
        $anamesa_type_id = $dt_put['anamesa_type_id'];
        $main_complaint = $dt_put['main_complaint'];
        $history_disease_now = $dt_put['history_disease_now'];
        $history_disease_past = $dt_put['history_disease_past'];
        $history_drugs = $dt_put['history_drugs'];
        $family_name = $dt_put['family_name'];
        $family_relation_id = $dt_put['family_relation_id'];

        // data soap_o
        $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $oximetry_frequency = $dt_put['oximetry_frequency'];
        $temperature = $dt_put['temperature'];
        $blood_sugar = $dt_put['blood_sugar'];
        $sore_score = $dt_put['sore_score'];
        $ews = $dt_put['ews'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $imt = $dt_put['imt'];
        $head_circumference = $dt_put['head_circumference'];
        $chest_circumference = $dt_put['chest_circumference'];
        $arm_circumference = $dt_put['arm_circumference'];
        $stomach_circumference = $dt_put['stomach_circumference'];
        $knee_circumference = $dt_put['knee_circumference'];

        // data soap_a
        $anythingelse = $dt_put['anythingelse'];
        $icd10_notes = $dt_put['icd10_notes'];
        $non_icd10 = implode('|', $dt_put['non_icd10']);

        // data soap_a_action
        $icd10_arr = $dt_put['icd10'];

        // data soap_p
        $therapy_plan = $dt_put['therapy_plan'];

        // data soap_p_action
        $icd9cm_arr = $dt_put['icd9cm'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration = array(
            'id' => $registration_id,
            'type_visit_id' => $type_visit_id
        );

        $this->Registration->replace($dt_registration);

        $exit_reason = "";
        if ($exit_id != "") {
            $select = 'a.id, a.name';
            $exit = $this->Exits->select($exit_id, $select);
    
            if (count($exit) == 0) {
                $this->REST_Return(404, 'there is no exit with id ' . $exit_id);
                return;
            }
    
            $exit_reason = $exit[0]->name;
        }

        $dt_registration_destination = array(
            'id' => $registration_destination_id,
            'registration_id' => $registration_id,
            'health_facility_item_id' => $health_facility_item_id,
            'exit_id' => $exit_id,
            'exit_reason' => $exit_reason,
            'type_emergency_id' => $type_emergency_id,
        );

        $this->Registration_destination->replace($dt_registration_destination);

        // nurse update
        $dt_nurse = array(
            'anamesa_type_id'           => $anamesa_type_id,
            'main_complaint'            => $main_complaint,
            'history_disease_now'       => $history_disease_now,
            'history_disease_past'      => $history_disease_past,
            'family_name'               => $family_name,
            'family_relation_id'        => $family_relation_id,
            'systolic_blood_pressure'   => $systolic_blood_pressure,
            'diastolic_blood_pressure'  => $diastolic_blood_pressure,
            'pulse_frequency'           => $pulse_frequency,
            'breath_frequency'          => $breath_frequency,
            'oximetry_frequency'        => $oximetry_frequency,
            'temperature'               => $temperature,
            'blood_sugar'               => $blood_sugar,
            'sore_score'                => $sore_score,
            'ews'                       => $ews,
            'weight'                    => $weight,
            'height'                    => $height,
            'imt'                       => $imt,
            'head_circumference'        => $head_circumference,
            'chest_circumference'       => $chest_circumference,
            'arm_circumference'         => $arm_circumference,
            'stomach_circumference'     => $stomach_circumference,
            'knee_circumference'        => $knee_circumference,
        );

        // create nurse if do not exist
        if ($nurse_id == null) {
            $select = 'a.id, a.employee_id';
            $user = $this->User->select($user_id, $select);

            $employee_id = $user[0]->employee_id;

            $dt_nurse['registration_destination_id'] = $registration_destination_id;
            $dt_nurse['employee_nurse_id'] = $employee_id;
            $dt_nurse['date_added'] = date('Y-m-d H:i:s');

            $this->Nurse->create($dt_nurse);
        } else {
            $dt_nurse['id'] = $nurse_id;

            $this->Nurse->replace($dt_nurse);
        }
    
        if ($soap_s_id != null) {
            // soap_s update
            $dt_soap_s = array(
                'id'                    => $soap_s_id,
                'anamesa_type_id'       => $anamesa_type_id,
                'main_complaint'        => $main_complaint,
                'history_disease_now'   => $history_disease_now,
                'history_disease_past'  => $history_disease_past,
                'history_drugs'         => $history_drugs,
                'family_name'           => $family_name,
                'family_relation_id'    => $family_relation_id,
                'user_updated'          => $user_id,
            );
            $this->Soap_s->replace($dt_soap_s);
        } else {
            Logger::info($this->debug_uniq, 'cannot update soap_s');
        }

        if ($soap_o_id != null) {
            // soap_o update
            $dt_soap_o = array(
                'id'                        => $soap_o_id,
                'systolic_blood_pressure'   => $systolic_blood_pressure,
                'diastolic_blood_pressure'  => $diastolic_blood_pressure,
                'pulse_frequency'           => $pulse_frequency,
                'breath_frequency'          => $breath_frequency,
                'oximetry_frequency'        => $oximetry_frequency,
                'temperature'               => $temperature,
                'blood_sugar'               => $blood_sugar,
                'sore_score'                => $sore_score,
                'ews'                       => $ews,
                'weight'                    => $weight,
                'height'                    => $height,
                'imt'                       => $imt,
                'head_circumference'        => $head_circumference,
                'chest_circumference'       => $chest_circumference,
                'arm_circumference'         => $arm_circumference,
                'stomach_circumference'     => $stomach_circumference,
                'knee_circumference'        => $knee_circumference,
                'user_updated'              => $user_id,
            );
            $this->Soap_o->replace($dt_soap_o);
        } else {
            Logger::info($this->debug_uniq, 'cannot update soap_o');
        }

        if ($soap_a_id != null) {
            // soap_a update
            $dt_soap_a = array(
                'id'            => $soap_a_id,
                'anythingelse'  => $anythingelse,
                'icd10_notes'   => $icd10_notes,
                'non_icd10'     => $non_icd10,
                'user_updated'  => $user_id,
            );
            $this->Soap_a->replace($dt_soap_a);
    
            // soap_a_action update
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
        } else {
            Logger::info($this->debug_uniq, 'cannot update soap_a');
        }

        if ($soap_p_id != null) {
            // soap_p update
            $dt_soap_p = array(
                'id'                 => $soap_p_id,
                'therapy_plan'       => $therapy_plan
            );
            $this->Soap_p->replace($dt_soap_p);
    
            // soap_p_action update
            $select = "a.id, a.icd9cm_id";
            $where = array('soap_p_id' => $soap_p_id);
            $soap_p_action = $this->Soap_p_action->select(0, $select, $where);
    
            $tmp_delete_icd9cm_arr = [];
    
            // adding all current items (db) to temporary variable
            for ($i=0; $i < count($soap_p_action); $i++) { 
                $tmp_delete_icd9cm_arr[$soap_p_action[$i]->icd9cm_id] = $soap_p_action[$i]->id;
            }
    
            for ($i=0; $i < count($icd9cm_arr) ; $i++) { 
                $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];
                
                $icd9cm = $this->Icd9cm->select_id($icd9cm_id);
                if($icd9cm == "") {
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
        } else {
            Logger::info($this->debug_uniq, 'cannot update soap_p');
        }
    
        $this->db->trans_complete();
        /* End Transaction */
        
        Logger::info($this->debug_uniq, "|---- finish update jkn ----|");

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update_jkn soap', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );

        $this->update_eklaim_get($soap_id);
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_ophthal_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_ophthal_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $soap_id = $id;
    
        if ($soap_id === 0 || $soap_id === "0") {
            $this->REST_Return(400, 'soap_id is required');
            return;
        }

        $ophthal_od_encode = '{}';
        $ophthal_os_encode = '{}';

        if (isset($dt_put['ophthal_od']) && isset($dt_put['ophthal_os'])) {
            $ophthal_od = (object) array(
                'visus' => $dt_put['ophthal_od']['visus'],
                'tio_digital' => $dt_put['ophthal_od']['tio_digital'],
                'tio_tonometri_sciots' => $dt_put['ophthal_od']['tio_tonometri_sciots'],
                'eye_position' => $dt_put['ophthal_od']['eye_position'],
                'eye_movement' => $dt_put['ophthal_od']['eye_movement'],
                'super_silia' => $dt_put['ophthal_od']['super_silia'],
                'palpebra_edema' => $dt_put['ophthal_od']['palpebra_edema'],
                'palpebra_ptosis' => $dt_put['ophthal_od']['palpebra_ptosis'],
                'palpebra_lagophtalmus' => $dt_put['ophthal_od']['palpebra_lagophtalmus'],
                'palpebra_tumor' => $dt_put['ophthal_od']['palpebra_tumor'],
                'apparatus_lakrimalis' => $dt_put['ophthal_od']['apparatus_lakrimalis'],
                'silia' => $dt_put['ophthal_od']['silia'],
                'conjunctive_hiperemis' => $dt_put['ophthal_od']['conjunctive_hiperemis'],
                'conjunctive_bleed' => $dt_put['ophthal_od']['conjunctive_bleed'],
                'cornea' => $dt_put['ophthal_od']['cornea'],
                'camera_oculi' => $dt_put['ophthal_od']['camera_oculi'],
                'iris_color' => $dt_put['ophthal_od']['iris_color'],
                'iris_cripte' => $dt_put['ophthal_od']['iris_cripte'],
                'pupil_form' => $dt_put['ophthal_od']['pupil_form'],
                'pupil_diameter' => $dt_put['ophthal_od']['pupil_diameter'],
                'pupil_direct_light' => $dt_put['ophthal_od']['pupil_direct_light'],
                'pupil_indirect_light' => $dt_put['ophthal_od']['pupil_indirect_light'],
                'lens' => $dt_put['ophthal_od']['lens'],
                'shadow_test' => $dt_put['ophthal_od']['shadow_test'],
                'glandula' => $dt_put['ophthal_od']['glandula'],
                'field_view' => $dt_put['ophthal_od']['field_view'],
                'fundus' => $dt_put['ophthal_od']['fundus'],
            );
    
            $ophthal_os = (object) array(
                'visus' => $dt_put['ophthal_os']['visus'],
                'tio_digital' => $dt_put['ophthal_os']['tio_digital'],
                'tio_tonometri_sciots' => $dt_put['ophthal_os']['tio_tonometri_sciots'],
                'eye_position' => $dt_put['ophthal_os']['eye_position'],
                'eye_movement' => $dt_put['ophthal_os']['eye_movement'],
                'super_silia' => $dt_put['ophthal_os']['super_silia'],
                'palpebra_edema' => $dt_put['ophthal_os']['palpebra_edema'],
                'palpebra_ptosis' => $dt_put['ophthal_os']['palpebra_ptosis'],
                'palpebra_lagophtalmus' => $dt_put['ophthal_os']['palpebra_lagophtalmus'],
                'palpebra_tumor' => $dt_put['ophthal_os']['palpebra_tumor'],
                'apparatus_lakrimalis' => $dt_put['ophthal_os']['apparatus_lakrimalis'],
                'silia' => $dt_put['ophthal_os']['silia'],
                'conjunctive_hiperemis' => $dt_put['ophthal_os']['conjunctive_hiperemis'],
                'conjunctive_bleed' => $dt_put['ophthal_os']['conjunctive_bleed'],
                'cornea' => $dt_put['ophthal_os']['cornea'],
                'camera_oculi' => $dt_put['ophthal_os']['camera_oculi'],
                'iris_color' => $dt_put['ophthal_os']['iris_color'],
                'iris_cripte' => $dt_put['ophthal_os']['iris_cripte'],
                'pupil_form' => $dt_put['ophthal_os']['pupil_form'],
                'pupil_diameter' => $dt_put['ophthal_os']['pupil_diameter'],
                'pupil_direct_light' => $dt_put['ophthal_os']['pupil_direct_light'],
                'pupil_indirect_light' => $dt_put['ophthal_os']['pupil_indirect_light'],
                'lens' => $dt_put['ophthal_os']['lens'],
                'shadow_test' => $dt_put['ophthal_os']['shadow_test'],
                'glandula' => $dt_put['ophthal_os']['glandula'],
                'field_view' => $dt_put['ophthal_os']['field_view'],
                'fundus' => $dt_put['ophthal_os']['fundus'],
            );

            $ophthal_od_encode = json_encode($ophthal_od);
            $ophthal_os_encode = json_encode($ophthal_os);
        }

        $select = 'a.id';
        $where = array('soap_id' => $soap_id);
        $soap_o = $this->Soap_o->select(0, $select, $where);

        $soap_o_id = $soap_o[0]->id;

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_soap_o = array(
            'ophthal_od' => $ophthal_od_encode,
            'ophthal_os' => $ophthal_os_encode,
        );
    
        $this->Soap_o->update($soap_o_id, $dt_soap_o);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_o,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update soap_o', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_book_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_book_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $current_user = $this->User->read($user_id,false)[0]->employee_id;

        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $date_request = $dt_put['date_request'];

        $select = 'a.id';
        $soap = $this->$model->select($id, $select);
        if(count($soap) == 0) {
            $this->REST_Return(404, 'Soap with id ' . $id . ' does not exist!');
            return;
        }

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if(count($registration_destination) == 0) {
            $this->REST_Return(404, 'Registration_destination with id ' . $registration_destination_id . ' does not exist!');
            return;
        }

        $select = 'a.id';
        $doctor_in_charge = $this->Employee->select($doctor_in_charge_id, $select);
        if(count($doctor_in_charge) == 0) {
            $this->REST_Return(404, 'Employee with id ' . $doctor_in_charge_id . ' does not exist!');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'user_updated' => $current_user,
            'date_updated' => date("Y-m-d H:i:s",time())
        );
        $this->$model->update_v2($id, $dt_soap);

        // LABORATORY_REQUEST
        if(isset($dt_put['laboratory_request_items'])) {
            $laboratory_request_id = "";
            $select = 'a.id, a.date_request';
            $where = array('soap_id' => $id);
            $laboratory_request = $this->Laboratory_request->select(0, $select, $where);
            if (count($laboratory_request) > 0) {
                $laboratory_request_id = $laboratory_request[0]->id;
                $exist_date_request = date('Y-m-d', strtotime($laboratory_request[0]->date_request));
                if ($exist_date_request != $date_request) {
                    $dt_cancel = array(
                        'is_delete' => 1
                    );
                    $this->Laboratory_request->update_v2($laboratory_request_id, $dt_cancel);
                    
                    $laboratory_request_id = "";
                }
            }

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

                $dt_laboratory_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'date_request'          => $date_request
                );
                $id_laboratory_request = $this->Laboratory_request->create($dt_laboratory_request);

                $laboratory_request_id = $this->Laboratory_request->select_id($id_laboratory_request);
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
                            'date_request'              => $date_request,
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
            $radiology_request_id = "";
            $select = 'a.id, a.date_request';
            $where = array('soap_id' => $id);
            $radiology_request = $this->Radiology_request->select(0, $select, $where);
            if (count($radiology_request) > 0) {
                $radiology_request_id = $radiology_request[0]->id;
                $exist_date_request = date('Y-m-d', strtotime($radiology_request[0]->date_request));
                if ($exist_date_request != $date_request) {
                    $dt_cancel = array(
                        'is_cancel' => 1
                    );
                    $this->Radiology_request->update_v2($radiology_request_id, $dt_cancel);
                    
                    $radiology_request_id = "";
                }
            }

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

                $dt_radiology_request = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'soap_id'               => $id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'date_request'          => $date_request
                );
                $id_radiology_request = $this->Radiology_request->create($dt_radiology_request);

                $radiology_request_id = $this->Radiology_request->select_id($id_radiology_request);
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
                            'date_request'              => $date_request,
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
            $physiotherapy_request_id = "";
            $select = 'a.id, a.date_request';
            $where = array('soap_id' => $id);
            $physiotherapy_request = $this->Physiotherapy_request->select(0, $select, $where);
            if (count($physiotherapy_request) > 0) {
                $physiotherapy_request_id = $physiotherapy_request[0]->id;
                $exist_date_request = date('Y-m-d', strtotime($physiotherapy_request[0]->date_request));
                if ($exist_date_request != $date_request) {
                    $dt_cancel = array(
                        'is_delete' => 1
                    );
                    $this->Physiotherapy_request->update_v2($physiotherapy_request_id, $dt_cancel);
                    
                    $physiotherapy_request_id = "";
                }
            }

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
                    'soap_id'               => $id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'no_request'            => $no_request,
                    'queue'                 => $queue,
                    'date_request'          => $date_request
                );
                $id_physiotherapy_request = $this->Physiotherapy_request->create($dt_physiotherapy_request);

                $physiotherapy_request_id = $this->Physiotherapy_request->select_id($id_physiotherapy_request);
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
                                'physiotherapy_request_id'  => $physiotherapy_request_item[$i]->physiotherapy_request_id,
                                'physiotherapy_package_id'  => $physiotherapy_request_item[$i]->physiotherapy_package_id,
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
                            'physiotherapy_request_id'  => $physiotherapy_request_id,
                            'physiotherapy_package_id'  => $package_id,
                            'service_class_id'          => $service_class[0]->id,
                            'var_1'                     => $costing_services_class[0]->var_1,
                            'var_2'                     => $costing_services_class[0]->var_2,
                            'var_3'                     => $costing_services_class[0]->var_3,
                            'costing_id'                => $costing[0]->id,
                            'costing_name'              => $costing[0]->name,
                            'date_request'              => $date_request,
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
                                'physiotherapy_request_id'  => $physiotherapy_request_id,
                                'physiotherapy_package_id'  => $package_id,
                                'is_delete'                 => 1,
                            );
                            $this->Physiotherapy_request_item->replace($dt_physiotherapy_request_item);
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
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update soap', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
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
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_S
        $anamesa_type_id = $dt_put['anamesa_type_id'];
        $family_name = $dt_put['family_name'];
        $family_relation_id = $dt_put['family_relation_id'];
        $main_complaint = $dt_put['main_complaint'];
        $history_disease_now = $dt_put['history_disease_now'];
        $history_disease_past = $dt_put['history_disease_past'];
        $history_disease_family = $dt_put['history_disease_family'];
        $history_habitude = $dt_put['history_habitude'];
        $history_drugs = $dt_put['history_drugs'];

        $dt_soap_s = array(
            'soap_id'               => $soap_id,
            'anamesa_type_id'       => $anamesa_type_id,
            'family_name'           => $family_name,
            'family_relation_id'    => $family_relation_id,
            'main_complaint'        => $main_complaint,
            'history_disease_now'   => $history_disease_now,
            'history_disease_past'  => $history_disease_past,
            'history_disease_family'=> $history_disease_family,
            'history_habitude'      => $history_habitude,
            'history_drugs'         => $history_drugs,
            'user_updated'          => $user_id,
            'date_updated'          => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_s = $this->Soap_s->select(0, $select, $where);
        if (count($soap_s) == 0) {
            $soap_s_id = $this->Soap_s->create($dt_soap_s);
        } else {
            $soap_s_id = $soap_s[0]->id;
            $st_soap_s = $this->Soap_s->update_v2($soap_s_id, $dt_soap_s);
            if ($st_soap_s < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // PATIENT_ALLERGY
        $allergies_arr = $dt_put['allergies'];

        $select = 'a.norm, a.allergy_id';
        $where = array('norm' => $norm);
        $patient_allergy = $this->Patient_allergy->select(0, $select, $where);
        for ($j=0; $j < count($patient_allergy); $j++) { 
            $dt_patient_allergy = array(
                'is_delete' => 1,
            );

            $st_patient_allergy = $this->Patient_allergy->update_v2(0, $dt_patient_allergy, array('norm' => $norm));
            if ($st_patient_allergy < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        for ($i=0; $i < count($allergies_arr); $i++) {
            $allergy_name = $allergies_arr[$i]['name'];

            $dt_patient_allergy = array(
                'norm'              => $norm,
                'name'              => $allergy_name,
                'is_delete'         => 0,
            );

            $select = 'a.id';
            $where = array('name' => $allergy_name);
            $allergy = $this->Allergy->select(0, $select, $where);

            if(count($allergy) > 0) {
                $allergy_id = $allergy[0]->id;
            } else {
                $dt_allergy = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'name'                  => $allergy_name,
                );

                $allergy_id = $this->Allergy->create($dt_allergy);    
            }

            $dt_patient_allergy['allergy_id'] = $allergy_id;

            $this->Patient_allergy->replace($dt_patient_allergy);
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_s,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save soap_s', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_ttv_antropometri_put() {
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
        $this->form_validation->set_rules($this->save_ttv_antropometri_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

        $select = 'a.id, r.norm, p.birth_date';
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

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $norm = $registration_destination[0]->norm;        
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_O
        $aware = $dt_put['aware'];
        $eye_score = $dt_put['eye_score'];
        $verbal_score = $dt_put['verbal_score'];
        $motor_score = $dt_put['motor_score'];
        $oxygen_type_id = $dt_put['oxygen_type_id'];
        $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $oximetry_frequency = $dt_put['oximetry_frequency'];
        $temperature = $dt_put['temperature'];
        $skin_color = $dt_put['skin_color'];
        $behavior = $dt_put['behavior'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $imt = $dt_put['imt'];
        $head_circumference = $dt_put['head_circumference'];
        $chest_circumference = $dt_put['chest_circumference'];
        $arm_circumference = $dt_put['arm_circumference'];
        $stomach_circumference = $dt_put['stomach_circumference'];
        $knee_circumference = $dt_put['knee_circumference'];

        $dt_soap_o = array(
            'soap_id'                   => $soap_id,
            'aware'                     => $aware,
            'eye_score'                 => $eye_score,
            'verbal_score'              => $verbal_score,
            'motor_score'               => $motor_score,
            'oxygen_type_id'            => $oxygen_type_id,
            'systolic_blood_pressure'   => $systolic_blood_pressure,
            'diastolic_blood_pressure'  => $diastolic_blood_pressure,
            'pulse_frequency'           => $pulse_frequency,
            'breath_frequency'          => $breath_frequency,
            'oximetry_frequency'        => $oximetry_frequency,
            'temperature'               => $temperature,
            'skin_color'                => $skin_color,
            'behavior'                  => $behavior,
            'weight'                    => $weight,
            'height'                    => $height,
            'imt'                       => $imt,
            'head_circumference'        => $head_circumference,
            'chest_circumference'       => $chest_circumference,
            'arm_circumference'         => $arm_circumference,
            'stomach_circumference'     => $stomach_circumference,
            'knee_circumference'        => $knee_circumference,
            'user_updated'              => $user_id,
            'date_updated'              => date('Y-m-d H:i:s'),
        );
        
        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_o = $this->Soap_o->select(0, $select, $where);
        if (count($soap_o) == 0) {
            $soap_o_id = $this->Soap_o->create($dt_soap_o);
        } else {
            $soap_o_id = $soap_o[0]->id;
            $st_soap_o = $this->Soap_o->update_v2($soap_o_id, $dt_soap_o);
            if ($st_soap_o < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        
        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $doctor_in_charge_id,
            'source_id'                     => $soap_id,
            'observation_date'              => date('Y-m-d H:i:s'),
            'aware'                         => $aware,
            'eye_score'                     => $eye_score,
            'verbal_score'                  => $verbal_score,
            'motor_score'                   => $motor_score,
            'systolic'                      => $systolic_blood_pressure,
            'diastolic'                     => $diastolic_blood_pressure,
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

        $select = 'a.id';
        $where = array(
            'a.source_id' => $soap_id,
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
    
            $blood_pressure = (float) $systolic_blood_pressure;
            $pulse = (float) $pulse_frequency;
            $breathing = (float) $breath_frequency;
            $temperature = (float) $temperature;
            $oxygen_saturation = (float) $oximetry_frequency;
            $oxygen = $oxygen_type_id != 2 ? 2 : 0;
    
            $ews_score = count_ews_score($aware, $blood_pressure, $pulse, $breathing, $temperature, $oxygen_saturation, $oxygen);
        
            $dt_early_warning_score_item = array(
                'early_warning_score_id' => $early_warning_score_id,
                'employee_nurse_id'      => $doctor_in_charge_id,
                'source_id'              => $soap_id,
                'observation_time'       => date('H:i:s'),
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
                'a.source_id' => $soap_id,
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
                'source_id'         => $soap_id,
                'employee_nurse_id' => $doctor_in_charge_id,
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
                'employee_nurse_id'           => $doctor_in_charge_id,
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
            'source_id'             => $soap_id,
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
            'a.source_id' => $soap_id,
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

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_o,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save ttv & antropometri', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_O
        $head = $dt_put['head'];
        $head_hair_color = $dt_put['head_hair_color'];
        $head_hair_condition = $dt_put['head_hair_condition'];
        $eye_conjunctive = $dt_put['eye_conjunctive'];
        $eye_sclera = $dt_put['eye_sclera'];
        $eye_pupil = $dt_put['eye_pupil'];
        $eye_light_reflex = $dt_put['eye_light_reflex'];
        $eye_blind_color = $dt_put['eye_blind_color'];
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

        $ophthal_od_encode = '{}';
        $ophthal_os_encode = '{}';

        $ophthal_od = $dt_put['ophthal_od'];
        $ophthal_os = $dt_put['ophthal_os'];

        if (count($ophthal_od) > 0) {
            $ophthal_od_data = (object) array(
                'visus' => $ophthal_od['visus'],
                'tio_digital' => $ophthal_od['tio_digital'],
                'tio_tonometri_sciots' => $ophthal_od['tio_tonometri_sciots'],
                'eye_position' => $ophthal_od['eye_position'],
                'eye_movement' => $ophthal_od['eye_movement'],
                'super_silia' => $ophthal_od['super_silia'],
                'palpebra_edema' => $ophthal_od['palpebra_edema'],
                'palpebra_ptosis' => $ophthal_od['palpebra_ptosis'],
                'palpebra_lagophtalmus' => $ophthal_od['palpebra_lagophtalmus'],
                'palpebra_tumor' => $ophthal_od['palpebra_tumor'],
                'apparatus_lakrimalis' => $ophthal_od['apparatus_lakrimalis'],
                'silia' => $ophthal_od['silia'],
                'conjunctive_hiperemis' => $ophthal_od['conjunctive_hiperemis'],
                'conjunctive_bleed' => $ophthal_od['conjunctive_bleed'],
                'cornea' => $ophthal_od['cornea'],
                'camera_oculi' => $ophthal_od['camera_oculi'],
                'iris_color' => $ophthal_od['iris_color'],
                'iris_cripte' => $ophthal_od['iris_cripte'],
                'pupil_form' => $ophthal_od['pupil_form'],
                'pupil_diameter' => $ophthal_od['pupil_diameter'],
                'pupil_direct_light' => $ophthal_od['pupil_direct_light'],
                'pupil_indirect_light' => $ophthal_od['pupil_indirect_light'],
                'lens' => $ophthal_od['lens'],
                'shadow_test' => $ophthal_od['shadow_test'],
                'glandula' => $ophthal_od['glandula'],
                'field_view' => $ophthal_od['field_view'],
                'fundus' => $ophthal_od['fundus'],
            );
            $ophthal_od_encode = json_encode($ophthal_od_data);
        }

        if (count($ophthal_os) > 0) {
            $ophthal_os_data = (object) array(
                'visus' => $ophthal_os['visus'],
                'tio_digital' => $ophthal_os['tio_digital'],
                'tio_tonometri_sciots' => $ophthal_os['tio_tonometri_sciots'],
                'eye_position' => $ophthal_os['eye_position'],
                'eye_movement' => $ophthal_os['eye_movement'],
                'super_silia' => $ophthal_os['super_silia'],
                'palpebra_edema' => $ophthal_os['palpebra_edema'],
                'palpebra_ptosis' => $ophthal_os['palpebra_ptosis'],
                'palpebra_lagophtalmus' => $ophthal_os['palpebra_lagophtalmus'],
                'palpebra_tumor' => $ophthal_os['palpebra_tumor'],
                'apparatus_lakrimalis' => $ophthal_os['apparatus_lakrimalis'],
                'silia' => $ophthal_os['silia'],
                'conjunctive_hiperemis' => $ophthal_os['conjunctive_hiperemis'],
                'conjunctive_bleed' => $ophthal_os['conjunctive_bleed'],
                'cornea' => $ophthal_os['cornea'],
                'camera_oculi' => $ophthal_os['camera_oculi'],
                'iris_color' => $ophthal_os['iris_color'],
                'iris_cripte' => $ophthal_os['iris_cripte'],
                'pupil_form' => $ophthal_os['pupil_form'],
                'pupil_diameter' => $ophthal_os['pupil_diameter'],
                'pupil_direct_light' => $ophthal_os['pupil_direct_light'],
                'pupil_indirect_light' => $ophthal_os['pupil_indirect_light'],
                'lens' => $ophthal_os['lens'],
                'shadow_test' => $ophthal_os['shadow_test'],
                'glandula' => $ophthal_os['glandula'],
                'field_view' => $ophthal_os['field_view'],
                'fundus' => $ophthal_os['fundus'],
            );
            $ophthal_os_encode = json_encode($ophthal_os_data);
        }

        $dt_soap_o = array(
            'soap_id'                       => $soap_id,
            'head'                          => $head,
            'head_hair_color'               => $head_hair_color,
            'head_hair_condition'           => $head_hair_condition,
            'eye_conjunctive'               => $eye_conjunctive,
            'eye_sclera'                    => $eye_sclera,
            'eye_pupil'                     => $eye_pupil,
            'eye_light_reflex'              => $eye_light_reflex,
            'eye_blind_color'               => $eye_blind_color,
            'mouth_mucosa'                  => $mouth_mucosa,
            'mouth_discoloration'           => $mouth_discoloration,
            'mouth_tongue'                  => $mouth_tongue,
            'mouth_caries'                  => $mouth_caries,
            'neck_kgb'                      => $neck_kgb,
            'neck_trachea_position'         => $neck_trachea_position,
            'neck_massa'                    => $neck_massa,
            'neck_jvp'                      => $neck_jvp,
            'thorax_insp_chest'             => $thorax_insp_chest,
            'thorax_insp_retraction'        => $thorax_insp_retraction,
            'thorax_palp_chest'             => $thorax_palp_chest,
            'thorax_palp_fremitus'          => $thorax_palp_fremitus,
            'thorax_palp_crepitation'       => $thorax_palp_crepitation,
            'thorax_ausk_pul_vasiculer'     => $thorax_ausk_pul_vasiculer,
            'thorax_ausk_pul_rhonki'        => $thorax_ausk_pul_rhonki,
            'thorax_ausk_pul_wheezing'      => $thorax_ausk_pul_wheezing,
            'thorax_ausk_cor_heart_sound'   => $thorax_ausk_cor_heart_sound,
            'thorax_ausk_cor_murmur'        => $thorax_ausk_cor_murmur,
            'thorax_ausk_cor_gallop'        => $thorax_ausk_cor_gallop,
            'abdomen_supel'                 => $abdomen_supel,
            'abdomen_bowel_sounds'          => $abdomen_bowel_sounds,
            'abdomen_tenderness'            => $abdomen_tenderness,
            'abdomen_hepar_lien'            => $abdomen_hepar_lien,
            'abdomen_other_physical_check'  => $abdomen_other_physical_check,
            'extremity_acral'               => $extremity_acral,
            'extremity_crt'                 => $extremity_crt,
            'extremity_edema'               => $extremity_edema,
            'genitalia'                     => $genitalia,
            'physical_check'                => $localist_status,
            'localist_status'               => $localist_status,
            'ophthal_status'                => $ophthal_status,
            'neurologist_status'            => $neurologist_status,
            'ophthal_od'                    => $ophthal_od_encode,
            'ophthal_os'                    => $ophthal_os_encode,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_o = $this->Soap_o->select(0, $select, $where);
        if (count($soap_o) == 0) {
            $soap_o_id = $this->Soap_o->create($dt_soap_o);
        } else {
            $soap_o_id = $soap_o[0]->id;
            $st_soap_o = $this->Soap_o->update_v2($soap_o_id, $dt_soap_o);
            if ($st_soap_o < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_o,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save soap_o', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );
        
        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_A
        $icd10_notes = $dt_put['icd10_notes'];
        $non_icd10 = isset($dt_put['non_icd10']) ? implode('|', $dt_put['non_icd10']) : '';

        $dt_soap_a = array(
            'soap_id'       => $soap_id,
            'icd10_notes'   => $icd10_notes,
            'non_icd10'     => $non_icd10,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'a.soap_id' => $soap_id
        );
        $where_soap_a = 'a.cppt_id IS NULL';
        $this->db->where($where_soap_a);
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

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_a,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save diagnose', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];
        $advice = $dt_put['advice'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'advice' => $advice,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );
        
        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_P
        $therapy_plan = $dt_put['therapy_plan'];

        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'therapy_plan'          => $therapy_plan,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
        );
        $where_soap_p = 'a.cppt_id IS NULL';
        $this->db->where($where_soap_p);
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
            'doctor_in_charge_id' => $doctor_in_charge_id,
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
                'doctor_in_charge_id'   => $doctor_in_charge_id,
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

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_p,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save procedure', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_discharge_planning_put() {
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
        $this->form_validation->set_rules($this->save_discharge_planning_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_P
        $estimated_duration_treatment = $dt_put['estimated_duration_treatment'];
        $follow_up_care_arry = isset($dt_put['follow_up_care_arry']) ? implode(',', $dt_put['follow_up_care_arry']): '';
        $diet_and_nutrition_type_id = $dt_put['diet_and_nutrition_type_id'];
        $diet_and_nutrition = $dt_put['diet_and_nutrition'];
        $wound_care_type_id = $dt_put['wound_care_type_id'];
        $wound_care = $dt_put['wound_care'];
        $medical_rehabilitation_type_id = $dt_put['medical_rehabilitation_type_id'];
        $medical_rehabilitation = $dt_put['medical_rehabilitation'];
        $pain_management_type_id = $dt_put['pain_management_type_id'];
        $pain_management = $dt_put['pain_management'];
        $discharge_planning_other = $dt_put['discharge_planning_other'];

        $dt_soap_p = array(
            'soap_id'                        => $soap_id,
            'doctor_in_charge_id'            => $doctor_in_charge_id,
            'estimated_duration_treatment'   => $estimated_duration_treatment,
            'follow_up_care_arry'            => $follow_up_care_arry,
            'diet_and_nutrition_type_id'     => $diet_and_nutrition_type_id,
            'diet_and_nutrition'             => $diet_and_nutrition,
            'wound_care_type_id'             => $wound_care_type_id,
            'wound_care'                     => $wound_care,
            'medical_rehabilitation_type_id' => $medical_rehabilitation_type_id,
            'medical_rehabilitation'         => $medical_rehabilitation,
            'pain_management_type_id'        => $pain_management_type_id,
            'pain_management'                => $pain_management,
            'discharge_planning_other'       => $discharge_planning_other,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
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
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap_p,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save discharge_planning', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_hemodialysis_initiation_put() {
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
        $this->form_validation->set_rules($this->save_hemodialysis_initiation_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );
        
        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // HEMODIALYSIS
        $main_diagnosis = $dt_put['main_diagnosis'];
        $etiology = $dt_put['etiology'];
        $comorbidities = $dt_put['comorbidities'];
        $frequency_hd = $dt_put['frequency_hd'];
        $vascular_access = $dt_put['vascular_access'];

        $dt_hemodialysis_initiation = array(
            'soap_id'               => $soap_id,
            'main_diagnosis'        => $main_diagnosis,
            'etiology'              => $etiology,
            'comorbidities'         => $comorbidities,
            'frequency_hd'          => $frequency_hd,
            'vascular_access'       => $vascular_access,
            'user_added'            => $user_id,
            'user_updated'          => $user_id,
            'date_added'            => date('Y-m-d H:i:s'),
            'date_updated'          => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $hemodialysis_initiation = $this->Hemodialysis_initiation->select(0, $select, $where);
        if (count($hemodialysis_initiation) == 0) {
            $hemodialysis_initiation_id = $this->Hemodialysis_initiation->create($dt_hemodialysis_initiation);
        } else {
            $hemodialysis_initiation_id = $hemodialysis_initiation[0]->id;
            unset($dt_hemodialysis_initiation['user_added']);
            unset($dt_hemodialysis_initiation['date_added']);
            $st_hemodialysis_initiation = $this->Hemodialysis_initiation->update_v2($hemodialysis_initiation_id, $dt_hemodialysis_initiation);
            if ($st_hemodialysis_initiation < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis_initiation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save hemodialysis_initiation', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_medical_rehabilitation_put() {
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
        $this->form_validation->set_rules($this->save_medical_rehabilitation_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // MEDICAL_REHABILITATION
        $functional_testing = $dt_put['functional_testing'];
        $suspected_disease_type_id = $dt_put['suspected_disease_type_id'];
        $suspected_disease = $dt_put['suspected_disease'];
        $result = $dt_put['result'];
        $conclusion = $dt_put['conclusion'];
        $recommendation = $dt_put['recommendation'];
        $functional_diagnosis = $dt_put['functional_diagnosis'];
        $medical_diagnosis = $dt_put['medical_diagnosis'];

        $dt_medical_rehabilitation = array(
            'soap_id'                   => $soap_id,
            'functional_testing'        => $functional_testing,
            'suspected_disease_type_id' => $suspected_disease_type_id,
            'suspected_disease'         => $suspected_disease,
            'result'                    => $result,
            'conclusion'                => $conclusion,
            'recommendation'            => $recommendation,
            'functional_diagnosis'      => $functional_diagnosis,
            'medical_diagnosis'         => $medical_diagnosis,
            'user_added'                => $user_id,
            'user_updated'              => $user_id,
            'date_added'                => date('Y-m-d H:i:s'),
            'date_updated'              => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $medical_rehabilitation = $this->Medical_rehabilitation->select(0, $select, $where);
        if (count($medical_rehabilitation) == 0) {
            $medical_rehabilitation_id = $this->Medical_rehabilitation->create($dt_medical_rehabilitation);
        } else {
            $medical_rehabilitation_id = $medical_rehabilitation[0]->id;
            unset($dt_medical_rehabilitation['user_added']);
            unset($dt_medical_rehabilitation['date_added']);
            $st_medical_rehabilitation = $this->Medical_rehabilitation->update_v2($medical_rehabilitation_id, $dt_medical_rehabilitation);
            if ($st_medical_rehabilitation < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_rehabilitation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save medical_rehabilitation', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );
        
        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // LABORATORY_REQUEST
        if (isset($dt_put['laboratory_request_items'])) {
            $laboratory_request_items_arr = $dt_put['laboratory_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
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
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'clinical_indications'  => $clinical_indications,
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

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save laboratory_request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // RADIOLOGY_REQUEST
        if (isset($dt_put['radiology_request_items'])) {
            $radiology_request_items_arr = $dt_put['radiology_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
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
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
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
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save radiology_request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // PHYSIOTHERAPY_REQUEST
        if (isset($dt_put['physiotherapy_request_items'])) {
            $physiotherapy_request_items_arr = $dt_put['physiotherapy_request_items'];
    
            $select = 'a.id, a.status, a.no_request';
            $where = array(
                'a.soap_id' => $soap_id,
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
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save physiotherapy_request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
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
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];

        $select = 'a.id, r.norm, hfi.id as health_facility_item_id, hfi.health_facility_id';
        $where = array();
        $join = array(
            array('registration r', 'a.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
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

        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;
        $health_facility_id = $registration_destination[0]->health_facility_id;
        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_P
        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
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

            $select = 'a.id';
            $depo = $this->Depo->select($pharmacy_depo_id);
            if (count($depo) == 0) {
                $this->REST_Return(404, 'there is no depo with id ' . $pharmacy_depo_id);
                return;
            }

            $depo_id = $pharmacy_depo_id;

            $prescription = $this->db->query("
                SELECT pc.id, pc.flag, pc.no_order
                FROM pharmacy_prescription pc
                WHERE pc.soap_p_id = '$soap_p_id'
                AND pc.is_delete = 0
                AND (pc.cppt_id IS NULL OR pc.cppt_id = '')
            ")->result();
            $prescription_id = '';
            if (count($prescription) == 0 && count($form_prescription) > 0) {
                // count no_order & queue
                if ($health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6') { // RAWAT JALAN
                    $prefix = 'PRJ';
                    $depo_id = '972dcaba-cea0-4515-9756-75ea675c6322';
                } else {
                    $prefix = 'PRS';
                }
                
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
                    'pharmacy_depo_id'          => $depo_id == '' || $depo_id == null ? '972dcaba-cea0-4515-9756-75ea675c6322' : $depo_id,
                    'soap_p_id'                 => $soap_p_id,
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
                    $signatura_times_id = $form_prescription[$i]['signatura_times_id'] ?? null;
                    $administer_id = $form_prescription[$i]['administer_id'] ?? null;
                    $dose_unit_id = $form_prescription[$i]['dose_unit_id'] ?? null;

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

                    if ($signatura_times_id != null) {
                        $select = 'a.id';
                        $signatura_times = $this->Signatura_times->select($signatura_times_id, $select);
                        if (count($signatura_times) == 0) {
                            $this->REST_Return(404, 'signatura times with id ' . $signatura_times_id . ' does not exist');
                            return;
                        }
                    }

                    if ($administer_id != null) {
                        $select = 'a.id';
                        $administer = $this->Administer->select($administer_id, $select);
                        if (count($administer) == 0) {
                            $this->REST_Return(404, 'administer with id ' . $administer_id . ' does not exist');
                            return;
                        }
                    }

                    if ($dose_unit_id != null) {
                        $select = 'a.id';
                        $dose_unit = $this->Unit->select($dose_unit_id, $select);
                        if (count($dose_unit) == 0) {
                            $this->REST_Return(404, 'unit with id ' . $dose_unit_id . ' does not exist');
                            return;
                        }
                    }

                    $package_name = $package[0]->name;

                    $dose = $form_prescription[$i]['dose'];
                    $prescription_type_id = $form_prescription[$i]['prescription_type_id'];
                    $value_package = $form_prescription[$i]['value_package'];
                    $used = $form_prescription[$i]['used'];
                    $desc = $form_prescription[$i]['desc'];
                    $sort = $form_prescription[$i]['sort'];
                    $status_kardex = clean_postput($form_prescription[$i]['status_kardex'] ?? null);
                    
                    for ($j=0; $j < count($form_prescription_parent); $j++) { 
                        $id_parent = $form_prescription_parent[$j]['id'];
                        if ($id_parent == $prescription_type_id) {
                            $prescription_type_used = $form_prescription_parent[$j]['used'];
                            $prescription_type_value = $form_prescription_parent[$j]['value'];
                            $prescription_type_package = $form_prescription_parent[$j]['package'];
                            $prescription_type_signatura_times_id = $form_prescription_parent[$j]['signatura_times_id'];
                            $prescription_type_administer_id = $form_prescription_parent[$j]['administer_id'];
                            $prescription_type_desc = clean_postput($form_prescription_parent[$j]['desc'] ?? null);
                            $prescription_type_status_kardex = clean_postput($form_prescription_parent[$j]['status_kardex'] ?? null);

                            if ($prescription_type_signatura_times_id != null) {
                                $select = 'a.id';
                                $signatura_times = $this->Signatura_times->select($prescription_type_signatura_times_id, $select);
                                if (count($signatura_times) == 0) {
                                    $this->REST_Return(404, 'signatura times with id ' . $prescription_type_signatura_times_id . ' does not exist');
                                    return;
                                }
                                $signatura_times_id = $prescription_type_signatura_times_id;
                            }

                            if ($prescription_type_administer_id != null) {
                                $select = 'a.id';
                                $administer = $this->Administer->select($prescription_type_administer_id, $select);
                                if (count($administer) == 0) {
                                    $this->REST_Return(404, 'administer with id ' . $prescription_type_administer_id . ' does not exist');
                                    return;
                                }
                                $administer_id = $prescription_type_administer_id;
                            }

                            if ($prescription_type_desc != null) {
                                $desc = $prescription_type_desc;
                            }

                            if ($prescription_type_status_kardex != null) {
                                $status_kardex = $prescription_type_status_kardex;
                            }
                        }
                    }

                    $dt_prescription_item = array(
                        'pharmacy_prescription_id'      => $prescription_id,
                        'pharmacy_catalog_id'           => $pharmacy_catalog_id,
                        'doctor_in_charge_id'           => $doctor_in_charge_id,
                        'package_id'                    => $package_id,
                        'package_name'                  => $package_name,
                        'signatura_times_id'            => $signatura_times_id,
                        'administer_id'                 => $administer_id,
                        'dose'                          => $dose,
                        'dose_unit_id'                  => $dose_unit_id,
                        'prescription_item_type_id'     => $prescription_type_id,
                        'qty'                           => $value_package,
                        'used'                          => $used,
                        'desc'                          => $desc,
                        'sort'                          => $sort,
                        'status_kardex'                 => $status_kardex,
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
                'data'      => $dt_soap,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save physiotherapy_request', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_medical_check_up_put() {
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
        $this->form_validation->set_rules($this->save_medical_check_up_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $registration_destination_id = $dt_put['registration_destination_id'];
        $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
        $is_initial_inpatient = $dt_put['is_initial_inpatient'];
        $is_hemodialysis = $dt_put['is_hemodialysis'];
        $is_medical_rehabilitation = $dt_put['is_medical_rehabilitation'];
        $is_medical_check_up = $dt_put['is_medical_check_up'];

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

        $norm = $registration_destination[0]->norm;
    
        /* Start Transaction */
        $this->db->trans_start();

        // SOAP
        $dt_soap = array(
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'registration_destination_id' => $registration_destination_id,
            'is_initial_inpatient' => $is_initial_inpatient,
            'is_hemodialysis' => $is_hemodialysis,
            'is_medical_rehabilitation' => $is_medical_rehabilitation,
            'is_medical_check_up' => $is_medical_check_up,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.registration_destination_id' => $registration_destination_id,
            'a.is_delete' => 0,
            'a.is_resume_exit' => 0,
            'a.is_booking' => 0,
        );

        $soap_id = $dt_put['soap_id'] ?? null;

        if ($soap_id != null) {
            $soap = $this->$model->select($soap_id, $select);
        } else {
            $soap = $this->$model->select(0, $select, $where);
        }
        
        if (count($soap) == 0) {
            if ($soap_id != null) {
                $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                return;
            }
            $soap_id = $this->$model->create($dt_soap);
        } else {
            $soap_id = $soap[0]->id;
            unset($dt_soap['user_added']);
            unset($dt_soap['date_added']);
            $st_soap = $this->Soap->update_v2($soap_id, $dt_soap);
            if ($st_soap < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // MEDICAL_CHECK_UP
        $fit_to_work = $dt_put['fit_to_work'];
        $consultation = $dt_put['consultation'];

        $dt_medical_check_up = array(
            'soap_id'                   => $soap_id,
            'consultation'              => $consultation,
            'fit_to_work'               => $fit_to_work,
            'user_added'                => $user_id,
            'user_updated'              => $user_id,
            'date_added'                => date('Y-m-d H:i:s'),
            'date_updated'              => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $medical_check_up = $this->Medical_check_up->select(0, $select, $where);
        if (count($medical_check_up) == 0) {
            $medical_check_up_id = $this->Medical_check_up->create($dt_medical_check_up);
        } else {
            $medical_check_up_id = $medical_check_up[0]->id;
            unset($dt_medical_check_up['user_added']);
            unset($dt_medical_check_up['date_added']);
            $st_medical_check_up = $this->Medical_check_up->update_v2($medical_check_up_id, $dt_medical_check_up);
            if ($st_medical_check_up < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_check_up,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save medical_check_up', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $soap_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_status_bpjs_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_status_bpjs_rules);
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

        $select = 'a.id, a.registration_id, a.sts, r.code_booking, a.health_facility_item_id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $registration_id = $registration_destination[0]->registration_id;
        $health_facility_item_id = $registration_destination[0]->health_facility_item_id;
        $sts = $registration_destination[0]->sts;
        $code_booking = $registration_destination[0]->code_booking;

        $prescription = $this->db->query("
            SELECT a.id, a.no_order, a.queue
            FROM pharmacy_prescription as a
            WHERE a.registration_destination_id = '$registration_destination_id'
            AND a.is_delete = 0
            AND (a.cppt_id IS NULL OR a.cppt_id = '')
            ORDER BY a.date_added DESC
        ")->result();

        $prescription_queue = null;
        if (count($prescription) > 0) {
            $prescription_queue = $prescription[0]->queue;
        }

        // KHUSUS POLIKLNIK INSURANCE BPJS Kesehatan
        $this->load->library('Vclaim_antrol','vclaim_antrol');

        /* Start Transaction */
        $this->db->trans_start();

        if ($sts == 4) {
            $dt_registration_destination = array(
                'id' => $registration_destination_id,
                'registration_id' => $registration_id,
                'health_facility_item_id' => $health_facility_item_id,
                'sts' => 5,
                'date_task_5' => date('Y-m-d H:i:s'),
                'done_doctor_stamp' => date('Y-m-d H:i:s')
            );
    
            // UPDATE STS jadi 5 bahwa sudah selesai simpan semua kajian dokter
            $this->Registration_destination->replace($dt_registration_destination);
            
            // Cek kondisi code_booking exists
            // Cek code_booking dari table registration
            if ($code_booking) {
                $update_antrean_5 = array(
                    'kodebooking' => $code_booking,
                    'taskid' => 5,
                    'waktu' => $this->tomiliseconds(date("Y-m-d H:i:s", strtotime('+1 minutes')))
                );

                // CEK KALAU ADA RESEP
                $jenisresep = 'Non racikan';
                if ($prescription_queue) {
                    $update_antrean_5['jenisresep'] = $jenisresep;
                } else {
                    $update_antrean_5['jenisresep'] = 'Tidak ada';
                }

                // KIRIM API STS 5
                $updatewaktu_antrean_5 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_5));

                // CEK KALAU ADA RESEP KIRIM API 
                if($prescription_queue){
                    $farmasiAntrean = array(
                        "kodebooking" => $code_booking,
                        "jenisresep" => $jenisresep == 'Non racikan' ? 'non racikan' : 'racikan',
                        "nomorantrean" => $prescription_queue,
                        "keterangan" => "resep"
                    );
                    $resepantrean = $this->vclaim_antrol->request("/antrean/farmasi/add", "POST", json_encode($farmasiAntrean));
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
            $this->REST_Return(500, 'error while trying to update status bpjs', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_eklaim_get($p = '0') {
        if (!$p) {
            $this->REST_Return(404, 'there is no soap with id ' . $p);
            return;
        }

        $select = 'a.id,a.registration_destination_id,
        sa.id as soap_a_id, 
        sp.id as soap_p_id, 
        so.systolic_blood_pressure, 
        so.diastolic_blood_pressure'; 
        $join = array(            
            array('soap_o so', 'a.id = so.soap_id', 'LEFT'),
            array('soap_a sa', 'a.id = sa.soap_id', 'LEFT'),
            array('soap_p sp', 'a.id = sp.soap_id', 'LEFT'),
        );
        $where = array();
        $soap = $this->Soap->select($p, $select, $where, $join);
        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $p);
            return;
        }

        $registration_destination_id = $soap[0]->registration_destination_id;

        $where_r['rd.id'] = $registration_destination_id;

        $select_r = '
        a.norm,
        rd.registration_id,
        rd.doctor_in_charge_id,
        rd.health_facility_item_id,
        rd.checkin_date,
        rd.checkout_date,
        e.fullname as doctor_in_charge_name,
        ri.number_insurance,
        ri.sep,
        p.birth_date,
        p.fullname,
        p.sex,
        hfi.health_facility_id,
        ';
        $join_r = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
            array('employee e', 'rd.doctor_in_charge_id = e.id', 'INNER'),
            array('employee e2', 'rd.doctor_in_charge_id_2 = e2.id', 'LEFT'),
            array('employee e3', 'rd.doctor_in_charge_id_3 = e3.id', 'LEFT'),
            array('employee e4', 'rd.doctor_in_charge_id_4 = e4.id', 'LEFT'),
            array('employee e5', 'rd.doctor_in_charge_id_5 = e5.id', 'LEFT'),
            array('how_to_enter hte', 'a.how_to_enter_id = hte.id', 'INNER'),
            array('patient p', 'a.norm = p.norm', 'INNER'),
            array('exit ex', 'rd.exit_id = ex.id', 'LEFT'),
            array('bed b', 'rd.bed_id = b.id', 'LEFT'),
            array('room r', 'b.room_id = r.id', 'LEFT'),
            array('doctor_schedule_item dsi', 'rd.doctor_schedule_item_id = dsi.id', 'LEFT'),
            array('company c', 'ri.company_id = c.id', 'LEFT'),
        );
        $registration = $this->Registration->select(0, $select_r, $where_r, $join_r);
        
        $number_insurance = $registration[0]->number_insurance;
        $doctor_in_charge_id = $registration[0]->doctor_in_charge_id;
        $registration_id = $registration[0]->registration_id;
        $health_facility_item_id = $registration[0]->health_facility_item_id;
        $sep = $registration[0]->sep;
        $norm = $registration[0]->norm;
        $fullname = $registration[0]->fullname;
        $birth_date = $registration[0]->birth_date;
        $sex = $registration[0]->sex;

        $return = $this->BuatKlaimBaru($number_insurance,$sep,$norm,$fullname,$birth_date,$sex == '1' ? '1' : '2');
        if ($return == 'Ok') {
        } else {
            var_dump($return);
            return;
        }
        $diagnosa = array();
        $procedure = array();
        // SOAP_A_ACTION DIAGNOSA ICD10
        $this->Soap_a_action->whereFields['soap_a_id'] = $soap[0]->soap_a_id;
        $this->Soap_a_action->whereFields['is_delete'] = 0;
        $soap_a_action = $this->Soap_a_action->read();
        if($soap_a_action != []) {
            for ($j=0; $j < count($soap_a_action); $j++) { 
                $icd10 = $this->Icd10->read($soap_a_action[$j]->icd10_id);
                $diagnosa[] = $icd10[0]->code;
            }
            $diagnosa = implode("#",$diagnosa);
        }

        // SOAP_P_ACTION PROCEDURE ICD9
        $this->Soap_p_action->whereFields['soap_p_id'] = $soap[0]->soap_p_id;
        $this->Soap_p_action->whereFields['is_delete'] = 0;
        $soap_p_action = $this->Soap_p_action->read();
        if($soap_p_action != []) {
            for ($j=0; $j < count($soap_p_action); $j++) { 
                $icd9 = $this->Icd9cm->read($soap_p_action[$j]->icd9cm_id);
                $procedure[] = $icd9[0]->code;
            }
            $procedure = implode("#",$procedure);
        }

        $checkin_date               = $registration[0]->checkin_date;
        $checkout_date              = $registration[0]->checkin_date;
        $health_facility_id         = $registration[0]->health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? '2' : ($registration[0]->health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7' ? '3' : '1');
        $service_class_name         = $registration[0]->health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' ? '3' : $registration[0]->service_class_name;
        $adl_sub_acute              = '0';
        $adl_chronic                = '0';
        $icu_indikator              = '0';
        $icu_los                    = '0';
        $ventilator_hour            = '0';
        $upgrade_class_ind          = '0'; 
        $upgrade_class_class        = '0';
        $upgrade_class_los          = '0';
        $add_payment_pct            = '0';
        $birth_weight               = '0';
        $discharge_status           = '1';
        $diagnosa                   = $diagnosa;
        $procedure                  = $procedure;
        $tarif_poli_eks             = '0';
        $doctor_in_charge_name      = $registration[0]->doctor_in_charge_name;
        $kode_tarif                 = 'BS';
        $payor_id                   = '3';
        $payor_cd                   = 'JKN';
        $cob_cd                     = '#';
        $coder_nik                  = '123123123123';
        $registration_destination_id = $registration_destination_id;
        $soap_id                    = $soap[0]->id;
        $systolic_blood_pressure    = $soap[0]->systolic_blood_pressure;
        $diastolic_blood_pressure   = $soap[0]->diastolic_blood_pressure;
        // gp = Rujukan FKTP, hosp-trans = Rujukan FKRTL,
        // mp = Rujukan Spesialis, outp = Dari Rawat Jalan,
        // inp = Dari Rawat Inap, emd = Dari Rawat Darurat,
        // born = Lahir di RS, nursing = Rujukan Panti Jompo,
        // psych = Rujukan dari RS Jiwa, rehab = Rujukan Fasilitas
        // Rehab, other = Lain-lain
        $asalrujukan                = 'gp';

        $this->load->model("v1/finance/Medical_services_model", "Medical_services");

        $select_m = 'a.id'; 
        $join_m = array();
        $where_m['a.registration_destination_id'] = $registration_destination_id;
        $medical_services = $this->Medical_services->select(0, $select_m, $where_m);
        // var_dump(count($medical_services));
        if (count($medical_services) == 0) {
            $dt_medical_services = array(
                'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                'registration_destination_id' => $registration_destination_id,
                'sep' => $sep,
                'name' => $fullname,
                'doctor_in_charge_name' => $doctor_in_charge_id,
                'health_facility_id' => 'RAWAT JALAN',
                'checkin_date' => $checkin_date,
                'checkout_date' => $checkout_date,
                'percentage' => 25,
            );
            $medical_services_id = $this->Medical_services->create($dt_medical_services);
        } else {
            $medical_services_id = $medical_services[0]->id;
            $dt_medical_services = array(
                'id' => $medical_services_id,
                'institute_branch_id' => '240b8d8b-245e-42ba-85e6-2ba7d8ad5640',
                'registration_destination_id' => $registration_destination_id,
                'sep' => $sep,
                'name' => $fullname,
                'doctor_in_charge_name' => $doctor_in_charge_id,
                'health_facility_id' => 'RAWAT JALAN',
                'checkin_date' => $checkin_date,
                'checkout_date' => $checkout_date,
                'percentage' => 25,
            );
            $this->Medical_services->replace($dt_medical_services);
        }

        $this->UpdateDataKlaim($sep,$number_insurance,$checkin_date,$checkout_date,$health_facility_id,$service_class_name,$adl_sub_acute,
                            $adl_chronic,$icu_indikator,$icu_los,$ventilator_hour,$upgrade_class_ind,$upgrade_class_class,
                            $upgrade_class_los,$add_payment_pct,$birth_weight,$discharge_status,$diagnosa,$procedure,
                            $tarif_poli_eks,$doctor_in_charge_name,$kode_tarif,$payor_id,$payor_cd,$cob_cd,$coder_nik,$registration_destination_id,$systolic_blood_pressure,$diastolic_blood_pressure,$asalrujukan,$registration_id,$soap_id,$medical_services_id);

        // echo "<pre>";
        // var_dump($number_insurance,$sep,$norm,$fullname,$birth_date,$sex == '1' ? '1' : '2');
        // var_dump($soap);
        // echo "</pre>";die();
        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'id' => $registration_destination_id,
            'registration_id' => $registration_id,
            'health_facility_item_id' => $health_facility_item_id,
            'sts' => 8
        );

        // UPDATE STS jadi 8 bahwa sudah selesai simpan semua kajian dokter
        $this->Registration_destination->replace($dt_registration_destination);

        // if ($sts == 4) {
        //     $dt_registration_destination = array(
        //         'id' => $registration_destination_id,
        //         'registration_id' => $registration_id,
        //         'health_facility_item_id' => $health_facility_item_id,
        //         'sts' => 5,
        //         'date_task_5' => date('Y-m-d H:i:s'),
        //         'done_doctor_stamp' => date('Y-m-d H:i:s')
        //     );
    
        //     // UPDATE STS jadi 5 bahwa sudah selesai simpan semua kajian dokter
        //     $this->Registration_destination->replace($dt_registration_destination);
            
        //     // Cek kondisi code_booking exists
        //     // Cek code_booking dari table registration
        //     if ($code_booking) {
        //         $update_antrean_5 = array(
        //             'kodebooking' => $code_booking,
        //             'taskid' => 5,
        //             'waktu' => $this->tomiliseconds(date("Y-m-d H:i:s", strtotime('+1 minutes')))
        //         );

        //         // CEK KALAU ADA RESEP
        //         $jenisresep = 'Non racikan';
        //         if ($prescription_queue) {
        //             $update_antrean_5['jenisresep'] = $jenisresep;
        //         } else {
        //             $update_antrean_5['jenisresep'] = 'Tidak ada';
        //         }

        //         // KIRIM API STS 5
        //         $updatewaktu_antrean_5 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_5));

        //         // CEK KALAU ADA RESEP KIRIM API 
        //         if($prescription_queue){
        //             $farmasiAntrean = array(
        //                 "kodebooking" => $code_booking,
        //                 "jenisresep" => $jenisresep == 'Non racikan' ? 'non racikan' : 'racikan',
        //                 "nomorantrean" => $prescription_queue,
        //                 "keterangan" => "resep"
        //             );
        //             $resepantrean = $this->vclaim_antrol->request("/antrean/farmasi/add", "POST", json_encode($farmasiAntrean));
        //         }
        //     }
        // }
    
        $this->db->trans_complete();

        
        /* End Transaction */
    
        // if ($this->db->trans_status() === false) {
        //     $response_data = array(
        //         'error'     => $this->db->error(),
        //         'data'      => $dt_registration_destination,
        //         'query'     => $this->db->last_query()
        //     );
        //     $this->REST_Return(500, 'error while trying to update status bpjs', $response_data);
        //     return;
        // }
    
        // $response_data = array(
        //     'id'     => $id
        // );
    
        // $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    function getKey() {
        $keyRS = "ab2c60fd68dfe7c03e128c5a6bb796919a8bc1511b11d241c0fbc6a78e90e30d";   
        // $keyRS = "c6a286a5e0b7933abf1d1e13a265c8f56b12b1265eb80546419b3f3ac490b8a3";   
        return $keyRS;
    }
 
    function getUrlWS() {
        $UrlWS = "http://192.168.100.221/E-Klaim/ws.php";
        // $UrlWS = "https://9706-202-154-191-66.ngrok-free.app/E-Klaim/ws.php";
        return $UrlWS;
    }
     
    function getKelasRS() {
        $kelasRS = "BS";
        return $kelasRS;
    }
 
    function mc_encrypt($data, $strkey) {
        $key = hex2bin($strkey);
        if (mb_strlen($key, "8bit") !== 32) {
                throw new Exception("Needs a 256-bit key!");
        }

        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $iv = openssl_random_pseudo_bytes($iv_size); 
        $encrypted = openssl_encrypt($data,"aes-256-cbc",$key,OPENSSL_RAW_DATA,$iv );
        $signature = mb_substr(hash_hmac("sha256",$encrypted,$key,true),0,10,"8bit");
        $encoded = chunk_split(base64_encode($signature.$iv.$encrypted));        
        return $encoded;
    }
     
    function mc_decrypt($str, $strkey){
        $key = hex2bin($strkey);
        if (mb_strlen($key, "8bit") !== 32) {
            throw new Exception("Needs a 256-bit key!");
        }
        
        $iv_size = openssl_cipher_iv_length("aes-256-cbc");
        $decoded = base64_decode($str);
        $signature = mb_substr($decoded,0,10,"8bit");
        $iv = mb_substr($decoded,10,$iv_size,"8bit");
        $encrypted = mb_substr($decoded,$iv_size+10,NULL,"8bit");
        $calc_signature = mb_substr(hash_hmac("sha256",$encrypted,$key,true),0,10,"8bit");        
        if(!$this->mc_compare($signature,$calc_signature)) {
            return "SIGNATURE_NOT_MATCH"; 
        }
        
        $decrypted = openssl_decrypt($encrypted,"aes-256-cbc",$key,OPENSSL_RAW_DATA,$iv);
        return $decrypted;
    }
    
    function mc_compare($a, $b) {
        if (strlen($a) !== strlen($b)) {
            return false;
        }
        
        $result = 0;
        
        for($i = 0; $i < strlen($a); $i ++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }
        
        return $result == 0;
    }
     
    function GenerateNomorCovid(){	
        $nomor="";
        $request ='{
                        "metadata": {
                            "method": "generate_claim_number"
                        }, 
                        "data": {
                            "payor_id": "71" 
                        }
                    }';
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            $nomor=$msg['response']['claim_number'];
        }
        return $nomor;
    }
     
    function BuatKlaimBaru($nomor_kartu,$nomor_sep,$nomor_rm,$nama_pasien,$tgl_lahir,$gender){	
        $request ='{
                        "metadata":{
                            "method":"new_claim"
                        },
                        "data":{
                            "nomor_kartu":"'.$nomor_kartu.'",
                            "nomor_sep":"'.$nomor_sep.'",
                            "nomor_rm":"'.$nomor_rm.'",
                            "nama_pasien":"'.$nama_pasien.'",
                            "tgl_lahir":"'.$tgl_lahir.'",
                            "gender":"'.$gender.'"
                        }
                    }';
        $msg= $this->Request($request);
        // var_dump($msg);die();
        if ($msg) {
            if($msg['metadata']['message']=="Ok"){
                // var_dump("inacbg_klaim_baru","'".$nomor_sep."','".$msg['response']['patient_id']."','".$msg['response']['admission_id']."','".$msg['response']['hospital_admission_id']."'");
                // die();
            }else{
            }
        }else{
        }
        return $msg['metadata']['message'];
    }
     
    function BuatKlaimBaruInternal($nomor_kartu,$nomor_sep,$nomor_rm,$nama_pasien,$tgl_lahir,$gender){	
        $request ='{
                        "metadata":{
                            "method":"new_claim"
                        },
                        "data":{
                            "nomor_kartu":"'.$nomor_kartu.'",
                            "nomor_sep":"'.$nomor_sep.'",
                            "nomor_rm":"'.$nomor_rm.'",
                            "nama_pasien":"'.$nama_pasien.'",
                            "tgl_lahir":"'.$tgl_lahir.'",
                            "gender":"'.$gender.'"
                        }
                    }';
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            var_dump("inacbg_klaim_baru_internal","'".$nomor_sep."','".$msg['response']['patient_id']."','".$msg['response']['admission_id']."','".$msg['response']['hospital_admission_id']."'");
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
        return $msg['metadata']['message'];
    }
     
    function BuatKlaimBaru2($nomor_kartu,$nomor_sep,$nomor_rm,$nama_pasien,$tgl_lahir,$gender,$norawat){	
        $request ='{
                        "metadata":{
                            "method":"new_claim"
                        },
                        "data":{
                            "nomor_kartu":"'.$nomor_kartu.'",
                            "nomor_sep":"'.$nomor_sep.'",
                            "nomor_rm":"'.$nomor_rm.'",
                            "nama_pasien":"'.$nama_pasien.'",
                            "tgl_lahir":"'.$tgl_lahir.'",
                            "gender":"'.$gender.'"
                        }
                    }';
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            var_dump("inacbg_klaim_baru2","'".$norawat."','".$nomor_sep."','".$msg['response']['patient_id']."','".$msg['response']['admission_id']."','".$msg['response']['hospital_admission_id']."'");
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
        return $msg['metadata']['message'];
    }
     
    function UpdateDataPasien($nomor_rmlama,$nomor_kartu,$nomor_rm,$nama_pasien,$tgl_lahir,$gender){	
        $request ='{
                        "metadata": {
                            "method": "update_patient",
                            "nomor_rm": "'.$nomor_rmlama.'"
                        },
                        "data": {
                            "nomor_kartu": "'.$nomor_kartu.'",
                            "nomor_rm": "'.$nomor_rm.'",
                            "nama_pasien": "'.$nama_pasien.'",
                            "tgl_lahir": "'.$tgl_lahir.'",
                            "gender": "'.$gender.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function HapusDataPasien($nomor_rm,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method": "delete_patient"
                        },
                        "data": {
                            "nomor_rm": "'.$nomor_rm.'",
                            "coder_nik": "'.$coder_nik.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function cost_get($p = '0'){
        if (!$p) {
            $this->REST_Return(404, 'there is no id ' . $p);
            return;
        }

        $where_r['rd.id'] = $p;
        $select_r = 'a.norm,rd.registration_id,rd.register_date,rd.health_facility_item_id';
        $join_r = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
        );
        $registration = $this->Registration->select(0, $select_r, $where_r, $join_r);
        $register_date = date('Y-m-d', strtotime($registration[0]->register_date));

        // search history booking penunjang by reg -1
        $select_r_h = 'rd.id,rd.register_date';
        $where_r_h['a.norm'] = $registration[0]->norm;
        $where_r_h['rd.health_facility_item_id'] = $registration[0]->health_facility_item_id;
        $where_r_h['rd.register_date <='] = $register_date.' 00:00:00';
        $join_r_h = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
        );
        $registration_h = $this->Registration->select(0, $select_r_h, $where_r_h, $join_r_h);
        // var_dump($registration_h[0]->id);
        // search history booking penunjang by reg -1

        $where_register =  "('{$p}')";
        
        if ($registration_h) {
            $register_booking = $registration_h[0]->id; 
            $where_register =  "('{$p}','{$register_booking}')";
        }

        
        $prosedur_non_bedah_data = array();
        $prosedur_bedah_data = array();
        $konsultasi_data = array();
        $tenaga_ahli_data = array();
        $response_data_nurse = array();
        $response_data_rad = array();
        $response_data_lab = array();
        $response_data_kamar = array();
        $obat_kronis_data = array();
        $obat_kemoterapi_data = array();
        $obat_data = array();
        $bmhp_data = array();
        $response_data_pelayanan_darah = array();
        $response_data_sewa_alat = array();
        $response_data_rehabilitasi = array();
        $response_data_pres = array();
        $response_data_pres_two = array();

        $response_data_adm = $this->db->query("
            SELECT `a`.`costing_name`,`a`.`var_1` as `total` , `c`.`desc`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `transaction_administration` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_id` = '{$registration[0]->registration_id}'")->result();
        $response_data_nurse = $this->db->query("
            SELECT `a`.`costing_name`,`a`.`var_2` as `tarif`,`a`.`var_1` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `transaction_nurse` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN nurse n ON a.nurse_id = n.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `n`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_doctor = $this->db->query("
            SELECT `a`.`costing_name`,`a`.`var_1` as `tarif`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `d`.`fullname` as `doctor_name`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `transaction_doctor` as `a` 
            LEFT JOIN costing_services_class cs ON a.costing_id = cs.id
            LEFT JOIN costing c ON cs.costing_id = c.id
            LEFT JOIN soap si ON a.soap_id = si.id
            LEFT JOIN employee d ON a.doctor_in_charge_id = d.id
            WHERE `si`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        
        $response_data_lab = $this->db->query("
            SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `laboratory_request` as `a` 
            LEFT JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
            LEFT JOIN costing c ON b.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            LEFT JOIN soap si ON a.soap_id = si.id
            WHERE `si`.`registration_destination_id` IN {$where_register} AND `a`.`date_request` >= '{$register_date} 00:00:00' AND `a`.`date_request` <= '{$register_date} 23:59:59'
            AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
        $response_data_rad = $this->db->query("
            SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, `a`.`date_request`, `bb`.`id` as `result`,  `bb`.`radiology_request_item_id`, `b`.`radiology_request_id`
            FROM `radiology_request` as `a` 
            LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
            LEFT JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
            LEFT JOIN costing c ON b.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            LEFT JOIN soap si ON a.soap_id = si.id
            WHERE `si`.`registration_destination_id` IN {$where_register} AND `a`.`date_request` >= '{$register_date} 00:00:00' AND `a`.`date_request` <= '{$register_date} 23:59:59'
            AND `a`.`is_delete` = 0 AND `a`.`status` = 2")->result();
        $response_data_rehabilitasi = $this->db->query("
            SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `physiotherapy_request` as `a` 
            LEFT JOIN `physiotherapy_request_item` `b` ON `a`.`id` = `b`.`physiotherapy_request_id` 
            LEFT JOIN costing c ON b.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            LEFT JOIN soap si ON a.soap_id = si.id
            WHERE `si`.`registration_destination_id` IN {$where_register}
            AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
        $response_data_pres = $this->db->query("
            SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
            FROM `pharmacy_prescription` as `a` 
            LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
            LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
            LEFT JOIN soap_p spi ON a.soap_p_id = spi.id
            LEFT JOIN soap si ON spi.soap_id = si.id
            WHERE `si`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
        $response_data_pres_two = $this->db->query("
            SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
            FROM `pharmacy_prescription` as `a` 
            LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
            LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0 AND `a`.`soap_p_id` IS NULL AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
        $response_data_kamar = $this->db->query("
            SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
            FROM `transaction_bed` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_sewa_alat = $this->db->query("
            SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
            FROM `transaction_alkes` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_pelayanan_darah = $this->db->query("
            SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
            FROM `transaction_blood` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        
        $prosedur_non_bedah = 0;
        $prosedur_bedah = 0;
        $konsultasi=0;
        $tenaga_ahli=0;
        $keperawatan=0;
        $radiologi=0;
        $laboratorium=0;
        $kamar=0;
        $obat_kronis=0;
        $obat_kemoterapi=0;
        $obat=0;
        $pelayanan_darah=0;
        $bmhp=0;
        $sewa_alat=0;
        $rehabilitasi=0;

        for ($nurse=0; $nurse < count($response_data_nurse); $nurse++) {
            $keperawatan += $response_data_nurse[$nurse]->total;
        }
        for ($adm=0; $adm < count($response_data_adm); $adm++) {
            $konsultasi += $response_data_adm[$adm]->total;
        }
        for ($doctor=0; $doctor < count($response_data_doctor); $doctor++) {
            $konsultasi += $response_data_doctor[$doctor]->total;
        }
        for ($lab=0; $lab < count($response_data_lab); $lab++) {
            $laboratorium += $response_data_lab[$lab]->total;
        }
        for ($rehab=0; $rehab < count($response_data_rehab); $rehab++) {
            $rehabilitasi += $response_data_rehab[$rehab]->total;
        }
        for ($kamar=0; $kamar < count($response_data_kamar); $kamar++) {
            $kamar += $response_data_kamar[$kamar]->total;
        }
        for ($pelayanan_darah=0; $pelayanan_darah < count($response_data_pelayanan_darah); $pelayanan_darah++) {
            $pelayanan_darah += $response_data_pelayanan_darah[$pelayanan_darah]->total;
        }
        for ($rad=0; $rad < count($response_data_rad); $rad++) {
            $radiologi += $response_data_rad[$rad]->total;
        }
        for ($sewa_alat=0; $sewa_alat < count($response_data_sewa_alat); $sewa_alat++) {
            $sewa_alat += $response_data_sewa_alat[$sewa_alat]->total;
        }
        for ($pres=0; $pres < count($response_data_pres); $pres++) {
            $obat += $response_data_pres[$pres]->total;
        }
        for ($pres=0; $pres < count($response_data_pres_two); $pres++) {
            $obat += $response_data_pres_two[$pres]->total;
        }

        $response_data = array(
            'total_tarif_rs' => $keperawatan+$konsultasi+$laboratorium+$rehabilitasi+$kamar+$pelayanan_darah+$radiologi+$sewa_alat+$obat,
            'prosedur_non_bedah' => $prosedur_non_bedah, 
            'prosedur_bedah' => $prosedur_bedah, 
            'konsultasi' => $konsultasi,
            'tenaga_ahli' => $tenaga_ahli,
            'keperawatan' => $keperawatan,
            'radiologi' => $radiologi,
            'laboratorium' => $laboratorium,
            'kamar' => $kamar,
            'obat_kronis' => $obat_kronis,
            'obat_kemoterapi' => $obat_kemoterapi,
            'obat' => $obat,
            'pelayanan_darah' => $pelayanan_darah,
            'bmhp' => $bmhp,
            'sewa_alat' => $sewa_alat,
            'rehabilitasi' => $rehabilitasi,
            'prosedur_non_bedah_data' => $prosedur_non_bedah_data, 
            'prosedur_bedah_data' => $prosedur_bedah_data, 
            'konsultasi_data' => array_merge($response_data_adm,$response_data_doctor),
            'tenaga_ahli_data' => $tenaga_ahli_data,
            'keperawatan_data' => $response_data_nurse,
            'radiologi_data' => $response_data_rad,
            'laboratorium_data' => $response_data_lab,
            'kamar_data' => $response_data_kamar,
            'obat_kronis_data' => $obat_kronis_data,
            'obat_kemoterapi_data' => $obat_kemoterapi_data,
            'obat_data' => $response_data_pres,
            'pelayanan_darah_data' => $response_data_pelayanan_darah,
            'bmhp_data' => $bmhp_data,
            'sewa_alat_data' => $response_data_sewa_alat,
            'rehabilitasi_data' => $response_data_rehabilitasi,
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
     
    function UpdateDataKlaim($nomor_sep,$nomor_kartu,$tgl_masuk,$tgl_pulang,$jenis_rawat,$kelas_rawat,$adl_sub_acute,
        $adl_chronic,$icu_indikator,$icu_los,$ventilator_hour,$upgrade_class_ind,$upgrade_class_class,
        $upgrade_class_los,$add_payment_pct,$birth_weight,$discharge_status,$diagnosa,$procedure,
        $tarif_poli_eks,$nama_dokter,$kode_tarif,$payor_id,$payor_cd,$cob_cd,$coder_nik,$norawat,$sistole,$diastole,$asalrujukan,$registration_id,$soap_id,$medical_services_id){	
        
        $p = $norawat;
        
        $where_r['rd.id'] = $p;
        $select_r = 'a.norm,rd.registration_id,rd.register_date,rd.health_facility_item_id';
        $join_r = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
        );
        $registration = $this->Registration->select(0, $select_r, $where_r, $join_r);
        $register_date = date('Y-m-d', strtotime($registration[0]->register_date));

        // search history booking penunjang by reg -1
        $select_r_h = 'rd.id,rd.register_date';
        $where_r_h['a.norm'] = $registration[0]->norm;
        $where_r_h['rd.health_facility_item_id'] = $registration[0]->health_facility_item_id;
        $where_r_h['rd.register_date <='] = $register_date.' 00:00:00';
        $join_r_h = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
        );
        $registration_h = $this->Registration->select(0, $select_r_h, $where_r_h, $join_r_h);
        // var_dump($registration_h[0]->id);
        // search history booking penunjang by reg -1

        $where_register =  "('{$p}')";
        
        if ($registration_h) {
            $register_booking = $registration_h[0]->id; 
            $where_register =  "('{$p}','{$register_booking}')";
        }

        $response_data_adm = $this->db->query("
            SELECT `a`.`costing_name`,`a`.`var_1` as `total` , `c`.`desc`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `transaction_administration` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_id` = '{$registration[0]->registration_id}'")->result();
        $response_data_nurse = $this->db->query("
            SELECT `a`.`costing_name`,`a`.`var_2` as `tarif`,`a`.`var_1` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `transaction_nurse` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN nurse n ON a.nurse_id = n.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `n`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_doctor = $this->db->query("
            SELECT `a`.`costing_name`,`a`.`var_1` as `tarif`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `d`.`fullname` as `doctor_name`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `transaction_doctor` as `a` 
            LEFT JOIN costing_services_class cs ON a.costing_id = cs.id
            LEFT JOIN costing c ON cs.costing_id = c.id
            LEFT JOIN soap si ON a.soap_id = si.id
            LEFT JOIN employee d ON a.doctor_in_charge_id = d.id
            WHERE `si`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_lab = $this->db->query("
            SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `laboratory_request` as `a` 
            LEFT JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
            LEFT JOIN costing c ON b.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            LEFT JOIN soap si ON a.soap_id = si.id
            WHERE `si`.`registration_destination_id` IN {$where_register} AND `a`.`date_request` >= '{$register_date} 00:00:00' AND `a`.`date_request` <= '{$register_date} 23:59:59'
            AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
        $response_data_rad = $this->db->query("
            SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, `a`.`date_request`, `bb`.`id` as `result`,  `bb`.`radiology_request_item_id`, `b`.`radiology_request_id`
            FROM `radiology_request` as `a` 
            LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
            LEFT JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
            LEFT JOIN costing c ON b.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            LEFT JOIN soap si ON a.soap_id = si.id
            WHERE `si`.`registration_destination_id` IN {$where_register} AND `a`.`date_request` >= '{$register_date} 00:00:00' AND `a`.`date_request` <= '{$register_date} 23:59:59'
            AND `a`.`is_delete` = 0 AND `a`.`status` = 2")->result();
        $response_data_rehabilitasi = $this->db->query("
            SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
            FROM `physiotherapy_request` as `a` 
            LEFT JOIN `physiotherapy_request_item` `b` ON `a`.`id` = `b`.`physiotherapy_request_id` 
            LEFT JOIN costing c ON b.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            LEFT JOIN soap si ON a.soap_id = si.id
            WHERE `si`.`registration_destination_id` IN {$where_register}
            AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
        $response_data_pres = $this->db->query("
            SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
            FROM `pharmacy_prescription` as `a` 
            LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
            LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
            LEFT JOIN soap_p spi ON a.soap_p_id = spi.id
            LEFT JOIN soap si ON spi.soap_id = si.id
            WHERE `si`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
        $response_data_pres_two = $this->db->query("
            SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
            FROM `pharmacy_prescription` as `a` 
            LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
            LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0 AND `a`.`soap_p_id` IS NULL AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
        $response_data_kamar = $this->db->query("
            SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
            FROM `transaction_bed` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_sewa_alat = $this->db->query("
            SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
            FROM `transaction_alkes` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        $response_data_pelayanan_darah = $this->db->query("
            SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
            FROM `transaction_blood` as `a` 
            LEFT JOIN costing c ON a.costing_id = c.id
            LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
            WHERE `a`.`registration_destination_id` = '{$p}'
            AND `a`.`is_delete` = 0")->result();
        
        $prosedur_non_bedah = 0;
        $prosedur_bedah = 0;
        $konsultasi=0;
        $tenaga_ahli=0;
        $keperawatan=0;
        $radiologi=0;
        $laboratorium=0;
        $kamar=0;
        $obat_kronis=0;
        $obat_kemoterapi=0;
        $obat=0;
        $pelayanan_darah=0;
        $bmhp=0;
        $sewa_alat=0;
        $rehabilitasi=0;

        for ($nurse=0; $nurse < count($response_data_nurse); $nurse++) {
            $keperawatan += $response_data_nurse[$nurse]->total;
        }
        for ($adm=0; $adm < count($response_data_adm); $adm++) {
            $konsultasi += $response_data_adm[$adm]->total;
        }
        for ($doctor=0; $doctor < count($response_data_doctor); $doctor++) {
            $konsultasi += $response_data_doctor[$doctor]->total;
        }
        for ($lab=0; $lab < count($response_data_lab); $lab++) {
            $laboratorium += $response_data_lab[$lab]->total;
        }
        for ($rehab=0; $rehab < count($response_data_rehabilitasi); $rehab++) {
            $rehabilitasi += $response_data_rehabilitasi[$rehab]->total;
        }
        for ($kamar=0; $kamar < count($response_data_kamar); $kamar++) {
            $kamar += $response_data_kamar[$kamar]->total;
        }
        for ($pelayanan_darah=0; $pelayanan_darah < count($response_data_pelayanan_darah); $pelayanan_darah++) {
            $pelayanan_darah += $response_data_pelayanan_darah[$pelayanan_darah]->total;
        }
        for ($rad=0; $rad < count($response_data_rad); $rad++) {
            $radiologi += $response_data_rad[$rad]->total;
        }
        for ($sewa_alat=0; $sewa_alat < count($response_data_sewa_alat); $sewa_alat++) {
            $sewa_alat += $response_data_sewa_alat[$sewa_alat]->total;
        }
        for ($pres=0; $pres < count($response_data_pres); $pres++) {
            $obat += $response_data_pres[$pres]->total;
        }
        for ($pres=0; $pres < count($response_data_pres_two); $pres++) {
            $obat += $response_data_pres_two[$pres]->total;
        }

        $tarif_rs=$prosedur_non_bedah+$prosedur_bedah+$konsultasi+$tenaga_ahli+$keperawatan+$pelayanan_darah+$radiologi+$laboratorium+$kamar+$obat_kronis+$obat_kemoterapi+$obat+$bmhp+$sewa_alat+$rehabilitasi;
        $request ='{
                        "metadata": {
                            "method": "set_claim_data",
                            "nomor_sep": "'.$nomor_sep.'"
                        },
                        "data": {
                            "nomor_sep": "'.$nomor_sep.'",
                            "nomor_kartu": "'.$nomor_kartu.'",
                            "tgl_masuk": "'.$tgl_masuk.'",
                            "tgl_pulang": "'.$tgl_pulang.'",
                            "jenis_rawat": "'.$jenis_rawat.'",
                            "kelas_rawat": "'.$kelas_rawat.'",
                            "adl_sub_acute": "'.$adl_sub_acute.'",
                            "adl_chronic": "'.$adl_chronic.'",
                            "icu_indikator": "'.$icu_indikator.'",
                            "icu_los": "'.$icu_los.'",
                            "ventilator_hour": "'.$ventilator_hour.'",
                            "upgrade_class_ind": "'.$upgrade_class_ind.'",
                            "upgrade_class_class": "'.$upgrade_class_class.'",
                            "upgrade_class_los": "'.$upgrade_class_los.'",
                            "add_payment_pct": "'.$add_payment_pct.'",
                            "birth_weight": "'.$birth_weight.'",
                            "sistole": '.$sistole.',
                            "diastole": '.$diastole.',
                            "discharge_status": "'.$discharge_status.'",
                            "diagnosa": "'.$diagnosa.'",
                            "procedure": "'.$procedure.'",
                            "diagnosa_inagrouper": "'.$diagnosa.'",
                            "procedure_inagrouper": "'.$procedure.'",
                            "tarif_rs": {
                                "prosedur_non_bedah": "'.$prosedur_non_bedah.'",
                                "prosedur_bedah": "'.$prosedur_bedah.'",
                                "konsultasi": "'.$konsultasi.'",
                                "tenaga_ahli": "'.$tenaga_ahli.'",
                                "keperawatan": "'.$keperawatan.'",
                                "penunjang": "0",
                                "radiologi": "'.$radiologi.'",
                                "laboratorium": "'.$laboratorium.'",
                                "pelayanan_darah": "'.$pelayanan_darah.'",
                                "rehabilitasi": "'.$rehabilitasi.'",
                                "kamar": "'.($kamar+$tarif_poli_eks).'",
                                "rawat_intensif": "0",
                                "obat": "'.$obat.'",
                                "obat_kronis": "'.$obat_kronis.'",
                                "obat_kemoterapi": "'.$obat_kemoterapi.'",
                                "alkes": "0",
                                "bmhp": "'.$bmhp.'",
                                "sewa_alat": "'.$sewa_alat.'"
                            },
                            "tarif_poli_eks": "0",
                            "nama_dokter": "'.$nama_dokter.'",
                            "kode_tarif": "'.$kode_tarif.'",
                            "payor_id": "3",
                            "payor_cd": "JKN",
                            "cob_cd": "'.$cob_cd.'",
                            "coder_nik": "'.$coder_nik.'"
                        }
                }';
        $this->load->model("v1/finance/Medical_services_model", "Medical_services");
        $dt_medical_services = array(
            'id' => $medical_services_id,
            'cost_hostpital' => $tarif_rs,
        );
        $this->Medical_services->replace($dt_medical_services);
        $msg= $this->Request($request);
        // echo "Data : ".$request;
        // echo "Data : ".var_dump($msg);die();
        if($msg['metadata']['message']=="Ok"){
            // Hapus2("inacbg_data_terkirim", "no_sep='".$nomor_sep."'");
            // var_dump("inacbg_data_terkirim","'".$nomor_sep."','".$coder_nik."'");
            $this->GroupingStages($nomor_sep,$coder_nik,$medical_services_id);
        }else{
            // echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
    }
     
    function UpdateDataKlaimInternal($nomor_sep,$nomor_kartu,$tgl_masuk,$tgl_pulang,$jenis_rawat,$kelas_rawat,$adl_sub_acute,
                            $adl_chronic,$icu_indikator,$icu_los,$ventilator_hour,$upgrade_class_ind,$upgrade_class_class,
                            $upgrade_class_los,$add_payment_pct,$birth_weight,$discharge_status,$diagnosa,$procedure,
                            $tarif_poli_eks,$nama_dokter,$kode_tarif,$payor_id,$payor_cd,$cob_cd,$coder_nik,$norawat,$sistole,$diastole,$asalrujukan){	
        
        $prosedur_non_bedah=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ralan Dokter Paramedis' and nm_perawatan not like '%terapi%'")+
                            getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ranap Dokter Paramedis' and nm_perawatan not like '%terapi%'");
        if($prosedur_non_bedah==""){
            $prosedur_non_bedah="0";
        }
        $prosedur_bedah=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Operasi'");
        if($prosedur_bedah==""){
            $prosedur_bedah="0";
        }
        $konsultasi=(getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ranap Dokter'")+
                    getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ralan Dokter'"));
        if($konsultasi==""){
            $konsultasi="0";
        }
        $tenaga_ahli=0;
        if($tenaga_ahli==""){
            $tenaga_ahli="0";
        }
        $keperawatan=(getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ranap Paramedis'")+
                    getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ralan Paramedis'"));
        if($keperawatan==""){
            $keperawatan="0";
        }
        $radiologi=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Radiologi'");
        if($radiologi==""){
            $radiologi="0";
        }
        $laboratorium=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Laborat'");
        if($laboratorium==""){
            $laboratorium="0";
        }
        $kamar=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Kamar'");
        if($kamar==""){
            $kamar="0";
        }
        $obat_kronis=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where nm_perawatan like '%kronis%' and no_rawat='".$norawat."' and status='Obat'");
        $obat_kemoterapi=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where nm_perawatan like '%kemo%' and no_rawat='".$norawat."' and status='Obat'");
        $obat=(getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Obat'")+
            getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Retur Obat'")+
            getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Resep Pulang'")-$obat_kronis-$obat_kemoterapi);
        if($obat==""){
            $obat="0";
        }        
        if($obat_kemoterapi==""){
            $obat_kemoterapi="0";
        }        
        if($obat_kronis==""){
            $obat_kronis="0";
        }        
        $bmhp=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Tambahan'");
        if($bmhp==""){
            $bmhp="0";
        }
        $sewa_alat=(getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Harian'")+
                    getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Service'"));
        if($sewa_alat==""){
            $sewa_alat="0";
        }
        $rehabilitasi=getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ralan Dokter Paramedis' and nm_perawatan like '%terapi%'")+
                            getOne("select if(sum(totalbiaya)='','0',sum(totalbiaya)) from billing where no_rawat='".$norawat."' and status='Ranap Dokter Paramedis' and nm_perawatan like '%terapi%'");
        if($rehabilitasi==""){
            $rehabilitasi="0";
        }
        
        $hasilcorona=bukaquery(
                "select pemulasaraan_jenazah,if(pemulasaraan_jenazah='Ya',1,0) as ytpemulasaraan_jenazah, 
                kantong_jenazah,if(kantong_jenazah='Ya',1,0) as ytkantong_jenazah, 
                peti_jenazah,if(peti_jenazah='Ya',1,0) as ytpeti_jenazah,  
                plastik_erat,if(plastik_erat='Ya',1,0) as ytplastik_erat,  
                desinfektan_jenazah,if(desinfektan_jenazah='Ya',1,0) as ytdesinfektan_jenazah,   
                mobil_jenazah,if(mobil_jenazah='Ya',1,0) as ytmobil_jenazah,    
                desinfektan_mobil_jenazah,if(desinfektan_mobil_jenazah='Ya',1,0) as ytdesinfektan_mobil_jenazah,  
                covid19_status_cd,if(covid19_status_cd='ODP',1,if(covid19_status_cd='PDP',2,3)) as ytcovid19_status_cd, 
                nomor_kartu_t, episodes1, episodes2,episodes3, episodes4, episodes5, episodes6, 
                covid19_cc_ind,if(covid19_cc_ind='Ya',1,0) as ytcovid19_cc_ind 
                from perawatan_corona where no_rawat='".$norawat."'");
        if($bariscorona = mysqli_fetch_array($hasilcorona)) {
            $episodes1 = $bariscorona["episodes1"];
            $episodes2 = $bariscorona["episodes2"];
            $episodes3 = $bariscorona["episodes3"];
            $episodes4 = $bariscorona["episodes4"];
            $episodes5 = $bariscorona["episodes5"];
            $episodes6 = $bariscorona["episodes6"];
            $episodes  = ($episodes1==0?"":"1;$episodes1#").($episodes2==0?"":"2;$episodes2#").($episodes3==0?"":"3;$episodes3#").($episodes4==0?"":"4;$episodes4#").($episodes5==0?"":"5;$episodes5#").($episodes6==0?"":"6;$episodes6#");  
            $episodes  = substr($episodes, 0, -1);
            $request ='{
                            "metadata": {
                                "method": "set_claim_data",
                                "nomor_sep": "'.$nomor_sep.'"
                            },
                            "data": {
                                "nomor_sep": "'.$nomor_sep.'",
                                "nomor_kartu": "'.$nomor_kartu.'",
                                "tgl_masuk": "'.$tgl_masuk.'",
                                "tgl_pulang": "'.$tgl_pulang.'",
                                "cara_masuk": "'.$asalrujukan.'",
                                "jenis_rawat": "'.$jenis_rawat.'",
                                "kelas_rawat": "'.$kelas_rawat.'",
                                "adl_sub_acute": "'.$adl_sub_acute.'",
                                "adl_chronic": "'.$adl_chronic.'",
                                "icu_indikator": "'.$icu_indikator.'",
                                "icu_los": "'.$icu_los.'",
                                "ventilator_hour": "'.$ventilator_hour.'",
                                "upgrade_class_ind": "'.$upgrade_class_ind.'",
                                "upgrade_class_class": "'.$upgrade_class_class.'",
                                "upgrade_class_los": "'.$upgrade_class_los.'",
                                "add_payment_pct": "'.$add_payment_pct.'",
                                "birth_weight": "'.$birth_weight.'",
                                "sistole": '.$sistole.',
                                "diastole": '.$diastole.',
                                "discharge_status": "'.$discharge_status.'",
                                "diagnosa": "'.$diagnosa.'",
                                "procedure": "'.$procedure.'",
                                "diagnosa_inagrouper": "'.$diagnosa.'",
                                "procedure_inagrouper": "'.$procedure.'",
                                "tarif_rs": {
                                    "prosedur_non_bedah": "'.$prosedur_non_bedah.'",
                                    "prosedur_bedah": "'.$prosedur_bedah.'",
                                    "konsultasi": "'.$konsultasi.'",
                                    "tenaga_ahli": "'.$tenaga_ahli.'",
                                    "keperawatan": "'.$keperawatan.'",
                                    "penunjang": "0",
                                    "radiologi": "'.$radiologi.'",
                                    "laboratorium": "'.$laboratorium.'",
                                    "pelayanan_darah": "0",
                                    "rehabilitasi": "'.$rehabilitasi.'",
                                    "kamar": "'.($kamar+$tarif_poli_eks).'",
                                    "rawat_intensif": "0",
                                    "obat": "'.$obat.'",
                                    "obat_kronis": "'.$obat_kronis.'",
                                    "obat_kemoterapi": "'.$obat_kemoterapi.'",
                                    "alkes": "0",
                                    "bmhp": "'.$bmhp.'",
                                    "sewa_alat": "'.$sewa_alat.'"
                                },
                                "pemulasaraan_jenazah": "'.$bariscorona["ytpemulasaraan_jenazah"].'", 
                                "kantong_jenazah": "'.$bariscorona["ytkantong_jenazah"].'", 
                                "peti_jenazah": "'.$bariscorona["ytpeti_jenazah"].'", 
                                "plastik_erat": "'.$bariscorona["ytplastik_erat"].'", 
                                "desinfektan_jenazah": "'.$bariscorona["ytdesinfektan_jenazah"].'", 
                                "mobil_jenazah": "'.$bariscorona["ytmobil_jenazah"].'", 
                                "desinfektan_mobil_jenazah": "'.$bariscorona["ytdesinfektan_mobil_jenazah"].'", 
                                "covid19_status_cd": "'.$bariscorona["ytcovid19_status_cd"].'", 
                                "nomor_kartu_t": "'.$bariscorona["nomor_kartu_t"].'", 
                                "episodes": "'.$episodes.'",
                                "covid19_cc_ind": "'.$bariscorona["ytcovid19_cc_ind"].'",
                                "tarif_poli_eks": "'.$tarif_poli_eks.'",
                                "nama_dokter": "'.$nama_dokter.'",
                                "kode_tarif": "'.$kode_tarif.'",
                                "payor_id": "71",
                                "payor_cd": "JAMINAN COVID-19",
                                "cob_cd": "'.$cob_cd.'",
                                "coder_nik": "'.$coder_nik.'"
                            }
                    }';
        }else{
            $request ='{
                            "metadata": {
                                "method": "set_claim_data",
                                "nomor_sep": "'.$nomor_sep.'"
                            },
                            "data": {
                                "nomor_sep": "'.$nomor_sep.'",
                                "nomor_kartu": "'.$nomor_kartu.'",
                                "tgl_masuk": "'.$tgl_masuk.'",
                                "tgl_pulang": "'.$tgl_pulang.'",
                                "jenis_rawat": "'.$jenis_rawat.'",
                                "kelas_rawat": "'.$kelas_rawat.'",
                                "adl_sub_acute": "'.$adl_sub_acute.'",
                                "adl_chronic": "'.$adl_chronic.'",
                                "icu_indikator": "'.$icu_indikator.'",
                                "icu_los": "'.$icu_los.'",
                                "ventilator_hour": "'.$ventilator_hour.'",
                                "upgrade_class_ind": "'.$upgrade_class_ind.'",
                                "upgrade_class_class": "'.$upgrade_class_class.'",
                                "upgrade_class_los": "'.$upgrade_class_los.'",
                                "add_payment_pct": "'.$add_payment_pct.'",
                                "birth_weight": "'.$birth_weight.'",
                                "sistole": '.$sistole.',
                                "diastole": '.$diastole.',
                                "discharge_status": "'.$discharge_status.'",
                                "diagnosa": "'.$diagnosa.'",
                                "procedure": "'.$procedure.'",
                                "diagnosa_inagrouper": "'.$diagnosa.'",
                                "procedure_inagrouper": "'.$procedure.'",
                                "tarif_rs": {
                                    "prosedur_non_bedah": "'.$prosedur_non_bedah.'",
                                    "prosedur_bedah": "'.$prosedur_bedah.'",
                                    "konsultasi": "'.$konsultasi.'",
                                    "tenaga_ahli": "'.$tenaga_ahli.'",
                                    "keperawatan": "'.$keperawatan.'",
                                    "penunjang": "0",
                                    "radiologi": "'.$radiologi.'",
                                    "laboratorium": "'.$laboratorium.'",
                                    "pelayanan_darah": "0",
                                    "rehabilitasi": "0",
                                    "kamar": "'.($kamar+$tarif_poli_eks).'",
                                    "rawat_intensif": "0",
                                    "obat": "'.$obat.'",
                                    "obat_kronis": "'.$obat_kronis.'",
                                    "obat_kemoterapi": "'.$obat_kemoterapi.'",
                                    "alkes": "0",
                                    "bmhp": "'.$bmhp.'",
                                    "sewa_alat": "'.$sewa_alat.'"
                                },
                                "tarif_poli_eks": "0",
                                "nama_dokter": "'.$nama_dokter.'",
                                "kode_tarif": "'.$kode_tarif.'",
                                "payor_id": "3",
                                "payor_cd": "JKN",
                                "cob_cd": "'.$cob_cd.'",
                                "coder_nik": "'.$coder_nik.'"
                            }
                    }';
        }
            
        //echo "Data : ".$request;
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            Hapus2("inacbg_data_terkirim_internal", "no_sep='".$nomor_sep."'");
            var_dump("inacbg_data_terkirim_internal","'".$nomor_sep."','".$coder_nik."'");
            GroupingStage1Internal($nomor_sep,$coder_nik);
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
    }
     
    function UpdateDataKlaim2($nomor_sep,$nomor_kartu,$tgl_masuk,$tgl_pulang,$jenis_rawat,$kelas_rawat,$adl_sub_acute,
                            $adl_chronic,$icu_indikator,$icu_los,$ventilator_hour,$upgrade_class_ind,$upgrade_class_class,
                            $upgrade_class_los,$add_payment_pct,$birth_weight,$discharge_status,$diagnosa,$procedure,
                            $tarif_poli_eks,$nama_dokter,$kode_tarif,$payor_id,$payor_cd,$cob_cd,$coder_nik,
                            $prosedur_non_bedah,$prosedur_bedah,$konsultasi,$tenaga_ahli,$keperawatan,$penunjang,
                            $radiologi,$laboratorium,$pelayanan_darah,$rehabilitasi,$kamar,$rawat_intensif,$obat,
                            $obat_kronis,$obat_kemoterapi,$alkes,$bmhp,$sewa_alat,$sistole,$diastole,$cara_masuk){	
        $request ='{
                        "metadata": {
                            "method": "set_claim_data",
                            "nomor_sep": "'.$nomor_sep.'"
                        },
                        "data": {
                            "nomor_sep": "'.$nomor_sep.'",
                            "nomor_kartu": "'.$nomor_kartu.'",
                            "tgl_masuk": "'.$tgl_masuk.'",
                            "tgl_pulang": "'.$tgl_pulang.'",
                            "cara_masuk": "'.$cara_masuk.'",
                            "jenis_rawat": "'.$jenis_rawat.'",
                            "kelas_rawat": "'.$kelas_rawat.'",
                            "adl_sub_acute": "'.$adl_sub_acute.'",
                            "adl_chronic": "'.$adl_chronic.'",
                            "icu_indikator": "'.$icu_indikator.'",
                            "icu_los": "'.$icu_los.'",
                            "ventilator_hour": "'.$ventilator_hour.'",
                            "upgrade_class_ind": "'.$upgrade_class_ind.'",
                            "upgrade_class_class": "'.$upgrade_class_class.'",
                            "upgrade_class_los": "'.$upgrade_class_los.'",
                            "add_payment_pct": "'.$add_payment_pct.'",
                            "birth_weight": "'.$birth_weight.'",
                            "sistole": '.$sistole.',
                            "diastole": '.$diastole.',
                            "discharge_status": "'.$discharge_status.'",
                            "diagnosa": "'.$diagnosa.'",
                            "procedure": "'.$procedure.'",
                            "diagnosa_inagrouper": "'.$diagnosa.'",
                            "procedure_inagrouper": "'.$procedure.'",
                            "tarif_rs": {
                                "prosedur_non_bedah": "'.$prosedur_non_bedah.'",
                                "prosedur_bedah": "'.$prosedur_bedah.'",
                                "konsultasi": "'.$konsultasi.'",
                                "tenaga_ahli": "'.$tenaga_ahli.'",
                                "keperawatan": "'.$keperawatan.'",
                                "penunjang": "'.$penunjang.'",
                                "radiologi": "'.$radiologi.'",
                                "laboratorium": "'.$laboratorium.'",
                                "pelayanan_darah": "'.$pelayanan_darah.'",
                                "rehabilitasi": "'.$rehabilitasi.'",
                                "kamar": "'.$kamar.'",
                                "rawat_intensif": "'.$rawat_intensif.'",
                                "obat": "'.$obat.'",
                                "obat_kronis": "'.$obat_kronis.'",
                                "obat_kemoterapi": "'.$obat_kemoterapi.'",
                                "alkes": "'.$alkes.'",
                                "bmhp": "'.$bmhp.'",
                                "sewa_alat": "'.$sewa_alat.'"
                            },
                            "tarif_poli_eks": "'.$tarif_poli_eks.'",
                            "nama_dokter": "'.$nama_dokter.'",
                            "kode_tarif": "'.$kode_tarif.'",
                            "payor_id": "'.$payor_id.'",
                            "payor_cd": "'.$payor_cd.'",
                            "cob_cd": "'.$cob_cd.'",
                            "coder_nik": "'.$coder_nik.'"
                        }
                }';
        //echo "Data : ".$request;
        $msg= $this->Request($request);
        $respon="Berhasil";
        if($msg['metadata']['message']=="Ok"){
            Hapus2("inacbg_data_terkirim2", "no_sep='".$nomor_sep."'");
            var_dump("inacbg_data_terkirim2","'".$nomor_sep."','".$coder_nik."'");
            GroupingStage12($nomor_sep,$coder_nik);
            $respon="Berhasil";
        }else{
            $respon="Gagal";
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
        return $respon;
    }
     
    function UpdateDataKlaim3($nomor_sep,$nomor_kartu,$tgl_masuk,$tgl_pulang,$jenis_rawat,$kelas_rawat,$adl_sub_acute,
                            $adl_chronic,$icu_indikator,$icu_los,$ventilator_hour,$upgrade_class_ind,$upgrade_class_class,
                            $upgrade_class_los,$add_payment_pct,$birth_weight,$discharge_status,$diagnosa,$procedure,
                            $tarif_poli_eks,$nama_dokter,$kode_tarif,$payor_id,$payor_cd,$cob_cd,$coder_nik,
                            $prosedur_non_bedah,$prosedur_bedah,$konsultasi,$tenaga_ahli,$keperawatan,$penunjang,
                            $radiologi,$laboratorium,$pelayanan_darah,$rehabilitasi,$kamar,$rawat_intensif,$obat,
                            $obat_kronis,$obat_kemoterapi,$alkes,$bmhp,$sewa_alat,$pemulasaraan_jenazah,$kantong_jenazah, 
                            $peti_jenazah,$plastik_erat,$desinfektan_jenazah,$mobil_jenazah,$desinfektan_mobil_jenazah,
                            $covid19_status_cd,$nomor_kartu_t,$episodes,$covid19_cc_ind,$sistole,$diastole,$cara_masuk){	
        $request ='{
                        "metadata": {
                            "method": "set_claim_data",
                            "nomor_sep": "'.$nomor_sep.'"
                        },
                        "data": {
                            "nomor_sep": "'.$nomor_sep.'",
                            "nomor_kartu": "'.$nomor_kartu.'",
                            "tgl_masuk": "'.$tgl_masuk.'",
                            "tgl_pulang": "'.$tgl_pulang.'",
                            "cara_masuk": "'.$cara_masuk.'",
                            "jenis_rawat": "'.$jenis_rawat.'",
                            "kelas_rawat": "'.$kelas_rawat.'",
                            "adl_sub_acute": "'.$adl_sub_acute.'",
                            "adl_chronic": "'.$adl_chronic.'",
                            "icu_indikator": "'.$icu_indikator.'",
                            "icu_los": "'.$icu_los.'",
                            "ventilator_hour": "'.$ventilator_hour.'",
                            "upgrade_class_ind": "'.$upgrade_class_ind.'",
                            "upgrade_class_class": "'.$upgrade_class_class.'",
                            "upgrade_class_los": "'.$upgrade_class_los.'",
                            "add_payment_pct": "'.$add_payment_pct.'",
                            "birth_weight": "'.$birth_weight.'",
                            "sistole": '.$sistole.',
                            "diastole": '.$diastole.',
                            "discharge_status": "'.$discharge_status.'",
                            "diagnosa": "'.$diagnosa.'",
                            "procedure": "'.$procedure.'",
                            "diagnosa_inagrouper": "'.$diagnosa.'",
                            "procedure_inagrouper": "'.$procedure.'",
                            "tarif_rs": {
                                "prosedur_non_bedah": "'.$prosedur_non_bedah.'",
                                "prosedur_bedah": "'.$prosedur_bedah.'",
                                "konsultasi": "'.$konsultasi.'",
                                "tenaga_ahli": "'.$tenaga_ahli.'",
                                "keperawatan": "'.$keperawatan.'",
                                "penunjang": "'.$penunjang.'",
                                "radiologi": "'.$radiologi.'",
                                "laboratorium": "'.$laboratorium.'",
                                "pelayanan_darah": "'.$pelayanan_darah.'",
                                "rehabilitasi": "'.$rehabilitasi.'",
                                "kamar": "'.$kamar.'",
                                "rawat_intensif": "'.$rawat_intensif.'",
                                "obat": "'.$obat.'",
                                "obat_kronis": "'.$obat_kronis.'",
                                "obat_kemoterapi": "'.$obat_kemoterapi.'",
                                "alkes": "'.$alkes.'",
                                "bmhp": "'.$bmhp.'",
                                "sewa_alat": "'.$sewa_alat.'"
                            },
                            "pemulasaraan_jenazah": "'.$pemulasaraan_jenazah.'", 
                            "kantong_jenazah": "'.$kantong_jenazah.'", 
                            "peti_jenazah": "'.$peti_jenazah.'", 
                            "plastik_erat": "'.$plastik_erat.'", 
                            "desinfektan_jenazah": "'.$desinfektan_jenazah.'", 
                            "mobil_jenazah": "'.$mobil_jenazah.'", 
                            "desinfektan_mobil_jenazah": "'.$desinfektan_mobil_jenazah.'", 
                            "covid19_status_cd": "'.$covid19_status_cd.'", 
                            "nomor_kartu_t": "'.$nomor_kartu_t.'", 
                            "episodes": "'.$episodes.'",
                            "covid19_cc_ind": "'.$covid19_cc_ind.'",
                            "tarif_poli_eks": "'.$tarif_poli_eks.'",
                            "nama_dokter": "'.$nama_dokter.'",
                            "kode_tarif": "'.$kode_tarif.'",
                            "payor_id": "'.$payor_id.'",
                            "payor_cd": "'.$payor_cd.'",
                            "cob_cd": "'.$cob_cd.'",
                            "coder_nik": "'.$coder_nik.'"
                        }
                }';
        //echo "Data : ".$request;
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            Hapus2("inacbg_data_terkirim2", "no_sep='".$nomor_sep."'");
            var_dump("inacbg_data_terkirim2","'".$nomor_sep."','".$coder_nik."'");
            GroupingStage13($nomor_sep,$coder_nik);
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
    }
     
    function UpdateDataProsedur($nomor_sep,$procedure,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method": "set_claim_data",
                            "nomor_sep": "'.$nomor_sep.'",
                        },
                        "data": {
                            "procedure": "'.$procedure.'",
                            "coder_nik": "'.$coder_nik.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function HapusSemuaProsedur($nomor_sep,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method": "set_claim_data",
                            "nomor_sep": "'.$nomor_sep.'",
                        },
                            "data": {
                            "procedure": "#",
                            "coder_nik": "'.$coder_nik.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function GroupingStages($nomor_sep,$coder_nik,$medical_services_id){	
        $this->load->model("v1/finance/Medical_services_model", "Medical_services");
        $select_m = 'a.id,a.is_special_cmg'; 
        $join_m = array();
        $where_m['a.registration_destination_id'] = $registration_destination_id;
        $medical_services = $this->Medical_services->select(0, $select_m, $where_m);

        if ($medical_services[0]->is_special_cmg == '0') {
            $request ='{
                            "metadata": {
                                "method":"grouper",
                                "stage":"1"
                            },
                            "data": {
                                "nomor_sep":"'.$nomor_sep.'"
                            }
                    }';
            $msg= $this->Request($request);
            // echo "Data : ".$request;
            // echo "Data : ".var_dump($msg);die();
            
            if($msg['metadata']['message']=="Ok"){
                // Hapus2("inacbg_grouping_stage1", "no_sep='".$nomor_sep."'");
                $cbg                = $msg['response']['cbg']['tariff'];
                $sub_acute          = $msg['response']['sub_acute']['tariff'];
                $chronic            = $msg['response']['chronic']['tariff'];
                $add_payment_amt    = $msg['response']['add_payment_amt'];
                // var_dump("inacbg_grouping_stage1","'".$nomor_sep."','".$msg['response']['cbg']['code']."','".$msg['response']['cbg']['description']."','".($cbg+$sub_acute+$chronic+$add_payment_amt)."'");
                $dt_medical_services = array(
                    'id' => $medical_services_id,
                    'code_inacbg' => $msg['response']['cbg']['code'],
                    'desc_inacbg' => $msg['response']['cbg']['description'],
                    'cost_inacbg' => $cbg+$sub_acute+$chronic+$add_payment_amt,
                    'json_obj' => json_encode($msg),
                );
                $this->Medical_services->replace($dt_medical_services);
                // $this->FinalisasiKlaim($nomor_sep,$coder_nik);
                // return json_encode($msg);
            }else{
                // echo "\nRespon INACBG : ".$msg['metadata']['message'];
            }
        }
    }
     
    function GroupingStage1Internal($nomor_sep,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method":"grouper",
                            "stage":"1"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            Hapus2("inacbg_grouping_stage1_internal", "no_sep='".$nomor_sep."'");
            $cbg                = validangka($msg['response']['cbg']['tariff']);
            $sub_acute          = validangka($msg['response']['sub_acute']['tariff']);
            $chronic            = validangka($msg['response']['chronic']['tariff']);
            $add_payment_amt    = validangka($msg['response']['add_payment_amt']);
            var_dump("inacbg_grouping_stage1_internal","'".$nomor_sep."','".$msg['response']['cbg']['code']."','".$msg['response']['cbg']['description']."','".($cbg+$sub_acute+$chronic+$add_payment_amt)."'");
            FinalisasiKlaim($nomor_sep,$coder_nik);
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
    }
     
    function GroupingStage12($nomor_sep,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method":"grouper",
                            "stage":"1"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            Hapus2("inacbg_grouping_stage12", "no_sep='".$nomor_sep."'");
            $cbg                = validangka($msg['response']['cbg']['tariff']);
            $sub_acute          = validangka($msg['response']['sub_acute']['tariff']);
            $chronic            = validangka($msg['response']['chronic']['tariff']);
            $add_payment_amt    = validangka($msg['response']['add_payment_amt']);
            var_dump("inacbg_grouping_stage12","'".$nomor_sep."','".$msg['response']['cbg']['code']."','".$msg['response']['cbg']['description']."','".($cbg+$sub_acute+$chronic+$add_payment_amt)."'");
            FinalisasiKlaim($nomor_sep,$coder_nik);
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
    }
     
    function GroupingStage13($nomor_sep,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method":"grouper",
                            "stage":"1"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        if($msg['metadata']['message']=="Ok"){
            Hapus2("inacbg_grouping_stage12", "no_sep='".$nomor_sep."'");
            $cbg                = validangka($msg['response']['cbg']['tariff']);
            $sub_acute          = validangka($msg['response']['sub_acute']['tariff']);
            $chronic            = validangka($msg['response']['chronic']['tariff']);
            $add_payment_amt    = validangka($msg['response']['add_payment_amt']);
            var_dump("inacbg_grouping_stage12","'".$nomor_sep."','".$msg['response']['cbg']['code']."','".$msg['response']['cbg']['description']."','".($cbg+$sub_acute+$chronic+$add_payment_amt+$msg['response']['covid19_data']['episodes'][0]['tariff']+$msg['response']['covid19_data']['episodes'][1]['tariff']+$msg['response']['covid19_data']['episodes'][2]['tariff']+$msg['response']['covid19_data']['episodes'][3]['tariff']+$msg['response']['covid19_data']['episodes'][4]['tariff']+$msg['response']['covid19_data']['episodes'][5]['tariff']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['pemulasaraan']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['kantong']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['peti']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['plastik']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['desinfektan_jenazah']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['mobil']+$msg['response']['covid19_data']['pemulasaraan_jenazah']['desinfektan_mobil']+$msg['response']['covid19_data']['top_up_rawat_gross']+$msg['response']['covid19_data']['top_up_rawat']+$msg['response']['covid19_data']['top_up_jenazah'])."'");
            FinalisasiKlaim($nomor_sep,$coder_nik);
        }else{
            echo "\nRespon INACBG : ".$msg['metadata']['message'];
        }
    }
     
    function GroupingStage2_put($p){	 
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
        // if ($this->form_validation->run() == FALSE){
        //     // Form Validation Errors
        //     $response_data = $this->form_validation->error_array();
        //     $this->REST_Return(422, 'Format error', $response_data);
        //     return;
        // }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $registration_destination_id = $id;
    
        if ($registration_destination_id === 0 || $registration_destination_id === "0") {
            $this->REST_Return(400, 'registration_destination_id is required');
            return;
        }
        $where_r['rd.id'] = $registration_destination_id;
        $select_r = '
        ri.sep
        ';
        $join_r = array(
            array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
            array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary="1"', 'INNER'),
        );
        $registration = $this->Registration->select(0, $select_r, $where_r, $join_r);
        if (count($registration) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $nomor_sep = $registration[0]->sep;
        $special_cmg_option = $dt_put['special_cmg_option'];
        $request = '{
            "metadata": {
                "method":"grouper",
                "stage":"2"
            },
            "data": {
                "nomor_sep":"'.$nomor_sep.'",
                "special_cmg": "'.$special_cmg_option.'"
            }
        }';

        $msg = $this->Request($request);
        $this->load->model("v1/finance/Medical_services_model", "Medical_services");
        if($msg['metadata']['message']=="Ok"){
            $select_m = 'a.id'; 
            $join_m = array();
            $where_m['a.registration_destination_id'] = $registration_destination_id;
            $medical_services = $this->Medical_services->select(0, $select_m, $where_m);
            $cbg                = $msg['response']['cbg']['tariff'];
            $sub_acute          = $msg['response']['sub_acute']['tariff'];
            $chronic            = $msg['response']['chronic']['tariff'];
            $add_payment_amt    = $msg['response']['add_payment_amt'];
            $dt_medical_services = array(
                'id' => $medical_services[0]->id,
                'code_inacbg' => $msg['response']['cbg']['code'],
                'desc_inacbg' => $msg['response']['cbg']['description'],
                'cost_inacbg' => $cbg+$sub_acute+$chronic+$add_payment_amt,
                'is_special_cmg' => 1,
                'json_obj' => json_encode($msg)
            );
            $this->Medical_services->replace($dt_medical_services);
        }
    }
     
    // function FinalisasiKlaim($nomor_sep,$coder_nik){	
    //     $request ='{
    //                     "metadata": {
    //                         "method":"claim_final"
    //                     },
    //                     "data": {
    //                         "nomor_sep":"'.$nomor_sep.'",
    //                         "coder_nik": "'.$coder_nik.'"
    //                     }
    //             }';
    //     $msg= $this->Request($request);
    //     if($msg['metadata']['message']=="Ok"){
    //         //KirimKlaimIndividualKeDC($nomor_sep);
    //     }else{
    //         echo "\nRespon INACBG : ".$msg['metadata']['message'];
    //     }
    // }
     
    function EditUlangKlaim($nomor_sep){	
        $request ='{
                        "metadata": {
                            "method":"reedit_claim"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function KirimKlaimPeriodeKeDC($start_dt,$stop_dt,$jenis_rawat){	
        $request ='{
                        "metadata": {
                            "method":"send_claim"
                        },
                        "data": {
                            "start_dt":"'.$start_dt.'",
                            "stop_dt":"'.$stop_dt.'",
                            "jenis_rawat":"'.$jenis_rawat.'",
                            "date_type":"2"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function KirimKlaimIndividualKeDC($nomor_sep){	
        $request ='{
                        "metadata": {
                            "method":"send_claim_individual"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        //echo $msg['metadata']['message']."";
    }
     
    function MenarikDataKlaimPeriode($start_dt,$stop_dt,$jenis_rawat){	
        $request ='{
                        "metadata": {
                            "method":"pull_claim"
                        },
                        "data": {
                            "start_dt":"'.$start_dt.'",
                            "stop_dt":"'.$stop_dt.'",
                            "jenis_rawat":"'.$jenis_rawat.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function MengambilDataDetailPerklaim($nomor_sep){	
        $request ='{
                        "metadata": {
                            "method":"get_claim_data"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function MengambilSetatusPerklaim($nomor_sep){	
        $request ='{
                        "metadata": {
                            "method":"get_claim_status"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function MenghapusKlaim($nomor_sep,$coder_nik){	
        $request ='{
                        "metadata": {
                            "method":"delete_claim"
                        },
                        "data": {
                            "nomor_sep":"'.$nomor_sep.'",
                            "coder_nik":"'.$coder_nik.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function CetakKlaim($nomor_sep){	
        $request ='{
                        "metadata": {
                            "method": "claim_print"
                        },
                        "data": {
                            "nomor_sep": "'.$nomor_sep.'"
                        }
                }';
        $msg= $this->Request($request);
        echo $msg['metadata']['message']."";
    }
     
    function Request($request){
        $json = $this->mc_encrypt ($request, $this->getKey());  
        $header = array("Content-Type: application/x-www-form-urlencoded");        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrlWS());
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        $response = curl_exec($ch);
        $first = strpos($response, "\n")+1;
        $last = strrpos($response, "\n")-1;
        $hasilresponse = substr($response,$first,strlen($response) - $first - $last);
        $hasildecrypt = $this->mc_decrypt($hasilresponse, $this->getKey());
        //echo $hasildecrypt;
        $msg = json_decode($hasildecrypt,true);
        return $msg;
    }

    public function pdf_to_base64_get($p = '0'){
        // Path to your PDF (can be inside assets, uploads, etc.)
        define('UPLOAD_DIR', 'assets/document/insurance/');
        $filePath = UPLOAD_DIR . $p;

        // Check if file exists
        if (!file_exists($filePath)) {
            show_404('PDF file not found');
            return;
        }

        // Read file contents
        $pdfData = file_get_contents($filePath);

        // Convert to base64
        $base64 = base64_encode($pdfData);

        // Return as JSON
        $response = [
            'filename' => basename($filePath),
            'base64' => 'data:application/pdf;base64,' . $base64
        ];

        $this->REST_Return(200, 'SUCCESS', $response);
        return;
    }

    public function pdf_to_base64_lab_get($p = '0'){
        // Path to your PDF (can be inside assets, uploads, etc.)
        define('UPLOAD_DIR', 'assets/document/lab/');
        $filePath = UPLOAD_DIR . $p;
        // Check if file exists
        if (!file_exists($filePath)) {
            show_404('PDF file not found');
            return;
        }

        // Read file contents
        $pdfData = file_get_contents($filePath);

        // Convert to base64
        $base64 = base64_encode($pdfData);

        // Return as JSON
        $response = [
            'filename' => basename($filePath),
            'base64' => 'data:application/pdf;base64,' . $base64
        ];

        $this->REST_Return(200, 'SUCCESS', $response);
        return;
    }

    public function get_booking_request_get($p = 0) {
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

        $norm = $q_strings['norm'] ?? null;

        if ($norm == null) {
            $this->REST_Return(400, 'norm is required');
            return;
        }

        // SELECT s.id, r.norm, lr.no_request, rr.no_request, pr.no_request 
        // FROM soap s 
        // INNER JOIN registration_destination rd ON s.registration_destination_id = rd.id
        // INNER JOIN registration r ON rd.registration_id = r.id
        // LEFT JOIN laboratory_request lr ON s.id = lr.soap_id AND lr.is_delete = 0
        // LEFT JOIN radiology_request rr ON s.id = rr.soap_id AND rr.is_cancel = 0 AND rr.is_delete = 0
        // LEFT JOIN physiotherapy_request pr ON s.id = pr.soap_id AND pr.is_delete = 0
        // WHERE r.norm = "040158"
        // AND s.is_booking = 1

        $select = 'a.id,
        a.date_added,
        a.doctor_in_charge_id,
        e.name as doctor_in_charge_name,
        lr.id as lab_request_id,
        lr.no_request as lab_no_request,
        rr.id as rad_request_id,
        rr.no_request as rad_no_request,
        pr.id as phy_request_id,
        pr.no_request as phy_no_request
        ';
        $where = array(
            'r.norm' => $norm,
            'a.is_booking' => 1,
        );
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('employee e', 'a.doctor_in_charge_id = e.id', 'INNER'),
            array('laboratory_request lr', 'a.id = lr.soap_id AND lr.is_delete = 0', 'LEFT'),
            array('radiology_request rr', 'a.id = rr.soap_id AND rr.is_cancel = 0 AND rr.is_delete = 0', 'LEFT'),
            array('physiotherapy_request pr', 'a.id = pr.soap_id AND pr.is_delete = 0', 'LEFT'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function transfer_booking_request_put($p = 0) {
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
        $this->form_validation->set_rules($this->transfer_booking_request_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $soap_id = $id;

        if ($soap_id === 0 || $soap_id === "0") {
            $this->REST_Return(400, 'soap_id is required');
            return;
        }

        $select= 'a.id';
        $soap = $this->$model->select($soap_id, $select);
        if (count($soap) == 0) {
            $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
            return;
        }

        $booking_id = $dt_put['booking_id'];
        $request_id = $dt_put['request_id'];
        $type_request_id = $dt_put['type_request_id'];

        $select = 'a.id, lr.no_request as lab_no_request, rr.no_request as rad_no_request, pr.no_request as phy_no_request';
        $where = array();
        $join = array(
            array('laboratory_request lr', 'a.id = lr.soap_id AND lr.is_delete = 0', 'LEFT'),
            array('radiology_request rr', 'a.id = rr.soap_id AND rr.is_cancel = 0 AND rr.is_delete = 0', 'LEFT'),
            array('physiotherapy_request pr', 'a.id = pr.soap_id AND pr.is_delete = 0', 'LEFT'),
        );
        $booking = $this->$model->select($booking_id, $select, $where, $join);
        if (count($booking) == 0) {
            $this->REST_Return(404, 'there is no booking with id ' . $booking_id);
            return;
        }

        $booking_request_count = 0;
        
        for ($i=0; $i < count($booking); $i++) { 
            $lab_no_request = $booking[$i]->lab_no_request;
            $rad_no_request = $booking[$i]->rad_no_request;
            $phy_no_request = $booking[$i]->phy_no_request;

            if ($lab_no_request != null) {
                $booking_request_count += 1;
            }

            if ($rad_no_request != null) {
                $booking_request_count += 1;
            }

            if ($phy_no_request != null) {
                $booking_request_count += 1;
            }
        }

        /* Start Transaction */
        $this->db->trans_start();
        
        if ($type_request_id == 1) {
            $select = 'a.id';
            $laboratory_request = $this->Laboratory_request->select($request_id, $select);
            if (count($laboratory_request) == 0) {
                $this->REST_Return(404, 'there is no laboratory request with id ' . $request_id);
                return;
            }

            $dt_laboratory_request = array(
                'soap_id' => $soap_id,
            );
            $this->Laboratory_request->update_v2($request_id, $dt_laboratory_request);
        } elseif ($type_request_id == 2) {
            $select = 'a.id';
            $radiology_request = $this->Radiology_request->select($request_id, $select);
            if (count($radiology_request) == 0) {
                $this->REST_Return(404, 'there is no radiology request with id ' . $request_id);
                return;
            }

            $dt_radiology_request = array(
                'soap_id' => $soap_id,
            );
            $this->Radiology_request->update_v2($request_id, $dt_radiology_request);
        } elseif ($type_request_id == 3) {
            $select = 'a.id';
            $physiotherapy_request = $this->Physiotherapy_request->select($request_id, $select);
            if (count($physiotherapy_request) == 0) {
                $this->REST_Return(404, 'there is no physiotherapy request with id ' . $request_id);
                return;
            }

            $dt_physiotherapy_request = array(
                'soap_id' => $soap_id,
            );
            $this->Physiotherapy_request->update_v2($request_id, $dt_physiotherapy_request);
        }

        if ($booking_request_count == 1) {
            $dt_booking = array(
                'is_delete' => 1
            );
            $this->$model->update_v2($booking_id, $dt_booking);
        }
      
        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to transfer booking request', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}