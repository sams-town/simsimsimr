<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class How_to_enter extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
            array('field' => 'address', 'label' => 'address', 'rules' => 'trim'),
            array('field' => 'pic', 'label' => 'pic', 'rules' => 'trim|max_length[100]'),
            array('field' => 'phone', 'label' => 'phone', 'rules' => 'trim|max_length[100]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|max_length[100]'),
            array('field' => 'email', 'label' => 'email', 'rules' => 'trim|max_length[100]'),
            array('field' => 'type_how_to_enter_id', 'label' => 'type_how_to_enter_id', 'rules' => 'trim|required|max_length[1]')
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
            array('field' => 'address', 'label' => 'address', 'rules' => 'trim'),
            array('field' => 'pic', 'label' => 'pic', 'rules' => 'trim|max_length[100]'),
            array('field' => 'phone', 'label' => 'phone', 'rules' => 'trim|max_length[100]'),
            array('field' => 'status', 'label' => 'status', 'rules' => 'trim|max_length[100]'),
            array('field' => 'email', 'label' => 'email', 'rules' => 'trim|max_length[100]'),
            array('field' => 'type_how_to_enter_id', 'label' => 'type_how_to_enter_id', 'rules' => 'trim|max_length[1]')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }
}