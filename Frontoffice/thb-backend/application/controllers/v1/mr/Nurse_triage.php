<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_triage extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
    }

    /**
     * Nurse_triage API
     * --------------------
     * @param: nurse_id
     * @param: case_type_id
     * @param: eye_response
     * @param: verbal_response
     * @param: motor_response
     * @param: systolic_blood_pressure
     * @param: diastolic_blood_pressure
     * @param: capillary_refill_time
     * @param: breath_frequency
     * @param: pulse_frequency
     * @param: temperature
     * @param: blood_oxygen
     * @param: deformity_type_id
     * @param: deformity
     * @param: contusion_type_id
     * @param: contusion
     * @param: abrasion_type_id
     * @param: abrasion
     * @param: penetration_type_id
     * @param: penetration
     * @param: laceration_type_id
     * @param: laceration
     * @param: wound_depth_type_id
     * @param: wound_depth
     * @param: anythingelse
     * @param: dead_on_arrival_arry
     * @param: doa_time
     * @param: die_time
     * @param: airway_type_id
     * @param: respiratory_arry
     * @param: circulation_arry
     * @param: consciousness_arry
     * @param: triage_result
     * @param: isolation_criteria_arry
     * @param: isolation_criteria
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_triage
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
            $this->form_validation->set_rules('case_type_id', 'case_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('eye_response', 'eye_response', 'trim|max_length[1]');
            $this->form_validation->set_rules('verbal_response', 'verbal_response', 'trim|max_length[1]');
            $this->form_validation->set_rules('motor_response', 'motor_response', 'trim|max_length[1]');
            $this->form_validation->set_rules('systolic_blood_pressure', 'systolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('diastolic_blood_pressure', 'diastolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('capillary_refill_time', 'capillary_refill_time', 'trim|max_length[1]');
            $this->form_validation->set_rules('breath_frequency', 'breath_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('pulse_frequency', 'pulse_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('temperature', 'temperature', 'trim|max_length[8]');
            $this->form_validation->set_rules('blood_oxygen', 'blood_oxygen', 'trim|max_length[8]');
            $this->form_validation->set_rules('deformity_type_id', 'deformity_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('deformity', 'deformity', 'trim|max_length[100]');
            $this->form_validation->set_rules('contusion_type_id', 'contusion_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('contusion', 'contusion', 'trim|max_length[100]');
            $this->form_validation->set_rules('abrasion_type_id', 'abrasion_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('abrasion', 'abrasion', 'trim|max_length[100]');
            $this->form_validation->set_rules('penetration_type_id', 'penetration_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('penetration', 'penetration', 'trim|max_length[100]');
            $this->form_validation->set_rules('laceration_type_id', 'laceration_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('laceration', 'laceration', 'trim|max_length[100]');
            $this->form_validation->set_rules('wound_depth_type_id', 'wound_depth_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('wound_depth', 'wound_depth', 'trim|max_length[100]');
            $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim|max_length[100]');
            $this->form_validation->set_rules('dead_on_arrival_arry', 'dead_on_arrival_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('doa_time', 'doa_time', 'trim');
            $this->form_validation->set_rules('die_time', 'die_time', 'trim');
            $this->form_validation->set_rules('airway_type_id', 'airway_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('respiratory_arry', 'respiratory_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('circulation_arry', 'circulation_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('consciousness_arry', 'consciousness_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('triage_result', 'triage_result', 'trim|max_length[100]');
            $this->form_validation->set_rules('isolation_criteria_arry', 'isolation_criteria_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('isolation_criteria', 'isolation_criteria', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check nurse_id
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check nurse_id
                if($this->Nurse->read($dt_post['nurse_id'],false) != []) {
                    $dt_nurse_triage = array(
                        'nurse_id'                  => $dt_post['nurse_id'],
                        'case_type_id'              => isset($dt_post['case_type_id']) ? $dt_post['case_type_id'] : '',
                        'eye_response'              => isset($dt_post['eye_response']) ? $dt_post['eye_response'] : '',
                        'verbal_response'           => isset($dt_post['verbal_response']) ? $dt_post['verbal_response'] : '',
                        'motor_response'            => isset($dt_post['motor_response']) ? $dt_post['motor_response'] : '',
                        'systolic_blood_pressure'   => isset($dt_post['systolic_blood_pressure']) ? $dt_post['systolic_blood_pressure'] : '',
                        'diastolic_blood_pressure'  => isset($dt_post['diastolic_blood_pressure']) ? $dt_post['diastolic_blood_pressure'] : '',
                        'capillary_refill_time'     => isset($dt_post['capillary_refill_time']) ? $dt_post['capillary_refill_time'] : '',
                        'breath_frequency'          => isset($dt_post['breath_frequency']) ? $dt_post['breath_frequency'] : '',
                        'pulse_frequency'           => isset($dt_post['pulse_frequency']) ? $dt_post['pulse_frequency'] : '',
                        'temperature'               => isset($dt_post['temperature']) ? $dt_post['temperature'] : '',
                        'blood_oxygen'              => isset($dt_post['blood_oxygen']) ? $dt_post['blood_oxygen'] : '',
                        'deformity_type_id'         => isset($dt_post['deformity_type_id']) ? $dt_post['deformity_type_id'] : '',
                        'deformity'                 => isset($dt_post['deformity']) ? $dt_post['deformity'] : '',
                        'contusion_type_id'         => isset($dt_post['contusion_type_id']) ? $dt_post['contusion_type_id'] : '',
                        'contusion'                 => isset($dt_post['contusion']) ? $dt_post['contusion'] : '',
                        'abrasion_type_id'          => isset($dt_post['abrasion_type_id']) ? $dt_post['abrasion_type_id'] : '',
                        'abrasion'                  => isset($dt_post['abrasion']) ? $dt_post['abrasion'] : '',
                        'penetration_type_id'       => isset($dt_post['penetration_type_id']) ? $dt_post['penetration_type_id'] : '',
                        'penetration'               => isset($dt_post['penetration']) ? $dt_post['penetration'] : '',
                        'laceration_type_id'        => isset($dt_post['laceration_type_id']) ? $dt_post['laceration_type_id'] : '',
                        'laceration'                => isset($dt_post['laceration']) ? $dt_post['laceration'] : '',
                        'wound_depth_type_id'       => isset($dt_post['wound_depth_type_id']) ? $dt_post['wound_depth_type_id'] : '',
                        'wound_depth'               => isset($dt_post['wound_depth']) ? $dt_post['wound_depth'] : '',
                        'anythingelse'              => isset($dt_post['anythingelse']) ? $dt_post['anythingelse'] : '',
                        'dead_on_arrival_arry'      => isset($dt_post['dead_on_arrival_arry']) ? implode(',', $dt_post['dead_on_arrival_arry']) : '',
                        'doa_time'                  => isset($dt_post['doa_time']) ? $dt_post['doa_time'] : '',
                        'die_time'                  => isset($dt_post['die_time']) ? $dt_post['die_time'] : '',
                        'airway_type_id'            => isset($dt_post['airway_type_id']) ? $dt_post['airway_type_id'] : '',
                        'respiratory_arry'          => isset($dt_post['respiratory_arry']) ? implode(',', $dt_post['respiratory_arry']) : '',
                        'circulation_arry'          => isset($dt_post['circulation_arry']) ? implode(',', $dt_post['circulation_arry']) : '',
                        'consciousness_arry'        => isset($dt_post['consciousness_arry']) ? implode(',', $dt_post['consciousness_arry']) : '',
                        'triage_result'             => isset($dt_post['triage_result']) ? $dt_post['triage_result'] : '',
                        'isolation_criteria_arry'   => isset($dt_post['isolation_criteria_arry']) ? implode(',', $dt_post['isolation_criteria_arry']) : '',
                        'isolation_criteria'        => isset($dt_post['isolation_criteria']) ? $dt_post['isolation_criteria'] : '',
                        'date_added'                => date('Y-m-d H:i:s',time()),
                    );
                    $id = $this->$model->create($dt_nurse_triage);
                } else {
                    $this->REST_Return(404, 'Wrong nurse_id!');
                    return;
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
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Nurse_triage with API
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
     * Nurse_triage API
     * --------------------
     * @param: case_type_id
     * @param: eye_response
     * @param: verbal_response
     * @param: motor_response
     * @param: systolic_blood_pressure
     * @param: diastolic_blood_pressure
     * @param: capillary_refill_time
     * @param: breath_frequency
     * @param: pulse_frequency
     * @param: temperature
     * @param: blood_oxygen
     * @param: deformity_type_id
     * @param: deformity
     * @param: contusion_type_id
     * @param: contusion
     * @param: abrasion_type_id
     * @param: abrasion
     * @param: penetration_type_id
     * @param: penetration
     * @param: laceration_type_id
     * @param: laceration
     * @param: wound_depth_type_id
     * @param: wound_depth
     * @param: anythingelse
     * @param: dead_on_arrival_arry
     * @param: doa_time
     * @param: die_time
     * @param: airway_type_id
     * @param: respiratory_arry
     * @param: circulation_arry
     * @param: consciousness_arry
     * @param: triage_result
     * @param: isolation_criteria_arry
     * @param: isolation_criteria
     * --------------------
     * @method : POST
     * @link: version/mr/nurse_triage
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
            $this->form_validation->set_rules('case_type_id', 'case_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('eye_response', 'eye_response', 'trim|max_length[1]');
            $this->form_validation->set_rules('verbal_response', 'verbal_response', 'trim|max_length[1]');
            $this->form_validation->set_rules('motor_response', 'motor_response', 'trim|max_length[1]');
            $this->form_validation->set_rules('systolic_blood_pressure', 'systolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('diastolic_blood_pressure', 'diastolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('capillary_refill_time', 'capillary_refill_time', 'trim|max_length[1]');
            $this->form_validation->set_rules('breath_frequency', 'breath_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('pulse_frequency', 'pulse_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('temperature', 'temperature', 'trim|max_length[8]');
            $this->form_validation->set_rules('blood_oxygen', 'blood_oxygen', 'trim|max_length[8]');
            $this->form_validation->set_rules('deformity_type_id', 'deformity_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('deformity', 'deformity', 'trim|max_length[100]');
            $this->form_validation->set_rules('contusion_type_id', 'contusion_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('contusion', 'contusion', 'trim|max_length[100]');
            $this->form_validation->set_rules('abrasion_type_id', 'abrasion_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('abrasion', 'abrasion', 'trim|max_length[100]');
            $this->form_validation->set_rules('penetration_type_id', 'penetration_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('penetration', 'penetration', 'trim|max_length[100]');
            $this->form_validation->set_rules('laceration_type_id', 'laceration_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('laceration', 'laceration', 'trim|max_length[100]');
            $this->form_validation->set_rules('wound_depth_type_id', 'wound_depth_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('wound_depth', 'wound_depth', 'trim|max_length[100]');
            $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim|max_length[100]');
            $this->form_validation->set_rules('dead_on_arrival_arry', 'dead_on_arrival_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('doa_time', 'doa_time', 'trim');
            $this->form_validation->set_rules('die_time', 'die_time', 'trim');
            $this->form_validation->set_rules('airway_type_id', 'airway_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('respiratory_arry', 'respiratory_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('circulation_arry', 'circulation_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('consciousness_arry', 'consciousness_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('triage_result', 'triage_result', 'trim|max_length[100]');
            $this->form_validation->set_rules('isolation_criteria_arry', 'isolation_criteria_arry', 'trim|max_length[50]');
            $this->form_validation->set_rules('isolation_criteria', 'isolation_criteria', 'trim|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $nurse_triage = $this->$model->read($id); 
                if($nurse_triage != []) {
                    $dt_nurse_triage = array(
                        'id'                        => $id,
                        'case_type_id'              => isset($dt_put['case_type_id']) ? $dt_put['case_type_id'] : $nurse_triage[0]->case_type_id,
                        'eye_response'              => isset($dt_put['eye_response']) ? $dt_put['eye_response'] : $nurse_triage[0]->eye_response,
                        'verbal_response'           => isset($dt_put['verbal_response']) ? $dt_put['verbal_response'] : $nurse_triage[0]->verbal_response,
                        'motor_response'            => isset($dt_put['motor_response']) ? $dt_put['motor_response'] : $nurse_triage[0]->motor_response,
                        'systolic_blood_pressure'   => isset($dt_put['systolic_blood_pressure']) ? $dt_put['systolic_blood_pressure'] : $nurse_triage[0]->systolic_blood_pressure,
                        'diastolic_blood_pressure'  => isset($dt_put['diastolic_blood_pressure']) ? $dt_put['diastolic_blood_pressure'] : $nurse_triage[0]->diastolic_blood_pressure,
                        'capillary_refill_time'     => isset($dt_put['capillary_refill_time']) ? $dt_put['capillary_refill_time'] : $nurse_triage[0]->capillary_refill_time,
                        'breath_frequency'          => isset($dt_put['breath_frequency']) ? $dt_put['breath_frequency'] : $nurse_triage[0]->breath_frequency,
                        'pulse_frequency'           => isset($dt_put['pulse_frequency']) ? $dt_put['pulse_frequency'] : $nurse_triage[0]->pulse_frequency,
                        'temperature'               => isset($dt_put['temperature']) ? $dt_put['temperature'] : $nurse_triage[0]->temperature,
                        'blood_oxygen'              => isset($dt_put['blood_oxygen']) ? $dt_put['blood_oxygen'] : $nurse_triage[0]->blood_oxygen,
                        'deformity_type_id'         => isset($dt_put['deformity_type_id']) ? $dt_put['deformity_type_id'] : $nurse_triage[0]->deformity_type_id,
                        'deformity'                 => isset($dt_put['deformity']) ? $dt_put['deformity'] : $nurse_triage[0]->deformity,
                        'contusion_type_id'         => isset($dt_put['contusion_type_id']) ? $dt_put['contusion_type_id'] : $nurse_triage[0]->contusion_type_id,
                        'contusion'                 => isset($dt_put['contusion']) ? $dt_put['contusion'] : $nurse_triage[0]->contusion,
                        'abrasion_type_id'          => isset($dt_put['abrasion_type_id']) ? $dt_put['abrasion_type_id'] : $nurse_triage[0]->abrasion_type_id,
                        'abrasion'                  => isset($dt_put['abrasion']) ? $dt_put['abrasion'] : $nurse_triage[0]->abrasion,
                        'penetration_type_id'       => isset($dt_put['penetration_type_id']) ? $dt_put['penetration_type_id'] : $nurse_triage[0]->penetration_type_id,
                        'penetration'               => isset($dt_put['penetration']) ? $dt_put['penetration'] : $nurse_triage[0]->penetration,
                        'laceration_type_id'        => isset($dt_put['laceration_type_id']) ? $dt_put['laceration_type_id'] : $nurse_triage[0]->laceration_type_id,
                        'laceration'                => isset($dt_put['laceration']) ? $dt_put['laceration'] : $nurse_triage[0]->laceration,
                        'wound_depth_type_id'       => isset($dt_put['wound_depth_type_id']) ? $dt_put['wound_depth_type_id'] : $nurse_triage[0]->wound_depth_type_id,
                        'wound_depth'               => isset($dt_put['wound_depth']) ? $dt_put['wound_depth'] : $nurse_triage[0]->wound_depth,
                        'anythingelse'              => isset($dt_put['anythingelse']) ? $dt_put['anythingelse'] : $nurse_triage[0]->anythingelse,
                        'dead_on_arrival_arry'      => isset($dt_put['dead_on_arrival_arry']) ? implode(',',$dt_put['dead_on_arrival_arry']) : $nurse_triage[0]->dead_on_arrival_arry,
                        'doa_time'                  => isset($dt_put['doa_time']) ? $dt_put['doa_time'] : $nurse_triage[0]->doa_time,
                        'die_time'                  => isset($dt_put['die_time']) ? $dt_put['die_time'] : $nurse_triage[0]->die_time,
                        'airway_type_id'            => isset($dt_put['airway_type_id']) ? $dt_put['airway_type_id'] : $nurse_triage[0]->airway_type_id,
                        'respiratory_arry'          => isset($dt_put['respiratory_arry']) ? implode(',',$dt_put['respiratory_arry']) : $nurse_triage[0]->respiratory_arry,
                        'circulation_arry'          => isset($dt_put['circulation_arry']) ? implode(',',$dt_put['circulation_arry']) : $nurse_triage[0]->circulation_arry,
                        'consciousness_arry'        => isset($dt_put['consciousness_arry']) ? implode(',',$dt_put['consciousness_arry']) : $nurse_triage[0]->consciousness_arry,
                        'triage_result'             => isset($dt_put['triage_result']) ? $dt_put['triage_result'] : $nurse_triage[0]->triage_result,
                        'isolation_criteria_arry'   => isset($dt_put['isolation_criteria_arry']) ? implode(',',$dt_put['isolation_criteria_arry']) : $nurse_triage[0]->isolation_criteria_arry,
                        'isolation_criteria'        => isset($dt_put['isolation_criteria']) ? $dt_put['isolation_criteria'] : $nurse_triage[0]->isolation_criteria,
                    );
                    $id = $this->$model->update($id, $dt_nurse_triage);
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