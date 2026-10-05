<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Anesthesia_note extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");

        $this->create_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|max_length[36]'),
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
    
        $anesthesia_note_id = $id;
    
        if ($anesthesia_note_id === 0 || $anesthesia_note_id === "0") {
            $this->REST_Return(400, 'anesthesia_note_id is required');
            return;
        }
    
        $output = $this->$model->read($anesthesia_note_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->respiration_arry = ($output[0]->respiration_arry != "") ? explode(',', $output[0]->respiration_arry) : [];
            $output[0]->cardiovascular_arry = ($output[0]->cardiovascular_arry != "") ? explode(',', $output[0]->cardiovascular_arry) : [];
            $output[0]->hepato_arry = ($output[0]->hepato_arry != "") ? explode(',', $output[0]->hepato_arry) : [];
            $output[0]->neuro_arry = ($output[0]->neuro_arry != "") ? explode(',', $output[0]->neuro_arry) : [];
            $output[0]->renal_endocrine_arry = ($output[0]->renal_endocrine_arry != "") ? explode(',', $output[0]->renal_endocrine_arry) : [];
            $output[0]->other_arry = ($output[0]->other_arry != "") ? explode(',', $output[0]->other_arry) : [];
            $output[0]->anesthesia_plan_arry = ($output[0]->anesthesia_plan_arry != "") ? explode(',', $output[0]->anesthesia_plan_arry) : [];
            
            $output[0]->premedication = json_decode($output[0]->premedication);
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

        $operating_room_id = $dt_post['operating_room_id'];

        $select = 'a.id';
        $operating_room = $this->Operating_room->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }

        $priority_type_id = $dt_post['priority_type_id'];
        $examination_date = $dt_post['examination_date'];
        $mallampati_classification_type_id = $dt_post['mallampati_classification_type_id'];
        $respiration_arry = implode(',', $dt_post['respiration_arry']);
        $respiration_dbn = $dt_post['respiration_dbn'];
        $respiration_notes = $dt_post['respiration_notes'];
        $cardiovascular_arry = implode(',', $dt_post['cardiovascular_arry']);
        $cardiovascular_dbn = $dt_post['cardiovascular_dbn'];
        $cardiovascular_notes = $dt_post['cardiovascular_notes'];
        $hepato_arry = implode(',', $dt_post['hepato_arry']);
        $hepato_dbn = $dt_post['hepato_dbn'];
        $hepato_notes = $dt_post['hepato_notes'];
        $neuro_arry = implode(',', $dt_post['neuro_arry']);
        $neuro_dbn = $dt_post['neuro_dbn'];
        $neuro_notes = $dt_post['neuro_notes'];
        $renal_endocrine_arry = implode(',', $dt_post['renal_endocrine_arry']);
        $renal_endocrine_dbn = $dt_post['renal_endocrine_dbn'];
        $renal_endocrine_notes = $dt_post['renal_endocrine_notes'];
        $other_arry = implode(',', $dt_post['other_arry']);
        $other_dbn = $dt_post['other_dbn'];
        $other_notes = $dt_post['other_notes'];
        $drug_allergy = $dt_post['drug_allergy'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $pulse_frequency = $dt_post['pulse_frequency'];
        $breath_frequency = $dt_post['breath_frequency'];
        $temperature = $dt_post['temperature'];
        $medications_currently_type_id = $dt_post['medications_currently_type_id'];
        $history_anesthesia = $dt_post['history_anesthesia'];
        $anamnesa_from_type_id = $dt_post['anamnesa_from_type_id'];
        $fasting_solid_food = $dt_post['fasting_solid_food'];
        $fasting_liquid_food = $dt_post['fasting_liquid_food'];
        $drugs_given_type_id = $dt_post['drugs_given_type_id'];
        $anesthesia_plan_arry = implode(',', $dt_post['anesthesia_plan_arry']);
        $anesthesia_asa_type_id = $dt_post['anesthesia_asa_type_id'];
        $anesthesia_plan_type_id = $dt_post['anesthesia_plan_type_id'];
        $premedication = $dt_post['premedication'];

        $premedication_data = array(
            'data' => array(),
        );
        
        for ($i=0; $i < count($premedication); $i++) { 
            if (!isset($premedication[$i]['drug']) || !isset($premedication[$i]['time'])) {
                $this->REST_Return(400, 'drug & time field in premedication is required');
                return;
            }

            $drug = $premedication[$i]['drug'];
            $time = $premedication[$i]['time'];

            $dt_premedication = array(
                'drug' => $drug,
                'time' => $time,
            );

            array_push($premedication_data['data'], $dt_premedication);
        }

        $premedication_encode = json_encode($premedication_data);

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_anesthesia_note = array(
            'operating_room_id'                 => $operating_room_id,
            'priority_type_id'                  => $priority_type_id,
            'examination_date'                  => $examination_date,
            'mallampati_classification_type_id' => $mallampati_classification_type_id,
            'respiration_arry'                  => $respiration_arry,
            'respiration_dbn'                   => $respiration_dbn,
            'respiration_notes'                 => $respiration_notes,
            'cardiovascular_arry'               => $cardiovascular_arry,
            'cardiovascular_dbn'                => $cardiovascular_dbn,
            'cardiovascular_notes'              => $cardiovascular_notes,
            'hepato_arry'                       => $hepato_arry,
            'hepato_dbn'                        => $hepato_dbn,
            'hepato_notes'                      => $hepato_notes,
            'neuro_arry'                        => $neuro_arry,
            'neuro_dbn'                         => $neuro_dbn,
            'neuro_notes'                       => $neuro_notes,
            'renal_endocrine_arry'              => $renal_endocrine_arry,
            'renal_endocrine_dbn'               => $renal_endocrine_dbn,
            'renal_endocrine_notes'             => $renal_endocrine_notes,
            'other_arry'                        => $other_arry,
            'other_dbn'                         => $other_dbn,
            'other_notes'                       => $other_notes,
            'drug_allergy'                      => $drug_allergy,
            'systolic'                          => $systolic,
            'diastolic'                         => $diastolic,
            'weight'                            => $weight,
            'height'                            => $height,
            'pulse_frequency'                   => $pulse_frequency,
            'breath_frequency'                  => $breath_frequency,
            'temperature'                       => $temperature,
            'medications_currently_type_id'     => $medications_currently_type_id,
            'history_anesthesia'                => $history_anesthesia,
            'anamnesa_from_type_id'             => $anamnesa_from_type_id,
            'fasting_solid_food'                => $fasting_solid_food,
            'fasting_liquid_food'               => $fasting_liquid_food,
            'drugs_given_type_id'               => $drugs_given_type_id,
            'anesthesia_plan_arry'              => $anesthesia_plan_arry,
            'anesthesia_asa_type_id'            => $anesthesia_asa_type_id,
            'anesthesia_plan_type_id'           => $anesthesia_plan_type_id,
            'premedication'                     => $premedication_encode,
            'user_added'                        => $user_id,
            'date_added'                        => date('Y-m-d H:i:s',time()),
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
        
        $id = $this->$model->create($dt_anesthesia_note);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_anesthesia_note,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create anesthesia_note', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
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
    
        $anesthesia_note_id = $id;
    
        if ($anesthesia_note_id === 0 || $anesthesia_note_id === "0") {
            $this->REST_Return(400, 'anesthesia_note_id is required');
            return;
        }

        $select = 'a.id';
        $anesthesia_note = $this->$model->select($anesthesia_note_id, $select);

        if (count($anesthesia_note) == 0) {
            $this->REST_Return(404, 'there is no anesthesia_note with id ' . $anesthesia_note_id);
            return;
        }

        $priority_type_id = $dt_put['priority_type_id'];
        $examination_date = $dt_put['examination_date'];
        $mallampati_classification_type_id = $dt_put['mallampati_classification_type_id'];
        $respiration_arry = implode(',', $dt_put['respiration_arry']);
        $respiration_dbn = $dt_put['respiration_dbn'];
        $respiration_notes = $dt_put['respiration_notes'];
        $cardiovascular_arry = implode(',', $dt_put['cardiovascular_arry']);
        $cardiovascular_dbn = $dt_put['cardiovascular_dbn'];
        $cardiovascular_notes = $dt_put['cardiovascular_notes'];
        $hepato_arry = implode(',', $dt_put['hepato_arry']);
        $hepato_dbn = $dt_put['hepato_dbn'];
        $hepato_notes = $dt_put['hepato_notes'];
        $neuro_arry = implode(',', $dt_put['neuro_arry']);
        $neuro_dbn = $dt_put['neuro_dbn'];
        $neuro_notes = $dt_put['neuro_notes'];
        $renal_endocrine_arry = implode(',', $dt_put['renal_endocrine_arry']);
        $renal_endocrine_dbn = $dt_put['renal_endocrine_dbn'];
        $renal_endocrine_notes = $dt_put['renal_endocrine_notes'];
        $other_arry = implode(',', $dt_put['other_arry']);
        $other_dbn = $dt_put['other_dbn'];
        $other_notes = $dt_put['other_notes'];
        $drug_allergy = $dt_put['drug_allergy'];
        $systolic = $dt_put['systolic'];
        $diastolic = $dt_put['diastolic'];
        $weight = $dt_put['weight'];
        $height = $dt_put['height'];
        $pulse_frequency = $dt_put['pulse_frequency'];
        $breath_frequency = $dt_put['breath_frequency'];
        $temperature = $dt_put['temperature'];
        $medications_currently_type_id = $dt_put['medications_currently_type_id'];
        $history_anesthesia = $dt_put['history_anesthesia'];
        $anamnesa_from_type_id = $dt_put['anamnesa_from_type_id'];
        $fasting_solid_food = $dt_put['fasting_solid_food'];
        $fasting_liquid_food = $dt_put['fasting_liquid_food'];
        $drugs_given_type_id = $dt_put['drugs_given_type_id'];
        $anesthesia_plan_arry = implode(',', $dt_put['anesthesia_plan_arry']);
        $anesthesia_asa_type_id = $dt_put['anesthesia_asa_type_id'];
        $anesthesia_plan_type_id = $dt_put['anesthesia_plan_type_id'];
        $premedication = $dt_put['premedication'];

        $premedication_data = array(
            'data' => array(),
        );
        
        for ($i=0; $i < count($premedication); $i++) { 
            if (!isset($premedication[$i]['drug']) || !isset($premedication[$i]['time'])) {
                $this->REST_Return(400, 'drug & time field in premedication is required');
                return;
            }

            $drug = $premedication[$i]['drug'];
            $time = $premedication[$i]['time'];

            $dt_premedication = array(
                'drug' => $drug,
                'time' => $time,
            );

            array_push($premedication_data['data'], $dt_premedication);
        }

        $premedication_encode = json_encode($premedication_data);

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_anesthesia_note = array(
            'priority_type_id'                  => $priority_type_id,
            'examination_date'                  => $examination_date,
            'mallampati_classification_type_id' => $mallampati_classification_type_id,
            'respiration_arry'                  => $respiration_arry,
            'respiration_dbn'                   => $respiration_dbn,
            'respiration_notes'                 => $respiration_notes,
            'cardiovascular_arry'               => $cardiovascular_arry,
            'cardiovascular_dbn'                => $cardiovascular_dbn,
            'cardiovascular_notes'              => $cardiovascular_notes,
            'hepato_arry'                       => $hepato_arry,
            'hepato_dbn'                        => $hepato_dbn,
            'hepato_notes'                      => $hepato_notes,
            'neuro_arry'                        => $neuro_arry,
            'neuro_dbn'                         => $neuro_dbn,
            'neuro_notes'                       => $neuro_notes,
            'renal_endocrine_arry'              => $renal_endocrine_arry,
            'renal_endocrine_dbn'               => $renal_endocrine_dbn,
            'renal_endocrine_notes'             => $renal_endocrine_notes,
            'other_arry'                        => $other_arry,
            'other_dbn'                         => $other_dbn,
            'other_notes'                       => $other_notes,
            'drug_allergy'                      => $drug_allergy,
            'systolic'                          => $systolic,
            'diastolic'                         => $diastolic,
            'weight'                            => $weight,
            'height'                            => $height,
            'pulse_frequency'                   => $pulse_frequency,
            'breath_frequency'                  => $breath_frequency,
            'temperature'                       => $temperature,
            'medications_currently_type_id'     => $medications_currently_type_id,
            'history_anesthesia'                => $history_anesthesia,
            'anamnesa_from_type_id'             => $anamnesa_from_type_id,
            'fasting_solid_food'                => $fasting_solid_food,
            'fasting_liquid_food'               => $fasting_liquid_food,
            'drugs_given_type_id'               => $drugs_given_type_id,
            'anesthesia_plan_arry'              => $anesthesia_plan_arry,
            'anesthesia_asa_type_id'            => $anesthesia_asa_type_id,
            'anesthesia_plan_type_id'           => $anesthesia_plan_type_id,
            'premedication'                     => $premedication_encode,
            'user_updated'                      => $user_id,
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($anesthesia_note_id, $dt_anesthesia_note);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_anesthesia_note,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update anesthesia_note', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}