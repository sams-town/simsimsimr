<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Nurse_neonate_inpatient extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Nurse_model", "Nurse");
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
    
        $nurse_neonate_inpatient_id = $id;
        
        $nurse_id = $q_strings['nurse_id'] ?? null;

        $where = $q_strings;
        
        $select = 'a.*';
        $where['a.is_delete'] = 0;
        if ($nurse_neonate_inpatient_id !== null && $nurse_neonate_inpatient_id !== "0" && $nurse_neonate_inpatient_id !== 0) {
            $output = $this->$model->select($nurse_neonate_inpatient_id, $select, $where);

            if (count($output) == 0) {
                $this->REST_Return(404, 'there is no nurse_neonate_inpatient with id ' . $nurse_neonate_inpatient_id);
                return;
            }
        } else {
            if ($nurse_id == null) {
                $this->REST_Return(400, 'nurse_id is required if no id provided');
                return;
            }
            $output = $this->$model->select(0, $select, $where);
        }

        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->mother_lab_result_arry = ($output[$i]->mother_lab_result_arry != "") ? explode(',', $output[$i]->mother_lab_result_arry) : [];
            $output[$i]->postnatal_drug_arry = ($output[$i]->postnatal_drug_arry != "") ? explode(',', $output[$i]->postnatal_drug_arry) : [];
            $output[$i]->comm_needs_colloquial_arry = ($output[$i]->comm_needs_colloquial_arry != "") ? explode(',', $output[$i]->comm_needs_colloquial_arry) : [];
            $output[$i]->edu_needs_information_arry = ($output[$i]->edu_needs_information_arry != "") ? explode(',', $output[$i]->edu_needs_information_arry) : [];
            $output[$i]->evaluation_comfort_arry = ($output[$i]->evaluation_comfort_arry != "") ? explode(',', $output[$i]->evaluation_comfort_arry) : [];
            $output[$i]->evaluation_client_movement_arry = ($output[$i]->evaluation_client_movement_arry != "") ? explode(',', $output[$i]->evaluation_client_movement_arry) : [];


            $output[$i]->medical_devices = json_decode($output[$i]->medical_devices)->data;
            $output[$i]->examination_history = json_decode($output[$i]->examination_history)->data;
        }
    
        $response_data = $output;
    
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}