<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Downpayment extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Payment_method_model", "Payment_method");
        $this->load->model("$this->directory/Cashier_model", "Cashier");
        $this->load->model("v1/mr/Registration_model", "Registration");

        $this->update_rules = array(
            array('field' => 'notes', 'label' => 'notes', 'rules' => 'trim|max_length[100]'),
            array('field' => 'var_1', 'label' => 'var_1', 'rules' => 'trim|max_length[8]'),
            array('field' => 'flag_status_id', 'label' => 'flag_status_id', 'rules' => 'trim|max_length[1]'),
        );
    }

    /**
     * Downpayment API
     * --------------------
     * @param: registration_id
     * @param: cashier_id
     * @param: notes
     * @param: var_1
     * @param: flag_status_id
     * --------------------
     * @method : POST
     * @link: version/finance/Downpayment
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
            $this->form_validation->set_rules('registration_id', 'registration_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('cashier_id', 'cashier_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('notes', 'notes', 'trim|max_length[100]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[8]');
            $this->form_validation->set_rules('flag_status_id', 'flag_status_id', 'trim|max_length[1]');
            // Data Downpayment_items
            $this->form_validation->set_rules('downpayment_items[]', 'downpayment_items', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check registration_id and cashier_id
                //CHECK CUSTOMER EXIST OR NOT
                $data_patient = $this->Registration->read($dt_post['registration_id'], false)[0];
                // $return_bride = $this->bride_accurate('api/customer/detail.do?id='.$data_patient->bride_cust_id);
                // if ($return_bride['s'] == false){
                //     //CREATE CUSTOMER
                //     // $json_body = 
                //     // '{
                //     //     "data": [
                //     //         {
                //     //         "name": '.$data_patient->fullname.',
                //     //         "number": '.$data_patient->norm.',
                //     //         "billCity": "",
                //     //         "billCountry": "",
                //     //         "billProvince": "",
                //     //         "billStreet": "",
                //     //         "billZipCode": "",
                //     //         "category": "",
                //     //         "mobilePhone": '.$data_patient->phone.',
                //     //         "notes": "",
                //     //         "phone": '.$data_patient->phone.'
                //     //         }
                //     //     ]
                //     // }';
                //     $json_body = array('data'=>[
                //         array(
                //             'name' => $data_patient->fullname,
                //             'number' => $data_patient->norm,
                //             'billCity' => "",
                //             'billCountry' => "",
                //             'billProvince' => "",
                //             'billStreet' => "",
                //             'billZipCode' => "",
                //             'category' => "",
                //             'mobilePhone' => $data_patient->phone,
                //             'notes' => "",
                //             'phone' => $data_patient->phone
                //         )
                //     ]);
                //     $return_bride = $this->bride_accurate('api/pos/customer/save.do', json_encode($json_body));
                //     if ($return_bride['s'] == true) {
                //         $data_bride_pasien_update = array(
                //             'norm' => $data_patient->norm,
                //             'name' => $data_patient->name,
                //             'birth_date' => $data_patient->birth_date,
                //             'birth_place' => $data_patient->birth_place,
                //             'bride_cust' => $return_bride['d'][0]['r']['customerNo'],
                //             'bride_cust_id' => $return_bride['d'][0]['r']['id'],
                //         );
                //         $this->Patient->replace($data_bride_pasien_update);
                //     } else {
                //         var_dump($return_bride);
                //         $this->REST_Return(404, 'Failed Accurate = ');
                //         return;
                //     }
                // }
                $data_patient = $this->Registration->read($dt_post['registration_id'], false)[0];
                if($this->Registration->read($dt_post['registration_id'], false) != [] && $this->Cashier->read($dt_post['cashier_id'], false) != [] && $dt_post['downpayment_items'] != []) {
                    // echo "<pre>";
                    // var_dump($dt_post);die();
                    $t = time();
                    $dt_downpayment = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'registration_id'       => $dt_post['registration_id'],
                        'cashier_id'            => $dt_post['cashier_id'],
                        'notes'                 => isset($dt_post['notes']) ? $dt_post['notes'] : '',
                        'var_1'                 => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                        'flag_status_id'        => isset($dt_post['flag_status_id']) ? $dt_post['flag_status_id'] : '',
                        'date_added'            => date("Y-m-d H:i:s",$t)
                    );
                    $id = $this->$model->create($dt_downpayment);
    
                    // INIT_DOWNPAYMENT_ITEM
                    for ($i=0; $i < count($dt_post['downpayment_items']); $i++) { 
                        // Check payment_method_id
                        if ($this->Payment_method->read($dt_post['downpayment_items'][$i]['payment_method_id'], false) != []) {
                            $dt_downpayment_item = array(
                                'downpayment_id'         => $id,
                                'payment_method_id'      => $dt_post['downpayment_items'][$i]['payment_method_id'],
                                'var_1'                  => isset($dt_post['downpayment_items'][$i]['var_1']) ? $dt_post['downpayment_items'][$i]['var_1'] : '',
                                'var_2'                  => isset($dt_post['downpayment_items'][$i]['var_2']) ? $dt_post['downpayment_items'][$i]['var_2'] : '',
                                'var_3'                  => isset($dt_post['downpayment_items'][$i]['var_3']) ? $dt_post['downpayment_items'][$i]['var_3'] : ''
                            );
                            $this->Downpayment_item->replace($dt_downpayment_item);
                            // $json_body_dp = array(
                            //     "customerNo" => $data_patient->bride_cust,
                            //     "dpAmount" => isset($dt_post['downpayment_items'][$i]['var_1']) ? $dt_post['downpayment_items'][$i]['var_1'] : '',
                            //     "transDate" => date('d/m/Y')
                            // );
                            // // echo "<pre>";
                            // // var_dump($json_body_dp);die();
                            // $return_bride_payment = $this->bride_accurate('api/sales-invoice/create-down-payment.do', json_encode($json_body_dp));
                            // if ($return_bride_payment['s'] == true) {
                            //     // var_dump($return_bride_payment);
                            //     // $this->REST_Return(404, 'Failed Accurate = ');
                            //     // return;
                            // } else {
                            //     var_dump($return_bride_payment);
                            //     $this->REST_Return(404, 'Failed Accurate = ');
                            //     return;
                            // }
                        } else {
                            $this->REST_Return(404, 'There is no payment_method with id ' . $dt_post['downpayment_items'][$i]['payment_method_id']);
                            return;
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Wrong registration_id or cashier_id!');
                    return;
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
                    $this->REST_Return(200, 'SUCCESS', $response_data);
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function bride_accurate($url = null,$param = null) {
        $url_acc   = 'https://zeus.accurate.id/accurate/';
        $SESSIONID = '61e16c4a-0ca3-4b42-ad79-e60ec8ae5d8b';
        $token = 'e4c2912b-8585-4fe6-90b1-ba170657c628';
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        $authorization[] = "Authorization:Bearer ".$token; 
        $authorization[] = "X-Session-ID:".$SESSIONID; 
        if($param !== null){
            $authorization[] = "Content-Type: application/json"; 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $authorization ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true); 
    }

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
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $downpayment_item = $this->Downpayment_item->read($output[$i]->id);
                for ($j=0; $j < count($downpayment_item); $j++) { 
                    foreach ($downpayment_item[$j] as $key => $val) {
                        $output[$i]->downpayment_items[$j][$key] = $val;
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
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $output = $this->$model->read_ssr($id);
            $response_data = array();
            for ($i=0; $i < count($output->rows); $i++) { 
                $downpayment_item = $this->Downpayment_item->read($output->rows[$i]->id);
                for ($j=0; $j < count($downpayment_item); $j++) { 
                    foreach ($downpayment_item[$j] as $key => $val) {
                        $output->rows[$i]->downpayment_items[$j][$key] = $val;
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
}