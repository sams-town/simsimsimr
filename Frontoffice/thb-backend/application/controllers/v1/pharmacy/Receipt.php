<?php defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
            array('field' => 'is_deleted', 'label' => 'is_deleted', 'rules' => 'trim|required|max_length[1]'),
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
                    'date_receipt' => $dt_post['date_receipt'],
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
                    'validation_key_1'      => $dt_post['validation_key_1'] != "" ? $dt_post['validation_key_1'] : null,
                    // 'validation_key_1'      => isset($dt_post['validation_key_1']) ? $this->base64ToFile($dt_post['validation_key_1'],$code) : '',
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
                        'value_package' =>  str_replace(".", "", $dt_post['receipt_items'][$i]['value_package']),
                        'sum_item' =>  str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                        'sum_package' => $dt_post['receipt_items'][$i]['sum_package'],
                        'price_item' =>  str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_item'])),
                        'price_package' => str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_package'])),
                        'discount_item' => $dt_post['receipt_items'][$i]['discount_item'],
                        'discount_price' => str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['discount_price'])),
                        'package_big_id' => $dt_post['receipt_items'][$i]['package_big_id'],
                        // 'package_name_big' => $dt_post['receipt_items'][$i]['package_name_big'],
                        'expired_date' =>$dt_post['receipt_items'][$i]['expired_date'],
                        'batch' => (string) $dt_post['receipt_items'][$i]['batch'],
                        'sort' =>  $dt_post['receipt_items'][$i]['sort'],
                    );
                    
                    $this->ReceiptItems->replace($dt_rc_item);
                    if ($dt_post['validation_key_1'] != ""){
                        $dt_prc_replace = array(
                            'id' => $dt_post['receipt_items'][$i]['signature_validation_id'],
                            'receipt_id' => $id,
                        );
                        $this->PurchaseOrderItem->replace($dt_prc_replace);
                        $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_post['pharmacy_depo_id'];
                        $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_post['receipt_items'][$i]['pharmacy_catalog_id'];
                        $pharmacy_asset = $this->PharmacyAssets->read(0,false);

                        $hna = str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_item']));
                        $discount_price = str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['discount_price']));
                        $hp = str_replace(",", ".", str_replace(".", "", $dt_post['receipt_items'][$i]['price_item'])) * 1.3;
                        $hpp = $hna - $discount_price;
                        $in_amount = 0;
                        $currentAmount = 0;
                        $prevAmount = 0;

                        if (empty($pharmacy_asset)){
                            $dt_pharmacy_asset = array(
                                'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                'prev_amount' => 0,
                                'physical_amount' => str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                                'admin_amount' => str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                                'desc' => $dt_post['pharmacy_asset']['desc'],
                                'flag' => 1,
                            );
                            $this->PharmacyAssets->replace($dt_pharmacy_asset);

                            $dt_pharmacy_asset_card = array(
                                'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                'reff_id' => $id,
                                'prev_amount' => 0,
                                'in_amount' => str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                                'out_amount' => 0,
                                'current_amount' => str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                                'hna_item' => $hna,
                                'hp_item' => $hpp,
                                'flag' => 1,
                                'hja_item' => $hp,
                                'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                'batch' => (string) $dt_post['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                            );

                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            

                            $this->PharmacyAssetsPrice->whereFields['pharmacy_catalog_id'] = $dt_post['receipt_items'][$i]['pharmacy_catalog_id'];
                            $this->PharmacyAssetsPrice->whereFields['is_active'] = 1;
                            $checkPriceLast = $this->PharmacyAssetsPrice->read(0,false)[0];
                            if($checkPriceLast){
                                $this->db->where('id', $checkPriceLast->id);
                                $this->db->limit(1);
                                $this->db->update('pharmacy_assets_price', array('is_active'=>null));
                            }

                            $dt_pharmacy_asset_price = array(
                                'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                'package_id' => $dt_post['receipt_items'][$i]['package_id'],
                                'hna_item' => $hna,
                                'hp_item' => $hp,
                                'hja_item' => $hp,
                                'hpp_item' => $hpp,
                                'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                'batch' => (string) $dt_post['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                                'is_active' => 1,
                            );

                            $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);
                            $checkReceipt = $this->$model->read($id,false)[0];

                            $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                            $checkPO = $this->PurchaseOrderItem->read($id,false);

                            $dt_comparison = array (
                                'purchase_order_id' => $checkReceipt->purchase_order_id,
                                'receipt_id' => $checkReceipt->id,
                                'total_po' => $checkPO[$i]->sum_item,
                                'total_receipt' => str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                                'date_updated' => date('Y-m-d H:i:s'),
                                'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                            );

                            $this->ReceiptCompare->create($dt_comparison);
                        }else{
                            // if ($pharmacy_asset[0]->flag == 1 ){
                                $in_amount = str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']);
                                $prevAmount = $pharmacy_asset[0]->physical_amount;
                                $currentAmount = $pharmacy_asset[0]->physical_amount + str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']);
                            // }else{
                            //     $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                            //     return;
                            // }
                            $dt_pharmacy_asset = array(
                                'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                'batch' => (string) $dt_post['batch'],
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
                                'hp_item' => $hpp,
                                'flag'  => 1,
                                'hja_item' => $hp,
                                'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                'date_created' => date('Y-m-d H:i:s'),
                            );

                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            
                            $this->PharmacyAssetsPrice->whereFields['pharmacy_catalog_id'] = $dt_post['receipt_items'][$i]['pharmacy_catalog_id'];
                            $this->PharmacyAssetsPrice->whereFields['is_active'] = 1;
                            $checkPriceLast = $this->PharmacyAssetsPrice->read(0,false)[0];
                            if($checkPriceLast){
                                $this->db->where('id', $checkPriceLast->id);
                                $this->db->limit(1);
                                $this->db->update('pharmacy_assets_price', array('is_active'=>null));
                            }

                            $dt_pharmacy_asset_price = array(
                                'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                'package_id' => $dt_post['receipt_items'][$i]['package_id'],
                                'hna_item' => $hna,
                                'hp_item' => $hp,
                                'hja_item' => $hp,
                                'hpp_item' => $hpp,
                                'expired_date' => $dt_post['receipt_items'][$i]['expired_date'],
                                'batch' => (string) $dt_post['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                                'is_active' => 1,
                            );

                            $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);

                            $checkReceipt = $this->$model->read($id,false)[0];

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
                                    'total_receipt' => $checkComparison[$x]->total_receipt + str_replace(".", "", $dt_post['receipt_items'][$i]['sum_item']),
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'pharmacy_catalog_id' => $dt_post['receipt_items'][$i]['pharmacy_catalog_id'],
                                );
                                
                                $this->ReceiptCompare->replace($dt_comparison);
                            }
                        }
                    }
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
                    'date_receipt' => $dt_put['date_receipt'],
                    'date' => $dt_put['date'],
                    'ntotal' => str_replace(",", ".", str_replace(".", "", $dt_put['ntotal'])),
                    'ndiscount' => str_replace(",", ".", str_replace(".", "", $dt_put['ndiscount'])),
                    'ntax' => str_replace(",", ".", str_replace(".", "", $dt_put['ntax'])),
                    'nround' => str_replace(",", ".", str_replace(".", "", $dt_put['nround'])),
                    'nfinish' => str_replace(",", ".", str_replace(".", "", $dt_put['nfinish'])),
                    'validation_key_1'  => $dt_put['validation_key_1'] != "" ? $dt_put['validation_key_1'] : null,
                    'user_updated' => $user_id,
                    'date_updated' => date('Y-m-d H:i:s'),
                    'flag' => $dt_put['flag'],
                );

                $this->$model->update($id, $dt_receipt);

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
                        'value_package' =>  str_replace(".", "", $dt_put['receipt_items'][$i]['value_package']),
                        'sum_item' =>  str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
                        'sum_package' => $dt_put['receipt_items'][$i]['sum_package'],
                        'price_item' =>  str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item'])),
                        'price_package' => str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_package'])),
                        'discount_item' => $dt_put['receipt_items'][$i]['discount_item'],
                        'discount_price' => str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['discount_price'])),
                        'package_big_id' => $dt_put['receipt_items'][$i]['package_big_id'],
                        // 'package_name_big' => $dt_put['receipt_items'][$i]['package_name_big'],
                        'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                        'batch' => (string)  $dt_put['receipt_items'][$i]['batch'],
                    );

                    $this->ReceiptItems->replace($dt_rc_item);
                    // $dt_prc_replace = array(
                    //     'id' => $dt_put['receipt_items'][$i]['signature_validation_id'],
                    //     'receipt_id' => $id,
                    // );
                    // $this->PurchaseOrderItem->replace($dt_prc_replace);

                    if ($dt_put['validation_key_1']){
                        $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = $dt_put['pharmacy_depo_id'];
                        $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];
                        $pharmacy_asset = $this->PharmacyAssets->read(0,false);

                        $hna = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item']));
                        $hp = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['price_item'])) * 1.3;
                        $discount_price = str_replace(",", ".", str_replace(".", "", $dt_put['receipt_items'][$i]['discount_price']));
                        $hpp = $hna - $discount_price;

                        $in_amount = 0;
                        $currentAmount = 0;
                        $prevAmount = 0;

                        if (empty($pharmacy_asset)){
                            $dt_pharmacy_asset = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'prev_amount' => 0,
                                'physical_amount' => str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
                                'admin_amount' => str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
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
                                'in_amount' => str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
                                'out_amount' => 0,
                                'flag' => 1,
                                'current_amount' => str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
                                'hna_item' => $hna,
                                'hp_item' => $hpp,
                                'hja_item' => $hp,
                                'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                                'batch' => (string) $dt_put['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                            );
                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            
                            $this->PharmacyAssetsPrice->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];
                            $this->PharmacyAssetsPrice->whereFields['is_active'] = 1;
                            $checkPriceLast = $this->PharmacyAssetsPrice->read(0,false)[0];
                            if($checkPriceLast){
                                $this->db->where('id', $checkPriceLast->id);
                                $this->db->limit(1);
                                $this->db->update('pharmacy_assets_price', array('is_active'=>null));
                            }

                            $dt_pharmacy_asset_price = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'package_id' => $dt_put['receipt_items'][$i]['package_id'],
                                'hna_item' => $hna,
                                'hp_item' => $hp,
                                'hpp_item' => $hpp,
                                'hja_item' => $hp,
                                'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                                'batch' => (string) $dt_put['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                                'is_active' => 1,
                            );
                            $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);
                            $checkReceipt = $this->$model->read($id,false)[0];

                            $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $checkReceipt->purchase_order_id;
                            $checkPO = $this->PurchaseOrderItem->read(0,false);

                            $dt_comparison = array (
                                'purchase_order_id' => $checkReceipt->purchase_order_id,
                                'receipt_id' => $checkReceipt->id,
                                'total_po' => $checkPO[$i]->sum_item,
                                'total_receipt' => str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
                                'date_updated' => date('Y-m-d H:i:s'),
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                            );

                            $this->ReceiptCompare->create($dt_comparison);
                        }else{
                            // if ($pharmacy_asset[0]->flag == 1 ){
                                $in_amount = str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']);
                                $prevAmount = $pharmacy_asset[0]->physical_amount;
                                $currentAmount = $pharmacy_asset[0]->physical_amount + str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']);
                            // }else{
                            //     $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                            //     return;
                            // }
                            $dt_pharmacy_asset = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'batch' => (string) $dt_put['batch'],
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
                                'batch' => (string)  $dt_put['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                            );

                            $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            
                            $this->PharmacyAssetsPrice->whereFields['pharmacy_catalog_id'] = $dt_put['receipt_items'][$i]['pharmacy_catalog_id'];
                            $this->PharmacyAssetsPrice->whereFields['is_active'] = 1;
                            $checkPriceLast = $this->PharmacyAssetsPrice->read(0,false)[0];
                            if($checkPriceLast){
                                $this->db->where('id', $checkPriceLast->id);
                                $this->db->limit(1);
                                $this->db->update('pharmacy_assets_price', array('is_active'=>null));
                            }

                            $dt_pharmacy_asset_price = array(
                                'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                                'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                'package_id' => $dt_put['receipt_items'][$i]['package_id'],
                                'hna_item' => $hna,
                                'hp_item' => $hp,
                                'hpp_item' => $hpp,
                                'hja_item' => $hp,
                                'expired_date' => $dt_put['receipt_items'][$i]['expired_date'],
                                'batch' => (string) $dt_put['receipt_items'][$i]['batch'],
                                'date_created' => date('Y-m-d H:i:s'),
                                'is_active' => 1,
                            );
                            $this->PharmacyAssetsPrice->create($dt_pharmacy_asset_price);
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
                                    'total_receipt' => $checkComparison[$x]->total_receipt + str_replace(".", "", $dt_put['receipt_items'][$i]['sum_item']),
                                    'date_updated' => date('Y-m-d H:i:s'),
                                    'pharmacy_catalog_id' => $dt_put['receipt_items'][$i]['pharmacy_catalog_id'],
                                );
                                
                                $this->ReceiptCompare->replace($dt_comparison);
                            }
                        }
                    }
                }
                // save to transaction signature validation 
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
                } else {
                    $this->to_accurate_faktur($id);
                    $this->to_accurate_penerimaan($id);
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

    public function accurate_put($p = 0)
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

            if (!$id){
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
                $this->to_accurate_faktur($id);
                // $this->to_accurate_penerimaan($id);
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

    public function to_accurate_faktur($id = 0){
        // echo "<pre>";
        $model = $this->name;
        $output = $this->$model->read($id)[0];
        for($i = 0; $i< count($output); $i++){
            $this->ReceiptItems->whereFields['receipt_id'] = $id;
            $receiptitem = $this->ReceiptItems->read();
            for($j = 0; $j <count($receiptitem); $j++){
                $detailItem[$j] = array(
                    'itemNo' => $receiptitem[$j]->bride_code,
                    'unitPrice' => $receiptitem[$j]->price_item,
                    '_status' => $receiptitem[$j]->is_delete == 1 ? 'delete' : '',
                    'itemDiscPercent' => $receiptitem[$j]->discount_item,
                    'itemUnitName' => $receiptitem[$j]->package_name,
                    'purchaseOrderNumber' => $output->no_document_purchase,
                    'quantity' => $receiptitem[$j]->sum_item
                );
                // $output[$i]->receiptitem[$j]['id'] = $receiptitem[$j]->id;
                // $output[$i]->receiptitem[$j]['purchase_order_id'] = $receiptitem[$j]->purchase_order_id;
                // $output[$i]->receiptitem[$j]['pharmacy_catalog_id'] = $receiptitem[$j]->pharmacy_catalog_id;
                // $output[$i]->receiptitem[$j]['pharmacy_catalog_name'] = $receiptitem[$j]->pharmacy_catalog_name;
                // $output[$i]->receiptitem[$j]['package_id'] = $receiptitem[$j]->package_id;
                // $output[$i]->receiptitem[$j]['package_name'] = $receiptitem[$j]->package_name;
                // $output[$i]->receiptitem[$j]['value_package'] = $receiptitem[$j]->value_package;
                // $output[$i]->receiptitem[$j]['sum_item'] = $receiptitem[$j]->sum_item;
                // $output[$i]->receiptitem[$j]['sum_package'] = $receiptitem[$j]->sum_package;
                // $output[$i]->receiptitem[$j]['price_item'] = $receiptitem[$j]->price_item;
                // $output[$i]->receiptitem[$j]['price_package'] = $receiptitem[$j]->price_package;
                // $output[$i]->receiptitem[$j]['discount_item'] = $receiptitem[$j]->discount_item;
                // $output[$i]->receiptitem[$j]['discount_price'] = $receiptitem[$j]->discount_price;
                // $output[$i]->receiptitem[$j]['sort'] = $receiptitem[$j]->sort;
                // $output[$i]->receiptitem[$j]['package_isi'] = $receiptitem[$j]->package_isi;
                // $output[$i]->receiptitem[$j]['before_discount'] = $receiptitem[$j]->before_discount;
            }
        }
        // var_dump($output);
        // var_dump($receiptitem);
        $toAccurateFAKTUR = array(
            'transDate' => date("d/m/Y",strtotime($output->date)),
            'vendorNo' => $output->bride_code,
            'cashDiscount' => $output->ndiscountotal ? $output->ndiscountotal : 0,
            'number' => $output->no_document,
            'billNumber' => $output->no_invoice,
            'paymentTermName' => $output->payment_method_name,
            'toAddress' => '',
            'detailItem' => $detailItem,
        );
        // var_dump($toAccurateFAKTUR);
        // echo "</pre>";
        // die();
        $return_bride_faktur = $this->bride_accurate('api/purchase-invoice/save.do', json_encode($toAccurateFAKTUR));
        if ($return_bride_faktur['s'] == true) {
            $this->$model->replace(array('id'=>$id,'is_accurate'=>1));
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
        } else {
            // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_faktur),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
            var_dump($return_bride_faktur);
            var_dump($toAccurateFAKTUR);
            $this->REST_Return(404, 'Failed Accurate = ');
            return;
        }
    }

    public function update_salah_get($id = 0){
        // echo "<pre>";
        $model = $this->name;
        $this->$model->whereFields['supplier_id'] != 'f8de0b6f-cc9d-427f-bc45-32f06eda2bc1';
        $this->$model->whereFields['is_accurate'] = 0;
        $this->$model->whereFields['is_deleted'] = 0;
        // $this->$model->limit = 10;
        $output = $this->$model->read();
        for($i = 0; $i< count($output); $i++){
            $this->ReceiptItems->whereFields['receipt_id'] = $output[$i]->id;
            $receiptitem = $this->ReceiptItems->read();
            $detailItem=array();
            for($j = 0; $j <count($receiptitem); $j++){
                $detailItem[$j] = array(
                    'itemNo' => $receiptitem[$j]->bride_code,
                    'unitPrice' => $receiptitem[$j]->price_item,
                    '_status' => $receiptitem[$j]->is_delete == 1 ? 'delete' : '',
                    'itemDiscPercent' => $receiptitem[$j]->discount_item,
                    'itemUnitName' => $receiptitem[$j]->package_name,
                    // 'purchaseOrderNumber' => $output[$i]->no_document_purchase,
                    'quantity' => $receiptitem[$j]->sum_item
                );
            }
            // var_dump($output[$i]);
            // var_dump($receiptitem);
            // $return_bride = $this->bride_accurate('api/purchase-invoice/detail.do?number='.$output[$i]->no_document);
            // if ($return_bride['s'] == false){
            //     // CREATE CUSTOMER
            // } else {
                $toAccurateFAKTUR = array(
                    // 'id' => $return_bride['d']['id'],
                    'transDate' => date("d/m/Y",strtotime($output[$i]->date)),
                    'vendorNo' => $output[$i]->bride_code,
                    'cashDiscount' => $output[$i]->ndiscountotal ? $output[$i]->ndiscountotal : 0,
                    'number' => $output[$i]->no_document,
                    'billNumber' => $output[$i]->no_invoice,
                    'paymentTermName' => $output[$i]->payment_method_name,
                    'toAddress' => '',
                    'detailItem' => $detailItem,
                );
                // var_dump($toAccurateFAKTUR);
                // echo "</pre>";
                // die();
                $return_bride_faktur = $this->bride_accurate('api/purchase-invoice/save.do', json_encode($toAccurateFAKTUR));
                if ($return_bride_faktur['s'] == true) {
                    if($output[$i]->id){
                        $this->$model->replace(array('id'=>$output[$i]->id,'is_accurate'=>4));
                    }
                    // $this->db->trans_complete();
                    // /* End Transaction */
                    // if ($this->db->trans_status() === false) {
                    //     $response_data = $this->db->trans_status();
                    //     $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
                    // } else {
                    //     $response_data = array(
                    //         'id'     => $id
                    //     );
                    //     $this->REST_Return(201, 'SUCCESS', $response_data);
                    // }
                } else {
                    // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_faktur),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
                    var_dump($return_bride_faktur);
                    var_dump($output[$i]->no_document);
                    $this->REST_Return(404, 'Failed Accurate = ');
                    // return;
                }
            // }
        }

    }

    public function update_delete_get($id = 0){
        // echo "<pre>";
        $model = $this->name;
        $this->$model->whereFields['is_accurate'] = 3;
        // $this->$model->whereFields['is_deleted'] = 0;
        // $this->$model->limit = 10;
        $output = $this->$model->read();
        for($i = 0; $i< count($output); $i++){
            $this->ReceiptItems->whereFields['receipt_id'] = $output[$i]->id;
            $receiptitem = $this->ReceiptItems->read();
            for($j = 0; $j <count($receiptitem); $j++){
                $detailItem[$j] = array(
                    'itemNo' => $receiptitem[$j]->bride_code,
                    'unitPrice' => $receiptitem[$j]->price_item,
                    '_status' => $receiptitem[$j]->is_delete == 1 ? 'delete' : '',
                    'itemDiscPercent' => $receiptitem[$j]->discount_item,
                    'itemUnitName' => $receiptitem[$j]->package_name,
                    // 'purchaseOrderNumber' => $output[$i]->no_document_purchase,
                    'quantity' => $receiptitem[$j]->sum_item
                );
            }
            // var_dump($output[$i]);
            // var_dump($receiptitem);
            $return_bride = $this->bride_accurate('api/purchase-invoice/detail.do?number='.$output[$i]->no_document);
            if ($return_bride['s'] == false){
                // CREATE CUSTOMER
            } else {
                $toAccurateFAKTUR = array(
                    'id' => $return_bride['d']['id'],
                    // 'transDate' => date("d/m/Y",strtotime($output[$i]->date)),
                    // 'vendorNo' => $output[$i]->bride_code,
                    // 'cashDiscount' => $output[$i]->ndiscountotal ? $output[$i]->ndiscountotal : 0,
                    // 'number' => $output[$i]->no_document,
                    // 'billNumber' => $output[$i]->no_invoice,
                    // 'paymentTermName' => $output[$i]->payment_method_name,
                    // 'toAddress' => '',
                    // 'detailItem' => $detailItem,
                );
                // var_dump($toAccurateFAKTUR);
                // echo "</pre>";
                // die();
                $return_bride_faktur = $this->bride_accurate('api/purchase-invoice/delete.do?id='.$return_bride['d']['id']);
                if ($return_bride_faktur['s'] == true) {
                    if($output[$i]->id){
                        $this->$model->replace(array('id'=>$output[$i]->id,'is_accurate'=>0));
                    }
                    // $this->db->trans_complete();
                    // /* End Transaction */
                    // if ($this->db->trans_status() === false) {
                    //     $response_data = $this->db->trans_status();
                    //     $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
                    // } else {
                    //     $response_data = array(
                    //         'id'     => $id
                    //     );
                    //     $this->REST_Return(201, 'SUCCESS', $response_data);
                    // }
                } else {
                    // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_faktur),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
                    var_dump($return_bride_faktur);
                    var_dump($output[$i]->no_document);
                    $this->REST_Return(404, 'Failed Accurate = ');
                    // return;
                }
            }
        }

    }

    public function to_accurate_penerimaan($id = 0){
        echo "<pre>";
        $model = $this->name;
        $output = $this->$model->read($id)[0];
        for($i = 0; $i< count($output); $i++){
            $this->ReceiptItems->whereFields['receipt_id'] = $id;
            $receiptitem = $this->ReceiptItems->read();
            for($j = 0; $j <count($receiptitem); $j++){
                $detailItem[$j] = array(
                    'itemNo' => $receiptitem[$j]->bride_code,
                    'unitPrice' => $receiptitem[$j]->price_item,
                    '_status' => $receiptitem[$j]->is_delete == 1 ? 'delete' : '',
                    'itemDiscPercent' => $receiptitem[$j]->discount_item,
                    'itemUnitName' => $receiptitem[$j]->package_name,
                    'purchaseOrderNumber' => $output->no_document_purchase,
                    'warehouseName' => $output->pharmacy_depo_name,
                    'quantity' => $receiptitem[$j]->sum_item
                );
                // $output[$i]->receiptitem[$j]['id'] = $receiptitem[$j]->id;
                // $output[$i]->receiptitem[$j]['purchase_order_id'] = $receiptitem[$j]->purchase_order_id;
                // $output[$i]->receiptitem[$j]['pharmacy_catalog_id'] = $receiptitem[$j]->pharmacy_catalog_id;
                // $output[$i]->receiptitem[$j]['pharmacy_catalog_name'] = $receiptitem[$j]->pharmacy_catalog_name;
                // $output[$i]->receiptitem[$j]['package_id'] = $receiptitem[$j]->package_id;
                // $output[$i]->receiptitem[$j]['package_name'] = $receiptitem[$j]->package_name;
                // $output[$i]->receiptitem[$j]['value_package'] = $receiptitem[$j]->value_package;
                // $output[$i]->receiptitem[$j]['sum_item'] = $receiptitem[$j]->sum_item;
                // $output[$i]->receiptitem[$j]['sum_package'] = $receiptitem[$j]->sum_package;
                // $output[$i]->receiptitem[$j]['price_item'] = $receiptitem[$j]->price_item;
                // $output[$i]->receiptitem[$j]['price_package'] = $receiptitem[$j]->price_package;
                // $output[$i]->receiptitem[$j]['discount_item'] = $receiptitem[$j]->discount_item;
                // $output[$i]->receiptitem[$j]['discount_price'] = $receiptitem[$j]->discount_price;
                // $output[$i]->receiptitem[$j]['sort'] = $receiptitem[$j]->sort;
                // $output[$i]->receiptitem[$j]['package_isi'] = $receiptitem[$j]->package_isi;
                // $output[$i]->receiptitem[$j]['before_discount'] = $receiptitem[$j]->before_discount;
            }
        }
        // var_dump($output);
        // var_dump($receiptitem);
        $toAccuratePENERIMAAN = array(
            'receiveNumber' => $output->no_receipt_shipment,
            'transDate' => date("d/m/Y",strtotime($output->date_added)),
            'vendorNo' => $output->bride_code,
            'cashDiscount' => $output->ndiscountotal ? $output->ndiscountotal : 0,
            'number' => $output->no_invoice,
            'paymentTermName' => $output->payment_method_name,
            'toAddress' => 'RS Bunda Halimah',
            'detailItem' => $detailItem,
        );
        // var_dump($toAccuratePENERIMAAN);
        // echo "</pre>";
        // die();
        $return_bride_penerimaan = $this->bride_accurate('api/purchase-invoice/save.do', json_encode($toAccurateFAKTUR));
        if ($return_bride_penerimaan['s'] == true) {
        } else {
            // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_penerimaan),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
            var_dump($return_bride_penerimaan);
            var_dump($toAccuratePENERIMAAN);
            $this->REST_Return(404, 'Failed Accurate = ');
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
            for($i = 0; $i< count($output); $i++){
                $this->ReceiptItems->whereFields['receipt_id'] = $id;
                $receipt_items = $this->ReceiptItems->read();
                for($j = 0; $j <count($receipt_items); $j++){
                    $output[$i]->receipt_items[$j]['id'] = $receipt_items[$j]->id;
                    $output[$i]->receipt_items[$j]['purchase_order_id'] = $receipt_items[$j]->purchase_order_id;
                    $output[$i]->receipt_items[$j]['pharmacy_catalog_id'] = $receipt_items[$j]->pharmacy_catalog_id;
                    $output[$i]->receipt_items[$j]['pharmacy_catalog_name'] = $receipt_items[$j]->pharmacy_catalog_name;
                    $output[$i]->receipt_items[$j]['package_id'] = $receipt_items[$j]->package_id;
                    $output[$i]->receipt_items[$j]['batch'] = (string) $receipt_items[$j]->batch;
                    $output[$i]->receipt_items[$j]['expired_date'] = $receipt_items[$j]->expired_date;
                    $output[$i]->receipt_items[$j]['package_name'] = $receipt_items[$j]->package_name;
                    $output[$i]->receipt_items[$j]['value_package'] = $receipt_items[$j]->value_package;
                    $output[$i]->receipt_items[$j]['sum_item'] = $receipt_items[$j]->sum_item;
                    $output[$i]->receipt_items[$j]['sum_package'] = $receipt_items[$j]->sum_package;
                    $output[$i]->receipt_items[$j]['price_item'] = $receipt_items[$j]->price_item;
                    $output[$i]->receipt_items[$j]['price_package'] = $receipt_items[$j]->price_package;
                    $output[$i]->receipt_items[$j]['discount_item'] = $receipt_items[$j]->discount_item;
                    $output[$i]->receipt_items[$j]['discount_price'] = $receipt_items[$j]->discount_price;
                    $output[$i]->receipt_items[$j]['sort'] = $receipt_items[$j]->sort;
                    $output[$i]->receipt_items[$j]['package_isi'] = $receipt_items[$j]->package_isi;
                    $output[$i]->receipt_items[$j]['before_discount'] = $receipt_items[$j]->before_discount;
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
            if(isset($q_strings['no_document_po'])){
                $no_document_po = $q_strings['no_document_po']; 
                unset($q_strings['no_document_po']);
                $this->$model->whereFields['f.no_document'] = $no_document_po;
            }
            if(isset($q_strings['supplier_id'])){
                $supplier_id = $q_strings['supplier_id']; 
                unset($q_strings['supplier_id']);
                $this->$model->whereFields['a.supplier_id'] = $supplier_id;
            }
            if(isset($q_strings['no_invoice'])){
                $no_invoice = $q_strings['no_invoice']; 
                unset($q_strings['no_invoice']);
                $this->$model->whereFields['a.no_invoice'] = $no_invoice;
            }
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $this->$model->whereFields['a.date_added >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date_added <='] = $q_strings['date_end'].' 23:00:00';
            } else {
                $q_strings['date_start'] = date('Y-m-d').' 00:00:00';
                $q_strings['date_end'] = date('Y-m-d').' 23:00:00';
                $this->$model->whereFields['a.date_added >='] = $q_strings['date_start'].' 00:00:00';
                $this->$model->whereFields['a.date_added <='] = $q_strings['date_end'].' 23:00:00';
            }
            $this->$model->whereFields['a.is_deleted'] = 0;
            if ($q_strings != []){
                if(isset($q_strings['pharmacy_catalog_id'])) {
                    $search_pharmacy_catalog_id = $q_strings['pharmacy_catalog_id']; unset($q_strings['pharmacy_catalog_id']);
                    $this->$model->whereFields['ri.pharmacy_catalog_id'] = $search_pharmacy_catalog_id;
                } 
                $output = $this->$model->filter_data($q_strings,true,$search_pharmacy_catalog_id);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();

            if (empty($output)){
                $this->REST_Return(404, 'Data not found!');
                return;
            }
            for($i = 0; $i< count($output->rows); $i++){
                $receiptItem = $this->ReceiptItems->read($output->rows[$i]->id);
                for ($j = 0; $j < count($receiptItem); $j++){
                    $output->rows[$i]->receipt_items[$j]['id'] = $receiptItem[$j]->id; 
                    $output->rows[$i]->receipt_items[$j]['receipt_id'] = $receiptItem[$j]->receipt_id; 
                    $output->rows[$i]->receipt_items[$j]['pharmacy_catalog_id'] = $receiptItem[$j]->pharmacy_catalog_id;
                    $output->rows[$i]->receipt_items[$j]['pharmacy_catalog_name'] = $receiptItem[$j]->pharmacy_catalog_name;
                    $output->rows[$i]->receipt_items[$j]['package_big_id'] = $receiptItem[$j]->package_big_id;
                    $output->rows[$i]->receipt_items[$j]['package_id'] = $receiptItem[$j]->package_id;
                    $output->rows[$i]->receipt_items[$j]['package_name'] = $receiptItem[$j]->package_name;
                    $output->rows[$i]->receipt_items[$j]['package_name_big'] = $receiptItem[$j]->package_name_big;
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
                    $output->rows[$i]->receipt_items[$j]['batch'] = (string) $receiptItem[$j]->batch;
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

    public function excel_get($p = 0){
		header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $error  = true;
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

        $response_data = array();
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
        $sheet->setCellValue('A1', $response_data[0]->name); // Set kolom A1 dengan tulisan "DATA "
        $sheet->mergeCells('A1:E1'); // Set Merge Cell pada kolom A1 sampai E1
        $sheet->getStyle('A1')->getFont()->setBold(true); // Set bold kolom A1
        // Buat header tabel nya pada baris ke 3
        $sheet->setCellValue('A3', "NO DP"); 
        $sheet->setCellValue('B3', "TANGGAL FAKTUR"); 
        $sheet->setCellValue('C3', "TANGGAL DATANG"); 
        $sheet->setCellValue('D3', "NO FAKTUR");
        $sheet->setCellValue('E3', "TOP"); 
        $sheet->setCellValue('F3', "TANGGAL JT TEMPO"); 
        $sheet->setCellValue('G3', "NILAI FAKTUR"); 
        $sheet->setCellValue('H3', "PBF"); 
        $numrow = 4; // Set baris pertama untuk isi tabel adalah baris ke 4
        // $output_opname_item = $this->$model->read(0,false);

        $select = 'a.*, b.name as pharmacy_depo_name, c.name as payment_method_name, c.days, ee.fullname as receipt_name, d.bride_code, h.name as payment_name, d.name as supplier_name, e.username as user_name, f.no_document as no_document_purchase, f.ndiscountotal, g.no_document as no_document_planning';
        $where = array(
            'a.date >=' => $date_start,
            'a.date <=' => $date_end,
        );

        if (isset($q_strings['date_receipt'])) {
            $where['a.date_receipt'] = $q_strings['date_receipt'];
        }

        if (isset($q_strings['supplier_id'])) {
            $where['a.supplier_id'] = $q_strings['supplier_id'];
        }

        if (isset($q_strings['no_document'])) {
            $where['f.no_document'] = $q_strings['no_document'];
        }

        $output_opname_item = $this->$model->select(0, $select, $where);
        $no=1;

        foreach($output_opname_item as $data){ // Lakukan looping pada variabel 
            $sheet->setCellValue('A'.$numrow, $no);
            $sheet->setCellValue('B'.$numrow, date('d/m/Y', strtotime($data->date)));
            $sheet->setCellValue('C'.$numrow, date('d/m/Y', strtotime($data->date_receipt)));
            $sheet->setCellValue('D'.$numrow, $data->no_invoice);
            $sheet->setCellValue('E'.$numrow, $data->days);
            $sheet->setCellValue('F'.$numrow, date('Y-m-d', strtotime($data->date. ' + '.$data->days.' day')));
            $sheet->setCellValue('G'.$numrow, (int)$data->nfinish);
            $sheet->setCellValue('H'.$numrow, $data->supplier_name);
            $no++;
            $numrow++;
        }
        $sheet->getDefaultRowDimension()->setRowHeight(-1);
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->setTitle('Laporan DP');
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.'Laporan DP'.'.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        // $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
	}

	public function delete_delete($p = 0) {
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
            $user_id = $this->security->xss_clean($is_valid_token['data']->id);
            # Form Validation
            $this->form_validation->set_data($dt_put);
            $this->form_validation->set_rules($this->delete_rules);
            if ($this->form_validation->run() == FALSE){
                // Form Validation Errors
                $response_data = $this->form_validation->error_array();
                $this->REST_Return(422, 'Oops, something went wrong, Please try again latter.', $response_data);
            } else {
                $model = $this->name;
                /* Start Transaction */
                $this->db->trans_start();
                if($this->$model->read($id) != []) {
                    if ($this->$model->read($id)[0]->validation_key_1 != "" || $this->$model->read($id)[0]->validation_key_1 != NULL) {
                        if ($id) {
                            $items_data = $this->ReceiptItems->read($id);
                            // var_dump($items_data);
                            // die();
                            for ($i = 0; $i< count($items_data); $i++)
                            {

                                $catalogCheck = $this->Catalog->read($items_data[$i]->pharmacy_catalog_id,false);
                                if (empty($catalogCheck)){
                                    $this->REST_Return(422, 'Oops, something went wrong, Pharmacy catalog not found!');
                                    return;
                                }

                                $this->PharmacyAssets->whereFields['pharmacy_depo_id'] = '8da5d967-18da-4f87-86dc-0a9416611c11';
                                $this->PharmacyAssets->whereFields['pharmacy_catalog_id'] = $items_data[$i]->pharmacy_catalog_id;
                                $pharmacy_asset = $this->PharmacyAssets->read(0,false);

                                $hna = str_replace(",", ".", str_replace(".", "", $items_data[$i]->price_item));
                                $hp = str_replace(",", ".", str_replace(".", "", $items_data[$i]->price_item)) * 1.3;

                                $out_amount = 0;
                                $currentAmount = 0;
                                $prevAmount = 0;

                                // if ($pharmacy_asset[0]->flag == 1 ){
                                    $out_amount = str_replace(".", "", $items_data[$i]->sum_item);
                                    $prevAmount = $pharmacy_asset[0]->physical_amount;
                                    $currentAmount = $pharmacy_asset[0]->physical_amount - str_replace(".", "", $items_data[$i]->sum_item);
                                // }else{
                                //     $this->REST_Return(422, 'Oops, something went wrong, Please check your flag on this module.');
                                //     return;
                                // }
                                $dt_pharmacy_asset = array(
                                    'pharmacy_depo_id' => '8da5d967-18da-4f87-86dc-0a9416611c11',
                                    'pharmacy_catalog_id' => $items_data[$i]->pharmacy_catalog_id,
                                    'batch' => (string) $items_data[$i]->batch,
                                    'prev_amount' => $prevAmount,
                                    'physical_amount' => $currentAmount,
                                    'admin_amount' => $currentAmount,
                                );
                                $id_pharmacy = $this->PharmacyAssets->replace($dt_pharmacy_asset);
                                
                                $dt_pharmacy_asset_card = array(
                                    'pharmacy_depo_id' => '8da5d967-18da-4f87-86dc-0a9416611c11',
                                    'reff_id' => $id,
                                    'pharmacy_catalog_id' => $items_data[$i]->pharmacy_catalog_id,
                                    'prev_amount' => $prevAmount,
                                    'in_amount' => 0,
                                    'out_amount' => $out_amount,
                                    'current_amount' => $currentAmount,
                                    'hna_item' => $hna,
                                    'hp_item' => $hp,
                                    'flag'  => 11,
                                    'hja_item' => $hp,
                                    'expired_date' => $items_data[$i]->expired_date,
                                    'date_created' => date('Y-m-d H:i:s'),
                                );

                                $this->PharmacyAssetsCard->create($dt_pharmacy_asset_card);
                            }
                        }
                    }
                    $want_to_delete = array(
                        $this->$model->primaryKey => $id,
                    );
					foreach ($dt_put as $key => $value) {
						$want_to_delete[$key] = $value;
					}
                    $want_to_delete['user_deleted'] = $user_id;
                    $want_to_delete['date_deleted'] = date('Y-m-d H:i:s');
                    $id = $this->$model->update($id, $want_to_delete);
                } else {
                    $this->REST_Return(404, $this->name . ' with id ' . $id . ' does not exist!');
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