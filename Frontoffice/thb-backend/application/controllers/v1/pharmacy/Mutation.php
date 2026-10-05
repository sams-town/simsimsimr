<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Mutation extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_model_item", "MutationItem");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("v1/setting/Transaction_signature_validation_model", "TransactionSignatureValidation");
        $this->load->model("v1/setting/Signature_validation_model", "SignatureValidation");
        $this->load->model("$this->directory/Pharmacy_assets_model", "PharmacyAssets");
        $this->load->model("$this->directory/Pharmacy_assets_card_model", "PharmacyAssetsCard");
        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
    }

    /**
     * Planning API
     * --------------------
     * @param: pharmacy_depo_id
     * @param: type_doc_id
     * @param: no_document
     * @param: ntotal
     * @param: ndiscount
     * @param: nmargins
     * @param: nfinish
     * @param: date
     * --------------------
     * @method : POST
     * @link: version/mr/planning
     **/

     public function create_post() 
    {
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
            $this->form_validation->set_rules('pharmacy_depo_send_id', 'pharmacy_depo_send_id' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_depo_recive_id', 'pharmacy_depo_recive_id','trim|required|max_length[36]');
            $this->form_validation->set_rules('type_doc_id', 'type_doc_id', 'trim|required');
            $this->form_validation->set_rules('date', 'date' , 'trim|required');
            
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                $model = $this->name;
                $this->db->trans_start();
                $codecounter = array(
                    'var_1' => 'MUT',
                    'var_2' => date('Y'),
                    'var_3' => date('m'),
                    'var_5' => date('Y-m')
                );
                $this->load->library("transaction");
                $checkcounter = $this->transaction->counter($codecounter);
                $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 4, "0", STR_PAD_LEFT);
                if ($checkcounter['counter_queue'] == '0') {
                    $this->Counter_queue->replace(array(
                        'var_1' => 'MUT',
                        'var_2' => date('Y'),
                        'var_3' => date('m'),
                        'var_5' => date('Y-m'),
                        'counter_queue' => 0
                    ));
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 4, "0", STR_PAD_LEFT);
                }
                $dt_mutation = array(
                    'no_document'           => $code,
                    'pharmacy_depo_send_id' => $dt_post['pharmacy_depo_send_id'], 
                    'pharmacy_depo_recive_id' => $dt_post['pharmacy_depo_recive_id'],
                    'type_doc_id' => $dt_post['type_doc_id'],
                    'notes' => $dt_post['notes'],
                    'date' => $dt_post['date'],
                    'user_added' => $user_id,
                    'validation_key_1'=> $dt_post['validation_key_1'] != "" ? $dt_post['validation_key_1'] : NULL,
                    'validation_key_1_date'=> $dt_post['validation_key_1_date'] != "" ? date('Y-m-d H:i:s') : NULL,
                    'flag_verification' => $dt_post['validation_key_1'] != "" ? 1 : 0,
                    'date_added' => date('Y-m-d H:i:s',time()),
                );
                $id = $this->$model->create($dt_mutation);

                for ($i= 0; $i < count($dt_post['mutation_items']); $i ++){
                    $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_send_id'];
                    $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_post['mutation_items'][$i]['pharmacy_catalog_id'];
                    $c_PharmacyAssetFrom = $this->PharmacyAssets->read(0,false)[0];
                    // check amount depo_from 
                    $f_physical_a = $c_PharmacyAssetFrom->physical_amount; 
                    $f_admin_a = $c_PharmacyAssetFrom->admin_amount;

                    // if ($f_physical_a < $dt_post['mutation_items'][$i]['value_recive'] || $f_admin_a < $dt_post['mutation_items'][$i]['value_recive']) {
                    //     $this->REST_Return(400, 'Oops, something went wrong, Insufficient quantity requested.');
                    //     return;
                    // }
                    
                    $dt_mutation_item = array(
                        'pharmacy_mutation_id' => $id, 
                        'pharmacy_catalog_id' => $dt_post['mutation_items'][$i]['pharmacy_catalog_id'], 
                        'package_id' => $dt_post['mutation_items'][$i]['package_id'], 
                        'batch' => (string) $dt_post['mutation_items'][$i]['batch'],
                        'expired_date' => $dt_post['mutation_items'][$i]['expired_date'],
                        'value_recive' => $dt_post['mutation_items'][$i]['value_recive'], 
                        'value_send' => $dt_post['mutation_items'][$i]['value_send'],
                        'user_updated' => $user_id,
                        'date_updated' => date('Y-m-d H:i:s',time())
                    );

                    $this->MutationItem->replace($dt_mutation_item);

                }
                    
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $id
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    private function base64ToFile ($data = 0, $id = 0) {
        define('UPLOAD_DIR', 'assets/document/usg/');
        $data         = base64_decode($data);
        $file_name    = 'image_vkey_prc_'.$id; // You can change it to anything
        $file         = UPLOAD_DIR . $file_name . '.png';
        $success      = file_put_contents($file, $data);
        return $file;
    }

    public function update_put($p = 0)
    {
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
            $this->form_validation->set_rules('pharmacy_depo_send_id', 'pharmacy_depo_send_id' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_depo_recive_id', 'pharmacy_depo_recive_id','trim|required|max_length[36]');
            $this->form_validation->set_rules('type_doc_id', 'type_doc_id', 'trim|required');
            $this->form_validation->set_rules('date', 'date' , 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check pharmacy_depo_id
                $model = $this->name;
                $this->db->trans_start();
                $dt_mutation = array(
                    'id' => $id,
                    'pharmacy_depo_send_id' => $dt_put['pharmacy_depo_send_id'], 
                    'pharmacy_depo_recive_id' => $dt_put['pharmacy_depo_recive_id'],
                    'type_doc_id' => $dt_put['type_doc_id'],
                    'date' => $dt_put['date'],
                    'notes' => $dt_put['notes'],
                    // 'validation_key_1'=> isset($dt_put['validation_key_1']) ? $dt_put['validation_key_1'] : $dt_put['validation_key_1'],
                    // 'validation_key_2'=> isset($dt_put['validation_key_2']) ? $dt_put['validation_key_2'] : $dt_put['validation_key_2'],
                    'validation_key_2'=> $dt_put['validation_key_2'] != "" ? $dt_put['validation_key_2'] : NULL,
                    'validation_key_3'=> $dt_put['validation_key_3'] != "" ? $dt_put['validation_key_3'] : NULL,
                    'validation_key_2_date'=> $dt_put['validation_key_2'] != "" ? date('Y-m-d H:i:s',time()) : NULL,
                    'validation_key_3_date'=> $dt_put['validation_key_3'] != "" ? date('Y-m-d H:i:s',time()) : NULL,
                    'flag_verification' => $dt_put['validation_key_2'] != "" ? 2 : 0,
                    'flag_verification' => $dt_put['validation_key_3'] != "" ? 3 : ($dt_put['validation_key_2'] != "" ? 2 : 0),
                    'user_updated' => $user_id,
                    'date_updated' => date('Y-m-d H:i:s',time()),
                );
                $flag_verr = $dt_put['validation_key_3'] != "" ? 3 : ($dt_put['validation_key_2'] != "" ? 2 : 0);
                // var_dump($dt_mutation);die();
                $this->$model->replace($dt_mutation);

                for ($i= 0; $i < count($dt_put['mutation_items']); $i ++){
                    $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_recive_id'];
                    $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_put['mutation_items'][$i]['pharmacy_catalog_id'];
                    $c_PharmacyAssetRecive = $this->PharmacyAssets->read(0,false)[0];

                    $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_send_id'];
                    $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_put['mutation_items'][$i]['pharmacy_catalog_id'];
                    $c_PharmacyAssetSend = $this->PharmacyAssets->read(0,false)[0];

                    // check amount recive 
                    $admin_recive = $c_PharmacyAssetRecive->admin_amount;
                    
                    // check amount send 
                    $admin_send = $c_PharmacyAssetSend->admin_amount; 

                    // $hna = str_replace(",", ".", str_replace(".", "", $dt_put['mutation_items'][$i]['hna_item']));
                    // $hp = str_replace(",", ".", str_replace(".", "", $dt_put['mutation_items'][$i]['hp_item'])) * 0.1;
                    
                    if ($flag_verr != 3) {
                        if ($admin_send < $dt_put['mutation_items'][$i]['value_recive'] || $admin_send < $dt_put['mutation_items'][$i]['value_recive']) {
                            $this->REST_Return(400, 'Oops, something went wrong, Insufficient quantity requested.');
                            return;
                        }
                    }
                    
                    $dt_mutation_item = array(
                        'pharmacy_mutation_id' => $id, 
                        'pharmacy_catalog_id' => $dt_put['mutation_items'][$i]['pharmacy_catalog_id'], 
                        'package_id' => $dt_put['mutation_items'][$i]['package_id'], 
                        'expired_date' => $dt_put['mutation_items'][$i]['expired_date'],
                        'batch' => (string)  $dt_put['mutation_items'][$i]['batch'],
                        'value_recive' => $dt_put['mutation_items'][$i]['value_recive'], 
                        'value_send' => $dt_put['mutation_items'][$i]['value_send'],
                        'user_updated' => $user_id,
                        'date_updated' => date('Y-m-d H:i:s',time())
                    );

                    $this->MutationItem->replace($dt_mutation_item);

                    if ($dt_put['validation_key_1'] != "" && $dt_put['validation_key_2'] != "" && $dt_put['validation_key_3'] == ""){
                        //OBAT KELUAR                          
                        $dt_from = array(
                            'pharmacy_depo_id' => $dt_put['pharmacy_depo_send_id'],
                            'pharmacy_catalog_id' => $dt_put['mutation_items'][$i]['pharmacy_catalog_id'],
                            'batch' => (string)  $dt_put['mutation_items'][$i]['batch'],
                            'expired_date' => $dt_put['mutation_items'][$i]['expired_date'],
                            'prev_amount' => $admin_send,
                            'physical_amount' => $admin_send - $dt_put['mutation_items'][$i]['value_recive'],
                            'admin_amount' => $admin_send - $dt_put['mutation_items'][$i]['value_recive'],
                            'date_updated' => date('Y-m-d H:i:s',time())
                        );
                        $ca_dt_from = $this->PharmacyAssets->replace($dt_from);

                        if ($ca_dt_from == true){
                            $dt_pharmacy_asset_card = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_send_id'],
                                'pharmacy_catalog_id' => $dt_put['mutation_items'][$i]['pharmacy_catalog_id'],
                                'prev_amount' => $admin_send,
                                'in_amount' => 0,
                                'out_amount' => $dt_put['mutation_items'][$i]['value_recive'],
                                'current_amount' => $admin_send - $dt_put['mutation_items'][$i]['value_recive'],
                                'flag' => 4,
                                'batch' => (string)  $dt_put['mutation_items'][$i]['batch'],
                                'expired_date' => $dt_put['mutation_items'][$i]['expired_date'],
                                'reff_id' => $id,
                                'date_created' => date('Y-m-d H:i:s',time()),
                            );
                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                        }
                    }
                    if ($dt_put['validation_key_1'] != "" && $dt_put['validation_key_2'] != "" && $dt_put['validation_key_3'] != ""){
                        //OBAT MASUK                          
                        $dt_dst = array(
                            'pharmacy_depo_id' => $dt_put['pharmacy_depo_recive_id'],
                            'pharmacy_catalog_id' => $dt_put['mutation_items'][$i]['pharmacy_catalog_id'],
                            'batch' => (string)  $dt_put['mutation_items'][$i]['batch'],
                            'expired_date' => $dt_put['mutation_items'][$i]['expired_date'],
                            'prev_amount' => $admin_recive,
                            'physical_amount' => $admin_recive + $dt_put['mutation_items'][$i]['value_recive'],
                            'admin_amount' => $admin_recive + $dt_put['mutation_items'][$i]['value_recive'],
                            'date_updated' => date('Y-m-d H:i:s',time())
                        );

                        $ca_dt_dst = $this->PharmacyAssets->replace($dt_dst);
                        if ($ca_dt_dst == true){
                            $dt_pharmacy_asset_card = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_recive_id'],
                                'pharmacy_catalog_id' => $dt_put['mutation_items'][$i]['pharmacy_catalog_id'],
                                'prev_amount' => $admin_recive,
                                'in_amount' => $dt_put['mutation_items'][$i]['value_recive'],
                                'out_amount' => 0,
                                'current_amount' => $admin_recive + $dt_put['mutation_items'][$i]['value_recive'],
                                'flag' => 4,
                                'batch' => (string)  $dt_put['mutation_items'][$i]['batch'],
                                'expired_date' => $dt_put['mutation_items'][$i]['expired_date'],
                                'reff_id' => $id,
                                'date_created' => date('Y-m-d H:i:s',time()),
                            );
                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                        }
                    }
                }   
                /* End Transaction */
                $this->db->trans_complete();
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
                } else {
                    $response_data = array(
                        'id'     => $id
                    );
                    $this->REST_Return(201, 'SUCCESS', $response_data);
                }
            }
        } else {
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $is_valid_token['message'] ,$response_data);
            return;
        }
    }

    public function detail_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields = $q_strings;
            $output = $this->$model->read($id);
            $response_data = array();
            // code here
            for($i = 0; $i< count($output); $i++){
                $this->MutationItem->whereFields['pharmacy_mutation_id'] = $output[$i]->id;
                $mutationItems = $this->MutationItem->read(0, false);
                for ($j = 0; $j < count($mutationItems); $j++){
                    $output[$i]->mutation_items[$j]['pharmacy_mutation_id'] = $mutationItems[$j]->pharmacy_mutation_id; 
                    $output[$i]->mutation_items[$j]['pharmacy_catalog_id'] = $mutationItems[$j]->pharmacy_catalog_id;
                    $output[$i]->mutation_items[$j]['package_id'] = $mutationItems[$j]->package_id;
                    $output[$i]->mutation_items[$j]['expired_date'] = $mutationItems[$j]->expired_date;
                    $output[$i]->mutation_items[$j]['batch'] = (string) $mutationItems[$j]->batch;
                    $output[$i]->mutation_items[$j]['value_recive'] = $mutationItems[$j]->value_recive;
                    $output[$i]->mutation_items[$j]['value_send'] = $mutationItems[$j]->value_send;
                    $output[$i]->mutation_items[$j]['hna_item'] = $mutationItems[$j]->hna_item;
                    $output[$i]->mutation_items[$j]['hp_item'] = $mutationItems[$j]->hp_item;
                    $output[$i]->mutation_items[$j]['notes'] = $mutationItems[$j]->notes;
                    $output[$i]->mutation_items[$j]['catalog_name'] = $mutationItems[$j]->catalog_name;
                    $output[$i]->mutation_items[$j]['package_name'] = $mutationItems[$j]->package_name;
                    $output[$i]->mutation_items[$j]['user_updated'] = $mutationItems[$j]->user_updated;
                    $output[$i]->mutation_items[$j]['user_name'] = $mutationItems[$j]->user_name;
                    $output[$i]->mutation_items[$j]['date_updated'] = $mutationItems[$j]->date_updated;
                }
            }
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }
    public function list_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            // if(isset($q_strings['module_id'])) $this->SignatureValidation->whereFields['module_id'] = $q_strings['module_id']; unset($q_strings['module_id']);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $q_strings['is_delete'] = '0';
            if ($q_strings != []){
                $this->$model->wherefields['a.is_delete'] = $q_strings['is_delete'];
                $output = $this->$model->filter_data($q_strings,true);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();

            if (empty($output)){
                $this->REST_Return(404, 'Data not found!');
                return;
            }
            for($i = 0; $i< count($output->rows); $i++){
                $this->MutationItem->whereFields['pharmacy_mutation_id'] = $output->rows[$i]->id;
                $mutationItems = $this->MutationItem->read(0, false);
                for ($j = 0; $j < count($mutationItems); $j++){
                    // SEARCH LAST STOCK
                    $where_s['a.pharmacy_depo_id'] = $output->rows[$i]->pharmacy_depo_send_id;
                    $where_s['a.pharmacy_catalog_id'] = $mutationItems[$j]->pharmacy_catalog_id;
                    $select_s = 'a.admin_amount';
                    $join_s = array();
                    $order_s = array();
                    $stock_data = $this->PharmacyAssets->select(0, $select_s, $where_s, $join_s, $order_s)[0];
                    // SEARCH LAST STOCK
                    $output->rows[$i]->supplier = $supplier_data;
                    $output->rows[$i]->mutation_items[$j]['pharmacy_mutation_id'] = $mutationItems[$j]->pharmacy_mutation_id; 
                    $output->rows[$i]->mutation_items[$j]['pharmacy_catalog_id'] = $mutationItems[$j]->pharmacy_catalog_id;
                    $output->rows[$i]->mutation_items[$j]['package_id'] = $mutationItems[$j]->package_id;
                    $output->rows[$i]->mutation_items[$j]['expired_date'] = $mutationItems[$j]->expired_date;
                    $output->rows[$i]->mutation_items[$j]['batch'] = (string) $mutationItems[$j]->batch;
                    $output->rows[$i]->mutation_items[$j]['value_recive'] = $mutationItems[$j]->value_recive;
                    $output->rows[$i]->mutation_items[$j]['value_send'] = $mutationItems[$j]->value_send;
                    $output->rows[$i]->mutation_items[$j]['hna_item'] = $mutationItems[$j]->hna_item;
                    $output->rows[$i]->mutation_items[$j]['hp_item'] = $mutationItems[$j]->hp_item;
                    $output->rows[$i]->mutation_items[$j]['stock'] = $stock_data->admin_amount;
                    $output->rows[$i]->mutation_items[$j]['notes'] = $mutationItems[$j]->notes;
                    $output->rows[$i]->mutation_items[$j]['catalog_name'] = $mutationItems[$j]->catalog_name;
                    $output->rows[$i]->mutation_items[$j]['package_name'] = $mutationItems[$j]->package_name;
                    $output->rows[$i]->mutation_items[$j]['user_updated'] = $mutationItems[$j]->user_updated;
                    $output->rows[$i]->mutation_items[$j]['user_name'] = $mutationItems[$j]->user_name;
                    $output->rows[$i]->mutation_items[$j]['date_updated'] = $mutationItems[$j]->date_updated;
                }
                // $signatureValidation = $this->SignatureValidation->read()[0];
                //     if ($signatureValidation){
                //         $this->TransactionSignatureValidation->whereFields['signature_validation_id'] = $signatureValidation->id;
                //         $getTransactionVal = $this->TransactionSignatureValidation->read();
                //         if ($getTransactionVal){
                //             for ($k = 0; $k < count($getTransactionVal); $k++){
                //                 $output->rows[$i]->transaction_signature_validation[$k]['id'] = $getTransactionVal[$k]->id;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['signature_validation_id'] = $getTransactionVal[$k]->signature_validation_id;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['group_id'] = $getTransactionVal[$k]->group_id;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['level_validation'] = $getTransactionVal[$k]->level_validation;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['user_id'] = $getTransactionVal[$k]->user_id;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['status'] = $getTransactionVal[$k]->status;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['group_name'] = $getTransactionVal[$k]->group_name;
                //                 $output->rows[$i]->transaction_signature_validation[$k]['user_name'] = $getTransactionVal[$k]->user_name;
                //             }
                //         }
                //     }
            }
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }
}