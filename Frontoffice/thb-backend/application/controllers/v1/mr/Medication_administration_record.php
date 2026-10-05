<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Medication_administration_record extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Mar_history_model", "Mar_history");
        $this->load->model("$this->directory/Registration_model", "Registration");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/pharmacy/Medicine_types_model", "Medicine_types");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'pharmacy_prescription_id', 'label' => 'pharmacy_prescription_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->give_medicine_rules = array(
            array('field' => 'medication_administration_record_item_id', 'label' => 'medication_administration_record_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'date', 'label' => 'date', 'rules' => 'trim|required'),
            array('field' => 'time', 'label' => 'time', 'rules' => 'trim|required'),
            array('field' => 'amount', 'label' => 'amount', 'rules' => 'trim|required'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
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
    
        $mar_id = $id;
    
        if ($mar_id === 0 || $mar_id === "0") {
            $this->REST_Return(400, 'medication_administration_record_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($mar_id);
        $response_data = array();

        $select = 'a.id, a.name';
        $medicine_types = $this->Medicine_types->select(0, $select);
    
        if (count($output) != 0) {
            $medication_administration_record_id = $output[0]->id;

            $select = 'a.*';
            $where = array('medication_administration_record_id' => $medication_administration_record_id);
            $mar_item = $this->Medication_administration_record_item->select(0, $select, $where);

            for ($i=0; $i < count($mar_item); $i++) { 
                $medicine_type_id = $mar_item[$i]->medicine_type_id;
                
                for ($j=0; $j < count($medicine_types); $j++) { 
                    $medicine_type_id_tmp = $medicine_types[$j]->id;
                    $medicine_type_name = $medicine_types[$j]->name;

                    if ($medicine_type_id == $medicine_type_id_tmp) {
                        if (!isset($output[0]->medication_administration_record_items[$j])) {
                            $output[0]->medication_administration_record_items[$j] = (object) array();
                        }

                        $output[0]->medication_administration_record_items[$j]->name_type = $medicine_type_name;
                        $output[0]->medication_administration_record_items[$j]->data[] = $mar_item[$i];
                    }
                }
            }

            $output[0]->medication_administration_record_items = array_values($output[0]->medication_administration_record_items);
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
        $output = $this->$model->read_ssr($id);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function list_history_get($p = 0){
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
    
        if(isset($q_strings['limit'])) $this->Mar_history->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->Mar_history->offset = $q_strings['offset']; unset($q_strings['offset']);
    
        $this->Mar_history->whereFields = $q_strings;
        $output = $this->Mar_history->read_ssr($id);
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function reconciliation_get($p = 0) {
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
    
        $registration_id = $id;

        if ($registration_id === 0 || $registration_id === "0") {
            $this->REST_Return(400, 'registration_id is required');
            return;
        }

        $select = 'a.id';
        $registration = $this->Registration->select($registration_id, $select);

        if (count($registration) == 0) {
            $this->REST_Return(404, 'there is no registration with id ' . $registration_id);
            return;
        }

        $select = 'a.*, r.id as registration_id, hf.name as health_facility_name';
        $where = array('r.id' => $registration_id);
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
            array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
            array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
        );
        $mar = $this->$model->select(0, $select, $where, $join);

        $output = array(
            array(
                'no' => 1,
                'data' => array(),
            ),
            array(
                'no' => 2,
                'data' => array(),
            ),
            array(
                'no' => 3,
                'data' => array(),
            ),
        );

        for ($i=0; $i < count($mar); $i++) { 
            $mar_id = $mar[$i]->id;
            $health_facility_name = $mar[$i]->health_facility_name;

            $select = 'a.id, pc.name as pharmacy_catalog_name, a.quantity, a.current_quantity, a.signa, mt.name as medicine_type_name, a.stopped_date';
            $where = array('medication_administration_record_id' => $mar_id);
            $join = array(
                array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
                array('medicine_types mt', 'a.medicine_type_id = mt.id', 'INNER'),
            );
            $mar_item = $this->Medication_administration_record_item->select(0, $select, $where, $join);

            for ($j=0; $j < count($mar_item); $j++) { 
                $mar_item_id = $mar_item[$j]->id;

                $select = 'a.id, a.date_added';
                $where = array('medication_administration_record_item_id' => $mar_item_id);
                $join = array();
                $order = array(
                    'date_added' => 'DESC',
                );
                $mar_history = $this->Mar_history->select(0, $select, $where, $join, $order);

                $mar_history_date_added = $mar_history[0]->date_added;

                $mar_item[$j]->last_give_date = $mar_history_date_added;
            }

            $mar[$i]->items = $mar_item;

            if ($health_facility_name == 'IGD' || $health_facility_name == 'RAWAT JALAN') {
                array_push($output[0]['data'], $mar[$i]);
            } elseif ($health_facility_name == 'RAWAT INAP') {
                array_push($output[1]['data'], $mar[$i]);
            }
        }

        $select = 'a.id, a.no_order, ';
        $where = array('r.id' => $registration_id, 'a.flag' => 1);
        $join = array(
            array('registration_destination rd', 'a.registration_destination_id = rd.id', 'INNER'),
            array('registration r', 'rd.registration_id = r.id', 'INNER'),
        );
        $order = array(
            'date_added' => 'DESC',
        );
        $this->Prescription->limit = 1;
        $prescription = $this->Prescription->select(0, $select, $where, $join);

        $prescription_id = $prescription[0]->id;

        $select = 'a.pharmacy_catalog_id, pc.name as pharmacy_catalog_name, a.qty, a.used';
        $where = array('a.is_delete' => 0);
        $join = array(
            array('pharmacy_catalog pc', 'a.pharmacy_catalog_id = pc.id', 'INNER'),
        );
        $prescription_item = $this->Prescription_item->select($prescription_id, $select);

        $prescription[0]->prescription_items = $prescription_item;

        array_push($output[2]['data'], $prescription);

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
        $pharmacy_prescription_id = $dt_post['pharmacy_prescription_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);

        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id, a.date_added';
        $prescription = $this->Prescription->select($pharmacy_prescription_id, $select);

        if (count($prescription) == 0) {
            $this->REST_Return(404, 'there is no pharmacy_prescription with id ' . $pharmacy_prescription_id);
            return;
        }
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_mar = array(
            'registration_destination_id' => $registration_destination_id,
            'pharmacy_prescription_id' => $pharmacy_prescription_id,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_mar);

        $select = 'a.*';
        $prescription_item = $this->Prescription_item->select($pharmacy_prescription_id, $select);

        for ($i=0; $i < count($prescription_item); $i++) { 
            $pharmacy_catalog_id = $prescription_item[$i]->pharmacy_catalog_id;
            $medicine_type_id = '9e3822c7-1539-407a-9e35-ba8f4c40ece0'; // FIXME master medicine_type from catalog
            $prescription_date = $prescription[0]->date_added;
            $signa = $prescription_item[$i]->used;
            $quantity = $prescription_item[$i]->qty;
            $current_quantity = $quantity;

            $dt_mar_item = array(
                'medication_administration_record_id' => $id,
                'pharmacy_catalog_id' => $pharmacy_catalog_id,
                'medicine_type_id' => $medicine_type_id,
                'prescription_date' => $prescription_date,
                'signa' => $signa,
                'quantity' => $quantity,
                'current_quantity' => $current_quantity,
                'user_added' => $user_id,
                'date_added' => date('Y-m-d H:i:s',time()),
                'date_updated' => date('Y-m-d H:i:s',time()),
            );

            $this->Medication_administration_record_item->create($dt_mar_item);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_mar,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create medication_administration_record', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }

    public function give_medicine_post() {
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
        $this->form_validation->set_rules($this->give_medicine_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $medication_administration_record_item_id = $dt_post['medication_administration_record_item_id'];
        $employee_nurse_id = $dt_post['employee_nurse_id'];
        $date = $dt_post['date'];
        $time = $dt_post['time'];
        $amount = $dt_post['amount'];

        $select = 'a.id, a.current_quantity';
        $mar_item = $this->Medication_administration_record_item->select($medication_administration_record_item_id, $select);
        
        if (count($mar_item) == 0) {
            $this->REST_Return(404, 'there is no medication_administration_record_item with id ' . $medication_administration_record_item_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($employee_nurse_id, $select);
        
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $employee_nurse_id);
            return;
        }

        $current_quantity = $mar_item[0]->current_quantity;

        if ($current_quantity == 0) {
            $this->REST_Return(409, 'no medicine left with id ' . $medication_administration_record_item_id);
            return;
        }

        $current_current_quantity = (float) $current_quantity - (float) $amount;

        if ($current_current_quantity < 0) {
            $this->REST_Return(409, 'not enough medicine to fulfill the request amount id ' . $medication_administration_record_item_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_mar_item = array(
            'current_quantity' => $current_current_quantity,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s',time()),
        );

        $this->Medication_administration_record_item->update($medication_administration_record_item_id, $dt_mar_item);
    
        $dt_mar_history = array(
            'medication_administration_record_item_id' => $medication_administration_record_item_id,
            'employee_nurse_id' => $employee_nurse_id,
            'date' => $date,
            'time' => $time,
            'amount' => $amount,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->Mar_history->create($dt_mar_history);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_mar_history,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create mar_history', $response_data);
            return;
        }
    
        $response_data = array(
            'mar_history_id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }
}