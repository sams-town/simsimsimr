<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Writer\Ods\Thumbnails;
use Restserver\Libraries\REST_Controller;
class Nurse_schedule extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/{$this->name}_request_model", "{$this->name}_request");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/hr/Employee_model", "Employee");
        // Load Helper
        $this->load->helper("utils");

        $this->create_rules = array(
            array('field' => 'coordinator_id', 'label' => 'coordinator_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );

        $this->request_item_rules = array(
            array('field' => 'nurse_schedule_id', 'label' => 'nurse_schedule_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'reason', 'label' => 'reason', 'rules' => 'trim|max_length[250]'),
        );

        $this->update_item_rules = array(
            array('field' => 'nurse_schedule_id', 'label' => 'nurse_schedule_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
        );

        $this->approve_request_rules = array(
            array('field' => 'is_approve', 'label' => 'is_approve', 'rules' => 'trim|max_length[1]'),
        );

        $this->refuse_request_rules = array(
            array('field' => 'is_refuse', 'label' => 'is_refuse', 'rules' => 'trim|max_length[1]'),
        );

        $this->validate_rules = array(
            array('field' => 'is_validate', 'label' => 'is_validate', 'rules' => 'trim|max_length[1]'),
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

        $nurse_schedule_id = $id;

        if ($nurse_schedule_id === 0 || $nurse_schedule_id === "0") {
            $this->REST_Return(400, 'nurse_schedule_id is required');
            return;
        }

        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($nurse_schedule_id);
        $response_data = array();

        if (count($output) > 0) {
            $date = $output[0]->date;

            $year = date('Y', strtotime($date));
            $month = date('m', strtotime($date));
            $days = date('t', strtotime($date));
            
            $items = [];

            for ($i=1; $i <= $days; $i++) { 
                $date_item = "$year-$month-" . sprintf("%02d", $i);
                
                $item = [
                    'date' => $date_item,
                    'morning_nurses' => [],
                    'afternoon_nurses' => [],
                    'night_nurses' => [],
                ];

                $select = 'a.*, e.fullname as nurse_name';
                $where = array(
                    'nurse_schedule_id' => $nurse_schedule_id,
                    'date' => $date_item,
                );
                $join = array(
                    array('employee e', 'a.nurse_id = e.id', 'INNER'),
                );
                $nurse_schedule_item = $this->Nurse_schedule_item->select(0, $select, $where, $join);

                for ($j=0; $j < count($nurse_schedule_item); $j++) { 
                    $shift_type_id = $nurse_schedule_item[$j]->shift_type_id;

                    if ($shift_type_id == 1) {
                        array_push($item['morning_nurses'], $nurse_schedule_item[$j]);
                    } elseif ($shift_type_id == 2) {
                        array_push($item['afternoon_nurses'], $nurse_schedule_item[$j]);
                    } elseif ($shift_type_id == 3) {
                        array_push($item['night_nurses'], $nurse_schedule_item[$j]);
                    }
                }

                array_push($items, $item);
            }

            $output[0]->items = $items;
        }

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

        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 
        
        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }

        $where = [];

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;

        if ($health_facility_item_id != null) {
            $where['a.health_facility_item_id'] = $health_facility_item_id;
        }

        $select = 'a.*, hfi.name as health_facility_item_name';
        $where['a.is_delete'] = 0;
        $join = array(
            array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
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

        $coordinator_id = $dt_post['coordinator_id'];
        $health_facility_item_id = $dt_post['health_facility_item_id'];
        $date = $dt_post['date'];

        $select = 'a.id';
        $employee = $this->Employee->select($coordinator_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no coordinator with id ' . $coordinator_id);
            return;
        }

        $select = 'a.id, a.name';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $health_facility_item_name = $health_facility_item[0]->name;

        $select = 'a.id';
        $where = array(
            'a.health_facility_item_id' => $health_facility_item_id,
            'a.date' => $date,
        );
        $nurse_schedule = $this->$model->select(0, $select, $where);

        $month = date('F', strtotime($date));

        if (count($nurse_schedule) > 0) {
            $this->REST_Return(409, "$month's nurse schedule for $health_facility_item_name is already exist");
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_schedule = array(
            'coordinator_id'            => $coordinator_id,
            'health_facility_item_id'   => $health_facility_item_id,
            'date'                      => $date,
            'date_added'                => date("Y-m-d H:i:s"),
            'date_updated'              => date("Y-m-d H:i:s"),
            'user_added'                => $user_id,
        );

        $id = $this->$model->create($dt_nurse_schedule);        

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_schedule,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_schedule', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_item_put() {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
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
        $this->form_validation->set_rules($this->update_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_schedule_id = $dt_put['nurse_schedule_id'];
        $date = $dt_put['date'];
        $items = $dt_put['items'];

        $select = 'a.id, a.is_validate';
        $nurse_schedule = $this->Nurse_schedule->select($nurse_schedule_id, $select);
        if (count($nurse_schedule) == 0) {
            $this->REST_Return(404, 'there is no nurse_schedule with id ' . $nurse_schedule_id);
            return;
        }

        $is_validate = $nurse_schedule[0]->is_validate;

        $now = time();
        $date_morning_str = $date . ' 07:00:00';
        $date_afternoon_str = $date . ' 14:00:00';
        $date_night_str = $date . ' 21:00:00';

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($items); $i++) { 
            $nurse_id = clean_postput($items[$i]['nurse_id']);
            $nurse_subs_id = clean_postput($items[$i]['nurse_subs_id']);
            $shift_type_id = $items[$i]['shift_type_id'] ?? null;
            $role_type_id = $items[$i]['role_type_id'] ?? null;
            $status = $items[$i]['status'] ?? null;
            $status_subs = clean_postput($items[$i]['status_subs']);
            $reason = clean_postput($items[$i]['reason']);

            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id in items is required');
                return;
            }

            if ($shift_type_id == null) {
                $this->REST_Return(400, 'shift_type_id in items is required');
                return;
            }

            if ($role_type_id == null) {
                $this->REST_Return(400, 'role_type_id in items is required');
                return;
            }

            if ($status == null) {
                $this->REST_Return(400, 'status in items is required');
                return;
            }

            if ($is_validate == 1) {
                if ($shift_type_id == 1) {
                    $date_str = $date_morning_str;
                } elseif ($shift_type_id == 2) {
                    $date_str = $date_afternoon_str;
                } elseif ($shift_type_id == 3) {
                    $date_str = $date_night_str;
                }
    
                $date_subtract = date('Y-m-d H:i:s', strtotime($date_str . ' -24 hours'));
                if ($status == 2) {
                    $date_subtract = date('Y-m-d H:i:s', strtotime($date_str . ' -2 hours'));
                }
    
                $date_subtract_timestamp = strtotime($date_subtract);
                if ($now > $date_subtract_timestamp) {
                    $this->REST_Return(422, 'Tidak bisa mengubah jadwal, sudah melewati batas waktu');
                    return;
                }

                if ($reason == null) {
                    $this->REST_Return(400, 'Alasan wajib jika jadwal sudah di-validasi');
                    return;
                }
            }

            // Sakit
            if ($status == 4) {
                if ($nurse_subs_id == null) {
                    $this->REST_Return(400, 'nurse_subs_id in items is required if status is 4');
                    return;
                }

                if ($status_subs == null) {
                    $this->REST_Return(400, 'status_subs in items is required if status is 4');
                    return;
                }

                $select = 'a.id';
                $employee = $this->Employee->select($nurse_subs_id, $select);
                if (count($employee) == 0) {
                    $this->REST_Return(404, 'there is no nurse_subs with id ' . $nurse_subs_id);
                    return;
                }
            }

            $select = 'a.id';
            $employee = $this->Employee->select($nurse_id, $select);
            if (count($employee) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }

            $dt_nurse_schedule_item = array(
                'nurse_schedule_id' => $nurse_schedule_id,
                'nurse_id'          => $nurse_id,
                'nurse_subs_id'     => $nurse_subs_id,
                'date'              => $date,
                'shift_type_id'     => $shift_type_id,
                'role_type_id'      => $role_type_id,
                'status'            => $status,
                'status_subs'       => $status_subs,
                'reason'            => $reason,
                'date_added'        => date("Y-m-d H:i:s"),
                'date_updated'      => date("Y-m-d H:i:s"),
                'user_added'        => $user_id,
            );

            $select = 'a.id';
            $where = array(
                'nurse_schedule_id' => $nurse_schedule_id,
                'date' => $date,
                'nurse_id' => $nurse_id,
                'shift_type_id' => $shift_type_id,
            );
            $nurse_schedule_item = $this->Nurse_schedule_item->select(0, $select, $where);
            if (count($nurse_schedule_item) == 0) {
                $this->Nurse_schedule_item->create($dt_nurse_schedule_item);
            } else {
                $nurse_schedule_item_id = $nurse_schedule_item[0]->id;

                unset($dt_nurse_schedule_item['date_added']);
                unset($dt_nurse_schedule_item['user_added']);
                $dt_nurse_schedule_item['user_updated'] = $user_id;

                $this->Nurse_schedule_item->update_v2($nurse_schedule_item_id, $dt_nurse_schedule_item);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_schedule_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_schedule_item', $response_data);
            return;
        }

        $response_data = array(
            'id' => $nurse_schedule_id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function validate_put($p = 0) {
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
        $this->form_validation->set_rules($this->validate_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_schedule_id = $id;

        if ($nurse_schedule_id === 0 || $nurse_schedule_id === "0") {
            $this->REST_Return(400, 'nurse_schedule_id is required');
            return;
        }

        $select = 'a.id, a.is_validate';
        $nurse_schedule = $this->Nurse_schedule->select($nurse_schedule_id, $select);
        if (count($nurse_schedule) == 0) {
            $this->REST_Return(404, 'there is no nurse_schedule with id ' . $nurse_schedule_id);
            return;
        }

        $is_validate = $nurse_schedule[0]->is_validate;

        if ($is_validate == 1) {
            $this->REST_Return(409, 'Jadwal jaga sudah ter-validasi');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_schedule = array(
            'is_validate' => 1,
            'date_updated' => date('Y-m-d H:i:s'),
            'user_updated' => $user_id,
        );

        $this->Nurse_schedule->update_v2($nurse_schedule_id, $dt_nurse_schedule);

        // TODO - send notif perawat

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_schedule,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to validate nurse_schedule', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function request_item_post() {
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
        $this->form_validation->set_rules($this->request_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_schedule_id = $dt_post['nurse_schedule_id'];
        $nurse_schedule_item_id = clean_postput($dt_post['nurse_schedule_item_id']);
        $nurse_schedule_item_subs_id = clean_postput($dt_post['nurse_schedule_item_subs_id']);
        $nurse_id = clean_postput($dt_post['nurse_id']);
        $status = $dt_post['status'];
        $shift_type_id = $dt_post['shift_type_id'] ?? null;
        $role_type_id = $dt_post['role_type_id'] ?? null;
        $date = $dt_post['date'] ?? null;
        $reason = $dt_post['reason'] ?? null;

        if ($reason == null) {
            $this->REST_Return(400, 'Alasan wajib di-isi');
            return;
        }

        $select = 'a.id, u.id as coordinator_user_id';
        $where = array();
        $join = array(
            array('employee e', 'a.coordinator_id = e.id', 'INNER'),
            array('user u', 'a.coordinator_id = u.employee_id', 'INNER'),
        );
        $nurse_schedule = $this->Nurse_schedule->select($nurse_schedule_id, $select, $where, $join);
        if (count($nurse_schedule) == 0) {
            $this->REST_Return(404, 'there is no nurse_schedule with id ' . $nurse_schedule_id);
            return;
        }

        $coordinator_user_id = $nurse_schedule[0]->coordinator_user_id;
        
        // Ganti jadwal
        if ($status == 3 ) {
            if ($nurse_schedule_item_id == null) {
                $this->REST_Return(400, 'nurse_schedule_item_id is required if status is 3');
                return;
            }

            if ($nurse_schedule_item_subs_id == null) {
                $this->REST_Return(400, 'nurse_schedule_item_subs_id is required if status is 3');
                return;
            }

            $select = 'a.id, a.date, a.shift_type_id, e.fullname as nurse_name';
            $where = array(
                'a.nurse_schedule_id' => $nurse_schedule_id,
            );
            $join = array(
                array('employee e', 'a.nurse_id = e.id', 'INNER'),
            );
            $nurse_schedule_item = $this->Nurse_schedule_item->select($nurse_schedule_item_id, $select, $where, $join);
            if (count($nurse_schedule_item) == 0) {
                $this->REST_Return(404, 'there is no nurse_schedule_item with id ' . $nurse_schedule_item_id);
                return;
            }

            $nurse_ask = $nurse_schedule_item[0]->nurse_name;
            $date_ask = date('Y-m-d', strtotime($nurse_schedule_item[0]->date));
            $shift_ask = $nurse_schedule_item[0]->shift_type_id;

            if ($shift_ask == 1) {
                $shift_ask = 'Pagi';
            } elseif ($shift_ask == 2) {
                $shift_ask = 'Sore';
            } elseif ($shift_ask == 3) {
                $shift_ask = 'Malam';
            }

            $select = 'a.id, a.date, a.shift_type_id, e.fullname as nurse_name';
            $where = array(
                'a.nurse_schedule_id' => $nurse_schedule_id,
            );
            $join = array(
                array('employee e', 'a.nurse_id = e.id', 'INNER'),
            );
            $nurse_schedule_item_subs = $this->Nurse_schedule_item->select($nurse_schedule_item_subs_id, $select, $where, $join);
            if (count($nurse_schedule_item_subs) == 0) {
                $this->REST_Return(404, 'there is no nurse_schedule_item_subs with id ' . $nurse_schedule_item_subs_id);
                return;
            }

            $nurse_subs = $nurse_schedule_item_subs[0]->nurse_name;
            $date_subs = date('Y-m-d', strtotime($nurse_schedule_item_subs[0]->date));
            $shift_subs = $nurse_schedule_item_subs[0]->shift_type_id;

            if ($shift_subs == 1) {
                $shift_subs = 'Pagi';
            } elseif ($shift_subs == 2) {
                $shift_subs = 'Sore';
            } elseif ($shift_subs == 3) {
                $shift_subs = 'Malam';
            }
        } elseif ($status == 2) { // Overtime
            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id is required if overtime');
                return;
            }
            
            // Overtime pilih waktu Overtime Pagi/Sore/Malam
            if ($shift_type_id == null) {
                $this->REST_Return(400, 'shift_type_id is required if overtime');
                return;
            }

            if ($role_type_id == null) {
                $this->REST_Return(400, 'role_type_id is required if overtime');
                return;
            }

            if ($date == null) {
                $this->REST_Return(400, 'date is required if overtime');
                return;
            }

            $select = 'a.id, a.fullname';
            $employee = $this->Employee->select($nurse_id, $select);
            if (count($employee) == 0) {
                $this->REST_Return(404, 'there is no nurse with id ' . $nurse_id);
                return;
            }

            $nurse_name = $employee[0]->fullname;

            if ($shift_type_id == 1) {
                $shift_text = 'Pagi';
            } elseif ($shift_type_id == 2) {
                $shift_text = 'Sore';
            } elseif ($shift_type_id == 3) {
                $shift_text = 'Malam';
            }
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_schedule_request = array(
            'nurse_schedule_id'             => $nurse_schedule_id,
            'nurse_schedule_item_id'        => $nurse_schedule_item_id,
            'nurse_schedule_item_subs_id'   => $nurse_schedule_item_subs_id,
            'nurse_id'                      => $nurse_id,
            'shift_type_id'                 => $shift_type_id,
            'role_type_id'                  => $role_type_id,
            'date'                          => $date,
            'status'                        => $status,
            'reason'                        => $reason,
            'date_added'                    => date("Y-m-d H:i:s"),
            'date_updated'                  => date("Y-m-d H:i:s"),
            'user_added'                    => $user_id,
        );

        $id = $this->Nurse_schedule_request->create($dt_nurse_schedule_request);

        if ($status == 3) {
            $title = 'Permintaan Jadwal Jaga';
            $message = "Perawat $nurse_ask meminta tukar jadwal pada tanggal $date_ask shift $shift_ask dengan tanggal $date_subs shift $shift_subs perawat $nurse_subs";
        } elseif ($status == 2) {
            $title = 'Permintaan Jadwal Jaga';
            $message = "Perawat $nurse_name meminta lembur pada tanggal $date shift $shift_text";
        }

        $this->Notifications->createNotification($coordinator_user_id, null, $title, $message, 'nurse_schedule_request', ['nurse_schedule_request_id' => $id]);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_schedule_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create nurse_schedule_request', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function approve_request_put($p = 0) {
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
        $this->form_validation->set_rules($this->approve_request_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_schedule_request_id = $id;

        if ($nurse_schedule_request_id === 0 || $nurse_schedule_request_id === "0") {
            $this->REST_Return(400, 'nurse_schedule_request_id is required');
            return;
        }

        $select = 'a.id, a.is_approve, u.id as coordinator_user_id, a.nurse_schedule_id, a.nurse_schedule_item_id, a.nurse_schedule_item_subs_id, a.nurse_id, a.shift_type_id, a.role_type_id, a.date, a.status';
        $where = array();
        $join = array(
            array('nurse_schedule ns', 'a.nurse_schedule_id = ns.id', 'INNER'),
            array('user u', 'ns.coordinator_id = u.employee_id', 'INNER'),
        );
        $nurse_schedule_request = $this->Nurse_schedule_request->select($nurse_schedule_request_id, $select, $where, $join);
        if (count($nurse_schedule_request) == 0) {
            $this->REST_Return(404, 'there is no nurse_schedule_request with id ' . $nurse_schedule_request_id);
            return;
        }

        $is_approve = $nurse_schedule_request[0]->is_approve;
        $coordinator_user_id = $nurse_schedule_request[0]->coordinator_user_id;
        $nurse_schedule_id = $nurse_schedule_request[0]->nurse_schedule_id;
        $nurse_schedule_item_id = $nurse_schedule_request[0]->nurse_schedule_item_id;
        $nurse_schedule_item_subs_id = $nurse_schedule_request[0]->nurse_schedule_item_subs_id;
        $nurse_id = $nurse_schedule_request[0]->nurse_id;
        $shift_type_id = $nurse_schedule_request[0]->shift_type_id;
        $role_type_id = $nurse_schedule_request[0]->role_type_id;
        $date = $nurse_schedule_request[0]->date;
        $status = $nurse_schedule_request[0]->status;

        if ($is_approve == 1) {
            $this->REST_Return(409, 'Permintaan ini sudah disetujui');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        if ($status == 3) {
            $select = 'a.id, a.nurse_id';
            $nurse_schedule_item = $this->Nurse_schedule_item->select($nurse_schedule_item_id, $select);
            $nurse_id = $nurse_schedule_item[0]->nurse_id;
    
            $select = 'a.id, a.nurse_id';
            $nurse_schedule_item_subs = $this->Nurse_schedule_item->select($nurse_schedule_item_subs_id, $select);
            $nurse_subs_id = $nurse_schedule_item_subs[0]->nurse_id;

            $dt_nurse_schedule_item = array(
                'nurse_subs_id' => $nurse_subs_id,
                'status' => $status,
                'date_updated' => date('Y-m-d H:i:s'),
                'user_updated' => $user_id,
            );

            $dt_nurse_schedule_item_subs = array(
                'nurse_subs_id' => $nurse_id,
                'status' => $status,
                'date_updated' => date('Y-m-d H:i:s'),
                'user_updated' => $user_id,
            );

            $this->Nurse_schedule_item->update_v2($nurse_schedule_item_id, $dt_nurse_schedule_item);
            $this->Nurse_schedule_item->update_v2($nurse_schedule_item_subs_id, $dt_nurse_schedule_item_subs);
        } elseif ($status == 2) {
            $dt_nurse_schedule_item = array(
                'nurse_schedule_id' => $nurse_schedule_id,
                'nurse_id'          => $nurse_id,
                'date'              => $date,
                'shift_type_id'     => $shift_type_id,
                'role_type_id'      => $role_type_id,
                'status'            => $status,
                'date_added'        => date("Y-m-d H:i:s"),
                'date_updated'      => date("Y-m-d H:i:s"),
                'user_added'        => $user_id,
            );

            $this->Nurse_schedule_item->create($dt_nurse_schedule_item);
        }

        $dt_nurse_schedule_request = array(
            'is_approve' => 1,
            'date_updated' => date('Y-m-d H:i:s'),
            'user_updated' => $user_id,
        );

        $this->Nurse_schedule_request->update_v2($nurse_schedule_request_id, $dt_nurse_schedule_request);

        $this->Notifications->readNotificationWithItem($coordinator_user_id, $nurse_schedule_request_id, 'nurse_schedule_request_id');

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_schedule_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_schedule_request', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function refuse_request_put($p = 0) {
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
        $this->form_validation->set_rules($this->refuse_request_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $nurse_schedule_request_id = $id;

        if ($nurse_schedule_request_id === 0 || $nurse_schedule_request_id === "0") {
            $this->REST_Return(400, 'nurse_schedule_request_id is required');
            return;
        }

        $select = 'a.id, a.is_refuse, u.id as coordinator_user_id';
        $where = array();
        $join = array(
            array('nurse_schedule ns', 'a.nurse_schedule_id = ns.id', 'INNER'),
            array('user u', 'ns.coordinator_id = u.employee_id', 'INNER'),
        );
        $nurse_schedule_request = $this->Nurse_schedule_request->select($nurse_schedule_request_id, $select, $where, $join);
        if (count($nurse_schedule_request) == 0) {
            $this->REST_Return(404, 'there is no nurse_schedule_request with id ' . $nurse_schedule_request_id);
            return;
        }

        $is_refuse = $nurse_schedule_request[0]->is_refuse;
        $coordinator_user_id = $nurse_schedule_request[0]->coordinator_user_id;

        if ($is_refuse == 1) {
            $this->REST_Return(409, 'Permintaan ini sudah ditolak');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_nurse_schedule_request = array(
            'is_refuse' => 1,
            'date_updated' => date('Y-m-d H:i:s'),
            'user_updated' => $user_id,
        );

        $this->Nurse_schedule_request->update_v2($nurse_schedule_request_id, $dt_nurse_schedule_request);

        $this->Notifications->readNotificationWithItem($coordinator_user_id, $nurse_schedule_request_id, 'nurse_schedule_request_id');

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_nurse_schedule_request,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update nurse_schedule_request', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }


}