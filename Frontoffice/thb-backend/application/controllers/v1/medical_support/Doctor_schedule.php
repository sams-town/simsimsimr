<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Doctor_schedule extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Doctor_schedule API
     * --------------------
     * @param: doctor_id
     * @param: health_facility_item_id
     * @param: date_added
     * @param: doctor_schedule_items
     * --------------------
     * @method : POST
     * @link: version/mr/Doctor_schedule
     **/

     public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('doctor_id', 'doctor_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('health_facility_item_id', 'health_facility_item_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_schedule_items[]', 'doctor_schedule_items', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                // Check doctor_id and health_facility_item_id
                if($this->Employee->read($dt_post['doctor_id'], false) != [] && $this->Health_facility_item->read($dt_post['health_facility_item_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();

                    $dt_doctor_schedule = array(
                        'institute_branch_id'       => $institute_branch_id,
                        'doctor_id'                 => $dt_post['doctor_id'],
                        'health_facility_item_id'   => $dt_post['health_facility_item_id'],
                        'date_added'                => date("Y-m-d H:i:s", time()),
                    );
                    $id = $this->$model->create($dt_doctor_schedule);

                    // INIT_DOCTOR_SCHEDULE_ITEM
                    for ($i=0; $i < count($dt_post['doctor_schedule_items']); $i++) { 
                        $dt_doctor_schedule_item = array(
                            'doctor_schedule_id'        => $id,
                            'day'                       => isset($dt_post['doctor_schedule_items'][$i]['day']) ? $dt_post['doctor_schedule_items'][$i]['day'] : '',
                            'hour_start'                => isset($dt_post['doctor_schedule_items'][$i]['hour_start']) ? $dt_post['doctor_schedule_items'][$i]['hour_start'] : '',
                            'hour_end'                  => isset($dt_post['doctor_schedule_items'][$i]['hour_end']) ? $dt_post['doctor_schedule_items'][$i]['hour_end'] : '',
                            'quota'                     => isset($dt_post['doctor_schedule_items'][$i]['quota']) ? $dt_post['doctor_schedule_items'][$i]['quota'] : '',
                            'quota_bo'                  => isset($dt_post['doctor_schedule_items'][$i]['quota_bo']) ? $dt_post['doctor_schedule_items'][$i]['quota_bo'] : '',
                            'quota_jkn'                 => isset($dt_post['doctor_schedule_items'][$i]['quota_jkn']) ? $dt_post['doctor_schedule_items'][$i]['quota_jkn'] : '',
                            'date_added'                => date("Y-m-d H:i:s", time()),
                        );
                        $this->Doctor_schedule_item->create($dt_doctor_schedule_item);
                    }
                    $this->db->trans_complete();
                    /* End Transaction */
                    if ($this->db->trans_status() === false) {
                        $response_data = $this->db->trans_status();
                        $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    } else {
                        $response_data = array(
                            'id'     => $id
                        );
                        $this->REST_Return(201, 'SUCCESS', $response_data);
                    }
                } else {
                    $this->REST_Return(404, 'Wrong doctor_id or health_facility_item_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * GET Doctor_schedule with API
     * -------------------------
     * @method: GET
     */

     public function detail_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $this->Doctor_schedule_item->orderFields;
                $this->Doctor_schedule_item->orderFields = array('a.day' => 'ASC');
                $this->Doctor_schedule_item->whereFields['doctor_schedule_id'] = $output[$i]->id;
                $this->Doctor_schedule_item->whereFields['a.is_delete'] = '0';
                $doctor_schedule_item = $this->Doctor_schedule_item->read();
                for ($j=0; $j < count($doctor_schedule_item); $j++) { 
                    $output[$i]->doctor_schedule_items[$j]['id'] = $doctor_schedule_item[$j]->id;
                    $output[$i]->doctor_schedule_items[$j]['day'] = $doctor_schedule_item[$j]->day;
                    $output[$i]->doctor_schedule_items[$j]['day_name'] = $doctor_schedule_item[$j]->day_name;
                    $output[$i]->doctor_schedule_items[$j]['hour_start'] = $doctor_schedule_item[$j]->hour_start;
                    $output[$i]->doctor_schedule_items[$j]['hour_end'] = $doctor_schedule_item[$j]->hour_end;
                    $output[$i]->doctor_schedule_items[$j]['quota'] = $doctor_schedule_item[$j]->quota;
                    $output[$i]->doctor_schedule_items[$j]['quota_bo'] = $doctor_schedule_item[$j]->quota_bo;
                    $output[$i]->doctor_schedule_items[$j]['quota_jkn'] = $doctor_schedule_item[$j]->quota_jkn;
                }
            }
            if ($output != [] && $id != 0) {
                $response_data = $output[0];
                $error   = false;
            } else if($output != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    public function list_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                $this->Doctor_schedule_item->orderFields;
                $this->Doctor_schedule_item->orderFields = array('a.day' => 'ASC');
                $this->Doctor_schedule_item->whereFields['doctor_schedule_id'] = $output->rows[$i]->id;
                $this->Doctor_schedule_item->whereFields['a.is_delete'] = '0';
                $doctor_schedule_item = $this->Doctor_schedule_item->read();
                for ($j=0; $j < count($doctor_schedule_item); $j++) { 
                    $output->rows[$i]->doctor_schedule_items[$j]['day'] = $doctor_schedule_item[$j]->day;
                    $output->rows[$i]->doctor_schedule_items[$j]['day_name'] = $doctor_schedule_item[$j]->day_name;
                    $output->rows[$i]->doctor_schedule_items[$j]['hour_start'] = $doctor_schedule_item[$j]->hour_start;
                    $output->rows[$i]->doctor_schedule_items[$j]['hour_end'] = $doctor_schedule_item[$j]->hour_end;
                    $output->rows[$i]->doctor_schedule_items[$j]['quota'] = $doctor_schedule_item[$j]->quota;
                    $output->rows[$i]->doctor_schedule_items[$j]['quota_bo'] = $doctor_schedule_item[$j]->quota_bo;
                    $output->rows[$i]->doctor_schedule_items[$j]['quota_jkn'] = $doctor_schedule_item[$j]->quota_jkn;
                }
            }
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
	}

    function day_to_hari ($hariInggris) {
        switch ($hariInggris) {
          case 'Sunday':
            return '7';
          case 'Monday':
            return '1';
          case 'Tuesday':
            return '2';
          case 'Wednesday':
            return '3';
          case 'Thursday':
            return '4';
          case 'Friday':
            return '5';
          case 'Saturday':
            return '6';
          default:
            return 'hari tidak valid';
        }
    }

    public function daily_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = "{$this->name}_item";
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        $where = $q_strings;

        if(isset($q_strings['date'])) {
            $date = $this->day_to_hari(date('l', strtotime($q_strings['date'])));
            $where['a.day'] = $date;
            unset($where['date']);
        }

        $where['c.health_facility_item_id !='] = 'd46e2eb6-85a9-4bd4-b515-a0858cc50c8f';
        $where['a.is_delete'] = 0;
    
        $select = 'a.*,b.name as day_name,c.code,c.doctor_id as doctor_id,d.fullname as doctor_name,d.bride_bpjs,
                    c.health_facility_item_id,e.name as health_facility_item_name,
                    e.health_facility_id,f.name as health_facility_name,e.bride_bpjs as bride_bpjs_poli,e.bride_bpjsspecialist';
        $join = array(
            array('helper b', 'a.day = b.code AND variable="day"', 'LEFT'),
            array('doctor_schedule c', 'a.doctor_schedule_id = c.id', 'LEFT'),
            array('employee d', 'c.doctor_id = d.id', 'LEFT'),
            array('health_facility_item e', 'c.health_facility_item_id = e.id', 'LEFT'),
            array('health_facility f', 'e.health_facility_id = f.id', 'LEFT'),
        );
        $order = array('a.hour_start' => 'ASC');
        $output = $this->$model->select(0, $select, $where, $join, $order);
        
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


    /**
     * Doctor_schedule API
     * --------------------
     * @param: doctor_id
     * @param: health_facility_item_id
     * --------------------
     * @method : POST
     * @link: version/mr/Doctor_schedule
     **/

    public function update_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('doctor_id', 'doctor_id', 'trim|required|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $doctor_schedule = $this->$model->read($id);
                if($doctor_schedule != []) {
                    // Check doctor_id and health_facility_item_id
                    if($this->Employee->read($dt_put['doctor_id'], false) != [] && $this->Health_facility_item->read($dt_put['health_facility_item_id'], false) != []) {
                        $dt_doctor_schedule = array(
                            'id'                            => $id,
                            'doctor_id'                     => isset($dt_put['doctor_id']) ? $dt_put['doctor_id'] : $doctor_schedule[0]->doctor_id,
                            'health_facility_item_id'       => isset($dt_put['health_facility_item_id']) ? $dt_put['health_facility_item_id'] : $doctor_schedule[0]->health_facility_item_id,
                        );
                        $id = $this->$model->update($id, $dt_doctor_schedule);
                    } else {
                        $this->REST_Return(404, 'Wrong doctor_id or health_facility_item_id!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Doctor_schedule with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}