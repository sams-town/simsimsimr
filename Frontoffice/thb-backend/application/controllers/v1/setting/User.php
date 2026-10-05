<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class User extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/User_group_model", "user_group");
        $this->load->model("$this->directory/Setting_model", "Setting");
    }

    /**
     * User API
     * --------------------
     * @param: institute_branch_id
     * @param: employee_id
     * @param: username
     * @param: password
     * --------------------
     * @method : POST
     * @link: version/system/user
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
        $this->form_validation->set_rules('username', 'username', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('employee_id', 'employee_id', 'trim|required');
        $this->form_validation->set_rules('group_id', 'group_id', 'trim|required');
        $this->form_validation->set_rules('password', 'password', 'trim|required');
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $model = $this->name;
            $model_user_group ='user_group';
            /* Start Transaction */
            $this->db->trans_start();
            $this->$model->whereFields['a.employee_id'] = $dt_post['employee_id'];
            $check_exitst = $this->$model->read(0, false);
            if($check_exitst) {
                $this->REST_Return(404, 'duplicate employee with id ' . $dt_post['employee_id'] . ' ');
                return;
            } else {
                $dt_user = array(
                    'institute_branch_id'  => $institute_branch_id,
                    'employee_id'          => $dt_post['employee_id'],
                    'username'             => $dt_post['username'],
                    'password'             => sha1($dt_post['password']),
                    'date_added'           => date('Y-m-d H:i:s'),
                );
                $id_user = $this->$model->create($dt_user);
                $dt_usergroup = array(
                    'user_id'           => $id_user,
                    'group_id'          => $dt_post['group_id'],
                );
                $this->$model_user_group->replace($dt_usergroup);
            }
            $this->db->trans_complete();
            /* End Transaction */
            if ($this->db->trans_status() === false) {
                $response_data = $this->db->trans_status();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $response_data = array(
                    'id'     => $id_user
                );
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;     
        }
    }

    /**
     * GET User with API
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            if ($output || $output == [] && $id == '0') {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, 'User with id ' . $id . ' does not exist!');
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
     * User API
     * --------------------
     * @param: institute_branch_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/system/user
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
        $this->form_validation->set_rules('username', 'username', 'trim|required|max_length[100]');
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $model = $this->name;
            /* Start Transaction */
            $this->db->trans_start();
            if(count($this->$model->read($id)) == 1) {
                $dt_user = array(
                    'id'                   => $id,
                    'username'                 => $dt_put['username']
                );
                $id = $this->$model->update($id, $dt_user);
            } else {
                $this->REST_Return(404, 'User with id ' . $id . ' does not exist!');
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
     * User API
     * --------------------
     * @param: institute_branch_id
     * @param: is_delete
     * --------------------
     * @method : POST
     * @link: version/system/user
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
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            $model = $this->name;
            $model_user_group ='user_group';
            /* Start Transaction */
            $this->db->trans_start();
            if($this->$model->read($id) != []) {
                $dt_user = array(
                    'id'                   => $id,
                    'is_delete'            => $dt_put['is_delete']
                );
                $id = $this->$model_user_group->update($id, $dt_user);
            } else {
                $this->REST_Return(404, 'User with id ' . $id . ' does not exist!');
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

        $this->$model->whereLike = $q_strings;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $user_id = $output->rows[$i]->id;

            $select = 'a.*';
            $where = array(
                'a.user_id' => $user_id
            );
            $setting = $this->Setting->select(0, $select, $where);
            if (count($setting) == 0) {
                $dt_setting = array(
                'user_id' => $user_id
                );
                $this->Setting->create($dt_setting);
            }
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}