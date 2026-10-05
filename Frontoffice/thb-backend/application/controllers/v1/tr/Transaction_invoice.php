<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;class Transaction_invoice extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("v1/mr/Patient_model", "Patient");
        $this->load->model("v1/mr/Registration_model", "Registration");
        $this->load->model("v1/finance/Cashier_model", "Cashier");
        $this->load->model("v1/finance/Payment_model", "Payment");
        $this->load->model("v1/finance/Costing_value_model", "Costing_value");
        $this->load->model("v1/finance/Costing_services_class_model", "Costing_services_class");
        $this->load->model("v1/finance/Costing_services_class_inv_model", "Costing_services_class_inv");
        $this->load->model("v1/finance/Costing_model", "Costing");
        $this->load->model("v1/finance/Payment_item_model", "Payment_item");
        $this->load->model("v1/tr/Transaction_invoice_item_model", "Transaction_invoice_item");
        $this->load->model("v1/tr/Transaction_administration_model", "Transaction_administration");
        $this->load->model("v1/tr/Transaction_alkes_model", "Transaction_alkes");
        $this->load->model("v1/tr/Transaction_bed_model", "Transaction_bed");
        $this->load->model("v1/tr/Transaction_bhp_model", "Transaction_bhp");
        $this->load->model("v1/tr/Transaction_blood_model", "Transaction_blood");
        $this->load->model("v1/tr/Transaction_doctor_model", "Transaction_doctor");
        $this->load->model("v1/tr/Transaction_mcu_model", "Transaction_mcu");
        $this->load->model("v1/tr/Transaction_nurse_model", "Transaction_nurse");
        $this->load->model("v1/tr/Accurate_bak_model", "Accurate_bak");
        $this->load->model("v1/hr/Employee_position_model", "Employee_position");
        $this->load->model("v1/setting/Notifications_model", "Notifications");
        $this->load->model("v1/tr/Transaction_physiotherapy_model", "Transaction_physiotherapy");
        $this->load->model("v1/pharmacy/Prescription_model", "Transaction_prescription");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Transaction_prescription_item");
        $this->load->model("v1/health_facility/Laboratory_request_item_model", "Laboratory_request_item");
        $this->load->model("v1/health_facility/Radiology_request_item_model", "Radiology_request_item");
        $this->load->model("v1/health_facility/Physiotherapy_request_item_model", "Physiotherapy_request_item");
        $this->load->model("v1/health_facility/Laboratory_request_read_model", "Laboratory_request_read");
        $this->load->model("v1/health_facility/Radiology_request_read_model", "Radiology_request_read");
        $this->load->model("v1/health_facility/Physiotherapy_request_read_model", "Physiotherapy_request_read");
    }

    /**
     * Transaction_invoice API
     * --------------------
     * @param: registration_id
     * @param: cashier_id
     * @param: var_1
     * @param: var_2
     * @param: var_3
     * @param: date_added
     * @param: transaction_id
     * --------------------
     * @method : POST
     * @link: version/tr/transaction_invoice
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
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[36]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[36]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[36]');
            $this->form_validation->set_rules('date_added', 'date_added', 'trim');
            // Data Transaction_invoice_item
            $this->form_validation->set_rules('transactions[]', 'transactions', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check registration_id and chasier_id
                $registration_id = $dt_post['registration_id'];
                if($this->Registration->read($dt_post['registration_id'], false) != [] && $this->Cashier->read($dt_post['cashier_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $t = time();
                    $data_patient = $this->Registration->read($dt_post['registration_id'], false)[0];
                    if ($data_patient->health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7') {
                        $kdinv = 'IGD';
                    } else if ($data_patient->health_facility_id == '40f06534-07ec-4221-ad9d-dea08196079f') {
                        $kdinv = 'RANAP';
                    } else {
                        $kdinv = 'RALAN';
                    }
                    $prescription_check = false;
                    $prescription_id = '';
                    $codecounter = array(
                        'var_1' => $kdinv,
                        'var_2' => date('Y'),
                        'var_3' => date('m'),
                        'var_5' => date('Y-m')
                    );
                    $this->load->library("transaction");
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = date('d').'/'.$codecounter['var_3'].'/'.$codecounter['var_1'].'/'.str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT).'/'.$codecounter['var_2'];
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => $kdinv,
                            'var_2' => date('Y'),
                            'var_3' => date('m'),
                            'var_5' => date('Y-m'),
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = date('d').'/'.$codecounter['var_3'].'/'.$codecounter['var_1'].'/'.str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT).'/'.$codecounter['var_2'];
                    }
                    $dt_transaction_invoice = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'registration_id'       => $dt_post['registration_id'],
                        'code'                  => $code,
                        'cashier_id'            => $user_id,
                        'is_delete'             => 0,
                        'var_1'                 => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                        'var_2'                 => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                        'var_3'                 => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                        'date_added'            => date("Y-m-d H:i:s",$t)
                    );

                    //CHECK CUSTOMER EXIST OR NOT
                    $return_bride = $this->bride_accurate('api/customer/detail.do?id='.$data_patient->bride_cust_id);
                    if ($return_bride['s'] == false){
                        // CREATE CUSTOMER
                        // $json_body = 
                        // '{
                        //     "data": [
                        //         {
                        //         "name": '.$data_patient->fullname.',
                        //         "number": '.$data_patient->norm.',
                        //         "billCity": "",
                        //         "billCountry": "",
                        //         "billProvince": "",
                        //         "billStreet": "",
                        //         "billZipCode": "",
                        //         "category": "",
                        //         "mobilePhone": '.$data_patient->phone.',
                        //         "notes": "",
                        //         "phone": '.$data_patient->phone.'
                        //         }
                        //     ]
                        // }';
                        $json_body = array('data'=>[
                            array(
                                'name' => $data_patient->fullname,
                                'number' => $data_patient->norm,
                                'billCity' => "",
                                'billCountry' => "",
                                'billProvince' => "",
                                'billStreet' => "",
                                'billZipCode' => "",
                                'category' => "",
                                'mobilePhone' => $data_patient->phone,
                                'notes' => "",
                                'phone' => $data_patient->phone
                            )
                        ]);
                        $return_bride = $this->bride_accurate('api/pos/customer/save.do', json_encode($json_body));
                        if ($return_bride['s'] == true) {
                            $data_bride_pasien_update = array(
                                'norm' => $data_patient->norm,
                                'name' => $data_patient->name,
                                'birth_date' => $data_patient->birth_date,
                                'birth_place' => $data_patient->birth_place,
                                'bride_cust' => $return_bride['d'][0]['r']['customerNo'],
                                'bride_cust_id' => $return_bride['d'][0]['r']['id'],
                            );
                            $this->Patient->replace($data_bride_pasien_update);
                        } else {
                            // var_dump($return_bride);
                            // $this->REST_Return(404, 'Failed Accurate = ');
                            // return;
                        }
                    }
                    $data_patient = $this->Registration->read($dt_post['registration_id'], false)[0];
                    // echo "<pre>";
                    // var_dump($dt_transaction_invoice);
                    // var_dump($dt_post);
                    // echo "</pre>";
                    // die();
                    $this->db->trans_start();
                    $id = $this->$model->create($dt_transaction_invoice);
                    // $id = 1;
                    // INIT_TRANSACTION_INVOICE_ITEM
                    $detailItem = array();
                    $detailPrescItem=array();
                    for ($i=0; $i < count($dt_post['transactions']); $i++) { 
                        // Check all kind of transaction
                        if ($dt_post['transactions'][$i]['transaction_id'] != "") {
                            // Tambahkan filter khusus untuk Transaction_doctor
                            $this->Transaction_doctor->whereFields['d.id'] = $registration_id;
                            $this->Transaction_nurse->whereFields['d.id'] = $registration_id;
                            $this->Radiology_request_read->whereFields['d.id'] = $registration_id;
                            $this->Laboratory_request_read->whereFields['d.id'] = $registration_id;

                            if ($this->Transaction_administration->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_alkes->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_bed->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_bhp->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_blood->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_doctor->read($dt_post['transactions'][$i]['transaction_id'], false) != [] || 
                                $this->Transaction_mcu->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_nurse->read($dt_post['transactions'][$i]['transaction_id'], false) != [] || 
                                $this->Physiotherapy_request_read->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Radiology_request_read->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Laboratory_request_read->read($dt_post['transactions'][$i]['transaction_id'], false) != [] ||
                                $this->Transaction_prescription->read($dt_post['transactions'][$i]['transaction_id'], false) != []) {
                                $dt_transaction_invoice_item = array(
                                    'transaction_invoice_id'    => $id,
                                    'transaction_id'            => $dt_post['transactions'][$i]['transaction_id'],
                                    'flag_status_id'            => isset($dt_post['transactions'][$i]['flag_status_id']) ? $dt_post['transactions'][$i]['flag_status_id'] : '',
                                    'type_transaction_id'       => isset($dt_post['transactions'][$i]['type_transaction_id']) ? $dt_post['transactions'][$i]['type_transaction_id'] : '',
                                );
                                $this->Transaction_invoice_item->replace($dt_transaction_invoice_item);

                                $this->Transaction_prescription->whereFields['a.type_prescription_id'] = 2; 
                                $data_resep = $this->Transaction_prescription->read($dt_post['transactions'][$i]['transaction_id'], false);
                                if ($data_resep != []) {
                                    $prescription_check = true;
                                    $prescription_id = $data_resep[0]->id;
                                    $patient_name = $data_resep[0]->patient_name;
                                    $norm = $data_resep[0]->norm;
                                    $health_facility_item_name = $data_resep[0]->health_facility_item_name;
                                }
                                $this->Transaction_prescription->whereFields = array();
                            } else {
                                $this->REST_Return(404, 'There is no transaction with id ' . $dt_post['transactions'][$i]['transaction_id']);
                                return;
                            }
                            // 13 Administration
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 13) {
                                $dt_administration = $this->Transaction_administration->read($dt_post['transactions'][$i]['transaction_id'],false);
                                if ($dt_administration[0]->costing_id) {
                                    $this->Costing_services_class->whereFields = array();
                                    $this->Costing_services_class->whereFields['a.costing_id'] = $dt_administration[0]->costing_id; 
                                    $dt_cost = $this->Costing_services_class->read(0,false);
                                    $detailItem[$i] = array(
                                        'itemNo' => $dt_cost[0]->bride_code,
                                        'unitPrice' => $dt_administration[0]->var_1,
                                        'departmentName' => 'ADMINISTRASI',
                                        // 'dataClassification1Name' => 'Poliklinik'
                                    );
    
                                    if ($dt_administration[0]->costing_id == '413a9aa3-dfdc-4e97-b01e-efd44773d229') {

                                        $service_charges = (($dt_post['var_3']-$dt_administration[0]->var_1)*3)/100;
                                        
                                        if ($service_charges > 5000000) {
                                            $service_charges = 5000000;
                                        }

                                        $this->Transaction_administration->replace(array(
                                            'id' => $dt_administration[0]->id,
                                            'var_1' => $service_charges,
                                        ));

                                        $this->$model->replace(array(
                                            'id'         => $id,
                                            'var_3'      => isset($dt_post['var_3']) ? $dt_post['var_3']+$service_charges : '',
                                        ));
                                    }
    
    
                                }
                            }
    
                            // 1 Nurse
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 1) {
                                $dt_nurse = $this->Transaction_nurse->read($dt_post['transactions'][$i]['transaction_id'],false);
                                if ($dt_nurse[0]->costing_id) {
                                    $this->Costing_services_class->whereFields = array();
                                    $this->Costing_services_class->whereFields['a.costing_id'] = $dt_nurse[0]->costing_id; 
                                    $dt_cost = $this->Costing_services_class->read(0,false);
                                    $detailItem[$i] = array(
                                        'itemNo' => $dt_cost[0]->bride_code,
                                        'unitPrice' => $dt_nurse[0]->var_2,
                                        // 'departmentName' => $dt_cost[0]->departmentName,
                                        // 'dataClassification1Name' => 'Poliklinik'
                                    );
                                }
                            }
            
                            // 2 Doctor
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 2) {
                                $dt_doctor = $this->Transaction_doctor->read($dt_post['transactions'][$i]['transaction_id'],false);
                                if ($dt_doctor[0]->costing_id) {
                                    // $this->Costing_services_class->whereFields = array();
                                    $dt_cost = $this->Costing_services_class->read($dt_doctor[0]->costing_id,false);
                                    $detailItem[$i] = array(
                                        'itemNo' => $dt_cost[0]->bride_code,
                                        'unitPrice' => $dt_doctor[0]->var_1,
                                        'quantity' => $dt_doctor[0]->var_2,
                                        'departmentName' => $data_patient->health_facility_item_name,
                                        // 'dataClassification1Name' => 'Poliklinik'
                                    );
                                }
                                $detailItemTotrDocter = array(
                                    'id' => $dt_post['transactions'][$i]['transaction_id'],
                                    'var_1' => $dt_post['transactions'][$i]['var_1'],
                                    'var_2' => $dt_post['transactions'][$i]['var_2'],
                                    'var_3' => $dt_post['transactions'][$i]['var_3'],
                                    'var_4' => $dt_post['transactions'][$i]['var_4'],
                                    'disc_id' => $dt_post['transactions'][$i]['disc_id']
                                );
                                $this->Transaction_doctor->replace($detailItemTotrDocter);
                            }
            
                            // 3 Lab
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 11) {
                                $dt_lab = $this->Laboratory_request_read->read($dt_post['transactions'][$i]['transaction_id'],false);
                                if ($dt_lab[0]->costing_id) {
                                    $this->Costing_services_class->whereFields = array();
                                    $this->Costing_services_class->whereFields['a.costing_id'] = $dt_lab[0]->costing_id; 
                                    $dt_cost = $this->Costing_services_class->read($dt_lab[0]->costing_id,false);
                                    $detailItem[$i] = array(
                                        'itemNo' => $dt_cost[0]->bride_code,
                                        'unitPrice' => $dt_lab[0]->var_1,
                                        'departmentName' => 'LABORATORIUM',
                                        // 'departmentName' => $dt_cost[0]->bride_department_sub,
                                        // 'dataClassification1Name' => 'Poliklinik'
                                    );
                                }
                            }
            
                            // 4 Rad
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 12) {
                                $dt_rad = $this->Radiology_request_read->read($dt_post['transactions'][$i]['transaction_id'],false);
                                if ($dt_lab[0]->costing_id) {
                                    $this->Costing_services_class->whereFields = array();
                                    $this->Costing_services_class->whereFields['a.costing_id'] = $dt_rad[0]->costing_id; 
                                    $dt_cost = $this->Costing_services_class->read(0,false);
                                    $detailItem[$i] = array(
                                        'itemNo' => $dt_cost[0]->bride_code,
                                        'unitPrice' => $dt_rad[0]->var_1,
                                        'departmentName' => 'RADIOLOGI',
                                        // 'departmentName' => $dt_cost[0]->departmentName,
                                        // 'dataClassification1Name' => 'Poliklinik'
                                    );
                                }
                            }
            
                            // 4 Phy
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 12) {
                                $dt_phy = $this->Physiotherapy_request_read->read($dt_post['transactions'][$i]['transaction_id'],false);
                                if ($dt_phy[0]->costing_id) {
                                    $this->Costing_services_class->whereFields = array();
                                    $this->Costing_services_class->whereFields['a.costing_id'] = $dt_phy[0]->costing_id; 
                                    $dt_cost = $this->Costing_services_class->read(0,false);
                                    $detailItem[$i] = array(
                                        'itemNo' => $dt_cost[0]->bride_code,
                                        'unitPrice' => $dt_phy[0]->var_1,
                                        'departmentName' => $dt_cost[0]->bride_departement_sub,
                                        // 'dataClassification1Name' => 'Poliklinik'
                                    );
                                }
                            }
            
                            // 5 Presc
                            if($dt_post['transactions'][$i]['type_transaction_id'] == 14) {
                                $dt_presc = $this->Transaction_prescription->read($dt_post['transactions'][$i]['transaction_id'],false);
                                $dt_presc_item = $this->Transaction_prescription_item->read($dt_post['transactions'][$i]['transaction_id'],false);
                                for ($pi=0; $pi < count($dt_presc_item); $pi++) { 
                                    $detailPrescItem[$pi] = array(
                                        'itemNo' => $dt_presc_item[$pi]->bride_code,
                                        'unitPrice' => $dt_presc_item[$pi]->var_1,
                                        'warehouseName' => $dt_presc[0]->depo_name,
                                        'departmentName' => 'FARMASI',
                                        'quantity' => $dt_presc_item[$pi]->qty-($dt_presc_item[$pi]->is_retur_full === 1 && $dt_presc_item[$pi]->pharmacy_catalog_id_retur || $dt_presc_item[$pi]->pharmacy_catalog_id_ref ? 0 : $dt_presc_item[$pi]->qty_retur)
                                    );
                                }
                            }
                        }

                    }
                    $detailItem = array_merge($detailItem,$detailPrescItem);
                    $json_body_inv = array(
                        'number' => $code,
                        'customerNo' => $data_patient->norm,
                        'detailItem' => $detailItem,
                        'transDate' => date('d/m/Y')
                    );
                    
                    // 10.03.01.05.00 BPJS TK DEBIT
                    // 40.21.00.00.07 BPJS TK KREDIT
                    // 10.03.01.04.00 BPJS JKN DEBIT
                    // 40.21.00.00.02 BPJS JKN KREDIT
                    // $json_body_journal_voucher[0] = array(
                    //     'accountNo' => $code,
                    //     'customerNo' => $data_patient->norm,
                    //     'detailItem' => $detailItem,
                    //     'transDate' => date('d/m/Y')
                    // );
                    // echo "<pre>";
                    // var_dump($json_body_inv);
                    // echo "</pre>";
                    // die();
                    $return_bride_inv = $this->bride_accurate('api/sales-invoice/save.do', json_encode($json_body_inv));
                    if ($return_bride_inv['s'] == true) {
                    } else {
                        $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_inv),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
                        // var_dump($return_bride_inv);
                        // var_dump($json_body_inv);
                        // $this->REST_Return(404, 'Failed Accurate = ');
                        // return;
                    }

                    // NOTIFICATIONS
                    if ($prescription_check) {
                        // BILLING SELESAI NOTIF
                        $title = 'Billing Selesai';
                        $message = "Billing Selesai Obat Pulang Pasien $patient_name ($norm) dari $health_facility_item_name sudah Bisa di serahkan kepada pasien.";
                        $this->Notifications->createNotification(null, 'a641b0bd-e185-4d33-a07f-2506c06bc639', $title, $message, 'prescription_checkout_patient', ['prescription_id' => $prescription_id]);
                    }

                    if ($data_patient->health_facility_id == '3cb626f5-9148-4fb1-8663-3d7f028b01a6' && $data_patient->company_id != '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                        // BILLING SELESAI NOTIF PASIEN RAJAL KE USER FARMASI
                        $title = 'Invoice Selesai';
                        $message = "Invoice Selesai Pasien $data_patient->name ($data_patient->norm) dari $data_patient->health_facility_item_name sudah Bisa melakukan pembayaran atau pelunasan.";
                        $this->Notifications->createNotification(null, 'a641b0bd-e185-4d33-a07f-2506c06bc639', $title, $message, 'form_checkout_patient', ['registration_id' => $dt_post['registration_id']]);
                    }

                    if ($data_patient->health_facility_id == '444b3134-5b75-4b67-af72-a558d46716d7' && $data_patient->health_facility_id != '40f06534-07ec-4221-ad9d-dea08196079f' && $data_patient->company_id != '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8') {
                        // BILLING SELESAI NOTIF PASIEN IGD KE USER ADMISI
                        $title = 'Invoice Selesai';
                        $message = "Invoice Selesai Pasien $patient_data_patient->name ($data_patient->norm) dari $data_patient->health_facility_item_name sudah Bisa melakukan pembayaran atau pelunasan.";
                        $this->Notifications->createNotification(null, '238b6c22-caaf-4bd2-9009-e1553909324f', $title, $message, 'form_checkout_patient', ['registration_id' => $dt_post['registration_id']]);
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
                } else {
                    $this->REST_Return(404, 'Wrong registration_id or cashier_id!');
                }
                return;     
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function create_free_post() {
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
            // $this->form_validation->set_rules('registration_id', 'registration_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('cashier_id', 'cashier_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('var_1', 'var_1', 'trim|max_length[36]');
            $this->form_validation->set_rules('var_2', 'var_2', 'trim|max_length[36]');
            $this->form_validation->set_rules('var_3', 'var_3', 'trim|max_length[36]');
            $this->form_validation->set_rules('date_added', 'date_added', 'trim');
            // Data Transaction_invoice_item
            $this->form_validation->set_rules('transactions[]', 'transactions', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check registration_id and chasier_id
                if($this->Cashier->read($dt_post['cashier_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $t = time();
                    $kdinv = 'RBS';
                    $codecounter = array(
                        'var_1' => $kdinv,
                        'var_2' => date('Y'),
                        'var_3' => date('m'),
                        'var_5' => date('Y-m')
                    );
                    $this->load->library("transaction");
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = date('d').'/'.$codecounter['var_3'].'/'.$codecounter['var_1'].'/'.str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT).'/'.$codecounter['var_2'];
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => $kdinv,
                            'var_2' => date('Y'),
                            'var_3' => date('m'),
                            'var_5' => date('Y-m'),
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = date('d').'/'.$codecounter['var_3'].'/'.$codecounter['var_1'].'/'.str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT).'/'.$codecounter['var_2'];
                    }
                    $dt_transaction_invoice = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'pharmacy_prescription_id'       => $dt_post['transactions'][0]['pharmacy_prescription_id'],
                        'code'                  => $code,
                        'cashier_id'            => $user_id,
                        'is_delete'             => 0,
                        'var_1'                 => isset($dt_post['var_1']) ? $dt_post['var_1'] : '',
                        'var_2'                 => isset($dt_post['var_2']) ? $dt_post['var_2'] : '',
                        'var_3'                 => isset($dt_post['var_3']) ? $dt_post['var_3'] : '',
                        'date_added'            => date("Y-m-d H:i:s",$t)
                    );
                    // echo "<pre>";
                    // var_dump($dt_transaction_invoice);
                    // var_dump($dt_post);
                    // echo "</pre>";
                    // die();
                    $this->db->trans_start();
                    $id = $this->$model->create($dt_transaction_invoice);
                    $dt_transaction_invoice_item = array(
                        'transaction_invoice_id'    => $id,
                        'transaction_id'            => $dt_post['transactions'][0]['pharmacy_prescription_id'],
                        'flag_status_id'            => 2,
                        'type_transaction_id'       => 14,
                    );
                    $this->Transaction_invoice_item->replace($dt_transaction_invoice_item);
                    $dt_prescription = array(
                        'id'                        => $dt_post['transactions'][0]['pharmacy_prescription_id'],
                        'var_3'                     => $dt_post['var_3'],
                    );
                    // echo "<pre>";
                    // var_dump($dt_prescription);
                    // echo "<pre>";
                    // die();
                    $this->Transaction_prescription->replace($dt_prescription);
                    for ($i=0; $i < count($dt_post['transactions']); $i++) { 
                        // Check all kind of transaction
                        $dt_prescription_item = array(
                            'pharmacy_prescription_id'      => $dt_post['transactions'][$i]['pharmacy_prescription_id'],
                            'pharmacy_catalog_id'           => $dt_post['transactions'][$i]['pharmacy_catalog_id'],
                            'var_4'                         => $dt_post['transactions'][$i]['var_4'],
                        );
                        $this->Transaction_prescription_item->replace($dt_prescription_item);
                        // 5 Presc
                        // if($dt_post['transactions'][$i]['type_transaction_id'] == 14) {
                        //     $dt_presc = $this->Transaction_prescription->read($dt_post['transactions'][$i]['transaction_id'],false);
                        //     $dt_presc_item = $this->Transaction_prescription_item->read($dt_post['transactions'][$i]['transaction_id'],false);
                        //     for ($pi=0; $pi < count($dt_presc_item); $pi++) { 
                        //         $detailPrescItem[$pi] = array(
                        //             'itemNo' => $dt_presc_item[$pi]->bride_code,
                        //             'unitPrice' => $dt_presc_item[$pi]->var_1,
                        //             'warehouseName' => $dt_presc[0]->depo_name,
                        //             'departmentName' => 'FARMASI',
                        //             'quantity' => $dt_presc_item[$pi]->qty-($dt_presc_item[$pi]->is_retur_full === 1 && $dt_presc_item[$pi]->pharmacy_catalog_id_retur || $dt_presc_item[$pi]->pharmacy_catalog_id_ref ? 0 : $dt_presc_item[$pi]->qty_retur)
                        //         );
                        //     }
                        // }
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
                } else {
                    $this->REST_Return(404, 'Wrong registration_id or cashier_id!');
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
            $registration_id = '';
            if(isset($q_strings['registration_id'])){
                $registration_id = $q_strings['registration_id']; 
                unset($q_strings['registration_id']);
            }
            if($registration_id != ''){
                $this->$model->whereFields['a.registration_id'] = $registration_id; 
            };
            $id = '';
            if(isset($q_strings['id'])){
                $id = $q_strings['id']; 
                unset($q_strings['id']);
            }
            if($id != ''){
                $this->$model->whereFields['a.id'] = $id; 
            };
            $this->$model->whereFields['a.is_delete'] = 0; 
            $output = $this->$model->read();
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $dt_transaction_invoice_item = $this->Transaction_invoice_item->read($output[$i]->id);
                for ($j=0; $j < count($dt_transaction_invoice_item); $j++) { 
                    $output[$i]->transactions[$j]['transaction_id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                    $output[$i]->transactions[$j]['flag_status_id'] = $dt_transaction_invoice_item[$j]->flag_status_id;
                    $output[$i]->transactions[$j]['type_transaction_id'] = $dt_transaction_invoice_item[$j]->type_transaction_id;
                
                    // 1 Nurse
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '1') {
                        $this->Transaction_nurse->whereFields['d.id'] = $output[$i]->registration_id; 
                        $dt_nurse = $this->Transaction_nurse->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['nurse_id'] = $dt_nurse[0]->nurse_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_nurse[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_nurse[0]->costing_name;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_nurse[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_nurse[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_nurse[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_nurse[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_nurse[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_nurse[0]->date_added;
                        if ($dt_nurse[0]->costing_id) {
                            $this->Costing_services_class_inv->whereFields = array();
                            $this->Costing_services_class_inv->whereFields['a.costing_id'] = $dt_nurse[0]->costing_id; 
                            $dt_cost = $this->Costing_services_class_inv->read(0,false);
                            $output[$i]->transactions[$j]['type_costing_id'] = $dt_cost[0]->type_costing_id;
                            $output[$i]->transactions[$j]['classification_one_type_id'] = $dt_cost[0]->classification_one_type_id;
                            $output[$i]->transactions[$j]['classification_two_type_id'] = $dt_cost[0]->classification_two_type_id;
                            $output[$i]->transactions[$j]['classification_three_type_id'] = $dt_cost[0]->classification_three_type_id;
                            $output[$i]->transactions[$j]['type_costing_name'] = $dt_cost[0]->type_costing_name;
                            $output[$i]->transactions[$j]['classification_one_type_name'] = $dt_cost[0]->classification_one_type_name;
                            $output[$i]->transactions[$j]['classification_two_type_name'] = $dt_cost[0]->classification_two_type_name;
                            $output[$i]->transactions[$j]['classification_three_type_name'] = $dt_cost[0]->classification_three_type_name;

                            // KELAS 2
                            $this->Costing_services_class_inv->whereFields = array();
                            $this->Costing_services_class_inv->whereFields['a.service_class_id'] = 'f39fbb8f-082e-4c71-8802-92943b6e4683'; 
                            $this->Costing_services_class_inv->whereFields['a.company_insurance_id'] = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'; 
                            $this->Costing_services_class_inv->whereFields['c.name'] = $dt_nurse[0]->costing_name; 
                            $dt_cost = $this->Costing_services_class_inv->read(0,false);
                            $output[$i]->transactions[$j]['var_1_service_class_2'] = $dt_cost[0]->var_1;
                        }
                    }

                    // 2 Doctor
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '2') {
                        $this->Transaction_doctor->whereFields['d.id'] = $output[$i]->registration_id; 
                        $dt_doctor = $this->Transaction_doctor->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['soap_id'] = $dt_doctor[0]->soap_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_doctor[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_doctor[0]->costing_name;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_doctor[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_doctor[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_doctor[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_doctor[0]->var_3;
                        if ($dt_doctor[0]->disc_id == 1) {
                            $output[$i]->transactions[$j]['var_4'] = ($dt_doctor[0]->var_1*$dt_doctor[0]->var_4)/100;
                            // $output[$i]->transactions[$j]['var_4'] = ($dt_doctor[0]->var_1-($dt_doctor[0]->var_1*$dt_doctor[0]->var_4)/100)*$dt_doctor[0]->var_2;
                        } else if ($dt_doctor[0]->disc_id == 2) {
                            $output[$i]->transactions[$j]['var_4'] = $dt_doctor[0]->var_4;
                        } else {
                            $output[$i]->transactions[$j]['var_4'] = 0;
                        }
                        $output[$i]->transactions[$j]['disc_id'] = $dt_doctor[0]->disc_id;
                        $output[$i]->transactions[$j]['notes'] = $dt_doctor[0]->notes;
                        $output[$i]->transactions[$j]['doctor_in_charge_name'] = $dt_doctor[0]->doctor_in_charge_name;
                        $output[$i]->transactions[$j]['date_added'] = $dt_doctor[0]->date_added;
                        if ($dt_doctor[0]->costing_id) {
                            $this->Costing_services_class_inv->whereFields = array();
                            $this->Costing_services_class_inv->whereFields['a.id'] = $dt_doctor[0]->costing_id; 
                            $dt_cost = $this->Costing_services_class_inv->read(0,false);
                            $output[$i]->transactions[$j]['type_costing_id'] = $dt_cost[0]->type_costing_id;
                            $output[$i]->transactions[$j]['classification_one_type_id'] = $dt_cost[0]->classification_one_type_id;
                            $output[$i]->transactions[$j]['classification_two_type_id'] = $dt_cost[0]->classification_two_type_id;
                            $output[$i]->transactions[$j]['classification_three_type_id'] = $dt_cost[0]->classification_three_type_id;
                            $output[$i]->transactions[$j]['type_costing_name'] = $dt_cost[0]->type_costing_name;
                            $output[$i]->transactions[$j]['classification_one_type_name'] = $dt_cost[0]->classification_one_type_name;
                            $output[$i]->transactions[$j]['classification_two_type_name'] = $dt_cost[0]->classification_two_type_name;
                            $output[$i]->transactions[$j]['classification_three_type_name'] = $dt_cost[0]->classification_three_type_name;

                            // KELAS 2
                            $this->Costing_services_class_inv->whereFields = array();
                            $this->Costing_services_class_inv->whereFields['a.service_class_id'] = 'f39fbb8f-082e-4c71-8802-92943b6e4683'; 
                            $this->Costing_services_class_inv->whereFields['a.company_insurance_id'] = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'; 
                            $this->Costing_services_class_inv->whereFields['c.name'] = $dt_doctor[0]->costing_name; 
                            $dt_cost = $this->Costing_services_class_inv->read(0,false);
                            $output[$i]->transactions[$j]['var_1_service_class_2'] = $dt_cost[0]->var_1;
                        }
                    }

                    // 14 Prescription
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '14') {
                        if ($dt_transaction_invoice_item[$j]->transaction_id) {
                            $dt_presc = $this->Transaction_prescription->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                            $this->Transaction_prescription_item->whereFields['a.is_delete'] = 0; 
                            $dt_presc_item = $this->Transaction_prescription_item->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                            for ($pi=0; $pi < count($dt_presc_item); $pi++) { 
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_prescription_id'] = @$dt_presc_item[$pi]->pharmacy_prescription_id;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_name'] = @$dt_presc_item[$pi]->pharmacy_catalog_name;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['qty'] = @$dt_presc_item[$pi]->qty;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['qty_retur'] = @$dt_presc_item[$pi]->qty_retur;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['is_retur_full'] = @$dt_presc_item[$pi]->is_retur_full;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_id_retur'] = @$dt_presc_item[$pi]->pharmacy_catalog_id_retur;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_id_ref'] = @$dt_presc_item[$pi]->pharmacy_catalog_id_ref;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['var_1'] = @$dt_presc_item[$pi]->var_1;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['var_2'] = @$dt_presc_item[$pi]->var_2;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['var_3'] = @$dt_presc_item[$pi]->var_3;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['var_4'] = @$dt_presc_item[$pi]->var_4;
                            }
                            $output[$i]->transactions[$j]['pharmacy_prescription_id'] = @$dt_presc[0]->id;
                            $output[$i]->transactions[$j]['fullname'] = @$dt_presc[0]->fullname;
                            $output[$i]->transactions[$j]['var_1'] = @$dt_presc[0]->var_1;
                            $output[$i]->transactions[$j]['var_2'] = @$dt_presc[0]->var_2;
                            $output[$i]->transactions[$j]['var_3'] = @$dt_presc[0]->var_3;
                            $output[$i]->transactions[$j]['date_added'] = @$dt_presc[0]->date_added;
                        }
                    }

                    // 4 Administration
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '13') {
                        $dt_administration = $this->Transaction_administration->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                        $this->Costing_services_class_inv->whereFields = array();
                        $this->Costing_services_class->whereFields['a.costing_id'] = $dt_administration[0]->costing_id; 
                        $dt_cost = $this->Costing_services_class->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_administration[0]->registration_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_administration[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_cost[0]->name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_administration[0]->var_1;
                        if ($dt_administration[0]->disc_id == 1) {
                            $output[$i]->transactions[$j]['var_4'] = ($dt_administration[0]->var_1*$dt_administration[0]->var_4)/100;
                            // $output[$i]->transactions[$j]['var_4'] = ($dt_administration[0]->var_1-($dt_administration[0]->var_1*$dt_administration[0]->var_4)/100)*$dt_administration[0]->var_2;
                        } else if ($dt_administration[0]->disc_id == 2) {
                            $output[$i]->transactions[$j]['var_4'] = $dt_administration[0]->var_4;
                        } else {
                            $output[$i]->transactions[$j]['var_4'] = 0;
                        }
                        $output[$i]->transactions[$j]['date_added'] = $dt_administration[0]->date_added;
                    }

                    // Laboratorium
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '11') {
                        $this->Laboratory_request_item->whereFields = array();
                        $this->Laboratory_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                        $dt_lab = $this->Laboratory_request_item->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_lab[0]->registration_id;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_lab[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_lab[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_lab[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_lab[0]->var_1;
                        $output[$i]->transactions[$j]['date_added'] = $dt_lab[0]->date_added;
                    }

                    // Radiology
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '12') {
                        $this->Radiology_request_item->whereFields = array();
                        $this->Radiology_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                        $dt_rad = $this->Radiology_request_item->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_rad;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_rad[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_rad[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_rad[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_rad[0]->var_1;
                        $output[$i]->transactions[$j]['date_added'] = $dt_rad[0]->date_added;
                    }

                    // 5 Alkes
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '5') {
                        $dt_alkes = $this->Transaction_alkes->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_alkes[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_alkes[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_alkes[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_alkes[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_alkes[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_alkes[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_alkes[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_alkes[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_alkes[0]->date_added;

                        // KELAS 2
                        $this->Costing_services_class_inv->whereFields = array();
                        $this->Costing_services_class_inv->whereFields['a.service_class_id'] = 'f39fbb8f-082e-4c71-8802-92943b6e4683'; 
                        $this->Costing_services_class_inv->whereFields['a.company_insurance_id'] = 'e5a1e978-af77-4901-9ef8-4ebaeee77dfa'; 
                        $this->Costing_services_class_inv->whereFields['c.name'] = $dt_alkes[0]->costing_name; 
                        $dt_cost = $this->Costing_services_class_inv->read(0,false);
                        $output[$i]->transactions[$j]['var_1_service_class_2'] = $dt_cost[0]->var_1;
                    }

                    // 6 BHP
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '6') {
                        $dt_bhp = $this->Transaction_bhp->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_bhp[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_bhp[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_bhp[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_bhp[0]->var_2;
                        $output[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_bhp[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_bhp[0]->date_added;
                    }

                    // 7 Blood
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '7') {
                        $dt_blood = $this->Transaction_blood->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_blood[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_blood[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_blood[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_blood[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_blood[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_blood[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_blood[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_blood[0]->date_added;
                    }

                    // 8 MCU
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '8') {
                        $dt_mcu = $this->Transaction_mcu->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_mcu[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_mcu[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_mcu[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_mcu[0]->var_2;
                        $output[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_mcu[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_mcu[0]->date_added;
                    }

                    // 9 Physiotherapy
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '15') {
                        $this->Physiotherapy_request_item->whereFields = array();
                        $this->Physiotherapy_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                        $dt_physiotherapy = $this->Physiotherapy_request_item->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_physiotherapy;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_physiotherapy[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_physiotherapy[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_physiotherapy[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_physiotherapy[0]->var_1;
                        $output[$i]->transactions[$j]['notes'] = $dt_physiotherapy[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_physiotherapy[0]->date_added;
                    }

                    // 10 Bed
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '10') {
                        $dt_bed = $this->Transaction_bed->read($dt_transaction_invoice_item[$j]->transaction_id);
                        
                        // // Total hours
                        // $checkin   = $dt_bed[0]->checkin_date;
                        // $checkout  = $dt_bed[0]->checkout_date;
                        // $start     = new DateTime($checkin);
                        // $end       = new DateTime($checkout);
                        // $output[$i]->transactions[$j]['checkin_date']  = $checkin;
                        // $output[$i]->transactions[$j]['checkout_date'] = $checkout;

                        // $diff = $end->diff($start);
                        // $totalHours =  $diff->h + ($diff->days * 24) + ($diff->m > 30 ? 1 : 0);
                        
                        // // count prorate room
                        // $cost_prorate = $dt_bed[0]->is_non_prorate == 0 ? $dt_bed[0]->var_1 / 24 : $dt_bed[0]->var_1;
                        // $var_2        = $cost_prorate * $totalHours;

                        // // count prorate room
                        // $output[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2 == null && $dt_bed[0]->var_2 == '' && $dt_bed[0]->var_2 == 0 ? $totalHours : $dt_bed[0]->var_2;
                        // $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1 == null && $dt_bed[0]->var_1 == '' && $dt_bed[0]->var_1 == 0 ? $cost_prorate : $cost_prorate;
                        // $output[$i]->transactions[$j]['cost_prorate'] = $cost_prorate;
                        // Calculate costs
                            $checkin   = $dt_bed[0]->checkin_date;
                            $checkout  = $dt_bed[0]->checkout_date;
                            $start     = new DateTime($checkin);
                            $end       = new DateTime($checkout);
                            $output[$i]->transactions[$j]['checkin_date']  = $checkin;
                            $output[$i]->transactions[$j]['checkout_date'] = $checkout;
    
                            // Hitung selisih waktu
                            $diff = $end->diff($start);
    
                            // Ambil selisih hari dasar
                            $totalDays = $diff->days;
    
                            // Jika ada sisa jam/menit, tambahkan 1 hari
                            if ($diff->h > 0 || $diff->i > 0) {
                                $totalDays += 1;
                            }
    
                            // Jika total hari 0, minimal tetap 1 hari
                            if ($totalDays == 0) {
                                $totalDays = 1;
                            }
    
                            // Hitung total harga
                            $cost_prorate = $dt_bed[0]->is_non_prorate == 0 ? $dt_bed[0]->var_1 : $dt_bed[0]->var_1;
                            $var_2        = $cost_prorate * $totalDays;
    
                            $output[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2 == null && $dt_bed[0]->var_2 == '' && $dt_bed[0]->var_2 == 0 ? $totalDays : $dt_bed[0]->var_2;
                            $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1 == null && $dt_bed[0]->var_1 == '' && $dt_bed[0]->var_1 == 0 ? $cost_prorate : $cost_prorate;
                            $output[$i]->transactions[$j]['cost_prorate'] = $cost_prorate;
                        // Calculate costs

                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_bed[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['bed_id'] = $dt_bed[0]->bed_id;
                        $output[$i]->transactions[$j]['bed_name'] = $dt_bed[0]->bed_name;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_bed[0]->costing_name;
                        // $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1;
                        // $output[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_bed[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_bed[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_bed[0]->date_added;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_bed[0]->health_facility_item_name;
                    }
                }
                $this->Payment->whereFields['a.invoice_id'] = $output[$i]->id; 
                $this->Payment->whereFields['a.is_delete'] = 0; 
                $dt_payment = $this->Payment->read();
                if ($dt_payment) {
                    for ($dp=0; $dp < count($dt_payment); $dp++) { 
                        $this->Payment_item->whereFields['p.is_delete'] = 0; 
                        $dt_payment_item = $this->Payment_item->read($dt_payment[$dp]->id);
                        for ($x=0; $x < count($dt_payment_item); $x++) { 
                            $output[$i]->payment = $dt_payment_item;
                            $output[$i]->var_2 += $dt_payment_item[$x]->var_3;
                        }
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
                $this->REST_Return(200, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($output || $output == [] && $id == '0') {
                
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(200, $this->name . ' with id ' . $id . ' does not exist!');
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

    public function detail_free_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
            $pharmacy_prescription_id = '';
            if(isset($q_strings['pharmacy_prescription_id'])){
                $pharmacy_prescription_id = $q_strings['pharmacy_prescription_id']; 
                unset($q_strings['pharmacy_prescription_id']);
            }
            if($pharmacy_prescription_id != ''){
                $this->$model->whereFields['a.pharmacy_prescription_id'] = $pharmacy_prescription_id; 
            };
            $id = '';
            if(isset($q_strings['id'])){
                $id = $q_strings['id']; 
                unset($q_strings['id']);
            }
            if($id != ''){
                $this->$model->whereFields['a.id'] = $id; 
            };
            $this->$model->whereFields['a.is_delete'] = 0; 
            $output = $this->$model->read();
            $response_data = array();
            for ($i=0; $i < count($output); $i++) { 
                $dt_transaction_invoice_item = $this->Transaction_invoice_item->read($output[$i]->id);
                for ($j=0; $j < count($dt_transaction_invoice_item); $j++) { 
                    $output[$i]->transactions[$j]['transaction_id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                    $output[$i]->transactions[$j]['flag_status_id'] = $dt_transaction_invoice_item[$j]->flag_status_id;
                    $output[$i]->transactions[$j]['type_transaction_id'] = $dt_transaction_invoice_item[$j]->type_transaction_id;
                
                    // 1 Nurse
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '1') {
                        $dt_nurse = $this->Transaction_nurse->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['nurse_id'] = $dt_nurse[0]->nurse_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_nurse[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_nurse[0]->costing_name;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_nurse[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_nurse[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_nurse[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_nurse[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_nurse[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_nurse[0]->date_added;
                    }

                    // 2 Doctor
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '2') {
                        $dt_doctor = $this->Transaction_doctor->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['soap_id'] = $dt_doctor[0]->soap_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_doctor[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_doctor[0]->costing_name;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_doctor[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_doctor[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_doctor[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_doctor[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_doctor[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_doctor[0]->date_added;
                    }

                    // 14 Prescription
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '14') {
                        if ($dt_transaction_invoice_item[$j]->transaction_id) {
                            $dt_presc = $this->Transaction_prescription->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                            $dt_presc_item = $this->Transaction_prescription_item->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                            for ($pi=0; $pi < count($dt_presc_item); $pi++) { 
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_prescription_id'] = @$dt_presc_item[$pi]->pharmacy_prescription_id;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_name'] = @$dt_presc_item[$pi]->pharmacy_catalog_name;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['qty'] = @$dt_presc_item[$pi]->qty;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['qty_retur'] = @$dt_presc_item[$pi]->qty_retur;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['is_retur_full'] = @$dt_presc_item[$pi]->is_retur_full;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_id_retur'] = @$dt_presc_item[$pi]->pharmacy_catalog_id_retur;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_id_ref'] = @$dt_presc_item[$pi]->pharmacy_catalog_id_ref;
                                $output[$i]->transactions[$j]['prescription_item'][$pi]['var_1'] = @$dt_presc_item[$pi]->var_1;
                            }
                            $output[$i]->transactions[$j]['pharmacy_prescription_id'] = @$dt_presc[0]->id;
                            $output[$i]->transactions[$j]['var_1'] = @$dt_presc[0]->var_1;
                            $output[$i]->transactions[$j]['date_added'] = @$dt_presc[0]->date_added;
                        }
                    }

                    // 4 Administration
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '13') {
                        $dt_administration = $this->Transaction_administration->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                        $this->Costing_services_class->whereFields = array();
                        $this->Costing_services_class->whereFields['a.costing_id'] = $dt_administration[0]->costing_id; 
                        $dt_cost = $this->Costing_services_class->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_administration[0]->registration_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_administration[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_cost[0]->name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_administration[0]->var_1;
                        $output[$i]->transactions[$j]['date_added'] = $dt_administration[0]->date_added;
                    }

                    // Laboratorium
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '11') {
                        $this->Laboratory_request_item->whereFields = array();
                        $this->Laboratory_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                        $dt_lab = $this->Laboratory_request_item->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_lab[0]->registration_id;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_lab[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_lab[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_lab[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_lab[0]->var_1;
                        $output[$i]->transactions[$j]['date_added'] = $dt_lab[0]->date_added;
                    }

                    // Radiology
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '12') {
                        $this->Radiology_request_item->whereFields = array();
                        $this->Radiology_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                        $dt_rad = $this->Radiology_request_item->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_rad;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_rad[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_rad[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_rad[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_rad[0]->var_1;
                        $output[$i]->transactions[$j]['date_added'] = $dt_rad[0]->date_added;
                    }

                    // 5 Alkes
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '5') {
                        $dt_alkes = $this->Transaction_alkes->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_alkes[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_alkes[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_alkes[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_alkes[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_alkes[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_alkes[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_alkes[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_alkes[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_alkes[0]->date_added;
                    }

                    // 6 BHP
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '6') {
                        $dt_bhp = $this->Transaction_bhp->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_bhp[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_bhp[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_bhp[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_bhp[0]->var_2;
                        $output[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_bhp[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_bhp[0]->date_added;
                    }

                    // 7 Blood
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '7') {
                        $dt_blood = $this->Transaction_blood->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_blood[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_blood[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_blood[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_blood[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_blood[0]->var_2;
                        $output[$i]->transactions[$j]['var_3'] = $dt_blood[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_blood[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_blood[0]->date_added;
                    }

                    // 8 MCU
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '8') {
                        $dt_mcu = $this->Transaction_mcu->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_mcu[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_mcu[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_mcu[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_mcu[0]->var_2;
                        $output[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_mcu[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_mcu[0]->date_added;
                    }

                    // 9 Physiotherapy
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '15') {
                        $this->Physiotherapy_request_item->whereFields = array();
                        $this->Physiotherapy_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                        $dt_physiotherapy = $this->Physiotherapy_request_item->read(0,false);
                        $output[$i]->transactions[$j]['registration_id'] = $dt_physiotherapy;
                        $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_physiotherapy[0]->health_facility_item_name;
                        $output[$i]->transactions[$j]['costing_id'] = $dt_physiotherapy[0]->costing_id;
                        $output[$i]->transactions[$j]['costing_name'] = $dt_physiotherapy[0]->costing_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_physiotherapy[0]->var_1;
                        $output[$i]->transactions[$j]['notes'] = $dt_physiotherapy[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_physiotherapy[0]->date_added;
                    }

                    // 10 Bed
                    if($dt_transaction_invoice_item[$j]->type_transaction_id == '10') {
                        $dt_bed = $this->Transaction_bed->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_destination_id'] = $dt_bed[0]->registration_destination_id;
                        $output[$i]->transactions[$j]['bed_id'] = $dt_bed[0]->bed_id;
                        $output[$i]->transactions[$j]['bed_name'] = $dt_bed[0]->bed_name;
                        $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1;
                        $output[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2;
                        $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_3;
                        $output[$i]->transactions[$j]['notes'] = $dt_bed[0]->notes;
                        $output[$i]->transactions[$j]['date_added'] = $dt_bed[0]->date_added;
                    }
                }
                $this->Payment->whereFields['a.invoice_id'] = $output[$i]->id; 
                $this->Payment->whereFields['a.is_delete'] = 0; 
                $dt_payment = $this->Payment->read();
                if ($dt_payment) {
                    for ($dp=0; $dp < count($dt_payment); $dp++) { 
                        $this->Payment_item->whereFields['p.is_delete'] = 0; 
                        $dt_payment_item = $this->Payment_item->read($dt_payment[$dp]->id);
                        for ($x=0; $x < count($dt_payment_item); $x++) { 
                            $output[$i]->payment = $dt_payment_item;
                            $output[$i]->var_2 += $dt_payment_item[$x]->var_3;
                        }
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
                $this->REST_Return(422, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($output || $output == [] && $id == '0') {
                
                $response_data = $output;
                $error   = false;
            } else {
                $this->REST_Return(422, $this->name . ' with id ' . $id . ' does not exist!');
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

    public function journal_get($p = 0){
        die();
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        $response_data = array();
        if($id != ''){
            // $this->$model->whereFields['a.id'] = $id; 
            $this->$model->whereFields['a.is_delete'] = 0; 
            $this->$model->whereFields['ri.company_id'] = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8'; 
            $this->$model->whereFields['hfi.health_facility_id !='] = '40f06534-07ec-4221-ad9d-dea08196079f'; 
            $this->$model->whereFields['r.register_date >='] = '2024-09-01 00:00:00';
            $this->$model->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
            $this->$model->whereFields['ri.is_primary'] = 1; 
            $this->$model->whereFields['a.is_accurate'] = 1; 
            $this->$model->whereFields['a.is_journal'] = 0; 
            $output = $this->$model->read();
            for ($i=0; $i < count($output); $i++) { 
                // 10.03.01.05.00 BPJS TK DEBIT
                // 40.21.00.00.07 BPJS TK KREDIT
                // 10.03.01.04.00 BPJS JKN DEBIT
                // 40.21.00.00.02 BPJS JKN KREDIT
                $detailJournalVoucher[0] = array(
                    'accountNo' => '10.03.01.04.00',
                    'amount' => $output[$i]->var_3,
                    'amountType' => 'DEBIT',
                    'customerNo' => $output[$i]->norm,
                    'subsidiaryType' => 'CUSTOMER'
                );
                $detailJournalVoucher[1] = array(
                    'accountNo' => '40.21.00.00.02',
                    'amount' => $output[$i]->var_3,
                    'amountType' => 'CREDIT',
                    'customerNo' => $output[$i]->norm,
                    'subsidiaryType' => 'CUSTOMER'
                );
                $toJournal = array(
                    'detailJournalVoucher' => $detailJournalVoucher,
                    'description' => 'BPJS PASIEN '.$output[$i]->fullname.' NO INVOICE :'.$output[$i]->code,
                    'number' => $output[$i]->code,
                    'transDate' => date("d/m/Y",strtotime($output[$i]->date_added))
                );
                // echo "<pre>";
                // var_dump($toJournal);
                // echo "</pre>";
                // die();
                $return_bride_inv = $this->bride_accurate('api/journal-voucher/save.do', json_encode($toJournal));
                if ($return_bride_inv['s'] == true) {
                    $this->$model->replace(array('id'=>$output[$i]->id,'is_journal'=>1));
                } else {
                    // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_inv),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
                    var_dump($return_bride_inv);
                    var_dump($toJournal);
                    // $this->REST_Return(404, 'Failed Accurate = ');
                    // return;
                }
            }
        };
        $this->REST_Return(200, 'SUCCESS', $response_data);
	}

    public function report_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $response_data = array();
        // // $this->$model->whereFields['a.id'] = $id; 
        // $this->$model->whereFields['a.is_delete'] = 0; 
        // $this->$model->whereFields['r.register_date >='] = '2024-09-01 00:00:00';
        // $this->$model->whereFields['r.register_dates <='] = '2024-09-07 00:00:00';
        // // $this->$model->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
        // $this->$model->whereFields['ri.is_primary'] = 1; 
        // $response_data = $this->$model->read();

        $output = $this->db->query("
			SELECT `r`.`id`, `r`.`code` as `registrasi_code`, `r`.`norm`, `r`.`register_date`, p.fullname as nama_pasien, `rd`.`id` as `registration_destination_id`, `rd`.`id` as `registration_destination_id`, `hf`.`name` as Unit, `rd`.`checkin_date` as `tgl_masuk`, `rd`.`checkout_date` as `tgl_keluar`
            FROM `registration` as `r` 
            LEFT JOIN `registration_destination` `rd` ON `r`.`id` = `rd`.`registration_id` 
            LEFT JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary` = 1
            INNER JOIN patient p ON p.norm = r.norm
            INNER JOIN helper h ON h.code = p.sex AND h.variable = 'sex'
            LEFT JOIN helper h2 ON h2.code = ri.insurance_id AND h2.variable = 'insurance'
            INNER JOIN health_facility_item hfi ON hfi.id = rd.health_facility_item_id
            INNER JOIN health_facility hf ON hf.id = hfi.health_facility_id
            WHERE `r`.`is_cancel` = 0 AND `ri`.`company_id` = '1a055e0f-1b9d-42d2-bb84-7d8bf82fc4b8' AND `hf`.`id` = '3cb626f5-9148-4fb1-8663-3d7f028b01a6' AND
            `r`.`register_date` >= '2024-09-01 00:00:00' AND `r`.`register_date` <= '2024-09-31 23:59:59' AND `ri`.`is_primary` = 1 
            ORDER BY `r`.`register_date` ASC
		")->result();
        for ($i=0; $i < count($output); $i++) {
            if ($output[$i]->Unit == 'RAWAT INAP') {
                $output[$i]->tgl_masuk = date('d/m/Y', strtotime($output[$i]->tgl_masuk));
                $output[$i]->tgl_keluar = date('d/m/Y', strtotime($output[$i]->tgl_keluar));
            } else if ($output[$i]->Unit == 'IGD') {
                $output[$i]->tgl_masuk = date('d/m/Y', strtotime($output[$i]->register_date));
                $output[$i]->tgl_keluar = date('d/m/Y', strtotime($output[$i]->register_date));
            } else if ($output[$i]->Unit == 'RAWAT INAP') {
                $output[$i]->tgl_masuk = date('d/m/Y', strtotime($output[$i]->register_date));
                $output[$i]->tgl_keluar = date('d/m/Y', strtotime($output[$i]->register_date));
            }
            $response_data_adm = $this->db->query("
                SELECT `a`.`costing_name`,SUM(`a`.`var_1`) as `total` 
                FROM `transaction_administration` as `a` 
                WHERE `a`.`registration_id` = '{$output[$i]->id}'")->result();
            $output[$i]->total += $response_data_adm[0]->total;
            $response_data_nurse = $this->db->query("
                SELECT `a`.`costing_name`,SUM(`a`.`var_3`) as `total` 
                FROM `transaction_nurse` as `a` 
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0")->result();
            $output[$i]->total += $response_data_nurse[0]->total;
            $response_data_doctor = $this->db->query("
                SELECT `a`.`costing_name`,SUM(`a`.`var_3`) as `total` 
                FROM `transaction_doctor` as `a` 
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0")->result();
            $output[$i]->total += $response_data_doctor[0]->total;
            $response_data_lab = $this->db->query("
                SELECT `b`.`costing_name`,SUM(`b`.`var_1`) as `total` 
                FROM `laboratory_request` as `a` 
                LEFT JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
            $output[$i]->total += $response_data_lab[0]->total;
            $response_data_rad = $this->db->query("
                SELECT `b`.`costing_name`,SUM(`b`.`var_1`) as `total` 
                FROM `radiology_request` as `a` 
                LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
            $output[$i]->total += $response_data_rad[0]->total;
            $response_data_pres = $this->db->query("
                SELECT `c`.`name`,SUM(`b`.`var_2`) as `total` 
                FROM `pharmacy_prescription` as `a` 
                LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
                LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `b`.`is_retur_full` = 0")->result();
            $output[$i]->total += $response_data_pres[0]->total;
        }
        $this->REST_Return(200, 'SUCCESS', $output);
	}

    public function report_detail_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $response_data = array();
        // // $this->$model->whereFields['a.id'] = $id; 
        // $this->$model->whereFields['a.is_delete'] = 0; 
        // $this->$model->whereFields['r.register_date >='] = '2024-09-01 00:00:00';
        // $this->$model->whereFields['r.register_dates <='] = '2024-09-07 00:00:00';
        // // $this->$model->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
        // $this->$model->whereFields['ri.is_primary'] = 1; 
        // $response_data = $this->$model->read();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $output = $this->db->query("
			SELECT `r`.`id`, `r`.`code` as `registrasi_code`, `h2`.`name` as `insurance`, `r`.`norm`, `c`.`name` as `penjamin`, `r`.`register_date`, p.fullname as nama_pasien, `rd`.`id` as `registration_destination_id`, `rd`.`id` as `registration_destination_id`, `hf`.`name` as Unit, `hfi`.`name` as Unit_sub, `rd`.`checkin_date` as `tgl_masuk`, `rd`.`checkout_date` as `tgl_keluar`
            FROM `registration` as `r` 
            LEFT JOIN `registration_destination` `rd` ON `r`.`id` = `rd`.`registration_id` 
            LEFT JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary` = 1
            INNER JOIN patient p ON p.norm = r.norm
            LEFT JOIN helper h2 ON h2.code = ri.insurance_id AND h2.variable = 'insurance'
            LEFT JOIN company c ON ri.company_id = c.id
            INNER JOIN health_facility_item hfi ON hfi.id = rd.health_facility_item_id
            INNER JOIN health_facility hf ON hf.id = hfi.health_facility_id
            WHERE `r`.`is_cancel` = 0 AND `hf`.`id` = '444b3134-5b75-4b67-af72-a558d46716d7' AND
            `r`.`register_date` >= '2024-10-01 00:00:00' AND `r`.`register_date` <= '2024-10-18 23:59:59' AND `ri`.`is_primary` = 1 AND `rd`.`is_transfer` = 0 
            ORDER BY `r`.`register_date` ASC
		")->result();
        $sheet->setCellValue('A2', "NORM"); 
        $sheet->setCellValue('B2', "Nama Pasien"); 
        $sheet->setCellValue('C2', "Penjamin"); 
        $sheet->setCellValue('D2', "Unit");
        $sheet->setCellValue('E2', "Poli/Ruangan"); 
        $sheet->setCellValue('F2', "Tgl.Masuk"); 
        $sheet->setCellValue('G2', "Tgl.Keluar"); 
        $sheet->setCellValue('J2', "Total Tarif"); 
        $numrow = 3; // Set baris pertama untuk isi tabel adalah baris ke 4
        for ($i=0; $i < count($output); $i++) {
            $response_data_adm = $this->db->query("
                SELECT `a`.`costing_name`,`a`.`var_1` as `total` , `c`.`desc`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                FROM `transaction_administration` as `a` 
                LEFT JOIN costing c ON a.costing_id = c.id
                LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
                WHERE `a`.`registration_id` = '{$output[$i]->id}'")->result();
            $response_data_nurse = $this->db->query("
                SELECT `a`.`costing_name`,`a`.`var_2` as `tarif`,`a`.`var_1` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                FROM `transaction_nurse` as `a` 
                LEFT JOIN costing c ON a.costing_id = c.id
                LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0")->result();
            $response_data_doctor = $this->db->query("
                SELECT `a`.`costing_name`,`a`.`var_1` as `tarif`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                FROM `transaction_doctor` as `a` 
                LEFT JOIN costing_services_class cs ON a.costing_id = cs.id
                LEFT JOIN costing c ON cs.costing_id = c.id
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0")->result();
            $response_data_lab = $this->db->query("
                SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                FROM `laboratory_request` as `a` 
                LEFT JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
                LEFT JOIN costing c ON b.costing_id = c.id
                LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
            $response_data_rad = $this->db->query("
                SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                FROM `radiology_request` as `a` 
                LEFT JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
                LEFT JOIN costing c ON b.costing_id = c.id
                LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
            $response_data_pres = $this->db->query("
                SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`var_2` as `total` 
                FROM `pharmacy_prescription` as `a` 
                LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
                LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
                WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `b`.`is_retur_full` = 0")->result();

            $sheet->setCellValue('A'.$numrow, $output[$i]->norm);
            $sheet->setCellValue('B'.$numrow, $output[$i]->nama_pasien);
            $sheet->setCellValue('C'.$numrow, $output[$i]->insurance.' '.$output[$i]->penjamin);
            $sheet->setCellValue('D'.$numrow, $output[$i]->Unit);
            $sheet->setCellValue('E'.$numrow, $output[$i]->Unit_sub);
            $sheet->setCellValue('F'.$numrow, date('d/m/Y', strtotime($output[$i]->register_date)));
            $sheet->setCellValue('G'.$numrow, date('d/m/Y', strtotime($output[$i]->register_date)));
            $numrow_detail = $numrow+1;
            $numrow++;
            
            for ($adm=0; $adm < count($response_data_adm); $adm++) {
                $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('H'.$numrow_detail, $response_data_adm[$adm]->desc);
                $sheet->setCellValue('I'.$numrow_detail, $response_data_adm[$adm]->bride_department_sub);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_adm[$adm]->costing_name);
                $sheet->setCellValue('K'.$numrow_detail, $response_data_adm[$adm]->total);
                $sheet->setCellValue('L'.$numrow_detail, 1);
                $sheet->setCellValue('M'.$numrow_detail, $response_data_adm[$adm]->total);
                $sheet->setCellValue('N'.$numrow_detail, $response_data_adm[$adm]->date_added);
                $sheet->setCellValue('O'.$numrow_detail, $response_data_adm[$adm]->total);
                $sheet->setCellValue('P'.$numrow_detail, $response_data_adm[$adm]->costing_id);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            for ($nurse=0; $nurse < count($response_data_nurse); $nurse++) {
                $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('H'.$numrow_detail, $response_data_nurse[$nurse]->desc);
                $sheet->setCellValue('I'.$numrow_detail, $response_data_nurse[$nurse]->bride_department_sub);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_nurse[$nurse]->costing_name);
                $sheet->setCellValue('K'.$numrow_detail, $response_data_nurse[$nurse]->tarif);
                $sheet->setCellValue('L'.$numrow_detail, $response_data_nurse[$nurse]->qty);
                $sheet->setCellValue('M'.$numrow_detail, $response_data_nurse[$nurse]->total);
                $sheet->setCellValue('N'.$numrow_detail, $response_data_nurse[$nurse]->date_added);
                $sheet->setCellValue('O'.$numrow_detail, $response_data_nurse[$nurse]->total);
                $sheet->setCellValue('P'.$numrow_detail, $response_data_nurse[$nurse]->costing_id);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            for ($doctor=0; $doctor < count($response_data_doctor); $doctor++) {
                $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('H'.$numrow_detail, $response_data_doctor[$doctor]->desc);
                $sheet->setCellValue('I'.$numrow_detail, $response_data_doctor[$doctor]->bride_department_sub == '' ? $output[$i]->Unit_sub : $response_data_doctor[$doctor]->bride_department_sub);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_doctor[$doctor]->costing_name);
                $sheet->setCellValue('K'.$numrow_detail, $response_data_doctor[$doctor]->tarif);
                $sheet->setCellValue('L'.$numrow_detail, $response_data_doctor[$doctor]->qty);
                $sheet->setCellValue('M'.$numrow_detail, $response_data_doctor[$doctor]->total);
                $sheet->setCellValue('N'.$numrow_detail, $response_data_doctor[$doctor]->date_added);
                $sheet->setCellValue('O'.$numrow_detail, $response_data_doctor[$doctor]->total);
                $sheet->setCellValue('P'.$numrow_detail, $response_data_doctor[$doctor]->costing_id);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            for ($lab=0; $lab < count($response_data_lab); $lab++) {
                $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('H'.$numrow_detail, $response_data_lab[$lab]->desc);
                $sheet->setCellValue('I'.$numrow_detail, $response_data_lab[$lab]->bride_department_sub);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_lab[$lab]->costing_name);
                $sheet->setCellValue('K'.$numrow_detail, $response_data_lab[$lab]->tarif);
                $sheet->setCellValue('L'.$numrow_detail, 1);
                $sheet->setCellValue('M'.$numrow_detail, $response_data_lab[$lab]->total);
                $sheet->setCellValue('N'.$numrow_detail, $response_data_lab[$lab]->date_added);
                $sheet->setCellValue('O'.$numrow_detail, $response_data_lab[$lab]->total);
                $sheet->setCellValue('P'.$numrow_detail, $response_data_lab[$lab]->costing_id);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            for ($rad=0; $rad < count($response_data_rad); $rad++) {
                $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('H'.$numrow_detail, $response_data_rad[$rad]->desc);
                $sheet->setCellValue('I'.$numrow_detail, $response_data_rad[$rad]->bride_department_sub);
                $sheet->setCellValue('J'.$numrow_detail, $response_data_rad[$rad]->costing_name);
                $sheet->setCellValue('K'.$numrow_detail, $response_data_rad[$rad]->tarif);
                $sheet->setCellValue('L'.$numrow_detail, 1);
                $sheet->setCellValue('M'.$numrow_detail, $response_data_rad[$rad]->total);
                $sheet->setCellValue('N'.$numrow_detail, $response_data_rad[$rad]->date_added);
                $sheet->setCellValue('O'.$numrow_detail, $response_data_rad[$rad]->total);
                $sheet->setCellValue('P'.$numrow_detail, $response_data_rad[$rad]->costing_id);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
            for ($pres=0; $pres < count($response_data_pres); $pres++) {
                $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                $sheet->setCellValue('H'.$numrow_detail, 'OBAT/ALKES');
                $sheet->setCellValue('I'.$numrow_detail, 'OBAT/ALKES');
                $sheet->setCellValue('J'.$numrow_detail, $response_data_pres[$pres]->name);
                $sheet->setCellValue('K'.$numrow_detail, $response_data_pres[$pres]->tarif);
                $sheet->setCellValue('L'.$numrow_detail, $response_data_pres[$pres]->qty);
                $sheet->setCellValue('M'.$numrow_detail, $response_data_pres[$pres]->total);
                $sheet->setCellValue('N'.$numrow_detail, $response_data_pres[$pres]->date_added);
                $sheet->setCellValue('O'.$numrow_detail, $response_data_pres[$pres]->total);
                $numrow_detail++; // Tambah 1 setiap kali looping
                $numrow++;
            }
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Laporan Apexa Billing");
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Apexa Billing.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
	}

    public function jurnal_piutang_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);

        

        $detailJournalVoucher[0] = array(
            'accountNo' => '10.03.01.04.00',
            'amount' => $output[$i]->var_3,
            'amountType' => 'DEBIT',
            'customerNo' => $output[$i]->norm,
            'subsidiaryType' => 'CUSTOMER'
        );
        $detailJournalVoucher[1] = array(
            'accountNo' => '40.21.00.00.02',
            'amount' => $output[$i]->var_3,
            'amountType' => 'CREDIT',
            'customerNo' => $output[$i]->norm,
            'subsidiaryType' => 'CUSTOMER'
        );
        $toJournal = array(
            'detailJournalVoucher' => $detailJournalVoucher,
            'description' => 'BPJS PASIEN '.$output[$i]->fullname.' NO INVOICE :'.$output[$i]->code,
            'number' => $output[$i]->code,
            'transDate' => date("d/m/Y",strtotime($output[$i]->date_added))
        );
        // echo "<pre>";
        // var_dump($toJournal);
        // echo "</pre>";
        // die();
        $return_bride_inv = $this->bride_accurate('api/journal-voucher/save.do', json_encode($toJournal));
        if ($return_bride_inv['s'] == true) {
            // $this->$model->replace(array('id'=>$output[$i]->id,'is_journal'=>1));
        } else {
            // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_inv),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
            var_dump($return_bride_inv);
            var_dump($toJournal);
            // $this->REST_Return(404, 'Failed Accurate = ');
            // return;
        }
    }

    public function report_detail_detail_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', 0); 
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $response_data = array();
        // // $this->$model->whereFields['a.id'] = $id; 
        // $this->$model->whereFields['a.is_delete'] = 0; 
        // $this->$model->whereFields['r.register_date >='] = '2024-09-01 00:00:00';
        // $this->$model->whereFields['r.register_dates <='] = '2024-09-07 00:00:00';
        // // $this->$model->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
        // $this->$model->whereFields['ri.is_primary'] = 1; 
        // $response_data = $this->$model->read();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $output = $this->db->query("
			SELECT `r`.`id`, `r`.`code` as `registrasi_code`, `h2`.`name` as `insurance`, `r`.`norm`, `c`.`name` as `penjamin`, `r`.`register_date`, `ri`.`sep`, p.fullname as nama_pasien, `rd`.`id` as `registration_destination_id`, `hf`.`name` as Unit, `hfi`.`name` as Unit_sub, `rd`.`checkin_date` as `tgl_masuk`, `rd`.`checkout_date` as `tgl_keluar`
            FROM `registration` as `r` 
            LEFT JOIN `registration_destination` `rd` ON `r`.`id` = `rd`.`registration_id` 
            LEFT JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary` = 1
            INNER JOIN patient p ON p.norm = r.norm
            LEFT JOIN helper h2 ON h2.code = ri.insurance_id AND h2.variable = 'insurance'
            LEFT JOIN company c ON ri.company_id = c.id
            INNER JOIN health_facility_item hfi ON hfi.id = rd.health_facility_item_id
            INNER JOIN health_facility hf ON hf.id = hfi.health_facility_id
            WHERE `r`.`is_cancel` = 0 AND
            `r`.`register_date` >= '2025-12-01 00:00:00' AND `r`.`register_date` <= '2025-12-11 23:59:59' AND `ri`.`is_primary` = 1 
            ORDER BY `r`.`register_date` ASC
            ")->result();
            // `ri`.`insurance_id` = 1 AND `hf`.`id` != '444b3134-5b75-4b67-af72-a558d46716d7' AND
        // WHERE `r`.`is_cancel` = 0 AND `hf`.`id` = '444b3134-5b75-4b67-af72-a558d46716d7' AND
        $sheet->setCellValue('A2', "NORM"); 
        $sheet->setCellValue('B2', "Nama Pasien"); 
        $sheet->setCellValue('C2', "Penjamin"); 
        $sheet->setCellValue('D2', "SEP"); 
        $sheet->setCellValue('E2', "Unit");
        $sheet->setCellValue('F2', "Poli/Ruangan"); 
        $sheet->setCellValue('G2', "Tgl.Masuk"); 
        $sheet->setCellValue('H2', "Tgl.Keluar"); 
        $sheet->setCellValue('J2', "TARIF/OBAT"); 
        $numrow = 3; // Set baris pertama untuk isi tabel adalah baris ke 4
        for ($i=0; $i < count($output); $i++) {
            if ($output[$i]->registration_destination_id != null || $output[$i]->registration_destination_id != "") {
                $response_data_adm = $this->db->query("
                    SELECT `a`.`costing_name`,`a`.`var_1` as `total` , `c`.`desc`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                    FROM `transaction_administration` as `a` 
                    INNER JOIN costing c ON a.costing_id = c.id
                    INNER JOIN costing_services_class cs ON c.id = cs.costing_id
                    INNER JOIN transaction_invoice_item tii ON a.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `a`.`registration_id` = '{$output[$i]->id}'")->result();
                $response_data_nurse = $this->db->query("
                    SELECT `a`.`costing_name`,`a`.`var_2` as `tarif`,`a`.`var_1` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                    FROM `transaction_nurse` as `a` 
                    INNER JOIN costing c ON a.costing_id = c.id
                    INNER JOIN nurse n ON a.nurse_id = n.id
                    INNER JOIN costing_services_class cs ON c.id = cs.costing_id
                    INNER JOIN transaction_invoice_item tii ON a.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `n`.`registration_destination_id` = '{$output[$i]->registration_destination_id}' 
                    AND `a`.`is_delete` = 0")->result();
                $response_data_doctor = $this->db->query("
                    SELECT `a`.`date_added`,`a`.`costing_name`,`a`.`var_1` as `tarif`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `cs`.`service_class_id`, `c`.`desc`, `cs`.`costing_id`, `d`.`fullname` as `doctor_name`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                    FROM `transaction_doctor` as `a` 
                    INNER JOIN costing_services_class cs ON a.costing_id = cs.id
                    INNER JOIN costing c ON cs.costing_id = c.id
                    INNER JOIN soap si ON a.soap_id = si.id
                    LEFT JOIN employee d ON a.doctor_in_charge_id = d.id
                    INNER JOIN transaction_invoice_item tii ON a.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `si`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0")->result();
                $response_data_lab = $this->db->query("
                    SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`,  `cs`.`bride_department`, `cs`.`bride_department_sub`
                    FROM `laboratory_request` as `a` 
                    INNER JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
                    INNER JOIN costing c ON b.costing_id = c.id
                    INNER JOIN costing_services_class cs ON c.id = cs.costing_id
                    INNER JOIN soap si ON a.soap_id = si.id
                    INNER JOIN transaction_invoice_item tii ON b.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `si`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
                $response_data_rad = $this->db->query("
                    SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, `a`.`date_request`, `bb`.`id` as `result`,  `bb`.`radiology_request_item_id`, `b`.`radiology_request_id`
                    FROM `radiology_request` as `a` 
                    INNER JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
                    INNER JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
                    INNER JOIN costing c ON b.costing_id = c.id
                    INNER JOIN costing_services_class cs ON c.id = cs.costing_id
                    INNER JOIN soap si ON a.soap_id = si.id
                    INNER JOIN transaction_invoice_item tii ON b.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `si`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `bb`.`is_delete` = 0 GROUP BY `bb`.`id`")->result();
                $response_data_pres = $this->db->query("
                    SELECT `c`.`name`, `a`.`no_order`, `b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
                    FROM `pharmacy_prescription` as `a` 
                    LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
                    LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
                    LEFT JOIN soap_p spi ON a.soap_p_id = spi.id
                    LEFT JOIN soap si ON spi.soap_id = si.id
                    WHERE `si`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
                    // LEFT JOIN transaction_invoice_item tii ON b.pharmacy_prescription_id = tii.transaction_id
                    // LEFT JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                $response_data_pres_two = $this->db->query("
                    SELECT `c`.`name`, `a`.`no_order`, `b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
                    FROM `pharmacy_prescription` as `a` 
                    LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
                    LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
                    WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0 AND (a.soap_p_id IS NULL OR a.soap_p_id = '') AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
                // INNER JOIN transaction_invoice_item tii ON b.pharmacy_prescription_id = tii.transaction_id
                // INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                $response_data_bed = $this->db->query("
                    SELECT `c`.`name` as `costing_name`,`a`.`is_non_prorate`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
                    FROM `transaction_bed` as `a` 
                    INNER JOIN costing c ON a.costing_id = c.id
                    INNER JOIN costing_services_class cs ON c.id = cs.costing_id
                    INNER JOIN transaction_invoice_item tii ON a.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0")->result();
                $response_data_phy = $this->db->query("
                    SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `a`.`date_request`, `b`.`physiotherapy_request_id`
                    FROM `physiotherapy_request` as `a` 
                    INNER JOIN `physiotherapy_request_item` `b` ON `a`.`id` = `b`.`physiotherapy_request_id` 
                    INNER JOIN transaction_invoice_item tii ON b.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
                $response_data_alkes = $this->db->query("
                    SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
                    FROM `transaction_alkes` as `a` 
                    INNER JOIN costing c ON a.costing_id = c.id
                    INNER JOIN costing_services_class cs ON c.id = cs.costing_id
                    INNER JOIN transaction_invoice_item tii ON a.id = tii.transaction_id
                    INNER JOIN transaction_invoice ti ON tii.transaction_invoice_id = ti.id AND `ti`.`registration_id` = '{$output[$i]->id}' AND ti.is_delete = 0
                    WHERE `a`.`registration_destination_id` = '{$output[$i]->registration_destination_id}'
                    AND `a`.`is_delete` = 0")->result();

                $sheet->setCellValue('A'.$numrow, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow, $output[$i]->sep);
                $sheet->setCellValue('E'.$numrow, $output[$i]->Unit);
                $sheet->setCellValue('F'.$numrow, $output[$i]->Unit_sub);
                $sheet->setCellValue('G'.$numrow, date('d/m/Y', strtotime($output[$i]->tgl_masuk)));
                $sheet->setCellValue('H'.$numrow, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                $numrow_detail = $numrow+1;
                $numrow++;
                
                for ($adm=0; $adm < count($response_data_adm); $adm++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_adm[$adm]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_adm[$adm]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_adm[$adm]->total);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_adm[$adm]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_adm[$adm]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_adm[$adm]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($nurse=0; $nurse < count($response_data_nurse); $nurse++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_nurse[$nurse]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_nurse[$nurse]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_nurse[$nurse]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_nurse[$nurse]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_nurse[$nurse]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_nurse[$nurse]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_nurse[$nurse]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($doctor=0; $doctor < count($response_data_doctor); $doctor++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, (strpos($response_data_doctor[$doctor]->desc, 'Intensive') !== false) ? 'Intensive Care' : $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_doctor[$doctor]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_doctor[$doctor]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_doctor[$doctor]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_doctor[$doctor]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_doctor[$doctor]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_doctor[$doctor]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_doctor[$doctor]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_doctor[$doctor]->doctor_name);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($bed=0; $bed < count($response_data_bed); $bed++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_bed[$bed]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_bed[$bed]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_bed[$bed]->is_non_prorate == 0 ? $response_data_bed[$bed]->tarif / 24 : $response_data_bed[$bed]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_bed[$bed]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, ($response_data_bed[$bed]->is_non_prorate == 0 ? $response_data_bed[$bed]->tarif / 24 : $response_data_bed[$bed]->tarif)*$response_data_bed[$bed]->qty);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_bed[$bed]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_bed[$bed]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($alkes=0; $alkes < count($response_data_alkes); $alkes++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail,  (strpos($response_data_alkes[$alkes]->desc, 'Intensive') !== false) ? 'Intensive Care' : $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_alkes[$alkes]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_alkes[$alkes]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_alkes[$alkes]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_alkes[$alkes]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_alkes[$alkes]->tarif*$response_data_alkes[$alkes]->qty);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_alkes[$alkes]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_alkes[$alkes]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($lab=0; $lab < count($response_data_lab); $lab++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_lab[$lab]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_lab[$lab]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_lab[$lab]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_lab[$lab]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_lab[$lab]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_lab[$lab]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($phy=0; $phy < count($response_data_phy); $phy++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_phy[$phy]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_phy[$phy]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_phy[$phy]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_phy[$phy]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_phy[$phy]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_phy[$phy]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_phy[$phy]->costing_id);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($rad=0; $rad < count($response_data_rad); $rad++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_rad[$rad]->desc);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_rad[$rad]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_rad[$rad]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_rad[$rad]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_rad[$rad]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, 'tet');
                    $sheet->setCellValue('P'.$numrow_detail, 'http://192.168.100.3:8080/radiology/list/process/print-expertise/'.$response_data_rad[$rad]->radiology_request_id.'/'.$response_data_rad[$rad]->radiology_request_item_id.'/'.$response_data_rad[$rad]->result);


                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($pres_one=0; $pres_one < count($response_data_pres); $pres_one++) {
                    $total = $response_data_pres[$pres_one]->tarif * $response_data_pres[$pres_one]->qty;
                    $qty = $response_data_pres[$pres_one]->qty;

                    if ($response_data_pres[$pres_one]->pharmacy_catalog_id_ref == null) {
                        if ($response_data_pres[$pres_one]->qty_retur != null) {
                            $total = ($response_data_pres[$pres_one]->qty - $response_data_pres[$pres_one]->qty_retur) * $response_data_pres[$pres_one]->tarif;
                            $qty = $response_data_pres[$pres_one]->qty - $response_data_pres[$pres_one]->qty_retur;
                        }
                    }
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, 'OBAT/ALKES');
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_pres[$pres_one]->name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_pres[$pres_one]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $qty);
                    $sheet->setCellValue('M'.$numrow_detail, $total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_pres[$pres_one]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_pres[$pres_one]->no_order);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($pres=0; $pres < count($response_data_pres_two); $pres++) {
                    $total_two = $response_data_pres_two[$pres]->tarif * $response_data_pres_two[$pres]->qty;
                    $qty = $response_data_pres_two[$pres]->qty;

                    if ($response_data_pres_two[$pres]->pharmacy_catalog_id_ref == null) {
                        if ($response_data_pres_two[$pres]->qty_retur != null) {
                            $total_two = ($response_data_pres_two[$pres]->qty - $response_data_pres_two[$pres]->qty_retur) * $response_data_pres_two[$pres]->tarif;
                            $qty = $response_data_pres_two[$pres]->qty - $response_data_pres_two[$pres]->qty_retur;
                        }
                    }
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->sep);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('F'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_masuk)));
                    $sheet->setCellValue('H'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('I'.$numrow_detail, 'OBAT/ALKES');
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_pres_two[$pres]->name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_pres_two[$pres]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $qty);
                    $sheet->setCellValue('M'.$numrow_detail, $total_two);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_pres_two[$pres]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $total_two);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_pres_two[$pres]->no_order);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
            }
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Laporan Apexa Billing");
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Apexa Billing.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
	}

    

    public function report_detail_detail_inv_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '200048M');
        ini_set('max_execution_time', 0); 
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $response_data = array();
        // // $this->$model->whereFields['a.id'] = $id; 
        // $this->$model->whereFields['a.is_delete'] = 0; 
        // $this->$model->whereFields['r.register_date >='] = '2024-09-01 00:00:00';
        // $this->$model->whereFields['r.register_dates <='] = '2024-09-07 00:00:00';
        // // $this->$model->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
        // $this->$model->whereFields['ri.is_primary'] = 1; 
        // $response_data = $this->$model->read();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $output = $this->db->query("
			SELECT `tii`.`transaction_invoice_id`, `tii`.`transaction_id`, `tii`.`type_transaction_id`, `r`.`id`, `r`.`code` as `registrasi_code`, `h2`.`name` as `insurance`, `r`.`norm`, `c`.`name` as `penjamin`, `r`.`register_date`, p.fullname as nama_pasien, `rd`.`id` as `registration_destination_id`, `hf`.`name` as Unit, `hfi`.`name` as Unit_sub, `rd`.`checkin_date` as `tgl_masuk`, `rd`.`checkout_date` as `tgl_keluar`
            FROM `transaction_invoice` as `ti` 
            INNER JOIN `transaction_invoice_item` `tii` ON `ti`.`id` = `tii`.`transaction_invoice_id` 
            LEFT JOIN `registration` `r` ON `ti`.`registration_id` = `r`.`id` 
            LEFT JOIN `registration_destination` `rd` ON `r`.`id` = `rd`.`registration_id` 
            LEFT JOIN `registration_insurance` `ri` ON `rd`.`id` = `ri`.`registration_destination_id` AND `ri`.`is_primary` = 1
            INNER JOIN patient p ON p.norm = r.norm
            LEFT JOIN helper h2 ON h2.code = ri.insurance_id AND h2.variable = 'insurance'
            LEFT JOIN company c ON ri.company_id = c.id
            INNER JOIN health_facility_item hfi ON hfi.id = rd.health_facility_item_id
            INNER JOIN health_facility hf ON hf.id = hfi.health_facility_id
            WHERE `ti`.`is_delete` = 0 AND
            `r`.`is_cancel` = 0 AND `r`.`register_date` >= '2025-09-23 00:00:00' AND `r`.`register_date` <= '2025-09-26 23:59:59' AND `ri`.`is_primary` = 1 
            ORDER BY `r`.`register_date` ASC
            ")->result();
            // `ri`.`insurance_id` = 1 AND `hf`.`id` != '444b3134-5b75-4b67-af72-a558d46716d7' AND
        // WHERE `r`.`is_cancel` = 0 AND `hf`.`id` = '444b3134-5b75-4b67-af72-a558d46716d7' AND
        $sheet->setCellValue('A2', "NORM"); 
        $sheet->setCellValue('B2', "Nama Pasien"); 
        $sheet->setCellValue('C2', "Penjamin"); 
        $sheet->setCellValue('D2', "Unit");
        $sheet->setCellValue('E2', "Poli/Ruangan"); 
        $sheet->setCellValue('F2', "Tgl.Masuk"); 
        $sheet->setCellValue('G2', "Tgl.Keluar"); 
        $sheet->setCellValue('J2', "Total Tarif"); 
        $numrow = 3; // Set baris pertama untuk isi tabel adalah baris ke 4
        for ($i=0; $i < count($output); $i++) {
            if ($output[$i]->transaction_id != null || $output[$i]->transaction_id != "") {
                if ($output[$i]->type_transaction_id == '4') {
                    $response_data_adm = $this->db->query("
                        SELECT `a`.`costing_name`,`a`.`var_1` as `total`
                        FROM `transaction_administration` as `a` 
                        WHERE `a`.`id` = '{$output[$i]->transaction_id}'")->result();
                }
                if ($output[$i]->type_transaction_id == '1') {
                    $response_data_nurse = $this->db->query("
                    SELECT `a`.`costing_name`,`a`.`var_2` as `tarif`,`a`.`var_1` as `qty`,`a`.`var_3` as `total`
                    FROM `transaction_nurse` as `a` 
                    WHERE `a`.`id` = '{$output[$i]->transaction_id}'
                    AND `a`.`is_delete` = 0")->result();
                }
                if ($output[$i]->type_transaction_id == '2') {
                    $response_data_doctor = $this->db->query("
                        SELECT `a`.`costing_name`,`a`.`var_1` as `tarif`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `d`.`fullname` as `doctor_name`
                        FROM `transaction_doctor` as `a` 
                        INNER JOIN soap si ON a.soap_id = si.id
                        INNER JOIN employee d ON a.doctor_in_charge_id = d.id
                        WHERE `a`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0")->result();
                }
                if ($output[$i]->type_transaction_id == '11') {
                    $response_data_lab = $this->db->query("
                        SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total`
                        FROM `laboratory_request` as `a` 
                        INNER JOIN `laboratory_request_item` `b` ON `a`.`id` = `b`.`laboratory_request_id` 
                        WHERE `b`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
                }
                if ($output[$i]->type_transaction_id == '12') {
                    $response_data_rad = $this->db->query("
                        SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `a`.`date_request`, `bb`.`id` as `result`,  `bb`.`radiology_request_item_id`, `b`.`radiology_request_id`
                        FROM `radiology_request` as `a` 
                        INNER JOIN `radiology_request_item` `b` ON `a`.`id` = `b`.`radiology_request_id` 
                        INNER JOIN `radiology_request_item_image` `bb` ON `b`.`id` = `bb`.`radiology_request_item_id` 
                        WHERE `b`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0 AND `a`.`status` = 2 AND `b`.`status` = 2 AND `bb`.`status` = 2 AND `b`.`is_delete` = 0 AND `bb`.`is_delete` = 0 GROUP BY `bb`.`id`")->result();
                }
                if ($output[$i]->type_transaction_id == '12') {
                    $response_data_phy = $this->db->query("
                        SELECT `b`.`costing_name`,`b`.`var_1` as `tarif`,`b`.`var_2` as `qty`,`b`.`var_1` as `total` , `a`.`date_request`, `b`.`physiotherapy_request_id`
                        FROM `physiotherapy_request` as `a` 
                        INNER JOIN `physiotherapy_request_item` `b` ON `a`.`id` = `b`.`physiotherapy_request_id` 
                        WHERE `b`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0")->result();
                }
                if ($output[$i]->type_transaction_id == '6') {
                    $response_data_pres = $this->db->query("
                        SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
                        FROM `pharmacy_prescription` as `a` 
                        LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
                        LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
                        WHERE `a`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0 AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
                    $response_data_pres_two = $this->db->query("
                        SELECT `c`.`name`,`b`.`var_1` as `tarif`,`b`.`qty` as `qty`,`b`.`qty_retur` as `qty_retur`,`b`.`var_2` as `total`,`b`.`pharmacy_catalog_id_ref` as `pharmacy_catalog_id_ref`
                        FROM `pharmacy_prescription` as `a` 
                        LEFT JOIN `pharmacy_prescription_item` `b` ON `a`.`id` = `b`.`pharmacy_prescription_id` 
                        LEFT JOIN `pharmacy_catalog` `c` ON `b`.`pharmacy_catalog_id` = `c`.`id` 
                        WHERE `a`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0 AND `a`.`soap_p_id` IS NULL AND `b`.`is_delete` = 0 AND `a`.`flag` = 1")->result();
                }
                if ($output[$i]->type_transaction_id == '10') {
                    $response_data_bed = $this->db->query("
                        SELECT `c`.`name` as `costing_name`,`a`.`is_non_prorate`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
                        FROM `transaction_bed` as `a` 
                        LEFT JOIN costing c ON a.costing_id = c.id
                        LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
                        WHERE `a`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0")->result();
                }
                if ($output[$i]->type_transaction_id == '5') {
                        $response_data_alkes = $this->db->query("
                        SELECT `c`.`name` as `costing_name`,`a`.`var_1`,`a`.`var_2` as `qty`,`a`.`var_3` as `total` , `c`.`desc`, `cs`.`costing_id`, `cs`.`bride_department`, `cs`.`bride_department_sub`, cs.var_1 as tarif
                        FROM `transaction_alkes` as `a` 
                        LEFT JOIN costing c ON a.costing_id = c.id
                        LEFT JOIN costing_services_class cs ON c.id = cs.costing_id
                        WHERE `a`.`id` = '{$output[$i]->transaction_id}'
                        AND `a`.`is_delete` = 0")->result();
                }

                $sheet->setCellValue('A'.$numrow, $output[$i]->norm);
                $sheet->setCellValue('B'.$numrow, $output[$i]->nama_pasien);
                $sheet->setCellValue('C'.$numrow, $output[$i]->insurance.' '.$output[$i]->penjamin);
                $sheet->setCellValue('D'.$numrow, $output[$i]->Unit);
                $sheet->setCellValue('E'.$numrow, $output[$i]->Unit_sub);
                $sheet->setCellValue('F'.$numrow, date('d/m/Y', strtotime($output[$i]->tgl_masuk)));
                $sheet->setCellValue('G'.$numrow, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                $numrow_detail = $numrow+1;
                $numrow++;
                
                for ($adm=0; $adm < count($response_data_adm); $adm++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_adm[$adm]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_adm[$adm]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_adm[$adm]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_adm[$adm]->total);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_adm[$adm]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_adm[$adm]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_adm[$adm]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_adm[$adm]->costing_id);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($nurse=0; $nurse < count($response_data_nurse); $nurse++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_nurse[$nurse]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_nurse[$nurse]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_nurse[$nurse]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_nurse[$nurse]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_nurse[$nurse]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_nurse[$nurse]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_nurse[$nurse]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_nurse[$nurse]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_nurse[$nurse]->costing_id);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($doctor=0; $doctor < count($response_data_doctor); $doctor++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_doctor[$doctor]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_doctor[$doctor]->bride_department_sub == '' ? $output[$i]->Unit_sub : $response_data_doctor[$doctor]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_doctor[$doctor]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_doctor[$doctor]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_doctor[$doctor]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_doctor[$doctor]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_doctor[$doctor]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_doctor[$doctor]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_doctor[$doctor]->doctor_name);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($bed=0; $bed < count($response_data_bed); $bed++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_bed[$bed]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_bed[$bed]->bride_department_sub == '' ? $output[$i]->Unit_sub : $response_data_bed[$bed]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_bed[$bed]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_bed[$bed]->is_non_prorate == 0 ? $response_data_bed[$bed]->tarif / 24 : $response_data_bed[$bed]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_bed[$bed]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, ($response_data_bed[$bed]->is_non_prorate == 0 ? $response_data_bed[$bed]->tarif / 24 : $response_data_bed[$bed]->tarif)*$response_data_bed[$bed]->qty);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_bed[$bed]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_bed[$bed]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($alkes=0; $alkes < count($response_data_alkes); $alkes++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_alkes[$alkes]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_alkes[$alkes]->bride_department_sub == '' ? $output[$i]->Unit_sub : $response_data_alkes[$alkes]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_alkes[$alkes]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_alkes[$alkes]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $response_data_alkes[$alkes]->qty);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_alkes[$alkes]->tarif*$response_data_alkes[$alkes]->qty);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_alkes[$alkes]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_alkes[$alkes]->total);
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($lab=0; $lab < count($response_data_lab); $lab++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_lab[$lab]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_lab[$lab]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_lab[$lab]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_lab[$lab]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_lab[$lab]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_lab[$lab]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_lab[$lab]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_lab[$lab]->costing_id);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($rad=0; $rad < count($response_data_rad); $rad++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_masuk)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar ? $output[$i]->tgl_keluar : $output[$i]->tgl_masuk)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_rad[$rad]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_rad[$rad]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_rad[$rad]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_rad[$rad]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_rad[$rad]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_rad[$rad]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, 'tet');
                    $sheet->setCellValue('P'.$numrow_detail, 'http://192.168.100.3:8080/radiology/list/process/print-expertise/'.$response_data_rad[$rad]->radiology_request_id.'/'.$response_data_rad[$rad]->radiology_request_item_id.'/'.$response_data_rad[$rad]->result);


                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($phy=0; $phy < count($response_data_phy); $phy++) {
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, $response_data_phy[$phy]->desc);
                    $sheet->setCellValue('I'.$numrow_detail, $response_data_phy[$phy]->bride_department_sub);
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_phy[$phy]->costing_name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_phy[$phy]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, 1);
                    $sheet->setCellValue('M'.$numrow_detail, $response_data_phy[$phy]->total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_phy[$phy]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $response_data_phy[$phy]->total);
                    $sheet->setCellValue('P'.$numrow_detail, $response_data_phy[$phy]->costing_id);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($pres=0; $pres < count($response_data_pres); $pres++) {
                    $total = $response_data_pres[$pres]->tarif * $response_data_pres[$pres]->qty;
                    $qty = $response_data_pres[$pres]->qty;

                    if ($response_data_pres[$pres]->pharmacy_catalog_id_ref == null) {
                        if ($response_data_pres[$pres]->qty_retur != null) {
                            $total = ($response_data_pres[$pres]->qty - $response_data_pres[$pres]->qty_retur) * $response_data_pres[$pres]->tarif;
                            $qty = $response_data_pres[$pres]->qty - $response_data_pres[$pres]->qty_retur;
                        }
                    }
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->register_date)));
                    $sheet->setCellValue('H'.$numrow_detail, 'OBAT/ALKES');
                    $sheet->setCellValue('I'.$numrow_detail, 'OBAT/ALKES');
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_pres[$pres]->name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_pres[$pres]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $qty);
                    $sheet->setCellValue('M'.$numrow_detail, $total);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_pres[$pres]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $total);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                for ($pres=0; $pres < count($response_data_pres_two); $pres++) {
                    $total_two = $response_data_pres_two[$pres]->tarif * $response_data_pres_two[$pres]->qty;
                    $qty = $response_data_pres_two[$pres]->qty;

                    if ($response_data_pres_two[$pres]->pharmacy_catalog_id_ref == null) {
                        if ($response_data_pres_two[$pres]->qty_retur != null) {
                            $total_two = ($response_data_pres_two[$pres]->qty - $response_data_pres_two[$pres]->qty_retur) * $response_data_pres_two[$pres]->tarif;
                            $qty = $response_data_pres_two[$pres]->qty - $response_data_pres_two[$pres]->qty_retur;
                        }
                    }
                    $sheet->setCellValue('A'.$numrow_detail, $output[$i]->norm);
                    $sheet->setCellValue('B'.$numrow_detail, $output[$i]->nama_pasien);
                    $sheet->setCellValue('C'.$numrow_detail, $output[$i]->insurance.' '.$output[$i]->penjamin);
                    $sheet->setCellValue('D'.$numrow_detail, $output[$i]->Unit);
                    $sheet->setCellValue('E'.$numrow_detail, $output[$i]->Unit_sub);
                    $sheet->setCellValue('F'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_masuk)));
                    $sheet->setCellValue('G'.$numrow_detail, date('d/m/Y', strtotime($output[$i]->tgl_keluar)));
                    $sheet->setCellValue('H'.$numrow_detail, 'OBAT/ALKES');
                    $sheet->setCellValue('I'.$numrow_detail, 'OBAT/ALKES');
                    $sheet->setCellValue('J'.$numrow_detail, $response_data_pres_two[$pres]->name);
                    $sheet->setCellValue('K'.$numrow_detail, $response_data_pres_two[$pres]->tarif);
                    $sheet->setCellValue('L'.$numrow_detail, $qty);
                    $sheet->setCellValue('M'.$numrow_detail, $total_two);
                    $sheet->setCellValue('N'.$numrow_detail, $response_data_pres_two[$pres]->date_added);
                    $sheet->setCellValue('O'.$numrow_detail, $total_two);
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
            }
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Laporan Apexa Billing");
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Apexa Billing.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
	}

    public function report_payment_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $response_data = array();
        // // $this->$model->whereFields['a.id'] = $id; 
        // $this->$model->whereFields['a.is_delete'] = 0; 
        // $this->$model->whereFields['r.register_date >='] = '2024-09-01 00:00:00';
        // $this->$model->whereFields['r.register_dates <='] = '2024-09-07 00:00:00';
        // // $this->$model->whereFields['r.register_date <='] = date('Y-m-d').' 23:59:59';
        // $this->$model->whereFields['ri.is_primary'] = 1; 
        // $response_data = $this->$model->read();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = [
            'font' => ['bold' => true], // Set font nya jadi bold
            'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = [
            'alignment' => [
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ],
            'borders' => [
            'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
            'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
            'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
            'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
            ]
        ];
        $output = $this->db->query("
			SELECT `r`.`code` as `registrasi_code`, `tr`.`code`,`r`.`norm`, `r`.`register_date`, p.date_added, pt.fullname as nama_pasien, pt.fullname as nama_pasien, pi.var_3 as total
            FROM `payment` as `p` 
            LEFT JOIN `payment_item` `pi` ON `p`.`id` = `pi`.`payment_id` 
            LEFT JOIN `transaction_invoice` `tr` ON `p`.`invoice_id` = `tr`.`id` 
            LEFT JOIN `registration` `r` ON `tr`.`registration_id` = `r`.`id` 
            LEFT JOIN `registration_destination` `rd` ON `r`.`id` = `rd`.`registration_id` 
            LEFT JOIN `patient` `pt` ON `r`.`norm` = `pt`.`norm` 
            WHERE `r`.`is_cancel` = 0 AND `pi`.`payment_method_id` = '3c476753-e0b1-497f-8a5c-c3a28a5e3901' AND
            `r`.`register_date` >= '2024-09-01 00:00:00' AND `r`.`register_date` <= '2024-09-30 20:59:59'
            ORDER BY `tr`.`code` ASC
		")->result();
        $sheet->setCellValue('A2', "INVOICE"); 
        $sheet->setCellValue('B2', "NORM"); 
        $sheet->setCellValue('C2', "Nama Pasien"); 
        $sheet->setCellValue('D2', "Metode Pembayaran"); 
        $sheet->setCellValue('E2', "Total");
        $sheet->setCellValue('F2', "Tanggal"); 
        $numrow = 3; // Set baris pertama untuk isi tabel adalah baris ke 4
        for ($i=0; $i < count($output); $i++) {
            $sheet->setCellValue('A'.$numrow, $output[$i]->code);
            $sheet->setCellValue('B'.$numrow, $output[$i]->norm);
            $sheet->setCellValue('C'.$numrow, $output[$i]->nama_pasien);
            $sheet->setCellValue('D'.$numrow, "CASH");
            $sheet->setCellValue('E'.$numrow, $output[$i]->total);
            $sheet->setCellValue('F'.$numrow, date('d/m/Y', strtotime($output[$i]->date_added)));
            $numrow_detail = $numrow+1;
            $numrow++;
        }
        
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle("Laporan Apexa Billing");
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Apexa Billing.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
	}

    public function excel_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $registration_id = '';
        if(isset($q_strings['registration_id'])){
            $registration_id = $q_strings['registration_id']; 
            unset($q_strings['registration_id']);
        }
        if($registration_id != ''){
            // $this->$model->whereFields['a.registration_id'] = $registration_id; 
        };
        $id = '';
        if(isset($q_strings['id'])){
            $id = $q_strings['id']; 
            unset($q_strings['id']);
        }
        if($id != ''){
            // $this->$model->whereFields['a.id'] = $id; 
        };
        $output = $this->$model->read();
        $response_data = array();
        for ($i=0; $i < count($output); $i++) { 
            $output[$i]->registration_data = $this->Registration->read($output[$i]->registration_id)[0];
            $dt_transaction_invoice_item = $this->Transaction_invoice_item->read($output[$i]->id);
            for ($j=0; $j < count($dt_transaction_invoice_item); $j++) { 
                $output[$i]->transactions[$j]['transaction_id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                $output[$i]->transactions[$j]['flag_status_id'] = $dt_transaction_invoice_item[$j]->flag_status_id;
                $output[$i]->transactions[$j]['type_transaction_id'] = $dt_transaction_invoice_item[$j]->type_transaction_id;
            
                // 1 Nurse
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '1') {
                    $dt_nurse = $this->Transaction_nurse->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $dt_cost = $this->Costing->read($dt_nurse[0]->costing_id);
                    $output[$i]->transactions[$j]['nurse_id'] = $dt_nurse[0]->nurse_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_nurse[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_code'] = $dt_cost[0]->code;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_nurse[0]->costing_name;
                    $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_nurse[0]->health_facility_item_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_nurse[0]->var_2;
                    $output[$i]->transactions[$j]['var_2'] = $dt_nurse[0]->var_2;
                    $output[$i]->transactions[$j]['var_3'] = $dt_nurse[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_nurse[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_nurse[0]->date_added;
                }

                // 2 Doctor
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '2') {
                    $dt_doctor = $this->Transaction_doctor->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['soap_id'] = $dt_doctor[0]->soap_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_doctor[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_doctor[0]->costing_name;
                    $this->Costing_value->whereFields['a.costing_services_class_id'] = $dt_doctor[0]->costing_id; 
                    $dt_cv = $this->Costing_value->read(0,false);
                    $output[$i]->transactions[$j]['costing_detail'] = $dt_cv;
                    $output[$i]->transactions[$j]['var_1'] = $dt_doctor[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_doctor[0]->var_2;
                    $output[$i]->transactions[$j]['var_3'] = $dt_doctor[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_doctor[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_doctor[0]->date_added;
                    $dt_cost = $this->Costing->read($dt_cv[0]->costing_id);
                    $output[$i]->transactions[$j]['costing_code'] = $dt_cost[0]->code;
                }

                // 3 Prescription
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '6') {
                    if ($dt_transaction_invoice_item[$j]->transaction_id) {
                        $dt_prescription = $this->Transaction_prescription->read($dt_transaction_invoice_item[$j]->transaction_id);
                        $output[$i]->transactions[$j]['registration_id'] = @$dt_prescription[0]->registration_id;
                        $output[$i]->transactions[$j]['var_1'] = @$dt_prescription[0]->var_1;
                        $output[$i]->transactions[$j]['date_added'] = @$dt_prescription[0]->date_added;
                    }
                }

                // 4 Administration
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '4') {
                    $dt_administration = $this->Transaction_administration->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_id'] = $dt_administration[0]->registration_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_administration[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_administration[0]->costing_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_administration[0]->var_1;
                    $output[$i]->transactions[$j]['date_added'] = $dt_administration[0]->date_added;
                }

                // 5 Alkes
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '5') {
                    $dt_alkes = $this->Transaction_alkes->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_destination_id'] = $dt_alkes[0]->registration_destination_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_alkes[0]->costing_id;
                    $output[$i]->transactions[$j]['health_facility_item_name'] = $dt_alkes[0]->health_facility_item_name;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_alkes[0]->costing_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_alkes[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_alkes[0]->var_2;
                    $output[$i]->transactions[$j]['var_3'] = $dt_alkes[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_alkes[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_alkes[0]->date_added;
                }

                // 6 BHP
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '6') {
                    $dt_bhp = $this->Transaction_bhp->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_destination_id'] = $dt_bhp[0]->registration_destination_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_bhp[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_bhp[0]->costing_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_bhp[0]->var_2;
                    $output[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_bhp[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_bhp[0]->date_added;
                }

                // 7 Blood
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '7') {
                    $dt_blood = $this->Transaction_blood->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_destination_id'] = $dt_blood[0]->registration_destination_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_blood[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_blood[0]->costing_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_blood[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_blood[0]->var_2;
                    $output[$i]->transactions[$j]['var_3'] = $dt_blood[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_blood[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_blood[0]->date_added;
                }

                // 8 MCU
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '8') {
                    $dt_mcu = $this->Transaction_mcu->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_destination_id'] = $dt_mcu[0]->registration_destination_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_mcu[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_mcu[0]->costing_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_mcu[0]->var_2;
                    $output[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_mcu[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_mcu[0]->date_added;
                }

                // 9 Physiotherapy
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '9') {
                    $dt_physiotherapy = $this->Transaction_physiotherapy->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_destination_id'] = $dt_physiotherapy[0]->registration_destination_id;
                    $output[$i]->transactions[$j]['costing_id'] = $dt_physiotherapy[0]->costing_id;
                    $output[$i]->transactions[$j]['costing_name'] = $dt_physiotherapy[0]->costing_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_physiotherapy[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_physiotherapy[0]->var_2;
                    $output[$i]->transactions[$j]['var_1'] = $dt_physiotherapy[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_physiotherapy[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_physiotherapy[0]->date_added;
                }

                // 10 Bed
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '10') {
                    $dt_bed = $this->Transaction_bed->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output[$i]->transactions[$j]['registration_destination_id'] = $dt_bed[0]->registration_destination_id;
                    $output[$i]->transactions[$j]['bed_id'] = $dt_bed[0]->bed_id;
                    $output[$i]->transactions[$j]['bed_name'] = $dt_bed[0]->bed_name;
                    $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1;
                    $output[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2;
                    $output[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_3;
                    $output[$i]->transactions[$j]['notes'] = $dt_bed[0]->notes;
                    $output[$i]->transactions[$j]['date_added'] = $dt_bed[0]->date_added;
                }
            }
            $this->Payment->whereFields['a.invoice_id'] = $id; 
            $dt_payment = $this->Payment->read();
            if ($dt_payment) {
                for ($dp=0; $dp < count($dt_payment); $dp++) { 
                    $dt_payment_item = $this->Payment_item->read($dt_payment[$dp]->id);
                    for ($j=0; $j < count($dt_payment_item); $j++) { 
                        $output[$i]->payment = $dt_payment_item;
                        $output[$i]->var_2 += $dt_payment_item[$j]->var_3;
                    }
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
        if ($output || $output == [] && $id == '0') {
            $response_data = $output;
            $error   = false;
        } else {
            $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
            return;
        }
        if ($error == true) {
            $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
        } else {
            // echo "<pre>";
            // var_dump($response_data);
            // // foreach($response_data as $data){ // Lakukan looping pada variabel 
            // //     foreach($data->transactions as $data_detail){ // Lakukan looping pada variabel 
            // //         echo "<pre>";
            // //         var_dump($data_detail['costing_name']);
            // //         var_dump($data_detail['var_1']);
            // //     }
            // // }
            // die();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
            $style_col = [
                'font' => ['bold' => true], // Set font nya jadi bold
                'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                ],
                'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
                ]
            ];
            // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
            $style_row = [
                'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
                ],
                'borders' => [
                'top' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border top dengan garis tipis
                'right' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],  // Set border right dengan garis tipis
                'bottom' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN], // Set border bottom dengan garis tipis
                'left' => ['borderStyle'  => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN] // Set border left dengan garis tipis
                ]
            ];
            // $sheet->setCellValue('A1', "DATA KARYAWAN"); // Set kolom A1 dengan tulisan "DATA "
            // $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
            // $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
            // Buat header tabel nya pada baris ke 3
            $sheet->setCellValue('A1', "HEADER"); 
            $sheet->setCellValue('B1', "No Faktur"); 
            $sheet->setCellValue('C1', "Tgl Faktur"); 
            $sheet->setCellValue('D1', "No Pelanggan");
            $sheet->setCellValue('E1', "Alamat Faktur"); 
            $sheet->setCellValue('F1', "Kena PPN"); 
            $sheet->setCellValue('G1', "Total Termasuk PPN"); 
            $sheet->setCellValue('H1', "Nomor Faktur Pajak"); 
            $sheet->setCellValue('I1', "Faktur Dimuka"); 
            $sheet->setCellValue('J1', "Diskon Faktur (%)"); 
            $sheet->setCellValue('K1', "Diskon Faktur (Rp)"); 
            $sheet->setCellValue('L1', "Keterangan"); 
            $sheet->setCellValue('M1', "Nama Cabang"); 
            $sheet->setCellValue('N1', "No PO"); 
            $sheet->setCellValue('O1', "Pengiriman"); 
            $sheet->setCellValue('P1', "Tgl Pengiriman"); 
            $sheet->setCellValue('Q1', "FOB"); 
            $sheet->setCellValue('R1', "Syarat Pembayaran"); 
            $sheet->setCellValue('S1', "Bank Pembayaran"); 
            $sheet->setCellValue('T1', "Nilai Pembayaran"); 
            $sheet->setCellValue('U1', "Kustom Karakter 1"); 
            $sheet->setCellValue('V1', "Kustom Karakter 2"); 
            $sheet->setCellValue('W1', "Kustom Karakter 3"); 
            $sheet->setCellValue('X1', "Kustom Karakter 4"); 
            $sheet->setCellValue('Y1', "Kustom Karakter 5"); 
            $sheet->setCellValue('Z1', "Kustom Karakter 6"); 
            $sheet->setCellValue('AA1', "Kustom Karakter 7"); 
            $sheet->setCellValue('AB1', "Kustom Karakter 8"); 
            $sheet->setCellValue('AC1', "Kustom Karakter 9"); 
            $sheet->setCellValue('AD1', "Kustom Karakter 10"); 
            $sheet->setCellValue('AE1', "Kustom Angka 1"); 
            $sheet->setCellValue('AF1', "Kustom Angka 2"); 
            $sheet->setCellValue('AG1', "Kustom Angka 3"); 
            $sheet->setCellValue('AH1', "Kustom Angka 4"); 
            $sheet->setCellValue('AI1', "Kustom Angka 5"); 
            $sheet->setCellValue('AJ1', "Kustom Angka 6"); 
            $sheet->setCellValue('AK1', "Kustom Angka 7"); 
            $sheet->setCellValue('AL1', "Kustom Angka 8"); 
            $sheet->setCellValue('AM1', "Kustom Angka 9"); 
            $sheet->setCellValue('AN1', "Kustom Angka 10"); 
            $sheet->setCellValue('AO1', "Kustom Tanggal 1"); 
            $sheet->setCellValue('AP1', "Kustom Tanggal 2"); 
            $sheet->setCellValue('AQ1', "Nomor VA"); 
            $sheet->setCellValue('AR1', "Nomor Akun Piutang"); 
            $sheet->setCellValue('AS1', "Pembayaran Dengan Kode Unik"); 

            $sheet->setCellValue('A2', "ITEM"); 
            $sheet->setCellValue('B2', "Kode Barang"); 
            $sheet->setCellValue('C2', "Nama Barang"); 
            $sheet->setCellValue('D2', "Kuantitas");
            $sheet->setCellValue('E2', "Satuan"); 
            $sheet->setCellValue('F2', "Harga Satuan"); 
            $sheet->setCellValue('G2', "Diskon Barang (%)"); 
            $sheet->setCellValue('H2', "Diskon Barang (Rp)"); 
            $sheet->setCellValue('I2', "Catatan Barang"); 
            $sheet->setCellValue('J2', "Nama Gudang"); 
            $sheet->setCellValue('K2', "ID Salesman"); 
            $sheet->setCellValue('L2', "Nama Dept Barang"); 
            $sheet->setCellValue('M2', "No Proyek Barang"); 
            $sheet->setCellValue('N2', "unit Bisnis"); 
            $sheet->setCellValue('O2', "Kustom Karakter 2"); 
            $sheet->setCellValue('P2', "Kustom Karakter 3"); 
            $sheet->setCellValue('Q2', "Kustom Karakter 4"); 
            $sheet->setCellValue('R2', "Kustom Karakter 5"); 
            $sheet->setCellValue('S2', "Kustom Karakter 6"); 
            $sheet->setCellValue('T2', "Kustom Karakter 7"); 
            $sheet->setCellValue('U2', "Kustom Karakter 8"); 
            $sheet->setCellValue('V2', "Kustom Karakter 9"); 
            $sheet->setCellValue('W2', "Kustom Karakter 10"); 
            $sheet->setCellValue('X2', "Kustom Karakter 11"); 
            $sheet->setCellValue('Y2', "Kustom Karakter 12"); 
            $sheet->setCellValue('Z2', "Kustom Karakter 13"); 
            $sheet->setCellValue('AA2', "Kustom Karakter 14"); 
            $sheet->setCellValue('AB2', "Kustom Karakter 15"); 
            $sheet->setCellValue('AC2', "Kustom Angka1"); 
            $sheet->setCellValue('AD2', "Kustom Angka 2"); 
            $sheet->setCellValue('AE2', "Kustom Angka 3"); 
            $sheet->setCellValue('AF2', "Kustom Angka 4"); 
            $sheet->setCellValue('AG2', "Kustom Angka 5"); 
            $sheet->setCellValue('AH2', "Kustom Angka 6"); 
            $sheet->setCellValue('AI2', "Kustom Angka 7"); 
            $sheet->setCellValue('AJ2', "Kustom Angka 8"); 
            $sheet->setCellValue('AK2', "Kustom Angka 9"); 
            $sheet->setCellValue('AL2', "Kustom Angka 10"); 
            $sheet->setCellValue('AM2', "Kustom Tanggal 1"); 
            $sheet->setCellValue('AN2', "Kustom Tanggal 2"); 
            $sheet->setCellValue('AO2', "Kategori Keuangan 1"); 
            $sheet->setCellValue('AP2', "Kategori Keuangan 2"); 
            $sheet->setCellValue('AQ2', "Kategori Keuangan 3"); 
            $sheet->setCellValue('AR2', "Kategori Keuangan 4"); 
            $sheet->setCellValue('AS2', "Kategori Keuangan 5"); 
            $sheet->setCellValue('AT2', "Kategori Keuangan 6"); 
            $sheet->setCellValue('AU2', "Kategori Keuangan 7"); 
            $sheet->setCellValue('AV2', "Kategori Keuangan 8"); 
            $sheet->setCellValue('AW2', "Kategori Keuangan 9"); 
            $sheet->setCellValue('AX2', "Kategori Keuangan 10"); 
            $sheet->setCellValue('AY2', "No. Pengiriman Pesanan"); 

            $sheet->setCellValue('A3', "EXPENSE");
            $sheet->setCellValue('B3', "No Biaya");
            $sheet->setCellValue('C3', "Nama Biaya");
            $sheet->setCellValue('D3', "Nilai Biaya");
            $sheet->setCellValue('E3', "Catatan Biaya");
            $sheet->setCellValue('F3', "Nama Dept Biaya");
            $sheet->setCellValue('G3', "No Proyek Biaya");
            $sheet->setCellValue('H3', "Kategori Keuangan 1");
            $sheet->setCellValue('I3', "Kategori Keuangan 2");
            $sheet->setCellValue('J3', "Kategori Keuangan 3");
            $sheet->setCellValue('K3', "Kategori Keuangan 4");
            $sheet->setCellValue('L3', "Kategori Keuangan 5");
            $sheet->setCellValue('M3', "Kategori Keuangan 6");
            $sheet->setCellValue('N3', "Kategori Keuangan 7");
            $sheet->setCellValue('O3', "Kategori Keuangan 8");
            $sheet->setCellValue('P3', "Kategori Keuangan 9");
            $sheet->setCellValue('Q3', "Kategori Keuangan 10");
            $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
            foreach($response_data as $data){ // Lakukan looping pada variabel 
                $sheet->setCellValue('A'.$numrow, 'HEADER');
                $sheet->setCellValue('B'.$numrow, $data->id);
                $sheet->setCellValue('C'.$numrow, date('d/m/Y', strtotime($data->date_added)));
                $sheet->setCellValue('D'.$numrow, $data->registration_data->bride_cust);
                $sheet->setCellValue('E'.$numrow, $data->registration_data->address);
                $sheet->setCellValue('F'.$numrow, 'Tidak');
                $sheet->setCellValue('G'.$numrow, 'Tidak');
                $sheet->setCellValue('H'.$numrow, '');
                $sheet->setCellValue('I'.$numrow, 'Ya');
                $sheet->setCellValue('J'.$numrow, '');
                $sheet->setCellValue('K'.$numrow, '');
                $sheet->setCellValue('L'.$numrow, 'Faktur Pasien A.N '.$data->registration_data->fullname);//Keterangan
                $sheet->setCellValue('M'.$numrow, '');
                $sheet->setCellValue('N'.$numrow, '');
                $sheet->setCellValue('O'.$numrow, '');
                $sheet->setCellValue('P'.$numrow, '');
                $sheet->setCellValue('Q'.$numrow, '');
                $sheet->setCellValue('R'.$numrow, '');
                $sheet->setCellValue('S'.$numrow, '110101');
                $sheet->setCellValue('T'.$numrow, '');
                $sheet->setCellValue('U'.$numrow, '');
                $sheet->setCellValue('V'.$numrow, '');
                $sheet->setCellValue('W'.$numrow, '');
                $sheet->setCellValue('X'.$numrow, '');
                $sheet->setCellValue('Y'.$numrow, '');
                $sheet->setCellValue('Z'.$numrow, '');
                $sheet->setCellValue('AA'.$numrow, '');
                $sheet->setCellValue('AB'.$numrow, '');
                $sheet->setCellValue('AC'.$numrow, '');
                $sheet->setCellValue('AD'.$numrow, '');
                $sheet->setCellValue('AE'.$numrow, '');
                $sheet->setCellValue('AF'.$numrow, '');
                $sheet->setCellValue('AG'.$numrow, '');
                $sheet->setCellValue('AH'.$numrow, '');
                $sheet->setCellValue('AI'.$numrow, '');
                $sheet->setCellValue('AJ'.$numrow, '');
                $sheet->setCellValue('AK'.$numrow, '');
                $sheet->setCellValue('AL'.$numrow, '');
                $sheet->setCellValue('AM'.$numrow, '');
                $sheet->setCellValue('AN'.$numrow, '');
                $sheet->setCellValue('AO'.$numrow, '');
                $sheet->setCellValue('AP'.$numrow, '');
                $sheet->setCellValue('AQ'.$numrow, '');
                $sheet->setCellValue('AR'.$numrow, '');
                $sheet->setCellValue('AS'.$numrow, '');
                $numrow_detail = $numrow+1;
                foreach($data->transactions as $data_detail){
                    $sheet->setCellValue('A'.$numrow_detail, 'ITEM');
                    $sheet->setCellValue('B'.$numrow_detail, $data_detail['costing_code']);
                    $sheet->setCellValue('C'.$numrow_detail, $data_detail['costing_name']);
                    $sheet->setCellValue('D'.$numrow_detail, 1);
                    $sheet->setCellValue('E'.$numrow_detail, 'PASIEN');
                    $sheet->setCellValue('F'.$numrow_detail, $data_detail['var_1']);
                    $sheet->setCellValue('G'.$numrow_detail, '');
                    $sheet->setCellValue('H'.$numrow_detail, '');
                    $sheet->setCellValue('I'.$numrow_detail, '');
                    $sheet->setCellValue('J'.$numrow_detail, '');
                    $sheet->setCellValue('K'.$numrow_detail, 'E.00004');
                    $sheet->setCellValue('J'.$numrow_detail, '');
                    $sheet->setCellValue('K'.$numrow_detail, '');
                    $sheet->setCellValue('L'.$numrow_detail, '');
                    $sheet->setCellValue('M'.$numrow_detail, '');
                    $sheet->setCellValue('N'.$numrow_detail, '');
                    $sheet->setCellValue('N'.$numrow_detail, '');
                    $sheet->setCellValue('O'.$numrow_detail, '');
                    $sheet->setCellValue('P'.$numrow_detail, '');
                    $sheet->setCellValue('Q'.$numrow_detail, '');
                    $sheet->setCellValue('R'.$numrow_detail, '');
                    $sheet->setCellValue('S'.$numrow_detail, '');
                    $sheet->setCellValue('T'.$numrow_detail, '');
                    $sheet->setCellValue('U'.$numrow_detail, '');
                    $sheet->setCellValue('V'.$numrow_detail, '');
                    $sheet->setCellValue('W'.$numrow_detail, '');
                    $sheet->setCellValue('X'.$numrow_detail, '');
                    $sheet->setCellValue('Y'.$numrow_detail, '');
                    $sheet->setCellValue('Z'.$numrow_detail, '');
                    $sheet->setCellValue('AA'.$numrow_detail, '');
                    $sheet->setCellValue('AB'.$numrow_detail, '');
                    $sheet->setCellValue('AC'.$numrow_detail, '');
                    $sheet->setCellValue('AD'.$numrow_detail, '');
                    $sheet->setCellValue('AE'.$numrow_detail, '');
                    $sheet->setCellValue('AF'.$numrow_detail, '');
                    $sheet->setCellValue('AG'.$numrow_detail, '');
                    $sheet->setCellValue('AH'.$numrow_detail, '');
                    $sheet->setCellValue('AI'.$numrow_detail, '');
                    $sheet->setCellValue('AJ'.$numrow_detail, '');
                    $sheet->setCellValue('AK'.$numrow_detail, '');
                    $sheet->setCellValue('AL'.$numrow_detail, '');
                    $sheet->setCellValue('AM'.$numrow_detail, '');
                    $sheet->setCellValue('AN'.$numrow_detail, '');
                    $sheet->setCellValue('AO'.$numrow_detail, 'Poliklinik');
                    $numrow_detail++; // Tambah 1 setiap kali looping
                    $numrow++;
                }
                $numrow++;
            }
            $sheet->getDefaultRowDimension()->setRowHeight(-1);
            $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->setTitle("Import");
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Import.xlsx"'); // Set nama file excel nya
            header('Cache-Control: max-age=0');
            $writer->save('php://output');
            // $this->REST_Return(200, 'SUCCESS', $response_data);
        }
        return;
	}

    public function list_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
        //     return;
        // }

        $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
        if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
        if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);

        if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
            $date_start = $q_strings['date_start'] . ' 00:00:00';
            $date_end = $q_strings['date_end'] . ' 23:59:59';
            $this->$model->whereFields['a.date_added >='] = $date_start;
            $this->$model->whereFields['a.date_added <='] = $date_end;

            unset($q_strings['date_start']);
            unset($q_strings['date_end']);
        } 
        
        $this->$model->whereFields['a.is_delete'] = 0;
        $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
        $output = $this->$model->read_ssr($id);
        $response_data = array();
        
        for ($i=0; $i < count($output->rows); $i++) { 

            if ($output->rows[$i]->registration_id) {
                $select = 'a.id, 
                ri.number_insurance, 
                h2.name as insurance_name, 
                c.name as company_name, 
                c.insurance_type_id, 
                a.norm, 
                ri.sep, 
                hf.name as health_facility_name, 
                rd.health_facility_item_id, 
                hfi.name as health_facility_item_name, 
                b.name as bed_name, 
                br.name as room_name';
                $join = array(
                    array('registration_destination rd', 'a.id = rd.registration_id', 'INNER'),
                    array('registration_insurance ri', 'rd.id = ri.registration_destination_id AND ri.is_primary=1', 'INNER'),
                    array('health_facility_item hfi', 'rd.health_facility_item_id = hfi.id', 'INNER'),
                    array('health_facility hf', 'hfi.health_facility_id = hf.id', 'INNER'),
                    array('bed b', 'rd.bed_id = b.id', 'LEFT'),
                    array('room br', 'b.room_id = br.id', 'LEFT'),
                    array('helper h2', "ri.insurance_id = h2.code and h2.variable='insurance'", 'INNER'),
                    array('company c', "ri.company_id = c.id", 'LEFT'),
                );
                $where = array(
                    'a.id' => $output->rows[$i]->registration_id
                );
                $output_data_patient = $this->Registration->select(0, $select, $where, $join);
    
                $output->rows[$i]->data_patient = $output_data_patient;
            }
            $dt_transaction_invoice_item = array();
            $this->Transaction_invoice_item->whereFields['a.is_delete'] = 0;
            
            $dt_transaction_invoice_item = $this->Transaction_invoice_item->read($output->rows[$i]->id, false);
            $this->Transaction_invoice_item->whereFields = array();
            for ($j=0; $j < count($dt_transaction_invoice_item); $j++) { 
                $output->rows[$i]->transactions[$j]['transaction_id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                $output->rows[$i]->transactions[$j]['flag_status_id'] = $dt_transaction_invoice_item[$j]->flag_status_id;
                $output->rows[$i]->transactions[$j]['type_transaction_id'] = $dt_transaction_invoice_item[$j]->type_transaction_id;
            
                // 1 Nurse
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '1') {
                    $this->Transaction_nurse->whereFields['d.id'] = $output->rows[$i]->registration_id; 
                    $dt_nurse = $this->Transaction_nurse->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output->rows[$i]->transactions[$j]['nurse_id'] = $dt_nurse[0]->nurse_id;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_nurse[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_nurse[0]->costing_name;
                    $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_nurse[0]->health_facility_item_name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_nurse[0]->var_1;
                    $output->rows[$i]->transactions[$j]['var_2'] = $dt_nurse[0]->var_2;
                    $output->rows[$i]->transactions[$j]['var_3'] = $dt_nurse[0]->var_3;
                    $output->rows[$i]->transactions[$j]['notes'] = $dt_nurse[0]->notes;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_nurse[0]->date_added;
                    if ($dt_nurse[0]->costing_id) {
                        $this->Costing_services_class_inv->whereFields = array();
                        $this->Costing_services_class_inv->whereFields['a.costing_id'] = $dt_nurse[0]->costing_id; 
                        $dt_cost = $this->Costing_services_class_inv->read(0,false);
                        $output->rows[$i]->transactions[$j]['type_costing_id'] = $dt_cost[0]->type_costing_id;
                        $output->rows[$i]->transactions[$j]['classification_one_type_id'] = $dt_cost[0]->classification_one_type_id;
                        $output->rows[$i]->transactions[$j]['classification_two_type_id'] = $dt_cost[0]->classification_two_type_id;
                        $output->rows[$i]->transactions[$j]['classification_three_type_id'] = $dt_cost[0]->classification_three_type_id;
                        $output->rows[$i]->transactions[$j]['type_costing_name'] = $dt_cost[0]->type_costing_name;
                        $output->rows[$i]->transactions[$j]['classification_one_type_name'] = $dt_cost[0]->classification_one_type_name;
                        $output->rows[$i]->transactions[$j]['classification_two_type_name'] = $dt_cost[0]->classification_two_type_name;
                        $output->rows[$i]->transactions[$j]['classification_three_type_name'] = $dt_cost[0]->classification_three_type_name;
                    }
                }

                // 2 Doctor
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '2') {
                    $this->Transaction_doctor->whereFields['d.id'] = $output->rows[$i]->registration_id; 
                    $dt_doctor = $this->Transaction_doctor->read($dt_transaction_invoice_item[$j]->transaction_id);
                    if ($dt_doctor[0]->costing_id) {

                        $output->rows[$i]->transactions[$j]['soap_id'] = $dt_doctor[0]->soap_id;
                        $output->rows[$i]->transactions[$j]['costing_id'] = $dt_doctor[0]->costing_id;
                        $output->rows[$i]->transactions[$j]['costing_name'] = $dt_doctor[0]->costing_name;
                        $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_doctor[0]->health_facility_item_name;
                        $output->rows[$i]->transactions[$j]['var_1'] = $dt_doctor[0]->var_1;
                        $output->rows[$i]->transactions[$j]['var_2'] = $dt_doctor[0]->var_2;
                        $output->rows[$i]->transactions[$j]['var_3'] = $dt_doctor[0]->var_3;
                        if ($dt_doctor[0]->disc_id == 1) {
                            $output->rows[$i]->transactions[$j]['var_4'] = ($dt_doctor[0]->var_1*$dt_doctor[0]->var_4)/100;
                            // $output->rows[$i]->transactions[$j]['var_4'] = ($dt_doctor[0]->var_1-($dt_doctor[0]->var_1*$dt_doctor[0]->var_4)/100)*$dt_doctor[0]->var_2;
                        } else if ($dt_doctor[0]->disc_id == 2) {
                            $output->rows[$i]->transactions[$j]['var_4'] = $dt_doctor[0]->var_4;
                        } else {
                            $output->rows[$i]->transactions[$j]['var_4'] = 0;
                        }
                        $output->rows[$i]->transactions[$j]['disc_id'] = $dt_doctor[0]->disc_id;
                        $output->rows[$i]->transactions[$j]['notes'] = $dt_doctor[0]->notes;
                        $output->rows[$i]->transactions[$j]['doctor_in_charge_name'] = $dt_doctor[0]->doctor_in_charge_name;
                        $output->rows[$i]->transactions[$j]['date_added'] = $dt_doctor[0]->date_added;
                        if ($dt_doctor[0]->costing_id) {
                            $this->Costing_services_class_inv->whereFields = array();
                            $this->Costing_services_class_inv->whereFields['a.id'] = $dt_doctor[0]->costing_id; 
                            $dt_cost = $this->Costing_services_class_inv->read(0,false);
                            $output->rows[$i]->transactions[$j]['type_costing_id'] = $dt_cost[0]->type_costing_id;
                            $output->rows[$i]->transactions[$j]['classification_one_type_id'] = $dt_cost[0]->classification_one_type_id;
                            $output->rows[$i]->transactions[$j]['classification_two_type_id'] = $dt_cost[0]->classification_two_type_id;
                            $output->rows[$i]->transactions[$j]['classification_three_type_id'] = $dt_cost[0]->classification_three_type_id;
                            $output->rows[$i]->transactions[$j]['type_costing_name'] = $dt_cost[0]->type_costing_name;
                            $output->rows[$i]->transactions[$j]['classification_one_type_name'] = $dt_cost[0]->classification_one_type_name;
                            $output->rows[$i]->transactions[$j]['classification_two_type_name'] = $dt_cost[0]->classification_two_type_name;
                            $output->rows[$i]->transactions[$j]['classification_three_type_name'] = $dt_cost[0]->classification_three_type_name;
                        }
                    }
                }

                // 14 Prescription
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '14') {
                    if ($dt_transaction_invoice_item[$j]->transaction_id) {
                        $dt_presc = $this->Transaction_prescription->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                        $this->Transaction_prescription_item->whereFields['a.is_delete'] = 0; 
                        $dt_presc_item = $this->Transaction_prescription_item->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                        for ($pi=0; $pi < count($dt_presc_item); $pi++) { 
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_prescription_id'] = @$dt_presc_item[$pi]->pharmacy_prescription_id;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_name'] = @$dt_presc_item[$pi]->pharmacy_catalog_name;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['qty'] = @$dt_presc_item[$pi]->qty;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['qty_retur'] = @$dt_presc_item[$pi]->qty_retur;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['is_retur_full'] = @$dt_presc_item[$pi]->is_retur_full;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_id_retur'] = @$dt_presc_item[$pi]->pharmacy_catalog_id_retur;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['pharmacy_catalog_id_ref'] = @$dt_presc_item[$pi]->pharmacy_catalog_id_ref;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['var_1'] = @$dt_presc_item[$pi]->var_1;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['var_2'] = @$dt_presc_item[$pi]->var_2;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['var_3'] = @$dt_presc_item[$pi]->var_3;
                            $output->rows[$i]->transactions[$j]['prescription_item'][$pi]['var_4'] = @$dt_presc_item[$pi]->var_4;
                        }
                        $output->rows[$i]->transactions[$j]['fullname'] = @$dt_presc[0]->fullname;
                        $output->rows[$i]->transactions[$j]['var_1'] = @$dt_presc[0]->var_1;
                        $output->rows[$i]->transactions[$j]['var_2'] = @$dt_presc[0]->var_2;
                        $output->rows[$i]->transactions[$j]['var_3'] = @$dt_presc[0]->var_3;
                        $output->rows[$i]->transactions[$j]['date_added'] = @$dt_presc[0]->date_added;
                    }
                }

                // 4 Administration
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '13') {
                    $dt_administration = $this->Transaction_administration->read($dt_transaction_invoice_item[$j]->transaction_id,false);
                    $this->Costing_services_class_inv->whereFields = array();
                    $this->Costing_services_class->whereFields['a.costing_id'] = $dt_administration[0]->costing_id; 
                    $dt_cost = $this->Costing_services_class->read(0,false);
                    $output->rows[$i]->transactions[$j]['registration_id'] = $dt_administration[0]->registration_id;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_administration[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_cost[0]->name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_administration[0]->var_1;
                    if ($dt_administration[0]->disc_id == 1) {
                        $output->rows[$i]->transactions[$j]['var_4'] = ($dt_administration[0]->var_1*$dt_administration[0]->var_4)/100;
                        // $output->rows[$i]->transactions[$j]['var_4'] = ($dt_administration[0]->var_1-($dt_administration[0]->var_1*$dt_administration[0]->var_4)/100)*$dt_administration[0]->var_2;
                    } else if ($dt_administration[0]->disc_id == 2) {
                        $output->rows[$i]->transactions[$j]['var_4'] = $dt_administration[0]->var_4;
                    } else {
                        $output->rows[$i]->transactions[$j]['var_4'] = 0;
                    }
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_administration[0]->date_added;
                }

                // Laboratorium
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '11') {
                    $this->Laboratory_request_item->whereFields = array();
                    $this->Laboratory_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                    $dt_lab = $this->Laboratory_request_item->read(0,false);
                    $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_lab[0]->health_facility_item_name;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_lab[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_lab[0]->costing_name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_lab[0]->var_1;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_lab[0]->date_added;
                }

                // Radiology
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '12') {
                    $this->Radiology_request_item->whereFields = array();
                    $this->Radiology_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                    $dt_rad = $this->Radiology_request_item->read(0,false);
                    $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_rad[0]->health_facility_item_name;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_rad[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_rad[0]->costing_name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_rad[0]->var_1;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_rad[0]->date_added;
                }

                // 5 Alkes
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '5') {
                    $dt_alkes = $this->Transaction_alkes->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output->rows[$i]->transactions[$j]['registration_destination_id'] = $dt_alkes[0]->registration_destination_id;
                    $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_alkes[0]->health_facility_item_name;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_alkes[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_alkes[0]->costing_name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_alkes[0]->var_1;
                    $output->rows[$i]->transactions[$j]['var_2'] = $dt_alkes[0]->var_2;
                    $output->rows[$i]->transactions[$j]['var_3'] = $dt_alkes[0]->var_3;
                    $output->rows[$i]->transactions[$j]['notes'] = $dt_alkes[0]->notes;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_alkes[0]->date_added;
                }

                // 6 BHP
                // if($dt_transaction_invoice_item[$j]->type_transaction_id == '6') {
                //     $dt_bhp = $this->Transaction_bhp->read($dt_transaction_invoice_item[$j]->transaction_id);
                //     $output->rows[$i]->transactions[$j]['registration_destination_id'] = $dt_bhp[0]->registration_destination_id;
                //     $output->rows[$i]->transactions[$j]['costing_id'] = $dt_bhp[0]->costing_id;
                //     $output->rows[$i]->transactions[$j]['costing_name'] = $dt_bhp[0]->costing_name;
                //     $output->rows[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_1;
                //     $output->rows[$i]->transactions[$j]['var_2'] = $dt_bhp[0]->var_2;
                //     $output->rows[$i]->transactions[$j]['var_1'] = $dt_bhp[0]->var_3;
                //     $output->rows[$i]->transactions[$j]['notes'] = $dt_bhp[0]->notes;
                //     $output->rows[$i]->transactions[$j]['date_added'] = $dt_bhp[0]->date_added;
                // }

                // 7 Blood
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '7') {
                    $dt_blood = $this->Transaction_blood->read($dt_transaction_invoice_item[$j]->transaction_id);
                    $output->rows[$i]->transactions[$j]['registration_destination_id'] = $dt_blood[0]->registration_destination_id;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_blood[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_blood[0]->costing_name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_blood[0]->var_1;
                    $output->rows[$i]->transactions[$j]['var_2'] = $dt_blood[0]->var_2;
                    $output->rows[$i]->transactions[$j]['var_3'] = $dt_blood[0]->var_3;
                    $output->rows[$i]->transactions[$j]['notes'] = $dt_blood[0]->notes;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_blood[0]->date_added;
                }

                // 8 MCU
                // if($dt_transaction_invoice_item[$j]->type_transaction_id == '8') {
                //     $dt_mcu = $this->Transaction_mcu->read($dt_transaction_invoice_item[$j]->transaction_id);
                //     $output->rows[$i]->transactions[$j]['registration_destination_id'] = $dt_mcu[0]->registration_destination_id;
                //     $output->rows[$i]->transactions[$j]['costing_id'] = $dt_mcu[0]->costing_id;
                //     $output->rows[$i]->transactions[$j]['costing_name'] = $dt_mcu[0]->costing_name;
                //     $output->rows[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_1;
                //     $output->rows[$i]->transactions[$j]['var_2'] = $dt_mcu[0]->var_2;
                //     $output->rows[$i]->transactions[$j]['var_1'] = $dt_mcu[0]->var_3;
                //     $output->rows[$i]->transactions[$j]['notes'] = $dt_mcu[0]->notes;
                //     $output->rows[$i]->transactions[$j]['date_added'] = $dt_mcu[0]->date_added;
                // }

                // 9 Physiotherapy
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '15') {
                    $this->Physiotherapy_request_item->whereFields = array();
                    $this->Physiotherapy_request_item->whereFields['a.id'] = $dt_transaction_invoice_item[$j]->transaction_id;
                    $dt_physiotherapy = $this->Physiotherapy_request_item->read(0,false);
                    $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_physiotherapy[0]->health_facility_item_name;
                    $output->rows[$i]->transactions[$j]['costing_id'] = $dt_physiotherapy[0]->costing_id;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_physiotherapy[0]->costing_name;
                    $output->rows[$i]->transactions[$j]['var_1'] = $dt_physiotherapy[0]->var_1;
                    $output->rows[$i]->transactions[$j]['notes'] = $dt_physiotherapy[0]->notes;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_physiotherapy[0]->date_added;
                }

                // 10 Bed
                if($dt_transaction_invoice_item[$j]->type_transaction_id == '10') {
                    $dt_bed = $this->Transaction_bed->read($dt_transaction_invoice_item[$j]->transaction_id);
                    
                    // Calculate costs
                        $checkin   = $dt_bed[0]->checkin_date;
                        $checkout  = $dt_bed[0]->checkout_date;
                        $start     = new DateTime($checkin);
                        $end       = new DateTime($checkout);
                        $output->rows[$i]->transactions[$j]['checkin_date']  = $checkin;
                        $output->rows[$i]->transactions[$j]['checkout_date'] = $checkout;

                        // Hitung selisih waktu
                        $diff = $end->diff($start);

                        // Ambil selisih hari dasar
                        $totalDays = $diff->days;

                        // Jika ada sisa jam/menit, tambahkan 1 hari
                        if ($diff->h > 0 || $diff->i > 0) {
                            $totalDays += 1;
                        }

                        // Jika total hari 0, minimal tetap 1 hari
                        if ($totalDays == 0) {
                            $totalDays = 1;
                        }

                        // Hitung total harga
                        $cost_prorate = $dt_bed[0]->is_non_prorate == 0 ? $dt_bed[0]->var_1 : $dt_bed[0]->var_1;
                        $var_2        = $cost_prorate * $totalDays;

                        $output->rows[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2 == null && $dt_bed[0]->var_2 == '' && $dt_bed[0]->var_2 == 0 ? $totalDays : $dt_bed[0]->var_2;
                        $output->rows[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1 == null && $dt_bed[0]->var_1 == '' && $dt_bed[0]->var_1 == 0 ? $cost_prorate : $cost_prorate;
                        $output->rows[$i]->transactions[$j]['cost_prorate'] = $cost_prorate;
                    // Calculate costs

                    $output->rows[$i]->transactions[$j]['registration_destination_id'] = $dt_bed[0]->registration_destination_id;
                    $output->rows[$i]->transactions[$j]['bed_id'] = $dt_bed[0]->bed_id;
                    $output->rows[$i]->transactions[$j]['bed_name'] = $dt_bed[0]->bed_name;
                    $output->rows[$i]->transactions[$j]['costing_name'] = $dt_bed[0]->costing_name;
                    // $output->rows[$i]->transactions[$j]['var_1'] = $dt_bed[0]->var_1;
                    // $output->rows[$i]->transactions[$j]['var_2'] = $dt_bed[0]->var_2;
                    $output->rows[$i]->transactions[$j]['var_3'] = $dt_bed[0]->var_3;
                    $output->rows[$i]->transactions[$j]['notes'] = $dt_bed[0]->notes;
                    $output->rows[$i]->transactions[$j]['date_added'] = $dt_bed[0]->date_added;
                    $output->rows[$i]->transactions[$j]['health_facility_item_name'] = $dt_bed[0]->health_facility_item_name;
                }
            }
            $this->Payment->whereFields['a.invoice_id'] = $output->rows[$i]->id; 
            $this->Payment->whereFields['a.is_delete'] = 0; 
            $dt_payment = $this->Payment->read(0,false);
            if ($dt_payment) {
                for ($dp=0; $dp < count($dt_payment); $dp++) { 
                    $this->Payment_item->whereFields['p.is_delete'] = 0; 
                    $dt_payment_item = $this->Payment_item->read($dt_payment[$dp]->id);
                    for ($x=0; $x < count($dt_payment_item); $x++) { 
                        $output->rows[$i]->payment = $dt_payment_item;
                        $output->rows[$i]->var_2 += $dt_payment_item[$x]->var_3;
                    }
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
                if($this->$model->read($id,false) != []) {
                    $dt_inv = array(
                        'id'                   => $id,
                        'user_deleted'         => $user_id,
                        'date_deleted'         => date('Y-m-d H:i:s'),
                        'is_delete'            => 1,
                        'reason'               => $dt_put['reason']
                    );
                    $this->$model->replace($dt_inv);

                    $this->Transaction_invoice_item->whereFields['transaction_invoice_id'] = $id;
                    $dt_item = $this->Transaction_invoice_item->read(0,false);
                    // Cancel registration_destination
                    for ($i=0; $i < count($dt_item); $i++) { 
                        $dt_inv_item = array(
                            'transaction_invoice_id'    => $dt_item[$i]->transaction_invoice_id,
                            'transaction_id'            => $dt_item[$i]->transaction_id,
                            'flag_status_id'            => $dt_item[$i]->flag_status_id,
                            'is_delete'                 => 1
                        );
                        $this->Transaction_invoice_item->replace($dt_inv_item);
                        $dt_administration = $this->Transaction_administration->read($dt_item[$i]->transaction_id,false);
                        if ($dt_administration[0]->costing_id) {
                            if ($dt_administration[0]->costing_id == '413a9aa3-dfdc-4e97-b01e-efd44773d229') {
                                $this->Transaction_administration->replace(array(
                                    'id' => $dt_administration[0]->id,
                                    'var_1' => 0,
                                ));
                            }
                        }
                    }

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

    public function check_transaction_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $registration_id = $q_strings['registration_id'];
        // Token Validation
        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            if (!$registration_id) {
                $this->REST_Return(404, 'there is no id ' . $registration_id);
                return;
            }

            $select = 'a.*,'; 
            $join = array(            
                array('transaction_invoice ti', 'a.transaction_invoice_id = ti.id', 'LEFT'),
            );
            $where = array();
            $where['ti.is_delete'] = 0;
            $where['ti.registration_id'] = $registration_id;
            $response_data = $this->Transaction_invoice_item->select(0, $select, $where, $join);
            $this->REST_Return(200, 'SUCCESS', $response_data);
            return;
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }

    }

    public function bpjs_tk_get($p = 0) {
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

        $registration_id = $q_strings['registration_id'] ?? null;

        if ($registration_id == null) {
            $this->REST_Return(400, 'registration_id is required');
            return;
        }


        $output = [];

        $select = 'a.id, a.costing_name, a.var_1, 1 as type_id, "tr_admin" as type';
        $where = array(
            'a.registration_id' => $registration_id
        );
        $tr_administration = $this->Transaction_administration->select(0, $select, $where);

        $select = 'a.id, a.costing_name, a.var_1, a.var_2, a.var_3, 2 as type_id, "tr_doctor" as type';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('soap s', 'a.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
        );
        $tr_doctor = $this->Transaction_doctor->select(0, $select, $where, $join);

        $select = 'a.id, a.costing_name, a.var_1, a.var_2, a.var_3, 3 as type_id, "tr_nurse" as type';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('nurse n', 'a.nurse_id = n.id', 'INNER'),
            array('registration_destination rd', 'n.registration_destination_id = rd.id', 'INNER'),
        );
        $tr_nurse = $this->Transaction_nurse->select(0, $select, $where, $join);

        $select = 'a.id, a.costing_name, a.var_1, 4 as type_id, "lab_item" as type';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('laboratory_request lr', 'a.laboratory_request_id = lr.id', 'INNER'),
            array('soap s', 'lr.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
        );
        $lab_req_item = $this->Laboratory_request_item->select(0, $select, $where, $join);

        $select = 'a.id, a.costing_name, a.var_1, 5 as type_id, "rad_item" as type';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('radiology_request rr', 'a.radiology_request_id = rr.id', 'INNER'),
            array('soap s', 'rr.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
        );
        $rad_req_item = $this->Radiology_request_item->select(0, $select, $where, $join);

        $select = 'a.id, a.costing_name, a.var_1, 6 as type_id, "phy_item" as type';
        $where = array(
            'rd.registration_id' => $registration_id,
            'a.is_delete' => 0,
        );
        $join = array(
            array('physiotherapy_request pr', 'a.physiotherapy_request_id = pr.id', 'INNER'),
            array('soap s', 'pr.soap_id = s.id', 'INNER'),
            array('registration_destination rd', 's.registration_destination_id = rd.id', 'INNER'),
        );
        $phy_req_item = $this->Physiotherapy_request_item->select(0, $select, $where, $join);

        $output = array_merge($output, $tr_administration, $tr_doctor, $tr_nurse, $lab_req_item, $rad_req_item, $phy_req_item);

        $response_data = $output;

        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}