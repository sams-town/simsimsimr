<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Patient_tasks extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Nurse_tasks_model", "Nurse_tasks");
        $this->load->model("v1/medical_support/Nurse_schedule_item_model", "Nurse_schedule_item");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/pharmacy/Kardex_model", "Kardex");
        $this->load->model("v1/pharmacy/Kardex_item_model", "Kardex_item");
        $this->load->model("v1/pharmacy/Kardex_non_pharmacological_model", "Kardex_non_pharmacological");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->check_rules = array(
            array('field' => 'patient_tasks_item_id', 'label' => 'patient_tasks_item_id', 'rules' => 'trim|required|max_length[36]'),
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

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $patient_tasks = $id;

        if ($patient_tasks === 0 || $patient_tasks === "0") {
            $this->REST_Return(400, 'patient_tasks is required');
            return;
        }

        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($patient_tasks);
        $response_data = array();

        if (count($output) > 0) {
            $patient_tasks_id = $output[0]->id;

            $select = 'a.*';
            $where = array(
                'a.patient_tasks_id' => $patient_tasks_id,
            );
            $patient_tasks_item = $this->Patient_tasks_item->select(0, $select, $where);
            

            $output[0]->items = $patient_tasks_item;
        }


        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function initiate_get($p = 0) {
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

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $norm = $q_strings['norm'] ?? null;

        $date = $q_strings['date'] ?? null;

        if ($date == null) {
            $date = date('Y-m-d');
        }

        $select = 'a.id';
        $where = array(
            'hfi.health_facility_id' => '40f06534-07ec-4221-ad9d-dea08196079f', // RAWAT INAP
            'a.is_checkout' => 0,
            'a.is_cancel' => 0,
        );
        if ($norm != null) {
            $where['r.norm'] = $norm;
        }
        $join = array(
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            array('registration r', 'a.registration_id = r.id', 'INNER'),
        );
        $registration_destination = $this->Registration_destination->select(0, $select, $where, $join);

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($registration_destination); $i++) { 
            $registration_destination_id = $registration_destination[$i]->id;

            $select = 'a.id';
            $where = array(
                'a.registration_destination_id' => $registration_destination_id,
                'a.date' => $date,
            );
            $patient_tasks = $this->Patient_tasks->select(0, $select, $where);

            if (count($patient_tasks) > 0) {
                continue;
            }

            $this->Patient_tasks->new_task($registration_destination_id, $date, $user_id);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to initiate patient tasks', $response_data);
            return;
        }

        $response_data = $registration_destination;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function check_put($p = 0) {
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
        $this->form_validation->set_rules($this->check_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $select = 'a.id, a.employee_id, e.fullname as nurse_name';
        $where = array();
        $join = array(
            array('employee e', 'a.employee_id = e.id', 'INNER'),
        );
        $user = $this->User->select($user_id, $select);

        $nurse_id = $user[0]->employee_id;
        $nurse_name = $user[0]->nurse_name;

        $patient_tasks_id = $id;

        if ($patient_tasks_id === 0 || $patient_tasks_id === "0") {
            $this->REST_Return(400, 'patient_tasks_id is required');
            return;
        }

        $select = 'a.id, a.date, a.registration_destination_id';
        $patient_tasks = $this->Patient_tasks->select($patient_tasks_id, $select);
        if (count($patient_tasks) == 0) {
            $this->REST_Return(404, 'there is no patient_tasks with id ' . $patient_tasks_id);
            return;
        }

        $date = $patient_tasks[0]->date;
        $registration_destination_id = $patient_tasks[0]->registration_destination_id;

        $patient_tasks_item_id = $dt_put['patient_tasks_item_id'];

        $select = 'a.id, a.status';
        $patient_tasks_item = $this->Patient_tasks_item->select($patient_tasks_item_id, $select);
        if (count($patient_tasks_item) == 0) {
            $this->REST_Return(404, 'there is no patient_tasks_item with id ' . $patient_tasks_item_id);
            return;
        }

        $status = $patient_tasks_item[0]->status;

        if ($status == 1) {
            $this->REST_Return(409, 'Item ini sudah ter-checklist');
            return;
        }

        $select = 'a.id';
        $where = array(
            'a.date' => $date,
            'a.nurse_id' => $nurse_id,
        );
        $join = array(
            array('nurse_schedule ns', 'a.nurse_schedule_id = ns.id', 'INNER'),
        );
        $nurse_schedule_item = $this->Nurse_schedule_item->select(0, $select, $where, $join);
        if (count($nurse_schedule_item) == 0) {
            $this->REST_Return(409, "Perawat $nurse_name tidak ada shift pada tanggal $date");
            return;
        }

        $nurse_schedule_item_id = $nurse_schedule_item[0]->id;

        $select = 'a.id';
        $where = array(
            'a.nurse_schedule_item_id' => $nurse_schedule_item_id,
            'a.registration_destination_id' => $registration_destination_id,
        );
        $nurse_tasks = $this->Nurse_tasks->select(0, $select, $where);
        if (count($nurse_tasks) == 0) {
            $this->REST_Return(409, "Pasien ini bukan merupakan tugas perawat $nurse_name");
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_patient_tasks_item = array(
            'nurse_id' => $nurse_id,
            'status' => 1,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );

        $this->Patient_tasks_item->update_v2($patient_tasks_item_id, $dt_patient_tasks_item);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_patient_tasks_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to check patient_tasks_item', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}