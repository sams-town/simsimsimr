<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_adult_model", "{$this->name}_adult");
        $this->load->model("$this->directory/{$this->name}_child_model", "{$this->name}_child");
        $this->load->model("$this->directory/{$this->name}_adult_inpatient_model", "{$this->name}_adult_inpatient");
        $this->load->model("$this->directory/{$this->name}_child_inpatient_model", "{$this->name}_child_inpatient");
        $this->load->model("$this->directory/{$this->name}_neonate_inpatient_model", "{$this->name}_neonate_inpatient");
        $this->load->model("$this->directory/{$this->name}_elderly_inpatient_model", "{$this->name}_elderly_inpatient");
        $this->load->model("$this->directory/{$this->name}_obgyn_model", "{$this->name}_obgyn");
        $this->load->model("$this->directory/{$this->name}_obgyn_special_model", "{$this->name}_obgyn_special");
        $this->load->model("$this->directory/{$this->name}_emergency_model", "{$this->name}_emergency");
        $this->load->model("$this->directory/{$this->name}_triage_model", "{$this->name}_triage");
        $this->load->model("$this->directory/{$this->name}_physiotherapy_model", "{$this->name}_physiotherapy");
        $this->load->model("$this->directory/{$this->name}_soap_model", "{$this->name}_soap");
        $this->load->model("$this->directory/{$this->name}_sdki_slki_siki_model", "{$this->name}_sdki_slki_siki");
        $this->load->model("$this->directory/{$this->name}_hemodialysis_model", "{$this->name}_hemodialysis");
        $this->load->model("$this->directory/Sdki_model", "Sdki");
        $this->load->model("$this->directory/Slki_model", "Slki");
        $this->load->model("$this->directory/Siki_model", "Siki");
        $this->load->model("$this->directory/inpatient/Vital_signs_monitoring_model", "Vital_signs_monitoring");
        $this->load->model("$this->directory/inpatient/Early_warning_score_model", "Early_warning_score");
        $this->load->model("$this->directory/inpatient/Early_warning_score_item_model", "Early_warning_score_item");
        $this->load->model("$this->directory/inpatient/Temperature_chart_model", "Temperature_chart");
        $this->load->model("$this->directory/inpatient/Temperature_chart_item_model", "Temperature_chart_item");
        $this->load->model("$this->directory/inpatient/Pews_model", "Pews");
        $this->load->model("$this->directory/inpatient/Pews_item_model", "Pews_item");
        $this->load->model("$this->directory/Humpty_dumpty_scale_fall_risk_model", "Humpty_dumpty_scale_fall_risk");
        $this->load->model("$this->directory/Morse_scale_fall_risk_model", "Morse_scale_fall_risk");
        $this->load->model("$this->directory/Newborn_baby_model", "Newborn_baby");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_o_model", "Soap_o");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/tr/Transaction_nurse_model", "Transaction_nurse");
        $this->load->model("v1/mr/Letter_model", "Letter");
        $this->load->model("v1/setting/User_model", "User");
        // Load Helper
        $this->load->helper('ews');
        $this->load->helper('utils');

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->save_anamnesis_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'anamesa_type_id', 'label' => 'anamesa_type_id', 'rules' => 'trim'),
            array('field' => 'family_name', 'label' => 'family_name', 'rules' => 'trim'),
            array('field' => 'family_relation_id', 'label' => 'family_relation_id', 'rules' => 'trim'),
            array('field' => 'main_complaint', 'label' => 'main_complaint', 'rules' => 'trim'),
            array('field' => 'history_disease_now', 'label' => 'history_disease_now', 'rules' => 'trim'),
            array('field' => 'history_disease_past', 'label' => 'history_disease_past', 'rules' => 'trim'),
        );

        $this->save_ttv_antropometri_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'systolic_blood_pressure', 'label' => 'systolic_blood_pressure', 'rules' => 'trim'),
            array('field' => 'diastolic_blood_pressure', 'label' => 'diastolic_blood_pressure', 'rules' => 'trim'),
            array('field' => 'pulse_frequency', 'label' => 'pulse_frequency', 'rules' => 'trim'),
            array('field' => 'breath_frequency', 'label' => 'breath_frequency', 'rules' => 'trim'),
            array('field' => 'oximetry_frequency', 'label' => 'oximetry_frequency', 'rules' => 'trim'),
            array('field' => 'temperature', 'label' => 'temperature', 'rules' => 'trim'),
            array('field' => 'oxygen_type_id', 'label' => 'oxygen_type_id', 'rules' => 'trim'),
            array('field' => 'aware', 'label' => 'aware', 'rules' => 'trim'),
            array('field' => 'eye_score', 'label' => 'eye_score', 'rules' => 'trim'),
            array('field' => 'verbal_score', 'label' => 'verbal_score', 'rules' => 'trim'),
            array('field' => 'motor_score', 'label' => 'motor_score', 'rules' => 'trim'),
            array('field' => 'weight', 'label' => 'weight', 'rules' => 'trim'),
            array('field' => 'height', 'label' => 'height', 'rules' => 'trim'),
            array('field' => 'imt', 'label' => 'imt', 'rules' => 'trim'),
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim'),
            array('field' => 'chest_circumference', 'label' => 'chest_circumference', 'rules' => 'trim'),
            array('field' => 'arm_circumference', 'label' => 'arm_circumference', 'rules' => 'trim'),
            array('field' => 'stomach_circumference', 'label' => 'stomach_circumference', 'rules' => 'trim'),
            array('field' => 'knee_circumference', 'label' => 'knee_circumference', 'rules' => 'trim'),
        );

        $this->save_functional_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'aids', 'label' => 'aids', 'rules' => 'trim'),
            array('field' => 'prothesa', 'label' => 'prothesa', 'rules' => 'trim'),
            array('field' => 'disabled', 'label' => 'disabled', 'rules' => 'trim'),
            array('field' => 'adl_type_id', 'label' => 'adl_type_id', 'rules' => 'trim'),
            array('field' => 'pain_score', 'label' => 'pain_score', 'rules' => 'trim'),
        );
        
        $this->save_hemodialysis_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'hemodialysis_date', 'label' => 'hemodialysis_date', 'rules' => 'trim'),
            array('field' => 'hemodialysis_type_id', 'label' => 'hemodialysis_type_id', 'rules' => 'trim'),
            array('field' => 'frequency_type_id', 'label' => 'frequency_type_id', 'rules' => 'trim'),
            array('field' => 'dialysate_type_id', 'label' => 'dialysate_type_id', 'rules' => 'trim'),
            array('field' => 'vascular_type_id', 'label' => 'vascular_type_id', 'rules' => 'trim'),
            array('field' => 'long_hours', 'label' => 'long_hours', 'rules' => 'trim'),
            array('field' => 'blood_velocity', 'label' => 'blood_velocity', 'rules' => 'trim'),
            array('field' => 'initial_dose', 'label' => 'initial_dose', 'rules' => 'trim'),
            array('field' => 'intermittent', 'label' => 'intermittent', 'rules' => 'trim'),
            array('field' => 'ufg', 'label' => 'ufg', 'rules' => 'trim'),
            array('field' => 'dialysate_velocity', 'label' => 'dialysate_velocity', 'rules' => 'trim'),
            array('field' => 'maintenance', 'label' => 'maintenance', 'rules' => 'trim'),
            array('field' => 'lmwh', 'label' => 'lmwh', 'rules' => 'trim'),
            array('field' => 'dialysate_temperature', 'label' => 'dialysate_temperature', 'rules' => 'trim'),
            array('field' => 'vascular_jugularis', 'label' => 'vascular_jugularis', 'rules' => 'trim'),
            array('field' => 'heparin_type_id', 'label' => 'heparin_type_id', 'rules' => 'trim'),
            array('field' => 'without_heparin', 'label' => 'without_heparin', 'rules' => 'trim'),
            array('field' => 'profiling_type_id', 'label' => 'profiling_type_id', 'rules' => 'trim'),
            array('field' => 'ultrafiltration', 'label' => 'ultrafiltration', 'rules' => 'trim'),
            array('field' => 'natrium', 'label' => 'natrium', 'rules' => 'trim'),
            array('field' => 'bicarbonate', 'label' => 'bicarbonate', 'rules' => 'trim'),
            array('field' => 'therapy', 'label' => 'therapy', 'rules' => 'trim'),
            array('field' => 'medicine', 'label' => 'medicine', 'rules' => 'trim'),
            array('field' => 'injection', 'label' => 'injection', 'rules' => 'trim'),
        );

        $this->save_emergency_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'anamesa_type_id', 'label' => 'anamesa_type_id', 'rules' => 'trim'),
            array('field' => 'been_hospitalized_type_id', 'label' => 'been_hospitalized_type_id', 'rules' => 'trim'),
            array('field' => 'been_hospitalized_answer', 'label' => 'been_hospitalized_answer', 'rules' => 'trim'),
            array('field' => 'any_history_family_disease_type_id', 'label' => 'any_history_family_disease_type_id', 'rules' => 'trim'),
            array('field' => 'any_history_family_answer_arry', 'label' => 'any_history_family_answer_arry', 'rules' => 'trim'),
            array('field' => 'any_history_family_answer', 'label' => 'any_history_family_answer', 'rules' => 'trim'),
            array('field' => 'any_surgery', 'label' => 'any_surgery', 'rules' => 'trim'),
            array('field' => 'any_surgery_date', 'label' => 'any_surgery_date', 'rules' => 'trim'),
            array('field' => 'complication', 'label' => 'complication', 'rules' => 'trim'),
            array('field' => 'history_allergy_type_id', 'label' => 'history_allergy_type_id', 'rules' => 'trim'),
            array('field' => 'history_allergy', 'label' => 'history_allergy', 'rules' => 'trim'),
            array('field' => 'history_psychological_arry', 'label' => 'history_psychological_arry', 'rules' => 'trim'),
            array('field' => 'body_mass_index', 'label' => 'body_mass_index', 'rules' => 'trim'),
            array('field' => 'patient_food_intake', 'label' => 'patient_food_intake', 'rules' => 'trim'),
            array('field' => 'patient_certain_diets', 'label' => 'patient_certain_diets', 'rules' => 'trim'),
            array('field' => 'six_month_lose_weight', 'label' => 'six_month_lose_weight', 'rules' => 'trim'),
            array('field' => 'lose_weight', 'label' => 'lose_weight', 'rules' => 'trim'),
            array('field' => 'patient_disease_condition', 'label' => 'patient_disease_condition', 'rules' => 'trim'),
            array('field' => 'nutrition_total_score', 'label' => 'nutrition_total_score', 'rules' => 'trim'),
            array('field' => 'fall_risk_method_type_id', 'label' => 'fall_risk_method_type_id', 'rules' => 'trim'),
            array('field' => 'fall_risk_type_id', 'label' => 'fall_risk_type_id', 'rules' => 'trim'),
            array('field' => 'nursing_diagnoses_arry', 'label' => 'nursing_diagnoses_arry', 'rules' => 'trim'),
            array('field' => 'nursing_diagnoses', 'label' => 'nursing_diagnoses', 'rules' => 'trim'),
            array('field' => 'implementation_arry', 'label' => 'implementation_arry', 'rules' => 'trim'),
            array('field' => 'implementation', 'label' => 'implementation', 'rules' => 'trim'),
            array('field' => 'any_surgery_type_id', 'label' => 'any_surgery_type_id', 'rules' => 'trim'),
            array('field' => 'fall_risk_component_a', 'label' => 'fall_risk_component_a', 'rules' => 'trim'),
            array('field' => 'fall_risk_component_b', 'label' => 'fall_risk_component_b', 'rules' => 'trim'),
        );

        $this->save_adult_inpatient_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'psychological_status_arry', 'label' => 'psychological_status_arry', 'rules' => 'trim'),
            array('field' => 'psychological_status', 'label' => 'psychological_status', 'rules' => 'trim'),
            array('field' => 'mental_status_arry', 'label' => 'mental_status_arry', 'rules' => 'trim'),
            array('field' => 'mental_status_problems', 'label' => 'mental_status_problems', 'rules' => 'trim'),
            array('field' => 'mental_status_violence', 'label' => 'mental_status_violence', 'rules' => 'trim'),
            array('field' => 'elimination_defecate', 'label' => 'elimination_defecate', 'rules' => 'trim'),
            array('field' => 'elimination_urination', 'label' => 'elimination_urination', 'rules' => 'trim'),
            array('field' => 'elimination_consistency_type_id', 'label' => 'elimination_consistency_type_id', 'rules' => 'trim'),
            array('field' => 'elimination_color_type_id', 'label' => 'elimination_color_type_id', 'rules' => 'trim'),
            array('field' => 'elimination_color', 'label' => 'elimination_color', 'rules' => 'trim'),
            array('field' => 'sore_complaints_type_id', 'label' => 'sore_complaints_type_id', 'rules' => 'trim'),
            array('field' => 'sore_complaints', 'label' => 'sore_complaints', 'rules' => 'trim'),
            array('field' => 'sore_goes_away_arry', 'label' => 'sore_goes_away_arry', 'rules' => 'trim'),
            array('field' => 'sore_goes_away', 'label' => 'sore_goes_away', 'rules' => 'trim'),
            array('field' => 'sore_method_type_id', 'label' => 'sore_method_type_id', 'rules' => 'trim'),
            array('field' => 'sore_nrs_score', 'label' => 'sore_nrs_score', 'rules' => 'trim'),
            array('field' => 'sore_next_nrs_score', 'label' => 'sore_next_nrs_score', 'rules' => 'trim'),
            array('field' => 'sore_affects_arry', 'label' => 'sore_affects_arry', 'rules' => 'trim'),
            array('field' => 'sore_affects', 'label' => 'sore_affects', 'rules' => 'trim'),
            array('field' => 'discharge_treatment_duration', 'label' => 'discharge_treatment_duration', 'rules' => 'trim'),
            array('field' => 'discharge_further_care_arry', 'label' => 'discharge_further_care_arry', 'rules' => 'trim'),
            array('field' => 'tell_doctor_type_id', 'label' => 'tell_doctor_type_id', 'rules' => 'trim'),
            array('field' => 'tell_doctor_date', 'label' => 'tell_doctor_date', 'rules' => 'trim'),
            array('field' => 'face_score', 'label' => 'face_score', 'rules' => 'trim'),
            array('field' => 'legs_score', 'label' => 'legs_score', 'rules' => 'trim'),
            array('field' => 'activity_score', 'label' => 'activity_score', 'rules' => 'trim'),
            array('field' => 'cry_score', 'label' => 'cry_score', 'rules' => 'trim'),
            array('field' => 'consolability_score', 'label' => 'consolability_score', 'rules' => 'trim'),
            array('field' => 'sore_total_score', 'label' => 'sore_total_score', 'rules' => 'trim'),
            array('field' => 'duration', 'label' => 'duration', 'rules' => 'trim'),
            array('field' => 'location', 'label' => 'location', 'rules' => 'trim'),
            array('field' => 'response', 'label' => 'response', 'rules' => 'trim'),
            array('field' => 'frequency', 'label' => 'frequency', 'rules' => 'trim'),
            array('field' => 'sore_quality_arry', 'label' => 'sore_quality_arry', 'rules' => 'trim'),
            array('field' => 'medical_devices_type_id', 'label' => 'medical_devices_type_id', 'rules' => 'trim'),
            array('field' => 'medical_devices_raw', 'label' => 'medical_devices_raw', 'rules' => 'trim'),
            array('field' => 'examination_history_raw', 'label' => 'examination_history_raw', 'rules' => 'trim'),
            array('field' => 'care_plan_diet_nutrition_type_id', 'label' => 'care_plan_diet_nutrition_type_id', 'rules' => 'trim'),
            array('field' => 'care_plan_diet_nutrition', 'label' => 'care_plan_diet_nutrition', 'rules' => 'trim'),
            array('field' => 'care_plan_rehabilitation_type_id', 'label' => 'care_plan_rehabilitation_type_id', 'rules' => 'trim'),
            array('field' => 'care_plan_rehabilitation', 'label' => 'care_plan_rehabilitation', 'rules' => 'trim'),
            array('field' => 'care_plan_wound_type_id', 'label' => 'care_plan_wound_type_id', 'rules' => 'trim'),
            array('field' => 'care_plan_wound', 'label' => 'care_plan_wound', 'rules' => 'trim'),
            array('field' => 'care_plan_pain_management_type_id', 'label' => 'care_plan_pain_management_type_id', 'rules' => 'trim'),
            array('field' => 'care_plan_pain_management', 'label' => 'care_plan_pain_management', 'rules' => 'trim'),
            array('field' => 'care_plan_other', 'label' => 'care_plan_other', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_type_id', 'label' => 'evaluation_smoke_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_amount', 'label' => 'evaluation_smoke_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_long', 'label' => 'evaluation_smoke_long', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_unit', 'label' => 'evaluation_smoke_unit', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_type_id', 'label' => 'evaluation_alcohol_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_type', 'label' => 'evaluation_alcohol_type', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_amount', 'label' => 'evaluation_alcohol_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_type_id', 'label' => 'evaluation_sedative_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_type', 'label' => 'evaluation_sedative_type', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_amount', 'label' => 'evaluation_sedative_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_unit', 'label' => 'evaluation_sedative_unit', 'rules' => 'trim'),
            array('field' => 'evaluation_sleep_type_id', 'label' => 'evaluation_sleep_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sleep', 'label' => 'evaluation_sleep', 'rules' => 'trim'),
            array('field' => 'evaluation_daily_type_id', 'label' => 'evaluation_daily_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_activity_type_id', 'label' => 'evaluation_activity_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_top_type_id', 'label' => 'evaluation_extremities_top_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_grip_type_id', 'label' => 'evaluation_grip_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_grip_other', 'label' => 'evaluation_grip_other', 'rules' => 'trim'),
            array('field' => 'evaluation_coordination_type_id', 'label' => 'evaluation_coordination_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_coordination_other', 'label' => 'evaluation_coordination_other', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_bottom_type_id', 'label' => 'evaluation_extremities_bottom_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_bottom_bone', 'label' => 'evaluation_extremities_bottom_bone', 'rules' => 'trim'),
            array('field' => 'evaluation_ambulation_type_id', 'label' => 'evaluation_ambulation_type_id', 'rules' => 'trim'),
        );

        $this->save_child_inpatient_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'growth_birth_weight', 'label' => 'growth_birth_weight', 'rules' => 'trim'),
            array('field' => 'growth_birth_height', 'label' => 'growth_birth_height', 'rules' => 'trim'),
            array('field' => 'growth_breast_milk_age', 'label' => 'growth_breast_milk_age', 'rules' => 'trim'),
            array('field' => 'growth_formula_milk_age', 'label' => 'growth_formula_milk_age', 'rules' => 'trim'),
            array('field' => 'growth_solid_food_age', 'label' => 'growth_solid_food_age', 'rules' => 'trim'),
            array('field' => 'growth_additional_food_age', 'label' => 'growth_additional_food_age', 'rules' => 'trim'),
            array('field' => 'growth_prone_age', 'label' => 'growth_prone_age', 'rules' => 'trim'),
            array('field' => 'growth_sit_age', 'label' => 'growth_sit_age', 'rules' => 'trim'),
            array('field' => 'growth_crawl_age', 'label' => 'growth_crawl_age', 'rules' => 'trim'),
            array('field' => 'growth_stand_age', 'label' => 'growth_stand_age', 'rules' => 'trim'),
            array('field' => 'growth_walk_age', 'label' => 'growth_walk_age', 'rules' => 'trim'),
            array('field' => 'medical_devices', 'label' => 'medical_devices', 'rules' => 'trim'),
            array('field' => 'examination_history', 'label' => 'examination_history', 'rules' => 'trim'),
            array('field' => 'immunization_bcg_arry', 'label' => 'immunization_bcg_arry', 'rules' => 'trim'),
            array('field' => 'immunization_hepatitis_b_arry', 'label' => 'immunization_hepatitis_b_arry', 'rules' => 'trim'),
            array('field' => 'immunization_dpt_arry', 'label' => 'immunization_dpt_arry', 'rules' => 'trim'),
            array('field' => 'immunization_polio_arry', 'label' => 'immunization_polio_arry', 'rules' => 'trim'),
            array('field' => 'immunization_measles_arry', 'label' => 'immunization_measles_arry', 'rules' => 'trim'),
            array('field' => 'immunization_hib_arry', 'label' => 'immunization_hib_arry', 'rules' => 'trim'),
            array('field' => 'immunization_ipd_arry', 'label' => 'immunization_ipd_arry', 'rules' => 'trim'),
            array('field' => 'immunization_flu_arry', 'label' => 'immunization_flu_arry', 'rules' => 'trim'),
            array('field' => 'immunization_chickenpox_arry', 'label' => 'immunization_chickenpox_arry', 'rules' => 'trim'),
            array('field' => 'immunization_mmr_arry', 'label' => 'immunization_mmr_arry', 'rules' => 'trim'),
            array('field' => 'immunization_typhoid_arry', 'label' => 'immunization_typhoid_arry', 'rules' => 'trim'),
            array('field' => 'immunization_hepatitis_a_arry', 'label' => 'immunization_hepatitis_a_arry', 'rules' => 'trim'),
            array('field' => 'immunization_hpv_arry', 'label' => 'immunization_hpv_arry', 'rules' => 'trim'),
            array('field' => 'immunization_rotavirus_arry', 'label' => 'immunization_rotavirus_arry', 'rules' => 'trim'),
            array('field' => 'immunization_influenza_arry', 'label' => 'immunization_influenza_arry', 'rules' => 'trim'),
            array('field' => 'comm_needs_speech_type_id', 'label' => 'comm_needs_speech_type_id', 'rules' => 'trim'),
            array('field' => 'comm_needs_speech_disorder', 'label' => 'comm_needs_speech_disorder', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_arry', 'label' => 'comm_needs_colloquial_arry', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_local', 'label' => 'comm_needs_colloquial_local', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_other', 'label' => 'comm_needs_colloquial_other', 'rules' => 'trim'),
            array('field' => 'comm_needs_translator_type_id', 'label' => 'comm_needs_translator_type_id', 'rules' => 'trim'),
            array('field' => 'edu_needs_barriers_type_id', 'label' => 'edu_needs_barriers_type_id', 'rules' => 'trim'),
            array('field' => 'edu_needs_barriers_arry', 'label' => 'edu_needs_barriers_arry', 'rules' => 'trim'),
            array('field' => 'edu_needs_barriers_other', 'label' => 'edu_needs_barriers_other', 'rules' => 'trim'),
            array('field' => 'edu_needs_preferred_arry', 'label' => 'edu_needs_preferred_arry', 'rules' => 'trim'),
            array('field' => 'edu_needs_level_of_education_type_id', 'label' => 'edu_needs_level_of_education_type_id', 'rules' => 'trim'),
            array('field' => 'edu_needs_level_of_education_other', 'label' => 'edu_needs_level_of_education_other', 'rules' => 'trim'),
            array('field' => 'edu_needs_information_arry', 'label' => 'edu_needs_information_arry', 'rules' => 'trim'),
            array('field' => 'edu_needs_information_other', 'label' => 'edu_needs_information_other', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_type_id', 'label' => 'evaluation_smoke_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_amount', 'label' => 'evaluation_smoke_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_long', 'label' => 'evaluation_smoke_long', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_unit', 'label' => 'evaluation_smoke_unit', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_type_id', 'label' => 'evaluation_alcohol_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_type', 'label' => 'evaluation_alcohol_type', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_amount', 'label' => 'evaluation_alcohol_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_type_id', 'label' => 'evaluation_sedative_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_type', 'label' => 'evaluation_sedative_type', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_amount', 'label' => 'evaluation_sedative_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_unit', 'label' => 'evaluation_sedative_unit', 'rules' => 'trim'),
            array('field' => 'evaluation_sleep_type_id', 'label' => 'evaluation_sleep_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sleep', 'label' => 'evaluation_sleep', 'rules' => 'trim'),
            array('field' => 'evaluation_daily_type_id', 'label' => 'evaluation_daily_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_activity_type_id', 'label' => 'evaluation_activity_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_top_type_id', 'label' => 'evaluation_extremities_top_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_grip_type_id', 'label' => 'evaluation_grip_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_grip_other', 'label' => 'evaluation_grip_other', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_bottom_type_id', 'label' => 'evaluation_extremities_bottom_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_bottom_bone', 'label' => 'evaluation_extremities_bottom_bone', 'rules' => 'trim'),
            array('field' => 'evaluation_ambulation_type_id', 'label' => 'evaluation_ambulation_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_mental_status_type_id', 'label' => 'evaluation_mental_status_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_psychological_status_type_id', 'label' => 'evaluation_psychological_status_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_psychological_status_other', 'label' => 'evaluation_psychological_status_other', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_defecate_type_id', 'label' => 'evaluation_elimination_defecate_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_defecate_freq', 'label' => 'evaluation_elimination_defecate_freq', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_urination_type_id', 'label' => 'evaluation_elimination_urination_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_menstruation_type_id', 'label' => 'evaluation_sexual_menstruation_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_circumcision_type_id', 'label' => 'evaluation_sexual_circumcision_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_circumcision', 'label' => 'evaluation_sexual_circumcision', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_problem_type_id', 'label' => 'evaluation_sexual_problem_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_problem', 'label' => 'evaluation_sexual_problem', 'rules' => 'trim'),
        );

        $this->save_neonate_inpatient_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'gestational_age', 'label' => 'gestational_age', 'rules' => 'trim'),
            array('field' => 'corrected_age', 'label' => 'corrected_age', 'rules' => 'trim'),
            array('field' => 'type_of_delivery', 'label' => 'type_of_delivery', 'rules' => 'trim'),
            array('field' => 'labor_diagnosis', 'label' => 'labor_diagnosis', 'rules' => 'trim'),
            array('field' => 'maternal_medical_history', 'label' => 'maternal_medical_history', 'rules' => 'trim'),
            array('field' => 'baby_gender', 'label' => 'baby_gender', 'rules' => 'trim'),
            array('field' => 'birth_history_arry', 'label' => 'birth_history_arry', 'rules' => 'trim'),
            array('field' => 'birth_history_lk', 'label' => 'birth_history_lk', 'rules' => 'trim'),
            array('field' => 'birth_history_ld', 'label' => 'birth_history_ld', 'rules' => 'trim'),
            array('field' => 'medical_devices_raw', 'label' => 'medical_devices_raw', 'rules' => 'trim'),
            array('field' => 'examination_history_raw', 'label' => 'examination_history_raw', 'rules' => 'trim'),
            array('field' => 'postnatal_drug_arry', 'label' => 'postnatal_drug_arry', 'rules' => 'trim'),
            array('field' => 'postnatal_drug_vit_k', 'label' => 'postnatal_drug_vit_k', 'rules' => 'trim'),
            array('field' => 'postnatal_drug_eye', 'label' => 'postnatal_drug_eye', 'rules' => 'trim'),
            array('field' => 'postnatal_drug_other', 'label' => 'postnatal_drug_other', 'rules' => 'trim'),
            array('field' => 'comm_needs_speech_type_id', 'label' => 'comm_needs_speech_type_id', 'rules' => 'trim'),
            array('field' => 'comm_needs_speech_disorder', 'label' => 'comm_needs_speech_disorder', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_arry', 'label' => 'comm_needs_colloquial_arry', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_local', 'label' => 'comm_needs_colloquial_local', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_other', 'label' => 'comm_needs_colloquial_other', 'rules' => 'trim'),
            array('field' => 'comm_needs_translator_type_id', 'label' => 'comm_needs_translator_type_id', 'rules' => 'trim'),
            array('field' => 'edu_needs_level_of_education_type_id', 'label' => 'edu_needs_level_of_education_type_id', 'rules' => 'trim'),
            array('field' => 'edu_needs_level_of_education_other', 'label' => 'edu_needs_level_of_education_other', 'rules' => 'trim'),
            array('field' => 'edu_needs_information_arry', 'label' => 'edu_needs_information_arry', 'rules' => 'trim'),
            array('field' => 'edu_needs_information_other', 'label' => 'edu_needs_information_other', 'rules' => 'trim'),
            array('field' => 'socio_cultural_ethnic', 'label' => 'socio_cultural_ethnic', 'rules' => 'trim'),
            array('field' => 'socio_cultural_citizen', 'label' => 'socio_cultural_citizen', 'rules' => 'trim'),
            array('field' => 'socio_cultural_residence_type_id', 'label' => 'socio_cultural_residence_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_who_cares_type_id', 'label' => 'socio_cultural_who_cares_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_parent', 'label' => 'socio_cultural_parent', 'rules' => 'trim'),
            array('field' => 'socio_cultural_visit_type_id', 'label' => 'socio_cultural_visit_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_touch_type_id', 'label' => 'socio_cultural_touch_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_carry_type_id', 'label' => 'socio_cultural_carry_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_eye_contact_type_id', 'label' => 'socio_cultural_eye_contact_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_speak_type_id', 'label' => 'socio_cultural_speak_type_id', 'rules' => 'trim'),
            array('field' => 'socio_cultural_other', 'label' => 'socio_cultural_other', 'rules' => 'trim'),
            array('field' => 'evaluation_comfort_arry', 'label' => 'evaluation_comfort_arry', 'rules' => 'trim'),
            array('field' => 'evaluation_comfort_device_type_id', 'label' => 'evaluation_comfort_device_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_comfort_other', 'label' => 'evaluation_comfort_other', 'rules' => 'trim'),
            array('field' => 'evaluation_rest_pattern_type_id', 'label' => 'evaluation_rest_pattern_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_vagina_type_id', 'label' => 'evaluation_vagina_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_catheter_woman_type_id', 'label' => 'evaluation_catheter_woman_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_menstruation_type_id', 'label' => 'evaluation_menstruation_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_reproduction_woman_other', 'label' => 'evaluation_reproduction_woman_other', 'rules' => 'trim'),
            array('field' => 'evaluation_prepuce_type_id', 'label' => 'evaluation_prepuce_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_scrotum_type_id', 'label' => 'evaluation_scrotum_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_catheter_man_type_id', 'label' => 'evaluation_catheter_man_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_hyposdias_type_id', 'label' => 'evaluation_hyposdias_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_reproduction_man_other', 'label' => 'evaluation_reproduction_man_other', 'rules' => 'trim'),
            array('field' => 'evaluation_urination_freq', 'label' => 'evaluation_urination_freq', 'rules' => 'trim'),
            array('field' => 'evaluation_urine_production', 'label' => 'evaluation_urine_production', 'rules' => 'trim'),
            array('field' => 'evaluation_urine_type_id', 'label' => 'evaluation_urine_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_catheter_type_id', 'label' => 'evaluation_elimination_catheter_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_dysuria_type_id', 'label' => 'evaluation_dysuria_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_other', 'label' => 'evaluation_elimination_other', 'rules' => 'trim'),
            array('field' => 'evaluation_client_movement_arry', 'label' => 'evaluation_client_movement_arry', 'rules' => 'trim'),
            array('field' => 'evaluation_safety_other', 'label' => 'evaluation_safety_other', 'rules' => 'trim'),
        );

        $this->save_obgyn_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'medical_devices', 'label' => 'medical_devices', 'rules' => 'trim'),
            array('field' => 'examination_history', 'label' => 'examination_history', 'rules' => 'trim'),
            array('field' => 'comm_needs_speech_type_id', 'label' => 'comm_needs_speech_type_id', 'rules' => 'trim'),
            array('field' => 'comm_needs_speech_disorder', 'label' => 'comm_needs_speech_disorder', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_arry', 'label' => 'comm_needs_colloquial_arry', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_local', 'label' => 'comm_needs_colloquial_local', 'rules' => 'trim'),
            array('field' => 'comm_needs_colloquial_other', 'label' => 'comm_needs_colloquial_other', 'rules' => 'trim'),
            array('field' => 'comm_needs_translator_type_id', 'label' => 'comm_needs_translator_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_unit', 'label' => 'evaluation_smoke_unit', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_unit', 'label' => 'evaluation_sedative_unit', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_type_id', 'label' => 'evaluation_smoke_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_amount', 'label' => 'evaluation_smoke_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_smoke_long', 'label' => 'evaluation_smoke_long', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_type_id', 'label' => 'evaluation_alcohol_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_type', 'label' => 'evaluation_alcohol_type', 'rules' => 'trim'),
            array('field' => 'evaluation_alcohol_amount', 'label' => 'evaluation_alcohol_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_type_id', 'label' => 'evaluation_sedative_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_type', 'label' => 'evaluation_sedative_type', 'rules' => 'trim'),
            array('field' => 'evaluation_sedative_amount', 'label' => 'evaluation_sedative_amount', 'rules' => 'trim'),
            array('field' => 'evaluation_sleep_type_id', 'label' => 'evaluation_sleep_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sleep', 'label' => 'evaluation_sleep', 'rules' => 'trim'),
            array('field' => 'evaluation_daily_type_id', 'label' => 'evaluation_daily_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_activity_type_id', 'label' => 'evaluation_activity_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_top_type_id', 'label' => 'evaluation_extremities_top_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_grip_type_id', 'label' => 'evaluation_grip_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_grip_other', 'label' => 'evaluation_grip_other', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_bottom_type_id', 'label' => 'evaluation_extremities_bottom_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_extremities_bottom_bone', 'label' => 'evaluation_extremities_bottom_bone', 'rules' => 'trim'),
            array('field' => 'evaluation_ambulation_type_id', 'label' => 'evaluation_ambulation_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_mental_status_type_id', 'label' => 'evaluation_mental_status_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_psychological_status_type_id', 'label' => 'evaluation_psychological_status_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_psychological_status_other', 'label' => 'evaluation_psychological_status_other', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_defecate_type_id', 'label' => 'evaluation_elimination_defecate_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_defecate_freq', 'label' => 'evaluation_elimination_defecate_freq', 'rules' => 'trim'),
            array('field' => 'evaluation_elimination_urination_type_id', 'label' => 'evaluation_elimination_urination_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_marital_status_type_id', 'label' => 'evaluation_sexual_marital_status_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_marital_status', 'label' => 'evaluation_sexual_marital_status', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_menstruation_type_id', 'label' => 'evaluation_sexual_menstruation_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_obstetrics_gynecology', 'label' => 'evaluation_sexual_obstetrics_gynecology', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_other_habits', 'label' => 'evaluation_sexual_other_habits', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_circumcision_type_id', 'label' => 'evaluation_sexual_circumcision_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_circumcision', 'label' => 'evaluation_sexual_circumcision', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_problem_type_id', 'label' => 'evaluation_sexual_problem_type_id', 'rules' => 'trim'),
            array('field' => 'evaluation_sexual_problem', 'label' => 'evaluation_sexual_problem', 'rules' => 'trim'),
            array('field' => 'birth_history', 'label' => 'birth_history', 'rules' => 'trim'),
        );

        $this->save_obgyn_special_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'anamnesis_g', 'label' => 'anamnesis_g', 'rules' => 'trim'),
            array('field' => 'anamnesis_p', 'label' => 'anamnesis_p', 'rules' => 'trim'),
            array('field' => 'anamnesis_a', 'label' => 'anamnesis_a', 'rules' => 'trim'),
            array('field' => 'anamnesis_h', 'label' => 'anamnesis_h', 'rules' => 'trim'),
            array('field' => 'anamnesis_period', 'label' => 'anamnesis_period', 'rules' => 'trim'),
            array('field' => 'anamnesis_antenatal_type_id', 'label' => 'anamnesis_antenatal_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_disease_pregnancy_type_id', 'label' => 'anamnesis_disease_pregnancy_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_disease_pregnancy_other', 'label' => 'anamnesis_disease_pregnancy_other', 'rules' => 'trim'),
            array('field' => 'anamnesis_his_type_id', 'label' => 'anamnesis_his_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_his_date', 'label' => 'anamnesis_his_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_his_hour', 'label' => 'anamnesis_his_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_type_id', 'label' => 'anamnesis_amniotic_fluid_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_date', 'label' => 'anamnesis_amniotic_fluid_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_hour', 'label' => 'anamnesis_amniotic_fluid_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_color_type_id', 'label' => 'anamnesis_amniotic_fluid_color_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_color_other', 'label' => 'anamnesis_amniotic_fluid_color_other', 'rules' => 'trim'),
            array('field' => 'anamnesis_blood_slym_type_id', 'label' => 'anamnesis_blood_slym_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_type_id', 'label' => 'anamnesis_antepartum_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_date', 'label' => 'anamnesis_antepartum_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_hour', 'label' => 'anamnesis_antepartum_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_amount', 'label' => 'anamnesis_antepartum_amount', 'rules' => 'trim'),
            array('field' => 'anamnesis_seizure_type_id', 'label' => 'anamnesis_seizure_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_seizure_clonic_long', 'label' => 'anamnesis_seizure_clonic_long', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_type_id', 'label' => 'anamnesis_straining_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_date', 'label' => 'anamnesis_straining_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_hour', 'label' => 'anamnesis_straining_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_amount', 'label' => 'anamnesis_straining_amount', 'rules' => 'trim'),
            array('field' => 'anamnesis_other_problem', 'label' => 'anamnesis_other_problem', 'rules' => 'trim'),
            array('field' => 'physical_leopold_one', 'label' => 'physical_leopold_one', 'rules' => 'trim'),
            array('field' => 'physical_leopold_two_type_id', 'label' => 'physical_leopold_two_type_id', 'rules' => 'trim'),
            array('field' => 'physical_leopold_three_type_id', 'label' => 'physical_leopold_three_type_id', 'rules' => 'trim'),
            array('field' => 'physical_leopold_three_other', 'label' => 'physical_leopold_three_other', 'rules' => 'trim'),
            array('field' => 'physical_leopold_four_type_id', 'label' => 'physical_leopold_four_type_id', 'rules' => 'trim'),
            array('field' => 'physical_fetal_heart_rate_freq', 'label' => 'physical_fetal_heart_rate_freq', 'rules' => 'trim'),
            array('field' => 'physical_fetal_heart_rate_rhythm_type_id', 'label' => 'physical_fetal_heart_rate_rhythm_type_id', 'rules' => 'trim'),
            array('field' => 'physical_vulva_type_id', 'label' => 'physical_vulva_type_id', 'rules' => 'trim'),
            array('field' => 'physical_vulva_other', 'label' => 'physical_vulva_other', 'rules' => 'trim'),
            array('field' => 'physical_internal_portio_type_id', 'label' => 'physical_internal_portio_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_opening_type_id', 'label' => 'physical_internal_opening_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_opening_cm', 'label' => 'physical_internal_opening_cm', 'rules' => 'trim'),
            array('field' => 'physical_internal_depletion_percent', 'label' => 'physical_internal_depletion_percent', 'rules' => 'trim'),
            array('field' => 'physical_internal_amniotic_type_id', 'label' => 'physical_internal_amniotic_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_amniotic_color', 'label' => 'physical_internal_amniotic_color', 'rules' => 'trim'),
            array('field' => 'physical_internal_presentation_type_id', 'label' => 'physical_internal_presentation_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_presentation_other', 'label' => 'physical_internal_presentation_other', 'rules' => 'trim'),
            array('field' => 'physical_ctg_baseline', 'label' => 'physical_ctg_baseline', 'rules' => 'trim'),
            array('field' => 'physical_ctg_variability_type_id', 'label' => 'physical_ctg_variability_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_acceleration_type_id', 'label' => 'physical_ctg_acceleration_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_deceleration_type_id', 'label' => 'physical_ctg_deceleration_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_type_id', 'label' => 'physical_ctg_contractions_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_freq', 'label' => 'physical_ctg_contractions_freq', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_duration', 'label' => 'physical_ctg_contractions_duration', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_strength', 'label' => 'physical_ctg_contractions_strength', 'rules' => 'trim'),
            array('field' => 'physical_ctg_fetal_movement', 'label' => 'physical_ctg_fetal_movement', 'rules' => 'trim'),
            array('field' => 'physical_ctg_interpretation', 'label' => 'physical_ctg_interpretation', 'rules' => 'trim'),
            array('field' => 'diagnosis_mother', 'label' => 'diagnosis_mother', 'rules' => 'trim'),
            array('field' => 'diagnosis_baby', 'label' => 'diagnosis_baby', 'rules' => 'trim'),
            array('field' => 'prognosis_mother_type_id', 'label' => 'prognosis_mother_type_id', 'rules' => 'trim'),
            array('field' => 'prognosis_mother_night', 'label' => 'prognosis_mother_night', 'rules' => 'trim'),
            array('field' => 'prognosis_baby_type_id', 'label' => 'prognosis_baby_type_id', 'rules' => 'trim'),
            array('field' => 'prognosis_baby_night', 'label' => 'prognosis_baby_night', 'rules' => 'trim'),
            array('field' => 'plan_type_id', 'label' => 'plan_type_id', 'rules' => 'trim'),
            array('field' => 'obstetric_problems', 'label' => 'obstetric_problems', 'rules' => 'trim'),
        );

        $this->save_obgyn_anamnesis_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'anamnesis_g', 'label' => 'anamnesis_g', 'rules' => 'trim'),
            array('field' => 'anamnesis_p', 'label' => 'anamnesis_p', 'rules' => 'trim'),
            array('field' => 'anamnesis_a', 'label' => 'anamnesis_a', 'rules' => 'trim'),
            array('field' => 'anamnesis_h', 'label' => 'anamnesis_h', 'rules' => 'trim'),
            array('field' => 'anamnesis_period', 'label' => 'anamnesis_period', 'rules' => 'trim'),
            array('field' => 'anamnesis_antenatal_type_id', 'label' => 'anamnesis_antenatal_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_disease_pregnancy_type_id', 'label' => 'anamnesis_disease_pregnancy_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_disease_pregnancy_other', 'label' => 'anamnesis_disease_pregnancy_other', 'rules' => 'trim'),
            array('field' => 'anamnesis_his_type_id', 'label' => 'anamnesis_his_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_his_date', 'label' => 'anamnesis_his_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_his_hour', 'label' => 'anamnesis_his_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_type_id', 'label' => 'anamnesis_amniotic_fluid_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_date', 'label' => 'anamnesis_amniotic_fluid_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_hour', 'label' => 'anamnesis_amniotic_fluid_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_color_type_id', 'label' => 'anamnesis_amniotic_fluid_color_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_amniotic_fluid_color_other', 'label' => 'anamnesis_amniotic_fluid_color_other', 'rules' => 'trim'),
            array('field' => 'anamnesis_blood_slym_type_id', 'label' => 'anamnesis_blood_slym_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_type_id', 'label' => 'anamnesis_antepartum_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_date', 'label' => 'anamnesis_antepartum_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_hour', 'label' => 'anamnesis_antepartum_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_antepartum_amount', 'label' => 'anamnesis_antepartum_amount', 'rules' => 'trim'),
            array('field' => 'anamnesis_seizure_type_id', 'label' => 'anamnesis_seizure_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_seizure_clonic_long', 'label' => 'anamnesis_seizure_clonic_long', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_type_id', 'label' => 'anamnesis_straining_type_id', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_date', 'label' => 'anamnesis_straining_date', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_hour', 'label' => 'anamnesis_straining_hour', 'rules' => 'trim'),
            array('field' => 'anamnesis_straining_amount', 'label' => 'anamnesis_straining_amount', 'rules' => 'trim'),
            array('field' => 'anamnesis_other_problem', 'label' => 'anamnesis_other_problem', 'rules' => 'trim'),
        );

        $this->save_obgyn_physical_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'type_nurse_id', 'label' => 'type_nurse_id', 'rules' => 'trim|required|max_length[2]'),
            array('field' => 'physical_leopold_one', 'label' => 'physical_leopold_one', 'rules' => 'trim'),
            array('field' => 'physical_leopold_two_type_id', 'label' => 'physical_leopold_two_type_id', 'rules' => 'trim'),
            array('field' => 'physical_leopold_three_type_id', 'label' => 'physical_leopold_three_type_id', 'rules' => 'trim'),
            array('field' => 'physical_leopold_three_other', 'label' => 'physical_leopold_three_other', 'rules' => 'trim'),
            array('field' => 'physical_leopold_four_type_id', 'label' => 'physical_leopold_four_type_id', 'rules' => 'trim'),
            array('field' => 'physical_fetal_heart_rate_freq', 'label' => 'physical_fetal_heart_rate_freq', 'rules' => 'trim'),
            array('field' => 'physical_fetal_heart_rate_rhythm_type_id', 'label' => 'physical_fetal_heart_rate_rhythm_type_id', 'rules' => 'trim'),
            array('field' => 'physical_vulva_type_id', 'label' => 'physical_vulva_type_id', 'rules' => 'trim'),
            array('field' => 'physical_vulva_other', 'label' => 'physical_vulva_other', 'rules' => 'trim'),
            array('field' => 'physical_internal_portio_type_id', 'label' => 'physical_internal_portio_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_opening_type_id', 'label' => 'physical_internal_opening_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_opening_cm', 'label' => 'physical_internal_opening_cm', 'rules' => 'trim'),
            array('field' => 'physical_internal_depletion_percent', 'label' => 'physical_internal_depletion_percent', 'rules' => 'trim'),
            array('field' => 'physical_internal_amniotic_type_id', 'label' => 'physical_internal_amniotic_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_amniotic_color', 'label' => 'physical_internal_amniotic_color', 'rules' => 'trim'),
            array('field' => 'physical_internal_presentation_type_id', 'label' => 'physical_internal_presentation_type_id', 'rules' => 'trim'),
            array('field' => 'physical_internal_presentation_other', 'label' => 'physical_internal_presentation_other', 'rules' => 'trim'),
            array('field' => 'physical_ctg_baseline', 'label' => 'physical_ctg_baseline', 'rules' => 'trim'),
            array('field' => 'physical_ctg_variability_type_id', 'label' => 'physical_ctg_variability_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_acceleration_type_id', 'label' => 'physical_ctg_acceleration_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_deceleration_type_id', 'label' => 'physical_ctg_deceleration_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_type_id', 'label' => 'physical_ctg_contractions_type_id', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_freq', 'label' => 'physical_ctg_contractions_freq', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_duration', 'label' => 'physical_ctg_contractions_duration', 'rules' => 'trim'),
            array('field' => 'physical_ctg_contractions_strength', 'label' => 'physical_ctg_contractions_strength', 'rules' => 'trim'),
            array('field' => 'physical_ctg_fetal_movement', 'label' => 'physical_ctg_fetal_movement', 'rules' => 'trim'),
            array('field' => 'physical_ctg_interpretation', 'label' => 'physical_ctg_interpretation', 'rules' => 'trim'),
        );
    }

    /**
     * Nurse API
     * --------------------
     * @param: registration_destination_id
     * @param: employee_nurse_id
     * @param: anamesa_type_id
     * @param: family_name
     * @param: family_relation_id
     * @param: main_complaint
     * @param: history_disease_now
     * @param: history_disease_past
     * @param: systolic_blood_pressure_arry
     * @param: general_state_type_id
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
     * @param: aids
     * @param: prothesa
     * @param: disabled
     * @param: adl_type_id
     * --------------------
     * @method : POST
     * @link: version/mr/nurse
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
            $this->form_validation->set_rules('employee_nurse_id', 'employee_nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_tension', 'history_tension', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('systolic_blood_pressure_arry', 'systolic_blood_pressure_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('general_state_type_id', 'general_state_type_id', 'trim|max_length[1]');
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
            $this->form_validation->set_rules('aids', 'aids', 'trim');
            $this->form_validation->set_rules('prothesa', 'prothesa', 'trim');
            $this->form_validation->set_rules('disabled', 'disabled', 'trim');
            $this->form_validation->set_rules('adl_type_id', 'adl_type_id', 'trim|max_length[1]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check registartion_destination & employee_nurse_id
                if($this->Registration_destination->read($dt_post['registration_destination_id'], false) != [] && $this->Employee->read($dt_post['employee_nurse_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_nurse = array(
                        'registration_destination_id'               => isset($dt_post['registration_destination_id']) ? $dt_post['registration_destination_id'] : '',
                        'employee_nurse_id'                         => isset($dt_post['employee_nurse_id']) ? $dt_post['employee_nurse_id'] : '',
                        'anamesa_type_id'                           => isset($dt_post['anamesa_type_id']) ? $dt_post['anamesa_type_id'] : '',
                        'family_name'                               => isset($dt_post['family_name']) ? $dt_post['family_name'] : '',
                        'family_relation_id'                        => isset($dt_post['family_relation_id']) ? $dt_post['family_relation_id'] : '',
                        'main_complaint'                            => isset($dt_post['main_complaint']) ? $dt_post['main_complaint'] : '',
                        'history_disease_now'                       => isset($dt_post['history_disease_now']) ? $dt_post['history_disease_now'] : '',
                        'history_tension'                           => isset($dt_post['history_tension']) ? $dt_post['history_tension'] : '',
                        'history_disease_past'                      => isset($dt_post['history_disease_past']) ? $dt_post['history_disease_past'] : '',
                        'systolic_blood_pressure_arry'              => isset($dt_post['systolic_blood_pressure_arry']) ? implode(',',$dt_post['systolic_blood_pressure_arry']) : '',
                        'general_state_type_id'                     => isset($dt_post['general_state_type_id']) ? $dt_post['general_state_type_id'] : '',
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
                        'aids'                                      => isset($dt_post['aids']) ? $dt_post['aids'] : '',
                        'prothesa'                                  => isset($dt_post['prothesa']) ? $dt_post['prothesa'] : '',
                        'disabled'                                  => isset($dt_post['disabled']) ? $dt_post['disabled'] : '',
                        'adl_type_id'                               => isset($dt_post['adl_type_id']) ? $dt_post['adl_type_id'] : '',
                        'date_added'                                => date('Y-m-d H:i:s')
                    );
                    $id = $this->$model->create($dt_nurse);
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(200, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong registration_destination or employee_nurse_id!');
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

    /**
     * Nurse API
     * --------------------
     * @param: registration_destination_id
     * @param: employee_nurse_id
     * @param: type_nurse_id
     * @param: anamesa_type_id
     * @param: family_name
     * @param: family_relation_id
     * @param: main_complaint
     * @param: history_disease_now
     * @param: history_disease_past
     * @param: systolic_blood_pressure_arry
     * @param: general_state_type_id
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
     * @param: aids
     * @param: prothesa
     * @param: disabled
     * @param: adl_type_id
     * --------------------
     * @method : POST
     * @link: version/mr/nurse
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
            $this->form_validation->set_rules('registration_destination_id', 'registration_destination_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('employee_nurse_id', 'employee_nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('type_nurse_id', 'type_nurse_id', 'trim|max_length[4]');
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_tension', 'history_tension', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('history_tension', 'history_tension', 'trim');
            $this->form_validation->set_rules('systolic_blood_pressure_arry', 'systolic_blood_pressure_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('general_state_type_id', 'general_state_type_id', 'trim|max_length[1]');
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
            $this->form_validation->set_rules('aids', 'aids', 'trim');
            $this->form_validation->set_rules('prothesa', 'prothesa', 'trim');
            $this->form_validation->set_rules('disabled', 'disabled', 'trim');
            $this->form_validation->set_rules('adl_type_id', 'adl_type_id', 'trim|max_length[1]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);

                // Check registartion_destination & employee_nurse_id
                if($this->Registration_destination->read($dt_post['registration_destination_id'], false) != [] && $this->Employee->read($dt_post['employee_nurse_id'], false) != []) {
                    $registration_destination_id = $dt_post['registration_destination_id'];
                    $employee_nurse_id = $dt_post['employee_nurse_id'];

                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_nurse = array(
                        'registration_destination_id'               => isset($dt_post['registration_destination_id']) ? $dt_post['registration_destination_id'] : '',
                        'employee_nurse_id'                         => isset($dt_post['employee_nurse_id']) ? $dt_post['employee_nurse_id'] : '',
                        'type_nurse_id'                             => isset($dt_post['type_nurse_id']) ? $dt_post['type_nurse_id'] : '',
                        'anamesa_type_id'                           => isset($dt_post['anamesa_type_id']) ? $dt_post['anamesa_type_id'] : '',
                        'family_name'                               => isset($dt_post['family_name']) ? $dt_post['family_name'] : '',
                        'family_relation_id'                        => isset($dt_post['family_relation_id']) ? $dt_post['family_relation_id'] : '',
                        'main_complaint'                            => isset($dt_post['main_complaint']) ? $dt_post['main_complaint'] : '',
                        'history_disease_now'                       => isset($dt_post['history_disease_now']) ? $dt_post['history_disease_now'] : '',
                        'history_disease_past'                      => isset($dt_post['history_disease_past']) ? $dt_post['history_disease_past'] : '',
                        'history_tension'                           => isset($dt_post['history_tension']) ? $dt_post['history_tension'] : '',
                        'systolic_blood_pressure_arry'              => isset($dt_post['systolic_blood_pressure_arry']) ? implode(',',$dt_post['systolic_blood_pressure_arry']) : '',
                        'general_state_type_id'                     => isset($dt_post['general_state_type_id']) ? $dt_post['general_state_type_id'] : '',
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
                        'sore_score'                                => isset($dt_post['sore_score']) ? $dt_post['sore_score'] : '',
                        'ews'                                       => isset($dt_post['ews']) ? $dt_post['ews'] : '',
                        'weight'                                    => isset($dt_post['weight']) ? $dt_post['weight'] : '',
                        'classification_weight_type_id'             => isset($dt_post['classification_weight_type_id']) ? $dt_post['classification_weight_type_id'] : '',
                        'height'                                    => isset($dt_post['height']) ? $dt_post['height'] : '',
                        'imt'                                       => isset($dt_post['imt']) ? $dt_post['imt'] : '',
                        'head_circumference'                        => isset($dt_post['head_circumference']) ? $dt_post['head_circumference'] : '',
                        'chest_circumference'                       => isset($dt_post['chest_circumference']) ? $dt_post['chest_circumference'] : '',
                        'arm_circumference'                         => isset($dt_post['arm_circumference']) ? $dt_post['arm_circumference'] : '',
                        'stomach_circumference'                     => isset($dt_post['stomach_circumference']) ? $dt_post['stomach_circumference'] : '',
                        'knee_circumference'                        => isset($dt_post['knee_circumference']) ? $dt_post['knee_circumference'] : '',
                        'aids'                                      => isset($dt_post['aids']) ? $dt_post['aids'] : '',
                        'prothesa'                                  => isset($dt_post['prothesa']) ? $dt_post['prothesa'] : '',
                        'disabled'                                  => isset($dt_post['disabled']) ? $dt_post['disabled'] : '',
                        'adl_type_id'                               => isset($dt_post['adl_type_id']) ? $dt_post['adl_type_id'] : '',
                        'date_added'                                => date('Y-m-d H:i:s')
                    );
                    $id = $this->$model->create($dt_nurse);

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
                        'employee_nurse_id'             => $employee_nurse_id,
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
                        'employee_nurse_id'      => $employee_nurse_id,
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
                            'employee_nurse_id'           => $employee_nurse_id,
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

                    if ($this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->code_booking && $this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->call_nurse_stamp == null) {
                        // $this->Registration_destination->replace(array('id'=>$dt_post['registration_destination_id'],'health_facility_item_id'=>$this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->health_facility_item_id,'registration_id'=>$this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->registration_id, 'call_doctor_stamp'=>date('Y-m-d H:i:s',time()), 'sts' => 4));
                        $this->Registration_destination->replace(array('id'=>$dt_post['registration_destination_id'],'health_facility_item_id'=>$this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->health_facility_item_id,'registration_id'=>$this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->registration_id, 'date_task_3'=>date('Y-m-d H:i:s'), 'call_nurse_stamp'=>date('Y-m-d H:i:s')));
                        $update_antrean_3 = array(
                            'kodebooking'       => $this->Registration_destination->read($dt_post['registration_destination_id'], false)[0]->code_booking,
                            'taskid'            => 3,
                            'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s"))
                        );
                        $this->load->library('Vclaim_antrol','vclaim_antrol');
                        $updatewaktu_antrean_3 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_3));
                        $json_update_antrean_3 = json_decode($updatewaktu_antrean_3, true);
                        $json_update_antrean_3 = json_decode($json_update_antrean_3);
                        // jika gagal update antrean bpjs
                        // var_dump($json_update_antrean_3);
                        if($json_update_antrean_3->metadata->code != 200) {
                            // rollback
                            // $this->db->trans_rollback();
                            // $this->REST_Return(422, $json_update_antrean_3->metadata->message, $json_update_antrean_3->metadata->message);
                            // return;
                        }
                    }
                    if ($dt_post['type_nurse_id'] == 1) {
                        // INIT_NURSE_ADULT
                        $dt_nurse_adult = array(
                            'nurse_id'                              => $id,
                            'been_hospitalized_type_id'             => isset($dt_post['been_hospitalized_type_id']) ? $dt_post['been_hospitalized_type_id'] : '',
                            'been_hospitalized_answer'              => isset($dt_post['been_hospitalized_answer']) ? $dt_post['been_hospitalized_answer'] : '',
                            'any_history_family_disease_type_id'    => isset($dt_post['any_history_family_disease_type_id']) ? $dt_post['any_history_family_disease_type_id'] : '',
                            'any_history_family_answer_arry'        => isset($dt_post['any_history_family_answer_arry']) ? implode(',',$dt_post['any_history_family_answer_arry']) : '',
                            'any_history_family_answer'             => isset($dt_post['any_history_family_answer']) ? $dt_post['any_history_family_answer'] : '',
                            'any_surgery'                           => isset($dt_post['any_surgery']) ? $dt_post['any_surgery'] : '',
                            'any_surgery_date'                      => isset($dt_post['any_surgery_date']) ? $dt_post['any_surgery_date'] : '',
                            'complication'                          => isset($dt_post['complication']) ? $dt_post['complication'] : '',
                            'history_allergy_type_id'               => isset($dt_post['history_allergy_type_id']) ? $dt_post['history_allergy_type_id'] : '',
                            'history_allergy'                       => isset($dt_post['history_allergy']) ? $dt_post['history_allergy'] : '',
                            'history_psychological_arry'            => isset($dt_post['history_psychological_arry']) ? implode(',',$dt_post['history_psychological_arry']) : '',
                            'body_mass_index'                       => isset($dt_post['body_mass_index']) ? $dt_post['body_mass_index'] : '',
                            'patient_food_intake'                   => isset($dt_post['patient_food_intake']) ? $dt_post['patient_food_intake'] : '',
                            'patient_certain_diets'                 => isset($dt_post['patient_certain_diets']) ? $dt_post['patient_certain_diets'] : '',
                            'lose_weight_months'                    => isset($dt_post['lose_weight_months']) ? $dt_post['lose_weight_months'] : '',
                            'lose_weight'                           => isset($dt_post['lose_weight']) ? $dt_post['lose_weight'] : '',
                            'patient_disease_condition'             => isset($dt_post['patient_disease_condition']) ? $dt_post['patient_disease_condition'] : '',
                            'nutrition_total_score'                 => isset($dt_post['nutrition_total_score']) ? $dt_post['nutrition_total_score'] : '',
                            'sore_complaints_type_id'               => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
                            'sore_complaints'                       => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
                            'sore_goes_away_arry'                   => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
                            'sore_goes_away'                        => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
                            'sore_method_type_id'                   => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
                            'sore_method_score'                     => isset($dt_post['sore_method_score']) ? $dt_post['sore_method_score'] : '',
                            'sore_method_arry'                      => isset($dt_post['sore_method_arry']) ? implode(',',$dt_post['sore_method_arry']) : '',
                            'tell_doctor_type_id'                   => isset($dt_post['tell_doctor_type_id']) ? $dt_post['tell_doctor_type_id'] : '',
                            'tell_doctor_date'                      => isset($dt_post['tell_doctor_date']) ? $dt_post['tell_doctor_date'] : '',
                            'face_score'                            => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
                            'legs_score'                            => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
                            'activity_score'                        => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
                            'cry_score'                             => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
                            'consolability_score'                   => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
                            'sore_total_score'                      => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
                            'duration'                              => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                            'location'                              => isset($dt_post['location']) ? $dt_post['location'] : '',
                            'response'                              => isset($dt_post['response']) ? $dt_post['response'] : '',
                            'frequency'                             => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
                            'fall_risk_method_type_id'              => isset($dt_post['fall_risk_method_type_id']) ? $dt_post['fall_risk_method_type_id'] : '',
                            'fall_risk_total_score'                 => isset($dt_post['fall_risk_total_score']) ? $dt_post['fall_risk_total_score'] : '',
                            'fall_risk_type_id'                     => isset($dt_post['fall_risk_type_id']) ? $dt_post['fall_risk_type_id'] : '',
                            'nursing_diagnoses_arry'                => isset($dt_post['nursing_diagnoses_arry']) ? implode(',',$dt_post['nursing_diagnoses_arry']) : '',
                            'plan_diit'                             => isset($dt_post['plan_diit']) ? $dt_post['plan_diit'] : '',
                            'plan_activity'                         => isset($dt_post['plan_activity']) ? $dt_post['plan_activity'] : '',
                            'plan_medicine'                         => isset($dt_post['plan_medicine']) ? $dt_post['plan_medicine'] : '',
                            'plan_recontrol'                        => isset($dt_post['plan_recontrol']) ? $dt_post['plan_recontrol'] : '',
                            'implementation_arry'                   => isset($dt_post['implementation_arry']) ? implode(',',$dt_post['implementation_arry']) : '',
                        );
                        $this->Nurse_adult->create($dt_nurse_adult);
                    } elseif ($dt_post['type_nurse_id'] == 2) {
                        // INIT_NURSE_CHILD
                        $dt_nurse_child = array(
                            'nurse_id'                              => $id,
                            'been_hospitalized_type_id'             => isset($dt_post['been_hospitalized_type_id']) ? $dt_post['been_hospitalized_type_id'] : '',
                            'been_hospitalized_answer'              => isset($dt_post['been_hospitalized_answer']) ? $dt_post['been_hospitalized_answer'] : '',
                            'any_history_family_disease_type_id'    => isset($dt_post['any_history_family_disease_type_id']) ? $dt_post['any_history_family_disease_type_id'] : '',
                            'any_history_family_answer_arry'        => isset($dt_post['any_history_family_answer_arry']) ? implode(',',$dt_post['any_history_family_answer_arry']) : '',
                            'any_history_family_answer'             => isset($dt_post['any_history_family_answer']) ? $dt_post['any_history_family_answer'] : '',
                            'any_surgery'                           => isset($dt_post['any_surgery']) ? $dt_post['any_surgery'] : '',
                            'any_surgery_date'                      => isset($dt_post['any_surgery_date']) ? $dt_post['any_surgery_date'] : '',
                            'complication'                          => isset($dt_post['complication']) ? $dt_post['complication'] : '',
                            'history_allergy_type_id'               => isset($dt_post['history_allergy_type_id']) ? $dt_post['history_allergy_type_id'] : '',
                            'history_allergy'                       => isset($dt_post['history_allergy']) ? $dt_post['history_allergy'] : '',
                            'history_psychological_arry'            => isset($dt_post['history_psychological_arry']) ? implode(',',$dt_post['history_psychological_arry']) : '',
                            'history_immunization_arry'             => isset($dt_post['history_immunization_arry']) ? implode(',',$dt_post['history_immunization_arry']) : '',
                            'history_immunization'                  => isset($dt_post['history_immunization']) ? $dt_post['history_immunization'] : '',
                            'body_mass_index'                       => isset($dt_post['body_mass_index']) ? $dt_post['body_mass_index'] : '',
                            'patient_food_intake'                   => isset($dt_post['patient_food_intake']) ? $dt_post['patient_food_intake'] : '',
                            'patient_certain_diets'                 => isset($dt_post['patient_certain_diets']) ? $dt_post['patient_certain_diets'] : '',
                            'lose_weight_months'                    => isset($dt_post['lose_weight_months']) ? $dt_post['lose_weight_months'] : '',
                            'lose_weight'                           => isset($dt_post['lose_weight']) ? $dt_post['lose_weight'] : '',
                            'patient_disease_condition'             => isset($dt_post['patient_disease_condition']) ? $dt_post['patient_disease_condition'] : '',
                            'nutrition_total_score'                 => isset($dt_post['nutrition_total_score']) ? $dt_post['nutrition_total_score'] : '',
                            'sore_complaints_type_id'               => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
                            'sore_complaints'                       => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
                            'sore_goes_away_arry'                   => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
                            'sore_goes_away'                        => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
                            'sore_method_type_id'                   => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
                            'sore_method_score'                     => isset($dt_post['sore_method_score']) ? $dt_post['sore_method_score'] : '',
                            'sore_method_arry'                      => isset($dt_post['sore_method_arry']) ? implode(',',$dt_post['sore_method_arry']) : '',
                            'tell_doctor_type_id'                   => isset($dt_post['tell_doctor_type_id']) ? $dt_post['tell_doctor_type_id'] : '',
                            'tell_doctor_date'                      => isset($dt_post['tell_doctor_date']) ? $dt_post['tell_doctor_date'] : '',
                            'face_score'                            => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
                            'legs_score'                            => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
                            'activity_score'                        => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
                            'cry_score'                             => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
                            'consolability_score'                   => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
                            'sore_total_score'                      => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
                            'duration'                              => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                            'location'                              => isset($dt_post['location']) ? $dt_post['location'] : '',
                            'response'                              => isset($dt_post['response']) ? $dt_post['response'] : '',
                            'frequency'                             => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
                            'fall_risk_method_type_id'              => isset($dt_post['fall_risk_method_type_id']) ? $dt_post['fall_risk_method_type_id'] : '',
                            'fall_risk_total_score'                 => isset($dt_post['fall_risk_total_score']) ? $dt_post['fall_risk_total_score'] : '',
                            'fall_risk_type_id'                     => isset($dt_post['fall_risk_type_id']) ? $dt_post['fall_risk_type_id'] : '',
                            'nursing_diagnoses_arry'                => isset($dt_post['nursing_diagnoses_arry']) ? implode(',',$dt_post['nursing_diagnoses_arry']) : '',
                            'plan_diit'                             => isset($dt_post['plan_diit']) ? $dt_post['plan_diit'] : '',
                            'plan_activity'                         => isset($dt_post['plan_activity']) ? $dt_post['plan_activity'] : '',
                            'plan_medicine'                         => isset($dt_post['plan_medicine']) ? $dt_post['plan_medicine'] : '',
                            'plan_recontrol'                        => isset($dt_post['plan_recontrol']) ? $dt_post['plan_recontrol'] : '',
                            'implementation_arry'                   => isset($dt_post['implementation_arry']) ? implode(',',$dt_post['implementation_arry']) : '',
                        );
                        $this->Nurse_child->create($dt_nurse_child);
                    } elseif ($dt_post['type_nurse_id'] == 3) {
                        // INIT_NURSE_OBGYN
                        $dt_nurse_obgyn = array(
                            'nurse_id'                              => $id,
                            'history_disease_type_id'               => isset($dt_post['history_disease_type_id']) ? $dt_post['history_disease_type_id'] : '',
                            'history_disease'                       => isset($dt_post['history_disease']) ? $dt_post['history_disease'] : '',
                            'been_hospitalized_type_id'             => isset($dt_post['been_hospitalized_type_id']) ? $dt_post['been_hospitalized_type_id'] : '',
                            'been_hospitalized_answer'              => isset($dt_post['been_hospitalized_answer']) ? $dt_post['been_hospitalized_answer'] : '',
                            'been_hospitalized_date'                => isset($dt_post['been_hospitalized_date']) ? $dt_post['been_hospitalized_date'] : '',
                            'been_hospitalized_place'               => isset($dt_post['been_hospitalized_place']) ? $dt_post['been_hospitalized_place'] : '',
                            'any_surgery_type_id'                   => isset($dt_post['any_surgery_type_id']) ? $dt_post['any_surgery_type_id'] : '',
                            'any_surgery_date'                      => isset($dt_post['any_surgery_date']) ? $dt_post['any_surgery_date'] : '',
                            'any_surgery_place'                     => isset($dt_post['any_surgery_place']) ? $dt_post['any_surgery_place'] : '',
                            'under_treatment_type_id'               => isset($dt_post['under_treatment_type_id']) ? $dt_post['under_treatment_type_id'] : '',
                            'under_treatment'                       => isset($dt_post['under_treatment']) ? $dt_post['under_treatment'] : '',
                            'any_history_family_disease_type_id'    => isset($dt_post['any_history_family_disease_type_id']) ? $dt_post['any_history_family_disease_type_id'] : '',
                            'any_history_family_answer_arry'        => isset($dt_post['any_history_family_answer_arry']) ? implode(',',$dt_post['any_history_family_answer_arry']) : '',
                            'any_history_family_answer'             => isset($dt_post['any_history_family_answer']) ? $dt_post['any_history_family_answer'] : '',
                            'dependence_substance_type_id'          => isset($dt_post['dependence_substance_type_id']) ? $dt_post['dependence_substance_type_id'] : '',
                            'dependence_substance_arry'             => isset($dt_post['dependence_substance_arry']) ? implode(',',$dt_post['dependence_substance_arry']) : '',
                            'dependence_substance'                  => isset($dt_post['dependence_substance']) ? $dt_post['dependence_substance'] : '',
                            'employment_history_type_id'            => isset($dt_post['employment_history_type_id']) ? $dt_post['employment_history_type_id'] : '',
                            'employment_history'                    => isset($dt_post['employment_history']) ? $dt_post['employment_history'] : '',
                            'allergy_history_type_id'               => isset($dt_post['allergy_history_type_id']) ? $dt_post['allergy_history_type_id'] : '',
                            'allergy_history_arry'                  => isset($dt_post['allergy_history_arry']) ? implode(',',$dt_post['allergy_history_arry']) : '',
                            'allergy_history_medicine'              => isset($dt_post['allergy_history_medicine']) ? $dt_post['allergy_history_medicine'] : '',
                            'allergy_history_food'                  => isset($dt_post['allergy_history_food']) ? $dt_post['allergy_history_food'] : '',
                            'allergy_history_other'                 => isset($dt_post['allergy_history_other']) ? $dt_post['allergy_history_other'] : '',
                            'marriage_history_type_id'              => isset($dt_post['marriage_history_type_id']) ? $dt_post['marriage_history_type_id'] : '',
                            'marriage_history'                      => isset($dt_post['marriage_history']) ? $dt_post['marriage_history'] : '',
                            'contraception_use_type_id'             => isset($dt_post['contraception_use_type_id']) ? $dt_post['contraception_use_type_id'] : '',
                            'contraception_use'                     => isset($dt_post['contraception_use']) ? $dt_post['contraception_use'] : '',
                            'contraception_length_of_use'           => isset($dt_post['contraception_length_of_use']) ? $dt_post['contraception_length_of_use'] : '',
                            'contraception_use_complaint'           => isset($dt_post['contraception_use_complaint']) ? $dt_post['contraception_use_complaint'] : '',
                            'age_first_marriage'                    => isset($dt_post['age_first_marriage']) ? $dt_post['age_first_marriage'] : '',
                            'marriage_one'                          => isset($dt_post['marriage_one']) ? $dt_post['marriage_one'] : '',
                            'marriage_two'                          => isset($dt_post['marriage_two']) ? $dt_post['marriage_two'] : '',
                            'marriage_three'                        => isset($dt_post['marriage_three']) ? $dt_post['marriage_three'] : '',
                            'period_menarche_age'                   => isset($dt_post['period_menarche_age']) ? $dt_post['period_menarche_age'] : '',
                            'period_cycle'                          => isset($dt_post['period_cycle']) ? $dt_post['period_cycle'] : '',
                            'period_volume'                         => isset($dt_post['period_volume']) ? $dt_post['period_volume'] : '',
                            'period_regular_type_id'                => isset($dt_post['period_regular_type_id']) ? $dt_post['period_regular_type_id'] : '',
                            'period_regular'                        => isset($dt_post['period_regular']) ? $dt_post['period_regular'] : '',
                            'period_complaint_type_id'              => isset($dt_post['period_complaint_type_id']) ? $dt_post['period_complaint_type_id'] : '',
                            'period_complaint'                      => isset($dt_post['period_complaint']) ? $dt_post['period_complaint'] : '',
                            'hpht'                                  => isset($dt_post['hpht']) ? $dt_post['hpht'] : '',
                            'estimated_delivery'                    => isset($dt_post['estimated_delivery']) ? $dt_post['estimated_delivery'] : '',
                            'gestational_age'                       => isset($dt_post['gestational_age']) ? $dt_post['gestational_age'] : '',
                            'gynecological_disease_type_id'         => isset($dt_post['gynecological_disease_type_id']) ? $dt_post['gynecological_disease_type_id'] : '',
                            'gynecological_disease_arry'            => isset($dt_post['gynecological_disease_arry']) ? implode(',',$dt_post['gynecological_disease_arry']) : '',
                            'gynecological_disease'                 => isset($dt_post['gynecological_disease']) ? $dt_post['gynecological_disease'] : '',
                            'speech_disorders_type_id'              => isset($dt_post['speech_disorders_type_id']) ? $dt_post['speech_disorders_type_id'] : '',
                            'speech_disorders'                      => isset($dt_post['speech_disorders']) ? $dt_post['speech_disorders'] : '',
                            'translator_type_id'                    => isset($dt_post['translator_type_id']) ? $dt_post['translator_type_id'] : '',
                            'translator'                            => isset($dt_post['translator']) ? $dt_post['translator'] : '',
                            'sign_language_type_id'                 => isset($dt_post['sign_language_type_id']) ? $dt_post['sign_language_type_id'] : '',
                            'risk_injury_a_type_id'                 => isset($dt_post['risk_injury_a_type_id']) ? $dt_post['risk_injury_a_type_id'] : '',
                            'risk_injury_b_type_id'                 => isset($dt_post['risk_injury_b_type_id']) ? $dt_post['risk_injury_b_type_id'] : '',
                            'functional_status_type_id'             => isset($dt_post['functional_status_type_id']) ? $dt_post['functional_status_type_id'] : '',
                            'functional_status'                     => isset($dt_post['functional_status']) ? $dt_post['functional_status'] : '',
                            'aids'                                  => isset($dt_post['obgyn_aids']) ? $dt_post['obgyn_aids'] : '',
                            'tell_doctor_risk_type_id'              => isset($dt_post['tell_doctor_risk_type_id']) ? $dt_post['tell_doctor_risk_type_id'] : '',
                            'tell_doctor_risk_date'                 => isset($dt_post['tell_doctor_risk_date']) ? $dt_post['tell_doctor_risk_date'] : '',
                            'risk_injury_result'                    => isset($dt_post['risk_injury_result']) ? $dt_post['risk_injury_result'] : '',
                            'sore_complaints_type_id'               => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
                            'sore_complaints'                       => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
                            'sore_goes_away_arry'                   => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
                            'sore_goes_away'                        => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
                            'sore_method_type_id'                   => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
                            'sore_method_score'                     => isset($dt_post['sore_method_score']) ? $dt_post['sore_method_score'] : '',
                            'sore_method_arry'                      => isset($dt_post['sore_method_arry']) ? implode(',',$dt_post['sore_method_arry']) : '',
                            'tell_doctor_sore_type_id'              => isset($dt_post['tell_doctor_sore_type_id']) ? $dt_post['tell_doctor_sore_type_id'] : '',
                            'tell_doctor_sore_date'                 => isset($dt_post['tell_doctor_sore_date']) ? $dt_post['tell_doctor_sore_date'] : '',
                            'face_score'                            => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
                            'legs_score'                            => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
                            'activity_score'                        => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
                            'cry_score'                             => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
                            'consolability_score'                   => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
                            'sore_total_score'                      => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
                            'duration'                              => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                            'location'                              => isset($dt_post['location']) ? $dt_post['location'] : '',
                            'response'                              => isset($dt_post['response']) ? $dt_post['response'] : '',
                            'frequency'                             => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
                            'nutrition_arry'                        => isset($dt_post['nutrition_arry']) ? implode(',',$dt_post['nutrition_arry']) : '',
                            'nutrition_one'                         => isset($dt_post['nutrition_one']) ? $dt_post['nutrition_one'] : '',
                            'nutrition_two'                         => isset($dt_post['nutrition_two']) ? $dt_post['nutrition_two'] : '',
                            'nutrition_three'                       => isset($dt_post['nutrition_three']) ? $dt_post['nutrition_three'] : '',
                            'nutrition_four'                        => isset($dt_post['nutrition_four']) ? $dt_post['nutrition_four'] : '',
                            'nutrition_date'                        => isset($dt_post['nutrition_date']) ? $dt_post['nutrition_date'] : '',
                            'nutrition_total_score'                 => isset($dt_post['nutrition_total_score']) ? $dt_post['nutrition_total_score'] : '',
                            'patient_special_diagnose_type_id'      => isset($dt_post['patient_special_diagnose_type_id']) ? $dt_post['patient_special_diagnose_type_id'] : '',
                            'patient_special_diagnose_arry'         => isset($dt_post['patient_special_diagnose_arry']) ? implode(',',$dt_post['patient_special_diagnose_arry']) : '',
                            'patient_special_diagnose'              => isset($dt_post['patient_special_diagnose']) ? $dt_post['patient_special_diagnose'] : '',
                            'midwifery_type_id'                     => isset($dt_post['midwifery_type_id']) ? $dt_post['midwifery_type_id'] : '',
                            'midwifery_problem'                     => isset($dt_post['midwifery_problem']) ? $dt_post['midwifery_problem'] : '',
                            'midwifery_goals'                       => isset($dt_post['midwifery_goals']) ? $dt_post['midwifery_goals'] : '',
                        );
                        $this->Nurse_obgyn->create($dt_nurse_obgyn);
                    } elseif ($dt_post['type_nurse_id'] == 4) {
                        
                    } elseif ($dt_post['type_nurse_id'] == 5) {
                        // INIT_NURSE_TRIAGE
                        $dt_nurse_triage = array(
                            'nurse_id'                          => $id,
                            'case_type_id'                      => isset($dt_post['case_type_id']) ? $dt_post['case_type_id'] : '',
                            'eye_response'                      => isset($dt_post['eye_response']) ? $dt_post['eye_response'] : '',
                            'verbal_response'                   => isset($dt_post['verbal_response']) ? $dt_post['verbal_response'] : '',
                            'motor_response'                    => isset($dt_post['motor_response']) ? $dt_post['motor_response'] : '',
                            'systolic_blood_pressure'           => isset($dt_post['triage_systolic_blood_pressure']) ? $dt_post['triage_systolic_blood_pressure'] : '',
                            'diastolic_blood_pressure'          => isset($dt_post['triage_diastolic_blood_pressure']) ? $dt_post['triage_diastolic_blood_pressure'] : '',
                            'capillary_refill_time'             => isset($dt_post['capillary_refill_time']) ? $dt_post['capillary_refill_time'] : '',
                            'breath_frequency'                  => isset($dt_post['triage_breath_frequency']) ? $dt_post['triage_breath_frequency'] : '',
                            'pulse_frequency'                   => isset($dt_post['triage_pulse_frequency']) ? $dt_post['triage_pulse_frequency'] : '',
                            'temperature'                       => isset($dt_post['triage_temperature']) ? $dt_post['triage_temperature'] : '',
                            'blood_oxygen'                      => isset($dt_post['blood_oxygen']) ? $dt_post['blood_oxygen'] : '',
                            'deformity_type_id'                 => isset($dt_post['deformity_type_id']) ? $dt_post['deformity_type_id'] : '',
                            'deformity'                         => isset($dt_post['deformity']) ? $dt_post['deformity'] : '',
                            'contusion_type_id'                 => isset($dt_post['contusion_type_id']) ? $dt_post['contusion_type_id'] : '',
                            'contusion'                         => isset($dt_post['contusion']) ? $dt_post['contusion'] : '',
                            'abrasion_type_id'                  => isset($dt_post['abrasion_type_id']) ? $dt_post['abrasion_type_id'] : '',
                            'abrasion'                          => isset($dt_post['abrasion']) ? $dt_post['abrasion'] : '',
                            'penetration_type_id'               => isset($dt_post['penetration_type_id']) ? $dt_post['penetration_type_id'] : '',
                            'penetration'                       => isset($dt_post['penetration']) ? $dt_post['penetration'] : '',
                            'laceration_type_id'                => isset($dt_post['laceration_type_id']) ? $dt_post['laceration_type_id'] : '',
                            'laceration'                        => isset($dt_post['laceration']) ? $dt_post['laceration'] : '',
                            'wound_depth_type_id'               => isset($dt_post['wound_depth_type_id']) ? $dt_post['wound_depth_type_id'] : '',
                            'wound_depth'                       => isset($dt_post['wound_depth']) ? $dt_post['wound_depth'] : '',
                            'anythingelse'                      => isset($dt_post['anythingelse']) ? $dt_post['anythingelse'] : '',
                            'dead_on_arrival_arry'              => isset($dt_post['dead_on_arrival_arry']) ? implode(',', $dt_post['dead_on_arrival_arry']) : '',
                            'doa_time'                          => isset($dt_post['doa_time']) ? $dt_post['doa_time'] : '',
                            'die_time'                          => isset($dt_post['die_time']) ? $dt_post['die_time'] : '',
                            'airway_type_id'                    => isset($dt_post['airway_type_id']) ? $dt_post['airway_type_id'] : '',
                            'respiratory_arry'                  => isset($dt_post['respiratory_arry']) ? implode(',', $dt_post['respiratory_arry']) : '',
                            'circulation_arry'                  => isset($dt_post['circulation_arry']) ? implode(',', $dt_post['circulation_arry']) : '',
                            'consciousness_arry'                => isset($dt_post['consciousness_arry']) ? implode(',', $dt_post['consciousness_arry']) : '',
                            'triage_result'                     => isset($dt_post['triage_result']) ? $dt_post['triage_result'] : '',
                            'isolation_criteria_arry'           => isset($dt_post['isolation_criteria_arry']) ? implode(',', $dt_post['isolation_criteria_arry']) : '',
                            'isolation_criteria'                => isset($dt_post['isolation_criteria']) ? $dt_post['isolation_criteria'] : '',
                            'date_added'                        => date('Y-m-d H:i:s',time()),
                        );
                        $this->Nurse_triage->create($dt_nurse_triage);
                    } elseif ($dt_post['type_nurse_id'] == 6) {
                        // INIT_CHILD_INPATIENT
                        $dt_nurse_child_inpatient = array(
                            'nurse_id'                          => $id,
                            'psychological_status_arry'         => isset($dt_post['psychological_status_arry']) ? implode(',',$dt_post['psychological_status_arry']) : '',
                            'psychological_status'              => isset($dt_post['psychological_status']) ? $dt_post['psychological_status'] : '',
                            'mental_status_arry'                => isset($dt_post['mental_status_arry']) ? implode(',',$dt_post['mental_status_arry']) : '',
                            'mental_status_problems'            => isset($dt_post['mental_status_problems']) ? $dt_post['mental_status_problems'] : '',
                            'mental_status_violence'            => isset($dt_post['mental_status_violence']) ? $dt_post['mental_status_violence'] : '',
                            'spiritual_status_a'                => isset($dt_post['spiritual_status_a']) ? $dt_post['spiritual_status_a'] : '',
                            'spiritual_status_b'                => isset($dt_post['spiritual_status_b']) ? $dt_post['spiritual_status_b'] : '',
                            'dietary_habit'                     => isset($dt_post['dietary_habit']) ? $dt_post['dietary_habit'] : '',
                            'nutrition_arry'                    => isset($dt_post['nutrition_arry']) ? implode(',',$dt_post['nutrition_arry']) : '',
                            'nutrition'                         => isset($dt_post['nutrition']) ? $dt_post['nutrition'] : '',
                            'elimination_defecate'              => isset($dt_post['elimination_defecate']) ? $dt_post['elimination_defecate'] : '',
                            'elimination_urination'             => isset($dt_post['elimination_urination']) ? $dt_post['elimination_urination'] : '',
                            'elimination_consistency_type_id'   => isset($dt_post['elimination_consistency_type_id']) ? $dt_post['elimination_consistency_type_id'] : '',
                            'elimination_color_type_id'         => isset($dt_post['elimination_color_type_id']) ? $dt_post['elimination_color_type_id'] : '',
                            'elimination_color'                 => isset($dt_post['elimination_color']) ? $dt_post['elimination_color'] : '',
                            'immunization_arry'                 => isset($dt_post['immunization_arry']) ? implode(',',$dt_post['immunization_arry']) : '',
                            'immunization'                      => isset($dt_post['immunization']) ? $dt_post['immunization'] : '',
                            'development_history_arry'          => isset($dt_post['development_history_arry']) ? implode(',',$dt_post['development_history_arry']) : '',
                            'development_smile'                 => isset($dt_post['development_smile']) ? $dt_post['development_smile'] : '',
                            'development_stand'                 => isset($dt_post['development_stand']) ? $dt_post['development_stand'] : '',
                            'development_prone'                 => isset($dt_post['development_prone']) ? $dt_post['development_prone'] : '',
                            'development_walk'                  => isset($dt_post['development_walk']) ? $dt_post['development_walk'] : '',
                            'development_sit'                   => isset($dt_post['development_sit']) ? $dt_post['development_sit'] : '',
                            'development_speak'                 => isset($dt_post['development_speak']) ? $dt_post['development_speak'] : '',
                            'development_crawl'                 => isset($dt_post['development_crawl']) ? $dt_post['development_crawl'] : '',
                            'development_school'                => isset($dt_post['development_school']) ? $dt_post['development_school'] : '',
                            'nutritional_screening_a'           => isset($dt_post['nutritional_screening_a']) ? $dt_post['nutritional_screening_a'] : '',
                            'nutritional_screening_b'           => isset($dt_post['nutritional_screening_b']) ? $dt_post['nutritional_screening_b'] : '',
                            'nutritional_screening_c'           => isset($dt_post['nutritional_screening_c']) ? $dt_post['nutritional_screening_c'] : '',
                            'nutritional_screening_total_score' => isset($dt_post['nutritional_screening_total_score']) ? $dt_post['nutritional_screening_total_score'] : '',
                            'sore_complaints_type_id'           => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
                            'sore_complaints'                   => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
                            'sore_goes_away_arry'               => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
                            'sore_goes_away'                    => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
                            'sore_method_type_id'               => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
                            'sore_method_score'                 => isset($dt_post['sore_method_score']) ? $dt_post['sore_method_score'] : '',
                            'sore_method_arry'                  => isset($dt_post['sore_method_arry']) ? implode(',',$dt_post['sore_method_arry']) : '',
                            'tell_doctor_type_id'               => isset($dt_post['tell_doctor_type_id']) ? $dt_post['tell_doctor_type_id'] : '',
                            'tell_doctor_date'                  => isset($dt_post['tell_doctor_date']) ? $dt_post['tell_doctor_date'] : '',
                            'face_score'                        => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
                            'legs_score'                        => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
                            'activity_score'                    => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
                            'cry_score'                         => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
                            'consolability_score'               => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
                            'sore_total_score'                  => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
                            'duration'                          => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                            'location'                          => isset($dt_post['location']) ? $dt_post['location'] : '',
                            'response'                          => isset($dt_post['response']) ? $dt_post['response'] : '',
                            'frequency'                         => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
                            'sore_quality_arry'                 => isset($dt_post['sore_quality_arry']) ? implode(',',$dt_post['sore_quality_arry']) : '',
                            'sore_frequency'                    => isset($dt_post['sore_frequency']) ? $dt_post['sore_frequency'] : '',
                            'sore_occurs_arry'                  => isset($dt_post['sore_occurs_arry']) ? implode(',',$dt_post['sore_occurs_arry']) : '',
                            'sore_occurs'                       => isset($dt_post['sore_occurs']) ? $dt_post['sore_occurs'] : '',
                            'humpty_dumpty_scale_type_id'       => isset($dt_post['humpty_dumpty_scale_type_id']) ? $dt_post['humpty_dumpty_scale_type_id'] : '',
                            'sensoric_vision_type_id'           => isset($dt_post['sensoric_vision_type_id']) ? $dt_post['sensoric_vision_type_id'] : '',
                            'sensoric_olfactory_type_id'        => isset($dt_post['sensoric_olfactory_type_id']) ? $dt_post['sensoric_olfactory_type_id'] : '',
                            'sensoric_hearing_type_id'          => isset($dt_post['sensoric_hearing_type_id']) ? $dt_post['sensoric_hearing_type_id'] : '',
                            'cognitive_type_id'                 => isset($dt_post['cognitive_type_id']) ? $dt_post['cognitive_type_id'] : '',
                            'motor_daily_type_id'               => isset($dt_post['motor_daily_type_id']) ? $dt_post['motor_daily_type_id'] : '',
                            'motor_walking_type_id'             => isset($dt_post['motor_walking_type_id']) ? $dt_post['motor_walking_type_id'] : '',
                            'educational_needs_arry'            => isset($dt_post['educational_needs_arry']) ? implode(',',$dt_post['educational_needs_arry']) : '',
                            'educational_needs'                 => isset($dt_post['educational_needs']) ? $dt_post['educational_needs'] : '',
                            'educational_obstacle_type_id'      => isset($dt_post['educational_obstacle_type_id']) ? $dt_post['educational_obstacle_type_id'] : '',
                            'educational_obstacle_arry'         => isset($dt_post['educational_obstacle_arry']) ? implode(',',$dt_post['educational_obstacle_arry']) : '',
                            'educational_obstacle'              => isset($dt_post['educational_obstacle']) ? $dt_post['educational_obstacle'] : '',
                            'translator_type_id'                => isset($dt_post['translator_type_id']) ? $dt_post['translator_type_id'] : '',
                            'translator'                        => isset($dt_post['translator']) ? $dt_post['translator'] : '',
                            'criteria_patients_at_risk_arry'    => isset($dt_post['criteria_patients_at_risk_arry']) ? implode(',',$dt_post['criteria_patients_at_risk_arry']) : '',
                            'needs_home_care_type_id'           => isset($dt_post['needs_home_care_type_id']) ? $dt_post['needs_home_care_type_id'] : '',
                            'needs_home_care'                   => isset($dt_post['needs_home_care']) ? $dt_post['needs_home_care'] : '',
                            'needs_implant_type_id'             => isset($dt_post['needs_implant_type_id']) ? $dt_post['needs_implant_type_id'] : '',
                            'needs_implant'                     => isset($dt_post['needs_implant']) ? $dt_post['needs_implant'] : '',
                            'needs_aids_type_id'                => isset($dt_post['needs_aids_type_id']) ? $dt_post['needs_aids_type_id'] : '',
                            'needs_aids'                        => isset($dt_post['needs_aids']) ? $dt_post['needs_aids'] : '',
                            'needs_install_equip_type_id'       => isset($dt_post['needs_install_equip_type_id']) ? $dt_post['needs_install_equip_type_id'] : '',
                            'needs_install_equip'               => isset($dt_post['needs_install_equip']) ? $dt_post['needs_install_equip'] : '',
                            'needs_community_type_id'           => isset($dt_post['needs_community_type_id']) ? $dt_post['needs_community_type_id'] : '',
                            'needs_community'                   => isset($dt_post['needs_community']) ? $dt_post['needs_community'] : '',
                            'needs_therapist_type_id'           => isset($dt_post['needs_therapist_type_id']) ? $dt_post['needs_therapist_type_id'] : '',
                            'needs_therapist'                   => isset($dt_post['needs_therapist']) ? $dt_post['needs_therapist'] : '',
                            'needs_other_type_id'               => isset($dt_post['needs_other_type_id']) ? $dt_post['needs_other_type_id'] : '',
                            'needs_other'                       => isset($dt_post['needs_other']) ? $dt_post['needs_other'] : '',
                            'nursing_problems_arry'             => isset($dt_post['nursing_problems_arry']) ? implode(',',$dt_post['nursing_problems_arry']) : '',
                            'nursing_problems_a'                => isset($dt_post['nursing_problems_a']) ? $dt_post['nursing_problems_a'] : '',
                            'nursing_problems_b'                => isset($dt_post['nursing_problems_b']) ? $dt_post['nursing_problems_b'] : '',
                            'nursing_problems_c'                => isset($dt_post['nursing_problems_c']) ? $dt_post['nursing_problems_c'] : '',
                        );
                        $this->Nurse_child_inpatient->create($dt_nurse_child_inpatient);
                    } elseif ($dt_post['type_nurse_id'] == 7) {
                        // INIT_ADULT_INPATIENT
                        // $nurse_adult_inpatient = array(
                        //     'nurse_id'                          => $id,
                        //     'psychological_status_arry'         => isset($dt_post['psychological_status_arry']) ? implode(',',$dt_post['psychological_status_arry']) : '',
                        //     'psychological_status'              => isset($dt_post['psychological_status']) ? $dt_post['psychological_status'] : '',
                        //     'mental_status_arry'                => isset($dt_post['mental_status_arry']) ? implode(',',$dt_post['mental_status_arry']) : '',
                        //     'mental_status_problems'            => isset($dt_post['mental_status_problems']) ? $dt_post['mental_status_problems'] : '',
                        //     'mental_status_violence'            => isset($dt_post['mental_status_violence']) ? $dt_post['mental_status_violence'] : '',
                        //     'spiritual_status_a'                => isset($dt_post['spiritual_status_a']) ? $dt_post['spiritual_status_a'] : '',
                        //     'spiritual_status_b'                => isset($dt_post['spiritual_status_b']) ? $dt_post['spiritual_status_b'] : '',
                        //     'elimination_defecate'              => isset($dt_post['elimination_defecate']) ? $dt_post['elimination_defecate'] : '',
                        //     'elimination_urination'             => isset($dt_post['elimination_urination']) ? $dt_post['elimination_urination'] : '',
                        //     'elimination_consistency_type_id'   => isset($dt_post['elimination_consistency_type_id']) ? $dt_post['elimination_consistency_type_id'] : '',
                        //     'elimination_color_type_id'         => isset($dt_post['elimination_color_type_id']) ? $dt_post['elimination_color_type_id'] : '',
                        //     'elimination_color'                 => isset($dt_post['elimination_color']) ? $dt_post['elimination_color'] : '',
                        //     'sore_complaints_type_id'           => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
                        //     'sore_complaints'                   => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
                        //     'sore_goes_away_arry'               => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
                        //     'sore_goes_away'                    => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
                        //     'sore_method_type_id'               => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
                        //     'sore_method_score'                 => isset($dt_post['sore_method_score']) ? $dt_post['sore_method_score'] : '',
                        //     'sore_method_arry'                  => isset($dt_post['sore_method_arry']) ? implode(',',$dt_post['sore_method_arry']) : '',
                        //     'tell_doctor_type_id'               => isset($dt_post['tell_doctor_type_id']) ? $dt_post['tell_doctor_type_id'] : '',
                        //     'tell_doctor_date'                  => isset($dt_post['tell_doctor_date']) ? $dt_post['tell_doctor_date'] : '',
                        //     'face_score'                        => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
                        //     'legs_score'                        => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
                        //     'activity_score'                    => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
                        //     'cry_score'                         => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
                        //     'consolability_score'               => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
                        //     'sore_total_score'                  => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
                        //     'duration'                          => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                        //     'location'                          => isset($dt_post['location']) ? $dt_post['location'] : '',
                        //     'response'                          => isset($dt_post['response']) ? $dt_post['response'] : '',
                        //     'frequency'                         => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
                        //     'sore_quality_arry'                 => isset($dt_post['sore_quality_arry']) ? implode(',',$dt_post['sore_quality_arry']) : '',
                        //     'sore_frequency'                    => isset($dt_post['sore_frequency']) ? $dt_post['sore_frequency'] : '',
                        //     'sore_occurs_arry'                  => isset($dt_post['sore_occurs_arry']) ? implode(',',$dt_post['sore_occurs_arry']) : '',
                        //     'sore_occurs'                       => isset($dt_post['sore_occurs']) ? $dt_post['sore_occurs'] : '',
                        //     'fall_risk_type_id'                 => isset($dt_post['fall_risk_type_id']) ? $dt_post['fall_risk_type_id'] : '',
                        //     'sensoric_vision_type_id'           => isset($dt_post['sensoric_vision_type_id']) ? $dt_post['sensoric_vision_type_id'] : '',
                        //     'sensoric_olfactory_type_id'        => isset($dt_post['sensoric_olfactory_type_id']) ? $dt_post['sensoric_olfactory_type_id'] : '',
                        //     'sensoric_hearing_type_id'          => isset($dt_post['sensoric_hearing_type_id']) ? $dt_post['sensoric_hearing_type_id'] : '',
                        //     'cognitive_type_id'                 => isset($dt_post['cognitive_type_id']) ? $dt_post['cognitive_type_id'] : '',
                        //     'motor_daily_type_id'               => isset($dt_post['motor_daily_type_id']) ? $dt_post['motor_daily_type_id'] : '',
                        //     'motor_walking_type_id'             => isset($dt_post['motor_walking_type_id']) ? $dt_post['motor_walking_type_id'] : '',
                        //     'nutritional_screening_a'           => isset($dt_post['nutritional_screening_a']) ? $dt_post['nutritional_screening_a'] : '',
                        //     'nutritional_screening_b'           => isset($dt_post['nutritional_screening_b']) ? $dt_post['nutritional_screening_b'] : '',
                        //     'nutritional_screening_c'           => isset($dt_post['nutritional_screening_c']) ? $dt_post['nutritional_screening_c'] : '',
                        //     'nutritional_screening_total_score' => isset($dt_post['nutritional_screening_total_score']) ? $dt_post['nutritional_screening_total_score'] : '',
                        //     'criteria_patients_at_risk_arry'    => isset($dt_post['criteria_patients_at_risk_arry']) ? implode(',',$dt_post['criteria_patients_at_risk_arry']) : '',
                        //     'educational_needs_arry'            => isset($dt_post['educational_needs_arry']) ? implode(',',$dt_post['educational_needs_arry']) : '',
                        //     'educational_needs'                 => isset($dt_post['educational_needs']) ? $dt_post['educational_needs'] : '',
                        //     'educational_obstacle_type_id'      => isset($dt_post['educational_obstacle_type_id']) ? $dt_post['educational_obstacle_type_id'] : '',
                        //     'educational_obstacle_arry'         => isset($dt_post['educational_obstacle_arry']) ? implode(',',$dt_post['educational_obstacle_arry']) : '',
                        //     'educational_obstacle'              => isset($dt_post['educational_obstacle']) ? $dt_post['educational_obstacle'] : '',
                        //     'translator_type_id'                => isset($dt_post['translator_type_id']) ? $dt_post['translator_type_id'] : '',
                        //     'translator'                        => isset($dt_post['translator']) ? $dt_post['translator'] : '',
                        //     'needs_home_care_type_id'           => isset($dt_post['needs_home_care_type_id']) ? $dt_post['needs_home_care_type_id'] : '',
                        //     'needs_home_care'                   => isset($dt_post['needs_home_care']) ? $dt_post['needs_home_care'] : '',
                        //     'needs_implant_type_id'             => isset($dt_post['needs_implant_type_id']) ? $dt_post['needs_implant_type_id'] : '',
                        //     'needs_implant'                     => isset($dt_post['needs_implant']) ? $dt_post['needs_implant'] : '',
                        //     'needs_aids_type_id'                => isset($dt_post['needs_aids_type_id']) ? $dt_post['needs_aids_type_id'] : '',
                        //     'needs_aids'                        => isset($dt_post['needs_aids']) ? $dt_post['needs_aids'] : '',
                        //     'needs_install_equip_type_id'       => isset($dt_post['needs_install_equip_type_id']) ? $dt_post['needs_install_equip_type_id'] : '',
                        //     'needs_install_equip'               => isset($dt_post['needs_install_equip']) ? $dt_post['needs_install_equip'] : '',
                        //     'needs_community_type_id'           => isset($dt_post['needs_community_type_id']) ? $dt_post['needs_community_type_id'] : '',
                        //     'needs_community'                   => isset($dt_post['needs_community']) ? $dt_post['needs_community'] : '',
                        //     'needs_therapist_type_id'           => isset($dt_post['needs_therapist_type_id']) ? $dt_post['needs_therapist_type_id'] : '',
                        //     'needs_therapist'                   => isset($dt_post['needs_therapist']) ? $dt_post['needs_therapist'] : '',
                        //     'needs_other_type_id'               => isset($dt_post['needs_other_type_id']) ? $dt_post['needs_other_type_id'] : '',
                        //     'needs_other'                       => isset($dt_post['needs_other']) ? $dt_post['needs_other'] : '',
                        //     'nursing_problems_arry'             => isset($dt_post['nursing_problems_arry']) ? implode(',',$dt_post['nursing_problems_arry']) : '',
                        //     'nursing_problems_a'                => isset($dt_post['nursing_problems_a']) ? $dt_post['nursing_problems_a'] : '',
                        //     'nursing_problems_b'                => isset($dt_post['nursing_problems_b']) ? $dt_post['nursing_problems_b'] : '',
                        //     'nursing_problems_c'                => isset($dt_post['nursing_problems_c']) ? $dt_post['nursing_problems_c'] : '',
                        // );
                        // $this->Nurse_adult_inpatient->create($nurse_adult_inpatient);
                    } elseif ($dt_post['type_nurse_id'] == 8) {
                        // INIT_ELDERLY_INPATIENT
                        $nurse_elderly_inpatient = array(
                            'nurse_id'                              => $id,
                            'illness_history_type_id'               => isset($dt_post['illness_history_type_id']) ? $dt_post['illness_history_type_id'] : '',
                            'illness_history'                       => isset($dt_post['illness_history']) ? $dt_post['illness_history'] : '',
                            'allergy_history_type_id'               => isset($dt_post['allergy_history_type_id']) ? $dt_post['allergy_history_type_id'] : '',
                            'allergy_history'                       => isset($dt_post['allergy_history']) ? $dt_post['allergy_history'] : '',
                            'any_surgery'                           => isset($dt_post['any_surgery']) ? $dt_post['any_surgery'] : '',
                            'any_surgery_date'                      => isset($dt_post['any_surgery_date']) ? $dt_post['any_surgery_date'] : '',
                            'complication'                          => isset($dt_post['complication']) ? $dt_post['complication'] : '',
                            'been_hospitalized_type_id'             => isset($dt_post['been_hospitalized_type_id']) ? $dt_post['been_hospitalized_type_id'] : '',
                            'been_hospitalized_answer'              => isset($dt_post['been_hospitalized_answer']) ? $dt_post['been_hospitalized_answer'] : '',
                            'any_history_family_disease_type_id'    => isset($dt_post['any_history_family_disease_type_id']) ? $dt_post['any_history_family_disease_type_id'] : '',
                            'any_history_family_answer_arry'        => isset($dt_post['any_history_family_answer_arry']) ? implode(',',$dt_post['any_history_family_answer_arry']) : '',
                            'any_history_family_answer'             => isset($dt_post['any_history_family_answer']) ? $dt_post['any_history_family_answer'] : '',
                            'dependence_type_id'                    => isset($dt_post['dependence_type_id']) ? $dt_post['dependence_type_id'] : '',
                            'dependence'                            => isset($dt_post['dependence']) ? $dt_post['dependence'] : '',
                            'sore_complaints_type_id'               => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
                            'sore_complaints'                       => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
                            'sore_goes_away_arry'                   => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
                            'sore_goes_away'                        => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
                            'sore_method_type_id'                   => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
                            'face_score'                            => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
                            'legs_score'                            => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
                            'activity_score'                        => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
                            'cry_score'                             => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
                            'consolability_score'                   => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
                            'sore_total_score'                      => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
                            'duration'                              => isset($dt_post['duration']) ? $dt_post['duration'] : '',
                            'location'                              => isset($dt_post['location']) ? $dt_post['location'] : '',
                            'response'                              => isset($dt_post['response']) ? $dt_post['response'] : '',
                            'frequency'                             => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
                            'sore_quality_arry'                     => isset($dt_post['sore_quality_arry']) ? implode(',',$dt_post['sore_quality_arry']) : '',
                            'sore_frequency'                        => isset($dt_post['sore_frequency']) ? $dt_post['sore_frequency'] : '',
                            'sore_occurs_arry'                      => isset($dt_post['sore_occurs_arry']) ? implode(',',$dt_post['sore_occurs_arry']) : '',
                            'sore_occurs'                           => isset($dt_post['sore_occurs']) ? $dt_post['sore_occurs'] : '',
                            'sore_affects_arry'                     => isset($dt_post['sore_affects_arry']) ? implode(',',$dt_post['sore_affects_arry']) : '',
                            'sore_affects'                          => isset($dt_post['sore_affects']) ? $dt_post['sore_affects'] : '',
                            'activity_shower'                       => isset($dt_post['activity_shower']) ? $dt_post['activity_shower'] : '',
                            'activity_clothes'                      => isset($dt_post['activity_clothes']) ? $dt_post['activity_clothes'] : '',
                            'activity_mobilization'                 => isset($dt_post['activity_mobilization']) ? $dt_post['activity_mobilization'] : '',
                            'activity_defecate'                     => isset($dt_post['activity_defecate']) ? $dt_post['activity_defecate'] : '',
                            'activity_urination'                    => isset($dt_post['activity_urination']) ? $dt_post['activity_urination'] : '',
                            'activity_eat_drink'                    => isset($dt_post['activity_eat_drink']) ? $dt_post['activity_eat_drink'] : '',
                            'braden_scale_activity'                 => isset($dt_post['braden_scale_activity']) ? $dt_post['braden_scale_activity'] : '',
                            'braden_scale_mobilization'             => isset($dt_post['braden_scale_mobilization']) ? $dt_post['braden_scale_mobilization'] : '',
                            'braden_scale_nutrition'                => isset($dt_post['braden_scale_nutrition']) ? $dt_post['braden_scale_nutrition'] : '',
                            'braden_scale_friction'                 => isset($dt_post['braden_scale_friction']) ? $dt_post['braden_scale_friction'] : '',
                            'braden_scale_perception'               => isset($dt_post['braden_scale_perception']) ? $dt_post['braden_scale_perception'] : '',
                            'braden_scale_humidity'                 => isset($dt_post['braden_scale_humidity']) ? $dt_post['braden_scale_humidity'] : '',
                            'braden_scale_total_score'              => isset($dt_post['braden_scale_total_score']) ? $dt_post['braden_scale_total_score'] : '',
                            'nutrition_total_score'                 => isset($dt_post['nutrition_total_score']) ? $dt_post['nutrition_total_score'] : '',
                            'sociocultural_arry'                    => isset($dt_post['sociocultural_arry']) ? implode(',',$dt_post['sociocultural_arry']) : '',
                            'sociocultural_days'                    => isset($dt_post['sociocultural_days']) ? $dt_post['sociocultural_days'] : '',
                            'sociocultural_other'                   => isset($dt_post['sociocultural_other']) ? $dt_post['sociocultural_other'] : '',
                            'language_arry'                         => isset($dt_post['language_arry']) ? implode(',',$dt_post['language_arry']) : '',
                            'local_language'                        => isset($dt_post['local_language']) ? $dt_post['local_language'] : '',
                            'other_language'                        => isset($dt_post['other_language']) ? $dt_post['other_language'] : '',
                            'expression_emotion_arry'               => isset($dt_post['expression_emotion_arry']) ? implode(',',$dt_post['expression_emotion_arry']) : '',
                            'relationship_family_arry'              => isset($dt_post['relationship_family_arry']) ? implode(',',$dt_post['relationship_family_arry']) : '',
                            'relationship_family'                   => isset($dt_post['relationship_family']) ? $dt_post['relationship_family'] : '',
                            'thought_process_arry'                  => isset($dt_post['thought_process_arry']) ? implode(',',$dt_post['thought_process_arry']) : '',
                            'family_understand_arry'                => isset($dt_post['family_understand_arry']) ? implode(',',$dt_post['family_understand_arry']) : '',
                            'nursing_problems_arry'                 => isset($dt_post['nursing_problems_arry']) ? implode(',',$dt_post['nursing_problems_arry']) : '',
                            'discharge_criteria_arry'               => isset($dt_post['discharge_criteria_arry']) ? implode(',',$dt_post['discharge_criteria_arry']) : '',
                            'home_planning_arry'                    => isset($dt_post['home_planning_arry']) ? implode(',',$dt_post['home_planning_arry']) : '',
                            'home_planning'                         => isset($dt_post['home_planning']) ? $dt_post['home_planning'] : '',
                        );
                        $this->Nurse_elderly_inpatient->create($nurse_elderly_inpatient);
                    } elseif ($dt_post['type_nurse_id'] == 9) {
                        // INIT_NURSE_PHYSIOTHERAPY
                        $dt_nurse_physiotherapy = array(
                            'nurse_id'                      => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
                            'medical_diagnosis'             => isset($dt_post['medical_diagnosis']) ? $dt_post['medical_diagnosis'] : '',
                            'support_diagnosis'             => isset($dt_post['support_diagnosis']) ? $dt_post['support_diagnosis'] : '',
                            'general_rr'                    => isset($dt_post['general_rr']) ? $dt_post['general_rr'] : '',
                            'general_inspection'            => isset($dt_post['general_inspection']) ? $dt_post['general_inspection'] : '',
                            'general_palpation'             => isset($dt_post['general_palpation']) ? $dt_post['general_palpation'] : '',
                            'general_active'                => isset($dt_post['general_active']) ? $dt_post['general_active'] : '',
                            'general_passive'               => isset($dt_post['general_passive']) ? $dt_post['general_passive'] : '',
                            'general_isometric'             => isset($dt_post['general_isometric']) ? $dt_post['general_isometric'] : '',
                            'general_auscultation'          => isset($dt_post['general_auscultation']) ? $dt_post['general_auscultation'] : '',
                            'general_percussion'            => isset($dt_post['general_percussion']) ? $dt_post['general_percussion'] : '',
                            'special_muscle_test'           => isset($dt_post['special_muscle_test']) ? $dt_post['special_muscle_test'] : '',
                            'special_rom_test'              => isset($dt_post['special_rom_test']) ? $dt_post['special_rom_test'] : '',
                            'special_anthropometry_test'    => isset($dt_post['special_anthropometry_test']) ? $dt_post['special_anthropometry_test'] : '',
                            'special_sore_scale'            => isset($dt_post['special_sore_scale']) ? $dt_post['special_sore_scale'] : '',
                            'special_specific_test'         => isset($dt_post['special_specific_test']) ? $dt_post['special_specific_test'] : '',
                            'functional_abilities'          => isset($dt_post['functional_abilities']) ? $dt_post['functional_abilities'] : '',
                            'physiotherapy_diagnosis'       => isset($dt_post['physiotherapy_diagnosis']) ? $dt_post['physiotherapy_diagnosis'] : '',
                            'physiotherapy_program'         => isset($dt_post['physiotherapy_program']) ? $dt_post['physiotherapy_program'] : '',
                            'evaluation_follow_up'          => isset($dt_post['evaluation_follow_up']) ? $dt_post['evaluation_follow_up'] : '',
                        );
                        $this->Nurse_physiotherapy->create($dt_nurse_physiotherapy);
                    } elseif ($dt_post['type_nurse_id'] == 10) {
                        // INIT_NURSE_SOAP
                        $dt_nurse_soap = array(
                            'nurse_id'      => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
                            's'             => isset($dt_post['s']) ? $dt_post['s'] : '',
                            'o'             => isset($dt_post['o']) ? $dt_post['o'] : '',
                            'a'             => isset($dt_post['a']) ? $dt_post['a'] : '',
                            'p'             => isset($dt_post['p']) ? $dt_post['p'] : ''
                        );
                        $this->Nurse_soap->create($dt_nurse_soap);
                    } elseif ($dt_post['type_nurse_id'] == 11) {
                        // INIT_NURSE_SDKI_SLKI_SIKI
                        // Check sdki_id or slki_id or siki_id
                        if($this->Sdki->read($dt_post['sdki_id'], false) != [] && $this->Slki->read($dt_post['slki_id'], false) != [] && $this->Siki->read($dt_post['siki_id'], false) != []) {
                            $dt_nurse_sdki_slki_siki = array(
                                'nurse_id'                  => $id,
                                'sdki_id'                   => isset($dt_post['sdki_id']) ? $dt_post['sdki_id'] : '',
                                'slki_id'                   => isset($dt_post['slki_id']) ? $dt_post['slki_id'] : '',
                                'siki_id'                   => isset($dt_post['siki_id']) ? $dt_post['siki_id'] : '',
                                'associated_with_arry'      => isset($dt_post['associated_with_arry']) ? implode(',',$dt_post['associated_with_arry']) : '',
                                'outcome_criteria_arry'     => isset($dt_post['outcome_criteria_arry']) ? implode(',',$dt_post['outcome_criteria_arry']) : '',
                                'observation_arry'          => isset($dt_post['observation_arry']) ? implode(',',$dt_post['observation_arry']) : '',
                                'collaboration_arry'        => isset($dt_post['collaboration_arry']) ? implode(',',$dt_post['collaboration_arry']) : '',
                                'therapeutic_arry'          => isset($dt_post['therapeutic_arry']) ? implode(',',$dt_post['therapeutic_arry']) : '',
                                'education_arry'            => isset($dt_post['education_arry']) ? implode(',',$dt_post['education_arry']) : '',
                            );
                            $this->Nurse_sdki_slki_siki->create($dt_nurse_sdki_slki_siki);
                        } else {
                            $this->REST_Return(404, 'Wrong nurse_id or sdki_id or slki_id or siki_id!');
                            return;
                        }
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
                        $this->REST_Return(200, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong registration_destination or employee_nurse_id!');
                }
                return;     
            }
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
    
        $this->$model->whereFields = $q_strings;

        if(isset($q_strings['norm'])) {
            $this->$model->whereFields['r.norm'] = $q_strings['norm'];
            unset($this->$model->whereFields['norm']);
        }

        if (isset($q_strings['nurse_id'])){
            $this->$model->whereFields['a.id'] = $q_strings['nurse_id'];
            unset($this->$model->whereFields['nurse_id']);
        }
            
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($id);
        

        for ($i=0; $i < count($output); $i++) { 
            $nurse_id = $output[$i]->id;

            $select = 'a.*, e.fullname as employee_nurse_name';
            $where = array(
                'a.nurse_id' => $nurse_id
            );
            $join = array(
                array('employee e', 'a.user_nurse_id = e.id', 'LEFT'),
            );
            $tr_nurse = $this->Transaction_nurse->select(0, $select, $where, $join);
            
            $output[$i]->transaction_nurse = $tr_nurse;

            // $this->Nurse_soap->whereFields=array();
            // $this->Nurse_soap->whereFields['a.nurse_id']=$output[$i]->id;
            // $dt_nurse_soap = $this->Nurse_soap->read(0,false);
            // $output[$i]->soap                           = $dt_nurse_soap;
            // $output[$i]->systolic_blood_pressure_arry   = $output[$i]->systolic_blood_pressure_arry != '' ? preg_split("/\,/", $output[$i]->systolic_blood_pressure_arry) : '';
            // $output[$i]->general_state_arry             = $output[$i]->general_state_arry != '' ? preg_split("/\,/", $output[$i]->general_state_arry) : '';

            $output[$i]->humpty_dumpty = array(
                'id' => '',
                'age' => '',
                'gender' => '',
                'diagnosis' => '',
                'cognitive_impairment' => '',
                'environmental_factors' => '',
                'surgery_sedation_anesthesia' => '',
                'use_of_medication' => '',
                'total_score' => '',
            );

            $select = 'a.id,
            a.age,
            a.gender,
            a.diagnosis,
            a.cognitive_impairment,
            a.environmental_factors,
            a.surgery_sedation_anesthesia,
            a.use_of_medication,
            a.total_score,
            a.date_updated
            ';
            $where = array(
                'a.nurse_id' => $nurse_id,
            );
            $humpty_dumpty = $this->Humpty_dumpty_scale_fall_risk->select(0, $select, $where);
            if (count($humpty_dumpty) > 0) {
                $output[$i]->humpty_dumpty['id'] = $humpty_dumpty[0]->id;
                $output[$i]->humpty_dumpty['age'] = $humpty_dumpty[0]->age;
                $output[$i]->humpty_dumpty['gender'] = $humpty_dumpty[0]->gender;
                $output[$i]->humpty_dumpty['diagnosis'] = $humpty_dumpty[0]->diagnosis;
                $output[$i]->humpty_dumpty['cognitive_impairment'] = $humpty_dumpty[0]->cognitive_impairment;
                $output[$i]->humpty_dumpty['environmental_factors'] = $humpty_dumpty[0]->environmental_factors;
                $output[$i]->humpty_dumpty['surgery_sedation_anesthesia'] = $humpty_dumpty[0]->surgery_sedation_anesthesia;
                $output[$i]->humpty_dumpty['use_of_medication'] = $humpty_dumpty[0]->use_of_medication;
                $output[$i]->humpty_dumpty['total_score'] = $humpty_dumpty[0]->total_score;
                $output[$i]->humpty_dumpty['date_updated'] = $humpty_dumpty[0]->date_updated;
            }

            $output[$i]->morse = array(
                'id' => '',
                'history_of_falls' => '',
                'secondary_diagnosis' => '',
                'walking_ambulation' => '',
                'side_effects_of_falling' => '',
                'way_of_walking' => '',
                'mental_status' => '',
                'total_score' => '',
            );

            $select = 'a.id,
            a.history_of_falls,
            a.secondary_diagnosis,
            a.walking_ambulation,
            a.side_effects_of_falling,
            a.way_of_walking,
            a.mental_status,
            a.total_score,
            a.date_updated
            ';
            $where = array(
                'a.nurse_id' => $nurse_id,
            );
            $morse = $this->Morse_scale_fall_risk->select(0, $select, $where);

            if (count($morse) > 0) {
                $output[$i]->morse['id'] = $morse[0]->id;
                $output[$i]->morse['history_of_falls'] = $morse[0]->history_of_falls;
                $output[$i]->morse['secondary_diagnosis'] = $morse[0]->secondary_diagnosis;
                $output[$i]->morse['walking_ambulation'] = $morse[0]->walking_ambulation;
                $output[$i]->morse['side_effects_of_falling'] = $morse[0]->side_effects_of_falling;
                $output[$i]->morse['way_of_walking'] = $morse[0]->way_of_walking;
                $output[$i]->morse['mental_status'] = $morse[0]->mental_status;
                $output[$i]->morse['total_score'] = $morse[0]->total_score;
                $output[$i]->morse['date_updated'] = $morse[0]->date_updated;
            }
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Nurse with API
     * -------------------------
     * @method: GET
     */

    // public function detail_backup_get($p = 0) {
    //     header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = $this->name;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         if(isset($q_strings['norm'])) $this->$model->whereFields['r.norm'] = $q_strings['norm'];
    //         if (isset($q_strings['nurse_id'])){
    //             $this->$model->whereFields['a.id'] = $q_strings['nurse_id'];
    //         }else{
    //             $this->$model->whereFields = $q_strings;
    //         }
    //         $this->$model->whereFields['a.is_delete'] = '0';
    //         $output = $this->$model->read($id);
    //         $response_data = array();
    //         if ($output != [] && $id != 0) {
    //             $nurse_id = $output[0]->id;

    //             $select = 'a.*, e.fullname as employee_nurse_name';
    //             $where = array(
    //                 'a.nurse_id' => $nurse_id
    //             );
    //             $join = array(
    //                 array('employee e', 'a.user_nurse_id = e.id', 'LEFT'),
    //             );
    //             $tr_nurse = $this->Transaction_nurse->select(0, $select, $where, $join);
                
    //             $output[0]->transaction_nurse = $tr_nurse;

    //             $response_data = $output;
    //             $error   = false;
    //         } else if($output != [] or $id == 0) {
    //             for ($j=0; $j < count($output); $j++) { 
    //                 $this->Nurse_soap->whereFields=array();$this->Nurse_soap->whereFields['a.nurse_id']=$output[$j]->id;
    //                 $dt_nurse_soap = $this->Nurse_soap->read(0,false);
    //                 $output[$j]->soap   = $dt_nurse_soap;
    //                 $output[$j]->systolic_blood_pressure_arry   = $output[$j]->systolic_blood_pressure_arry != '' ? preg_split("/\,/", $output[$j]->systolic_blood_pressure_arry) : '';
    //                 $output[$j]->general_state_arry             = $output[$j]->general_state_arry != '' ? preg_split("/\,/", $output[$j]->general_state_arry) : '';
    //             }
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
        
        $this->$model->whereFields = $q_strings;

        if(isset($q_strings['norm'])) $this->$model->whereFields['r.norm'] = $q_strings['norm']; unset($this->$model->whereFields['norm']);
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $nurse_id = $output->rows[$i]->id;

            $select = 'a.*, e.fullname as employee_nurse_name';
            $join = array(
                array('employee e', 'a.user_nurse_id = e.id', 'LEFT'),
            );
            $order = array('a.date_added' => 'DESC');
            $where = array('a.nurse_id' => $nurse_id, 'a.is_delete' => 0);
            $tr_nurse = $this->Transaction_nurse->select(0, $select, $where, $join,$order);
            
            for ($j=0; $j < count($tr_nurse); $j++) { 
                $costing_id = $tr_nurse[$j]->costing_id;

                $select = 'a.id';
                $where = array('a.costing_id' => $costing_id);
                $costing_services_class = $this->Costing_services_class->select(0, $select, $where);

                $tr_nurse[$j]->costing_service_class_id = $costing_services_class[0]->id;
            }

            $output->rows[$i]->transaction_nurse = $tr_nurse;

            $output->rows[$i]->humpty_dumpty = array(
                'id' => '',
                'age' => '',
                'gender' => '',
                'diagnosis' => '',
                'cognitive_impairment' => '',
                'environmental_factors' => '',
                'surgery_sedation_anesthesia' => '',
                'use_of_medication' => '',
                'total_score' => '',
            );

            $select = 'a.id,
            a.age,
            a.gender,
            a.diagnosis,
            a.cognitive_impairment,
            a.environmental_factors,
            a.surgery_sedation_anesthesia,
            a.use_of_medication,
            a.total_score
            ';
            $where = array(
                'a.nurse_id' => $nurse_id,
            );
            $humpty_dumpty = $this->Humpty_dumpty_scale_fall_risk->select(0, $select, $where);
            if (count($humpty_dumpty) > 0) {
                $output->rows[$i]->humpty_dumpty['id'] = $humpty_dumpty[0]->id;
                $output->rows[$i]->humpty_dumpty['age'] = $humpty_dumpty[0]->age;
                $output->rows[$i]->humpty_dumpty['gender'] = $humpty_dumpty[0]->gender;
                $output->rows[$i]->humpty_dumpty['diagnosis'] = $humpty_dumpty[0]->diagnosis;
                $output->rows[$i]->humpty_dumpty['cognitive_impairment'] = $humpty_dumpty[0]->cognitive_impairment;
                $output->rows[$i]->humpty_dumpty['environmental_factors'] = $humpty_dumpty[0]->environmental_factors;
                $output->rows[$i]->humpty_dumpty['surgery_sedation_anesthesia'] = $humpty_dumpty[0]->surgery_sedation_anesthesia;
                $output->rows[$i]->humpty_dumpty['use_of_medication'] = $humpty_dumpty[0]->use_of_medication;
                $output->rows[$i]->humpty_dumpty['total_score'] = $humpty_dumpty[0]->total_score;
            }

            $output->rows[$i]->morse = array(
                'id' => '',
                'history_of_falls' => '',
                'secondary_diagnosis' => '',
                'walking_ambulation' => '',
                'side_effects_of_falling' => '',
                'way_of_walking' => '',
                'mental_status' => '',
                'total_score' => '',
            );

            $select = 'a.id,
            a.history_of_falls,
            a.secondary_diagnosis,
            a.walking_ambulation,
            a.side_effects_of_falling,
            a.way_of_walking,
            a.mental_status,
            a.total_score
            ';
            $where = array(
                'a.nurse_id' => $nurse_id,
            );
            $morse = $this->Morse_scale_fall_risk->select(0, $select, $where);

            if (count($morse) > 0) {
                $output->rows[$i]->morse['id'] = $morse[0]->id;
                $output->rows[$i]->morse['history_of_falls'] = $morse[0]->history_of_falls;
                $output->rows[$i]->morse['secondary_diagnosis'] = $morse[0]->secondary_diagnosis;
                $output->rows[$i]->morse['walking_ambulation'] = $morse[0]->walking_ambulation;
                $output->rows[$i]->morse['side_effects_of_falling'] = $morse[0]->side_effects_of_falling;
                $output->rows[$i]->morse['way_of_walking'] = $morse[0]->way_of_walking;
                $output->rows[$i]->morse['mental_status'] = $morse[0]->mental_status;
                $output->rows[$i]->morse['total_score'] = $morse[0]->total_score;
            }
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_vital_get($p = 0){
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
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            $data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                $data[$i]['id'] = $output->rows[$i]->id;
                $data[$i]['registration_destination_id'] = $output->rows[$i]->registration_destination_id;
                $data[$i]['systolic_blood_pressure'] = $output->rows[$i]->systolic_blood_pressure;
                $data[$i]['diastolic_blood_pressure'] = $output->rows[$i]->diastolic_blood_pressure;
                $data[$i]['classification_blood_pressure_type_id'] = $output->rows[$i]->classification_blood_pressure_type_id;
                $data[$i]['pulse_frequency'] = $output->rows[$i]->pulse_frequency;
                $data[$i]['breath_frequency'] = $output->rows[$i]->breath_frequency;
                $data[$i]['oximetry_frequency'] = $output->rows[$i]->oximetry_frequency;
                $data[$i]['classification_oximetry_type_id'] = $output->rows[$i]->classification_oximetry_type_id;
                $data[$i]['temperature'] = $output->rows[$i]->temperature;
                $data[$i]['classification_temperature_type_id'] = $output->rows[$i]->classification_temperature_type_id;
                $data[$i]['blood_sugar'] = $output->rows[$i]->blood_sugar;
            }
            if ($output->rows != [] && $id != 0) {
                $response_data = $data;
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $data;
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
    
        $limit = $q_strings['limit'] ?? 2; // FIXME - HARDCODE LIMIT SEMENTARA
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
        a.anamesa_type_id,
        a.family_name,
        a.family_relation_id,
        a.main_complaint,
        a.history_disease_now,
        a.history_disease_past,
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
        hf.id as health_facility_id,
        hf.name as health_facility_name,
        hfi.id as health_facility_item_id,
        hfi.name as health_facility_item_name,
        a.employee_nurse_id,
        e.fullname as employee_nurse_name,
        a.date_added
        ';
        $where_main['a.is_delete'] = 0;
        $where_main['r.norm'] = $norm;
        unset($where_main['norm']);
        $join_main = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('employee e', 'a.employee_nurse_id = e.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        $order = array('a.date_added' => 'DESC');
        $output = $this->$model->select(0, $select, $where_main, $join_main, $order);

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

    /**
     * Nurse API
     * --------------------
     * @param: anamesa_type_id
     * @param: family_name
     * @param: family_relation_id
     * @param: main_complaint
     * @param: history_disease_now
     * @param: history_disease_past
     * @param: systolic_blood_pressure_arry
     * @param: general_state_type_id
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
     * @param: aids
     * @param: prothesa
     * @param: disabled
     * @param: adl_type_id
     * --------------------
     * @method : POST
     * @link: version/mr/nurse
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
            $this->form_validation->set_rules('anamesa_type_id', 'anamesa_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('family_name', 'family_name', 'trim|max_length[100]');
            $this->form_validation->set_rules('family_relation_id', 'family_relation_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('main_complaint', 'main_complaint', 'trim');
            $this->form_validation->set_rules('history_disease_now', 'history_disease_now', 'trim');
            $this->form_validation->set_rules('history_disease_past', 'history_disease_past', 'trim');
            $this->form_validation->set_rules('history_tension', 'history_tension', 'trim');
            $this->form_validation->set_rules('systolic_blood_pressure_arry', 'systolic_blood_pressure_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('general_state_type_id', 'general_state_type_id', 'trim|max_length[1]');
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
            $this->form_validation->set_rules('aids', 'aids', 'trim');
            $this->form_validation->set_rules('prothesa', 'prothesa', 'trim');
            $this->form_validation->set_rules('disabled', 'disabled', 'trim');
            $this->form_validation->set_rules('adl_type_id', 'adl_type_id', 'trim|max_length[1]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);

                $select = 'a.id, a.employee_id';
                $user = $this->User->select($user_id, $select);
                $employee_nurse_id = $user[0]->employee_id;

                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse = $this->$model->read($id); 
                if($nurse != []) {
                    $registration_destination_id = $nurse[0]->registration_destination_id;

                    $dt_nurse = array(
                        'id'                                        => $id,
                        'anamesa_type_id'                           => isset($dt_put['anamesa_type_id']) ? $dt_put['anamesa_type_id'] : $nurse[0]->anamesa_type_id,
                        'family_name'                               => isset($dt_put['family_name']) ? $dt_put['family_name'] : $nurse[0]->family_name,
                        'family_relation_id'                        => isset($dt_put['family_relation_id']) ? $dt_put['family_relation_id'] : $nurse[0]->family_relation_id,
                        'main_complaint'                            => isset($dt_put['main_complaint']) ? $dt_put['main_complaint'] : $nurse[0]->main_complaint,
                        'history_disease_now'                       => isset($dt_put['history_disease_now']) ? $dt_put['history_disease_now'] : $nurse[0]->history_disease_now,
                        'history_disease_past'                      => isset($dt_put['history_disease_past']) ? $dt_put['history_disease_past'] : $nurse[0]->history_disease_past,
                        'history_tension'                           => isset($dt_put['history_tension']) ? $dt_put['history_tension'] : $nurse[0]->history_tension,
                        'systolic_blood_pressure_arry'              => isset($dt_put['systolic_blood_pressure_arry']) ? implode(',',$dt_put['systolic_blood_pressure_arry']) : $nurse[0]->systolic_blood_pressure_arry,
                        'general_state_type_id'                     => isset($dt_put['general_state_type_id']) ? $dt_put['general_state_type_id'] : $nurse[0]->general_state_type_id,
                        'eye_score'                                 => isset($dt_put['eye_score']) ? $dt_put['eye_score'] : '',
                        'verbal_score'                              => isset($dt_put['verbal_score']) ? $dt_put['verbal_score'] : '',
                        'motor_score'                               => isset($dt_put['motor_score']) ? $dt_put['motor_score'] : '',
                        'pupil_score'                               => isset($dt_put['pupil_score']) ? $dt_put['pupil_score'] : '',
                        'verbal_response'                           => isset($dt_put['verbal_response']) ? $dt_put['verbal_response'] : '',
                        'oxygen_type_id'                            => isset($dt_put['oxygen_type_id']) ? $dt_put['oxygen_type_id'] : '',
                        'systolic_blood_pressure'                   => isset($dt_put['systolic_blood_pressure']) ? $dt_put['systolic_blood_pressure'] : $nurse[0]->systolic_blood_pressure,
                        'diastolic_blood_pressure'                  => isset($dt_put['diastolic_blood_pressure']) ? $dt_put['diastolic_blood_pressure'] : $nurse[0]->diastolic_blood_pressure,
                        'classification_blood_pressure_type_id'     => isset($dt_put['classification_blood_pressure_type_id']) ? $dt_put['classification_blood_pressure_type_id'] : $nurse[0]->classification_blood_pressure_type_id,
                        'pulse_frequency'                           => isset($dt_put['pulse_frequency']) ? $dt_put['pulse_frequency'] : $nurse[0]->pulse_frequency,
                        'breath_frequency'                          => isset($dt_put['breath_frequency']) ? $dt_put['breath_frequency'] : $nurse[0]->breath_frequency,
                        'oximetry_frequency'                        => isset($dt_put['oximetry_frequency']) ? $dt_put['oximetry_frequency'] : $nurse[0]->oximetry_frequency,
                        'classification_oximetry_type_id'           => isset($dt_put['classification_oximetry_type_id']) ? $dt_put['classification_oximetry_type_id'] : $nurse[0]->classification_oximetry_type_id,
                        'temperature'                               => isset($dt_put['temperature']) ? $dt_put['temperature'] : $nurse[0]->temperature,
                        'classification_temperature_type_id'        => isset($dt_put['classification_temperature_type_id']) ? $dt_put['classification_temperature_type_id'] : $nurse[0]->classification_temperature_type_id,
                        'blood_sugar'                               => isset($dt_put['blood_sugar']) ? $dt_put['blood_sugar'] : $nurse[0]->blood_sugar,
                        'sore_score'                                => isset($dt_put['sore_score']) ? $dt_put['sore_score'] : $nurse[0]->sore_score,
                        'ews'                                       => isset($dt_put['ews']) ? $dt_put['ews'] : $nurse[0]->ews,
                        'weight'                                    => isset($dt_put['weight']) ? $dt_put['weight'] : $nurse[0]->weight,
                        'classification_weight_type_id'             => isset($dt_put['classification_weight_type_id']) ? $dt_put['classification_weight_type_id'] : $nurse[0]->classification_weight_type_id,
                        'height'                                    => isset($dt_put['height']) ? $dt_put['height'] : $nurse[0]->height,
                        'imt'                                       => isset($dt_put['imt']) ? $dt_put['imt'] : $nurse[0]->imt,
                        'head_circumference'                        => isset($dt_put['head_circumference']) ? $dt_put['head_circumference'] : $nurse[0]->head_circumference,
                        'chest_circumference'                       => isset($dt_put['chest_circumference']) ? $dt_put['chest_circumference'] : $nurse[0]->chest_circumference,
                        'arm_circumference'                         => isset($dt_put['arm_circumference']) ? $dt_put['arm_circumference'] : $nurse[0]->arm_circumference,
                        'stomach_circumference'                     => isset($dt_put['stomach_circumference']) ? $dt_put['stomach_circumference'] : $nurse[0]->stomach_circumference,
                        'knee_circumference'                        => isset($dt_put['knee_circumference']) ? $dt_put['knee_circumference'] : $nurse[0]->knee_circumference,
                        'aids'                                      => isset($dt_put['aids']) ? $dt_put['aids'] : $nurse[0]->aids,
                        'prothesa'                                  => isset($dt_put['prothesa']) ? $dt_put['prothesa'] : $nurse[0]->prothesa,
                        'disabled'                                  => isset($dt_put['disabled']) ? $dt_put['disabled'] : $nurse[0]->disabled,
                        'adl_type_id'                               => isset($dt_put['adl_type_id']) ? $dt_put['adl_type_id'] : $nurse[0]->adl_type_id
                    );
                    $id = $this->$model->update($id, $dt_nurse);

                    // INPUT TTV
                    $verbal_response = $dt_put['verbal_response'];
                    $oxygen_type_id = $dt_put['oxygen_type_id'];
                    $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
                    $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
                    $pulse_frequency = $dt_put['pulse_frequency'];
                    $breath_frequency = $dt_put['breath_frequency'];
                    $oximetry_frequency = $dt_put['oximetry_frequency'];
                    $temperature = $dt_put['temperature'];
                    $sore_score = $dt_put['sore_score'];
                    $ews = $dt_put['ews'];
                    $blood_sugar = $dt_put['blood_sugar'];
                    $weight = $dt_put['weight'];
                    $height = $dt_put['height'];

                    $dt_vital_signs_monitoring = array(
                        'registration_destination_id'   => $registration_destination_id,
                        'employee_nurse_id'             => $employee_nurse_id,
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

                    $aware = $dt_put['aware'];
                    $eye_score = $dt_put['eye_score'];
                    $verbal_score = $dt_put['verbal_score'];
                    $motor_score = $dt_put['motor_score'];
                    $pupil_score = $dt_put['pupil_score'];
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
                        'employee_nurse_id'      => $employee_nurse_id,
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
                            'employee_nurse_id'           => $employee_nurse_id,
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
                } else {
                    $this->REST_Return(404, 'Nurse with id ' . $id . ' does not exist!');
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
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // ANAMNESIS
        $anamesa_type_id = $dt_put['anamesa_type_id'];
        $family_name = $dt_put['family_name'];
        $family_relation_id = $dt_put['family_relation_id'];
        $main_complaint = $dt_put['main_complaint'];
        $history_disease_now = $dt_put['history_disease_now'];
        $history_disease_past = $dt_put['history_disease_past'];

        $dt_anamnesis = array(
            'anamesa_type_id'       => $anamesa_type_id,
            'family_name'           => $family_name,
            'family_relation_id'    => $family_relation_id,
            'main_complaint'        => $main_complaint,
            'history_disease_now'   => $history_disease_now,
            'history_disease_past'  => $history_disease_past,
        );

        $st_anamnesis = $this->$model->update_v2($nurse_id, $dt_anamnesis);
        if ($st_anamnesis < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_anamnesis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save anamnesis', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
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
    
        $id = $dt_put['id'];
        $registration_destination_id = $dt_put['registration_destination_id'];
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id, a.health_facility_item_id, a.registration_id, r.code_booking, a.call_nurse_stamp, p.birth_date';
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
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // TTV & ANTROPOMETRI
        $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $oximetry_frequency = $dt_put['oximetry_frequency'];
        $temperature = $dt_put['temperature'];
        $oxygen_type_id = $dt_put['oxygen_type_id'];
        $aware = $dt_put['aware'];
        $eye_score = $dt_put['eye_score'];
        $verbal_score = $dt_put['verbal_score'];
        $motor_score = $dt_put['motor_score'];
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

        $dt_ttv_antropometri = array(
            'systolic_blood_pressure'   => $systolic_blood_pressure,
            'diastolic_blood_pressure'  => $diastolic_blood_pressure,
            'pulse_frequency'           => $pulse_frequency,
            'breath_frequency'          => $breath_frequency,
            'oximetry_frequency'        => $oximetry_frequency,
            'temperature'               => $temperature,
            'oxygen_type_id'            => $oxygen_type_id,
            'aware'                     => $aware,
            'eye_score'                 => $eye_score,
            'verbal_score'              => $verbal_score,
            'motor_score'               => $motor_score,
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
        );

        $st_ttv_antropometri = $this->$model->update_v2($nurse_id, $dt_ttv_antropometri);
        if ($st_ttv_antropometri < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // INPUT TTV
        $dt_vital_signs_monitoring = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'source_id'                     => $nurse_id,
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
            'a.source_id' => $nurse_id,
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
                'employee_nurse_id'      => $employee_nurse_id,
                'source_id'              => $nurse_id,
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
                'a.source_id' => $nurse_id,
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
                'source_id'         => $nurse_id,
                'employee_nurse_id' => $employee_nurse_id,
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
                'employee_nurse_id'           => $employee_nurse_id,
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
            'source_id'             => $nurse_id,
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
            'a.source_id' => $nurse_id,
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
                'data'      => $dt_ttv_antropometri,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save ttv & antropometri', $response_data);
            return;
        } else {
            // UPDATE STATUS BPJS
            $registration_id = $registration_destination[0]->registration_id;
            $health_facility_item_id = $registration_destination[0]->health_facility_item_id;
            $code_booking = $registration_destination[0]->code_booking;
            $call_nurse_stamp = $registration_destination[0]->call_nurse_stamp;
    
            if ($code_booking && $call_nurse_stamp == null) {
                $dt_destination = array(
                    'id' => $registration_destination_id,
                    'registration_id' => $registration_id,
                    'health_facility_item_id' => $health_facility_item_id,
                    'date_task_3' => date('Y-m-d H:i:s'), 
                    'call_nurse_stamp'=> date('Y-m-d H:i:s'),
                );
    
                $this->Registration_destination->replace($dt_destination);
    
                $update_antrean_3 = array(
                    'kodebooking'       => $code_booking,
                    'taskid'            => 3,
                    'waktu'             => $this->tomiliseconds(date("Y-m-d H:i:s"))
                );
                $this->load->library('Vclaim_antrol','vclaim_antrol');
    
                $updatewaktu_antrean_3 = $this->vclaim_antrol->request("/antrean/updatewaktu", "POST", json_encode($update_antrean_3));
                $json_update_antrean_3 = json_decode($updatewaktu_antrean_3, true);
                $json_update_antrean_3 = json_decode($json_update_antrean_3);
            }
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_functional_put() {
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
        $this->form_validation->set_rules($this->save_functional_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id, p.birth_date';
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

        $patient_birth_date = $registration_destination[0]->birth_date;
        $patient_age = count_age($patient_birth_date);
        
        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // FUNCTIONAL
        $aids = $dt_put['aids'];
        $prothesa = $dt_put['prothesa'];
        $disabled = $dt_put['disabled'];
        $adl_type_id = $dt_put['adl_type_id'];
        $pain_score = $dt_put['pain_score'];

        $dt_functional = array(
            'aids'          => $aids,
            'prothesa'      => $prothesa,
            'disabled'      => $disabled,
            'adl_type_id'   => $adl_type_id,
            'pain_score'    => $pain_score,
        );

        $st_functional = $this->$model->update_v2($nurse_id, $dt_functional);
        if ($st_functional < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        // FALL RISK
        if ($patient_age < 18) {
            $age = $dt_put['age'];
            $gender = $dt_put['gender'];
            $diagnosis = $dt_put['diagnosis'];
            $cognitive_impairment = $dt_put['cognitive_impairment'];
            $environmental_factors = $dt_put['environmental_factors'];
            $surgery_sedation_anesthesia = $dt_put['surgery_sedation_anesthesia'];
            $use_of_medication = $dt_put['use_of_medication'];
            $total_score = $dt_put['total_score'];
            
            $dt_humpty_dumpty = array(
                'registration_destination_id'   => $registration_destination_id,
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
            $history_of_falls = $dt_put['history_of_falls'];
            $secondary_diagnosis = $dt_put['secondary_diagnosis'];
            $walking_ambulation = $dt_put['walking_ambulation'];
            $side_effects_of_falling = $dt_put['side_effects_of_falling'];
            $way_of_walking = $dt_put['way_of_walking'];
            $mental_status = $dt_put['mental_status'];
            $total_score = $dt_put['total_score'];

            $dt_morse = array(
                'registration_destination_id'   => $registration_destination_id,
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

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_functional,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save functional', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_hemodialysis_put() {
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
        $this->form_validation->set_rules($this->save_hemodialysis_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // HEMODIALYSIS
        $hemodialysis_date = $dt_put['hemodialysis_date'];
        $hemodialysis_type_id = $dt_put['hemodialysis_type_id'];
        $frequency_type_id = $dt_put['frequency_type_id'];
        $dialysate_type_id = $dt_put['dialysate_type_id'];
        $vascular_type_id = $dt_put['vascular_type_id'];
        $long_hours = $dt_put['long_hours'];
        $blood_velocity = $dt_put['blood_velocity'];
        $initial_dose = $dt_put['initial_dose'];
        $intermittent = $dt_put['intermittent'];
        $ufg = $dt_put['ufg'];
        $dialysate_velocity = $dt_put['dialysate_velocity'];
        $maintenance = $dt_put['maintenance'];
        $lmwh = $dt_put['lmwh'];
        $dialysate_temperature = $dt_put['dialysate_temperature'];
        $vascular_jugularis = $dt_put['vascular_jugularis'];
        $heparin_type_id = $dt_put['heparin_type_id'];
        $without_heparin = $dt_put['without_heparin'];
        $profiling_type_id = $dt_put['profiling_type_id'];
        $ultrafiltration = $dt_put['ultrafiltration'];
        $natrium = $dt_put['natrium'];
        $bicarbonate = $dt_put['bicarbonate'];
        $therapy = $dt_put['therapy'];
        $medicine = $dt_put['medicine'];
        $injection = $dt_put['injection'];

        $dt_hemodialysis = array(
            'registration_destination_id' => $registration_destination_id,
            'nurse_id'              => $nurse_id,
            'hemodialysis_date'     => $hemodialysis_date,
            'hemodialysis_type_id'  => $hemodialysis_type_id,
            'frequency_type_id'     => $frequency_type_id,
            'dialysate_type_id'     => $dialysate_type_id,
            'vascular_type_id'      => $vascular_type_id,
            'long_hours'            => $long_hours,
            'blood_velocity'        => $blood_velocity,
            'initial_dose'          => $initial_dose,
            'intermittent'          => $intermittent,
            'ufg'                   => $ufg,
            'dialysate_velocity'    => $dialysate_velocity,
            'maintenance'           => $maintenance,
            'lmwh'                  => $lmwh,
            'dialysate_temperature' => $dialysate_temperature,
            'vascular_jugularis'    => $vascular_jugularis,
            'heparin_type_id'       => $heparin_type_id,
            'without_heparin'       => $without_heparin,
            'profiling_type_id'     => $profiling_type_id,
            'ultrafiltration'       => $ultrafiltration,
            'natrium'               => $natrium,
            'bicarbonate'           => $bicarbonate,
            'therapy'               => $therapy,
            'medicine'              => $medicine,
            'injection'             => $injection,
            'user_added'            => $user_id,
            'date_added'            => date('Y-m-d H:i:s'),
            'date_updated'          => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_hemodialysis = $this->Nurse_hemodialysis->select(0, $select, $where);        
        if (count($nurse_hemodialysis) == 0) {
            $this->Nurse_hemodialysis->create($dt_hemodialysis);
        } else {
            $nurse_hemodialysis_id = $nurse_hemodialysis[0]->id;
            unset($dt_hemodialysis['user_added']);
            unset($dt_hemodialysis['date_added']);
            $dt_hemodialysis['user_updated'] = $user_id;
            $st_hemodialysis = $this->Nurse_hemodialysis->update_v2($nurse_hemodialysis_id, $dt_hemodialysis);
            if ($st_hemodialysis < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save hemodialysis', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_emergency_put() {
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
        $this->form_validation->set_rules($this->save_emergency_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // EMERGENCY
        $been_hospitalized_type_id = $dt_put['been_hospitalized_type_id'];
        $been_hospitalized_answer = $dt_put['been_hospitalized_answer'];
        $any_history_family_disease_type_id = $dt_put['any_history_family_disease_type_id'];
        $any_history_family_answer_arry = implode(',', $dt_put['any_history_family_answer_arry']);
        $any_history_family_answer = $dt_put['any_history_family_answer'];
        $any_surgery = $dt_put['any_surgery'];
        $any_surgery_date = $dt_put['any_surgery_date'];
        $complication = $dt_put['complication'];
        $history_allergy_type_id = $dt_put['history_allergy_type_id'];
        $history_allergy = $dt_put['history_allergy'];
        $history_psychological_arry = implode(',', $dt_put['history_psychological_arry']);
        $body_mass_index = $dt_put['body_mass_index'];
        $patient_food_intake = $dt_put['patient_food_intake'];
        $patient_certain_diets = $dt_put['patient_certain_diets'];
        $six_month_lose_weight = $dt_put['six_month_lose_weight'];
        $lose_weight = $dt_put['lose_weight'];
        $patient_disease_condition = $dt_put['patient_disease_condition'];
        $nutrition_total_score = $dt_put['nutrition_total_score'];
        $fall_risk_method_type_id = $dt_put['fall_risk_method_type_id'];
        $fall_risk_type_id = $dt_put['fall_risk_type_id'];
        $nursing_diagnoses_arry = implode(',', $dt_put['nursing_diagnoses_arry']);
        $nursing_diagnoses = $dt_put['nursing_diagnoses'];
        $implementation_arry = implode(',', $dt_put['implementation_arry']);
        $implementation = $dt_put['implementation'];
        $any_surgery_type_id = $dt_put['any_surgery_type_id'];
        $fall_risk_component_a = $dt_put['fall_risk_component_a'];
        $fall_risk_component_b = $dt_put['fall_risk_component_b'];

        $dt_emergency = array(
            'nurse_id'                           => $nurse_id,
            'been_hospitalized_answer'           => $been_hospitalized_answer,
            'been_hospitalized_type_id'          => $been_hospitalized_type_id,
            'any_history_family_disease_type_id' => $any_history_family_disease_type_id,
            'any_history_family_answer_arry'     => $any_history_family_answer_arry,
            'any_history_family_answer'          => $any_history_family_answer,
            'any_surgery'                        => $any_surgery,
            'any_surgery_date'                   => $any_surgery_date,
            'complication'                       => $complication,
            'history_allergy_type_id'            => $history_allergy_type_id,
            'history_allergy'                    => $history_allergy,
            'history_psychological_arry'         => $history_psychological_arry,
            'body_mass_index'                    => $body_mass_index,
            'patient_food_intake'                => $patient_food_intake,
            'patient_certain_diets'              => $patient_certain_diets,
            'six_month_lose_weight'              => $six_month_lose_weight,
            'lose_weight'                        => $lose_weight,
            'patient_disease_condition'          => $patient_disease_condition,
            'nutrition_total_score'              => $nutrition_total_score,
            'fall_risk_method_type_id'           => $fall_risk_method_type_id,
            'fall_risk_type_id'                  => $fall_risk_type_id,
            'nursing_diagnoses_arry'             => $nursing_diagnoses_arry,
            'nursing_diagnoses'                  => $nursing_diagnoses,
            'implementation_arry'                => $implementation_arry,
            'implementation'                     => $implementation,
            'any_surgery_type_id'                => $any_surgery_type_id,
            'fall_risk_component_a'              => $fall_risk_component_a,
            'fall_risk_component_b'              => $fall_risk_component_b,
            'user_added'                         => $user_id,
            'date_added'                         => date('Y-m-d H:i:s'),
            'date_updated'                       => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_emergency = $this->Nurse_emergency->select(0, $select, $where);        
        if (count($nurse_emergency) == 0) {
            $this->Nurse_emergency->create($dt_emergency);
        } else {
            $nurse_emergency_id = $nurse_emergency[0]->id;
            unset($dt_emergency['user_added']);
            unset($dt_emergency['date_added']);
            $dt_emergency['user_updated'] = $user_id;
            $st_emergency = $this->Nurse_emergency->update_v2($nurse_emergency_id, $dt_emergency);
            if ($st_emergency < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }
        
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_emergency,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save emergency', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_adult_inpatient_put() {
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
        $this->form_validation->set_rules($this->save_adult_inpatient_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // ADULT INPATIENT
        $psychological_status_arry = $dt_put['psychological_status_arry'] ?? '' != '' ? implode(',', $dt_put['psychological_status_arry']): '';
        $psychological_status = $dt_put['psychological_status'];
        $mental_status_arry = $dt_put['mental_status_arry'] ?? '' != '' ? implode(',', $dt_put['mental_status_arry']): '';
        $mental_status_problems = $dt_put['mental_status_problems'];
        $mental_status_violence = $dt_put['mental_status_violence'];
        $elimination_defecate = $dt_put['elimination_defecate'];
        $elimination_urination = $dt_put['elimination_urination'];
        $elimination_consistency_type_id = $dt_put['elimination_consistency_type_id'];
        $elimination_color_type_id = $dt_put['elimination_color_type_id'];
        $elimination_color = $dt_put['elimination_color'];
        // $sore_complaints_type_id = $dt_put['sore_complaints_type_id'];
        // $sore_complaints = $dt_put['sore_complaints'];
        // $sore_goes_away_arry = $dt_put['sore_goes_away_arry'] ?? '' != '' ? implode(',', $dt_put['sore_goes_away_arry']): '';
        // $sore_goes_away = $dt_put['sore_goes_away'];
        // $sore_method_type_id = $dt_put['sore_method_type_id'];
        // $sore_affects_arry = $dt_put['sore_affects_arry'] ?? '' != '' ? implode(',', $dt_put['sore_affects_arry']): '';
        // $sore_affects = $dt_put['sore_affects'];
        $discharge_treatment_duration = $dt_put['discharge_treatment_duration'];
        $discharge_further_care_arry = $dt_put['discharge_further_care_arry'] ?? '' != '' ? implode(',', $dt_put['discharge_further_care_arry']): '';
        // $tell_doctor_type_id = $dt_put['tell_doctor_type_id'];
        // $tell_doctor_date = $dt_put['tell_doctor_date'];
        // $face_score = $dt_put['face_score'];
        // $legs_score = $dt_put['legs_score'];
        // $activity_score = $dt_put['activity_score'];
        // $cry_score = $dt_put['cry_score'];
        // $consolability_score = $dt_put['consolability_score'];
        // $sore_total_score = $dt_put['sore_total_score'];
        // $duration = $dt_put['duration'];
        // $location = $dt_put['location'];
        // $frequency = $dt_put['frequency'];
        // $sore_quality_arry = $dt_put['sore_quality_arry'] ?? '' != '' ? implode(',', $dt_put['sore_quality_arry']): '';
        $medical_devices_type_id = $dt_put['medical_devices_type_id'];
        $medical_devices_raw = $dt_put['medical_devices'];
        $examination_history_raw = $dt_put['examination_history'];
        $care_plan_diet_nutrition_type_id = $dt_put['care_plan_diet_nutrition_type_id'];
        $care_plan_diet_nutrition = $dt_put['care_plan_diet_nutrition'];
        $care_plan_rehabilitation_type_id = $dt_put['care_plan_rehabilitation_type_id'];
        $care_plan_rehabilitation = $dt_put['care_plan_rehabilitation'];
        $care_plan_wound_type_id = $dt_put['care_plan_wound_type_id'];
        $care_plan_wound = $dt_put['care_plan_wound'];
        $care_plan_pain_management_type_id = $dt_put['care_plan_pain_management_type_id'];
        $care_plan_pain_management = $dt_put['care_plan_pain_management'];
        $care_plan_other = $dt_put['care_plan_other'];
        $evaluation_smoke_type_id = $dt_put['evaluation_smoke_type_id'];
        $evaluation_smoke_amount = $dt_put['evaluation_smoke_amount'];
        $evaluation_smoke_long = $dt_put['evaluation_smoke_long'];
        $evaluation_smoke_unit = $dt_put['evaluation_smoke_unit'];
        $evaluation_alcohol_type_id = $dt_put['evaluation_alcohol_type_id'];
        $evaluation_alcohol_type = $dt_put['evaluation_alcohol_type'];
        $evaluation_alcohol_amount = $dt_put['evaluation_alcohol_amount'];
        $evaluation_sedative_type_id = $dt_put['evaluation_sedative_type_id'];
        $evaluation_sedative_type = $dt_put['evaluation_sedative_type'];
        $evaluation_sedative_amount = $dt_put['evaluation_sedative_amount'];
        $evaluation_sedative_unit = $dt_put['evaluation_sedative_unit'];
        $evaluation_sleep_type_id = $dt_put['evaluation_sleep_type_id'];
        $evaluation_sleep = $dt_put['evaluation_sleep'];
        $evaluation_daily_type_id = $dt_put['evaluation_daily_type_id'];
        $evaluation_activity_type_id = $dt_put['evaluation_activity_type_id'];
        $evaluation_extremities_top_type_id = $dt_put['evaluation_extremities_top_type_id'];
        $evaluation_grip_type_id = $dt_put['evaluation_grip_type_id'];
        $evaluation_grip_other = $dt_put['evaluation_grip_other'];
        $evaluation_coordination_type_id = $dt_put['evaluation_coordination_type_id'];
        $evaluation_coordination_other = $dt_put['evaluation_coordination_other'];
        $evaluation_extremities_bottom_type_id = $dt_put['evaluation_extremities_bottom_type_id'];
        $evaluation_extremities_bottom_bone = $dt_put['evaluation_extremities_bottom_bone'];
        $evaluation_ambulation_type_id = $dt_put['evaluation_ambulation_type_id'];
        
        $medical_devices = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($medical_devices_raw); $i++) { 
            if (!isset($medical_devices_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in medical_devices is required');
                return;
            }

            if (!isset($medical_devices_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in medical_devices is required');
                return;
            }

            $type = $medical_devices_raw[$i]['type'];
            $explanation = $medical_devices_raw[$i]['explanation'];

            $dt_medical_devices = array(
                'type' => $type,
                'explanation' => $explanation,
            );

            array_push($medical_devices->data, $dt_medical_devices);
        }

        $medical_devices = json_encode($medical_devices);

        $examination_history = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($examination_history_raw); $i++) { 
            if (!isset($examination_history_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['page'])) {
                $this->REST_Return(400, 'page field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in examination_history is required');
                return;
            }

            $type = $examination_history_raw[$i]['type'];
            $page = $examination_history_raw[$i]['page'];
            $explanation = $examination_history_raw[$i]['explanation'];

            $dt_examination_history = array(
                'type' => $type,
                'page' => $page,
                'explanation' => $explanation,
            );

            array_push($examination_history->data, $dt_examination_history);
        }
        
        $examination_history = json_encode($examination_history);

        $dt_nurse_adult_inpatient = array(
            'nurse_id'                               => $nurse_id,
            'psychological_status_arry'              => $psychological_status_arry,
            'psychological_status'                   => $psychological_status,
            'mental_status_arry'                     => $mental_status_arry,
            'mental_status_problems'                 => $mental_status_problems,
            'mental_status_violence'                 => $mental_status_violence,
            'elimination_defecate'                   => $elimination_defecate,
            'elimination_urination'                  => $elimination_urination,
            'elimination_consistency_type_id'        => $elimination_consistency_type_id,
            'elimination_color_type_id'              => $elimination_color_type_id,
            'elimination_color'                      => $elimination_color,
            // 'sore_complaints_type_id'                => $sore_complaints_type_id,
            // 'sore_complaints'                        => $sore_complaints,
            // 'sore_goes_away_arry'                    => $sore_goes_away_arry,
            // 'sore_goes_away'                         => $sore_goes_away,
            // 'sore_method_type_id'                    => $sore_method_type_id,
            // 'sore_affects_arry'                      => $sore_affects_arry,
            // 'sore_affects'                           => $sore_affects,
            'discharge_treatment_duration'           => $discharge_treatment_duration,
            'discharge_further_care_arry'            => $discharge_further_care_arry,
            // 'tell_doctor_type_id'                    => $tell_doctor_type_id,
            // 'tell_doctor_date'                       => $tell_doctor_date,
            // 'face_score'                             => $face_score,
            // 'legs_score'                             => $legs_score,
            // 'activity_score'                         => $activity_score,
            // 'cry_score'                              => $cry_score,
            // 'consolability_score'                    => $consolability_score,
            // 'sore_total_score'                       => $sore_total_score,
            // 'duration'                               => $duration,
            // 'location'                               => $location,
            // 'frequency'                              => $frequency,
            // 'sore_quality_arry'                      => $sore_quality_arry,
            'medical_devices_type_id'                => $medical_devices_type_id,
            'medical_devices'                        => $medical_devices,
            'examination_history'                    => $examination_history,
            'care_plan_diet_nutrition_type_id'       => $care_plan_diet_nutrition_type_id,
            'care_plan_diet_nutrition'               => $care_plan_diet_nutrition,
            'care_plan_rehabilitation_type_id'       => $care_plan_rehabilitation_type_id,
            'care_plan_rehabilitation'               => $care_plan_rehabilitation,
            'care_plan_wound_type_id'                => $care_plan_wound_type_id,
            'care_plan_wound'                        => $care_plan_wound,
            'care_plan_pain_management_type_id'      => $care_plan_pain_management_type_id,
            'care_plan_pain_management'              => $care_plan_pain_management,
            'care_plan_other'                        => $care_plan_other,
            'evaluation_smoke_type_id'               => $evaluation_smoke_type_id,
            'evaluation_smoke_amount'                => $evaluation_smoke_amount,
            'evaluation_smoke_long'                  => $evaluation_smoke_long,
            'evaluation_smoke_unit'                  => $evaluation_smoke_unit,
            'evaluation_alcohol_type_id'             => $evaluation_alcohol_type_id,
            'evaluation_alcohol_type'                => $evaluation_alcohol_type,
            'evaluation_alcohol_amount'              => $evaluation_alcohol_amount,
            'evaluation_sedative_type_id'            => $evaluation_sedative_type_id,
            'evaluation_sedative_type'               => $evaluation_sedative_type,
            'evaluation_sedative_amount'             => $evaluation_sedative_amount,
            'evaluation_sedative_unit'               => $evaluation_sedative_unit,
            'evaluation_sleep_type_id'               => $evaluation_sleep_type_id,
            'evaluation_sleep'                       => $evaluation_sleep,
            'evaluation_daily_type_id'               => $evaluation_daily_type_id,
            'evaluation_activity_type_id'            => $evaluation_activity_type_id,
            'evaluation_extremities_top_type_id'     => $evaluation_extremities_top_type_id,
            'evaluation_grip_type_id'                => $evaluation_grip_type_id,
            'evaluation_grip_other'                  => $evaluation_grip_other,
            'evaluation_coordination_type_id'        => $evaluation_coordination_type_id,
            'evaluation_coordination_other'          => $evaluation_coordination_other,
            'evaluation_extremities_bottom_type_id'  => $evaluation_extremities_bottom_type_id,
            'evaluation_extremities_bottom_bone'     => $evaluation_extremities_bottom_bone,
            'evaluation_ambulation_type_id'          => $evaluation_ambulation_type_id,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_adult_inpatient = $this->Nurse_adult_inpatient->select(0, $select, $where);        
        if (count($nurse_adult_inpatient) == 0) {
            $this->Nurse_adult_inpatient->create($dt_nurse_adult_inpatient);
        } else {
            $nurse_adult_inpatient_id = $nurse_adult_inpatient[0]->id;
            unset($dt_nurse_adult_inpatient['user_added']);
            unset($dt_nurse_adult_inpatient['date_added']);
            $dt_nurse_adult_inpatient['user_updated'] = $user_id;
            $st_adult_inpatient = $this->Nurse_adult_inpatient->update_v2($nurse_adult_inpatient_id, $dt_nurse_adult_inpatient);
            if ($st_adult_inpatient < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_adult_inpatient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save adult_inpatient', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_child_inpatient_put() {
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
        $this->form_validation->set_rules($this->save_child_inpatient_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // CHILD INPATIENT
        $growth_birth_weight = $dt_put['growth_birth_weight'];
        $growth_birth_height = $dt_put['growth_birth_height'];
        $growth_breast_milk_age = $dt_put['growth_breast_milk_age'];
        $growth_breast_milk_unit = $dt_put['growth_breast_milk_unit'];
        $growth_formula_milk_age = $dt_put['growth_formula_milk_age'];
        $growth_formula_milk_unit = $dt_put['growth_formula_milk_unit'];
        $growth_solid_food_age = $dt_put['growth_solid_food_age'];
        $growth_solid_food_unit = $dt_put['growth_solid_food_unit'];
        $growth_additional_food_age = $dt_put['growth_additional_food_age'];
        $growth_additional_food_unit = $dt_put['growth_additional_food_unit'];
        $growth_prone_age = $dt_put['growth_prone_age'];
        $growth_prone_unit = $dt_put['growth_prone_unit'];
        $growth_sit_age = $dt_put['growth_sit_age'];
        $growth_sit_unit = $dt_put['growth_sit_unit'];
        $growth_crawl_age = $dt_put['growth_crawl_age'];
        $growth_crawl_unit = $dt_put['growth_crawl_unit'];
        $growth_stand_age = $dt_put['growth_stand_age'];
        $growth_stand_unit = $dt_put['growth_stand_unit'];
        $growth_walk_age = $dt_put['growth_walk_age'];
        $growth_walk_unit = $dt_put['growth_walk_unit'];
        $medical_devices_type_id = $dt_put['medical_devices_type_id'];
        $medical_devices_raw = $dt_put['medical_devices'];
        $examination_history_raw = $dt_put['examination_history'];
        $immunization_bcg_arry = $dt_put['immunization_bcg_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_bcg_arry']) : '';
        $immunization_hepatitis_b_arry = $dt_put['immunization_hepatitis_b_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_hepatitis_b_arry']) : '';
        $immunization_dpt_arry = $dt_put['immunization_dpt_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_dpt_arry']) : '';
        $immunization_polio_arry = $dt_put['immunization_polio_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_polio_arry']) : '';
        $immunization_measles_arry = $dt_put['immunization_measles_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_measles_arry']) : '';
        $immunization_hib_arry = $dt_put['immunization_hib_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_hib_arry']) : '';
        $immunization_ipd_arry = $dt_put['immunization_ipd_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_ipd_arry']) : '';
        $immunization_flu_arry = $dt_put['immunization_flu_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_flu_arry']) : '';
        $immunization_chickenpox_arry = $dt_put['immunization_chickenpox_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_chickenpox_arry']) : '';
        $immunization_mmr_arry = $dt_put['immunization_mmr_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_mmr_arry']) : '';
        $immunization_typhoid_arry = $dt_put['immunization_typhoid_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_typhoid_arry']) : '';
        $immunization_hepatitis_a_arry = $dt_put['immunization_hepatitis_a_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_hepatitis_a_arry']) : '';
        $immunization_hpv_arry = $dt_put['immunization_hpv_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_hpv_arry']) : '';
        $immunization_rotavirus_arry = $dt_put['immunization_rotavirus_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_rotavirus_arry']) : '';
        $immunization_influenza_arry = $dt_put['immunization_influenza_arry'] ?? [] != [] ? implode(',', $dt_put['immunization_influenza_arry']) : '';
        $comm_needs_speech_type_id = $dt_put['comm_needs_speech_type_id'];
        $comm_needs_speech_disorder = $dt_put['comm_needs_speech_disorder'];
        $comm_needs_colloquial_arry = $dt_put['comm_needs_colloquial_arry'] ?? [] != [] ? implode(',', $dt_put['comm_needs_colloquial_arry']) : '';
        $comm_needs_colloquial_local = $dt_put['comm_needs_colloquial_local'];
        $comm_needs_colloquial_other = $dt_put['comm_needs_colloquial_other'];
        $comm_needs_translator_type_id = $dt_put['comm_needs_translator_type_id'];
        $edu_needs_barriers_type_id = $dt_put['edu_needs_barriers_type_id'];
        $edu_needs_barriers_arry = $dt_put['edu_needs_barriers_arry'] ?? [] != [] ? implode(',', $dt_put['edu_needs_barriers_arry']) : '';
        $edu_needs_barriers_other = $dt_put['edu_needs_barriers_other'];
        $edu_needs_preferred_arry = $dt_put['edu_needs_preferred_arry'] ?? [] != [] ? implode(',', $dt_put['edu_needs_preferred_arry']) : '';
        $edu_needs_level_of_education_type_id = $dt_put['edu_needs_level_of_education_type_id'];
        $edu_needs_level_of_education_other = $dt_put['edu_needs_level_of_education_other'];
        $edu_needs_information_arry = $dt_put['edu_needs_information_arry'] ?? [] != [] ? implode(',', $dt_put['edu_needs_information_arry']) : '';
        $edu_needs_information_other = $dt_put['edu_needs_information_other'];
        $evaluation_smoke_type_id = $dt_put['evaluation_smoke_type_id'];
        $evaluation_smoke_amount = $dt_put['evaluation_smoke_amount'];
        $evaluation_smoke_long = $dt_put['evaluation_smoke_long'];
        $evaluation_smoke_unit = $dt_put['evaluation_smoke_unit'];
        $evaluation_alcohol_type_id = $dt_put['evaluation_alcohol_type_id'];
        $evaluation_alcohol_type = $dt_put['evaluation_alcohol_type'];
        $evaluation_alcohol_amount = $dt_put['evaluation_alcohol_amount'];
        $evaluation_sedative_type_id = $dt_put['evaluation_sedative_type_id'];
        $evaluation_sedative_type = $dt_put['evaluation_sedative_type'];
        $evaluation_sedative_amount = $dt_put['evaluation_sedative_amount'];
        $evaluation_sedative_unit = $dt_put['evaluation_sedative_unit'];
        $evaluation_sleep_type_id = $dt_put['evaluation_sleep_type_id'];
        $evaluation_sleep = $dt_put['evaluation_sleep'];
        $evaluation_daily_type_id = $dt_put['evaluation_daily_type_id'];
        $evaluation_activity_type_id = $dt_put['evaluation_activity_type_id'];
        $evaluation_extremities_top_type_id = $dt_put['evaluation_extremities_top_type_id'];
        $evaluation_grip_type_id = $dt_put['evaluation_grip_type_id'];
        $evaluation_grip_other = $dt_put['evaluation_grip_other'];
        $evaluation_extremities_bottom_type_id = $dt_put['evaluation_extremities_bottom_type_id'];
        $evaluation_extremities_bottom_bone = $dt_put['evaluation_extremities_bottom_bone'];
        $evaluation_ambulation_type_id = $dt_put['evaluation_ambulation_type_id'];
        $evaluation_mental_status_type_id = $dt_put['evaluation_mental_status_type_id'];
        $evaluation_psychological_status_type_id = $dt_put['evaluation_psychological_status_type_id'];
        $evaluation_psychological_status_other = $dt_put['evaluation_psychological_status_other'];
        $evaluation_elimination_defecate_type_id = $dt_put['evaluation_elimination_defecate_type_id'];
        $evaluation_elimination_defecate_freq = $dt_put['evaluation_elimination_defecate_freq'];
        $evaluation_elimination_urination_type_id = $dt_put['evaluation_elimination_urination_type_id'];
        // $evaluation_sexual_menstruation_type_id = $dt_put['evaluation_sexual_menstruation_type_id'];
        $evaluation_sexual_circumcision_type_id = $dt_put['evaluation_sexual_circumcision_type_id'];
        $evaluation_sexual_circumcision = $dt_put['evaluation_sexual_circumcision'];
        $evaluation_sexual_problem_type_id = $dt_put['evaluation_sexual_problem_type_id'];
        $evaluation_sexual_problem = $dt_put['evaluation_sexual_problem'];
        
        $medical_devices = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($medical_devices_raw); $i++) { 
            if (!isset($medical_devices_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in medical_devices is required');
                return;
            }

            if (!isset($medical_devices_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in medical_devices is required');
                return;
            }

            $type = $medical_devices_raw[$i]['type'];
            $explanation = $medical_devices_raw[$i]['explanation'];

            $dt_medical_devices = array(
                'type' => $type,
                'explanation' => $explanation,
            );

            array_push($medical_devices->data, $dt_medical_devices);
        }

        $medical_devices = json_encode($medical_devices);

        $examination_history = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($examination_history_raw); $i++) { 
            if (!isset($examination_history_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['page'])) {
                $this->REST_Return(400, 'page field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in examination_history is required');
                return;
            }

            $type = $examination_history_raw[$i]['type'];
            $page = $examination_history_raw[$i]['page'];
            $explanation = $examination_history_raw[$i]['explanation'];

            $dt_examination_history = array(
                'type' => $type,
                'page' => $page,
                'explanation' => $explanation,
            );

            array_push($examination_history->data, $dt_examination_history);
        }
        
        $examination_history = json_encode($examination_history);

        $dt_nurse_child_inpatient = array(
            'nurse_id'                               => $nurse_id,
            'growth_birth_weight'                      => $growth_birth_weight,
            'growth_birth_height'                      => $growth_birth_height,
            'growth_breast_milk_age'                   => $growth_breast_milk_age,
            'growth_breast_milk_unit'                  => $growth_breast_milk_unit,
            'growth_formula_milk_age'                  => $growth_formula_milk_age,
            'growth_formula_milk_unit'                 => $growth_formula_milk_unit,
            'growth_solid_food_age'                    => $growth_solid_food_age,
            'growth_solid_food_unit'                   => $growth_solid_food_unit,
            'growth_additional_food_age'               => $growth_additional_food_age,
            'growth_additional_food_unit'              => $growth_additional_food_unit,
            'growth_prone_age'                         => $growth_prone_age,
            'growth_prone_unit'                        => $growth_prone_unit,
            'growth_sit_age'                           => $growth_sit_age,
            'growth_sit_unit'                          => $growth_sit_unit,
            'growth_crawl_age'                         => $growth_crawl_age,
            'growth_crawl_unit'                        => $growth_crawl_unit,
            'growth_stand_age'                         => $growth_stand_age,
            'growth_stand_unit'                        => $growth_stand_unit,
            'growth_walk_age'                          => $growth_walk_age,
            'growth_walk_unit'                         => $growth_walk_unit,
            'medical_devices_type_id'                  => $medical_devices_type_id,
            'medical_devices'                          => $medical_devices,
            'examination_history'                      => $examination_history,
            'immunization_bcg_arry'                    => $immunization_bcg_arry,
            'immunization_hepatitis_b_arry'            => $immunization_hepatitis_b_arry,
            'immunization_dpt_arry'                    => $immunization_dpt_arry,
            'immunization_polio_arry'                  => $immunization_polio_arry,
            'immunization_measles_arry'                => $immunization_measles_arry,
            'immunization_hib_arry'                    => $immunization_hib_arry,
            'immunization_ipd_arry'                    => $immunization_ipd_arry,
            'immunization_flu_arry'                    => $immunization_flu_arry,
            'immunization_chickenpox_arry'             => $immunization_chickenpox_arry,
            'immunization_mmr_arry'                    => $immunization_mmr_arry,
            'immunization_typhoid_arry'                => $immunization_typhoid_arry,
            'immunization_hepatitis_a_arry'            => $immunization_hepatitis_a_arry,
            'immunization_hpv_arry'                    => $immunization_hpv_arry,
            'immunization_rotavirus_arry'              => $immunization_rotavirus_arry,
            'immunization_influenza_arry'              => $immunization_influenza_arry,
            'comm_needs_speech_type_id'                => $comm_needs_speech_type_id,
            'comm_needs_speech_disorder'               => $comm_needs_speech_disorder,
            'comm_needs_colloquial_arry'               => $comm_needs_colloquial_arry,
            'comm_needs_colloquial_local'              => $comm_needs_colloquial_local,
            'comm_needs_colloquial_other'              => $comm_needs_colloquial_other,
            'comm_needs_translator_type_id'            => $comm_needs_translator_type_id,
            'edu_needs_barriers_type_id'               => $edu_needs_barriers_type_id,
            'edu_needs_barriers_arry'                  => $edu_needs_barriers_arry,
            'edu_needs_barriers_other'                 => $edu_needs_barriers_other,
            'edu_needs_preferred_arry'                 => $edu_needs_preferred_arry,
            'edu_needs_level_of_education_type_id'     => $edu_needs_level_of_education_type_id,
            'edu_needs_level_of_education_other'       => $edu_needs_level_of_education_other,
            'edu_needs_information_arry'               => $edu_needs_information_arry,
            'edu_needs_information_other'              => $edu_needs_information_other,
            'evaluation_smoke_type_id'                 => $evaluation_smoke_type_id,
            'evaluation_smoke_amount'                  => $evaluation_smoke_amount,
            'evaluation_smoke_long'                    => $evaluation_smoke_long,
            'evaluation_smoke_unit'                    => $evaluation_smoke_unit,
            'evaluation_alcohol_type_id'               => $evaluation_alcohol_type_id,
            'evaluation_alcohol_type'                  => $evaluation_alcohol_type,
            'evaluation_alcohol_amount'                => $evaluation_alcohol_amount,
            'evaluation_sedative_type_id'              => $evaluation_sedative_type_id,
            'evaluation_sedative_type'                 => $evaluation_sedative_type,
            'evaluation_sedative_amount'               => $evaluation_sedative_amount,
            'evaluation_sedative_unit'               => $evaluation_sedative_unit,
            'evaluation_sleep_type_id'                 => $evaluation_sleep_type_id,
            'evaluation_sleep'                         => $evaluation_sleep,
            'evaluation_daily_type_id'                 => $evaluation_daily_type_id,
            'evaluation_activity_type_id'              => $evaluation_activity_type_id,
            'evaluation_extremities_top_type_id'       => $evaluation_extremities_top_type_id,
            'evaluation_grip_type_id'                  => $evaluation_grip_type_id,
            'evaluation_grip_other'                    => $evaluation_grip_other,
            'evaluation_extremities_bottom_type_id'    => $evaluation_extremities_bottom_type_id,
            'evaluation_extremities_bottom_bone'       => $evaluation_extremities_bottom_bone,
            'evaluation_ambulation_type_id'            => $evaluation_ambulation_type_id,
            'evaluation_mental_status_type_id'         => $evaluation_mental_status_type_id,
            'evaluation_psychological_status_type_id'  => $evaluation_psychological_status_type_id,
            'evaluation_psychological_status_other'    => $evaluation_psychological_status_other,
            'evaluation_elimination_defecate_type_id'  => $evaluation_elimination_defecate_type_id,
            'evaluation_elimination_defecate_freq'     => $evaluation_elimination_defecate_freq,
            'evaluation_elimination_urination_type_id' => $evaluation_elimination_urination_type_id,
            // 'evaluation_sexual_menstruation_type_id'   => $evaluation_sexual_menstruation_type_id,
            'evaluation_sexual_circumcision_type_id'   => $evaluation_sexual_circumcision_type_id,
            'evaluation_sexual_circumcision'           => $evaluation_sexual_circumcision,
            'evaluation_sexual_problem_type_id'        => $evaluation_sexual_problem_type_id,
            'evaluation_sexual_problem'                => $evaluation_sexual_problem,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_child_inpatient = $this->Nurse_child_inpatient->select(0, $select, $where);        
        if (count($nurse_child_inpatient) == 0) {
            $this->Nurse_child_inpatient->create($dt_nurse_child_inpatient);
        } else {
            $nurse_child_inpatient_id = $nurse_child_inpatient[0]->id;
            unset($dt_nurse_child_inpatient['user_added']);
            unset($dt_nurse_child_inpatient['date_added']);
            $dt_nurse_child_inpatient['user_updated'] = $user_id;
            $st_child_inpatient = $this->Nurse_child_inpatient->update_v2($nurse_child_inpatient_id, $dt_nurse_child_inpatient);
            if ($st_child_inpatient < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_child_inpatient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save child_inpatient', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_neonate_inpatient_put() {
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
        $this->form_validation->set_rules($this->save_neonate_inpatient_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // NEONATE INPATIENT
        $gestational_age = $dt_put['gestational_age'];
        $corrected_age = $dt_put['corrected_age'];
        $type_of_delivery = $dt_put['type_of_delivery'];
        $labor_diagnosis = $dt_put['labor_diagnosis'];
        $mother_blood_type_id = $dt_put['mother_blood_type_id'];
        $mother_lab_result_arry = $dt_put['mother_lab_result_arry'] ?? '' != '' ? implode(',', $dt_put['mother_lab_result_arry']): '';
        $mother_lab_result = $dt_put['mother_lab_result'];
        $maternal_medical_history = $dt_put['maternal_medical_history'];
        $baby_gender = $dt_put['baby_gender'];
        $birth_history_type_id = $dt_put['birth_history_type_id'];
        $birth_history_lk = $dt_put['birth_history_lk'];
        $birth_history_ld = $dt_put['birth_history_ld'];
        $medical_devices_raw = $dt_put['medical_devices'];
        $examination_history_raw = $dt_put['examination_history'];
        $postnatal_drug_arry = $dt_put['postnatal_drug_arry'] ?? '' != '' ? implode(',', $dt_put['postnatal_drug_arry']): '';
        $postnatal_drug_vit_k = $dt_put['postnatal_drug_vit_k'];
        $postnatal_drug_eye = $dt_put['postnatal_drug_eye'];
        $postnatal_drug_other = $dt_put['postnatal_drug_other'];
        $comm_needs_speech_type_id = $dt_put['comm_needs_speech_type_id'];
        $comm_needs_speech_disorder = $dt_put['comm_needs_speech_disorder'];
        $comm_needs_colloquial_arry = $dt_put['comm_needs_colloquial_arry'] ?? '' != '' ? implode(',', $dt_put['comm_needs_colloquial_arry']): '';
        $comm_needs_colloquial_local = $dt_put['comm_needs_colloquial_local'];
        $comm_needs_colloquial_other = $dt_put['comm_needs_colloquial_other'];
        $comm_needs_translator_type_id = $dt_put['comm_needs_translator_type_id'];
        $edu_needs_level_of_education_type_id = $dt_put['edu_needs_level_of_education_type_id'];
        $edu_needs_level_of_education_other = $dt_put['edu_needs_level_of_education_other'];
        $edu_needs_information_arry = $dt_put['edu_needs_information_arry'] ?? '' != '' ? implode(',', $dt_put['edu_needs_information_arry']): '';
        $edu_needs_information_other = $dt_put['edu_needs_information_other'];
        $socio_cultural_ethnic = $dt_put['socio_cultural_ethnic'];
        $socio_cultural_citizen = $dt_put['socio_cultural_citizen'];
        $socio_cultural_residence_type_id = $dt_put['socio_cultural_residence_type_id'];
        $socio_cultural_who_cares_type_id = $dt_put['socio_cultural_who_cares_type_id'];
        $socio_cultural_parent = $dt_put['socio_cultural_parent'];
        $socio_cultural_visit_type_id = $dt_put['socio_cultural_visit_type_id'];
        $socio_cultural_touch_type_id = $dt_put['socio_cultural_touch_type_id'];
        $socio_cultural_carry_type_id = $dt_put['socio_cultural_carry_type_id'];
        $socio_cultural_eye_contact_type_id = $dt_put['socio_cultural_eye_contact_type_id'];
        $socio_cultural_speak_type_id = $dt_put['socio_cultural_speak_type_id'];
        $socio_cultural_other = $dt_put['socio_cultural_other'];
        $evaluation_comfort_arry = $dt_put['evaluation_comfort_arry'] ?? '' != '' ? implode(',', $dt_put['evaluation_comfort_arry']): '';
        $evaluation_comfort_device_type_id = $dt_put['evaluation_comfort_device_type_id'];
        $evaluation_comfort_other = $dt_put['evaluation_comfort_other'];
        $evaluation_rest_pattern_type_id = $dt_put['evaluation_rest_pattern_type_id'];
        $evaluation_vagina_type_id = $dt_put['evaluation_vagina_type_id'];
        $evaluation_catheter_woman_type_id = $dt_put['evaluation_catheter_woman_type_id'];
        $evaluation_menstruation_type_id = $dt_put['evaluation_menstruation_type_id'];
        $evaluation_reproduction_woman_other = $dt_put['evaluation_reproduction_woman_other'];
        $evaluation_prepuce_type_id = $dt_put['evaluation_prepuce_type_id'];
        $evaluation_scrotum_type_id = $dt_put['evaluation_scrotum_type_id'];
        $evaluation_catheter_man_type_id = $dt_put['evaluation_catheter_man_type_id'];
        $evaluation_hyposdias_type_id = $dt_put['evaluation_hyposdias_type_id'];
        $evaluation_reproduction_man_other = $dt_put['evaluation_reproduction_man_other'];
        $evaluation_urination_freq = $dt_put['evaluation_urination_freq'];
        $evaluation_urine_production = $dt_put['evaluation_urine_production'];
        $evaluation_urine_type_id = $dt_put['evaluation_urine_type_id'];
        $evaluation_elimination_catheter_type_id = $dt_put['evaluation_elimination_catheter_type_id'];
        $evaluation_dysuria_type_id = $dt_put['evaluation_dysuria_type_id'];
        $evaluation_elimination_other = $dt_put['evaluation_elimination_other'];
        $evaluation_client_movement_arry = $dt_put['evaluation_client_movement_arry'] ?? '' != '' ? implode(',', $dt_put['evaluation_client_movement_arry']): '';
        $evaluation_safety_other = $dt_put['evaluation_safety_other'];
        
        $medical_devices = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($medical_devices_raw); $i++) { 
            if (!isset($medical_devices_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in medical_devices is required');
                return;
            }

            if (!isset($medical_devices_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in medical_devices is required');
                return;
            }

            $type = $medical_devices_raw[$i]['type'];
            $explanation = $medical_devices_raw[$i]['explanation'];

            $dt_medical_devices = array(
                'type' => $type,
                'explanation' => $explanation,
            );

            array_push($medical_devices->data, $dt_medical_devices);
        }

        $medical_devices = json_encode($medical_devices);

        $examination_history = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($examination_history_raw); $i++) { 
            if (!isset($examination_history_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['page'])) {
                $this->REST_Return(400, 'page field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in examination_history is required');
                return;
            }

            $type = $examination_history_raw[$i]['type'];
            $page = $examination_history_raw[$i]['page'];
            $explanation = $examination_history_raw[$i]['explanation'];

            $dt_examination_history = array(
                'type' => $type,
                'page' => $page,
                'explanation' => $explanation,
            );

            array_push($examination_history->data, $dt_examination_history);
        }
        
        $examination_history = json_encode($examination_history);

        $dt_nurse_neonate_inpatient = array(
            'nurse_id'                               => $nurse_id,
            'gestational_age'                        => $gestational_age,
            'corrected_age'                          => $corrected_age,
            'type_of_delivery'                       => $type_of_delivery,
            'labor_diagnosis'                        => $labor_diagnosis,
            'mother_blood_type_id'                   => $mother_blood_type_id,
            'mother_lab_result_arry'                 => $mother_lab_result_arry,
            'mother_lab_result'                      => $mother_lab_result,
            'maternal_medical_history'               => $maternal_medical_history,
            'baby_gender'                            => $baby_gender,
            'birth_history_type_id'                  => $birth_history_type_id,
            'birth_history_lk'                       => $birth_history_lk,
            'birth_history_ld'                       => $birth_history_ld,
            'medical_devices'                        => $medical_devices,
            'examination_history'                    => $examination_history,
            'postnatal_drug_arry'                    => $postnatal_drug_arry,
            'postnatal_drug_vit_k'                   => $postnatal_drug_vit_k,
            'postnatal_drug_eye'                     => $postnatal_drug_eye,
            'postnatal_drug_other'                   => $postnatal_drug_other,
            'comm_needs_speech_type_id'              => $comm_needs_speech_type_id,
            'comm_needs_speech_disorder'             => $comm_needs_speech_disorder,
            'comm_needs_colloquial_arry'             => $comm_needs_colloquial_arry,
            'comm_needs_colloquial_local'            => $comm_needs_colloquial_local,
            'comm_needs_colloquial_other'            => $comm_needs_colloquial_other,
            'comm_needs_translator_type_id'          => $comm_needs_translator_type_id,
            'edu_needs_level_of_education_type_id'   => $edu_needs_level_of_education_type_id,
            'edu_needs_level_of_education_other'     => $edu_needs_level_of_education_other,
            'edu_needs_information_arry'             => $edu_needs_information_arry,
            'edu_needs_information_other'            => $edu_needs_information_other,
            'socio_cultural_ethnic'                  => $socio_cultural_ethnic,
            'socio_cultural_citizen'                 => $socio_cultural_citizen,
            'socio_cultural_residence_type_id'       => $socio_cultural_residence_type_id,
            'socio_cultural_who_cares_type_id'       => $socio_cultural_who_cares_type_id,
            'socio_cultural_parent'                  => $socio_cultural_parent,
            'socio_cultural_visit_type_id'           => $socio_cultural_visit_type_id,
            'socio_cultural_touch_type_id'           => $socio_cultural_touch_type_id,
            'socio_cultural_carry_type_id'           => $socio_cultural_carry_type_id,
            'socio_cultural_eye_contact_type_id'     => $socio_cultural_eye_contact_type_id,
            'socio_cultural_speak_type_id'           => $socio_cultural_speak_type_id,
            'socio_cultural_other'                   => $socio_cultural_other,
            'evaluation_comfort_arry'                => $evaluation_comfort_arry,
            'evaluation_comfort_device_type_id'      => $evaluation_comfort_device_type_id,
            'evaluation_comfort_other'               => $evaluation_comfort_other,
            'evaluation_rest_pattern_type_id'        => $evaluation_rest_pattern_type_id,
            'evaluation_vagina_type_id'              => $evaluation_vagina_type_id,
            'evaluation_catheter_woman_type_id'      => $evaluation_catheter_woman_type_id,
            'evaluation_menstruation_type_id'        => $evaluation_menstruation_type_id,
            'evaluation_reproduction_woman_other'    => $evaluation_reproduction_woman_other,
            'evaluation_prepuce_type_id'             => $evaluation_prepuce_type_id,
            'evaluation_scrotum_type_id'             => $evaluation_scrotum_type_id,
            'evaluation_catheter_man_type_id'        => $evaluation_catheter_man_type_id,
            'evaluation_hyposdias_type_id'           => $evaluation_hyposdias_type_id,
            'evaluation_reproduction_man_other'      => $evaluation_reproduction_man_other,
            'evaluation_urination_freq'              => $evaluation_urination_freq,
            'evaluation_urine_production'            => $evaluation_urine_production,
            'evaluation_urine_type_id'               => $evaluation_urine_type_id,
            'evaluation_elimination_catheter_type_id' => $evaluation_elimination_catheter_type_id,
            'evaluation_dysuria_type_id'              => $evaluation_dysuria_type_id,
            'evaluation_elimination_other'            => $evaluation_elimination_other,
            'evaluation_client_movement_arry'         => $evaluation_client_movement_arry,
            'evaluation_safety_other'                 => $evaluation_safety_other,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_neonate_inpatient = $this->Nurse_neonate_inpatient->select(0, $select, $where);        
        if (count($nurse_neonate_inpatient) == 0) {
            $this->Nurse_neonate_inpatient->create($dt_nurse_neonate_inpatient);
        } else {
            $nurse_neonate_inpatient_id = $nurse_neonate_inpatient[0]->id;
            unset($dt_nurse_neonate_inpatient['user_added']);
            unset($dt_nurse_neonate_inpatient['date_added']);
            $dt_nurse_neonate_inpatient['user_updated'] = $user_id;
            $st_neonate_inpatient = $this->Nurse_neonate_inpatient->update_v2($nurse_neonate_inpatient_id, $dt_nurse_neonate_inpatient);
            if ($st_neonate_inpatient < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_neonate_inpatient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save neonate_inpatient', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_obgyn_put() {
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
        $this->form_validation->set_rules($this->save_obgyn_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // OBGYN
        $medical_devices_raw = $dt_put['medical_devices'];
        $examination_history_raw = $dt_put['examination_history'];
        $comm_needs_speech_type_id = $dt_put['comm_needs_speech_type_id'];
        $comm_needs_speech_disorder = $dt_put['comm_needs_speech_disorder'];
        $comm_needs_colloquial_arry = $dt_put['comm_needs_colloquial_arry'] ?? null != null ? implode(',', $dt_put['comm_needs_colloquial_arry']): '';
        $comm_needs_colloquial_local = $dt_put['comm_needs_colloquial_local'];
        $comm_needs_colloquial_other = $dt_put['comm_needs_colloquial_other'];
        $comm_needs_translator_type_id = $dt_put['comm_needs_translator_type_id'];
        $evaluation_smoke_unit = $dt_put['evaluation_smoke_unit'];
        $evaluation_sedative_unit = $dt_put['evaluation_sedative_unit'];
        $evaluation_smoke_type_id = $dt_put['evaluation_smoke_type_id'];
        $evaluation_smoke_amount = $dt_put['evaluation_smoke_amount'];
        $evaluation_smoke_long = $dt_put['evaluation_smoke_long'];
        $evaluation_alcohol_type_id = $dt_put['evaluation_alcohol_type_id'];
        $evaluation_alcohol_type = $dt_put['evaluation_alcohol_type'];
        $evaluation_alcohol_amount = $dt_put['evaluation_alcohol_amount'];
        $evaluation_sedative_type_id = $dt_put['evaluation_sedative_type_id'];
        $evaluation_sedative_type = $dt_put['evaluation_sedative_type'];
        $evaluation_sedative_amount = $dt_put['evaluation_sedative_amount'];
        $evaluation_sleep_type_id = $dt_put['evaluation_sleep_type_id'];
        $evaluation_sleep = $dt_put['evaluation_sleep'];
        $evaluation_daily_type_id = $dt_put['evaluation_daily_type_id'];
        $evaluation_activity_type_id = $dt_put['evaluation_activity_type_id'];
        $evaluation_extremities_top_type_id = $dt_put['evaluation_extremities_top_type_id'];
        $evaluation_grip_type_id = $dt_put['evaluation_grip_type_id'];
        $evaluation_grip_other = $dt_put['evaluation_grip_other'];
        $evaluation_extremities_bottom_type_id = $dt_put['evaluation_extremities_bottom_type_id'];
        $evaluation_extremities_bottom_bone = $dt_put['evaluation_extremities_bottom_bone'];
        $evaluation_ambulation_type_id = $dt_put['evaluation_ambulation_type_id'];
        $evaluation_mental_status_type_id = $dt_put['evaluation_mental_status_type_id'];
        $evaluation_psychological_status_type_id = $dt_put['evaluation_psychological_status_type_id'];
        $evaluation_psychological_status_other = $dt_put['evaluation_psychological_status_other'];
        $evaluation_elimination_defecate_type_id = $dt_put['evaluation_elimination_defecate_type_id'];
        $evaluation_elimination_defecate_freq = $dt_put['evaluation_elimination_defecate_freq'];
        $evaluation_elimination_urination_type_id = $dt_put['evaluation_elimination_urination_type_id'];
        $evaluation_sexual_marital_status_type_id = $dt_put['evaluation_sexual_marital_status_type_id'];
        $evaluation_sexual_marital_status = $dt_put['evaluation_sexual_marital_status'];
        $evaluation_sexual_menstruation_type_id = $dt_put['evaluation_sexual_menstruation_type_id'];
        $evaluation_sexual_obstetrics_gynecology = $dt_put['evaluation_sexual_obstetrics_gynecology'];
        $evaluation_sexual_history_contraception = $dt_put['evaluation_sexual_history_contraception'];
        $evaluation_sexual_other_habits = $dt_put['evaluation_sexual_other_habits'];
        $evaluation_sexual_circumcision_type_id = $dt_put['evaluation_sexual_circumcision_type_id'];
        $evaluation_sexual_circumcision = $dt_put['evaluation_sexual_circumcision'];
        $evaluation_sexual_problem_type_id = $dt_put['evaluation_sexual_problem_type_id'];
        $evaluation_sexual_problem = $dt_put['evaluation_sexual_problem'];
        $birth_history_raw = $dt_put['birth_history'];
        
        $medical_devices = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($medical_devices_raw); $i++) { 
            if (!isset($medical_devices_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in medical_devices is required');
                return;
            }

            if (!isset($medical_devices_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in medical_devices is required');
                return;
            }

            $type = $medical_devices_raw[$i]['type'];
            $explanation = $medical_devices_raw[$i]['explanation'];

            $dt_medical_devices = array(
                'type' => $type,
                'explanation' => $explanation,
            );

            array_push($medical_devices->data, $dt_medical_devices);
        }

        $medical_devices = json_encode($medical_devices);

        $examination_history = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($examination_history_raw); $i++) { 
            if (!isset($examination_history_raw[$i]['type'])) {
                $this->REST_Return(400, 'type field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['page'])) {
                $this->REST_Return(400, 'page field in examination_history is required');
                return;
            }

            if (!isset($examination_history_raw[$i]['explanation'])) {
                $this->REST_Return(400, 'explanation field in examination_history is required');
                return;
            }

            $type = $examination_history_raw[$i]['type'];
            $page = $examination_history_raw[$i]['page'];
            $explanation = $examination_history_raw[$i]['explanation'];

            $dt_examination_history = array(
                'type' => $type,
                'page' => $page,
                'explanation' => $explanation,
            );

            array_push($examination_history->data, $dt_examination_history);
        }
        
        $examination_history = json_encode($examination_history);

        $birth_history = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($birth_history_raw); $i++) { 
            if (!isset($birth_history_raw[$i]['year'])) {
                $this->REST_Return(400, 'year field in birth_history is required');
                return;
            }

            if (!isset($birth_history_raw[$i]['birth_time'])) {
                $this->REST_Return(400, 'birth_time field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['pregnancy_complications'])) {
                $this->REST_Return(400, 'pregnancy_complications field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['spontaneous'])) {
                $this->REST_Return(400, 'spontaneous field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['action'])) {
                $this->REST_Return(400, 'action field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['complications'])) {
                $this->REST_Return(400, 'complications field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['sex'])) {
                $this->REST_Return(400, 'sex field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['weight'])) {
                $this->REST_Return(400, 'weight field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['condition'])) {
                $this->REST_Return(400, 'condition field in birth_history is required');
                return;
            }
            
            if (!isset($birth_history_raw[$i]['breastfeeding'])) {
                $this->REST_Return(400, 'breastfeeding field in birth_history is required');
                return;
            }

            $year = $birth_history_raw[$i]['year'];
            $birth_time = $birth_history_raw[$i]['birth_time'];
            $pregnancy_complications = $birth_history_raw[$i]['pregnancy_complications'];
            $spontaneous = $birth_history_raw[$i]['spontaneous'];
            $action = $birth_history_raw[$i]['action'];
            $complications = $birth_history_raw[$i]['complications'];
            $sex = $birth_history_raw[$i]['sex'];
            $weight = $birth_history_raw[$i]['weight'];
            $condition = $birth_history_raw[$i]['condition'];
            $breastfeeding = $birth_history_raw[$i]['breastfeeding'];

            $dt_birth_history = array(
                'year' => $year,
                'birth_time' => $birth_time,
                'pregnancy_complications' => $pregnancy_complications,
                'spontaneous' => $spontaneous,
                'action' => $action,
                'complications' => $complications,
                'sex' => $sex,
                'weight' => $weight,
                'condition' => $condition,
                'breastfeeding' => $breastfeeding,
            );

            array_push($birth_history->data, $dt_birth_history);
        }

        $birth_history = json_encode($birth_history);

        $dt_nurse_obgyn = array(
            'nurse_id'                                 => $nurse_id,
            'medical_devices'                          => $medical_devices,
            'examination_history'                      => $examination_history,
            'comm_needs_speech_type_id'                => $comm_needs_speech_type_id,
            'comm_needs_speech_disorder'               => $comm_needs_speech_disorder,
            'comm_needs_colloquial_arry'               => $comm_needs_colloquial_arry,
            'comm_needs_colloquial_local'              => $comm_needs_colloquial_local,
            'comm_needs_colloquial_other'              => $comm_needs_colloquial_other,
            'comm_needs_translator_type_id'            => $comm_needs_translator_type_id,
            'evaluation_smoke_unit'                    => $evaluation_smoke_unit,
            'evaluation_sedative_unit'                 => $evaluation_sedative_unit,
            'evaluation_smoke_type_id'                 => $evaluation_smoke_type_id,
            'evaluation_smoke_amount'                  => $evaluation_smoke_amount,
            'evaluation_smoke_long'                    => $evaluation_smoke_long,
            'evaluation_alcohol_type_id'               => $evaluation_alcohol_type_id,
            'evaluation_alcohol_type'                  => $evaluation_alcohol_type,
            'evaluation_alcohol_amount'                => $evaluation_alcohol_amount,
            'evaluation_sedative_type_id'              => $evaluation_sedative_type_id,
            'evaluation_sedative_type'                 => $evaluation_sedative_type,
            'evaluation_sedative_amount'               => $evaluation_sedative_amount,
            'evaluation_sleep_type_id'                 => $evaluation_sleep_type_id,
            'evaluation_sleep'                         => $evaluation_sleep,
            'evaluation_daily_type_id'                 => $evaluation_daily_type_id,
            'evaluation_activity_type_id'              => $evaluation_activity_type_id,
            'evaluation_extremities_top_type_id'       => $evaluation_extremities_top_type_id,
            'evaluation_grip_type_id'                  => $evaluation_grip_type_id,
            'evaluation_grip_other'                    => $evaluation_grip_other,
            'evaluation_extremities_bottom_type_id'    => $evaluation_extremities_bottom_type_id,
            'evaluation_extremities_bottom_bone'       => $evaluation_extremities_bottom_bone,
            'evaluation_ambulation_type_id'            => $evaluation_ambulation_type_id,
            'evaluation_mental_status_type_id'         => $evaluation_mental_status_type_id,
            'evaluation_psychological_status_type_id'  => $evaluation_psychological_status_type_id,
            'evaluation_psychological_status_other'    => $evaluation_psychological_status_other,
            'evaluation_elimination_defecate_type_id'  => $evaluation_elimination_defecate_type_id,
            'evaluation_elimination_defecate_freq'     => $evaluation_elimination_defecate_freq,
            'evaluation_elimination_urination_type_id' => $evaluation_elimination_urination_type_id,
            'evaluation_sexual_marital_status_type_id' => $evaluation_sexual_marital_status_type_id,
            'evaluation_sexual_marital_status'         => $evaluation_sexual_marital_status,
            'evaluation_sexual_menstruation_type_id'   => $evaluation_sexual_menstruation_type_id,
            'evaluation_sexual_obstetrics_gynecology'  => $evaluation_sexual_obstetrics_gynecology,
            'evaluation_sexual_history_contraception'  => $evaluation_sexual_history_contraception,
            'evaluation_sexual_other_habits'           => $evaluation_sexual_other_habits,
            'evaluation_sexual_circumcision_type_id'   => $evaluation_sexual_circumcision_type_id,
            'evaluation_sexual_circumcision'           => $evaluation_sexual_circumcision,
            'evaluation_sexual_problem_type_id'        => $evaluation_sexual_problem_type_id,
            'evaluation_sexual_problem'                => $evaluation_sexual_problem,
            'birth_history'                            => $birth_history,
            'user_added'                               => $user_id,
            'user_updated'                             => $user_id,
            'date_added'                               =>  date('Y-m-d H:i:s',time()),
            'date_updated'                             =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn = $this->Nurse_obgyn->select(0, $select, $where);        
        if (count($nurse_obgyn) == 0) {
            $this->Nurse_obgyn->create($dt_nurse_obgyn);
        } else {
            $nurse_obgyn_id = $nurse_obgyn[0]->id;
            unset($dt_nurse_obgyn['user_added']);
            unset($dt_nurse_obgyn['date_added']);
            $dt_nurse_obgyn['user_updated'] = $user_id;
            $st_obgyn = $this->Nurse_obgyn->update_v2($nurse_obgyn_id, $dt_nurse_obgyn);
            if ($st_obgyn < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save obgyn', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_obgyn_special_put() {
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
        $this->form_validation->set_rules($this->save_obgyn_special_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // NURSE OBGYN
        $dt_nurse_obgyn = array(
            'nurse_id' => $nurse_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn = $this->Nurse_obgyn->select(0, $select, $where);        
        if (count($nurse_obgyn) == 0) {
            $nurse_obgyn_id = $this->Nurse_obgyn->create($dt_nurse_obgyn);
        } else {
            $nurse_obgyn_id = $nurse_obgyn[0]->id;
        }

        // OBGYN SPECIAL
        $anamnesis_g = $dt_put['anamnesis_g'];
        $anamnesis_p = $dt_put['anamnesis_p'];
        $anamnesis_a = $dt_put['anamnesis_a'];
        $anamnesis_h = $dt_put['anamnesis_h'];
        $anamnesis_period = $dt_put['anamnesis_period'];
        $anamnesis_antenatal_type_id = $dt_put['anamnesis_antenatal_type_id'];
        $anamnesis_disease_pregnancy_arry = $dt_put['anamnesis_disease_pregnancy_arry'] ?? null != null ? implode(',',$dt_put['anamnesis_disease_pregnancy_arry']): '';
        $anamnesis_disease_pregnancy_other = $dt_put['anamnesis_disease_pregnancy_other'];
        $anamnesis_his_type_id = $dt_put['anamnesis_his_type_id'];
        $anamnesis_his_date = $dt_put['anamnesis_his_date'];
        $anamnesis_amniotic_fluid_type_id = $dt_put['anamnesis_amniotic_fluid_type_id'];
        $anamnesis_amniotic_fluid_date = $dt_put['anamnesis_amniotic_fluid_date'];
        $anamnesis_amniotic_fluid_color_type_id = $dt_put['anamnesis_amniotic_fluid_color_type_id'];
        $anamnesis_amniotic_fluid_color_other = $dt_put['anamnesis_amniotic_fluid_color_other'];
        $anamnesis_blood_slym_type_id = $dt_put['anamnesis_blood_slym_type_id'];
        $anamnesis_antepartum_type_id = $dt_put['anamnesis_antepartum_type_id'];
        $anamnesis_antepartum_date = $dt_put['anamnesis_antepartum_date'];
        $anamnesis_antepartum_amount = $dt_put['anamnesis_antepartum_amount'];
        $anamnesis_seizure_type_id = $dt_put['anamnesis_seizure_type_id'];
        $anamnesis_seizure_clonic_long = $dt_put['anamnesis_seizure_clonic_long'];
        $anamnesis_straining_type_id = $dt_put['anamnesis_straining_type_id'];
        $anamnesis_straining_date = $dt_put['anamnesis_straining_date'];
        $anamnesis_straining_amount = $dt_put['anamnesis_straining_amount'];
        $anamnesis_other_problem = $dt_put['anamnesis_other_problem'];
        $physical_leopold_one = $dt_put['physical_leopold_one'];
        $physical_leopold_two_type_id = $dt_put['physical_leopold_two_type_id'];
        $physical_leopold_three_type_id = $dt_put['physical_leopold_three_type_id'];
        $physical_leopold_three_other = $dt_put['physical_leopold_three_other'];
        $physical_leopold_four_type_id = $dt_put['physical_leopold_four_type_id'];
        $physical_fetal_heart_rate_freq = $dt_put['physical_fetal_heart_rate_freq'];
        $physical_fetal_heart_rate_rhythm_type_id = $dt_put['physical_fetal_heart_rate_rhythm_type_id'];
        $physical_vulva_type_id = $dt_put['physical_vulva_type_id'];
        $physical_vulva_other = $dt_put['physical_vulva_other'];
        $physical_internal_portio_type_id = $dt_put['physical_internal_portio_type_id'];
        $physical_internal_opening_type_id = $dt_put['physical_internal_opening_type_id'];
        $physical_internal_opening_cm = $dt_put['physical_internal_opening_cm'];
        $physical_internal_depletion_percent = $dt_put['physical_internal_depletion_percent'];
        $physical_internal_amniotic_type_id = $dt_put['physical_internal_amniotic_type_id'];
        $physical_internal_amniotic_color = $dt_put['physical_internal_amniotic_color'];
        $physical_internal_presentation_type_id = $dt_put['physical_internal_presentation_type_id'];
        $physical_internal_presentation_face = $dt_put['physical_internal_presentation_face'];
        $physical_internal_presentation_other = $dt_put['physical_internal_presentation_other'];
        $physical_internal_lowest_part_drop_type_id = $dt_put['physical_internal_lowest_part_drop_type_id'];
        $physical_internal_lowest_part_drop_hodge_one = $dt_put['physical_internal_lowest_part_drop_hodge_one'];
        $physical_internal_lowest_part_drop_hodge_two = $dt_put['physical_internal_lowest_part_drop_hodge_two'];
        $physical_internal_lowest_part_drop_hodge_three = $dt_put['physical_internal_lowest_part_drop_hodge_three'];
        $physical_internal_lowest_part_drop_hodge_four = $dt_put['physical_internal_lowest_part_drop_hodge_four'];
        $physical_ctg_baseline = $dt_put['physical_ctg_baseline'];
        $physical_ctg_variability_type_id = $dt_put['physical_ctg_variability_type_id'];
        $physical_ctg_acceleration_type_id = $dt_put['physical_ctg_acceleration_type_id'];
        $physical_ctg_deceleration_type_id = $dt_put['physical_ctg_deceleration_type_id'];
        $physical_ctg_deceleration_type_type_id = $dt_put['physical_ctg_deceleration_type_type_id'];
        $physical_ctg_contractions_type_id = $dt_put['physical_ctg_contractions_type_id'];
        $physical_ctg_contractions_freq = $dt_put['physical_ctg_contractions_freq'];
        $physical_ctg_contractions_duration = $dt_put['physical_ctg_contractions_duration'];
        $physical_ctg_contractions_strength = $dt_put['physical_ctg_contractions_strength'];
        $physical_ctg_fetal_movement = $dt_put['physical_ctg_fetal_movement'];
        $physical_ctg_interpretation = $dt_put['physical_ctg_interpretation'];
        $diagnosis_mother = $dt_put['diagnosis_mother'];
        $diagnosis_baby = $dt_put['diagnosis_baby'];
        $prognosis_mother_type_id = $dt_put['prognosis_mother_type_id'];
        $prognosis_mother_night = $dt_put['prognosis_mother_night'];
        $prognosis_baby_type_id = $dt_put['prognosis_baby_type_id'];
        $prognosis_baby_night = $dt_put['prognosis_baby_night'];
        $plan_type_id = $dt_put['plan_type_id'];
        $obstetric_problems = $dt_put['obstetric_problems'];

        $dt_obgyn_special = array(
            'nurse_obgyn_id'                         => $nurse_obgyn_id,
            'employee_nurse_id'                      => $employee_nurse_id,
            'anamnesis_g'                            => $anamnesis_g,
            'anamnesis_p'                            => $anamnesis_p,
            'anamnesis_a'                            => $anamnesis_a,
            'anamnesis_h'                            => $anamnesis_h,
            'anamnesis_period'                       => $anamnesis_period,
            'anamnesis_antenatal_type_id'            => $anamnesis_antenatal_type_id,
            'anamnesis_disease_pregnancy_arry'       => $anamnesis_disease_pregnancy_arry,
            'anamnesis_disease_pregnancy_other'      => $anamnesis_disease_pregnancy_other,
            'anamnesis_his_type_id'                  => $anamnesis_his_type_id,
            'anamnesis_his_date'                     => $anamnesis_his_date,
            'anamnesis_amniotic_fluid_type_id'       => $anamnesis_amniotic_fluid_type_id,
            'anamnesis_amniotic_fluid_date'          => $anamnesis_amniotic_fluid_date,
            'anamnesis_amniotic_fluid_color_type_id' => $anamnesis_amniotic_fluid_color_type_id,
            'anamnesis_amniotic_fluid_color_other'   => $anamnesis_amniotic_fluid_color_other,
            'anamnesis_blood_slym_type_id'           => $anamnesis_blood_slym_type_id,
            'anamnesis_antepartum_type_id'           => $anamnesis_antepartum_type_id,
            'anamnesis_antepartum_date'              => $anamnesis_antepartum_date,
            'anamnesis_antepartum_amount'            => $anamnesis_antepartum_amount,
            'anamnesis_seizure_type_id'              => $anamnesis_seizure_type_id,
            'anamnesis_seizure_clonic_long'          => $anamnesis_seizure_clonic_long,
            'anamnesis_straining_type_id'            => $anamnesis_straining_type_id,
            'anamnesis_straining_date'               => $anamnesis_straining_date,
            'anamnesis_straining_amount'             => $anamnesis_straining_amount,
            'anamnesis_other_problem'                => $anamnesis_other_problem,
            'physical_leopold_one'                     => $physical_leopold_one,
            'physical_leopold_two_type_id'             => $physical_leopold_two_type_id,
            'physical_leopold_three_type_id'           => $physical_leopold_three_type_id,
            'physical_leopold_three_other'             => $physical_leopold_three_other,
            'physical_leopold_four_type_id'            => $physical_leopold_four_type_id,
            'physical_fetal_heart_rate_freq'           => $physical_fetal_heart_rate_freq,
            'physical_fetal_heart_rate_rhythm_type_id' => $physical_fetal_heart_rate_rhythm_type_id,
            'physical_vulva_type_id'                   => $physical_vulva_type_id,
            'physical_vulva_other'                     => $physical_vulva_other,
            'physical_internal_portio_type_id'         => $physical_internal_portio_type_id,
            'physical_internal_opening_type_id'        => $physical_internal_opening_type_id,
            'physical_internal_opening_cm'             => $physical_internal_opening_cm,
            'physical_internal_depletion_percent'      => $physical_internal_depletion_percent,
            'physical_internal_amniotic_type_id'       => $physical_internal_amniotic_type_id,
            'physical_internal_amniotic_color'         => $physical_internal_amniotic_color,
            'physical_internal_presentation_type_id'   => $physical_internal_presentation_type_id,
            'physical_internal_presentation_face'      => $physical_internal_presentation_face,
            'physical_internal_presentation_other'     => $physical_internal_presentation_other,
            'physical_internal_lowest_part_drop_type_id' => $physical_internal_lowest_part_drop_type_id,
            'physical_internal_lowest_part_drop_hodge_one'   => $physical_internal_lowest_part_drop_hodge_one,
            'physical_internal_lowest_part_drop_hodge_two'   => $physical_internal_lowest_part_drop_hodge_two,
            'physical_internal_lowest_part_drop_hodge_three' => $physical_internal_lowest_part_drop_hodge_three,
            'physical_internal_lowest_part_drop_hodge_four'  => $physical_internal_lowest_part_drop_hodge_four,
            'physical_ctg_baseline'                    => $physical_ctg_baseline,
            'physical_ctg_variability_type_id'         => $physical_ctg_variability_type_id,
            'physical_ctg_acceleration_type_id'        => $physical_ctg_acceleration_type_id,
            'physical_ctg_deceleration_type_id'        => $physical_ctg_deceleration_type_id,
            'physical_ctg_deceleration_type_type_id'   => $physical_ctg_deceleration_type_type_id,
            'physical_ctg_contractions_type_id'        => $physical_ctg_contractions_type_id,
            'physical_ctg_contractions_freq'           => $physical_ctg_contractions_freq,
            'physical_ctg_contractions_duration'       => $physical_ctg_contractions_duration,
            'physical_ctg_contractions_strength'       => $physical_ctg_contractions_strength,
            'physical_ctg_fetal_movement'              => $physical_ctg_fetal_movement,
            'physical_ctg_interpretation'              => $physical_ctg_interpretation,
            'diagnosis_mother'                         => $diagnosis_mother,
            'diagnosis_baby'                           => $diagnosis_baby,
            'prognosis_mother_type_id'                 => $prognosis_mother_type_id,
            'prognosis_mother_night'                   => $prognosis_mother_night,
            'prognosis_baby_type_id'                   => $prognosis_baby_type_id,
            'prognosis_baby_night'                     => $prognosis_baby_night,
            'plan_type_id'                             => $plan_type_id,
            'obstetric_problems'                       => $obstetric_problems,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_obgyn_id' => $nurse_obgyn_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn_special = $this->Nurse_obgyn_special->select(0, $select, $where);        
        if (count($nurse_obgyn_special) == 0) {
            $this->Nurse_obgyn_special->create($dt_obgyn_special);
        } else {
            $nurse_obgyn_special_id = $nurse_obgyn_special[0]->id;
            unset($dt_obgyn_special['user_added']);
            unset($dt_obgyn_special['date_added']);
            $dt_obgyn_special['user_updated'] = $user_id;
            $st_obgyn = $this->Nurse_obgyn_special->update_v2($nurse_obgyn_special_id, $dt_obgyn_special);
            if ($st_obgyn < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save obgyn special', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_obgyn_anamnesis_put() {
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
        $this->form_validation->set_rules($this->save_obgyn_anamnesis_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // NURSE OBGYN
        $dt_nurse_obgyn = array(
            'nurse_id' => $nurse_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn = $this->Nurse_obgyn->select(0, $select, $where);        
        if (count($nurse_obgyn) == 0) {
            $nurse_obgyn_id = $this->Nurse_obgyn->create($dt_nurse_obgyn);
        } else {
            $nurse_obgyn_id = $nurse_obgyn[0]->id;
        }

        // OBGYN ANAMNESIS
        $anamnesis_g = $dt_put['anamnesis_g'];
        $anamnesis_p = $dt_put['anamnesis_p'];
        $anamnesis_a = $dt_put['anamnesis_a'];
        $anamnesis_h = $dt_put['anamnesis_h'];
        $anamnesis_period = $dt_put['anamnesis_period'];
        $anamnesis_antenatal_type_id = $dt_put['anamnesis_antenatal_type_id'];
        $anamnesis_disease_pregnancy_type_id = $dt_put['anamnesis_disease_pregnancy_type_id'];
        $anamnesis_disease_pregnancy_other = $dt_put['anamnesis_disease_pregnancy_other'];
        $anamnesis_his_type_id = $dt_put['anamnesis_his_type_id'];
        $anamnesis_his_date = $dt_put['anamnesis_his_date'];
        $anamnesis_his_hour = $dt_put['anamnesis_his_hour'];
        $anamnesis_amniotic_fluid_type_id = $dt_put['anamnesis_amniotic_fluid_type_id'];
        $anamnesis_amniotic_fluid_date = $dt_put['anamnesis_amniotic_fluid_date'];
        $anamnesis_amniotic_fluid_hour = $dt_put['anamnesis_amniotic_fluid_hour'];
        $anamnesis_amniotic_fluid_color_type_id = $dt_put['anamnesis_amniotic_fluid_color_type_id'];
        $anamnesis_amniotic_fluid_color_other = $dt_put['anamnesis_amniotic_fluid_color_other'];
        $anamnesis_blood_slym_type_id = $dt_put['anamnesis_blood_slym_type_id'];
        $anamnesis_antepartum_type_id = $dt_put['anamnesis_antepartum_type_id'];
        $anamnesis_antepartum_date = $dt_put['anamnesis_antepartum_date'];
        $anamnesis_antepartum_hour = $dt_put['anamnesis_antepartum_hour'];
        $anamnesis_antepartum_amount = $dt_put['anamnesis_antepartum_amount'];
        $anamnesis_seizure_type_id = $dt_put['anamnesis_seizure_type_id'];
        $anamnesis_seizure_clonic_long = $dt_put['anamnesis_seizure_clonic_long'];
        $anamnesis_straining_type_id = $dt_put['anamnesis_straining_type_id'];
        $anamnesis_straining_date = $dt_put['anamnesis_straining_date'];
        $anamnesis_straining_hour = $dt_put['anamnesis_straining_hour'];
        $anamnesis_straining_amount = $dt_put['anamnesis_straining_amount'];
        $anamnesis_other_problem = $dt_put['anamnesis_other_problem'];        

        $dt_obgyn_anamnesis = array(
            'nurse_obgyn_id'                         => $nurse_obgyn_id,
            'employee_nurse_id'                      => $employee_nurse_id,
            'anamnesis_g'                            => $anamnesis_g,
            'anamnesis_p'                            => $anamnesis_p,
            'anamnesis_a'                            => $anamnesis_a,
            'anamnesis_h'                            => $anamnesis_h,
            'anamnesis_period'                       => $anamnesis_period,
            'anamnesis_antenatal_type_id'            => $anamnesis_antenatal_type_id,
            'anamnesis_disease_pregnancy_type_id'    => $anamnesis_disease_pregnancy_type_id,
            'anamnesis_disease_pregnancy_other'      => $anamnesis_disease_pregnancy_other,
            'anamnesis_his_type_id'                  => $anamnesis_his_type_id,
            'anamnesis_his_date'                     => $anamnesis_his_date,
            'anamnesis_his_hour'                     => $anamnesis_his_hour,
            'anamnesis_amniotic_fluid_type_id'       => $anamnesis_amniotic_fluid_type_id,
            'anamnesis_amniotic_fluid_date'          => $anamnesis_amniotic_fluid_date,
            'anamnesis_amniotic_fluid_hour'          => $anamnesis_amniotic_fluid_hour,
            'anamnesis_amniotic_fluid_color_type_id' => $anamnesis_amniotic_fluid_color_type_id,
            'anamnesis_amniotic_fluid_color_other'   => $anamnesis_amniotic_fluid_color_other,
            'anamnesis_blood_slym_type_id'           => $anamnesis_blood_slym_type_id,
            'anamnesis_antepartum_type_id'           => $anamnesis_antepartum_type_id,
            'anamnesis_antepartum_date'              => $anamnesis_antepartum_date,
            'anamnesis_antepartum_hour'              => $anamnesis_antepartum_hour,
            'anamnesis_antepartum_amount'            => $anamnesis_antepartum_amount,
            'anamnesis_seizure_type_id'              => $anamnesis_seizure_type_id,
            'anamnesis_seizure_clonic_long'          => $anamnesis_seizure_clonic_long,
            'anamnesis_straining_type_id'            => $anamnesis_straining_type_id,
            'anamnesis_straining_date'               => $anamnesis_straining_date,
            'anamnesis_straining_hour'               => $anamnesis_straining_hour,
            'anamnesis_straining_amount'             => $anamnesis_straining_amount,
            'anamnesis_other_problem'                => $anamnesis_other_problem,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_obgyn_id' => $nurse_obgyn_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn_special = $this->Nurse_obgyn_special->select(0, $select, $where);        
        if (count($nurse_obgyn_special) == 0) {
            $this->Nurse_obgyn_special->create($dt_obgyn_anamnesis);
        } else {
            $nurse_obgyn_special_id = $nurse_obgyn_special[0]->id;
            unset($dt_obgyn_anamnesis['user_added']);
            unset($dt_obgyn_anamnesis['date_added']);
            $dt_obgyn_anamnesis['user_updated'] = $user_id;
            $st_obgyn = $this->Nurse_obgyn_special->update_v2($nurse_obgyn_special_id, $dt_obgyn_anamnesis);
            if ($st_obgyn < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save obgyn anamnesis', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_obgyn_physical_put() {
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
        $this->form_validation->set_rules($this->save_obgyn_physical_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // NURSE OBGYN
        $dt_nurse_obgyn = array(
            'nurse_id' => $nurse_id,
            'user_added' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn = $this->Nurse_obgyn->select(0, $select, $where);        
        if (count($nurse_obgyn) == 0) {
            $nurse_obgyn_id = $this->Nurse_obgyn->create($dt_nurse_obgyn);
        } else {
            $nurse_obgyn_id = $nurse_obgyn[0]->id;
        }

        // OBGYN PHYSICAL
        $physical_leopold_one = $dt_put['physical_leopold_one'];
        $physical_leopold_two_type_id = $dt_put['physical_leopold_two_type_id'];
        $physical_leopold_three_type_id = $dt_put['physical_leopold_three_type_id'];
        $physical_leopold_three_other = $dt_put['physical_leopold_three_other'];
        $physical_leopold_four_type_id = $dt_put['physical_leopold_four_type_id'];
        $physical_fetal_heart_rate_freq = $dt_put['physical_fetal_heart_rate_freq'];
        $physical_fetal_heart_rate_rhythm_type_id = $dt_put['physical_fetal_heart_rate_rhythm_type_id'];
        $physical_vulva_type_id = $dt_put['physical_vulva_type_id'];
        $physical_vulva_other = $dt_put['physical_vulva_other'];
        $physical_internal_portio_type_id = $dt_put['physical_internal_portio_type_id'];
        $physical_internal_opening_type_id = $dt_put['physical_internal_opening_type_id'];
        $physical_internal_opening_cm = $dt_put['physical_internal_opening_cm'];
        $physical_internal_depletion_percent = $dt_put['physical_internal_depletion_percent'];
        $physical_internal_amniotic_type_id = $dt_put['physical_internal_amniotic_type_id'];
        $physical_internal_amniotic_color = $dt_put['physical_internal_amniotic_color'];
        $physical_internal_presentation_type_id = $dt_put['physical_internal_presentation_type_id'];
        $physical_internal_presentation_other = $dt_put['physical_internal_presentation_other'];
        $physical_ctg_baseline = $dt_put['physical_ctg_baseline'];
        $physical_ctg_variability_type_id = $dt_put['physical_ctg_variability_type_id'];
        $physical_ctg_acceleration_type_id = $dt_put['physical_ctg_acceleration_type_id'];
        $physical_ctg_deceleration_type_id = $dt_put['physical_ctg_deceleration_type_id'];
        $physical_ctg_contractions_type_id = $dt_put['physical_ctg_contractions_type_id'];
        $physical_ctg_contractions_freq = $dt_put['physical_ctg_contractions_freq'];
        $physical_ctg_contractions_duration = $dt_put['physical_ctg_contractions_duration'];
        $physical_ctg_contractions_strength = $dt_put['physical_ctg_contractions_strength'];
        $physical_ctg_fetal_movement = $dt_put['physical_ctg_fetal_movement'];
        $physical_ctg_interpretation = $dt_put['physical_ctg_interpretation'];

        $dt_obgyn_physical = array(
            'nurse_obgyn_id'                         => $nurse_obgyn_id,
            'employee_nurse_id'                      => $employee_nurse_id,
            'physical_leopold_one'                     => $physical_leopold_one,
            'physical_leopold_two_type_id'             => $physical_leopold_two_type_id,
            'physical_leopold_three_type_id'           => $physical_leopold_three_type_id,
            'physical_leopold_three_other'             => $physical_leopold_three_other,
            'physical_leopold_four_type_id'            => $physical_leopold_four_type_id,
            'physical_fetal_heart_rate_freq'           => $physical_fetal_heart_rate_freq,
            'physical_fetal_heart_rate_rhythm_type_id' => $physical_fetal_heart_rate_rhythm_type_id,
            'physical_vulva_type_id'                   => $physical_vulva_type_id,
            'physical_vulva_other'                     => $physical_vulva_other,
            'physical_internal_portio_type_id'         => $physical_internal_portio_type_id,
            'physical_internal_opening_type_id'        => $physical_internal_opening_type_id,
            'physical_internal_opening_cm'             => $physical_internal_opening_cm,
            'physical_internal_depletion_percent'      => $physical_internal_depletion_percent,
            'physical_internal_amniotic_type_id'       => $physical_internal_amniotic_type_id,
            'physical_internal_amniotic_color'         => $physical_internal_amniotic_color,
            'physical_internal_presentation_type_id'   => $physical_internal_presentation_type_id,
            'physical_internal_presentation_other'     => $physical_internal_presentation_other,
            'physical_ctg_baseline'                    => $physical_ctg_baseline,
            'physical_ctg_variability_type_id'         => $physical_ctg_variability_type_id,
            'physical_ctg_acceleration_type_id'        => $physical_ctg_acceleration_type_id,
            'physical_ctg_deceleration_type_id'        => $physical_ctg_deceleration_type_id,
            'physical_ctg_contractions_type_id'        => $physical_ctg_contractions_type_id,
            'physical_ctg_contractions_freq'           => $physical_ctg_contractions_freq,
            'physical_ctg_contractions_duration'       => $physical_ctg_contractions_duration,
            'physical_ctg_contractions_strength'       => $physical_ctg_contractions_strength,
            'physical_ctg_fetal_movement'              => $physical_ctg_fetal_movement,
            'physical_ctg_interpretation'              => $physical_ctg_interpretation,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_obgyn_id' => $nurse_obgyn_id,
            'a.is_delete' => 0,
        );
        $nurse_obgyn_special = $this->Nurse_obgyn_special->select(0, $select, $where);        
        if (count($nurse_obgyn_special) == 0) {
            $this->Nurse_obgyn_special->create($dt_obgyn_physical);
        } else {
            $nurse_obgyn_special_id = $nurse_obgyn_special[0]->id;
            unset($dt_obgyn_physical['user_added']);
            unset($dt_obgyn_physical['date_added']);
            $dt_obgyn_physical['user_updated'] = $user_id;
            $st_obgyn = $this->Nurse_obgyn_special->update_v2($nurse_obgyn_special_id, $dt_obgyn_physical);
            if ($st_obgyn < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save obgyn physical', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_newborn_baby_put() {
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
        $this->form_validation->set_rules($this->save_neonate_inpatient_rules);
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
        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $type_nurse_id = $dt_put['type_nurse_id'];

        $select = 'a.id';
        $where = array();
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select, $where);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // NURSE
        $dt_nurse = array(
            'registration_destination_id' => $registration_destination_id,
            'employee_nurse_id' => $employee_nurse_id,
            'type_nurse_id' => $type_nurse_id,
            'date_added' => date("Y-m-d H:i:s",time()),
        );
        
        if ($id == null) {
            $nurse_id = $this->$model->create($dt_nurse);
        } else {
            $nurse_id = $id;
            $select = 'a.id';
            $nurse = $this->$model->select($nurse_id, $select);
            if (count($nurse) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }
        }

        // NEWBORN BABY
        $doctor_id = $dt_put['doctor_id'] ?? null;
        $midwife_id = $dt_put['midwife_id'] ?? null;

        if ($doctor_id != null) {
            $select = 'a.id';
            $employee = $this->Employee->select($doctor_id, $select);
            if (count($employee) == 0) {
                $this->REST_Return(404, 'there is no doctor with id ' . $doctor_id);
                return;
            }
        } else {
            $doctor_id = null;
        }

        if ($midwife_id != null) {
            $select = 'a.id';
            $employee_2 = $this->Employee->select($midwife_id, $select);
            if (count($employee_2) == 0) {
                $this->REST_Return(404, 'there is no midwife with id ' . $midwife_id);
                return;
            }
        } else {
            $midwife_id = null;
        }

        $birth_hour = $dt_put['birth_hour'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $apgar_score_heart_rate_i = $dt_put['apgar_score_heart_rate_i'];
        $apgar_score_respiratory_i = $dt_put['apgar_score_respiratory_i'];
        $apgar_score_muscle_tone_i = $dt_put['apgar_score_muscle_tone_i'];
        $apgar_score_reflex_i = $dt_put['apgar_score_reflex_i'];
        $apgar_score_skin_color_i = $dt_put['apgar_score_skin_color_i'];
        $apgar_score_total_i = $dt_put['apgar_score_total_i'];
        $apgar_score_heart_rate_ii = $dt_put['apgar_score_heart_rate_ii'];
        $apgar_score_respiratory_ii = $dt_put['apgar_score_respiratory_ii'];
        $apgar_score_muscle_tone_ii = $dt_put['apgar_score_muscle_tone_ii'];
        $apgar_score_reflex_ii = $dt_put['apgar_score_reflex_ii'];
        $apgar_score_skin_color_ii = $dt_put['apgar_score_skin_color_ii'];
        $apgar_score_total_ii = $dt_put['apgar_score_total_ii'];
        $conclution = $dt_put['conclution'];
        $external_genitalia = $dt_put['external_genitalia'];
        $anus = $dt_put['anus'];
        $meconium = $dt_put['meconium'];
        $miksi = $dt_put['miksi'];
        $vernix = $dt_put['vernix'];
        $caput_succedaneum_type_id = $dt_put['caput_succedaneum_type_id'];
        $cephalohematoma_type_id = $dt_put['cephalohematoma_type_id'];
        $head_type_id = $dt_put['head_type_id'];
        $suture_type_id = $dt_put['suture_type_id'];
        $large_fontanel = $dt_put['large_fontanel'];
        $small_fontanel = $dt_put['small_fontanel'];
        $died_in_pregnancy_type_id = $dt_put['died_in_pregnancy_type_id'];
        $died_in_childbirth_type_id = $dt_put['died_in_childbirth_type_id'];
        $died_after_birth_type_id = $dt_put['died_after_birth_type_id'];
        $congenital_disorder = $dt_put['congenital_disorder'];
        $baby_trauma = $dt_put['baby_trauma'];
        $hfb_mucus_suction_type_id = $dt_put['hfb_mucus_suction_type_id'];
        $hfb_stimulus_type_id = $dt_put['hfb_stimulus_type_id'];
        $hfb_ambu_type_id = $dt_put['hfb_ambu_type_id'];
        $hfb_ambu_long = $dt_put['hfb_ambu_long'];
        $hfb_heart_massage_type_id = $dt_put['hfb_heart_massage_type_id'];
        $hfb_heart_massage_long = $dt_put['hfb_heart_massage_long'];
        $hfb_endotracheal_tube_type_id = $dt_put['hfb_endotracheal_tube_type_id'];
        $hfb_endotracheal_tube_number = $dt_put['hfb_endotracheal_tube_number'];
        $hfb_endotracheal_tube_size = $dt_put['hfb_endotracheal_tube_size'];
        $hfb_oxygen_type_id = $dt_put['hfb_oxygen_type_id'];
        $hfb_resuscitation_type_id = $dt_put['hfb_resuscitation_type_id'];
        $hfb_umbilical_cord_wrapped = $dt_put['hfb_umbilical_cord_wrapped'];
        $hfb_eye_application = $dt_put['hfb_eye_application'];
        $hfb_umbilical_cord_application = $dt_put['hfb_umbilical_cord_application'];
        $hfb_given_medicines = $dt_put['hfb_given_medicines'];
        $hfb_head_size_bitemporalis = $dt_put['hfb_head_size_bitemporalis'];
        $hfb_head_size_biparietalis = $dt_put['hfb_head_size_biparietalis'];
        $hfb_head_size_fronto_occipitalis = $dt_put['hfb_head_size_fronto_occipitalis'];
        $hfb_head_size_mento_occipitalis = $dt_put['hfb_head_size_mento_occipitalis'];
        $hfb_head_size_suboccipito_bregmatica = $dt_put['hfb_head_size_suboccipito_bregmatica'];
        $hfb_circumference_suboccipito_bregmatica = $dt_put['hfb_circumference_suboccipito_bregmatica'];
        $hfb_circumference_fronto_occipitalis = $dt_put['hfb_circumference_fronto_occipitalis'];
        $hfb_circumference_mento_occipitalis = $dt_put['hfb_circumference_mento_occipitalis'];
        $hfb_chest_circumference = $dt_put['hfb_chest_circumference'];
        $hfb_upper_arm_circumference = $dt_put['hfb_upper_arm_circumference'];
        $hfb_delivery_type_id = $dt_put['hfb_delivery_type_id'];
        $hfb_delivery_other = $dt_put['hfb_delivery_other'];
        $hfb_assisted_by = $dt_put['hfb_assisted_by'];
        $hfb_newborn_baby_type_id = $dt_put['hfb_newborn_baby_type_id'];
        $hfb_baby_condition_type_id = $dt_put['hfb_baby_condition_type_id'];
        $gestational_age = $dt_put['gestational_age'];
        $water_breaks_hour = $dt_put['water_breaks_hour'];
        $water_breaks_color = $dt_put['water_breaks_color'];
        $umbilical_cord_entanglement_type_id = $dt_put['umbilical_cord_entanglement_type_id'];
        $special_note = $dt_put['special_note'];
        $transferred_date = $dt_put['transferred_date'];
        $birth_order = $dt_put['birth_order'];
        $complication = $dt_put['complication'];
        $birth_date = $dt_put['birth_date'];
        $baby_name = $dt_put['baby_name'];

        $dt_newborn_baby = array(
            'registration_destination_id'              => $registration_destination_id,
            'nurse_id'                                 => $nurse_id,
            'doctor_id'                                => $doctor_id,
            'midwife_id'                               => $midwife_id,
            'birth_hour'                               => $birth_hour,
            'weight'                                   => $weight,
            'height'                                   => $height,
            'apgar_score_heart_rate_i'                 => $apgar_score_heart_rate_i,
            'apgar_score_respiratory_i'                => $apgar_score_respiratory_i,
            'apgar_score_muscle_tone_i'                => $apgar_score_muscle_tone_i,
            'apgar_score_reflex_i'                     => $apgar_score_reflex_i,
            'apgar_score_skin_color_i'                 => $apgar_score_skin_color_i,
            'apgar_score_total_i'                      => $apgar_score_total_i,
            'apgar_score_heart_rate_ii'                => $apgar_score_heart_rate_ii,
            'apgar_score_respiratory_ii'               => $apgar_score_respiratory_ii,
            'apgar_score_muscle_tone_ii'               => $apgar_score_muscle_tone_ii,
            'apgar_score_reflex_ii'                    => $apgar_score_reflex_ii,
            'apgar_score_skin_color_ii'                => $apgar_score_skin_color_ii,
            'apgar_score_total_ii'                     => $apgar_score_total_ii,
            'conclution'                               => $conclution,
            'external_genitalia'                       => $external_genitalia,
            'anus'                                     => $anus,
            'meconium'                                 => $meconium,
            'miksi'                                    => $miksi,
            'vernix'                                   => $vernix,
            'caput_succedaneum_type_id'                => $caput_succedaneum_type_id,
            'cephalohematoma_type_id'                  => $cephalohematoma_type_id,
            'head_type_id'                             => $head_type_id,
            'suture_type_id'                           => $suture_type_id,
            'large_fontanel'                           => $large_fontanel,
            'small_fontanel'                           => $small_fontanel,
            'died_in_pregnancy_type_id'                => $died_in_pregnancy_type_id,
            'died_in_childbirth_type_id'               => $died_in_childbirth_type_id,
            'died_after_birth_type_id'                 => $died_after_birth_type_id,
            'congenital_disorder'                      => $congenital_disorder,
            'baby_trauma'                              => $baby_trauma,
            'hfb_mucus_suction_type_id'                => $hfb_mucus_suction_type_id,
            'hfb_stimulus_type_id'                     => $hfb_stimulus_type_id,
            'hfb_ambu_type_id'                         => $hfb_ambu_type_id,
            'hfb_ambu_long'                            => $hfb_ambu_long,
            'hfb_heart_massage_type_id'                => $hfb_heart_massage_type_id,
            'hfb_heart_massage_long'                   => $hfb_heart_massage_long,
            'hfb_endotracheal_tube_type_id'            => $hfb_endotracheal_tube_type_id,
            'hfb_endotracheal_tube_number'             => $hfb_endotracheal_tube_number,
            'hfb_endotracheal_tube_size'               => $hfb_endotracheal_tube_size,
            'hfb_oxygen_type_id'                       => $hfb_oxygen_type_id,
            'hfb_resuscitation_type_id'                => $hfb_resuscitation_type_id,
            'hfb_umbilical_cord_wrapped'               => $hfb_umbilical_cord_wrapped,
            'hfb_eye_application'                      => $hfb_eye_application,
            'hfb_umbilical_cord_application'           => $hfb_umbilical_cord_application,
            'hfb_given_medicines'                      => $hfb_given_medicines,
            'hfb_head_size_bitemporalis'               => $hfb_head_size_bitemporalis,
            'hfb_head_size_biparietalis'               => $hfb_head_size_biparietalis,
            'hfb_head_size_fronto_occipitalis'         => $hfb_head_size_fronto_occipitalis,
            'hfb_head_size_mento_occipitalis'          => $hfb_head_size_mento_occipitalis,
            'hfb_head_size_suboccipito_bregmatica'     => $hfb_head_size_suboccipito_bregmatica,
            'hfb_circumference_suboccipito_bregmatica' => $hfb_circumference_suboccipito_bregmatica,
            'hfb_circumference_fronto_occipitalis'     => $hfb_circumference_fronto_occipitalis,
            'hfb_circumference_mento_occipitalis'      => $hfb_circumference_mento_occipitalis,
            'hfb_chest_circumference'                  => $hfb_chest_circumference,
            'hfb_upper_arm_circumference'              => $hfb_upper_arm_circumference,
            'hfb_delivery_type_id'                     => $hfb_delivery_type_id,
            'hfb_delivery_other'                       => $hfb_delivery_other,
            'hfb_assisted_by'                          => $hfb_assisted_by,
            'hfb_newborn_baby_type_id'                 => $hfb_newborn_baby_type_id,
            'hfb_baby_condition_type_id'               => $hfb_baby_condition_type_id,
            'gestational_age'                          => $gestational_age,
            'water_breaks_hour'                        => $water_breaks_hour,
            'water_breaks_color'                       => $water_breaks_color,
            'umbilical_cord_entanglement_type_id'      => $umbilical_cord_entanglement_type_id,
            'special_note'                             => $special_note,
            'transferred_date'                         => $transferred_date,
            'birth_order'                              => $birth_order,
            'complication'                             => $complication,
            'birth_date'                               => $birth_date,
            'baby_name'                               => $baby_name,
            'user_added'                             => $user_id,
            'user_updated'                           => $user_id,
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );

        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id,
            'a.is_delete' => 0,
        );
        $newborn_baby = $this->Newborn_baby->select(0, $select, $where);        
        if (count($newborn_baby) == 0) {
            $this->Newborn_baby->create($dt_newborn_baby);
        } else {
            $newborn_baby_id = $newborn_baby[0]->id;
            unset($dt_newborn_baby['user_added']);
            unset($dt_newborn_baby['date_added']);
            $dt_newborn_baby['user_updated'] = $user_id;
            $st_newborn_baby = $this->Newborn_baby->update_v2($newborn_baby_id, $dt_newborn_baby);
            if ($st_newborn_baby < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // create letter certificate new born baby 
        
        $select = 'a.id';
        $where = array(
            'a.nurse_id' => $nurse_id
        );
        $letter = $this->Letter->select(0, $select, $where);

        // add new row if not exits
        if (count($letter) == 0) {
            $codecounter = array(
                'var_1' => 'RM-SKL',
                'var_2' => $this->numberToRomanRepresentation(date('m')),
                'var_3' => date('Y'),
                'var_5' => date('Y-m')
            );
            $this->load->library("transaction");
            $checkcounter = $this->transaction->counter($codecounter);
            $no_letter = $checkcounter['counter_queue'].'/'.$codecounter['var_1'].'/'.$codecounter['var_2'].'/'.$codecounter['var_3'];
            if ($checkcounter['counter_queue'] == '0') {
                $this->Counter_queue->replace(array(
                    'var_1' => 'RM-SKL',
                    'var_2' => $this->numberToRomanRepresentation(date('m')),
                    'var_3' => date('Y'),
                    'var_5' => date('Y-m'),
                    'counter_queue' => 0
                ));
                $checkcounter = $this->transaction->counter($codecounter);
                $no_letter = $checkcounter['counter_queue'].'/'.$codecounter['var_1'].'/'.$codecounter['var_2'].'/'.$codecounter['var_3'];
            }

            $dt_letter = array(
                'registration_destination_id'   => $registration_destination_id,
                'no'                            => $no_letter,
                'nurse_id'                      => $nurse_id,
                'letter_type_id'                => 'c2ed646e-e24a-496a-b333-67ea16e3cab4',
                'date'                          => date('Y-m-d'),
                'start'                         => date('Y-m-d'),
                'end'                           => date('Y-m-d')
            );
            $this->Letter->create($dt_letter);
        }

        // create letter certificate new born baby 

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_newborn_baby,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save newborn baby', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $nurse_id
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
            $this->REST_Return(400, 'nurse_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_id = $this->$model->select_id($id);

        if ($nurse_id == "") {
            $this->REST_Return(404, 'there is no nurse with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_nurse = array(
            'is_delete' => $is_delete,
        );

        $this->$model->update_v2($nurse_id, $dt_nurse);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nurse', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
    function numberToRomanRepresentation($number) {
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }
}