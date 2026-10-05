<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Employee extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Employee_position_model", "Employee_position");
        $this->load->model("$this->directory/Position_model", "Position");

        $this->update_rules = array(
            array('field' => 'f_title', 'label' => 'f_title', 'rules' => 'trim|max_length[15]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
            array('field' => 'fullname', 'label' => 'fullname', 'rules' => 'trim|max_length[130]'),
            array('field' => 'b_title', 'label' => 'b_title', 'rules' => 'trim|max_length[20]'),
            array('field' => 'birth_date', 'label' => 'birth_date', 'rules' => 'trim'),
            array('field' => 'sex', 'label' => 'sex', 'rules' => 'trim|max_length[1]'),
            array('field' => 'address', 'label' => 'address', 'rules' => 'trim')
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }


    /**
     * GET Employee with API
     * -------------------------
     * @method: GET
     */

     public function doctor_detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $this->$model->whereFields['institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['is_delete'] = '0';
            if (isset($q_strings['health_facility_item_id'])) {
                if ($q_strings['health_facility_item_id'] == '40f06534-07ec-4221-ad9d-dea08196079f') {
                    $output = $this->$model->get_position_specialist($institute_branch_id);
                } else if ($q_strings['health_facility_item_id'] == 'cbd51a04-eb9b-4ddf-89fc-f5f2e2865144') {
                    $output = $this->$model->get_position_general($institute_branch_id);
                } else {
                    $output = $this->$model->get_position_health_facility_item($institute_branch_id, $q_strings['health_facility_item_id']);
                }
            } else {
                $output = $this->$model->get_all_doctor();
            }
            $response_data = array();
            if ($output || $output == [] && $id == '0') {
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(404, 'Employee with id ' . $id . ' does not exist!');
                return;
            }
            if ($error == true) {
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                return;
            } else {
                $this->REST_Return(200, 'SUCCESS', $response_data);
                return;
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function doctor_specialist_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        // $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $select = 'a.*, p.health_facility_item_id, hfi.name as health_facility_item_name, p.name as position_name';
        $where = array(
            'a.is_delete' => 0,
            'p.is_specialist' => 1,
            'hf.id' => '3cb626f5-9148-4fb1-8663-3d7f028b01a6',
        );
        $join = array(
		    array('employee_position ep', 'ep.employee_id = a.id', 'INNER'),
		    array('position p', 'p.id = ep.position_id', 'INNER'),
		    array('health_facility_item hfi', 'hfi.id = p.health_facility_item_id', 'INNER'),
		    array('health_facility hf', 'hf.id = hfi.health_facility_id', 'INNER'),
        );
        $order = array('a.name' => 'ASC');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function doctor_general_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        // $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $select = 'a.*, p.health_facility_item_id, hfi.name as health_facility_item_name, p.name as position_name';
        $where = array(
            'a.id' => 'd3dd8008-4e78-4cd9-aee7-886833e73899',
            'a.is_delete' => 0,
            'p.is_doctor' => 1,
        );
        $join = array(
		    array('employee_position ep', 'ep.employee_id = a.id', 'INNER'),
		    array('position p', 'p.id = ep.position_id', 'INNER'),
		    array('health_facility_item hfi', 'hfi.id = p.health_facility_item_id', 'INNER'),
		    array('health_facility hf', 'hf.id = hfi.health_facility_id', 'INNER'),
        );
        $order = array('a.name' => 'ASC');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function detail_get($p = 0) {
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

        $employee_id = $id;

        if ($employee_id === 0 || $employee_id === "0") {
            $this->REST_Return(400, 'employee_id is required');
            return;
        }

        $select = 'a.*,s.name as sex_name, b.date_expired as position_date_expired,c.id as position_id,c.name as position_name,d.id as health_facility_item_id,d.name as health_facility_item_name,
				e.id as health_facility_id,e.name as health_facility_name, c.is_doctor, c.is_specialist';
        $where = array(
            'a.is_delete' => 0
        );
        $join = array(
            array('helper s', 'a.sex = s.code AND s.variable="sex"', 'LEFT'),
            array('employee_position b', 'a.id = b.employee_id', 'LEFT'),
            array('position c', 'b.position_id = c.id', 'LEFT'),
            array('health_facility_item d', 'c.health_facility_item_id = d.id', 'LEFT'),
            array('health_facility e', 'd.health_facility_id = e.id', 'LEFT'),
        );
        $output = $this->$model->select($employee_id, $select, $where, $join);

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
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

        $limit = $q_strings['limit'] ?? 20;
        $offset = $q_strings['offset'] ?? 0;

        $this->$model->limit = $limit; 
        $this->$model->offset = $offset; 
        
        if(isset($q_strings['limit']) || isset($q_strings['offset'])) {
            unset($q_strings['limit']);
            unset($q_strings['offset']);
        }

        if (isset($q_strings['position_id'])) {
            $q_strings['ep.position_id'] = $q_strings['position_id'];
            unset($q_strings['position_id']);
        }

        $select = 'a.*, h.name as sex_name, ep.date_expired as position_date_expired,p.id as position_id,p.name as position_name,hfi.id as health_facility_item_id,hfi.name as health_facility_item_name,
					hf.id as health_facility_id,hf.name as health_facility_name, p.department_id, d.name as department_name, u.id as user_id';
        $where = $q_strings;
        $where['a.is_delete'] = 0;
        $join = array(
            array('helper h', 'a.sex = h.code AND h.variable="sex"', 'LEFT'),
            array('employee_position ep', 'ep.employee_id = a.id AND ep.is_delete=0', 'LEFT'),
		    array('position p', 'ep.position_id = p.id', 'LEFT'),
		    array('department d', 'p.department_id = d.id', 'LEFT'),
            array('health_facility_item hfi', 'p.health_facility_item_id = hfi.id', 'LEFT'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'LEFT'),
            array('user u', 'a.id = u.employee_id', 'LEFT'),
        );
        $order = array(
            'a.name' => 'ASC'
        );
        $output = $this->$model->select(0, $select, $where, $join, $order);

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;   

        $response_data = (object) array(
            'rows' => $output,
            'start' => ($offset + $limit),
            'iTotal' => $iTotal
        );
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_backup_get($p = 0){
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
            $this->$model->whereFields['s.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
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
    
    public function sync_get($p = 0){
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
            $this->$model->whereFields['s.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            if ($output->rows != [] && $id != 0) {
                $response_data = $output->rows[0];
                $error   = false;
            } else if($output->rows != [] or $id == 0) {
                $json_body = array();
                for ($i = 0; $i < count($output->rows); $i++) {
                    if ($output->rows[$i]->bride_id == null && $output->rows[$i]->employee_id !== null) {
                        if ($output->rows[$i]->email == "") {
                            $json_body =array(
                                'number' => $output->rows[$i]->employee_id,
                                'name' => $output->rows[$i]->fullname,
                                'salutation' => $output->rows[$i]->bride_salutation,
                                'position' => $output->rows[$i]->occupation,
                                'salesman' => true
                            );
                        } else {
                            $json_body =array(
                                'number' => $output->rows[$i]->employee_id,
                                'name' => $output->rows[$i]->fullname,
                                'salutation' => $output->rows[$i]->bride_salutation,
                                'email' => $output->rows[$i]->email,
                                'position' => $output->rows[$i]->occupation,
                                'salesman' => true
                            );
                        }
                        // $return_bride = $this->bride_accurate('api/department/delete.do?id='.$output[$i]->bride_id, json_encode($json_body));
                        $return_bride = $this->bride_accurate('api/employee/save.do', json_encode($json_body));
                        // // var_dump($return_bride['r']['parent']['id']);
                        // // var_dump($json_body);
                        var_dump($json_body);
                        // // die();
                        if ($return_bride['s'] == true) {
                            $data_bride_employee = array(
                                'id' => $output->rows[$i]->id,
                                'bride_employee_id' => $output->rows[$i]->employee_id,
                                'bride_id' => $return_bride['r']['id'],
                            );
                            $this->Employee->replace($data_bride_employee);
                        } else {
                            var_dump($return_bride);
                            $this->REST_Return(404, 'Failed Accurate = ');
                            return;
                        }
                    }
                }
                // die();
                $response_data = $return_bride;
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

    public function bride_accurate($url = null,$param = null) {
        $url_acc   = 'https://zeus.accurate.id/accurate/';
        $SESSIONID = 'a70e6f70-f3cf-47d0-a3b0-10f8edad8b45';
        $token = '4e01b3e9-55e7-4c82-a44a-1d54b6a97a98';
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        $authorization[] = "Authorization:Bearer ".$token; 
        $authorization[] = "X-Session-ID:".$SESSIONID; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
    }

    /**
     * Employee API
     * --------------------
     * @param: f_title
     * @param: name
     * @param: fullname
     * @param: b_title
     * @param: birth_date
     * @param: sex
     * @param: address
     * --------------------
     * @method : POST
     * @link: version/hr/Employee
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
            $this->form_validation->set_rules('f_title', 'f_title', 'trim|max_length[15]');
            $this->form_validation->set_rules('name', 'name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('fullname', 'fullname', 'trim|max_length[130]');
            $this->form_validation->set_rules('b_title', 'b_title', 'trim|max_length[20]');
            $this->form_validation->set_rules('birth_date', 'birth_date', 'trim');
            $this->form_validation->set_rules('sex', 'sex', 'trim|max_length[1]');
            $this->form_validation->set_rules('address', 'address', 'trim');
            $this->form_validation->set_rules('position_id', 'position_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('date_expired', 'date_expired', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $this->Position->whereFields['institute_branch_id'] = $institute_branch_id;
                if($this->Position->read($dt_post['position_id'], false) != []) {
                    $dt_employee = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'f_title'               => isset($dt_post['f_title']) ? $dt_post['f_title'] : '',
                        'name'                  => $dt_post['name'],
                        'fullname'              => isset($dt_post['fullname']) ? $dt_post['fullname'] : '',
                        'b_title'               => isset($dt_post['b_title']) ? $dt_post['b_title'] : '',
                        'birth_date'            => isset($dt_post['birth_date']) ? $dt_post['birth_date'] : '',
                        'sex'                   => isset($dt_post['sex']) ? $dt_post['sex'] : '',
                        'address'               => isset($dt_post['address']) ? $dt_post['address'] : '',
                    );
                    $id = $this->$model->create($dt_employee);
    
                    // INIT_EMPLOYEE_POSITION
                    $dt_employee_position = array(
                        'employee_id'       => $id,
                        'position_id'       => $dt_post['position_id'],
                        'date_expired'      => isset($dt_post['date_expired']) ? $dt_post['date_expired'] : '',
                    );
                    $this->Employee_position->replace($dt_employee_position);
                } else {
                    $this->REST_Return(404, 'There is no position with id ' . $dt_post['position_id']);
                    return;
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

    /**
     * Employee API
     * --------------------
     * @param: f_title
     * @param: name
     * @param: fullname
     * @param: b_title
     * @param: birth_date
     * @param: sex
     * @param: address
     * --------------------
     * @method : POST
     * @link: version/mr/Employee
     **/

     public function update_backup_put($p = 0) {
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
            $this->form_validation->set_rules('f_title', 'f_title', 'trim|max_length[15]');
            $this->form_validation->set_rules('name', 'name', 'trim|max_length[100]');
            $this->form_validation->set_rules('fullname', 'fullname', 'trim|max_length[130]');
            $this->form_validation->set_rules('b_title', 'b_title', 'trim|max_length[20]');
            $this->form_validation->set_rules('birth_date', 'birth_date', 'trim');
            $this->form_validation->set_rules('sex', 'sex', 'trim|max_length[1]');
            $this->form_validation->set_rules('address', 'address', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $employee = $this->$model->read($id);
                $position = $this->Position->read($dt_put['position_id']); 
                if($employee != [] && $position != []) {
                    $dt_employee = array(
                        'id'                => $id,
                        'f_title'           => isset($dt_put['f_title']) ? $dt_put['f_title'] : $employee[0]->f_title,
                        'name'              => isset($dt_put['name']) ? $dt_put['name'] : $employee[0]->name,
                        'fullname'          => isset($dt_put['fullname']) ? $dt_put['fullname'] : $employee[0]->fullname,
                        'b_title'           => isset($dt_put['b_title']) ? $dt_put['b_title'] : $employee[0]->b_title,
                        'birth_date'        => isset($dt_put['birth_date']) ? $dt_put['birth_date'] : $employee[0]->birth_date,
                        'sex'               => isset($dt_put['sex']) ? $dt_put['sex'] : $employee[0]->sex,
                        'address'           => isset($dt_put['address']) ? $dt_put['address'] : $employee[0]->address,
                    );
                    $this->$model->update($id, $dt_employee);

                    $dt_employee_position = array(
                        'employee_id'       => $id,
                        'position_id'       => $dt_put['position_id'],
                        'date_expired'      => isset($dt_put['date_expired']) ? $dt_put['date_expired'] : $position[0]->date_expired,
                    );
                    $this->Employee_position->replace($dt_employee_position);
                } else {
                    $this->REST_Return(404, 'Wrong employee_id or position_id!');
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

    public function update_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->update_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $employee_id = $id;

        if ($employee_id === 0 || $employee_id === "0") {
            $this->REST_Return(400, 'employee_id is required');
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_id);
            return;
        }

        $address = $dt_put['address'];
        $b_title = $dt_put['b_title'];
        $f_title = $dt_put['f_title'];
        $birth_date = $dt_put['birth_date'];
        $fullname = $dt_put['fullname'];
        $name = $dt_put['name'];
        $sex = $dt_put['sex'];
        $position_id = $dt_put['position_id'];
        $date_expired = $dt_put['date_expired'];

        $select = 'a.id';
        $position = $this->Position->select($position_id, $select);
        if (count($position) == 0) {
            $this->REST_Return(404, 'there is no position with id ' . $position_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_employee = array(
            'address'       => $address,
            'b_title'       => $b_title,
            'birth_date'    => $birth_date,
            'f_title'       => $f_title,
            'fullname'      => $fullname,
            'name'          => $name,
            'sex'           => $sex,
        );

        $this->$model->update_v2($employee_id, $dt_employee);

        $dt_employee_position_delete = array(
            'is_delete' => 1,
        );
        $where_delete = array(
            'employee_id' => $employee_id,
        );
        $this->Employee_position->update_v2(0, $dt_employee_position_delete, $where_delete);

        $dt_employee_position = array(
            'employee_id'  => $employee_id,
            'position_id'  => $position_id,
            'date_expired' => $date_expired,
            'is_delete'    => 0,
        );
        $this->Employee_position->replace($dt_employee_position);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_employee,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update employee', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function upload_ttd_post($p = 0) {
        $_POST      = $this->security->xss_clean($_POST);
        $_FILES     = $this->security->xss_clean($_FILES);
        $_SERVER    = $this->security->xss_clean($_SERVER);
        $id         = $this->security->xss_clean($p);
        $model      = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        $doctor_id = $id;

        $select = 'a.id, a.name';
        $doctor = $this->$model->select($doctor_id, $select);
        
        if (count($doctor) == 0) {
            $this->REST_Return(404, 'employee with id ' . $id . ' does not exist!');
            return;
        }

        $doctor_name = $doctor[0]->name;
        $doctor_name_file = str_replace(' ', '-', $doctor_name);

        # upload ttd
        $type = $_FILES['ttd']['type'];
        $filename_raw = $_FILES['ttd']['name'];
        $filename_raw = str_replace(' ', '_', $filename_raw);

        $_FILES['images']['name'] = $filename_raw;
        $_FILES['images']['type'] = $type;
        $_FILES['images']['tmp_name'] = $_FILES['ttd']['tmp_name'];
        $_FILES['images']['error'] = $_FILES['ttd']['error'];
        $_FILES['images']['size'] = $_FILES['ttd']['size'];
        
        $config['upload_path'] = './assets/document/ttd';
        $config['allowed_types'] = 'jpg|jpeg|png|svg';
        $config['overwrite'] = true;	
        $config['file_name'] = 'ttd-dr-' . $doctor_name_file;

        $this->load->library('upload', $config, 'upload_ttd');

        $this->upload_ttd->initialize($config);
        if ($this->upload_ttd->do_upload('images') ) {
            $fileData = $this->upload_ttd->data();
        } else {       
            $response_data = array(
                'error'     => $this->upload_ttd->display_errors(),
                'data'      => [],
                'query'     => ""
            );

            $this->REST_Return(500, 'error while trying to upload file', $response_data);
            return;
        }

        $extension = explode('/', $type)[1];
        $ttd = 'assets/document/ttd/' . 'ttd-dr-' . $doctor_name_file . '.' . $extension;
        
        /* Start Transaction */
        $this->db->trans_start();

        $dt_employee = array(
            'ttd' => $ttd,
        );

        $this->$model->update($doctor_id, $dt_employee);

        $this->db->trans_complete();
        /* End Transaction */
        
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_employee,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update employee', $response_data);
            return;
        }

        $response_data = $id;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }

    public function doctor_schedule_day_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['bride_bpjs'])) {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $this->$model->whereFields['institute_branch_id'] = $institute_branch_id;
                $this->$model->whereFields['is_delete'] = '0';
                $day_now = date("l");
                $day_now = date("w", strtotime($day_now));
                $output = $this->$model->get_position_health_facility_item_schedule_day($institute_branch_id, $q_strings['bride_bpjs'], $day_now);
                $response_data = array();
                if ($output || $output == [] && $id == '0') {
                    $response_data = $output;
                    $error   = false;
                } else {
                    $this->REST_Return(404, 'Employee with id ' . $id . ' does not exist!');
                    return;
                }
                if ($error == true) {
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                    return;
                } else {
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                    return;
                }
            } else {
                $this->REST_Return(400, 'Health_facility_item_id is required!');
                return;
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function list_doctor_get($p = 0) {
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

        $is_specialist = $q_strings['is_specialist'] ?? 'all';

        if ($is_specialist == 'all') {
            $is_specialist = '';
        } else if ($is_specialist == '0' || $is_specialist == '1'){
            $is_specialist = "AND p.is_specialist = " . $is_specialist;
        } else {
            $this->REST_Return(400, 'invalid is_specialist');
            return;
        }
        
        $output = $this->db->query("
            SELECT e.id,
            e.fullname, 
            p.name AS position_name, 
            hfi.name AS 
            health_facility_item_name
            FROM employee e
            INNER JOIN employee_position ep ON e.id = ep.employee_id
            INNER JOIN position p ON ep.position_id = p.id
            INNER JOIN health_facility_item hfi ON p.health_facility_item_id = hfi.id
            WHERE p.is_doctor = 1
            AND e.is_delete = 0
            $is_specialist
            AND p.name NOT LIKE 'perawat%'
            AND p.name NOT LIKE 'bidan%'
            AND p.name NOT LIKE 'penata%'
            ORDER BY e.name
        ")->result();

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_nurse_get($p = 0) {
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

        $position = $q_strings['position'] ?? 'all';

        if ($position == 'all') {
            $position = "AND p.name LIKE 'perawat%' OR p.name LIKE 'bidan%'";
        } else if ($position == 'midwife'){
            $position = "AND p.name LIKE 'bidan%'";
        } else if ($position == 'nurse') {
            $position = "AND p.name LIKE 'perawat%'";
        } else {
            $this->REST_Return(400, 'invalid position');
            return;
        }

        $output = $this->db->query("
            SELECT e.id,
            e.fullname, 
            p.name AS position_name, 
            hfi.name AS 
            health_facility_item_name
            FROM employee e
            INNER JOIN employee_position ep ON e.id = ep.employee_id
            INNER JOIN position p ON ep.position_id = p.id
            INNER JOIN health_facility_item hfi ON p.health_facility_item_id = hfi.id
            WHERE p.is_doctor = 1
            AND e.is_delete = 0
            $position
            ORDER BY e.name
        ")->result();

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function delete_delete($p = 0) {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
        $id         = $this->security->xss_clean($p);

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 

        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->delete_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        if ($id === 0 || $id === "0") {
            $this->REST_Return(400, 'employee_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $employee_id = $this->$model->select_id($id);

        if ($employee_id == "") {
            $this->REST_Return(404, 'there is no employee with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_employee = array(
            'is_delete' => $is_delete,
        );

        $this->$model->update_v2($employee_id, $dt_employee);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_employee,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete employee', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

}