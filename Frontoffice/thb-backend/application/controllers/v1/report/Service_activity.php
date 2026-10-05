<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Service_activity extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/Dashboard_model", "Dashboard");
    }

    /**
     * GET Service_activity with API
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
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $output = $this->Dashboard->get_service_activity($q_strings['date_start'],$q_strings['date_end']);
            } else {
                $this->REST_Return(400, 'date_start & date_end is required.');
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