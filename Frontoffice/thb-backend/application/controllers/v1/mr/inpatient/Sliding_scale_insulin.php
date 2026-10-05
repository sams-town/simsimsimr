<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Sliding_scale_insulin extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("v1/mr/Registration_destination_model", "Registration_destination");
        $this->load->model("v1/hr/Employee_model", "Employee");

        $this->create_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'employee_nurse_id', 'label' => 'employee_nurse_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'monitoring_date', 'label' => 'monitoring_date', 'rules' => 'trim|required'),
        );

        $this->update_rules = array(
            array('field' => 'registration_destination_id', 'label' => 'registration_destination_id', 'rules' => 'trim|max_length[36]'),
        );

        $this->create_item_rules = array(
            array('field' => 'sliding_scale_insulin_id', 'label' => 'sliding_scale_insulin_id', 'rules' => 'trim|required|max_length[36]'),
            array('field' => 'time', 'label' => 'time', 'rules' => 'trim|required'),
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
    
        $sliding_scale_insulin_id = $id;
    
        if ($sliding_scale_insulin_id === 0 || $sliding_scale_insulin_id === "0") {
            $this->REST_Return(400, 'sliding_scale_insulin_id is required');
            return;
        }
    
        $this->$model->whereFields['a.is_delete'] = 0;
        $output = $this->$model->read($sliding_scale_insulin_id);
        $response_data = array();
    
        if (count($output) > 0) {
            $sliding_scale_insulin_id = $output[0]->id;

            $select = 'a.*';
            $where = array('sliding_scale_insulin_id' => $sliding_scale_insulin_id);
            $sliding_scale_insulin_item = $this->Sliding_scale_insulin_item->select(0, $select, $where);

            for ($i=0; $i < count($sliding_scale_insulin_item); $i++) { 
                $sliding_scale_insulin_item[$i]->gds_hiperglikemia = json_decode($sliding_scale_insulin_item[$i]->gds_hiperglikemia);
                $sliding_scale_insulin_item[$i]->novorapid_hiperglikemia = json_decode($sliding_scale_insulin_item[$i]->novorapid_hiperglikemia);
                $sliding_scale_insulin_item[$i]->gds_hipoglikemia = json_decode($sliding_scale_insulin_item[$i]->gds_hipoglikemia);
                $sliding_scale_insulin_item[$i]->novorapid_hipoglikemia = json_decode($sliding_scale_insulin_item[$i]->novorapid_hipoglikemia);
            }

            $output[0]->sliding_scale_insulin_item = $sliding_scale_insulin_item;
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

        for ($i=0; $i < count($output->rows); $i++) { 
            $sliding_scale_insulin_id = $output->rows[$i]->id;


            $select = 'a.*';
            $where = array('sliding_scale_insulin_id' => $sliding_scale_insulin_id, 'a.is_delete' => 0);
            $sliding_scale_insulin_items = $this->Sliding_scale_insulin_item->select(0, $select, $where);

            $output->rows[$i]->sliding_scale_insulin_items = $sliding_scale_insulin_items;
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

        $monitoring_date = $dt_post['monitoring_date'];

        $select = 'a.id';
        $where = array(
            'registration_destination_id' => $registration_destination_id,
            'monitoring_date' => $monitoring_date,
        );
        $sliding_scale_insulin = $this->$model->select(0, $select, $where);

        if (count($sliding_scale_insulin) > 0) {
            $this->REST_Return(409, 'sliding_scale_insulin on ' . $monitoring_date . ' is already exist');
            return;
        }

        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_sliding_scale_insulin = array(
            'registration_destination_id'   => $registration_destination_id,
            'employee_nurse_id'             => $employee_nurse_id,
            'monitoring_date'               => $monitoring_date,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );

        $id = $this->$model->create($dt_sliding_scale_insulin);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sliding_scale_insulin,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create sliding_scale_insulin', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }

    // public function update_put($p = 0) {
    //     // CALL JSON POST
    //     $data       = $this->json_put();
    //     // DEFINE ATTR
    //     $dt_put     = $data['dt_postput'];
    //     $model      = $this->name;
    //     $id         = $this->security->xss_clean($p);
    
    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] , $response_data);
    //         return;
    //     } 
    
    //     // Form Validation
    //     $this->form_validation->set_data($dt_put);
    //     $this->form_validation->set_rules($this->update_rules);
    //     if ($this->form_validation->run() == FALSE){
    //         // Form Validation Errors
    //         $response_data = $this->form_validation->error_array();
    //         $this->REST_Return(422, 'Format error', $response_data);
    //         return;
    //     }
    
    //     $user_id = $this->security->xss_clean($is_valid_token['data']->id);
    
    //     $sliding_scale_insulin_item_id = $id;
    
    //     if ($sliding_scale_insulin_item_id === 0 || $sliding_scale_insulin_item_id === "0") {
    //         $this->REST_Return(400, 'sliding_scale_insulin_item_id is required');
    //         return;
    //     }
        
    //     $select = 'a.id';
    //     $sliding_scale_insulin_item = $this->Sliding_scale_insulin_item->select($sliding_scale_insulin_item_id, $select);
        
    //     if (count($sliding_scale_insulin_item) == 0) {
    //         $this->REST_Return(404, 'there is no sliding_scale_insulin_item with id ' . $sliding_scale_insulin_item_id);
    //         return;
    //     }

    //     $gds_hiperglikemia = $dt_put['gds_hiperglikemia'];
    //     $gds_hipoglikemia = $dt_put['gds_hipoglikemia'];

    //     $gds_hiperglikemia_data = (object) array(
    //         'data' => array(),
    //     );

    //     $novorapid_hiperglikemia_data = (object) array(
    //         'data' => array(),
    //     );

    //     $gds_hipoglikemia_data = (object) array(
    //         'data' => array(),
    //     );

    //     $novorapid_hipoglikemia_data = (object) array(
    //         'data' => array(),
    //     );

    //     for ($i=0; $i < count($gds_hiperglikemia); $i++) { 
    //         $observation_hours = $gds_hiperglikemia[$i]['observation_hours'];
    //         $value = $gds_hiperglikemia[$i]['value'];

    //         if ($observation_hours == null) {
    //             $this->REST_Return(400, 'field observation_hours in gds_hiperglikemia is required');
    //             return;
    //         }

    //         if ($value == null) {
    //             $this->REST_Return(400, 'field value in gds_hiperglikemia is required');
    //             return;
    //         }
            
    //         $dt_gds_hiperglikemia = array(
    //             'observation_hours' => $observation_hours,
    //             'value' => $value,
    //             'user_added' => $user_id,
    //         );
            
    //         $value = (int) $value;

    //         // NOTE - HARDCODE NOVORAPID
    //         if ($value < 100) {
    //             $novorapid = 'Lapor';
    //         } elseif ($value >= 100 && $value < 200) {
    //             $novorapid = 'Tanpa Extra';
    //         } elseif ($value >= 200 && $value < 250) {
    //             $novorapid = 'Extra 5 iu';
    //         } elseif ($value >= 250 && $value < 300) {
    //             $novorapid = 'Extra 10 iu';
    //         } elseif ($value >= 300 && $value < 350) {
    //             $novorapid = 'Extra 15 iu';
    //         } elseif ($value >= 350 && $value < 400) {
    //             $novorapid = 'Extra 20 iu';
    //         }

    //         $dt_novorapid_hiperglikemia = array(
    //             'observation_hours' => $observation_hours,
    //             'novorapid' => $novorapid,
    //             'user_added' => $user_id,
    //         );

    //         array_push($gds_hiperglikemia_data->data, $dt_gds_hiperglikemia);        
    //         array_push($novorapid_hiperglikemia_data->data, $dt_novorapid_hiperglikemia);        
    //     }

    //     for ($i=0; $i < count($gds_hipoglikemia); $i++) { 
    //         $observation_hours = $gds_hipoglikemia[$i]['observation_hours'];
    //         $value = $gds_hipoglikemia[$i]['value'];

    //         if ($observation_hours == null) {
    //             $this->REST_Return(400, 'field observation_hours in gds_hipoglikemia is required');
    //             return;
    //         }

    //         if ($value == null) {
    //             $this->REST_Return(400, 'field value in gds_hipoglikemia is required');
    //             return;
    //         }
            
    //         $dt_gds_hipoglikemia = array(
    //             'observation_hours' => $observation_hours,
    //             'value' => $value,
    //             'user_added' => $user_id,
    //         );

    //         $value = (int) $value;

    //         // NOTE - HARDCODE NOVORAPID
    //         if ($value < 60) {
    //             $novorapid = 'D40% 2 Flash';
    //         } elseif ($value >= 60 && $value < 100) {
    //             $novorapid = 'D40% 1 Flash';
    //         } elseif ($value >= 100 && $value < 200) {
    //             $novorapid = 'D40% Tidak diberikan';
    //         } elseif ($value >= 200) {
    //             $novorapid = 'D10% Ganti NS';
    //         }

    //         $dt_novorapid_hipoglikemia = array(
    //             'observation_hours' => $observation_hours,
    //             'novorapid' => $novorapid,
    //             'user_added' => $user_id,
    //         );

    //         array_push($gds_hipoglikemia_data->data, $dt_gds_hipoglikemia);        
    //         array_push($novorapid_hipoglikemia_data->data, $dt_novorapid_hipoglikemia);        
    //     }

    //     $gds_hiperglikemia = json_encode($gds_hiperglikemia_data);
    //     $novorapid_hiperglikemia = json_encode($novorapid_hiperglikemia_data);
    //     $gds_hipoglikemia = json_encode($gds_hipoglikemia_data);
    //     $novorapid_hipoglikemia = json_encode($novorapid_hipoglikemia_data);

    //     /* Start Transaction */
    //     $this->db->trans_start();
    
    //     $dt_sliding_scale_insulin_item = array(
    //         'gds_hiperglikemia'         => $gds_hiperglikemia,
    //         'novorapid_hiperglikemia'   => $novorapid_hiperglikemia,
    //         'gds_hipoglikemia'          => $gds_hipoglikemia,
    //         'novorapid_hipoglikemia'    => $novorapid_hipoglikemia,
    //         'user_updated'              => $user_id,
    //         'date_updated'              => date('Y-m-d H:i:s',time()),
    //     );
    
    //     $this->Sliding_scale_insulin_item->update($sliding_scale_insulin_item_id, $dt_sliding_scale_insulin_item);
    
    //     $this->db->trans_complete();
    //     /* End Transaction */
    
    //     if ($this->db->trans_status() === false) {
    //         $response_data = array(
    //             'error'     => $this->db->error(),
    //             'data'      => $dt_sliding_scale_insulin_item,
    //             'query'     => $this->db->last_query()
    //         );
    //         $this->REST_Return(500, 'error while trying to update sliding_scale_insulin_item', $response_data);
    //         return;
    //     }
    
    //     $response_data = array(
    //         'id'     => $id
    //     );
    
    //     $this->REST_Return(200, 'SUCCESS', $response_data);
    // }


    public function create_item_post() {
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
        $this->form_validation->set_rules($this->create_item_rules);
        if ($this->form_validation->run() == FALSE){
            // Form Validation Errors
            $response_data = $this->form_validation->error_array();
            $this->REST_Return(422, 'Format error', $response_data);
            return;
        }
    
        $user_id = $this->security->xss_clean($is_valid_token['data']->id);

        $sliding_scale_insulin_id = $dt_post['sliding_scale_insulin_id'];

        $select = 'a.id';
        $sliding_scale_insulin = $this->$model->select($sliding_scale_insulin_id, $select);

        if (count($sliding_scale_insulin) == 0) {
            $this->REST_Return(404, 'there is no sliding_scale_insulin with id ' . $sliding_scale_insulin_id);
            return;
        }

        $time = date('H', strtotime($dt_post['time'])) . ':00:00';

        $select = 'a.id';
        $where = array(
            'sliding_scale_insulin_id' => $sliding_scale_insulin_id,
            'time' => $time,
        );
        $ssi = $this->Sliding_scale_insulin_item->select(0, $select, $where);

        if (count($ssi) > 0) {
            $this->REST_Return(409, 'sliding_scale_insulin_item on ' . $time . ' is already exist');
            return;
        }

        $gds_hiperglikemia = (float) $dt_post['gds_hiperglikemia'];
        $gds_hipoglikemia = (float) $dt_post['gds_hipoglikemia'];

        // NOTE - HARDCODE NOVORAPID
        if ($gds_hiperglikemia < 100) {
            $novorapid_hiperglikemia = 'Lapor';
        } elseif ($gds_hiperglikemia >= 100 && $gds_hiperglikemia < 200) {
            $novorapid_hiperglikemia = 'Tanpa Extra';
        } elseif ($gds_hiperglikemia >= 200 && $gds_hiperglikemia < 250) {
            $novorapid_hiperglikemia = 'Extra 5 iu';
        } elseif ($gds_hiperglikemia >= 250 && $gds_hiperglikemia < 300) {
            $novorapid_hiperglikemia = 'Extra 10 iu';
        } elseif ($gds_hiperglikemia >= 300 && $gds_hiperglikemia < 350) {
            $novorapid_hiperglikemia = 'Extra 15 iu';
        } elseif ($gds_hiperglikemia >= 350 && $gds_hiperglikemia < 400) {
            $novorapid_hiperglikemia = 'Extra 20 iu';
        }

        // NOTE - HARDCODE NOVORAPID
        if ($gds_hipoglikemia < 60) {
            $novorapid_hipoglikemia = 'D40% 2 Flash';
        } elseif ($gds_hipoglikemia >= 60 && $gds_hipoglikemia < 100) {
            $novorapid_hipoglikemia = 'D40% 1 Flash';
        } elseif ($gds_hipoglikemia >= 100 && $gds_hipoglikemia < 200) {
            $novorapid_hipoglikemia = 'D40% Tidak diberikan';
        } elseif ($gds_hipoglikemia >= 200) {
            $novorapid_hipoglikemia = 'D10% Ganti NS';
        }

        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_sliding_scale_insulin_item = array(
            'sliding_scale_insulin_id'  => $sliding_scale_insulin_id,
            'time'                      => $time,
            'gds_hiperglikemia'         => $gds_hiperglikemia,
            'novorapid_hiperglikemia'   => $novorapid_hiperglikemia,
            'gds_hipoglikemia'          => $gds_hipoglikemia,
            'novorapid_hipoglikemia'    => $novorapid_hipoglikemia,
            'user_added'                => $user_id,
            'date_added'                => date('Y-m-d H:i:s',time()),
            'date_updated'              => date('Y-m-d H:i:s',time()),
        );

        $id = $this->Sliding_scale_insulin_item->create($dt_sliding_scale_insulin_item);

        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sliding_scale_insulin_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create sliding_scale_insulin_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    }

    public function update_item_put($p = 0) {
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
    
        $sliding_scale_insulin_item_id = $id;
    
        if ($sliding_scale_insulin_item_id === 0 || $sliding_scale_insulin_item_id === "0") {
            $this->REST_Return(400, 'sliding_scale_insulin_item_id is required');
            return;
        }

        $select = 'a.id';
        $ssi = $this->Sliding_scale_insulin_item->select($sliding_scale_insulin_item_id, $select);

        if (count($ssi) == 0) {
            $this->REST_Return(404, 'there is no sliding_scale_insulin_item with id ' . $sliding_scale_insulin_item_id);
            return;
        }

        $gds_hiperglikemia = (float) $dt_put['gds_hiperglikemia'];
        $gds_hipoglikemia = (float) $dt_put['gds_hipoglikemia'];

        // NOTE - HARDCODE NOVORAPID
        if ($gds_hiperglikemia < 100) {
            $novorapid_hiperglikemia = 'Lapor';
        } elseif ($gds_hiperglikemia >= 100 && $gds_hiperglikemia < 200) {
            $novorapid_hiperglikemia = 'Tanpa Extra';
        } elseif ($gds_hiperglikemia >= 200 && $gds_hiperglikemia < 250) {
            $novorapid_hiperglikemia = 'Extra 5 iu';
        } elseif ($gds_hiperglikemia >= 250 && $gds_hiperglikemia < 300) {
            $novorapid_hiperglikemia = 'Extra 10 iu';
        } elseif ($gds_hiperglikemia >= 300 && $gds_hiperglikemia < 350) {
            $novorapid_hiperglikemia = 'Extra 15 iu';
        } elseif ($gds_hiperglikemia >= 350 && $gds_hiperglikemia < 400) {
            $novorapid_hiperglikemia = 'Extra 20 iu';
        }

        // NOTE - HARDCODE NOVORAPID
        if ($gds_hipoglikemia < 60) {
            $novorapid_hipoglikemia = 'D40% 2 Flash';
        } elseif ($gds_hipoglikemia >= 60 && $gds_hipoglikemia < 100) {
            $novorapid_hipoglikemia = 'D40% 1 Flash';
        } elseif ($gds_hipoglikemia >= 100 && $gds_hipoglikemia < 200) {
            $novorapid_hipoglikemia = 'D40% Tidak diberikan';
        } elseif ($gds_hipoglikemia >= 200) {
            $novorapid_hipoglikemia = 'D10% Ganti NS';
        }

        /* Start Transaction */
        $this->db->trans_start();
        
        $dt_sliding_scale_insulin_item = array(
            'gds_hiperglikemia'         => $gds_hiperglikemia,
            'novorapid_hiperglikemia'   => $novorapid_hiperglikemia,
            'gds_hipoglikemia'          => $gds_hipoglikemia,
            'novorapid_hipoglikemia'    => $novorapid_hipoglikemia,
            'user_updated'              => $user_id,
            'date_updated'              => date('Y-m-d H:i:s',time()),
        );
    
        $this->Sliding_scale_insulin_item->update($sliding_scale_insulin_item_id, $dt_sliding_scale_insulin_item);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_sliding_scale_insulin_item,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to update sliding_scale_insulin_item', $response_data);
            return;
        }
    
        $response_data = array(
            'id'     => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
    }
}