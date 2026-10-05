<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_adult extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
    }

    /**
     * Nurse_adult API
     * --------------------
     * @param: nurse_id
     * @param: been_hospitalized_type_id
     * @param: been_hospitalized_answer
     * @param: any_history_family_disease_type_id
     * @param: any_history_family_answer_arry
     * @param: any_history_family_answer
     * @param: any_surgery
     * @param: any_surgery_date
     * @param: complication
     * @param: history_allergy_type_id
     * @param: history_allergy
     * @param: history_psychological_arry
     * @param: body_mass_index
     * @param: patient_food_intake
     * @param: patient_certain_diets
     * @param: lose_weight_months
     * @param: lose_weight
     * @param: patient_disease_condition
     * @param: nutrition_total_score
     * @param: sore_complaints_type_id
     * @param: sore_complaints
     * @param: sore_goes_away_arry
     * @param: sore_goes_away
     * @param: sore_method_type_id
     * @param: sore_method_score
     * @param: sore_method_arry
     * @param: tell_doctor_type_id
     * @param: tell_doctor_date
     * @param: face_score
     * @param: legs_score
     * @param: activity_score
     * @param: cry_score
     * @param: consolability_score
     * @param: sore_total_score
     * @param: duration
     * @param: location
     * @param: response
     * @param: frequency
     * @param: fall_risk_method_type_id
     * @param: fall_risk_total_score
     * @param: fall_risk_type_id
     * @param: nursing_diagnoses_arry
     * @param: plan_diit
     * @param: plan_activity
     * @param: plan_medicine
     * @param: plan_recontrol
     * @param: implementation_arry
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_adult
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
            $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('been_hospitalized_type_id', 'been_hospitalized_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('been_hospitalized_answer', 'been_hospitalized_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_history_family_disease_type_id', 'any_history_family_disease_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('any_history_family_answer_arry', 'any_history_family_answer_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('any_history_family_answer', 'any_history_family_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery', 'any_surgery', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery_date', 'any_surgery_date', 'trim');
            $this->form_validation->set_rules('complication', 'complication', 'trim|max_length[100]');
            $this->form_validation->set_rules('history_allergy_type_id', 'history_allergy_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim|max_length[100]');
            $this->form_validation->set_rules('history_psychological_arry', 'history_psychological_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('body_mass_index', 'body_mass_index', 'trim|max_length[1]');
            $this->form_validation->set_rules('patient_food_intake', 'patient_food_intake', 'trim|max_length[1]');
            $this->form_validation->set_rules('patient_certain_diets', 'patient_certain_diets', 'trim|max_length[1]');
            $this->form_validation->set_rules('lose_weight_months', 'lose_weight_months', 'trim|max_length[1]');
            $this->form_validation->set_rules('lose_weight', 'lose_weight', 'trim|max_length[4]');
            $this->form_validation->set_rules('patient_disease_condition', 'patient_disease_condition', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutrition_total_score', 'nutrition_total_score', 'trim|max_length[2]');
            $this->form_validation->set_rules('sore_complaints_type_id', 'sore_complaints_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sore_complaints', 'sore_complaints', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_goes_away_arry', 'sore_goes_away_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_goes_away', 'sore_goes_away', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_method_type_id', 'sore_method_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sore_method_score', 'sore_method_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('sore_method_arry', 'sore_method_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('tell_doctor_type_id', 'tell_doctor_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('tell_doctor_date', 'tell_doctor_date', 'trim');
            $this->form_validation->set_rules('face_score', 'face_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('legs_score', 'legs_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_score', 'activity_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('cry_score', 'cry_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('consolability_score', 'consolability_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('sore_total_score', 'sore_total_score', 'trim|max_length[2]');
            $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[10]');
            $this->form_validation->set_rules('location', 'location', 'trim|max_length[100]');
            $this->form_validation->set_rules('response', 'response', 'trim|max_length[100]');
            $this->form_validation->set_rules('frequency', 'frequency', 'trim|max_length[100]');
            $this->form_validation->set_rules('fall_risk_method_type_id', 'fall_risk_method_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('fall_risk_total_score', 'fall_risk_total_score', 'trim|max_length[2]');
            $this->form_validation->set_rules('fall_risk_type_id', 'fall_risk_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('nursing_diagnoses_arry', 'nursing_diagnoses_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('plan_diit', 'plan_diit', 'trim|max_length[100]');
            $this->form_validation->set_rules('plan_activity', 'plan_activity', 'trim|max_length[100]');
            $this->form_validation->set_rules('plan_medicine', 'plan_medicine', 'trim|max_length[100]');
            $this->form_validation->set_rules('plan_recontrol', 'plan_recontrol', 'trim|max_length[100]');
            $this->form_validation->set_rules('implementation_arry', 'implementation_arry', 'trim|max_length[50]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check nurse_id
                if($this->Nurse->read($dt_post['nurse_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_nurse_adult = array(
                        'nurse_id'                              => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
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
                    $id = $this->$model->create($dt_nurse_adult);
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
                    $this->REST_Return(404, 'Wrong nurse_id!');
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
     * GET Nurse_adult with API
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
            $output = $this->$model->read($id);
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
            $output = $this->$model->read_ssr($id);
            $response_data = array();
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
     * Nurse_adult API
     * --------------------
     * @param: been_hospitalized_type_id
     * @param: been_hospitalized_answer
     * @param: any_history_family_disease_type_id
     * @param: any_history_family_answer_arry
     * @param: any_history_family_answer
     * @param: any_surgery
     * @param: any_surgery_date
     * @param: complication
     * @param: history_allergy_type_id
     * @param: history_allergy
     * @param: history_psychological_arry
     * @param: body_mass_index
     * @param: patient_food_intake
     * @param: patient_certain_diets
     * @param: lose_weight_months
     * @param: lose_weight
     * @param: patient_disease_condition
     * @param: nutrition_total_score
     * @param: sore_complaints_type_id
     * @param: sore_complaints
     * @param: sore_goes_away_arry
     * @param: sore_goes_away
     * @param: sore_method_type_id
     * @param: sore_method_score
     * @param: sore_method_arry
     * @param: tell_doctor_type_id
     * @param: tell_doctor_date
     * @param: face_score
     * @param: legs_score
     * @param: activity_score
     * @param: cry_score
     * @param: consolability_score
     * @param: sore_total_score
     * @param: duration
     * @param: location
     * @param: response
     * @param: frequency
     * @param: fall_risk_method_type_id
     * @param: fall_risk_total_score
     * @param: fall_risk_type_id
     * @param: nursing_diagnoses_arry
     * @param: plan_diit
     * @param: plan_activity
     * @param: plan_medicine
     * @param: plan_recontrol
     * @param: implementation_arry
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_adult
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
            $this->form_validation->set_rules('been_hospitalized_type_id', 'been_hospitalized_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('been_hospitalized_answer', 'been_hospitalized_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_history_family_disease_type_id', 'any_history_family_disease_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('any_history_family_answer_arry', 'any_history_family_answer_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('any_history_family_answer', 'any_history_family_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery', 'any_surgery', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery_date', 'any_surgery_date', 'trim');
            $this->form_validation->set_rules('complication', 'complication', 'trim|max_length[100]');
            $this->form_validation->set_rules('history_allergy_type_id', 'history_allergy_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('history_allergy', 'history_allergy', 'trim|max_length[100]');
            $this->form_validation->set_rules('history_psychological_arry', 'history_psychological_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('body_mass_index', 'body_mass_index', 'trim|max_length[1]');
            $this->form_validation->set_rules('patient_food_intake', 'patient_food_intake', 'trim|max_length[1]');
            $this->form_validation->set_rules('patient_certain_diets', 'patient_certain_diets', 'trim|max_length[1]');
            $this->form_validation->set_rules('lose_weight_months', 'lose_weight_months', 'trim|max_length[1]');
            $this->form_validation->set_rules('lose_weight', 'lose_weight', 'trim|max_length[4]');
            $this->form_validation->set_rules('patient_disease_condition', 'patient_disease_condition', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutrition_total_score', 'nutrition_total_score', 'trim|max_length[2]');
            $this->form_validation->set_rules('sore_complaints_type_id', 'sore_complaints_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sore_complaints', 'sore_complaints', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_goes_away_arry', 'sore_goes_away_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_goes_away', 'sore_goes_away', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_method_type_id', 'sore_method_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sore_method_score', 'sore_method_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('sore_method_arry', 'sore_method_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('tell_doctor_type_id', 'tell_doctor_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('tell_doctor_date', 'tell_doctor_date', 'trim');
            $this->form_validation->set_rules('face_score', 'face_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('legs_score', 'legs_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_score', 'activity_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('cry_score', 'cry_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('consolability_score', 'consolability_score', 'trim|max_length[1]');
            $this->form_validation->set_rules('sore_total_score', 'sore_total_score', 'trim|max_length[2]');
            $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[10]');
            $this->form_validation->set_rules('location', 'location', 'trim|max_length[100]');
            $this->form_validation->set_rules('response', 'response', 'trim|max_length[100]');
            $this->form_validation->set_rules('frequency', 'frequency', 'trim|max_length[100]');
            $this->form_validation->set_rules('fall_risk_method_type_id', 'fall_risk_method_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('fall_risk_total_score', 'fall_risk_total_score', 'trim|max_length[2]');
            $this->form_validation->set_rules('fall_risk_type_id', 'fall_risk_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('nursing_diagnoses_arry', 'nursing_diagnoses_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('plan_diit', 'plan_diit', 'trim|max_length[100]');
            $this->form_validation->set_rules('plan_activity', 'plan_activity', 'trim|max_length[100]');
            $this->form_validation->set_rules('plan_medicine', 'plan_medicine', 'trim|max_length[100]');
            $this->form_validation->set_rules('plan_recontrol', 'plan_recontrol', 'trim|max_length[100]');
            $this->form_validation->set_rules('implementation_arry', 'implementation_arry', 'trim|max_length[50]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse_adult = $this->$model->read($id); 
                if($nurse_adult != []) {
                    $dt_nurse_adult = array(
                        'id'                                    => $id,
                        'been_hospitalized_type_id'             => isset($dt_put['been_hospitalized_type_id']) ? $dt_put['been_hospitalized_type_id'] : $nurse_adult[0]->been_hospitalized_type_id,
                        'been_hospitalized_answer'              => isset($dt_put['been_hospitalized_answer']) ? $dt_put['been_hospitalized_answer'] : $nurse_adult[0]->been_hospitalized_answer,
                        'any_history_family_disease_type_id'    => isset($dt_put['any_history_family_disease_type_id']) ? $dt_put['any_history_family_disease_type_id'] : $nurse_adult[0]->any_history_family_disease_type_id,
                        'any_history_family_answer_arry'        => isset($dt_put['any_history_family_answer_arry']) ? implode(',',$dt_put['any_history_family_answer_arry']) : $nurse_adult[0]->any_history_family_answer_arry,
                        'any_history_family_answer'             => isset($dt_put['any_history_family_answer']) ? $dt_put['any_history_family_answer'] : $nurse_adult[0]->any_history_family_answer,
                        'any_surgery'                           => isset($dt_put['any_surgery']) ? $dt_put['any_surgery'] : $nurse_adult[0]->any_surgery,
                        'any_surgery_date'                      => isset($dt_put['any_surgery_date']) ? $dt_put['any_surgery_date'] : $nurse_adult[0]->any_surgery_date,
                        'complication'                          => isset($dt_put['complication']) ? $dt_put['complication'] : $nurse_adult[0]->complication,
                        'history_allergy_type_id'               => isset($dt_put['history_allergy_type_id']) ? $dt_put['history_allergy_type_id'] : $nurse_adult[0]->history_allergy_type_id,
                        'history_allergy'                       => isset($dt_put['history_allergy']) ? $dt_put['history_allergy'] : $nurse_adult[0]->history_allergy,
                        'history_psychological_arry'            => isset($dt_put['history_psychological_arry']) ? implode(',',$dt_put['history_psychological_arry']) : $nurse_adult[0]->history_psychological_arry,
                        'body_mass_index'                       => isset($dt_put['body_mass_index']) ? $dt_put['body_mass_index'] : $nurse_adult[0]->body_mass_index,
                        'patient_food_intake'                   => isset($dt_put['patient_food_intake']) ? $dt_put['patient_food_intake'] : $nurse_adult[0]->patient_food_intake,
                        'patient_certain_diets'                 => isset($dt_put['patient_certain_diets']) ? $dt_put['patient_certain_diets'] : $nurse_adult[0]->patient_certain_diets,
                        'lose_weight_months'                    => isset($dt_put['lose_weight_months']) ? $dt_put['lose_weight_months'] : $nurse_adult[0]->lose_weight_months,
                        'lose_weight'                           => isset($dt_put['lose_weight']) ? $dt_put['lose_weight'] : $nurse_adult[0]->lose_weight,
                        'patient_disease_condition'             => isset($dt_put['patient_disease_condition']) ? $dt_put['patient_disease_condition'] : $nurse_adult[0]->patient_disease_condition,
                        'nutrition_total_score'                 => isset($dt_put['nutrition_total_score']) ? $dt_put['nutrition_total_score'] : $nurse_adult[0]->nutrition_total_score,
                        'sore_complaints_type_id'               => isset($dt_put['sore_complaints_type_id']) ? $dt_put['sore_complaints_type_id'] : $nurse_adult[0]->sore_complaints_type_id,
                        'sore_complaints'                       => isset($dt_put['sore_complaints']) ? $dt_put['sore_complaints'] : $nurse_adult[0]->sore_complaints,
                        'sore_goes_away_arry'                   => isset($dt_put['sore_goes_away_arry']) ? implode(',',$dt_put['sore_goes_away_arry']) : $nurse_adult[0]->sore_goes_away_arry,
                        'sore_goes_away'                        => isset($dt_put['sore_goes_away']) ? $dt_put['sore_goes_away'] : $nurse_adult[0]->sore_goes_away,
                        'sore_method_type_id'                   => isset($dt_put['sore_method_type_id']) ? $dt_put['sore_method_type_id'] : $nurse_adult[0]->sore_method_type_id,
                        'sore_method_score'                     => isset($dt_put['sore_method_score']) ? $dt_put['sore_method_score'] : $nurse_adult[0]->sore_method_score,
                        'sore_method_arry'                      => isset($dt_put['sore_method_arry']) ? implode(',',$dt_put['sore_method_arry']) : $nurse_adult[0]->sore_method_arry,
                        'tell_doctor_type_id'                   => isset($dt_put['tell_doctor_type_id']) ? $dt_put['tell_doctor_type_id'] : $nurse_adult[0]->tell_doctor_type_id,
                        'tell_doctor_date'                      => isset($dt_put['tell_doctor_date']) ? $dt_put['tell_doctor_date'] : $nurse_adult[0]->tell_doctor_date,
                        'face_score'                            => isset($dt_put['face_score']) ? $dt_put['face_score'] : $nurse_adult[0]->face_score,
                        'legs_score'                            => isset($dt_put['legs_score']) ? $dt_put['legs_score'] : $nurse_adult[0]->legs_score,
                        'activity_score'                        => isset($dt_put['activity_score']) ? $dt_put['activity_score'] : $nurse_adult[0]->activity_score,
                        'cry_score'                             => isset($dt_put['cry_score']) ? $dt_put['cry_score'] : $nurse_adult[0]->cry_score,
                        'consolability_score'                   => isset($dt_put['consolability_score']) ? $dt_put['consolability_score'] : $nurse_adult[0]->consolability_score,
                        'sore_total_score'                      => isset($dt_put['sore_total_score']) ? $dt_put['sore_total_score'] : $nurse_adult[0]->sore_total_score,
                        'duration'                              => isset($dt_put['duration']) ? $dt_put['duration'] : $nurse_adult[0]->duration,
                        'location'                              => isset($dt_put['location']) ? $dt_put['location'] : $nurse_adult[0]->location,
                        'response'                              => isset($dt_put['response']) ? $dt_put['response'] : $nurse_adult[0]->response,
                        'frequency'                             => isset($dt_put['frequency']) ? $dt_put['frequency'] : $nurse_adult[0]->frequency,
                        'fall_risk_method_type_id'              => isset($dt_put['fall_risk_method_type_id']) ? $dt_put['fall_risk_method_type_id'] : $nurse_adult[0]->fall_risk_method_type_id,
                        'fall_risk_total_score'                 => isset($dt_put['fall_risk_total_score']) ? $dt_put['fall_risk_total_score'] : $nurse_adult[0]->fall_risk_total_score,
                        'fall_risk_type_id'                     => isset($dt_put['fall_risk_type_id']) ? $dt_put['fall_risk_type_id'] : $nurse_adult[0]->fall_risk_type_id,
                        'nursing_diagnoses_arry'                => isset($dt_put['nursing_diagnoses_arry']) ? implode(',',$dt_put['nursing_diagnoses_arry']) : $nurse_adult[0]->nursing_diagnoses_arry,
                        'plan_diit'                             => isset($dt_put['plan_diit']) ? $dt_put['plan_diit'] : $nurse_adult[0]->plan_diit,
                        'plan_activity'                         => isset($dt_put['plan_activity']) ? $dt_put['plan_activity'] : $nurse_adult[0]->plan_activity,
                        'plan_medicine'                         => isset($dt_put['plan_medicine']) ? $dt_put['plan_medicine'] : $nurse_adult[0]->plan_medicine,
                        'plan_recontrol'                        => isset($dt_put['plan_recontrol']) ? $dt_put['plan_recontrol'] : $nurse_adult[0]->plan_recontrol,
                        'implementation_arry'                   => isset($dt_put['implementation_arry']) ? implode(',',$dt_put['implementation_arry']) : $nurse_adult[0]->implementation_arry,
                    );
                    $id = $this->$model->update($id, $dt_nurse_adult);
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
}