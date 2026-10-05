<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nutritional_care extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
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
    
        $nutritional_care_id = $id;
    
        if ($nutritional_care_id === 0 || $nutritional_care_id === "0") {
            $this->REST_Return(400, 'nutritional_care_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($nutritional_care_id);
        $response_data = array();
    
        if (count($output) > 0) {
            $output[0]->medical_history_arry = ($output[0]->medical_history_arry != '') ? explode(',', $output[0]->medical_history_arry) : [];
            $output[0]->dietary_habit_arry = ($output[0]->dietary_habit_arry != '') ? explode(',', $output[0]->dietary_habit_arry) : [];
            $output[0]->drinking_pattern_arry = ($output[0]->drinking_pattern_arry != '') ? explode(',', $output[0]->drinking_pattern_arry) : [];
            $output[0]->eating_habits_arry = ($output[0]->eating_habits_arry != '') ? explode(',', $output[0]->eating_habits_arry) : [];
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

        for ($i=0; $i < count($output->rows); $i++) { 
            $output->rows[$i]->medical_history_arry = ($output->rows[$i]->medical_history_arry != '') ? explode(',', $output->rows[$i]->medical_history_arry) : [];
            $output->rows[$i]->dietary_habit_arry = ($output->rows[$i]->dietary_habit_arry != '') ? explode(',', $output->rows[$i]->dietary_habit_arry) : [];
            $output->rows[$i]->drinking_pattern_arry = ($output->rows[$i]->drinking_pattern_arry != '') ? explode(',', $output->rows[$i]->drinking_pattern_arry) : [];
            $output->rows[$i]->eating_habits_arry = ($output->rows[$i]->eating_habits_arry != '') ? explode(',', $output->rows[$i]->eating_habits_arry) : [];
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $registration_destination_id = $dt_post['registration_destination_id'];
        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

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

        $medical_history_arry = implode(',', $dt_post['medical_history_arry']);
        $medical_history = $dt_post['medical_history'];
        $socio_economic = $dt_post['socio_economic'];
        $smoker_type_id = $dt_post['smoker_type_id'];
        $food_allergy_type_id = $dt_post['food_allergy_type_id'];
        $food_allergy = $dt_post['food_allergy'];
        $food_taboo_type_id = $dt_post['food_taboo_type_id'];
        $food_taboo = $dt_post['food_taboo'];
        $diet_type_id = $dt_post['diet_type_id'];
        $dietary_habit = $dt_post['dietary_habit'];
        $dietary_habit_arry = implode(',', $dt_post['dietary_habit_arry']);
        $drinking_pattern_day = $dt_post['drinking_pattern_day'];
        $drinking_pattern_arry = implode(',', $dt_post['drinking_pattern_arry']);
        $drinking_pattern = $dt_post['drinking_pattern'];
        $smrs_intake = $dt_post['smrs_intake'];
        $hospital_intake_type_id = $dt_post['hospital_intake_type_id'];
        $eating_habits_arry = implode(',', $dt_post['eating_habits_arry']);
        $previous_diet_experience_type_id = $dt_post['previous_diet_experience_type_id'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $bmi = $dt_post['bmi'];
        $temperature = $dt_post['temperature'];
        $nutritional_status_type_id = $dt_post['nutritional_status_type_id'];
        $normal_weight = $dt_post['normal_weight'];
        $weight_loss = $dt_post['weight_loss'];
        $weight_loss_in = $dt_post['weight_loss_in'];
        $weight_loss_in_type_id = $dt_post['weight_loss_in_type_id'];
        $biochemistry_related = $dt_post['biochemistry_related'];
        $nauseous_type_id = $dt_post['nauseous_type_id'];
        $vomit_type_id = $dt_post['vomit_type_id'];
        $appetite_type_id = $dt_post['appetite_type_id'];
        $bloated_type_id = $dt_post['bloated_type_id'];
        $constipation_type_id = $dt_post['constipation_type_id'];
        $diarrhea_type_id = $dt_post['diarrhea_type_id'];
        $swallowing_disorder_type_id = $dt_post['swallowing_disorder_type_id'];
        $chewing_disorder_type_id = $dt_post['chewing_disorder_type_id'];
        $sucking_disorder_type_id = $dt_post['sucking_disorder_type_id'];
        $edema_type_id = $dt_post['edema_type_id'];
        $other_data = $dt_post['other_data'];
        $nutritional_diagnosis = $dt_post['nutritional_diagnosis'];
        $objective = $dt_post['objective'];
        $diet_prescription = $dt_post['diet_prescription'];
        $nutrition_education_counseling = $dt_post['nutrition_education_counseling'];
        $monitoring_and_evaluation = $dt_post['monitoring_and_evaluation'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nutritional_care = array(
            'registration_destination_id'      => $registration_destination_id,
            'doctor_in_charge_id'              => $doctor_in_charge_id,
            'medical_history_arry'             => $medical_history_arry,
            'medical_history'                  => $medical_history,
            'socio_economic'                   => $socio_economic,
            'smoker_type_id'                   => $smoker_type_id,
            'food_allergy_type_id'             => $food_allergy_type_id,
            'food_allergy'                     => $food_allergy,
            'food_taboo_type_id'               => $food_taboo_type_id,
            'food_taboo'                       => $food_taboo,
            'diet_type_id'                     => $diet_type_id,
            'dietary_habit'                    => $dietary_habit,
            'dietary_habit_arry'               => $dietary_habit_arry,
            'drinking_pattern_day'             => $drinking_pattern_day,
            'drinking_pattern_arry'            => $drinking_pattern_arry,
            'drinking_pattern'                 => $drinking_pattern,
            'smrs_intake'                      => $smrs_intake,
            'hospital_intake_type_id'          => $hospital_intake_type_id,
            'eating_habits_arry'               => $eating_habits_arry,
            'previous_diet_experience_type_id' => $previous_diet_experience_type_id,
            'systolic'                         => $systolic,
            'diastolic'                        => $diastolic,
            'weight'                           => $weight,
            'height'                           => $height,
            'bmi'                              => $bmi,
            'temperature'                      => $temperature,
            'nutritional_status_type_id'       => $nutritional_status_type_id,
            'normal_weight'                    => $normal_weight,
            'weight_loss'                      => $weight_loss,
            'weight_loss_in'                   => $weight_loss_in,
            'weight_loss_in_type_id'           => $weight_loss_in_type_id,
            'biochemistry_related'             => $biochemistry_related,
            'nauseous_type_id'                 => $nauseous_type_id,
            'vomit_type_id'                    => $vomit_type_id,
            'appetite_type_id'                 => $appetite_type_id,
            'bloated_type_id'                  => $bloated_type_id,
            'constipation_type_id'             => $constipation_type_id,
            'diarrhea_type_id'                 => $diarrhea_type_id,
            'swallowing_disorder_type_id'      => $swallowing_disorder_type_id,
            'chewing_disorder_type_id'         => $chewing_disorder_type_id,
            'sucking_disorder_type_id'         => $sucking_disorder_type_id,
            'edema_type_id'                    => $edema_type_id,
            'other_data'                       => $other_data,
            'nutritional_diagnosis'            => $nutritional_diagnosis,
            'objective'                        => $objective,
            'diet_prescription'                => $diet_prescription,
            'nutrition_education_counseling'   => $nutrition_education_counseling,
            'monitoring_and_evaluation'        => $monitoring_and_evaluation,
            'user_added'                       => $user_id,
            'date_added'                       => date('Y-m-d H:i:s',time()),
            'date_updated'                     => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_nutritional_care);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutritional_care,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nutritional_care', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $nutritional_care_id = $id;
    
        if ($nutritional_care_id === 0 || $nutritional_care_id === "0") {
            $this->REST_Return(400, 'nutritional_care_id is required');
            return;
        }
    
        $select = 'a.id';
        $nutritional_care = $this->$model->select($nutritional_care_id, $select);

        if (count($nutritional_care) == 0) {
            $this->REST_Return(404, 'there is no nutritional_care with id ' . $nutritional_care_id);
            return;
        }

        $medical_history_arry = implode(',', $dt_put['medical_history_arry']);
        $medical_history = $dt_put['medical_history'];
        $socio_economic = $dt_put['socio_economic'];
        $smoker_type_id = $dt_put['smoker_type_id'];
        $food_allergy_type_id = $dt_put['food_allergy_type_id'];
        $food_allergy = $dt_put['food_allergy'];
        $food_taboo_type_id = $dt_put['food_taboo_type_id'];
        $food_taboo = $dt_put['food_taboo'];
        $diet_type_id = $dt_put['diet_type_id'];
        $dietary_habit = $dt_put['dietary_habit'];
        $dietary_habit_arry = implode(',', $dt_put['dietary_habit_arry']);
        $drinking_pattern_day = $dt_put['drinking_pattern_day'];
        $drinking_pattern_arry = implode(',', $dt_put['drinking_pattern_arry']);
        $drinking_pattern = $dt_put['drinking_pattern'];
        $smrs_intake = $dt_put['smrs_intake'];
        $hospital_intake_type_id = $dt_put['hospital_intake_type_id'];
        $eating_habits_arry = implode(',', $dt_put['eating_habits_arry']);
        $previous_diet_experience_type_id = $dt_put['previous_diet_experience_type_id'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $bmi = $dt_put['bmi'];
        $temperature = $dt_put['temperature'];
        $nutritional_status_type_id = $dt_put['nutritional_status_type_id'];
        $normal_weight = $dt_put['normal_weight'];
        $weight_loss = $dt_put['weight_loss'];
        $weight_loss_in = $dt_put['weight_loss_in'];
        $weight_loss_in_type_id = $dt_put['weight_loss_in_type_id'];
        $biochemistry_related = $dt_put['biochemistry_related'];
        $nauseous_type_id = $dt_put['nauseous_type_id'];
        $vomit_type_id = $dt_put['vomit_type_id'];
        $appetite_type_id = $dt_put['appetite_type_id'];
        $bloated_type_id = $dt_put['bloated_type_id'];
        $constipation_type_id = $dt_put['constipation_type_id'];
        $diarrhea_type_id = $dt_put['diarrhea_type_id'];
        $swallowing_disorder_type_id = $dt_put['swallowing_disorder_type_id'];
        $chewing_disorder_type_id = $dt_put['chewing_disorder_type_id'];
        $sucking_disorder_type_id = $dt_put['sucking_disorder_type_id'];
        $edema_type_id = $dt_put['edema_type_id'];
        $other_data = $dt_put['other_data'];
        $nutritional_diagnosis = $dt_put['nutritional_diagnosis'];
        $objective = $dt_put['objective'];
        $diet_prescription = $dt_put['diet_prescription'];
        $nutrition_education_counseling = $dt_put['nutrition_education_counseling'];
        $monitoring_and_evaluation = $dt_put['monitoring_and_evaluation'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nutritional_care = array(
            'medical_history_arry'             => $medical_history_arry,
            'medical_history'                  => $medical_history,
            'socio_economic'                   => $socio_economic,
            'smoker_type_id'                   => $smoker_type_id,
            'food_allergy_type_id'             => $food_allergy_type_id,
            'food_allergy'                     => $food_allergy,
            'food_taboo_type_id'               => $food_taboo_type_id,
            'food_taboo'                       => $food_taboo,
            'diet_type_id'                     => $diet_type_id,
            'dietary_habit'                    => $dietary_habit,
            'dietary_habit_arry'               => $dietary_habit_arry,
            'drinking_pattern_day'             => $drinking_pattern_day,
            'drinking_pattern_arry'            => $drinking_pattern_arry,
            'drinking_pattern'                 => $drinking_pattern,
            'smrs_intake'                      => $smrs_intake,
            'hospital_intake_type_id'          => $hospital_intake_type_id,
            'eating_habits_arry'               => $eating_habits_arry,
            'previous_diet_experience_type_id' => $previous_diet_experience_type_id,
            'systolic'                         => $systolic,
            'diastolic'                        => $diastolic,
            'weight'                           => $weight,
            'height'                           => $height,
            'bmi'                              => $bmi,
            'temperature'                      => $temperature,
            'nutritional_status_type_id'       => $nutritional_status_type_id,
            'normal_weight'                    => $normal_weight,
            'weight_loss'                      => $weight_loss,
            'weight_loss_in'                   => $weight_loss_in,
            'weight_loss_in_type_id'           => $weight_loss_in_type_id,
            'biochemistry_related'             => $biochemistry_related,
            'nauseous_type_id'                 => $nauseous_type_id,
            'vomit_type_id'                    => $vomit_type_id,
            'appetite_type_id'                 => $appetite_type_id,
            'bloated_type_id'                  => $bloated_type_id,
            'constipation_type_id'             => $constipation_type_id,
            'diarrhea_type_id'                 => $diarrhea_type_id,
            'swallowing_disorder_type_id'      => $swallowing_disorder_type_id,
            'chewing_disorder_type_id'         => $chewing_disorder_type_id,
            'sucking_disorder_type_id'         => $sucking_disorder_type_id,
            'edema_type_id'                    => $edema_type_id,
            'other_data'                       => $other_data,
            'nutritional_diagnosis'            => $nutritional_diagnosis,
            'objective'                        => $objective,
            'diet_prescription'                => $diet_prescription,
            'nutrition_education_counseling'   => $nutrition_education_counseling,
            'monitoring_and_evaluation'        => $monitoring_and_evaluation,
            'user_updated'                     => $user_id,
            'date_updated'                     => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($nutritional_care_id, $dt_nutritional_care);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutritional_care,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nutritional_care', $response_data);
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
            $this->REST_Return(400, 'nutritional_care_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $nutritional_care_id = $this->$model->select_id($id);
    
        if ($nutritional_care_id == "") {
            $this->REST_Return(404, 'there is no nutritional_care with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_nutritional_care = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($nutritional_care_id, $dt_nutritional_care);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutritional_care,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nutritional_care', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}