<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_hemodialysis extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Nurse_model", "Nurse");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'nurse_id', 'label' => 'nurse_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'hemodialysis_date', 'label' => 'hemodialysis_date', 'rules' => 'trim'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        $hemodialysis_id = $id;
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $this->$model->whereFields = $q_strings;
        $output = $this->$model->read($hemodialysis_id);
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
        $nurse_id = $dt_post['nurse_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $nurse = $this->Nurse->select($nurse_id, $select);

        if (count($nurse) == 0) {
            $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        $hemodialysis_date = $dt_post['hemodialysis_date'];
        $hemodialysis_type_id = $dt_post['hemodialysis_type_id'];
        $frequency_type_id = $dt_post['frequency_type_id'];
        $dialysate_type_id = $dt_post['dialysate_type_id'];
        $dialysate_temperature = $dt_post['dialysate_temperature'];
        $vascular_type_id = $dt_post['vascular_type_id'];
        $vascular_jugularis = $dt_post['vascular_jugularis'];
        $long_hours = $dt_post['long_hours'];
        $blood_velocity = $dt_post['blood_velocity'];
        $initial_dose = $dt_post['initial_dose'];
        $intermittent = $dt_post['intermittent'];
        $ufg = $dt_post['ufg'];
        $dialysate_velocity = $dt_post['dialysate_velocity'];
        $maintenance = $dt_post['maintenance'];
        $lmwh = $dt_post['lmwh'];
        $heparin_type_id = $dt_post['heparin_type_id'];
        $without_heparin = $dt_post['without_heparin'];
        $profiling_type_id = $dt_post['profiling_type_id'];
        $ultrafiltration = $dt_post['ultrafiltration'];
        $natrium = $dt_post['natrium'];
        $bicarbonate = $dt_post['bicarbonate'];
        $therapy = $dt_post['therapy'];
        $medicine = $dt_post['medicine'];
        $injection = $dt_post['injection'];
        $etiology = $dt_post['etiology'];
        $comorbidities = $dt_post['comorbidities'];
    
        $dt_hemodialysis = array(
            'registration_destination_id'   => $registration_destination_id,
            'nurse_id'                      => $nurse_id,
            'hemodialysis_date'             => $hemodialysis_date,
            'hemodialysis_type_id'          => $hemodialysis_type_id,
            'frequency_type_id'             => $frequency_type_id,
            'dialysate_type_id'             => $dialysate_type_id,
            'dialysate_temperature'         => $dialysate_temperature,
            'vascular_type_id'              => $vascular_type_id,
            'vascular_jugularis'            => $vascular_jugularis,
            'long_hours'                    => $long_hours,
            'blood_velocity'                => $blood_velocity,
            'initial_dose'                  => $initial_dose,
            'intermittent'                  => $intermittent,
            'ufg'                           => $ufg,
            'dialysate_velocity'            => $dialysate_velocity,
            'maintenance'                   => $maintenance,
            'lmwh'                          => $lmwh,
            'heparin_type_id'               => $heparin_type_id,
            'without_heparin'               => $without_heparin,
            'profiling_type_id'             => $profiling_type_id,
            'ultrafiltration'               => $ultrafiltration,
            'natrium'                       => $natrium,
            'bicarbonate'                   => $bicarbonate,
            'therapy'                       => $therapy,
            'medicine'                      => $medicine,
            'injection'                     => $injection,
            'etiology'                      => $etiology,
            'comorbidities'                 => $comorbidities,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_hemodialysis);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create hemodialysis', $response_data);
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

        $hemodialysis_id = $id;

        if ($hemodialysis_id === 0 || $hemodialysis_id === "0") {
            $this->REST_Return(400, 'hemodialysis_id is required');
            return;
        }

        $select = 'a.id';
        $hemodialysis = $this->$model->select($hemodialysis_id, $select);

        if (count($hemodialysis) == 0) {
            $this->REST_Return(404, 'there is no hemodialysis with id ' . $hemodialysis_id);
            return;
        }
        
        /* Start Transaction */
        $this->db->trans_start();

        $hemodialysis_date = $dt_put['hemodialysis_date'];
        $hemodialysis_type_id = $dt_put['hemodialysis_type_id'];
        $frequency_type_id = $dt_put['frequency_type_id'];
        $dialysate_type_id = $dt_put['dialysate_type_id'];
        $dialysate_temperature = $dt_put['dialysate_temperature'];
        $vascular_type_id = $dt_put['vascular_type_id'];
        $vascular_jugularis = $dt_put['vascular_jugularis'];
        $long_hours = $dt_put['long_hours'];
        $blood_velocity = $dt_put['blood_velocity'];
        $initial_dose = $dt_put['initial_dose'];
        $intermittent = $dt_put['intermittent'];
        $ufg = $dt_put['ufg'];
        $dialysate_velocity = $dt_put['dialysate_velocity'];
        $maintenance = $dt_put['maintenance'];
        $lmwh = $dt_put['lmwh'];
        $heparin_type_id = $dt_put['heparin_type_id'];
        $without_heparin = $dt_put['without_heparin'];
        $profiling_type_id = $dt_put['profiling_type_id'];
        $ultrafiltration = $dt_put['ultrafiltration'];
        $natrium = $dt_put['natrium'];
        $bicarbonate = $dt_put['bicarbonate'];
        $therapy = $dt_put['therapy'];
        $medicine = $dt_put['medicine'];
        $injection = $dt_put['injection'];
        $etiology = $dt_put['etiology'];
        $comorbidities = $dt_put['comorbidities'];
    
        $dt_hemodialysis = array(
            'hemodialysis_date'     => $hemodialysis_date,
            'hemodialysis_type_id'  => $hemodialysis_type_id,
            'frequency_type_id'     => $frequency_type_id,
            'dialysate_type_id'     => $dialysate_type_id,
            'dialysate_temperature' => $dialysate_temperature,
            'vascular_type_id'      => $vascular_type_id,
            'vascular_jugularis'    => $vascular_jugularis,
            'long_hours'            => $long_hours,
            'blood_velocity'        => $blood_velocity,
            'initial_dose'          => $initial_dose,
            'intermittent'          => $intermittent,
            'ufg'                   => $ufg,
            'dialysate_velocity'    => $dialysate_velocity,
            'maintenance'           => $maintenance,
            'lmwh'                  => $lmwh,
            'heparin_type_id'       => $heparin_type_id,
            'without_heparin'       => $without_heparin,
            'profiling_type_id'     => $profiling_type_id,
            'ultrafiltration'       => $ultrafiltration,
            'natrium'               => $natrium,
            'bicarbonate'           => $bicarbonate,
            'therapy'               => $therapy,
            'medicine'              => $medicine,
            'injection'             => $injection,
            'etiology'              => $etiology,
            'comorbidities'         => $comorbidities,
            'user_updated'          => $user_id,
            'date_updated'          => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($hemodialysis_id, $dt_hemodialysis);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update hemodialysis', $response_data);
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
            $this->REST_Return(400, 'hemodialysis_id is required');
            return;
        }
    
        $hemodialysis_id = $this->$model->select_id($id);
    
        if ($hemodialysis_id == "") {
            $this->REST_Return(404, 'there is no hemodialysis with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_hemodialysis = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($hemodialysis_id, $dt_hemodialysis);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_hemodialysis,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete hemodialysis', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}