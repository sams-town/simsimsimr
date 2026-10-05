<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Receipt extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_model_item", "ReceiptItems");
        $this->load->model("$this->directory/{$this->name}_po_comparison_model", "ReceiptCompare");
        $this->load->model("$this->directory/Purchase_order_model", "PurchaseOrder");
        $this->load->model("$this->directory/Purchase_order_model_item", "PurchaseOrderItem");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("v1/setting/Transaction_signature_validation_model", "TransactionSignatureValidation");
        $this->load->model("$this->directory/Pharmacy_assets_model", "PharmacyAssets");
        $this->load->model("$this->directory/Pharmacy_assets_card_model", "PharmacyAssetsCard");
        $this->load->model("$this->directory/Pharmacy_assets_price_model", "PharmacyAssetsPrice");
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
            $this->form_validation->set_rules('no_invoice', 'no_invoice' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('supplier_id', 'supplier_id' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_planning_id', 'pharmacy_planning_id','trim|max_length[36]');
            $this->form_validation->set_rules('purchase_order_id', 'purchase_order_id','trim|max_length[36]');
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id','trim|required|max_length[36]');
            $this->form_validation->set_rules('payment_method_id', 'payment_method_id','trim|required|max_length[36]');

            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
            } else {
                // if ($dt_post['flag'] == 0 || $dt_post['flag'] == 1 ){
                    $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                    $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                    // Check pharmacy_depo_id
                    $model = $this->name;
                    $this->db->trans_start();
                    $codecounter = array(
                        'var_1' => 'PN',
                        'var_2' => date('Y'),
                        'var_3' => date('m'),
                        'var_5' => date('Y-m')
                    );
                    $this->load->library("transaction");
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT);
                    if ($checkcounter['counter_queue'] == '0') {
                        $this->Counter_queue->replace(array(
                            'var_1' => 'PN',
                            'var_2' => date('Y'),
                            'var_3' => date('m'),
                            'var_5' => date('Y-m'),
                            'counter_queue' => 0
                        ));
                        $checkcounter = $this->transaction->counter($codecounter);
                        $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT);
                    }
                    // code here 
                    $dt_receipt = array(
                        'no_document' => $code,
                        'no_invoice' => $dt_post['no_invoice'],
                        'date' => $dt_post['date'],
                        'supplier_id' => $dt_post['supplier_id'],
                        'pharmacy_planning_id' => $dt_post['pharmacy_planning_id'],
                        'purchase_order_id' => $dt_post['purchase_order_id'],
                        'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                        'payment_method_id' => $dt_post['payment_method_id'],
                        'ntotal' => str_replace(",", ".", str_replace(".", "", $dt_post['ntotal'])),
                        'ndiscount' => str_replace(",", ".", str_replace(".", "", $dt_post['ndiscount'])),
                        'ntax' => str_replace(",", ".", str_replace(".", "", $dt_post['ntax'])),
                        'nround' => str_replace(",", ".", str_replace(".", "", $dt_post['nround'])),
                        'nfinish' => str_replace(",", ".", str_replace(".", "", $dt_post['nfinish'])),
                        'validation_key_1'      => isset($dt_post['validation_key_1']) ? $this->base64ToFile($dt_post['validation_key_1'],$code) : '',
                        'user_added' => $user_id,
                        'date_added' => date('Y-m-d h:i:s'),                   
                        'flag' => $dt_post['flag'],
                    );

                    $id = $this->$model->create($dt_receipt);

                    for ($i = 0; $i< count($dt_post['receipt_items']); $i++)
                    {
                        $catalogCheck = $this->Catalog->read($dt_post['receipt_items'][$i]['pharmacy_catalog_id'],false);
                        if (empty($catalogCheck)){
                            $this->REST_Return(422, 'Oops, something went wrong, Pharmacy catalog not found!');
                            return;
                        }
                        $dt_rc_item = array(
                            'receipt_id' => $id,
                            'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                            'package_id' =>   $dt_post['receipt_items'][$i]['package_id'],
                            'package_name' =>   $dt_post['receipt_items'][$i]['package_name'],
                            'value_package' =>  $dt_post['receipt_items'][$i]['value_package'],
                            'sum_item' =>  $dt_post['receipt_items'][$i]['sum_item'],
                            'sum_package' => $dt_post['receipt_items'][$i]['sum_package'],
                            'price_item' =>  str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_item'])),
                            'price_package' => str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_package'])),
                            'discount_item' => str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['discount_item'])),
                            'discount_price' => str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['discount_price'])),
                            'package_big_id' => $dt_post['receipt_items'][$i]['package_big_id'],
                            // 'package_name_big' => $dt_post['receipt_items'][$i]['package_name_big'],
                            'expired_date' =>$dt_post['receipt_items'][$i]['expired_date'],
                            'sort' =>  $dt_post['receipt_items'][$i]['sort'],
                        );

                        $this->ReceiptItems->replace($dt_rc_item);
                        $dt_prc_replace = array(
                            'id' => $dt_post['receipt_items'][$i]['signature_validation_id'],
                            'receipt_id' => $id,
                        );
                        $this->PurchaseOrderItem->replace($dt_prc_replace);

                        if ($dt_post['validation_key_1']){
                            $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_id'];
                            $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_post['receipt_items'][$i]['pharmacy_catalog_id'];
                            $pharmacy_asset = $this->PharmacyAssets->read(0,false);

                            $hna = str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_item']));
                            $hp = str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_item'])) * 0.1;

                            $in_amount = 0;
                            $currentAmount = 0;
                            $prevAmount = 0;

                            if (empty($pharmacy_asset)){
                                $dt_pharmacy_asset = array(
                                    'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                    'prev_amount' => 0,
                                    'physical_amount' => $dt_post['receipt_items'][$i]['sum_item'],
                                    'admin_amount' => $dt_post['receipt_items'][$i]['sum_item'],
                                    'desc' => $dt_post['pharmacy_asset']['desc'],
                                    'flag' => 1,
                                );
                                $this->PharmacyAssets->replace($dt_pharmacy_asset);

                                $dt_pharmacy_asset_card = array(
                                    'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                    'reff_id' => $id,
                                    'prev_amount' => 0,
                                    'in_amount' => $dt_post['receipt_items'][$i]['sum_item'],
                                    'out_amount' => 0,
                                    'current_amount' => $dt_post['receipt_items'][$i]['sum_item'],
                                    'hna_item' => $hna,
                                    'hp_item' => $hp,
                                    'flag' => 1,
                                    'hja_item' => $hp,
                                    'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                    'date_created' => date('Y-m-d h:i:s'),
                                );

                                $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                                

                                $dt_pharmacy_asset_price = array(
                                    'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                    'package_id' => $dt_post['receipt_items'][$i]['package_id'],
                                    'hna_item' => $hna,
                                    'hp_item' => $hp,
                                    'hja_item' => $hp,
                                    'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                    'date_created' => date('Y-m-d h:i:s'),
                                );

                                $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);
                                $checkReceipt = $this->$model->read(0,false)[0];

                                $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                                $checkPO = $this->PurchaseOrderItem->read(0,false);

                                $dt_comparison = array (
                                    'purchase_order_id' => $checkReceipt->purchase_order_id,
                                    'receipt_id' => $checkReceipt->id,
                                    'total_po' => $checkPO[$i]->sum_item,
                                    'total_receipt' => $dt_post['receipt_items'][$i]['sum_item'],
                                    'date_updated' => date('Y-m-d h:i:s'),
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                );

                                $this->ReceiptCompare->create($dt_comparison);
                            }else{
                                if ($pharmacy_asset[0]->flag == 1 ){
                                    $in_amount = $dt_post['receipt_items'][$i]['sum_item'];
                                    $prevAmount = $pharmacy_asset[0]->physical_amount;
                                    $currentAmount = $pharmacy_asset[0]->physical_amount + $dt_post['receipt_items'][$i]['sum_item'];
                                }else{
                                    $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                                }
                                $dt_pharmacy_asset = array(
                                    'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                    // 'batch' => $dt_post['batch'],
                                    'prev_amount' => $prevAmount,
                                    'physical_amount' => $currentAmount,
                                    'admin_amount' => $currentAmount,
                                );
                                $id_pharmacy = $this->PharmacyAssets->replace($dt_pharmacy_asset);
                                
                                $dt_pharmacy_asset_card = array(
                                    'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                    'reff_id' => $id,
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                    'prev_amount' => $prevAmount,
                                    'in_amount' => $in_amount,
                                    'out_amount' => 0,
                                    'current_amount' => $currentAmount,
                                    'hna_item' => $hna,
                                    'hp_item' => $hp,
                                    'flag'  => 1,
                                    'hja_item' => $hp,
                                    'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                    'date_created' => date('Y-m-d h:i:s'),
                                );

                                $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                                $checkReceipt = $this->$model->read(0,false)[0];

                                $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                                $checkPO = $this->PurchaseOrderItem->read(0,false);

                                $this->ReceiptCompare->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                                $this->ReceiptCompare->whereFields['pharmacy_catalog_id'] = $dt_post['receipt_items'][$i]['pharmacy_catalog_id'];

                                $checkComparison = $this->ReceiptCompare->read(0,false);
                                for ($x = 0; $x < count($checkComparison); $x ++){
                                    $dt_comparison = array (
                                        'id' => $checkComparison[$x]->id,
                                        'purchase_order_id' => $checkReceipt->purchase_order_id,
                                        'receipt_id' => $checkReceipt->id,
                                        'total_po' => $checkPO[$i]->sum_item,
                                        'total_receipt' => $checkComparison[$x]->total_receipt + $dt_post['receipt_items'][$i]['sum_item'],
                                        'date_updated' => date('Y-m-d h:i:s'),
                                        'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                    );
                                    
                                    $this->ReceiptCompare->replace($dt_comparison);
                                }
                            }
                        }
                    }
                // }
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
        $file_name    = 'image_vkey_pm_'.$id; // You can change it to anything
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

        $is_valid_token = $this->authorization_token->validateToken();
        if (!empty($is_valid_token) AND $is_valid_token['status'] === TRUE) {
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules('no_invoice', 'no_invoice' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('supplier_id', 'supplier_id' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_planning_id', 'pharmacy_planning_id','trim|max_length[36]');
            $this->form_validation->set_rules('purchase_order_id', 'purchase_order_id','trim|max_length[36]');
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id','trim|required|max_length[36]');
            $this->form_validation->set_rules('payment_method_id', 'payment_method_id','trim|required|max_length[36]');

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
                // code here 
                $dt_receipt = array(
                    'id' => $id,
                    'pharmacy_planning_id' => $dt_put['pharmacy_planning_id'],
                    'purchase_order_id' => $dt_put['purchase_order_id'],
                    'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    'payment_method_id' => $dt_put['payment_method_id'],
                    'supplier_id' => $dt_put['supplier_id'],
                    'no_invoice' => $dt_put['no_invoice'],
                    'date' => $dt_put['date'],
                    'ntotal' => str_replace(",", ".", str_replace(".", "", $dt_put['ntotal'])),
                    'ndiscount' => str_replace(",", ".", str_replace(".", "", $dt_put['ndiscount'])),
                    'ntax' => str_replace(",", ".", str_replace(".", "", $dt_put['ntax'])),
                    'nround' => str_replace(",", ".", str_replace(".", "", $dt_put['nround'])),
                    'nfinish' => str_replace(",", ".", str_replace(".", "", $dt_put['nfinish'])),
                    'validation_key_1'=> isset($dt_put['validation_key_1']) ? $this->base64ToFile($dt_put['validation_key_1'],$dt_put['no_document']) : $planning[0]->validation_key_1,
                    'user_updated' => $user_id,
                    'date_updated' => date('Y-m-d h:i:s'),
                    'flag' => $dt_put['flag'],
                );

                $id = $this->$model->update($id, $dt_receipt);

                for ($i = 0; $i< count($dt_put['receipt_items']); $i++)
                {
                    $catalogCheck = $this->Catalog->read($dt_put['receipt_items'][$i]['pharmacy_catalog_id'],false);
                    if (empty($catalogCheck)){
                        $this->REST_Return(422, 'Oops, something went wrong, Pharmacy catalog not found!');
                        return;
                    }
                    $dt_rc_item = array(
                        'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                        'package_id' =>   $dt_put['receipt_items'][$i]['package_id'],
                        'value_package' =>  $dt_put['receipt_items'][$i]['value_package'],
                        'sum_item' =>  $dt_put['receipt_items'][$i]['sum_item'],
                        'sum_package' => $dt_put['receipt_items'][$i]['sum_package'],
                        'price_item' =>  str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item'])),
                        'price_package' => str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_package'])),
                        'discount_item' => str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['discount_item'])),
                        'discount_price' => str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['discount_price'])),
                        'package_big_id' => $dt_put['receipt_items'][$i]['package_big_id'],
                        // 'package_name_big' => $dt_put['receipt_items'][$i]['package_name_big'],
                        'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                    );

                    $this->ReceiptItems->replace($dt_rc_item);
                    $dt_prc_replace = array(
                        'id' => $dt_put['receipt_items'][$i]['signature_validation_id'],
                        'receipt_id' => $id,
                    );
                    $this->PurchaseOrderItem->replace($dt_prc_replace);

                    if ($dt_put['validation_key_1']){
                        $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                        $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];
                        $pharmacy_asset = $this->PharmacyAssets->read(0,false);

                        $hna = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item']));
                        $hp = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item'])) * 0.1;

                        $in_amount = 0;
                        $currentAmount = 0;
                        $prevAmount = 0;

                        if (empty($pharmacy_asset)){
                            $dt_pharmacy_asset = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'prev_amount' => 0,
                                'physical_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                                'admin_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                                'desc' => $dt_put['pharmacy_asset']['desc'],
                                'flag' => 1,
                            );
                            $this->PharmacyAssets->replace($dt_pharmacy_asset);

                            $dt_pharmacy_asset_card = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'reff_id' => $id,
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'prev_amount' => 0,
                                'in_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                                'out_amount' => 0,
                                'flag' => 1,
                                'current_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                                'hna_item' => $hna,
                                'hp_item' => $hp,
                                'hja_item' => $hp,
                                'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                                'date_created' => date('Y-m-d h:i:s'),
                            );

                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            $checkReceipt = $this->$model->read(0,false)[0];

                            $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                            $checkPO = $this->PurchaseOrderItem->read(0,false);

                            $dt_comparison = array (
                                'purchase_order_id' => $checkReceipt->purchase_order_id,
                                'receipt_id' => $checkReceipt->id,
                                'total_po' => $checkPO[$i]->sum_item,
                                'total_receipt' => $dt_put['receipt_items'][$i]['sum_item'],
                                'date_updated' => date('Y-m-d h:i:s'),
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                            );

                            $this->ReceiptCompare->create($dt_comparison);
                        }else{
                            if ($pharmacy_asset[0]->flag == 1 ){
                                $in_amount = $dt_put['receipt_items'][$i]['sum_item'];
                                $prevAmount = $pharmacy_asset[0]->physical_amount;
                                $currentAmount = $pharmacy_asset[0]->physical_amount + $dt_put['receipt_items'][$i]['sum_item'];
                            }else{
                                $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                            }
                            $dt_pharmacy_asset = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                // 'batch' => $dt_put['batch'],
                                'prev_amount' => $prevAmount,
                                'physical_amount' => $currentAmount,
                                'admin_amount' => $currentAmount,
                            );
                            $id_pharmacy = $this->PharmacyAssets->replace($dt_pharmacy_asset);
                            
                            $dt_pharmacy_asset_card = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'id' => $id,
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'prev_amount' => $prevAmount,
                                'in_amount' => $in_amount,
                                'out_amount' => 0,
                                'flag' => 1,
                                'current_amount' => $currentAmount,
                                'hna_item' => $hna,
                                'hp_item' => $hp,
                                'hja_item' => $hp,
                                'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                                'date_created' => date('Y-m-d h:i:s'),
                            );

                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            $checkReceipt = $this->$model->read(0,false)[0];

                            $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                            $checkPO = $this->PurchaseOrderItem->read(0,false);

                            $this->ReceiptCompare->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                            $this->ReceiptCompare->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];

                            $checkComparison = $this->ReceiptCompare->read(0,false);
                            for ($x = 0; $x < count($checkComparison); $x ++){
                                $dt_comparison = array (
                                    'id' => $checkComparison[$x]->id,
                                    'purchase_order_id' => $checkReceipt->purchase_order_id,
                                    'receipt_id' => $checkReceipt->id,
                                    'total_po' => $checkPO[$i]->sum_item,
                                    'total_receipt' => $checkComparison[$x]->total_receipt + $dt_put['receipt_items'][$i]['sum_item'],
                                    'date_updated' => date('Y-m-d h:i:s'),
                                    'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                );
                                
                                $this->ReceiptCompare->replace($dt_comparison);
                            }
                        }
                    }

                    // if ($dt_put['flag'] == 2){
                    //     $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                    //     $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];
                    //     $pharmacy_asset = $this->PharmacyAssets->read(0,false);

                    //     $hna = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item']));
                    //     $hp = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item'])) * 0.1;

                    //     $in_amount = 0;
                    //     $currentAmount = 0;
                    //     $prevAmount = 0;

                    //     if (empty($pharmacy_asset)){
                    //         $dt_pharmacy_asset = array(
                    //             'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    //             'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                    //             'prev_amount' => 0,
                    //             'physical_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                    //             'admin_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                    //             'desc' => $dt_put['pharmacy_asset']['desc'],
                    //             'flag' => 1,
                    //         );
                    //         $this->PharmacyAssets->replace($dt_pharmacy_asset);

                    //         $dt_pharmacy_asset_card = array(
                    //             'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    //             'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                    //             'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    //             'prev_amount' => 0,
                    //             'in_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                    //             'out_amount' => 0,
                    //             'current_amount' => $dt_put['receipt_items'][$i]['sum_item'],
                    //             'hna_item' => $hna,
                    //             'hp_item' => $hp,
                    //             'hja_item' => $hp,
                    //             'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                    //             'date_created' => date('Y-m-d h:i:s'),
                    //         );

                    //         $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                    //         $checkReceipt = $this->$model->read(0,false)[0];

                    //         $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                    //         $checkPO = $this->PurchaseOrderItem->read(0,false);

                    //         $dt_comparison = array (
                    //             'purchase_order_id' => $checkReceipt->purchase_order_id,
                    //             'receipt_id' => $checkReceipt->id,
                    //             'total_po' => $checkPO[$i]->sum_item,
                    //             'total_receipt' => $dt_put['receipt_items'][$i]['sum_item'],
                    //             'date_updated' => date('Y-m-d h:i:s'),
                    //             'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                    //         );

                    //         $this->ReceiptCompare->create($dt_comparison);
                    //     }else{
                    //         if ($pharmacy_asset[0]->flag == 1 ){
                    //             $in_amount = $dt_put['receipt_items'][$i]['sum_item'];
                    //             $prevAmount = $pharmacy_asset[0]->physical_amount;
                    //             $currentAmount = $pharmacy_asset[0]->physical_amount + $dt_put['receipt_items'][$i]['sum_item'];
                    //         }else{
                    //             $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                    //         }
                    //         $dt_pharmacy_asset = array(
                    //             'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    //             'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                    //             // 'batch' => $dt_put['batch'],
                    //             'prev_amount' => $prevAmount,
                    //             'physical_amount' => $currentAmount,
                    //             'admin_amount' => $currentAmount,
                    //         );
                    //         $id_pharmacy = $this->PharmacyAssets->replace($dt_pharmacy_asset);
                            
                    //         $dt_pharmacy_asset_card = array(
                    //             'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    //             'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                    //             'prev_amount' => $prevAmount,
                    //             'in_amount' => $in_amount,
                    //             'out_amount' => 0,
                    //             'current_amount' => $currentAmount,
                    //             'hna_item' => $hna,
                    //             'hp_item' => $hp,
                    //             'hja_item' => $hp,
                    //             'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                    //             'date_created' => date('Y-m-d h:i:s'),
                    //         );

                    //         $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                    //         $checkReceipt = $this->$model->read(0,false)[0];

                    //         $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                    //         $checkPO = $this->PurchaseOrderItem->read(0,false);

                    //         $this->ReceiptCompare->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                    //         $this->ReceiptCompare->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];

                    //         $checkComparison = $this->ReceiptCompare->read(0,false);
                    //         for ($x = 0; $x < count($checkComparison); $x ++){
                    //             $dt_comparison = array (
                    //                 'id' => $checkComparison[$x]->id,
                    //                 'purchase_order_id' => $checkReceipt->purchase_order_id,
                    //                 'receipt_id' => $checkReceipt->id,
                    //                 'total_po' => $checkPO[$i]->sum_item,
                    //                 'total_receipt' => $checkComparison[$x]->total_receipt + $dt_put['receipt_items'][$i]['sum_item'],
                    //                 'date_updated' => date('Y-m-d h:i:s'),
                    //                 'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                    //             );
                                
                    //             $this->ReceiptCompare->replace($dt_comparison);
                    //         }
                    //     }
                    // }
                }
                // save to transaction signature validation 
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
            if(isset($q_strings['module_id'])) $this->SignatureValidation->whereFields['module_id'] = $q_strings['module_id']; unset($q_strings['module_id']);
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereLike = $q_strings;
            $output = $this->$model->read_ssr($id);
            $response_data = array();

            if (empty($output)){
                $this->REST_Return(404, 'Data not found!');
                return;
            }
            for($i = 0; $i< count($output->rows); $i++){
                $receiptItem = $this->ReceiptItems->read($output->rows[$i]->id);
                for ($j = 0; $j < count($receiptItem); $j++){
                    $output->rows[$i]->receipt_items[$j]['receipt_id'] = $receiptItem[$j]->receipt_id; 
                    $output->rows[$i]->receipt_items[$j]['pharmacy_catalog_id'] = $receiptItem[$j]->pharmacy_catalog_id;
                    $output->rows[$i]->receipt_items[$j]['pharmacy_catalog_name'] = $receiptItem[$j]->pharmacy_catalog_name;
                    $output->rows[$i]->receipt_items[$j]['package_id'] = $receiptItem[$j]->package_id;
                    $output->rows[$i]->receipt_items[$j]['package_name'] = $receiptItem[$j]->package_name;
                    $output->rows[$i]->receipt_items[$j]['value_package'] = $receiptItem[$j]->value_package;
                    $output->rows[$i]->receipt_items[$j]['sum_item'] = $receiptItem[$j]->sum_item;
                    $output->rows[$i]->receipt_items[$j]['sum_package'] = $receiptItem[$j]->sum_package;
                    $output->rows[$i]->receipt_items[$j]['price_item'] = $receiptItem[$j]->price_item;
                    $output->rows[$i]->receipt_items[$j]['price_package'] = $receiptItem[$j]->price_package;
                    $output->rows[$i]->receipt_items[$j]['discount_item'] = $receiptItem[$j]->discount_item;
                    $output->rows[$i]->receipt_items[$j]['discount_price'] = $receiptItem[$j]->discount_price;
                    $output->rows[$i]->receipt_items[$j]['sort'] = $receiptItem[$j]->sort;
                    $output->rows[$i]->receipt_items[$j]['package_isi'] = $receiptItem[$j]->package_isi;
                    $output->rows[$i]->receipt_items[$j]['before_discount'] = $receiptItem[$j]->before_discount;
                    $output->rows[$i]->receipt_items[$j]['expired_date'] = $receiptItem[$j]->expired_date;
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
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }
    public function pharmacy_card_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            // var_dump($q_strings['date_created']);
            // die();
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            if(isset($q_strings['limit'])) $this->PharmacyAssetsCard->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->PharmacyAssetsCard->offset = $q_strings['offset']; unset($q_strings['offset']);
            $start_date = '';
            $end_date = '';
            if(isset($q_strings['start_date'])){
                $start_date = $q_strings['start_date']; 
                $start_date .= ' 00:00:00';
                unset($q_strings['start_date']);
            }
            if(isset($q_strings['end_date'])){
                $end_date = $q_strings['end_date']; 
                $end_date .= ' 23:59:59';
                unset($q_strings['end_date']);
            }
            $this->PharmacyAssetsCard->whereFields = $q_strings;
            if($start_date != ''){
                $this->PharmacyAssetsCard->whereFields['a.date_created >='] = $start_date; 
                $this->PharmacyAssetsCard->whereFields['a.date_created <='] = $end_date; 
            };

            $output = $this->PharmacyAssetsCard->read_ssr();
            $response_data = array();
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