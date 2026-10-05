<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Doctor_schedule_item extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/mr/Appointment_schedule_model", "Appointment_schedule");
        // Load Helper 
        $this->load->helper('queue');

        $this->update_rules = array(
            array('field' => 'day', 'label' => 'day', 'rules' => 'trim|max_length[1]'),
            array('field' => 'hour_start', 'label' => 'hour_start', 'rules' => 'trim'),
            array('field' => 'hour_end', 'label' => 'hour_end', 'rules' => 'trim'),
            array('field' => 'quota', 'label' => 'quota', 'rules' => 'trim|max_length[6]'),
            array('field' => 'quota_bo', 'label' => 'quota_bo', 'rules' => 'trim|max_length[6]'),
            array('field' => 'quota_jkn', 'label' => 'quota_jkn', 'rules' => 'trim|max_length[6]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Doctor_schedule_item API
     * --------------------
     * @param: doctor_schedule_id
     * @param: doctor_schedule_items
     * --------------------
     * @method : POST
     * @link: version/mr/Doctor_schedule_item
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
            $this->form_validation->set_rules('doctor_schedule_id', 'doctor_schedule_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_schedule_items[]', 'doctor_schedule_items', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                // Check doctor_schedule_id
                if($this->Employee->read($dt_post['doctor_schedule_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    for ($i=0; $i < count($dt_post['doctor_schedule_items']); $i++) { 
                        $dt_doctor_schedule_item = array(
                            'doctor_schedule_id'        => $dt_post['doctor_schedule_id'],
                            'day'                       => isset($dt_post['doctor_schedule_items'][$i]['day']) ? $dt_post['doctor_schedule_items'][$i]['day'] : '',
                            'hour_start'                => isset($dt_post['doctor_schedule_items'][$i]['hour_start']) ? $dt_post['doctor_schedule_items'][$i]['hour_start'] : '',
                            'hour_end'                  => isset($dt_post['doctor_schedule_items'][$i]['hour_end']) ? $dt_post['doctor_schedule_items'][$i]['hour_end'] : '',
                            'quota'                     => isset($dt_post['doctor_schedule_items'][$i]['quota']) ? $dt_post['doctor_schedule_items'][$i]['quota'] : '',
                            'quota_bo'                  => isset($dt_post['doctor_schedule_items'][$i]['quota_bo']) ? $dt_post['doctor_schedule_items'][$i]['quota_bo'] : '',
                            'quota_jkn'                 => isset($dt_post['doctor_schedule_items'][$i]['quota_jkn']) ? $dt_post['doctor_schedule_items'][$i]['quota_jkn'] : '',
                            'date_added'                => date("Y-m-d H:i:s", time()),
                        );
                        $id = $this->$model->create($dt_doctor_schedule_item);
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
                    $this->REST_Return(404, 'Wrong doctor_schedule_id!');
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
     * GET Doctor_schedule_item with API
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
            $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            if (!$q_strings['doctor_id']) {
                for ($x=0; $x < count($output); $x++) { 
                    if ($output[$x]->day == 1){
                        $date = date('Y-m-d', strtotime('next Monday'));
                    } else if ($output[$x]->day == 2){
                        $date = date('Y-m-d', strtotime('next Tuesday'));
                    } else if ($output[$x]->day == 3){
                        $date = date('Y-m-d', strtotime('next Wednesday'));
                    } else if ($output[$x]->day == 4){
                        $date = date('Y-m-d', strtotime('next Thursday'));
                    } else if ($output[$x]->day == 5){
                        $date = date('Y-m-d', strtotime('next Friday'));
                    } else if ($output[$x]->day == 6){
                        $date = date('Y-m-d', strtotime('next Saturday'));
                    } else if ($output[$x]->day == 7){
                        $date = date('Y-m-d', strtotime('next Sunday'));
                    }
                    $output_vclaim = array();
                    if ($date) {
                        $this->load->library('Vclaim_antrol_compress','vclaim_antrol_compress');
                        $output_vclaim = $this->vclaim_antrol_compress->request("/jadwaldokter/kodepoli/".$output[$x]->bride_bpjs_poli."/tanggal/".$date, "GET");
                        if ($output_vclaim) {
                            $output_vclaim = json_decode($output_vclaim, true);
                        }
                    }
                    $output[$x]->vclaim = $output_vclaim;
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
            $this->form_validation->set_rules($this->update_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if(count($this->$model->read($id)) == 1) {
                    $want_to_update = array(
                        'id' => $id,
                    );
					foreach ($dt_put as $key => $value) {
                        if ($key != 'code') {
                            $want_to_update[$key] = $value;
                        }
					}
                    $id = $this->$model->update($id, $want_to_update);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
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

	public function update_hfis_put($p = 0) {
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
            $this->form_validation->set_rules($this->update_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $this->load->library('Vclaim_antrol','vclaim_antrol');
                $this->load->model("v1/medical_support/Doctor_schedule_item_model", "Doctor_schedule_item");
                $this->Doctor_schedule_item->whereFields['d.bride_bpjs'] = $dt_put['bride_bpjs'];
                $this->Doctor_schedule_item->whereFields['a.is_delete'] = 0;
                $data = $this->Doctor_schedule_item->read(0,false);
                if($data){
                    echo '<pre>';
                    $jadwal = array();
                    for ($i=0; $i < count($data); $i++) { 
                        $jadwal[$i] = array(
                            'hari' => $data[$i]->day,
                            'buka' => preg_replace('/(\d{2}:\d{2}):00/', '${1}', $data[$i]->hour_start),
                            'tutup' => preg_replace('/(\d{2}:\d{2}):00/', '${1}', $data[$i]->hour_end)
                        );
                    }
                    $DataPost = array(
                        "kodepoli"          => $data[0]->bride_bpjs_poli,
                        "kodesubspesialis"  => $data[0]->bride_bpjsspecialist,
                        "kodedokter"        => $data[0]->bride_bpjs,
                        "jadwal"            => $jadwal
                    );
                    
                    echo '<pre>';
                    var_dump($DataPost);
                    echo '</pre>';
                    var_dump($d);die();
                    $hasil = $this->vclaim_antrol->request("/jadwaldokter/updatejadwaldokter", "POST", json_encode($DataPost));
                    $d = json_decode($hasil, true);
                    $DataGET = array(['noSEP' => $d->response,'code' => $d->metaData->code,'message' => $d->metaData->message]);
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
                    $this->REST_Return(200, 'SUCCESS', $DataGET);
                }
                return;     
            }
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
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
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

    public function quota_bo_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            // Check id
            if($id == '0' || !isset($q_strings['date_appointment'])) {
                $this->REST_Return(404, 'id or date_appointment is missing!');
                return;
            }
            $output = $this->$model->read($id);
            if($output != []) {
                // Queue time interval
                $this->Appointment_schedule->whereFields['a.doctor_schedule_item_id'] = $output[0]->id;
                $this->Appointment_schedule->whereFields['b.date_appointment'] = $q_strings['date_appointment'];
                $appointment_schedule = $this->Appointment_schedule->read();
                $queue_time = queue_time_interval($appointment_schedule, $output[0]->hour_start, $output[0]->hour_end, $output[0]->quota, $output[0]->quota_bo, $q_strings['date_appointment']);
                
                $output[0]->minutes_long = $queue_time['minutes_long'];
                $output[0]->date_appointment = $q_strings['date_appointment'];
                $output[0]->queue = $queue_time['queue'];

                $error = false;
            } else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }     
            $response_data = $output[0];
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

    
}