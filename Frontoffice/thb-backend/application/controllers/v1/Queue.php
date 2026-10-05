<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Queue extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/Queue_model", "Queue");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/medical_support/Doctor_schedule_item_model", "Doctor_schedule_item");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
    }

    /**
     * GET Queue with API
     * -------------------------
     * @method: GET
     */

     public function detail_get($p = 0) {
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        header("Access-Control-Allow-Origin: *");
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['health_facility_item_id']) && isset($q_strings['doctor_in_charge_id']) && isset($q_strings['doctor_schedule_item_id'])) {
                // Check health_facility_item_id & doctor_in_charge_id & doctor_schedule_item_id
                if($this->Health_facility_item->read($q_strings['health_facility_item_id']) != [] && $this->Employee->read($q_strings['doctor_in_charge_id']) != [] && $this->Doctor_schedule_item->read($q_strings['doctor_schedule_item_id']) != []) {
                    $output = $this->Queue->display_queue($q_strings['health_facility_item_id'],$q_strings['doctor_in_charge_id'],$q_strings['doctor_schedule_item_id']);
                    if($output == []) {
                        $this->REST_Return(404, 'No Queue!');
                        return;    
                    }
                } else {
                    $this->REST_Return(404, 'Wrong health_facility_item_id or doctor_in_charge_id or doctor_schedule_item_id!');
                    return;
                }
            } else {
                $this->REST_Return(400, 'Health_facility_item_id & Doctor_in_charge_id & Doctor_schedule_item_id is required.');
                return;
            }
            $response_data = $output[0];
            $error   = false;
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

    /**
     * GET Queue with API
     * -------------------------
     * @method: GET
     */

     public function get_number_get($p = 0) {
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        header("Access-Control-Allow-Origin: *");
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['health_facility_item_id']) && isset($q_strings['doctor_in_charge_id']) && isset($q_strings['doctor_schedule_item_id'])) {
                // Check health_facility_item_id & doctor_in_charge_id & doctor_schedule_item_id
                if($this->Health_facility_item->read($q_strings['health_facility_item_id']) != [] && $this->Employee->read($q_strings['doctor_in_charge_id']) != [] && $this->Doctor_schedule_item->read($q_strings['doctor_schedule_item_id']) != []) {
                    $output = $this->Queue->get_queue_number($q_strings['health_facility_item_id'],$q_strings['doctor_in_charge_id'],$q_strings['doctor_schedule_item_id']);
                } else {
                    $this->REST_Return(404, 'Wrong health_facility_item_id or doctor_in_charge_id or doctor_schedule_item_id!');
                    return;
                }
            } else {
                $this->REST_Return(400, 'Health_facility_item_id & Doctor_in_charge_id & Doctor_schedule_item_id is required.');
                return;
            }
            $response_data = $output;
            $error   = false;
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

    /**
     * GET Queue with API
     * -------------------------
     * @method: GET
     */

     public function next_put($p = 0) {
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        header("Access-Control-Allow-Origin: *");
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['health_facility_item_id']) && isset($q_strings['doctor_in_charge_id']) && isset($q_strings['doctor_schedule_item_id'])) {
                // Check health_facility_item_id & doctor_in_charge_id
                if($this->Health_facility_item->read($q_strings['health_facility_item_id']) != [] && $this->Employee->read($q_strings['doctor_in_charge_id']) != [] && $this->Doctor_schedule_item->read($q_strings['doctor_schedule_item_id']) != []) {
                    $output = $this->Queue->next_queue($q_strings['health_facility_item_id'],$q_strings['doctor_in_charge_id'],$q_strings['doctor_schedule_item_id']);
                } else {
                    $this->REST_Return(404, 'Wrong health_facility_item_id doctor_in_charge_id or doctor_schedule_item_id!');
                    return;
                }
            } else {
                $this->REST_Return(400, 'Health_facility_item_id & Doctor_in_charge_id & Doctor_schedule_item_id is required.');
                return;
            }
            $response_data = $output;
            $error   = false;
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