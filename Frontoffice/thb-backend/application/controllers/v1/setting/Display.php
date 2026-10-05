<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Display extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_poli_model", "{$this->name}_poli");
        $this->load->model("v1/medical_support/Doctor_schedule_model", "Doctor_schedule");
        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Display API
     * --------------------
     * @param: health_facility_item_id
     * @param: costing_id
     * @param: service_class_id
     * @param: type_room_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/health_facility/room
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
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('display[]', 'display', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_display = array(
                    'institute_branch_id'  => $institute_branch_id,
                    'name'                 => $dt_post['name'],
                    'user_added'           => $user_id,
                    'date_added'           => date("Y-m-d H:i:s", time()),
                    // 'last_updated'      => date("Y-m-d H:i:s", time()),
                );
                $id = $this->$model->create($dt_display);
                // INIT_DISPLAY_POLI 
                for ($i=0; $i < count($dt_post['display']); $i++) { 
                    $this->Doctor_schedule->whereFields['a.health_facility_item_id'] = $dt_post['display'][$i]['health_facility_item_id'];
                    $this->Doctor_schedule->whereFields['a.doctor_id'] = $dt_post['display'][$i]['doctor_id'];
                    $this->Doctor_schedule->whereFields['a.is_delete'] = 0;
                    $dt_schedule = $this->Doctor_schedule->read(0,false); 
                    if ($dt_schedule) {
                        $dt_display_poli = array(
                            'display_id'        => $id,
                            'doctor_schedule_id'=> $dt_schedule[0]->id,
                            'user_added'        => $user_id,
                            'date_added'        => date('Y-m-d H:i:s',time())
                        );
                        $this->Display_poli->create($dt_display_poli);
                    }
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
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    /**
     * Patient API
     * --------------------
     * @param: idcard
     * @param: typeidcard
     * @param: alias
     * @param: f_title
     * @param: name
     * @param: b_title
     * @param: fullname
     * @param: birth_date
     * @param: birth_place
     * @param: address
     * @param: sex
     * @param: blood
     * @param: married_id
     * @param: profession_id
     * @param: citizenship_id
     * @param: religion_id
     * @param: last_edu_id
     * @param: phone
     * @param: email
     * @param: address_id
     * @param: patient_issue_id
     * --------------------
     * @method : POST
     * @link: version/mr/patient
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
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('name', 'name', 'trim|max_length[100]');
            $this->form_validation->set_rules('display[]', 'display', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $display = $this->$model->read($id);
                if($display != []) {
                    $dt_display = array(
                        'id'                    => $id,
                        'name'                  => isset($dt_put['name']) ? $dt_put['name'] : $display[0]->name,
                        'user_updated'          => $user_id,
                        'date_updated'          => date('Y-m-d H:i:s',time())
                    );
                    $this->$model->replace($dt_display);
                    for ($i=0; $i < count($dt_put['delete']); $i++) {
                        if ($dt_put['delete'][$i]['id'] != NULL) {
                            $this->Display_poli->whereFields['a.id'] = $dt_put['delete'][$i]['id'];
                            $this->Display_poli->whereFields['a.is_delete'] = 0;
                            $display_poli_dl = $this->Display_poli->read(0,false); 
                            if($display_poli_dl) {
                                $dt_display_poli_dl = array(
                                    'id'                => $display_poli_dl[0]->id,
                                    'is_delete'         => 1,
                                    'user_deleted'      => $user_id,
                                    'date_deleted'      => date('Y-m-d H:i:s',time())
                                );
                                $this->Display_poli->replace($dt_display_poli_dl);
                            }
                        }
                    }
                    for ($i=0; $i < count($dt_put['display']); $i++) {
                        if ($dt_put['display'][$i]['health_facility_item_id'] != NULL && $dt_put['display'][$i]['doctor_id'] != NULL) {
                            $this->Display_poli->whereFields['ds.health_facility_item_id'] = $dt_put['display'][$i]['health_facility_item_id'];
                            $this->Display_poli->whereFields['ds.doctor_id'] = $dt_put['display'][$i]['doctor_id'];
                            $this->Display_poli->whereFields['a.is_delete'] = 0;
                            $display_poli = $this->Display_poli->read(0,false); 
                            if($display_poli) {
                                $this->Doctor_schedule->whereFields['a.health_facility_item_id'] = $dt_put['display'][$i]['health_facility_item_id'];
                                $this->Doctor_schedule->whereFields['a.doctor_id'] = $dt_put['display'][$i]['doctor_id'];
                                $this->Doctor_schedule->whereFields['a.is_delete'] = 0;
                                $dt_schedule = $this->Doctor_schedule->read(0,false); 
                                if ($dt_schedule) {
                                    $dt_display_poli = array(
                                        'id'                => $display_poli[0]->id,
                                        'display_id'        => $id,
                                        'doctor_schedule_id'=> $dt_schedule[0]->id,
                                        'user_updated'      => $user_id,
                                        'date_updated'      => date('Y-m-d H:i:s',time())
                                    );
                                    $this->Display_poli->replace($dt_display_poli);
                                }
                            } else {
                                $this->Doctor_schedule->whereFields['a.health_facility_item_id'] = $dt_put['display'][$i]['health_facility_item_id'];
                                $this->Doctor_schedule->whereFields['a.doctor_id'] = $dt_put['display'][$i]['doctor_id'];
                                $this->Doctor_schedule->whereFields['a.is_delete'] = 0;
                                $dt_schedule = $this->Doctor_schedule->read(0,false); 
                                if ($dt_schedule) {
                                    $dt_display_poli = array(
                                        'display_id'        => $id,
                                        'doctor_schedule_id'=> $dt_schedule[0]->id,
                                        'user_added'        => $user_id,
                                        'date_added'        => date('Y-m-d H:i:s',time())
                                    );
                                    $this->Display_poli->create($dt_display_poli);
                                }
                            }
                        }
                    }
                } else {
                    $this->REST_Return(404, $this->name.' with id ' . $id . ' does not exist!');
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

    /**
     * GET Room with API
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $this->Display_poli->whereFields['a.display_id'] = $output[$i]->id;
                $this->Display_poli->whereFields['a.is_delete'] = '0';
                $DP = $this->Display_poli->read(0,false);
                if ($DP != []) {
                    for ($j=0; $j < count($DP); $j++) { 
                        $output[$i]->display[$j]['id'] = $DP[$j]->id;
                        $output[$i]->display[$j]['doctor_name'] = $DP[$j]->doctor_name;
                        $output[$i]->display[$j]['health_facility_item_name'] = $DP[$j]->health_facility_item_name;
                    }
                } 
            }
            if ($output != [] && $id != 0) {
                $response_data = $output;
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
            if($output) {
                for ($i=0; $i < count($output->rows); $i++) {
                    $this->Display_poli->whereFields['a.display_id'] = $output->rows[$i]->id;
                    $this->Display_poli->whereFields['a.is_delete'] = '0';
                    $DP = $this->Display_poli->read(0,false);
                    if ($DP != []) {
                        for ($j=0; $j < count($DP); $j++) { 
                            $output->rows[$i]->display[$j]['id'] = $DP[$j]->id;
                            $output->rows[$i]->display[$j]['doctor_name'] = $DP[$j]->doctor_name;
                            $output->rows[$i]->display[$j]['health_facility_item_name'] = $DP[$j]->health_facility_item_name;
                        }
                    } 
                }
            }
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
}