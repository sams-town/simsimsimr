<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Soap_o extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_o_action_model", "Soap_o_action");

        $this->update_rules = array(
            array('field' => 'physical_check', 'label' => 'physical_check', 'rules' => 'trim'),
            array('field' => 'anythingelse', 'label' => 'anythingelse', 'rules' => 'trim'),
            array('field' => 'systolic_blood_pressure', 'label' => 'systolic_blood_pressure', 'rules' => 'trim|max_length[8]'),
            array('field' => 'diastolic_blood_pressure', 'label' => 'diastolic_blood_pressure', 'rules' => 'trim|max_length[8]'),
            array('field' => 'classification_blood_pressure_type_id', 'label' => 'classification_blood_pressure_type_id', 'rules' => 'trim|max_length[1]'),
            array('field' => 'pulse_frequency', 'label' => 'pulse_frequency', 'rules' => 'trim|max_length[8]'),
            array('field' => 'breath_frequency', 'label' => 'breath_frequency', 'rules' => 'trim|max_length[8]'),
            array('field' => 'oximetry_frequency', 'label' => 'oximetry_frequency', 'rules' => 'trim|max_length[8]'),
            array('field' => 'classification_oximetry_type_id', 'label' => 'classification_oximetry_type_id', 'rules' => 'trim|max_length[1]'),
            array('field' => 'temperature', 'label' => 'temperature', 'rules' => 'trim|max_length[8]'),
            array('field' => 'classification_temperature_type_id', 'label' => 'classification_temperature_type_id', 'rules' => 'trim|max_length[1]'),
            array('field' => 'blood_sugar', 'label' => 'blood_sugar', 'rules' => 'trim|max_length[8]'),
            array('field' => 'weight', 'label' => 'weight', 'rules' => 'trim|max_length[8]'),
            array('field' => 'classification_weight_type_id', 'label' => 'classification_weight_type_id', 'rules' => 'trim|max_length[1]'),
            array('field' => 'height', 'label' => 'height', 'rules' => 'trim|max_length[8]'),
            array('field' => 'imt', 'label' => 'imt', 'rules' => 'trim|max_length[8]'),
            array('field' => 'head_circumference', 'label' => 'head_circumference', 'rules' => 'trim|max_length[8]'),
            array('field' => 'chest_circumference', 'label' => 'chest_circumference', 'rules' => 'trim|max_length[8]'),
            array('field' => 'arm_circumference', 'label' => 'arm_circumference', 'rules' => 'trim|max_length[8]'),
            array('field' => 'stomach_circumference', 'label' => 'stomach_circumference', 'rules' => 'trim|max_length[8]'),
            array('field' => 'knee_circumference', 'label' => 'knee_circumference', 'rules' => 'trim|max_length[8]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
            array('field' => 'name', 'label' => 'name', 'rules' => 'trim|max_length[100]'),
        );
    }

    /**
     * Soap_o API
     * --------------------
     * @param: soap_id
     * @param: physical_check
     * @param: anythingelse
     * @param: systolic_blood_pressure
     * @param: diastolic_blood_pressure
     * @param: classification_blood_pressure_type_id
     * @param: pulse_frequency
     * @param: breath_frequency
     * @param: oximetry_frequency
     * @param: classification_oximetry_type_id
     * @param: temperature
     * @param: classification_temperature_type_id
     * @param: blood_sugar
     * @param: weight
     * @param: classification_weight_type_id
     * @param: height
     * @param: imt
     * @param: head_circumference
     * @param: chest_circumference
     * @param: arm_circumference
     * @param: stomach_circumference
     * @param: knee_circumference
     * @param: canvasing_type_id
     * @param: canvasing_text
     * @param: attr1
     * @param: attr2
     * --------------------
     * @method : POST
     * @link: version/mr/soap_o
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
            // DATA SOAP_O
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('physical_check', 'physical_check', 'trim');
            $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim');
            $this->form_validation->set_rules('systolic_blood_pressure', 'systolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('diastolic_blood_pressure', 'diastolic_blood_pressure', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_blood_pressure_type_id', 'classification_blood_pressure_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('pulse_frequency', 'pulse_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('breath_frequency', 'breath_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('oximetry_frequency', 'oximetry_frequency', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_oximetry_type_id', 'classification_oximetry_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('temperature', 'temperature', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_temperature_type_id', 'classification_temperature_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('blood_sugar', 'blood_sugar', 'trim|max_length[8]');
            $this->form_validation->set_rules('weight', 'weight', 'trim|max_length[8]');
            $this->form_validation->set_rules('classification_weight_type_id', 'classification_weight_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('height', 'height', 'trim|max_length[8]');
            $this->form_validation->set_rules('imt', 'imt', 'trim|max_length[8]');
            $this->form_validation->set_rules('head_circumference', 'head_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('chest_circumference', 'chest_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('arm_circumference', 'arm_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('stomach_circumference', 'stomach_circumference', 'trim|max_length[8]');
            $this->form_validation->set_rules('knee_circumference', 'knee_circumference', 'trim|max_length[8]');
            // DATA SOAP_O_ACTION
            $this->form_validation->set_rules('canvasing_type_id', 'canvasing_type_id', 'trim|max_length[1]');
            $this->form_validation->set_rules('canvasing_text', 'canvasing_text', 'trim');
            $this->form_validation->set_rules('attr1', 'attr1', 'trim');
            $this->form_validation->set_rules('attr2', 'attr2', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check soap_id
                if($this->Soap->read($dt_post['soap_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();

                    // INIT_SOAP_O
                    $dt_soap_o = array(
                        'soap_id'                                   => $dt_post['soap_id'],
                        'physical_check'                            => isset($dt_post['physical_check']) ? $dt_post['physical_check'] : '',
                        'anythingelse'                              => isset($dt_post['anythingelse']) ? $dt_post['anythingelse'] : '',
                        'systolic_blood_pressure'                   => isset($dt_post['systolic_blood_pressure']) ? $dt_post['systolic_blood_pressure'] : '',
                        'diastolic_blood_pressure'                  => isset($dt_post['diastolic_blood_pressure']) ? $dt_post['diastolic_blood_pressure'] : '',
                        'classification_blood_pressure_type_id'     => isset($dt_post['classification_blood_pressure_type_id']) ? $dt_post['classification_blood_pressure_type_id'] : '',
                        'pulse_frequency'                           => isset($dt_post['pulse_frequency']) ? $dt_post['pulse_frequency'] : '',
                        'breath_frequency'                          => isset($dt_post['breath_frequency']) ? $dt_post['breath_frequency'] : '',
                        'oximetry_frequency'                        => isset($dt_post['oximetry_frequency']) ? $dt_post['oximetry_frequency'] : '',
                        'classification_oximetry_type_id'           => isset($dt_post['classification_oximetry_type_id']) ? $dt_post['classification_oximetry_type_id'] : '',
                        'temperature'                               => isset($dt_post['temperature']) ? $dt_post['temperature'] : '',
                        'classification_temperature_type_id'        => isset($dt_post['classification_temperature_type_id']) ? $dt_post['classification_temperature_type_id'] : '',
                        'blood_sugar'                               => isset($dt_post['blood_sugar']) ? $dt_post['blood_sugar'] : '',
                        'weight'                                    => isset($dt_post['weight']) ? $dt_post['weight'] : '',
                        'classification_weight_type_id'             => isset($dt_post['classification_weight_type_id']) ? $dt_post['classification_weight_type_id'] : '',
                        'height'                                    => isset($dt_post['height']) ? $dt_post['height'] : '',
                        'imt'                                       => isset($dt_post['imt']) ? $dt_post['imt'] : '',
                        'head_circumference'                        => isset($dt_post['head_circumference']) ? $dt_post['head_circumference'] : '',
                        'chest_circumference'                       => isset($dt_post['chest_circumference']) ? $dt_post['chest_circumference'] : '',
                        'arm_circumference'                         => isset($dt_post['arm_circumference']) ? $dt_post['arm_circumference'] : '',
                        'stomach_circumference'                     => isset($dt_post['stomach_circumference']) ? $dt_post['stomach_circumference'] : '',
                        'knee_circumference'                        => isset($dt_post['knee_circumference']) ? $dt_post['knee_circumference'] : '',
                    );
                    $id = $this->$model->create($dt_soap_o);

                    // INIT_SOAP_O_ACTION
                    $dt_soap_o_action = array(
                        'soap_o_id'                 => $id,
                        'canvasing_type_id'         => isset($dt_post['canvasing_type_id']) ? $dt_post['canvasing_type_id'] : '',
                        'canvasing_text'            => isset($dt_post['canvasing_text']) ? $dt_post['canvasing_text'] : '',
                        'attr1'                     => isset($dt_post['attr1']) ? $dt_post['attr1'] : '',
                        'attr2'                     => isset($dt_post['attr2']) ? $dt_post['attr2'] : ''
                    );
                    $id_soap_o_action = $this->Soap_o_action->create($dt_soap_o_action);

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
                } else {
                    $this->REST_Return(404, 'Wrong soap_id!');
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
     * GET Soap_o with API
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
            $output = $this->$model->read($id);
            $response_data = array();
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


    // /**
    //  * Soap_o API
    //  * --------------------
    //  * @param: vitals
    //  * @param: anthropometry
    //  * @param: physical_check
    //  * @param: anythingelse
    //  * --------------------
    //  * @method : POST
    //  * @link: version/mr/soap_o
    //  **/

    // public function update_put($p = 0) {
    //     $id         = $this->security->xss_clean($p);
    //     // CALL JSON POST
    //     $data       = $this->json_put();
    //     // DEFINE ATTR
    //     $dt_put     = $data['dt_postput'];
    //     $device     = $data['dt_server']['HTTP_USER_AGENT'];
    //     $ip         = $this->get_client_ip();
    //     $error      = TRUE;

    //     // Token Validation
    //     $is_valid_token = $this->authorization_token->validateToken();
    //     if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
    //         # Form Validation
    //         $this->form_validation->set_data($dt_put);
    //         $this->form_validation->set_rules('diagnosis', 'diagnosis', 'trim');
    //         $this->form_validation->set_rules('development_notes', 'development_notes', 'trim');
    //         $this->form_validation->set_rules('anythingelse', 'anythingelse', 'trim');
    //         if ($this->form_validation->run() == FALSE){
    //             // Form Validation Errors
    //             $response_data = $this->form_validation->error_array();
    //             $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //         } else {
    //             $model = $this->name;
    //             /* Start Transaction */
    //             $this->db->trans_start();
    //             $soap_o = $this->$model->read($id); 
    //             if($soap_o != []) {
    //                 $dt_soap_o = array(
    //                     'id'                    => $id,
    //                     'vitals'                => isset($dt_put['vitals']) ? $dt_put['vitals'] : $soap_o[0]->vitals,
    //                     'anthropometry'         => isset($dt_put['anthropometry']) ? $dt_put['anthropometry'] : $soap_o[0]->anthropometry,
    //                     'physical_check'        => isset($dt_put['physical_check']) ? $dt_put['physical_check'] : $soap_o[0]->physical_check,
    //                     'anythingelse'          => isset($dt_put['anythingelse']) ? $dt_put['anythingelse'] : $soap_o[0]->anythingelse
    //                 );
    //                 $id = $this->$model->update($id, $dt_soap_o);
    //             } else {
    //                 $this->REST_Return(404, 'Soap_o with id ' . $id . ' does not exist!');
    //                 return;
    //             }
    //             $this->db->trans_complete();
    //             /* End Transaction */
    //             if ($this->db->trans_status() === false) {
    //                 $response_data = array();
    //                 $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
    //             } else {
    //                 $response_data = $id;
    //                 $this->REST_Return(200, 'SUCCESS', $response_data);
    //             }
    //             return;     
    //         }
    //     } else {
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
    //         return;
    //     }
    // }
}