<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Helper extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");

        $this->create_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|required|max_length[15]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
            array('field' => 'variable', 'label' => 'variable', 'rules' => 'trim|required|max_length[100]'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'trim|required')
        );

        $this->update_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|max_length[15]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
            array('field' => 'variable', 'label' => 'variable', 'rules' => 'trim|max_length[100]'),
            array('field' => 'description', 'label' => 'description', 'rules' => 'trim')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }
}