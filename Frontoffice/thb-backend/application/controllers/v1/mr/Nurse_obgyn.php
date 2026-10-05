<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_obgyn extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");

        $this->create_rules = array(
            array('field' => 'nurse_id', 'label' => 'nurse_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'nurse_id', 'label' => 'nurse_id', 'rules' => 'trim'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Nurse_obgyn API
     * --------------------
     * @param: nurse_id
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_obgyn
     **/

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
    
        $nurse_id = $dt_post['nurse_id'];

        $select = 'a.id';
        $nurse = $this->Nurse->select($nurse_id, $select);

        if (count($nurse) == 0) {
            $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
            return;
        }
    
        $history_disease_type_id = $dt_post['history_disease_type_id'];
        $history_disease = $dt_post['history_disease'];
        $been_hospitalized_type_id = $dt_post['been_hospitalized_type_id'];
        $been_hospitalized_answer = $dt_post['been_hospitalized_answer'];
        $been_hospitalized_date = $dt_post['been_hospitalized_date'];
        $been_hospitalized_place = $dt_post['been_hospitalized_place'];
        $any_surgery_type_id = $dt_post['any_surgery_type_id'];
        $any_surgery_date = $dt_post['any_surgery_date'];
        $any_surgery_place = $dt_post['any_surgery_place'];
        $under_treatment_type_id = $dt_post['under_treatment_type_id'];
        $under_treatment = $dt_post['under_treatment'];
        $any_history_family_disease_type_id = $dt_post['any_history_family_disease_type_id'];
        $any_history_family_answer_arry = implode(',', $dt_post['any_history_family_answer_arry']);
        $any_history_family_answer = $dt_post['any_history_family_answer'];
        $dependence_substance_type_id = $dt_post['dependence_substance_type_id'];
        $dependence_substance_arry = implode(',', $dt_post['dependence_substance_arry']);
        $dependence_substance = $dt_post['dependence_substance'];
        $employment_history_type_id = $dt_post['employment_history_type_id'];
        $employment_history = $dt_post['employment_history'];
        $allergy_history_type_id = $dt_post['allergy_history_type_id'];
        $allergy_history_arry = implode(',', $dt_post['allergy_history_arry']);
        $allergy_history_medicine = $dt_post['allergy_history_medicine'];
        $allergy_history_food = $dt_post['allergy_history_food'];
        $allergy_history_other = $dt_post['allergy_history_other'];
        $marriage_history_type_id = $dt_post['marriage_history_type_id'];
        $marriage_history = $dt_post['marriage_history'];
        $contraception_use_type_id = $dt_post['contraception_use_type_id'];
        $contraception_use = $dt_post['contraception_use'];
        $contraception_length_of_use = $dt_post['contraception_length_of_use'];
        $contraception_use_complaint = $dt_post['contraception_use_complaint'];
        $age_first_marriage = $dt_post['age_first_marriage'];
        $marriage_one = $dt_post['marriage_one'];
        $marriage_two = $dt_post['marriage_two'];
        $marriage_three = $dt_post['marriage_three'];
        $period_menarche_age = $dt_post['period_menarche_age'];
        $period_cycle = $dt_post['period_cycle'];
        $period_volume = $dt_post['period_volume'];
        $period_regular_type_id = $dt_post['period_regular_type_id'];
        $period_regular = $dt_post['period_regular'];
        $period_complaint_type_id = $dt_post['period_complaint_type_id'];
        $period_complaint = $dt_post['period_complaint'];
        $hpht = $dt_post['hpht'];
        $estimated_delivery = $dt_post['estimated_delivery'];
        $gestational_age = $dt_post['gestational_age'];
        $gynecological_disease_type_id = $dt_post['gynecological_disease_type_id'];
        $gynecological_disease_arry = implode(',', $dt_post['gynecological_disease_arry']);
        $gynecological_disease = $dt_post['gynecological_disease'];
        $speech_disorders_type_id = $dt_post['speech_disorders_type_id'];
        $speech_disorders = $dt_post['speech_disorders'];
        $translator_type_id = $dt_post['translator_type_id'];
        $translator = $dt_post['translator'];
        $sign_language_type_id = $dt_post['sign_language_type_id'];
        $risk_injury_a_type_id = $dt_post['risk_injury_a_type_id'];
        $risk_injury_b_type_id = $dt_post['risk_injury_b_type_id'];
        $functional_status_type_id = $dt_post['functional_status_type_id'];
        $functional_status = $dt_post['functional_status'];
        $aids = $dt_post['aids'];
        $tell_doctor_risk_type_id = $dt_post['tell_doctor_risk_type_id'];
        $tell_doctor_risk_date = $dt_post['tell_doctor_risk_date'];
        $risk_injury_result = $dt_post['risk_injury_result'];
        $sore_complaints_type_id = $dt_post['sore_complaints_type_id'];
        $sore_complaints = $dt_post['sore_complaints'];
        $sore_goes_away_arry = implode(',', $dt_post['sore_goes_away_arry']);
        $sore_goes_away = $dt_post['sore_goes_away'];
        $sore_method_type_id = $dt_post['sore_method_type_id'];
        $sore_method_score = $dt_post['sore_method_score'];
        $sore_method_arry = implode(',', $dt_post['sore_method_arry']);
        $tell_doctor_sore_type_id = $dt_post['tell_doctor_sore_type_id'];
        $tell_doctor_sore_date = $dt_post['tell_doctor_sore_date'];
        $face_score = $dt_post['face_score'];
        $legs_score = $dt_post['legs_score'];
        $activity_score = $dt_post['activity_score'];
        $cry_score = $dt_post['cry_score'];
        $consolability_score = $dt_post['consolability_score'];
        $sore_total_score = $dt_post['sore_total_score'];
        $duration = $dt_post['duration'];
        $location = $dt_post['location'];
        $response = $dt_post['response'];
        $frequency = $dt_post['frequency'];
        $nutrition_arry = implode(',', $dt_post['nutrition_arry']);
        $nutrition_one = $dt_post['nutrition_one'];
        $nutrition_two = $dt_post['nutrition_two'];
        $nutrition_three = $dt_post['nutrition_three'];
        $nutrition_four = $dt_post['nutrition_four'];
        $nutrition_date = $dt_post['nutrition_date'];
        $nutrition_total_score = $dt_post['nutrition_total_score'];
        $patient_special_diagnose_type_id = $dt_post['patient_special_diagnose_type_id'];
        $patient_special_diagnose_arry = implode(',', $dt_post['patient_special_diagnose_arry']);
        $patient_special_diagnose = $dt_post['patient_special_diagnose'];
        $midwifery_type_id = $dt_post['midwifery_type_id'];
        $midwifery_problem = $dt_post['midwifery_problem'];
        $midwifery_goals = $dt_post['midwifery_goals'];
        $medical_devices_type_id = $dt_post['medical_devices_type_id'];
        $medical_devices_raw = $dt_post['medical_devices'];
        $care_plan_diet_nutrition_type_id = $dt_post['care_plan_diet_nutrition_type_id'];
        $care_plan_diet_nutrition = $dt_post['care_plan_diet_nutrition'];
        $care_plan_rehabilitation_type_id = $dt_post['care_plan_rehabilitation_type_id'];
        $care_plan_rehabilitation = $dt_post['care_plan_rehabilitation'];
        $care_plan_wound_type_id = $dt_post['care_plan_wound_type_id'];
        $care_plan_wound = $dt_post['care_plan_wound'];
        $care_plan_pain_management_type_id = $dt_post['care_plan_pain_management_type_id'];
        $care_plan_pain_management = $dt_post['care_plan_pain_management'];
        $care_plan_other = $dt_post['care_plan_other'];
        $evaluation_habits_arry = implode(',', $dt_post['evaluation_habits_arry']);
        $evaluation_habits_medicine_type_id = $dt_post['evaluation_habits_medicine_type_id'];
        $evaluation_habits_medicine_other = $dt_post['evaluation_habits_medicine_other'];
        $evaluation_habits_jamu_type_id = $dt_post['evaluation_habits_jamu_type_id'];
        $evaluation_habits_jamu = $dt_post['evaluation_habits_jamu'];
        $evaluation_habits_alcohol_type_id = $dt_post['evaluation_habits_alcohol_type_id'];
        $evaluation_habits_alcohol = $dt_post['evaluation_habits_alcohol'];
        $evaluation_habits_smoke_type_id = $dt_post['evaluation_habits_smoke_type_id'];
        $evaluation_habits_smoke = $dt_post['evaluation_habits_smoke'];
        $evaluation_habits_smoke_amount = $dt_post['evaluation_habits_smoke_amount'];
        $evaluation_rest_type_id = $dt_post['evaluation_rest_type_id'];
        $evaluation_daily_type_id = $dt_post['evaluation_daily_type_id'];
        $evaluation_activity_type_id = $dt_post['evaluation_activity_type_id'];
        $evaluation_extremities_top_type_id = $dt_post['evaluation_extremities_top_type_id'];
        $evaluation_grip_type_id = $dt_post['evaluation_grip_type_id'];
        $evaluation_grip_other = $dt_post['evaluation_grip_other'];
        $evaluation_extremities_bottom_type_id = $dt_post['evaluation_extremities_bottom_type_id'];
        $evaluation_extremities_bottom_bone = $dt_post['evaluation_extremities_bottom_bone'];
        $evaluation_ambulation_type_id = $dt_post['evaluation_ambulation_type_id'];

        $medical_devices = (object) array(
            'data' => $medical_devices_raw,
        );

        $medical_devices = json_encode($medical_devices);

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_obgyn = array(
            'nurse_id'                              => $nurse_id,
            'history_disease_type_id'               => $history_disease_type_id,
            'history_disease'                       => $history_disease,
            'been_hospitalized_type_id'             => $been_hospitalized_type_id,
            'been_hospitalized_answer'              => $been_hospitalized_answer,
            'been_hospitalized_date'                => $been_hospitalized_date,
            'been_hospitalized_place'               => $been_hospitalized_place,
            'any_surgery_type_id'                   => $any_surgery_type_id,
            'any_surgery_date'                      => $any_surgery_date,
            'any_surgery_place'                     => $any_surgery_place,
            'under_treatment_type_id'               => $under_treatment_type_id,
            'under_treatment'                       => $under_treatment,
            'any_history_family_disease_type_id'    => $any_history_family_disease_type_id,
            'any_history_family_answer_arry'        => $any_history_family_answer_arry,
            'any_history_family_answer'             => $any_history_family_answer,
            'dependence_substance_type_id'          => $dependence_substance_type_id,
            'dependence_substance_arry'             => $dependence_substance_arry,
            'dependence_substance'                  => $dependence_substance,
            'employment_history_type_id'            => $employment_history_type_id,
            'employment_history'                    => $employment_history,
            'allergy_history_type_id'               => $allergy_history_type_id,
            'allergy_history_arry'                  => $allergy_history_arry,
            'allergy_history_medicine'              => $allergy_history_medicine,
            'allergy_history_food'                  => $allergy_history_food,
            'allergy_history_other'                 => $allergy_history_other,
            'marriage_history_type_id'              => $marriage_history_type_id,
            'marriage_history'                      => $marriage_history,
            'contraception_use_type_id'             => $contraception_use_type_id,
            'contraception_use'                     => $contraception_use,
            'contraception_length_of_use'           => $contraception_length_of_use,
            'contraception_use_complaint'           => $contraception_use_complaint,
            'age_first_marriage'                    => $age_first_marriage,
            'marriage_one'                          => $marriage_one,
            'marriage_two'                          => $marriage_two,
            'marriage_three'                        => $marriage_three,
            'period_menarche_age'                   => $period_menarche_age,
            'period_cycle'                          => $period_cycle,
            'period_volume'                         => $period_volume,
            'period_regular_type_id'                => $period_regular_type_id,
            'period_regular'                        => $period_regular,
            'period_complaint_type_id'              => $period_complaint_type_id,
            'period_complaint'                      => $period_complaint,
            'hpht'                                  => $hpht,
            'estimated_delivery'                    => $estimated_delivery,
            'gestational_age'                       => $gestational_age,
            'gynecological_disease_type_id'         => $gynecological_disease_type_id,
            'gynecological_disease_arry'            => $gynecological_disease_arry,
            'gynecological_disease'                 => $gynecological_disease,
            'speech_disorders_type_id'              => $speech_disorders_type_id,
            'speech_disorders'                      => $speech_disorders,
            'translator_type_id'                    => $translator_type_id,
            'translator'                            => $translator,
            'sign_language_type_id'                 => $sign_language_type_id,
            'risk_injury_a_type_id'                 => $risk_injury_a_type_id,
            'risk_injury_b_type_id'                 => $risk_injury_b_type_id,
            'functional_status_type_id'             => $functional_status_type_id,
            'functional_status'                     => $functional_status,
            'aids'                                  => $aids,
            'tell_doctor_risk_type_id'              => $tell_doctor_risk_type_id,
            'tell_doctor_risk_date'                 => $tell_doctor_risk_date,
            'risk_injury_result'                    => $risk_injury_result,
            'sore_complaints_type_id'               => $sore_complaints_type_id,
            'sore_complaints'                       => $sore_complaints,
            'sore_goes_away_arry'                   => $sore_goes_away_arry,
            'sore_goes_away'                        => $sore_goes_away,
            'sore_method_type_id'                   => $sore_method_type_id,
            'sore_method_score'                     => $sore_method_score,
            'sore_method_arry'                      => $sore_method_arry,
            'tell_doctor_sore_type_id'              => $tell_doctor_sore_type_id,
            'tell_doctor_sore_date'                 => $tell_doctor_sore_date,
            'face_score'                            => $face_score,
            'legs_score'                            => $legs_score,
            'activity_score'                        => $activity_score,
            'cry_score'                             => $cry_score,
            'consolability_score'                   => $consolability_score,
            'sore_total_score'                      => $sore_total_score,
            'duration'                              => $duration,
            'location'                              => $location,
            'response'                              => $response,
            'frequency'                             => $frequency,
            'nutrition_arry'                        => $nutrition_arry,
            'nutrition_one'                         => $nutrition_one,
            'nutrition_two'                         => $nutrition_two,
            'nutrition_three'                       => $nutrition_three,
            'nutrition_four'                        => $nutrition_four,
            'nutrition_date'                        => $nutrition_date,
            'nutrition_total_score'                 => $nutrition_total_score,
            'patient_special_diagnose_type_id'      => $patient_special_diagnose_type_id,
            'patient_special_diagnose_arry'         => $patient_special_diagnose_arry,
            'patient_special_diagnose'              => $patient_special_diagnose,
            'midwifery_type_id'                     => $midwifery_type_id,
            'midwifery_problem'                     => $midwifery_problem,
            'midwifery_goals'                       => $midwifery_goals,
            'medical_devices_type_id'               => $medical_devices_type_id,
            'medical_devices'                       => $medical_devices,
            'care_plan_diet_nutrition_type_id'      => $care_plan_diet_nutrition_type_id,
            'care_plan_diet_nutrition'              => $care_plan_diet_nutrition,
            'care_plan_rehabilitation_type_id'      => $care_plan_rehabilitation_type_id,
            'care_plan_rehabilitation'              => $care_plan_rehabilitation,
            'care_plan_wound_type_id'               => $care_plan_wound_type_id,
            'care_plan_wound'                       => $care_plan_wound,
            'care_plan_pain_management_type_id'     => $care_plan_pain_management_type_id,
            'care_plan_pain_management'             => $care_plan_pain_management,
            'care_plan_other'                       => $care_plan_other,
            'evaluation_habits_arry'                => $evaluation_habits_arry,
            'evaluation_habits_medicine_type_id'    => $evaluation_habits_medicine_type_id,
            'evaluation_habits_medicine_other'      => $evaluation_habits_medicine_other,
            'evaluation_habits_jamu_type_id'        => $evaluation_habits_jamu_type_id,
            'evaluation_habits_jamu'                => $evaluation_habits_jamu,
            'evaluation_habits_alcohol_type_id'     => $evaluation_habits_alcohol_type_id,
            'evaluation_habits_alcohol'             => $evaluation_habits_alcohol,
            'evaluation_habits_smoke_type_id'       => $evaluation_habits_smoke_type_id,
            'evaluation_habits_smoke'               => $evaluation_habits_smoke,
            'evaluation_habits_smoke_amount'        => $evaluation_habits_smoke_amount,
            'evaluation_rest_type_id'               => $evaluation_rest_type_id,
            'evaluation_daily_type_id'              => $evaluation_daily_type_id,
            'evaluation_activity_type_id'           => $evaluation_activity_type_id,
            'evaluation_extremities_top_type_id'    => $evaluation_extremities_top_type_id,
            'evaluation_grip_type_id'               => $evaluation_grip_type_id,
            'evaluation_grip_other'                 => $evaluation_grip_other,
            'evaluation_extremities_bottom_type_id' => $evaluation_extremities_bottom_type_id,
            'evaluation_extremities_bottom_bone'    => $evaluation_extremities_bottom_bone,
            'evaluation_ambulation_type_id'         => $evaluation_ambulation_type_id,
            'user_added'                            => $user_id,
            'date_added'                            => date('Y-m-d H:i:s',time()),
            'date_updated'                          => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_nurse_obgyn);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_obgyn', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }

    /**
     * GET Nurse_obgyn with API
     * -------------------------
     * @method: GET
     */

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
    
        $nurse_obgyn_id = $id;

        $nurse_id = $q_strings['nurse_id'] ?? null;

        if ($nurse_obgyn_id === 0 || $nurse_obgyn_id === "0") {
            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id is required if nurse_obgyn_id is not provided');
                return;
            }
        }
        
        $select = 'a.*';
        $where = array(
            'a.is_delete' => 0
        );
        if ($nurse_id != null) {
            $where['a.nurse_id'] = $nurse_id;
            $output = $this->$model->select(0, $select, $where);
        } else {
            $output = $this->$model->select($nurse_obgyn_id, $select, $where);
        }

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->comm_needs_colloquial_arry = ($output[$i]->comm_needs_colloquial_arry != '') ? explode(',', $output[$i]->comm_needs_colloquial_arry): [];

            $output[$i]->medical_devices = json_decode($output[$i]->medical_devices)->data;
            $output[$i]->examination_history = json_decode($output[$i]->examination_history)->data;
            $output[$i]->birth_history = json_decode($output[$i]->birth_history)->data;
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
    
        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * Nurse_obgyn API
     * --------------------
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_obgyn
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
    
        $nurse_obgyn_id = $id;
    
        if ($nurse_obgyn_id === 0 || $nurse_obgyn_id === "0") {
            $this->REST_Return(400, 'nurse_obgyn_id is required');
            return;
        }

        $select = "a.id";
        $nurse_obgyn = $this->$model->select($nurse_obgyn_id, $select);
    
        if (count($nurse_obgyn) == 0) {
            $this->REST_Return(404, 'there is no nurse_obgyn with id ' . $nurse_obgyn_id);
            return;
        }
    
        $history_disease_type_id = $dt_put['history_disease_type_id'];
        $history_disease = $dt_put['history_disease'];
        $been_hospitalized_type_id = $dt_put['been_hospitalized_type_id'];
        $been_hospitalized_answer = $dt_put['been_hospitalized_answer'];
        $been_hospitalized_date = $dt_put['been_hospitalized_date'];
        $been_hospitalized_place = $dt_put['been_hospitalized_place'];
        $any_surgery_type_id = $dt_put['any_surgery_type_id'];
        $any_surgery_date = $dt_put['any_surgery_date'];
        $any_surgery_place = $dt_put['any_surgery_place'];
        $under_treatment_type_id = $dt_put['under_treatment_type_id'];
        $under_treatment = $dt_put['under_treatment'];
        $any_history_family_disease_type_id = $dt_put['any_history_family_disease_type_id'];
        $any_history_family_answer_arry = implode(',', $dt_put['any_history_family_answer_arry']);
        $any_history_family_answer = $dt_put['any_history_family_answer'];
        $dependence_substance_type_id = $dt_put['dependence_substance_type_id'];
        $dependence_substance_arry = implode(',', $dt_put['dependence_substance_arry']);
        $dependence_substance = $dt_put['dependence_substance'];
        $employment_history_type_id = $dt_put['employment_history_type_id'];
        $employment_history = $dt_put['employment_history'];
        $allergy_history_type_id = $dt_put['allergy_history_type_id'];
        $allergy_history_arry = implode(',', $dt_put['allergy_history_arry']);
        $allergy_history_medicine = $dt_put['allergy_history_medicine'];
        $allergy_history_food = $dt_put['allergy_history_food'];
        $allergy_history_other = $dt_put['allergy_history_other'];
        $marriage_history_type_id = $dt_put['marriage_history_type_id'];
        $marriage_history = $dt_put['marriage_history'];
        $contraception_use_type_id = $dt_put['contraception_use_type_id'];
        $contraception_use = $dt_put['contraception_use'];
        $contraception_length_of_use = $dt_put['contraception_length_of_use'];
        $contraception_use_complaint = $dt_put['contraception_use_complaint'];
        $age_first_marriage = $dt_put['age_first_marriage'];
        $marriage_one = $dt_put['marriage_one'];
        $marriage_two = $dt_put['marriage_two'];
        $marriage_three = $dt_put['marriage_three'];
        $period_menarche_age = $dt_put['period_menarche_age'];
        $period_cycle = $dt_put['period_cycle'];
        $period_volume = $dt_put['period_volume'];
        $period_regular_type_id = $dt_put['period_regular_type_id'];
        $period_regular = $dt_put['period_regular'];
        $period_complaint_type_id = $dt_put['period_complaint_type_id'];
        $period_complaint = $dt_put['period_complaint'];
        $hpht = $dt_put['hpht'];
        $estimated_delivery = $dt_put['estimated_delivery'];
        $gestational_age = $dt_put['gestational_age'];
        $gynecological_disease_type_id = $dt_put['gynecological_disease_type_id'];
        $gynecological_disease_arry = implode(',', $dt_put['gynecological_disease_arry']);
        $gynecological_disease = $dt_put['gynecological_disease'];
        $speech_disorders_type_id = $dt_put['speech_disorders_type_id'];
        $speech_disorders = $dt_put['speech_disorders'];
        $translator_type_id = $dt_put['translator_type_id'];
        $translator = $dt_put['translator'];
        $sign_language_type_id = $dt_put['sign_language_type_id'];
        $risk_injury_a_type_id = $dt_put['risk_injury_a_type_id'];
        $risk_injury_b_type_id = $dt_put['risk_injury_b_type_id'];
        $functional_status_type_id = $dt_put['functional_status_type_id'];
        $functional_status = $dt_put['functional_status'];
        $aids = $dt_put['aids'];
        $tell_doctor_risk_type_id = $dt_put['tell_doctor_risk_type_id'];
        $tell_doctor_risk_date = $dt_put['tell_doctor_risk_date'];
        $risk_injury_result = $dt_put['risk_injury_result'];
        $sore_complaints_type_id = $dt_put['sore_complaints_type_id'];
        $sore_complaints = $dt_put['sore_complaints'];
        $sore_goes_away_arry = implode(',', $dt_put['sore_goes_away_arry']);
        $sore_goes_away = $dt_put['sore_goes_away'];
        $sore_method_type_id = $dt_put['sore_method_type_id'];
        $sore_method_score = $dt_put['sore_method_score'];
        $sore_method_arry = implode(',', $dt_put['sore_method_arry']);
        $tell_doctor_sore_type_id = $dt_put['tell_doctor_sore_type_id'];
        $tell_doctor_sore_date = $dt_put['tell_doctor_sore_date'];
        $face_score = $dt_put['face_score'];
        $legs_score = $dt_put['legs_score'];
        $activity_score = $dt_put['activity_score'];
        $cry_score = $dt_put['cry_score'];
        $consolability_score = $dt_put['consolability_score'];
        $sore_total_score = $dt_put['sore_total_score'];
        $duration = $dt_put['duration'];
        $location = $dt_put['location'];
        $response = $dt_put['response'];
        $frequency = $dt_put['frequency'];
        $nutrition_arry = implode(',', $dt_put['nutrition_arry']);
        $nutrition_one = $dt_put['nutrition_one'];
        $nutrition_two = $dt_put['nutrition_two'];
        $nutrition_three = $dt_put['nutrition_three'];
        $nutrition_four = $dt_put['nutrition_four'];
        $nutrition_date = $dt_put['nutrition_date'];
        $nutrition_total_score = $dt_put['nutrition_total_score'];
        $patient_special_diagnose_type_id = $dt_put['patient_special_diagnose_type_id'];
        $patient_special_diagnose_arry = implode(',', $dt_put['patient_special_diagnose_arry']);
        $patient_special_diagnose = $dt_put['patient_special_diagnose'];
        $midwifery_type_id = $dt_put['midwifery_type_id'];
        $midwifery_problem = $dt_put['midwifery_problem'];
        $midwifery_goals = $dt_put['midwifery_goals'];
        $medical_devices_type_id = $dt_put['medical_devices_type_id'];
        $medical_devices_raw = $dt_put['medical_devices'];
        $care_plan_diet_nutrition_type_id = $dt_put['care_plan_diet_nutrition_type_id'];
        $care_plan_diet_nutrition = $dt_put['care_plan_diet_nutrition'];
        $care_plan_rehabilitation_type_id = $dt_put['care_plan_rehabilitation_type_id'];
        $care_plan_rehabilitation = $dt_put['care_plan_rehabilitation'];
        $care_plan_wound_type_id = $dt_put['care_plan_wound_type_id'];
        $care_plan_wound = $dt_put['care_plan_wound'];
        $care_plan_pain_management_type_id = $dt_put['care_plan_pain_management_type_id'];
        $care_plan_pain_management = $dt_put['care_plan_pain_management'];
        $care_plan_other = $dt_put['care_plan_other'];
        $evaluation_habits_arry = implode(',', $dt_put['evaluation_habits_arry']);
        $evaluation_habits_medicine_type_id = $dt_put['evaluation_habits_medicine_type_id'];
        $evaluation_habits_medicine_other = $dt_put['evaluation_habits_medicine_other'];
        $evaluation_habits_jamu_type_id = $dt_put['evaluation_habits_jamu_type_id'];
        $evaluation_habits_jamu = $dt_put['evaluation_habits_jamu'];
        $evaluation_habits_alcohol_type_id = $dt_put['evaluation_habits_alcohol_type_id'];
        $evaluation_habits_alcohol = $dt_put['evaluation_habits_alcohol'];
        $evaluation_habits_smoke_type_id = $dt_put['evaluation_habits_smoke_type_id'];
        $evaluation_habits_smoke = $dt_put['evaluation_habits_smoke'];
        $evaluation_habits_smoke_amount = $dt_put['evaluation_habits_smoke_amount'];
        $evaluation_rest_type_id = $dt_put['evaluation_rest_type_id'];
        $evaluation_daily_type_id = $dt_put['evaluation_daily_type_id'];
        $evaluation_activity_type_id = $dt_put['evaluation_activity_type_id'];
        $evaluation_extremities_top_type_id = $dt_put['evaluation_extremities_top_type_id'];
        $evaluation_grip_type_id = $dt_put['evaluation_grip_type_id'];
        $evaluation_grip_other = $dt_put['evaluation_grip_other'];
        $evaluation_extremities_bottom_type_id = $dt_put['evaluation_extremities_bottom_type_id'];
        $evaluation_extremities_bottom_bone = $dt_put['evaluation_extremities_bottom_bone'];
        $evaluation_ambulation_type_id = $dt_put['evaluation_ambulation_type_id'];

        $medical_devices = (object) array(
            'data' => $medical_devices_raw,
        );

        $medical_devices = json_encode($medical_devices);
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nurse_obgyn = array(
            'history_disease_type_id'               => $history_disease_type_id,
            'history_disease'                       => $history_disease,
            'been_hospitalized_type_id'             => $been_hospitalized_type_id,
            'been_hospitalized_answer'              => $been_hospitalized_answer,
            'been_hospitalized_date'                => $been_hospitalized_date,
            'been_hospitalized_place'               => $been_hospitalized_place,
            'any_surgery_type_id'                   => $any_surgery_type_id,
            'any_surgery_date'                      => $any_surgery_date,
            'any_surgery_place'                     => $any_surgery_place,
            'under_treatment_type_id'               => $under_treatment_type_id,
            'under_treatment'                       => $under_treatment,
            'any_history_family_disease_type_id'    => $any_history_family_disease_type_id,
            'any_history_family_answer_arry'        => $any_history_family_answer_arry,
            'any_history_family_answer'             => $any_history_family_answer,
            'dependence_substance_type_id'          => $dependence_substance_type_id,
            'dependence_substance_arry'             => $dependence_substance_arry,
            'dependence_substance'                  => $dependence_substance,
            'employment_history_type_id'            => $employment_history_type_id,
            'employment_history'                    => $employment_history,
            'allergy_history_type_id'               => $allergy_history_type_id,
            'allergy_history_arry'                  => $allergy_history_arry,
            'allergy_history_medicine'              => $allergy_history_medicine,
            'allergy_history_food'                  => $allergy_history_food,
            'allergy_history_other'                 => $allergy_history_other,
            'marriage_history_type_id'              => $marriage_history_type_id,
            'marriage_history'                      => $marriage_history,
            'contraception_use_type_id'             => $contraception_use_type_id,
            'contraception_use'                     => $contraception_use,
            'contraception_length_of_use'           => $contraception_length_of_use,
            'contraception_use_complaint'           => $contraception_use_complaint,
            'age_first_marriage'                    => $age_first_marriage,
            'marriage_one'                          => $marriage_one,
            'marriage_two'                          => $marriage_two,
            'marriage_three'                        => $marriage_three,
            'period_menarche_age'                   => $period_menarche_age,
            'period_cycle'                          => $period_cycle,
            'period_volume'                         => $period_volume,
            'period_regular_type_id'                => $period_regular_type_id,
            'period_regular'                        => $period_regular,
            'period_complaint_type_id'              => $period_complaint_type_id,
            'period_complaint'                      => $period_complaint,
            'hpht'                                  => $hpht,
            'estimated_delivery'                    => $estimated_delivery,
            'gestational_age'                       => $gestational_age,
            'gynecological_disease_type_id'         => $gynecological_disease_type_id,
            'gynecological_disease_arry'            => $gynecological_disease_arry,
            'gynecological_disease'                 => $gynecological_disease,
            'speech_disorders_type_id'              => $speech_disorders_type_id,
            'speech_disorders'                      => $speech_disorders,
            'translator_type_id'                    => $translator_type_id,
            'translator'                            => $translator,
            'sign_language_type_id'                 => $sign_language_type_id,
            'risk_injury_a_type_id'                 => $risk_injury_a_type_id,
            'risk_injury_b_type_id'                 => $risk_injury_b_type_id,
            'functional_status_type_id'             => $functional_status_type_id,
            'functional_status'                     => $functional_status,
            'aids'                                  => $aids,
            'tell_doctor_risk_type_id'              => $tell_doctor_risk_type_id,
            'tell_doctor_risk_date'                 => $tell_doctor_risk_date,
            'risk_injury_result'                    => $risk_injury_result,
            'sore_complaints_type_id'               => $sore_complaints_type_id,
            'sore_complaints'                       => $sore_complaints,
            'sore_goes_away_arry'                   => $sore_goes_away_arry,
            'sore_goes_away'                        => $sore_goes_away,
            'sore_method_type_id'                   => $sore_method_type_id,
            'sore_method_score'                     => $sore_method_score,
            'sore_method_arry'                      => $sore_method_arry,
            'tell_doctor_sore_type_id'              => $tell_doctor_sore_type_id,
            'tell_doctor_sore_date'                 => $tell_doctor_sore_date,
            'face_score'                            => $face_score,
            'legs_score'                            => $legs_score,
            'activity_score'                        => $activity_score,
            'cry_score'                             => $cry_score,
            'consolability_score'                   => $consolability_score,
            'sore_total_score'                      => $sore_total_score,
            'duration'                              => $duration,
            'location'                              => $location,
            'response'                              => $response,
            'frequency'                             => $frequency,
            'nutrition_arry'                        => $nutrition_arry,
            'nutrition_one'                         => $nutrition_one,
            'nutrition_two'                         => $nutrition_two,
            'nutrition_three'                       => $nutrition_three,
            'nutrition_four'                        => $nutrition_four,
            'nutrition_date'                        => $nutrition_date,
            'nutrition_total_score'                 => $nutrition_total_score,
            'patient_special_diagnose_type_id'      => $patient_special_diagnose_type_id,
            'patient_special_diagnose_arry'         => $patient_special_diagnose_arry,
            'patient_special_diagnose'              => $patient_special_diagnose,
            'midwifery_type_id'                     => $midwifery_type_id,
            'midwifery_problem'                     => $midwifery_problem,
            'midwifery_goals'                       => $midwifery_goals,
            'medical_devices_type_id'               => $medical_devices_type_id,
            'medical_devices'                       => $medical_devices,
            'care_plan_diet_nutrition_type_id'      => $care_plan_diet_nutrition_type_id,
            'care_plan_diet_nutrition'              => $care_plan_diet_nutrition,
            'care_plan_rehabilitation_type_id'      => $care_plan_rehabilitation_type_id,
            'care_plan_rehabilitation'              => $care_plan_rehabilitation,
            'care_plan_wound_type_id'               => $care_plan_wound_type_id,
            'care_plan_wound'                       => $care_plan_wound,
            'care_plan_pain_management_type_id'     => $care_plan_pain_management_type_id,
            'care_plan_pain_management'             => $care_plan_pain_management,
            'care_plan_other'                       => $care_plan_other,
            'evaluation_habits_arry'                => $evaluation_habits_arry,
            'evaluation_habits_medicine_type_id'    => $evaluation_habits_medicine_type_id,
            'evaluation_habits_medicine_other'      => $evaluation_habits_medicine_other,
            'evaluation_habits_jamu_type_id'        => $evaluation_habits_jamu_type_id,
            'evaluation_habits_jamu'                => $evaluation_habits_jamu,
            'evaluation_habits_alcohol_type_id'     => $evaluation_habits_alcohol_type_id,
            'evaluation_habits_alcohol'             => $evaluation_habits_alcohol,
            'evaluation_habits_smoke_type_id'       => $evaluation_habits_smoke_type_id,
            'evaluation_habits_smoke'               => $evaluation_habits_smoke,
            'evaluation_habits_smoke_amount'        => $evaluation_habits_smoke_amount,
            'evaluation_rest_type_id'               => $evaluation_rest_type_id,
            'evaluation_daily_type_id'              => $evaluation_daily_type_id,
            'evaluation_activity_type_id'           => $evaluation_activity_type_id,
            'evaluation_extremities_top_type_id'    => $evaluation_extremities_top_type_id,
            'evaluation_grip_type_id'               => $evaluation_grip_type_id,
            'evaluation_grip_other'                 => $evaluation_grip_other,
            'evaluation_extremities_bottom_type_id' => $evaluation_extremities_bottom_type_id,
            'evaluation_extremities_bottom_bone'    => $evaluation_extremities_bottom_bone,
            'evaluation_ambulation_type_id'         => $evaluation_ambulation_type_id,
            'user_updated'                          => $user_id,
            'date_updated'                          => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($nurse_obgyn_id, $dt_nurse_obgyn);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_obgyn', $response_data);
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
    
        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'nurse_obgyn_id is required');
            return;
        }
    
        $nurse_obgyn_id = $this->$model->select_id($id);
    
        if ($nurse_obgyn_id == "") {
            $this->REST_Return(404, 'there is no nurse_obgyn with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_nurse_obgyn = array(
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($nurse_obgyn_id, $dt_nurse_obgyn);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nurse_obgyn', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}