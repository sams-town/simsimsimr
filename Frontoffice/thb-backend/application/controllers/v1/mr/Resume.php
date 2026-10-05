<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Resume extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        $this->load->helper('url');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("$this->directory/Icd9cm_model", "Icd9cm");
        $this->load->model("$this->directory/Icd10_model", "Icd10");
        $this->load->model("$this->directory/Soap_model", "Soap");
        $this->load->model("$this->directory/Soap_s_model", "Soap_s");
        $this->load->model("$this->directory/Soap_o_model", "Soap_o");
        $this->load->model("$this->directory/Soap_a_model", "Soap_a");
        $this->load->model("$this->directory/Soap_a_action_model", "Soap_a_action");
        $this->load->model("$this->directory/Soap_p_model", "Soap_p");
        $this->load->model("$this->directory/Soap_p_action_model", "Soap_p_action");
        $this->load->model("$this->directory/Soap_inpatient_model", "Soap_inpatient");
        $this->load->model("v1/health_facility/Laboratory_request_item_model", "Laboratory_request_item");
        $this->load->model("v1/health_facility/Radiology_request_item_model", "Radiology_request_item");
        $this->load->model("v1/health_facility/Radiology_request_item_image_model", "Radiology_request_item_image");
        $this->load->model("v1/health_facility/Physiotherapy_request_item_model", "Physiotherapy_request_item");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Exits_model", "Exits");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'exit_id', 'label' => 'exit_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->pre_create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'exit_id', 'label' => 'exit_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'exit_id', 'label' => 'exit_id', 'rules' => 'trim|required|max_length[36]'),
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
    
        $resume_id = $id;
    
        if ($resume_id === 0 || $resume_id === "0") {
            $this->REST_Return(400, 'resume_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($resume_id);
        $response_data = array();

        for ($i=0; $i < count($output); $i++) { 
            $id = $output[$i]->id;
            $soap_id = $output[$i]->soap_id;
            $registration_destination_id = $output[$i]->registration_destination_id;
            
            $select = 'a.significant_medications, a.take_home_medications, a.exit_id, a.exit_anythingelse, a.exit_condition_type_id, a.exit_condition_anythingelse, a.care_instruction_control, a.care_instruction_control_date, a.care_instruction_anythingelse, a.summary_medical_history, a.home_education, a.indication_for_hospitalization';
            $soap = $this->Soap->select($soap_id, $select);
            foreach ($soap[0] as $key => $val) {
                $output[$i]->$key = $val;
            }

            $select = '
            a.head,
            a.head_hair_color,
            a.head_hair_condition,
            a.eye_conjunctive,
            a.eye_sclera,
            a.eye_pupil,
            a.eye_light_reflex,
            a.mouth_mucosa,
            a.mouth_discoloration,
            a.mouth_tongue,
            a.mouth_caries,
            a.neck_kgb,
            a.neck_trachea_position,
            a.neck_massa,
            a.neck_jvp,
            a.thorax_insp_chest,
            a.thorax_insp_retraction,
            a.thorax_palp_chest,
            a.thorax_palp_fremitus,
            a.thorax_palp_crepitation,
            a.thorax_ausk_pul_vasiculer,
            a.thorax_ausk_pul_rhonki,
            a.thorax_ausk_pul_wheezing,
            a.thorax_ausk_cor_heart_sound,
            a.thorax_ausk_cor_murmur,
            a.thorax_ausk_cor_gallop,
            a.abdomen_supel,
            a.abdomen_bowel_sounds,
            a.abdomen_tenderness,
            a.abdomen_hepar_lien,
            a.abdomen_other_physical_check,
            a.extremity_acral,
            a.extremity_crt,
            a.extremity_edema,
            a.genitalia,
            a.localist_status,
            a.ophthal_status,
            a.neurologist_status
            ';
            $where = array('a.soap_id' => $soap_id);
            $soap_o = $this->Soap_o->select(0, $select, $where);

            $output[$i]->head = $soap_o[0]->head;
            $output[$i]->head_hair_color = $soap_o[0]->head_hair_color;
            $output[$i]->head_hair_condition = $soap_o[0]->head_hair_condition;
            $output[$i]->eye_conjunctive = $soap_o[0]->eye_conjunctive;
            $output[$i]->eye_sclera = $soap_o[0]->eye_sclera;
            $output[$i]->eye_pupil = $soap_o[0]->eye_pupil;
            $output[$i]->eye_light_reflex = $soap_o[0]->eye_light_reflex;
            $output[$i]->mouth_mucosa = $soap_o[0]->mouth_mucosa;
            $output[$i]->mouth_discoloration = $soap_o[0]->mouth_discoloration;
            $output[$i]->mouth_tongue = $soap_o[0]->mouth_tongue;
            $output[$i]->mouth_caries = $soap_o[0]->mouth_caries;
            $output[$i]->neck_kgb = $soap_o[0]->neck_kgb;
            $output[$i]->neck_trachea_position = $soap_o[0]->neck_trachea_position;
            $output[$i]->neck_massa = $soap_o[0]->neck_massa;
            $output[$i]->neck_jvp = $soap_o[0]->neck_jvp;
            $output[$i]->thorax_insp_chest = $soap_o[0]->thorax_insp_chest;
            $output[$i]->thorax_insp_retraction = $soap_o[0]->thorax_insp_retraction;
            $output[$i]->thorax_palp_chest = $soap_o[0]->thorax_palp_chest;
            $output[$i]->thorax_palp_fremitus = $soap_o[0]->thorax_palp_fremitus;
            $output[$i]->thorax_palp_crepitation = $soap_o[0]->thorax_palp_crepitation;
            $output[$i]->thorax_ausk_pul_vasiculer = $soap_o[0]->thorax_ausk_pul_vasiculer;
            $output[$i]->thorax_ausk_pul_rhonki = $soap_o[0]->thorax_ausk_pul_rhonki;
            $output[$i]->thorax_ausk_pul_wheezing = $soap_o[0]->thorax_ausk_pul_wheezing;
            $output[$i]->thorax_ausk_cor_heart_sound = $soap_o[0]->thorax_ausk_cor_heart_sound;
            $output[$i]->thorax_ausk_cor_murmur = $soap_o[0]->thorax_ausk_cor_murmur;
            $output[$i]->thorax_ausk_cor_gallop = $soap_o[0]->thorax_ausk_cor_gallop;
            $output[$i]->abdomen_supel = $soap_o[0]->abdomen_supel;
            $output[$i]->abdomen_bowel_sounds = $soap_o[0]->abdomen_bowel_sounds;
            $output[$i]->abdomen_tenderness = $soap_o[0]->abdomen_tenderness;
            $output[$i]->abdomen_hepar_lien = $soap_o[0]->abdomen_hepar_lien;
            $output[$i]->abdomen_other_physical_check = $soap_o[0]->abdomen_other_physical_check;
            $output[$i]->extremity_acral = $soap_o[0]->extremity_acral;
            $output[$i]->extremity_crt = $soap_o[0]->extremity_crt;
            $output[$i]->extremity_edema = $soap_o[0]->extremity_edema;
            $output[$i]->genitalia = $soap_o[0]->genitalia;
            $output[$i]->localist_status = $soap_o[0]->localist_status;
            $output[$i]->ophthal_status = $soap_o[0]->ophthal_status;
            $output[$i]->neurologist_status = $soap_o[0]->neurologist_status;

            $soap_a = $this->db->query("
                SELECT a.id
                FROM soap_a as a
                WHERE soap_id = '$soap_id'
                AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                ORDER BY a.id ASC
            ")->result();
            
            $soap_a_id = $soap_a[0]->id;

            $select = 'a.icd10_id, i.code as icd10_code, i.code_s as icd10_code_s, i.name as icd10_name';
            $where = array('soap_a_id' => $soap_a_id, 'a.is_delete' => 0);
            $join = array(
                array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
            );
            $order = array(
                'a.index' => 'ASC',
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);
            
            $output[$i]->icd10 = $soap_a_action;

            $select = 'a.id';
            $where = array('soap_id' => $soap_id);
            $soap_p = $this->Soap_p->select(0, $select, $where);
            
            $soap_p_id = $soap_p[0]->id;

            $select = 'a.icd9cm_id, i.code as icd9cm_code, i.code_s as icd9cm_code_s, i.name as icd9cm_name';
            $where = array('soap_p_id' => $soap_p_id, 'a.is_delete' => 0);
            $join = array(
                array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
            );
            $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);
            
            $output[$i]->icd9cm = $soap_p_action;

            $output[$i]->condition = "";
            $output[$i]->indication = "";
            $output[$i]->plan = "";

            $select = 'a.condition, a.indication, a.plan';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id
            );
            $soap_inpatient = $this->Soap_inpatient->select(0, $select, $where);

            if (count($soap_inpatient) > 0) {
                $condition = $soap_inpatient[0]->condition;
                $indication = $soap_inpatient[0]->indication;
                $plan = $soap_inpatient[0]->plan;

                $output[$i]->condition = $condition;
                $output[$i]->indication = $indication;
                $output[$i]->plan = $plan;
            }

            $select = 'a.item_id, lri.costing_name, lri.laboratory_package_id, lri.laboratory_request_id, lp.code_lis';
            $where = array('resume_item_type_id' => 1, 'a.is_delete' => 0);
            $join = array(
                array('laboratory_request_item lri', 'a.item_id = lri.id', 'INNER'),
                array('laboratory_package lp', 'lri.laboratory_package_id = lp.id', 'INNER'),
            );
            $laboratory_request_items = $this->Resume_item->select($id, $select, $where, $join);
            
            for ($k=0; $k < count($laboratory_request_items); $k++) { 
                $laboratory_request_id = $laboratory_request_items[$k]->laboratory_request_id;
                
                // LIS RESULT
                $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                $code_lis = $laboratory_request_items[$k]->code_lis;
                $laboratory_request_items[$k]->result = [];

                if (count($lis_response) > 0) {
                    $result = $lis_response[0]['result_detail'];

                    for ($l=0; $l < count($result); $l++) { 
                        $flag = $result[$l]['flag'];
                        $parameter_id = $result[$l]['ParameterID'];
                        
                        if ($parameter_id == $code_lis) {
                            array_push($laboratory_request_items[$k]->result, $result[$l]);
                        }
                    }
                }
            }

            $output[$i]->laboratory_request_items = $laboratory_request_items;

            $select = 'a.item_id, rri.costing_name, rri.radiology_package_id, rri.radiology_request_id';
            $where = array('resume_item_type_id' => 2, 'a.is_delete' => 0);
            $join = array(
                array('radiology_request_item rri', 'a.item_id = rri.id', 'INNER'),
            );
            $radiology_request_items = $this->Resume_item->select($id, $select, $where, $join);
            
            for ($k=0; $k < count($radiology_request_items); $k++) { 
                $radiology_request_item_id = $radiology_request_items[$k]->item_id;
                
                $select_rad_image = 'a.*';
                $where_rad_image = array(
                    'a.radiology_request_item_id' => $radiology_request_item_id, 
                    'a.is_delete' => 0
                );
                $radiology_request_item_image = $this->Radiology_request_item_image->select(0, $select_rad_image, $where_rad_image);
                
                for ($kk=0; $kk < count($radiology_request_item_image); $kk++) { 
                    $url_raw = $radiology_request_item_image[$kk]->url;
                    $name = substr($url_raw, 20);

                    $radiology_request_item_image[$kk]->radiology_request_id = $radiology_request_items[$k]->radiology_request_id;
                    $radiology_request_item_image[$kk]->name = $name;
                }
                
                $radiology_request_items[$k]->url = $radiology_request_item_image;
            }


            $output[$i]->radiology_request_items = $radiology_request_items;

            $select = 'a.item_id, pri.costing_name, pri.physiotherapy_package_id';
            $where = array('resume_item_type_id' => 3, 'a.is_delete' => 0);
            $join = array(
                array('physiotherapy_request_item pri', 'a.item_id = pri.id', 'INNER'),
            );
            $physiotherapy_request_items = $this->Resume_item->select($id, $select, $where, $join);

            $output[$i]->physiotherapy_request_items = $physiotherapy_request_items;
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

        $registration_destination_id = $q_strings['registration_destination_id'] ?? null;

        if ($registration_destination_id == null) {
            $norm = $q_strings['norm'] ?? null;
            if ($norm == null) {
                $this->REST_Return(400, 'norm is required if registration_destination_id is not provided');
                return;
            }
        }

        if (isset($q_strings['norm'])) {
            $this->$model->whereFields['r.norm'] = $q_strings['norm'];
            unset($this->$model->whereFields['norm']);
        }

        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $id = $output->rows[$i]->id;
            $soap_id = $output->rows[$i]->soap_id;
            
            $select = 'a.significant_medications, a.take_home_medications, a.exit_id, a.exit_anythingelse, a.exit_condition_type_id, a.exit_condition_anythingelse, a.care_instruction_control, a.care_instruction_control_date, a.care_instruction_anythingelse, a.summary_medical_history, a.home_education, a.indication_for_hospitalization';
            $soap = $this->Soap->select($soap_id, $select);
            foreach ($soap[0] as $key => $val) {
                $output->rows[$i]->$key = $val;
            }

            $select = '
            a.head,
            a.head_hair_color,
            a.head_hair_condition,
            a.eye_conjunctive,
            a.eye_sclera,
            a.eye_pupil,
            a.eye_light_reflex,
            a.mouth_mucosa,
            a.mouth_discoloration,
            a.mouth_tongue,
            a.mouth_caries,
            a.neck_kgb,
            a.neck_trachea_position,
            a.neck_massa,
            a.neck_jvp,
            a.thorax_insp_chest,
            a.thorax_insp_retraction,
            a.thorax_palp_chest,
            a.thorax_palp_fremitus,
            a.thorax_palp_crepitation,
            a.thorax_ausk_pul_vasiculer,
            a.thorax_ausk_pul_rhonki,
            a.thorax_ausk_pul_wheezing,
            a.thorax_ausk_cor_heart_sound,
            a.thorax_ausk_cor_murmur,
            a.thorax_ausk_cor_gallop,
            a.abdomen_supel,
            a.abdomen_bowel_sounds,
            a.abdomen_tenderness,
            a.abdomen_hepar_lien,
            a.abdomen_other_physical_check,
            a.extremity_acral,
            a.extremity_crt,
            a.extremity_edema,
            a.genitalia,
            a.localist_status,
            a.ophthal_status,
            a.neurologist_status
            ';
            $where = array('a.soap_id' => $soap_id);
            $soap_o = $this->Soap_o->select(0, $select, $where);

            $output->rows[$i]->head = $soap_o[0]->head;
            $output->rows[$i]->head_hair_color = $soap_o[0]->head_hair_color;
            $output->rows[$i]->head_hair_condition = $soap_o[0]->head_hair_condition;
            $output->rows[$i]->eye_conjunctive = $soap_o[0]->eye_conjunctive;
            $output->rows[$i]->eye_sclera = $soap_o[0]->eye_sclera;
            $output->rows[$i]->eye_pupil = $soap_o[0]->eye_pupil;
            $output->rows[$i]->eye_light_reflex = $soap_o[0]->eye_light_reflex;
            $output->rows[$i]->mouth_mucosa = $soap_o[0]->mouth_mucosa;
            $output->rows[$i]->mouth_discoloration = $soap_o[0]->mouth_discoloration;
            $output->rows[$i]->mouth_tongue = $soap_o[0]->mouth_tongue;
            $output->rows[$i]->mouth_caries = $soap_o[0]->mouth_caries;
            $output->rows[$i]->neck_kgb = $soap_o[0]->neck_kgb;
            $output->rows[$i]->neck_trachea_position = $soap_o[0]->neck_trachea_position;
            $output->rows[$i]->neck_massa = $soap_o[0]->neck_massa;
            $output->rows[$i]->neck_jvp = $soap_o[0]->neck_jvp;
            $output->rows[$i]->thorax_insp_chest = $soap_o[0]->thorax_insp_chest;
            $output->rows[$i]->thorax_insp_retraction = $soap_o[0]->thorax_insp_retraction;
            $output->rows[$i]->thorax_palp_chest = $soap_o[0]->thorax_palp_chest;
            $output->rows[$i]->thorax_palp_fremitus = $soap_o[0]->thorax_palp_fremitus;
            $output->rows[$i]->thorax_palp_crepitation = $soap_o[0]->thorax_palp_crepitation;
            $output->rows[$i]->thorax_ausk_pul_vasiculer = $soap_o[0]->thorax_ausk_pul_vasiculer;
            $output->rows[$i]->thorax_ausk_pul_rhonki = $soap_o[0]->thorax_ausk_pul_rhonki;
            $output->rows[$i]->thorax_ausk_pul_wheezing = $soap_o[0]->thorax_ausk_pul_wheezing;
            $output->rows[$i]->thorax_ausk_cor_heart_sound = $soap_o[0]->thorax_ausk_cor_heart_sound;
            $output->rows[$i]->thorax_ausk_cor_murmur = $soap_o[0]->thorax_ausk_cor_murmur;
            $output->rows[$i]->thorax_ausk_cor_gallop = $soap_o[0]->thorax_ausk_cor_gallop;
            $output->rows[$i]->abdomen_supel = $soap_o[0]->abdomen_supel;
            $output->rows[$i]->abdomen_bowel_sounds = $soap_o[0]->abdomen_bowel_sounds;
            $output->rows[$i]->abdomen_tenderness = $soap_o[0]->abdomen_tenderness;
            $output->rows[$i]->abdomen_hepar_lien = $soap_o[0]->abdomen_hepar_lien;
            $output->rows[$i]->abdomen_other_physical_check = $soap_o[0]->abdomen_other_physical_check;
            $output->rows[$i]->extremity_acral = $soap_o[0]->extremity_acral;
            $output->rows[$i]->extremity_crt = $soap_o[0]->extremity_crt;
            $output->rows[$i]->extremity_edema = $soap_o[0]->extremity_edema;
            $output->rows[$i]->genitalia = $soap_o[0]->genitalia;
            $output->rows[$i]->localist_status = $soap_o[0]->localist_status;
            $output->rows[$i]->ophthal_status = $soap_o[0]->ophthal_status;
            $output->rows[$i]->neurologist_status = $soap_o[0]->neurologist_status;

            $soap_a = $this->db->query("
                SELECT a.id
                FROM soap_a as a
                WHERE soap_id = '$soap_id'
                AND (a.cppt_id IS NULL OR a.cppt_id = '') 
                ORDER BY a.id ASC
            ")->result();
            
            $soap_a_id = $soap_a[0]->id;

            $select = 'a.icd10_id, i.code as icd10_code, i.code_s as icd10_code_s, i.name as icd10_name';
            $where = array('soap_a_id' => $soap_a_id, 'a.is_delete' => 0);
            $join = array(
                array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
            );
            $order = array(
                'a.index' => 'ASC',
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, $order);
            
            $output->rows[$i]->icd10 = $soap_a_action;

            $select = 'a.id';
            $where = array('soap_id' => $soap_id);
            $soap_p = $this->Soap_p->select(0, $select, $where);
            
            $soap_p_id = $soap_p[0]->id;

            $select = 'a.icd9cm_id, i.code as icd9cm_code, i.code_s as icd9cm_code_s, i.name as icd9cm_name';
            $where = array('soap_p_id' => $soap_p_id, 'a.is_delete' => 0);
            $join = array(
                array('icd9cm i', 'a.icd9cm_id = i.id', 'INNER'),
            );
            $soap_p_action = $this->Soap_p_action->select(0, $select, $where, $join);
            
            $output->rows[$i]->icd9cm = $soap_p_action;

            $select = 'a.item_id, lri.costing_name, lri.laboratory_package_id, lri.laboratory_request_id, lp.code_lis';
            $where = array('resume_item_type_id' => 1, 'a.is_delete' => 0);
            $join = array(
                array('laboratory_request_item lri', 'a.item_id = lri.id', 'INNER'),
                array('laboratory_package lp', 'lri.laboratory_package_id = lp.id', 'INNER'),
            );
            $laboratory_request_items = $this->Resume_item->select($id, $select, $where, $join);
            
            for ($k=0; $k < count($laboratory_request_items); $k++) { 
                $laboratory_request_id = $laboratory_request_items[$k]->laboratory_request_id;
                
                // LIS RESULT
                $lis_response = $this->bride_lis('/api_lis/view.php/order/'. $laboratory_request_id);

                $code_lis = $laboratory_request_items[$k]->code_lis;
                $laboratory_request_items[$k]->result = [];
                

                if (count($lis_response) > 0) {
                    $result = $lis_response[0]['result_detail'];

                    for ($l=0; $l < count($result); $l++) { 
                        $flag = $result[$l]['flag'];
                        $parameter_id = $result[$l]['ParameterID'];
                        
                        // if ($flag != '' && $parameter_id == $code_lis) {
                        if ($parameter_id == $code_lis) {
                            array_push($laboratory_request_items[$k]->result, $result[$l]);
                        }
                    }
                }
            }

            $output->rows[$i]->laboratory_request_items = $laboratory_request_items;

            $select = 'a.item_id, rri.costing_name, rri.radiology_package_id, rri.radiology_request_id';
            $where = array('resume_item_type_id' => 2, 'a.is_delete' => 0);
            $join = array(
                array('radiology_request_item rri', 'a.item_id = rri.id', 'INNER'),
            );
            $radiology_request_items = $this->Resume_item->select($id, $select, $where, $join);

            for ($k=0; $k < count($radiology_request_items); $k++) { 
                $radiology_request_item_id = $radiology_request_items[$k]->item_id;
                
                $select_rad_image = 'a.*';
                $where_rad_image = array(
                    'a.radiology_request_item_id' => $radiology_request_item_id, 
                    'a.is_delete' => 0
                );
                $radiology_request_item_image = $this->Radiology_request_item_image->select(0, $select_rad_image, $where_rad_image);
                
                for ($kk=0; $kk < count($radiology_request_item_image); $kk++) { 
                    $url_raw = $radiology_request_item_image[$kk]->url;
                    $name = substr($url_raw, 20);

                    $radiology_request_item_image[$kk]->radiology_request_id = $radiology_request_items[$k]->radiology_request_id;
                    $radiology_request_item_image[$kk]->name = $name;
                }
                
                $radiology_request_items[$k]->url = $radiology_request_item_image;
            }

            $output->rows[$i]->radiology_request_items = $radiology_request_items;

            $select = 'a.item_id, pri.costing_name, pri.physiotherapy_package_id';
            $where = array('resume_item_type_id' => 3, 'a.is_delete' => 0);
            $join = array(
                array('physiotherapy_request_item pri', 'a.item_id = pri.id', 'INNER'),
            );
            $physiotherapy_request_items = $this->Resume_item->select($id, $select, $where, $join);

            $output->rows[$i]->physiotherapy_request_items = $physiotherapy_request_items;
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
        $health_facility_item_id = $dt_post['health_facility_item_id'];
        $exit_id = $dt_post['exit_id'];

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

        if ($health_facility_item_id != null) {
            $select = 'a.id';
            $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
    
            if (count($health_facility_item) == 0) {
                $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
                return;
            }
        }

        $select = 'a.id';
        $exits = $this->Exits->select($exit_id, $select);

        if (count($exits) == 0) {
            $this->REST_Return(404, 'there is no exits with id ' . $exit_id);
            return;
        }

        $physical_check = $dt_post['physical_check'];
        $significant_medications = $dt_post['significant_medications'];
        $take_home_medications = $dt_post['take_home_medications'];
        $exit_anythingelse = $dt_post['exit_anythingelse'];
        $exit_condition_type_id = $dt_post['exit_condition_type_id'];
        $exit_condition_anythingelse = $dt_post['exit_condition_anythingelse'];
        $care_instruction_control = $dt_post['care_instruction_control'];
        $care_instruction_control_date = $dt_post['care_instruction_control_date'];
        $care_instruction_anythingelse = $dt_post['care_instruction_anythingelse'];
        $summary_medical_history = $dt_post['summary_medical_history'];
        $home_education = $dt_post['home_education'];
        $indication_for_hospitalization = $dt_post['indication_for_hospitalization'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_soap = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'exit_id' => $exit_id,
            'significant_medications' => $significant_medications,
            'take_home_medications' => $take_home_medications,
            'exit_anythingelse' => $exit_anythingelse,
            'exit_condition_type_id' => $exit_condition_type_id,
            'exit_condition_anythingelse' => $exit_condition_anythingelse,
            'care_instruction_control' => $care_instruction_control,
            'care_instruction_control_date' => $care_instruction_control_date,
            'care_instruction_anythingelse' => $care_instruction_anythingelse,
            'summary_medical_history' => $summary_medical_history,
            'home_education' => $home_education,
            'indication_for_hospitalization' => $indication_for_hospitalization,
            'is_resume_exit' => 1,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $soap_id = $this->Soap->create($dt_soap);

        // ST_GENERALIST
        $head = $dt_post['head'];
        $head_hair_color = $dt_post['head_hair_color'];
        $head_hair_condition = $dt_post['head_hair_condition'];
        $eye_conjunctive = $dt_post['eye_conjunctive'];
        $eye_sclera = $dt_post['eye_sclera'];
        $eye_pupil = $dt_post['eye_pupil'];
        $eye_light_reflex = $dt_post['eye_light_reflex'];
        $mouth_mucosa = $dt_post['mouth_mucosa'];
        $mouth_discoloration = $dt_post['mouth_discoloration'];
        $mouth_tongue = $dt_post['mouth_tongue'];
        $mouth_caries = $dt_post['mouth_caries'];
        $neck_kgb = $dt_post['neck_kgb'];
        $neck_trachea_position = $dt_post['neck_trachea_position'];
        $neck_massa = $dt_post['neck_massa'];
        $neck_jvp = $dt_post['neck_jvp'];
        $thorax_insp_chest = $dt_post['thorax_insp_chest'];
        $thorax_insp_retraction = $dt_post['thorax_insp_retraction'];
        $thorax_palp_chest = $dt_post['thorax_palp_chest'];
        $thorax_palp_fremitus = $dt_post['thorax_palp_fremitus'];
        $thorax_palp_crepitation = $dt_post['thorax_palp_crepitation'];
        $thorax_ausk_pul_vasiculer = $dt_post['thorax_ausk_pul_vasiculer'];
        $thorax_ausk_pul_rhonki = $dt_post['thorax_ausk_pul_rhonki'];
        $thorax_ausk_pul_wheezing = $dt_post['thorax_ausk_pul_wheezing'];
        $thorax_ausk_cor_heart_sound = $dt_post['thorax_ausk_cor_heart_sound'];
        $thorax_ausk_cor_murmur = $dt_post['thorax_ausk_cor_murmur'];
        $thorax_ausk_cor_gallop = $dt_post['thorax_ausk_cor_gallop'];
        $abdomen_supel = $dt_post['abdomen_supel'];
        $abdomen_bowel_sounds = $dt_post['abdomen_bowel_sounds'];
        $abdomen_tenderness = $dt_post['abdomen_tenderness'];
        $abdomen_hepar_lien = $dt_post['abdomen_hepar_lien'];
        $abdomen_other_physical_check = $dt_post['abdomen_other_physical_check'];
        $extremity_acral = $dt_post['extremity_acral'];
        $extremity_crt = $dt_post['extremity_crt'];
        $extremity_edema = $dt_post['extremity_edema'];
        $genitalia = $dt_post['genitalia'];
        $localist_status = $dt_post['localist_status'];
        $ophthal_status = $dt_post['ophthal_status'];
        $neurologist_status = $dt_post['neurologist_status'];

        $dt_soap_o = array(
            'soap_id' => $soap_id,
            'physical_check' => $physical_check,
            'head' => $head,
            'head_hair_color' => $head_hair_color,
            'head_hair_condition' => $head_hair_condition,
            'eye_conjunctive' => $eye_conjunctive,
            'eye_sclera' => $eye_sclera,
            'eye_pupil' => $eye_pupil,
            'eye_light_reflex' => $eye_light_reflex,
            'mouth_mucosa' => $mouth_mucosa,
            'mouth_discoloration' => $mouth_discoloration,
            'mouth_tongue' => $mouth_tongue,
            'mouth_caries' => $mouth_caries,
            'neck_kgb' => $neck_kgb,
            'neck_trachea_position' => $neck_trachea_position,
            'neck_massa' => $neck_massa,
            'neck_jvp' => $neck_jvp,
            'thorax_insp_chest' => $thorax_insp_chest,
            'thorax_insp_retraction' => $thorax_insp_retraction,
            'thorax_palp_chest' => $thorax_palp_chest,
            'thorax_palp_fremitus' => $thorax_palp_fremitus,
            'thorax_palp_crepitation' => $thorax_palp_crepitation,
            'thorax_ausk_pul_vasiculer' => $thorax_ausk_pul_vasiculer,
            'thorax_ausk_pul_rhonki' => $thorax_ausk_pul_rhonki,
            'thorax_ausk_pul_wheezing' => $thorax_ausk_pul_wheezing,
            'thorax_ausk_cor_heart_sound' => $thorax_ausk_cor_heart_sound,
            'thorax_ausk_cor_murmur' => $thorax_ausk_cor_murmur,
            'thorax_ausk_cor_gallop' => $thorax_ausk_cor_gallop,
            'abdomen_supel' => $abdomen_supel,
            'abdomen_bowel_sounds' => $abdomen_bowel_sounds,
            'abdomen_tenderness' => $abdomen_tenderness,
            'abdomen_hepar_lien' => $abdomen_hepar_lien,
            'abdomen_other_physical_check' => $abdomen_other_physical_check,
            'extremity_acral' => $extremity_acral,
            'extremity_crt' => $extremity_crt,
            'extremity_edema' => $extremity_edema,
            'genitalia' => $genitalia,
            'localist_status' => $localist_status,
            'ophthal_status' => $ophthal_status,
            'neurologist_status' => $neurologist_status,
        );

        $this->Soap_o->create($dt_soap_o);
    
        $icd10_notes = $dt_post['icd10_notes'];
        $therapy_plan = $dt_post['therapy_plan'];

        $dt_resume = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
            'soap_id' => $soap_id,
            'health_facility_item_id' => ($health_facility_item_id == "") ? null : $health_facility_item_id,
            'icd10_notes' => $icd10_notes,
            'therapy_plan' => $therapy_plan,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_resume);

        $dt_soap_a = array(
            'soap_id' => $soap_id,
            'date_added' => date("Y-m-d H:i:s")
        );

        $soap_a_id = $this->Soap_a->create($dt_soap_a);

        $icd10_arr = $dt_post['icd10'];
        if ($icd10_arr != null) {
    
            for ($i=0; $i < count($icd10_arr) ; $i++) {
                if (!isset($icd10_arr[$i]['icd10_id'])) {
                    $this->REST_Return(400, 'field icd10_id in icd10 is required');
                }
                
                $icd10_id = $icd10_arr[$i]['icd10_id'];

                $icd10 = $this->Icd10->select($icd10_id, $select);

                if (count($icd10) == 0) {
                    $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                    return;
                }

                $dt_soap_a_action = array(
                    'soap_a_id' => $soap_a_id,
                    'icd10_id' => $icd10_id,
                    'index' => $i
                );

                $this->Soap_a_action->create($dt_soap_a_action);
            }
        }

        $dt_soap_p = array(
            'soap_id' => $soap_id,
        );

        $soap_p_id = $this->Soap_p->create($dt_soap_p);

        $icd9cm_arr = $dt_post['icd9cm'];
        if ($icd9cm_arr != null) {
    
            for ($i=0; $i < count($icd9cm_arr) ; $i++) {
                if (!isset($icd9cm_arr[$i]['icd9cm_id'])) {
                    $this->REST_Return(400, 'field icd9cm_id in icd9cm is required');
                    return;
                }
                
                $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];

                $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);

                if (count($icd9cm) == 0) {
                    $this->REST_Return(404, 'there is no icd9cm with id ' . $icd9cm_id);
                    return;
                }

                $dt_soap_p_action = array(
                    'soap_p_id' => $soap_p_id,
                    'icd9cm_id' => $icd9cm_id,
                );

                $this->Soap_p_action->create($dt_soap_p_action);
            }
        }

        $laboratory_request_items_arr = $dt_post['laboratory_request_items'];
        if ($laboratory_request_items_arr != null) {
            for ($i=0; $i < count($laboratory_request_items_arr); $i++) { 
                if (!isset($laboratory_request_items_arr[$i]['laboratory_request_item_id'])) {
                    $this->REST_Return(400, 'field laboratory_request_item_id in laboratory_request_items is required');
                    return;
                }

                $laboratory_request_item_id = $laboratory_request_items_arr[$i]['laboratory_request_item_id'];

                $select = 'a.id';
                $where = array('a.id' => $laboratory_request_item_id);
                $laboratory_request_item = $this->Laboratory_request_item->select(0, $select, $where);

                if (count($laboratory_request_item) == 0) {
                    $this->REST_Return(404, 'there is no laboratory_request_item with id ' . $laboratory_request_item_id);
                    return;
                }

                $dt_resume_item = array(
                    'resume_id' => $id,
                    'item_id' => $laboratory_request_item_id,
                    'resume_item_type_id' => 1,
                );

                $this->Resume_item->replace($dt_resume_item);
            }
        }

        $radiology_request_items_arr = $dt_post['radiology_request_items'];
        if ($radiology_request_items_arr != null) {
            for ($i=0; $i < count($radiology_request_items_arr); $i++) { 
                if (!isset($radiology_request_items_arr[$i]['radiology_request_item_id'])) {
                    $this->REST_Return(400, 'field radiology_request_item_id in radiology_request_items is required');
                    return;
                }

                $radiology_request_item_id = $radiology_request_items_arr[$i]['radiology_request_item_id'];

                $select = 'a.id';
                $where = array('a.id' => $radiology_request_item_id);
                $radiology_request_item = $this->Radiology_request_item->select(0, $select, $where);

                if (count($radiology_request_item) == 0) {
                    $this->REST_Return(404, 'there is no radiology_request_item with id ' . $radiology_request_item_id);
                    return;
                }

                $dt_resume_item = array(
                    'resume_id' => $id,
                    'item_id' => $radiology_request_item_id,
                    'resume_item_type_id' => 2,
                );

                $this->Resume_item->replace($dt_resume_item);
            }
        }

        $physiotherapy_request_items_arr = $dt_post['physiotherapy_request_items'];
        if ($physiotherapy_request_items_arr != null) {
            for ($i=0; $i < count($physiotherapy_request_items_arr); $i++) { 
                if (!isset($physiotherapy_request_items_arr[$i]['physiotherapy_request_item_id'])) {
                    $this->REST_Return(400, 'field physiotherapy_request_item_id in physiotherapy_request_items is required');
                    return;
                }

                $physiotherapy_request_item_id = $physiotherapy_request_items_arr[$i]['physiotherapy_request_item_id'];

                $select = 'a.id';
                $where = array('a.id' => $physiotherapy_request_item_id);
                $physiotherapy_request_item = $this->Physiotherapy_request_item->select(0, $select, $where);

                if (count($physiotherapy_request_item) == 0) {
                    $this->REST_Return(404, 'there is no physiotherapy_request_item with id ' . $physiotherapy_request_item_id);
                    return;
                }

                $dt_resume_item = array(
                    'resume_id' => $id,
                    'item_id' => $physiotherapy_request_item_id,
                    'resume_item_type_id' => 3,
                );

                $this->Resume_item->replace($dt_resume_item);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_resume,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create resume', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }

    public function pre_create_post() {
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
    
        $response_data = array(
            'id'     => $dt_post['registration_destination_id']
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
    
        $resume_id = $id;
    
        if ($resume_id === 0 || $resume_id === "0") {
            $this->REST_Return(400, 'resume_id is required');
            return;
        }

        $select = 'a.id, a.soap_id';
        $resume = $this->$model->select($resume_id, $select);

        if (count($resume) == 0) {
            $this->REST_Return(404, 'there is no resume with id ' . $resume_id);
            return;
        }

        $soap_id = $resume[0]->soap_id;

        $exit_id = $dt_put['exit_id'];

        $select = 'a.id';
        $exit = $this->Exits->select($exit_id, $select);

        if (count($exit) == 0) {
            $this->REST_Return(404, 'there is no exit with id ' . $exit_id);
            return;
        }

        $physical_check = $dt_put['physical_check'];
        $significant_medications = $dt_put['significant_medications'];
        $take_home_medications = $dt_put['take_home_medications'];
        $exit_anythingelse = $dt_put['exit_anythingelse'];
        $exit_condition_type_id = $dt_put['exit_condition_type_id'];
        $exit_condition_anythingelse = $dt_put['exit_condition_anythingelse'];
        $care_instruction_control = $dt_put['care_instruction_control'];
        $care_instruction_control_date = $dt_put['care_instruction_control_date'];
        $care_instruction_anythingelse = $dt_put['care_instruction_anythingelse'];
        $summary_medical_history = $dt_put['summary_medical_history'];
        $home_education = $dt_put['home_education'];
        $indication_for_hospitalization = $dt_put['indication_for_hospitalization'];

        /* Start Transaction */
        $this->db->trans_start();
     
        $dt_soap = array(
            'exit_id'                        => $exit_id,
            'significant_medications'        => $significant_medications,
            'take_home_medications'          => $take_home_medications,
            'exit_anythingelse'              => $exit_anythingelse,
            'exit_condition_type_id'         => $exit_condition_type_id,
            'exit_condition_anythingelse'    => $exit_condition_anythingelse,
            'care_instruction_control'       => $care_instruction_control,
            'care_instruction_control_date'  => $care_instruction_control_date,
            'care_instruction_anythingelse'  => $care_instruction_anythingelse,
            'summary_medical_history'        => $summary_medical_history,
            'home_education'                 => $home_education,
            'indication_for_hospitalization' => $indication_for_hospitalization,
            'is_resume_exit'                 => 1,
            'user_updated'                   => $user_id,
            'date_updated'                   => date('Y-m-d H:i:s'),
        );
    
        $this->Soap->update($soap_id, $dt_soap);

        // ST_GENERALIST
        $head = $dt_put['head'];
        $head_hair_color = $dt_put['head_hair_color'];
        $head_hair_condition = $dt_put['head_hair_condition'];
        $eye_conjunctive = $dt_put['eye_conjunctive'];
        $eye_sclera = $dt_put['eye_sclera'];
        $eye_pupil = $dt_put['eye_pupil'];
        $eye_light_reflex = $dt_put['eye_light_reflex'];
        $mouth_mucosa = $dt_put['mouth_mucosa'];
        $mouth_discoloration = $dt_put['mouth_discoloration'];
        $mouth_tongue = $dt_put['mouth_tongue'];
        $mouth_caries = $dt_put['mouth_caries'];
        $neck_kgb = $dt_put['neck_kgb'];
        $neck_trachea_position = $dt_put['neck_trachea_position'];
        $neck_massa = $dt_put['neck_massa'];
        $neck_jvp = $dt_put['neck_jvp'];
        $thorax_insp_chest = $dt_put['thorax_insp_chest'];
        $thorax_insp_retraction = $dt_put['thorax_insp_retraction'];
        $thorax_palp_chest = $dt_put['thorax_palp_chest'];
        $thorax_palp_fremitus = $dt_put['thorax_palp_fremitus'];
        $thorax_palp_crepitation = $dt_put['thorax_palp_crepitation'];
        $thorax_ausk_pul_vasiculer = $dt_put['thorax_ausk_pul_vasiculer'];
        $thorax_ausk_pul_rhonki = $dt_put['thorax_ausk_pul_rhonki'];
        $thorax_ausk_pul_wheezing = $dt_put['thorax_ausk_pul_wheezing'];
        $thorax_ausk_cor_heart_sound = $dt_put['thorax_ausk_cor_heart_sound'];
        $thorax_ausk_cor_murmur = $dt_put['thorax_ausk_cor_murmur'];
        $thorax_ausk_cor_gallop = $dt_put['thorax_ausk_cor_gallop'];
        $abdomen_supel = $dt_put['abdomen_supel'];
        $abdomen_bowel_sounds = $dt_put['abdomen_bowel_sounds'];
        $abdomen_tenderness = $dt_put['abdomen_tenderness'];
        $abdomen_hepar_lien = $dt_put['abdomen_hepar_lien'];
        $abdomen_other_physical_check = $dt_put['abdomen_other_physical_check'];
        $extremity_acral = $dt_put['extremity_acral'];
        $extremity_crt = $dt_put['extremity_crt'];
        $extremity_edema = $dt_put['extremity_edema'];
        $genitalia = $dt_put['genitalia'];
        $localist_status = $dt_put['localist_status'];
        $ophthal_status = $dt_put['ophthal_status'];
        $neurologist_status = $dt_put['neurologist_status'];

        $dt_soap_o = array(
            'soap_id' => $soap_id,
            'physical_check' => $physical_check,
            'head' => $head,
            'head_hair_color' => $head_hair_color,
            'head_hair_condition' => $head_hair_condition,
            'eye_conjunctive' => $eye_conjunctive,
            'eye_sclera' => $eye_sclera,
            'eye_pupil' => $eye_pupil,
            'eye_light_reflex' => $eye_light_reflex,
            'mouth_mucosa' => $mouth_mucosa,
            'mouth_discoloration' => $mouth_discoloration,
            'mouth_tongue' => $mouth_tongue,
            'mouth_caries' => $mouth_caries,
            'neck_kgb' => $neck_kgb,
            'neck_trachea_position' => $neck_trachea_position,
            'neck_massa' => $neck_massa,
            'neck_jvp' => $neck_jvp,
            'thorax_insp_chest' => $thorax_insp_chest,
            'thorax_insp_retraction' => $thorax_insp_retraction,
            'thorax_palp_chest' => $thorax_palp_chest,
            'thorax_palp_fremitus' => $thorax_palp_fremitus,
            'thorax_palp_crepitation' => $thorax_palp_crepitation,
            'thorax_ausk_pul_vasiculer' => $thorax_ausk_pul_vasiculer,
            'thorax_ausk_pul_rhonki' => $thorax_ausk_pul_rhonki,
            'thorax_ausk_pul_wheezing' => $thorax_ausk_pul_wheezing,
            'thorax_ausk_cor_heart_sound' => $thorax_ausk_cor_heart_sound,
            'thorax_ausk_cor_murmur' => $thorax_ausk_cor_murmur,
            'thorax_ausk_cor_gallop' => $thorax_ausk_cor_gallop,
            'abdomen_supel' => $abdomen_supel,
            'abdomen_bowel_sounds' => $abdomen_bowel_sounds,
            'abdomen_tenderness' => $abdomen_tenderness,
            'abdomen_hepar_lien' => $abdomen_hepar_lien,
            'abdomen_other_physical_check' => $abdomen_other_physical_check,
            'extremity_acral' => $extremity_acral,
            'extremity_crt' => $extremity_crt,
            'extremity_edema' => $extremity_edema,
            'genitalia' => $genitalia,
            'localist_status' => $localist_status,
            'ophthal_status' => $ophthal_status,
            'neurologist_status' => $neurologist_status,
            'date_updated' => date('Y-m-d H:i:s'),
            'user_updated' => $user_id,
        );

        $where = array(
            'soap_id' => $soap_id
        );
        $aff_row = $this->Soap_o->update_v2(0, $dt_soap_o, $where);
        if ($aff_row > 1) {
            $this->REST_Return(422, 'something wrong when trying to update soap_o');
            return;
        }

        $icd10_notes = $dt_put['icd10_notes'];
        $therapy_plan = $dt_put['therapy_plan'];

        $dt_resume = array(
            'icd10_notes' => $icd10_notes,
            'therapy_plan' => $therapy_plan,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),
        );

        $aff_row = $this->$model->update_v2($resume_id, $dt_resume);
        if ($aff_row > 1) {
            $this->REST_Return(422, 'something wrong when trying to update resume');
            return;
        }

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_a = $this->Soap_a->select(0, $select, $where);
        $soap_a_id = $soap_a[0]->id;

        if ($soap_a_id == null) {
            $this->REST_Return(404, 'there is no soap_a with soap_id ' . $soap_id);
            return;
        }
    
        $select = 'a.id';
        $where = array('soap_a_id' => $soap_a_id, 'is_delete' => 0);
        $soap_a_action = $this->Soap_a_action->select(0, $select, $where);

        if (count($soap_a_action) > 0) {
            $dt_soap_a_action = array(
                'is_delete' => 1
            );
            $aff_rows = $this->Soap_a_action->update_v2(0, $dt_soap_a_action, $where);
            
            if (count($soap_a_action) != $aff_rows) {
                $this->REST_Return(422, 'something wrong when trying to delete soap_a_action');
                return;
            }
        }

        $icd10_arr = $dt_put['icd10'];
        if ($icd10_arr != null) {
            for ($i=0; $i < count($icd10_arr) ; $i++) {
                if (!isset($icd10_arr[$i]['icd10_id'])) {
                    $this->REST_Return(400, 'field icd10_id in icd10 is required');
                }
                
                $icd10_id = $icd10_arr[$i]['icd10_id'];

                $icd10 = $this->Icd10->select($icd10_id, $select);

                if (count($icd10) == 0) {
                    $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                    return;
                }

                $dt_soap_a_action = array(
                    'soap_a_id' => $soap_a_id,
                    'icd10_id' => $icd10_id,
                    'index' => $i,
                    'is_delete' => 0
                );

                $select = 'a.id';
                $where = array('icd10_id' => $icd10_id, 'soap_a_id' => $soap_a_id);
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where);

                if (count($soap_a_action) == 0) {
                    $this->Soap_a_action->create($dt_soap_a_action);
                } else {
                    $soap_p_action_id = $soap_a_action[0]->id;
                    $this->Soap_a_action->update_v2($soap_p_action_id, $dt_soap_a_action);
                }
            }
        }

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_p = $this->Soap_p->select(0, $select, $where);
        $soap_p_id = $soap_p[0]->id;

        if ($soap_p_id == null) {
            $this->REST_Return(404, 'there is no soap_p with soap_id ' . $soap_id);
            return;
        }

        $select = 'a.id';
        $where = array('soap_p_id' => $soap_p_id, 'is_delete' => 0);
        $soap_p_action = $this->Soap_p_action->select(0, $select, $where);

        if (count($soap_p_action) > 0) {
            $dt_soap_p_action = array(
                'is_delete' => 1
            );
            $aff_rows = $this->Soap_p_action->update_v2(0, $dt_soap_p_action, $where);

            if (count($soap_p_action) != $aff_rows) {
                $this->REST_Return(422, 'something wrong when trying to delete soap_p_action');
                return;
            }
        }

        $icd9cm_arr = $dt_put['icd9cm'];
        if ($icd9cm_arr != null) {
            for ($i=0; $i < count($icd9cm_arr) ; $i++) {
                if (!isset($icd9cm_arr[$i]['icd9cm_id'])) {
                    $this->REST_Return(400, 'field icd9cm_id in icd9cm is required');
                    return;
                }
                
                $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];

                $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);

                if (count($icd9cm) == 0) {
                    $this->REST_Return(404, 'there is no icd9cm with id ' . $icd9cm_id);
                    return;
                }

                $dt_soap_p_action = array(
                    'soap_p_id' => $soap_p_id,
                    'icd9cm_id' => $icd9cm_id,
                    'is_delete' => 0,
                );

                $select = 'a.id';
                $where = array('icd9cm_id' => $icd9cm_id, 'soap_p_id' => $soap_p_id);
                $soap_p_action = $this->Soap_p_action->select(0, $select, $where);

                if (count($soap_p_action) == 0) {
                    $this->Soap_p_action->create($dt_soap_p_action);
                } else {
                    $soap_p_action_id = $soap_p_action[0]->id;
                    $this->Soap_p_action->update_v2($soap_p_action_id, $dt_soap_p_action);
                }
            }
        }

        $select = 'a.resume_id';
        $where = array(
            'resume_id' => $resume_id,
            'resume_item_type_id' => 1,
            'is_delete' => 0,
        );
        $resume_item = $this->Resume_item->select(0, $select, $where);

        if (count($resume_item) > 0) {
            $dt_resume_item = array(
                'is_delete' => 1
            );

            $this->Resume_item->update_v2(0, $dt_resume_item, $where);
        }

        $laboratory_request_items_arr = $dt_put['laboratory_request_items'];
        if ($laboratory_request_items_arr != null) {
            for ($i=0; $i < count($laboratory_request_items_arr); $i++) { 
                if (!isset($laboratory_request_items_arr[$i]['laboratory_request_item_id'])) {
                    $this->REST_Return(400, 'field laboratory_request_item_id in laboratory_request_items is required');
                    return;
                }

                $laboratory_request_item_id = $laboratory_request_items_arr[$i]['laboratory_request_item_id'];

                $select = 'a.id';
                $where = array('a.id' => $laboratory_request_item_id);
                $laboratory_request_item = $this->Laboratory_request_item->select(0, $select, $where);

                if (count($laboratory_request_item) == 0) {
                    $this->REST_Return(404, 'there is no laboratory_request_item with id ' . $laboratory_request_item_id);
                    return;
                }

                $dt_resume_item = array(
                    'resume_id' => $id,
                    'item_id' => $laboratory_request_item_id,
                    'resume_item_type_id' => 1,
                    'is_delete' => 0,
                );

                $this->Resume_item->replace($dt_resume_item);
            }
        }

        $select = 'a.resume_id';
        $where = array(
            'resume_id' => $resume_id,
            'resume_item_type_id' => 2,
            'is_delete' => 0,
        );
        $resume_item = $this->Resume_item->select(0, $select, $where);

        if (count($resume_item) > 0) {
            $dt_resume_item = array(
                'is_delete' => 1
            );

            $this->Resume_item->update_v2(0, $dt_resume_item, $where);
        }

        $radiology_request_items_arr = $dt_put['radiology_request_items'];
        if ($radiology_request_items_arr != null) {
            for ($i=0; $i < count($radiology_request_items_arr); $i++) { 
                if (!isset($radiology_request_items_arr[$i]['radiology_request_item_id'])) {
                    $this->REST_Return(400, 'field radiology_request_item_id in radiology_request_items is required');
                    return;
                }

                $radiology_request_item_id = $radiology_request_items_arr[$i]['radiology_request_item_id'];

                $select = 'a.id';
                $where = array('a.id' => $radiology_request_item_id);
                $radiology_request_item = $this->Radiology_request_item->select(0, $select, $where);

                if (count($radiology_request_item) == 0) {
                    $this->REST_Return(404, 'there is no radiology_request_item with id ' . $radiology_request_item_id);
                    return;
                }

                $dt_resume_item = array(
                    'resume_id' => $id,
                    'item_id' => $radiology_request_item_id,
                    'resume_item_type_id' => 2,
                    'is_delete' => 0,
                );

                $this->Resume_item->replace($dt_resume_item);
            }
        }

        $select = 'a.resume_id';
        $where = array(
            'resume_id' => $resume_id,
            'resume_item_type_id' => 3,
            'is_delete' => 0,
        );
        $resume_item = $this->Resume_item->select(0, $select, $where);

        if (count($resume_item) > 0) {
            $dt_resume_item = array(
                'is_delete' => 1
            );

            $this->Resume_item->update_v2(0, $dt_resume_item, $where);
        }

        $physiotherapy_request_items_arr = $dt_put['physiotherapy_request_items'];
        if ($physiotherapy_request_items_arr != null) {
            for ($i=0; $i < count($physiotherapy_request_items_arr); $i++) { 
                if (!isset($physiotherapy_request_items_arr[$i]['physiotherapy_request_item_id'])) {
                    $this->REST_Return(400, 'field physiotherapy_request_item_id in physiotherapy_request_items is required');
                    return;
                }

                $physiotherapy_request_item_id = $physiotherapy_request_items_arr[$i]['physiotherapy_request_item_id'];

                $select = 'a.id';
                $where = array('a.id' => $physiotherapy_request_item_id);
                $physiotherapy_request_item = $this->Physiotherapy_request_item->select(0, $select, $where);

                if (count($physiotherapy_request_item) == 0) {
                    $this->REST_Return(404, 'there is no physiotherapy_request_item with id ' . $physiotherapy_request_item_id);
                    return;
                }

                $dt_resume_item = array(
                    'resume_id' => $id,
                    'item_id' => $physiotherapy_request_item_id,
                    'resume_item_type_id' => 3,
                    'is_delete' => 0,
                );

                $this->Resume_item->replace($dt_resume_item);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_resume,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update resume', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function bride_lis($url = null,$param = null) {
        // url : 192.168.100.153/api_lis/index.php
        $url_acc   = '192.168.100.153';
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        // $authorization[] = "Authorization:Bearer ".$token; 
        // $authorization[] = "X-Session-ID:".$SESSIONID; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json;charset=utf-8"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
    }
}