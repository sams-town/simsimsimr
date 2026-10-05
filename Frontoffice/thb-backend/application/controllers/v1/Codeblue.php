<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Codeblue extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");

        $this->create_rules = array(
            array('field' => 'bed_id', 'label' => 'bed_id', 'rules' => 'trim|required|max_length[36]'),
        );
    }

    public function create_post()
    {
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];

        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
        $validateToken = $this->authorization_token->validateToken();
        if (empty($validateToken) AND $validateToken['status'] === FALSE) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
        $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
        $user_id = $this->security->xss_clean($validateToken['data']->id);
        $dt_codeblue = array(
            'institute_branch_id'   => $institute_branch_id,
            'bed_id'                => $dt_post['bed_id'],
            'user'                  => $user_id,
            'timestamp'             => date('Y-m-d H:i:s')
        );
        $id = $this->Codeblue->create($dt_codeblue);
        $response_data = array('id'=> $id);
        $this->REST_Return(201, 'SUCCESS', $response_data);
        return;
    }
}