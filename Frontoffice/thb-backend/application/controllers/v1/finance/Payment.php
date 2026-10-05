<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Payment extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/{$this->name}_method_model", "{$this->name}_method");
        $this->load->model("$this->directory/Cashier_model", "Cashier");
        $this->load->model("v1/tr/Accurate_bak_model", "Accurate_bak");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/mr/Insurance_model", "Insurance");
        $this->load->model("v1/tr/Transaction_invoice_model", "Transaction_invoice");

        $this->update_rules = array(
            array('field' => 'flag_status_id', 'label' => 'flag_status_id', 'rules' => 'trim|max_length[1]'),
        );
    }

    /**
     * Payment API
     * --------------------
     * @param: invoice_id
     * @param: cashier_id
     * @param: flag_status_id
     * @param: payment_items
     * --------------------
     * @method : POST
     * @link: version/finance/Payment
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
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_rules('invoice_id', 'invoice_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('cashier_id', 'cashier_id', 'trim|max_length[36]');
            $this->form_validation->set_rules('flag_status_id', 'flag_status_id', 'trim|max_length[1]');
            // Data Payment_item
            $this->form_validation->set_rules('payment_items[]', 'payment_items', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // Check invoice_id and cashier_id
                if ($dt_post['registration_id']){
                    $data_patient = $this->Registration->read($dt_post['registration_id'], false)[0];
                }
                if($this->Transaction_invoice->read($dt_post['invoice_id'], false) != []) {
                    $t = time();
                    $dt_payment = array(
                        'invoice_id'       => $dt_post['invoice_id'],
                        'cashier_id'       => $user_id,
                        'flag_status_id'   => isset($dt_post['flag_status_id']) ? $dt_post['flag_status_id'] : '',
                        'date_added'            => date("Y-m-d H:i:s",$t)
                    );
                    $id = $this->$model->create($dt_payment);
                    if ($id) {
                        $dt_invoice = array(
                            'id'               => $dt_post['invoice_id'],
                            'payment_id'       => $id,
                        );
                        $this->Transaction_invoice->replace($dt_invoice);
                    }
                    // $id = 1;
    
                    // INIT_PAYMENT_ITEM
                    for ($i=0; $i < count($dt_post['payment_items']); $i++) { 
                        if ($this->Payment_method->read($dt_post['payment_items'][$i]['payment_method_id'], false) != []) {
                            if ($dt_post['payment_items'][$i]['payment_method_id'] == '' || $dt_post['payment_items'][$i]['payment_method_id'] == null || $dt_post['payment_items'][$i]['payment_method_type_id'] == '' || $dt_post['payment_items'][$i]['payment_method_type_id'] == null) {
                                $this->REST_Return(422, 'Cara Bayar Harus diisi!' ,array());
                                return;
                            }
                            $dt_payment_item = array(
                                'payment_id'             => $id,
                                'payment_method_id'      => $dt_post['payment_items'][$i]['payment_method_id'],
                                'payment_method_type_id' => $dt_post['payment_items'][$i]['payment_method_type_id'],
                                'insurance_id'           => $dt_post['payment_items'][$i]['insurance_id'],
                                'var_1'                  => isset($dt_post['payment_items'][$i]['var_1']) ? $dt_post['payment_items'][$i]['var_1'] : '',
                                'var_2'                  => isset($dt_post['payment_items'][$i]['var_2']) ? $dt_post['payment_items'][$i]['var_2'] : '',
                                'var_3'                  => isset($dt_post['payment_items'][$i]['var_3']) ? $dt_post['payment_items'][$i]['var_3'] : ''
                            );
                            $this->Payment_item->replace($dt_payment_item);
                            $dt_pm = $this->Payment_method->read($dt_post['payment_items'][$i]['payment_method_id'], false)[0];
                            $detailItem[0] = array(
                                "invoiceNo"=> $this->Transaction_invoice->read($dt_post['invoice_id'], false)[0]->code,
                                "paymentAmount"=> $dt_post['amount_payment']
                            );
                            if ($dt_post['registration_id']) {
                                $json_body_payment = array(
                                    "bankNo"=> $dt_pm->coa,
                                    "chequeAmount" => isset($dt_post['payment_items'][$i]['var_3']) ? $dt_post['payment_items'][$i]['var_3'] : '',
                                    "detailInvoice" => $detailItem,
                                    "customerNo" => $data_patient->norm,
                                    "transDate" => date('d/m/Y')
                                );
                                // echo "<pre>";
                                // var_dump($json_body_payment);die();
                                $return_bride_payment = $this->bride_accurate('api/sales-receipt/save.do', json_encode($json_body_payment));
                                if ($return_bride_payment['s'] == true) {
                                    // var_dump($return_bride_payment);
                                    // $this->REST_Return(404, 'Failed Accurate = ');
                                    // return;
                                } else {
                                    $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$dt_post['invoice_id'],'payment_id'=>$id,'response'=>json_encode($return_bride_inv),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
                                    // var_dump($return_bride_payment);
                                    // $this->REST_Return(404, 'Failed Accurate = ');
                                    // return;
                                }
                            }

                        } else {
                            $this->REST_Return(404, 'There is no payment_method with id ' . $dt_post['payment_items'][$i]['payment_method_id']);
                            return;
                        }
                    }
                } else {
                    $this->REST_Return(404, 'Wrong invoice_id');
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
        $this->load->model("v1/setting/Accurate_bride_model", "Accurate_bride");
        $dataBride = $this->Accurate_bride->read()[0];
        $url_acc   = 'https://zeus.accurate.id/accurate/';
        $sessionid = $dataBride->sessionid;
        $token = $dataBride->token;
        $ch = curl_init($url_acc.$url);
        $authorization   = array();
        $authorization[] = "Authorization:Bearer ".$token; 
        $authorization[] = "X-Session-ID:".$sessionid; 
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

    public function delete_put($p = 0) {
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
            $this->form_validation->set_rules('reason', 'reason', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                // $this->$model->whereFields['c.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['h.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['i.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['j.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['l.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['m.institute_branch_id'] = $institute_branch_id;
                // $this->$model->whereFields['n.institute_branch_id'] = $institute_branch_id;
                if($this->Transaction_invoice->read($id,false) != []) {
                    $dtinv = $this->Transaction_invoice->read($id,false)[0];
                    $dt_inv = array(
                        'id'                   => $id,
                        'payment_id'         => null
                    );
                    $this->Transaction_invoice->replace($dt_inv);
                    $dt_payment = array(
                        'id'                   => $dtinv->payment_id,
                        'invoice_id'           => $id,
                        'user_deleted'         => $user_id,
                        'date_deleted'         => date('Y-m-d H:i:s'),
                        'is_delete'            => 1,
                        'reason'               => $dt_put['reason']
                    );
                    $this->$model->replace($dt_payment);
                } else {
                    $this->REST_Return(404, 'Registration with id ' . $id . ' does not exist!');
                    return;
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = array();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
                } else {
                    $response_data = $id;
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
}