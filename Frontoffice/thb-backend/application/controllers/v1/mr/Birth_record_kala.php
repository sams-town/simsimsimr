<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Birth_record_kala extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Birth_record_model", "Birth_record");
        $this->load->model("$this->directory/Partograph_model", "Partograph");
        $this->load->model("$this->directory/Monitoring_kala_four_model", "Monitoring_kala_four");

        $this->create_rules = array(
            array('field' => 'birth_record_id', 'label' => 'birth_record_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->save_kala_one_rules = array(
            array('field' => 'birth_record_id', 'label' => 'birth_record_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'kala_one_latent_phase', 'label' => 'kala_one_latent_phase', 'rules' => 'trim'),
            array('field' => 'kala_one_intervention_type_id', 'label' => 'kala_one_intervention_type_id', 'rules' => 'trim'),
            array('field' => 'kala_one_alert_line_type_id', 'label' => 'kala_one_alert_line_type_id', 'rules' => 'trim'),
            array('field' => 'kala_one_active_phase', 'label' => 'kala_one_active_phase', 'rules' => 'trim'),
            array('field' => 'kala_one_problem_management', 'label' => 'kala_one_problem_management', 'rules' => 'trim'),
            array('field' => 'kala_one_result', 'label' => 'kala_one_result', 'rules' => 'trim'),
        );

        $this->save_kala_two_rules = array(
            array('field' => 'birth_record_id', 'label' => 'birth_record_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'kala_two_episiotomy_type_id', 'label' => 'kala_two_episiotomy_type_id', 'rules' => 'trim'),
            array('field' => 'kala_two_episiotomy', 'label' => 'kala_two_episiotomy', 'rules' => 'trim'),
            array('field' => 'kala_two_companion_type_id', 'label' => 'kala_two_companion_type_id', 'rules' => 'trim'),
            array('field' => 'kala_two_fetal_distress_type_id', 'label' => 'kala_two_fetal_distress_type_id', 'rules' => 'trim'),
            array('field' => 'kala_two_fetal_distress_a', 'label' => 'kala_two_fetal_distress_a', 'rules' => 'trim'),
            array('field' => 'kala_two_fetal_distress_b', 'label' => 'kala_two_fetal_distress_b', 'rules' => 'trim'),
            array('field' => 'kala_two_monitoring_result', 'label' => 'kala_two_monitoring_result', 'rules' => 'trim'),
            array('field' => 'kala_two_shoulder_dystocia_type_id', 'label' => 'kala_two_shoulder_dystocia_type_id', 'rules' => 'trim'),
            array('field' => 'kala_two_shoulder_dystocia', 'label' => 'kala_two_shoulder_dystocia', 'rules' => 'trim'),
            array('field' => 'kala_two_problem_management', 'label' => 'kala_two_problem_management', 'rules' => 'trim'),
        );

        $this->save_kala_three_rules = array(
            array('field' => 'birth_record_id', 'label' => 'birth_record_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'kala_three_initiation_type_id', 'label' => 'kala_three_initiation_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_initiation', 'label' => 'kala_three_initiation', 'rules' => 'trim'),
            array('field' => 'kala_three_long', 'label' => 'kala_three_long', 'rules' => 'trim'),
            array('field' => 'kala_three_oxytocin_type_id', 'label' => 'kala_three_oxytocin_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_oxytocin', 'label' => 'kala_three_oxytocin', 'rules' => 'trim'),
            array('field' => 'kala_three_oxytocin_reason', 'label' => 'kala_three_oxytocin_reason', 'rules' => 'trim'),
            array('field' => 'kala_three_oxytocin_second_type_id', 'label' => 'kala_three_oxytocin_second_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_oxytocin_second', 'label' => 'kala_three_oxytocin_second', 'rules' => 'trim'),
            array('field' => 'kala_three_umbilical_cord_type_id', 'label' => 'kala_three_umbilical_cord_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_umbilical_cord', 'label' => 'kala_three_umbilical_cord', 'rules' => 'trim'),
            array('field' => 'kala_three_uterine_fundus_type_id', 'label' => 'kala_three_uterine_fundus_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_uterine_fundus', 'label' => 'kala_three_uterine_fundus', 'rules' => 'trim'),
            array('field' => 'kala_three_placenta_type_id', 'label' => 'kala_three_placenta_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_placenta_a', 'label' => 'kala_three_placenta_a', 'rules' => 'trim'),
            array('field' => 'kala_three_placenta_b', 'label' => 'kala_three_placenta_b', 'rules' => 'trim'),
            array('field' => 'kala_three_placenta_not_type_id', 'label' => 'kala_three_placenta_not_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_placenta_not', 'label' => 'kala_three_placenta_not', 'rules' => 'trim'),
            array('field' => 'kala_three_laceration_type_id', 'label' => 'kala_three_laceration_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_laceration', 'label' => 'kala_three_laceration', 'rules' => 'trim'),
            array('field' => 'kala_three_uterine_atony_type_id', 'label' => 'kala_three_uterine_atony_type_id', 'rules' => 'trim'),
            array('field' => 'kala_three_uterine_atony', 'label' => 'kala_three_uterine_atony', 'rules' => 'trim'),
            array('field' => 'kala_three_amount_blood', 'label' => 'kala_three_amount_blood', 'rules' => 'trim'),
            array('field' => 'kala_three_problem_management', 'label' => 'kala_three_problem_management', 'rules' => 'trim'),
            array('field' => 'kala_three_result', 'label' => 'kala_three_result', 'rules' => 'trim'),
        );

        $this->save_kala_four_rules = array(
            array('field' => 'birth_record_id', 'label' => 'birth_record_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'kala_four_mother_ku', 'label' => 'kala_four_mother_ku', 'rules' => 'trim'),
            array('field' => 'kala_four_mother_td', 'label' => 'kala_four_mother_td', 'rules' => 'trim'),
            array('field' => 'kala_four_pulse', 'label' => 'kala_four_pulse', 'rules' => 'trim'),
            array('field' => 'kala_four_breath', 'label' => 'kala_four_breath', 'rules' => 'trim'),
            array('field' => 'kala_four_problem_management', 'label' => 'kala_four_problem_management', 'rules' => 'trim'),
            array('field' => 'kala_four_result', 'label' => 'kala_four_result', 'rules' => 'trim'),
        );

        $this->save_monitoring_kala_four_rules = array(
            array('field' => 'birth_record_id', 'label' => 'birth_record_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'monitoring', 'label' => 'monitoring', 'rules' => 'trim'),
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
    
        $birth_record_kala_id = $id;
    
        if ($birth_record_kala_id === 0 || $birth_record_kala_id === "0") {
            $birth_record_id = $q_strings['birth_record_id'] ?? null;
            if ($birth_record_id == null) {
                $this->REST_Return(400, 'birth_record_id is required if birth_record_kala_id is not provided');
                return;
            }
            $this->$model->whereFields['a.birth_record_id'] = $birth_record_id;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($birth_record_kala_id);
        $response_data = array();

        if (count($output) > 0) {
            $birth_record_kala_id = $output[0]->id;

            $output[0]->kala_two_companion_arry = ($output[0]->kala_two_companion_arry != '') ? explode(',', $output[0]->kala_two_companion_arry): [];

            $select = 'a.*';
            $where = array(
                'a.birth_record_kala_id' => $birth_record_kala_id,
                'a.is_delete' => 0,
            );
            $join = array();
            $order = array(
                'a.time_id' => 'ASC', 'a.time' => 'ASC'
            );
            $monitoring_kala_four = $this->Monitoring_kala_four->select(0, $select, $where, $join, $order);

            $output[0]->monitoring_kala_four = $monitoring_kala_four;
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

        $birth_record_id = $dt_post['birth_record_id'];
        
        $select = 'a.id';
        $birth_record = $this->Birth_record->select($birth_record_id, $select);
        
        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }

        $kala_one_latent_phase = $dt_post['kala_one_latent_phase'];
        $kala_one_intervention_type_id = $dt_post['kala_one_intervention_type_id'];
        $kala_one_alert_line_type_id = $dt_post['kala_one_alert_line_type_id'];
        $kala_one_active_phase = $dt_post['kala_one_active_phase'];
        $kala_one_problem_management = $dt_post['kala_one_problem_management'];
        $kala_one_result = $dt_post['kala_one_result'];
        $kala_two_episiotomy_type_id = $dt_post['kala_two_episiotomy_type_id'];
        $kala_two_episiotomy = $dt_post['kala_two_episiotomy'];
        $kala_two_companion_type_id = $dt_post['kala_two_companion_type_id'];
        $kala_two_fetal_distress_type_id = $dt_post['kala_two_fetal_distress_type_id'];
        $kala_two_fetal_distress_a = $dt_post['kala_two_fetal_distress_a'];
        $kala_two_fetal_distress_b = $dt_post['kala_two_fetal_distress_b'];
        $kala_two_monitoring_result = $dt_post['kala_two_monitoring_result'];
        $kala_two_shoulder_dystocia_type_id = $dt_post['kala_two_shoulder_dystocia_type_id'];
        $kala_two_shoulder_dystocia = $dt_post['kala_two_shoulder_dystocia'];
        $kala_two_problem_management = $dt_post['kala_two_problem_management'];
        $kala_three_initiation_type_id = $dt_post['kala_three_initiation_type_id'];
        $kala_three_initiation = $dt_post['kala_three_initiation'];
        $kala_three_long = $dt_post['kala_three_long'];
        $kala_three_oxytocin_type_id = $dt_post['kala_three_oxytocin_type_id'];
        $kala_three_oxytocin = $dt_post['kala_three_oxytocin'];
        $kala_three_oxytocin_reason = $dt_post['kala_three_oxytocin_reason'];
        $kala_three_oxytocin_second_type_id = $dt_post['kala_three_oxytocin_second_type_id'];
        $kala_three_oxytocin_second = $dt_post['kala_three_oxytocin_second'];
        $kala_three_umbilical_cord_type_id = $dt_post['kala_three_umbilical_cord_type_id'];
        $kala_three_umbilical_cord = $dt_post['kala_three_umbilical_cord'];
        $kala_three_uterine_fundus_type_id = $dt_post['kala_three_uterine_fundus_type_id'];
        $kala_three_uterine_fundus = $dt_post['kala_three_uterine_fundus'];
        $kala_three_placenta_type_id = $dt_post['kala_three_placenta_type_id'];
        $kala_three_placenta_a = $dt_post['kala_three_placenta_a'];
        $kala_three_placenta_b = $dt_post['kala_three_placenta_b'];
        $kala_three_placenta_not_type_id = $dt_post['kala_three_placenta_not_type_id'];
        $kala_three_placenta_not = $dt_post['kala_three_placenta_not'];
        $kala_three_laceration_type_id = $dt_post['kala_three_laceration_type_id'];
        $kala_three_laceration = $dt_post['kala_three_laceration'];
        $kala_three_uterine_atony_type_id = $dt_post['kala_three_uterine_atony_type_id'];
        $kala_three_uterine_atony = $dt_post['kala_three_uterine_atony'];
        $kala_three_amount_blood = $dt_post['kala_three_amount_blood'];
        $kala_three_problem_management = $dt_post['kala_three_problem_management'];
        $kala_three_result = $dt_post['kala_three_result'];
        $kala_four_mother_ku = $dt_post['kala_four_mother_ku'];
        $kala_four_mother_td = $dt_post['kala_four_mother_td'];
        $kala_four_pulse = $dt_post['kala_four_pulse'];
        $kala_four_breath = $dt_post['kala_four_breath'];
        $kala_four_problem_management = $dt_post['kala_four_problem_management'];
        $kala_four_result = $dt_post['kala_four_result'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_birth_record_kala = array(
            'birth_record_id' => $birth_record_id,
            'kala_one_latent_phase' => $kala_one_latent_phase,
            'kala_one_intervention_type_id' => $kala_one_intervention_type_id,
            'kala_one_alert_line_type_id' => $kala_one_alert_line_type_id,
            'kala_one_active_phase' => $kala_one_active_phase,
            'kala_one_problem_management' => $kala_one_problem_management,
            'kala_one_result' => $kala_one_result,
            'kala_two_episiotomy_type_id' => $kala_two_episiotomy_type_id,
            'kala_two_episiotomy' => $kala_two_episiotomy,
            'kala_two_companion_type_id' => $kala_two_companion_type_id,
            'kala_two_fetal_distress_type_id' => $kala_two_fetal_distress_type_id,
            'kala_two_fetal_distress_a' => $kala_two_fetal_distress_a,
            'kala_two_fetal_distress_b' => $kala_two_fetal_distress_b,
            'kala_two_monitoring_result' => $kala_two_monitoring_result,
            'kala_two_shoulder_dystocia_type_id' => $kala_two_shoulder_dystocia_type_id,
            'kala_two_shoulder_dystocia' => $kala_two_shoulder_dystocia,
            'kala_two_problem_management' => $kala_two_problem_management,
            'kala_three_initiation_type_id' => $kala_three_initiation_type_id,
            'kala_three_initiation' => $kala_three_initiation,
            'kala_three_long' => $kala_three_long,
            'kala_three_oxytocin_type_id' => $kala_three_oxytocin_type_id,
            'kala_three_oxytocin' => $kala_three_oxytocin,
            'kala_three_oxytocin_reason' => $kala_three_oxytocin_reason,
            'kala_three_oxytocin_second_type_id' => $kala_three_oxytocin_second_type_id,
            'kala_three_oxytocin_second' => $kala_three_oxytocin_second,
            'kala_three_umbilical_cord_type_id' => $kala_three_umbilical_cord_type_id,
            'kala_three_umbilical_cord' => $kala_three_umbilical_cord,
            'kala_three_uterine_fundus_type_id' => $kala_three_uterine_fundus_type_id,
            'kala_three_uterine_fundus' => $kala_three_uterine_fundus,
            'kala_three_placenta_type_id' => $kala_three_placenta_type_id,
            'kala_three_placenta_a' => $kala_three_placenta_a,
            'kala_three_placenta_b' => $kala_three_placenta_b,
            'kala_three_placenta_not_type_id' => $kala_three_placenta_not_type_id,
            'kala_three_placenta_not' => $kala_three_placenta_not,
            'kala_three_laceration_type_id' => $kala_three_laceration_type_id,
            'kala_three_laceration' => $kala_three_laceration,
            'kala_three_uterine_atony_type_id' => $kala_three_uterine_atony_type_id,
            'kala_three_uterine_atony' => $kala_three_uterine_atony,
            'kala_three_amount_blood' => $kala_three_amount_blood,
            'kala_three_problem_management' => $kala_three_problem_management,
            'kala_three_result' => $kala_three_result,
            'kala_four_mother_ku' => $kala_four_mother_ku,
            'kala_four_mother_td' => $kala_four_mother_td,
            'kala_four_pulse' => $kala_four_pulse,
            'kala_four_breath' => $kala_four_breath,
            'kala_four_problem_management' => $kala_four_problem_management,
            'kala_four_result' => $kala_four_result,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_birth_record_kala);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_birth_record_kala,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create birth_record_kala', $response_data);
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
    
        $birth_record_kala_id = $id;
    
        if ($birth_record_kala_id === 0 || $birth_record_kala_id === "0") {
            $this->REST_Return(400, 'birth_record_kala_id is required');
            return;
        }

        $select = 'a.id';
        $birth_record_kala = $this->$model->select($birth_record_kala_id, $select);

        if (count($birth_record_kala) == 0) {
            $this->REST_Return(404, 'there is no birth_record_kala with id ' . $birth_record_kala_id);
            return;
        }

        $kala_one_latent_phase = $dt_put['kala_one_latent_phase'];
        $kala_one_intervention_type_id = $dt_put['kala_one_intervention_type_id'];
        $kala_one_alert_line_type_id = $dt_put['kala_one_alert_line_type_id'];
        $kala_one_active_phase = $dt_put['kala_one_active_phase'];
        $kala_one_problem_management = $dt_put['kala_one_problem_management'];
        $kala_one_result = $dt_put['kala_one_result'];
        $kala_two_episiotomy_type_id = $dt_put['kala_two_episiotomy_type_id'];
        $kala_two_episiotomy = $dt_put['kala_two_episiotomy'];
        $kala_two_companion_type_id = $dt_put['kala_two_companion_type_id'];
        $kala_two_fetal_distress_type_id = $dt_put['kala_two_fetal_distress_type_id'];
        $kala_two_fetal_distress_a = $dt_put['kala_two_fetal_distress_a'];
        $kala_two_fetal_distress_b = $dt_put['kala_two_fetal_distress_b'];
        $kala_two_monitoring_result = $dt_put['kala_two_monitoring_result'];
        $kala_two_shoulder_dystocia_type_id = $dt_put['kala_two_shoulder_dystocia_type_id'];
        $kala_two_shoulder_dystocia = $dt_put['kala_two_shoulder_dystocia'];
        $kala_two_problem_management = $dt_put['kala_two_problem_management'];
        $kala_three_initiation_type_id = $dt_put['kala_three_initiation_type_id'];
        $kala_three_initiation = $dt_put['kala_three_initiation'];
        $kala_three_long = $dt_put['kala_three_long'];
        $kala_three_oxytocin_type_id = $dt_put['kala_three_oxytocin_type_id'];
        $kala_three_oxytocin = $dt_put['kala_three_oxytocin'];
        $kala_three_oxytocin_reason = $dt_put['kala_three_oxytocin_reason'];
        $kala_three_oxytocin_second_type_id = $dt_put['kala_three_oxytocin_second_type_id'];
        $kala_three_oxytocin_second = $dt_put['kala_three_oxytocin_second'];
        $kala_three_umbilical_cord_type_id = $dt_put['kala_three_umbilical_cord_type_id'];
        $kala_three_umbilical_cord = $dt_put['kala_three_umbilical_cord'];
        $kala_three_uterine_fundus_type_id = $dt_put['kala_three_uterine_fundus_type_id'];
        $kala_three_uterine_fundus = $dt_put['kala_three_uterine_fundus'];
        $kala_three_placenta_type_id = $dt_put['kala_three_placenta_type_id'];
        $kala_three_placenta_a = $dt_put['kala_three_placenta_a'];
        $kala_three_placenta_b = $dt_put['kala_three_placenta_b'];
        $kala_three_placenta_not_type_id = $dt_put['kala_three_placenta_not_type_id'];
        $kala_three_placenta_not = $dt_put['kala_three_placenta_not'];
        $kala_three_laceration_type_id = $dt_put['kala_three_laceration_type_id'];
        $kala_three_laceration = $dt_put['kala_three_laceration'];
        $kala_three_uterine_atony_type_id = $dt_put['kala_three_uterine_atony_type_id'];
        $kala_three_uterine_atony = $dt_put['kala_three_uterine_atony'];
        $kala_three_amount_blood = $dt_put['kala_three_amount_blood'];
        $kala_three_problem_management = $dt_put['kala_three_problem_management'];
        $kala_three_result = $dt_put['kala_three_result'];
        $kala_four_mother_ku = $dt_put['kala_four_mother_ku'];
        $kala_four_mother_td = $dt_put['kala_four_mother_td'];
        $kala_four_pulse = $dt_put['kala_four_pulse'];
        $kala_four_breath = $dt_put['kala_four_breath'];
        $kala_four_problem_management = $dt_put['kala_four_problem_management'];
        $kala_four_result = $dt_put['kala_four_result'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_birth_record_kala = array(
            'kala_one_latent_phase' => $kala_one_latent_phase,
            'kala_one_intervention_type_id' => $kala_one_intervention_type_id,
            'kala_one_alert_line_type_id' => $kala_one_alert_line_type_id,
            'kala_one_active_phase' => $kala_one_active_phase,
            'kala_one_problem_management' => $kala_one_problem_management,
            'kala_one_result' => $kala_one_result,
            'kala_two_episiotomy_type_id' => $kala_two_episiotomy_type_id,
            'kala_two_episiotomy' => $kala_two_episiotomy,
            'kala_two_companion_type_id' => $kala_two_companion_type_id,
            'kala_two_fetal_distress_type_id' => $kala_two_fetal_distress_type_id,
            'kala_two_fetal_distress_a' => $kala_two_fetal_distress_a,
            'kala_two_fetal_distress_b' => $kala_two_fetal_distress_b,
            'kala_two_monitoring_result' => $kala_two_monitoring_result,
            'kala_two_shoulder_dystocia_type_id' => $kala_two_shoulder_dystocia_type_id,
            'kala_two_shoulder_dystocia' => $kala_two_shoulder_dystocia,
            'kala_two_problem_management' => $kala_two_problem_management,
            'kala_three_initiation_type_id' => $kala_three_initiation_type_id,
            'kala_three_initiation' => $kala_three_initiation,
            'kala_three_long' => $kala_three_long,
            'kala_three_oxytocin_type_id' => $kala_three_oxytocin_type_id,
            'kala_three_oxytocin' => $kala_three_oxytocin,
            'kala_three_oxytocin_reason' => $kala_three_oxytocin_reason,
            'kala_three_oxytocin_second_type_id' => $kala_three_oxytocin_second_type_id,
            'kala_three_oxytocin_second' => $kala_three_oxytocin_second,
            'kala_three_umbilical_cord_type_id' => $kala_three_umbilical_cord_type_id,
            'kala_three_umbilical_cord' => $kala_three_umbilical_cord,
            'kala_three_uterine_fundus_type_id' => $kala_three_uterine_fundus_type_id,
            'kala_three_uterine_fundus' => $kala_three_uterine_fundus,
            'kala_three_placenta_type_id' => $kala_three_placenta_type_id,
            'kala_three_placenta_a' => $kala_three_placenta_a,
            'kala_three_placenta_b' => $kala_three_placenta_b,
            'kala_three_placenta_not_type_id' => $kala_three_placenta_not_type_id,
            'kala_three_placenta_not' => $kala_three_placenta_not,
            'kala_three_laceration_type_id' => $kala_three_laceration_type_id,
            'kala_three_laceration' => $kala_three_laceration,
            'kala_three_uterine_atony_type_id' => $kala_three_uterine_atony_type_id,
            'kala_three_uterine_atony' => $kala_three_uterine_atony,
            'kala_three_amount_blood' => $kala_three_amount_blood,
            'kala_three_problem_management' => $kala_three_problem_management,
            'kala_three_result' => $kala_three_result,
            'kala_four_mother_ku' => $kala_four_mother_ku,
            'kala_four_mother_td' => $kala_four_mother_td,
            'kala_four_pulse' => $kala_four_pulse,
            'kala_four_breath' => $kala_four_breath,
            'kala_four_problem_management' => $kala_four_problem_management,
            'kala_four_result' => $kala_four_result,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->update($birth_record_kala_id, $dt_birth_record_kala);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_birth_record_kala,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update birth_record_kala', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function save_kala_one_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_kala_one_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $birth_record_id = $dt_put['birth_record_id'];

        $select = 'a.id';
        $where = array();
        $birth_record = $this->Birth_record->select($birth_record_id, $select, $where);
        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH RECORD KALA
        $dt_birth_record_kala = array(
            'birth_record_id' => $birth_record_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.birth_record_id' => $birth_record_id,
            'a.is_delete' => 0,
        );
        $birth_record_kala = $this->$model->select(0, $select, $where);
        if (count($birth_record_kala) == 0) {
            $birth_record_kala_id = $this->$model->create($dt_birth_record_kala);
        } else {
            $birth_record_kala_id = $birth_record_kala[0]->id;
            unset($dt_birth_record_kala['user_added']);
            unset($dt_birth_record_kala['date_added']);
            $st_birth_record_kala = $this->$model->update_v2($birth_record_kala_id, $dt_birth_record_kala);
            if ($st_birth_record_kala < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // KALA ONE
        $kala_one_latent_phase = $dt_put['kala_one_latent_phase'];
        $kala_one_intervention_type_id = $dt_put['kala_one_intervention_type_id'];
        $kala_one_alert_line_type_id = $dt_put['kala_one_alert_line_type_id'];
        // $kala_one_active_phase = $dt_put['kala_one_active_phase'];
        $kala_one_problem = $dt_put['kala_one_problem'];
        $kala_one_problem_management = $dt_put['kala_one_problem_management'];
        $kala_one_result = $dt_put['kala_one_result'];

        $dt_kala_one = array(
            'kala_one_latent_phase'         => $kala_one_latent_phase,
            'kala_one_intervention_type_id' => $kala_one_intervention_type_id,
            'kala_one_alert_line_type_id'   => $kala_one_alert_line_type_id,
            // 'kala_one_active_phase'         => $kala_one_active_phase,
            'kala_one_problem'              => $kala_one_problem,
            'kala_one_problem_management'   => $kala_one_problem_management,
            'kala_one_result'               => $kala_one_result,
        );

        $st_kala_one = $this->$model->update_v2($birth_record_kala_id, $dt_kala_one);
        if ($st_kala_one < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kala_one,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save kala one', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_kala_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_kala_two_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_kala_two_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $birth_record_id = $dt_put['birth_record_id'];

        $select = 'a.id';
        $where = array();
        $birth_record = $this->Birth_record->select($birth_record_id, $select, $where);
        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH RECORD KALA
        $dt_birth_record_kala = array(
            'birth_record_id' => $birth_record_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.birth_record_id' => $birth_record_id,
            'a.is_delete' => 0,
        );
        $birth_record_kala = $this->$model->select(0, $select, $where);
        if (count($birth_record_kala) == 0) {
            $birth_record_kala_id = $this->$model->create($dt_birth_record_kala);
        } else {
            $birth_record_kala_id = $birth_record_kala[0]->id;
            unset($dt_birth_record_kala['user_added']);
            unset($dt_birth_record_kala['date_added']);
            $st_birth_record_kala = $this->$model->update_v2($birth_record_kala_id, $dt_birth_record_kala);
            if ($st_birth_record_kala < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // KALA TWO
        $kala_two_episiotomy_type_id = $dt_put['kala_two_episiotomy_type_id'];
        $kala_two_episiotomy = $dt_put['kala_two_episiotomy'];
        $kala_two_companion_type_id = $dt_put['kala_two_companion_type_id'];
        $kala_two_companion_arry = ($dt_put['kala_two_companion_arry'] != []) ? implode(',',$dt_put['kala_two_companion_arry']) : '';
        $kala_two_fetal_distress_type_id = $dt_put['kala_two_fetal_distress_type_id'];
        $kala_two_fetal_distress = $dt_put['kala_two_fetal_distress'];
        // $kala_two_fetal_distress_a = $dt_put['kala_two_fetal_distress_a'];
        // $kala_two_fetal_distress_b = $dt_put['kala_two_fetal_distress_b'];
        // $kala_two_monitoring_result = $dt_put['kala_two_monitoring_result'];
        $kala_two_shoulder_dystocia_type_id = $dt_put['kala_two_shoulder_dystocia_type_id'];
        $kala_two_shoulder_dystocia = $dt_put['kala_two_shoulder_dystocia'];
        $kala_two_problem = $dt_put['kala_two_problem'];
        $kala_two_problem_management = $dt_put['kala_two_problem_management'];
        $kala_two_result = $dt_put['kala_two_result'];

        $dt_kala_two = array(
            "kala_two_episiotomy_type_id"        => $kala_two_episiotomy_type_id,
            "kala_two_episiotomy"                => $kala_two_episiotomy,
            "kala_two_companion_type_id"         => $kala_two_companion_type_id,
            "kala_two_companion_arry"            => $kala_two_companion_arry,
            "kala_two_fetal_distress_type_id"    => $kala_two_fetal_distress_type_id,
            "kala_two_fetal_distress"            => $kala_two_fetal_distress,
            // "kala_two_fetal_distress_a"          => $kala_two_fetal_distress_a,
            // "kala_two_fetal_distress_b"          => $kala_two_fetal_distress_b,
            // "kala_two_monitoring_result"         => $kala_two_monitoring_result,
            "kala_two_shoulder_dystocia_type_id" => $kala_two_shoulder_dystocia_type_id,
            "kala_two_shoulder_dystocia"         => $kala_two_shoulder_dystocia,
            "kala_two_problem"                   => $kala_two_problem,
            "kala_two_problem_management"        => $kala_two_problem_management,
            "kala_two_result"                    => $kala_two_result,
        );

        $st_kala_two = $this->$model->update_v2($birth_record_kala_id, $dt_kala_two);
        if ($st_kala_two < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kala_two,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save kala two', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_kala_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_kala_three_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_kala_three_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $birth_record_id = $dt_put['birth_record_id'];

        $select = 'a.id';
        $where = array();
        $birth_record = $this->Birth_record->select($birth_record_id, $select, $where);
        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH RECORD KALA
        $dt_birth_record_kala = array(
            'birth_record_id' => $birth_record_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.birth_record_id' => $birth_record_id,
            'a.is_delete' => 0,
        );
        $birth_record_kala = $this->$model->select(0, $select, $where);
        if (count($birth_record_kala) == 0) {
            $birth_record_kala_id = $this->$model->create($dt_birth_record_kala);
        } else {
            $birth_record_kala_id = $birth_record_kala[0]->id;
            unset($dt_birth_record_kala['user_added']);
            unset($dt_birth_record_kala['date_added']);
            $st_birth_record_kala = $this->$model->update_v2($birth_record_kala_id, $dt_birth_record_kala);
            if ($st_birth_record_kala < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // KALA THREE
        $kala_three_initiation_type_id = $dt_put['kala_three_initiation_type_id'];
        $kala_three_initiation = $dt_put['kala_three_initiation'];
        $kala_three_long = $dt_put['kala_three_long'];
        $kala_three_oxytocin_type_id = $dt_put['kala_three_oxytocin_type_id'];
        $kala_three_oxytocin = $dt_put['kala_three_oxytocin'];
        $kala_three_oxytocin_reason = $dt_put['kala_three_oxytocin_reason'];
        $kala_three_oxytocin_second_type_id = $dt_put['kala_three_oxytocin_second_type_id'];
        $kala_three_oxytocin_second = $dt_put['kala_three_oxytocin_second'];
        $kala_three_umbilical_cord_type_id = $dt_put['kala_three_umbilical_cord_type_id'];
        $kala_three_umbilical_cord = $dt_put['kala_three_umbilical_cord'];
        $kala_three_uterine_fundus_type_id = $dt_put['kala_three_uterine_fundus_type_id'];
        $kala_three_uterine_fundus = $dt_put['kala_three_uterine_fundus'];
        $kala_three_placenta_type_id = $dt_put['kala_three_placenta_type_id'];
        $kala_three_placenta = $dt_put['kala_three_placenta'];
        // $kala_three_placenta_a = $dt_put['kala_three_placenta_a'];
        // $kala_three_placenta_b = $dt_put['kala_three_placenta_b'];
        $kala_three_placenta_not_type_id = $dt_put['kala_three_placenta_not_type_id'];
        $kala_three_placenta_not = $dt_put['kala_three_placenta_not'];
        $kala_three_laceration_type_id = $dt_put['kala_three_laceration_type_id'];
        $kala_three_laceration = $dt_put['kala_three_laceration'];
        $kala_three_laceration_degree = $dt_put['kala_three_laceration_degree'];
        $kala_three_laceration_suture = $dt_put['kala_three_laceration_suture'];
        $kala_three_laceration_not_suture_reason = $dt_put['kala_three_laceration_not_suture_reason'];
        $kala_three_laceration_anesthesia = $dt_put['kala_three_laceration_anesthesia'];
        $kala_three_uterine_atony_type_id = $dt_put['kala_three_uterine_atony_type_id'];
        $kala_three_uterine_atony = $dt_put['kala_three_uterine_atony'];
        $kala_three_amount_blood = $dt_put['kala_three_amount_blood'];
        $kala_three_problem = $dt_put['kala_three_problem'];
        $kala_three_problem_management = $dt_put['kala_three_problem_management'];
        $kala_three_result = $dt_put['kala_three_result'];

        $dt_kala_three = array(
            'kala_three_initiation_type_id'      => $kala_three_initiation_type_id,
            'kala_three_initiation'              => $kala_three_initiation,
            'kala_three_long'                    => $kala_three_long,
            'kala_three_oxytocin_type_id'        => $kala_three_oxytocin_type_id,
            'kala_three_oxytocin'                => $kala_three_oxytocin,
            'kala_three_oxytocin_reason'         => $kala_three_oxytocin_reason,
            'kala_three_oxytocin_second_type_id' => $kala_three_oxytocin_second_type_id,
            'kala_three_oxytocin_second'         => $kala_three_oxytocin_second,
            'kala_three_umbilical_cord_type_id'  => $kala_three_umbilical_cord_type_id,
            'kala_three_umbilical_cord'          => $kala_three_umbilical_cord,
            'kala_three_uterine_fundus_type_id'  => $kala_three_uterine_fundus_type_id,
            'kala_three_uterine_fundus'          => $kala_three_uterine_fundus,
            'kala_three_placenta_type_id'        => $kala_three_placenta_type_id,
            'kala_three_placenta'                => $kala_three_placenta,
            // 'kala_three_placenta_a'              => $kala_three_placenta_a,
            // 'kala_three_placenta_b'              => $kala_three_placenta_b,
            'kala_three_placenta_not_type_id'    => $kala_three_placenta_not_type_id,
            'kala_three_placenta_not'            => $kala_three_placenta_not,
            'kala_three_laceration_type_id'      => $kala_three_laceration_type_id,
            'kala_three_laceration'              => $kala_three_laceration,
            'kala_three_laceration_degree'            => $kala_three_laceration_degree,
            'kala_three_laceration_suture'            => $kala_three_laceration_suture,
            'kala_three_laceration_not_suture_reason' => $kala_three_laceration_not_suture_reason,
            'kala_three_laceration_anesthesia'        => $kala_three_laceration_anesthesia,
            'kala_three_uterine_atony_type_id'   => $kala_three_uterine_atony_type_id,
            'kala_three_uterine_atony'           => $kala_three_uterine_atony,
            'kala_three_amount_blood'            => $kala_three_amount_blood,
            'kala_three_problem'                 => $kala_three_problem,
            'kala_three_problem_management'      => $kala_three_problem_management,
            'kala_three_result'                  => $kala_three_result,
        );

        $st_kala_three = $this->$model->update_v2($birth_record_kala_id, $dt_kala_three);
        if ($st_kala_three < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kala_three,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save kala three', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_kala_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_kala_four_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_kala_four_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $birth_record_id = $dt_put['birth_record_id'];

        $select = 'a.id';
        $where = array();
        $birth_record = $this->Birth_record->select($birth_record_id, $select, $where);
        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH RECORD KALA
        $dt_birth_record_kala = array(
            'birth_record_id' => $birth_record_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.birth_record_id' => $birth_record_id,
            'a.is_delete' => 0,
        );
        $birth_record_kala = $this->$model->select(0, $select, $where);
        if (count($birth_record_kala) == 0) {
            $birth_record_kala_id = $this->$model->create($dt_birth_record_kala);
        } else {
            $birth_record_kala_id = $birth_record_kala[0]->id;
            unset($dt_birth_record_kala['user_added']);
            unset($dt_birth_record_kala['date_added']);
            $st_birth_record_kala = $this->$model->update_v2($birth_record_kala_id, $dt_birth_record_kala);
            if ($st_birth_record_kala < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // KALA FOUR
        $kala_four_mother_ku = $dt_put['kala_four_mother_ku'];
        // $kala_four_mother_td = $dt_put['kala_four_mother_td'];
        $kala_four_pulse = $dt_put['kala_four_pulse'];
        $kala_four_systolic = $dt_put['kala_four_systolic'];
        $kala_four_diastolic = $dt_put['kala_four_diastolic'];
        $kala_four_breath = $dt_put['kala_four_breath'];
        $kala_four_problem = $dt_put['kala_four_problem'];
        $kala_four_problem_management = $dt_put['kala_four_problem_management'];
        $kala_four_result = $dt_put['kala_four_result'];

        $dt_kala_four = array(
            'kala_four_mother_ku'          => $kala_four_mother_ku,
            // 'kala_four_mother_td'          => $kala_four_mother_td,
            'kala_four_pulse'              => $kala_four_pulse,
            'kala_four_systolic'           => $kala_four_systolic,
            'kala_four_diastolic'          => $kala_four_diastolic,
            'kala_four_breath'             => $kala_four_breath,
            'kala_four_problem'            => $kala_four_problem,
            'kala_four_problem_management' => $kala_four_problem_management,
            'kala_four_result'             => $kala_four_result,
        );

        $st_kala_four = $this->$model->update_v2($birth_record_kala_id, $dt_kala_four);
        if ($st_kala_four < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_kala_four,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save kala four', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_kala_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_monitoring_kala_four_put() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_put    = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->save_monitoring_kala_four_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $birth_record_id = $dt_put['birth_record_id'];

        $select = 'a.id';
        $where = array();
        $birth_record = $this->Birth_record->select($birth_record_id, $select, $where);
        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH RECORD KALA
        $dt_birth_record_kala = array(
            'birth_record_id' => $birth_record_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.birth_record_id' => $birth_record_id,
            'a.is_delete' => 0,
        );
        $birth_record_kala = $this->$model->select(0, $select, $where);
        if (count($birth_record_kala) == 0) {
            $birth_record_kala_id = $this->$model->create($dt_birth_record_kala);
        } else {
            $birth_record_kala_id = $birth_record_kala[0]->id;
            unset($dt_birth_record_kala['user_added']);
            unset($dt_birth_record_kala['date_added']);
            $st_birth_record_kala = $this->$model->update_v2($birth_record_kala_id, $dt_birth_record_kala);
            if ($st_birth_record_kala < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // MONITORING KALA FOUR
        $monitoring = $dt_put['monitoring'];

        // $dt_monitoring_kala_four_delete = array(
        //     'date_updated' => date('Y-m-d H:i:s'),
        //     'user_updated' => $user_id,
        //     'is_delete' => 1,
        // );
        // $where_delete = array(
        //     'birth_record_kala_id' => $birth_record_kala_id,
        //     'is_delete' => 0,
        // );
        // $this->Monitoring_kala_four->update_v2(0, $dt_monitoring_kala_four_delete, $where_delete);

        for ($i=0; $i < count($monitoring); $i++) { 
            $time_id = $monitoring[$i]['time_id'] ?? null;
            $time = $monitoring[$i]['time'] ?? null;
            $systolic = $monitoring[$i]['systolic'] ?? null;
            $diastolic = $monitoring[$i]['diastolic'] ?? null;
            $pulse = $monitoring[$i]['pulse'] ?? null;
            $temperature = $monitoring[$i]['temperature'] ?? null;
            $fundal_height = $monitoring[$i]['fundal_height'] ?? null;
            $uterus_contraction = $monitoring[$i]['uterus_contraction'] ?? null;
            $bladder_volume = $monitoring[$i]['bladder_volume'] ?? null;
            $bleeding_volume = $monitoring[$i]['bleeding_volume'] ?? null;

            if ($time_id == null) {
                $this->REST_Return(400, 'time_id is required in monitoring field');
                return;
            }

            if ($time == null) {
                $this->REST_Return(400, 'time is required in monitoring field');
                return;
            }

            $dt_monitoring_kala_four = array(
                'birth_record_kala_id'  => $birth_record_kala_id,
                'time_id'               => $time_id,
                'time'                  => $time,
                'systolic'              => $systolic,
                'diastolic'             => $diastolic,
                'pulse'                 => $pulse,
                'temperature'           => $temperature,
                'fundal_height'         => $fundal_height,
                'uterus_contraction'    => $uterus_contraction,
                'bladder_volume'        => $bladder_volume,
                'bleeding_volume'       => $bleeding_volume,
                'date_updated'          => date('Y-m-d H:i:s'),
                'user_updated'          => $user_id,
                'is_delete'             => 0,
            );
    
            $select = 'a.id';
            $where = array(
                'a.birth_record_kala_id' => $birth_record_kala_id,
                'a.time_id' => $time_id,
                // 'a.time' => $time,
            );
            $monitoring_kala_four = $this->Monitoring_kala_four->select(0, $select, $where);
            if (count($monitoring_kala_four) == 0) {
                $dt_monitoring_kala_four['date_added'] = date('Y-m-d H:i:s');
                $dt_monitoring_kala_four['user_added'] = $user_id;
                $this->Monitoring_kala_four->create($dt_monitoring_kala_four);
            } else {
                $monitoring_kala_four_id = $monitoring_kala_four[0]->id;
                $st_monitoring_kala_four = $this->Monitoring_kala_four->update_v2($monitoring_kala_four_id, $dt_monitoring_kala_four);
                if ($st_monitoring_kala_four < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            }
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_monitoring_kala_four,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save kala four', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_kala_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
}