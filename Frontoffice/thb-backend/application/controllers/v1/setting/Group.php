<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Group extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Group_module_model", "Group_module");
        $this->load->model("$this->directory/Module_model", "Module");

        $this->create_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required|max_length[100]'),
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Group_module API
     * --------------------
     * @param: group_id
     * @param: module
     * --------------------
     * @method : POST
     * @link: version/system/group_module
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
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            # Form Validation
            $this->form_validation->set_rules('name', 'name', 'trim|required');
            $this->form_validation->set_rules('module_id[]', 'module_id', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check group_id
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_group_modul = array();
                
                $dt_group = array(
                    'institute_branch_id'  => $institute_branch_id,
                    'name'          => $dt_post['name'],
                );
                $id = $this->$model->create($dt_group);
                for ($i=0; $i < count($dt_post['module_id']) ; $i++) { 
                    // Check module_id
                    if($this->Module->read($dt_post['module_id'][$i],false) != []) {
                        array_push($dt_group_modul, array(
                            'group_id'              => $id,
                            'module_id'             => $dt_post['module_id'][$i]
                        ));
                    } else {
                        $this->REST_Return(404, 'There is no module with id ' . $dt_post['module'][$i]);
                        return;
                    }
                }
                // var_dump($dt_group_modul);die();
                $this->Group_module->replace_batch($dt_group_modul);
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = array(
                        'message'     => 'Created!'
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                }
                return;   
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

	public function update_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules($this->update_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_group_modul=array();
                if(count($this->$model->read($id)) == 1) {
                    $want_to_update = array(
                        'id' => $id,
                        'name' => $dt_put['name'],
                    );
                    $this->$model->update($id, $want_to_update);
                    for ($i=0; $i < count($dt_put['module_id']) ; $i++) { 
                        // Check module_id
                        if($this->Module->read($dt_put['module_id'][$i],false) != []) {
                            array_push($dt_group_modul, array(
                                'group_id'              => $id,
                                'module_id'             => $dt_put['module_id'][$i]
                            ));
                        } else {
                            $this->REST_Return(404, 'There is no module with id ' . $dt_put['module'][$i]);
                            return;
                        }
                    }
                    // var_dump($dt_group_modul);die();
                    $this->Group_module->replace_batch($dt_group_modul);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $response_data = $output;
                $gm = array();
                for ($i=0; $i < count($output->rows); $i++) { 
                    $Group_module = $this->Group_module->read($output->rows[$i]->id);
                    for ($o=0; $o < count($Group_module); $o++) { 
                        $gm[$o] = $Group_module[$o]->module_id;
                    }
                    $output->rows[$i]->module_id = $gm;
                }
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
}