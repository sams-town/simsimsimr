<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_adult_inpatient extends My_Controller {
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
     * Nurse_adult_inpatient API
     * --------------------
     * @param: nurse_id
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_adult_inpatient
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
        
        $nurse_id = $dt_post['nurse_id'];
        $psychological_status_arry = implode(',', $dt_post['psychological_status_arry']);
        $psychological_status = $dt_post['psychological_status'];
        $mental_status_arry = implode(',', $dt_post['mental_status_arry']);
        $mental_status_problems = $dt_post['mental_status_problems'];
        $mental_status_violence = $dt_post['mental_status_violence'];
        $elimination_defecate = $dt_post['elimination_defecate'];
        $elimination_urination = $dt_post['elimination_urination'];
        $elimination_consistency_type_id = $dt_post['elimination_consistency_type_id'];
        $elimination_color_type_id = $dt_post['elimination_color_type_id'];
        $elimination_color = $dt_post['elimination_color'];
        $sore_complaints_type_id = $dt_post['sore_complaints_type_id'];
        $sore_complaints = $dt_post['sore_complaints'];
        $sore_goes_away_arry = implode(',', $dt_post['sore_goes_away_arry']);
        $sore_goes_away = $dt_post['sore_goes_away'];
        $sore_method_type_id = $dt_post['sore_method_type_id'];
        $sore_nrs_score = $dt_post['sore_nrs_score'];
        $sore_next_nrs_score = $dt_post['sore_next_nrs_score'];
        $sore_affects_arry = implode(',', $dt_post['sore_affects_arry']);
        $sore_affects = $dt_post['sore_affects'];
        $discharge_treatment_duration = $dt_post['discharge_treatment_duration'];
        $discharge_further_care_arry = implode(',', $dt_post['discharge_further_care_arry']);
        $tell_doctor_type_id = $dt_post['tell_doctor_type_id'];
        $tell_doctor_date = $dt_post['tell_doctor_date'];
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
        $sore_quality_arry = implode(',', $dt_post['sore_quality_arry']);
        $medical_devices_type_id = $dt_post['medical_devices_type_id'];
        $medical_devices_raw = $dt_post['medical_devices'];
        $examination_history_raw = $dt_post['examination_history'];
        $care_plan_diet_nutrition_type_id = $dt_post['care_plan_diet_nutrition_type_id'];
        $care_plan_diet_nutrition = $dt_post['care_plan_diet_nutrition'];
        $care_plan_rehabilitation_type_id = $dt_post['care_plan_rehabilitation_type_id'];
        $care_plan_rehabilitation = $dt_post['care_plan_rehabilitation'];
        $care_plan_wound_type_id = $dt_post['care_plan_wound_type_id'];
        $care_plan_wound = $dt_post['care_plan_wound'];
        $care_plan_pain_management_type_id = $dt_post['care_plan_pain_management_type_id'];
        $care_plan_pain_management = $dt_post['care_plan_pain_management'];
        $care_plan_other = $dt_post['care_plan_other'];
        $evaluation_smoke_type_id = $dt_post['evaluation_smoke_type_id'];
        $evaluation_smoke_amount = $dt_post['evaluation_smoke_amount'];
        $evaluation_smoke_long = $dt_post['evaluation_smoke_long'];
        $evaluation_alcohol_type_id = $dt_post['evaluation_alcohol_type_id'];
        $evaluation_alcohol_type = $dt_post['evaluation_alcohol_type'];
        $evaluation_alcohol_amount = $dt_post['evaluation_alcohol_amount'];
        $evaluation_sedative_type_id = $dt_post['evaluation_sedative_type_id'];
        $evaluation_sedative_type = $dt_post['evaluation_sedative_type'];
        $evaluation_sedative_amount = $dt_post['evaluation_sedative_amount'];
        $evaluation_sleep_type_id = $dt_post['evaluation_sleep_type_id'];
        $evaluation_sleep = $dt_post['evaluation_sleep'];
        $evaluation_daily_type_id = $dt_post['evaluation_daily_type_id'];
        $evaluation_activity_type_id = $dt_post['evaluation_activity_type_id'];
        $evaluation_extremities_top_type_id = $dt_post['evaluation_extremities_top_type_id'];
        $evaluation_grip_type_id = $dt_post['evaluation_grip_type_id'];
        $evaluation_grip_other = $dt_post['evaluation_grip_other'];
        $evaluation_coordination_type_id = $dt_post['evaluation_coordination_type_id'];
        $evaluation_coordination_other = $dt_post['evaluation_coordination_other'];
        $evaluation_extremities_bottom_type_id = $dt_post['evaluation_extremities_bottom_type_id'];
        $evaluation_extremities_bottom_bone = $dt_post['evaluation_extremities_bottom_bone'];
        $evaluation_ambulation_type_id = $dt_post['evaluation_ambulation_type_id'];

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

        /* Start Transaction */
        $this->db->trans_start();
    
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
            'sore_complaints_type_id'                => $sore_complaints_type_id,
            'sore_complaints'                        => $sore_complaints,
            'sore_goes_away_arry'                    => $sore_goes_away_arry,
            'sore_goes_away'                         => $sore_goes_away,
            'sore_method_type_id'                    => $sore_method_type_id,
            'sore_nrs_score'                         => $sore_nrs_score,
            'sore_next_nrs_score'                    => $sore_next_nrs_score,
            'sore_affects_arry'                      => $sore_affects_arry,
            'sore_affects'                           => $sore_affects,
            'discharge_treatment_duration'           => $discharge_treatment_duration,
            'discharge_further_care_arry'            => $discharge_further_care_arry,
            'tell_doctor_type_id'                    => $tell_doctor_type_id,
            'tell_doctor_date'                       => $tell_doctor_date,
            'face_score'                             => $face_score,
            'legs_score'                             => $legs_score,
            'activity_score'                         => $activity_score,
            'cry_score'                              => $cry_score,
            'consolability_score'                    => $consolability_score,
            'sore_total_score'                       => $sore_total_score,
            'duration'                               => $duration,
            'location'                               => $location,
            'response'                               => $response,
            'frequency'                              => $frequency,
            'sore_quality_arry'                      => $sore_quality_arry,
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
            'evaluation_alcohol_type_id'             => $evaluation_alcohol_type_id,
            'evaluation_alcohol_type'                => $evaluation_alcohol_type,
            'evaluation_alcohol_amount'              => $evaluation_alcohol_amount,
            'evaluation_sedative_type_id'            => $evaluation_sedative_type_id,
            'evaluation_sedative_type'               => $evaluation_sedative_type,
            'evaluation_sedative_amount'             => $evaluation_sedative_amount,
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
            'date_added'                             =>  date('Y-m-d H:i:s',time()),
            'date_updated'                           =>  date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_nurse_adult_inpatient);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_adult_inpatient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_adult_inpatient', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
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
    
        $nurse_adult_patient_id = $id;
    
        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($nurse_adult_patient_id);
        $response_data = array();

        for ($i=0; $i < count($output); $i++) {
            $output[$i]->psychological_status_arry = ($output[$i]->psychological_status_arry != "") ? explode(',', $output[$i]->psychological_status_arry) : [];
            $output[$i]->mental_status_arry = ($output[$i]->mental_status_arry != "") ? explode(',', $output[$i]->mental_status_arry) : [];
            $output[$i]->sore_goes_away_arry = ($output[$i]->sore_goes_away_arry != "") ? explode(',', $output[$i]->sore_goes_away_arry) : [];
            $output[$i]->sore_affects_arry = ($output[$i]->sore_affects_arry != "") ? explode(',', $output[$i]->sore_affects_arry) : [];
            $output[$i]->discharge_further_care_arry = ($output[$i]->discharge_further_care_arry != "") ? explode(',', $output[$i]->discharge_further_care_arry) : [];
            $output[$i]->sore_quality_arry = ($output[$i]->sore_quality_arry != "") ? explode(',', $output[$i]->sore_quality_arry) : [];

            $output[$i]->medical_devices = json_decode($output[$i]->medical_devices)->data;
            $output[$i]->examination_history = json_decode($output[$i]->examination_history)->data;
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
     * Nurse_adult_inpatient API
     * --------------------
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_adult_inpatient
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
    
        $nurse_adult_inpatient_id = $id;
    
        if ($nurse_adult_inpatient_id === 0 || $nurse_adult_inpatient_id === "0") {
            $this->REST_Return(400, '$nurse_adult_inpatient_id is required');
            return;
        }
    
        $select = "a.id";
        $nurse_adult_inpatient = $this->$model->select($nurse_adult_inpatient_id, $select);
    
        if (count($nurse_adult_inpatient) == 0) {
            $this->REST_Return(404, 'there is no nurse_adult_inpatient with id ' . $nurse_adult_inpatient_id);
            return;
        }

        $psychological_status_arry = implode(',', $dt_put['psychological_status_arry']);
        $psychological_status = $dt_put['psychological_status'];
        $mental_status_arry = implode(',', $dt_put['mental_status_arry']);
        $mental_status_problems = $dt_put['mental_status_problems'];
        $mental_status_violence = $dt_put['mental_status_violence'];
        $elimination_defecate = $dt_put['elimination_defecate'];
        $elimination_urination = $dt_put['elimination_urination'];
        $elimination_consistency_type_id = $dt_put['elimination_consistency_type_id'];
        $elimination_color_type_id = $dt_put['elimination_color_type_id'];
        $elimination_color = $dt_put['elimination_color'];
        $sore_complaints_type_id = $dt_put['sore_complaints_type_id'];
        $sore_complaints = $dt_put['sore_complaints'];
        $sore_goes_away_arry = implode(',', $dt_put['sore_goes_away_arry']);
        $sore_goes_away = $dt_put['sore_goes_away'];
        $sore_method_type_id = $dt_put['sore_method_type_id'];
        $sore_nrs_score = $dt_put['sore_nrs_score'];
        $sore_next_nrs_score = $dt_put['sore_next_nrs_score'];
        $sore_affects_arry = implode(',', $dt_put['sore_affects_arry']);
        $sore_affects = $dt_put['sore_affects'];
        $discharge_treatment_duration = $dt_put['discharge_treatment_duration'];
        $discharge_further_care_arry = implode(',', $dt_put['discharge_further_care_arry']);
        $tell_doctor_type_id = $dt_put['tell_doctor_type_id'];
        $tell_doctor_date = $dt_put['tell_doctor_date'];
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
        $sore_quality_arry = implode(',', $dt_put['sore_quality_arry']);
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
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nurse_adult_inpatient = array(
            'psychological_status_arry'             => $psychological_status_arry,
            'psychological_status'                  => $psychological_status,
            'mental_status_arry'                    => $mental_status_arry,
            'mental_status_problems'                => $mental_status_problems,
            'mental_status_violence'                => $mental_status_violence,
            'elimination_defecate'                  => $elimination_defecate,
            'elimination_urination'                 => $elimination_urination,
            'elimination_consistency_type_id'       => $elimination_consistency_type_id,
            'elimination_color_type_id'             => $elimination_color_type_id,
            'elimination_color'                     => $elimination_color,
            'sore_complaints_type_id'               => $sore_complaints_type_id,
            'sore_complaints'                       => $sore_complaints,
            'sore_goes_away_arry'                   => $sore_goes_away_arry,
            'sore_goes_away'                        => $sore_goes_away,
            'sore_method_type_id'                   => $sore_method_type_id,
            'sore_nrs_score'                        => $sore_nrs_score,
            'sore_next_nrs_score'                   => $sore_next_nrs_score,
            'sore_affects_arry'                     => $sore_affects_arry,
            'sore_affects'                          => $sore_affects,
            'discharge_treatment_duration'          => $discharge_treatment_duration,
            'discharge_further_care_arry'           => $discharge_further_care_arry,
            'tell_doctor_type_id'                   => $tell_doctor_type_id,
            'tell_doctor_date'                      => $tell_doctor_date,
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
            'sore_quality_arry'                     => $sore_quality_arry,
            'medical_devices_type_id'               => $medical_devices_type_id,
            'medical_devices'                       => $medical_devices,
            'examination_history'                   => $examination_history,
            'care_plan_diet_nutrition_type_id'      => $care_plan_diet_nutrition_type_id,
            'care_plan_diet_nutrition'              => $care_plan_diet_nutrition,
            'care_plan_rehabilitation_type_id'      => $care_plan_rehabilitation_type_id,
            'care_plan_rehabilitation'              => $care_plan_rehabilitation,
            'care_plan_wound_type_id'               => $care_plan_wound_type_id,
            'care_plan_wound'                       => $care_plan_wound,
            'care_plan_pain_management_type_id'     => $care_plan_pain_management_type_id,
            'care_plan_pain_management'             => $care_plan_pain_management,
            'care_plan_other'                       => $care_plan_other,
            'evaluation_smoke_type_id'              => $evaluation_smoke_type_id,
            'evaluation_smoke_amount'               => $evaluation_smoke_amount,
            'evaluation_smoke_long'                 => $evaluation_smoke_long,
            'evaluation_alcohol_type_id'            => $evaluation_alcohol_type_id,
            'evaluation_alcohol_type'               => $evaluation_alcohol_type,
            'evaluation_alcohol_amount'             => $evaluation_alcohol_amount,
            'evaluation_sedative_type_id'           => $evaluation_sedative_type_id,
            'evaluation_sedative_type'              => $evaluation_sedative_type,
            'evaluation_sedative_amount'            => $evaluation_sedative_amount,
            'evaluation_sleep_type_id'              => $evaluation_sleep_type_id,
            'evaluation_sleep'                      => $evaluation_sleep,
            'evaluation_daily_type_id'              => $evaluation_daily_type_id,
            'evaluation_activity_type_id'           => $evaluation_activity_type_id,
            'evaluation_extremities_top_type_id'    => $evaluation_extremities_top_type_id,
            'evaluation_grip_type_id'               => $evaluation_grip_type_id,
            'evaluation_grip_other'                 => $evaluation_grip_other,
            'evaluation_coordination_type_id'       => $evaluation_coordination_type_id,
            'evaluation_coordination_other'         => $evaluation_coordination_other,
            'evaluation_extremities_bottom_type_id' => $evaluation_extremities_bottom_type_id,
            'evaluation_extremities_bottom_bone'    => $evaluation_extremities_bottom_bone,
            'evaluation_ambulation_type_id'         => $evaluation_ambulation_type_id,
            'user_updated'                          => $user_id,
            'date_updated'                          => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($nurse_adult_inpatient_id, $dt_nurse_adult_inpatient);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_adult_inpatient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_adult_inpatient', $response_data);
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
            $this->REST_Return(400, 'nurse_adult_inpatient_id is required');
            return;
        }
    
        $nurse_adult_inpatient_id = $this->$model->select_id($id);
    
        if ($nurse_adult_inpatient_id == "") {
            $this->REST_Return(404, 'there is no nurse_adult_inpatient with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_nurse_adult_inpatient = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($nurse_adult_inpatient_id, $dt_nurse_adult_inpatient);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_adult_inpatient,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nurse_adult_inpatient', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}