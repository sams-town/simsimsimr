<?php defined('BASEPATH') OR exit('No direct script access allowed');
use Restserver\Libraries\REST_Controller;
class Price extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("$this->directory/Pharmacy_assets_price_model", "Pharmacy_assets_price");
        $this->load->model("$this->directory/Prescription_item_model", "Prescription_item");
    }

    public function update_hpp_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }
        
        $data_price = $this->db->query("
        SELECT `a`.`id`, `a`.`pharmacy_catalog_id`, `a`.`is_active`, a.date_created, a.hpp_item
        FROM `pharmacy_assets_price` as `a` 
        WHERE  `a`.`is_active` = 1 AND a.hpp_item is null")->result();

        $this->db->trans_start();

        for ($i=0; $i < count($data_price); $i++) { 
            if ($data_price[$i]->id) {

                $data_receipt_item = $this->db->query("
                SELECT a.price_item, a.discount_item, a.pharmacy_catalog_id, a.receipt_id, b.date_added
                FROM `receipt_items` as `a` 
                INNER JOIN `receipts` `b` ON `a`.`receipt_id` = `b`.`id` 
                WHERE `b`.`is_deleted` = 0 AND `a`.`pharmacy_catalog_id` = '{$data_price[$i]->pharmacy_catalog_id}' ORDER BY b.date_added DESC")->result();
                
                $pharmacy_catalog_id_hpp = $data_price[$i]->pharmacy_catalog_id;
                if ($pharmacy_catalog_id_hpp && $data_receipt_item) {
                    $pharmacy_catalog_id_receipt = $data_receipt_item[0]->pharmacy_catalog_id;
                    if ($pharmacy_catalog_id_hpp == $pharmacy_catalog_id_receipt) {

                        $pharmacy_price_id          = $data_price[$i]->id;
                        $pharmacy_catalog_id        = $data_receipt_item[0]->pharmacy_catalog_id;
                        $discount_price             = ($data_receipt_item[0]->price_item*$data_receipt_item[0]->discount_item)/100;
                        $price_item                 = $data_receipt_item[0]->price_item;

                        $dt_update_hpp = array(
                            'id'          => $pharmacy_price_id,
                            'hpp_item'    => $price_item - $discount_price,
                        );
                        // $this->$model->update_v2($bed_id, $dt_bed);
                        $hpp_update = $this->Pharmacy_assets_price->update_v2($pharmacy_price_id, $dt_update_hpp);
                    }
                }
            }
        }
        
        // $this->REST_Return(200, 'SUCCESS', $dt_update_hpp);
        // return;

        $this->db->trans_complete();
        /* End Transaction */
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying tr', $data_price);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', $data_price);
        return;
    }

    public function update_hpp_prescription_get($p = 0){
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        $id     = $this->security->xss_clean($p);
        $model  = $this->name;
    
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }
        
        $data_prescription = $this->db->query("
        SELECT `a`.`pharmacy_prescription_id`, `a`.`pharmacy_catalog_id`
        FROM `pharmacy_prescription_item` as `a` 
        INNER JOIN `pharmacy_prescription` `b` ON `a`.`pharmacy_prescription_id` = `b`.`id` 
        WHERE  `b`.`flag` = 1 AND `b`.`date_added` >= '2025-12-01 00:00:00' AND `b`.`date_added` <= '2025-12-08 23:59:59'")->result();

        $this->db->trans_start();

        for ($i=0; $i < count($data_prescription); $i++) { 
            if ($data_prescription[$i]->pharmacy_prescription_id) {

                $data_price = $this->db->query("
                SELECT `a`.`id`, `a`.`pharmacy_catalog_id`, `a`.`is_active`, a.date_created, a.hpp_item
                FROM `pharmacy_assets_price` as `a` 
                WHERE  `a`.`is_active` = 1  AND `a`.`pharmacy_catalog_id` = '{$data_prescription[$i]->pharmacy_catalog_id}'")->result();
                
                $pharmacy_catalog_id_hpp = $data_prescription[$i]->pharmacy_catalog_id;
                if ($pharmacy_catalog_id_hpp && $data_price) {
                    $pharmacy_catalog_id_receipt = $data_price[0]->pharmacy_catalog_id;
                    if ($pharmacy_catalog_id_hpp == $pharmacy_catalog_id_receipt) {

                        $pharmacy_prescription_id   = $data_prescription[$i]->pharmacy_prescription_id;
                        $hpp_item                   = $data_price[0]->hpp_item;

                        $dt_update_hpp = array(
                            'pharmacy_prescription_id'  => $pharmacy_prescription_id,
                            'pharmacy_catalog_id'       => $pharmacy_catalog_id_hpp,
                            'var_3'                     => $hpp_item,
                        );
                        // $this->$model->update_v2($bed_id, $dt_bed);
                        $hpp_update = $this->Prescription_item->replace($dt_update_hpp);
                    }
                }
            }
        }
        
        // $this->REST_Return(200, 'SUCCESS', $dt_update_hpp);
        // return;

        $this->db->trans_complete();
        /* End Transaction */
        if ($this->db->trans_status() === false) {
            $response_data = array(
                'error'     => $this->db->error(),
                'data'      => [],
                'query'     => $this->db->last_query()
            );
            $this->REST_Return(500, 'error while trying tr', $data_prescription);
            return;
        }

        $this->REST_Return(200, 'SUCCESS', $data_prescription);
        return;
    }
}