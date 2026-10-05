<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Birth_record extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Partograph_model", "Partograph");

        $this->create_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->save_note_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date_birth', 'label' => 'date_birth', 'rules' => 'trim'),
            array('field' => 'birthing_place_type_id', 'label' => 'birthing_place_type_id', 'rules' => 'trim'),
            array('field' => 'birthing_place_other', 'label' => 'birthing_place_other', 'rules' => 'trim'),
            array('field' => 'birthing_place_address', 'label' => 'birthing_place_address', 'rules' => 'trim'),
            array('field' => 'note', 'label' => 'note', 'rules' => 'trim'),
            array('field' => 'referral_type_id', 'label' => 'referral_type_id', 'rules' => 'trim'),
            array('field' => 'referral_kala', 'label' => 'referral_kala', 'rules' => 'trim'),
            array('field' => 'referral_reason', 'label' => 'referral_reason', 'rules' => 'trim'),
            array('field' => 'referral_place', 'label' => 'referral_place', 'rules' => 'trim'),
            array('field' => 'referral_companion_type_id', 'label' => 'referral_companion_type_id', 'rules' => 'trim'),
            array('field' => 'referral_companion_arry', 'label' => 'referral_companion_arry', 'rules' => 'trim'),
            array('field' => 'problem_type_id', 'label' => 'problem_type_id', 'rules' => 'trim'),
            array('field' => 'problem_arry', 'label' => 'problem_arry', 'rules' => 'trim'),
        );

        $this->save_newborn_baby_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'newborn_baby_weight', 'label' => 'newborn_baby_weight', 'rules' => 'trim'),
            array('field' => 'newborn_baby_height', 'label' => 'newborn_baby_height', 'rules' => 'trim'),
            array('field' => 'newborn_baby_sex_type_id', 'label' => 'newborn_baby_sex_type_id', 'rules' => 'trim'),
            array('field' => 'newborn_baby_assessment_type_id', 'label' => 'newborn_baby_assessment_type_id', 'rules' => 'trim'),
            array('field' => 'newborn_baby_normal_arry', 'label' => 'newborn_baby_normal_arry', 'rules' => 'trim'),
            array('field' => 'newborn_baby_asphyxia_arry', 'label' => 'newborn_baby_asphyxia_arry', 'rules' => 'trim'),
            array('field' => 'newborn_baby_disabled', 'label' => 'newborn_baby_disabled', 'rules' => 'trim'),
            array('field' => 'newborn_baby_hypothermia_type_id', 'label' => 'newborn_baby_hypothermia_type_id', 'rules' => 'trim'),
            array('field' => 'newborn_baby_hypothermia_a', 'label' => 'newborn_baby_hypothermia_a', 'rules' => 'trim'),
            array('field' => 'newborn_baby_hypothermia_b', 'label' => 'newborn_baby_hypothermia_b', 'rules' => 'trim'),
            array('field' => 'newborn_baby_hypothermia_c', 'label' => 'newborn_baby_hypothermia_c', 'rules' => 'trim'),
            array('field' => 'newborn_baby_breast_milk_type_id', 'label' => 'newborn_baby_breast_milk_type_id', 'rules' => 'trim'),
            array('field' => 'newborn_baby_breast_milk_hours', 'label' => 'newborn_baby_breast_milk_hours', 'rules' => 'trim'),
            array('field' => 'newborn_baby_breast_milk_not', 'label' => 'newborn_baby_breast_milk_not', 'rules' => 'trim'),
            array('field' => 'newborn_baby_problem_management', 'label' => 'newborn_baby_problem_management', 'rules' => 'trim'),
            array('field' => 'newborn_baby_result', 'label' => 'newborn_baby_result', 'rules' => 'trim'),
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
    
        $birth_record_id = $id;
    
        if ($birth_record_id === 0 || $birth_record_id === "0") {
            $partograph_id = $q_strings['partograph_id'] ?? null;
            if ($partograph_id == null) {
                $this->REST_Return(400, 'partograph_id is required if birth_record_id is not provided');
                return;
            }
            $this->$model->whereFields['a.partograph_id'] = $partograph_id;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($birth_record_id);
        
        if (count($output) > 0) {
            $output[0]->referral_companion_arry = ($output[0]->referral_companion_arry != '') ? explode(',', $output[0]->referral_companion_arry): [];
            $output[0]->problem_arry = ($output[0]->problem_arry != '') ? explode(',', $output[0]->problem_arry): [];
            $output[0]->newborn_baby_normal_arry = ($output[0]->newborn_baby_normal_arry != '') ? explode(',', $output[0]->newborn_baby_normal_arry): [];
            $output[0]->newborn_baby_abnormal_arry = ($output[0]->newborn_baby_abnormal_arry != '') ? explode(',', $output[0]->newborn_baby_abnormal_arry): [];
            $output[0]->newborn_baby_asphyxia_arry = ($output[0]->newborn_baby_asphyxia_arry != '') ? explode(',', $output[0]->newborn_baby_asphyxia_arry): [];

            $output[0]->newborn_baby_weight = (float) $output[0]->newborn_baby_weight;
            $output[0]->newborn_baby_height = (float) $output[0]->newborn_baby_height;
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

        $partograph_id = $dt_post['partograph_id'];
        
        $select = 'a.id';
        $partograph = $this->Partograph->select($partograph_id, $select);
        
        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }

        $date_birth = $dt_post['date_birth'];
        $birthing_place_type_id = $dt_post['birthing_place_type_id'];
        $birthing_place_other = $dt_post['birthing_place_other'];
        $birthing_place_address = $dt_post['birthing_place_address'];
        $note = $dt_post['note'];
        $referral_reason = $dt_post['referral_reason'];
        $referral_place = $dt_post['referral_place'];
        $referral_companion_type_id = $dt_post['referral_companion_type_id'];
        $problem_type_id = $dt_post['problem_type_id'];
        $newborn_baby_weight = $dt_post['newborn_baby_weight'];
        $newborn_baby_height = $dt_post['newborn_baby_height'];
        $newborn_baby_sex_type_id = $dt_post['newborn_baby_sex_type_id'];
        $newborn_baby_assessment_type_id = $dt_post['newborn_baby_assessment_type_id'];
        $newborn_baby_normal_arry = $dt_post['newborn_baby_normal_arry'];
        $newborn_baby_asphyxia_arry = $dt_post['newborn_baby_asphyxia_arry'];
        $newborn_baby_disabled = $dt_post['newborn_baby_disabled'];
        $newborn_baby_hypothermia_type_id = $dt_post['newborn_baby_hypothermia_type_id'];
        $newborn_baby_hypothermia_a = $dt_post['newborn_baby_hypothermia_a'];
        $newborn_baby_hypothermia_b = $dt_post['newborn_baby_hypothermia_b'];
        $newborn_baby_hypothermia_c = $dt_post['newborn_baby_hypothermia_c'];
        $newborn_baby_breast_milk_type_id = $dt_post['newborn_baby_breast_milk_type_id'];
        $newborn_baby_breast_milk_hours = $dt_post['newborn_baby_breast_milk_hours'];
        $newborn_baby_breast_milk_not = $dt_post['newborn_baby_breast_milk_not'];
        $newborn_baby_problem_management = $dt_post['newborn_baby_problem_management'];
        $newborn_baby_result = $dt_post['newborn_baby_result'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_birth_record = array(
            'partograph_id' => $partograph_id,
            'date_birth' => $date_birth,
            'birthing_place_type_id' => $birthing_place_type_id,
            'birthing_place_other' => $birthing_place_other,
            'birthing_place_address' => $birthing_place_address,
            'note' => $note,
            'referral_reason' => $referral_reason,
            'referral_place' => $referral_place,
            'referral_companion_type_id' => $referral_companion_type_id,
            'problem_type_id' => $problem_type_id,
            'newborn_baby_weight' => $newborn_baby_weight,
            'newborn_baby_height' => $newborn_baby_height,
            'newborn_baby_sex_type_id' => $newborn_baby_sex_type_id,
            'newborn_baby_assessment_type_id' => $newborn_baby_assessment_type_id,
            'newborn_baby_normal_arry' => $newborn_baby_normal_arry,
            'newborn_baby_asphyxia_arry' => $newborn_baby_asphyxia_arry,
            'newborn_baby_disabled' => $newborn_baby_disabled,
            'newborn_baby_hypothermia_type_id' => $newborn_baby_hypothermia_type_id,
            'newborn_baby_hypothermia_a' => $newborn_baby_hypothermia_a,
            'newborn_baby_hypothermia_b' => $newborn_baby_hypothermia_b,
            'newborn_baby_hypothermia_c' => $newborn_baby_hypothermia_c,
            'newborn_baby_breast_milk_type_id' => $newborn_baby_breast_milk_type_id,
            'newborn_baby_breast_milk_hours' => $newborn_baby_breast_milk_hours,
            'newborn_baby_breast_milk_not' => $newborn_baby_breast_milk_not,
            'newborn_baby_problem_management' => $newborn_baby_problem_management,
            'newborn_baby_result' => $newborn_baby_result,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_birth_record);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_birth_record,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create birth_record', $response_data);
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
    
        $birth_record_id = $id;
    
        if ($birth_record_id === 0 || $birth_record_id === "0") {
            $this->REST_Return(400, 'birth_record_id is required');
            return;
        }

        $select = 'a.id';
        $birth_record = $this->$model->select($birth_record_id, $select);

        if (count($birth_record) == 0) {
            $this->REST_Return(404, 'there is no birth_record with id ' . $birth_record_id);
            return;
        }

        $date_birth = $dt_put['date_birth'];
        $birthing_place_type_id = $dt_put['birthing_place_type_id'];
        $birthing_place_other = $dt_put['birthing_place_other'];
        $birthing_place_address = $dt_put['birthing_place_address'];
        $note = $dt_put['note'];
        $referral_reason = $dt_put['referral_reason'];
        $referral_place = $dt_put['referral_place'];
        $referral_companion_type_id = $dt_put['referral_companion_type_id'];
        $problem_type_id = $dt_put['problem_type_id'];
        $newborn_baby_weight = $dt_put['newborn_baby_weight'];
        $newborn_baby_height = $dt_put['newborn_baby_height'];
        $newborn_baby_sex_type_id = $dt_put['newborn_baby_sex_type_id'];
        $newborn_baby_assessment_type_id = $dt_put['newborn_baby_assessment_type_id'];
        $newborn_baby_normal_arry = $dt_put['newborn_baby_normal_arry'];
        $newborn_baby_asphyxia_arry = $dt_put['newborn_baby_asphyxia_arry'];
        $newborn_baby_disabled = $dt_put['newborn_baby_disabled'];
        $newborn_baby_hypothermia_type_id = $dt_put['newborn_baby_hypothermia_type_id'];
        $newborn_baby_hypothermia_a = $dt_put['newborn_baby_hypothermia_a'];
        $newborn_baby_hypothermia_b = $dt_put['newborn_baby_hypothermia_b'];
        $newborn_baby_hypothermia_c = $dt_put['newborn_baby_hypothermia_c'];
        $newborn_baby_breast_milk_type_id = $dt_put['newborn_baby_breast_milk_type_id'];
        $newborn_baby_breast_milk_hours = $dt_put['newborn_baby_breast_milk_hours'];
        $newborn_baby_breast_milk_not = $dt_put['newborn_baby_breast_milk_not'];
        $newborn_baby_problem_management = $dt_put['newborn_baby_problem_management'];
        $newborn_baby_result = $dt_put['newborn_baby_result'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_birth_record = array(
            'date_birth' => $date_birth,
            'birthing_place_type_id' => $birthing_place_type_id,
            'birthing_place_other' => $birthing_place_other,
            'birthing_place_address' => $birthing_place_address,
            'note' => $note,
            'referral_reason' => $referral_reason,
            'referral_place' => $referral_place,
            'referral_companion_type_id' => $referral_companion_type_id,
            'problem_type_id' => $problem_type_id,
            'newborn_baby_weight' => $newborn_baby_weight,
            'newborn_baby_height' => $newborn_baby_height,
            'newborn_baby_sex_type_id' => $newborn_baby_sex_type_id,
            'newborn_baby_assessment_type_id' => $newborn_baby_assessment_type_id,
            'newborn_baby_normal_arry' => $newborn_baby_normal_arry,
            'newborn_baby_asphyxia_arry' => $newborn_baby_asphyxia_arry,
            'newborn_baby_disabled' => $newborn_baby_disabled,
            'newborn_baby_hypothermia_type_id' => $newborn_baby_hypothermia_type_id,
            'newborn_baby_hypothermia_a' => $newborn_baby_hypothermia_a,
            'newborn_baby_hypothermia_b' => $newborn_baby_hypothermia_b,
            'newborn_baby_hypothermia_c' => $newborn_baby_hypothermia_c,
            'newborn_baby_breast_milk_type_id' => $newborn_baby_breast_milk_type_id,
            'newborn_baby_breast_milk_hours' => $newborn_baby_breast_milk_hours,
            'newborn_baby_breast_milk_not' => $newborn_baby_breast_milk_not,
            'newborn_baby_problem_management' => $newborn_baby_problem_management,
            'newborn_baby_result' => $newborn_baby_result,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->update($birth_record_id, $dt_birth_record);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_birth_record,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update birth_record', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function save_note_put() {
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
        $this->form_validation->set_rules($this->save_note_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $partograph_id = $dt_put['partograph_id'];

        $select = 'a.id';
        $where = array();
        $partograph = $this->Partograph->select($partograph_id, $select, $where);
        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH_RECORD
        $dt_birth_record = array(
            'partograph_id' => $partograph_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.partograph_id' => $partograph_id,
            'a.is_delete' => 0,
        );
        $birth_record = $this->$model->select(0, $select, $where);
        if (count($birth_record) == 0) {
            $birth_record_id = $this->$model->create($dt_birth_record);
        } else {
            $birth_record_id = $birth_record[0]->id;
            unset($dt_birth_record['user_added']);
            unset($dt_birth_record['date_added']);
            $st_birth_record = $this->Birth_record->update_v2($birth_record_id, $dt_birth_record);
            if ($st_birth_record < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // NOTES
        $date_birth = $dt_put['date_birth'];
        $birthing_place_type_id = $dt_put['birthing_place_type_id'];
        $birthing_place_other = $dt_put['birthing_place_other'];
        $birthing_place_address = $dt_put['birthing_place_address'];
        $note = $dt_put['note'];
        $referral_type_id = $dt_put['referral_type_id'];
        $referral_kala = $dt_put['referral_kala'];
        $referral_reason = $dt_put['referral_reason'];
        $referral_place = $dt_put['referral_place'];
        $referral_companion_type_id = $dt_put['referral_companion_type_id'];
        $referral_companion_arry = ($dt_put['referral_companion_arry'] != []) ? implode(',', $dt_put['referral_companion_arry']) : '';
        $problem_type_id = $dt_put['problem_type_id'];
        $problem_arry = ($dt_put['problem_arry'] != []) ? implode(',', $dt_put['problem_arry']) : '';

        $dt_note = array(
            'date_birth'                 => $date_birth,
            'birthing_place_type_id'     => $birthing_place_type_id,
            'birthing_place_other'       => $birthing_place_other,
            'birthing_place_address'     => $birthing_place_address,
            'note'                       => $note,
            'referral_type_id'           => $referral_type_id,
            'referral_kala'              => $referral_kala,
            'referral_reason'            => $referral_reason,
            'referral_place'             => $referral_place,
            'referral_companion_type_id' => $referral_companion_type_id,
            'referral_companion_arry'    => $referral_companion_arry,
            'problem_type_id'            => $problem_type_id,
            'problem_arry'               => $problem_arry,
        );

        $st_note = $this->$model->update_v2($birth_record_id, $dt_note);
        if ($st_note < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_note,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save note', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function save_newborn_baby_put() {
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
        $this->form_validation->set_rules($this->save_newborn_baby_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    
        $partograph_id = $dt_put['partograph_id'];

        $select = 'a.id';
        $where = array();
        $partograph = $this->Partograph->select($partograph_id, $select, $where);
        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        // BIRTH_RECORD
        $dt_birth_record = array(
            'partograph_id' => $partograph_id,
            'user_added' => $user_id,
            'user_updated' => $user_id,
            'date_added' => date("Y-m-d H:i:s",time()),
            'date_updated' => date("Y-m-d H:i:s",time()),
        );
        
        $select = 'a.id';
        $where = array(
            'a.partograph_id' => $partograph_id,
            'a.is_delete' => 0,
        );
        $birth_record = $this->$model->select(0, $select, $where);
        if (count($birth_record) == 0) {
            $birth_record_id = $this->$model->create($dt_birth_record);
        } else {
            $birth_record_id = $birth_record[0]->id;
            unset($dt_birth_record['user_added']);
            unset($dt_birth_record['date_added']);
            $st_birth_record = $this->Birth_record->update_v2($birth_record_id, $dt_birth_record);
            if ($st_birth_record < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // NEWBORN BABY
        $newborn_baby_weight = $dt_put['newborn_baby_weight'];
        $newborn_baby_height = $dt_put['newborn_baby_height'];
        $newborn_baby_sex_type_id = $dt_put['newborn_baby_sex_type_id'];
        $newborn_baby_assessment_type_id = $dt_put['newborn_baby_assessment_type_id'];
        $newborn_baby_normal_arry = ($dt_put['newborn_baby_normal_arry'] != []) ? implode(',', $dt_put['newborn_baby_normal_arry']) : '';
        $newborn_baby_abnormal_arry = ($dt_put['newborn_baby_abnormal_arry'] != []) ? implode(',', $dt_put['newborn_baby_abnormal_arry']) : '';
        $newborn_baby_asphyxia_arry = ($dt_put['newborn_baby_asphyxia_arry'] != []) ? implode(',', $dt_put['newborn_baby_asphyxia_arry']) : '';
        $newborn_baby_asphyxia_others = $dt_put['newborn_baby_asphyxia_others'];
        $newborn_baby_disabled = $dt_put['newborn_baby_disabled'];
        $newborn_baby_hypothermia = $dt_put['newborn_baby_hypothermia'];
        // $newborn_baby_hypothermia_type_id = $dt_put['newborn_baby_hypothermia_type_id'];
        // $newborn_baby_hypothermia_a = $dt_put['newborn_baby_hypothermia_a'];
        // $newborn_baby_hypothermia_b = $dt_put['newborn_baby_hypothermia_b'];
        // $newborn_baby_hypothermia_c = $dt_put['newborn_baby_hypothermia_c'];
        $newborn_baby_breast_milk_type_id = $dt_put['newborn_baby_breast_milk_type_id'];
        $newborn_baby_breast_milk_hours = $dt_put['newborn_baby_breast_milk_hours'];
        $newborn_baby_breast_milk_not = $dt_put['newborn_baby_breast_milk_not'];
        $newborn_baby_problem = $dt_put['newborn_baby_problem'];
        $newborn_baby_problem_management = $dt_put['newborn_baby_problem_management'];
        $newborn_baby_result = $dt_put['newborn_baby_result'];

        $dt_note = array(
            'newborn_baby_weight'              => $newborn_baby_weight,
            'newborn_baby_height'              => $newborn_baby_height,
            'newborn_baby_sex_type_id'         => $newborn_baby_sex_type_id,
            'newborn_baby_assessment_type_id'  => $newborn_baby_assessment_type_id,
            'newborn_baby_normal_arry'         => $newborn_baby_normal_arry,
            'newborn_baby_abnormal_arry'       => $newborn_baby_abnormal_arry,
            'newborn_baby_asphyxia_arry'       => $newborn_baby_asphyxia_arry,
            'newborn_baby_asphyxia_others'     => $newborn_baby_asphyxia_others,
            'newborn_baby_disabled'            => $newborn_baby_disabled,
            'newborn_baby_hypothermia'         => $newborn_baby_hypothermia,
            // 'newborn_baby_hypothermia_type_id' => $newborn_baby_hypothermia_type_id,
            // 'newborn_baby_hypothermia_a'       => $newborn_baby_hypothermia_a,
            // 'newborn_baby_hypothermia_b'       => $newborn_baby_hypothermia_b,
            // 'newborn_baby_hypothermia_c'       => $newborn_baby_hypothermia_c,
            'newborn_baby_breast_milk_type_id' => $newborn_baby_breast_milk_type_id,
            'newborn_baby_breast_milk_hours'   => $newborn_baby_breast_milk_hours,
            'newborn_baby_breast_milk_not'     => $newborn_baby_breast_milk_not,
            'newborn_baby_problem'             => $newborn_baby_problem,
            'newborn_baby_problem_management'  => $newborn_baby_problem_management,
            'newborn_baby_result'              => $newborn_baby_result,
        );

        $st_note = $this->$model->update_v2($birth_record_id, $dt_note);
        if ($st_note < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_note,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to save newborn baby', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $birth_record_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}