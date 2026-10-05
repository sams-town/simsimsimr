<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Radiology_package extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Category_package_model", "Category_package");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");

        $this->update_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|max_length[10]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                // Costing_services_class
                $this->Costing_services_class->whereFields['a.costing_id'] = $output[$i]->costing_id;
                $costing_services_class = $this->Costing_services_class->read(0,false);
                for ($k=0; $k < count($costing_services_class); $k++) {
                    foreach ($costing_services_class[$k] as $key => $value) {
                        if($key == 'costing_id') { continue; }
                        $output[$i]->costing_services_class[$key] = $value;
                    } 
                }
            }
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

    public function detail_v2_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $most_used = $this->$model->get_most_used_package($q_strings);

        $select = 'a.name as category_name';
        $where = array('package' => 'RADIOLOGY');
        $category_arr = $this->Category_package->select(0, $select, $where);
        
        for ($i=0; $i < count($most_used); $i++) { 
            $category = $most_used[$i]->category;

            // $this->Costing_services_class->whereFields['a.costing_id'] = $most_used[$i]->costing_id;
            // $costing_services_class = $this->Costing_services_class->read(0, false);
            
            // for ($k=0; $k < count($costing_services_class); $k++) {
            //     foreach ($costing_services_class[$k] as $key => $value) {
            //         if($key == 'costing_id') { continue; }
            //         $most_used[$i]->costing_services_class[$key] = $value;
            //     } 
            // }

            foreach ($most_used[$i] as $key => $val) {
                $explode = explode('_', $key, 2);
                $csc = $explode[0];
                
                if ($csc == "csc") 
                {
                    unset($most_used[$i]->$key);
                    $key = $explode[1];
                    $most_used[$i]->costing_services_class[$key] = $val;
                }
            }
            
            for ($j=0; $j < count($category_arr); $j++) { 
                $category_name = $category_arr[$j]->category_name;

                if ($category_name == $category) {                    
                    if (!isset($category_arr[$j]->category_items)) {
                        $category_arr[$j]->category_items = array();
                    }

                    array_push($category_arr[$j]->category_items, $most_used[$i]);
                    break;
                }
            }
        }

        $response_data = $category_arr;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function category_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $most_used = $this->$model->get_most_used_category();

        $response_data = $most_used;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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
            for ($i=0; $i < count($output->rows); $i++) { 
                // Costing_services_class
                $costing_services_class = $this->Costing_services_class->read($output->rows[$i]->costing_id);
                for ($k=0; $k < count($costing_services_class); $k++) {
                    foreach ($costing_services_class[$k] as $key => $value) {
                        if($key == 'costing_id') { continue; }
                        $output->rows[$i]->costing_services_class[$k][$key] = $value;
                    } 
                }
            }
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
     * radiology_package API
     * --------------------
     * @param: employee_id
     * @param: position_id
     * @param: date_expired
     * --------------------
     * @method : POST
     * @link: version/health_facility/radiology_pacakage
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
            # Form Validation
            $this->form_validation->set_rules('costing_id', 'costing_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('code', 'code', 'trim|required|max_length[10]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check costing_id
                if($this->Costing->read($dt_post['costing_id'],false) != []) {
                    $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $dt_laboratory_package = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'costing_id'            => $dt_post['costing_id'],
                        'code'                  => $dt_post['code'],
                        'name'                  => $dt_post['name']
                    );
                    $id = $this->$model->create($dt_laboratory_package);
    
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
                    return;     
                } else {
                    $this->REST_Return(404, 'Wrong costing_id!');
                    return;
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}