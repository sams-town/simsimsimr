<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nutrition extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Diet_model", "Diet");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/health_facility/Room_model", "Room");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_a_model", "Soap_a");
        $this->load->model("v1/mr/Soap_a_action_model", "Soap_a_action");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'room_id', 'label' => 'room_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'diet_id', 'label' => 'diet_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'time_type_id', 'label' => 'time_type_id', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'room_id', 'label' => 'room_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'diet_id', 'label' => 'diet_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );

        $this->approve_rules = array(
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|required|max_length[1]'),
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
    
        $nutrition_id = $id;
    
        if ($nutrition_id === 0 || $nutrition_id === "0") {
            $this->REST_Return(400, 'nutrition_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($nutrition_id);
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

        if (isset($q_strings['name'])) {
            $this->$model->whereLike['p.name'] = $q_strings['name'];
            unset($this->$model->whereFields['name']);
        }

        if (isset($q_strings['norm'])) {
            $this->$model->whereFields['r.norm'] = $q_strings['norm'];
            unset($this->$model->whereFields['norm']);
        }
        
        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $this->$model->whereFields['a.date_added >='] = $q_strings['date_start'].' 00:00:00';
            $this->$model->whereFields['a.date_added <='] = $q_strings['date_end'].' 23:59:59';
        }
        unset($this->$model->whereFields['date_start']);
        unset($this->$model->whereFields['date_end']);
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $registration_destination_id = $output->rows[$i]->registration_destination_id;
            
            $select = 'a.id';
            $soap = $this->Soap->select(0, $select, array('a.registration_destination_id'=>$registration_destination_id,'a.is_initial_inpatient' => 1));

            // SOAP_A
            $output->rows[$i]->icd10 = [];

            if (count($soap) > 0) { 
                
                $select = 'a.id, a.icd10_id, a.index, a.is_delete, i.code as icd10_code, i.name as icd10_name';
                $where = array('sa.soap_id' => $soap[0]->id);
                $join = array(
                    array('soap_a sa', 'a.soap_a_id = sa.id', 'INNER'),
                    array('icd10 i', 'a.icd10_id = i.id', 'INNER'),
                );
                $soap_a_action = $this->Soap_a_action->select(0, $select, $where, $join, array(), array('a.icd10_id'));
    
                $icd10 = $soap_a_action;

                $output->rows[$i]->icd10 = $icd10;
            }
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
        $health_facility_item_id = $dt_post['health_facility_item_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];
        $room_id = $dt_post['room_id'];
        $diet_id = $dt_post['diet_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $select = 'a.id';
        $room = $this->Room->select($room_id, $select);
        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $room_id);
            return;
        }

        $select = 'a.id';
        $diet = $this->Diet->select($diet_id, $select);
        if (count($diet) == 0) {
            $this->REST_Return(404, 'there is no diet with id ' . $diet_id);
            return;
        }

        $time_arr = $dt_post['time_arr'];
        $date = $dt_post['date'];
        $delivery_time = $dt_post['delivery_time'];
        $notes = $dt_post['notes'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nutrition = array(
            'registration_destination_id'   => $registration_destination_id,
            'health_facility_item_id'       => $health_facility_item_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'room_id'                       => $room_id,
            'diet_id'                       => $diet_id,
            'date'                          => $date,
            'delivery_time'                 => $delivery_time,
            'notes'                         => $notes,
            'status'                        => 0,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        foreach ($time_arr as $val) {
            if ($val == 1 || $val == 2 || $val == 3) {
                $dt_nutrition['time_type_id'] = $val;
                $id = $this->$model->create($dt_nutrition);
            }
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutrition,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nutrition', $response_data);
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

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);

        $employee_approved_id = $user[0]->employee_id;
    
        $nutrition_id = $id;
    
        if ($nutrition_id === 0 || $nutrition_id === "0") {
            $this->REST_Return(400, 'nutrition_id is required');
            return;
        }

        $select = 'a.id';
        $nutrition = $this->$model->select($nutrition_id, $select);

        if (count($nutrition) == 0) {
            $this->REST_Return(404, 'there is no nutrition with id ' . $nutrition_id);
            return;
        }

        $employee_nurse_id = $dt_put['employee_nurse_id'];
        $room_id = $dt_put['room_id'];
        $diet_id = $dt_put['diet_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $select = 'a.id';
        $room = $this->Room->select($room_id, $select);
        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $room_id);
            return;
        }

        $select = 'a.id';
        $diet = $this->Diet->select($diet_id, $select);
        if (count($diet) == 0) {
            $this->REST_Return(404, 'there is no diet with id ' . $diet_id);
            return;
        }

        $time_type_id = $dt_put['time_type_id'];
        $date = $dt_put['date'];
        $delivery_time = $dt_put['delivery_time'];
        $notes = $dt_put['notes'];
        $status = $dt_put['status'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nutrition = array(
            'employee_nurse_id' => $employee_nurse_id,
            'time_type_id'      => $time_type_id,
            'room_id'           => $room_id,
            'diet_id'           => $diet_id,
            'date'              => $date,
            'delivery_time'     => $delivery_time,
            'notes'             => $notes,
            'status'            => $status,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );

        if ($status == 1) {
            $dt_nutrition['employee_approved_id'] = $employee_approved_id;
            $dt_nutrition['approved_date'] = date('Y-m-d H:i:s');
        }
    
        $this->$model->update($nutrition_id, $dt_nutrition);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutrition,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nutrition', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function approve_put($p = 0) {
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
        $this->form_validation->set_rules($this->approve_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $select = 'a.id, a.employee_id';
        $user = $this->User->select($user_id, $select);

        $employee_approved_id = $user[0]->employee_id;
    
        $nutrition_id = $id;
    
        if ($nutrition_id === 0 || $nutrition_id === "0") {
            $this->REST_Return(400, 'nutrition_id is required');
            return;
        }

        $select = 'a.id';
        $nutrition = $this->$model->select($nutrition_id, $select);

        if (count($nutrition) == 0) {
            $this->REST_Return(404, 'there is no nutrition with id ' . $nutrition_id);
            return;
        }

        $status = $dt_put['status'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_nutrition = array(
            'employee_approved_id'  => $employee_approved_id,
            'status'                => $status,
            'approved_date'         => date('Y-m-d H:i:s',time()),
            'user_updated'          => $user_id,
            'date_updated'          => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($nutrition_id, $dt_nutrition);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutrition,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nutrition', $response_data);
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
            $this->REST_Return(400, 'nutrition_id is required');
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $nutrition_id = $this->$model->select_id($id);
    
        if ($nutrition_id == "") {
            $this->REST_Return(404, 'there is no nutrition with id ' . $id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];
    
        $dt_nutrition = array(
            'user_updated' => $user_id,
            'is_delete' => $is_delete,
        );
    
        $this->$model->update($nutrition_id, $dt_nutrition);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nutrition,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete nutrition', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}