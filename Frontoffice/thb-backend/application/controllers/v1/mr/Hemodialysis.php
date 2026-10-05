<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Hemodialysis extends My_Controller {
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
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|max_length[1]'),
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
    
        $hemodialysis_id = $id;
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($hemodialysis_id);

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->nursing_intervention_arry = ($output[$i]->nursing_intervention_arry != "") ? explode(',', $output[$i]->nursing_intervention_arry) : [];
            $output[$i]->collaborative_intervention_arry = ($output[$i]->collaborative_intervention_arry != "") ? explode(',', $output[$i]->collaborative_intervention_arry) : [];
            $output[$i]->vascular_access_arry = ($output[$i]->vascular_access_arry != "") ? explode(',', $output[$i]->vascular_access_arry) : [];
            $output[$i]->heparinization_arry = ($output[$i]->heparinization_arry != "") ? explode(',', $output[$i]->heparinization_arry) : [];
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
            $output->rows[$i]->nursing_intervention_arry = ($output->rows[$i]->nursing_intervention_arry != "") ? explode(',', $output->rows[$i]->nursing_intervention_arry) : [];
            $output->rows[$i]->collaborative_intervention_arry = ($output->rows[$i]->collaborative_intervention_arry != "") ? explode(',', $output->rows[$i]->collaborative_intervention_arry) : [];
            $output->rows[$i]->vascular_access_arry = ($output->rows[$i]->vascular_access_arry != "") ? explode(',', $output->rows[$i]->vascular_access_arry) : [];
            $output->rows[$i]->heparinization_arry = ($output->rows[$i]->heparinization_arry != "") ? explode(',', $output->rows[$i]->heparinization_arry) : [];
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

        $nursing_intervention_arry = implode(',', $dt_post['nursing_intervention_arry']);
        $nursing_intervention_health_education = $dt_post['nursing_intervention_health_education'];
        $nursing_intervention_other = $dt_post['nursing_intervention_other'];
        $collaborative_intervention_arry = implode(',', $dt_post['collaborative_intervention_arry']);
        $collaborative_intervention_antibiotics = $dt_post['collaborative_intervention_antibiotics'];
        $hemodialysis_type_id = $dt_post['hemodialysis_type_id'];
        $weight_yl_post = $dt_post['weight_yl_post'];
        $weight_pre = $dt_post['weight_pre'];
        $long_hours = $dt_post['long_hours'];
        $quick_of_blood = $dt_post['quick_of_blood'];
        $vena = $dt_post['vena'];
        $dialyzer = $dt_post['dialyzer'];
        $uf_goal = $dt_post['uf_goal'];
        $artery = $dt_post['artery'];
        $iso_uf = $dt_post['iso_uf'];
        $long_iso_uf = $dt_post['long_iso_uf'];
        $n_or_r = $dt_post['n_or_r'];
        $blood_bank = $dt_post['blood_bank'];
        $weight_post = $dt_post['weight_post'];
        $tmp = $dt_post['tmp'];
        $qd = $dt_post['qd'];
        $profiling_type_id = $dt_post['profiling_type_id'];
        $profiling_uf = $dt_post['profiling_uf'];
        $profiling_bicarbonate = $dt_post['profiling_bicarbonate'];
        $vascular_access_arry = implode(',', $dt_post['vascular_access_arry']);
        $vascular_access_av_shunt_type_id = $dt_post['vascular_access_av_shunt_type_id'];
        $vascular_access_cdl_type_id = $dt_post['vascular_access_cdl_type_id'];
        $vascular_access_femoral_type_id = $dt_post['vascular_access_femoral_type_id'];
        $dialysate_type_id = $dt_post['dialysate_type_id'];
        $dialysate_conductivity = $dt_post['dialysate_conductivity'];
        $dialysate_calcium = $dt_post['dialysate_calcium'];
        $dialysate_temperature = $dt_post['dialysate_temperature'];
        $dialysate_base = $dt_post['dialysate_base'];
        $heparinization_arry = implode(',', $dt_post['heparinization_arry']);
        $heparinization_circulation = $dt_post['heparinization_circulation'];
        $heparinization_initial = $dt_post['heparinization_initial'];
        $heparinization_kontinyu = $dt_post['heparinization_kontinyu'];
        $heparinization_lmwh = $dt_post['heparinization_lmwh'];
        $heparinization_heparin = $dt_post['heparinization_heparin'];
        $medical_observation = $dt_post['medical_observation'];
        $routine_medication = $dt_post['routine_medication'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_hemodialysis = array(
            'registration_destination_id'           => $registration_destination_id,
            'doctor_in_charge_id'                   => $doctor_in_charge_id,
            'nursing_intervention_arry'             => $nursing_intervention_arry,
            'nursing_intervention_health_education' => $nursing_intervention_health_education,
            'nursing_intervention_other'            => $nursing_intervention_other,
            'collaborative_intervention_arry'       => $collaborative_intervention_arry,
            'collaborative_intervention_antibiotics' => $collaborative_intervention_antibiotics,
            'hemodialysis_type_id'                  => $hemodialysis_type_id,
            'weight_yl_post'                        => $weight_yl_post,
            'weight_pre'                            => $weight_pre,
            'long_hours'                            => $long_hours,
            'quick_of_blood'                        => $quick_of_blood,
            'vena'                                  => $vena,
            'dialyzer'                              => $dialyzer,
            'uf_goal'                               => $uf_goal,
            'artery'                                => $artery,
            'iso_uf'                                => $iso_uf,
            'long_iso_uf'                           => $long_iso_uf,
            'n_or_r'                                => $n_or_r,
            'blood_bank'                            => $blood_bank,
            'weight_post'                           => $weight_post,
            'tmp'                                   => $tmp,
            'qd'                                    => $qd,
            'profiling_type_id'                     => $profiling_type_id,
            'profiling_uf'                          => $profiling_uf,
            'profiling_bicarbonate'                 => $profiling_bicarbonate,
            'vascular_access_arry'                  => $vascular_access_arry,
            'vascular_access_av_shunt_type_id'      => $vascular_access_av_shunt_type_id,
            'vascular_access_cdl_type_id'           => $vascular_access_cdl_type_id,
            'vascular_access_femoral_type_id'       => $vascular_access_femoral_type_id,
            'dialysate_type_id'                     => $dialysate_type_id,
            'dialysate_conductivity'                => $dialysate_conductivity,
            'dialysate_calcium'                     => $dialysate_calcium,
            'dialysate_temperature'                 => $dialysate_temperature,
            'dialysate_base'                        => $dialysate_base,
            'heparinization_arry'                   => $heparinization_arry,
            'heparinization_circulation'            => $heparinization_circulation,
            'heparinization_initial'                => $heparinization_initial,
            'heparinization_kontinyu'               => $heparinization_kontinyu,
            'heparinization_lmwh'                   => $heparinization_lmwh,
            'heparinization_heparin'                => $heparinization_heparin,
            'medical_observation'                   => $medical_observation,
            'routine_medication'                    => $routine_medication,
            'user_added'                            => $user_id,
            'date_added'                            => date('Y-m-d H:i:s',time()),
            'date_updated'                          => date('Y-m-d H:i:s',time()),
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
    
        $this->REST_Return(201, 'SUCCESS', $response_data);
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

        $nursing_intervention_arry = implode(',', $dt_put['nursing_intervention_arry']);
        $nursing_intervention_health_education = $dt_put['nursing_intervention_health_education'];
        $nursing_intervention_other = $dt_put['nursing_intervention_other'];
        $collaborative_intervention_arry = implode(',', $dt_put['collaborative_intervention_arry']);
        $collaborative_intervention_antibiotics = $dt_put['collaborative_intervention_antibiotics'];
        $hemodialysis_type_id = $dt_put['hemodialysis_type_id'];
        $weight_yl_post = $dt_put['weight_yl_post'];
        $weight_pre = $dt_put['weight_pre'];
        $long_hours = $dt_put['long_hours'];
        $quick_of_blood = $dt_put['quick_of_blood'];
        $vena = $dt_put['vena'];
        $dialyzer = $dt_put['dialyzer'];
        $uf_goal = $dt_put['uf_goal'];
        $artery = $dt_put['artery'];
        $iso_uf = $dt_put['iso_uf'];
        $long_iso_uf = $dt_put['long_iso_uf'];
        $n_or_r = $dt_put['n_or_r'];
        $blood_bank = $dt_put['blood_bank'];
        $weight_post = $dt_put['weight_post'];
        $tmp = $dt_put['tmp'];
        $qd = $dt_put['qd'];
        $profiling_type_id = $dt_put['profiling_type_id'];
        $profiling_uf = $dt_put['profiling_uf'];
        $profiling_bicarbonate = $dt_put['profiling_bicarbonate'];
        $vascular_access_arry = implode(',', $dt_put['vascular_access_arry']);
        $vascular_access_av_shunt_type_id = $dt_put['vascular_access_av_shunt_type_id'];
        $vascular_access_cdl_type_id = $dt_put['vascular_access_cdl_type_id'];
        $vascular_access_femoral_type_id = $dt_put['vascular_access_femoral_type_id'];
        $dialysate_type_id = $dt_put['dialysate_type_id'];
        $dialysate_conductivity = $dt_put['dialysate_conductivity'];
        $dialysate_calcium = $dt_put['dialysate_calcium'];
        $dialysate_temperature = $dt_put['dialysate_temperature'];
        $dialysate_base = $dt_put['dialysate_base'];
        $heparinization_arry = implode(',', $dt_put['heparinization_arry']);
        $heparinization_circulation = $dt_put['heparinization_circulation'];
        $heparinization_initial = $dt_put['heparinization_initial'];
        $heparinization_kontinyu = $dt_put['heparinization_kontinyu'];
        $heparinization_lmwh = $dt_put['heparinization_lmwh'];
        $heparinization_heparin = $dt_put['heparinization_heparin'];
        $medical_observation = $dt_put['medical_observation'];
        $routine_medication = $dt_put['routine_medication'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_hemodialysis = array(
            'nursing_intervention_arry'             => $nursing_intervention_arry,
            'nursing_intervention_health_education' => $nursing_intervention_health_education,
            'nursing_intervention_other'            => $nursing_intervention_other,
            'collaborative_intervention_arry'       => $collaborative_intervention_arry,
            'collaborative_intervention_antibiotics' => $collaborative_intervention_antibiotics,
            'hemodialysis_type_id'                  => $hemodialysis_type_id,
            'weight_yl_post'                        => $weight_yl_post,
            'weight_pre'                            => $weight_pre,
            'long_hours'                            => $long_hours,
            'quick_of_blood'                        => $quick_of_blood,
            'vena'                                  => $vena,
            'dialyzer'                              => $dialyzer,
            'uf_goal'                               => $uf_goal,
            'artery'                                => $artery,
            'iso_uf'                                => $iso_uf,
            'long_iso_uf'                           => $long_iso_uf,
            'n_or_r'                                => $n_or_r,
            'blood_bank'                            => $blood_bank,
            'weight_post'                           => $weight_post,
            'tmp'                                   => $tmp,
            'qd'                                    => $qd,
            'profiling_type_id'                     => $profiling_type_id,
            'profiling_uf'                          => $profiling_uf,
            'profiling_bicarbonate'                 => $profiling_bicarbonate,
            'vascular_access_arry'                  => $vascular_access_arry,
            'vascular_access_av_shunt_type_id'      => $vascular_access_av_shunt_type_id,
            'vascular_access_cdl_type_id'           => $vascular_access_cdl_type_id,
            'vascular_access_femoral_type_id'       => $vascular_access_femoral_type_id,
            'dialysate_type_id'                     => $dialysate_type_id,
            'dialysate_conductivity'                => $dialysate_conductivity,
            'dialysate_calcium'                     => $dialysate_calcium,
            'dialysate_temperature'                 => $dialysate_temperature,
            'dialysate_base'                        => $dialysate_base,
            'heparinization_arry'                   => $heparinization_arry,
            'heparinization_circulation'            => $heparinization_circulation,
            'heparinization_initial'                => $heparinization_initial,
            'heparinization_kontinyu'               => $heparinization_kontinyu,
            'heparinization_lmwh'                   => $heparinization_lmwh,
            'heparinization_heparin'                => $heparinization_heparin,
            'medical_observation'                   => $medical_observation,
            'routine_medication'                    => $routine_medication,
            'user_updated'                          => $user_id,
            'date_updated'                          => date('Y-m-d H:i:s',time()),
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