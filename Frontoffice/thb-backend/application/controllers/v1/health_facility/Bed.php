<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Bed extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/Room_model", "Room");
        $this->load->model("$this->directory/Room_setting_model", "Room_setting");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/tr/Transaction_bed_model", "Transaction_bed");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");

        $this->create_rules = array(
            array('field' => 'room_id', 'label' => 'room_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'type_status_bed_id', 'label' => 'type_status_bed_id', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->swap_rules = array(
            array('field' => 'swap_bed_id', 'label' => 'swap_bed_id', 'rules' => 'trim|required|max_length[36]'),
        );
    }

    /**
     * GET Bed with API
     * -------------------------
     * @method: GET
     */

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
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $select = 'a.id, a.registration_id, r.norm, r.icd10_id, r.icd10_name, p.fullname as patient_name, h.name as alias_name';
                $where = array('bed_id' => $output[$i]->id, 'is_checkout' => 0);
                $join = array(
                    array('registration r', 'a.registration_id = r.id', 'INNER'),
                    array('patient p', 'r.norm = p.norm', 'INNER'),
                    array('helper h', 'p.alias = h.code AND h.variable="alias"', 'LEFT'),
                );
                $registration_destination = $this->Registration_destination->select(0, $select, $where, $join);

                $output[$i]->registration_destination_id = '';
                $output[$i]->norm = '';
                $output[$i]->patient_name = '';
                $output[$i]->icd10_id = '';
                $output[$i]->icd10_name = '';
                
                if($registration_destination != []) {
                    for ($k=0; $k < count($registration_destination); $k++) { 
                        $patient_name = $registration_destination[$k]->patient_name;

                        $output[$i]->registration_destination_id = $registration_destination[$k]->id;
                        $output[$i]->norm = $registration_destination[$k]->norm;
                        $output[$i]->patient_name = $patient_name;
                        $output[$i]->icd10_id = $registration_destination[$k]->icd10_id;
                        $output[$i]->icd10_name = $registration_destination[$k]->icd10_name;                        
                        $output[$i]->icd10_name = $registration_destination[$k]->icd10_name;                        
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
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Registration_destination->whereFields['bed_id'] = $output->rows[$i]->id;
                $this->Registration_destination->whereFields['checkout_date'] = null;
                $registration_destination = $this->Registration_destination->read(0,false);
                
                $output->rows[$i]->registration_destination_id = '';
                $output->rows[$i]->norm = '';
                $output->rows[$i]->patient_name = '';
                $output->rows[$i]->icd10_id = '';
                $output->rows[$i]->icd10_name = '';
                
                if($registration_destination != []) {
                    $registration = $this->Registration->read($registration_destination[0]->registration_id);
                    
                    $patient = $this->Patient->read($registration[0]->norm,false);

                    $output->rows[$i]->registration_destination_id = $registration_destination[0]->id;
                    $output->rows[$i]->norm = $registration[0]->norm;
                    $output->rows[$i]->patient_name = $patient[0]->name;
                    $output->rows[$i]->icd10_id = $registration[0]->icd10_id;
                    $output->rows[$i]->icd10_name = $registration[0]->icd10_name;
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

    public function swap_put($p = 0) {
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
        $this->form_validation->set_rules($this->swap_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $bed_id = $id;

        if ($bed_id === 0 || $bed_id === "0") {
            $this->REST_Return(400, 'bed_id is required');
            return;
        }

        $select = 'a.id, a.type_status_bed_id, rd.id as registration_destination_id, a.room_id';
        $where = array(
            'a.is_delete' => 0,
        );
        $join = array(
            array('registration_destination rd', 'a.id = rd.bed_id AND rd.is_checkout = 0', 'LEFT'),
        );
        $bed = $this->$model->select($bed_id, $select, $where, $join);
        if (count($bed) == 0) {
            $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
            return;
        }

        $bed_status = $bed[0]->type_status_bed_id;

        $swap_bed_id = $dt_put['swap_bed_id'];

        $select = 'a.id, a.type_status_bed_id, rd.id as registration_destination_id';
        $where = array(
            'a.is_delete' => 0,
        );
        $join = array(
            array('registration_destination rd', 'a.id = rd.bed_id AND rd.is_checkout = 0', 'LEFT'),
        );
        $swap_bed = $this->$model->select($swap_bed_id, $select, $where, $join);
        if (count($swap_bed) == 0) {
            $this->REST_Return(404, 'there is no swap_bed with id ' . $swap_bed_id);
            return;
        }

        $swap_bed_status = $swap_bed[0]->type_status_bed_id;

        // $select = 'a.id';
        // $where = array(
        //     'a.bed_id' => $bed_id,
        //     'a.registration_destination_id' => $bed[0]->registration_destination_id,
        //     'a.is_delete' => 0,
        // );
        // $transaction_bed = $this->Transaction_bed->select(0, $select, $where);
        // if (count($transaction_bed) == 0) {
        //     $this->REST_Return(404, 'there is no transaction_bed with id ' . $bed_id);
        //     return;
        // }

        // $transaction_bed_id = $transaction_bed[0]->id;

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($bed); $i++) { 
            $bed_registration_destination_id = $bed[$i]->registration_destination_id;
            $bed_room_id = $bed[$i]->room_id;
            if ($bed_status == 2 || $bed_registration_destination_id != null) {
                $dt_registration_destination = array(
                    'bed_id' => $swap_bed_id
                );
                $this->Registration_destination->update_v2($bed_registration_destination_id, $dt_registration_destination);

                // $dt_transaction_bed = array(
                //     'bed_id' => $swap_bed_id
                // );
                // $this->Transaction_bed->update_v2($transaction_bed_id, $dt_transaction_bed);
    
                $dt_bed = array(
                    'type_status_bed_id' => 2,
                );
                $this->Bed->update_v2($swap_bed_id, $dt_bed);
    
                if ($swap_bed_status == 1) {
                    $dt_bed = array(
                        'type_status_bed_id' => 1,
                    );
                    $this->Bed->update_v2($bed_id, $dt_bed);
                }

                // check if empty room set room to default setting
                $this->Room_setting->reset_default_if_empty($bed_room_id);
            }
        }


        for ($i=0; $i < count($swap_bed); $i++) { 
            $swap_bed_registration_destination_id = $swap_bed[$i]->registration_destination_id;
            if ($swap_bed_status == 2 || $swap_bed_registration_destination_id != null) {
                $dt_registration_destination = array(
                    'bed_id' => $bed_id
                );
                $this->Registration_destination->update_v2($swap_bed_registration_destination_id, $dt_registration_destination);
    
                $dt_bed = array(
                    'type_status_bed_id' => 2,
                );
                $this->Bed->update_v2($bed_id, $dt_bed);
    
                if ($bed_status == 1) {
                    $dt_bed = array(
                        'type_status_bed_id' => 1,
                    );
                    $this->Bed->update_v2($swap_bed_id, $dt_bed);
                }

                // $select = 'a.id';
                // $where = array(
                //     'a.bed_id' => $bed_id,
                //     'a.registration_destination_id' => $swap_bed_registration_destination_id,
                //     'a.is_delete' => 0,
                // );
                // $transaction_bed_swap = $this->Transaction_bed->select(0, $select, $where);

                // if (count($transaction_bed_swap) == 0) {
                //     $this->REST_Return(404, 'there is no transaction_bed with id ' . $bed_id);
                //     return;
                // }

                // $transaction_swap_bed_id = $transaction_bed_swap[0]->id;
                
                // $dt_transaction_swap_bed = array(
                //     'bed_id' => $bed_id
                // );
                // $this->Transaction_bed->update_v2($transaction_swap_bed_id, $dt_transaction_swap_bed);
            }
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to swap bed', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
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

        $room_id = $dt_post['room_id'];
        $name = $dt_post['name'];

        $select = 'a.id';
        $room = $this->Room->select($room_id, $select);
        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $room_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_bed = array(
            'room_id'            => $room_id,
            'name'               => $name,
            'type_status_bed_id' => 1,
            'date_added'         => date('Y-m-d H:i:s',time()),
            'last_updated'       => date('Y-m-d H:i:s',time()),
        );

        $id = $this->$model->create($dt_bed);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_bed,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create bed', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
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

        $bed_id = $id;

        if ($bed_id === 0 || $bed_id === "0") {
            $this->REST_Return(400, 'bed_id is required');
            return;
        }

        $select = 'a.id';
        $bed = $this->$model->select($bed_id, $select);
        if (count($bed) == 0) {
            $this->REST_Return(404, 'there is no bed with id ' . $bed_id);
            return;
        }

        $name = $dt_put['name'];
        $type_status_bed_id = $dt_put['type_status_bed_id'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_bed = array(
            'name'               => $name,
            'type_status_bed_id' => $type_status_bed_id,
            'last_updated'       => date('Y-m-d H:i:s',time()),
        );

        $this->$model->update_v2($bed_id, $dt_bed);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_bed,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update bed', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
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
            $this->REST_Return(400, 'bed_id is required');
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $bed_id = $this->$model->select_id($id);

        if ($bed_id == "") {
            $this->REST_Return(404, 'there is no bed with id ' . $id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_complete();
        
        $is_delete = $dt_put['is_delete'];

        $dt_bed = array(
            'last_updated' => $user_id,
            'is_delete' => $is_delete,
        );

        $this->$model->update_v2($bed_id, $dt_bed);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_bed,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete bed', $response_data);
            return;
        }

        $response_data = array(
            'id' => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function fix_status_get($p = 0) {
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

        /* Start Transaction */
        $this->db->trans_start();

        $bed_filled = $this->db->query("
            SELECT bed_id, r.name, COUNT(*) AS total_bed, b.type_status_bed_id 
            FROM registration_destination rd 
            INNER JOIN bed b ON rd.bed_id = b.id
            INNER JOIN room r ON b.room_id = r.id
            WHERE rd.bed_id <> ''
            AND rd.bed_id IS NOT NULL
            AND rd.is_checkout = 0
            AND b.type_status_bed_id = 1
            GROUP BY bed_id
        ")->result();

        for ($i=0; $i < count($bed_filled); $i++) { 
            $bed_id = $bed_filled[$i]->bed_id;

            $dt_bed = array(
                'type_status_bed_id' => 2,
            );
            $this->$model->update_v2($bed_id, $dt_bed);
        }

        $bed_available = $this->db->query("
            SELECT b.id, b.type_status_bed_id, rd.id as registration_destination_id
            FROM bed b
            INNER JOIN room r ON b.room_id = r.id
            INNER JOIN registration_destination rd ON b.id = rd.bed_id AND rd.is_checkout = 0 AND rd.is_cancel = 1
            WHERE b.is_delete = 0 AND b.type_status_bed_id = 2 AND r.is_delete = 0
        ")->result();

        for ($i=0; $i < count($bed_available); $i++) { 
            $bed_id = $bed_available[$i]->id;

            $dt_bed = array(
                'type_status_bed_id' => 1,
            );
            $this->$model->update_v2($bed_id, $dt_bed);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to fix bed status', $response_data);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', []);
        return;
    }
}