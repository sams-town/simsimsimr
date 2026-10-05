<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Independent_assessment_sc extends My_Controller {
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
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim'),
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
    
        $independent_assessment_sc_id = $id;
    
        if ($independent_assessment_sc_id === 0 || $independent_assessment_sc_id === "0") {
            $this->REST_Return(400, 'independent_assessment_sc_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($independent_assessment_sc_id);
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

        $anc_minimal = $dt_post['anc_minimal'];
        $pink_kia_book = $dt_post['pink_kia_book'];
        $good_ku = $dt_post['good_ku'];
        $gcs_normal = $dt_post['gcs_normal'];
        $tension_changes = $dt_post['tension_changes'];
        $blood_test_before = $dt_post['blood_test_before'];
        $blood_test_after = $dt_post['blood_test_after'];
        $test_pt_aptt_ct_bt = $dt_post['test_pt_aptt_ct_bt'];
        $blood_transfusion = $dt_post['blood_transfusion'];
        $blood_type = $dt_post['blood_type'];
        $urinalysis_check = $dt_post['urinalysis_check'];
        $have_usg_data = $dt_post['have_usg_data'];
        $have_hiv_data = $dt_post['have_hiv_data'];
        $have_hepatitis_data = $dt_post['have_hepatitis_data'];
        $using_partograph = $dt_post['using_partograph'];
        $nulliparous_spontaneous = $dt_post['nulliparous_spontaneous'];
        $nulliparous_induction = $dt_post['nulliparous_induction'];
        $multipara_spontaneous = $dt_post['multipara_spontaneous'];
        $multipara_induction = $dt_post['multipara_induction'];
        $multipara_uterine_injury = $dt_post['multipara_uterine_injury'];
        $nulliparous_breech = $dt_post['nulliparous_breech'];
        $multipara_breech = $dt_post['multipara_breech'];
        $all_pregnancies_multiple_fetuses = $dt_post['all_pregnancies_multiple_fetuses'];
        $all_pregnancies_oblique_fetal = $dt_post['all_pregnancies_oblique_fetal'];
        $all_pregnancies_single_fetus = $dt_post['all_pregnancies_single_fetus'];
        $peb = $dt_post['peb'];
        $premature_rupture = $dt_post['premature_rupture'];
        $former_sc = $dt_post['former_sc'];
        $fetal_malposition = $dt_post['fetal_malposition'];
        $induction_failure = $dt_post['induction_failure'];
        $placental_abnormality = $dt_post['placental_abnormality'];
        $labor_not_progressing = $dt_post['labor_not_progressing'];
        $cephalopelvic_disproportion = $dt_post['cephalopelvic_disproportion'];
        $other_text = $dt_post['other_text'];
        $other = $dt_post['other'];
        $good_ku_after = $dt_post['good_ku_after'];
        $patient_died = $dt_post['patient_died'];
        $patient_died_referal = $dt_post['patient_died_referal'];
        $complications = $dt_post['complications'];
        $expansion_of_action = $dt_post['expansion_of_action'];
        $requires_expansion = $dt_post['requires_expansion'];
        $requires_further_care = $dt_post['requires_further_care'];
        $neonate_patient_died = $dt_post['neonate_patient_died'];
        $neonate_patient_died_referal = $dt_post['neonate_patient_died_referal'];
        $neonate_complications = $dt_post['neonate_complications'];
        $neonate_requires_expansion = $dt_post['neonate_requires_expansion'];
        $neonate_requires_further_care = $dt_post['neonate_requires_further_care'];
        $not_exceeding_rate = $dt_post['not_exceeding_rate'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_independent_assessment_sc = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'anc_minimal'                   => $anc_minimal,
            'pink_kia_book'                 => $pink_kia_book,
            'good_ku'                       => $good_ku,
            'gcs_normal'                    => $gcs_normal,
            'tension_changes'               => $tension_changes,
            'blood_test_before'             => $blood_test_before,
            'blood_test_after'              => $blood_test_after,
            'test_pt_aptt_ct_bt'            => $test_pt_aptt_ct_bt,
            'blood_transfusion'             => $blood_transfusion,
            'blood_type'                    => $blood_type,
            'urinalysis_check'              => $urinalysis_check,
            'have_usg_data'                 => $have_usg_data,
            'have_hiv_data'                 => $have_hiv_data,
            'have_hepatitis_data'           => $have_hepatitis_data,
            'using_partograph'              => $using_partograph,
            'nulliparous_spontaneous'       => $nulliparous_spontaneous,
            'nulliparous_induction'         => $nulliparous_induction,
            'multipara_spontaneous'         => $multipara_spontaneous,
            'multipara_induction'           => $multipara_induction,
            'multipara_uterine_injury'      => $multipara_uterine_injury,
            'nulliparous_breech'            => $nulliparous_breech,
            'multipara_breech'              => $multipara_breech,
            'all_pregnancies_multiple_fetuses' => $all_pregnancies_multiple_fetuses,
            'all_pregnancies_oblique_fetal' => $all_pregnancies_oblique_fetal,
            'all_pregnancies_single_fetus'  => $all_pregnancies_single_fetus,
            'peb'                           => $peb,
            'premature_rupture'             => $premature_rupture,
            'former_sc'                     => $former_sc,
            'fetal_malposition'             => $fetal_malposition,
            'induction_failure'             => $induction_failure,
            'placental_abnormality'         => $placental_abnormality,
            'labor_not_progressing'         => $labor_not_progressing,
            'cephalopelvic_disproportion'   => $cephalopelvic_disproportion,
            'other_text'                    => $other_text,
            'other'                         => $other,
            'good_ku_after'                 => $good_ku_after,
            'patient_died'                  => $patient_died,
            'patient_died_referal'          => $patient_died_referal,
            'complications'                 => $complications,
            'expansion_of_action'           => $expansion_of_action,
            'requires_expansion'            => $requires_expansion,
            'requires_further_care'         => $requires_further_care,
            'neonate_patient_died'          => $neonate_patient_died,
            'neonate_patient_died_referal'  => $neonate_patient_died_referal,
            'neonate_complications'         => $neonate_complications,
            'neonate_requires_expansion'    => $neonate_requires_expansion,
            'neonate_requires_further_care' => $neonate_requires_further_care,
            'not_exceeding_rate'            => $not_exceeding_rate,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_independent_assessment_sc);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_independent_assessment_sc,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create independent_assessment_sc', $response_data);
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
    
        $independent_assessment_sc_id = $id;
    
        if ($independent_assessment_sc_id === 0 || $independent_assessment_sc_id === "0") {
            $this->REST_Return(400, 'independent_assessment_sc_id is required');
            return;
        }
    
        $select = "a.id";
        $independent_assessment_sc = $this->$model->select($independent_assessment_sc_id, $select);
    
        if (count($independent_assessment_sc) == 0) {
            $this->REST_Return(404, 'independent_assessment_sc with id ' . $independent_assessment_sc_id . ' does not exist!');
            return;
        }

        $anc_minimal = $dt_put['anc_minimal'];
        $pink_kia_book = $dt_put['pink_kia_book'];
        $good_ku = $dt_put['good_ku'];
        $gcs_normal = $dt_put['gcs_normal'];
        $tension_changes = $dt_put['tension_changes'];
        $blood_test_before = $dt_put['blood_test_before'];
        $blood_test_after = $dt_put['blood_test_after'];
        $test_pt_aptt_ct_bt = $dt_put['test_pt_aptt_ct_bt'];
        $blood_transfusion = $dt_put['blood_transfusion'];
        $blood_type = $dt_put['blood_type'];
        $urinalysis_check = $dt_put['urinalysis_check'];
        $have_usg_data = $dt_put['have_usg_data'];
        $have_hiv_data = $dt_put['have_hiv_data'];
        $have_hepatitis_data = $dt_put['have_hepatitis_data'];
        $using_partograph = $dt_put['using_partograph'];
        $nulliparous_spontaneous = $dt_put['nulliparous_spontaneous'];
        $nulliparous_induction = $dt_put['nulliparous_induction'];
        $multipara_spontaneous = $dt_put['multipara_spontaneous'];
        $multipara_induction = $dt_put['multipara_induction'];
        $multipara_uterine_injury = $dt_put['multipara_uterine_injury'];
        $nulliparous_breech = $dt_put['nulliparous_breech'];
        $multipara_breech = $dt_put['multipara_breech'];
        $all_pregnancies_multiple_fetuses = $dt_put['all_pregnancies_multiple_fetuses'];
        $all_pregnancies_oblique_fetal = $dt_put['all_pregnancies_oblique_fetal'];
        $all_pregnancies_single_fetus = $dt_put['all_pregnancies_single_fetus'];
        $peb = $dt_put['peb'];
        $premature_rupture = $dt_put['premature_rupture'];
        $former_sc = $dt_put['former_sc'];
        $fetal_malposition = $dt_put['fetal_malposition'];
        $induction_failure = $dt_put['induction_failure'];
        $placental_abnormality = $dt_put['placental_abnormality'];
        $labor_not_progressing = $dt_put['labor_not_progressing'];
        $cephalopelvic_disproportion = $dt_put['cephalopelvic_disproportion'];
        $other_text = $dt_put['other_text'];
        $other = $dt_put['other'];
        $good_ku_after = $dt_put['good_ku_after'];
        $patient_died = $dt_put['patient_died'];
        $patient_died_referal = $dt_put['patient_died_referal'];
        $complications = $dt_put['complications'];
        $expansion_of_action = $dt_put['expansion_of_action'];
        $requires_expansion = $dt_put['requires_expansion'];
        $requires_further_care = $dt_put['requires_further_care'];
        $neonate_patient_died = $dt_put['neonate_patient_died'];
        $neonate_patient_died_referal = $dt_put['neonate_patient_died_referal'];
        $neonate_complications = $dt_put['neonate_complications'];
        $neonate_requires_expansion = $dt_put['neonate_requires_expansion'];
        $neonate_requires_further_care = $dt_put['neonate_requires_further_care'];
        $not_exceeding_rate = $dt_put['not_exceeding_rate'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_independent_assessment_sc = array(
            'anc_minimal'                   => $anc_minimal,
            'pink_kia_book'                 => $pink_kia_book,
            'good_ku'                       => $good_ku,
            'gcs_normal'                    => $gcs_normal,
            'tension_changes'               => $tension_changes,
            'blood_test_before'             => $blood_test_before,
            'blood_test_after'              => $blood_test_after,
            'test_pt_aptt_ct_bt'            => $test_pt_aptt_ct_bt,
            'blood_transfusion'             => $blood_transfusion,
            'blood_type'                    => $blood_type,
            'urinalysis_check'              => $urinalysis_check,
            'have_usg_data'                 => $have_usg_data,
            'have_hiv_data'                 => $have_hiv_data,
            'have_hepatitis_data'           => $have_hepatitis_data,
            'using_partograph'              => $using_partograph,
            'nulliparous_spontaneous'       => $nulliparous_spontaneous,
            'nulliparous_induction'         => $nulliparous_induction,
            'multipara_spontaneous'         => $multipara_spontaneous,
            'multipara_induction'           => $multipara_induction,
            'multipara_uterine_injury'      => $multipara_uterine_injury,
            'nulliparous_breech'            => $nulliparous_breech,
            'multipara_breech'              => $multipara_breech,
            'all_pregnancies_multiple_fetuses' => $all_pregnancies_multiple_fetuses,
            'all_pregnancies_oblique_fetal' => $all_pregnancies_oblique_fetal,
            'all_pregnancies_single_fetus'  => $all_pregnancies_single_fetus,
            'peb'                           => $peb,
            'premature_rupture'             => $premature_rupture,
            'former_sc'                     => $former_sc,
            'fetal_malposition'             => $fetal_malposition,
            'induction_failure'             => $induction_failure,
            'placental_abnormality'         => $placental_abnormality,
            'labor_not_progressing'         => $labor_not_progressing,
            'cephalopelvic_disproportion'   => $cephalopelvic_disproportion,
            'other_text'                    => $other_text,
            'other'                         => $other,
            'good_ku_after'                 => $good_ku_after,
            'patient_died'                  => $patient_died,
            'patient_died_referal'          => $patient_died_referal,
            'complications'                 => $complications,
            'expansion_of_action'           => $expansion_of_action,
            'requires_expansion'            => $requires_expansion,
            'requires_further_care'         => $requires_further_care,
            'neonate_patient_died'          => $neonate_patient_died,
            'neonate_patient_died_referal'  => $neonate_patient_died_referal,
            'neonate_complications'         => $neonate_complications,
            'neonate_requires_expansion'    => $neonate_requires_expansion,
            'neonate_requires_further_care' => $neonate_requires_further_care,
            'not_exceeding_rate'            => $not_exceeding_rate,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($independent_assessment_sc_id, $dt_independent_assessment_sc);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_independent_assessment_sc,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update independent_assessment_sc', $response_data);
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
            $this->REST_Return(400, 'independent_assessment_sc_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $independent_assessment_sc_id = $this->$model->select_id($id);
    
        if ($independent_assessment_sc_id == "") {
            $this->REST_Return(404, 'there is no independent_assessment_sc with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_independent_assessment_sc = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($independent_assessment_sc_id, $dt_independent_assessment_sc);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_independent_assessment_sc,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete independent_assessment_sc', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}