<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Institute_branch extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/Institute_model", "Institute");
    }

    /**
     * Institute Branch API
     * --------------------
     * @param: institute_id
     * @param: code
     * @param: name
     * @param: phone
     * @param: email
     * --------------------
     * @method : POST
     * @link: version/system/institute_branch
     **/

     public function create_post() {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('institute_id', 'institute_id', 'trim|required');
            $this->form_validation->set_rules('code', 'code', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('phone', 'phone', 'trim|required|max_length[15]');
            $this->form_validation->set_rules('email', 'email', 'trim|required|max_length[255]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                # Check institute_id
                if($this->Institute->read($_POST['institute_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
    
                    # Upload Logo
                    if (isset($_FILES['logo']['name'])){
                        $_FILES['images']['name']= $_FILES['logo']['name'];
                        $_FILES['images']['type']= $_FILES['logo']['type'];
                        $_FILES['images']['tmp_name']= $_FILES['logo']['tmp_name'];
                        $_FILES['images']['error']= $_FILES['logo']['error'];
                        $_FILES['images']['size']= $_FILES['logo']['size'];
                        $files_file = $_FILES['logo']['name'];
                        $config['upload_path'] = './assets/document/institute/logo';
                        $config['allowed_types'] = 'jpeg|png|jpg|PNG|JPG';
                        $config['overwrite'] = true;	
                        $config['file_name'] = $_FILES['logo']['name'];
                        $this->load->library('upload', $config, 'photo_upload');
                        $this->photo_upload->initialize($config);
                        if ($this->photo_upload->do_upload('images') ) {
                            $fileData = $this->photo_upload->data();
                        } else {
                            $error = array('error' => $this->photo_upload->display_errors());
                            echo $error['error'];
                            die();
                        }
                    } else {
                        $this->REST_Return(422, 'Logo is required.');
                        return;
                    }
                    
                    $dt_institute_branch = array(
                        'institute_id'      => $_POST['institute_id'],
                        'code'              => $_POST['code'],
                        'name'              => $_POST['name'],
                        'phone'             => $_POST['phone'],
                        'email'             => $_POST['email'],
                        'logo'              => $files_file
                    );
                    $id = $this->$model->create($dt_institute_branch);
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
                    $this->REST_Return(404, 'Wrong institute_id!');
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
     * GET Institute with API
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

    /**
     * Institute Branch API
     * --------------------
     * @param: institute_id
     * @param: code
     * @param: name
     * @param: phone
     * @param: email
     * --------------------
     * @method : POST
     * @link: version/system/institute_branch
     **/

     public function update_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_rules('institute_id', 'institute_id', 'trim|required');
            $this->form_validation->set_rules('code', 'code', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('phone', 'phone', 'trim|required|max_length[15]');
            $this->form_validation->set_rules('email', 'email', 'trim|required|max_length[255]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();     
                $data = $this->$model->read($id,false);
                if($data != []) {
                    # Check institute_id
                    if($this->Institute->read($_POST['institute_id'], false) != []) {
                        # Upload Logo
                        if (isset($_FILES['logo']['name'])){
                            $_FILES['images']['name']= $_FILES['logo']['name'];
                            $_FILES['images']['type']= $_FILES['logo']['type'];
                            $_FILES['images']['tmp_name']= $_FILES['logo']['tmp_name'];
                            $_FILES['images']['error']= $_FILES['logo']['error'];
                            $_FILES['images']['size']= $_FILES['logo']['size'];
                            $files_file = $_FILES['logo']['name'];
                            $config['upload_path'] = './assets/document/institute/logo';
                            $config['allowed_types'] = 'jpeg|png|jpg|PNG|JPG';
                            $config['overwrite'] = true;	
                            $config['file_name'] = $_FILES['logo']['name'];
                            $this->load->library('upload', $config, 'photo_upload');
                            $this->photo_upload->initialize($config);
                            if ($this->photo_upload->do_upload('images') ) {
                                $fileData = $this->photo_upload->data();
                            } else {
                                $error = array('error' => $this->photo_upload->display_errors());
                                echo $error['error'];
                                die();
                            }
                        } else {
                            $files_file = $data[0]->logo;
                        }
        
                        $dt_institute_branch = array(
                            'id'                => $id,
                            'institute_id'      => $_POST['institute_id'],
                            'code'              => $_POST['code'],
                            'name'              => $_POST['name'],
                            'phone'             => $_POST['phone'],
                            'email'             => $_POST['email'],
                            'logo'              => $files_file
                        );
                        $id = $this->$model->update($id, $dt_institute_branch);
                    } else {
                        $this->REST_Return(404, 'Wrong institute_id!');
                        return;
                    }
                } else {
                    $this->REST_Return(404, 'Institute Branch with id ' . $id . ' does not exist!');
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