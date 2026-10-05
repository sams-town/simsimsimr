<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Receipt extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/pharmacy/Receipt_model", "Receipt");
        // Load Helper
        $this->load->helper("excel");
    }

    public function get_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

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
            'No. Dokumen' => array(
                'width' => 15
            ),
            'No. PO' => array(
                'width' => 15
            ),
            'Gudang/Depo' => array(
                'width' => 15
            ),
            'Faktur' => array(
                'width' => 15
            ),
            'Tgl.Faktur' => array(
                'width' => 15
            ),
            'Tgl.Terima' => array(
                'width' => 15
            ),
            'TOP' => array(
                'width' => 15
            ),
            'Tgl.Jatuh Tempo' => array(
                'width' => 15
            ),
            'Cara Bayar' => array(
                'width' => 15
            ),
            'Supplier' => array(
                'width' => 15
            ),
            'Nilai' => array(
                'width' => 15
            ),
            'Nilai Diskon' => array(
                'width' => 15
            ),
            'Nilai Total' => array(
                'width' => 15
            ),
            'Status' => array(
                'width' => 15
            ),
            'Waktu Buat' => array(
                'width' => 15
            ),
        );

        $rows = array();

        $where = array(
            'a.is_deleted' => 0,
            'a.date_added >=' => $date_start,
            'a.date_added <=' => $date_end,
        );

        if (isset($q_strings['no_document'])) {
            $where['a.no_document'] = $q_strings['no_document'];
        }

        if (isset($q_strings['no_document_purchase'])) {
            $where['f.no_document'] = $q_strings['no_document_purchase'];
        }

        if (isset($q_strings['supplier_id'])) {
            $where['a.supplier_id'] = $q_strings['supplier_id'];
        }

        if (isset($q_strings['pharmacy_catalog_id'])) {
            $where['a.id'] = $q_strings['pharmacy_catalog_id'];
        }

        $select = "a.*, b.name as pharmacy_depo_name, c.name as payment_method_name, c.days, ee.fullname as receipt_name, d.bride_code, h.name as payment_name, d.name as supplier_name, e.username as user_name, f.no_document as no_document_purchase, f.ndiscountotal, g.no_document as no_document_planning";

        $join = array();
        $order = array('a.date_added' => 'ASC');
        $output = $this->Receipt->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
            $no_document = $output[$i]->no_document;
            $no_document_purchase = $output[$i]->no_document_purchase;
            $pharmacy_depo_name = $output[$i]->pharmacy_depo_name;
            $no_invoice = $output[$i]->no_invoice;
            $date = $output[$i]->date;
            $date_receipt = $output[$i]->date_receipt;
            $days = $output[$i]->days;
            $jt = date('Y-m-d', strtotime($output[$i]->date. ' + '.$output[$i]->days.' day'));
            $payment_method_name = $output[$i]->payment_method_name;
            $supplier_name = $output[$i]->supplier_name;
            $ntotal = $output[$i]->ntotal;
            $ndiscount = $output[$i]->ndiscount;
            $nfinish = $output[$i]->nfinish;
            $flag = $output[$i]->flag;
            $date_added = $output[$i]->date_added;

            $row = array($no_document,$no_document_purchase,$pharmacy_depo_name,$no_invoice,$date,$date_receipt,$days,$jt,$payment_method_name,$supplier_name,$ntotal,$ndiscount,$nfinish,$flag,$date_added);
            
            array_push($rows, $row);
        }
        
        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'Penerimaan_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function get_item_get() {
        header("Access-Control-Allow-Origin: *");
        parse_str($_SERVER['QUERY_STRING'],$q_strings);
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 0); 
        
        // Token Validation
        // $is_valid_token = $this->authorization_token->validateToken();
        // if (!empty($is_valid_token) AND $is_valid_token['status'] == NULL) {
        //     $response_data = 'Invalid Token.';
        //     $this->REST_Return(401, $is_valid_token['message'] , $response_data);
        //     return;
        // }

        if (!isset($q_strings['date_start']) || !isset($q_strings['date_end'])) {
            $this->REST_Return(400, 'date_start & date_end is required');
            return;
        } 

        $date_start = $q_strings['date_start'] . ' 00:00:00';
        $date_end = $q_strings['date_end'] . ' 23:59:59';

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
            'No.Penerimaan' => array(
                'width' => 15
            ),
            'Tgl.Penerimaan' => array(
                'width' => 15
            ),
            'No.PO' => array(
                'width' => 15
            ),
            'Tgl.PO' => array(
                'width' => 15
            ),
            'Nomor Faktur' => array(
                'width' => 15
            ),
            'Tgl.Faktur' => array(
                'width' => 15
            ),
            'Tgl.Terima' => array(
                'width' => 15
            ),
            'Tgl.Jatuh Tempo' => array(
                'width' => 15
            ),
            'TOP' => array(
                'width' => 15
            ),
            'Cara Bayar' => array(
                'width' => 15
            ),
            'Nama Supplier' => array(
                'width' => 15
            ),
            'Nama Barang/Katalog' => array(
                'width' => 15
            ),
            'Item' => array(
                'width' => 15
            ),
            'Satuan' => array(
                'width' => 15
            ),
            'Harga Satuan' => array(
                'width' => 15
            ),
            'Diskon %' => array(
                'width' => 15
            ),
            'Diskon Nominal' => array(
                'width' => 15
            ),
            'Total Harga Sebelum Diskon' => array(
                'width' => 15
            ),
            'Total Harga Setelah Diskon' => array(
                'width' => 15
            ),
            'Batch' => array(
                'width' => 15
            ),
            'Expired' => array(
                'width' => 15
            ),
        );

        $rows = array();

        $where = array(
            'a.date_added >=' => $date_start,
            'a.date_added <=' => $date_end,
        );

        if (isset($q_strings['no_document'])) {
            $where['a.no_document'] = $q_strings['no_document'];
        }

        if (isset($q_strings['no_document_purchase'])) {
            $where['f.no_document'] = $q_strings['no_document_purchase'];
        }

        if (isset($q_strings['supplier_id'])) {
            $where['a.supplier_id'] = $q_strings['supplier_id'];
        }

        if (isset($q_strings['pharmacy_catalog_id'])) {
            $where['a.id'] = $q_strings['pharmacy_catalog_id'];
        }

        $select = "a.*, 
        b.name as pharmacy_depo_name, 
        c.name as payment_method_name, 
        c.days, 
        ee.fullname as receipt_name, 
        d.bride_code, 
        h.name as payment_name, 
        d.name as supplier_name, 
        e.username as user_name, 
        f.no_document as no_document_purchase, 
        f.date as date_po, 
        f.ndiscountotal, 
        g.no_document as no_document_planning,
        pc.name as phatmacy_catalog_name,
        ri.batch,
        ri.package_name,
        ri.sum_item,
        ri.sum_package,
        ri.price_item,
        ri.price_package,
        ri.discount_item,
        ri.discount_price,
        ";
        $where['a.is_deleted'] = 0;
        $join = array(
            array('receipt_items ri', 'a.id = ri.receipt_id', 'LEFT'),
            array('pharmacy_catalog pc', 'ri.pharmacy_catalog_id = pc.id', 'LEFT'),
            array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
            array('payment_method c','a.payment_method_id = c.id','LEFT'),
            array('company d','a.supplier_id = d.id','LEFT'),
            array('user e','a.user_added = e.id','LEFT'),
            array('employee ee','e.employee_id = ee.id','LEFT'),
            array('purchase_order f','a.purchase_order_id = f.id','LEFT'),
            array('helper h','f.price_type = h.code AND h.variable="type_price"','LEFT'),
            array('pharmacy_planning g','a.pharmacy_planning_id = g.id','LEFT'),
        );
        $order = array('a.date_added' => 'ASC');
        $this->db->group_by(array("ri.receipt_id", "ri.pharmacy_catalog_id"));
        $output = $this->Receipt->select(0, $select, $where, $join, $order);

        for ($i=0; $i < count($output); $i++) { 
            $no_document = $output[$i]->no_document;
            $no_document_purchase = $output[$i]->no_document_purchase;
            $pharmacy_depo_name = $output[$i]->pharmacy_depo_name;
            $no_invoice = $output[$i]->no_invoice;
            $date = $output[$i]->date;
            $date_po = $output[$i]->date_po;
            $date_receipt = $output[$i]->date_receipt;
            $days = $output[$i]->days;
            $jt = date('Y-m-d', strtotime($output[$i]->date. ' + '.$output[$i]->days.' day'));
            $payment_method_name = $output[$i]->payment_method_name;
            $supplier_name = $output[$i]->supplier_name;
            $ntotal = $output[$i]->ntotal;
            $ndiscount = $output[$i]->ndiscount;
            $nfinish = $output[$i]->nfinish;
            $flag = $output[$i]->flag;
            $date_added = $output[$i]->date_added;
            $phatmacy_catalog_name = $output[$i]->phatmacy_catalog_name;
            $batch = $output[$i]->batch;
            $package_name = $output[$i]->package_name;
            $sum_item = $output[$i]->sum_item;
            $sum_package = $output[$i]->sum_package;
            $price_item = $output[$i]->price_item;
            $price_package = $output[$i]->price_package;
            $discount_item = $output[$i]->discount_item;
            $discount_price = $output[$i]->discount_price;
            $expired_date = $output[$i]->expired_date;

            $row = array(
                $no_document,
                $date_added,
                $no_document_purchase,
                $date_po,
                $no_invoice,
                $date,
                $date_receipt,
                $jt,
                $days,
                $payment_method_name,
                $supplier_name,
                $phatmacy_catalog_name,
                $sum_item,
                $package_name,
                $price_item,
                $discount_item,
                ($discount_item > 0) ? ($price_item/100)*$discount_item : 0,
                $sum_item * $price_item,
                $sum_item * ($discount_item > 0 ? $price_item - (($price_item/100)*$discount_item) : $price_item),
                $batch,
                $expired_date,
            );
            
            array_push($rows, $row);
        }
        
        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'Penerimaan_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}