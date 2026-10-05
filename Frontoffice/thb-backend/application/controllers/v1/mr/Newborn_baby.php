<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Newborn_baby extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Letter_model", "Letter");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_id', 'label' => 'doctor_id', 'rules' => 'trim|required|max_length[36]'),
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
        
        $newborn_baby_id = $id;
        
        $nurse_id = $q_strings['nurse_id'] ?? null;

        $where = $q_strings;
        
        $select = 'a.*,f.fullname as doctor_name, f.ttd as doctor_signature';
        $where['a.is_delete'] = 0;
        $join = array(
            array('employee f', 'a.hfb_assisted_by = f.id', 'LEFT'),
        );
        if ($newborn_baby_id !== null && $newborn_baby_id !== "0" && $newborn_baby_id !== 0) {
            $output = $this->$model->select($newborn_baby_id, $select, $where, $join);

            if (count($output) == 0) {
                $this->REST_Return(404, 'there is no newborn_baby with id ' . $newborn_baby_id);
                return;
            }
        } else {
            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id is required if no id provided');
                return;
            }
            $output = $this->$model->select(0, $select, $where, $join);
        }

        for ($i=0; $i < count($output); $i++) {
            $select = 'a.*,
                pm.fullname as name_mother,
                pm.idcard as idcard_mother,
                rp.name as name_father,
                hp.name as profession_name,
                rp.idcard as idcard_father
            ';
            $where = array(
                'a.nurse_id' => $output[$i]->nurse_id
            );
            $join = array(
                array('registration_destination rd', 'a.registration_destination_id = rd.id', 'LEFT'),
                array('registration r', 'rd.registration_id = r.id', 'LEFT'),
                array('patient p', 'r.norm = p.norm', 'LEFT'),
                array('patient pm', 'p.norm_mother = pm.norm', 'LEFT'),
                array('responsible_person rp', 'p.norm = rp.norm', 'LEFT'),
			    array('helper hp', 'pm.profession_id = hp.code AND hp.variable="profession"', 'LEFT'),
            );
            $letter = $this->Letter->select(0, $select, $where, $join);
            $output[$i]->letter = $letter;
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
        $doctor_id = $dt_post['doctor_id'];
        $midwife_id = $dt_post['midwife_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_id);
            return;
        }

        if ($midwife_id != null) {
            $select = 'a.id';
            $employee_2 = $this->Employee->select($midwife_id, $select);

            if (count($employee_2) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $midwife_id);
                return;
            }
        } else {
            $midwife_id = null;
        }

        $birth_hour = $dt_post['birth_hour'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $apgar_score_heart_rate = $dt_post['apgar_score_heart_rate'];
        $apgar_score_respiratory = $dt_post['apgar_score_respiratory'];
        $apgar_score_muscle_tone = $dt_post['apgar_score_muscle_tone'];
        $apgar_score_reflex = $dt_post['apgar_score_reflex'];
        $apgar_score_skin_color = $dt_post['apgar_score_skin_color'];
        $apgar_score_total = $dt_post['apgar_score_total'];
        $conclution = $dt_post['conclution'];
        $external_genitalia = $dt_post['external_genitalia'];
        $anus = $dt_post['anus'];
        $meconium = $dt_post['meconium'];
        $miksi = $dt_post['miksi'];
        $vernix = $dt_post['vernix'];
        $caput_succedaneum_type_id = $dt_post['caput_succedaneum_type_id'];
        $cephalohematoma_type_id = $dt_post['cephalohematoma_type_id'];
        $head_type_id = $dt_post['head_type_id'];
        $suture_type_id = $dt_post['suture_type_id'];
        $large_fontanel = $dt_post['large_fontanel'];
        $small_fontanel = $dt_post['small_fontanel'];
        $died_in_pregnancy_type_id = $dt_post['died_in_pregnancy_type_id'];
        $died_in_childbirth_type_id = $dt_post['died_in_childbirth_type_id'];
        $died_after_birth_type_id = $dt_post['died_after_birth_type_id'];
        $congenital_disorder = $dt_post['congenital_disorder'];
        $baby_trauma = $dt_post['baby_trauma'];
        $hfb_mucus_suction_type_id = $dt_post['hfb_mucus_suction_type_id'];
        $hfb_stimulus_type_id = $dt_post['hfb_stimulus_type_id'];
        $hfb_ambu_type_id = $dt_post['hfb_ambu_type_id'];
        $hfb_ambu_long = $dt_post['hfb_ambu_long'];
        $hfb_heart_massage_type_id = $dt_post['hfb_heart_massage_type_id'];
        $hfb_heart_massage_long = $dt_post['hfb_heart_massage_long'];
        $hfb_endotracheal_tube_type_id = $dt_post['hfb_endotracheal_tube_type_id'];
        $hfb_endotracheal_tube_number = $dt_post['hfb_endotracheal_tube_number'];
        $hfb_oxygen_type_id = $dt_post['hfb_oxygen_type_id'];
        $hfb_resuscitation_type_id = $dt_post['hfb_resuscitation_type_id'];
        $hfb_umbilical_cord_wrapped = $dt_post['hfb_umbilical_cord_wrapped'];
        $hfb_given_medicines = $dt_post['hfb_given_medicines'];
        $hfb_head_size_bitemporalis = $dt_post['hfb_head_size_bitemporalis'];
        $hfb_head_size_biparietalis = $dt_post['hfb_head_size_biparietalis'];
        $hfb_head_size_fronto_occipitalis = $dt_post['hfb_head_size_fronto_occipitalis'];
        $hfb_head_size_mento_occipitalis = $dt_post['hfb_head_size_mento_occipitalis'];
        $hfb_head_size_suboccipito_bregmatica = $dt_post['hfb_head_size_suboccipito_bregmatica'];
        $hfb_chest_circumference = $dt_post['hfb_chest_circumference'];
        $hfb_upper_arm_circumference = $dt_post['hfb_upper_arm_circumference'];
        $hfb_delivery_type_id = $dt_post['hfb_delivery_type_id'];
        $hfb_delivery_other = $dt_post['hfb_delivery_other'];
        $hfb_assisted_by = $dt_post['hfb_assisted_by'];
        $hfb_newborn_baby_type_id = $dt_post['hfb_newborn_baby_type_id'];
        $hfb_baby_condition_type_id = $dt_post['hfb_baby_condition_type_id'];
        $gestational_age = $dt_post['gestational_age'];
        $water_breaks_hour = $dt_post['water_breaks_hour'];
        $water_breaks_color = $dt_post['water_breaks_color'];
        $umbilical_cord_entanglement_type_id = $dt_post['umbilical_cord_entanglement_type_id'];
        $special_note = $dt_post['special_note'];
        $transferred_date = $dt_post['transferred_date'];
        $baby_name = $dt_post['baby_name'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_newborn_baby = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_id' => $doctor_id,
            'midwife_id' => $midwife_id,
            'birth_hour' => $birth_hour,
            'weight' => $weight,
            'height' => $height,
            'apgar_score_heart_rate' => $apgar_score_heart_rate,
            'apgar_score_respiratory' => $apgar_score_respiratory,
            'apgar_score_muscle_tone' => $apgar_score_muscle_tone,
            'apgar_score_reflex' => $apgar_score_reflex,
            'apgar_score_skin_color' => $apgar_score_skin_color,
            'apgar_score_total' => $apgar_score_total,
            'conclution' => $conclution,
            'external_genitalia' => $external_genitalia,
            'anus' => $anus,
            'meconium' => $meconium,
            'miksi' => $miksi,
            'vernix' => $vernix,
            'caput_succedaneum_type_id' => $caput_succedaneum_type_id,
            'cephalohematoma_type_id' => $cephalohematoma_type_id,
            'head_type_id' => $head_type_id,
            'suture_type_id' => $suture_type_id,
            'large_fontanel' => $large_fontanel,
            'small_fontanel' => $small_fontanel,
            'died_in_pregnancy_type_id' => $died_in_pregnancy_type_id,
            'died_in_childbirth_type_id' => $died_in_childbirth_type_id,
            'died_after_birth_type_id' => $died_after_birth_type_id,
            'congenital_disorder' => $congenital_disorder,
            'baby_trauma' => $baby_trauma,
            'hfb_mucus_suction_type_id' => $hfb_mucus_suction_type_id,
            'hfb_stimulus_type_id' => $hfb_stimulus_type_id,
            'hfb_ambu_type_id' => $hfb_ambu_type_id,
            'hfb_ambu_long' => $hfb_ambu_long,
            'hfb_heart_massage_type_id' => $hfb_heart_massage_type_id,
            'hfb_heart_massage_long' => $hfb_heart_massage_long,
            'hfb_endotracheal_tube_type_id' => $hfb_endotracheal_tube_type_id,
            'hfb_endotracheal_tube_number' => $hfb_endotracheal_tube_number,
            'hfb_oxygen_type_id' => $hfb_oxygen_type_id,
            'hfb_resuscitation_type_id' => $hfb_resuscitation_type_id,
            'hfb_umbilical_cord_wrapped' => $hfb_umbilical_cord_wrapped,
            'hfb_given_medicines' => $hfb_given_medicines,
            'hfb_head_size_bitemporalis' => $hfb_head_size_bitemporalis,
            'hfb_head_size_biparietalis' => $hfb_head_size_biparietalis,
            'hfb_head_size_fronto_occipitalis' => $hfb_head_size_fronto_occipitalis,
            'hfb_head_size_mento_occipitalis' => $hfb_head_size_mento_occipitalis,
            'hfb_head_size_suboccipito_bregmatica' => $hfb_head_size_suboccipito_bregmatica,
            'hfb_chest_circumference' => $hfb_chest_circumference,
            'hfb_upper_arm_circumference' => $hfb_upper_arm_circumference,
            'hfb_delivery_type_id' => $hfb_delivery_type_id,
            'hfb_delivery_other' => $hfb_delivery_other,
            'hfb_assisted_by' => $hfb_assisted_by,
            'hfb_newborn_baby_type_id' => $hfb_newborn_baby_type_id,
            'hfb_baby_condition_type_id' => $hfb_baby_condition_type_id,
            'gestational_age' => $gestational_age,
            'water_breaks_hour' => $water_breaks_hour,
            'water_breaks_color' => $water_breaks_color,
            'umbilical_cord_entanglement_type_id' => $umbilical_cord_entanglement_type_id,
            'special_note' => $special_note,
            'baby_name' => $baby_name,
            'transferred_date' => $transferred_date,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_newborn_baby);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_newborn_baby,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create newborn_baby', $response_data);
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
    
        $newborn_baby_id = $id;
    
        if ($newborn_baby_id === 0 || $newborn_baby_id === "0") {
            $this->REST_Return(400, 'newborn_baby_id is required');
            return;
        }

        $select = 'a.id';
        $newborn_baby = $this->$model->select($newborn_baby_id, $select);

        if (count($newborn_baby) == 0) {
            $this->REST_Return(404, 'there is no newborn_baby with id ' . $newborn_baby_id);
            return;
        }

        $birth_hour = $dt_put['birth_hour'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $apgar_score_heart_rate = $dt_put['apgar_score_heart_rate'];
        $apgar_score_respiratory = $dt_put['apgar_score_respiratory'];
        $apgar_score_muscle_tone = $dt_put['apgar_score_muscle_tone'];
        $apgar_score_reflex = $dt_put['apgar_score_reflex'];
        $apgar_score_skin_color = $dt_put['apgar_score_skin_color'];
        $apgar_score_total = $dt_put['apgar_score_total'];
        $conclution = $dt_put['conclution'];
        $external_genitalia = $dt_put['external_genitalia'];
        $anus = $dt_put['anus'];
        $meconium = $dt_put['meconium'];
        $miksi = $dt_put['miksi'];
        $vernix = $dt_put['vernix'];
        $caput_succedaneum_type_id = $dt_put['caput_succedaneum_type_id'];
        $cephalohematoma_type_id = $dt_put['cephalohematoma_type_id'];
        $head_type_id = $dt_put['head_type_id'];
        $suture_type_id = $dt_put['suture_type_id'];
        $large_fontanel = $dt_put['large_fontanel'];
        $small_fontanel = $dt_put['small_fontanel'];
        $died_in_pregnancy_type_id = $dt_put['died_in_pregnancy_type_id'];
        $died_in_childbirth_type_id = $dt_put['died_in_childbirth_type_id'];
        $died_after_birth_type_id = $dt_put['died_after_birth_type_id'];
        $congenital_disorder = $dt_put['congenital_disorder'];
        $baby_trauma = $dt_put['baby_trauma'];
        $hfb_mucus_suction_type_id = $dt_put['hfb_mucus_suction_type_id'];
        $hfb_stimulus_type_id = $dt_put['hfb_stimulus_type_id'];
        $hfb_ambu_type_id = $dt_put['hfb_ambu_type_id'];
        $hfb_ambu_long = $dt_put['hfb_ambu_long'];
        $hfb_heart_massage_type_id = $dt_put['hfb_heart_massage_type_id'];
        $hfb_heart_massage_long = $dt_put['hfb_heart_massage_long'];
        $hfb_endotracheal_tube_type_id = $dt_put['hfb_endotracheal_tube_type_id'];
        $hfb_endotracheal_tube_number = $dt_put['hfb_endotracheal_tube_number'];
        $hfb_oxygen_type_id = $dt_put['hfb_oxygen_type_id'];
        $hfb_resuscitation_type_id = $dt_put['hfb_resuscitation_type_id'];
        $hfb_umbilical_cord_wrapped = $dt_put['hfb_umbilical_cord_wrapped'];
        $hfb_given_medicines = $dt_put['hfb_given_medicines'];
        $hfb_head_size_bitemporalis = $dt_put['hfb_head_size_bitemporalis'];
        $hfb_head_size_biparietalis = $dt_put['hfb_head_size_biparietalis'];
        $hfb_head_size_fronto_occipitalis = $dt_put['hfb_head_size_fronto_occipitalis'];
        $hfb_head_size_mento_occipitalis = $dt_put['hfb_head_size_mento_occipitalis'];
        $hfb_head_size_suboccipito_bregmatica = $dt_put['hfb_head_size_suboccipito_bregmatica'];
        $hfb_chest_circumference = $dt_put['hfb_chest_circumference'];
        $hfb_upper_arm_circumference = $dt_put['hfb_upper_arm_circumference'];
        $hfb_delivery_type_id = $dt_put['hfb_delivery_type_id'];
        $hfb_delivery_other = $dt_put['hfb_delivery_other'];
        $hfb_assisted_by = $dt_put['hfb_assisted_by'];
        $hfb_newborn_baby_type_id = $dt_put['hfb_newborn_baby_type_id'];
        $hfb_baby_condition_type_id = $dt_put['hfb_baby_condition_type_id'];
        $gestational_age = $dt_put['gestational_age'];
        $water_breaks_hour = $dt_put['water_breaks_hour'];
        $water_breaks_color = $dt_put['water_breaks_color'];
        $umbilical_cord_entanglement_type_id = $dt_put['umbilical_cord_entanglement_type_id'];
        $special_note = $dt_put['special_note'];
        $baby_name = $dt_put['baby_name'];
        $transferred_date = $dt_put['transferred_date'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_newborn_baby = array(
            'birth_hour' => $birth_hour,
            'weight' => $weight,
            'height' => $height,
            'apgar_score_heart_rate' => $apgar_score_heart_rate,
            'apgar_score_respiratory' => $apgar_score_respiratory,
            'apgar_score_muscle_tone' => $apgar_score_muscle_tone,
            'apgar_score_reflex' => $apgar_score_reflex,
            'apgar_score_skin_color' => $apgar_score_skin_color,
            'apgar_score_total' => $apgar_score_total,
            'conclution' => $conclution,
            'external_genitalia' => $external_genitalia,
            'anus' => $anus,
            'meconium' => $meconium,
            'miksi' => $miksi,
            'vernix' => $vernix,
            'caput_succedaneum_type_id' => $caput_succedaneum_type_id,
            'cephalohematoma_type_id' => $cephalohematoma_type_id,
            'head_type_id' => $head_type_id,
            'suture_type_id' => $suture_type_id,
            'large_fontanel' => $large_fontanel,
            'small_fontanel' => $small_fontanel,
            'died_in_pregnancy_type_id' => $died_in_pregnancy_type_id,
            'died_in_childbirth_type_id' => $died_in_childbirth_type_id,
            'died_after_birth_type_id' => $died_after_birth_type_id,
            'congenital_disorder' => $congenital_disorder,
            'baby_trauma' => $baby_trauma,
            'hfb_mucus_suction_type_id' => $hfb_mucus_suction_type_id,
            'hfb_stimulus_type_id' => $hfb_stimulus_type_id,
            'hfb_ambu_type_id' => $hfb_ambu_type_id,
            'hfb_ambu_long' => $hfb_ambu_long,
            'hfb_heart_massage_type_id' => $hfb_heart_massage_type_id,
            'hfb_heart_massage_long' => $hfb_heart_massage_long,
            'hfb_endotracheal_tube_type_id' => $hfb_endotracheal_tube_type_id,
            'hfb_endotracheal_tube_number' => $hfb_endotracheal_tube_number,
            'hfb_oxygen_type_id' => $hfb_oxygen_type_id,
            'hfb_resuscitation_type_id' => $hfb_resuscitation_type_id,
            'hfb_umbilical_cord_wrapped' => $hfb_umbilical_cord_wrapped,
            'hfb_given_medicines' => $hfb_given_medicines,
            'hfb_head_size_bitemporalis' => $hfb_head_size_bitemporalis,
            'hfb_head_size_biparietalis' => $hfb_head_size_biparietalis,
            'hfb_head_size_fronto_occipitalis' => $hfb_head_size_fronto_occipitalis,
            'hfb_head_size_mento_occipitalis' => $hfb_head_size_mento_occipitalis,
            'hfb_head_size_suboccipito_bregmatica' => $hfb_head_size_suboccipito_bregmatica,
            'hfb_chest_circumference' => $hfb_chest_circumference,
            'hfb_upper_arm_circumference' => $hfb_upper_arm_circumference,
            'hfb_delivery_type_id' => $hfb_delivery_type_id,
            'hfb_delivery_other' => $hfb_delivery_other,
            'hfb_assisted_by' => $hfb_assisted_by,
            'hfb_newborn_baby_type_id' => $hfb_newborn_baby_type_id,
            'hfb_baby_condition_type_id' => $hfb_baby_condition_type_id,
            'gestational_age' => $gestational_age,
            'water_breaks_hour' => $water_breaks_hour,
            'water_breaks_color' => $water_breaks_color,
            'umbilical_cord_entanglement_type_id' => $umbilical_cord_entanglement_type_id,
            'special_note' => $special_note,
            'baby_name' => $baby_name,
            'transferred_date' => $transferred_date,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),        
        );
    
        $this->$model->update($newborn_baby_id, $dt_newborn_baby);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_newborn_baby,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update newborn_baby', $response_data);
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
            $this->REST_Return(400, 'newborn_baby_id is required');
            return;
        }
    
        $newborn_baby_id = $this->$model->select_id($id);
    
        if ($newborn_baby_id == "") {
            $this->REST_Return(404, 'there is no newborn_baby with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_newborn_baby = array(
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($newborn_baby_id, $dt_newborn_baby);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_newborn_baby,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete newborn_baby', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}