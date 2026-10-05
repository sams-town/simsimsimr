<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Room extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_setting_model", "{$this->name}_setting");
        $this->load->model("$this->directory/Bed_model", "Bed");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/setting/Health_facility_item_model", "Health_facility_item");
        $this->load->model("v1/setting/Helper_model", "Helper");
        $this->load->model("v1/setting/Service_class_model", "Service_class");
        $this->load->model("v1/hr/Employee_model", "Employee");
        // Load Helper
        $this->load->helper('logger');

        $this->create_rules = array(
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'costing_id', 'label' => 'costing_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'service_class_id', 'label' => 'service_class_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'type_room_id', 'label' => 'type_room_id', 'rules' => 'trim|required'),
            array('field' => 'is_surgery', 'label' => 'is_surgery', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_isolation', 'label' => 'is_isolation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'gender_availability', 'label' => 'gender_availability', 'rules' => 'trim|max_length[1]'),
            array('field' => 'room_pressure', 'label' => 'room_pressure', 'rules' => 'trim|max_length[1]'),
        );

        $this->update_rules = array(
            array('field' => 'health_facility_item_id', 'label' => 'health_facility_item_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'costing_id', 'label' => 'costing_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'service_class_id', 'label' => 'service_class_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|required'),
            array('field' => 'type_room_id', 'label' => 'type_room_id', 'rules' => 'trim|required'),
        );

        $this->update_temp_rules = array(
            array('field' => 'is_surgery', 'label' => 'is_surgery', 'rules' => 'trim|required|max_length[1]'),
            // array('field' => 'is_internal', 'label' => 'is_internal', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_isolation', 'label' => 'is_isolation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'gender_availability', 'label' => 'gender_availability', 'rules' => 'trim|max_length[1]'),
            array('field' => 'room_pressure', 'label' => 'room_pressure', 'rules' => 'trim|max_length[1]'),
            array('field' => 'costing_id', 'label' => 'costing_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'service_class_id', 'label' => 'service_class_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );

        $this->update_setting_rules = array(
            array('field' => 'is_surgery', 'label' => 'is_surgery', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'is_isolation', 'label' => 'is_isolation', 'rules' => 'trim|required|max_length[1]'),
            array('field' => 'gender_availability', 'label' => 'gender_availability', 'rules' => 'trim|max_length[1]'),
            array('field' => 'room_pressure', 'label' => 'room_pressure', 'rules' => 'trim|max_length[1]'),
        );
    }

    /**
     * Room API
     * --------------------
     * @param: health_facility_item_id
     * @param: costing_id
     * @param: service_class_id
     * @param: type_room_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/health_facility/room
     **/

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

        $health_facility_item_id = $dt_post['health_facility_item_id'];
        $costing_id = $dt_post['costing_id'];
        $service_class_id = $dt_post['service_class_id'];
        $is_surgery = $dt_post['is_surgery'] ?? null;
        $is_internal = $dt_post['is_internal'] ?? null;
        $is_isolation = $dt_post['is_isolation'] ?? null;
        $gender_availability = $dt_post['gender_availability'] ?? null;
        $room_pressure = $dt_post['room_pressure'] ?? null;
        
        $select = 'a.id';
        $health_facility_item = $this->Health_facility_item->select($health_facility_item_id, $select);
        if (count($health_facility_item) == 0) {
            $this->REST_Return(404, 'there is no health_facility_item with id ' . $health_facility_item_id);
            return;
        }

        $select = 'a.id';
        $costing = $this->Costing->select($costing_id, $select);
        if (count($costing) == 0) {
            $this->REST_Return(404, 'there is no costing with id ' . $costing_id);
            return;
        }

        $select = 'a.id';
        $service_class = $this->Service_class->select($service_class_id, $select);
        if (count($service_class) == 0) {
            $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
            return;
        }
        
        $name = $dt_post['name'];
        $type_room_id = $dt_post['type_room_id'];
    
        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_room = array(
            'health_facility_item_id'   => $health_facility_item_id,
            'costing_id'                => $costing_id,
            'service_class_id'          => $service_class_id,
            'name'                      => $name,
            'type_room_id'              => $type_room_id,
            'is_surgery'                => $is_surgery,
            'is_internal'               => $is_internal,
            'is_isolation'              => $is_isolation,
            'gender_availability'       => ($gender_availability == '') ? null: $gender_availability,
            'room_pressure'             => $room_pressure,
            'date_added'                => date('Y-m-d H:i:s',time()),
            'last_updated'              => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_room);

        // Room Setting
        $dt_room_setting = array(
            'room_id' => $id,
            'is_surgery' => $is_surgery,
            'is_internal' => $is_internal,
            'is_isolation' => $is_isolation,
            'costing_id' => $costing_id,
            'service_class_id' => $service_class_id,
            'gender_availability' => ($gender_availability == '') ? null: $gender_availability,
            'room_pressure' => $room_pressure,
        );

        $this->Room_setting->replace($dt_room_setting);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create room', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Room with API
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            // Helper table
            $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = '0';
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $this->Bed->whereFields['a.room_id'] = $output[$i]->id;
                $this->Bed->whereFields['b.institute_branch_id'] = $institute_branch_id;
                $this->Bed->whereFields['a.is_delete'] = '0';
                $bed = $this->Bed->read(0,false);
                if ($bed != []) {
                    for ($j=0; $j < count($bed); $j++) { 
                        $output[$i]->beds[$j]['id'] = $bed[$j]->id;
                        $output[$i]->beds[$j]['name'] = $bed[$j]->name;
                        $output[$i]->beds[$j]['type_status_bed_id'] = $bed[$j]->type_status_bed_id;                        
                        $output[$i]->beds[$j]['type_status_bed_name'] = $bed[$j]->type_status_bed_name;                        
                        $output[$i]->beds[$j]['date_added'] = $bed[$j]->date_added;
                        $output[$i]->beds[$j]['last_updated'] = $bed[$j]->last_updated;
                        $output[$i]->beds[$j]['is_delete'] = $bed[$j]->is_delete;
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
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $limit; 
            unset($q_strings['limit']);
        } 
        
        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $offset; 
            unset($q_strings['offset']);
        } 
    
        if(isset($q_strings['name'])) {
            $this->$model->whereLike['a.name'] = $q_strings['name'];
            unset($q_strings['name']);
        }

        $where_main = $q_strings;

        $select = 'a.*, sc.name as service_class_name, h.name as type_room_name';
        $where_main['a.is_delete'] = 0;
        $join_main = array(
            array('service_class sc', 'a.service_class_id = sc.id', 'LEFT'),
            array('helper h', 'a.type_room_id = h.code AND h.variable="type_room"', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where_main, $join_main);

        // sorting room number from 1
        usort($output, function($a, $b) {
			// Extract the numbers from the strings
			$numA = (int) filter_var($a->name, FILTER_SANITIZE_NUMBER_INT);
			$numB = (int) filter_var($b->name, FILTER_SANITIZE_NUMBER_INT);
			
			return $numB - $numA;
		});

        for ($i=0; $i < count($output); $i++) {
            $room_id = $output[$i]->id;

            $select = 'a.id, a.name, a.type_status_bed_id, h.name as type_status_bed_name, a.date_added, a.last_updated';
            $where = array(
                'a.room_id' => $room_id,
                'a.is_delete' => 0,
            );
            $join = array(
                array('helper h', 'a.type_status_bed_id = h.code AND h.variable="type_status_bed"', 'INNER'),
            );
            $beds = $this->Bed->select(0, $select, $where, $join);

            $output[$i]->beds = $beds;

            $select = 'a.is_surgery, a.is_isolation, a.is_internal, a.gender_availability, a.room_pressure, a.costing_id, a.service_class_id, sc.name as service_class_name';
            $where = array();
            $join = array(
                array('service_class sc', 'a.service_class_id = sc.id', 'LEFT'),
            );
            $room_setting = $this->Room_setting->select($room_id, $select, $where, $join);
            if (count($room_setting) == 0) {
                $dt_room_setting = array(
                    'room_id' => $room_id,
                );
                $this->Room_setting->replace($dt_room_setting);
            }

            $output[$i]->is_surgery = $room_setting[0]->is_surgery;
            $output[$i]->is_isolation = $room_setting[0]->is_isolation;
            $output[$i]->is_internal = $room_setting[0]->is_internal;
            $output[$i]->gender_availability = $room_setting[0]->gender_availability;
            $output[$i]->room_pressure = $room_setting[0]->room_pressure;
            $output[$i]->costing_id = $room_setting[0]->costing_id;
            $output[$i]->service_class_id = $room_setting[0]->service_class_id;
            $output[$i]->service_class_name = $room_setting[0]->service_class_name;
        }
        
        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where_main, $join_main)[0]->iTotal;

		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    // public function list_get($p = 0){
	// 	header("Access-Control-Allow-Origin: *");
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = $this->name;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
    //         if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
    //         if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
    //         // $this->$model->whereLike = $q_strings;
    //         if(isset($q_strings['name'])) {
    //             $this->$model->whereLike['a.name'] = $q_strings['name'];
    //             unset($q_strings['name']);
    //         }
    //         if(isset($q_strings['type_room_id'])) {
    //             $this->$model->whereFields['a.type_room_id'] = $q_strings['type_room_id'];
    //             unset($q_strings['type_room_id']);
    //         }
    //         $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
    //         // Helper table
    //         $this->$model->whereFields['b.institute_branch_id'] = $institute_branch_id;
    //         $this->$model->whereFields['a.is_delete'] = '0';
    //         $output = $this->$model->read_ssr($id);
    //         $response_data = array();
    //         for ($i=0; $i < count($output->rows); $i++) {
    //             $this->Bed->whereFields['a.room_id'] = $output->rows[$i]->id;
    //             $this->Bed->whereFields['b.institute_branch_id'] = $institute_branch_id;
    //             $this->Bed->whereFields['a.is_delete'] = '0';
    //             $bed = $this->Bed->read(0,false);
    //             if ($bed != []) {
    //                 for ($j=0; $j < count($bed); $j++) { 
    //                     $output->rows[$i]->beds[$j]['id'] = $bed[$j]->id;
    //                     $output->rows[$i]->beds[$j]['name'] = $bed[$j]->name;
    //                     $output->rows[$i]->beds[$j]['type_status_bed_id'] = $bed[$j]->type_status_bed_id;                        
    //                     $output->rows[$i]->beds[$j]['type_status_bed_name'] = $bed[$j]->type_status_bed_name;                        
    //                     $output->rows[$i]->beds[$j]['date_added'] = $bed[$j]->date_added;
    //                     $output->rows[$i]->beds[$j]['last_updated'] = $bed[$j]->last_updated;
    //                     $output->rows[$i]->beds[$j]['is_delete'] = $bed[$j]->is_delete;
    //                 }
    //             } 
    //         }
    //         if ($output->rows != [] && $id != 0) {
    //             $response_data = $output->rows[0];
    //             $error   = false;
    //         } else if($output->rows != [] or $id == 0) {
    //             $response_data = $output;
    //             $error   = false;
    //         } else {
    //             $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
    //             return;
    //         }
    //         if ($error == true) {
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $this->REST_Return(200, 'SUCCESS', $response_data);
    //         }
    //         return;
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
	// }

    public function list_v2_get($p = 0){
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
    
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $q_strings['limit']; 
            unset($q_strings['limit']);
        }
        
        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $q_strings['offset']; 
            unset($q_strings['offset']);
        }
    
        $where = $q_strings;
        $where['a.is_delete'] = 0;

        $select = 'a.id, a.name, sc.id as service_class_id, sc.name as service_class_name';
        $join = array(
            array('service_class sc', 'a.service_class_id = sc.id', 'LEFT'),
        );
        $order = array('a.name' => 'ASC');
        $output = $this->$model->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
            $room_id = $output[$i]->id;

            $select_bed = 'a.id, a.type_status_bed_id, a.last_updated';
            $where_bed = array(
                'a.room_id' => $room_id,
                'a.is_delete' => 0,
            );
            $bed = $this->Bed->select(0, $select_bed, $where_bed);

            $available = 0;
            $capacity = count($bed);

            $bed_last_updated = '1970-01-01';

            for ($k=0; $k < count($bed); $k++) { 
                $bed_status = $bed[$k]->type_status_bed_id;
                $tmp_last_updated = $bed[$k]->last_updated;

                if (strtotime($tmp_last_updated) > strtotime($bed_last_updated)) {
                    $bed_last_updated = $tmp_last_updated;
                }

                if ($bed_status == 1) {
                    $available += 1;
                }
            }

            $output[$i]->capacity = $capacity;
            $output[$i]->available = $available;
            $output[$i]->last_updated = $bed_last_updated;
        }
        
        $response_data = $output;

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

    public function ward_get($p = 0){
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

        $where = array();
    
        if(isset($q_strings['type_room_id'])) {
            $where['a.code'] = $q_strings['type_room_id'];
        }

        $select = 'a.id, a.code as type_room_id, a.name';
        $where['a.variable'] = 'type_room';
        $where['a.is_delete'] = 0;
        $join = array();
        $order = array('a.name' => 'ASC');
        $type_room = $this->Helper->select(0, $select, $where, $join, $order);

        $output = $type_room;

        for ($i=0; $i < count($output); $i++) { 
            $available = 0;
            $filled = 0;

            $type_room_id = $output[$i]->type_room_id;

            $select = 'a.id, a.name, a.health_facility_item_id, hfi.name as health_facility_item_name';
            $where = array('type_room_id' => $type_room_id, 'a.is_delete' => 0);
            $join = array(
                array('health_facility_item hfi', 'a.health_facility_item_id = hfi.id', 'INNER'),
            );
            $room = $this->$model->select(0, $select, $where, $join, $order);

            for ($k=0; $k < count($room); $k++) { 
                $room_id = $room[$k]->id;
                $health_facility_item_id = $room[$k]->health_facility_item_id;
                $health_facility_item_name = $room[$k]->health_facility_item_name;

                $output[$i]->health_facility_item_id = $health_facility_item_id;
                $output[$i]->health_facility_item_name = $health_facility_item_name;

                $room_available = 0;
                $room_filled = 0;

                $select = 'a.id, a.type_status_bed_id, a.name';
                $where = array('room_id' => $room_id, 'a.is_delete' => 0);
                $join = array();
                $bed = $this->Bed->select(0, $select, $where, $join, $order);

                for ($b=0; $b < count($bed); $b++) { 
                    $type_status_bed_id = $bed[$b]->type_status_bed_id;
                    
                    if ($type_status_bed_id == 1) {
                        $room_available += 1;
                    } elseif ($type_status_bed_id == 2) {
                        $room_filled += 1;
                    }
                }

                $room[$k]->available = $room_available;
                $room[$k]->filled = $room_filled;

                $available += $room_available;
                $filled += $room_filled;
            }

            $output[$i]->available = $available;
            $output[$i]->filled = $filled;
            $output[$i]->total = $available + $filled;
        }
        
        $response_data = $output;
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Room with API
     * -------------------------
     * @method: GET
     */

     public function monitoring_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['type_room_id'])) {
                $output = $this->$model->room_monitoring($q_strings['type_room_id']);
            } else {
                $output = $this->$model->room_monitoring();
            }
            $response_data = $output;
            $error = false;
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

    public function class_monitoring_get($p = 0) {
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
    
        $select = 'a.id, a.name';
        $where = array(
            'a.name !=' => '.'
        );
        $order = array('a.index' => 'ASC');
        $output = $this->Service_class->select(0, $select, $where,array(),$order);

        for ($i=0; $i < count($output); $i++) {
            $service_class_id = $output[$i]->id;
            $service_class_name = $output[$i]->name;

            $select = 'a.id';
            $where = array(
                'a.service_class_id' => $service_class_id,
                'a.is_delete' => 0,
            );
            $room = $this->Room->select(0, $select, $where);

            $total = 0;
            $available = 0;

            for ($j=0; $j < count($room); $j++) { 
                $room_id = $room[$j]->id;

                $select = 'a.id, a.type_status_bed_id';
                $where = array(
                    'a.room_id' => $room_id,
                    'a.is_delete' => 0,
                );
                $bed = $this->Bed->select(0, $select, $where);

                $total += count($bed);

                for ($k=0; $k < count($bed); $k++) { 
                    $bed_status = $bed[$k]->type_status_bed_id;

                    if ($bed_status == 1) {
                        $available += 1;
                    }
                }
            }
            $output[$i]->total = $total;
            $output[$i]->available = $available;
        }

        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * GET Room with API
     * -------------------------
     * @method: GET
     */

     public function type_monitoring_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $output = $this->$model->type_room_monitoring();
            $response_data = $output;
            $error = false;
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

    public function aplicares_sync_room_get($p = '0') {
        $this->load->library('Vclaim_aplicares','vclaim_aplicares');
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        Logger::info($this->debug_uniq, "|---- start aplicares sync room ----|");

        Logger::info($this->debug_uniq, "hit: " . "/rest/bed/read/0070R032/1/100");

        $request_rest_bed = $this->vclaim_aplicares->request("/rest/bed/read/0070R032/1/100", "GET");
        $response_rest_bed = json_decode($request_rest_bed, true) ?? [];

        Logger::info($this->debug_uniq, "res: ", $response_rest_bed);

        $rest_bed_message = $response_rest_bed['metadata']['message'];
        if ($rest_bed_message != 'OK') {
            $this->REST_Return(422, 'failed to get aplicares_rest_bed', $response_rest_bed);
            return;
        }

        // $this->REST_Return(200, 'ok', $response_rest_bed);
        // return;

        $rest_bed_list = $response_rest_bed['response']['list'];

        $data = array();

        $select = 'a.id, a.code as type_room_id, a.name, a.description';
        $where = array('variable' => 'type_room', 'a.is_delete' => 0);
        $join = array();
        $order = array('a.name' => 'ASC');
        $type_room = $this->Helper->select(0, $select, $where, $join, $order);

        $payload_list = array();
        $total_create = 0;
        $total_update = 0;

        $is_error = 0;

        for ($i=0; $i < count($type_room); $i++) { 
            $type_room_id = $type_room[$i]->type_room_id;
            $group_name = $type_room[$i]->name;
            $group_code = $type_room[$i]->description;

            Logger::info($this->debug_uniq, "processing $group_name");

            $select = 'a.id, a.name, a.service_class_id, a.health_facility_item_id';
            $where = array('a.type_room_id' => $type_room_id, 'a.is_delete' => 0);
            $room = $this->$model->select(0, $select, $where);

            $room_list = array();

            for ($j=0; $j < count($room); $j++) { 
                $room_id = $room[$j]->id;
                $room_service_class_id = $room[$j]->service_class_id;
                $health_facility_item_id = $room[$j]->health_facility_item_id;

                $select = 'a.id, a.code';
                $service_class = $this->Service_class->select($room_service_class_id, $select);

                $service_class_code = $service_class[0]->code;

                $select = 'a.id, a.type_status_bed_id';
                $where = array('a.room_id' => $room_id, 'a.is_delete' => 0);
                $bed = $this->Bed->select(0, $select, $where);

                $total_bed = count($bed);
                $available = 0;

                for ($b=0; $b < $total_bed; $b++) { 
                    $bed_status = $bed[$b]->type_status_bed_id;

                    if ($bed_status == 1) {
                        $available += 1;
                    }
                }

                // NOTE - HARDCODE CLASS INTENSIF CARE
                if ($health_facility_item_id == '6c7d7e53-ff24-4246-85b2-07ea77ac801f') { // ICU
                    $service_class_code = 'ICU';
                } elseif ($health_facility_item_id == 'ff1f252e-daab-4bdb-b308-e9f4cb11a47d') { //ICU ISOLASI
                    $service_class_code = 'ISO';
                } elseif ($health_facility_item_id == '7f2a9f80-c30d-4cbd-9f0d-364cda2ec84c') { //ICCU
                    $service_class_code = 'ICC';
                } elseif ($health_facility_item_id == '5515af72-6dcb-415d-89e4-2433e7b0149d') { //NICU
                    $service_class_code = 'NIC';
                } elseif ($health_facility_item_id == 'ed40f235-1c7b-4954-aebf-b3dc70a674b7') { //NICU ISOLASI
                    $service_class_code = 'ISO';
                } elseif ($health_facility_item_id == 'e66e6ed4-4ef4-4375-a6d0-0445e3ec3039') { //PICU
                    $service_class_code = 'PIC';
                }

                if (key_exists($service_class_code, $room_list) != 1) {
                    $room_list[$service_class_code] = array(
                        'total' => 0,
                        'available' => 0,
                    );
                }
                
                $room_list[$service_class_code]['total'] += $total_bed;
                $room_list[$service_class_code]['available'] += $available;
            }

            // if ($type_room_id == 1) {
            //     var_dump($room_list);
            //     die();
            // }

            $payload = array(
                'koderuang'          => $group_code,
                'namaruang'          => $group_name,
                'tersediapria'       => 0,
                'tersediawanita'     => 0,
            );

            // if ($type_room_id != 1) {
            //     continue;
            // }

            Logger::info($this->debug_uniq, "room list: ", $room_list);

            // var_dump($room_list);
            // die();

            $count = 0;
            foreach ($room_list as $key => $val) {
                $count += 1;
                if (count($room_list) > 1) {
                    $payload['koderuang'] = $group_code . '-' . $key;
                }

                $payload['kodekelas'] = $key;
                $payload['kapasitas'] = $val['total'];
                $payload['tersedia'] = $val['available'];
                $payload['tersediapriawanita'] = $val['available'];

                // if ($key == 'KL1') {
                //     var_dump($payload);
                //     die();
                // }

                // var_dump(count($room_list));
                // die();

                Logger::info($this->debug_uniq, "payload $count: ", $payload);

                $exist = 0;
                for ($j=0; $j < count($rest_bed_list); $j++) { 
                    $rest_bed_code_class = $rest_bed_list[$j]['kodekelas'];
                    $rest_bed_code = $rest_bed_list[$j]['koderuang'];

                    if (count($room_list) > 1) {
                        if ($rest_bed_code == $group_code . '-' . $key && $rest_bed_code_class == $key) {
                            $exist = 1;
                        }
                    } else {
                        if ($rest_bed_code == $group_code && $rest_bed_code_class == $key) {
                            $exist = 1;
                        }
                    }
                }

                // if ($key == 'KL1') {
                //     var_dump($exist);
                //     die();
                // }

                if ($exist == 0) {
                    Logger::info($this->debug_uniq, "trying to create payload $count");
                    Logger::info($this->debug_uniq, "hit: " . "/rest/bed/create/0070R032");

                    // $payload['maudiapain'] = 'create';
                    $total_create += 1;
                    $retry = 3;
                    while (true) {
                        $r_create_rest_bed = $this->vclaim_aplicares->request("/rest/bed/create/0070R032", "POST", json_encode($payload));
                        $res_create_rest_bed = json_decode($r_create_rest_bed, true) ?? [];

                        Logger::info($this->debug_uniq, "res: ", $res_create_rest_bed);
    
                        $rest_bed_message = $res_create_rest_bed['metadata']['message'];
    
                        if ($rest_bed_message != 'Data berhasil disimpan.') {
                            Logger::error($this->debug_uniq, "failed to create aplicares_rest_bed");
                            Logger::error($this->debug_uniq, "trying to create again");
                            $retry -= 1;
                            $is_error = 1;
                            // $this->REST_Return(422, 'failed to create aplicares_rest_bed', $res_create_rest_bed);
                            // return;
                        } else {
                            break;
                        }

                        if ($retry == 0) {
                            break;
                        }
                    }

                } else {
                    Logger::info($this->debug_uniq, "trying to update payload $count");
                    Logger::info($this->debug_uniq, "hit: " . "/rest/bed/update/0070R032");

                    // $payload['maudiapain'] = 'update';
                    $total_update += 1;
                    $retry = 3;
                    while (true) {
                        $r_update_rest_bed = $this->vclaim_aplicares->request("/rest/bed/update/0070R032", "POST", json_encode($payload));
                        $res_update_rest_bed = json_decode($r_update_rest_bed, true) ?? [];
    
                        Logger::info($this->debug_uniq, "res: ", $res_update_rest_bed);
    
                        $rest_bed_message = $res_update_rest_bed['metadata']['message'];
    
                        if ($rest_bed_message != 'Data berhasil diupdate.') {
                            Logger::error($this->debug_uniq, "failed to update aplicares_rest_bed");
                            Logger::error($this->debug_uniq, "trying to update again");
                            $retry -= 1;
                            $is_error = 1;
                            // $this->REST_Return(422, 'failed to update aplicares_rest_bed', $res_update_rest_bed);
                            // return;
                        } else {
                            break;
                        }

                        if ($retry == 0) {
                            break;
                        }
                        
                    }
                }

                array_push($payload_list, $payload);
            }
        }

        $rest_bed_delete_list = $rest_bed_list;
        
        for ($i=0; $i < count($rest_bed_list); $i++) { 
            $rest_bed_code_class = $rest_bed_list[$i]['kodekelas'];
            $rest_bed_code = $rest_bed_list[$i]['koderuang'];

            for ($j=0; $j < count($payload_list); $j++) { 
                $kodekelas = $payload_list[$j]['kodekelas'];
                $koderuang = $payload_list[$j]['koderuang'];

                if ($rest_bed_code == $koderuang && $rest_bed_code_class == $kodekelas) {
                    unset($rest_bed_delete_list[$i]);
                }
            }
        }

        $rest_bed_delete_list = array_values($rest_bed_delete_list);

        Logger::info($this->debug_uniq, "room list delete: ", $rest_bed_delete_list);

        for ($i=0; $i < count($rest_bed_delete_list); $i++) {      
            if ($is_error != 0) {
                Logger::info($this->debug_uniq, "error detected, not deleting...");
                break;
            }

            $kodekelas = $rest_bed_delete_list[$i]['kodekelas'];
            $koderuang = $rest_bed_delete_list[$i]['koderuang'];

            $payload_delete = array(
                'kodekelas' => $kodekelas,
                'koderuang' => $koderuang,
            );

            Logger::info($this->debug_uniq, "trying to delete payload: ", $payload_delete);
            Logger::info($this->debug_uniq, "hit: " . "/rest/bed/delete/0070R032");
            $retry = 3;
            while (true) {
                $r_delete_rest_bed = $this->vclaim_aplicares->request("/rest/bed/delete/0070R032", "POST", json_encode($payload_delete));
                $res_delete_rest_bed = json_decode($r_delete_rest_bed, true);
    
                $rest_bed_message = $res_delete_rest_bed['metadata']['message'];
    
                if ($rest_bed_message != 'Data berhasil dihapus.') {
                    Logger::error($this->debug_uniq, "failed to delete aplicares_rest_bed");
                    Logger::error($this->debug_uniq, "trying to delete again");
                    $retry -= 1;
                    // $this->REST_Return(422, 'failed to delete aplicares_rest_bed', $res_delete_rest_bed);
                    // return;
                } else {
                    break;
                }

                if ($retry == 0) {
                    break;
                }
            }
        }

        Logger::info($this->debug_uniq, "|---- end aplicares sync room ----|");

        $response_data = array(
            'total_create' => $total_create,
            'total_update' => $total_update,
            'total_delete' => count($rest_bed_delete_list),
        );

        $this->REST_Return(200, 'Synchronization successful!', $response_data);
        return;
    }

    public function availability_get($p = 0) {
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
    
        $select = 'a.id, a.code as type_room_id, a.name, a.description';
        $where = array('variable' => 'type_room', 'a.is_delete' => 0);
        $join = array();
        $order = array('a.name' => 'ASC');
        $type_room = $this->Helper->select(0, $select, $where, $join, $order);

        $output = [];
        $data = [];

        for ($i=0; $i < count($type_room); $i++) { 
            $code = $type_room[$i]->type_room_id;
            $name = $type_room[$i]->name;

            $data[$name] = array();

            $room_total = $this->db->query("
                SELECT sc.name, sc.index, COUNT(*) as total
                FROM room r
                INNER JOIN bed b ON r.id = b.room_id
                INNER JOIN service_class sc ON r.service_class_id = sc.id
                WHERE r.type_room_id = '$code'
                AND r.is_delete = 0
                AND b.is_delete = 0
                GROUP BY r.service_class_id 
                ORDER BY sc.index ASC
            ")->result();

            for ($j=0; $j < count($room_total); $j++) { 
                $service_class_name = $room_total[$j]->name;
                $total = $room_total[$j]->total;
                $service_class_index = $room_total[$j]->index;

                $data[$name][$service_class_name] = array(
                    'total' => $total,
                    'available' => 0,
                    'index' => $service_class_index,
                );

            }

            $room_available = $this->db->query("
                SELECT sc.name, sc.index, COUNT(*) as available
                FROM room r
                INNER JOIN service_class sc ON r.service_class_id = sc.id
                LEFT JOIN bed b ON r.id = b.room_id
                WHERE r.type_room_id = '$code'
                AND r.is_delete = 0
                AND b.type_status_bed_id = 1
                AND b.is_delete = 0
                GROUP BY r.service_class_id
                ORDER BY sc.index ASC
            ")->result();

            if (count($room_available) == 0) {
                $data[$name][$service_class_name]['available'] = 0;
            }

            for ($j=0; $j < count($room_available); $j++) { 
                $service_class_name = $room_total[$j]->name;
                $available = $room_available[$j]->available;
                $service_class_index = $room_available[$j]->index;

                $data[$name][$service_class_name]['available'] = $available;
                $data[$name][$service_class_name]['service_class_index'] = $service_class_index;
            }
        }

        foreach ($data as $key => $val) {
            foreach ($val as $key_item => $val_item) {
                $dt_output = array(
                    'name' => $key,
                    'class' => $key_item,
                    'total' => $val_item['total'],
                    'available' => $val_item['available'],
                    'index' => $val_item['index'],
                );

                array_push($output, $dt_output);
            }
        }

        usort($output, function ($a, $b) {
            $aClass = is_numeric($a['class']) ? (int)$a['class'] : PHP_INT_MAX;
            $bClass = is_numeric($b['class']) ? (int)$b['class'] : PHP_INT_MAX;
            // return $aClass <=> $bClass;
            return $a['index'] <=> $b['index'];
        });

        // usort($output, function ($a, $b) {
        // });
        
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    /**
     * Room API
     * --------------------
     * @param: health_facility_item_id
     * @param: costing_id
     * @param: service_class_id
     * @param: type_room_id
     * @param: name
     * --------------------
     * @method : POST
     * @link: version/mr/room
     **/
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
    
        $room_id = $id;
    
        if ($room_id === 0 || $room_id === "0") {
            $this->REST_Return(400, 'room_id is required');
            return;
        }

        $select = 'a.id';
        $where = array('a.is_delete' => 0);
        $room = $this->$model->select($room_id, $select, $where);

        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $room_id);
            return;
        }

        $health_facility_item_id = $dt_put['health_facility_item_id'];
        $costing_id = $dt_put['costing_id'];
        $service_class_id = $dt_put['service_class_id'];
        $name = $dt_put['name'];
        $type_room_id = $dt_put['type_room_id'];
        $is_surgery = $dt_put['is_surgery'];
        $is_internal = $dt_put['is_internal'];
        $is_isolation = $dt_put['is_isolation'];
        $gender_availability = $dt_put['gender_availability'];
        $room_pressure = $dt_put['room_pressure'];

        /* Start Transaction */
        $this->db->trans_start();

        $dt_room = array(
            'health_facility_item_id' => $health_facility_item_id,
            // 'costing_id'              => $costing_id,
            // 'service_class_id'        => $service_class_id,
            'name'                    => $name,
            'type_room_id'            => $type_room_id,
            // 'is_surgery'              => $is_surgery,
            // 'is_isolation'            => $is_isolation,
            // 'gender_availability'     => ($gender_availability == '') ? null: $gender_availability,
            // 'room_pressure'           => $room_pressure,
            'last_updated'            => date('Y-m-d H:i:s'),
        );

        $this->$model->update_v2($room_id, $dt_room);

        // Room Setting
        $dt_room_setting = array(
            'room_id' => $room_id,
            'is_surgery' => $is_surgery,
            'is_internal' => $is_internal,
            'is_isolation' => $is_isolation,
            'costing_id' => $costing_id,
            'service_class_id' => $service_class_id,
            'gender_availability' => ($gender_availability == '') ? null: $gender_availability,
            'room_pressure' => $room_pressure,
        );

        $this->Room_setting->replace($dt_room_setting);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update room', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function update_temp_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_temp_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
        $room_id = $id;
    
        if ($room_id === 0 || $room_id === "0") {
            $this->REST_Return(400, 'room_id is required');
            return;
        }

        $select = 'a.id';
        $where = array('a.is_delete' => 0);
        $room = $this->$model->select($room_id, $select, $where);

        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $room_id);
            return;
        }

        $is_surgery = $dt_put['is_surgery'];
        // $is_internal = $dt_put['is_internal'];
        $is_isolation = $dt_put['is_isolation'];
        $gender_availability = $dt_put['gender_availability'];
        $room_pressure = $dt_put['room_pressure'];
        $costing_id = $dt_put['costing_id'];
        $service_class_id = $dt_put['service_class_id'];

        $select = 'a.id';
        $costing = $this->Costing->select($costing_id, $select);
        if (count($costing) == 0) {
            $this->REST_Return(404, 'there is no costing with id ' . $costing_id);
            return;
        }

        $select = 'a.id';
        $service_class = $this->Service_class->select($service_class_id, $select);
        if (count($service_class) == 0) {
            $this->REST_Return(404, 'there is no service_class with id ' . $service_class_id);
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_room = array(
            'costing_id'          => $costing_id,
            'service_class_id'    => $service_class_id,
            'is_surgery'          => $is_surgery,
            // 'is_internal'      => $is_internal,
            'is_isolation'        => $is_isolation,
            'gender_availability' => ($gender_availability == '') ? null: $gender_availability,
            'room_pressure'       => $room_pressure,
            'last_updated'        => date('Y-m-d H:i:s'),
        );

        $this->$model->update_v2($room_id, $dt_room);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_room,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update room temp', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function transfer_available_get($p = 0) {
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
        
        $where = $q_strings;

        $output = array();

        $select = 'a.id, a.name, b.name as type_room_name, a.code';
        $where['a.is_delete'] = 0;
        $room = $this->$model->select(0, $select, $where);
        
        for ($i=0; $i < count($room); $i++) { 
            $room_id = $room[$i]->id;

            $select = 'a.id, a.name';
            $where = array(
                'a.room_id' => $room_id,
                'a.type_status_bed_id' => 1,
                'a.is_delete' => 0
            );
            $bed = $this->Bed->select(0, $select, $where);
            if (count($bed) > 0) {
                $room[$i]->beds = $bed;

                array_push($output, $room[$i]);
            }
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function update_setting_put($p = 0) {
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
        $this->form_validation->set_rules($this->update_setting_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }

        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $room_id = $id;

        if ($room_id === 0 || $room_id === "0") {
            $this->REST_Return(400, 'room_id is required');
            return;
        }

        $select = 'a.id';
        $room = $this->$model->select($room_id, $select);
        if (count($room) == 0) {
            $this->REST_Return(404, 'there is no room with id ' . $room_id);
            return;
        }

        $is_surgery = $dt_put['is_surgery'] ?? null;
        $is_isolation = $dt_put['is_isolation'] ?? null;
        $gender_availability = $dt_put['gender_availability'] ?? null;
        $room_pressure = $dt_put['room_pressure'] ?? null;

        /* Start Transaction */
        $this->db->trans_start();

        $select = 'a.room_id';
        $room_setting = $this->Room_setting->select($room_id, $select);
        if (count($room_setting) == 0) {
            $dt_room_setting = array(
                'room_id' => $room_id
            );
            $this->Room_setting->replace($dt_room_setting);
        }

        $dt_room_setting = array(
            'is_surgery' => $is_surgery,
            'is_isolation' => $is_isolation,
            'gender_availability' => ($gender_availability == '') ? null: $gender_availability,
            'room_pressure' => $room_pressure,
        );

        $where = array(
            'room_id' => $room_id
        );
        $this->Room_setting->update_v2(0, $dt_room_setting, $where);

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_room_setting,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update room', $response_data);
            return;
        }

        $response_data = array(
            'id'     => $id
        );

        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function find_bed_get($p = 0) {
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

        $type_room_id = $q_strings['type_room_id'] ?? null;
        $service_class_id = $q_strings['service_class_id'] ?? null;
        $doctor_in_charge_id = $q_strings['doctor_in_charge_id'] ?? null;
        $sex = $q_strings['sex'] ?? null;
        $icd10_code = $q_strings['icd10_code'] ?? null;

        if ($type_room_id == null) {
            $this->REST_Return(400, 'type_room_id is required');
            return;
        }

        if ($service_class_id == null) {
            $this->REST_Return(400, 'service_class_id is required');
            return;
        }

        if ($doctor_in_charge_id == null) {
            $this->REST_Return(400, 'doctor_in_charge_id is required');
            return;
        }

        if ($sex == null) {
            $this->REST_Return(400, 'sex is required');
            return;
        }

        $select = 'a.id';
        $service_class = $this->Service_class->select($service_class_id, $select);
        if (count($service_class) == 0) {
            $this->REST_Return(404, 'there is no service class with id ' . $service_class_id);
            return;
        }

        $select = 'a.id, ep.position_id';
        $where = array();
        $join = array(
            array('employee_position ep', 'a.id = ep.employee_id', 'INNER'),
        );
        $order = array(
            'a.id' => 'ASC'
        );
        $employee = $this->Employee->select($doctor_in_charge_id, $select, $where, $join, $order);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
            return;
        }

        $position_id = $employee[0]->position_id;

        // f0b459cb-2ce9-4048-b72e-60cf9d877af6 DOKTER BEDAH PLASTIK
        // 5d4a8db1-057f-4f36-8350-991c3d26f936 DOKTER BEDAH SYARAF
        // 9a9fa850-6d06-4fea-9453-edf90fb39e79 DOKTER BEDAH UMUM
        // 6da8c631-99f4-4979-b6ea-46cc0b21e499 DOKTER BEDAH UROLOGI
        // 1ee70cdd-3aa8-4dca-b711-d9eae4b78503 DOKTER BTKV
        // cec81b02-3435-4891-b3d5-b615e3a3726d DOKTER DIGESTIF
        // 9ee35a29-2934-409a-b1ed-0637089defd7 SPESIALIS BEDAH ANAK
        // 114411d1-d35f-4b1d-b444-4bcf2c2c8d55 DOKTER ORTHOPEDI

        // POSITION ID DOKTER BEDAH
        $surgical_specialist = [
            "f0b459cb-2ce9-4048-b72e-60cf9d877af6",
            "5d4a8db1-057f-4f36-8350-991c3d26f936",
            "9a9fa850-6d06-4fea-9453-edf90fb39e79",
            "6da8c631-99f4-4979-b6ea-46cc0b21e499",
            "1ee70cdd-3aa8-4dca-b711-d9eae4b78503",
            "cec81b02-3435-4891-b3d5-b615e3a3726d",
            "9ee35a29-2934-409a-b1ed-0637089defd7",
            "47834ae1-5c3d-41cb-bfbd-4353ef055ffc",
            "45fd4635-70a3-4e83-b64e-b9e6c27b9f68",
            "114411d1-d35f-4b1d-b444-4bcf2c2c8d55"
        ];

        $is_surgery = 0;
        if (in_array($position_id, $surgical_specialist)) {
            $is_surgery = 1;
        }

        // POSITION ID PENYAKIT DALAM
        $internal_medicine_doctor = [
            "6517241f-67a1-49bd-9f0d-9d11caaa3678"
        ];

        $is_internal = 0;
        // if (in_array($position_id, $internal_medicine_doctor)) {
        //     $is_internal = 1;
        // }

        $room_id = '';
        $room_name = '';
        $bed_id = '';
        $bed_name = '';

        if ($sex == 1) {
            $kelamin = 'Laki-laki';
        } else {
            $kelamin = 'Perempuan';
        }

        if ($service_class_id == '8d3b8d25-4606-4db5-9ec1-59ec22514210') {
            $kelas = 'Kelas 1';
        } elseif ($service_class_id == 'f39fbb8f-082e-4c71-8802-92943b6e4683') {
            $kelas = 'Kelas 2';
        } elseif ($service_class_id == 'd168732a-fdef-456d-836c-750e003187e0') {
            $kelas = 'Kelas 3';
        } elseif ($service_class_id == '52d76ebf-aa04-4308-a1a1-61e325333787') {
            $kelas = 'VIP';
        } elseif ($service_class_id == 'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4') {
            $kelas = 'Non Kelas';
        } elseif ($service_class_id == '7560e211-15ed-4113-bd9a-943a1130d306') {
            $kelas = 'Isolasi';
        } elseif ($service_class_id == '01a37f8e-2baf-4057-b8fb-7a825cb12e19') {
            $kelas = 'Intensif Care';
        } 

        $gender_filter = true;
        $sc_filter = true;
        $is_psychiatric = 0;
        $room_pressure = '';

        // FILTER RUANGAN
        if ($type_room_id == 5) { // G.ANAK TIDAK PERLU GENDER YANG SAMA
            $gender_filter = false;
        } else if ($type_room_id == 10 || $type_room_id == 31) { // ORCHID TIDAK FILTER KELAS
            $sc_filter = false;
        } else if ($type_room_id == 27) { // RUANG JIWA BED TIDAK DISATUKAN DENGAN PASIEN LAIN
            $sc_filter = false;
            $is_surgery = 0;
            $is_psychiatric = 0; // NOTE - JADI 0 DULU BELUM BERJALAN
        } else if ($type_room_id == 30) { // ORCHID ISOLASI MASUK TEKANAN POSITIF ATAU NEGATIF
            $sc_filter = false;
            $positive_icd = ['B20.0' ,'B20.1' ,'B20.2' ,'B20.3' ,'B20.4' ,'B20.5' ,'B20.6' ,'B20.7' ,'B20.8' ,'B20.9' ,'B21.0' ,'B21.1' ,'B21.2' ,'B21.3' ,'B21.7' ,'B21.8' ,'B21.9' ,'B22.0' ,'B22.1' ,'B22.2' ,'B22.7' ,'B23.0' ,'B23.1' ,'B23.2' ,'B23.8' ,'B24','D70.9','C91.0','A41.9'];
            $negative_icd = ['J10.0','J10.1','J10.8','J11.0','J11.1','J11.8','A15.0','A15.1','A15.2','A15.3','A15.4','A15.5','A15.6','A15.7','A15.8','A15.9','A16.0','A16.1','A16.2','A16.3','A16.4','A16.5','A16.7','A16.8','A16.9','A17.0','A17.1','A17.8','A17.9','A18.0','A18.1','A18.2','A18.3','A18.4','A18.5','A18.6','A18.7','A18.8','A19.0','A19.1','A19.2','A19.8','A19.9','A82','U07.1','A39.0','B01','B05','B97.2'];

            if ($icd10_code != null) {
                $icd10 = explode('|', $icd10_code);
                for ($i=0; $i < count($icd10); $i++) { 
                    if (in_array($icd10[$i], $positive_icd)) {
                        $room_pressure = '+';
                    } elseif (in_array($icd10[$i], $negative_icd)) {
                        $room_pressure = '-';
                    }
                }
            }
        } else if($type_room_id == 13) { // ICU
            $sc_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        } else if($type_room_id == 16) { // ICCU
            $sc_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        } else if($type_room_id == 15) { // ICU ISOLASI
            $sc_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        } else if($type_room_id == 17) { // NICU
            $sc_filter = false;
            $gender_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        } else if($type_room_id == 14) { // PICU
            $sc_filter = false;
            $gender_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        } else if($type_room_id == 18) { // PERINATOLOGI
            $sc_filter = false;
            $gender_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        } else if($type_room_id == 29) { // OK
            $sc_filter = false;
            $gender_filter = false;
            $is_surgery = 0;
            $is_internal = 0;
        }

        $res = $this->clean_room($type_room_id, $sex, $service_class_id, $gender_filter, $sc_filter, $room_pressure);

        $room_class = $res['room_class'];
        $room_not_class = $res['room_not_class'];

        $test = array(
            'room_class' => $room_class,
            'room_not_class' => $room_not_class,
        );

        $room_class_filtered = [];

        // FILTER BED MEMBEDAKAN BEDAH DAN NON BEDAH DAN PENYAKIT DALAM
        for ($i=0; $i < count($room_class); $i++) { 
            $room_is_surgery = $room_class[$i]->is_surgery;
            $room_is_internal = $room_class[$i]->is_internal;
            if ($room_is_surgery == $is_surgery && $room_is_internal == $is_internal) {
                array_push($room_class_filtered, $room_class[$i]);
            }
        }
        
        $room_not_class_filtered = [];

        // UNTUK MENGATUR NAIK ATAU TURUN KELAS
        // $classes = ['Isolasi', 'Intensif Care', 'Non Kelas', 'Kelas 3', 'Kelas 2', 'Kelas 1', 'VIP'];
        $classes = ['Kelas 3', 'Kelas 2', 'Kelas 1', 'VIP'];
        $classes_id = [
            '7560e211-15ed-4113-bd9a-943a1130d306' => 'Isolasi',
            '01a37f8e-2baf-4057-b8fb-7a825cb12e19' => 'Intensif Care',
            'e7ce65ba-3d51-41aa-add7-df8f6a40e2b4' => 'Non Kelas',
            'd168732a-fdef-456d-836c-750e003187e0' => 'Kelas 3',
            'f39fbb8f-082e-4c71-8802-92943b6e4683' => 'Kelas 2',
            '8d3b8d25-4606-4db5-9ec1-59ec22514210' => 'Kelas 1',
            '52d76ebf-aa04-4308-a1a1-61e325333787' => 'VIP',
        ];
        $idx_classes = array_search($kelas, $classes);
        $fix_class = array('Isolasi', 'Intensif Care', 'Non Kelas'); // ALWAYS AVAILABLE
        $class = array_merge($fix_class, array_slice($classes, $idx_classes));
        for ($i=0; $i < count($room_not_class); $i++) { 
            if (in_array($classes_id[$room_not_class[$i]->service_class_id], $class)) {
                array_push($room_not_class_filtered, $room_not_class[$i]);
            }
        }

        $output = array(
            'room_id' => '',
            'room_name' => '',
            'room_service_class' => '',
            'bed_id' => '',
            'bed_name' => '',
            'is_surgery' => '',
        );

        // FIND ORIGINAL BED
        $message = 'Sukses mendapat tempat tidur';
        $res = $this->clean_bed($room_class_filtered, $is_psychiatric);
        if ($res['bed_id'] != '') {
            $room_id = $res['room_id'];
            $room_name = $res['room_name'];
            $room_service_class = $res['room_service_class'];
            $health_facility_item_id = $res['health_facility_item_id'];
            $bed_id = $res['bed_id'];
            $bed_name = $res['bed_name'];
        }

        // NAIK KELAS KE BANGSAL YANG SAMA KETIKA KELAS YANG DIINGINKAN PENUH
        if ($bed_id == '') {
            if (count($room_not_class_filtered) > 0) {
                $message = 'Ketersediaan kamar tidur untuk ' . $kelas . ' dan ' . $kelamin .  ' sudah penuh, akan naik kelas ke bangsal sama';
                if($type_room_id == 13 || $type_room_id == 16 || $type_room_id == 17 || $type_room_id == 14 || $type_room_id == 18 || $type_room_id == 29) { // ICU
                    $message = 'Sukses mendapat tempat tidur';
                }
                $res = $this->clean_bed($room_not_class_filtered);
                if ($res['bed_id'] != '') {
                    $room_id = $res['room_id'];
                    $room_name = $res['room_name'];
                    $room_service_class = $res['room_service_class'];
                    $health_facility_item_id = $res['health_facility_item_id'];
                    $bed_id = $res['bed_id'];
                    $bed_name = $res['bed_name'];
                }
            }

            if ($type_room_id == 30) { // ORCHID ISOLASI CARI RUANGAN KOSONG
                $res = $this->clean_room(30, $sex, $service_class_id, $gender_filter, $sc_filter, $room_pressure, true);
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] != '') {
                    $room_id = $res['room_id'];
                    $room_name = $res['room_name'];
                    $room_service_class = $res['room_service_class'];
                    $health_facility_item_id = $res['health_facility_item_id'];
                    $bed_id = $res['bed_id'];
                    $bed_name = $res['bed_name'];
                }
            }

            if ($type_room_id == 7) { // JASMINE CARI RUANGAN KOSONG JIKA PENYAKIT DALAM ATAU BEDAH
                $res = $this->clean_room(7, $sex, $service_class_id, $gender_filter, $sc_filter, $room_pressure, true);
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] != '') {
                    $room_id = $res['room_id'];
                    $room_name = $res['room_name'];
                    $room_service_class = $res['room_service_class'];
                    $health_facility_item_id = $res['health_facility_item_id'];
                    $bed_id = $res['bed_id'];
                    $bed_name = $res['bed_name'];
                }
            }

            if ($type_room_id == 5) { // G. ANAK CARI RUANGAN KOSONG
                $res = $this->clean_room(5, $sex, $service_class_id, $gender_filter, $sc_filter, $room_pressure, true);
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] != '') {
                    $room_id = $res['room_id'];
                    $room_name = $res['room_name'];
                    $room_service_class = $res['room_service_class'];
                    $health_facility_item_id = $res['health_facility_item_id'];
                    $bed_id = $res['bed_id'];
                    $bed_name = $res['bed_name'];
                }
            }

            if ($type_room_id == 2) { // SAKURA CARI RUANGAN KOSONG
                $res = $this->clean_room(2, $sex, $service_class_id, $gender_filter, $sc_filter, $room_pressure, true);
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] != '') {
                    $room_id = $res['room_id'];
                    $room_name = $res['room_name'];
                    $room_service_class = $res['room_service_class'];
                    $health_facility_item_id = $res['health_facility_item_id'];
                    $bed_id = $res['bed_id'];
                    $bed_name = $res['bed_name'];
                }
            }
        }

        // NAIK KELAS KE BANGSAL BERIKUTNYA JIKA PENCARIAN TEMPAT TIDUR DIBANGSAL YANG SAMA PENUH
        if ($bed_id == '') {
            if ($type_room_id == 6) { // G.KEBIDANAN NUMPANG KE G.ANAK
                $message = 'Ketersediaan kamar tidur untuk ' . $kelas . ' dan ' . $kelamin .  ' Gardenia Kebidanan sudah penuh, akan meminjam ruangan ke Gardenia Anak';
                $res = $this->clean_room(5, $sex, $service_class_id);
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] == '') {
                    $this->REST_Return(404, 'Ketersediaan kamar tidur untuk ' . $kelas . ' dan ' . $kelamin .  ' Gardenia Kebidanan sudah penuh, akan meminjam ruangan ke Gardenia Anak dan ketersediaan Gardenia Anak sudah penuh', $output);
                    return;
                }
                $room_id = $res['room_id'];
                $room_name = $res['room_name'];
                $room_service_class = $res['room_service_class'];
                $health_facility_item_id = $res['health_facility_item_id'];
                $bed_id = $res['bed_id'];
                $bed_name = $res['bed_name'];
            }

            if ($type_room_id == 7) { // JASMINE NAIK KE SAKURA
                $message = 'Ketersediaan kamar tidur untuk ' . $kelas . ' dan ' . $kelamin .  ' Jasmine sudah penuh, akan naik kelas ke Sakura';
                $res = $this->clean_room(2, $sex, '52d76ebf-aa04-4308-a1a1-61e325333787');
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] == '') {
                    $this->REST_Return(404, 'Ketersediaan kamar tidur untuk ' . $kelas . ' dan ' . $kelamin .  ' Jasmine sudah penuh, akan naik kelas ke Sakura dan ketersediaan Sakura sudah penuh', $output);
                    return;
                }
                $room_id = $res['room_id'];
                $room_name = $res['room_name'];
                $room_service_class = $res['room_service_class'];
                $health_facility_item_id = $res['health_facility_item_id'];
                $bed_id = $res['bed_id'];
                $bed_name = $res['bed_name'];
            }

            if ($type_room_id == 10) { // ORCHID PINJAM ANTARA BEDAH DAN NON BEDAH
                if ($is_surgery == 1) {
                    $is_surgery_txt = ' Bedah';
                    $is_surgery_txt_2 = ' Non Bedah';
                } elseif ($is_surgery == 0) {
                    if ($is_internal == 1) {
                        $is_surgery_txt = ' Non Bedah Penyakit Dalam';
                        $is_surgery_txt_2 = ' Bedah Penyakit Dalam';
                    } else {
                        $is_surgery_txt = ' Non Bedah';
                        $is_surgery_txt_2 = ' Bedah';
                    }
                }
                $message = 'Ketersediaan kamar tidur untuk ' . $kelamin . $is_surgery_txt . ' Orchid sudah penuh, akan pinjam ke Orchid' . $is_surgery_txt_2;
                $res = $this->clean_room(10, $sex, $service_class_id, $gender_filter, $sc_filter, '', true);
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] == '') {
                    $message = 'Ketersediaan kamar tidur untuk ' . $kelamin . $is_surgery_txt . ' Orchid sudah penuh, akan naik kelas ke Jasmine';
                    $res = $this->clean_room(7, $sex, '8d3b8d25-4606-4db5-9ec1-59ec22514210');
                    $res = $this->clean_bed($res['room_class']);
                    if ($res['bed_id'] == '') {
                        $this->REST_Return(404, 'Ketersediaan kamar tidur untuk ' . $kelamin . $is_surgery_txt . ' Orchid dan Jasmine sudah penuh', $output);
                        return;
                    }
                }
                $room_id = $res['room_id'];
                $room_name = $res['room_name'];
                $room_service_class = $res['room_service_class'];
                $health_facility_item_id = $res['health_facility_item_id'];
                $bed_id = $res['bed_id'];
                $bed_name = $res['bed_name'];
            }

            if ($type_room_id == 15) { // ICU ISOLASI NUMPANG KE ICU BIASA
                $message = 'Ketersediaan kamar tidur untuk ICU Isolasi sudah penuh, akan pindah ke ICU biasa';
                $res = $this->clean_room(13, $sex, '01a37f8e-2baf-4057-b8fb-7a825cb12e19');
                $res = $this->clean_bed($res['room_class']);
                if ($res['bed_id'] == '') {
                    $this->REST_Return(404, 'Ketersediaan kamar tidur untuk ICU Isolasi sudah penuh, akan pindah ke ICU biasa dan ketersediaan ICU biasa sudah penuh', $output);
                    return;
                }
                $room_id = $res['room_id'];
                $room_name = $res['room_name'];
                $room_service_class = $res['room_service_class'];
                $health_facility_item_id = $res['health_facility_item_id'];
                $bed_id = $res['bed_id'];
                $bed_name = $res['bed_name'];
            }
        }

        if ($bed_id == '') {
            if ($room_pressure == '-') {
                $room_pressure = ' Negatif';
            } elseif ($room_pressure == '+') {
                $room_pressure = ' Positif';
            } else {
                $room_pressure = '';
            }

            $message = 'Ketersediaan kamar tidur untuk ' . $kelas . $room_pressure . ' dan ' . $kelamin .  ' sudah penuh';

            if ($type_room_id == 10) { // ORCHID
                if ($is_surgery == 1) {
                    $is_surgery = 'Bedah';
                } elseif ($is_surgery == 0) {
                    $is_surgery = 'Non Bedah';
                }
                $message = 'Ketersediaan kamar tidur untuk Orchid ' . $is_surgery . ' dan ' . $kelamin .  ' sudah penuh';
            }

            $this->REST_Return(404, $message, $output);
            return;
        }

        $output = array(
            'room_id' => $room_id,
            'room_name' => $room_name,
            'room_service_class' => $classes_id[$room_service_class],
            'health_facility_item_id' => $health_facility_item_id,
            'bed_id' => $bed_id,
            'bed_name' => $bed_name,
            'is_surgery' => $is_surgery,
            'is_internal' => $is_internal,
        );

        Logger::info($this->debug_uniq, "message: " . $message);
        Logger::info($this->debug_uniq, "output:", $output);

        $this->REST_Return(200, $message, $output);
        return;
    }

    function clean_room($type_room_id, $sex, $service_class_id, $gender_filter = true, $sc_filter = true, $room_pressure = '', $empty_filter = false) {
        $output = array(
            'room_class' => array(),
            'room_not_class' => array(),
        );

        $room_pressure_sql = '';
        if ($room_pressure != '') {
            $room_pressure_sql = "AND r.room_pressure = '$room_pressure'";
        }

        $rooms = $this->db->query("
            SELECT r.id, r.name, r.gender_availability, r.service_class_id, sc.name as service_class_name, r.is_surgery, r.is_internal, r.health_facility_item_id
            FROM room r
            INNER JOIN service_class sc ON r.service_class_id = sc.id
            WHERE r.type_room_id = $type_room_id
            $room_pressure_sql
            AND r.is_delete = 0
        ")->result();

        // sorting room number from 1
        usort($rooms, function($a, $b) {
			// Extract the numbers from the strings
			$numA = (int) filter_var($a->name, FILTER_SANITIZE_NUMBER_INT);
			$numB = (int) filter_var($b->name, FILTER_SANITIZE_NUMBER_INT);
			
			return $numB - $numA;
		});

        for ($i=0; $i < count($rooms); $i++) { 
            $room_id = $rooms[$i]->id;
            $room_gender_availability = $rooms[$i]->gender_availability;
            $room_service_class_id = $rooms[$i]->service_class_id;

            if ($empty_filter) {
                $beds = $this->db->query("
                    SELECT id
                    FROM bed
                    WHERE room_id = '$room_id'
                    AND is_delete = 0
                    AND type_status_bed_id != 1
                ")->result();
                if (count($beds) != 0) {
                    continue;
                }
            }

            if ($gender_filter) {
                if (($room_gender_availability == $sex) || ($room_gender_availability == null)) {
                    if ($sc_filter) {
                        if ($room_service_class_id == $service_class_id) {
                            array_push($output['room_class'], $rooms[$i]);   
                        } else {
                            array_push($output['room_not_class'], $rooms[$i]);   
                        }
                    } else {
                        array_push($output['room_class'], $rooms[$i]);
                    }
                }
            } else {
                if ($room_service_class_id == $service_class_id) {
                    array_push($output['room_class'], $rooms[$i]);   
                } else {
                    array_push($output['room_not_class'], $rooms[$i]);   
                }
            }
        }

        return $output;
    }

    function clean_bed($want_to_find, $is_psychiatric = 0) {
        $output = array(
            'room_id' => '',
            'room_name' => '',
            'room_service_class' => '',
            'health_facility_item_id' => '',
            'bed_id' => '',
            'bed_name' => '',
        );

        // PSYCHIATRIC PATIENT BED FILTER
        $type_status_bed_id = 'AND b.type_status_bed_id = 1';
        if ($is_psychiatric == 1) {
            $type_status_bed_id = '';
        }
        $psychiatric_room = [];
        
        for ($i=0; $i < count($want_to_find); $i++) { 
            $room_id = $want_to_find[$i]->id;
            $room_name = $want_to_find[$i]->name;
            $room_service_class = $want_to_find[$i]->service_class_id;
            $health_facility_item_id = $want_to_find[$i]->health_facility_item_id;

            $beds = $this->db->query("
                SELECT b.id, b.name, b.type_status_bed_id
                FROM bed b
                WHERE b.is_delete = 0
                $type_status_bed_id
                AND b.room_id = '$room_id'
            ")->result();

            // sorting bed number from 1
            usort($beds, function($a, $b) {
                // Extract the numbers from the strings
                $numA = (int) filter_var($a->name, FILTER_SANITIZE_NUMBER_INT);
                $numB = (int) filter_var($b->name, FILTER_SANITIZE_NUMBER_INT);
                
                return $numA - $numB;
            });

            // finding the first available bed
            if (count($beds) > 0) {
                if ($is_psychiatric == 1) {
                    $dt_pyschiatric = array(
                        'room_id' => $room_id,
                        'room_name' => $room_name,
                        'bed_available' => 0,
                        'bed_not_available' => 0,
                        'beds' => $beds
                    );

                    for ($j=0; $j < count($beds); $j++) { 
                        $bed_id = $beds[0]->id;
                        $bed_name = $beds[0]->name;
                        $bed_type_status_bed_id = $beds[0]->type_status_bed_id;

                        if ($bed_type_status_bed_id == 1) {
                            $dt_pyschiatric['bed_available'] = $dt_pyschiatric['bed_available'] + 1;
                        } else if ($bed_type_status_bed_id == 2) {
                            $dt_pyschiatric['bed_not_available'] = $dt_pyschiatric['bed_not_available'] + 1;
                        }
                    }

                    array_push($psychiatric_room, $dt_pyschiatric);
                } else {
                    $bed_id = $beds[0]->id;
                    $bed_name = $beds[0]->name;

                    $output = array(
                        'room_id' => $room_id,
                        'room_name' => $room_name,
                        'room_service_class' => $room_service_class,
                        'health_facility_item_id' => $health_facility_item_id,
                        'bed_id' => $bed_id,
                        'bed_name' => $bed_name,
                    );

                    break;
                }
            }
        }

        return $output;
    }

    public function floor_plan_get($p = 0) {
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

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;

        if ($health_facility_item_id != null) {
            $q_strings['a.health_facility_item_id'] = $health_facility_item_id;
            unset($q_strings['health_facility_item_id']);
        }
        
        $where = $q_strings;
        
        $select = 'a.id,
        a.name,
        a.code,
        a.is_surgery,
        a.is_internal,
        a.is_isolation,
        a.gender_availability,
        a.room_pressure,
        a.costing_id,
        a.service_class_id,
        sc.name as service_class_name
        ';
        $where['a.is_delete'] = 0;
        $join = array(
            array('service_class sc', 'a.service_class_id = sc.id', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        // sorting room number from 1
        usort($output, function($a, $b) {
			// Extract the numbers from the strings
			$numA = (int) filter_var($a->name, FILTER_SANITIZE_NUMBER_INT);
			$numB = (int) filter_var($b->name, FILTER_SANITIZE_NUMBER_INT);
			
			return $numB - $numA;
		});

        for ($i=0; $i < count($output); $i++) { 
            $room_id = $output[$i]->id;

            $select = 'a.id, a.name, a.type_status_bed_id';
            $where = array(
                'a.room_id' => $room_id,
                'a.is_delete' => 0,
            );
            $join = array();
            $order = array('a.name' => 'ASC');
            $beds = $this->Bed->select(0, $select, $where, $join, $order);

            for ($j=0; $j < count($beds); $j++) { 
                $bed_id = $beds[$j]->id;

                $select = 'r.norm, p.fullname as patient_name, c.name as company_name, h4.name as insurance_name, ri.insurance_id, ri.service_class_name, p.sex as patient_sex, rd.id as registration_destination_id, h.name as alias_name, rd.is_transfer';
                $where = array();
                $join = array(
                    array('registration_destination rd', 'a.id = rd.bed_id AND rd.is_checkout = 0 AND rd.is_cancel = 0', 'INNER'),
                    array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary = 1', 'INNER'),
                    array('helper h4', 'ri.insurance_id = h4.code AND h4.variable = "insurance"', 'LEFT'),
                    array('company c', 'ri.company_id = c.id', 'LEFT'),
                    array('registration r', 'rd.registration_id = r.id', 'INNER'),
                    array('patient p', 'r.norm = p.norm', 'INNER'),
                    array('helper h', 'p.alias = h.code AND h.variable="alias"', 'INNER'),
                );
                $order = array('a.name' => 'ASC');
                $bed = $this->Bed->select($bed_id, $select, $where, $join, $order);

                $beds[$j]->patients = $bed;
            }

            $output[$i]->beds = $beds;
        }

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function reset_setting_get($p = 0) {
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

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;

        if ($health_facility_item_id == null) {
            $this->REST_Return(400, 'health_facility_item_id is required');
            return;
        }

        $q_strings['a.health_facility_item_id'] = $health_facility_item_id;
        unset($q_strings['health_facility_item_id']);
        
        $where = $q_strings;
        
        $select = 'a.id, rs.is_surgery, rs.is_internal, rs.is_isolation, rs.gender_availability, rs.room_pressure';
        $where['a.is_delete'] = 0;
        $join = array(
            array('room_setting rs', 'a.id = rs.room_id', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($output); $i++) { 
            $room_id = $output[$i]->id;
            $is_surgery = $output[$i]->is_surgery;
            $is_internal = $output[$i]->is_internal;
            $is_isolation = $output[$i]->is_isolation;
            $gender_availability = $output[$i]->gender_availability;
            $room_pressure = $output[$i]->room_pressure;

            $dt_room = array(
                'is_surgery'          => $is_surgery,
                'is_internal'         => $is_internal,
                'is_isolation'        => $is_isolation,
                'gender_availability' => $gender_availability,
                'room_pressure'       => $room_pressure,
            );

            $this->$model->update_v2($room_id, $dt_room);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to reset setting', $response_data);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', $output);
        return;
    }

    public function fix_gender_get($p = 0) {
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

        $health_facility_item_id = $q_strings['health_facility_item_id'] ?? null;

        if ($health_facility_item_id == null) {
            $this->REST_Return(400, 'health_facility_item_id is required');
            return;
        }

        // POSITION ID DOKTER BEDAH
        $surgical_specialist = [
            "f0b459cb-2ce9-4048-b72e-60cf9d877af6",
            "5d4a8db1-057f-4f36-8350-991c3d26f936",
            "9a9fa850-6d06-4fea-9453-edf90fb39e79",
            "6da8c631-99f4-4979-b6ea-46cc0b21e499",
            "1ee70cdd-3aa8-4dca-b711-d9eae4b78503",
            "45fd4635-70a3-4e83-b64e-b9e6c27b9f68",
            "9ee35a29-2934-409a-b1ed-0637089defd7",
        ];

        $q_strings['a.health_facility_item_id'] = $health_facility_item_id;
        unset($q_strings['health_facility_item_id']);
        
        $where = $q_strings;
        
        $select = 'a.id, rs.is_surgery, rs.is_internal, rs.is_isolation, rs.gender_availability, rs.room_pressure';
        $where['a.is_delete'] = 0;
        $join = array(
            array('room_setting rs', 'a.id = rs.room_id', 'INNER'),
        );
        $output = $this->$model->select(0, $select, $where, $join);

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($output); $i++) { 
            $room_id = $output[$i]->id;
            $gender_availability = $output[$i]->gender_availability;
            $room_is_surgery = $output[$i]->is_surgery;

            $select = 'a.id, p.sex, ep.position_id';
            $where = array(
                'a.is_delete' => 0,
                'a.room_id' => $room_id,
            );
            $join = array(
                array('registration_destination rd', 'a.id = rd.bed_id AND rd.is_checkout = 0 AND rd.is_cancel = 0', 'INNER'),
                array('registration r', 'rd.registration_id = r.id', 'INNER'),
                array('patient p', 'r.norm = p.norm', 'INNER'),
                array('employee_position ep', 'rd.doctor_in_charge_id = ep.employee_id', 'INNER'),
            );
            $bed = $this->Bed->select(0, $select, $where, $join);

            $valid = true;
            $valid_surgery = true;
            $cur_sex = null;
            $cur_surgery = null;
            for ($k=0; $k < count($bed); $k++) { 
                $sex = $bed[$k]->sex;
                $position_id = $bed[$k]->position_id;

                $is_surgery = 0;
                if (in_array($position_id, $surgical_specialist)) {
                    $is_surgery = 1;
                }

                if ($cur_sex === null) {
                    $cur_sex = $sex;
                }

                if ($cur_sex != $sex) {
                    $valid = false;
                }

                if ($cur_surgery === null) {
                    $cur_surgery = $is_surgery;
                }

                if ($cur_surgery != $is_surgery) {
                    $valid_surgery = false;
                }
            }

            if (!$valid || count($bed) == 0) {
                $cur_sex = $gender_availability;
            }
            if (!$valid_surgery || count($bed) == 0) {
                $cur_surgery = $room_is_surgery;
            }

            $dt_room = array(
                'gender_availability' => $cur_sex,
                'is_surgery' => $cur_surgery,
            );

            $this->$model->update_v2($room_id, $dt_room);
        }

        $this->db->trans_complete();
        /* End Transaction */

        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to fix gender', $response_data);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', $output);
        return;
    }

}