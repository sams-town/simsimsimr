<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Auth extends My_Controller {

    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        $this->load->model("v1/setting/User_model", "User");
        $this->load->model("v1/setting/User_history_model", "User_history");
        $this->load->model("v1/setting/Group_model", "Group");
        $this->load->model("v1/setting/User_group_model", "User_group");
        $this->load->model("v1/Institute_branch_model", "Institute_branch");
        $this->load->model("v1/hr/Employee_model", "Employee");
        // Load Model
    }

    /**
     * signin API
     * --------------------
     * @param: username
     * @param: password
     * --------------------
     * @method : POST
     * @link: version/authentication/auth/signin
     **/

    public function signin_post(){
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        # Form Validation
        $this->form_validation->set_rules('username', 'username', 'trim|required');
        $this->form_validation->set_rules('password', 'password', 'trim|required|max_length[25]');
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = array(
                'error'     => $this->form_validation->error_array()
            );
            $this->REST_Return(422, $response_data);
        } else {
            /* Start Transaction */
            $this->db->trans_start();
            $this->User->whereFields = array(
                'username' => stripslashes($dt_post['username']),
                'password' => sha1($dt_post['password'])
            );
            $user = $this->User->read($p = 0, $post = false);
            $this->db->trans_complete();
            if ($user){
                // if(password_verify($this->input->post('password', TRUE), $output[0]->password)){
                    // Load Authorization Token Library
                    $this->load->library('Authorization_Token');
                    // Generate Token
                    $token_data['id'] = $user[0]->id;
                    $token_data['institute_branch_id'] = $user[0]->institute_branch_id;
                    $token_data['username'] = $user[0]->username;
                    $token_data['time'] = time();
                    $user_token = $this->authorization_token->generateToken($token_data);
                    $this->User_history->create(array('user_id'=>$user[0]->id,'ip'=>$ip,'device'=>$device,'date'=> date('Y-m-d H:i:s',time())));
                    // $this->db->trans_start();
                    // $this->User_m->create(array('id'=>$user[0]->id,'last_token'=>$user_token,'user_updated' => 1,'date_updated' => date('Y-m-d H:i:s')), 'update');
                    // $this->db->trans_complete();
                    
                    // echo "<pre>";var_dump($user_token);echo "</pre>";die();
                    // if ($this->db->trans_status() === false) {
                    //     // Error Database
                    //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    // } else {
                        $response_data = array(
                            'status'    => true,
                            'token'     => $user_token
                        );
                        $this->REST_Return(200, 'SignIn Successful', $response_data);
                    // }
                // } else {
                //     $this->REST_Return(422, 'Your Password Invalid, please Try Again', $response_data);
                // }
            } else {
                $this->REST_Return(422, 'No user with that address/Your Password Invalid, please Try Again');
            }
            return;
        }
    }

    /**
     * GET Costing with API
     * -------------------------
     * @method: GET
     */

     public function profile_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        $error  = true;
        $model  = 'User';

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $id = $this->security->xss_clean($is_valid_token['data']->id);
            $output = array(); $output_user = array(); $output_employee = array(); $output_user_group = array(); $output_module = array();
            $output_user = $this->$model->read($id)[0];
            if ($output_user->employee_id) {
                $output_employee = $this->Employee->read($output_user->employee_id)[0];
            }
            if ($output_user->id) {
                $this->User_group->whereFields = array(
                    'user_id' => $output_user->id
                );
                
                $this->User_group->read_auth();
                $output_module = $this->User_group->read($p = 0, $post = false);
                
                if ($output_user->institute_branch_id) {
                    $institute_branch = $this->Institute_branch->read($p = $output_user->institute_branch_id, $post = false)[0];
                }

                if ($output_module[0]->group_id) {
                    $output_user_group = $this->Group->read($p = $output_module[0]->group_id, $post = false)[0];
                }

            }
            // if ($output_user->id) {
            //     $this->User_group->whereFields = array(
            //         'user_id' => $output_user->id
            //     );
            //     $output_user_group = $this->User_group->read($p = 0, $post = false);
            // }
            $output = array(
                'user' => $output_user,
                'employee' => $output_employee,
                'user_group' => $output_user_group,
                'institute_branch' => $institute_branch,
                'module' => $output_module
            );
            $response_data = array();
            if ($output_user || $output_user == [] && $id == '0') {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, 'Costing with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
            return;
        } else {
            $response_data = array(
                'error' => $is_valid_token['message']
            );
            $this->REST_Return(401, $response_data);
            return;
        }
    }
}