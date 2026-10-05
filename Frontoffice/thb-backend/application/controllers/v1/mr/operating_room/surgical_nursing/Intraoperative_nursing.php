<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Intraoperative_nursing extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/health_facility/Operating_room_model", "Operating_room");
        $this->load->model("v1/hr/Employee_model", "Employee");

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
    
        $intraoperative_nursing_id = $id;
    
        if ($intraoperative_nursing_id === 0 || $intraoperative_nursing_id === "0") {
            $this->REST_Return(400, 'intraoperative_nursing_id is required');
            return;
        }
    
        $output = $this->$model->read($intraoperative_nursing_id);
        $response_data = array();

        if (count($output) > 0) {
            $output[0]->aids_position_arry = ($output[0]->aids_position_arry != "") ? explode(',', $output[0]->aids_position_arry) : [];
            $output[0]->tourniquet = json_decode($output[0]->tourniquet);
            $output[0]->drain_usage = json_decode($output[0]->drain_usage);
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
 
        $timeout_type_id = $dt_post['timeout_type_id'];
        $timeout = $dt_post['timeout'];
        $instrument_type_id = $dt_post['instrument_type_id'];
        $instrument = $dt_post['instrument'];
        $prosthesis_implant_type_id = $dt_post['prosthesis_implant_type_id'];
        $prosthesis_implant = $dt_post['prosthesis_implant'];
        $operation_start_time = $dt_post['operation_start_time'];
        $operation_finish_time = $dt_post['operation_finish_time'];
        $anesthesia_start_time = $dt_post['anesthesia_start_time'];
        $anesthesia_finish_time = $dt_post['anesthesia_finish_time'];
        $operation_performed = $dt_post['operation_performed'];
        $operation_type_id = $dt_post['operation_type_id'];
        $anesthesia_type_id = $dt_post['anesthesia_type_id'];
        $level_of_awareness_type_id = $dt_post['level_of_awareness_type_id'];
        $level_of_awareness_e = $dt_post['level_of_awareness_e'];
        $level_of_awareness_m = $dt_post['level_of_awareness_m'];
        $level_of_awareness_v = $dt_post['level_of_awareness_v'];
        $emotional_status_type_id = $dt_post['emotional_status_type_id'];
        $cannula_position_type_id = $dt_post['cannula_position_type_id'];
        $cannula_position = $dt_post['cannula_position'];
        $operation_position_type_id = $dt_post['operation_position_type_id'];
        $operation_position = $dt_post['operation_position'];
        $arm_position_type_id = $dt_post['arm_position_type_id'];
        $arm_position = $dt_post['arm_position'];
        $aids_position_arry = implode(',', $dt_post['aids_position_arry']);
        $aids_position = $dt_post['aids_position'];
        $urinary_catheter_type_id = $dt_post['urinary_catheter_type_id'];
        $skin_preparation_type_id = $dt_post['skin_preparation_type_id'];
        $skin_preparation = $dt_post['skin_preparation'];
        $diathermy_type_id = $dt_post['diathermy_type_id'];
        $dispersive_electrode_type_id = $dt_post['dispersive_electrode_type_id'];
        $dispersive_electrode = $dt_post['dispersive_electrode'];
        $skin_condition_before_type_id = $dt_post['skin_condition_before_type_id'];
        $skin_condition_before = $dt_post['skin_condition_before'];
        $skin_condition_after_type_id = $dt_post['skin_condition_after_type_id'];
        $skin_condition_after = $dt_post['skin_condition_after'];
        $heating_cooling_unit_type_id = $dt_post['heating_cooling_unit_type_id'];
        $temperature = $dt_post['temperature'];
        $temperature_start_time = $dt_post['temperature_start_time'];
        $temperature_finish_time = $dt_post['temperature_finish_time'];
        $tourniquet_supervised_by_id = $dt_post['tourniquet_supervised_by_id'];
        $use_of_laser_code = $dt_post['use_of_laser_code'];
        $laser_supervised_by_id = $dt_post['laser_supervised_by_id'];
        $use_of_implants_type_id = $dt_post['use_of_implants_type_id'];
        $implants_expired_date = $dt_post['implants_expired_date'];
        $implants_factory = $dt_post['implants_factory'];
        $implants_type = $dt_post['implants_type'];
        $implants_size = $dt_post['implants_size'];
        $implants_no = $dt_post['implants_no'];
        $drain_usage = $dt_post['drain_usage'];
        $wound_irrigation_arry = implode(',',$dt_post['wound_irrigation_arry']);
        $wound_irrigation = $dt_post['wound_irrigation'];
        $fluid_usage_arry = implode(',',$dt_post['fluid_usage_arry']);
        $fluid_usage_glycine = $dt_post['fluid_usage_glycine'];
        $fluid_usage_sterile_water = $dt_post['fluid_usage_sterile_water'];
        $fluid_usage_water_irrigation = $dt_post['fluid_usage_water_irrigation'];
        $fluid_usage_sodium_chloride = $dt_post['fluid_usage_sodium_chloride'];
        $fluid_usage_other = $dt_post['fluid_usage_other'];
        $tools_wrapped_type_id = $dt_post['tools_wrapped_type_id'];
        $tools_wrapped_type = $dt_post['tools_wrapped_type'];
        $tools_wrapped_other = $dt_post['tools_wrapped_other'];
        $wrapping_type_id = $dt_post['wrapping_type_id'];
        $wrapping_type = $dt_post['wrapping_type'];
        $specimen_type_id = $dt_post['specimen_type_id'];
        $specimen_histology = $dt_post['specimen_histology'];
        $specimen_cytology = $dt_post['specimen_cytology'];
        $specimen_culture = $dt_post['specimen_culture'];
        $specimen_other = $dt_post['specimen_other'];
        $specimen_other_type = $dt_post['specimen_other_type'];
        $amount_of_fluid_examination = $dt_post['amount_of_fluid_examination'];

        if($dt_post['operation_supervised_by_id'] != '' || $dt_post['operation_supervised_by_id'] != null) {
            $operation_supervised_by_id = $dt_post['operation_supervised_by_id'];

            $select = 'a.id';
            $operation_supervised_by = $this->Employee->select($operation_supervised_by_id, $select);

            if (count($operation_supervised_by) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $operation_supervised_by_id);
                return;
            }
        } else {
            $operation_supervised_by_id = null;
        }

        if($dt_post['tourniquet_supervised_by_id'] != '' || $dt_post['tourniquet_supervised_by_id'] != null) {
            $tourniquet_supervised_by_id = $dt_post['tourniquet_supervised_by_id'];

            $select = 'a.id';
            $tourniquet_supervised_by = $this->Employee->select($tourniquet_supervised_by_id, $select);

            if (count($tourniquet_supervised_by) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $tourniquet_supervised_by_id);
                return;
            }
        } else {
            $tourniquet_supervised_by_id = null;
        }

        if($dt_post['laser_supervised_by_id'] != '' || $dt_post['laser_supervised_by_id'] != null) {
            $laser_supervised_by_id = $dt_post['laser_supervised_by_id'];

            $select = 'a.id';
            $laser_supervised_by = $this->Employee->select($laser_supervised_by_id, $select);

            if (count($laser_supervised_by) == 0) {
                $this->REST_Return(404, 'there is no employee with id ' . $laser_supervised_by_id);
                return;
            }
        } else {
            $laser_supervised_by_id = null;
        }

        $drain_usage_data = (object) array(
            'data' => array(),
        );

        for ($i=0; $i < count($drain_usage); $i++) { 
            $type = $drain_usage[$i]['type'];
            $category = $drain_usage[$i]['category'];
            $notes = $drain_usage[$i]['notes'];
            $size = $drain_usage[$i]['size'];

            if ($type == null) {
                $this->REST_Return(400, 'field type in drain_usage is required');
                return;
            }

            if ($category == null) {
                $this->REST_Return(400, 'field category in drain_usage is required');
                return;
            }

            if ($notes == null) {
                $this->REST_Return(400, 'field notes in drain_usage is required');
                return;
            }

            if ($size == null) {
                $this->REST_Return(400, 'field size in drain_usage is required');
                return;
            }
            
            $dt_drain_usage = array(
                'type' => $type,
                'category' => $category,
                'notes' => $notes,
                'size' => $size,
            );

            array_push($drain_usage_data->data, $dt_drain_usage);   
        }

        $tourniquet_data = (object) array(
            'data' => array(
                'tourniquet_right_arm_start_time' =>  $dt_post['tourniquet']['tourniquet_right_arm_start_time'],
                'tourniquet_right_arm_finish_time' =>  $dt_post['tourniquet']['tourniquet_right_arm_finish_time'],
                'tourniquet_right_arm_pressure' =>  $dt_post['tourniquet']['tourniquet_right_arm_pressure'],
                'tourniquet_left_arm_start_time' =>  $dt_post['tourniquet']['tourniquet_left_arm_start_time'],
                'tourniquet_left_arm_finish_time' =>  $dt_post['tourniquet']['tourniquet_left_arm_finish_time'],
                'tourniquet_left_arm_pressure' =>  $dt_post['tourniquet']['tourniquet_left_arm_pressure'],
                'tourniquet_right_foot_start_time' =>  $dt_post['tourniquet']['tourniquet_right_foot_start_time'],
                'tourniquet_right_foot_finish_time' =>  $dt_post['tourniquet']['tourniquet_right_foot_finish_time'],
                'tourniquet_right_foot_pressure' =>  $dt_post['tourniquet']['tourniquet_right_foot_pressure'],
                'tourniquet_left_foot_start_time' =>  $dt_post['tourniquet']['tourniquet_left_foot_start_time'],
                'tourniquet_left_foot_finish_time' =>  $dt_post['tourniquet']['tourniquet_left_foot_finish_time'],
                'tourniquet_left_foot_pressure' =>  $dt_post['tourniquet']['tourniquet_left_foot_pressure'],
            ),
        );

        $tourniquet_encode = json_encode($tourniquet_data);
        $drain_usage_encode = json_encode($drain_usage_data);
    
        /* Start Transaction */
        $this->db->trans_start();

        // INTRAOPERATIVE
        $dt_intraoperative_nursing = array(
            'operating_room_id'             => $operating_room_id,
            'timeout_type_id'               => $timeout_type_id,
            'timeout'                       => $timeout,
            'instrument_type_id'            => $instrument_type_id,
            'instrument'                    => $instrument,
            'prosthesis_implant_type_id'    => $prosthesis_implant_type_id,
            'prosthesis_implant'            => $prosthesis_implant,
            'operation_start_time'          => $operation_start_time,
            'operation_finish_time'         => $operation_finish_time,
            'anesthesia_start_time'         => $anesthesia_start_time,
            'anesthesia_finish_time'        => $anesthesia_finish_time,
            'operation_performed'           => $operation_performed,
            'operation_type_id'             => $operation_type_id,
            'anesthesia_type_id'            => $anesthesia_type_id,
            'level_of_awareness_type_id'    => $level_of_awareness_type_id,
            'level_of_awareness_e'          => $level_of_awareness_e,
            'level_of_awareness_m'          => $level_of_awareness_m,
            'level_of_awareness_v'          => $level_of_awareness_v,
            'emotional_status_type_id'      => $emotional_status_type_id,
            'cannula_position_type_id'      => $cannula_position_type_id,
            'cannula_position'              => $cannula_position,
            'operation_position_type_id'    => $operation_position_type_id,
            'operation_position'            => $operation_position,
            'operation_supervised_by_id'    => $operation_supervised_by_id,
            'arm_position_type_id'          => $arm_position_type_id,
            'arm_position'                  => $arm_position,
            'aids_position_arry'            => $aids_position_arry,
            'aids_position'                 => $aids_position,
            'urinary_catheter_type_id'      => $urinary_catheter_type_id,
            'skin_preparation_type_id'      => $skin_preparation_type_id,
            'skin_preparation'              => $skin_preparation,
            'diathermy_type_id'             => $diathermy_type_id,
            'dispersive_electrode_type_id'  => $dispersive_electrode_type_id,
            'dispersive_electrode'          => $dispersive_electrode,
            'skin_condition_before_type_id' => $skin_condition_before_type_id,
            'skin_condition_before'         => $skin_condition_before,
            'skin_condition_after_type_id'  => $skin_condition_after_type_id,
            'skin_condition_after'          => $skin_condition_after,
            'heating_cooling_unit_type_id'  => $heating_cooling_unit_type_id,
            'temperature'                   => $temperature,
            'temperature_start_time'        => $temperature_start_time,
            'temperature_finish_time'       => $temperature_finish_time,
            'tourniquet_supervised_by_id'   => $tourniquet_supervised_by_id,
            'tourniquet'                    => $tourniquet_encode,
            'use_of_laser_code'             => $use_of_laser_code,
            'laser_supervised_by_id'        => $laser_supervised_by_id,
            'use_of_implants_type_id'       => $use_of_implants_type_id,
            'implants_expired_date'         => $implants_expired_date,
            'implants_factory'              => $implants_factory,
            'implants_type'                 => $implants_type,
            'implants_size'                 => $implants_size,
            'implants_no'                   => $implants_no,
            'drain_usage'                   => $drain_usage_encode,
            'wound_irrigation_arry'         => $wound_irrigation_arry,
            'wound_irrigation'              => $wound_irrigation,
            'fluid_usage_arry'              => $fluid_usage_arry,
            'fluid_usage_glycine'           => $fluid_usage_glycine,
            'fluid_usage_sterile_water'     => $fluid_usage_sterile_water,
            'fluid_usage_water_irrigation'  => $fluid_usage_water_irrigation,
            'fluid_usage_sodium_chloride'   => $fluid_usage_sodium_chloride,
            'fluid_usage_other'             => $fluid_usage_other,
            'tools_wrapped_type_id'         => $tools_wrapped_type_id,
            'tools_wrapped_type'            => $tools_wrapped_type,
            'tools_wrapped_other'           => $tools_wrapped_other,
            'wrapping_type_id'              => $wrapping_type_id,
            'wrapping_type'                 => $wrapping_type,
            'specimen_type_id'              => $specimen_type_id,
            'specimen_histology'            => $specimen_histology,
            'specimen_cytology'             => $specimen_cytology,
            'specimen_culture'              => $specimen_culture,
            'specimen_other'                => $specimen_other,
            'specimen_other_type'           => $specimen_other_type,
            'amount_of_fluid_examination'   => $amount_of_fluid_examination,
            'user_added'                    => $user_id,
            'date_added'                    => date('Y-m-d H:i:s',time()),
            'date_updated'                  => date('Y-m-d H:i:s',time()),
        );
    
        $id = $this->$model->create($dt_intraoperative_nursing);
    
        $this->db->trans_complete();
        /* End Transaction */
    
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => $dt_intraoperative_nursing,
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying to create intraoperative_nursing', $response_data);
            return;
        }
    
        $response_data = array(
            'id' => $id
        );
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;     
    
    }
}