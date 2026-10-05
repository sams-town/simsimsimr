<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_elderly_inpatient extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
    }

    /**
     * Nurse_elderly_inpatient API
     * --------------------
     * @param: nurse_id
     * @param: illness_history_type_id
     * @param: illness_history
     * @param: allergy_history_type_id
     * @param: allergy_history
     * @param: any_surgery
     * @param: any_surgery_date
     * @param: complication
     * @param: been_hospitalized_type_id
     * @param: been_hospitalized_answer
     * @param: any_history_family_disease_type_id
     * @param: any_history_family_answer_arry
     * @param: any_history_family_answer
     * @param: dependence_type_id
     * @param: dependence
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
     * @param: sore_quality_arry
     * @param: sore_frequency
     * @param: sore_occurs_arry
     * @param: sore_occurs
     * @param: sore_affects_arry
     * @param: sore_affects
     * @param: activity_shower
     * @param: activity_clothes
     * @param: activity_mobilization
     * @param: activity_defecate
     * @param: activity_urination
     * @param: activity_eat_drink
     * @param: braden_scale_activity
     * @param: braden_scale_mobilization
     * @param: braden_scale_nutrition
     * @param: braden_scale_friction
     * @param: braden_scale_perception
     * @param: braden_scale_humidity
     * @param: braden_scale_total_score
     * @param: nutrition_total_score
     * @param: sociocultural_arry
     * @param: sociocultural_days
     * @param: sociocultural_other
     * @param: language_arry
     * @param: local_language
     * @param: other_language
     * @param: expression_emotion_arry
     * @param: relationship_family_arry
     * @param: relationship_family
     * @param: thought_process_arry
     * @param: family_understand_arry
     * @param: nursing_problems_arry
     * @param: discharge_criteria_arry
     * @param: home_planning_arry
     * @param: home_planning
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_elderly_inpatient
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
            $this->form_validation->set_rules('illness_history_type_id', 'illness_history_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('illness_history', 'illness_history', 'trim|max_length[100]');
            $this->form_validation->set_rules('allergy_history_type_id', 'allergy_history_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('allergy_history', 'allergy_history', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery', 'any_surgery', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery_date', 'any_surgery_date', 'trim');
            $this->form_validation->set_rules('complication', 'complication', 'trim|max_length[100]');
            $this->form_validation->set_rules('been_hospitalized_type_id', 'been_hospitalized_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('been_hospitalized_answer', 'been_hospitalized_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_history_family_disease_type_id', 'any_history_family_disease_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('any_history_family_answer_arry', 'any_history_family_answer_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('any_history_family_answer', 'any_history_family_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('dependence_type_id', 'dependence_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('dependence', 'dependence', 'trim|max_length[100]');
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
            $this->form_validation->set_rules('sore_total_score', 'sore_total_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[4]');
            $this->form_validation->set_rules('location', 'location', 'trim|max_length[100]');
            $this->form_validation->set_rules('response', 'response', 'trim|max_length[100]');
            $this->form_validation->set_rules('frequency', 'frequency', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_quality_arry', 'sore_quality_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_frequency', 'sore_frequency', 'trim|max_length[4]');
            $this->form_validation->set_rules('sore_occurs_arry', 'sore_occurs_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_occurs', 'sore_occurs', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_affects_arry', 'sore_affects_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_affects', 'sore_affects', 'trim|max_length[100]');
            $this->form_validation->set_rules('activity_shower', 'activity_shower', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_clothes', 'activity_clothes', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_mobilization', 'activity_mobilization', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_defecate', 'activity_defecate', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_urination', 'activity_urination', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_eat_drink', 'activity_eat_drink', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_activity', 'braden_scale_activity', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_mobilization', 'braden_scale_mobilization', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_nutrition', 'braden_scale_nutrition', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_friction', 'braden_scale_friction', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_perception', 'braden_scale_perception', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_humidity', 'braden_scale_humidity', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_total_score', 'braden_scale_total_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('nutrition_total_score', 'nutrition_total_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('sociocultural_arry', 'sociocultural_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sociocultural_days', 'sociocultural_days', 'trim|max_length[100]');
            $this->form_validation->set_rules('sociocultural_other', 'sociocultural_other', 'trim|max_length[100]');
            $this->form_validation->set_rules('language_arry', 'language_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('local_language', 'local_language', 'trim|max_length[100]');
            $this->form_validation->set_rules('other_language', 'other_language', 'trim|max_length[100]');
            $this->form_validation->set_rules('expression_emotion_arry', 'expression_emotion_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('relationship_family_arry', 'relationship_family_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('relationship_family', 'relationship_family', 'trim|max_length[100]');
            $this->form_validation->set_rules('thought_process_arry', 'thought_process_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('family_understand_arry', 'family_understand_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('nursing_problems_arry', 'nursing_problems_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('discharge_criteria_arry', 'discharge_criteria_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('home_planning_arry', 'home_planning_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('home_planning', 'home_planning', 'trim|max_length[100]');
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
                    $nurse_elderly_inpatient = array(
                        'nurse_id'                              => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
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
                    $id = $this->$model->create($nurse_elderly_inpatient);
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
     * GET Nurse_elderly_inpatient with API
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
     * Nurse_elderly_inpatient API
     * --------------------
     * @param: illness_history_type_id
     * @param: illness_history
     * @param: allergy_history_type_id
     * @param: allergy_history
     * @param: any_surgery
     * @param: any_surgery_date
     * @param: complication
     * @param: been_hospitalized_type_id
     * @param: been_hospitalized_answer
     * @param: any_history_family_disease_type_id
     * @param: any_history_family_answer_arry
     * @param: any_history_family_answer
     * @param: dependence_type_id
     * @param: dependence
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
     * @param: sore_quality_arry
     * @param: sore_frequency
     * @param: sore_occurs_arry
     * @param: sore_occurs
     * @param: sore_affects_arry
     * @param: sore_affects
     * @param: activity_shower
     * @param: activity_clothes
     * @param: activity_mobilization
     * @param: activity_defecate
     * @param: activity_urination
     * @param: activity_eat_drink
     * @param: braden_scale_activity
     * @param: braden_scale_mobilization
     * @param: braden_scale_nutrition
     * @param: braden_scale_friction
     * @param: braden_scale_perception
     * @param: braden_scale_humidity
     * @param: braden_scale_total_score
     * @param: nutrition_total_score
     * @param: sociocultural_arry
     * @param: sociocultural_days
     * @param: sociocultural_other
     * @param: language_arry
     * @param: local_language
     * @param: other_language
     * @param: expression_emotion_arry
     * @param: relationship_family_arry
     * @param: relationship_family
     * @param: thought_process_arry
     * @param: family_understand_arry
     * @param: nursing_problems_arry
     * @param: discharge_criteria_arry
     * @param: home_planning_arry
     * @param: home_planning
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_elderly_inpatient
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
            $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('illness_history_type_id', 'illness_history_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('illness_history', 'illness_history', 'trim|max_length[100]');
            $this->form_validation->set_rules('allergy_history_type_id', 'allergy_history_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('allergy_history', 'allergy_history', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery', 'any_surgery', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_surgery_date', 'any_surgery_date', 'trim');
            $this->form_validation->set_rules('complication', 'complication', 'trim|max_length[100]');
            $this->form_validation->set_rules('been_hospitalized_type_id', 'been_hospitalized_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('been_hospitalized_answer', 'been_hospitalized_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('any_history_family_disease_type_id', 'any_history_family_disease_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('any_history_family_answer_arry', 'any_history_family_answer_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('any_history_family_answer', 'any_history_family_answer', 'trim|max_length[100]');
            $this->form_validation->set_rules('dependence_type_id', 'dependence_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('dependence', 'dependence', 'trim|max_length[100]');
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
            $this->form_validation->set_rules('sore_total_score', 'sore_total_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[4]');
            $this->form_validation->set_rules('location', 'location', 'trim|max_length[100]');
            $this->form_validation->set_rules('response', 'response', 'trim|max_length[100]');
            $this->form_validation->set_rules('frequency', 'frequency', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_quality_arry', 'sore_quality_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_frequency', 'sore_frequency', 'trim|max_length[4]');
            $this->form_validation->set_rules('sore_occurs_arry', 'sore_occurs_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_occurs', 'sore_occurs', 'trim|max_length[100]');
            $this->form_validation->set_rules('sore_affects_arry', 'sore_affects_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sore_affects', 'sore_affects', 'trim|max_length[100]');
            $this->form_validation->set_rules('activity_shower', 'activity_shower', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_clothes', 'activity_clothes', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_mobilization', 'activity_mobilization', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_defecate', 'activity_defecate', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_urination', 'activity_urination', 'trim|max_length[1]');
            $this->form_validation->set_rules('activity_eat_drink', 'activity_eat_drink', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_activity', 'braden_scale_activity', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_mobilization', 'braden_scale_mobilization', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_nutrition', 'braden_scale_nutrition', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_friction', 'braden_scale_friction', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_perception', 'braden_scale_perception', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_humidity', 'braden_scale_humidity', 'trim|max_length[1]');
            $this->form_validation->set_rules('braden_scale_total_score', 'braden_scale_total_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('nutrition_total_score', 'nutrition_total_score', 'trim|max_length[4]');
            $this->form_validation->set_rules('sociocultural_arry', 'sociocultural_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('sociocultural_days', 'sociocultural_days', 'trim|max_length[100]');
            $this->form_validation->set_rules('sociocultural_other', 'sociocultural_other', 'trim|max_length[100]');
            $this->form_validation->set_rules('language_arry', 'language_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('local_language', 'local_language', 'trim|max_length[100]');
            $this->form_validation->set_rules('other_language', 'other_language', 'trim|max_length[100]');
            $this->form_validation->set_rules('expression_emotion_arry', 'expression_emotion_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('relationship_family_arry', 'relationship_family_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('relationship_family', 'relationship_family', 'trim|max_length[100]');
            $this->form_validation->set_rules('thought_process_arry', 'thought_process_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('family_understand_arry', 'family_understand_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('nursing_problems_arry', 'nursing_problems_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('discharge_criteria_arry', 'discharge_criteria_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('home_planning_arry', 'home_planning_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('home_planning', 'home_planning', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse_elderly_inpatient = $this->$model->read($id); 
                if($nurse_elderly_inpatient != []) {
                    $nurse_elderly_inpatient = array(
                        'id'                                    => $id,
                        'illness_history_type_id'               => isset($dt_put['illness_history_type_id']) ? $dt_put['illness_history_type_id'] : $nurse_elderly_inpatient[0]->illness_history_type_id,
                        'illness_history'                       => isset($dt_put['illness_history']) ? $dt_put['illness_history'] : $nurse_elderly_inpatient[0]->illness_history,
                        'allergy_history_type_id'               => isset($dt_put['allergy_history_type_id']) ? $dt_put['allergy_history_type_id'] : $nurse_elderly_inpatient[0]->allergy_history_type_id,
                        'allergy_history'                       => isset($dt_put['allergy_history']) ? $dt_put['allergy_history'] : $nurse_elderly_inpatient[0]->allergy_history,
                        'any_surgery'                           => isset($dt_put['any_surgery']) ? $dt_put['any_surgery'] : $nurse_elderly_inpatient[0]->any_surgery,
                        'any_surgery_date'                      => isset($dt_put['any_surgery_date']) ? $dt_put['any_surgery_date'] : $nurse_elderly_inpatient[0]->any_surgery_date,
                        'complication'                          => isset($dt_put['complication']) ? $dt_put['complication'] : $nurse_elderly_inpatient[0]->complication,
                        'been_hospitalized_type_id'             => isset($dt_put['been_hospitalized_type_id']) ? $dt_put['been_hospitalized_type_id'] : $nurse_elderly_inpatient[0]->been_hospitalized_type_id,
                        'been_hospitalized_answer'              => isset($dt_put['been_hospitalized_answer']) ? $dt_put['been_hospitalized_answer'] : $nurse_elderly_inpatient[0]->been_hospitalized_answer,
                        'any_history_family_disease_type_id'    => isset($dt_put['any_history_family_disease_type_id']) ? $dt_put['any_history_family_disease_type_id'] : $nurse_elderly_inpatient[0]->any_history_family_disease_type_id,
                        'any_history_family_answer_arry'        => isset($dt_put['any_history_family_answer_arry']) ? implode(',',$dt_put['any_history_family_answer_arry']) : $nurse_elderly_inpatient[0]->any_history_family_answer_arry,
                        'any_history_family_answer'             => isset($dt_put['any_history_family_answer']) ? $dt_put['any_history_family_answer'] : $nurse_elderly_inpatient[0]->any_history_family_answer,
                        'dependence_type_id'                    => isset($dt_put['dependence_type_id']) ? $dt_put['dependence_type_id'] : $nurse_elderly_inpatient[0]->dependence_type_id,
                        'dependence'                            => isset($dt_put['dependence']) ? $dt_put['dependence'] : $nurse_elderly_inpatient[0]->dependence,
                        'sore_complaints_type_id'               => isset($dt_put['sore_complaints_type_id']) ? $dt_put['sore_complaints_type_id'] : $nurse_elderly_inpatient[0]->sore_complaints_type_id,
                        'sore_complaints'                       => isset($dt_put['sore_complaints']) ? $dt_put['sore_complaints'] : $nurse_elderly_inpatient[0]->sore_complaints,
                        'sore_goes_away_arry'                   => isset($dt_put['sore_goes_away_arry']) ? implode(',',$dt_put['sore_goes_away_arry']) : $nurse_elderly_inpatient[0]->sore_goes_away_arry,
                        'sore_goes_away'                        => isset($dt_put['sore_goes_away']) ? $dt_put['sore_goes_away'] : $nurse_elderly_inpatient[0]->sore_goes_away,
                        'sore_method_type_id'                   => isset($dt_put['sore_method_type_id']) ? $dt_put['sore_method_type_id'] : $nurse_elderly_inpatient[0]->sore_method_type_id,
                        'sore_method_score'                     => isset($dt_put['sore_method_score']) ? $dt_put['sore_method_score'] : $nurse_elderly_inpatient[0]->sore_method_score,
                        'sore_method_arry'                      => isset($dt_put['sore_method_arry']) ? implode(',',$dt_put['sore_method_arry']) : $nurse_elderly_inpatient[0]->sore_method_arry,
                        'tell_doctor_type_id'                   => isset($dt_put['tell_doctor_type_id']) ? $dt_put['tell_doctor_type_id'] : $nurse_elderly_inpatient[0]->tell_doctor_type_id,
                        'tell_doctor_date'                      => isset($dt_put['tell_doctor_date']) ? $dt_put['tell_doctor_date'] : $nurse_elderly_inpatient[0]->tell_doctor_date,
                        'face_score'                            => isset($dt_put['face_score']) ? $dt_put['face_score'] : $nurse_elderly_inpatient[0]->face_score,
                        'legs_score'                            => isset($dt_put['legs_score']) ? $dt_put['legs_score'] : $nurse_elderly_inpatient[0]->legs_score,
                        'activity_score'                        => isset($dt_put['activity_score']) ? $dt_put['activity_score'] : $nurse_elderly_inpatient[0]->activity_score,
                        'cry_score'                             => isset($dt_put['cry_score']) ? $dt_put['cry_score'] : $nurse_elderly_inpatient[0]->cry_score,
                        'consolability_score'                   => isset($dt_put['consolability_score']) ? $dt_put['consolability_score'] : $nurse_elderly_inpatient[0]->consolability_score,
                        'sore_total_score'                      => isset($dt_put['sore_total_score']) ? $dt_put['sore_total_score'] : $nurse_elderly_inpatient[0]->sore_total_score,
                        'duration'                              => isset($dt_put['duration']) ? $dt_put['duration'] : $nurse_elderly_inpatient[0]->duration,
                        'location'                              => isset($dt_put['location']) ? $dt_put['location'] : $nurse_elderly_inpatient[0]->location,
                        'response'                              => isset($dt_put['response']) ? $dt_put['response'] : $nurse_elderly_inpatient[0]->response,
                        'frequency'                             => isset($dt_put['frequency']) ? $dt_put['frequency'] : $nurse_elderly_inpatient[0]->frequency,
                        'sore_quality_arry'                     => isset($dt_put['sore_quality_arry']) ? implode(',',$dt_put['sore_quality_arry']) : $nurse_elderly_inpatient[0]->sore_quality_arry,
                        'sore_frequency'                        => isset($dt_put['sore_frequency']) ? $dt_put['sore_frequency'] : $nurse_elderly_inpatient[0]->sore_frequency,
                        'sore_occurs_arry'                      => isset($dt_put['sore_occurs_arry']) ? implode(',',$dt_put['sore_occurs_arry']) : $nurse_elderly_inpatient[0]->sore_occurs_arry,
                        'sore_occurs'                           => isset($dt_put['sore_occurs']) ? $dt_put['sore_occurs'] : $nurse_elderly_inpatient[0]->sore_occurs,
                        'sore_affects_arry'                     => isset($dt_put['sore_affects_arry']) ? implode(',',$dt_put['sore_affects_arry']) : $nurse_elderly_inpatient[0]->sore_affects_arry,
                        'sore_affects'                          => isset($dt_put['sore_affects']) ? $dt_put['sore_affects'] : $nurse_elderly_inpatient[0]->sore_affects,
                        'activity_shower'                       => isset($dt_put['activity_shower']) ? $dt_put['activity_shower'] : $nurse_elderly_inpatient[0]->activity_shower,
                        'activity_clothes'                      => isset($dt_put['activity_clothes']) ? $dt_put['activity_clothes'] : $nurse_elderly_inpatient[0]->activity_clothes,
                        'activity_mobilization'                 => isset($dt_put['activity_mobilization']) ? $dt_put['activity_mobilization'] : $nurse_elderly_inpatient[0]->activity_mobilization,
                        'activity_defecate'                     => isset($dt_put['activity_defecate']) ? $dt_put['activity_defecate'] : $nurse_elderly_inpatient[0]->activity_defecate,
                        'activity_urination'                    => isset($dt_put['activity_urination']) ? $dt_put['activity_urination'] : $nurse_elderly_inpatient[0]->activity_urination,
                        'activity_eat_drink'                    => isset($dt_put['activity_eat_drink']) ? $dt_put['activity_eat_drink'] : $nurse_elderly_inpatient[0]->activity_eat_drink,
                        'braden_scale_activity'                 => isset($dt_put['braden_scale_activity']) ? $dt_put['braden_scale_activity'] : $nurse_elderly_inpatient[0]->braden_scale_activity,
                        'braden_scale_mobilization'             => isset($dt_put['braden_scale_mobilization']) ? $dt_put['braden_scale_mobilization'] : $nurse_elderly_inpatient[0]->braden_scale_mobilization,
                        'braden_scale_nutrition'                => isset($dt_put['braden_scale_nutrition']) ? $dt_put['braden_scale_nutrition'] : $nurse_elderly_inpatient[0]->braden_scale_nutrition,
                        'braden_scale_friction'                 => isset($dt_put['braden_scale_friction']) ? $dt_put['braden_scale_friction'] : $nurse_elderly_inpatient[0]->braden_scale_friction,
                        'braden_scale_perception'               => isset($dt_put['braden_scale_perception']) ? $dt_put['braden_scale_perception'] : $nurse_elderly_inpatient[0]->braden_scale_perception,
                        'braden_scale_humidity'                 => isset($dt_put['braden_scale_humidity']) ? $dt_put['braden_scale_humidity'] : $nurse_elderly_inpatient[0]->braden_scale_humidity,
                        'braden_scale_total_score'              => isset($dt_put['braden_scale_total_score']) ? $dt_put['braden_scale_total_score'] : $nurse_elderly_inpatient[0]->braden_scale_total_score,
                        'nutrition_total_score'                 => isset($dt_put['nutrition_total_score']) ? $dt_put['nutrition_total_score'] : $nurse_elderly_inpatient[0]->nutrition_total_score,
                        'sociocultural_arry'                    => isset($dt_put['sociocultural_arry']) ? implode(',',$dt_put['sociocultural_arry']) : $nurse_elderly_inpatient[0]->sociocultural_arry,
                        'sociocultural_days'                    => isset($dt_put['sociocultural_days']) ? $dt_put['sociocultural_days'] : $nurse_elderly_inpatient[0]->sociocultural_days,
                        'sociocultural_other'                   => isset($dt_put['sociocultural_other']) ? $dt_put['sociocultural_other'] : $nurse_elderly_inpatient[0]->sociocultural_other,
                        'language_arry'                         => isset($dt_put['language_arry']) ? implode(',',$dt_put['language_arry']) : $nurse_elderly_inpatient[0]->language_arry,
                        'local_language'                        => isset($dt_put['local_language']) ? $dt_put['local_language'] : $nurse_elderly_inpatient[0]->local_language,
                        'other_language'                        => isset($dt_put['other_language']) ? $dt_put['other_language'] : $nurse_elderly_inpatient[0]->other_language,
                        'expression_emotion_arry'               => isset($dt_put['expression_emotion_arry']) ? implode(',',$dt_put['expression_emotion_arry']) : $nurse_elderly_inpatient[0]->expression_emotion_arry,
                        'relationship_family_arry'              => isset($dt_put['relationship_family_arry']) ? implode(',',$dt_put['relationship_family_arry']) : $nurse_elderly_inpatient[0]->relationship_family_arry,
                        'relationship_family'                   => isset($dt_put['relationship_family']) ? $dt_put['relationship_family'] : $nurse_elderly_inpatient[0]->relationship_family,
                        'thought_process_arry'                  => isset($dt_put['thought_process_arry']) ? implode(',',$dt_put['thought_process_arry']) : $nurse_elderly_inpatient[0]->thought_process_arry,
                        'family_understand_arry'                => isset($dt_put['family_understand_arry']) ? implode(',',$dt_put['family_understand_arry']) : $nurse_elderly_inpatient[0]->family_understand_arry,
                        'nursing_problems_arry'                 => isset($dt_put['nursing_problems_arry']) ? implode(',',$dt_put['nursing_problems_arry']) : $nurse_elderly_inpatient[0]->nursing_problems_arry,
                        'discharge_criteria_arry'               => isset($dt_put['discharge_criteria_arry']) ? implode(',',$dt_put['discharge_criteria_arry']) : $nurse_elderly_inpatient[0]->discharge_criteria_arry,
                        'home_planning_arry'                    => isset($dt_put['home_planning_arry']) ? implode(',',$dt_put['home_planning_arry']) : $nurse_elderly_inpatient[0]->home_planning_arry,
                        'home_planning'                         => isset($dt_put['home_planning']) ? $dt_put['home_planning'] : $nurse_elderly_inpatient[0]->home_planning,
                    );
                    $id = $this->$model->update($id, $nurse_elderly_inpatient);
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