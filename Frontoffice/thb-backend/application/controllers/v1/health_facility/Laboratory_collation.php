<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Laboratory_collation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Laboratory_collation_parameter_model", "Laboratory_collation_parameter");
        $this->load->model("$this->directory/Laboratory_parameter_model", "Laboratory_parameter");

        $this->update_rules = array(
            array('field' => 'code', 'label' => 'code', 'rules' => 'trim|max_length[10]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    public function detail_get($p = 0) {
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
                $this->Laboratory_collation_parameter->whereFields['a.is_delete'] = '0';
                $laboratory_collation_parameter = $this->Laboratory_collation_parameter->read($output[$i]->id);
                for ($j=0; $j < count($laboratory_collation_parameter); $j++) { 
                    $laboratory_parameter = $this->Laboratory_parameter->read($laboratory_collation_parameter[$j]->parameter_id);
                    $output[$i]->parameter[$j]['parameter_id'] = $laboratory_parameter[0]->id; 
                    $output[$i]->parameter[$j]['name'] = $laboratory_parameter[0]->name; 
                    $output[$i]->parameter[$j]['value'] = $laboratory_parameter[0]->value; 
                    $output[$i]->parameter[$j]['unit'] = $laboratory_parameter[0]->unit; 
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
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                $this->Laboratory_collation_parameter->whereFields['a.is_delete'] = '0';
                $laboratory_collation_parameter = $this->Laboratory_collation_parameter->read($output->rows[$i]->id);
                for ($j=0; $j < count($laboratory_collation_parameter); $j++) { 
                    $laboratory_parameter = $this->Laboratory_parameter->read($laboratory_collation_parameter[$j]->parameter_id);
                    $output->rows[$i]->parameter[$j]['parameter_id'] = $laboratory_parameter[0]->id; 
                    $output->rows[$i]->parameter[$j]['name'] = $laboratory_parameter[0]->name; 
                    $output->rows[$i]->parameter[$j]['value'] = $laboratory_parameter[0]->value; 
                    $output->rows[$i]->parameter[$j]['unit'] = $laboratory_parameter[0]->unit; 
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
     * Laboratory_package API
     * --------------------
     * @param: code
     * @param: name
     * @param: parameter
     * --------------------
     * @method : POST
     * @link: version/health_facility/laboratory_collation
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
            $this->form_validation->set_rules('code', 'code', 'trim|required|max_length[10]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('parameter[]', 'parameter', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $dt_laboratory_collation = array(
                    'institute_branch_id'   => $institute_branch_id,
                    'code'                  => $dt_post['code'],
                    'name'                  => $dt_post['name']
                );
                $id = $this->$model->create($dt_laboratory_collation);
                
                // INIT_LABORATORY_COLLATION_PARAMETER
                for ($i=0; $i < count($dt_post['parameter']) ; $i++) {
                    // Check parameter_id
                    if($this->Laboratory_parameter->read($dt_post['parameter'][$i]['parameter_id'], false) != []) {
                        $dt_laboratory_collation_parameter = array(
                            'laboratory_collation_id'   => $id,
                            'parameter_id'              => $dt_post['parameter'][$i]['parameter_id'],
                        );
                        $this->Laboratory_collation_parameter->replace($dt_laboratory_collation_parameter);
                    } else {
                        $this->REST_Return(404, 'There is no parameter with id ' . $dt_post['parameter'][$i]['parameter_id']);
                        return;
                    }
                }
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
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}