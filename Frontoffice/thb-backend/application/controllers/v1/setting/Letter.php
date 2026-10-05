<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Letter extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_type_model", "{$this->name}");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'trim|required')
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
            array('field' => 'content', 'label' => 'content', 'rules' => 'trim|required')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
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
    
        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}