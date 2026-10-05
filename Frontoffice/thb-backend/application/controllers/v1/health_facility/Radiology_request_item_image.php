<?php defined('BASEPATH') or exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;

class Radiology_request_item_image extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        $this->load->helper('url');
        // Load Model
        $this->load->model("$this->directory/Radiology_request_image_model", "Radiology_request_image");
        $this->load->model("$this->directory/Radiology_request_model", "Radiology_request");
        $this->load->model("$this->directory/Radiology_request_read_model", "Radiology_request_read");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/hr/Position_model", "Position");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/setting/Notifications_model", "Notifications");

        $this->update_rules = array(
            array('field' => 'result', 'label' => 'result', 'rules' => 'trim'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[255]'),
            array('field' => 'date_process', 'label' => 'date_process', 'rules' => 'trim'),
            array('field' => 'date_result', 'label' => 'date_result', 'rules' => 'trim'),
            array('field' => 'doctor_lab_id', 'label' => 'doctor_lab_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'user_lab_id', 'label' => 'user_lab_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'conclusion', 'label' => 'conclusion', 'rules' => 'trim'),
            array('field' => 'notes', 'label' => 'notes', 'rules' => 'trim'),
            array('field' => 'advice', 'label' => 'advice', 'rules' => 'trim'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

    }

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Radiology_request_image";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $radiology_request_item_image_id = $id;

        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($radiology_request_item_image_id);
        $response_data = array();

        if ($output == []) {
            $this->REST_Return(404, 'radiology_request_item_image with id ' . $radiology_request_item_image_id . ' does not exist!');
            return;
        }

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = "Radiology_request_image";
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

        $radiology_request_item_image_id = $id;

        $select = "a.id,a.radiology_request_item_id,a.date_result";
        $radiology_request_item_image = $this->$model->select($radiology_request_item_image_id, $select);
        if (count($radiology_request_item_image) == 0) {
            $this->REST_Return(404, 'radiology_request_item_image with id ' . $radiology_request_item_image_id . ' does not exist!');
            return;
        }

        $exist_date_result = $radiology_request_item_image[0]->date_result;

        $radiology_request_item_id = $radiology_request_item_image[0]->radiology_request_item_id;

        $select = "a.id, a.costing_name, a.radiology_request_id, a.date_request, a.costing_name, c.health_facility_item_id, hfi.health_facility_id, p.name as patient_name, hp.name as patient_alias, d.norm";
        $where = array();
        $join = array(
            array('radiology_request h', 'a.radiology_request_id = h.id', 'LEFT'),
            array('soap b', 'h.soap_id = b.id', 'LEFT'),
            array('registration_destination c', 'b.registration_destination_id = c.id', 'LEFT'),
            array('registration d', 'c.registration_id = d.id', 'LEFT'),
            array('health_facility_item hfi', 'c.health_facility_item_id = hfi.id', 'LEFT'),
            array('patient p', 'd.norm = p.norm', 'LEFT'),
            array('helper hp', 'p.alias = hp.code AND hp.variable="alias"', 'LEFT'),
        );
        $radiology_request_item = $this->Radiology_request_read->select($radiology_request_item_id, $select, $where, $join);

        /* Start Transaction */
        $this->db->trans_start();
        
        $health_facility_id = $radiology_request_item[0]->health_facility_id;
        if ($exist_date_result == null && $health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
            $health_facility_item_id = $radiology_request_item[0]->health_facility_item_id;
            $item_name = $radiology_request_item[0]->costing_name;
            $patient_name = $radiology_request_item[0]->patient_name;
            $patient_alias = $radiology_request_item[0]->patient_alias;
            $norm = $radiology_request_item[0]->norm;

            $select = 'a.id';
            $where = array(
                'a.health_facility_item_id' => $health_facility_item_id,
            );
            $position = $this->Position->select(0, $select, $where);

            $position_id = $position[0]->id;
            
            $title = 'Expertise Radiologi';
            $message = "Expertise $item_name pasien $patient_alias $patient_name ($norm) sudah terisi";
            $this->Notifications->createNotification(null, $position_id, $title, $message, 'rad_expertise', ['radiology_request_item_id' => $radiology_request_item_id]);
        }

        $radiology_request_id = $radiology_request_item[0]->radiology_request_id;
        $date_request = $radiology_request_item[0]->date_request;
        $costing_name = $radiology_request_item[0]->costing_name;
        $date_process = $dt_put['date_process'];
        $date_result = $dt_put['date_result'];
        $doctor_lab_id = $dt_put['doctor_lab_id'];
        $user_lab_id = $dt_put['user_lab_id'];
        $conclusion = $dt_put['conclusion'];
        $notes = $dt_put['notes'];
        $advice = $dt_put['advice'];

        $dt_radiology_request_item_image = array(
            'id'                        => $radiology_request_item_image_id,
            'radiology_request_item_id' => $radiology_request_item_id,
            'status'                    => 2,
            'date_request'              => $date_request,
            'date_process'              => $date_process,
            'date_result'               => $date_result,
            'doctor_lab_id'             => $doctor_lab_id,
            'user_lab_id'               => $user_lab_id,
            'conclusion'                => $conclusion,
            'notes'                     => $notes,
            'advice'                    => $advice,
        );

        // replace url if result is set
        if (isset($dt_put['result'])) {
            if ($dt_put['result'] != "") {
                $select = 'a.id, a.soap_id';
                $radiology_request = $this->Radiology_request->select($radiology_request_id, $select);

                if (count($radiology_request) == 0) {
                    $this->REST_Return(404, 'there is no radiology_request with id ' . $radiology_request_id);
                    return;
                }

                $soap_id = $radiology_request[0]->soap_id;

                $select = 'a.id, r.norm';
                $soap = $this->Soap->select($soap_id, $select);

                if (count($soap) == 0) {
                    $this->REST_Return(404, 'there is no soap with id ' . $soap_id);
                    return;
                }
                
                $norm = $soap[0]->norm;

                $select = 'a.norm, a.name';
                $patient = $this->Patient->select($norm, $select);

                if (count($patient) == 0) {
                    $this->REST_Return(404, 'there is no patient with norm ' . $norm);
                    return;
                }

                $patient_name = $patient[0]->name;

                $result = $dt_put['result'];
                $extension = $dt_put['extension'];
                $unix_time = strtotime($date_request);
                $date = date('dmY', $unix_time);

                $costing_name = str_replace(' ', '_', $costing_name);
                $costing_name = str_replace('/', '', $costing_name);
                $patient_name = str_replace(' ', '_', $patient_name);

                if ($extension == NULL) {
                    $this->REST_Return(400, 'extension field is required');
                    return;
                }

                $extension = explode('/', $extension)[1];
                
                $bytes = random_bytes(5 / 2);
                $random_6 = substr(bin2hex($bytes), 0, 5);
                
                $url = $this->base64ToFile($result, $random_6 . '-' . $costing_name . '-' . $date . '-' . $norm . '-' . $patient_name . '.' . $extension);
                
                $dt_radiology_request_item_image['url'] = $url;
            }
        }

        $id = $this->$model->update($radiology_request_item_image_id, $dt_radiology_request_item_image);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_radiology_request_item_image,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update radiology_request_item_image', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $radiology_request_item_image_id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function delete_delete($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = "Radiology_request_image";
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

        $radiology_request_item_image_id = $this->$model->select_id($id);

        if ($radiology_request_item_image_id == "") {
            $this->REST_Return(404, 'there is no radiology_request_item_image_id with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_radiology_request_item_image = array(
            'is_delete' => $is_delete,
        );

        $id = $this->$model->update($radiology_request_item_image_id, $dt_radiology_request_item_image);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_radiology_request_item_image,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update radiology_request_item_image', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
    
    private function base64ToFile ($p = 0, $filename = '') {
        define('UPLOAD_DIR', 'assets/document/usg/');
        $data         = base64_decode($p);
        $file_name    = $filename; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name;
        $success      = file_put_contents($file, $data);
        return $file;
    }
}
