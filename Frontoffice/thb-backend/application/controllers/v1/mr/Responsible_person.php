<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Responsible_person extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
    }

    // /**
    //  * Responsible_person API
    //  * --------------------
    //  * @param: responsible_id
    //  * @param: idcard
    //  * @param: typeidcard
    //  * @param: name
    //  * @param: phone
    //  * @param: address_id
    //  * --------------------
    //  * @method : POST
    //  * @link: version/mr/responsible_person
    //  **/

    // public function create_post() {
    //     // CALL JSON POST
    //     $data       = $this->json_post();
    //     // DEFINE ATTR
    //     $dt_post    = $data['dt_postput'];
    //     $device     = $data['dt_server']['HTTP_USER_AGENT'];
    //     $ip         = $this->get_client_ip();
    //     $error      = TRUE;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         # Form Validation
    //         $this->form_validation->set_rules('responsible_id', 'responsible_id', 'trim|max_length[2]');
    //         $this->form_validation->set_rules('idcard', 'idcard', 'trim|max_length[20]');
    //         $this->form_validation->set_rules('typeidcard', 'typeidcard', 'trim|max_length[1]');
    //         $this->form_validation->set_rules('name', 'name', 'trim|max_length[255]');
    //         $this->form_validation->set_rules('phone', 'phone', 'trim|max_length[15]');
    //         $this->form_validation->set_rules('address_id', 'address_id', 'trim|max_length[30]');
    //         if ($this->form_validation->run() == FALSE){
    //             // Form Validation Errors
    //             $response_data = array(
    //                 'error'     => $this->form_validation->error_array()
    //             );
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             // Check institute_branch_id
    //             $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    //             $model = $this->name;
    //                 /* Start Transaction */
    //                 $this->db->trans_start();
    //                 $dt_responsible_person = array(
    //                     'responsible_id'    => $dt_post['responsible_id'],
    //                     'idcard'            => isset($dt_post['idcard']) ? $dt_post['idcard'] : '',
    //                     'typeidcard'        => isset($dt_post['typeidcard']) ? $dt_post['typeidcard'] : '',
    //                     'name'              => isset($dt_post['name']) ? $dt_post['name'] : '',
    //                     'phone'             => isset($dt_post['phone']) ? $dt_post['phone'] : '',
    //                     'address_id'        => isset($dt_post['address_id']) ? $dt_post['address_id'] : ''
    //                 );
    //                 $id = $this->$model->replace($dt_responsible_person);
    //                 $this->db->trans_complete();
    //                 /* End Transaction */
    //                 if ($this->db->trans_status() === false) {
    //                     $response_data = array(
    //                         'error'   => $this->db->trans_status()
    //                     );
    //                     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //                 } else {
    //                     $response_data = array(
    //                         'id'     => $id
    //                     );
    //                     $this->REST_Return(201, 'SUCCESS', $response_data);
    //                 }
    //             return;     
    //         }
    //     } else {
    //         $response_data = array(
    //             'error' => 'Invalid Token.'
    //         );
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
    // }

    /**
     * GET Responsible_person with API
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
            $output = $this->$model->read($id);
            $response_data = array();
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
}