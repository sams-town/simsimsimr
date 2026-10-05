<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_child_inpatient extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
    }

    /**
     * Nurse_child_inpatient API
     * --------------------
     * @param: nurse_id
     * @param: psychological_status_arry
     * @param: psychological_status
     * @param: mental_status_arry
     * @param: mental_status_problems
     * @param: mental_status_violence
     * @param: spiritual_status_a
     * @param: spiritual_status_b
     * @param: dietary_habit
     * @param: nutrition_arry
     * @param: nutrition
     * @param: elimination_defecate
     * @param: elimination_urination
     * @param: elimination_consistency_type_id
     * @param: elimination_color_type_id
     * @param: elimination_color
     * @param: immunization_arry
     * @param: immunization
     * @param: development_history_arry
     * @param: development_smile
     * @param: development_stand
     * @param: development_prone
     * @param: development_walk
     * @param: development_sit
     * @param: development_speak
     * @param: development_crawl
     * @param: development_school
     * @param: nutritional_screening_a
     * @param: nutritional_screening_b
     * @param: nutritional_screening_c
     * @param: nutritional_screening_total_score
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
     * @param: humpty_dumpty_scale_type_id
     * @param: sensoric_vision_type_id
     * @param: sensoric_olfactory_type_id
     * @param: sensoric_hearing_type_id
     * @param: cognitive_type_id
     * @param: motor_daily_type_id
     * @param: motor_walking_type_id
     * @param: educational_needs_arry
     * @param: educational_needs
     * @param: educational_obstacle_type_id
     * @param: educational_obstacle_arry
     * @param: educational_obstacle
     * @param: translator_type_id
     * @param: translator
     * @param: criteria_patients_at_risk_arry
     * @param: needs_home_care_type_id
     * @param: needs_home_care
     * @param: needs_implant_type_id
     * @param: needs_implant
     * @param: needs_aids_type_id
     * @param: needs_aids
     * @param: needs_install_equip_type_id
     * @param: needs_install_equip
     * @param: needs_community_type_id
     * @param: needs_community
     * @param: needs_therapist_type_id
     * @param: needs_therapist
     * @param: needs_other_type_id
     * @param: needs_other
     * @param: nursing_problems_arry
     * @param: nursing_problems_a
     * @param: nursing_problems_b
     * @param: nursing_problems_c
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_child_inpatient
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
            $this->form_validation->set_rules('psychological_status_arry', 'psychological_status_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('psychological_status', 'psychological_status', 'trim|max_length[100]');
            $this->form_validation->set_rules('mental_status_arry', 'mental_status_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('mental_status_problems', 'mental_status_problems', 'trim|max_length[100]');
            $this->form_validation->set_rules('mental_status_violence', 'mental_status_violence', 'trim|max_length[100]');
            $this->form_validation->set_rules('spiritual_status_a', 'spiritual_status_a', 'trim');
            $this->form_validation->set_rules('spiritual_status_b', 'spiritual_status_b', 'trim');
            $this->form_validation->set_rules('dietary_habit', 'dietary_habit', 'trim|max_length[4]');
            $this->form_validation->set_rules('nutrition_arry', 'nutrition_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('nutrition', 'nutrition', 'trim|max_length[100]');
            $this->form_validation->set_rules('elimination_defecate', 'elimination_defecate', 'trim|max_length[4]');
            $this->form_validation->set_rules('elimination_urination', 'elimination_urination', 'trim|max_length[4]');
            $this->form_validation->set_rules('elimination_consistency_type_id', 'elimination_consistency_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('elimination_color_type_id', 'elimination_color_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('elimination_color', 'elimination_color', 'trim|max_length[100]');
            $this->form_validation->set_rules('immunization_arry', 'immunization_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('immunization', 'immunization', 'trim|max_length[100]');
            $this->form_validation->set_rules('development_history_arry', 'development_history_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('development_smile', 'development_smile', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_stand', 'development_stand', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_prone', 'development_prone', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_walk', 'development_walk', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_sit', 'development_sit', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_speak', 'development_speak', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_crawl', 'development_crawl', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_school', 'development_school', 'trim|max_length[4]');
            $this->form_validation->set_rules('nutritional_screening_a', 'nutritional_screening_a', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutritional_screening_b', 'nutritional_screening_b', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutritional_screening_c', 'nutritional_screening_c', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutritional_screening_total_score', 'nutritional_screening_total_score', 'trim|max_length[4]');
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
            $this->form_validation->set_rules('humpty_dumpty_scale_type_id', 'humpty_dumpty_scale_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sensoric_vision_type_id', 'sensoric_vision_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sensoric_olfactory_type_id', 'sensoric_olfactory_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sensoric_hearing_type_id', 'sensoric_hearing_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('cognitive_type_id', 'cognitive_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('motor_daily_type_id', 'motor_daily_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('motor_walking_type_id', 'motor_walking_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('educational_needs_arry', 'educational_needs_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('educational_needs', 'educational_needs', 'trim|max_length[100]');
            $this->form_validation->set_rules('educational_obstacle_type_id', 'educational_obstacle_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('educational_obstacle_arry', 'educational_obstacle_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('educational_obstacle', 'educational_obstacle', 'trim|max_length[100]');
            $this->form_validation->set_rules('translator_type_id', 'translator_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('translator', 'translator', 'trim|max_length[100]');
            $this->form_validation->set_rules('criteria_patients_at_risk_arry', 'criteria_patients_at_risk_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('needs_home_care_type_id', 'needs_home_care_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_home_care', 'needs_home_care', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_implant_type_id', 'needs_implant_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_implant', 'needs_implant', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_aids_type_id', 'needs_aids_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_aids', 'needs_aids', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_install_equip_type_id', 'needs_install_equip_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_install_equip', 'needs_install_equip', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_community_type_id', 'needs_community_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_community', 'needs_community', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_therapist_type_id', 'needs_therapist_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_therapist', 'needs_therapist', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_other_type_id', 'needs_other_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_other', 'needs_other', 'trim|max_length[100]');
            $this->form_validation->set_rules('nursing_problems_arry', 'nursing_problems_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('nursing_problems_a', 'nursing_problems_a', 'trim|max_length[100]');
            $this->form_validation->set_rules('nursing_problems_b', 'nursing_problems_b', 'trim|max_length[100]');
            $this->form_validation->set_rules('nursing_problems_c', 'nursing_problems_c', 'trim|max_length[100]');
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
                    $dt_nurse_child_inpatient = array(
                        'nurse_id'                          => isset($dt_post['nurse_id']) ? $dt_post['nurse_id'] : '',
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
                    $id = $this->$model->create($dt_nurse_child_inpatient);
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
     * GET Nurse_child_inpatient with API
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
    
        $nurse_child_inpatient_id = $id;
        
        $nurse_id = $q_strings['nurse_id'] ?? null;

        $where = $q_strings;
        
        $select = 'a.*';
        $where['a.is_delete'] = 0;
        if ($nurse_child_inpatient_id !== null && $nurse_child_inpatient_id !== "0" && $nurse_child_inpatient_id !== 0) {
            $output = $this->$model->select($nurse_child_inpatient_id, $select, $where);

            if (count($output) == 0) {
                $this->REST_Return(404, 'there is no nurse_child_inpatient with id ' . $nurse_child_inpatient_id);
                return;
            }
        } else {
            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id is required if no id provided');
                return;
            }
            $output = $this->$model->select(0, $select, $where);
        }

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->immunization_bcg_arry = ($output[$i]->immunization_bcg_arry != "") ? explode(',', $output[$i]->immunization_bcg_arry) : [];
            $output[$i]->immunization_hepatitis_b_arry = ($output[$i]->immunization_hepatitis_b_arry != "") ? explode(',', $output[$i]->immunization_hepatitis_b_arry) : [];
            $output[$i]->immunization_dpt_arry = ($output[$i]->immunization_dpt_arry != "") ? explode(',', $output[$i]->immunization_dpt_arry) : [];
            $output[$i]->immunization_polio_arry = ($output[$i]->immunization_polio_arry != "") ? explode(',', $output[$i]->immunization_polio_arry) : [];
            $output[$i]->immunization_measles_arry = ($output[$i]->immunization_measles_arry != "") ? explode(',', $output[$i]->immunization_measles_arry) : [];
            $output[$i]->immunization_hib_arry = ($output[$i]->immunization_hib_arry != "") ? explode(',', $output[$i]->immunization_hib_arry) : [];
            $output[$i]->immunization_ipd_arry = ($output[$i]->immunization_ipd_arry != "") ? explode(',', $output[$i]->immunization_ipd_arry) : [];
            $output[$i]->immunization_flu_arry = ($output[$i]->immunization_flu_arry != "") ? explode(',', $output[$i]->immunization_flu_arry) : [];
            $output[$i]->immunization_chickenpox_arry = ($output[$i]->immunization_chickenpox_arry != "") ? explode(',', $output[$i]->immunization_chickenpox_arry) : [];
            $output[$i]->immunization_mmr_arry = ($output[$i]->immunization_mmr_arry != "") ? explode(',', $output[$i]->immunization_mmr_arry) : [];
            $output[$i]->immunization_typhoid_arry = ($output[$i]->immunization_typhoid_arry != "") ? explode(',', $output[$i]->immunization_typhoid_arry) : [];
            $output[$i]->immunization_hepatitis_a_arry = ($output[$i]->immunization_hepatitis_a_arry != "") ? explode(',', $output[$i]->immunization_hepatitis_a_arry) : [];
            $output[$i]->immunization_hpv_arry = ($output[$i]->immunization_hpv_arry != "") ? explode(',', $output[$i]->immunization_hpv_arry) : [];
            $output[$i]->immunization_rotavirus_arry = ($output[$i]->immunization_rotavirus_arry != "") ? explode(',', $output[$i]->immunization_rotavirus_arry) : [];
            $output[$i]->immunization_influenza_arry = ($output[$i]->immunization_influenza_arry != "") ? explode(',', $output[$i]->immunization_influenza_arry) : [];
            $output[$i]->comm_needs_colloquial_arry = ($output[$i]->comm_needs_colloquial_arry != "") ? explode(',', $output[$i]->comm_needs_colloquial_arry) : [];
            $output[$i]->edu_needs_barriers_arry = ($output[$i]->edu_needs_barriers_arry != "") ? explode(',', $output[$i]->edu_needs_barriers_arry) : [];
            $output[$i]->edu_needs_preferred_arry = ($output[$i]->edu_needs_preferred_arry != "") ? explode(',', $output[$i]->edu_needs_preferred_arry) : [];
            $output[$i]->edu_needs_information_arry = ($output[$i]->edu_needs_information_arry != "") ? explode(',', $output[$i]->edu_needs_information_arry) : [];


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
     * Nurse_child_inpatient API
     * --------------------
     * @param: psychological_status_arry
     * @param: psychological_status
     * @param: mental_status_arry
     * @param: mental_status_problems
     * @param: mental_status_violence
     * @param: spiritual_status_a
     * @param: spiritual_status_b
     * @param: dietary_habit
     * @param: nutrition_arry
     * @param: nutrition
     * @param: elimination_defecate
     * @param: elimination_urination
     * @param: elimination_consistency_type_id
     * @param: elimination_color_type_id
     * @param: elimination_color
     * @param: immunization_arry
     * @param: immunization
     * @param: development_history_arry
     * @param: development_smile
     * @param: development_stand
     * @param: development_prone
     * @param: development_walk
     * @param: development_sit
     * @param: development_speak
     * @param: development_crawl
     * @param: development_school
     * @param: nutritional_screening_a
     * @param: nutritional_screening_b
     * @param: nutritional_screening_c
     * @param: nutritional_screening_total_score
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
     * @param: humpty_dumpty_scale_type_id
     * @param: sensoric_vision_type_id
     * @param: sensoric_olfactory_type_id
     * @param: sensoric_hearing_type_id
     * @param: cognitive_type_id
     * @param: motor_daily_type_id
     * @param: motor_walking_type_id
     * @param: educational_needs_arry
     * @param: educational_needs
     * @param: educational_obstacle_type_id
     * @param: educational_obstacle_arry
     * @param: educational_obstacle
     * @param: translator_type_id
     * @param: translator
     * @param: criteria_patients_at_risk_arry
     * @param: needs_home_care_type_id
     * @param: needs_home_care
     * @param: needs_implant_type_id
     * @param: needs_implant
     * @param: needs_aids_type_id
     * @param: needs_aids
     * @param: needs_install_equip_type_id
     * @param: needs_install_equip
     * @param: needs_community_type_id
     * @param: needs_community
     * @param: needs_therapist_type_id
     * @param: needs_therapist
     * @param: needs_other_type_id
     * @param: needs_other
     * @param: nursing_problems_arry
     * @param: nursing_problems_a
     * @param: nursing_problems_b
     * @param: nursing_problems_c
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_child_inpatient
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
            $this->form_validation->set_rules('psychological_status_arry', 'psychological_status_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('psychological_status', 'psychological_status', 'trim|max_length[100]');
            $this->form_validation->set_rules('mental_status_arry', 'mental_status_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('mental_status_problems', 'mental_status_problems', 'trim|max_length[100]');
            $this->form_validation->set_rules('mental_status_violence', 'mental_status_violence', 'trim|max_length[100]');
            $this->form_validation->set_rules('spiritual_status_a', 'spiritual_status_a', 'trim');
            $this->form_validation->set_rules('spiritual_status_b', 'spiritual_status_b', 'trim');
            $this->form_validation->set_rules('dietary_habit', 'dietary_habit', 'trim|max_length[4]');
            $this->form_validation->set_rules('nutrition_arry', 'nutrition_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('nutrition', 'nutrition', 'trim|max_length[100]');
            $this->form_validation->set_rules('elimination_defecate', 'elimination_defecate', 'trim|max_length[4]');
            $this->form_validation->set_rules('elimination_urination', 'elimination_urination', 'trim|max_length[4]');
            $this->form_validation->set_rules('elimination_consistency_type_id', 'elimination_consistency_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('elimination_color_type_id', 'elimination_color_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('elimination_color', 'elimination_color', 'trim|max_length[100]');
            $this->form_validation->set_rules('immunization_arry', 'immunization_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('immunization', 'immunization', 'trim|max_length[100]');
            $this->form_validation->set_rules('development_history_arry', 'development_history_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('development_smile', 'development_smile', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_stand', 'development_stand', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_prone', 'development_prone', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_walk', 'development_walk', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_sit', 'development_sit', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_speak', 'development_speak', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_crawl', 'development_crawl', 'trim|max_length[4]');
            $this->form_validation->set_rules('development_school', 'development_school', 'trim|max_length[4]');
            $this->form_validation->set_rules('nutritional_screening_a', 'nutritional_screening_a', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutritional_screening_b', 'nutritional_screening_b', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutritional_screening_c', 'nutritional_screening_c', 'trim|max_length[1]');
            $this->form_validation->set_rules('nutritional_screening_total_score', 'nutritional_screening_total_score', 'trim|max_length[4]');
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
            $this->form_validation->set_rules('humpty_dumpty_scale_type_id', 'humpty_dumpty_scale_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sensoric_vision_type_id', 'sensoric_vision_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sensoric_olfactory_type_id', 'sensoric_olfactory_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('sensoric_hearing_type_id', 'sensoric_hearing_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('cognitive_type_id', 'cognitive_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('motor_daily_type_id', 'motor_daily_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('motor_walking_type_id', 'motor_walking_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('educational_needs_arry', 'educational_needs_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('educational_needs', 'educational_needs', 'trim|max_length[100]');
            $this->form_validation->set_rules('educational_obstacle_type_id', 'educational_obstacle_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('educational_obstacle_arry', 'educational_obstacle_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('educational_obstacle', 'educational_obstacle', 'trim|max_length[100]');
            $this->form_validation->set_rules('translator_type_id', 'translator_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('translator', 'translator', 'trim|max_length[100]');
            $this->form_validation->set_rules('criteria_patients_at_risk_arry', 'criteria_patients_at_risk_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('needs_home_care_type_id', 'needs_home_care_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_home_care', 'needs_home_care', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_implant_type_id', 'needs_implant_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_implant', 'needs_implant', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_aids_type_id', 'needs_aids_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_aids', 'needs_aids', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_install_equip_type_id', 'needs_install_equip_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_install_equip', 'needs_install_equip', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_community_type_id', 'needs_community_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_community', 'needs_community', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_therapist_type_id', 'needs_therapist_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_therapist', 'needs_therapist', 'trim|max_length[100]');
            $this->form_validation->set_rules('needs_other_type_id', 'needs_other_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('needs_other', 'needs_other', 'trim|max_length[100]');
            $this->form_validation->set_rules('nursing_problems_arry', 'nursing_problems_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('nursing_problems_a', 'nursing_problems_a', 'trim|max_length[100]');
            $this->form_validation->set_rules('nursing_problems_b', 'nursing_problems_b', 'trim|max_length[100]');
            $this->form_validation->set_rules('nursing_problems_c', 'nursing_problems_c', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse_child_inpatient = $this->$model->read($id); 
                if($nurse_child_inpatient != []) {
                    $dt_nurse_child_inpatient = array(
                        'id'                                => $id,
                        'psychological_status_arry'         => isset($dt_put['psychological_status_arry']) ? implode(',',$dt_put['psychological_status_arry']) : $nurse_child_inpatient[0]->psychological_status_arry,
                        'psychological_status'              => isset($dt_put['psychological_status']) ? $dt_put['psychological_status'] : $nurse_child_inpatient[0]->psychological_status,
                        'mental_status_arry'                => isset($dt_put['mental_status_arry']) ? implode(',',$dt_put['mental_status_arry']) : $nurse_child_inpatient[0]->mental_status_arry,
                        'mental_status_problems'            => isset($dt_put['mental_status_problems']) ? $dt_put['mental_status_problems'] : $nurse_child_inpatient[0]->mental_status_problems,
                        'mental_status_violence'            => isset($dt_put['mental_status_violence']) ? $dt_put['mental_status_violence'] : $nurse_child_inpatient[0]->mental_status_violence,
                        'spiritual_status_a'                => isset($dt_put['spiritual_status_a']) ? $dt_put['spiritual_status_a'] : $nurse_child_inpatient[0]->spiritual_status_a,
                        'spiritual_status_b'                => isset($dt_put['spiritual_status_b']) ? $dt_put['spiritual_status_b'] : $nurse_child_inpatient[0]->spiritual_status_b,
                        'dietary_habit'                     => isset($dt_put['dietary_habit']) ? $dt_put['dietary_habit'] : $nurse_child_inpatient[0]->dietary_habit,
                        'nutrition_arry'                    => isset($dt_put['nutrition_arry']) ? implode(',',$dt_put['nutrition_arry']) : $nurse_child_inpatient[0]->nutrition_arry,
                        'nutrition'                         => isset($dt_put['nutrition']) ? $dt_put['nutrition'] : $nurse_child_inpatient[0]->nutrition,
                        'elimination_defecate'              => isset($dt_put['elimination_defecate']) ? $dt_put['elimination_defecate'] : $nurse_child_inpatient[0]->elimination_defecate,
                        'elimination_urination'             => isset($dt_put['elimination_urination']) ? $dt_put['elimination_urination'] : $nurse_child_inpatient[0]->elimination_urination,
                        'elimination_consistency_type_id'   => isset($dt_put['elimination_consistency_type_id']) ? $dt_put['elimination_consistency_type_id'] : $nurse_child_inpatient[0]->elimination_consistency_type_id,
                        'elimination_color_type_id'         => isset($dt_put['elimination_color_type_id']) ? $dt_put['elimination_color_type_id'] : $nurse_child_inpatient[0]->elimination_color_type_id,
                        'elimination_color'                 => isset($dt_put['elimination_color']) ? $dt_put['elimination_color'] : $nurse_child_inpatient[0]->elimination_color,
                        'immunization_arry'                 => isset($dt_put['immunization_arry']) ? implode(',',$dt_put['immunization_arry']) : $nurse_child_inpatient[0]->immunization_arry,
                        'immunization'                      => isset($dt_put['immunization']) ? $dt_put['immunization'] : $nurse_child_inpatient[0]->immunization,
                        'development_history_arry'          => isset($dt_put['development_history_arry']) ? implode(',',$dt_put['development_history_arry']) : $nurse_child_inpatient[0]->development_history_arry,
                        'development_smile'                 => isset($dt_put['development_smile']) ? $dt_put['development_smile'] : $nurse_child_inpatient[0]->development_smile,
                        'development_stand'                 => isset($dt_put['development_stand']) ? $dt_put['development_stand'] : $nurse_child_inpatient[0]->development_stand,
                        'development_prone'                 => isset($dt_put['development_prone']) ? $dt_put['development_prone'] : $nurse_child_inpatient[0]->development_prone,
                        'development_walk'                  => isset($dt_put['development_walk']) ? $dt_put['development_walk'] : $nurse_child_inpatient[0]->development_walk,
                        'development_sit'                   => isset($dt_put['development_sit']) ? $dt_put['development_sit'] : $nurse_child_inpatient[0]->development_sit,
                        'development_speak'                 => isset($dt_put['development_speak']) ? $dt_put['development_speak'] : $nurse_child_inpatient[0]->development_speak,
                        'development_crawl'                 => isset($dt_put['development_crawl']) ? $dt_put['development_crawl'] : $nurse_child_inpatient[0]->development_crawl,
                        'development_school'                => isset($dt_put['development_school']) ? $dt_put['development_school'] : $nurse_child_inpatient[0]->development_school,
                        'nutritional_screening_a'           => isset($dt_put['nutritional_screening_a']) ? $dt_put['nutritional_screening_a'] : $nurse_child_inpatient[0]->nutritional_screening_a,
                        'nutritional_screening_b'           => isset($dt_put['nutritional_screening_b']) ? $dt_put['nutritional_screening_b'] : $nurse_child_inpatient[0]->nutritional_screening_b,
                        'nutritional_screening_c'           => isset($dt_put['nutritional_screening_c']) ? $dt_put['nutritional_screening_c'] : $nurse_child_inpatient[0]->nutritional_screening_c,
                        'nutritional_screening_total_score' => isset($dt_put['nutritional_screening_total_score']) ? $dt_put['nutritional_screening_total_score'] : $nurse_child_inpatient[0]->nutritional_screening_total_score,
                        'sore_complaints_type_id'           => isset($dt_put['sore_complaints_type_id']) ? $dt_put['sore_complaints_type_id'] : $nurse_child_inpatient[0]->sore_complaints_type_id,
                        'sore_complaints'                   => isset($dt_put['sore_complaints']) ? $dt_put['sore_complaints'] : $nurse_child_inpatient[0]->sore_complaints,
                        'sore_goes_away_arry'               => isset($dt_put['sore_goes_away_arry']) ? implode(',',$dt_put['sore_goes_away_arry']) : $nurse_child_inpatient[0]->sore_goes_away_arry,
                        'sore_goes_away'                    => isset($dt_put['sore_goes_away']) ? $dt_put['sore_goes_away'] : $nurse_child_inpatient[0]->sore_goes_away,
                        'sore_method_type_id'               => isset($dt_put['sore_method_type_id']) ? $dt_put['sore_method_type_id'] : $nurse_child_inpatient[0]->sore_method_type_id,
                        'sore_method_score'                 => isset($dt_put['sore_method_score']) ? $dt_put['sore_method_score'] : $nurse_child_inpatient[0]->sore_method_score,
                        'sore_method_arry'                  => isset($dt_put['sore_method_arry']) ? implode(',',$dt_put['sore_method_arry']) : $nurse_child_inpatient[0]->sore_method_arry,
                        'tell_doctor_type_id'               => isset($dt_put['tell_doctor_type_id']) ? $dt_put['tell_doctor_type_id'] : $nurse_child_inpatient[0]->tell_doctor_type_id,
                        'tell_doctor_date'                  => isset($dt_put['tell_doctor_date']) ? $dt_put['tell_doctor_date'] : $nurse_child_inpatient[0]->tell_doctor_date,
                        'face_score'                        => isset($dt_put['face_score']) ? $dt_put['face_score'] : $nurse_child_inpatient[0]->face_score,
                        'legs_score'                        => isset($dt_put['legs_score']) ? $dt_put['legs_score'] : $nurse_child_inpatient[0]->legs_score,
                        'activity_score'                    => isset($dt_put['activity_score']) ? $dt_put['activity_score'] : $nurse_child_inpatient[0]->activity_score,
                        'cry_score'                         => isset($dt_put['cry_score']) ? $dt_put['cry_score'] : $nurse_child_inpatient[0]->cry_score,
                        'consolability_score'               => isset($dt_put['consolability_score']) ? $dt_put['consolability_score'] : $nurse_child_inpatient[0]->consolability_score,
                        'sore_total_score'                  => isset($dt_put['sore_total_score']) ? $dt_put['sore_total_score'] : $nurse_child_inpatient[0]->sore_total_score,
                        'duration'                          => isset($dt_put['duration']) ? $dt_put['duration'] : $nurse_child_inpatient[0]->duration,
                        'location'                          => isset($dt_put['location']) ? $dt_put['location'] : $nurse_child_inpatient[0]->location,
                        'response'                          => isset($dt_put['response']) ? $dt_put['response'] : $nurse_child_inpatient[0]->response,
                        'frequency'                         => isset($dt_put['frequency']) ? $dt_put['frequency'] : $nurse_child_inpatient[0]->frequency,
                        'sore_quality_arry'                 => isset($dt_put['sore_quality_arry']) ? implode(',',$dt_put['sore_quality_arry']) : $nurse_child_inpatient[0]->sore_quality_arry,
                        'sore_frequency'                    => isset($dt_put['sore_frequency']) ? $dt_put['sore_frequency'] : $nurse_child_inpatient[0]->sore_frequency,
                        'sore_occurs_arry'                  => isset($dt_put['sore_occurs_arry']) ? implode(',',$dt_put['sore_occurs_arry']) : $nurse_child_inpatient[0]->sore_occurs_arry,
                        'sore_occurs'                       => isset($dt_put['sore_occurs']) ? $dt_put['sore_occurs'] : $nurse_child_inpatient[0]->sore_occurs,
                        'humpty_dumpty_scale_type_id'       => isset($dt_put['humpty_dumpty_scale_type_id']) ? $dt_put['humpty_dumpty_scale_type_id'] : $nurse_child_inpatient[0]->humpty_dumpty_scale_type_id,
                        'sensoric_vision_type_id'           => isset($dt_put['sensoric_vision_type_id']) ? $dt_put['sensoric_vision_type_id'] : $nurse_child_inpatient[0]->sensoric_vision_type_id,
                        'sensoric_olfactory_type_id'        => isset($dt_put['sensoric_olfactory_type_id']) ? $dt_put['sensoric_olfactory_type_id'] : $nurse_child_inpatient[0]->sensoric_olfactory_type_id,
                        'sensoric_hearing_type_id'          => isset($dt_put['sensoric_hearing_type_id']) ? $dt_put['sensoric_hearing_type_id'] : $nurse_child_inpatient[0]->sensoric_hearing_type_id,
                        'cognitive_type_id'                 => isset($dt_put['cognitive_type_id']) ? $dt_put['cognitive_type_id'] : $nurse_child_inpatient[0]->cognitive_type_id,
                        'motor_daily_type_id'               => isset($dt_put['motor_daily_type_id']) ? $dt_put['motor_daily_type_id'] : $nurse_child_inpatient[0]->motor_daily_type_id,
                        'motor_walking_type_id'             => isset($dt_put['motor_walking_type_id']) ? $dt_put['motor_walking_type_id'] : $nurse_child_inpatient[0]->motor_walking_type_id,
                        'educational_needs_arry'            => isset($dt_put['educational_needs_arry']) ? implode(',',$dt_put['educational_needs_arry']) : $nurse_child_inpatient[0]->educational_needs_arry,
                        'educational_needs'                 => isset($dt_put['educational_needs']) ? $dt_put['educational_needs'] : $nurse_child_inpatient[0]->educational_needs,
                        'educational_obstacle_type_id'      => isset($dt_put['educational_obstacle_type_id']) ? $dt_put['educational_obstacle_type_id'] : $nurse_child_inpatient[0]->educational_obstacle_type_id,
                        'educational_obstacle_arry'         => isset($dt_put['educational_obstacle_arry']) ? implode(',',$dt_put['educational_obstacle_arry']) : $nurse_child_inpatient[0]->educational_obstacle_arry,
                        'educational_obstacle'              => isset($dt_put['educational_obstacle']) ? $dt_put['educational_obstacle'] : $nurse_child_inpatient[0]->educational_obstacle,
                        'translator_type_id'                => isset($dt_put['translator_type_id']) ? $dt_put['translator_type_id'] : $nurse_child_inpatient[0]->translator_type_id,
                        'translator'                        => isset($dt_put['translator']) ? $dt_put['translator'] : $nurse_child_inpatient[0]->translator,
                        'criteria_patients_at_risk_arry'    => isset($dt_put['criteria_patients_at_risk_arry']) ? implode(',',$dt_put['criteria_patients_at_risk_arry']) : $nurse_child_inpatient[0]->criteria_patients_at_risk_arry,
                        'needs_home_care_type_id'           => isset($dt_put['needs_home_care_type_id']) ? $dt_put['needs_home_care_type_id'] : $nurse_child_inpatient[0]->needs_home_care_type_id,
                        'needs_home_care'                   => isset($dt_put['needs_home_care']) ? $dt_put['needs_home_care'] : $nurse_child_inpatient[0]->needs_home_care,
                        'needs_implant_type_id'             => isset($dt_put['needs_implant_type_id']) ? $dt_put['needs_implant_type_id'] : $nurse_child_inpatient[0]->needs_implant_type_id,
                        'needs_implant'                     => isset($dt_put['needs_implant']) ? $dt_put['needs_implant'] : $nurse_child_inpatient[0]->needs_implant,
                        'needs_aids_type_id'                => isset($dt_put['needs_aids_type_id']) ? $dt_put['needs_aids_type_id'] : $nurse_child_inpatient[0]->needs_aids_type_id,
                        'needs_aids'                        => isset($dt_put['needs_aids']) ? $dt_put['needs_aids'] : $nurse_child_inpatient[0]->needs_aids,
                        'needs_install_equip_type_id'       => isset($dt_put['needs_install_equip_type_id']) ? $dt_put['needs_install_equip_type_id'] : $nurse_child_inpatient[0]->needs_install_equip_type_id,
                        'needs_install_equip'               => isset($dt_put['needs_install_equip']) ? $dt_put['needs_install_equip'] : $nurse_child_inpatient[0]->needs_install_equip,
                        'needs_community_type_id'           => isset($dt_put['needs_community_type_id']) ? $dt_put['needs_community_type_id'] : $nurse_child_inpatient[0]->needs_community_type_id,
                        'needs_community'                   => isset($dt_put['needs_community']) ? $dt_put['needs_community'] : $nurse_child_inpatient[0]->needs_community,
                        'needs_therapist_type_id'           => isset($dt_put['needs_therapist_type_id']) ? $dt_put['needs_therapist_type_id'] : $nurse_child_inpatient[0]->needs_therapist_type_id,
                        'needs_therapist'                   => isset($dt_put['needs_therapist']) ? $dt_put['needs_therapist'] : $nurse_child_inpatient[0]->needs_therapist,
                        'needs_other_type_id'               => isset($dt_put['needs_other_type_id']) ? $dt_put['needs_other_type_id'] : $nurse_child_inpatient[0]->needs_other_type_id,
                        'needs_other'                       => isset($dt_put['needs_other']) ? $dt_put['needs_other'] : $nurse_child_inpatient[0]->needs_other,
                        'nursing_problems_arry'             => isset($dt_put['nursing_problems_arry']) ? implode(',',$dt_put['nursing_problems_arry']) : $nurse_child_inpatient[0]->nursing_problems_arry,
                        'nursing_problems_a'                => isset($dt_put['nursing_problems_a']) ? $dt_put['nursing_problems_a'] : $nurse_child_inpatient[0]->nursing_problems_a,
                        'nursing_problems_b'                => isset($dt_put['nursing_problems_b']) ? $dt_put['nursing_problems_b'] : $nurse_child_inpatient[0]->nursing_problems_b,
                        'nursing_problems_c'                => isset($dt_put['nursing_problems_c']) ? $dt_put['nursing_problems_c'] : $nurse_child_inpatient[0]->nursing_problems_c,
                    );
                    $id = $this->$model->update($id, $dt_nurse_child_inpatient);
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