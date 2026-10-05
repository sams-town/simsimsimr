<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Soap_p extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Soap_model", "Soap");
        $this->load->model("v1/mr/Soap_p_action_model", "Soap_p_action");
        $this->load->model("v1/mr/Icd9cm_model", "Icd9cm");
        $this->load->model("v1/hr/Employee_model", "Employee");
        $this->load->model("v1/pharmacy/Prescription_model", "Prescription");
        $this->load->model("v1/pharmacy/Catalog_model", "Catalog");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");
    }

    /**
     * Soap_p API
     * --------------------
     * @param: soap_id
     * @param: therapy_plan
     * @param: icd9cm_id
     * --------------------
     * @method : POST
     * @link: version/mr/soap_p
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
            // DATA SOAP_P
            $this->form_validation->set_rules('soap_id', 'soap_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('doctor_in_charge_id', 'doctor_in_charge_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('therapy_plan', 'therapy_plan', 'trim');
            // DATA SOAP_P_ACTION
            $this->form_validation->set_rules('icd9cm[]', 'icd9cm', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                // Check soap_id
                if($this->Soap->read($dt_post['soap_id'], false) != []) {
                    $model = $this->name;

                    
                    $doctor_in_charge_id = $dt_post['doctor_in_charge_id'];
                    if ($doctor_in_charge_id != null) {
                        $employee = $this->Employee->select_id($doctor_in_charge_id);
                        
                        if ($employee == "") {
                            $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
                            return;
                        }
                    }

                    /* Start Transaction */
                    $this->db->trans_start();

                    // INIT_SOAP_P
                    $dt_soap_p = array(
                        'soap_id'               => $dt_post['soap_id'],
                        'doctor_in_charge_id'   => $dt_post['doctor_in_charge_id'],
                        'therapy_plan'          => isset($dt_post['therapy_plan']) ? $dt_post['therapy_plan'] : '',
                    );
                    $id = $this->$model->create($dt_soap_p);

                    // INIT_SOAP_P_ACTION
                    for ($i=0; $i < count($dt_post['icd9cm']) ; $i++) { 
                        // Check icd9cm_id
                        if($this->Icd9cm->read($dt_post['icd9cm'][$i]['icd9cm_id'], false) != []) {
                            $dt_soap_p_action = array(
                                'soap_p_id'        => $id,
                                'icd9cm_id'        => $dt_post['icd9cm'][$i]['icd9cm_id']
                            );
                            $this->Soap_p_action->create($dt_soap_p_action);
                        } else {
                            $this->REST_Return(404, 'There is no icd9cm with id ' . $dt_post['icd9cm'][$i]['icd9cm_id']);
                            return;
                        }
                    }

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
     * GET Soap_p with API
     * -------------------------
     * @method: GET
     */

    public function detail_get($p = 0) {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) {
                $this->Soap_p_action->whereFields['a.is_delete'] = 0;
                $this->Soap_p_action->whereFields['soap_p_id'] = $output[$i]->id; 
                $soap_p_action = $this->Soap_p_action->read();
                for ($j=0; $j < count($soap_p_action); $j++) { 
                    $icd9cm = $this->Icd9cm->read($soap_p_action[$j]->icd9cm_id);
                    $output[$i]->icd9cm[$j]['id'] = $soap_p_action[$j]->icd9cm_id;
                    $output[$i]->icd9cm[$j]['name'] = $icd9cm[0]->name;
                    $output[$i]->icd9cm[$j]['code'] = $icd9cm[0]->code;
                    $output[$i]->icd9cm[$j]['code_s'] = $icd9cm[0]->code_s;
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
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $this->Soap_p_action->whereFields['soap_p_id'] = $output->rows[$i]->id; 
                $soap_p_action = $this->Soap_p_action->read();
                for ($j=0; $j < count($soap_p_action); $j++) { 
                    $icd9cm = $this->Icd9cm->read($soap_p_action[$j]->icd9cm_id);
                    $output->rows[$i]->icd9cm[$j]['icd9cm_id'] = $soap_p_action[$j]->icd9cm_id;
                    $output->rows[$i]->icd9cm[$j]['icd9cm_name'] = $icd9cm[0]->name;
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

    public function prescription_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Prescription";

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields['s.registration_destination_id'] = $q_strings['registration_destination_id'];
            $this->$model->whereFields['hf.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['hri.institute_branch_id'] = $institute_branch_id;
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) {
                $form_prescription = $this->Prescription_item->read($output->rows[$i]->id);
                if ($form_prescription != []) {
                    for ($j=0; $j < count($form_prescription); $j++) { 
                        $catalog = $this->Catalog->read($form_prescription[$j]->pharmacy_catalog_id,false)[0];
                        $output->rows[$i]->form_prescription[$j]['pharmacy_prescription_id'] = $form_prescription[$j]->pharmacy_prescription_id;
                        $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_id'] = $form_prescription[$j]->pharmacy_catalog_id;
                        $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_name'] = $catalog->name;
                        $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_code'] = $catalog->code;
                        $output->rows[$i]->form_prescription[$j]['prescription_item_type_id'] = $form_prescription[$j]->prescription_item_type_id;
                        $output->rows[$i]->form_prescription[$j]['package_id'] = $form_prescription[$j]->package_id;
                        $output->rows[$i]->form_prescription[$j]['package_name'] = $form_prescription[$j]->package_name;
                        $output->rows[$i]->form_prescription[$j]['dose'] = $form_prescription[$j]->dose;
                        $output->rows[$i]->form_prescription[$j]['dose_name'] = $catalog->dose_name;
                        $output->rows[$i]->form_prescription[$j]['dose_doctor'] = $form_prescription[$j]->dose_doctor;
                        $output->rows[$i]->form_prescription[$j]['batch'] = $form_prescription[$j]->batch;
                        $output->rows[$i]->form_prescription[$j]['expired'] = $form_prescription[$j]->expired;
                        $output->rows[$i]->form_prescription[$j]['qty_doctor'] = $form_prescription[$j]->qty_doctor;
                        $output->rows[$i]->form_prescription[$j]['qty'] = $form_prescription[$j]->qty;
                        $output->rows[$i]->form_prescription[$j]['qty_copy'] = $form_prescription[$j]->qty_copy;
                        $output->rows[$i]->form_prescription[$j]['used'] = $form_prescription[$j]->used;
                        $output->rows[$i]->form_prescription[$j]['desc'] = $form_prescription[$j]->desc;
                        $output->rows[$i]->form_prescription[$j]['var_1'] = $form_prescription[$j]->var_1;
                        $output->rows[$i]->form_prescription[$j]['var_2'] = $form_prescription[$j]->var_2;
                        $output->rows[$i]->form_prescription[$j]['var_3'] = $form_prescription[$j]->var_3;
                        $output->rows[$i]->form_prescription[$j]['doctor_in_charge_id'] = $form_prescription[$j]->doctor_in_charge_id;
                        $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_id_ref'] = $form_prescription[$j]->pharmacy_catalog_id_ref;
                        $output->rows[$i]->form_prescription[$j]['pharmacy_catalog_id_sub'] = $form_prescription[$j]->pharmacy_catalog_id_sub;
                        $output->rows[$i]->form_prescription[$j]['sort'] = $form_prescription[$j]->sort;

                    }
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


    /**
     * Soap_p API
     * --------------------
     * @param: therapy_plan
     * --------------------
     * @method : POST
     * @link: version/mr/soap_p
     **/

    public function update_put($p = 0) {
        $id         = $this->security->xss_clean($p);
        // CALL JSON POST
        $data       = $this->json_put();
        // DEFINE ATTR
        $dt_put     = $data['dt_postput'];
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('therapy_plan', 'therapy_plan', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;

                $user_id = $this->security->xss_clean($is_valid_token['data']->id);

                $soap_p_tmp = $this->Soap_p->select_id($id);

                if ($soap_p_tmp == '') {
                    $this->REST_Return(404, 'there is no soap_p with id ' . $id);
                    return;
                }

                $doctor_in_charge_id = $dt_put['doctor_in_charge_id'];
                if ($doctor_in_charge_id != null) {
                    $employee = $this->Employee->select_id($doctor_in_charge_id);
                    
                    if ($employee == "") {
                        $this->REST_Return(404, 'there is no employee with id ' . $doctor_in_charge_id);
                        return;
                    }
                }

                // SOAP_P
                $soap_p = $this->Soap_p->read($id, false)[0]; 
                $dt_soap_p = array(
                    'id'                    => $id,
                    'soap_id'               => $soap_p->soap_id,
                    'doctor_in_charge_id'   => $doctor_in_charge_id,
                    'therapy_plan'          => isset($dt_put['therapy_plan']) ? $dt_put['therapy_plan'] : $soap_p->therapy_plan,
                );
                $this->Soap_p->replace($dt_soap_p);

                // SOAP_P_ACTION
                if(isset($dt_put['icd9cm'])) {
                    $this->Soap_p_action->whereFields['soap_p_id'] = $id;
                    $soap_p_action = $this->Soap_p_action->read(0,false);
                    $icd9cm_arr = $dt_put['icd9cm'];
                    $tmp_delete_icd9cm_arr = array();
                    // adding all current items (db) to temporary variable
                    for ($i=0; $i < count($soap_p_action); $i++) { 
                        $tmp_delete_icd9cm_arr[$soap_p_action[$i]->icd9cm_id] = $soap_p_action[$i]->id;
                    }
                    for ($i=0; $i < count($icd9cm_arr) ; $i++) { 
                        $icd9cm_id = $icd9cm_arr[$i]['icd9cm_id'];
                        
                        $icd9cm = $this->Icd9cm->select_id($icd9cm_id);

                        if($icd9cm == '') {
                            $this->REST_Return(404, 'There is no icd9cm with id ' . $icd9cm_id);
                            return;
                        }

                        $dt_soap_p_action = array(
                            'soap_p_id'         => $soap_p->id,
                            'icd9cm_id'         => $icd9cm_id,
                            'is_delete'         => 0,
                            'user_added'        => $user_id,
                            'user_updated'      => $user_id,
                        );

                        // create or update items according to the items provided and unset used items from temporary variable
                        if(array_key_exists($icd9cm_id,$tmp_delete_icd9cm_arr) == 1) {
                            $dt_soap_p_action['id'] = $tmp_delete_icd9cm_arr[$icd9cm_id];
                            $this->Soap_p_action->replace($dt_soap_p_action);

                            unset($tmp_delete_icd9cm_arr[$icd9cm_id]);
                        } else {
                            $this->Soap_p_action->create($dt_soap_p_action);
                        }

                    }

                    // delete all items in temporary variable
                    foreach ($tmp_delete_icd9cm_arr as $icd9cm_id => $soap_p_action_id) {
                        $dt_soap_p_action = array(
                            'id'                => $soap_p_action_id,
                            'soap_p_id'         => $soap_p->id,
                            'icd9cm_id'         => $icd9cm_id,
                            'is_delete'         => 1,
                            'user_updated'      => $user_id,
                            'user_deleted'      => $user_id,
                        );
                        $this->Soap_p_action->replace($dt_soap_p_action);
                    }
                }

                // /* Start Transaction */
                // $this->db->trans_start();
                // $soap_p = $this->$model->read($id); 
                // if($soap_p != []) {
                //     $dt_soap_p = array(
                //         'id'                    => $id,
                //         'therapy_plan'          => isset($dt_put['therapy_plan']) ? $dt_put['therapy_plan'] : $soap_p[0]->therapy_plan
                //     );
                //     $id = $this->$model->update($id, $dt_soap_p);
                // } else {
                //     $this->REST_Return(404, 'Soap_p with id ' . $id . ' does not exist!');
                //     return;
                // }
                // $this->db->trans_complete();
                // /* End Transaction */
                // if ($this->db->trans_status() === false) {
                //     $response_data = array();
                //     $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                // } else {
                //     $response_data = $id;
                //     $this->REST_Return(200, 'SUCCESS', $response_data);
                // }

                $response_data = $id;
                $this->REST_Return(200, 'SUCCESS', $response_data);
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }
}