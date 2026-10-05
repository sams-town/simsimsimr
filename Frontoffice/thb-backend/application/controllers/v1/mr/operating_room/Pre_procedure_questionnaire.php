<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Pre_procedure_questionnaire extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
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
    
        $pre_procedure_questionnaire_id = $id;
    
        if ($pre_procedure_questionnaire_id === 0 || $pre_procedure_questionnaire_id === "0") {
            $this->REST_Return(400, 'pre_procedure_questionnaire_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($pre_procedure_questionnaire_id);
        $response_data = array();
    
    
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
        $employee_nurse_id = $dt_post['employee_nurse_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
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

        $previous_surgery_type_id = $dt_post['previous_surgery_type_id'];
        $previous_surgery_type = $dt_post['previous_surgery_type'];
        $previous_surgery_year = $dt_post['previous_surgery_year'];
        $history_of_allergies_type_id = $dt_post['history_of_allergies_type_id'];
        $history_of_allergies = $dt_post['history_of_allergies'];
        $using_drugs_type_id = $dt_post['using_drugs_type_id'];
        $using_drugs_since = $dt_post['using_drugs_since'];
        $using_drugs_last = $dt_post['using_drugs_last'];
        $using_drugs_type = $dt_post['using_drugs_type'];
        $dentures_type_id = $dt_post['dentures_type_id'];
        $taking_medications_type_id = $dt_post['taking_medications_type_id'];
        $taking_medications_type = $dt_post['taking_medications_type'];
        $taking_medications_times = $dt_post['taking_medications_times'];
        $head_trauma_type_id = $dt_post['head_trauma_type_id'];
        $head_trauma_long = $dt_post['head_trauma_long'];
        $epilepsy_type_id = $dt_post['epilepsy_type_id'];
        $epilepsy_since = $dt_post['epilepsy_since'];
        $stroke_type_id = $dt_post['stroke_type_id'];
        $stroke_many = $dt_post['stroke_many'];
        $stroke_last = $dt_post['stroke_last'];
        $dizzy_type_id = $dt_post['dizzy_type_id'];
        $dizzy_often_type_id = $dt_post['dizzy_often_type_id'];
        $dizzy_vomit_type_id = $dt_post['dizzy_vomit_type_id'];
        $asthma_type_id = $dt_post['asthma_type_id'];
        $asthma_hospitalized_type_id = $dt_post['asthma_hospitalized_type_id'];
        $asthma_last = $dt_post['asthma_last'];
        $smoke_type_id = $dt_post['smoke_type_id'];
        $smoke_many = $dt_post['smoke_many'];
        $cough_with_blood_type_id = $dt_post['cough_with_blood_type_id'];
        $hypertension_type_id = $dt_post['hypertension_type_id'];
        $hypertension_many = $dt_post['hypertension_many'];
        $left_chest_pain_type_id = $dt_post['left_chest_pain_type_id'];
        $heart_disease_type_id = $dt_post['heart_disease_type_id'];
        $heart_disease_many = $dt_post['heart_disease_many'];
        $gastric_pains_type_id = $dt_post['gastric_pains_type_id'];
        $hepatitis_type_id = $dt_post['hepatitis_type_id'];
        $hepatitis_hospitalized_type_id = $dt_post['hepatitis_hospitalized_type_id'];
        $diabetes_type_id = $dt_post['diabetes_type_id'];
        $diabetes_hospitalized_type_id = $dt_post['diabetes_hospitalized_type_id'];
        $diabetes_many = $dt_post['diabetes_many'];
        $spinal_trauma_type_id = $dt_post['spinal_trauma_type_id'];
        $spinal_trauma_when = $dt_post['spinal_trauma_when'];
        $unspecified_disease_type_id = $dt_post['unspecified_disease_type_id'];
        $unspecified_disease = $dt_post['unspecified_disease'];
        $menstruation = $dt_post['menstruation'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_pre_procedure_questionnaire = array(
            'registration_destination_id'    => $registration_destination_id,
            'employee_nurse_id'              => $employee_nurse_id,
            'previous_surgery_type_id'       => $previous_surgery_type_id,
            'previous_surgery_type'          => $previous_surgery_type,
            'previous_surgery_year'          => $previous_surgery_year,
            'history_of_allergies_type_id'   => $history_of_allergies_type_id,
            'history_of_allergies'           => $history_of_allergies,
            'using_drugs_type_id'            => $using_drugs_type_id,
            'using_drugs_since'              => $using_drugs_since,
            'using_drugs_last'               => $using_drugs_last,
            'using_drugs_type'               => $using_drugs_type,
            'dentures_type_id'               => $dentures_type_id,
            'taking_medications_type_id'     => $taking_medications_type_id,
            'taking_medications_type'        => $taking_medications_type,
            'taking_medications_times'       => $taking_medications_times,
            'head_trauma_type_id'            => $head_trauma_type_id,
            'head_trauma_long'               => $head_trauma_long,
            'epilepsy_type_id'               => $epilepsy_type_id,
            'epilepsy_since'                 => $epilepsy_since,
            'stroke_type_id'                 => $stroke_type_id,
            'stroke_many'                    => $stroke_many,
            'stroke_last'                    => $stroke_last,
            'dizzy_type_id'                  => $dizzy_type_id,
            'dizzy_often_type_id'            => $dizzy_often_type_id,
            'dizzy_vomit_type_id'            => $dizzy_vomit_type_id,
            'asthma_type_id'                 => $asthma_type_id,
            'asthma_hospitalized_type_id'    => $asthma_hospitalized_type_id,
            'asthma_last'                    => $asthma_last,
            'smoke_type_id'                  => $smoke_type_id,
            'smoke_many'                     => $smoke_many,
            'cough_with_blood_type_id'       => $cough_with_blood_type_id,
            'hypertension_type_id'           => $hypertension_type_id,
            'hypertension_many'              => $hypertension_many,
            'left_chest_pain_type_id'        => $left_chest_pain_type_id,
            'heart_disease_type_id'          => $heart_disease_type_id,
            'heart_disease_many'             => $heart_disease_many,
            'gastric_pains_type_id'          => $gastric_pains_type_id,
            'hepatitis_type_id'              => $hepatitis_type_id,
            'hepatitis_hospitalized_type_id' => $hepatitis_hospitalized_type_id,
            'diabetes_type_id'               => $diabetes_type_id,
            'diabetes_hospitalized_type_id'  => $diabetes_hospitalized_type_id,
            'diabetes_many'                  => $diabetes_many,
            'spinal_trauma_type_id'          => $spinal_trauma_type_id,
            'spinal_trauma_when'             => $spinal_trauma_when,
            'unspecified_disease_type_id'    => $unspecified_disease_type_id,
            'unspecified_disease'            => $unspecified_disease,
            'menstruation'                   => $menstruation,
            'user_added'                     => $user_id,
            'date_added'                     => date('Y-m-d H:i:s',time()),
            'date_updated'                   => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_pre_procedure_questionnaire);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pre_procedure_questionnaire,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create pre_procedure_questionnaire', $response_data);
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
    
        $pre_procedure_questionnaire_id = $id;
    
        if ($pre_procedure_questionnaire_id === 0 || $pre_procedure_questionnaire_id === "0") {
            $this->REST_Return(400, 'pre_procedure_questionnaire_id is required');
            return;
        }

        $select = 'a.id';
        $pre_procedure_questionnaire = $this->$model->select($pre_procedure_questionnaire_id, $select);
        if (count($pre_procedure_questionnaire) == 0) {
            $this->REST_Return(404, 'there is no pre_procedure_questionnaire with id ' . $pre_procedure_questionnaire_id);
            return;
        }
    
        $previous_surgery_type_id = $dt_put['previous_surgery_type_id'];
        $previous_surgery_type = $dt_put['previous_surgery_type'];
        $previous_surgery_year = $dt_put['previous_surgery_year'];
        $history_of_allergies_type_id = $dt_put['history_of_allergies_type_id'];
        $history_of_allergies = $dt_put['history_of_allergies'];
        $using_drugs_type_id = $dt_put['using_drugs_type_id'];
        $using_drugs_since = $dt_put['using_drugs_since'];
        $using_drugs_last = $dt_put['using_drugs_last'];
        $using_drugs_type = $dt_put['using_drugs_type'];
        $dentures_type_id = $dt_put['dentures_type_id'];
        $taking_medications_type_id = $dt_put['taking_medications_type_id'];
        $taking_medications_type = $dt_put['taking_medications_type'];
        $taking_medications_times = $dt_put['taking_medications_times'];
        $head_trauma_type_id = $dt_put['head_trauma_type_id'];
        $head_trauma_long = $dt_put['head_trauma_long'];
        $epilepsy_type_id = $dt_put['epilepsy_type_id'];
        $epilepsy_since = $dt_put['epilepsy_since'];
        $stroke_type_id = $dt_put['stroke_type_id'];
        $stroke_many = $dt_put['stroke_many'];
        $stroke_last = $dt_put['stroke_last'];
        $dizzy_type_id = $dt_put['dizzy_type_id'];
        $dizzy_often_type_id = $dt_put['dizzy_often_type_id'];
        $dizzy_vomit_type_id = $dt_put['dizzy_vomit_type_id'];
        $asthma_type_id = $dt_put['asthma_type_id'];
        $asthma_hospitalized_type_id = $dt_put['asthma_hospitalized_type_id'];
        $asthma_last = $dt_put['asthma_last'];
        $smoke_type_id = $dt_put['smoke_type_id'];
        $smoke_many = $dt_put['smoke_many'];
        $cough_with_blood_type_id = $dt_put['cough_with_blood_type_id'];
        $hypertension_type_id = $dt_put['hypertension_type_id'];
        $hypertension_many = $dt_put['hypertension_many'];
        $left_chest_pain_type_id = $dt_put['left_chest_pain_type_id'];
        $heart_disease_type_id = $dt_put['heart_disease_type_id'];
        $heart_disease_many = $dt_put['heart_disease_many'];
        $gastric_pains_type_id = $dt_put['gastric_pains_type_id'];
        $hepatitis_type_id = $dt_put['hepatitis_type_id'];
        $hepatitis_hospitalized_type_id = $dt_put['hepatitis_hospitalized_type_id'];
        $diabetes_type_id = $dt_put['diabetes_type_id'];
        $diabetes_hospitalized_type_id = $dt_put['diabetes_hospitalized_type_id'];
        $diabetes_many = $dt_put['diabetes_many'];
        $spinal_trauma_type_id = $dt_put['spinal_trauma_type_id'];
        $spinal_trauma_when = $dt_put['spinal_trauma_when'];
        $unspecified_disease_type_id = $dt_put['unspecified_disease_type_id'];
        $unspecified_disease = $dt_put['unspecified_disease'];
        $menstruation = $dt_put['menstruation'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_pre_procedure_questionnaire = array(
            'previous_surgery_type_id'       => $previous_surgery_type_id,
            'previous_surgery_type'          => $previous_surgery_type,
            'previous_surgery_year'          => $previous_surgery_year,
            'history_of_allergies_type_id'   => $history_of_allergies_type_id,
            'history_of_allergies'           => $history_of_allergies,
            'using_drugs_type_id'            => $using_drugs_type_id,
            'using_drugs_since'              => $using_drugs_since,
            'using_drugs_last'               => $using_drugs_last,
            'using_drugs_type'               => $using_drugs_type,
            'dentures_type_id'               => $dentures_type_id,
            'taking_medications_type_id'     => $taking_medications_type_id,
            'taking_medications_type'        => $taking_medications_type,
            'taking_medications_times'       => $taking_medications_times,
            'head_trauma_type_id'            => $head_trauma_type_id,
            'head_trauma_long'               => $head_trauma_long,
            'epilepsy_type_id'               => $epilepsy_type_id,
            'epilepsy_since'                 => $epilepsy_since,
            'stroke_type_id'                 => $stroke_type_id,
            'stroke_many'                    => $stroke_many,
            'stroke_last'                    => $stroke_last,
            'dizzy_type_id'                  => $dizzy_type_id,
            'dizzy_often_type_id'            => $dizzy_often_type_id,
            'dizzy_vomit_type_id'            => $dizzy_vomit_type_id,
            'asthma_type_id'                 => $asthma_type_id,
            'asthma_hospitalized_type_id'    => $asthma_hospitalized_type_id,
            'asthma_last'                    => $asthma_last,
            'smoke_type_id'                  => $smoke_type_id,
            'smoke_many'                     => $smoke_many,
            'cough_with_blood_type_id'       => $cough_with_blood_type_id,
            'hypertension_type_id'           => $hypertension_type_id,
            'hypertension_many'              => $hypertension_many,
            'left_chest_pain_type_id'        => $left_chest_pain_type_id,
            'heart_disease_type_id'          => $heart_disease_type_id,
            'heart_disease_many'             => $heart_disease_many,
            'gastric_pains_type_id'          => $gastric_pains_type_id,
            'hepatitis_type_id'              => $hepatitis_type_id,
            'hepatitis_hospitalized_type_id' => $hepatitis_hospitalized_type_id,
            'diabetes_type_id'               => $diabetes_type_id,
            'diabetes_hospitalized_type_id'  => $diabetes_hospitalized_type_id,
            'diabetes_many'                  => $diabetes_many,
            'spinal_trauma_type_id'          => $spinal_trauma_type_id,
            'spinal_trauma_when'             => $spinal_trauma_when,
            'unspecified_disease_type_id'    => $unspecified_disease_type_id,
            'unspecified_disease'            => $unspecified_disease,
            'menstruation'                   => $menstruation,
            'user_updated'                   => $user_id,
            'date_updated'                   => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($pre_procedure_questionnaire_id, $dt_pre_procedure_questionnaire);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pre_procedure_questionnaire,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update pre_procedure_questionnaire', $response_data);
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
            $this->REST_Return(400, 'pre_procedure_questionnaire_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $pre_procedure_questionnaire_id = $this->$model->select_id($id);
    
        if ($pre_procedure_questionnaire_id == "") {
            $this->REST_Return(404, 'there is no pre_procedure_questionnaire with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_pre_procedure_questionnaire = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($pre_procedure_questionnaire_id, $dt_pre_procedure_questionnaire);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_pre_procedure_questionnaire,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete pre_procedure_questionnaire', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}