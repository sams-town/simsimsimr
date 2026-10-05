<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Medical_rehabilitation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Icd9cm_model", "Icd9cm");
        $this->load->model("$this->directory/Icd10_model", "Icd10");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'soap_id', 'label' => 'soap_id', 'rules' => 'trim|max_length[36]'),
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
    
        $medical_rehabilitation_id = $id;
    
        if ($medical_rehabilitation_id === 0 || $medical_rehabilitation_id === "0") {
            $this->REST_Return(400, 'medical_rehabilitation_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($medical_rehabilitation_id);
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

        $service_date = $dt_post['service_date'];
        $anamnesa = $dt_post['anamnesa'];
        $functional_testing = $dt_post['functional_testing'];
        $support_examination = $dt_post['support_examination'];
        $advice = $dt_post['advice'];
        $evaluation = $dt_post['evaluation'];
        $suspected_disease_type_id = $dt_post['suspected_disease_type_id'];
        $suspected_disease = $dt_post['suspected_disease'];

        $medical_diagnosis = $dt_post['medical_diagnosis'];
        $functional_diagnosis = $dt_post['functional_diagnosis'];
        $kfr_management = $dt_post['kfr_management'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_medical_rehabilitation = array(
            'registration_destination_id'   => $registration_destination_id,
            'doctor_in_charge_id'           => $doctor_in_charge_id,
            'service_date'                  => $service_date,
            'anamnesa'                      => $anamnesa,
            'functional_testing'            => $functional_testing,
            'support_examination'           => $support_examination,
            'advice'                        => $advice,
            'evaluation'                    => $evaluation,
            'suspected_disease_type_id'     => $suspected_disease_type_id,
            'suspected_disease'             => $suspected_disease,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_medical_rehabilitation);

        // add item (icd10, icd9cm)
        for ($i=0; $i < count($medical_diagnosis); $i++) { 
            $icd10_id = $medical_diagnosis[$i]['icd10_id'];

            if ($icd10_id == null) {
                $this->REST_Return(400, 'field icd10_id in medical_diagnosis is required');
                return;
            }

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_medical_rehabilitation_item = array(
                'medical_rehabilitation_id' => $id,
                'item_type_id' => 1,
                'item_id' => $icd10_id,
            );

            $this->Medical_rehabilitation_item->create($dt_medical_rehabilitation_item);
        }

        for ($i=0; $i < count($functional_diagnosis); $i++) { 
            $icd10_id = $functional_diagnosis[$i]['icd10_id'];

            if ($icd10_id == null) {
                $this->REST_Return(400, 'field icd10_id in functional_diagnosis is required');
                return;
            }

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_medical_rehabilitation_item = array(
                'medical_rehabilitation_id' => $id,
                'item_type_id' => 2,
                'item_id' => $icd10_id,
            );

            $this->Medical_rehabilitation_item->create($dt_medical_rehabilitation_item);
        }

        for ($i=0; $i < count($kfr_management); $i++) { 
            $icd9cm_id = $kfr_management[$i]['icd9cm_id'];

            if ($icd9cm_id == null) {
                $this->REST_Return(400, 'field icd9cm_id in kfr_management is required');
                return;
            }

            $select = 'a.id';
            $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);

            if (count($icd9cm) == 0) {
                $this->REST_Return(404, 'there is no icd9cm with id ' . $icd9cm_id);
                return;
            }

            $dt_medical_rehabilitation_item = array(
                'medical_rehabilitation_id' => $id,
                'item_type_id' => 3,
                'item_id' => $icd9cm_id,
            );

            $this->Medical_rehabilitation_item->create($dt_medical_rehabilitation_item);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_medical_rehabilitation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create medical_rehabilitation', $response_data);
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
    
        $medical_rehabilitation_id = $id;
    
        if ($medical_rehabilitation_id === 0 || $medical_rehabilitation_id === "0") {
            $this->REST_Return(400, 'medical_rehabilitation_id is required');
            return;
        }

        $service_date = $dt_put['service_date'];
        $anamnesa = $dt_put['anamnesa'];
        $functional_testing = $dt_put['functional_testing'];
        $support_examination = $dt_put['support_examination'];
        $advice = $dt_put['advice'];
        $evaluation = $dt_put['evaluation'];
        $suspected_disease_type_id = $dt_put['suspected_disease_type_id'];
        $suspected_disease = $dt_put['suspected_disease'];

        $medical_diagnosis = $dt_put['medical_diagnosis'];
        $functional_diagnosis = $dt_put['functional_diagnosis'];
        $kfr_management = $dt_put['kfr_management'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $medical_rehabilitation = array(
            'service_date'                  => $service_date,
            'anamnesa'                      => $anamnesa,
            'functional_testing'            => $functional_testing,
            'support_examination'           => $support_examination,
            'advice'                        => $advice,
            'evaluation'                    => $evaluation,
            'suspected_disease_type_id'     => $suspected_disease_type_id,
            'suspected_disease'             => $suspected_disease,
            'user_updated'                  => $user_id,
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($medical_rehabilitation_id, $medical_rehabilitation);
        
        // add or delete item

        // delete all current medical_diagnosis
        $dt_medical_rehabilitation_item_delete = array(
            'is_delete' => 1,
        );
        $where_delete = array(
            'medical_rehabilitation_id' => $medical_rehabilitation_id,
            'item_type_id' => 1,
        );
        $this->Medical_rehabilitation_item->update_v2(0, $dt_medical_rehabilitation_item_delete, $where_delete);

        for ($i=0; $i < count($medical_diagnosis); $i++) { 
            $icd10_id = $medical_diagnosis[$i]['icd10_id'];

            if ($icd10_id == null) {
                $this->REST_Return(400, 'field icd10_id in medical_diagnosis is required');
                return;
            }

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_medical_rehabilitation_item = array(
                'medical_rehabilitation_id' => $medical_rehabilitation_id,
                'item_type_id' => 1,
                'item_id' => $icd10_id,
                'is_delete' => 0,
            );
            
            $select = 'a.id';
            $where = array(
                'a.item_id' => $icd10_id,
            );
            $medical_rehabilitation_item = $this->Medical_rehabilitation_item->select(0, $select, $where);

            if (count($medical_rehabilitation_item) > 0) {
                $medical_rehabilitation_item_id = $medical_rehabilitation_item[0]->id;

                $dt_medical_rehabilitation_item['id'] = $medical_rehabilitation_item_id;

                $this->Medical_rehabilitation_item->replace($dt_medical_rehabilitation_item);
            } else {
                $this->Medical_rehabilitation_item->create($dt_medical_rehabilitation_item);
            }
        }

        // delete all current functional_diagnosis
        $dt_medical_rehabilitation_item_delete = array(
            'is_delete' => 1,
        );
        $where_delete = array(
            'medical_rehabilitation_id' => $medical_rehabilitation_id,
            'item_type_id' => 2,
        );
        $this->Medical_rehabilitation_item->update_v2(0, $dt_medical_rehabilitation_item_delete, $where_delete);

        for ($i=0; $i < count($functional_diagnosis); $i++) { 
            $icd10_id = $functional_diagnosis[$i]['icd10_id'];

            if ($icd10_id == null) {
                $this->REST_Return(400, 'field icd10_id in functional_diagnosis is required');
                return;
            }

            $select = 'a.id';
            $icd10 = $this->Icd10->select($icd10_id, $select);

            if (count($icd10) == 0) {
                $this->REST_Return(404, 'there is no icd10 with id ' . $icd10_id);
                return;
            }

            $dt_medical_rehabilitation_item = array(
                'medical_rehabilitation_id' => $medical_rehabilitation_id,
                'item_type_id' => 2,
                'item_id' => $icd10_id,
                'is_delete' => 0,
            );
            
            $select = 'a.id';
            $where = array(
                'a.item_id' => $icd10_id,
            );
            $medical_rehabilitation_item = $this->Medical_rehabilitation_item->select(0, $select, $where);

            if (count($medical_rehabilitation_item) > 0) {
                $medical_rehabilitation_item_id = $medical_rehabilitation_item[0]->id;

                $dt_medical_rehabilitation_item['id'] = $medical_rehabilitation_item_id;

                $this->Medical_rehabilitation_item->replace($dt_medical_rehabilitation_item);
            } else {
                $this->Medical_rehabilitation_item->create($dt_medical_rehabilitation_item);
            }
        }

        // delete all current kfr_management
        $dt_medical_rehabilitation_item_delete = array(
            'is_delete' => 1,
        );
        $where_delete = array(
            'medical_rehabilitation_id' => $medical_rehabilitation_id,
            'item_type_id' => 3,
        );
        $this->Medical_rehabilitation_item->update_v2(0, $dt_medical_rehabilitation_item_delete, $where_delete);

        for ($i=0; $i < count($kfr_management); $i++) { 
            $icd9cm_id = $kfr_management[$i]['icd9cm_id'];

            if ($icd9cm_id == null) {
                $this->REST_Return(400, 'field icd9cm_id in kfr_management is required');
                return;
            }

            $select = 'a.id';
            $icd9cm = $this->Icd9cm->select($icd9cm_id, $select);

            if (count($icd9cm) == 0) {
                $this->REST_Return(404, 'there is no icd9cm with id ' . $icd9cm_id);
                return;
            }

            $dt_medical_rehabilitation_item = array(
                'medical_rehabilitation_id' => $medical_rehabilitation_id,
                'item_type_id' => 3,
                'item_id' => $icd9cm_id,
                'is_delete' => 0,
            );
            
            $select = 'a.id';
            $where = array(
                'a.item_id' => $icd9cm_id,
            );
            $medical_rehabilitation_item = $this->Medical_rehabilitation_item->select(0, $select, $where);

            if (count($medical_rehabilitation_item) > 0) {
                $medical_rehabilitation_item_id = $medical_rehabilitation_item[0]->id;

                $dt_medical_rehabilitation_item['id'] = $medical_rehabilitation_item_id;

                $this->Medical_rehabilitation_item->replace($dt_medical_rehabilitation_item);
            } else {
                $this->Medical_rehabilitation_item->create($dt_medical_rehabilitation_item);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $medical_rehabilitation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update medical_rehabilitation', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_result_put($p = 0) {
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
    
        $medical_rehabilitation_id = $id;
    
        if ($medical_rehabilitation_id === 0 || $medical_rehabilitation_id === "0") {
            $this->REST_Return(400, 'medical_rehabilitation_id is required');
            return;
        }

        $result = $dt_put['result'];
        $conclusion = $dt_put['conclusion'];
        $recommendation = $dt_put['recommendation'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $medical_rehabilitation = array(
            'result'            => $result,
            'conclusion'        => $conclusion,
            'recommendation'    => $recommendation,
            'user_updated'      => $user_id,
            'date_result'       => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($medical_rehabilitation_id, $medical_rehabilitation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $medical_rehabilitation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update medical_rehabilitation', $response_data);
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
            $this->REST_Return(400, 'medical_rehabilitation_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $medical_rehabilitation_id = $this->$model->select_id($id);
    
        if ($medical_rehabilitation_id == "") {
            $this->REST_Return(404, 'there is no hemodialysis with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_hemodialysis = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $id = $this->$model->update($medical_rehabilitation_id, $dt_hemodialysis);
    
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
            'id' => $medical_rehabilitation_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}