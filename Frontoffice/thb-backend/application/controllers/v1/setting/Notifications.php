<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Notifications extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/User_model", "User");
        $this->load->model("v1/mr/Treatment_room_model", "Treatment_room");

        $this->update_rules = array(
            array('field' => 'id', 'label' => '', 'rules' => 'trim'),
        );
    }

    public function get_get($p = 0) {
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
               
        $status = $q_strings['status'] ?? 'all';
        $employee_id = $q_strings['employee_id'] ?? 10;
        $offset = $q_strings['offset'] ?? 0;
        $limit = $q_strings['limit'] ?? 20;

        if ($status == 'all') {
            $status = '';
        } elseif ($status == 'read') {
            $status = 'AND read_at IS NOT NULL';
        } elseif ($status == 'delivered') {
            $status = 'AND read_at IS NULL';
        }

        if ($employee_id != null) {
            $select = 'a.id';
            $where = array(
                'a.employee_id' => $employee_id,
            );
            $user = $this->User->select(0, $select, $where);
            if (count($user) > 0) {
                $user_id = $user[0]->id;
            }
        }

        $output = $this->db->query("
            SELECT *
            FROM notifications
            WHERE user_id = '$user_id'
            $status
            ORDER BY created_at DESC
            LIMIT $limit
            OFFSET $offset
        ")->result();

        for ($i=0; $i < count($output); $i++) { 
            $type = $output[$i]->type;
            $data = $output[$i]->data;

            if ($data != null) {
                $data_decode = json_decode($data);

                // if ($type == 'transfer') {
                //     $treatment_room_id = $data_decode->treatment_room_id;

                //     $select = 'a.id as treatment_room_id, a.bed_id, b.name as bed_name, ro.id as room_id, ro.name as room_name, a.from_unit, a.to_unit, hfi.name as from_unit_name, hfi2.name as to_unit_name, p.fullname as patient_name, h.name as alias_name';
                //     $where = array();
                //     $join = array(
                //         array('health_facility_item hfi', 'a.from_unit = hfi.id', 'INNER'),
                //         array('health_facility_item hfi2', 'a.to_unit = hfi2.id', 'INNER'),
                //         array('bed b', 'a.bed_id = b.id', 'INNER'),
                //         array('room ro', 'b.room_id = ro.id', 'INNER'),
                //         array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
                //         array('registration r', 'rd.registration_id = r.id', 'INNER'),
                //         array('patient p', 'r.norm = p.norm', 'INNER'),
                //         array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
                //     );
                //     $treatment_room = $this->Treatment_room->select($treatment_room_id, $select, $where, $join);
                    
                //     $data_decode = $treatment_room;
                // }

                $output[$i]->data = $data_decode;
            }
        }

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function read_put($p = 0) {
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
    
        $notification_id = $id;
    
        if ($notification_id === 0 || $notification_id === "0") {
            $this->REST_Return(400, 'notification_id is required');
            return;
        }
    
        $select = "a.id, a.read_at";
        $notification = $this->$model->select($notification_id, $select);
        if (count($notification) == 0) {
            $this->REST_Return(404, 'notification with id ' . $notification_id . ' does not exist!');
            return;
        }

        $read_at = $notification[0]->read_at;

        if ($read_at != null) {
            $this->REST_Return(409, 'this notification has been read');
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();

        $dt_notification = array(
            'read_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
    
        $st = $this->$model->update_v2($notification_id, $dt_notification);
        if ($st < 0) {
            $this->REST_Return(422, 'something when wrong');
            return;
        }

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_notification,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to read notification', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}