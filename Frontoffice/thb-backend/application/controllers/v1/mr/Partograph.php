<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Partograph extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'doctor_id', 'label' => 'doctor_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'midwife_id', 'label' => 'midwife_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
            array('field' => 'doctor_id', 'label' => 'doctor_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'midwife_id', 'label' => 'midwife_id', 'rules' => 'trim|required|max_length[36]'),
        );

        $this->create_item_rules = array(
            array('field' => 'items', 'label' => 'items', 'rules' => 'trim'),
        );

        $this->delete_item_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_hours', 'label' => 'observation_hours', 'rules' => 'trim|required'),
            array('field' => 'item_type_id', 'label' => 'item_type_id', 'rules' => 'trim|required|max_length[2]'),
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
    
        $partograph_id = $id;

        if ($partograph_id === 0 || $partograph_id === "0") {
            $registration_destination_id = $q_strings['registration_destination_id'] ?? null;
            if ($registration_destination_id == null) {
                $this->REST_Return(400, 'registration_destination_id is required if partograph_id is not provided');
                return;
            }
        }

        $this->$model->whereFields = $q_strings;
    
        $this->$model->whereFields['a.is_delete'] = 0;;
        $output = $this->$model->read($partograph_id,false);
        $response_data = array();
        
        if (count($output) > 0) {
            $partograph_id = $output[0]->id;

            $select = 'a.*';
            $where = array(
                'partograph_id' => $partograph_id,
                'is_delete' => 0,
            );
            $join = array();
            $order = array('observation_hours' => 'ASC', 'item_type_id' => 'ASC');
            $partograph_item = $this->Partograph_item->select(0, $select, $where, $join, $order);

            $cervical_dilation = array();
            $head_descent = array();
            $contraction = array();
            $fetal_heart_rate = array();
            $amniotic_fluid = array();
            $infiltration = array();
            $blood_pressure = array();
            $pulse = array();
            $temperature = array();
            $urine = array();
            $oxytocin = array();
            $iv_fluids_meds = array();

            for ($i=0; $i < count($partograph_item); $i++) { 
                $observation_hours = $partograph_item[$i]->observation_hours;
                $item_type_id = $partograph_item[$i]->item_type_id;
                
                if ($item_type_id == 1) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_cervical_dilation = array(
                        'item_type_id'      => 1,
                        'observation_hours' => $observation_hours,
                        'cervical_dilation' => $value_1,
                    );

                    array_push($cervical_dilation, $dt_cervical_dilation);
                }

                if ($item_type_id == 2) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_head_descent = array(
                        'item_type_id' => 2,
                        'observation_hours' => $observation_hours,
                        'head_descent' => $value_1,
                    );

                    array_push($head_descent, $dt_head_descent);
                }

                if ($item_type_id == 3) {
                    $value_1 = $partograph_item[$i]->value_1;
                    $value_2 = $partograph_item[$i]->value_2;

                    $dt_contraction = array(
                        'item_type_id' => 3,
                        'observation_hours' => $observation_hours,
                        'contraction' => $value_1,
                        'duration' => $value_2,
                    );

                    array_push($contraction, $dt_contraction);
                }

                if ($item_type_id == 4) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_fetal_heart_rate = array(
                        'item_type_id' => 4,
                        'observation_hours' => $observation_hours,
                        'fetal_heart_rate' => $value_1,
                    );

                    array_push($fetal_heart_rate, $dt_fetal_heart_rate);
                }

                if ($item_type_id == 5) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_amniotic_fluid = array(
                        'item_type_id' => 5,
                        'observation_hours' => $observation_hours,
                        'amniotic_fluid' => $value_1,
                    );

                    array_push($amniotic_fluid, $dt_amniotic_fluid);
                }

                if ($item_type_id == 6) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_infiltration = array(
                        'item_type_id' => 6,
                        'observation_hours' => $observation_hours,
                        'infiltration' => $value_1,
                    );

                    array_push($infiltration, $dt_infiltration);
                }

                if ($item_type_id == 7) {
                    $value_1 = $partograph_item[$i]->value_1;
                    $value_2 = $partograph_item[$i]->value_2;

                    $dt_blood_pressure = array(
                        'item_type_id' => 7,
                        'observation_hours' => $observation_hours,
                        'systolic' => $value_1,
                        'diastolic' => $value_2,
                    );

                    array_push($blood_pressure, $dt_blood_pressure);
                }

                if ($item_type_id == 8) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_pulse = array(
                        'item_type_id' => 8,
                        'observation_hours' => $observation_hours,
                        'pulse' => $value_1,
                    );

                    array_push($pulse, $dt_pulse);
                }

                if ($item_type_id == 9) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_temperature = array(
                        'item_type_id' => 9,
                        'observation_hours' => $observation_hours,
                        'temperature' => $value_1,
                    );

                    array_push($temperature, $dt_temperature);
                }

                if ($item_type_id == 10) {
                    $value_1 = $partograph_item[$i]->value_1;
                    $value_2 = $partograph_item[$i]->value_2;
                    $value_3 = $partograph_item[$i]->value_3;

                    $dt_urine = array(
                        'item_type_id' => 10,
                        'observation_hours' => $observation_hours,
                        'protein' => $value_1,
                        'acetone' => $value_2,
                        'volume' => $value_3,
                    );

                    array_push($urine, $dt_urine);
                }

                if ($item_type_id == 11) {
                    $value_1 = $partograph_item[$i]->value_1;
                    $value_2 = $partograph_item[$i]->value_2;

                    $dt_oxytocin = array(
                        'item_type_id' => 11,
                        'observation_hours' => $observation_hours,
                        'oxytocin' => $value_1,
                        'drops' => $value_2,
                    );

                    array_push($oxytocin, $dt_oxytocin);
                }

                if ($item_type_id == 12) {
                    $value_1 = $partograph_item[$i]->value_1;

                    $dt_iv_fluids_meds = array(
                        'item_type_id' => 12,
                        'observation_hours' => $observation_hours,
                        'iv_fluids_meds' => $value_1,
                    );

                    array_push($iv_fluids_meds, $dt_iv_fluids_meds);
                }
            }

            $output[0]->cervical_dilation = $cervical_dilation;
            $output[0]->head_descent = $head_descent;
            $output[0]->contraction = $contraction;
            $output[0]->fetal_heart_rate = $fetal_heart_rate;
            $output[0]->amniotic_fluid = $amniotic_fluid;
            $output[0]->infiltration = $infiltration;
            $output[0]->blood_pressure = $blood_pressure;
            $output[0]->pulse = $pulse;
            $output[0]->temperature = $temperature;
            $output[0]->urine = $urine;
            $output[0]->oxytocin = $oxytocin;
            $output[0]->iv_fluids_meds = $iv_fluids_meds;
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function get_hours_get($p = 0) {
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
    
        $partograph_id = $id;
        
        if ($partograph_id === 0 || $partograph_id === "0") {
            $this->REST_Return(400, 'partograph_id is required');
            return;
        }

        if (!isset($q_strings['addition_time']) || !isset($q_strings['max_time'])) {
            $this->REST_Return(400, 'param addition_time & max_time is required');
            return;
        }

        $addition_time = (int) $q_strings['addition_time'] * 60;
        $max_time = strtotime($q_strings['max_time']);

        $select = 'a.id, a.datetime_start';
        $where = array('a.is_delete' => 0);
        $partograph = $this->$model->select($partograph_id, $select, $where);

        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }

        $datetime_start = $partograph[0]->datetime_start;
        $timestamp = strtotime($datetime_start);

        $output = array();

        while ($timestamp <= $max_time) {
            $date = date('Y-m-d H:i:s', $timestamp);

            // echo "\n";
    
            array_push($output, $date);
    
            $timestamp += $addition_time;
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
        $doctor_id = $dt_post['doctor_id'];
        $midwife_id = $dt_post['midwife_id'];

        $select = 'a.id';
        $registration_destination = $this->Registration_destination->select($registration_destination_id, $select);
        
        if (count($registration_destination) == 0) {
            $this->REST_Return(404, 'there is no registration_destination with id ' . $registration_destination_id);
            return;
        }

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_id, $select);
        
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_id);
            return;
        }

        $select = 'a.id';
        $employee_2 = $this->Employee->select($midwife_id, $select);
        
        if (count($employee_2) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $midwife_id);
            return;
        }

        $datetime_start = $dt_post['datetime_start'];
        $water_breaks_since = $dt_post['water_breaks_since'];
        $stomachache_since = $dt_post['stomachache_since'];
        $gravida = $dt_post['gravida'];
        $para = $dt_post['para'];
        $abortus = $dt_post['abortus'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_partograph = array(
            'registration_destination_id' => $registration_destination_id,
            'doctor_id' => $doctor_id,
            'midwife_id' => $midwife_id,
            'datetime_start' => $datetime_start,
            'water_breaks_since' => $water_breaks_since,
            'stomachache_since' => $stomachache_since,
            'gravida' => $gravida,
            'para' => $para,
            'abortus' => $abortus,
            'user_added' => $user_id,
            'date_added' => date('Y-m-d H:i:s',time()),
            'date_updated' => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_partograph);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_partograph,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create partograph', $response_data);
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
    
        $partograph_id = $id;
    
        if ($partograph_id === 0 || $partograph_id === "0") {
            $this->REST_Return(400, 'partograph_id is required');
            return;
        }

        $select = 'a.id';
        $partograph = $this->$model->select($partograph_id, $select);

        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }

        $doctor_id = $dt_put['doctor_id'];
        $midwife_id = $dt_put['midwife_id'];

        $select = 'a.id';
        $employee = $this->Employee->select($doctor_id, $select);
        if (count($employee) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $doctor_id);
            return;
        }

        $select = 'a.id';
        $employee_2 = $this->Employee->select($midwife_id, $select);
        if (count($employee_2) == 0) {
            $this->REST_Return(404, 'there is no employee with id ' . $midwife_id);
            return;
        }

        $datetime_start = $dt_put['datetime_start'];
        $water_breaks_since = $dt_put['water_breaks_since'];
        $stomachache_since = $dt_put['stomachache_since'];
        $gravida = $dt_put['gravida'];
        $para = $dt_put['para'];
        $abortus = $dt_put['abortus'];

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_partograph = array(
            'doctor_id' => $doctor_id,
            'midwife_id' => $midwife_id,
            'datetime_start' => $datetime_start,
            'water_breaks_since' => $water_breaks_since,
            'stomachache_since' => $stomachache_since,
            'gravida' => $gravida,
            'para' => $para,
            'abortus' => $abortus,
            'user_updated' => $user_id,
            'date_updated' => date('Y-m-d H:i:s'),
        );
    
        $this->$model->update($partograph_id, $dt_partograph);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_partograph,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update partograph', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }

    public function item_put($p = 0) {
        // CALL JSON POST
        $data       = $this->json_post();
        // DEFINE ATTR
        $dt_post    = $data['dt_postput'];
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
        $this->form_validation->set_data($dt_post);
        $this->form_validation->set_rules($this->create_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $partograph_id = $id;
    
        if ($partograph_id === 0 || $partograph_id === "0") {
            $this->REST_Return(400, 'partograph_id is required');
            return;
        }

        $select = 'a.id';
        $partograph = $this->$model->select($partograph_id, $select);

        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }

        $this->create_item_rules = array(
            array('field' => 'partograph_id', 'label' => 'partograph_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'observation_hours', 'label' => 'observation_hours', 'rules' => 'trim|required'),
            array('field' => 'item_type_id', 'label' => 'item_type_id', 'rules' => 'trim|required|max_length[2]'),
        );

        $items = $dt_post['items'];

        /* Start Transaction */
        $this->db->trans_start();

        for ($i=0; $i < count($items); $i++) { 
            $observation_hours = $items[$i]['observation_hours'] ?? null;
            $item_type_id = $items[$i]['item_type_id'] ?? null;

            if ($observation_hours == null) {
                $this->REST_Return(400, 'observation_hours in items is required');
                return;
            }

            if ($item_type_id == null) {
                $this->REST_Return(400, 'item_type_id in items is required');
                return;
            }

            $name_1 = $items[$i]['name_1'] ?? null;
            $name_2 = $items[$i]['name_2'] ?? null;
            $name_3 = $items[$i]['name_3'] ?? null;
            $value_1 = $items[$i]['value_1'] ?? null;
            $value_2 = $items[$i]['value_2'] ?? null;
            $value_3 = $items[$i]['value_3'] ?? null;

            $dt_partograph_item = array(
                'partograph_id'     => $partograph_id,
                'observation_hours' => $observation_hours,
                'item_type_id'      => $item_type_id,
                'name_1'            => $name_1,
                'name_2'            => $name_2,
                'name_3'            => $name_3,
                'value_1'           => $value_1,
                'value_2'           => $value_2,
                'value_3'           => $value_3,
                'user_updated'      => $user_id,
                'date_updated'      => date('Y-m-d H:i:s'),
                'is_delete'         => 0
            );
    
            $select = 'a.partograph_id';
            $where = array(
                'partograph_id' => $partograph_id,
                'observation_hours' => $observation_hours,
                'item_type_id' => $item_type_id,
                'is_delete' => 0,
            );
            $partograph_item = $this->Partograph_item->select(0, $select, $where);
    
            if (count($partograph_item) == 0) {
                $dt_partograph_item['user_added'] = $user_id;
                $dt_partograph_item['date_added'] = date('Y-m-d H:i:s');
            }
    
            $this->Partograph_item->replace($dt_partograph_item);
        }
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_partograph_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create/update partograph_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $partograph_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }

    public function item_delete_delete() {
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $model      = $this->name;
    
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] , $response_data);
            return;
        } 
    
        // Form Validation
        $this->form_validation->set_data($dt_put);
        $this->form_validation->set_rules($this->delete_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $partograph_id = $dt_put['partograph_id'];

        $select = 'a.id';
        $partograph = $this->$model->select($partograph_id, $select);

        if (count($partograph) == 0) {
            $this->REST_Return(404, 'there is no partograph with id ' . $partograph_id);
            return;
        }

        $observation_hours = $dt_put['observation_hours'];
        $item_type_id = $dt_put['item_type_id'];

        $select = 'a.partograph_id';
        $where = array(
            'partograph_id' => $partograph_id,
            'observation_hours' => $observation_hours,
            'item_type_id' => $item_type_id,
            'is_delete' => 0,
        );
        $partograph_item = $this->Partograph_item->select(0, $select, $where);

        if (count($partograph_item) == 0) {
            $this->REST_Return(404, 'there is no partograph_item with the given data');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();

        $dt_partograph_item = array(
            'partograph_id'     => $partograph_id,
            'observation_hours' => $observation_hours,
            'item_type_id'      => $item_type_id,
            'is_delete'         => 1,
            'user_updated'      => $user_id,
            'date_updated'      => date('Y-m-d H:i:s'),
        );

        $this->Partograph_item->replace($dt_partograph_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_partograph_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to delete partograph_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $partograph_id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}