<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Fluid_balance extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_intake_model", "{$this->name}_intake");
        $this->load->model("$this->directory/{$this->name}_intake_item_model", "{$this->name}_intake_item");
        $this->load->model("$this->directory/{$this->name}_output_model", "{$this->name}_output");
        $this->load->model("$this->directory/{$this->name}_output_item_model", "{$this->name}_output_item");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'fluid_balance_date', 'label' => 'fluid_balance_date', 'rules' => 'trim|required'),
            array('field' => 'infusion_scheme', 'label' => 'infusion_scheme', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->create_intake_rules = array(
            array('field' => 'fluid_balance_id', 'label' => 'fluid_balance_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->create_intake_item_rules = array(
            array('field' => 'fluid_balance_intake_id', 'label' => 'fluid_balance_intake_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->create_output_rules = array(
            array('field' => 'fluid_balance_id', 'label' => 'fluid_balance_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->create_output_item_rules = array(
            array('field' => 'fluid_balance_output_id', 'label' => 'fluid_balance_output_id', 'rules' => 'trim|required|max_length[36]'),
        );
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
    
        $fluid_balance_id = $id;
    
        if ($fluid_balance_id === 0 || $fluid_balance_id === "0") {
            $this->REST_Return(400, 'fluid_balance_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($fluid_balance_id);
        $response_data = array();
    
        if (count($output) > 0) {
            $fluid_balance_id = $output[0]->id;

        }
    
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
    
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    
        $this->$model->whereFields = $q_strings;
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read_ssr($id);

        for ($i=0; $i < count($output->rows); $i++) { 
            $fluid_balance_id = $output->rows[$i]->id;

            $select = 'a.*';
            $where = array('fluid_balance_id' => $fluid_balance_id, 'a.is_delete' => 0);
            $fluid_balance_intake = $this->Fluid_balance_intake->select(0, $select, $where);

            for ($j=0; $j < count($fluid_balance_intake); $j++) { 
                $fluid_balance_intake_id = $fluid_balance_intake[$j]->id;

                $select = 'a.*';
                $where = array('fluid_balance_intake_id' => $fluid_balance_intake_id, 'a.is_delete' => 0);
                $fluid_balance_intake_item = $this->Fluid_balance_intake_item->select(0, $select, $where);

                $fluid_balance_intake[$j]->fluid_balance_intake_items = $fluid_balance_intake_item;
            }

            $output->rows[$i]->fluid_balance_intake = $fluid_balance_intake;

            $select = 'a.*';
            $where = array('fluid_balance_id' => $fluid_balance_id, 'a.is_delete' => 0);
            $fluid_balance_output = $this->Fluid_balance_output->select(0, $select, $where);

            for ($j=0; $j < count($fluid_balance_output); $j++) { 
                $fluid_balance_output_id = $fluid_balance_output[$j]->id;

                $select = 'a.*';
                $where = array('fluid_balance_output_id' => $fluid_balance_output_id, 'a.is_delete' => 0);
                $fluid_balance_output_item = $this->Fluid_balance_output_item->select(0, $select, $where);

                $fluid_balance_output[$j]->fluid_balance_output_items = $fluid_balance_output_item;
            }

            $output->rows[$i]->fluid_balance_output = $fluid_balance_output;
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function create_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $registration_destination_id = $dt_post['registration_destination_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);

        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $fluid_balance_date = $dt_post['fluid_balance_date'];
        $infusion_scheme = $dt_post['infusion_scheme'];
        $total_intake_24 = $dt_post['total_intake_24'];
        $total_output_24 = $dt_post['total_output_24'];
        $iwl = $dt_post['iwl'];
        $diuresis = $dt_post['diuresis'];
        $fluid_balance_24 = $dt_post['fluid_balance_24'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'fluid_balance_date'            => $fluid_balance_date,
            'infusion_scheme'               => $infusion_scheme,
            'total_intake_24'               => $total_intake_24,
            'total_output_24'               => $total_output_24,
            'iwl'                           => $iwl,
            'diuresis'                      => $diuresis,
            'fluid_balance_24'              => $fluid_balance_24,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_fluid_balance);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create fluid_balance', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
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
    
        $fluid_balance_id = $id;
    
        if ($fluid_balance_id === 0 || $fluid_balance_id === "0") {
            $this->REST_Return(400, 'fluid_balance_id is required');
            return;
        }
    
        $select = "a.id";
        $fluid_balance = $this->$model->select($fluid_balance_id, $select);
    
        if (count($fluid_balance) == 0) {
            $this->REST_Return(404, 'fluid_balance with id ' . $fluid_balance_id . ' does not exist!');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $infusion_scheme = $dt_put['infusion_scheme'];
        $total_intake_24 = $dt_put['total_intake_24'];
        $total_output_24 = $dt_put['total_output_24'];
        $iwl = $dt_put['iwl'];
        $diuresis = $dt_put['diuresis'];
        $fluid_balance_24 = $dt_put['fluid_balance_24'];
        
        $dt_fluid_balance = array(
            'infusion_scheme'               => $infusion_scheme,
            'total_intake_24'               => $total_intake_24,
            'total_output_24'               => $total_output_24,
            'iwl'                           => $iwl,
            'diuresis'                      => $diuresis,
            'fluid_balance_24'              => $fluid_balance_24,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $this->$model->update($fluid_balance_id, $dt_fluid_balance);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update fluid_balance', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function create_intake_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_intake_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $fluid_balance_id = $dt_post['fluid_balance_id'];

        $select = 'a.id';
        $fluid_balance = $this->$model->select($fluid_balance_id, $select);

        if (count($fluid_balance) == 0) {
            $this->REST_Return(404, 'there is no fluid_balance with id ' . $fluid_balance_id);
            return;
        }

        $type = $dt_post['type'];
        $iv_line = $dt_post['iv_line'];
        $iv_line_location = $dt_post['iv_line_location'];
        $subtotal_first = $dt_post['subtotal_first'];
        $subtotal_second = $dt_post['subtotal_second'];
        $subtotal_third = $dt_post['subtotal_third'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance_intake = array(
            'fluid_balance_id'  => $fluid_balance_id,
            'type'              => $type,
            'iv_line'           => $iv_line,
            'iv_line_location'  => $iv_line_location,
            'subtotal_first'    => $subtotal_first,
            'subtotal_second'   => $subtotal_second,
            'subtotal_third'    => $subtotal_third,
            'user_added'        => $user_id,
            'date_added'        => date('Y-m-d H:i:s',time()),
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Fluid_balance_intake->create($dt_fluid_balance_intake);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance_intake,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create fluid_balance_intake', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_intake_put($p = 0) {
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
    
        $fluid_balance_intake_id = $id;
    
        if ($fluid_balance_intake_id === 0 || $fluid_balance_intake_id === "0") {
            $this->REST_Return(400, 'fluid_balance_intake_id is required');
            return;
        }

        $select = 'a.id';
        $fluid_balance_intake = $this->Fluid_balance_intake->select($fluid_balance_intake_id, $select);

        if (count($fluid_balance_intake) == 0) {
            $this->REST_Return(404, 'there is no fluid_balance_intake with id ' . $fluid_balance_intake_id);
            return;
        }

        $type = $dt_put['type'];
        $iv_line = $dt_put['iv_line'];
        $iv_line_location = $dt_put['iv_line_location'];
        $subtotal_first = $dt_put['subtotal_first'];
        $subtotal_second = $dt_put['subtotal_second'];
        $subtotal_third = $dt_put['subtotal_third'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance_intake = array(
            'type'              => $type,
            'iv_line'           => $iv_line,
            'iv_line_location'  => $iv_line_location,
            'subtotal_first'    => $subtotal_first,
            'subtotal_second'   => $subtotal_second,
            'subtotal_third'    => $subtotal_third,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $this->Fluid_balance_intake->update($fluid_balance_intake_id, $dt_fluid_balance_intake);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance_intake,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update fluid_balance_intake', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function create_intake_item_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_intake_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $fluid_balance_intake_id = $dt_post['fluid_balance_intake_id'];

        $select = 'a.id';
        $fluid_balance_intake = $this->Fluid_balance_intake->select($fluid_balance_intake_id, $select);

        if (count($fluid_balance_intake) == 0) {
            $this->REST_Return(404, 'there is no fluid_balance_intake with id ' . $fluid_balance_intake_id);
            return;
        }

        $time = $dt_post['time'];
        $fluid_name = $dt_post['fluid_name'];
        $initial_fluid = $dt_post['initial_fluid'];
        $fluid_in = $dt_post['fluid_in'];
        $total = $dt_post['total'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance_intake_item = array(
            'fluid_balance_intake_id'  => $fluid_balance_intake_id,
            'time'                     => $time,
            'fluid_name'               => $fluid_name,
            'initial_fluid'            => $initial_fluid,
            'fluid_in'                 => $fluid_in,
            'total'                    => $total,
            'user_added'               => $user_id,
            'date_added'               => date('Y-m-d H:i:s',time()),
            'date_updated'             => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Fluid_balance_intake_item->create($dt_fluid_balance_intake_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance_intake_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create fluid_balance_intake_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function create_output_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_output_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $fluid_balance_id = $dt_post['fluid_balance_id'];

        $select = 'a.id';
        $fluid_balance = $this->$model->select($fluid_balance_id, $select);

        if (count($fluid_balance) == 0) {
            $this->REST_Return(404, 'there is no fluid_balance with id ' . $fluid_balance_id);
            return;
        }

        $rinse = $dt_post['rinse'];
        $subtotal_first = $dt_post['subtotal_first'];
        $subtotal_second = $dt_post['subtotal_second'];
        $subtotal_third = $dt_post['subtotal_third'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance_output = array(
            'fluid_balance_id'  => $fluid_balance_id,
            'rinse'             => $rinse,
            'subtotal_first'    => $subtotal_first,
            'subtotal_second'   => $subtotal_second,
            'subtotal_third'    => $subtotal_third,
            'user_added'        => $user_id,
            'date_added'        => date('Y-m-d H:i:s',time()),
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Fluid_balance_output->create($dt_fluid_balance_output);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance_output,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create fluid_balance_output', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_output_put($p = 0) {
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
    
        $fluid_balance_output_id = $id;
    
        if ($fluid_balance_output_id === 0 || $fluid_balance_output_id === "0") {
            $this->REST_Return(400, 'fluid_balance_output_id is required');
            return;
        }

        $select = 'a.id';
        $fluid_balance_output = $this->Fluid_balance_output->select($fluid_balance_output_id, $select);

        if (count($fluid_balance_output) == 0) {
            $this->REST_Return(404, 'there is no fluid_balance_output with id ' . $fluid_balance_output_id);
            return;
        }

        $rinse = $dt_put['rinse'];
        $subtotal_first = $dt_put['subtotal_first'];
        $subtotal_second = $dt_put['subtotal_second'];
        $subtotal_third = $dt_put['subtotal_third'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance_output = array(
            'rinse'             => $rinse,
            'subtotal_first'    => $subtotal_first,
            'subtotal_second'   => $subtotal_second,
            'subtotal_third'    => $subtotal_third,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s',time()),
        );
    
        $this->Fluid_balance_output->update($fluid_balance_output_id, $dt_fluid_balance_output);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance_output,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update fluid_balance_output', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function create_output_item_post() {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_output_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $fluid_balance_output_id = $dt_post['fluid_balance_output_id'];

        $select = 'a.id';
        $fluid_balance_output = $this->Fluid_balance_output->select($fluid_balance_output_id, $select);

        if (count($fluid_balance_output) == 0) {
            $this->REST_Return(404, 'there is no fluid_balance_output with id ' . $fluid_balance_output_id);
            return;
        }

        $time = $dt_post['time'];
        $vomit = $dt_post['vomit'];
        $diarrhea = $dt_post['diarrhea'];
        $ngt_gt = $dt_post['ngt_gt'];
        $bleeding = $dt_post['bleeding'];
        $urine = $dt_post['urine'];
        $fluid_in = $dt_post['fluid_in'];
        $fluid_out = $dt_post['fluid_out'];
        $total_fluid = $dt_post['total_fluid'];
        $drain_1 = $dt_post['drain_1'];
        $drain_2 = $dt_post['drain_2'];
        $drain_3 = $dt_post['drain_3'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_fluid_balance_output_item = array(
            'fluid_balance_output_id'  => $fluid_balance_output_id,
            'time'                     => $time,
            'vomit'                    => $vomit,
            'diarrhea'                 => $diarrhea,
            'ngt_gt'                   => $ngt_gt,
            'bleeding'                 => $bleeding,
            'urine'                    => $urine,
            'fluid_in'                 => $fluid_in,
            'fluid_out'                => $fluid_out,
            'total_fluid'              => $total_fluid,
            'drain_1'                  => $drain_1,
            'drain_2'                  => $drain_2,
            'drain_3'                  => $drain_3,
            'user_added'               => $user_id,
            'date_added'               => date('Y-m-d H:i:s',time()),
            'date_updated'             => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Fluid_balance_output_item->create($dt_fluid_balance_output_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_fluid_balance_output_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create fluid_balance_output_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}