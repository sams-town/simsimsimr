<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Triage extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
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

        $triage_id = $id;

        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($triage_id);
        $response_data = array();

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->dead_on_arrival_arry = explode(',', $output[$i]->dead_on_arrival_arry);
            $output[$i]->airway_arry = explode(',', $output[$i]->airway_arry);
            $output[$i]->respiratory_arry = explode(',', $output[$i]->respiratory_arry);
            $output[$i]->circulation_arry = explode(',', $output[$i]->circulation_arry);
            $output[$i]->consciousness_arry = explode(',', $output[$i]->consciousness_arry);
            $output[$i]->isolation_criteria_arry = explode(',', $output[$i]->isolation_criteria_arry);
            $output[$i]->exposure_arry = explode(',', $output[$i]->exposure_arry);
            $output[$i]->psychology_arry = explode(',', $output[$i]->psychology_arry);
            $output[$i]->forward_destination_arry = explode(',', $output[$i]->forward_destination_arry);
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
            $output->rows[$i]->dead_on_arrival_arry = explode(',', $output->rows[$i]->dead_on_arrival_arry);
            $output->rows[$i]->airway_arry = explode(',', $output->rows[$i]->airway_arry);
            $output->rows[$i]->respiratory_arry = explode(',', $output->rows[$i]->respiratory_arry);
            $output->rows[$i]->circulation_arry = explode(',', $output->rows[$i]->circulation_arry);
            $output->rows[$i]->consciousness_arry = explode(',', $output->rows[$i]->consciousness_arry);
            $output->rows[$i]->isolation_criteria_arry = explode(',', $output->rows[$i]->isolation_criteria_arry);
            $output->rows[$i]->exposure_arry = explode(',', $output->rows[$i]->exposure_arry);
            $output->rows[$i]->psychology_arry = explode(',', $output->rows[$i]->psychology_arry);
            $output->rows[$i]->forward_destination_arry = explode(',', $output->rows[$i]->forward_destination_arry);
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

        $registration_destination_id = $dt_post['registration_destination_id'];

        $registration_destination = $this->Registration_destination->select_id($registration_destination_id);

        if ($registration_destination == "") {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];

        $employee = $this->Employee->select_id($doctor_in_charge_id);

        if ($employee == "") {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $case_type_id = $dt_post['case_type_id'];
        $eye_response = $dt_post['eye_response'];
        $verbal_response = $dt_post['verbal_response'];
        $motor_response = $dt_post['motor_response'];
        $systolic_blood_pressure = $dt_post['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_post['diastolic_blood_pressure'];
        $capillary_refill_time = $dt_post['capillary_refill_time'];
        $breath_frequency = $dt_post['breath_frequency'];
        $pulse_frequency = $dt_post['pulse_frequency'];
        $temperature = $dt_post['temperature'];
        $deformity_type_id = $dt_post['deformity_type_id'];
        $deformity = $dt_post['deformity'];
        $contusion_type_id = $dt_post['contusion_type_id'];
        $contusion = $dt_post['contusion'];
        $abrasion_type_id = $dt_post['abrasion_type_id'];
        $abrasion = $dt_post['abrasion'];
        $penetration_type_id = $dt_post['penetration_type_id'];
        $penetration = $dt_post['penetration'];
        $laceration_type_id = $dt_post['laceration_type_id'];
        $laceration = $dt_post['laceration'];
        $wound_depth_type_id = $dt_post['wound_depth_type_id'];
        $wound_depth = $dt_post['wound_depth'];
        $anythingelse = $dt_post['anythingelse'];
        $dead_on_arrival_arry = implode(',', $dt_post['dead_on_arrival_arry']);
        $doa_time = $dt_post['doa_time'];
        $die_time = $dt_post['die_time'];
        $airway_type_id = $dt_post['airway_type_id'];
        $airway_arry = implode(',', $dt_post['airway_arry']);
        $wong_baker_score = $dt_post['wong_baker_score'];
        $respiratory_arry = implode(',', $dt_post['respiratory_arry']);
        $circulation_arry = implode(',', $dt_post['circulation_arry']);
        $consciousness_arry = implode(',', $dt_post['consciousness_arry']);
        $triage_result = $dt_post['triage_result'];
        $isolation_criteria_arry = implode(',', $dt_post['isolation_criteria_arry']);
        $isolation_criteria = $dt_post['isolation_criteria'];
        $isolation_type_id = $dt_post['isolation_type_id'];
        $exposure_arry = implode(',', $dt_post['exposure_arry']);
        $psychology_arry = implode(',', $dt_post['psychology_arry']);
        $forward_destination_arry = implode(',', $dt_post['forward_destination_arry']);

        $dt_triage = array(
            'registration_destination_id'   => $registration_destination_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'case_type_id'                  => $case_type_id,
            'eye_response'                  => $eye_response,
            'verbal_response'               => $verbal_response,
            'motor_response'                => $motor_response,
            'systolic_blood_pressure'       => $systolic_blood_pressure,
            'diastolic_blood_pressure'      => $diastolic_blood_pressure,
            'capillary_refill_time'         => $capillary_refill_time,
            'breath_frequency'              => $breath_frequency,
            'pulse_frequency'               => $pulse_frequency,
            'temperature'                   => $temperature,
            'deformity_type_id'             => $deformity_type_id,
            'deformity'                     => $deformity,
            'contusion_type_id'             => $contusion_type_id,
            'contusion'                     => $contusion,
            'abrasion_type_id'              => $abrasion_type_id,
            'abrasion'                      => $abrasion,
            'penetration_type_id'           => $penetration_type_id,
            'penetration'                   => $penetration,
            'laceration_type_id'            => $laceration_type_id,
            'laceration'                    => $laceration,
            'wound_depth_type_id'           => $wound_depth_type_id,
            'wound_depth'                   => $wound_depth,
            'anythingelse'                  => $anythingelse,
            'dead_on_arrival_arry'          => $dead_on_arrival_arry,
            'doa_time'                      => $doa_time,
            'die_time'                      => $die_time,
            'airway_type_id'                => $airway_type_id,
            'airway_arry'                   => $airway_arry,
            'wong_baker_score'              => $wong_baker_score,
            'respiratory_arry'              => $respiratory_arry,
            'circulation_arry'              => $circulation_arry,
            'consciousness_arry'            => $consciousness_arry,
            'triage_result'                 => $triage_result,
            'isolation_criteria_arry'       => $isolation_criteria_arry,
            'isolation_criteria'            => $isolation_criteria,
            'isolation_type_id'             => $isolation_type_id,
            'exposure_arry'                 => $exposure_arry,
            'psychology_arry'               => $psychology_arry,
            'forward_destination_arry'      => $forward_destination_arry,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $id = $this->$model->create($dt_triage);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_triage,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create triage', $response_data);
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
        $id         = $this->security->xss_clean($p);
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        $triage_id = $id;
    
        $triage = $this->$model->select_id($triage_id);
    
        if ($triage == "") {
            $this->REST_Return(404, 'triage with id ' . $triage_id . ' does not exist!');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();
    
        $case_type_id = $dt_put['case_type_id'];
        $eye_response = $dt_put['eye_response'];
        $verbal_response = $dt_put['verbal_response'];
        $motor_response = $dt_put['motor_response'];
        $systolic_blood_pressure = $dt_put['systolic_blood_pressure'];
        $diastolic_blood_pressure = $dt_put['diastolic_blood_pressure'];
        $capillary_refill_time = $dt_put['capillary_refill_time'];
        $breath_frequency = $dt_put['breath_frequency'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $temperature = $dt_put['temperature'];
        $deformity_type_id = $dt_put['deformity_type_id'];
        $deformity = $dt_put['deformity'];
        $contusion_type_id = $dt_put['contusion_type_id'];
        $contusion = $dt_put['contusion'];
        $abrasion_type_id = $dt_put['abrasion_type_id'];
        $abrasion = $dt_put['abrasion'];
        $penetration_type_id = $dt_put['penetration_type_id'];
        $penetration = $dt_put['penetration'];
        $laceration_type_id = $dt_put['laceration_type_id'];
        $laceration = $dt_put['laceration'];
        $wound_depth_type_id = $dt_put['wound_depth_type_id'];
        $wound_depth = $dt_put['wound_depth'];
        $anythingelse = $dt_put['anythingelse'];
        $dead_on_arrival_arry = implode(',', $dt_put['dead_on_arrival_arry']);
        $doa_time = $dt_put['doa_time'];
        $die_time = $dt_put['die_time'];
        $airway_type_id = $dt_put['airway_type_id'];
        $airway_arry = implode(',', $dt_put['airway_arry']);
        $wong_baker_score = $dt_put['wong_baker_score'];
        $respiratory_arry = implode(',', $dt_put['respiratory_arry']);
        $circulation_arry = implode(',', $dt_put['circulation_arry']);
        $consciousness_arry = implode(',', $dt_put['consciousness_arry']);
        $triage_result = $dt_put['triage_result'];
        $isolation_criteria_arry = implode(',', $dt_put['isolation_criteria_arry']);
        $isolation_criteria = $dt_put['isolation_criteria'];
        $isolation_type_id = $dt_put['isolation_type_id'];
        $exposure_arry = implode(',', $dt_put['exposure_arry']);
        $psychology_arry = implode(',', $dt_put['psychology_arry']);
        $forward_destination_arry = implode(',', $dt_put['forward_destination_arry']);
    
        $dt_triage = array(
            'case_type_id'                  => $case_type_id,
            'eye_response'                  => $eye_response,
            'verbal_response'               => $verbal_response,
            'motor_response'                => $motor_response,
            'systolic_blood_pressure'       => $systolic_blood_pressure,
            'diastolic_blood_pressure'      => $diastolic_blood_pressure,
            'capillary_refill_time'         => $capillary_refill_time,
            'breath_frequency'              => $breath_frequency,
            'pulse_frequency'               => $pulse_frequency,
            'temperature'                   => $temperature,
            'deformity_type_id'             => $deformity_type_id,
            'deformity'                     => $deformity,
            'contusion_type_id'             => $contusion_type_id,
            'contusion'                     => $contusion,
            'abrasion_type_id'              => $abrasion_type_id,
            'abrasion'                      => $abrasion,
            'penetration_type_id'           => $penetration_type_id,
            'penetration'                   => $penetration,
            'laceration_type_id'            => $laceration_type_id,
            'laceration'                    => $laceration,
            'wound_depth_type_id'           => $wound_depth_type_id,
            'wound_depth'                   => $wound_depth,
            'anythingelse'                  => $anythingelse,
            'dead_on_arrival_arry'          => $dead_on_arrival_arry,
            'doa_time'                      => $doa_time,
            'die_time'                      => $die_time,
            'airway_type_id'                => $airway_type_id,
            'airway_arry'                   => $airway_arry,
            'wong_baker_score'              => $wong_baker_score,
            'respiratory_arry'              => $respiratory_arry,
            'circulation_arry'              => $circulation_arry,
            'consciousness_arry'            => $consciousness_arry,
            'triage_result'                 => $triage_result,
            'isolation_criteria_arry'       => $isolation_criteria_arry,
            'isolation_criteria'            => $isolation_criteria,
            'isolation_type_id'             => $isolation_type_id,
            'exposure_arry'                 => $exposure_arry,
            'psychology_arry'               => $psychology_arry,
            'forward_destination_arry'      => $forward_destination_arry,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->update($triage_id, $dt_triage);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_triage,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update triage', $response_data);
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
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $triage_id = $this->$model->select_id($id);
    
        if ($triage_id == "") {
            $this->REST_Return(404, 'there is no triage_id with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_triage = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($triage_id, $dt_triage);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_triage,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update triage', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }


}