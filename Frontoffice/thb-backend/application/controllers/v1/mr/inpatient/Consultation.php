<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Consultation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/mr/Icd10_model", "Icd10");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_in_charge_id', 'label' => 'doctor_in_charge_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'consultant_id', 'label' => 'consultant_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim'),
            array('field' => 'consultation_type_id', 'label' => 'consultation_type_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'icd10[]', 'label' => 'icd10', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim'),
        );

        $this->done_rules = array(
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->cancel_rules = array(
            array('field' => 'is_cancel', 'label' => 'is_cancel', 'rules' => 'trim|required|max_length[1]'),
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
    
        $consultation_id = $id;
    
        if ($consultation_id === 0 || $consultation_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($consultation_id);
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

        for ($i=0; $i < count($output->rows); $i++) { 
            $consultation_id = $output->rows[$i]->id;

            $select = 'i.id as icd10_id, i.code as icd10_code, i.code_s as icd10_code_s, i.name as icd10_name';
            $where = array(
                'a.consultation_id' => $consultation_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
            );
            $icd10 = $this->Consultation_item->select(0, $select, $where, $join);

            $output->rows[$i]->icd10 = $icd10;
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
        $consultant_id = $dt_post['consultant_id'];

        $select = 'a.id, a.doctor_in_charge_id_2, a.doctor_in_charge_id_3, a.doctor_in_charge_id_4, a.doctor_in_charge_id_5, a.doctor_in_charge_id_6, a.doctor_in_charge_id_7, a.doctor_in_charge_id_8, a.doctor_in_charge_id_9, a.doctor_in_charge_id_10, a.doctor_in_charge_id_11, a.doctor_in_charge_id_12';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $doctor_in_charge_id_2 = $registration_destination[0]->doctor_in_charge_id_2;
        $doctor_in_charge_id_3 = $registration_destination[0]->doctor_in_charge_id_3;
        $doctor_in_charge_id_4 = $registration_destination[0]->doctor_in_charge_id_4;
        $doctor_in_charge_id_5 = $registration_destination[0]->doctor_in_charge_id_5;
        $doctor_in_charge_id_6 = $registration_destination[0]->doctor_in_charge_id_6;
        $doctor_in_charge_id_7 = $registration_destination[0]->doctor_in_charge_id_7;
        $doctor_in_charge_id_8 = $registration_destination[0]->doctor_in_charge_id_8;
        $doctor_in_charge_id_9 = $registration_destination[0]->doctor_in_charge_id_9;
        $doctor_in_charge_id_10 = $registration_destination[0]->doctor_in_charge_id_10;
        $doctor_in_charge_id_11 = $registration_destination[0]->doctor_in_charge_id_11;
        $doctor_in_charge_id_12 = $registration_destination[0]->doctor_in_charge_id_12;

        $doctors = array($doctor_in_charge_id_2,$doctor_in_charge_id_3,$doctor_in_charge_id_4,$doctor_in_charge_id_5,$doctor_in_charge_id_6,$doctor_in_charge_id_7,$doctor_in_charge_id_8,$doctor_in_charge_id_9,$doctor_in_charge_id_10,$doctor_in_charge_id_11,$doctor_in_charge_id_12);

        // if (in_array($consultant_id, $doctors) == true) {
        //     $this->REST_Return(409, 'doctor with id ' . $consultant_id . ' is already take care of this patient');
        //     return;
        // }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_in_charge_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $select = 'a.id';
        $employee2 = $this->Employee->select($consultant_id, $select);
        if (count($employee2) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $consultant_id);
            return;
        }

        // $date = $dt_post['date'];
        // $diagnosis = $dt_post['diagnosis'];
        $clinical_history = $dt_post['clinical_history'];
        $consultation_type_id = $dt_post['consultation_type_id'];
    
        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        // if ($doctor_in_charge_id_2 == null) {
        //     $dt_registration_destination['doctor_in_charge_id_2'] = $consultant_id;
        // } else {
        //     if ($doctor_in_charge_id_3 == null) {
        //         $dt_registration_destination['doctor_in_charge_id_3'] = $consultant_id;
        //     } else {
        //         if ($doctor_in_charge_id_4 == null) {
        //             $dt_registration_destination['doctor_in_charge_id_4'] = $consultant_id;
        //         } else {
        //             if ($doctor_in_charge_id_5 == null) {
        //                 $dt_registration_destination['doctor_in_charge_id_5'] = $consultant_id;
        //             } else {
        //                 $this->REST_Return(409, 'cannot adding more doctors 5 max');
        //                 return;
        //             }
        //         }
        //     }
        // }

        $assigned = false;

        // Untuk set consultant_id yang sudah ada di registration_destination
        for ($i = 2; $i <= 12; $i++) {
            $key = "doctor_in_charge_id_$i";
            if ($$key == $consultant_id) {
                $assigned = true;
                break;
            }
        }

        if (!$assigned) {
            for ($i = 2; $i <= 12; $i++) {
                $key = "doctor_in_charge_id_$i";
                if ($$key == null) {
                    $dt_registration_destination[$key] = $consultant_id;
                    $assigned = true;
                    break;
                }
            }
        }

        if (!$assigned) {
            $this->REST_Return(409, 'Tidak dapat menambahkan lebih banyak konsultasi dokter');
            return;
        }

        $this->Registration_destination->update_v2($registration_destination_id, $dt_registration_destination);
    
        $dt_consultation = array(
            'registration_destination_id'   => $registration_destination_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'consultant_id'                 => $consultant_id,
            // 'date'                          => $date,
            // 'diagnosis'                     => $diagnosis,
            'clinical_history'              => $clinical_history,
            'consultation_type_id'          => $consultation_type_id,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_consultation);

        $icd10_arr = $dt_post['icd10'];

        for ($i=0; $i < count($icd10_arr); $i++) { 
            $icd10_id = $icd10_arr[$i]['icd10_id'];

            if ($icd10_id == null) {
                $this->REST_Return(400, 'icd10_id in icd10 is required');
                return;
            }

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);
            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_consultation_item = array(
                'consultation_id' => $id,
                'icd10_id' => $icd10_id,
                'user_added' => $user_id,
                'date_added' => date('Y-m-d H:i:s',time()),
                'date_updated' => date('Y-m-d H:i:s',time()),
            );

            $this->Consultation_item->replace($dt_consultation_item);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create consultation', $response_data);
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
    
        $consultation_id = $id;
    
        if ($consultation_id === 0 || $consultation_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }

        $select = 'a.id';
        $consultation = $this->$model->select($consultation_id, $select);

        if (count($consultation) == 0) {
            $this->REST_Return(404, 'there is no consultation with id ' . $consultation_id);
            return;
        }

        // $diagnosis = $dt_put['diagnosis'];
        $clinical_history = $dt_put['clinical_history'];
        $consultation_answer = $dt_put['consultation_answer'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_consultation = array(
            // 'diagnosis'        => $diagnosis,
            'clinical_history' => $clinical_history,
            'consultation_answer' => $consultation_answer,
            'user_updated'     => $user_id,
            'date_updated'     => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($consultation_id, $dt_consultation);

        $dt_consultation_item_delete = array(
            'is_delete' => 1,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
        $where_delete = array(
            'consultation_id' => $consultation_id,
        );
        $this->Consultation_item->update_v2(0, $dt_consultation_item_delete, $where_delete);

        $icd10_arr = $dt_put['icd10'];

        for ($i=0; $i < count($icd10_arr); $i++) { 
            $icd10_id = $icd10_arr[$i]['icd10_id'];

            if ($icd10_id == null) {
                $this->REST_Return(400, 'icd10_id in icd10 is required');
                return;
            }

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);
            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $select = 'a.consultation_id';
            $where = array(
                'consultation_id' => $consultation_id,
                'icd10_id' => $icd10_id,
            );
            $consultation_item = $this->Consultation_item->select(0, $select, $where);

            $dt_consultation_item = array(
                'consultation_id' => $id,
                'icd10_id' => $icd10_id,
                'is_delete' => 0,
                'date_updated' => date('Y-m-d H:i:s',time()),
            );

            if (count($consultation_item) == 0) {
                $dt_consultation_item['user_added'] = $user_id;
                $dt_consultation_item['date_added'] = date('Y-m-d H:i:s',time());
            }

            $this->Consultation_item->replace($dt_consultation_item);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update consultation', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function done_put($p = 0) {
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
        $this->form_validation->set_rules($this->done_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $consultation_id = $id;
    
        if ($consultation_id === 0 || $consultation_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }

        $select = 'a.id, a.registration_destination_id, a.consultant_id, a.consultation_type_id';
        $consultation = $this->$model->select($consultation_id, $select);
        if (count($consultation) == 0) {
            $this->REST_Return(404, 'there is no consultation with id ' . $consultation_id);
            return;
        }

        $registration_destination_id = $consultation[0]->registration_destination_id;
        $consultant_id = $consultation[0]->consultant_id;
        $consultation_type_id = $consultation[0]->consultation_type_id;

        $select = 'a.id, a.doctor_in_charge_id_2, a.doctor_in_charge_id_3, a.doctor_in_charge_id_4, a.doctor_in_charge_id_5, a.doctor_in_charge_id_6, a.doctor_in_charge_id_7, a.doctor_in_charge_id_8, a.doctor_in_charge_id_9, a.doctor_in_charge_id_10, a.doctor_in_charge_id_11, a.doctor_in_charge_id_12';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        $doctor_in_charge_id_2 = $registration_destination[0]->doctor_in_charge_id_2;
        $doctor_in_charge_id_3 = $registration_destination[0]->doctor_in_charge_id_3;
        $doctor_in_charge_id_4 = $registration_destination[0]->doctor_in_charge_id_4;
        $doctor_in_charge_id_5 = $registration_destination[0]->doctor_in_charge_id_5;
        $doctor_in_charge_id_6 = $registration_destination[0]->doctor_in_charge_id_6;
        $doctor_in_charge_id_7 = $registration_destination[0]->doctor_in_charge_id_7;
        $doctor_in_charge_id_8 = $registration_destination[0]->doctor_in_charge_id_8;
        $doctor_in_charge_id_9 = $registration_destination[0]->doctor_in_charge_id_9;
        $doctor_in_charge_id_10 = $registration_destination[0]->doctor_in_charge_id_10;
        $doctor_in_charge_id_11 = $registration_destination[0]->doctor_in_charge_id_11;
        $doctor_in_charge_id_12 = $registration_destination[0]->doctor_in_charge_id_12;

        $status = $dt_put['status'];

        /* Start Transaction */
        $this->db->trans_start();

        if ($consultation_type_id == 3) {
            $dt_registration_destination = array(
                'user_updated' => $user_id,
                'date_updated' => date('Y-m-d H:i:s'),
            );
    
            // if ($doctor_in_charge_id_2 == $consultant_id) {
            //     $dt_registration_destination['doctor_in_charge_id_2'] = null;
            // } else {
            //     if ($doctor_in_charge_id_3 == $consultant_id) {
            //         $dt_registration_destination['doctor_in_charge_id_3'] = null;
            //     } else {
            //         if ($doctor_in_charge_id_4 == $consultant_id) {
            //             $dt_registration_destination['doctor_in_charge_id_4'] = null;
            //         } else {
            //             if ($doctor_in_charge_id_5 == $consultant_id) {
            //                 $dt_registration_destination['doctor_in_charge_id_5'] = null;
            //             } else {
            //                 $this->REST_Return(422, 'something wrong');
            //                 return;
            //             }
            //         }
            //     }
            // }

            for ($i = 2; $i <= 12; $i++) {
                $key = "doctor_in_charge_id_$i";
                if ($$key == $consultant_id) {
                    $dt_registration_destination[$key] = null;
                    $assigned = true;
                    break;
                }
            }

            if (!$assigned) {
                $this->REST_Return(422, 'something wrong');
                return;
            }
            
            $this->Registration_destination->update_v2($registration_destination_id, $dt_registration_destination);
        }
    
        $dt_consultation = array(
            'status'           => $status,
            'user_updated'     => $user_id,
            'date_updated'     => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($consultation_id, $dt_consultation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update consultation', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function shift_put($p = 0) {
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
        $this->form_validation->set_rules($this->done_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $consultation_id = $id;
    
        if ($consultation_id === 0 || $consultation_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }

        $select = 'a.id, a.registration_destination_id, a.consultant_id';
        $consultation = $this->$model->select($consultation_id, $select);

        if (count($consultation) == 0) {
            $this->REST_Return(404, 'there is no consultation with id ' . $consultation_id);
            return;
        }

        $registration_destination_id = $consultation[0]->registration_destination_id;
        $consultant_id = $consultation[0]->consultant_id;

        $select = 'a.id, a.doctor_in_charge_id_2, a.doctor_in_charge_id_3, a.doctor_in_charge_id_4, a.doctor_in_charge_id_5, a.doctor_in_charge_id_6, a.doctor_in_charge_id_7, a.doctor_in_charge_id_8, a.doctor_in_charge_id_9, a.doctor_in_charge_id_10, a.doctor_in_charge_id_11, a.doctor_in_charge_id_12';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        $doctor_in_charge_id_2 = $registration_destination[0]->doctor_in_charge_id_2;
        $doctor_in_charge_id_3 = $registration_destination[0]->doctor_in_charge_id_3;
        $doctor_in_charge_id_4 = $registration_destination[0]->doctor_in_charge_id_4;
        $doctor_in_charge_id_5 = $registration_destination[0]->doctor_in_charge_id_5;
        $doctor_in_charge_id_6 = $registration_destination[0]->doctor_in_charge_id_6;
        $doctor_in_charge_id_7 = $registration_destination[0]->doctor_in_charge_id_7;
        $doctor_in_charge_id_8 = $registration_destination[0]->doctor_in_charge_id_8;
        $doctor_in_charge_id_9 = $registration_destination[0]->doctor_in_charge_id_9;
        $doctor_in_charge_id_10 = $registration_destination[0]->doctor_in_charge_id_10;
        $doctor_in_charge_id_11 = $registration_destination[0]->doctor_in_charge_id_11;
        $doctor_in_charge_id_12 = $registration_destination[0]->doctor_in_charge_id_12;

        $status = $dt_put['status'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        if ($status == 1) {
            $dt_registration_destination['doctor_in_charge_id'] = $consultant_id;
        }

        // if ($doctor_in_charge_id_2 == $consultant_id) {
        //     $dt_registration_destination['doctor_in_charge_id_2'] = null;
        // } else {
        //     if ($doctor_in_charge_id_3 == $consultant_id) {
        //         $dt_registration_destination['doctor_in_charge_id_3'] = null;
        //     } else {
        //         if ($doctor_in_charge_id_4 == $consultant_id) {
        //             $dt_registration_destination['doctor_in_charge_id_4'] = null;
        //         } else {
        //             if ($doctor_in_charge_id_5 == $consultant_id) {
        //                 $dt_registration_destination['doctor_in_charge_id_5'] = null;
        //             } else {
        //                 $this->REST_Return(422, 'something wrong');
        //                 return;
        //             }
        //         }
        //     }
        // }

        for ($i = 2; $i <= 12; $i++) {
            $key = "doctor_in_charge_id_$i";
            if ($$key == $consultant_id) {
                $dt_registration_destination[$key] = null;
                $assigned = true;
                break;
            }
        }

        if (!$assigned) {
            $this->REST_Return(422, 'something wrong');
            return;
        }
        
        $this->Registration_destination->update_v2($registration_destination_id, $dt_registration_destination);

        $dt_consultation = array(
            'status'           => $status,
            'user_updated'     => $user_id,
            'date_updated'     => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($consultation_id, $dt_consultation);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update consultation', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
    
    public function cancel_put($p = 0) {
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
        $this->form_validation->set_rules($this->cancel_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $consultation_id = $id;
    
        if ($consultation_id === 0 || $consultation_id === "0") {
            $this->REST_Return(400, 'consultation_id is required');
            return;
        }

        $select = 'a.id, a.registration_destination_id, a.consultant_id';
        $consultation = $this->$model->select($consultation_id, $select);

        if (count($consultation) == 0) {
            $this->REST_Return(404, 'there is no consultation with id ' . $consultation_id);
            return;
        }

        $registration_destination_id = $consultation[0]->registration_destination_id;
        $consultant_id = $consultation[0]->consultant_id;

        $select = 'a.id, a.doctor_in_charge_id_2, a.doctor_in_charge_id_3, a.doctor_in_charge_id_4, a.doctor_in_charge_id_5, a.doctor_in_charge_id_6, a.doctor_in_charge_id_7, a.doctor_in_charge_id_8, a.doctor_in_charge_id_9, a.doctor_in_charge_id_10, a.doctor_in_charge_id_11, a.doctor_in_charge_id_12';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        $doctor_in_charge_id_2 = $registration_destination[0]->doctor_in_charge_id_2;
        $doctor_in_charge_id_3 = $registration_destination[0]->doctor_in_charge_id_3;
        $doctor_in_charge_id_4 = $registration_destination[0]->doctor_in_charge_id_4;
        $doctor_in_charge_id_5 = $registration_destination[0]->doctor_in_charge_id_5;
        $doctor_in_charge_id_6 = $registration_destination[0]->doctor_in_charge_id_6;
        $doctor_in_charge_id_7 = $registration_destination[0]->doctor_in_charge_id_7;
        $doctor_in_charge_id_8 = $registration_destination[0]->doctor_in_charge_id_8;
        $doctor_in_charge_id_9 = $registration_destination[0]->doctor_in_charge_id_9;
        $doctor_in_charge_id_10 = $registration_destination[0]->doctor_in_charge_id_10;
        $doctor_in_charge_id_11 = $registration_destination[0]->doctor_in_charge_id_11;
        $doctor_in_charge_id_12 = $registration_destination[0]->doctor_in_charge_id_12;

        $is_cancel = $dt_put['is_cancel'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_registration_destination = array(
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        // if ($doctor_in_charge_id_2 == $consultant_id) {
        //     $dt_registration_destination['doctor_in_charge_id_2'] = null;
        // } else {
        //     if ($doctor_in_charge_id_3 == $consultant_id) {
        //         $dt_registration_destination['doctor_in_charge_id_3'] = null;
        //     } else {
        //         if ($doctor_in_charge_id_4 == $consultant_id) {
        //             $dt_registration_destination['doctor_in_charge_id_4'] = null;
        //         } else {
        //             if ($doctor_in_charge_id_5 == $consultant_id) {
        //                 $dt_registration_destination['doctor_in_charge_id_5'] = null;
        //             } else {
        //                 $this->REST_Return(422, 'something wrong');
        //                 return;
        //             }
        //         }
        //     }
        // }

        for ($i = 2; $i <= 12; $i++) {
            $key = "doctor_in_charge_id_$i";
            if ($$key == $consultant_id) {
                $dt_registration_destination[$key] = null;
                $assigned = true;
                break;
            }
        }

        if (!$assigned) {
            $this->REST_Return(422, 'something wrong');
            return;
        }

        $this->Registration_destination->update_v2($registration_destination_id, $dt_registration_destination);
    
        $dt_consultation = array(
            'is_cancel'    => $is_cancel,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($consultation_id, $dt_consultation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_consultation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to cancel consultation', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}