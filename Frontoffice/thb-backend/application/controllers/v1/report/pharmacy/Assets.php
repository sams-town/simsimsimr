<?php defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Assets extends My_Controller {
    public function __construct() {
        parent::__construct();
        // Load Authorization Token Library
        $this->load->library('Authorization_Token');
        // Load Model
        $this->load->model("v1/pharmacy/Pharmacy_assets_model", "Pharmacy_assets");
        $this->load->model("v1/pharmacy/Pharmacy_assets_card_model", "Pharmacy_assets_card");
        $this->load->model("v1/pharmacy/Prescription_item_model", "Prescription_item");
        $this->load->model("v1/pharmacy/Mutation_model_item", "Mutation_item");
        $this->load->model("v1/pharmacy/Receipt_model_item", "Receipt_item");
        // Load Helper
        $this->load->helper("excel");
    }

    public function card_get() {
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

        $start_date = $q_strings['date_start'] . ' 00:00:00';
        $end_date = $q_strings['date_end'] . ' 23:59:59';


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
            'Tipe Transaksi' => array(
                'width' => 15
            ),
            'Detail Transaksi' => array(
                'width' => 15
            ),
            'Pengirim' => array(
                'width' => 15
            ),
            'Tujuan' => array(
                'width' => 15
            ),
            'Tanggal Transaksi' => array(
                'width' => 15
            ),
            'Katalog' => array(
                'width' => 15
            ),
            'Awal' => array(
                'width' => 15
            ),
            'Masuk' => array(
                'width' => 15
            ),
            'Keluar' => array(
                'width' => 15
            ),
            'Akhir' => array(
                'width' => 15
            ),
        );

        $rows = array();

        $where = array(
            'a.date_created >=' => $start_date,
            'a.date_created <=' => $end_date,
        );

        if (isset($q_strings['pharmacy_depo_id'])) {
            $pharmacy_depo_id = $q_strings['pharmacy_depo_id'];
            $where['b.id'] = $q_strings['pharmacy_depo_id'];
        }
    
        if (isset($q_strings['pharmacy_catalog_id'])) {
            $pharmacy_catalog_id = $q_strings['pharmacy_catalog_id'];
            $where['c.id'] = $q_strings['pharmacy_catalog_id'];
        }
        $where['a.flag'] = 4;

        if (!$limit) {
            $limit = 1000;
        }

        if (!$offset) {
            $offset = 0;
        }

        $select = 'a.*,
        b.name as depo_name,
        c.name as catalog_name,
        p.name as package';
        $join = array(
            array('pharmacy_depo b','a.pharmacy_depo_id = b.id','LEFT'),
            array('pharmacy_catalog c','a.pharmacy_catalog_id = c.id','LEFT'),
            array('package p','c.package_id = p.id','LEFT'),
        );
        $order = array('a.date_created' => 'asc');
        $output = $this->Pharmacy_assets_card->select(0, $select, $where, $join, $order);
        if($pharmacy_depo_id && $pharmacy_catalog_id) {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `pharmacy_depo_id` = '$pharmacy_depo_id' AND
                    `pharmacy_catalog_id` = '$pharmacy_catalog_id' AND
                    `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        } else if($pharmacy_depo_id) {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `pharmacy_depo_id` = '$pharmacy_depo_id' AND
                    `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        
        } else if ($pharmacy_catalog_id) {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `pharmacy_catalog_id` = '$pharmacy_catalog_id' AND
                    `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        } else {
            $output = $this->db->query("
                SELECT
                `full_a`.*, 
                `b`.`name` as `depo_name`, 
                `c`.`name` as `catalog_name`, 
                `p`.`name` as `package`
                FROM (
                    SELECT id, pharmacy_depo_id, pharmacy_catalog_id
                    FROM pharmacy_assets_card
                    WHERE `date_created` >= '$start_date'
                    AND `date_created` <= '$end_date' 
                    $flag
                    ORDER BY `date_created` DESC 
                    LIMIT $limit
                    OFFSET $offset
                ) as `a`
                LEFT JOIN `pharmacy_assets_card` `full_a` ON `a`.`id` = `full_a`.`id`
                LEFT JOIN `pharmacy_depo` `b` ON `a`.`pharmacy_depo_id` = `b`.`id`
                LEFT JOIN `pharmacy_catalog` `c` ON `a`.`pharmacy_catalog_id` = `c`.`id`
                LEFT JOIN `package` `p` ON `c`.`package_id` = `p`.`id`
            ")->result();
        }
        
        for ($i=0; $i < count($output); $i++) { 
            $flag = $output[$i]->flag;
            $flag_desc = '-';
            $date_created = $output[$i]->date_created;
            $catalog_name = $output[$i]->catalog_name;
            $prev_amount = $output[$i]->prev_amount;
            $in_amount = $output[$i]->in_amount;
            $out_amount = $output[$i]->out_amount;
            $current_amount = $output[$i]->current_amount;

            $reff_id = $output[$i]->reff_id;
            $pharmacy_catalog_id = $output[$i]->pharmacy_catalog_id;

            // SEARCH RECEIPT
            if ($flag == 1) {
                $flag = 'Penerimaan';
                $where_r['a.receipt_id'] = $reff_id;
                $where_r['a.pharmacy_catalog_id'] = $pharmacy_catalog_id;
                $select_r = 'r.no_document, r.no_invoice, po.no_document as no_document_po, pd.name as depo_name, s.name as supplier_name, e.fullname';
                $join_r = array(
                    array('receipts r','a.receipt_id = r.id','LEFT'),
                    array('company s','r.supplier_id = s.id','LEFT'),
                    array('purchase_order po','r.purchase_order_id = po.id','LEFT'),
                    array('user u','r.validation_key_1 = u.id','LEFT'),
                    array('employee e','u.employee_id = e.id','LEFT'),
                    array('pharmacy_depo pd','r.pharmacy_depo_id = pd.id','LEFT'),
                );
                $order_r = array('a.receipt_id' => 'asc');
                $output_r = $this->Receipt_item->select(0, $select_r, $where_r, $join_r, $order_r);

                $no_document = $output_r[0]->no_document;
                $no_document_po = $output_r[0]->no_document_po;
                $no_invoice = $output_r[0]->no_invoice;
                $supplier_name = $output_r[0]->supplier_name;
                $depo_name = $output_r[0]->depo_name;
                $fullname = $output_r[0]->fullname;
                $flag_r = $output_r[0]->flag;

                $flag_desc = $no_document ? 'Material Receipt: ' . $no_document . 
                ', Supplier: ' . $supplier_name . 
                ', No. PO: ' . $no_document_po . 
                ', Faktur: ' . $no_invoice . 
                ', Gudang: ' . $depo_name .
                ', User: ' . $fullname .
                ', Jam: ' . date('H:i:s', strtotime($date_created)) : $flag_r;
            }

            // SEARCH PRESCRIPTION
            if ($flag == 3) {
                $flag = 'Pemakaian';
                $where_p['a.pharmacy_prescription_id'] = $output[$i]->reff_id;
                $where_p['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                $select_p = 'pp.no_order, p.fullname as patient, pd.name as depo_name, e.fullname';
                $join_p = array(
                    array('pharmacy_prescription pp','a.pharmacy_prescription_id = pp.id','LEFT'),
                    array('patient p','pp.norm = p.norm','LEFT'),
                    array('user u','pp.user_1 = u.id','LEFT'),
                    array('employee e','u.employee_id = e.id','LEFT'),
                    array('pharmacy_depo pd','pp.pharmacy_depo_id = pd.id','LEFT'),
                );
                $order_p = array('a.pharmacy_prescription_id' => 'asc');
                $output_p = $this->Prescription_item->select(0, $select_p, $where_p, $join_p, $order_p);

                $no_order = $output_p[0]->no_order;
                $depo_name = $output_p[0]->depo_name;
                $patient = $output_p[0]->patient;
                $fullname = $output_p[0]->fullname;

                $flag_desc = $no_order ? 'Pemakaian Obat/Alkes No: ' . $no_order .
                ', Pasien A/N: ' . $patient .
                ', Gudang: ' . $depo_name .
                ', User: ' . $fullname .
                ', Jam: ' . date('H:i:s', strtotime($date_created)) : $output_p->flag;
            }

            // SEARCH MUTATION
            if ($flag == 4) {
                $flag = 'Mutasi';
                $where_m['a.pharmacy_mutation_id'] = $output[$i]->reff_id;
                $where_m['a.pharmacy_catalog_id'] = $output[$i]->pharmacy_catalog_id;
                $select_m = 'pm.no_document, mds.name as depo_name_send, mdr.name as depo_name_recieve, e2.fullname as user_send, e3.fullname as user_recive';
                $join_m = array(
                    array('pharmacy_mutation pm','a.pharmacy_mutation_id = pm.id','LEFT'),
                    array('user u2','pm.validation_key_2 = u2.id','LEFT'),
                    array('user u3','pm.validation_key_3 = u3.id','LEFT'),
                    array('employee e2','u2.employee_id = e2.id','LEFT'),
                    array('employee e3','u3.employee_id = e3.id','LEFT'),
                    array('pharmacy_depo mds','pm.pharmacy_depo_send_id = mds.id','LEFT'),
                    array('pharmacy_depo mdr','pm.pharmacy_depo_recive_id = mdr.id','LEFT'),
                );
                $order_m = array('a.pharmacy_mutation_id' => 'asc');
                $output_m = $this->Mutation_item->select(0, $select_m, $where_m, $join_m, $order_m);


                $in_amount = $output[$i]->in_amount;
                $out_amount = $output[$i]->out_amount;
                $no_document = $output_m[0]->no_document;
                $depo_name_send = $output_m[0]->depo_name_send;
                $depo_name_recieve = $output_m[0]->depo_name_recieve;
                $user_send = $output_m[0]->user_send;
                $user_recive = $output_m[0]->user_recive;

                if ($in_amount == 0) {
                    $flag_desc = $no_document ? 'Room Inventory Movement Asal: ' . $depo_name_send .
                    ', Tujuan: ' . $depo_name_recieve .
                    ', User: ' . $user_send .
                    ', Jam: ' . date('H:i:s', strtotime($output[$i]->date_created)) : $output_m->flag;
                }

                if ($out_amount == 0) {
                    $flag_desc = $no_document ? 'Room Inventory Movement Asal: ' . $depo_name_send .
                    ', Tujuan: ' . $depo_name_recieve .
                    ', User: ' . $user_recive .
                    ', Jam: ' . date('H:i:s', strtotime($output[$i]->date_created)) : $output_m->flag;
                }
            }

            $row = array($flag,$flag_desc,$depo_name_send,$depo_name_recieve,$date_created,$catalog_name,$prev_amount,$in_amount,$out_amount,$current_amount);
            
            array_push($rows, $row);
        }
        
        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'Kartu_Stok_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }

    public function monitoring_get() {
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

        if (!isset($q_strings['date'])) {
            $this->REST_Return(400, 'date is required');
            return;
        } 

        $date = $q_strings['date'];

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
            'Gudang/Apotik/Satelit' => array(
                'width' => 20
            ),
            'Katalog' => array(
                'width' => 45
            ),
            'Jumlah Stok' => array(
                'width' => 15
            ),
            'Jumlah Fisik' => array(
                'width' => 15
            ),
            'Jumlah Sebelum' => array(
                'width' => 15
            ),
            'HPP' => array(
                'width' => 15
            ),
            'Last Update' => array(
                'width' => 17
            ),
        );

        $rows = array();

        $where = [];

        if (isset($q_strings['pharmacy_depo_id'])) {
            $pharmacy_depo_id = $q_strings['pharmacy_depo_id'];
            $where[] = "pac.pharmacy_depo_id = '$pharmacy_depo_id'";
        }

        if (isset($q_strings['pharmacy_catalog_id'])) {
            $pharmacy_catalog_id = $q_strings['pharmacy_catalog_id'];
            $where[] = "pac.pharmacy_catalog_id = '$pharmacy_catalog_id'";
        }

        if (count($where) > 0) {
            $where = 'AND ' . implode(' AND ', $where);
        } else {
            $where = '';
        }

        $output = $this->db->query("
            SELECT pd.name AS depo_name, pc.name AS catalog_name, pc.price, pac.prev_amount, pac.in_amount, pac.out_amount, pac.current_amount, pac.date_created 
            FROM pharmacy_assets_card pac 
            INNER JOIN pharmacy_depo pd ON pac.pharmacy_depo_id = pd.id
            INNER JOIN pharmacy_catalog pc ON pac.pharmacy_catalog_id = pc.id
            WHERE pac.date_created >= '2025-04-01 23:59:59' AND pac.date_created <= '$date 23:59:59' $where
            GROUP BY pac.pharmacy_catalog_id 
            ORDER BY pac.date_created DESC
        ")->result();

        for ($i=0; $i < count($output); $i++) { 
            $depo_name = $output[$i]->depo_name;
            $catalog_name = $output[$i]->catalog_name;
            $admin_amount = $output[$i]->current_amount;
            $physical_amount = $output[$i]->current_amount;
            $prev_amount = $output[$i]->prev_amount;
            $price = $output[$i]->price;
            $date_created = $output[$i]->date_created;

            $row = array($depo_name,$catalog_name,$admin_amount,$physical_amount,$prev_amount,$price,$date_created);
            
            array_push($rows, $row);
        }
        
        $spreadsheet = create_excel($headers, $rows, $style);
        
        $writer = new Xlsx($spreadsheet);
        
        $filename = 'Monitor_Stok_' . date('Ymd_His');
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');

        ini_set('memory_limit', '156M');
        ini_set('max_execution_time', 30); 
    }
}