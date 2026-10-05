<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_obgyn_special extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Nurse_obgyn_model", "Nurse_obgyn");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'nurse_obgyn_id', 'label' => 'nurse_obgyn_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'nurse_obgyn_id', 'label' => 'nurse_obgyn_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|max_length[36]'),
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
    
        $nurse_obgyn_special_id = $id;

        $nurse_id = $q_strings['nurse_id'] ?? null;

        if ($nurse_obgyn_special_id === 0 || $nurse_obgyn_special_id === "0") {
            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id is required if nurse_obgyn_special_id is not provided');
                return;
            }
        }
        
        $select = 'a.*, no.nurse_id';
        $where = array(
            'a.is_delete' => 0
        );
        $join = array(
            array('nurse_obgyn no', 'a.nurse_obgyn_id = no.id', 'INNER'),
        );
        if ($nurse_id != null) {
            $where['no.nurse_id'] = $nurse_id;
            $output = $this->$model->select(0, $select, $where, $join);
        } else {
            $output = $this->$model->select($nurse_obgyn_special_id, $select, $where, $join);
        }

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->anamnesis_disease_pregnancy_arry = $output[$i]->anamnesis_disease_pregnancy_arry != '' ? explode(',', $output[$i]->anamnesis_disease_pregnancy_arry) : [];
            
            $output[$i]->anamnesis_amniotic_fluid_date = $output[$i]->anamnesis_amniotic_fluid_date == "0000-00-00 00:00:00" ? null : $output[$i]->anamnesis_amniotic_fluid_date;
            $output[$i]->anamnesis_his_date = $output[$i]->anamnesis_his_date == "0000-00-00 00:00:00" ? null : $output[$i]->anamnesis_his_date;
            $output[$i]->anamnesis_antepartum_date = $output[$i]->anamnesis_antepartum_date == "0000-00-00 00:00:00" ? null : $output[$i]->anamnesis_antepartum_date;
            $output[$i]->anamnesis_straining_date = $output[$i]->anamnesis_straining_date == "0000-00-00 00:00:00" ? null : $output[$i]->anamnesis_straining_date;
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

        $nurse_obgyn_id = $dt_post['nurse_obgyn_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];

        $select = 'a.id';
        $nurse_obgyn = $this->Nurse_obgyn->select($nurse_obgyn_id, $select);

        if (count($nurse_obgyn) == 0) {
            $this->REST_Return(404, 'there is no nurse_obgyn with id ' . $nurse_obgyn_id);
            return;
        }
    
        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }
    
        $anamnesis_g = $dt_post['anamnesis_g'];
        $anamnesis_p = $dt_post['anamnesis_p'];
        $anamnesis_a = $dt_post['anamnesis_a'];
        $anamnesis_h = $dt_post['anamnesis_h'];
        $anamnesis_period = $dt_post['anamnesis_period'];
        $anamnesis_antenatal_type_id = $dt_post['anamnesis_antenatal_type_id'];
        $anamnesis_disease_pregnancy_arry = $dt_post['anamnesis_disease_pregnancy_arry'] ?? null != null ? implode(',',$dt_post['anamnesis_disease_pregnancy_arry']): '';
        $anamnesis_disease_pregnancy_other = $dt_post['anamnesis_disease_pregnancy_other'];
        $anamnesis_his_type_id = $dt_post['anamnesis_his_type_id'];
        $anamnesis_his_date = $dt_post['anamnesis_his_date'];
        $anamnesis_amniotic_fluid_type_id = $dt_post['anamnesis_amniotic_fluid_type_id'];
        $anamnesis_amniotic_fluid_date = $dt_post['anamnesis_amniotic_fluid_date'];
        $anamnesis_amniotic_fluid_color_type_id = $dt_post['anamnesis_amniotic_fluid_color_type_id'];
        $anamnesis_amniotic_fluid_color_other = $dt_post['anamnesis_amniotic_fluid_color_other'];
        $anamnesis_blood_slym_type_id = $dt_post['anamnesis_blood_slym_type_id'];
        $anamnesis_antepartum_type_id = $dt_post['anamnesis_antepartum_type_id'];
        $anamnesis_antepartum_date = $dt_post['anamnesis_antepartum_date'];
        $anamnesis_antepartum_amount = $dt_post['anamnesis_antepartum_amount'];
        $anamnesis_seizure_type_id = $dt_post['anamnesis_seizure_type_id'];
        $anamnesis_seizure_clonic_long = $dt_post['anamnesis_seizure_clonic_long'];
        $anamnesis_straining_type_id = $dt_post['anamnesis_straining_type_id'];
        $anamnesis_straining_date = $dt_post['anamnesis_straining_date'];
        $anamnesis_straining_amount = $dt_post['anamnesis_straining_amount'];
        $anamnesis_other_problem = $dt_post['anamnesis_other_problem'];
        $physical_leopold_one = $dt_post['physical_leopold_one'];
        $physical_leopold_two_type_id = $dt_post['physical_leopold_two_type_id'];
        $physical_leopold_three_type_id = $dt_post['physical_leopold_three_type_id'];
        $physical_leopold_three_other = $dt_post['physical_leopold_three_other'];
        $physical_leopold_four_type_id = $dt_post['physical_leopold_four_type_id'];
        $physical_fetal_heart_rate_freq = $dt_post['physical_fetal_heart_rate_freq'];
        $physical_fetal_heart_rate_rhythm_type_id = $dt_post['physical_fetal_heart_rate_rhythm_type_id'];
        $physical_vulva_type_id = $dt_post['physical_vulva_type_id'];
        $physical_vulva_other = $dt_post['physical_vulva_other'];
        $physical_internal_portio_type_id = $dt_post['physical_internal_portio_type_id'];
        $physical_internal_opening_type_id = $dt_post['physical_internal_opening_type_id'];
        $physical_internal_opening_cm = $dt_post['physical_internal_opening_cm'];
        $physical_internal_depletion_percent = $dt_post['physical_internal_depletion_percent'];
        $physical_internal_amniotic_type_id = $dt_post['physical_internal_amniotic_type_id'];
        $physical_internal_amniotic_color = $dt_post['physical_internal_amniotic_color'];
        $physical_internal_face = $dt_post['physical_internal_face'];
        $physical_internal_presentation_type_id = $dt_post['physical_internal_presentation_type_id'];
        $physical_internal_presentation_other = $dt_post['physical_internal_presentation_other'];
        $physical_internal_lowest_part_drop_type_id = $dt_post['physical_internal_lowest_part_drop_type_id'];
        $physical_ctg_baseline = $dt_post['physical_ctg_baseline'];
        $physical_ctg_variability_type_id = $dt_post['physical_ctg_variability_type_id'];
        $physical_ctg_acceleration_type_id = $dt_post['physical_ctg_acceleration_type_id'];
        $physical_ctg_deceleration_type_id = $dt_post['physical_ctg_deceleration_type_id'];
        $physical_ctg_contractions_type_id = $dt_post['physical_ctg_contractions_type_id'];
        $physical_ctg_contractions_freq = $dt_post['physical_ctg_contractions_freq'];
        $physical_ctg_contractions_duration = $dt_post['physical_ctg_contractions_duration'];
        $physical_ctg_contractions_strength = $dt_post['physical_ctg_contractions_strength'];
        $physical_ctg_fetal_movement = $dt_post['physical_ctg_fetal_movement'];
        $physical_ctg_interpretation = $dt_post['physical_ctg_interpretation'];
        $diagnosis_mother = $dt_post['diagnosis_mother'];
        $diagnosis_baby = $dt_post['diagnosis_baby'];
        $prognosis_mother_type_id = $dt_post['prognosis_mother_type_id'];
        $prognosis_mother_night = $dt_post['prognosis_mother_night'];
        $prognosis_baby_type_id = $dt_post['prognosis_baby_type_id'];
        $prognosis_baby_night = $dt_post['prognosis_baby_night'];
        $plan_type_id = $dt_post['plan_type_id'];
        $obstetric_problems = $dt_post['obstetric_problems'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_obgyn = array(
            'nurse_obgyn_id'                           => $nurse_obgyn_id,
            'employee_nurse_id'                        => $employee_nurse_id,
            'anamnesis_g'                              => $anamnesis_g,
            'anamnesis_p'                              => $anamnesis_p,
            'anamnesis_a'                              => $anamnesis_a,
            'anamnesis_h'                              => $anamnesis_h,
            'anamnesis_period'                         => $anamnesis_period,
            'anamnesis_antenatal_type_id'              => $anamnesis_antenatal_type_id,
            'anamnesis_disease_pregnancy_arry'         => $anamnesis_disease_pregnancy_arry,
            'anamnesis_disease_pregnancy_other'        => $anamnesis_disease_pregnancy_other,
            'anamnesis_his_type_id'                    => $anamnesis_his_type_id,
            'anamnesis_his_date'                       => $anamnesis_his_date,
            'anamnesis_amniotic_fluid_type_id'         => $anamnesis_amniotic_fluid_type_id,
            'anamnesis_amniotic_fluid_date'            => $anamnesis_amniotic_fluid_date,
            'anamnesis_amniotic_fluid_color_type_id'   => $anamnesis_amniotic_fluid_color_type_id,
            'anamnesis_amniotic_fluid_color_other'     => $anamnesis_amniotic_fluid_color_other,
            'anamnesis_blood_slym_type_id'             => $anamnesis_blood_slym_type_id,
            'anamnesis_antepartum_type_id'             => $anamnesis_antepartum_type_id,
            'anamnesis_antepartum_date'                => $anamnesis_antepartum_date,
            'anamnesis_antepartum_amount'              => $anamnesis_antepartum_amount,
            'anamnesis_seizure_type_id'                => $anamnesis_seizure_type_id,
            'anamnesis_seizure_clonic_long'            => $anamnesis_seizure_clonic_long,
            'anamnesis_straining_type_id'              => $anamnesis_straining_type_id,
            'anamnesis_straining_date'                 => $anamnesis_straining_date,
            'anamnesis_straining_amount'               => $anamnesis_straining_amount,
            'anamnesis_other_problem'                  => $anamnesis_other_problem,
            'physical_leopold_one'                     => $physical_leopold_one,
            'physical_leopold_two_type_id'             => $physical_leopold_two_type_id,
            'physical_leopold_three_type_id'           => $physical_leopold_three_type_id,
            'physical_leopold_three_other'             => $physical_leopold_three_other,
            'physical_leopold_four_type_id'            => $physical_leopold_four_type_id,
            'physical_fetal_heart_rate_freq'           => $physical_fetal_heart_rate_freq,
            'physical_fetal_heart_rate_rhythm_type_id' => $physical_fetal_heart_rate_rhythm_type_id,
            'physical_vulva_type_id'                   => $physical_vulva_type_id,
            'physical_vulva_other'                     => $physical_vulva_other,
            'physical_internal_portio_type_id'         => $physical_internal_portio_type_id,
            'physical_internal_opening_type_id'        => $physical_internal_opening_type_id,
            'physical_internal_opening_cm'             => $physical_internal_opening_cm,
            'physical_internal_depletion_percent'      => $physical_internal_depletion_percent,
            'physical_internal_amniotic_type_id'       => $physical_internal_amniotic_type_id,
            'physical_internal_amniotic_color'         => $physical_internal_amniotic_color,
            'physical_internal_face'                   => $physical_internal_face,
            'physical_internal_presentation_type_id'   => $physical_internal_presentation_type_id,
            'physical_internal_presentation_other'     => $physical_internal_presentation_other,
            'physical_internal_lowest_part_drop_type_id' => $physical_internal_lowest_part_drop_type_id,
            'physical_ctg_baseline'                    => $physical_ctg_baseline,
            'physical_ctg_variability_type_id'         => $physical_ctg_variability_type_id,
            'physical_ctg_acceleration_type_id'        => $physical_ctg_acceleration_type_id,
            'physical_ctg_deceleration_type_id'        => $physical_ctg_deceleration_type_id,
            'physical_ctg_contractions_type_id'        => $physical_ctg_contractions_type_id,
            'physical_ctg_contractions_freq'           => $physical_ctg_contractions_freq,
            'physical_ctg_contractions_duration'       => $physical_ctg_contractions_duration,
            'physical_ctg_contractions_strength'       => $physical_ctg_contractions_strength,
            'physical_ctg_fetal_movement'              => $physical_ctg_fetal_movement,
            'physical_ctg_interpretation'              => $physical_ctg_interpretation,
            'diagnosis_mother'                         => $diagnosis_mother,
            'diagnosis_baby'                           => $diagnosis_baby,
            'prognosis_mother_type_id'                 => $prognosis_mother_type_id,
            'prognosis_mother_night'                   => $prognosis_mother_night,
            'prognosis_baby_type_id'                   => $prognosis_baby_type_id,
            'prognosis_baby_night'                     => $prognosis_baby_night,
            'plan_type_id'                             => $plan_type_id,
            'obstetric_problems'                       => $obstetric_problems,
            'user_added'                               => $user_id,
            'date_added'                               => date('Y-m-d H:i:s',time()),
            'date_updated'                             => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_nurse_obgyn);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_obgyn', $response_data);
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
    
        $nurse_obgyn_special_id = $id;
    
        if ($nurse_obgyn_special_id === 0 || $nurse_obgyn_special_id === "0") {
            $this->REST_Return(400, 'nurse_obgyn_special_id is required');
            return;
        }

        $select = 'a.id';
        $nurse_obgyn_special = $this->$model->select($nurse_obgyn_special_id, $select);

        if (count($nurse_obgyn_special) == 0) {
            $this->REST_Return(404, 'there is no nurse_obgyn_special with id ' . $nurse_obgyn_special_id);
            return;
        }
    
        $anamnesis_g = $dt_put['anamnesis_g'];
        $anamnesis_p = $dt_put['anamnesis_p'];
        $anamnesis_a = $dt_put['anamnesis_a'];
        $anamnesis_h = $dt_put['anamnesis_h'];
        $anamnesis_period = $dt_put['anamnesis_period'];
        $anamnesis_antenatal_type_id = $dt_put['anamnesis_antenatal_type_id'];
        $anamnesis_disease_pregnancy_type_id = $dt_put['anamnesis_disease_pregnancy_type_id'];
        $anamnesis_disease_pregnancy_other = $dt_put['anamnesis_disease_pregnancy_other'];
        $anamnesis_his_type_id = $dt_put['anamnesis_his_type_id'];
        $anamnesis_his_date = $dt_put['anamnesis_his_date'];
        $anamnesis_his_hour = $dt_put['anamnesis_his_hour'];
        $anamnesis_amniotic_fluid_type_id = $dt_put['anamnesis_amniotic_fluid_type_id'];
        $anamnesis_amniotic_fluid_date = $dt_put['anamnesis_amniotic_fluid_date'];
        $anamnesis_amniotic_fluid_hour = $dt_put['anamnesis_amniotic_fluid_hour'];
        $anamnesis_amniotic_fluid_color_type_id = $dt_put['anamnesis_amniotic_fluid_color_type_id'];
        $anamnesis_amniotic_fluid_color_other = $dt_put['anamnesis_amniotic_fluid_color_other'];
        $anamnesis_blood_slym_type_id = $dt_put['anamnesis_blood_slym_type_id'];
        $anamnesis_antepartum_type_id = $dt_put['anamnesis_antepartum_type_id'];
        $anamnesis_antepartum_date = $dt_put['anamnesis_antepartum_date'];
        $anamnesis_antepartum_hour = $dt_put['anamnesis_antepartum_hour'];
        $anamnesis_antepartum_amount = $dt_put['anamnesis_antepartum_amount'];
        $anamnesis_seizure_type_id = $dt_put['anamnesis_seizure_type_id'];
        $anamnesis_seizure_clonic_long = $dt_put['anamnesis_seizure_clonic_long'];
        $anamnesis_straining_type_id = $dt_put['anamnesis_straining_type_id'];
        $anamnesis_straining_date = $dt_put['anamnesis_straining_date'];
        $anamnesis_straining_hour = $dt_put['anamnesis_straining_hour'];
        $anamnesis_straining_amount = $dt_put['anamnesis_straining_amount'];
        $anamnesis_other_problem = $dt_put['anamnesis_other_problem'];
        $physical_leopold_one = $dt_put['physical_leopold_one'];
        $physical_leopold_two_type_id = $dt_put['physical_leopold_two_type_id'];
        $physical_leopold_three_type_id = $dt_put['physical_leopold_three_type_id'];
        $physical_leopold_three_other = $dt_put['physical_leopold_three_other'];
        $physical_leopold_four_type_id = $dt_put['physical_leopold_four_type_id'];
        $physical_fetal_heart_rate_freq = $dt_put['physical_fetal_heart_rate_freq'];
        $physical_fetal_heart_rate_rhythm_type_id = $dt_put['physical_fetal_heart_rate_rhythm_type_id'];
        $physical_vulva_type_id = $dt_put['physical_vulva_type_id'];
        $physical_vulva_other = $dt_put['physical_vulva_other'];
        $physical_internal_portio_type_id = $dt_put['physical_internal_portio_type_id'];
        $physical_internal_opening_type_id = $dt_put['physical_internal_opening_type_id'];
        $physical_internal_opening_cm = $dt_put['physical_internal_opening_cm'];
        $physical_internal_depletion_percent = $dt_put['physical_internal_depletion_percent'];
        $physical_internal_amniotic_type_id = $dt_put['physical_internal_amniotic_type_id'];
        $physical_internal_amniotic_color = $dt_put['physical_internal_amniotic_color'];
        $physical_internal_presentation_type_id = $dt_put['physical_internal_presentation_type_id'];
        $physical_internal_presentation_other = $dt_put['physical_internal_presentation_other'];
        $physical_ctg_baseline = $dt_put['physical_ctg_baseline'];
        $physical_ctg_variability_type_id = $dt_put['physical_ctg_variability_type_id'];
        $physical_ctg_acceleration_type_id = $dt_put['physical_ctg_acceleration_type_id'];
        $physical_ctg_deceleration_type_id = $dt_put['physical_ctg_deceleration_type_id'];
        $physical_ctg_contractions_type_id = $dt_put['physical_ctg_contractions_type_id'];
        $physical_ctg_contractions_freq = $dt_put['physical_ctg_contractions_freq'];
        $physical_ctg_contractions_duration = $dt_put['physical_ctg_contractions_duration'];
        $physical_ctg_contractions_strength = $dt_put['physical_ctg_contractions_strength'];
        $physical_ctg_fetal_movement = $dt_put['physical_ctg_fetal_movement'];
        $physical_ctg_interpretation = $dt_put['physical_ctg_interpretation'];
        $diagnosis_mother = $dt_put['diagnosis_mother'];
        $diagnosis_baby = $dt_put['diagnosis_baby'];
        $prognosis_mother_type_id = $dt_put['prognosis_mother_type_id'];
        $prognosis_mother_night = $dt_put['prognosis_mother_night'];
        $prognosis_baby_type_id = $dt_put['prognosis_baby_type_id'];
        $prognosis_baby_night = $dt_put['prognosis_baby_night'];
        $plan_type_id = $dt_put['plan_type_id'];
        $obstetric_problems = $dt_put['obstetric_problems'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nurse_obgyn_special = array(
            'anamnesis_g'                              => $anamnesis_g,
            'anamnesis_p'                              => $anamnesis_p,
            'anamnesis_a'                              => $anamnesis_a,
            'anamnesis_h'                              => $anamnesis_h,
            'anamnesis_period'                         => $anamnesis_period,
            'anamnesis_antenatal_type_id'              => $anamnesis_antenatal_type_id,
            'anamnesis_disease_pregnancy_type_id'      => $anamnesis_disease_pregnancy_type_id,
            'anamnesis_disease_pregnancy_other'        => $anamnesis_disease_pregnancy_other,
            'anamnesis_his_type_id'                    => $anamnesis_his_type_id,
            'anamnesis_his_date'                       => $anamnesis_his_date,
            'anamnesis_his_hour'                       => $anamnesis_his_hour,
            'anamnesis_amniotic_fluid_type_id'         => $anamnesis_amniotic_fluid_type_id,
            'anamnesis_amniotic_fluid_date'            => $anamnesis_amniotic_fluid_date,
            'anamnesis_amniotic_fluid_hour'            => $anamnesis_amniotic_fluid_hour,
            'anamnesis_amniotic_fluid_color_type_id'   => $anamnesis_amniotic_fluid_color_type_id,
            'anamnesis_amniotic_fluid_color_other'     => $anamnesis_amniotic_fluid_color_other,
            'anamnesis_blood_slym_type_id'             => $anamnesis_blood_slym_type_id,
            'anamnesis_antepartum_type_id'             => $anamnesis_antepartum_type_id,
            'anamnesis_antepartum_date'                => $anamnesis_antepartum_date,
            'anamnesis_antepartum_hour'                => $anamnesis_antepartum_hour,
            'anamnesis_antepartum_amount'              => $anamnesis_antepartum_amount,
            'anamnesis_seizure_type_id'                => $anamnesis_seizure_type_id,
            'anamnesis_seizure_clonic_long'            => $anamnesis_seizure_clonic_long,
            'anamnesis_straining_type_id'              => $anamnesis_straining_type_id,
            'anamnesis_straining_date'                 => $anamnesis_straining_date,
            'anamnesis_straining_hour'                 => $anamnesis_straining_hour,
            'anamnesis_straining_amount'               => $anamnesis_straining_amount,
            'anamnesis_other_problem'                  => $anamnesis_other_problem,
            'physical_leopold_one'                     => $physical_leopold_one,
            'physical_leopold_two_type_id'             => $physical_leopold_two_type_id,
            'physical_leopold_three_type_id'           => $physical_leopold_three_type_id,
            'physical_leopold_three_other'             => $physical_leopold_three_other,
            'physical_leopold_four_type_id'            => $physical_leopold_four_type_id,
            'physical_fetal_heart_rate_freq'           => $physical_fetal_heart_rate_freq,
            'physical_fetal_heart_rate_rhythm_type_id' => $physical_fetal_heart_rate_rhythm_type_id,
            'physical_vulva_type_id'                   => $physical_vulva_type_id,
            'physical_vulva_other'                     => $physical_vulva_other,
            'physical_internal_portio_type_id'         => $physical_internal_portio_type_id,
            'physical_internal_opening_type_id'        => $physical_internal_opening_type_id,
            'physical_internal_opening_cm'             => $physical_internal_opening_cm,
            'physical_internal_depletion_percent'      => $physical_internal_depletion_percent,
            'physical_internal_amniotic_type_id'       => $physical_internal_amniotic_type_id,
            'physical_internal_amniotic_color'         => $physical_internal_amniotic_color,
            'physical_internal_presentation_type_id'   => $physical_internal_presentation_type_id,
            'physical_internal_presentation_other'     => $physical_internal_presentation_other,
            'physical_ctg_baseline'                    => $physical_ctg_baseline,
            'physical_ctg_variability_type_id'         => $physical_ctg_variability_type_id,
            'physical_ctg_acceleration_type_id'        => $physical_ctg_acceleration_type_id,
            'physical_ctg_deceleration_type_id'        => $physical_ctg_deceleration_type_id,
            'physical_ctg_contractions_type_id'        => $physical_ctg_contractions_type_id,
            'physical_ctg_contractions_freq'           => $physical_ctg_contractions_freq,
            'physical_ctg_contractions_duration'       => $physical_ctg_contractions_duration,
            'physical_ctg_contractions_strength'       => $physical_ctg_contractions_strength,
            'physical_ctg_fetal_movement'              => $physical_ctg_fetal_movement,
            'physical_ctg_interpretation'              => $physical_ctg_interpretation,
            'diagnosis_mother'                         => $diagnosis_mother,
            'diagnosis_baby'                           => $diagnosis_baby,
            'prognosis_mother_type_id'                 => $prognosis_mother_type_id,
            'prognosis_mother_night'                   => $prognosis_mother_night,
            'prognosis_baby_type_id'                   => $prognosis_baby_type_id,
            'prognosis_baby_night'                     => $prognosis_baby_night,
            'plan_type_id'                             => $plan_type_id,
            'obstetric_problems'                       => $obstetric_problems,
            'user_updated'                             => $user_id,
            'date_updated'                             => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($nurse_obgyn_special_id, $dt_nurse_obgyn_special);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn_special,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_obgyn_special', $response_data);
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
            $this->REST_Return(400, 'nurse_obgyn_special_id is required');
            return;
        }
    
        $nurse_obgyn_special_id = $this->$model->select_id($id);
    
        if ($nurse_obgyn_special_id == "") {
            $this->REST_Return(404, 'there is no nurse_obgyn_special with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_nurse_obgyn_special = array(
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($nurse_obgyn_special_id, $dt_nurse_obgyn_special);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_obgyn_special,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nurse_obgyn_special', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}