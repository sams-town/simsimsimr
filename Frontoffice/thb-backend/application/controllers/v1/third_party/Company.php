<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Company extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        // Load Helper
        $this->load->helper("upload");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
            array('field' => 'flag', 'label' => 'flag', 'rules' => 'trim|required|max_length[1]')
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
            array('field' => 'flag', 'label' => 'flag', 'rules' => 'trim|max_length[1]')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function upload_logo_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $device     = $_SERVER['HTTP_USER_AGENT'];
        $id         = $this->security->xss_clean($p);
        $ip         = $this->get_client_ip();
        $error      = TRUE;
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $company_id = $id;

        if ($company_id === 0 || $company_id === "0") {
            $this->REST_Return(400, 'company_id is required');
            return;
        }

        $select = 'a.id, a.name';
        $company = $this->Company->select($company_id, $select);
        if (count($company) == 0) {
            $this->REST_Return(404, 'there is no company with id ' . $company_id);
            return;
        }

        $company_name = $company[0]->name;
        $company_name = str_replace(" ", "_", $company_name);
        $company_name = str_replace(".", "", $company_name);

        $filename = $company_name . '_' . date('Ymd');

        $upload = upload_file($_FILES['file'], $filename, 'assets/document/company/logo');
        if ($upload['status'] == 'failed') {
            $this->REST_Return(422, $upload['message'], array());
            return;
        }
        
        $ext_type = $upload['type'];
        $filename = $filename . '.' . $ext_type;
        $url = 'assets/document/company/logo/' . $filename;

        /* Start Transaction */
        $this->db->trans_start();

        $dt_company = array(
            'logo_url' => $url,
        );

        $this->$model->update_v2($company_id, $dt_company);
       
        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_company,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to upload company logo', $response_data);
            return;
        }

        $response_data = $id;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }
}