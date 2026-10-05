<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Preanesthesia_evaluation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");

        $this->create_rules = array(
            array('field' => 'operating_room_id', 'label' => 'operating_room_id', 'rules' => 'trim|required|max_length[36]'),
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
    
        $preanesthesia_evaluation_id = $id;
    
        if ($preanesthesia_evaluation_id === 0 || $preanesthesia_evaluation_id === "0") {
            $this->REST_Return(400, 'preanesthesia_evaluation_id is required');
            return;
        }
    
        $output = $this->$model->read($preanesthesia_evaluation_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->respiratory_path_arry = ($output[0]->respiratory_path_arry != "") ? explode(',', $output[0]->respiratory_path_arry) : [];
            $output[0]->respiration_arry = ($output[0]->respiration_arry != "") ? explode(',', $output[0]->respiration_arry) : [];
            $output[0]->cardiovascular_arry = ($output[0]->cardiovascular_arry != "") ? explode(',', $output[0]->cardiovascular_arry) : [];
            $output[0]->digestive_system_arry = ($output[0]->digestive_system_arry != "") ? explode(',', $output[0]->digestive_system_arry) : [];
            $output[0]->kidney_arry = ($output[0]->kidney_arry != "") ? explode(',', $output[0]->kidney_arry) : [];
            $output[0]->other_arry = ($output[0]->other_arry != "") ? explode(',', $output[0]->other_arry) : [];
            $output[0]->anesthesia_plan_arry = ($output[0]->anesthesia_plan_arry != "") ? explode(',', $output[0]->anesthesia_plan_arry) : [];
            $output[0]->premedication = json_decode($output[0]->premedication);
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

        $operating_room_id = $dt_post['operating_room_id'];

        $select = 'a.id';
        $operating_room = $this->Operating_room->select($operating_room_id, $select);

        if (count($operating_room) == 0) {
            $this->REST_Return(404, 'there is no operating_room with id ' . $operating_room_id);
            return;
        }

        $awareness = $dt_post['awareness'];
        $weight = $dt_post['weight'];
        $height = $dt_post['height'];
        $systolic = $dt_post['systolic'];
        $diastolic = $dt_post['diastolic'];
        $pulse = $dt_post['pulse'];
        $respiratory_rate = $dt_post['respiratory_rate'];
        $temperature = $dt_post['temperature'];
        $cvp_sp02 = $dt_post['cvp_sp02'];
        $history_complications_type_id = $dt_post['history_complications_type_id'];
        $tooth_type_id = $dt_post['tooth_type_id'];
        $dentures_type_id = $dt_post['dentures_type_id'];
        $dentures_position_type_id = $dt_post['dentures_position_type_id'];
        $respiratory_path_arry = implode(',', $dt_post['respiratory_path_arry']);
        $mallampati_score_type_id = $dt_post['mallampati_score_type_id'];
        $respiration_arry = implode(',', $dt_post['respiration_arry']);
        $respiration_cigarrete_amount = $dt_post['respiration_cigarrete_amount'];
        $respiration_cigarrete_long = $dt_post['respiration_cigarrete_long'];
        $respiration_asthma = $dt_post['respiration_asthma'];
        $cardiovascular_arry = implode(',', $dt_post['cardiovascular_arry']);
        $cardiovascular_other = $dt_post['cardiovascular_other'];
        $digestive_system_arry = implode(',', $dt_post['digestive_system_arry']);
        $digestive_system_other = $dt_post['digestive_system_other'];
        $neuromusculoskeletal_arry = implode(',', $dt_post['neuromusculoskeletal_arry']);
        $neuromusculoskeletal_other = $dt_post['neuromusculoskeletal_other'];
        $kidney_arry = implode(',', $dt_post['kidney_arry']);
        $other_arry = implode(',', $dt_post['other_arry']);
        $medications_currently = $dt_post['medications_currently'];
        $allergy = $dt_post['allergy'];
        $last_meal_hours = $dt_post['last_meal_hours'];
        $last_drink_hours = $dt_post['last_drink_hours'];
        $anesthesia_plan_arry = implode(',', $dt_post['anesthesia_plan_arry']);
        $anesthesia_asa_type_id = $dt_post['anesthesia_asa_type_id'];
        $anesthesia_plan_type_id = $dt_post['anesthesia_plan_type_id'];
        $premedication = $dt_post['premedication'];
 
        $premedication_data = array(
            'data' => array(),
        );
        
        for ($i=0; $i < count($premedication); $i++) { 
            if (!isset($premedication[$i]['drug']) || !isset($premedication[$i]['time'])) {
                $this->REST_Return(400, 'drug & time field in premedication is required');
                return;
            }

            $drug = $premedication[$i]['drug'];
            $time = $premedication[$i]['time'];

            $dt_premedication = array(
                'drug' => $drug,
                'time' => $time,
            );

            array_push($premedication_data['data'], $dt_premedication);
        }

        $premedication_encode = json_encode($premedication_data);

        /* Start Transaction */
        $this->db->trans_start();
    
        $dt_preanesthesia_evaluation = array(
            'operating_room_id'             => $operating_room_id,
            'awareness'                     => $awareness,
            'weight'                        => $weight,
            'height'                        => $height,
            'systolic'                      => $systolic,
            'diastolic'                     => $diastolic,
            'pulse'                         => $pulse,
            'respiratory_rate'              => $respiratory_rate,
            'temperature'                   => $temperature,
            'cvp_sp02'                      => $cvp_sp02,
            'history_complications_type_id' => $history_complications_type_id,
            'tooth_type_id'                 => $tooth_type_id,
            'dentures_type_id'              => $dentures_type_id,
            'dentures_position_type_id'     => $dentures_position_type_id,
            'respiratory_path_arry'         => $respiratory_path_arry,
            'mallampati_score_type_id'      => $mallampati_score_type_id,
            'respiration_arry'              => $respiration_arry,
            'respiration_cigarrete_amount'  => $respiration_cigarrete_amount,
            'respiration_cigarrete_long'    => $respiration_cigarrete_long,
            'respiration_asthma'            => $respiration_asthma,
            'cardiovascular_arry'           => $cardiovascular_arry,
            'cardiovascular_other'          => $cardiovascular_other,
            'digestive_system_arry'         => $digestive_system_arry,
            'digestive_system_other'        => $digestive_system_other,
            'neuromusculoskeletal_arry'     => $neuromusculoskeletal_arry,
            'neuromusculoskeletal_other'    => $neuromusculoskeletal_other,
            'kidney_arry'                   => $kidney_arry,
            'other_arry'                    => $other_arry,
            'medications_currently'         => $medications_currently,
            'allergy'                       => $allergy,
            'last_meal_hours'               => $last_meal_hours,
            'last_drink_hours'              => $last_drink_hours,
            'anesthesia_plan_arry'          => $anesthesia_plan_arry,
            'anesthesia_asa_type_id'        => $anesthesia_asa_type_id,
            'anesthesia_plan_type_id'       => $anesthesia_plan_type_id,
            'premedication'                 => $premedication_encode,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
        
        $id = $this->$model->create($dt_preanesthesia_evaluation);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_preanesthesia_evaluation,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create preanesthesia_evaluation', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }
}