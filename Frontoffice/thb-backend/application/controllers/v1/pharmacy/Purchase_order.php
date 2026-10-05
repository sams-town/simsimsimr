<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Purchase_order extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/{$this->name}_model", "{$this->name}");
        $this->load->model("$this->directory/{$this->name}_model_item", "PurchaseOrderItem");
        $this->load->model("$this->directory/Catalog_model", "Catalog");
        $this->load->model("$this->directory/Planning_item_model", "Planning_item");
        $this->load->model("$this->directory/Receipt_model", "Receipt");
        $this->load->model("$this->directory/Receipt_po_comparison_model", "Receipt_po_comparison");
        $this->load->model("v1/setting/Coa_model", "Coa");
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
            $this->form_validation->set_rules('supplier_id', 'supplier_id' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('payment_method_id', 'payment_method_id','trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('no_planning', 'no_planning' , 'trim|max_length[36]');
            $this->form_validation->set_rules('po_type', 'po_type' , 'trim|required');
            $this->form_validation->set_rules('date', 'date' , 'trim|required');
            // $this->form_validation->set_rules('due_day', 'due_day' , 'trim|required');
            // $this->form_validation->set_rules('due_date_start', 'due_date_start', 'trim|required');
            // $this->form_validation->set_rules('due_date_end', 'due_date_end','trim|required');
            $this->form_validation->set_rules('account_number', 'account_number' , 'trim');
            $this->form_validation->set_rules('account_name', 'account_name' , 'trim|max_length[30]');
            $this->form_validation->set_rules('price_type', 'price_type' , 'trim|required');
            $this->form_validation->set_rules('time_period', 'time_period' , 'trim|numeric');
            $this->form_validation->set_rules('ntotal', 'ntotal' , 'trim|required');
            $this->form_validation->set_rules('ntax', 'ntax' , 'trim|required');
            $this->form_validation->set_rules('nround', 'nround' , 'trim');
            $this->form_validation->set_rules('nfinish', 'nfinish' , 'trim|required');
            
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
                    'var_1' => 'PO',
                    'var_2' => date('Y'),
                    'var_3' => date('m'),
                    'var_5' => date('Y-m')
                );
                $this->load->library("transaction");
                $checkcounter = $this->transaction->counter($codecounter);
                $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT);
                if ($checkcounter['counter_queue'] == '0') {
                    $this->Counter_queue->replace(array(
                        'var_1' => 'PO',
                        'var_2' => date('Y'),
                        'var_3' => date('m'),
                        'var_5' => date('Y-m'),
                        'counter_queue' => 0
                    ));
                    $checkcounter = $this->transaction->counter($codecounter);
                    $code = $codecounter['var_1'].$codecounter['var_2'].$codecounter['var_3'].str_pad($checkcounter['counter_queue'], 5, "0", STR_PAD_LEFT);
                }
                $dt_po = array(
                    'supplier_id' => $dt_post['supplier_id'],
                    'payment_method_id' => $dt_post['payment_method_id'],
                    'pharmacy_depo_id' => $dt_post['pharmacy_depo_id'],
                    'no_document'   => $code,
                    'no_planning' => $dt_post['no_planning'],
                    'po_type' => $dt_post['po_type'],
                    'date' => $dt_post['date'],
                    'due_day' => $dt_post['due_day'],
                    'due_date_start' => $dt_post['due_date_start'],
                    'due_date_end' => $dt_post['due_date_end'],
                    'account_number' => $dt_post['account_number'],
                    'account_name' => $dt_post['account_name'],
                    'price_type' => $dt_post['price_type'],
                    'is_ppn' => $dt_post['is_ppn'],
                    'time_period' => $dt_post['time_period'],
                    'ntotal' => str_replace(",", ".", str_replace(".", "", $dt_post['ntotal'])),
                    'ndiscount' => str_replace(",", ".", str_replace(".", "", $dt_post['ndiscount'])),
                    'ntax' => str_replace(",", ".", str_replace(".", "", $dt_post['ntax'])),
                    'nround' => str_replace(",", ".", str_replace(".", "", $dt_post['nround'])),
                    'nfinish' => str_replace(",", ".", str_replace(".", "", $dt_post['nfinish'])),
                    'delivery_fee' => str_replace(",", ".", str_replace(".", "", $dt_post['delivery_fee'])),
                    'ndiscountotal' => str_replace(",", ".", str_replace(".", "", $dt_post['ndiscountotal'])),
                    'ndiscounttotal' => str_replace(",", ".", str_replace(".", "", $dt_post['ndiscounttotal'])),
                    'validation_key_1'      => $dt_post['validation_key_1'] != "" ? $dt_post['validation_key_1'] : NULL,
                    // 'validation_key_1'      => isset($dt_post['validation_key_1']) ? $this->base64ToFile($dt_post['validation_key_1'],$code) : '',
                    'user_added' => $user_id,
                    'date_added' => date('Y-m-d H:i:s',time()),
                );
                $id = $this->$model->create($dt_po);
                for ($i = 0; $i< count($dt_post['purchase_order_items']); $i++)
                {
                    $catalogCheck = $this->Catalog->read($dt_post['purchase_order_items'][$i]['pharmacy_catalog_id'],false);
                    if (empty($catalogCheck)){
                        $this->REST_Return(422, 'Oops, something went wrong, Pharmacy catalog not found!');
                        return;
                    }
                    $dt_po_item = array(
                        'purchase_order_id' => $id,
                        'pharmacy_catalog_id' => $dt_post['purchase_order_items'][$i]['pharmacy_catalog_id'],
                        'package_id' =>   $dt_post['purchase_order_items'][$i]['package_id'],
                        'package_name' =>  $dt_post['purchase_order_items'][$i]['package_name'],
                        'package_big_id' =>   $dt_post['purchase_order_items'][$i]['package_big_id'],
                        'value_package' =>  str_replace(",", ".", str_replace(".", "", $dt_post['purchase_order_items'][$i]['value_package'])),
                        'sum_item' =>  str_replace(",", ".", str_replace(".", "", $dt_post['purchase_order_items'][$i]['sum_item'])),
                        'sum_package' => str_replace(",", ".", str_replace(".", "", $dt_post['purchase_order_items'][$i]['sum_package'])),
                        'price_item' =>  str_replace(",", ".", str_replace(".", "", $dt_post['purchase_order_items'][$i]['price_item'])),
                        'price_package' => str_replace(",", ".", str_replace(".", "", $dt_post['purchase_order_items'][$i]['price_package'])),
                        'discount_item' => $dt_post['purchase_order_items'][$i]['discount_item'],
                        'discount_price' => str_replace(",", ".", str_replace(".", "", $dt_post['purchase_order_items'][$i]['discount_price'])),
                        'sort' => $dt_post['purchase_order_items'][$i]['sort'],
                        'package_isi' => $dt_post['purchase_order_items'][$i]['package_isi'],
                        'before_discount' => $dt_post['purchase_order_items'][$i]['before_discount'],
                    );
                    $this->PurchaseOrderItem->create($dt_po_item);
                    if($dt_post['no_planning'] !== "") {
                        $dt_prc_replace = array(
                            'pharmacy_planning_id' => $dt_post['no_planning'],
                            'pharmacy_catalog_id' => $dt_post['purchase_order_items'][$i]['pharmacy_catalog_id'],
                            'purchase_order_id' => $id,
                        );
                        $this->Planning_item->replace($dt_prc_replace);
                    }
                }
                $this->to_accurate($id);
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
        $file_name    = 'image_vkey_po_'.$id; // You can change it to anything
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
            $this->form_validation->set_rules('supplier_id', 'supplier_id' , 'trim|required|max_length[36]');
            $this->form_validation->set_rules('payment_method_id', 'payment_method_id','trim|required|max_length[36]');
            $this->form_validation->set_rules('pharmacy_depo_id', 'pharmacy_depo_id', 'trim|required|max_length[36]');
            $this->form_validation->set_rules('no_planning', 'no_planning' , 'trim|max_length[36]');
            $this->form_validation->set_rules('po_type', 'po_type' , 'trim|required');
            $this->form_validation->set_rules('due_day', 'due_day' , 'trim|required');
            $this->form_validation->set_rules('due_date_start', 'due_date_start', 'trim|required');
            $this->form_validation->set_rules('due_date_end', 'due_date_end','trim|required');
            $this->form_validation->set_rules('account_number', 'account_number' , 'trim');
            $this->form_validation->set_rules('account_name', 'account_name' , 'trim|max_length[30]');
            $this->form_validation->set_rules('price_type', 'price_type' , 'trim|required');
            $this->form_validation->set_rules('time_period', 'time_period' , 'trim|numeric');
            $this->form_validation->set_rules('ntotal', 'ntotal' , 'trim|required');
            $this->form_validation->set_rules('ntax', 'ntax' , 'trim|required');
            $this->form_validation->set_rules('nround', 'nround' , 'trim');
            $this->form_validation->set_rules('nfinish', 'nfinish' , 'trim|required');

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
                $dt_po = array(
                    'id' => $id,
                    'supplier_id' => $dt_put['supplier_id'],
                    'payment_method_id' => $dt_put['payment_method_id'],
                    'pharmacy_depo_id' => $dt_put['pharmacy_depo_id'],
                    'no_planning' => $dt_put['no_planning'],
                    'po_type' => $dt_put['po_type'],
                    'date' => $dt_put['date'],
                    'due_day' => $dt_put['due_day'],
                    'due_date_start' => $dt_put['due_date_start'],
                    'due_date_end' => $dt_put['due_date_end'],
                    'account_number' => $dt_put['account_number'],
                    'account_name' => $dt_put['account_name'],
                    'price_type' => $dt_put['price_type'],
                    'time_period' => $dt_put['time_period'],
                    'is_ppn' => $dt_put['is_ppn'],
                    'ntotal' => str_replace(",", ".", str_replace(".", "", $dt_put['ntotal'])),
                    'ndiscount' => str_replace(",", ".", str_replace(".", "", $dt_put['ndiscount'])),
                    'ntax' => str_replace(",", ".", str_replace(".", "", $dt_put['ntax'])),
                    'ndiscountotal' => str_replace(",", ".", str_replace(".", "", $dt_put['ndiscountotal'])),
                    'ndiscounttotal' => str_replace(",", ".", str_replace(".", "", $dt_put['ndiscounttotal'])),
                    'delivery_fee' => str_replace(",", ".", str_replace(".", "", $dt_put['delivery_fee'])),
                    'nround' => str_replace(",", ".", str_replace(".", "", $dt_put['nround'])),
                    'nfinish' => str_replace(",", ".", str_replace(".", "", $dt_put['nfinish'])),
                    'validation_key_1'=> isset($dt_put['validation_key_1']) ? $dt_put['validation_key_1'] : $planning[0]->validation_key_1,
                    // 'validation_key_1'=> isset($dt_put['validation_key_1']) ? $this->base64ToFile($dt_put['validation_key_1'],$dt_put['no_document']) : $planning[0]->validation_key_1,
                    'user_updated' => $user_id,
                    'date_updated' => date('Y-m-d H:i:s',time()),
                );

                $this->$model->update($id,$dt_po);
                
                $where_delete = [];
                for ($i = 0; $i< count($dt_put['purchase_order_items']); $i++)
                {
                    $catalogCheck = $this->Catalog->read($dt_put['purchase_order_items'][$i]['pharmacy_catalog_id'],false);
                    array_push($where_delete, $dt_put['purchase_order_items'][$i]['pharmacy_catalog_id']);
                    if (empty($catalogCheck)){
                        $this->REST_Return(422, 'Oops, something went wrong, Pharmacy catalog not found!');
                        return;
                    }
                    $dt_po_item_id = array();
                    if ($dt_put['purchase_order_items'][$i]['id'] != "") {
                        $dt_po_item_id = array(
                            'id' => $dt_put['purchase_order_items'][$i]['id'],
                        );
                    }
                    $dt_po_item = array(
                        'purchase_order_id' => $id,
                        'pharmacy_catalog_id' => $dt_put['purchase_order_items'][$i]['pharmacy_catalog_id'],
                        'package_id' =>   $dt_put['purchase_order_items'][$i]['package_id'],
                        'package_big_id' =>   $dt_put['purchase_order_items'][$i]['package_big_id'],
                        'package_name' =>  $dt_put['purchase_order_items'][$i]['package_name'],
                        'value_package' =>  str_replace(".", "", $dt_put['purchase_order_items'][$i]['value_package']),
                        'sum_item' =>  $dt_put['purchase_order_items'][$i]['sum_item'],
                        'sum_package' => $dt_put['purchase_order_items'][$i]['sum_package'],
                        'price_item' =>  str_replace(",", ".", str_replace(".", "", $dt_put['purchase_order_items'][$i]['price_item'])),
                        'price_package' => str_replace(",", ".", str_replace(".", "", $dt_put['purchase_order_items'][$i]['price_package'])),
                        'discount_item' => $dt_put['purchase_order_items'][$i]['discount_item'],
                        'discount_price' => str_replace(",", ".", str_replace(".", "", $dt_put['purchase_order_items'][$i]['discount_price'])),
                        'sort' => $dt_put['purchase_order_items'][$i]['sort'],
                        'package_isi' => $dt_put['purchase_order_items'][$i]['package_isi'],
                        'before_discount' => $dt_put['purchase_order_items'][$i]['before_discount'],
                    );
                    $dt_po_item = array_merge($dt_po_item_id,$dt_po_item);
                    $this->PurchaseOrderItem->replace_uuid($dt_po_item);
                }
                //delete item
                $this->PurchaseOrderItem->whereFields['a.purchase_order_id'] = $id;
                $this->PurchaseOrderItem->whereNotInFields['a.pharmacy_catalog_id'] = $where_delete;
                $purchase_order_items_del = $this->PurchaseOrderItem->read();
                for($j_d = 0; $j_d <count($purchase_order_items_del); $j_d++){
                    if($purchase_order_items_del[$j_d]->id){
                        $this->db->where('id', $purchase_order_items_del[$j_d]->id);
                        $this->db->delete('purchase_order_items');
                    }
                }
                $this->db->trans_complete();
                /* End Transaction */
                if ($this->db->trans_status() === false) {
                    $response_data = $this->db->trans_status();
                    $this->REST_Return(422, 'Oops, something went wrong, Please try again later.', $response_data);
                } else {
                    // $this->to_accurate($id);
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

    public function to_accurate($id = 0){
        // echo "<pre>";
        $model = $this->name;
        $output = $this->$model->read($id)[0];
        for($i = 0; $i< count($output); $i++){
            $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $id;
            $purchase_order_items = $this->PurchaseOrderItem->read();
            for($j = 0; $j <count($purchase_order_items); $j++){
                $detailItem[$j] = array(
                    'itemNo' => $purchase_order_items[$j]->bride_code,
                    'unitPrice' => str_replace(",", ".", str_replace(".", "", $purchase_order_items[$j]->price_item)),
                    '_status' => $purchase_order_items[$j]->is_delete == 1 ? 'delete' : '',
                    'itemDiscPercent' => str_replace(",", ".", str_replace(".", "", $purchase_order_items[$j]->discount_item)),
                    'itemUnitName' => $purchase_order_items[$j]->package_name,
                    'quantity' => $purchase_order_items[$j]->sum_item
                );
                // $output[$i]->purchase_order_items[$j]['id'] = $purchase_order_items[$j]->id;
                // $output[$i]->purchase_order_items[$j]['purchase_order_id'] = $purchase_order_items[$j]->purchase_order_id;
                // $output[$i]->purchase_order_items[$j]['pharmacy_catalog_id'] = $purchase_order_items[$j]->pharmacy_catalog_id;
                // $output[$i]->purchase_order_items[$j]['pharmacy_catalog_name'] = $purchase_order_items[$j]->pharmacy_catalog_name;
                // $output[$i]->purchase_order_items[$j]['package_id'] = $purchase_order_items[$j]->package_id;
                // $output[$i]->purchase_order_items[$j]['package_name'] = $purchase_order_items[$j]->package_name;
                // $output[$i]->purchase_order_items[$j]['value_package'] = $purchase_order_items[$j]->value_package;
                // $output[$i]->purchase_order_items[$j]['sum_item'] = $purchase_order_items[$j]->sum_item;
                // $output[$i]->purchase_order_items[$j]['sum_package'] = $purchase_order_items[$j]->sum_package;
                // $output[$i]->purchase_order_items[$j]['price_item'] = $purchase_order_items[$j]->price_item;
                // $output[$i]->purchase_order_items[$j]['price_package'] = $purchase_order_items[$j]->price_package;
                // $output[$i]->purchase_order_items[$j]['discount_item'] = $purchase_order_items[$j]->discount_item;
                // $output[$i]->purchase_order_items[$j]['discount_price'] = $purchase_order_items[$j]->discount_price;
                // $output[$i]->purchase_order_items[$j]['sort'] = $purchase_order_items[$j]->sort;
                // $output[$i]->purchase_order_items[$j]['package_isi'] = $purchase_order_items[$j]->package_isi;
                // $output[$i]->purchase_order_items[$j]['before_discount'] = $purchase_order_items[$j]->before_discount;
            }
        }
        var_dump($id);
        var_dump($output);
        var_dump($purchase_order_items);
        $toAccuratePO = array(
            'transDate' => date("d/m/Y",strtotime($output->date_added)),
            'vendorNo' => $output->bride_code,
            'cashDiscount' => $output->ndiscountotal,
            'number' => $output->no_document,
            'paymentTermName' => $output->payment_method_name,
            'toAddress' => 'RS Bunda Halimah',
            'detailItem' => $detailItem,
        );
        // var_dump($toAccuratePO);
        // echo "</pre>";
        // die();
        $return_bride_po = $this->bride_accurate('api/purchase-order/save.do', json_encode($toAccuratePO));
        if ($return_bride_po['s'] == true) {
        } else {
            // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_po),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
            var_dump($return_bride_po);
            var_dump($toAccuratePO);
            $this->REST_Return(404, 'Failed Accurate = ');
            return;
        }
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
                    $this->to_accurate_delete($p);
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

    public function to_accurate_delete($id = 0){
        // echo "<pre>";
        $model = $this->name;
        $output = $this->$model->read($id)[0];
        var_dump('api/purchase-order/delete.do?number='.$output->no_document);
        // echo "</pre>";
        // die();
        $return_bride_po = $this->bride_accurate_delete('api/purchase-order/delete.do?number='.$output->no_document);
        if ($return_bride_po['s'] == true) {
        } else {
            // $this->Accurate_bak->create(array('institute_branch_id'=>$institute_branch_id,'invoice_id'=>$id,'response'=>json_encode($return_bride_po),'body'=>json_encode($json_body_inv),'date_added'=>date('Y-m-d H:i:s')));
            var_dump($return_bride_po);
            var_dump($toAccuratePO);
            $this->REST_Return(404, 'Failed Accurate = ');
            return;
        }
    }

    public function vendor_get($id = 0){
        $return_bride_vendor = $this->bride_accurate('api/vendor/list.do?sp.pageSize=300&sp.page=1&fields=id,vendorNo,name', json_encode($toAccuratePO));
        if ($return_bride_vendor['s'] == true) {
            $this->REST_Return(200, 'Failed Accurate = ',$return_bride_vendor);
        } else {
            var_dump($return_bride_vendor);
            $this->REST_Return(404, 'Failed Accurate = ');
            return;
        }
    }
    public function coa_get($id = 0){
        $return_bride_vendor = $this->bride_accurate('api/glaccount/list.do?sp.pageSize=100&sp.page='.$id.'&fields=id,accountType,asOf,currencyCode,name,no,id,memo,openBalance,parentNo');
        for ($i=0; $i < count($return_bride_vendor['d']); $i++) { 
            // echo $return_bride_vendor['d'][$i]['id'];
            $this->Coa->replace(array(
                'id'=>$return_bride_vendor['d'][$i]['id'],
                'no'=>$return_bride_vendor['d'][$i]['no'],
                'asOf'=>$return_bride_vendor['d'][$i]['asOf'],
                'accountType'=>$return_bride_vendor['d'][$i]['accountType'],
                'name'=>$return_bride_vendor['d'][$i]['name'],
                'memo'=>$return_bride_vendor['d'][$i]['memo'],
            ));
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

    public function bride_accurate_delete($url = null,$param = null) {
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
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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
                $this->PurchaseOrderItem->whereFields['purchase_order_id'] = $id;
                $purchase_order_items = $this->PurchaseOrderItem->read();
                for($j = 0; $j <count($purchase_order_items); $j++){
                        $output[$i]->purchase_order_items[$j]['id'] = $purchase_order_items[$j]->id;
                        $output[$i]->purchase_order_items[$j]['purchase_order_id'] = $purchase_order_items[$j]->purchase_order_id;
                        $output[$i]->purchase_order_items[$j]['pharmacy_catalog_id'] = $purchase_order_items[$j]->pharmacy_catalog_id;
                        $output[$i]->purchase_order_items[$j]['pharmacy_catalog_name'] = $purchase_order_items[$j]->pharmacy_catalog_name;
                        $output[$i]->purchase_order_items[$j]['package_id'] = $purchase_order_items[$j]->package_id;
                        $output[$i]->purchase_order_items[$j]['package_name'] = $purchase_order_items[$j]->package_name;
                        $output[$i]->purchase_order_items[$j]['value_package'] = $purchase_order_items[$j]->value_package;
                        $output[$i]->purchase_order_items[$j]['sum_item'] = $purchase_order_items[$j]->sum_item;
                        $output[$i]->purchase_order_items[$j]['sum_package'] = $purchase_order_items[$j]->sum_package;
                        $output[$i]->purchase_order_items[$j]['price_item'] = $purchase_order_items[$j]->price_item;
                        $output[$i]->purchase_order_items[$j]['price_package'] = $purchase_order_items[$j]->price_package;
                        $output[$i]->purchase_order_items[$j]['discount_item'] = $purchase_order_items[$j]->discount_item;
                        $output[$i]->purchase_order_items[$j]['discount_price'] = $purchase_order_items[$j]->discount_price;
                        $output[$i]->purchase_order_items[$j]['sort'] = $purchase_order_items[$j]->sort;
                        $output[$i]->purchase_order_items[$j]['package_isi'] = $purchase_order_items[$j]->package_isi;
                        $output[$i]->purchase_order_items[$j]['before_discount'] = $purchase_order_items[$j]->before_discount;
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
            if(isset($q_strings['limit'])) $this->$model->limit = $q_strings['limit']; unset($q_strings['limit']);
            if(isset($q_strings['offset'])) $this->$model->offset = $q_strings['offset']; unset($q_strings['offset']);
            $q_strings['is_delete'] = '0';
            if(isset($q_strings['date_start']) && isset($q_strings['date_end'])) {
                $this->$model->whereFields['a.date >='] = $q_strings['date_start'];
                $this->$model->whereFields['a.date <='] = $q_strings['date_end'];
            } else {
                $q_strings['date_start'] = '2025-04-15';
                $q_strings['date_end'] = date('Y-m-d');
                $this->$model->whereFields['a.date >='] = $q_strings['date_start'];
                $this->$model->whereFields['a.date <='] = $q_strings['date_end'];
            }
            if ($q_strings != []){
                if(isset($q_strings['pharmacy_catalog_id'])) {
                    $search_pharmacy_catalog_id = $q_strings['pharmacy_catalog_id']; unset($q_strings['pharmacy_catalog_id']);
                    $this->$model->whereFields['poi.pharmacy_catalog_id'] = $search_pharmacy_catalog_id;
                } 
                $this->$model->wherefields['a.is_delete'] = $q_strings['is_delete'];
                $output = $this->$model->filter_data($q_strings,true,$search_pharmacy_catalog_id);
            } else {
                $output = $this->$model->read_ssr($id);
            }
            $response_data = array();
            if (empty($output->rows)){
                $this->REST_Return(404, 'Data not found!');
                return;
            }
            for($i = 0; $i< count($output->rows); $i++){
                if ($search_pharmacy_catalog_id !== '') {
                    $this->PurchaseOrderItem->wherefields['a.pharmacy_catalog_id'] = $search_pharmacy_catalog_id;
                }
                $purchase_order_items = $this->PurchaseOrderItem->read($output->rows[$i]->id);
                // var_dump($purchase_order_items);
                for($j = 0; $j < count($purchase_order_items); $j++){
                    $output->rows[$i]->purchase_order_items[$j]['id'] = $purchase_order_items[$j]->id;
                    $output->rows[$i]->purchase_order_items[$j]['purchase_order_id'] = $purchase_order_items[$j]->purchase_order_id;
                    $output->rows[$i]->purchase_order_items[$j]['pharmacy_catalog_id'] = $purchase_order_items[$j]->pharmacy_catalog_id;
                    $output->rows[$i]->purchase_order_items[$j]['pharmacy_catalog_name'] = $purchase_order_items[$j]->pharmacy_catalog_name;
                    $output->rows[$i]->purchase_order_items[$j]['package_id'] = $purchase_order_items[$j]->package_id;
                    $output->rows[$i]->purchase_order_items[$j]['package_big_id'] = $purchase_order_items[$j]->package_big_id;
                    $output->rows[$i]->purchase_order_items[$j]['package_name'] = $purchase_order_items[$j]->package_name;
                    $output->rows[$i]->purchase_order_items[$j]['package_name_big'] = $purchase_order_items[$j]->package_name_big;
                    $output->rows[$i]->purchase_order_items[$j]['value_package'] = $purchase_order_items[$j]->value_package;
                    $output->rows[$i]->purchase_order_items[$j]['sum_item'] = $purchase_order_items[$j]->sum_item;
                    $output->rows[$i]->purchase_order_items[$j]['sum_package'] = $purchase_order_items[$j]->sum_package;
                    $output->rows[$i]->purchase_order_items[$j]['price_item'] = $purchase_order_items[$j]->price_item;
                    $output->rows[$i]->purchase_order_items[$j]['price_package'] = $purchase_order_items[$j]->price_package;
                    $output->rows[$i]->purchase_order_items[$j]['discount_item'] = $purchase_order_items[$j]->discount_item;
                    $output->rows[$i]->purchase_order_items[$j]['discount_price'] = $purchase_order_items[$j]->discount_price;
                    $output->rows[$i]->purchase_order_items[$j]['sort'] = $purchase_order_items[$j]->sort;
                    $output->rows[$i]->purchase_order_items[$j]['package_isi'] = $purchase_order_items[$j]->package_isi;
                    $output->rows[$i]->purchase_order_items[$j]['before_discount'] = $purchase_order_items[$j]->before_discount;
                }
                if (count($purchase_order_items) == 0 ){
                //      $output = array();
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

    public function ordering_get($p = 0){
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

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';
    
        if(isset($q_strings['limit'])) {
            $limit = $q_strings['limit'];
            $this->$model->limit = $q_strings['limit']; 
            unset($q_strings['limit']);
        }

        if(isset($q_strings['offset'])) {
            $offset = $q_strings['offset'];
            $this->$model->offset = $q_strings['offset']; 
            unset($q_strings['offset']);
        }

        $select = 'a.id, 
        poi.pharmacy_catalog_id, 
        pc.name as nama_obat, 
        poi.value_package as qty, 
        poi.package_name as satuan, 
        poi.price_item as harga_pcs, 
        poi.discount_item as disc, 
        poi.discount_price as total_disc,
        c.name as distributor,
        a.date as tanggal_po';
        $where = array(
            'a.is_delete' => 0,
            'poi.is_delete' => 0,
            'a.date >=' => $date_start,
            'a.date <=' => $date_end,
        );
        $join = array(
            array('purchase_order_items poi', 'a.id = poi.purchase_order_id', 'INNER'),
            array('pharmacy_catalog pc', 'poi.pharmacy_catalog_id = pc.id', 'INNER'),
            array('company c', 'a.supplier_id = c.id', 'INNER'),
        );
        $order = array(
            'a.date' => 'ASC',
        );
        $output = $this->$model->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
            $purchase_order_id = $output[$i]->id;
            $pharmacy_catalog_id = $output[$i]->pharmacy_catalog_id;

            $qty_po = $output[$i]->qty;
            $harga_pcs_po = $output[$i]->harga_pcs;
            $total_disc_po = $output[$i]->total_disc;
            $total_harga_po = (float) $harga_pcs_po * (float) $qty_po - (float) $total_disc_po;

            $output[$i]->total_harga_po = $total_harga_po;
            $output[$i]->keterangan = '';
            $output[$i]->tanggal_terima = '';
            $output[$i]->nama_penerima = '';
            $output[$i]->harga_box = '';
            $output[$i]->qty_box = '';
            $output[$i]->qty_receipt = '';
            $output[$i]->harga_pcs = '';
            $output[$i]->disc = '';
            $output[$i]->total_disc = '';
            $output[$i]->total_harga = '';
            $output[$i]->no_faktur = '';
            $output[$i]->tgl_faktur = '';
            $output[$i]->top = '';
            $output[$i]->tgl_jatuh_tempo = '';

            $select_comparison = 'a.id, a.receipt_id, a.total_po, a.total_receipt';
            $where_comparison = array(
                'purchase_order_id' => $purchase_order_id,
                'pharmacy_catalog_id' => $pharmacy_catalog_id,
            );
            $join_comparison = array();
            $receipt_po_comparison = $this->Receipt_po_comparison->select(0, $select_comparison, $where_comparison, $join_comparison);

            if (count($receipt_po_comparison) > 0) {
                $receipt_id = $receipt_po_comparison[0]->receipt_id;
                $total_po = $receipt_po_comparison[0]->total_po;
                $total_receipt = $receipt_po_comparison[0]->total_receipt;

                $keterangan = '';

                if ((int) $total_po == (int) $total_receipt) {
                    $keterangan = 'Sukses';
                }

                $output[$i]->keterangan = $keterangan;

                $select_receipt = 'a.date_receipt as tanggal_terima, 
                a.date,
                e.fullname as nama_penerima, 
                ri.price_package as harga_box,
                ri.sum_package as qty_box,
                ri.value_package as qty,
                ri.price_item as harga_pcs,
                ri.discount_item as disc,
                ri.discount_price as total_disc,
                pm.days,
                a.no_invoice as no_faktur';
                $where_receipt = array(
                    'a.is_deleted' => 0,
                    'ri.pharmacy_catalog_id' => $pharmacy_catalog_id,
                );
                $join_receipt = array(
                    array('receipt_items ri', 'a.id = ri.receipt_id', 'INNER'),
                    array('payment_method pm', 'a.payment_method_id = pm.id', 'INNER'),
                    array('user u', 'u.id = a.validation_key_1', 'LEFT'),
                    array('employee e', 'u.employee_id = e.id', 'LEFT'),
                );
                $receipt = $this->Receipt->select($receipt_id, $select_receipt, $where_receipt, $join_receipt);

                if (count($receipt) > 0) {
                    $tanggal_terima = $receipt[0]->tanggal_terima;
                    $nama_penerima = $receipt[0]->nama_penerima;
                    $harga_box = $receipt[0]->harga_box;
                    $qty_box = $receipt[0]->qty_box;
                    $qty_receipt = $receipt[0]->qty;
                    $harga_pcs = $receipt[0]->harga_pcs;
                    $disc = $receipt[0]->disc;
                    $total_disc = $receipt[0]->total_disc;
                    $total_harga = (float) $harga_pcs * (float) $qty_receipt - (float) $total_disc;
                    $no_faktur = $receipt[0]->no_faktur;
                    $tgl_faktur = $receipt[0]->date;
                    $top = $receipt[0]->days;
                    $tgl_jatuh_tempo = date('Y-m-d', strtotime($tgl_faktur . ' +'. $top . ' days'));

                    $output[$i]->tanggal_terima = $tanggal_terima;
                    $output[$i]->nama_penerima = $nama_penerima;
                    $output[$i]->harga_box = $harga_box;
                    $output[$i]->qty_box = $qty_box;
                    $output[$i]->qty_receipt = $qty_receipt;
                    $output[$i]->harga_pcs = $harga_pcs;
                    $output[$i]->disc = $disc;
                    $output[$i]->total_disc = $total_disc;
                    $output[$i]->total_harga = $total_harga;
                    $output[$i]->no_faktur = $no_faktur;
                    $output[$i]->tgl_faktur = $tgl_faktur;
                    $output[$i]->top = $top;
                    $output[$i]->tgl_jatuh_tempo = $tgl_jatuh_tempo;
                }                
            }
        }

        $this->$model->limit = null;
        $this->$model->offset = null;
        $select = 'count(*) as iTotal';
        $iTotal = $this->$model->select(0, $select, $where, $join)[0]->iTotal;
        
		$response_data = (object) array(
			'rows' => $output,
			'start' => ($offset + $limit),
			'iTotal' => $iTotal
		);
        
        $this->REST_Return(200, 'SUCCESS', $response_data);
        return;
    }
}