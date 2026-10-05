<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_emergency extends My_Controller {
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
            array('field' => 'nurse_id', 'label' => 'nurse_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Nurse_emergency API
     * --------------------
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_emergency
     **/
    // FIXME - DEPRECATED, DELETE SOON!
    // public function create_post() {
    //     // CALL JSON POST
    //     $data       = $this->json_post();
    //     // DEFINE ATTR
    //     $dt_post    = $data['dt_postput'];
    //     $device     = $data['dt_server']['HTTP_USER_AGENT'];
    //     $ip         = $this->get_client_ip();
    //     $error      = TRUE;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         # Form Validation
    //         $this->form_validation->set_rules('nurse_id', 'nurse_id', 'trim|required|max_length[36]');
    //         $this->form_validation->set_rules('sore_complaints_type_id', 'sore_complaints_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sore_complaints', 'sore_complaints', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('sore_goes_away_arry', 'sore_goes_away_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('sore_goes_away', 'sore_goes_away', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('sore_method_type_id', 'sore_method_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sore_method_score', 'sore_method_score', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('sore_method_arry', 'sore_method_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('tell_doctor_type_id', 'tell_doctor_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('tell_doctor_date', 'tell_doctor_date', 'trim');
    //         $this->form_validation->set_rules('face_score', 'face_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('legs_score', 'legs_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('activity_score', 'activity_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('cry_score', 'cry_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('consolability_score', 'consolability_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sore_total_score', 'sore_total_score', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('location', 'location', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('sore_quality_arry', 'sore_quality_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('frequency', 'frequency', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('sore_affetcs_arry', 'sore_affetcs_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('sore_affects', 'sore_affects', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('nutritional_screening_a', 'nutritional_screening_a', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('nutritional_screening_b', 'nutritional_screening_b', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('nutritional_screening_total_score', 'nutritional_screening_total_score', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('fall_risk_method_type_id', 'fall_risk_method_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sensoric_vision_type_id', 'sensoric_vision_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sensoric_olfactory_type_id', 'sensoric_olfactory_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sensoric_hearing_type_id', 'sensoric_hearing_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('cognitive_type_id', 'cognitive_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('motor_daily_type_id', 'motor_daily_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('motor_walking_type_id', 'motor_walking_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('educational_needs_arry', 'educational_needs_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('educational_needs', 'educational_needs', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('educational_obstacle_type_id', 'educational_obstacle_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('educational_obstacle_arry', 'educational_obstacle_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('educational_obstacle', 'educational_obstacle', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('translator_type_id', 'translator_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('translator', 'translator', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('criteria_patients_at_risk_arry', 'criteria_patients_at_risk_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('information_to_family_arry', 'information_to_family_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('nursing_problems_arry', 'nursing_problems_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('nursing_problems_a', 'nursing_problems_a', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('nursing_problems_b', 'nursing_problems_b', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('nursing_problems_c', 'nursing_problems_c', 'trim|max_length[100]');
    //         if ($this->form_validation->run() == FALSE){
    //             // Form Validation Errors
    //             $response_data = $this->form_validation->error_array();
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             // Check nurse_id
    //             if($this->Nurse->read($dt_post['nurse_id'], false) != []) {
    //                 $model = $this->name;
    //                 /* Start Transaction */
    //                 $this->db->trans_start();
    //                 $dt_nurse_emergency = array(
    //                     'nurse_id'                          => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
    //                     'sore_complaints_type_id'           => isset($dt_post['sore_complaints_type_id']) ? $dt_post['sore_complaints_type_id'] : '',
    //                     'sore_complaints'                   => isset($dt_post['sore_complaints']) ? $dt_post['sore_complaints'] : '',
    //                     'sore_goes_away_arry'               => isset($dt_post['sore_goes_away_arry']) ? implode(',',$dt_post['sore_goes_away_arry']) : '',
    //                     'sore_goes_away'                    => isset($dt_post['sore_goes_away']) ? $dt_post['sore_goes_away'] : '',
    //                     'sore_method_type_id'               => isset($dt_post['sore_method_type_id']) ? $dt_post['sore_method_type_id'] : '',
    //                     'sore_method_score'                 => isset($dt_post['sore_method_score']) ? $dt_post['sore_method_score'] : '',
    //                     'sore_method_arry'                  => isset($dt_post['sore_method_arry']) ? implode(',',$dt_post['sore_method_arry']) : '',
    //                     'tell_doctor_type_id'               => isset($dt_post['tell_doctor_type_id']) ? $dt_post['tell_doctor_type_id'] : '',
    //                     'tell_doctor_date'                  => isset($dt_post['tell_doctor_date']) ? $dt_post['tell_doctor_date'] : '',
    //                     'face_score'                        => isset($dt_post['face_score']) ? $dt_post['face_score'] : '',
    //                     'legs_score'                        => isset($dt_post['legs_score']) ? $dt_post['legs_score'] : '',
    //                     'activity_score'                    => isset($dt_post['activity_score']) ? $dt_post['activity_score'] : '',
    //                     'cry_score'                         => isset($dt_post['cry_score']) ? $dt_post['cry_score'] : '',
    //                     'consolability_score'               => isset($dt_post['consolability_score']) ? $dt_post['consolability_score'] : '',
    //                     'sore_total_score'                  => isset($dt_post['sore_total_score']) ? $dt_post['sore_total_score'] : '',
    //                     'duration'                          => isset($dt_post['duration']) ? $dt_post['duration'] : '',
    //                     'location'                          => isset($dt_post['location']) ? $dt_post['location'] : '',
    //                     'sore_quality_arry'                 => isset($dt_post['sore_quality_arry']) ? implode(',',$dt_post['sore_quality_arry']) : '',
    //                     'frequency'                         => isset($dt_post['frequency']) ? $dt_post['frequency'] : '',
    //                     'sore_affetcs_arry'                 => isset($dt_post['sore_affetcs_arry']) ? implode(',',$dt_post['sore_affetcs_arry']) : '',
    //                     'sore_affects'                      => isset($dt_post['sore_affects']) ? $dt_post['sore_affects'] : '',
    //                     'nutritional_screening_a'           => isset($dt_post['nutritional_screening_a']) ? $dt_post['nutritional_screening_a'] : '',
    //                     'nutritional_screening_b'           => isset($dt_post['nutritional_screening_b']) ? $dt_post['nutritional_screening_b'] : '',
    //                     'nutritional_screening_total_score' => isset($dt_post['nutritional_screening_total_score']) ? $dt_post['nutritional_screening_total_score'] : '',
    //                     'fall_risk_method_type_id'          => isset($dt_post['fall_risk_method_type_id']) ? $dt_post['fall_risk_method_type_id'] : '',
    //                     'sensoric_vision_type_id'           => isset($dt_post['sensoric_vision_type_id']) ? $dt_post['sensoric_vision_type_id'] : '',
    //                     'sensoric_olfactory_type_id'        => isset($dt_post['sensoric_olfactory_type_id']) ? $dt_post['sensoric_olfactory_type_id'] : '',
    //                     'sensoric_hearing_type_id'          => isset($dt_post['sensoric_hearing_type_id']) ? $dt_post['sensoric_hearing_type_id'] : '',
    //                     'cognitive_type_id'                 => isset($dt_post['cognitive_type_id']) ? $dt_post['cognitive_type_id'] : '',
    //                     'motor_daily_type_id'               => isset($dt_post['motor_daily_type_id']) ? $dt_post['motor_daily_type_id'] : '',
    //                     'motor_walking_type_id'             => isset($dt_post['motor_walking_type_id']) ? $dt_post['motor_walking_type_id'] : '',
    //                     'educational_needs_arry'            => isset($dt_post['educational_needs_arry']) ? implode(',',$dt_post['educational_needs_arry']) : '',
    //                     'educational_needs'                 => isset($dt_post['educational_needs']) ? $dt_post['educational_needs'] : '',
    //                     'educational_obstacle_type_id'      => isset($dt_post['educational_obstacle_type_id']) ? $dt_post['educational_obstacle_type_id'] : '',
    //                     'educational_obstacle_arry'         => isset($dt_post['educational_obstacle_arry']) ? implode(',',$dt_post['educational_obstacle_arry']) : '',
    //                     'educational_obstacle'              => isset($dt_post['educational_obstacle']) ? $dt_post['educational_obstacle'] : '',
    //                     'translator_type_id'                => isset($dt_post['translator_type_id']) ? $dt_post['translator_type_id'] : '',
    //                     'translator'                        => isset($dt_post['translator']) ? $dt_post['translator'] : '',
    //                     'criteria_patients_at_risk_arry'    => isset($dt_post['criteria_patients_at_risk_arry']) ? implode(',',$dt_post['criteria_patients_at_risk_arry']) : '',
    //                     'information_to_family_arry'        => isset($dt_post['information_to_family_arry']) ? implode(',',$dt_post['information_to_family_arry']) : '',
    //                     'nursing_problems_arry'             => isset($dt_post['nursing_problems_arry']) ? implode(',',$dt_post['nursing_problems_arry']) : '',
    //                     'nursing_problems_a'                => isset($dt_post['nursing_problems_a']) ? $dt_post['nursing_problems_a'] : '',
    //                     'nursing_problems_b'                => isset($dt_post['nursing_problems_b']) ? $dt_post['nursing_problems_b'] : '',
    //                     'nursing_problems_c'                => isset($dt_post['nursing_problems_c']) ? $dt_post['nursing_problems_c'] : '',
    //                 );
    //                 $id = $this->$model->create($dt_nurse_emergency);
    //                 $this->db->trans_complete();
    //                 /* End Transaction */
    //                 if ($this->db->trans_status() === false) {
    //                     $response_data = $this->db->trans_status();
    //                     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //                 } else {
    //                     $response_data = array(
    //                         'id'     => $id
    //                     );
    //                     $this->REST_Return(200, 'SUCCESS', $response_data);
    //                 }
    //             } else {
    //                 $this->REST_Return(404, 'Wrong nurse_id!');
    //             }
    //             return;     
    //         }
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
    // }

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
        
        /* Start Transaction */
        $this->db->trans_start();

        $been_hospitalized_type_id = $dt_post['been_hospitalized_type_id'];
        $been_hospitalized_answer = $dt_post['been_hospitalized_answer'];
        $any_history_family_disease_type_id = $dt_post['any_history_family_disease_type_id'];
        $any_history_family_answer_arry = implode(',', $dt_post['any_history_family_answer_arry']);
        $any_history_family_answer = $dt_post['any_history_family_answer'];
        $any_surgery = $dt_post['any_surgery'];
        $any_surgery_date = $dt_post['any_surgery_date'];
        $complication = $dt_post['complication'];
        $history_allergy_type_id = $dt_post['history_allergy_type_id'];
        $history_allergy = $dt_post['history_allergy'];
        $history_psychological_arry = implode(',', $dt_post['history_psychological_arry']);
        $body_mass_index = $dt_post['body_mass_index'];
        $patient_food_intake = $dt_post['patient_food_intake'];
        $patient_certain_diets = $dt_post['patient_certain_diets'];
        $six_month_lose_weight = $dt_post['six_month_lose_weight'];
        $lose_weight = $dt_post['lose_weight'];
        $patient_disease_condition = $dt_post['patient_disease_condition'];
        $nutrition_total_score = $dt_post['nutrition_total_score'];
        $fall_risk_method_type_id = $dt_post['fall_risk_method_type_id'];
        $fall_risk_type_id = $dt_post['fall_risk_type_id'];
        $nursing_diagnoses_arry = implode(',', $dt_post['nursing_diagnoses_arry']);
        $nursing_diagnoses = $dt_post['nursing_diagnoses'];
        $implementation_arry = implode(',', $dt_post['implementation_arry']);
        $implementation = $dt_post['implementation'];
        $any_surgery_type_id = $dt_post['any_surgery_type_id'];
        $fall_risk_component_a = $dt_post['fall_risk_component_a'];
        $fall_risk_component_b = $dt_post['fall_risk_component_b'];
    
        $dt_nurse_emergency = array(
            'nurse_id'                          => $nurse_id,
            'been_hospitalized_type_id'         => $been_hospitalized_type_id,
            'been_hospitalized_answer'          => $been_hospitalized_answer,
            'any_history_family_disease_type_id'=> $any_history_family_disease_type_id,
            'any_history_family_answer_arry'    => $any_history_family_answer_arry,
            'any_history_family_answer'         => $any_history_family_answer,
            'any_surgery'                       => $any_surgery,
            'any_surgery_date'                  => $any_surgery_date,
            'complication'                      => $complication,
            'history_allergy_type_id'           => $history_allergy_type_id,
            'history_allergy'                   => $history_allergy,
            'history_psychological_arry'        => $history_psychological_arry,
            'body_mass_index'                   => $body_mass_index,
            'patient_food_intake'               => $patient_food_intake,
            'patient_certain_diets'             => $patient_certain_diets,
            'six_month_lose_weight'             => $six_month_lose_weight,
            'lose_weight'                       => $lose_weight,
            'patient_disease_condition'         => $patient_disease_condition,
            'nutrition_total_score'             => $nutrition_total_score,
            'fall_risk_method_type_id'          => $fall_risk_method_type_id,
            'fall_risk_type_id'                 => $fall_risk_type_id,
            'nursing_diagnoses_arry'            => $nursing_diagnoses_arry,
            'nursing_diagnoses'                 => $nursing_diagnoses,
            'implementation_arry'               => $implementation_arry,
            'implementation'                    => $implementation,
            'any_surgery_type_id'               => $any_surgery_type_id,
            'fall_risk_component_a'             => $fall_risk_component_a,
            'fall_risk_component_b'             => $fall_risk_component_b,
            'user_added'                        => $user_id,
            'user_updated'                      => $user_id,
            'date_added'                        => date('Y-m-d H:i:s',time()),
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_nurse_emergency);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_emergency,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_emergency', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }

    /**
     * GET Nurse_emergency with API
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
    
        $nurse_emergency_id = $id;
    
        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($nurse_emergency_id);
        $response_data = array();
    
        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->any_history_family_answer_arry = explode(',', $output[$i]->any_history_family_answer_arry);
            $output[$i]->history_psychological_arry = explode(',', $output[$i]->history_psychological_arry);
            $output[$i]->nursing_diagnoses_arry = explode(',', $output[$i]->nursing_diagnoses_arry);
            $output[$i]->implementation_arry = explode(',', $output[$i]->implementation_arry);
            $output[$i]->pulmo_arry = explode(',', $output[$i]->pulmo_arry);
        }
    
        $response_data = $output;
    
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
     * Nurse_emergency API
     * --------------------
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_emergency
     **/
    // FIXME - DEPRECATED, DELETE SOON!
    // public function update_put($p = 0) {
    //     $id         = $this->security->xss_clean($p);
    //     // CALL JSON POST
    //     $data       = $this->json_put();
    //     // DEFINE ATTR
    //     $dt_put     = $data['dt_postput'];
    //     $device     = $data['dt_server']['HTTP_USER_AGENT'];
    //     $ip         = $this->get_client_ip();
    //     $error      = TRUE;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         # Form Validation
    //         $this->form_validation->set_data($dt_put);
    //         $this->form_validation->set_rules('sore_complaints_type_id', 'sore_complaints_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sore_complaints', 'sore_complaints', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('sore_goes_away_arry', 'sore_goes_away_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('sore_goes_away', 'sore_goes_away', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('sore_method_type_id', 'sore_method_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sore_method_score', 'sore_method_score', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('sore_method_arry', 'sore_method_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('tell_doctor_type_id', 'tell_doctor_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('tell_doctor_date', 'tell_doctor_date', 'trim');
    //         $this->form_validation->set_rules('face_score', 'face_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('legs_score', 'legs_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('activity_score', 'activity_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('cry_score', 'cry_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('consolability_score', 'consolability_score', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sore_total_score', 'sore_total_score', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('duration', 'duration', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('location', 'location', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('sore_quality_arry', 'sore_quality_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('frequency', 'frequency', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('sore_affetcs_arry', 'sore_affetcs_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('sore_affects', 'sore_affects', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('nutritional_screening_a', 'nutritional_screening_a', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('nutritional_screening_b', 'nutritional_screening_b', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('nutritional_screening_total_score', 'nutritional_screening_total_score', 'trim|max_length[4]');
    //         $this->form_validation->set_rules('fall_risk_method_type_id', 'fall_risk_method_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sensoric_vision_type_id', 'sensoric_vision_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sensoric_olfactory_type_id', 'sensoric_olfactory_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('sensoric_hearing_type_id', 'sensoric_hearing_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('cognitive_type_id', 'cognitive_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('motor_daily_type_id', 'motor_daily_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('motor_walking_type_id', 'motor_walking_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('educational_needs_arry', 'educational_needs_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('educational_needs', 'educational_needs', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('educational_obstacle_type_id', 'educational_obstacle_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('educational_obstacle_arry', 'educational_obstacle_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('educational_obstacle', 'educational_obstacle', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('translator_type_id', 'translator_type_id', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('translator', 'translator', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('criteria_patients_at_risk_arry', 'criteria_patients_at_risk_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('information_to_family_arry', 'information_to_family_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('nursing_problems_arry', 'nursing_problems_arry', 'trim|max_length[50]');
    //         $this->form_validation->set_rules('nursing_problems_a', 'nursing_problems_a', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('nursing_problems_b', 'nursing_problems_b', 'trim|max_length[100]');
    //         $this->form_validation->set_rules('nursing_problems_c', 'nursing_problems_c', 'trim|max_length[100]');
    //         if ($this->form_validation->run() == FALSE){
    //             // Form Validation Errors
    //             $response_data = $this->form_validation->error_array();
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $model = $this->name;
    //             /* Start Transaction */
    //             $this->db->trans_start();
    //             $nurse_emergency = $this->$model->read($id); 
    //             if($nurse_emergency != []) {
    //                 $dt_nurse_emergency = array(
    //                     'id'                                => $id,
    //                     'sore_complaints_type_id'           => isset($dt_put['sore_complaints_type_id']) ? $dt_put['sore_complaints_type_id'] : $nurse_emergency[0]->sore_complaints_type_id,
    //                     'sore_complaints'                   => isset($dt_put['sore_complaints']) ? $dt_put['sore_complaints'] : $nurse_emergency[0]->sore_complaints,
    //                     'sore_goes_away_arry'               => isset($dt_put['sore_goes_away_arry']) ? implode(',',$dt_put['sore_goes_away_arry']) : $nurse_emergency[0]->sore_goes_away_arry,
    //                     'sore_goes_away'                    => isset($dt_put['sore_goes_away']) ? $dt_put['sore_goes_away'] : $nurse_emergency[0]->sore_goes_away,
    //                     'sore_method_type_id'               => isset($dt_put['sore_method_type_id']) ? $dt_put['sore_method_type_id'] : $nurse_emergency[0]->sore_method_type_id,
    //                     'sore_method_score'                 => isset($dt_put['sore_method_score']) ? $dt_put['sore_method_score'] : $nurse_emergency[0]->sore_method_score,
    //                     'sore_method_arry'                  => isset($dt_put['sore_method_arry']) ? implode(',',$dt_put['sore_method_arry']) : $nurse_emergency[0]->sore_method_arry,
    //                     'tell_doctor_type_id'               => isset($dt_put['tell_doctor_type_id']) ? $dt_put['tell_doctor_type_id'] : $nurse_emergency[0]->tell_doctor_type_id,
    //                     'tell_doctor_date'                  => isset($dt_put['tell_doctor_date']) ? $dt_put['tell_doctor_date'] : $nurse_emergency[0]->tell_doctor_date,
    //                     'face_score'                        => isset($dt_put['face_score']) ? $dt_put['face_score'] : $nurse_emergency[0]->face_score,
    //                     'legs_score'                        => isset($dt_put['legs_score']) ? $dt_put['legs_score'] : $nurse_emergency[0]->legs_score,
    //                     'activity_score'                    => isset($dt_put['activity_score']) ? $dt_put['activity_score'] : $nurse_emergency[0]->activity_score,
    //                     'cry_score'                         => isset($dt_put['cry_score']) ? $dt_put['cry_score'] : $nurse_emergency[0]->cry_score,
    //                     'consolability_score'               => isset($dt_put['consolability_score']) ? $dt_put['consolability_score'] : $nurse_emergency[0]->consolability_score,
    //                     'sore_total_score'                  => isset($dt_put['sore_total_score']) ? $dt_put['sore_total_score'] : $nurse_emergency[0]->sore_total_score,
    //                     'duration'                          => isset($dt_put['duration']) ? $dt_put['duration'] : $nurse_emergency[0]->duration,
    //                     'location'                          => isset($dt_put['location']) ? $dt_put['location'] : $nurse_emergency[0]->location,
    //                     'sore_quality_arry'                 => isset($dt_put['sore_quality_arry']) ? implode(',',$dt_put['sore_quality_arry']) : $nurse_emergency[0]->sore_quality_arry,
    //                     'frequency'                         => isset($dt_put['frequency']) ? $dt_put['frequency'] : $nurse_emergency[0]->frequency,
    //                     'sore_affetcs_arry'                 => isset($dt_put['sore_affetcs_arry']) ? implode(',',$dt_put['sore_affetcs_arry']) : $nurse_emergency[0]->sore_affetcs_arry,
    //                     'sore_affects'                      => isset($dt_put['sore_affects']) ? $dt_put['sore_affects'] : $nurse_emergency[0]->sore_affects,
    //                     'nutritional_screening_a'           => isset($dt_put['nutritional_screening_a']) ? $dt_put['nutritional_screening_a'] : $nurse_emergency[0]->nutritional_screening_a,
    //                     'nutritional_screening_b'           => isset($dt_put['nutritional_screening_b']) ? $dt_put['nutritional_screening_b'] : $nurse_emergency[0]->nutritional_screening_b,
    //                     'nutritional_screening_total_score' => isset($dt_put['nutritional_screening_total_score']) ? $dt_put['nutritional_screening_total_score'] : $nurse_emergency[0]->nutritional_screening_total_score,
    //                     'fall_risk_method_type_id'          => isset($dt_put['fall_risk_method_type_id']) ? $dt_put['fall_risk_method_type_id'] : $nurse_emergency[0]->fall_risk_method_type_id,
    //                     'sensoric_vision_type_id'           => isset($dt_put['sensoric_vision_type_id']) ? $dt_put['sensoric_vision_type_id'] : $nurse_emergency[0]->sensoric_vision_type_id,
    //                     'sensoric_olfactory_type_id'        => isset($dt_put['sensoric_olfactory_type_id']) ? $dt_put['sensoric_olfactory_type_id'] : $nurse_emergency[0]->sensoric_olfactory_type_id,
    //                     'sensoric_hearing_type_id'          => isset($dt_put['sensoric_hearing_type_id']) ? $dt_put['sensoric_hearing_type_id'] : $nurse_emergency[0]->sensoric_hearing_type_id,
    //                     'cognitive_type_id'                 => isset($dt_put['cognitive_type_id']) ? $dt_put['cognitive_type_id'] : $nurse_emergency[0]->cognitive_type_id,
    //                     'motor_daily_type_id'               => isset($dt_put['motor_daily_type_id']) ? $dt_put['motor_daily_type_id'] : $nurse_emergency[0]->motor_daily_type_id,
    //                     'motor_walking_type_id'             => isset($dt_put['motor_walking_type_id']) ? $dt_put['motor_walking_type_id'] : $nurse_emergency[0]->motor_walking_type_id,
    //                     'educational_needs_arry'            => isset($dt_put['educational_needs_arry']) ? implode(',',$dt_put['educational_needs_arry']) : $nurse_emergency[0]->educational_needs_arry,
    //                     'educational_needs'                 => isset($dt_put['educational_needs']) ? $dt_put['educational_needs'] : $nurse_emergency[0]->educational_needs,
    //                     'educational_obstacle_type_id'      => isset($dt_put['educational_obstacle_type_id']) ? $dt_put['educational_obstacle_type_id'] : $nurse_emergency[0]->educational_obstacle_type_id,
    //                     'educational_obstacle_arry'         => isset($dt_put['educational_obstacle_arry']) ? implode(',',$dt_put['educational_obstacle_arry']) : $nurse_emergency[0]->educational_obstacle_arry,
    //                     'educational_obstacle'              => isset($dt_put['educational_obstacle']) ? $dt_put['educational_obstacle'] : $nurse_emergency[0]->educational_obstacle,
    //                     'translator_type_id'                => isset($dt_put['translator_type_id']) ? $dt_put['translator_type_id'] : $nurse_emergency[0]->translator_type_id,
    //                     'translator'                        => isset($dt_put['translator']) ? $dt_put['translator'] : $nurse_emergency[0]->translator,
    //                     'criteria_patients_at_risk_arry'    => isset($dt_put['criteria_patients_at_risk_arry']) ? implode(',',$dt_put['criteria_patients_at_risk_arry']) : $nurse_emergency[0]->criteria_patients_at_risk_arry,
    //                     'information_to_family_arry'        => isset($dt_put['information_to_family_arry']) ? implode(',',$dt_put['information_to_family_arry']) : $nurse_emergency[0]->information_to_family_arry,
    //                     'nursing_problems_arry'             => isset($dt_put['nursing_problems_arry']) ? implode(',',$dt_put['nursing_problems_arry']) : $nurse_emergency[0]->nursing_problems_arry,
    //                     'nursing_problems_a'                => isset($dt_put['nursing_problems_a']) ? $dt_put['nursing_problems_a'] : $nurse_emergency[0]->nursing_problems_a,
    //                     'nursing_problems_b'                => isset($dt_put['nursing_problems_b']) ? $dt_put['nursing_problems_b'] : $nurse_emergency[0]->nursing_problems_b,
    //                     'nursing_problems_c'                => isset($dt_put['nursing_problems_c']) ? $dt_put['nursing_problems_c'] : $nurse_emergency[0]->nursing_problems_c,
    //                 );
    //                 $id = $this->$model->update($id, $dt_nurse_emergency);
    //             } else {
    //                 $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
    //                 return;
    //             }
    //             $this->db->trans_complete();
    //             /* End Transaction */
    //             if ($this->db->trans_status() === false) {
    //                 $response_data = array();
    //                 $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //             } else {
    //                 $response_data = $id;
    //                 $this->REST_Return(200, 'SUCCESS', $response_data);
    //             }
    //             return;     
    //         }
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
    // }

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
        $nurse_emergency_id = $id;
    
        $select = 'a.id';
        $nurse_emergency = $this->$model->select($nurse_emergency_id, $select);

        if (count($nurse_emergency) == 0) {
            $this->REST_Return(404, 'there is no nurse_emergency with id ' . $nurse_emergency_id);
            return;
        }

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
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nurse_emergency = array(
            'id'                                => $nurse_emergency_id,
            'been_hospitalized_type_id'         => $been_hospitalized_type_id,
            'been_hospitalized_answer'          => $been_hospitalized_answer,
            'any_history_family_disease_type_id'=> $any_history_family_disease_type_id,
            'any_history_family_answer_arry'    => $any_history_family_answer_arry,
            'any_history_family_answer'         => $any_history_family_answer,
            'any_surgery'                       => $any_surgery,
            'any_surgery_date'                  => $any_surgery_date,
            'complication'                      => $complication,
            'history_allergy_type_id'           => $history_allergy_type_id,
            'history_allergy'                   => $history_allergy,
            'history_psychological_arry'        => $history_psychological_arry,
            'body_mass_index'                   => $body_mass_index,
            'patient_food_intake'               => $patient_food_intake,
            'patient_certain_diets'             => $patient_certain_diets,
            'six_month_lose_weight'             => $six_month_lose_weight,
            'lose_weight'                       => $lose_weight,
            'patient_disease_condition'         => $patient_disease_condition,
            'nutrition_total_score'             => $nutrition_total_score,
            'fall_risk_method_type_id'          => $fall_risk_method_type_id,
            'fall_risk_type_id'                 => $fall_risk_type_id,
            'nursing_diagnoses_arry'            => $nursing_diagnoses_arry,
            'nursing_diagnoses'                 => $nursing_diagnoses,
            'implementation_arry'               => $implementation_arry,
            'implementation'                    => $implementation,
            'any_surgery_type_id'               => $any_surgery_type_id,
            'fall_risk_component_a'             => $fall_risk_component_a,
            'fall_risk_component_b'             => $fall_risk_component_b,
            'user_updated'                      => $user_id,
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->update($nurse_emergency_id, $dt_nurse_emergency);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_emergency,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_emergency', $response_data);
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
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $nurse_emergency_id = $id;
    
        $select = 'a.id';
        $nurse_emergency = $this->$model->select($nurse_emergency_id, $select);

        if (count($nurse_emergency) == 0) {
            $this->REST_Return(404, 'there is no nurse_emergency with id ' . $nurse_emergency_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_nurse_emergency = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($nurse_emergency_id, $dt_nurse_emergency);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_emergency,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nurse_emergency', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}