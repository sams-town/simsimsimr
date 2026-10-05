<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Generik extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");

        $this->create_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|required|max_length[10]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]')
        );

        $this->update_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|max_length[10]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function create_post()
    {
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];

        $margin =  json_encode($dt_post['margin']);
        $validateToken = $this->authorization_token->validateToken();
        if (!empty($validateToken) AND $validateToken['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            $dt_depo = array(
                'institute_branch_id' => $institute_branch_id,
                'code' => $dt_post['code'],
                'name' => $dt_post['name'],
                'margin' => $margin,
            );
    
            $id = $this->Depo->create($dt_depo);
    
            $response_data = array(
                'id'     => $id
            );
            $this->REST_Return(201, 'SUCCESS', $response_data);
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }
    public function update_put($p = 0)
    {
        $id         = $this->security->xss_clean($p);
        $data       = $this->json_put();
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];

        $validateToken = $this->authorization_token->validateToken();
        if (!empty($validateToken) AND $validateToken['status'] === TRUE) {
            $margin = "";
            if (count($dt_put['margin']) > 0) {
                $margin =  json_encode($dt_put['margin']);
            }

            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            $dt_depo = array(
                'id' => $id,
                'institute_branch_id' => $institute_branch_id,
                'code' => $dt_put['code'],
                'name' => $dt_put['name'],
                'margin' => $margin,
            );
    
            $this->Depo->update($id, $dt_depo);
    
            $response_data = array(
                'id'     => $id
            );
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }
}