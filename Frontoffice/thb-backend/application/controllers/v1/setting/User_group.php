<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class User_Group extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
    }

    /**
     * Group API
     * --------------------
     * @param: institute_branch_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/system/user_group
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

        # Form Validation
        $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = array(
                'error'     => $this->form_validation->error_array()
            );
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $model = $this->name;
            /* Start Transaction */
            $this->db->trans_start();
            $dt_user_group = array(
                'institute_branch_id'  => $institute_branch_id,
                'name'                 => $dt_post['name']
            );
            $id = $this->$model->create($dt_user_group);
            $this->db->trans_complete();
            /* End Transaction */
            if ($this->db->trans_status() === false) {
                $response_data = array(
                    'error'   => $this->db->trans_status()
                );
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $response_data = array(
                    'id'     => $id
                );
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;     
        }
    }

    /**
     * GET Group with API
     * -------------------------
     * @method: GET
     */

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
        
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $this->$model->whereFields = $q_strings;
            $this->$model->whereFields['institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output || $output == [] && $id == '0') {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, 'Group with id ' . $id . ' does not exist!');
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


    /**
     * Group API
     * --------------------
     * @param: institute_branch_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/system/user_group
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
        # Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = array(
                'error'     => $this->form_validation->error_array()
            );
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $model = $this->name;
            /* Start Transaction */
            $this->db->trans_start();
            if(count($this->$model->read($id)) == 1) {
                $dt_user_group = array(
                    'id'                   => $id,
                    'name'                 => $dt_put['name']
                );
                $id = $this->$model->update($id, $dt_user_group);
            } else {
                $this->REST_Return(404, 'Group with id ' . $id . ' does not exist!');
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
    }

    /**
     * Group API
     * --------------------
     * @param: institute_branch_id
     * @param: is_delete
     * --------------------
     * @method : POST
     * @link: version/system/user_group
     **/

    public function delete_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        # Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules('is_delete', 'is_delete', 'trim|required|max_length[1]');
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = array(
                'error'     => $this->form_validation->error_array()
            );
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $model = $this->name;
            /* Start Transaction */
            $this->db->trans_start();
            if($this->$model->read($id) != []) {
                $dt_user_group = array(
                    'id'                   => $id,
                    'is_delete'            => $dt_put['is_delete']
                );
                $id = $this->$model->update($id, $dt_user_group);
            } else {
                $this->REST_Return(404, 'Group with id ' . $id . ' does not exist!');
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
    }
}