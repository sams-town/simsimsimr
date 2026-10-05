<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Operative_report extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_a_model", "Soap_a");
        $this->load->model("v1/mr/Soap_a_action_model", "Soap_a_action");
        $this->load->model("v1/mr/Soap_p_model", "Soap_p");
        $this->load->model("v1/mr/Icd10_model", "Icd10");

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
    
        $operating_room_id = $q_strings['operating_room_id'];
    
        if ($operating_room_id == null) {
            $this->REST_Return(400, 'operating_room_id is required');
            return;
        }
    
        $select = 'a.*, or.registration_destination_id, or.soap_id, s.doctor_in_charge_id';
        $where = array(
            'a.operating_room_id' => $operating_room_id
        );
        $join = array(
            array('operating_room or', 'a.operating_room_id = or.id', 'INNER'),
            array('soap s', 'or.soap_id = s.id', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);
        $response_data = array();

        if (count($output) > 0) {
            $soap_id = $output[0]->soap_id;
            $doctor_in_charge_id = $output[0]->doctor_in_charge_id;
            
            $select = 'a.id, a.non_icd10';
            $where = array(
                'soap_id' => $soap_id
            );
            $soap_a = $this->Soap_a->select(0, $select, $where);

            $soap_a_id = $soap_a[0]->id;
            $pre_non_icd10 = ($soap_a[0]->non_icd10 != "") ? explode('|', $soap_a[0]->non_icd10) : [];

            $select = 'a.id, i.id as icd10_id, i.name as icd10_name, i.code as icd10_code';
            $where = array(
                'a.soap_a_id' => $soap_a_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join);

            $pre_icd10 = $soap_a_action;

            $select = 'a.id, a.therapy_plan';
            $where = array(
                'soap_id' => $soap_id,
                'doctor_in_charge_id' => $doctor_in_charge_id,
            );
            $soap_p = $this->Soap_p->select(0, $select, $where);

            $therapy_plan = $soap_p[0]->therapy_plan;

            $post_icd10_raw = json_decode($output[0]->post_icd10);

            $post_icd10_data = $post_icd10_raw->data;

            $post_icd10 = [];

            for ($i=0; $i < count($post_icd10_data); $i++) { 
                $icd10_id = $post_icd10_data[$i]->icd10_id;

                $select = 'a.id, a.name, a.code';
                $icd10 = $this->Icd10->select($icd10_id, $select);
                
                $icd10_id = $icd10[0]->id;
                $icd10_name = $icd10[0]->name;
                $icd10_code = $icd10[0]->code;

                $dt_icd10 = array(
                    'icd10_id' => $icd10_id,
                    'icd10_name' => $icd10_name,
                    'icd10_code' => $icd10_code,
                );

                array_push($post_icd10, $dt_icd10);
            }

            $output[0]->pre_icd10 = $pre_icd10;
            $output[0]->pre_non_icd10 = $pre_non_icd10;
            $output[0]->post_icd10 = $post_icd10;
            $output[0]->post_non_icd10 = ($output[0]->post_non_icd10 != "") ? explode('|', $output[0]->post_non_icd10) : [];
            $output[0]->therapy_plan = $therapy_plan;
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

        $select = 'a.id, a.soap_id';
        $operating_room = $this->Operating_room->select($operating_room_id, $select);
        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }

        $soap_id = $operating_room[0]->soap_id;

        $select = 'a.id, a.doctor_in_charge_id';
        $soap = $this->Soap->select($soap_id, $select);
        
        $doctor_in_charge_id = $soap[0]->doctor_in_charge_id;

        $pre_icd10 = $dt_post['pre_icd10'];
        $pre_non_icd10 = isset($dt_post['pre_non_icd10']) ? implode('|', $dt_post['pre_non_icd10']) : '';
        $therapy_plan = $dt_post['therapy_plan'];
        $post_icd10 = $dt_post['post_icd10'];
        $post_non_icd10 = isset($dt_post['post_non_icd10']) ? implode('|', $dt_post['post_non_icd10']) : '';
        $start_time = $dt_post['start_time'];
        $finish_time = $dt_post['finish_time'];
        $anesthesia_type = $dt_post['anesthesia_type'];
        $start_anesthesia_time = $dt_post['start_anesthesia_time'];
        $finish_anesthesia_time = $dt_post['finish_anesthesia_time'];
        $special_operation_type_id = $dt_post['special_operation_type_id'];
        $operation_group_type_id = $dt_post['operation_group_type_id'];
        $bleeding = $dt_post['bleeding'];
        $drain = $dt_post['drain'];
        $surgical_report = $dt_post['surgical_report'];
        $implant_type_id = $dt_post['implant_type_id'];
        $implant_code = $dt_post['implant_code'];
        $complications = $dt_post['complications'];
        $tissues_organs_removed = $dt_post['tissues_organs_removed'];
        $samples_sent = $dt_post['samples_sent'];
        $treatment_management_instructions = $dt_post['treatment_management_instructions'];
        $implant_image = $dt_post['implant_image'];

        // SOAP_A
        $dt_soap_a = array(
            'soap_id'       => $soap_id,
            'non_icd10'     => $pre_non_icd10,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_a = $this->Soap_a->select(0, $select, $where);
        if (count($soap_a) == 0) {
            $dt_soap_a['date_added'] = date('Y-m-d H:i:s');
            $soap_a_id = $this->Soap_a->create($dt_soap_a);
        } else {
            $soap_a_id = $soap_a[0]->id;
            $st_soap_a = $this->Soap_a->update_v2($soap_a_id, $dt_soap_a);
            if ($st_soap_a < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_A_ACTION
        $icd10_arr = $pre_icd10;

        $dt_soap_a_action_delete = array(
            'date_deleted' => date('Y-m-d H:i:s'),
            'user_deleted' => $user_id,
            'is_delete' => 1,
        );
        $where_delete = array(
            'soap_a_id' => $soap_a_id,
            'is_delete' => 0
        );
        $st_soap_a_action = $this->Soap_a_action->update_v2(0, $dt_soap_a_action_delete, $where_delete);
        if ($st_soap_a_action < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        for ($i=0; $i < count($icd10_arr); $i++) { 
            $icd10_id = $icd10_arr[$i]['icd10_id'];

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);
            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_soap_a_action = array(
                'soap_a_id' => $soap_a_id,
                'icd10_id'  => $icd10_id,
                'index'     => $i,
                'user_deleted' => '',
                'date_deleted' => '',
                'is_delete' => 0,
            );
   
            $select = 'a.id';
            $where = array(
                'a.soap_a_id' => $soap_a_id,
                'a.icd10_id' => $icd10_id,
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where);
            if (count($soap_a_action) == 0) {
                $this->Soap_a_action->create($dt_soap_a_action);
            } else {
                $soap_a_action_id = $soap_a_action[0]->id;
                $st_soap_a_action = $this->Soap_a_action->update_v2($soap_a_action_id, $dt_soap_a_action);
                if ($st_soap_a_action < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            }
        }

        // SOAP_P
        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'therapy_plan'          => $therapy_plan,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
        );
        $soap_p = $this->Soap_p->select(0, $select, $where);
        if (count($soap_p) == 0) {
            $soap_p_id = $this->Soap_p->create($dt_soap_p);
        } else {
            $soap_p_id = $soap_p[0]->id;
            $st_soap_p = $this->Soap_p->update_v2($soap_p_id, $dt_soap_p);
            if ($st_soap_p < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $post_icd10_data = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($post_icd10); $i++) { 
            if (!isset($post_icd10[$i]['icd10_id'])) {
                $this->REST_Return(400, 'icd10_id field in post_icd10 is required');
                return;
            }

            $icd10_id = $post_icd10[$i]['icd10_id'];

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_post_icd10 = array(
                'icd10_id' => $icd10_id
            );

            array_push($post_icd10_data->data, $dt_post_icd10);
        }

        $post_icd10 = json_encode($post_icd10_data);

        /* Start Transaction */
        $this->db->trans_start();

        if (isset($implant_url)) {
            $extension = $dt_post['extension'];
            $costing_name = $outputdata[$i]->costing_name;
            $date_request_raw = $outputdata[$i]->date_request;
            $unix_time = strtotime($date_request_raw);
            $date = date('dmY', $unix_time);

            $costing_name = str_replace(' ', '_', $costing_name);
            $costing_name = str_replace('/', '', $costing_name);
            $patient_name = str_replace(' ', '_', $patient_name);

            if ($extension == NULL) {
                $this->REST_Return(400, 'extension field is required');
                return;
            }

            $extension = explode('/', $extension)[1];

            $bytes = random_bytes(6 / 2);
            $random_6 = substr(bin2hex($bytes), 0, 6);
      
            $url = $this->base64ToFile($implant_url, $random_6 . '-' . $date . '-' . $norm . '-' . $patient_name . '.' . $extension);
            
            $implant_url = $url;
        } 
    
        $dt_operative_report = array(
            'operating_room_id'                 => $operating_room_id,
            'post_icd10'                        => $post_icd10,
            'post_non_icd10'                    => $post_non_icd10,
            'start_time'                        => $start_time,
            'finish_time'                       => $finish_time,
            'anesthesia_type'                   => $anesthesia_type,
            'start_anesthesia_time'             => $start_anesthesia_time,
            'finish_anesthesia_time'            => $finish_anesthesia_time,
            'special_operation_type_id'         => $special_operation_type_id,
            'operation_group_type_id'           => $operation_group_type_id,
            'bleeding'                          => $bleeding,
            'drain'                             => $drain,
            'surgical_report'                   => $surgical_report,
            'implant_type_id'                   => $implant_type_id,
            'implant_code'                      => $implant_code,
            'implant_url'                       => $implant_url,
            'complications'                     => $complications,
            'tissues_organs_removed'            => $tissues_organs_removed,
            'samples_sent'                      => $samples_sent,
            'treatment_management_instructions' => $treatment_management_instructions,
            'user_added'                        => $user_id,
            'date_added'                        => date('Y-m-d H:i:s',time()),
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_operative_report);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operative_report,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create operative_report', $response_data);
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
    
        $operative_report_id = $id;
    
        if ($operative_report_id === 0 || $operative_report_id === "0") {
            $this->REST_Return(400, 'operative_report_id is required');
            return;
        }

        $select = 'a.id, or.soap_id';
        $where = array();
        $join = array(
            array('operating_room or', 'a.operating_room_id = or.id', 'INNER'),
        );
        $operative_report = $this->$model->select($operative_report_id, $select, $where, $join);
        if (count($operative_report) == 0) {
            $this->REST_Return(404, 'there is no operative_report with id ' . $operative_report_id);
            return;
        }

        $soap_id = $operative_report[0]->soap_id;

        $select = 'a.id, a.doctor_in_charge_id';
        $soap = $this->Soap->select($soap_id, $select);
        
        $doctor_in_charge_id = $soap[0]->doctor_in_charge_id;

        $pre_icd10 = $dt_put['pre_icd10'];
        $pre_non_icd10 = isset($dt_put['pre_non_icd10']) ? implode('|', $dt_put['pre_non_icd10']) : '';
        $therapy_plan = $dt_put['therapy_plan'];
        $post_icd10 = $dt_put['post_icd10'];
        $post_non_icd10 = isset($dt_put['post_non_icd10']) ? implode('|', $dt_put['post_non_icd10']) : '';
        $start_time = $dt_put['start_time'];
        $finish_time = $dt_put['finish_time'];
        $anesthesia_type = $dt_put['anesthesia_type'];
        $start_anesthesia_time = $dt_put['start_anesthesia_time'];
        $finish_anesthesia_time = $dt_put['finish_anesthesia_time'];
        $special_operation_type_id = $dt_put['special_operation_type_id'];
        $operation_group_type_id = $dt_put['operation_group_type_id'];
        $bleeding = $dt_put['bleeding'];
        $drain = $dt_put['drain'];
        $surgical_report = $dt_put['surgical_report'];
        $implant_type_id = $dt_put['implant_type_id'];
        $implant_code = $dt_put['implant_code'];
        $implant_url = $dt_put['implant_url'];
        $complications = $dt_put['complications'];
        $tissues_organs_removed = $dt_put['tissues_organs_removed'];
        $samples_sent = $dt_put['samples_sent'];
        $treatment_management_instructions = $dt_put['treatment_management_instructions'];

        // SOAP_A
        $dt_soap_a = array(
            'soap_id'       => $soap_id,
            'non_icd10'     => $pre_non_icd10,
            'user_updated'  => $user_id,
            'date_updated'  => date('Y-m-d H:i:s'),
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id
        );
        $soap_a = $this->Soap_a->select(0, $select, $where);
        if (count($soap_a) == 0) {
            $dt_soap_a['date_added'] = date('Y-m-d H:i:s');
            $soap_a_id = $this->Soap_a->create($dt_soap_a);
        } else {
            $soap_a_id = $soap_a[0]->id;
            $st_soap_a = $this->Soap_a->update_v2($soap_a_id, $dt_soap_a);
            if ($st_soap_a < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        // SOAP_A_ACTION
        $icd10_arr = $pre_icd10;

        $dt_soap_a_action_delete = array(
            'date_deleted' => date('Y-m-d H:i:s'),
            'user_deleted' => $user_id,
            'is_delete' => 1,
        );
        $where_delete = array(
            'soap_a_id' => $soap_a_id,
            'is_delete' => 0
        );
        $st_soap_a_action = $this->Soap_a_action->update_v2(0, $dt_soap_a_action_delete, $where_delete);
        if ($st_soap_a_action < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        for ($i=0; $i < count($icd10_arr); $i++) { 
            $icd10_id = $icd10_arr[$i]['icd10_id'];

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);
            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_soap_a_action = array(
                'soap_a_id' => $soap_a_id,
                'icd10_id'  => $icd10_id,
                'index'     => $i,
                'user_deleted' => '',
                'date_deleted' => '',
                'is_delete' => 0,
            );
   
            $select = 'a.id';
            $where = array(
                'a.soap_a_id' => $soap_a_id,
                'a.icd10_id' => $icd10_id,
            );
            $soap_a_action = $this->Soap_a_action->select(0, $select, $where);
            if (count($soap_a_action) == 0) {
                $this->Soap_a_action->create($dt_soap_a_action);
            } else {
                $soap_a_action_id = $soap_a_action[0]->id;
                $st_soap_a_action = $this->Soap_a_action->update_v2($soap_a_action_id, $dt_soap_a_action);
                if ($st_soap_a_action < 0) {
                    $this->REST_Return(422, 'something when wrong');
                    return;
                }
            }
        }

        // SOAP_P
        $dt_soap_p = array(
            'soap_id'               => $soap_id,
            'doctor_in_charge_id'   => $doctor_in_charge_id,
            'therapy_plan'          => $therapy_plan,
        );

        $select = 'a.id';
        $where = array(
            'soap_id' => $soap_id,
            'doctor_in_charge_id' => $doctor_in_charge_id,
        );
        $soap_p = $this->Soap_p->select(0, $select, $where);
        if (count($soap_p) == 0) {
            $soap_p_id = $this->Soap_p->create($dt_soap_p);
        } else {
            $soap_p_id = $soap_p[0]->id;
            $st_soap_p = $this->Soap_p->update_v2($soap_p_id, $dt_soap_p);
            if ($st_soap_p < 0) {
                $this->REST_Return(422, 'something when wrong');
                return;
            }
        }

        $post_icd10_data = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($post_icd10); $i++) { 
            if (!isset($post_icd10[$i]['icd10_id'])) {
                $this->REST_Return(400, 'icd10_id field in post_icd10 is required');
                return;
            }

            $icd10_id = $post_icd10[$i]['icd10_id'];

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_post_icd10 = array(
                'icd10_id' => $icd10_id
            );

            array_push($post_icd10_data->data, $dt_post_icd10);
        }

        $post_icd10 = json_encode($post_icd10_data);

        /* Start Transaction */
        $this->db->trans_start();

        if (isset($implant_url)) {
            $extension = $dt_put['extension'];
            $costing_name = $outputdata[$i]->costing_name;
            $date_request_raw = $outputdata[$i]->date_request;
            $unix_time = strtotime($date_request_raw);
            $date = date('dmY', $unix_time);

            $costing_name = str_replace(' ', '_', $costing_name);
            $costing_name = str_replace('/', '', $costing_name);
            $patient_name = str_replace(' ', '_', $patient_name);

            if ($extension == NULL) {
                $this->REST_Return(400, 'extension field is required');
                return;
            }

            $extension = explode('/', $extension)[1];

            $bytes = random_bytes(6 / 2);
            $random_6 = substr(bin2hex($bytes), 0, 6);
      
            $url = $this->base64ToFile($implant_url, $random_6 . '-' . $date . '-' . $norm . '-' . $patient_name . '.' . $extension);
            
            $implant_url = $url;
        } 
    
        $dt_operative_report = array(
            'post_icd10'                        => $post_icd10,
            'post_non_icd10'                    => $post_non_icd10,
            'start_time'                        => $start_time,
            'finish_time'                       => $finish_time,
            'anesthesia_type'                   => $anesthesia_type,
            'start_anesthesia_time'             => $start_anesthesia_time,
            'finish_anesthesia_time'            => $finish_anesthesia_time,
            'special_operation_type_id'         => $special_operation_type_id,
            'operation_group_type_id'           => $operation_group_type_id,
            'bleeding'                          => $bleeding,
            'drain'                             => $drain,
            'surgical_report'                   => $surgical_report,
            'implant_type_id'                   => $implant_type_id,
            'implant_code'                      => $implant_code,
            'implant_url'                       => $implant_url,
            'complications'                     => $complications,
            'tissues_organs_removed'            => $tissues_organs_removed,
            'samples_sent'                      => $samples_sent,
            'treatment_management_instructions' => $treatment_management_instructions,
            'user_updated'                      => $user_id,
            'date_updated'                      => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update_v2($operative_report_id, $dt_operative_report);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_operative_report,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update operative_report', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    private function base64ToFile ($p = 0, $filename = '') {
        define('UPLOAD_DIR', 'assets/document/implant/');
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name;
        $success      = file_put_contents($file, $data);
        $file         = $file_name;
        return $file;
    }
}