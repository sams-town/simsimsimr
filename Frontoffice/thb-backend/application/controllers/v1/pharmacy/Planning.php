<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Planning extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "{$this->name}_item");
        $this->load->model("$this->directory/Depo_model", "Depo");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("$this->directory/Pharmacy_assets_model", "Pharmacy_assets");
        $this->load->model("$this->directory/Purchase_order_model_item", "Purchase_order_item");
        $this->load->model("$this->directory/Receipt_model_item", "Receipt_item");
        $this->load->model("v1/third_party/Brand_model", "Brand");
        $this->load->model("v1/third_party/Company_model", "Company");
        $this->load->model("v1/setting/Package_model", "Package");
        $this->load->model("v1/setting/Transaction_signature_validation_model", "TransactionSignatureValidation");
        $this->load->model("v1/setting/Signature_validation_model", "SignatureValidation");

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
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('type_doc_id', 'type_doc_id', 'trim|required|max_length[4]');
            $this->form_validation->set_rules('ntotal', 'ntotal', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('ndiscount', 'ndiscount', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('nmargins', 'nmargins', 'trim|max_length[13]');
            $this->form_validation->set_rules('nfinish', 'nfinish', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('date', 'date', 'trim|required');
            $this->form_validation->set_rules('planning_items[]', 'planning_items', 'trim|required');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check pharmacy_depo_id
                if($this->Depo->read($dt_post['pharmacy_depo_id'], false) != []) {
                    $model = $this->name;
                    /* Start Transaction */
                    $this->db->trans_start();
                    $codecounter = array(
                        'var_1' => 'PRC',
                        'var_2' => date('Y'),
                        'var_3' => date('m'),
                        'var_5' => date('Y-m')
                    );
                    $this->load->library("transaction");
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 4, "0", STR_PAD_LEFT);
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => 'PRC',
                            'var_2' => date('Y'),
                            'var_3' => date('m'),
                            'var_5' => date('Y-m'),
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 4, "0", STR_PAD_LEFT);
                    }
                    $dt_planning = array(
                        'institute_branch_id'   => $institute_branch_id,
                        'pharmacy_depo_id'      => $dt_post['pharmacy_depo_id'],
                        'type_doc_id'           => $dt_post['type_doc_id'],
                        'no_document'           => $code,
                        'ntotal'                => str_replace(",", ".", str_replace(".", "", $dt_post['ntotal'])),
                        'ndiscount'             => str_replace(",", ".", str_replace(".", "", $dt_post['ndiscount'])),
                        'nmargins'              => str_replace(",", ".", str_replace(".", "", $dt_post['nmargins'])),
                        'nfinish'               => str_replace(",", ".", str_replace(".", "", $dt_post['nfinish'])),
                        'date'                  => $dt_post['date'],
                        'validation_key_1'      => isset($dt_post['validation_key_1']) ? $this->base64ToFile($dt_post['validation_key_1'],$code) : '',
                        'user_added'            => $user_id,
                        'date_added'            => date('Y-m-d H:i:s',time()),
                    );
                    // var_dump($dt_planning);
                    // die();
                    $id = $this->$model->create($dt_planning);

                    // INIT_PHARMACY_PLANNING_ITEM
                    for ($i=0; $i < count($dt_post['planning_items']); $i++) { 
                        $package = $this->Package->read($dt_post['planning_items'][$i]['package_id'],false);
                        $package_big = $this->Package->read($dt_post['planning_items'][$i]['package_big_id'],false);
                        // Check pharmacy_catalog_id & package_id
                        if($this->Catalog->read($dt_post['planning_items'][$i]['pharmacy_catalog_id'],false) != [] && $package != []) {
                            $dt_planning_items = array(
                                'pharmacy_planning_id'  => $id,
                                'pharmacy_catalog_id'   => $dt_post['planning_items'][$i]['pharmacy_catalog_id'],
                                'package_id'            => $dt_post['planning_items'][$i]['package_id'],
                                'package_name'          => $package[0]->name,
                                'package_big_id'        => $dt_post['planning_items'][$i]['package_big_id'],
                                'package_big_name'      => $package_big[0]->name,
                                'value_package'         => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['value_package'])),
                                'min'                   => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['min'])),
                                'admin_amount'          => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['admin_amount'])),
                                'sum_item'              => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['sum_item'])),
                                'sum_package'           => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['sum_package'])),
                                'price_item'            => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['price_item'])),
                                'price_package'         => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['price_package'])),
                                'discount_item'         => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['discount_item'])),
                                'discount_price'        => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['discount_price'])),
                                'sort'                  => $dt_post['planning_items'][$i]['sort'],
                                'on_prc'                => $dt_post['planning_items'][$i]['on_prc'],
                                'on_po'                 => $dt_post['planning_items'][$i]['on_po'],
                                'on_pm'                 => $dt_post['planning_items'][$i]['on_pm']
                            );
                            $this->Planning_item->replace($dt_planning_items);
                        } else {
                            $this->REST_Return(404, 'There is no pharmacy_catalog_id with id ' . $dt_post['planning_items'][$i]['pharmacy_catalog_id'] . ' or package_id ' . $dt_post['planning_items'][$i]['package_id']);
                            return;
                        }
                    }
                    // save to transaction signature validation 
                    for ($j= 0; $j < count ($dt_post['transaction_signature_val']); $j++) {
                        $dt_transaction_signature_val = array(
                            'signature_validation_id' => $dt_post['transaction_signature_val'][$j]['signature_validation_id'],
                            'group_id' => $dt_post['transaction_signature_val'][$j]['group_id'],
                            'level_validation' => $dt_post['transaction_signature_val'][$j]['level_validation'],
                            'user_id' => $user_id,
                            'status' => $dt_post['transaction_signature_val'][$j]['status'],
                            'user_added' => $user_id,
                            'date_added' => date('Y-m-d H:i:s',time()),
                        );
                        $this->TransactionSignatureValidation->create($dt_transaction_signature_val);
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
                    $this->REST_Return(404, 'Wrong pharmacy_depo_id!');
                }
                return;     
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

    /**
     * Planning API
     * --------------------
     * @param: ntotal
     * @param: ndiscount
     * @param: nmargins
     * @param: nfinish
     * @param: date
     * --------------------
     * @method : POST
     * @link: version/mr/planning
     **/

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
            $this->form_validation->set_rules('ntotal', 'ntotal', 'trim|max_length[13]');
            $this->form_validation->set_rules('ndiscount', 'ndiscount', 'trim|max_length[13]');
            $this->form_validation->set_rules('nmargins', 'nmargins', 'trim|max_length[13]');
            $this->form_validation->set_rules('nfinish', 'nfinish', 'trim|max_length[13]');
            $this->form_validation->set_rules('date', 'date', 'trim');
            $this->form_validation->set_rules('planning_items[]', 'planning_items', 'trim');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                $this->$model->whereFields['a.is_delete'] = 0;
                $planning = $this->$model->read($id,false);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                if($planning != []) {
                    $dt_planning = array(
                        'id'              => $id,
                        'ntotal'          => isset($dt_put['ntotal']) ? str_replace(",", ".", str_replace(".", "", $dt_put['ntotal'])) : $planning[0]->ntotal,
                        'ndiscount'       => isset($dt_put['ndiscount']) ? str_replace(",", ".", str_replace(".", "", $dt_put['ndiscount'])) : $planning[0]->ndiscount,
                        'nmargins'        => isset($dt_put['nmargins']) ? str_replace(",", ".", str_replace(".", "", $dt_put['nmargins'])) : $planning[0]->nmargins,
                        'nfinish'         => isset($dt_put['nfinish']) ? str_replace(",", ".", str_replace(".", "", $dt_put['nfinish'])) : $planning[0]->nfinish,
                        'validation_key_1'=> isset($dt_put['validation_key_1']) ? $this->base64ToFile($dt_put['validation_key_1'],$planning[0]->no_document) : $planning[0]->validation_key_1,
                        'date'            => isset($dt_put['date']) ? $dt_put['date'] : $planning[0]->date,
                    );
                    $this->$model->replace($dt_planning);
                    // UPDATE_PHARMACY_PLANNING_ITEM
                    for ($i=0; $i < count($dt_put['planning_items']); $i++) { 
                        $package = $this->Package->read($dt_put['planning_items'][$i]['package_id'],false);

                        $this->Planning_item->whereFields['pharmacy_planning_id'] = $dt_put['planning_items'][$i]['pharmacy_planning_id'];
                        $this->Planning_item->whereFields['pharmacy_catalog_id'] = $dt_put['planning_items'][$i]['pharmacy_catalog_id'];
                        $planning_item = $this->Planning_item->read(0,false);
                        
                        // Check planning_item
                        if ($planning_item == []){ $this->REST_Return(404, 'There is no package_id ' . $dt_put['planning_items'][$i]['package_id']); return;}
                        // Check package_id
                        if($package != []) {
                            $dt_planning_items = array(
                                'pharmacy_planning_id'  => $dt_put['planning_items'][$i]['pharmacy_planning_id'],
                                'pharmacy_catalog_id'   => $dt_put['planning_items'][$i]['pharmacy_catalog_id'],
                                'package_id'            => $dt_put['planning_items'][$i]['package_id'],
                                'package_big_id'        => $dt_put['planning_items'][$i]['package_big_id'],
                                'package_name_big'      => $dt_put['planning_items'][$i]['package_name_big'],
                                'package_name'          => $dt_put['planning_items'][$i]['package_name'],
                                'value_package'         => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['value_package'])),
                                'sum_item'              => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['sum_item'])),
                                'min'                   => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['min'])),
                                'admin_amount'          => str_replace(",", ".", str_replace(".", "", $dt_post['planning_items'][$i]['admin_amount'])),
                                'sum_package'           => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['sum_package'])),
                                'price_item'            => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['price_item'])),
                                'price_package'         => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['price_package'])),
                                'discount_item'         => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['discount_item'])),
                                'discount_price'        => str_replace(",", ".", str_replace(".", "", $dt_put['planning_items'][$i]['discount_price'])),
                                'sort'                  => $dt_put['planning_items'][$i]['sort'],
                                'on_prc'                => $dt_put['planning_items'][$i]['on_prc'],
                                'on_po'                 => $dt_put['planning_items'][$i]['on_po'],
                                'on_pm'                 => $dt_put['planning_items'][$i]['on_pm']
                            );
                            $this->Planning_item->replace($dt_planning_items);
                        } else {
                            $this->REST_Return(404, 'There is no package_id ' . $dt_put['planning_items'][$i]['package_id']);
                            return;
                        }
                    }
                    // update transaction signature validation 
                    // for ($j= 0; $j < count ($dt_put['transaction_signature_val']); $j++) 
                    // {
                    //     $dt_transaction_signature_val = array(
                    //         'id' => $dt_put['transaction_signature_val'][$j]['id'],
                    //         'signature_validation_id' => $dt_put['transaction_signature_val'][$j]['signature_validation_id'],
                    //         'group_id' => $dt_put['transaction_signature_val'][$j]['group_id'],
                    //         'level_validation' => $dt_put['transaction_signature_val'][$j]['level_validation'],
                    //         'user_id' => $user_id,
                    //         'status' => $dt_put['transaction_signature_val'][$j]['status'],
                    //         'user_updated' => $user_id,
                    //         'date_updated' => date('Y-m-d H:i:s',time()),
                    //     );
                    //     $this->TransactionSignatureValidation->replace($dt_transaction_signature_val);
                    // }
                } else {
                    $this->REST_Return(404, 'Planning with id ' . $id . ' does not exist!');
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
            $this->$model->whereLike = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = 0;
            $output = $this->$model->read_ssr();
            $response_data = array();
            if (!empty($output->rows)){
                for ($i = 0; $i < count($output->rows); $i++) {
                    $planning_items = $this->Planning_item->read($output->rows[$i]->id);
                    if ($planning_items) {
                        for ($j = 0; $j < count($planning_items); $j++){
                            $catalog = $this->Catalog->read($planning_items[$j]->pharmacy_catalog_id);
                            $output->rows[$i]->planning_items[$j]['pharmacy_planning_id'] = $planning_items[$j]->pharmacy_planning_id;
                            $output->rows[$i]->planning_items[$j]['pharmacy_catalog_id'] = $planning_items[$j]->pharmacy_catalog_id;
                            $output->rows[$i]->planning_items[$j]['code'] = $catalog[0]->code;
                            $output->rows[$i]->planning_items[$j]['supplier_id'] = $catalog[0]->suppliers_id;
                            $output->rows[$i]->planning_items[$j]['pharmacy_catalog_name'] = $catalog[0]->name;
                            $output->rows[$i]->planning_items[$j]['package_id'] = $planning_items[$j]->package_id;
                            $output->rows[$i]->planning_items[$j]['package_name'] = $planning_items[$j]->package_name;
                            $output->rows[$i]->planning_items[$j]['package_big_id'] = $planning_items[$j]->package_big_id;
                            $output->rows[$i]->planning_items[$j]['package_big_name'] = $planning_items[$j]->package_big_name;
                            $output->rows[$i]->planning_items[$j]['value_package'] = $planning_items[$j]->value_package;
                            $output->rows[$i]->planning_items[$j]['sum_item'] = $planning_items[$j]->sum_item;
                            $output->rows[$i]->planning_items[$j]['admin_amount'] = $planning_items[$j]->admin_amount;
                            $output->rows[$i]->planning_items[$j]['min'] = $planning_items[$j]->min;
                            $output->rows[$i]->planning_items[$j]['sum_package'] = $planning_items[$j]->sum_package;
                            $output->rows[$i]->planning_items[$j]['price_item'] = $planning_items[$j]->price_item;
                            $output->rows[$i]->planning_items[$j]['price_package'] = $planning_items[$j]->price_package;
                            $output->rows[$i]->planning_items[$j]['discount_item'] = $planning_items[$j]->discount_item;
                            $output->rows[$i]->planning_items[$j]['discount_price'] = $planning_items[$j]->discount_price;
                            $output->rows[$i]->planning_items[$j]['sort'] = $planning_items[$j]->sort;
                            $output->rows[$i]->planning_items[$j]['package_isi'] = $planning_items[$j]->package_isi;
                            $output->rows[$i]->planning_items[$j]['on_prc'] = $planning_items[$j]->on_prc;
                            $output->rows[$i]->planning_items[$j]['on_po'] = $planning_items[$j]->on_po;
                            $output->rows[$i]->planning_items[$j]['on_pm'] = $planning_items[$j]->on_pm;
                        }
                    }
                    $signatureValidation = $this->SignatureValidation->read()[0];
                    if ($signatureValidation){
                        $this->TransactionSignatureValidation->whereFields['signature_validation_id'] = $signatureValidation->id;
                        $getTransactionVal = $this->TransactionSignatureValidation->read();
                        if ($getTransactionVal){
                            for ($k = 0; $k < count($getTransactionVal); $k++){
                                $output->rows[$i]->transaction_signature_validation[$k]['id'] = $getTransactionVal[$k]->id;
                                $output->rows[$i]->transaction_signature_validation[$k]['signature_validation_id'] = $getTransactionVal[$k]->signature_validation_id;
                                $output->rows[$i]->transaction_signature_validation[$k]['group_id'] = $getTransactionVal[$k]->group_id;
                                $output->rows[$i]->transaction_signature_validation[$k]['level_validation'] = $getTransactionVal[$k]->level_validation;
                                $output->rows[$i]->transaction_signature_validation[$k]['user_id'] = $getTransactionVal[$k]->user_id;
                                $output->rows[$i]->transaction_signature_validation[$k]['status'] = $getTransactionVal[$k]->status;
                                $output->rows[$i]->transaction_signature_validation[$k]['group_name'] = $getTransactionVal[$k]->group_name;
                                $output->rows[$i]->transaction_signature_validation[$k]['user_name'] = $getTransactionVal[$k]->user_name;
                            }
                        }
                    }
                }
            }
            if(!empty($output) && $id != 0){
                $response_data = $output;
                $error = false;
            }else if (!empty($output) && $id == 0){
                $response_data = $output;   
                $error = false;
            }else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            // $response_data = $getTransactionVal;
            if ($error){
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }else{
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    public function refs_get($p = 0)
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
            $this->$model->whereLike = $q_strings;
            $this->$model->whereFields['a.institute_branch_id'] = $institute_branch_id;
            $this->$model->whereFields['a.is_delete'] = 0;
            $output = $this->$model->read_ssr();
            $response_data = array();
            if (!empty($output->rows)){
                for ($i = 0; $i < count($output->rows); $i++) {
                    $planning_items = $this->Planning_item->read($output->rows[$i]->id);
                    if ($planning_items) {
                        for ($j = 0; $j < count($planning_items); $j++){
                            $catalog = $this->Catalog->read($planning_items[$j]->pharmacy_catalog_id);
                            $output->rows[$i]->planning_items[$j]['pharmacy_planning_id'] = $planning_items[$j]->pharmacy_planning_id;
                            $output->rows[$i]->planning_items[$j]['pharmacy_catalog_id'] = $planning_items[$j]->pharmacy_catalog_id;
                            $output->rows[$i]->planning_items[$j]['code'] = $catalog[0]->code;
                            $output->rows[$i]->planning_items[$j]['supplier_id'] = $catalog[0]->suppliers_id;
                            $output->rows[$i]->planning_items[$j]['pharmacy_catalog_name'] = $catalog[0]->name;
                            $output->rows[$i]->planning_items[$j]['package_id'] = $planning_items[$j]->package_id;
                            $output->rows[$i]->planning_items[$j]['package_big_id'] = $planning_items[$j]->package_big_id;
                            $output->rows[$i]->planning_items[$j]['package_big_name'] = $planning_items[$j]->package_big_name;
                            $output->rows[$i]->planning_items[$j]['package_name'] = $planning_items[$j]->package_name;
                            $output->rows[$i]->planning_items[$j]['value_package'] = $planning_items[$j]->value_package;
                            $output->rows[$i]->planning_items[$j]['sum_item'] = $planning_items[$j]->sum_item;
                            $output->rows[$i]->planning_items[$j]['admin_amount'] = $planning_items[$j]->admin_amount;
                            $output->rows[$i]->planning_items[$j]['min'] = $planning_items[$j]->min;
                            $output->rows[$i]->planning_items[$j]['sum_package'] = $planning_items[$j]->sum_package;
                            $output->rows[$i]->planning_items[$j]['price_item'] = $planning_items[$j]->price_item;
                            $output->rows[$i]->planning_items[$j]['price_package'] = $planning_items[$j]->price_package;
                            $output->rows[$i]->planning_items[$j]['discount_item'] = $planning_items[$j]->discount_item;
                            $output->rows[$i]->planning_items[$j]['discount_price'] = $planning_items[$j]->discount_price;
                            $output->rows[$i]->planning_items[$j]['sort'] = $planning_items[$j]->sort;
                            $output->rows[$i]->planning_items[$j]['package_isi'] = $planning_items[$j]->package_isi;
                            $output->rows[$i]->planning_items[$j]['on_prc'] = $planning_items[$j]->on_prc;
                            $output->rows[$i]->planning_items[$j]['on_po'] = $planning_items[$j]->on_po;
                            $output->rows[$i]->planning_items[$j]['on_pm'] = $planning_items[$j]->on_pm;
                        }
                    }
                }
            }
            if(!empty($output) && $id != 0){
                $response_data = $output;
                $error = false;
            }else if (!empty($output) && $id == 0){
                $response_data = $output;   
                $error = false;
            }else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            // $response_data = $getTransactionVal;
            if ($error){
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }else{
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
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
            $this->$model->whereFields['a.is_delete'] = 0;
            $output = $this->$model->read($id);
            $response_data = array();
            for ($i = 0; $i < count($output); $i++) {
                $planning_items = $this->Planning_item->read($output[$i]->id);
                for ($j = 0; $j < count($planning_items); $j++){
                    $output[$i]->planning_items[$j]['pharmacy_planning_id'] = $planning_items[$j]->pharmacy_planning_id;
                    $output[$i]->planning_items[$j]['pharmacy_catalog_id'] = $planning_items[$j]->pharmacy_catalog_id;
                    $output[$i]->planning_items[$j]['package_id'] = $planning_items[$j]->package_id;
                    $output[$i]->planning_items[$j]['package_name'] = $planning_items[$j]->package_name;
                    $output[$i]->planning_items[$j]['value_package'] = $planning_items[$j]->value_package;
                    $output[$i]->planning_items[$j]['sum_item'] = $planning_items[$j]->sum_item;
                    $output[$i]->planning_items[$j]['sum_package'] = $planning_items[$j]->sum_package;
                    $output[$i]->planning_items[$j]['admin_amount'] = $planning_items[$j]->admin_amount;
                    $output[$i]->planning_items[$j]['min'] = $planning_items[$j]->min;
                    $output[$i]->planning_items[$j]['price_item'] = $planning_items[$j]->price_item;
                    $output[$i]->planning_items[$j]['price_package'] = $planning_items[$j]->price_package;
                    $output[$i]->planning_items[$j]['discount_item'] = $planning_items[$j]->discount_item;
                    $output[$i]->planning_items[$j]['discount_price'] = $planning_items[$j]->discount_price;
                    $output[$i]->planning_items[$j]['sort'] = $planning_items[$j]->sort;
                    $output[$i]->planning_items[$j]['package_isi'] = $planning_items[$j]->package_isi;
                    $output[$i]->planning_items[$j]['on_prc'] = $planning_items[$j]->on_prc;
                    $output[$i]->planning_items[$j]['on_po'] = $planning_items[$j]->on_po;
                    $output[$i]->planning_items[$j]['on_pm'] = $planning_items[$j]->on_pm;
                }
            }
            if(!empty($output) && $id != 0){
                $response_data = $output;
                $error = false;
            }else if (!empty($output) && $id == 0){
                $response_data = $output;   
                $error = false;
            }else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error){
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }else{
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    public function low_depo_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Pharmacy_assets";

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            // $this->$model->whereFields['a.pharmacy_depo_id'] = $id;
            // $output = $this->$model->read(0,false);

            // $where['a.pharmacy_depo_id'] = $id;
            // $where['r.is_deleted'] = 0;
            // $where['c.min > '] = '0';
            // $select = 'a.pharmacy_catalog_id,
            // c.value_package,c.package_id,c.package_big_id,c.value_package_big,a.admin_amount,
            // b.name as depo_name,c.name as catalog_name,c.price,c.min,c.max,p.name as package_name,d.name as package_big_name, 
            // e.name as suppliers_master_name,f.name as factory_name,er.name as suppliers_name,ri.receipt_id,er.account_alias, count(r.supplier_id) as price_point,ri.price_item,ri.discount_item,ri.discount_price';
            // $join = array(
            //     array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
            //     array('pharmacy_catalog c','a.pharmacy_catalog_id = c.id','LEFT'),
            //     array('package p', 'c.package_id = p.id', 'LEFT'),
            //     array('package d', 'c.package_big_id = d.id', 'LEFT'),
            //     array('company e', 'c.suppliers_id = e.id', 'LEFT'),
            //     array('company f', 'c.factory_id = f.id', 'LEFT'),
            //     array('receipt_items ri','a.pharmacy_catalog_id = ri.pharmacy_catalog_id','LEFT'),
            //     array('receipts r','ri.receipt_id = r.id','LEFT'),
            //     array('company er', 'r.supplier_id = er.id', 'LEFT'),
            // );
            // $order = array('c.name' => 'asc','ri.price_item' => 'asc');
            // // $order = array('c.name' => 'asc');
            // // $this->$model->limit = 200;
            // $this->db->group_by(array("a.pharmacy_catalog_id","ri.price_item"));
            // $output = $this->$model->select(0, $select, $where, $join, $order);

            $output = $this->db->query("
                SELECT `a`.`pharmacy_catalog_id`, `c`.`value_package`, `c`.`package_id`, `c`.`package_big_id`,
                `c`.`value_package_big`, `a`.`admin_amount`, `b`.`name` as `depo_name`, `c`.`name` as `catalog_name`, `c`.`price`,
                `c`.`min`, `c`.`max`, `p`.`name` as `package_name`, `d`.`name` as `package_big_name`, `e`.`name` as
                `suppliers_master_name`, `f`.`name` as `factory_name`, `er`.`name` as `suppliers_name`, `ri`.`receipt_id`,
                `er`.`account_alias`, `ri`.`price_item`, `ri`.`discount_item`,
                `ri`.`discount_price`
                FROM `pharmacy_assets` as `a`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
                LEFT JOIN `package` `d` ON `c`.`package_big_id` = `d`.`id`
                LEFT JOIN `company` `e` ON `c`.`suppliers_id` = `e`.`id`
                LEFT JOIN `company` `f` ON `c`.`factory_id` = `f`.`id`
                LEFT JOIN `receipt_items` `ri` ON `a`.`pharmacy_catalog_id` = `ri`.`pharmacy_catalog_id`
                LEFT JOIN `receipts` `r` ON `ri`.`receipt_id` = `r`.`id`
                LEFT JOIN `company` `er` ON `r`.`supplier_id` = `er`.`id`
                WHERE `a`.`pharmacy_depo_id` = $id
                AND `r`.`is_deleted` = 0
                AND `c`.`min` > 0
                AND `a`.`admin_amount` <= `c`.`min`
                GROUP BY `a`.`pharmacy_catalog_id`, `er`.`id`, `ri`.`price_item`
                ORDER BY `c`.`name` ASC, `ri`.`price_item` ASC
            ")->result();

            $response_data = array();
            $new_output = array();
            $i_new = 0;
            $catalog_comparison = array();
            for ($i = 0; $i < count($output); $i++) {
                $pharmacy_catalog_id = $output[$i]->pharmacy_catalog_id;
                $suppliers_name = $output[$i]->suppliers_name;
                $price_item = $output[$i]->price_item;

                if (key_exists($pharmacy_catalog_id, $catalog_comparison) != 1) {
                    $catalog_comparison[$pharmacy_catalog_id] = array();
                }

                $catalog_comparison[$pharmacy_catalog_id][] = array(
                    'suppliers_name' => $suppliers_name,
                    'price_item' => (float) $price_item,
                    'point' => 0,
                    'index' => $i,
                );

                usort($catalog_comparison[$pharmacy_catalog_id], function($a, $b) {
                    return $a['price_item'] <=> $b['price_item'];
                });

                $point = 5;
                for ($j=0; $j < count($catalog_comparison[$pharmacy_catalog_id]); $j++) { 
                    $catalog_comparison[$pharmacy_catalog_id][$j]['point'] = $point;
                    $index = $catalog_comparison[$pharmacy_catalog_id][$j]['index'];
                    $output[$index]->price_point = $point;
                    $point--;
                }

                if($output[$i]->admin_amount <= $output[$i]->min){
                    // $this->Purchase_order_item->whereFields['a.pharmacy_catalog_id'] = $CatalogData[0]->id;
                    // $this->Purchase_order_item->whereFields['p.pharmacy_depo_id'] = $id;
                    // $CheckPO = $this->Purchase_order_item->read(0,false);
                    // $this->Planning_item->whereFields['a.pharmacy_catalog_id'] = $CatalogData[0]->id;
                    // $this->Planning_item->whereFields['pp.pharmacy_depo_id'] = $id;
                    // $CheckPRC = $this->Planning_item->read(0,false);
                    // $CheckPM = '';
                    // if($CheckPO[0]->receipt_id){
                    //     $this->Receipt_item->whereFields['a.pharmacy_catalog_id'] = $CatalogData[0]->id;
                    //     $this->Receipt_item->whereFields['a.receipt_id'] = $CheckPO[0]->receipt_id;
                    //     $CheckPM = $this->Receipt_item->read(0,false);
                    // }
                    
                    // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
                    $where_r['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                    $where_r['a.receipt_id'] = $output[$i]->receipt_id;
                    $select_r = 'a.price_item,a.discount_item,a.discount_price,pcs.package_id,pcs.package_big_id,pcs.value_package,pcs.value_package_big,ps.name as package_name,pb.name as package_big_name';
                    $join_r = array(
                        array('receipts b','a.receipt_id = b.id','LEFT'),
                        array('pharmacy_catalog_supplier pcs','b.supplier_id = pcs.suppliers_id','LEFT'),
                        array('package ps', 'pcs.package_id = ps.id', 'LEFT'),
                        array('package pb', 'pcs.package_big_id = pb.id', 'LEFT'),
                    );
                    $order_r = array('b.date' => 'desc');
                    $LastReceive = $this->Receipt_item->select(0, $select_r, $where_r, $join_r, $order_r)[0];
                    // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
                    // Point
                    // 0	1
                    // 7	2
                    // 14	3
                    // 30	4
                    // 45	5
                    // 60	6
                    // 70	7
                    // 90	8
                    // > 90	9
                    if ($output[$i]->account_alias == 0) {
                        $output[$i]->account_alias = 1;
                    } else if ($output[$i]->account_alias == 7) {
                        $output[$i]->account_alias = 2;
                    } else if ($output[$i]->account_alias == 14) {
                        $output[$i]->account_alias = 3;
                    } else if ($output[$i]->account_alias == 30) {
                        $output[$i]->account_alias = 4;
                    } else if ($output[$i]->account_alias == 45) {
                        $output[$i]->account_alias = 5;
                    } else if ($output[$i]->account_alias == 60) {
                        $output[$i]->account_alias = 6;
                    } else if ($output[$i]->account_alias == 70) {
                        $output[$i]->account_alias = 7;
                    } else if ($output[$i]->account_alias == 90) {
                        $output[$i]->account_alias = 8;
                    } else if ($output[$i]->account_alias > 90) {
                        $output[$i]->account_alias = 9;
                    } else {
                        $output[$i]->account_alias = $output[$i]->account_alias;
                    }
                    $output[$i]->price = $output[$i]->price_item;
                    // $output[$i]->price = $LastReceive->price_item ? $LastReceive->price_item : $output[$i]->price;
                    // $output[$i]->discount_price = $LastReceive->discount_price ? $LastReceive->discount_price : 0;
                    // $output[$i]->discount_item = $LastReceive->discount_item ? $LastReceive->discount_item : 0;
                    $output[$i]->id = $output[$i]->pharmacy_catalog_id;
                    $output[$i]->package_id = $LastReceive->package_id ? $LastReceive->package_id : $output[$i]->package_id; 
                    $output[$i]->package_name = $LastReceive->package_name ? $LastReceive->package_name : $output[$i]->package_name; 
                    $output[$i]->package_big_id = $LastReceive->package_big_id ? $LastReceive->package_big_id : $output[$i]->package_big_id; 
                    $output[$i]->package_big_name = $LastReceive->package_big_name ? $LastReceive->package_big_name : $output[$i]->package_big_name; 
                    $new_output[$i_new] = $output[$i];
                    $i_new++;
                    // $output[$i]->code = $CatalogData[0]->code;
                    // $output[$i]->category_catalog_id = $CatalogData[0]->category_catalog_id;
                    // $output[$i]->generik_id = $CatalogData[0]->generik_id;
                    // $output[$i]->name = $CatalogData[0]->name;
                    // $output[$i]->on_po = $CheckPO[0] ? 1 : 0;
                    // $output[$i]->on_prc = $CheckPRC[0] ? 1 : 0;
                    // if ($CheckPM[0]) {
                    //     $output[$i]->value_package_receipt = $CheckPM[0]->value_package;
                    //     $output[$i]->value_package_po = $CheckPO[0]->value_package;
                    //     if($CheckPM[0]->value_package != $CheckPO[0]->value_package) {
                    //         //RECEIPT KURANG
                    //         $output[$i]->on_pm = 1;
                    //     } else {
                    //         //RECEIPT DONE
                    //         $output[$i]->on_prc = 0;
                    //         $output[$i]->on_po = 0;
                    //         $output[$i]->on_pm = 0;
                    //     }
                    // }else{
                    //     //BELUM RECEIPT
                    //     $output[$i]->on_pm = 1;
                    // }
                    // $output[$i]->name_brand = $CatalogData[0]->name_brand;
                    // $output[$i]->brand_id = $CatalogData[0]->brand_id;
                    // $output[$i]->package_id = $CatalogData[0]->package_id;
                    // $output[$i]->package_name = $CatalogData[0]->package_name;
                    // $output[$i]->value_package = $CatalogData[0]->value_package;
                    // $output[$i]->package_big_id = $CatalogData[0]->package_big_id;
                    // $output[$i]->package_big_name = $CatalogData[0]->package_big_name;
                    // $output[$i]->value_package_big = $CatalogData[0]->value_package_big;
                    // $output[$i]->suppliers_id = $CatalogData[0]->suppliers_id;
                    // $output[$i]->factory_id = $CatalogData[0]->factory_id;
                    // $output[$i]->composition = $CatalogData[0]->composition;
                    // $output[$i]->dose_id = $CatalogData[0]->dose_id;
                    // $output[$i]->dose = $CatalogData[0]->dose;
                    // $output[$i]->min = $CatalogData[0]->min;
                    // $output[$i]->price = $CatalogData[0]->price;
                    // $output[$i]->max = $CatalogData[0]->max;
                    // $output[$i]->is_delete = $CatalogData[0]->is_delete;
                    // $output[$i]->date_added = $CatalogData[0]->date_added;
                }

            }
            if(!empty($output) && $id != 0){
                $response_data = $new_output;
                $error = false;
            }else if (!empty($new_output) && $id == 0){
                $response_data = $new_output;   
                $error = false;
            }else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error){
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }else{
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    
    
    public function supplier_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "{$this->name}_item";

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            if ($q_strings['planning_id'] == '' || $q_strings['planning_id'] == null || $q_strings['supplier_id'] == '' || $q_strings['supplier_id'] == null) {
                $this->REST_Return(404, 'Fill Planning Documents & Supplier!');
                return;
            }
            $where['a.pharmacy_planning_id'] = $q_strings['planning_id'];
            $where['sb.suppliers_id'] = $q_strings['supplier_id'];
            $select = 'a.*,b.code,b.name as pharmacy_catalog_name,sb.package_big_id,sb.value_package_big,sb.package_id,sb.value_package';
            $join = array(
        		array('pharmacy_catalog b','a.pharmacy_catalog_id = b.id','LEFT'),
        		array('pharmacy_catalog_supplier sb','a.pharmacy_catalog_id = sb.id','LEFT'),
            );
            $order = array('b.name' => 'asc');
            $output = $this->$model->select(0, $select, $where, $join, $order);
            $response_data = array();
            for ($i = 0; $i < count($output); $i++) {
                // SEARCH LAST RECEIVE ITEM/LASTFAKTUR
                $where_r['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                $select_r = 'a.discount_item,a.discount_price';
                $join_r = array(
                    array('receipts b','a.receipt_id = b.id','LEFT'),
                );
                $order_r = array('b.date' => 'desc');
                $LastReceive = $this->Receipt_item->select(0, $select_r, $where_r, $join_r, $order_r)[0];
                // SEARCH LAST RECEIVE ITEM/LASTFAKTUR

                $output[$i]->discount_price = $LastReceive->discount_price ? $LastReceive->discount_price : 0;
                $output[$i]->discount_item = $LastReceive->discount_item ? $LastReceive->discount_item : 0;
            }
            if(!empty($output) && $id != 0){
                $response_data = $output;
                $error = false;
            }else if (!empty($output) && $id == 0){
                $response_data = $output;   
                $error = false;
            }else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error){
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }else{
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    public function item_depo_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Pharmacy_assets";

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            $this->$model->whereFields['a.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
            $this->$model->whereFields['a.pharmacy_catalog_id'] = $q_strings['pharmacy_catalog_id'];
            $output = $this->$model->read(0,false);
            $response_data = array();
            if (!empty($output)) {
                for ($i = 0; $i < count($output); $i++) {
                    $CatalogData = $this->Catalog->read($output[$i]->pharmacy_catalog_id,false);
                    $this->Planning_item->whereFields['a.pharmacy_catalog_id'] = $CatalogData[0]->id;
                    $this->Planning_item->whereFields['pp.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
                    $CheckPRC = $this->Planning_item->read(0,false);
                    $this->Purchase_order_item->whereFields['a.pharmacy_catalog_id'] = $CatalogData[0]->id;
                    $this->Purchase_order_item->whereFields['p.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
                    $CheckPO = $this->Purchase_order_item->read(0,false);
                    $CheckPM = '';
                    if($CheckPO[0]->receipt_id){
                        $this->Receipt_item->whereFields['a.pharmacy_catalog_id'] = $CatalogData[0]->id;
                        $this->Receipt_item->whereFields['a.receipt_id'] = $CheckPO[0]->receipt_id;
                        $CheckPM = $this->Receipt_item->read(0,false);
                    }
                    $output[$i]->id = $CatalogData[0]->id;
                    $output[$i]->code = $CatalogData[0]->code;
                    $output[$i]->category_catalog_id = $CatalogData[0]->category_catalog_id;
                    $output[$i]->generik_id = $CatalogData[0]->generik_id;
                    $output[$i]->on_prc = $CheckPRC[0] ? 1 : 0;
                    $output[$i]->on_po = $CheckPO[0] ? 1 : 0;
                    if ($CheckPM[0]) {
                        $output[$i]->value_package_receipt = $CheckPM[0]->value_package;
                        $output[$i]->value_package_po = $CheckPO[0]->value_package;
                        if($CheckPM[0]->value_package != $CheckPO[0]->value_package) {
                            //RECEIPT KURANG
                            $output[$i]->on_pm = 1;
                        } else {
                            //RECEIPT DONE
                            $output[$i]->on_prc = 0;
                            $output[$i]->on_po = 0;
                            $output[$i]->on_pm = 0;
                        }
                    }else{
                        //BELUM RECEIPT
                        $output[$i]->on_pm = 1;
                    }
                    $output[$i]->name = $CatalogData[0]->name;
                    $output[$i]->name_brand = $CatalogData[0]->name_brand;
                    $output[$i]->brand_id = $CatalogData[0]->brand_id;
                    $output[$i]->package_id = $CatalogData[0]->package_id;
                    $output[$i]->package_name = $CatalogData[0]->package_name;
                    $output[$i]->value_package = $CatalogData[0]->value_package;
                    $output[$i]->package_big_id = $CatalogData[0]->package_big_id;
                    $output[$i]->package_big_name = $CatalogData[0]->package_big_name;
                    $output[$i]->value_package_big = $CatalogData[0]->value_package_big;
                    $output[$i]->suppliers_id = $CatalogData[0]->suppliers_id;
                    $output[$i]->factory_id = $CatalogData[0]->factory_id;
                    $output[$i]->composition = $CatalogData[0]->composition;
                    $output[$i]->dose_id = $CatalogData[0]->dose_id;
                    $output[$i]->dose = $CatalogData[0]->dose;
                    $output[$i]->min = $CatalogData[0]->min;
                    $output[$i]->price = $CatalogData[0]->price;
                    $output[$i]->max = $CatalogData[0]->max;
                    $output[$i]->is_delete = $CatalogData[0]->is_delete;
                    $output[$i]->date_added = $CatalogData[0]->date_added;
                }
            } else {
                $output = $this->Catalog->read($q_strings['pharmacy_catalog_id'],false);
                for ($i = 0; $i < count($output); $i++) {
                    $this->Purchase_order_item->whereFields['a.pharmacy_catalog_id'] = $output[$i]->id;
                    $this->Purchase_order_item->whereFields['p.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
                    $CheckPO = $this->Purchase_order_item->read(0,false);
                    $this->Planning_item->whereFields['a.pharmacy_catalog_id'] = $output[$i]->id;
                    $this->Planning_item->whereFields['pp.pharmacy_depo_id'] = $q_strings['pharmacy_depo_id'];
                    $CheckPRC = $this->Planning_item->read(0,false);
                    $output[$i]->on_po = $CheckPO[0] ? 1 : 0;
                    $output[$i]->on_prc = $CheckPRC[0] ? 1 : 0;
                }
            }
            if(!empty($output) && $id != 0){
                $response_data = $output;
                $error = false;
            }else if (!empty($output) && $id == 0){
                $response_data = $output;   
                $error = false;
            }else {
                $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
                return;
            }
            if ($error){
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            }else{
                $this->REST_Return(200, 'SUCCESS', $response_data);
            }
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }
    public function transaction_signature_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id         = $this->security->xss_clean($p);
        $data       = $this->json_put();
        $device     = $data['dt_server']['HTTP_USER_AGENT'];
        $ip         = $this->get_client_ip();
        $error      = TRUE;

        $validatedToken = $this->authorization_token->validateToken();
        if (!empty($validatedToken) AND $validatedToken['status'] === TRUE){
            if(isset($q_strings['limit'])) $this->SignatureValidation->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->SignatureValidation->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->SignatureValidation->whereFields['module_id'] = $id;
            $output = $this->SignatureValidation->read();
            $response_data = array();
            if (empty($output)) {
                $this->REST_Return(404,'Signature validation does not exist!');
                return;
            }
            for ($i = 0; $i< count($output); $i++)
            {
                $this->TransactionSignatureValidation->whereFields['signature_validation_id'] = $output[$i]->id;
                $transaction_signature_validations = $this->TransactionSignatureValidation->read();
                    for($j = 0; $j < count($transaction_signature_validations); $j++){
                        $output[$i]->transaction_signature_validations[$j]['id'] = $transaction_signature_validations[$j]->id;
                        $output[$i]->transaction_signature_validations[$j]['group_id'] = $transaction_signature_validations[$j]->group_id;
                        $output[$i]->transaction_signature_validations[$j]['group_name'] = $transaction_signature_validations[$j]->group_name;
                        $output[$i]->transaction_signature_validations[$j]['level_validation'] = $transaction_signature_validations[$j]->level_validation;
                        $output[$i]->transaction_signature_validations[$j]['user_id'] = $transaction_signature_validations[$j]->user_id;
                        $output[$i]->transaction_signature_validations[$j]['status'] = $transaction_signature_validations[$j]->status;
                        $output[$i]->transaction_signature_validations[$j]['user_added'] = $transaction_signature_validations[$j]->user_added;
                        $output[$i]->transaction_signature_validations[$j]['date_added'] = $transaction_signature_validations[$j]->date_added;
                        $output[$i]->transaction_signature_validations[$j]['user_updated'] = $transaction_signature_validations[$j]->user_updated;
                        $output[$i]->transaction_signature_validations[$j]['date_updated'] = $transaction_signature_validations[$j]->date_updated;
                    }
            }
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validatedToken['message'] ,$response_data);
            return;
        }

    }
}