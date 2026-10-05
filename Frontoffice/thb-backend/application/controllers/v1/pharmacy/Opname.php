<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Opname extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_item_model", "Opname_item");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("$this->directory/Pharmacy_assets_model", "PharmacyAssets");
        $this->load->model("$this->directory/Pharmacy_assets_card_model", "PharmacyAssetsCard");
        $this->load->model("$this->directory/Pharmacy_assets_price_model", "PharmacyAssetsPrice");
        $this->delete_rules = array(
            array('field' => 'is_delete', 'label' => 'is_delete', 'rules' => 'trim|required|max_length[1]'),
        );
        $this->load->helper("excel");
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
                        'date_added' => date('Y-m-d H:i:s'),                   
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
                                    'date_created' => date('Y-m-d H:i:s'),
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
                                    'date_created' => date('Y-m-d H:i:s'),
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
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                );

                                $this->ReceiptCompare->create($dt_comparison);
                            }else{
                                // if ($pharmacy_asset[0]->flag == 1 ){
                                    $in_amount = $dt_post['receipt_items'][$i]['sum_item'];
                                    $prevAmount = $pharmacy_asset[0]->physical_amount;
                                    $currentAmount = $pharmacy_asset[0]->physical_amount + $dt_post['receipt_items'][$i]['sum_item'];
                                // }else{
                                //     $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                                // }
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
                                    'date_created' => date('Y-m-d H:i:s'),
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
                                        'date_updated' => date('Y-m-d H:i:s'),
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
                    'date_updated' => date('Y-m-d H:i:s'),
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
                                'date_created' => date('Y-m-d H:i:s'),
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
                                'date_updated' => date('Y-m-d H:i:s'),
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                            );

                            $this->ReceiptCompare->create($dt_comparison);
                        }else{
                            // if ($pharmacy_asset[0]->flag == 1 ){
                                $in_amount = $dt_put['receipt_items'][$i]['sum_item'];
                                $prevAmount = $pharmacy_asset[0]->physical_amount;
                                $currentAmount = $pharmacy_asset[0]->physical_amount + $dt_put['receipt_items'][$i]['sum_item'];
                            // }else{
                            //     $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                            // }
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
                                'date_created' => date('Y-m-d H:i:s'),
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
                                    'date_updated' => date('Y-m-d H:i:s'),
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
                    //             'date_created' => date('Y-m-d H:i:s'),
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
                    //             'date_updated' => date('Y-m-d H:i:s'),
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
                    //             'date_created' => date('Y-m-d H:i:s'),
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
                    //                 'date_updated' => date('Y-m-d H:i:s'),
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

    public function itemupdate_put($p = 0)
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
            $this->form_validation->set_rules('accumulation', 'accumulation' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('batch', 'batch' , 'trim|max_length[36]');
            $this->form_validation->set_rules('expired_date', 'expired_date' , 'trim|max_length[36]');
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
            } else {
                $institute_branch_id = $this->security->xss_clean($is_valid_token['data']->institute_branch_id);
                $user_id = $this->security->xss_clean($is_valid_token['data']->id);
                // Check pharmacy_depo_id
                $model = "Opname_item";
                $this->db->trans_start();
                // code here 
                $dt_opnameItem = array(
                    'id' => $id,
                    'batch' => $dt_put['batch'],
                    'expired_date' => $dt_put['expired_date'],
                    'accumulation' => $dt_put['accumulation'],
                    'rack_id' => $dt_put['rack_id'],
                    'rackname_id' => $dt_put['rackname_id'],
                    'user_bak' => $dt_put['user'],
                    'timestampt' => date('Y-m-d H:i:s'),
                );
                $this->$model->replace($dt_opnameItem);
                $datatostok = $this->$model->read($id)[0];
                if ($datatostok && $datatostok->pharmacy_depo_id && $datatostok->pharmacy_catalog_id){
                    $dt_opnameAssets = array(
                        'pharmacy_depo_id' => $datatostok->pharmacy_depo_id,
                        'pharmacy_catalog_id' => $datatostok->pharmacy_catalog_id,
                        'batch' => $dt_put['batch'],
                        'expired_date' => $dt_put['expired_date'],
                        'physical_amount' => $dt_put['accumulation'],
                        'admin_amount' => $dt_put['accumulation'],
                        'date_updated' => date('Y-m-d H:i:s'),
                    );
                    $this->PharmacyAssets->replace($dt_opnameAssets);
                    $dt_opnameAssetsCard = array(
                        'pharmacy_depo_id' => $datatostok->pharmacy_depo_id,
                        'pharmacy_catalog_id' => $datatostok->pharmacy_catalog_id,
                        'reff_id' => $id,
                        'prev_amount' => 0,
                        'in_amount' => $dt_put['accumulation'],
                        'out_amount' => 0,
                        'current_amount' => $dt_put['accumulation'],
                        'flag' => 7,
                        'expired_date' => $dt_put['expired_date'],
                        'date_created' => date('Y-m-d H:i:s'),
                    );
                    $this->PharmacyAssetsCard->create($dt_opnameAssetsCard);
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

    public function item_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = "Opname_item";

        $validateToken = $this->authorization_token->validateToken();

        if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            $this->$model->whereFields['a.opname_id'] = $id;
            $output = $this->$model->read(0,false);
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


    // public function item_get($p = 0)
    // {
    //     parse_str($_SERVER['QUERY_STRING'],$q_strings);
    //     $error  = true;
    //     $id     = $this->security->xss_clean($p);
    //     $model  = "Opname_item";
    //     $validateToken = $this->authorization_token->validateToken();
    //     if(!empty($validateToken && $validateToken['status'] == TRUE)){
    //         $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
    //         $head = $this->Opname->select($id, false);

    //         $where_r['a.id'] = $id;
    //         if($head[0]->pharmacy_depo_id == '8da5d967-18da-4f87-86dc-0a9416611c11') {
    //             $select_r = '
    //             pa.pharmacy_catalog_id, pc.name as catalog_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
    //             pr.name as name_rack, 
    //             prn.name as name_rackname,poi.rack_id,poi.rackname_id,
    //             ';
    //             $join_r = array(
    //                 array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
    //                 array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
    //                 array('package p', 'pc.package_id = p.id', 'LEFT'),
    //                 array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
    //                 array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "36e47cd1-63b3-44f7-8d96-76e824c22f1a"', 'LEFT'),
    //                 array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
    //                 array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
    //             );
    //         } else if($head[0]->pharmacy_depo_id == '297c44f0-12ef-40e6-b9a7-c831065b3372') {
    //             $select_r = '
    //             pa.pharmacy_catalog_id, pc.name as catalog_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
    //             pr.name as name_rack, 
    //             prn.name as name_rackname,poi.rack_id,poi.rackname_id,
    //             ';
    //             $join_r = array(
    //                 array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
    //                 array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
    //                 array('package p', 'pc.package_id = p.id', 'LEFT'),
    //                 array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
    //                 array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "8a7899ea-73e7-4eb7-88ad-cece4d56aa44"', 'LEFT'),
    //                 array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
    //                 array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
    //             );
    //         } else if($head[0]->pharmacy_depo_id == '4105d8bc-5769-463e-bc67-aae2fc5adf10') {
    //             $select_r = '
    //             pa.pharmacy_catalog_id, pc.name as catalog_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
    //             pr.name as name_rack, 
    //             prn.name as name_rackname,poi.rack_id,poi.rackname_id,
    //             ';
    //             $join_r = array(
    //                 array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
    //                 array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
    //                 array('package p', 'pc.package_id = p.id', 'LEFT'),
    //                 array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
    //                 array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "df54921d-1873-4748-b6c1-06aecb387cad"', 'LEFT'),
    //                 array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
    //                 array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
    //             );
    //         } else if($head[0]->pharmacy_depo_id == '822d71e8-7a2b-4fc6-a372-40a6b458d8c3') {
    //             $select_r = '
    //             pa.pharmacy_catalog_id, pc.name as catalog_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
    //             pr.name as name_rack, 
    //             prn.name as name_rackname,poi.rack_id,poi.rackname_id,
    //             ';
    //             $join_r = array(
    //                 array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
    //                 array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
    //                 array('package p', 'pc.package_id = p.id', 'LEFT'),
    //                 array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
    //                 array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "26f064f7-7d47-4af1-8ecc-2d487a64bf3a"', 'LEFT'),
    //                 array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
    //                 array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
    //             );
    //         } else {
    //             $select_r = '
    //             pa.pharmacy_catalog_id, pc.name as catalog_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna
    //             ';
    //             // a.id,a.pharmacy_catalog_id,a.catalog_name,a.expired_date,a.package_name,a.package_big_name,
    //             // a.accumulation,a.hna,a.hp,a.sum_adm,a.sum_physical,a.user,a.user_bak,a.timestampt,a.batch,a.rack_id,a.rackname_id,
    //             // po.pharmacy_depo_id,
    //             // pr.name as name_rack, 
    //             // prn.name as name_rackname,
    //             $join_r = array(
    //                 array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
    //                 array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
    //                 array('package p', 'pc.package_id = p.id', 'LEFT'),
    //                 array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
    //                 // array('pharmacy_opname po', 'a.opname_id = po.id', 'LEFT'),
    //                 // array('pharmacy_depo pd','po.pharmacy_depo_id = pd.id','LEFT'),
    //                 // array('pharmacy_opname_activation poa', 'po.pharmacy_activation_id = poa.id', 'LEFT'),
    //                 // array('helper ci', 'pc.category_id = ci.code AND ci.variable="type_category_p"', 'LEFT'),
    //                 // array('pharmacy_rack pr', 'a.rack_id = pr.id', 'LEFT'),
    //                 // array('pharmacy_rackname prn', 'a.rackname_id = prn.id', 'LEFT'),
    //             );
    //         }

    //         $output = $this->Opname->select(0, $select_r, $where_r, $join_r);

    //         // $this->$model->whereFields['a.opname_id'] = $id;
    //         // $output = $this->$model->read(0,false);
    //         $response_data = array();
    //         // code here
    //         $response_data = $output;
    //         $this->REST_Return(200, 'SUCCESS', $response_data);    
    //     }else{
    //         $response_data = 'Invalid Token.';
    //         $this->REST_Return(401, $validateToken['message'] ,$response_data);
    //         return;
    //     }
    // }

    public function item_excel_get($p = 0)
    {
        $head = $this->Opname->read($p, false);
        // var_dump($head);die();
       
        $style = array(
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array(
                        'argb' => 'FF000000',
                    ),
                ),
            ),
        );

        $headers = array(
            'NO' => array(
                'width' => 15
            ),
            'NAMA BARANG' => array(
                'width' => 15
            ),
            'JENIS KATALOG' => array(
                'width' => 15
            ),
            'SATUAN' => array(
                'width' => 15
            ),
            'JUMLAH SISTEM' => array(
                'width' => 15
            ),
            'JUMLAH FISIK' => array(
                'width' => 15
            ),
            'EXPIRED' => array(
                'width' => 15
            ),
            'NOMOR RAK' => array(
                'width' => 15
            ),
            'NAMA RAK' => array(
                'width' => 15
            ),
        );
        
        $where_r['a.id'] = $p;
        $where_r['pc.is_delete'] = 0;
        if($head[0]->pharmacy_depo_id == '8da5d967-18da-4f87-86dc-0a9416611c11') {
            $select_r = '
            pa.pharmacy_catalog_id, pc.name as catalog_name, cc.name as category_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
            pr.name as name_rack, 
            prn.name as name_rackname,poi.rack_id,poi.rackname_id,
            ';
            $join_r = array(
                array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
                array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
                array('category_catalog cc', 'pc.category_catalog_id = cc.id', 'LEFT'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
                array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "36e47cd1-63b3-44f7-8d96-76e824c22f1a"', 'LEFT'),
                array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
                array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
            );
            $order_r = array('pr.name' => 'asc');
        } else if($head[0]->pharmacy_depo_id == '297c44f0-12ef-40e6-b9a7-c831065b3372') {
            $select_r = '
            pa.pharmacy_catalog_id, pc.name as catalog_name, cc.name as category_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
            pr.name as name_rack, 
            prn.name as name_rackname,poi.rack_id,poi.rackname_id,
            ';
            $join_r = array(
                array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
                array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
                array('category_catalog cc', 'pc.category_catalog_id = cc.id', 'LEFT'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
                array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "bda8de5b-b5c5-418f-bb60-0efb96303765"', 'LEFT'),
                array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
                array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
            );
            $order_r = array('pr.name' => 'asc');
        } else if($head[0]->pharmacy_depo_id == '4105d8bc-5769-463e-bc67-aae2fc5adf10') {
            $select_r = '
            pa.pharmacy_catalog_id, pc.name as catalog_name, cc.name as category_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
            pr.name as name_rack, 
            prn.name as name_rackname,poi.rack_id,poi.rackname_id,
            ';
            $join_r = array(
                array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
                array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
                array('category_catalog cc', 'pc.category_catalog_id = cc.id', 'LEFT'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
                array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "df54921d-1873-4748-b6c1-06aecb387cad"', 'LEFT'),
                array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
                array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
            );
            $order_r = array('pr.name' => 'asc');
        } else if($head[0]->pharmacy_depo_id == '822d71e8-7a2b-4fc6-a372-40a6b458d8c3') {
            $select_r = '
            pa.pharmacy_catalog_id, pc.name as catalog_name, cc.name as category_name, pc.package_id, p.name as package_name, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna,
            pr.name as name_rack, 
            prn.name as name_rackname,poi.rack_id,poi.rackname_id,
            ';
            $join_r = array(
                array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
                array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
                array('category_catalog cc', 'pc.category_catalog_id = cc.id', 'LEFT'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
                array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id = "26f064f7-7d47-4af1-8ecc-2d487a64bf3a"', 'LEFT'),
                array('pharmacy_rack pr', 'poi.rack_id = pr.id', 'LEFT'),
                array('pharmacy_rackname prn', 'poi.rackname_id = prn.id', 'LEFT'),
            );
            $order_r = array('pr.name' => 'asc');
        } else {
            $select_r = 'poi.opname_id,
            a.id,a.pharmacy_catalog_id,a.catalog_name,a.expired_date,a.package_name,a.package_big_name,
            a.accumulation,a.hna,a.hp,a.sum_adm,a.sum_physical,a.user,a.user_bak,a.timestampt,a.batch,a.rack_id,a.rackname_id,
            po.pharmacy_depo_id,
            pr.name as name_rack, 
            prn.name as name_rackname,
            ';
            // pa.pharmacy_catalog_id, pc.name as catalog_name, cc.name as category_name, pc.package_id, p.name as package_name,pa.expired_date, pa.physical_amount as sum_physical, pa.admin_amount as sum_adm,pap.hp_item as hp,pap.hna_item as hna
            $join_r = array(
                array('pharmacy_assets pa', 'a.pharmacy_depo_id = pa.pharmacy_depo_id', 'INNER'),
                array('pharmacy_opname_item poi', 'pa.pharmacy_catalog_id = poi.pharmacy_catalog_id AND poi.opname_id=a.id', 'LEFT'),
                array('pharmacy_catalog pc', 'pa.pharmacy_catalog_id = pc.id', 'INNER'),
                array('category_catalog cc', 'pc.category_catalog_id = cc.id', 'LEFT'),
                array('package p', 'pc.package_id = p.id', 'LEFT'),
                array('pharmacy_assets_price pap', 'pa.pharmacy_catalog_id = pap.pharmacy_catalog_id AND is_active = "1"', 'INNER'),
                array('pharmacy_opname po', 'a.opname_id = po.id', 'LEFT'),
                array('pharmacy_depo pd','po.pharmacy_depo_id = pd.id','LEFT'),
                array('pharmacy_opname_activation poa', 'po.pharmacy_activation_id = poa.id', 'LEFT'),
                array('helper ci', 'pc.category_id = ci.code AND ci.variable="type_category_p"', 'LEFT'),
                array('pharmacy_rack pr', 'a.rack_id = pr.id', 'LEFT'),
                array('pharmacy_rackname prn', 'a.rackname_id = prn.id', 'LEFT'),
            );
            $order_r = array('pc.name' => 'asc');
        }
        $output = $this->Opname->select(0, $select_r, $where_r, $join_r, $order_r);

        $rows = array();
        $row = array();

        
        for ($i=0; $i < count($output); $i++) { 
            $select_check = 'a.pharmacy_catalog_id, a.opname_id';
            $where_check['a.opname_id'] = $p;
            $where_check['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
            $check_item = $this->Opname_item->select(0, $select_check, $where_check)[0];
            if (!$check_item->pharmacy_catalog_id) {
                $catalog_name = $output[$i]->catalog_name;
                $category_name = $output[$i]->category_name;
                $package_name = $output[$i]->package_name;
                $sum_physical = $output[$i]->sum_physical;
                $kosong = '';
                $name_rack = $output[$i]->name_rack;
                $name_rackname = $output[$i]->name_rackname;

                $row = array($i,$catalog_name,$category_name,$package_name,$sum_physical,$kosong,$name_rack,$name_rackname);
                array_push($rows, $row);
                
                // $rows = array(
                //     'opname_id' => $p,                
                //     'pharmacy_catalog_id' => $output[$i]->pharmacy_catalog_id,              
                //     'package_id' => $output[$i]->package_id,               
                //     'catalog_name' => $output[$i]->catalog_name,                 
                //     'package_name' => $output[$i]->package_name,                 
                //     'sum_adm' => $output[$i]->sum_adm,              
                //     'sum_physical' => $output[$i]->sum_physical,                  
                //     'accumulation' => $output[$i]->sum_physical,                  
                //     'hp' => $output[$i]->hp,               
                //     'hna' => $output[$i]->hna,                    
                //     'expired_date' => $output[$i]->expired_date,                 
                //     'sort' => $i,                                          
                //     'rack_id' => $output[$i]->rack_id,              
                //     'rackname_id' => $output[$i]->rackname_id,              
                // );
                // $this->Opname_item->create($rows);
                // echo '<pre>';
                // var_dump($rows);
            }
        }
        echo '<pre>';
        var_dump($rows);
        die();
        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);

        $filename = $head[0]->no_document.'_'.$head[0]->pharmacy_depo_name.date('Ymd_His');
        ob_end_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        ob_end_clean();
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
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $this->$model->whereFields['poa.flag'] = 0;
            $this->$model->whereLike = $q_strings;
            $output = $this->$model->read_ssr($id);
            $response_data = array();

            if (empty($output)){
                $this->REST_Return(404, 'Data not found!');
                return;
            }
            // for($i = 0; $i< count($output->rows); $i++){
            //     $receiptItem = $this->ReceiptItems->read($output->rows[$i]->id);
            //     for ($j = 0; $j < count($receiptItem); $j++){
            //         $output->rows[$i]->receipt_items[$j]['receipt_id'] = $receiptItem[$j]->receipt_id; 
            //         $output->rows[$i]->receipt_items[$j]['pharmacy_catalog_id'] = $receiptItem[$j]->pharmacy_catalog_id;
            //         $output->rows[$i]->receipt_items[$j]['pharmacy_catalog_name'] = $receiptItem[$j]->pharmacy_catalog_name;
            //         $output->rows[$i]->receipt_items[$j]['package_id'] = $receiptItem[$j]->package_id;
            //         $output->rows[$i]->receipt_items[$j]['package_name'] = $receiptItem[$j]->package_name;
            //         $output->rows[$i]->receipt_items[$j]['value_package'] = $receiptItem[$j]->value_package;
            //         $output->rows[$i]->receipt_items[$j]['sum_item'] = $receiptItem[$j]->sum_item;
            //         $output->rows[$i]->receipt_items[$j]['sum_package'] = $receiptItem[$j]->sum_package;
            //         $output->rows[$i]->receipt_items[$j]['price_item'] = $receiptItem[$j]->price_item;
            //         $output->rows[$i]->receipt_items[$j]['price_package'] = $receiptItem[$j]->price_package;
            //         $output->rows[$i]->receipt_items[$j]['discount_item'] = $receiptItem[$j]->discount_item;
            //         $output->rows[$i]->receipt_items[$j]['discount_price'] = $receiptItem[$j]->discount_price;
            //         $output->rows[$i]->receipt_items[$j]['sort'] = $receiptItem[$j]->sort;
            //         $output->rows[$i]->receipt_items[$j]['package_isi'] = $receiptItem[$j]->package_isi;
            //         $output->rows[$i]->receipt_items[$j]['before_discount'] = $receiptItem[$j]->before_discount;
            //         $output->rows[$i]->receipt_items[$j]['expired_date'] = $receiptItem[$j]->expired_date;
            //     }
            // }
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        }else{
            $response_data = 'Invalid Token.';
            $this->REST_Return(401, $validateToken['message'] ,$response_data);
            return;
        }
    }

    public function close_get($p = 0)
    {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        // $validateToken = $this->authorization_token->validateToken();

        // if(!empty($validateToken && $validateToken['status'] == TRUE)){
            $institute_branch_id = $this->security->xss_clean($validateToken['data']->institute_branch_id);
            $output = $this->$model->read('3ad4c80b-55c3-4cd6-89cf-93034bca37ae');
            $response_data = array();
            for($i = 0; $i< count($output); $i++){
                $this->Opname_item->whereFields['opname_id'] = $output[$i]->id;
                $opnameItem = $this->Opname_item->read();
                for ($j = 0; $j < count($opnameItem); $j++){
                    $dt_replace_so = array(
                        'pharmacy_depo_id'=> $opnameItem[$j]->pharmacy_depo_id,
                        'pharmacy_catalog_id'=> $opnameItem[$j]->pharmacy_catalog_id,
                        'prev_amount'=> 0,
                        'expired_date'=> $opnameItem[$j]->expired_date,
                        'flag'=> 1,
                        'desc'=> 'Penutupan Stock Opaname 06 Oktober 2024',
                        'date_updated'=> date('Y-m-d H:i:s')
                    );
                    $this->PharmacyAssets->replace($dt_replace_so);
                    // echo "<pre>";
                    // var_dump($dt_replace_so);
                    // echo "</pre>";
                    // $output->rows[$i]->receipt_items[$j]['receipt_id'] = $opnameItem[$j]->receipt_id; 
                }
            }
            // code here
            $response_data = $output;
            $this->REST_Return(200, 'SUCCESS', $response_data);    
        // }else{
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $validateToken['message'] ,$response_data);
        //     return;
        // }
    }

    
}